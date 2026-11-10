<?php
$tabela = 'receber_sas';
require_once("../../../conexao.php");

$id = $_POST['id'];

$pdo->query("UPDATE $tabela SET pago = 'Não', data_pgto = '', forma_pgto = '', ref_pix = '' WHERE id = '$id' ");
echo 'Excluído com Sucesso';
?>