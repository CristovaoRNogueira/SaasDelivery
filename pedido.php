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

<body style="background: #ededdf;">
	<div class="container" align="center" style="padding:10px; font-size: 'Verdana';">
		<img src="<?php echo $url_sistema ?>img/check_ok.png" width="80px">
		<br>
		<span style="font-size: 18px; margin-top: 5px"><b>Nº PEDIDO <?php echo $n_pedido ?></b></span>
		<br>
		<span style="font-size: 12px; margin-top: -5px">Ficamos gratos por realizar essa compra!</span><br>

		<?php if($total_cartoes_config > 0){ ?>
		<span style="font-size: 12px; margin-top: -5px">Você ganhou mais um cartão Fidelidade</span><br>

		<span style="font-size: 12px; margin-top: -5px">Você possui <b><?php echo $cartoes ?> 
		<?php if($cartoes == 1) {
			echo 'Cartão';
		} else { 
			echo 'Cartões';
		} ?>
		 Fidelidade </b></span><br>

		<?php } ?>


		<span style="font-size: 13px;"><b>Novo Pedido -> </b> <span><a href="<?php echo $url_sistema ?><?php echo $url_site ?>?novo_pedido=1">Clique Aqui</a></span></span>


		<hr style="margin:7px">
		<p>
			<span style="font-size: 13px;"><b>Pagamento: </b> <span><?php echo $tipo_pgto ?></span></span> /
			<span style="font-size: 13px;"><b>Pago: </b> <span style="color:<?php echo $cor_pago ?>"><?php echo $pago ?></span></span>
			<br>
			<span style="font-size: 13px;"><b>Opção de Entrega: </b> <span><?php echo $entrega ?></span></span><br>
			<span style="font-size: 13px;"><b>Pagamento: <span style="color:<?php echo $cor_pago ?>; font-size: 15px">R$ <?php echo $valorF ?></span></b></span><br>
			<?php if ($taxa_extra_pedido > 0) { ?>
				<span style="font-size: 12px;"><b>Taxa adicional:</b> R$ <?php echo $taxa_extra_pedidoF ?></span><br>
			<?php } ?>
			<?php if ($access_token == "") { ?>
				<span style="font-size: 13px;"><b>Chave Pix:</b> <span style="font-size: 13px"><?php echo $tipo_chave ?> <?php echo @$dados_pagamento ?></span></span><br>
			<?php } ?>
		</p>

		<hr style="margin:7px">
		<p style="background: #757571; padding:2px; color:#FFF; font-size:13px">CONFIRA SEUS ITENS</p>



		<div class="row itens" style="font-size:12px">
			<?php
			$res = $pdo->query("SELECT * from carrinho where pedido = '$id' and empresa = '$id_empresa' order by id asc");
			$dados = $res->fetchAll(PDO::FETCH_ASSOC);
			$linhas = count($dados);

			$sub_tot;
			for ($i = 0; $i < count($dados); $i++) {
				foreach ($dados[$i] as $key => $value) {
				}
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
			if(@count($res3) > 0){
				$nome_unidade = $res3[0]['nome'];
			}else{
				$nome_unidade = 'Sem Unidade';
			}


		$ocultar_quantidades = '';
			$sigla_unidade = '';
			if($nome_unidade == 'Quilogramas' or $nome_unidade == 'Quilo' or $nome_unidade == 'Quilograma' or $nome_unidade == 'KG'){
				$sigla_unidade = ' (KG)';
				$ocultar_quantidades = 'ocultar';
			}

			if($nome_unidade == 'Metros' or $nome_unidade == 'Metro' or $nome_unidade == 'M' or $nome_unidade == 'm'){
				$sigla_unidade = ' (m)';
				$ocultar_quantidades = 'ocultar';
			}

			if($nome_unidade == 'Litro' or $nome_unidade == 'Litros' or $nome_unidade == 'L'){
				$sigla_unidade = ' (L)';
				$ocultar_quantidades = 'ocultar';
			}

			//tratamento separa string
			$qt = explode(".", $quantidade);
			if($qt[1] > 0){
				$quantidadeF = $quantidade;		
			}else{
				$quantidadeF = $qt[0];
			}

			?>



				<div align="left" class="col-9"> <span class=""><?php echo $quantidadeF ?> <?php echo $sigla_unidade ?> </span> - <?php echo $nome_produto ?> <?php echo $sigla_variacao ?> <?php echo $sigla_grade ?>

				</div>

				<div align="right" class="col-3">
					R$ <?php
							$total_linha = $total_item * $quantidade;
							$total_itemF = number_format($total_linha, 2, ',', '.');
							// $total = number_format( $cp1 , 2, ',', '.');
							echo $total_itemF;
							?>
				</div>

				<div class="col-12">
					<?php echo renderizarDetalhesItemPedido($pdo, $dados[$i]); ?>
				</div>

				<hr style="margin:2px">

			<?php } ?>
		</div>



		<div style="font-size: 12px; margin-top: 15px" align="left" id="listar_st">



		</div>


		<?php if($total_cartoes_config > 0){ ?>


		<br>

		<?php if($total_cartoes_config == $cartoes){ ?>

			<span style="font-size: 12px; margin-top: -5px">Você completou <b><?php echo $total_cartoes_config ?> cartões fidelidades </b>, em sua próxima compra digite a palavra cartao no campo de cupom de desconto para ganhar um desconto de  <b><?php echo $valor_cupom_config ?> Reais!</b></span><br>

		<?php }else{ ?>

			<span style="font-size: 12px; margin-top: -5px">Após completar <b><?php echo $total_cartoes_config ?> cartões </b>, você poderá trocar por um cupom de <b>desconto de <?php echo $valor_cupom_config ?> Reais!</b></span><br>
		
		<?php } ?>

<br>
<div class="row" style="display: flex; justify-content: center; flex-wrap: wrap;">
 <?php 
for($i=1; $i<=$total_cartoes_config; $i++){ 
		if($cartoes >= $i){
			$valor = 0;
			$opacity = 1;
		}else{
			$valor = 1;
			$opacity = 0.3;		}
	?>

<div class="col-md-1 col-2" align="center">
<img src="<?php echo $url_sistema ?>img/cartao.png" width="100%" style="filter: grayscale(<?php echo $valor ?>); filter: opacity(<?php echo $opacity ?>)">
</div>
<?php } ?>
</div>


<?php } ?>

<br>



		<div style="margin-top: 25px">
			<a href="http://api.whatsapp.com/send?1=pt_BR&phone=<?php echo $tel_whats ?>" target="_blank"><img src="<?php echo $url_sistema ?>img/whats.png" width="80%"></a>
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
