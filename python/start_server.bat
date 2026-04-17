@echo off
title OurSchoolERP Python Server
echo Starting OurSchoolERP Python API on port 8000...
cd /d "C:\xampp\htdocs\ourschoolerp\python"
python -m uvicorn main:app --host 0.0.0.0 --port 8000 --reload
pause
