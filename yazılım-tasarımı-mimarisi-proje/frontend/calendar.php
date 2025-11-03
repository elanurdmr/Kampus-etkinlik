<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademik Takvim</title>
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
                <a href="calendar.php" class="nav-link active">Takvim</a>
                <a href="qr-scan.php" class="nav-link">QR Okut</a>
                <a href="admin.php" class="nav-link">Yönetim</a>
            </nav>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <section class="calendar-section">
                <h2><i class="fas fa-calendar"></i> Akademik Takvim</h2>
                
                <!-- Filtreler -->
                <div class="filters">
                    <select id="event-type-filter" onchange="filterEvents()">
                        <option value="">Tüm Etkinlikler</option>
                        <option value="sınav">Sınavlar</option>
                        <option value="ödev">Ödevler</option>
                        <option value="etkinlik">Etkinlikler</option>
                    </select>
                </div>

                <!-- Takvim Görünümü -->
                <div class="calendar-view">
                    <div id="calendar-container">
                        <div class="loading">
                            <i class="fas fa-spinner fa-spin"></i> Takvim yükleniyor...
                        </div>
                    </div>
                </div>

                <!-- Liste Görünümü -->
                <div class="list-view">
                    <h3>Etkinlik Listesi</h3>
                    <div id="events-list" class="events-list">
                        <!-- Etkinlikler JavaScript ile yüklenecek -->
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="footer">
            <p>&copy; 2024 Akademik Takvim Sistemi - Tüm hakları saklıdır</p>
        </footer>
    </div>

    <!-- Etkinlik Detay Modal -->
    <div id="event-detail-modal" class="popup-modal hidden">
        <div class="popup-content large">
            <button class="close-popup" onclick="closeEventDetail()">
                <i class="fas fa-times"></i>
            </button>
            <h3 id="event-detail-title"></h3>
            <div id="event-detail-content" class="event-detail">
                <!-- Detaylar JavaScript ile yüklenecek -->
            </div>
        </div>
    </div>

    <script src="js/api.js"></script>
    <script src="js/calendar.js"></script>
</body>
</html>

