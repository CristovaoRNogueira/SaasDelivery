<?php 
$tabela = 'planos';
require_once("../../../conexao.php");

$id = $_POST['id'];

$pdo->query("DELETE FROM $tabela WHERE id = '$id' ");
$pdo->query("DELETE FROM planos_itens WHERE plano = '$id' ");
$pdo->query("DELETE FROM planos_recursos WHERE plano = '$id' ");

echo 'Excluído com Sucesso';
?>