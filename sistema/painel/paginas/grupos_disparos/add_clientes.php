<?php 
@session_start();
$id_empresa = @$_SESSION['empresa'];
require_once("../../../conexao.php");
$tabela = 'grupos_clientes';


$grupo = $_POST['grupo'];
$id = @$_POST['id'];

//validacao nome

$query = $pdo->query("SELECT * from $tabela where grupo = '$grupo' and empresa = '0' and cliente = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$id_reg = @$res[0]['id'];
if(@count($res) > 0 and $id != $id_reg){
	echo 'Cliente já Adicionado!';
	exit();
}


$query = $pdo->prepare("INSERT INTO $tabela SET grupo = :grupo, cliente = :cliente, empresa = '0' ");
$query->bindValue(":grupo", "$grupo");
$query->bindValue(":cliente", "$id");
$query->execute();

echo 'Adicionado com Sucesso';

 ?>