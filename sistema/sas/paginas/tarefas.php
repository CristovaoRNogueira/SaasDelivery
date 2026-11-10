<?php
$pag = 'tarefas';

if (@$tarefas == 'ocultar') {
	echo "<script>window.location='index'</script>";
	exit();
}


?>

<div class="justify-content-between">
	<form action="rel/pagar_class.php" target="_blank" method="POST">
		<div class="left-content mt-2">
			<a style="margin-bottom: 20px; margin-top: 20px" class="btn ripple btn-primary text-white" onclick="inserir()"
				type="button"><i class="fe fe-plus me-2"></i>Adicionar <?php echo ucfirst($pag); ?></a>

			<a class="btn btn-danger" href="#" onclick="deletarSel()" title="Excluir" id="btn-deletar" style="display:none"><i
					class="fe fe-trash-2"></i> Deletar</a>

			<a class="btn btn-success" id="btn-concluir" href="#" onclick="concluirSel()" title="Concluir Tarefas" id="btn-deletar" style="display:none"><i class="fa fa-check-square"></i> Concluir Tarefas</a>
			

			<div class="cab_mobile"></div>

			<div style="display: inline-block; margin-bottom: 10px">
				<input style="height:35px; width:49%; font-size: 13px;" type="date" class="form-control2" name="dataInicial"
					id="dataInicial" value="<?php echo $data_inicio_mes ?>" required onchange="buscar()">
				<input style="height:35px; width:49%; font-size: 13px" type="date" name="dataFinal" id="dataFinal"
					class="form-control2" value="<?php echo $data_final_mes ?>" required onchange="buscar()">
			</div>
			<div style="display: inline-block; margin-bottom: 10px">
					<span class="<?php echo @$lancar_tarefas ?>">
						<select class="form-select sel60 " aria-label="Default select example" name="usuario_selecionado"
							id="usuario_selecionado" style="width:350px;" onchange="buscar()">
							<?php
							if ($nivel_usuario == 'Administrador' || @$lancar_tarefas == '') {
								$query = $pdo->query("SELECT * FROM usuarios where empresa is null or empresa = 0 order by nome asc");
							} else {
								$query = $pdo->query("SELECT * FROM usuarios where id = '$id_usuario' ");
							}
							$res = $query->fetchAll(PDO::FETCH_ASSOC);
							for ($i = 0; $i < @count($res); $i++) {
								foreach ($res[$i] as $key => $value) {
								}
								?>
								<option value="<?php echo $res[$i]['id'] ?>" <?php if ($res[$i]['id'] == $id_usuario) { ?> selected <?php } ?>>
									<?php echo $res[$i]['nome'] ?>
								</option>
							<?php } ?>
						</select>
					</span>
					</div>


			<div class="card-group" style="margin-bottom: -30px">
				<div class="card text-center mb-5" style="width: 100%; margin-right: 10px; border-radius: 10px; height:90px">
					<a class="text-white" href="#" onclick="$('#tipo_data_filtro').val('Atrasadas'); buscar(); ">
						<div class="card-header bg-red">
							Tarefas Atrasadas
							<i class="fa fa-external-link pull-right"></i>
						</div>
						<div class="card-corpo">
							<p class="card-text" style="margin-top:-15px;">
							<h5><span class="text-danger" id="tarefas_atrasadas">0</span></h5>
							</p>
						</div>
					</a>
				</div>
				<div class="card text-center mb-5" style="width: 100%; margin-right: 10px; border-radius: 10px; height:90px">
					<a href="#" onclick="$('#tipo_data_filtro').val('Pendentes'); buscar(); ">
						<div class="card-header bg-orange">
							Tarefas Pendentes
							<i class="fa fa-external-link pull-right"></i>
						</div>
						<div class="card-corpo">
							<p class="card-text" style="margin-top:-15px;">
							<h5><span class="text-danger" id="tarefas_pendentes">0</span></h5>
							</p>
						</div>
					</a>
				</div>

				<div class="card text-center mb-5" style="width: 100%; margin-right: 10px; border-radius: 10px; height:90px">
					<a href="#" onclick=" $('#tipo_data_filtro').val('Hoje'); buscar();">
						<div class="card-header" style="background:rgb(0, 154, 201)">
							Tarefas Para Hoje
							<i class="fa fa-external-link pull-right"></i>
						</div>
						<div class="card-corpo">
							<p class="card-text" style="margin-top:-15px;">
							<h5><span style="color:rgb(0, 154, 201)" id="tarefas_hoje">0</span></h5>
							</p>
						</div>
					</a>
				</div>
				<div class="card text-center mb-5" style="width: 100%; margin-right: 10px; border-radius: 10px; height:90px">
					<a href="#" onclick="$('#tipo_data_filtro').val('Concluidas'); buscar();">
						<div class="card-header" style="background: rgb(0, 160, 67);">
							Tarefas Concluidas
							<i class="fa fa-external-link pull-right"></i>
						</div>
						<div class="card-corpo">
							<p class="card-text" style="margin-top:-15px;">
							<h5><span style="color:rgb(0, 160, 67)" class="verde" id="tarefas_concluidas">0</span></h5>
							</p>
						</div>
					</a>
				</div>
			</div>

		</div>
		<input type="hidden" name="tipo_data_filtro" id="tipo_data_filtro">
	</form>

</div>



<div class="row row-sm">
	<div class="col-lg-12">
		<div class="card custom-card">
			<div class="card-body" id="listar">
			</div>
		</div>
	</div>
</div>



<input type="hidden" id="ids">


<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_inserir"></span></h4>
				<button id="btn-fechar" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<form id="form_tarefas">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12 mb-2">
							<label>Título</label>
							<input maxlength="255" type="text" class="form-control" id="titulo" name="titulo"
								placeholder="Titulo da Tarefa" required>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 mb-2">
							<span class="<?php echo @$lancar_tarefas ?>">
								<label>Usuário</label>
								<select class="form-select sel61" aria-label="Default select example" name="usuario_tarefa"
									id="usuario_tarefa" style="width:100%;">
									<?php
									if ($nivel_usuario == 'Administrador' || @$lancar_tarefas == '') {
										$query = $pdo->query("SELECT * FROM usuarios where (empresa = 0 or empresa is null) order by nome asc");
									} else {
										$query = $pdo->query("SELECT * FROM usuarios where id = '$id_usuario' ");
									}
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									for ($i = 0; $i < @count($res); $i++) {
										foreach ($res[$i] as $key => $value) {
										}
										?>
										<option value="<?php echo $res[$i]['id'] ?>" <?php if ($res[$i]['id'] == $id_usuario) { ?> selected
											<?php } ?>>
											<?php echo $res[$i]['nome'] ?>
										</option>
									<?php } ?>
								</select>
							</span>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 mb-2">
							<label>Hora</label>
							<input type="time" class="form-control" id="hora" name="hora" placeholder="" required>
						</div>
						<div class="col-md-6 mb-2">
							<label>Data</label>
							<input type="date" class="form-control" id="data_tarefa" name="data" required=""
								value="<?php echo $data_atual ?>">
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 mb-2">
							<label>Hora Alerta</label>
							<input type="time" class="form-control" id="hora_alerta" name="hora_alerta" placeholder="">
						</div>
						<div class="col-md-6 mb-2">
							<label>Prioridade</label>
							<select class="form-select" id="prioridade" name="prioridade">
								<option value="Baixa">Baixa</option>
								<option value="Média">Média</option>
								<option value="Alta">Alta</option>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 mb-2">
							<label>Recorrência</label>
							<select class="form-select" id="recorrencia" name="recorrencia">
								<option value="0">Não Recorrente</option>
								<?php
								$query = $pdo->query("SELECT * from frequencias where empresa = 0 order by id asc");
								$res = $query->fetchAll(PDO::FETCH_ASSOC);
								$linhas = @count($res);
								if ($linhas > 0) {
									for ($i = 0; $i < $linhas; $i++) {
										echo '<option value="' . $res[$i]['dias'] . '">' . $res[$i]['frequencia'] . '</option>';
									}
								} else {
									echo '<option value="0">Cadastre uma Forma de Pagamento</option>';
								}
								?>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 mb-2">
							<label>Descrição</label>
							<input maxlength="255" type="text" class="form-control" id="descricao" name="descricao"
								placeholder="Descrição da tarefa se Houver">
						</div>
					</div>
					<input type="hidden" class="form-control" id="id" name="id">
					<br>
					<small>
						<div id="mensagem" align="center"></div>
					</small>
				</div>
				<div class="modal-footer">
				<button id="btn_salvar" type="submit" class="btn btn-primary">Salvar<i class="fa fa-check ms-2"></i>
							</button>
							<button class="btn btn-primary" type="button" id="btn_carregando">
								<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Carregando...
							</button>
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
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_dados"></span></h4>
				<button id="btn-fechar-dados" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>

			<div class="modal-body">

				<div class="row">
					<div class="col-md-12">
						<div class="tile">
							<div class="table-responsive">
								<table id="" class="text-left table table-bordered">
									<tr>
										<td width="20%" class="bg-primary text-white">Usuário</td>
										<td><span id="usuario_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white">Usuário cad</td>
										<td><span id="usuario_lanc_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Data</td>
										<td><span id="data_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Hora</td>
										<td><span id="hora_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Status</td>
										<td><span id="status_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Hora Alerta</td>
										<td><span id="hora_mensagem_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Recorrência</td>
										<td><span id="recorrencia_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Prioridade</td>
										<td><span id="prioridade_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Descricao</td>
										<td><span id="descricao_dados"></span></td>
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



<input type="hidden" id="data_selecionada" value="<?php echo $data_atual ?>">
<input type="hidden" id="array_datas" value="">

<script type="text/javascript">
	$(document).ready(function () {
		$('.sel61').select2({
			dropdownParent: $('#modalForm')
		});
		$('.sel60').select2({
			//dropdownParent: $('#modalForm')
		});
		buscar()
	});
</script>

<script>
	$("#form_tarefas").submit(function () {
		event.preventDefault();
		var formData = new FormData(this);
		$('#btn_salvar').hide();
    $('#btn_carregando').show();
		$.ajax({
			url: 'paginas/' + pag + "/salvar.php",
			type: 'POST',
			data: formData,
			success: function (mensagem) {
				$('#mensagem').text('');
				$('#mensagem').removeClass()
				if (mensagem.trim() == "Salvo com Sucesso") {
					alertsucesso(mensagem)
					$('#btn-fechar').click();
					buscar();
					$('#mensagem').text('')
				} else {
					$('#mensagem').addClass('text-danger')
					$('#mensagem').text(mensagem)
				}
				$('#btn_salvar').show();
            $('#btn_carregando').hide();
			},
			cache: false,
			contentType: false,
			processData: false,
		});
	});
</script>




<script type="text/javascript">
	function marcarTodos() {
		let checkbox = document.getElementById('input-todos');
		var usuario = $('#id_permissoes').val();

		if (checkbox.checked) {
			adicionarPermissoes(usuario);
		} else {
			limparPermissoes(usuario);
		}
	}
</script>





<script type="text/javascript">
	function buscar() {
		var filtro = $('#tipo_data_filtro').val();
		var dataInicial = $('#dataInicial').val();
		var dataFinal = $('#dataFinal').val();
		var usuario_selecionado = $('#usuario_selecionado').val();
		listar(filtro, dataInicial, dataFinal, usuario_selecionado)
	}




</script>




<script type="text/javascript">
	// DAR FOCO EM UMA MODAL
	const modalForm = document.getElementById('modalForm')
	const title = document.getElementById('titulo')
	modalForm.addEventListener('shown.bs.modal', () => {
		title.focus()
	})
</script>


<script type="text/javascript">	var pag = "<?= $pag ?>"</script>
<script src="js/ajax.js"></script>