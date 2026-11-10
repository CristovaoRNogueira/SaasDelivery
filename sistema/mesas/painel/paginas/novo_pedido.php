<?php
$pag = 'novo_pedido';

$itens_pag = 10;

if (@$pag == 'ocultar') {
	echo "<script>window.location='index'</script>";
	exit();
}

// pegar a pagina atual
if (@$_POST['pagina'] == "") {
	@$_POST['pagina'] = 0;
}
$pagina = intval(@$_POST['pagina']);
$limite = $pagina * $itens_pag;

$numero_pagina = $pagina + 1;

if ($pagina > 0) {
	$pag_anterior = $pagina - 1;
	$pag_inativa_ant = '';
} else {
	$pag_anterior = $pagina;
	$pag_inativa_ant = 'desabilitar_botao';
}

$pag_proxima = $pagina + 1;


//totalizar páginas
$query2 = $pdo->query("SELECT * from mesas where nome like '%$buscar%' order by id asc");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$linhas2 = @count($res2);

$num_paginas = ceil($linhas2 / $itens_pag);
if ($pag_proxima == $num_paginas) {
	$pag_inativa_prox = 'desabilitar_botao';
	$pag_proxima = $pagina;
} else {
	$pag_inativa_prox = '';
}


?>
<div id="pages_maincontent" style="background: #FFF; ">

	<div class="loginform" style="margin-top: 10px">
		<form method="post">
			<input type="text" name="buscar" id="buscar" value="<?php echo $buscar ?>" class="form_input required"
				placeholder="Buscar Mesas" style="background: transparent !important; width:80%; float:left" />
			<button id="btn_filtrar" class="limpar_botao" type="submit"><img src="images/icons/blue/search.png" width="23px"
					style="float:left; margin-top: 12px"></button>
		</form>
	</div>
	<div class="page_single layout_fullwidth_padding">

		<ul class="posts2" id="listar">
			<?php
			$query = $pdo->query("SELECT * from mesas where nome like '%$buscar%' order by id asc LIMIT $limite, $itens_pag");
			$res = $query->fetchAll(PDO::FETCH_ASSOC);
			$linhas = @count($res);
			if ($linhas > 0) {
				for ($i = 0; $i < $linhas; $i++) {
					$id = $res[$i]['id'];
					$nome = $res[$i]['nome'];
					$ativo = $res[$i]['ativo'];
					$status = $res[$i]['status'];



					$mostrar_fechar = '';

					//abertura da mesa
					$query2 = $pdo->query("SELECT * FROM abertura_mesa where mesa = '$id' and status = 'Aberta'");
					$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
					$total_reg2 = @count($res2);
					if ($total_reg2 > 0) {
						$total = $res2[0]['total'];
						$cliente = $res2[0]['cliente'];
						$horario_abertura = $res2[0]['horario_abertura'];
						$garcon = $res2[0]['garcon'];
						$obs = $res2[0]['obs'];
						$id_abertura_mesa = $res2[0]['id'];
						$pessoas = $res2[0]['pessoas'];
						$status_abertura = $res2[0]['status'];

						if ($obs == "") {
							$obsF = '';
						} else {
							$obsF = '(' . $res2[0]['obs'] . ')';
						}


						$query2 = $pdo->query("SELECT * FROM usuarios where id = '$garcon' ");
						$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
						$nome_garcon = '(' . $res2[0]['nome'] . ')';

						$horario_aberturaF = date('H:i', @strtotime($horario_abertura));

						$pessoas_mesa = '(' . $pessoas . ')';
					} else {
						$total = 0;
						$cliente = "";
						$horario_aberturaF = "<span style='color:green; font-size:10px'>(Disponível)</span>";
						$nome_garcon = "&nbsp;";
						$totalF = '';
						$obs = '';
						$pessoas_mesa = '';
						$mostrar_fechar = 'ocultar';
						$pessoas = '0';
						$id_abertura_mesa = '';
						$garcon = '';
						$status_abertura = '';
						$obsF = '';
					}



					$ocultar_botoes = 'ocultar';
					if ($ativo == 'Sim') {
						$imagem_mesa = 'mesa_verde.png';
						if ($status == 'Aberta') {
							$imagem_mesa = 'mesa_vermelha.png';
							$ocultar_botoes = '';
						} else {
							$imagem_mesa = 'mesa_verde.png';
						}
					} else {
						$imagem_mesa = 'mesa_inativa.png';
						$horario_aberturaF = "<span style='color:gray; font-size:10px'>(Inativa)</span>";
					}



					//totalizar o valor dos itens
					$total_mesa = 0;
					$query2 = $pdo->query("SELECT * FROM carrinho where mesa = '$id_abertura_mesa' ");
					$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
					$total_reg2 = @count($res2);
					if ($total_reg2 > 0 and $status_abertura == 'Aberta') {
						for ($i2 = 0; $i2 < $total_reg2; $i2++) {
							$total_mesa += $res2[$i2]['total_item'];
						}
					} else {
						$total_mesa = 0;
					}





					if ($ativo == 'Sim' and $status == 'Aberta') {
						$totalF = 'R$ ' . @number_format($total_mesa, 2, ',', '.');
					}



					//calcular a comissao
					if ($comissao_garcon > 0 and $total_mesa > 0) {
						$total_comissao = $total_mesa * $comissao_garcon / 100;
					} else {
						$total_comissao = 0;
					}

					$total_comissaoF = @number_format($total_comissao, 2, ',', '.');


					//calcular o couvert
					$total_couvert = $pessoas * $couvert;
					$total_couvertF = @number_format($total_couvert, 2, ',', '.');

					$subtotal = $total_mesa + $total_comissao + $total_couvert;
					$subtotalF = @number_format($subtotal, 2, ',', '.');

					$ocultar_botoes = 'ocultar';
					if ($status == 'Aberta') {
						$ocultar_botoes = '';
					}



					echo <<<HTML
					<li class="swipeout">
					<div class="swiper-wrapper">		
					<div class="swiper-slide swipeout-content item-content">
					<div class="post_entry" onclick="abrirMesa('{$id}', '{$status}', '{$nome}','{$obsF}', '{$id_abertura_mesa}','{$ativo}')" title="Abrir Mesa">
					
					<div class="post_details">
					<div class="post_category textos_list">Mesa {$nome}</div>				
					<p style="font-size:12px">{$horario_aberturaF} <span style="font-size: 12px; color:red">{$totalF}</span>{$pessoas_mesa}</p>
					<p class="subtitulo_list">{$nome_garcon} </p>
					</div>
					<div class="post_swipe"><img src="images/swipe_more.png" alt="" title="" /></div>
					</div>
					</div>
					<div class="swiper-slide swipeout-actions-right">
						


					<a class="{$mostrar_fechar}" href="../../painel/rel/comprovante_mesa.php?id={$id_abertura_mesa}" target="_blank" style="width: 16%; background: #4d4d4d" href="#"><img src="images/icons/white/arquivo.png" alt="" title="Ativar / Desativar" /></a>

					<a class="{$mostrar_fechar}" onclick="editar('{$id_abertura_mesa}', '{$cliente}', '{$garcon}', '{$obs}', '{$pessoas}')" style="width: 16%; background: #3c6cb5" href="#" class="action1"><img src="images/icons/white/edit.png" alt="" title="Editar" /></a>

					<big><a onclick="abrirMesa('{$id}', '{$status}', '{$nome}','{$obsF}', '{$id_abertura_mesa}','{$ativo}')" style="width: 16%; background: #00bdff" href="#" class="action1"><img src="images/icons/white/plus2.png" alt="" title="Abrir Mesa" /></a></big>

			
					
					
					
					</div>
					</div>
					</li>	
HTML;
				}
			} else {
				echo 'Nenhum Registro Encontrado!';
			}
			?>

		</ul>


		<div class="shop_pagination">
			<a href="#" onclick="paginar('<?php echo $pag_anterior ?>', '<?php echo $buscar ?>')"
				class="prev_shop <?php echo $pag_inativa_ant ?>">ANTERIOR</a>
			<span class="shop_pagenr"><?php echo @$numero_pagina ?>/<?php echo @$num_paginas ?></span>
			<a href="#" onclick="paginar('<?php echo $pag_proxima ?>', '<?php echo $buscar ?>')"
				class="next_shop <?php echo $pag_inativa_prox ?>">PRÓXIMA</a>
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





<!-- POPUP ABRIR MESA -->
<a style="display:block" href="#" class="action1 open-popup" data-popup=".popup-mesa" id="btn_mesa"></a>

<div class="popup popup-mesa" id="popupMesa">
	<div class="content-block" style="padding:5px !important">
		<h5 class="page_title"><b><span id="titulo_inserir"></span> <span style="background: #fff" id="nome_da_mesa"></span></b></h5>
		<div class="page_single layout_fullwidth_padding">
			<div class="contactform">
				<form id="form" method="post">

					<div class="form_row">
						<label class="labels_form">Nome do Cliente</label>
						<input type="text" name="nome" id="nome" value="" class="form_input" placeholder="Nome do Cliente" />
					</div>

					<div style="width:100%; float:right">
						<label class="labels_form">Garçon</label>
						<div class="selector_overlay">
							<select name="garcon" id="garcon" class="" style="height:40px; !important;">
								<?php
								$query = $pdo->query("SELECT * FROM usuarios where nivel = 'Garçon' or nivel = 'Atendente' ORDER BY nome asc");
								$res = $query->fetchAll(PDO::FETCH_ASSOC);
								$total_reg = @count($res);
								if ($total_reg > 0) {
									for ($i = 0; $i < $total_reg; $i++) {
										if ($res[$i]['id'] == $id_usuario) {
											echo '<option selected value="' . $res[$i]['id'] . '">' . $res[$i]['nome'] . '</option>';
										} else {
											echo '<option value="' . $res[$i]['id'] . '">' . $res[$i]['nome'] . '</option>';
										}
									}
								} else {
									echo '<option value="0">Cadastre um Garçon</option>';
								}
								?>
							</select>
						</div>
					</div>


					<div style="width:28%; float:left">
						<label class="labels_form">Pessoa Mesa</label>
						<input type="text" name="pessoas" id="pessoas" value="" class="form_input" style="height:32px;"
							placeholder="1" required />
					</div>


					<div style="width:66%; float:right">
						<label class="labels_form">Observação</label>
						<input type="text" name="obs" id="obs" value="" style="height:25px;" class="form_input"
							placeholder="Observações" />
					</div>





					<input type="submit" name="btn_salvar" class="form_submit botao_salvar" id="btn_salvar" value="SALVAR" />
					<div style="display:none; text-align: center;" id="img_loader"><img src="images/loader.gif"></div>

					<input type="hidden" name="id" id="id_abertura">
					<input type="hidden" name="id_ab" id="id">

				</form>
			</div>
		</div>
	</div>
	<div class="close_popup_button">
		<a id="btn-fechar" href="#" onclick="limparCampos()" class="close-popup" data-popup=".popup-mesa"><img
				src="images/icons/black/menu_close.png" alt="" title="" /></a>
	</div>
</div>




<!-- POPUP FECHAR MESA -->
<a style="display:block" href="#" class="action1 open-popup" data-popup=".popup-fechamento" id="btn_fechamento"></a>

<div class="popup popup-fechamento" id="popupFechamento">
	<div class="content-block" style="padding:5px !important">
		<h5 class="page_title"><b>Fechar Mesa</b></h5>
		<div class="page_single layout_fullwidth_padding">
			<div class="contactform">
				<form id="form_fechamento" method="post">


					<div style="width:100%; float:right">
						<label class="labels_form">Garçon</label>
						<div class="selector_overlay">
							<select name="garcon" id="garcon" class="" style="height:40px; !important;">
								<?php
								$query = $pdo->query("SELECT * FROM usuarios where nivel = 'Garçon' or nivel = 'Atendente' ORDER BY nome asc");
								$res = $query->fetchAll(PDO::FETCH_ASSOC);
								$total_reg = @count($res);
								if ($total_reg > 0) {
									for ($i = 0; $i < $total_reg; $i++) {
										if ($res[$i]['id'] == $id_usuario) {
											echo '<option selected value="' . $res[$i]['id'] . '">' . $res[$i]['nome'] . '</option>';
										} else {
											echo '<option value="' . $res[$i]['id'] . '">' . $res[$i]['nome'] . '</option>';
										}
									}
								} else {
									echo '<option value="0">Cadastre um Garçon</option>';
								}
								?>
							</select>
						</div>
					</div>


					<div style="width:48%; float:left">
						<label class="labels_form">Total Itens</label>
						<input readonly="true" type="text" name="total_itens" id="total_itens_fechamento" value=""
							class="form_input" placeholder="Total Itens" style="height:30px;" />
					</div>


					<div style="width:48%; float:right">
						<label class="labels_form">Comissão R$</label>
						<input onkeyup="calcular()" type="text" name="comissao" id="comissao_fechamento" value="" class="form_input"
							style="height:30px;" />
					</div>


					<div style="width:48%; float:left">
						<label class="labels_form">Couvert</label>
						<input onkeyup="calcular()" type="text" name="couvert" id="couvert_fechamento" value="" class="form_input"
							style="height:30px;" />
					</div>

					<div style="width:48%; float:right">
						<label class="labels_form">Subtotal</label>
						<input onkeyup="calcular()" type="text" name="subtotal" id="subtotal_fechamento" value="" class="form_input"
							style="height:30px;" />
					</div>


					<div style="width:100%; float:right">
						<label class="labels_form">Forma de Pagamento</label>
						<div class="selector_overlay">
							<select name="forma_pgto" id="forma_pgto" class="" style="height:40px; !important;">
								<?php
								$query = $pdo->query("SELECT * FROM  formas_pgto ORDER BY id asc");
								$res = $query->fetchAll(PDO::FETCH_ASSOC);
								$total_reg = @count($res);
								if ($total_reg > 0) {
									for ($i = 0; $i < $total_reg; $i++) {
										echo '<option value="' . $res[$i]['nome'] . '">' . $res[$i]['nome'] . '</option>';
									}
								} else {
									echo '<option value="0">Cadastre uma Forma de Pagamento</option>';
								}
								?>
							</select>
						</div>
					</div>




					<div class="form_row">
						<label class="labels_form">Observações</label>
						<input type="text" name="obs" id="obs_fechamento" class="form_input" placeholder="Observações" />
					</div>



					<input type="submit" name="btn_salvar_fechamento" class="form_submit botao_salvar" id="btn_salvar_fechamento"
						value="FECHAR" />



					<div align="center" style="display:none" id="img_loader"><img src="images/loader.gif"></div>

					<input type="hidden" name="id" id="id_fechamento">
					<input type="hidden" name="id_ab" id="id_ab_fechamento">

				</form>
			</div>
		</div>
	</div>
	<div class="close_popup_button">
		<a id="btn-fechamento" href="#" onclick="limparCampos()" class="close-popup" data-popup=".popup-fechamento"><img
				src="images/icons/black/menu_close.png" alt="" title="" /></a>
	</div>
</div>




<!-- POPUP ITENS -->
<a style="display:block" href="#" class="action1 open-popup" data-popup=".popup-itens" id="btn_itens"></a>

<div class="popup popup-itens" id="popupItens">
	<div class="content-block" style="padding:5px !important">
		<h5 class="page_title"><b>Mesa <span style="background: transparent" id="nome_da_mesa_itens"></span></b></h5>
		<div class="page_single layout_fullwidth_padding">
			<div class="contactform">

				<div id="listar_carrinho">
				</div>

				<form method="POST" action="../../../index" target="_blank">
					<input type="submit" class="form_submit botao_salvar_itens" value="Adicionar Itens" />
					<div align="center" style="display:none" id="img_loader"><img src="images/loader.gif"></div>

					<input type="hidden" name="id_mesa" id="id_itens">
					<input type="hidden" name="id_abertura_mesa" id="id_abertura_mesa">
				</form>

			</div>
		</div>
	</div>
	<div class="close_popup_button">
		<a id="btn-fechar-itensa" href="#" onclick="limparCampos(); location.reload();" class="close-popup" data-popup=".popup-itens"><img
				src="images/icons/black/menu_close.png" alt="" title="" /></a>
	</div>
</div>




<script type="text/javascript">
	var pag = "<?= $pag ?>"
</script>

<script>
	setTimeout(function() {
		listarCarrinho();
	}, 20000)
</script>

<script type="text/javascript">
	// $(document).ready(function() {
	// 	setInterval(listarCarrinho, 20000);

	// });

	function abrirMesa(id, status, nome, obs, id_abertura_mesa, ativo) {


		$('#titulo_inserir').text('Abrir Mesa');

		if (ativo != 'Sim') {
			alert('Mesa Indiponível!!')
			return;
		}

		if (status == 'Aberta') {
			itens(id, nome, obs, id_abertura_mesa);
		} else {
			$('#id_abertura').val(id);
			$('#nome_da_mesa').text(nome);
			$('#btn_mesa').click();
		}

		limparCampos();

	}

	function itens(id, nome, obs, id_abertura_mesa) {

		$('#nome_da_mesa_itens').text(nome);
		$('#obs_da_mesa_itens').text(obs);
		$('#id_itens').val(id);
		$('#id_abertura_mesa').val(id_abertura_mesa);

		$('#btn_itens').click();

		listarCarrinho();

	}

	function listarCarrinho() {
		var id = $('#id_abertura_mesa').val();
		$.ajax({

			url: 'paginas/' + pag + "/listar_carrinho.php",
			method: 'POST',
			data: {
				id
			},
			dataType: "html",

			success: function(result) {
				$("#listar_carrinho").html(result);
				$('#mensagem_itens').text('');
			}
		});
	}



	function editar(id, cliente, garcon, obs, pessoas) {
		$('#id').val(id);
		$('#nome').val(cliente);
		$('#garcon').val(garcon).change();
		$('#obs').val(obs);
		$('#pessoas').val(pessoas);


		$('#titulo_inserir').text('Editar Mesa');
		$('#btn_mesa').click();

	}



	function mostrar(nome, email, telefone, endereco, data_cad, cpf, numero, bairro, cidade, estado, cep, cnpj, valor, data_pgto, ativo, dominio, servidor, banco, usuario, senha, frequencia, pago, forma_pgto, vencimento) {

		$('#titulo_dados').text(nome);
		$('#email_dados').text(email);
		$('#telefone_dados').text(telefone);
		$('#endereco_dados').text(endereco + ' ' + numero + ' ' + bairro + ' ' + cidade + ' ' + estado);
		$('#cpf_dados').text(cpf);
		$('#cnpj_dados').text(cnpj);
		$('#valor_dados').text(valor);
		$('#dominio_dados').text(dominio);

		$('#btn_mostrar').click();
	}

	function limparCampos() {
		$('#id').val('');
		$('#nome').val('');
		$('#obs').val('');
		$('#pessoas').val(1);

	}


	function fecharMesa(id, nome, obs, id_abertura_mesa, couvert, comissao, subtotal, garcon, total_itens) {

		$('#id_fechamento').val(id);
		$('#id_ab_fechamento').val(id_abertura_mesa);
		$('#obs_fechamento').val(obs);
		$('#nome_da_mesa_fechamento').text(nome);
		$('#couvert_fechamento').val(couvert);
		$('#comissao_fechamento').val(comissao);
		$('#subtotal_fechamento').val(subtotal);
		$('#garcon_fechamento').val(garcon).change();
		$('#total_itens_fechamento').val(total_itens);

		$('#btn_fechamento').click();



	}





	function paginar(pag, busca) {
		$('#input_pagina').val(pag);
		$('#input_buscar').val(busca);
		$('#paginacao').click();
	}








	function totalizar() {
		valor = $('#valor-baixar').val();
		desconto = $('#valor-desconto').val();
		juros = $('#valor-juros').val();
		multa = $('#valor-multa').val();
		taxa = $('#valor-taxa').val();

		valor = valor.replace(",", ".");
		desconto = desconto.replace(",", ".");
		juros = juros.replace(",", ".");
		multa = multa.replace(",", ".");
		taxa = taxa.replace(",", ".");

		if (valor == "") {
			valor = 0;
		}

		if (desconto == "") {
			desconto = 0;
		}

		if (juros == "") {
			juros = 0;
		}

		if (multa == "") {
			multa = 0;
		}

		if (taxa == "") {
			taxa = 0;
		}

		subtotal = parseFloat(valor) + parseFloat(juros) + parseFloat(taxa) + parseFloat(multa) - parseFloat(desconto);


		console.log(subtotal)

		$('#subtotal').val(subtotal);

	}

	function calcular() {
		var comissao = $('#comissao_fechamento').val();
		var couvert = $('#couvert_fechamento').val();
		var total_itens = $('#total_itens_fechamento').val();

		if (comissao == "") {
			comissao = 0;
		}

		if (couvert == "") {
			couvert = 0;
		}

		if (total_itens == "") {
			total_itens = 0;
		}

		var subtotal = parseFloat(comissao) + parseFloat(couvert) + parseFloat(total_itens);
		$('#subtotal_fechamento').val(subtotal.toFixed(2));
	}




	$("#form_fechamento").submit(function() {

		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: '../../painel/paginas/' + pag + "/fechamento.php",
			type: 'POST',
			data: formData,

			success: function(mensagem) {
				$('#mensagem_fechamento').text('');
				$('#mensagem_fechamento').removeClass()
				if (mensagem.trim() == "Salvo com Sucesso") {

					$('#btn-fechar-fechamento').click();
					listar();

				} else {

					$('#mensagem_fechamento').addClass('text-danger')
					$('#mensagem_fechamento').text(mensagem)
				}


			},

			cache: false,
			contentType: false,
			processData: false,

		});

	});
</script>