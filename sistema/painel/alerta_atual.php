<?php
@session_start();
$id_empresa = @$_SESSION['empresa'];
require_once("../conexao.php");

try {
    // Busca o alerta ativo mais recente, com data <= hoje (ou nula)
    $sql = "SELECT id, titulo, texto, data
              FROM alertas_sas
             WHERE (ativo = 'Sim' OR ativo = '1' OR ativo = 'sim' OR ativo = 'SIM')
               AND (data IS NULL OR data <= CURDATE())
             ORDER BY COALESCE(data, '9999-12-31') DESC, id DESC
             LIMIT 1";

    $stmt = $pdo->query($sql);
    $row  = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Se o texto foi salvo com HTML pelo editor, envie como está
        echo json_encode([
            'ok' => true,
            'alerta' => [
                'id'     => (int)$row['id'],
                'titulo' => $row['titulo'] ?? '',
                'texto'  => $row['texto'] ?? '',
                'data'   => $row['data'] ?? null,
            ]
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['ok' => false, 'msg' => 'Sem alertas ativos'], JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'msg' => 'Erro ao consultar', 'err' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}


?>