<?php
// Kulüp etkinlikleri için basit mock veri kaynağı
// Tarihler her zaman bugüne göre ileri tarihler olacak şekilde hesaplanır.

function getKulupEtkinlikleriMock(): array {
    $bugun = new DateTimeImmutable('today');

    return [
        [
            'kulup' => 'Yazılım Kulübü',
            'ad' => 'Kodlama Günü',
            'tarih' => $bugun->modify('+3 days')->format('d.m.Y'),
            'aciklama' => 'Web, mobil ve yapay zeka atölyeleri ile tam gün kodlama maratonu.',
            'konum' => 'Dudullu Kampüs B Blok Lab-201',
            'one_cikan' => true,
        ],
        [
            'kulup' => 'Yazılım Kulübü',
            'ad' => 'Git & GitHub Atölyesi',
            'tarih' => $bugun->modify('+7 days')->format('d.m.Y'),
            'aciklama' => 'Versiyon kontrolü, branching ve pull request pratikleri.',
            'konum' => 'Online / Teams',
            'one_cikan' => true,
        ],
        [
            'kulup' => 'Psikoloji Kulübü',
            'ad' => 'Empati ve İletişim Atölyesi',
            'tarih' => $bugun->modify('+10 days')->format('d.m.Y'),
            'aciklama' => 'Etkili iletişim, aktif dinleme ve empati egzersizleri.',
            'konum' => 'D Blok Konferans Salonu',
            'one_cikan' => true,
        ],
        [
            'kulup' => 'Fotoğrafçılık Kulübü',
            'ad' => 'Kampüs Kareleri Gezisi',
            'tarih' => $bugun->modify('+5 days')->format('d.m.Y'),
            'aciklama' => 'Kampüs genelinde fotoğraf turu ve kompozisyon eğitimi.',
            'konum' => 'Dudullu Kampüs - Ana Giriş',
            'one_cikan' => false,
        ],
        [
            'kulup' => 'Spor Kulübü',
            'ad' => 'Sabah Koşusu & Sağlıklı Yaşam',
            'tarih' => $bugun->modify('+1 days')->format('d.m.Y'),
            'aciklama' => 'Kampüs çevresinde koşu ve beslenme üzerine kısa seminer.',
            'konum' => 'Spor Merkezi Önü',
            'one_cikan' => false,
        ],
        [
            'kulup' => 'Müzik Kulübü',
            'ad' => 'Açık Sahne Gecesi',
            'tarih' => $bugun->modify('+14 days')->format('d.m.Y'),
            'aciklama' => 'Öğrenci gruplarının canlı performansları ile açık sahne etkinliği.',
            'konum' => 'Kantin Sahnesi',
            'one_cikan' => false,
        ],
    ];
}

