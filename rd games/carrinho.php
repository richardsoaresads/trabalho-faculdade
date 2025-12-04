<?php
session_start();
require 'conexao.php';

// só cliente logado
if (!isset($_SESSION['cliente_id'])) {
    header("Location: login.php");
    exit;
}

$cliente_id   = (int)$_SESSION['cliente_id'];
$nome_cliente = $_SESSION['cliente_nome'] ?? '';

$admin_logado   = isset($_SESSION['admin_id']);
$cliente_logado = true;

// Busca vendas desse cliente
$sql = "SELECT 
            v.id,
            v.valor,
            v.data_venda,
            j.nome AS nome_jogo
        FROM vendas v
        JOIN jogos j ON j.id = v.id_jogo
        WHERE v.id_cliente = ?
        ORDER BY v.data_venda DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$result = $stmt->get_result();

$total_gasto = 0;
$compras = [];
while ($row = $result->fetch_assoc()) {
    $compras[] = $row;
    $total_gasto += (float)$row['valor'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>RD Jogos - Carrinho / Minhas Compras</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="wrap">

  <!-- Cabeçalho igual da loja -->
  <header class="header">
    <div class="header-inner">
      <div class="logo-title">RD Jogos</div>
      <div class="nav-links">
        <a href="loja.php">Loja</a>

        <?php if ($cliente_logado): ?>
          <a href="carrinho.php">Carrinho</a>
        <?php endif; ?>

        <?php if ($admin_logado): ?>
          <a href="admin_dashboard.php">Painel Admin</a>
        <?php endif; ?>

        <?php if ($cliente_logado): ?>
          <span>Olá, <?=htmlspecialchars($nome_cliente)?></span>
          <a href="logout.php">Sair</a>
        <?php else: ?>
          <a href="login.php">Login</a>
          <a href="register.php">Cadastro</a>
        <?php endif; ?>

        <button type="button" id="btn-toggle-theme" class="btn-toggle-theme">Dark/Light</button>
      </div>
    </div>
  </header>

  <main class="main-content">
    <h2>Minhas Compras</h2>

    <div class="table-box">
      <table>
        <thead>
          <tr>
            <th>Jogo</th>
            <th>Valor</th>
            <th>Data da Compra</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($compras) > 0): ?>
            <?php foreach ($compras as $row): ?>
              <tr>
                <td><?=htmlspecialchars($row['nome_jogo'])?></td>
                <td>R$ <?=number_format($row['valor'], 2, ',', '.')?></td>
                <td><?=date('d/m/Y H:i', strtotime($row['data_venda']))?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="3">Você ainda não fez nenhuma compra.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <p style="margin-top:15px;font-weight:bold;">
      Total gasto: R$ <?=number_format($total_gasto, 2, ',', '.')?>
    </p>
  </main>

  <footer class="footer">
    <div class="footer-inner">
      <span>© <?=date('Y')?> - RD Jogos</span>
      <div>
        <span>Siga o criador:</span>
        <a href="https://www.instagram.com/SEU_INSTAGRAM" target="_blank">Instagram</a>
        <a href="https://www.linkedin.com/in/SEU_LINKEDIN" target="_blank">LinkedIn</a>
        <a href="https://github.com/SEU_GITHUB" target="_blank">GitHub</a>
      </div>
    </div>
  </footer>

</div>

<script>
(function() {
  const savedTheme = localStorage.getItem('rdjogos_theme');
  if (savedTheme) {
    document.body.setAttribute('data-theme', savedTheme);
  } else {
    document.body.setAttribute('data-theme', 'dark');
  }

  const btn = document.getElementById('btn-toggle-theme');
  if (btn) {
    btn.addEventListener('click', function() {
      const current = document.body.getAttribute('data-theme') || 'dark';
      const next = current === 'dark' ? 'light' : 'dark';
      document.body.setAttribute('data-theme', next);
      localStorage.setItem('rdjogos_theme', next);
    });
  }
})();
</script>
</body>
</html>

