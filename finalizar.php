<?php
@session_start();
require_once("cabecalho.php");
require_once("js/ajax/ApiConfig.php");

$sessao = @$_SESSION['sessao_usuario'];
$id_usuario = @$_SESSION['id'];

$sessao_pedido_balcao = @$_SESSION['pedido_balcao'];



$total_carrinho = 0;
$total_carrinhoF = 0;
$query = $pdo->query("SELECT * FROM carrinho where sessao = '$sessao' and empresa = '$id_empresa' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);


if ($total_reg == 0) {
  echo "<script>window.location='index'</script>";
  exit();
} else {
  for ($i = 0; $i < $total_reg; $i++) {
    foreach ($res[$i] as $key => $value) {
    }

    $id = $res[$i]['id'];
    $total_item = $res[$i]['total_item'];
    $produto = $res[$i]['produto'];
    $pedido = $res[$i]['pedido'];
    $quantidade_it = $res[$i]['quantidade'];

    $total_item = $total_item * $quantidade_it;

    $total_carrinho += $total_item;
    
  }
}

$total_carrinho += $taxa_extra;
$total_carrinhoF = number_format($total_carrinho, 2, ',', '.');

$esconder_opc_delivery = '';
$valor_entrega = '';
$clicar_sim = '#collapseTwo';
$numero_colapse = '4';

$taxa_entregaF = 0;
$taxa_entrega = 0;

$nome_cliente = "";
$tel_cliente = "";
$rua = "";
$numero = "";
$bairro = "";
$complemento = "";

$ocultar_retirar = '';
$ocultar_consumir = '';
$ocultar_delivery = '';

if($retirar_local == "Não"){
  $ocultar_retirar = 'none';
}

if($entrega_delivery == "Não"){
  $ocultar_delivery = 'none';
}

if($consumir_local == "Não"){
  $ocultar_consumir = 'none';
}


$visualizar_pagamento = '';
if($somente_pix_delivery == 'Sim'){
  $visualizar_pagamento = 'none';
}

?>
<style>
  .telefone_user::placeholder {
    font-size: 20px;
    color: #999;
  }

  #nome::placeholder {
    font-size: 20px;
    color: #888; /* opcional, muda a cor do placeholder */
  }
</style>

<div class="main-container">

  <nav class="navbar bg-light fixed-top" style="box-shadow: 0px 3px 5px rgba(0, 0, 0, 0.20);">
    <div class="container-fluid">
      <a class="navbar-brand" href="<?php echo $url_site ?>">
        <img src="sistema/img/<?php echo $logo_sistema ?>" alt="" width="80px" class="d-inline-block align-text-top">
        Finalizar Pedido
      </a>

      <?php require_once("icone-carrinho.php") ?>

    </div>
  </nav>



  <div class="accordion" id="accordionExample"
    style="margin-top: 55px; margin-bottom: 130px; overflow: scroll; height:100%; scrollbar-width: thin; z-index: 100">
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingOne">
        <button id="btn_ident" class="accordion-button" type="button" data-bs-toggle="collapse"
          data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="font-size:14px">
          1 - IDENTIFICAÇÃO
        </button>
      </h2>
      <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
        data-bs-parent="#accordionExample">
        <div class="accordion-body" align="center">

         <div style="display: flex; align-items: center; justify-content: center;">
          <i class="fas fa-phone" style="font-size: 22px; margin-right: 5px;"></i>
          <input onkeyup="" type="tel" class="input telefone_user" name="telefone" id="telefone" required value="" placeholder="(00) 00000-0000" style="width: 250px; text-align: center; border: 0; margin-top: -15px; font-size: 22px;">
        </div>

          <img src="img/user.jpg" width="50px" height="50px">

          <div class="nome_user"> <input onclick="" type="text" class="input" name="nome" id="nome" required value="" placeholder="Seu Nome" style="width:200px; text-align: center; border:0; font-size: 17px"></div>
          


          <hr>
          <div><b>Finalizar seu Pedido?</b></div>
          <hr>
          <div class="row">
            <div class="col-6">
              <a href="<?php echo $url_site ?>" class="btn btn-danger botao_nao">NÃO</a>

            </div>

            <div class="col-6">

              <a class="btn btn-success botao_sim" onclick="dados(); buscarNome()">SIM</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="accordion-item <?php echo $esconder_opc_delivery ?>">
      <h2 class="accordion-header" id="headingTwo">
        <button id="colapse-2" class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
          data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" style="font-size:14px">
          2 - MODO DE ENTREGA
        </button>
      </h2>



      <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
        data-bs-parent="#accordionExample">
        <div class="accordion-body">

          <ul class="list-group form-check">

           
            <li style="display:<?php echo $ocultar_retirar ?> !important" onclick="retirar()" class="list-group-item d-flex justify-content-between align-items-center">
              Retirar no Local
              <input onchange="retirar()" class="form-check-input" type="radio" name="radio_retirar" id="radio_retirar">
            </li>
          
            <li style="display:<?php echo $ocultar_consumir ?> !important" onclick="local()" class="list-group-item d-flex justify-content-between align-items-center">
              Consumir no Local
              <input onchange="local()" class="form-check-input" type="radio" name="radio_local" id="radio_local">
            </li>
         
            <li style="display:<?php echo $ocultar_delivery ?> !important" onclick="entrega()" class="list-group-item d-flex justify-content-between align-items-center">
              Entrega Delivery
              <input onchange="entrega()" class="form-check-input" type="radio" name="radio_entrega" id="radio_entrega">
            </li>
            

          </ul>

        </div>
      </div>






    </div>
    <div class="accordion-item <?php echo $esconder_opc_delivery ?>">
      <h2 class="accordion-header" id="headingThree">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
          data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree" id="colapse-3"
          style="font-size:14px">
          3 - ENDEREÇO OU UNIDADE DE RETIRADA
        </button>
      </h2>
      <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
        data-bs-parent="#accordionExample">
        <div class="accordion-body">

          <div id="area-retirada">
            <a href="" class="" data-bs-toggle="collapse" data-bs-target="#collapse4"
              style="text-decoration: none; color:#000">
              <div>
                <b> <span id="consumir-local">Endereço da nossa Loja </span></b><br>
                <?php echo $endereco_sistema ?>
                <i class="bi bi-check-lg"></i>
              </div>
            </a>
          </div>


          <div id="area-endereco">

            <?php if ($entrega_distancia == "Sim" and $chave_api_maps != "") { ?>
              <div class="row">
                <div class="col-4">
                  <div class="group">
                    <input type="text" class="input" name="cep" id="cep" required onblur="calcularFreteDistancia()">
                    <span class="highlight"></span>
                    <span class="bar"></span>
                    <label class="label">CEP*</label>
                  </div>
                </div>


              </div>
            <?php } ?>

            <div class="row">
              <div class="col-7">
                <div class="group">
                  <input type="text" class="input" name="endereco" id="rua" required>

                  <span class="highlight"></span>
                  <span class="bar"></span>
                  <label class="label">Rua* </label>
                </div>
              </div>

              <div class="col-2" style="margin:0px; padding:0px; margin-top: 10px">
                <span><button style="width:30px; height:30px; background:  #3765d0; color:#FFF; border:none"
                    title="Buscar Localização" class="" onclick="localizacao()"> <i class="bi bi-geo-alt"
                      class="text-primary"></i></button></span>

              </div>

              <div class="col-3">
                <div class="group">
                  <input type="text" class="input" name="numero" id="numero" required>
                  <span class="highlight"></span>
                  <span class="bar"></span>
                  <label class="label">Número*</label>
                </div>
              </div>
            </div>


            <div class="row">
              <div class="col-5">
                <div class="group">
                  <input type="text" class="input" name="complemento" id="complemento" required>
                  <span class="highlight"></span>
                  <span class="bar"></span>
                  <label class="label">Complemento</label>
                </div>
              </div>

              <?php if ($entrega_distancia != "Sim" or $chave_api_maps == "") { ?>

         <div class="col-7" style="margin-top:3px">
  <div class="group">
    <select class="input" name="bairro" id="bairro" required style="background: transparent; border: none;" onchange="calcularFrete()">
      <option value="">Selecione um Bairro</option>
      <?php
      $query = $pdo->query("SELECT * FROM bairros where empresa = '$id_empresa' ORDER BY id asc");
      $res = $query->fetchAll(PDO::FETCH_ASSOC);
      $total_reg = @count($res);
      if ($total_reg > 0) {
        for ($i = 0; $i < $total_reg; $i++) {
          $valor = $res[$i]['valor'];
          $valorF = 'R$ ' . number_format($valor, 2, ',', '.');

          if ($res[$i]['nome'] == $bairro) {
            $classe_bairro = 'selected';
          } else {
            $classe_bairro = '';
          }

          echo '<option value="' . $res[$i]['nome'] . '" ' . $classe_bairro . '>' . $res[$i]['nome'] . ' - ' . $valorF . '</option>';
        }
      } else {
        echo '<option value="">Cadastre um Bairro</option>';
      }
      ?>
    </select>
    <span class="highlight"></span>
    <span class="bar"></span>
  </div>
</div>



              <div class="col-12">
                <div class="group">
                  <input type="text" class="input" name="referencia" id="referencia" >
                  <span class="highlight"></span>
                  <span class="bar"></span>
                  <label class="label">Ponto de Referência</label>
                </div>
              </div>

              <?php } else { ?>
                <div class="col-7">
                  <div class="group">
                    <input type="text" class="input" name="bairro" id="bairro" required>
                    <span class="highlight"></span>
                    <span class="bar"></span>
                    <label class="label">Bairro*</label>
                  </div>
                </div>

                <div class="row">

                  <div class="col-12">
                    <div class="group">
                      <input type="text" class="input" name="cidade" id="cidade">
                      <span class="highlight"></span>
                      <span class="bar"></span>
                      <label class="label">Cidade*</label>
                    </div>
                  </div>


                </div>


              <?php } ?>


              <div align="center" class="avancar-pgto">



                <a id="colap-4" href="javascript:void(0)" class="btn btn-success btn-sm" data-bs-toggle="collapse"
                  data-bs-target="#collapse4">Avançar para Pagamento</a>

              </div>


            </div>

          </div>
        </div>
      </div>
    </div>
    <div class="accordion-item">
      <h2 class="accordion-header" id="heading4">
        <button id="collapse-4" class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
          data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
          <?php echo $numero_colapse ?> - PAGAMENTO
        </button>
      </h2>
      <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="heading4"
        data-bs-parent="#accordionExample">
        <div class="accordion-body">

          <div class="row" style="font-size:14px">

            <div class="col-3 form-check" style="display:<?php echo $visualizar_pagamento ?>">
              <small>Dinheiro
                <input onchange="dinheiro()" class="form-check-input" type="radio" name="racio_dinheiro"
                  id="radio_dinheiro">
              </small>
            </div>

            <div class="col-2 form-check">
              <small>Pix
                <input onchange="pix()" class="form-check-input" type="radio" name="radio_pix" id="radio_pix">
              </small>
            </div>


            <div class="col-3 form-check" style="display:<?php echo $visualizar_pagamento ?>">
              <small>Crédito
                <input onchange="credito()" class="form-check-input" type="radio" name="radio_credito"
                  id="radio_credito">
              </small>
            </div>

            <div class="col-3 form-check" style="display:<?php echo $visualizar_pagamento ?>">
              <small>Débito
                <input onchange="debito()" class="form-check-input" type="radio" name="radio_debito" id="radio_debito">
              </small>
            </div>

          </div>

          <?php if($sessao_pedido_balcao == 'BALCÃO' ){ ?>
          <div>
            <br>
            <select class="form-select form-select-sm" name="esta_pago" id="esta_pago" style="width:200px">
              <option value="Não">Não está Pago</option>
              <option value="Sim">Já está Pago</option>
            </select>
          </div>
        <?php } ?>

          <div id="pagar_pix" style="margin-top: 15px">
            <div id="listar_pix">

            </div>
          </div>


          <div id="pagar_dinheiro" style="margin-top: 15px">
            <b>Dinheiro na Entrega </b><br>
            <div class="row">
              <div class="col-5">
                <small>Precisa de Troco? </small>
              </div>
              <div class="col-7" style="margin-top: -13px">
                <div class="group">
                  <input type="text" class="input" name="troco" id="troco">
                  <span class="highlight"></span>
                  <span class="bar"></span>
                  <label class="label">Vou precisar de troco para</label>
                </div>
              </div>
            </div>

          </div>



          <div id="pagar_credito" style="margin-top: 15px">
            <b>Pagar com Cartão de Crédito </b><br>
            <small>O Pagamento será efetuado no ato da entrega com cartão de crédito</small>
          </div>

          <div id="pagar_debito" style="margin-top: 15px">
            <b>Pagar com Cartão de Débito </b><br>
            <small>O Pagamento será efetuado no ato da entrega com cartão de débito</small>
          </div>



        </div>



        <div class="row" style="margin:10px" id="div_cupom">
          <small><b>Tem Cupom?</b></small>
          <div class="col-md-2 col-9">
            <input class="form-control" type="text" name="cupom" id="cupom" placeholder="Código do Cupom">
          </div>
          <div class="col-md-1 col-3">
            <button onclick="cupom()" class="btn btn-primary" type="button" name="btn_cupom" id="btn_cupom"><i
                class="bi bi-search"></i></button>
          </div>
        </div>


        <div class="group mt-4 mx-4" id="area-obs">
          <input type="text" class="input" name="obs" id="obs" value="">
          <span class="highlight"></span>
          <span class="bar"></span>
          <label class="label">Observações do Pedido</label>
        </div>
      </div>



    </div>





  </div>


</div>


<input type="hidden" id="entrega" value="<?php echo $valor_entrega ?>">
<input type="hidden" id="pagamento">
<input type="hidden" id="taxa-entrega-input">
<input type="hidden" id="id_cliente">
<input type="hidden" id="valor-cupom">


<div class="total-finalizar">
  <div class="total-pedido" style="border: solid 1px #ababab; border-radius: 10px;">

   


    <span id="area-taxa">
      <span class="previsao_entrega">Taxa de Entrega: <span class="text-danger">R$ <span id="taxa-entrega"></span>
        </span></span>
      <span class="previsao_entrega mx-2">Previsão <?php echo $previsao_entrega ?> Minutos</span>

    </span>
    <br>


     <?php if($taxa_extra > 0){ ?>
    <span id="area-taxa">
      <span class="previsao_entrega"><?php echo $titulo_taxa ?>: <span class="text-danger">R$ <?php echo $taxa_extra ?> 
    </span>
    <br>
  <?php } ?>

      

    <div id="desconto_div" style="display:none; ">
      <span><b>Desconto do Cupom</b></span>
      <span class="direita"> <b>R$ <span id="total_cupom"></span></b></span>
    </div>
    <br>
    <big>
      <span><b>TOTAL À PAGAR</b></span>
      <span class="direita"> <b>R$ <span id="total-carrinho-finalizar"><?php echo $total_carrinhoF ?></span></b></span>
    </big>
  </div>


  <div class="d-grid gap-2 mt-4 abaixo">
    <a href='javascript:void(0)' id="botao_finalizar" onclick="finalizarPedido()" class="btn btn-warning btn-lg">Concluir
      Pedido</a>

    <div align="center" id="div_img" style="display:none"><img src="img/loading.gif" width="100%" height="40px"></div>
  </div>
</div>


</body>

</html>


<!-- jQery -->
<script src="js/jquery-3.4.1.min.js"></script>

<!-- Mascaras JS -->
<script type="text/javascript" src="js/mascaras.js"></script>

<!-- Ajax para funcionar Mascaras JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- SweetAlert JS -->
<script src="sistema/painel/js/sweetalert2.all.min.js"></script>
<script src="sistema/painel/js/sweetalert1.min.css"></script>
<script src="sistema/painel/js/alertas.js"></script>

<!-- Alertas -->
<script src="sistema/assets/plugins/sweet-alert/sweetalert.min.js"></script>
<script src="sistema/assets/plugins/sweet-alert/jquery.sweet-alert.js"></script>



<script>
  function copiar() {
    document.querySelector("#chave_pix_copia").select();
    document.querySelector("#chave_pix_copia").setSelectionRange(0, 99999); /* Para mobile */
    document.execCommand("copy");
    //$("#chave_pix_copia").hide();
    alertWarning('Chave Pix Copiada! Use a opção Copie e Cole para Pagar')
  }
</script>

<script type="text/javascript">
  function retirar() {
    document.getElementById('radio_retirar').checked = true;
    document.getElementById('radio_local').checked = false;
    document.getElementById('radio_entrega').checked = false;
    $('#colapse-3').click();
    $('#entrega').val('Retirar');
    $('#consumir-local').text('Endereço de Retirada');

    document.getElementById('area-retirada').style.display = "block";
    document.getElementById('area-endereco').style.display = "none";

    document.getElementById('area-taxa').style.display = "none";
    calcularFrete()
  }
</script>


<script type="text/javascript">
  $(document).ready(function() {

    var pedido_balcao = "<?= $sessao_pedido_balcao ?>";



    if (pedido_balcao != 'BALCÃO') {
      //recuperar variaveis de sesssao
      var nome_cl = localStorage.nome_cli_del;
      var tel_cl = localStorage.telefone_cli_del;
      $('#telefone').val(tel_cl);
      $('#nome').val(nome_cl);
    }


    var id_usuario = "";

    $('#telefone').focus();
    document.getElementById('area-endereco').style.display = "none";
    document.getElementById('area-obs').style.display = "none";
    document.getElementById('area-taxa').style.display = "none";

    document.getElementById('pagar_pix').style.display = "none";
    document.getElementById('pagar_debito').style.display = "none";
    document.getElementById('pagar_credito').style.display = "none";
    document.getElementById('pagar_dinheiro').style.display = "none";
  });



  function local() {
    document.getElementById('radio_retirar').checked = false;
    document.getElementById('radio_local').checked = true;
    document.getElementById('radio_entrega').checked = false;
    $('#colapse-3').click();
    $('#entrega').val('Consumir Local');
    $('#consumir-local').text('Endereço da nossa unidade');

    document.getElementById('area-retirada').style.display = "block";
    document.getElementById('area-endereco').style.display = "none";

    document.getElementById('area-taxa').style.display = "none";
    calcularFrete()
  }


  function entrega() {
    document.getElementById('radio_retirar').checked = false;
    document.getElementById('radio_local').checked = false;
    document.getElementById('radio_entrega').checked = true;
    $('#colapse-3').click();
    $('#entrega').val('Delivery');

    document.getElementById('area-retirada').style.display = "none";
    document.getElementById('area-endereco').style.display = "block";

    document.getElementById('area-taxa').style.display = "inline-block";

    var chave_api_maps = "<?= $chave_api_maps ?>";
    var entrega_distancia = "<?= $entrega_distancia ?>";

    if (chave_api_maps == "" || entrega_distancia != "Sim") {
      calcularFrete();
    } else {
      calcularFreteDistancia();
    }

    var access_token = "<?= $access_token ?>";
    if (access_token != "") {
      setTimeout(pix, 1000);
      document.getElementById('radio_pix').checked = true;
    }


  }
</script>


<script type="text/javascript">
  function pix() {

    var entrega = $('#entrega').val();

    var bairro = $('#bairro').val();
    if(bairro === "" && entrega == 'Delivery'){
      return;
    }


    var total_compra = "<?= $total_carrinho ?>";
    total_compra = total_compra.replace(",", ".");
    var taxa_entrega = $('#taxa-entrega-input').val();
    taxa_entrega = taxa_entrega.replace(",", ".");
    var cupom = $('#valor-cupom').val();

    var total_compra_semF = "<?= $total_carrinho ?>";
    var pedido_minimo = "<?= $pedido_minimo ?>";
    if (pedido_minimo > 0) {
      if (parseFloat(total_compra_semF) < parseFloat(pedido_minimo)) {
        alertWarning('Seu pedido precisar ser superior a R$' + pedido_minimo);
        $('#botao_finalizar').show();
        $('#div_img').hide();
        return;
      }
    }

    if (taxa_entrega == "") {
      taxa_entrega = 0;
    }

    if (cupom == "") {
      cupom = 0;
    }

    var total_compra_final = parseFloat(total_compra) + parseFloat(taxa_entrega) - parseFloat(cupom);

    var valor = total_compra_final.toFixed(2)

    $.ajax({
      url: 'js/ajax/pix.php',
      method: 'POST',
      data: {
        valor
      },
      dataType: "html",

      success: function(result) {
        $('#listar_pix').html(result);
      }
    });

    document.getElementById('radio_credito').checked = false;
    document.getElementById('radio_debito').checked = false;
    document.getElementById('radio_dinheiro').checked = false;

    $('#pagamento').val('Pix');

    document.getElementById('pagar_pix').style.display = "block";
    document.getElementById('pagar_debito').style.display = "none";
    document.getElementById('pagar_credito').style.display = "none";
    document.getElementById('pagar_dinheiro').style.display = "none";
    document.getElementById('area-obs').style.display = "block";

    verficarpgto()
  }

  //VERIFICAR SE FOI PAGO NO PIX
  function verficarpgto() {

    var pgto = $('#pagamento').val();
    

    setInterval(function() {

        var pedidoauto = 'Sim';

        if(pgto == "Pix"){
          finalizarPedido(pedidoauto)
        }
        

      },
      90000);

  }

  function dinheiro() {

    document.getElementById('radio_credito').checked = false;
    document.getElementById('radio_debito').checked = false;
    document.getElementById('radio_pix').checked = false;

    $('#pagamento').val('Dinheiro');

    document.getElementById('pagar_pix').style.display = "none";
    document.getElementById('pagar_debito').style.display = "none";
    document.getElementById('pagar_credito').style.display = "none";
    document.getElementById('pagar_dinheiro').style.display = "block";
    document.getElementById('area-obs').style.display = "block";
  }

  function debito() {

    document.getElementById('radio_credito').checked = false;
    document.getElementById('radio_pix').checked = false;
    document.getElementById('radio_dinheiro').checked = false;

    $('#pagamento').val('Cartão de Débito');

    document.getElementById('pagar_pix').style.display = "none";
    document.getElementById('pagar_debito').style.display = "block";
    document.getElementById('pagar_credito').style.display = "none";
    document.getElementById('pagar_dinheiro').style.display = "none";
    document.getElementById('area-obs').style.display = "block";
  }


  function credito() {

    document.getElementById('radio_pix').checked = false;
    document.getElementById('radio_debito').checked = false;
    document.getElementById('radio_dinheiro').checked = false;

    $('#pagamento').val('Cartão de Crédito');

    document.getElementById('pagar_pix').style.display = "none";
    document.getElementById('pagar_debito').style.display = "none";
    document.getElementById('pagar_credito').style.display = "block";
    document.getElementById('pagar_dinheiro').style.display = "none";
    document.getElementById('area-obs').style.display = "block";
  }
</script>


<script type="text/javascript">
  function finalizarPedido(pedidoauto) {



    $('#botao_finalizar').hide();
    $('#div_img').show();

    var pedido_balcao = "<?= $sessao_pedido_balcao ?>";

    var codigo_pix = $('#codigo_pix').val();

    var nome = $('#nome').val();
    var telefone = $('#telefone').val();
    var mesa = $('#mesa').val();
    var id_usuario = "";


    localStorage.setItem('nome_cli_del', nome);
    localStorage.setItem('telefone_cli_del', telefone);



    if(pedido_balcao == ""){

    if (telefone.length < 15) {
      alertWarning("Coloque o número de telefone completo");
      $('#telefone').focus();
      $('#botao_finalizar').show();
      $('#div_img').hide();
      return;
    }


    if (telefone == "") {
      alertWarning('Preencha seu Telefone');
      $('#telefone').focus();
      $('#botao_finalizar').show();
      $('#div_img').hide();
      return;
    }


    if (nome == "") {
      alertWarning('Preencha seu Nome');
      $('#nome').focus();
      $('#botao_finalizar').show();
      $('#div_img').hide();
      return;
    }

  }

    var modo = $('#radio_retirar').val();

    if (modo == "") {
      alertWarning('Escolha um modo de entrega');
      $('#botao_finalizar').show();
      $('#div_img').hide();
      return;
    }

    var nome_cliente = $('#nome').val();
    var tel_cliente = $('#telefone').val();
    var id_cliente = $('#id_cliente').val();


    var pagamento = $('#pagamento').val();
    var entrega = $('#entrega').val();
    var rua = $('#rua').val();
    var numero = $('#numero').val();
    var complemento = $('#complemento').val();
    var bairro = $('#bairro').val();
    var troco = $('#troco').val();
    var obs = $('#obs').val();
    var taxa_entrega = $('#taxa-entrega-input').val();
    var pedido_whatsapp = "<?= $api_whatsapp ?>";
    var cupom = $('#valor-cupom').val();
    var referencia = $('#referencia').val();

    var esta_pago = $('#esta_pago').val();



    var chave_api_maps = "<?= $chave_api_maps ?>";
    var entrega_distancia = "<?= $entrega_distancia ?>";

    if (chave_api_maps == "" || entrega_distancia != "Sim") {
      var cep = "";
      var cidade = "";
    } else {
      var cep = $('#cep').val();
      var cidade = $('#cidade').val();
    }


    if (entrega == "Delivery" && entrega_distancia == 'Sim') {
      if (cep == "") {
        alertWarning("Preencha o CEP");
        $('#cep').focus();
        $('#botao_finalizar').show();
        $('#div_img').hide();
        return;
      }
    }



    if (taxa_entrega == "") {
      alertWarning("Digite um CEP válido para receber seu Pedido, ou atualize a chave da api do google maps para o calculo de frete por distância!");
      $('#botao_finalizar').show();
      $('#div_img').hide();
      $('#colapse-').click();
      return;
    }



    if (cupom == "") {
      cupom = 0;
    }

    if (taxa_entrega == "") {
      taxa_entrega = 0;
    }

    var dataAtual = new Date();
    var horas = dataAtual.getHours();
    var minutos = dataAtual.getMinutes();
    var hora = horas + ':' + minutos;


    var total_compra = "<?= $total_carrinho ?>";
    var pedido_minimo = "<?= $pedido_minimo ?>";


    if (pedido_minimo > 0) {
      if (parseFloat(total_compra) < parseFloat(pedido_minimo)) {
        alertWarning('Seu pedido precisar ser superior a R$' + pedido_minimo);
        $('#botao_finalizar').show();
        $('#div_img').hide();
        return;
      }
    }

    if (entrega == "") {
      alertWarning('Selecione uma forma de entrega');
      $('#colapse-2').click();
      $('#botao_finalizar').show();
      $('#div_img').hide();
      return;
    }



    if (entrega == "Delivery" && rua == "") {
      alertWarning('Preencha o Campo Rua');
      $('#colapse-3').click();
      $('#botao_finalizar').show();
      $('#div_img').hide();

      return;
    }

    if (entrega == "Delivery" && numero == "") {
      alertWarning('Preencha o Campo Número');
      $('#colapse-3').click();
      $('#botao_finalizar').show();
      $('#div_img').hide();
      return;
    }

    if (entrega == "Delivery" && bairro == "") {
      alertWarning('Escolha um Bairro');
      $('#colapse-3').click();
      $('#botao_finalizar').show();
      $('#div_img').hide();
      return;
    }


    if(
      !document.getElementById('radio_pix').checked &&
      !document.getElementById('radio_dinheiro').checked &&
      !document.getElementById('radio_credito').checked &&
      !document.getElementById('radio_debito').checked
    ){
      alertWarning('Selecione uma forma de pagamento');
      $('#botao_finalizar').show();
      $('#div_img').hide();
      $('#collapse-4').click();
      return;
    }
    if (pagamento == "") {
      alertWarning('Selecione uma forma de pagamento');
      $('#botao_finalizar').show();
      $('#div_img').hide();
      $('#collapse-4').click();
      return;
    }

    if (pagamento == "Dinheiro" && troco == "") {
      //alert('Digite o total a ser pago para o troco');
      // $('#botao_finalizar').show(); 
      //$('#div_img').hide();
      //return;
    }

    var total_compra_final = parseFloat(total_compra) + parseFloat(taxa_entrega) - parseFloat(cupom);

    var total_compra_finalF = total_compra_final.toFixed(2)

    if (pagamento == "Dinheiro" && troco < total_compra_final && troco != "") {
      alertWarning('Digite um valor maior que o total da compra!');
      $('#botao_finalizar').show();
      $('#div_img').hide();
      return;
    }


    var abrir_comprovante = "<?= $abrir_comprovante ?>";

    $.ajax({

      url: 'js/ajax/inserir-pedido.php',
      method: 'POST',
      data: {
        pagamento,
        entrega,
        rua,
        numero,
        bairro,
        complemento,
        troco,
        obs,
        nome_cliente,
        tel_cliente,
        id_cliente,
        mesa,
        cupom,
        codigo_pix,
        cep,
        cidade,
        taxa_entrega,
        esta_pago,
        referencia
      },
      dataType: "html",


      success: function(mensagem) {
        //alert(mensagem)


        if (mensagem == 'Pagamento nao realizado!!') {

          if (pedidoauto != 'Sim') {
            alertWarning('Realize o Pagamento antes de Prosseguir!!');
          }

          $('#botao_finalizar').show();
          $('#div_img').hide();
          return;
        }



        var msg = mensagem.split("*")




        $('#mensagem').text('');
        $('#mensagem').removeClass()

        var msg1 = msg[1].trim();
        var msg2 = msg[2].trim();
        var previsao = msg[0].trim();


        if (msg1 == "Pedido Finalizado") {


          if (pedido_balcao == 'BALCÃO') {
            window.close();
          } else {

            setTimeout(() => {
              if (pedidoauto != 'Sim') {
                //alert('Pedido Finalizado!');
                finalizado();

              }


              //chamar o comprovante 
              var id = msg2;
              if (abrir_comprovante != 'Não') {
                window.open("sistema/painel/rel/comprovante2_class.php?id=" + id + "&enviar=sim");
              }

             // função para codificar
              function encodeId(id){
                return btoa(id + '|hugo'); // pequena "salvação" pra evitar decode direto
              }

              var idCodificado = encodeId(id);

              setTimeout(() => {
                window.location = 'pedido/' + idCodificado;
              }, 2000);


            }, 500);

          }



        } else {

        }



        if (pedido_whatsapp == 'manual') {
          let a = document.createElement('a');
          //a.target= '_blank';
          a.href = 'http://api.whatsapp.com/send?1=pt_BR&phone=<?= $tel_whatsapp ?>&text= *Novo Pedido*  %0A%0A Hora: *' + hora + '* %0A Total: R$ *' + total_compra_finalF + '* %0A Entrega: *' + entrega + '* %0A Pagamento: *' + pagamento + '* %0A Cliente: *' + nome_cliente + '* %0A Previsão de Entrega: *' + previsao + '*';
          a.click();
        }



        $('#botao_finalizar').show();
        $('#div_img').hide();

      }
    });



  }
</script>







<script type="text/javascript">
  function calcularFrete() {

    var bairro = $('#bairro').val();
    var total_compra = "<?= $total_carrinho ?>";
    var entrega = $('#entrega').val();

    $.ajax({
      url: 'js/ajax/calcular-frete.php',
      method: 'POST',
      data: {
        bairro,
        total_compra,
        entrega
      },
      dataType: "html",

      success: function(result) {
        var split = result.split("-");
        $('#taxa-entrega').text(split[0]);
        $('#total-carrinho-finalizar').text(split[1]);
        $('#taxa-entrega-input').val(split[0]);


      }
    });


    setTimeout(pix, 1000);
  }
</script>








<script type="text/javascript">
  function buscarNome() {

    var tel = $('#telefone').val();

    $.ajax({
      url: 'js/ajax/listar-nome.php',
      method: 'POST',
      data: {
        tel
      },
      dataType: "text",

      success: function(result) {

        var split = result.split("**");

        $('#rua').val(split[1]);
        $('#numero').val(split[2]);
        $('#bairro').val(split[3]).change();
        $('#complemento').val(split[4]);
        $('#taxa-entrega-input').val(split[5]);
        $('#taxa-entrega').text(split[6]);
        $('#id_cliente').text(split[7]);
        $('#cep').val(split[8]);
        $('#cidade').val(split[9]);
        $('#referencia').val(split[10]);
      }
    });
  }
</script>



<script type="text/javascript">
  function dados() {

    //buscarNome()

    $('#nome').show();

    var nome = $('#nome').val();
    var telefone = $('#telefone').val();
    var id_usuario = "";

     var pedido_balcao = "<?= $sessao_pedido_balcao ?>";

    if(pedido_balcao == ""){

      if (telefone.length < 15) {
        alertWarning("Coloque o número de telefone completo");
        return;
      }

      if (telefone == "") {
        alertWarning('Preencha seu Telefone');
        $('#telefone').focus();
        return;
      }


      if (nome == "") {
        alertWarning('Preencha seu Nome');
        $('#telefone').focus();
        return;
      }

    }

    $('#colapse-2').click();


  }




  function cupom() {
    var total_compra = "<?= $total_carrinho ?>";
    var taxa_entrega = $('#taxa-entrega-input').val();
    var telefone_cliente = $('#telefone').val();
    if (taxa_entrega == "") {
      taxa_entrega = 0;
    }
    var total_final = parseFloat(total_compra) + parseFloat(taxa_entrega);
    var codigo_cupom = $('#cupom').val();
    if (codigo_cupom == "") {
      alertWarning("Preencha o código do cupom");
      return;
    }

    $.ajax({
      url: 'js/ajax/cupom.php',
      method: 'POST',
      data: {
        total_final,
        codigo_cupom,
        telefone_cliente
      },
      dataType: "text",

      success: function(mensagem) {
        var split = mensagem.split('**')


        if (split[0].trim() == '0') {
          alertWarning('Código do Cupom Inválido');
        } else if (split[0].trim() == '1') {
          alertWarning('Este cupom está vencido!');
        } else if (split[0].trim() == '2') {
          alertWarning('Este cupom não é mais válido!');
        } else if (split[0].trim() == '3') {
          alertWarning('Este cupom só é válido para compras acima de R$' + split[1]);
        } else {

          $('#total-carrinho-finalizar').text(split[0]);
          $('#valor-cupom').val(split[1]);
          $('#div_cupom').hide();

          $('#desconto_div').show();
          $('#total_cupom').text(split[2]);


          alertWarning('Cupom Inserido!');
          pix();
        }

      },

    });
  }
</script>



<script>
  var restaurantCoords = {
    latitude: "<?= $latitude_rest ?>",
    longitude: "<?= $longitude_rest ?>"
  };


  document.getElementById('cep').addEventListener('input', function(event) {
    var cep = event.target.value.replace(/\D/g, '');

    if (cep.length === 8) {
      fetchAddressData(cep);
    }
  });

  function calcularFreteDistancia() {

    var chave_api = "<?= $chave_api_maps ?>";
    var distancia_km = "<?= $distancia_entrega_km ?>";

    event.preventDefault();
    var cep = document.getElementById('cep').value;
    var address = document.getElementById('rua').value;
    var number = document.getElementById('numero').value;
    var bairro = document.getElementById('bairro').value;
    var cidade = document.getElementById('cidade').value;
    var complement = document.getElementById('complemento').value;

    if (cep == "") {
      return;
    }

    var encodedAddress = encodeURIComponent(address + ', ' + number + ', ' + bairro + ', ' + cidade + ', ' + complement);
    //alert(encodedAddress)

    fetch('https://maps.googleapis.com/maps/api/geocode/json?address=' + encodedAddress + '&key=' + chave_api)
      .then(function(response) {
        return response.json();
      })
      .then(function(data) {
        if (data.status === 'OK' && data.results.length > 0) {

          var deliveryCoords = {

            latitude: data.results[0].geometry.location.lat,
            longitude: data.results[0].geometry.location.lng
          };

          var distance = calculateDistance(restaurantCoords, deliveryCoords);

          //alert(distance)
          //document.getElementById('distancia').innerText = distance.toFixed(2) + ' KM';

          if (distance <= distancia_km) {
            var deliveryCost = calculateDeliveryCost(distance);
            $('#taxa-entrega').text(deliveryCost.toFixed(2));
            $('#taxa-entrega-input').val(deliveryCost);

            var total_compra = "<?= $total_carrinho ?>";
            var total_final_compra = parseFloat(deliveryCost) + parseFloat(total_compra);
            $('#total-carrinho-finalizar').text(total_final_compra.toLocaleString('pt-br', {
              minimumFractionDigits: 2
            }));

          } else {
            alertWarning('Endereço fora da área de entrega.');
            return;
          }
        } else {
          alertWarning('Endereço inválido ou Chave Api Inexistente.');
          return;
        }
      })
      .catch(function(error) {
        console.error(error);
      });
  }

  function fetchAddressData(cep) {
    fetch('https://viacep.com.br/ws/' + cep + '/json/')
      .then(function(response) {
        return response.json();
      })
      .then(function(data) {
        if (!data.erro) {
          document.getElementById('rua').value = data.logradouro;
          document.getElementById('bairro').value = data.bairro;
          document.getElementById('cidade').value = data.localidade;
          document.getElementById('numero').focus();

        }
      })
      .catch(function(error) {
        console.error(error);
      });
  }

  function calculateDistance(coord1, coord2) {
    var lat1 = toRadians(coord1.latitude);
    var lon1 = toRadians(coord1.longitude);
    var lat2 = toRadians(coord2.latitude);
    var lon2 = toRadians(coord2.longitude);

    var R = 6371; // Raio da Terra em quilômetros

    var dLat = lat2 - lat1;
    var dLon = lon2 - lon1;

    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(lat1) * Math.cos(lat2) *
      Math.sin(dLon / 2) * Math.sin(dLon / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    var distance = R * c;
    return distance;
  }

  function toRadians(degrees) {
    return degrees * (Math.PI / 180);
  }

  function calculateDeliveryCost(distance) {
    var valor_km = "<?= $valor_km ?>";
    if (valor_km == "" || valor_km <= 0) {
      valor_km = 1;
    }
    var roundedDistance = Math.ceil(distance);

    //aqui entra a verificação solicitada
    /*
    if(roundedDistance <= 3){
      return 5;
    }else if (roundedDistance > 3 && roundedDistance <= 6){
      return 7;
    }else if (roundedDistance > 6 && roundedDistance <= 9){
      return 10;
    }
    */

    //abaixo calculo com base no valor das configurações;

    if(valor_km == "" || valor_km <= 0){
        valor_km = 1;
      }
      var roundedDistance = Math.ceil(distance);
      return roundedDistance * valor_km;

  }
</script>




<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script>
  var x = document.getElementById("demo");

  function localizacao() {
    $("#rua").val('Carregando!!');

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else {
      x.innerHTML = "Geolocalização não é suportada nesse browser.";
    }


  }

  function showPosition(position) {
    lat = position.coords.latitude;
    lon = position.coords.longitude;

    geo(lat, lon);
  }

  function showError(error) {
    switch (error.code) {
      case error.PERMISSION_DENIED:
        x.innerHTML = "Usuário rejeitou a solicitação de Geolocalização."
        break;
      case error.POSITION_UNAVAILABLE:
        x.innerHTML = "Localização indisponível."
        break;
      case error.TIMEOUT:
        x.innerHTML = "O tempo da requisição expirou."
        break;
      case error.UNKNOWN_ERROR:
        x.innerHTML = "Algum erro desconhecido aconteceu."
        break;
    }
  }
</script>



<script type="text/javascript">
  function geo(latitude, longitude) {

    $.getJSON('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + latitude + '&lon=' + longitude, function(res) {

      var entrega_distancia = "<?= $entrega_distancia ?>";

      //rua
      $("#rua").val(res.address.road);



      //cidade ver qual
      var cidade = res.address.city;

      if (cidade == "" || cidade == null) {
        //TENTAR PEGAR POR TOWN
        var cidade = res.address.town;

        if (cidade == "" || cidade == null) {
          var cidade = res.address.village;
        }
      }


      //cep
      $("#cep").val(res.address.postcode);

      //cidade
      $("#cidade").val(cidade);


      //bairo
      if (entrega_distancia == 'Sim') {
        $("#bairro").val(res.address.suburb);
        calcularFreteDistancia()
      } else {
        //$("#bairro").val(res.address.suburb).change();
      }




    });
  };
</script>




<!-- Link para o CSS do Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<!-- Inicialização do Select2 -->
<script>
  $(document).ready(function() {
    $('#bairro').select2({
      placeholder: "Selecione um Bairro", // Placeholder
      allowClear: true, // Permite limpar a seleção
      width: '100%' // Para garantir que ocupe toda a largura disponível
    });
  });
</script>

<!-- CSS Customizado para o Select2 -->
<style>
  /* Remover borda extra do Select2 e adicionar borda inferior */
  .select2-container--default .select2-selection--single {
    border: none !important;
    border-bottom: 1px solid #000 !important; /* Linha simples abaixo do input */
    height: 38px !important; /* Ajusta a altura do campo */
  }

  /* Ajustar a altura do input para ficar alinhado */
  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 38px !important; /* Ajuste a altura para ficar igual ao seu input */
    font-size: 17px !important; /* Ajuste o tamanho da fonte conforme necessário */
  }

  /* Estilo do dropdown do Select2 */
  .select2-container--default .select2-selection--single .select2-selection__arrow {
    top: 10px; /* Posiciona a seta corretamente */
  }

  /* Estilo do campo do select quando estiver em foco */
  .select2-container--default .select2-selection--single:focus {
    border-color: #000 !important; /* Caso precise adicionar um foco */
  }

  .select2-container--default .select2-selection--single {
  border-radius: 0 !important;
}

</style>
