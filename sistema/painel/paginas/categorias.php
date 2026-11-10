<?php
//verificar se ele tem a permissão de estar nessa página
if (@$categorias == 'ocultar') {
	echo "<script>window.location='../index.php'</script>";
	exit();
}
$pag = 'categorias';
?>
<div class="breadcrumb-header justify-content-between">
	<div class="left-content mt-2">
		<a class="btn ripple btn-primary text-white" onclick="inserir()" type="button"><i class="fe fe-plus me-1"></i> Adicionar <?php echo ucfirst($pag); ?></a>


		<!-- EXCLUIR MULTIPLO -->
		<a class="btn btn-danger" href="#" onclick="deletarSel()" title="Excluir" id="btn-deletar" style="display:none"><i class="fe fe-trash-2"></i> Deletar</a>


	</div>
</div>



<div class="bs-example widget-shadow" style="padding:15px" id="listar">

</div>


<input type="hidden" id="ids">


<!-- MODAL CATEGORIAS-->
<div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title"><span id="titulo_inserir"></span></h4>
				<button id="btn-fechar" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<form id="form">
				<div class="modal-body">

					<div class="row">
						<div class="col-md-6 needs-validation was-validated">
							<div class="form-group">
								<label>Categoria</label>
								<input type="text" class="form-control" id="nome" name="nome" placeholder="Categoria" required>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label>Mais Sabores</label>
								<select class="form-select" id="mais_sabores" name="mais_sabores">
									<option value="Não">Não</option>
									<option value="Sim">Sim</option>
								</select>
							</div>
						</div>


						<div class="col-md-3">
							<div class="form-group">
								<label>Delivery</label>
								<select class="form-select" id="delivery" name="delivery">
									<option value="Sim">Sim</option>
									<option value="Não">Não</option>

								</select>
							</div>
						</div>


					</div>


					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label>Descrição</label>
								<input maxlength="255" type="text" class="form-control" id="descricao" name="descricao" placeholder="Pequena Descrição">
							</div>
						</div>



					</div>



					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								<label>Foto</label>
								<input class="form-control" type="file" name="foto" onChange="carregarImg();" id="foto">
							</div>
						</div>
						<div class="col-md-4">
							<div id="divImg">
								<img src="images/<?php echo $pag ?>/sem-foto.jpg" width="80px" id="target">
							</div>
						</div>

					</div>



					<input type="hidden" name="id" id="id">

					<br>
					<small>
						<div id="mensagem" align="center"></div>
					</small>
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary" id="btn_salvar">Salvar<i class="fa fa-check ms-2"></i></button>
					<button class="btn btn-primary" type="button" id="btn_carregando" style="display: none">
						<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Carregando...
					</button>
				</div>
			</form>


		</div>
	</div>
</div>





<!-- Modal Variações-->
<div class="modal fade" id="modalVariacoes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_nome_var"></span> - Variações</h4>
				<button id="btn-fechar-var" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span class="text-white" aria-hidden="true">&times;</span></button>
			</div>

			<div class="modal-body">
				<form id="form-var">


					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label>Sigla</label>
								<input maxlength="5" type="text" class="form-control" id="sigla" name="sigla" placeholder="P / M / G">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>Nome</label>
								<input maxlength="35" type="text" class="form-control" id="nome_var" name="nome" placeholder="Pequena / Média /Grande ..." required>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label>Máx Sabores</label>
								<input type="number" class="form-control" id="sabores" name="sabores" value="0" placeholder="1">
							</div>
						</div>


					</div>

					<div class="row">

						<div class="col-md-9">
							<div class="form-group">
								<label>Descrição</label>
								<input maxlength="50" type="text" class="form-control" id="descricao_var" name="descricao" placeholder="8 Fatias">
							</div>
						</div>

						<div class="col-md-3" style="margin-top: 25px">
							<button id="btn_var" type="submit" class="btn btn-primary">Salvar<i class="fa fa-check ms-2"></i></button>

						</div>
					</div>

					<input type="hidden" id="id_var" name="id">
					<input type="hidden" id="id_var_editar" name="id_var_editar">

				</form>

				<br>
				<small>
					<div id="mensagem-var" align="center"></div>
				</small>


				<hr>
				<div id="listar-var"></div>
			</div>


		</div>
	</div>
</div>






<!-- Modal Adicionais-->
<div class="modal fade" id="modalAdicionais" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_nome_adc"></span> - Adicionais</h4>
				<button id="btn-fechar-adc" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span class="text-white" aria-hidden="true">&times;</span></button>
			</div>

			<div class="modal-body">
				<form id="form-adc">

					<div class="row">

						<div class="col-md-5">
							<div class="form-group">
								<input maxlength="50" type="text" class="form-control" id="nome_adc" name="nome" placeholder="Nome" required>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<input maxlength="50" type="text" class="form-control" id="valor_adc" name="valor" placeholder="Valor" required>
							</div>
						</div>

						<div class="col-md-3">
							<button id="btn_editar_adc" type="submit" class="btn btn-primary">Salvar</button>

						</div>
					</div>

					<input type="hidden" id="id_adc" name="id">
					<input type="hidden" id="id_adc_editar" name="id_adc_editar">

				</form>

				<br>
				<small>
					<div id="mensagem-adc" align="center"></div>
				</small>



				<div id="listar-adc"></div>
			</div>


		</div>
	</div>
</div>








<!-- Modal Bordas-->
<div class="modal fade" id="modalBordas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_nome_bordas"></span> - Bordas</h4>
				<button id="btn-fechar-bordas" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span class="text-white" aria-hidden="true">&times;</span></button>
			</div>

			<div class="modal-body">
				<form id="form-bordas">



					<div class="row">

						<div class="col-md-5">
							<div class="form-group">
								<input maxlength="50" type="text" class="form-control" id="nome_bordas" name="nome" placeholder="Nome" required>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<input maxlength="50" type="text" class="form-control" id="valor_bordas" name="valor" placeholder="Valor" required>
							</div>
						</div>

						<div class="col-md-3">
							<button id="btn_borda" type="submit" class="btn btn-primary">Salvar</button>

						</div>
					</div>

					<input type="hidden" id="id_bordas" name="id">
					<input type="hidden" id="id_borda_editar" name="id_borda_editar">

				</form>

				<br>
				<small>
					<div id="mensagem-bordas" align="center"></div>
				</small>



				<div id="listar-bordas"></div>
			</div>


		</div>
	</div>
</div>



<script type="text/javascript">
	var pag = "<?= $pag ?>"
</script>
<script src="js/ajax.js"></script>



<script type="text/javascript">
	function carregarImg() {
		var target = document.getElementById('target');
		var file = document.querySelector("#foto").files[0];

		var reader = new FileReader();

		reader.onloadend = function() {
			target.src = reader.result;
		};

		if (file) {
			reader.readAsDataURL(file);

		} else {
			target.src = "";
		}
	}



	function excluirVar(id) {
		var id_var = $('#id_var').val()
		$.ajax({
			url: 'paginas/' + pag + "/excluir-variacoes.php",
			method: 'POST',
			data: {
				id
			},
			dataType: "text",

			success: function(mensagem) {
				if (mensagem.trim() == "Excluído com Sucesso") {
					listarVariacoes(id_var);
				} else {
					$('#mensagem-excluir-var').addClass('text-danger')
					$('#mensagem-excluir-var').text(mensagem)
				}

			},

		});
	}


	function listarVariacoes(id) {
		$.ajax({
			url: 'paginas/' + pag + "/listar-variacoes.php",
			method: 'POST',
			data: {
				id
			},
			dataType: "html",

			success: function(result) {
				$("#listar-var").html(result);
				$('#mensagem-excluir-var').text('');
			}
		});
	}


	$("#form-var").submit(function() {

		var id_var = $('#id_var').val()

		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: 'paginas/' + pag + "/inserir-variacoes.php",
			type: 'POST',
			data: formData,

			success: function(mensagem) {
				$('#mensagem-var').text('');
				$('#mensagem-var').removeClass()
				if (mensagem.trim() == "Salvo com Sucesso") {

					//$('#btn-fechar-var').click();
					listarVariacoes(id_var);
					limparCamposVar();

				} else {

					$('#mensagem-var').addClass('text-danger')
					$('#mensagem-var').text(mensagem)
				}


			},

			cache: false,
			contentType: false,
			processData: false,

		});

	});
</script>







<script type="text/javascript">
	$("#form-adc").submit(function() {

		var id_adc = $('#id_adc').val()

		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: 'paginas/' + pag + "/inserir-adicionais.php",
			type: 'POST',
			data: formData,

			success: function(mensagem) {
				$('#mensagem-adc').text('');
				$('#mensagem-adc').removeClass()
				if (mensagem.trim() == "Salvo com Sucesso") {

					//$('#btn-fechar-var').click();
					listarAdicionais(id_adc);
					limparCamposAdc();

				} else {

					$('#mensagem-adc').addClass('text-danger')
					$('#mensagem-adc').text(mensagem)
				}


			},

			cache: false,
			contentType: false,
			processData: false,

		});

	});
</script>


<script type="text/javascript">
	function limparCamposAdc() {

		$('#nome_adc').val('');
		$('#valor_adc').val('');

	}

	function limparCamposVar() {

		$('#nome_var').val('');
		$('#sigla').val('');
		$('#descricao_var').val('');
		$('#sabores').val('');
		$('#id_var_editar').val('');
		$('#btn_var').text('Salvar');

	}





	function listarAdicionais(id) {
		$.ajax({
			url: 'paginas/' + pag + "/listar-adicionais.php",
			method: 'POST',
			data: {
				id
			},
			dataType: "html",

			success: function(result) {
				$("#listar-adc").html(result);
				$('#mensagem-excluir-adc').text('');
			}
		});
	}





	function excluirAdc(id) {
		var id_adc = $('#id_adc').val()
		$.ajax({
			url: 'paginas/' + pag + "/excluir-adicionais.php",
			method: 'POST',
			data: {
				id
			},
			dataType: "text",

			success: function(mensagem) {
				if (mensagem.trim() == "Excluído com Sucesso") {
					listarAdicionais(id_adc);
				} else {
					$('#mensagem-excluir-adc').addClass('text-danger')
					$('#mensagem-excluir-adc').text(mensagem)
				}

			},

		});
	}




	function ativarAdc(id, acao) {
		var id_adc = $('#id_adc').val()
		$.ajax({
			url: 'paginas/' + pag + "/mudar-status-adc.php",
			method: 'POST',
			data: {
				id,
				acao
			},
			dataType: "text",

			success: function(mensagem) {
				if (mensagem.trim() == "Alterado com Sucesso") {
					listarAdicionais(id_adc);
				} else {
					$('#mensagem-excluir-adc').addClass('text-danger')
					$('#mensagem-excluir-adc').text(mensagem)
				}

			},

		});
	}
</script>







<script type="text/javascript">
	$("#form-bordas").submit(function() {

		var id_bordas = $('#id_bordas').val()

		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: 'paginas/' + pag + "/inserir-bordas.php",
			type: 'POST',
			data: formData,

			success: function(mensagem) {
				$('#mensagem-bordas').text('');
				$('#mensagem-bordas').removeClass()
				if (mensagem.trim() == "Salvo com Sucesso") {

					//$('#btn-fechar-var').click();
					listarBordas(id_bordas);
					limparCamposBordas();

				} else {

					$('#mensagem-bordas').addClass('text-danger')
					$('#mensagem-bordas').text(mensagem)
				}


			},

			cache: false,
			contentType: false,
			processData: false,

		});

	});
</script>


<script type="text/javascript">
	function limparCamposBordas() {

		$('#nome_bordas').val('');
		$('#valor_bordas').val('');


		$('#id_borda_editar').val('');
		$('#btn_borda').text('Salvar');


	}


	function listarBordas(id) {
		$.ajax({
			url: 'paginas/' + pag + "/listar-bordas.php",
			method: 'POST',
			data: {
				id
			},
			dataType: "html",

			success: function(result) {
				$("#listar-bordas").html(result);
				$('#mensagem-excluir-bordas').text('');
			}
		});
	}





	function excluirBordas(id) {
		var id_bordas = $('#id_bordas').val()
		$.ajax({
			url: 'paginas/' + pag + "/excluir-bordas.php",
			method: 'POST',
			data: {
				id
			},
			dataType: "text",

			success: function(mensagem) {
				if (mensagem.trim() == "Excluído com Sucesso") {
					listarBordas(id_bordas);
				} else {
					$('#mensagem-excluir-bordas').addClass('text-danger')
					$('#mensagem-excluir-bordas').text(mensagem)
				}

			},

		});
	}




	function ativarBordas(id, acao) {
		var id_bordas = $('#id_bordas').val()
		$.ajax({
			url: 'paginas/' + pag + "/mudar-status-bordas.php",
			method: 'POST',
			data: {
				id,
				acao
			},
			dataType: "text",

			success: function(mensagem) {
				if (mensagem.trim() == "Alterado com Sucesso") {
					listarBordas(id_bordas);
				} else {
					$('#mensagem-excluir-bordas').addClass('text-danger')
					$('#mensagem-excluir-bordas').text(mensagem)
				}

			},

		});
	}



	function editarAdc(id, nome, valor) {
		$('#nome_adc').val(nome);
		$('#valor_adc').val(valor);
		$('#id_adc_editar').val(id);

		$('#btn_editar_adc').text('Editar');

	}
</script>