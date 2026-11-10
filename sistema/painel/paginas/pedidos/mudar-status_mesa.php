<?php
require_once("../../../conexao.php");
$tabela = 'carrinho';

$id = $_POST['id'];
$acao = $_POST['acao'];

$pdo->query("UPDATE $tabela SET status = '$acao' where id = '$id'");

echo 'Alterado com Sucesso***';
