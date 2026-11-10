<?php
$pag = 'planos';

if (@$planos == 'ocultar') {
	echo "<script>window.location='index'</script>";
	exit();
}
?>

<div class="breadcrumb-header justify-content-between">
	<div class="left-content mt-2">
		<a class="btn ripple btn-primary text-white" onclick="inserir()" type="button"><i class="fe fe-plus me-1"></i>
			Adicionar <?php echo ucfirst($pag); ?></a>




<big><a class="btn btn-danger" href="#" onclick="deletarSel()" title="Excluir" id="btn-deletar" style="display:none"><i class="fe fe-trash-2"></i> Deletar</a></big>

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


<!-- MODAL CADASTRAR -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_inserir"></span></h4>
				<button id="btn-fechar" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<form id="form">
				<div class="modal-body">


					<div class="row needs-validation was-validated">
						<div class="col-md-12 mb-2 col-6">
							<label>Nome</label>
							<input type="text" class="form-control" id="nome" name="nome" placeholder="Nome do Acesso" required>
						</div>

						
					</div>

					<div class="row needs-validation was-validated">
						<div class="col-md-6 col-6">
							<label>Digite o valor</label>
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text">R$</span>
								</div>
								<input type="text" id="valor" name="valor" class="form-control" placeholder="R$ 0,00"
									oninput="mascaraMoeda(this)">
							</div>
						</div>


						<div class="col-md-6 mb-2 col-6">
							<label>Quantidade Dispositivos</label>
							<input type="text" class="form-control" id="dispositivos" name="dispositivos" placeholder="Dispositvos para Whatsapp" >
						</div>

					</div>


					<div class="row needs-validation was-validated">
						<div class="col-md-6 mb-2 col-6">
							<label>Quantidade Clientes</label>
							<input type="text" class="form-control" id="clientes" name="clientes" placeholder="Limitar em" >
						</div>

						<div class="col-md-6 mb-2 col-6">
							<label>Quantidade Usuários</label>
							<input type="text" class="form-control" id="usuarios" name="usuarios" placeholder="Limitar em" >
						</div>

					
					</div>


					</div>

					<div class="modal-footer">
					<button id="btn_salvar" type="submit" class="btn btn-primary">Salvar <i class="fa-solid fa-check"></i>
					</button>
					<button class="btn btn-primary" type="button" id="btn_carregando">
						<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Carregando...
					</button>
				</div>


					<input type="hidden" class="form-control" id="id" name="id">

					<br>
					<small>
						<div id="mensagem" align="center"></div>
					</small>
				</div>

			</form>
		</div>
	</div>
</div>






<!-- MODAL Item -->
<div class="modal fade" id="modalItem" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_item"></span></h4>
				<button id="btn-fechar-item" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<form id="form_item">
				<div class="modal-body">


					<div class="row needs-validation was-validated">
						<div class="col-md-8 mb-2">
							<label>Característica</label>
							<input type="text" class="form-control" id="nome_item" name="nome" placeholder="Característica Item" required>
						</div>

						<div class="col-md-4" style="margin-top: 25px">
							<button type="submit" class="btn btn-primary">Salvar</button>
						</div>
					</div>

					<hr>

					<div id="listar_itens"></div>

					</div>		


					<input type="hidden" class="form-control" id="id_item" name="id">

					<br>
					<small>
						<div id="mensagem" align="center"></div>
					</small>
				</div>

			</form>
		</div>
	</div>
</div>





<!-- MODAL Recursos -->
<div class="modal fade" id="modalRec" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_rec"></span></h4>
				<button id="btn-fechar-rec" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<form id="form_rec">
				<div class="modal-body">


					<div class="row needs-validation was-validated">
						<div class="col-md-8 mb-2">
							<label>Recurso</label>
							<select class="form-select" name="recurso" id="recurso">
								<?php
								$query = $pdo->query("SELECT * from recursos order by id asc");
								$res = $query->fetchAll(PDO::FETCH_ASSOC);
								$linhas = @count($res);
								if ($linhas > 0) {
									for ($i = 0; $i < $linhas; $i++) { ?>
										<option value="<?php echo $res[$i]['id'] ?>"><?php echo $res[$i]['nome'] ?></option>
									<?php }
								} ?>
							</select>
						</div>

						<div class="col-md-4" style="margin-top: 25px">
							<button type="submit" class="btn btn-primary">Salvar</button>
						</div>
					</div>

					<hr>

					<div id="listar_recursos"></div>

					</div>		


					<input type="hidden" class="form-control" id="id_rec" name="id">

					<br>
					<small>
						<div id="mensagem" align="center"></div>
					</small>
				</div>

			</form>
		</div>
	</div>
</div>






<script type="text/javascript">var pag = "<?= $pag ?>"</script>
<script src="js/ajax.js"></script>


<script type="text/javascript">
	
$("#form_item").submit(function (event) {

    event.preventDefault();
    var formData = new FormData(this);    
    $.ajax({
        url: 'paginas/' + pag + "/salvar_itens.php",
        type: 'POST',
        data: formData,
        success: function (mensagem) {            
            if (mensagem.trim() == "Salvo com Sucesso") {
                $('#nome_item').val('');                
                listarItens();                
            } else {
                alertErro(mensagem)
            }            
        },
        cache: false,
        contentType: false,
        processData: false,
    });
});


function listarItens() {

	var id = $('#id_item').val();     
    $.ajax({
        url: 'paginas/' + pag + "/listar_itens.php",
        method: 'POST',
        data: { id },
        dataType: "html",

        success: function (result) {
            $("#listar_itens").html(result);           
        }
    });
}




$("#form_rec").submit(function (event) {

    event.preventDefault();
    var formData = new FormData(this);    
    $.ajax({
        url: 'paginas/' + pag + "/salvar_rec.php",
        type: 'POST',
        data: formData,
        success: function (mensagem) {            
            if (mensagem.trim() == "Salvo com Sucesso") {
                
                listarRecursos();                
            } else {
                alertErro(mensagem)
            }            
        },
        cache: false,
        contentType: false,
        processData: false,
    });
});


function listarRecursos() {

	var id = $('#id_rec').val();     
    $.ajax({
        url: 'paginas/' + pag + "/listar_recursos.php",
        method: 'POST',
        data: { id },
        dataType: "html",

        success: function (result) {
            $("#listar_recursos").html(result);           
        }
    });
}
</script>