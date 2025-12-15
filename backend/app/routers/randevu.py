from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from sqlalchemy import and_, or_
from typing import List, Optional
from datetime import datetime, date, time, timedelta
import schemas
import models
from database import get_db
from email_service import email_service

router = APIRouter()

# ========== ÖĞRETİM ÜYESİ İŞLEMLERİ ==========

@router.post("/ogretim-uyesi", response_model=schemas.OgretimUyesi, status_code=status.HTTP_201_CREATED)
def ogretim_uyesi_olustur(ogretim_uyesi: schemas.OgretimUyesiCreate, db: Session = Depends(get_db)):
    """Yeni öğretim üyesi oluşturur"""
    # Email kontrolü
    mevcut = db.query(models.OgretimUyesi).filter(models.OgretimUyesi.email == ogretim_uyesi.email).first()
    if mevcut:
        raise HTTPException(status_code=400, detail="Bu email adresi zaten kullanılıyor")
    
    db_ogretim_uyesi = models.OgretimUyesi(**ogretim_uyesi.model_dump())
    db.add(db_ogretim_uyesi)
    db.commit()
    db.refresh(db_ogretim_uyesi)
    return db_ogretim_uyesi


@router.get("/ogretim-uyeleri", response_model=List[schemas.OgretimUyesi])
def ogretim_uyelerini_listele(
    skip: int = 0,
    limit: int = 100,
    aktif_mi: bool = True,
    bolum: Optional[str] = None,
    db: Session = Depends(get_db)
):
    """Tüm öğretim üyelerini listeler"""
    query = db.query(models.OgretimUyesi)
    
    if aktif_mi:
        query = query.filter(models.OgretimUyesi.aktif == True)
    
    if bolum:
        query = query.filter(models.OgretimUyesi.bolum.ilike(f"%{bolum}%"))
    
    ogretim_uyeleri = query.offset(skip).limit(limit).all()
    return ogretim_uyeleri


@router.get("/ogretim-uyesi/{ogretim_uyesi_id}", response_model=schemas.OgretimUyesi)
def ogretim_uyesi_detay(ogretim_uyesi_id: int, db: Session = Depends(get_db)):
    """Belirli bir öğretim üyesinin detaylarını getirir"""
    ogretim_uyesi = db.query(models.OgretimUyesi).filter(models.OgretimUyesi.id == ogretim_uyesi_id).first()
    if not ogretim_uyesi:
        raise HTTPException(status_code=404, detail="Öğretim üyesi bulunamadı")
    return ogretim_uyesi


@router.get("/ogretim-uyesi/{ogretim_uyesi_id}/takvim")
def ogretim_uyesi_takvim(
    ogretim_uyesi_id: int,
    baslangic_tarihi: Optional[date] = None,
    bitis_tarihi: Optional[date] = None,
    db: Session = Depends(get_db)
):
    """Öğretim üyesinin randevu takvimini getirir"""
    ogretim_uyesi = db.query(models.OgretimUyesi).filter(models.OgretimUyesi.id == ogretim_uyesi_id).first()
    if not ogretim_uyesi:
        raise HTTPException(status_code=404, detail="Öğretim üyesi bulunamadı")
    
    query = db.query(models.Randevu).filter(models.Randevu.ogretim_uyesi_id == ogretim_uyesi_id)
    
    if baslangic_tarihi:
        query = query.filter(models.Randevu.randevu_tarihi >= baslangic_tarihi)
    if bitis_tarihi:
        query = query.filter(models.Randevu.randevu_tarihi <= bitis_tarihi)
    
    randevular = query.order_by(
        models.Randevu.randevu_tarihi.asc(),
        models.Randevu.randevu_saati.asc()
    ).all()
    
    # Öğrenci bilgilerini ekle
    result = []
    for randevu in randevular:
        ogrenci = db.query(models.Kullanici).filter(models.Kullanici.id == randevu.ogrenci_id).first()
        result.append({
            "id": randevu.id,
            "randevu_tarihi": str(randevu.randevu_tarihi),
            "randevu_saati": str(randevu.randevu_saati),
            "konu": randevu.konu,
            "aciklama": randevu.aciklama,
            "durum": randevu.durum,
            "ogrenci_adi": f"{ogrenci.ad} {ogrenci.soyad}" if ogrenci else "Bilinmiyor",
            "ogrenci_no": ogrenci.ogrenci_no if ogrenci else None,
            "ogrenci_email": ogrenci.email if ogrenci else None
        })
    
    return {
        "ogretim_uyesi_id": ogretim_uyesi_id,
        "ogretim_uyesi_adi": f"{ogretim_uyesi.ad} {ogretim_uyesi.soyad}",
        "randevular": result
    }


# ========== RANDEVU İŞLEMLERİ ==========

@router.post("/randevu", response_model=schemas.Randevu, status_code=status.HTTP_201_CREATED)
def randevu_olustur(randevu: schemas.RandevuCreate, db: Session = Depends(get_db)):
    """Yeni randevu oluşturur"""
    
    # Öğretim üyesi kontrolü
    ogretim_uyesi = db.query(models.OgretimUyesi).filter(models.OgretimUyesi.id == randevu.ogretim_uyesi_id).first()
    if not ogretim_uyesi:
        raise HTTPException(status_code=404, detail="Öğretim üyesi bulunamadı")
    
    if not ogretim_uyesi.aktif:
        raise HTTPException(status_code=400, detail="Bu öğretim üyesi aktif değil")
    
    # Öğrenci kontrolü
    ogrenci = db.query(models.Kullanici).filter(models.Kullanici.id == randevu.ogrenci_id).first()
    if not ogrenci:
        raise HTTPException(status_code=404, detail="Öğrenci bulunamadı")
    
    # Geçmiş tarih kontrolü
    if randevu.randevu_tarihi < date.today():
        raise HTTPException(status_code=400, detail="Geçmiş tarih için randevu oluşturulamaz")
    
    # Aynı öğrencinin aynı tarih ve saatte başka randevusu var mı kontrol et
    mevcut_randevu = db.query(models.Randevu).filter(
        and_(
            models.Randevu.ogrenci_id == randevu.ogrenci_id,
            models.Randevu.randevu_tarihi == randevu.randevu_tarihi,
            models.Randevu.randevu_saati == randevu.randevu_saati,
            models.Randevu.durum.in_(["bekliyor", "onaylandi"])
        )
    ).first()
    
    if mevcut_randevu:
        raise HTTPException(
            status_code=400,
            detail="Bu tarih ve saatte zaten bir randevunuz var"
        )
    
    # Öğretim üyesinin aynı tarih ve saatte başka randevusu var mı kontrol et
    ogretim_uyesi_randevu = db.query(models.Randevu).filter(
        and_(
            models.Randevu.ogretim_uyesi_id == randevu.ogretim_uyesi_id,
            models.Randevu.randevu_tarihi == randevu.randevu_tarihi,
            models.Randevu.randevu_saati == randevu.randevu_saati,
            models.Randevu.durum.in_(["bekliyor", "onaylandi"])
        )
    ).first()
    
    if ogretim_uyesi_randevu:
        raise HTTPException(
            status_code=400,
            detail="Bu öğretim üyesinin bu tarih ve saatte başka bir randevusu var"
        )
    
    # Randevu oluştur
    db_randevu = models.Randevu(**randevu.model_dump())
    db.add(db_randevu)
    db.commit()
    db.refresh(db_randevu)
    return db_randevu


@router.get("/randevular", response_model=List[schemas.Randevu])
def randevulari_listele(
    ogretim_uyesi_id: Optional[int] = None,
    ogrenci_id: Optional[int] = None,
    durum: Optional[str] = None,
    baslangic_tarihi: Optional[date] = None,
    bitis_tarihi: Optional[date] = None,
    skip: int = 0,
    limit: int = 100,
    db: Session = Depends(get_db)
):
    """Randevuları listeler"""
    query = db.query(models.Randevu)
    
    if ogretim_uyesi_id:
        query = query.filter(models.Randevu.ogretim_uyesi_id == ogretim_uyesi_id)
    if ogrenci_id:
        query = query.filter(models.Randevu.ogrenci_id == ogrenci_id)
    if durum:
        query = query.filter(models.Randevu.durum == durum)
    if baslangic_tarihi:
        query = query.filter(models.Randevu.randevu_tarihi >= baslangic_tarihi)
    if bitis_tarihi:
        query = query.filter(models.Randevu.randevu_tarihi <= bitis_tarihi)
    
    randevular = query.order_by(
        models.Randevu.randevu_tarihi.desc(),
        models.Randevu.randevu_saati.desc()
    ).offset(skip).limit(limit).all()
    
    return randevular


@router.get("/randevu/{randevu_id}", response_model=schemas.Randevu)
def randevu_detay(randevu_id: int, db: Session = Depends(get_db)):
    """Belirli bir randevunun detaylarını getirir"""
    randevu = db.query(models.Randevu).filter(models.Randevu.id == randevu_id).first()
    if not randevu:
        raise HTTPException(status_code=404, detail="Randevu bulunamadı")
    return randevu


@router.get("/ogrenci/{ogrenci_id}/randevular")
def ogrenci_randevulari(ogrenci_id: int, db: Session = Depends(get_db)):
    """Belirli bir öğrencinin tüm randevularını getirir (öğretim üyesi bilgisiyle birlikte)"""
    randevular = db.query(models.Randevu).filter(
        models.Randevu.ogrenci_id == ogrenci_id
    ).order_by(
        models.Randevu.randevu_tarihi.desc(),
        models.Randevu.randevu_saati.desc()
    ).all()
    
    # Öğretim üyesi bilgilerini ekle
    result = []
    for randevu in randevular:
        ogretim_uyesi = db.query(models.OgretimUyesi).filter(
            models.OgretimUyesi.id == randevu.ogretim_uyesi_id
        ).first()
        
        result.append({
            "id": randevu.id,
            "ogretim_uyesi_id": randevu.ogretim_uyesi_id,
            "ogretim_uyesi_adi": f"{ogretim_uyesi.ad} {ogretim_uyesi.soyad}" if ogretim_uyesi else "Bilinmiyor",
            "ogretim_uyesi_unvan": ogretim_uyesi.unvan if ogretim_uyesi else None,
            "ogretim_uyesi_bolum": ogretim_uyesi.bolum if ogretim_uyesi else None,
            "randevu_tarihi": str(randevu.randevu_tarihi),
            "randevu_saati": str(randevu.randevu_saati),
            "konu": randevu.konu,
            "aciklama": randevu.aciklama,
            "durum": randevu.durum,
            "olusturma_tarihi": str(randevu.olusturma_tarihi)
        })
    
    return result


@router.put("/randevu/{randevu_id}/durum")
def randevu_durum_guncelle(
    randevu_id: int,
    yeni_durum: str,
    db: Session = Depends(get_db)
):
    """Randevu durumunu günceller (onaylandi, reddedildi, tamamlandi, iptal_edildi)"""
    gecerli_durumlar = ["bekliyor", "onaylandi", "reddedildi", "tamamlandi", "iptal_edildi"]
    
    if yeni_durum not in gecerli_durumlar:
        raise HTTPException(
            status_code=400,
            detail=f"Geçersiz durum. Geçerli durumlar: {', '.join(gecerli_durumlar)}"
        )
    
    randevu = db.query(models.Randevu).filter(models.Randevu.id == randevu_id).first()
    if not randevu:
        raise HTTPException(status_code=404, detail="Randevu bulunamadı")
    
    # Öğrenci ve öğretim üyesi bilgilerini al (email için)
    ogrenci = db.query(models.Kullanici).filter(models.Kullanici.id == randevu.ogrenci_id).first()
    ogretim_uyesi = db.query(models.OgretimUyesi).filter(models.OgretimUyesi.id == randevu.ogretim_uyesi_id).first()
    
    randevu.durum = yeni_durum
    db.commit()
    
    # Onay veya red durumunda email gönder ve bildirim oluştur
    if yeni_durum in ["onaylandi", "reddedildi"] and ogrenci and ogretim_uyesi:
        try:
            # Email gönder
            email_service.randevu_onay_gonder(
                ogrenci_email=ogrenci.email,
                ogrenci_adi=f"{ogrenci.ad} {ogrenci.soyad}",
                ogretim_uyesi_adi=f"{ogretim_uyesi.unvan} {ogretim_uyesi.ad} {ogretim_uyesi.soyad}",
                randevu_tarihi=str(randevu.randevu_tarihi),
                randevu_saati=str(randevu.randevu_saati),
                konu=randevu.konu,
                durum=yeni_durum
            )
            
            # Bildirim oluştur
            bildirim_baslik = f"Randevunuz {yeni_durum}"
            bildirim_mesaj = f"{ogretim_uyesi.unvan} {ogretim_uyesi.ad} {ogretim_uyesi.soyad} ile {randevu.randevu_tarihi} tarihli randevunuz {yeni_durum}."
            
            bildirim = models.Bildirim(
                kullanici_id=ogrenci.id,
                baslik=bildirim_baslik,
                mesaj=bildirim_mesaj,
                tip=f"randevu_{yeni_durum}",
                ilgili_randevu_id=randevu.id
            )
            db.add(bildirim)
            db.commit()
            
        except Exception as e:
            print(f"Email/Bildirim hatası: {str(e)}")
    
    return {"message": "Randevu durumu başarıyla güncellendi", "randevu_id": randevu_id, "yeni_durum": yeni_durum}


@router.put("/randevu/{randevu_id}", response_model=schemas.Randevu)
def randevu_guncelle(
    randevu_id: int,
    randevu_guncelleme: schemas.RandevuUpdate,
    db: Session = Depends(get_db)
):
    """Randevu bilgilerini günceller"""
    randevu = db.query(models.Randevu).filter(models.Randevu.id == randevu_id).first()
    if not randevu:
        raise HTTPException(status_code=404, detail="Randevu bulunamadı")
    
    # Sadece onaylanmamış randevular güncellenebilir
    if randevu.durum in ["onaylandi", "tamamlandi"]:
        raise HTTPException(
            status_code=400,
            detail="Onaylanmış veya tamamlanmış randevular güncellenemez"
        )
    
    # Güncelleme verilerini uygula
    guncelleme_data = randevu_guncelleme.model_dump(exclude_unset=True)
    for field, value in guncelleme_data.items():
        setattr(randevu, field, value)
    
    db.commit()
    db.refresh(randevu)
    return randevu


@router.delete("/randevu/{randevu_id}")
def randevu_iptal(randevu_id: int, db: Session = Depends(get_db)):
    """Randevuyu iptal eder (silmez, durumunu değiştirir)"""
    randevu = db.query(models.Randevu).filter(models.Randevu.id == randevu_id).first()
    if not randevu:
        raise HTTPException(status_code=404, detail="Randevu bulunamadı")
    
    if randevu.durum == "iptal_edildi":
        raise HTTPException(status_code=400, detail="Randevu zaten iptal edilmiş")
    
    if randevu.durum == "tamamlandi":
        raise HTTPException(status_code=400, detail="Tamamlanmış randevular iptal edilemez")
    
    randevu.durum = "iptal_edildi"
    db.commit()
    
    return {"message": "Randevu başarıyla iptal edildi", "randevu_id": randevu_id}


@router.get("/randevu/{randevu_id}/hatirlatma-gonder")
def randevu_hatirlatma_gonder(randevu_id: int, db: Session = Depends(get_db)):
    """Randevu hatırlatması gönderir (manuel tetikleme)"""
    randevu = db.query(models.Randevu).filter(models.Randevu.id == randevu_id).first()
    if not randevu:
        raise HTTPException(status_code=404, detail="Randevu bulunamadı")
    
    # Randevu tarihi kontrolü (sadece gelecekteki randevular için)
    randevu_datetime = datetime.combine(randevu.randevu_tarihi, randevu.randevu_saati)
    if randevu_datetime < datetime.now():
        raise HTTPException(status_code=400, detail="Geçmiş randevular için hatırlatma gönderilemez")
    
    # Öğrenci ve öğretim üyesi bilgilerini al
    ogrenci = db.query(models.Kullanici).filter(models.Kullanici.id == randevu.ogrenci_id).first()
    ogretim_uyesi = db.query(models.OgretimUyesi).filter(models.OgretimUyesi.id == randevu.ogretim_uyesi_id).first()
    
    email_gonderildi = False
    
    # Email gönder
    if ogrenci and ogretim_uyesi:
        try:
            email_gonderildi = email_service.randevu_hatirlatma_gonder(
                ogrenci_email=ogrenci.email,
                ogrenci_adi=f"{ogrenci.ad} {ogrenci.soyad}",
                ogretim_uyesi_adi=f"{ogretim_uyesi.unvan} {ogretim_uyesi.ad} {ogretim_uyesi.soyad}",
                randevu_tarihi=randevu.randevu_tarihi.strftime("%d.%m.%Y"),
                randevu_saati=str(randevu.randevu_saati)[:-3],
                konu=randevu.konu
            )
        except Exception as e:
            print(f"Email gönderme hatası: {str(e)}")
    
    # Flag güncelle
    randevu.hatirlatma_gonderildi = True
    randevu.hatirlatma_tarihi = datetime.now()
    
    # Bildirim oluştur
    if ogrenci and ogretim_uyesi:
        try:
            bildirim = models.Bildirim(
                kullanici_id=ogrenci.id,
                baslik="Randevu Hatırlatması",
                mesaj=f"{ogretim_uyesi.unvan} {ogretim_uyesi.ad} {ogretim_uyesi.soyad} ile {randevu.randevu_tarihi} tarihinde saat {str(randevu.randevu_saati)[:-3]} randevunuz bulunmaktadır.",
                tip="randevu_hatirlatma",
                ilgili_randevu_id=randevu.id
            )
            db.add(bildirim)
        except Exception as e:
            print(f"Bildirim oluşturma hatası: {str(e)}")
    
    db.commit()
    
    return {
        "message": "Randevu hatırlatması gönderildi" if email_gonderildi else "Randevu kaydedildi (email gönderilemedi)",
        "email_gonderildi": email_gonderildi,
        "randevu_id": randevu_id,
        "randevu_tarihi": str(randevu.randevu_tarihi),
        "randevu_saati": str(randevu.randevu_saati)
    }


@router.get("/randevu-hatirlatmalari/gonder")
def yaklasan_randevu_hatirlatmalari(db: Session = Depends(get_db)):
    """
    Yaklaşan randevular için otomatik hatırlatma gönderir.
    Bu endpoint bir cron job veya scheduled task tarafından düzenli olarak çağrılabilir.
    """
    # 24 saat içindeki randevuları bul
    simdi = datetime.now()
    yarin = simdi + timedelta(days=1)
    
    yaklasan_randevular = db.query(models.Randevu).filter(
        and_(
            models.Randevu.randevu_tarihi >= simdi.date(),
            models.Randevu.randevu_tarihi <= yarin.date(),
            models.Randevu.durum.in_(["bekliyor", "onaylandi"]),
            models.Randevu.hatirlatma_gonderildi == False
        )
    ).all()
    
    gonderilen_hatirlatmalar = []
    email_basarili = 0
    email_basarisiz = 0
    
    for randevu in yaklasan_randevular:
        randevu_datetime = datetime.combine(randevu.randevu_tarihi, randevu.randevu_saati)
        saat_farki = (randevu_datetime - simdi).total_seconds() / 3600  # Saat cinsinden
        
        # 24 saat içindeki randevular için hatırlatma gönder
        if 0 <= saat_farki <= 24:
            # Öğrenci ve öğretim üyesi bilgilerini al
            ogrenci = db.query(models.Kullanici).filter(models.Kullanici.id == randevu.ogrenci_id).first()
            ogretim_uyesi = db.query(models.OgretimUyesi).filter(
                models.OgretimUyesi.id == randevu.ogretim_uyesi_id
            ).first()
            
            email_gonderildi = False
            
            # Email gönder
            if ogrenci and ogretim_uyesi:
                try:
                    email_gonderildi = email_service.randevu_hatirlatma_gonder(
                        ogrenci_email=ogrenci.email,
                        ogrenci_adi=f"{ogrenci.ad} {ogrenci.soyad}",
                        ogretim_uyesi_adi=f"{ogretim_uyesi.unvan} {ogretim_uyesi.ad} {ogretim_uyesi.soyad}",
                        randevu_tarihi=randevu.randevu_tarihi.strftime("%d.%m.%Y"),
                        randevu_saati=str(randevu.randevu_saati)[:-3],
                        konu=randevu.konu
                    )
                    
                    if email_gonderildi:
                        email_basarili += 1
                    else:
                        email_basarisiz += 1
                except Exception as e:
                    print(f"Email gönderme hatası: {str(e)}")
                    email_basarisiz += 1
            
            # Flag güncelle
            randevu.hatirlatma_gonderildi = True
            randevu.hatirlatma_tarihi = datetime.now()
            
            # Bildirim oluştur
            if ogrenci and ogretim_uyesi:
                try:
                    bildirim = models.Bildirim(
                        kullanici_id=ogrenci.id,
                        baslik="Randevu Hatırlatması",
                        mesaj=f"{ogretim_uyesi.unvan} {ogretim_uyesi.ad} {ogretim_uyesi.soyad} ile {randevu.randevu_tarihi} tarihinde saat {str(randevu.randevu_saati)[:-3]} randevunuz bulunmaktadır.",
                        tip="randevu_hatirlatma",
                        ilgili_randevu_id=randevu.id
                    )
                    db.add(bildirim)
                except Exception as e:
                    print(f"Bildirim oluşturma hatası: {str(e)}")
            
            db.commit()
            
            gonderilen_hatirlatmalar.append({
                "randevu_id": randevu.id,
                "ogrenci_id": randevu.ogrenci_id,
                "ogrenci_email": ogrenci.email if ogrenci else None,
                "ogretim_uyesi_id": randevu.ogretim_uyesi_id,
                "randevu_tarihi": str(randevu.randevu_tarihi),
                "randevu_saati": str(randevu.randevu_saati),
                "email_gonderildi": email_gonderildi
            })
    
    return {
        "message": f"{len(gonderilen_hatirlatmalar)} hatırlatma işlendi",
        "email_basarili": email_basarili,
        "email_basarisiz": email_basarisiz,
        "gonderilen_hatirlatmalar": gonderilen_hatirlatmalar
    }


