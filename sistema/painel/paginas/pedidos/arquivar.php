<?php
@session_start();
require_once('../../../conexao.php');

$id_empresa = (int) ($_SESSION['empresa'] ?? 0);
$id = (int) ($_POST['id'] ?? 0);
$acao = $_POST['acao'] ?? 'arquivar';

if ($id_empresa <= 0 || $id <= 0) {
	echo 'Pedido inválido!';
	exit();
}

$arquivado = $acao === 'restaurar' ? 'Não' : 'Sim';
try {
	$pdo->beginTransaction();
	$query = $pdo->prepare('UPDATE vendas SET arquivado = :arquivado WHERE id = :id AND empresa = :empresa');
	$query->execute([':arquivado' => $arquivado, ':id' => $id, ':empresa' => $id_empresa]);
	$pedido_encontrado = $query->rowCount() > 0;

	$query_financeiro = $pdo->prepare("UPDATE receber SET arquivado = :arquivado
		WHERE id_ref = :id AND empresa = :empresa
		AND (referencia IN ('Venda', 'Pagamento Pedido', 'Delivery', 'Retirar', 'Consumir Local')
			OR referencia LIKE 'Acr%scimo Pedido')");
	$query_financeiro->execute([':arquivado' => $arquivado, ':id' => $id, ':empresa' => $id_empresa]);
	$pdo->commit();

	echo $pedido_encontrado ? 'Salvo com Sucesso' : 'Pedido não encontrado ou já atualizado!';
} catch (Throwable $erro) {
	if ($pdo->inTransaction()) {
		$pdo->rollBack();
	}
	echo 'Não foi possível atualizar o pedido!';
}
