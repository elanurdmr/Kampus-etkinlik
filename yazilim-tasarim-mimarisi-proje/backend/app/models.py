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

