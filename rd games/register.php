<?php
session_start();
require 'conexao.php';

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if (!$nome || !$email || !$senha) {
        $erro = "Preencha todos os campos.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM clientes WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $erro = "E-mail já cadastrado.";
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $ins = $conn->prepare("INSERT INTO clientes (nome, email, senha) VALUES (?, ?, ?)");
            $ins->bind_param("sss", $nome, $email, $hash);
            if ($ins->execute()) {
                $_SESSION['cliente_id'] = $ins->insert_id;
                $_SESSION['cliente_nome'] = $nome;
                header("Location: loja.php");
                exit;
            } else {
                $erro = "Erro ao cadastrar.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Cadastrar - Loja</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="wrap">
  <h2>Cadastro</h2>
  <?php if ($erro): ?><div class="erro"><?=htmlspecialchars($erro)?></div><?php endif; ?>
  <form method="post">
    <label>Nome</label>
    <input name="nome" required>
    <label>Email</label>
    <input name="email" type="email" required>
    <label>Senha</label>
    <input name="senha" type="password" required>
    <button class="btn">Cadastrar</button>
  </form>
  <p><a href="login.php">Já tem conta? Entrar</a></p>
</div>
</body>
</html>
