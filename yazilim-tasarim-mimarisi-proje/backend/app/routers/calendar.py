from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from typing import List
from datetime import datetime, timedelta
import schemas
import models
from database import get_db

router = APIRouter()

@router.post("/etkinlik", response_model=schemas.AkademikEtkinlik, status_code=status.HTTP_201_CREATED)
def etkinlik_olustur(etkinlik: schemas.AkademikEtkinlikCreate, db: Session = Depends(get_db)):
    """Yeni akademik etkinlik oluşturur (sınav, ödev, etkinlik vb.)"""
    db_etkinlik = models.AkademikEtkinlik(**etkinlik.model_dump())
    db.add(db_etkinlik)
    db.commit()
    db.refresh(db_etkinlik)
    return db_etkinlik

@router.get("/etkinlikler", response_model=List[schemas.AkademikEtkinlik])
def etkinlikleri_listele(
    skip: int = 0, 
    limit: int = 100, 
    etkinlik_turu: str = None,
    db: Session = Depends(get_db)
):
    """Tüm akademik etkinlikleri listeler"""
    query = db.query(models.AkademikEtkinlik).filter(models.AkademikEtkinlik.aktif == True)
    
    if etkinlik_turu:
        query = query.filter(models.AkademikEtkinlik.etkinlik_turu == etkinlik_turu)
    
    etkinlikler = query.order_by(models.AkademikEtkinlik.baslangic_tarihi).offset(skip).limit(limit).all()
    return etkinlikler

@router.get("/etkinlik/{etkinlik_id}", response_model=schemas.AkademikEtkinlik)
def etkinlik_detay(etkinlik_id: int, db: Session = Depends(get_db)):
    """Belirli bir etkinliğin detaylarını getirir"""
    etkinlik = db.query(models.AkademikEtkinlik).filter(models.AkademikEtkinlik.id == etkinlik_id).first()
    if not etkinlik:
        raise HTTPException(status_code=404, detail="Etkinlik bulunamadı")
    return etkinlik

@router.get("/yaklasan-etkinlikler")
def yaklasan_etkinlikler(gun_sayisi: int = 7, db: Session = Depends(get_db)):
    """Yaklaşan etkinlikleri ve geri sayımı döndürür"""
    simdi = datetime.now()
    gelecek = simdi + timedelta(days=gun_sayisi)
    
    etkinlikler = db.query(models.AkademikEtkinlik).filter(
        models.AkademikEtkinlik.aktif == True,
        models.AkademikEtkinlik.baslangic_tarihi >= simdi,
        models.AkademikEtkinlik.baslangic_tarihi <= gelecek
    ).order_by(models.AkademikEtkinlik.baslangic_tarihi).all()
    
    # Geri sayım hesaplama
    yaklasan_liste = []
    for etkinlik in etkinlikler:
        kalan_sure = etkinlik.baslangic_tarihi - simdi
        kalan_gun = kalan_sure.days
        kalan_saat = kalan_sure.seconds // 3600
        kalan_dakika = (kalan_sure.seconds % 3600) // 60
        
        yaklasan_liste.append({
            "id": etkinlik.id,
            "baslik": etkinlik.baslik,
            "etkinlik_turu": etkinlik.etkinlik_turu,
            "baslangic_tarihi": etkinlik.baslangic_tarihi,
            "konum": etkinlik.konum,
            "kalan_gun": kalan_gun,
            "kalan_saat": kalan_saat,
            "kalan_dakika": kalan_dakika,
            "popup_goster": kalan_gun <= 1  # 1 gün veya daha az kaldıysa popup göster
        })
    
    return {
        "toplam": len(yaklasan_liste),
        "etkinlikler": yaklasan_liste
    }

@router.put("/etkinlik/{etkinlik_id}", response_model=schemas.AkademikEtkinlik)
def etkinlik_guncelle(
    etkinlik_id: int, 
    etkinlik_guncelleme: schemas.AkademikEtkinlikCreate, 
    db: Session = Depends(get_db)
):
    """Etkinlik bilgilerini günceller"""
    db_etkinlik = db.query(models.AkademikEtkinlik).filter(models.AkademikEtkinlik.id == etkinlik_id).first()
    if not db_etkinlik:
        raise HTTPException(status_code=404, detail="Etkinlik bulunamadı")
    
    for key, value in etkinlik_guncelleme.model_dump().items():
        setattr(db_etkinlik, key, value)
    
    db_etkinlik.guncellenme_tarihi = datetime.now()
    db.commit()
    db.refresh(db_etkinlik)
    return db_etkinlik

@router.delete("/etkinlik/{etkinlik_id}", status_code=status.HTTP_204_NO_CONTENT)
def etkinlik_sil(etkinlik_id: int, db: Session = Depends(get_db)):
    """Etkinliği siler (soft delete)"""
    db_etkinlik = db.query(models.AkademikEtkinlik).filter(models.AkademikEtkinlik.id == etkinlik_id).first()
    if not db_etkinlik:
        raise HTTPException(status_code=404, detail="Etkinlik bulunamadı")
    
    db_etkinlik.aktif = False
    db.commit()
    return None

@router.post("/geri-sayim-ayarlari", response_model=schemas.GeriSayimAyarlari)
def geri_sayim_ayarla(ayarlar: schemas.GeriSayimAyarlariCreate, db: Session = Depends(get_db)):
    """Etkinlik için geri sayım ayarlarını oluşturur"""
    db_ayar = models.GeriSayimAyarlari(**ayarlar.model_dump())
    db.add(db_ayar)
    db.commit()
    db.refresh(db_ayar)
    return db_ayar

