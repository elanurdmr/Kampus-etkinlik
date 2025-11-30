<?php include "db.php"; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öğretim Üyesi Randevu</title>

    <link rel="stylesheet" href="style.css">

    <style>
        .randevu-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }
        .randevu-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        select, input {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 15px;
            width: 100%;
            margin-bottom: 15px;
        }
        .submit-btn {
            width: 100%;
            background: #d32f2f;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 5px;
            font-size: 17px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background: #b71c1c;
        }
        .success-message, .error-message {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
            display: none;
        }
        .success-message { background: #e8f5e9; color: #2e7d32; }
        .error-message { background: #ffebee; color: #c62828; }
    </style>
</head>

<body>

<?php include "header.php"; ?>

<div class="randevu-container">

    <h2>📅 Öğretim Üyesi Randevu Sistemi</h2>
    <p style="color:#666;margin-bottom:20px;">
        Öğretim üyesi seç, tarih ve saat belirle, randevunu oluştur.
    </p>

    <div class="randevu-card">

        <div id="success" class="success-message"></div>
        <div id="error" class="error-message"></div>

        <label>Öğretim Üyesi Seç:</label>
        <select id="ogretmen">
            <option value="">Liste backend'den gelecek</option>
        </select>

        <label>Tarih Seç:</label>
        <input type="date" id="tarih">

        <label>Saat Seç:</label>
        <input type="time" id="saat">

        <button class="submit-btn" onclick="randevuOlustur()">📌 Randevu Al</button>

    </div>
</div>

<?php include "footer.php"; ?>

<script src="script.js"></script>

<script>
function randevuOlustur() {
    let ogretmen = document.getElementById("ogretmen").value;
    let tarih = document.getElementById("tarih").value;
    let saat = document.getElementById("saat").value;

    let success = document.getElementById("success");
    let error = document.getElementById("error");

    success.style.display = "none";
    error.style.display = "none";

    if (!ogretmen || !tarih || !saat) {
        error.style.display = "block";
        error.innerHTML = "❌ Lütfen tüm alanları doldurun!";
        return;
    }

    success.style.display = "block";
    success.innerHTML = "✅ Randevunuz oluşturuldu!";
}
</script>

</body>
</html>
