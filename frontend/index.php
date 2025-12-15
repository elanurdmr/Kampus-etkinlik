<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "db.php";
require_once "kulup_etkinlikleri_mock.php";

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
  <title>Kampüs Etkinlik Takip Sistemi</title>
  <link rel="stylesheet" href="style.css">
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
  <h2>Duyurular</h2>
  <div class="duyuru-listesi">
    <?php if (!empty($oneCikanEtkinlikler)): ?>
      <?php foreach (array_slice($oneCikanEtkinlikler, 0, 3) as $etkinlik): ?>
        <div class="duyuru">
          <h3><?php echo htmlspecialchars($etkinlik['ad']); ?></h3>
          <p>
            <?php echo htmlspecialchars($etkinlik['kulup']); ?> |
            <?php echo htmlspecialchars($etkinlik['tarih']); ?> |
            <?php echo htmlspecialchars($etkinlik['konum']); ?>
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
</body>
</html>
