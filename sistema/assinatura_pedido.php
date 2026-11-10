<?php

/**
 * Gera uma assinatura dos dados visiveis do pedido para detectar edicoes.
 */
function gerarAssinaturaPedido(PDO $pdo, $idPedido, $idEmpresa)
{
    $arquivoPedido = dirname(__DIR__) . '/pedido.php';
    $arquivoDetalhes = __DIR__ . '/detalhes_item_pedido.php';
    if (function_exists('opcache_invalidate')) {
        @opcache_invalidate($arquivoPedido, true);
        @opcache_invalidate($arquivoDetalhes, true);
    }

    $query = $pdo->prepare('SELECT id, valor, total_pago, valor_recebido, troco, pago, taxa_entrega, tipo_pgto, cupom, taxa_extra_pedido, obs FROM vendas WHERE id = :id AND empresa = :empresa LIMIT 1');
    $query->execute([':id' => (int) $idPedido, ':empresa' => (int) $idEmpresa]);
    $venda = $query->fetch(PDO::FETCH_ASSOC);
    if (!$venda) {
        return '';
    }

    $query = $pdo->prepare('SELECT id, produto, quantidade, total_item, obs, item, variacao, nome_produto, sabores, borda, categoria, valor_unitario FROM carrinho WHERE pedido = :pedido AND empresa = :empresa ORDER BY id');
    $query->execute([':pedido' => (int) $idPedido, ':empresa' => (int) $idEmpresa]);
    $itens = $query->fetchAll(PDO::FETCH_ASSOC);

    $query = $pdo->prepare('SELECT id, carrinho, id_item, tabela, quantidade, grade, tipagem FROM temp WHERE carrinho IN (SELECT id FROM carrinho WHERE pedido = :pedido AND empresa = :empresa) ORDER BY carrinho, id');
    $query->execute([':pedido' => (int) $idPedido, ':empresa' => (int) $idEmpresa]);
    $complementos = $query->fetchAll(PDO::FETCH_ASSOC);

    $versaoLayout = @filemtime($arquivoPedido) . ':' . @filemtime($arquivoDetalhes);

    return hash('sha256', json_encode([$venda, $itens, $complementos, $versaoLayout], JSON_UNESCAPED_UNICODE));
}

