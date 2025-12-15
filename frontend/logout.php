<?php
session_start();

// Tüm session değişkenlerini temizle
$_SESSION = array();

// Session cookie'sini sil
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Session'ı yok et
session_destroy();

// Login sayfasına yönlendir
header("Location: login.php");
exit;
?>
