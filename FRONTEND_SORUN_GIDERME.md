# Frontend Sorun Giderme Kılavuzu

## ✅ Yapılan Düzeltmeler

1. **Session Tutarsızlığı Düzeltildi**
   - `login.php` artık hem `$_SESSION['user_id']` hem de `$_SESSION['ogrenci_id']` set ediyor
   - Tüm dosyalarda `$_SESSION['user_id']` kontrolü tutarlı hale getirildi

2. **Gereksiz session_start() Kaldırıldı**
   - `login.php` ve `signup.php` dosyalarından gereksiz `session_start()` çağrıları kaldırıldı
   - `db.php` zaten `session_start()` içeriyor

3. **URL Yolları Düzeltildi**
   - `start_frontend.bat` ve `start_all.bat` dosyalarındaki yanlış URL'ler düzeltildi
   - Doğru URL: `http://localhost/yazilim-tasarim-mimarisi-proje/frontend/`

## 🔍 Frontend'i Test Etme

### 1. Test Sayfasını Kullan
Tarayıcıda şu adrese gidin:
```
http://localhost/yazilim-tasarim-mimarisi-proje/frontend/test.php
```

Bu sayfa şunları kontrol eder:
- ✅ PHP çalışıyor mu?
- ✅ Veritabanı bağlantısı başarılı mı?
- ✅ Session aktif mi?
- ✅ Backend API erişilebilir mi?

### 2. Ana Sayfayı Test Et
```
http://localhost/yazilim-tasarim-mimarisi-proje/frontend/index.php
```

### 3. Giriş Sayfasını Test Et
```
http://localhost/yazilim-tasarim-mimarisi-proje/frontend/login.php
```

## ⚠️ Olası Sorunlar ve Çözümleri

### Sorun 1: "404 Not Found" Hatası
**Neden:** XAMPP Apache çalışmıyor veya URL yolu yanlış

**Çözüm:**
1. XAMPP Control Panel'i açın
2. Apache servisini başlatın (Start butonuna tıklayın)
3. Doğru URL'yi kullanın: `http://localhost/yazilim-tasarim-mimarisi-proje/frontend/`

### Sorun 2: "Veritabanı Bağlantı Hatası"
**Neden:** MySQL çalışmıyor veya veritabanı yok

**Çözüm:**
1. XAMPP Control Panel'den MySQL'i başlatın
2. Veritabanının oluşturulduğundan emin olun:
   ```sql
   mysql -u root -p < database/create_database.sql
   ```

### Sorun 3: "Session Hatası" veya "Headers Already Sent"
**Neden:** `session_start()` birden fazla kez çağrılıyor

**Çözüm:**
- Bu sorun düzeltildi! `db.php` dosyası `session_start()` içeriyor ve diğer dosyalarda gereksiz çağrılar kaldırıldı.

### Sorun 4: "Backend API'ye Bağlanılamıyor"
**Neden:** Backend çalışmıyor

**Çözüm:**
1. Backend'i başlatın:
   ```bash
   cd backend/app
   ..\..\venv\Scripts\activate
   python main.py
   ```
2. Backend'in çalıştığını kontrol edin: `http://localhost:8000`

## 📋 Kontrol Listesi

Frontend'in çalışması için:

- [ ] XAMPP Apache çalışıyor mu? (Port 80)
- [ ] XAMPP MySQL çalışıyor mu? (Port 3306)
- [ ] Veritabanı oluşturuldu mu? (`akademik_sistem`)
- [ ] Backend çalışıyor mu? (Port 8000) - Sadece API sayfaları için gerekli
- [ ] Doğru URL kullanılıyor mu? (`http://localhost/yazilim-tasarim-mimarisi-proje/frontend/`)

## 🚀 Hızlı Başlatma

1. **XAMPP'i Başlat:**
   - XAMPP Control Panel'i aç
   - Apache'yi başlat
   - MySQL'i başlat

2. **Frontend'e Eriş:**
   ```
   http://localhost/yazilim-tasarim-mimarisi-proje/frontend/
   ```

3. **Test Sayfasını Kontrol Et:**
   ```
   http://localhost/yazilim-tasarim-mimarisi-proje/frontend/test.php
   ```

## 📞 Hala Çalışmıyorsa

1. Test sayfasını açın (`test.php`)
2. Hangi kontrollerin başarısız olduğunu görün
3. İlgili sorun giderme adımlarını uygulayın

**İyi Çalışmalar! 🎓**

