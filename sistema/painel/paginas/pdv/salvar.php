<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
require_once("../../../caixa_operador.php");
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, refaça o Login!';
	exit();
}
$id_usuario = $_SESSION['id'];
$id_caixa = exigirCaixaAbertoOperador($pdo, $id_usuario, $id_empresa);
$tabela = 'receber';


$sessao = $id_usuario;

$data_atual = date('Y-m-d');
$mes_atual = Date('m');
$ano_atual = Date('Y');
$data_inicio_mes = $ano_atual."-".$mes_atual."-01";
$data_inicio_ano = $ano_atual."-01-01";

$data_ontem = date('Y-m-d', strtotime("-1 days",strtotime($data_atual)));
$data_amanha = date('Y-m-d', strtotime("+1 days",strtotime($data_atual)));

$total_compra = 0;


if($mes_atual == '04' || $mes_atual == '06' || $mes_atual == '09' || $mes_atual == '11'){
	$data_final_mes = $ano_atual.'-'.$mes_atual.'-30';
}else if($mes_atual == '02'){
	$bissexto = date('L', @mktime(0, 0, 0, 1, 1, $ano_atual));
	if($bissexto == 1){
		$data_final_mes = $ano_atual.'-'.$mes_atual.'-29';
	}else{
		$data_final_mes = $ano_atual.'-'.$mes_atual.'-28';
	}

}else{
	$data_final_mes = $ano_atual.'-'.$mes_atual.'-31';
}



$troco_bruto = trim((string) ($_POST['troco'] ?? ''));
$troco_informado = $troco_bruto !== '';
$troco = $troco_informado ? (float) str_replace(',', '.', $troco_bruto) : 0;
$desconto = trim((string) ($_POST['desconto'] ?? '0'));
$desconto = str_replace(' ', '', $desconto);
if (strpos($desconto, ',') !== false) {
	$desconto = str_replace('.', '', $desconto);
	$desconto = str_replace(',', '.', $desconto);
}
$desconto = (float) $desconto;
$cliente = $_POST['cliente'];
$formas_pagamento_pdv = $_POST['forma_pgto_pdv'] ?? [];
$valores_pagamento_pdv = $_POST['valor_pgto_pdv'] ?? [];
if (!is_array($formas_pagamento_pdv)) $formas_pagamento_pdv = [$formas_pagamento_pdv];
if (!is_array($valores_pagamento_pdv)) $valores_pagamento_pdv = [$valores_pagamento_pdv];
$saida = trim((string) ($formas_pagamento_pdv[0] ?? ''));
$data = $_POST['data2'];
$frete = trim((string) ($_POST['frete'] ?? '0'));
if (strpos($frete, ',') !== false) {
	$frete = str_replace('.', '', $frete);
	$frete = str_replace(',', '.', $frete);
}
$frete = (float) $frete;
$mesa = @$_POST['mesa'];
$entrega = @$_POST['entrega'];
$pago_delivery = @$_POST['pago_delivery'];

$modalidades_permitidas = ['' => true];
if ($entrega_delivery === 'Sim') $modalidades_permitidas['Delivery'] = true;
if ($consumir_local === 'Sim') $modalidades_permitidas['Consumir Local'] = true;
if ($retirar_local === 'Sim') $modalidades_permitidas['Retirar'] = true;
if (!isset($modalidades_permitidas[$entrega])) {
	echo 'Esta modalidade de entrega está desativada nas configurações!';
	exit();
}

if($entrega != "" and $cliente == ""){
	echo 'Para qualquer opcao do delivery precisa selecionar um cliente!';
	exit();
}

$tipo_pedido = '';
if($mesa == "" and $entrega == ""){
	$tipo_pedido = 'Balcão';
}


$tipo_desconto = $_POST['tipo_desconto'];
$subtotal_venda = $_POST['subtotal_venda'];

$dataF = implode('/', array_reverse(explode('-', $data)));

if($desconto == ""){
	$desconto = 0;
}

if($frete == ""){
	$frete = 0;
}

if($cliente == ""){
	$cliente = 0;
}

$total_cartoes_cliente = 0;
$query = $pdo->query("SELECT * FROM clientes where id = '$cliente'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$nome_cliente = @$res[0]['nome'];
$total_cartoes_cliente = @$res[0]['cartoes'];
if(@count($res) > 0){
    if($total_cartoes_cliente == ""){
      $total_cartoes_cliente = 0;
    }
    if($total_cartoes_cliente < $total_cartoes_config){
      $total_cartoes_cliente = $total_cartoes_cliente + 1;
    } 

    if($total_cartoes_config == 0){
       $total_cartoes_cliente = 0;
    }

$pdo->query("UPDATE clientes SET  cartoes = '$total_cartoes_cliente' where id = '$cliente'");

}


$total_carrinho = 0;
$query = $pdo->query("SELECT * FROM carrinho where sessao = '$sessao' and pedido = 0 and mesa = '' and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {
	$itens_carrinho = $res;
  for ($i = 0; $i < $total_reg; $i++) {
    foreach ($res[$i] as $key => $value) {
    }

    $id = $res[$i]['id'];
    $total_item = $res[$i]['total_item'];
    $produto = $res[$i]['produto'];
    $quantidade = $res[$i]['quantidade'];    

    $total_carrinho += $total_item;


    $query2 = $pdo->query("SELECT * FROM produtos where id = '$produto'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    $id_categoria = $res2[0]['categoria'];
    $valor_produto = $res2[0]['valor_venda'];
    $estoque = $res2[0]['estoque'];
    $tem_estoque = $res2[0]['tem_estoque'];
    $valor_compra = $res2[0]['valor_compra'];
    $valor_compra = $quantidade * $valor_compra;
    $total_compra += $valor_compra;
  }
} else {
  echo '0';
  exit();
}




if ($desconto < 0) {
	echo 'O desconto não pode ser negativo!';
	exit();
}

if ($tipo_desconto == '%') {
	if ($desconto > 100) {
		echo 'O desconto percentual não pode ser maior que 100%!';
		exit();
	}
	$valor_desconto = round($total_carrinho * $desconto / 100, 2);
} else {
	$valor_desconto = round($desconto, 2);
}

$total_final = round($total_carrinho + $frete - $valor_desconto, 2);

if($total_final <= 0){
	echo 'O valor da Venda tem que ser maior que zero';
	exit();
}


if(strtotime($data) > strtotime($data_atual)){
	$pago = 'Não';
	$data_pgto = '';
	$usuario_pgto = '';
}else{
	$pago = 'Sim';
	$data_pgto = $data;
	$usuario_pgto = $id_usuario;
}

// No pedido delivery, a situação financeira é definida explicitamente no PDV.
if ($entrega == 'Delivery') {
	$pago = $pago_delivery == 'Sim' ? 'Sim' : 'Não';
	$data_pgto = $pago == 'Sim' ? $data : '';
	$usuario_pgto = $pago == 'Sim' ? $id_usuario : '';
}

function valorPagamentoPdv($valor): float
{
	$valor = trim((string) $valor);
	if ($valor === '') return 0;
	if (strpos($valor, ',') !== false) {
		$valor = str_replace('.', '', $valor);
		$valor = str_replace(',', '.', $valor);
	}
	return round((float) $valor, 2);
}

$query_formas = $pdo->prepare("SELECT id, nome FROM formas_pgto WHERE empresa = :empresa");
$query_formas->execute([':empresa' => $id_empresa]);
$formas_validas = [];
foreach ($query_formas->fetchAll(PDO::FETCH_ASSOC) as $forma_valida) {
	$formas_validas[$forma_valida['nome']] = (int) $forma_valida['id'];
}
$formas_validas['pix_mp'] = 0;

$pagamentos_pdv = [];
$total_pagamentos_pdv = 0;
foreach ($formas_pagamento_pdv as $indice => $forma_pagamento) {
	$forma_pagamento = trim((string) $forma_pagamento);
	$valor_pagamento = valorPagamentoPdv($valores_pagamento_pdv[$indice] ?? 0);
	if ($forma_pagamento === '' && $valor_pagamento <= 0) continue;
	if ($forma_pagamento === '' || !array_key_exists($forma_pagamento, $formas_validas)) {
		echo 'Selecione uma forma de pagamento válida em todas as parcelas!';
		exit();
	}
	if ($valor_pagamento <= 0) {
		echo 'Informe um valor maior que zero em todas as formas de pagamento!';
		exit();
	}
	$pagamentos_pdv[] = [
		'forma' => $forma_pagamento === 'pix_mp' ? 'Pix QRCode' : $forma_pagamento,
		'forma_original' => $forma_pagamento,
		'forma_id' => $formas_validas[$forma_pagamento],
		'valor' => $valor_pagamento,
	];
	$total_pagamentos_pdv += $valor_pagamento;
}

if ($pago === 'Sim') {
	if (!$pagamentos_pdv) {
		echo 'Adicione pelo menos uma forma de pagamento!';
		exit();
	}
	if (abs(round($total_pagamentos_pdv, 2) - $total_final) > 0.01) {
		echo 'A soma das formas de pagamento deve ser igual ao total da venda!';
		exit();
	}
}

$valor_pagamento_dinheiro = 0;
foreach ($pagamentos_pdv as $pagamento_pdv) {
	if (strcasecmp($pagamento_pdv['forma_original'], 'Dinheiro') === 0) {
		$valor_pagamento_dinheiro += $pagamento_pdv['valor'];
	}
}

if ($troco_informado) {
	if ($valor_pagamento_dinheiro <= 0) {
		echo 'O campo Troco Para só pode ser usado quando uma das formas de pagamento for Dinheiro!';
		exit();
	}
	if ($troco < $valor_pagamento_dinheiro) {
		echo 'O valor entregue em dinheiro não pode ser menor que a parcela em dinheiro!';
		exit();
	}
}

$tipo_pgto_registro = $pagamentos_pdv
	? implode(' + ', array_values(array_unique(array_column($pagamentos_pdv, 'forma'))))
	: ($saida === 'pix_mp' ? 'Pix QRCode' : $saida);
$id_saida = $pagamentos_pdv[0]['forma_id'] ?? 0;

// A baixa de estoque ocorre somente depois da validação integral dos pagamentos.
foreach ($itens_carrinho as $item_carrinho) {
	$query_produto = $pdo->prepare("SELECT estoque, tem_estoque FROM produtos WHERE id = :id AND empresa = :empresa LIMIT 1");
	$query_produto->execute([':id' => $item_carrinho['produto'], ':empresa' => $id_empresa]);
	$produto_estoque = $query_produto->fetch(PDO::FETCH_ASSOC);
	if ($produto_estoque && $produto_estoque['tem_estoque'] === 'Sim') {
		$novo_estoque = (float) $produto_estoque['estoque'] - (float) $item_carrinho['quantidade'];
		$query_estoque = $pdo->prepare("UPDATE produtos SET estoque = :estoque WHERE id = :id AND empresa = :empresa");
		$query_estoque->execute([':estoque' => $novo_estoque, ':id' => $item_carrinho['produto'], ':empresa' => $id_empresa]);
	}
}
$valor_recebido = $pago == 'Sim' ? $total_final : 0;


//recuperar número do pedido
$query = $pdo->query("SELECT * FROM vendas where data = curDate() and empresa = '$id_empresa' order by id desc limit 1");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$num_pedido = @$res[0]['pedido'];
if ($num_pedido == "") {
  $num_pedido = 0;
}
$pedido = $num_pedido + 1;

$troco_calculado = 0;
if ($troco_informado && $valor_pagamento_dinheiro > 0) {
	$troco_calculado = round($troco - $valor_pagamento_dinheiro, 2);
}

if ($pago != 'Sim') {
	$troco = 0;
	$troco_calculado = 0;
}
$total_pago_registro = $pago == 'Sim' ? $total_final : 0;

if($entrega == ""){
	$entrega = 'Consumir Local';
}


$query = $pdo->prepare("INSERT INTO vendas SET cliente = '$cliente', valor = '$total_final', total_pago = '$total_pago_registro', valor_recebido = '$valor_recebido', troco = '$troco_calculado', data = curDate(), hora = curTime(), status = 'Iniciado', pago = '$pago', obs = '', taxa_entrega = '$frete', tipo_pgto = :tipo_pgto, usuario_baixa = '0', entrega = '$entrega', mesa = '$mesa', nome_cliente = '$nome_cliente', cupom = '$valor_desconto', pago_entregador = 'Não', pedido = '$pedido', ref_api = '', tipo_pedido = '$tipo_pedido', taxa_extra_pedido = '0', caixa = '$id_caixa', custo = '$total_compra', empresa = '$id_empresa' ");
$query->bindValue(':tipo_pgto', $tipo_pgto_registro);
$query->execute();
$id_pedido = $pdo->lastInsertId();
$id_pedido_feito = $pdo->lastInsertId();

if ($pago === 'Sim') {
	$query_pagamento = $pdo->prepare("INSERT INTO vendas_pagamentos (venda, forma_pgto, valor, usuario, caixa, data, hora, empresa)
		VALUES (:venda, :forma, :valor, :usuario, :caixa, CURDATE(), CURTIME(), :empresa)");
	foreach ($pagamentos_pdv as $pagamento_pdv) {
		$query_pagamento->execute([
			':venda' => $id_pedido,
			':forma' => $pagamento_pdv['forma'],
			':valor' => $pagamento_pdv['valor'],
			':usuario' => $id_usuario,
			':caixa' => $id_caixa,
			':empresa' => $id_empresa,
		]);
	}
}


// Lançar cada forma separadamente no financeiro. Pedidos pendentes continuam
// com um único título até que o pagamento seja confirmado.
if ($pago === 'Sim' && $pagamentos_pdv) {
	$query_receber = $pdo->prepare("INSERT INTO receber SET descricao = 'Nova Venda', cliente = :cliente,
		valor = :valor, vencimento = :data, data_pgto = :data, data_lanc = CURDATE(), forma_pgto = :forma,
		frequencia = '0', arquivo = 'sem-foto.jpg', foto = 'sem-foto.jpg', subtotal = :valor,
		usuario_lanc = :usuario, usuario_pgto = :usuario, pago = 'Sim', referencia = 'Venda',
		id_ref = :pedido, caixa = :caixa, hora = CURTIME(), tipo = 'Balcão', custo = :custo, empresa = :empresa");

	foreach ($pagamentos_pdv as $pagamento_pdv) {
		$proporcao_pagamento = $total_final > 0 ? $pagamento_pdv['valor'] / $total_final : 0;
		$query_receber->execute([
			':cliente' => $cliente,
			':valor' => $pagamento_pdv['valor'],
			':data' => $data_pgto ?: date('Y-m-d'),
			':forma' => $pagamento_pdv['forma_id'],
			':usuario' => $id_usuario,
			':pedido' => $id_pedido,
			':caixa' => $id_caixa,
			':custo' => round($total_compra * $proporcao_pagamento, 2),
			':empresa' => $id_empresa,
		]);
	}
} else {
	$pdo->query("INSERT INTO receber SET descricao = 'Nova Venda', cliente = '$cliente', valor = '$total_final', vencimento = '$data', data_pgto = " . ($data_pgto == '' ? "NULL" : "'$data_pgto'") . ", data_lanc = curDate(), forma_pgto = '$id_saida', frequencia = '0',  arquivo = 'sem-foto.jpg', foto = 'sem-foto.jpg', subtotal = '$total_final', usuario_lanc = '$id_usuario', usuario_pgto = " . ($usuario_pgto == '' ? "NULL" : "'$usuario_pgto'") . ", pago = '$pago', referencia = 'Venda', id_ref = '$id_pedido', caixa = '$id_caixa', hora = curTime(), tipo = 'Balcão', custo = '$total_compra', empresa = '$id_empresa' ");
}


//relacionar itens do carrinho com o pedido
$pdo->query("UPDATE carrinho SET cliente = '$cliente', pedido = '$id_pedido' where sessao = '$id_usuario' and pedido = '0' and mesa = ''");
$pdo->query("UPDATE temp SET pedido = '$id_pedido' where sessao = '$id_usuario' and pedido = '0'");



echo 'Salvo com Sucesso-'.$id_pedido;

 ?>
