<?php
require_once("../../conexao.php");
@session_start();
$pag_inicial = 'home';
$data_page = 'index';

if (@$_SESSION['aut_token_9yxA'] != 'xnBxfyXZbx9yxA') {
	echo "<script>localStorage.setItem('id_usu', '')</script>";
	unset($_SESSION['id'], $_SESSION['nome'], $_SESSION['nivel']);
	$_SESSION['msg'] = "";
	echo '<script>window.location="../"</script>';
	exit();
}

if (@$_SESSION['nivel'] != 'Administrador') {
	require_once("verificar_permissoes.php");
}

$buscar = @$_POST['buscar'];
$dataInicial = @$_POST['dataInicial'];
$dataFinal = @$_POST['dataFinal'];
$pago = @$_POST['pago'];

$data_hoje = date('Y-m-d');
$data_atual = date('Y-m-d');
$mes_atual = Date('m');
$ano_atual = Date('Y');
$data_inicio_mes = $ano_atual . "-" . $mes_atual . "-01";
$data_inicio_ano = $ano_atual . "-01-01";

$data_ontem = date('Y-m-d', strtotime("-1 days", strtotime($data_atual)));
$data_amanha = date('Y-m-d', strtotime("+1 days", strtotime($data_atual)));


if ($mes_atual == '04' || $mes_atual == '06' || $mes_atual == '09' || $mes_atual == '11') {
	$data_final_mes = $ano_atual . '-' . $mes_atual . '-30';
} else if ($mes_atual == '02') {
	$bissexto = date('L', @mktime(0, 0, 0, 1, 1, $ano_atual));
	if ($bissexto == 1) {
		$data_final_mes = $ano_atual . '-' . $mes_atual . '-29';
	} else {
		$data_final_mes = $ano_atual . '-' . $mes_atual . '-28';
	}
} else {
	$data_final_mes = $ano_atual . '-' . $mes_atual . '-31';
}


if (@$_GET['pagina'] != "") {
	$pagina = @$_GET['pagina'];

	if ($pagina != "" and $pagina != "index" and $pagina != "home") {
		$data_page = 'blog';
	}
} else {
	$pagina = $pag_inicial;
}


$id_usuario = @$_SESSION['id'];
$query = $pdo->query("SELECT * from usuarios where id = '$id_usuario'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	$nome_usuario = $res[0]['nome'];
	$email_usuario = $res[0]['email'];
	$telefone_usuario = $res[0]['telefone'];
	$senha_usuario = $res[0]['senha'];
	$nivel_usuario = $res[0]['nivel'];
	$foto_usuario = $res[0]['foto'];
	$endereco_usuario = $res[0]['endereco'];
} else {
	echo '<script>window.location="../"</script>';
	exit();
}

require_once("cabecalho.php");




?>



<?php
echo "<script>localStorage.setItem('pagina', '$pagina')</script>";
require_once("paginas/" . $pagina . ".php");
?>



<div class="popup popup-perfil">
	<div class="content-block" style="padding:5px !important">
		<h2 class="page_title">EDITAR DADOS</h2>
		<div class="page_single layout_fullwidth_padding">
			<div class="contactform">
				<form id="form-perfil" method="post">

					<div class="form_row">
						<label class="labels_form">Nome</label>
						<input type="text" name="nome" value="<?php echo $nome_usuario ?>" class="form_input" />
					</div>

					<div class="form_row">
						<label class="labels_form">Email</label>
						<input type="text" name="email" value="<?php echo $email_usuario ?>" class="form_input" />
					</div>

					<div class="form_row">
						<label class="labels_form">Telefone</label>
						<input type="text" name="telefone" value="<?php echo $telefone_usuario ?>" class="form_input" />
					</div>

					<div style="width:48%; float:left">
						<label class="labels_form">Senha</label>
						<input type="password" name="senha" value="" class="form_input" style="height:25px;" required="" />
					</div>

					<div style="width:48%; float:right">
						<label class="labels_form">Confirmar Senha</label>
						<input type="password" name="conf_senha" value="" class="form_input" style="height:25px;" required="" />
					</div>


					<div class="form_row" align="center" onclick="foto_perfil.click()">
						<img src="../../painel/images/perfil/<?php echo $foto_usuario ?>" width="100px" id="target_perfil"><br>
						<img src="images/icone-arquivo.png" width="100px" style="margin-top: -12px">
					</div>

					<input onChange="carregarImgPerfil();" type="file" name="foto" id="foto_perfil" style="display:none">

					<input type="submit" name="btn_salvar" class="form_submit botao_salvar" id="btn_salvar_perfil" value="SALVAR" />

					<input type="hidden" name="id_usuario" value="<?php echo $id_usuario ?>">

				</form>
			</div>
		</div>
	</div>
	<div class="close_popup_button">
		<a href="#" class="close-popup" data-popup=".popup-perfil"><img src="images/icons/black/menu_close.png" alt="" title="" /></a>
	</div>
</div>


<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/jquery.validate.min.js"></script>
<script src="js/swiper.min.js"></script>
<script src="js/jquery.custom.js"></script>
<script src="js/ajax.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<!-- Mascaras JS -->
<script type="text/javascript" src="js/mascaras.js"></script>

<!-- Ajax para funcionar Mascaras JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>


</body>

</html>

<script>
	setTimeout(function() {
		listarCarrinho();
	}, 10000)
</script>

<script type="text/javascript">
	$(document).ready(function() {
		$('#buscar').focus();
		$('.sel2').select2({
			dropdownParent: $('#popupForm')
		});

		$('.sel3').select2({

		});

		$('.sel_pagina').select2({
			dropdownParent: $('#pages_maincontent')
		});

	});
</script>

<style type="text/css">
	.select2-selection__rendered {
		line-height: 40px !important;
		font-size: 16px !important;
		color: #666666 !important;
	}

	.select2-selection {
		height: 40px !important;
		font-size: 16px !important;
		color: #666666 !important;
	}
</style>





<script type="text/javascript">
	function carregarImgPerfil() {
		var target = document.getElementById('target_perfil');
		var file = document.querySelector("#foto_perfil").files[0];

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
	$("#form-perfil").submit(function() {

		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: "../../painel/editar-perfil.php",
			type: 'POST',
			data: formData,

			success: function(mensagem) {
				$('#msg-perfil').text('');
				$('#msg-perfil').removeClass()
				if (mensagem.trim() == "Editado com Sucesso") {
					toast(mensagem, 'verde')
					$('#btn-fechar-perfil').click();
					location.reload();


				} else {

					$('#msg-perfil').addClass('text-danger')
					toast(mensagem, 'vermelha')
				}


			},

			cache: false,
			contentType: false,
			processData: false,

		});

	});
</script>