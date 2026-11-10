<?php 

//trazer as configurações
$query5 = $pdo->query("SELECT * from config where empresa = '0' or empresa is null");
$res5 = $query5->fetchAll(PDO::FETCH_ASSOC);
$linhas5 = @count($res5);
if ($linhas5 > 0) {	
	$dados_pagamento_sas = $res5[0]['dados_pagamento'];	
	$access_token_mp_sas = $res5[0]['access_token'];	
	$public_key_mp_sas = $res5[0]['public_key'];	
}else{
	$dados_pagamento_sas = "";
	$access_token_mp_sas = "";
	$public_key_mp_sas = "";
}

$access_token = $access_token_mp_sas;
$public_key     = $public_key_mp_sas;
 ?>