<?php 
$tabela = 'grupos_disparos';
require_once("../../../conexao.php");

$id = $_POST['id'];

$pdo->query("DELETE FROM grupos_clientes WHERE grupo = '$id' ");
$pdo->query("DELETE FROM $tabela WHERE id = '$id' ");


echo 'Excluído com Sucesso';
?>