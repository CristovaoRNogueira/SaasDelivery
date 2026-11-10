<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
$tabela = 'vendas';
$data_hoje = date('Y-m-d');

$status_solicitado = @$_POST['status'];
$status = '%' . $status_solicitado . '%';
$status_busca = '%' . $status_solicitado . '%';
$ult_ped = @$_POST['ult_pedido'];
$direcao_ordem = strtolower((string) @$_POST['ordem']) === 'desc' ? 'DESC' : 'ASC';

$total_vendas = 0;

$busca = @$_POST['p1'];
$pagina = 0;
$num_paginas = 0;
$ultima_pagina = 0;

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
$busca_like = '%' . $busca . '%';
$query_total = $pdo->prepare("SELECT COUNT(*) FROM $tabela where $filtro_listagem and COALESCE(nome_cliente, '') like :busca $sql_listagem");
$query_total->execute([':busca' => $busca_like]);
$num_total = (int) $query_total->fetchColumn();
$query = $pdo->prepare("SELECT v.*,
	(SELECT c.nome FROM clientes c WHERE c.id = v.cliente LIMIT 1) AS cliente_cadastro_nome,
	(SELECT c.telefone FROM clientes c WHERE c.id = v.cliente LIMIT 1) AS cliente_cadastro_telefone,
	(SELECT COUNT(*) FROM carrinho ca WHERE ca.pedido = v.id AND ca.empresa = v.empresa AND ca.status IS NOT NULL AND ca.status != '' AND ca.status != 'Cancelado') AS total_itens_preparo,
	(SELECT COUNT(*) FROM carrinho ca WHERE ca.pedido = v.id AND ca.empresa = v.empresa AND ca.status IN ('Pronto', 'Finalizado')) AS itens_prontos
	FROM vendas v where $filtro_listagem and COALESCE(nome_cliente, '') like :busca $sql_listagem order by $ordem_listagem");
$query->execute([':busca' => $busca_like]);
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {

	echo <<<HTML
	<small>
	<style>
		.tabela-pedidos-compacta { font-size: 11px; }
		.tabela-pedidos-compacta th { font-size: 12px; padding: 7px 8px !important; }
		.tabela-pedidos-compacta td { font-size: 11px; padding: 6px 8px !important; vertical-align: middle; }
		.tabela-pedidos-compacta .btn-sm { font-size: 10px; padding: 4px 6px; }
	</style>
	<table class="table table-bordered text-nowrap border-bottom dt-responsive tabela-pedidos-compacta" id="">
	<thead> 
	<tr> 
	<th>Cliente</th>	
	<th>Valor</th> 	
	<th>Total Pago</th> 
	<th>Troco</th>	
	<th>Forma PGTO</th> 	
	<th>Preparo</th>
	<th>Status</th>	 	
	<th>Data / Hora</th>		
	<th>Ações</th>
	</tr> 
	</thead> 
	<tbody>	
HTML;

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
		$cupom = (float) ($res[$i]['cupom'] ?? 0);
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
		$cupomF = number_format($cupom, 2, ',', '.');
		$trocoF = number_format($troco, 2, ',', '.');
		$taxa_entregaF = number_format($taxa_entrega, 2, ',', '.');
		$dataF = implode('/', array_reverse(explode('-', $data)));
		$horaF = date("H:i", strtotime($hora));	

		$hora_pedido = date('H:i', strtotime("+$previsao_entrega minutes", strtotime($hora)));

		if ($hora_pedido < date('H:i')) {
			$classe_pago2 = '#ffdddd';
		} else {
			$classe_pago2 = '';
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





		if ($status == 'Iniciado') {
			$classe_alerta = '#eb0000';
			$total_vendas += $valor;
			$titulo_link = 'Mudar para Aceito!';
			$cor_icone_link = '#eb0000';
			$acao_link = 'Aceito';
		} else if ($status == 'Aceito') {
			$classe_alerta = '#0097ff';
			$total_vendas += 0;
			$titulo_link = 'Mudar para Preparando!';
			$cor_icone_link = '#0097ff';
			$acao_link = 'Preparando';
		} else if ($status == 'Preparando') {
			$classe_alerta = '#ff8400';
			$total_vendas += 0;
			$titulo_link = 'Mudar para Entrega!';
			$cor_icone_link = '#ff8400';
			$acao_link = 'Entrega';
		} else {
			$classe_alerta = '#00dc49';
			$total_vendas += $valor;
			$titulo_link = 'Mudar para Finalizado!';
			$cor_icone_link = '#00dc49';
			$acao_link = 'Finalizado';
		}

		if ($pago == 'Sim') {
			$classe_excluir = 'ocultar';
			$visivel = 'ocultar';
			$classe_pago = 'text-verde';
			$texto_pago = '(Pago)';
		} else {
			$classe_excluir = '';
			$visivel = '';
			$classe_pago = '';
			$texto_pago = '';
		}
		$permite_alteracao = in_array($status, ['Iniciado', 'Aceito', 'Preparando'], true)
			|| ($status == 'Entrega' && strcasecmp(trim((string) $entrega), 'Delivery') !== 0);
		$ocultar_alteracao = $permite_alteracao ? '' : 'ocultar';
		$display_alteracao = $permite_alteracao ? 'inline-block' : 'none';

		if ($obs != "") {
			$classe_info = 'primary';
		} else {
			$classe_info = 'info';
		}

		if ($entrega == 'Delivery') {
			$classe_entrega = 'text-danger';
		} else if ($entrega == 'Retirar') {
			$classe_entrega = 'text-azul';
		} else {
			$classe_entrega = 'text-success';
		}


		//validando impressao automatica
		if ($status == 'Iniciado' and $impressao_automatica == 'Site' and $impressao != "Sim") {
			$ids .= $id . '-';
			//coloco a impressao = sim
			$pdo->query("UPDATE $tabela SET impressao = 'Sim' where id = '$id'");
		}


		if ($pago == 'Sim') {
			$exibicao_pagamento = '<span style="display:inline-block; font-size:11px; font-weight:600; color:#138a36; background:#e8f7ed; border-radius:4px; padding:3px 6px; white-space:nowrap">R$ ' . $total_pagoF . ' pago</span>';
		} else if ($valor_recebido > 0) {
			$exibicao_pagamento = '<span style="display:inline-block; font-size:11px; font-weight:600; color:#138a36; background:#e8f7ed; border-radius:4px; padding:3px 6px; white-space:nowrap">R$ ' . $valor_recebidoF . ' recebido</span>';
			$exibicao_pagamento .= '<span style="font-size:10px; color:#8b8b8b; margin:0 3px">/</span>';
			$exibicao_pagamento .= '<span style="display:inline-block; font-size:11px; font-weight:600; color:#c62828; background:#ffebee; border-radius:4px; padding:3px 6px; white-space:nowrap">R$ ' . $saldo_pendenteF . ' pendente</span>';
		} else {
			$exibicao_pagamento = '<span style="display:inline-block; font-size:11px; font-weight:600; color:#c62828; background:#ffebee; border-radius:4px; padding:3px 6px; white-space:nowrap">R$ ' . $saldo_pendenteF . ' pendente</span>';
		}


		if ($api_whatsapp != 'Não') {
			$ocultar_modal_whats = '';
			$ocultar_icone_whats = 'ocultar';
		}else{
			$ocultar_modal_whats = 'ocultar';
			$ocultar_icone_whats = '';
		}


		$span_balcao = '';
		if($tipo_pedido == 'Balcão'){
			$span_balcao = '<span style="font-size:10px; color:gray">(Balcão)</span>';
		}

		if ($status == 'Finalizado') {
			$botao_status = '<span style="display:inline-block; color:#fff; background:#198754; padding:8px; width:100%; text-align:center; font-size:12px; border-radius:6px;">Entregue <i class="fa fa-check"></i></span>';
		} else {
			$botao_status = '<a style="color:#fff; background:' . $cor_icone_link . '; padding:8px; width:100%; display:inline-block; text-align:center; font-size:12px; border-radius:6px;" title="' . $titulo_link . '" href="#" onclick="ativarPedido(\'' . $id . '\',\'' . $acao_link . '\',\'' . $telefone_cliente . '\',\'' . $valorF . '\',\'' . $tipo_pgto . '\',\'' . $hora_pedido . '\',\'' . $entrega . '\')">' . $status . ' <i class="fa fa-arrow-right"></i></a>';
		}

		$total_itens_preparo = (int) ($res[$i]['total_itens_preparo'] ?? 0);
		$itens_prontos = (int) ($res[$i]['itens_prontos'] ?? 0);

		if ($total_itens_preparo <= 0) {
			$resumo_preparo = '<span style="font-size:10px; color:#667085">Sem preparo</span>';
		} elseif ($itens_prontos >= $total_itens_preparo) {
			if (stripos((string) $entrega, 'delivery') !== false) {
				$texto_pronto = 'Pronto para entrega';
			} elseif (stripos((string) $entrega, 'retir') !== false || stripos((string) $tipo_pedido, 'balc') !== false) {
				$texto_pronto = 'Pronto para retirada';
			} else {
				$texto_pronto = 'Pronto para servir';
			}

			$resumo_preparo = '<span style="display:inline-block; max-width:105px; white-space:normal; line-height:1.15; font-size:10px; font-weight:700; color:#137333; background:#e6f4ea; border-radius:5px; padding:4px 6px"><i class="fa fa-check-circle"></i> ' . $texto_pronto . '</span>';
		} else {
			$percentual_preparo = (int) round(($itens_prontos / $total_itens_preparo) * 100);
			$resumo_preparo = '<div style="min-width:92px; max-width:105px; white-space:normal">';
			$resumo_preparo .= '<span style="font-size:10px; font-weight:600; color:#344054">Preparo ' . $itens_prontos . ' de ' . $total_itens_preparo . '</span>';
			$resumo_preparo .= '<div style="height:4px; margin-top:3px; overflow:hidden; border-radius:4px; background:#e4e7ec"><div style="height:100%; width:' . $percentual_preparo . '%; background:#f59e0b"></div></div>';
			$resumo_preparo .= '</div>';
		}


		echo <<<HTML
<tr style="background: {$classe_pago2}">
<td><sapn style="color:{$classe_alerta}"><i class="fa fa-square"></i></sapn> <b>Pedido ({$pedido})</b> / {$nome_cliente} <span class="{$classe_entrega}"><small>({$entrega}) {$span_balcao} </small></span>




		<a class="{$ocultar_alteracao}" style="display:{$display_alteracao}; margin-left:10px" href="#" onclick="darDesconto('{$id}', '{$cupomF}')" title="Dar Desconto"><i class="fa fa-usd text-success"></i></a>

		<form class="{$ocultar_alteracao}" method="POST" action="../../{$url_site}" target="_blank" style="display:{$display_alteracao};">
					<input type="hidden" name="id_edicao" value="{$id}">
					<button style="outline:none; background:transparent; border:none; " type="submit" title="Editar Pedido"><i class="fa fa-edit text-info"></i>
				</form>


</td>
<td>R$ {$valorF}</td>
<td>{$exibicao_pagamento}</td>
<td>R$ {$trocoF}</td>
<td>{$tipo_pgto}</td>
<td>{$resumo_preparo}</td>
<td>
<span id="area_status_{$id}">
{$botao_status}
</span>
<img id="status_link_{$id}" src="../../img/loading.gif" width="80px" style="display:none">

</td>
<td style="text-align:center; line-height:1.15; min-width:78px;">
	<span style="display:block; font-size:11px; font-weight:600; color:#344054;">{$dataF}</span>
	<span style="display:block; margin-top:3px; font-size:10px; color:#667085;">{$horaF}</span>
</td>
<td>	

		<a class="btn btn-{$classe_info}-light btn-sm" href="#" onclick="mostrar('{$id}', '{$nome_cliente}', '{$pedido}')" title="Ver Dados"><i class="fa fa-info-circle "></i></a>

		<a class="btn btn-secondary-light btn-sm" href="#" onclick="arquivarPedido('{$id}', '{$acao_arquivo}')" title="{$titulo_arquivo}"><i class="fa {$icone_arquivo}"></i></a>



		<big><a href="#" class="btn btn-danger-light btn-sm {$classe_excluir}" onclick="excluir('{$id}')" title="Excluir"><i class="fa fa-trash-can text-danger"></i></a></big>



<a id="btn_baixar_{$id}" class="btn btn-success-light btn-sm {$classe_excluir}" href="#" onclick="baixar('{$id}', '{$pedido}', '{$nome_cliente}', '{$tipo_pgto}', '{$valorF}', '{$valor_recebidoF}', '{$saldo_pendenteF}')" title="Confirmar Pgto">
<i class="fa fa-check-square"></i></a>



<a href="#" class="btn btn-success-light btn-sm  {$ocultar_modal_whats}" onclick="modalWhats('{$cliente}','Cliente')" title="Enviar Mensagem para o Cliente"><i class="fa-brands fa-whatsapp"></i></a>

<a class="btn btn-success-light btn-sm {$ocultar_icone_whats}" target="_blank" href="http://api.whatsapp.com/send?1=pt_BR&phone={$telefone_clienteF}" title="Whatsapp Cliente">
	<i class="fa-brands fa-whatsapp"></i></a>



			<a class="btn btn-dark-light btn-sm" href="#" onclick="gerarComprovante('{$id}', '{$tipo_pedido}')" title="Gerar Comprovante"><i class="fa fa-file-pdf-o"></i></a>		
			
		
	
		</td>
</tr>
HTML;
	}

	$total_vendasF = number_format($total_vendas, 2, ',', '.');

	echo <<<HTML
</tbody>
<small><div align="center" id="mensagem-excluir"></div></small>
</table>

<br>	
<div align="right">Total de Pedidos: <span class=""> {$num_total}</span> </div>

</small>


<div style="display:none" aria-hidden="true">
<a href="#" onclick="listar('{$busca}', 0)" class="btn btn-outline-primary "><i class="fa fa-chevron-left"></i></a>
HTML;

for($i = 0; $i < @$num_paginas; $i++){
	$estilo = '';
	if($pagina == $i){
		$estilo = 'active';
	}
	$pagina_cont = $i + 1;
	if($pagina >= ($i - 2) && $pagina <= ($i + 2)){ 
echo <<<HTML
		<a style="width:35px" href="#" onclick="listar('{$busca}', '{$i}')" class="btn btn btn-outline-primary {$estilo}">{$pagina_cont}</a>   
HTML;                  

}

}
echo <<<HTML
<a href="#" onclick="listar('{$busca}', '{$ultima_pagina}')" class="btn btn-outline-primary "><i class="fa fa-chevron-right"></i></a>
</div>

HTML;
} else {
	$pagina = 0;
	echo '<small>Não possui nenhum Pedido Hoje ainda!</small>';
}







$query = $pdo->query("SELECT * FROM vendas where data = CurDate() and status = 'Iniciado' and (arquivado IS NULL or arquivado != 'Sim') and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_dos_itens_pedidos = @count($res);


?>

<script type="text/javascript">
	$(document).ready(function() {

		var pagina = "<?= $pagina ?>";
		$('#pagina').val(pagina);

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
		$('#ace_pedidos').text("<?= $total_ace ?>");

		$('#total-dos-pedidos').text("<?= $total_dos_itens_pedidos ?>");

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
				url: 'paginas/' + pag + "/mudar-status.php",
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


	function darDesconto(id, descontoAtual){
		$('#id_desconto').val(id);
		$('#desconto_valor').val(descontoAtual || '0,00');
		$('#mensagem-desconto').text('').removeClass();
		$('#modalDesconto').modal('show');
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
