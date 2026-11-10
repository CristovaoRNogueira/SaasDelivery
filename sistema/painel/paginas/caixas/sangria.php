<?php 

$tabela = 'sangrias';

require_once("../../../conexao.php");

@session_start();

$id_usuario = $_SESSION['id'];

$data_atual = 'Y-m-d';

$id = $_POST['id'];
$valor_sangria = $_POST['valor_sangria'];
$nome_operador_sangria = $_POST['nome_operador_sangria'];



$pdo->query("INSERT $tabela SET usuario = '$id_usuario', valor = '$valor_sangria', data = curDate(), hora = curTime(), caixa = '$id', feito_por = $nome_operador_sangria");


echo 'Salvo com Sucesso';



?>



