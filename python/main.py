import os
import json
import requests
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
SQL_FILE_PATH      = os.getenv("SQL_FILE_PATH", "../new domains/new db tables/tables.sql")
CSS_FOLDER_PATH    = os.getenv("CSS_FOLDER_PATH", "C:/xampp/htdocs/ourschoolerp/assets/inilabs")
CSS_UPDATE_API_KEY = os.getenv("CSS_UPDATE_API_KEY", "")
SERVER_DOMAINS     = json.loads(os.getenv("SERVER_DOMAINS", "{}"))
MVC_ZIP_PATH       = os.getenv("MVC_ZIP_PATH", "C:/xampp/htdocs/ourschoolerp/mvc.zip")
MVC_DEPLOY_API_KEY = os.getenv("MVC_DEPLOY_API_KEY", "")
DUMMY_SERVERS      = json.loads(os.getenv("DUMMY_SERVERS", "{}"))
CPANEL_CONFIGS     = json.loads(os.getenv("CPANEL_CONFIGS", "{}"))

# CSS files to sync (all custom inilabs files except install.css)
CSS_FILES_TO_SYNC  = [
    "inilabs.css",
    "responsive.css",
    "combined.css",
    "hidetable.css",
    "mailandmedia.css",
    "custom-overrides.css",
]
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


# ─── Update CSS (Bulk) ─────────────────────────────────────────────────────────

@app.post("/update-css-bulk")
async def update_css_bulk(request: Request):
    """
    Push CSS files to multiple live subdomains at once.
    Body: { "subdomain_ids": [1,2,3] }  OR  { "server": "godaddy" }
    """
    body = await request.json()
    subdomain_ids = body.get("subdomain_ids", [])
    server        = body.get("server", "")

    if not subdomain_ids and not server:
        raise HTTPException(status_code=422, detail="Provide either 'subdomain_ids' or 'server'")

    main_conn = get_db_connection()
    if not main_conn:
        raise HTTPException(status_code=500, detail="Could not connect to main database")
    try:
        cur = main_conn.cursor(dictionary=True)
        if subdomain_ids:
            placeholders = ",".join(["%s"] * len(subdomain_ids))
            cur.execute(
                f"SELECT * FROM subdomain_settings WHERE id IN ({placeholders}) AND status='active'",
                subdomain_ids,
            )
        else:
            cur.execute(
                "SELECT * FROM subdomain_settings WHERE server=%s AND status='active'",
                (server,),
            )
        subdomains = cur.fetchall()
    except Error as e:
        raise HTTPException(status_code=500, detail=f"Main DB error: {e}")
    finally:
        if main_conn.is_connected():
            cur.close()
            main_conn.close()

    if not subdomains:
        raise HTTPException(status_code=404, detail="No active subdomains found for the given criteria")

    # Read all CSS files once
    files_payload = {}
    for filename in CSS_FILES_TO_SYNC:
        filepath = os.path.join(CSS_FOLDER_PATH, filename)
        if os.path.exists(filepath):
            with open(filepath, "r", encoding="utf-8") as f:
                files_payload[filename] = f.read()

    if not files_payload:
        raise HTTPException(status_code=500, detail=f"No CSS files found in: {CSS_FOLDER_PATH}")

    results      = []
    success_count = 0

    for sub in subdomains:
        srv = sub.get("server", "")
        if srv not in SERVER_DOMAINS:
            results.append({"id": sub["id"], "subdomain": sub.get("subdomain"), "success": False, "message": f"No domain mapping for server: {srv}"})
            continue

        base_domain = SERVER_DOMAINS[srv]
        target_url  = f"https://{sub['subdomain']}.{base_domain}/cssupdate/receive"

        try:
            resp = requests.post(
                target_url,
                json={"api_key": CSS_UPDATE_API_KEY, "files": files_payload},
                timeout=60,
                verify=False,
            )
            try:
                result = resp.json()
                results.append({"id": sub["id"], "subdomain": sub.get("subdomain"), "success": result.get("success", False), "message": result.get("message", "")})
                if result.get("success"):
                    success_count += 1
            except ValueError:
                results.append({"id": sub["id"], "subdomain": sub.get("subdomain"), "success": False, "message": f"HTTP {resp.status_code}: Cssupdate.php not deployed on live server"})
        except requests.exceptions.RequestException as e:
            results.append({"id": sub["id"], "subdomain": sub.get("subdomain"), "success": False, "message": str(e)})

    return {
        "success":       success_count > 0,
        "total":         len(subdomains),
        "success_count": success_count,
        "message":       f"CSS synced to {success_count}/{len(subdomains)} subdomains",
        "details":       results,
    }


# ─── Update CSS (Single) ───────────────────────────────────────────────────────

@app.post("/update-css/{subdomain_id}")
async def update_css(subdomain_id: int):
    """
    Reads the local inilabs.css and pushes it to the live subdomain server
    via HTTP POST to the cssupdate/receive endpoint on the live site.
    """
    # 1. Fetch subdomain info from central DB
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

    # 2. Read all CSS files from local folder
    files_payload = {}
    missing = []
    for filename in CSS_FILES_TO_SYNC:
        filepath = os.path.join(CSS_FOLDER_PATH, filename)
        if os.path.exists(filepath):
            with open(filepath, "r", encoding="utf-8") as f:
                files_payload[filename] = f.read()
        else:
            missing.append(filename)

    if not files_payload:
        raise HTTPException(status_code=500, detail=f"No CSS files found in: {CSS_FOLDER_PATH}")

    # 3. Build target URL from server → domain mapping
    server = subdomain.get("server", "")
    if server not in SERVER_DOMAINS:
        raise HTTPException(status_code=500, detail=f"No domain mapping for server: {server}")

    base_domain = SERVER_DOMAINS[server]
    target_url  = f"https://{subdomain['subdomain']}.{base_domain}/cssupdate/receive"

    # 4. POST all CSS files to live server receiver as JSON
    use_proxy = False
    try:
        resp = requests.post(
            target_url,
            json={"api_key": CSS_UPDATE_API_KEY, "files": files_payload},
            timeout=60,
            verify=False,
        )
        try:
            result = resp.json()
            if missing:
                result["skipped"] = missing
            return result
        except ValueError:
            if resp.status_code in [406, 403, 404]:
                use_proxy = True
            else:
                raise HTTPException(
                    status_code=500,
                    detail=f"Live server returned HTTP {resp.status_code}. Cssupdate.php may not be deployed yet."
                )
    except requests.exceptions.ConnectionError:
        raise HTTPException(status_code=500, detail=f"Cannot connect to live server: {target_url}")
    except requests.exceptions.RequestException as e:
        raise HTTPException(status_code=500, detail=f"Request failed: {e}")

    # 5. Fallback when direct POST is blocked (406)
    if use_proxy:
        # Try cPanel API first (most reliable — bypasses mod_security entirely)
        if subdomain.get("server") in CPANEL_CONFIGS:
            result = _upload_files_via_cpanel(subdomain, files_payload, "assets/inilabs")
        else:
            result = _update_css_via_proxy(subdomain, files_payload)
        if missing:
            result["skipped"] = missing
        return result


# ─── Bootstrap via Dummy Server Copy ───────────────────────────────────────────

BROWSER_HEADERS = {
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
    "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
}


def _bootstrap_via_dummy(subdomain: dict) -> dict:
    """
    Calls bootstrap_copy.php on the dummy server.
    The dummy server copies Cssupdate.php + css_update_config.php + Mvcdeploy.php
    directly into the target subdomain (same cPanel account — no FTP needed).
    """
    server = subdomain.get("server", "")
    if server not in DUMMY_SERVERS:
        return {"success": False, "message": f"No dummy server configured for: {server}. Fill in DUMMY_SERVERS in python/.env"}

    domain_suffix = SERVER_DOMAINS.get(server, "")
    if not domain_suffix:
        return {"success": False, "message": f"No domain mapping for server: {server}"}

    dummy_host = DUMMY_SERVERS[server]
    dummy_url  = f"https://{dummy_host}/bootstrap_copy.php"

    headers = {
        "User-Agent": "Mozilla/5.0 (compatible; OurSchoolERP/1.0)",
        "Accept": "application/json, text/plain, */*",
    }
    payload = {
        "api_key":       CSS_UPDATE_API_KEY,
        "subdomain":     subdomain["subdomain"],
        "domain_suffix": f".{domain_suffix}",
    }

    # Try HTTPS first, fall back to HTTP if blocked
    merged_headers = {**BROWSER_HEADERS, **headers}
    for scheme in ["https", "http"]:
        url = f"{scheme}://{dummy_host}/bootstrap_copy.php"
        try:
            resp = requests.post(url, data=payload, headers=merged_headers, timeout=30, verify=False)
            try:
                return resp.json()
            except ValueError:
                # Show actual response for debugging
                preview = resp.text[:300].strip()
                return {
                    "success": False,
                    "message": f"HTTP {resp.status_code} from {url}. Server response: {preview}"
                }
        except requests.exceptions.ConnectionError:
            continue
        except requests.exceptions.RequestException as e:
            return {"success": False, "message": str(e)}

    return {"success": False, "message": f"Cannot connect to dummy server: {dummy_host} (tried HTTP and HTTPS)"}


@app.post("/bootstrap-subdomain/{subdomain_id}")
async def bootstrap_subdomain(subdomain_id: int):
    """Upload Cssupdate.php to a single live subdomain via FTP — fully automatic."""
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

    return _bootstrap_via_dummy(subdomain)


@app.post("/bootstrap-subdomain-bulk")
async def bootstrap_subdomain_bulk(request: Request):
    """Upload Cssupdate.php to multiple live subdomains at once via FTP."""
    body          = await request.json()
    subdomain_ids = body.get("subdomain_ids", [])
    server        = body.get("server", "")

    if not subdomain_ids and not server:
        raise HTTPException(status_code=422, detail="Provide either 'subdomain_ids' or 'server'")

    main_conn = get_db_connection()
    if not main_conn:
        raise HTTPException(status_code=500, detail="Could not connect to main database")
    try:
        cur = main_conn.cursor(dictionary=True)
        if subdomain_ids:
            placeholders = ",".join(["%s"] * len(subdomain_ids))
            cur.execute(f"SELECT * FROM subdomain_settings WHERE id IN ({placeholders}) AND status='active'", subdomain_ids)
        else:
            cur.execute("SELECT * FROM subdomain_settings WHERE server=%s AND status='active'", (server,))
        subdomains = cur.fetchall()
    except Error as e:
        raise HTTPException(status_code=500, detail=f"Main DB error: {e}")
    finally:
        if main_conn.is_connected():
            cur.close()
            main_conn.close()

    if not subdomains:
        raise HTTPException(status_code=404, detail="No active subdomains found")

    results, success_count = [], 0
    for sub in subdomains:
        result = _bootstrap_via_dummy(sub)
        results.append({"id": sub["id"], "subdomain": sub.get("subdomain"), **result})
        if result.get("success"):
            success_count += 1

    return {
        "success": success_count > 0,
        "total": len(subdomains),
        "success_count": success_count,
        "message": f"Bootstrap complete: {success_count}/{len(subdomains)} subdomains",
        "details": results,
    }


# ─── Upload mvcdeploy.php Script ───────────────────────────────────────────────

MVCDEPLOY_SCRIPT_PATH = os.path.join(os.path.dirname(__file__), "..", "mvcdeploy.php")


def _upload_files_via_cpanel(subdomain: dict, files: dict, remote_subdir: str) -> dict:
    """
    Upload files directly to any subdomain path using cPanel UAPI (Fileman::upload_files).
    Completely bypasses mod_security — uses cPanel HTTPS API with token auth.
    files: {filename: content_string}
    remote_subdir: path relative to subdomain root (e.g. 'assets/inilabs')
    """
    server = subdomain.get("server", "")
    cp = CPANEL_CONFIGS.get(server)
    if not cp:
        return {"success": False, "message": f"No cPanel config for server: {server}. Add to CPANEL_CONFIGS in python/.env"}

    domain_suffix = SERVER_DOMAINS.get(server, "")
    sub_name      = subdomain["subdomain"]
    target_dir    = f"{cp['home']}/public_html/{sub_name}.{domain_suffix}/{remote_subdir}"
    api_url       = f"https://{cp['host']}:{cp['port']}/execute/Fileman/upload_files"
    headers       = {"Authorization": f"cpanel {cp['user']}:{cp['token']}"}

    updated, failed = [], []
    for filename, content in files.items():
        try:
            resp = requests.post(
                api_url,
                headers=headers,
                data={"dir": target_dir, "overwrite": 1},
                files={"file-0": (filename, content.encode("utf-8"), "application/octet-stream")},
                verify=False,
                timeout=30,
            )
            result = resp.json()
            if result.get("status") == 1:
                updated.append(filename)
            else:
                errors = result.get("errors") or result.get("messages") or [str(result)]
                failed.append(f"{filename}: {errors}")
        except Exception as e:
            failed.append(f"{filename}: {e}")

    return {
        "success":  bool(updated) and not failed,
        "message":  "Updated: " + ", ".join(updated) + (" | Failed: " + ", ".join(failed) if failed else "") + " (via cPanel API)",
        "updated":  updated,
        "failed":   failed,
    }


def _update_css_via_proxy(subdomain: dict, files_payload: dict) -> dict:
    """Send CSS files via dummy server proxy using form data (bypasses mod_security JSON block)."""
    server = subdomain.get("server", "")
    if server not in DUMMY_SERVERS:
        return {"success": False, "message": f"No dummy server for: {server}"}
    domain_suffix = SERVER_DOMAINS.get(server, "")
    import base64
    encoded_files = {k: base64.b64encode(v.encode("utf-8")).decode("ascii") for k, v in files_payload.items()}
    post_data = {
        "api_key":       CSS_UPDATE_API_KEY,
        "subdomain":     subdomain["subdomain"],
        "domain_suffix": f".{domain_suffix}",
        "files":         json.dumps(encoded_files),
        "encoded":       "1",
    }

    dummy_host = DUMMY_SERVERS[server]
    # Try sync/ subfolder first (has .htaccess that disables mod_security)
    # Fall back to css_sync.php at root
    urls_to_try = [
        f"https://{dummy_host}/sync/",
        f"https://{dummy_host}/sync/index.php",
        f"https://{dummy_host}/css_sync.php",
    ]

    for url in urls_to_try:
        try:
            resp = requests.post(url, data=post_data, timeout=60, verify=False)
            if resp.status_code == 406:
                continue  # mod_security blocked — try next URL
            try:
                return resp.json()
            except ValueError:
                return {"success": False, "message": f"HTTP {resp.status_code} from {url}: {resp.text[:200]}"}
        except requests.exceptions.ConnectionError:
            continue
        except requests.exceptions.RequestException as e:
            return {"success": False, "message": str(e)}

    return {"success": False, "message": f"All proxy URLs blocked by mod_security on {dummy_host}. Add sync/.htaccess to disable it."}


def _upload_controller_to_subdomain(subdomain: dict, filename: str, content: str) -> dict:
    """Upload a PHP controller file to mvc/controllers/ via Cssupdate or cPanel API."""
    server = subdomain.get("server", "")
    if server not in SERVER_DOMAINS:
        return {"success": False, "message": f"No domain mapping for server: {server}"}

    # Try cPanel API first if configured (bypasses mod_security)
    if server in CPANEL_CONFIGS:
        return _upload_files_via_cpanel(subdomain, {filename: content}, "mvc/controllers")

    # Fallback: use Cssupdate::receive_script
    base_domain = SERVER_DOMAINS[server]
    target_url  = f"https://{subdomain['subdomain']}.{base_domain}/cssupdate/receive_script"
    try:
        resp = requests.post(
            target_url,
            json={"api_key": CSS_UPDATE_API_KEY, "script_content": content, "filename": filename},
            timeout=30,
            verify=False,
        )
        try:
            return resp.json()
        except ValueError:
            return {"success": False, "message": f"HTTP {resp.status_code}: Cssupdate.php not deployed yet."}
    except requests.exceptions.RequestException as e:
        return {"success": False, "message": str(e)}


def _upload_deploy_script_to_subdomain(subdomain: dict, script_content: str) -> dict:
    """Send mvcdeploy.php content to the live subdomain via Cssupdate::receive_script()."""
    server = subdomain.get("server", "")
    if server not in SERVER_DOMAINS:
        return {"success": False, "message": f"No domain mapping for server: {server}"}

    base_domain = SERVER_DOMAINS[server]
    target_url  = f"https://{subdomain['subdomain']}.{base_domain}/cssupdate/receive_script"

    try:
        resp = requests.post(
            target_url,
            json={"api_key": CSS_UPDATE_API_KEY, "script_content": script_content},
            timeout=30,
            verify=False,
        )
        try:
            return resp.json()
        except ValueError:
            return {
                "success": False,
                "message": f"HTTP {resp.status_code}: Cssupdate.php not deployed on live server yet.",
            }
    except requests.exceptions.ConnectionError:
        return {"success": False, "message": f"Cannot connect to {target_url}"}
    except requests.exceptions.RequestException as e:
        return {"success": False, "message": str(e)}


@app.post("/upload-deploy-script/{subdomain_id}")
async def upload_deploy_script(subdomain_id: int):
    """Upload local mvcdeploy.php to a single live subdomain's webroot."""
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

    script_path = os.path.abspath(MVCDEPLOY_SCRIPT_PATH)
    if not os.path.exists(script_path):
        raise HTTPException(status_code=500, detail=f"mvcdeploy.php not found at: {script_path}")

    with open(script_path, "r", encoding="utf-8") as f:
        script_content = f.read()

    return _upload_deploy_script_to_subdomain(subdomain, script_content)


@app.post("/upload-deploy-script-bulk")
async def upload_deploy_script_bulk(request: Request):
    """Upload mvcdeploy.php to multiple live subdomains at once."""
    body          = await request.json()
    subdomain_ids = body.get("subdomain_ids", [])
    server        = body.get("server", "")

    if not subdomain_ids and not server:
        raise HTTPException(status_code=422, detail="Provide either 'subdomain_ids' or 'server'")

    main_conn = get_db_connection()
    if not main_conn:
        raise HTTPException(status_code=500, detail="Could not connect to main database")
    try:
        cur = main_conn.cursor(dictionary=True)
        if subdomain_ids:
            placeholders = ",".join(["%s"] * len(subdomain_ids))
            cur.execute(
                f"SELECT * FROM subdomain_settings WHERE id IN ({placeholders}) AND status='active'",
                subdomain_ids,
            )
        else:
            cur.execute(
                "SELECT * FROM subdomain_settings WHERE server=%s AND status='active'", (server,)
            )
        subdomains = cur.fetchall()
    except Error as e:
        raise HTTPException(status_code=500, detail=f"Main DB error: {e}")
    finally:
        if main_conn.is_connected():
            cur.close()
            main_conn.close()

    if not subdomains:
        raise HTTPException(status_code=404, detail="No active subdomains found")

    script_path = os.path.abspath(MVCDEPLOY_SCRIPT_PATH)
    if not os.path.exists(script_path):
        raise HTTPException(status_code=500, detail=f"mvcdeploy.php not found at: {script_path}")

    with open(script_path, "r", encoding="utf-8") as f:
        script_content = f.read()

    results, success_count = [], 0
    for sub in subdomains:
        result = _upload_deploy_script_to_subdomain(sub, script_content)
        results.append({"id": sub["id"], "subdomain": sub.get("subdomain"), **result})
        if result.get("success"):
            success_count += 1

    return {
        "success":       success_count > 0,
        "total":         len(subdomains),
        "success_count": success_count,
        "message":       f"mvcdeploy.php uploaded to {success_count}/{len(subdomains)} subdomains",
        "details":       results,
    }


# ─── Deploy MVC ────────────────────────────────────────────────────────────────

def _deploy_mvc_to_subdomain(subdomain: dict, zip_bytes: bytes) -> dict:
    """
    Upload mvc.zip to the live subdomain via the Mvcdeploy CI controller.
    URL: https://{subdomain}.{domain}/mvcdeploy/receive
    Auto-checks controller exists first; uploads via Cssupdate if missing.
    """
    server = subdomain.get("server", "")
    if server not in SERVER_DOMAINS:
        return {"success": False, "message": f"No domain mapping for server: {server}"}

    base_domain  = SERVER_DOMAINS[server]
    sub_name     = subdomain['subdomain']
    check_url    = f"https://{sub_name}.{base_domain}/mvcdeploy/check"
    deploy_url   = f"https://{sub_name}.{base_domain}/mvcdeploy/receive"

    # ── Step 1: Check if Mvcdeploy controller exists ────────────────────────
    controller_exists = False
    try:
        check = requests.get(check_url, timeout=10, verify=False)
        controller_exists = (check.status_code == 200)
    except requests.exceptions.RequestException:
        pass

    # ── Step 2: Auto-upload Mvcdeploy.php if missing ────────────────────────
    if not controller_exists:
        local_ctrl = os.path.join(os.path.dirname(__file__), "..", "mvc", "controllers", "Mvcdeploy.php")
        local_ctrl = os.path.abspath(local_ctrl)
        if not os.path.exists(local_ctrl):
            return {"success": False, "message": "Mvcdeploy.php not found locally. Cannot auto-upload."}

        with open(local_ctrl, "r", encoding="utf-8") as f:
            ctrl_content = f.read()

        upload_result = _upload_controller_to_subdomain(subdomain, "Mvcdeploy.php", ctrl_content)
        if not upload_result.get("success"):
            return {
                "success": False,
                "message": f"Mvcdeploy controller missing. Auto-upload failed: {upload_result.get('message')}. "
                           f"Please ensure Cssupdate.php is deployed on {sub_name}.{base_domain} first.",
            }

    # ── Step 3: Deploy MVC ──────────────────────────────────────────────────
    try:
        resp = requests.post(
            deploy_url,
            files={"mvc_zip": ("mvc.zip", zip_bytes, "application/zip")},
            data={"api_key": CSS_UPDATE_API_KEY},
            timeout=120,
            verify=False,
        )
        try:
            result = resp.json()
            if not controller_exists and result.get("success"):
                result["message"] += " (Mvcdeploy.php was auto-uploaded first)"
            return result
        except ValueError:
            if resp.status_code == 406 and server in CPANEL_CONFIGS:
                return _deploy_mvc_via_cpanel(subdomain, zip_bytes)
            return {"success": False, "message": f"HTTP {resp.status_code}: unexpected response from live server"}
    except requests.exceptions.ConnectionError:
        return {"success": False, "message": f"Cannot connect to {deploy_url}"}
    except requests.exceptions.Timeout:
        return {"success": False, "message": "Request timed out — server may still be processing"}
    except requests.exceptions.RequestException as e:
        return {"success": False, "message": str(e)}


def _deploy_mvc_via_cpanel(subdomain: dict, zip_bytes: bytes) -> dict:
    """
    Full MVC deployment via cPanel API only — no web server requests at all.
    Works even when mod_security blocks all HTTP requests to the subdomain.
    """
    server = subdomain.get("server", "")
    cp = CPANEL_CONFIGS.get(server)
    if not cp:
        return {"success": False, "message": f"No cPanel config for: {server}"}

    domain_suffix = SERVER_DOMAINS.get(server, "")
    sub_name      = subdomain["subdomain"]
    sub_root      = f"{cp['home']}/public_html/{sub_name}.{domain_suffix}"
    api_base      = f"https://{cp['host']}:{cp['port']}"
    headers       = {"Authorization": f"cpanel {cp['user']}:{cp['token']}"}

    def uapi(func, **params):
        """Call cPanel UAPI endpoint."""
        r = requests.post(f"{api_base}/execute/{func}", headers=headers, data=params, verify=False, timeout=60)
        return r.json()

    def api2(module, func, **params):
        """Call cPanel API2 endpoint."""
        data = {"cpanel_jsonapi_version": 2, "cpanel_jsonapi_module": module, "cpanel_jsonapi_func": func, **params}
        r = requests.post(f"{api_base}/json-api/cpanel", headers=headers, data=data, verify=False, timeout=60)
        return r.json()

    dummy_host = DUMMY_SERVERS.get(server, "")
    if not dummy_host:
        return {"success": False, "message": f"No dummy server configured for: {server}"}

    dummy_root_path = f"{cp['home']}/public_html/{dummy_host}"

    try:
        # Step 1: Upload mvc.zip to dummy server root via cPanel API
        resp = requests.post(
            f"{api_base}/execute/Fileman/upload_files",
            headers=headers,
            data={"dir": dummy_root_path, "overwrite": 1},
            files={"file-0": ("mvc.zip", zip_bytes, "application/zip")},
            verify=False,
            timeout=120,
        )
        ur = resp.json()
        if ur.get("status") != 1:
            return {"success": False, "message": f"Upload mvc.zip to dummy server failed: {ur.get('errors', ur)}"}

        # Step 2: GET bootstrap_copy.php with browser User-Agent (Python UA gets 406)
        browser_headers = {
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
            "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
        }
        r = requests.get(
            f"https://{dummy_host}/bootstrap_copy.php",
            params={"k": CSS_UPDATE_API_KEY, "s": sub_name, "d": f".{domain_suffix}"},
            headers=browser_headers,
            timeout=120,
            verify=False,
        )
        try:
            return r.json()
        except ValueError:
            return {"success": False, "message": f"bootstrap_copy.php GET HTTP {r.status_code}: {r.text[:200]}"}

    except Exception as e:
        return {"success": False, "message": f"cPanel MVC deploy error: {e}"}


@app.post("/deploy-mvc/{subdomain_id}")
async def deploy_mvc(subdomain_id: int):
    """Deploy local mvc.zip to a single live subdomain."""
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

    if not os.path.exists(MVC_ZIP_PATH):
        raise HTTPException(status_code=500, detail=f"Local mvc.zip not found: {MVC_ZIP_PATH}")

    with open(MVC_ZIP_PATH, "rb") as f:
        zip_bytes = f.read()

    result = _deploy_mvc_to_subdomain(subdomain, zip_bytes)
    return result


@app.post("/deploy-mvc-bulk")
async def deploy_mvc_bulk(request: Request):
    """Deploy local mvc.zip to multiple live subdomains at once."""
    body          = await request.json()
    subdomain_ids = body.get("subdomain_ids", [])
    server        = body.get("server", "")

    if not subdomain_ids and not server:
        raise HTTPException(status_code=422, detail="Provide either 'subdomain_ids' or 'server'")

    main_conn = get_db_connection()
    if not main_conn:
        raise HTTPException(status_code=500, detail="Could not connect to main database")
    try:
        cur = main_conn.cursor(dictionary=True)
        if subdomain_ids:
            placeholders = ",".join(["%s"] * len(subdomain_ids))
            cur.execute(
                f"SELECT * FROM subdomain_settings WHERE id IN ({placeholders}) AND status='active'",
                subdomain_ids,
            )
        else:
            cur.execute(
                "SELECT * FROM subdomain_settings WHERE server=%s AND status='active'",
                (server,),
            )
        subdomains = cur.fetchall()
    except Error as e:
        raise HTTPException(status_code=500, detail=f"Main DB error: {e}")
    finally:
        if main_conn.is_connected():
            cur.close()
            main_conn.close()

    if not subdomains:
        raise HTTPException(status_code=404, detail="No active subdomains found for the given criteria")

    if not os.path.exists(MVC_ZIP_PATH):
        raise HTTPException(status_code=500, detail=f"Local mvc.zip not found: {MVC_ZIP_PATH}")

    # Read zip once — shared across all deployments
    with open(MVC_ZIP_PATH, "rb") as f:
        zip_bytes = f.read()

    results       = []
    success_count = 0

    for sub in subdomains:
        result = _deploy_mvc_to_subdomain(sub, zip_bytes)
        results.append({
            "id":        sub["id"],
            "subdomain": sub.get("subdomain"),
            "success":   result.get("success", False),
            "message":   result.get("message", ""),
        })
        if result.get("success"):
            success_count += 1

    return {
        "success":       success_count > 0,
        "total":         len(subdomains),
        "success_count": success_count,
        "message":       f"MVC deployed to {success_count}/{len(subdomains)} subdomains",
        "details":       results,
    }


# ─── Full Deploy (New Subdomain Setup) ────────────────────────────────────────

def _full_deploy_to_subdomain(subdomain: dict) -> dict:
    """
    Calls full_deploy.php on the dummy server.
    Extracts ALL zip files (assets, frontend, mvc, etc.) to the target subdomain.
    Used for NEW subdomain creation/full redeployment.
    """
    server = subdomain.get("server", "")
    if server not in DUMMY_SERVERS:
        return {"success": False, "message": f"No dummy server configured for: {server}. Fill in DUMMY_SERVERS in python/.env"}

    domain_suffix = SERVER_DOMAINS.get(server, "")
    if not domain_suffix:
        return {"success": False, "message": f"No domain mapping for server: {server}"}

    dummy_url = f"https://{DUMMY_SERVERS[server]}/full_deploy.php"

    try:
        resp = requests.post(
            dummy_url,
            data={
                "api_key":       CSS_UPDATE_API_KEY,
                "subdomain":     subdomain["subdomain"],
                "domain_suffix": f".{domain_suffix}",
            },
            timeout=120,
            verify=False,
        )
        try:
            return resp.json()
        except ValueError:
            return {
                "success": False,
                "message": f"HTTP {resp.status_code}: full_deploy.php not found on dummy server. Upload it first.",
            }
    except requests.exceptions.ConnectionError:
        return {"success": False, "message": f"Cannot connect to dummy server: {dummy_url}"}
    except requests.exceptions.Timeout:
        return {"success": False, "message": "Timed out — extraction may still be running on server"}
    except requests.exceptions.RequestException as e:
        return {"success": False, "message": str(e)}


@app.post("/full-deploy/{subdomain_id}")
async def full_deploy(subdomain_id: int):
    """Full deploy — extract ALL zip files to a single subdomain (new domain setup)."""
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

    return _full_deploy_to_subdomain(subdomain)


@app.post("/full-deploy-bulk")
async def full_deploy_bulk(request: Request):
    """Full deploy ALL zip files to multiple subdomains at once."""
    body          = await request.json()
    subdomain_ids = body.get("subdomain_ids", [])
    server        = body.get("server", "")

    if not subdomain_ids and not server:
        raise HTTPException(status_code=422, detail="Provide either 'subdomain_ids' or 'server'")

    main_conn = get_db_connection()
    if not main_conn:
        raise HTTPException(status_code=500, detail="Could not connect to main database")
    try:
        cur = main_conn.cursor(dictionary=True)
        if subdomain_ids:
            placeholders = ",".join(["%s"] * len(subdomain_ids))
            cur.execute(f"SELECT * FROM subdomain_settings WHERE id IN ({placeholders}) AND status='active'", subdomain_ids)
        else:
            cur.execute("SELECT * FROM subdomain_settings WHERE server=%s AND status='active'", (server,))
        subdomains = cur.fetchall()
    except Error as e:
        raise HTTPException(status_code=500, detail=f"Main DB error: {e}")
    finally:
        if main_conn.is_connected():
            cur.close()
            main_conn.close()

    if not subdomains:
        raise HTTPException(status_code=404, detail="No active subdomains found")

    results, success_count = [], 0
    for sub in subdomains:
        result = _full_deploy_to_subdomain(sub)
        results.append({"id": sub["id"], "subdomain": sub.get("subdomain"), **result})
        if result.get("success"):
            success_count += 1

    return {
        "success":       success_count > 0,
        "total":         len(subdomains),
        "success_count": success_count,
        "message":       f"Full deploy done: {success_count}/{len(subdomains)} subdomains",
        "details":       results,
    }


if __name__ == "__main__":
    import uvicorn
    uvicorn.run("main:app", host=APP_HOST, port=APP_PORT, reload=True)
