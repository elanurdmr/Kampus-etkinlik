<?php
session_start();
$currentPage = basename($_SERVER['PHP_SELF']);

// Kullanıcı ID'si (gerçek uygulamada session'dan gelecek)
$kullanici_id = $_SESSION['user_id'] ?? 1;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bildirimler | Kampüs Sistemi</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .bildirimler-container {
      max-width: 1000px;
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
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 15px;
    }
    
    .page-header h2 {
      margin: 0;
      font-size: 2em;
      display: flex;
      align-items: center;
      gap: 15px;
    }
    
    .badge-count {
      background: white;
      color: #b30000;
      padding: 8px 16px;
      border-radius: 20px;
      font-weight: bold;
      font-size: 0.9em;
    }
    
    .filters {
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      margin-bottom: 20px;
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
      align-items: center;
    }
    
    .btn {
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
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
    }
    
    .btn-secondary {
      background: #6c757d;
      color: white;
    }
    
    .btn-secondary:hover {
      background: #5a6268;
    }
    
    .bildirim-card {
      background: white;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 15px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: all 0.3s;
      border-left: 5px solid #ddd;
      display: flex;
      gap: 20px;
      align-items: start;
    }
    
    .bildirim-card:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      transform: translateY(-2px);
    }
    
    .bildirim-card.okunmamis {
      background: #fff5f5;
      border-left-color: #b30000;
    }
    
    .bildirim-icon {
      width: 50px;
      height: 50px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.65em;
      font-weight: bold;
      color: #333;
      flex-shrink: 0;
      text-align: center;
      padding: 5px;
      box-sizing: border-box;
    }
    
    .icon-onay {
      background: #d4edda;
      color: #155724;
    }
    
    .icon-red {
      background: #f8d7da;
      color: #721c24;
    }
    
    .icon-hatirlatma {
      background: #fff3cd;
      color: #856404;
    }
    
    .icon-sistem {
      background: #d1ecf1;
      color: #0c5460;
    }
    
    .bildirim-content {
      flex: 1;
    }
    
    .bildirim-header {
      display: flex;
      justify-content: space-between;
      align-items: start;
      margin-bottom: 10px;
      gap: 10px;
    }
    
    .bildirim-baslik {
      font-weight: 600;
      color: #333;
      font-size: 1.1em;
    }
    
    .bildirim-card.okunmamis .bildirim-baslik {
      color: #b30000;
    }
    
    .bildirim-tarih {
      color: #999;
      font-size: 0.85em;
      white-space: nowrap;
    }
    
    .bildirim-mesaj {
      color: #666;
      line-height: 1.6;
      margin-bottom: 15px;
    }
    
    .bildirim-actions {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }
    
    .btn-small {
      padding: 6px 12px;
      font-size: 0.85em;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.3s;
      font-weight: 600;
    }
    
    .btn-link {
      background: #b30000;
      color: white;
    }
    
    .btn-link:hover {
      background: #8b0000;
    }
    
    .btn-delete {
      background: #dc3545;
      color: white;
    }
    
    .btn-delete:hover {
      background: #c82333;
    }
    
    .btn-mark {
      background: #6c757d;
      color: white;
    }
    
    .btn-mark:hover {
      background: #5a6268;
    }
    
    .empty-state {
      text-align: center;
      padding: 80px 20px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .empty-state-icon {
      width: 80px;
      height: 80px;
      margin: 0 auto 20px;
      background: #f0f0f0;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2em;
      color: #999;
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
    
    @media (max-width: 768px) {
      .bildirim-card {
        flex-direction: column;
      }
      
      .bildirim-header {
        flex-direction: column;
        align-items: start;
      }
    }
  </style>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="bildirimler-container">
  <div class="page-header">
    <h2>
      Bildirimler
      <span class="badge-count" id="okunmamisSayisi">0</span>
    </h2>
    <button class="btn btn-secondary" onclick="tumunuOkunduIsaretle()">
      Tümünü Okundu İşaretle
    </button>
  </div>

  <div class="filters">
    <button class="btn btn-primary" onclick="filtreDegistir('tumunu')">
      Tümü
    </button>
    <button class="btn btn-secondary" onclick="filtreDegistir('okunmamis')">
      Okunmamışlar
    </button>
    <button class="btn btn-secondary" onclick="filtreDegistir('okunmus')">
      Okunmuşlar
    </button>
  </div>

  <div id="bildirimlerList" class="loading">
    <div class="spinner"></div>
    <p>Bildirimler yükleniyor...</p>
  </div>
</div>

<script>
const API_BASE_URL = 'http://localhost:8000/api/bildirimler';
// kullaniciId navbar.php'den geliyor, eğer yoksa buradan al
if (typeof window.kullaniciId === 'undefined') {
  window.kullaniciId = <?php echo $kullanici_id; ?>;
}
let aktifFiltre = 'tumu';

const bildirimIkonlari = {
  'randevu_onaylandi': { text: 'ONAY', class: 'icon-onay' },
  'randevu_reddedildi': { text: 'RED', class: 'icon-red' },
  'randevu_hatirlatma': { text: 'HATIRLATMA', class: 'icon-hatirlatma' },
  'sistem': { text: 'SISTEM', class: 'icon-sistem' }
};

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
  bildirimleriYukle();
  okunmamisSayisiGuncelle();
});

// Bildirimleri yükle
async function bildirimleriYukle() {
  const container = document.getElementById('bildirimlerList');
  container.innerHTML = '<div class="loading"><div class="spinner"></div><p>Bildirimler yükleniyor...</p></div>';
  
  try {
    let url = `${API_BASE_URL}/kullanici/${window.kullaniciId}/bildirimler`;
    
    if (aktifFiltre === 'okunmamis') {
      url += '?okunmamis_mi=true';
    } else if (aktifFiltre === 'okunmus') {
      url += '?okunmamis_mi=false';
    }
    
    const response = await fetch(url);
    if (!response.ok) throw new Error('Bildirimler yüklenemedi');
    
    const bildirimler = await response.json();
    
    if (bildirimler.length === 0) {
      container.innerHTML = `
        <div class="empty-state">
          <div class="empty-state-icon"></div>
          <h3>Bildirim Yok</h3>
          <p>${aktifFiltre === 'okunmamis' ? 'Okunmamış bildiriminiz bulunmuyor' : 'Henüz bildiriminiz yok'}</p>
        </div>
      `;
      return;
    }
    
    let html = '';
    bildirimler.forEach(bildirim => {
      const ikonBilgi = bildirimIkonlari[bildirim.tip] || bildirimIkonlari['sistem'];
      const tarih = new Date(bildirim.olusturma_tarihi).toLocaleString('tr-TR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
      
      html += `
        <div class="bildirim-card ${!bildirim.okundu ? 'okunmamis' : ''}" data-id="${bildirim.id}">
          <div class="bildirim-icon ${ikonBilgi.class}">
            ${ikonBilgi.text}
          </div>
          
          <div class="bildirim-content">
            <div class="bildirim-header">
              <div class="bildirim-baslik">${bildirim.baslik}</div>
              <div class="bildirim-tarih">${tarih}</div>
            </div>
            
            <div class="bildirim-mesaj">${bildirim.mesaj}</div>
            
            <div class="bildirim-actions">
              ${!bildirim.okundu ? `
                <button class="btn-small btn-mark" onclick="bildirimOkunduIsaretle(${bildirim.id})">
                  Okundu İşaretle
                </button>
              ` : ''}
              ${bildirim.ilgili_randevu_id ? `
                <button class="btn-small btn-link" onclick="randevuGit(${bildirim.ilgili_randevu_id})">
                  Randevuya Git
                </button>
              ` : ''}
              <button class="btn-small btn-delete" onclick="bildirimSil(${bildirim.id})">
                Sil
              </button>
            </div>
          </div>
        </div>
      `;
    });
    
    container.innerHTML = html;
  } catch (error) {
    console.error('Hata:', error);
    container.innerHTML = `
      <div class="empty-state">
        <div class="empty-state-icon"></div>
        <h3>Hata</h3>
        <p>Bildirimler yüklenirken bir hata oluştu</p>
      </div>
    `;
  }
}

// Okunmamış sayısını güncelle
async function okunmamisSayisiGuncelle() {
  try {
    const response = await fetch(`${API_BASE_URL}/kullanici/${window.kullaniciId}/bildirimler/okunmamis-sayisi`);
    if (!response.ok) return;
    
    const data = await response.json();
    document.getElementById('okunmamisSayisi').textContent = data.okunmamis_sayisi;
  } catch (error) {
    console.error('Hata:', error);
  }
}

// Bildirimi okundu işaretle
async function bildirimOkunduIsaretle(bildirimId) {
  try {
    const response = await fetch(`${API_BASE_URL}/bildirim/${bildirimId}/okundu`, {
      method: 'PUT'
    });
    
    if (response.ok) {
      bildirimleriYukle();
      okunmamisSayisiGuncelle();
    }
  } catch (error) {
    console.error('Hata:', error);
    alert('Bildirim güncellenemedi');
  }
}

// Tümünü okundu işaretle
async function tumunuOkunduIsaretle() {
  if (!confirm('Tüm bildirimleri okundu olarak işaretlemek istiyor musunuz?')) {
    return;
  }
  
  try {
    const response = await fetch(`${API_BASE_URL}/kullanici/${window.kullaniciId}/bildirimler/tumunu-okundu-isaretle`, {
      method: 'PUT'
    });
    
    if (response.ok) {
      alert('Tüm bildirimler okundu olarak işaretlendi');
      bildirimleriYukle();
      okunmamisSayisiGuncelle();
    }
  } catch (error) {
    console.error('Hata:', error);
    alert('İşlem başarısız');
  }
}

// Bildirimi sil
async function bildirimSil(bildirimId) {
  if (!confirm('Bu bildirimi silmek istediğinizden emin misiniz?')) {
    return;
  }
  
  try {
    const response = await fetch(`${API_BASE_URL}/bildirim/${bildirimId}`, {
      method: 'DELETE'
    });
    
    if (response.ok) {
      bildirimleriYukle();
      okunmamisSayisiGuncelle();
    } else {
      alert('Bildirim silinemedi');
    }
  } catch (error) {
    console.error('Hata:', error);
    alert('Bağlantı hatası');
  }
}

// Randevuya git
function randevuGit(randevuId) {
  window.location.href = 'randevularim.php';
}

// Filtre değiştir
function filtreDegistir(filtre) {
  aktifFiltre = filtre;
  
  // Buton stillerini güncelle
  document.querySelectorAll('.filters .btn').forEach(btn => {
    btn.classList.remove('btn-primary');
    btn.classList.add('btn-secondary');
  });
  event.target.classList.remove('btn-secondary');
  event.target.classList.add('btn-primary');
  
  bildirimleriYukle();
}
</script>

<?php include "footer.php"; ?>

</body>
</html>

