<?php
@session_start();
$id_usuario = 0;
$empresa = @$_SESSION['empresa'];
$tabela = 'chamados_respostas';
require_once("../../../conexao.php");

$id = $_POST['id'];
$texto = $_POST['texto_resposta'];

$pdo->query("UPDATE chamados set respondido = 'Não' WHERE id = '$id' ");


$query = $pdo->prepare("INSERT INTO $tabela SET empresa = '$empresa', data = curDate(), hora = curTime(), usuario = '$id_usuario', texto = :texto, chamado = '$id' ");

$query->bindValue(":texto", "$texto");
$query->execute();

echo 'Salvo com Sucesso';
