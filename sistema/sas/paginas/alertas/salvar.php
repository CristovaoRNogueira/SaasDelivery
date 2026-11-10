<?php
$tabela = 'alertas_sas';
require_once("../../../conexao.php");


$titulo = $_POST['titulo'];
$texto = $_POST['texto'];
$id = $_POST['id'];


if ($id == "") {
	$query = $pdo->prepare("INSERT INTO $tabela SET titulo = :titulo, texto = :texto, data = curDate(), ativo = 'Sim' ");
} else {
	$query = $pdo->prepare("UPDATE $tabela SET titulo = :titulo, texto = :texto where id = '$id'");
}
$query->bindValue(":titulo", "$titulo");
$query->bindValue(":texto", "$texto");
$query->execute();

echo 'Salvo com Sucesso';
?>