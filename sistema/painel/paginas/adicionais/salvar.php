<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
$tabela = 'adicionais';
require_once("../../../conexao.php");
require_once("../../buscar_config.php");

if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, refaça o Login!';
	exit();
}

$id = $_POST['id'];
$nome = $_POST['nome'];
$valor = $_POST['valor'];

if($valor == ""){
    $valor = 0;
}
$valor = str_replace(',', '.', $valor);


//validar nome
$query = $pdo->query("SELECT * from $tabela where nome = '$nome' and empresa = '$id_empresa' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) > 0 and $id != $res[0]['id']){
	echo 'Adicional já Cadastrado, escolha outro!!';
	exit();
}


if($id == ""){
	$query = $pdo->prepare("INSERT INTO $tabela SET nome = :nome, valor = :valor, ativo = 'Sim', empresa = '$id_empresa' ");
}else{
	$query = $pdo->prepare("UPDATE $tabela SET nome = :nome, valor = :valor WHERE id = '$id'");
	$pdo->query("UPDATE itens_grade SET valor = '$valor', texto = '$nome' where adicional = '$id'");
}

$query->bindValue(":nome", "$nome");
$query->bindValue(":valor", "$valor");
$query->execute();

echo 'Salvo com Sucesso';

?>