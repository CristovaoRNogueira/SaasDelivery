<?php 
require_once("../../../conexao.php");
$tabela = 'valor_adiantamento';

$id = $_POST['id'];

$pdo->query("DELETE from $tabela where id = '$id'");
$pdo->query("DELETE from receber where adiantamento = '$id'");
echo 'Excluído com Sucesso';

