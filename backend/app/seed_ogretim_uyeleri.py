# -*- coding: utf-8 -*-
"""
Öğretim üyeleri için örnek veri ekleme
"""
import sys
import io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

from database import SessionLocal
from models import OgretimUyesi
import json

db = SessionLocal()

# Öğretim üyeleri verileri
ogretim_uyeleri = [
    {
        "ad": "Ahmet",
        "soyad": "Yılmaz",
        "email": "ahmet.yilmaz@universite.edu.tr",
        "unvan": "Prof. Dr.",
        "bolum": "Bilgisayar Mühendisliği",
        "ofis_no": "A-301",
        "telefon": "0212-555-0101",
        "calisma_saatleri": json.dumps({
            "pazartesi": "09:00-12:00, 14:00-17:00",
            "sali": "09:00-12:00",
            "carsamba": "14:00-17:00",
            "persembe": "09:00-12:00",
            "cuma": "09:00-12:00"
        }, ensure_ascii=False),
        "aktif": True
    },
    {
        "ad": "Ayşe",
        "soyad": "Kaya",
        "email": "ayse.kaya@universite.edu.tr",
        "unvan": "Doç. Dr.",
        "bolum": "Yazılım Mühendisliği",
        "ofis_no": "A-302",
        "telefon": "0212-555-0102",
        "calisma_saatleri": json.dumps({
            "pazartesi": "10:00-13:00",
            "sali": "10:00-13:00, 14:00-16:00",
            "carsamba": "10:00-13:00",
            "persembe": "14:00-17:00",
            "cuma": "10:00-12:00"
        }, ensure_ascii=False),
        "aktif": True
    },
    {
        "ad": "Mehmet",
        "soyad": "Demir",
        "email": "mehmet.demir@universite.edu.tr",
        "unvan": "Dr. Öğr. Üyesi",
        "bolum": "Bilgisayar Mühendisliği",
        "ofis_no": "A-303",
        "telefon": "0212-555-0103",
        "calisma_saatleri": json.dumps({
            "pazartesi": "13:00-16:00",
            "sali": "13:00-16:00",
            "carsamba": "09:00-12:00",
            "persembe": "13:00-16:00",
            "cuma": "13:00-15:00"
        }, ensure_ascii=False),
        "aktif": True
    },
    {
        "ad": "Fatma",
        "soyad": "Şahin",
        "email": "fatma.sahin@universite.edu.tr",
        "unvan": "Dr. Öğr. Üyesi",
        "bolum": "Yazılım Mühendisliği",
        "ofis_no": "A-304",
        "telefon": "0212-555-0104",
        "calisma_saatleri": json.dumps({
            "pazartesi": "14:00-17:00",
            "sali": "09:00-12:00",
            "carsamba": "14:00-17:00",
            "persembe": "09:00-12:00",
            "cuma": "14:00-16:00"
        }, ensure_ascii=False),
        "aktif": True
    },
    {
        "ad": "Ali",
        "soyad": "Öztürk",
        "email": "ali.ozturk@universite.edu.tr",
        "unvan": "Prof. Dr.",
        "bolum": "Endüstri Mühendisliği",
        "ofis_no": "B-201",
        "telefon": "0212-555-0105",
        "calisma_saatleri": json.dumps({
            "pazartesi": "09:00-12:00",
            "sali": "09:00-12:00",
            "carsamba": "09:00-12:00",
            "persembe": "14:00-17:00",
            "cuma": "09:00-12:00"
        }, ensure_ascii=False),
        "aktif": True
    },
    {
        "ad": "Zeynep",
        "soyad": "Arslan",
        "email": "zeynep.arslan@universite.edu.tr",
        "unvan": "Doç. Dr.",
        "bolum": "Endüstri Mühendisliği",
        "ofis_no": "B-202",
        "telefon": "0212-555-0106",
        "calisma_saatleri": json.dumps({
            "pazartesi": "10:00-13:00",
            "sali": "14:00-17:00",
            "carsamba": "10:00-13:00",
            "persembe": "10:00-13:00",
            "cuma": "10:00-12:00"
        }, ensure_ascii=False),
        "aktif": True
    },
    {
        "ad": "Can",
        "soyad": "Çelik",
        "email": "can.celik@universite.edu.tr",
        "unvan": "Dr. Öğr. Üyesi",
        "bolum": "Elektrik-Elektronik Mühendisliği",
        "ofis_no": "C-101",
        "telefon": "0212-555-0107",
        "calisma_saatleri": json.dumps({
            "pazartesi": "13:00-16:00",
            "sali": "13:00-16:00",
            "carsamba": "13:00-16:00",
            "persembe": "09:00-12:00",
            "cuma": "13:00-15:00"
        }, ensure_ascii=False),
        "aktif": True
    },
    {
        "ad": "Elif",
        "soyad": "Kurt",
        "email": "elif.kurt@universite.edu.tr",
        "unvan": "Dr. Öğr. Üyesi",
        "bolum": "Makine Mühendisliği",
        "ofis_no": "D-201",
        "telefon": "0212-555-0108",
        "calisma_saatleri": json.dumps({
            "pazartesi": "14:00-17:00",
            "sali": "09:00-12:00",
            "carsamba": "14:00-17:00",
            "persembe": "14:00-17:00",
            "cuma": "14:00-16:00"
        }, ensure_ascii=False),
        "aktif": True
    }
]

print("="*50)
print("Öğretim Üyeleri Ekleniyor...")
print("="*50)

eklenen_sayisi = 0
guncellenen_sayisi = 0

for uyesi_data in ogretim_uyeleri:
    # Email kontrolü
    mevcut = db.query(OgretimUyesi).filter(
        OgretimUyesi.email == uyesi_data["email"]
    ).first()
    
    if not mevcut:
        uyesi = OgretimUyesi(**uyesi_data)
        db.add(uyesi)
        eklenen_sayisi += 1
        print(f"✓ {uyesi_data['unvan']} {uyesi_data['ad']} {uyesi_data['soyad']} eklendi")
    else:
        # Mevcut kaydı güncelle
        for key, value in uyesi_data.items():
            setattr(mevcut, key, value)
        guncellenen_sayisi += 1
        print(f"↻ {uyesi_data['unvan']} {uyesi_data['ad']} {uyesi_data['soyad']} güncellendi")

db.commit()

print("\n" + "="*50)
print(f"✅ {eklenen_sayisi} öğretim üyesi eklendi")
if guncellenen_sayisi > 0:
    print(f"↻ {guncellenen_sayisi} öğretim üyesi güncellendi")
print("="*50)

# Toplam sayıyı göster
toplam = db.query(OgretimUyesi).filter(OgretimUyesi.aktif == True).count()
print(f"\n📊 Toplam aktif öğretim üyesi sayısı: {toplam}")

db.close()
