<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
$tabela = 'anotacoes';
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, refaça o Login!';
	exit();
}


$id_usuario = @$_SESSION['id'];


$id = @$_POST['id'];
$titulo = @$_POST['titulo'];
$msg = @$_POST['msg'];
$mostrar_home = @$_POST['mostrar_home'];
$privado = @$_POST['privado'];


if ($id == "") {
	$query = $pdo->prepare("INSERT INTO $tabela SET titulo = :titulo, msg = :msg, usuario = '$id_usuario', data = curDate(), mostrar_home = '$mostrar_home', privado = '$privado', empresa = '$id_empresa' ");
} else {
	$query = $pdo->prepare("UPDATE $tabela SET titulo = :titulo, msg = :msg, usuario = '$id_usuario', mostrar_home = '$mostrar_home', privado = '$privado' where id = '$id'");
}
$query->bindValue(":titulo", "$titulo");
$query->bindValue(":msg", "$msg");
$query->execute();

echo 'Salvo com Sucesso';
