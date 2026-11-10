<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, refaça o Login!';
	exit();
}

$tabela = 'grades';

$texto = $_POST['texto'];
$tipo_item = $_POST['tipo_item'];
$valor_item = $_POST['valor_item'];
$limite = $_POST['limite'];
$produto = $_POST['id'];
$nome_comprovante = $_POST['nome_comprovante'];
$adicional = $_POST['adicional'];
$id_grade_editar = $_POST['id_grade_editar'];
$valor_item = str_replace(',', '.', $valor_item);

if($id_grade_editar == ""){
	$query = $pdo->prepare("INSERT INTO $tabela SET produto = '$produto', tipo_item = :tipo_item, valor_item = :valor_item, texto = :texto, limite = :limite, ativo = 'Sim', nome_comprovante = :nome_comprovante, adicional = '$adicional', empresa = '$id_empresa' ");
}else{
	$query = $pdo->prepare("UPDATE $tabela SET tipo_item = :tipo_item, valor_item = :valor_item, texto = :texto, limite = :limite, nome_comprovante = :nome_comprovante, adicional = '$adicional' WHERE id = '$id_grade_editar'");
}



$query->bindValue(":tipo_item", "$tipo_item");
$query->bindValue(":valor_item", "$valor_item");
$query->bindValue(":texto", "$texto");
$query->bindValue(":limite", "$limite");
$query->bindValue(":nome_comprovante", "$nome_comprovante");
$query->execute();

echo 'Salvo com Sucesso';