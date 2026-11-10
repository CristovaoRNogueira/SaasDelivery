<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, refaça o Login!';
	exit();
}
$tabela = 'adicionais_cat';

$nome = $_POST['nome'];
$produto = $_POST['id'];
$valor = $_POST['valor'];
$id_adc_editar = @$_POST['id_adc_editar'];
$valor = str_replace(',', '.', $valor);

//validar sigla
$query = $pdo->query("SELECT * from $tabela where nome = '$nome' and categoria = '$produto'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) > 0 and $id_adc_editar != $res[0]['id']){
	echo 'Adicional já Cadastrado, escolha outro!!';
	exit();
}

if($id_adc_editar == ""){
	$query = $pdo->prepare("INSERT INTO $tabela SET nome = :nome, valor = :valor, categoria = '$produto', ativo = 'Sim', empresa = '$id_empresa' ");
}else{
	$query = $pdo->prepare("UPDATE $tabela SET nome = :nome, valor = :valor WHERE id = '$id_adc_editar'");
}



$query->bindValue(":nome", "$nome");
$query->bindValue(":valor", "$valor");
$query->execute();

echo 'Salvo com Sucesso';