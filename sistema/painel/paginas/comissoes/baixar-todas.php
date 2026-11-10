<?php 
require_once("../../../conexao.php");
$tabela = 'pagar';
@session_start();
$id_usuario = $_SESSION['id'];

$dataInicial = @$_POST['data_inicial'];
$dataFinal = @$_POST['data_final'];
$funcionario = @$_POST['id_funcionario'];

//verificar caixa aberto
$query1 = $pdo->query("SELECT * from caixas where operador = '$id_usuario' and data_fechamento is null order by id desc limit 1");
$res1 = $query1->fetchAll(PDO::FETCH_ASSOC);
if(@count($res1) > 0){
	$id_caixa = @$res1[0]['id'];
}else{
	$id_caixa = 0;
}
// 

if($dataInicial == ""){
	$dataInicial = date('Y-m-d');
}

if($dataFinal == ""){
	$dataFinal = date('Y-m-d');
}

$pdo->query("UPDATE $tabela SET pago = 'Sim', usuario_pgto = '$id_usuario', data_pgto = curDate(), caixa = '$id_caixa' where data_lanc >= '$dataInicial' and data_lanc <= '$dataFinal' and pago = 'Não' and funcionario LIKE '$funcionario' and referencia = 'Comissão'");

echo 'Baixado com Sucesso';
 ?>