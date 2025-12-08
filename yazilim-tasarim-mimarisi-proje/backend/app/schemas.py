from pydantic import BaseModel, EmailStr
from datetime import datetime
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

