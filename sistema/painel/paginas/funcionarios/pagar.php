<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, refaça o Login!';
	exit();
}
$tabela = 'pagar';

@session_start();
$id_usuario = $_SESSION['id'];

$valor = $_POST['total'];
$data = $_POST['data'];
$funcionario = $_POST['id_func'];

$data_atual = date('Y-m-d');

if(@strtotime($data) <= @strtotime($data_atual)){
	$data_pgto = $data;
	$pago = 'Sim';
	$usuario_pgto = $id_usuario;
}else{
	$data_pgto = '';
	$pago = 'Não';
	$usuario_pgto = '';
}

$query = $pdo->prepare("INSERT INTO $tabela SET descricao = 'Pagamento Entregador', tipo = 'Entregador', valor = :valor, data_lanc = curDate(), vencimento = '$data', data_pgto = '$data_pgto', usuario_lanc = '$id_usuario', usuario_pgto = '$usuario_pgto', foto = 'sem-foto.jpg', pessoa = '', pago = '$pago', funcionario = '$funcionario', subtotal = :valor, empresa = '$id_empresa'");

$query->bindValue(":valor", "$valor");
$query->execute();


echo 'Salvo com Sucesso';


$pdo->query("DELETE from $tabela where referencia = 'Comissão' and funcionario = '$funcionario' and pago = 'Não'");

$pdo->query("UPDATE vendas SET pago_entregador = 'Sim' where entregador = '$funcionario' and pago_entregador = 'Não' and (arquivado IS NULL or arquivado != 'Sim')");

?>
