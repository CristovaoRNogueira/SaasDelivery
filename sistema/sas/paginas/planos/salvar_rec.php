<?php 
$tabela = 'planos_recursos';
require_once("../../../conexao.php");


$recurso = $_POST['recurso'];
$plano = $_POST['id'];

//validacao nome
$query = $pdo->query("SELECT * from $tabela where recurso = '$recurso' and plano = '$plano'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) > 0){
	echo 'Recurso já Cadastrado para o plano!';
	exit();
}


$query = $pdo->prepare("INSERT INTO $tabela SET recurso = :recurso, plano = :plano");
$query->bindValue(":recurso", "$recurso");
$query->bindValue(":plano", "$plano");
$query->execute();

echo 'Salvo com Sucesso';
 ?>