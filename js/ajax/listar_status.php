<?php 
require_once('../../sistema/conexao.php');

$id = (int)$_POST['id'];
$query = $pdo->prepare("SELECT status from vendas where id = :id");
$query->execute([':id' => $id]);
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$status = @$res[0]['status'];

$etapas = [
    'Iniciado' => ['nome' => 'Pedido Enviado', 'sub' => 'Recebido pelo restaurante', 'icone' => 'bi-send-check'],
    'Aceito' => ['nome' => 'Pedido Confirmado', 'sub' => 'Confirmado pela loja', 'icone' => 'bi-check2-circle'],
    'Preparando' => ['nome' => 'Em Produção', 'sub' => 'Sendo preparado com carinho', 'icone' => 'bi-fire'],
    'Entrega' => ['nome' => 'Saiu para Entrega', 'sub' => 'A caminho do seu endereço', 'icone' => 'bi-bicycle'],
    'Finalizado' => ['nome' => 'Entregue / Concluído', 'sub' => 'Pedido finalizado com sucesso', 'icone' => 'bi-bag-check-fill']
];

$ordem = ['Iniciado', 'Aceito', 'Preparando', 'Entrega', 'Finalizado'];
$idxAtual = array_search($status, $ordem);
if($idxAtual === false) {
    $idxAtual = 0;
}

echo '<div class="timeline-stepper">';
foreach($ordem as $i => $chave) {
    $item = $etapas[$chave];
    $classe = '';
    $iconHtml = '';
    
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