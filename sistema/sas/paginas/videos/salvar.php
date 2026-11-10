<?php 
$tabela = 'videos';
require_once("../../../conexao.php");

$titulo = $_POST['titulo'];
$link = $_POST['link'];
$id = $_POST['id'];


if($id == ""){
$query = $pdo->prepare("INSERT INTO $tabela SET titulo = :titulo, link = :link");
	
}else{
$query = $pdo->prepare("UPDATE $tabela SET titulo = :titulo, link = :link where id = '$id'");
}
$query->bindValue(":titulo", "$titulo");
$query->bindValue(":link", "$link");
$query->execute();

echo 'Salvo com Sucesso';
 ?>