<?php include "db.php"; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kulüp Öneri Sistemi</title>
    <link rel="stylesheet" href="style.css">

    <style>
        .oneri-container{
            max-width:900px;
            margin:40px auto;
            padding:20px;
        }
        .card{
            background:white;
            padding:30px;
            border-radius:10px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }
        select,button{
            padding:12px;
            width:100%;
            border-radius:6px;
            border:1px solid #ccc;
            font-size:15px;
            margin-bottom:15px;
        }
        .btn{
            background:#c62828;
            color:white;
            border:none;
            font-weight:bold;
            cursor:pointer;
        }
        .btn:hover{ background:#8e0000; }
        .result-box{
            padding:15px;
            margin-top:20px;
            border-radius:8px;
            background:#f5f5f5;
            display:none;
        }
    </style>
</head>

<body>

<?php include "header.php"; ?>

<div class="oneri-container">
    <h2>🤖 Kulüp Etkinlik Öneri Sistemi</h2>
    <p>İlgi alanlarını seç ve sana uygun etkinlikleri önerelim.</p>

    <div class="card">
        <label>İlgi Alanın:</label>
        <select id="ilgi">
            <option value="Yazılım">Yazılım</option>
            <option value="Müzik">Müzik</option>
            <option value="Spor">Spor</option>
            <option value="Psikoloji">Psikoloji</option>
        </select>

        <button class="btn" onclick="oneriGetir()">Önerileri Göster</button>

        <div id="sonuc" class="result-box"></div>
    </div>
</div>

<?php include "footer.php"; ?>

<script src="script.js"></script>

<script>
function oneriGetir(){
    let ilgi = document.getElementById("ilgi").value;
    let sonuc = document.getElementById("sonuc");

    sonuc.style.display = "block";
    sonuc.innerHTML = "🔍 '" + ilgi + "' için etkinlik önerileri backend’den gelecek.";
}
</script>

</body>
</html>
