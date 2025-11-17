<?php include "db.php"; ?>
<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
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

<header class="topbar">
  <h1>Kampüs Etkinlik Takip Sistemi</h1>
  <nav class="menu">
    <a href="index.php" class="<?= $currentPage == 'index.php' ? 'active' : '' ?>">Ana Sayfa</a>
    <a href="etkinlikler.php" class="<?= $currentPage == 'etkinlikler.php' ? 'active' : '' ?>">Etkinlikler</a>
    <a href="takvim.php" class="<?= $currentPage == 'takvim.php' ? 'active' : '' ?>">Akademik Takvim</a>
    <a href="akademik-takvim.php" class="<?= $currentPage == 'akademik-takvim.php' ? 'active' : '' ?>">📅 Akademik Takvim (API)</a>
    <a href="etkinlik-yonetim.php" class="<?= $currentPage == 'etkinlik-yonetim.php' ? 'active' : '' ?>">➕ Etkinlik Yönetimi</a>

    <!-- Kullanıcı oturumu yoksa -->
    <?php if (!isset($_SESSION['user_id'])): ?>
      <a href="login.php" class="login-btn">Giriş Yap</a>
      <a href="signup.php" class="signup-btn">Kayıt Ol</a>
    <?php else: ?>
      <!-- Giriş yaptıysa -->
      <a href="profile.php" class="profile-btn">Profilim</a>
      <a href="logout.php" class="logout-btn">Çıkış Yap</a>
    <?php endif; ?>
  </nav>
</header>



<main class="etkinlikler">
  <div class="event-card">
    <h3>Yazılım Kulübü</h3>
    <p><b>Etkinlik:</b> Kodlama Günü 2025</p>
    <p><b>Tarih:</b> 12 Kasım 2025</p>
    <p>Yapay zeka ve web geliştirme workshopları. Katılanlara sertifika verilecektir.</p>
    <button class="katil-btn" data-event="Kodlama Günü 2025">Katıl</button>
  </div>

  <div class="event-card">
    <h3>Psikoloji Kulübü</h3>
    <p><b>Etkinlik:</b> Empati Semineri</p>
    <p><b>Tarih:</b> 18 Kasım 2025</p>
    <p>Empati becerilerini geliştirmeye yönelik interaktif oturumlar.</p>
    <button class="katil-btn" data-event="Empati Semineri">Katıl</button>
  </div>

  <div class="event-card">
    <h3>Fotoğrafçılık Kulübü</h3>
    <p><b>Etkinlik:</b> Kampüs Kareleri</p>
    <p><b>Tarih:</b> 22 Kasım 2025</p>
    <p>Kampüs genelinde fotoğraf turu yapılacak. En iyi kare sergilenecek.</p>
    <button class="katil-btn" data-event="Kampüs Kareleri">Katıl</button>
  </div>
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
