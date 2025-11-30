from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from typing import List
from datetime import datetime, timedelta
import secrets
import hashlib
import schemas
import models
from database import get_db

router = APIRouter()

def generate_qr_code(etkinlik_id: int, etkinlik_baslik: str) -> str:
    """QR kod için benzersiz token oluşturur"""
    timestamp = str(datetime.now().timestamp())
    random_str = secrets.token_urlsafe(32)
    data = f"{etkinlik_id}-{etkinlik_baslik}-{timestamp}-{random_str}"
    qr_hash = hashlib.sha256(data.encode()).hexdigest()
    return qr_hash

@router.post("/qr-kod-olustur", response_model=schemas.QRKod, status_code=status.HTTP_201_CREATED)
def qr_kod_olustur(qr_data: schemas.QRKodCreate, db: Session = Depends(get_db)):
    """Etkinlik için QR kod oluşturur"""
    # Etkinliği kontrol et
    etkinlik = db.query(models.AkademikEtkinlik).filter(
        models.AkademikEtkinlik.id == qr_data.etkinlik_id
    ).first()
    
    if not etkinlik:
        raise HTTPException(status_code=404, detail="Etkinlik bulunamadı")
    
    # QR kod oluştur
    qr_kod_str = generate_qr_code(etkinlik.id, etkinlik.baslik)
    
    # Geçerlilik süresi ayarla (varsayılan: etkinlik tarihine kadar)
    if not qr_data.gecerlilik_suresi:
        gecerlilik_suresi = etkinlik.baslangic_tarihi + timedelta(hours=2)
    else:
        gecerlilik_suresi = qr_data.gecerlilik_suresi
    
    db_qr = models.QRKod(
        etkinlik_id=qr_data.etkinlik_id,
        qr_kod=qr_kod_str,
        gecerlilik_suresi=gecerlilik_suresi
    )
    
    db.add(db_qr)
    db.commit()
    db.refresh(db_qr)
    
    return db_qr

@router.get("/qr-kod/{etkinlik_id}")
def etkinlik_qr_kodlari(etkinlik_id: int, db: Session = Depends(get_db)):
    """Etkinliğin QR kodlarını listeler"""
    qr_kodlar = db.query(models.QRKod).filter(
        models.QRKod.etkinlik_id == etkinlik_id,
        models.QRKod.aktif == True
    ).all()
    
    return {"toplam": len(qr_kodlar), "qr_kodlar": qr_kodlar}

@router.post("/qr-dogrula", status_code=status.HTTP_200_OK)
def qr_kod_dogrula(qr_kod: str, db: Session = Depends(get_db)):
    """QR kodun geçerliliğini kontrol eder"""
    db_qr = db.query(models.QRKod).filter(
        models.QRKod.qr_kod == qr_kod,
        models.QRKod.aktif == True
    ).first()
    
    if not db_qr:
        raise HTTPException(status_code=404, detail="QR kod bulunamadı veya geçersiz")
    
    # Geçerlilik süresini kontrol et
    if db_qr.gecerlilik_suresi and datetime.now() > db_qr.gecerlilik_suresi:
        raise HTTPException(status_code=400, detail="QR kodun geçerlilik süresi dolmuş")
    
    # Etkinlik bilgilerini getir
    etkinlik = db.query(models.AkademikEtkinlik).filter(
        models.AkademikEtkinlik.id == db_qr.etkinlik_id
    ).first()
    
    return {
        "gecerli": True,
        "qr_id": db_qr.id,
        "etkinlik": {
            "id": etkinlik.id,
            "baslik": etkinlik.baslik,
            "etkinlik_turu": etkinlik.etkinlik_turu,
            "baslangic_tarihi": etkinlik.baslangic_tarihi,
            "konum": etkinlik.konum
        },
        "mesaj": "QR kod geçerli"
    }

@router.post("/katilim-olustur", response_model=schemas.Katilim, status_code=status.HTTP_201_CREATED)
def katilim_olustur(katilim: schemas.KatilimCreate, db: Session = Depends(get_db)):
    """QR kod okutularak katılım kaydı oluşturur"""
    # QR kodu doğrula
    db_qr = db.query(models.QRKod).filter(
        models.QRKod.qr_kod == katilim.qr_kod,
        models.QRKod.aktif == True
    ).first()
    
    if not db_qr:
        raise HTTPException(status_code=404, detail="Geçersiz QR kod")
    
    # Geçerlilik kontrolü
    if db_qr.gecerlilik_suresi and datetime.now() > db_qr.gecerlilik_suresi:
        raise HTTPException(status_code=400, detail="QR kodun süresi dolmuş")
    
    # Kullanıcıyı kontrol et
    kullanici = db.query(models.Kullanici).filter(
        models.Kullanici.id == katilim.kullanici_id
    ).first()
    
    if not kullanici:
        raise HTTPException(status_code=404, detail="Kullanıcı bulunamadı")
    
    # Daha önce katılım var mı kontrol et
    mevcut_katilim = db.query(models.Katilim).filter(
        models.Katilim.kullanici_id == katilim.kullanici_id,
        models.Katilim.etkinlik_id == katilim.etkinlik_id
    ).first()
    
    if mevcut_katilim:
        raise HTTPException(status_code=400, detail="Bu etkinliğe zaten katılım kaydınız var")
    
    # Katılım kaydı oluştur
    db_katilim = models.Katilim(
        kullanici_id=katilim.kullanici_id,
        etkinlik_id=katilim.etkinlik_id,
        qr_kod_id=db_qr.id,
        onaylandi=True,
        katilim_turu="qr_kod"
    )
    
    db.add(db_katilim)
    db.commit()
    db.refresh(db_katilim)
    
    return db_katilim

@router.get("/katilimlar/{etkinlik_id}")
def etkinlik_katilimlari(etkinlik_id: int, db: Session = Depends(get_db)):
    """Etkinliğe katılanları listeler"""
    katilimlar = db.query(models.Katilim).filter(
        models.Katilim.etkinlik_id == etkinlik_id,
        models.Katilim.onaylandi == True
    ).all()
    
    katilim_listesi = []
    for katilim in katilimlar:
        kullanici = db.query(models.Kullanici).filter(
            models.Kullanici.id == katilim.kullanici_id
        ).first()
        
        katilim_listesi.append({
            "katilim_id": katilim.id,
            "kullanici_id": kullanici.id,
            "ad_soyad": f"{kullanici.ad} {kullanici.soyad}",
            "ogrenci_no": kullanici.ogrenci_no,
            "katilim_tarihi": katilim.katilim_tarihi,
            "katilim_turu": katilim.katilim_turu
        })
    
    return {
        "toplam_katilim": len(katilim_listesi),
        "katilimlar": katilim_listesi
    }

@router.post("/kullanici", response_model=schemas.Kullanici, status_code=status.HTTP_201_CREATED)
def kullanici_olustur(kullanici: schemas.KullaniciCreate, db: Session = Depends(get_db)):
    """Yeni kullanıcı oluşturur"""
    # Email kontrolü
    mevcut_kullanici = db.query(models.Kullanici).filter(
        models.Kullanici.email == kullanici.email
    ).first()
    
    if mevcut_kullanici:
        raise HTTPException(status_code=400, detail="Bu email ile kayıtlı kullanıcı var")
    
    db_kullanici = models.Kullanici(**kullanici.model_dump())
    db.add(db_kullanici)
    db.commit()
    db.refresh(db_kullanici)
    
    return db_kullanici
@router.post("/kayit-olustur")
def kayit_olustur(data: dict, db: Session = Depends(get_db)):
    """
    Frontend popup formdan gelen kaydı alır:
    - Ad
    - Soyad
    - Email
    - Telefon
    - Etkinlik Adı
    """

    # 1. Kullanıcı var mı?
    kullanici = db.query(models.Kullanici).filter(
        models.Kullanici.email == data["email"]
    ).first()

    if not kullanici:
        # Yeni kullanıcı oluştur
        kullanici = models.Kullanici(
            ad=data["first_name"],
            soyad=data["last_name"],
            email=data["email"],
            telefon=data["phone"],
            ogrenci_no="000000",
            sifre="1234"
        )
        db.add(kullanici)
        db.commit()
        db.refresh(kullanici)

    # 2. Etkinliği bul
    etkinlik = db.query(models.AkademikEtkinlik).filter(
        models.AkademikEtkinlik.baslik == data["event_name"]
    ).first()

    if not etkinlik:
        raise HTTPException(status_code=404, detail="Etkinlik bulunamadı")

    # 3. QR kod oluştur
    qr = models.QRKod(
        etkinlik_id=etkinlik.id,
        qr_kod=generate_qr_code(etkinlik.id, etkinlik.baslik),
        gecerlilik_suresi=etkinlik.baslangic_tarihi
    )
    db.add(qr)
    db.commit()
    db.refresh(qr)

    # 4. Katılım oluştur
    katilim = models.Katilim(
        kullanici_id=kullanici.id,
        etkinlik_id=etkinlik.id,
        qr_kod_id=qr.id,
        onaylandi=True,
        katilim_turu="form"
    )
    db.add(katilim)
    db.commit()

    return {
        "status": "ok",
        "mesaj": "Katılım başarıyla oluşturuldu!",
        "qr_kod": qr.qr_kod
    }

