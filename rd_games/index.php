<?php include 'conexao.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>RD Games - Gerenciador de Produtos</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <h1>RD Games</h1>
    <p class="subtitle">Painel de gerenciamento de produtos</p>
  </header>

  <main>
    <a href="adicionar.php" class="btn primary">+ Adicionar Produto</a>

    <table>
      <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Descrição</th>
        <th>Preço</th>
        <th>Categoria</th>
        <th>Quantidade</th>
        <th>Imagem</th>
        <th>Ações</th>
      </tr>

      <?php
        $sql = "SELECT * FROM produtos ORDER BY id DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>".$row['id']."</td>";
            echo "<td>".htmlspecialchars($row['nome'])."</td>";
            echo "<td>".htmlspecialchars($row['descricao'])."</td>";
            echo "<td>R$ ".number_format($row['preco'], 2, ',', '.')."</td>";
            echo "<td>".htmlspecialchars($row['categoria'])."</td>";
            echo "<td>".$row['quantidade']."</td>";
            echo "<td>".( $row['imagem'] ? "<img src='".$row['imagem']."' width='60'>" : "-") ."</td>";
            echo "<td>
    <a href='editar.php?id=" . $row['id'] . "' class='btn_edit'>Editar</a>
    <a href='excluir.php?id=" . $row['id'] . "' class='btn_delete' onclick=\"return confirm('Excluir este produto?')\">Excluir</a>
</td>";

            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='8'>Nenhum produto encontrado.</td></tr>";
        }
      ?>
    </table>
  </main>

  <footer>
    <p>Projeto de Extensão - RD Games</p>
  </footer>
</body>
</html>