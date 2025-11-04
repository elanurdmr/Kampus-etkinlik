@echo off
echo ========================================
echo Akademik Takvim Sistemi - Backend
echo ========================================
echo.

echo Virtual environment aktif ediliyor...
call venv\Scripts\activate.bat

echo.
echo Backend baslatiliyor...
cd backend\app
python main.py

pause

