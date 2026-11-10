<?php
//verificar se ele tem a permissão de estar nessa página
if (@$dias == 'ocultar') {
	echo "<script>window.location='../index.php'</script>";
	exit();
}
$pag = 'dias';
?>
<div class="breadcrumb-header justify-content-between">
	<div class="left-content mt-2">
		<a class="btn ripple btn-primary text-white" onclick="inserir()" type="button"><i class="fe fe-plus me-1"></i> Adicionar <?php echo ucfirst($pag); ?></a>

		<a class="btn btn-danger" href="#" onclick="deletarSel()" title="Excluir" id="btn-deletar" style="display:none"><i class="fe fe-trash-2"></i> Deletar</a>

	</div>
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


<!-- Modal Inserir-->
<div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title"><span id="titulo_inserir"></span></h4>
				<button id="btn-fechar" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<form id="form">
				<div class="modal-body">

					<div class="row ">
						<div class="col-md-9">
							<div class="form-group">
								<label for="exampleInputEmail1">Dia Fechado</label>
								<select class="form-select" name="dia" id="dia" style="width:100%;">
									<option value="Segunda-Feira">Segunda-Feira</option>
									<option value="Terça-Feira">Terça-Feira</option>
									<option value="Quarta-Feira">Quarta-Feira</option>
									<option value="Quinta-Feira">Quinta-Feira</option>
									<option value="Sexta-Feira">Sexta-Feira</option>
									<option value="Sábado">Sábado</option>
									<option value="Domingo">Domingo</option>

								</select>
							</div>
						</div>


						<div class="col-md-3" style="margin-top: 24px">
							<div class="form-group">
								<button type="submit" class="btn btn-primary" id="btn_salvar">Salvar<i class="fa fa-check ms-2"></i></button>
								<button class="btn btn-primary" type="button" id="btn_carregando" style="display: none">
									<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
								</button>
							</div>
						</div>

					</div>



					<input type="hidden" name="id" id="id">

					<br>
					<small>
						<div id="mensagem" align="center"></div>
					</small>
				</div>


			</form>


		</div>
	</div>
</div>





<script type="text/javascript">
	var pag = "<?= $pag ?>"
</script>
<script src="js/ajax.js"></script>