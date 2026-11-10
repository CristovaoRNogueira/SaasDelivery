<?php 
$pag = 'entregadores';
$itens_pag = 10;

@session_start();
$id_usuario = @$_SESSION['id'];

if(@$entregadores == 'ocultar'){
	echo "<script>window.location='index'</script>";
	exit();
}

// pegar a pagina atual
if(@$_POST['pagina'] == ""){
	@$_POST['pagina'] = 0;
}
$pagina = intval(@$_POST['pagina']);
$limite = $pagina * $itens_pag;

$numero_pagina = $pagina + 1;

if($pagina > 0){
	$pag_anterior = $pagina - 1;
	$pag_inativa_ant = '';
}else{
	$pag_anterior = $pagina;
	$pag_inativa_ant = 'desabilitar_botao';
}

$pag_proxima = $pagina + 1;


//totalizar páginas
$query2 = $pdo->query("SELECT * from $pag where id_entregador = '$id_usuario' or pedido like '%$buscar%' order by id desc");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$linhas2 = @count($res2);

$num_paginas = ceil($linhas2/$itens_pag);					
if($pag_proxima == $num_paginas){
	$pag_inativa_prox = 'desabilitar_botao';
	$pag_proxima = $pagina;						
}else{
	$pag_inativa_prox = '';

}


//TOTAL PEDENTE
$total_pedente = 0;
$query2 = $pdo->query("SELECT * from vendas where entregador = '$id_usuario'  and pago_entregador = 'Não' and status = 'Finalizado'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$linhas2 = @count($res2);
$taxa_entrega = $res2[0]['taxa_entrega'];

for($i=0; $i<$linhas2; $i++){

$total_pedente += $taxa_entrega;
}


//TOTAL PAGO
$total_pago = 0;
$query2 = $pdo->query("SELECT * from vendas where entregador = '$id_usuario'  and pago_entregador = 'Sim' and status = 'Finalizado'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$linhas2 = @count($res2);
$taxa_entrega = $res2[0]['taxa_entrega'];

for ($i = 0; $i < $linhas2; $i++) {

	$total_pago += $taxa_entrega;
}


?>
<div id="pages_maincontent" style="background: #FFF; ">

	<div class="loginform" style="margin-top: 10px">
		<form method="post">
			<input  type="text" name="buscar" id="buscar" value="<?php echo $buscar ?>" class="form_input required" placeholder="Buscar" style="background: transparent !important; width:80%; float:left" />
			<button id="btn_filtrar" class="limpar_botao" type="submit"><img src="images/icons/blue/search.png" width="23px" style="float:left; margin-top: 12px"></button>
		</form>
	</div>
	<div class="page_single layout_fullwidth_padding"> 

		<ul class="posts2" id="listar">
			<?php 
			$query = $pdo->query("SELECT * from $pag where id_entregador = '$id_usuario' and status = 'Finalizado' order by id desc LIMIT $limite, $itens_pag");
			$res = $query->fetchAll(PDO::FETCH_ASSOC);
			$linhas = @count($res);
			if($linhas > 0){
				for($i=0; $i<$linhas; $i++){
					$id = $res[$i]['id'];
					$nome = $res[$i]['nome'];
					$pedido = $res[$i]['pedido'];
					$cliente = $res[$i]['cliente'];	
					$data = $res[$i]['data'];
					$status = $res[$i]['status'];
					$pago = $res[$i]['pago'];

					$valor = $res[$i]['valor'];
					$taxa_entrega = $res[$i]['taxa_entrega'];

					$dataF = implode('/', array_reverse(@explode('-', $data)));

					$taxa_entregaF = @number_format($taxa_entrega, 2, ',', '.');
					
					

echo <<<HTML
					<li class="">
					<div class="swiper-wrapper">		
					<div class="swiper-slide swipeout-content item-content">
					<div class="post_entry" onclick="mostrar('{$nome}','{$pedido}','{$cliente}','{$dataF}', '{$status}','{$pago}','{$valor}','{$taxa_entrega}')" title="Mostrar Dados">
					
					<div class="post_details">
					<div class="post_category textos_list">Pedido {$pedido}</div>				
					<p style="font-size:12px">{$cliente}</p>
					<p class="subtitulo_list">{$dataF}</p>
					<p class="subtitulo_list" style="color: green">{$taxa_entregaF}</p>
					</div>
					<div class="post_swipe"><img src="images/swipe_more.png" alt="" title="" /></div>
					</div>
					</div>
					<div class="swiper-slide swipeout-actions-right">

		
					
					</div>
					</div>
					</li>	
HTML;

				}

			}else{
				echo 'Nenhum Registro Encontrado!';
			}
			?> 

		</ul>

		
		<div class="shop_pagination">
			<a href="#" onclick="paginar('<?php echo $pag_anterior ?>', '<?php echo $buscar ?>')" class="prev_shop <?php echo $pag_inativa_ant ?>">'ANTERIOR'</a>
			<span class="shop_pagenr"><?php echo @$numero_pagina ?>/<?php echo @$num_paginas ?></span>
			<a href="#" onclick="paginar('<?php echo $pag_proxima ?>', '<?php echo $buscar ?>')" class="next_shop <?php echo $pag_inativa_prox ?>">PRÓXIMA</a>
		</div>	

		<form method="post" style="display:none">
			<input type="text" name="pagina" id="input_pagina">
			<input type="text" name="buscar" id="input_buscar">
			<button type="submit" id="paginacao"></button>
		</form>

		<p align="right" style="margin-top: -10px">
				<span style="margin-right: 10px; font-size: 11px;">Total Pendentes  <span style="color:red">R$ <?php echo $total_pedente ?></span></span>
				<span>Total Pago  <span style="color:green; font-size: 11px;">R$ <?php echo $total_pago ?> </span></span>
			</p>

	</div>	


</div>


</div>
</div>
</div>

</div>
</div>




<a style="display:block" href="#" class="action1 open-popup" data-popup=".popup-mostrar" id="btn_mostrar"></a>
<!-- Social Icons Popup -->
<div class="popup popup-mostrar" style="width:85%; margin:20px;  background: transparent; margin-top: 100px; ">
	<div class="content-block">

		<h2 class="page_title_dados" id="titulo_dados"></h2>   


		<ul class="responsive_table tabela_dados" style="">

			<li class="table_row celula_tabela">
				<div class="table_section" style="width:25%">Pedido</div>
				<div id="pedido_dados" class="table_section" style="width:70%"></div>                        
			</li>

			<li class="table_row celula_tabela">
				<div class="table_section" style="width:25%">Cliente</div>
				<div id="cliente_dados" class="table_section" style="width:70%"></div>                        
			</li>

			<li class="table_row celula_tabela">
				<div class="table_section" style="width:25%">Data</div>
				<div id="data_dados" class="table_section" style="width:70%"></div>                        
			</li>


			<li class="table_row celula_tabela">
				<div class="table_section" style="width:25%">Status</div>
				<div id="status_dados" class="table_section" style="width:70%"></div>                        
			</li>

			<li class="table_row celula_tabela">
				<div class="table_section" style="width:25%">Pago</div>
				<div id="pago_dados" class="table_section" style="width:70%"></div>                        
			</li>


			<li class="table_row celula_tabela">
				<div class="table_section" style="width:25%">Valor</div>
				<div id="valor_dados" class="table_section" style="width:70%"></div>                        
			</li>

			<li class="table_row celula_tabela">
				<div class="table_section" style="width:25%">Taxa</div>
				<div id="taxa_entrega_dados" class="table_section" style="width:70%"></div>                        
			</li>

		</ul>



		<div class="close_popup_button"><a href="#" class="close-popup" data-popup=".popup-mostrar"><img style="margin-top: 10px" src="images/icons/white/menu_close.png" alt="" title="" width="25px"/></a></div>
	</div>
</div>













<script type="text/javascript">var pag = "<?=$pag?>"</script>

<script type="text/javascript">



	function mostrar(nome, pedido, cliente, dataF, status, pago, valor, taxa_entrega){

		$('#titulo_dados').text(nome);
		$('#pedido_dados').text(pedido);
		$('#cliente_dados').text(cliente);
		$('#data_dados').text(dataF);
		$('#status_dados').text(status);
		$('#pago_dados').text(pago);
		$('#valor_dados').text(valor);
		$('#taxa_entrega_dados').text(taxa_entrega);   	

		$('#btn_mostrar').click();
	}


	
	
</script>




