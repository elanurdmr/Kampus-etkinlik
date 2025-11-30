<?php


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "akademik_sistem";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Bağlantı hatası: " . $conn->connect_error);
}
?>
