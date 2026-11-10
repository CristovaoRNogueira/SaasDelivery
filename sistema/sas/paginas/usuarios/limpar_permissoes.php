<?php 
require_once("../../../conexao.php");

$id_usuario = $_POST['id_usuario'];

$pdo->query("DELETE FROM usuarios_permissoes_sas where usuario = '$id_usuario'");

?>