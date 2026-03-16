from db_connection import get_db_connection
from mysql.connector import Error

def fetch_students(limit=5):
    """
    Fetches a list of students from the database and prints their details.
    """
    connection = get_db_connection()
    if not connection:
        return

    try:
        cursor = connection.cursor(dictionary=True) # dictionary=True returns rows as dicts
        
        # Example Query: Fetch basic student information
        query = "SELECT studentID, name, roll, email, phone FROM student LIMIT %s"
        cursor.execute(query, (limit,))
        
        students = cursor.fetchall()
        
        if not students:
            print("No students found in the database.")
        else:
            print(f"{'ID':<5} | {'Name':<20} | {'Roll':<10} | {'Phone':<15}")
            print("-" * 60)
            for student in students:
                # Handle potential None values for clean printing
                name = student['name'] if student['name'] else "N/A"
                roll = student['roll'] if student['roll'] else "N/A"
                phone = student['phone'] if student['phone'] else "N/A"
                
                print(f"{student['studentID']:<5} | {name:<20} | {roll:<10} | {phone:<15}")

    except Error as e:
        print(f"Error while fetching data: {e}")
    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()
            print("\nDatabase connection closed.")

if __name__ == "__main__":
    print("--- Student Data Fetcher ---\n")
    fetch_students(10)
