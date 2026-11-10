<?php
@session_start();
require_once('../../sistema/conexao.php');

$id_empresa = @$_SESSION['empresa'];
if($id_empresa == ""){
    echo 'Sem Sessão da Empresa, acesse novamente!';
    exit();
}
require_once("../../sistema/painel/buscar_config.php");

require_once('../../js/ajax/ApiConfig.php');

if ($dados_pagamento != "" || $api_merc == '') {
    echo '<b>Chave Pix</b><br>';
    echo '<b>' . $tipo_chave . '</b> : ' . $dados_pagamento;
    exit();
}

$valor = $_POST['valor'];

$curl = curl_init();

$dados["transaction_amount"] = (float) $valor;
$dados["description"] = "Venda Delivery";
$dados["external_reference"] = "2";
$dados["payment_method_id"] = "pix";
$dados["notification_url"] = "https://google.com";
$dados["payer"]["email"] = "deliverye@hotmail.com";
$dados["payer"]["first_name"] = "Delivery";
$dados["payer"]["last_name"] = "Pagamento";

$dados["payer"]["identification"]["type"] = "CPF";
$dados["payer"]["identification"]["number"] = "34152426764";

$dados["payer"]["address"]["zip_code"] = "06233200";
$dados["payer"]["address"]["street_name"] = "Av. das Nações Unidas";
$dados["payer"]["address"]["street_number"] = "3003";
$dados["payer"]["address"]["neighborhood"] = "Bonfim";
$dados["payer"]["address"]["city"] = "Osasco";
$dados["payer"]["address"]["federal_unit"] = "SP";

curl_setopt_array(
    $curl,
    array(
        CURLOPT_URL => 'https://api.mercadopago.com/v1/payments',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($dados),
        CURLOPT_HTTPHEADER => array(
            'accept: application/json',
            'content-type: application/json',
            'X-Idempotency-Key: ' . date('Y-m-d-H:i:s-') . rand(0, 1500),
            'Authorization: Bearer ' . $access_token
        ),
    )
);
$response = curl_exec($curl);
$resultado = json_decode($response);

$id = $dados["external_reference"];
//var_dump($response);
curl_close($curl);
$codigo_pix = $resultado->point_of_interaction->transaction_data->qr_code;

$id_ref = $resultado->id;

echo "
<img style='display:block;' width='200px' id='base64image'
src='data:image/jpeg;base64, " . $resultado->point_of_interaction->transaction_data->qr_code_base64 . "'/>";
echo '
<a style="margin-left:15px" class="link-neutro" href="javascript:void(0)" onClick="copiar()"><i class="bi bi-clipboard text-primary"></i> <span ><small><small>Copiar Chave Pix <br><input type="text" id="chave_pix_copia" value="' . $codigo_pix . '" style="background: transparent; border:none; width:100px; opacity:0" readonly></small></small></span> </a>';

echo '<input type="hidden" id="codigo_pix" value="' . $id_ref . '">';
