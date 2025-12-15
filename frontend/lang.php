<?php
// Global dil ayarları ve basit çeviri helper'ı
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Dil parametresi geldiyse güncelle
if (isset($_GET['lang'])) {
    $langParam = $_GET['lang'] === 'en' ? 'en' : 'tr';
    $_SESSION['lang'] = $langParam;
    // Aynı sayfayı query paramsız yenile
    $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?');
    header("Location: " . $redirectUrl);
    exit;
}

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'tr';
}

$currentLang = $_SESSION['lang'];

function t(string $tr, string $en): string {
    global $currentLang;
    return $currentLang === 'en' ? $en : $tr;
}


