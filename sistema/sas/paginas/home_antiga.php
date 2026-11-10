<?php
$pag = 'home';

if ($mostrar_registros == 'Não') {
	$sql_usuario = " and usuario = '$id_usuario '";
	$sql_usuario_lanc = " and usuario_lanc = '$id_usuario '";
} else {
	$sql_usuario = " ";
	$sql_usuario_lanc = " ";
}

//total clientes
if ($mostrar_registros == 'Não') {
	$query = $pdo->query("SELECT * from empresas where usuario = '$id_usuario'");
} else {
	$query = $pdo->query("SELECT * from empresas");
}
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_clientes = @count($res);

//total clientes mes
$query = $pdo->query("SELECT * from empresas where data_cad >= '$data_inicio_mes' and data_cad <= '$data_final_mes' $sql_usuario");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_clientes_mes = @count($res);

$total_pagar_vencidas = 0;
$query = $pdo->query("SELECT * from pagar_sas where vencimento < curDate() and pago != 'Sim' $sql_usuario_lanc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$contas_pagar_vencidas = @count($res);
for ($i = 0; $i < $contas_pagar_vencidas; $i++) {
	$valor_1 = $res[$i]['valor'];
	$total_pagar_vencidas += $valor_1;
}
$total_pagar_vencidasF = @number_format($total_pagar_vencidas, 2, ',', '.');



$total_receber_vencidas = 0;
$query = $pdo->query("SELECT * from receber_sas where vencimento < curDate() and pago != 'Sim' $sql_usuario_lanc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$contas_receber_vencidas = @count($res);
for ($i = 0; $i < $contas_receber_vencidas; $i++) {
	$valor_2 = $res[$i]['valor'];
	$total_receber_vencidas += $valor_2;
}
$total_receber_vencidasF = @number_format($total_receber_vencidas, 2, ',', '.');


//total recebidas mes
$total_recebidas_mes = 0;
$query = $pdo->query("SELECT * from receber_sas where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' $sql_usuario_lanc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$contas_recebidas_mes = @count($res);
for ($i = 0; $i < $contas_recebidas_mes; $i++) {
	$valor_3 = $res[$i]['valor'];
	$total_recebidas_mes += $valor_3;
}
$total_recebidas_mesF = @number_format($total_recebidas_mes, 2, ',', '.');

//total contas pagas mes
$total_pagas_mes = 0;
$query = $pdo->query("SELECT * from pagar_sas where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' $sql_usuario_lanc");
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
$query = $pdo->query("SELECT * from receber_sas where data_pgto = curDate() and pago = 'Sim' $sql_usuario_lanc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$contas_recebidas_dia = @count($res);
for ($i = 0; $i < $contas_recebidas_dia; $i++) {
	$valor_3 = $res[$i]['valor'];
	$total_recebidas_dia += $valor_3;
}
$total_recebidas_diaF = @number_format($total_recebidas_dia, 2, ',', '.');

//total contas pagas dia
$total_pagas_dia = 0;
$query = $pdo->query("SELECT * from pagar_sas where data_pgto = curDate() and pago = 'Sim' $sql_usuario_lanc");
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



//verificar se ele tem a permissão de estar nessa página
if (@$home == 'ocultar') {
	echo "<script>window.location='index'</script>";
	exit();
}
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
									<p class="mb-1">Total de Empresas</p>
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
							<div class=" zoom circle-icon iconBgRoxo text-center align-self-center overflow-hidden ">
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
											<?php echo $total_saldo_mesF ?></h4>
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




	<!-- ################################ FIM PAINEL DE INFORMAÇÕES ###################################### -->
	<?php

	$query = $pdo->query("SELECT * from anotacoes where mostrar_home = 'Sim' and empresa = 0 order by id desc");
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


			$query2 = $pdo->query("SELECT * FROM usuarios where id = '$usuario'");
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
			

<div style="font-size: 12px; padding: 4px"><b>{$titulo}:</b> {$msg}</div>
				
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





<div class="row mb-2 m-2">
								<?php 
									$query = $pdo->query("SELECT * from empresas");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$linhas = @count($res);
									if($linhas > 0){
									for($i=0; $i<$linhas; $i++){
										$nome_empresa = $res[$i]['nome'];	
										$id_empresa = $res[$i]['id'];
										$data_cad = $res[$i]['data_cad'];
										$data_cadF = implode('/', array_reverse(@explode('-', $data_cad)));

										//alterar as cores de fundo
										if($i == 0 || $i == 10 || $i == 20){
											$fundo = '#edefff';
										}
										if($i == 1 || $i == 11 || $i == 21){
											$fundo = '#ffeeed';
										}
										if($i == 2 || $i == 12 || $i == 22){
											$fundo = '#ffedfc';
										}
										if($i == 3 || $i == 13 || $i == 23){
											$fundo = '#edfbff';
										}
										if($i == 4 || $i == 14 || $i == 24){
											$fundo = '#edffee';
										}
										if($i == 5 || $i == 15 || $i == 25){
											$fundo = '#faffed';
										}
										if($i == 6 || $i == 16 || $i == 26){
											$fundo = '#fffaed';
										}
										if($i == 7 || $i == 17 || $i == 27){
											$fundo = '#fcedff';
										}
										if($i == 8 || $i == 18 || $i == 28){
											$fundo = '#fff0ed';
										}
										if($i == 9 || $i == 19 || $i == 29){
											$fundo = '#edfff4';
										}
										
										//totalizar os moradores
									$query2 = $pdo->query("SELECT * from receber_sas where cliente = '$id_empresa' and pago != 'Sim' and vencimento < curDate()");
									$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
									$contas_vencidas = @count($res2);

									$query2 = $pdo->query("SELECT * from config where empresa = '$id_empresa'");
									$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
									$logo_empresa = $res2[0]['icone'];

																	
								 ?>

								<div class="col-xl-3 col-lg-3 col-md-6 col-12" style="padding:5px; background: <?php echo $fundo; ?>; font-weight: 500; margin-top: 15px;">
									<a href="#" onclick="abrirEmpresa('<?php echo $id_empresa ?>')">
									<div style="padding:6px; box-shadow: 2px 2px 0px 0px rgba(0, 0, 0, 0.1);">
									<p class="mb-2" style="color:#454444"><?php echo mb_strtoupper($nome_empresa) ?> </p>
									<span style="font-size: 12px; color:#575757" class="mb-1" >Contas Vencidas: <span style="color:#690705"><?php echo $contas_vencidas ?></span></span>
									<p class="tx-11 " style="font-size: 12px; color:#575757">Cadastrada: <span style="color:blue"><b><?php echo $data_cadF ?></b></span> </p>
									<img src="../img/<?php echo $logo_empresa ?>" width="70px" style="position:absolute; right:10px;top:20px">
									</div>
									</a>
								</div>

							<?php } } ?>
								
					</div>
					



</div>





<script type="text/javascript">
	function abrirEmpresa(id){
		 $.ajax({
        url: 'pegar_sessao.php',
        method: 'POST',
        data: {id},
        dataType: "html",

        success:function(result){
            window.location="../painel";
        }
    });
	}
</script>