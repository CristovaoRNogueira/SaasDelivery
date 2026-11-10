<?php
@session_start();
$id_empresa = 0;

header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$tabela = 'dispositivos';

require_once("../../../conexao.php");


$appkey = $_POST['appkey'] ?? '';

if($appkey == ''){
    echo json_encode(['status' => 400, 'mensagem' => 'AppKey não informada']);
    exit();
}

$stmt = $pdo->prepare("
    UPDATE $tabela 
    SET 
        status = 'Ativo',
        status_api = 'conectado'
    WHERE appkey = :appkey
    AND empresa = :empresa
");

$executou = $stmt->execute([
    ':appkey' => $appkey,
    ':empresa' => $id_empresa
]);

if($executou)
{
    $stmtConfig = $pdo->prepare("
        UPDATE config 
        SET token_whatsapp = :token
        WHERE empresa = :empresa
    ");

    $stmtConfig->execute([
        ':token' => $appkey,
        ':empresa' => $id_empresa
    ]);

    echo json_encode([
        'status' => 200,
        'mensagem' => 'Dispositivo conectado com sucesso'
    ]);
}
else
{
    echo json_encode([
        'status' => 500,
        'mensagem' => 'Erro ao atualizar dispositivo'
    ]);
}
?>