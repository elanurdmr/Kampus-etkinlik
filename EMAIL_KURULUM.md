# 📧 Email Sistemi Kurulum Rehberi

## Gmail SMTP ile Ücretsiz Email Gönderimi

### 1. Gmail Hesabı Hazırlığı

#### Adım 1: 2 Adımlı Doğrulama (2FA) Aktif Edin

1. Google Hesabınıza gidin: https://myaccount.google.com/
2. Sol menüden **Güvenlik** seçin
3. **2 Adımlı Doğrulama** bölümüne tıklayın
4. **Başla** butonuna tıklayıp talimatları izleyin

#### Adım 2: Uygulama Şifresi Oluşturun

1. Güvenlik sayfasında **Uygulama şifreleri**'ne tıklayın
2. Alt kısımda "Uygulama seç" dropdown'ından **Mail** seçin
3. "Cihaz seç" dropdown'ından **Diğer (Özel ad)** seçin
4. İsim girin: `Kampus Etkinlik Sistemi`
5. **Oluştur** butonuna tıklayın
6. Görünen 16 haneli şifreyi kopyalayın (örnek: `abcd efgh ijkl mnop`)

**ÖNEMLİ:** Bu şifre sadece bir kez gösterilir! Kaydettiğinizden emin olun.

### 2. Backend Yapılandırması

#### .env Dosyası Oluşturun

`backend/app/` klasöründe `.env` dosyası oluşturun:

```bash
cd backend/app
cp .env.example .env
```

`.env` dosyasını düzenleyin:

```env
# Veritabanı
DATABASE_URL=mysql+pymysql://root@127.0.0.1:3306/kampus-sistemi

# Email Ayarları
EMAIL_ADDRESS=sizin-email@gmail.com
EMAIL_PASSWORD=abcd efgh ijkl mnop
```

**Not:** `EMAIL_PASSWORD` yerine aldığınız 16 haneli uygulama şifresini yapıştırın (boşluksuz).

### 3. Backend'i Yeniden Başlatın

```bash
cd backend/app
source ../../venv/bin/activate  # macOS/Linux
python main.py
```

### 4. Test Edin

#### Manuel Test:
1. Randevu oluşturun
2. Öğretim üyesi takviminden onaylayın
3. Email geldi mi kontrol edin

#### API Test:
```bash
# Hatırlatma test et
curl http://localhost:8000/api/randevu/randevu-hatirlatmalari/gonder
```

## Email Türleri

### 1. Randevu Hatırlatma 📅
- **Ne zaman:** 24 saat önceden otomatik
- **Kime:** Öğrenci
- **İçerik:** Randevu detayları, tarih, saat, konu

### 2. Randevu Onayı ✅
- **Ne zaman:** Öğretim üyesi onayladığında
- **Kime:** Öğrenci
- **İçerik:** Onaylandı bildirimi + detaylar

### 3. Randevu Reddi ❌
- **Ne zaman:** Öğretim üyesi reddeddiğinde
- **Kime:** Öğrenci
- **İçerik:** Reddedildi bildirimi + detaylar

## Otomatik Hatırlatma Sistemi

### Seçenek 1: Cron Job (Linux/macOS)

```bash
# Her saat başı kontrol et
crontab -e
```

Şu satırı ekleyin:
```
0 * * * * curl http://localhost:8000/api/randevu/randevu-hatirlatmalari/gonder
```

### Seçenek 2: Python Script (Tüm işletim sistemleri)

`scheduled_tasks.py` oluşturun:

```python
import schedule
import time
import requests

def hatirlatma_kontrol():
    try:
        response = requests.get("http://localhost:8000/api/randevu/randevu-hatirlatmalari/gonder")
        print(f"Hatırlatma kontrolü: {response.json()}")
    except Exception as e:
        print(f"Hata: {e}")

# Her saat başı çalıştır
schedule.every().hour.do(hatirlatma_kontrol)

print("Hatırlatma servisi başlatıldı...")
while True:
    schedule.run_pending()
    time.sleep(60)
```

Schedule kütüphanesini yükleyin:
```bash
pip install schedule
```

Arka planda çalıştırın:
```bash
python scheduled_tasks.py &
```

### Seçenek 3: Windows Task Scheduler

1. **Görev Zamanlayıcı**'yı açın
2. **Temel Görev Oluştur**'a tıklayın
3. İsim: `Randevu Hatırlatma`
4. Tetikleyici: **Günlük** (her gün)
5. Başlangıç: `00:00`
6. Her `1` saatte tekrarla
7. Eylem: **Program başlat**
8. Program: `C:\Windows\System32\curl.exe`
9. Argümanlar: `http://localhost:8000/api/randevu/randevu-hatirlatmalari/gonder`

## Sorun Giderme

### 1. "SMTPAuthenticationError: Username and Password not accepted"

**Neden:** Gmail şifresi yanlış veya uygulama şifresi kullanılmamış.

**Çözüm:**
- 2FA aktif mi kontrol edin
- Uygulama şifresi oluşturdunuz mu?
- `.env` dosyasında doğru şifre var mı?

### 2. Email gelmiyor

**Kontrol listesi:**
- ✅ Backend çalışıyor mu?
- ✅ `.env` dosyası doğru mu?
- ✅ Email adresi geçerli mi?
- ✅ Spam klasörünü kontrol ettiniz mi?
- ✅ Backend loglarında hata var mı?

### 3. "Email servisi aktif değil" uyarısı

**Neden:** `.env` dosyasında `EMAIL_PASSWORD` boş.

**Çözüm:** `.env` dosyasını oluşturun ve Gmail uygulama şifresini ekleyin.

## Gmail Limitleri

- **Günlük limit:** 500 email
- **Dakikada:** ~100 email
- **Önerilir:** Büyük projeler için SendGrid kullanın

## Alternatif Email Servisleri

### SendGrid (Önerilen)
- ✅ Aylık 100 email ücretsiz
- ✅ Profesyonel
- ✅ Raporlama
- 💰 100+ email için ücretli

### Mailgun
- ✅ İlk 3 ay 5000 email ücretsiz
- 💰 Sonrası ücretli

### AWS SES
- 💰 1000 email = $0.10
- ⚙️ Daha karmaşık kurulum

## Test Email Adresleri

Geliştirme sırasında gerçek email göndermeden test etmek için:

### Mailtrap.io (Önerilen)
- Ücretsiz test email servisi
- Gerçek email göndermeye gerek yok
- Tüm emailler Mailtrap'te görünür

`.env` ayarı:
```env
EMAIL_ADDRESS=your-mailtrap-username
EMAIL_PASSWORD=your-mailtrap-password
```

`email_service.py` SMTP ayarı:
```python
self.smtp_server = "smtp.mailtrap.io"
self.smtp_port = 2525
```

---

**Sorular için:** Backend loglarını kontrol edin veya issue açın.
