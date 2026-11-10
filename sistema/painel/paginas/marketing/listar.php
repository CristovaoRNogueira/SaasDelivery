<?php
@session_start();
$id_empresa = @$_SESSION['empresa'];
$tabela = 'marketing';

$id_usuario = @$_SESSION['id'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");

$filtro_busca = @$_POST['p1'];
if($filtro_busca != ""){
	$sql_busca = " and total_disparos > 0 ";
	$ativo_disparos = 'active';
	$ativo_todas = '';
}else{
	$sql_busca = " ";
	$ativo_disparos = '';
	$ativo_todas = 'active';
}

$query = $pdo->query("SELECT * from $tabela where empresa = '$id_empresa' $sql_busca order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	?>

	<!-- Barra de Filtros e Pesquisa -->
	<div class="row mb-4">
		<div class="col-md-8">
			<div class="input-group">
				<span class="input-group-text bg-primary text-white"><i class="fa fa-search"></i></span>
				<form action="rel/marketing_class.php" target="_blank" method="post">
				<input type="text" class="form-control" id="pesquisar" name="pesquisar" placeholder="Buscar campanhas...">
				<input type="hidden" id="filtro_busca" name="filtro_busca">
				<button id="btn_rel" type="submit" style="display:none"></button>
				</form>
			</div>
		</div>
	</div>

	<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button onclick="$('#filtro_busca').val(''); buscar()" class="nav-link <?php echo $ativo_todas ?>" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Todas</button>
  </li>
  <li class="nav-item " role="presentation">
    <button onclick="$('#filtro_busca').val('Disparos'); buscar()" class="nav-link <?php echo $ativo_disparos ?>" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Em Disparos</button>
  </li> 
</ul>

	<!-- Grid de Cards -->
	<div class="row row-cards" id="contenedor-cards">
		<?php
		for ($i = 0; $i < $linhas; $i++) {
			$id = $res[$i]['id'];
			$arquivo = $res[$i]['arquivo'];
			$audio = $res[$i]['audio'];
			$data_envio = $res[$i]['data_envio'];
			$data = $res[$i]['data'];
			$envios = $res[$i]['envios'];
			$forma_envio = $res[$i]['forma_envio'];
			$documento = $res[$i]['documento'];
			$dispositivo = $res[$i]['dispositivo'];
			$total_disparos = $res[$i]['total_disparos'];

			if($total_disparos == ""){
				$total_disparos = 0;
			}


			// Obter dados brutos e decodificar as entidades HTML
			$titulo = html_entity_decode($res[$i]['titulo'], ENT_QUOTES, 'UTF-8');
			$msg = html_entity_decode($res[$i]['mensagem'], ENT_QUOTES, 'UTF-8');
			$msg2 = html_entity_decode($res[$i]['mensagem2'], ENT_QUOTES, 'UTF-8');

			// Codificar a mensagem para ser enviada via URL
			$msgF = rawurlencode($msg);
			$msg2F = rawurlencode($msg2);

			$data_envioF = implode('/', array_reverse(@explode('-', $data_envio)));
			$dataF = implode('/', array_reverse(@explode('-', $data)));

			if ($forma_envio == "") {
				$forma_envio = "Todos";
			}

			$tituloF = mb_strimwidth($titulo, 0, 40, "...");


			$ocultar_audio = 'ocultar';
			if ($audio != "") {
				$ocultar_audio = '';
			}

			$ocultar_foto = 'ocultar';
			if ($arquivo != "sem-foto.png") {
				$ocultar_foto = '';
			}

			if ($forma_envio == "") {
				$forma_envio = "Todos";
			}

			$ocultar_doc = 'ocultar';
			if ($documento != "sem-foto.png") {
				$ocultar_doc = '';
			}

			$ocultar_reg = '';

			//extensão do arquivo
			$ext = pathinfo($documento, PATHINFO_EXTENSION);
			if ($ext == 'pdf') {
				$tumb_arquivo = 'pdf.png';
			} else if ($ext == 'rar' || $ext == 'zip') {
				$tumb_arquivo = 'rar.png';
			} else {
				$tumb_arquivo = $documento;
			}

			$query2 = $pdo->query("SELECT * FROM disparos where campanha = '$id' ORDER BY id desc");
			$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
			$total_envios = @count($res2);

			if($total_envios == 0){
				$pdo->query("UPDATE marketing set total_disparos = 0 where id = '$id'");
			}

			$query2 = $pdo->query("SELECT * FROM disparos where campanha = '$id' ORDER BY data_disparo desc limit 1");
			$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
			$hora_ultimo_envio = @$res2[0]['hora'];

			$ocultar_parar = '';
			if($total_envios == 0){
				$ocultar_parar = 'ocultar';
			}
			?>

			<!-- Card de Campanha -->
			<div class="col-lg-4 col-md-6 col-sm-12 mb-4 card-campanha">
				<div class="card card-sm h-100 shadow-sm hover-shadow">
					<!-- Cabeçalho do Card -->
					<div class="card-header bg-transparent border-bottom-0 d-flex justify-content-between align-items-center">
						<h5 class="card-title mb-0 text-primary">
							<?= $tituloF ?>
						</h5>
						
					</div>

					<!-- Corpo do Card -->
					<div class="card-body pt-0">
						<!-- Informações da Campanha -->
						<div class="row g-3 mb-3">
							<!-- Tipo de Envio -->
							<div class="col-6">
								<small class="text-muted d-block">Tipo de Envio</small>
								<span class="badge bg-info-transparent text-info rounded-pill">
									<i class="bi bi-send me-1"></i>
									<?= $forma_envio ?>
								</span>
							</div>

							<!-- Último Envio -->
							<div class="col-6">
								<small class="text-muted d-block">Último Envio</small>
								<?php if($hora_ultimo_envio != ""): ?>
								<span class="badge bg-secondary-transparent text-secondary rounded-pill">
									<i class="fa fa-clock me-1"></i>
									<?= $hora_ultimo_envio ?>
								</span>
								<?php else: ?>
									<span class="badge bg-secondary-transparent text-secondary rounded-pill">
										<i class="fa fa-clock me-1"></i>
										Aguardando Disparo
									</span>
								<?php endif; ?>
							</div>
						</div>

						<!-- Barra de Progresso para Envios Pendentes -->
						<div class="mb-3">
							<div class="d-flex justify-content-between align-items-center mb-1">
								<small class="text-muted">Envios Pendentes</small>
								<span class="badge bg-primary-transparent text-primary" style="font-size: 12px"><?= $total_envios ?> / <?= $total_disparos ?></span>
							</div>
							<div class="progress" style="height: 6px;">
								<?php
								// Calculando a porcentagem para barra de progresso (exemplo)
								if($total_envios > 0 and $total_disparos > 0){
									$porcentagem = min(100, (($total_disparos - $total_envios) / $total_disparos) * 100);  // Apenas um exemplo
								}else{
									$porcentagem = 0;
								}
								
								?>
								<div class="progress-bar bg-primary" style="width: <?= $porcentagem ?>%" role="progressbar"
									aria-valuenow="<?= $porcentagem ?>" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
						</div>


							<!-- Elementos de Mídia -->
							<div class="d-flex gap-2 mb-3">
								<?php if ($arquivo != "sem-foto.png"): ?>
									<div class="position-relative">
    								<img src="images/marketing/<?= $arquivo ?>" class="rounded shadow-sm" width="40" height="40"
											data-bs-toggle="tooltip" title="Imagem">
										<div class="dropdown" style="display: inline-block" title="Excluir Imagem" data-bs-toggle="tooltip">
											<a title="Excluir Imagem" href="#" data-bs-toggle="dropdown" aria-expanded="false">
												<i class="fa fa-close text-danger"></i>
											</a>
											<ul class="dropdown-menu p-2" style="width: 180px; box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px !important; background-color: #ffe4e4;">
												<li>Excluir Imagem? <a href="#" onclick="excluirImagem(<?= $id ?>)"><span class="text-danger">Sim</span></a></li>
											</ul>
										</div>
									</div>
									

								<?php endif; ?>

								<?php if ($audio != ""): ?>
									<div class="position-relative">
										<span class="d-inline-block rounded shadow-sm p-2 bg-light" data-bs-toggle="tooltip" title="Áudio">
											<i class="fa fa-music text-warning"></i>
										</span>
										<div class="dropdown" style="display: inline-block" title="Excluir Imagem" data-bs-toggle="tooltip">
											<a class="<?= $ocultar_audio ?>" title="Excluir Imagem" href="#" data-bs-toggle="dropdown" aria-expanded="false">
												<i class="fa fa-close text-danger"></i>
											</a>
											<ul class="dropdown-menu p-2" style="width: 180px; box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px !important; background-color: #ffe4e4;">
												<li>Excluir Imagem? <a href="#" onclick="excluirAudio(<?= $id ?>)"><span class="text-danger">Sim</span></a></li>
											</ul>
										</div>
									</div>
								<?php endif; ?>

								<?php if ($documento != "sem-foto.png"): ?>
									<div class="position-relative">
										<a href="images/marketing/<?= $documento ?>" target="_blank" class="d-inline-block">
											<img src="images/marketing/<?= $tumb_arquivo ?>" class="rounded shadow-sm" width="40" height="40"
												data-bs-toggle="tooltip" title="Documento">
										</a>
										<div class="dropdown" style="display: inline-block" title="Excluir Imagem" data-bs-toggle="tooltip">
											<a  title="Excluir Imagem" href="#" data-bs-toggle="dropdown" aria-expanded="false">
												<i class="fa fa-close text-danger"></i>
											</a>
											<ul class="dropdown-menu p-2" style="width: 180px; box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px !important; background-color: #ffe4e4;">
												<li>Excluir Imagem? <a href="#" onclick="excluirDoc(<?= $id ?>)"><span class="text-danger">Sim</span></a></li>
											</ul>
										</div>
									</div>
								<?php endif; ?>
							</div>
					</div>

					<!-- Rodapé do Card -->
					<div class="card-footer bg-transparent border-top d-flex justify-content-between align-items-center">
						<small class="text-muted">Criado em: <?= $dataF ?></small>

						<!-- Botões Principais (os mais usados) -->
						<div class="btn-group">
							<button class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Editar Campanha"
								onclick="editar('<?= $id ?>','<?= $titulo ?>', '<?= $msgF ?>', '<?= $msg2F ?>', '<?= $arquivo ?>', '<?= $audio ?>', '<?= $documento ?>', '<?= $dispositivo ?>')">
								<i class="fa fa-edit"></i>
							</button>
							<button class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="Visuaizar Campanha"
								onclick="mostrar('<?= $id ?>','<?= $titulo ?>', '<?= $msgF ?>', '<?= $msg2F ?>', '<?= $arquivo ?>', '<?= $audio ?>', '<?= $documento ?>', '<?= $dataF ?>', '<?= $data_envioF ?>', '<?= $arquivo ?>', '<?= $tumb_arquivo ?>')">
								<i class="fa fa-eye"></i>
							</button>
							<button class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Disparar Campanha"
								onclick="disparar('<?= $id ?>','<?= $titulo ?>', '<?= $msgF ?>', '<?= $msg2F ?>', '<?= $arquivo ?>', '<?= $audio ?>', '<?= $tumb_arquivo ?>')">
								<i class="fa fa-paper-plane"></i>
							</button>
							<button class="btn btn-sm btn-outline-danger <?= $ocultar_parar ?>" data-bs-toggle="tooltip" title="Parar Campanha"
								onclick="pararModal('<?= $id ?>')">
								<i class="fa fa-ban"></i>
							</button>
							<button class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Excluir Campanha"
								onclick="excluir('<?= $id ?>')">
								<i class="fa fa-trash"></i>
							</button>
							

						</div>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>

	<div class="text-center mt-4" id="sem-registros" style="display: none;">
		<div class="alert alert-warning">
			<i class="fa fa-exclamation-triangle me-2"></i>
			Nenhuma campanha encontrada!
		</div>
	</div>

	<?php
} else {
	?>
	<div class="alert alert-info">
		<i class="fa fa-info-circle me-2"></i>
		Você ainda não possui nenhuma campanha de marketing cadastrada.
		<button type="button" class="btn btn-sm btn-primary float-end" onclick="$('#modalForm').modal('show');">
			<i class="fa fa-plus me-1"></i> Criar Campanha
		</button>
	</div>
	<?php
}
?>

<style>

</style>

<script type="text/javascript">
// Script para funcionalidades adicionais na listagem de marketing

$(document).ready(function() {
    // Ativar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Função de pesquisa para cards
    $("#pesquisar").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        var found = false;
        
        $(".card-campanha").filter(function() {
            var matches = $(this).text().toLowerCase().indexOf(value) > -1;
            $(this).toggle(matches);
            if (matches) found = true;
        });
        
        // Mostrar mensagem quando nenhum resultado for encontrado
        if (!found && value.length > 0) {
            $("#sem-registros").show();
        } else {
            $("#sem-registros").hide();
        }
    });
    
    // Adicionar classe para exibir animação quando um novo card é adicionado
    function refreshCardAnimations() {
        $(".card-campanha").each(function(index) {
            var $card = $(this);
            setTimeout(function() {
                $card.addClass("show");
            }, index * 100);
        });
    }
    
    refreshCardAnimations();
});

// Funções para os botões principais (reutilizando as funções existentes)
function formatarMensagemModal(msg) {
    // Essa função pode ser mantida conforme estava no código original
    // Ajuste conforme necessário para formatar a mensagem corretamente
    return msg.replace(/\n/g, '<br>');
}





function pararModal(id) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success", // Adiciona margem à direita do botão "Sim, Excluir!"
            cancelButton: "btn btn-danger me-1",
            container: 'swal-whatsapp-container'
        },
        buttonsStyling: false
    });

    swalWithBootstrapButtons.fire({
        title: "Parar os Disparos?",
        text: "Você irá parar todos os disparos dessa campanha!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sim, Parar!",
        cancelButtonText: "Não, Cancelar!",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            pararDisparo(id);
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

// Sobrescrever a função de parar para usar a versão com Sweet Alert
function pararDisparo(id) {
    $.ajax({
        url: 'paginas/' + pag + "/parar_envios.php",
        method: 'POST',
        data: {id: id},
        dataType: "text",
        success: function (mensagem) {
            if (mensagem.trim() == "Parado com Sucesso") {
                Swal.fire({
                    title: 'Parado!',
                    text: 'Os envios foram interrompidos com sucesso.',
                    icon: 'success',
                    timer: 1500,
                    timerProgressBar: true,
                });
                
                // Atualizar a listagem
                listar();
            } else {
                Swal.fire({
                    title: 'Erro!',
                    text: mensagem,
                    icon: 'error'
                });
            }
        }
    });
}
</script>


<script type="text/javascript">
	function editar(id, titulo, msg, msg2, arquivo, audio, documento, dispositivo) {

		// Antes de definir o src, verifique a extensão
var ext = documento.split('.').pop().toLowerCase();
if (ext == 'pdf') {
  $('#target_documento').attr('src','images/marketing/pdf.png');
} else if (ext == 'rar' || ext == 'zip') {
  $('#target_documento').attr('src','images/marketing/rar.png');
} else {
  $('#target_documento').attr('src','images/marketing/' + documento);
}
		
		
		msg = decodeURIComponent(msg);
		msg2 = decodeURIComponent(msg2);
		

		// Atualiza os campos do formulário
		$('#titulo_inserir').text('Editar Registro');
		$('#id').val(id);
		$('#titulo').val(titulo);
		$('#msg').val(msg);  // Alterado de .html() para .val()
		$('#msg2').val(msg2);
		$('#dispositivo').val(dispositivo).change();

		$('#titulo_inserir').text('Editar Campanha');
		$('#foto').val('');
		$('#target').attr('src','images/marketing/' + arquivo);
		


		// Atualiza o preview após preencher o campo msg
		atualizarPreview();
		atualizarPreview2();

		// Mostra a aba de edição
		$('#modalForm').modal('show');
	}




function mostrar(id, titulo, msg, msg2, arquivo, audio, documento, dataF, data_envioF, arquivo, tumb_arquivo) {
    msg = decodeURIComponent(msg);  // Decodifica a mensagem URL-encoded
	msg2 = decodeURIComponent(msg2);

    $('#id_dados').text(id);
    $('#titulo_dados').text(titulo);
	$('#data_envio_dados').text(data_envioF);
	$('#data_dados').text(dataF);
    $('#mensagem_dados').html(formatarMensagemModal(msg));
    $('#mensagem2_dados').html(formatarMensagemModal(msg2));


	$('#target_dados').attr('src','images/marketing/' + arquivo);
		$('#audio_dados').attr('src','images/marketing/' + audio);
		$('#target_documento_dados').attr('src','images/marketing/' + tumb_arquivo);

    $('#modalDados').modal('show');
}

	function limparCampos() {
		$('#id').val('');
		$('#titulo').val('');
		$('#preview').text('');
		$('#msg').val('');
		$('#msg2').val('');
		$('#ids').val('');
		$('#btn-deletar').hide();
		$('#dispositivo').val('').change();


		$('#foto').val('');
		$('#audio').val('');
		$('#documento').val('');
		$('#target').attr('src','images/marketing/sem-foto.png');
		$('#target_documento').attr('src','images/marketing/sem-foto.png');

		atualizarPreview();
		atualizarPreview2();
	}
</script>


<script>
	
	function disparar(id, titulo, msg, msg2, arquivo, audio, documento){

 msg = decodeURIComponent(msg);  // Decodifica a mensagem URL-encoded
	msg2 = decodeURIComponent(msg2);

		$('#nome_entrada').text(titulo);		
		$('#id_entrada').val(id);	
		$('#arquivo_excel').val('');	
		$('#arquivo_texto').val('');
		$('#quantidade').val('');
		$('#intervalo_horas').val('1');
		$('#frequencia').val('0').change();
		

		$('#total_clientes').text("Alterar Opção de Teste: 0");	

		$('#titulo_disparar').text(titulo);

		$('#mensagem_disparar').html(formatarMensagemModal(msg));
		$('#mensagem2_disparar').html(formatarMensagemModal(msg2));

		$('#clientes').val('Teste').change();	

		$('#target_disparar').attr('src','images/marketing/' + arquivo);
		$('#audio_disparar').attr('src','images/marketing/' + audio);
		$('#target_documento_disparar').attr('src','images/marketing/' + documento);
		$('#target_arquivo_excel').attr('src','images/marketing/sem-foto.jpg');
		$('#target_arquivo_texto').attr('src','images/marketing/sem-foto.jpg');

		if(arquivo == 'sem-foto.png'){
			$('#target_disparar_div').hide();
		}else{
			$('#target_disparar_div').show();
		}

		if(audio == ''){
			$('#audio_disparar_div').hide();
		}else{
			$('#audio_disparar_div').show();
		}

		if(documento == 'sem-foto.png'){
			$('#target_documento_disparar_div').hide();
		}else{
			$('#target_documento_disparar_div').show();
		}

		$('#modalEntrada').modal('show');
	}
</script>




<script type="text/javascript">
	

	function excluirImagem(id){
    $.ajax({
        url: 'paginas/' + pag + "/excluir_imagem.php",
        method: 'POST',
        data: {id},
        dataType: "text",

        success: function (mensagem) {            
            if (mensagem.trim() == "Excluído com Sucesso") {                
				alertsucesso('Imagem excluída com sucesso!');        
                listar();                
            } else {
				alertError('Erro ao excluir imagem: ' + mensagem);
            }

        },      

    });
}

function excluirAudio(id){
    $.ajax({
        url: 'paginas/' + pag + "/excluir_audio.php",
        method: 'POST',
        data: {id},
        dataType: "text",

        success: function (mensagem) {    
            if (mensagem.trim() == "Excluído com Sucesso") {        
				alertsucesso('Áudio excluído com sucesso!');        
                listar();                
            } else {
				alertError('Erro ao excluir áudio: ' + mensagem);
            }

        },      

    });
}


function excluirDoc(id){
    $.ajax({
        url: 'paginas/' + pag + "/excluir_doc.php",
        method: 'POST',
        data: {id},
        dataType: "text",

        success: function (mensagem) {            
            if (mensagem.trim() == "Excluído com Sucesso") {  
				alertsucesso('Documento excluído com sucesso!');              
                listar();                
            } else {
					alertError('Erro ao excluir documento: ' + mensagem);
                }

        },      

    });
}



</script>
