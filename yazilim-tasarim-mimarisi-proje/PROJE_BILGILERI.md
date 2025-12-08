# Akademik Takvim ve QR Sistem - Proje Bilgileri

## 📌 Proje Özeti

Bu proje, üniversite öğrencileri için akademik takvim takibi ve QR kod ile etkinlik katılım sistemi sağlayan bir web uygulamasıdır.

## 🎯 Geliştirilen Fonksiyonlar

### 1. Akademik Takvim ve Geri Sayım Başlatma, Pop-up Açılması

**Alt Özellikler:**
- ✅ Akademik takvimin sisteme girilmesi (CRUD işlemleri)
- ✅ Geri sayım için kullanıcıların tarihlerle ilişkilendirilmesi
- ✅ Gerçek zamanlı geri sayım başlatma
- ✅ Pop-up tetikleme koşulu ve uygun kriterlerin tanımlanması
- ✅ Kullanıcı etkileşimi (otomatik ve manuel pop-up gösterimi)

**Teknik Detaylar:**
- Backend API'leri:
  - `POST /api/calendar/etkinlik` - Etkinlik oluşturma
  - `GET /api/calendar/etkinlikler` - Tüm etkinlikleri listeleme
  - `GET /api/calendar/yaklasan-etkinlikler` - Yaklaşan etkinlikler ve geri sayım
  - `PUT /api/calendar/etkinlik/{id}` - Etkinlik güncelleme
  - `DELETE /api/calendar/etkinlik/{id}` - Etkinlik silme

- Frontend Sayfaları:
  - `index.php` - Ana sayfa (yaklaşan etkinlikler)
  - `calendar.php` - Takvim görünümü
  - JavaScript ile dinamik geri sayım
  - Pop-up modal sistemi

**Geri Sayım Mantığı:**
```javascript
// Hedef tarihe kadar kalan süre hesaplanır
const diff = targetDate - now;
const days = Math.floor(diff / (1000 * 60 * 60 * 24));
const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

// 24 saat içindeki etkinlikler için pop-up gösterilir
if (days <= 1) {
    showPopup(event);
}
```

### 2. QR ile Onay Sistemi

**Alt Özellikler:**
- ✅ QR kod oluşturma (güvenli, benzersiz)
- ✅ Katılımcıların QR kod okutması
- ✅ Katılım kaydının oluşturulması
- ✅ Onay bildiriminin gelmesi

**Teknik Detaylar:**
- Backend API'leri:
  - `POST /api/qr/qr-kod-olustur` - QR kod oluşturma
  - `POST /api/qr/qr-dogrula` - QR kod doğrulama
  - `POST /api/qr/katilim-olustur` - Katılım kaydı oluşturma
  - `GET /api/qr/katilimlar/{id}` - Katılımcı listesi

- Frontend Sayfaları:
  - `qr-scan.php` - QR kod okutma sayfası
  - `admin.php` - QR kod oluşturma ve yönetim
  - Başarı modal'ı ile anında onay

**QR Kod Güvenliği:**
```python
# SHA-256 ile güvenli QR kod üretimi
timestamp = str(datetime.now().timestamp())
random_str = secrets.token_urlsafe(32)
data = f"{etkinlik_id}-{etkinlik_baslik}-{timestamp}-{random_str}"
qr_hash = hashlib.sha256(data.encode()).hexdigest()
```

## 🏗️ Mimari Yapı

### Backend (Python/FastAPI)
```
backend/
├── app/
│   ├── main.py              # Ana uygulama ve router'lar
│   ├── database.py          # Veritabanı bağlantısı
│   ├── models.py            # SQLAlchemy modelleri
│   ├── schemas.py           # Pydantic şemaları
│   ├── routers/
│   │   ├── calendar.py      # Akademik takvim endpoint'leri
│   │   └── qr_system.py     # QR sistem endpoint'leri
│   ├── create_tables.py     # Tablo oluşturma scripti
│   └── test_api.py          # API test scripti
```

### Frontend (PHP/JavaScript)
```
frontend/
├── index.php                # Ana sayfa
├── calendar.php             # Takvim sayfası
├── qr-scan.php              # QR okutma sayfası
├── admin.php                # Yönetim paneli
├── config.php               # Yapılandırma
├── css/
│   └── style.css            # Ana stil dosyası
└── js/
    ├── api.js               # API iletişim fonksiyonları
    ├── main.js              # Ana sayfa JavaScript
    ├── calendar.js          # Takvim JavaScript
    ├── qr-scan.js           # QR okuma JavaScript
    ├── popup.js             # Pop-up yönetimi
    └── admin.js             # Yönetim paneli JavaScript
```

### Veritabanı (MySQL)
```
database/
└── create_database.sql      # Veritabanı ve tablo oluşturma scripti
```

## 📊 Veritabanı Modeli

### Tablolar

1. **kullanicilar**
   - Öğrenci ve kullanıcı bilgileri
   - Alanlar: id, ad, soyad, email, ogrenci_no, rol

2. **akademik_etkinlikler**
   - Sınav, ödev ve etkinlik bilgileri
   - Alanlar: id, baslik, aciklama, etkinlik_turu, baslangic_tarihi, bitis_tarihi, konum

3. **qr_kodlar**
   - Etkinlik QR kodları
   - Alanlar: id, etkinlik_id, qr_kod, gecerlilik_suresi

4. **katilimlar**
   - Katılım kayıtları
   - Alanlar: id, kullanici_id, etkinlik_id, qr_kod_id, katilim_tarihi, onaylandi

5. **geri_sayim_ayarlari**
   - Geri sayım yapılandırmaları
   - Alanlar: id, etkinlik_id, geri_sayim_suresi, popup_goster

## 🔄 İş Akışları

### Etkinlik Oluşturma ve Geri Sayım
```
1. Admin etkinlik oluşturur (admin.php)
   ↓
2. Backend'e POST isteği gönderilir
   ↓
3. Veritabanına etkinlik kaydedilir
   ↓
4. Frontend'de etkinlik görüntülenir
   ↓
5. JavaScript geri sayımı başlatır
   ↓
6. 24 saat içinde pop-up gösterilir
```

### QR Kod ile Katılım
```
1. Admin QR kod oluşturur
   ↓
2. Backend benzersiz QR hash üretir
   ↓
3. Öğrenci QR kodu okutur/girer
   ↓
4. Backend QR'ı doğrular
   ↓
5. Katılım kaydı oluşturulur
   ↓
6. Başarı bildirimi gösterilir
```

## 🎨 Kullanıcı Arayüzü

### Renk Paleti
- Primary: #2563eb (Mavi)
- Secondary: #1e40af (Koyu Mavi)
- Success: #10b981 (Yeşil)
- Warning: #f59e0b (Turuncu)
- Danger: #ef4444 (Kırmızı)

### Responsive Tasarım
- Desktop: > 768px
- Tablet: 768px - 1024px
- Mobile: < 768px

### UI Bileşenleri
- Modal Pop-up'lar
- Toast Bildirimleri
- Geri Sayım Widget'ları
- Etkinlik Kartları
- Form Elemanları
- Tab Menüleri

## 🔒 Güvenlik Önlemleri

1. **SQL Injection Koruması**
   - SQLAlchemy ORM kullanımı
   - Parameterized queries

2. **XSS Koruması**
   - Input sanitization
   - Output encoding

3. **CORS Güvenliği**
   - Kontrollü CORS politikası
   - Sadece izin verilen originler

4. **QR Kod Güvenliği**
   - SHA-256 hash
   - Geçerlilik süresi kontrolü
   - Tek kullanımlık katılım

## 📈 Gelecek Geliştirmeler

Proje toplam 7 fonksiyon içerecek. İlk 2 fonksiyon tamamlandı.

**Planlanmış 5 Fonksiyon:**
1. ❌ Kullanıcı kimlik doğrulama ve oturum yönetimi
2. ❌ Email/SMS bildirimleri
3. ❌ Raporlama ve istatistikler
4. ❌ Dosya/materyal paylaşımı
5. ❌ Mobil uygulama entegrasyonu

## 📝 Test Senaryoları

### Manuel Test Adımları

1. **Etkinlik Oluşturma Testi**
   - Yönetim paneline git
   - Yeni etkinlik formu doldur
   - Kaydet ve listelenen etkinliği kontrol et

2. **Geri Sayım Testi**
   - Ana sayfayı aç
   - Yaklaşan etkinlik geri sayımını kontrol et
   - Pop-up'ın açıldığını doğrula (yakın tarihli etkinlik için)

3. **QR Kod Testi**
   - Yönetim panelinden QR kod oluştur
   - QR kodu kopyala
   - QR Okut sayfasından öğrenci seç
   - QR kodu gir ve doğrula
   - Başarı mesajını kontrol et

4. **API Testi**
   - `test_sistem.bat` çalıştır
   - Tüm endpoint'lerin çalıştığını doğrula

## 🛠️ Kullanılan Teknolojiler ve Versiyonlar

### Backend
- Python 3.14
- FastAPI 0.120.4
- SQLAlchemy 2.0.44
- PyMySQL 1.1.2
- Pydantic 2.12.3
- Uvicorn 0.38.0

### Frontend
- PHP 7.4+
- JavaScript (ES6+)
- HTML5
- CSS3

### Veritabanı
- MySQL 5.7+

### Araçlar
- VS Code
- MySQL Workbench
- Postman (API test için)
- Git

## 👥 Proje Ekibi

Yazılım Tasarımı ve Mimarisi Dersi Projesi

## 📅 Proje Zaman Çizelgesi

- ✅ Faz 1: Planlama ve Tasarım
- ✅ Faz 2: Veritabanı Tasarımı
- ✅ Faz 3: Backend Geliştirme
- ✅ Faz 4: Frontend Geliştirme
- ✅ Faz 5: Entegrasyon ve Test
- ⏳ Faz 6: Kalan 5 Fonksiyon (Gelecek)

## 📄 Lisans

Bu proje eğitim amaçlı geliştirilmiştir.

---

**Son Güncelleme:** 1 Kasım 2025

