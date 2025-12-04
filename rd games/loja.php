<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'conexao.php';

// Consulta jogos
$sql = "SELECT * FROM jogos ORDER BY criado_em DESC";
$res = $conn->query($sql);

// Mensagem (compra realizada, erro, etc.)
$mensagem = '';
$tipo_msg = '';
if (isset($_SESSION['last_msg'])) {
    $mensagem = $_SESSION['last_msg']['texto'] ?? '';
    $tipo_msg = $_SESSION['last_msg']['tipo'] ?? '';
    unset($_SESSION['last_msg']);
}

// Info de login
$cliente_logado = isset($_SESSION['cliente_id']);
$nome_cliente   = $cliente_logado ? ($_SESSION['cliente_nome'] ?? '') : null;
$admin_logado   = isset($_SESSION['admin_id']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>RD Jogos - Loja</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="wrap">

  <!-- CABEÇALHO -->
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

  <!-- CONTEÚDO PRINCIPAL -->
  <main class="main-content">
    <?php if ($mensagem): ?>
      <div class="alert <?= $tipo_msg === 'erro' ? 'erro' : 'ok' ?>">
        <?=htmlspecialchars($mensagem)?>
      </div>
    <?php endif; ?>

    <h2>Jogos Disponíveis</h2>

    <div class="cards">
      <?php while ($j = $res->fetch_assoc()): 
        $estoque = isset($j['estoque']) ? (int)$j['estoque'] : 0;
      ?>
        <div class="card">
          <?php if (!empty($j['imagem'])): ?>
            <img src="assets/uploads/<?=htmlspecialchars($j['imagem'])?>" class="thumb" alt="Capa do jogo">
          <?php endif; ?>

          <h3><?=htmlspecialchars($j['nome'])?></h3>
          <p><?=nl2br(htmlspecialchars($j['descricao']))?></p>
          <div class="preco">R$ <?=number_format($j['preco'], 2, ',', '.')?></div>
          <small>Estoque: <?=$estoque?></small>

          <?php if ($cliente_logado && $estoque > 0): ?>
            <form method="post" action="comprar.php" style="margin-top:8px;">
              <input type="hidden" name="id_jogo" value="<?=$j['id']?>">
              <button class="btn">Comprar</button>
            </form>
          <?php elseif (!$cliente_logado): ?>
            <p style="margin-top:8px;"><small>Faça <a href="login.php">login</a> para comprar</small></p>
          <?php else: ?>
            <p style="margin-top:8px;"><small>Estoque esgotado.</small></p>
          <?php endif; ?>
        </div>
      <?php endwhile; ?>
    </div>
  </main>

  <!-- RODAPÉ -->
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

<!-- SCRIPT DO DARK MODE -->
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
