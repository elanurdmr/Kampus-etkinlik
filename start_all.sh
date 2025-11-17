#!/bin/bash

echo "========================================"
echo "Kampüs Etkinlik Sistemi - Tam Başlatma"
echo "========================================"
echo ""

# Projenin ana dizinine git
cd "$(dirname "$0")"

echo "Backend ve Frontend ayrı terminallerde başlatılıyor..."
echo ""

# Backend'i yeni bir terminal penceresinde başlat
osascript <<EOF
tell application "Terminal"
    do script "cd '$PWD' && source venv/bin/activate && cd backend/app && python main.py"
    set custom title of front window to "Backend Server (Port 8000)"
end tell
EOF

# 2 saniye bekle (Backend'in başlaması için)
sleep 2

# Frontend'i yeni bir terminal penceresinde başlat
osascript <<EOF
tell application "Terminal"
    do script "cd '$PWD/frontend' && php -S localhost:8080"
    set custom title of front window to "Frontend Server (Port 8080)"
end tell
EOF

echo ""
echo "========================================"
echo "✓ Backend: http://localhost:8000"
echo "✓ Frontend: http://localhost:8080"
echo "========================================"
echo ""
echo "Her iki server da ayrı terminal pencerelerinde başlatıldı!"
echo "Kapatmak için her iki terminal penceresini de kapatabilirsiniz (Ctrl+C)."
echo ""

