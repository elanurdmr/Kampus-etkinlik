-- Akademik Takvim ve QR Sistem Veritabanı
-- MySQL Veritabanı Oluşturma ve Tablo Tanımları

-- Veritabanı oluştur
CREATE DATABASE IF NOT EXISTS akademik_sistem CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE akademik_sistem;

-- Kullanıcılar Tablosu
CREATE TABLE IF NOT EXISTS kullanicilar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad VARCHAR(100) NOT NULL,
    soyad VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    ogrenci_no VARCHAR(50) UNIQUE NOT NULL,
    rol VARCHAR(50) DEFAULT 'ogrenci',
    olusturma_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP,
    aktif BOOLEAN DEFAULT TRUE,
    INDEX idx_email (email),
    INDEX idx_ogrenci_no (ogrenci_no)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Akademik Etkinlikler Tablosu
CREATE TABLE IF NOT EXISTS akademik_etkinlikler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    baslik VARCHAR(200) NOT NULL,
    aciklama TEXT,
    etkinlik_turu VARCHAR(50) NOT NULL COMMENT 'sınav, ödev, etkinlik',
    baslangic_tarihi DATETIME NOT NULL,
    bitis_tarihi DATETIME,
    konum VARCHAR(200),
    olusturma_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP,
    guncellenme_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    aktif BOOLEAN DEFAULT TRUE,
    INDEX idx_baslangic (baslangic_tarihi),
    INDEX idx_etkinlik_turu (etkinlik_turu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- QR Kodlar Tablosu
CREATE TABLE IF NOT EXISTS qr_kodlar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    etkinlik_id INT NOT NULL,
    qr_kod VARCHAR(500) UNIQUE NOT NULL,
    olusturma_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP,
    gecerlilik_suresi DATETIME,
    aktif BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (etkinlik_id) REFERENCES akademik_etkinlikler(id) ON DELETE CASCADE,
    INDEX idx_qr_kod (qr_kod),
    INDEX idx_etkinlik_id (etkinlik_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Katılımlar Tablosu
CREATE TABLE IF NOT EXISTS katilimlar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kullanici_id INT NOT NULL,
    etkinlik_id INT NOT NULL,
    qr_kod_id INT,
    katilim_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP,
    onaylandi BOOLEAN DEFAULT FALSE,
    katilim_turu VARCHAR(50) DEFAULT 'qr_kod' COMMENT 'qr_kod, manuel',
    FOREIGN KEY (kullanici_id) REFERENCES kullanicilar(id) ON DELETE CASCADE,
    FOREIGN KEY (etkinlik_id) REFERENCES akademik_etkinlikler(id) ON DELETE CASCADE,
    FOREIGN KEY (qr_kod_id) REFERENCES qr_kodlar(id) ON DELETE SET NULL,
    INDEX idx_kullanici (kullanici_id),
    INDEX idx_etkinlik (etkinlik_id),
    UNIQUE KEY unique_katilim (kullanici_id, etkinlik_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Geri Sayım Ayarları Tablosu
CREATE TABLE IF NOT EXISTS geri_sayim_ayarlari (
    id INT AUTO_INCREMENT PRIMARY KEY,
    etkinlik_id INT NOT NULL,
    geri_sayim_suresi INT DEFAULT 24 COMMENT 'Saat cinsinden',
    popup_goster BOOLEAN DEFAULT TRUE,
    bildirim_gonder BOOLEAN DEFAULT TRUE,
    olusturma_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (etkinlik_id) REFERENCES akademik_etkinlikler(id) ON DELETE CASCADE,
    INDEX idx_etkinlik_id (etkinlik_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Örnek veri ekle
INSERT INTO kullanicilar (ad, soyad, email, ogrenci_no, rol) VALUES
('Ahmet', 'Yılmaz', 'ahmet.yilmaz@universite.edu.tr', '2021001001', 'ogrenci'),
('Ayşe', 'Kaya', 'ayse.kaya@universite.edu.tr', '2021001002', 'ogrenci'),
('Mehmet', 'Demir', 'mehmet.demir@universite.edu.tr', '2021001003', 'ogrenci'),
('Fatma', 'Şahin', 'fatma.sahin@universite.edu.tr', 'OGR2024001', 'ogretmen');

-- Örnek etkinlikler
INSERT INTO akademik_etkinlikler (baslik, aciklama, etkinlik_turu, baslangic_tarihi, bitis_tarihi, konum) VALUES
('Yazılım Mimarisi Ara Sınav', 'Ara dönem sınavı', 'sınav', DATE_ADD(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 3 DAY), 'A-205'),
('Proje Teslimi', 'Final projesi teslim tarihi', 'ödev', DATE_ADD(NOW(), INTERVAL 7 DAY), DATE_ADD(NOW(), INTERVAL 7 DAY), 'Online'),
('Kariyer Günleri Etkinliği', 'Şirket temsilcileri ile tanışma', 'etkinlik', DATE_ADD(NOW(), INTERVAL 5 DAY), DATE_ADD(NOW(), INTERVAL 5 DAY), 'Konferans Salonu'),
('Veritabanı Final Sınavı', 'Dönem sonu sınavı', 'sınav', DATE_ADD(NOW(), INTERVAL 14 DAY), DATE_ADD(NOW(), INTERVAL 14 DAY), 'B-101');

