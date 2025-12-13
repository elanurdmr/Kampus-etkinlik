<?php
// Veritabanına şifre alanı ekleme scripti
include "db.php";

// kullanicilar tablosuna sifre alanı ekle (eğer yoksa)
$sql = "ALTER TABLE kullanicilar 
        ADD COLUMN IF NOT EXISTS sifre VARCHAR(255) NULL AFTER email";

if ($conn->query($sql) === TRUE) {
    echo "Şifre alanı başarıyla eklendi veya zaten mevcut.<br>";
} else {
    // IF NOT EXISTS MySQL'de çalışmıyor, hata kontrolü yap
    if (strpos($conn->error, 'Duplicate column name') !== false) {
        echo "Şifre alanı zaten mevcut.<br>";
    } else {
        echo "Hata: " . $conn->error . "<br>";
    }
}

// Test kullanıcıları oluştur (şifre: 123456)
$testUsers = [
    ['email' => 'admin@test.com', 'ad' => 'Admin', 'soyad' => 'Kullanıcı', 'ogrenci_no' => 'ADMIN001', 'rol' => 'admin', 'sifre' => password_hash('123456', PASSWORD_DEFAULT)],
    ['email' => 'ogretmen@test.com', 'ad' => 'Öğretmen', 'soyad' => 'Test', 'ogrenci_no' => 'OGR001', 'rol' => 'ogretmen', 'sifre' => password_hash('123456', PASSWORD_DEFAULT)],
    ['email' => 'ogrenci@test.com', 'ad' => 'Öğrenci', 'soyad' => 'Test', 'ogrenci_no' => 'OGR002', 'rol' => 'ogrenci', 'sifre' => password_hash('123456', PASSWORD_DEFAULT)]
];

foreach ($testUsers as $user) {
    // Önce kontrol et
    $check = $conn->prepare("SELECT id FROM kullanicilar WHERE email = ?");
    $check->bind_param("s", $user['email']);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO kullanicilar (email, ad, soyad, ogrenci_no, rol, sifre) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $user['email'], $user['ad'], $user['soyad'], $user['ogrenci_no'], $user['rol'], $user['sifre']);
        
        if ($stmt->execute()) {
            echo "Test kullanıcısı oluşturuldu: " . $user['email'] . " (Rol: " . $user['rol'] . ")<br>";
        } else {
            echo "Hata: " . $stmt->error . "<br>";
        }
    } else {
        // Mevcut kullanıcının şifresini güncelle
        $update = $conn->prepare("UPDATE kullanicilar SET sifre = ?, rol = ? WHERE email = ?");
        $update->bind_param("sss", $user['sifre'], $user['rol'], $user['email']);
        $update->execute();
        echo "Kullanıcı güncellendi: " . $user['email'] . " (Rol: " . $user['rol'] . ")<br>";
    }
}

echo "<br><strong>Test Kullanıcıları:</strong><br>";
echo "Admin: admin@test.com / 123456<br>";
echo "Öğretmen: ogretmen@test.com / 123456<br>";
echo "Öğrenci: ogrenci@test.com / 123456<br>";

$conn->close();
?>
