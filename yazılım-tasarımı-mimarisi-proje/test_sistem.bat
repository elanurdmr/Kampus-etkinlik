@echo off
echo ========================================
echo Akademik Takvim Sistemi - Test
echo ========================================
echo.

echo Virtual environment aktif ediliyor...
call venv\Scripts\activate.bat

echo.
echo API testleri calistiriliyor...
cd backend\app
python test_api.py

echo.
pause

