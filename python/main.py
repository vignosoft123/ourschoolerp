import os
from fastapi import FastAPI, HTTPException, Request
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
from db_connection import get_db_connection
from mysql.connector import Error
import mysql.connector
from dotenv import load_dotenv

load_dotenv()

APP_ENV = os.getenv("APP_ENV", "local")
APP_HOST = os.getenv("APP_HOST", "0.0.0.0")
APP_PORT = int(os.getenv("APP_PORT", 8000))
SQL_FILE_PATH = os.getenv("SQL_FILE_PATH", "../new domains/new db tables/tables.sql")
ALLOWED_ORIGINS = os.getenv(
    "ALLOWED_ORIGINS",
    "http://staging.ourschoolerp.localhost,http://localhost"
).split(",")

app = FastAPI(title="OurSchoolERP API", version="1.0.0")

app.add_middleware(
    CORSMiddleware,
    allow_origins=ALLOWED_ORIGINS,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


def get_target_db(host, user, password, database):
    try:
        conn = mysql.connector.connect(
            host=host, user=user, password=password, database=database
        )
        return conn
    except Error as e:
        return None


# ─── Health ────────────────────────────────────────────────────────────────────

@app.get("/")
def root(request: Request):
    return {
        "message": "OurSchoolERP Python API is running",
        "env": APP_ENV,
        "domain": request.headers.get("host", ""),
    }


# ─── Students ──────────────────────────────────────────────────────────────────

@app.get("/students")
def get_students(limit: int = 10):
    conn = get_db_connection()
    if not conn:
        raise HTTPException(status_code=500, detail="Database connection failed")
    try:
        cur = conn.cursor(dictionary=True)
        cur.execute("SELECT studentID, name, roll, email, phone FROM student LIMIT %s", (limit,))
        students = cur.fetchall()
        return {"total": len(students), "students": students}
    except Error as e:
        raise HTTPException(status_code=500, detail=str(e))
    finally:
        if conn.is_connected():
            cur.close()
            conn.close()


@app.get("/students/{student_id}")
def get_student(student_id: int):
    conn = get_db_connection()
    if not conn:
        raise HTTPException(status_code=500, detail="Database connection failed")
    try:
        cur = conn.cursor(dictionary=True)
        cur.execute(
            "SELECT studentID, name, roll, email, phone FROM student WHERE studentID = %s",
            (student_id,)
        )
        student = cur.fetchone()
        if not student:
            raise HTTPException(status_code=404, detail="Student not found")
        return student
    except Error as e:
        raise HTTPException(status_code=500, detail=str(e))
    finally:
        if conn.is_connected():
            cur.close()
            conn.close()


# ─── Table Creator ─────────────────────────────────────────────────────────────

def _run_sql_on_subdomain(subdomain: dict) -> dict:
    """Connect to a subdomain's DB and execute the tables.sql file."""
    # Validate required fields
    for field in ("db_host", "db_user", "db_pass", "db_name"):
        if not subdomain.get(field):
            raise HTTPException(
                status_code=422,
                detail=f"Subdomain record is missing field: '{field}'"
            )

    target_conn = get_target_db(
        subdomain["db_host"],
        subdomain["db_user"],
        subdomain["db_pass"],
        subdomain["db_name"],
    )
    if not target_conn:
        raise HTTPException(
            status_code=500,
            detail=f"Could not connect to DB '{subdomain['db_name']}' on {subdomain['db_host']}"
        )

    if not os.path.exists(SQL_FILE_PATH):
        target_conn.close()
        raise HTTPException(
            status_code=500,
            detail=f"SQL file not found: {SQL_FILE_PATH}"
        )

    with open(SQL_FILE_PATH, "r") as f:
        sql_content = f.read()

    try:
        cursor = target_conn.cursor()
        statements = [s.strip() for s in sql_content.split(";") if s.strip()]
        for stmt in statements:
            cursor.execute(stmt)
        target_conn.commit()
        cursor.close()
        return {
            "success": True,
            "message": f"Tables created for '{subdomain.get('subdomain', subdomain['db_name'])}'",
            "statements_executed": len(statements),
        }
    except Error as e:
        target_conn.rollback()
        raise HTTPException(status_code=500, detail=f"SQL error on '{subdomain['db_name']}': {e}")
    finally:
        if target_conn.is_connected():
            target_conn.close()


@app.post("/create-tables/{subdomain_id}")
async def create_tables(subdomain_id: int):
    main_conn = get_db_connection()
    if not main_conn:
        raise HTTPException(status_code=500, detail="Could not connect to main database")
    try:
        cur = main_conn.cursor(dictionary=True)
        cur.execute("SELECT * FROM subdomain_settings WHERE id = %s", (subdomain_id,))
        subdomain = cur.fetchone()
    except Error as e:
        raise HTTPException(status_code=500, detail=f"Main DB error: {e}")
    finally:
        if main_conn.is_connected():
            cur.close()
            main_conn.close()

    if not subdomain:
        raise HTTPException(status_code=404, detail=f"Subdomain ID {subdomain_id} not found")

    return _run_sql_on_subdomain(subdomain)


@app.post("/create-tables-bulk")
async def create_tables_bulk(server: str):
    """
    Creates tables for all active subdomains on a given server.
    Call as: POST /create-tables-bulk?server=mychools
    """
    main_conn = get_db_connection()
    if not main_conn:
        raise HTTPException(status_code=500, detail="Could not connect to main database")
    try:
        cur = main_conn.cursor(dictionary=True)
        cur.execute(
            "SELECT * FROM subdomain_settings WHERE server = %s AND status = 'active'",
            (server,)
        )
        subdomains = cur.fetchall()
    except Error as e:
        raise HTTPException(status_code=500, detail=f"Main DB error: {e}")
    finally:
        if main_conn.is_connected():
            cur.close()
            main_conn.close()

    if not subdomains:
        raise HTTPException(status_code=404, detail=f"No active subdomains found for server: {server}")

    results = []
    success_count = 0
    for sub in subdomains:
        try:
            res = _run_sql_on_subdomain(sub)
            results.append({"id": sub["id"], "subdomain": sub.get("subdomain"), "success": True, "message": res["message"]})
            success_count += 1
        except HTTPException as e:
            results.append({"id": sub["id"], "subdomain": sub.get("subdomain"), "success": False, "message": e.detail})

    return {
        "success": success_count > 0,
        "message": f"Processed {success_count}/{len(subdomains)} subdomains on server '{server}'",
        "domains_processed": len(subdomains),
        "success_count": success_count,
        "details": results,
    }


# ─── Helpers ───────────────────────────────────────────────────────────────────

def ensure_stats_columns(main_conn):
    """
    Adds school_age, total_students, total_app_users to subdomain_settings
    if they do not already exist (safe to call on every request).
    """
    cur = main_conn.cursor()
    for col, definition in [
        ("school_age",      "INT DEFAULT 0 COMMENT 'Years with ≥1 student'"),
        ("total_students",  "INT DEFAULT 0"),
        ("total_app_users", "INT DEFAULT 0"),
    ]:
        cur.execute("""
            SELECT COUNT(*) FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME   = 'subdomain_settings'
              AND COLUMN_NAME  = %s
        """, (col,))
        if cur.fetchone()[0] == 0:
            cur.execute(f"ALTER TABLE subdomain_settings ADD COLUMN {col} {definition}")
    main_conn.commit()
    cur.close()


def ensure_school_age_info_table(main_conn):
    """
    Creates the school_age_info table in the central DB if it does not exist.
    Stores per-subdomain, per-academic-year student and app-user counts.
    """
    cur = main_conn.cursor()
    cur.execute("""
        CREATE TABLE IF NOT EXISTS school_age_info (
            id               INT AUTO_INCREMENT PRIMARY KEY,
            subdomain_id     INT         NOT NULL,
            finyear          VARCHAR(30) NOT NULL,
            numberofstudents INT         DEFAULT 0,
            numberofappusers INT         DEFAULT 0,
            updated_at       TIMESTAMP   DEFAULT CURRENT_TIMESTAMP
                             ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_subdomain_year (subdomain_id, finyear)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    """)
    main_conn.commit()
    cur.close()


def _compute_school_stats(tenant_conn, db_name: str) -> dict:
    """
    Connects to a tenant DB and returns:
      school_age      – number of academic years that have ≥1 student
      total_students  – overall student count
      total_app_users – students with a non-empty device_token
      years           – list of {finyear, students, app_users} per year (ordered by startingdate)
    """
    cur = tenant_conn.cursor(dictionary=True)

    # Check if device_token column exists in this tenant's student table
    cur.execute("""
        SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = %s
          AND TABLE_NAME   = 'student'
          AND COLUMN_NAME  = 'device_token'
    """, (db_name,))
    has_device_token = cur.fetchone()["cnt"] > 0

    # Fetch per-year student counts – only years that actually have students
    cur.execute("""
        SELECT sy.schoolyearID,
               COALESCE(NULLIF(sy.schoolyear,''), sy.schoolyeartitle, CAST(sy.schoolyearID AS CHAR)) AS finyear,
               sy.startingdate,
               COUNT(s.studentID) AS total_students
        FROM schoolyear sy
        INNER JOIN student s ON s.createschoolyearID = sy.schoolyearID
        GROUP BY sy.schoolyearID, finyear, sy.startingdate
        ORDER BY sy.startingdate ASC
    """)
    year_rows = cur.fetchall()

    year_stats      = []
    total_students  = 0
    total_app_users = 0

    for row in year_rows:
        finyear  = row["finyear"]
        students = int(row["total_students"] or 0)

        if has_device_token:
            cur.execute("""
                SELECT COUNT(*) AS app_users FROM student
                WHERE createschoolyearID = %s
                  AND device_token IS NOT NULL
                  AND device_token != ''
            """, (row["schoolyearID"],))
            app_users = int(cur.fetchone()["app_users"] or 0)
        else:
            app_users = 0

        year_stats.append({
            "finyear":   finyear,
            "students":  students,
            "app_users": app_users,
        })
        total_students  += students
        total_app_users += app_users

    cur.close()
    return {
        "school_age":      len(year_stats),
        "total_students":  total_students,
        "total_app_users": total_app_users,
        "years":           year_stats,        # ← new: per-year detail
    }


# ─── Refresh Schools Age ───────────────────────────────────────────────────────

@app.post("/refresh-schools-age")
async def refresh_schools_age(server: str):
    """
    For every active subdomain on *server*:
      1. Connects to the tenant DB
      2. Computes school_age, total_students, total_app_users
      3. Updates subdomain_settings in the central DB
    Also auto-creates the three columns in subdomain_settings if missing.
    """
    main_conn = get_db_connection()
    if not main_conn:
        raise HTTPException(status_code=500, detail="Could not connect to main database")

    # Ensure the extra columns + school_age_info table exist
    try:
        ensure_stats_columns(main_conn)
        ensure_school_age_info_table(main_conn)
    except Error as e:
        raise HTTPException(status_code=500, detail=f"Migration error: {e}")

    # Fetch active subdomains for the requested server
    try:
        cur = main_conn.cursor(dictionary=True)
        cur.execute(
            "SELECT * FROM subdomain_settings WHERE server = %s AND status = 'active'",
            (server,)
        )
        subdomains = cur.fetchall()
        cur.close()
    except Error as e:
        main_conn.close()
        raise HTTPException(status_code=500, detail=f"Main DB error: {e}")

    if not subdomains:
        main_conn.close()
        raise HTTPException(status_code=404, detail=f"No active subdomains found for server: {server}")

    results = []
    success_count = 0

    for sub in subdomains:
        tenant_conn = get_target_db(
            sub["db_host"], sub["db_user"], sub["db_pass"], sub["db_name"]
        )
        if not tenant_conn:
            results.append({
                "id":        sub["id"],
                "subdomain": sub.get("subdomain"),
                "success":   False,
                "message":   f"Cannot connect to DB '{sub['db_name']}' on {sub['db_host']}",
            })
            continue

        try:
            stats = _compute_school_stats(tenant_conn, sub["db_name"])

            upd = main_conn.cursor()

            # 1. Update summary columns in subdomain_settings
            upd.execute("""
                UPDATE subdomain_settings
                   SET school_age      = %s,
                       total_students  = %s,
                       total_app_users = %s
                 WHERE id = %s
            """, (stats["school_age"], stats["total_students"], stats["total_app_users"], sub["id"]))

            # 2. Upsert per-year rows into school_age_info
            for yr in stats["years"]:
                upd.execute("""
                    INSERT INTO school_age_info
                        (subdomain_id, finyear, numberofstudents, numberofappusers)
                    VALUES (%s, %s, %s, %s)
                    ON DUPLICATE KEY UPDATE
                        numberofstudents = VALUES(numberofstudents),
                        numberofappusers = VALUES(numberofappusers)
                """, (sub["id"], yr["finyear"], yr["students"], yr["app_users"]))

            main_conn.commit()
            upd.close()

            results.append({
                "id":              sub["id"],
                "subdomain":       sub.get("subdomain"),
                "success":         True,
                "school_age":      stats["school_age"],
                "total_students":  stats["total_students"],
                "total_app_users": stats["total_app_users"],
                "years_saved":     len(stats["years"]),
                "message":         "Updated successfully",
            })
            success_count += 1

        except Error as e:
            results.append({
                "id":        sub["id"],
                "subdomain": sub.get("subdomain"),
                "success":   False,
                "message":   str(e),
            })
        finally:
            if tenant_conn.is_connected():
                tenant_conn.close()

    main_conn.close()

    return {
        "success":          success_count > 0,
        "server":           server,
        "domains_processed": len(subdomains),
        "success_count":    success_count,
        "message":          f"Refreshed {success_count}/{len(subdomains)} subdomains on server '{server}'",
        "details":          results,
    }


# ─── School Age Info (pivot data) ─────────────────────────────────────────────

@app.get("/school-age-info")
async def get_school_age_info(server: str = None):
    """
    Returns pivot data from school_age_info joined with subdomain_settings.
    Optional ?server=hostgator filter.
    Response shape:
      {
        years: ["2022-2023", "2023-2024", ...],      # all distinct years (sorted)
        subdomains: [
          { subdomain_id, subdomain, site_name, server,
            data: { "2022-2023": {students, app_users}, ... } }
        ]
      }
    """
    main_conn = get_db_connection()
    if not main_conn:
        raise HTTPException(status_code=500, detail="Could not connect to main database")

    try:
        # Make sure the table exists before querying
        ensure_school_age_info_table(main_conn)

        cur = main_conn.cursor(dictionary=True)
        query = """
            SELECT sai.subdomain_id, sai.finyear,
                   sai.numberofstudents, sai.numberofappusers,
                   ss.subdomain, ss.site_name, ss.server
            FROM   school_age_info  sai
            INNER JOIN subdomain_settings ss ON ss.id = sai.subdomain_id
        """
        params = []
        if server:
            query += " WHERE ss.server = %s"
            params.append(server)
        query += " ORDER BY ss.subdomain ASC, sai.finyear ASC"

        cur.execute(query, params or ())
        rows = cur.fetchall()
        cur.close()

        # Collect all unique years (preserve insertion order then sort)
        seen_years, years = set(), []
        for row in rows:
            if row["finyear"] not in seen_years:
                seen_years.add(row["finyear"])
                years.append(row["finyear"])
        years.sort()

        # Build per-subdomain map
        subdomain_map: dict = {}
        for row in rows:
            sid = row["subdomain_id"]
            if sid not in subdomain_map:
                subdomain_map[sid] = {
                    "subdomain_id": sid,
                    "subdomain":    row["subdomain"],
                    "site_name":    row["site_name"] or row["subdomain"],
                    "server":       row["server"],
                    "data":         {},
                }
            subdomain_map[sid]["data"][row["finyear"]] = {
                "students":  row["numberofstudents"],
                "app_users": row["numberofappusers"],
            }

        return {
            "years":            years,
            "subdomains":       list(subdomain_map.values()),
            "total_subdomains": len(subdomain_map),
        }

    except Error as e:
        raise HTTPException(status_code=500, detail=str(e))
    finally:
        if main_conn.is_connected():
            main_conn.close()


# ─── Statistics ────────────────────────────────────────────────────────────────

@app.get("/statistics/{subdomain_id}")
async def get_statistics(subdomain_id: int):
    """
    Returns per-academic-year statistics for a given subdomain:
      - total students enrolled in that year (by createschoolyearID)
      - students who have used the mobile app (device_token is set)
    """
    # 1. Fetch subdomain credentials from the central DB
    main_conn = get_db_connection()
    if not main_conn:
        raise HTTPException(status_code=500, detail="Could not connect to main database")
    try:
        cur = main_conn.cursor(dictionary=True)
        cur.execute("SELECT * FROM subdomain_settings WHERE id = %s", (subdomain_id,))
        subdomain = cur.fetchone()
    except Error as e:
        raise HTTPException(status_code=500, detail=f"Main DB error: {e}")
    finally:
        if main_conn.is_connected():
            cur.close()
            main_conn.close()

    if not subdomain:
        raise HTTPException(status_code=404, detail=f"Subdomain ID {subdomain_id} not found")

    # 2. Connect to the tenant DB
    tenant_conn = get_target_db(
        subdomain["db_host"],
        subdomain["db_user"],
        subdomain["db_pass"],
        subdomain["db_name"],
    )
    if not tenant_conn:
        raise HTTPException(
            status_code=500,
            detail=f"Could not connect to tenant DB '{subdomain['db_name']}' on {subdomain['db_host']}"
        )

    try:
        cur = tenant_conn.cursor(dictionary=True)

        # 3. Check whether device_token column exists in student table
        cur.execute("""
            SELECT COUNT(*) AS cnt
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = %s
              AND TABLE_NAME   = 'student'
              AND COLUMN_NAME  = 'device_token'
        """, (subdomain["db_name"],))
        has_device_token = cur.fetchone()["cnt"] > 0

        # 4. Fetch all academic years ordered chronologically
        cur.execute("""
            SELECT schoolyearID, schoolyear, schoolyeartitle,
                   startingdate, endingdate
            FROM schoolyear
            ORDER BY startingdate ASC
        """)
        years = cur.fetchall()

        # 5. For every year collect student counts
        year_stats = []
        total_students_all = 0
        total_app_users_all = 0

        for yr in years:
            yid = yr["schoolyearID"]

            # Total students admitted in this year
            cur.execute(
                "SELECT COUNT(*) AS cnt FROM student WHERE createschoolyearID = %s",
                (yid,)
            )
            total_students = cur.fetchone()["cnt"]

            # Mobile-app users in this year
            if has_device_token:
                cur.execute(
                    """SELECT COUNT(*) AS cnt FROM student
                       WHERE createschoolyearID = %s
                         AND device_token IS NOT NULL
                         AND device_token != ''""",
                    (yid,)
                )
                app_users = cur.fetchone()["cnt"]
            else:
                app_users = 0

            app_pct = round((app_users / total_students * 100), 1) if total_students > 0 else 0

            total_students_all += total_students
            total_app_users_all += app_users

            year_stats.append({
                "schoolyearID":    yid,
                "year_label":      yr["schoolyear"] or yr["schoolyeartitle"] or str(yid),
                "startingdate":    str(yr["startingdate"]) if yr["startingdate"] else None,
                "endingdate":      str(yr["endingdate"])   if yr["endingdate"]   else None,
                "total_students":  total_students,
                "app_users":       app_users,
                "app_percentage":  app_pct,
            })

        overall_app_pct = round((total_app_users_all / total_students_all * 100), 1) if total_students_all > 0 else 0

        return {
            "subdomain":              subdomain["subdomain"],
            "site_name":              subdomain.get("site_name") or subdomain["subdomain"],
            "has_device_token_column": has_device_token,
            "total_students":         total_students_all,
            "total_app_users":        total_app_users_all,
            "overall_app_percentage": overall_app_pct,
            "years":                  year_stats,
        }

    except Error as e:
        raise HTTPException(status_code=500, detail=f"Tenant DB error: {e}")
    finally:
        if tenant_conn.is_connected():
            cur.close()
            tenant_conn.close()


if __name__ == "__main__":
    import uvicorn
    uvicorn.run("main:app", host=APP_HOST, port=APP_PORT, reload=True)
