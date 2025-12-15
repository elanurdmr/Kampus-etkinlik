<?php 
include "db.php"; 
require_once "kulup_etkinlikleri_mock.php";
$currentPage = basename($_SERVER['PHP_SELF']); 
$kulupEtkinlikleri = getKulupEtkinlikleriMock();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Etkinlikler | Kampüs Etkinlik Takip Sistemi</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
  // Aktif sayfa ismini tespit ediyoruz
  $currentPage = basename($_SERVER['PHP_SELF']);
?>
<?php include "navbar.php"; ?>



<main class="etkinlikler">
  <?php foreach ($kulupEtkinlikleri as $etkinlik): ?>
    <div class="event-card">
      <h3><?php echo htmlspecialchars($etkinlik['kulup']); ?></h3>
      <p><b>Etkinlik:</b> <?php echo htmlspecialchars($etkinlik['ad']); ?></p>
      <p><b>Tarih:</b> <?php echo htmlspecialchars($etkinlik['tarih']); ?></p>
      <p><?php echo htmlspecialchars($etkinlik['aciklama']); ?></p>
      <p><b>Konum:</b> <?php echo htmlspecialchars($etkinlik['konum']); ?></p>
      <button class="katil-btn" data-event="<?php echo htmlspecialchars($etkinlik['ad']); ?>">Katıl</button>
    </div>
  <?php endforeach; ?>
</main>

<!-- POPUP FORM -->
<div id="popup" class="popup">
  <div class="popup-content">
    <span class="close">&times;</span>
    <h2>Etkinliğe Katılım Formu</h2>
    <p id="event-name"></p>
    <input type="text" id="ad" placeholder="Adınız">
    <input type="text" id="soyad" placeholder="Soyadınız">
    <input type="email" id="email" placeholder="E-posta adresiniz">
    <input type="tel" id="telefon" placeholder="Telefon numaranız">
    <button id="onayla">Gönder</button>
  </div>
</div>

<?php include "footer.php"; ?>

<script src="script.js"></script>
</body>
</html>
