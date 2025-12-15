<?php
session_start();
include "auth_helper.php";
requireRole('ogretmen');

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Öğretmen Paneli | Kampüs Sistemi</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .panel-container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 20px;
    }
    
    .panel-header {
      background: linear-gradient(135deg, #0066cc 0%, #004499 100%);
      color: white;
      padding: 30px;
      border-radius: 15px;
      margin-bottom: 30px;
      box-shadow: 0 8px 20px rgba(0,102,204,0.3);
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
      color: #0066cc;
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
      background: #0066cc;
      color: white;
      border-color: #0066cc;
      transform: translateY(-3px);
    }
  </style>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="panel-container">
  <div class="panel-header">
    <h1>Öğretmen Paneli</h1>
    <p>Hoş geldiniz, <?php echo htmlspecialchars($_SESSION['ad'] . ' ' . $_SESSION['soyad']); ?></p>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <h3>Bekleyen Randevular</h3>
      <p class="value" id="bekleyenRandevuSayisi">-</p>
    </div>
    <div class="stat-card">
      <h3>Toplam Randevular</h3>
      <p class="value" id="toplamRandevuSayisi">-</p>
    </div>
    <div class="stat-card">
      <h3>Bu Ay Tamamlanan</h3>
      <p class="value" id="tamamlananSayisi">-</p>
    </div>
  </div>

  <div class="quick-actions">
    <h2>Hızlı İşlemler</h2>
    <div class="actions-grid">
      <a href="ogretmen-randevular.php" class="action-btn">Randevularım</a>
      <a href="ogretim-uyesi-program.php" class="action-btn">Çalışma Programım</a>
      <a href="etkinlikler.php" class="action-btn">Etkinlikler</a>
      <a href="takvim.php" class="action-btn">Akademik Takvim</a>
    </div>
  </div>
</div>

<script>
const API_BASE_URL = 'http://localhost:8000/api';
const ogretmenEmail = "<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email'], ENT_QUOTES) : ''; ?>";
let ogretimUyesiId = null;

// Önce ogretim_uyesi_id'yi bul, sonra istatistikleri yükle
async function initOgretmenPanel() {
  try {
    if (!ogretmenEmail) {
      console.warn('Öğretmen emaili bulunamadı');
      return;
    }

    const res = await fetch(`${API_BASE_URL}/randevu/ogretim-uyeleri`);
    if (!res.ok) {
      console.error('Öğretim üyeleri alınamadı', res.status);
      return;
    }

    const ogretimUyeleri = await res.json();
    const eslesen = ogretimUyeleri.find(u => (u.email || '').toLowerCase() === ogretmenEmail.toLowerCase());

    if (!eslesen) {
      console.warn('Bu email ile eşleşen öğretim üyesi bulunamadı:', ogretmenEmail);
      return;
    }

    ogretimUyesiId = eslesen.id;
    await yukleIstatistikler();
  } catch (error) {
    console.error('initOgretmenPanel hatası:', error);
  }
}

// İstatistikleri yükle
async function yukleIstatistikler() {
  try {
    if (!ogretimUyesiId) {
      return; // henüz eşleşme yapılmadı
    }

    // Doğru endpoint'i kullan
    const response = await fetch(`${API_BASE_URL}/randevu/randevular?ogretim_uyesi_id=${ogretimUyesiId}`);
    if (response.ok) {
      const randevular = await response.json();
      const bekleyen = randevular.filter(r => r.durum === 'bekliyor').length;
      const tamamlanan = randevular.filter(r => r.durum === 'tamamlandi').length;
      
      document.getElementById('bekleyenRandevuSayisi').textContent = bekleyen;
      document.getElementById('toplamRandevuSayisi').textContent = randevular.length;
      document.getElementById('tamamlananSayisi').textContent = tamamlanan;
    }
  } catch (error) {
    console.error('Hata:', error);
  }
}

document.addEventListener('DOMContentLoaded', initOgretmenPanel);
</script>

<?php include "footer.php"; ?>

</body>
</html>
