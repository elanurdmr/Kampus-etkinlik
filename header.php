<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<header class="topbar">
    <!-- Sol Menü Butonu -->
    <span id="menuBtn" class="hamburger">&#9776;</span>

    <!-- Orta Başlık -->
    <h1 class="site-title">Kampüs Etkinlik Takip Sistemi</h1>

    <!-- Sağdaki butonlar -->
    <div class="right-buttons">
        <a href="login.php" class="login-btn">Giriş Yap</a>
        <a href="signup.php" class="signup-btn">Kayıt Ol</a>
    </div>
</header>

<!-- Yan Menü -->
<div id="sideMenu" class="side-menu">
    <div class="close-btn" id="closeMenu">&times;</div>

    <a href="index.php">Ana Sayfa</a>
    <a href="etkinlikler.php">Etkinlikler</a>
    <a href="akademik-takvim.php">Akademik Takvim (API)</a>
    <a href="kulup-oneri.php">KULÜP ETKİNLİK ÖNERİ SİSTEMİ</a> <!-- yapay zeka kısımı -->
    <a href="puan-sistemi.php">PUAN & ROZET SİSTEMİ</a>
    <a href="kutuphane.php">KÜTÜPHANE REZERVASYONU</a>
    <a href="#">ARKADAŞ EKLEME</a>
    <a href="qr-etkinlik.php">QR İLE ETKİNLİK ONAYI</a>
    <a href="randevu.php">ÖĞRETİM ÜYESİ RANDEVU</a>
    <a href="etkinlik-yonetim.php">Etkinlik Yönetimi</a>
</div>
<script src="script.js"></script>

