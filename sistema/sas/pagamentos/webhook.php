<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
//error_reporting(E_ALL);

include("./config.php");
require("../sistema/conexao.php");

if($_GET["topic"]=="" || $_GET["id"]==""){
    die("sem_dados");
}

// CONSULTAR PAGAMENTO
$curl = curl_init();
curl_setopt_array($curl, array(
CURLOPT_URL => 'https://api.mercadopago.com/v1/payments/'.$id,
CURLOPT_RETURNTRANSFER => true,
CURLOPT_ENCODING => '',
CURLOPT_MAXREDIRS => 10,
CURLOPT_TIMEOUT => 0,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => 'GET',
CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer '.$TOKEN_MERCADO_PAGO,
),
));

$response_original = curl_exec($curl);

curl_close($curl);
$response = json_decode($response_original, true);

if(empty($response)){ die("dados_vazios"); }

$idcobranca = $response["collection"]["external_reference"];
$status = $response["collection"]["status"];
$payment_method_id = $response["collection"]["payment_method_id"];
$transaction_amount= $response["collection"]["transaction_amount"];
$id_mercado_pago = $response["collection"]["id"];

if($status == "approved"){
		$valor_pago = $transaction_amount;
		$query = $pdo->query("UPDATE agendamentos_temp SET valor_pago = '$valor_pago' where ref_pix = '$id_mercado_pago'");

    	//$ref_pix = $id_mercado_pago;
        
        //$forma_pgto = $payment_method_id;
        //require("pagamento_aprovado.php");

    echo json_encode(array("status" => "pago"));
    die;

}
