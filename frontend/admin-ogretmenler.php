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
  <title>Öğretim Üyesi Yönetimi | Admin Paneli</title>
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
  </style>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="admin-container">
  <div class="page-header">
    <h1>Öğretim Üyesi Yönetimi</h1>
  </div>

  <div class="content-box">
    <h2>Tüm Öğretim Üyeleri</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Ad Soyad</th>
          <th>Unvan</th>
          <th>Bölüm</th>
          <th>Email</th>
          <th>Telefon</th>
          <th>Durum</th>
        </tr>
      </thead>
      <tbody id="ogretmenlerTable">
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

// Öğretim üyelerini yükle
async function yukleOgretmenler() {
  try {
    const response = await fetch(`${API_BASE_URL}/randevu/ogretim-uyeleri`);
    if (response.ok) {
      const ogretmenler = await response.json();
      renderOgretmenler(ogretmenler);
    } else {
      document.getElementById('ogretmenlerTable').innerHTML = `
        <tr>
          <td colspan="7" style="text-align: center; padding: 40px; color: #999;">
            Öğretim üyesi listesi yüklenemedi.
          </td>
        </tr>
      `;
    }
  } catch (error) {
    console.error('Hata:', error);
    document.getElementById('ogretmenlerTable').innerHTML = `
      <tr>
        <td colspan="7" style="text-align: center; padding: 40px; color: #dc3545;">
          Veri yüklenirken hata oluştu.
        </td>
      </tr>
    `;
  }
}

function renderOgretmenler(ogretmenler) {
  if (ogretmenler.length === 0) {
    document.getElementById('ogretmenlerTable').innerHTML = `
      <tr>
        <td colspan="7" style="text-align: center; padding: 40px;">
          Henüz öğretim üyesi bulunmuyor.
        </td>
      </tr>
    `;
    return;
  }
  
  let html = '';
  ogretmenler.forEach(ogretmen => {
    html += `
      <tr>
        <td>${ogretmen.id}</td>
        <td>${ogretmen.ad} ${ogretmen.soyad}</td>
        <td>${ogretmen.unvan || '-'}</td>
        <td>${ogretmen.bolum || '-'}</td>
        <td>${ogretmen.email}</td>
        <td>${ogretmen.telefon || '-'}</td>
        <td>${ogretmen.aktif ? 'Aktif' : 'Pasif'}</td>
      </tr>
    `;
  });
  
  document.getElementById('ogretmenlerTable').innerHTML = html;
}

document.addEventListener('DOMContentLoaded', yukleOgretmenler);
</script>

<?php include "footer.php"; ?>

</body>
</html>
