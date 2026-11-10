<?php
require_once ("../../../../conexao.php");
$table = 'vendas';

@session_start();
$id_usuario = @$_SESSION['id'];

$data_hoje = date('Y-m-d');

$id = $_POST['id'];


$pdo->query("UPDATE entregadores SET pago = 'Sim', status = 'Finalizado', data = curDate() where id = '$id'");

$pdo->query("UPDATE $table SET status = 'Finalizado', pago = 'Sim', usuario_baixa = $id_usuario where id = '$id'");



echo 'Alterado com Sucesso';