<?php 
$tabela = 'planos';
require_once("../../../conexao.php");


$nome = $_POST['nome'];
$valor = $_POST['valor'];
$clientes = $_POST['clientes'];
$usuarios = $_POST['usuarios'];
$dispositivos = $_POST['dispositivos'];

$valor = str_replace('.', '', $valor);
$valor = str_replace(',', '.', $valor);

$id = $_POST['id'];

if($clientes == ""){
	$clientes = 0;
}

if($usuarios == ""){
	$usuarios = 0;
}

//validacao nome
$query = $pdo->query("SELECT * from $tabela where nome = '$nome'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$id_reg = @$res[0]['id'];
if(@count($res) > 0 and $id != $id_reg){
	echo 'Nome já Cadastrado para o plano!';
	exit();
}


if($id == ""){
$query = $pdo->prepare("INSERT INTO $tabela SET nome = :nome, valor = :valor, clientes = :clientes, usuarios = :usuarios, dispositivos = :dispositivos");
	
}else{
$query = $pdo->prepare("UPDATE $tabela SET nome = :nome, valor = :valor, clientes = :clientes, usuarios = :usuarios, dispositivos = :dispositivos where id = '$id'");
}
$query->bindValue(":nome", "$nome");
$query->bindValue(":valor", "$valor");
$query->bindValue(":clientes", "$clientes");
$query->bindValue(":usuarios", "$usuarios");
$query->bindValue(":dispositivos", "$dispositivos");
$query->execute();

echo 'Salvo com Sucesso';
 ?>