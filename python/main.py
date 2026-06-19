import os
import io
import json
import socket
import ftplib
import base64
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
ASSETS_ZIP_PATH    = os.getenv("ASSETS_ZIP_PATH", "C:/xampp/htdocs/ourschoolerp/assets.zip")
FRONTEND_ZIP_PATH  = os.getenv("FRONTEND_ZIP_PATH", "C:/xampp/htdocs/ourschoolerp/frontend.zip")
MVC_DEPLOY_API_KEY = os.getenv("MVC_DEPLOY_API_KEY", "")
DUMMY_SERVERS      = json.loads(os.getenv("DUMMY_SERVERS", "{}"))
CPANEL_CONFIGS     = json.loads(os.getenv("CPANEL_CONFIGS", "{}"))
FTP_CONFIGS        = json.loads(os.getenv("FTP_CONFIGS", "{}"))
DB_IMPORT_SQL_PATH   = os.getenv("DB_IMPORT_SQL_PATH", "")
GODADDY_API_KEY      = os.getenv("GODADDY_API_KEY", "")
GODADDY_API_SECRET   = os.getenv("GODADDY_API_SECRET", "")

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


@app.get("/config-check")
def config_check():
    """Debug endpoint — shows which servers have cPanel/dummy configured."""
    return {
        "cpanel_configs": list(CPANEL_CONFIGS.keys()),
        "ftp_configs":    list(FTP_CONFIGS.keys()),
        "dummy_servers":  list(DUMMY_SERVERS.keys()),
        "server_domains": list(SERVER_DOMAINS.keys()),
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
                # Direct POST blocked — try FTP, then cPanel API, then proxy
                if resp.status_code in [406, 403, 404]:
                    if srv in FTP_CONFIGS:
                        ftp_cfg = FTP_CONFIGS[srv]
                        webroot = ftp_cfg.get("webroot", "")
                        remote_dir = f"{webroot}/{sub['subdomain']}.{base_domain}/assets/inilabs" if webroot \
                                     else f"{sub['subdomain']}.{base_domain}/assets/inilabs"
                        result = _upload_files_via_ftp(ftp_cfg, remote_dir, files_payload)
                    elif srv in CPANEL_CONFIGS:
                        result = _upload_files_via_cpanel(sub, files_payload, "assets/inilabs")
                    else:
                        result = _update_css_via_proxy(sub, files_payload)
                    results.append({"id": sub["id"], "subdomain": sub.get("subdomain"), "success": result.get("success", False), "message": result.get("message", "")})
                    if result.get("success"):
                        success_count += 1
                else:
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
    server = subdomain.get("server", "").lower()
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
        srv = subdomain.get("server", "")
        if srv in FTP_CONFIGS:
            # FTP upload — bypasses mod_security, uses correct paths for HostGator/BigRock
            ftp_cfg = FTP_CONFIGS[srv]
            domain_suffix = SERVER_DOMAINS.get(srv, "")
            sub_name = subdomain["subdomain"]
            webroot = ftp_cfg.get("webroot", "")
            remote_dir = f"{webroot}/{sub_name}.{domain_suffix}/assets/inilabs" if webroot \
                         else f"{sub_name}.{domain_suffix}/assets/inilabs"
            result = _upload_files_via_ftp(ftp_cfg, remote_dir, files_payload)
        elif srv in CPANEL_CONFIGS:
            result = _upload_files_via_cpanel(subdomain, files_payload, "assets/inilabs")
        else:
            result = _update_css_via_proxy(subdomain, files_payload)
        if missing:
            result["skipped"] = missing
        return result


# ─── Bootstrap via Dummy Server Copy ───────────────────────────────────────────
# @deploy-doc bootstrap
# Copies Cssupdate.php + Mvcdeploy.php + css_update_config.php from dummy server
# to the target subdomain using bootstrap_copy.php on the dummy server.
# Uses browser User-Agent to bypass HostGator Monarx Security.
# Dummy server URLs stored in DUMMY_SERVERS (.env).
# @deploy-doc-end

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
    server = subdomain.get("server", "").lower()
    if server not in DUMMY_SERVERS:
        return {"success": False, "message": f"No dummy server configured for: {server}. Fill in DUMMY_SERVERS in python/.env"}

    domain_suffix = SERVER_DOMAINS.get(server, "")
    if not domain_suffix:
        return {"success": False, "message": f"No domain mapping for server: {server}"}

    dummy_host = DUMMY_SERVERS[server]
    dummy_url  = f"https://{dummy_host}/bootstrap_copy.php"

    payload = {
        "api_key":       CSS_UPDATE_API_KEY,
        "subdomain":     subdomain["subdomain"],
        "domain_suffix": f".{domain_suffix}",
    }

    # Try HTTPS first, fall back to HTTP if blocked
    merged_headers = BROWSER_HEADERS
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


# Remote DB host IPs per server (stored in subdomain_settings.db_host)
REMOTE_DB_HOSTS = {
    "godaddy":    "118.139.183.79",
    "hostgator":  "119.18.54.141",
    "myschools":  "119.18.54.166",
    "schoolhour": "162.241.123.136",
    "collegehour": "103.76.231.69",
}


def _build_cpanel_auth_headers(cp: dict) -> list:
    """Return list of Authorization headers to try (token first, then Basic)."""
    headers = []
    if cp.get("token"):
        headers.append({"Authorization": f"cpanel {cp['user']}:{cp['token']}"})
    if cp.get("password"):
        creds = base64.b64encode(f"{cp['user']}:{cp['password']}".encode()).decode()
        headers.append({"Authorization": f"Basic {creds}"})
    return headers


def _cpanel_call(cp: dict, auth_headers: list, endpoint: str, params: dict):
    """
    Call a cPanel UAPI endpoint trying each auth header in order.
    Returns (response_dict, working_header) or raises ValueError with message.
    """
    url = f"https://{cp['host']}:{cp['port']}/execute/{endpoint}"
    last_error = "no auth methods configured"
    for h in auth_headers:
        try:
            resp = requests.post(url, headers=h, params=params, timeout=30, verify=False)
        except Exception as e:
            last_error = f"connection error: {e}"
            continue
        if resp.status_code == 401:
            last_error = f"HTTP 401 with {list(h.values())[0][:25]}..."
            continue
        try:
            data = resp.json()
            return data, h
        except ValueError:
            last_error = f"non-JSON HTTP {resp.status_code}: {resp.text[:200]}"
            continue
    raise ValueError(f"cPanel auth failed — {last_error}")


def _build_sql_importer_php(db_name: str, db_user: str, db_pass: str) -> str:
    # NOTE: Only skip '--' comment lines. Do NOT skip '/*!' lines — those are MySQL
    # conditional comments (e.g. /*!40101 SET NAMES utf8 */) and ARE valid SQL.
    return (
        "<?php\n"
        "set_time_limit(300);\n"
        "$conn = new mysqli('localhost', '" + db_user + "', '" + db_pass + "', '" + db_name + "');\n"
        "if ($conn->connect_error) { die(json_encode(['success'=>false,'error'=>'Connect: '.$conn->connect_error])); }\n"
        "$conn->query(\"SET NAMES utf8mb4\");\n"
        "$sql_file = __DIR__ . '/import_db.sql';\n"
        "if (!file_exists($sql_file)) { die(json_encode(['success'=>false,'error'=>'SQL file not found'])); }\n"
        "$sql = file_get_contents($sql_file);\n"
        "$lines = explode(\"\\n\", $sql);\n"
        "$stmt = ''; $count = 0; $errors = [];\n"
        "foreach ($lines as $line) {\n"
        "    $line = rtrim($line);\n"
        "    if ($line==='' || substr($line,0,2)==='--') continue;\n"
        "    $stmt .= $line . \"\\n\";\n"
        "    if (substr(rtrim($line),-1)===';') {\n"
        "        $stmt = trim($stmt);\n"
        "        if ($stmt && !preg_match('/^(USE |CREATE DATABASE)/i', $stmt)) {\n"
        "            if (!$conn->query($stmt)) { $errors[] = substr($conn->error,0,120); }\n"
        "            else { $count++; }\n"
        "        }\n"
        "        $stmt = '';\n"
        "    }\n"
        "}\n"
        "$conn->close();\n"
        "echo json_encode(['success'=>true,'imported'=>$count,'errors'=>$errors]);\n"
        "@unlink($sql_file);\n"
        "@unlink(__FILE__);\n"
    )


def _cpanel_fileman_upload_to_dir(cp: dict, auth_header: dict, target_dir: str, files: dict) -> dict:
    """Upload files to a specific cPanel directory via Fileman API. files: {name: str|bytes}"""
    api_url = f"https://{cp['host']}:{cp['port']}/execute/Fileman/upload_files"
    updated, failed = [], []
    for filename, content in files.items():
        if isinstance(content, str):
            content = content.encode("utf-8")
        try:
            resp = requests.post(
                api_url,
                headers=auth_header,
                data={"dir": target_dir, "overwrite": 1},
                files={"file-0": (filename, content, "application/octet-stream")},
                verify=False,
                timeout=60,
            )
            result = resp.json()
            if result.get("status") == 1:
                updated.append(filename)
            else:
                errs = result.get("errors") or result.get("messages") or [str(result)]
                failed.append(f"{filename}: {'; '.join(str(e) for e in errs)}")
        except Exception as e:
            failed.append(f"{filename}: {e}")
    return {"updated": updated, "failed": failed}


@app.post("/create-cpanel-subdomain")
async def create_cpanel_subdomain(request: Request):
    """
    Full one-click cPanel setup:
      1. Create subdomain folder
      2. Create MySQL database  ({prefix}{subdomain})
      3. Create MySQL user      ({prefix}{subdomain})
      4. Grant ALL PRIVILEGES
      5. Import SQL file (DB_IMPORT_SQL_PATH)
      6. Full Deploy — unzip all 7 zip files via dummy server
      7. Insert record in subdomain_settings
    DB password convention: {subdomain}@123456
    """
    body      = await request.json()
    server    = body.get("server", "").lower().strip()
    subdomain = body.get("subdomain", "").lower().strip()

    if not server or not subdomain:
        raise HTTPException(status_code=422, detail="'server' and 'subdomain' are required")

    cp = CPANEL_CONFIGS.get(server)
    if not cp:
        return {"success": False, "message": f"No cPanel credentials for '{server}'. Add token to CPANEL_CONFIGS in python/.env"}

    domain_suffix = SERVER_DOMAINS.get(server, "")
    if not domain_suffix:
        return {"success": False, "message": f"No domain mapping for server '{server}'"}

    auth_headers = _build_cpanel_auth_headers(cp)
    results = {}

    # ── Step 1: Create subdomain folder ─────────────────────────────────────
    ftp_cfg  = FTP_CONFIGS.get(server, {})
    full_sub = f"{subdomain}.{domain_suffix}"

    # Document root convention per server:
    # - GoDaddy: public_html/{sub}.{domain}  (cPanel standard for cloud hosting)
    # - HostGator / MySchools / Schoolhour / Collegehour: {sub}.{domain} directly in FTP home
    #   (matches where existing subdomains live, and where bootstrap_copy.php / full_deploy.php
    #    extract to via dirname(__DIR__) — dummy at home root → parent = home → target = home/{sub})
    if server == "godaddy":
        dir_path = f"public_html/{full_sub}"
    else:
        dir_path = full_sub

    try:
        data, working_auth = _cpanel_call(cp, auth_headers, "SubDomain/addsubdomain",
                                          {"domain": subdomain, "rootdomain": domain_suffix, "dir": dir_path})
    except ValueError as e:
        return {"success": False, "message": str(e)}

    if data.get("status") != 1:
        errors = data.get("errors") or data.get("messages") or [str(data)]
        # "already exists" is acceptable — continue with DB setup
        err_str = "; ".join(str(e) for e in errors)
        if "exist" not in err_str.lower():
            return {"success": False, "message": f"Subdomain creation failed: {err_str}"}
        results["subdomain"] = f"already existed — skipped"
    else:
        results["subdomain"] = f"{full_sub} created"

    # ── Step 1b (HostGator/FTP servers): Ensure subdomain doc root is at HOME ROOT ──
    # HostGator cPanel ignores our 'dir' param and always creates subdomains under
    # public_html/{sub}.domain. But full_deploy.php and existing subdomains all live
    # at the FTP home root (no public_html). We fix this by:
    #   (a) FTP mkdir — create {sub}.domain directly in the FTP home root
    #   (b) modifysubdomain — point the Apache vhost to the home-root directory
    if server != "godaddy" and server in FTP_CONFIGS:
        ftp_local = FTP_CONFIGS[server]
        try:
            fconn = ftplib.FTP()
            fconn.connect(ftp_local['host'], int(ftp_local.get('port', 21)), timeout=30)
            fconn.login(ftp_local['user'], ftp_local['pass'])
            fconn.set_pasv(True)
            try:
                fconn.mkd(full_sub)
                results["home_dir"] = f"{full_sub}/ created at FTP home root"
            except ftplib.error_perm:
                results["home_dir"] = f"{full_sub}/ already exists at FTP home root"
            fconn.quit()
        except Exception as e:
            results["home_dir"] = f"FTP mkdir warning: {e}"
        # Update cPanel vhost to serve from home-root dir (not public_html)
        try:
            mod_data, _ = _cpanel_call(cp, [working_auth], "SubDomain/modifysubdomain", {
                "domain": subdomain, "rootdomain": domain_suffix, "newdocroot": dir_path,
            })
            if mod_data.get("status") == 1:
                results["docroot"] = f"vhost doc root → {dir_path}"
            else:
                errs = mod_data.get("errors") or [str(mod_data)]
                results["docroot"] = f"modifysubdomain warning: {'; '.join(str(e) for e in errs)}"
        except Exception as e:
            results["docroot"] = f"modifysubdomain error: {e}"

    # ── Step 1d: Add DNS A record in GoDaddy cloud DNS ──────────────────────
    # cPanel's SubDomain/addsubdomain only updates the local BIND zone file.
    # GoDaddy's nameservers (domaincontrol.com) are a separate DNS system and
    # must be updated via GoDaddy's Domains API for the subdomain to resolve publicly.
    if server == "godaddy":
        dns_result = _godaddy_add_dns_record(domain_suffix, subdomain)
        results["godaddy_dns"] = dns_result["message"]

    # Use the working auth header for all subsequent calls
    single_auth = [working_auth]

    # ── Step 2: Get MySQL prefix ─────────────────────────────────────────────
    try:
        restr, _ = _cpanel_call(cp, single_auth, "Mysql/get_restrictions", {})
        db_prefix = (restr.get("data") or {}).get("db_prefix") \
                 or (restr.get("data") or {}).get("prefix") \
                 or f"{cp['user']}_"
    except Exception:
        db_prefix = f"{cp['user']}_"

    db_name     = f"{db_prefix}{subdomain}"
    db_user     = f"{db_prefix}{subdomain}"
    db_password = f"{subdomain}@123456"

    # ── Step 3: Create database ──────────────────────────────────────────────
    try:
        db_data, _ = _cpanel_call(cp, single_auth, "Mysql/create_database", {"name": db_name})
        if db_data.get("status") == 1:
            results["database"] = f"{db_name} created"
        else:
            errs = db_data.get("errors") or [str(db_data)]
            results["database"] = f"warning: {'; '.join(str(e) for e in errs)}"
    except Exception as e:
        results["database"] = f"error: {e}"

    # ── Step 4: Create user ──────────────────────────────────────────────────
    try:
        usr_data, _ = _cpanel_call(cp, single_auth, "Mysql/create_user",
                                   {"name": db_user, "password": db_password})
        if usr_data.get("status") == 1:
            results["db_user"] = f"{db_user} created"
        else:
            errs = usr_data.get("errors") or [str(usr_data)]
            results["db_user"] = f"warning: {'; '.join(str(e) for e in errs)}"
    except Exception as e:
        results["db_user"] = f"error: {e}"

    # ── Step 5: Grant ALL PRIVILEGES ────────────────────────────────────────
    try:
        priv_data, _ = _cpanel_call(cp, single_auth, "Mysql/set_privileges_on_database",
                                    {"user": db_user, "database": db_name, "privileges": "ALL PRIVILEGES"})
        if priv_data.get("status") == 1:
            results["privileges"] = "ALL PRIVILEGES granted"
        else:
            errs = priv_data.get("errors") or [str(priv_data)]
            results["privileges"] = f"warning: {'; '.join(str(e) for e in errs)}"
    except Exception as e:
        results["privileges"] = f"error: {e}"

    # ── Step 6: Import SQL file into new database ────────────────────────────
    # Files are uploaded to the DUMMY server (not the new subdomain) because:
    # 1. Dummy server is always live — no DNS propagation delay for new subdomains
    # 2. Dummy server is on the same physical host, so 'localhost' MySQL = same DB server
    # NOTE: dummy server is ALWAYS at {home}/{dummy_host} — NOT under public_html.
    # Regular subdomains use public_html/ but dummy1 lives directly in the FTP home.
    dummy_host = DUMMY_SERVERS.get(server, "")
    if DB_IMPORT_SQL_PATH and os.path.exists(DB_IMPORT_SQL_PATH):
        if not dummy_host:
            results["sql_import"] = f"skipped — no dummy server configured for '{server}'"
        else:
            try:
                with open(DB_IMPORT_SQL_PATH, "rb") as f:
                    sql_bytes = f.read()
                php_script = _build_sql_importer_php(db_name, db_user, db_password)
                dummy_dir = f"{cp['home']}/{dummy_host}"
                upload_result = _cpanel_fileman_upload_to_dir(cp, working_auth, dummy_dir, {
                    "import_db.sql": sql_bytes,
                    "import_db_run.php": php_script.encode("utf-8"),
                })
                if upload_result["failed"]:
                    results["sql_import"] = f"upload failed: {'; '.join(upload_result['failed'])}"
                else:
                    importer_url = f"https://{dummy_host}/import_db_run.php"
                    try:
                        imp_resp = requests.get(importer_url, timeout=120, verify=False, headers=BROWSER_HEADERS)
                        imp_data = imp_resp.json()
                        if imp_data.get("success"):
                            err_note = f" ({len(imp_data['errors'])} warnings)" if imp_data.get("errors") else ""
                            results["sql_import"] = f"imported {imp_data.get('imported', 0)} statements{err_note}"
                        else:
                            results["sql_import"] = f"importer error: {imp_data.get('error', 'unknown')}"
                    except requests.exceptions.Timeout:
                        results["sql_import"] = "importer timed out (may still be running in background)"
                    except Exception as e:
                        results["sql_import"] = f"importer HTTP error: {e}"
            except Exception as e:
                results["sql_import"] = f"sql import setup error: {e}"
    else:
        results["sql_import"] = "skipped (DB_IMPORT_SQL_PATH not set)"

    # ── Step 7: Full Deploy — unzip all zip files via dummy server ───────────
    # Always push the latest full_deploy.php first so the dummy server definitely has
    # the version that writes database.php (older manually-uploaded versions did not).
    full_deploy_local = os.path.normpath(os.path.join(os.path.dirname(__file__), "..", "full_deploy.php"))
    if os.path.exists(full_deploy_local) and dummy_host:
        try:
            with open(full_deploy_local, "rb") as f:
                fd_bytes = f.read()
            dummy_dir2 = f"{cp['home']}/{dummy_host}"
            _cpanel_fileman_upload_to_dir(cp, working_auth, dummy_dir2, {"full_deploy.php": fd_bytes})
        except Exception:
            pass  # non-fatal — proceed with whatever is already on dummy server

    full_deploy_result = _full_deploy_to_subdomain({
        "server":    server,
        "subdomain": subdomain,
        "db_name":   db_name,
        "db_user":   db_user,
        "db_pass":   db_password,
    })
    results["full_deploy"] = full_deploy_result.get("message", str(full_deploy_result))

    # ── Step 8: Insert into subdomain_settings ───────────────────────────────
    db_host = REMOTE_DB_HOSTS.get(server, "localhost")
    try:
        main_conn = get_db_connection()
        if main_conn:
            cur = main_conn.cursor(dictionary=True)
            cur.execute("SELECT id FROM subdomain_settings WHERE subdomain=%s AND server=%s",
                        (subdomain, server))
            existing = cur.fetchone()
            if existing:
                results["record"] = f"record already exists (id {existing['id']}) — not duplicated"
            else:
                cur.execute("""
                    INSERT INTO subdomain_settings
                        (server, subdomain, db_host, db_name, db_user, db_pass,
                         site_name, logo_url, theme_color, status)
                    VALUES (%s, %s, %s, %s, %s, %s, %s, '', '#ffffff', 'active')
                """, (server, subdomain, db_host, db_name, db_user, db_password,
                      subdomain.capitalize()))
                main_conn.commit()
                results["record"] = "inserted into subdomain_settings"
            cur.close()
            main_conn.close()
    except Exception as e:
        results["record"] = f"DB insert error: {e}"

    # ── Step 9: Trigger AutoSSL to issue HTTPS certificate ───────────────────
    # cPanel UI triggers AutoSSL automatically on subdomain creation.
    # Our API call does not — so we trigger it manually here.
    try:
        ssl_data, _ = _cpanel_call(cp, single_auth, "SSL/start_autossl_check", {})
        if ssl_data.get("status") == 1:
            results["ssl"] = "AutoSSL triggered — certificate will be ready in a few minutes"
        else:
            errs = ssl_data.get("errors") or [str(ssl_data)]
            results["ssl"] = f"AutoSSL trigger warning: {'; '.join(str(e) for e in errs)}"
    except Exception as e:
        results["ssl"] = f"AutoSSL trigger failed: {e}"

    return {
        "success": True,
        "message": f"Setup complete for '{full_sub}'",
        "full_domain": full_sub,
        "db_name":     db_name,
        "db_user":     db_user,
        "db_password": db_password,
        "db_host":     db_host,
        "steps":       results,
    }


# ─── Upload mvcdeploy.php Script ───────────────────────────────────────────────
# @deploy-doc upload-deploy-script
# Sends Mvcdeploy.php content to live subdomain via Cssupdate::receive_script().
# For HostGator: uses cPanel API instead (mod_security blocks direct POST).
# @deploy-doc-end

MVCDEPLOY_SCRIPT_PATH = os.path.join(os.path.dirname(__file__), "..", "mvcdeploy.php")


def _upload_files_via_cpanel(subdomain: dict, files: dict, remote_subdir: str) -> dict:
    """
    Upload files directly to any subdomain path using cPanel UAPI (Fileman::upload_files).
    Completely bypasses mod_security — uses cPanel HTTPS API with token auth.
    files: {filename: content_string}
    remote_subdir: path relative to subdomain root (e.g. 'assets/inilabs')
    """
    server = subdomain.get("server", "").lower()
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
    server = subdomain.get("server", "").lower()
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
    server = subdomain.get("server", "").lower()
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
    server = subdomain.get("server", "").lower()
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
# @deploy-doc mvc-deploy
# Deploys local mvc.zip to a live subdomain.
# GoDaddy flow: checks Mvcdeploy/check → auto-uploads controller if missing →
#   POSTs mvc.zip to Mvcdeploy/receive → server renames mvc→mvc1, unzips, restores configs.
# HostGator flow (mod_security blocks direct POST):
#   1. Uploads mvc.zip to dummy server via cPanel API (port 2083)
#   2. GET bootstrap_copy.php on dummy server with browser User-Agent
#   3. Dummy PHP extracts zip, restores database.php + css_update_config.php
# Config files preserved: mvc/config/development/database.php, mvc/config/css_update_config.php
# MVC_ZIP_PATH and MVC_DEPLOY_API_KEY set in python/.env
# @deploy-doc-end


def _upload_files_via_ftp(ftp_config: dict, remote_dir: str, files: dict) -> dict:
    """
    Upload files to remote_dir (relative to FTP home) via a single FTP connection.
    files: {filename: bytes_or_str}
    Returns dict with success/message/updated/failed.
    If FTP password is wrong, error message instructs user to update python/.env.
    """
    try:
        ftp = ftplib.FTP()
        ftp.connect(ftp_config["host"], int(ftp_config.get("port", 21)), timeout=60)
        ftp.login(ftp_config["user"], ftp_config["pass"])
        try:
            ftp.cwd(remote_dir)
        except ftplib.error_perm as e:
            ftp.quit()
            return {"success": False, "message": f"FTP directory not found: {remote_dir} — {e}"}
        updated, failed = [], []
        for filename, content in files.items():
            try:
                if isinstance(content, str):
                    content = content.encode("utf-8")
                ftp.storbinary(f"STOR {filename}", io.BytesIO(content))
                updated.append(filename)
            except Exception as e:
                failed.append(f"{filename}: {e}")
        ftp.quit()
        return {
            "success": bool(updated) and not failed,
            "message": "Uploaded: " + ", ".join(updated) + (" | Failed: " + ", ".join(failed) if failed else "") + " (via FTP)",
            "updated": updated,
            "failed": failed,
        }
    except ftplib.error_perm as e:
        return {"success": False, "message": f"FTP login failed — update 'pass' in FTP_CONFIGS in python/.env then restart Python server: {e}"}
    except Exception as e:
        return {"success": False, "message": f"FTP connection error: {e}"}


def _godaddy_add_dns_record(domain: str, name: str) -> dict:
    """Add/update an A record in GoDaddy's cloud DNS (domaincontrol.com nameservers).
    IP is resolved dynamically from the root domain so it stays correct if the server moves."""
    if not GODADDY_API_KEY or not GODADDY_API_SECRET:
        return {"success": False, "message": "GODADDY_API_KEY/SECRET not set in .env"}
    try:
        ip = socket.gethostbyname(domain)
    except Exception:
        ip = "118.139.183.79"  # fallback — GoDaddy server IP
    url = f"https://api.godaddy.com/v1/domains/{domain}/records/A/{name}"
    headers = {
        "Authorization": f"sso-key {GODADDY_API_KEY}:{GODADDY_API_SECRET}",
        "Content-Type": "application/json",
    }
    try:
        resp = requests.put(url, headers=headers, json=[{"data": ip, "ttl": 3600}], timeout=30)
        if resp.status_code == 200:
            return {"success": True, "message": f"{name}.{domain} → {ip} added to GoDaddy DNS"}
        else:
            return {"success": False, "message": f"GoDaddy DNS API error {resp.status_code}: {resp.text[:300]}"}
    except Exception as e:
        return {"success": False, "message": f"GoDaddy DNS request failed: {e}"}


def _ftp_mkdirs(ftp, path: str):
    """Create nested directories relative to FTP home. Ignores errors for existing dirs."""
    parts = [p for p in path.replace('\\', '/').split('/') if p]
    current = ''
    for part in parts:
        current = f"{current}/{part}" if current else part
        try:
            ftp.mkd(current)
        except ftplib.error_perm:
            pass  # already exists — fine


def _upload_mvc_zip_to_dummy(server: str, zip_bytes: bytes) -> dict:
    """
    Upload mvc.zip to the dummy server for the given server (hostgator / myschools).
    Called once before deploying to multiple subdomains — Rocket button then just
    calls bootstrap_copy.php / trigger() without re-uploading the zip each time.
    """
    if server not in FTP_CONFIGS:
        return {"success": False, "message": f"No FTP config for server: {server}"}
    dummy_host = DUMMY_SERVERS.get(server, "")
    if not dummy_host:
        return {"success": False, "message": f"No dummy server configured for: {server}"}

    ftp_cfg = FTP_CONFIGS[server]

    if server == "hostgator":
        # FTP chrooted to home — dummy1 is directly in home (no public_html)
        dummy_dir = dummy_host  # "dummy1.ourschoolerp.com"
    else:
        # BigRock: FTP home is account root, dummy1 is directly there too
        dummy_dir = dummy_host  # "dummy1.myschoolserp.com"

    return _upload_files_via_ftp(ftp_cfg, dummy_dir, {"mvc.zip": zip_bytes})


def _upload_assets_zip_to_dummy(server: str, zip_bytes: bytes) -> dict:
    """Upload assets.zip to the dummy server for the given FTP-based server."""
    if server not in FTP_CONFIGS:
        return {"success": False, "message": f"No FTP config for server: {server}"}
    dummy_host = DUMMY_SERVERS.get(server, "")
    if not dummy_host:
        return {"success": False, "message": f"No dummy server configured for: {server}"}
    return _upload_files_via_ftp(FTP_CONFIGS[server], dummy_host, {"assets.zip": zip_bytes})


def _upload_frontend_zip_to_dummy(server: str, zip_bytes: bytes) -> dict:
    """Upload frontend.zip to the dummy server for the given FTP-based server."""
    if server not in FTP_CONFIGS:
        return {"success": False, "message": f"No FTP config for server: {server}"}
    dummy_host = DUMMY_SERVERS.get(server, "")
    if not dummy_host:
        return {"success": False, "message": f"No dummy server configured for: {server}"}
    return _upload_files_via_ftp(FTP_CONFIGS[server], dummy_host, {"frontend.zip": zip_bytes})


def _deploy_mvc_via_ftp(subdomain: dict, zip_bytes: bytes) -> dict:
    """
    FTP-based MVC deploy. Two strategies depending on server:

    HostGator: mvc.zip expected already on dummy server (uploaded via /upload-mvc-zip).
      Just calls bootstrap_copy.php — no FTP per subdomain. Fast.

    BigRock (myschools): mvc.zip expected already on dummy server.
      Calls bootstrap_copy.php on dummy — no per-subdomain FTP. Fast.

    If mvc.zip is NOT on dummy (first run or stale), returns a clear error message
    telling the user to click "Upload MVC to Dummy" first.
    """
    server = subdomain.get("server", "").lower()
    ftp_cfg = FTP_CONFIGS[server]
    domain_suffix = SERVER_DOMAINS.get(server, "")
    sub_name = subdomain["subdomain"]

    dummy_host = DUMMY_SERVERS.get(server, "")
    if not dummy_host:
        return {"success": False, "message": f"No dummy server configured for: {server}"}

    try:
        r = requests.get(
            f"https://{dummy_host}/bootstrap_copy.php",
            params={"k": CSS_UPDATE_API_KEY, "s": sub_name, "d": f".{domain_suffix}"},
            headers=BROWSER_HEADERS,
            timeout=120,
            verify=False,
        )
        try:
            result = r.json()
            # bootstrap_copy returns success:false with "mvc.zip not found" when zip missing
            if not result.get("success") and "mvc.zip" in result.get("message", "").lower():
                result["message"] = (
                    "mvc.zip not on dummy server — click 'Upload MVC to Dummy' button first, then retry Rocket."
                )
            return result
        except ValueError:
            return {"success": False, "message": f"bootstrap_copy GET HTTP {r.status_code}: {r.text[:200]}"}
    except requests.exceptions.RequestException as e:
        return {"success": False, "message": f"bootstrap_copy GET failed: {e}"}


def _deploy_mvc_to_subdomain(subdomain: dict, zip_bytes: bytes) -> dict:
    """
    Upload mvc.zip to the live subdomain via the Mvcdeploy CI controller.
    URL: https://{subdomain}.{domain}/mvcdeploy/receive
    Auto-checks controller exists first; uploads via Cssupdate if missing.
    """
    server = subdomain.get("server", "").lower()
    if server not in SERVER_DOMAINS:
        return {"success": False, "message": f"No domain mapping for server: {server}"}

    base_domain  = SERVER_DOMAINS[server]
    sub_name     = subdomain['subdomain']
    check_url    = f"https://{sub_name}.{base_domain}/mvcdeploy/check"
    deploy_url   = f"https://{sub_name}.{base_domain}/mvcdeploy/receive"

    # ── Step 1: Check if Mvcdeploy controller exists ────────────────────────
    # Use browser User-Agent — python-requests UA is blocked by mod_security on some hosts.
    # Treat 406 as "exists": server responded but blocked the request, meaning the
    # controller IS there and mod_security is the issue (not a missing file).
    controller_exists = False
    try:
        check = requests.get(check_url, timeout=10, verify=False, headers=BROWSER_HEADERS)
        controller_exists = check.status_code in [200, 406]
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
    # FTP-configured servers (HostGator/BigRock): upload via FTP then trigger extraction.
    # This bypasses mod_security entirely and uses correct subdomain paths.
    if server in FTP_CONFIGS:
        result = _deploy_mvc_via_ftp(subdomain, zip_bytes)
        if not controller_exists and result.get("success"):
            result["message"] += " (Mvcdeploy.php was auto-uploaded first)"
        return result

    # Other servers (e.g. GoDaddy): direct HTTP POST to receive()
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
    server = subdomain.get("server", "").lower()
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


@app.post("/upload-mvc-zip/{server}")
async def upload_mvc_zip(server: str):
    """
    Upload local mvc.zip to the dummy server for HostGator or BigRock (myschools).
    Call this ONCE before clicking Rocket on multiple subdomains — Rocket will then
    just call bootstrap_copy.php without re-uploading the zip each time.
    GoDaddy does not use this endpoint (direct HTTP POST per subdomain).
    """
    if server not in FTP_CONFIGS:
        raise HTTPException(status_code=400, detail=f"No FTP config for server '{server}'. Valid: {list(FTP_CONFIGS.keys())}")
    if server not in DUMMY_SERVERS:
        raise HTTPException(status_code=400, detail=f"No dummy server configured for '{server}'")
    if not os.path.exists(MVC_ZIP_PATH):
        raise HTTPException(status_code=500, detail=f"Local mvc.zip not found: {MVC_ZIP_PATH}")

    with open(MVC_ZIP_PATH, "rb") as f:
        zip_bytes = f.read()

    result = _upload_mvc_zip_to_dummy(server, zip_bytes)
    return result


@app.post("/upload-assets-zip/{server}")
async def upload_assets_zip(server: str):
    """
    Upload local assets.zip to the dummy server for HostGator / MySchools / Schoolhour / Collegehour.
    Call this ONCE before clicking Deploy Assets on multiple subdomains.
    GoDaddy does not use this endpoint (direct POST per subdomain).
    """
    if server not in FTP_CONFIGS:
        raise HTTPException(status_code=400, detail=f"No FTP config for '{server}'. FTP servers only.")
    if server not in DUMMY_SERVERS:
        raise HTTPException(status_code=400, detail=f"No dummy server configured for '{server}'")
    if not os.path.exists(ASSETS_ZIP_PATH):
        raise HTTPException(status_code=500, detail=f"Local assets.zip not found: {ASSETS_ZIP_PATH}")
    with open(ASSETS_ZIP_PATH, "rb") as f:
        zip_bytes = f.read()
    return _upload_assets_zip_to_dummy(server, zip_bytes)


@app.post("/upload-frontend-zip/{server}")
async def upload_frontend_zip(server: str):
    """Upload local frontend.zip to the dummy server for FTP-based servers."""
    if server not in FTP_CONFIGS:
        raise HTTPException(status_code=400, detail=f"No FTP config for '{server}'. FTP servers only.")
    if server not in DUMMY_SERVERS:
        raise HTTPException(status_code=400, detail=f"No dummy server configured for '{server}'")
    if not os.path.exists(FRONTEND_ZIP_PATH):
        raise HTTPException(status_code=500, detail=f"Local frontend.zip not found: {FRONTEND_ZIP_PATH}")
    with open(FRONTEND_ZIP_PATH, "rb") as f:
        zip_bytes = f.read()
    return _upload_frontend_zip_to_dummy(server, zip_bytes)


def _deploy_assets_to_subdomain(subdomain: dict, zip_bytes: bytes) -> dict:
    """
    Deploy assets.zip to a live subdomain.
    FTP servers: calls bootstrap_copy.php?type=assets on dummy (zip must be uploaded first).
    GoDaddy: direct HTTP POST to Assetsdeploy CI controller; auto-uploads it if missing.
    """
    server = subdomain.get("server", "").lower()
    if server not in SERVER_DOMAINS:
        return {"success": False, "message": f"No domain mapping for server: {server}"}
    base_domain = SERVER_DOMAINS[server]
    sub_name    = subdomain['subdomain']

    # FTP servers: call bootstrap_copy.php with type=assets on dummy server
    if server in FTP_CONFIGS:
        dummy_host = DUMMY_SERVERS.get(server, "")
        if not dummy_host:
            return {"success": False, "message": f"No dummy server configured for: {server}"}
        try:
            r = requests.get(
                f"https://{dummy_host}/bootstrap_copy.php",
                params={"k": CSS_UPDATE_API_KEY, "s": sub_name, "d": f".{base_domain}", "type": "assets"},
                headers=BROWSER_HEADERS,
                timeout=120,
                verify=False,
            )
            try:
                result = r.json()
                if not result.get("success") and "assets.zip" in result.get("message", "").lower():
                    result["message"] = (
                        "assets.zip not on dummy server — click 'Upload Assets to Dummy' button first, then retry."
                    )
                return result
            except ValueError:
                return {"success": False, "message": f"bootstrap_copy GET {r.status_code}: {r.text[:200]}"}
        except requests.exceptions.RequestException as e:
            return {"success": False, "message": f"bootstrap_copy GET failed: {e}"}

    # GoDaddy: upload assets.zip via cPanel Fileman API (avoids PHP post_max_size limit),
    # then GET /assetsdeploy/trigger to extract (same pattern as Mvcdeploy.trigger).
    cp = CPANEL_CONFIGS.get(server)
    if not cp:
        return {"success": False, "message": f"No cPanel config for: {server}"}

    sub_root = f"{cp['home']}/public_html/{sub_name}.{base_domain}"
    api_base = f"https://{cp['host']}:{cp['port']}"
    cpanel_headers = {"Authorization": f"cpanel {cp['user']}:{cp['token']}"}

    # ── Step 1: Upload assets.zip to live subdomain webroot via cPanel Fileman ──
    try:
        up = requests.post(
            f"{api_base}/execute/Fileman/upload_files",
            headers=cpanel_headers,
            data={"dir": sub_root, "overwrite": 1},
            files={"file-0": ("assets.zip", zip_bytes, "application/zip")},
            verify=False,
            timeout=180,
        )
        ur = up.json()
        if ur.get("status") != 1:
            return {"success": False, "message": f"cPanel Fileman upload failed: {ur.get('errors', ur)}"}
    except Exception as e:
        return {"success": False, "message": f"cPanel upload error: {e}"}

    # ── Step 2: Auto-upload Assetsdeploy.php if missing ────────────────────────
    check_url   = f"https://{sub_name}.{base_domain}/assetsdeploy/check"
    trigger_url = f"https://{sub_name}.{base_domain}/assetsdeploy/trigger"

    controller_exists = False
    try:
        check = requests.get(check_url, timeout=10, verify=False, headers=BROWSER_HEADERS)
        controller_exists = check.status_code in [200, 406]
    except requests.exceptions.RequestException:
        pass

    if not controller_exists:
        local_ctrl = os.path.abspath(
            os.path.join(os.path.dirname(__file__), "..", "mvc", "controllers", "Assetsdeploy.php")
        )
        if not os.path.exists(local_ctrl):
            return {"success": False, "message": "Assetsdeploy.php not found locally. Cannot auto-upload."}
        with open(local_ctrl, "r", encoding="utf-8") as f:
            ctrl_content = f.read()
        upload_result = _upload_controller_to_subdomain(subdomain, "Assetsdeploy.php", ctrl_content)
        if not upload_result.get("success"):
            return {
                "success": False,
                "message": f"Assetsdeploy controller missing. Auto-upload failed: {upload_result.get('message')}. "
                           f"Ensure Cssupdate.php is deployed on {sub_name}.{base_domain} first.",
            }

    # ── Step 3: GET trigger to extract assets.zip on live server ───────────────
    try:
        r = requests.get(
            trigger_url,
            params={"api_key": CSS_UPDATE_API_KEY},
            headers=BROWSER_HEADERS,
            timeout=180,
            verify=False,
        )
        try:
            result = r.json()
            if not controller_exists and result.get("success"):
                result["message"] += " (Assetsdeploy.php was auto-uploaded first)"
            return result
        except ValueError:
            return {"success": False, "message": f"trigger GET {r.status_code}: {r.text[:200]}"}
    except requests.exceptions.ConnectionError:
        return {"success": False, "message": f"Cannot connect to {trigger_url}"}
    except requests.exceptions.Timeout:
        return {"success": False, "message": "Trigger request timed out — server may still be processing"}
    except requests.exceptions.RequestException as e:
        return {"success": False, "message": str(e)}


@app.post("/ftp-upload-file")
async def ftp_upload_file(request: Request):
    """
    Upload a single file from localhost to the same relative path on ALL active
    subdomains of the selected server.
    Body: { "server": "hostgator", "file_path": "frontend/default/views/partials/footer.blade.php" }
    """
    body      = await request.json()
    server    = body.get("server", "").lower().strip()
    file_path = body.get("file_path", "").strip().replace("\\", "/").lstrip("/")

    if not server or not file_path:
        raise HTTPException(status_code=422, detail="'server' and 'file_path' are required")

    # Security: block path traversal
    if ".." in file_path:
        raise HTTPException(status_code=400, detail="Invalid file path — path traversal not allowed")

    local_base = os.path.normpath("C:/xampp/htdocs/ourschoolerp")
    local_file = os.path.normpath(os.path.join(local_base, file_path))
    if not local_file.startswith(local_base):
        raise HTTPException(status_code=400, detail="Path is outside project root")
    if not os.path.isfile(local_file):
        raise HTTPException(status_code=404, detail=f"File not found locally: {file_path}")

    with open(local_file, "rb") as f:
        content = f.read()

    filename = os.path.basename(file_path)
    file_dir = os.path.dirname(file_path).replace("\\", "/")  # e.g. "frontend/default/views/partials"

    # Fetch all active subdomains for this server
    conn = get_db_connection()
    if not conn:
        raise HTTPException(status_code=500, detail="DB connection failed")
    cur = conn.cursor(dictionary=True)
    cur.execute("SELECT * FROM subdomain_settings WHERE server=%s AND status='active'", (server,))
    subdomains = cur.fetchall()
    cur.close(); conn.close()

    if not subdomains:
        return {"success": False, "message": f"No active subdomains found for server '{server}'"}

    results = {}
    domain_suffix = SERVER_DOMAINS.get(server, "")

    # ── GoDaddy: cPanel Fileman API ────────────────────────────────────────────
    if server in CPANEL_CONFIGS:
        cp = CPANEL_CONFIGS[server]
        auth_headers = _build_cpanel_auth_headers(cp)
        ftp_cfg = FTP_CONFIGS.get(server, {})
        webroot = ftp_cfg.get("webroot", "public_html")
        for sub in subdomains:
            sub_name = sub["subdomain"]
            if file_dir:
                target_dir = f"{cp['home']}/{webroot}/{sub_name}.{domain_suffix}/{file_dir}" if webroot \
                             else f"{cp['home']}/{sub_name}.{domain_suffix}/{file_dir}"
            else:
                target_dir = f"{cp['home']}/{webroot}/{sub_name}.{domain_suffix}" if webroot \
                             else f"{cp['home']}/{sub_name}.{domain_suffix}"
            res = _cpanel_fileman_upload_to_dir(cp, auth_headers[0], target_dir, {filename: content})
            results[sub_name] = "uploaded" if res["updated"] else f"failed: {'; '.join(res['failed'])}"

    # ── FTP servers ────────────────────────────────────────────────────────────
    elif server in FTP_CONFIGS:
        ftp_cfg = FTP_CONFIGS[server]
        webroot = ftp_cfg.get("webroot", "")
        for sub in subdomains:
            sub_name = sub["subdomain"]
            if file_dir:
                remote_dir = f"{webroot}/{sub_name}.{domain_suffix}/{file_dir}" if webroot \
                             else f"{sub_name}.{domain_suffix}/{file_dir}"
            else:
                remote_dir = f"{webroot}/{sub_name}.{domain_suffix}" if webroot \
                             else f"{sub_name}.{domain_suffix}"
            try:
                ftp = ftplib.FTP()
                ftp.connect(ftp_cfg["host"], int(ftp_cfg.get("port", 21)), timeout=60)
                ftp.login(ftp_cfg["user"], ftp_cfg["pass"])
                _ftp_mkdirs(ftp, remote_dir)
                ftp.cwd(remote_dir)
                ftp.storbinary(f"STOR {filename}", io.BytesIO(content))
                ftp.quit()
                results[sub_name] = "uploaded"
            except Exception as e:
                results[sub_name] = f"failed: {e}"
    else:
        return {"success": False, "message": f"No upload method configured for server '{server}'"}

    success_count = sum(1 for v in results.values() if v == "uploaded")
    return {
        "success": success_count > 0,
        "message": f"Uploaded '{file_path}' to {success_count}/{len(results)} subdomain(s)",
        "results": results,
    }


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


@app.post("/deploy-assets-bulk")
async def deploy_assets_bulk(request: Request):
    """Deploy local assets.zip to multiple live subdomains at once."""
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

    if not os.path.exists(ASSETS_ZIP_PATH):
        raise HTTPException(status_code=500, detail=f"Local assets.zip not found: {ASSETS_ZIP_PATH}")

    with open(ASSETS_ZIP_PATH, "rb") as f:
        zip_bytes = f.read()

    results       = []
    success_count = 0

    for sub in subdomains:
        result = _deploy_assets_to_subdomain(sub, zip_bytes)
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
        "message":       f"Assets deployed to {success_count}/{len(subdomains)} subdomains",
        "details":       results,
    }


# ─── Frontend Deploy ──────────────────────────────────────────────────────────

def _deploy_frontend_to_subdomain(subdomain: dict, zip_bytes: bytes) -> dict:
    server = subdomain.get("server", "").lower()
    if server not in SERVER_DOMAINS:
        return {"success": False, "message": f"No domain mapping for server: {server}"}
    base_domain = SERVER_DOMAINS[server]
    sub_name    = subdomain["subdomain"]

    # FTP servers: call bootstrap_copy.php with type=frontend on dummy
    if server in FTP_CONFIGS:
        dummy_host = DUMMY_SERVERS.get(server, "")
        if not dummy_host:
            return {"success": False, "message": f"No dummy server for: {server}"}
        try:
            r = requests.get(
                f"https://{dummy_host}/bootstrap_copy.php",
                params={"k": CSS_UPDATE_API_KEY, "s": sub_name, "d": f".{base_domain}", "type": "frontend"},
                headers=BROWSER_HEADERS, timeout=120, verify=False,
            )
            try:
                result = r.json()
                if not result.get("success") and "frontend.zip" in result.get("message", "").lower():
                    result["message"] = "frontend.zip not on dummy server — click 'Upload Frontend to Dummy' first."
                return result
            except ValueError:
                return {"success": False, "message": f"bootstrap_copy GET {r.status_code}: {r.text[:200]}"}
        except requests.exceptions.RequestException as e:
            return {"success": False, "message": f"bootstrap_copy GET failed: {e}"}

    # GoDaddy: cPanel Fileman upload + GET trigger (avoids PHP post_max_size limit)
    cpanel_cfg = CPANEL_CONFIGS.get(server)
    if not cpanel_cfg:
        return {"success": False, "message": f"No cPanel config for server: {server}"}

    remote_dir   = f"{cpanel_cfg['home']}/public_html/{sub_name}.{base_domain}"
    upload_url   = f"https://{cpanel_cfg['host']}:{cpanel_cfg['port']}/execute/Fileman/upload_files"
    trigger_url  = f"https://{sub_name}.{base_domain}/frontenddeploy/trigger"
    check_url    = f"https://{sub_name}.{base_domain}/frontenddeploy/check"

    # Step 1: upload frontend.zip to subdomain webroot via cPanel Fileman
    try:
        upload_resp = requests.post(
            upload_url,
            headers={
                "Authorization": f"cpanel {cpanel_cfg['user']}:{cpanel_cfg['token']}",
            },
            files={"file-1": ("frontend.zip", zip_bytes, "application/zip")},
            data={"dir": remote_dir, "overwrite": "1"},
            timeout=300, verify=False,
        )
        upload_data = upload_resp.json()
        if not (upload_data.get("status") == 1 or upload_data.get("errors") is None):
            errors = upload_data.get("errors") or upload_data.get("messages") or str(upload_data)
            return {"success": False, "message": f"cPanel Fileman upload failed: {errors}"}
    except requests.exceptions.RequestException as e:
        return {"success": False, "message": f"cPanel Fileman upload error: {e}"}

    # Step 2: auto-upload Frontenddeploy.php controller if missing
    controller_exists = False
    try:
        check = requests.get(check_url, timeout=10, verify=False, headers=BROWSER_HEADERS)
        controller_exists = check.status_code in [200, 406]
    except requests.exceptions.RequestException:
        pass

    if not controller_exists:
        local_ctrl = os.path.abspath(
            os.path.join(os.path.dirname(__file__), "..", "mvc", "controllers", "Frontenddeploy.php")
        )
        if not os.path.exists(local_ctrl):
            return {"success": False, "message": "Frontenddeploy.php not found locally."}
        with open(local_ctrl, "r", encoding="utf-8") as f:
            ctrl_content = f.read()
        upload_result = _upload_controller_to_subdomain(subdomain, "Frontenddeploy.php", ctrl_content)
        if not upload_result.get("success"):
            return {"success": False, "message": f"Frontenddeploy.php auto-upload failed: {upload_result.get('message')}"}

    # Step 3: trigger extraction via GET
    try:
        resp = requests.get(
            trigger_url,
            params={"api_key": CSS_UPDATE_API_KEY},
            headers=BROWSER_HEADERS, timeout=180, verify=False,
        )
        try:
            result = resp.json()
            if not controller_exists and result.get("success"):
                result["message"] += " (Frontenddeploy.php auto-uploaded first)"
            return result
        except ValueError:
            return {"success": False, "message": f"HTTP {resp.status_code}: unexpected response"}
    except requests.exceptions.ConnectionError:
        return {"success": False, "message": f"Cannot connect to {trigger_url}"}
    except requests.exceptions.Timeout:
        return {"success": False, "message": "Request timed out — server may still be processing"}
    except requests.exceptions.RequestException as e:
        return {"success": False, "message": str(e)}


@app.post("/deploy-frontend-bulk")
async def deploy_frontend_bulk(request: Request):
    """Deploy local frontend.zip to multiple live subdomains at once."""
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

    if not os.path.exists(FRONTEND_ZIP_PATH):
        raise HTTPException(status_code=500, detail=f"Local frontend.zip not found: {FRONTEND_ZIP_PATH}")

    with open(FRONTEND_ZIP_PATH, "rb") as f:
        zip_bytes = f.read()

    results       = []
    success_count = 0

    for sub in subdomains:
        result = _deploy_frontend_to_subdomain(sub, zip_bytes)
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
        "message":       f"Frontend deployed to {success_count}/{len(subdomains)} subdomains",
        "details":       results,
    }


# ─── Full Deploy (New Subdomain Setup) ────────────────────────────────────────

def _full_deploy_to_subdomain(subdomain: dict) -> dict:
    """
    Calls full_deploy.php on the dummy server.
    Extracts ALL zip files (assets, frontend, mvc, etc.) to the target subdomain.
    Used for NEW subdomain creation/full redeployment.
    """
    server = subdomain.get("server", "").lower()
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
                "db_user":       subdomain.get("db_user", ""),
                "db_name":       subdomain.get("db_name", ""),
                "db_pass":       subdomain.get("db_pass", ""),
            },
            headers=BROWSER_HEADERS,
            timeout=120,
            verify=False,
        )
        try:
            return resp.json()
        except ValueError:
            return {
                "success": False,
                "message": f"HTTP {resp.status_code}: full_deploy.php returned non-JSON (mod_security block or file missing). Response: {resp.text[:200]}",
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
