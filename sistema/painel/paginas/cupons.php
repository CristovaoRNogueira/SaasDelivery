<?php
//verificar se ele tem a permissão de estar nessa página
if (@$cupons == 'ocultar') {
	echo "<script>window.location='../index.php'</script>";
	exit();
}
$pag = 'cupons';
?>
<div class="breadcrumb-header justify-content-between">
	<div class="left-content mt-2">
		<a class="btn ripple btn-primary text-white" onclick="inserir()" type="button"><i class="fe fe-plus me-1"></i> Adicionar <?php echo ucfirst($pag); ?></a>

		<a class="btn btn-danger" href="#" onclick="deletarSel()" title="Excluir" id="btn-deletar" style="display:none"><i class="fe fe-trash-2"></i> Deletar</a>

	</div>
</div>


<div class="bs-example widget-shadow" style="padding:15px" id="listar">

</div>

<input type="hidden" id="ids">



<!-- Modal Inserir-->
<div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title"><span id="titulo_inserir"></span></h4>
				<button id="btn-fechar" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<form id="form">
				<div class="modal-body">

					<div class="row">
						<div class="col-md-4 needs-validation was-validated">
							<div class="form-group">
								<label>Código <span class="text-danger" style="font-size: 9px">(Obrigatório)</span></label>
								<input type="text" class="form-control" id="codigo" name="codigo" placeholder="Código do Cupom" required>
							</div>
						</div>

						<div class="col-md-4 needs-validation was-validated">
							<div class="form-group">
								<label>Valor <span class="text-danger" style="font-size: 9px">(Obrigatório)</span></label>
								<input type="text" class="form-control" id="valor" name="valor" placeholder="Valor" required>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label>Tipo Valor</label>
								<select class="form-select" id="tipo" name="tipo">
									<option value="R$">Em Reais</option>
									<option value="%">Porcentagem</option>
								</select>
							</div>
						</div>

					</div>

					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label>Quantidade</label>
								<input type="number" class="form-control" id="quantidade" name="quantidade" placeholder="Quantidade" value="1">
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label>Data Validade</label>
								<input type="date" class="form-control" id="data_validade" name="data" placeholder="Data de Validade">
							</div>
						</div>


						<div class="col-md-4">
							<div class="form-group">
								<label>Valor Mínimo</label>
								<input type="text" class="form-control" id="valor_minimo" name="valor_minimo" placeholder="Valor Mínimo">
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
					<button class=" btn btn-primary" type="button" id="btn_carregando" style="display: none">
						<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Carregando...
					</button>
				</div>
			</form>


		</div>
	</div>
</div>





<script type="text/javascript">
	var pag = "<?= $pag ?>"
</script>
<script src="js/ajax.js"></script>


<script>
	modalForm.addEventListener('shown.bs.modal', () => {
		codigo.focus()
	})
</script>