<?php require_once "lang.php"; ?>
<footer class="footer-info">
  <div class="footer-container">
    <div class="footer-column">
      <h4><?= t('Hakkımızda', 'About Us') ?></h4>
      <p>
        <?= t(
          'Kampüs Etkinlik Takip Sistemi, öğrencilerin üniversite içi etkinlikleri kolayca takip etmesini sağlar.',
          'The Campus Event Tracking System helps students easily follow on‑campus events.'
        ); ?>
      </p>
    </div>

    <div class="footer-column">
      <h4><?= t('Bize Ulaşın', 'Contact Us') ?></h4>
      <p><?= t('E-posta', 'Email') ?>: <a href="mailto:info@kampusetkinlik.com">info@kampusetkinlik.com</a></p>
      <p><?= t('Telefon', 'Phone') ?>: +90 212 123 45 67</p>
      <p><?= t('Adres', 'Address') ?>: Doğuş Üniversitesi</p>
    </div>

    <div class="footer-column">
      <h4><?= t('Yasal Bilgiler', 'Legal') ?></h4>
      <ul>
        <li><a href="#"><?= t('Gizlilik Politikası', 'Privacy Policy') ?></a></li>
        <li><a href="#"><?= t('Kullanım Koşulları', 'Terms of Use') ?></a></li>
        <li><a href="#"><?= t('Çerez Politikası', 'Cookie Policy') ?></a></li>
      </ul>
    </div>

    <div class="footer-column">
      <h4><?= t('Bizi Takip Edin', 'Follow Us') ?></h4>
      <div class="social-icons">
        <a href="#" class="social-icon facebook" title="Facebook">f</a>
        <a href="#" class="social-icon instagram" title="Instagram">📷</a>
        <a href="#" class="social-icon twitter" title="Twitter">🐦</a>
        <a href="#" class="social-icon linkedin" title="LinkedIn">in</a>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <p>
      © 2025 <?= t('Kampüs Etkinlik Takip Sistemi | Tüm Hakları Saklıdır.', 'Campus Event Tracking System | All rights reserved.') ?>
    </p>
  </div>
</footer>
