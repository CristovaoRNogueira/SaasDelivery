<?php

// Mostrar ou ocultar colunas
$mostrar_delivery = ($tipo == "" || $tipo == "Delivery");
$mostrar_balcao   = ($tipo == "" || $tipo == "Balcão");
$mostrar_mesa     = ($tipo == "" || $tipo == "Mesa");

if($tipo == ""){
	$sql_tipo = ' ';
}else{
	if($tipo == "Delivery"){
		$tipo = '';
	}
	$sql_tipo = " and tipo_pedido = '$tipo' ";
}

if($horaInicial == "" || $horaFinal == ""){
	$sql_hora = ' ';
}else{	
	if($dataInicial == $dataFinal){
		$sql_hora = " AND hora >= '$horaInicial' AND hora <= '$horaFinal' ";
	}else{
		$sql_hora = " AND ( (data = '$dataInicial' AND hora >= '$horaInicial')  OR (data = '$dataFinal' AND hora <= '$horaFinal')) ";
	}
	
}


$dataInicialF = implode('/', array_reverse(explode('-', $dataInicial)));
$dataFinalF = implode('/', array_reverse(explode('-', $dataFinal)));

if ($dataInicial == $dataFinal) {
	$texto_apuracao = 'APURADO EM ' . $dataInicialF;
} else if ($dataInicial == '1980-01-01') {
	$texto_apuracao = 'APURADO EM TODO O PERÍODO';
} else {
	$texto_apuracao = 'APURAÇÃO DE ' . $dataInicialF . ' ATÉ ' . $dataFinalF;
}


$status_arquivado = $status === 'Arquivados';
if ($status == '') {
	$acao_rel = '';
} else {
	if ($status_arquivado) {
		$acao_rel = ' Arquivadas ';
	} else if ($status == 'Finalizado') {
		$acao_rel = ' Finalizadas ';
	} else {
		$acao_rel = ' Canceladas ';
	}
}

if ($forma_pgto == '') {
	$texto_tabela = '';
} else {
	$texto_tabela = ' ' . $forma_pgto;
}


$sql_arquivado = $status_arquivado ? " and arquivado = 'Sim' " : " and (arquivado IS NULL or arquivado != 'Sim') ";
$status = $status_arquivado ? '%' : '%' . $status . '%';
$forma_pgto = '%' . $forma_pgto . '%';


$total_delivery_liquidoF = 0;
$total_balcao_liquidoF = 0;
$total_mesas_liquidoF = 0;

?>

<!DOCTYPE html>
<html>

<head>

	<style>
		@import url('https://fonts.cdnfonts.com/css/tw-cen-mt-condensed');

		@page {
			margin: 145px 20px 25px 20px;
		}

		#header {
			position: fixed;
			left: 0px;
			top: -110px;
			bottom: 100px;
			right: 0px;
			height: 35px;
			text-align: center;
			padding-bottom: 100px;
		}

		#content {
			margin-top: 0px;
		}

		#footer {
			position: fixed;
			left: 0px;
			bottom: -60px;
			right: 0px;
			height: 80px;
		}

		#footer .page:after {
			content: counter(page, my-sec-counter);
		}

		body {
			font-family: 'Tw Cen MT', sans-serif;
		}

		.marca {
			position: fixed;
			left: 50;
			top: 100;
			width: 80%;
			opacity: 10%;
		}
	</style>

</head>

<body>
	<?php
	if ($marca_dagua == 'Sim' and $tipo_rel != 'HTML') { ?>
		<img class="marca" src="<?php echo $url_sistema ?>sistema/img/<?php echo $logo_sistema ?>">
	<?php } ?>


	<div id="header">

		<div style="border-style: solid; font-size: 10px; height: 55px;">
			<table style="width: 100%; border: 0px solid #ccc;">
				<tr>
					<td style="border: 1px; solid #000; width: 25%; text-align: left;">
						<img style="margin-top: 0px; margin-left: 7px;" id="imag"
							src="<?php echo $url_sistema ?>sistema/img/<?php echo $logo_sistema ?>" width="60%">
					</td>

					<td style="text-align: center; font-size: 10px; width: 40%;">

						<b><?php echo mb_strtoupper($nome_sistema) ?></b><br>
						<b>CNPJ: </b><?php echo mb_strtoupper($cnpj_sistema) ?><br>
						<b>INSTAGRAM:</b> <?php echo mb_strtoupper($instagram_sistema) ?><br>
						<?php echo mb_strtoupper($endereco_sistema) ?>


					</td>
					<td style="width: 30%; text-align: right; font-size: 11px;padding-right: 10px;">
						<b><big>Relatório de Vendas <?php echo $acao_rel ?> <?php echo $texto_tabela ?></big></b><br>
						<small><?php echo mb_strtoupper($data_hoje) ?></small>
					</td>
				</tr>
			</table>
		</div>

		<br>

		<?php
		$total_vendas = 0;
		$total_vendasF = 0;
		$total_canceladas = 0;
		$total_canceladasF = 0;
		$total_delivery = 0;
		$total_deliveryF = 0;
		$total_mesas = 0;
		$total_mesasF = 0;
		$total_balcao = 0;
		$total_balcaoF = 0;

		$total_delivery_custo = 0;
		$total_deliveryF_custo = 0;
		$total_mesas_custo = 0;
		$total_mesasF_custo = 0;
		$total_balcao_custo = 0;
		$total_balcaoF_custo = 0;

		$total_delivery_liquido = 0;
		$total_deliveryF_liquido = 0;
		$total_mesas_liquido = 0;
		$total_mesasF_liquido = 0;
		$total_balcao_liquido = 0;
		$total_balcaoF_liquido = 0;
		$query = $pdo->query("SELECT * from vendas where (data >= '$dataInicial' and data <= '$dataFinal') and pago = 'Sim' and status LIKE '$status' and tipo_pgto LIKE '$forma_pgto' and empresa = '$id_empresa' $sql_arquivado $sql_tipo $sql_hora order by data asc, hora asc ");
		$res = $query->fetchAll(PDO::FETCH_ASSOC);
		$total_reg = count($res);
		require_once(__DIR__ . '/pagamentos_venda.php');
		$pagamentos_por_venda = carregarPagamentosPorVenda($pdo, $res, (int) $id_empresa);
		if ($total_reg > 0) {
		?>


			<table id="cabecalhotabela"
				style="border-bottom-style: solid; font-size: 8px; margin-bottom:10px; width: 100%; table-layout: fixed;">
				<thead>

					<tr id="cabeca" style="margin-left: 0px; background-color:#CCC">
						<td style="width:30%">CLIENTE</td>	
						<td style="width:12%">REFERÊNCIA</td>						
						<td style="width:10%">R$ TOTAL</td>	
						<td style="width:9%">CUSTO</td>					
						<td style="width:20%">FORMA PGTO</td>
						<td style="width:10%">DATA</td>
						<td style="width:9%">HORA</td>

					</tr>
				</thead>
			</table>
	</div>

	<div id="footer" class="row">
		<hr style="margin-bottom: 0;">
		<table style="width:100%;">
			<tr style="width:100%;">
				<td style="width:60%; font-size: 10px; text-align: left;"><?php echo $nome_sistema ?> Telefone:
					<?php echo $telefone_sistema ?>
				</td>
				<td style="width:40%; font-size: 10px; text-align: right;">
					<p class="page">Página </p>
				</td>
			</tr>
		</table>
	</div>

	<div id="content" style="margin-top: -5;">



		<table style="width: 100%; table-layout: fixed; font-size:8px; text-transform: uppercase;">
			<thead>
			<tbody>


				<?php
				$totais_por_pgto = [];
				for ($i = 0; $i < $total_reg; $i++) {
					foreach ($res[$i] as $key => $value) {
					}
					$id = $res[$i]['id'];
					$cliente = $res[$i]['cliente'];
					$valor = $res[$i]['valor'];
					$total_pago = $res[$i]['total_pago'];
					$troco = $res[$i]['troco'];
					$data = $res[$i]['data'];
					$hora = $res[$i]['hora'];
					$status = $res[$i]['status'];
					$pago = $res[$i]['pago'];
					$obs = $res[$i]['obs'];
					$taxa_entrega = $res[$i]['taxa_entrega'];
					$tipo_pgto_original = (string) $res[$i]['tipo_pgto'];
					$usuario_baixa = $res[$i]['usuario_baixa'];
					$mesa = $res[$i]['mesa'];
					$nome_do_cliente = $res[$i]['nome_cliente'];
					$tipo_pedido = $res[$i]['tipo_pedido'];
					$custo = $res[$i]['custo'];
					$pagamentos_venda = obterPagamentosVenda($pagamentos_por_venda, (int) $id, $tipo_pgto_original, (float) $total_pago);
					$tipo_pgto = descreverPagamentosVenda($pagamentos_venda);

					$valorF = number_format($valor, 2, ',', '.');
					$total_pagoF = number_format($total_pago, 2, ',', '.');
					$trocoF = number_format($troco, 2, ',', '.');
					$taxa_entregaF = number_format($taxa_entrega, 2, ',', '.');
					$dataF = implode('/', array_reverse(explode('-', $data)));
					//$horaF = date("H:i", strtotime($hora));	



					$query2 = $pdo->query("SELECT * FROM usuarios where id = '$usuario_baixa'");
					$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
					$total_reg2 = @count($res2);
					if ($total_reg2 > 0) {
						$nome_usuario_pgto = $res2[0]['nome'];
					} else {
						$nome_usuario_pgto = 'Nenhum!';
					}


					$query2 = $pdo->query("SELECT * FROM clientes where id = '$cliente'");
					$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
					$total_reg2 = @count($res2);
					if ($total_reg2 > 0) {
						$nome_cliente = $res2[0]['nome'];
					} else {
						$nome_cliente = 'Nenhum!';
					}

					if ($mesa != '0' and $mesa != '') {
						$nome_cliente = 'Mesa: ' . $mesa;
					}

					if ($nome_do_cliente != "") {
						$nome_cliente = $nome_do_cliente;
					}


					if ($status == 'Finalizado') {
						$classe_alerta = 'text-verde';
						$total_vendas += $valor;
						$classe_linha = '';
						$classe_square = 'verde';
						$imagem = 'verde.jpg';
					} else if ($status == 'Cancelado') {
						$classe_alerta = 'text-danger';
						$total_canceladas += $valor;
						$classe_linha = 'text-muted';
						$classe_square = 'text-danger';

						$imagem = 'vermelho.jpg';
					} else {
						$classe_alerta = 'text-primary';
						$total_vendas += $valor;
						$classe_linha = '';
						$classe_square = 'verde';
						$imagem = 'verde.jpg';
					}




					$total_vendasF = number_format($total_vendas, 2, ',', '.');
					$total_canceladasF = number_format($total_canceladas, 2, ',', '.');
					$custoF = number_format($custo, 2, ',', '.');


					if($tipo_pedido == ""){
						$total_delivery += $total_pago;
						$total_delivery_custo += $custo;
						$total_delivery_liquido = $total_delivery - $total_delivery_custo;
					}

					if($tipo_pedido == "Balcão"){
						$total_balcao += $total_pago;
						$total_balcao_custo += $custo;
						$total_balcao_liquido = $total_balcao - $total_balcao_custo;
					}


					if($tipo_pedido == "Mesa"){
						$total_mesas += $total_pago;
						$total_mesas_custo += $custo;
						$total_mesas_liquido = $total_mesas - $total_mesas_custo;
					}



					$total_deliveryF = number_format($total_delivery, 2, ',', '.');
					$total_balcaoF = number_format($total_balcao, 2, ',', '.');
					$total_mesasF = number_format($total_mesas, 2, ',', '.');

					$total_deliveryF_custo = number_format($total_delivery_custo, 2, ',', '.');
					$total_balcaoF_custo = number_format($total_balcao_custo, 2, ',', '.');
					$total_mesasF_custo = number_format($total_mesas_custo, 2, ',', '.');

					$total_delivery_liquidoF = number_format($total_delivery_liquido, 2, ',', '.');
					$total_balcao_liquidoF = number_format($total_balcao_liquido, 2, ',', '.');
					$total_mesas_liquidoF = number_format($total_mesas_liquido, 2, ',', '.');


					if ($status != 'Cancelado') {
						foreach ($pagamentos_venda as $pagamento_venda) {
							$pgto_key = $pagamento_venda['forma'];
							$totais_por_pgto[$pgto_key] = ($totais_por_pgto[$pgto_key] ?? 0) + (float) $pagamento_venda['valor'];
						}
					}
				?>

					<tr class="">
						<td style="width:30%" align="left">
							<img src="<?php echo $url_sistema ?>/img/<?php echo $imagem ?>" width="11px" height="11px"
								style="margin-top:3px">					
							<?php echo $nome_cliente ?>
						</td>		
						<td style="width:12%"><?php echo $tipo_pedido ?></td>				
						<td style="width:10%">R$ <?php echo $total_pagoF ?></td>
						<td style="width:9%" style="color:red">R$ <?php echo $custoF ?></td>					
						<td style="width:20%"><?php echo $tipo_pgto ?></td>
						<td style="width:10%;"><?php echo $dataF ?> </td>
						<td style="width:9%;"><?php echo $hora ?> </td>

					</tr>

			<?php }
			} ?>
			</tbody>

			</thead>
		</table>


	</div>
	<hr>

<table>
	<thead>
	<tbody>

		<tr>
			<td style="width:180px;"></td>

			<?php if($mostrar_delivery){ ?>
				<td style="font-size: 10px; width:180px; text-align:right;">
					<b>DELIVERY:</b> <span style="color:green">R$ <?= $total_deliveryF ?></span>
				</td>
			<?php } ?>

			<?php if($mostrar_balcao){ ?>
				<td style="font-size: 10px; width:180px; text-align:right;">
					<b>BALCÃO:</b> <span style="color:green">R$ <?= $total_balcaoF ?></span>
				</td>
			<?php } ?>

			<?php if($mostrar_mesa){ ?>
				<td style="font-size: 10px; width:180px; text-align:right;">
					<b>MESAS:</b> <span style="color:green">R$ <?= $total_mesasF ?></span>
				</td>
			<?php } ?>
		</tr>

		<tr>
			<td></td>

			<?php if($mostrar_delivery){ ?>
				<td style="font-size:10px; text-align:right;">
					<b>CUSTO:</b> <span style="color:red">R$ <?= $total_deliveryF_custo ?></span>
				</td>
			<?php } ?>

			<?php if($mostrar_balcao){ ?>
				<td style="font-size:10px; text-align:right;">
					<b>CUSTO:</b> <span style="color:red">R$ <?= $total_balcaoF_custo ?></span>
				</td>
			<?php } ?>

			<?php if($mostrar_mesa){ ?>
				<td style="font-size:10px; text-align:right;">
					<b>CUSTO:</b> <span style="color:red">R$ <?= $total_mesasF_custo ?></span>
				</td>
			<?php } ?>
		</tr>

		<tr>
			<td></td>

			<?php if($mostrar_delivery){ ?>
				<td style="font-size:10px; text-align:right;">
					<b>LÍQUIDO:</b> <span style="color:blue">R$ <?= $total_delivery_liquidoF ?></span>
				</td>
			<?php } ?>

			<?php if($mostrar_balcao){ ?>
				<td style="font-size:10px; text-align:right;">
					<b>LÍQUIDO:</b> <span style="color:blue">R$ <?= $total_balcao_liquidoF ?></span>
				</td>
			<?php } ?>

			<?php if($mostrar_mesa){ ?>
				<td style="font-size:10px; text-align:right;">
					<b>LÍQUIDO:</b> <span style="color:blue">R$ <?= $total_mesas_liquidoF ?></span>
				</td>
			<?php } ?>
		</tr>

	</tbody>
	</thead>
</table>




<?php if(!empty($totais_por_pgto)){ 
    // (Opcional) ordenar do maior pro menor
    arsort($totais_por_pgto);
?>
    <hr>
    <div style="width:100%; margin-top:5px;">
        <div style="font-size:11px; font-weight:bold; margin-bottom:6px;">RESUMO POR FORMA DE PAGAMENTO</div>

        <table style="width:100%; border-collapse:collapse; font-size:10px;">
            <?php foreach($totais_por_pgto as $fp => $vl){ ?>
                <tr>
                    <td style="text-align:left; padding:3px 0; width:70%;">
                        <b><?= mb_strtoupper($fp) ?>:</b>
                    </td>
                    <td style="text-align:right; padding:3px 0; width:30%;">
                        <span style="color:green">R$ <?= number_format($vl, 2, ',', '.') ?></span>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
<?php } ?>



</body>

</html>
