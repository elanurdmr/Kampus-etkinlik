<?php
session_start();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Öğretim Üyesi Takvimi | Kampüs Sistemi</title>
  <link rel="stylesheet" href="style.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    .takvim-container {
      max-width: 1400px;
      margin: 40px auto;
      padding: 20px;
    }
    
    .page-header {
      background: linear-gradient(135deg, #b30000 0%, #8b0000 100%);
      color: white;
      padding: 30px;
      border-radius: 15px;
      margin-bottom: 30px;
      box-shadow: 0 8px 20px rgba(179,0,0,0.3);
    }
    
    .page-header h2 {
      margin: 0 0 10px 0;
      font-size: 2em;
      display: flex;
      align-items: center;
      gap: 15px;
    }
    
    .page-header p {
      opacity: 0.95;
      font-size: 1.1em;
    }
    
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    
    .stat-card {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      text-align: center;
      transition: transform 0.3s;
    }
    
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .stat-number {
      font-size: 2.5em;
      font-weight: bold;
      color: #b30000;
      margin-bottom: 10px;
    }
    
    .stat-label {
      color: #666;
      font-size: 1em;
    }
    
    .filters-card {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }
    
    .filters-card h3 {
      margin-bottom: 20px;
      color: #b30000;
      font-size: 1.3em;
    }
    
    .filter-group {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      align-items: end;
    }
    
    .form-group {
      display: flex;
      flex-direction: column;
    }
    
    .form-group label {
      margin-bottom: 8px;
      font-weight: 600;
      color: #333;
    }
    
    .form-group select,
    .form-group input {
      padding: 12px;
      border: 2px solid #ddd;
      border-radius: 8px;
      font-size: 1em;
      transition: border 0.3s;
    }
    
    .form-group select:focus,
    .form-group input:focus {
      outline: none;
      border-color: #b30000;
    }
    
    .btn {
      padding: 12px 30px;
      border: none;
      border-radius: 8px;
      font-size: 1em;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .btn-primary {
      background: #b30000;
      color: white;
    }
    
    .btn-primary:hover {
      background: #8b0000;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(179,0,0,0.3);
    }
    
    .view-toggle {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
    }
    
    .toggle-btn {
      padding: 10px 20px;
      border: 2px solid #ddd;
      background: white;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s;
      font-weight: 600;
    }
    
    .toggle-btn.active {
      background: #b30000;
      color: white;
      border-color: #b30000;
    }
    
    .toggle-btn:hover:not(.active) {
      border-color: #b30000;
      color: #b30000;
    }
    
    .randevu-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }
    
    .randevu-card {
      background: white;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      border-left: 5px solid #b30000;
      transition: all 0.3s;
    }
    
    .randevu-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    }
    
    .randevu-header {
      display: flex;
      justify-content: space-between;
      align-items: start;
      margin-bottom: 15px;
      gap: 10px;
    }
    
    .randevu-header h4 {
      color: #b30000;
      font-size: 1.2em;
      flex: 1;
    }
    
    .durum-badge {
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.85em;
      font-weight: 600;
      white-space: nowrap;
    }
    
    .durum-bekliyor {
      background: #fff3cd;
      color: #856404;
    }
    
    .durum-onaylandi {
      background: #d4edda;
      color: #155724;
    }
    
    .durum-reddedildi {
      background: #f8d7da;
      color: #721c24;
    }
    
    .durum-tamamlandi {
      background: #d1ecf1;
      color: #0c5460;
    }
    
    .durum-iptal_edildi {
      background: #e2e3e5;
      color: #383d41;
    }
    
    .randevu-info {
      margin: 15px 0;
    }
    
    .info-row {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 10px 0;
      padding: 8px;
      background: #f8f9fa;
      border-radius: 6px;
    }
    
    .info-icon {
      font-size: 1.2em;
    }
    
    .info-text {
      flex: 1;
    }
    
    .info-label {
      font-size: 0.85em;
      color: #666;
      display: block;
    }
    
    .info-value {
      font-weight: 600;
      color: #333;
      font-size: 1em;
    }
    
    .randevu-actions {
      display: flex;
      gap: 10px;
      margin-top: 20px;
      padding-top: 15px;
      border-top: 1px solid #eee;
    }
    
    .btn-small {
      padding: 8px 16px;
      font-size: 0.9em;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.3s;
      font-weight: 600;
    }
    
    .btn-success {
      background: #28a745;
      color: white;
    }
    
    .btn-success:hover {
      background: #218838;
      transform: scale(1.05);
    }
    
    .btn-danger {
      background: #dc3545;
      color: white;
    }
    
    .btn-danger:hover {
      background: #c82333;
      transform: scale(1.05);
    }
    
    .btn-secondary {
      background: #6c757d;
      color: white;
    }
    
    .btn-secondary:hover {
      background: #5a6268;
      transform: scale(1.05);
    }
    
    .empty-state {
      text-align: center;
      padding: 80px 20px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .empty-state-icon {
      font-size: 4em;
      margin-bottom: 20px;
    }
    
    .empty-state h3 {
      color: #666;
      margin-bottom: 10px;
      font-size: 1.5em;
    }
    
    .empty-state p {
      color: #999;
      font-size: 1.1em;
    }
    
    .loading {
      text-align: center;
      padding: 60px;
      background: white;
      border-radius: 12px;
    }
    
    .spinner {
      border: 4px solid #f3f3f3;
      border-top: 4px solid #b30000;
      border-radius: 50%;
      width: 50px;
      height: 50px;
      animation: spin 1s linear infinite;
      margin: 0 auto 20px;
    }
    
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    
    .timeline-view {
      display: none;
    }
    
    .timeline-view.active {
      display: block;
    }
    
    .timeline {
      position: relative;
      padding: 20px 0;
    }
    
    .timeline::before {
      content: '';
      position: absolute;
      left: 30px;
      top: 0;
      bottom: 0;
      width: 3px;
      background: linear-gradient(to bottom, #b30000, #8b0000);
    }
    
    .timeline-item {
      position: relative;
      padding-left: 70px;
      margin-bottom: 30px;
    }
    
    .timeline-dot {
      position: absolute;
      left: 20px;
      width: 24px;
      height: 24px;
      border-radius: 50%;
      background: white;
      border: 4px solid #b30000;
      box-shadow: 0 0 0 4px #fff5f5;
    }
    
    .timeline-date {
      font-weight: bold;
      color: #b30000;
      margin-bottom: 10px;
      font-size: 1.1em;
    }
    
    @media (max-width: 768px) {
      .randevu-grid {
        grid-template-columns: 1fr;
      }
      
      .filter-group {
        grid-template-columns: 1fr;
      }
      
      .stats-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }
  </style>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="takvim-container">
  <div class="page-header">
    <h2>
      <span>📅</span>
      Öğretim Üyesi Randevu Takvimi
    </h2>
    <p>Öğretim üyelerinin randevu takvimlerini görüntüleyin ve yönetin</p>
  </div>

  <div class="stats-grid" id="statsGrid">
    <div class="stat-card">
      <div class="stat-number" id="statToplam">0</div>
      <div class="stat-label">Toplam Randevu</div>
    </div>
    <div class="stat-card">
      <div class="stat-number" id="statBekleyen">0</div>
      <div class="stat-label">Bekleyen</div>
    </div>
    <div class="stat-card">
      <div class="stat-number" id="statOnaylanan">0</div>
      <div class="stat-label">Onaylanan</div>
    </div>
    <div class="stat-card">
      <div class="stat-number" id="statTamamlanan">0</div>
      <div class="stat-label">Tamamlanan</div>
    </div>
  </div>

  <div class="filters-card">
    <h3>🔍 Filtreler</h3>
    <div class="filter-group">
      <div class="form-group">
        <label for="ogretimUyesiSelect">Öğretim Üyesi</label>
        <select id="ogretimUyesiSelect">
          <option value="">Öğretim üyesi seçin...</option>
        </select>
      </div>
      
      <div class="form-group">
        <label for="durumFilter">Durum</label>
        <select id="durumFilter">
          <option value="">Tümü</option>
          <option value="bekliyor">Bekliyor</option>
          <option value="onaylandi">Onaylandı</option>
          <option value="reddedildi">Reddedildi</option>
          <option value="tamamlandi">Tamamlandı</option>
        </select>
      </div>
      
      <div class="form-group">
        <label for="baslangicTarihi">Başlangıç</label>
        <input type="date" id="baslangicTarihi">
      </div>
      
      <div class="form-group">
        <label for="bitisTarihi">Bitiş</label>
        <input type="date" id="bitisTarihi">
      </div>
      
      <div class="form-group">
        <label style="opacity: 0;">Ara</label>
        <button class="btn btn-primary" onclick="takvimiYukle()">🔍 Ara</button>
      </div>
    </div>
  </div>

  <div class="view-toggle" id="viewToggle" style="display: none;">
    <button class="toggle-btn active" onclick="changeView('grid')">📊 Grid Görünüm</button>
    <button class="toggle-btn" onclick="changeView('timeline')">📅 Zaman Çizelgesi</button>
  </div>

  <div id="loadingState" class="loading">
    <div class="spinner"></div>
    <p>Yükleniyor...</p>
  </div>

  <div id="takvimContent" style="display: none;">
    <div id="gridView" class="randevu-grid"></div>
    <div id="timelineView" class="timeline-view"></div>
  </div>
</div>

<script>
const API_BASE_URL = 'http://localhost:8000/api/randevu';
let currentView = 'grid';
let allRandevular = [];

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
  ogretimUyeleriniYukle();
  
  // Tarih input'larına varsayılan değerler
  const bugun = new Date();
  const gelecek = new Date(bugun);
  gelecek.setDate(gelecek.getDate() + 30);
  
  document.getElementById('baslangicTarihi').value = bugun.toISOString().split('T')[0];
  document.getElementById('bitisTarihi').value = gelecek.toISOString().split('T')[0];
});

// Öğretim üyelerini yükle
async function ogretimUyeleriniYukle() {
  try {
    const response = await fetch(`${API_BASE_URL}/ogretim-uyeleri?aktif_mi=true`);
    if (!response.ok) throw new Error('Öğretim üyeleri yüklenemedi');
    
    const ogretimUyeleri = await response.json();
    const select = document.getElementById('ogretimUyesiSelect');
    
    ogretimUyeleri.forEach(uyesi => {
      const option = document.createElement('option');
      option.value = uyesi.id;
      option.textContent = `${uyesi.unvan || ''} ${uyesi.ad} ${uyesi.soyad} - ${uyesi.bolum || ''}`;
      select.appendChild(option);
    });
    
    document.getElementById('loadingState').style.display = 'none';
  } catch (error) {
    console.error('Hata:', error);
    document.getElementById('loadingState').innerHTML = 
      '<div class="empty-state"><h3>⚠️ Hata</h3><p>Öğretim üyeleri yüklenemedi</p></div>';
  }
}

// Takvimi yükle
async function takvimiYukle() {
  const ogretimUyesiId = document.getElementById('ogretimUyesiSelect').value;
  const durum = document.getElementById('durumFilter').value;
  const baslangicTarihi = document.getElementById('baslangicTarihi').value;
  const bitisTarihi = document.getElementById('bitisTarihi').value;
  
  if (!ogretimUyesiId) {
    alert('Lütfen bir öğretim üyesi seçin');
    return;
  }
  
  document.getElementById('loadingState').style.display = 'block';
  document.getElementById('takvimContent').style.display = 'none';
  document.getElementById('viewToggle').style.display = 'none';
  
  try {
    let url = `${API_BASE_URL}/ogretim-uyesi/${ogretimUyesiId}/takvim`;
    if (baslangicTarihi) url += `?baslangic_tarihi=${baslangicTarihi}`;
    if (bitisTarihi) {
      url += baslangicTarihi ? `&bitis_tarihi=${bitisTarihi}` : `?bitis_tarihi=${bitisTarihi}`;
    }
    
    const response = await fetch(url);
    if (!response.ok) throw new Error('Takvim yüklenemedi');
    
    const data = await response.json();
    allRandevular = data.randevular;
    
    // Durum filtreleme
    if (durum) {
      allRandevular = allRandevular.filter(r => r.durum === durum);
    }
    
    // İstatistikleri güncelle
    updateStats(allRandevular);
    
    // Görünümü göster
    document.getElementById('loadingState').style.display = 'none';
    document.getElementById('takvimContent').style.display = 'block';
    document.getElementById('viewToggle').style.display = 'flex';
    
    // Mevcut görünümü göster
    if (currentView === 'grid') {
      showGridView(allRandevular);
    } else {
      showTimelineView(allRandevular);
    }
    
  } catch (error) {
    console.error('Hata:', error);
    document.getElementById('loadingState').innerHTML = 
      '<div class="empty-state"><h3>⚠️ Hata</h3><p>Takvim yüklenirken bir hata oluştu</p></div>';
  }
}

// İstatistikleri güncelle
function updateStats(randevular) {
  const stats = {
    toplam: randevular.length,
    bekleyen: randevular.filter(r => r.durum === 'bekliyor').length,
    onaylanan: randevular.filter(r => r.durum === 'onaylandi').length,
    tamamlanan: randevular.filter(r => r.durum === 'tamamlandi').length
  };
  
  document.getElementById('statToplam').textContent = stats.toplam;
  document.getElementById('statBekleyen').textContent = stats.bekleyen;
  document.getElementById('statOnaylanan').textContent = stats.onaylanan;
  document.getElementById('statTamamlanan').textContent = stats.tamamlanan;
}

// Grid görünümü
function showGridView(randevular) {
  const container = document.getElementById('gridView');
  
  if (randevular.length === 0) {
    container.innerHTML = `
      <div class="empty-state" style="grid-column: 1/-1;">
        <div class="empty-state-icon">📭</div>
        <h3>Randevu Bulunamadı</h3>
        <p>Seçilen kriterlere uygun randevu bulunmuyor</p>
      </div>
    `;
    return;
  }
  
  let html = '';
  randevular.forEach(randevu => {
    const durumClass = `durum-${randevu.durum}`;
    const durumText = {
      'bekliyor': 'Bekliyor',
      'onaylandi': 'Onaylandı',
      'reddedildi': 'Reddedildi',
      'tamamlandi': 'Tamamlandı',
      'iptal_edildi': 'İptal Edildi'
    }[randevu.durum] || randevu.durum;
    
    const tarih = new Date(randevu.randevu_tarihi).toLocaleDateString('tr-TR', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
    
    const saat = randevu.randevu_saati.substring(0, 5);
    
    html += `
      <div class="randevu-card">
        <div class="randevu-header">
          <h4>${randevu.konu}</h4>
          <span class="durum-badge ${durumClass}">${durumText}</span>
        </div>
        
        <div class="randevu-info">
          <div class="info-row">
            <span class="info-icon">👤</span>
            <div class="info-text">
              <span class="info-label">Öğrenci</span>
              <span class="info-value">${randevu.ogrenci_adi}</span>
            </div>
          </div>
          
          <div class="info-row">
            <span class="info-icon">📅</span>
            <div class="info-text">
              <span class="info-label">Tarih</span>
              <span class="info-value">${tarih}</span>
            </div>
          </div>
          
          <div class="info-row">
            <span class="info-icon">⏰</span>
            <div class="info-text">
              <span class="info-label">Saat</span>
              <span class="info-value">${saat}</span>
            </div>
          </div>
          
          ${randevu.ogrenci_no ? `
            <div class="info-row">
              <span class="info-icon">🎓</span>
              <div class="info-text">
                <span class="info-label">Öğrenci No</span>
                <span class="info-value">${randevu.ogrenci_no}</span>
              </div>
            </div>
          ` : ''}
          
          ${randevu.aciklama ? `
            <div class="info-row">
              <span class="info-icon">📝</span>
              <div class="info-text">
                <span class="info-label">Açıklama</span>
                <span class="info-value">${randevu.aciklama}</span>
              </div>
            </div>
          ` : ''}
        </div>
        
        <div class="randevu-actions">
          ${randevu.durum === 'bekliyor' ? `
            <button class="btn-small btn-success" onclick="randevuDurumGuncelle(${randevu.id}, 'onaylandi')">
              ✓ Onayla
            </button>
            <button class="btn-small btn-danger" onclick="randevuDurumGuncelle(${randevu.id}, 'reddedildi')">
              ✗ Reddet
            </button>
          ` : ''}
          ${randevu.durum === 'onaylandi' ? `
            <button class="btn-small btn-secondary" onclick="randevuDurumGuncelle(${randevu.id}, 'tamamlandi')">
              ✓ Tamamlandı İşaretle
            </button>
          ` : ''}
        </div>
      </div>
    `;
  });
  
  container.innerHTML = html;
}

// Zaman çizelgesi görünümü
function showTimelineView(randevular) {
  const container = document.getElementById('timelineView');
  
  if (randevular.length === 0) {
    container.innerHTML = `
      <div class="empty-state">
        <div class="empty-state-icon">📭</div>
        <h3>Randevu Bulunamadı</h3>
        <p>Seçilen kriterlere uygun randevu bulunmuyor</p>
      </div>
    `;
    return;
  }
  
  // Tarihe göre grupla
  const groupedByDate = {};
  randevular.forEach(r => {
    const date = r.randevu_tarihi;
    if (!groupedByDate[date]) {
      groupedByDate[date] = [];
    }
    groupedByDate[date].push(r);
  });
  
  let html = '<div class="timeline">';
  
  Object.keys(groupedByDate).sort().forEach(date => {
    const randevularGun = groupedByDate[date];
    const tarih = new Date(date).toLocaleDateString('tr-TR', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
    
    html += `
      <div class="timeline-item">
        <div class="timeline-dot"></div>
        <div class="timeline-date">${tarih}</div>
        <div class="randevu-grid" style="grid-template-columns: 1fr;">
    `;
    
    randevularGun.sort((a, b) => a.randevu_saati.localeCompare(b.randevu_saati)).forEach(randevu => {
      const durumClass = `durum-${randevu.durum}`;
      const durumText = {
        'bekliyor': 'Bekliyor',
        'onaylandi': 'Onaylandı',
        'reddedildi': 'Reddedildi',
        'tamamlandi': 'Tamamlandı'
      }[randevu.durum] || randevu.durum;
      
      const saat = randevu.randevu_saati.substring(0, 5);
      
      html += `
        <div class="randevu-card" style="margin-bottom: 15px;">
          <div class="randevu-header">
            <h4>${saat} - ${randevu.konu}</h4>
            <span class="durum-badge ${durumClass}">${durumText}</span>
          </div>
          <p style="color: #666; margin: 10px 0;">
            <strong>Öğrenci:</strong> ${randevu.ogrenci_adi} ${randevu.ogrenci_no ? '(' + randevu.ogrenci_no + ')' : ''}
          </p>
          ${randevu.aciklama ? `<p style="color: #999; font-size: 0.9em;">${randevu.aciklama}</p>` : ''}
        </div>
      `;
    });
    
    html += `
        </div>
      </div>
    `;
  });
  
  html += '</div>';
  container.innerHTML = html;
}

// Görünüm değiştir
function changeView(view) {
  currentView = view;
  
  // Toggle butonlarını güncelle
  document.querySelectorAll('.toggle-btn').forEach(btn => {
    btn.classList.remove('active');
  });
  event.target.classList.add('active');
  
  // Görünümü göster
  if (view === 'grid') {
    document.getElementById('gridView').style.display = 'grid';
    document.getElementById('timelineView').style.display = 'none';
    showGridView(allRandevular);
  } else {
    document.getElementById('gridView').style.display = 'none';
    document.getElementById('timelineView').style.display = 'block';
    showTimelineView(allRandevular);
  }
}

// Randevu durumunu güncelle
async function randevuDurumGuncelle(randevuId, yeniDurum) {
  const durumText = {
    'onaylandi': 'onaylamak',
    'reddedildi': 'reddetmek',
    'tamamlandi': 'tamamlandı olarak işaretlemek'
  }[yeniDurum] || 'güncellemek';
  
  if (!confirm(`Bu randevuyu ${durumText} istediğinizden emin misiniz?`)) {
    return;
  }
  
  try {
    const response = await fetch(`${API_BASE_URL}/randevu/${randevuId}/durum?yeni_durum=${yeniDurum}`, {
      method: 'PUT'
    });
    
    if (response.ok) {
      alert('✓ Randevu durumu başarıyla güncellendi');
      takvimiYukle();
    } else {
      const data = await response.json();
      alert('✗ ' + (data.detail || 'Randevu durumu güncellenemedi'));
    }
  } catch (error) {
    console.error('Hata:', error);
    alert('✗ Bağlantı hatası. Lütfen tekrar deneyin.');
  }
}
</script>

<?php include "footer.php"; ?>

</body>
</html>

