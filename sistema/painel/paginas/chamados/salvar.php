<?php
@session_start();
$id_empresa = @$_SESSION['empresa'];
$tabela = 'chamados';
require_once("../../../conexao.php");
require_once("../../buscar_config.php");

$id_usuario = @$_SESSION['id'];


$id = @$_POST['id'];
$titulo = @$_POST['titulo'];
$texto = @$_POST['texto'];

if($texto == ""){
	echo 'Preencha o texto com a descrição do chamado!';
	exit();
}

if ($id == "") {
	$query = $pdo->prepare("INSERT INTO $tabela SET titulo = :titulo, texto = :texto, data = curDate(), empresa = '$id_empresa', status = 'Aberto', respondido = 'Não'");
} else {
	$query = $pdo->prepare("UPDATE $tabela SET titulo = :titulo, texto = :texto where id = '$id'");
}
$query->bindValue(":titulo", "$titulo");
$query->bindValue(":texto", "$texto");
$query->execute();

echo 'Salvo com Sucesso';
