<?php
require_once("../../../conexao.php");
$id = @$_POST['id'];


$pdo->query("DELETE from disparos where campanha = '$id'");
echo 'Parado com Sucesso';
?>