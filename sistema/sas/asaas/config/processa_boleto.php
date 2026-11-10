<?php 
@session_start();
require("../../../conexao.php");

$id_conta = $_GET['id'];

$query = $pdo->prepare("SELECT * FROM receber_sas WHERE id = :id_conta");
$query->bindValue(":id_conta", $id_conta);
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);

if (count($res) > 0) {
    $id = $res[0]['id'];
    $descricao = $res[0]['descricao'];
    $valor = $res[0]['valor'];
    $ref_pix = $res[0]['ref_pix'];
    $vencimento = $res[0]['vencimento'];
    
}

// Cálculo multa e juros
$valor_multa = 0;
$valor_juros = 0;
$data_hoje = date('Y-m-d');
if (strtotime($vencimento) < strtotime($data_hoje)) {
    $valor_multa = $multa_atraso;
    $dias_vencidos = floor((strtotime($data_hoje) - strtotime($vencimento)) / (60 * 60 * 24));
    $valor_juros = ($valor * $juros_atraso / 100) * $dias_vencidos;
}

$total = $valor + $valor_multa + $valor_juros;
$valor_total = $total;

// Recebe dados do cliente (POST)
$nome = $_POST['nome'] ?? null;
$cpf = preg_replace('/[^0-9]/', '', $_POST['cpf'] ?? '');
$email = $_POST['email'] ?? null;
$telefone = preg_replace('/[^0-9]/', '', $_POST['telefone'] ?? '');
$cep = preg_replace('/[^0-9]/', '', $_POST['cep'] ?? '');
$rua = $_POST['rua'] ?? null;
$numero = $_POST['numero'] ?? null;
$bairro = $_POST['bairro'] ?? null;
$cidade = $_POST['cidade'] ?? null;
$estado = $_POST['estado'] ?? null;
$complemento = $_POST['complemento'] ?? null;

// Vencimento em 3 dias (padrão)
//$data_vencimento = date('Y-m-d', strtotime("+3 days"));
$data_vencimento = date('Y-m-d');

require("configApi.php");

// Criar cliente na API Asaas
$dados_cliente = [
    'name' => $nome,
    'cpfCnpj' => $cpf,
    'email' => $email,
    'phone' => $telefone,
    'postalCode' => $cep,
    'address' => $rua,
    'addressNumber' => $numero,
    'complement' => $complemento,
    'province' => $bairro,
    'city' => $cidade,
    'state' => $estado
    
];


$curl_cliente = curl_init();
curl_setopt_array($curl_cliente, array(
    CURLOPT_URL => 'https://api.asaas.com/v3/customers',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($dados_cliente),
    CURLOPT_HTTPHEADER => array(
        'accept: application/json',
        'content-type: application/json',
        'User-agent: 123',
        'access_token: '.$access_token
    ),
));
$response_cliente = curl_exec($curl_cliente);
$resultado_cliente = json_decode($response_cliente, true);
curl_close($curl_cliente);

// Verificar se houve erro na criação do cliente
if (isset($resultado_cliente['errors'])) {
    echo '<div class="alert alert-danger" role="alert">';
    echo 'Erro ao cadastrar cliente: ' . $resultado_cliente['errors'][0]['description'];
    echo '</div>';
    exit;
}

// Verificar se houve erro na criação do cliente ou se não retornou o ID
if (!$resultado_cliente || !isset($resultado_cliente['id'])) {
    echo '<div class="alert alert-danger" role="alert">';
    echo 'Erro ao cadastrar cliente: ';
    echo '<pre>';
    print_r($resultado_cliente); // Mostra o erro completo
    echo '</pre>';
    echo '</div>';

    echo '<h5>Dados enviados para o Asaas (Cliente):</h5>';
    echo '<pre>';
    print_r($dados_cliente);
    echo '</pre>';

    
    exit;
}

// Criar boleto na API Asaas
$dados_boleto = [
    'customer' => $resultado_cliente['id'],
    'billingType' => 'BOLETO',
    'value' => floatval($valor_total),
    'dueDate' => $data_vencimento,
    'description' => 'Pedido #' . $id_conta,
    'externalReference' => $id_conta,    
    'daysAfterDueDateToRegistrationCancellation' => 3 // Cancela o registro após 3 dias do vencimento
];

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.asaas.com/v3/payments',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($dados_boleto),
    CURLOPT_HTTPHEADER => array(
        'accept: application/json',
        'content-type: application/json',
        'User-agent: 123',
        'access_token: '.$access_token
    ),
));
$response = curl_exec($curl);
$resultado = json_decode($response, true);
curl_close($curl);

// Início da estrutura da modal
echo '<div class="modal-content-inner">';

// Verificar se houve erro na criação do boleto
if (isset($resultado['errors'])) {
    echo '<div class="alert alert-danger mb-4" role="alert">';
    echo 'Erro ao gerar boleto: ' . $resultado['errors'][0]['description'];
    echo '</div>';

    echo '<div class="text-center mt-3">';
    echo '<button type="button" class="btn btn-secondary" onclick="$(\'#myModalBoleto\').css(\'display\', \'none\');">Fechar</button>';
    echo '</div>';
} else {
    // Atualizar o registro com o ID da transação
    if (isset($resultado['id'])) {
        $pdo->query("UPDATE receber_sas SET ref_pix = '{$resultado['id']}' where id = '$id_conta'");

        // Cabeçalho com informações principais
        echo '<div class="text-center mb-4">';
        echo '<h4>Boleto Gerado com Sucesso</h4>';
        echo '<p class="lead">Pedido #' . $id_conta . '</p>';
        echo '</div>';

        // Informações do boleto em cards
        echo '<div class="card mb-4">';
        echo '<div class="card-body">';
        echo '<div class="row">';
        echo '<div class="col-md-6">';
        echo '<p><strong>Valor:</strong> <span class="text-primary">R$ ' . number_format($valor_total, 2, ',', '.') . '</span></p>';
        echo '<p><strong>Vencimento:</strong> ' . date('d/m/Y', strtotime($data_vencimento)) . '</p>';
        echo '</div>';

      
        echo '</div>'; // Fim da row
        echo '</div>'; // Fim do card-body
        echo '</div>'; // Fim do card

        // Código de barras do boleto
        if (isset($resultado['bankSlipBarCode'])) {
            echo '<div class="card mb-4">';
            echo '<div class="card-header bg-light">Código de Barras</div>';
            echo '<div class="card-body">';
            echo '<p class="text-monospace text-break" style="word-wrap: break-word;">' . $resultado['bankSlipBarCode'] . '</p>';
            echo '</div>';
            echo '</div>';
        }

        // Botões de ação
        echo '<div class="d-flex justify-content-center gap-2 mb-4">';

        // Link para visualizar o boleto
        if (isset($resultado['bankSlipUrl'])) {
            echo '<a href="' . $resultado['bankSlipUrl'] . '" target="_blank" class="btn btn-primary mx-2">';
            echo '<i class="fas fa-file-invoice"></i> Visualizar Boleto';
            echo '</a>';

            echo '<a href="' . $resultado['bankSlipUrl'] . '" download class="btn btn-outline-primary mx-2">';
            echo '<i class="fas fa-download"></i> Download PDF';
            echo '</a>';
        }

        echo '</div>';

        // Instruções para pagamento
        echo '<div class="card mb-3">';
        echo '<div class="card-header bg-light">Instruções para Pagamento</div>';
        echo '<div class="card-body">';
        echo '<ul class="mb-0">';
        echo '<li>O boleto pode ser pago em qualquer banco até a data de vencimento.</li>';
        echo '<li>Após o pagamento, a confirmação pode levar até 3 dias úteis.</li>';
        echo '<li>Pagamentos usando o QRCODE do boleto, a confirmação é imediata.</li>';
        echo '</ul>';
        echo '</div>';
        echo '</div>';

        // Botão de fechar na parte inferior
        echo '<div class="text-center mt-4">';
        echo '<button type="button" class="btn btn-secondary" onclick="$(\'#myModalBoleto\').css(\'display\', \'none\');">Fechar</button>';
        echo '</div>';
    } else {
        echo '<div class="alert alert-warning mb-4" role="alert">';
        echo 'Não foi possível gerar o boleto. Por favor, tente novamente.';
        echo '</div>';

        echo '<div class="text-center mt-3">';
        echo '<button type="button" class="btn btn-secondary" onclick="$(\'#myModalBoleto\').css(\'display\', \'none\');">Fechar</button>';
        echo '</div>';
    }
}

echo '</div>'; // Fim da modal-content-inner/
?>