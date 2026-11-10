<?php
@session_start();

require_once("../conexao.php");

//verificar se ele tem a permissão de estar nessa página
if (@$compras == 'ocultar') {
	echo "<script>window.location='../index.php'</script>";
	exit();
}



$query2 = $pdo->query("SELECT * FROM usuarios where id = '$id_usuario'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
if (@count($res2) > 0) {
	$nome_usuario = $res2[0]['nivel'];
}



$pag = 'compras';


$data_hoje = date('Y-m-d');
$data_ontem = date('Y-m-d', strtotime("-1 days", strtotime($data_hoje)));

$mes_atual = Date('m');
$ano_atual = Date('Y');
$data_inicio_mes = $ano_atual . "-" . $mes_atual . "-01";

if ($mes_atual == '4' || $mes_atual == '6' || $mes_atual == '9' || $mes_atual == '11') {
	$dia_final_mes = '30';
} else if ($mes_atual == '2') {
	$dia_final_mes = '28';
} else {
	$dia_final_mes = '31';
}

$data_final_mes = $ano_atual . "-" . $mes_atual . "-" . $dia_final_mes;


?>

<div class="breadcrumb-header justify-content-between">
	<div class="left-content">
		<a class="btn ripple btn-primary text-white" onclick="inserir()" type="button"><i class="fe fe-plus me-1"></i>
			Adicionar <?php echo ucfirst($pag); ?></a>


	</div>

	<div class="col-md-5" style="margin-bottom:5px;">
		<div style="float:left; margin-right:10px"><span><small><i title="Data de Vencimento Inicial"
						class="fa fa-calendar-o"></i></small></span></div>
		<div style="float:left; margin-right:20px">
			<input type="date" class="form-control " name="data-inicial" id="data-inicial-caixa"
				value="<?php echo $data_inicio_mes ?>" required>
		</div>

		<div style="float:left; margin-right:10px"><span><small><i title="Data de Vencimento Final" class="fa fa-calendar-o"></i></small></span></div>
		<div style="float:left; margin-right:10px">
			<input type="date" class="form-control " name="data-final" id="data-final-caixa"
				value="<?php echo $data_final_mes ?>" required>
		</div>
	</div>





	<div class="group-btn">
		<div class="row">
			<div class="btn-group" role="group" aria-label="Basic radio toggle button group">
				<input type="radio" class="btn-check" name="btnradio" id="btnradio21" checked onclick="valorData('<?php echo $data_ontem ?>', '<?php echo $data_ontem ?>')">
				<label class="btn btn-outline-primary" for="btnradio21">Ontem</label>

				<input type="radio" class="btn-check" name="btnradio" id="btnradio22" checked onclick="valorData('<?php echo $data_hoje ?>', '<?php echo $data_hoje ?>')">
				<label class="btn btn-outline-primary" for="btnradio22">Hoje</label>

				<input type="radio" class="btn-check" name="btnradio" id="btnradio33" onclick="valorData('<?php echo $data_inicio_mes ?>', '<?php echo $data_final_mes ?>')">
				<label class="btn btn-outline-primary" for="btnradio33">Mês</label>
			</div>
		</div>
	</div>




	<div class="group-btn">
		<div class="row">
			<div class="btn-group" role="group" aria-label="Basic radio toggle button group">


				<input type="radio" class="btn-check" name="btnradio" id="btnradio24" checked onclick="buscarContas('')">
				<label class="btn btn-outline-primary" for="btnradio24" title="Todas as Contas">Todas</label>

				<input type="radio" class="btn-check" name="btnradio" id="btnradio25" onclick="buscarContas('Não')">
				<label class="btn btn-outline-primary" for="btnradio25" title="Contas Pendentes">Pendentes</label>

				<input type="radio" class="btn-check" name="btnradio" id="btnradio36" onclick="buscarContas('Sim')">
				<label class="btn btn-outline-primary" for="btnradio36" title="Contas Pagas">Pagas</label>
			</div>
		</div>
	</div>

	<input type="hidden" id="buscar-contas">

</div>


<div class="bs-example widget-shadow" style="padding:15px">


	<div id="listar">

	</div>

</div>






<!-- Modal Inserir-->
<div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title"><span id="titulo_inserir"></span></h4>
				<button id="btn-fechar" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<form id="form">
				<div class="modal-body">

					<div class="row">
						<div class="col-md-5">
							<div class="form-group">
								<label for="exampleInputEmail1">Produto</label>
								<select class="form-select sel3" id="produto" name="produto" style="width:100%;">

									<?php
									$query = $pdo->query("SELECT * FROM produtos where tem_estoque = 'Sim' and empresa = '$id_empresa' ORDER BY nome asc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$total_reg = @count($res);



									if ($total_reg > 0) {
										for ($i = 0; $i < $total_reg; $i++) {
											foreach ($res[$i] as $key => $value) {
											}
											echo '<option value="' . $res[$i]['id'] . '">' . $res[$i]['nome'] . '</option>';
										}
									} else {
										echo '<option value="0">Cadastre um Produto</option>';
									}
									?>


								</select>
							</div>
						</div>

						<div class="col-md-5">

							<div class="form-group">
								<label for="exampleInputEmail1">Fornecedor</label>
								<select class="form-select sel3" id="pessoa" name="pessoa" style="width:100%;">

									<?php
									$query = $pdo->query("SELECT * FROM fornecedores where empresa = '$id_empresa' ORDER BY nome asc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$total_reg = @count($res);

									echo '<option value="0">Selecione um Fornecedor</option>';

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
						</div>

						<div class="col-md-2">

							<div class="form-group">
								<label for="exampleInputEmail1">Quantidade</label>
								<input type="number" class="form-control" id="quantidade" name="quantidade" placeholder="Quantidade"
									required>
							</div>
						</div>

					</div>




					<div class="row">

						<div class="col-md-4">

							<div class="form-group">
								<label for="exampleInputEmail1">Valor Total Compra</label>
								<input type="text" class="form-control" id="valor" name="valor" placeholder="Valor" required>
							</div>
						</div>


						<div class="col-md-4">

							<div class="form-group">
								<label for="exampleInputEmail1">Vencimento</label>
								<input type="date" class="form-control" id="data_venc" name="data_venc"
									value="<?php echo $data_hoje ?>">
							</div>
						</div>


						<div class="col-md-4">

							<div class="form-group">
								<label for="exampleInputEmail1">Pago Em</label>
								<input type="date" class="form-control" id="data_pgto" name="data_pgto">
							</div>
						</div>



					</div>





					<div class="row">

						<div class="col-md-4 col">
							<div class="form-group">
								<label>Forma de Pagamento</label>
								<select class="form-select" name="saida" id="saida">
									<?php
									$query = $pdo->query("SELECT * from formas_pgto where empresa = '$id_empresa' order by id desc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$linhas = @count($res);
									if ($linhas > 0) {
										for ($i = 0; $i < $linhas; $i++) {
											echo '<option value="' . $res[$i]['id'] . '">' . $res[$i]['nome'] . '</option>';
										}
									} else {
										echo '<option value="0">Cadastre uma Forma de Pagamento</option>';
									}
									?>

								</select>
							</div>
						</div>

						<div class="col-md-8 mb-3">
							<label>Observações</label>
							<input maxlength="255" type="text" class="form-control" id="obs" name="obs" placeholder="Observações">
						</div>

					</div>



					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								<label>Arquivo (Nota Fiscal)</label>
								<input class="form-control" type="file" name="foto" onChange="carregarImg();" id="foto">
							</div>
						</div>
						<div class="col-md-4">
							<div id="divImg">
								<img src="images/contas/sem-foto.jpg" width="80px" id="target">
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
					<button type="submit" class="btn btn-primary">Salvar<i class="fa fa-check ms-2"></i></button>
				</div>
			</form>


		</div>
	</div>
</div>




<!-- Modal Dados -->
<div class="modal fade" id="modalDados" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="exampleModalLabel"><span id="nome_dados"></span><span id="titulo_dados"></span></h4>
				<button id="btn-fechar-dados" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>

			<div class="modal-body">

				<div class="row">


					<div class="col-md-9">
						<div class="tile">
							<div class="table-responsive">
								<table id="" class="text-left table table-bordered">


									<tr>
										<td width="30%" class="bg-primary text-white">Valor</td>
										<td><span id="valor_dados"></span></td>
									</tr>

									<tr>
										<td class="bg-primary text-white">Data Lançamento</td>
										<td><span id="data_lanc_dados"></span></td>
									</tr>

									<tr>
										<td class="bg-primary text-white">Data Vencimento</td>
										<td><span id="data_venc_dados"></span></td>
									</tr>


									<tr>
										<td class="bg-primary text-white">Fornecedor</td>
										<td><span id="pessoa_dados"></span></td>
									</tr>


									<tr>
										<td class="bg-primary text-white w_150">FORMA PGTO</td>
										<td><span id="forma_pgto_dados"></span></td>
									</tr>

									<tr>
										<td class="bg-primary text-white w_150">Pago</td>
										<td><span id="pago_dados"></span></td>
									</tr>

									<tr>
										<td class="bg-primary text-white w_150">OBS</td>
										<td><span id="obs_dados"></span></td>
									</tr>


									<tr>
										<td class="bg-primary text-white w_150">Telefone</td>
										<td><span id="telefone_dados"></span></td>
									</tr>


									<tr>
										<td class="bg-primary text-white">Data PGTO</td>
										<td><span id="data_pgto_dados"></span></td>
									</tr>

									<tr>
										<td class="bg-primary text-white">Usuário Lanc</td>
										<td><span id="usuario_lanc_dados"></span></td>
									</tr>

									<tr>
										<td class="bg-primary text-white">Usuário Baixa</td>
										<td><span id="usuario_baixa_dados"></span></td>
									</tr>





								</table>
							</div>
						</div>
					</div>

					<div class="col-md-3">
						<div class="tile">
							<div class="table-responsive">
								<table id="" class="text-left table table-bordered">


									<tr>
										<td align="center"><a id="link_mostrar" target="_blank" title="Clique para abrir o arquivo!">
												<img width="250px" id="target_mostrar">
											</a></td>
									</tr>

								</table>
							</div>
						</div>
					</div>



				</div>

			</div>

		</div>
	</div>
</div>







<script type="text/javascript">
	var pag = "<?= $pag ?>"
</script>
<script src="js/ajax.js"></script>


<script type="text/javascript">
	$(document).ready(function() {
		$('.sel2').select2({
			dropdownParent: $('#modalForm')
		});
	});
</script>


<script type="text/javascript">
	function carregarImg() {
		var target = document.getElementById('target');
		var file = document.querySelector("#foto").files[0];


		var arquivo = file['name'];
		resultado = arquivo.split(".", 2);

		if (resultado[1] === 'pdf') {
			$('#target').attr('src', "images/pdf.png");
			return;
		}

		if (resultado[1] === 'rar' || resultado[1] === 'zip') {
			$('#target').attr('src', "images/rar.png");
			return;
		}



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
</script>



<script type="text/javascript">
	function valorData(dataInicio, dataFinal) {
		$('#data-inicial-caixa').val(dataInicio);
		$('#data-final-caixa').val(dataFinal);
		listar();
	}
</script>



<script type="text/javascript">
	$('#data-inicial-caixa').change(function() {
		//$('#tipo-busca').val('');
		listar();
	});

	$('#data-final-caixa').change(function() {
		//$('#tipo-busca').val('');
		listar();
	});
</script>





<script type="text/javascript">
	function listar() {

		var dataInicial = $('#data-inicial-caixa').val();
		var dataFinal = $('#data-final-caixa').val();
		var status = $('#buscar-contas').val();

		$.ajax({
			url: 'paginas/' + pag + "/listar.php",
			method: 'POST',
			data: {
				dataInicial,
				dataFinal,
				status
			},
			dataType: "html",

			success: function(result) {
				$("#listar").html(result);
				$('#mensagem-excluir').text('');
			}
		});
	}
</script>



<script type="text/javascript">
	function buscarContas(status) {
		$('#buscar-contas').val(status);
		listar();
	}
</script>




<script type="text/javascript">
	function baixar(id) {
		$.ajax({
			url: 'paginas/' + pag + "/baixar.php",
			method: 'POST',
			data: {
				id
			},
			dataType: "text",

			success: function(mensagem) {
				if (mensagem.trim() == "Baixado com Sucesso") {
					listar();
				} else {
					$('#mensagem-excluir').addClass('text-danger')
					$('#mensagem-excluir').text(mensagem)
				}

			},

		});
	}
</script>
