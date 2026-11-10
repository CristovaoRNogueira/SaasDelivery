<?php
@session_start();

$tabela = 'dispositivos';

require_once("../../../conexao.php");

$id_empresa = @$_SESSION['empresa'];

require_once("../../buscar_config.php");

$id = $_POST['id'] ?? '';

if($id == ''){
    echo 'ID não informado';
    exit();
}

$query = $pdo->prepare("SELECT * FROM $tabela WHERE id = :id AND empresa = :empresa LIMIT 1");
$query->execute([
    ':id' => $id,
    ':empresa' => $id_empresa
]);

$dispositivo = $query->fetch(PDO::FETCH_ASSOC);

if(!$dispositivo){
    echo 'Dispositivo não encontrado';
    exit();
}

$appkey = $dispositivo['appkey'];

// Tenta apagar na Menuia
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => 'https://chatbot.menuia.com/api/developer',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => json_encode([
        'authkey' => $instancia_whatsapp,
        'message' => $appkey,
        'apagarDispositivo' => 'true'
    ])
]);

$retorno = curl_exec($curl);
$erroCurl = curl_error($curl);
curl_close($curl);

// Mesmo se der erro na Menuia, remove do banco
$delete = $pdo->prepare("DELETE FROM $tabela WHERE id = :id AND empresa = :empresa");
$delete->execute([
    ':id' => $id,
    ':empresa' => $id_empresa
]);

// Se o dispositivo apagado for o principal, limpa token no config
if($appkey == @$token_whatsapp){
    $pdo->prepare("UPDATE config SET token_whatsapp = '' WHERE empresa = :empresa")
        ->execute([':empresa' => $id_empresa]);
}

echo 'Excluído com Sucesso';
?>