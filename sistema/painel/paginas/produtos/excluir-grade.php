<?php 
require_once("../../../conexao.php");
$tabela = 'grades';

$id = $_POST['id'];

$pdo->query("DELETE from $tabela where id = '$id'");
$pdo->query("DELETE from itens_grade where grade = '$id'");
echo 'Excluído com Sucesso';

?>