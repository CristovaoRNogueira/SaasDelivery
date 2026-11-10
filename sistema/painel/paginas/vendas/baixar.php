<?php
@session_start();
require_once("../../../conexao.php");
require_once("../../../caixa_operador.php");
$tabela = 'vendas';
$id_usuario = $_SESSION['id'];
$id_empresa = $_SESSION['empresa'];
$id_caixa = obterCaixaAbertoOperador($pdo, $id_usuario, $id_empresa);


$id = $_POST['id'];

$pdo->query("UPDATE $tabela SET pago = 'Sim', valor_recebido = valor, total_pago = valor, troco = 0, usuario_baixa = '$id_usuario' where id = '$id'");

echo 'Baixado com Sucesso';
 ?>
