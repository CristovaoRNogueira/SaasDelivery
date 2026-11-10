<?php 
require_once('../../sistema/conexao.php');

$id = (int)@$_POST['id'];
$query = $pdo->prepare("SELECT status FROM vendas WHERE id = :id");
$query->execute([':id' => $id]);
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$status_raw = trim((string)@$res[0]['status']);
$status_norm = mb_strtolower($status_raw, 'UTF-8');

// Definição das 5 etapas da esteira
$etapas = [
    'Iniciado' => ['nome' => 'Pedido Enviado', 'sub' => 'Recebido pelo restaurante', 'icone' => 'bi-send-check'],
    'Aceito' => ['nome' => 'Pedido Confirmado', 'sub' => 'Confirmado pela loja', 'icone' => 'bi-check2-circle'],
    'Preparando' => ['nome' => 'Em Produção', 'sub' => 'Sendo preparado com carinho', 'icone' => 'bi-fire'],
    'Entrega' => ['nome' => 'Saiu para Entrega', 'sub' => 'A caminho do seu endereço', 'icone' => 'bi-bicycle'],
    'Finalizado' => ['nome' => 'Entregue / Concluído', 'sub' => 'Pedido finalizado com sucesso', 'icone' => 'bi-bag-check-fill']
];

$ordem = ['Iniciado', 'Aceito', 'Preparando', 'Entrega', 'Finalizado'];

// Verificação de cancelamento
if (in_array($status_norm, ['cancelado', 'cancelada'], true) || strpos($status_norm, 'cancel') !== false) {
    echo '<div class="alert alert-danger text-center rounded-4 py-3 mb-0" style="border: 1px solid #f5c2c7; background: #fff5f5;">
            <i class="bi bi-x-circle-fill text-danger fs-3 d-block mb-1"></i>
            <strong class="text-danger d-block">Pedido Cancelado</strong>
            <span class="text-muted small">Este pedido foi cancelado pelo restaurante. Em caso de dúvidas, entre em contato via WhatsApp.</span>
          </div>';
    exit();
}

// Mapeamento normalizado de status (cobrindo sinônimos do sistema como 'Finalizado', 'Entregue', 'Concluído')
$isFinalizado = false;
$idxAtual = 0;

if (
    in_array($status_norm, ['finalizado', 'finalizada', 'entregue', 'concluído', 'concluido', 'concluida', 'finalizados', 'entregues'], true)
    || strpos($status_norm, 'finaliz') !== false
    || strpos($status_norm, 'entregu') !== false
    || strpos($status_norm, 'conclui') !== false
    || strpos($status_norm, 'concluí') !== false
) {
    $idxAtual = 4;
    $isFinalizado = true;
} elseif (
    in_array($status_norm, ['entrega', 'saiu para entrega', 'em rota', 'rota', 'a caminho', 'despachado', 'transporte'], true)
    || strpos($status_norm, 'rota') !== false
    || strpos($status_norm, 'caminho') !== false
    || $status_norm === 'entrega'
) {
    $idxAtual = 3;
} elseif (
    in_array($status_norm, ['preparando', 'em produção', 'em producao', 'em preparo', 'produção', 'producao', 'cozinha', 'pronto'], true)
    || strpos($status_norm, 'prepar') !== false
    || strpos($status_norm, 'produ') !== false
    || strpos($status_norm, 'cozinha') !== false
) {
    $idxAtual = 2;
} elseif (
    in_array($status_norm, ['aceito', 'aceita', 'confirmado', 'confirmada', 'aprovado', 'aprovada'], true)
    || strpos($status_norm, 'aceit') !== false
    || strpos($status_norm, 'confirm') !== false
    || strpos($status_norm, 'aprov') !== false
) {
    $idxAtual = 1;
} else {
    // Padrão: Iniciado / Aberto / Aguardando
    $idxAtual = 0;
}

// Badge informativo do status atual
if ($isFinalizado) {
    echo '<div class="d-flex align-items-center gap-2 p-2 px-3 rounded-pill mb-3" style="background: #e8f5e9; color: #2e7d32; font-size: 13px; font-weight: 600;">
            <i class="bi bi-check-circle-fill"></i>
            <span>Pedido Concluído e Entregue!</span>
          </div>';
} elseif ($idxAtual === 3) {
    echo '<div class="d-flex align-items-center gap-2 p-2 px-3 rounded-pill mb-3" style="background: #e3f2fd; color: #1565c0; font-size: 13px; font-weight: 600;">
            <i class="bi bi-bicycle"></i>
            <span>Saiu para Entrega — A caminho do seu endereço!</span>
          </div>';
} elseif ($idxAtual === 2) {
    echo '<div class="d-flex align-items-center gap-2 p-2 px-3 rounded-pill mb-3" style="background: #fff8e1; color: #b78103; font-size: 13px; font-weight: 600;">
            <i class="bi bi-fire"></i>
            <span>Em Produção — Sendo preparado na cozinha!</span>
          </div>';
} elseif ($idxAtual === 1) {
    echo '<div class="d-flex align-items-center gap-2 p-2 px-3 rounded-pill mb-3" style="background: #e0f7fa; color: #00838f; font-size: 13px; font-weight: 600;">
            <i class="bi bi-check2-circle"></i>
            <span>Pedido Confirmado — Em breve iniciará o preparo!</span>
          </div>';
} else {
    echo '<div class="d-flex align-items-center gap-2 p-2 px-3 rounded-pill mb-3" style="background: #f8f9fa; color: #495057; font-size: 13px; font-weight: 600;">
            <i class="bi bi-send-check"></i>
            <span>Pedido Recebido — Aguardando confirmação da loja!</span>
          </div>';
}

echo '<div class="timeline-stepper">';
foreach($ordem as $i => $chave) {
    $item = $etapas[$chave];
    $classe = '';
    $iconHtml = '';
    
    if ($isFinalizado) {
        // Se o status for o final, todas as 5 etapas recebem obrigatoriamente a classe .completed
        $classe = 'completed';
        $iconHtml = '<i class="bi bi-check-lg"></i>';
    } else {
        if ($i < $idxAtual) {
            $classe = 'completed';
            $iconHtml = '<i class="bi bi-check-lg"></i>';
        } elseif ($i === $idxAtual) {
            $classe = 'active';
            $iconHtml = '<i class="bi '.$item['icone'].'"></i>';
        } else {
            $classe = 'pending';
            $iconHtml = '<span>'.($i + 1).'</span>';
        }
    }
    
    $ultimo = ($i === count($ordem) - 1) ? 'is-last' : '';

    echo '<div class="timeline-step '.$classe.' '.$ultimo.'">
            <div class="timeline-node">
                <div class="timeline-icon">'.$iconHtml.'</div>
                <div class="timeline-line"></div>
            </div>
            <div class="timeline-content">
                <div class="timeline-title">'.$item['nome'].'</div>
                <div class="timeline-sub">'.$item['sub'].'</div>
            </div>
          </div>';
}
echo '</div>';
?>
