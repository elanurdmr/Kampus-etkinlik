<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Output buffering başlat
ob_start();

try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    include "db.php";
    require_once "lang.php";
    require_once "kulup_etkinlikleri_mock.php";
} catch (Exception $e) {
    die("Hata: " . $e->getMessage());
}

// Aktif sayfa ismini tespit ediyoruz
$currentPage = basename($_SERVER['PHP_SELF']);
$kulupEtkinlikleri = getKulupEtkinlikleriMock();
$oneCikanEtkinlikler = array_values(array_filter($kulupEtkinlikleri, fn($e) => !empty($e['one_cikan'])));
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= t('Kampüs Etkinlik Takip Sistemi', 'Campus Event Tracking System') ?></title>
  <link rel="stylesheet" href="style.css">
  <style>
    .yaklasan-popup {
      position: fixed;
      right: 20px;
      bottom: 20px;
      max-width: 320px;
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.25);
      padding: 16px 18px;
      z-index: 999;
      display: none;
      border-left: 4px solid #c41e3a;
    }
    .yaklasan-popup-baslik {
      font-weight: 700;
      font-size: 0.95em;
      margin-bottom: 4px;
      color: #c41e3a;
    }
    .yaklasan-popup-icerik {
      font-size: 0.9em;
      color: #444;
      margin-bottom: 8px;
    }
    .yaklasan-popup-geri-sayim {
      font-size: 0.85em;
      font-weight: 600;
      color: #c41e3a;
      margin-bottom: 8px;
    }
    .yaklasan-popup-alt {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.8em;
      color: #777;
    }
    .yaklasan-popup-kapat {
      background: none;
      border: none;
      font-size: 1.2em;
      cursor: pointer;
      color: #999;
      padding: 0;
      margin-left: 8px;
    }
    .yaklasan-popup-kapat:hover {
      color: #666;
    }
    @media (max-width: 600px) {
      .yaklasan-popup {
        left: 10px;
        right: 10px;
      }
    }
  </style>
</head>
<body>

<?php include "navbar.php"; ?>

<section class="slider">
  <div class="slides">
    <?php
    $sql = "SELECT * FROM dersler";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo "
        <div class='slide-item'>
          <img src='{$row['foto']}' alt='{$row['ders_adi']}'>
          <div class='caption'>
            <h2>{$row['ders_adi']}</h2>
            <p>{$row['ogretim_uyesi']} | {$row['gun']} {$row['saat']}</p>
          </div>
        </div>";
      }
    }
    ?>
  </div>

  <button id='prevBtn' class='nav-btn'>&#10094;</button>
  <button id='nextBtn' class='nav-btn'>&#10095;</button>
</section>

<section class="duyurular">
  <h2><?= t('Duyurular', 'Announcements') ?></h2>
  <div class="duyuru-listesi">
    <?php if (!empty($oneCikanEtkinlikler)): ?>
      <?php foreach (array_slice($oneCikanEtkinlikler, 0, 3) as $etkinlik): ?>
        <div class="duyuru">
          <h3>
            <?php
              echo htmlspecialchars(
                $currentLang === 'en' && !empty($etkinlik['ad_en'])
                  ? $etkinlik['ad_en']
                  : $etkinlik['ad']
              );
            ?>
          </h3>
          <p>
            <?php
              echo htmlspecialchars(
                $currentLang === 'en' && !empty($etkinlik['kulup_en'])
                  ? $etkinlik['kulup_en']
                  : $etkinlik['kulup']
              );
            ?> |
            <?php echo htmlspecialchars($etkinlik['tarih']); ?> |
            <?php
              echo htmlspecialchars(
                $currentLang === 'en' && !empty($etkinlik['konum_en'])
                  ? $etkinlik['konum_en']
                  : $etkinlik['konum']
              );
            ?>
          </p>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="duyuru">
        <h3>Şu anda öne çıkan bir etkinlik yok</h3>
        <p>Yeni kulüp etkinlikleri eklendikçe burada göreceksiniz.</p>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include "footer.php"; ?>

<script src="script.js"></script>
<script>
const ONERI_API_URL = 'http://localhost:8000/api/oneri';
const CURRENT_USER_ID = <?php echo isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0; ?>;
let yaklasanEtkinlik = null;
let countdownInterval = null;

function baslatCountdown() {
  if (!yaklasanEtkinlik) return;
  const hedef = new Date(yaklasanEtkinlik.tarih);
  const countdownEl = document.getElementById('yaklasanCountdown');

  function guncelle() {
    const now = new Date();
    const diff = hedef - now;
    if (diff <= 0) {
      clearInterval(countdownInterval);
      countdownEl.textContent = 'Etkinlik başladı!';
      return;
    }
    const minutes = Math.floor(diff / 60000);
    const gun = Math.floor(minutes / (60 * 24));
    const saat = Math.floor((minutes % (60 * 24)) / 60);
    const dakika = minutes % 60;
    countdownEl.textContent = `Kalan süre: ${gun}g ${saat}s ${dakika}d`;
  }

  guncelle();
  countdownInterval = setInterval(guncelle, 60000);
}

async function yukleYaklasanPopup() {
  if (!CURRENT_USER_ID) return;
  const popup = document.getElementById('yaklasanPopup');

  try {
    const res = await fetch(`${ONERI_API_URL}/kullanici-yaklasan/${CURRENT_USER_ID}`);
    const data = await res.json();
    if (!data.success || !data.etkinlikler || data.etkinlikler.length === 0) return;

    // En yakın etkinlik ilk sırada
    yaklasanEtkinlik = data.etkinlikler[0];
    const hedef = new Date(yaklasanEtkinlik.tarih);
    const now = new Date();
    const diffHours = (hedef - now) / 3600000;

    // Sadece 24 saatten az kalmışsa popup göster
    if (diffHours <= 0 || diffHours > 24) return;

    document.getElementById('yaklasanBaslik').textContent = yaklasanEtkinlik.etkinlik_adi;
    document.getElementById('yaklasanDetay').textContent =
      `${yaklasanEtkinlik.kulup_adi || 'Kulüp Etkinliği'} • ${hedef.toLocaleString('tr-TR')}`;

    popup.style.display = 'block';
    baslatCountdown();
  } catch (e) {
    console.error('Yaklaşan etkinlik popup hatası:', e);
  }
}

function kapatYaklasanPopup() {
  const popup = document.getElementById('yaklasanPopup');
  popup.style.display = 'none';
  if (countdownInterval) clearInterval(countdownInterval);
}

document.addEventListener('DOMContentLoaded', () => {
  yukleYaklasanPopup();
});
</script>

<div id="yaklasanPopup" class="yaklasan-popup">
  <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">
    <div>
      <div class="yaklasan-popup-baslik">Bu etkinliğe çok az kaldı!</div>
      <div id="yaklasanBaslik" class="yaklasan-popup-icerik"></div>
      <div id="yaklasanCountdown" class="yaklasan-popup-geri-sayim"></div>
    </div>
    <button class="yaklasan-popup-kapat" onclick="kapatYaklasanPopup()">&times;</button>
  </div>
  <div class="yaklasan-popup-alt">
    <span id="yaklasanDetay"></span>
    <a href="etkinlik-onerileri.php" style="color:#c41e3a; font-weight:600; text-decoration:none;">Detay</a>
  </div>
</div>

</body>
</html>
<?php ob_end_flush(); ?>
