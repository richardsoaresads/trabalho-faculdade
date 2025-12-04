<?php
session_start();
require 'conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'] ?? 'cliente'; // cliente ou admin

    if ($tipo === 'cliente') {
        $email = trim($_POST['email']);
        $senha = $_POST['senha'];

        if (!$email || !$senha) {
            $erro = "Preencha todos os campos.";
        } else {
            $stmt = $conn->prepare("SELECT id, nome, senha FROM clientes WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                if (password_verify($senha, $row['senha'])) {
                    $_SESSION['cliente_id']   = $row['id'];
                    $_SESSION['cliente_nome'] = $row['nome'];
                    header("Location: loja.php");
                    exit;
                } else {
                    $erro = "Senha incorreta.";
                }
            } else {
                $erro = "Cliente não encontrado.";
            }
        }

    } else { // ---- LOGIN DE ADMIN FIXO AQUI ----
        $usuario = trim($_POST['email']); // campo de usuário
        $senha   = $_POST['senha'];

        // admin fixo: admin / admin123
        if ($usuario === 'admin' && $senha === 'admin123') {
            $_SESSION['admin_id']      = 1;
            $_SESSION['admin_usuario'] = 'admin';
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $erro = "Usuário ou senha de administrador inválidos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Login</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
select{width:100%;padding:8px;border-radius:6px;border:1px solid #333;background:#0b0b0d;color:#eee;margin-bottom:8px}
</style>
</head>
<body>
<div class="wrap">
  <h2>Entrar</h2>
  <?php if ($erro): ?><div class="erro"><?=htmlspecialchars($erro)?></div><?php endif; ?>
  <form method="post">
    <label>Tipo de acesso</label>
    <select name="tipo">
      <option value="cliente">Cliente</option>
      <option value="admin">Administrador</option>
    </select>

    <label>Email / Usuário</label>
    <input name="email" type="text" required>

    <label>Senha</label>
    <input name="senha" type="password" required>

    <button class="btn">Entrar</button>
  </form>
  <p><a href="register.php">Ainda não tem conta? Cadastrar</a></p>
</div>
</body>
</html>
