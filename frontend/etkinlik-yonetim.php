<?php
include "db.php";
require_once "lang.php";
?>
<!DOCTYPE html>
<html lang="<?= $currentLang === 'en' ? 'en' : 'tr' ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= t('Etkinlik Yönetimi | Kampüs Sistemi', 'Event Management | Campus System') ?></title>
  <link rel="stylesheet" href="style.css">
  <style>
    .yonetim-container {
      max-width: 800px;
      margin: 40px auto;
      padding: 20px;
    }
    .form-card {
      background: white;
      padding: 30px;
      border-radius: 10px;
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
    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 14px;
      box-sizing: border-box;
    }
    .form-group textarea {
      min-height: 100px;
      resize: vertical;
    }
    .submit-btn {
      background: #2196F3;
      color: white;
      padding: 15px 30px;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      width: 100%;
    }
    .submit-btn:hover {
      background: #1976D2;
    }
    .success-message {
      background: #e8f5e9;
      color: #2e7d32;
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
      display: none;
    }
    .error-message {
      background: #ffebee;
      color: #c62828;
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
      display: none;
    }
    .api-status {
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 20px;
      text-align: center;
    }
    .api-online {
      background: #e8f5e9;
      color: #2e7d32;
    }
    .api-offline {
      background: #ffebee;
      color: #c62828;
    }
    .form-hint {
      font-size: 12px;
      color: #999;
      margin-top: 5px;
    }
  </style>
</head>
<body>

<?php
  $currentPage = basename($_SERVER['PHP_SELF']);
?>

<?php include "navbar.php"; ?>

<div class="yonetim-container">
  <h2><?= t('Yeni Etkinlik Ekle', 'Add New Event') ?></h2>
  <p style="color: #666; margin-bottom: 20px;">
    <?= t(
      "Backend API'ye yeni etkinlik ekleyin:",
      'Create a new event in the Backend API:'
    ) ?>
    <code>http://localhost:8000/api/calendar/etkinlik</code>
  </p>

  <div id="api-status" class="api-status">
    <span>⏳ API durumu kontrol ediliyor...</span>
  </div>

  <div id="success-message" class="success-message"></div>
  <div id="error-message" class="error-message"></div>

  <div class="form-card">
    <form id="etkinlik-form">
      <div class="form-group">
        <label for="baslik"><?= t('Etkinlik Başlığı *', 'Event Title *') ?></label>
        <input type="text" id="baslik" name="baslik" required placeholder="<?= t('Örn: Yazılım Geliştirme Workshop', 'e.g. Software Development Workshop') ?>">
      </div>

      <div class="form-group">
        <label for="etkinlik_turu"><?= t('Etkinlik Türü *', 'Event Type *') ?></label>
        <select id="etkinlik_turu" name="etkinlik_turu" required>
          <option value=""><?= t('Seçiniz...', 'Select...') ?></option>
          <option value="sinav"><?= t('Sınav', 'Exam') ?></option>
          <option value="odev"><?= t('Ödev', 'Assignment') ?></option>
          <option value="etkinlik"><?= t('Etkinlik', 'Event') ?></option>
          <option value="seminer"><?= t('Seminer', 'Seminar') ?></option>
          <option value="proje"><?= t('Proje', 'Project') ?></option>
        </select>
      </div>

      <div class="form-group">
        <label for="aciklama">📄 <?= t('Açıklama', 'Description') ?></label>
        <textarea id="aciklama" name="aciklama" placeholder="<?= t('Etkinlik hakkında detaylı bilgi...', 'Detailed information about the event...') ?>"></textarea>
      </div>

      <div class="form-group">
        <label for="baslangic_tarihi"><?= t('Başlangıç Tarihi ve Saati *', 'Start Date & Time *') ?></label>
        <input type="datetime-local" id="baslangic_tarihi" name="baslangic_tarihi" required>
        <div class="form-hint"><?= t('Etkinliğin başlayacağı tarih ve saat', 'Date and time when the event starts') ?></div>
      </div>

      <div class="form-group">
        <label for="bitis_tarihi"><?= t('Bitiş Tarihi ve Saati', 'End Date & Time') ?></label>
        <input type="datetime-local" id="bitis_tarihi" name="bitis_tarihi">
        <div class="form-hint"><?= t('Opsiyonel - Etkinliğin biteceği tarih ve saat', 'Optional - Date and time when the event ends') ?></div>
      </div>

      <div class="form-group">
        <label for="konum"><?= t('Konum', 'Location') ?></label>
        <input type="text" id="konum" name="konum" placeholder="<?= t('Örn: A Blok Konferans Salonu', 'e.g. A Block Conference Hall') ?>">
      </div>

      <button type="submit" class="submit-btn">➕ <?= t('Etkinlik Ekle', 'Add Event') ?></button>
    </form>
  </div>

  <div style="margin-top: 20px; text-align: center;">
    <a href="akademik-takvim.php" style="color: #2196F3; text-decoration: none; font-weight: bold;">
      <?= t('Akademik Takvime Dön', 'Back to Academic Calendar') ?>
    </a>
  </div>
</div>

<?php include "footer.php"; ?>

<script>
const API_URL = 'http://localhost:8000/api/calendar';

// Sayfa yüklendiğinde API durumunu kontrol et
document.addEventListener('DOMContentLoaded', function() {
  checkAPIStatus();
  setupForm();
});

// API durumunu kontrol et
async function checkAPIStatus() {
  const statusDiv = document.getElementById('api-status');
  
  try {
    const response = await fetch('http://localhost:8000/');
    
    if (response.ok) {
      statusDiv.className = 'api-status api-online';
      statusDiv.innerHTML = '<?= t('Backend API çalışıyor', 'Backend API is running') ?>';
    } else {
      throw new Error('API yanıt vermiyor');
    }
  } catch (error) {
    statusDiv.className = 'api-status api-offline';
    statusDiv.innerHTML = '<?= t("Backend API çalışmıyor - Lütfen Backend'i başlatın", 'Backend API is not running - Please start the backend server') ?>';
  }
}

// Form işlemlerini ayarla
function setupForm() {
  const form = document.getElementById('etkinlik-form');
  
  form.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
      baslik: document.getElementById('baslik').value,
      etkinlik_turu: document.getElementById('etkinlik_turu').value,
      aciklama: document.getElementById('aciklama').value || null,
      baslangic_tarihi: document.getElementById('baslangic_tarihi').value,
      bitis_tarihi: document.getElementById('bitis_tarihi').value || null,
      konum: document.getElementById('konum').value || null
    };
    
    await submitEtkinlik(formData);
  });
}

// Etkinliği Backend API'ye gönder
async function submitEtkinlik(data) {
  const successDiv = document.getElementById('success-message');
  const errorDiv = document.getElementById('error-message');
  const submitBtn = document.querySelector('.submit-btn');
  
  // Mesajları temizle
  successDiv.style.display = 'none';
  errorDiv.style.display = 'none';
  
  // Butonu devre dışı bırak
  submitBtn.disabled = true;
  submitBtn.textContent = '⏳ Gönderiliyor...';
  
  try {
    const response = await fetch(`${API_URL}/etkinlik`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data)
    });
    
    const result = await response.json();
    
    if (response.ok) {
      // Başarılı
      successDiv.style.display = 'block';
      successDiv.innerHTML = `
        <strong><?= t('Başarılı!', 'Success!') ?></strong><br>
        <?= t('Etkinlik başarıyla eklendi:', 'Event successfully created:') ?> <strong>${result.baslik}</strong><br>
        <small>ID: ${result.id}</small>
      `;
      
      // Formu temizle
      document.getElementById('etkinlik-form').reset();
      
      // 3 saniye sonra akademik takvim sayfasına yönlendir
      setTimeout(() => {
        window.location.href = 'akademik-takvim.php';
      }, 2000);
      
    } else {
      throw new Error(result.detail || '<?= t('Bir hata oluştu', 'An error occurred') ?>');
    }
    
  } catch (error) {
    errorDiv.style.display = 'block';
    errorDiv.innerHTML = `
      <strong><?= t('Hata!', 'Error!') ?></strong><br>
      ${error.message}<br>
      <small><?= t("Backend API'nin çalıştığından emin olun.", 'Please make sure the Backend API is running.') ?></small>
    `;
  } finally {
    // Butonu tekrar aktif et
    submitBtn.disabled = false;
    submitBtn.textContent = '➕ <?= t('Etkinlik Ekle', 'Add Event') ?>';
  }
}
</script>

</body>
</html>


