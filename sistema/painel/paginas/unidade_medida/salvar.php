<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, refaça o Login!';
	exit();
}
$tabela = 'unidade_medida';


$nome = $_POST['nome'];
$unidade = $_POST['unidade'];
$id = $_POST['id'];

//validacao 
$query = $pdo->query("SELECT * from $tabela where nome = '$nome' and empresa = '$id_empresa' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$id_reg = @$res[0]['id'];
if(@count($res) > 0 and $id != $id_reg){
	echo 'Nome já Cadastrado!';
	exit();
}



if($id == ""){
$query = $pdo->prepare("INSERT INTO $tabela SET nome = :nome, unidade = :unidade, empresa = '$id_empresa' ");
	
}else{
$query = $pdo->prepare("UPDATE $tabela SET nome = :nome, unidade = :unidade where id = '$id'");
}
$query->bindValue(":nome", "$nome");
$query->bindValue(":unidade", "$unidade");

$query->execute();

echo 'Salvo com Sucesso';


 ?>
