<?php
// Basit test sayfası - Frontend'in çalışıp çalışmadığını kontrol eder
echo "<!DOCTYPE html><html><head><title>Frontend Test</title></head><body>";
echo "<h1>✅ Frontend Çalışıyor!</h1>";
echo "<p>PHP versiyonu: " . phpversion() . "</p>";

// Veritabanı bağlantısını test et
include "db.php";
if ($conn) {
    echo "<p>✅ Veritabanı bağlantısı başarılı!</p>";
    echo "<p>Veritabanı: akademik_sistem</p>";
} else {
    echo "<p>❌ Veritabanı bağlantısı başarısız!</p>";
}

// Session test
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p>✅ Session aktif!</p>";
} else {
    echo "<p>❌ Session aktif değil!</p>";
}

echo "<hr>";
echo "<h2>Mevcut Sayfalar:</h2>";
echo "<ul>";
echo "<li><a href='index.php'>Ana Sayfa (index.php)</a></li>";
echo "<li><a href='login.php'>Giriş (login.php)</a></li>";
echo "<li><a href='signup.php'>Kayıt (signup.php)</a></li>";
echo "<li><a href='akademik-takvim.php'>Akademik Takvim (akademik-takvim.php)</a></li>";
echo "<li><a href='etkinlik-yonetim.php'>Etkinlik Yönetimi (etkinlik-yonetim.php)</a></li>";
echo "</ul>";

echo "<hr>";
echo "<h2>Backend API Test:</h2>";
echo "<p>Backend API'ye bağlanmayı deniyor...</p>";

$ch = curl_init('http://localhost:8010/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    echo "<p>✅ Backend API çalışıyor! (http://localhost:8010)</p>";
} else {
    echo "<p>❌ Backend API çalışmıyor veya erişilemiyor! (http://localhost:8010)</p>";
    echo "<p>HTTP Kodu: " . $httpCode . "</p>";
    echo "<p>Lütfen Backend'i başlatın: <code>cd backend/app && python main.py</code></p>";
}

echo "</body></html>";
?>

<script src="script.js"></script>
