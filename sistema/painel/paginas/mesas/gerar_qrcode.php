<?php 
require_once("../../../conexao.php");
$tabela = 'mesas';

$id = $_POST['id'];
$nome_mesa = $_POST['nome'];

$url_mesa = $url_sistema.'mesa/'.$id;

//gerar qrcode

echo '<img src="https://api.qrserver.com/v1/create-qr-code/?size=350x350&data='.$url_mesa.'" alt="QR Code">
<br><br>
<p>Mesa: '.$nome_mesa.'</p>
';

?>