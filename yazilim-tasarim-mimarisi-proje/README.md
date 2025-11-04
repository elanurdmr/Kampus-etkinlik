# Akademik Takvim ve QR Sistem

Üniversite öğrencileri için akademik takvim takibi ve QR kod ile etkinlik katılım sistemi.

## 🎯 Özellikler

### 1. Akademik Takvim ve Geri Sayım
- ✅ Akademik etkinliklerin (sınav, ödev, etkinlik) sisteme girilmesi
- ✅ Etkinlik tarihleri için otomatik geri sayım
- ✅ Yaklaşan etkinlikler için pop-up bildirimleri
- ✅ Kullanıcı dostu takvim görünümü
- ✅ Etkinlik filtreleme ve arama

### 2. QR Kod ile Onay Sistemi
- ✅ Etkinlikler için QR kod oluşturma
- ✅ QR kod okutarak katılım onayı
- ✅ Katılımcı takibi ve raporlama
- ✅ Geçerlilik süresi yönetimi
- ✅ Anlık onay bildirimleri

## 🛠️ Teknolojiler

- **Backend:** Python (FastAPI)
- **Frontend:** PHP, JavaScript
- **Veritabanı:** MySQL
- **Kütüphaneler:** SQLAlchemy, PyMySQL, uvicorn

## 📋 Gereksinimler

- Python 3.8+
- MySQL 5.7+
- PHP 7.4+
- Modern web tarayıcı

## 🚀 Kurulum

### 1. Projeyi İndirin
```bash
cd yazılım-tasarımı-mimarisi-proje
```

### 2. Virtual Environment Oluşturun (Zaten mevcut)
```bash
# Windows için
venv\Scripts\activate
```

### 3. Python Paketlerini Yükleyin (Zaten yüklü)
```bash
pip install -r requirements.txt
```

### 4. MySQL Veritabanını Oluşturun
```bash
mysql -u root -p < database/create_database.sql
```

Veya Python ile:
```bash
cd backend/app
python create_tables.py
```

### 5. Environment Değişkenlerini Ayarlayın
Backend klasöründe `.env` dosyası oluşturun:
```
DATABASE_URL=mysql+pymysql://root:your_password@localhost:3306/akademik_sistem
```

### 6. Backend'i Başlatın
```bash
cd backend/app
python main.py
```
Backend http://localhost:8000 adresinde çalışacaktır.

### 7. Frontend'i Çalıştırın
PHP built-in server kullanarak:
```bash
cd frontend
php -S localhost:8080
```

Veya XAMPP/WAMP gibi bir web server kullanabilirsiniz.

## 📱 Kullanım

### Ana Sayfa (index.php)
- Yaklaşan etkinlikleri görüntüleyin
- Geri sayım takibi yapın
- Pop-up bildirimler alın

### Takvim (calendar.php)
- Tüm akademik etkinlikleri görüntüleyin
- Etkinlik türüne göre filtreleyin
- Detaylı bilgilere erişin

### QR Okutma (qr-scan.php)
- Öğrenci seçin
- QR kodu okutun veya manuel girin
- Katılım onayı alın

### Yönetim Paneli (admin.php)
- Yeni etkinlik oluşturun
- Etkinlikler için QR kod üretin
- Katılımcı listelerini görüntüleyin

## 🔌 API Endpoints

### Akademik Takvim
```
POST   /api/calendar/etkinlik              - Yeni etkinlik oluştur
GET    /api/calendar/etkinlikler           - Tüm etkinlikleri listele
GET    /api/calendar/etkinlik/{id}         - Etkinlik detayı
GET    /api/calendar/yaklasan-etkinlikler  - Yaklaşan etkinlikler
PUT    /api/calendar/etkinlik/{id}         - Etkinlik güncelle
DELETE /api/calendar/etkinlik/{id}         - Etkinlik sil
```

### QR Sistem
```
POST   /api/qr/qr-kod-olustur    - QR kod oluştur
POST   /api/qr/qr-dogrula        - QR kod doğrula
POST   /api/qr/katilim-olustur   - Katılım kaydı oluştur
GET    /api/qr/katilimlar/{id}   - Etkinlik katılımlarını listele
POST   /api/qr/kullanici         - Yeni kullanıcı oluştur
```

## 📊 Veritabanı Şeması

- **kullanicilar:** Öğrenci/kullanıcı bilgileri
- **akademik_etkinlikler:** Etkinlik detayları
- **qr_kodlar:** QR kod bilgileri
- **katilimlar:** Katılım kayıtları
- **geri_sayim_ayarlari:** Geri sayım ayarları

## 🎨 Özellikler

### Geri Sayım Sistemi
- Gerçek zamanlı geri sayım
- Gün, saat, dakika gösterimi
- 24 saat içindeki etkinlikler için pop-up
- Otomatik bildirimler

### QR Kod Sistemi
- Benzersiz QR kod üretimi
- Geçerlilik süresi kontrolü
- Tekrarlı katılım engelleme
- Anlık onay mekanizması

## 🔒 Güvenlik

- SQL Injection koruması (SQLAlchemy ORM)
- CORS yapılandırması
- Input validasyonu
- Güvenli QR kod üretimi (SHA-256)

## 🐛 Sorun Giderme

### Backend başlatılamıyor
- Virtual environment aktif mi kontrol edin
- Tüm paketlerin yüklendiğinden emin olun
- MySQL servisinin çalıştığını kontrol edin

### Veritabanı bağlantı hatası
- MySQL kullanıcı adı ve şifresini kontrol edin
- `akademik_sistem` veritabanının oluşturulduğundan emin olun
- `.env` dosyasındaki DATABASE_URL'yi kontrol edin

### Frontend API'ye bağlanamıyor
- Backend'in çalıştığından emin olun (http://localhost:8000)
- CORS ayarlarını kontrol edin
- Browser console'da hata mesajlarını kontrol edin

## 📝 Lisans

Bu proje eğitim amaçlı geliştirilmiştir.

## 👥 Katkıda Bulunanlar

Yazılım Tasarımı ve Mimarisi Dersi Projesi

## 📧 İletişim

Sorularınız için proje ekibi ile iletişime geçebilirsiniz.

---

**Not:** Bu sistem 2 temel fonksiyon ile başlatılmıştır. Gelecekte 5 ek fonksiyon eklenecektir.

