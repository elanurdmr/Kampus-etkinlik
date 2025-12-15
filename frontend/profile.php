<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$currentPage = basename($_SERVER['PHP_SELF']);
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['rol'] ?? 'ogrenci';

// Kullanıcı bilgilerini veritabanından çek
$stmt = $conn->prepare("SELECT * FROM kullanicilar WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    // Eski ogrenciler tablosunda ara
    $stmt2 = $conn->prepare("SELECT * FROM ogrenciler WHERE ogrenci_id = ?");
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    
    if ($result2->num_rows > 0) {
        $oldUser = $result2->fetch_assoc();
        $user = [
            'id' => $oldUser['ogrenci_id'],
            'ad' => $oldUser['ad'],
            'soyad' => $oldUser['soyad'],
            'email' => $oldUser['eposta'],
            'ogrenci_no' => $oldUser['ogrenci_no'] ?? '',
            'rol' => 'ogrenci'
        ];
    } else {
        $user = [
            'ad' => $_SESSION['ad'] ?? '',
            'soyad' => $_SESSION['soyad'] ?? '',
            'email' => $_SESSION['email'] ?? '',
            'ogrenci_no' => $_SESSION['ogrenci_no'] ?? '',
            'rol' => $user_role
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profilim | Kampüs Sistemi</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .profile-container {
      max-width: 900px;
      margin: 40px auto;
      padding: 20px;
    }
    
    .profile-header {
      background: linear-gradient(135deg, #b30000 0%, #8b0000 100%);
      color: white;
      padding: 40px;
      border-radius: 15px;
      margin-bottom: 30px;
      box-shadow: 0 8px 20px rgba(179,0,0,0.3);
      text-align: center;
    }
    
    .profile-avatar {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      background: white;
      color: #b30000;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 3em;
      font-weight: bold;
      margin: 0 auto 20px;
      border: 5px solid rgba(255,255,255,0.3);
    }
    
    .profile-header h1 {
      margin: 0 0 10px 0;
      font-size: 2em;
    }
    
    .profile-header p {
      margin: 5px 0;
      opacity: 0.9;
    }
    
    .profile-content {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    
    .profile-card {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .profile-card h2 {
      margin: 0 0 20px 0;
      color: #333;
      font-size: 1.3em;
      border-bottom: 2px solid #b30000;
      padding-bottom: 10px;
    }
    
    .info-row {
      display: flex;
      justify-content: space-between;
      padding: 12px 0;
      border-bottom: 1px solid #eee;
    }
    
    .info-row:last-child {
      border-bottom: none;
    }
    
    .info-label {
      font-weight: 600;
      color: #666;
    }
    
    .info-value {
      color: #333;
      text-align: right;
    }
    
    .badge-role {
      display: inline-block;
      padding: 5px 12px;
      border-radius: 15px;
      font-size: 0.85em;
      font-weight: 600;
      margin-top: 5px;
    }
    
    .badge-admin {
      background: #28a745;
      color: white;
    }
    
    .badge-ogretmen {
      background: #0066cc;
      color: white;
    }
    
    .badge-ogrenci {
      background: #6c757d;
      color: white;
    }
  </style>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="profile-container">
  <div class="profile-header">
    <div class="profile-avatar">
      <?php 
      $initials = strtoupper(substr($user['ad'], 0, 1) . substr($user['soyad'], 0, 1));
      echo $initials;
      ?>
    </div>
    <h1><?php echo htmlspecialchars($user['ad'] . ' ' . $user['soyad']); ?></h1>
    <p>
      <span class="badge-role badge-<?php echo $user['rol']; ?>">
        <?php 
        echo $user['rol'] === 'admin' ? 'Admin' : 
             ($user['rol'] === 'ogretmen' ? 'Öğretmen' : 'Öğrenci');
        ?>
      </span>
    </p>
  </div>

  <div class="profile-content">
    <div class="profile-card">
      <h2>Kişisel Bilgiler</h2>
      <div class="info-row">
        <span class="info-label">Ad Soyad:</span>
        <span class="info-value"><?php echo htmlspecialchars($user['ad'] . ' ' . $user['soyad']); ?></span>
      </div>
      <div class="info-row">
        <span class="info-label">Email:</span>
        <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
      </div>
      <?php if (!empty($user['ogrenci_no'])): ?>
      <div class="info-row">
        <span class="info-label">Öğrenci No:</span>
        <span class="info-value"><?php echo htmlspecialchars($user['ogrenci_no']); ?></span>
      </div>
      <?php endif; ?>
      <div class="info-row">
        <span class="info-label">Rol:</span>
        <span class="info-value">
          <span class="badge-role badge-<?php echo $user['rol']; ?>">
            <?php 
            echo $user['rol'] === 'admin' ? 'Admin' : 
                 ($user['rol'] === 'ogretmen' ? 'Öğretmen' : 'Öğrenci');
            ?>
          </span>
        </span>
      </div>
    </div>

    <div class="profile-card">
      <h2>Etkinlik Puanları & Seviye</h2>
      <div class="info-row">
        <span class="info-label">Toplam Puan:</span>
        <span class="info-value" id="profilToplamPuan">-</span>
      </div>
      <div class="info-row">
        <span class="info-label">Seviye:</span>
        <span class="info-value" id="profilSeviye">-</span>
      </div>
      <div class="info-row">
        <span class="info-label">Rozet:</span>
        <span class="info-value" id="profilRozet">-</span>
      </div>
      <div class="info-row">
        <span class="info-label">Toplam Katılım:</span>
        <span class="info-value" id="profilToplamKatilim">-</span>
      </div>
      <div class="info-row">
        <span class="info-label">Streak (Gün):</span>
        <span class="info-value" id="profilStreak">-</span>
      </div>
      <div class="info-row">
        <span class="info-label">Son Katılım Tarihi:</span>
        <span class="info-value" id="profilSonKatilim">-</span>
      </div>
      <div style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 10px;">
        <strong>Son Etkinlikler:</strong>
        <ul id="profilEtkinlikGecmisi" style="margin: 8px 0 0; padding-left: 18px; font-size: 0.9em; color: #555;">
          <li>Henüz etkinlik katılımınız yok.</li>
        </ul>
      </div>
      <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
        <a href="logout.php" style="display: inline-block; padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: all 0.3s;">
          Çıkış Yap
        </a>
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>

<script>
const PUAN_API_BASE_URL = 'http://localhost:8000/api/qr';
if (typeof window.kullaniciId === 'undefined') {
  window.kullaniciId = <?php echo (int) $user_id; ?>;
}

async function yukleProfilPuanlari() {
  try {
    if (!window.kullaniciId || window.kullaniciId <= 0) return;

    const res = await fetch(`${PUAN_API_BASE_URL}/kullanici/${window.kullaniciId}/istatistik`);
    if (!res.ok) return;
    const data = await res.json();

    document.getElementById('profilToplamPuan').textContent = data.toplam_puan ?? 0;
    document.getElementById('profilSeviye').textContent = data.seviye ?? 1;
    document.getElementById('profilRozet').textContent = data.rozet || '-';
    document.getElementById('profilToplamKatilim').textContent = data.toplam_katilim ?? 0;
    document.getElementById('profilStreak').textContent = data.streak_gun ?? 0;
    document.getElementById('profilSonKatilim').textContent = data.son_katilim_tarihi || '-';

    const ul = document.getElementById('profilEtkinlikGecmisi');
    ul.innerHTML = '';
    if (data.gecmis && data.gecmis.length > 0) {
      data.gecmis.forEach(item => {
        const li = document.createElement('li');
        const tarih = new Date(item.tarih).toLocaleString('tr-TR', {
          day: '2-digit',
          month: '2-digit',
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        });
        li.textContent = `${item.etkinlik_baslik} (+${item.puan} puan, ${tarih})`;
        ul.appendChild(li);
      });
    } else {
      const li = document.createElement('li');
      li.textContent = 'Henüz etkinlik katılımınız yok.';
      ul.appendChild(li);
    }
  } catch (e) {
    console.error('Puan bilgisi yüklenirken hata:', e);
  }
}

document.addEventListener('DOMContentLoaded', yukleProfilPuanlari);
</script>

</body>
</html>
