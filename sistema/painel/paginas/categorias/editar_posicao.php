<?php 
require_once("../../../conexao.php");
$tabela = 'categorias';

$id = $_POST['id'];
$posicao = $_POST['posicao'];


$pdo->query("UPDATE $tabela SET posicao = '$posicao' where id = '$id'");
echo 'Editado com Sucesso';

?>