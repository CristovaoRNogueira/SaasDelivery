<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
//error_reporting(E_ALL);



include("./config.php");
require("../../conexao.php");



$id_conta = filter_var($_GET['id_conta'], @FILTER_SANITIZE_STRING);

$query5 = $pdo->query("SELECT * from config where empresa = '' or empresa = 0");
$res5 = $query5->fetchAll(PDO::FETCH_ASSOC);
$api_pagamento = $res5[0]['api_pagamento'];
if($api_pagamento == "Asaas"){
    echo '<script>window.location="'.$url_sistema.'pagamento_plano_asaas/'.$id_conta.'"</script>';  
}

$query = $pdo->prepare("SELECT * FROM receber_sas where id = :id_conta");
$query->bindValue(":id_conta", "$id_conta");
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
$cliente = $res[0]['cliente'];
$descricao = $res[0]['descricao'];
$valor = $res[0]['valor'];
$ref_pix = $res[0]['ref_pix'];
$data = $res[0]['data_lanc'];
$hora = $res[0]['hora'];
$dataF = implode('/', array_reverse(explode('-', $data)));
$horaF = date("H:i", strtotime($hora));
$vencimento = $res[0]['vencimento'];
$pago = $res[0]['pago'];

//CODIGO PARA CALCULAR OS JUROS
$valor_multa = 0;
$valor_juros = 0;
$data_hoje = date('Y-m-d');

if (@strtotime($vencimento) < @strtotime($data_hoje)) {
    
    $valor_multa = $multa_atraso;

            //pegar a quantidade de dias que o pagamento está atrasado
    $dif = @strtotime($data_hoje) - @strtotime($vencimento);
    $dias_vencidos = floor($dif / (60 * 60 * 24));

    $valor_juros = ($valor * $juros_atraso / 100) * $dias_vencidos;
}

$valor = $valor_multa + $valor_juros + $valor;
$valor_multa_juros = $valor_multa + $valor_juros;

$valor_jurosF = @number_format($valor_juros, 2, ',', '.');



$valorF = number_format($valor, 2, ',', '.');


if($pago == 'Sim'){
    echo '<div style="text-align: center; margin-top: 100px"> <img src="../img/conta_paga.png" class="imgsistema_mobile"></div>';
    exit();
}


if($ref_pix != ""){
     require('consultar_pagamento.php');
     if($status_api == 'approved'){
         echo 'Essa conta Já foi Paga';  
         exit();  
        }
}



$query = $pdo->query("SELECT * FROM empresas where id = '$cliente'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$nome_cliente =  $res[0]['nome'];
$cpf_cliente =  '450.417.700-50';
$email_cliente =  $res[0]['email'];
if($email_cliente == ""){
    $email_cliente = 'cliente@hotmail.com';
}

$token_valor = ($valor!="")? sha1($valor) : "";
$doc = $cpf_cliente;
$doc =  str_replace(array(",", ".", "-", "/", " "), "", $doc);
$ref = $_REQUEST["ref"];
$email = $email_cliente;
$gerarDireto = $_REQUEST["gerarDireto"];
$descricao = $descricao;
$nome = $nome_cliente;
$sobrenome = $_REQUEST["sobrenome"];

?>
<html lang="pt-br">
<head>
    <title>Pagamento</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <meta name="Description" content="Sistema <?php echo $nome_sistema ?>">
    <meta name="Author" content="Hugo Vasconcelos">
    <meta name="Keywords" content="sistemas hugo vasconcelos, sistemas hugo, sistemas portal hugo cursos, portal hugocursos, sistemas com php8" />
    <script src="https://sdk.mercadopago.com/js/v2"></script>
    <link href="./assets/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/signin.css" rel="stylesheet">
    <script src="./assets/jquery-3.6.4.min.js"></script>
</head>
<body  class="text-center">


<form action="agendamento_confirmado" method="post" style="display:none">
    <input type="hidden" name="id" value="<?=$id_conta;?>">
     <input type="hidden" name="enviar" value="Sim">
    <button id="btn_form" type="submit"></button>
</form>



<div style="max-width: 500px; max-height: 800px; margin: 0 auto;  text-align: center; margin-bottom: 20px; word-break: break-all;" >


<div id="info_pagamento" style="text-align: center;"> 
        <p class="h3 font-weight-normal" style=" font-size: 18px; border-radius: 4px;"><span>(<?php echo $descricao ?>)</span> <span style="color:green; ">R$ <?=$valorF;?></span> </p>     
        </div>    

<div  id="paymentBrick_container">
        </div>
        <div id="statusScreenBrick_container">
        </div>
        <div class="form-signin" id="form-pago" style="display:none;text-align: center;">
            <h1 class="h3 mb-3 font-weight-normal">Obrigado!</h1>
            <img class="mb-4"  src="<?=$url_sistema;?>sistema/sas/pagamentos/assets/check_ok.png" alt="" width="120" height="120">
            <br>
            <h5><?=$MSG_APOS_PAGAMENTO;?></h5>
            <br>
            Código do pagamento: <?php echo $_GET["id"]; ?>
        </div>

    </div>
    <style>
        body{font-family:arial}
    </style>
    <script>
        var payment_check;
        const mp = new MercadoPago('<?=$TOKEN_MERCADO_PAGO_PUBLICO;?>', {
            locale: 'pt-BR'
        });
        const bricksBuilder = mp.bricks();
        const renderPaymentBrick = async (bricksBuilder) => {
            const settings = {
                initialization: {
                    amount: '<?=$valor;?>',
                    payer: {
                        firstName: "<?=$nome;?>",
                        lastName: "<?=$sobrenome;?>",
                        email: "<?=$email;?>",
                        identification: {
                            type: '<?=(strlen($doc)>11? "CNPJ" : "CPF");?>',
                            number: '<?=$doc;?>',
                        },
                        address: {
                            zipCode: '',
                            federalUnit: '',
                            city: '',
                            neighborhood: '',
                            streetName: '',
                            streetNumber: '',
                            complement: '',
                        }
                    },
                },
                customization: {
                    visual: {
                        style: {
                            theme: "dark",
                        },
                    },
                    paymentMethods: {
                        <?php if($ATIVAR_CARTAO_CREDITO=="1"){?>creditCard: "all",<?php } ?>
                        <?php if($ATIVAR_CARTAO_DEBIDO=="1"){?>debitCard: "all",<?php } ?>
                        <?php if($ATIVAR_BOLETO=="1"){?>ticket: "all",<?php } ?>
                        <?php if($ATIVAR_PIX=="1"){?>bankTransfer: "all",<?php } ?>
                        maxInstallments: 12
                    },
                },
                callbacks: {
                    onReady: () => {
                    },
                    onSubmit: ({ selectedPaymentMethod, formData }) => {

                        formData.external_reference = '<?=$ref;?>';
                        formData.description = '<?=$descricao;?>';
                        var id_conta = '<?=$id_conta;?>';

                        return new Promise((resolve, reject) => {
                            fetch("<?=$url_sistema;?>sistema/sas/pagamentos/process_payment.php", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                },
                                body: JSON.stringify(formData),
                            })
                            .then((response) => response.json())
                            .then((response) => {
                // receber o resultado do pagamento
                                if(response.status==true){
                                    window.location.href = "<?=$url_sistema;?>sistema/sas/pagamentos/index.php?id="+response.id+'&id_conta='+id_conta;
                                }
                                if(response.status!=true){
                                    alert(response.message);
                                }
                                resolve();
                            })
                            .catch((error) => {
                                reject();
                            });
                        });
                    },
                    onError: (error) => {
                        console.error(error);
                    },
                },
            };
            window.paymentBrickController = await bricksBuilder.create(
                "payment",
                "paymentBrick_container",
                settings
                );
        };

        const renderStatusScreenBrick = async (bricksBuilder) => {
            const settings = {
                initialization: {
                    paymentId: '<?=$_GET["id"];?>',
                },
                customization: {
                    visual: {
                        hideStatusDetails: false,
                        hideTransactionDate: false,
                        style: {
            theme: 'dark', // 'default' | 'dark' | 'bootstrap' | 'flat'
        }
    },
    backUrls: {
        //'error': '<http://<your domain>/error>',
        //'return': '<http://<your domain>/homepage>'
    }
},
callbacks: {
    onReady: () => {
        check("<?=$_GET["id"];?>", "<?=$_GET["id_conta"];?>");
    },
    onError: (error) => {
    },
},
};
window.statusScreenBrickController = await bricksBuilder.create('statusScreen', 'statusScreenBrick_container', settings);
};

<?php if($_GET["id"]!=""){ ?>
    renderStatusScreenBrick(bricksBuilder);
<?php } else { ?>
    <?php if($valor==""){?>
        alert("O valor do pagamento está vazio.");
    <?php } ?>
    renderPaymentBrick(bricksBuilder);
<?php } ?>
var redi = "<?=$URL_REDIRECIONAR;?>";
function check(id, id_conta) {
    var settings = {
        "url": "<?=$url_sistema;?>sistema/sas/pagamentos/process_payment.php?acc=check&id=" + id + "&id_conta=" + id_conta,
        "method": "GET",
        "timeout": 0
    };
    $.ajax(settings).done(function(response) {
        try {
            if (response.status == "pago") {
                $("#statusScreenBrick_container").slideUp("fast");
                $("#form-pago").slideDown("fast");
                if (redi.trim() == "Sim") {
                    setTimeout(() => {
                        window.location = "<?=$url_sistema;?>painel/mensalidades";
                        //$("#btn_form").click();
                    }, 6000);
                }
            } else {
                setTimeout(() => {
                    check(id)
                }, 3000);
            }
        } catch (error) {
            alert("Erro ao localizar o pagamento, contacte com o suporte");
        }
    });
}
</script>

<script type="text/javascript">
    function clique(){       
        document.getElementById("clique_aqui").style.display = 'none';
        }
</script>

</body>
</html>