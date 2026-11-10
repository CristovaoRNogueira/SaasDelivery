<?php 
require_once("../../../conexao.php");
$adicional = $_POST['adicional'];


$query = $pdo->query("SELECT * FROM adicionais where id = '$adicional'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
echo @$res[0]['valor'];

?>