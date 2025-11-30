<?php include "db.php"; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kütüphane Rezervasyon</title>

    <link rel="stylesheet" href="style.css">
    <style>
        .rez-container{
            max-width:850px;
            margin:40px auto;
            padding:20px;
        }
        .card{
            padding:25px;
            background:white;
            border-radius:10px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }
        select,input,button{
            padding:12px;
            width:100%;
            margin-bottom:15px;
            border-radius:6px;
            border:1px solid #ccc;
            font-size:15px;
        }
        button{
            background:#283593;
            color:white;
            font-weight:bold;
            cursor:pointer;
        }
        button:hover{ background:#1a237e; }
    </style>
</head>

<body>

<?php include "header.php"; ?>

<div class="rez-container">
    <h2>📚 Kütüphane Rezervasyon Sistemi</h2>

    <div class="card">

        <label>Alan Seç</label>
        <select id="alan">
            <option value="Sessiz Çalışma">Sessiz Çalışma Alanı</option>
            <option value="Toplu Çalışma">Toplu Çalışma Odası</option>
        </select>

        <label>Tarih Seç</label>
        <input type="date" id="tarih">

        <label>Saat Seç</label>
        <input type="time" id="saat">

        <button onclick="rezervasyon()">Rezervasyon Yap</button>

        <div id="sonuc" style="padding:10px;display:none;background:#e8f5e9;margin-top:15px;border-radius:6px;color:#2e7d32;"></div>
    </div>
</div>

<?php include "footer.php"; ?>
<script src="script.js"></script>

<script>
function rezervasyon(){
    let alan = document.getElementById("alan").value;
    let tarih = document.getElementById("tarih").value;
    let saat = document.getElementById("saat").value;

    if(!alan || !tarih || !saat){
        alert("Lütfen tüm alanları doldurun!");
        return;
    }

    document.getElementById("sonuc").style.display="block";
    document.getElementById("sonuc").innerHTML="✔ Rezervasyon oluşturuldu.";
}
</script>

</body>
</html>
