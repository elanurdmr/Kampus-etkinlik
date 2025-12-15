#!/bin/bash

echo "========================================"
echo "Akademik Takvim Sistemi - Backend"
echo "========================================"
echo ""

# Proje kök dizinine git
cd "$(dirname "$0")"

# Virtual environment kontrolü
if [ ! -d "venv" ]; then
    echo "Virtual environment bulunamadı. Oluşturuluyor..."
    python3 -m venv venv
    echo "Virtual environment oluşturuldu."
fi

# Virtual environment'ı aktif et
echo "Virtual environment aktif ediliyor..."
source venv/bin/activate

# Gerekli paketleri kontrol et ve yükle
echo ""
echo "Paketler kontrol ediliyor..."
pip install -q -r requirements.txt

# Port 8000 kontrolü
PORT=8000
if lsof -Pi :$PORT -sTCP:LISTEN -t >/dev/null 2>&1 ; then
    echo ""
    echo "⚠️  UYARI: Port $PORT zaten kullanılıyor!"
    PIDS=$(lsof -ti:$PORT)
    echo "Port $PORT'i kullanan işlemler: $PIDS"
    read -p "Bu işlemleri durdurup devam etmek istiyor musunuz? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        kill -9 $PIDS 2>/dev/null
        sleep 1
        echo "Port $PORT temizlendi."
    else
        echo "Backend başlatılamadı. Port $PORT'i manuel olarak temizleyin."
        exit 1
    fi
fi

echo ""
echo "Backend başlatılıyor..."
cd backend/app

# .env dosyası kontrolü
if [ ! -f ".env" ]; then
    echo ""
    echo "⚠️  UYARI: .env dosyası bulunamadı!"
    echo "Backend/app klasöründe .env dosyası oluşturun:"
    echo "DATABASE_URL=mysql+pymysql://root@127.0.0.1:3306/kampus-sistemi"
    echo ""
    read -p "Devam etmek istiyor musunuz? (y/n): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

# Backend'i başlat
python main.py


