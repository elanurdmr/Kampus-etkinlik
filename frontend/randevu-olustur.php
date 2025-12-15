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
  <title>Öğretim Üyesi Randevu | Kampüs Sistemi</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .randevu-container {
      max-width: 900px;
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
    
    .page-header {
      background: linear-gradient(135deg, #b30000 0%, #8b0000 100%);
      color: white;
      padding: 25px;
      border-radius: 12px;
      margin-bottom: 30px;
      box-shadow: 0 4px 12px rgba(179,0,0,0.3);
    }
    
    .page-header h2 {
      margin: 0 0 10px 0;
      font-size: 1.8em;
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
    .form-group select,
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
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #b30000;
    }
    
    .form-group textarea {
      min-height: 100px;
      resize: vertical;
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
      box-shadow: 0 4px 8px rgba(179,0,0,0.3);
    }
    
    #ogretimUyeleriList {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 15px;
      margin-top: 20px;
    }
    
    .ogretim-uyesi-card {
      border: 2px solid #ddd;
      border-radius: 8px;
      padding: 15px;
      cursor: pointer;
      transition: all 0.3s;
      background: white;
    }
    
    .ogretim-uyesi-card:hover {
      border-color: #b30000;
      background: #fff5f5;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(179,0,0,0.2);
    }
    
    .ogretim-uyesi-card.selected {
      border-color: #b30000;
      background: #ffe6e6;
      box-shadow: 0 4px 12px rgba(179,0,0,0.3);
    }
    
    .ogretim-uyesi-card h4 {
      margin: 0 0 10px 0;
      color: #b30000;
      font-size: 1.1em;
    }
    
    .ogretim-uyesi-card p {
      margin: 5px 0;
      color: #666;
      font-size: 0.9em;
    }
    
    #loadingSpinner {
      text-align: center;
      padding: 40px;
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
      display: block;
      text-align: center;
      padding: 20px;
    }
    
    .spinner {
      border: 3px solid #f3f3f3;
      border-top: 3px solid #b30000;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      animation: spin 1s linear infinite;
      margin: 0 auto;
    }
    
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="randevu-container">
  <div class="page-header">
    <h2>📅 Öğretim Üyesi Randevu Oluştur</h2>
    <p>Öğretim üyesi ile randevu almak için formu doldurun</p>
  </div>

  <div id="alertContainer"></div>

  <div class="form-card">
    <h3>1. Öğretim Üyesi Seçin</h3>
    <div id="loadingSpinner" style="text-align: center; padding: 40px; display: block;">
      <div class="spinner"></div>
      <p>Öğretim üyeleri yükleniyor...</p>
    </div>
    <div id="ogretimUyeleriList"></div>
  </div>

  <form id="randevuForm" class="form-card" style="display: none;">
    <h3>2. Randevu Bilgileri</h3>
    
    <div class="form-group">
      <label for="randevuTarihi">Randevu Tarihi *</label>
      <input type="date" id="randevuTarihi" name="randevuTarihi" required min="<?php echo date('Y-m-d'); ?>">
    </div>
    
    <div class="form-group">
      <label for="randevuSaati">Randevu Saati *</label>
      <input type="time" id="randevuSaati" name="randevuSaati" required>
    </div>
    
    <div class="form-group">
      <label for="konu">Konu *</label>
      <input type="text" id="konu" name="konu" placeholder="Örn: Ders konusu hakkında görüşme" required>
    </div>
    
    <div class="form-group">
      <label for="aciklama">Açıklama</label>
      <textarea id="aciklama" name="aciklama" placeholder="Randevu hakkında ek bilgiler..."></textarea>
    </div>
    
    <input type="hidden" id="ogretimUyesiId" name="ogretimUyesiId">
    <input type="hidden" id="ogrenciId" name="ogrenciId" value="<?php echo $kullanici_id; ?>">
    
    <button type="submit" class="btn btn-primary">Randevu Oluştur</button>
  </form>
</div>

<script>
const API_BASE_URL = 'http://localhost:8000/api/randevu';
// kullaniciId navbar.php'den geliyor, eğer yoksa buradan al
if (typeof window.kullaniciId === 'undefined') {
  window.kullaniciId = <?php echo $kullanici_id; ?>;
}
// const kullaniciId tanımlamıyoruz, direkt window.kullaniciId kullanacağız

// Sayfa yüklendiğinde öğretim üyelerini yükle
document.addEventListener('DOMContentLoaded', function() {
  console.log('DOM yüklendi, öğretim üyeleri yükleniyor...');
  try {
    ogretimUyeleriniYukle();
  } catch (error) {
    console.error('Öğretim üyeleri yükleme hatası:', error);
  }
  
  // Tarih input'una minimum değer ata
  const tarihInput = document.getElementById('randevuTarihi');
  if (tarihInput) {
    tarihInput.min = new Date().toISOString().split('T')[0];
  }
});

// Eğer DOM zaten yüklüyse
if (document.readyState === 'loading') {
  // DOM henüz yükleniyor, event listener yukarıda
} else {
  // DOM zaten yüklü, direkt çalıştır
  console.log('DOM zaten yüklü, direkt çalıştırılıyor...');
  try {
    ogretimUyeleriniYukle();
  } catch (error) {
    console.error('Öğretim üyeleri yükleme hatası:', error);
  }
}

// Öğretim üyelerini yükle
async function ogretimUyeleriniYukle() {
  console.log('ogretimUyeleriniYukle() çağrıldı');
  const loadingSpinner = document.getElementById('loadingSpinner');
  const container = document.getElementById('ogretimUyeleriList');
  
  if (!loadingSpinner || !container) {
    console.error('DOM elementleri bulunamadı!', { loadingSpinner, container });
    return;
  }
  
  try {
    console.log('API çağrısı yapılıyor:', `${API_BASE_URL}/ogretim-uyeleri`);
    // Loading göster
    loadingSpinner.style.display = 'block';
    container.innerHTML = '';
    
    // Timeout kontrolü
    const timeoutId = setTimeout(() => {
      console.error('Timeout: API yanıt vermedi');
      if (loadingSpinner) loadingSpinner.style.display = 'none';
      if (container) {
        container.innerHTML = `
          <div style="background: #fff3cd; border: 2px solid #ffc107; padding: 20px; border-radius: 8px; color: #856404;">
            <h4 style="margin: 0 0 10px 0;">⏱️ Bağlantı Zaman Aşımı</h4>
            <p>Backend sunucusuna bağlanılamıyor. Lütfen backend'in çalıştığından emin olun.</p>
            <p style="font-size: 0.9em; margin-top: 10px;">
              <code style="background: #f5f5f5; padding: 5px; border-radius: 4px;">cd backend/app && python main.py</code>
            </p>
          </div>
        `;
      }
    }, 5000);
    
    const response = await fetch(`${API_BASE_URL}/ogretim-uyeleri`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json'
      }
    });
    
    clearTimeout(timeoutId);
    console.log('API yanıtı alındı:', response.status, response.ok);
    
    if (!response.ok) {
      const errorText = await response.text();
      console.error('API hatası:', errorText);
      throw new Error(`API Hatası: ${response.status} - ${errorText}`);
    }
    
    const ogretimUyeleri = await response.json();
    console.log('Öğretim üyeleri alındı:', ogretimUyeleri.length, 'adet');
    
    // Loading gizle
    loadingSpinner.style.display = 'none';
    
    if (!ogretimUyeleri || ogretimUyeleri.length === 0) {
      container.innerHTML = '<div class="alert alert-error">Henüz öğretim üyesi eklenmemiş.</div>';
      return;
    }
    
    let html = '';
    ogretimUyeleri.forEach(uyesi => {
      html += `
        <div class="ogretim-uyesi-card" onclick="ogretimUyesiSec(${uyesi.id})" data-id="${uyesi.id}">
          <h4>${uyesi.unvan || ''} ${uyesi.ad} ${uyesi.soyad}</h4>
          <p><strong>Bölüm:</strong> ${uyesi.bolum || 'Belirtilmemiş'}</p>
          <p><strong>Email:</strong> ${uyesi.email}</p>
          ${uyesi.ofis_no ? `<p><strong>Ofis:</strong> ${uyesi.ofis_no}</p>` : ''}
        </div>
      `;
    });
    
    container.innerHTML = html;
    console.log(`${ogretimUyeleri.length} öğretim üyesi başarıyla yüklendi ve gösterildi`);
  } catch (error) {
    console.error('Hata:', error);
    if (loadingSpinner) loadingSpinner.style.display = 'none';
    if (container) {
      let errorMessage = 'Öğretim üyeleri yüklenirken bir hata oluştu.';
      
      if (error.message.includes('Failed to fetch') || error.message.includes('NetworkError')) {
        errorMessage = `
          <div style="background: #fff3cd; border: 2px solid #ffc107; padding: 20px; border-radius: 8px; color: #856404;">
            <h4 style="margin: 0 0 10px 0;">⚠️ Backend Sunucusu Çalışmıyor</h4>
            <p style="margin: 5px 0;">Backend API'ye bağlanılamıyor. Lütfen backend sunucusunun çalıştığından emin olun.</p>
            <p style="margin: 10px 0; font-size: 0.9em;">
              <strong>Backend'i başlatmak için:</strong><br>
              <code style="background: #f5f5f5; padding: 5px; border-radius: 4px; display: inline-block; margin-top: 5px;">
                cd backend/app && python main.py
              </code>
            </p>
            <p style="margin: 10px 0; font-size: 0.85em; color: #666;">
              API URL: <a href="http://localhost:8000" target="_blank">http://localhost:8000</a>
            </p>
          </div>
        `;
      } else {
        errorMessage = `
          <div style="background: #f8d7da; border: 2px solid #dc3545; padding: 20px; border-radius: 8px; color: #721c24;">
            <h4 style="margin: 0 0 10px 0;">❌ Hata</h4>
            <p style="margin: 5px 0;">${error.message}</p>
            <p style="margin: 10px 0; font-size: 0.85em;">
              Backend çalışıyor mu kontrol edin: <a href="http://localhost:8000" target="_blank">http://localhost:8000</a>
            </p>
          </div>
        `;
      }
      
      container.innerHTML = errorMessage;
    }
  }
}

// Öğretim üyesi seç
function ogretimUyesiSec(id) {
  // Tüm kartlardan seçili class'ını kaldır
  document.querySelectorAll('.ogretim-uyesi-card').forEach(card => {
    card.classList.remove('selected');
  });
  
  // Seçilen kartı işaretle
  const selectedCard = document.querySelector(`[data-id="${id}"]`);
  if (selectedCard) {
    selectedCard.classList.add('selected');
    document.getElementById('ogretimUyesiId').value = id;
    document.getElementById('randevuForm').style.display = 'block';
    
    // Sayfayı form'a kaydır
    document.getElementById('randevuForm').scrollIntoView({ behavior: 'smooth' });
  }
}

// Form gönder
document.getElementById('randevuForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const formData = {
    ogretim_uyesi_id: parseInt(document.getElementById('ogretimUyesiId').value),
    ogrenci_id: parseInt(document.getElementById('ogrenciId').value),
    randevu_tarihi: document.getElementById('randevuTarihi').value,
    randevu_saati: document.getElementById('randevuSaati').value,
    konu: document.getElementById('konu').value,
    aciklama: document.getElementById('aciklama').value
  };
  
  try {
    const response = await fetch(`${API_BASE_URL}/randevu`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(formData)
    });
    
    const data = await response.json();
    
    if (response.ok) {
      showAlert('Randevu başarıyla oluşturuldu!', 'success');
      document.getElementById('randevuForm').reset();
      document.getElementById('randevuForm').style.display = 'none';
      document.querySelectorAll('.ogretim-uyesi-card').forEach(card => {
        card.classList.remove('selected');
      });
      
      // 3 saniye sonra randevularım sayfasına yönlendir
      setTimeout(() => {
        window.location.href = 'randevularim.php';
      }, 3000);
    } else {
      showAlert(data.detail || 'Randevu oluşturulurken bir hata oluştu', 'error');
    }
  } catch (error) {
    console.error('Hata:', error);
    showAlert('Bağlantı hatası. Lütfen tekrar deneyin.', 'error');
  }
});

// Alert göster
function showAlert(message, type) {
  const container = document.getElementById('alertContainer');
  container.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
  
  setTimeout(() => {
    container.innerHTML = '';
  }, 5000);
}
</script>

<?php include "footer.php"; ?>

</body>
</html>

