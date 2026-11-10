<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, refaça o Login!';
	exit();
}
$tabela = 'receber';


@session_start();
$id_usuario = $_SESSION['id'];

$hora = date('H');

########## SAUDAÇÃO #############
if ($hora < 12 && $hora >= 6)
	$saudacao = "Bom Dia";

if ($hora >= 12 && $hora < 18)
	$saudacao = "Boa Tarde";

if ($hora >= 18 && $hora <= 23)
	$saudacao = "Boa Noite";
if ($hora < 6 && $hora >= 0)
	$saudacao = "Boa madrugada";




$id = $_POST['id-parcelar'];
$nome = $_POST['nome-parcelar'];
$dias_frequencia = $_POST['frequencia'];
$qtd_parcelas = $_POST['qtd-parcelar'];

$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);

$descricao = $res[0]['descricao'];
$cliente = $res[0]['cliente'];
$valor = $res[0]['valor'];
$data_lanc = $res[0]['data_lanc'];
$data_venc = $res[0]['vencimento'];
$data_pgto = $res[0]['data_pgto'];
$usuario_lanc = $res[0]['usuario_lanc'];
$usuario_pgto = $res[0]['usuario_pgto'];
$frequencia = $res[0]['frequencia'];
$saida = $res[0]['forma_pgto'];
$arquivo = $res[0]['arquivo'];
$pago = $res[0]['pago'];
$referencia = $res[0]['referencia'];
$referencia = $res[0]['referencia'];
$id_ref = $res[0]['id_ref'];
$hash = $res[0]['hash'];

if ($hash != "") {
	require("../../apis/cancelar_agendamento.php");
}

if($id_ref == ""){
	$id_ref = 0;
}

if($cliente == ""){
	$cliente = 0;
}

if($usuario_pgto == ""){
	$usuario_pgto = 0;
}


for($i=1; $i <= $qtd_parcelas; $i++){

	$nova_descricao = $descricao . ' - Parcela '.$i;
	$novo_valor = $valor / $qtd_parcelas;
	$dias_parcela = $i - 1;
	$dias_parcela_2 = ($i - 1) * $dias_frequencia;

	if($i == 1){
		$novo_vencimento = $data_venc;
	}else{

		if($dias_frequencia == 30 || $dias_frequencia == 31){
			
			$novo_vencimento = date('Y/m/d', strtotime("+$dias_parcela month",strtotime($data_venc)));

		}else if($dias_frequencia == 90){ 
			$dias_parcela = $dias_parcela * 3;
			$novo_vencimento = date('Y/m/d', strtotime("+$dias_parcela month",strtotime($data_venc)));

		}else if($dias_frequencia == 180){ 

			$dias_parcela = $dias_parcela * 6;
			$novo_vencimento = date('Y/m/d', strtotime("+$dias_parcela month",strtotime($data_venc)));

		}else if($dias_frequencia == 360 || $dias_frequencia == 365){ 

			$dias_parcela = $dias_parcela * 12;
			$novo_vencimento = date('Y/m/d', strtotime("+$dias_parcela month",strtotime($data_venc)));

		}else{
			
			$novo_vencimento = date('Y/m/d', strtotime("+$dias_parcela_2 days",strtotime($data_venc)));
		}
		
	}

				
		$novo_valor = number_format($novo_valor, 2, ',', '.');
		$novo_valor = str_replace('.', '', $novo_valor);
		$novo_valor = str_replace(',', '.', $novo_valor);
		$resto_conta = $valor - $novo_valor * $qtd_parcelas;
		$resto_conta = number_format($resto_conta, 2);
		
		if($i == $qtd_parcelas){
			$novo_valor = $novo_valor + $resto_conta;
		}

	//PEGAR O TELEONE DO CLIENTE
	$query2 = $pdo->query("SELECT * FROM clientes where id = '$cliente'");
	$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
	if (@count($res2) > 0) {
		$telefone_cliente = $res2[0]['telefone'];
		$nome_cliente = $res2[0]['nome'];
	} else {
		$telefone_cliente = '';
		$nome_cliente = '';
	}

	// extrair o primiero nome
	$primeiroNome = explode(" ", $nome_cliente);


	$pdo->query("INSERT INTO $tabela set descricao = '$nova_descricao', cliente = '$cliente', valor = '$novo_valor', usuario_lanc = '$id_usuario', data_lanc = curDate(), vencimento = '$novo_vencimento', frequencia = '$frequencia', forma_pgto = '$saida', arquivo = '$arquivo', pago = 'Não', referencia = '$referencia', id_ref = '$id_ref', subtotal = '$novo_valor', empresa = '$id_empresa' ");
	$id_ult_registro = $pdo->lastInsertId();


	$valorF = @number_format($valor, 2, ',', '.');

	$novo_vencimentoF = implode('/', array_reverse(explode('-', $novo_vencimento)));

	//enviar whatsapp
	if ($api_whatsapp != 'Não' and $telefone_cliente != '') {


		$telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone_cliente);

		############## AGENDAR MENSAGEM PARA RENOVAÇÃO ###########################
		$mensagem_whatsapp = '🔔_Lembrete Automático de Vencimento!_ %0A%0A';
		$mensagem_whatsapp .= $saudacao . ' *' . $primeiroNome[0] . '* tudo bem? 😀%0A%0A';
		$mensagem_whatsapp .= '_Queremos lembrar que você tem uma Conta Vencendo Hoje_ %0A%0A';
		$mensagem_whatsapp .= 'Empresa: *' . $nome_sistema . '* %0A';
		$mensagem_whatsapp .= 'Nome: *' . $nome_cliente . '* %0A';
		$mensagem_whatsapp .= 'Valor: *R$ ' . $valorF . '* %0A';
		$mensagem_whatsapp .= 'Data de Vencimento: *' . $novo_vencimentoF . '* %0A%0A';
		$mensagem_whatsapp .= '_Entre em contato conosco para acertar o pagamento!_ %0A%0A';


		if ($dados_pagamento != "") {
			$mensagem_whatsapp .= '*Dados para o Pagamento:* %0A';
			$mensagem_whatsapp .= $dados_pagamento;
		}

		$mensagem_whatsapp .= '%0A';
		$mensagem_whatsapp .= '🤖 _Esta é uma mensagem automática!_';

		$data_agd = $novo_vencimento . ' 09:00:00';
		require('../../apis/agendar.php');

		$pdo->query("UPDATE $tabela SET hash = '$hash' where id = '$id'");
	}


}

$pdo->query("DELETE from $tabela where id = '$id'");

echo 'Parcelado com Sucesso';

