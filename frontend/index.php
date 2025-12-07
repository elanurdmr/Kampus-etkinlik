<?php include "db.php"; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kampüs Etkinlik Takip Sistemi</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
  // Aktif sayfa ismini tespit ediyoruz
  $currentPage = basename($_SERVER['PHP_SELF']);
?>
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
    <div class="duyuru">
      <h3>Kütüphane Çalışma Saatleri Güncellendi</h3>
      <p>Yeni saatler: 09.00 - 22.00 | Hafta sonu: 10.00 - 18.00</p>
    </div>
    <div class="duyuru">
      <h3>Yazılım Kulübü Hackathon Başvuruları Başladı</h3>
      <p>Son başvuru: 15 Kasım 2025. Kazanan ekibe ödül!</p>
    </div>
    <div class="duyuru">
      <h3>Psikoloji Kulübü Söyleşi Etkinliği</h3>
      <p>20 Kasım 2025, D Blok Konferans Salonu. Konuk: Dr. Elif Yıldırım.</p>
    </div>
  </div>
</section>

<?php include "footer.php"; ?>

<script src="script.js"></script>
</body>
</html>
