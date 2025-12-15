<?php
session_start();
include "db.php";
require_once "lang.php";
$currentPage = basename($_SERVER['PHP_SELF']);
// Giriş yapmış kullanıcının ID'si (yoksa demo 1)
$kullanici_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 1;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Content-Type" content="text
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= t('İlgi Alanlarım | Kampüs Sistemi', 'My Interests | Campus System') ?></title>
  <link rel="stylesheet" href="style.css">
  <style>
    .ilgi-container {
      max-width: 800px;
      margin: 40px auto;
      padding: 30px;
    }
    
    .ilgi-baslik {
      text-align: center;
      color: #c41e3a;
      margin-bottom: 20px;
    }
    
    .aciklama {
      text-align: center;
      color: #666;
      margin-bottom: 40px;
      font-size: 1.1em;
    }
    
    .ilgi-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 15px;
      margin-bottom: 30px;
    }
    
    .ilgi-item {
      background: white;
      border: 2px solid #e9ecef;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .ilgi-item:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(196, 30, 58, 0.2);
    }
    
    .ilgi-item.selected {
      background: linear-gradient(135deg, #c41e3a 0%, #8b1528 100%);
      color: white;
      border-color: #c41e3a;
    }
    
    .ilgi-icon {
      font-size: 2.5em;
      margin-bottom: 10px;
    }
    
    .ilgi-adi {
      font-weight: 600;
      font-size: 1.1em;
    }
    
    .kaydet-btn {
      background: linear-gradient(135deg, #c41e3a 0%, #8b1528 100%);
      color: white;
      border: none;
      padding: 15px 40px;
      font-size: 1.1em;
      border-radius: 8px;
      cursor: pointer;
      display: block;
      margin: 0 auto;
      transition: all 0.3s;
    }
    
    .kaydet-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(196, 30, 58, 0.4);
    }
    
    .mesaj {
      text-align: center;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      display: none;
    }
    
    .mesaj.success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    
    .mesaj.error {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    
    .oneriler-link {
      text-align: center;
      margin-top: 20px;
    }
    
    .oneriler-link a {
      color: #c41e3a;
      text-decoration: none;
      font-weight: 600;
      font-size: 1.1em;
    }
  </style>
</head>
<body>

<?php include "navbar.php"; ?>

<main class="ilgi-container">
  <h1 class="ilgi-baslik"><?= t('İlgi Alanlarını Seç', 'Choose Your Interests') ?></h1>
  <p class="aciklama">
    <?= t(
      'İlgilendiğin alanları seç, sana özel etkinlik önerileri alalım!',
      'Select the topics you are interested in and we will suggest events just for you!'
    ); ?>
  </p>

  <div id="mesaj" class="mesaj"></div>

  <div id="ilgiGrid" class="ilgi-grid">
    <!-- JavaScript ile dolacak -->
  </div>

  <button onclick="kaydetIlgiAlanlari()" class="kaydet-btn">
    💾 <?= t('İlgi Alanlarımı Kaydet', 'Save My Interests') ?>
  </button>

  <div class="oneriler-link">
    <a href="etkinlik-onerileri.php">
      <?= t('Etkinlik Önerilerine Git', 'Go to Event Suggestions') ?>
    </a>
  </div>
</main>

<?php include "footer.php"; ?>

<script>
const API_URL = 'http://localhost:8000/api/oneri';
const KULLANICI_ID = <?= $kullanici_id ?>;
let secilenIlgiAlanlar = new Set();

// Icon eşleştirmeleri
const iconlar = {
  'Spor': '',
  'Müzik': '',
  'Teknoloji': '',
  'Sanat': '',
  'Edebiyat': '',
  'Sinema': '',
  'Tiyatro': '',
  'Fotoğrafçılık': '',
  'Sosyal Sorumluluk': '',
  'Girişimcilik': ''
};

// Sayfa yüklendiğinde
window.addEventListener('DOMContentLoaded', async () => {
  await yukleIlgiAlanlari();
  await yukleKullaniciIlgiAlanlari();
});

async function yukleIlgiAlanlari() {
  try {
    const response = await fetch(`${API_URL}/ilgi-alanlari`);
    const data = await response.json();
    
    if (data.success) {
      const grid = document.getElementById('ilgiGrid');
      grid.innerHTML = '';
      
      data.data.forEach(alan => {
        const icon = iconlar[alan.alan_adi] || '';
        const div = document.createElement('div');
        div.className = 'ilgi-item';
        div.dataset.id = alan.id;
        div.innerHTML = `
          <div class="ilgi-icon">${icon}</div>
          <div class="ilgi-adi">${alan.alan_adi}</div>
        `;
        div.onclick = () => toggleIlgiAlani(alan.id, div);
        grid.appendChild(div);
      });
    }
  } catch (error) {
    console.error('İlgi alanları yüklenemedi:', error);
    gosterMesaj('<?= t('İlgi alanları yüklenirken hata oluştu', 'An error occurred while loading interests') ?>', 'error');
  }
}

async function yukleKullaniciIlgiAlanlari() {
  try {
    const response = await fetch(`${API_URL}/kullanici-ilgi-alanlari/${KULLANICI_ID}`);
    const data = await response.json();
    
    if (data.success) {
      data.data.forEach(alan => {
        secilenIlgiAlanlar.add(alan.id);
        const item = document.querySelector(`[data-id="${alan.id}"]`);
        if (item) item.classList.add('selected');
      });
    }
  } catch (error) {
    console.error('Kullanıcı ilgi alanları yüklenemedi:', error);
  }
}

function toggleIlgiAlani(id, element) {
  if (secilenIlgiAlanlar.has(id)) {
    secilenIlgiAlanlar.delete(id);
    element.classList.remove('selected');
  } else {
    secilenIlgiAlanlar.add(id);
    element.classList.add('selected');
  }
}

async function kaydetIlgiAlanlari() {
  if (secilenIlgiAlanlar.size === 0) {
    gosterMesaj('<?= t('Lütfen en az bir ilgi alanı seçin', 'Please select at least one interest') ?>', 'error');
    return;
  }
  
  try {
    const response = await fetch(`${API_URL}/kullanici-ilgi-alanlari`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        kullanici_id: KULLANICI_ID,
        ilgi_alani_ids: Array.from(secilenIlgiAlanlar)
      })
    });
    
    const data = await response.json();
    
    if (data.success) {
      gosterMesaj('<?= t('İlgi alanlarınız kaydedildi!', 'Your interests have been saved!') ?>', 'success');
      setTimeout(() => {
        window.location.href = 'etkinlik-onerileri.php';
      }, 1500);
    } else {
      gosterMesaj('<?= t('Kaydetme sırasında hata oluştu', 'An error occurred while saving') ?>', 'error');
    }
  } catch (error) {
    console.error('Kaydetme hatası:', error);
    gosterMesaj('<?= t('Bir hata oluştu', 'An error occurred') ?>', 'error');
  }
}

function gosterMesaj(mesaj, tip) {
  const mesajDiv = document.getElementById('mesaj');
  mesajDiv.textContent = mesaj;
  mesajDiv.className = `mesaj ${tip}`;
  mesajDiv.style.display = 'block';
  
  if (tip === 'success') {
    setTimeout(() => {
      mesajDiv.style.display = 'none';
    }, 3000);
  }
}
</script>

</body>
</html>


