<?php
include ('data_formatada.php');


//BUSCAR AS INFORMAÇÕES DO PEDIDO
$query = $pdo->query("SELECT * from vendas where id = '$id' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);

$id = $res[0]['id'];
$cliente = $res[0]['cliente'];
$valor = $res[0]['valor'];
$total_pago = $res[0]['total_pago'];
$valor_recebido = (float) ($res[0]['valor_recebido'] ?? 0);
$saldo_pendente = max(0, (float) $res[0]['valor'] - $valor_recebido);
$troco = $res[0]['troco'];
$data = $res[0]['data'];
$hora = $res[0]['hora'];
$status = $res[0]['status'];
$pago = $res[0]['pago'];
$obs = $res[0]['obs'];
$taxa_entrega = $res[0]['taxa_entrega'];
$tipo_pgto = $res[0]['tipo_pgto'];
$descricao_pagamento = htmlspecialchars($tipo_pgto, ENT_QUOTES, 'UTF-8');
$query_pagamentos = $pdo->prepare("SELECT forma_pgto, valor FROM vendas_pagamentos WHERE venda = :venda AND empresa = :empresa ORDER BY id");
$query_pagamentos->execute([':venda' => $id, ':empresa' => (int) ($res[0]['empresa'] ?? ($id_empresa ?? 0))]);
$pagamentos_pedido = $query_pagamentos->fetchAll(PDO::FETCH_ASSOC);
if (count($pagamentos_pedido) > 0) {
  $linhas_pagamento = [];
  foreach ($pagamentos_pedido as $pagamento_pedido) {
    $linhas_pagamento[] = htmlspecialchars($pagamento_pedido['forma_pgto'], ENT_QUOTES, 'UTF-8') . ': R$ ' . number_format($pagamento_pedido['valor'], 2, ',', '.');
  }
  $descricao_pagamento = implode('<br>', $linhas_pagamento);
}
$usuario_baixa = $res[0]['usuario_baixa'];
$entrega = $res[0]['entrega'];
$mesa = $res[0]['mesa'];
$nome_cliente_ped = $res[0]['nome_cliente'];
$cupom = $res[0]['cupom'];
$n_pedido = $res[0]['pedido'];
$taxa_extra_pedido = (float) ($res[0]['taxa_extra_pedido'] ?? 0);
$descricao_taxa_extra = trim((string) ($titulo_taxa ?? ''));
if ($descricao_taxa_extra === '') {
  $descricao_taxa_extra = 'Taxa de Embalagem';
}

$cupomF = number_format($cupom, 2, ',', '.');
$valorF = number_format($valor, 2, ',', '.');
$total_pagoF = number_format($total_pago, 2, ',', '.');
$valor_recebidoF = number_format($valor_recebido, 2, ',', '.');
$saldo_pendenteF = number_format($saldo_pendente, 2, ',', '.');
$trocoF = number_format($troco, 2, ',', '.');
$taxa_entregaF = number_format($taxa_entrega, 2, ',', '.');
$taxa_extra_pedidoF = number_format($taxa_extra_pedido, 2, ',', '.');
$dataF = implode('/', array_reverse(explode('-', $data)));
//$horaF = date("H:i", strtotime($hora)); 

$valor_dos_itens = $valor - $taxa_entrega + $cupom - $taxa_extra_pedido;
$valor_dos_itensF = number_format($valor_dos_itens, 2, ',', '.');

$hora_pedido = date('H:i', strtotime("+$previsao_entrega minutes", strtotime($hora)));

$query2 = $pdo->query("SELECT * FROM clientes where id = '$cliente'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$total_reg2 = @count($res2);
if ($total_reg2 > 0) {
  $nome_cliente = @$res2[0]['nome'] . ' Tel: ' . @$res2[0]['telefone'];
  $telefone_cliente = @$res2[0]['telefone'];
  $rua_cliente = @$res2[0]['endereco'];
  $numero_cliente = @$res2[0]['numero'];
  $complemento_cliente = @$res2[0]['complemento'];
  $bairro_cliente = @$res2[0]['bairro'];
  $cidade_cliente = @$res2[0]['cidade'];
} else {

  if ($mesa != '0' and $mesa != '') {
    $nome_cliente = 'Mesa: ' . $mesa;
  } else {
    $nome_cliente = $nome_cliente_ped;
  }

  if ($nome_cliente_ped != "") {
    $nome_cliente = $nome_cliente_ped;
  }

  $telefone_cliente = '';
  $rua_cliente = '';
  $numero_cliente = '';
  $complemento_cliente = '';
  $bairro_cliente = '';
}

if ($entrega == 'Retirar') {
  $entrega = 'Retirar no Local';
}

if ($entrega == 'Consumir Local') {
  $entrega = 'Consumir no Local';
}


?>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<?php if (@$_GET['imp'] != 'Não') { ?>
  <script type="text/javascript">
    $(document).ready(function () {
      window.print();
      window.close();
    });
  </script>
<?php } ?>

<!-- CSS only -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
  integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">

<style type="text/css">
  * {
    margin: 0px;

    /*Espaçamento da margem da esquerda e da Direita*/
    padding: 0px;
    background-color: #ffffff;


  }

  .text {
    &-center {
      text-align: center;
    }
  }

  .printer-ticket {
    display: table !important;
    width: 100%;

    /*largura do Campos que vai os textos*/
    max-width: 400px;
    font-weight: light;
    line-height: 1.3em;

    /*Espaçamento da margem da esquerda e da Direita*/
    padding: 0px;
    font-family: TimesNewRoman, Geneva, sans-serif;

    /*tamanho da Fonte do Texto*/
    font-size:
      <?php echo $fonte_comprovante ?>
      px;



  }

  .th {
    font-weight: inherit;
    /*Espaçamento entre as uma linha para outra*/
    padding: 5px;
    text-align: center;
    /*largura dos tracinhos entre as linhas*/
    border-bottom: 1px dashed #000000;
  }

  .itens {
    font-weight: inherit;
    /*Espaçamento entre as uma linha para outra*/
    padding: 5px;

  }

  .valores {
    font-weight: inherit;
    /*Espaçamento entre as uma linha para outra*/
    padding: 2px 5px;

  }


  .cor {
    color: #000000;
  }


  .title {
    font-size: 12px;
    text-transform: uppercase;
    font-weight: bold;
  }

  /*margem Superior entre as Linhas*/
  .margem-superior {
    padding-top: 5px;
  }


  }
</style>



<div class="printer-ticket">
	  <div align="center">
    <b><?php echo $nome_sistema ?></b>
    </div>



  <div class="th">
    <?php echo $endereco_sistema ?> <br />
    <small>Contato: <?php echo $telefone_sistema ?>
      <?php if ($cnpj_sistema != "") {
        echo ' / CNPJ ' . @$cnpj_sistema;
      } ?>
    </small>
  </div>



  <div class="th">
    <?php if ($mesa != "" && $mesa != "0") {
      echo '<b>Mesa: ' . $mesa . ' </b> / ';
      
    } ?>
    Cliente <?php echo $nome_cliente ?>
    <br>
    Pedido: <b><?php echo $n_pedido ?></b> - Data: <?php echo $dataF ?> Hora: <?php echo $hora ?>
  </div>

  <div class="th title">Comprovante de Venda</div>

  <div class="th">CUPOM NÃO FISCAL</div>

  <?php

  $nome_produto2 = '';
  $res = $pdo->query("SELECT * from carrinho where pedido = '$id' order by id asc");
  $dados = $res->fetchAll(PDO::FETCH_ASSOC);
  $linhas = count($dados);

  $sub_tot;
  for ($i = 0; $i < count($dados); $i++) {
    foreach ($dados[$i] as $key => $value) {
    }
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


    $nome_borda = '';
    if ($borda != "") {
      $query8 = $pdo->query("SELECT * FROM bordas where id = '$borda'");
      $res8 = $query8->fetchAll(PDO::FETCH_ASSOC);
      $total_reg8 = @count($res8);
      if ($total_reg8 > 0) {
        $nome_borda = $res8[0]['nome'];
      } else {
        $nome_borda = 'Sem Borda';
      }
    }



    ?>

    <div class=" itens">

      <div align="left"> <?php echo $quantidade ?> - <?php echo $nome_produto ?>   <?php echo $sigla_variacao ?>
        <?php echo $sigla_grade ?>
        <span style="position:absolute; right:5px">
          R$ <?php
          $total_itemF = number_format($total_item, 2, ',', '.');
          // $total = number_format( $cp1 , 2, ',', '.');
          echo $total_itemF;
          ?>
        </span>
      </div>



      <?php

      if ($sabores > 1) {
        echo '<span style="margin-left: 15px"><small><b>Atenção: </b>' . $sabores . ' Sabores</small></span><br>';
      }

      if ($nome_borda != '') {
        echo '<span style="margin-left: 15px"><small><b>Borda: </b>' . $nome_borda . '</small></span><br>';
      }


      $query2 = $pdo->query("SELECT * FROM temp where carrinho = '$id_carrinho' and tabela = 'adicionais'");
      $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
      $total_reg2 = @count($res2);
      if ($total_reg2 > 0) {
        if ($total_reg2 > 1) {
          $texto_adicional = '(' . $total_reg2 . ') Tipos de Adicionais';
        } else {
          $texto_adicional = '(' . $total_reg2 . ') Tipo de Adicional';
        }
        ?>

        <div align="left" style="margin-left: 15px">
          <small><b><?php echo $texto_adicional ?> : </b>
            <?php
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
              echo '(' . $quantidade_temp . ') ' . $nome_adc;
              if ($i2 < ($total_reg2 - 1)) {
                echo ', ';
              }
            }
            ?>
          </small>
        </div>

      <?php } ?>






      <?php
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
            echo '<div >';
            echo '<b><small>' . $nome_da_grade . '</small></b>';
            ;

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
              echo ' <i><small>' . $quant_item . $nome_item . '</i></small>';
            }

            echo '<br>';
            echo '</div>';

          }

        }


      }


      ?>


      <?php

      if ($obs_item != "") {
        ?>
        <div align="left" style="margin-left: 15px">
          <small><b>Observação : </b>
            <?php echo $obs_item ?>
          </small>
        </div>
      <?php } ?>

    </div>


  <?php } ?>

  <div class="th" style="margin-bottom: 7px"></div>


  <?php if ($entrega == "Delivery") { ?>
    <div class="row valores">
      <div class="col-6">Total</div>
      <div class="col-6" align="right">R$ <?php echo @$valor_dos_itensF ?></div>
    </div>
  <?php } ?>

  <?php if ($entrega == "Delivery") { ?>
    <div class="row valores">
      <div class="col-6">Frete</div>
      <div class="col-6" align="right">R$ <?php echo @$taxa_entregaF ?></div>
    </div>
  <?php } ?>

  <?php if ($taxa_extra_pedido > 0) { ?>
    <div class="row valores">
      <div class="col-6"><?php echo $descricao_taxa_extra ?></div>
      <div class="col-6" align="right">R$ <?php echo $taxa_extra_pedidoF ?></div>
    </div>
  <?php } ?>

  <?php if ($cupom > 0) { ?>
    <div class="row valores">
      <div class="col-6">Cupom de Desconto</div>
      <div class="col-6" align="right">R$ <?php echo @$cupomF ?></div>
    </div>
  <?php } ?>

  <div class="row valores">
    <div class="col-6">SubTotal</div>
    <div class="col-6" align="right">R$ <b><?php echo @$valorF ?></b></div>
  </div>


  </tr>


  <?php if ($valor_recebido > 0 && $saldo_pendente > 0) { ?>
    <div class="row valores">
      <div class="col-6">Valor Recebido</div>
      <div class="col-6" align="right">R$ <?php echo @$valor_recebidoF ?></div>
    </div>
  <?php } ?>

  <?php if ($saldo_pendente > 0) { ?>
    <div class="row valores" style="font-weight: bold;">
      <div class="col-6">Acréscimo Pendente</div>
      <div class="col-6" align="right">R$ <?php echo @$saldo_pendenteF ?></div>
    </div>
  <?php } ?>

  <?php if ($troco > 0) { ?>
    <div class="row valores">
      <div class="col-6">Troco</div>
      <div class="col-6" align="right">R$ <?php echo @$trocoF ?></div>
    </div>
  <?php } ?>

  <div class="th" style="margin-bottom: 7px"></div>

  <div class="row valores">
    <div class="col-6">Forma de Pagamento</div>
    <div class="col-6" align="right"><?php echo $descricao_pagamento ?></div>
  </div>

  <div class="row valores">
    <div class="col-6">Forma de Entrega</div>
    <div class="col-6" align="right"><?php echo @$entrega ?></div>
  </div>


  <div class="row valores">
    <div class="col-6">Está Pago</div>
    <div class="col-6" align="right"><b><?php echo @$pago ?></b></div>
  </div>

  <div class="th" style="margin-bottom: 10px"></div>


  <?php if ($entrega == "Delivery") { ?>
    <div class="valores" align="center">
      <b>Endereço para Entrega</b>
      <br>
      <?php echo $rua_cliente ?>   <?php echo $numero_cliente ?>   <?php echo $complemento_cliente ?>
      <?php echo $bairro_cliente ?>   <?php echo $cidade_cliente ?>
    </div>
    <div class="th" style="margin-bottom: 10px"></div>
  <?php } ?>


  <?php if ($obs != "") { ?>
    <div class="valores" align="center">
      <b>Observações do Pedido</b>
      <br>
      <?php echo $obs ?>
    </div>
    <div class="th" style="margin-bottom: 10px"></div>
  <?php } ?>
