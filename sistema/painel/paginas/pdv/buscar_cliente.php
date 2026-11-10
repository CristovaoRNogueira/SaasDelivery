<?php
@session_start();
header('Content-Type: application/json; charset=utf-8');

require_once('../../../conexao.php');

$id_empresa = (int) ($_SESSION['empresa'] ?? 0);
$id = (int) ($_POST['id'] ?? 0);

if ($id_empresa <= 0 || $id <= 0) {
	echo json_encode(['erro' => 'Cliente inválido!'], JSON_UNESCAPED_UNICODE);
	exit();
}

$query = $pdo->prepare("SELECT id, nome, telefone, data_nasc, cpf, email, cep, endereco, numero, complemento, bairro, cidade, estado FROM clientes WHERE id = :id AND empresa = :empresa LIMIT 1");
$query->execute([
	':id' => $id,
	':empresa' => $id_empresa,
]);
$cliente = $query->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
	echo json_encode(['erro' => 'Cliente não encontrado!'], JSON_UNESCAPED_UNICODE);
	exit();
}

echo json_encode($cliente, JSON_UNESCAPED_UNICODE);
