<?php
@session_start();
$id_usuario = @$_SESSION['id'];
$pag = 'vendas';
$itens_pag = 10;

if(@$pag == 'ocultar'){
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
$query2 = $pdo->query("SELECT * from $pag where entregador = '$id' order by id asc");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$linhas2 = @count($res2);

$num_paginas = ceil($linhas2/$itens_pag);					
if($pag_proxima == $num_paginas){
	$pag_inativa_prox = 'desabilitar_botao';
	$pag_proxima = $pagina;						
}else{
	$pag_inativa_prox = '';

}


?>
<div id="pages_maincontent" style="background: #FFF; ">


	<div class="page_single layout_fullwidth_padding" style="margin-top: 10px"> 

		<ul class="posts2" id="listar">
			<?php 
			$query = $pdo->query("SELECT * from $pag where entregador = '$id_usuario' and pedido like '%$buscar%' and status != 'Finalizado' order by id asc LIMIT $limite, $itens_pag");
			$res = $query->fetchAll(PDO::FETCH_ASSOC);
			$linhas = @count($res);
			if($linhas > 0){
				for($i=0; $i<$linhas; $i++){
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
					$tipo_pgto = $res[$i]['tipo_pgto'];
					$usuario_baixa = $res[$i]['usuario_baixa'];
					$entrega = $res[$i]['entrega'];
					$mesa = $res[$i]['mesa'];
					$nome_cliente_ped = $res[$i]['nome_cliente'];
					$cupom = $res[$i]['cupom'];
					$n_pedido = $res[$i]['pedido'];

					$cupomF = number_format($cupom, 2, ',', '.');
					$valorF = number_format($valor, 2, ',', '.');
					$total_pagoF = number_format($total_pago, 2, ',', '.');
					$trocoF = number_format($troco, 2, ',', '.');
					$taxa_entregaF = number_format($taxa_entrega, 2, ',', '.');
					$dataF = implode('/', array_reverse(explode('-', $data)));



					$valor_dos_itens = $valor - $taxa_entrega + $cupom;
					$valor_dos_itensF = number_format($valor_dos_itens, 2, ',', '.');

					$hora_pedido = date('H:i', strtotime("+$previsao_entrega minutes", strtotime($hora)));

					$query2 = $pdo->query("SELECT * FROM clientes where id = '$cliente'");
					$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
					$total_reg2 = @count($res2);
					if ($total_reg2 > 0) {
						$nome_cliente = @$res2[0]['nome'];
						$telefone_cliente = @$res2[0]['telefone'];
						$rua_cliente = @$res2[0]['endereco'];
						$numero_cliente = @$res2[0]['numero'];
						$complemento_cliente = @$res2[0]['complemento'];
						$bairro_cliente = @$res2[0]['bairro'];
						$cidade_cliente = @$res2[0]['cidade'];
						$cep_cliente = @$res2[0]['cep'];
					}

					if($cep_cliente != ''){
						$cep_cliente = 'CEP:'.$cep_cliente;
					}


					$tel_whatsF = '55' . preg_replace('/[ ()-]+/', '', $telefone_cliente);


					if ($obs != "") {
							$obs_item = 'Observações: '.$obs;
					}else{
						$obs_item = '';
					}


	



echo <<<HTML
					<li class="swipeout">
					<div class="swiper-wrapper">		
					<div class="swiper-slide swipeout-content item-content">
					<div class="post_entry"  onclick="mostrar('{$nome_cliente}','{$telefone_cliente}','{$rua_cliente}','{$numero_cliente}', '{$complemento_cliente}','{$bairro_cliente}','{$cidade_cliente}','{$n_pedido}','{$pago}','{$valorF}','{$total_pagoF}','{$trocoF}','{$tipo_pgto}','{$hora_pedido}','{$obs_item}')" title="Mostrar Dados">
					
					<div class="post_details">
					<div class="post_category textos_list">Pedido N° {$n_pedido}</div>				
					<p style="font-size:12px">{$nome_cliente} {$telefone_cliente}</p>
					<p class="subtitulo_list">{$rua_cliente}, {$numero_cliente} {$complemento_cliente} {$bairro_cliente} {$cidade_cliente}</p>
					</div>
					<div class="post_swipe"><img src="images/swipe_more.png" alt="" title="" /></div>
					</div>
					</div>
					<div class="swiper-slide swipeout-actions-right">


					<a target="_blank" style="width: 17%; background: #1d8c32" href="http://api.whatsapp.com/send?1=pt_BR&phone={$tel_whatsF}" title="Whatsapp"><img src="images/icons/white/whatsapp.png" alt="" title="" /></a>


					<a target="_blank" style="width: 17%; background: #00bdff" href="http://maps.google.com/?saddr=Current%20Location&daddr={$rua_cliente},%20{$numero_cliente}%20{$bairro_cliente}%20{$cep_cliente}" class="action1"><img src="images/icons/white/map.png" alt="" title="Editar" /></a>


					<a onclick="confirmarEntrega('{$id}')" style="width: 17%; background: #000bbb" href="#"><img src="images/icons/white/square-check.png" alt="" title="Confirmar Entrega" /></a>


					
					
					
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
			<a href="#" onclick="paginar('<?php echo $pag_anterior ?>', '<?php echo $buscar ?>')" class="prev_shop <?php echo $pag_inativa_ant ?>">ANTERIOR</a>
			<span class="shop_pagenr"><?php echo @$numero_pagina ?>/<?php echo @$num_paginas ?></span>
			<a href="#" onclick="paginar('<?php echo $pag_proxima ?>', '<?php echo $buscar ?>')" class="next_shop <?php echo $pag_inativa_prox ?>">PRÓXIMA</a>
		</div>	

		<form method="post" style="display:none">
			<input type="text" name="pagina" id="input_pagina">
			<input type="text" name="buscar" id="input_buscar">
			<button type="submit" id="paginacao"></button>
		</form>

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
				<div class="table_section" style="width:25%">N° Pedido:</div>
				<div id="n_pedido_dados" class="table_section" style="width:70%"></div>                        
			</li>

			<li class="table_row celula_tabela">
				<div class="table_section" style="width:25%">Telefone:</div>
				<div id="telefone_dados" class="table_section" style="width:70%"></div>                     
			</li>


			<li class="table_row celula_tabela">
				<div class="table_section" style="width:25%">Pago:</div>
				<div id="pago_dados" class="table_section" style="width:70%"></div>                        
			</li>

			<li class="table_row celula_tabela">
				<div class="table_section" style="width:25%">Forma PGTO:</div>
				<div id="tipo_pgto_dados" class="table_section" style="width:70%"></div>                        
			</li>

			<li class="table_row celula_tabela">
				<div class="table_section" style="width:25%">Valor:</div>
				<div id="valor_dados" class="table_section" style="width:70%"></div>                        
			</li>

			<?php if($tipo_pgto == 'Dinheiro'){ ?>

		
			<li class="table_row celula_tabela">
				<div class="table_section" style="width:25%">Total Pago:</div>
				<div id="total_pago_dados" class="table_section" style="width:70%"></div>                        
			</li>

			<li class="table_row celula_tabela">
				<div class="table_section" style="width:25%">Troco:</div>
				<div id="troco_dados" class="table_section" style="width:70%"></div>                        
			</li>
			<?php } ?>


			<li class="table_row celula_tabela">
				<div class="table_section" style="width:30%">Previsão Entrega:</div><b>
				<div id="hora_pedido_dados" class="table_section" style="width:60%"></div></b>                       
			</li>

		

			<li class="table_row celula_tabela">
				<div class="table_section" style="width:20%">Endereço:</div>
				<div id="endereco_dados" class="table_section" style="width:70%"></div>                        
			</li>


			<li class="table_row celula_tabela">
				<div class="table_section" style="width:25%">Observação:</div>
				<div id="obs_item_dados" class="table_section" style="width:70%"></div>                        
			</li>


		</ul>	




		<div class="close_popup_button"><a href="#" class="close-popup" data-popup=".popup-mostrar"><img style="margin-top: 10px" src="images/icons/white/menu_close.png" alt="" title="" width="25px"/></a></div>
	</div>
</div>






<a style="display:block" href="#" class="action1 open-popup" data-popup=".popup-confirmar" id="btn_confirmar"></a>
<div class="popup popup-confirmar"
	style="width:85%; margin:20px;  background: transparent; margin-top: 100px; overflow: none ">
	<div class="content-block">

		<h2 class="page_title_excluir">CONFIRMAR ENTREGA</h2>

		<div class="botoes_confirmar" align="center" style="height: 70px; border:1px solid #bdbbbb">
			<div style="width:49%; float:left"><input onclick="confirmar()" type="button"
					class="form_submit botao_confirmar botao_modelo" value="CONFIRMAR" /></div>
			<div style="width:49%; float:right"><input data-popup=".popup-confirmar" type="button"
					class="form_submit botao_cancelar botao_modelo close-popup" value="CANCELAR" /></div>
					<br>
					<small><div id="mensagem-confirmar" align="center"></div></small>
		</div>
		

		<input type="hidden" id="id_confirmar">

		

		<div class="close_popup_button"><a href="#" class="close-popup" data-popup=".popup-confirmar"><img
					style="margin-top: 10px" src="images/icons/white/menu_close.png" alt="" title="" width="25px" /></a></div>

	</div>
</div>










<script type="text/javascript">var pag = "<?=$pag?>"</script>

<script type="text/javascript">




	function mostrar(nome_cliente, telefone_cliente, rua_cliente, numero_cliente, complemento_cliente, bairro_cliente, cidade_cliente, n_pedido, pago, valorF, total_pagoF, trocoF, tipo_pgto, hora_pedido, obs_item){

		$('#titulo_dados').text(nome_cliente);
		$('#telefone_dados').text(telefone_cliente);
		$('#endereco_dados').text(rua_cliente+' '+numero_cliente+' '+complemento_cliente+' '+bairro_cliente+' '+cidade_cliente);
		$('#n_pedido_dados').text(n_pedido);
		$('#pago_dados').text(pago);
		$('#valor_dados').text(valorF);
		$('#total_pago_dados').text(total_pagoF);
		$('#troco_dados').text(trocoF);
		$('#tipo_pgto_dados').text(tipo_pgto);
		$('#hora_pedido_dados').text(hora_pedido);
		$('#obs_item_dados').text(obs_item);
  	

		$('#btn_mostrar').click();
	}


	function status(id, nome, status) {
		$('#id_status').val(id);
		$('#status_alterar').val(status).change();
		$('#titulo_status').text(nome);
		$('#btn_status').click();
	}

	function confirmarEntrega(id){
		$('#id_confirmar').val(id);    	  	    
		$('#btn_confirmar').click();
	}

	function confirmar(){
		
		var id =  $('#id_confirmar').val();    
    $('#mensagem-confirmar').text('Carregando...')
    
    $.ajax({
        url: 'paginas/' + pag + "/mudar-status.php",
        method: 'POST',
        data: {id},
        dataType: "html",

        success:function(mensagem){


            if (mensagem.trim() == "Alterado com Sucesso") { 
								location.reload();
								//$('#btn_confirmar').click();
            } else {

							$('#mensagem-confirmar').text(mensagem)
               
            }
        }
    });
	}




	
</script>




