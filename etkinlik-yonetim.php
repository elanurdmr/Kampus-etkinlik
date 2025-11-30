<?php include "db.php"; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Etkinlik Yönetimi | Kampüs Sistemi</title>

  <!-- Style -->
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "header.php"; ?>


<div class="yonetim-container">
  <h2>➕ Yeni Etkinlik Ekle</h2>
  <p style="color: #666; margin-bottom: 20px;">Backend API'ye yeni etkinlik ekleyin: 
    <code>http://localhost:8010/api/calendar/etkinlik</code>
  </p>

  <div id="api-status" class="api-status">⏳ API durumu kontrol ediliyor...</div>

  <div id="success-message" class="success-message"></div>
  <div id="error-message" class="error-message"></div>

  <div class="form-card">
    <form id="etkinlik-form">
      <div class="form-group">
        <label for="baslik">📝 Etkinlik Başlığı *</label>
        <input type="text" id="baslik" name="baslik" required placeholder="Örn: Yazılım Geliştirme Workshop">
      </div>

      <div class="form-group">
        <label for="etkinlik_turu">🏷️ Etkinlik Türü *</label>
        <select id="etkinlik_turu" name="etkinlik_turu" required>
          <option value="">Seçiniz...</option>
          <option value="sinav">Sınav</option>
          <option value="odev">Ödev</option>
          <option value="etkinlik">Etkinlik</option>
          <option value="seminer">Seminer</option>
          <option value="proje">Proje</option>
        </select>
      </div>

      <div class="form-group">
        <label for="aciklama">📄 Açıklama</label>
        <textarea id="aciklama" name="aciklama"></textarea>
      </div>

      <div class="form-group">
        <label for="baslangic_tarihi">📅 Başlangıç Tarihi *</label>
        <input type="datetime-local" id="baslangic_tarihi" name="baslangic_tarihi" required>
      </div>

      <div class="form-group">
        <label for="bitis_tarihi">📅 Bitiş Tarihi</label>
        <input type="datetime-local" id="bitis_tarihi" name="bitis_tarihi">
      </div>

      <div class="form-group">
        <label for="konum">📍 Konum</label>
        <input type="text" id="konum" name="konum">
      </div>

      <button type="submit" class="submit-btn">Etkinlik Ekle</button>
    </form>
  </div>

  <div style="margin-top: 20px; text-align: center;">
    <a href="akademik-takvim.php" class="geri-link">← Akademik Takvime Dön</a>
  </div>
</div>


<?php include "footer.php"; ?>

<script src="script.js"></script>

<script>
const API_URL = "http://localhost:8010/api/calendar";

document.addEventListener("DOMContentLoaded", () => {
  checkAPIStatus();
  setupForm();
});

async function checkAPIStatus() {
  const statusDiv = document.getElementById("api-status");

  try {
    const res = await fetch("http://localhost:8010/");
    if (res.ok) {
      statusDiv.className = "api-status api-online";
      statusDiv.innerHTML = "✅ Backend API çalışıyor";
    } else throw new Error();
  } catch (e) {
    statusDiv.className = "api-status api-offline";
    statusDiv.innerHTML = "❌ Backend API çalışmıyor";
  }
}

function setupForm() {
  const form = document.getElementById("etkinlik-form");

  form.addEventListener("submit", async e => {
    e.preventDefault();

    const data = {
      baslik: baslik.value,
      etkinlik_turu: etkinlik_turu.value,
      aciklama: aciklama.value,
      baslangic_tarihi: baslangic_tarihi.value,
      bitis_tarihi: bitis_tarihi.value,
      konum: konum.value
    };

    await submitEtkinlik(data);
  });
}

async function submitEtkinlik(data) {
  const success = document.getElementById("success-message");
  const error = document.getElementById("error-message");

  success.style.display = "none";
  error.style.display = "none";

  try {
    const res = await fetch(API_URL + "/etkinlik", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data)
    });

    const result = await res.json();

    if (res.ok) {
      success.style.display = "block";
      success.innerHTML = "🎉 Etkinlik başarıyla eklendi!";
    } else {
      throw new Error(result.detail);
    }
  } catch (err) {
    error.style.display = "block";
    error.innerHTML = "❌ Hata: " + err.message;
  }
}
</script>

</body>
</html>
