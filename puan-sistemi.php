<?php include "db.php"; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Puan & Rozet Sistemi</title>
    <link rel="stylesheet" href="style.css">

    <style>
        .puan-container{
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
        .puan-display{
            font-size:40px;
            font-weight:bold;
            text-align:center;
            margin-bottom:25px;
        }
        .rozet-list div{
            background:#f5f5f5;
            padding:15px;
            border-radius:8px;
            margin-bottom:10px;
        }
    </style>
</head>

<body>

<?php include "header.php"; ?>

<div class="puan-container">
    <h2>🏆 Puan & Rozet Sistemi</h2>

    <div class="card">
        <div class="puan-display">
            Toplam Puanın: <span id="puan">0</span>
        </div>

        <h3>🎖 Kazanılan Rozetler</h3>
        <div class="rozet-list" id="rozetler">
            Henüz rozet kazanılmadı.
        </div>
    </div>
</div>

<?php include "footer.php"; ?>

<script src="script.js"></script>

<script>
let puan = 120; 
let rozetler = ["Başlangıç Etkinlikçisi","Haftalık Katılımcı"];

document.getElementById("puan").innerText = puan;

let box = document.getElementById("rozetler");
box.innerHTML = "";
rozetler.forEach(r=>{
    box.innerHTML += `<div>🏅 ${r}</div>`;
});
</script>

</body>
</html>
