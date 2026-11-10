<?php
@session_start();
$id_usuario = @$_SESSION['id'];
$tabela = 'tarefas_sas';
require_once("../../../conexao.php");

// Obter a data e hora atuais
$data_atual = date('Y-m-d');
$hora_atual = date('H:i:s');

$descricao = $_POST['descricao'];
$data = $_POST['data'];
$hora = $_POST['hora'];
$hora_alerta = $_POST['hora_alerta'];
$usuario_tarefa = $_POST['usuario_tarefa'];
$prioridade = $_POST['prioridade'];
$id = $_POST['id'];
$titulo = $_POST['titulo'];
$recorrencia = @$_POST['recorrencia'];


$dataF = implode('/', array_reverse(@explode('-', $data)));
$horaF = date("H:i", @strtotime($hora));


// Verificar se a data está no passado
if (strtotime($data) < strtotime($data_atual)) {
	echo 'Erro! Esta data já passou, escolha outra data.';
	exit();
}

// Verificar se a data é hoje e a hora está no passado
if ($data === $data_atual && strtotime($hora) < strtotime($hora_atual)) {
	echo 'Erro! Este horário já passou, escolha um horário válido.';
	exit();
}

//validacao hora
$query = $pdo->query("SELECT * from $tabela where data = '$data' and hora = '$hora' and usuario = '$usuario_tarefa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$id_reg = @$res[0]['id'];
if (@count($res) > 0 and $id != $id_reg) {
	echo 'Horário já Agendado!';
	exit();
}


if (strtotime($hora_alerta) >= strtotime($hora)) {
	echo 'A hora do alerta tem que ser menor que a hora do agendamento da tarefa!';
	exit();
}


$query = $pdo->query("SELECT * from usuarios where id = '$usuario_tarefa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$telefone_usuario = @$res[0]['telefone'];
$nome_usuario = @$res[0]['nome'];



if ($id == "") {
	$query = $pdo->prepare("INSERT INTO $tabela SET usuario = '$usuario_tarefa', usuario_lanc = '$id_usuario', data = '$data', hora = '$hora', hora_mensagem = '$hora_alerta', descricao = :descricao, status = 'Agendada', prioridade = '$prioridade', titulo = :titulo, recorrencia = '$recorrencia' ");

} else {
	$query = $pdo->prepare("UPDATE $tabela SET data = '$data', hora = '$hora', hora_mensagem = '$hora_alerta', descricao = :descricao, prioridade = '$prioridade', titulo = :titulo, usuario = '$usuario_tarefa', recorrencia = '$recorrencia'  where id = '$id'");
}
$query->bindValue(":descricao", "$descricao");
$query->bindValue(":titulo", "$titulo");

$query->execute();
$ultimo_id = $pdo->lastInsertId();


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


//recuperar hash
$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$hash = @$res[0]['hash'];

if ($hash != "") {
	require("../../apis/cancelar_agendamento.php");
}


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

	$data_agd = $data . ' ' . $hora_alerta;
	require('../../apis/agendar.php');

	$pdo->query("UPDATE $tabela SET hash = '$hash' where id = '$ultimo_id'");

}

echo 'Salvo com Sucesso';
?>