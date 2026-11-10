<?php 
require_once("../../../conexao.php");
$tabela = 'abertura_mesa';

$mesa_troca = $_POST['mesa_troca'];
$id_abertura_troca = $_POST['id_abertura_troca'];
$id_mesa_troca = $_POST['id_mesa_troca'];

$pdo->query("UPDATE abertura_mesa SET mesa = '$mesa_troca' where id = '$id_abertura_troca'");
$pdo->query("UPDATE mesas SET status = 'Fechada' where id = '$id_mesa_troca'");
$pdo->query("UPDATE mesas SET status = 'Aberta' where id = '$mesa_troca'");

echo 'Salvo com Sucesso';

?>