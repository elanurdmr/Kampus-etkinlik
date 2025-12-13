"""
Veritabanı tablolarını oluşturmak için script
"""
from database import engine, Base
from models import (
    AkademikEtkinlik, Kullanici, QRKod, Katilim, GeriSayimAyarlari,
    IlgiAlani, KullaniciIlgiAlani, Kulup, KulupEtkinligi, KullaniciEtkinlikTercihi,
    Kutuphane, KutuphaneRezervasyonu, KutuphaneDoluluk,
    OgretimUyesi, Randevu, Bildirim
)

def create_tables():
    """Tüm tabloları oluştur"""
    print("Tablolar oluşturuluyor...")
    Base.metadata.create_all(bind=engine)
    print("Tablolar başarıyla oluşturuldu!")

if __name__ == "__main__":
    create_tables()

