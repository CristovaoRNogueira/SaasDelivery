<?php
@session_start();
$id_usuario = @$_SESSION['id'];
require_once '../../conexao.php';

$id = $_POST['id'];
$mensagem = $_POST['mensagem'];
$tipo = $_POST['tipo'];


    $query = $pdo->query("SELECT * FROM clientes where id = '$id'");
    $res = $query->fetchAll(PDO::FETCH_ASSOC);
    if (@count($res) > 0) {
        $telefone_envio = $res[0]['telefone'];
    }else{
        echo 'Nenhum cliente encontrado!';
        die;
    }


$telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone_envio);

if ($api_whatsapp == 'menuia') {
    @$mensagem = str_replace("%0A", "\n", $mensagem);

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://chatbot.menuia.com/api/create-message',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
            'appkey' => $token_whatsapp,
            'authkey' => $instancia_whatsapp,
            'to' => $telefone_envio,
            'message' => $mensagem,
            'licence' => 'hugocursos',
            'sandbox' => 'false'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    //echo $response;
}



if ($api_whatsapp == 'wm') {
    $url = "http://api.wordmensagens.com.br/send-text";

    $data = array(
        'instance' => $instancia_whatsapp,
        'to' => $telefone_envio,
        'token' => $token_whatsapp,
        'message' => $mensagem
    );

    $options = array(
        'http' => array(
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );

    $stream = stream_context_create($options);

    $result = @file_get_contents($url, false, $stream);

    //echo $result;

}


if ($api_whatsapp == 'newtek') {
    $mensagem = str_replace("%0A", "\n", $mensagem);
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://webapi.newteksoft.com.br/enviar-texto',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode(array(
            "instancia" => $instancia_whatsapp,
            "token" => $token_whatsapp,
            "mensagem" => $mensagem,
            "para" => array($telefone_envio),
            "delay" => "1"
        )),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    //echo $response;
}


echo 'Mensagem enviada com sucesso!';
?>