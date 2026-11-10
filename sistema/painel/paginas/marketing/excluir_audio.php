<?php 
require_once("../../../conexao.php");
$tabela = 'marketing';

$id = $_POST['id'];

$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
$audio = $res[0]['audio'];

@unlink('../../images/marketing/'.$audio);

$pdo->query("UPDATE $tabela SET audio = '' where id = '$id'");
echo 'Excluído com Sucesso';
 ?>