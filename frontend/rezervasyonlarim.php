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
  <title>Rezervasyonlarım | Kampüs Sistemi</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .rezervasyonlar-container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 20px;
    }
    
    .page-header {
      text-align: center;
      margin-bottom: 40px;
    }
    
    .page-header h1 {
      color: #333;
      font-size: 2.5em;
      margin-bottom: 10px;
    }
    
    .filter-tabs {
      display: flex;
      gap: 15px;
      margin-bottom: 30px;
      justify-content: center;
    }
    
    .filter-tab {
      padding: 14px 30px;
      border: 2px solid #ddd;
      background: white;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s;
      font-weight: 600;
      font-size: 1.05em;
    }
    
    .filter-tab:hover {
      border-color: #b30000;
      background: #fff5f5;
    }
    
    .filter-tab.active {
      background: linear-gradient(135deg, #b30000 0%, #8b0000 100%);
      color: white;
      border-color: #b30000;
    }
    
    .rezervasyon-list {
      display: grid;
      gap: 20px;
    }
    
    .rezervasyon-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      padding: 25px;
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 20px;
      transition: transform 0.3s, box-shadow 0.3s;
      border-left: 5px solid #b30000;
    }
    
    .rezervasyon-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .rezervasyon-card.gecmis {
      border-left-color: #9e9e9e;
      opacity: 0.8;
    }
    
    .rezervasyon-info h3 {
      font-size: 1.5em;
      color: #333;
      margin-bottom: 15px;
    }
    
    .rezervasyon-detay {
      display: grid;
      gap: 10px;
      color: #666;
      font-size: 1.05em;
    }
    
    .detay-item {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .rezervasyon-actions {
      display: flex;
      flex-direction: column;
      gap: 10px;
      justify-content: center;
    }
    
    .action-btn {
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s;
      text-align: center;
      white-space: nowrap;
    }
    
    .btn-edit {
      background: #2196F3;
      color: white;
    }
    
    .btn-edit:hover {
      background: #1976D2;
      transform: scale(1.05);
    }
    
    .btn-delete {
      background: linear-gradient(135deg, #b30000 0%, #8b0000 100%);
      color: white;
      box-shadow: 0 2px 6px rgba(179,0,0,0.3);
    }
    
    .btn-delete:hover {
      background: linear-gradient(135deg, #8b0000 0%, #6b0000 100%);
      transform: scale(1.05);
      box-shadow: 0 4px 10px rgba(179,0,0,0.4);
    }
    
    .loading {
      text-align: center;
      padding: 60px 20px;
      font-size: 1.3em;
      color: #666;
    }
    
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: #999;
    }
    
    .empty-state h3 {
      font-size: 1.8em;
      margin-bottom: 15px;
    }
    
    .success-message {
      background: #e8f5e9;
      color: #2e7d32;
      padding: 15px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      display: none;
      text-align: center;
    }
    
    .error-message {
      background: #ffebee;
      color: #c62828;
      padding: 15px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      display: none;
      text-align: center;
    }
    
    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 1000;
      justify-content: center;
      align-items: center;
    }
    
    .modal.active {
      display: flex;
    }
    
    .modal-content {
      background: white;
      padding: 30px;
      border-radius: 12px;
      max-width: 500px;
      width: 90%;
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    
    .modal-header {
      margin-bottom: 20px;
    }
    
    .modal-header h3 {
      font-size: 1.5em;
      color: #333;
    }
    
    .modal-body {
      margin-bottom: 25px;
      color: #666;
      line-height: 1.6;
    }
    
    .modal-actions {
      display: flex;
      gap: 10px;
      justify-content: flex-end;
    }
    
    .modal-btn {
      padding: 12px 24px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s;
    }
    
    .modal-btn-cancel {
      background: #e0e0e0;
      color: #333;
    }
    
    .modal-btn-cancel:hover {
      background: #bdbdbd;
    }
    
    .modal-btn-confirm {
      background: linear-gradient(135deg, #b30000 0%, #8b0000 100%);
      color: white;
    }
    
    .modal-btn-confirm:hover {
      background: linear-gradient(135deg, #8b0000 0%, #6b0000 100%);
      box-shadow: 0 4px 10px rgba(179,0,0,0.3);
    }
    
    @media (max-width: 768px) {
      .rezervasyon-card {
        grid-template-columns: 1fr;
      }
      
      .rezervasyon-actions {
        flex-direction: row;
      }
      
      .filter-tabs {
        flex-direction: column;
      }
      
      .filter-tab {
        width: 100%;
      }
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="rezervasyonlar-container">
  <div class="page-header">
    <h1>Rezervasyonlarım</h1>
    <p>Kütüphane rezervasyonlarınızı görüntüleyin ve yönetin</p>
    <div style="margin-top: 20px;">
      <a href="rezervasyon-yap.php" style="display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #b30000 0%, #8b0000 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s; box-shadow: 0 4px 10px rgba(179,0,0,0.3);">
        Yeni Rezervasyon Yap
      </a>
    </div>
  </div>

  <div id="success-message" class="success-message"></div>
  <div id="error-message" class="error-message"></div>

  <div class="filter-tabs">
    <button class="filter-tab active" onclick="filterRezervasyonlar('aktif')">
      Rezervasyonlarım
    </button>
    <button class="filter-tab" onclick="filterRezervasyonlar('gecmis')">
      Geçmiş Rezervasyonlarım
    </button>
  </div>

  <div id="loading" class="loading">
    <p>Rezervasyonlar yükleniyor...</p>
  </div>

  <div id="rezervasyon-list" class="rezervasyon-list" style="display: none;"></div>
  
  <div id="empty-state" class="empty-state" style="display: none;">
    <h3>Henüz Rezervasyon Yok</h3>
    <p>Henüz hiç rezervasyon yapmamışsınız.</p>
    <a href="rezervasyon-yap.php" style="display: inline-block; margin-top: 20px; padding: 12px 30px; background: linear-gradient(135deg, #b30000 0%, #8b0000 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; box-shadow: 0 4px 10px rgba(179,0,0,0.3);">
      İlk Rezervasyonunuzu Yapın
    </a>
  </div>
</div>

<!-- Silme Onay Modal -->
<div id="delete-modal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Rezervasyonu Sil</h3>
    </div>
    <div class="modal-body">
      <p>Bu rezervasyonu silmek istediğinizden emin misiniz?</p>
      <p><strong>Bu işlem geri alınamaz!</strong></p>
    </div>
    <div class="modal-actions">
      <button class="modal-btn modal-btn-cancel" onclick="closeDeleteModal()">
        İptal
      </button>
      <button class="modal-btn modal-btn-confirm" onclick="confirmDelete()">
        Sil
      </button>
    </div>
  </div>
</div>

<script>
const API_URL = 'http://localhost:8000/api/kutuphane';
// kullaniciId navbar.php'den geliyor
if (typeof window.kullaniciId === 'undefined') {
  window.kullaniciId = <?= $kullanici_id ?>;
}

let rezervasyonlar = [];
let currentFilter = 'aktif';
let deleteRezervasyonId = null;

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
  fetchRezervasyonlar();
});

// Rezervasyonları çek
async function fetchRezervasyonlar() {
  const loadingDiv = document.getElementById('loading');
  const listDiv = document.getElementById('rezervasyon-list');
  const emptyDiv = document.getElementById('empty-state');
  const errorDiv = document.getElementById('error-message');
  
  try {
    const response = await fetch(`${API_URL}/kullanici/${kullaniciId}/rezervasyonlar`);
    
    if (!response.ok) {
      throw new Error('Rezervasyonlar yüklenemedi');
    }
    
    rezervasyonlar = await response.json();
    
    loadingDiv.style.display = 'none';
    
    if (rezervasyonlar.length === 0) {
      emptyDiv.style.display = 'block';
    } else {
      listDiv.style.display = 'grid';
      displayRezervasyonlar();
    }
    
  } catch (error) {
    console.error('Hata:', error);
    loadingDiv.style.display = 'none';
    errorDiv.style.display = 'block';
    errorDiv.textContent = error.message;
  }
}

// Rezervasyonları filtrele ve göster
function filterRezervasyonlar(filter) {
  currentFilter = filter;
  
  // Tab'ları güncelle
  document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.classList.remove('active');
  });
  event.target.classList.add('active');
  
  displayRezervasyonlar();
}

// Rezervasyonları görüntüle
function displayRezervasyonlar() {
  const listDiv = document.getElementById('rezervasyon-list');
  const emptyDiv = document.getElementById('empty-state');
  
  const bugun = new Date();
  bugun.setHours(0, 0, 0, 0);
  
  // Filtreleme
  let filtered = rezervasyonlar.filter(rez => {
    const rezTarihi = new Date(rez.rezervasyon_tarihi);
    rezTarihi.setHours(0, 0, 0, 0);
    
    if (currentFilter === 'aktif') {
      return rezTarihi >= bugun;
    } else {
      return rezTarihi < bugun;
    }
  });
  
  if (filtered.length === 0) {
    listDiv.style.display = 'none';
    emptyDiv.style.display = 'block';
    emptyDiv.innerHTML = `
      <h3>Rezervasyon Yok</h3>
      <p>${currentFilter === 'aktif' ? 'Aktif rezervasyonunuz bulunmuyor.' : 'Geçmiş rezervasyonunuz bulunmuyor.'}</p>
    `;
    return;
  }
  
  emptyDiv.style.display = 'none';
  listDiv.style.display = 'grid';
  
  // Tarihe göre sırala (yeniden eskiye)
  filtered.sort((a, b) => new Date(b.rezervasyon_tarihi) - new Date(a.rezervasyon_tarihi));
  
  listDiv.innerHTML = filtered.map(rez => {
    const isGecmis = currentFilter === 'gecmis';
    const tarih = formatTarih(rez.rezervasyon_tarihi);
    
    return `
      <div class="rezervasyon-card ${isGecmis ? 'gecmis' : ''}">
        <div class="rezervasyon-info">
          <h3>${rez.kutuphane_adi || 'Doğuş Kütüphanesi'}</h3>
          <div class="rezervasyon-detay">
            ${rez.koltuk_no ? `
              <div class="detay-item" style="background: #f5f5f5; padding: 8px; border-radius: 6px; margin-bottom: 10px;">
                <strong style="color: #b30000; font-size: 1.2em;">Koltuk Numarası: ${rez.koltuk_no}</strong>
              </div>
            ` : ''}
            <div class="detay-item">
              <strong>Tarih:</strong> ${tarih}
            </div>
            <div class="detay-item">
              <strong>Saat:</strong> ${rez.baslangic_saati} - ${rez.bitis_saati}
            </div>
            <div class="detay-item">
              <strong>Rezervasyon No:</strong> #${rez.id}
            </div>
          </div>
        </div>
        <div class="rezervasyon-actions">
          ${!isGecmis ? `
            <button class="action-btn btn-delete" onclick="openDeleteModal(${rez.id})">
              Sil
            </button>
          ` : ''}
        </div>
      </div>
    `;
  }).join('');
}

// Tarih formatlama
function formatTarih(tarihStr) {
  const tarih = new Date(tarihStr);
  const options = { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric',
    weekday: 'long'
  };
  return tarih.toLocaleDateString('tr-TR', options);
}

// Silme modalını aç
function openDeleteModal(rezId) {
  deleteRezervasyonId = rezId;
  document.getElementById('delete-modal').classList.add('active');
}

// Silme modalını kapat
function closeDeleteModal() {
  deleteRezervasyonId = null;
  document.getElementById('delete-modal').classList.remove('active');
}

// Silmeyi onayla
async function confirmDelete() {
  if (!deleteRezervasyonId) return;
  
  const successDiv = document.getElementById('success-message');
  const errorDiv = document.getElementById('error-message');
  
  try {
    const response = await fetch(`${API_URL}/rezervasyon/${deleteRezervasyonId}`, {
      method: 'DELETE'
    });
    
    if (!response.ok) {
      throw new Error('Rezervasyon silinemedi');
    }
    
    successDiv.style.display = 'block';
    successDiv.textContent = 'Rezervasyon başarıyla silindi!';
    
    setTimeout(() => {
      successDiv.style.display = 'none';
    }, 3000);
    
    closeDeleteModal();
    
    // Listeyi yenile
    await fetchRezervasyonlar();
    
  } catch (error) {
    console.error('Hata:', error);
    errorDiv.style.display = 'block';
    errorDiv.textContent = error.message;
    
    setTimeout(() => {
      errorDiv.style.display = 'none';
    }, 3000);
    
    closeDeleteModal();
  }
}
</script>

<?php include 'footer.php'; ?>

</body>
</html>








