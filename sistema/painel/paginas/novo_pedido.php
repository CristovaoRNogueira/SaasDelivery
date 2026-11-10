<?php
@session_start();
require_once("verificar.php");
require_once("../conexao.php");

$pag = 'novo_pedido';
if (@$novo_pedido == 'ocultar') {
	echo "<script>window.location='../index.php'</script>";
	exit();
}

$query2 = $pdo->query("SELECT * FROM usuarios where id = '$id_usuario' and empresa = '$id_empresa' ");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
if (@count($res2) > 0) {
	$nome_usuario = $res2[0]['nivel'];
}



?>

<div class="breadcrumb-header justify-content-between">
	<div class="left-content mt-2">


	</div>
</div>

<div style="position: relative; max-width: 260px;">
    <i class="fa fa-search" 
       style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#777; font-size:13px;">
    </i>

    <input 
        type="text" 
        id="buscar_cliente" 
        placeholder="Buscar Cliente"
        onkeyup="listar(this.value)"
        style="
            width:100%;
            height:38px;
            padding:8px 12px 8px 35px;
            border:1px solid #d0d0d0;
            border-radius:20px;
            outline:none;
            font-size:13px;
            color:#333;
            background:#fff;
            box-shadow:0 2px 6px rgba(0,0,0,0.08);
        ">
</div>

<div style="padding:5px" id="listar">

</div>



<!-- Modal modalAbertura-->
<div class="modal fade" id="modalAbertura" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h6 class="modal_title"><span id="titulo_inserir"></span> <span id="nome_da_mesa"></span></h6>
				<button id="btn-fechar" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<form id="form">
				<div class="modal-body">

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="exampleInputEmail1">Nome Cliente</label>
								<input type="text" class="form-control" id="nome" name="nome" placeholder="Nome do Cliente" required>
							</div>
						</div>
						<div class="col-md-6">

							<div class="form-group">
								<label for="exampleInputEmail1">Garçon</label>
								<select class="form-select sel5" id="garcon" name="garcon" style="width:100%;">

									<?php
									$query = $pdo->query("SELECT * FROM usuarios where (nivel = 'Garçon' or nivel = 'Atendente') and empresa = '$id_empresa' ORDER BY nome asc");
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
					</div>






					<div class="row">

						<div class="col-md-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Pessoas Mesa</label>
								<input type="number" class="form-control" id="pessoas" name="pessoas" placeholder="Número de Pessoas" required>
							</div>
						</div>

						<div class="col-md-9">
							<div class="form-group">
								<label for="exampleInputEmail1">Observações</label>
								<input type="text" class="form-control" id="obs" name="obs" placeholder="Observações">
							</div>
						</div>

					</div>

					<input type="hidden" name="id" id="id_abertura">
					<input type="hidden" name="id_ab" id="id">

					<br>
					<small>
						<div id="mensagem" align="center"></div>
					</small>
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Salvar <i class="fa fa-check ms-2"></i></button>
				</div>
			</form>


		</div>
	</div>
</div>






<!-- Modal Itens-->
<div class="modal fade" id="modalItens" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title">Mesa <span id="nome_da_mesa_itens"></span> <small><span
							id="obs_da_mesa_itens"></span></small></h4>
				<button onclick="listar()" id="btn-fechar-itens" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span class="text-white" aria-hidden="true">&times;</span></button>
			</div>

			<div class="modal-body">

				<div id="listar_carrinho">

				</div>


				<br>
				<small>
					<div id="mensagem_itens" align="center"></div>
				</small>
			</div>


			<div class="modal-footer">
				<form method="POST" action="pdv" target="_blank">
					<input type="hidden" name="id_mesa" id="id_itens">
					<input type="hidden" name="id_abertura_mesa" id="id_abertura_mesa">
					<button type="submit" class="btn btn-primary">Adicionar Itens<i class="fa fa-check ms-2"></i></button>

				</form>
			</div>

		</div>
	</div>
</div>








<!-- Modal Fechamento-->
<div class="modal fade" id="modalFechamento" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title">Fechar Mesa <span id="nome_da_mesa_fechamento"></span></h4>
				<button id="btn-fechar-fechamento" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<form id="form_fechamento">
				<div class="modal-body">

					<div class="row">
						<div class="col-md-6">

							<div class="form-group">
								<label for="exampleInputEmail1">Garçon</label>
								<select class="form-select sel6" id="garcon_fechamento" name="garcon" style="width:100%;">

									<?php
									$query = $pdo->query("SELECT * FROM usuarios where (nivel = 'Garçon' or nivel = 'Atendente') and empresa = '$id_empresa' ORDER BY nome asc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$total_reg = @count($res);
									if ($total_reg > 0) {
										for ($i = 0; $i < $total_reg; $i++) {

											echo '<option value="' . $res[$i]['id'] . '">' . $res[$i]['nome'] . '</option>';
										}
									} else {
										echo '<option value="0">Cadastre um Garçon</option>';
									}
									?>


								</select>
							</div>

						</div>


						<div class="col-md-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Total Itens</label>
								<input readonly="true" type="text" class="form-control" id="total_itens_fechamento" name="total_itens"
									placeholder="Total Itens">
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Comissão R$</label>
								<input onkeyup="calcular()" type="text" class="form-control" id="comissao_fechamento" name="comissao"
									placeholder="">
							</div>
						</div>

					</div>




					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label for="couvert_fechamento">Couvert</label>
								<input onkeyup="calcular()" type="text" class="form-control" id="couvert_fechamento" name="couvert"
									placeholder="">
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label for="total_valor_fechamento">AV</label>
								<input style="color:red" readonly="" type="text" class="form-control" id="total_valor_fechamento" name="total_valor_fechamento" placeholder="">
							</div>
						</div>


						<div class="col-md-2">
							<div class="form-group">
								<label for="">Total Mesa</label>
								<input readonly type="text" class="form-control" id="total_mesa_fechamento" name="total_mesa_fechamento" placeholder="">
							</div>
						</div>

						<div class="col-md-3">
            	<label class="block text-sm font-medium text-dark mb-1">Desconto <a id="desc_reais" class="desconto_link_ativo" href="#" onclick="tipoDesc('reais')">R$</a> / <a id="desc_p" class="desconto_link_inativo" href="#" onclick="tipoDesc('%')">%</a></label>
             
              <input type="number" class="form-control" id="desconto" name="desconto" placeholder="R$" onkeyup="totalizar()">
            </div>




						<div class="col-md-3">
							<div class="form-group">
								<label for="subtotal_fechamento">Subtotal</label>
								<input readonly type="text" class="form-control bg-light" id="subtotal_fechamento" name="subtotal" placeholder="" title="Calculado automaticamente pelo sistema">
							</div>
						</div>



						<div class="col-md-4">
							<div class="form-group">
								<label>Forma de Pagamento</label>
								<select class="form-select" id="forma_pgto_fechamento" name="forma_pgto" onchange="formaPgto()">
									<?php
									$query = $pdo->query("SELECT * FROM formas_pgto where empresa = '$id_empresa' ORDER BY id asc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$total_reg = @count($res);
									if ($total_reg > 0) {
										for ($i = 0; $i < $total_reg; $i++) {

											echo '<option value="' . $res[$i]['id'] . '" data-nome="'.$res[$i]['nome'].'">' . $res[$i]['nome'] . '</option>';
										}
									} else {
										echo '<option value="0">Cadastre uma Forma de Pagamento</option>';
									}
									?>


								</select>
							</div>
						</div>


						<div class="col-md-3" id="div_total_recebido">
							<div class="form-group">
								<label for="">Total Recebido R$</label>
								<input oninput="mascaraMoeda(this)" type="text" class="form-control" id="total_recebido" name="total_recebido" placeholder="Recebimento Dinheiro" onkeyup="calcularTroco()">
							</div>
						</div>

						<div class="col-md-2" id="div_troco">
							<div class="form-group">
								<label for="">Troco</label>
								<input oninput="mascaraMoeda(this)" type="text" class="form-control" id="troco" name="troco" placeholder="Troco">
							</div>
						</div>


					</div>





					<div class="row">

						<div class="col-md-12">
							<div class="form-group">
								<label for="exampleInputEmail1">Observações</label>
								<input type="text" class="form-control" id="obs_fechamento" name="obs" placeholder="Observações">
							</div>
						</div>



					</div>


					<input type="hidden" name="tipo_desconto" id="tipo_desconto" value="reais">
					<input type="hidden" name="id" id="id_fechamento">
					<input type="hidden" name="id_ab" id="id_ab_fechamento">

					<br>
					<small>
						<div id="mensagem_fechamento" align="center"></div>
					</small>
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Salvar<i class="fa fa-check ms-2"></i></button>
				</div>
			</form>


		</div>
	</div>
</div>







<!-- Modal VALORES-->
<div class="modal fade" id="modalValores" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title">Mesa <span id="nome_valor"></span></h4>
				<button id="btn-fechar-valor" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button" onclick="listar()"><span class="text-white" aria-hidden="true">&times;</span></button>
			</div>

			<form id="form-valor" method="post">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label>Nome</label>
								<input class="form-control" type="text" name="nome" id="nome_ad" placeholder="Nome" required>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>Valor</label>
								<input class="form-control" type="text" name="valor" id="valor_ad" placeholder="R$ 0,00" required>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Forma de Pagamento</label>
								<select class="form-select" id="forma_pgto_valores" name="forma_pgto">
									<?php
									$query = $pdo->query("SELECT * FROM formas_pgto where empresa = '$id_empresa' ORDER BY id asc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$total_reg = @count($res);
									if ($total_reg > 0) {
										for ($i = 0; $i < $total_reg; $i++) {

											echo '<option value="' . $res[$i]['id'] . '">' . $res[$i]['nome'] . '</option>';
										}
									} else {
										echo '<option value="0">Cadastre uma Forma de Pagamento</option>';
									}
									?>


								</select>
							</div>

						</div>
						<div class="col-md-2" style="margin-top:25px">
							<button type="submit" class="btn btn-primary">Inserir<i class="fa fa-check ms-2"></i></button>
						</div>
					</div>


					<hr>

					<small>
						<div id="listar-valor"></div>
					</small>

					<br>
					<small>
						<div align="center" id="mensagem-valor"></div>
					</small>

					<input type="hidden" class="form-control" name="id" id="id_valor">

				</div>
			</form>

		</div>
	</div>
</div>



<!-- Modal EXCLUIR-->
<div class="modal fade" id="modalExcluir" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true" data-bs-backdrop="static">
	<div class="modal-dialog" role="document" style="box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title">Excluir Registro - <span id="nome_modal"> </span></h4>
				<button id="btn-fechar-excluir" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span class="text-white" aria-hidden="true">&times;</span></button>
			</div>

			<div class="modal-body">

				<div class="row">
					<div class="col-md-7">
						<label for="exampleInputEmail1">Gerente</label>
						<select class="form-select sel40" id="gerente" name="gerente" style="width:100%;">
							<?php
							$query = $pdo->query("SELECT * FROM usuarios where nivel = 'Gerente' or nivel = 'Administrador' and empresa = '$id_empresa'");
							$res = $query->fetchAll(PDO::FETCH_ASSOC);
							$total_reg = @count($res);

							echo '<option value="">Selecione um Gerente</option>';

							if ($total_reg > 0) {
								for ($i = 0; $i < $total_reg; $i++) {
									foreach ($res[$i] as $key => $value) {
									}
									echo '<option value="' . $res[$i]['id'] . '">' . $res[$i]['nome'] . '</option>';
								}
							}
							?>


						</select>
					</div>


					<div class="col-md-3" style="padding:0">
						<div class="form-group">
							<label for="exampleInputEmail1">Senha</label>
							<input type="password" class="form-control" id="senha" name="senha" placeholder="Senha">
						</div>
					</div>

					<div class="col-md-2" style="margin-top: 23px">
						<button class="btn btn-primary" onclick="excluirCarrinho()">Ok</button>
					</div>
				</div>


				<br>
				<small>
					<div align="center" id="mensagem-excluir-modal"></div>
				</small>

				<input type="hidden" class="form-control" name="id_car_excluir" id="id_car_excluir">

			</div>

		</div>
	</div>
</div>






<!-- Modal Troca-->
<div class="modal fade" id="modalTroca" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true" data-bs-backdrop="static">
	<div class="modal-dialog" role="document" style="box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title">Trocar Mesa - <span id="nome_troca"> </span></h4>
				<button id="btn-fechar-troca" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span class="text-white" aria-hidden="true">&times;</span></button>
			</div>

			<form id="form_troca">
			<div class="modal-body">
				
				<div class="row">
					<div class="col-md-6">
						<label for="exampleInputEmail1">Mesa</label>
						<select class="form-select" id="mesa_troca" name="mesa_troca" style="width:100%;">
							<?php
							$query = $pdo->query("SELECT * FROM mesas where ativo = 'Sim' and status = 'Fechada' and empresa = '$id_empresa'");
							$res = $query->fetchAll(PDO::FETCH_ASSOC);
							$total_reg = @count($res);							

							if ($total_reg > 0) {
								for ($i = 0; $i < $total_reg; $i++) {
									foreach ($res[$i] as $key => $value) {
									}
									echo '<option value="' . $res[$i]['id'] . '">' . $res[$i]['nome'] . '</option>';
								}
							}
							?>


						</select>
					</div>
					

					<div class="col-md-4" style="margin-top: 23px">
						<button class="btn btn-primary" type="submit">Trocar</button>
					</div>
				</div>


				<br>
				<small>
					<div align="center" id="mensagem_troca"></div>
				</small>

				<input type="hidden" class="form-control" name="id_abertura_troca" id="id_abertura_troca">
				<input type="hidden" class="form-control" name="id_mesa_troca" id="id_mesa_troca">
				<input type="hidden" class="form-control" name="nome_mesa" id="nome_mesa">

			</div>
		</form>

		</div>
	</div>
</div>





<script type="text/javascript">
	window.addEventListener("focus", function() {    
    listarCarrinho();
    listarValores();
});

	document.addEventListener("visibilitychange", function() {
    if (!document.hidden) {
        listarCarrinho();
        listarValores();
    }
});
</script>


<script type="text/javascript">
	var pag = "<?= $pag ?>"
</script>
<script src="js/ajax.js"></script>


<script type="text/javascript">
	$(document).ready(function() {
		var buscar_cliente = $('#buscar_cliente');
		
		formaPgto()

		setInterval(listar(buscar_cliente), 20000);
		setInterval(listarCarrinho, 20000);

		$('.sel5').select2({
			dropdownParent: $('#modalAbertura')
		});

		$('.sel6').select2({
			dropdownParent: $('#modalFechamento')
		});

		$('.sel40').select2({
			dropdownParent: $('#modalExcluir')
		});
	});



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

	function calcular() {
		var comissao = $('#comissao_fechamento').val();
		var couvert = $('#couvert_fechamento').val();
		var total_itens = $('#total_itens_fechamento').val();
		var total_valor_fechamento = $('#total_valor_fechamento').val();

		if (comissao == "") {
			comissao = 0;
		}

		if (couvert == "") {
			couvert = 0;
		}

		if (total_itens == "") {
			total_itens = 0;
		}

		if (total_valor_fechamento == "") {
			total_valor_fechamento = 0;
		}

		var subtotal = parseFloat(comissao) + parseFloat(couvert) + parseFloat(total_itens) - total_valor_fechamento;
		$('#subtotal_fechamento').val(subtotal.toFixed(2));
	}





	$("#form_fechamento").submit(function() {

		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: 'paginas/' + pag + "/fechamento.php",
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





	$("#form-valor").submit(function() {

		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: 'paginas/' + pag + "/salvar_valor.php",
			type: 'POST',
			data: formData,

			success: function(mensagem) {
				$('#mensagem-valor').text('');
				$('#mensagem-valor').removeClass()
				if (mensagem.trim() == "Salvo com Sucesso") {

					$('#nome_ad').val('')
					$('#valor_ad').val('')

					listarValores();

				} else {

					$('#mensagem-valor').addClass('text-danger')
					$('#mensagem-valor').text(mensagem)
				}


			},

			cache: false,
			contentType: false,
			processData: false,

		});

	});



	function listarValores() {
		var id = $('#id_valor').val();
		$.ajax({
			url: 'paginas/' + pag + "/listar_valores.php",
			method: 'POST',
			data: {
				id
			},
			dataType: "text",

			success: function(result) {
				$("#listar-valor").html(result);
			}
		});

	}

	function abrirFechamentoPeloAdiantamento(id, nome, obs, idAbertura, couvert, comissao, subtotal, garcon, totalItens, totalValor) {
		$('#modalValores').modal('hide');
		fecharMesa(id, nome, obs, idAbertura, couvert, comissao, subtotal, garcon, totalItens, totalValor);
	}





	$("#form_troca").submit(function() {

		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: 'paginas/' + pag + "/troca_mesa.php",
			type: 'POST',
			data: formData,

			success: function(mensagem) {
				$('#mensagem_troca').text('');
				$('#mensagem_troca').removeClass()
				if (mensagem.trim() == "Salvo com Sucesso") {

					$('#btn-fechar-troca').click();
					listar();

				} else {

					$('#mensagem_troca').addClass('text-danger')
					$('#mensagem_troca').text(mensagem)
				}


			},

			cache: false,
			contentType: false,
			processData: false,

		});

	});




	function totalizar(){
				var desconto = $("#desconto").val();				
				var tipo_desconto = $("#tipo_desconto").val();
				var total_mesa = $("#total_mesa_fechamento").val();	
				$.ajax({
					url: 'paginas/' + pag + "/totalizar.php",
					method: 'POST',
					data: {desconto, tipo_desconto, total_mesa},
					dataType: "html",

					success:function(result){
						$("#subtotal_fechamento").val(result);  
						calcularTroco();          
					}
				});	


			}



			function tipoDesc(p){
	$('#desc_reais').removeClass()
	$('#desc_p').removeClass()

	if(p == '%'){
		$('#desconto').attr('placeholder', '%');
		$('#desc_reais').addClass('desconto_link_inativo')
		$('#desc_p').addClass('desconto_link_ativo')
	}else{
		$('#desconto').attr('placeholder', 'R$');
		$('#desc_reais').addClass('desconto_link_ativo')
		$('#desc_p').addClass('desconto_link_inativo')
	}

	 $("#tipo_desconto").val(p);
	 totalizar();
}


function formaPgto(){
	 var forma_pgto = $("#forma_pgto_fechamento option:selected").data("nome");	
	if(forma_pgto == 'Dinheiro'){
		$("#div_troco").show();          
		$("#div_total_recebido").show();          
	}else{
		$("#div_troco").hide();          
		$("#div_total_recebido").hide(); 

		$("#total_recebido").val('');
		$("#troco").val('');

	}
}


function calcularTroco(){
	var total_recebido = $("#total_recebido").val();		
	var subtotal = $("#subtotal_fechamento").val();				
					
				$.ajax({
					url: 'paginas/' + pag + "/troco.php",
					method: 'POST',
					data: {total_recebido, subtotal},
					dataType: "html",

					success:function(result){						
						$("#troco").val(result);            
					}
				});	
}
</script>
