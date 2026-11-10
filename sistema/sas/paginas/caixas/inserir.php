<?php 

$tabela = 'caixas';

require_once("../../../conexao.php");

@session_start();
$id_usuario = @$_SESSION['id'];

$operador = $_POST['operador'];
$valor_abertura = $_POST['valor_abertura'];
$data_abertura = $_POST['data_abertura'];
$data_abertura = $_POST['data_abertura'];


// 2. Converte a vírgula para ponto e remove os pontos de milhar
$valor_abertura = str_replace(',', '.', str_replace('.', '', $valor_abertura));

$obs = $_POST['obs'];
$id = $_POST['id'];

if($id == ""){
//verificar se está aberto
$query2 = $pdo->query("SELECT * FROM caixas where operador = '$operador' and data_fechamento is null");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
if(@count($res2) > 0){
	echo 'O caixa já está aberto para esse usuário!';
	exit();
}
}

if($id == ""){
	$query = $pdo->prepare("INSERT INTO $tabela SET operador = '$operador', data_abertura = '$data_abertura', valor_abertura = :valor_abertura, usuario_abertura = '$id_usuario', obs = :obs, empresa = 0");	
}else{
	$query = $pdo->prepare("UPDATE $tabela SET operador = '$operador', data_abertura = '$data_abertura', valor_abertura = :valor_abertura, usuario_abertura = '$id_usuario', obs = :obs where id = '$id'");
}

$query->bindValue(":valor_abertura", "$valor_abertura");
$query->bindValue(":obs", "$obs");
$query->execute();
echo 'Salvo com Sucesso'; 

?>