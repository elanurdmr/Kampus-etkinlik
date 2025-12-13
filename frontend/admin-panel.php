<?php
session_start();
include "auth_helper.php";
requireRole('admin');

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Paneli | Kampüs Sistemi</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .panel-container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 20px;
    }
    
    .panel-header {
      background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
      color: white;
      padding: 30px;
      border-radius: 15px;
      margin-bottom: 30px;
      box-shadow: 0 8px 20px rgba(40,167,69,0.3);
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
      color: #28a745;
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
      background: #28a745;
      color: white;
      border-color: #28a745;
      transform: translateY(-3px);
    }
  </style>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="panel-container">
  <div class="panel-header">
    <h1>Admin Paneli</h1>
    <p>Hoş geldiniz, <?php echo htmlspecialchars($_SESSION['ad'] . ' ' . $_SESSION['soyad']); ?></p>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <h3>Toplam Kullanıcı</h3>
      <p class="value" id="kullaniciSayisi">-</p>
    </div>
    <div class="stat-card">
      <h3>Toplam Randevu</h3>
      <p class="value" id="randevuSayisi">-</p>
    </div>
    <div class="stat-card">
      <h3>Aktif Etkinlik</h3>
      <p class="value" id="etkinlikSayisi">-</p>
    </div>
    <div class="stat-card">
      <h3>Öğretim Üyesi</h3>
      <p class="value" id="ogretmenSayisi">-</p>
    </div>
  </div>

  <div class="quick-actions">
    <h2>Yönetim İşlemleri</h2>
    <div class="actions-grid">
      <a href="admin-kullanicilar.php" class="action-btn">Kullanıcı Yönetimi</a>
      <a href="admin-ogretmenler.php" class="action-btn">Öğretim Üyesi Yönetimi</a>
      <a href="admin-randevular.php" class="action-btn">Randevu Yönetimi</a>
      <a href="etkinlik-yonetim.php" class="action-btn">Etkinlik Yönetimi</a>
      <a href="etkinlikler.php" class="action-btn">Tüm Etkinlikler</a>
      <a href="ogretim-uyesi-program.php" class="action-btn">Öğretim Üyesi Programları</a>
    </div>
  </div>
</div>

<script>
const API_BASE_URL = 'http://localhost:8000/api';

// İstatistikleri yükle
async function yukleIstatistikler() {
  try {
    // Kullanıcı sayısı
    // const kullaniciResponse = await fetch(`${API_BASE_URL}/admin/kullanicilar/sayisi`);
    // if (kullaniciResponse.ok) {
    //   const data = await kullaniciResponse.json();
    //   document.getElementById('kullaniciSayisi').textContent = data.sayisi || 0;
    // }
    
    // Randevu sayısı
    // const randevuResponse = await fetch(`${API_BASE_URL}/randevu/tum-randevular`);
    // if (randevuResponse.ok) {
    //   const randevular = await randevuResponse.json();
    //   document.getElementById('randevuSayisi').textContent = randevular.length || 0;
    // }
    
    // Etkinlik sayısı
    // const etkinlikResponse = await fetch(`${API_BASE_URL}/calendar/etkinlikler`);
    // if (etkinlikResponse.ok) {
    //   const etkinlikler = await etkinlikResponse.json();
    //   document.getElementById('etkinlikSayisi').textContent = etkinlikler.length || 0;
    // }
    
    // Öğretmen sayısı
    // const ogretmenResponse = await fetch(`${API_BASE_URL}/randevu/ogretim-uyeleri`);
    // if (ogretmenResponse.ok) {
    //   const ogretmenler = await ogretmenResponse.json();
    //   document.getElementById('ogretmenSayisi').textContent = ogretmenler.length || 0;
    // }
  } catch (error) {
    console.error('Hata:', error);
  }
}

document.addEventListener('DOMContentLoaded', yukleIstatistikler);
</script>

<?php include "footer.php"; ?>

</body>
</html>
