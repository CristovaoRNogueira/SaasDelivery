<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, refaça o Login!';
	exit();
}
$tabela = 'dias';

$id = $_POST['id'];
$dia = $_POST['dia'];

//validar email
$query = $pdo->query("SELECT * from $tabela where dia = '$dia' and empresa = '$id_empresa' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if (@count($res) > 0 and $id != $res[0]['id']) {
	echo 'Dia já Cadastrado, escolha outro!!';
	exit();
}


if ($id == "") {
	$query = $pdo->prepare("INSERT INTO $tabela SET dia = :dia, empresa = '$id_empresa' ");
} else {
	$query = $pdo->prepare("UPDATE $tabela SET dia = :dia WHERE id = '$id'");
}

$query->bindValue(":dia", "$dia");
$query->execute();

echo 'Salvo com Sucesso';

?>