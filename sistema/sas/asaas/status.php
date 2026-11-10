<?php
@session_start();
require("../../conexao.php");

$id_conta = @filter_var($_GET['id'], FILTER_SANITIZE_STRING);

//ref pix testes aprovado pay_wdx0czye9gp2u4tj

// Buscar dados da cobrança localmente
$query = $pdo->prepare("SELECT * FROM receber_sas where id = :id_conta");
$query->bindValue(":id_conta", "$id_conta");
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);

if ($total_reg > 0) {
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
    $frequencia = $res[0]['frequencia'];
    $saida_antiga = $res[0]['forma_pgto'];
    $arquivo = $res[0]['arquivo'];
    $referencia = $res[0]['referencia'];
    $usuario_lanc = $res[0]['usuario_lanc'];
    $id_ref = $res[0]['id_ref'];
    
}

require("config/configApi.php");

// Verifica o pagamento se ainda não estiver marcado como pago
if (@$api_pagamento == 'Asaas' and @$pago != 'Sim') {
    $payment_id = @$ref_pix;

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.asaas.com/v3/payments/' . $payment_id, // Consulta completa da cobrança
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'accept: application/json',
            'content-type: application/json',
            'User-Agent: MeuSistema/1.0',
            'access_token: ' . $access_token
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    $resultado = json_decode($response);

    // Apenas para visualizar o retorno completo em teste:
    // echo "<pre>"; print_r($resultado); echo "</pre>"; exit();

    // Variáveis corretas agora:
    $status_api = $resultado->status ?? 'PENDING'; // Status
    $valor_liquido = $resultado->netValue ?? 0; // Valor líquido recebido
    $total_pago = $resultado->value ?? 0; // Valor da cobrança
    $tipo_pagamento = $resultado->billingType ?? 'PIX'; // Forma de pagamento

    //echo "Status: $status_api <br>";
    //echo "Valor Pago (líquido): R$ " . number_format($valor_liquido, 2, ',', '.') . "<br>";
    //echo "Valor Original: R$ " . number_format($total_pago, 2, ',', '.') . "<br>";
    //echo "Forma de Pagamento: $tipo_pagamento <br>";


    if($tipo_pagamento == 'CREDIT_CARD'){
        $tipo_pagamento = 'Cartão de Crédito';
    }
   
    if($tipo_pagamento == 'BOLETO'){
        $tipo_pagamento = 'Boleto';
    }

    if($tipo_pagamento == 'PIX '){
        $tipo_pagamento = 'Pix';
    }

    // Se confirmado ou recebido, atualiza como pago no banco
    if (in_array($status_api, ['RECEIVED', 'CONFIRMED'])) {
       $query2 = $pdo->query("SELECT * from formas_pgto where nome like '%$tipo_pagamento%' and (empresa = 0 or empresa is null)");
                        $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
                        $id_forma_pgto = @$res2[0]['id'];

                        $pdo->query("UPDATE receber_sas set pago = 'Sim', data_pgto = curDate(), valor = '$total_pago', subtotal = '$total_pago', forma_pgto = '$id_forma_pgto' where id = '$id_conta'");   


                         //verificar se a conta possui recorrencia para criar a proxima
        if (@$frequencia > 0) {

        $data_venc = $vencimento;
    //CRIAR A PRÓXIMA CONTA A PAGAR
    $dias_frequencia = $frequencia;

    if ($dias_frequencia == 30 || $dias_frequencia == 31) {
        $nova_data_vencimento = date('Y/m/d', strtotime("+1 month", strtotime($data_venc)));

    } else if ($dias_frequencia == 90) {
        $nova_data_vencimento = date('Y/m/d', strtotime("+3 month", strtotime($data_venc)));

    } else if ($dias_frequencia == 180) {
        $nova_data_vencimento = date('Y/m/d', strtotime("+6 month", strtotime($data_venc)));

    } else if ($dias_frequencia == 360 || $dias_frequencia == 365) {
        $nova_data_vencimento = date('Y/m/d', strtotime("+1 year", strtotime($data_venc)));

    } else {
        $nova_data_vencimento = date('Y/m/d', strtotime("+$dias_frequencia days", strtotime($data_venc)));
    }


    
        $pdo->query("INSERT INTO receber_sas set descricao = '$descricao', cliente = '$cliente', valor = '$valor', data_lanc = curDate(), vencimento = '$nova_data_vencimento', frequencia = '$frequencia', forma_pgto = '$saida_antiga', arquivo = '$arquivo', pago = 'Não', referencia = '$referencia', usuario_lanc = '$usuario_lanc', hora = curTime()");
                
    }



    //chamar o aprova plano caso essa conta seja de um upgrade de plano
    if($referencia == 'Upgrade'){
        $id_plano = $id_ref;
        $id_empresa = $cliente;
        if($id_plano > 0 and $id_empresa > 0){
            require("aprovar_plano.php");
        }
        
    }

    }


}

// Se já pago, redireciona direto
if (@$pago == 'Sim') {
    echo "<META HTTP-EQUIV=REFRESH CONTENT = '0;URL=" . $url_sistema . "painel/asaas/obrigado.php?id=" . $id_conta . "'>";
    exit;
}
?>
