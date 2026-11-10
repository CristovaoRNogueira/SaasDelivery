<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
$tabela = 'vendas';
$data_hoje = date('Y-m-d');

$status_solicitado = @$_POST['status'];
$status = '%' . $status_solicitado . '%';
$ult_ped = @$_POST['ult_pedido'];
$direcao_ordem = strtolower((string) @$_POST['ordem']) === 'desc' ? 'DESC' : 'ASC';

$total_vendas = 0;

$sql_escopo_pedidos = " and empresa = '$id_empresa' ";
if($pedidos_pdv_delivery != 'Sim'){
	$sql_escopo_pedidos = " and tipo_pedido != 'Balcão' and empresa = '$id_empresa' ";
}
$sql_pedido_balcao = $sql_escopo_pedidos . " and (arquivado IS NULL or arquivado != 'Sim') ";

// Todos os contadores em uma única consulta.
$filtro_tipo_contadores = $pedidos_pdv_delivery != 'Sim' ? " AND tipo_pedido != 'BalcÃ£o'" : '';
$query_contadores = $pdo->query("SELECT
	SUM(CASE WHEN status = 'Iniciado' AND (arquivado IS NULL OR arquivado != 'Sim') THEN 1 ELSE 0 END) total_ini,
	SUM(CASE WHEN status = 'Aceito' AND (arquivado IS NULL OR arquivado != 'Sim') THEN 1 ELSE 0 END) total_ace,
	SUM(CASE WHEN status = 'Preparando' AND (arquivado IS NULL OR arquivado != 'Sim') THEN 1 ELSE 0 END) total_prep,
	SUM(CASE WHEN status = 'Entrega' AND (arquivado IS NULL OR arquivado != 'Sim') THEN 1 ELSE 0 END) total_ent,
	SUM(CASE WHEN status = 'Finalizado' AND data = CURDATE() AND (arquivado IS NULL OR arquivado != 'Sim') THEN 1 ELSE 0 END) total_finalizados,
	SUM(CASE WHEN arquivado = 'Sim' THEN 1 ELSE 0 END) total_arquivados,
	SUM(CASE WHEN status NOT IN ('Finalizado', 'Cancelado') AND (arquivado IS NULL OR arquivado != 'Sim') THEN 1 ELSE 0 END) total_pedidos
	FROM vendas WHERE empresa = '$id_empresa' $filtro_tipo_contadores");
$contadores = $query_contadores->fetch(PDO::FETCH_ASSOC) ?: [];
$total_ini = (int) ($contadores['total_ini'] ?? 0);
$total_ace = (int) ($contadores['total_ace'] ?? 0);
$total_prep = (int) ($contadores['total_prep'] ?? 0);
$total_ent = (int) ($contadores['total_ent'] ?? 0);
$total_finalizados = (int) ($contadores['total_finalizados'] ?? 0);
$total_arquivados = (int) ($contadores['total_arquivados'] ?? 0);
$total_pedidos = (int) ($contadores['total_pedidos'] ?? 0);


$query = $pdo->query("SELECT * FROM $tabela where status != 'Finalizado' and status != 'Cancelado' $sql_pedido_balcao order by id desc limit 1");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$id_ult_pedido = @$res[0]['id'];

if ($ult_ped < $id_ult_pedido and $ult_ped != "") {
	echo '<audio autoplay="true">
<source src="../../img/audio.mp3" type="audio/mpeg" />
</audio>';
}

if ($ult_ped == "" and $id_ult_pedido != "") {
	echo '<audio autoplay="true">
<source src="../../img/audio.mp3" type="audio/mpeg" />
</audio>';
}

$ids = '';
if ($status_solicitado == 'Arquivados') {
	$filtro_listagem = "arquivado = 'Sim'";
	$sql_listagem = $sql_escopo_pedidos;
} else if ($status_solicitado == 'Finalizado') {
	$filtro_listagem = "status = 'Finalizado' and data = CurDate()";
	$sql_listagem = $sql_pedido_balcao;
} else {
	$filtro_listagem = "status LIKE '$status' and status != 'Finalizado' and status != 'Cancelado'";
	$sql_listagem = $sql_pedido_balcao;
}
$ordem_listagem = "data $direcao_ordem, hora $direcao_ordem, id $direcao_ordem";
$query = $pdo->query("SELECT v.*,
	(SELECT c.nome FROM clientes c WHERE c.id = v.cliente LIMIT 1) AS cliente_cadastro_nome,
	(SELECT c.telefone FROM clientes c WHERE c.id = v.cliente LIMIT 1) AS cliente_cadastro_telefone,
	(SELECT COUNT(*) FROM carrinho ca WHERE ca.pedido = v.id AND ca.empresa = v.empresa AND ca.status IS NOT NULL AND ca.status != '' AND ca.status != 'Cancelado') AS total_itens_preparo,
	(SELECT COUNT(*) FROM carrinho ca WHERE ca.pedido = v.id AND ca.empresa = v.empresa AND ca.status IN ('Pronto', 'Finalizado')) AS itens_prontos
	FROM vendas v where $filtro_listagem $sql_listagem order by $ordem_listagem");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {


	for ($i = 0; $i < $total_reg; $i++) {
		foreach ($res[$i] as $key => $value) {
		}
		$id = $res[$i]['id'];
		$arquivado = $res[$i]['arquivado'] ?? 'Não';
		$acao_arquivo = $arquivado === 'Sim' ? 'restaurar' : 'arquivar';
		$titulo_arquivo = $arquivado === 'Sim' ? 'Restaurar Pedido' : 'Arquivar Pedido';
		$icone_arquivo = $arquivado === 'Sim' ? 'fa-rotate-left' : 'fa-box-archive';
		$cliente = $res[$i]['cliente'];
		$valor = $res[$i]['valor'];
		$total_pago = $res[$i]['total_pago'];
		$valor_recebido = (float) ($res[$i]['valor_recebido'] ?? 0);
		$troco = $res[$i]['troco'];
		$data = $res[$i]['data'];
		$hora = $res[$i]['hora'];
		$status = $res[$i]['status'];
		$pago = $res[$i]['pago'];
		$obs = $res[$i]['obs'];
		$taxa_entrega = $res[$i]['taxa_entrega'];
		$tipo_pgto = $res[$i]['tipo_pgto'];
		$usuario_baixa = $res[$i]['usuario_baixa'];
		$entrega = $res[$i]['entrega'];
		$mesa = $res[$i]['mesa'];
		$nome_cliente_ped = $res[$i]['nome_cliente'];
		$pedido = $res[$i]['pedido'];
		$impressao = $res[$i]['impressao'];
		$codigo_pix = $res[$i]['ref_api'];
		$tipo_pedido = $res[$i]['tipo_pedido'];

		//verificar pgto pix
		if ($arquivado != 'Sim' and $codigo_pix != "" and $pago != "Sim") {
			require("verificar_pgto.php");
			if (@$status_api == 'approved') {
				$pdo->query("UPDATE vendas set pago = 'Sim', valor_recebido = valor, tipo_pgto = 'Pix' where id = '$id'");
			}
		}

		$valorF = number_format($valor, 2, ',', '.');
		$total_pagoF = number_format($total_pago, 2, ',', '.');
		$valor_recebidoF = number_format($valor_recebido, 2, ',', '.');
		$saldo_pendenteF = number_format(max(0, $valor - $valor_recebido), 2, ',', '.');
		$trocoF = number_format($troco, 2, ',', '.');
		$taxa_entregaF = number_format($taxa_entrega, 2, ',', '.');
		$dataF = implode('/', array_reverse(explode('-', $data)));
		$horaF = date("H:i", strtotime($hora));

		$hora_pedido = date('H:i', strtotime("+$previsao_entrega minutes", strtotime($hora)));

		if ($hora_pedido < date('H:i')) {
			$classe_pago2 = '#ffdddd';
		} else {
			$classe_pago2 = '#ffffff';
		}

		if (!empty($res[$i]['cliente_cadastro_nome'])) {
			$nome_cliente = $res[$i]['cliente_cadastro_nome'];
			$telefone_cliente = (string) ($res[$i]['cliente_cadastro_telefone'] ?? '');
			$telefone_clienteF = '55' . preg_replace('/[ ()-]+/', '', $telefone_cliente);
		} else {
			if ($mesa != '0' and $mesa != '') {
				$nome_cliente = 'Mesa: ' . $mesa;
			} else {
				$nome_cliente = $nome_cliente_ped;
			}
			$telefone_cliente = '';
			$telefone_clienteF = '';
		}


		$whatsapp_cliente = '55' . preg_replace('/[ ()-]+/', '', $telefone_cliente);


		if ($status == 'Iniciado') {
			$classe_alerta = '#eb0000';
			$total_vendas += $valor;
			$titulo_link = 'Mudar para Aceito!';
			$acao_link = 'Aceito';
			$class_btn = 'btn-danger';
		} else if ($status == 'Aceito') {
			$classe_alerta = '#0097ff';
			$total_vendas += 0;
			$titulo_link = 'Mudar para Preparando!';
			$acao_link = 'Preparando';
			$class_btn = 'btn-info';
		} else if ($status == 'Preparando') {
			$classe_alerta = '#ff8400';
			$total_vendas += 0;
			$titulo_link = 'Mudar para Entrega!';
			$acao_link = 'Entrega';
			$class_btn = 'btn-warning';
		} else {
			$classe_alerta = '#00dc49';
			$total_vendas += $valor;
			$titulo_link = 'Mudar para Finalizado!';
			$acao_link = 'Finalizado';
			$class_btn = 'btn-success';
		}

		if ($status == 'Finalizado') {
			$botao_status = '<span style="display:inline-block; color:#fff; background:#198754; padding:5px 8px; text-align:center; font-size:9px; border-radius:20px;">Entregue <i class="fa fa-check"></i></span>';
		} else {
			$botao_status = '<a class="btn ' . $class_btn . ' btn-rounded mb-1" style="font-size:9px; padding:5px" title="' . $titulo_link . '" href="#" onclick="ativarPedido(\'' . $id . '\',\'' . $acao_link . '\',\'' . $telefone_cliente . '\',\'' . $valorF . '\',\'' . $tipo_pgto . '\',\'' . $hora_pedido . '\',\'' . $entrega . '\')">' . $status . ' <i class="fa fa-arrow-right"></i></a>';
		}

		if ($pago == 'Sim') {
			$classe_excluir = 'ocultar';
			$visivel = 'ocultar';
			$classe_pago = 'text-verde';
			$texto_pago = '(Pago)';
		} else {
			$classe_excluir = '';
			$visivel = '';
			$classe_pago = 'text-danger';
			$texto_pago = '';
		}

		if ($pago == 'Sim') {
			$exibicao_pagamento = '<span style="font-size:10px; font-weight:600; color:#138a36; background:#e8f7ed; border-radius:4px; padding:2px 5px">R$ ' . $total_pagoF . ' pago</span>';
		} else if ($valor_recebido > 0) {
			$exibicao_pagamento = '<span style="font-size:10px; font-weight:600; color:#138a36">R$ ' . $valor_recebidoF . ' recebido</span>';
			$exibicao_pagamento .= '<span style="font-size:9px; color:#888"> / </span>';
			$exibicao_pagamento .= '<span style="font-size:10px; font-weight:600; color:#c62828">R$ ' . $saldo_pendenteF . ' pendente</span>';
		} else {
			$exibicao_pagamento = '<span style="font-size:10px; font-weight:600; color:#c62828; background:#ffebee; border-radius:4px; padding:2px 5px">R$ ' . $saldo_pendenteF . ' pendente</span>';
		}

		if ($obs != "") {
			$classe_info = 'warning';
		} else {
			$classe_info = 'info';
		}

		if ($entrega == 'Delivery') {
			$classe_entrega = 'text-danger';
		} else if ($entrega == 'Retirar') {
			$classe_entrega = 'text-primary';
		} else {
			$classe_entrega = 'text-verde';
		}


		//validando impressao automatica
		if ($status == 'Iniciado' and $impressao_automatica == 'Site' and $impressao != "Sim") {
			$ids .= $id . '-';
			//coloco a impressao = sim
			$pdo->query("UPDATE $tabela SET impressao = 'Sim' where id = '$id'");
		}

		$nome_clienteF = mb_strimwidth($nome_cliente, 0, 16, "...");

		$classe_troco = '';
		if ($troco == "" || $troco == 0) {
			$classe_troco = 'ocultar';
		}

		if ($telefone_cliente == '' || $telefone_cliente == "Sem Registro") {
			$ocultar_whats = 'ocultar';
		} else {
			$ocultar_whats = '';
		}

		$tel_whatsF = '55' . preg_replace('/[ ()-]+/', '', $telefone_cliente);


		if ($api_whatsapp != 'Não') {
			$ocultar_modal_whats = '';
			$ocultar_icone_whats = 'ocultar';
		}else{
			$ocultar_modal_whats = 'ocultar';
			$ocultar_icone_whats = '';
		}

		$total_itens_preparo = (int) ($res[$i]['total_itens_preparo'] ?? 0);
		$itens_prontos = (int) ($res[$i]['itens_prontos'] ?? 0);

		if ($total_itens_preparo <= 0) {
			$resumo_preparo = '<span style="font-size:9px; color:#667085">Sem preparo</span>';
		} elseif ($itens_prontos >= $total_itens_preparo) {
			if (stripos((string) $entrega, 'delivery') !== false) {
				$texto_pronto = 'Pronto para entrega';
			} elseif (stripos((string) $entrega, 'retir') !== false || stripos((string) $tipo_pedido, 'balc') !== false) {
				$texto_pronto = 'Pronto para retirada';
			} else {
				$texto_pronto = 'Pronto para servir';
			}

			$resumo_preparo = '<span style="display:inline-block; font-size:9px; font-weight:700; color:#137333; background:#e6f4ea; border-radius:5px; padding:3px 5px"><i class="fa fa-check-circle"></i> ' . $texto_pronto . '</span>';
		} else {
			$percentual_preparo = (int) round(($itens_prontos / $total_itens_preparo) * 100);
			$resumo_preparo = '<div style="width:100%; white-space:normal">';
			$resumo_preparo .= '<span style="font-size:9px; font-weight:600; color:#344054">Preparo ' . $itens_prontos . ' de ' . $total_itens_preparo . '</span>';
			$resumo_preparo .= '<div style="height:4px; margin-top:2px; overflow:hidden; border-radius:4px; background:#e4e7ec"><div style="height:100%; width:' . $percentual_preparo . '%; background:#f59e0b"></div></div>';
			$resumo_preparo .= '</div>';
		}

		echo <<<HTML
<div class="col-md-3 widget " style="display: inline-block;">
			<div class="" style="padding:4px; min-height: 100px; background: {$classe_pago2};">
				<div style="border-bottom: 1px solid #000">
					<span style="color:{$classe_alerta}"> <i class="fa fa-square"></i></span> Pedido <b>{$pedido}</b>
					<span style="position:absolute; right:5px; text-align:right; line-height:1.05;">
						<small style="display:block; font-size:9px; color:#555;">{$dataF}</small>
						<small style="display:block; font-size:8px; color:#777;">{$horaF}</small>
					</span>
				</div>

				<div style="margin-top: 5px">
					<span><small><i><span style="color:#000">{$nome_clienteF} <span class="{$classe_entrega}"><small>({$entrega}) </small></span></span></i></small></span>					
				</div>

				<div>
					<span id="area_status_{$id}"><small><i>Status <b>{$botao_status}</b></i></small></span>		
					<img id="status_link_{$id}" src="../../img/loading.gif" width="80px" style="display:none">			
				</div>
				<div style="margin:3px 0 5px 0; padding:4px 5px; background:#f8f9fa; border-radius:4px">
					{$resumo_preparo}
				</div>
					<div style="border-bottom: 1px solid #000; padding-bottom: 5px">
						<div style="font-size:10px">
							<span>{$exibicao_pagamento}</span>
							<span class="{$classe_troco}" style="margin-left:4px">Troco: {$trocoF}</span>
						</div>
						<div style="margin-top:4px; white-space:normal">
							<span style="display:inline-block; padding:2px 5px; border-radius:4px; background:#eef2f6; color:#475467; font-size:9px; font-weight:600">{$tipo_pgto}</span>
						</div>
					</div>
					<div align="center" style="">
					<span>	

		<a class="btn btn-{$classe_info}-light btn-sm" href="#" onclick="mostrar('{$id}', '{$nome_cliente}', '{$pedido}')" title="Ver Dados"><i class="fa fa-info-circle"></i></a>

		<a class="btn btn-secondary-light btn-sm" href="#" onclick="arquivarPedido('{$id}', '{$acao_arquivo}')" title="{$titulo_arquivo}"><i class="fa {$icone_arquivo}"></i></a>



<big><a href="#" class="btn btn-danger-light btn-sm {$classe_excluir}" onclick="excluir('{$id}')" title="Excluir"><i class="fa fa-trash-can text-danger"></i></a></big>

	<a id="btn_baixar_{$id}" class="btn btn-success-light btn-sm {$classe_excluir}" href="#" onclick="baixar('{$id}', '{$pedido}', '{$nome_cliente}', '{$tipo_pgto}', '{$valorF}', '{$valor_recebidoF}', '{$saldo_pendenteF}')" title="Confirmar Pgto"><i class="fa fa-check-square"></i></a>


		<a class="btn btn-dark-light btn-sm" href="#" onclick="gerarComprovante('{$id}', '{$tipo_pedido}')" title="Gerar Comprovante"><i class="fa fa-file-pdf-o"></i></a>

	<a href="#" class="btn btn-success-light btn-sm  {$ocultar_modal_whats}" onclick="modalWhats('{$cliente}','Cliente')" title="Enviar Mensagem para o Cliente"><i class="fa-brands fa-whatsapp"></i></a>

<a class="btn btn-success-light btn-sm {$ocultar_icone_whats}" target="_blank" href="http://api.whatsapp.com/send?1=pt_BR&phone={$telefone_clienteF}" title="Whatsapp Cliente">
	<i class="fa-brands fa-whatsapp"></i></a>




		
	
		</span>				
				</div>
				
			</div>
		</div>	
HTML;
	}

	$total_vendasF = number_format($total_vendas, 2, ',', '.');
} else {
	echo '<small>Não possui nenhum Pedido Hoje ainda!</small>';
}



$query = $pdo->query("SELECT * FROM vendas where data = CurDate() and status = 'Iniciado' and (arquivado IS NULL or arquivado != 'Sim') and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_dos_itens_pedidos = @count($res);


?>

<script type="text/javascript">
	$(document).ready(function() {

		var ids = "<?= $ids ?>";
		var id_imp = ids.split("-");
		//alert(ids)

		for (i = 0; i < id_imp.length - 1; i++) {
			var id_pedido = id_imp[i];

			let a = document.createElement('a');
			a.target = '_blank';
			a.href = 'rel/comprovante.php?id=' + id_pedido;
			a.click();
		}



		$('#todos_pedidos').text("<?= $total_pedidos ?>");
		$('#ini_pedidos').text("<?= $total_ini ?>");
		$('#prep_pedidos').text("<?= $total_prep ?>");
		$('#ent_pedidos').text("<?= $total_ent ?>");
		$('#finalizados_pedidos').text("<?= $total_finalizados ?>");
		$('#arquivados_pedidos').text("<?= $total_arquivados ?>");
		$('#id_pedido').val("<?= $id_ult_pedido ?>");

		$('#total-dos-pedidos').text("<?= $total_dos_itens_pedidos ?>");

		
		$('#tabela_filter label input').focus();
	});
</script>


<script type="text/javascript">
	function mostrar(id, cliente, pedido) {
		listarPedido(id);
		$('#nome_dados').text('Pedido ' + pedido + ' - Cliente: ' + cliente);
		$('#modalDados').modal('show');
	}
</script>

<script type="text/javascript">
	function baixar(id, pedido, cliente, pgto, total, recebido, pendente) {

		$('#nome_baixar').text('Confirmar Pagamento: Pedido ' + pedido + ' - Cliente: ' + cliente);
		$('#resumo-total').text('R$ ' + total);
		$('#resumo-recebido').text('R$ ' + recebido);
		$('#resumo-pendente').text('R$ ' + pendente);
		var container = $('#formas-pagamento-pedido');
		container.data('saldo', converterValorPagamentoPedido(pendente));
		container.find('.forma-pagamento-pedido:not(:first)').remove();
		container.find('.campo-forma-pgto').first().val(pgto).change();
		container.find('.campo-valor-pgto').first().val(pendente);
		$('#id_baixar').val(id);
		$('#modalBaixar').modal('show');
	}
</script>


<script type="text/javascript">
	function ativarPedido(id, acao, telefone, total, pagamento, hora, entrega) {
		var pedido_whatsapp = "<?= $api_whatsapp ?>";

		$('#status_link_' + id).show();
		$('#area_status_' + id).hide();


		if (entrega == 'Delivery') {

			var texto = 'Seu Pedido saiu para entrega';
		} else if (entrega == 'Retirar') {
			var texto = 'Seu Pedido ficou pronto, pode vir retirá-lo';
		} else {
			var texto = 'Seu Pedido já ficou pronto, pode vir!!';
		}

		var imp_aut = '<?= $impressao_automatica ?>';

		if (imp_aut == 'Sim' && acao == 'Preparando') {
			let a = document.createElement('a');
			a.target = '_blank';
			a.href = 'rel/comprovante.php?id=' + id;
			a.click();
		}

		if (acao == 'Entrega' && entrega == 'Delivery') {
			$('#id_entregador').val(id);
			$('#modalEntregador').modal('show');
		}

		setTimeout(function() {

			$.ajax({
				url: 'paginas/pedidos/mudar-status.php',
				method: 'POST',
				data: {
					id,
					acao,
					telefone,
					total,
					pagamento,
					texto
				},
				dataType: "text",

				success: function(mensagem) {
					var split = mensagem.split("***");

					var msg = split[0];

					if (msg == "Alterado com Sucesso") {

						if (acao.trim() === 'Entrega') {
							if (pedido_whatsapp != 'Não') {
								//let a= document.createElement('a');
								// a.target= '_blank';
								// a.href= 'http://api.whatsapp.com/send?1=pt_BR&phone='+telefone+'&text= *Atençãoooo*  %0A '+texto+' %0A *Total* R$'+total+' %0A *Tipo Pagamento* '+pagamento;
								// a.click();
							}
						}
						listar();
						$('#status_link_' + id).hide();
						$('#area_status_' + id).show();
					} else {
						alertInformativoFechar(mensagem)
						//$('#mensagem-excluir').addClass('text-danger')
						//$('#mensagem-excluir').text(mensagem)

						if(mensagem.trim() === 'O pedido ainda está pendente de pagamento, dê a baixa no pagamento antes!'){
							setTimeout(function() {
								$('#btn_baixar_' + id).click();
								}, 2000);
						}


						$('#status_link_' + id).hide();
						$('#area_status_' + id).show();
					}


				},

			});

		}, 1000);
	}
</script>


<script type="text/javascript">
	function gerarComprovante(id, tipo_pedido) {


		let a = document.createElement('a');
		a.target = '_blank';
		if(tipo_pedido == "Balcão"){
			a.href = 'rel/comprovante_balcao.php?id=' + id;
		}else{
			a.href = 'rel/comprovante.php?id=' + id;
		}
		
		a.click();
	}

</script>


	<script type="text/javascript">
		function modalWhats(id, tipo) {
		$('#id_whats').val(id);
		$('#tipo_whats').val(tipo);
		$('#mensagem_whatsapp').val('');
		$('#modalWhats').modal('show');
	}

	</script>
