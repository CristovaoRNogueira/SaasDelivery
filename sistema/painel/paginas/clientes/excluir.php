<?php 
$tabela = 'clientes';
require_once("../../../conexao.php");

$id = $_POST['id'];


$query = $pdo->query("SELECT * FROM receber where cliente = '$id' and pago = 'Não' and (arquivado IS NULL or arquivado != 'Sim')");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if (@count($res) > 0) {
  echo 'Você não pode excluir este cliente, existem Valores a receber associadas a ele, primeiro exclua a conta e depois exclua o cliente!';
  exit();
}

$pdo->query("DELETE FROM $tabela WHERE id = '$id' ");
echo 'Excluído com Sucesso';
?>
