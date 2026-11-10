<?php
@session_start();
$id_empresa = $_SESSION['empresa'];

require_once("../../../conexao.php");
require_once("../../buscar_config.php");
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, refaça o Login!';
	exit();
}
$tabela = 'bordas';

$nome = $_POST['nome'];
$produto = $_POST['id'];
$valor = $_POST['valor'];
$valor = str_replace(',', '.', $valor);
$id_borda_editar = $_POST['id_borda_editar'];



if($id_borda_editar == ""){
$query = $pdo->prepare("INSERT INTO $tabela SET nome = :nome, valor = :valor, categoria = '$produto', ativo = 'Sim', empresa = '$id_empresa'");
}else{
	$query = $pdo->prepare("UPDATE $tabela SET nome = :nome, valor = :valor, categoria = '$produto' WHERE id = '$id_borda_editar'");
}


$query->bindValue(":nome", "$nome");
$query->bindValue(":valor", "$valor");
$query->execute();

echo 'Salvo com Sucesso';