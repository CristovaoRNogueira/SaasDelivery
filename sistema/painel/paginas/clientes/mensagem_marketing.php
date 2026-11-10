<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, refaça o Login!';
	exit();
}
$tabela = 'clientes';


$id = $_POST['id'];
$nome = $_POST['nome'];
$cartoes = $_POST['cartoes'];
$tel_cliente = $_POST['telefone'];
$total_cartoes_troca = $total_cartoes_config;

if ($api_whatsapp != 'Não') {
  $telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $tel_cliente);

 if($total_cartoes_troca == ""){
 	$total_cartoes_troca = 0;
 }

  if($cartoes == ""){
 	$cartoes = 0;
 }
  
$cartoes_faltantes = $total_cartoes_troca - $cartoes;

$mensagem  = '🎉 *Falta pouco!* %0A%0A';
$mensagem .= 'Olá *' . $nome . '*, tudo bem? 😊 %0A%0A';
$mensagem .= 'Você já possui *' . $cartoes . '* cartões no nosso programa de fidelidade. %0A';
if($cartoes_faltantes > 0){
	$mensagem .= 'Faltam apenas *' . $cartoes_faltantes . '* cartões para completar sua troca! ⭐ %0A%0A';
}else{
	$mensagem .= 'Parabéns, Você já completou os cartões! ⭐ %0A%0A';
}

$mensagem .= 'Não perca, continue comprando conosco e aproveite! 🎁';


 require ("../../../../js/ajax/api_texto.php");

}


?>