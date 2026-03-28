import os
import mysql.connector
from mysql.connector import Error
from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from dotenv import load_dotenv
import json

# Load environment variables
load_dotenv()

app = FastAPI(title="Subdomain Table Creator")

# Add CORS middleware to allow requests from the PHP frontend
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"], # In production, restrict this to your domain
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Database connection for fetching subdomain settings from main DB
def get_main_db_connection():
    try:
        connection = mysql.connector.connect(
            host=os.getenv("DB_HOST"),
            user=os.getenv("DB_USER"),
            password=os.getenv("DB_PASS"),
            database=os.getenv("DB_NAME"),
            port=int(os.getenv("DB_PORT", 3306))
        )
        return connection
    except Error as e:
        print(f"Error connecting to main DB: {e}")
        return None

# Database connection for target subdomain
def get_target_db_connection(host, user, password, database):
    try:
        connection = mysql.connector.connect(
            host=host,
            user=user,
            password=password,
            database=database
        )
        return connection
    except Error as e:
        print(f"Error connecting to target DB {database} on {host}: {e}")
        return None

@app.get("/")
def read_root():
    return {"message": "Subdomain Table Creator API is running"}

@app.post("/create-tables/{subdomain_id}")
async def create_tables(subdomain_id: int):
    # 1. Fetch subdomain settings from main DB
    main_conn = get_main_db_connection()
    if not main_conn:
        raise HTTPException(status_code=500, detail="Could not connect to main database")
    
    try:
        cursor = main_conn.cursor(dictionary=True)
        cursor.execute("SELECT * FROM subdomain_settings WHERE id = %s", (subdomain_id,))
        subdomain = cursor.fetchone()
        cursor.close()
        main_conn.close()
    except Error as e:
        if main_conn: main_conn.close()
        raise HTTPException(status_code=500, detail=f"Database error while fetching subdomain: {e}")

    if not subdomain:
        raise HTTPException(status_code=404, detail=f"Subdomain with ID {subdomain_id} not found")

    # 2. Connect to target DB
    target_conn = get_target_db_connection(
        subdomain['db_host'],
        subdomain['db_user'],
        subdomain['db_pass'],
        subdomain['db_name']
    )
    
    if not target_conn:
        raise HTTPException(status_code=500, detail=f"Could not connect to target database: {subdomain['db_name']} on {subdomain['db_host']}")

    # 3. Read SQL file
    sql_path = r"C:\xampp\htdocs\ourschoolerp\new domains\new db tables\tables.sql"
    if not os.path.exists(sql_path):
        target_conn.close()
        raise HTTPException(status_code=500, detail=f"SQL file not found at {sql_path}")
        
    try:
        with open(sql_path, 'r') as f:
            sql_content = f.read()
    except Exception as e:
        target_conn.close()
        raise HTTPException(status_code=500, detail=f"Could not read SQL file: {e}")

    # 4. Execute SQL
    try:
        cursor = target_conn.cursor()
        
        # Split sql_content by semicolon to execute individual statements
        # This is a simple split, for more complex SQL you might need a proper parser
        statements = sql_content.split(';')
        executed_count = 0
        
        for statement in statements:
            stmt = statement.strip()
            if stmt:
                cursor.execute(stmt)
                executed_count += 1
            
        target_conn.commit()
        cursor.close()
        target_conn.close()
        
        return {
            "success": True, 
            "message": f"Tables created successfully for {subdomain['subdomain']}",
            "statements_executed": executed_count
        }
        
    except Error as e:
        if target_conn: 
            target_conn.rollback()
            target_conn.close()
        print(f"SQL Execution Error: {e}")
        raise HTTPException(status_code=500, detail=f"Error executing SQL on target database: {e}")

@app.post("/create-tables-bulk")
async def create_tables_bulk(server: str):
    # 1. Fetch all subdomain IDs for the given server
    main_conn = get_main_db_connection()
    if not main_conn:
        raise HTTPException(status_code=500, detail="Could not connect to main database")
    
    try:
        cursor = main_conn.cursor(dictionary=True)
        cursor.execute("SELECT id FROM subdomain_settings WHERE server = %s AND status = 'active'", (server,))
        subdomains = cursor.fetchall()
        cursor.close()
        main_conn.close()
    except Error as e:
        if main_conn: main_conn.close()
        raise HTTPException(status_code=500, detail=f"Database error while fetching subdomains for server {server}: {e}")

    if not subdomains:
        raise HTTPException(status_code=404, detail=f"No active subdomains found for server: {server}")

    results = []
    success_count = 0
    
    for sub in subdomains:
        try:
            # We call the create_tables logic here (reused)
            res = await create_tables(sub['id'])
            results.append({"id": sub['id'], "success": True, "message": res['message']})
            success_count += 1
        except Exception as e:
            results.append({"id": sub['id'], "success": False, "message": str(e)})

    return {
        "success": success_count > 0,
        "message": f"Processed {success_count} out of {len(subdomains)} subdomains for server {server}",
        "domains_processed": len(subdomains),
        "success_count": success_count,
        "details": results
    }

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)
