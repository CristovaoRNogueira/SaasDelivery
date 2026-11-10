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


$id = $_POST['id'];

$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$foto = @$res[0]['arquivo'];
$hash = @$res[0]['hash'];

if($foto != "sem-foto.png"){
	@unlink('../../images/contas/'.$foto);
}

$referencia_agendamento = 'pagar';
if($hash != ""){
	require("../../apis/cancelar_agendamento.php");
}

$pdo->query("DELETE FROM $tabela WHERE id = '$id' ");
echo 'Excluído com Sucesso';
?>