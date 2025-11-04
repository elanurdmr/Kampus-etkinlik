@echo off
echo ========================================
echo Kampus Etkinlik Sistemi - Tam Baslatma
echo ========================================
echo.

echo Backend ve Frontend ayri pencerelerde baslatiliyor...
echo.

REM Backend'i yeni bir pencerede başlat
start "Backend Server (Port 8000)" cmd /k "cd /d "%~dp0" && call venv\Scripts\activate.bat && cd backend\app && python main.py"

REM 2 saniye bekle (Backend'in başlaması için)
timeout /t 2 /nobreak > nul

REM Frontend'i yeni bir pencerede başlat
start "Frontend Server (Port 8080)" cmd /k "cd /d "%~dp0frontend" && php -S localhost:8080"

echo.
echo ========================================
echo ✓ Backend: http://localhost:8000
echo ✓ Frontend: http://localhost:8080
echo ========================================
echo.
echo Her iki server da ayri pencerelerde baslatildi!
echo Kapatmak icin her iki pencereyi de kapatabilirsiniz.
echo.

pause

