<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "db.php";
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Akademik Takvim | Kampüs Sistemi</title>
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
    <a href="takvim.php" class="<?= $currentPage == 'takvim.php' ? 'active' : '' ?>">Eski Takvim</a>
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


<main class="takvim-container">
  <h2>2025-2026 Akademik Takvimi</h2>
  <table class="takvim-table">
    <tr>
      <th>Dönem</th>
      <th>Tarih</th>
      <th>Etkinlik / Açıklama</th>
    </tr>

    <?php
    $sql = "SELECT * FROM takvim ORDER BY id ASC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        echo "
          <tr>
            <td>{$row['donem']}</td>
            <td>{$row['tarih']}</td>
            <td>{$row['aciklama']}</td>
          </tr>
        ";
      }
    } else {
      echo "<tr><td colspan='3'>Henüz veri eklenmedi.</td></tr>";
    }
    ?>
  </table>
</main>

<?php include "footer.php"; ?>

</body>
</html>
