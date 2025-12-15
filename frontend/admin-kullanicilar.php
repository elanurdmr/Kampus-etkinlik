<?php
session_start();
include "auth_helper.php";
requireRole('admin');
include "db.php";

$currentPage = basename($_SERVER['PHP_SELF']);

// Kulüp başkanı yetkisi güncelleme
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kullanici_id'], $_POST['action'])) {
  $kullaniciId = (int)$_POST['kullanici_id'];
  $action = $_POST['action'] === 'kulup_baskani_yap' ? 1 : 0;

  $stmt = $conn->prepare("UPDATE kullanicilar SET kulup_baskani = ? WHERE id = ?");
  if ($stmt) {
    $stmt->bind_param("ii", $action, $kullaniciId);
    if ($stmt->execute()) {
      $message = $action ? "Kulüp başkanı yetkisi verildi." : "Kulüp başkanı yetkisi kaldırıldı.";
    }
    $stmt->close();
  }
}

// Tüm kullanıcıları çek
$kullanicilar = [];
$result = $conn->query("SELECT * FROM kullanicilar ORDER BY id ASC");
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $kullanicilar[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kullanıcı Yönetimi | Admin Paneli</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .admin-container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 20px;
    }
    
    .page-header {
      background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
      color: white;
      padding: 30px;
      border-radius: 15px;
      margin-bottom: 30px;
      box-shadow: 0 8px 20px rgba(40,167,69,0.3);
    }
    
    .page-header h1 {
      margin: 0;
      font-size: 2em;
    }
    
    .content-box {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      margin-bottom: 20px;
    }
    
    .content-box h2 {
      margin: 0 0 20px 0;
      color: #333;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
    }
    
    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }
    
    th {
      background: #f5f5f5;
      font-weight: 600;
      color: #333;
    }
    
    .badge {
      padding: 5px 10px;
      border-radius: 12px;
      font-size: 0.85em;
      font-weight: 600;
    }
    
    .badge-admin { background: #28a745; color: white; }
    .badge-ogretmen { background: #0066cc; color: white; }
    .badge-ogrenci { background: #6c757d; color: white; }
    .badge-kulup { background: #ff9800; color: white; }
    .btn-small {
      padding: 6px 10px;
      font-size: 0.8em;
      border-radius: 6px;
      border: none;
      cursor: pointer;
    }
    .btn-yes {
      background: #ff9800;
      color: #fff;
    }
    .btn-no {
      background: #e0e0e0;
      color: #333;
    }
    .flash-message {
      margin-bottom: 15px;
      padding: 10px 15px;
      border-radius: 8px;
      background: #e8f5e9;
      color: #2e7d32;
      font-size: 0.9em;
    }
  </style>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="admin-container">
  <div class="page-header">
    <h1>Kullanıcı Yönetimi</h1>
  </div>

  <div class="content-box">
    <h2>Tüm Kullanıcılar</h2>
    <?php if (!empty($message)): ?>
      <div class="flash-message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Ad Soyad</th>
          <th>Email</th>
          <th>Öğrenci No</th>
          <th>Rol</th>
          <th>Durum</th>
          <th>Kulüp Başkanı</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($kullanicilar) === 0): ?>
          <tr>
            <td colspan="7" style="text-align: center; padding: 40px;">
              Henüz kullanıcı bulunmuyor.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($kullanicilar as $kullanici): ?>
            <?php
              $rol = $kullanici['rol'] ?? 'ogrenci';
              $rolClass = $rol === 'admin' ? 'badge-admin' : ($rol === 'ogretmen' ? 'badge-ogretmen' : 'badge-ogrenci');
              $rolText = $rol === 'admin' ? 'Admin' : ($rol === 'ogretmen' ? 'Öğretmen' : 'Öğrenci');
              $kulupBaskani = !empty($kullanici['kulup_baskani']);
            ?>
            <tr>
              <td><?php echo (int)$kullanici['id']; ?></td>
              <td><?php echo htmlspecialchars($kullanici['ad'] . ' ' . $kullanici['soyad']); ?></td>
              <td><?php echo htmlspecialchars($kullanici['email']); ?></td>
              <td><?php echo htmlspecialchars($kullanici['ogrenci_no'] ?? '-'); ?></td>
              <td><span class="badge <?php echo $rolClass; ?>"><?php echo $rolText; ?></span></td>
              <td><?php echo !empty($kullanici['aktif']) ? 'Aktif' : 'Pasif'; ?></td>
              <td>
                <?php if ($rol === 'ogrenci'): ?>
                  <?php if ($kulupBaskani): ?>
                    <span class="badge badge-kulup">Kulüp Başkanı</span>
                    <form method="POST" style="display:inline;">
                      <input type="hidden" name="kullanici_id" value="<?php echo (int)$kullanici['id']; ?>">
                      <input type="hidden" name="action" value="kulup_baskani_kaldir">
                      <button type="submit" class="btn-small btn-no">Yetkiyi Kaldır</button>
                    </form>
                  <?php else: ?>
                    <form method="POST" style="display:inline;">
                      <input type="hidden" name="kullanici_id" value="<?php echo (int)$kullanici['id']; ?>">
                      <input type="hidden" name="action" value="kulup_baskani_yap">
                      <button type="submit" class="btn-small btn-yes">Kulüp Başkanı Yap</button>
                    </form>
                  <?php endif; ?>
                <?php else: ?>
                  -
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include "footer.php"; ?>

</body>
</html>
