from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from sqlalchemy import and_, func
from typing import List
from datetime import datetime, date, time, timedelta
import schemas
import models
from database import get_db

router = APIRouter()

@router.post("/kutuphane", response_model=schemas.Kutuphane, status_code=status.HTTP_201_CREATED)
def kutuphane_olustur(kutuphane: schemas.KutuphaneCreate, db: Session = Depends(get_db)):
    """Yeni kütüphane oluşturur"""
    db_kutuphane = models.Kutuphane(**kutuphane.model_dump())
    db.add(db_kutuphane)
    db.commit()
    db.refresh(db_kutuphane)
    return db_kutuphane


@router.get("/kutuphaneler", response_model=List[schemas.Kutuphane])
def kutuphaneleri_listele(
    skip: int = 0, 
    limit: int = 100,
    aktif_mi: bool = None,
    db: Session = Depends(get_db)
):
    """Tüm kütüphaneleri listeler"""
    query = db.query(models.Kutuphane)
    
    if aktif_mi is not None:
        query = query.filter(models.Kutuphane.aktif == aktif_mi)
    
    kutuphaneler = query.offset(skip).limit(limit).all()
    return kutuphaneler


@router.get("/kutuphane/{kutuphane_id}", response_model=schemas.Kutuphane)
def kutuphane_detay(kutuphane_id: int, db: Session = Depends(get_db)):
    """Belirli bir kütüphanenin detaylarını getirir"""
    kutuphane = db.query(models.Kutuphane).filter(models.Kutuphane.id == kutuphane_id).first()
    if not kutuphane:
        raise HTTPException(status_code=404, detail="Kütüphane bulunamadı")
    return kutuphane


@router.get("/kutuphane/{kutuphane_id}/musaitlik")
def kutuphane_musaitlik(
    kutuphane_id: int, 
    tarih: date,
    baslangic_saati: time = None,
    bitis_saati: time = None,
    db: Session = Depends(get_db)
):
    """
    Belirli bir tarih ve saat aralığında kütüphanenin müsaitlik durumunu döndürür.
    Eğer saat belirtilmezse, o tarihteki tüm rezervasyonları sayar.
    """
    kutuphane = db.query(models.Kutuphane).filter(models.Kutuphane.id == kutuphane_id).first()
    if not kutuphane:
        raise HTTPException(status_code=404, detail="Kütüphane bulunamadı")
    
    # Saat aralığı belirtildiyse, çakışan rezervasyonları bul
    if baslangic_saati and bitis_saati:
        # Seçilen saat aralığıyla çakışan rezervasyonlar
        # Çakışma koşulu: (rezervasyon_baslangic < seçilen_bitis) VE (rezervasyon_bitis > seçilen_baslangic)
        cakisan_rezervasyonlar = db.query(models.KutuphaneRezervasyonu).filter(
            and_(
                models.KutuphaneRezervasyonu.kutuphane_id == kutuphane_id,
                models.KutuphaneRezervasyonu.rezervasyon_tarihi == tarih,
                models.KutuphaneRezervasyonu.durum.in_(["beklemede", "onaylandi"]),
                models.KutuphaneRezervasyonu.baslangic_saati < bitis_saati,
                models.KutuphaneRezervasyonu.bitis_saati > baslangic_saati
            )
        ).count()
        
        rezervasyon_sayisi = cakisan_rezervasyonlar
    else:
        # Saat belirtilmediyse o tarihteki tüm rezervasyonları say
        rezervasyonlar = db.query(models.KutuphaneRezervasyonu).filter(
            and_(
                models.KutuphaneRezervasyonu.kutuphane_id == kutuphane_id,
                models.KutuphaneRezervasyonu.rezervasyon_tarihi == tarih,
                models.KutuphaneRezervasyonu.durum.in_(["beklemede", "onaylandi"])
            )
        ).count()
        
        rezervasyon_sayisi = rezervasyonlar
    
    return {
        "kutuphane_id": kutuphane_id,
        "kutuphane_adi": kutuphane.ad,
        "toplam_kapasite": kutuphane.toplam_kapasite,
        "tarih": tarih,
        "baslangic_saati": baslangic_saati,
        "bitis_saati": bitis_saati,
        "rezervasyonlar": rezervasyon_sayisi,
        "musait_kapasite": kutuphane.toplam_kapasite - rezervasyon_sayisi,
        "acilis_saati": kutuphane.acilis_saati,
        "kapanis_saati": kutuphane.kapanis_saati
    }


@router.post("/rezervasyon", response_model=schemas.KutuphaneRezervasyonu, status_code=status.HTTP_201_CREATED)
def rezervasyon_olustur(rezervasyon: schemas.KutuphaneRezervasyonuCreate, db: Session = Depends(get_db)):
    """Yeni kütüphane rezervasyonu oluşturur"""
    
    # Kütüphane var mı kontrol et
    kutuphane = db.query(models.Kutuphane).filter(models.Kutuphane.id == rezervasyon.kutuphane_id).first()
    if not kutuphane:
        raise HTTPException(status_code=404, detail="Kütüphane bulunamadı")
    
    # Kullanıcı var mı kontrol et
    kullanici = db.query(models.Kullanici).filter(models.Kullanici.id == rezervasyon.kullanici_id).first()
    if not kullanici:
        raise HTTPException(status_code=404, detail="Kullanıcı bulunamadı")
    
    # Aynı kullanıcının aynı tarih ve saatte başka rezervasyonu var mı kontrol et
    mevcut_rezervasyon = db.query(models.KutuphaneRezervasyonu).filter(
        and_(
            models.KutuphaneRezervasyonu.kullanici_id == rezervasyon.kullanici_id,
            models.KutuphaneRezervasyonu.rezervasyon_tarihi == rezervasyon.rezervasyon_tarihi,
            models.KutuphaneRezervasyonu.durum.in_(["beklemede", "onaylandi"]),
            # Saat çakışması kontrolü
            models.KutuphaneRezervasyonu.baslangic_saati < rezervasyon.bitis_saati,
            models.KutuphaneRezervasyonu.bitis_saati > rezervasyon.baslangic_saati
        )
    ).first()
    
    if mevcut_rezervasyon:
        raise HTTPException(
            status_code=400, 
            detail="Bu tarih ve saatte zaten bir rezervasyonunuz var"
        )
    
    # Kapasite kontrolü - O tarih ve saat aralığında kaç kişi rezervasyon yapmış
    ayni_saatteki_rezervasyonlar = db.query(models.KutuphaneRezervasyonu).filter(
        and_(
            models.KutuphaneRezervasyonu.kutuphane_id == rezervasyon.kutuphane_id,
            models.KutuphaneRezervasyonu.rezervasyon_tarihi == rezervasyon.rezervasyon_tarihi,
            models.KutuphaneRezervasyonu.durum.in_(["beklemede", "onaylandi"]),
            models.KutuphaneRezervasyonu.baslangic_saati < rezervasyon.bitis_saati,
            models.KutuphaneRezervasyonu.bitis_saati > rezervasyon.baslangic_saati
        )
    ).count()
    
    if ayni_saatteki_rezervasyonlar >= kutuphane.toplam_kapasite:
        raise HTTPException(
            status_code=400, 
            detail=f"Bu tarih ve saat aralığında kütüphane kapasitesi dolu ({kutuphane.toplam_kapasite} kişi)"
        )
    
    # Otomatik koltuk numarası ata
    # O saat aralığında kullanılan koltukları bul
    kullanilan_koltuklar = db.query(models.KutuphaneRezervasyonu.koltuk_no).filter(
        and_(
            models.KutuphaneRezervasyonu.kutuphane_id == rezervasyon.kutuphane_id,
            models.KutuphaneRezervasyonu.rezervasyon_tarihi == rezervasyon.rezervasyon_tarihi,
            models.KutuphaneRezervasyonu.durum.in_(["beklemede", "onaylandi"]),
            models.KutuphaneRezervasyonu.baslangic_saati < rezervasyon.bitis_saati,
            models.KutuphaneRezervasyonu.bitis_saati > rezervasyon.baslangic_saati,
            models.KutuphaneRezervasyonu.koltuk_no.isnot(None)
        )
    ).all()
    
    # Kullanılan koltuk numaralarını çıkar
    kullanilan_koltuk_numaralari = set()
    for (koltuk,) in kullanilan_koltuklar:
        if koltuk and koltuk.startswith('K-'):
            try:
                kullanilan_koltuk_numaralari.add(int(koltuk.split('-')[1]))
            except:
                pass
    
    # Boş koltuk numarasını bul (1'den başlayarak)
    koltuk_no = None
    for i in range(1, kutuphane.toplam_kapasite + 1):
        if i not in kullanilan_koltuk_numaralari:
            koltuk_no = f"K-{i}"
            break
    
    # Rezervasyon oluştur
    db_rezervasyon = models.KutuphaneRezervasyonu(
        **rezervasyon.model_dump(),
        koltuk_no=koltuk_no,
        durum="onaylandi"  # Otomatik onaylı
    )
    db.add(db_rezervasyon)
    db.commit()
    db.refresh(db_rezervasyon)
    return db_rezervasyon


@router.get("/rezervasyonlar", response_model=List[schemas.KutuphaneRezervasyonu])
def rezervasyonlari_listele(
    kutuphane_id: int = None,
    kullanici_id: int = None,
    tarih: date = None,
    durum: str = None,
    skip: int = 0,
    limit: int = 100,
    db: Session = Depends(get_db)
):
    """Kütüphane rezervasyonlarını listeler"""
    query = db.query(models.KutuphaneRezervasyonu)
    
    if kutuphane_id:
        query = query.filter(models.KutuphaneRezervasyonu.kutuphane_id == kutuphane_id)
    if kullanici_id:
        query = query.filter(models.KutuphaneRezervasyonu.kullanici_id == kullanici_id)
    if tarih:
        query = query.filter(models.KutuphaneRezervasyonu.rezervasyon_tarihi == tarih)
    if durum:
        query = query.filter(models.KutuphaneRezervasyonu.durum == durum)
    
    rezervasyonlar = query.order_by(
        models.KutuphaneRezervasyonu.rezervasyon_tarihi.desc(),
        models.KutuphaneRezervasyonu.baslangic_saati.desc()
    ).offset(skip).limit(limit).all()
    
    return rezervasyonlar


@router.get("/kullanici/{kullanici_id}/rezervasyonlar")
def kullanici_rezervasyonlari(kullanici_id: int, db: Session = Depends(get_db)):
    """Belirli bir kullanıcının tüm rezervasyonlarını getirir (kütüphane bilgisiyle birlikte)"""
    rezervasyonlar = db.query(models.KutuphaneRezervasyonu).filter(
        models.KutuphaneRezervasyonu.kullanici_id == kullanici_id
    ).order_by(
        models.KutuphaneRezervasyonu.rezervasyon_tarihi.desc(),
        models.KutuphaneRezervasyonu.baslangic_saati.desc()
    ).all()
    
    # Kütüphane bilgilerini ekle
    result = []
    for rez in rezervasyonlar:
        kutuphane = db.query(models.Kutuphane).filter(models.Kutuphane.id == rez.kutuphane_id).first()
        
        rez_dict = {
            "id": rez.id,
            "kutuphane_id": rez.kutuphane_id,
            "kullanici_id": rez.kullanici_id,
            "rezervasyon_tarihi": str(rez.rezervasyon_tarihi),
            "baslangic_saati": str(rez.baslangic_saati),
            "bitis_saati": str(rez.bitis_saati),
            "koltuk_no": rez.koltuk_no,
            "durum": rez.durum,
            "olusturma_tarihi": str(rez.olusturma_tarihi),
            "kutuphane_adi": kutuphane.ad if kutuphane else None
        }
        result.append(rez_dict)
    
    return result


@router.get("/rezervasyon/{rezervasyon_id}", response_model=schemas.KutuphaneRezervasyonu)
def rezervasyon_detay(rezervasyon_id: int, db: Session = Depends(get_db)):
    """Belirli bir rezervasyonun detaylarını getirir"""
    rezervasyon = db.query(models.KutuphaneRezervasyonu).filter(
        models.KutuphaneRezervasyonu.id == rezervasyon_id
    ).first()
    
    if not rezervasyon:
        raise HTTPException(status_code=404, detail="Rezervasyon bulunamadı")
    
    return rezervasyon


@router.put("/rezervasyon/{rezervasyon_id}/iptal")
def rezervasyon_iptal(rezervasyon_id: int, db: Session = Depends(get_db)):
    """Rezervasyonu iptal eder"""
    rezervasyon = db.query(models.KutuphaneRezervasyonu).filter(
        models.KutuphaneRezervasyonu.id == rezervasyon_id
    ).first()
    
    if not rezervasyon:
        raise HTTPException(status_code=404, detail="Rezervasyon bulunamadı")
    
    if rezervasyon.durum == "iptal_edildi":
        raise HTTPException(status_code=400, detail="Rezervasyon zaten iptal edilmiş")
    
    rezervasyon.durum = "iptal_edildi"
    db.commit()
    
    return {"message": "Rezervasyon başarıyla iptal edildi", "rezervasyon_id": rezervasyon_id}


@router.delete("/rezervasyon/{rezervasyon_id}")
def rezervasyon_sil(rezervasyon_id: int, db: Session = Depends(get_db)):
    """Rezervasyonu siler"""
    rezervasyon = db.query(models.KutuphaneRezervasyonu).filter(
        models.KutuphaneRezervasyonu.id == rezervasyon_id
    ).first()
    
    if not rezervasyon:
        raise HTTPException(status_code=404, detail="Rezervasyon bulunamadı")
    
    db.delete(rezervasyon)
    db.commit()
    
    return {"message": "Rezervasyon başarıyla silindi", "rezervasyon_id": rezervasyon_id}


@router.post("/doluluk", response_model=schemas.KutuphaneDoluluk)
def doluluk_guncelle(doluluk: schemas.KutuphaneDolulukCreate, db: Session = Depends(get_db)):
    """Kütüphane anlık doluluk bilgisini günceller"""
    
    # Mevcut kaydı bul
    mevcut_doluluk = db.query(models.KutuphaneDoluluk).filter(
        and_(
            models.KutuphaneDoluluk.kutuphane_id == doluluk.kutuphane_id,
            models.KutuphaneDoluluk.tarih == doluluk.tarih,
            models.KutuphaneDoluluk.saat_araligi == doluluk.saat_araligi
        )
    ).first()
    
    if mevcut_doluluk:
        # Güncelle
        mevcut_doluluk.dolu_koltuk_sayisi = doluluk.dolu_koltuk_sayisi
        mevcut_doluluk.guncelleme_tarihi = datetime.now()
        db.commit()
        db.refresh(mevcut_doluluk)
        return mevcut_doluluk
    else:
        # Yeni kayıt oluştur
        db_doluluk = models.KutuphaneDoluluk(**doluluk.model_dump())
        db.add(db_doluluk)
        db.commit()
        db.refresh(db_doluluk)
        return db_doluluk


@router.get("/kutuphane/{kutuphane_id}/doluluk-istatistik")
def doluluk_istatistikleri(
    kutuphane_id: int,
    baslangic_tarihi: date,
    bitis_tarihi: date,
    db: Session = Depends(get_db)
):
    """Belirli bir tarih aralığında kütüphanenin doluluk istatistiklerini döndürür"""
    
    kutuphane = db.query(models.Kutuphane).filter(models.Kutuphane.id == kutuphane_id).first()
    if not kutuphane:
        raise HTTPException(status_code=404, detail="Kütüphane bulunamadı")
    
    # Rezervasyon sayısı
    rezervasyon_sayisi = db.query(func.count(models.KutuphaneRezervasyonu.id)).filter(
        and_(
            models.KutuphaneRezervasyonu.kutuphane_id == kutuphane_id,
            models.KutuphaneRezervasyonu.rezervasyon_tarihi >= baslangic_tarihi,
            models.KutuphaneRezervasyonu.rezervasyon_tarihi <= bitis_tarihi,
            models.KutuphaneRezervasyonu.durum.in_(["beklemede", "onaylandi", "tamamlandi"])
        )
    ).scalar()
    
    # Ortalama doluluk
    ortalama_doluluk = db.query(func.avg(models.KutuphaneDoluluk.dolu_koltuk_sayisi)).filter(
        and_(
            models.KutuphaneDoluluk.kutuphane_id == kutuphane_id,
            models.KutuphaneDoluluk.tarih >= baslangic_tarihi,
            models.KutuphaneDoluluk.tarih <= bitis_tarihi
        )
    ).scalar()
    
    return {
        "kutuphane_id": kutuphane_id,
        "kutuphane_adi": kutuphane.ad,
        "toplam_kapasite": kutuphane.toplam_kapasite,
        "baslangic_tarihi": baslangic_tarihi,
        "bitis_tarihi": bitis_tarihi,
        "toplam_rezervasyon": rezervasyon_sayisi or 0,
        "ortalama_doluluk": round(float(ortalama_doluluk or 0), 2),
        "doluluk_yuzdesi": round((float(ortalama_doluluk or 0) / kutuphane.toplam_kapasite) * 100, 2) if kutuphane.toplam_kapasite > 0 else 0
    }

