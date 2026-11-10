<?php
@session_start();

require_once("../../../conexao.php");
$id_empresa = 0;


$query = $pdo->query("
    SELECT *
    FROM dispositivos
    WHERE empresa = '$id_empresa'
    AND status_api = 'conectado'
");

$res = $query->fetchAll(PDO::FETCH_ASSOC);

foreach($res as $item){

    $appkey = $item['appkey'];

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://chatbot.menuia.com/api/developer",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'authkey' => $instancia_whatsapp,
            'message' => $appkey,
            'licence' => 'hugocursos',
            'checkDispositivo' => 'true'
        ]
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    $response = json_decode($response, true);

    if(
        !isset($response['status']) ||
        $response['status'] != 200
    ){

        $pdo->query("
            UPDATE dispositivos
            SET status_api = 'desconectado'
            WHERE id = '".$item['id']."'
        ");

    }
}

echo 'OK';