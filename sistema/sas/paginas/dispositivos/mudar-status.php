<?php 
$tabela = 'config';
require_once("../../../conexao.php");

$id = $_POST['id'];
$appkey = $_POST['acao'];


$pdo->query("UPDATE $tabela SET token_whatsapp = '$appkey' WHERE empresa = 0 ");
echo 'Alterado com Sucesso';
?>