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


if __name__ == "__main__":
    import uvicorn
    uvicorn.run("main:app", host=APP_HOST, port=APP_PORT, reload=True)
