<?php
session_start();
include "db.php";
require_once "lang.php";
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= t('Etkinlik Takvimim | Kampüs Sistemi', 'My Event Calendar | Campus System') ?></title>
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
      display: none; /* Filtre butonlarını gizle */
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
    .katilacagim-container {
      margin-top: 40px;
    }
    .katilacagim-card {
      background: white;
      padding: 15px 20px;
      margin: 10px 0;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
      border-left: 4px solid #4caf50;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 8px;
    }
    .katilacagim-baslik {
      font-weight: 600;
      color: #333;
    }
    .katilacagim-detay {
      font-size: 0.9em;
      color: #666;
    }
    .katilacagim-geri-sayim {
      font-size: 0.9em;
      font-weight: 600;
      color: #c41e3a;
    }
  </style>
</head>
<body>

<?php
  $currentPage = basename($_SERVER['PHP_SELF']);
  $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
?>

<?php include "navbar.php"; ?>

<main class="takvim-container">
  <h2><?= t('Etkinlik Takvimim', 'My Event Calendar') ?></h2>
  
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

  <section class="katilacagim-container">
    <h3><?= t('Katılacağım Kulüp Etkinliklerim', 'Club Events I Will Attend') ?></h3>
    <p style="color:#666; font-size:0.95em;">
      <?= t(
        'İlgi alanlarına göre önerilen ve <strong>Katılacağım</strong> dediğin kulüp etkinlikleri burada listelenir.',
        'Club events recommended based on your interests and marked as <strong>I will attend</strong> are listed here.'
      ); ?>
    </p>
    <div id="katilacagim-container"></div>
  </section>
</main>

<?php include "footer.php"; ?>

<script>
const API_URL = 'http://localhost:8000/api/calendar';
const ONERI_API_URL = 'http://localhost:8000/api/oneri';
const CURRENT_USER_ID = <?php echo $userId; ?>;
let allEtkinlikler = [];
let currentFilter = 'all';

// Sayfa yüklendiğinde etkinlikleri çek
document.addEventListener('DOMContentLoaded', function() {
  fetchEtkinlikler();
  setupFilters();
  if (CURRENT_USER_ID > 0) {
    fetchKatilacagimEtkinlikler();
  }
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
      <strong>Hata:</strong> Backend API'ye bağlanılamadı.<br>
      <small>Lütfen Backend'in çalıştığından emin olun: http://localhost:8000</small><br>
      <small>Hata detayı: ${error.message}</small>
    `;
  }
}

// Etkinlikleri göster
function displayEtkinlikler(etkinlikler) {
  const container = document.getElementById('etkinlikler-container');
  
  if (etkinlikler.length === 0) {
    container.innerHTML = '';
    return;
  }
  
  container.innerHTML = etkinlikler.map(etkinlik => {
    const baslangicTarihi = new Date(etkinlik.baslangic_tarihi);
    const bitisTarihi = etkinlik.bitis_tarihi ? new Date(etkinlik.bitis_tarihi) : null;
    const now = new Date();
    let geriSayimHtml = '';
    if (baslangicTarihi > now) {
      const diffMs = baslangicTarihi - now;
      const diffMinutes = Math.floor(diffMs / 60000);
      const gun = Math.floor(diffMinutes / (60 * 24));
      const saat = Math.floor((diffMinutes % (60 * 24)) / 60);
      const dakika = diffMinutes % 60;
      geriSayimHtml = `<p><strong><?= t('Kalan Süre:', 'Time Left:') ?></strong> ${gun}${'<?= t('g', 'd') ?>'} ${saat}${'<?= t('s', 'h') ?>'} ${dakika}${'<?= t('d', 'm') ?>'}</p>`;
    }
    
    return `
      <div class="etkinlik-card" data-type="${etkinlik.etkinlik_turu}">
        <h3>
          <span class="etkinlik-type type-${etkinlik.etkinlik_turu}">${etkinlik.etkinlik_turu.toUpperCase()}</span>
          ${etkinlik.baslik}
        </h3>
        <div class="etkinlik-info">
          <p><strong>Başlangıç:</strong> <span class="etkinlik-date">${formatTarih(baslangicTarihi)}</span></p>
          ${bitisTarihi ? `<p><strong>Bitiş:</strong> <span class="etkinlik-date">${formatTarih(bitisTarihi)}</span></p>` : ''}
          ${etkinlik.konum ? `<p><strong>Konum:</strong> ${etkinlik.konum}</p>` : ''}
          ${geriSayimHtml}
          ${etkinlik.aciklama ? `<p><strong>Açıklama:</strong> ${etkinlik.aciklama}</p>` : ''}
        </div>
        <div style="margin-top: 10px; font-size: 12px; color: #999;">
          <span>🔑 ID: ${etkinlik.id}</span> | 
          <span>${etkinlik.aktif ? 'Aktif' : 'Pasif'}</span>
        </div>
      </div>
    `;
  }).join('');
}

// Kullanıcının "katılacağım" dediği kulüp etkinliklerini çek
async function fetchKatilacagimEtkinlikler() {
  const container = document.getElementById('katilacagim-container');
  container.innerHTML = '<p class="loading">⏳ Kulüp etkinliklerin yükleniyor...</p>';

  try {
    const response = await fetch(`${ONERI_API_URL}/kullanici-tercihleri/${CURRENT_USER_ID}`);
    const data = await response.json();

    if (!data.success || !data.data || data.data.length === 0) {
      container.innerHTML = '<p style="color:#999;"><?= t('Henüz katılacağım olarak işaretlediğin bir kulüp etkinliği yok.', 'You have no club events marked as I will attend yet.') ?></p>';
      return;
    }

    const now = new Date();
    const gelecektekiler = data.data.filter(item => {
      const t = new Date(item.etkinlik.tarih);
      return item.durum === 'katilacak' && t > now;
    });

    if (gelecektekiler.length === 0) {
      container.innerHTML = '<p style="color:#999;"><?= t('Gelecekte tarihli katılacağın kulüp etkinliği bulunmuyor.', 'There are no upcoming club events you will attend.') ?></p>';
      return;
    }

    container.innerHTML = '';
    gelecektekiler.forEach(item => {
      const t = new Date(item.etkinlik.tarih);
      const diffMs = t - now;
      const diffMinutes = Math.floor(diffMs / 60000);
      const gun = Math.floor(diffMinutes / (60 * 24));
      const saat = Math.floor((diffMinutes % (60 * 24)) / 60);
      const dakika = diffMinutes % 60;

      const div = document.createElement('div');
      div.className = 'katilacagim-card';
      div.innerHTML = `
        <div>
          <div class="katilacagim-baslik">${item.etkinlik.etkinlik_adi}</div>
          <div class="katilacagim-detay">
            ${t.toLocaleString('tr-TR')} | ${item.etkinlik.kulup_adi || '<?= t('Kulüp Etkinliği', 'Club Event') ?>'}
          </div>
        </div>
        <div class="katilacagim-geri-sayim">
          <?= t('Kalan:', 'Remaining:') ?> ${gun}${'<?= t('g', 'd') ?>'} ${saat}${'<?= t('s', 'h') ?>'} ${dakika}${'<?= t('d', 'm') ?>'}
        </div>
      `;
      container.appendChild(div);
    });
  } catch (e) {
    console.error('Katılacağım etkinlikler yüklenemedi:', e);
    container.innerHTML = '<p style="color:#c41e3a;">Kulüp etkinliklerin yüklenirken bir hata oluştu.</p>';
  }
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

</body>
</html>


