<?php 
require_once("../../../conexao.php");
$tabela = 'vendas';

$id = $_POST['id'];

//remover os itens da venda do estoque
$query = $pdo->query("SELECT * FROM carrinho where pedido = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
for($i=0; $i < $total_reg; $i++){
	$id_produto = $res[$i]['produto'];
	$quantidade = $res[$i]['quantidade'];

	$query2 = $pdo->query("SELECT * FROM produtos where id = '$id_produto'");
	$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
	$total_reg2 = @count($res2);
	$estoque = @$res2[0]['estoque'];
	$tem_estoque = @$res2[0]['tem_estoque'];

	if($tem_estoque == 'Sim'){
		$novo_estoque = $estoque + $quantidade;
		$pdo->query("UPDATE produtos SET estoque = '$novo_estoque' where id = '$id_produto'");
	}
}

$pdo->query("UPDATE $tabela SET status = 'Cancelado' where id = '$id'");
echo 'Excluído com Sucesso';

