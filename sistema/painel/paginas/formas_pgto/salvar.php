<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, refaça o Login!';
	exit();
}
$tabela = 'formas_pgto';

$id = $_POST['id'];
$nome = $_POST['nome'];
$taxa = $_POST['taxa'];

if($taxa == ""){
	$taxa = 0;
}

//validar nome
$query = $pdo->query("SELECT * from $tabela where nome = '$nome' and empresa = '$id_empresa' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) > 0 and $id != $res[0]['id']){
	echo 'Nome já Cadastrado, escolha outro!!';
	exit();
}


if($id == ""){
	$query = $pdo->prepare("INSERT INTO $tabela SET nome = :nome, taxa = '$taxa', empresa = '$id_empresa' ");
}else{
	$query = $pdo->prepare("UPDATE $tabela SET nome = :nome, taxa = '$taxa' WHERE id = '$id'");
}

$query->bindValue(":nome", "$nome");
$query->execute();

echo 'Salvo com Sucesso';
 ?>