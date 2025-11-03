<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetim Paneli</title>
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
                <a href="qr-scan.php" class="nav-link">QR Okut</a>
                <a href="admin.php" class="nav-link active">Yönetim</a>
            </nav>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <section class="admin-section">
                <h2><i class="fas fa-cog"></i> Yönetim Paneli</h2>
                
                <!-- Tab Menü -->
                <div class="tabs">
                    <button class="tab-btn active" onclick="showTab('events-tab')">
                        <i class="fas fa-calendar-plus"></i> Etkinlik Yönetimi
                    </button>
                    <button class="tab-btn" onclick="showTab('qr-tab')">
                        <i class="fas fa-qrcode"></i> QR Kod Oluştur
                    </button>
                    <button class="tab-btn" onclick="showTab('participants-tab')">
                        <i class="fas fa-users"></i> Katılımcılar
                    </button>
                </div>

                <!-- Etkinlik Yönetimi Tab -->
                <div id="events-tab" class="tab-content active">
                    <div class="form-container">
                        <h3>Yeni Etkinlik Oluştur</h3>
                        <form id="event-form" class="admin-form">
                            <div class="form-group">
                                <label for="event-title">Etkinlik Başlığı *</label>
                                <input type="text" id="event-title" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="event-description">Açıklama</label>
                                <textarea id="event-description" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="event-type">Etkinlik Türü *</label>
                                    <select id="event-type" class="form-control" required>
                                        <option value="">Seçiniz...</option>
                                        <option value="sınav">Sınav</option>
                                        <option value="ödev">Ödev</option>
                                        <option value="etkinlik">Etkinlik</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="event-location">Konum</label>
                                    <input type="text" id="event-location" class="form-control">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="event-start-date">Başlangıç Tarihi *</label>
                                    <input type="datetime-local" id="event-start-date" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="event-end-date">Bitiş Tarihi</label>
                                    <input type="datetime-local" id="event-end-date" class="form-control">
                                </div>
                            </div>

                            <button type="submit" class="btn-primary">
                                <i class="fas fa-plus"></i> Etkinlik Oluştur
                            </button>
                        </form>
                    </div>

                    <!-- Mevcut Etkinlikler -->
                    <div class="existing-events">
                        <h3>Mevcut Etkinlikler</h3>
                        <div id="admin-events-list" class="admin-events-list">
                            <!-- Liste JavaScript ile yüklenecek -->
                        </div>
                    </div>
                </div>

                <!-- QR Kod Oluşturma Tab -->
                <div id="qr-tab" class="tab-content">
                    <div class="form-container">
                        <h3>Etkinlik için QR Kod Oluştur</h3>
                        <form id="qr-form" class="admin-form">
                            <div class="form-group">
                                <label for="qr-event-select">Etkinlik Seç *</label>
                                <select id="qr-event-select" class="form-control" required>
                                    <option value="">Etkinlik seçiniz...</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="qr-validity">Geçerlilik Süresi</label>
                                <input type="datetime-local" id="qr-validity" class="form-control">
                                <small class="form-text">Boş bırakılırsa etkinlik tarihine göre ayarlanır</small>
                            </div>

                            <button type="submit" class="btn-primary">
                                <i class="fas fa-qrcode"></i> QR Kod Oluştur
                            </button>
                        </form>
                    </div>

                    <!-- Oluşturulan QR Kod -->
                    <div id="qr-result" class="qr-result hidden">
                        <h3>Oluşturulan QR Kod</h3>
                        <div id="qr-display" class="qr-display">
                            <!-- QR kod burada gösterilecek -->
                        </div>
                    </div>
                </div>

                <!-- Katılımcılar Tab -->
                <div id="participants-tab" class="tab-content">
                    <div class="form-container">
                        <h3>Katılımcı Listesi</h3>
                        <div class="form-group">
                            <label for="participant-event-select">Etkinlik Seç</label>
                            <select id="participant-event-select" class="form-control" onchange="loadParticipants()">
                                <option value="">Etkinlik seçiniz...</option>
                            </select>
                        </div>
                    </div>

                    <div id="participants-list" class="participants-list">
                        <p class="text-muted">Bir etkinlik seçin</p>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="footer">
            <p>&copy; 2024 Akademik Takvim Sistemi - Tüm hakları saklıdır</p>
        </footer>
    </div>

    <!-- Bildirim Toast -->
    <div id="toast-notification" class="toast hidden">
        <i class="fas fa-check-circle"></i>
        <span id="toast-message">İşlem başarılı</span>
    </div>

    <script src="js/api.js"></script>
    <script src="js/admin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
</body>
</html>

