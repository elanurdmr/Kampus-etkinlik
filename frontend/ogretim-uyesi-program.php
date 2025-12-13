<?php
session_start();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Öğretim Üyesi Programları | Kampüs Sistemi</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .program-container {
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
    
    .search-card {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }
    
    .search-box {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }
    
    .search-input {
      flex: 1;
      min-width: 300px;
      padding: 15px;
      border: 2px solid #ddd;
      border-radius: 10px;
      font-size: 1.1em;
      transition: border 0.3s;
    }
    
    .search-input:focus {
      outline: none;
      border-color: #b30000;
    }
    
    .ogretim-uyeleri-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
      gap: 20px;
    }
    
    .ogretim-uyesi-card {
      background: white;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: all 0.3s;
      cursor: pointer;
      border: 2px solid transparent;
    }
    
    .ogretim-uyesi-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
      border-color: #b30000;
    }
    
    .card-header {
      display: flex;
      align-items: start;
      gap: 15px;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 2px solid #f0f0f0;
    }
    
    .avatar {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: linear-gradient(135deg, #b30000, #8b0000);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.5em;
      font-weight: bold;
    }
    
    .card-info {
      flex: 1;
    }
    
    .card-info h3 {
      margin: 0 0 5px 0;
      color: #b30000;
      font-size: 1.3em;
    }
    
    .card-info .bolum {
      color: #666;
      font-size: 0.95em;
    }
    
    .contact-info {
      margin: 15px 0;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    
    .contact-row {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 0.9em;
      color: #666;
    }
    
    .program-preview {
      background: #f8f9fa;
      padding: 15px;
      border-radius: 8px;
      margin-top: 15px;
    }
    
    .program-preview h4 {
      margin: 0 0 10px 0;
      font-size: 1em;
      color: #333;
    }
    
    .program-day {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px solid #e0e0e0;
    }
    
    .program-day:last-child {
      border-bottom: none;
    }
    
    .day-name {
      font-weight: 600;
      color: #333;
    }
    
    .day-hours {
      color: #b30000;
      font-weight: 600;
    }
    
    .day-closed {
      color: #999;
      font-style: italic;
    }
    
    /* Modal */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0,0,0,0.7);
      z-index: 1000;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    
    .modal.active {
      display: flex;
    }
    
    .modal-content {
      background: white;
      border-radius: 15px;
      max-width: 900px;
      width: 100%;
      max-height: 90vh;
      overflow-y: auto;
      position: relative;
      animation: slideUp 0.3s ease;
    }
    
    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(50px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .modal-header {
      background: linear-gradient(135deg, #b30000, #8b0000);
      color: white;
      padding: 30px;
      border-radius: 15px 15px 0 0;
      position: relative;
    }
    
    .modal-close {
      position: absolute;
      top: 15px;
      right: 15px;
      background: rgba(255,255,255,0.2);
      border: none;
      color: white;
      font-size: 1.5em;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .modal-close:hover {
      background: rgba(255,255,255,0.3);
      transform: rotate(90deg);
    }
    
    .modal-body {
      padding: 30px;
    }
    
    .info-section {
      margin-bottom: 30px;
    }
    
    .info-section h3 {
      color: #b30000;
      margin-bottom: 15px;
      font-size: 1.3em;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
    }
    
    .info-item {
      background: #f8f9fa;
      padding: 15px;
      border-radius: 8px;
      border-left: 4px solid #b30000;
    }
    
    .info-label {
      font-size: 0.85em;
      color: #666;
      margin-bottom: 5px;
    }
    
    .info-value {
      font-weight: 600;
      color: #333;
      font-size: 1.05em;
    }
    
    .week-schedule {
      display: grid;
      gap: 15px;
    }
    
    .schedule-day {
      background: #f8f9fa;
      border-radius: 10px;
      padding: 20px;
      display: flex;
      align-items: center;
      gap: 20px;
      transition: all 0.3s;
    }
    
    .schedule-day:hover {
      background: #fff5f5;
      box-shadow: 0 2px 8px rgba(179,0,0,0.1);
    }
    
    .schedule-day.closed {
      opacity: 0.6;
    }
    
    .day-badge {
      background: linear-gradient(135deg, #b30000, #8b0000);
      color: white;
      padding: 15px 20px;
      border-radius: 10px;
      font-weight: bold;
      min-width: 100px;
      text-align: center;
      font-size: 1.1em;
    }
    
    .day-badge.closed {
      background: #6c757d;
    }
    
    .schedule-time {
      flex: 1;
      font-size: 1.3em;
      font-weight: 600;
      color: #b30000;
    }
    
    .schedule-time.closed {
      color: #999;
      font-style: italic;
    }
    
    .action-buttons {
      display: flex;
      gap: 15px;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 2px solid #f0f0f0;
    }
    
    .btn {
      padding: 12px 30px;
      border: none;
      border-radius: 8px;
      font-size: 1em;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      flex: 1;
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
    
    @media (max-width: 768px) {
      .ogretim-uyeleri-grid {
        grid-template-columns: 1fr;
      }
      
      .search-input {
        min-width: 100%;
      }
      
      .schedule-day {
        flex-direction: column;
        text-align: center;
      }
      
      .day-badge {
        width: 100%;
      }
    }
  </style>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="program-container">
  <div class="page-header">
    <h2>
      <span>🗓️</span>
      Öğretim Üyeleri Çalışma Programları
    </h2>
    <p>Öğretim üyelerinin haftalık çalışma saatlerini görüntüleyin</p>
  </div>

  <div class="search-card">
    <div class="search-box">
      <input 
        type="text" 
        class="search-input" 
        id="searchInput" 
        placeholder="🔍 Öğretim üyesi ara (isim, bölüm veya unvan)..."
        onkeyup="aramaYap()"
      >
    </div>
  </div>

  <div id="ogretimUyeleriContainer" class="ogretim-uyeleri-grid"></div>
</div>

<!-- Modal -->
<div id="programModal" class="modal" onclick="modalKapat(event)">
  <div class="modal-content" onclick="event.stopPropagation()">
    <div class="modal-header">
      <button class="modal-close" onclick="modalKapat()">×</button>
      <div style="display: flex; align-items: center; gap: 20px;">
        <div class="avatar" id="modalAvatar"></div>
        <div>
          <h2 id="modalBaslik" style="margin: 0 0 5px 0;"></h2>
          <p id="modalBolum" style="margin: 0; opacity: 0.9;"></p>
        </div>
      </div>
    </div>
    
    <div class="modal-body">
      <div class="info-section">
        <h3>📋 İletişim Bilgileri</h3>
        <div class="info-grid" id="modalIletisim"></div>
      </div>
      
      <div class="info-section">
        <h3>📅 Haftalık Çalışma Programı</h3>
        <div class="week-schedule" id="modalProgram"></div>
      </div>
      
      <div class="action-buttons">
        <button class="btn btn-primary" onclick="randevuSayfasinaGit()">
          📅 Randevu Al
        </button>
      </div>
    </div>
  </div>
</div>

<script>
const API_BASE_URL = 'http://localhost:8000/api/randevu';
let ogretimUyeleri = [];
let selectedOgretimUyesi = null;

const gunler = {
  'pazartesi': 'Pazartesi',
  'sali': 'Salı',
  'carsamba': 'Çarşamba',
  'persembe': 'Perşembe',
  'cuma': 'Cuma',
  'cumartesi': 'Cumartesi',
  'pazar': 'Pazar'
};

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
  ogretimUyeleriniYukle();
});

// Öğretim üyelerini yükle
async function ogretimUyeleriniYukle() {
  try {
    const response = await fetch(`${API_BASE_URL}/ogretim-uyeleri?aktif_mi=true`);
    if (!response.ok) throw new Error('Yükleme hatası');
    
    ogretimUyeleri = await response.json();
    
    // Örnek çalışma saatleri ekle (ilk 5 için)
    ogretimUyeleri = ogretimUyeleri.map((uyesi, index) => {
      if (!uyesi.calisma_saatleri && index < 25) {
        // Her öğretim üyesine farklı program
        const programs = [
          '{"pazartesi":"09:00-17:00","sali":"09:00-17:00","carsamba":"10:00-16:00","persembe":"09:00-17:00","cuma":"09:00-15:00"}',
          '{"pazartesi":"10:00-18:00","sali":"10:00-18:00","carsamba":"09:00-17:00","persembe":"10:00-18:00","cuma":"10:00-14:00"}',
          '{"pazartesi":"08:00-16:00","sali":"08:00-16:00","carsamba":"08:00-16:00","persembe":"08:00-16:00","cuma":"08:00-12:00"}',
          '{"pazartesi":"09:00-17:00","carsamba":"09:00-17:00","cuma":"09:00-17:00"}',
          '{"sali":"10:00-18:00","persembe":"10:00-18:00","cuma":"13:00-17:00"}'
        ];
        uyesi.calisma_saatleri = programs[index % programs.length];
      }
      return uyesi;
    });
    
    ogretimUyeleriniGoster(ogretimUyeleri);
  } catch (error) {
    console.error('Hata:', error);
    document.getElementById('ogretimUyeleriContainer').innerHTML = `
      <div class="empty-state" style="grid-column: 1/-1;">
        <div class="empty-state-icon">⚠️</div>
        <h3>Yükleme Hatası</h3>
        <p>Öğretim üyeleri yüklenirken bir hata oluştu</p>
      </div>
    `;
  }
}

// Öğretim üyelerini göster
function ogretimUyeleriniGoster(liste) {
  const container = document.getElementById('ogretimUyeleriContainer');
  
  if (liste.length === 0) {
    container.innerHTML = `
      <div class="empty-state" style="grid-column: 1/-1;">
        <div class="empty-state-icon">🔍</div>
        <h3>Sonuç Bulunamadı</h3>
        <p>Arama kriterlerinize uygun öğretim üyesi bulunamadı</p>
      </div>
    `;
    return;
  }
  
  let html = '';
  liste.forEach(uyesi => {
    const initials = (uyesi.ad[0] + uyesi.soyad[0]).toUpperCase();
    const program = parseCalismaSaatleri(uyesi.calisma_saatleri);
    const toplamGun = Object.keys(program).filter(gun => program[gun]).length;
    
    html += `
      <div class="ogretim-uyesi-card" onclick='modalAc(${JSON.stringify(uyesi).replace(/'/g, "\\'")} )'>
        <div class="card-header">
          <div class="avatar">${initials}</div>
          <div class="card-info">
            <h3>${uyesi.unvan || ''} ${uyesi.ad} ${uyesi.soyad}</h3>
            <div class="bolum">${uyesi.bolum || 'Bölüm belirtilmemiş'}</div>
          </div>
        </div>
        
        <div class="contact-info">
          <div class="contact-row">
            <span>📧</span>
            <span>${uyesi.email}</span>
          </div>
          ${uyesi.telefon ? `
            <div class="contact-row">
              <span>📞</span>
              <span>${uyesi.telefon}</span>
            </div>
          ` : ''}
          ${uyesi.ofis_no ? `
            <div class="contact-row">
              <span>🚪</span>
              <span>Ofis: ${uyesi.ofis_no}</span>
            </div>
          ` : ''}
        </div>
        
        <div class="program-preview">
          <h4>📅 Çalışma Günleri: ${toplamGun > 0 ? toplamGun + ' gün' : 'Program yok'}</h4>
          ${programOnizleme(program)}
        </div>
      </div>
    `;
  });
  
  container.innerHTML = html;
}

// Çalışma saatlerini parse et
function parseCalismaSaatleri(calisma_saatleri) {
  if (!calisma_saatleri) return {};
  
  try {
    return JSON.parse(calisma_saatleri);
  } catch {
    return {};
  }
}

// Program önizleme
function programOnizleme(program) {
  if (Object.keys(program).length === 0) {
    return '<div class="program-day"><span class="day-closed">Program belirlenmemiş</span></div>';
  }
  
  let html = '';
  let count = 0;
  const maxShow = 3;
  
  Object.keys(gunler).forEach(gun => {
    if (count < maxShow) {
      html += `
        <div class="program-day">
          <span class="day-name">${gunler[gun]}</span>
          <span class="${program[gun] ? 'day-hours' : 'day-closed'}">
            ${program[gun] || 'Kapalı'}
          </span>
        </div>
      `;
      count++;
    }
  });
  
  if (Object.keys(program).length > maxShow) {
    html += '<div style="text-align: center; color: #999; font-size: 0.9em; margin-top: 10px;">Detaylar için tıklayın...</div>';
  }
  
  return html;
}

// Modal aç
function modalAc(uyesi) {
  selectedOgretimUyesi = uyesi;
  
  const initials = (uyesi.ad[0] + uyesi.soyad[0]).toUpperCase();
  const program = parseCalismaSaatleri(uyesi.calisma_saatleri);
  
  document.getElementById('modalAvatar').textContent = initials;
  document.getElementById('modalBaslik').textContent = `${uyesi.unvan || ''} ${uyesi.ad} ${uyesi.soyad}`;
  document.getElementById('modalBolum').textContent = uyesi.bolum || 'Bölüm belirtilmemiş';
  
  // İletişim bilgileri
  let iletisimHtml = `
    <div class="info-item">
      <div class="info-label">Email</div>
      <div class="info-value">${uyesi.email}</div>
    </div>
  `;
  
  if (uyesi.telefon) {
    iletisimHtml += `
      <div class="info-item">
        <div class="info-label">Telefon</div>
        <div class="info-value">${uyesi.telefon}</div>
      </div>
    `;
  }
  
  if (uyesi.ofis_no) {
    iletisimHtml += `
      <div class="info-item">
        <div class="info-label">Ofis</div>
        <div class="info-value">${uyesi.ofis_no}</div>
      </div>
    `;
  }
  
  document.getElementById('modalIletisim').innerHTML = iletisimHtml;
  
  // Haftalık program
  let programHtml = '';
  Object.keys(gunler).forEach(gun => {
    const saatler = program[gun];
    const isClosed = !saatler;
    
    programHtml += `
      <div class="schedule-day ${isClosed ? 'closed' : ''}">
        <div class="day-badge ${isClosed ? 'closed' : ''}">${gunler[gun]}</div>
        <div class="schedule-time ${isClosed ? 'closed' : ''}">
          ${saatler || 'Kapalı'}
        </div>
      </div>
    `;
  });
  
  document.getElementById('modalProgram').innerHTML = programHtml;
  document.getElementById('programModal').classList.add('active');
  document.body.style.overflow = 'hidden';
}

// Modal kapat
function modalKapat(event) {
  if (!event || event.target.id === 'programModal' || event.target.classList.contains('modal-close')) {
    document.getElementById('programModal').classList.remove('active');
    document.body.style.overflow = 'auto';
    selectedOgretimUyesi = null;
  }
}

// Randevu sayfasına git
function randevuSayfasinaGit() {
  window.location.href = 'randevu-olustur.php';
}

// Arama yap
function aramaYap() {
  const arama = document.getElementById('searchInput').value.toLowerCase();
  
  if (!arama) {
    ogretimUyeleriniGoster(ogretimUyeleri);
    return;
  }
  
  const filtrelenmis = ogretimUyeleri.filter(uyesi => {
    const ad = (uyesi.ad + ' ' + uyesi.soyad).toLowerCase();
    const unvan = (uyesi.unvan || '').toLowerCase();
    const bolum = (uyesi.bolum || '').toLowerCase();
    
    return ad.includes(arama) || unvan.includes(arama) || bolum.includes(arama);
  });
  
  ogretimUyeleriniGoster(filtrelenmis);
}
</script>

<?php include "footer.php"; ?>

</body>
</html>

