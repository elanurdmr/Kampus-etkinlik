"""
Doğuş Üniversitesi akademik kadro sayfasından öğretim üyelerini çeken script
Not: Web scraping için beautifulsoup4 ve requests kütüphaneleri gereklidir.
Şimdilik örnek verilerle çalışmaktadır.
"""
from sqlalchemy.orm import Session
from database import SessionLocal, engine
from models import OgretimUyesi, Base
import re

# Veritabanı tabloları oluştur
Base.metadata.create_all(bind=engine)

def temizle_metin(metin):
    """Metindeki gereksiz boşlukları temizle"""
    if not metin:
        return ""
    return re.sub(r'\s+', ' ', metin.strip())

def ogretim_uyesi_ekle(db: Session, ad, soyad, unvan, bolum, email=None, telefon=None, ofis_no=None):
    """Veritabanına öğretim üyesi ekle"""
    
    # Email oluştur (yoksa)
    if not email:
        ad_temiz = ad.lower().replace('ı', 'i').replace('ş', 's').replace('ğ', 'g').replace('ü', 'u').replace('ö', 'o').replace('ç', 'c')
        soyad_temiz = soyad.lower().replace('ı', 'i').replace('ş', 's').replace('ğ', 'g').replace('ü', 'u').replace('ö', 'o').replace('ç', 'c')
        email = f"{ad_temiz}.{soyad_temiz}@dogus.edu.tr"
    
    # Mevcut kontrolü
    mevcut = db.query(OgretimUyesi).filter(OgretimUyesi.email == email).first()
    if mevcut:
        print(f"✗ {unvan} {ad} {soyad} zaten mevcut")
        return None
    
    # Yeni öğretim üyesi oluştur
    ogretim_uyesi = OgretimUyesi(
        ad=ad,
        soyad=soyad,
        email=email,
        unvan=unvan,
        bolum=bolum,
        telefon=telefon,
        ofis_no=ofis_no,
        aktif=True
    )
    
    db.add(ogretim_uyesi)
    db.commit()
    db.refresh(ogretim_uyesi)
    print(f"✓ {unvan} {ad} {soyad} eklendi")
    return ogretim_uyesi

def seed_dogus_ogretim_uyeleri():
    """
    Doğuş Üniversitesi örnek öğretim üyelerini ekle
    Not: Gerçek web scraping için site yapısı analiz edilmeli
    """
    db = SessionLocal()
    
    try:
        print("\n" + "="*60)
        print("Doğuş Üniversitesi Öğretim Üyeleri Ekleniyor")
        print("="*60 + "\n")
        
        # Mühendislik Fakültesi
        print("\n--- Mühendislik Fakültesi ---")
        ogretim_uyesi_ekle(db, "Ahmet", "Yılmaz", "Prof. Dr.", "Bilgisayar Mühendisliği", ofis_no="A-301")
        ogretim_uyesi_ekle(db, "Mehmet", "Kaya", "Doç. Dr.", "Bilgisayar Mühendisliği", ofis_no="A-302")
        ogretim_uyesi_ekle(db, "Ayşe", "Demir", "Dr. Öğr. Üyesi", "Bilgisayar Mühendisliği", ofis_no="A-303")
        ogretim_uyesi_ekle(db, "Fatma", "Şahin", "Prof. Dr.", "Yazılım Mühendisliği", ofis_no="A-304")
        ogretim_uyesi_ekle(db, "Ali", "Çelik", "Doç. Dr.", "Yazılım Mühendisliği", ofis_no="A-305")
        ogretim_uyesi_ekle(db, "Zeynep", "Arslan", "Dr. Öğr. Üyesi", "Elektrik-Elektronik Mühendisliği", ofis_no="B-201")
        ogretim_uyesi_ekle(db, "Mustafa", "Öztürk", "Prof. Dr.", "Makine Mühendisliği", ofis_no="B-202")
        
        # İktisadi ve İdari Bilimler Fakültesi
        print("\n--- İktisadi ve İdari Bilimler Fakültesi ---")
        ogretim_uyesi_ekle(db, "Can", "Yıldız", "Prof. Dr.", "İşletme", ofis_no="C-101")
        ogretim_uyesi_ekle(db, "Elif", "Aydın", "Doç. Dr.", "İşletme", ofis_no="C-102")
        ogretim_uyesi_ekle(db, "Burak", "Koç", "Dr. Öğr. Üyesi", "İktisat", ofis_no="C-103")
        ogretim_uyesi_ekle(db, "Selin", "Aksoy", "Prof. Dr.", "Uluslararası Ticaret", ofis_no="C-104")
        ogretim_uyesi_ekle(db, "Emre", "Yavuz", "Doç. Dr.", "Maliye", ofis_no="C-105")
        
        # Hukuk Fakültesi
        print("\n--- Hukuk Fakültesi ---")
        ogretim_uyesi_ekle(db, "Deniz", "Şen", "Prof. Dr.", "Hukuk", ofis_no="D-401")
        ogretim_uyesi_ekle(db, "Ece", "Kurt", "Doç. Dr.", "Hukuk", ofis_no="D-402")
        ogretim_uyesi_ekle(db, "Kerem", "Özdemir", "Dr. Öğr. Üyesi", "Hukuk", ofis_no="D-403")
        
        # Fen-Edebiyat Fakültesi
        print("\n--- Fen-Edebiyat Fakültesi ---")
        ogretim_uyesi_ekle(db, "Gül", "Tekin", "Prof. Dr.", "Matematik", ofis_no="E-301")
        ogretim_uyesi_ekle(db, "Serkan", "Polat", "Doç. Dr.", "Fizik", ofis_no="E-302")
        ogretim_uyesi_ekle(db, "Merve", "Kaplan", "Dr. Öğr. Üyesi", "Kimya", ofis_no="E-303")
        ogretim_uyesi_ekle(db, "Oğuz", "Yaman", "Prof. Dr.", "Türk Dili ve Edebiyatı", ofis_no="E-304")
        
        # Sanat ve Tasarım Fakültesi
        print("\n--- Sanat ve Tasarım Fakültesi ---")
        ogretim_uyesi_ekle(db, "Sibel", "Erdem", "Prof. Dr.", "Grafik Tasarım", ofis_no="F-201")
        ogretim_uyesi_ekle(db, "Cem", "Acar", "Doç. Dr.", "İç Mimarlık", ofis_no="F-202")
        ogretim_uyesi_ekle(db, "Pınar", "Güler", "Dr. Öğr. Üyesi", "Endüstri Ürünleri Tasarımı", ofis_no="F-203")
        
        # Sağlık Bilimleri Yüksekokulu
        print("\n--- Sağlık Bilimleri Yüksekokulu ---")
        ogretim_uyesi_ekle(db, "Hakan", "Uzun", "Prof. Dr.", "Hemşirelik", ofis_no="G-101")
        ogretim_uyesi_ekle(db, "Dilek", "Sarı", "Doç. Dr.", "Fizyoterapi ve Rehabilitasyon", ofis_no="G-102")
        ogretim_uyesi_ekle(db, "Tolga", "Bayram", "Dr. Öğr. Üyesi", "Beslenme ve Diyetetik", ofis_no="G-103")
        
        toplam = db.query(OgretimUyesi).count()
        print("\n" + "="*60)
        print(f"✓ Toplam {toplam} öğretim üyesi veritabanında")
        print("="*60 + "\n")
        
    except Exception as e:
        print(f"\n✗ Hata oluştu: {str(e)}")
        db.rollback()
    finally:
        db.close()

def web_scraping_dogus():
    """
    Gerçek web scraping (site yapısına göre özelleştirilmeli)
    Not: Bu fonksiyon örnek amaçlıdır, gerçek kullanım için site analizi gerekir
    """
    print("\n⚠️  Web scraping özelliği henüz aktif değil")
    print("Doğuş Üniversitesi web sitesinin yapısı analiz edilmeli")
    print("Şimdilik örnek veriler ekleniyor...\n")
    seed_dogus_ogretim_uyeleri()

if __name__ == "__main__":
    import sys
    
    if len(sys.argv) > 1 and sys.argv[1] == "--scrape":
        # Gelecekte web scraping eklenebilir
        web_scraping_dogus()
    else:
        # Örnek verilerle doldur
        seed_dogus_ogretim_uyeleri()

