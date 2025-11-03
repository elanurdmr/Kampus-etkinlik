<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Kod Okutma</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="logo">
                <i class="fas fa-graduation-cap"></i>
                <h1>Akademik Takvim Sistemi</h1>
            </div>
            <nav class="nav">
                <a href="index.php" class="nav-link">Ana Sayfa</a>
                <a href="calendar.php" class="nav-link">Takvim</a>
                <a href="qr-scan.php" class="nav-link active">QR Okut</a>
                <a href="admin.php" class="nav-link">Yönetim</a>
            </nav>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <section class="qr-section">
                <h2><i class="fas fa-qrcode"></i> QR Kod ile Etkinliğe Katıl</h2>
                
                <div class="qr-scanner-container">
                    <!-- Kullanıcı Seçimi -->
                    <div class="user-selection">
                        <label for="user-select">Öğrenci Seç:</label>
                        <select id="user-select" class="form-control">
                            <option value="">Öğrenci seçiniz...</option>
                        </select>
                    </div>

                    <!-- QR Kod Okuyucu -->
                    <div class="qr-reader">
                        <div id="qr-reader-container">
                            <i class="fas fa-qrcode qr-icon"></i>
                            <h3>QR Kodu Okutun</h3>
                            <p>Etkinlik QR kodunu kamera ile taratın veya manuel girin</p>
                        </div>
                        
                        <!-- Manuel QR Kod Girişi -->
                        <div class="manual-qr-input">
                            <label for="qr-code-input">Veya QR Kodu Manuel Girin:</label>
                            <input type="text" id="qr-code-input" class="form-control" placeholder="QR kod...">
                            <button onclick="verifyQRCode()" class="btn-primary">
                                <i class="fas fa-check"></i> Doğrula
                            </button>
                        </div>
                    </div>

                    <!-- Sonuç Mesajı -->
                    <div id="scan-result" class="scan-result hidden">
                        <!-- Sonuç JavaScript ile gösterilecek -->
                    </div>
                </div>

                <!-- Son Katılımlar -->
                <div class="recent-participations">
                    <h3>Son Katılımlar</h3>
                    <div id="recent-list" class="recent-list">
                        <p class="text-muted">Henüz katılım kaydı yok</p>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="footer">
            <p>&copy; 2024 Akademik Takvim Sistemi - Tüm hakları saklıdır</p>
        </footer>
    </div>

    <!-- Başarı Modal -->
    <div id="success-modal" class="popup-modal hidden">
        <div class="popup-content success">
            <div class="popup-icon success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3>Katılım Onaylandı!</h3>
            <div id="success-message"></div>
            <button class="btn-primary" onclick="closeSuccessModal()">Tamam</button>
        </div>
    </div>

    <script src="js/api.js"></script>
    <script src="js/qr-scan.js"></script>
</body>
</html>

