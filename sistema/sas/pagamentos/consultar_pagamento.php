<?php
include('tokens.php');
//$access_token = 'APP_USR-7645692252055791-101021-ba91ccf6cd290bc3115e3270a30edb1e-131939455';
//$ref_pix = '102345603532';
$curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.mercadopago.com/v1/payments/'.$ref_pix,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'accept: application/json',
        'content-type: application/json',
        'Authorization: Bearer '.$access_token
    ),
    ));
    $response = curl_exec($curl);
    $resultado = json_decode($response);
curl_close($curl);
//echo $resultado->status;
$status_api = @$resultado->status;
$total_pago_api = @$resultado->transaction_amount;
$metodo_pagamento = @$resultado->payment_method_id; // Ex: "pix", "visa", "master"
$tipo_pagamento = @$resultado->payment_type_id; // Ex: "credit_card", "debit_card", "ticket (boleto)", "bank_transfer (pix)"

if($tipo_pagamento == 'credit_card'){
    $tipo_pagamento = 'Cartão de Crédito';
}

if($tipo_pagamento == 'debit_card'){
    $tipo_pagamento = 'Cartão de Débito';
}

if($tipo_pagamento == 'ticket'){
    $tipo_pagamento = 'Boleto';
}

if($tipo_pagamento == 'bank_transfer'){
    $tipo_pagamento = 'Pix';
}

if($status_api == 'approved'){

}
//var_dump($resultado);

?>