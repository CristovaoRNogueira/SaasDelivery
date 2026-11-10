 <!-- Vincula um arquivo de manifesto que fornece informações sobre o aplicativo/web app, como ícones, nome do aplicativo e mais. É útil para quando o site é adicionado à tela inicial de um dispositivo.-->
<link rel="manifest" href="manifest.json">

<!--Define a cor do tema do navegador, neste caso, preto.-->
<meta name="theme-color" content="#000000">

<?php 
require_once("../conexao.php");
@session_start();

$mensagem_sessao = @$_SESSION['msg'];

######CRIAR UMA ADM CASO NÃO TENHA
$query = $pdo->query("SELECT * from usuarios where nivel = 'Administrador'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
$senha = '123';
$senha_crip = password_hash($senha, PASSWORD_DEFAULT);
if($linhas == 0){
    $pdo->query("INSERT INTO usuarios SET nome = '$nome_sistema', email = '$email_sistema', senha_crip = '$senha_crip', nivel = 'Administrador', ativo = 'Sim', foto = 'sem-foto.jpg', telefone = '$telefone_sistema', data = curDate() ");
}

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/png" href="../img/icone.png" sizes="32x32">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, minimal-ui">
   
    <title><?php echo $nome_sistema ?></title>
    <link rel="stylesheet" href="painel/css/swiper.css">
    <link rel="stylesheet" href="painel/style.css">
    <link rel="stylesheet" href="painel/css/novos_estilos.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,900" rel="stylesheet"> 
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    
    <!-- Permite que o site seja executado em tela cheia quando adicionado à tela inicial em dispositivos da Apple.-->
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <!-- Define a cor e a transparência da barra de status em dispositivos da Apple quando o site é executado em tela cheia.-->
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">    
    <!-- Define um ícone para quando o site é adicionado à tela inicial em dispositivos da Apple.-->
    <link rel="apple-touch-icon" sizes="128x128" href="../img/icone.png">

   
   

    <!-- importante para o PWA -->
</head>
<body id="mobile_wrap">
<div class="panel-overlay"></div>
<div id="splash_imagem" align="center" style="display:none"><img src="painel/images/loader_splash2.gif" width="70%"><br><img src="../img/logo.png" width="70%"></div>
<div id="pagina" style="display:none">
 <div class="content-block" >
            <div align="center" style="margin-bottom: 20px"><img src="../img/logo.png" width="150px" ></div>
            <div class="loginform">
                <form method="post" action="autenticar.php">
                    <input type="text" name="usuario" value="" id="usuario" class="form_input required" placeholder="Digite Seu Email" required="" />
                    <input type="password" name="senha" value="" class="form_input required" placeholder="Senha" required="" />

                    <div class="form_row contactform">                      
                    <input type="checkbox" value="Sim" hidden="hidden" id="salvar_acesso" name="salvar">
                    <label class="switch" for="salvar_acesso"></label><span class="label_switch"> Salvar Acesso</span>
                 </div>
                   
                    <input type="submit" name="submit" class="form_submit botao_salvar" value="LOGAR" />
                </form>
                <div class="signup_bottom">
                    <p>Recuperar Senha?</p>
                    <a href="#" data-popup=".popup-recuperar" class="open-popup">RECUPERAR</a>
                </div>
            </div>
          
        </div>


   
    <!-- Forgot Password Popup -->
    <div class="popup popup-recuperar">
        <div class="content-block">
            <h4 style="font-size:15px !important"><small>RECUPERAR SENHA</small></h4>
            <div class="loginform">
               <form method="post" id="form-recuperar">
                    <input type="email" name="email" id="email-recuperar" value="" class="form_input required" placeholder="Insira seu E-mail" required/>
                    <input type="submit" name="submit" class="form_submit botao_salvar" id="submitforgot" value="RESETAR SENHA" />
                </form>
                <div class="signup_bottom">
                    <p><small>Verifique seu email, ou seu whatsapp para redefinir a senha!</small></p>
                </div>
            </div>
            <div class="close_popup_button">
                <a href="#" class="close-popup " data-popup=".popup-recuperar"><img src="painel/images/icons/black/menu_close.png" alt="" title="" /></a>
            </div>
        </div>
    </div>
</div>	


<form action="autenticar.php" method="post" style="display:none">
    <input type="text" name="id" id="id_usua">
    <input type="text" name="pagina" id="pagina_salva">
    <button type="submit" id="btn_auto"></button>
</form>


<script src="painel/js/jquery-3.3.1.min.js"></script>
<script src="painel/js/jquery.validate.min.js" ></script>
<script src="painel/js/swiper.min.js"></script>
<script src="painel/js/jquery.custom.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</body>
</html>




<script type="text/javascript">
    $(document).ready(function() {        
        
        var email_usuario = localStorage.email_usu;        
        var senha_usuario = localStorage.senha_usu;
        var id_usuario = localStorage.id_usu;
        var pagina = localStorage.pagina;

        

        if(id_usuario != "" && id_usuario != undefined){
            $('#pagina').hide();
            $('#splash_imagem').show();
            $('#id_usua').val(id_usuario);
            $('#pagina_salva').val(pagina);
            $('#btn_auto').click();
        }else{
            $('#pagina').show();
            $('#splash_imagem').hide();
            var mensagem_sessao = "<?=$mensagem_sessao?>";
            if(mensagem_sessao != ""){
                toast(mensagem_sessao, "#eb5244")
            }
        }

        if(email_usuario != "" && email_usuario != undefined){
            $('#salvar_acesso').prop('checked', true);
        }else{
            $('#salvar_acesso').prop('checked', false);


        }

        $('#usuario').val(email_usuario);
        $('#password-field').val(senha_usuario);


        

    });
</script>

 

<script type="text/javascript">
    $("#form-recuperar").submit(function () {

        $('#mensagem-recuperar').text('Enviando!!');
        $('#submitforgot').hide();


        event.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: "../recuperar-senha.php",
            type: 'POST',
            data: formData,

            success: function (mensagem) {
                $('#mensagem-recuperar').text('');
                $('#mensagem-recuperar').removeClass()
                if (mensagem.trim() == "Recuperado com Sucesso") {
                                    
                    $('#email-recuperar').val('');
                    $('#mensagem-recuperar').addClass('text-success')
                    toast('Link para troca de senha no Email ou no seu whatsapp!', 'verde');                       

                } else {

                    $('#mensagem-recuperar').addClass('text-danger')
                    toast(mensagem, 'vermelha'); 
                }

                $('#submitforgot').show();


            },

            cache: false,
            contentType: false,
            processData: false,

        });

    });
</script>


<script type="text/javascript">
    function toast(mensagem, cor){ 
        if(cor == 'verde'){
            cor = '#24c76a';
        } 

        if(cor == 'vermelha'){
            cor = '#d4483b';
        }

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

<!-- Base Js File -->
<script src="service-worker.js"></script>
<script src="painel/js/base.js"></script>