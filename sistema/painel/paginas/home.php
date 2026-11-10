<?php
$pag = 'home';


$sql_usuario = " ";
$sql_usuario_lanc = " ";

//total clientes

$query = $pdo->query("SELECT * from clientes where empresa = '$id_empresa' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_clientes = @count($res);

//total clientes mes
$query = $pdo->query("SELECT * from clientes where data_cad >= '$data_inicio_mes' and data_cad <= '$data_final_mes' and empresa = '$id_empresa' $sql_usuario");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_clientes_mes = @count($res);

$total_pagar_vencidas = 0;
$query = $pdo->query("SELECT * from pagar where vencimento < curDate() and pago != 'Sim' and empresa = '$id_empresa' $sql_usuario_lanc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$contas_pagar_vencidas = @count($res);
for ($i = 0; $i < $contas_pagar_vencidas; $i++) {
	$valor_1 = $res[$i]['valor'];
	$total_pagar_vencidas += $valor_1;
}
$total_pagar_vencidasF = @number_format($total_pagar_vencidas, 2, ',', '.');



$total_receber_vencidas = 0;
$query = $pdo->query("SELECT * from receber where vencimento < curDate() and pago != 'Sim' and (arquivado IS NULL or arquivado != 'Sim') and empresa = '$id_empresa' $sql_usuario_lanc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$contas_receber_vencidas = @count($res);
for ($i = 0; $i < $contas_receber_vencidas; $i++) {
	$valor_2 = $res[$i]['valor'];
	$total_receber_vencidas += $valor_2;
}
$total_receber_vencidasF = @number_format($total_receber_vencidas, 2, ',', '.');


//total recebidas mes
$total_recebidas_mes = 0;
$query = $pdo->query("SELECT * from receber where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and (arquivado IS NULL or arquivado != 'Sim') and empresa = '$id_empresa' $sql_usuario_lanc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$contas_recebidas_mes = @count($res);
for ($i = 0; $i < $contas_recebidas_mes; $i++) {
	$valor_3 = $res[$i]['valor'];
	$total_recebidas_mes += $valor_3;
}
$total_recebidas_mesF = @number_format($total_recebidas_mes, 2, ',', '.');

//total contas pagas mes
$total_pagas_mes = 0;
$query = $pdo->query("SELECT * from pagar where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and empresa = '$id_empresa' $sql_usuario_lanc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$contas_pagas_mes = @count($res);
for ($i = 0; $i < $contas_pagas_mes; $i++) {
	$valor_4 = $res[$i]['valor'];
	$total_pagas_mes += $valor_4;
}
$total_pagas_mesF = @number_format($total_pagas_mes, 2, ',', '.');


$total_saldo_mes = $total_recebidas_mes - $total_pagas_mes;
$total_saldo_mesF = @number_format($total_saldo_mes, 2, ',', '.');
if ($total_saldo_mes >= 0) {
	$classe_saldo = 'iconSusses';
	$classe_saldo2 = 'iconBgSusses';
	$classe_icon_dia = 'fa fa-caret-up text-success';
} else {
	$classe_saldo = 'iconDanger';
	$classe_saldo2 = 'iconBgDanger';
	$classe_icon_dia = 'fa fa-caret-down text-danger';
}




//total recebidas dia
$total_recebidas_dia = 0;
$query = $pdo->query("SELECT * from receber where data_pgto = curDate() and pago = 'Sim' and (arquivado IS NULL or arquivado != 'Sim') and empresa = '$id_empresa' $sql_usuario_lanc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$contas_recebidas_dia = @count($res);
for ($i = 0; $i < $contas_recebidas_dia; $i++) {
	$valor_3 = $res[$i]['valor'];
	$total_recebidas_dia += $valor_3;
}
$total_recebidas_diaF = @number_format($total_recebidas_dia, 2, ',', '.');

//total contas pagas dia
$total_pagas_dia = 0;
$query = $pdo->query("SELECT * from pagar where data_pgto = curDate() and pago = 'Sim' and empresa = '$id_empresa' $sql_usuario_lanc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$contas_pagas_dia = @count($res);
for ($i = 0; $i < $contas_pagas_dia; $i++) {
	$valor_4 = $res[$i]['valor'];
	$total_pagas_dia += $valor_4;
}
$total_pagas_diaF = @number_format($total_pagas_dia, 2, ',', '.');


$total_saldo_dia = $total_recebidas_dia - $total_pagas_dia;
$total_saldo_diaF = @number_format($total_saldo_dia, 2, ',', '.');
if ($total_saldo_dia >= 0) {
	$classe_saldo_dia = 'bg-success';
} else {
	$classe_saldo_dia = 'bg-danger';
}




//valores para o grafico de linhas (ano atual, despesas e recebimentos)
$total_meses_pagar_grafico = '';
$total_meses_receber_grafico = '';

for ($i = 1; $i <= 12; $i++) {
	if ($i < 10) {
		$mes_verificado = '0' . $i;
	} else {
		$mes_verificado = $i;
	}


	$data_inicio_mes_grafico = $ano_atual . "-" . $mes_verificado . "-01";

	if ($mes_verificado == '04' || $mes_verificado == '06' || $mes_verificado == '09' || $mes_verificado == '11') {
		$data_final_mes_grafico = $ano_atual . '-' . $mes_verificado . '-30';
	} else if ($mes_verificado == '02') {
		$bissexto = date('L', @mktime(0, 0, 0, 1, 1, $mes_verificado));
		if ($bissexto == 1) {
			$data_final_mes_grafico = $ano_atual . '-' . $mes_verificado . '-29';
		} else {
			$data_final_mes_grafico = $ano_atual . '-' . $mes_verificado . '-28';
		}
	} else {
		$data_final_mes_grafico = $ano_atual . '-' . $mes_verificado . '-31';
	}




	//total recebidas mes
	$total_recebidas_mes_grafico = 0;
	$query2 = $pdo->query("SELECT * from receber where data_pgto >= '$data_inicio_mes_grafico' and data_pgto <= '$data_final_mes_grafico' and pago = 'Sim' and (arquivado IS NULL or arquivado != 'Sim') and empresa = '$id_empresa' $sql_usuario_lanc");
	$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
	$contas_recebidas_mes_grafico = @count($res2);
	for ($i2 = 0; $i2 < $contas_recebidas_mes_grafico; $i2++) {
		$valor_3 = $res2[$i2]['valor'];
		$total_recebidas_mes_grafico += $valor_3;
	}


	//total contas pagas mes
	$total_pagas_mes_grafico = 0;
	$query2 = $pdo->query("SELECT * from pagar where data_pgto >= '$data_inicio_mes_grafico' and data_pgto <= '$data_final_mes_grafico' and pago = 'Sim' and empresa = '$id_empresa' $sql_usuario_lanc");
	$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
	$contas_pagas_mes_grafico = @count($res2);
	for ($i2 = 0; $i2 < $contas_pagas_mes_grafico; $i2++) {
		$valor_4 = $res2[$i2]['valor'];
		$total_pagas_mes_grafico += $valor_4;
	}


	$total_meses_pagar_grafico .= $total_pagas_mes_grafico . '-';
	$total_meses_receber_grafico .= $total_recebidas_mes_grafico . '-';
}





//tarefas
$query = $pdo->query("SELECT * from tarefas where usuario = '$id_usuario' and status = 'Agendada' and data <= curDate() and hora < curTime() and empresa = '$id_empresa' order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$tarefas_atrasadas = @count($res);

$query = $pdo->query("SELECT * from tarefas where usuario = '$id_usuario' and status = 'Agendada' and empresa = '$id_empresa' order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_tarefas = @count($res);


$query = $pdo->query("SELECT * from tarefas where usuario = '$id_usuario' and status = 'Agendada' and data = curDate() and empresa = '$id_empresa' order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$tarefas_agendadas_hoje = @count($res);

#####ANOTAÇÕES#####################################
$query = $pdo->query("SELECT * from anotacoes where empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
for ($i5 = 0; $i5 < $linhas; $i5++) {
	$msg = $res[$i5]['msg'];
	$titulo = $res[$i5]['titulo'];
}


//verificar se ele tem a permissão de estar nessa página
if (@$home == 'ocultar') {
	echo "<script>window.location='index'</script>";
	exit();
}


#####PUXAR TOTAL DE DELIVERY############################################
$valor_delivery = 0;
$query = $pdo->query("SELECT * from vendas where entrega = 'Delivery' and (arquivado IS NULL or arquivado != 'Sim') and empresa = '$id_empresa' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas1 = @count($res);
for ($i6 = 0; $i6 < $linhas1; $i6++) {
	$valor_delivery += $res[$i6]['valor'];
}
$valor_deliveryF = @number_format($valor_delivery, 2, ',', '.');
$porcent_deli = $linhas1 / 100;



#####PUXAR TOTAL DE CONSUMIR LOCAL############################################
$valor_local = 0;
$query = $pdo->query("SELECT * from vendas where entrega = 'Consumir Local' and (arquivado IS NULL or arquivado != 'Sim') and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas2 = @count($res);
for ($i6 = 0; $i6 < $linhas2; $i6++) {
	$valor_local += $res[$i6]['valor'];
}
$valor_localF = @number_format($valor_local, 2, ',', '.');
$porcent_local = $linhas2 / 100;


#####PUXAR TOTAL DE CRETIRADA############################################
$valor_retirada = 0;
$query = $pdo->query("SELECT * from vendas where entrega = 'Retirar' and (arquivado IS NULL or arquivado != 'Sim') and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas3 = @count($res);
for ($i6 = 0; $i6 < $linhas3; $i6++) {
	$valor_retirada += $res[$i6]['valor'];
}
$valor_retiradaF = @number_format($valor_retirada, 2, ',', '.');
$porcent_retirada = $linhas3 / 100;


##### PUXAR TOTAL DA MESA ############################################
$valor_mesa = 0;
$query = $pdo->query("SELECT * from abertura_mesa where empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas4 = @count($res);
for ($i6 = 0; $i6 < $linhas4; $i6++) {
	$valor_mesa += $res[$i6]['total'];
}
$valor_mesaF = @number_format($valor_mesa, 2, ',', '.');
$porcent_mesa = $linhas4 / 100;



?>


<div class="mt-4 justify-content-between">


	<?php if ($ativo_sistema == '') { ?>

		<div style="background: #ffc341; color:#3e3e3e; padding:10px; font-size:14px; margin-bottom:10px">

			<div><i class="fa fa-info-circle"></i> <b>Aviso: </b> Prezado Cliente, não identificamos o pagamento de sua última
				mensalidade, entre em contato conosco o mais rápido possivel para regularizar o pagamento, caso contário seu
				acesso ao sistema será desativado.</div>

		</div>

	<?php } ?>




	<div class="row">

		<div class="col-xl-3 col-lg-12 col-md-6 col-xs-12">
			<div class="card sales-card"
				style="box-shadow: 2px 2px 0px 0px rgba(0, 0, 0, 0.1); height:130px; border-radius: 10px;">
				<a href="clientes">
					<div class="row">
						<div class="col-8">
							<div class="ps-4 pt-4 pe-3 pb-4">
								<div class="">
									<p class="mb-1">Clientes Cadastrados</p>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<h4 class="tx-20 font-weight-semibold mb-2"><?php echo $total_clientes ?></h4>
									</div>
									<p class="mb-0 tx-12 text-muted">Este Mês<i class="fa fa-caret-up mx-1 text-success"></i>
										<span class="iconSusses tx-11"><?php echo $total_clientes_mes ?></span>
									</p>
								</div>
							</div>
						</div>
						<div class="col-4">

							<div class=" zoom circle-icon iconBgSusses text-center align-self-center overflow-hidden">
								<i class="fe fe-user tx-16 text-white"></i>
							</div>

						</div>
					</div>
				</a>
			</div>
		</div>


		<div class="col-xl-3 col-lg-12 col-md-6 col-xs-12">
			<div class="card sales-card"
				style="box-shadow: 2px 2px 0px 0px rgba(0, 0, 0, 0.1); height:130px; border-radius: 10px">
				<a href="pagar">
					<div class="row">
						<div class="col-8">
							<div class="ps-4 pt-4 pe-3 pb-4">
								<div class="">
									<p class=" mb-1">Despesas Vencidas</p>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<h4 class="tx-20 font-weight-semibold mb-2">R$ <?php echo $total_pagar_vencidasF ?></h4>
									</div>
									<p class="mb-0 tx-12 text-muted">Pagar Vencidas<i class="fa fa-caret-down mx-1 text-danger"></i>
										<span class="iconDanger tx-11"><?php echo $contas_pagar_vencidas ?></span>
									</p>
								</div>
							</div>
						</div>
						<div class="col-4">

							<div class="zoom circle-icon iconBgDanger text-center align-self-center overflow-hidden">
								<i class="bi bi-currency-dollar tx-16 text-white"></i>
							</div>

						</div>
					</div>
				</a>
			</div>
		</div>
		<div class="col-xl-3 col-lg-12 col-md-6 col-xs-12">
			<div class="card sales-card"
				style="box-shadow: 2px 2px 0px 0px rgba(0, 0, 0, 0.1); height:130px; border-radius: 10px">
				<a href="receber">
					<div class="row">
						<div class="col-8">
							<div class="ps-4 pt-4 pe-3 pb-4">
								<div class="">
									<p class=" mb-1">Receber Vencidas</p>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<h4 class="tx-20 font-weight-semibold mb-2">R$ <?php echo $total_receber_vencidasF ?></h4>
									</div>
									<p class="mb-0 tx-12 text-muted">Receb. Vencidas<i class="fa fa-caret-up mx-1 text-success"></i>
										<span class="iconSusses tx-11"><?php echo $contas_receber_vencidas ?></span>
									</p>

								</div>
							</div>
						</div>
						<div class="col-4">

							<div class=" zoom circle-icon iconBgSusses text-center align-self-center overflow-hidden">
								<i class="bi bi-currency-dollar tx-16 text-white"></i>
							</div>

						</div>
					</div>
				</a>
			</div>
		</div>

		<div class="col-xl-3 col-lg-12 col-md-6 col-xs-12">
			<div class="card sales-card"
				style="box-shadow: 2px 2px 0px 0px rgba(0, 0, 0, 0.1); height:130px; border-radius: 10px">
				<a href="receber">
					<div class="row">
						<div class="col-8">
							<div class="ps-4 pt-4 pe-3 pb-4">
								<div class="">
									<p class=" mb-1">Saldo no Mês</p>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<h4 class="tx-20 font-weight-semibold mb-2 <?php echo $classe_saldo ?>">R$
											<?php echo $total_saldo_mesF ?>
										</h4>
									</div>
									<p class="mb-0 tx-12 text-muted">Saldo Hoje<i class="mx-1 <?php echo $classe_icon_dia ?>"></i>
										<span class="iconHome tx-11 <?php echo $classe_saldo ?> tx-11">R$
											<?php echo $total_saldo_diaF ?></span>
									</p>

								</div>
							</div>
						</div>
						<div class="col-4">

							<div class=" zoom circle-icon text-center align-self-center overflow-hidden <?php echo $classe_saldo2 ?>">
								<i class="bi bi-currency-dollar tx-16 text-white"></i>
							</div>

						</div>
					</div>
				</a>
			</div>
		</div>
	</div>


	<div id="statistics2"></div>



	<!-- ################################ FIM PAINEL DE INFORMAÇÕES ###################################### -->
	<?php

	$query = $pdo->query("SELECT * from anotacoes where mostrar_home = 'Sim' and empresa = '$id_empresa' order by id desc");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$linhas = @count($res);
	if ($linhas > 0) {

		echo <<<HTML
<small>

<div class='row'>
	
	<thead style="margin-bottom: 50px"> 
		
		
			<div class="col-xl-12" style="margin-bottom: -17px">
			<div class="card sales-card border border-dark" style="box-shadow: 2px 2px 0px 0px rgba(0, 0, 0, 0.2);">
			

<div  style="text-align:center; font-size: 20px; padding: 4px"><strong>PAINEL DE INFORMAÇÕES</strong> </div>
				
			</div>


	</div>


	</thead> 
	<tbody>	


		
HTML;

		for ($i = 0; $i < $linhas; $i++) {
			$id = $res[$i]['id'];
			$titulo = $res[$i]['titulo'];
			$msg = $res[$i]['msg'];
			$usuario = $res[$i]['usuario'];
			$data = $res[$i]['data'];


			$query2 = $pdo->query("SELECT * FROM usuarios where id = '$usuario' and empresa = '$id_empresa'");
			$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
			if (@count($res2) > 0) {
				$nome_usuario = $res2[0]['nome'];
			} else {
				$nome_usuario = 'Sem Usuário';
			}

			$dataF = implode('/', array_reverse(@explode('-', $data)));


			$msgF = mb_strimwidth($msg, 0, 250, "...");


			echo <<<HTML
<tr>

			<div class="col-xl-12" style="margin-bottom: -18px;">
			<div class="card sales-card border border-white" style="box-shadow: 2px 2px 0px 0px rgba(0, 0, 0, 0.1);">
			

<div style="font-size: 12px; padding: 4px"><b>{$titulo}:</b> {$msgF}</div>
				
			</div>


	</div>


</tr>
HTML;
		}
	}




	echo <<<HTML
</tbody>
</div>

HTML;
	?>


	<!-- ################################ FIM PAINEL DE INFORMAÇÕES ###################################### -->







	<div class="card custom-card overflow-hidden ocultar_mobile">
		<div class="card-header border-bottom-0">
			<div>
				<h3 class="card-title mb-2 ">Recebimentos / Despesas <?php echo $ano_atual ?></h3> <span
					class="d-block tx-12 mb-0 text-muted"></span>
			</div>
		</div>
		<div class="card-body">
			<div id="statistics1"></div>
		</div>
	</div>


</div>




<div class="row">


	<div class="col-xl-6 col-lg-12 col-md-6 col-xs-12">
		<a href="tarefas">
			<div class="card sales-card " style="box-shadow: 2px 2px 0px 0px rgba(0, 0, 0, 0.1);">
				<div class="row">

					<div class="col-8">
						<div class="ps-4 pt-4 pe-3 pb-4">
							<div class="">
								<h6 class="mb-2 tx-12 ">Total de Tarefas Pendentes</h6>
							</div>
							<div class="pb-0 mt-0">
								<div class="d-flex">
									<h4 class="tx-20 font-weight-semibold mb-2 text-danger"><?php echo $total_tarefas ?></h4>
								</div>
								<p class="mb-0 tx-12 text-muted">Pendentes Hoje<i class="fa fa-caret-up mx-2 text-success"></i>
									<span class="text-success font-weight-semibold"> <?php echo $tarefas_agendadas_hoje ?></span>

									<span style="margin-left: 20px">Tarefas Atrasadas<i class="fa fa-caret-down mx-2 text-danger"></i>
										<span class="text-danger font-weight-semibold"> <?php echo $tarefas_atrasadas ?></span></span>
								</p>

							</div>
						</div>
					</div>

					<div class="col-4">
						<div class="circle-icon bg-primary-transparent text-center align-self-center overflow-hidden">
							<i class="bi bi-card-checklist tx-16 text-primary"></i>
						</div>
					</div>
				</div>
			</div>
		</a>
	</div>

	<div class="col-sm-12 col-lg-12 col-xl-6 col-xxl-12">
		<div class="card">
			<div class="card-header pb-3">
				<h3 class="card-title mb-2">Vendas / DELIVERY</h3>
			</div>
			<div class="card-body p-0 customers mt-1">
				<div class="country-card pt-0">
					<div class="mb-4">

						<div class="d-flex">
							<span class="tx-13 font-weight-semibold">Delivery</span>
							<div class="ms-auto"><span class="text-info mx-1"><i class="fa-solid fa-dollar-sign"></i></span><span
									class="number-font"><b> R$ <?php echo $valor_deliveryF ?></b></span> (<?php echo $porcent_deli ?>%)</div>
						</div>
						<div class="progress ht-8 br-5 mt-2">
							<div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: <?php echo $porcent_deli ?>%"></div>
						</div>
					</div>
					<div class="mb-4">
						<div class="d-flex mb-1">
							<span class="tx-13 font-weight-semibold">Consumir Local</span>
							<div class="ms-auto"><span class="text-info mx-1"><i class="fa-solid fa-dollar-sign"></i></span><span
									class="number-font"><b> R$ <?php echo $valor_localF ?></b></span> (<?php echo $porcent_local ?>%)</div>
						</div>
						<div class="progress ht-8 br-5 mt-2">
							<div class="progress-bar progress-bar-striped progress-bar-animated bg-info" style="width: <?php echo $porcent_local ?>%"></div>
						</div>
					</div>
					<div class="mb-4">
						<div class="d-flex">
							<span class="tx-13 font-weight-semibold">Retirada</span>
							<div class="ms-auto"><span class="text-info mx-1"><i class="fa-solid fa-dollar-sign"></i></span><span
									class="number-font"><b> R$ <?php echo $valor_retiradaF ?></b></span> (<?php echo $porcent_retirada ?>%)</div>
						</div>
						<div class="progress ht-8 br-5 mt-2">
							<div class="progress-bar progress-bar-striped progress-bar-animated bg-secondary" style="width: <?php echo $porcent_retirada ?>%">
							</div>
						</div>
					</div>
					<div class="mb-4">
						<div class="d-flex">
							<span class="tx-13 font-weight-semibold">Mesa</span>
							<div class="ms-auto"><span class="text-info mx-1"><i class="fa-solid fa-dollar-sign"></i></span><span
									class="number-font"><b> R$ <?php echo $valor_mesaF ?></b></span> (<?php echo $porcent_mesa ?>%)</div>
						</div>
						<div class="progress ht-8 br-5 mt-2">
							<div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" style="width: <?php echo $porcent_mesa ?>%"></div>
						</div>
					</div>

				</div>

			</div>
		</div>
	</div>


</div>





<div class="row row-sm">




</div>



<script type="text/javascript">
	// GRAFICO DE BARRAS
	function statistics1() {
		var total_pagar = "<?= $total_meses_pagar_grafico ?>";
		var total_receber = "<?= $total_meses_receber_grafico ?>";

		var split_pagar = total_pagar.split("-");
		var split_receber = total_receber.split("-");


		setTimeout(() => {
			var options1 = {
				series: [{
					name: 'Contas à Receber',
					data: [split_receber[0], split_receber[1], split_receber[2], split_receber[3], split_receber[4], split_receber[5], split_receber[6], split_receber[7], split_receber[8], split_receber[9], split_receber[10], split_receber[11]],
				}, {
					name: 'Contas à Pagar',
					data: [split_pagar[0], split_pagar[1], split_pagar[2], split_pagar[3], split_pagar[4], split_pagar[5], split_pagar[6], split_pagar[7], split_pagar[8], split_pagar[9], split_pagar[10], split_pagar[11]],
				}],
				chart: {
					type: 'bar',
					height: 280
				},
				grid: {
					borderColor: '#f2f6f7',
				},
				colors: ["#098522", "#bf281d"],
				plotOptions: {
					bar: {
						colors: {
							ranges: [{
								from: -100,
								to: -46,
								color: '#098522'
							}, {
								from: -45,
								to: 0,
								color: '#bf281d'
							}]
						},
						columnWidth: '40%',
					}
				},
				dataLabels: {
					enabled: false,
				},
				stroke: {
					show: true,
					width: 4,
					colors: ['transparent']
				},
				legend: {
					show: true,
					position: 'top',
				},
				yaxis: {
					title: {
						text: 'Valores',
						style: {
							color: '#adb5be',
							fontSize: '14px',
							fontFamily: 'poppins, sans-serif',
							fontWeight: 600,
							cssClass: 'apexcharts-yaxis-label',
						},
					},
					labels: {
						formatter: function(y) {
							return y.toFixed(0) + "";
						}
					}
				},
				xaxis: {
					type: 'month',
					categories: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
					axisBorder: {
						show: true,
						color: 'rgba(119, 119, 142, 0.05)',
						offsetX: 0,
						offsetY: 0,
					},
					axisTicks: {
						show: true,
						borderType: 'solid',
						color: 'rgba(119, 119, 142, 0.05)',
						width: 6,
						offsetX: 0,
						offsetY: 0
					},
					labels: {
						rotate: -90
					}
				}
			};
			document.getElementById('statistics1').innerHTML = '';
			var chart1 = new ApexCharts(document.querySelector("#statistics1"), options1);
			chart1.render();
		}, 300);
	}
</script>





<div class="modal fade" id="modalAlertaSaas" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg" style="border-top: 4px solid #ffc107;">
      <div class="modal-header border-0 pb-0">
        <div class="d-flex align-items-center gap-3">
          <span class="alert-badge">
            <i class="fas fa-exclamation-triangle"></i>
          </span>
          <h5 class="modal-title mb-0" id="alertaTitulo"></h5>
        </div>
      </div>
      <div class="modal-body">
        <div id="alertaTexto" style="font-weight: normal;"></div>
      </div>
      <div class="modal-footer justify-content-end">
        <button type="button" class="btn btn-primary" id="btnFecharAlerta">Fechar</button>
      </div>
    </div>
  </div>
</div>


<style>
  .alert-badge{
    display:inline-flex; align-items:center; justify-content:center;
    width:44px; height:44px; border-radius:50%;
    background: rgba(255,193,7,.18); /* amarelo suave */
    color:#b58100; font-size:1.25rem;
    box-shadow: 0 2px 6px rgba(0,0,0,.06) inset;
  }
</style>


<script>
$(function () {
  $.getJSON('alerta_atual.php', function (resp) {
    if (!resp || !resp.ok || !resp.alerta) return;

    const a = resp.alerta;                       // { id, titulo, texto, ... }
    const title = (a.titulo || 'Aviso');
    const text  = (a.texto  || '');

    // fingerprint do conteúdo (título + texto)
    const normTitle = title.trim().toLowerCase();
    const normText  = text.replace(/\s+/g, ' ').trim();
    const fp = fnv1a(normTitle + '\n' + normText);

    // chave por ID (se existir) ou por título normalizado
    const storageKey = 'alerta_saas_fp_' + (a.id ? ('id_' + a.id) : ('t_' + encodeURIComponent(normTitle)));
    const seen = localStorage.getItem(storageKey);

    if (seen === fp) return; // já visto com esse mesmo conteúdo

    // Preenche e mostra modal
    $('#alertaTitulo').text(title);
    $('#alertaTexto').html(text);

    const modalEl = document.getElementById('modalAlertaSaas');
    const modal   = new bootstrap.Modal(modalEl);

    const markSeen = () => localStorage.setItem(storageKey, fp);

    $('#btnFecharAlerta').off('click').on('click', function () {
      markSeen();
      modal.hide();
    });

    // se fechar por qualquer outro meio no futuro, ainda marca como visto
    modalEl.addEventListener('hidden.bs.modal', function () {
      markSeen();
    }, { once: true });

    modal.show();
  });

  // hash rápido FNV-1a (32-bit) para fingerprint
  function fnv1a(str) {
    let h = 0x811c9dc5;
    for (let i = 0; i < str.length; i++) {
      h ^= str.charCodeAt(i);
      h = (h + ((h<<1) + (h<<4) + (h<<7) + (h<<8) + (h<<24))) >>> 0;
    }
    return ('00000000' + h.toString(16)).slice(-8);
  }
});
</script>
