<?php
@session_start();
require_once ("conexao.php");



################# CRIAR UM ADM CASO NÃO TENHA NENHUM ############################################
$query = $pdo->query("SELECT * from usuarios WHERE nivel = 'Administrador' and ativo = 'Sim' and empresa = 0");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);

$senha = '123';
$senha_crip = password_hash($senha, PASSWORD_DEFAULT);

if ($linhas == 0) {
	$pdo->query("INSERT INTO usuarios SET nome = 'Administrador', email = '$email_sistema', cpf = '000.000.000-00', senha = '', senha_crip = '$senha_crip', nivel = 'Administrador', ativo = 'Sim', foto = 'sem-foto.jpg', telefone = '$telefone_sistema', data = curDate(), empresa = 0");
}


//VERIFICAR SE EXISTE CARGO ADMIN CADASTRADO
$query = $pdo->query("SELECT * FROM cargos WHERE nome = 'Administrador'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg == 0) {
	//CRIAR UM USUÁRIO ADMFIN
	$pdo->query("INSERT INTO cargos SET nome = 'Administrador'");
}


//VERIFICAR SE EXISTE CARGO ENTREGADOR CADASTRADO
$query = $pdo->query("SELECT * FROM cargos WHERE nome = 'Entregador'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg == 0) {
	//CRIAR UM USUÁRIO ADMIN
	$pdo->query("INSERT INTO cargos SET nome = 'Entregador'");
}


//VERIFICAR SE EXISTE CARGO GARCON CADASTRADO
$query = $pdo->query("SELECT * FROM cargos WHERE nome = 'Garçon'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg == 0) {
	//CRIAR UM USUÁRIO ADMIN
	$pdo->query("INSERT INTO cargos SET nome = 'Garçon'");
}

//APAGAR OS REGISTROS DOS DIAS ANTERIORES A 30 DIAS
$data_atual = date('Y-m-d');
$data_30_ant = date('Y-m-d', strtotime("-$dias_apagar days", strtotime($data_atual)));
$query = $pdo->query("SELECT * FROM temp WHERE data < '$data_30_ant'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {
	for ($i = 0; $i < $total_reg; $i++) {
		foreach ($res[$i] as $key => $value) {
		}
		$id = $res[$i]['id'];
		$pdo->query("DELETE FROM temp WHERE id = '$id'");
	}
}


$query = $pdo->query("SELECT * FROM carrinho WHERE data < '$data_30_ant'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {
	for ($i = 0; $i < $total_reg; $i++) {
		foreach ($res[$i] as $key => $value) {
		}
		$id = $res[$i]['id'];
		$pdo->query("DELETE FROM carrinho WHERE id = '$id'");
	}
}


$emoji = json_decode('"\ud83d\udd14"');


### MENSAGEM DE SALDAÇÃO###################################################################################
$hora = date('H');

if ($hora < 12 && $hora >= 6)
	$saudacao = "Bom Dia";

if ($hora >= 12 && $hora < 18)
	$saudacao = "Boa Tarde";

if ($hora >= 18 && $hora <= 23)
	$saudacao = "Boa Noite";
if ($hora < 6 && $hora >= 0)
	$saudacao = "Boa madrugada";


//PERCORRER AS CONTAS PARA GERAR AS MENSAGEM PARA ADM #############################################################

if (@$mensagem_auto == 'Sim' and strtotime($data_cobranca) != strtotime($data_atual) and $api_whatsapp != 'Não') {

	$query = $pdo->query("SELECT * from pagar where vencimento <= curDate() and pago = 'Não' order by id asc ");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if ($total_reg > 0) {
		for ($i = 0; $i < $total_reg; $i++) {
			$descricao = $res[$i]['descricao'];
			$valor = $res[$i]['valor'];
			$data_venc = $res[$i]['vencimento'];

			$data_vencF = implode('/', array_reverse(explode('-', $data_venc)));
			$valorF = number_format($valor, 2, ',', '.');

			$query2 = $pdo->query("SELECT * FROM usuarios where nivel = 'Administrador'");
			$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
			if (@count($res2) > 0) {
				$nome = $res2[0]['nome'];
				$telefone = $res2[0]['telefone'];
			}

			$primeiroNome = explode(" ", $nome);

			$mensagem_whatsapp = $emoji . ' *Lembrete Automático de Vencimento!* %0A%0A';

			$mensagem_whatsapp .= $saudacao . ' *' . $primeiroNome[0] . '*%0A%0A';

			if (strtotime($data_venc) == strtotime($data_atual)) {
				$mensagem_whatsapp .= '_Você tem uma Conta Vencendo Hoje_ %0A%0A';
			} else {
				$mensagem_whatsapp .= '_Você tem uma Conta Vencida_ %0A%0A';
			}


			//api whats	###########################################################################
			$telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone);

			$mensagem_whatsapp .= 'Empresa: *' . $nome_sistema . '* %0A';
			$mensagem_whatsapp .= 'Descrição: *' . $descricao . '* %0A';
			$mensagem_whatsapp .= 'Valor: *R$ ' . $valorF . '* %0A';
			$mensagem_whatsapp .= 'Data de Vencimento: *' . $data_vencF . '* %0A%0A';

			$mensagem_whatsapp .= '_Favor efetuar o pagamento!_ %0A';

			require ('painel/apis/texto.php');
		}
	}

	$pdo->query("UPDATE config SET data_cobranca = curDate()");
}


?>



<!DOCTYPE html>
<html lang="pt-BR">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
	<!-- META DATA -->
	<meta charset="UTF-8">
	<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="Description" content="Sistema para Delivery Interativo, sistemas para delivery, delivery interativo, sistemas Hugo Vasconcelos, sistemas hugocursos, sistemas hugo">
	<meta name="Author" content="Hugo Vasconcelos do Portal HugoCursos">
	<meta name="Keywords" content="sistema para delivery, sistemas hugo vasconcelos, sistemas hugo, portal hugocursos" />

	<!-- TITLE -->
	<title><?php echo $nome_sistema ?></title>


	<link rel="icon" href="img/<?php echo $icone_sistema ?>" type="image/x-icon" />
	<link href="assets/css/icons.css" rel="stylesheet">
	<link id="style" href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link href="assets/css/style.css" rel="stylesheet">
	<link href="assets/css/custom.css" rel="stylesheet">
	<link href="assets/css/style-dark.css" rel="stylesheet">
	<link href="assets/css/style-transparent.css" rel="stylesheet">
	<link href="assets/css/skin-modes.css" rel="stylesheet" />
	<link href="assets/css/animate.css" rel="stylesheet">




</head>


<!-- GLOBAL-LOADER -->
<div id="global-loader">
	<img src="img/loader.gif" class="loader-img loader loader_mobile" alt="">
</div>
<!-- /GLOBAL-LOADER -->

<?php 
if($fundo_login != "" and $fundo_login != "sem-foto.png"){ ?>
<body class="ltr error-page1 bg-primary" id="pagina" style="background: url('img/<?php echo $fundo_login ?>') no-repeat center center fixed; background-size: cover;">
<?php }else{ ?>
<body class="ltr error-page1 bg-primary" id="pagina" >
<?php } ?>


	<div class="square-box">
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
	</div>


	<div class="page">


		<div class="page-single">
			<div class="container">
				<div class="row">
					<div
						class="col-xl-5 col-lg-6 col-md-8 col-sm-8 col-xs-10 card-sigin-main mx-auto my-auto py-4 justify-content-center">
						<div class="card-sigin">
							<!-- Demo content-->
							<div class="main-card-signin d-md-flex">
								<div class="wd-100p">
									<div align="center" class="d-flex mb-4 justify-content-center"><a href="index.php"><img
												src="img/logo.png" class="sign-favicon" alt="logo" width="50%"></a></div>
									<div class="">
										<div class="main-signup-header">

											<div class="panel panel-primary">

												<div class="panel-body tabs-menu-body border-0 p-3">

													<?php
													if (isset($_SESSION['msg'])) {

														echo '<div class="alert alert-danger mg-b-0 mb-3 alert-dismissible fade show" role="alert">
											<strong><span class="alert-inner--icon"><i class="fe fe-slash"></i></span></strong> ' . $_SESSION['msg'] . '!
											<button aria-label="Close" class="btn-close" data-bs-dismiss="alert" type="button"><span aria-hidden="true">&times;</span></button>
										</div>';

														unset($_SESSION['msg']);
													}
													?>




													<form method="post" action="autenticar.php">
														<label>Usuário</label>
														<div class="input-group mb-4">
															<div class="input-group-text">
																<i class="fe fe-at-sign"></i>
															</div>
															<input type="text" class="form-control" name="usuario" id="usuario"
																placeholder="Digite o Usuário" required="">
														</div>
														<div class="form-group">
															<label class="control-label">Senha</label>
															<div class="input-group mb-4">
																<div class="input-group-text">
																	<span toggle="#password-field" class="fa fa-fw fa-eye  toggle-password"></span>
																</div>
																<input id="password-field" type="password" class="form-control" name="senha"
																	placeholder="Digite sua Senha" required value="">
															</div>
														</div>

														<div class="form-group" style="margin-left: 22px">
															<span><input class="form-check-input" type="checkbox" value="Sim" name="salvar"
																	id="salvar_acesso"></span>
															<span class="control-label" style="margin-top:5px">Salvar Acesso</span>
														</div>

														<button class="btn btn-primary btn-block">Entrar no Sistema</button>

													</form>

												</div>
											</div>

											<div class="main-signin-footer text-center mt-3">

												<p><a href="" class="mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal">Esqueceu sua
														Senha?</a></p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>





</body>

</html>




<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title" id="exampleModalLabel">Recuperar Senha</h5>
				<button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span aria-hidden="true"
						class="text-white">&times;</span></button>

			</div>
			<form method="post" id="form-recuperar">
				<div class="modal-body">
					<label for="recipient-name" class="col-form-label">Email:</label>
					<input placeholder="Digite seu Email" class="form-control" type="email" name="email" id="email-recuperar"
						required>

					<br>
					<small>
						<div id="mensagem-recuperar" align="center"></div>
					</small>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
					<button type="submit" class="btn btn-primary">Recuperar Senha</button>
				</div>
			</form>
		</div>
	</div>
</div>


<form action="autenticar.php" method="post" style="display:none">
	<input type="text" name="id" id="id_usua">
	<input type="text" name="pagina" id="pagina_salva">
	<button type="submit" id="btn_auto"></button>
</form>


<script src="assets/plugins/jquery/jquery.min.js"></script>
<script src="assets/plugins/bootstrap/js/popper.min.js"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/plugins/moment/moment.js"></script>
<script src="assets/js/eva-icons.min.js"></script>
<script src="assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="assets/js/themecolor.js"></script>
<script src="assets/js/custom.js"></script>


<script type="text/javascript">
	$(document).ready(function () {

		var modo_teste = "<?= $modo_teste ?>";
		if(modo_teste == 'Sim'){
			setTimeout(function() {
	 	    $('#usuario').val('contato@hugocursos.com.br');
			$('#password-field').val('123');
	        }, 1000)
		}

		var email_usuario = localStorage.email_usu;
		var senha_usuario = localStorage.senha_usu;
		var id_usuario = localStorage.id_usu;
		var pagina = localStorage.pagina;

		var redirecionar = "<?= $entrar_automatico ?>";

		if (id_usuario != "" && id_usuario != undefined && redirecionar == 'Sim') {
			$('#pagina').hide();
			$('#id_usua').val(id_usuario);
			$('#pagina_salva').val(pagina);
			$('#btn_auto').click();
		} else {
			$('#pagina').show();
		}

		if (email_usuario != "" && email_usuario != undefined) {
			$('#salvar_acesso').prop('checked', true);
		} else {
			$('#salvar_acesso').prop('checked', false);
		}

		$('#usuario').val(email_usuario);
		$('#password-field').val(senha_usuario);

	});
</script>


<script>
	$(".toggle-password").click(function () {

		$(this).toggleClass("fa-eye fa-eye-slash");
		var input = $($(this).attr("toggle"));
		if (input.attr("type") == "password") {
			input.attr("type", "text");
		} else {
			input.attr("type", "password");
		}
	});
</script>




<script type="text/javascript">
	$("#form-recuperar").submit(function () {

		$('#mensagem-recuperar').text('Enviando!!');

		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: "recuperar-senha.php",
			type: 'POST',
			data: formData,

			success: function (mensagem) {
				$('#mensagem-recuperar').text('');
				$('#mensagem-recuperar').removeClass()
				if (mensagem.trim() == "Recuperado com Sucesso") {

					$('#email-recuperar').val('');
					$('#mensagem-recuperar').addClass('text-success')
					$('#mensagem-recuperar').text('Sua Senha foi enviada para o Email e o Whatsapp')

				} else {

					$('#mensagem-recuperar').addClass('text-danger')
					$('#mensagem-recuperar').text(mensagem)
				}


			},

			cache: false,
			contentType: false,
			processData: false,

		});

	});
</script>
