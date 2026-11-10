<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, refaça o Login!';
	exit();
}
$tabela = 'variacoes';

$variacao = $_POST['variacao'];

$query = $pdo->query("SELECT * from variacoes_cat where id = '$variacao'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);

$sigla = $res[0]['sigla'];
$nome = $res[0]['nome'];
$valor = $_POST['valor'];
$valor = str_replace(',', '.', $valor);
$descricao = $res[0]['descricao'];
$produto = $_POST['id'];

$id_editar = $_POST['id_item'];

$sigla = str_replace(' ', '', $sigla);

if($id_editar == ""){
	//validar sigla
	$query = $pdo->query("SELECT * from $tabela where sigla = '$sigla' and produto = '$produto'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	if(@count($res) > 0){
		echo 'Valor Já Cadastrado para essa variação';
		exit();
	}
}

if($id_editar == ""){
	$query = $pdo->prepare("INSERT INTO $tabela SET nome = :nome, produto = '$produto', sigla = :sigla, valor = :valor, descricao = :descricao, ativo = 'Sim', empresa = '$id_empresa' ");
}else{
	$query = $pdo->prepare("UPDATE $tabela SET nome = :nome, produto = '$produto', sigla = :sigla, valor = :valor, descricao = :descricao WHERE id = '$id_editar'");
}



$query->bindValue(":nome", "$nome");
$query->bindValue(":valor", "$valor");
$query->bindValue(":descricao", "$descricao");
$query->bindValue(":sigla", "$sigla");
$query->execute();

echo 'Salvo com Sucesso';