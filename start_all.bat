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

REM Frontend XAMPP Apache ile calisacak
echo Frontend XAMPP Apache ile calisacak
echo XAMPP Control Panel'den Apache'yi baslatin
echo Frontend adresi: http://localhost/yazilim-tasarim-mimarisi-proje/frontend/

echo.
echo ========================================
echo ✓ Backend: http://localhost:8000
echo ✓ Frontend: http://localhost/yazilim-tasarim-mimarisi-proje/frontend/
echo ========================================
echo.
echo NOT: Frontend icin XAMPP Apache'nin calisiyor olmasi gerekiyor!
echo XAMPP Control Panel'den Apache'yi baslatin.
echo.
echo Her iki server da ayri pencerelerde baslatildi!
echo Kapatmak icin her iki pencereyi de kapatabilirsiniz.
echo.

pause

