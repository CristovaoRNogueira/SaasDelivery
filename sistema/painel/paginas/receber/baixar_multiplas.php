<?php 
$tabela = 'receber';
require_once("../../../conexao.php");
require_once("../../../caixa_operador.php");

@session_start();
$id_usuario = $_SESSION['id'];
$id_empresa = $_SESSION['empresa'];
$id_caixa = obterCaixaAbertoOperador($pdo, $id_usuario, $id_empresa);
//  , caixa = '$id_caixa'

$id = $_POST['novo_id'];

$pdo->query("UPDATE $tabela SET data_pgto = curDate(), usuario_pgto = '$id_usuario', pago = 'Sim', caixa = '$id_caixa', hora = curTime() where id = '$id' and pago != 'Sim'");

?>
