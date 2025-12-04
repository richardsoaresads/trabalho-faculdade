<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: admin_dashboard.php");
    exit;
}

// carrega dados do jogo
$stmt = $conn->prepare("SELECT * FROM jogos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$jogo = $res->fetch_assoc();

if (!$jogo) {
    header("Location: admin_dashboard.php");
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome        = trim($_POST['nome'] ?? '');
    $descricao   = trim($_POST['descricao'] ?? '');
    $preco_raw   = trim($_POST['preco'] ?? '');
    $estoque_raw = trim($_POST['estoque'] ?? '');
    $preco       = floatval(str_replace(',', '.', $preco_raw));
    $estoque     = (int)$estoque_raw;
    if ($estoque < 0) $estoque = 0;

    $filename = $jogo['imagem'];

    // tratar nova imagem
    if (!empty($_FILES['imagem']['name'])) {
        $allowed = ['jpg','jpeg','png','gif','webp'];
        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $erro = 'Tipo de imagem não permitido.';
        } elseif ($_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
            $erro = 'Erro no upload da imagem.';
        } else {
            $uploadDir = __DIR__ . '/assets/uploads';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $novoNome = uniqid('img_') . '.' . $ext;
            $move = move_uploaded_file($_FILES['imagem']['tmp_name'], $uploadDir . '/' . $novoNome);
            if (!$move) {
                $erro = 'Falha ao salvar imagem.';
            } else {
                // apagar a antiga se existir
                if (!empty($filename)) {
                    @unlink($uploadDir . '/' . $filename);
                }
                $filename = $novoNome;
            }
        }
    }

    if ($erro === '') {
        $stmt2 = $conn->prepare("UPDATE jogos SET nome = ?, descricao = ?, preco = ?, imagem = ?, estoque = ? WHERE id = ?");
        $stmt2->bind_param("ssdsii", $nome, $descricao, $preco, $filename, $estoque, $id);
        if ($stmt2->execute()) {
            $sucesso = 'Jogo atualizado com sucesso.';
            // atualiza dados na tela
            $jogo['nome'] = $nome;
            $jogo['descricao'] = $descricao;
            $jogo['preco'] = $preco;
            $jogo['imagem'] = $filename;
            $jogo['estoque'] = $estoque;
        } else {
            $erro = 'Erro ao atualizar: ' . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Editar Jogo - Admin</title>
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
.thumb-atual {
  max-width:150px;
  display:block;
  margin-bottom:10px;
}
</style>
</head>
<body>
<div class="wrap">
  <div class="topo">
    <h1>Editar Jogo</h1>
    <div>
      <a class="btn small" href="admin_dashboard.php">Voltar</a>
      <a class="btn small" href="admin_logout.php">Sair</a>
    </div>
  </div>

  <?php if ($erro): ?><div class="alert erro"><?=htmlspecialchars($erro)?></div><?php endif; ?>
  <?php if ($sucesso): ?><div class="alert ok"><?=htmlspecialchars($sucesso)?></div><?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <label>Nome</label>
    <input name="nome" value="<?=htmlspecialchars($jogo['nome'])?>" required>

    <label>Descrição</label>
    <textarea name="descricao"><?=htmlspecialchars($jogo['descricao'])?></textarea>

    <label>Preço (ex: 59.90)</label>
    <input name="preco" value="<?=htmlspecialchars(number_format($jogo['preco'], 2, ',', '.'))?>" required>

    <label>Estoque</label>
    <input type="number" name="estoque" min="0" value="<?= (int)$jogo['estoque'] ?>" required>

    <label>Imagem atual</label>
    <?php if (!empty($jogo['imagem'])): ?>
      <img class="thumb-atual" src="assets/uploads/<?=htmlspecialchars($jogo['imagem'])?>" alt="">
    <?php else: ?>
      <p><small>Sem imagem.</small></p>
    <?php endif; ?>

    <label>Nova imagem (opcional)</label>
    <input type="file" name="imagem" accept="image/*">

    <button class="btn">Salvar alterações</button>
  </form>
</div>
</body>
</html>
