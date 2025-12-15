from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from database import get_db
from models import (
    IlgiAlani,
    KullaniciIlgiAlani,
    Kulup,
    KulupEtkinligi,
    KullaniciEtkinlikTercihi,
    Kullanici,
)
from pydantic import BaseModel
from typing import List, Optional
from datetime import datetime
from ai_oneri_engine import oneri_motoru

router = APIRouter(prefix="/api/oneri", tags=["Öneri Sistemi"])


# Pydantic modelleri
class IlgiAlaniCreate(BaseModel):
    alan_adi: str
    aciklama: Optional[str] = None


class KullaniciIlgiAlaniCreate(BaseModel):
    kullanici_id: int
    ilgi_alani_ids: List[int]


class KulupCreate(BaseModel):
    kulup_adi: str
    aciklama: Optional[str] = None
    ilgi_alani_id: Optional[int] = None


class KulupEtkinligiCreate(BaseModel):
    kulup_id: int
    etkinlik_adi: str
    aciklama: Optional[str] = None
    tarih: str  # ISO format
    konum: Optional[str] = None
    ilgi_alanlari: Optional[str] = None  # Virgülle ayrılmış ID'ler


class EtkinlikTercihCreate(BaseModel):
    kullanici_id: int
    etkinlik_id: int
    durum: str  # 'katilacak', 'katilmayacak', 'belki'


# İlgi alanları endpoints
@router.get("/ilgi-alanlari")
def get_ilgi_alanlari(db: Session = Depends(get_db)):
    """Tüm ilgi alanlarını listele"""
    ilgi_alanlari = db.query(IlgiAlani).filter(IlgiAlani.aktif == True).all()
    return {
        "success": True,
        "data": [
            {
                "id": alan.id,
                "alan_adi": alan.alan_adi,
                "aciklama": alan.aciklama
            }
            for alan in ilgi_alanlari
        ]
    }


@router.post("/ilgi-alani")
def create_ilgi_alani(ilgi_alani: IlgiAlaniCreate, db: Session = Depends(get_db)):
    """Yeni ilgi alanı oluştur (Admin)"""
    yeni_alan = IlgiAlani(
        alan_adi=ilgi_alani.alan_adi,
        aciklama=ilgi_alani.aciklama
    )
    db.add(yeni_alan)
    db.commit()
    db.refresh(yeni_alan)
    return {"success": True, "message": "İlgi alanı oluşturuldu", "id": yeni_alan.id}


@router.post("/kullanici-ilgi-alanlari")
def save_kullanici_ilgi_alanlari(data: KullaniciIlgiAlaniCreate, db: Session = Depends(get_db)):
    """Kullanıcının ilgi alanlarını kaydet"""
    # Önce mevcut ilgi alanlarını sil
    db.query(KullaniciIlgiAlani).filter(
        KullaniciIlgiAlani.kullanici_id == data.kullanici_id
    ).delete()
    
    # Yeni ilgi alanlarını ekle
    for ilgi_alani_id in data.ilgi_alani_ids:
        yeni_kayit = KullaniciIlgiAlani(
            kullanici_id=data.kullanici_id,
            ilgi_alani_id=ilgi_alani_id
        )
        db.add(yeni_kayit)
    
    db.commit()
    return {"success": True, "message": "İlgi alanları kaydedildi"}


@router.get("/kullanici-ilgi-alanlari/{kullanici_id}")
def get_kullanici_ilgi_alanlari(kullanici_id: int, db: Session = Depends(get_db)):
    """Kullanıcının ilgi alanlarını getir"""
    ilgi_alanlari = db.query(KullaniciIlgiAlani, IlgiAlani).join(
        IlgiAlani, KullaniciIlgiAlani.ilgi_alani_id == IlgiAlani.id
    ).filter(
        KullaniciIlgiAlani.kullanici_id == kullanici_id
    ).all()
    
    return {
        "success": True,
        "data": [
            {
                "id": alan.IlgiAlani.id,
                "alan_adi": alan.IlgiAlani.alan_adi
            }
            for alan in ilgi_alanlari
        ]
    }


# Kulüpler endpoints
@router.get("/kulupler")
def get_kulupler(db: Session = Depends(get_db)):
    """Tüm kulüpleri listele"""
    kulupler = db.query(Kulup).filter(Kulup.aktif == True).all()
    return {
        "success": True,
        "data": [
            {
                "id": kulup.id,
                "kulup_adi": kulup.kulup_adi,
                "aciklama": kulup.aciklama,
                "ilgi_alani_id": kulup.ilgi_alani_id
            }
            for kulup in kulupler
        ]
    }


@router.post("/kulup")
def create_kulup(kulup: KulupCreate, db: Session = Depends(get_db)):
    """Yeni kulüp oluştur"""
    yeni_kulup = Kulup(
        kulup_adi=kulup.kulup_adi,
        aciklama=kulup.aciklama,
        ilgi_alani_id=kulup.ilgi_alani_id
    )
    db.add(yeni_kulup)
    db.commit()
    db.refresh(yeni_kulup)
    return {"success": True, "message": "Kulüp oluşturuldu", "id": yeni_kulup.id}


# Kulüp etkinlikleri endpoints
@router.post("/kulup-etkinligi")
def create_kulup_etkinligi(etkinlik: KulupEtkinligiCreate, db: Session = Depends(get_db)):
    """Yeni kulüp etkinliği oluştur"""
    yeni_etkinlik = KulupEtkinligi(
        kulup_id=etkinlik.kulup_id,
        etkinlik_adi=etkinlik.etkinlik_adi,
        aciklama=etkinlik.aciklama,
        tarih=datetime.fromisoformat(etkinlik.tarih.replace('Z', '+00:00')),
        konum=etkinlik.konum,
        ilgi_alanlari=etkinlik.ilgi_alanlari
    )
    db.add(yeni_etkinlik)
    db.commit()
    db.refresh(yeni_etkinlik)
    return {"success": True, "message": "Etkinlik oluşturuldu", "id": yeni_etkinlik.id}


@router.get("/oneriler/{kullanici_id}")
def get_oneriler(kullanici_id: int, db: Session = Depends(get_db)):
    """🤖 Yapay Zeka ile Kullanıcıya Özel Etkinlik Önerileri"""
    
    # Kullanıcının ilgi alanlarını getir
    kullanici_ilgi_alanlari = db.query(KullaniciIlgiAlani).filter(
        KullaniciIlgiAlani.kullanici_id == kullanici_id
    ).all()
    
    if not kullanici_ilgi_alanlari:
        return {
            "success": True,
            "message": "Lütfen önce ilgi alanlarınızı seçin",
            "data": []
        }
    
    kullanici_ilgi_alan_ids = [ia.ilgi_alani_id for ia in kullanici_ilgi_alanlari]
    
    # Tüm ilgi alanlarını al (AI modeli için gerekli)
    tum_ilgi_alanlari_query = db.query(IlgiAlani).all()
    tum_ilgi_alan_ids = [ia.id for ia in tum_ilgi_alanlari_query]
    
    # Kullanıcının geçmiş tercihlerini getir (Collaborative Filtering için)
    gecmis_tercihler = db.query(KullaniciEtkinlikTercihi).filter(
        KullaniciEtkinlikTercihi.kullanici_id == kullanici_id
    ).all()
    
    gecmis_tercih_listesi = [
        {"durum": t.durum, "etkinlik_id": t.etkinlik_id}
        for t in gecmis_tercihler
    ]
    
    # Gelecekteki etkinlikleri getir
    simdiki_zaman = datetime.now()
    tum_etkinlikler = db.query(KulupEtkinligi, Kulup).join(
        Kulup, KulupEtkinligi.kulup_id == Kulup.id
    ).filter(
        KulupEtkinligi.aktif == True,
        KulupEtkinligi.tarih > simdiki_zaman
    ).all()
    
    # Önerilen etkinlikler
    oneriler = []
    
    for etkinlik, kulup in tum_etkinlikler:
        # Etkinlik ilgi alanlarını parse et
        etkinlik_ilgi_alan_ids = []
        if etkinlik.ilgi_alanlari:
            etkinlik_ilgi_alan_ids = [
                int(x) for x in etkinlik.ilgi_alanlari.split(',') if x.strip()
            ]
        
        # Etkinlik popülerliğini hesapla
        etkinlik_tercihleri = db.query(KullaniciEtkinlikTercihi).filter(
            KullaniciEtkinlikTercihi.etkinlik_id == etkinlik.id
        ).all()
        
        katilacak_sayisi = sum(1 for t in etkinlik_tercihleri if t.durum == 'katilacak')
        toplam_tercih = len(etkinlik_tercihleri)
        
        etkinlik_populerligi = {
            'katilacak': katilacak_sayisi,
            'toplam': toplam_tercih
        }
        
        # 🤖 YAPAY ZEKA MODELİ İLE SKOR HESAPLA
        ai_skor, detaylar = oneri_motoru.hesapla_oneri_skoru(
            kullanici_ilgi_alanlari=kullanici_ilgi_alan_ids,
            etkinlik_ilgi_alanlari=etkinlik_ilgi_alan_ids,
            kulup_ilgi_alani=kulup.ilgi_alani_id,
            etkinlik_tarihi=etkinlik.tarih,
            gecmis_tercihler=gecmis_tercih_listesi,
            tum_ilgi_alanlari=tum_ilgi_alan_ids,
            etkinlik_populerligi=etkinlik_populerligi
        )
        
        # Sadece yeterli skora sahip etkinlikleri öner (threshold: 20)
        if ai_skor >= 20:
            # Kullanıcının bu etkinlik için tercihini kontrol et
            tercih = db.query(KullaniciEtkinlikTercihi).filter(
                KullaniciEtkinlikTercihi.kullanici_id == kullanici_id,
                KullaniciEtkinlikTercihi.etkinlik_id == etkinlik.id
            ).first()
            
            tercih_durumu = tercih.durum if tercih else None
            
            oneriler.append({
                "id": etkinlik.id,
                "etkinlik_adi": etkinlik.etkinlik_adi,
                "aciklama": etkinlik.aciklama,
                "tarih": etkinlik.tarih.isoformat(),
                "konum": etkinlik.konum,
                "kulup": {
                    "id": kulup.id,
                    "kulup_adi": kulup.kulup_adi
                },
                "eslesme_skoru": round(ai_skor, 1),
                "tercih_durumu": tercih_durumu,
                "ai_analiz": detaylar  # Yapay zeka analiz detayları
            })
    
    # Yapay zeka skorlarına göre sırala (en yüksek önce)
    oneriler.sort(key=lambda x: x['eslesme_skoru'], reverse=True)
    
    return {
        "success": True,
        "data": oneriler,
        "ai_powered": True,
        "model": "Hybrid Content-Based + Collaborative Filtering"
    }


@router.post("/etkinlik-tercih")
def save_etkinlik_tercih(tercih: EtkinlikTercihCreate, db: Session = Depends(get_db)):
    """Kullanıcının etkinlik tercihini kaydet (katılacak/katılmayacak)"""
    
    # Mevcut tercihi kontrol et
    mevcut_tercih = db.query(KullaniciEtkinlikTercihi).filter(
        KullaniciEtkinlikTercihi.kullanici_id == tercih.kullanici_id,
        KullaniciEtkinlikTercihi.etkinlik_id == tercih.etkinlik_id
    ).first()
    
    if mevcut_tercih:
        # Güncelle
        mevcut_tercih.durum = tercih.durum
    else:
        # Yeni oluştur
        yeni_tercih = KullaniciEtkinlikTercihi(
            kullanici_id=tercih.kullanici_id,
            etkinlik_id=tercih.etkinlik_id,
            durum=tercih.durum
        )
        db.add(yeni_tercih)
    
    db.commit()
    return {"success": True, "message": f"Tercihiniz kaydedildi: {tercih.durum}"}


@router.get("/kullanici-tercihleri/{kullanici_id}")
def get_kullanici_tercihleri(kullanici_id: int, db: Session = Depends(get_db)):
    """Kullanıcının tüm etkinlik tercihlerini getir"""
    
    tercihler = db.query(KullaniciEtkinlikTercihi, KulupEtkinligi, Kulup).join(
        KulupEtkinligi, KullaniciEtkinlikTercihi.etkinlik_id == KulupEtkinligi.id
    ).join(
        Kulup, KulupEtkinligi.kulup_id == Kulup.id
    ).filter(
        KullaniciEtkinlikTercihi.kullanici_id == kullanici_id
    ).all()
    
    return {
        "success": True,
        "data": [
            {
                "etkinlik": {
                    "id": etkinlik.id,
                    "etkinlik_adi": etkinlik.etkinlik_adi,
                    "tarih": etkinlik.tarih.isoformat(),
                    "kulup_adi": kulup.kulup_adi
                },
                "durum": tercih.durum,
                "kayit_tarihi": tercih.olusturma_tarihi.isoformat()
            }
            for tercih, etkinlik, kulup in tercihler
        ]
    }


@router.get("/kullanici-yaklasan/{kullanici_id}")
def kullanici_yaklasan_etkinlikler(kullanici_id: int, db: Session = Depends(get_db)):
    """
    Kullanıcının 'katilacak' olarak işaretlediği en yakın kulüp etkinliklerini ve geri sayımı döner.
    Akademik takvimden bağımsız olarak sadece kulüp etkinlik tercihleri baz alınır.
    """
    simdi = datetime.now()

    # Kullanıcının katılacağı etkinlik tercihlerini getir (sadece gelecekteki etkinlikler)
    tercihler = (
        db.query(KullaniciEtkinlikTercihi, KulupEtkinligi, Kulup)
        .join(KulupEtkinligi, KullaniciEtkinlikTercihi.etkinlik_id == KulupEtkinligi.id)
        .join(Kulup, KulupEtkinligi.kulup_id == Kulup.id)
        .filter(
            KullaniciEtkinlikTercihi.kullanici_id == kullanici_id,
            KullaniciEtkinlikTercihi.durum == "katilacak",
            KulupEtkinligi.tarih >= simdi,
            KulupEtkinligi.aktif == True,  # noqa: E712
        )
        .order_by(KulupEtkinligi.tarih.asc())
        .all()
    )

    etkinlik_listesi = []
    for tercih, etkinlik, kulup in tercihler:
        kalan = etkinlik.tarih - simdi
        kalan_saniye = int(kalan.total_seconds())
        if kalan_saniye < 0:
            continue
        kalan_gun = kalan_saniye // 86400
        kalan_saat = (kalan_saniye % 86400) // 3600
        kalan_dakika = (kalan_saniye % 3600) // 60

        etkinlik_listesi.append(
            {
                "etkinlik_id": etkinlik.id,
                "etkinlik_adi": etkinlik.etkinlik_adi,
                "kulup_adi": kulup.kulup_adi,
                "tarih": etkinlik.tarih.isoformat(),
                "konum": etkinlik.konum,
                "kalan_gun": kalan_gun,
                "kalan_saat": kalan_saat,
                "kalan_dakika": kalan_dakika,
            }
        )

    return {
        "success": True,
        "toplam": len(etkinlik_listesi),
        "etkinlikler": etkinlik_listesi,
    }


