"""
API test scripti - Backend'in çalıştığını test eder
"""
import requests
import json
from datetime import datetime, timedelta

API_BASE_URL = "http://localhost:8000/api"

def test_connection():
    """API bağlantısını test et"""
    try:
        response = requests.get("http://localhost:8000/")
        if response.status_code == 200:
            print("✓ Backend bağlantısı başarılı")
            print(f"  Yanıt: {response.json()}")
            return True
        else:
            print("✗ Backend bağlantı hatası")
            return False
    except Exception as e:
        print(f"✗ Bağlantı hatası: {e}")
        print("  Backend'in çalıştığından emin olun: python main.py")
        return False

def test_create_event():
    """Etkinlik oluşturmayı test et"""
    try:
        event_data = {
            "baslik": "Test Sınavı",
            "aciklama": "Bu bir test sınavıdır",
            "etkinlik_turu": "sınav",
            "baslangic_tarihi": (datetime.now() + timedelta(days=5)).isoformat(),
            "konum": "Test Salonu",
            "aktif": True
        }
        
        response = requests.post(
            f"{API_BASE_URL}/calendar/etkinlik",
            json=event_data
        )
        
        if response.status_code == 201:
            print("✓ Etkinlik oluşturma başarılı")
            data = response.json()
            print(f"  Etkinlik ID: {data['id']}")
            return data['id']
        else:
            print(f"✗ Etkinlik oluşturma hatası: {response.status_code}")
            print(f"  Hata: {response.text}")
            return None
    except Exception as e:
        print(f"✗ Etkinlik oluşturma hatası: {e}")
        return None

def test_get_events():
    """Etkinlikleri listelemeyi test et"""
    try:
        response = requests.get(f"{API_BASE_URL}/calendar/etkinlikler")
        
        if response.status_code == 200:
            events = response.json()
            print(f"✓ Etkinlik listeleme başarılı")
            print(f"  Toplam etkinlik: {len(events)}")
            return True
        else:
            print(f"✗ Etkinlik listeleme hatası: {response.status_code}")
            return False
    except Exception as e:
        print(f"✗ Etkinlik listeleme hatası: {e}")
        return False

def test_get_upcoming_events():
    """Yaklaşan etkinlikleri test et"""
    try:
        response = requests.get(f"{API_BASE_URL}/calendar/yaklasan-etkinlikler?gun_sayisi=7")
        
        if response.status_code == 200:
            data = response.json()
            print(f"✓ Yaklaşan etkinlikler başarılı")
            print(f"  Yaklaşan etkinlik sayısı: {data['toplam']}")
            return True
        else:
            print(f"✗ Yaklaşan etkinlikler hatası: {response.status_code}")
            return False
    except Exception as e:
        print(f"✗ Yaklaşan etkinlikler hatası: {e}")
        return False

def test_create_qr_code(event_id):
    """QR kod oluşturmayı test et"""
    if not event_id:
        print("⊘ QR kod testi atlandı (etkinlik ID yok)")
        return None
    
    try:
        qr_data = {
            "etkinlik_id": event_id
        }
        
        response = requests.post(
            f"{API_BASE_URL}/qr/qr-kod-olustur",
            json=qr_data
        )
        
        if response.status_code == 201:
            data = response.json()
            print("✓ QR kod oluşturma başarılı")
            print(f"  QR Kod: {data['qr_kod'][:20]}...")
            return data['qr_kod']
        else:
            print(f"✗ QR kod oluşturma hatası: {response.status_code}")
            return None
    except Exception as e:
        print(f"✗ QR kod oluşturma hatası: {e}")
        return None

def test_verify_qr_code(qr_code):
    """QR kod doğrulamayı test et"""
    if not qr_code:
        print("⊘ QR doğrulama testi atlandı (QR kod yok)")
        return
    
    try:
        response = requests.post(
            f"{API_BASE_URL}/qr/qr-dogrula",
            json={"qr_kod": qr_code}
        )
        
        if response.status_code == 200:
            data = response.json()
            print("✓ QR kod doğrulama başarılı")
            print(f"  Etkinlik: {data['etkinlik']['baslik']}")
            return True
        else:
            print(f"✗ QR kod doğrulama hatası: {response.status_code}")
            return False
    except Exception as e:
        print(f"✗ QR kod doğrulama hatası: {e}")
        return False

def run_all_tests():
    """Tüm testleri çalıştır"""
    print("=" * 50)
    print("API Test Başlatılıyor...")
    print("=" * 50)
    print()
    
    # Backend bağlantısı
    if not test_connection():
        print("\n❌ Backend çalışmıyor. Testler durduruldu.")
        return
    
    print()
    
    # Etkinlik testleri
    test_get_events()
    print()
    
    test_get_upcoming_events()
    print()
    
    event_id = test_create_event()
    print()
    
    # QR kod testleri
    qr_code = test_create_qr_code(event_id)
    print()
    
    test_verify_qr_code(qr_code)
    print()
    
    print("=" * 50)
    print("Test Tamamlandı!")
    print("=" * 50)

if __name__ == "__main__":
    run_all_tests()

