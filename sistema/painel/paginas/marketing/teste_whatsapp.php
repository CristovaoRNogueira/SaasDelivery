<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
$id_usuario = $_SESSION['id'];



$msg = $_POST['msg'];
$arquivo = $_POST['arquivo'];
$audio = $_POST['audio'];
$documento = $_POST['documento'];


$msg = html_entity_decode($msg);


$msg = preg_replace([
    '/<strong[^>]*>(.*?)<\/strong>/i',  // negrito
    '/<p[^>]*>|<\/p>|<br[^>]*>/i'       // quebras de linha
], [
    '*$1*',
    "\n"
], $msg);


$msg = strip_tags($msg);
$msg = preg_replace('/\n{3,}/', "\n\n", trim($msg));




$query = $pdo->query("SELECT * FROM usuarios where id = '$id_usuario'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
$telefone_envio = $res[0]['telefone'];




// Formatar o telefone removendo caracteres especiais e adicionando o código do país
$telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone_envio);




if ($api_whatsapp == 'menuia') {
    $msg = str_replace("%0A", "\n", $msg);

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
            'message' => $msg,
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
        'instance' => $instancia,
        'to' => $telefone_envio,
        'token' => $token,
        'message' => $msg
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
    $mensagem_whatsapp = str_replace("%0A", "\n", $mensagem_whatsapp);
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
            "instancia" => $instancia,
            "token" => $token,
            "mensagem" => $msg,
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


echo 'Enviado';
?>