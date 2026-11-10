<?php 
$tabela = 'usuarios';
require_once("../../../conexao.php");

$id = $_POST['id'];
$acao = $_POST['acao'];

$pdo->query("UPDATE empresas SET ativo = '$acao' WHERE id = '$id' ");
$pdo->query("UPDATE $tabela SET ativo = '$acao' WHERE empresa = '$id' ");

echo 'Alterado com Sucesso';
?>