<?php 
require_once("../../../conexao.php");
$tabela = 'itens_grade';

$id = $_POST['id'];

$pdo->query("DELETE from $tabela where id = '$id'");
echo 'Excluído com Sucesso';

?>