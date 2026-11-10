<?php
$tabela = 'pagar_sas';
require_once("../../../conexao.php");
@session_start();
$id_usuario = $_SESSION['id'];

$id = $_POST['id-baixar'];
$data_atual = date('Y-m-d');
$dia = date('d');
$mes = date('m');
$ano = date('Y');



$valor = $_POST['valor-baixar'];
// 2. Converte a vírgula para ponto e remove os pontos de milhar
$valor = str_replace(',', '.', str_replace('.', '', $valor));


$multa = $_POST['valor-multa'];
$multa = str_replace(',', '.', $multa);

$desconto = $_POST['valor-desconto'];
$desconto = str_replace(',', '.', $desconto);

$juros = $_POST['valor-juros'];
$juros = str_replace(',', '.', $juros);

$valor = $_POST['valor-baixar'];
// 2. Converte a vírgula para ponto e remove os pontos de milhar
$valor = str_replace(',', '.', str_replace('.', '', $valor));
$valor_padrao = $valor;

$subtotal = $_POST['subtotal'];
// 2. Converte a vírgula para ponto e remove os pontos de milhar
$subtotal = str_replace(',', '.', str_replace('.', '', $subtotal));

$saida = $_POST['saida-baixar'];
$data_baixar = $_POST['data-baixar'];

if ($juros == "") {
	$juros = 0;
}

if ($multa == "") {
	$multa = 0;
}

if ($desconto == "") {
	$desconto = 0;
}


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

if ($fornecedor == "") {
	$fornecedor = 0;
}

if ($funcionario == "") {
	$funcionario = 0;
}

if ($usuario_pgto == "") {
	$usuario_pgto = 0;
}


if ($valor > $valor_antigo) {
	echo 'O valor a ser pago não pode ser superior ao valor da conta! O valor da conta é de R$ ' . $valor_antigo;
	exit();
}

if ($valor <= 0) {
	echo 'O precisa ser maior que 0 ';
	exit();
}



if ($valor == $valor_antigo) {

	$pdo->query("UPDATE $tabela set forma_pgto = '$saida', usuario_pgto = '$id_usuario', pago = 'Sim', subtotal = '$subtotal', juros = '$juros', multa = '$multa', desconto = '$desconto', data_pgto = '$data_baixar', hora = curTime() where id = '$id'");

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



} else {

	$descricao = '(Resíduo) ' . $descricao;
	//PEGAR RESIDUOS DA CONTA
	$total_resid = 0;
	$query = $pdo->query("SELECT * FROM $tabela WHERE id_ref = '$id' and residuo = 'Sim'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	if (@count($res) > 0) {

		for ($i = 0; $i < @count($res); $i++) {
			foreach ($res[$i] as $key => $value) {
			}
			$valor_resid = $res[$i]['valor'];
			$total_resid += $valor_resid;
		}
	}

	$valor_antigo = $valor_antigo - ($subtotal - $multa - $juros);

	$pdo->query("INSERT INTO $tabela set id_ref = '$id', referencia = '$referencia', valor = '$valor_padrao', data_pgto = curDate(), vencimento = curDate(), data_lanc = curDate(), descricao = '$descricao', usuario_lanc = '$id_usuario', usuario_pgto = '$id_usuario', fornecedor = '$fornecedor', funcionario = '$funcionario', forma_pgto = '$saida', frequencia = '$frequencia', arquivo = '$arquivo', subtotal = '$subtotal', pago = 'Sim', multa = '$multa', juros = '$juros', desconto = '$desconto', residuo = 'Sim', hora = curTime()");



	$pdo->query("UPDATE $tabela set forma_pgto = '$saida', usuario_pgto = '$id_usuario', valor = '$valor_antigo', data_pgto = curDate() where id = '$id'");


	if ($api_whatsapp != 'Não' and $telefone_sistema != '') {

		$valorF = @number_format($valor_antigo, 2, ',', '.');

		$telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone_sistema);
		$mensagem_whatsapp = '*' . $nome_sistema . '*%0A';
		$mensagem_whatsapp .= '_Conta Vencendo Hoje_ %0A';
		$mensagem_whatsapp .= '*Descrição:* ' . $descricao . ' %0A';
		$mensagem_whatsapp .= '*Valor:* ' . $valorF . ' %0A';

		$data_agd = $vencimento . ' 08:00:00';
		require('../../apis/agendar.php');

		$pdo->query("UPDATE $tabela SET hash = '$hash' where id = '$id'");

	}

}

echo 'Baixado com Sucesso!';

?>