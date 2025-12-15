<?php
// Rol kontrolü helper fonksiyonları
// NOT: session_start() burada çağrılmaz, çağıran sayfada çağrılmalı

if (!function_exists('getCurrentUserRole')) {
    function getCurrentUserRole() {
        if (session_status() === PHP_SESSION_NONE) {
            return 'ogrenci';
        }
        return isset($_SESSION['rol']) ? $_SESSION['rol'] : 'ogrenci';
    }
}

function isAdmin() {
    return getCurrentUserRole() === 'admin';
}

function isOgretmen() {
    return getCurrentUserRole() === 'ogretmen';
}

function isOgrenci() {
    return getCurrentUserRole() === 'ogrenci';
}

function requireRole($requiredRole) {
    // Basit sistem - sadece giriş kontrolü yap
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
    // Rol kontrolü yapılmıyor, herkes öğrenci olarak kabul ediliyor
}

function redirectToPanel() {
    // Herkes ana sayfaya yönlendirilir (basit sistem)
    header("Location: index.php");
    exit;
}
?>
