<?php 
require_once("../../../conexao.php");
require_once("../../../pedido_financeiro.php");
$tabela = 'vendas';
@session_start();
$id_usuario = $_SESSION['id'];


$id = (int) ($_POST['id'] ?? 0);
$id_empresa = (int) ($_SESSION['empresa'] ?? 0);
$valorInformado = trim((string) ($_POST['valor'] ?? ''));
$valorInformado = str_replace(['R$', ' '], '', $valorInformado);

if (strpos($valorInformado, ',') !== false) {
  $valorInformado = str_replace('.', '', $valorInformado);
  $valorInformado = str_replace(',', '.', $valorInformado);
}

if ($valorInformado === '' || !is_numeric($valorInformado)) {
  echo 'Informe um valor de desconto válido! Exemplo: 0,99';
  exit();
}

$valor = round((float) $valorInformado, 2);
if ($valor < 0) {
  echo 'O desconto não pode ser negativo!';
  exit();
}

$query = $pdo->prepare('SELECT * FROM vendas WHERE id = :id AND empresa = :empresa LIMIT 1');
$query->execute([':id' => $id, ':empresa' => $id_empresa]);
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {
  if (!pedidoPermiteEdicao($pdo, $id, $id_empresa)) {
    echo 'O pedido já saiu para entrega e não pode receber desconto!';
    exit();
  }
  $valor_total = (float) $res[0]['valor'];
  $desconto_atual = (float) ($res[0]['cupom'] ?? 0);
  $valor_sem_desconto = round($valor_total + $desconto_atual, 2);

  if ($valor > $valor_sem_desconto) {
    echo 'O desconto não pode ser maior que o valor do pedido (R$ ' . number_format($valor_sem_desconto, 2, ',', '.') . ')!';
    exit();
  }

  $novo_valor = round($valor_sem_desconto - $valor, 2);
} else {
  echo 'Pedido não encontrado!';
  exit();
}

$query = $pdo->prepare("UPDATE $tabela SET cupom = :valor WHERE id = :id AND empresa = :empresa");
$query->execute([':valor' => $valor, ':id' => $id, ':empresa' => $id_empresa]);
sincronizarFinanceiroPedidoEditado($pdo, $id, $id_empresa, $novo_valor);

echo 'Baixado com Sucesso';
