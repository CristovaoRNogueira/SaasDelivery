<?php

if ($api_whatsapp == 'menuia') {
  $mensagem_whatsapp = str_replace("%0A", "\n", $mensagem);

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
      'message' => $mensagem_whatsapp,
      'sandbox' => 'false'
    ),
  )
  );

  $response = curl_exec($curl);

  curl_close($curl);
  //echo $response;

  $response = json_decode($response, true);
  $status_mensagem = $response['message'] ?? 500;
  echo $status_mensagem;
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

  echo $result;

}


if ($api_whatsapp == 'newtek') {
  $mensagem_whatsapp = str_replace("%0A", "\n", $mensagem);
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
    CURLOPT_POSTFIELDS => json_encode(
      array(
        "instancia" => $instancia_whatsapp,
        "token" => $token_whatsapp,
        "mensagem" => $mensagem,
        "para" => array($telefone_envio),
        "delay" => "1"
      )
    ),
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json'
    ),
  )
  );

  $response = curl_exec($curl);

  curl_close($curl);
  echo $response;
}




/*
|--------------------------------------------------------------------------
| EVOLUTION API
|--------------------------------------------------------------------------
*/
if ($api_whatsapp === 'evolution') {

   @$mensagem_whatsapp = str_replace("%0A", "\n", $mensagem); 

    $retorno = [
        'api'  => 'evolution',
        'ok'   => false,
        'msg'  => 'WhatsApp (Evolution): não enviado.',
        'http' => null,
        'raw'  => null,
        'url'  => null
    ];

    // campos obrigatórios
    $url_base = rtrim(trim($url_evolution ?? ''), '/');
    $instance = trim($instancia_whatsapp ?? '');
    $api_key  = trim($token_whatsapp ?? '');

    if ($url_base === '' || $instance === '' || $api_key === '') {
        $retorno['msg'] = 'URL, Instance ou ApiKey não configurados.';
    } else {

        
        // 🔥 endpoint CORRETO da Evolution
        $url = "{$url_base}/message/sendText/{$instance}";
        $retorno['url'] = $url;

        $payload = [
            "number" => $telefone_envio,
            "text"   => $mensagem_whatsapp
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                "Content-Type: application/json",
                "apikey: {$api_key}"
            ],
            CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($ch);
        $http     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err      = curl_error($ch);
        curl_close($ch);

        $retorno['http'] = $http;
        $retorno['raw']  = $response;

        if ($response === false || $err) {
            //echo "Erro cURL: " . ($err ?: 'sem detalhes');
        } else {
            if ($http >= 200 && $http < 300) {
                $retorno['ok']  = true;
                //echo 'WhatsApp (Evolution): enviado com sucesso.';
            } else {
                //echo "Erro HTTP {$http}";
            }
        }
    }

    
}

?>