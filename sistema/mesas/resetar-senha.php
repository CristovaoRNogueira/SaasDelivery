<?php 
require_once("../conexao.php");

if( !isset($_REQUEST['email']) || !isset($_REQUEST['token']) ) {
    header('location: '.$url_sistema);
    exit;
}

$statement = $pdo->prepare("SELECT * FROM usuarios WHERE email=? AND token=?");
$statement->execute([@$_REQUEST['email'],@$_REQUEST['token']]);
$result = $statement->fetchAll();
$tot = $statement->rowCount();
if($tot == 0) {
    header('location: '.$url_sistema);
    exit;
}

$_SESSION['temp_reset_email'] = @$_REQUEST['email'];
$_SESSION['temp_reset_token'] = @$_REQUEST['token'];



?>
 <!DOCTYPE html>
<html>
<head>
	<title><?php echo $nome_sistema ?></title>
	
	 <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="apple-touch-icon" href="painel/images/apple-touch-icon.png" />
    <link rel="apple-touch-startup-image" href="painel/images/apple-touch-startup-image-640x920.png">
    <title>APP Modelo</title>
    <link rel="stylesheet" href="painel/css/swiper.css">
    <link rel="stylesheet" href="painel/style.css">
    <link rel="stylesheet" href="painel/css/novos_estilos.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,900" rel="stylesheet"> 
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>


<body>
	 <div class="loginform">		
		<div class="form">
			<div align="center"><img src="painel/images/logo.png" class="imagem" style="width:250px; margin-top:50px"></div>
			 <br>
       <small><div id="mensagem-recuperar" align="center"></div></small>
       <br>
			<form method="post" id="form-recuperar">

				 <input type="password" name="senha" value="" id="senha" class="form_input required" placeholder="Digite uma nova senha" required="" />

				  <input type="password" name="re_senha" value="" id="re_senha" class="form_input required" placeholder="Repetir senha" required="" />

    
       	<input type="hidden" name="token" id="token" value="">

       	<input type="hidden" name="email" id="email" value="<?php echo @$_REQUEST['email'] ?>">

				 <input type="submit" class="form_submit botao_salvar" value="ALTERAR SENHA" />


			</form>	
		</div>
	</div>

</body>
</html>




<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>


 <script type="text/javascript">
	$("#form-recuperar").submit(function () {

		$('#mensagem-recuperar').text("Alterando!!")

		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: "../alterar-senha.php",
			type: 'POST',
			data: formData,

			success: function (mensagem) {
				$('#mensagem-recuperar').text('');
				$('#mensagem-recuperar').removeClass()
				if (mensagem.trim() == "Senha alterada com Sucesso") {
					//$('#btn-fechar-rec').click();					
					$('#senha').val('');
					$('#re_senha').val('');
					toast('Sua Senha foi alterada com Sucesso!!', '#24c76a');
					window.location="index.php";		

				} else {

					$('#mensagem-recuperar').addClass('text-danger')
					toast(mensagem, '#d4483b');
					
				}


			},

			cache: false,
			contentType: false,
			processData: false,

		});

	});
</script>




<script src="painel/js/jquery-3.3.1.min.js"></script>
<script src="painel/js/jquery.validate.min.js" ></script>
<script src="painel/js/swiper.min.js"></script>
<script src="painel/js/jquery.custom.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>


<script type="text/javascript">
    function toast(mensagem, cor){        
        Toastify({
          text: mensagem,
          duration: 3000,
          destination: "https://github.com/apvarun/toastify-js",
          newWindow: true,
          close: true,
          gravity: "top", // `top` or `bottom`
          position: "center", // `left`, `center` or `right`
          stopOnFocus: true, // Prevents dismissing of toast on hover
          style: {
            background: cor, //verde #24c76a    vermelha #d4483b
          },
          onClick: function(){} // Callback after click
        }).showToast();
    }
</script>