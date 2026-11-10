<?php
require_once("../sistema/conexao.php");

$query = $pdo->query("SELECT * from clientes where data_mensagem <= curDate() and (retorno_enviado = 'Não' or retorno_enviado is null) and (marketing = '' or marketing is null) order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);

echo '<br>';
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$id = $res[$i]['id'];
		$nome = $res[$i]['nome'];
		$telefone = $res[$i]['telefone'];
		$id_empresa = $res[$i]['empresa'];

		require("../sistema/painel/buscar_config.php");

		$telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone);
		//enviar a notificação via whatsapp

		if ($api_whatsapp != 'Não') {
			$mensagem = '*----'.mb_strtoupper($nome_sistema).'-----* %0A%0A';
			$mensagem .= 'Olá *'.$nome.'*, %0A%0A';
			$mensagem .= $mensagem_retorno . '%0A%0A';
			$mensagem .= $link_retorno . '%0A';
			
			require ("api_texto_retorno.php");

			if(@$status_mensagem == "Mensagem enviada com sucesso." and $api_whatsapp == 'menuia'){
				$pdo->query("UPDATE clientes SET retorno_enviado = 'Sim' where id = '$id' order by id desc");
			}

			if($api_whatsapp != 'menuia'){
				$pdo->query("UPDATE clientes SET retorno_enviado = 'Sim' where id = '$id' order by id desc");
			}

			


		}


	}
}

?>