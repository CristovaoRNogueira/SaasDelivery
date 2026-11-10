<?php 
@session_start();
require_once('../../sistema/conexao.php');

$id = filter_var(@$_POST['id'], @FILTER_SANITIZE_STRING);
$obs = filter_var(@$_POST['obs'], @FILTER_SANITIZE_STRING);

$query = $pdo->prepare("UPDATE carrinho set obs = :obs WHERE id = :id"); 
$query->bindValue(":obs", "$obs");
$query->bindValue(":id", "$id");
$query->execute();

echo 'Salvo com Sucesso';

 ?>