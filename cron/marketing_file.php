<?php

if ($api_whatsapp == 'menuia') {

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
    'file' => $url_envio,
    'licence' => 'hugocursos',
    'sandbox' => 'false'
    ),
  ));

  $response = curl_exec($curl);

  curl_close($curl);
  echo $response;

}


if ($api_whatsapp == 'wm') {

	$mensagem = '';
	$data = array('instance' => "$instancia_whatsapp",
    'to' => "$telefone_envio",
    'token' => "$token_whatsapp",
    'message' => "$mensagem",
    'url' => "$url_envio");

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://api.wordmensagens.com.br/send-docnew',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $data,
  ));

  $response = curl_exec($curl);

  curl_close($curl);

  $response = json_decode($response, true);

  if($response['erro'] == false){
    //echo "Enviado com sucesso";
  }else if($response['erro'] == true){
    //echo "Erro no Envio > ".$response['message'];
  }

}




if ($api_whatsapp == 'newtek') {
  $mensagem = '';
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://webapi.newteksoft.com.br/send-docnew',
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







// espera existir:
// $api_whatsapp, $token_whatsapp, $instancia_whatsapp, $telefone_envio, $mensagem, $url_envio
// e no caso evolution: $url_evolution

if (!function_exists('detectMediaByUrl')) {
  function detectMediaByUrl($url) {
      $name = basename(parse_url($url, PHP_URL_PATH) ?? $url);
      $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));

      $mediatype = 'document';
      $mimetype  = 'application/octet-stream';

      if (in_array($ext, ['jpg','jpeg'])) { $mediatype = 'image';   $mimetype = 'image/jpeg'; }
      elseif ($ext === 'png')            { $mediatype = 'image';   $mimetype = 'image/png'; }
      elseif ($ext === 'webp')           { $mediatype = 'image';   $mimetype = 'image/webp'; }
      elseif ($ext === 'gif')            { $mediatype = 'image';   $mimetype = 'image/gif'; }
      elseif ($ext === 'mp4')            { $mediatype = 'video';   $mimetype = 'video/mp4'; }
      elseif ($ext === 'mov')            { $mediatype = 'video';   $mimetype = 'video/quicktime'; }
      elseif ($ext === 'pdf')            { $mediatype = 'document';$mimetype = 'application/pdf'; }
      elseif (in_array($ext, ['doc','docx'])) { $mediatype = 'document'; $mimetype = 'application/msword'; }
      elseif (in_array($ext, ['xls','xlsx'])) { $mediatype = 'document'; $mimetype = 'application/vnd.ms-excel'; }
      elseif (in_array($ext, ['ppt','pptx'])) { $mediatype = 'document'; $mimetype = 'application/vnd.ms-powerpoint'; }
      elseif ($ext === 'mp3')            { $mediatype = 'document';$mimetype = 'audio/mpeg'; }
     elseif ($ext === 'ogg') { 
    $mediatype = 'document'; 
    $mimetype = 'audio/ogg'; 
}
      elseif ($ext === 'wav')            { $mediatype = 'document';$mimetype = 'audio/wav'; }

      if ($name === '' || $name === '.' || $name === '/') {
          $name = 'arquivo.' . ($ext ?: 'bin');
      }

      return [$mediatype, $mimetype, $name];
  }
}



/*
|--------------------------------------------------------------------------
| EVOLUTION (sendMedia)
|--------------------------------------------------------------------------
*/
if ($api_whatsapp === 'evolution') {

    $url_base = rtrim(trim($url_evolution ?? ''), '/');
    $instance = trim($instancia_whatsapp ?? '');
    $api_key  = trim($token_whatsapp ?? '');

    if ($url_base === '' || $instance === '' || $api_key === '' || $url_envio === '') {
        $status_mensagem = "Evolution: URL/Instance/ApiKey/Arquivo não configurado.";
    } else {

        // pega extensão do arquivo pela URL (sem querystring)
        $path = parse_url($url_envio, PHP_URL_PATH);
        $nome = basename($path ?: $url_envio);
        $ext  = strtolower(pathinfo($nome, PATHINFO_EXTENSION));

        // ============================
        // 1) ÁUDIO REAL (mp3/ogg/wav)
        // ============================
        if (in_array($ext, ['mp3','ogg','wav'])) {

            // endpoint específico de áudio (tenta tocar como áudio)
            $url = "{$url_base}/message/sendWhatsAppAudio/{$instance}";

            $payload = [
                "number" => $telefone_envio,
                "audio"  => $url_envio, // URL pública do áudio
                "delay"  => 1
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
                CURLOPT_TIMEOUT        => 60,
            ]);

            $response = curl_exec($ch);
            $http     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err      = curl_error($ch);
            curl_close($ch);

            // fallback: se não deu certo, manda como "arquivo" pelo sendMedia
            if ($response === false || $err || $http < 200 || $http >= 300) {

                list($mediatype, $mimetype, $fileName) = detectMediaByUrl($url_envio);

                // força como document pra garantir envio
                $mediatype = 'document';
                if ($ext === 'mp3') $mimetype = 'audio/mpeg';
                if ($ext === 'ogg') $mimetype = 'audio/ogg';
                if ($ext === 'wav') $mimetype = 'audio/wav';

                $url = "{$url_base}/message/sendMedia/{$instance}";

                $payload2 = [
                    "number"    => $telefone_envio,
                    "mediatype" => $mediatype,
                    "mimetype"  => $mimetype,
                    "caption"   => " ",        // vazio pra não repetir texto
                    "media"     => $url_envio,
                    "fileName"  => $fileName
                ];

                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST           => true,
                    CURLOPT_HTTPHEADER     => [
                        "Content-Type: application/json",
                        "apikey: {$api_key}"
                    ],
                    CURLOPT_POSTFIELDS     => json_encode($payload2, JSON_UNESCAPED_UNICODE),
                    CURLOPT_CONNECTTIMEOUT => 10,
                    CURLOPT_TIMEOUT        => 60,
                ]);

                $response = curl_exec($ch);
                $http     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $err      = curl_error($ch);
                curl_close($ch);
            }

        // ============================
        // 2) OUTROS ARQUIVOS (img/pdf/video) -> sendMedia
        // ============================
        } else {

            list($mediatype, $mimetype, $fileName) = detectMediaByUrl($url_envio);

            $url = "{$url_base}/message/sendMedia/{$instance}";

            $payload = [
                "number"    => $telefone_envio,
                "mediatype" => $mediatype,
                "mimetype"  => $mimetype,
                "caption"   => " ",        // vazio pra não repetir texto
                "media"     => $url_envio,
                "fileName"  => $fileName
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
                CURLOPT_TIMEOUT        => 60,
            ]);

            $response = curl_exec($ch);
            $http     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err      = curl_error($ch);
            curl_close($ch);
        }

        // ============================
        // STATUS
        // ============================
        if ($response === false || $err) {
            $status_mensagem = "Erro Evolution: " . ($err ?: 'sem detalhes');
        } else {
            $status_mensagem = ($http >= 200 && $http < 300)
                ? "Mensagem enviada com sucesso."
                : "Erro Evolution HTTP {$http}";
        }
    }
}


?>