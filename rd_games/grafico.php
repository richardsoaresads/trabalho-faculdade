<?php include 'conexao.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>RD Games - Relatórios e Gráficos</title>
  <link rel="stylesheet" href="estilo.css">
  <style>
    /* Garantir espaço pro gráfico */
    .report-area {
      max-width: 1000px;
      margin: 0 auto;
      padding: 24px;
      border-radius: 20px;
      background: rgba(15, 23, 42, 0.95);
      box-shadow:
        0 20px 40px rgba(15, 23, 42, 0.9),
        0 0 0 1px rgba(148, 163, 184, 0.15);
    }

    #chartContainer {
      margin-top: 24px;
      padding: 16px;
      border-radius: 16px;
      background: rgba(15, 23, 42, 0.9);
      min-height: 320px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    #categoryChart {
      max-width: 500px;
      max-height: 300px;
    }
  </style>
</head>
<body>
  <header>
    <div class="header-inner">
      <div class="title-wrap">
        <h1>RD Games</h1>
        <p class="subtitle">Relatórios e gráficos de produtos</p>
      </div>

      <div class="controls">
        <a href="index.php" class="btn">Voltar para produtos</a>
        <button id="btn-pdf" type="button" class="btn primary">Exportar PDF</button>

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
    <?php
      // Busca todos os produtos
      $sql = "SELECT nome, categoria, preco, quantidade FROM produtos";
      $result = $conn->query($sql);

      $produtos = [];
      if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          $produtos[] = $row;
        }
      }

      $categorias = [];
      $totalItens = 0;
      $totalValor = 0;

      foreach ($produtos as $p) {
        $cat = $p['categoria'] ?: 'Sem categoria';
        $preco = floatval($p['preco']);
        $qtd = intval($p['quantidade']);

        if (!isset($categorias[$cat])) {
          $categorias[$cat] = ['itens' => 0, 'qtd' => 0, 'valor' => 0];
        }

        $categorias[$cat]['itens'] += 1;
        $categorias[$cat]['qtd'] += $qtd;
        $categorias[$cat]['valor'] += $preco * $qtd;

        $totalItens += $qtd;
        $totalValor += $preco * $qtd;
      }

      $labels  = array_keys($categorias);
      $valores = array_map(function($c) { return $c['valor']; }, $categorias);
    ?>

    <section id="reportSection" class="report-area">
      <h3>Resumo do estoque</h3>

      <?php if (count($produtos) === 0): ?>
        <p id="reportSummary">Nenhum produto cadastrado para gerar relatório.</p>
      <?php else: ?>
        <p id="reportSummary">
          Total de categorias: <?php echo count($categorias); ?> |
          Total de itens em estoque: <?php echo $totalItens; ?> |
          Valor estimado em estoque:
          R$ <?php echo number_format($totalValor, 2, ',', '.'); ?>
        </p>
      <?php endif; ?>

      <div id="chartContainer">
        <?php if (count($labels) > 0): ?>
          <canvas id="categoryChart"></canvas>
        <?php else: ?>
          <span>Sem dados suficientes para montar o gráfico.</span>
        <?php endif; ?>
      </div>

      <div class="table-wrap" style="margin-top:16px;">
        <table>
          <thead>
            <tr>
              <th>Categoria</th>
              <th>Qtd. Itens</th>
              <th>Qtd. Unidades</th>
              <th>Valor total (R$)</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($categorias) > 0): ?>
              <?php foreach ($categorias as $nomeCat => $dados): ?>
                <tr>
                  <td><?php echo htmlspecialchars($nomeCat); ?></td>
                  <td><?php echo $dados['itens']; ?></td>
                  <td><?php echo $dados['qtd']; ?></td>
                  <td>R$ <?php echo number_format($dados['valor'], 2, ',', '.'); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="4">Sem dados para exibir.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <footer>
    <div class="footer-inner">
      <p>Projeto de Extensão - RD Games</p>
      <div class="socials">
        <a href="https://github.com/seu-usuario" target="_blank" rel="noopener" aria-label="GitHub"></a>
        <a href="https://www.linkedin.com/in/seu-usuario" target="_blank" rel="noopener" aria-label="LinkedIn"></a>
      </div>
    </div>
  </footer>

  <!-- LIBS PARA GRÁFICO E PDF (ordem importa) -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script>
    console.log('Chart global?', typeof Chart);
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
    // ==== DARK MODE ====
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

    // ==== DADOS DO PHP (NORMALIZADOS) ====
    const labels = <?php echo json_encode($labels, JSON_UNESCAPED_UNICODE); ?>;
    let valuesRaw = <?php echo json_encode($valores, JSON_UNESCAPED_UNICODE); ?>;

    // garante que seja array
    let valuesArr = Array.isArray(valuesRaw) ? valuesRaw : Object.values(valuesRaw);

    // garante que sejam números
    valuesArr = valuesArr.map(v => Number(v));
    const values = valuesArr;

    console.log('labels', labels);
    console.log('values normalizados', values);

    const canvasEl = document.getElementById('categoryChart');
    if (canvasEl && labels.length > 0 && typeof Chart !== 'undefined') {
      const ctx = canvasEl.getContext('2d');

      new Chart(ctx, {
        type: 'pie',
        data: {
          labels: labels,
          datasets: [{
            label: 'Valor por categoria (R$)',
            data: values
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { position: 'bottom' }
          }
        }
      });
    } else {
      console.warn('Não foi possível montar o gráfico. canvasEl:', canvasEl, 'labels:', labels, 'Chart:', typeof Chart);
    }

    // ==== PDF DA SEÇÃO DE RELATÓRIO ====
    document.getElementById('btn-pdf').addEventListener('click', async () => {
      const node = document.getElementById('reportSection');
      await new Promise(r => requestAnimationFrame(r));

      html2canvas(node, { scale: 2 }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p','mm','a4');
        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();
        const imgProps = pdf.getImageProperties(imgData);
        const imgWidth = pageWidth - 20;
        const imgHeight = (imgProps.height * imgWidth) / imgProps.width;
        let y = 10;
        pdf.setFontSize(14);
        pdf.text('Relatório RD Games - Estoque', 14, y);
        y += 8;
        pdf.addImage(imgData, 'PNG', 10, y, imgWidth, Math.min(imgHeight, pageHeight - y - 10));
        pdf.save('rdgames_grafico_relatorio.pdf');
      }).catch(err => {
        alert('Erro ao gerar PDF: ' + err.message);
      });
    });
  });
  </script>
</body>
</html>
