<?php 
@session_start();
$id_empresa = @$_SESSION['empresa'];

$recursos_marketing_whats = 'ocultar';
$recursos_pdv_balcao = 'ocultar';
$recursos_pedidos_na_mesa = 'ocultar';


$query = $pdo->query("SELECT * FROM clientes_recursos where empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);

if($total_reg > 0){
	for($i=0; $i < $total_reg; $i++){

		$recurso = @$res[$i]['recurso'];

		$query2 = $pdo->query("SELECT * FROM recursos where id = '$recurso'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);

		$chave = @$res2[0]['chave'];

		if($chave == 'marketing_whats'){
			$recursos_marketing_whats = '';
		}

		if($chave == 'pdv_balcao'){
			$recursos_pdv_balcao = '';
		}

		if($chave == 'pedidos_na_mesa'){
			$recursos_pedidos_na_mesa = '';
		}

		

	}
}
?>