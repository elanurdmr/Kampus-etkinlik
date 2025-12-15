<?php
// Rol kontrolü helper fonksiyonları
// NOT: session_start() burada çağrılmaz, çağıran sayfada çağrılmalı

if (!function_exists('getCurrentUserRole')) {
    function getCurrentUserRole() {
        if (session_status() === PHP_SESSION_NONE) {
            return 'guest';
        }
        return isset($_SESSION['rol']) ? $_SESSION['rol'] : 'ogrenci';
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin() {
        return getCurrentUserRole() === 'admin';
    }
}

if (!function_exists('isOgretmen')) {
    function isOgretmen() {
        return getCurrentUserRole() === 'ogretmen';
    }
}

if (!function_exists('isOgrenci')) {
    function isOgrenci() {
        return getCurrentUserRole() === 'ogrenci';
    }
}

if (!function_exists('isKulupBaskani')) {
    function isKulupBaskani() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return !empty($_SESSION['kulup_baskani']);
    }
}

/**
 * Belirli bir rol(ler)i zorunlu kılar.
 *
 * @param string|array $requiredRole 'admin' | 'ogretmen' | 'ogrenci' veya bunların listesi
 */
if (!function_exists('requireRole')) {
    function requireRole($requiredRole) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Giriş yapılmamışsa login'e gönder
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    $currentRole = getCurrentUserRole();

    // Birden fazla rol verildiyse, herhangi biri yeterli olsun
    if (is_array($requiredRole)) {
        if (!in_array($currentRole, $requiredRole, true)) {
            redirectToPanel();
        }
    } else {
        if ($currentRole !== $requiredRole) {
            redirectToPanel();
        }
    }
}
}

if (!function_exists('redirectToPanel')) {
    function redirectToPanel() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $role = getCurrentUserRole();

    if ($role === 'admin') {
        header("Location: admin-panel.php");
    } elseif ($role === 'ogretmen') {
        header("Location: ogretmen-panel.php");
    } elseif ($role === 'ogrenci') {
        header("Location: ogrenci-panel.php");
    } else {
        header("Location: index.php");
    }
    exit;
}
}
?>
