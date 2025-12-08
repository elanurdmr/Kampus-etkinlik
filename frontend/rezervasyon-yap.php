<?php
session_start();
$currentPage = basename($_SERVER['PHP_SELF']);

// Kullanıcı ID'si (gerçek uygulamada session'dan gelecek)
$kullanici_id = $_SESSION['user_id'] ?? 1; // Demo için 1
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kütüphane Rezervasyonu | Kampüs Sistemi</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .rezervasyon-container {
      max-width: 800px;
      margin: 40px auto;
      padding: 20px;
    }
    
    .form-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      padding: 30px;
      margin-bottom: 20px;
    }
    
    .kutuphane-info {
      background: linear-gradient(135deg, #b30000 0%, #8b0000 100%);
      color: white;
      padding: 25px;
      border-radius: 12px;
      margin-bottom: 30px;
      box-shadow: 0 4px 12px rgba(179,0,0,0.3);
    }
    
    .kutuphane-info h2 {
      margin: 0 0 10px 0;
      font-size: 1.8em;
    }
    
    .kutuphane-info p {
      margin: 5px 0;
      opacity: 0.9;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #333;
      font-size: 1.05em;
    }
    
    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 12px;
      border: 2px solid #ddd;
      border-radius: 8px;
      font-size: 1em;
      transition: border 0.3s;
      box-sizing: border-box;
    }
    
    .form-group input:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #b30000;
    }
    
    .form-group textarea {
      min-height: 80px;
      resize: vertical;
    }
    
    .form-hint {
      font-size: 0.9em;
      color: #666;
      margin-top: 5px;
    }
    
    .time-slots {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
      gap: 10px;
      margin-top: 10px;
    }
    
    .time-slot {
      padding: 12px;
      border: 2px solid #ddd;
      border-radius: 8px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s;
      background: white;
    }
    
    .time-slot:hover {
      border-color: #b30000;
      background: #fff5f5;
    }
    
    .time-slot.selected {
      border-color: #b30000;
      background: linear-gradient(135deg, #b30000 0%, #8b0000 100%);
      color: white;
      font-weight: 600;
    }
    
    .time-slot.disabled {
      opacity: 0.5;
      cursor: not-allowed;
      background: #f5f5f5;
    }
    
    .submit-btn {
      width: 100%;
      padding: 15px;
      background: linear-gradient(135deg, #b30000 0%, #8b0000 100%);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1.1em;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      margin-top: 20px;
      box-shadow: 0 4px 10px rgba(179,0,0,0.3);
    }
    
    .submit-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(179,0,0,0.4);
    }
    
    .submit-btn:disabled {
      background: #ccc;
      cursor: not-allowed;
      transform: none;
    }
    
    .success-message {
      background: #e8f5e9;
      color: #2e7d32;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      display: none;
      text-align: center;
    }
    
    .error-message {
      background: #ffebee;
      color: #c62828;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      display: none;
      text-align: center;
    }
    
    .loading {
      text-align: center;
      padding: 40px;
      font-size: 1.2em;
      color: #666;
    }
    
    .musaitlik-info {
      background: #f8f9fa;
      padding: 15px;
      border-radius: 8px;
      margin-top: 15px;
    }
    
    .musaitlik-item {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
    }
    
    .kapasite-bar {
      margin-top: 10px;
    }
    
    .progress-bar {
      height: 8px;
      background: #e0e0e0;
      border-radius: 4px;
      overflow: hidden;
    }
    
    .progress-fill {
      height: 100%;
      background: linear-gradient(90deg, #4caf50 0%, #8bc34a 100%);
      transition: width 0.3s;
    }
    
    .progress-fill.warning {
      background: linear-gradient(90deg, #ff9800 0%, #ffc107 100%);
    }
    
    .progress-fill.danger {
      background: linear-gradient(90deg, #f44336 0%, #e91e63 100%);
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="rezervasyon-container">
  <div style="text-align: right; margin-bottom: 20px;">
    <a href="rezervasyonlarim.php" style="display: inline-block; padding: 10px 20px; background: linear-gradient(135deg, #b30000 0%, #8b0000 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s; box-shadow: 0 2px 6px rgba(179,0,0,0.3);">
      Rezervasyonlarımı Görüntüle
    </a>
  </div>

  <div id="loading" class="loading">
    <p>Kütüphane bilgileri yükleniyor...</p>
  </div>

  <div id="success-message" class="success-message"></div>
  <div id="error-message" class="error-message"></div>

  <div id="kutuphane-info" style="display: none;"></div>
  
  <div id="rezervasyon-form-container" style="display: none;">
    <div class="form-card">
      <h2>Rezervasyon Bilgileri</h2>
      
      <form id="rezervasyon-form">
        <input type="hidden" id="kutuphane_id" value="">
        <input type="hidden" id="kullanici_id" value="<?= htmlspecialchars($kullanici_id) ?>">
        
        <div class="form-group">
          <label for="rezervasyon_tarihi">Rezervasyon Tarihi</label>
          <input 
            type="date" 
            id="rezervasyon_tarihi" 
            required
            min="<?= date('Y-m-d') ?>"
            max="<?= date('Y-m-d', strtotime('+30 days')) ?>"
          >
          <div class="form-hint">Bugünden itibaren 30 gün içinde seçim yapabilirsiniz</div>
        </div>
        
        <div class="form-group">
          <label>Başlangıç Saati</label>
          <div class="time-slots" id="baslangic-saatleri"></div>
        </div>
        
        <div class="form-group">
          <label>Bitiş Saati</label>
          <div class="time-slots" id="bitis-saatleri"></div>
        </div>
        
        <div id="musaitlik-durumu" class="musaitlik-info" style="display: none;">
          <h4 style="margin: 0 0 10px 0;">Müsaitlik Durumu</h4>
          <div class="musaitlik-item">
            <span>Toplam Kapasite:</span>
            <strong><span id="toplam-kapasite">-</span> kişi</strong>
          </div>
          <div class="musaitlik-item">
            <span>Mevcut Rezervasyon:</span>
            <strong><span id="mevcut-rezervasyon">-</span> kişi</strong>
          </div>
          <div class="musaitlik-item">
            <span>Müsait Koltuk:</span>
            <strong style="color: #4caf50;"><span id="musait-koltuk">-</span> kişi</strong>
          </div>
          <div class="kapasite-bar">
            <div class="progress-bar">
              <div class="progress-fill" id="doluluk-bar" style="width: 0%"></div>
            </div>
          </div>
        </div>
        
        <button type="submit" class="submit-btn" id="submit-btn">
          Rezervasyonu Onayla
        </button>
      </form>
    </div>
  </div>
</div>

<script>
const API_URL = 'http://localhost:8000/api/kutuphane';
let kutuphaneId = null;
const kullaniciId = <?= $kullanici_id ?>;

let kutuphane = null;
let selectedBaslangic = null;
let selectedBitis = null;

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
  fetchKutuphaneListesi();
  setupEventListeners();
});

// Event listener'ları ayarla
function setupEventListeners() {
  // Tarih değiştiğinde saat aralıklarını güncelle
  document.getElementById('rezervasyon_tarihi').addEventListener('change', function() {
    generateTimeSlots();
    checkMusaitlik();
  });
  
  // Form submit
  document.getElementById('rezervasyon-form').addEventListener('submit', submitRezervasyonu);
}

// Kütüphane listesini çek ve ilk kütüphaneyi seç
async function fetchKutuphaneListesi() {
  const loadingDiv = document.getElementById('loading');
  const errorDiv = document.getElementById('error-message');
  
  try {
    const response = await fetch(`${API_URL}/kutuphaneler`);
    
    if (!response.ok) {
      throw new Error('Kütüphaneler yüklenemedi');
    }
    
    const kutuphaneler = await response.json();
    
    if (kutuphaneler.length === 0) {
      throw new Error('Hiç kütüphane bulunamadı');
    }
    
    // İlk (ve tek) kütüphaneyi seç
    kutuphaneId = kutuphaneler[0].id;
    document.getElementById('kutuphane_id').value = kutuphaneId;
    
    // Kütüphane detayını yükle
    fetchKutuphaneDetay();
    
  } catch (error) {
    console.error('Hata:', error);
    loadingDiv.style.display = 'none';
    errorDiv.style.display = 'block';
    errorDiv.textContent = error.message;
  }
}

// Kütüphane detayını çek
async function fetchKutuphaneDetay() {
  const loadingDiv = document.getElementById('loading');
  const errorDiv = document.getElementById('error-message');
  
  try {
    const response = await fetch(`${API_URL}/kutuphane/${kutuphaneId}`);
    
    if (!response.ok) {
      throw new Error('Kütüphane bulunamadı');
    }
    
    kutuphane = await response.json();
    
    loadingDiv.style.display = 'none';
    displayKutuphaneInfo();
    document.getElementById('rezervasyon-form-container').style.display = 'block';
    
    // Bugünün tarihini default olarak seç
    document.getElementById('rezervasyon_tarihi').value = new Date().toISOString().split('T')[0];
    generateTimeSlots();
    
  } catch (error) {
    console.error('Hata:', error);
    loadingDiv.style.display = 'none';
    errorDiv.style.display = 'block';
    errorDiv.textContent = error.message;
  }
}

// Kütüphane bilgilerini göster
function displayKutuphaneInfo() {
  const infoDiv = document.getElementById('kutuphane-info');
  infoDiv.style.display = 'block';
  infoDiv.innerHTML = `
    <div class="kutuphane-info">
      <h2>${kutuphane.ad}</h2>
      <p><strong>Konum:</strong> ${kutuphane.konum}</p>
      <p><strong>Kapasite:</strong> ${kutuphane.toplam_kapasite} kişi</p>
      <p><strong>Çalışma Saatleri:</strong> ${kutuphane.acilis_saati} - ${kutuphane.kapanis_saati}</p>
      ${kutuphane.aciklama ? `<p>${kutuphane.aciklama}</p>` : ''}
    </div>
  `;
}

// Saat aralıklarını oluştur
function generateTimeSlots() {
  const baslangicDiv = document.getElementById('baslangic-saatleri');
  const bitisDiv = document.getElementById('bitis-saatleri');
  
  const acilis = kutuphane.acilis_saati || '08:00:00';
  const kapanis = kutuphane.kapanis_saati || '22:00:00';
  
  const saatler = generateHourSlots(acilis.substring(0, 5), kapanis.substring(0, 5));
  
  baslangicDiv.innerHTML = saatler.map(saat => 
    `<div class="time-slot" data-time="${saat}" onclick="selectBaslangic('${saat}')">${saat}</div>`
  ).join('');
  
  bitisDiv.innerHTML = '';
  selectedBaslangic = null;
  selectedBitis = null;
}

// Saat slot'larını oluştur
function generateHourSlots(start, end) {
  const slots = [];
  let current = start;
  
  while (current < end) {
    slots.push(current);
    // Bir saat ekle
    let [hours, minutes] = current.split(':').map(Number);
    hours++;
    current = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
  }
  
  return slots;
}

// Başlangıç saati seç
function selectBaslangic(saat) {
  selectedBaslangic = saat;
  
  // Başlangıç saatlerini güncelle
  document.querySelectorAll('#baslangic-saatleri .time-slot').forEach(slot => {
    slot.classList.remove('selected');
    if (slot.dataset.time === saat) {
      slot.classList.add('selected');
    }
  });
  
  // Bitiş saatlerini güncelle (başlangıçtan sonraki saatler)
  const bitisDiv = document.getElementById('bitis-saatleri');
  const acilis = kutuphane.acilis_saati || '08:00:00';
  const kapanis = kutuphane.kapanis_saati || '22:00:00';
  const allSlots = generateHourSlots(acilis.substring(0, 5), kapanis.substring(0, 5));
  
  const bitisSlots = allSlots.filter(s => s > saat);
  bitisDiv.innerHTML = bitisSlots.map(s => 
    `<div class="time-slot" data-time="${s}" onclick="selectBitis('${s}')">${s}</div>`
  ).join('');
  
  selectedBitis = null;
  checkMusaitlik();
}

// Bitiş saati seç
function selectBitis(saat) {
  selectedBitis = saat;
  
  document.querySelectorAll('#bitis-saatleri .time-slot').forEach(slot => {
    slot.classList.remove('selected');
    if (slot.dataset.time === saat) {
      slot.classList.add('selected');
    }
  });
  
  checkMusaitlik();
}

// Müsaitlik durumunu kontrol et
async function checkMusaitlik() {
  const tarih = document.getElementById('rezervasyon_tarihi').value;
  
  if (!tarih || !selectedBaslangic || !selectedBitis) {
    document.getElementById('musaitlik-durumu').style.display = 'none';
    return;
  }
  
  try {
    // Seçilen saat aralığıyla çakışan rezervasyonları kontrol et
    const response = await fetch(
      `${API_URL}/kutuphane/${kutuphaneId}/musaitlik?tarih=${tarih}&baslangic_saati=${selectedBaslangic}:00&bitis_saati=${selectedBitis}:00`
    );
    const data = await response.json();
    
    const mevcutRezervasyonlar = data.rezervasyonlar || 0;
    const musaitKoltuk = data.musait_kapasite || (data.toplam_kapasite - mevcutRezervasyonlar);
    const dolulukYuzdesi = (mevcutRezervasyonlar / data.toplam_kapasite) * 100;
    
    document.getElementById('toplam-kapasite').textContent = data.toplam_kapasite;
    document.getElementById('mevcut-rezervasyon').textContent = mevcutRezervasyonlar;
    document.getElementById('musait-koltuk').textContent = musaitKoltuk;
    
    const dolulukBar = document.getElementById('doluluk-bar');
    dolulukBar.style.width = dolulukYuzdesi + '%';
    dolulukBar.className = 'progress-fill';
    if (dolulukYuzdesi > 80) dolulukBar.classList.add('danger');
    else if (dolulukYuzdesi > 60) dolulukBar.classList.add('warning');
    
    document.getElementById('musaitlik-durumu').style.display = 'block';
    
    // Submit butonu durumu
    const submitBtn = document.getElementById('submit-btn');
    if (musaitKoltuk <= 0) {
      submitBtn.disabled = true;
      submitBtn.textContent = 'Kapasite Dolu';
    } else {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Rezervasyonu Onayla';
    }
    
  } catch (error) {
    console.error('Müsaitlik kontrolü hatası:', error);
  }
}

// Rezervasyon gönder
async function submitRezervasyonu(e) {
  e.preventDefault();
  
  const successDiv = document.getElementById('success-message');
  const errorDiv = document.getElementById('error-message');
  const submitBtn = document.getElementById('submit-btn');
  
  // Validasyon
  if (!selectedBaslangic || !selectedBitis) {
    errorDiv.style.display = 'block';
    errorDiv.textContent = 'Lütfen başlangıç ve bitiş saatini seçin!';
    return;
  }
  
  const rezervasyonData = {
    kutuphane_id: parseInt(kutuphaneId),
    kullanici_id: parseInt(kullaniciId),
    rezervasyon_tarihi: document.getElementById('rezervasyon_tarihi').value,
    baslangic_saati: selectedBaslangic + ':00',
    bitis_saati: selectedBitis + ':00'
  };
  
  // Buton devre dışı
  submitBtn.disabled = true;
  submitBtn.textContent = 'Gönderiliyor...';
  successDiv.style.display = 'none';
  errorDiv.style.display = 'none';
  
  try {
    const response = await fetch(`${API_URL}/rezervasyon`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(rezervasyonData)
    });
    
    const result = await response.json();
    
    if (response.ok) {
      successDiv.style.display = 'block';
      successDiv.innerHTML = `
        <strong>Başarılı!</strong><br>
        Rezervasyonunuz oluşturuldu.<br>
        <small>Rezervasyon No: ${result.id}</small>
      `;
      
      // Form'u temizle
      document.getElementById('rezervasyon-form').reset();
      selectedBaslangic = null;
      selectedBitis = null;
      
      // 3 saniye sonra rezervasyonlar sayfasına yönlendir
      setTimeout(() => {
        window.location.href = 'rezervasyonlarim.php';
      }, 2000);
      
    } else {
      throw new Error(result.detail || 'Bir hata oluştu');
    }
    
  } catch (error) {
    errorDiv.style.display = 'block';
    errorDiv.innerHTML = `
      <strong>Hata!</strong><br>
      ${error.message}
    `;
    submitBtn.disabled = false;
    submitBtn.textContent = 'Rezervasyonu Onayla';
  }
}
</script>

<?php include 'footer.php'; ?>

</body>
</html>
