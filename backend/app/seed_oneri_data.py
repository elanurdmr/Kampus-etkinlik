# -*- coding: utf-8 -*-
"""
Öneri sistemi için örnek veri ekleme
"""
import sys
import io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

from database import SessionLocal
from models import IlgiAlani, Kulup, KulupEtkinligi, Kullanici
from datetime import datetime, timedelta

db = SessionLocal()

# İlgi alanları ekle
ilgi_alanlari = [
    {"alan_adi": "Spor", "aciklama": "Futbol, basketbol, voleybol gibi spor aktiviteleri"},
    {"alan_adi": "Müzik", "aciklama": "Enstrüman çalma, şarkı söyleme, beste yapma"},
    {"alan_adi": "Teknoloji", "aciklama": "Yazılım, donanım, yapay zeka, robotik"},
    {"alan_adi": "Sanat", "aciklama": "Resim, heykel, fotoğrafçılık, dijital sanat"},
    {"alan_adi": "Edebiyat", "aciklama": "Kitap okuma, şiir, hikaye yazma"},
    {"alan_adi": "Sinema", "aciklama": "Film izleme, film analizi, kısa film çekimi"},
    {"alan_adi": "Tiyatro", "aciklama": "Oyunculuk, sahne sanatları"},
    {"alan_adi": "Fotoğrafçılık", "aciklama": "Fotoğraf çekimi, düzenleme"},
    {"alan_adi": "Sosyal Sorumluluk", "aciklama": "Gönüllülük, toplum projeleri"},
    {"alan_adi": "Girişimcilik", "aciklama": "İş kurma, proje geliştirme"}
]

print("İlgi alanları ekleniyor...")
for alan_data in ilgi_alanlari:
    mevcut = db.query(IlgiAlani).filter(IlgiAlani.alan_adi == alan_data["alan_adi"]).first()
    if not mevcut:
        alan = IlgiAlani(**alan_data)
        db.add(alan)
db.commit()
print(f"✓ {len(ilgi_alanlari)} ilgi alanı eklendi")

# İlgi alanlarını tekrar getir (ID'ler için)
tum_ilgi_alanlari = db.query(IlgiAlani).all()
ilgi_alan_map = {alan.alan_adi: alan.id for alan in tum_ilgi_alanlari}

# Kulüpler ekle
kulupler = [
    {"kulup_adi": "Futbol Kulübü", "aciklama": "Haftada 2 gün antrenman ve turnuvalar", "ilgi_alani_id": ilgi_alan_map.get("Spor")},
    {"kulup_adi": "Basketbol Kulübü", "aciklama": "Basketbol severlerin buluşma noktası", "ilgi_alani_id": ilgi_alan_map.get("Spor")},
    {"kulup_adi": "Müzik Kulübü", "aciklama": "Enstrüman dersleri ve konserler", "ilgi_alani_id": ilgi_alan_map.get("Müzik")},
    {"kulup_adi": "Yazılım Kulübü", "aciklama": "Kodlama atölyeleri ve hackathon", "ilgi_alani_id": ilgi_alan_map.get("Teknoloji")},
    {"kulup_adi": "Resim Kulübü", "aciklama": "Resim atölyeleri ve sergiler", "ilgi_alani_id": ilgi_alan_map.get("Sanat")},
    {"kulup_adi": "Edebiyat Kulübü", "aciklama": "Kitap okuma ve tartışma grupları", "ilgi_alani_id": ilgi_alan_map.get("Edebiyat")},
    {"kulup_adi": "Sinema Kulübü", "aciklama": "Film gösterimleri ve analizleri", "ilgi_alani_id": ilgi_alan_map.get("Sinema")},
    {"kulup_adi": "Tiyatro Kulübü", "aciklama": "Oyun provaları ve gösterimler", "ilgi_alani_id": ilgi_alan_map.get("Tiyatro")},
    {"kulup_adi": "Fotoğraf Kulübü", "aciklama": "Fotoğraf gezileri ve sergiler", "ilgi_alani_id": ilgi_alan_map.get("Fotoğrafçılık")},
    {"kulup_adi": "Sosyal Sorumluluk Kulübü", "aciklama": "Topluma faydalı projeler", "ilgi_alani_id": ilgi_alan_map.get("Sosyal Sorumluluk")},
]

print("\nKulüpler ekleniyor...")
for kulup_data in kulupler:
    mevcut = db.query(Kulup).filter(Kulup.kulup_adi == kulup_data["kulup_adi"]).first()
    if not mevcut:
        kulup = Kulup(**kulup_data)
        db.add(kulup)
db.commit()
print(f"✓ {len(kulupler)} kulüp eklendi")

# Kulüpleri tekrar getir
tum_kulupler = db.query(Kulup).all()
kulup_map = {kulup.kulup_adi: kulup.id for kulup in tum_kulupler}

# Kulüp etkinlikleri ekle
bugun = datetime.now()
etkinlikler = [
    {
        "kulup_id": kulup_map.get("Futbol Kulübü"),
        "etkinlik_adi": "Fakülteler Arası Futbol Turnuvası",
        "aciklama": "Tüm fakültelerin katılacağı futbol turnuvası",
        "tarih": bugun + timedelta(days=7),
        "konum": "Spor Salonu",
        "ilgi_alanlari": str(ilgi_alan_map.get("Spor"))
    },
    {
        "kulup_id": kulup_map.get("Müzik Kulübü"),
        "etkinlik_adi": "Akustik Gitar Atölyesi",
        "aciklama": "Başlangıç seviyesi gitar dersi",
        "tarih": bugun + timedelta(days=3),
        "konum": "Müzik Odası",
        "ilgi_alanlari": str(ilgi_alan_map.get("Müzik"))
    },
    {
        "kulup_id": kulup_map.get("Yazılım Kulübü"),
        "etkinlik_adi": "Python ile Yapay Zeka Workshop",
        "aciklama": "Machine Learning temelleri ve uygulamaları",
        "tarih": bugun + timedelta(days=5),
        "konum": "Bilgisayar Laboratuvarı",
        "ilgi_alanlari": str(ilgi_alan_map.get("Teknoloji"))
    },
    {
        "kulup_id": kulup_map.get("Sinema Kulübü"),
        "etkinlik_adi": "Klasik Film Gösterimi: The Godfather",
        "aciklama": "Francis Ford Coppola'nın başyapıtı",
        "tarih": bugun + timedelta(days=4),
        "konum": "Konferans Salonu",
        "ilgi_alanlari": str(ilgi_alan_map.get("Sinema"))
    },
    {
        "kulup_id": kulup_map.get("Fotoğraf Kulübü"),
        "etkinlik_adi": "Doğa Fotoğrafçılığı Gezisi",
        "aciklama": "Belgrad Ormanı fotoğraf çekimi",
        "tarih": bugun + timedelta(days=10),
        "konum": "Kampüs Önü (Toplanma)",
        "ilgi_alanlari": f"{ilgi_alan_map.get('Fotoğrafçılık')},{ilgi_alan_map.get('Sanat')}"
    },
    {
        "kulup_id": kulup_map.get("Yazılım Kulübü"),
        "etkinlik_adi": "24 Saatlik Hackathon",
        "aciklama": "Takım halinde proje geliştirme yarışması",
        "tarih": bugun + timedelta(days=14),
        "konum": "Bilgisayar Laboratuvarı",
        "ilgi_alanlari": f"{ilgi_alan_map.get('Teknoloji')},{ilgi_alan_map.get('Girişimcilik')}"
    },
    {
        "kulup_id": kulup_map.get("Tiyatro Kulübü"),
        "etkinlik_adi": "Hamlet Oyunu Gösterimi",
        "aciklama": "Shakespeare'in ünlü oyunu",
        "tarih": bugun + timedelta(days=20),
        "konum": "Tiyatro Salonu",
        "ilgi_alanlari": f"{ilgi_alan_map.get('Tiyatro')},{ilgi_alan_map.get('Edebiyat')}"
    },
    {
        "kulup_id": kulup_map.get("Sosyal Sorumluluk Kulübü"),
        "etkinlik_adi": "Ağaç Dikme Etkinliği",
        "aciklama": "Kampüs yeşillendirme projesi",
        "tarih": bugun + timedelta(days=6),
        "konum": "Kampüs Bahçesi",
        "ilgi_alanlari": str(ilgi_alan_map.get("Sosyal Sorumluluk"))
    },
]

print("\nEtkinlikler ekleniyor...")
for etkinlik_data in etkinlikler:
    mevcut = db.query(KulupEtkinligi).filter(
        KulupEtkinligi.etkinlik_adi == etkinlik_data["etkinlik_adi"]
    ).first()
    if not mevcut:
        etkinlik = KulupEtkinligi(**etkinlik_data)
        db.add(etkinlik)
db.commit()
print(f"✓ {len(etkinlikler)} etkinlik eklendi")

# Demo kullanıcı ekle
demo_kullanici = db.query(Kullanici).filter(Kullanici.email == "demo@example.com").first()
if not demo_kullanici:
    demo_kullanici = Kullanici(
        ad="Demo",
        soyad="Öğrenci",
        email="demo@example.com",
        ogrenci_no="20220001"
    )
    db.add(demo_kullanici)
    db.commit()
    print("\n✓ Demo kullanıcı eklendi (ID: {})".format(demo_kullanici.id))
else:
    print(f"\n✓ Demo kullanıcı mevcut (ID: {demo_kullanici.id})")

db.close()

print("\n" + "="*50)
print("✅ Tüm örnek veriler başarıyla eklendi!")
print("="*50)
print("\nDemo Kullanıcı Bilgileri:")
print(f"  ID: {demo_kullanici.id}")
print(f"  Ad Soyad: {demo_kullanici.ad} {demo_kullanici.soyad}")
print(f"  E-posta: {demo_kullanici.email}")
print(f"  Öğrenci No: {demo_kullanici.ogrenci_no}")
print("\nBu kullanıcı ID'sini frontend'de kullanabilirsiniz!")

