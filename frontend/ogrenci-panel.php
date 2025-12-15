<?php
session_start();
include "auth_helper.php";
requireRole('ogrenci');

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Öğrenci Paneli | Kampüs Sistemi</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .panel-container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 20px;
    }
    
    .panel-header {
      background: linear-gradient(135deg, #b30000 0%, #8b0000 100%);
      color: white;
      padding: 30px;
      border-radius: 15px;
      margin-bottom: 30px;
      box-shadow: 0 8px 20px rgba(179,0,0,0.3);
    }
    
    .panel-header h1 {
      margin: 0 0 10px 0;
      font-size: 2em;
    }
    
    .panel-header p {
      margin: 0;
      opacity: 0.9;
    }
    
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    
    .stat-card {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: transform 0.3s;
    }
    
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .stat-card h3 {
      margin: 0 0 10px 0;
      color: #666;
      font-size: 0.9em;
      font-weight: 600;
    }
    
    .stat-card .value {
      font-size: 2.5em;
      font-weight: bold;
      color: #b30000;
      margin: 0;
    }
    
    .quick-actions {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }
    
    .quick-actions h2 {
      margin: 0 0 20px 0;
      color: #333;
    }
    
    .actions-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
    }
    
    .action-btn {
      display: block;
      padding: 20px;
      background: #f5f5f5;
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      text-decoration: none;
      color: #333;
      text-align: center;
      transition: all 0.3s;
      font-weight: 600;
    }
    
    .action-btn:hover {
      background: #b30000;
      color: white;
      border-color: #b30000;
      transform: translateY(-3px);
    }
  </style>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="panel-container">
  <div class="panel-header">
    <h1>Öğrenci Paneli</h1>
    <p>Hoş geldiniz, <?php echo htmlspecialchars($_SESSION['ad'] . ' ' . $_SESSION['soyad']); ?></p>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <h3>Aktif Randevularım</h3>
      <p class="value" id="randevuSayisi">-</p>
    </div>
    <div class="stat-card">
      <h3>Kütüphane Rezervasyonlarım</h3>
      <p class="value" id="rezervasyonSayisi">-</p>
    </div>
    <div class="stat-card">
      <h3>Okunmamış Bildirimler</h3>
      <p class="value" id="bildirimSayisi">-</p>
    </div>
  </div>

  <div class="quick-actions">
    <h2>Hızlı İşlemler</h2>
    <div class="actions-grid">
      <a href="randevu-olustur.php" class="action-btn">Randevu Al</a>
      <a href="randevularim.php" class="action-btn">Randevularım</a>
      <a href="rezervasyon-yap.php" class="action-btn">Kütüphane Rezervasyonu</a>
      <a href="rezervasyonlarim.php" class="action-btn">Rezervasyonlarım</a>
      <a href="etkinlikler.php" class="action-btn">Etkinlikler</a>
      <a href="etkinlik-onerileri.php" class="action-btn">Öneriler</a>
      <a href="ilgi-alanlari.php" class="action-btn">İlgi Alanlarım</a>
      <a href="bildirimler.php" class="action-btn">Bildirimler</a>
      <a href="arkadaslar.php" class="action-btn">Arkadaşlarım</a>
    </div>
  </div>
</div>

<script>
const API_BASE_URL = 'http://localhost:8000/api';
// kullaniciId navbar.php'den geliyor
if (typeof window.kullaniciId === 'undefined') {
  window.kullaniciId = <?php echo $_SESSION['user_id']; ?>;
}

// İstatistikleri yükle
async function yukleIstatistikler() {
  try {
    // Randevu sayısı
    const randevuResponse = await fetch(`${API_BASE_URL}/randevu/kullanici/${kullaniciId}/randevular`);
    if (randevuResponse.ok) {
      const randevular = await randevuResponse.json();
      const aktifRandevular = randevular.filter(r => r.durum === 'bekliyor' || r.durum === 'onaylandi');
      document.getElementById('randevuSayisi').textContent = aktifRandevular.length;
    }
    
    // Bildirim sayısı
    const bildirimResponse = await fetch(`${API_BASE_URL}/bildirimler/kullanici/${kullaniciId}/bildirimler/okunmamis-sayisi`);
    if (bildirimResponse.ok) {
      const data = await bildirimResponse.json();
      document.getElementById('bildirimSayisi').textContent = data.okunmamis_sayisi || 0;
    }
    
    // Rezervasyon sayısı (kütüphane API'si varsa)
    // document.getElementById('rezervasyonSayisi').textContent = '0';
  } catch (error) {
    console.error('Hata:', error);
  }
}

document.addEventListener('DOMContentLoaded', yukleIstatistikler);
</script>

<?php include "footer.php"; ?>

</body>
</html>
