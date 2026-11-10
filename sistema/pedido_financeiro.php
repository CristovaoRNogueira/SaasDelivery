<?php

function pedidoPermiteEdicao(PDO $pdo, $idPedido, $idEmpresa)
{
    $query = $pdo->prepare('SELECT status, entrega FROM vendas WHERE id = :id AND empresa = :empresa LIMIT 1');
    $query->execute([':id' => (int) $idPedido, ':empresa' => (int) $idEmpresa]);
    $pedido = $query->fetch(PDO::FETCH_ASSOC);
    if (!$pedido) {
        return false;
    }

    if (in_array($pedido['status'], ['Iniciado', 'Aceito', 'Preparando'], true)) {
        return true;
    }

    return $pedido['status'] === 'Entrega' && strcasecmp(trim((string) $pedido['entrega']), 'Delivery') !== 0;
}

/**
 * Sincroniza o financeiro depois de uma alteracao no valor de um pedido.
 * Mantem o que ja foi efetivamente recebido e cria apenas um titulo para o saldo.
 */
function sincronizarFinanceiroPedidoEditado(PDO $pdo, $idPedido, $idEmpresa, $novoValor)
{
    $idPedido = (int) $idPedido;
    $idEmpresa = (int) $idEmpresa;
    $novoValor = max(0, round((float) $novoValor, 2));

    $query = $pdo->prepare('SELECT * FROM vendas WHERE id = :id AND empresa = :empresa LIMIT 1');
    $query->execute([':id' => $idPedido, ':empresa' => $idEmpresa]);
    $venda = $query->fetch(PDO::FETCH_ASSOC);
    if (!$venda) {
        throw new RuntimeException('Pedido nao encontrado para atualizar o financeiro.');
    }

    $valorRecebido = isset($venda['valor_recebido']) && $venda['valor_recebido'] !== null
        ? (float) $venda['valor_recebido']
        : ($venda['pago'] === 'Sim' ? (float) $venda['valor'] : 0);
    $valorRecebido = max(0, round($valorRecebido, 2));
    $saldoPendente = max(0, round($novoValor - $valorRecebido, 2));
    $pago = $saldoPendente <= 0 ? 'Sim' : 'Não';

    $credito = max(0, round($valorRecebido - $novoValor, 2));
    $update = $pdo->prepare("UPDATE vendas SET valor = :valor, valor_recebido = :recebido, pago = :pago, troco = :credito WHERE id = :id AND empresa = :empresa");
    $update->execute([
        ':valor' => $novoValor,
        ':recebido' => $valorRecebido,
        ':pago' => $pago,
        ':credito' => $credito,
        ':id' => $idPedido,
        ':empresa' => $idEmpresa,
    ]);

    $busca = $pdo->prepare("SELECT id FROM receber WHERE id_ref = :pedido AND referencia = 'Acréscimo Pedido' AND pago = 'Não' AND empresa = :empresa ORDER BY id DESC LIMIT 1");
    $busca->execute([':pedido' => $idPedido, ':empresa' => $idEmpresa]);
    $idReceber = $busca->fetchColumn();

    if ($saldoPendente > 0) {
        if ($idReceber) {
            $titulo = $pdo->prepare('UPDATE receber SET valor = :valor, subtotal = :valor, cliente = :cliente, vencimento = CURDATE() WHERE id = :id');
            $titulo->execute([':valor' => $saldoPendente, ':cliente' => $venda['cliente'], ':id' => $idReceber]);
        } else {
            $titulo = $pdo->prepare("INSERT INTO receber SET descricao = :descricao, cliente = :cliente, valor = :valor, subtotal = :valor, data_lanc = CURDATE(), hora = CURTIME(), pago = 'Não', vencimento = CURDATE(), foto = 'sem-foto.png', arquivo = 'sem-foto.png', referencia = 'Acréscimo Pedido', id_ref = :pedido, empresa = :empresa");
            $titulo->execute([
                ':descricao' => 'Acréscimo do pedido ' . ($venda['pedido'] ?: $idPedido),
                ':cliente' => $venda['cliente'],
                ':valor' => $saldoPendente,
                ':pedido' => $idPedido,
                ':empresa' => $idEmpresa,
            ]);
        }
    } elseif ($idReceber) {
        $excluir = $pdo->prepare("DELETE FROM receber WHERE id_ref = :pedido AND referencia = 'Acréscimo Pedido' AND pago = 'Não' AND empresa = :empresa");
        $excluir->execute([':pedido' => $idPedido, ':empresa' => $idEmpresa]);
    }

    return ['valor' => $novoValor, 'recebido' => $valorRecebido, 'saldo' => $saldoPendente, 'credito' => $credito, 'pago' => $pago];
}

