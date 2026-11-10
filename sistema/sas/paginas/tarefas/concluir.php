<?php
@session_start();
$id_usuario = @$_SESSION['id'];
$tabela = 'tarefas_sas';
require_once("../../../conexao.php");

$id = $_POST['id'];

$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$hash = @$res[0]['hash'];
$recorrencia = @$res[0]['recorrencia'];
$data = @$res[0]['data'];
$hora = @$res[0]['hora'];
$hora_alerta = @$res[0]['hora_mensagem'];
$descricao = @$res[0]['descricao'];
$usuario_tarefa = @$res[0]['usuario'];
$prioridade = @$res[0]['prioridade'];
$titulo = @$res[0]['titulo'];


if ($hash != "") {
	require("../../apis/cancelar_agendamento.php");
}

$pdo->query("UPDATE $tabela SET status = 'Concluída' WHERE id = '$id' ");


if($recorrencia != 'Não'){
################# CRIAR A PRÓXIMA CONTA A PAGAR CASO TENHA SIDO PAGA #################
$dias_frequencia = $recorrencia;

if ($dias_frequencia == 30 || $dias_frequencia == 31) {
	$nova_data_venc = date('Y/m/d', strtotime("+1 month", strtotime($data)));

} else if ($dias_frequencia == 90) {
	$nova_data_venc = date('Y/m/d', strtotime("+3 month", strtotime($data)));

} else if ($dias_frequencia == 180) {
	$nova_data_venc = date('Y/m/d', strtotime("+6 month", strtotime($data)));

} else if ($dias_frequencia == 360 || $dias_frequencia == 365) {
	date('Y/m/d', strtotime("+6 month", strtotime($data)));
} else {
	$nova_data_venc = date('Y/m/d', strtotime("+$dias_frequencia days", strtotime($data)));
}

$query = $pdo->prepare("INSERT INTO $tabela SET usuario = '$usuario_tarefa', usuario_lanc = '$id_usuario', data = '$nova_data_venc', hora = '$hora', hora_mensagem = '$hora_alerta', descricao = :descricao, status = 'Agendada', prioridade = '$prioridade', titulo = :titulo, recorrencia = '$recorrencia', empresa = 0 ");
$query->bindValue(":descricao", "$descricao");
$query->bindValue(":titulo", "$titulo");

$query->execute();
$ultimo_id = $pdo->lastInsertId();

$query = $pdo->query("SELECT * from usuarios where id = '$usuario_tarefa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$telefone_usuario = @$res[0]['telefone'];
$nome_usuario = @$res[0]['nome'];

// extrair o primiero nome
$primeiroNome = explode(" ", $nome_usuario);
$hora = date('H');
if ($hora < 12 && $hora >= 6)
	$saudacao = "Bom Dia";
if ($hora >= 12 && $hora < 18)
	$saudacao = "Boa Tarde";
if ($hora >= 18 && $hora <= 23)
	$saudacao = "Boa Noite";
if ($hora < 6 && $hora >= 0)
	$saudacao = "Boa madrugada";

	$dataF = implode('/', array_reverse(@explode('-', $data)));
	$horaF = date("H:i", @strtotime($hora));

//enviar whatsapp
if ($api_whatsapp != 'Não' and $telefone_usuario != '' and $hora_alerta != '') {
	$telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone_usuario);
	$mensagem_whatsapp = $saudacao . ' *' . $primeiroNome[0] . ' 😀*%0A%0A';
	$mensagem_whatsapp .= '*' . $nome_sistema . '*%0A%0A';
	$mensagem_whatsapp .= '_Lembrete de Tarefa Agendada_ %0A';
	$mensagem_whatsapp .= '*Titulo:* ' . $titulo . ' %0A';
	$mensagem_whatsapp .= '*Hora:* ' . $horaF . ' %0A';
	$mensagem_whatsapp .= '*Data:* ' . $dataF . ' %0A';
	$mensagem_whatsapp .= '*Descrição:* ' . $descricao . ' %0A';

	$data_agd = $nova_data_venc . ' ' . $hora_alerta;
	require('../../apis/agendar.php');

	$pdo->query("UPDATE $tabela SET hash = '$hash' where id = '$ultimo_id'");

}	
}

echo 'Tarefa Concluída';
?>