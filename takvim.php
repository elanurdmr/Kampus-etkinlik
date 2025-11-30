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

<?php include "header.php"; ?>



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

<script src="script.js"></script>

