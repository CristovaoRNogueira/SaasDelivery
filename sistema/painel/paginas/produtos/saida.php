<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, refaça o Login!';
	exit();
}
$tabela = 'saidas';
@session_start();
$id_usuario = $_SESSION['id'];

$id_produto = $_POST['id'];
$estoque = $_POST['estoque'];
$quantidade_saida = $_POST['quantidade_saida'];
$motivo_saida = $_POST['motivo_saida'];

if($estoque == ""){
	$estoque = 0;
}

if($quantidade_saida == ""){
	$quantidade_saida = 0;
}

$novo_estoque = $estoque - $quantidade_saida;

$query = $pdo->prepare("INSERT INTO $tabela SET produto = '$id_produto', quantidade = '$quantidade_saida', motivo = :motivo, usuario = '$id_usuario', data = curDate(), empresa = '$id_empresa' ");


$query->bindValue(":motivo", "$motivo_saida");
$query->execute();


//atualizar o total no estoque do produto
$pdo->query("UPDATE produtos SET estoque = '$novo_estoque' WHERE id = '$id_produto'");

echo 'Salvo com Sucesso';
 ?>