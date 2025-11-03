<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademik Takvim ve QR Sistem</title>
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
                <a href="index.php" class="nav-link active">Ana Sayfa</a>
                <a href="calendar.php" class="nav-link">Takvim</a>
                <a href="qr-scan.php" class="nav-link">QR Okut</a>
                <a href="admin.php" class="nav-link">Yönetim</a>
            </nav>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Hero Section -->
            <section class="hero">
                <h2>Akademik Etkinlikleri Takip Edin</h2>
                <p>Sınavlar, ödevler ve etkinlikler için geri sayım ve anında bildirimler</p>
            </section>

            <!-- Yaklaşan Etkinlikler -->
            <section class="upcoming-events">
                <div class="section-header">
                    <h3><i class="fas fa-calendar-alt"></i> Yaklaşan Etkinlikler</h3>
                    <button onclick="loadUpcomingEvents()" class="btn-refresh">
                        <i class="fas fa-sync-alt"></i> Yenile
                    </button>
                </div>
                <div id="events-container" class="events-grid">
                    <!-- Etkinlikler JavaScript ile yüklenecek -->
                    <div class="loading">
                        <i class="fas fa-spinner fa-spin"></i> Yükleniyor...
                    </div>
                </div>
            </section>

            <!-- İstatistikler -->
            <section class="stats">
                <div class="stat-card">
                    <i class="fas fa-calendar-check"></i>
                    <h4>Toplam Etkinlik</h4>
                    <p id="total-events">0</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-exclamation-circle"></i>
                    <h4>Yaklaşan</h4>
                    <p id="upcoming-count">0</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <h4>Katılımlarım</h4>
                    <p id="my-participations">0</p>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="footer">
            <p>&copy; 2024 Akademik Takvim Sistemi - Tüm hakları saklıdır</p>
        </footer>
    </div>

    <!-- Pop-up Modal -->
    <div id="countdown-popup" class="popup-modal hidden">
        <div class="popup-content">
            <button class="close-popup" onclick="closePopup()">
                <i class="fas fa-times"></i>
            </button>
            <div class="popup-icon">
                <i class="fas fa-bell"></i>
            </div>
            <h3 id="popup-title">Etkinlik Yaklaşıyor!</h3>
            <p id="popup-message"></p>
            <div class="countdown-display" id="countdown-display">
                <div class="countdown-item">
                    <span id="days-left" class="countdown-number">0</span>
                    <span class="countdown-label">Gün</span>
                </div>
                <div class="countdown-item">
                    <span id="hours-left" class="countdown-number">0</span>
                    <span class="countdown-label">Saat</span>
                </div>
                <div class="countdown-item">
                    <span id="minutes-left" class="countdown-number">0</span>
                    <span class="countdown-label">Dakika</span>
                </div>
            </div>
            <button class="btn-primary" onclick="closePopup()">Tamam</button>
        </div>
    </div>

    <script src="js/api.js"></script>
    <script src="js/main.js"></script>
    <script src="js/popup.js"></script>
</body>
</html>

