import os
import mysql.connector
from mysql.connector import Error
from dotenv import load_dotenv

# 1. Load the environment variables from the .env file
# By default, load_dotenv() looks for a file named .env in the same directory
load_dotenv()

def get_db_connection():
    """
    Creates and returns a database connection using credentials from .env
    """
    try:
        # 2. Get credentials using os.getenv()
        connection = mysql.connector.connect(
            host=os.getenv("DB_HOST"),
            user=os.getenv("DB_USER"),
            password=os.getenv("DB_PASS"),
            database=os.getenv("DB_NAME"),
            port=os.getenv("DB_PORT", 3306) # Default to 3306 if not specified
        )
        
        if connection.is_connected():
            return connection
            
    except Error as e:
        print(f"Error while connecting to MySQL: {e}")
        return None

def test_connection():
    connection = get_db_connection()
    if connection:
        db_info = connection.server_info
        print(f"Connected to MySQL Server version {db_info}")
        
        cursor = connection.cursor()
        cursor.execute("SELECT DATABASE();")
        record = cursor.fetchone()
        print(f"You're connected to database: {record[0]}")
        
        # Close connection
        cursor.close()
        connection.close()
        print("MySQL connection is closed")

if __name__ == "__main__":
    print("Testing database connection...")
    test_connection()
