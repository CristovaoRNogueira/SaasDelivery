<?php 
require_once("../../../conexao.php");
$tabela = 'adicionais';

$id = $_POST['id'];
$acao = $_POST['acao'];

$pdo->query("UPDATE $tabela SET ativo = '$acao' where id = '$id'");
$pdo->query("UPDATE itens_grade SET ativo = '$acao' where adicional = '$id'");
echo 'Alterado com Sucesso';

?>