<?php 
@session_start();
require_once("../../../conexao.php");

$acao = $_POST['acao'] ?? '';

if($acao == ''){
	echo 'Ação não informada!';
	exit();
}

if($acao == 'pausar'){
	$pausar_delivery = 'Sim';
}else{
	$pausar_delivery = 'Não';
}

$query = $pdo->prepare("UPDATE config SET pausar_delivery = :pausar_delivery");
$query->bindValue(":pausar_delivery", $pausar_delivery);
$query->execute();

echo 'Alterado com Sucesso';
?>