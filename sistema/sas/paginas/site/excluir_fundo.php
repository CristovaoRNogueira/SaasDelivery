<?php
$tabela = 'site';
require_once("../../../conexao.php");

$empresa = $_POST['id'];

$query = $pdo->query("SELECT * FROM $tabela where empresa = '$empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {	
	$fundo_topo = $res[0]['fundo_topo'];	
	@unlink('../../../img/logos/' . $fundo_topo);
} 



$pdo->query("UPDATE $tabela SET fundo_topo = '' WHERE empresa = '$empresa' ");
echo 'Excluído com Sucesso';
?>