<?php include "db.php"; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Etkinlik Yönetimi | Kampüs Sistemi</title>
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

<header class="topbar">
  <h1>Kampüs Etkinlik Takip Sistemi</h1>
  <nav class="menu">
    <a href="index.php" class="<?= $currentPage == 'index.php' ? 'active' : '' ?>">Ana Sayfa</a>
    <a href="etkinlikler.php" class="<?= $currentPage == 'etkinlikler.php' ? 'active' : '' ?>">Etkinlikler</a>
    <a href="takvim.php" class="<?= $currentPage == 'takvim.php' ? 'active' : '' ?>">Eski Takvim</a>
    <a href="akademik-takvim.php" class="<?= $currentPage == 'akademik-takvim.php' ? 'active' : '' ?>">Akademik Takvim (API)</a>
    <a href="etkinlik-yonetim.php" class="<?= $currentPage == 'etkinlik-yonetim.php' ? 'active' : '' ?>">Etkinlik Yönetimi</a>

    <?php if (!isset($_SESSION['user_id'])): ?>
      <a href="login.php" class="login-btn">Giriş Yap</a>
      <a href="signup.php" class="signup-btn">Kayıt Ol</a>
    <?php else: ?>
      <a href="profile.php" class="profile-btn">Profilim</a>
      <a href="logout.php" class="logout-btn">Çıkış Yap</a>
    <?php endif; ?>
  </nav>
</header>

<div class="yonetim-container">
  <h2>➕ Yeni Etkinlik Ekle</h2>
  <p style="color: #666; margin-bottom: 20px;">Backend API'ye yeni etkinlik ekleyin: <code>http://localhost:8000/api/calendar/etkinlik</code></p>

  <div id="api-status" class="api-status">
    <span>⏳ API durumu kontrol ediliyor...</span>
  </div>

  <div id="success-message" class="success-message"></div>
  <div id="error-message" class="error-message"></div>

  <div class="form-card">
    <form id="etkinlik-form">
      <div class="form-group">
        <label for="baslik">📝 Etkinlik Başlığı *</label>
        <input type="text" id="baslik" name="baslik" required placeholder="Örn: Yazılım Geliştirme Workshop">
      </div>

      <div class="form-group">
        <label for="etkinlik_turu">🏷️ Etkinlik Türü *</label>
        <select id="etkinlik_turu" name="etkinlik_turu" required>
          <option value="">Seçiniz...</option>
          <option value="sinav">Sınav</option>
          <option value="odev">Ödev</option>
          <option value="etkinlik">Etkinlik</option>
          <option value="seminer">Seminer</option>
          <option value="proje">Proje</option>
        </select>
      </div>

      <div class="form-group">
        <label for="aciklama">📄 Açıklama</label>
        <textarea id="aciklama" name="aciklama" placeholder="Etkinlik hakkında detaylı bilgi..."></textarea>
      </div>

      <div class="form-group">
        <label for="baslangic_tarihi">📅 Başlangıç Tarihi ve Saati *</label>
        <input type="datetime-local" id="baslangic_tarihi" name="baslangic_tarihi" required>
        <div class="form-hint">Etkinliğin başlayacağı tarih ve saat</div>
      </div>

      <div class="form-group">
        <label for="bitis_tarihi">📅 Bitiş Tarihi ve Saati</label>
        <input type="datetime-local" id="bitis_tarihi" name="bitis_tarihi">
        <div class="form-hint">Opsiyonel - Etkinliğin biteceği tarih ve saat</div>
      </div>

      <div class="form-group">
        <label for="konum">📍 Konum</label>
        <input type="text" id="konum" name="konum" placeholder="Örn: A Blok Konferans Salonu">
      </div>

      <button type="submit" class="submit-btn">✅ Etkinlik Ekle</button>
    </form>
  </div>

  <div style="margin-top: 20px; text-align: center;">
    <a href="akademik-takvim.php" style="color: #2196F3; text-decoration: none; font-weight: bold;">
      ← Akademik Takvime Dön
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
      statusDiv.innerHTML = '✅ Backend API çalışıyor';
    } else {
      throw new Error('API yanıt vermiyor');
    }
  } catch (error) {
    statusDiv.className = 'api-status api-offline';
    statusDiv.innerHTML = '❌ Backend API çalışmıyor - Lütfen Backend\'i başlatın';
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
        <strong>✅ Başarılı!</strong><br>
        Etkinlik başarıyla eklendi: <strong>${result.baslik}</strong><br>
        <small>ID: ${result.id}</small>
      `;
      
      // Formu temizle
      document.getElementById('etkinlik-form').reset();
      
      // 3 saniye sonra akademik takvim sayfasına yönlendir
      setTimeout(() => {
        window.location.href = 'akademik-takvim.php';
      }, 2000);
      
    } else {
      throw new Error(result.detail || 'Bir hata oluştu');
    }
    
  } catch (error) {
    errorDiv.style.display = 'block';
    errorDiv.innerHTML = `
      <strong>❌ Hata!</strong><br>
      ${error.message}<br>
      <small>Backend API'nin çalıştığından emin olun.</small>
    `;
  } finally {
    // Butonu tekrar aktif et
    submitBtn.disabled = false;
    submitBtn.textContent = '✅ Etkinlik Ekle';
  }
}
</script>

</body>
</html>

