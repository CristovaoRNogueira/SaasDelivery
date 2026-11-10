<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");

if($modo_teste == 'Sim'){
    echo 'Em modo de teste esse recurso fica desabilitado para gerar Pix!';
    return;
}

$valor = $_POST['valor'];
$valor_formatado = number_format($valor, 2, ',', '.');

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
            'Authorization: Bearer ' . $token_mp
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

echo '
<div style="text-align:center;">

    <div style="font-size:18px; font-weight:bold; margin-bottom:10px;">
        Valor: <span style="color:#28a745;">R$ '.$valor_formatado.'</span>
    </div>

    <img 
        id="base64image"
        src="data:image/jpeg;base64,' . $resultado->point_of_interaction->transaction_data->qr_code_base64 . '" 
        style="width:280px; max-width:100%; margin:10px auto; display:block; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.15);"
    />

    <div style="margin-top:10px;">
        <a href="#" onclick="copiar(); return false;" style="text-decoration:none;">
            <i class="bi bi-clipboard text-primary"></i> 
            <span style="font-size:13px;">Copiar Código Pix</span>
        </a>
    </div>

</div>

<input type="text" id="chave_pix_copia" value="' . $codigo_pix . '" style="opacity:0; position:absolute;">

<input type="hidden" id="codigo_pix" value="' . $id_ref . '">
';

echo '
<div id="status_pix" style="margin-top:12px; font-size:13px; color:#666;">
    Aguardando pagamento...
</div>
';
