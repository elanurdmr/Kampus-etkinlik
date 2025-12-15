<?php
// Kulüp etkinlikleri için basit mock veri kaynağı
// Tarihler her zaman bugüne göre ileri tarihler olacak şekilde hesaplanır.

function getKulupEtkinlikleriMock(): array {
    $bugun = new DateTimeImmutable('today');

    return [
        [
            'kulup' => 'Yazılım Kulübü',
            'kulup_en' => 'Software Club',
            'ad' => 'Kodlama Günü',
            'ad_en' => 'Coding Day',
            'tarih' => $bugun->modify('+3 days')->format('d.m.Y'),
            'aciklama' => 'Web, mobil ve yapay zeka atölyeleri ile tam gün kodlama maratonu.',
            'aciklama_en' => 'Full‑day coding marathon with web, mobile and AI workshops.',
            'konum' => 'Dudullu Kampüs B Blok Lab-201',
            'konum_en' => 'Dudullu Campus B Block Lab‑201',
            'one_cikan' => true,
        ],
        [
            'kulup' => 'Yazılım Kulübü',
            'kulup_en' => 'Software Club',
            'ad' => 'Git & GitHub Atölyesi',
            'ad_en' => 'Git & GitHub Workshop',
            'tarih' => $bugun->modify('+7 days')->format('d.m.Y'),
            'aciklama' => 'Versiyon kontrolü, branching ve pull request pratikleri.',
            'aciklama_en' => 'Version control, branching and pull request practice.',
            'konum' => 'Online / Teams',
            'konum_en' => 'Online / Teams',
            'one_cikan' => true,
        ],
        [
            'kulup' => 'Psikoloji Kulübü',
            'kulup_en' => 'Psychology Club',
            'ad' => 'Empati ve İletişim Atölyesi',
            'ad_en' => 'Empathy & Communication Workshop',
            'tarih' => $bugun->modify('+10 days')->format('d.m.Y'),
            'aciklama' => 'Etkili iletişim, aktif dinleme ve empati egzersizleri.',
            'aciklama_en' => 'Effective communication, active listening and empathy exercises.',
            'konum' => 'D Blok Konferans Salonu',
            'konum_en' => 'Block D Conference Hall',
            'one_cikan' => true,
        ],
        [
            'kulup' => 'Fotoğrafçılık Kulübü',
            'kulup_en' => 'Photography Club',
            'ad' => 'Kampüs Kareleri Gezisi',
            'ad_en' => 'Campus Frames Trip',
            'tarih' => $bugun->modify('+5 days')->format('d.m.Y'),
            'aciklama' => 'Kampüs genelinde fotoğraf turu ve kompozisyon eğitimi.',
            'aciklama_en' => 'Photo walk around campus and composition training.',
            'konum' => 'Dudullu Kampüs - Ana Giriş',
            'konum_en' => 'Dudullu Campus – Main Entrance',
            'one_cikan' => false,
        ],
        [
            'kulup' => 'Spor Kulübü',
            'kulup_en' => 'Sports Club',
            'ad' => 'Sabah Koşusu & Sağlıklı Yaşam',
            'ad_en' => 'Morning Run & Healthy Life',
            'tarih' => $bugun->modify('+1 days')->format('d.m.Y'),
            'aciklama' => 'Kampüs çevresinde koşu ve beslenme üzerine kısa seminer.',
            'aciklama_en' => 'Run around campus and a short seminar on nutrition.',
            'konum' => 'Spor Merkezi Önü',
            'konum_en' => 'In front of Sports Center',
            'one_cikan' => false,
        ],
        [
            'kulup' => 'Müzik Kulübü',
            'kulup_en' => 'Music Club',
            'ad' => 'Açık Sahne Gecesi',
            'ad_en' => 'Open Mic Night',
            'tarih' => $bugun->modify('+14 days')->format('d.m.Y'),
            'aciklama' => 'Öğrenci gruplarının canlı performansları ile açık sahne etkinliği.',
            'aciklama_en' => 'Open stage event with live performances by student bands.',
            'konum' => 'Kantin Sahnesi',
            'konum_en' => 'Cafeteria Stage',
            'one_cikan' => false,
        ],
    ];
}

