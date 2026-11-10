<?php 
try {
    // Entradas: usa variáveis já existentes ou POST/sessão
    $empresa   = isset($id_empresa) ? (int)$id_empresa : (isset($_POST['empresa']) ? (int)$_POST['empresa'] : (int)($_SESSION['empresa'] ?? 0));
    $planoNovo = isset($id_plano)   ? (int)$id_plano   : (int)($_POST['plano_novo'] ?? 0);
    if ($empresa <= 0 || $planoNovo <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'Empresa/Plano não informados.']); exit;
    }

    // Empresa atual
    $st = $pdo->prepare("SELECT id, plano, mensalidade, frequencia FROM empresas WHERE id = :id LIMIT 1");
    $st->execute([':id' => $empresa]);
    $emp = $st->fetch(PDO::FETCH_ASSOC);
    if (!$emp) { echo json_encode(['ok'=>false,'msg'=>'Empresa não encontrada.']); exit; }

    $planoAtual   = (int)$emp['plano'];
    $mensAtual    = (float)$emp['mensalidade'];
    $freqEmpresa  = (int)($emp['frequencia'] ?: 30);

    // Novo plano
    $st = $pdo->prepare("SELECT id, nome, valor FROM planos WHERE id = :id AND (ativo IN ('Sim','1') OR ativo IS NULL) LIMIT 1");
    $st->execute([':id' => $planoNovo]);
    $pl = $st->fetch(PDO::FETCH_ASSOC);
    if (!$pl) { echo json_encode(['ok'=>false,'msg'=>'Plano de destino indisponível.']); exit; }

    $nomePlanoNovo   = $pl['nome'] ?? 'Plano';
    $valorPlanoNovo  = (float)$pl['valor'];

    // Mensalidade em aberto (para pegar vencimento/frequência)
    $st = $pdo->prepare("
        SELECT id, valor, frequencia, vencimento
          FROM receber_sas
         WHERE cliente = :empresa
           AND referencia = 'Mensalidade'
           AND (pago IS NULL OR pago <> 'Sim')
         ORDER BY id DESC
         LIMIT 1
    ");
    $st->execute([':empresa' => $empresa]);
    $faturaAberta = $st->fetch(PDO::FETCH_ASSOC);

    $freqBase       = $faturaAberta ? (int)($faturaAberta['frequencia'] ?: $freqEmpresa ?: 30) : $freqEmpresa;
    $vencNovo       = $faturaAberta ? $faturaAberta['vencimento'] : date('Y-m-d', strtotime('+'.$freqBase.' days'));
    $valorBaseCiclo = $faturaAberta ? (float)$faturaAberta['valor'] : $mensAtual;

    // Normalização simples do valor do novo plano para o ciclo base
    // (se ciclo base for anual e novo valor "aparenta" mensal, multiplica por 12; e vice-versa)
    $valorNovoParaCiclo = $valorPlanoNovo;
    if ($freqBase >= 330 && $valorPlanoNovo < ($valorBaseCiclo * 0.75)) {
        $valorNovoParaCiclo = $valorPlanoNovo * 12.0; // mensal -> anual
    } elseif ($freqBase <= 35 && $valorPlanoNovo > ($valorBaseCiclo * 1.5)) {
        $valorNovoParaCiclo = $valorPlanoNovo / 12.0; // anual -> mensal
    }
    $valorNovoParaCiclo = round($valorNovoParaCiclo, 2);

    $pdo->beginTransaction();

    // 1) Atualiza empresa (plano + mensalidade normalizada)
    $up = $pdo->prepare("UPDATE empresas SET plano = :plano, mensalidade = :mens WHERE id = :id");
    $up->execute([':plano' => $planoNovo, ':mens' => $valorNovoParaCiclo, ':id' => $empresa]);

    // 2) Reaplica recursos do novo plano
    $pdo->prepare("DELETE FROM clientes_recursos WHERE empresa = :emp")->execute([':emp' => $empresa]);
    $rs = $pdo->prepare("SELECT recurso FROM planos_recursos WHERE plano = :plano");
    $rs->execute([':plano' => $planoNovo]);
    $recursos = $rs->fetchAll(PDO::FETCH_COLUMN);
    if ($recursos) {
        $insR = $pdo->prepare("INSERT INTO clientes_recursos (recurso, empresa) VALUES (:recurso, :empresa)");
        foreach ($recursos as $recurso) {
            $insR->execute([':recurso' => (int)$recurso, ':empresa' => $empresa]);
        }
    }

    // 3) Exclui TODAS as mensalidades em aberto (valor antigo)
    $del = $pdo->prepare("DELETE FROM receber_sas WHERE cliente = :cli AND referencia = 'Mensalidade' AND (pago IS NULL OR pago <> 'Sim')");
    $del->execute([':cli' => $empresa]);

    // 4) Cria a nova mensalidade com novo valor e MESMO vencimento
    $usuarioLanc = $_SESSION['id'] ?? null;
    $ins = $pdo->prepare("
        INSERT INTO receber_sas
            (descricao, cliente, valor, data_lanc, frequencia, arquivo, subtotal,
             usuario_lanc, pago, referencia, hora, vencimento)
        VALUES
            ('Mensalidade SAAS', :cli, :val, CURDATE(), :freq, 'sem-foto.png', :val,
             :user, 'Não', 'Mensalidade', CURTIME(), :venc)
    ");
    $ins->execute([
        ':cli'  => $empresa,
        ':val'  => $valorNovoParaCiclo,
        ':freq' => $freqBase,
        ':user' => $usuarioLanc,
        ':venc' => $vencNovo
    ]);
    $idNovaMensalidade = (int)$pdo->lastInsertId();

    $pdo->commit();

    /*
    echo json_encode([
        'ok'  => true,
        'msg' => "Plano atualizado para '{$nomePlanoNovo}'. Mensalidade agora em R$ ".number_format($valorNovoParaCiclo,2,',','.').".",
        'data'=> [
            'empresa'            => $empresa,
            'plano_antigo'       => $planoAtual,
            'plano_novo'         => $planoNovo,
            'mensalidade_antiga' => $mensAtual,
            'mensalidade_nova'   => $valorNovoParaCiclo,
            'freq_base_dias'     => $freqBase,
            'vencimento'         => $vencNovo,
            'nova_mens_id'       => $idNovaMensalidade
        ]
    ], JSON_UNESCAPED_UNICODE);

    */

} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    //echo json_encode(['ok'=>false,'msg'=>'Falha ao aprovar upgrade.','err'=>$e->getMessage()]);
}
