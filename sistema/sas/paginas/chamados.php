<?php
require_once("verificar.php");
$pag = 'chamados';


?>

<div class="row" style="margin:10px">
	<button onclick="listar('')" style="margin-right: 10px" class="btn btn-danger">Abertos</button>
	<button onclick="listar('Fechado')" class="btn btn-success">Fechados</button>
</div>

	<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
		<div class="row row-sm">
			<div class="col-lg-12">
				<div class="card custom-card">
					<div class="card-body" id="listar">
					</div>
				</div>
			</div>
		</div>
	</div>
	





<!-- Modal Resposta -->
<div class="modal fade" id="modalResposta" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="empresa_resposta"></h4>
				<button id="btn-fechar-resposta" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				
				<form id="form_resp">
					
					<b><span id="titulo_resposta"></span></b><br>						
					
					<span id="empresa_texto" style="font-weight: normal"></span>
					<br>

					<div class="row">
					<div class="col-md-10 mb-2">
						
							<input type="text" class="form-control" id="texto_resposta" name="texto_resposta"
								placeholder="Escreva a Resposta" required>						
					</div>

						<div class="col-md-2" >
							<button id="btn_salvar_resp" type="submit" class="btn btn-primary">Salvar<i class="fa fa-check ms-2"></i>
							</button>
							<button class="btn btn-primary" type="button" id="btn_carregando_resp">
								<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Carregando...
							</button>
						</div>

				</div>

				


				<input type="hidden" class="form-control" id="id_resposta" name="id">
				</form>

				<div id="listar_respostas" style="margin-top: 15px"></div>
				
			</div>
		</div>
	</div>
</div>





<!-- Modal Arquivos -->
<div class="modal fade" id="modalArquivos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="tituloModal">Gestão de Arquivos - <span id="nome-arquivo"> </span></h4>
				<button id="btn-fechar-arquivos" aria-label="Close" class="btn-close" data-bs-dismiss="modal"
					type="button"><span class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<form id="form-arquivos_sas" method="post">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								<label>Arquivo</label>
								<input class="form-control" type="file" name="arquivo_conta" onChange="carregarImgArquivos();"
									id="arquivo_conta">
							</div>
						</div>
						<div class="col-md-4">
							<div id="divImgArquivos">
								<img src="images/arquivos/sem-foto.png" width="60px" id="target-arquivos">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-8">
							<input type="text" class="form-control" name="nome-arq" id="nome-arq" placeholder="Nome do Arquivo * "
								required>
						</div>
						<div class="col-md-4">
							<button id="btn_inserir" type="submit" class="btn btn-primary">Inserir <i class="fa-solid fa-check ms-2"></i></button>
							<button class="btn btn-primary" type="button" id="btn_carregando_inserir" style="display: none">
								<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Inserindo...
							</button>
						</div>
					</div>
					<hr>
					<small>
						<div id="listar-arquivos"></div>
					</small>
					<br>
					<small>
						<div align="center" id="mensagem-arquivo"></div>
					</small>
					<input type="hidden" class="form-control" name="id-arquivo" id="id-arquivo">
				</div>
			</form>
		</div>
	</div>
</div>




<script type="text/javascript">
	var pag = "<?= $pag ?>"
</script>
<script src="js/ajax.js"></script>


<script type="text/javascript">
	$(document).ready(function () {
    $('#btn_carregando_resp').hide();
    
});

</script>



<script type="text/javascript">
	
$("#form_resp").submit(function (event) {

    event.preventDefault();
    var formData = new FormData(this);
    $('#btn_salvar_resp').hide();
    $('#btn_carregando_resp').show();
    $.ajax({
        url: 'paginas/' + pag + "/resposta.php",
        type: 'POST',
        data: formData,
        success: function (mensagem) {
            $('#mensagem').text('');
            $('#mensagem').removeClass()
            if (mensagem.trim() == "Salvo com Sucesso") {
                $('#btn-fechar-resposta').click();
                sucesso();
                listar();
                $('#mensagem').text('')
            } else {
                alertErro(mensagem)
                $('#mensagem').addClass('text-danger')
                $('#mensagem').text(mensagem)
            }
            $('#btn_salvar_resp').show();
            $('#btn_carregando_resp').hide();
        },
        cache: false,
        contentType: false,
        processData: false,
    });
});




	function listarRespostas() {

		var id = $('#id_resposta').val();

		$.ajax({
			url: 'paginas/' + pag + "/listar_respostas.php",
			method: 'POST',
			data: {
				id
			},
			dataType: "html",

			success: function(result) {
				$("#listar_respostas").html(result);
			}
		});
	}

</script>



<script type="text/javascript">
	function carregarImgArquivos() {
		var target = document.getElementById('target-arquivos');
		var file = document.querySelector("#arquivo_conta").files[0];

		var arquivo = file['name'];
		resultado = arquivo.split(".", 2);

		if (resultado[1] === 'pdf') {
			$('#target-arquivos').attr('src', "images/pdf.png");
			return;
		}

		if (resultado[1] === 'rar' || resultado[1] === 'zip') {
			$('#target-arquivos').attr('src', "images/rar.png");
			return;
		}

		if (resultado[1] === 'doc' || resultado[1] === 'docx' || resultado[1] === 'txt') {
			$('#target-arquivos').attr('src', "images/word.png");
			return;
		}


		if (resultado[1] === 'xlsx' || resultado[1] === 'xlsm' || resultado[1] === 'xls') {
			$('#target-arquivos').attr('src', "images/excel.png");
			return;
		}


		if (resultado[1] === 'xml') {
			$('#target-arquivos').attr('src', "images/xml.png");
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




    $("#form-arquivos_sas").submit(function(event) {
        event.preventDefault();
        var formData = new FormData(this);

        $('#btn_inserir').hide();
    $('#btn_carregando_inserir').show();

        $.ajax({
            url: '../painel/paginas/' + pag + "/arquivos.php",
            type: 'POST',
            data: formData,

            success: function(mensagem) {
                $('#mensagem-arquivo').text('');
                $('#mensagem-arquivo').removeClass()
                if (mensagem.trim() == "Inserido com Sucesso") {
                    //$('#btn-fechar-arquivos').click();
                    $('#nome-arq').val('');
                    $('#arquivo_conta').val('');
                    $('#target-arquivos').attr('src', 'images/arquivos/sem-foto.png');
                    listarArquivos();
                } else {
                    $('#mensagem-arquivo').addClass('text-danger')
                    $('#mensagem-arquivo').text(mensagem)
                }
                $('#btn_inserir').show();
                $('#btn_carregando_inserir').hide();
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });


    function listarArquivos() {
        var id = $('#id-arquivo').val();
        $.ajax({
            url: '../painel/paginas/' + pag + "/listar-arquivos.php",
            method: 'POST',
            data: {
                id
            },
            dataType: "text",
            success: function(result) {
                $("#listar-arquivos").html(result);
            }
        });
    }
</script>


