<?php 
$tabela = 'usuarios';
require_once("../../../conexao.php");

if($modo_teste == 'Sim'){
	echo 'Em modo de teste esse recurso fica desabilitado!';
	exit();
}

$id = $_POST['id'];
$acao = $_POST['acao'];

$pdo->query("UPDATE $tabela SET ativo = '$acao' WHERE id = '$id' ");



$query2 = $pdo->query("SELECT * from usuarios where id = '$id'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$empresa = $res2[0]['empresa'];

if($empresa > 0){
	$pdo->query("UPDATE $tabela SET ativo = '$acao' WHERE empresa = '$empresa' ");
}


echo 'Alterado com Sucesso';
?>