<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, refaça o Login!';
	exit();
}
$tabela = 'variacoes_cat';

$sigla = $_POST['sigla'];
$nome = $_POST['nome'];
$descricao = $_POST['descricao'];
$produto = $_POST['id'];
$sabores = $_POST['sabores'];
$id_var_editar = $_POST['id_var_editar'];

$sigla = str_replace(' ', '', $sigla);

if($id_var_editar == ""){
$query = $pdo->prepare("INSERT INTO $tabela SET nome = :nome, categoria = '$produto', sigla = :sigla,  descricao = :descricao,  sabores = '$sabores', empresa = '$id_empresa' ");
}else{
		$query = $pdo->prepare("UPDATE $tabela SET nome = :nome, categoria = '$produto', sigla = :sigla,  descricao = :descricao,  sabores = '$sabores' WHERE id = '$id_var_editar'");
}


$query->bindValue(":nome", "$nome");
$query->bindValue(":descricao", "$descricao");
$query->bindValue(":sigla", "$sigla");
$query->execute();

echo 'Salvo com Sucesso';