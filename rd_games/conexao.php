<?php
$servername = "sql100.infinityfree.com"; // o host que aparece no painel
$username   = "if0_40500140";           // seu MySQL username
$password   = "19042603arra";      // a senha que você criou
$dbname     = "if0_40500140_produtos_db";   // nome completo do banco

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
