<?php 
require_once("../../../conexao.php");

$id_usuario = $_POST['id_usuario'];

$pdo->query("DELETE FROM clientes_recursos where empresa = '$id_usuario'");

?>