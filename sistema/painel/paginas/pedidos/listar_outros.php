<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
$tabela = 'carrinho';
$data_hoje = date('Y-m-d');

$status = '%' . @$_POST['status'] . '%';
$origem = @$_POST['origem'];
$ult_ped = @$_POST['ult_pedido'];

$total_vendas = 0;

$sql_mesa = '';
$sql_concluido_base = " AND EXISTS (SELECT 1 FROM vendas vc WHERE vc.id = c.pedido AND vc.empresa = c.empresa)";
$sql_concluido_lista = " AND EXISTS (SELECT 1 FROM vendas vc WHERE vc.id = carrinho.pedido AND vc.empresa = carrinho.empresa)";

$sql_origem_base = '';
$sql_origem_lista = '';
if ($origem === 'Delivery') {
  $sql_origem_base = " AND EXISTS (SELECT 1 FROM vendas vo WHERE vo.id = c.pedido AND vo.empresa = c.empresa AND vo.entrega LIKE '%Delivery%')";
  $sql_origem_lista = " AND EXISTS (SELECT 1 FROM vendas vo WHERE vo.id = carrinho.pedido AND vo.empresa = carrinho.empresa AND vo.entrega LIKE '%Delivery%')";
} elseif ($origem === 'Mesa') {
  $sql_origem_base = " AND COALESCE(c.mesa, 0) > 0";
  $sql_origem_lista = " AND COALESCE(mesa, 0) > 0";
} elseif ($origem === 'Balcao') {
  $sql_origem_base = " AND COALESCE(c.mesa, 0) = 0 AND EXISTS (SELECT 1 FROM vendas vo WHERE vo.id = c.pedido AND vo.empresa = c.empresa AND (vo.tipo_pedido LIKE '%Balc%' OR vo.entrega LIKE '%Consum%Local%'))";
  $sql_origem_lista = " AND COALESCE(mesa, 0) = 0 AND EXISTS (SELECT 1 FROM vendas vo WHERE vo.id = carrinho.pedido AND vo.empresa = carrinho.empresa AND (vo.tipo_pedido LIKE '%Balc%' OR vo.entrega LIKE '%Consum%Local%'))";
}

//TOTAIS DOS PEDIDOS
// base da consulta: só itens da cozinha
$base = "FROM $tabela c 
         INNER JOIN produtos p ON p.id = c.produto
         WHERE p.preparado = 'Outro' $sql_mesa $sql_origem_base $sql_concluido_base";

// Aguardando
$query = $pdo->query("SELECT * $base AND c.status = 'Aguardando' and c.empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_ini = @count($res);

// Preparando
$query = $pdo->query("SELECT * $base AND c.status = 'Preparando' and c.empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_prep = @count($res);

// Pronto
$query = $pdo->query("SELECT * $base AND c.status = 'Pronto' and c.empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_ent = @count($res);

// Todos (Aguardando, Preparando ou Pronto)
$query = $pdo->query("SELECT * $base 
                      AND (c.status = 'Aguardando' 
                       OR  c.status = 'Preparando' 
                       OR  c.status = 'Pronto') and c.empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_pedidos = @count($res);

$query = $pdo->query("SELECT * FROM $tabela where status != 'Finalizado' and status != 'Cancelado' and empresa = '$id_empresa' $sql_concluido_lista order by id desc limit 1");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$id_ult_pedido = @$res[0]['id'];


if ($ult_ped < $id_ult_pedido and $ult_ped != "") {
  echo '<audio autoplay="true">
<source src="../../img/audio.mp3" type="audio/mpeg" />
</audio>';
}

if ($ult_ped == "" and $id_ult_pedido != "") {
  echo '<audio autoplay="true">
<source src="../../img/audio.mp3" type="audio/mpeg" />
</audio>';
}

$ids = '';
$query = $pdo->query("SELECT * FROM $tabela where status LIKE '$status' and status != 'Finalizado' and status != 'Cancelado' and empresa = '$id_empresa' $sql_mesa $sql_origem_lista $sql_concluido_lista order by data asc, hora asc, id asc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {


  for ($i = 0; $i < $total_reg; $i++) {
    foreach ($res[$i] as $key => $value) {
    }
    $id = $res[$i]['id'];
    $total_item = $res[$i]['total_item'];
    $produto = $res[$i]['produto'];
    $quantidade = $res[$i]['quantidade'];
    $obs = $res[$i]['obs'];
    $item = $res[$i]['item'];
    $variacao = $res[$i]['variacao'];
    $nome_produto_tab = $res[$i]['nome_produto'];
    $sabores = $res[$i]['sabores'];
    $borda = $res[$i]['borda'];
    $categoria = $res[$i]['categoria'];
    $valor_unit = $res[$i]['valor_unitario'];
    $status = $res[$i]['status'];
    $hora = $res[$i]['hora'];
    $data_pedido = $res[$i]['data'];
    $ab_mesa = $res[$i]['mesa'];
    $obs_item = $res[$i]['obs'];
    $pedido = $res[$i]['pedido'];


    if ($status == "") {
      continue;
    }


    $horaF = date("H:i", strtotime($hora));
    $dataF = $data_pedido != '' ? date("d/m/Y", strtotime($data_pedido)) : '';

    $query2 = $pdo->query("SELECT * FROM abertura_mesa where id = '$ab_mesa'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    $total_reg2 = @count($res2);
    if($total_reg2 > 0){
      $garcon = $res2[0]['garcon'];
      $mesa = $res2[0]['mesa'];
    }else{
      $garcon = 0;
      $mesa = 0;
    }
    

    $origem_pedido = 'Balcão';
    $cor_origem = '#6c757d';

    if ($total_reg2 > 0) {
      $origem_pedido = 'Mesa';
      $cor_origem = '#7b2cbf';
    }

    $query2 = $pdo->query("SELECT * FROM vendas where id = '$pedido' and empresa = '$id_empresa'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    $total_reg2 = @count($res2);
    if ($total_reg2 > 0) {
      $id_pedido = $res2[0]['pedido'];

      $tipo_pedido = trim((string) @$res2[0]['tipo_pedido']);
      $tipo_entrega = trim((string) @$res2[0]['entrega']);

      if (stripos($tipo_pedido, 'balc') !== false) {
        $origem_pedido = 'Balcão';
        $cor_origem = '#6c757d';
      } elseif (stripos($tipo_entrega, 'delivery') !== false) {
        $origem_pedido = 'Delivery';
        $cor_origem = '#dc3545';
      } elseif (stripos($tipo_entrega, 'retir') !== false) {
        $origem_pedido = 'Retirada';
        $cor_origem = '#fd7e14';
      } elseif (stripos($tipo_entrega, 'consum') !== false) {
        $origem_pedido = 'Consumir Local';
        $cor_origem = '#198754';
      } elseif ($tipo_pedido != '') {
        $origem_pedido = $tipo_pedido;
        $cor_origem = '#0d6efd';
      } elseif ($tipo_entrega != '') {
        $origem_pedido = $tipo_entrega;
        $cor_origem = '#0d6efd';
      }
    } else {
      $id_pedido = '';
    }


    $query2 = $pdo->query("SELECT * FROM mesas where id = '$mesa'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    if(@count($res2) > 0){
      $nome_mesa = 'Mesa '.@$res2[0]['nome'];
    }else{
      $nome_mesa = 'Pedido '.$id_pedido;
    }
    


    $query2 = $pdo->query("SELECT * FROM usuarios where id = '$garcon'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    $total_reg2 = @count($res2);
    if ($total_reg2 > 0) {
      $nome_garcon = $res2[0]['nome'];
    } else {
      $nome_garcon = '';
    }


    


    if ($status == 'Aguardando') {
      $classe_alerta = '#DC3545';
      $class_btn = 'btn-danger';
      $titulo_link = 'Mudar para Preparando!';
      $cor_icone_link = '#DC3545';
      $acao_link = 'Preparando';
    } else if ($status == 'Preparando') {
      $classe_alerta = '#0097ff';
      $class_btn = 'btn-info';
      $titulo_link = 'Mudar para Pronto!';
      $cor_icone_link = '#0097ff';
      $acao_link = 'Pronto';
    } else {
      $class_btn = 'btn-success';
      $classe_alerta = '#00dc49';
      $titulo_link = 'Mudar para Finalizado!';
      $cor_icone_link = '#00dc49';
      $acao_link = 'Finalizado';
    }



    if ($obs != "") {
      $classe_info = 'warning';
    } else {
      $classe_info = 'info';
    }


    $query2 = $pdo->query("SELECT * FROM variacoes where id = '$variacao'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    if (@count(@$res2) > 0) {
      $sigla_variacao = '(' . $res2[0]['sigla'] . ')';
    } else {
      $sigla_variacao = '';
    }



    $query2 = $pdo->query("SELECT * FROM temp where carrinho = '$id' and tabela = 'Variação' order by id asc limit 1");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    $id_do_item = @$res2[0]['id_item'];

    $query2 = $pdo->query("SELECT * FROM itens_grade where id = '$id_do_item'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    if (@$res2[0]['texto'] != "") {
      $sigla_grade = @$res2[0]['texto'];
    } else {
      $sigla_grade = '';
    }

   $query2 = $pdo->query("SELECT * FROM produtos where id = '$produto'");
  $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
  if (@count(@$res2) > 0) {
      $nome_produto = $res2[0]['nome'];
      $foto_produto = $res2[0]['foto'];
      $unidade = $res2[0]['unidade'];
      $preparado = $res2[0]['preparado'];
    } else {
      $nome_produto = $nome_produto_tab;
      $foto_produto = "";
      $unidade = "";
      $preparado = "";
    }

    if($preparado != 'Outro'){
      continue;
    }


    $query3 = $pdo->query("SELECT * FROM unidade_medida where id = '$unidade'");
      $res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
      if(@count($res3) > 0){
        $nome_unidade = $res3[0]['nome'];
      }else{
        $nome_unidade = 'Sem Unidade';
      }


    $ocultar_quantidades = '';
      $sigla_unidade = '';
      if($nome_unidade == 'Quilogramas' or $nome_unidade == 'Quilo' or $nome_unidade == 'Quilograma' or $nome_unidade == 'KG'){
        $sigla_unidade = ' (KG)';
        $ocultar_quantidades = 'ocultar';
      }

      if($nome_unidade == 'Metros' or $nome_unidade == 'Metro' or $nome_unidade == 'M' or $nome_unidade == 'm'){
        $sigla_unidade = ' (m)';
        $ocultar_quantidades = 'ocultar';
      }

      if($nome_unidade == 'Litro' or $nome_unidade == 'Litros' or $nome_unidade == 'L'){
        $sigla_unidade = ' (L)';
        $ocultar_quantidades = 'ocultar';
      }

      //tratamento separa string
      $qt = explode(".", $quantidade);
      if($qt[1] > 0){
        $quantidadeF = $quantidade;   
      }else{
        $quantidadeF = $qt[0];
      }


    $tabela_ad = 'adicionais';
    if ($sabores > 0) {
      $nome_produto = $nome_produto_tab;
      $tabela_ad = 'adicionais_cat';
    }

    $total_reg8 = 0;
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


    echo <<<HTML
<div class="col-md-4 widget d-inline-block" style="width: 30%">
      <div class="" style="padding:4px; min-height: 100px; height: 200px; overflow: scroll; scrollbar-width: thin; background: #ffffff;">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; border-bottom:1px solid #000; padding-bottom:3px">
          <span style="min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap"><span style="color:{$classe_alerta}"><i class="fa fa-square"></i></span> <b>{$nome_mesa}</b></span>
          <span style="flex-shrink:0; padding:2px 5px; border-radius:4px; background:#f2f4f7; color:#475467; font-size:9px; white-space:nowrap"><i class="fa fa-calendar"></i> {$dataF} &nbsp; <i class="fa fa-clock-o"></i> {$horaF}</span>
        </div>

        <div style="margin-top: 5px">
          <span><small><i><span style="color:#000">{$nome_garcon} </span></i></small></span>  
          <a style="position:absolute; right:20px" class="btn btn-dark-light btn-sm" href="#" onclick="gerarComprovante('{$id}')" title="Gerar Comprovante"><i class="fa fa-file-pdf-o"></i></a>        
        </div>

        <div style="margin: 3px 0 5px 0">
          <small><b>Origem:</b></small>
          <span style="display:inline-block; padding:2px 7px; border-radius:10px; color:#fff; background:{$cor_origem}; font-size:10px; font-weight:600">{$origem_pedido}</span>
        </div>

        <div style="border-bottom: 1px solid #000">
          <span id="area_status_{$id}"><small><i>Status <b>
            <a title="{$titulo_link}" href="#" onclick="ativarPedido('{$id}','{$acao_link}')" class="btn {$class_btn} btn-rounded mb-1" style="font-size: 9px; padding: 5px">{$status}<i class="fa fa-arrow-right {$cor_icone_link}"></i>
          </a></b></i></small></span>   
<img id="status_link_{$id}" src="../../img/loading.gif" width="80px" style="display:none">      
        </div>

          <div style=" padding-bottom: 5px; margin-top: 5px">
        <small>
          <span> <b><big>{$quantidadeF} {$sigla_unidade} - {$nome_produto} {$sigla_variacao} {$sigla_grade}</big></b></span><br>
HTML;


    if ($sabores > 1) {
      echo '<span style="margin-left: 15px"><b>Atenção: </b>' . $sabores . ' Sabores</span><br>';
    }

    if (@$total_reg8 > 0) {
      echo '<span style="margin-left: 15px"><b>Borda: </b>' . $nome_borda . '</span><br>';
    }


    $query2 = $pdo->query("SELECT * FROM temp where carrinho = '$id' and tabela = 'adicionais'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    $total_reg2 = @count($res2);
    if ($total_reg2 > 0) {
      if ($total_reg2 > 1) {
        $texto_adicional = '(' . $total_reg2 . ') Tipos de Adicionais';
      } else {
        $texto_adicional = '(' . $total_reg2 . ') Tipo de Adicional';
      }
      echo <<<HTML

    <div align="left" style="margin-left: 15px">
      <b>{$texto_adicional} : </b>
HTML;
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
      echo <<<HTML
      
    </div>
HTML;
    }







    $query2 = $pdo->query("SELECT * FROM temp where carrinho = '$id' and tabela = 'guarnicoes'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    $total_reg2 = @count($res2);
    if ($total_reg2 > 0) {
      if ($total_reg2 > 1) {
        $texto_adicional = '(' . $total_reg2 . ') Guarnições';
      } else {
        $texto_adicional = '(' . $total_reg2 . ') Guarnição';
      }
      echo <<<HTML

    <div align="left" style="margin-left: 15px">
      <small><b>{$texto_adicional} : </b>
HTML;
      for ($i2 = 0; $i2 < $total_reg2; $i2++) {
        foreach ($res2[$i2] as $key => $value) {
        }
        $id_temp = $res2[$i2]['id'];
        $id_item = $res2[$i2]['id_item'];

        $query3 = $pdo->query("SELECT * FROM guarnicoes where id = '$id_item'");
        $res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
        $total_reg3 = @count($res3);
        $nome_adc = $res3[0]['nome'];
        echo $nome_adc;
        if ($i2 < ($total_reg2 - 1)) {
          echo ', ';
        }
      }
      echo <<<HTML
      </small>
    </div>
HTML;
    }




    $query2 = $pdo->query("SELECT * FROM temp where carrinho = '$id' and tabela = 'ingredientes'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    $total_reg2 = @count($res2);
    if ($total_reg2 > 0) {
      if ($total_reg2 > 1) {
        $texto_adicional = '(' . $total_reg2 . ') Retirar Ingredientes';
      } else {
        $texto_adicional = '(' . $total_reg2 . ') Retirar Ingrediente';
      }
      echo <<<HTML
    <div align="left" style="margin-left: 15px">
      <small><b>{$texto_adicional} : </b>
HTML;
      for ($i2 = 0; $i2 < $total_reg2; $i2++) {
        foreach ($res2[$i2] as $key => $value) {
        }
        $id_temp = $res2[$i2]['id'];
        $id_item = $res2[$i2]['id_item'];

        $query3 = $pdo->query("SELECT * FROM ingredientes where id = '$id_item'");
        $res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
        $total_reg3 = @count($res3);
        $nome_adc = $res3[0]['nome'];
        echo $nome_adc;
        if ($i2 < ($total_reg2 - 1)) {
          echo ', ';
        }
      }
      echo <<<HTML
      </small>
    </div>
HTML;
    }





    //percorrer as grades do produto
    $query20 = $pdo->query("SELECT * FROM grades where produto = '$produto' and tipo_item != 'Variação'");
    $res20 = $query20->fetchAll(PDO::FETCH_ASSOC);
    $total_reg20 = @count($res20);
    if ($total_reg20 > 0) {
      for ($i20 = 0; $i20 < $total_reg20; $i20++) {
        $id_da_grade = $res20[$i20]['id'];
        $nome_da_grade = $res20[$i20]['nome_comprovante'];
        $tipo_item_grade = $res20[$i20]['tipo_item'];


        //buscar os itens selecionados pela grade
        $query2 = $pdo->query("SELECT * FROM temp where carrinho = '$id' and grade = '$id_da_grade'");
        $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
        $total_reg2 = @count($res2);
        if ($total_reg2 > 0) {

          echo '<b>' . $nome_da_grade . ' </b>';;

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
            echo '<i>' . $quant_item . $nome_item . '</i>';
          }

          echo '<br>';
        }
      }
    }




    if ($obs_item != "") {
      echo <<<HTML
    <div align="left" style="margin-left: 15px; color:blue">
      <b>Observação : </b>
        {$obs_item}
          
    </div>
HTML;
    }










    echo <<<HTML

</small>    
        </div>

      
        
      </div>
    </div>  
HTML;
  }
} else {
  echo '<small>Não possui nenhum Pedido Hoje ainda!</small>';
}



$query = $pdo->query("SELECT * FROM carrinho where data = CurDate() and status = 'Iniciado'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_dos_itens_pedidos = @count($res);


?>

<script type="text/javascript">
  $(document).ready(function() {

    var ids = "<?= $ids ?>";
    var id_imp = ids.split("-");
    //alert(ids)

    for (i = 0; i < id_imp.length - 1; i++) {
      var id_pedido = id_imp[i];

      let a = document.createElement('a');
      a.target = '_blank';
      a.href = 'rel/comprovante.php?id=' + id_pedido;
      a.click();
    }



    $('#todos_pedidos').text("<?= $total_pedidos ?>");
    $('#ini_pedidos').text("<?= $total_ini ?>");
    $('#prep_pedidos').text("<?= $total_prep ?>");
    $('#ent_pedidos').text("<?= $total_ent ?>");
    $('#id_pedido').val("<?= $id_ult_pedido ?>");

    $('#total-dos-pedidos').text("<?= $total_dos_itens_pedidos ?>");

    
  });
</script>






<script type="text/javascript">
  function ativarPedido(id, acao) {

    $('#status_link_' + id).show();
    $('#area_status_' + id).hide();

    setTimeout(function() {


      $.ajax({
        url: 'paginas/' + pag + "/mudar-status_mesa.php",
        method: 'POST',
        data: {
          id,
          acao
        },
        dataType: "text",

        success: function(mensagem) {
          var split = mensagem.split("***");

          if (split[0] == "Alterado com Sucesso") {
            listar();

          } else {
            $('#mensagem-excluir').addClass('text-danger')
            $('#mensagem-excluir').text(mensagem)
          }

          $('#status_link_' + id).hide();
          $('#area_status_' + id).show();

        },



      });

    }, 1000);
  }
</script>


<script type="text/javascript">
  function gerarComprovante(id) {


    let a = document.createElement('a');
    a.target = '_blank';
    a.href = 'rel/comprovante_item_mesa.php?id=' + id;
    a.click();
  }
</script>
