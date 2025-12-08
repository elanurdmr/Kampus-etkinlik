<?php
session_start();
include "db.php";
$currentPage = basename($_SERVER['PHP_SELF']);
// Demo kullanıcı ID'si
$kullanici_id = 1; // Gerek sistemde session'dan alınacak
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Etkinlik nerileri | Kamps Sistemi</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .oneriler-container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 30px;
    }
    
    .baslik {
      text-align: center;
      color: #c41e3a;
      margin-bottom: 15px;
    }
    
    .alt-baslik {
      text-align: center;
      color: #666;
      margin-bottom: 40px;
    }
    
    .etkinlik-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
      gap: 25px;
      margin-bottom: 30px;
    }
    
    .etkinlik-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
      overflow: hidden;
      transition: all 0.3s;
    }
    
    .etkinlik-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(196, 30, 58, 0.2);
    }
    
    .etkinlik-header {
      background: linear-gradient(135deg, #c41e3a 0%, #8b1528 100%);
      color: white;
      padding: 20px;
    }
    
    .etkinlik-adi {
      font-size: 1.3em;
      font-weight: 600;
      margin-bottom: 5px;
    }
    
    .kulup-adi {
      opacity: 0.9;
      font-size: 0.95em;
    }
    
    .etkinlik-body {
      padding: 20px;
    }
    
    .eslesme-badge {
      display: inline-block;
      background: #28a745;
      color: white;
      padding: 5px 15px;
      border-radius: 20px;
      font-size: 0.85em;
      font-weight: 600;
      margin-bottom: 15px;
    }
    
    .etkinlik-detay {
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      color: #666;
    }
    
    .etkinlik-detay-icon {
      margin-right: 10px;
      font-size: 1.2em;
    }
    
    .button-group {
      display: flex;
      gap: 10px;
      margin-top: 20px;
    }
    
    .btn {
      flex: 1;
      padding: 12px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s;
    }
    
    .btn-katil {
      background: #28a745;
      color: white;
    }
    
    .btn-katil:hover {
      background: #218838;
    }
    
    .btn-katilma {
      background: #dc3545;
      color: white;
    }
    
    .btn-katilma:hover {
      background: #c82333;
    }
    
    .btn-disabled {
      background: #6c757d;
      cursor: not-allowed;
      opacity: 0.6;
    }
    
    .tercih-badge {
      padding: 8px 15px;
      border-radius: 20px;
      font-weight: 600;
      text-align: center;
    }
    
    .tercih-katilacak {
      background: #d4edda;
      color: #155724;
    }
    
    .tercih-katilmayacak {
      background: #f8d7da;
      color: #721c24;
    }
    
    /* Popup Modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.7);
      animation: fadeIn 0.3s;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    
    .modal-content {
      background-color: white;
      margin: 10% auto;
      padding: 0;
      border-radius: 15px;
      max-width: 500px;
      box-shadow: 0 5px 30px rgba(0,0,0,0.3);
      animation: slideDown 0.3s;
    }
    
    @keyframes slideDown {
      from { transform: translateY(-50px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
    
    .modal-header {
      background: linear-gradient(135deg, #c41e3a 0%, #8b1528 100%);
      color: white;
      padding: 25px;
      border-radius: 15px 15px 0 0;
      text-align: center;
    }
    
    .modal-body {
      padding: 30px;
      text-align: center;
    }
    
    .modal-body h3 {
      margin-bottom: 15px;
      color: #333;
    }
    
    .modal-body p {
      color: #666;
      margin-bottom: 25px;
      line-height: 1.6;
    }
    
    .modal-buttons {
      display: flex;
      gap: 15px;
      justify-content: center;
    }
    
    .modal-btn {
      padding: 12px 30px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      font-size: 1em;
      transition: all 0.3s;
    }
    
    .close {
      color: white;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }
    
    .close:hover {
      opacity: 0.8;
    }
    
    .bos-durum {
      text-align: center;
      padding: 60px 20px;
      color: #666;
    }
    
    .bos-durum-icon {
      font-size: 5em;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>

<?php include "navbar.php"; ?>

<main class="oneriler-container">
  <h1 class="baslik">Senin İçin Önerilen Etkinlikler</h1>
  <p class="alt-baslik">İlgi alanlarına göre özel olarak seçildi!</p>

  <div id="etkinlikGrid" class="etkinlik-grid">
    <!-- JavaScript ile dolacak -->
  </div>
</main>

<!-- Pop-up Modal -->
<div id="etkinlikModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <span class="close" onclick="kapatModal()">&times;</span>
      <h2 id="modalBaslik">Yeni Etkinlik Önerisi!</h2>
    </div>
    <div class="modal-body">
      <h3 id="modalEtkinlikAdi"></h3>
      <p id="modalEtkinlikDetay"></p>
      <div class="modal-buttons">
        <button class="modal-btn btn-katil" onclick="cevapla('katilacak')">
          Katılacağım
        </button>
        <button class="modal-btn btn-katilma" onclick="cevapla('katilmayacak')">
          Katılmayacağım
        </button>
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>

<script>
const API_URL = 'http://localhost:8000/api/oneri';
const KULLANICI_ID = <?= $kullanici_id ?>;
let mevcutEtkinlikId = null;

window.addEventListener('DOMContentLoaded', () => {
  yukleOneriler();
});

async function yukleOneriler() {
  try {
    const response = await fetch(`${API_URL}/oneriler/${KULLANICI_ID}`);
    const data = await response.json();
    
    const grid = document.getElementById('etkinlikGrid');
    
    if (!data.success || data.data.length === 0) {
      grid.innerHTML = `
        <div class="bos-durum">
          <div class="bos-durum-icon"></div>
          <h3>Öneri Bulunamadı</h3>
          <p>${data.message || 'Şu anda sana uygun etkinlik önerisi yok.'}</p>
          <a href="ilgi-alanlari.php" style="color: #c41e3a; font-weight: 600;">
            İlgi Alanlarımı Düzenle
          </a>
        </div>
      `;
      return;
    }
    
    grid.innerHTML = '';
    
    data.data.forEach((etkinlik, index) => {
      const tarih = new Date(etkinlik.tarih);
      const card = document.createElement('div');
      card.className = 'etkinlik-card';
      card.innerHTML = `
        <div class="etkinlik-header">
          <div class="etkinlik-adi">${etkinlik.etkinlik_adi}</div>
          <div class="kulup-adi">${etkinlik.kulup.kulup_adi}</div>
        </div>
        <div class="etkinlik-body">
          <div class="eslesme-badge">
            🤖 AI Skoru: ${etkinlik.eslesme_skoru}%
          </div>
          ${etkinlik.ai_analiz ? `
          <details style="margin-top: 10px; font-size: 0.85em; color: #666;">
            <summary style="cursor: pointer; font-weight: 600; color: #c41e3a;">
              🤖 AI Analizi
            </summary>
            <div style="padding: 10px; background: #f8f9fa; border-radius: 5px; margin-top: 5px;">
              <div>• İçerik Benzerliği: ${etkinlik.ai_analiz.icerik_benzerligi}%</div>
              <div>• Zaman Uygunluğu: ${etkinlik.ai_analiz.zaman_skoru}%</div>
              <div>• Davranış Uyumu: ${etkinlik.ai_analiz.davranis_skoru}%</div>
              <div>• Popülerlik: ${etkinlik.ai_analiz.populerlik_skoru}%</div>
              <div>• Ortak İlgi Alanı: ${etkinlik.ai_analiz.ortak_ilgi_alan_sayisi} adet</div>
            </div>
          </details>
          ` : ''}
          <div class="etkinlik-detay">
            <span class="etkinlik-detay-icon"></span>
            ${tarih.toLocaleDateString('tr-TR', { day: 'numeric', month: 'long', year: 'numeric' })}
          </div>
          <div class="etkinlik-detay">
            <span class="etkinlik-detay-icon"></span>
            ${tarih.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' })}
          </div>
          ${etkinlik.konum ? `
          <div class="etkinlik-detay">
            <span class="etkinlik-detay-icon">📍</span>
            ${etkinlik.konum}
          </div>
          ` : ''}
          <div style="margin-top: 15px; color: #666; font-size: 0.95em;">
            ${etkinlik.aciklama || 'Detaylı bilgi için kulüp ile iletişime geçin.'}
          </div>
          
          <div class="button-group">
            ${etkinlik.tercih_durumu ? `
              <div class="tercih-badge tercih-${etkinlik.tercih_durumu}">
                ${etkinlik.tercih_durumu === 'katilacak' ? '✅ Katılacaksın' : '❌ Katılmayacaksın'}
              </div>
            ` : `
              <button class="btn btn-katil" onclick="gosterPopup(${etkinlik.id}, '${etkinlik.etkinlik_adi}', '${etkinlik.kulup.kulup_adi}', '${tarih.toLocaleString('tr-TR')}')">
                ✅ Katılacağım
              </button>
              <button class="btn btn-katilma" onclick="tercihKaydet(${etkinlik.id}, 'katilmayacak')">
                ❌ Katılmayacağım
              </button>
            `}
          </div>
        </div>
      `;
      grid.appendChild(card);
      
      // İlk etkinlik iin pop-up gster (eer tercih edilmemise)
      if (index === 0 && !etkinlik.tercih_durumu) {
        setTimeout(() => {
          gosterPopup(etkinlik.id, etkinlik.etkinlik_adi, etkinlik.kulup.kulup_adi, tarih.toLocaleString('tr-TR'));
        }, 500);
      }
    });
  } catch (error) {
    console.error('neriler yklenemedi:', error);
  }
}

function gosterPopup(etkinlikId, etkinlikAdi, kulupAdi, tarih) {
  mevcutEtkinlikId = etkinlikId;
  document.getElementById('modalEtkinlikAdi').textContent = etkinlikAdi;
  document.getElementById('modalEtkinlikDetay').innerHTML = `
    <strong>${kulupAdi}</strong><br>
    ${tarih}
  `;
  document.getElementById('etkinlikModal').style.display = 'block';
}

function kapatModal() {
  document.getElementById('etkinlikModal').style.display = 'none';
  mevcutEtkinlikId = null;
}

async function cevapla(durum) {
  if (!mevcutEtkinlikId) return;
  
  await tercihKaydet(mevcutEtkinlikId, durum);
  kapatModal();
}

async function tercihKaydet(etkinlikId, durum) {
  try {
    const response = await fetch(`${API_URL}/etkinlik-tercih`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        kullanici_id: KULLANICI_ID,
        etkinlik_id: etkinlikId,
        durum: durum
      })
    });
    
    const data = await response.json();
    
    if (data.success) {
      // Sayfayı yenile
      yukleOneriler();
    }
  } catch (error) {
    console.error('Tercih kaydedilemedi:', error);
  }
}

// Modal dıına tıklanınca kapat
window.onclick = function(event) {
  const modal = document.getElementById('etkinlikModal');
  if (event.target == modal) {
    kapatModal();
  }
}
</script>

</body>
</html>


