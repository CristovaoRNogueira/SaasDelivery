<?php 
$tabela = 'receber_sas';
require_once("../../../conexao.php");

$id = $_POST['id'];

$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$descricao = @$res[0]['descricao'];
$valor = @$res[0]['valor'];
$cliente = @$res[0]['cliente'];
$vencimento = @$res[0]['vencimento'];


//CODIGO PARA CALCULAR OS JUROS
$valor_multa = 0;
$valor_juros = 0;
$data_hoje = date('Y-m-d');

if (@strtotime($vencimento) < @strtotime($data_hoje)) {
	
	$valor_multa = $multa_atraso;

			//pegar a quantidade de dias que o pagamento está atrasado
	$dif = @strtotime($data_hoje) - @strtotime($vencimento);
	$dias_vencidos = floor($dif / (60 * 60 * 24));

	$valor_juros = ($valor * $juros_atraso / 100) * $dias_vencidos;
}

$valor = $valor_multa + $valor_juros + $valor;
$valor_multa_juros = $valor_multa + $valor_juros;

$valor_multa_jurosF = @number_format($valor_multa_juros, 2, ',', '.');
$valor_jurosF = @number_format($valor_juros, 2, ',', '.');

$data_atual = date('Y-m-d');

$vencimentoF = implode('/', array_reverse(@explode('-', $vencimento)));
$valorF = @number_format($valor, 2, ',', '.');


$query2 = $pdo->query("SELECT * FROM empresas where id = '$cliente'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
if(@count($res2) > 0){
	$nome_cliente = $res2[0]['nome'];
	$telefone_cliente = $res2[0]['telefone'];
}else{
	$nome_cliente = 'Sem Registro';
	$telefone_cliente = "";
}

$link_pgto = $url_sistema.'pagamento/'.$id;

if($api_whatsapp != 'Não' and $telefone_cliente != ''){			

			$telefone_envio = '55'.preg_replace('/[ ()-]+/' , '' , $telefone_cliente);
			$mensagem = '*'.$nome_sistema.'*%0A';
			if(strtotime($vencimento) < strtotime($data_atual)){
				$mensagem .= '_Conta Vencida_ %0A';
			}else{
				$mensagem .= '_Lembrete de Pagamento_ %0A';
			}
			
			$mensagem .= '*Descrição:* '.$descricao.' %0A';
			if($valor_multa_juros > 0){
				$mensagem .= '*Multa / Júros:* '.$valor_multa_jurosF.' %0A';		
			}
			$mensagem .= '*Valor:* '.$valorF.' %0A';	
			$mensagem .= '*Vencimento:* '.$vencimentoF.' %0A%0A';	

			if($dados_pagamento != ""){
				$mensagem .= '*Dados para o Pagamento:* %0A';
				$mensagem .= $dados_pagamento;
			}else{
				if($access_token_mp != "" || $chave_api_asaas != ""){
					$mensagem .= $link_pgto;
				}
			}		
			
			require('../../apis/texto.php');			
			
		}	

echo 'Cobrança Efetuada';
?>