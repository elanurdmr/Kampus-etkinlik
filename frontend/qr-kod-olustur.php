<?php
// Hata raporlamayı aç (geliştirme için)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "lang.php";
include "auth_helper.php";

// Sadece admin veya kulüp başkanı erişebilsin
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

if (!isAdmin() && !isKulupBaskani()) {
  redirectToPanel();
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="<?= $currentLang === 'en' ? 'en' : 'tr' ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= t('QR Kod Oluştur | Kampüs Sistemi', 'Generate QR Code | Campus System') ?></title>
  <link rel="stylesheet" href="style.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <style>
    .qr-container {
      max-width: 900px;
      margin: 40px auto;
      padding: 20px;
    }
    
    .page-header {
      background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
      color: white;
      padding: 30px;
      border-radius: 15px;
      margin-bottom: 30px;
      box-shadow: 0 8px 20px rgba(33,150,243,0.3);
    }
    
    .page-header h1 {
      margin: 0;
      font-size: 2em;
    }
    
    .form-card {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
      color: #333;
    }
    
    .form-group select {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 14px;
      box-sizing: border-box;
    }
    
    .btn {
      padding: 12px 24px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .btn-primary {
      background: #2196F3;
      color: white;
    }
    
    .btn-primary:hover {
      background: #1976D2;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(33,150,243,0.3);
    }
    
    .btn-primary:disabled {
      background: #ccc;
      cursor: not-allowed;
      transform: none;
    }
    
    .qr-result {
      display: none;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      text-align: center;
    }
    
    .qr-result.active {
      display: block;
    }
    
    #qrcode {
      margin: 20px auto;
      display: inline-block;
    }
    
    .qr-info {
      margin-top: 20px;
      padding: 15px;
      background: #f5f5f5;
      border-radius: 8px;
    }
    
    .qr-info p {
      margin: 5px 0;
      color: #666;
    }
    
    .qr-code-text {
      margin-top: 15px;
      padding: 10px;
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-family: monospace;
      word-break: break-all;
      font-size: 0.9em;
    }
    
    .btn-download {
      margin-top: 15px;
      background: #28a745;
      color: white;
    }
    
    .btn-download:hover {
      background: #218838;
    }
    
    .alert {
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    
    .alert-success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    
    .alert-error {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    
    .loading {
      text-align: center;
      padding: 20px;
      color: #666;
    }
  </style>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="qr-container">
  <div class="page-header">
    <h1><?= t('QR Kod Oluştur', 'Generate QR Code') ?></h1>
    <p><?= t('Etkinlik için QR kod oluşturun ve katılım takibi yapın', 'Generate QR code for events and track participation') ?></p>
  </div>

  <div id="alertContainer"></div>

  <div class="form-card">
    <h2><?= t('Etkinlik Seçin', 'Select Event') ?></h2>
    <div class="form-group">
      <label for="etkinlikSelect"><?= t('Etkinlik *', 'Event *') ?></label>
      <select id="etkinlikSelect" required>
        <option value=""><?= t('Yükleniyor...', 'Loading...') ?></option>
      </select>
    </div>
    
    <button type="button" class="btn btn-primary" id="qrOlusturBtn" onclick="qrKodOlustur()">
      <?= t('QR Kod Oluştur', 'Generate QR Code') ?>
    </button>
  </div>

  <div class="qr-result" id="qrResult">
    <h2><?= t('QR Kod Oluşturuldu', 'QR Code Generated') ?></h2>
    <div id="qrcode"></div>
    <div class="qr-info">
      <p><strong><?= t('Etkinlik:', 'Event:') ?></strong> <span id="etkinlikBaslik"></span></p>
      <p><strong><?= t('QR Kod:', 'QR Code:') ?></strong></p>
      <div class="qr-code-text" id="qrCodeText"></div>
    </div>
    <button type="button" class="btn btn-download" onclick="qrKodIndir()">
      <?= t('QR Kodu İndir', 'Download QR Code') ?>
    </button>
  </div>
</div>

<?php include "footer.php"; ?>

<script>
const API_BASE_URL = 'http://localhost:8000/api';
let qrCodeInstance = null;
let currentQRData = null;

// Sayfa yüklendiğinde etkinlikleri yükle
document.addEventListener('DOMContentLoaded', function() {
  etkinlikleriYukle();
});

// Etkinlikleri backend'den yükle
async function etkinlikleriYukle() {
  const select = document.getElementById('etkinlikSelect');
  
  try {
    const response = await fetch(`${API_BASE_URL}/calendar/etkinlikler`);
    if (!response.ok) {
      select.innerHTML = `<option value=""><?= t('Etkinlikler yüklenemedi', 'Failed to load events') ?></option>`;
      return;
    }
    
    const etkinlikler = await response.json();
    
    if (!etkinlikler || etkinlikler.length === 0) {
      select.innerHTML = `<option value=""><?= t('Henüz etkinlik bulunmuyor', 'No events found') ?></option>`;
      return;
    }
    
    select.innerHTML = '<option value=""><?= t('Etkinlik seçin...', 'Select event...') ?></option>';
    etkinlikler.forEach(etkinlik => {
      if (etkinlik.aktif) {
        const option = document.createElement('option');
        option.value = etkinlik.id;
        option.textContent = `${etkinlik.baslik} (${new Date(etkinlik.baslangic_tarihi).toLocaleDateString('tr-TR')})`;
        option.dataset.baslik = etkinlik.baslik;
        select.appendChild(option);
      }
    });
  } catch (error) {
    console.error('Etkinlik yükleme hatası:', error);
    select.innerHTML = `<option value=""><?= t('Bağlantı hatası', 'Connection error') ?></option>`;
  }
}

// QR kod oluştur
async function qrKodOlustur() {
  const etkinlikId = document.getElementById('etkinlikSelect').value;
  const etkinlikBaslik = document.getElementById('etkinlikSelect').selectedOptions[0]?.dataset.baslik;
  const btn = document.getElementById('qrOlusturBtn');
  const resultDiv = document.getElementById('qrResult');
  const alertContainer = document.getElementById('alertContainer');
  
  if (!etkinlikId) {
    alertContainer.innerHTML = `
      <div class="alert alert-error">
        <?= t('Lütfen bir etkinlik seçin', 'Please select an event') ?>
      </div>
    `;
    setTimeout(() => alertContainer.innerHTML = '', 3000);
    return;
  }
  
  btn.disabled = true;
  btn.textContent = '<?= t('Oluşturuluyor...', 'Generating...') ?>';
  
  try {
    const response = await fetch(`${API_BASE_URL}/qr/qr-kod-olustur`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        etkinlik_id: parseInt(etkinlikId)
      })
    });
    
    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.detail || '<?= t('QR kod oluşturulamadı', 'Failed to generate QR code') ?>');
    }
    
    const qrData = await response.json();
    currentQRData = qrData;
    
    // QR kod görselini oluştur
    const qrcodeDiv = document.getElementById('qrcode');
    qrcodeDiv.innerHTML = '';
    
    // QR kod string'ini oluştur (backend'den gelen qr_kod değeri)
    const qrCodeString = qrData.qr_kod;
    
    // QR.js ile QR kod oluştur
    qrCodeInstance = new QRCode(qrcodeDiv, {
      text: qrCodeString,
      width: 256,
      height: 256,
      colorDark: "#000000",
      colorLight: "#ffffff",
      correctLevel: QRCode.CorrectLevel.H
    });
    
    // Etkinlik bilgisini göster
    document.getElementById('etkinlikBaslik').textContent = etkinlikBaslik || '';
    document.getElementById('qrCodeText').textContent = qrCodeString;
    
    // Sonucu göster
    resultDiv.classList.add('active');
    
    alertContainer.innerHTML = `
      <div class="alert alert-success">
        <?= t('QR kod başarıyla oluşturuldu!', 'QR code generated successfully!') ?>
      </div>
    `;
    setTimeout(() => alertContainer.innerHTML = '', 3000);
    
  } catch (error) {
    console.error('QR kod oluşturma hatası:', error);
    alertContainer.innerHTML = `
      <div class="alert alert-error">
        <strong><?= t('Hata!', 'Error!') ?></strong><br>
        ${error.message}
      </div>
    `;
  } finally {
    btn.disabled = false;
    btn.textContent = '<?= t('QR Kod Oluştur', 'Generate QR Code') ?>';
  }
}

// QR kodu indir
function qrKodIndir() {
  const qrcodeDiv = document.getElementById('qrcode');
  const canvas = qrcodeDiv.querySelector('canvas');
  
  if (!canvas) {
    alert('<?= t('QR kod görseli bulunamadı', 'QR code image not found') ?>');
    return;
  }
  
  // Canvas'ı PNG olarak indir
  const link = document.createElement('a');
  link.download = `qr-kod-${currentQRData?.etkinlik_id || 'etkinlik'}.png`;
  link.href = canvas.toDataURL('image/png');
  link.click();
}
</script>

</body>
</html>
