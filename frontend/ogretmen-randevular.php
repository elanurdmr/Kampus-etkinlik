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
  <title>Randevularım | Öğretmen Paneli</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .panel-container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 20px;
    }
    
    .page-header {
      background: linear-gradient(135deg, #0066cc 0%, #004499 100%);
      color: white;
      padding: 30px;
      border-radius: 15px;
      margin-bottom: 30px;
      box-shadow: 0 8px 20px rgba(0,102,204,0.3);
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
    
    .randevu-card {
      background: white;
      border: 1px solid #e0e0e0;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 15px;
      transition: all 0.3s;
    }
    
    .randevu-card:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      transform: translateY(-2px);
    }
    
    .randevu-header {
      display: flex;
      justify-content: space-between;
      align-items: start;
      margin-bottom: 15px;
    }
    
    .randevu-baslik {
      font-weight: 600;
      font-size: 1.1em;
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
    
    .btn {
      padding: 8px 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      margin-right: 10px;
      transition: all 0.3s;
    }
    
    .btn-onayla {
      background: #28a745;
      color: white;
    }
    
    .btn-onayla:hover {
      background: #218838;
    }
    
    .btn-reddet {
      background: #dc3545;
      color: white;
    }
    
    .btn-reddet:hover {
      background: #c82333;
    }
  </style>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="panel-container">
  <div class="page-header">
    <h1>Randevularım</h1>
  </div>

  <div class="content-box">
    <h2>Bekleyen Randevular</h2>
    <div id="randevularList">
      <p style="text-align: center; padding: 40px; color: #999;">Yükleniyor...</p>
    </div>
  </div>
</div>

<script>
const API_BASE_URL = 'http://localhost:8000/api';
const ogretmenId = <?php echo $_SESSION['user_id']; ?>;

// Randevuları yükle
async function yukleRandevular() {
  try {
    const response = await fetch(`${API_BASE_URL}/randevu/ogretim-uyesi/${ogretmenId}/randevular`);
    if (response.ok) {
      const randevular = await response.json();
      renderRandevular(randevular);
    } else {
      document.getElementById('randevularList').innerHTML = `
        <p style="text-align: center; padding: 40px; color: #dc3545;">
          Randevular yüklenemedi.
        </p>
      `;
    }
  } catch (error) {
    console.error('Hata:', error);
    document.getElementById('randevularList').innerHTML = `
      <p style="text-align: center; padding: 40px; color: #dc3545;">
        Veri yüklenirken hata oluştu.
      </p>
    `;
  }
}

function renderRandevular(randevular) {
  if (randevular.length === 0) {
    document.getElementById('randevularList').innerHTML = `
      <p style="text-align: center; padding: 40px; color: #999;">
        Henüz randevunuz bulunmuyor.
      </p>
    `;
    return;
  }
  
  let html = '';
  randevular.forEach(randevu => {
    const durumClass = `badge-${randevu.durum || 'bekliyor'}`;
    const durumText = randevu.durum === 'bekliyor' ? 'Bekliyor' :
                     randevu.durum === 'onaylandi' ? 'Onaylandı' :
                     randevu.durum === 'reddedildi' ? 'Reddedildi' :
                     randevu.durum === 'tamamlandi' ? 'Tamamlandı' : randevu.durum;
    
    const tarih = new Date(randevu.randevu_tarihi).toLocaleDateString('tr-TR');
    const saat = randevu.randevu_saati;
    
    html += `
      <div class="randevu-card">
        <div class="randevu-header">
          <div>
            <div class="randevu-baslik">${randevu.konu}</div>
            <p style="margin: 5px 0; color: #666;">${tarih} - ${saat}</p>
            ${randevu.aciklama ? `<p style="margin: 10px 0; color: #666;">${randevu.aciklama}</p>` : ''}
          </div>
          <span class="badge ${durumClass}">${durumText}</span>
        </div>
        ${randevu.durum === 'bekliyor' ? `
          <div>
            <button class="btn btn-onayla" onclick="randevuDurumGuncelle(${randevu.id}, 'onaylandi')">
              Onayla
            </button>
            <button class="btn btn-reddet" onclick="randevuDurumGuncelle(${randevu.id}, 'reddedildi')">
              Reddet
            </button>
          </div>
        ` : ''}
      </div>
    `;
  });
  
  document.getElementById('randevularList').innerHTML = html;
}

async function randevuDurumGuncelle(randevuId, yeniDurum) {
  try {
    const response = await fetch(`${API_BASE_URL}/randevu/randevu/${randevuId}/durum?yeni_durum=${yeniDurum}`, {
      method: 'PUT'
    });
    
    if (response.ok) {
      alert('Randevu durumu güncellendi');
      yukleRandevular();
    } else {
      alert('Randevu durumu güncellenemedi');
    }
  } catch (error) {
    console.error('Hata:', error);
    alert('Bağlantı hatası');
  }
}

document.addEventListener('DOMContentLoaded', yukleRandevular);
</script>

<?php include "footer.php"; ?>

</body>
</html>
