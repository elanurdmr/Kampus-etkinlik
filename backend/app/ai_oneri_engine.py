# -*- coding: utf-8 -*-
"""
Yapay Zeka Tabanlı Etkinlik Öneri Motoru
Content-Based Filtering + Collaborative Filtering Hybrid Yaklaşım
"""
import numpy as np
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
from sklearn.preprocessing import MinMaxScaler
from typing import List, Dict, Tuple
from datetime import datetime, timedelta


class EtkinlikOneriMotoru:
    """
    Makine Öğrenmesi tabanlı etkinlik öneri sistemi
    
    Özellikler:
    1. Content-Based Filtering: İlgi alanları ve etkinlik özellikleri
    2. Collaborative Filtering: Kullanıcı davranışları
    3. Temporal Features: Zaman bazlı özellikler
    4. Popularity Score: Popülerlik skoru
    """
    
    def __init__(self):
        self.tfidf_vectorizer = TfidfVectorizer(max_features=100)
        self.scaler = MinMaxScaler()
        
    def _olustur_kullanici_profili(self, kullanici_ilgi_alanlari: List[int], 
                                     tum_ilgi_alanlari: List[int]) -> np.ndarray:
        """
        Kullanıcının ilgi alanı vektörünü oluştur (One-Hot Encoding benzeri)
        """
        profil = np.zeros(len(tum_ilgi_alanlari))
        for ilgi_alani_id in kullanici_ilgi_alanlari:
            if ilgi_alani_id in tum_ilgi_alanlari:
                idx = tum_ilgi_alanlari.index(ilgi_alani_id)
                profil[idx] = 1.0
        return profil
    
    def _olustur_etkinlik_profili(self, etkinlik_ilgi_alanlari: List[int],
                                    tum_ilgi_alanlari: List[int]) -> np.ndarray:
        """
        Etkinliğin ilgi alanı vektörünü oluştur
        """
        profil = np.zeros(len(tum_ilgi_alanlari))
        for ilgi_alani_id in etkinlik_ilgi_alanlari:
            if ilgi_alani_id in tum_ilgi_alanlari:
                idx = tum_ilgi_alanlari.index(ilgi_alani_id)
                profil[idx] = 1.0
        return profil
    
    def _hesapla_icerik_benzerligi(self, kullanici_profil: np.ndarray,
                                     etkinlik_profil: np.ndarray) -> float:
        """
        Cosine Similarity ile içerik benzerliği hesapla
        """
        if np.sum(kullanici_profil) == 0 or np.sum(etkinlik_profil) == 0:
            return 0.0
        
        similarity = cosine_similarity(
            kullanici_profil.reshape(1, -1),
            etkinlik_profil.reshape(1, -1)
        )[0][0]
        
        return float(similarity)
    
    def _hesapla_zaman_skoru(self, etkinlik_tarihi: datetime) -> float:
        """
        Etkinlik tarihine göre zaman skoru
        Yakın tarihli etkinlikler daha yüksek skor alır
        """
        simdiki_zaman = datetime.now()
        gun_farki = (etkinlik_tarihi - simdiki_zaman).days
        
        if gun_farki < 0:
            return 0.0  # Geçmiş etkinlik
        elif gun_farki <= 3:
            return 1.0  # Çok yakın (3 gün içinde)
        elif gun_farki <= 7:
            return 0.8  # Yakın (1 hafta içinde)
        elif gun_farki <= 14:
            return 0.6  # Orta (2 hafta içinde)
        elif gun_farki <= 30:
            return 0.4  # Uzak (1 ay içinde)
        else:
            return 0.2  # Çok uzak
    
    def _hesapla_populerlik_skoru(self, katilacak_sayisi: int,
                                    toplam_gorus_sayisi: int) -> float:
        """
        Popülerlik skoru: Katılacak sayısı / Toplam görüş sayısı
        """
        if toplam_gorus_sayisi == 0:
            return 0.5  # Nötr skor (yeni etkinlik)
        
        oran = katilacak_sayisi / toplam_gorus_sayisi
        return min(oran, 1.0)
    
    def _hesapla_coklu_ilgi_alani_bonusu(self, ortak_alan_sayisi: int) -> float:
        """
        Birden fazla ortak ilgi alanı varsa bonus skor
        """
        if ortak_alan_sayisi == 0:
            return 0.0
        elif ortak_alan_sayisi == 1:
            return 0.0
        elif ortak_alan_sayisi == 2:
            return 0.1
        else:
            return 0.2  # 3 veya daha fazla ortak alan
    
    def _kullanici_davranisi_skoru(self, gecmis_tercihler: List[Dict],
                                     etkinlik_ozellikleri: Dict) -> float:
        """
        Collaborative Filtering: Kullanıcının geçmiş tercihlerine göre skor
        
        - Benzer etkinliklere katıldıysa: yüksek skor
        - Benzer etkinlikleri reddetmiş: düşük skor
        """
        if not gecmis_tercihler:
            return 0.5  # Nötr (yeni kullanıcı)
        
        katilacak_sayisi = sum(1 for t in gecmis_tercihler if t['durum'] == 'katilacak')
        katilmayacak_sayisi = sum(1 for t in gecmis_tercihler if t['durum'] == 'katilmayacak')
        
        # Kullanıcının genel katılım eğilimi
        if katilacak_sayisi + katilmayacak_sayisi == 0:
            return 0.5
        
        katilim_orani = katilacak_sayisi / (katilacak_sayisi + katilmayacak_sayisi)
        
        # Benzer ilgi alanlarındaki etkinliklere katılım oranı
        # (Basitleştirilmiş - gerçekte daha detaylı analiz yapılabilir)
        
        return katilim_orani
    
    def hesapla_oneri_skoru(self, 
                             kullanici_ilgi_alanlari: List[int],
                             etkinlik_ilgi_alanlari: List[int],
                             kulup_ilgi_alani: int,
                             etkinlik_tarihi: datetime,
                             gecmis_tercihler: List[Dict],
                             tum_ilgi_alanlari: List[int],
                             etkinlik_populerligi: Dict = None) -> Tuple[float, Dict]:
        """
        Yapay Zeka ile etkinlik öneri skoru hesapla
        
        Returns:
            (skor, detaylar): 0-100 arası skor ve detaylı analiz
        """
        
        # 1. Content-Based Filtering: İçerik Benzerliği
        kullanici_profil = self._olustur_kullanici_profili(
            kullanici_ilgi_alanlari, tum_ilgi_alanlari
        )
        
        # Etkinlik ilgi alanları + kulüp ilgi alanı
        etkinlik_tum_ilgi_alanlari = etkinlik_ilgi_alanlari.copy()
        if kulup_ilgi_alani and kulup_ilgi_alani not in etkinlik_tum_ilgi_alanlari:
            etkinlik_tum_ilgi_alanlari.append(kulup_ilgi_alani)
        
        etkinlik_profil = self._olustur_etkinlik_profili(
            etkinlik_tum_ilgi_alanlari, tum_ilgi_alanlari
        )
        
        icerik_benzerligi = self._hesapla_icerik_benzerligi(
            kullanici_profil, etkinlik_profil
        )
        
        # 2. Temporal Features: Zaman Skoru
        zaman_skoru = self._hesapla_zaman_skoru(etkinlik_tarihi)
        
        # 3. Collaborative Filtering: Kullanıcı Davranışı
        davranis_skoru = self._kullanici_davranisi_skoru(
            gecmis_tercihler, {}
        )
        
        # 4. Popülerlik Skoru
        populerlik_skoru = 0.5
        if etkinlik_populerligi:
            populerlik_skoru = self._hesapla_populerlik_skoru(
                etkinlik_populerligi.get('katilacak', 0),
                etkinlik_populerligi.get('toplam', 0)
            )
        
        # 5. Çoklu İlgi Alanı Bonusu
        ortak_alanlar = set(kullanici_ilgi_alanlari) & set(etkinlik_tum_ilgi_alanlari)
        coklu_bonus = self._hesapla_coklu_ilgi_alani_bonusu(len(ortak_alanlar))
        
        # Ağırlıklı Skor Hesaplama (Hybrid Approach)
        AGIRLIKLAR = {
            'icerik': 0.40,      # %40 - En önemli faktör
            'zaman': 0.25,       # %25 - Yakınlık önemli
            'davranis': 0.20,    # %20 - Kullanıcı tercihleri
            'populerlik': 0.10,  # %10 - Diğerlerinin tercihi
            'bonus': 0.05        # %5 - Çoklu eşleşme bonusu
        }
        
        toplam_skor = (
            icerik_benzerligi * AGIRLIKLAR['icerik'] +
            zaman_skoru * AGIRLIKLAR['zaman'] +
            davranis_skoru * AGIRLIKLAR['davranis'] +
            populerlik_skoru * AGIRLIKLAR['populerlik'] +
            coklu_bonus * AGIRLIKLAR['bonus']
        )
        
        # 0-100 skalasına çevir
        final_skor = toplam_skor * 100
        
        # Detaylı analiz
        detaylar = {
            'icerik_benzerligi': round(icerik_benzerligi * 100, 2),
            'zaman_skoru': round(zaman_skoru * 100, 2),
            'davranis_skoru': round(davranis_skoru * 100, 2),
            'populerlik_skoru': round(populerlik_skoru * 100, 2),
            'bonus_skor': round(coklu_bonus * 100, 2),
            'ortak_ilgi_alan_sayisi': len(ortak_alanlar),
            'toplam_skor': round(final_skor, 2)
        }
        
        return final_skor, detaylar


# Global engine instance
oneri_motoru = EtkinlikOneriMotoru()


