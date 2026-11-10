<?php 
@session_start();
require_once('../../sistema/conexao.php');
require_once('../../sistema/pedido_financeiro.php');

$id_empresa = (int) (@$_SESSION['empresa']);
if ($id_empresa <= 0) {
	echo 'Sem Sessão da Empresa, acesse novamente!';
	exit();
}

$id = $_POST['id'];
$id_sabor = @$_POST['id_sabor'];
$sessao = @$_SESSION['sessao_usuario'];

$id_edicao = (int) (@$_SESSION['id_edicao']);
if ($id_edicao > 0 && !pedidoPermiteEdicao($pdo, $id_edicao, $id_empresa)) {
	echo 'Este pedido já saiu para entrega e não pode mais ser editado!';
	exit();
}

$query = $pdo->query("SELECT * FROM carrinho where id = '$id' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$produto = $res[0]['produto'];
$quantidade = $res[0]['quantidade'];
$total_item = $res[0]['total_item'];


$query = $pdo->query("SELECT * FROM produtos where id = '$produto' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$estoque = @$res[0]['estoque'];
$tem_estoque = @$res[0]['tem_estoque'];

$pdo->query("DELETE FROM carrinho WHERE id = '$id'");
$pdo->query("DELETE from temp where carrinho = '$id'");



if($id_sabor > 0){
$pdo->query("DELETE FROM carrinho where sessao = '$sessao' and (item = '$id_sabor' or id_sabor = '$id_sabor')");
}


if ($id_edicao > 0) {

	//adicionar o total item na tabela de vendas
	$query = $pdo->query("SELECT * FROM vendas where id = '$id_edicao'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$valor_venda = @$res[0]['valor'];
	$total_venda = $valor_venda - ($total_item * $quantidade);
	sincronizarFinanceiroPedidoEditado($pdo, $id_edicao, $id_empresa, $total_venda);
	
	}

echo 'Excluido com Sucesso';

 ?>
