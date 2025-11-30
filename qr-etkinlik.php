<?php include "db.php"; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Etkinlik Onayı</title>

    <link rel="stylesheet" href="style.css">
    <style>
        .qr-container{
            max-width:850px;
            margin:40px auto;
            padding:20px;
            text-align:center;
        }
        .card{
            padding:25px;
            background:white;
            border-radius:10px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }
        #qrPreview{
            margin-top:20px;
            max-width:250px;
        }
        input{
            padding:12px;
            width:100%;
            margin-top:10px;
            border-radius:6px;
            border:1px solid #ccc;
        }
    </style>
</head>

<body>

<?php include "header.php"; ?>

<div class="qr-container">
    <h2>📲 QR ile Etkinlik Onayı</h2>
    <p>Etkinlik girişinde gösterilmek üzere QR oluştur.</p>

    <div class="card">
        <label>Etkinlik Adı</label>
        <input type="text" id="etkinlik" placeholder="Örn: Yazılım Zirvesi">

        <button class="submit-btn" style="margin-top:15px;" onclick="qrOlustur()">QR Oluştur</button>

        <img id="qrPreview" style="display:none;">
    </div>
</div>

<?php include "footer.php"; ?>
<script src="script.js"></script>

<script>
function qrOlustur(){
    let etkinlik = document.getElementById("etkinlik").value;

    if(!etkinlik){
        alert("Etkinlik adı girin!");
        return;
    }

    let qrURL = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data="+encodeURIComponent(etkinlik);

    let img = document.getElementById("qrPreview");
    img.src = qrURL;
    img.style.display = "block";
}
</script>

</body>
</html>
