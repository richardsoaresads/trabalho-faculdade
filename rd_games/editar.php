<?php
include 'conexao.php';

$id = intval($_GET['id'] ?? 0);
$sql = "SELECT * FROM produtos WHERE id=$id";
$result = $conn->query($sql);
$produto = $result->fetch_assoc();

if (!$produto) {
  header("Location: index.php");
  exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nome = $conn->real_escape_string($_POST['nome']);
  $descricao = $conn->real_escape_string($_POST['descricao']);
  $preco = floatval($_POST['preco']);
  $categoria = $conn->real_escape_string($_POST['categoria']);
  $quantidade = intval($_POST['quantidade']);
  $imagem = $conn->real_escape_string($_POST['imagem']);

  $sql = "UPDATE produtos SET nome='$nome', descricao='$descricao', preco=$preco, categoria='$categoria', quantidade=$quantidade, imagem='$imagem' WHERE id=$id";

  if ($conn->query($sql) === TRUE) {
    header("Location: index.php?msg=edit_success");
    exit;
  } else {
    $erro = $conn->error;
  }
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>RD Games - Editar Produto</title>
  <link rel="stylesheet" href="estilo.css">
</head>
<body>
  <header>
    <h1>RD Games</h1>
    <p class="subtitle">Editar produto</p>
  </header>

  <main>
    <?php if (!empty($erro)): ?>
      <div class="alert error">Erro ao salvar: <?php echo htmlspecialchars($erro); ?></div>
    <?php endif; ?>

    <form method="post" class="form">
      <label>Nome:</label>
      <input type="text" name="nome" value="<?php echo htmlspecialchars($produto['nome']); ?>" required>

      <label>Descrição:</label>
      <textarea name="descricao"><?php echo htmlspecialchars($produto['descricao']); ?></textarea>

      <label>Preço (R$):</label>
      <input type="number" step="0.01" name="preco" value="<?php echo htmlspecialchars($produto['preco']); ?>" required>

      <label>Categoria:</label>
      <input type="text" name="categoria" value="<?php echo htmlspecialchars($produto['categoria']); ?>">

      <label>Quantidade:</label>
      <input type="number" name="quantidade" value="<?php echo htmlspecialchars($produto['quantidade']); ?>">

      <label>URL da Imagem:</label>
      <input type="text" name="imagem" value="<?php echo htmlspecialchars($produto['imagem']); ?>">

      <div class="actions">
        <button type="submit" class="btn primary">Salvar Alterações</button>
        <a href="index.php" class="btn">Voltar</a>
      </div>
    </form>
  </main>
</body>
</html>