from sqlalchemy import Column, Integer, String, DateTime, Boolean, Text, ForeignKey, Date, Time
from sqlalchemy.orm import relationship
from database import Base
from datetime import datetime

class AkademikEtkinlik(Base):
    """Akademik takvim etkinlikleri (sınav, ödev, etkinlik vb.)"""
    __tablename__ = "akademik_etkinlikler"
    
    id = Column(Integer, primary_key=True, index=True)
    baslik = Column(String(200), nullable=False)
    aciklama = Column(Text)
    etkinlik_turu = Column(String(50), nullable=False)  # sınav, ödev, etkinlik vb.
    baslangic_tarihi = Column(DateTime, nullable=False)
    bitis_tarihi = Column(DateTime)
    konum = Column(String(200))
    olusturma_tarihi = Column(DateTime, default=datetime.now)
    guncellenme_tarihi = Column(DateTime, default=datetime.now, onupdate=datetime.now)
    aktif = Column(Boolean, default=True)
    
    # QR kodları ile ilişki
    qr_kodlar = relationship("QRKod", back_populates="etkinlik")
    katilimlar = relationship("Katilim", back_populates="etkinlik")


class Kullanici(Base):
    """Öğrenci/Kullanıcı bilgileri"""
    __tablename__ = "kullanicilar"
    
    id = Column(Integer, primary_key=True, index=True)
    ad = Column(String(100), nullable=False)
    soyad = Column(String(100), nullable=False)
    email = Column(String(150), unique=True, nullable=False, index=True)
    ogrenci_no = Column(String(50), unique=True, index=True)
    rol = Column(String(50), default="ogrenci")  # ogrenci, ogretmen, admin
    olusturma_tarihi = Column(DateTime, default=datetime.now)
    aktif = Column(Boolean, default=True)
    
    # Katılımlar ile ilişki
    katilimlar = relationship("Katilim", back_populates="kullanici")


class QRKod(Base):
    """Etkinlik QR kodları"""
    __tablename__ = "qr_kodlar"
    
    id = Column(Integer, primary_key=True, index=True)
    etkinlik_id = Column(Integer, ForeignKey("akademik_etkinlikler.id"), nullable=False)
    qr_kod = Column(String(500), unique=True, nullable=False, index=True)
    olusturma_tarihi = Column(DateTime, default=datetime.now)
    gecerlilik_suresi = Column(DateTime)  # QR kod geçerlilik süresi
    aktif = Column(Boolean, default=True)
    
    # İlişkiler
    etkinlik = relationship("AkademikEtkinlik", back_populates="qr_kodlar")
    katilimlar = relationship("Katilim", back_populates="qr_kod")


class Katilim(Base):
    """Etkinlik katılım kayıtları"""
    __tablename__ = "katilimlar"
    
    id = Column(Integer, primary_key=True, index=True)
    kullanici_id = Column(Integer, ForeignKey("kullanicilar.id"), nullable=False)
    etkinlik_id = Column(Integer, ForeignKey("akademik_etkinlikler.id"), nullable=False)
    qr_kod_id = Column(Integer, ForeignKey("qr_kodlar.id"))
    katilim_tarihi = Column(DateTime, default=datetime.now)
    onaylandi = Column(Boolean, default=False)
    katilim_turu = Column(String(50), default="qr_kod")  # qr_kod, manuel vb.
    
    # İlişkiler
    kullanici = relationship("Kullanici", back_populates="katilimlar")
    etkinlik = relationship("AkademikEtkinlik", back_populates="katilimlar")
    qr_kod = relationship("QRKod", back_populates="katilimlar")


class GeriSayimAyarlari(Base):
    """Geri sayım ve pop-up ayarları"""
    __tablename__ = "geri_sayim_ayarlari"
    
    id = Column(Integer, primary_key=True, index=True)
    etkinlik_id = Column(Integer, ForeignKey("akademik_etkinlikler.id"), nullable=False)
    geri_sayim_suresi = Column(Integer, default=24)  # Saat cinsinden
    popup_goster = Column(Boolean, default=True)
    bildirim_gonder = Column(Boolean, default=True)
    olusturma_tarihi = Column(DateTime, default=datetime.now)


class IlgiAlani(Base):
    """İlgi alanları (Spor, Müzik, Teknoloji vb.)"""
    __tablename__ = "ilgi_alanlari"
    
    id = Column(Integer, primary_key=True, index=True)
    alan_adi = Column(String(100), unique=True, nullable=False)
    aciklama = Column(Text)
    olusturma_tarihi = Column(DateTime, default=datetime.now)
    aktif = Column(Boolean, default=True)


class KullaniciIlgiAlani(Base):
    """Kullanıcıların ilgi alanları (many-to-many)"""
    __tablename__ = "kullanici_ilgi_alanlari"
    
    id = Column(Integer, primary_key=True, index=True)
    kullanici_id = Column(Integer, ForeignKey("kullanicilar.id"), nullable=False)
    ilgi_alani_id = Column(Integer, ForeignKey("ilgi_alanlari.id"), nullable=False)
    olusturma_tarihi = Column(DateTime, default=datetime.now)


class Kulup(Base):
    """Kulüpler (Spor Kulübü, Müzik Kulübü vb.)"""
    __tablename__ = "kulupler"
    
    id = Column(Integer, primary_key=True, index=True)
    kulup_adi = Column(String(200), nullable=False)
    aciklama = Column(Text)
    ilgi_alani_id = Column(Integer, ForeignKey("ilgi_alanlari.id"))
    olusturma_tarihi = Column(DateTime, default=datetime.now)
    aktif = Column(Boolean, default=True)
    
    # İlişkiler
    etkinlikler = relationship("KulupEtkinligi", back_populates="kulup")


class KulupEtkinligi(Base):
    """Kulüp etkinlikleri"""
    __tablename__ = "kulup_etkinlikleri"
    
    id = Column(Integer, primary_key=True, index=True)
    kulup_id = Column(Integer, ForeignKey("kulupler.id"), nullable=False)
    etkinlik_adi = Column(String(200), nullable=False)
    aciklama = Column(Text)
    tarih = Column(DateTime, nullable=False)
    konum = Column(String(200))
    ilgi_alanlari = Column(String(500))  # Virgülle ayrılmış ilgi alanı ID'leri
    olusturma_tarihi = Column(DateTime, default=datetime.now)
    aktif = Column(Boolean, default=True)
    
    # İlişkiler
    kulup = relationship("Kulup", back_populates="etkinlikler")
    tercihler = relationship("KullaniciEtkinlikTercihi", back_populates="etkinlik")


class KullaniciEtkinlikTercihi(Base):
    """Kullanıcıların etkinlik tercihleri (katılacak/katılmayacak)"""
    __tablename__ = "kullanici_etkinlik_tercihleri"
    
    id = Column(Integer, primary_key=True, index=True)
    kullanici_id = Column(Integer, ForeignKey("kullanicilar.id"), nullable=False)
    etkinlik_id = Column(Integer, ForeignKey("kulup_etkinlikleri.id"), nullable=False)
    durum = Column(String(50), nullable=False)  # 'katilacak', 'katilmayacak', 'belki'
    olusturma_tarihi = Column(DateTime, default=datetime.now)
    
    # İlişkiler
    etkinlik = relationship("KulupEtkinligi", back_populates="tercihler")


class Kutuphane(Base):
    """Kütüphane bilgileri"""
    __tablename__ = "kutuphaneler"
    
    id = Column(Integer, primary_key=True, index=True)
    ad = Column(String(200), nullable=False)
    konum = Column(String(200), nullable=False)
    toplam_kapasite = Column(Integer, nullable=False)
    aciklama = Column(Text)
    acilis_saati = Column(Time, default="08:00:00")
    kapanis_saati = Column(Time, default="22:00:00")
    aktif = Column(Boolean, default=True)
    olusturma_tarihi = Column(DateTime, default=datetime.now)
    
    # İlişkiler
    rezervasyonlar = relationship("KutuphaneRezervasyonu", back_populates="kutuphane")
    doluluk_bilgileri = relationship("KutuphaneDoluluk", back_populates="kutuphane")


class KutuphaneRezervasyonu(Base):
    """Kütüphane rezervasyon kayıtları"""
    __tablename__ = "kutuphane_rezervasyonlar"
    
    id = Column(Integer, primary_key=True, index=True)
    kutuphane_id = Column(Integer, ForeignKey("kutuphaneler.id"), nullable=False)
    kullanici_id = Column(Integer, ForeignKey("kullanicilar.id"), nullable=False)
    rezervasyon_tarihi = Column(Date, nullable=False)
    baslangic_saati = Column(Time, nullable=False)
    bitis_saati = Column(Time, nullable=False)
    koltuk_no = Column(String(10))
    durum = Column(String(50), default="beklemede")  # beklemede, onaylandi, iptal_edildi, tamamlandi
    notlar = Column(Text)
    olusturma_tarihi = Column(DateTime, default=datetime.now)
    guncellenme_tarihi = Column(DateTime, default=datetime.now, onupdate=datetime.now)
    
    # İlişkiler
    kutuphane = relationship("Kutuphane", back_populates="rezervasyonlar")


class KutuphaneDoluluk(Base):
    """Kütüphane anlık doluluk bilgisi"""
    __tablename__ = "kutuphane_doluluk"
    
    id = Column(Integer, primary_key=True, index=True)
    kutuphane_id = Column(Integer, ForeignKey("kutuphaneler.id"), nullable=False)
    tarih = Column(Date, nullable=False)
    saat_araligi = Column(Time, nullable=False)
    dolu_koltuk_sayisi = Column(Integer, default=0)
    guncelleme_tarihi = Column(DateTime, default=datetime.now, onupdate=datetime.now)
    
    # İlişkiler
    kutuphane = relationship("Kutuphane", back_populates="doluluk_bilgileri")


class OgretimUyesi(Base):
    """Öğretim üyeleri bilgileri"""
    __tablename__ = "ogretim_uyeleri"
    
    id = Column(Integer, primary_key=True, index=True)
    ad = Column(String(100), nullable=False)
    soyad = Column(String(100), nullable=False)
    email = Column(String(150), unique=True, nullable=False, index=True)
    unvan = Column(String(100))  # Prof. Dr., Doç. Dr., Dr. Öğr. Üyesi vb.
    bolum = Column(String(200))
    ofis_no = Column(String(50))
    telefon = Column(String(20))
    calisma_saatleri = Column(Text)  # JSON formatında: {"pazartesi": "09:00-17:00", ...}
    aktif = Column(Boolean, default=True)
    olusturma_tarihi = Column(DateTime, default=datetime.now)
    
    # İlişkiler
    randevular = relationship("Randevu", back_populates="ogretim_uyesi")


class Randevu(Base):
    """Öğrenci-Öğretim üyesi randevu kayıtları"""
    __tablename__ = "randevular"
    
    id = Column(Integer, primary_key=True, index=True)
    ogretim_uyesi_id = Column(Integer, ForeignKey("ogretim_uyeleri.id"), nullable=False)
    ogrenci_id = Column(Integer, ForeignKey("kullanicilar.id"), nullable=False)
    randevu_tarihi = Column(Date, nullable=False)
    randevu_saati = Column(Time, nullable=False)
    konu = Column(String(200), nullable=False)
    aciklama = Column(Text)
    durum = Column(String(50), default="bekliyor")  # beklemede, onaylandi, reddedildi, tamamlandi, iptal_edildi
    hatirlatma_gonderildi = Column(Boolean, default=False)
    hatirlatma_tarihi = Column(DateTime)
    olusturma_tarihi = Column(DateTime, default=datetime.now)
    guncellenme_tarihi = Column(DateTime, default=datetime.now, onupdate=datetime.now)
    
    # İlişkiler
    ogretim_uyesi = relationship("OgretimUyesi", back_populates="randevular")


class Bildirim(Base):
    """Kullanıcı bildirimleri"""
    __tablename__ = "bildirimler"
    
    id = Column(Integer, primary_key=True, index=True)
    kullanici_id = Column(Integer, ForeignKey("kullanicilar.id"), nullable=False)
    baslik = Column(String(200), nullable=False)
    mesaj = Column(Text, nullable=False)
    tip = Column(String(50), nullable=False)  # randevu_onay, randevu_red, randevu_hatirlatma, sistem
    okundu = Column(Boolean, default=False)
    ilgili_randevu_id = Column(Integer, ForeignKey("randevular.id"), nullable=True)
    olusturma_tarihi = Column(DateTime, default=datetime.now)
    okunma_tarihi = Column(DateTime, nullable=True)


class KullaniciPuanDurumu(Base):
    """Etkinlik katılımlarına göre kullanıcı puan ve seviye bilgisi"""
    __tablename__ = "kullanici_puan_durumu"

    id = Column(Integer, primary_key=True, index=True)
    kullanici_id = Column(Integer, ForeignKey("kullanicilar.id"), unique=True, nullable=False)
    toplam_puan = Column(Integer, default=0)
    seviye = Column(Integer, default=1)
    toplam_katilim = Column(Integer, default=0)
    streak_gun = Column(Integer, default=0)
    son_katilim_tarihi = Column(Date, nullable=True)
    olusturma_tarihi = Column(DateTime, default=datetime.now)
    guncellenme_tarihi = Column(DateTime, default=datetime.now, onupdate=datetime.now)


class KatilimPuanLogu(Base):
    """Her katılım için puan hareketleri"""
    __tablename__ = "katilim_puan_loglari"

    id = Column(Integer, primary_key=True, index=True)
    kullanici_id = Column(Integer, ForeignKey("kullanicilar.id"), nullable=False)
    etkinlik_id = Column(Integer, ForeignKey("akademik_etkinlikler.id"), nullable=False)
    katilim_id = Column(Integer, ForeignKey("katilimlar.id"), nullable=False)
    puan = Column(Integer, nullable=False)
    aciklama = Column(String(255))
    tarih = Column(DateTime, default=datetime.now)


