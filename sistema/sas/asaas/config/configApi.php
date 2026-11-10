<?php

//trazer as configurações
$query5 = $pdo->query("SELECT * from config where empresa is null or empresa = 0");
$res5 = $query5->fetchAll(PDO::FETCH_ASSOC);
$linhas5 = @count($res5);
if ($linhas5 > 0) {	
	$dados_pagamento_sas = $res5[0]['dados_pagamento'];		
	$multa_atraso = $res5[0]['multa_atraso'];
	$juros_atraso = $res5[0]['juros_atraso'];	
	$chave_api_asaas = $res5[0]['chave_api_asaas'];	
	$api_pagamento = $res5[0]['api_pagamento'];	
}else{
	$dados_pagamento_sas = "";
	$chave_api_asaas = "";
}

$access_token   = $chave_api_asaas;
$host_dir       = $url_sistema.'/sas/asaas/'; //seu dominio e a pasta onde o codigo foi colocado, exemplo: seusite.com.br/checkout
?>