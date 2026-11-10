<?php 
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, refaça o Login!';
	exit();
}
$tabela = 'caixas';


$id_usuario = @$_SESSION['id'];

$operador = $_POST['operador'];
$valor_abertura = $_POST['valor_abertura'];
$valor_abertura = str_replace(',', '.', $valor_abertura);
$data_abertura = $_POST['data_abertura'];

$obs = $_POST['obs'];

$id = $_POST['id'];

$query_aberto = $pdo->prepare("SELECT id FROM caixas WHERE operador = :operador AND empresa = :empresa AND data_fechamento IS NULL AND id != :id LIMIT 1");
$query_aberto->execute([
	':operador' => (int) $operador,
	':empresa' => (int) $id_empresa,
	':id' => (int) ($id ?: 0)
]);
if ($query_aberto->fetchColumn()) {
	echo 'Este operador já possui um caixa aberto. Feche o caixa atual antes de abrir outro.';
	exit();
}

if($id == ""){
	$query = $pdo->prepare("INSERT INTO $tabela SET operador = '$operador', data_abertura = '$data_abertura', hora_abertura = CURTIME(), valor_abertura = :valor_abertura, usuario_abertura = '$id_usuario', obs = :obs, empresa = '$id_empresa'");	

}else{

	$query = $pdo->prepare("UPDATE $tabela SET operador = '$operador', data_abertura = '$data_abertura', valor_abertura = :valor_abertura, usuario_abertura = '$id_usuario', obs = :obs where id = '$id' and empresa = '$id_empresa'");

}



$query->bindValue(":valor_abertura", "$valor_abertura");
$query->bindValue(":obs", "$obs");

$query->execute();

echo 'Salvo com Sucesso'; 

?>
