<?php
// Session kontrolü - eğer session başlatılmamışsa başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// $currentPage değişkeni yoksa tanımla
if (!isset($currentPage)) {
    $currentPage = basename($_SERVER['PHP_SELF']);
}
?>
<!-- Profesyonel Navigation Bar -->
<style>
  /* Sidebar Overlay */
  .sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 998;
    display: none;
    opacity: 0;
    transition: opacity 0.3s;
  }
  
  .sidebar-overlay.active {
    display: block;
    opacity: 1;
  }
  
  /* Sidebar */
  .sidebar {
    position: fixed;
    top: 0;
    left: -300px;
    width: 300px;
    height: 100vh;
    background: white;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    z-index: 999;
    transition: left 0.3s ease;
    overflow-y: auto;
  }
  
  .sidebar.active {
    left: 0;
  }
  
  .sidebar-header {
    background: linear-gradient(135deg, #b30000 0%, #8b0000 100%);
    color: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 1;
  }
  
  .sidebar-header h3 {
    margin: 0;
    font-size: 1.2em;
  }
  
  .sidebar-close {
    background: none;
    border: none;
    color: white;
    font-size: 1.5em;
    cursor: pointer;
    padding: 5px;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background 0.3s;
  }
  
  .sidebar-close:hover {
    background: rgba(255, 255, 255, 0.2);
  }
  
  .sidebar-menu {
    list-style: none;
    margin: 0;
    padding: 10px 0;
    padding-bottom: 20px;
  }
  
  .sidebar::-webkit-scrollbar {
    width: 6px;
  }
  
  .sidebar::-webkit-scrollbar-track {
    background: #f1f1f1;
  }
  
  .sidebar::-webkit-scrollbar-thumb {
    background: #b30000;
    border-radius: 10px;
  }
  
  .sidebar::-webkit-scrollbar-thumb:hover {
    background: #8b0000;
  }
  
  .sidebar-menu li {
    margin: 0;
  }
  
  .sidebar-menu a {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    color: #333;
    text-decoration: none;
    transition: all 0.3s;
    border-left: 3px solid transparent;
  }
  
  .sidebar-menu a:hover {
    background: #f5f5f5;
    border-left-color: #b30000;
    color: #b30000;
  }
  
  .sidebar-menu a.active {
    background: #fff5f5;
    border-left-color: #b30000;
    color: #b30000;
    font-weight: 600;
  }
  
  .sidebar-menu-icon {
    margin-right: 12px;
    font-size: 1em;
    width: 20px;
    text-align: center;
    color: #b30000;
    font-weight: bold;
  }
  
  .sidebar-divider {
    height: 1px;
    background: #eee;
    margin: 10px 20px;
  }
  
  .sidebar-section-title {
    padding: 15px 20px 5px;
    font-size: 0.75em;
    text-transform: uppercase;
    color: #999;
    font-weight: 600;
    letter-spacing: 0.5px;
  }
  
  .professional-navbar {
    background: linear-gradient(135deg, #b30000 0%, #8b0000 100%);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    position: sticky;
    top: 0;
    z-index: 997;
  }
  
  .nav-container {
    max-width: 100%;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
    flex-wrap: wrap;
    gap: 10px;
  }
  
  .nav-left {
    display: flex;
    align-items: center;
    gap: 15px;
  }
  
  .sidebar-toggle {
    background: none;
    border: none;
    color: white;
    font-size: 1.5em;
    cursor: pointer;
    padding: 10px;
    border-radius: 50%;
    transition: background 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .sidebar-toggle:hover {
    background: rgba(255, 255, 255, 0.15);
  }
  
  .nav-brand {
    color: white;
    font-size: 1.1em;
    font-weight: 700;
    text-decoration: none;
    padding: 12px 0;
    letter-spacing: 0.3px;
  }
  
  .nav-actions {
    display: flex;
    gap: 10px;
    margin-left: auto;
    align-items: center;
  }
  
  /* Bildirim İkonu */
  .bildirim-icon-wrapper {
    position: relative;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: background 0.3s;
  }
  
  .bildirim-icon-wrapper:hover {
    background: rgba(255, 255, 255, 0.15);
  }
  
  .bildirim-icon {
    width: 24px;
    height: 24px;
    position: relative;
    display: block;
  }
  
  /* Zil tutma yeri (üstte küçük düz çubuk) */
  .bildirim-icon::before {
    content: "";
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 6px;
    height: 3px;
    background: white;
    border-radius: 2px 2px 0 0;
    box-sizing: border-box;
  }
  
  /* Zil gövdesi - üstte geniş, altta dar, yuvarlak */
  .bildirim-icon::after {
    content: "";
    position: absolute;
    top: 3px;
    left: 50%;
    transform: translateX(-50%);
    width: 18px;
    height: 16px;
    border: 2px solid white;
    border-bottom: none;
    border-radius: 50% 50% 0 0;
    box-sizing: border-box;
  }
  
  /* Zil dili - içteki küçük yarım daire */
  .bildirim-zil-dili {
    position: absolute;
    top: 12px;
    left: 50%;
    transform: translateX(-50%);
    width: 6px;
    height: 4px;
    background: white;
    border-radius: 0 0 50% 50%;
    display: block;
  }
  
  .bildirim-badge {
    position: absolute;
    top: 5px;
    right: 5px;
    background: #ff8800;
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7em;
    font-weight: bold;
    border: 2px solid #b30000;
    opacity: 0;
    transform: scale(0);
    transition: all 0.3s;
  }
  
  .bildirim-badge.active {
    opacity: 1;
    transform: scale(1);
  }
  
  /* Bildirim Dropdown */
  .bildirim-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    width: 380px;
    max-height: 500px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    z-index: 1000;
    display: none;
    flex-direction: column;
    overflow: hidden;
  }
  
  .bildirim-dropdown.active {
    display: flex;
  }
  
  .bildirim-dropdown-header {
    background: linear-gradient(135deg, #b30000 0%, #8b0000 100%);
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .bildirim-dropdown-header h3 {
    margin: 0;
    font-size: 1.1em;
  }
  
  .bildirim-dropdown-close {
    background: none;
    border: none;
    color: white;
    font-size: 1.5em;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background 0.3s;
  }
  
  .bildirim-dropdown-close:hover {
    background: rgba(255, 255, 255, 0.2);
  }
  
  .bildirim-dropdown-body {
    max-height: 400px;
    overflow-y: auto;
    padding: 10px;
  }
  
  .bildirim-dropdown-body::-webkit-scrollbar {
    width: 6px;
  }
  
  .bildirim-dropdown-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
  }
  
  .bildirim-dropdown-body::-webkit-scrollbar-thumb {
    background: #b30000;
    border-radius: 10px;
  }
  
  .bildirim-dropdown-body::-webkit-scrollbar-thumb:hover {
    background: #8b0000;
  }
  
  .bildirim-dropdown-item {
    padding: 12px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background 0.2s;
    border-radius: 8px;
    margin-bottom: 5px;
  }
  
  .bildirim-dropdown-item:hover {
    background: #f5f5f5;
  }
  
  .bildirim-dropdown-item.okunmamis {
    background: #fff5f5;
    border-left: 3px solid #b30000;
  }
  
  .bildirim-dropdown-item-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 5px;
  }
  
  .bildirim-dropdown-item-baslik {
    font-weight: 600;
    color: #333;
    font-size: 0.95em;
  }
  
  .bildirim-dropdown-item.okunmamis .bildirim-dropdown-item-baslik {
    color: #b30000;
  }
  
  .bildirim-dropdown-item-tarih {
    font-size: 0.75em;
    color: #999;
    white-space: nowrap;
    margin-left: 10px;
  }
  
  .bildirim-dropdown-item-mesaj {
    font-size: 0.85em;
    color: #666;
    line-height: 1.4;
  }
  
  .bildirim-dropdown-empty {
    text-align: center;
    padding: 40px 20px;
    color: #999;
  }
  
  .bildirim-dropdown-footer {
    padding: 10px;
    border-top: 1px solid #eee;
    text-align: center;
  }
  
  .bildirim-dropdown-footer a {
    color: #b30000;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9em;
  }
  
  .bildirim-dropdown-footer a:hover {
    text-decoration: underline;
  }
  
  @media (max-width: 768px) {
    .bildirim-dropdown {
      width: calc(100vw - 20px);
      right: 10px;
      max-height: 70vh;
    }
    
    .bildirim-dropdown-body {
      max-height: calc(70vh - 120px);
    }
  }
  
  .nav-btn {
    padding: 8px 16px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 0.8em;
    font-weight: 600;
    transition: all 0.3s;
    border: 2px solid transparent;
  }
  
  .nav-btn-primary {
    background: white;
    color: #b30000;
  }
  
  .nav-btn-primary:hover {
    background: #f0f0f0;
    transform: translateY(-1px);
  }
  
  .nav-btn-secondary {
    background: transparent;
    color: white;
    border-color: rgba(255, 255, 255, 0.5);
  }
  
  .nav-btn-secondary:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: white;
  }
  
  /* Kullanıcı Menüsü */
  .user-menu-wrapper {
    position: relative;
  }
  
  .user-menu-trigger {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    color: white;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 0.85em;
  }
  
  .user-menu-trigger:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.4);
  }
  
  .user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: white;
    color: #b30000;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.9em;
  }
  
  .user-name {
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  
  .user-menu-arrow {
    width: 0;
    height: 0;
    border-left: 4px solid transparent;
    border-right: 4px solid transparent;
    border-top: 5px solid white;
    transition: transform 0.3s;
  }
  
  .user-menu-wrapper.active .user-menu-arrow {
    transform: rotate(180deg);
  }
  
  .user-menu-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    width: 200px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 1000;
    display: none;
    overflow: hidden;
    opacity: 0;
    transform: translateY(-10px);
    transition: all 0.3s;
  }
  
  .user-menu-dropdown.active {
    display: block;
    opacity: 1;
    transform: translateY(0);
  }
  
  .user-menu-header {
    padding: 15px;
    background: linear-gradient(135deg, #b30000 0%, #8b0000 100%);
    color: white;
    border-bottom: 1px solid #eee;
  }
  
  .user-menu-header-name {
    font-weight: 600;
    font-size: 0.95em;
    margin-bottom: 3px;
  }
  
  .user-menu-header-email {
    font-size: 0.75em;
    opacity: 0.9;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  
  .user-menu-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 15px;
    color: #333;
    text-decoration: none;
    transition: background 0.2s;
    border-bottom: 1px solid #f0f0f0;
    font-size: 0.9em;
  }
  
  .user-menu-item:last-child {
    border-bottom: none;
  }
  
  .user-menu-item:hover {
    background: #f5f5f5;
    color: #b30000;
  }
  
  .user-menu-item.logout {
    color: #dc3545;
    border-top: 1px solid #f0f0f0;
  }
  
  .user-menu-item.logout:hover {
    background: #fff5f5;
    color: #c82333;
  }
  
  .user-menu-icon {
    width: 18px;
    text-align: center;
    font-size: 0.8em;
    color: #b30000;
    font-weight: bold;
  }
  
  /* Giriş Butonları */
  .auth-buttons {
    display: flex;
    gap: 8px;
  }
  
  .auth-btn {
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.85em;
    font-weight: 600;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
  }
  
  .auth-btn-login {
    background: transparent;
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.5);
  }
  
  .auth-btn-login:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: white;
  }
  
  .auth-btn-signup {
    background: white;
    color: #b30000;
  }
  
  .auth-btn-signup:hover {
    background: #f0f0f0;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
  
  /* Responsive */
  @media (max-width: 768px) {
    .sidebar {
      width: 280px;
      left: -280px;
    }
    
    .nav-brand {
      font-size: 0.9em;
    }
  }
  
  @media (max-width: 768px) {
    .user-name {
      display: none;
    }
    
    .user-menu-dropdown {
      right: -10px;
      width: 180px;
    }
    
    .auth-buttons {
      flex-direction: column;
      gap: 5px;
    }
    
    .auth-btn {
      padding: 6px 12px;
      font-size: 0.8em;
    }
  }
  
  @media (max-width: 480px) {
    .nav-container {
      padding: 0 10px;
    }
    
    .nav-brand {
      font-size: 0.85em;
    }
    
    .sidebar {
      width: 100%;
      left: -100%;
    }
    
    .user-menu-dropdown {
      width: 160px;
    }
  }
</style>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <h3>Menü</h3>
    <button class="sidebar-close" onclick="toggleSidebar()">×</button>
  </div>
  
  <ul class="sidebar-menu">
    <li><a href="index.php" class="<?= $currentPage == 'index.php' ? 'active' : '' ?>">
      <span class="sidebar-menu-icon">•</span>
      <span>Ana Sayfa</span>
    </a></li>
    
    <div class="sidebar-divider"></div>
    <div class="sidebar-section-title">Etkinlikler</div>
    
    <li><a href="etkinlikler.php" class="<?= $currentPage == 'etkinlikler.php' ? 'active' : '' ?>">
      <span class="sidebar-menu-icon">•</span>
      <span>Etkinlikler</span>
    </a></li>
    <li><a href="takvim.php" class="<?= $currentPage == 'takvim.php' ? 'active' : '' ?>">
      <span class="sidebar-menu-icon">•</span>
      <span>Akademik Takvim</span>
    </a></li>
    <li><a href="akademik-takvim.php" class="<?= $currentPage == 'akademik-takvim.php' ? 'active' : '' ?>">
      <span class="sidebar-menu-icon">•</span>
      <span>Takvim</span>
    </a></li>
    <li><a href="etkinlik-yonetim.php" class="<?= $currentPage == 'etkinlik-yonetim.php' ? 'active' : '' ?>">
      <span class="sidebar-menu-icon">•</span>
      <span>Etkinlik Yönetimi</span>
    </a></li>
    <li><a href="ilgi-alanlari.php" class="<?= $currentPage == 'ilgi-alanlari.php' ? 'active' : '' ?>">
      <span class="sidebar-menu-icon">•</span>
      <span>İlgi Alanlarım</span>
    </a></li>
    <li><a href="etkinlik-onerileri.php" class="<?= $currentPage == 'etkinlik-onerileri.php' ? 'active' : '' ?>">
      <span class="sidebar-menu-icon">•</span>
      <span>Öneriler</span>
    </a></li>
    
    <div class="sidebar-divider"></div>
    <div class="sidebar-section-title">Rezervasyonlar</div>
    
    <li><a href="rezervasyon-yap.php" class="<?= $currentPage == 'rezervasyon-yap.php' ? 'active' : '' ?>">
      <span class="sidebar-menu-icon">•</span>
      <span>Kütüphane Rezervasyonu</span>
    </a></li>
    
    <div class="sidebar-divider"></div>
    <div class="sidebar-section-title">Randevular</div>
    
    <li><a href="ogretim-uyesi-program.php" class="<?= $currentPage == 'ogretim-uyesi-program.php' ? 'active' : '' ?>">
      <span class="sidebar-menu-icon">•</span>
      <span>Öğretim Üyesi Programları</span>
    </a></li>
    <li><a href="randevu-olustur.php" class="<?= $currentPage == 'randevu-olustur.php' ? 'active' : '' ?>">
      <span class="sidebar-menu-icon">•</span>
      <span>Randevu Al</span>
    </a></li>
    <li><a href="randevularim.php" class="<?= $currentPage == 'randevularim.php' ? 'active' : '' ?>">
      <span class="sidebar-menu-icon">•</span>
      <span>Randevularım</span>
    </a></li>
  </ul>
</div>

<nav class="professional-navbar">
  <div class="nav-container">
    <div class="nav-left">
      <button class="sidebar-toggle" onclick="toggleSidebar()">
        ☰
      </button>
      <a href="index.php" class="nav-brand">Kampüs Etkinlik Sistemi</a>
    </div>
    
    <div class="nav-actions" id="navActions">
      <div class="bildirim-icon-wrapper" onclick="toggleBildirimDropdown(event)">
        <span class="bildirim-icon">
          <span class="bildirim-zil-dili"></span>
        </span>
        <span class="bildirim-badge" id="bildirimBadge"></span>
        
        <div class="bildirim-dropdown" id="bildirimDropdown">
          <div class="bildirim-dropdown-header">
            <h3>Bildirimler</h3>
            <button class="bildirim-dropdown-close" onclick="toggleBildirimDropdown(event)">×</button>
          </div>
          <div class="bildirim-dropdown-body" id="bildirimDropdownBody">
            <div class="loading" style="text-align: center; padding: 20px;">
              <p>Yükleniyor...</p>
            </div>
          </div>
          <div class="bildirim-dropdown-footer">
            <a href="bildirimler.php">Tümünü Gör</a>
          </div>
        </div>
      </div>
      
      <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="auth-buttons">
          <a href="login.php" class="auth-btn auth-btn-login">Giriş Yap</a>
          <a href="signup.php" class="auth-btn auth-btn-signup">Kayıt Ol</a>
        </div>
      <?php else: ?>
        <div class="user-menu-wrapper" onclick="toggleUserMenu(event)">
          <div class="user-menu-trigger">
            <div class="user-avatar">
              <?php 
                $userInitial = 'K';
                if (isset($_SESSION['ad'])) {
                  $userInitial = strtoupper(substr($_SESSION['ad'], 0, 1));
                }
                echo $userInitial;
              ?>
            </div>
            <span class="user-name">
              <?php echo isset($_SESSION['ad']) ? htmlspecialchars($_SESSION['ad']) : 'Kullanıcı'; ?>
            </span>
            <span class="user-menu-arrow"></span>
          </div>
          
          <div class="user-menu-dropdown" id="userMenuDropdown">
            <div class="user-menu-header">
              <div class="user-menu-header-name">
                <?php 
                  $fullName = 'Kullanıcı';
                  if (isset($_SESSION['ad']) && isset($_SESSION['soyad'])) {
                    $fullName = htmlspecialchars($_SESSION['ad'] . ' ' . $_SESSION['soyad']);
                  } elseif (isset($_SESSION['ad'])) {
                    $fullName = htmlspecialchars($_SESSION['ad']);
                  }
                  echo $fullName;
                ?>
              </div>
              <div class="user-menu-header-email">
                <?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>
              </div>
            </div>
            
            <a href="profile.php" class="user-menu-item">
              <span class="user-menu-icon">•</span>
              <span>Profilim</span>
            </a>
            
            <a href="randevularim.php" class="user-menu-item">
              <span class="user-menu-icon">•</span>
              <span>Randevularım</span>
            </a>
            
            <a href="bildirimler.php" class="user-menu-item">
              <span class="user-menu-icon">•</span>
              <span>Bildirimler</span>
            </a>
            
            <a href="logout.php" class="user-menu-item logout">
              <span class="user-menu-icon">•</span>
              <span>Çıkış Yap</span>
            </a>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</nav>

<script>
// Global değişkenler - window objesinde sakla
if (typeof window.BILDIRIM_API_URL === 'undefined') {
  window.BILDIRIM_API_URL = 'http://localhost:8000/api/bildirimler';
}
if (typeof window.kullaniciId === 'undefined') {
  window.kullaniciId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0; ?>;
}

// Sidebar toggle
function toggleSidebar() {
  try {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (!sidebar || !overlay) return;
    
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
    
    // Body scroll'u engelle
    if (sidebar.classList.contains('active')) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
  } catch (e) {
    console.error('Sidebar toggle hatası:', e);
  }
}

// Sidebar dışına tıklanınca kapat
document.addEventListener('click', function(event) {
  try {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggle = document.querySelector('.sidebar-toggle');
    
    if (sidebar && overlay && toggle &&
        sidebar.classList.contains('active') && 
        !sidebar.contains(event.target) && 
        !toggle.contains(event.target)) {
      toggleSidebar();
    }
  } catch (e) {
    console.error('Sidebar kapatma hatası:', e);
  }
});

// Kullanıcı menüsü toggle
function toggleUserMenu(event) {
  try {
    if (event) event.stopPropagation();
    const dropdown = document.getElementById('userMenuDropdown');
    const wrapper = event ? event.currentTarget : document.querySelector('.user-menu-wrapper');
    
    if (!dropdown || !wrapper) return;
    
    dropdown.classList.toggle('active');
    wrapper.classList.toggle('active');
  } catch (e) {
    console.error('Kullanıcı menüsü toggle hatası:', e);
  }
}

// Kullanıcı menüsü dışına tıklanınca kapat
document.addEventListener('click', function(event) {
  try {
    const userMenu = document.getElementById('userMenuDropdown');
    const userMenuWrapper = document.querySelector('.user-menu-wrapper');
    
    if (userMenu && userMenuWrapper && 
        userMenu.classList.contains('active') && 
        !userMenuWrapper.contains(event.target)) {
      userMenu.classList.remove('active');
      userMenuWrapper.classList.remove('active');
    }
  } catch (e) {
    console.error('Kullanıcı menüsü kapatma hatası:', e);
  }
});

// Sidebar linklerine tıklandığında kapat
document.addEventListener('DOMContentLoaded', function() {
  const sidebarLinks = document.querySelectorAll('.sidebar-menu a');
  if (sidebarLinks.length > 0) {
    sidebarLinks.forEach(link => {
      link.addEventListener('click', function() {
        // Eğer aynı sayfadaysa kapat
        if (this.classList.contains('active')) {
          toggleSidebar();
        } else {
          // Farklı sayfaya gidiyorsa kapat
          setTimeout(() => toggleSidebar(), 100);
        }
      });
    });
  }
});

// Bildirim dropdown toggle
function toggleBildirimDropdown(event) {
  try {
    if (event) event.stopPropagation();
    const dropdown = document.getElementById('bildirimDropdown');
    
    if (!dropdown) return;
    
    dropdown.classList.toggle('active');
    
    if (dropdown.classList.contains('active')) {
      bildirimleriYukle();
    }
  } catch (e) {
    console.error('Bildirim dropdown toggle hatası:', e);
  }
}

// Dışarı tıklanınca kapat
document.addEventListener('click', function(event) {
  try {
    const dropdown = document.getElementById('bildirimDropdown');
    const iconWrapper = document.querySelector('.bildirim-icon-wrapper');
    
    if (dropdown && iconWrapper && !dropdown.contains(event.target) && !iconWrapper.contains(event.target)) {
      dropdown.classList.remove('active');
    }
  } catch (e) {
    console.error('Bildirim dropdown kapatma hatası:', e);
  }
});

// Okunmamış bildirim sayısını güncelle
async function okunmamisSayisiGuncelle() {
  try {
    if (!window.kullaniciId || window.kullaniciId <= 0) {
      const badge = document.getElementById('bildirimBadge');
      if (badge) badge.classList.remove('active');
      return;
    }
    
    const response = await fetch(`${window.BILDIRIM_API_URL}/kullanici/${window.kullaniciId}/bildirimler/okunmamis-sayisi`);
    if (!response.ok) return;
    
    const data = await response.json();
    const badge = document.getElementById('bildirimBadge');
    
    if (badge) {
      if (data.okunmamis_sayisi > 0) {
        badge.textContent = data.okunmamis_sayisi > 99 ? '99+' : data.okunmamis_sayisi;
        badge.classList.add('active');
      } else {
        badge.classList.remove('active');
      }
    }
  } catch (error) {
    // Sessizce hata yakala - backend çalışmıyor olabilir
    console.error('Bildirim sayısı güncelleme hatası:', error);
  }
}

// Bildirimleri yükle
async function bildirimleriYukle() {
  try {
    const body = document.getElementById('bildirimDropdownBody');
    if (!body) return;
    
    if (!window.kullaniciId || window.kullaniciId <= 0) {
      body.innerHTML = '<div class="bildirim-dropdown-empty">Giriş yapmanız gerekiyor</div>';
      return;
    }
    
    body.innerHTML = '<div class="loading" style="text-align: center; padding: 20px;"><p>Yükleniyor...</p></div>';
    
    const response = await fetch(`${window.BILDIRIM_API_URL}/kullanici/${window.kullaniciId}/bildirimler?limit=10`);
    if (!response.ok) {
      body.innerHTML = '<div class="bildirim-dropdown-empty">Bağlantı hatası</div>';
      return;
    }
    
    const bildirimler = await response.json();
    
    if (!bildirimler || bildirimler.length === 0) {
      body.innerHTML = '<div class="bildirim-dropdown-empty">Henüz bildiriminiz yok</div>';
      return;
    }
    
    let html = '';
    bildirimler.forEach(bildirim => {
      const tarih = new Date(bildirim.olusturma_tarihi).toLocaleString('tr-TR', {
        day: '2-digit',
        month: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
      });
      
      html += `
        <div class="bildirim-dropdown-item ${!bildirim.okundu ? 'okunmamis' : ''}" 
             onclick="bildirimOkunduIsaretle(${bildirim.id}, event)">
          <div class="bildirim-dropdown-item-header">
            <div class="bildirim-dropdown-item-baslik">${bildirim.baslik || ''}</div>
            <div class="bildirim-dropdown-item-tarih">${tarih}</div>
          </div>
          <div class="bildirim-dropdown-item-mesaj">${bildirim.mesaj || ''}</div>
        </div>
      `;
    });
    
    body.innerHTML = html;
  } catch (error) {
    console.error('Bildirim yükleme hatası:', error);
    const body = document.getElementById('bildirimDropdownBody');
    if (body) {
      body.innerHTML = '<div class="bildirim-dropdown-empty">Yüklenirken hata oluştu</div>';
    }
  }
}

// Bildirimi okundu işaretle
async function bildirimOkunduIsaretle(bildirimId, event) {
  try {
    if (event) event.stopPropagation();
    
    if (!bildirimId) return;
    
    const response = await fetch(`${window.BILDIRIM_API_URL}/bildirim/${bildirimId}/okundu`, {
      method: 'PUT'
    });
    
    if (response.ok) {
      // UI'ı güncelle
      if (event && event.currentTarget) {
        const item = event.currentTarget;
        item.classList.remove('okunmamis');
        const baslik = item.querySelector('.bildirim-dropdown-item-baslik');
        if (baslik) baslik.style.color = '#333';
      }
      
      // Badge'i güncelle (turuncu nokta kaybolacak)
      okunmamisSayisiGuncelle();
    }
  } catch (error) {
    console.error('Bildirim okundu işaretleme hatası:', error);
  }
}

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
  try {
    if (window.kullaniciId && window.kullaniciId > 0) {
      okunmamisSayisiGuncelle();
      // Her 30 saniyede bir güncelle
      setInterval(okunmamisSayisiGuncelle, 30000);
    }
  } catch (e) {
    console.error('Sayfa yükleme hatası:', e);
  }
});
</script>

