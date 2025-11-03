<?php
include "db.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eposta = $_POST['eposta'];
    $sifre = $_POST['sifre'];

    $stmt = $conn->prepare("SELECT * FROM ogrenciler WHERE eposta=?");
    $stmt->bind_param("s", $eposta);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($sifre, $user['sifre'])) {
            $_SESSION['ogrenci_id'] = $user['ogrenci_id'];
            $_SESSION['ad'] = $user['ad'];
            $_SESSION['soyad'] = $user['soyad'];
            $_SESSION['bolum'] = $user['bolum'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Şifre yanlış!";
        }
    } else {
        $error = "Bu e-posta adresiyle kayıt bulunamadı!";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Giriş Yap | Kampüs Sistemi</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body { background: #f5f5f5; font-family: "Poppins", sans-serif; }
    .login-container { display: flex; justify-content: center; align-items: center; height: 100vh; }
    .login-box { background: #fff; width: 850px; display: flex; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 15px; overflow: hidden; }
    .login-left { width: 45%; display: flex; justify-content: center; align-items: center; }
    .login-left img { width: 220px; }
    .login-right { width: 55%; background: #fafafa; padding: 60px; }
    .login-right h2 { font-size: 28px; font-weight: 700; color: #222; margin-bottom: 30px; }
    .login-right input { width: 100%; padding: 12px; margin-bottom: 18px; border: 1px solid #ccc; border-radius: 8px; font-size: 15px; }
    .login-right button { width: 100%; background: #b00000; color: #fff; border: none; padding: 12px; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; }
    .login-right button:hover { background: #870000; }
    .error { color: red; text-align: center; margin-bottom: 15px; font-weight: bold; }
  </style>
</head>
<body>
<div class="login-container">
  <div class="login-box">
    <div class="login-left">
      <img src="img/dogus-logo.png" alt="Doğuş Üniversitesi">
    </div>
    <div class="login-right">
      <h2>DOÜ Kampüs Girişi</h2>
      <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
      <form method="POST">
        <input type="email" name="eposta" placeholder="E-posta adresiniz" required>
        <input type="password" name="sifre" placeholder="Şifrenizi giriniz" required>
        <button type="submit">Giriş Yap</button>
      </form>
      <p>Hesabınız yok mu? <a href="signup.php">Kayıt Ol</a></p>
    </div>
  </div>
</div>
</body>
</html>
