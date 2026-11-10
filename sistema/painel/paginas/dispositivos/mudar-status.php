<?php 
$tabela = 'config';
require_once("../../../conexao.php");

$id = $_POST['id'];
$appkey = $_POST['acao'];


$pdo->query("UPDATE $tabela SET token_whatsapp = '$appkey' WHERE empresa = '$id' ");
echo 'Alterado com Sucesso';
?>