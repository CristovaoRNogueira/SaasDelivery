<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
if($id_empresa == ""){
  echo 'Sem Sessão da Empresa, refaça o Login!';
  exit();
}
$id_usuario = $_SESSION['id'];

$tabela = 'vendas';

$id = $_POST['id'];
$id_pedido = $_POST['id'];
$entregador = $_POST['entregador'];

$query_entregador = $pdo->prepare("SELECT id FROM usuarios
	WHERE id = :id AND empresa = :empresa
	AND nivel IN ('Entregador', 'Motoboy', 'Motoboys', 'Entregadores')
	AND ativo = 'Sim' LIMIT 1");
$query_entregador->execute([':id' => $entregador, ':empresa' => $id_empresa]);
if (!$query_entregador->fetchColumn()) {
	echo 'Selecione um entregador ou motoboy ativo. Cadastre um caso a lista esteja vazia.';
	exit();
}

$pdo->query("UPDATE $tabela SET entregador = '$entregador' where id = '$id'");

echo 'Salvo com Sucesso';


//BUSCAR AS INFORMAÇÕES DO PEDIDO
$query = $pdo->query("SELECT * from vendas where id = '$id' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);

$id = $res[0]['id'];
$cliente = $res[0]['cliente'];
$valor = $res[0]['valor'];
$total_pago = $res[0]['total_pago'];
$troco = $res[0]['troco'];
$data = $res[0]['data'];
$hora = $res[0]['hora'];
$status = $res[0]['status'];
$pago = $res[0]['pago'];
$obs = $res[0]['obs'];
$taxa_entrega = $res[0]['taxa_entrega'];
$tipo_pgto = $res[0]['tipo_pgto'];
$usuario_baixa = $res[0]['usuario_baixa'];
$entrega = $res[0]['entrega'];
$mesa = $res[0]['mesa'];
$nome_cliente_ped = $res[0]['nome_cliente'];
$cupom = $res[0]['cupom'];
$n_pedido = $res[0]['pedido'];


$cupomF = number_format($cupom, 2, ',', '.');
$valorF = number_format($valor, 2, ',', '.');
$total_pagoF = number_format($total_pago, 2, ',', '.');
$trocoF = number_format($troco, 2, ',', '.');
$taxa_entregaF = number_format($taxa_entrega, 2, ',', '.');
$dataF = implode('/', array_reverse(explode('-', $data)));
//$horaF = date("H:i", strtotime($hora));	

$valor_dos_itens = $valor - $taxa_entrega + $cupom;
$valor_dos_itensF = number_format($valor_dos_itens, 2, ',', '.');

$hora_pedido = date('H:i', strtotime("+$previsao_entrega minutes", strtotime($hora)));

$query2 = $pdo->query("SELECT * FROM clientes where id = '$cliente'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$total_reg2 = @count($res2);

  $nome_cliente = @$res2[0]['nome'];
  $telefone_cliente = @$res2[0]['telefone'];
  $rua_cliente = @$res2[0]['endereco'];
  $numero_cliente = @$res2[0]['numero'];
  $complemento_cliente = @$res2[0]['complemento'];
  $bairro_cliente = @$res2[0]['bairro'];
  $cidade_cliente = @$res2[0]['cidade'];


if ($api_whatsapp != 'Não') {
  $query2 = $pdo->query("SELECT * FROM usuarios where id = '$entregador'");
  $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
  $tel_entregador = $res2[0]['telefone'];
  $nome_entregador = $res2[0]['nome'];

  $telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $tel_entregador);

  $mensagem = '*----------NOVA ENTREGA----------* %0A%0A';

  $mensagem .= '*Pedido:* ' . $n_pedido . '%0A';
  $mensagem .= '*Cliente:* ' . $nome_cliente . '%0A';
  $mensagem .= '*Telefone:* ' . $telefone_cliente . '%0A';
  $mensagem .= '*Valor:* R$ ' . $valorF . '%0A';
  $mensagem .= '*Pagamento:* ' . $tipo_pgto . '%0A';
  $mensagem .= '*Previsão Entrega:* ' . $hora_pedido . '%0A';
  $mensagem .= '%0A________________________________%0A%0A';
  $mensagem .= '*_Detalhes do Pedido_* %0A';




  //ABAIXO É PARA PEGAR OS PRODUTOS COMPRADOS
  $nome_produto2 = '';
  $res = $pdo->query("SELECT * from carrinho where pedido = '$id' order by id asc");
  $dados = $res->fetchAll(PDO::FETCH_ASSOC);
  $linhas = count($dados);


  $sub_tot;
  for ($i = 0; $i < count($dados); $i++) {
    foreach ($dados[$i] as $key => $value) {
    }
    $texto_produtos = '';
    $id_carrinho = $dados[$i]['id'];
    $id_produto = $dados[$i]['produto'];
    $quantidade = $dados[$i]['quantidade'];
    $total_item = $dados[$i]['total_item'];
    $obs_item = $dados[$i]['obs'];
    $item = $dados[$i]['item'];
    $variacao = $dados[$i]['variacao'];
    $nome_produto_tab = $dados[$i]['nome_produto'];
    $sabores = $dados[$i]['sabores'];
    $borda = $dados[$i]['borda'];
    $categoria = $dados[$i]['categoria'];
    $valor_unitario = $dados[$i]['valor_unitario'];

    $query2 = $pdo->query("SELECT * FROM variacoes where id = '$variacao'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    if (@count(@$res2) > 0) {
      $sigla_variacao = '(' . $res2[0]['sigla'] . ')';
    } else {
      $sigla_variacao = '';
    }




    $query2 = $pdo->query("SELECT * FROM temp where carrinho = '$id_carrinho' and tabela = 'Variação' order by id asc limit 1");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    $id_do_item = @$res2[0]['id_item'];

    $query2 = $pdo->query("SELECT * FROM itens_grade where id = '$id_do_item'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    if (@$res2[0]['texto'] != "") {
      $sigla_grade = @$res2[0]['texto'];
    } else {
      $sigla_grade = '';
    }

    $query2 = $pdo->query("SELECT * FROM produtos where id = '$id_produto'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    if (@count(@$res2) > 0) {
      $nome_produto = $res2[0]['nome'];
      $foto_produto = $res2[0]['foto'];
    } else {
      $nome_produto = $nome_produto_tab;
      $foto_produto = "";
    }


    $tabela_ad = 'adicionais';
    if ($sabores > 0) {
      $nome_produto = $nome_produto_tab;
      $tabela_ad = 'adicionais_cat';
    }


    $query8 = $pdo->query("SELECT * FROM bordas where id = '$borda'");
    $res8 = $query8->fetchAll(PDO::FETCH_ASSOC);
    $total_reg8 = @count($res8);
    if ($total_reg8 > 0) {
      $nome_borda = ' - ' . $res8[0]['nome'];
    } else {
      $nome_borda = '';
    }

    $texto_produtos .= '✅' . $quantidade . ' - ' . $nome_produto . ' ' . $sigla_variacao . ' ' . $sigla_grade . '%0A';



    $mensagem .= '%0A' . $texto_produtos;

    if ($total_reg8 > 0) {
      $mensagem .= $nome_borda . '%0A';
    }

    //COMEÇAR VER OS ADICIONAIS E OUTROS DOS DEMAIS ITENS QUE NAO SAO PIZZA 2 SAB
    $query2 = $pdo->query("SELECT * FROM temp where carrinho = '$id_carrinho' and tabela = 'adicionais'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    $total_reg2 = @count($res2);
    if ($total_reg2 > 0) {
      if ($total_reg2 > 1) {
        $texto_adicional = @$total_reg2 . ' Tipos de Adicionais';
      } else {
        $texto_adicional = @$total_reg2 . ' Tipo de Adicional';
      }
      $mensagem .= ' *' . $texto_adicional . '* %0A';
      for ($i2 = 0; $i2 < $total_reg2; $i2++) {
        foreach ($res2[$i2] as $key => $value) {
        }
        $id_temp = $res2[$i2]['id'];
        $id_item = $res2[$i2]['id_item'];
        $quantidade_temp = $res2[$i2]['quantidade'];

        $query3 = $pdo->query("SELECT * FROM $tabela_ad where id = '$id_item'");
        $res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
        $total_reg3 = @count($res3);
        $nome_adc = $res3[0]['nome'];
        if ($i2 < ($total_reg2 - 1)) {
          $nome_adc .= '%0A';
        }

        $mensagem .= '```(' . $quantidade_temp . ') ' . $nome_adc . '```' . '';
      }

      $mensagem .= '%0A';

    }





    //percorrer as grades do produto
    $query20 = $pdo->query("SELECT * FROM grades where produto = '$id_produto' and tipo_item != 'Variação'");
    $res20 = $query20->fetchAll(PDO::FETCH_ASSOC);
    $total_reg20 = @count($res20);
    if ($total_reg20 > 0) {
      for ($i20 = 0; $i20 < $total_reg20; $i20++) {
        $id_da_grade = $res20[$i20]['id'];
        $nome_da_grade = $res20[$i20]['nome_comprovante'];
        $tipo_item_grade = $res20[$i20]['tipo_item'];


        //buscar os itens selecionados pela grade
        $query2 = $pdo->query("SELECT * FROM temp where carrinho = '$id_carrinho' and grade = '$id_da_grade'");
        $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
        $total_reg2 = @count($res2);
        if ($total_reg2 > 0) {

          $mensagem .= '*' . $nome_da_grade . '* %0A';

          for ($i2 = 0; $i2 < $total_reg2; $i2++) {
            foreach ($res2[$i2] as $key => $value) {
            }
            $id_temp = $res2[$i2]['id'];
            $id_item = $res2[$i2]['id_item'];
            $tabela_item = $res2[$i2]['tabela'];
            $tipagem_item = $res2[$i2]['tipagem'];
            $grade_item = $res2[$i2]['grade'];

            if ($tipo_item_grade == 'Múltiplo') {
              $quant_item = '(' . $res2[$i2]['quantidade'] . ')';
            } else {
              $quant_item = '';
            }



            $query3 = $pdo->query("SELECT * FROM itens_grade where id = '$id_item'");
            $res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
            $total_reg3 = @count($res3);
            $nome_item = $res3[0]['texto'];
            if ($i2 < ($total_reg2 - 1)) {
              $nome_item .= ', ';
            }
            $mensagem .= '```' . $quant_item . $nome_item . '```%0A';
          }

          $mensagem .= '';


        }

      }



    }

  }

  //ond pizza 2 sab
  if ($obs != "") {
    $mensagem .= '%0A*Observações do Pedido*%0A';
    $mensagem .= '_' . $obs . '_' . '%0A%0A';
  }


  if ($entrega == "Delivery") {
    $mensagem .= '%0A*Endereço do Cliente*%0A';
    $endereco = $rua_cliente . ' ' . $numero_cliente . ' ' . $complemento_cliente . ' ' . $bairro_cliente . ' ' . $cidade_cliente;
    $mensagem .= $endereco;

  }
  $mensagem .= '%0A%0A';

  $mensagem .= '_Acessar Painel com seu telefone para visaulizar e confrimar a entrega:_ %0A';

  $mensagem .= $url_sistema . 'sistema';



  $data_mensagem = date('Y-m-d H:i:s');
  require ("../../../../js/ajax/api_texto.php");
}



$query = $pdo->prepare("INSERT INTO pagar SET descricao = 'Comissão Entrega', tipo = 'Comissão', valor = :valor, data_lanc = curDate(), vencimento = curDate(), usuario_lanc = '$id_usuario', foto = 'sem-foto.png', arquivo = 'sem-foto.jpg', pessoa = '0', pago = 'Não', funcionario = '$entregador', referencia = 'Comissão', id_ref = '$n_pedido', comissao = 'Entregador', empresa = '$id_empresa' ");
$query->bindValue(":valor", "$taxa_entrega");
$query->execute();
?>
