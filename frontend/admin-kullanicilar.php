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
  <title>Kullanıcı Yönetimi | Admin Paneli</title>
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
    
    .badge-admin { background: #28a745; color: white; }
    .badge-ogretmen { background: #0066cc; color: white; }
    .badge-ogrenci { background: #6c757d; color: white; }
  </style>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="admin-container">
  <div class="page-header">
    <h1>Kullanıcı Yönetimi</h1>
  </div>

  <div class="content-box">
    <h2>Tüm Kullanıcılar</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Ad Soyad</th>
          <th>Email</th>
          <th>Öğrenci No</th>
          <th>Rol</th>
          <th>Durum</th>
        </tr>
      </thead>
      <tbody id="kullanicilarTable">
        <tr>
          <td colspan="6" style="text-align: center; padding: 40px;">
            Yükleniyor...
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<script>
const API_BASE_URL = 'http://localhost:8000/api';

// Kullanıcıları yükle
async function yukleKullanicilar() {
  try {
    // Backend'de kullanıcı listesi endpoint'i yoksa, veritabanından çek
    const response = await fetch(`${API_BASE_URL}/kullanicilar`);
    if (response.ok) {
      const kullanicilar = await response.json();
      renderKullanicilar(kullanicilar);
    } else {
      // API yoksa placeholder göster
      document.getElementById('kullanicilarTable').innerHTML = `
        <tr>
          <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
            Kullanıcı listesi API'si henüz hazır değil. Veritabanından manuel kontrol edebilirsiniz.
          </td>
        </tr>
      `;
    }
  } catch (error) {
    console.error('Hata:', error);
    document.getElementById('kullanicilarTable').innerHTML = `
      <tr>
        <td colspan="6" style="text-align: center; padding: 40px; color: #dc3545;">
          Veri yüklenirken hata oluştu.
        </td>
      </tr>
    `;
  }
}

function renderKullanicilar(kullanicilar) {
  if (kullanicilar.length === 0) {
    document.getElementById('kullanicilarTable').innerHTML = `
      <tr>
        <td colspan="6" style="text-align: center; padding: 40px;">
          Henüz kullanıcı bulunmuyor.
        </td>
      </tr>
    `;
    return;
  }
  
  let html = '';
  kullanicilar.forEach(kullanici => {
    const rolClass = `badge-${kullanici.rol || 'ogrenci'}`;
    const rolText = kullanici.rol === 'admin' ? 'Admin' : 
                   (kullanici.rol === 'ogretmen' ? 'Öğretmen' : 'Öğrenci');
    
    html += `
      <tr>
        <td>${kullanici.id}</td>
        <td>${kullanici.ad} ${kullanici.soyad}</td>
        <td>${kullanici.email}</td>
        <td>${kullanici.ogrenci_no || '-'}</td>
        <td><span class="badge ${rolClass}">${rolText}</span></td>
        <td>${kullanici.aktif ? 'Aktif' : 'Pasif'}</td>
      </tr>
    `;
  });
  
  document.getElementById('kullanicilarTable').innerHTML = html;
}

document.addEventListener('DOMContentLoaded', yukleKullanicilar);
</script>

<?php include "footer.php"; ?>

</body>
</html>
