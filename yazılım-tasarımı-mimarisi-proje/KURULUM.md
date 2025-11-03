# Kurulum Rehberi

## Hızlı Başlangıç

### 1. MySQL Veritabanı Kurulumu

MySQL çalıştırın ve aşağıdaki komutu çalıştırın:

```bash
mysql -u root -p < database/create_database.sql
```

Veya MySQL Workbench kullanarak `database/create_database.sql` dosyasını çalıştırın.

### 2. Environment Dosyası Oluşturma

Backend klasöründe `.env` dosyası oluşturun (eğer yoksa):

```bash
cd backend
copy .env.example .env
```

`.env` dosyasını açın ve MySQL şifrenizi girin:
```
DATABASE_URL=mysql+pymysql://root:SIZIN_SIFRENIZ@localhost:3306/akademik_sistem
```

### 3. Virtual Environment'ı Aktif Edin

```bash
# Proje ana dizininde
venv\Scripts\activate
```

### 4. Backend'i Başlatın

```bash
cd backend\app
python main.py
```

Backend şu adreste çalışacak: http://localhost:8000

API dökümanı için: http://localhost:8000/docs

### 5. Frontend'i Başlatın

Yeni bir terminal açın:

```bash
cd frontend
php -S localhost:8080
```

Veya XAMPP/WAMP kullanıyorsanız, frontend klasörünü htdocs'a kopyalayın.

Frontend şu adreste çalışacak: http://localhost:8080

## Test Etme

### 1. API Test

Tarayıcıda şu adresi açın: http://localhost:8000

API çalışıyorsa şu mesajı görmelisiniz:
```json
{
  "message": "Akademik Takvim ve QR Sistem API'sine Hoş Geldiniz",
  "version": "1.0.0"
}
```

### 2. Örnek Veri Kontrolü

MySQL'de örnek verilerin yüklendiğini kontrol edin:

```sql
USE akademik_sistem;
SELECT * FROM akademik_etkinlikler;
SELECT * FROM kullanicilar;
```

### 3. Frontend Test

1. http://localhost:8080 adresine gidin
2. Ana sayfada yaklaşan etkinlikleri görmelisiniz
3. "Yönetim" sayfasından yeni etkinlik oluşturun
4. QR kod oluşturun
5. QR Okut sayfasından katılım yapın

## Sorun Giderme

### "No module named 'fastapi'" hatası
```bash
pip install -r requirements.txt
```

### MySQL bağlantı hatası
- MySQL servisinin çalıştığından emin olun
- .env dosyasındaki şifrenin doğru olduğunu kontrol edin
- akademik_sistem veritabanının oluşturulduğunu kontrol edin

### CORS hatası
- Backend'in çalıştığından emin olun
- main.py dosyasında CORS ayarlarının doğru olduğunu kontrol edin

### PHP bulunamadı hatası
PHP kurulu değilse indirin: https://www.php.net/downloads

Veya XAMPP kullanın: https://www.apachefriends.org/

## Önemli Notlar

- Backend her zaman 8000 portunda çalışmalı
- Frontend 8080 veya başka bir portta çalışabilir
- İlk çalıştırmada örnek veriler otomatik yüklenir
- QR kodlar SHA-256 ile şifrelenir

## Kullanım Senaryosu

1. **Yönetici:**
   - Yönetim paneline git
   - Yeni etkinlik oluştur
   - Etkinlik için QR kod üret

2. **Öğrenci:**
   - Ana sayfada yaklaşan etkinlikleri gör
   - Pop-up bildirimlerle hatırlat
   - QR Okut sayfasından katılım yap

3. **Sistem:**
   - Otomatik geri sayım
   - 24 saat içindeki etkinlikler için pop-up
   - Katılım kayıtları tutma

