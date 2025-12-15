from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from typing import List
from datetime import datetime
import schemas
import models
from database import get_db

router = APIRouter()

@router.get("/kullanici/{kullanici_id}/bildirimler", response_model=List[schemas.Bildirim])
def kullanici_bildirimleri(
    kullanici_id: int,
    okunmamis_mi: bool = None,
    skip: int = 0,
    limit: int = 100,
    db: Session = Depends(get_db)
):
    """Kullanıcının bildirimlerini getirir"""
    query = db.query(models.Bildirim).filter(models.Bildirim.kullanici_id == kullanici_id)
    
    if okunmamis_mi is not None:
        query = query.filter(models.Bildirim.okundu == (not okunmamis_mi))
    
    bildirimler = query.order_by(models.Bildirim.olusturma_tarihi.desc()).offset(skip).limit(limit).all()
    return bildirimler


@router.get("/kullanici/{kullanici_id}/bildirimler/okunmamis-sayisi")
def okunmamis_bildirim_sayisi(kullanici_id: int, db: Session = Depends(get_db)):
    """Kullanıcının okunmamış bildirim sayısını döner"""
    sayi = db.query(models.Bildirim).filter(
        models.Bildirim.kullanici_id == kullanici_id,
        models.Bildirim.okundu == False
    ).count()
    
    return {"okunmamis_sayisi": sayi}


@router.put("/bildirim/{bildirim_id}/okundu")
def bildirimi_okundu_isaretle(bildirim_id: int, db: Session = Depends(get_db)):
    """Bildirimi okundu olarak işaretler"""
    bildirim = db.query(models.Bildirim).filter(models.Bildirim.id == bildirim_id).first()
    
    if not bildirim:
        raise HTTPException(status_code=404, detail="Bildirim bulunamadı")
    
    bildirim.okundu = True
    bildirim.okunma_tarihi = datetime.now()
    db.commit()
    
    return {"message": "Bildirim okundu olarak işaretlendi"}


@router.put("/kullanici/{kullanici_id}/bildirimler/tumunu-okundu-isaretle")
def tum_bildirimleri_okundu_isaretle(kullanici_id: int, db: Session = Depends(get_db)):
    """Kullanıcının tüm bildirimlerini okundu olarak işaretler"""
    db.query(models.Bildirim).filter(
        models.Bildirim.kullanici_id == kullanici_id,
        models.Bildirim.okundu == False
    ).update({
        "okundu": True,
        "okunma_tarihi": datetime.now()
    })
    db.commit()
    
    return {"message": "Tüm bildirimler okundu olarak işaretlendi"}


@router.delete("/bildirim/{bildirim_id}")
def bildirimi_sil(bildirim_id: int, db: Session = Depends(get_db)):
    """Bildirimi siler"""
    bildirim = db.query(models.Bildirim).filter(models.Bildirim.id == bildirim_id).first()
    
    if not bildirim:
        raise HTTPException(status_code=404, detail="Bildirim bulunamadı")
    
    db.delete(bildirim)
    db.commit()
    
    return {"message": "Bildirim silindi"}


@router.post("/bildirim", response_model=schemas.Bildirim, status_code=status.HTTP_201_CREATED)
def bildirim_olustur(bildirim: schemas.BildirimCreate, db: Session = Depends(get_db)):
    """Yeni bildirim oluşturur (internal use)"""
    db_bildirim = models.Bildirim(**bildirim.model_dump())
    db.add(db_bildirim)
    db.commit()
    db.refresh(db_bildirim)
    return db_bildirim


