<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");

$codigo_pix = $_POST['codigo_pix'] ?? '';

if($codigo_pix == ''){
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Código Pix não recebido.'
    ]);
    exit();
}

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.mercadopago.com/v1/payments/' . $codigo_pix,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'accept: application/json',
        'content-type: application/json',
        'Authorization: Bearer ' . $token_mp
    ),
));

$response = curl_exec($curl);
$erro = curl_error($curl);
curl_close($curl);

if($erro){
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Erro CURL: ' . $erro
    ]);
    exit();
}

$resultado = json_decode($response);

$status_api = @$resultado->status;

if($status_api == ''){
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Não foi possível consultar o status do pagamento.'
    ]);
    exit();
}

echo json_encode([
    'status' => $status_api,
    'mensagem' => 'Status atual: ' . $status_api
]);
?>