<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'loja_jogos';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8");
