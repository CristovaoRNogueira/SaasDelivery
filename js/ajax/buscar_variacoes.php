<?php 
require_once('../../sistema/conexao.php');

$id = $_POST['id'];

$query = $pdo->query("SELECT * FROM variacoes_cat where id = '$id' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$sabores = $res[0]['sabores'];
echo $sabores;


?>