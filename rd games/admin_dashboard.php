<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// pega jogos
$jogos = $conn->query("SELECT * FROM jogos ORDER BY criado_em DESC");

// pega resumo de vendas
$sqlResumo = "
    SELECT 
        COUNT(v.id) AS total_vendas,
        COALESCE(SUM(v.valor), 0) AS total_arrecadado
    FROM vendas v
";
$resResumo = $conn->query($sqlResumo);
$resumo = $resResumo->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Painel Admin - RD Jogos</title>
<link rel="stylesheet" href="assets/css/admin.css">

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<style>
/* Caso não tenha admin.css ainda, isso já dá um layout básico */
body {
  background:#0b0b0d;
  color:#eee;
  font-family: Arial, sans-serif;
  margin:0;
}
.wrap {
  max-width: 1100px;
  margin:20px auto;
  background:#15151a;
  padding:20px;
  border-radius:10px;
  box-shadow:0 0 10px rgba(0,0,0,0.5);
}
.topo {
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:20px;
}
.topo h1 {
  margin:0;
}
.btn {
  display:inline-block;
  padding:8px 14px;
  border-radius:6px;
  background:#007bff;
  color:#fff;
  text-decoration:none;
  border:none;
  cursor:pointer;
  font-size:14px;
}
.btn.small {
  padding:4px 10px;
  font-size:12px;
}
.btn.danger {
  background:#dc3545;
}
.btn + .btn {
  margin-left:6px;
}
.acoes {
  margin-bottom:20px;
}
.lista {
  width:100%;
  border-collapse:collapse;
  margin-top:10px;
}
.lista th, .lista td {
  border:1px solid #333;
  padding:8px;
  text-align:left;
}
.lista th {
  background:#202028;
}
.lista tr:nth-child(even) {
  background:#181820;
}
</style>

</head>
<body>
<div class="wrap">
  <div class="topo">
    <h1>Painel Administrativo</h1>
    <div>
      <span>Admin: <?=htmlspecialchars($_SESSION['admin_usuario'] ?? 'admin')?></span>
      <a class="btn small" href="admin_logout.php">Sair</a>
    </div>
  </div>

  <div class="acoes">
    <a class="btn" href="admin_novo_jogo.php">+ Novo Jogo</a>
    <button type="button" class="btn" id="btn-pdf">Gerar PDF (Clientes & Valores)</button>
    <button type="button" class="btn" id="btn-grafico">Ver Gráfico de Vendas</button>
  </div>

  <h2>Resumo</h2>
  <p>Total de vendas: <?= (int)$resumo['total_vendas'] ?></p>
  <p>Total arrecadado: R$ <?= number_format($resumo['total_arrecadado'], 2, ',', '.') ?></p>

  <h2>Jogos cadastrados</h2>
  <table class="lista">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Preço</th>
        <th>Estoque</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($j = $jogos->fetch_assoc()): ?>
      <tr>
        <td><?= $j['id'] ?></td>
        <td><?= htmlspecialchars($j['nome']) ?></td>
        <td>R$ <?= number_format($j['preco'], 2, ',', '.') ?></td>
        <td><?= isset($j['estoque']) ? (int)$j['estoque'] : 0 ?></td>
        <td>
          <a class="btn small" href="admin_editar_jogo.php?id=<?= $j['id'] ?>">Editar</a>
          <a class="btn small danger" href="admin_excluir_jogo.php?id=<?= $j['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir?');">Excluir</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <!-- Área do gráfico -->
  <div id="grafico-container" style="margin-top:30px; display:none;">
    <h2>Gráfico de Valores Arrecadados por Dia</h2>
    <canvas id="graficoVendas" width="800" height="300"></canvas>
  </div>
</div>

<script>
// DEBUG: ver se JS carregou
console.log("admin_dashboard.js carregado");

// botões
const btnPdf = document.getElementById('btn-pdf');
const btnGrafico = document.getElementById('btn-grafico');

if (!btnPdf || !btnGrafico) {
  console.error("Botões não encontrados no DOM.");
}

if (btnPdf) {
  btnPdf.addEventListener('click', function() {
    console.log("Clique no botão PDF");
    gerarPDF();
  });
}

if (btnGrafico) {
  btnGrafico.addEventListener('click', function() {
    console.log("Clique no botão gráfico");
    mostrarGrafico();
  });
}

// ============= GRÁFICO =============
function mostrarGrafico() {
  document.getElementById('grafico-container').style.display = 'block';

  if (window._graficoJaCarregado) return;
  window._graficoJaCarregado = true;

  fetch('grafico_vendas.php')
    .then(r => {
      if (!r.ok) {
        throw new Error("Erro HTTP " + r.status);
      }
      return r.json();
    })
    .then(dados => {
      console.log("Dados do gráfico:", dados);
      const ctx = document.getElementById('graficoVendas').getContext('2d');
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: dados.labels,
          datasets: [{
            label: 'Total arrecadado (R$)',
            data: dados.valores
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: { beginAtZero: true }
          }
        }
      });
    })
    .catch(err => {
      alert('Erro ao carregar dados do gráfico.');
      console.error(err);
    });
}

// ============= PDF =============
async function gerarPDF() {
  console.log("Função gerarPDF chamada");
  const { jsPDF } = window.jspdf || {};
  if (!jsPDF) {
    alert("Erro: jsPDF não carregado.");
    console.error("jsPDF não encontrado em window.jspdf");
    return;
  }

  const doc = new jsPDF();

  try {
    const res = await fetch('relatorio_pdf.php');
    if (!res.ok) {
      throw new Error("Erro HTTP " + res.status);
    }
    const dados = await res.json();
    console.log("Dados do PDF:", dados);

    let y = 10;
    doc.setFontSize(14);
    doc.text('Relatorio de Clientes e Valores Arrecadados - RD Jogos', 10, y);
    y += 10;

    doc.setFontSize(11);
    doc.text('Total de clientes: ' + dados.total_clientes, 10, y); y += 6;
    doc.text('Total de vendas: ' + dados.total_vendas, 10, y); y += 6;
    doc.text('Total arrecadado: R$ ' + dados.total_arrecadado_formatado, 10, y); y += 10;

    doc.setFontSize(12);
    doc.text('Clientes:', 10, y); y += 6;
    doc.setFontSize(10);

    dados.clientes.forEach(c => {
      const linha = c.id + ' - ' + c.nome + ' (' + c.email + ') - Gasto: R$ ' + c.total_gasto_formatado;
      if (y > 280) {
        doc.addPage();
        y = 10;
      }
      doc.text(linha, 10, y);
      y += 5;
    });

    doc.save('relatorio_clientes_valores.pdf');
  } catch (e) {
    console.error(e);
    alert('Erro ao gerar PDF.');
  }
}
</script>

</body>
</html>
