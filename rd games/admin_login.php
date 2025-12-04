<?php
session_start();
if (isset($_SESSION['cliente_id'])) {
    header("Location: loja.php");
    exit;
}
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Loja de Jogos</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="wrap center">
  <h1>Loja de Jogos</h1>
  <div class="links">
    <a href="register.php" class="btn">Cadastrar</a>
    <a href="login.php" class="btn">Entrar</a>
    <a href="admin_login.php" class="btn">Admin</a>
  </div>
</div>
</body>
</html>
