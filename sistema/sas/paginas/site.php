<?php
require_once("verificar.php");
$pag = 'site';

//verificar se ele tem a permissão de estar nessa página
if (@$site == 'ocultar') {
	echo "<script>window.location='index'</script>";
	exit();
}


//buscar as informações
$query = $pdo->query("SELECT * FROM site where empresa = '0'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$logo = @$res[0]['logo'];
$titulo = @$res[0]['titulo'];
$subtitulo = @$res[0]['subtitulo'];
$botao1 = @$res[0]['botao1'];
$botao2 = @$res[0]['botao2'];
$botao3 = @$res[0]['botao3'];
$item1 = @$res[0]['item1'];
$item2 = @$res[0]['item2'];
$item3 = @$res[0]['item3'];
$titulo_recursos = @$res[0]['titulo_recursos'];
$titulo_perguntas = @$res[0]['titulo_perguntas'];
$titulo_rodape = @$res[0]['titulo_rodape'];
$link_rodape = @$res[0]['link_rodape'];
$descricao_rodape = @$res[0]['descricao_rodape'];
$botao_rodape = @$res[0]['botao_rodape'];
$logo_topo = @$res[0]['logo_topo'];
$fundo_topo = @$res[0]['fundo_topo'];
$fundo_topo_mobile = @$res[0]['fundo_topo_mobile'];

if($logo_topo == ""){
	$logo_topo = 'Sim';
}

if($logo == ""){
	$logo = 'sem-foto.png';
}

if($fundo_topo == ""){
	$fundo_topo = 'sem-foto.png';
}

if($fundo_topo_mobile == ""){
	$fundo_topo_mobile = 'sem-foto.png';
}

$query = $pdo->query("SELECT * from recursos_site where empresa = 0 order by id desc limit 1");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$posicao = @$res[0]['posicao_recurso'];
$nova_posicao = $posicao + 1;	

$query = $pdo->query("SELECT * from perguntas_site where empresa = 0 order by id desc limit 1");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$posicao_perguntas = @$res[0]['posicao_pergunta'];
$nova_posicao_perguntas = $posicao_perguntas + 1;

?>

<div class="justify-content-between" style="margin-top: 20px">
	<nav style="margin-bottom: 20px">
		<div class="nav nav-tabs" id="nav-tab" role="tablist">
			<button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button"
			role="tab" aria-controls="nav-home" aria-selected="true">
			<i class="fa fa-globe "></i>
		Topo Site</button>

		<button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab"
		data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
		<i class="fa fa-list "></i>
	Recursos</button>

	<button class="nav-link" id="nav-perguntas-tab" data-bs-toggle="tab"
	data-bs-target="#nav-perguntas" type="button" role="tab" aria-controls="nav-perguntas" aria-selected="false">
	<i class="fa fa-question "></i>
Perguntas</button>

<button class="nav-link" id="nav-perguntas-tab" data-bs-toggle="tab"
data-bs-target="#nav-rodape" type="button" role="tab" aria-controls="nav-rodape" aria-selected="false">
<i class="fa fa-globe "></i>
Rodapé</button>
</div>
</nav>
</div>

<form id="form">
	<div class="tab-content" id="nav-tabContent">
		<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">


			
			<div class="row">
				<div class="col-md-4 mb-2">
					<label>Logo (Retangular) Ex:600 x 200</label>
					<input type="file" class="form-control" id="foto" name="foto" onchange="carregarImg()">
				</div>
				<div class="col-md-2">
					<img width="100px" id="target" src="../img/logos/<?php echo $logo ?>">
				</div>


				<div class="col-md-3 mb-2">
					<label>Mostrar Logo Topo</label>
					
					<select class="form-select" name="logo_topo" id="logo_topo">
						<option value="Sim">Sim</option>	
						<option value="Não">Não</option>						
					</select>
				</div>


				



				
			</div>


			<div class="row">
				<div class="col-md-4 mb-2">
					<label>Fundo Topo (Retangular) Ex:1920 x 500  <a title="Excluir Imagem" href="#" onclick="excluirFundo('0')"><i class="fa fa-trash-can text-danger"></i></a></label>
					<input type="file" class="form-control" id="fundo_topo" name="fundo_topo" onchange="carregarImgFundoTopo()">
				</div>
				<div class="col-md-2">
					<img width="100px" id="target_fundo_topo" src="../img/logos/<?php echo $fundo_topo ?>">
				</div>


				<div class="col-md-5 mb-2">
					<label>Fundo Topo (Vertical) Ex:500 x 900  <a title="Excluir Imagem" href="#" onclick="excluirFundoMobile('0')"><i class="fa fa-trash-can text-danger"></i></a></label>
					<input type="file" class="form-control" id="fundo_topo_mobile" name="fundo_topo_mobile" onchange="carregarImgFundoTopoMobile()">
				</div>
				<div class="col-md-1">
					<img width="100px" id="target_fundo_topo_mobile" src="../img/logos/<?php echo $fundo_topo_mobile ?>">
				</div>
			</div>



			<div class="row">
				<div class="col-md-6 mb-2  needs-validation was-validated">
					<label>Título</label>
					<input type="text" class="form-control" id="titulo" name="titulo" placeholder="Título Site" value="<?php echo $titulo ?>">
				</div>
				<div class="col-md-6 mb-2">
					<label>Subtítulo</label>
					<input type="text" class="form-control" id="subtitulo" name="subtitulo" placeholder="Subtítulo do Site" value="<?php echo $subtitulo ?>">
				</div>
			</div>

			<div class="row">
				<div class="col-md-4 mb-2">
					<label>Botão 1</label>
					<input type="text" class="form-control" id="botao1" name="botao1" placeholder="Texto do Botão 1" value="<?php echo $botao1 ?>">
				</div>
				<div class="col-md-4 mb-2">
					<label>Botão 2</label>
					<input type="text" class="form-control" id="botao2" name="botao2" placeholder="Texto do Botão 2" value="<?php echo $botao2 ?>">
				</div>
				<div class="col-md-4 mb-2">
					<label>Botão 3</label>
					<input type="text" class="form-control" id="botao3" name="botao3" placeholder="Texto do Botão 3" value="<?php echo $botao3 ?>">
				</div>
			</div>

			<div class="row">
				<div class="col-md-4 mb-2">
					<label>Item 1</label>
					<input type="text" class="form-control" id="item1" name="item1" placeholder="Texto do Item 1" value="<?php echo $item1 ?>">
				</div>
				<div class="col-md-4 mb-2">
					<label>Item 2</label>
					<input type="text" class="form-control" id="item2" name="item2" placeholder="Texto do Item 2" value="<?php echo $item2 ?>">
				</div>
				<div class="col-md-4 mb-2">
					<label>Item 3</label>
					<input type="text" class="form-control" id="item3" name="item3" placeholder="Texto do Item 3" value="<?php echo $item3 ?>">
				</div>
			</div>


			<div align="right">
				<button id="btn_salvar" type="submit" class="btn btn-primary">Salvar <i
							class="fa-solid fa-check"></i></button>
					<button class="btn btn-primary" type="button" id="btn_carregando">
						<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Carregando...
					</button>
			</div>

			

		</div>



		<div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
			<div class="row">
				<div class="col-md-10 mb-2  needs-validation was-validated">
					<label>Título</label>
					<input type="text" class="form-control" id="titulo_recursos" name="titulo_recursos" placeholder="Título da área Recursos" value="<?php echo $titulo_recursos ?>">
				</div>

				<div class="col-md-2 mb-2  needs-validation was-validated" style="margin-top: 25px">
					<button id="" type="submit" class="btn btn-primary">Salvar <i
							class="fa-solid fa-check"></i></button>					
				</div>
			</div>

			<hr>


			<div class="row">
				<div class="col-md-1 mb-2">
					<label>Posição</label>
					<input type="text" class="form-control" id="posicao_recurso" name="posicao_recurso" placeholder="1,2,3" value="">
				</div>

				<div class="col-md-7 mb-2  needs-validation was-validated">
					<label>Título do Item Recurso</label>
					<input type="text" class="form-control" id="titulo_recurso" name="titulo_recurso" placeholder="Título do Item" value="">
				</div>

				<div class="col-md-4 mb-2  needs-validation was-validated">
					<label>Ícone do Item Recurso</label>
					
					<select class="form-select" name="icone_recurso" id="icone_recurso">
						<option value="fas fa-chart-line">Gráfico Linha</option>
						<option value="fas fa-coins">Moeda</option>
						<option value="fas fa-boxes">Caixas</option>
						<option value="fas fa-headset">Headset</option>	
						<option value="fas fa-users-cog">Equipe</option>
						<option value="fas fa-file-contract">Arquivo / PDF</option>
						<option value="fas fa-cogs">Engrenagens</option>
					    <option value="fas fa-bell">Notificação</option>
					    <option value="fas fa-pen">Editar</option>
					    <option value="fas fa-trash-alt">Lixo (Excluir)</option>
					    <option value="fas fa-camera">Câmera</option>
					    <option value="fas fa-calendar-alt">Calendário</option>
					    <option value="fas fa-key">Chave</option>
					    <option value="fas fa-envelope">Envelope (Email)</option>
					    <option value="fas fa-shopping-cart">Carrinho de Compras</option>
					    <option value="fas fa-chart-pie">Gráfico Pizza</option>
					    <option value="fas fa-home">Casa</option>
					    <option value="fas fa-book">Livro</option>
					    <option value="fas fa-cloud">Nuvem</option>
					    <option value="fas fa-print">Impressora</option>
					    <option value="fas fa-sync-alt">Sincronizar</option>
					    <option value="fas fa-users">Usuários</option>
					    <option value="fas fa-search">Lupa (Pesquisa)</option>
					    <option value="fas fa-globe">Globo (Mundo)</option>
					    <option value="fas fa-shield-alt">Escudo</option>
					</select>
				</div>

			</div>


			<div class="row">
				<div class="col-md-10 mb-2  needs-validation was-validated">
					<label>Descrição do Item Recurso</label>
					<input type="text" class="form-control" id="descricao_recurso" name="descricao_recurso" placeholder="Descrição do Item">
				</div>

				<div class="col-md-2 mb-2  needs-validation was-validated" style="margin-top: 25px">
					<button onclick="salvarRecursos()" type="button" class="btn btn-success">Inserir Item <i
							class="fa-solid fa-check"></i></button>					
				</div>
			</div>

			<input type="hidden" name="id" id="id_recurso">
			<input type="hidden" name="empresa" value="0">


			<div id="listar_recursos"></div>

		</div>



		<div class="tab-pane fade" id="nav-perguntas" role="perguntas" aria-labelledby="nav-perguntas-tab">
			
			<div class="row">
				<div class="col-md-10 mb-2  needs-validation was-validated">
					<label>Título</label>
					<input type="text" class="form-control" id="titulo_perguntas" name="titulo_perguntas" placeholder="Título da área Perguntas" value="<?php echo $titulo_perguntas ?>">
				</div>

				<div class="col-md-2 mb-2  needs-validation was-validated" style="margin-top: 25px">
					<button id="" type="submit" class="btn btn-primary">Salvar <i
							class="fa-solid fa-check"></i></button>					
				</div>
			</div>

			<hr>


			<div class="row">
				<div class="col-md-2 mb-2">
					<label>Posição</label>
					<input type="text" class="form-control" id="posicao_pergunta" name="posicao_pergunta" placeholder="1,2,3" value="">
				</div>

				<div class="col-md-10 mb-2  needs-validation was-validated">
					<label>Pergunta</label>
					<input type="text" class="form-control" id="titulo_pergunta" name="titulo_pergunta" placeholder="Título da Pergunta" value="">
				</div>
			</div>

				
			<div class="row">
				<div class="col-md-10 mb-2  needs-validation was-validated">
					<label>Resposta</label>
					<input type="text" class="form-control" id="descricao_pergunta" name="descricao_pergunta" placeholder="Resposta da Pergunta">
				</div>

				<div class="col-md-2 mb-2  needs-validation was-validated" style="margin-top: 25px">
					<button onclick="salvarPerguntas()" type="button" class="btn btn-success">Inserir Item <i
							class="fa-solid fa-check"></i></button>					
				</div>
			</div>

			<input type="hidden" name="id" id="id_pergunta">
			<input type="hidden" name="empresa" value="0">


			<div id="listar_perguntas"></div>


		</div>



		<div class="tab-pane fade" id="nav-rodape" role="rodape" aria-labelledby="nav-rodape-tab">
			
			<div class="row">

				<div class="col-md-8 mb-2  needs-validation was-validated">
					<label>Título</label>
					<input type="text" class="form-control" id="titulo_rodape" name="titulo_rodape" placeholder="Título Rodapé" value="<?php echo $titulo_rodape ?>">
				</div>

				<div class="col-md-4 mb-2">
					<label>Botão</label>
					<input type="text" class="form-control" id="botao_rodape" name="botao_rodape" placeholder="Texto Botão" value="<?php echo $botao_rodape ?>">
				</div>
				
			</div>

			<div class="row">
				<div class="col-md-12 mb-2">
					<label>Link Botão</label>
					<input type="text" class="form-control" id="link_rodape" name="link_rodape" placeholder="Usar #plans se for para área dos planos, ou usar um link completo https://google.com para outro lugar" value="<?php echo $link_rodape ?>">
				</div>
			</div>


			<div class="row">
				<div class="col-md-12 mb-2">
					<label>Descrição Rodapé</label>
					<input type="text" class="form-control" id="descricao_rodape" name="descricao_rodape" placeholder="Descrição do Rodapé" value="<?php echo $descricao_rodape ?>">
				</div>
			</div>

			<div align="right">
				<button id="" type="submit" class="btn btn-primary">Salvar <i
							class="fa-solid fa-check"></i></button>
					
			</div>

			
		</div>


		<input type="hidden" name="id" value="0">
	</div>
</form>

<br>
<script type="text/javascript">
	var pag = "<?= $pag ?>"
</script>
<script src="js/ajax.js"></script>


<script type="text/javascript">
	$(document).ready(function () {  
	$('#logo_topo').val("<?= $logo_topo ?>");  
    listarRecursos();
    limparCamposRecursosEntrada();

    listarPerguntas();
    limparCamposPerguntasEntrada();
});
</script>


<script type="text/javascript">
	function carregarImg() {
		var target = document.getElementById('target');
		var file = document.querySelector("#foto").files[0];

		var arquivo = file['name'];
		resultado = arquivo.split(".", 2);

		
		var reader = new FileReader();

		reader.onloadend = function () {
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
	function salvarRecursos(){
		var titulo_recurso = $('#titulo_recurso').val();
		var posicao_recurso = $('#posicao_recurso').val();
		var descricao_recurso = $('#descricao_recurso').val();
		var icone_recurso = $('#icone_recurso').val();
		var id = $('#id_recurso').val();

		$.ajax({
        url: 'paginas/' + pag + "/salvar_recurso.php",
        method: 'POST',
        data: { titulo_recurso, posicao_recurso, descricao_recurso, icone_recurso, id },
        dataType: "html",

        success: function (result) {
        	if(result.trim() == 'Salvo com Sucesso'){
        		listarRecursos();
        		 limparCamposRecursos();
        		 
        	}else{
        		alertWarning(result)
        	}
           
        }
		    });
		}


		function listarRecursos(){			
			 $.ajax({
		        url: 'paginas/' + pag + "/listar_recursos.php",
		        method: 'POST',
		        data: { },
		        dataType: "html",

		        success: function (result) {
		            $("#listar_recursos").html(result);		           
		        }
		    });
		}

		function limparCamposRecursosEntrada() {
		var posicao = "<?= $nova_posicao ?>";		
		$('#id_recurso').val('');
		$('#titulo_recurso').val('');
		$('#descricao_recurso').val('');
		$('#posicao_recurso').val(posicao);
		
	}
</script>





<script type="text/javascript">
	function salvarPerguntas(){
		var titulo_pergunta = $('#titulo_pergunta').val();
		var posicao_pergunta = $('#posicao_pergunta').val();
		var descricao_pergunta = $('#descricao_pergunta').val();
		
		var id = $('#id_pergunta').val();

		$.ajax({
        url: 'paginas/' + pag + "/salvar_pergunta.php",
        method: 'POST',
        data: { titulo_pergunta, posicao_pergunta, descricao_pergunta, id },
        dataType: "html",

        success: function (result) {
        	if(result.trim() == 'Salvo com Sucesso'){
        		listarPerguntas();
        		 limparCamposPerguntas();
        		 
        	}else{
        		alertWarning(result)
        	}
           
        }
		    });
		}


		function listarPerguntas(){			
			 $.ajax({
		        url: 'paginas/' + pag + "/listar_perguntas.php",
		        method: 'POST',
		        data: { },
		        dataType: "html",

		        success: function (result) {
		            $("#listar_perguntas").html(result);		           
		        }
		    });
		}

		function limparCamposPerguntasEntrada() {
		var posicao = "<?= $nova_posicao_perguntas ?>";		
		$('#id_pergunta').val('');
		$('#titulo_pergunta').val('');
		$('#descricao_pergunta').val('');
		$('#posicao_pergunta').val(posicao);
		
	}
</script>





<script type="text/javascript">
	function carregarImgFundoTopo() {
		var target = document.getElementById('target_fundo_topo');
		var file = document.querySelector("#fundo_topo").files[0];

		var arquivo = file['name'];
		resultado = arquivo.split(".", 2);

		
		var reader = new FileReader();

		reader.onloadend = function () {
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

// ALERT EXCLUIR #######################################
function excluirFundo(id) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success", // Adiciona margem à direita do botão "Sim, Excluir!"
            cancelButton: "btn btn-danger me-1",
            container: 'swal-whatsapp-container'
        },
        buttonsStyling: false
    });

    swalWithBootstrapButtons.fire({
        title: "Deseja Excluir a Imagem?",
        text: "Você não conseguirá recuperá-lo novamente!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sim, Excluir!",
        cancelButtonText: "Não, Cancelar!",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Realiza a requisição AJAX para excluir o item
            $.ajax({
                url: 'paginas/' + pag + "/excluir_fundo.php",
                method: 'POST',
                data: { id },
                dataType: "html",
                success: function (mensagem) {
                    if (mensagem.trim() == "Excluído com Sucesso") {
                        // Exibe mensagem de sucesso após a exclusão
                        swalWithBootstrapButtons.fire({
                            title: mensagem,
                            text: 'Fecharei em 1 segundo.',
                            icon: "success",
                            timer: 1000,
                            timerProgressBar: true,
                            confirmButtonText: 'OK',
                            customClass: {
                             container: 'swal-whatsapp-container'
                             }
                        });
                        location.reload();
                    } else {
                        // Exibe mensagem de erro se a requisição falhar
                        swalWithBootstrapButtons.fire({
                            title: "Opss!",
                            text: mensagem,
                            icon: "error",
                            confirmButtonText: 'OK',
                            customClass: {
                             container: 'swal-whatsapp-container'
                             }
                        });
                    }
                }
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            swalWithBootstrapButtons.fire({
                title: "Cancelado",
                text: "Fecharei em 1 segundo.",
                icon: "error",
                timer: 1000,
                timerProgressBar: true,
            });
        }
    });
}

</script>







<script type="text/javascript">
	function carregarImgFundoTopoMobile() {
		var target = document.getElementById('target_fundo_topo_mobile');
		var file = document.querySelector("#fundo_topo_mobile").files[0];

		var arquivo = file['name'];
		resultado = arquivo.split(".", 2);

		
		var reader = new FileReader();

		reader.onloadend = function () {
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

// ALERT EXCLUIR #######################################
function excluirFundoMobile(id) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success", // Adiciona margem à direita do botão "Sim, Excluir!"
            cancelButton: "btn btn-danger me-1",
            container: 'swal-whatsapp-container'
        },
        buttonsStyling: false
    });

    swalWithBootstrapButtons.fire({
        title: "Deseja Excluir a Imagem?",
        text: "Você não conseguirá recuperá-lo novamente!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sim, Excluir!",
        cancelButtonText: "Não, Cancelar!",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Realiza a requisição AJAX para excluir o item
            $.ajax({
                url: 'paginas/' + pag + "/excluir_fundo_mobile.php",
                method: 'POST',
                data: { id },
                dataType: "html",
                success: function (mensagem) {
                    if (mensagem.trim() == "Excluído com Sucesso") {
                        // Exibe mensagem de sucesso após a exclusão
                        swalWithBootstrapButtons.fire({
                            title: mensagem,
                            text: 'Fecharei em 1 segundo.',
                            icon: "success",
                            timer: 1000,
                            timerProgressBar: true,
                            confirmButtonText: 'OK',
                            customClass: {
                             container: 'swal-whatsapp-container'
                             }
                        });
                        location.reload();
                    } else {
                        // Exibe mensagem de erro se a requisição falhar
                        swalWithBootstrapButtons.fire({
                            title: "Opss!",
                            text: mensagem,
                            icon: "error",
                            confirmButtonText: 'OK',
                            customClass: {
                             container: 'swal-whatsapp-container'
                             }
                        });
                    }
                }
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            swalWithBootstrapButtons.fire({
                title: "Cancelado",
                text: "Fecharei em 1 segundo.",
                icon: "error",
                timer: 1000,
                timerProgressBar: true,
            });
        }
    });
}

</script>