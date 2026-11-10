<?php
require_once("../sistema/conexao.php");


$query5 = $pdo->query("SELECT * from disparos where hora <= curTime() and data_disparo = curDate()");
$res5 = $query5->fetchAll(PDO::FETCH_ASSOC);
$linhas5 = @count($res5);
if ($linhas5 > 0) {
	for ($i5 = 0; $i5 < $linhas5; $i5++) {

		$id = $res5[$i5]['id'];
		$nome = $res5[$i5]['nome'];
		$telefone = $res5[$i5]['telefone'];
		$campanha = $res5[$i5]['campanha'];
		$data_disparo = $res5[$i5]['data_disparo'];
		$hora = $res5[$i5]['hora'];
		$id_empresa = $res5[$i5]['empresa'];	

		require("../sistema/painel/buscar_config.php");


		$telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone);
		//enviar a notificação via whatsapp



		if ($api_whatsapp != 'Não' and $telefone != '') {

			$url_envio = '';

			$query = $pdo->query("SELECT * FROM marketing where id = '$campanha'");
			$res = $query->fetchAll(PDO::FETCH_ASSOC);
			$total_reg = @count($res);
			$titulo = $res[0]['titulo'];
			$mensagem_disparo = $res[0]['mensagem'];
			$mensagem_disparo2 = $res[0]['mensagem2'];
			$arquivo = $res[0]['arquivo'];
			$audio = $res[0]['audio'];
			$documento = $res[0]['documento'];
			$envios = $res[0]['envios'];
			$dispositivo = @$res[0]['dispositivo'];

			if($dispositivo != ""){
				$query50= $pdo->query("SELECT * FROM dispositivos where telefone = '$dispositivo'");
				$res50 = $query50->fetchAll(PDO::FETCH_ASSOC);
				$token_whatsapp = @$res50[0]['appkey'];
			}

		

			if ($mensagem_disparo2 == "") {
				$mensagem_disparo2 = $mensagem_disparo;
			}


			$mensagem_disparo = rand(0, 1) ? $mensagem_disparo : $mensagem_disparo2;


			$mensagem = '';
			if($mensagem_disparo != ""){
				$mensagem .= $mensagem_disparo;
			}


			$mensagem = @str_replace('{cliente}', @$nome, @$mensagem);
			


require ("texto.php");

$url_envio = $url_sistema."sistema/painel/images/marketing/".$arquivo;
if($arquivo != "sem-foto.png"){
	require("marketing_file.php");
}

$url_envio = $url_sistema."sistema/painel/images/marketing/".$audio;
if($audio != ""){	
	require("marketing_file.php");
}

$url_envio = $url_sistema."sistema/painel/images/marketing/".$documento;
if($documento != "sem-foto.png"){	
	$mensagem = 'arquivo.pdf';
	require("marketing_file.php");
}


			if((@$status_mensagem == "Mensagem enviada com sucesso." or @$status_mensagem == "O número não existe") and $api_whatsapp == 'menuia'){
				$pdo->query("DELETE from disparos where id = '$id'");
			}

			if($api_whatsapp != 'menuia'){
				$pdo->query("DELETE from disparos where id = '$id'");
			}


		}

	}

}

echo $linhas5;