<?php
// Mostrar erros (ajuda a descobrir o motivo do HTTP 500)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// inclui a conexão com o banco
require_once 'conexao.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>RD Games - Gerenciador de Produtos</title>
  <link rel="stylesheet" href="estilo.css">
</head>
<body>
  <header>
    <div class="header-inner">
      <div class="title-wrap">
        <h1>RD Games</h1>
        <p class="subtitle">Painel de gerenciamento de produtos</p>
      </div>

      <div class="controls">
        <a href="adicionar.php" class="btn primary">+ Adicionar Produto</a>
        <a href="grafico.php" class="btn">Relatórios e Gráficos</a>

        <div class="darkmode-toggle" role="switch" aria-checked="false" tabindex="0">
          <svg class="icon-moon" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
            <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" fill="currentColor"/>
          </svg>
          <span id="dm-label">Dark</span>
          <input type="checkbox" id="toggle-dark" hidden>
        </div>
      </div>
    </div>
  </header>

  <main>
    <div class="table-wrap">
      <table aria-label="Tabela de produtos cadastrados na RD Games">
        <thead>
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
        </thead>
        <tbody>
          <?php
            $sql = "SELECT * FROM produtos ORDER BY id DESC";
            $result = $conn->query($sql);

            if (!$result) {
              echo "<tr><td colspan='8'>Erro na consulta: " . htmlspecialchars($conn->error) . "</td></tr>";
            } elseif ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>".$row['id']."</td>";
                echo "<td>".htmlspecialchars($row['nome'])."</td>";
                echo "<td>".htmlspecialchars($row['descricao'])."</td>";
                echo "<td>R$ ".number_format($row['preco'], 2, ',', '.')."</td>";
                echo "<td>".htmlspecialchars($row['categoria'])."</td>";
                echo "<td>".$row['quantidade']."</td>";
                echo "<td>".( !empty($row['imagem']) ? "<img src='".htmlspecialchars($row['imagem'])."' width='60'>" : "-" ) ."</td>";
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
        </tbody>
      </table>
    </div>
  </main>

  <footer>
    <div class="footer-inner">
      <p>Projeto de Extensão - RD Games | Desenvolvido por Richard Soares Cardoso</p>
      <div class="socials">
        <a href="https://www.instagram.com/ricks.2w" target="_blank" rel="noopener" aria-label="Instagram">
          Instagram
        </a>
      </div>
    </div>
  </footer>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const toggleWrap = document.querySelector('.darkmode-toggle');
    const dmLabel = document.getElementById('dm-label');

    function applyMode(mode) {
      if (mode === 'light') {
        document.documentElement.classList.add('light');
        dmLabel.textContent = 'Light';
        toggleWrap.setAttribute('aria-checked','false');
      } else {
        document.documentElement.classList.remove('light');
        dmLabel.textContent = 'Dark';
        toggleWrap.setAttribute('aria-checked','true');
      }
    }

    const savedTheme = localStorage.getItem('rdgames_theme') || 'dark';
    applyMode(savedTheme);

    toggleWrap.addEventListener('click', () => {
      const cur = localStorage.getItem('rdgames_theme') || 'dark';
      const next = cur === 'dark' ? 'light' : 'dark';
      localStorage.setItem('rdgames_theme', next);
      applyMode(next);
    });
  });
  </script>
</body>
</html>
