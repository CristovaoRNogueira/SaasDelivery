<?php
@session_start();
header('Content-Type: application/json; charset=utf-8');
require_once('../../../conexao.php');
require_once('../../buscar_config.php');

$id_empresa = (int) ($_SESSION['empresa'] ?? 0);
$cliente = (int) ($_POST['cliente'] ?? 0);
if ($id_empresa <= 0 || $cliente <= 0) {
	echo json_encode(['erro' => 'Selecione um cliente para calcular o frete.'], JSON_UNESCAPED_UNICODE);
	exit();
}

$query = $pdo->prepare('SELECT endereco, numero, bairro, cidade, estado, cep, complemento FROM clientes WHERE id = :id AND empresa = :empresa LIMIT 1');
$query->execute([':id' => $cliente, ':empresa' => $id_empresa]);
$dados = $query->fetch(PDO::FETCH_ASSOC);
if (!$dados) {
	echo json_encode(['erro' => 'Cliente não encontrado.'], JSON_UNESCAPED_UNICODE);
	exit();
}

if ($entrega_distancia === 'Sim' && trim((string) $chave_api_maps) !== '') {
	echo json_encode([
		'modo' => 'distancia',
		'endereco' => implode(', ', array_filter([$dados['endereco'], $dados['numero'], $dados['bairro'], $dados['cidade'], $dados['estado'], $dados['cep']])),
		'chave' => $chave_api_maps,
		'latitude_rest' => (float) $latitude_rest,
		'longitude_rest' => (float) $longitude_rest,
		'distancia_maxima' => (float) $distancia_entrega_km,
		'valor_km' => ((float) $valor_km > 0 ? (float) $valor_km : 1),
	], JSON_UNESCAPED_UNICODE);
	exit();
}

$query = $pdo->prepare('SELECT valor FROM bairros WHERE nome = :bairro AND empresa = :empresa LIMIT 1');
$query->execute([':bairro' => $dados['bairro'], ':empresa' => $id_empresa]);
$valor = $query->fetchColumn();
echo json_encode([
	'modo' => 'bairro',
	'valor' => $valor !== false ? (float) $valor : 0,
	'bairro' => $dados['bairro'],
	'encontrado' => $valor !== false,
], JSON_UNESCAPED_UNICODE);
