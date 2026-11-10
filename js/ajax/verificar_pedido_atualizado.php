<?php
@session_start();
require_once('../../sistema/conexao.php');
require_once('../../sistema/assinatura_pedido.php');

$idPedido = (int) (@$_POST['id']);
$idEmpresa = (int) (@$_SESSION['empresa']);

header('Content-Type: text/plain; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if ($idPedido <= 0 || $idEmpresa <= 0) {
    http_response_code(400);
    exit();
}

echo gerarAssinaturaPedido($pdo, $idPedido, $idEmpresa);

