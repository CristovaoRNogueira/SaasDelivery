<?php 
$tabela = 'usuarios';
require_once("../../../conexao.php");

$id = $_POST['id'];

$senha = '123';
$senha_crip = password_hash($senha, PASSWORD_DEFAULT);

$pdo->query("UPDATE $tabela SET senha_crip = '$senha_crip' WHERE id = '$id' ");
echo 'Redefinido com Sucesso';
?>