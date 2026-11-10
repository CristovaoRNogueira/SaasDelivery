<?php
@session_start();
$id_usuario = @$_SESSION['id'];
$tabela = 'chamados_respostas';
require_once("../../../conexao.php");

$id = $_POST['id'];
$texto = $_POST['texto_resposta'];

$pdo->query("UPDATE chamados set respondido = 'Sim' WHERE id = '$id' ");

$query2 = $pdo->query("SELECT * FROM chamados where id = '$id'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$empresa = $res2[0]['empresa'];

$query2 = $pdo->query("SELECT * FROM empresas where id = '$empresa'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$nome_empresa = @$res2[0]['nome'];
$telefone_empresa = @$res2[0]['telefone'];

$query = $pdo->prepare("INSERT INTO $tabela SET empresa = '$empresa', data = curDate(), hora = curTime(), usuario = '$id_usuario', texto = :texto, chamado = '$id' ");

$query->bindValue(":texto", "$texto");
$query->execute();



//ENVIAR NOTIFICAÇÃO DA RESPOSTA PARA A EMPRESA
//api whats
	if ($api_whatsapp != 'Não' and $telefone_empresa != '') {
		$telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone_empresa);

		$mensagem_whatsapp = '*' . $nome_sistema . '*%0A%0A';
		$mensagem_whatsapp .= 'Seu Chamado foi Atualizado!!_ %0A%0A';
		$mensagem_whatsapp .= '*Resposta:* %0A';
		$mensagem_whatsapp .= $texto;
		$mensagem = $mensagem_whatsapp;

		require('../../apis/texto.php');



	}


echo 'Salvo com Sucesso';
