<?php 
$tabela = 'receber';
require_once("../../../conexao.php");

$valor = $_POST['valor'];
$pgto = $_POST['pgto'];

$valor = str_replace(',', '.', str_replace('.', '', $valor));

$query = $pdo->query("SELECT * FROM formas_pgto where id = '$pgto'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$taxa = @$res[0]['taxa'];

if($taxa == ""){
	$taxa == 0;
}

$taxa_conta = $taxa * $valor / 100;
$taxa_conta = @number_format($taxa_conta, 2, ',', '.');

echo $taxa_conta;
?>