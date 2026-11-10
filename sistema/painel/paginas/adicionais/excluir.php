<?php 
require_once("../../../conexao.php");
$tabela = 'adicionais';

$id = $_POST['id'];

$pdo->query("DELETE from $tabela where id = '$id'");
$pdo->query("DELETE from itens_grade where adicional = '$id'");
echo 'Excluído com Sucesso';

?>