<?php 
require_once('../../sistema/conexao.php');

$id = $_POST['id'];
$query = $pdo->query("SELECT * from vendas where id = '$id' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$status = @$res[0]['status'];

$imagem_iniciado = 'no_check.png';
if($status == 'Iniciado'){
	$imagem_iniciado = 'check.png';
}

$imagem_aceito = 'no_check.png';
if($status == 'Aceito'){
	$imagem_aceito = 'check.png';
	$imagem_iniciado = 'check.png';
}

$imagem_preparando = 'no_check.png';
if($status == 'Preparando'){
	$imagem_preparando = 'check.png';
	$imagem_aceito = 'check.png';
	$imagem_iniciado = 'check.png';
}



$imagem_entrega = 'no_check.png';
if($status == 'Entrega'){
	$imagem_entrega = 'check.png';
	$imagem_preparando = 'check.png';
	$imagem_aceito = 'check.png';
	$imagem_iniciado = 'check.png';
}

$imagem_finalizado = 'no_check.png';
if($status == 'Finalizado'){
	$imagem_finalizado = 'check.png';
	$imagem_preparando = 'check.png';
	$imagem_aceito = 'check.png';
	$imagem_iniciado = 'check.png';
	$imagem_entrega = 'check.png';
}

echo '<p style="border-radius: 50px; background: #FFF; width:100%; height:35px; line-height: 30px; padding-left:15px; margin-bottom:8px">
			<span style="margin-right: 10px"><img src="'.$url_sistema.'img/'.$imagem_iniciado.'" width="20px"></span><span>Pedido Enviado</span>
		</p>';

echo '<p style="border-radius: 50px; background: #FFF; width:100%; height:35px; line-height: 30px; padding-left:15px; margin-bottom:8px">
			<span style="margin-right: 10px"><img src="'.$url_sistema.'img/'.$imagem_aceito.'" width="20px"></span><span>Pedido Aceito</span>
		</p>';

echo '<p style="border-radius: 50px; background: #FFF; width:100%; height:35px; line-height: 30px; padding-left:15px; margin-bottom:8px">
			<span style="margin-right: 10px"><img src="'.$url_sistema.'img/'.$imagem_preparando.'" width="20px"></span><span>Pedido em Produção</span>
		</p>';

echo '<p style="border-radius: 50px; background: #FFF; width:100%; height:35px; line-height: 30px; padding-left:15px; margin-bottom:8px">
			<span style="margin-right: 10px"><img src="'.$url_sistema.'img/'.$imagem_entrega.'" width="20px"></span><span>Em Rota de Entrega</span>
		</p>';

echo '<p style="border-radius: 50px; background: #FFF; width:100%; height:35px; line-height: 30px; padding-left:15px; margin-bottom:8px">
			<span style="margin-right: 10px"><img src="'.$url_sistema.'img/'.$imagem_finalizado.'" width="20px"></span><span>Entregue</span>
		</p>';



 ?>