<?php
$tabela = 'carrinho';
require_once("../../../conexao.php");

$id_carrinho = $_POST['id'] ?? null;
if (!$id_carrinho) {
  echo '<div class="alert alert-danger">ID do carrinho não informado.</div>';
  exit();
}

// Buscar o item do carrinho
$query = $pdo->prepare("SELECT * FROM carrinho WHERE id = :id");
$query->execute([':id' => $id_carrinho]);
$it = $query->fetch(PDO::FETCH_ASSOC);

if (!$it) {
  echo '<div class="alert alert-warning">Item não encontrado.</div>';
  exit();
}

$id_produto        = $it['produto'];
$quantidade        = (float) ($it['quantidade'] ?? 0);
$total_item        = (float) ($it['total_item'] ?? 0);
$obs_item          = $it['obs'] ?? '';
$item              = $it['item'] ?? '';
$variacao          = $it['variacao'] ?? null;
$nome_produto_tab  = $it['nome_produto'] ?? '';
$sabores           = (int) ($it['sabores'] ?? 0);
$borda             = $it['borda'] ?? '';
$categoria         = $it['categoria'] ?? '';

// total do item (mantendo sua lógica)
$total_item = $total_item * max(1, $quantidade);

// tratar quantidade (ocultar .0)
$qt = explode('.', (string)$quantidade);
if (isset($qt[1]) && (int)$qt[1] > 0) {
  $quantidadeF = $quantidade;
} else {
  $quantidadeF = (int)$qt[0];
}

/* -------- variação (sigla) -------- */
$sigla_variacao = '';
if (!empty($variacao)) {
  $qVar = $pdo->prepare("SELECT sigla FROM variacoes WHERE id = :id");
  $qVar->execute([':id' => $variacao]);
  $sigla_variacao = $qVar->fetchColumn();
  $sigla_variacao = $sigla_variacao ? '(' . $sigla_variacao . ')' : '';
}

/* -------- grade (sigla texto) -------- */
$sigla_grade = '';
$qTempVar = $pdo->prepare("SELECT id_item FROM temp WHERE carrinho = :carrinho AND tabela = 'Variação' ORDER BY id ASC LIMIT 1");
$qTempVar->execute([':carrinho' => $id_carrinho]);
$id_do_item = $qTempVar->fetchColumn();
if ($id_do_item) {
  $qGradeItem = $pdo->prepare("SELECT texto FROM itens_grade WHERE id = :id");
  $qGradeItem->execute([':id' => $id_do_item]);
  $sigla_grade = $qGradeItem->fetchColumn() ?: '';
}

/* -------- produto, foto, unidade -------- */
$nome_produto = $nome_produto_tab;
$foto_produto = '';
$sigla_unidade = '';
$ocultar_quantidades = '';

if (!empty($id_produto)) {
  $qProd = $pdo->prepare("SELECT nome, foto, unidade FROM produtos WHERE id = :id");
  $qProd->execute([':id' => $id_produto]);
  $p = $qProd->fetch(PDO::FETCH_ASSOC);

  if ($p) {
    $nome_produto = $p['nome'] ?? $nome_produto_tab;
    $foto_produto = $p['foto'] ?? '';
    $unidade      = $p['unidade'] ?? null;

    if ($unidade) {
      $qUni = $pdo->prepare("SELECT nome FROM unidade_medida WHERE id = :id");
      $qUni->execute([':id' => $unidade]);
      $nome_unidade = $qUni->fetchColumn() ?: 'Sem Unidade';

      if (in_array($nome_unidade, ['Quilogramas','Quilo','Quilograma','KG'])) {
        $sigla_unidade = ' (KG)';
        $ocultar_quantidades = 'ocultar';
      }
      if (in_array($nome_unidade, ['Metros','Metro','M','m'])) {
        $sigla_unidade = ' (m)';
        $ocultar_quantidades = 'ocultar';
      }
      if (in_array($nome_unidade, ['Litro','Litros','L'])) {
        $sigla_unidade = ' (L)';
        $ocultar_quantidades = 'ocultar';
      }
    }
  }
}

/* -------- borda -------- */
$nome_borda = '';
if (!empty($borda)) {
  $qB = $pdo->prepare("SELECT nome FROM bordas WHERE id = :id");
  $qB->execute([':id' => $borda]);
  $nome_borda = $qB->fetchColumn() ?: 'Sem Borda';
}

/* -------- adicionais (temp) -------- */
$tabela_ad = 'adicionais';
if ($sabores > 0) {
  $tabela_ad = 'adicionais_cat';
}
$qAd = $pdo->prepare("SELECT id_item, quantidade FROM temp WHERE carrinho = :carrinho AND tabela = 'adicionais'");
$qAd->execute([':carrinho' => $id_carrinho]);
$adicionais = $qAd->fetchAll(PDO::FETCH_ASSOC);
$totalAd = count($adicionais);

/* -------- grades do produto (não variação) -------- */
$grades = [];
if (!empty($id_produto)) {
  $qGrades = $pdo->prepare("SELECT id, nome_comprovante, tipo_item FROM grades WHERE produto = :prod AND tipo_item != 'Variação' ORDER BY posicao ASC");
  $qGrades->execute([':prod' => $id_produto]);
  $grades = $qGrades->fetchAll(PDO::FETCH_ASSOC);
}

/* -------- formatações -------- */
$total_itemF = number_format($total_item, 2, ',', '.');

ob_start();
?>
<div class="row itens">
  <div class="col-9" align="left">
    <?php echo $quantidadeF ?> <?php echo $sigla_unidade ?> - <?php echo htmlspecialchars($nome_produto) ?>
    <?php echo ' ' . htmlspecialchars($sigla_variacao) . ' ' . htmlspecialchars($sigla_grade) ?>
  </div>
  <div class="col-3" align="right">
    R$ <?php echo $total_itemF; ?>
  </div>

  <?php if ($sabores > 1): ?>
    <span style="margin-left: 15px"><b>Atenção: </b><?php echo (int)$sabores; ?> Sabores</span><br>
  <?php endif; ?>

  <?php if (!empty($nome_borda)): ?>
    <span style="margin-left: 15px"><b>Borda: </b><?php echo htmlspecialchars($nome_borda); ?></span><br>
  <?php endif; ?>

  <?php if ($totalAd > 0): ?>
    <?php
      $texto_adicional = $totalAd > 1 ? "($totalAd) Tipos de Adicionais" : "($totalAd) Tipo de Adicional";
    ?>
    <div align="left" style="margin-left: 15px">
      <b><?php echo $texto_adicional ?> : </b>
      <?php
        foreach ($adicionais as $idx => $ad) {
          $id_item = $ad['id_item'];
          $qNomeAd = $pdo->prepare("SELECT nome FROM {$tabela_ad} WHERE id = :id");
          $qNomeAd->execute([':id' => $id_item]);
          $nome_adc = $qNomeAd->fetchColumn() ?: '';
          $qtd_adc  = $ad['quantidade'] ?? 1;
          echo '(' . $qtd_adc . ') ' . htmlspecialchars($nome_adc);
          if ($idx < $totalAd - 1) echo ', ';
        }
      ?>
     
    </div>
  <?php endif; ?>

  <?php
  // Percorre as grades (não variação)
  if (!empty($grades)) {
    foreach ($grades as $g) {
      $id_da_grade    = $g['id'];
      $nome_da_grade  = $g['nome_comprovante'];
      $tipo_item_grade= $g['tipo_item'];

      $qSel = $pdo->prepare("SELECT id_item, quantidade FROM temp WHERE carrinho = :c AND grade = :g ORDER BY id ASC");
      $qSel->execute([':c' => $id_carrinho, ':g' => $id_da_grade]);
      $selecionados = $qSel->fetchAll(PDO::FETCH_ASSOC);

      if (!empty($selecionados)) {
        echo '<div>';
        echo '<b><small>' . htmlspecialchars($nome_da_grade) . '</small></b>';

        $pedacos = [];
        foreach ($selecionados as $sel) {
          $id_item = $sel['id_item'];
          $qtd     = (int)($sel['quantidade'] ?? 1);

          $qItem = $pdo->prepare("SELECT texto FROM itens_grade WHERE id = :id");
          $qItem->execute([':id' => $id_item]);
          $nome_item = $qItem->fetchColumn() ?: '';

          $quant_lbl = ($tipo_item_grade === 'Múltiplo') ? '(' . $qtd . ')' : '';
          $pedacos[] = '<i><small>' . $quant_lbl . htmlspecialchars($nome_item) . '</small></i>';
        }
        echo ' ' . implode(', ', $pedacos);
        echo '<br>';
        echo '</div>';
      }
    }
  }
  ?>

  <?php if (!empty($obs_item)): ?>
    <div align="left" style="margin-left: 15px">
      <small><b>Observação : </b><?php echo nl2br(htmlspecialchars($obs_item)); ?></small>
    </div>
  <?php endif; ?>
</div>
<?php
echo ob_get_clean();
