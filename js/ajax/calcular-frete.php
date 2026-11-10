<?php 
@session_start();
require_once('../../sistema/conexao.php');

$id_empresa = @$_SESSION['empresa'];
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, acesse novamente!';
	exit();
}
require_once("../../sistema/painel/buscar_config.php");

$bairro = $_POST['bairro'];
$total_compra = $_POST['total_compra'];
$entrega = $_POST['entrega'];



$query = $pdo->query("SELECT * FROM bairros where nome = '$bairro' and empresa = '$id_empresa' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) > 0){
$taxa_entrega = $res[0]['valor'];
}else{
	$taxa_entrega = 0;
}

if($entrega != 'Delivery'){
	$taxa_entrega = 0;	
}
$taxa_entregaF = number_format($taxa_entrega, 2, ',', '.');	

$final_compra = $total_compra + $taxa_entrega;
$final_compraF = number_format($final_compra, 2, ',', '.');	

echo $taxa_entregaF . '-'. $final_compraF;
 ?>

