<?php include 'conexao.php'; ?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nome = $conn->real_escape_string($_POST['nome']);
  $descricao = $conn->real_escape_string($_POST['descricao']);
  $preco = floatval($_POST['preco']);
  $categoria = $conn->real_escape_string($_POST['categoria']);
  $quantidade = intval($_POST['quantidade']);
  $imagem = $conn->real_escape_string($_POST['imagem']);

  $sql = "INSERT INTO produtos (nome, descricao, preco, categoria, quantidade, imagem)
          VALUES ('$nome', '$descricao', $preco, '$categoria', $quantidade, '$imagem')";

  if ($conn->query($sql) === TRUE) {
    header("Location: index.php?msg=add_success");
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
  <title>RD Games - Adicionar Produto</title>
  <link rel="stylesheet" href="estilo.css">
</head>
<body>
  <header>
    <h1>RD Games</h1>
    <p class="subtitle">Adicionar novo produto</p>
  </header>

  <main>
    <?php if (!empty($erro)): ?>
      <div class="alert error">Erro ao salvar: <?php echo htmlspecialchars($erro); ?></div>
    <?php endif; ?>

    <form method="post" class="form">
      <label>Nome:</label>
      <input type="text" name="nome" required>

      <label>Descrição:</label>
      <textarea name="descricao"></textarea>

      <label>Preço (R$):</label>
      <input type="number" step="0.01" name="preco" required>

      <label>Categoria:</label>
      <input type="text" name="categoria">

      <label>Quantidade:</label>
      <input type="number" name="quantidade" value="1">

      <label>URL da Imagem:</label>
      <input type="text" name="imagem" placeholder="https://...">

      <div class="actions">
        <button type="submit" class="btn primary">Salvar</button>
        <a href="index.php" class="btn">Voltar</a>
      </div>
    </form>
  </main>
</body>
</html>