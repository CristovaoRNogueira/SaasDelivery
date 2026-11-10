<?php 
$id_empresa = 0;
$pag = 'dispositivos';

if(@$dispositivos == 'ocultar')
{
    echo "<script>window.location='../index.php'</script>";
    exit();
}

require_once("../conexao.php");

$query = $pdo->prepare("SELECT * FROM dispositivos WHERE status_api IS NOT NULL and empresa = '$id_empresa'");
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);

$total_dispositivos = @count($res);
$conectados = 0;
$desconectados = 0;

foreach($res as $dispositivo)
{
    if($dispositivo['status_api'] == 'conectado'){
        $conectados++;
    }else{
        $desconectados++;
    }
}



?>
 
<br>
<div class="row">

    <div class="col-xl-4 col-lg-12 col-md-6 col-xs-12">
        <div class="card sales-card"
            style="box-shadow: 2px 2px 0px 0px rgba(0, 0, 0, 0.1); height:130px; border-radius: 10px;">
            <div class="row">
                <div class="col-8">
                    <div class="ps-4 pt-4 pe-3 pb-4">
                        <div>
                            <p class="mb-1">Total Dispositivos</p>
                        </div>
                        <div class="pb-0 mt-0">
                            <div class="d-flex">
                                <h4 class="tx-20 font-weight-semibold mb-2"><?= $total_dispositivos; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="zoom circle-icon iconBgRoxo text-center align-self-center overflow-hidden">
                        <i class="fe fe-smartphone tx-16 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-12 col-md-6 col-xs-12">
        <div class="card sales-card"
            style="box-shadow: 2px 2px 0px 0px rgba(0, 0, 0, 0.1); height:130px; border-radius: 10px">
            <div class="row">
                <div class="col-8">
                    <div class="ps-4 pt-4 pe-3 pb-4">
                        <div>
                            <p class="mb-1">Conectados</p>
                        </div>
                        <div class="pb-0 mt-0">
                            <div class="d-flex">
                                <h4 class="tx-20 font-weight-semibold mb-2"><?= $conectados; ?> / <?= $total_dispositivos; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="zoom circle-icon iconBgSusses text-center align-self-center overflow-hidden">
                        <i class="bi bi-wifi tx-16 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-12 col-md-6 col-xs-12">
        <div class="card sales-card"
            style="box-shadow: 2px 2px 0px 0px rgba(0, 0, 0, 0.1); height:130px; border-radius: 10px">
            <div class="row">
                <div class="col-8">
                    <div class="ps-4 pt-4 pe-3 pb-4">
                        <div>
                            <p class="mb-1">Desconectados</p>
                        </div>
                        <div class="pb-0 mt-0">
                            <div class="d-flex">
                                <h4 class="tx-20 font-weight-semibold mb-2"><?= $desconectados; ?> / <?= $total_dispositivos; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="zoom circle-icon iconBgDanger text-center align-self-center overflow-hidden">
                        <i class="bi bi-wifi-off tx-16 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
  
</div>
 

<div class="main-page margin-mobile" style="margin-top: 10px">
    <div class="row">
        <div class="col-md-4">
            <button type="button" class="btn btn-primary" onclick="add()">
                <span class="fa fa-plus"></span> Adicionar Dispositivos
            </button>
        </div>
    </div>
</div>


<div class="bs-example widget-shadow" style="padding:15px; margin-top:0px" id="listar"></div>
<input type="hidden" id="ids">

<div class="modal fade" id="modaldispositivo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title" id="exampleModalLabel">Realize a Leitura do QRCode</h4>
                <button id="btn-fechar" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
                        class="text-white" aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body text-center">
                <div id="qrCodeContainer" style="display: none;"></div>
                <div id="statusMessage" style="display: none;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">var pag = "<?=$pag?>"</script>
<script src="js/ajax.js"></script>

<script type="text/javascript">


var timeoutQrCode = null;
var usuarioFechouQRCode = false;

$('#modaldispositivo').on('hidden.bs.modal', function () {
    usuarioFechouQRCode = true;

    if(timeoutQrCode){
        clearTimeout(timeoutQrCode);
        timeoutQrCode = null;
    }
});


function add(appkey = '') {

    usuarioFechouQRCode = false;

    if(timeoutQrCode){
        clearTimeout(timeoutQrCode);
        timeoutQrCode = null;
    }

    var authkey = '<?= $instancia_whatsapp; ?>';

    if(authkey == 'v9t7zpp51nsSCMeqqJWfI4lj8iGG12tyMqW8PwvBH3CojiUaHMs' || authkey == ''){
        alertWarning('Insira nas configurações a sua Instancia / Authkey da Menuia!');
        return;
    }

    var total_dispositivos = '<?= $total_dispositivos; ?>';
    

    $('#qrCodeContainer').html('').hide();
    $('#statusMessage').html('Carregando QR Code, aguarde...').show();
    $('#modaldispositivo').modal('show');

    $.ajax({
        url: 'paginas/' + pag + '/appkey.php',
        method: 'POST',
        data: { appkey: appkey },
        dataType: 'json',
        success: function(resposta) {

            if (resposta.status == 200) {

                checar_conexao(resposta.appkey, function(status) {

                    if (usuarioFechouQRCode) {
                        return;
                    }

                    if (status == 501 || status == 404) {

                        $('#statusMessage').html('Aguardando leitura do QR Code...').show();

                        qrCode(resposta.appkey);

                        timeoutQrCode = setTimeout(function() {
                            if(!usuarioFechouQRCode && $('#modaldispositivo').hasClass('show')){
                                add(resposta.appkey);
                            }
                        }, 10000);

                    } else if (status == 200) {

                        var qrImage = $('<img>')
                            .attr('src', 'https://chatbot.menuia.com/uploads/connected.png')
                            .css('max-width', '100%');

                        $('#qrCodeContainer').html(qrImage).show();

                        $.ajax({
                            url: 'paginas/' + pag + '/atualizar.php',
                            method: 'POST',
                            data: { appkey: resposta.appkey },
                            dataType: 'json',
                            success: function(resultado) {

                                if (resultado.status == 200) {
                                    $('#statusMessage').html('Dispositivo Conectado!').show();

                                    setTimeout(function() {
                                        location.reload();
                                    }, 3000);

                                } else {
                                    $('#statusMessage').html('Dispositivo Conectado, porém não conseguiu salvar no banco de dados!').show();
                                }
                            },
                            error: function() {
                                $('#statusMessage').html('Erro ao salvar conexão no banco de dados.').show();
                            }
                        });
                    } else {
                        $('#statusMessage').html('Não foi possível verificar o dispositivo. Tente novamente.').show();
                    }
                });

            } else if(resposta.status == 404) {

                $('#statusMessage').html('Appkey inválida, gere um novo dispositivo!').show();

            } else {

                $('#statusMessage').html('Erro ao buscar appkey.').show();
            }
        },
        error: function() {
            $('#statusMessage').html('Erro na requisição AJAX.').show();
        }
    });
}


function checar_conexao(appkey, callback) {
    var authkey = '<?= $instancia_whatsapp; ?>';

    $.ajax({
        url: 'https://chatbot.menuia.com/api/developer',
        type: 'POST',
        data: {
            authkey: authkey,
            message: appkey,
            licence: 'hugocursos',
            checkDispositivo: 'true'
        },
        success: function(response) {
            $('#loadingIndicator').hide();

            if (response.status === 200) {

                $.ajax({
                    url: 'paginas/' + pag + '/atualizar_numero.php',
                    method: 'POST',
                    data: { 
                        appkey: appkey, 
                        dados: response.dados
                    },
                    dataType: 'json',
                    success: function(resultado) {
                        callback(200);
                    },
                    error: function() {
                        console.log('Ocorreu um erro interno ao tentar salvar o número no banco de dados');
                        callback(200);
                    }
                });

            } else if (response.status === 404 || response.status === 403) {

                callback(404);

            } else {

                callback(500);

            }
        },
        error: function(error) {
            callback(501);
        }
    });
}


function qrCode(appkey)
{
     var authkey = '<?= $instancia_whatsapp; ?>';
    $.ajax({
        url: 'https://chatbot.menuia.com/api/developer', //Gerando um QRCODE
        type: 'POST',
        data: {
            authkey: authkey,
            message: appkey,
            licence: 'hugocursos',
            conecteQR: 'true',
            webhook:  '<?= $url_sistema; ?>webhook/menuia.php'
        },
        success: function (response) 
        {
            if (response.status === 200) 
            {
                var qrCode = response.message.qr;
                var qrImage = $('<img>').attr('src', qrCode).css('max-width', '100%');
               $('#qrCodeContainer').html(qrImage).show();
            } 
            else 
            {
                console.log(response);
                $('#statusMessage').html('Erro ao carregar o QR code.').show();
            }
        },
        error: function (error) 
        {
            if(error.status === 403)
            {
                $('#loadingIndicator').hide();
                var qrImage = $('<img>').attr('src', 'https://chatbot.menuia.com/public/license.jpg').css('max-width', '70%');
                $('#qrCodeContainer').html(qrImage).show();
                $('#statusMessage').html('Sua licença se encontra ativa em outro dispositivo, faça upgrade ou entre contato com a Menuia para mais informações!').show();
                 return;
            }
            else
            {
                var falha = error.responseJSON.message ?? 'Ocorreu um erro desconhecido';
                $('#loadingIndicator').hide();
                $('#statusMessage').html(falha).show();
                
            }
           
        }
        });  
}
</script>



<script type="text/javascript">
    

// ALERT EXCLUIR #######################################
function excluirDisp(id) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success", // Adiciona margem à direita do botão "Sim, Excluir!"
            cancelButton: "btn btn-danger me-1"
        },
        buttonsStyling: false
    });

    swalWithBootstrapButtons.fire({
        title: "Deseja Excluir?",
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
                url: 'paginas/' + pag + "/excluir.php",
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
                        });
                        location.reload();
                    } else {
                        // Exibe mensagem de erro se a requisição falhar
                        swalWithBootstrapButtons.fire({
                            title: "Opss!",
                            text: mensagem,
                            icon: "error",
                            confirmButtonText: 'OK',
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


    function verificarStatusDispositivos(){
    $.ajax({
        url: 'paginas/' + pag + '/verificar_status.php',
        method: 'POST',
        dataType: 'html',
        success: function(){
            listar();
        }
    });
}

verificarStatusDispositivos();

setInterval(function(){
    verificarStatusDispositivos();
}, 60000);
</script>