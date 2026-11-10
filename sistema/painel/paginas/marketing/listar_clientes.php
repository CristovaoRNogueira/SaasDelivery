<?php
@session_start();
$id_empresa = @$_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");

$dataMes = Date('m');
$dataDia = Date('d');
$dataAno = Date('Y');
$data_atual = date('Y-m-d');

$data_semana = date('Y/m/d', strtotime("-7 days", strtotime($data_atual)));

$id_usuario = $_SESSION['id'];

$clientes = $_POST['cli'];

// Buscar os Contatos que serão enviados
if ($clientes == "Teste") {
	$resultado = $pdo->query("SELECT telefone FROM config where telefone != '' and empresa = '$id_empresa' ");

} else if ($clientes == "Clientes Mês") {
	$resultado = $pdo->query("SELECT telefone FROM clientes where month(data_cad) = '$dataMes' and year(data_cad) = '$dataAno' and telefone != '' and empresa = '$id_empresa' and (marketing = '' or marketing is null) ");

} else if ($clientes == "Clientes Semana") {
	$resultado = $pdo->query("SELECT telefone FROM clientes where data_cad >= '$data_semana' and empresa = '$id_empresa' and telefone != '' and (marketing = '' or marketing is null) ");

} else if ($clientes == "Todos") {
	$resultado = $pdo->query("SELECT telefone FROM clientes where telefone != '' and empresa = '$id_empresa' and (marketing = '' or marketing is null) ");


}else if ($clientes == "Aniversariantes Mês"){
	$resultado = $pdo->query("SELECT * FROM clientes where month(data_nasc) = '$dataMes' and empresa = '$id_empresa' and telefone != '' and (marketing = '' or marketing is null)  ");	

}else if ($clientes == "Aniversariantes Dia"){
	$resultado = $pdo->query("SELECT * FROM clientes where month(data_nasc) = '$dataMes' and empresa = '$id_empresa' and day(data_nasc) = '$dataDia'  and telefone != '' and (marketing = '' or marketing is null) ");	

}else if ($clientes == "Inadimplentes"){
	$resultado = $pdo->query("SELECT DISTINCT c.nome, c.telefone, c.id FROM clientes c JOIN receber r ON c.id = r.cliente WHERE r.vencimento < CURDATE() AND r.pago = 'Não' and (r.arquivado IS NULL or r.arquivado != 'Sim') and r.empresa = '$id_empresa' ");

}else if ($clientes == "1"){
	$resultado = $pdo->query("SELECT * FROM clientes where cartoes = 1 and empresa = '$id_empresa' ");	

}else if ($clientes == "2"){
	$resultado = $pdo->query("SELECT * FROM clientes where cartoes = 2 and empresa = '$id_empresa' ");	

}else if ($clientes == "3"){
	$resultado = $pdo->query("SELECT * FROM clientes where cartoes = 3 and empresa = '$id_empresa' ");	

}else if ($clientes == "4"){
	$resultado = $pdo->query("SELECT * FROM clientes where cartoes = 4 and empresa = '$id_empresa' ");	

}else if ($clientes == "5"){
	$resultado = $pdo->query("SELECT * FROM clientes where cartoes = 5 and empresa = '$id_empresa' ");	

}else{
	$resultado = $pdo->query("SELECT * FROM grupos_clientes where grupo = '$clientes' and empresa = '$id_empresa'");	
}

$res = @$resultado->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);

echo $total_reg;

?>
