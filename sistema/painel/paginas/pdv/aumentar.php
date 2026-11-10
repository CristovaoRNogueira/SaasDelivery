<?php 
$tabela = 'carrinho';
require_once("../../../conexao.php");

$id = $_POST['id'];
$quantidade = $_POST['quantidade'];

$query = $pdo->query("SELECT * from $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$id_produto = $res[0]['produto'];
$total_item = $res[0]['total_item'];
$qt = $res[0]['quantidade'];
$valor = $total_item / $qt;

$query = $pdo->query("SELECT * from produtos where id = '$id_produto'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$estoque = $res[0]['estoque'];
$tem_estoque = $res[0]['tem_estoque'];
$vendas = $res[0]['vendas'];
$unidade = $res[0]['unidade'];

$nova_quant = $quantidade + 1;
$novo_total = $valor * $nova_quant;

if($estoque <= 0 and $tem_estoque == 'Sim'){
	echo 'A quantidade vendida não pode ser maior do que a quantidade em estoque!';
	exit();
}

$pdo->query("UPDATE $tabela SET quantidade = '$nova_quant', total_item = '$novo_total' WHERE id = '$id' ");


echo 'Excluído com Sucesso';

if($tem_estoque == 'Sim'){
	$novo_estoque = $estoque - 1;
	$vendas = $vendas + 1;
	//adicionar os produtos na tabela produtos
	//$pdo->query("UPDATE produtos SET estoque = '$novo_estoque', vendas = '$vendas' WHERE id = '$id_produto'");
}
?>