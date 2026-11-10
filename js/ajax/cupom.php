<?php
@session_start();
require_once ('../../sistema/conexao.php');

$id_empresa = @$_SESSION['empresa'];
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, acesse novamente!';
	exit();
}
require_once("../../sistema/painel/buscar_config.php");


$data_atual = date('Y-m-d');

$total_final = filter_var(@$_POST['total_final'], @FILTER_SANITIZE_STRING);
$codigo_cupom = filter_var(@$_POST['codigo_cupom'], @FILTER_SANITIZE_STRING);
$telefone_cliente = filter_var(@$_POST['telefone_cliente'], @FILTER_SANITIZE_STRING);


$query = $pdo->prepare("SELECT * FROM clientes where telefone = :telefone_cliente and empresa = '$id_empresa' ");
$query->bindValue(":telefone_cliente", "$telefone_cliente");
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$cartoes = @$res[0]['cartoes'];

if($codigo_cupom == 'cartao' || $codigo_cupom == 'cartão' || $codigo_cupom == 'Cartao' || $codigo_cupom == 'Cartão'){
	if($cartoes >= $total_cartoes_config){
		$valor_total = $total_final - $valor_cupom_config;
		$valor_totalF = number_format($valor_total, 2, ',', '.');
		$valor_cupomF = number_format($valor_cupom_config, 2, ',', '.');
		echo $valor_totalF . '**' . $valor_cupom_config . '**' . $valor_cupomF;

	}else{
		echo '0';
	}
}else{


	$query = $pdo->prepare("SELECT * FROM cupons where codigo = :codigo_cupom and empresa = '$id_empresa' ");
$query->bindValue(":codigo_cupom", "$codigo_cupom");
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_cupons = @count($res);
if ($total_cupons == 0) {
	echo '0';
	exit();
} else {
	$valor_cupom = $res[0]['valor'];
	$data = $res[0]['data'];
	$quantidade = $res[0]['quantidade'];
	$valor_minimo = $res[0]['valor_minimo'];
	$tipo = $res[0]['tipo'];



	if ($data != "" and $data != "0000-00-00") {
		if (strtotime($data) < strtotime($data_atual)) {
			echo '1';
			exit();
		}
	}

	if ($quantidade < 1) {
		echo '2';
		exit();
	}

	if ($valor_minimo > 0) {
		if ($total_final <= $valor_minimo) {
			echo '3**' . $valor_minimo;
			exit();
		}
	}

	if ($tipo == '%') {
		$valor_cupom = $valor_cupom * $total_final / 100;
		$valor_total = $total_final - $valor_cupom;

	} else {
		$valor_total = $total_final - $valor_cupom;
	}


	$valor_totalF = number_format($valor_total, 2, ',', '.');
	$valor_cupomF = number_format($valor_cupom, 2, ',', '.');
	echo $valor_totalF . '**' . $valor_cupom . '**' . $valor_cupomF;

	//deletar o cupom
	//$pdo->query("DELETE FROM cupons where codigo = '$codigo_cupom'");

	//abater 1 na quantidade de cupom
	$nova_quant = $quantidade - 1;
	$query = $pdo->prepare("UPDATE cupons SET quantidade = '$nova_quant' where codigo = :codigo_cupom");
	$query->bindValue(":codigo_cupom", "$codigo_cupom");
	$query->execute();
}

}



?>