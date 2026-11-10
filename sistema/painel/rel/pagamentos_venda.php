<?php

function carregarPagamentosPorVenda(PDO $pdo, array $vendas, int $empresa): array
{
	$ids = array_values(array_unique(array_filter(array_map('intval', array_column($vendas, 'id')))));
	if (!$ids) {
		return [];
	}

	$marcadores = implode(',', array_fill(0, count($ids), '?'));
	$sql = "SELECT venda, forma_pgto, SUM(valor) AS valor
		FROM vendas_pagamentos
		WHERE empresa = ? AND venda IN ($marcadores)
		GROUP BY venda, forma_pgto
		ORDER BY venda, MIN(id)";
	$query = $pdo->prepare($sql);
	$query->execute(array_merge([$empresa], $ids));

	$pagamentos = [];
	foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $registro) {
		$pagamentos[(int) $registro['venda']][] = [
			'forma' => trim((string) $registro['forma_pgto']) ?: 'Não Informado',
			'valor' => (float) $registro['valor'],
		];
	}

	return $pagamentos;
}

function obterPagamentosVenda(array $pagamentosPorVenda, int $venda, string $formaLegada, float $total): array
{
	if (!empty($pagamentosPorVenda[$venda])) {
		return $pagamentosPorVenda[$venda];
	}

	return [[
		'forma' => trim($formaLegada) ?: 'Não Informado',
		'valor' => $total,
	]];
}

function descreverPagamentosVenda(array $pagamentos): string
{
	$descricao = [];
	foreach ($pagamentos as $pagamento) {
		$descricao[] = $pagamento['forma'] . ': R$ ' . number_format($pagamento['valor'], 2, ',', '.');
	}

	return implode(' / ', $descricao);
}

