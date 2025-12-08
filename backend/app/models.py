from sqlalchemy import Column, Integer, String, DateTime, Boolean, Text, ForeignKey
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

