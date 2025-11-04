# 🚀 Sistemi Başlatma Kılavuzu

## Hızlı Başlatma (3 Adım)

### 1️⃣ XAMPP'i Başlat
- XAMPP Control Panel'i aç
- ✅ **Apache** servisini başlat
- ✅ **MySQL** servisini başlat

### 2️⃣ Backend'i Başlat

**Mac/Linux:**
```bash
cd "/Applications/XAMPP/xamppfiles/htdocs/yazılım-tasarımı-mimarisi-proje/backend/app"
source ../../venv/bin/activate
python main.py
```

**Windows:**
```cmd
cd "C:\xampp\htdocs\yazılım-tasarımı-mimarisi-proje\backend\app"
..\..\venv\Scripts\activate
python main.py
```

Backend şu adreste çalışacak: **http://localhost:8000**

### 3️⃣ Frontend'e Eriş

Tarayıcınızda: **http://localhost/yazılım-tasarımı-mimarisi-proje/frontend/**

---

## 🌐 Sistem Adresleri

### Frontend Sayfaları
| Sayfa | URL | Açıklama |
|-------|-----|----------|
| Ana Sayfa | `/frontend/index.php` | Genel bilgiler |
| Etkinlikler | `/frontend/etkinlikler.php` | Statik etkinlikler |
| Eski Takvim | `/frontend/takvim.php` | MySQL'den takvim |
| **📅 Akademik Takvim** | `/frontend/akademik-takvim.php` | **Backend API ile dinamik** |
| **➕ Etkinlik Yönetimi** | `/frontend/etkinlik-yonetim.php` | **Yeni etkinlik ekle (API)** |

### Backend API
| Endpoint | URL | Açıklama |
|----------|-----|----------|
| Ana Sayfa | `http://localhost:8000/` | API bilgileri |
| Swagger Docs | `http://localhost:8000/docs` | İnteraktif API dökümantasyonu |
| Etkinlikler | `http://localhost:8000/api/calendar/etkinlikler` | Tüm etkinlikler |
| Yeni Etkinlik | `POST http://localhost:8000/api/calendar/etkinlik` | Etkinlik ekle |
| QR Sistem | `http://localhost:8000/api/qr` | QR kod sistemi |

---

## 🎯 İlk Kullanım Adımları

1. **Backend Kontrolü**
   - http://localhost:8000 adresine git
   - JSON yanıt görmelisin

2. **Etkinlik Ekle**
   - Frontend'de "Etkinlik Yönetimi" menüsüne tıkla
   - Formu doldur ve gönder
   
3. **Etkinlikleri Görüntüle**
   - "Akademik Takvim (API)" menüsüne tıkla
   - Eklediğin etkinlikleri gör

4. **API Dökümantasyonu**
   - http://localhost:8000/docs adresine git
   - "Try it out" ile API'yi test et

---

## 🔄 Sistem Yeniden Başlatma

Backend çalışmayı bıraktıysa:

```bash
# Terminalde Ctrl+C ile durdurun
# Tekrar başlatmak için:
cd backend/app
source ../../venv/bin/activate  # Mac/Linux
python main.py
```

---

## 🛑 Sistemi Durdurma

1. Backend terminalinde: `Ctrl + C`
2. XAMPP Control Panel'den Apache ve MySQL'i durdur

---

## ✅ Sistem Kontrol Listesi

- [ ] XAMPP Apache çalışıyor mu? (Port 80)
- [ ] XAMPP MySQL çalışıyor mu? (Port 3306)
- [ ] Backend çalışıyor mu? (Port 8000)
- [ ] Frontend açılıyor mu?
- [ ] API docs görüntüleniyor mu? (http://localhost:8000/docs)

---

## 🆘 Sorun Giderme

### Backend başlamıyor
```bash
# Port 8000'i temizle
lsof -ti :8000 | xargs kill -9
# Tekrar başlat
cd backend/app
source ../../venv/bin/activate
python main.py
```

### "ModuleNotFoundError"
```bash
cd /Applications/XAMPP/xamppfiles/htdocs/yazılım-tasarımı-mimarisi-proje
source venv/bin/activate
pip install -r requirements.txt
```

### MySQL bağlantı hatası
- XAMPP'de MySQL'in çalıştığını kontrol et
- `backend/app/.env` dosyasının var olduğunu kontrol et

---

## 📚 Ek Kaynaklar

- **Backend API Dökümantasyonu:** http://localhost:8000/docs
- **Proje Bilgileri:** PROJE_BILGILERI.md
- **Detaylı Kurulum:** KURULUM.md
- **Hızlı Başlangıç:** HIZLI_BASLANGIC.txt

---

## 💡 İpuçları

- Backend her zaman **8000** portunda çalışmalı
- Frontend XAMPP ile **80** portunda çalışır
- API değişiklikleri otomatik yüklenir (hot reload)
- Veritabanı değişiklikleri için Backend'i yeniden başlatın

**İyi Çalışmalar! 🎓**

