<?php
session_start();
include "db.php";
include "auth_helper.php";
requireRole('ogrenci');

$currentPage = basename($_SERVER['PHP_SELF']);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$currentUserId = (int) $_SESSION['user_id'];

// Arama parametresi
$arama = isset($_GET['q']) ? trim($_GET['q']) : '';

// Arkadaşlık isteği işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksiyon'])) {
    $aksiyon = $_POST['aksiyon'];

    // Yeni arkadaşlık isteği
    if ($aksiyon === 'istek_gonder' && isset($_POST['hedef_id'])) {
        $hedefId = (int) $_POST['hedef_id'];

        if ($hedefId > 0 && $hedefId !== $currentUserId) {
            $stmt = $conn->prepare("
                INSERT INTO arkadasliklar (gonderen_id, alan_id, durum)
                VALUES (?, ?, 'beklemede')
                ON DUPLICATE KEY UPDATE durum = VALUES(durum), guncellenme_tarihi = NOW()
            ");
            $stmt->bind_param("ii", $currentUserId, $hedefId);
            $stmt->execute();
        }
    }

    // İstek kabul / red
    if (in_array($aksiyon, ['kabul', 'red'], true) && isset($_POST['istek_id'])) {
        $istekId = (int) $_POST['istek_id'];
        $yeniDurum = $aksiyon === 'kabul' ? 'kabul' : 'red';

        $stmt = $conn->prepare("
            UPDATE arkadasliklar
            SET durum = ?, guncellenme_tarihi = NOW()
            WHERE id = ? AND alan_id = ?
        ");
        $stmt->bind_param("sii", $yeniDurum, $istekId, $currentUserId);
        $stmt->execute();
    }

    // Gönderilen isteği iptal et
    if ($aksiyon === 'iptal' && isset($_POST['istek_id'])) {
        $istekId = (int) $_POST['istek_id'];

        $stmt = $conn->prepare("
            DELETE FROM arkadasliklar
            WHERE id = ? AND gonderen_id = ?
        ");
        $stmt->bind_param("ii", $istekId, $currentUserId);
        $stmt->execute();
    }

    header("Location: arkadaslar.php");
    exit;
}

// Arama sonuçları (arkadaş önerisi amaçlı) - ogrenciler tablosundan
$kullanicilar = [];
if ($arama !== '') {
    $sql = "
        SELECT o.ogrenci_id AS id, o.ad, o.soyad, o.eposta AS email, o.ogrenci_no,
               a.durum AS arkadas_durumu
        FROM ogrenciler o
        LEFT JOIN arkadasliklar a
          ON (
                (a.gonderen_id = ? AND a.alan_id = o.ogrenci_id)
             OR (a.alan_id = ? AND a.gonderen_id = o.ogrenci_id)
             )
        WHERE o.ogrenci_id != ?
          AND (o.ad LIKE ? OR o.soyad LIKE ? OR o.eposta LIKE ? OR o.ogrenci_no LIKE ?)
    ";
    $stmt = $conn->prepare($sql);
    $like = "%" . $arama . "%";
    $stmt->bind_param("iiissss", $currentUserId, $currentUserId, $currentUserId, $like, $like, $like, $like);
    $stmt->execute();
    $kullanicilar = $stmt->get_result();
}

// Bekleyen gelen istekler - ogrenciler tablosundan
$bekleyenIstekler = [];
$sqlBekleyen = "
    SELECT a.id, o.ad, o.soyad, o.eposta AS email
    FROM arkadasliklar a
    JOIN ogrenciler o ON o.ogrenci_id = a.gonderen_id
    WHERE a.alan_id = ? AND a.durum = 'beklemede'
";
$stmt = $conn->prepare($sqlBekleyen);
$stmt->bind_param("i", $currentUserId);
$stmt->execute();
$bekleyenIstekler = $stmt->get_result();

// Gönderdiğim bekleyen istekler - ogrenciler tablosundan
$gonderilenIstekler = [];
$sqlGonderilen = "
    SELECT a.id, o.ad, o.soyad, o.eposta AS email
    FROM arkadasliklar a
    JOIN ogrenciler o ON o.ogrenci_id = a.alan_id
    WHERE a.gonderen_id = ? AND a.durum = 'beklemede'
";
$stmt = $conn->prepare($sqlGonderilen);
$stmt->bind_param("i", $currentUserId);
$stmt->execute();
$gonderilenIstekler = $stmt->get_result();

// Arkadaş listesi - ogrenciler tablosundan
$arkadaslar = [];
$sqlArkadaslar = "
    SELECT o.ogrenci_id AS id, o.ad, o.soyad, o.eposta AS email
    FROM arkadasliklar a
    JOIN ogrenciler o 
      ON (o.ogrenci_id = a.gonderen_id AND a.alan_id = ?)
      OR (o.ogrenci_id = a.alan_id AND a.gonderen_id = ?)
    WHERE a.durum = 'kabul'
";
$stmt = $conn->prepare($sqlArkadaslar);
$stmt->bind_param("ii", $currentUserId, $currentUserId);
$stmt->execute();
$arkadaslar = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Arkadaşlarım | Kampüs Sistemi</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .friends-container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 20px;
    }
    .friends-header {
      background: linear-gradient(135deg, #b30000 0%, #8b0000 100%);
      color: white;
      padding: 30px;
      border-radius: 15px;
      margin-bottom: 30px;
      box-shadow: 0 8px 20px rgba(179,0,0,0.3);
    }
    .friends-header h1 {
      margin: 0 0 10px 0;
      font-size: 2em;
    }
    .friends-layout {
      display: grid;
      grid-template-columns: 2fr 1.5fr;
      gap: 20px;
    }
    @media (max-width: 900px) {
      .friends-layout {
        grid-template-columns: 1fr;
      }
    }
    .card {
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .card h2 {
      margin-top: 0;
      margin-bottom: 15px;
      font-size: 1.3em;
      border-bottom: 2px solid #b30000;
      padding-bottom: 8px;
      color: #333;
    }
    .search-form {
      display: flex;
      gap: 10px;
      margin-bottom: 15px;
      flex-wrap: wrap;
    }
    .search-form input[type="text"] {
      flex: 1;
      min-width: 200px;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
    }
    .btn {
      padding: 8px 14px;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      font-size: 0.9em;
      font-weight: 600;
      display: inline-block;
    }
    .btn-primary {
      background: #b30000;
      color: white;
    }
    .btn-primary:hover {
      background: #8b0000;
    }
    .btn-outline {
      background: white;
      color: #b30000;
      border: 1px solid #b30000;
    }
    .btn-outline:hover {
      background: #b30000;
      color: white;
    }
    .friends-list, .requests-list, .search-results {
      list-style: none;
      margin: 0;
      padding: 0;
    }
    .friends-item, .request-item, .search-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 0;
      border-bottom: 1px solid #eee;
    }
    .friends-item:last-child,
    .request-item:last-child,
    .search-item:last-child {
      border-bottom: none;
    }
    .user-info {
      display: flex;
      flex-direction: column;
    }
    .user-name {
      font-weight: 600;
      color: #333;
    }
    .user-email {
      font-size: 0.8em;
      color: #777;
    }
    .badge-status {
      font-size: 0.8em;
      padding: 3px 8px;
      border-radius: 10px;
      background: #f1f1f1;
      color: #666;
      margin-left: 10px;
    }
  </style>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="friends-container">
  <div class="friends-header">
    <h1>Arkadaşlarım</h1>
    <p>Diğer öğrencilerle bağlantı kur, arkadaşlık isteği gönder ve sosyal etkileşimi artır.</p>
  </div>

  <div class="friends-layout">
    <div class="card">
      <h2>Arkadaş Ara / Öneriler</h2>
      <form class="search-form" method="get" action="arkadaslar.php">
        <input type="text" name="q" placeholder="Ad, soyad, e‑posta veya öğrenci no ile ara..." value="<?php echo htmlspecialchars($arama); ?>">
        <button type="submit" class="btn btn-primary">Ara</button>
      </form>

      <?php if ($arama === ''): ?>
        <p style="color:#777; font-size:0.9em;">Arama yaparak arkadaş önerilerini görebilirsiniz.</p>
      <?php else: ?>
        <ul class="search-results">
          <?php if ($kullanicilar && $kullanicilar->num_rows > 0): ?>
            <?php while ($k = $kullanicilar->fetch_assoc()): ?>
              <li class="search-item">
                <div class="user-info">
                  <span class="user-name">
                    <?php echo htmlspecialchars($k['ad'] . ' ' . $k['soyad']); ?>
                    <?php if (!empty($k['ogrenci_no'])): ?>
                      <span class="badge-status"><?php echo htmlspecialchars($k['ogrenci_no']); ?></span>
                    <?php endif; ?>
                  </span>
                  <span class="user-email"><?php echo htmlspecialchars($k['email']); ?></span>
                </div>
                <div>
                  <?php if ($k['arkadas_durumu'] === 'kabul'): ?>
                    <span class="badge-status">Zaten arkadaşsınız</span>
                  <?php elseif ($k['arkadas_durumu'] === 'beklemede'): ?>
                    <span class="badge-status">İstek beklemede</span>
                  <?php elseif ($k['arkadas_durumu'] === 'red'): ?>
                    <span class="badge-status">Reddedildi</span>
                  <?php else: ?>
                    <form method="post" style="display:inline;">
                      <input type="hidden" name="aksiyon" value="istek_gonder">
                      <input type="hidden" name="hedef_id" value="<?php echo (int) $k['id']; ?>">
                      <button type="submit" class="btn btn-outline">Arkadaşlık isteği gönder</button>
                    </form>
                  <?php endif; ?>
                </div>
              </li>
            <?php endwhile; ?>
          <?php else: ?>
            <p>Aramanıza uygun kullanıcı bulunamadı.</p>
          <?php endif; ?>
        </ul>
      <?php endif; ?>
    </div>

    <div class="card">
      <h2>Bekleyen İstekler (Bana Gelen)</h2>
      <ul class="requests-list">
        <?php if ($bekleyenIstekler && $bekleyenIstekler->num_rows > 0): ?>
          <?php while ($r = $bekleyenIstekler->fetch_assoc()): ?>
            <li class="request-item">
              <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($r['ad'] . ' ' . $r['soyad']); ?></span>
                <span class="user-email"><?php echo htmlspecialchars($r['email']); ?></span>
              </div>
              <div>
                <form method="post" style="display:inline;">
                  <input type="hidden" name="aksiyon" value="kabul">
                  <input type="hidden" name="istek_id" value="<?php echo (int) $r['id']; ?>">
                  <button type="submit" class="btn btn-primary">Kabul Et</button>
                </form>
                <form method="post" style="display:inline; margin-left:5px;">
                  <input type="hidden" name="aksiyon" value="red">
                  <input type="hidden" name="istek_id" value="<?php echo (int) $r['id']; ?>">
                  <button type="submit" class="btn btn-outline">Reddet</button>
                </form>
              </div>
            </li>
          <?php endwhile; ?>
        <?php else: ?>
          <p style="color:#777; font-size:0.9em;">Şu anda sana gelen bekleyen arkadaşlık isteği yok.</p>
        <?php endif; ?>
      </ul>

      <h2 style="margin-top:25px;">Gönderdiğim İstekler</h2>
      <ul class="requests-list">
        <?php if ($gonderilenIstekler && $gonderilenIstekler->num_rows > 0): ?>
          <?php while ($g = $gonderilenIstekler->fetch_assoc()): ?>
            <li class="request-item">
              <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($g['ad'] . ' ' . $g['soyad']); ?></span>
                <span class="user-email"><?php echo htmlspecialchars($g['email']); ?></span>
              </div>
              <div>
                <form method="post" style="display:inline;">
                  <input type="hidden" name="aksiyon" value="iptal">
                  <input type="hidden" name="istek_id" value="<?php echo (int) $g['id']; ?>">
                  <button type="submit" class="btn btn-outline">İsteği Geri Çek</button>
                </form>
              </div>
            </li>
          <?php endwhile; ?>
        <?php else: ?>
          <p style="color:#777; font-size:0.9em;">Şu anda bekleyen gönderilmiş isteğiniz yok.</p>
        <?php endif; ?>
      </ul>

      <h2 style="margin-top:25px;">Arkadaş Listem</h2>
      <ul class="friends-list">
        <?php if ($arkadaslar && $arkadaslar->num_rows > 0): ?>
          <?php while ($f = $arkadaslar->fetch_assoc()): ?>
            <li class="friends-item">
              <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($f['ad'] . ' ' . $f['soyad']); ?></span>
                <span class="user-email"><?php echo htmlspecialchars($f['email']); ?></span>
              </div>
            </li>
          <?php endwhile; ?>
        <?php else: ?>
          <p style="color:#777; font-size:0.9em;">Henüz arkadaş eklemediniz.</p>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>

</body>
</html>


