<?php 
$tabela = 'clientes';
require_once("../../../conexao.php");

$id = $_POST['id'];

$pdo->query("UPDATE $tabela SET marketing = 'Não' WHERE id = '$id' ");
echo 'Excluído com Sucesso';
?>