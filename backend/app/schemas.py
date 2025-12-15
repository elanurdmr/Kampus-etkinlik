from pydantic import BaseModel, EmailStr
from datetime import datetime, date, time
from typing import Optional

# Akademik Etkinlik Şemaları
class AkademikEtkinlikBase(BaseModel):
    baslik: str
    aciklama: Optional[str] = None
    etkinlik_turu: str  # "sınav", "ödev", "etkinlik"
    baslangic_tarihi: datetime
    bitis_tarihi: Optional[datetime] = None
    konum: Optional[str] = None
    aktif: bool = True

class AkademikEtkinlikCreate(AkademikEtkinlikBase):
    pass

class AkademikEtkinlik(AkademikEtkinlikBase):
    id: int
    olusturma_tarihi: datetime
    guncellenme_tarihi: datetime
    
    class Config:
        from_attributes = True


# Kullanıcı Şemaları
class KullaniciBase(BaseModel):
    ad: str
    soyad: str
    email: EmailStr
    ogrenci_no: str
    rol: str = "ogrenci"

class KullaniciCreate(KullaniciBase):
    pass

class Kullanici(KullaniciBase):
    id: int
    olusturma_tarihi: datetime
    aktif: bool
    
    class Config:
        from_attributes = True


# QR Kod Şemaları
class QRKodBase(BaseModel):
    etkinlik_id: int

class QRKodCreate(QRKodBase):
    gecerlilik_suresi: Optional[datetime] = None

class QRKod(QRKodBase):
    id: int
    qr_kod: str
    olusturma_tarihi: datetime
    gecerlilik_suresi: Optional[datetime]
    aktif: bool
    
    class Config:
        from_attributes = True


# Katılım Şemaları
class KatilimBase(BaseModel):
    kullanici_id: int
    etkinlik_id: int
    qr_kod: str

class KatilimCreate(KatilimBase):
    pass

class Katilim(BaseModel):
    id: int
    kullanici_id: int
    etkinlik_id: int
    qr_kod_id: Optional[int]
    katilim_tarihi: datetime
    onaylandi: bool
    katilim_turu: str
    
    class Config:
        from_attributes = True


# Geri Sayım Ayarları
class GeriSayimAyarlariBase(BaseModel):
    etkinlik_id: int
    geri_sayim_suresi: int = 24
    popup_goster: bool = True
    bildirim_gonder: bool = True

class GeriSayimAyarlariCreate(GeriSayimAyarlariBase):
    pass

class GeriSayimAyarlari(GeriSayimAyarlariBase):
    id: int
    olusturma_tarihi: datetime
    
    class Config:
        from_attributes = True


# Kütüphane Şemaları
class KutuphaneBase(BaseModel):
    ad: str
    konum: str
    toplam_kapasite: int
    aciklama: Optional[str] = None
    acilis_saati: Optional[time] = None
    kapanis_saati: Optional[time] = None
    aktif: bool = True

class KutuphaneCreate(KutuphaneBase):
    pass

class Kutuphane(KutuphaneBase):
    id: int
    olusturma_tarihi: datetime
    
    class Config:
        from_attributes = True


# Kütüphane Rezervasyon Şemaları
class KutuphaneRezervasyonuBase(BaseModel):
    kutuphane_id: int
    kullanici_id: int
    rezervasyon_tarihi: date
    baslangic_saati: time
    bitis_saati: time
    notlar: Optional[str] = None

class KutuphaneRezervasyonuCreate(KutuphaneRezervasyonuBase):
    pass

class KutuphaneRezervasyonu(KutuphaneRezervasyonuBase):
    id: int
    koltuk_no: Optional[str] = None
    durum: str
    olusturma_tarihi: datetime
    guncellenme_tarihi: datetime
    
    class Config:
        from_attributes = True


# Kütüphane Doluluk Şemaları
class KutuphaneDolulukBase(BaseModel):
    kutuphane_id: int
    tarih: date
    saat_araligi: time
    dolu_koltuk_sayisi: int = 0

class KutuphaneDolulukCreate(KutuphaneDolulukBase):
    pass

class KutuphaneDoluluk(KutuphaneDolulukBase):
    id: int
    guncelleme_tarihi: datetime
    
    class Config:
        from_attributes = True


# Öğretim Üyesi Şemaları
class OgretimUyesiBase(BaseModel):
    ad: str
    soyad: str
    email: EmailStr
    unvan: Optional[str] = None
    bolum: Optional[str] = None
    ofis_no: Optional[str] = None
    telefon: Optional[str] = None
    calisma_saatleri: Optional[str] = None
    aktif: bool = True

class OgretimUyesiCreate(OgretimUyesiBase):
    pass

class OgretimUyesi(OgretimUyesiBase):
    id: int
    olusturma_tarihi: datetime
    
    class Config:
        from_attributes = True


# Randevu Şemaları
class RandevuBase(BaseModel):
    ogretim_uyesi_id: int
    ogrenci_id: int
    randevu_tarihi: date
    randevu_saati: time
    konu: str
    aciklama: Optional[str] = None

class RandevuCreate(RandevuBase):
    pass

class RandevuUpdate(BaseModel):
    durum: Optional[str] = None
    konu: Optional[str] = None
    aciklama: Optional[str] = None
    randevu_tarihi: Optional[date] = None
    randevu_saati: Optional[time] = None

class Randevu(RandevuBase):
    id: int
    durum: str
    hatirlatma_gonderildi: bool
    hatirlatma_tarihi: Optional[datetime] = None
    olusturma_tarihi: datetime
    guncellenme_tarihi: datetime
    
    class Config:
        from_attributes = True


# Bildirim Şemaları
class BildirimBase(BaseModel):
    kullanici_id: int
    baslik: str
    mesaj: str
    tip: str
    ilgili_randevu_id: Optional[int] = None

class BildirimCreate(BildirimBase):
    pass

class Bildirim(BildirimBase):
    id: int
    okundu: bool
    olusturma_tarihi: datetime
    okunma_tarihi: Optional[datetime] = None
    
    class Config:
        from_attributes = True

