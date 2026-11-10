<?php 
@session_start();
$id_empresa = $_SESSION['empresa'];
$id_usuario = $_SESSION['id'];
require_once("../../conexao.php");
require_once("../buscar_config.php");
include('data_formatada.php');

$dataInicial = $_GET['dataInicial'];
$dataFinal = $_GET['dataFinal'];
$status = $_GET['status'];
$forma_pgto = $_GET['forma_pgto'];
$tipo = $_GET['tipo'];

$horaInicial = $_GET['horaInicial'];
$horaFinal = $_GET['horaFinal'];

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

// Totais
$total_vendas = 0;
$total_canceladas = 0;

$total_delivery = 0;
$total_delivery_custo = 0;
$total_delivery_liquido = 0;

$total_balcao = 0;
$total_balcao_custo = 0;
$total_balcao_liquido = 0;

$total_mesas = 0;
$total_mesas_custo = 0;
$total_mesas_liquido = 0;

$total_custo_geral = 0;
$total_liquido_geral = 0;

// consulta
$query = $pdo->query("SELECT * from vendas where (data >= '$dataInicial' and data <= '$dataFinal') and pago = 'Sim' and status LIKE '$status' and tipo_pgto LIKE '$forma_pgto' and empresa = '$id_empresa' $sql_arquivado $sql_tipo $sql_hora order by data asc, hora asc ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = count($res);
require_once(__DIR__ . '/pagamentos_venda.php');
$pagamentos_por_venda = carregarPagamentosPorVenda($pdo, $res, (int) $id_empresa);

?>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<?php if (@$impressao_automatica != 'Não') { ?>
	<script>
	$(function(){
		window.print();
		window.close();
	});
	</script>
<?php } ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
	*{ margin:0; padding:0; background:#fff; box-sizing:border-box; }
	.printer-ticket{ width:100%; max-width:420px; margin:8px auto; font-family:Arial, Helvetica, sans-serif; font-size:<?php echo $fonte_comprovante ?>px; color:#000;}

	.header img{ display:block; margin:10px auto; }

	.th{ text-align:center; padding:6px 8px; border-bottom:1px solid #ccc; font-size:12px;}

	.title{ font-size:14px; font-weight:bold; margin:10px 0; text-align:center; }

	.itens{ font-size:11px; padding:4px 6px; }

	.row-itens{
		display:flex;
		align-items:center;
		justify-content:space-between;
		padding:6px 0;
		border-bottom:1px dashed #ccc;
	}

	.col-left{ width:55%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
	.col-center{ width:22%; text-align:right; }
	.col-right{ width:23%; text-align:right; font-size:10px; color:#888; }

	/* NOVAS CORES — modernas e consistentes */
	.text-verde{ color:#1E824C; font-weight:bold; }
	.text-vermelho{ color:#C0392B; font-weight:bold; }
	.text-azul{ color:#1F4788; font-weight:bold; }

	/* blocos */
	.rodape{ display:flex; margin-top:12px; gap:8px; }
	.rodape .box{
		flex:1;
		padding:8px;
		background:#F2F2F2;
		border-top:2px solid #ccc;
		font-size:11px;
	}
	.rodape .big{ font-size:13px; font-weight:bold; }

	@media print{
		.printer-ticket{ max-width:420px; font-size:<?php echo max(9, $fonte_comprovante - 1) ?>px; }
	}
</style>

<div class="printer-ticket">

	<div class="header" align="center">
		<img src="<?php echo $url_sistema ?>sistema/img/<?php echo $logo_sistema ?>" width="110">
	</div>

	<div class="th">
		<?php echo $endereco_sistema ?><br>
		<small>Contato: <?php echo $telefone_sistema ?> <?php if ($cnpj_sistema != "") echo ' / CNPJ '.$cnpj_sistema; ?></small>
	</div>

	<div class="title">DETALHAMENTO DAS VENDAS</div>

	<div class="itens">
		<div class="row-itens" style="border-bottom:1px solid #bbb; padding-bottom:8px;">
			<div class="col-left"><b>CLIENTE</b></div>
			<div class="col-center"><b>VALOR</b></div>
			<div class="col-right"><b>HORA</b></div>
		</div>

<?php
if ($total_reg > 0) {
	$totais_por_pgto = [];
	for ($i = 0; $i < $total_reg; $i++) {

		$r = $res[$i];
		$id = $r['id'];
		$cliente = $r['cliente'];
		$valor = (float)$r['valor'];
		$total_pago = (float)$r['total_pago'];
		$custo = (float) ($r['custo'] ?? 0);
		$hora = $r['hora'];
		$status_venda = $r['status'];
		$nome_do_cliente = $r['nome_cliente'];
		$mesa = $r['mesa'];
		$tipo_pedido = $r['tipo_pedido'];
		$tipo_pgto_original = (string) $r['tipo_pgto'];
		$pagamentos_venda = obterPagamentosVenda($pagamentos_por_venda, (int) $id, $tipo_pgto_original, $total_pago);
		$tipo_pgto = descreverPagamentosVenda($pagamentos_venda);

		// busca nome
		$q2 = $pdo->query("SELECT * FROM clientes WHERE id = '$cliente'");
		$r2 = $q2->fetchAll(PDO::FETCH_ASSOC);
		$nome_cliente = $r2 ? $r2[0]['nome'] : 'Nenhum';

		if ($mesa != '0' && $mesa != '') $nome_cliente = 'Mesa: '.$mesa;
		if ($nome_do_cliente != "") $nome_cliente = $nome_do_cliente;

		// classes de cor
		if ($status_venda == 'Finalizado'){
			$classe_alerta = 'text-verde';
			$total_vendas += $valor;
		}else if ($status_venda == 'Cancelado'){
			$classe_alerta = 'text-vermelho';
			$total_canceladas += $valor;
		}else{
			$classe_alerta = 'text-azul';
			$total_vendas += $valor;
		}

		// totais por tipo
		if ($tipo_pedido == "" || strtolower($tipo_pedido) == "delivery"){
			$total_delivery += $total_pago;
			$total_delivery_custo += $custo;
		}
		if ($tipo_pedido == "Balcão"){
			$total_balcao += $total_pago;
			$total_balcao_custo += $custo;
		}
		if ($tipo_pedido == "Mesa"){
			$total_mesas += $total_pago;
			$total_mesas_custo += $custo;
		}

		$total_custo_geral += $custo;

		$nome_print = mb_strimwidth($nome_cliente, 0, 24, "...");


		if ($status_venda != 'Cancelado') {
			foreach ($pagamentos_venda as $pagamento_venda) {
				$pgto_key = $pagamento_venda['forma'];
				$totais_por_pgto[$pgto_key] = ($totais_por_pgto[$pgto_key] ?? 0) + (float) $pagamento_venda['valor'];
			}
		}

?>
		<div class="row-itens">
			<div class="col-left"><?php echo $nome_print ?></div>
			<div class="col-center ">R$ <?php echo number_format($total_pago,2,',','.') ?></div>
			<div class="col-right"><?php echo $hora ?></div>
		</div>
		<div style="font-size:9px; color:#555; padding:0 0 5px 0;"><?php echo htmlspecialchars($tipo_pgto, ENT_QUOTES, 'UTF-8') ?></div>
<?php
	}

} else {
	echo "<div class='itens small-muted'>Nenhuma venda encontrada no período.</div>";
}

// fechamento dos totais
$total_delivery_liquido = $total_delivery - $total_delivery_custo;
$total_balcao_liquido   = $total_balcao - $total_balcao_custo;
$total_mesas_liquido    = $total_mesas - $total_mesas_custo;

$total_liquido_geral = $total_vendas - $total_custo_geral;

?>

	<hr style="margin-top:10px;">

	<!-- Cabeçalho dos tipos -->
	<div class="row itens" style="font-size:11px;">

		<?php if($mostrar_delivery){ ?>
		<div style="width:33%; float:left;">
			<b class="text-verde">DELIVERY</b>
		</div>
		<?php } ?>

		<?php if($mostrar_balcao){ ?>
		<div style="<?php echo ($mostrar_delivery?'width:33%; float:left;':'width:50%; float:left;') ?>">
			<b class="text-azul">BALCÃO</b>
		</div>
		<?php } ?>

		<?php if($mostrar_mesa){ ?>
		<div style="<?php echo (($mostrar_delivery && $mostrar_balcao)?'width:34%;':'width:50%;') ?> float:right;">
			<b class="text-vermelho">MESAS</b>
		</div>
		<?php } ?>

		<div style="clear:both"></div>
	</div>

	<!-- Totais -->
	<div class="row itens" style="margin-top:6px; font-size:11px;">

		<?php if($mostrar_delivery){ ?>
		<div style="width:33%; float:left;">
			Total: <span class=""><?php echo number_format($total_delivery,2,',','.') ?></span><br>
			Custo: <span style="color:red"><?php echo number_format($total_delivery_custo,2,',','.') ?></span><br>
			Líquido: <span style="color:green"><?php echo number_format($total_delivery_liquido,2,',','.') ?></span>
		</div>
		<?php } ?>

		<?php if($mostrar_balcao){ ?>
		<div style="<?php echo ($mostrar_delivery?'width:33%; float:left;':'width:50%; float:left;') ?>">
			Total: <span class=""><?php echo number_format($total_balcao,2,',','.') ?></span><br>
			Custo: <span style="color:red"><?php echo number_format($total_balcao_custo,2,',','.') ?></span><br>
			Líquido: <span style="color:green"><?php echo number_format($total_balcao_liquido,2,',','.') ?></span>
		</div>
		<?php } ?>

		<?php if($mostrar_mesa){ ?>
		<div style="<?php echo (($mostrar_delivery && $mostrar_balcao)?'width:34%;':'width:50%;') ?> float:right;">
			Total: <span class=""><?php echo number_format($total_mesas,2,',','.') ?></span><br>
			Custo: <span style="color:red"><?php echo number_format($total_mesas_custo,2,',','.') ?></span><br>
			Líquido: <span style="color:green"><?php echo number_format($total_mesas_liquido,2,',','.') ?></span>
		</div>
		<?php } ?>

		<div style="clear:both"></div>
	</div>

	<hr>

	<!-- Totais gerais -->
	<div class="rodape">
		<div class="box">
			Total Geral<br>
			<span class="big text-azul">R$ <?php echo number_format($total_vendas,2,',','.') ?></span>
		</div>

		<div class="box">
			Custo Geral<br>
			<span class="big" style="color:red !important">R$ <?php echo number_format($total_custo_geral,2,',','.') ?></span>
		</div>

		<div class="box">
			Liquido<br>
			<span class="big" style="color:green">R$ <?php echo number_format($total_liquido_geral,2,',','.') ?></span>
		</div>
	</div>

</div>


<?php if(!empty($totais_por_pgto)){ 
    // (Opcional) ordenar do maior pro menor
    arsort($totais_por_pgto);
?>
    <hr>
    <div style="width:90%; margin-top:5px; margin-left: 10px">
        <div style="font-size:10px; font-weight:bold; margin-bottom:6px;">RESUMO POR FORMA DE PAGAMENTO</div>

        <table style="width:90%; border-collapse:collapse; font-size:9px;">
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
