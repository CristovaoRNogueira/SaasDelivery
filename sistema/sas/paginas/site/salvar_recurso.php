<?php
$tabela = 'recursos_site';
require_once("../../../conexao.php");

@session_start();
$id_usuario = @$_SESSION['id'];


$id = @$_POST['id'];
$titulo_recurso = @$_POST['titulo_recurso'];
$descricao_recurso = @$_POST['descricao_recurso'];
$posicao_recurso = @$_POST['posicao_recurso'];
$icone_recurso = @$_POST['icone_recurso'];
$empresa = @$_POST['empresa'];

if($titulo_recurso == ""){
	echo 'Preencha o Título para o recurso!';
	exit();
}

if($descricao_recurso == ""){
	echo 'Preencha a Descrição para o recurso!';
	exit();
}



if ($id == "") {
	$query = $pdo->prepare("INSERT INTO $tabela SET titulo_recurso = :titulo_recurso, descricao_recurso = :descricao_recurso, posicao_recurso = :posicao_recurso, icone_recurso = :icone_recurso, empresa = '$empresa'");

} else {
	$query = $pdo->prepare("UPDATE $tabela SET titulo_recurso = :titulo_recurso, descricao_recurso = :descricao_recurso, posicao_recurso = :posicao_recurso, icone_recurso = :icone_recurso where id = '$id'");
}
$query->bindValue(":titulo_recurso", $titulo_recurso);
$query->bindValue(":descricao_recurso", $descricao_recurso);
$query->bindValue(":posicao_recurso", $posicao_recurso);
$query->bindValue(":icone_recurso", $icone_recurso);
$query->execute();

echo 'Salvo com Sucesso';
