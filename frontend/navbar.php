<!-- Profesyonel Navigation Bar -->
<style>
  .professional-navbar {
    background: linear-gradient(135deg, #b30000 0%, #8b0000 100%);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    position: sticky;
    top: 0;
    z-index: 999;
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
  
  .nav-brand {
    color: white;
    font-size: 1.1em;
    font-weight: 700;
    text-decoration: none;
    padding: 12px 0;
    letter-spacing: 0.3px;
  }
  
  .nav-menu {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 5px;
    flex-wrap: wrap;
    align-items: center;
  }
  
  .nav-menu li {
    position: relative;
  }
  
  .nav-menu a {
    display: block;
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    padding: 12px 12px;
    font-size: 0.85em;
    font-weight: 500;
    transition: all 0.3s ease;
    border-bottom: 2px solid transparent;
    white-space: nowrap;
  }
  
  .nav-menu a:hover {
    color: white;
    background: rgba(255, 255, 255, 0.15);
    border-bottom-color: #ffcccc;
  }
  
  .nav-menu a.active {
    color: white;
    background: rgba(255, 255, 255, 0.2);
    border-bottom-color: white;
  }
  
  .nav-actions {
    display: flex;
    gap: 10px;
    margin-left: auto;
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
  
  /* Mobile Menu Toggle */
  .mobile-toggle {
    display: none;
    background: none;
    border: none;
    color: white;
    font-size: 1.5em;
    cursor: pointer;
    padding: 10px;
  }
  
  /* Responsive */
  @media (max-width: 1200px) {
    .nav-menu a {
      padding: 12px 8px;
      font-size: 0.8em;
    }
    
    .nav-brand {
      font-size: 1em;
    }
  }
  
  @media (max-width: 968px) {
    .mobile-toggle {
      display: block;
    }
    
    .nav-brand {
      font-size: 0.95em;
    }
    
    .nav-menu {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: #b30000;
      flex-direction: column;
      gap: 0;
      display: none;
      box-shadow: 0 5px 10px rgba(0,0,0,0.3);
    }
    
    .nav-menu.active {
      display: flex;
    }
    
    .nav-menu a {
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
      padding: 15px 20px;
    }
    
    .nav-actions {
      padding: 15px;
      flex-direction: column;
      display: none;
      background: #b30000;
    }
    
    .nav-actions.active {
      display: flex;
    }
  }
  
  @media (max-width: 480px) {
    .nav-container {
      padding: 0 10px;
    }
    
    .nav-brand {
      font-size: 0.85em;
    }
  }
</style>

<nav class="professional-navbar">
  <div class="nav-container">
    <a href="index.php" class="nav-brand">Kampüs Etkinlik Sistemi</a>
    
    <button class="mobile-toggle" onclick="toggleMobileMenu()">
      ☰
    </button>
    
    <ul class="nav-menu" id="navMenu">
      <li><a href="index.php" class="<?= $currentPage == 'index.php' ? 'active' : '' ?>">Ana Sayfa</a></li>
      <li><a href="etkinlikler.php" class="<?= $currentPage == 'etkinlikler.php' ? 'active' : '' ?>">Etkinlikler</a></li>
      <li><a href="takvim.php" class="<?= $currentPage == 'takvim.php' ? 'active' : '' ?>">Akademik Takvim</a></li>
      <li><a href="akademik-takvim.php" class="<?= $currentPage == 'akademik-takvim.php' ? 'active' : '' ?>">Takvim</a></li>
      <li><a href="etkinlik-yonetim.php" class="<?= $currentPage == 'etkinlik-yonetim.php' ? 'active' : '' ?>">Etkinlik Yönetimi</a></li>
      <li><a href="ilgi-alanlari.php" class="<?= $currentPage == 'ilgi-alanlari.php' ? 'active' : '' ?>">İlgi Alanlarım</a></li>
      <li><a href="etkinlik-onerileri.php" class="<?= $currentPage == 'etkinlik-onerileri.php' ? 'active' : '' ?>">Öneriler</a></li>
      <li><a href="rezervasyon-yap.php" class="<?= $currentPage == 'rezervasyon-yap.php' ? 'active' : '' ?>">Kütüphane Rezervasyonu</a></li>
    </ul>
    
    <div class="nav-actions" id="navActions">
      <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="login.php" class="nav-btn nav-btn-secondary">Giriş Yap</a>
        <a href="signup.php" class="nav-btn nav-btn-primary">Kayıt Ol</a>
      <?php else: ?>
        <a href="profile.php" class="nav-btn nav-btn-secondary">Profilim</a>
        <a href="logout.php" class="nav-btn nav-btn-primary">Çıkış</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<script>
function toggleMobileMenu() {
  const menu = document.getElementById('navMenu');
  const actions = document.getElementById('navActions');
  menu.classList.toggle('active');
  actions.classList.toggle('active');
}
</script>

