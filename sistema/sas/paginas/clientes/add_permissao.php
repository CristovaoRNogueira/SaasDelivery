<?php 
require_once("../../../conexao.php");

$id_usuario = $_POST['usuario'];
$id_permissao = $_POST['id'];

$query = $pdo->query("SELECT * FROM clientes_recursos where recurso = '$id_permissao' and empresa = '$id_usuario'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){
	$pdo->query("DELETE FROM clientes_recursos where recurso = '$id_permissao' and empresa = '$id_usuario'");
}else{
	$pdo->query("INSERT INTO clientes_recursos SET recurso = '$id_permissao', empresa = '$id_usuario'");
}

?>