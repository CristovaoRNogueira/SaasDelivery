<?php
$tabela = 'pagar_sas';
require_once("../../../conexao.php");

@session_start();
$id_usuario = $_SESSION['id'];


$id = $_POST['novo_id'];


$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);

$descricao = $res[0]['descricao'];
$fornecedor = $res[0]['fornecedor'];
$funcionario = $res[0]['funcionario'];
$valor_antigo = $res[0]['valor'];
$data_lanc = $res[0]['data_lanc'];
$data_venc = $res[0]['vencimento'];
$data_pgto = $res[0]['data_pgto'];
$usuario_lanc = $res[0]['usuario_lanc'];
$usuario_pgto = $res[0]['usuario_pgto'];
$frequencia = $res[0]['frequencia'];
$saida_antiga = $res[0]['forma_pgto'];
$arquivo = $res[0]['arquivo'];
$pago = $res[0]['pago'];
$referencia = $res[0]['referencia'];
$hash = $res[0]['hash'];
$vencimento = $res[0]['vencimento'];
$quant_recorrencia = @$res[0]['quant_recorrencia'];
$recorrencia_inf = @$res[0]['recorrencia_inf'];

if ($hash != "") {
	require("../../apis/cancelar_agendamento.php");
}



//CRIAR A PRÓXIMA CONTA A PAGAR
$dias_frequencia = $frequencia;

if ($dias_frequencia == 30 || $dias_frequencia == 31) {
	$nova_data_vencimento = date('Y/m/d', strtotime("+1 month", strtotime($data_venc)));

} else if ($dias_frequencia == 90) {
	$nova_data_vencimento = date('Y/m/d', strtotime("+3 month", strtotime($data_venc)));

} else if ($dias_frequencia == 180) {
	$nova_data_vencimento = date('Y/m/d', strtotime("+6 month", strtotime($data_venc)));

} else if ($dias_frequencia == 360 || $dias_frequencia == 365) {
	$nova_data_vencimento = date('Y/m/d', strtotime("+1 year", strtotime($data_venc)));

} else {
	$nova_data_vencimento = date('Y/m/d', strtotime("+$dias_frequencia days", strtotime($data_venc)));
}


if (@$dias_frequencia > 0 and $recorrencia_inf == 'Não' and ($quant_recorrencia == '' || $quant_recorrencia == '0')) {
	$pdo->query("INSERT INTO $tabela set descricao = '$descricao', fornecedor = '$fornecedor', funcionario = '$funcionario', valor = '$valor_antigo', data_lanc = curDate(), vencimento = '$nova_data_vencimento', frequencia = '$frequencia', forma_pgto = '$saida_antiga', arquivo = '$arquivo', pago = 'Não', referencia = '$referencia', usuario_lanc = '$id_usuario', hora = curTime(), recorrencia_inf = '$recorrencia_inf'");
	$id_ult_registro = $pdo->lastInsertId();
}


$pdo->query("UPDATE $tabela SET data_pgto = curDate(), usuario_pgto = '$id_usuario', pago = 'Sim', hora = curTime() where id = '$id' and pago != 'Sim'");


echo 'Baixado com Sucesso';
?>