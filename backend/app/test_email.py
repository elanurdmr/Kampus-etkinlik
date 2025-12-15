"""
Email gönderimini test etmek için script
"""
from email_service import email_service

def test_email():
    print("="*60)
    print("Email Gönderim Testi")
    print("="*60)
    print()
    
    # Email servisi durumu
    print(f"Email servisi aktif: {email_service.enabled}")
    print(f"Gönderen email: {email_service.sender_email}")
    print(f"Şifre ayarlı: {'Evet' if email_service.sender_password else 'Hayır'}")
    print()
    
    if not email_service.enabled:
        print("❌ Email servisi aktif değil!")
        print()
        print("Çözüm:")
        print("1. backend/app/.env dosyasını açın")
        print("2. EMAIL_ADDRESS ve EMAIL_PASSWORD değerlerini düzenleyin")
        print("3. Gmail uygulama şifresi alın: https://myaccount.google.com/apppasswords")
        print()
        return
    
    # Test email gönder
    test_email_adresi = input("Test email adresinizi girin: ").strip()
    
    if not test_email_adresi:
        print("❌ Email adresi girilmedi!")
        return
    
    print()
    print("Test emaili gönderiliyor...")
    
    try:
        basarili = email_service.randevu_hatirlatma_gonder(
            ogrenci_email=test_email_adresi,
            ogrenci_adi="Test Öğrenci",
            ogretim_uyesi_adi="Prof. Dr. Ahmet Yılmaz",
            randevu_tarihi="15.12.2025",
            randevu_saati="14:00",
            konu="Test Randevusu"
        )
        
        if basarili:
            print()
            print("✅ Email başarıyla gönderildi!")
            print(f"📧 {test_email_adresi} adresine kontrol edin")
            print("   (Spam klasörüne de bakmayı unutmayın)")
        else:
            print()
            print("❌ Email gönderilemedi!")
            print("   Backend loglarını kontrol edin")
            
    except Exception as e:
        print()
        print(f"❌ Hata: {str(e)}")
        print()
        print("Olası sorunlar:")
        print("- Gmail şifresi yanlış")
        print("- 2FA aktif değil")
        print("- Uygulama şifresi kullanılmamış")
        print("- İnternet bağlantısı yok")

if __name__ == "__main__":
    test_email()

