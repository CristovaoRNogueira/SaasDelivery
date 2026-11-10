<?php 
$tabela = 'videos';
require_once("../../../conexao.php");

$id = $_POST['id'];
$posicao = $_POST['posicao'];

$pdo->query("UPDATE $tabela SET ordem = '$posicao' WHERE id = '$id' ");
echo 'Editado com Sucesso';
?>