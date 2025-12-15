<?php 
include "db.php"; 
require_once "lang.php";
require_once "kulup_etkinlikleri_mock.php";
$currentPage = basename($_SERVER['PHP_SELF']); 
$kulupEtkinlikleri = getKulupEtkinlikleriMock();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= t('Etkinlikler | Kampüs Etkinlik Takip Sistemi', 'Events | Campus Event Tracking System') ?></title>
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
      <h3>
        <?php
          echo htmlspecialchars(
            $currentLang === 'en' && !empty($etkinlik['kulup_en'])
              ? $etkinlik['kulup_en']
              : $etkinlik['kulup']
          );
        ?>
      </h3>
      <p><b><?= t('Etkinlik:', 'Event:') ?></b>
        <?php
          echo htmlspecialchars(
            $currentLang === 'en' && !empty($etkinlik['ad_en'])
              ? $etkinlik['ad_en']
              : $etkinlik['ad']
          );
        ?>
      </p>
      <p><b><?= t('Tarih:', 'Date:') ?></b> <?php echo htmlspecialchars($etkinlik['tarih']); ?></p>
      <p>
        <?php
          echo htmlspecialchars(
            $currentLang === 'en' && !empty($etkinlik['aciklama_en'])
              ? $etkinlik['aciklama_en']
              : $etkinlik['aciklama']
          );
        ?>
      </p>
      <p><b><?= t('Konum:', 'Location:') ?></b>
        <?php
          echo htmlspecialchars(
            $currentLang === 'en' && !empty($etkinlik['konum_en'])
              ? $etkinlik['konum_en']
              : $etkinlik['konum']
          );
        ?>
      </p>
      <button class="katil-btn" data-event="<?php echo htmlspecialchars($etkinlik['ad']); ?>"><?= t('Katıl', 'Join') ?></button>
    </div>
  <?php endforeach; ?>
</main>

<!-- POPUP FORM -->
<div id="popup" class="popup">
  <div class="popup-content">
    <span class="close">&times;</span>
    <h2><?= t('Etkinliğe Katılım Formu', 'Event Participation Form') ?></h2>
    <p id="event-name"></p>
    <input type="text" id="ad" placeholder="<?= t('Adınız', 'First name') ?>">
    <input type="text" id="soyad" placeholder="<?= t('Soyadınız', 'Last name') ?>">
    <input type="email" id="email" placeholder="<?= t('E-posta adresiniz', 'Email address') ?>">
    <input type="tel" id="telefon" placeholder="<?= t('Telefon numaranız', 'Phone number') ?>">
    <button id="onayla"><?= t('Gönder', 'Submit') ?></button>
  </div>
</div>

<?php include "footer.php"; ?>

<script src="script.js"></script>
</body>
</html>
