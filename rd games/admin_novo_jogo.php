<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$erro = '';
$sucesso = '';
$nome = '';
$descricao = '';
$preco_raw = '';
$estoque_raw = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome        = trim($_POST['nome'] ?? '');
    $descricao   = trim($_POST['descricao'] ?? '');
    $preco_raw   = trim($_POST['preco'] ?? '');
    $estoque_raw = trim($_POST['estoque'] ?? '');

    if ($nome === '' || $preco_raw === '' || $estoque_raw === '') {
        $erro = 'Preencha nome, preço e estoque.';
    } else {
        $preco   = floatval(str_replace(',', '.', $preco_raw));
        $estoque = (int)$estoque_raw;

        if ($estoque < 0) $estoque = 0;

        // trata upload (opcional)
        $filename = null;
        if (!empty($_FILES['imagem']['name'])) {
            $allowed = ['jpg','jpeg','png','gif','webp'];
            $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                $erro = 'Tipo de imagem não permitido. Use jpg, png, gif ou webp.';
            } elseif ($_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
                $erro = 'Erro no upload da imagem.';
            } else {
                $uploadDir = __DIR__ . '/assets/uploads';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                $filename = uniqid('img_') . '.' . $ext;
                $move = move_uploaded_file($_FILES['imagem']['tmp_name'], $uploadDir . '/' . $filename);
                if (!$move) {
                    $erro = 'Falha ao salvar imagem no servidor.';
                }
            }
        }

        if ($erro === '') {
            $stmt = $conn->prepare("INSERT INTO jogos (nome, descricao, preco, imagem, estoque) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdsi", $nome, $descricao, $preco, $filename, $estoque);
            if ($stmt->execute()) {
                $sucesso = 'Jogo criado com sucesso.';
                $nome = $descricao = $preco_raw = $estoque_raw = '';
            } else {
                $erro = 'Erro ao inserir no banco: ' . $conn->error;
                if ($filename) {
                    @unlink($uploadDir . '/' . $filename);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Novo Jogo - Admin</title>
<link rel="stylesheet" href="assets/css/admin.css">
<style>
body {
  background:#0b0b0d;
  color:#eee;
  font-family: Arial, sans-serif;
  margin:0;
}
.wrap {
  max-width: 700px;
  margin:20px auto;
  background:#15151a;
  padding:20px;
  border-radius:10px;
}
.topo {
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:20px;
}
input, textarea {
  width:100%;
  padding:8px;
  margin-bottom:10px;
  border-radius:6px;
  border:1px solid #333;
  background:#0b0b0d;
  color:#eee;
}
textarea { min-height:80px; resize:vertical; }
.btn {
  display:inline-block;
  padding:8px 14px;
  border-radius:6px;
  background:#007bff;
  color:#fff;
  text-decoration:none;
  border:none;
  cursor:pointer;
}
.btn.small { padding:4px 10px; font-size:12px; }
.alert {
  padding:10px 15px;
  border-radius:6px;
  margin-bottom:10px;
}
.alert.ok {
  background:#d4edda;
  color:#155724;
}
.alert.erro {
  background:#f8d7da;
  color:#721c24;
}
</style>
</head>
<body>
<div class="wrap">
  <div class="topo">
    <h1>Novo Jogo</h1>
    <div>
      <a class="btn small" href="admin_dashboard.php">Voltar</a>
      <a class="btn small" href="admin_logout.php">Sair</a>
    </div>
  </div>

  <?php if ($erro): ?><div class="alert erro"><?=htmlspecialchars($erro)?></div><?php endif; ?>
  <?php if ($sucesso): ?><div class="alert ok"><?=htmlspecialchars($sucesso)?></div><?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <label>Nome</label>
    <input name="nome" value="<?=htmlspecialchars($nome)?>" required>

    <label>Descrição</label>
    <textarea name="descricao"><?=htmlspecialchars($descricao)?></textarea>

    <label>Preço (ex: 59.90)</label>
    <input name="preco" value="<?=htmlspecialchars($preco_raw)?>" required>

    <label>Estoque inicial</label>
    <input type="number" name="estoque" min="0" value="<?=htmlspecialchars($estoque_raw)?>" required>

    <label>Imagem (opcional)</label>
    <input type="file" name="imagem" accept="image/*">

    <button class="btn">Criar jogo</button>
  </form>
</div>
</body>
</html>
