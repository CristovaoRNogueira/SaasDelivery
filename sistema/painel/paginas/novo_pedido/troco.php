<?php 
require_once("../../../conexao.php");

$total_recebido = $_POST['total_recebido'];
$subtotal = $_POST['subtotal'];

if($total_recebido == ""){
	exit();
}

$subtotal = str_replace('.', '', $subtotal);
$subtotal = str_replace(',', '.', $subtotal);

$total_recebido = str_replace('.', '', $total_recebido);
$total_recebido = str_replace(',', '.', $total_recebido);

$total = $total_recebido - $subtotal;
if($total <= 0){
	$total = 0;
}
$totalF = @number_format($total, 2, ',', '.');
echo $totalF;

?>