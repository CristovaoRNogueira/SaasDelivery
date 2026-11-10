<?php
@session_start();
require_once('../../../conexao.php');
require_once('../../buscar_config.php');
require_once('../../../caixa_operador.php');

$id_empresa = (int) ($_SESSION['empresa'] ?? 0);
$id_usuario = (int) ($_SESSION['id'] ?? 0);
if ($id_empresa <= 0 || $id_usuario <= 0) {
    echo 'Sua sessão expirou, faça login novamente para dar baixa!';
    exit();
}

$id_caixa = obterCaixaAbertoOperador($pdo, $id_usuario, $id_empresa);
$id = (int) ($_POST['id'] ?? 0);
$formas = $_POST['pgto'] ?? [];
$valoresInformados = $_POST['valor_pgto'] ?? [];
if (!is_array($formas)) $formas = [$formas];
if (!is_array($valoresInformados)) $valoresInformados = [$valoresInformados];

function valorPagamentoPedido($valor): float
{
    $valor = trim((string) $valor);
    if ($valor === '') return 0;
    $valor = str_replace('.', '', $valor);
    return round((float) str_replace(',', '.', $valor), 2);
}

try {
    $pdo->beginTransaction();
    $query = $pdo->prepare('SELECT valor, valor_recebido, cliente, tipo_pgto, arquivado FROM vendas WHERE id = :id AND empresa = :empresa FOR UPDATE');
    $query->execute([':id' => $id, ':empresa' => $id_empresa]);
    $venda = $query->fetch(PDO::FETCH_ASSOC);
	if (($venda['arquivado'] ?? 'Não') === 'Sim') {
		throw new RuntimeException('Restaure o pedido antes de lançar um pagamento.');
	}
    if (!$venda) throw new Exception('Pedido não encontrado!');

    $saldo = max(0, round((float) $venda['valor'] - (float) ($venda['valor_recebido'] ?? 0), 2));
    if ($saldo <= 0) throw new Exception('Este pedido já está totalmente pago!');

    $pagamentos = [];
    $totalPagamentos = 0;
    foreach ($formas as $indice => $forma) {
        $forma = trim((string) $forma);
        $valor = valorPagamentoPedido($valoresInformados[$indice] ?? 0);
        if ($forma === '' || $valor <= 0) throw new Exception('Informe a forma e um valor válido em todos os pagamentos!');

        $query = $pdo->prepare('SELECT id FROM formas_pgto WHERE nome = :nome AND empresa = :empresa LIMIT 1');
        $query->execute([':nome' => $forma, ':empresa' => $id_empresa]);
        $idForma = $query->fetchColumn();
        if (!$idForma) throw new Exception('Forma de pagamento inválida: ' . $forma);

        $pagamentos[] = ['forma' => $forma, 'id_forma' => $idForma, 'valor' => $valor];
        $totalPagamentos = round($totalPagamentos + $valor, 2);
    }

    if (abs($totalPagamentos - $saldo) > 0.009) {
        throw new Exception('A soma das formas de pagamento deve ser igual ao saldo de R$ ' . number_format($saldo, 2, ',', '.') . '.');
    }

    $formasResumo = array_column($pagamentos, 'forma');
    if ($valorRecebido = round((float) ($venda['valor_recebido'] ?? 0), 2)) {
        $query = $pdo->prepare('SELECT COUNT(*) FROM vendas_pagamentos WHERE venda = :venda AND empresa = :empresa');
        $query->execute([':venda' => $id, ':empresa' => $id_empresa]);
        if ((int) $query->fetchColumn() === 0) {
            $formaAnterior = trim((string) $venda['tipo_pgto']);
            if ($formaAnterior === '') $formaAnterior = 'Pagamento anterior';
            $query = $pdo->prepare('INSERT INTO vendas_pagamentos (venda, forma_pgto, valor, usuario, caixa, data, hora, empresa) VALUES (:venda, :forma, :valor, :usuario, :caixa, CURDATE(), CURTIME(), :empresa)');
            $query->execute([':venda' => $id, ':forma' => $formaAnterior, ':valor' => $valorRecebido, ':usuario' => $id_usuario, ':caixa' => $id_caixa, ':empresa' => $id_empresa]);
            array_unshift($formasResumo, $formaAnterior);
        }
    }

    $resumoFormas = implode(' + ', array_values(array_unique($formasResumo)));
    $query = $pdo->prepare("UPDATE vendas SET pago = 'Sim', valor_recebido = valor, total_pago = valor, troco = 0, usuario_baixa = :usuario, tipo_pgto = :formas WHERE id = :id AND empresa = :empresa");
    $query->execute([':usuario' => $id_usuario, ':formas' => $resumoFormas, ':id' => $id, ':empresa' => $id_empresa]);

    $queryPagamento = $pdo->prepare('INSERT INTO vendas_pagamentos (venda, forma_pgto, valor, usuario, caixa, data, hora, empresa) VALUES (:venda, :forma, :valor, :usuario, :caixa, CURDATE(), CURTIME(), :empresa)');
    $queryReceber = $pdo->prepare("INSERT INTO receber SET descricao = :descricao, cliente = :cliente, valor = :valor, subtotal = :valor, data_lanc = CURDATE(), hora = CURTIME(), pago = 'Sim', usuario_pgto = :usuario, vencimento = CURDATE(), data_pgto = CURDATE(), foto = 'sem-foto.png', arquivo = 'sem-foto.png', forma_pgto = :forma, caixa = :caixa, referencia = 'Pagamento Pedido', id_ref = :pedido, empresa = :empresa");
    foreach ($pagamentos as $pagamento) {
        $queryPagamento->execute([':venda' => $id, ':forma' => $pagamento['forma'], ':valor' => $pagamento['valor'], ':usuario' => $id_usuario, ':caixa' => $id_caixa, ':empresa' => $id_empresa]);
        $queryReceber->execute([
            ':descricao' => 'Pagamento do pedido ' . $id . ' - ' . $pagamento['forma'],
            ':cliente' => $venda['cliente'], ':valor' => $pagamento['valor'], ':usuario' => $id_usuario,
            ':forma' => $pagamento['id_forma'], ':caixa' => $id_caixa, ':pedido' => $id, ':empresa' => $id_empresa,
        ]);
    }

    $query = $pdo->prepare("DELETE FROM receber WHERE id_ref = :pedido AND referencia LIKE 'Acr%scimo Pedido' AND (pago IS NULL OR pago <> 'Sim') AND empresa = :empresa");
    $query->execute([':pedido' => $id, ':empresa' => $id_empresa]);
    $pdo->commit();
    echo 'Baixado com Sucesso';
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo $e->getMessage();
}
