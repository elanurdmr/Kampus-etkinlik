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
  <title>Randevularım | Kampüs Sistemi</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .randevular-container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 20px;
    }
    
    .page-header {
      background: linear-gradient(135deg, #b30000 0%, #8b0000 100%);
      color: white;
      padding: 25px;
      border-radius: 12px;
      margin-bottom: 30px;
      box-shadow: 0 4px 12px rgba(179,0,0,0.3);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 15px;
    }
    
    .page-header h2 {
      margin: 0;
      font-size: 1.8em;
    }
    
    .btn {
      padding: 12px 30px;
      border: none;
      border-radius: 8px;
      font-size: 1em;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      text-decoration: none;
      display: inline-block;
    }
    
    .btn-primary {
      background: white;
      color: #b30000;
    }
    
    .btn-primary:hover {
      background: #f0f0f0;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .filters {
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      margin-bottom: 20px;
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
      align-items: center;
    }
    
    .filters select {
      padding: 10px;
      border: 2px solid #ddd;
      border-radius: 8px;
      font-size: 1em;
    }
    
    .randevu-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      padding: 25px;
      margin-bottom: 20px;
      transition: all 0.3s;
      border-left: 4px solid #b30000;
    }
    
    .randevu-card:hover {
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
      transform: translateY(-2px);
    }
    
    .randevu-header {
      display: flex;
      justify-content: space-between;
      align-items: start;
      margin-bottom: 15px;
      flex-wrap: wrap;
      gap: 10px;
    }
    
    .randevu-header h3 {
      margin: 0;
      color: #b30000;
      font-size: 1.3em;
    }
    
    .durum-badge {
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.85em;
      font-weight: 600;
      text-transform: uppercase;
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
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      margin-bottom: 15px;
    }
    
    .info-item {
      display: flex;
      flex-direction: column;
    }
    
    .info-label {
      font-size: 0.85em;
      color: #666;
      margin-bottom: 5px;
    }
    
    .info-value {
      font-size: 1.1em;
      font-weight: 600;
      color: #333;
    }
    
    .randevu-actions {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-top: 15px;
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
    }
    
    .btn-danger {
      background: #dc3545;
      color: white;
    }
    
    .btn-danger:hover {
      background: #c82333;
    }
    
    .btn-secondary {
      background: #6c757d;
      color: white;
    }
    
    .btn-secondary:hover {
      background: #5a6268;
    }
    
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .empty-state h3 {
      color: #666;
      margin-bottom: 10px;
    }
    
    .empty-state p {
      color: #999;
      margin-bottom: 20px;
    }
    
    .loading {
      text-align: center;
      padding: 40px;
    }
    
    .spinner {
      border: 3px solid #f3f3f3;
      border-top: 3px solid #b30000;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      animation: spin 1s linear infinite;
      margin: 0 auto 20px;
    }
    
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="randevular-container">
  <div class="page-header">
    <h2>📅 Randevularım</h2>
    <a href="randevu-olustur.php" class="btn btn-primary">+ Yeni Randevu</a>
  </div>

  <div class="filters">
    <label for="durumFilter">Durum:</label>
    <select id="durumFilter" onchange="randevulariYukle()">
      <option value="">Tümü</option>
      <option value="bekliyor">Bekliyor</option>
      <option value="onaylandi">Onaylandı</option>
      <option value="reddedildi">Reddedildi</option>
      <option value="tamamlandi">Tamamlandı</option>
      <option value="iptal_edildi">İptal Edildi</option>
    </select>
  </div>

  <div id="randevularList" class="loading">
    <div class="spinner"></div>
    <p>Randevular yükleniyor...</p>
  </div>
</div>

<script>
const API_BASE_URL = 'http://localhost:8000/api/randevu';
// kullaniciId navbar.php'den geliyor
if (typeof window.kullaniciId === 'undefined') {
  window.kullaniciId = <?php echo $kullanici_id; ?>;
}

// Sayfa yüklendiğinde randevuları yükle
document.addEventListener('DOMContentLoaded', function() {
  randevulariYukle();
});

// Randevuları yükle
async function randevulariYukle() {
  const durum = document.getElementById('durumFilter').value;
  const container = document.getElementById('randevularList');
  
  container.innerHTML = '<div class="spinner"></div><p>Randevular yükleniyor...</p>';
  
  try {
    let url = `${API_BASE_URL}/ogrenci/${window.kullaniciId}/randevular`;
    const response = await fetch(url);
    
    if (!response.ok) throw new Error('Randevular yüklenemedi');
    
    const randevular = await response.json();
    
    // Durum filtresi uygula
    let filtrelenmisRandevular = randevular;
    if (durum) {
      filtrelenmisRandevular = randevular.filter(r => r.durum === durum);
    }
    
    // Tarihe göre sırala (gelecek randevular önce)
    filtrelenmisRandevular.sort((a, b) => {
      const tarihA = new Date(a.randevu_tarihi + ' ' + a.randevu_saati);
      const tarihB = new Date(b.randevu_tarihi + ' ' + b.randevu_saati);
      return tarihA - tarihB;
    });
    
    if (filtrelenmisRandevular.length === 0) {
      container.innerHTML = `
        <div class="empty-state">
          <h3>Randevu bulunamadı</h3>
          <p>${durum ? 'Bu durumda randevu bulunmuyor.' : 'Henüz randevu oluşturmadınız.'}</p>
          <a href="randevu-olustur.php" class="btn btn-primary">Yeni Randevu Oluştur</a>
        </div>
      `;
      return;
    }
    
    let html = '';
    filtrelenmisRandevular.forEach(randevu => {
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
            <h3>${randevu.konu}</h3>
            <span class="durum-badge ${durumClass}">${durumText}</span>
          </div>
          
          <div class="randevu-info">
            <div class="info-item">
              <span class="info-label">Öğretim Üyesi</span>
              <span class="info-value">${randevu.ogretim_uyesi_adi} ${randevu.ogretim_uyesi_unvan ? '(' + randevu.ogretim_uyesi_unvan + ')' : ''}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Bölüm</span>
              <span class="info-value">${randevu.ogretim_uyesi_bolum || 'Belirtilmemiş'}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Tarih</span>
              <span class="info-value">${tarih}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Saat</span>
              <span class="info-value">${saat}</span>
            </div>
          </div>
          
          ${randevu.aciklama ? `<p style="color: #666; margin: 15px 0;">${randevu.aciklama}</p>` : ''}
          
          <div class="randevu-actions">
            ${randevu.durum === 'bekliyor' || randevu.durum === 'onaylandi' ? `
              <button class="btn-small btn-danger" onclick="randevuIptal(${randevu.id})">İptal Et</button>
            ` : ''}
            ${randevu.durum === 'onaylandi' ? `
              <button class="btn-small btn-secondary" onclick="randevuHatirlatma(${randevu.id})">Hatırlatma Gönder</button>
            ` : ''}
          </div>
        </div>
      `;
    });
    
    container.innerHTML = html;
  } catch (error) {
    console.error('Hata:', error);
    container.innerHTML = `
      <div class="empty-state">
        <h3>Hata</h3>
        <p>Randevular yüklenirken bir hata oluştu. Lütfen sayfayı yenileyin.</p>
      </div>
    `;
  }
}

// Randevu iptal et
async function randevuIptal(randevuId) {
  if (!confirm('Bu randevuyu iptal etmek istediğinizden emin misiniz?')) {
    return;
  }
  
  try {
    const response = await fetch(`${API_BASE_URL}/randevu/${randevuId}`, {
      method: 'DELETE'
    });
    
    if (response.ok) {
      alert('Randevu başarıyla iptal edildi');
      randevulariYukle();
    } else {
      const data = await response.json();
      alert(data.detail || 'Randevu iptal edilemedi');
    }
  } catch (error) {
    console.error('Hata:', error);
    alert('Bağlantı hatası. Lütfen tekrar deneyin.');
  }
}

// Randevu hatırlatma gönder
async function randevuHatirlatma(randevuId) {
  try {
    const response = await fetch(`${API_BASE_URL}/randevu/${randevuId}/hatirlatma-gonder`);
    
    if (response.ok) {
      alert('Hatırlatma başarıyla gönderildi');
    } else {
      const data = await response.json();
      alert(data.detail || 'Hatırlatma gönderilemedi');
    }
  } catch (error) {
    console.error('Hata:', error);
    alert('Bağlantı hatası. Lütfen tekrar deneyin.');
  }
}
</script>

<?php include "footer.php"; ?>

</body>
</html>


