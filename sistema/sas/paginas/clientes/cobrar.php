<?php 
$tabela = 'receber';
require_once("../../../conexao.php");

$id = $_POST['id'];


$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$descricao = @$res[0]['descricao'];
$valor = @$res[0]['valor'];
$cliente = @$res[0]['cliente'];
$vencimento = @$res[0]['vencimento'];

$data_atual = date('Y-m-d');

$vencimentoF = implode('/', array_reverse(@explode('-', $vencimento)));
$valorF = @number_format($valor, 2, ',', '.');



$sino = json_decode('"\ud83d\udd14"');

$query2 = $pdo->query("SELECT * FROM empresas where id = '$cliente'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
if(@count($res2) > 0){
    $nome_cliente = $res2[0]['nome'];
    $telefone_cliente = $res2[0]['telefone'];
}else{
    $nome_cliente = 'Sem Registro';
    $telefone_cliente = "";
}

$point_down = json_decode('"\ud83d\udc47"');

if($api_whatsapp != 'Não' and $telefone_cliente != ''){

    $link_pgto = $url_sistema.'pagar/'.$id;


            $telefone_envio = '55'.preg_replace('/[ ()-]+/' , '' , $telefone_cliente);


            if(strtotime($vencimento) < strtotime($data_atual)){
                $mensagem = $sino.' _Lembrete Automático de Vencimento!_ %0A%0A';

                $mensagem .= '_Queremos Lembrar que Você tem uma Conta Vencida_ %0A%0A';
            }else{
                $mensagem = $sino.' _Lembrete Automático de Pagamento!_%0A%0A';
            }


            $mensagem .= '*'.$nome_sistema.'* %0A%0A';
            


            $mensagem .= '*Empresa: '.$nome_cliente.'*%0A';
            $mensagem .= '*Descrição:* '.$descricao.' %0A';
            $mensagem .= '*Valor:* '.$valorF.' %0A';
            $mensagem .= '*Vencimento:* '.$vencimentoF.' %0A%0A';   

            if($dados_pagamento == ""){
                    $mensagem .= 'Efetue o pagamento no link abaixo'.$point_down.' %0A';
                    $mensagem .= $link_pgto;
                }else{
                    $mensagem .= 'Dados de Pagamento Abaixo'.$point_down.' %0A';
                    $mensagem .= $dados_pagamento;
                }
        
            
            require('../../apis/texto.php');            
            
        }   

echo 'Cobrança Efetuada';
?>