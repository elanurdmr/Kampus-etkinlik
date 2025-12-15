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
  <title>Randevu Yönetimi | Admin Paneli</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .admin-container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 20px;
    }
    
    .page-header {
      background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
      color: white;
      padding: 30px;
      border-radius: 15px;
      margin-bottom: 30px;
      box-shadow: 0 8px 20px rgba(40,167,69,0.3);
    }
    
    .page-header h1 {
      margin: 0;
      font-size: 2em;
    }
    
    .content-box {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      margin-bottom: 20px;
    }
    
    .content-box h2 {
      margin: 0 0 20px 0;
      color: #333;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
    }
    
    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }
    
    th {
      background: #f5f5f5;
      font-weight: 600;
      color: #333;
    }
    
    .badge {
      padding: 5px 10px;
      border-radius: 12px;
      font-size: 0.85em;
      font-weight: 600;
    }
    
    .badge-bekliyor { background: #ffc107; color: #000; }
    .badge-onaylandi { background: #28a745; color: white; }
    .badge-reddedildi { background: #dc3545; color: white; }
    .badge-tamamlandi { background: #17a2b8; color: white; }
    .badge-iptal { background: #6c757d; color: white; }
  </style>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="admin-container">
  <div class="page-header">
    <h1>Randevu Yönetimi</h1>
  </div>

  <div class="content-box">
    <h2>Tüm Randevular</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Öğrenci</th>
          <th>Öğretim Üyesi</th>
          <th>Tarih</th>
          <th>Saat</th>
          <th>Konu</th>
          <th>Durum</th>
        </tr>
      </thead>
      <tbody id="randevularTable">
        <tr>
          <td colspan="7" style="text-align: center; padding: 40px;">
            Yükleniyor...
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<script>
const API_BASE_URL = 'http://localhost:8000/api';

// Randevuları yükle
async function yukleRandevular() {
  try {
    // Tüm randevuları getir (admin için özel endpoint gerekebilir)
    const response = await fetch(`${API_BASE_URL}/randevu/randevular`);
    if (response.ok) {
      const randevular = await response.json();
      renderRandevular(randevular);
    } else {
      document.getElementById('randevularTable').innerHTML = `
        <tr>
          <td colspan="7" style="text-align: center; padding: 40px; color: #999;">
            Randevu listesi yüklenemedi. API endpoint'i kontrol edin.
          </td>
        </tr>
      `;
    }
  } catch (error) {
    console.error('Hata:', error);
    document.getElementById('randevularTable').innerHTML = `
      <tr>
        <td colspan="7" style="text-align: center; padding: 40px; color: #dc3545;">
          Veri yüklenirken hata oluştu.
        </td>
      </tr>
    `;
  }
}

function renderRandevular(randevular) {
  if (randevular.length === 0) {
    document.getElementById('randevularTable').innerHTML = `
      <tr>
        <td colspan="7" style="text-align: center; padding: 40px;">
          Henüz randevu bulunmuyor.
        </td>
      </tr>
    `;
    return;
  }
  
  let html = '';
  randevular.forEach(randevu => {
    const durumClass = `badge-${randevu.durum || 'bekliyor'}`;
    const durumText = randevu.durum === 'bekliyor' ? 'Bekliyor' :
                     randevu.durum === 'onaylandi' ? 'Onaylandı' :
                     randevu.durum === 'reddedildi' ? 'Reddedildi' :
                     randevu.durum === 'tamamlandi' ? 'Tamamlandı' :
                     randevu.durum === 'iptal_edildi' ? 'İptal Edildi' : randevu.durum;
    
    html += `
      <tr>
        <td>${randevu.id}</td>
        <td>Öğrenci #${randevu.ogrenci_id}</td>
        <td>Öğretim Üyesi #${randevu.ogretim_uyesi_id}</td>
        <td>${randevu.randevu_tarihi}</td>
        <td>${randevu.randevu_saati}</td>
        <td>${randevu.konu}</td>
        <td><span class="badge ${durumClass}">${durumText}</span></td>
      </tr>
    `;
  });
  
  document.getElementById('randevularTable').innerHTML = html;
}

document.addEventListener('DOMContentLoaded', yukleRandevular);
</script>

<?php include "footer.php"; ?>

</body>
</html>
