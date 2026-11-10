<?php
$tabela = 'anotacoes';
require_once("../../../conexao.php");

@session_start();
$id_usuario = @$_SESSION['id'];


$id = @$_POST['id'];
$titulo = @$_POST['titulo'];
$msg = @$_POST['msg'];
$mostrar_home = @$_POST['mostrar_home'];
$privado = @$_POST['privado'];


if ($id == "") {
	$query = $pdo->prepare("INSERT INTO $tabela SET titulo = :titulo, msg = :msg, usuario = '$id_usuario', data = curDate(), mostrar_home = '$mostrar_home', privado = '$privado', empresa = 0");
} else {
	$query = $pdo->prepare("UPDATE $tabela SET titulo = :titulo, msg = :msg, usuario = '$id_usuario', mostrar_home = '$mostrar_home', privado = '$privado' where id = '$id'");
}
$query->bindValue(":titulo", "$titulo");
$query->bindValue(":msg", "$msg");
$query->execute();

echo 'Salvo com Sucesso';
