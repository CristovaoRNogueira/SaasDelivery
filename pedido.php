<?php
@session_start();
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
require_once("cabecalho.php");
require_once("js/ajax/ApiConfig.php");
require_once("sistema/assinatura_pedido.php");
require_once("sistema/detalhes_item_pedido.php");

$id = @$_GET['id'];

if($id != ''){
  $id_decode = base64_decode($id);
  $partes = explode('|', $id_decode);

  if(count($partes) == 2 && $partes[1] == 'hugo'){
    $id = $partes[0];
  } else {
    echo "<script>window.location='".$url_sistema.$url_site."'</script>";
    exit();
  }
}



//BUSCAR AS INFORMAÇÕES DO PEDIDO
$id = (int) $id;
$query = $pdo->prepare("SELECT * from vendas where id = :id and empresa = :empresa LIMIT 1");
$query->execute([':id' => $id, ':empresa' => $id_empresa]);
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if (@count($res) == 0) {
	http_response_code(404);
	echo 'Pedido não encontrado.';
	exit();
}

$cliente = $res[0]['cliente'];
$valor = $res[0]['valor'];
$total_pago = $res[0]['total_pago'];
$troco = $res[0]['troco'];
$data = $res[0]['data'];
$hora = $res[0]['hora'];
$status = $res[0]['status'];
$pago = $res[0]['pago'];
$obs = $res[0]['obs'];
$taxa_entrega = $res[0]['taxa_entrega'];
$tipo_pgto = $res[0]['tipo_pgto'];
$usuario_baixa = $res[0]['usuario_baixa'];
$entrega = $res[0]['entrega'];
$mesa = $res[0]['mesa'];
$nome_cliente_ped = $res[0]['nome_cliente'];
$cupom = $res[0]['cupom'];
$taxa_extra_pedido = (float) ($res[0]['taxa_extra_pedido'] ?? 0);
$n_pedido = $res[0]['pedido'];
$assinatura_pedido = gerarAssinaturaPedido($pdo, $id, $id_empresa);

$cupomF = number_format($cupom, 2, ',', '.');
$valorF = number_format($valor, 2, ',', '.');
$total_pagoF = number_format($total_pago, 2, ',', '.');
$trocoF = number_format($troco, 2, ',', '.');
$taxa_entregaF = number_format($taxa_entrega, 2, ',', '.');
$taxa_extra_pedidoF = number_format($taxa_extra_pedido, 2, ',', '.');
$dataF = implode('/', array_reverse(explode('-', $data)));
//$horaF = date("H:i", strtotime($hora));	

$valor_dos_itens = $valor - $taxa_entrega + $cupom;
$valor_dos_itensF = number_format($valor_dos_itens, 2, ',', '.');

$hora_pedido = date('H:i', strtotime("+$previsao_entrega minutes", strtotime($hora)));

if ($pago == 'Sim') {
	$cor_pago = 'green';
} else {
	$cor_pago = 'red';
}


$query = $pdo->query("SELECT * FROM clientes where id = '$cliente' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$cartoes = @$res[0]['cartoes'];
   

?>

<body style="background: #f8f9fa;">

	<nav class="navbar bg-white fixed-top" style="box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);">
		<div class="container-fluid" style="max-width: 680px;">
			<a class="navbar-brand d-flex align-items-center gap-2 text-dark text-decoration-none" href="<?php echo $url_sistema ?><?php echo $url_site ?>">
				<img src="<?php echo $url_sistema ?>sistema/img/<?php echo $logo_sistema ?>" alt="" width="38px" height="38px" style="object-fit: contain; border-radius: 8px;" class="d-inline-block align-text-top">
				<span class="fw-bold" style="font-size: 15px;">Acompanhar Pedido</span>
			</a>
			<a href="<?php echo $url_sistema ?><?php echo $url_site ?>?novo_pedido=1" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" style="font-size: 12px;">
				<i class="bi bi-plus-lg me-1"></i> Novo Pedido
			</a>
		</div>
	</nav>

	<div class="order-tracking-wrapper">

		<!-- Card de Sucesso / Cabeçalho -->
		<div class="order-tracking-card text-center">
			<div class="d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px; border-radius: 50%; background: #e8f5e9; color: #2e7d32;">
				<i class="bi bi-check2-circle" style="font-size: 32px;"></i>
			</div>
			<h4 class="fw-bold text-dark mb-1" style="letter-spacing: -0.3px;">Pedido #<?php echo $n_pedido ?></h4>
			<p class="text-muted mb-3" style="font-size: 13px;">Obrigado pela sua compra! Acompanhe o status abaixo em tempo real.</p>
			
			<div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
				<span class="badge rounded-pill px-3 py-2" style="background: #f1f3f5; color: #495057; font-size: 12px;">
					<i class="bi bi-calendar3 me-1"></i> <?php echo $dataF ?> às <?php echo $hora ?>
				</span>
				<span class="badge rounded-pill px-3 py-2" style="background: <?php echo ($pago == 'Sim') ? '#e8f5e9; color: #2e7d32;' : '#ffebee; color: #c62828;'; ?> font-size: 12px;">
					<i class="bi bi-cash-stack me-1"></i> <?php echo ($pago == 'Sim') ? 'Pago' : 'Pagamento Pendente'; ?>
				</span>
			</div>
		</div>

		<!-- Card da Timeline / Stepper Visual -->
		<div class="order-tracking-card">
			<div class="d-flex justify-content-between align-items-center mb-3">
				<h6 class="fw-bold text-dark m-0" style="font-size: 15px;">
					<i class="bi bi-clock-history text-danger me-1"></i> Status do Pedido
				</h6>
				<span class="badge bg-light text-secondary border rounded-pill" style="font-size: 11px;">
					<i class="bi bi-arrow-repeat me-1"></i> Atualização automática
				</span>
			</div>
			
			<div id="listar_st">
				<!-- Carregado via AJAX com layout moderno em Stepper -->
			</div>
		</div>

		<!-- Card de Resumo Financeiro e Detalhes da Entrega -->
		<div class="order-tracking-card">
			<h6 class="fw-bold text-dark mb-3" style="font-size: 15px;">
				<i class="bi bi-receipt text-danger me-1"></i> Resumo da Compra
			</h6>

			<div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 13px;">
				<span class="text-muted">Forma de Pagamento</span>
				<span class="fw-bold text-dark"><?php echo $tipo_pgto ?></span>
			</div>

			<div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 13px;">
				<span class="text-muted">Opção de Entrega</span>
				<span class="fw-bold text-dark"><?php echo $entrega ?></span>
			</div>

			<?php if ($taxa_entrega > 0) { ?>
			<div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 13px;">
				<span class="text-muted">Taxa de Entrega</span>
				<span class="fw-bold text-dark">R$ <?php echo $taxa_entregaF ?></span>
			</div>
			<?php } ?>

			<?php if ($taxa_extra_pedido > 0) { ?>
			<div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 13px;">
				<span class="text-muted"><?php echo $titulo_taxa ?></span>
				<span class="fw-bold text-dark">R$ <?php echo $taxa_extra_pedidoF ?></span>
			</div>
			<?php } ?>

			<?php if ($cupom > 0) { ?>
			<div class="d-flex justify-content-between align-items-center mb-2 text-success" style="font-size: 13px;">
				<span>Desconto Aplicado</span>
				<span class="fw-bold">- R$ <?php echo $cupomF ?></span>
			</div>
			<?php } ?>

			<?php if ($access_token == "" && @$dados_pagamento != "") { ?>
			<div class="p-2 mb-2 rounded-3 bg-light border" style="font-size: 12px;">
				<span class="text-muted">Chave Pix:</span> <strong class="text-dark"><?php echo $tipo_chave ?> <?php echo @$dados_pagamento ?></strong>
			</div>
			<?php } ?>

			<hr class="my-2" style="opacity: 0.12;">

			<div class="d-flex justify-content-between align-items-center mt-2">
				<span class="fw-bold text-dark" style="font-size: 15px;">Total do Pedido</span>
				<span class="fw-bold text-success" style="font-size: 18px;">R$ <?php echo $valorF ?></span>
			</div>
		</div>

		<!-- Card de Itens do Pedido -->
		<div class="order-tracking-card">
			<h6 class="fw-bold text-dark mb-3" style="font-size: 15px;">
				<i class="bi bi-basket2 text-danger me-1"></i> Itens do Pedido
			</h6>

			<div class="order-items-list">
			<?php
			$res = $pdo->query("SELECT * from carrinho where pedido = '$id' and empresa = '$id_empresa' order by id asc");
			$dados = $res->fetchAll(PDO::FETCH_ASSOC);
			$linhas = count($dados);

			for ($i = 0; $i < count($dados); $i++) {
				$id_carrinho = $dados[$i]['id'];
				$id_produto = $dados[$i]['produto'];
				$quantidade = $dados[$i]['quantidade'];
				$total_item = $dados[$i]['total_item'];
				$obs_item = $dados[$i]['obs'];
				$item = $dados[$i]['item'];
				$variacao = $dados[$i]['variacao'];
				$nome_produto_tab = $dados[$i]['nome_produto'];
				$sabores = $dados[$i]['sabores'];
				$borda = $dados[$i]['borda'];
				$categoria = $dados[$i]['categoria'];

				$query2 = $pdo->query("SELECT * FROM variacoes where id = '$variacao'");
				$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
				if (@count(@$res2) > 0) {
					$sigla_variacao = '(' . $res2[0]['sigla'] . ')';
				} else {
					$sigla_variacao = '';
				}

				$query2 = $pdo->query("SELECT * FROM temp where carrinho = '$id_carrinho' and tabela = 'Variação' order by id asc limit 1");
				$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
				$id_do_item = @$res2[0]['id_item'];

				$query2 = $pdo->query("SELECT * FROM itens_grade where id = '$id_do_item'");
				$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
				if (@$res2[0]['texto'] != "") {
					$sigla_grade = @$res2[0]['texto'];
				} else {
					$sigla_grade = '';
				}

				$query2 = $pdo->query("SELECT * FROM produtos where id = '$id_produto'");
				$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
				if (@count(@$res2) > 0) {
					$nome_produto = $res2[0]['nome'];
					$foto_produto = $res2[0]['foto'];
					$unidade = $res2[0]['unidade'];
				} else {
					$nome_produto = $nome_produto_tab;
					$foto_produto = "";
					$unidade = "";
				}

				$query3 = $pdo->query("SELECT * FROM unidade_medida where id = '$unidade'");
				$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
				if (@count($res3) > 0) {
					$nome_unidade = $res3[0]['nome'];
				} else {
					$nome_unidade = 'Sem Unidade';
				}

				$sigla_unidade = '';
				if ($nome_unidade == 'Quilogramas' or $nome_unidade == 'Quilo' or $nome_unidade == 'Quilograma' or $nome_unidade == 'KG') {
					$sigla_unidade = ' (KG)';
				}
				if ($nome_unidade == 'Metros' or $nome_unidade == 'Metro' or $nome_unidade == 'M' or $nome_unidade == 'm') {
					$sigla_unidade = ' (m)';
				}
				if ($nome_unidade == 'Litro' or $nome_unidade == 'Litros' or $nome_unidade == 'L') {
					$sigla_unidade = ' (L)';
				}

				$qt = explode(".", $quantidade);
				if ($qt[1] > 0) {
					$quantidadeF = $quantidade;
				} else {
					$quantidadeF = $qt[0];
				}

				$total_linha = $total_item * $quantidade;
				$total_itemF = number_format($total_linha, 2, ',', '.');
			?>
				<div class="order-item-row">
					<div style="flex: 1; min-width: 0;">
						<div class="fw-bold text-dark" style="font-size: 14px;">
							<span class="text-danger fw-bold"><?php echo $quantidadeF ?><?php echo $sigla_unidade ?>x</span> 
							<?php echo $nome_produto ?> <small class="text-secondary"><?php echo $sigla_variacao ?> <?php echo $sigla_grade ?></small>
						</div>
						<div class="mt-1" style="font-size: 12px;">
							<?php echo renderizarDetalhesItemPedido($pdo, $dados[$i]); ?>
						</div>
					</div>
					<div class="text-end ps-3" style="font-weight: 700; font-size: 14px; color: #212529;">
						R$ <?php echo $total_itemF ?>
					</div>
				</div>
			<?php } ?>
			</div>
		</div>

		<!-- Card de Fidelidade (se ativo) -->
		<?php if ($total_cartoes_config > 0) { ?>
		<div class="order-tracking-card text-center">
			<h6 class="fw-bold text-dark mb-2" style="font-size: 14px;">
				<i class="bi bi-star-fill text-warning me-1"></i> Cartão Fidelidade
			</h6>

			<?php if ($total_cartoes_config == $cartoes) { ?>
				<p class="text-success fw-bold mb-2" style="font-size: 13px;">
					Parabéns! Você completou <?php echo $total_cartoes_config ?> cartões fidelidade! Na próxima compra use o cupom <strong>cartao</strong> para ganhar R$ <?php echo $valor_cupom_config ?> de desconto!
				</p>
			<?php } else { ?>
				<p class="text-muted mb-2" style="font-size: 12px;">
					Você possui <strong><?php echo $cartoes ?> de <?php echo $total_cartoes_config ?></strong> selos. Complete para ganhar R$ <?php echo $valor_cupom_config ?> de desconto!
				</p>
			<?php } ?>

			<div class="d-flex justify-content-center align-items-center flex-wrap gap-2 mt-2">
			<?php 
			for ($i = 1; $i <= $total_cartoes_config; $i++) { 
				$atingido = ($cartoes >= $i);
			?>
				<div style="width: 42px; height: 42px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; background: <?php echo $atingido ? '#fff5f5' : '#f8f9fa'; ?>; border: 1.5px solid <?php echo $atingido ? '#ea1d2c' : '#dee2e6'; ?>;">
					<img src="<?php echo $url_sistema ?>img/cartao.png" width="28px" style="filter: grayscale(<?php echo $atingido ? 0 : 1 ?>); opacity: <?php echo $atingido ? 1 : 0.35 ?>;">
				</div>
			<?php } ?>
			</div>
		</div>
		<?php } ?>

		<!-- Botões de Ação Modernos -->
		<div class="d-flex flex-column gap-2 mt-3">
			<a href="http://api.whatsapp.com/send?1=pt_BR&phone=<?php echo $tel_whats ?>" target="_blank" class="btn-order-action btn-order-whatsapp">
				<i class="bi bi-whatsapp" style="font-size: 18px;"></i> Falar com o Restaurante no WhatsApp
			</a>

			<a href="<?php echo $url_sistema ?><?php echo $url_site ?>?novo_pedido=1" class="btn-order-action btn-order-new">
				<i class="bi bi-arrow-repeat" style="font-size: 17px;"></i> Fazer um Novo Pedido
			</a>
		</div>

	</div>

</body>


<script type="text/javascript">
	listarStatus();


	function listarStatus() {
		var id = "<?= $id ?>";
		var url_sistema = "<?= $url_sistema ?>";

		$.ajax({
			url: url_sistema + 'js/ajax/listar_status.php',
			method: 'POST',
			data: {
				id
			},
			dataType: "html",

			success: function(result) {
				$('#listar_st').html(result);
			}
		});
	}

	function verificarPedidoAtualizado() {
		var id = "<?= $id ?>";
		var assinaturaAtual = "<?= $assinatura_pedido ?>";
		var url_sistema = "<?= $url_sistema ?>";

		$.ajax({
			url: url_sistema + 'js/ajax/verificar_pedido_atualizado.php',
			method: 'POST',
			cache: false,
			data: { id },
			success: function(assinaturaNova) {
				if (assinaturaNova.trim() !== '' && assinaturaNova.trim() !== assinaturaAtual) {
					window.location.reload();
				}
			}
		});
	}

	setInterval(listarStatus, 15000);
	setInterval(verificarPedidoAtualizado, 5000);
</script>
