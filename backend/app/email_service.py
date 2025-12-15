"""
Email gönderim servisi - Gmail SMTP kullanarak
"""
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from datetime import datetime
import os
from dotenv import load_dotenv

load_dotenv()

class EmailService:
    def __init__(self):
        """
        Email servisi yapılandırması
        .env dosyasından email bilgilerini okur
        """
        self.smtp_server = "smtp.gmail.com"
        self.smtp_port = 587
        self.sender_email = os.getenv("EMAIL_ADDRESS", "kampus-sistem@gmail.com")
        self.sender_password = os.getenv("EMAIL_PASSWORD", "")
        self.enabled = bool(self.sender_password)  # Şifre varsa aktif
        
    def randevu_hatirlatma_gonder(self, ogrenci_email, ogrenci_adi, ogretim_uyesi_adi, 
                                   randevu_tarihi, randevu_saati, konu):
        """
        Randevu hatırlatma emaili gönder
        """
        if not self.enabled:
            print("⚠️  Email servisi aktif değil - .env dosyasına EMAIL_PASSWORD ekleyin")
            return False
        
        konu_email = f"🔔 Randevu Hatırlatması - {ogretim_uyesi_adi}"
        
        # HTML email içeriği
        mesaj_html = f"""
        <html>
          <body style="font-family: Arial, sans-serif; padding: 20px;">
            <div style="max-width: 600px; margin: 0 auto; border: 2px solid #b30000; border-radius: 10px; padding: 20px;">
              <h2 style="color: #b30000; text-align: center;">📅 Randevu Hatırlatması</h2>
              
              <p>Merhaba <strong>{ogrenci_adi}</strong>,</p>
              
              <p>Yaklaşan randevunuz hakkında size hatırlatma yapmak istiyoruz.</p>
              
              <div style="background-color: #fff5f5; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <h3 style="color: #b30000; margin-top: 0;">Randevu Detayları</h3>
                <p><strong>Öğretim Üyesi:</strong> {ogretim_uyesi_adi}</p>
                <p><strong>Tarih:</strong> {randevu_tarihi}</p>
                <p><strong>Saat:</strong> {randevu_saati}</p>
                <p><strong>Konu:</strong> {konu}</p>
              </div>
              
              <p style="color: #666;">Lütfen randevu saatinizden önce hazırlıklı olunuz.</p>
              
              <hr style="border: 1px solid #ddd; margin: 20px 0;">
              
              <p style="font-size: 12px; color: #999; text-align: center;">
                Kampüs Etkinlik Takip Sistemi<br>
                Bu otomatik bir bildirimdir, lütfen yanıtlamayınız.
              </p>
            </div>
          </body>
        </html>
        """
        
        return self._email_gonder(ogrenci_email, konu_email, mesaj_html)
    
    def randevu_onay_gonder(self, ogrenci_email, ogrenci_adi, ogretim_uyesi_adi,
                            randevu_tarihi, randevu_saati, konu, durum):
        """
        Randevu onay/red emaili gönder
        """
        if not self.enabled:
            print("⚠️  Email servisi aktif değil")
            return False
        
        if durum == "onaylandi":
            baslik = "✅ Randevunuz Onaylandı"
            durum_mesaj = "randevunuz onaylanmıştır."
            renk = "#28a745"
        elif durum == "reddedildi":
            baslik = "❌ Randevunuz Reddedildi"
            durum_mesaj = "randevunuz reddedilmiştir."
            renk = "#dc3545"
        else:
            return False
        
        konu_email = f"{baslik} - {ogretim_uyesi_adi}"
        
        mesaj_html = f"""
        <html>
          <body style="font-family: Arial, sans-serif; padding: 20px;">
            <div style="max-width: 600px; margin: 0 auto; border: 2px solid {renk}; border-radius: 10px; padding: 20px;">
              <h2 style="color: {renk}; text-align: center;">{baslik}</h2>
              
              <p>Merhaba <strong>{ogrenci_adi}</strong>,</p>
              
              <p>{ogretim_uyesi_adi} ile oluşturduğunuz {durum_mesaj}</p>
              
              <div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <h3 style="color: {renk}; margin-top: 0;">Randevu Detayları</h3>
                <p><strong>Öğretim Üyesi:</strong> {ogretim_uyesi_adi}</p>
                <p><strong>Tarih:</strong> {randevu_tarihi}</p>
                <p><strong>Saat:</strong> {randevu_saati}</p>
                <p><strong>Konu:</strong> {konu}</p>
              </div>
              
              <hr style="border: 1px solid #ddd; margin: 20px 0;">
              
              <p style="font-size: 12px; color: #999; text-align: center;">
                Kampüs Etkinlik Takip Sistemi<br>
                Bu otomatik bir bildirimdir, lütfen yanıtlamayınız.
              </p>
            </div>
          </body>
        </html>
        """
        
        return self._email_gonder(ogrenci_email, konu_email, mesaj_html)
    
    def _email_gonder(self, alici_email, konu, mesaj_html):
        """
        Email gönderme işlemi (internal)
        """
        try:
            # Email mesajı oluştur
            msg = MIMEMultipart('alternative')
            msg['Subject'] = konu
            msg['From'] = self.sender_email
            msg['To'] = alici_email
            
            # HTML içeriği ekle
            html_part = MIMEText(mesaj_html, 'html', 'utf-8')
            msg.attach(html_part)
            
            # SMTP sunucusuna bağlan ve gönder
            server = smtplib.SMTP(self.smtp_server, self.smtp_port)
            server.starttls()
            server.login(self.sender_email, self.sender_password)
            server.send_message(msg)
            server.quit()
            
            print(f"✓ Email gönderildi: {alici_email}")
            return True
            
        except smtplib.SMTPAuthenticationError:
            print("✗ Email gönderme hatası: Kullanıcı adı/şifre hatalı")
            print("  Gmail için 2FA aktifse 'Uygulama Şifresi' kullanmalısınız")
            return False
        except Exception as e:
            print(f"✗ Email gönderme hatası: {str(e)}")
            return False

# Global email service instance
email_service = EmailService()


