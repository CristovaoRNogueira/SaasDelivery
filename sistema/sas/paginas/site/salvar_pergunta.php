<?php
$tabela = 'perguntas_site';
require_once("../../../conexao.php");

@session_start();
$id_usuario = @$_SESSION['id'];


$id = @$_POST['id'];
$titulo_pergunta = @$_POST['titulo_pergunta'];
$descricao_pergunta = @$_POST['descricao_pergunta'];
$posicao_pergunta = @$_POST['posicao_pergunta'];
$empresa = @$_POST['empresa'];

if($titulo_pergunta == ""){
	echo 'Preencha o Título para o recurso!';
	exit();
}

if($descricao_pergunta == ""){
	echo 'Preencha a Descrição para o recurso!';
	exit();
}



if ($id == "") {
	$query = $pdo->prepare("INSERT INTO $tabela SET titulo_pergunta = :titulo_pergunta, descricao_pergunta = :descricao_pergunta, posicao_pergunta = :posicao_pergunta, empresa = '$empresa'");

} else {
	$query = $pdo->prepare("UPDATE $tabela SET titulo_pergunta = :titulo_pergunta, descricao_pergunta = :descricao_pergunta, posicao_pergunta = :posicao_pergunta where id = '$id'");
}
$query->bindValue(":titulo_pergunta", $titulo_pergunta);
$query->bindValue(":descricao_pergunta", $descricao_pergunta);
$query->bindValue(":posicao_pergunta", $posicao_pergunta);
$query->execute();

echo 'Salvo com Sucesso';
