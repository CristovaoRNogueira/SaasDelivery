<?php 
require_once("../../../conexao.php");

$desconto = $_POST['desconto'];
$tipo_desconto = $_POST['tipo_desconto'];
$total_mesa = $_POST['total_mesa'];

if($desconto == ""){
	$desconto = 0;
}

if($tipo_desconto == "%"){
	$total = $total_mesa - ($desconto * $total_mesa / 100);
}else{
	$total = $total_mesa - $desconto;
}

$totalF = @number_format($total, 2, ',', '.');
echo $totalF;

?>