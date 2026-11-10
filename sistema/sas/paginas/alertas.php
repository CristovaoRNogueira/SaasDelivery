<?php
$pag = 'alertas';

if (@$alertas == 'ocultar') {
	echo "<script>window.location='index'</script>";
	exit();
}

?>

<div class="breadcrumb-header justify-content-between">
	<div class="left-content mt-2">
		<a class="btn ripple btn-primary text-white" onclick="inserir()" type="button"><i class="fe fe-user-plus me-1"></i>
			Adicionar <?php echo ucfirst($pag); ?></a>
		<big><a class="btn btn-danger" href="#" onclick="deletarSel()" title="Excluir" id="btn-deletar"
				style="display:none"><i class="fe fe-trash-2"></i> Deletar</a></big>
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

<!-- Modal  -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_inserir"></span></h4>
				<button id="btn-fechar" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<form id="form">
				<div class="modal-body needs-validation was-validated">
					<div class="mb-3" style="display:block; width:100%;">
						<label for="titulo">Título <span class="text-danger" style="font-size:9px">(Obrigatório)</span></label>
						<input maxlength="100" type="text" class="form-control" id="titulo" name="titulo" placeholder="Digite o Nome" required>
					</div>

					<div id="campo-texto-alerta" class="mb-3" style="display:block; width:100%; max-width:100%;">
						<label for="trumbowyg-editor">Texto <span class="text-danger" style="font-size:9px">(Obrigatório)</span></label>
						<textarea class="form-control" id="trumbowyg-editor" name="texto" rows="12" placeholder="Digite o texto completo do alerta" required></textarea>
					</div>

					<input type="hidden" id="id" name="id">
					<small><div id="mensagem" align="center"></div></small>
				</div>
				<div class="modal-footer">
					<button id="btn_salvar" type="submit" class="btn btn-primary">Salvar<i class="fa fa-check ms-2"></i></button>
					<button class="btn btn-primary" type="button" id="btn_carregando">
						<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Carregando...
					</button>
				</div>
			</form>
		</div>
	</div>
</div>


</div>

<script type="text/javascript">var pag = "<?= $pag ?>"</script>
	<script src="js/ajax.js"></script>




<style>
  #modalForm #campo-texto-alerta,
  #modalForm #trumbowyg-editor {
	display: block !important;
	box-sizing: border-box !important;
	width: 100% !important;
	min-width: 100% !important;
	max-width: 100% !important;
  }

  #modalForm #trumbowyg-editor {
	height: 300px !important;
	min-height: 300px !important;
	font-weight: 400 !important;
	line-height: 1.5 !important;
	resize: vertical !important;
  }
</style>
