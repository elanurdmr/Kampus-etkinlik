<?php include "db.php"; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Akademik Takvim | Kampüs Sistemi</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .loading {
      text-align: center;
      padding: 20px;
      font-size: 18px;
      color: #666;
    }
    .error-message {
      background: #ffebee;
      color: #c62828;
      padding: 15px;
      border-radius: 5px;
      margin: 20px;
    }
    .etkinlik-card {
      background: white;
      padding: 20px;
      margin: 15px 0;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      border-left: 4px solid #2196F3;
    }
    .etkinlik-card h3 {
      margin-top: 0;
      color: #2196F3;
    }
    .etkinlik-type {
      display: inline-block;
      padding: 5px 10px;
      border-radius: 15px;
      font-size: 12px;
      font-weight: bold;
      margin-right: 10px;
    }
    .type-sinav { background: #ff5722; color: white; }
    .type-odev { background: #ff9800; color: white; }
    .type-etkinlik { background: #4caf50; color: white; }
    .etkinlik-info {
      margin: 10px 0;
      color: #555;
    }
    .etkinlik-date {
      font-weight: bold;
      color: #333;
    }
    .filter-buttons {
      margin: 20px 0;
      text-align: center;
    }
    .filter-btn {
      padding: 10px 20px;
      margin: 0 5px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      background: #e0e0e0;
      color: #333;
      font-weight: bold;
    }
    .filter-btn.active {
      background: #2196F3;
      color: white;
    }
  </style>
</head>
<body>

<?php
  $currentPage = basename($_SERVER['PHP_SELF']);
?>

<?php include "header.php"; ?>


<main class="takvim-container">
  <h2>🎓 Akademik Takvim - Backend API</h2>
  <p style="text-align: center; color: #666;">Bu sayfa Backend API'den veri çekmektedir: <code>http://localhost:8010/api/calendar</code></p>
  
  <div class="filter-buttons">
    <button class="filter-btn active" data-filter="all">Tümü</button>
    <button class="filter-btn" data-filter="sinav">Sınavlar</button>
    <button class="filter-btn" data-filter="odev">Ödevler</button>
    <button class="filter-btn" data-filter="etkinlik">Etkinlikler</button>
  </div>

  <div id="loading" class="loading">
    <p>⏳ Etkinlikler yükleniyor...</p>
  </div>

  <div id="error" style="display: none;"></div>
  <div id="etkinlikler-container"></div>
</main>

<?php include "footer.php"; ?>

<script>
const API_URL = 'http://localhost:8010/api/calendar';
let allEtkinlikler = [];
let currentFilter = 'all';

// Sayfa yüklendiğinde etkinlikleri çek
document.addEventListener('DOMContentLoaded', function() {
  fetchEtkinlikler();
  setupFilters();
});

// Etkinlikleri Backend API'den çek
async function fetchEtkinlikler() {
  const loadingDiv = document.getElementById('loading');
  const errorDiv = document.getElementById('error');
  const container = document.getElementById('etkinlikler-container');
  
  try {
    loadingDiv.style.display = 'block';
    errorDiv.style.display = 'none';
    
    const response = await fetch(`${API_URL}/etkinlikler`);
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    const data = await response.json();
    allEtkinlikler = data;
    
    loadingDiv.style.display = 'none';
    displayEtkinlikler(allEtkinlikler);
    
  } catch (error) {
    console.error('Hata:', error);
    loadingDiv.style.display = 'none';
    errorDiv.style.display = 'block';
    errorDiv.className = 'error-message';
    errorDiv.innerHTML = `
      <strong>⚠️ Hata:</strong> Backend API'ye bağlanılamadı.<br>
      <small>Lütfen Backend'in çalıştığından emin olun: http://localhost:8010</small><br>
      <small>Hata detayı: ${error.message}</small>
    `;
  }
}

// Etkinlikleri göster
function displayEtkinlikler(etkinlikler) {
  const container = document.getElementById('etkinlikler-container');
  
  if (etkinlikler.length === 0) {
    container.innerHTML = `
      <div style="text-align: center; padding: 40px; color: #999;">
        <h3>📅 Henüz etkinlik bulunmamaktadır</h3>
        <p>Yeni etkinlik eklemek için <a href="etkinlik-yonetim.php">Etkinlik Yönetimi</a> sayfasını kullanabilirsiniz.</p>
      </div>
    `;
    return;
  }
  
  container.innerHTML = etkinlikler.map(etkinlik => {
    const baslangicTarihi = new Date(etkinlik.baslangic_tarihi);
    const bitisTarihi = etkinlik.bitis_tarihi ? new Date(etkinlik.bitis_tarihi) : null;
    
    return `
      <div class="etkinlik-card" data-type="${etkinlik.etkinlik_turu}">
        <h3>
          <span class="etkinlik-type type-${etkinlik.etkinlik_turu}">${etkinlik.etkinlik_turu.toUpperCase()}</span>
          ${etkinlik.baslik}
        </h3>
        <div class="etkinlik-info">
          <p><strong>📅 Başlangıç:</strong> <span class="etkinlik-date">${formatTarih(baslangicTarihi)}</span></p>
          ${bitisTarihi ? `<p><strong>📅 Bitiş:</strong> <span class="etkinlik-date">${formatTarih(bitisTarihi)}</span></p>` : ''}
          ${etkinlik.konum ? `<p><strong>📍 Konum:</strong> ${etkinlik.konum}</p>` : ''}
          ${etkinlik.aciklama ? `<p><strong>📝 Açıklama:</strong> ${etkinlik.aciklama}</p>` : ''}
        </div>
        <div style="margin-top: 10px; font-size: 12px; color: #999;">
          <span>🆔 ID: ${etkinlik.id}</span> | 
          <span>✅ ${etkinlik.aktif ? 'Aktif' : 'Pasif'}</span>
        </div>
      </div>
    `;
  }).join('');
}

// Tarih formatlama
function formatTarih(tarih) {
  const options = { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  };
  return tarih.toLocaleDateString('tr-TR', options);
}

// Filtreleme butonlarını ayarla
function setupFilters() {
  const filterButtons = document.querySelectorAll('.filter-btn');
  
  filterButtons.forEach(button => {
    button.addEventListener('click', function() {
      // Aktif butonu değiştir
      filterButtons.forEach(btn => btn.classList.remove('active'));
      this.classList.add('active');
      
      // Filtreyi uygula
      const filter = this.dataset.filter;
      currentFilter = filter;
      
      if (filter === 'all') {
        displayEtkinlikler(allEtkinlikler);
      } else {
        const filtered = allEtkinlikler.filter(e => e.etkinlik_turu === filter);
        displayEtkinlikler(filtered);
      }
    });
  });
}

// Sayfa her 30 saniyede bir otomatik yenilensin (opsiyonel)
setInterval(fetchEtkinlikler, 30000);
</script>
<script src="script.js"></script>

