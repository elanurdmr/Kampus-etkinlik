<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "db.php";
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Akademik Takvim | Kampüs Sistemi</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .takvim-container {
      padding: 30px;
      max-width: 1400px;
      margin: 0 auto;
    }
    
    .takvim-baslik {
      text-align: center;
      color: #c41e3a;
      margin-bottom: 30px;
      font-size: 2em;
    }
    
    .donem-section {
      margin-bottom: 40px;
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .donem-baslik {
      background: linear-gradient(135deg, #c41e3a 0%, #8b1528 100%);
      color: white;
      padding: 15px 20px;
      border-radius: 8px;
      margin: -20px -20px 20px -20px;
      font-size: 1.5em;
      font-weight: bold;
    }
    
    .takvim-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    
    .takvim-table th {
      background: #f8f9fa;
      color: #333;
      padding: 12px;
      text-align: left;
      font-weight: 600;
      border-bottom: 2px solid #c41e3a;
    }
    
    .takvim-table td {
      padding: 12px;
      border-bottom: 1px solid #e9ecef;
    }
    
    .takvim-table tr:hover {
      background: #f8f9fa;
    }
    
    .tarih-col {
      width: 25%;
      color: #c41e3a;
      font-weight: 500;
    }
    
    .aciklama-col {
      width: 75%;
    }
    
    .tatil-item {
      background: #fff3cd;
    }
  </style>
</head>
<body>

<?php
  $currentPage = basename($_SERVER['PHP_SELF']);
?>

<?php include "navbar.php"; ?>

<main class="takvim-container">
  <h1 class="takvim-baslik">2025-2026 Akademik Takvimi</h1>

  <!-- GÜZ DÖNEMİ -->
  <div class="donem-section">
    <div class="donem-baslik">🍂 Güz Dönemi</div>
    <table class="takvim-table">
      <thead>
        <tr>
          <th class="tarih-col">Tarih</th>
          <th class="aciklama-col">Etkinlik / Açıklama</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="tarih-col">10-30 Temmuz 2025</td>
          <td>Kurum Dışı Yatay Geçiş (Başarı Ortalaması ile) Başvuru Tarihleri</td>
        </tr>
        <tr>
          <td class="tarih-col">6 Ağustos 2025</td>
          <td>Kurum Dışı Yatay Geçiş Başvuru Sonuçlarının İlanı</td>
        </tr>
        <tr>
          <td class="tarih-col">07-08 Ağustos 2025</td>
          <td>Yatay Geçişe Hak Kazanan Asil Öğrencilerin Kayıt Tarihleri</td>
        </tr>
        <tr>
          <td class="tarih-col">08-10 Eylül 2025</td>
          <td>Başarı Ortalaması ile Kurum İçi Yatay Geçiş Başvuru Tarihleri</td>
        </tr>
        <tr>
          <td class="tarih-col">10-12 Eylül 2025</td>
          <td>Çift Anadal ve Yandal Programlarına Başvuru Tarihleri</td>
        </tr>
        <tr>
          <td class="tarih-col">15 Eylül 2025</td>
          <td>Doğuş Üniversitesi Yeterlik Sınavı (DÜYES)</td>
        </tr>
        <tr>
          <td class="tarih-col">16-19 Eylül 2025</td>
          <td>Ders Kayıt Tarihleri</td>
        </tr>
        <tr>
          <td class="tarih-col">22 Eylül 2025</td>
          <td><strong>Güz Dönemi Derslerinin Başlaması</strong> (Açılış Töreni - Oryantasyon)</td>
        </tr>
        <tr>
          <td class="tarih-col">30 Eylül 2025</td>
          <td>İngilizce Dersi Muafiyet Sınavı (Yeni Kayıt Yaptıran Öğrenciler İçin)</td>
        </tr>
        <tr>
          <td class="tarih-col">30 Eylül - 2 Ekim 2025</td>
          <td>Güz Dönemi Ders Ekleme-Bırakma Tarihleri</td>
        </tr>
        <tr>
          <td class="tarih-col">10 Kasım 2025</td>
          <td class="tatil-item"><strong>Atatürk'ü Anma Günü</strong></td>
        </tr>
        <tr>
          <td class="tarih-col">3-9 Kasım 2025</td>
          <td>Ön Lisans Güz Dönemi Ara Sınav Tarihleri</td>
        </tr>
        <tr>
          <td class="tarih-col">3-16 Kasım 2025</td>
          <td>Lisans Güz Dönemi Ara Sınav Tarihleri</td>
        </tr>
        <tr>
          <td class="tarih-col">20 Kasım 2025</td>
          <td>Ara Sınav Notlarının Sisteme Girişi İçin Son Tarih</td>
        </tr>
        <tr>
          <td class="tarih-col">1-3 Aralık 2025</td>
          <td>Ara Sınav Mazeret Tarihleri</td>
        </tr>
        <tr>
          <td class="tarih-col">26 Aralık 2025</td>
          <td><strong>Güz Yarıyılı Derslerinin Sonu</strong></td>
        </tr>
        <tr>
          <td class="tarih-col">29 Aralık 2025 - 11 Ocak 2026</td>
          <td><strong>Güz Dönemi Final Sınavı Tarihleri</strong></td>
        </tr>
        <tr>
          <td class="tarih-col">12 Ocak 2026</td>
          <td>Bitirme Projesi / Yönlendirilmiş Çalışma Ödevlerinin Son Teslim Günü</td>
        </tr>
        <tr>
          <td class="tarih-col">13 Ocak 2026</td>
          <td>Final Sınavı Notlarının Sisteme Girişi İçin Son Tarih</td>
        </tr>
        <tr>
          <td class="tarih-col">19-27 Ocak 2026</td>
          <td>Güz Dönemi Bütünleme Sınavı Tarihleri</td>
        </tr>
        <tr>
          <td class="tarih-col">26 Ocak 2026</td>
          <td>Doğuş Üniversitesi Yeterlik Sınavı (DÜYES)</td>
        </tr>
        <tr>
          <td class="tarih-col">3-5 Şubat 2026</td>
          <td>Tek Ders Sınavı ve İlgili İşlemler</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- BAHAR DÖNEMİ -->
  <div class="donem-section">
    <div class="donem-baslik">🌸 Bahar Dönemi</div>
    <table class="takvim-table">
      <thead>
        <tr>
          <th class="tarih-col">Tarih</th>
          <th class="aciklama-col">Etkinlik / Açıklama</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="tarih-col">10-13 Şubat 2026</td>
          <td>Bahar Dönemi Ders Kayıt Tarihleri</td>
        </tr>
        <tr>
          <td class="tarih-col">16 Şubat 2026</td>
          <td><strong>Bahar Dönemi Derslerinin Başlaması</strong></td>
        </tr>
        <tr>
          <td class="tarih-col">17-20 Şubat 2026</td>
          <td>Yatay Geçiş ve Çift Anadal/Yandal İşlemleri</td>
        </tr>
        <tr>
          <td class="tarih-col">25 Şubat 2026</td>
          <td>İngilizce Dersi Muafiyet Sınavı (Yeni Kayıt Yaptıran Öğrenciler İçin)</td>
        </tr>
        <tr>
          <td class="tarih-col">24-26 Şubat 2026</td>
          <td>Bahar Dönemi Ders Ekleme-Bırakma Tarihleri</td>
        </tr>
        <tr>
          <td class="tarih-col">20-22 Mart 2026</td>
          <td class="tatil-item"><strong>Ramazan Bayramı</strong></td>
        </tr>
        <tr>
          <td class="tarih-col">30 Mart - 5 Nisan 2026</td>
          <td>Ön Lisans Bahar Dönemi Ara Sınav Tarihleri</td>
        </tr>
        <tr>
          <td class="tarih-col">30 Mart - 11 Nisan 2026</td>
          <td>Lisans Bahar Dönemi Ara Sınav Tarihleri</td>
        </tr>
        <tr>
          <td class="tarih-col">15 Nisan 2026</td>
          <td>Ara Sınav Notlarının Sisteme Girişi İçin Son Tarih</td>
        </tr>
        <tr>
          <td class="tarih-col">20-21-22 Nisan 2026</td>
          <td>Ara Sınav Mazeret Sınavı Tarihleri</td>
        </tr>
        <tr>
          <td class="tarih-col">23 Nisan 2026</td>
          <td class="tatil-item"><strong>Ulusal Egemenlik ve Çocuk Bayramı</strong></td>
        </tr>
        <tr>
          <td class="tarih-col">1 Mayıs 2026</td>
          <td class="tatil-item"><strong>Emek ve Dayanışma Günü</strong></td>
        </tr>
        <tr>
          <td class="tarih-col">19 Mayıs 2026</td>
          <td class="tatil-item"><strong>Atatürk'ü Anma Gençlik ve Spor Bayramı</strong></td>
        </tr>
        <tr>
          <td class="tarih-col">25 Mayıs 2026</td>
          <td><strong>Bahar Yarıyılı Derslerinin Sonu</strong></td>
        </tr>
        <tr>
          <td class="tarih-col">27-30 Mayıs 2026</td>
          <td class="tatil-item"><strong>Kurban Bayramı</strong></td>
        </tr>
        <tr>
          <td class="tarih-col">01-14 Haziran 2026</td>
          <td><strong>Bahar Yarıyılı Final Sınavı Tarihleri</strong></td>
        </tr>
        <tr>
          <td class="tarih-col">16 Haziran 2026</td>
          <td>Bitirme Projesi / Yönlendirilmiş Çalışma Ödevlerinin Son Teslim Tarihi</td>
        </tr>
        <tr>
          <td class="tarih-col">17 Haziran 2026</td>
          <td>Final Sınavı Notlarının Sisteme Girişi İçin Son Tarih</td>
        </tr>
        <tr>
          <td class="tarih-col">22-30 Haziran 2026</td>
          <td>Bütünleme Sınavı Tarihleri</td>
        </tr>
        <tr>
          <td class="tarih-col">22 Haziran 2026</td>
          <td>Doğuş Üniversitesi İngilizce Yeterlik Sınavı (DÜYES)</td>
        </tr>
        <tr>
          <td class="tarih-col">6-7 Temmuz 2026</td>
          <td>Tek Ders Sınavı Tarihi</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- YAZ ÖĞRETİMİ -->
  <div class="donem-section">
    <div class="donem-baslik">Yaz Öğretimi</div>
    <table class="takvim-table">
      <thead>
        <tr>
          <th class="tarih-col">Tarih</th>
          <th class="aciklama-col">Etkinlik / Açıklama</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="tarih-col">7-9 Temmuz 2026</td>
          <td>Yaz Döneminde Açılacak Derslerin Belirlenmesi İçin Ders Seçimi ve Mali Kayıt</td>
        </tr>
        <tr>
          <td class="tarih-col">10 Temmuz 2026</td>
          <td>Yaz Döneminde Açılan Derslerin İlanı</td>
        </tr>
        <tr>
          <td class="tarih-col">13 Temmuz 2026</td>
          <td><strong>Yaz Dönemi Derslerinin Başlangıcı</strong></td>
        </tr>
        <tr>
          <td class="tarih-col">15 Temmuz 2026</td>
          <td class="tatil-item"><strong>Demokrasi ve Milli Birlik Günü</strong></td>
        </tr>
        <tr>
          <td class="tarih-col">16-17 Temmuz 2026</td>
          <td>Açılan Derslerden Yeni Ders Seçme ve Açılamayan Derslerin Yerine Açılan Dersi Alma</td>
        </tr>
        <tr>
          <td class="tarih-col">27 Temmuz - 1 Ağustos</td>
          <td>Yaz Dönemi Ara Sınav Tarihleri</td>
        </tr>
        <tr>
          <td class="tarih-col">3 Ağustos 2026</td>
          <td>Ara Sınav Notlarının Sisteme Girişi İçin Son Tarih</td>
        </tr>
        <tr>
          <td class="tarih-col">22 Ağustos 2026</td>
          <td><strong>Yaz Dönemi Derslerinin Son Günü</strong></td>
        </tr>
        <tr>
          <td class="tarih-col">30 Ağustos 2026</td>
          <td class="tatil-item"><strong>Zafer Bayramı</strong></td>
        </tr>
        <tr>
          <td class="tarih-col">31 Ağustos - 5 Eylül 2026</td>
          <td>Yaz Dönemi Final Sınavı Tarihleri</td>
        </tr>
        <tr>
          <td class="tarih-col">6 Eylül 2026</td>
          <td>Final Sınavı Notlarının Sisteme Girişi İçin Son Tarih</td>
        </tr>
        <tr>
          <td class="tarih-col">08-12 Eylül 2026</td>
          <td>Bütünleme Sınavı Tarihleri</td>
        </tr>
        <tr>
          <td class="tarih-col">16 Eylül 2026</td>
          <td>Tek Ders Sınavı Tarihi</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- RESMİ TATİLLER -->
  <div class="donem-section">
    <div class="donem-baslik">Resmi Tatiller</div>
    <table class="takvim-table">
      <thead>
        <tr>
          <th class="tarih-col">Tarih</th>
          <th class="aciklama-col">Tatil</th>
        </tr>
      </thead>
      <tbody>
        <tr class="tatil-item">
          <td class="tarih-col">28-29 Ekim 2025</td>
          <td><strong>Cumhuriyet Bayramı</strong></td>
        </tr>
        <tr class="tatil-item">
          <td class="tarih-col">1 Ocak 2026</td>
          <td><strong>Yılbaşı</strong></td>
        </tr>
        <tr class="tatil-item">
          <td class="tarih-col">20-22 Mart 2026</td>
          <td><strong>Ramazan Bayramı</strong></td>
        </tr>
        <tr class="tatil-item">
          <td class="tarih-col">23 Nisan 2026</td>
          <td><strong>Ulusal Egemenlik ve Çocuk Bayramı</strong></td>
        </tr>
        <tr class="tatil-item">
          <td class="tarih-col">1 Mayıs 2026</td>
          <td><strong>Emek ve Dayanışma Günü</strong></td>
        </tr>
        <tr class="tatil-item">
          <td class="tarih-col">19 Mayıs 2026</td>
          <td><strong>Atatürk'ü Anma Gençlik ve Spor Bayramı</strong></td>
        </tr>
        <tr class="tatil-item">
          <td class="tarih-col">27-30 Mayıs 2026</td>
          <td><strong>Kurban Bayramı</strong></td>
        </tr>
        <tr class="tatil-item">
          <td class="tarih-col">15 Temmuz 2026</td>
          <td><strong>Demokrasi ve Milli Birlik Günü</strong></td>
        </tr>
        <tr class="tatil-item">
          <td class="tarih-col">30 Ağustos 2026</td>
          <td><strong>Zafer Bayramı</strong></td>
        </tr>
      </tbody>
    </table>
  </div>

  <div style="text-align: center; margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
    <p style="color: #666; font-size: 0.9em;">
      <strong>Not:</strong> Tüm işlemler için Öğrenci Bilgi Sistemi (OBS) 16:00'da kapanacaktır.<br>
      Yükseköğretim Kurumu'ndan yapılacak değişiklikler ve yeni kararlara uygun olarak akademik takvimde güncellemeler yapılabilecektir.
    </p>
  </div>
</main>

<?php include "footer.php"; ?>

</body>
</html>
