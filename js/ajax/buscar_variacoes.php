<?php 
@session_start();
require_once('../../sistema/conexao.php');

$id = $_POST['id'];
$id_empresa = @$_SESSION['empresa'];

if ($id_empresa != "") {
	$query = $pdo->query("SELECT * FROM variacoes_cat where id = '$id' and empresa = '$id_empresa' ");
} else {
	$query = $pdo->query("SELECT * FROM variacoes_cat where id = '$id' ");
}
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$sabores = @$res[0]['sabores'];
echo $sabores;


?>