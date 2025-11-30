<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ogr_no = $_POST['ogrenci_no'];
    $ad = $_POST['ad'];
    $soyad = $_POST['soyad'];
    $eposta = $_POST['eposta'];
    $sifre = password_hash($_POST['sifre'], PASSWORD_DEFAULT);
    $bolum = $_POST['bolum'];
    $gno = $_POST['gno'];
    $danisman_id = $_POST['danisman_id'];

    // Aynı e-posta ya da öğrenci no varsa kayıt olmasın
    $check = $conn->prepare("SELECT * FROM ogrenciler WHERE eposta=? OR ogrenci_no=?");
    $check->bind_param("ss", $eposta, $ogr_no);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $error = "Bu e-posta veya öğrenci numarası zaten kayıtlı!";
    } else {
        $stmt = $conn->prepare("INSERT INTO ogrenciler (ogrenci_no, ad, soyad, eposta, sifre, bolum, gno, kayit_tarihi, danisman_id)
                                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
        $stmt->bind_param("ssssssdi", $ogr_no, $ad, $soyad, $eposta, $sifre, $bolum, $gno, $danisman_id);

        if ($stmt->execute()) {
            header("Location: login.php?success=1");
            exit;
        } else {
            $error = "Kayıt sırasında bir hata oluştu.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kayıt Ol | Kampüs Sistemi</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body { background: #f5f5f5; font-family: "Poppins", sans-serif; }
    .signup-container { display: flex; justify-content: center; align-items: center; height: 100vh; }
    .signup-box { background: #fff; width: 900px; display: flex; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 15px; overflow: hidden; }
    .signup-left { width: 45%; display: flex; justify-content: center; align-items: center; background: #fff; }
    .signup-left img { width: 220px; }
    .signup-right { width: 55%; background: #fafafa; padding: 50px; }
    .signup-right h2 { font-size: 26px; font-weight: 700; color: #222; margin-bottom: 20px; }
    .signup-right input, .signup-right select { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 8px; }
    .signup-right button { width: 100%; background: #b00000; color: #fff; border: none; padding: 12px; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s; }
    .signup-right button:hover { background: #870000; }
    .error { color: red; text-align: center; margin-bottom: 15px; font-weight: bold; }
  </style>
</head>
<body>
<div class="signup-container">
  <div class="signup-box">
    <div class="signup-left">
      <img src="img/dogus-logo.png" alt="Doğuş Üniversitesi">
    </div>
    <div class="signup-right">
      <h2>DOÜ Kampüs Kayıt</h2>
      <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
      <form method="POST">
        <input type="text" name="ogrenci_no" placeholder="Öğrenci Numarası" required>
        <input type="text" name="ad" placeholder="Adınız" required>
        <input type="text" name="soyad" placeholder="Soyadınız" required>
        <input type="email" name="eposta" placeholder="E-posta adresiniz" required>
        <input type="password" name="sifre" placeholder="Şifrenizi belirleyin" required>
        <input type="text" name="bolum" placeholder="Bölümünüz" required>
        
        <button type="submit">Kayıt Ol</button>
      </form>
      <p>Zaten hesabınız var mı? <a href="login.php">Giriş Yap</a></p>
    </div>
  </div>
</div>
</body>
</html>
