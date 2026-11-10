<?php
@session_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
//error_reporting(E_ALL);

require("../../conexao.php");

$id_conta = filter_var($_GET['id_conta'], @FILTER_SANITIZE_STRING);

$query = $pdo->prepare("SELECT * FROM receber_sas where id = :id_conta");
$query->bindValue(":id_conta", "$id_conta");
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){
    $cliente = $res[0]['cliente'];
    $descricao = $res[0]['descricao'];
    $valor = $res[0]['valor'];
    $ref_pix = $res[0]['ref_pix'];
    $data = $res[0]['data_lanc'];
    $hora = $res[0]['hora'];
    $dataF = implode('/', array_reverse(explode('-', $data)));
    $horaF = date("H:i", strtotime($hora));   
    $vencimento = $res[0]['vencimento'];



}


$total = $valor;

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

    $valor = $valor_multa + $valor_juros + $valor;
    $valor_multa_juros = $valor_multa + $valor_juros;
    $total = $valor;
}


$totalF = @number_format($total, 2, ',', '.');


require("config/configApi.php");


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Checkout de pagamento</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="config/css/style.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --success-color: #1cc88a;
            --border-radius: 10px;
            --box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fc;
            color: #5a5c69;
            padding: 20px 0;
        }

        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
            background-color: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .page-title {
            color: #2e384d;
            font-weight: 600;
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 10px;
        }

        .page-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--primary-color);
        }

        .order-summary {
            background-color: var(--secondary-color);
            border-radius: var(--border-radius);
            padding: 25px;
            position: sticky;
            top: 20px;
            border: 1px solid rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }

        .order-summary:hover {
            transform: translateY(-5px);
        }

        .summary-title {
            font-weight: 600;
            color: #2e384d;
            margin-bottom: 20px;
            font-size: 18px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .summary-total {
            font-weight: 600;
            color: #2e384d;
            font-size: 16px;
        }

        .security-badge {
            display: flex;
            align-items: center;
            color: var(--success-color);
            font-weight: 500;
            font-size: 14px;
            margin-top: 15px;
        }

        .security-badge i {
            margin-right: 8px;
        }

        .payment-methods {
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .accordion-item {
            border: 1px solid rgba(0,0,0,0.1);
            margin-bottom: 15px;
            border-radius: var(--border-radius) !important;
            overflow: hidden;
        }

        .accordion-button {
            background-color: #fff;
            color: #2e384d;
            font-weight: 500;
            padding: 20px;
        }

        .accordion-button:not(.collapsed) {
            background-color: var(--primary-color);
            color: white;
        }

        .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(0,0,0,0.1);
        }

        .accordion-body {
            padding: 25px;
            background-color: #fff;
        }

        @media (max-width: 992px) {
            .order-summary {
                position: relative;
                margin-bottom: 30px;
            }
        }
    </style>
</head>
<body>
<div class="checkout-container">
    <h2 class="page-title">Faça seu Pagamento</h2>
    

    <div class="row">
        <!-- Resumo do pedido -->
        <div class="col-lg-4 mb-4 mb-lg-0">
            <div class="order-summary">
                <h6 class="summary-title">Detalhes do pedido</h6>

                <div class="summary-item">
                    <span>Subtotal</span>
                    <span>R$ <?php echo $totalF ?></span>
                </div>
               

                <div class="mt-4">
                    <p class="mb-3 small">O pagamento leva poucos segundos para ser liberado em nossa plataforma.</p>
                    <div class="security-badge">
                        <i class="fas fa-shield-alt"></i>
                        <span>Você está em um ambiente seguro</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Métodos de pagamento -->
        <div class="col-lg-8">
            <div class="payment-methods accordion" id="accordionPayment">
                <!-- Cartão de crédito -->
                <div class="accordion-item">
                    <?php include('config/blocos/cartao.php');?>
                </div>

                <!-- PIX -->
                <div class="accordion-item">
                    <?php include('config/blocos/pix.php');?>
                </div>

                <!-- Boleto -->
                <div class="accordion-item">
                    <?php include('config/blocos/boleto.php');?>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="status"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>


<script>
    $(document).ready(function() {
        // Garante que o preloader esteja oculto inicialmente
        $('#preloader').hide();

        // Quando o usuário submeter o formulário
        $('#pixClienteForm').submit(function(event) {
            event.preventDefault();

            $('#formErrorMessage').hide();

            // Exibe o preloader enquanto a transação é processada
            $('#preloader').show();
            $('#myModal').show();
            $('#modalBody').html('<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Carregando...</span></div><p class="mt-2">Gerando QR Code PIX...</p></div>');

            // Coletando os dados do formulário
            var formData = $(this).serialize();

            // Enviando os dados via POST para gerar o PIX
            $.post('<?php echo $url_sistema ?>sistema/sas/asaas/config/gerar_pix.php?id=<?php echo $id_conta;?>', formData, function(data) {
                $('#modalBody').html(data);
                $('#preloader').hide();

                // Habilita o botão para fechar o modal após 5 segundos
                setTimeout(function() {
                    $('.close-modal').show();
                }, 5000);
            }).fail(function(error) {
                $('#preloader').hide();
                $('#modalBody').html('<div class="alert alert-danger">Erro ao gerar o PIX. Por favor, tente novamente.<br>Detalhes: ' + error.statusText + '</div>');
                console.error('Erro:', error);
            });
        });

        // Fecha o modal quando o usuário clica no botão de fechar
        $('.close-modal').click(function() {
            $('#myModal').hide();
        });
    });
</script>

<script>
    // Script para manipulação da modal e envio do formulário
    $(document).ready(function() {
        // Referências aos elementos da modal
        var modal = $('#myModalBoleto');
        var closeModal = $('#closeModalBoleto');

        // Quando o formulário de boleto for submetido
        $("#form-boleto").on("submit", function(event) {
            event.preventDefault();

            // Coletando os dados do formulário
            var formData = $(this).serialize();
            var respostaDiv = $("#resposta-boleto");

            // Feedback visual para o usuário
            respostaDiv.html("<p class='text-info'>Gerando boleto, aguarde...</p>");

            // Requisição AJAX para gerar o boleto
            $.ajax({
                type: "POST",
                url: "<?php echo $url_sistema;?>sistema/sas/asaas/config/processa_boleto.php?id=<?php echo $id_conta; ?>",
                data: formData,
                success: function(data) {
                    // Coloca a resposta no corpo da modal
                    $('#modalBodyBoleto').html(data);

                    // Mostra a modal
                    modal.css("display", "block");

                    // Limpa a mensagem de carregamento
                    respostaDiv.html("");

                    // Habilita o fechamento da modal após 1 minuto
                    setTimeout(function() {
                        closeModal.show();
                    }, 60000); // 60000 milissegundos = 1 minuto
                },
                error: function(xhr, status, error) {
                    respostaDiv.html("<p class='text-danger'>Erro ao gerar boleto: " + error + "</p>");
                }
            });
        });

        // Fecha a modal quando o usuário clica no botão de fechar
        closeModal.click(function() {
            modal.css("display", "none");
        });

        // Evita o fechamento da modal ao clicar fora dela
        $(window).click(function(event) {
            if (event.target.id === "myModalBoleto") {
                event.stopPropagation();
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        var tempo = 2000; //Dois segundos

        (function selectNumUsuarios() {
            $.ajax({
                url: "<?php echo $url_sistema;?>sistema/sas/asaas/status.php?id=<?php echo $id_conta ?>",
                success: function(n) {
                    //essa é a function success, será executada se a requisição obtiver exito
                    $("#status").html(n);
                },
                complete: function() {
                    setTimeout(selectNumUsuarios, tempo);
                }
            });
        })();
    });
</script>
</body>
</html>