<?php
@session_start();
$url_site = $_GET['url'];
$_SESSION['url_site'] = $url_site;

// O acesso pelo link "Novo Pedido" encerra qualquer contexto anterior de
// edição, mesa ou balcão antes de carregar o cardápio da empresa.
if (@$_GET['novo_pedido'] == '1') {
  unset(
    $_SESSION['id_edicao'],
    $_SESSION['id_mesa'],
    $_SESSION['nome_mesa'],
    $_SESSION['id_ab_mesa'],
    $_SESSION['sessao_usuario'],
    $_SESSION['pedido_balcao']
  );
}

require_once("cabecalho.php");



$id_ab_mesa = "";
$id_mesa = @$_POST['id_mesa'];
$pedido_balcao = @$_POST['pedido_balcao'];

$url_instagram = 'https://www.instagram.com/' . $instagram_sistema . '/';

if ($pedido_balcao != "") {
  unset($_SESSION['id_mesa'], $_SESSION['nome_mesa'], $_SESSION['id_ab_mesa'], $_SESSION['sessao_usuario'], $_SESSION['id_edicao']);
  $_SESSION['pedido_balcao'] = $pedido_balcao;

  
}

@$sessão_balcao = $_SESSION['pedido_balcao'];

if ($sessão_balcao != '') {
  $nome_sistema = 'Pedido Balcão';
}


if ($id_mesa != "") {
  $_SESSION['id_mesa'] = $id_mesa;
  unset($_SESSION['id_edicao']);
}

if (@$_SESSION['id_mesa'] != "") {
  $id_mesa = $_SESSION['id_mesa'];
  unset($_SESSION['id_edicao']);
}



//buscar informações da edição pedido
$id_edicao = @$_POST['id_edicao'];

if ($id_edicao != "") {
  $_SESSION['id_edicao'] = $id_edicao;
  unset($_SESSION['id_mesa'], $_SESSION['nome_mesa'], $_SESSION['id_ab_mesa'], $_SESSION['sessao_usuario']);
}

if (@$_SESSION['id_edicao'] != "") {
  $id_edicao = $_SESSION['id_edicao'];
  unset($_SESSION['id_mesa'], $_SESSION['nome_mesa'], $_SESSION['id_ab_mesa'], $_SESSION['sessao_usuario']);
}

if ($id_edicao != "") {
  require_once('sistema/pedido_financeiro.php');
  if (!pedidoPermiteEdicao($pdo, $id_edicao, $id_empresa)) {
    unset($_SESSION['id_edicao']);
    echo '<script>alert("Este pedido já saiu para entrega e não pode mais ser editado!"); window.close();</script>';
    exit();
  }
}

//buscar os dados da mesa
$query2 = $pdo->query("SELECT * FROM mesas where id = '$id_mesa' ");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$nome_mesa = 'Mesa: ' . @$res2[0]['nome'];

if (@$res2[0]['nome'] == "") {
  $_SESSION['nome_mesa'] = '';
} else {
  $_SESSION['nome_mesa'] = $nome_mesa;
}

if($id_mesa > 0){
  $query2 = $pdo->query("SELECT * FROM abertura_mesa where mesa = '$id_mesa' and status = 'Aberta'");
  $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
  if(@count($res2) == 0){
    $pdo->query("INSERT INTO abertura_mesa SET mesa = '$id_mesa', cliente = '$nome_mesa', data = curDate(), horario_abertura = curTime(), garcon = '0', status = 'Aberta'");
    $pdo->query("UPDATE mesas set status = 'Aberta' where id = '$id_mesa'");
  }

  $query2 = $pdo->query("SELECT * FROM abertura_mesa where mesa = '$id_mesa' and status = 'Aberta'");
  $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
  $id_ab_mesa = @$res2[0]['id'];
  $_SESSION['id_ab_mesa'] = $id_ab_mesa;
}




$img = 'aberto.png';


if($id_edicao == ""){

    // 🔴 PRIORIDADE MÁXIMA: DELIVERY PAUSADO
  if (@$pausar_delivery == 'Sim' and $id_mesa == "" and $sessão_balcao == "") {
    $img = 'pausado.png'; // crie essa imagem
  } else {

      if ($status_estabelecimento == "Fechado" and $id_mesa == "" and $sessão_balcao == "") {
        $img = 'fechado.png';
      }

      if ($id_mesa == "" and $sessão_balcao == "") {
        $data = date('Y-m-d');
        //verificar se está aberto hoje
        $diasemana = array("Domingo", "Segunda-Feira", "Terça-Feira", "Quarta-Feira", "Quinta-Feira", "Sexta-Feira", "Sábado");
        $diasemana_numero = date('w', strtotime($data));
        $dia_procurado = $diasemana[$diasemana_numero];

        //percorrer os dias da semana que ele trabalha
        $query = $pdo->query("SELECT * FROM dias where dia = '$dia_procurado' and empresa = '$id_empresa' ");
        $res = $query->fetchAll(PDO::FETCH_ASSOC);
        if (@count($res) > 0) {
          $img = 'fechado.png';
        }

        $hora_atual = date('H:i:s');

        //nova verificação de horarios
        $start = strtotime(date('Y-m-d' . $horario_abertura));
        $end = strtotime(date('Y-m-d' . $horario_fechamento));
        $now = time();

        if ($start <= $now && $now <= $end) {
        } else {

          if ($end < $start) {

            if ($now > $start) {
            } else {
              if ($now < $end) {
              } else {
                $img = 'fechado.png';
              }
            }
          } else {
            $img = 'fechado.png';
          }
        }
      }

    }

}




?>
<link rel="stylesheet" href="css/font-awesome.css">
<link rel="stylesheet" href="css/templatemo_style.css">
<link rel="stylesheet" href="css/templatemo_misc.css">
<link rel="stylesheet" href="css/flexslider.css">
<link rel="stylesheet" href="css/testimonails-slider.css">

<link rel="stylesheet" href="css/style_cards_index.css">
<link rel="stylesheet" type="text/css" href="css/style.css?v=<?php echo @filemtime('css/style.css') ?: time(); ?>">

<script src="js/vendor/modernizr-2.6.1-respond-1.1.0.min.js"></script>

<script src="js/vendor/jquery-1.11.0.min.js"></script>
<script src="js/vendor/jquery.gmap3.min.js"></script>
<script src="js/plugins.js"></script>
<script src="js/main.js"></script>


<!-- BOTÃO Scroll-top -->
<button class="scroll-top scroll-to-target mb-25" data-target="html">
  <i class="icon-chevrons-up"></i>
</button>
<!-- Scroll-top-end-->

<style type="text/css">
  .img-aberto {
    animation-duration: 2s;
    animation-name: slidein;
    opacity: 0.9;
    position: fixed;
    bottom: 10px;
    left: 0;
    z-index: 300;
  }

  @keyframes slidein {
    from {
      margin-left: 200%;
      width: 200%
    }

    to {
      margin-left: 0%;
      width: 70px;
    }
  }

  @media (max-width: 350px) {
    .ocultar_mobile_menor{
      display:none;
    }
  }
</style>


<div class="main-container">

  <nav class="navbar bg-light fixed-top" style="box-shadow: 0px 3px 5px rgba(0, 0, 0, 0.20);">
    <div class="container-fluid">


      <a class="navbar-brand" href="<?php echo $url_site ?>">
        <img src="sistema/img/<?php echo $logo_sistema ?>" alt="" width="80px" class="d-inline-block align-text-top">
        <small>
          <?php
          if ($id_mesa == "") {
            //echo '<span class="ocultar_mobile_menor">'.$nome_sistema.'<span>';
          } else {
            echo $nome_mesa;
          }
          ?>
        </small>
      </a>



<?php
$status_delivery_txt = 'ABERTO';
$status_delivery_cor = '#198754';
$status_delivery_icone = 'bi-check-circle';

if ($id_mesa == "" and $sessão_balcao == "") {

  if (@$pausar_delivery == 'Sim') {

    $status_delivery_txt = 'PAUSADO';
    $status_delivery_cor = '#f59e0b';
    $status_delivery_icone = 'bi-pause-circle';

  } else if (@$status_estabelecimento == "Fechado") {

    $status_delivery_txt = 'FECHADO';
    $status_delivery_cor = '#dc3545';
    $status_delivery_icone = 'bi-x-circle';

  } else {

    $data = date('Y-m-d');
    $diasemana = array("Domingo", "Segunda-Feira", "Terça-Feira", "Quarta-Feira", "Quinta-Feira", "Sexta-Feira", "Sábado");
    $diasemana_numero = date('w', strtotime($data));
    $dia_procurado = $diasemana[$diasemana_numero];

    $query_status_dia = $pdo->query("SELECT * FROM dias where dia = '$dia_procurado' and empresa = '$id_empresa'");
    $res_status_dia = $query_status_dia->fetchAll(PDO::FETCH_ASSOC);

    if (@count($res_status_dia) > 0) {

      $status_delivery_txt = 'FECHADO';
      $status_delivery_cor = '#dc3545';
      $status_delivery_icone = 'bi-x-circle';

    } else {

      $start = strtotime(date('Y-m-d') . ' ' . $horario_abertura);
      $end = strtotime(date('Y-m-d') . ' ' . $horario_fechamento);
      $now = time();

      $aberto_agora = false;

      if ($end < $start) {
        if ($now > $start || $now < $end) {
          $aberto_agora = true;
        }
      } else {
        if ($start <= $now && $now <= $end) {
          $aberto_agora = true;
        }
      }

      if (!$aberto_agora) {
        $status_delivery_txt = 'FECHADO';
        $status_delivery_cor = '#dc3545';
        $status_delivery_icone = 'bi-x-circle';
      }
    }
  }
}
?>

<div class="status-delivery-topo">
  <span style="background: <?php echo $status_delivery_cor ?>;">
    <i class="bi <?php echo $status_delivery_icone ?>"></i>
    <?php echo $status_delivery_txt ?>
  </span>
</div>


      <?php require_once("icone-carrinho.php") ?>



    </div>
  </nav>




  <div id="slider" style="margin-top: 50px;" class="ocultar-banner-web">

    <?php if(@$pausar_delivery == 'Sim'){ ?>
  <div class="alert alert-warning">
    <strong>Delivery Pausado</strong><br>
    <?php echo $texto_fechamento != '' ? $texto_fechamento : 'Estamos temporariamente sem receber pedidos.'; ?>
  </div>
<?php } ?>



    <?php if ($id_mesa == "") { ?>

      <?php
      if ($banner_rotativo == 'Sim') {
        $query = $pdo->query("SELECT * FROM banner_rotativo where empresa = '$id_empresa'");
        $res = $query->fetchAll(PDO::FETCH_ASSOC);
        $total_reg = @count($res);
        if ($total_reg > 0) {
      ?>


          <div class="flexslider">
            <ul class="slides">

              <?php
              for ($i = 0; $i < $total_reg; $i++) {
                foreach ($res[$i] as $key => $value) {
                }
                $foto = $res[$i]['foto'];
                $categoria = $res[$i]['categoria'];


                $query2 = $pdo->query("SELECT * FROM categorias where id = '$categoria'");
                $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
                $total_reg2 = @count($res2);
                if ($total_reg2 > 0) {
                  $url = 'categoria-' . $res2[0]['url'];
                } else {
                  $url = '#';
                }



                if ($i == 0) {
                  $ativo = 'active';
                } else {
                  $ativo = '';
                }
              ?>

                <div class="carousel-item <?php echo $ativo ?>">
                  <a href="<?php echo $url ?>">
                    <img class="d-block w-100" src="sistema/painel/images/banner_rotativo/<?php echo $foto ?>" alt="First slide" width="100%">
                  </a>
                </div>

                <li>
                  <a href="<?php echo $url ?>">
                    <img src="sistema/painel/images/banner_rotativo/<?php echo $foto ?>" alt="" />
                  </a>
                </li>




              <?php } ?>
            </ul>
          </div>


      <?php }
      } ?>
    <?php } ?>
  </div>

  <!-- Menu Start -->
  <div class="container-xxl py-5 margin_top_web" style="margin-bottom: -70px">
    <div class="margem_container">
      <?php if ($id_mesa != "") { ?>
        <div class="text-center wow fadeInUp ocultar-mobile" data-wow-delay="0.1s" style="margin-bottom: 15px">
          <h5 class="section-title ff-secondary text-center text-primary fw-normal"><span style="color:#FEA116">Pedido Mesa</span></h5>

        </div>
      <?php } else if ($sessão_balcao != "") { ?>

        <div class="text-center wow fadeInUp ocultar-mobile" data-wow-delay="0.1s" style="margin-bottom: 15px">
          <h5 class="section-title ff-secondary text-center text-primary fw-normal"><span style="color:#FEA116">Pedido Balcão</span></h5>

        </div>


      <?php } else { ?>


        <div class="text-center wow fadeInUp ocultar-mobile" data-wow-delay="0.1s" style="margin-bottom: 15px">
          <h5 class="section-title ff-secondary text-center text-primary fw-normal"><span style="color:#FEA116">Nosso Cardápio</span></h5>

        </div>


      <?php

      } ?>
      <div class="catalog-quick-nav wow fadeInUp" data-wow-delay="0.1s">
        <a class="catalog-nav-chip" href="<?php echo htmlspecialchars($url_sistema . rawurlencode($url_site), ENT_QUOTES, 'UTF-8') ?>#categoria">
          <img src="img/acai.png" alt="Categorias">
          <span>Categorias</span>
        </a>
        <a class="catalog-nav-chip" href="<?php echo htmlspecialchars($url_sistema . rawurlencode($url_site), ENT_QUOTES, 'UTF-8') ?>#combo">
          <img src="img/acai2.png" alt="Combos">
          <span>Combos</span>
        </a>
        <a class="catalog-nav-chip" href="<?php echo htmlspecialchars($url_sistema . rawurlencode($url_site), ENT_QUOTES, 'UTF-8') ?>#promocao">
          <img src="img/icone3.png" alt="Promoções">
          <span>Promoções</span>
        </a>
      </div>
    </div>
  </div>
  <!-- Menu End -->


<div class="catalog-search-container" style="max-width: 400px !important; margin: 0 auto 16px auto !important; width: 100% !important; padding: 0 15px !important;">
  <div class="catalog-search-wrapper" style="max-width: 400px !important; height: 42px !important; min-height: 42px !important; max-height: 44px !important; margin: 0 auto !important; width: 100% !important; padding: 0 16px !important;">
    <i class="bi bi-search catalog-search-icon"></i>
    <input 
      onkeyup="buscarProduto()"
      placeholder="O que você procura hoje?" 
      type="text" 
      name="buscar" 
      id="buscar" 
      class="catalog-search-input"
      style="width: 100%; height: 100%; margin: 0 !important; padding: 0 !important; border: none; outline: none; background: transparent;"
      autocomplete="off"
    >
  </div>
</div>


<div id="area_busca" style="display:none; max-width: 500px; margin: -15px auto 20px auto;">
  
</div>

  <?php
  $query = $pdo->query("SELECT * FROM categorias where ativo = 'Sim' and delivery = 'Sim' and empresa = '$id_empresa' order by posicao asc");
  $res = $query->fetchAll(PDO::FETCH_ASSOC);
  $total_cat = @count($res);

  if ($total_cat > 0) { ?>



    <!-- CATEGORIAS -->
    <section class="category-area ocultar_dasktop mb-4" id="categoria">
      <div class="catalog-section-header px-3">
        <h5 class="catalog-section-title">
          <i class="bi bi-grid-fill text-danger"></i> Categorias
        </h5>
        <span class="catalog-section-badge"><?php echo $total_cat ?> categorias</span>
      </div>


      <div class="swiper-container category-active">
        <div class="swiper-wrapper">

          <?php
          $query = $pdo->query("SELECT * FROM categorias where ativo = 'Sim' and delivery = 'Sim' and empresa = '$id_empresa' order by posicao asc");
          $res = $query->fetchAll(PDO::FETCH_ASSOC);
          $total_reg = @count($res);
          if ($total_reg > 0) {
            for ($i = 0; $i < $total_reg; $i++) {
              foreach ($res[$i] as $key => $value) {
              }
              $cor = $res[$i]['cor'];
              $foto = $res[$i]['foto'];
              $nome = $res[$i]['nome'];
              $descricao = $res[$i]['descricao'];
              $url = $res[$i]['url'];
              $id = $res[$i]['id'];
              $mais_sabores = $res[$i]['mais_sabores'];
              $delivery = $res[$i]['delivery'];

              if ($id_mesa == "" and $delivery == 'Não' and $sessão_balcao == '') {
                continue;
              }

              $query2 = $pdo->query("SELECT * FROM produtos where categoria = '$id' and ativo = 'Sim' and delivery = 'Sim'");
              $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
              $tem_produto = @count($res2);
              $mostrar = 'ocultar';
              if ($tem_produto > 0) {
                for ($i2 = 0; $i2 < $tem_produto; $i2++) {
                  foreach ($res2[$i2] as $key => $value) {
                  }

                  $id_prod = $res2[$i2]['id'];
                  $estoque = $res2[$i2]['estoque'];
                  $tem_estoque = $res2[$i2]['tem_estoque'];

                  if (($tem_estoque == 'Sim' and $estoque > 0) or ($tem_estoque == 'Não')) {
                    $mostrar = '';
                  }
                }
              } else {
                $mostrar = 'ocultar';
              }

              $descricaoF = mb_strimwidth($descricao, 0, 23, "...");

              if ($mais_sabores == 'Sim') {
                $link_cat =  "categoria-sabores-" . $url;
              } else {
                $link_cat =  "categoria-" . $url;
              }



          ?>




              <div class="swiper-slide" style="width: auto; min-width: 145px; max-width: 165px; padding: 4px;">
                <a href="<?php echo $link_cat ?>" class="text-decoration-none">
                  <div class="catalog-category-card">
                    <div class="catalog-category-img-wrap">
                      <img src="sistema/painel/images/categorias/<?php echo $foto ?>" alt="<?php echo $nome ?>" class="catalog-category-img">
                    </div>
                    <div class="catalog-category-content">
                      <div class="catalog-category-title"><?php echo $nome ?></div>
                      <div class="catalog-category-desc"><?php echo $descricaoF ?></div>
                      <span class="catalog-category-count"><?php echo $tem_produto ?> Itens</span>
                    </div>
                  </div>
                </a>
              </div>

          <?php }
          } ?>

        </div>

        <div class="collapse">
          <div class="card card-body">


            <?php
            $query = $pdo->query("SELECT * FROM produtos where ativo = 'Sim' and combo = 'Sim' and delivery = 'Sim' and empresa = '$id_empresa'");
            $res = $query->fetchAll(PDO::FETCH_ASSOC);
            $tem_produto = @count($res);
            $mostrar = 'ocultar';
            if ($tem_produto > 0) {
              for ($i = 0; $i < $tem_produto; $i++) {
                foreach ($res[$i] as $key => $value) {
                }
                $id_prod = $res[$i]['id'];
                $foto = $res[$i]['foto'];
                $nome = $res[$i]['nome'];
                $descricao = $res[$i]['descricao'];
                $url = $res[$i]['url'];
                $estoque = $res[$i]['estoque'];
                $tem_estoque = $res[$i]['tem_estoque'];
                $valor = $res[$i]['valor_venda'];
                $valorF = number_format($valor, 2, ',', '.');

                $descricaoF = mb_strimwidth($descricao, 0, 70, "...");

                if ($tem_estoque == 'Sim' and $estoque <= 0) {
                  continue;
                } else {

                  $url_produto = 'produto-' . $url;
                }




                //verificar se o produto tem adicionais
                $query3 = $pdo->query("SELECT * FROM grades where produto = '$id_prod'");
                $res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
                $total_adc = @count($res3);
                if ($total_adc > 0) {
                  if ($tem_estoque == 'Sim' and $estoque <= 0) {
                    $url_produto = '#';
                  } else {
                    $url_produto = 'adicionais-' . $url;
                  }
                } else {
                  if ($tem_estoque == 'Sim' and $estoque <= 0) {
                    $url_produto = '#';
                  } else {
                    $url_produto = 'observacoes-' . $url;
                  }
                }




            ?>



                <div class="col-lg-9">
                  <div class="row gx-3">
                    <div class="col-xl-4 col-lg-6">
                      <div class="tpbrandproduct__item d-flex mb-20">
                        <div class="tpbrandproduct__img p-relative">
                          <img src="sistema/painel/images/produtos/<?php echo $foto ?>" alt="">
                          <div class="tpproduct__info bage tpbrandproduct__bage">

                          </div>
                        </div>
                        <div class="tpbrandproduct__contact">
                          <span class="tpbrandproduct__product-title"><a href="javascript:void(0)"><?php echo $nome ?></a></span>
                          <span class="fst-italic subtitulo_itens" style="color:#474747; font-size: 10px"><?php echo $descricaoF ?></span>
                          <div class="tpproduct__rating mb-5">
                            <a href="javascript:void(0)"><i class="icon-star_outline1"></i></a>
                            <a href="javascript:void(0)"><i class="icon-star_outline1"></i></a>
                            <a href="javascript:void(0)"><i class="icon-star_outline1"></i></a>
                            <a href="javascript:void(0)"><i class="icon-star_outline1"></i></a>
                            <a href="javascript:void(0)"><i class="icon-star_outline1"></i></a>
                          </div>
                          <div class="tpproduct__price">
                            <span><?php echo $valorF ?></span>

                          </div>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>


            <?php }
            } ?>


          </div>
        </div>
      </div>
    </section>
    <!-- FIM CATEGORIAS -->


  <?php } ?>






  <?php
  $query = $pdo->query("SELECT * FROM categorias where ativo = 'Sim' and delivery = 'Sim' and empresa = '$id_empresa' order by posicao asc");
  $res = $query->fetchAll(PDO::FETCH_ASSOC);
  $total_cat2 = @count($res);

  if ($total_cat2 > 0) { ?>


    <!-- CATEGORIAS DASKTOP -->
    <section class="brand-product ocultar_mobile mb-4" id="categoria">
      <div class="container">
        <div class="sections__wrapper white-bg p-4 brand-product" style="border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
          <div class="catalog-section-header">
            <h5 class="catalog-section-title">
              <i class="bi bi-grid-fill text-danger"></i> Categorias
            </h5>
            <span class="catalog-section-badge"><?php echo $total_cat2 ?> categorias</span>
          </div>
          <div class="row g-3">

            <?php
            $query = $pdo->query("SELECT * FROM categorias where ativo = 'Sim' and delivery = 'Sim' and empresa = '$id_empresa' order by posicao asc");
            $res = $query->fetchAll(PDO::FETCH_ASSOC);
            $total_reg = @count($res);
            if ($total_reg > 0) {
              for ($i = 0; $i < $total_reg; $i++) {
                foreach ($res[$i] as $key => $value) {
                }
                $cor = $res[$i]['cor'];
                $foto = $res[$i]['foto'];
                $nome = $res[$i]['nome'];
                $descricao = $res[$i]['descricao'];
                $url = $res[$i]['url'];
                $id = $res[$i]['id'];
                $mais_sabores = $res[$i]['mais_sabores'];
                $delivery = $res[$i]['delivery'];

                if ($id_mesa == "" and $delivery == 'Não' and $sessão_balcao == '') {
                  continue;
                }

                $query2 = $pdo->query("SELECT * FROM produtos where categoria = '$id' and ativo = 'Sim'  and delivery = 'Sim'");
                $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
                $tem_produto = @count($res2);
                $mostrar = 'ocultar';
                if ($tem_produto > 0) {
                  for ($i2 = 0; $i2 < $tem_produto; $i2++) {
                    foreach ($res2[$i2] as $key => $value) {
                    }

                    $id_prod = $res2[$i2]['id'];
                    $estoque = $res2[$i2]['estoque'];
                    $tem_estoque = $res2[$i2]['tem_estoque'];

                    if (($tem_estoque == 'Sim' and $estoque > 0) or ($tem_estoque == 'Não')) {
                      $mostrar = '';
                    }
                  }
                } else {
                  $mostrar = 'ocultar';
                }

                $descricaoF = mb_strimwidth($descricao, 0, 50, "...");

                if ($mais_sabores == 'Sim') {
                  $link_cat =  "categoria-sabores-" . $url;
                } else {
                  $link_cat =  "categoria-" . $url;
                }



            ?>




                <div class="col-xl-4 col-lg-4 col-md-6">
                  <a href="<?php echo $link_cat ?>" class="text-decoration-none">
                    <div class="catalog-category-card-desktop">
                      <div class="category-img-wrap">
                        <img class="imagem-cat" src="sistema/painel/images/categorias/<?php echo $foto ?>" alt="<?php echo $nome ?>">
                      </div>

                      <div class="category-info-wrap">
                        <span class="category-title"><?php echo $nome ?></span>
                        <span class="category-desc"><?php echo $descricaoF ?></span>
                        <span class="category-count"><i class="bi bi-tag-fill me-1"></i><?php echo $tem_produto ?> Itens</span>
                      </div>
                    </div>
                  </a>
                </div>


            <?php }
            } ?>





          </div>
        </div>
      </div>
    </section>
    <!-- FIM CATEGORIAS DASKTOP -->


  <?php } ?>







  <?php
  $query = $pdo->query("SELECT * FROM produtos where ativo = 'Sim' and promocao = 'Sim'  and delivery = 'Sim' and empresa = '$id_empresa'");
  $res = $query->fetchAll(PDO::FETCH_ASSOC);
  $tem_produto = @count($res);
  if ($tem_produto > 0) { ?>




    <!-- OFERTAS DA SEMANA -->
    <section class="brand-product mb-4" style="margin-top: 20px" id="promocao">
      <div class="container">
        <div class="sections__wrapper white-bg p-4 brand-product" style="border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
          <div class="catalog-section-header">
            <h5 class="catalog-section-title">
              <i class="bi bi-lightning-charge-fill text-danger"></i> Ofertas da Semana
            </h5>
            <span class="catalog-section-badge text-danger fw-bold">Imperdível</span>
          </div>
          <div class="row g-3">

            <?php
            $query = $pdo->query("SELECT * FROM produtos where ativo = 'Sim' and promocao = 'Sim'  and delivery = 'Sim' and empresa = '$id_empresa'");
            $res = $query->fetchAll(PDO::FETCH_ASSOC);
            $tem_produto = @count($res);
            $mostrar = 'ocultar';
            if ($tem_produto > 0) {
              for ($i = 0; $i < $tem_produto; $i++) {
                foreach ($res[$i] as $key => $value) {
                }
                $id_prod = $res[$i]['id'];
                $foto = $res[$i]['foto'];
                $nome = $res[$i]['nome'];
                $descricao = $res[$i]['descricao'];
                $url = $res[$i]['url'];
                $estoque = $res[$i]['estoque'];
                $tem_estoque = $res[$i]['tem_estoque'];
                $valor = $res[$i]['valor_venda'];
                $val_promocional = $res[$i]['val_promocional'];

                $valorF = number_format($valor, 2, ',', '.');
                $val_promocionalF = number_format($val_promocional, 2, ',', '.');

                $descricaoF = mb_strimwidth($descricao, 0, 50, "...");

                $valor_porc = $valor - $val_promocional;

                $valor_porcentagem = ($valor_porc / $valor) * 100;

                $valor_porcentagemF = number_format($valor_porcentagem, 2, ',', '.') . '%';


                if ($tem_estoque == 'Sim' and $estoque <= 0) {
                  continue;
                } else {

                  $url_produto = 'produto-' . $url;
                }



                //verificar se o produto tem adicionais
                $query3 = $pdo->query("SELECT * FROM grades where produto = '$id_prod'");
                $res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
                $total_adc = @count($res3);
                if ($total_adc > 0) {
                  if ($tem_estoque == 'Sim' and $estoque <= 0) {
                    $url_produto = '#';
                  } else {
                    $url_produto = 'adicionais-' . $url;
                  }
                } else {
                  if ($tem_estoque == 'Sim' and $estoque <= 0) {
                    $url_produto = '#';
                  } else {
                    $url_produto = 'observacoes-' . $url;
                  }
                }




            ?>







                <div class="col-xl-4 col-lg-6 col-md-6">
                  <div class="catalog-product-card catalog-product-card-horizontal">
                    <a href="<?php echo $url_produto ?>" class="catalog-product-link">
                      <div class="catalog-product-img-wrap">
                        <img src="sistema/painel/images/produtos/<?php echo $foto ?>" alt="<?php echo $nome ?>" class="catalog-product-img">
                        <span class="catalog-product-badge-discount">-<?php echo $valor_porcentagemF ?></span>
                      </div>


                      <div class="catalog-product-body">
                        <div>
                          <h6 class="catalog-product-title"><?php echo $nome ?></h6>
                          <p class="catalog-product-desc"><?php echo $descricaoF ?></p>
                        </div>
                        <div class="catalog-product-footer">
                          <div class="catalog-product-price-box">
                            <span class="catalog-price-promo">R$ <?php echo $val_promocionalF ?></span>
                            <del class="catalog-price-old">R$ <?php echo $valorF ?></del>
                          </div>
                          <span class="catalog-btn-add" title="Ver produto">
                            <i class="bi bi-plus-lg"></i>
                          </span>
                        </div>
                      </div>
                    </a>
                  </div>

                </div>


            <?php }
            } ?>





          </div>
        </div>
      </div>
    </section>
    <!-- FIM OFERTA DA SEMANA -->



  <?php } ?>


  <?php
  $query = $pdo->query("SELECT * FROM produtos where ativo = 'Sim' and combo = 'Sim' and delivery = 'Sim' and empresa = '$id_empresa'");
  $res = $query->fetchAll(PDO::FETCH_ASSOC);
  $tem_produto = @count($res);
  $mostrar = 'ocultar';
  if ($tem_produto > 0) { ?>


    <!-- APROVEITE NOSSO COMBOS -->
    <section class="blog-area mb-5" style="margin-top: 10px;" id="combo">
      <div class="container">
        <div class="catalog-section-header">
          <h5 class="catalog-section-title">
            <i class="bi bi-stars text-danger"></i> Aproveite Nossos Combos
          </h5>
          <span class="catalog-section-badge">Especial</span>
        </div>

        <div class="swiper-container tpblog-active">
          <div class="swiper-wrapper">


            <?php
            $query = $pdo->query("SELECT * FROM produtos where ativo = 'Sim' and combo = 'Sim'  and delivery = 'Sim' and empresa = '$id_empresa'");
            $res = $query->fetchAll(PDO::FETCH_ASSOC);
            $tem_produto = @count($res);
            $mostrar = 'ocultar';
            if ($tem_produto > 0) {
              for ($i = 0; $i < $tem_produto; $i++) {
                foreach ($res[$i] as $key => $value) {
                }
                $id_prod = $res[$i]['id'];
                $foto = $res[$i]['foto'];
                $nome = $res[$i]['nome'];
                $descricao = $res[$i]['descricao'];
                $url = $res[$i]['url'];
                $estoque = $res[$i]['estoque'];
                $tem_estoque = $res[$i]['tem_estoque'];
                $valor = $res[$i]['valor_venda'];
                $valorF = number_format($valor, 2, ',', '.');

                $promocao = $res[$i]['promocao'];
                $val_promocional = $res[$i]['val_promocional'];

          $val_promocionalF = @number_format($val_promocional, 2, ',', '.');

          if($val_promocional != 0 and $promocao != 'Não'){
            $valorF = $val_promocionalF;
          }

                $descricaoF = mb_strimwidth($descricao, 0, 70, "...");

                if ($tem_estoque == 'Sim' and $estoque <= 0) {
                  continue;
                } else {

                  $url_produto = 'produto-' . $url;
                }




                //verificar se o produto tem adicionais
                $query3 = $pdo->query("SELECT * FROM grades where produto = '$id_prod'");
                $res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
                $total_adc = @count($res3);
                if ($total_adc > 0) {
                  if ($tem_estoque == 'Sim' and $estoque <= 0) {
                    $url_produto = '#';
                  } else {
                    $url_produto = 'adicionais-' . $url;
                  }
                } else {
                  if ($tem_estoque == 'Sim' and $estoque <= 0) {
                    $url_produto = '#';
                  } else {
                    $url_produto = 'observacoes-' . $url;
                  }
                }


            ?>


                <div class="swiper-slide">
                  <div class="catalog-product-card combo-card">
                    <a href="<?php echo $url_produto ?>" class="catalog-product-link">
                      <div class="combo-img-wrap">
                        <img src="sistema/painel/images/produtos/<?php echo $foto ?>" alt="<?php echo $nome ?>" class="combo-img">
                      </div>

                      <div class="catalog-product-body">
                        <div>
                          <h6 class="catalog-product-title"><?php echo $nome ?></h6>
                          <p class="catalog-product-desc"><?php echo $descricaoF ?></p>
                        </div>
                        <div class="catalog-product-footer">
                          <div class="catalog-product-price-box">
                            <span class="catalog-price-promo">R$ <?php echo $valorF ?></span>
                          </div>
                          <span class="catalog-btn-add" title="Ver combo">
                            <i class="bi bi-plus-lg"></i>
                          </span>
                        </div>
                      </div>
                    </a>
                  </div>
                </div>
            <?php }
            } ?>
          </div>

        </div>

      </div>
    </section>
    <!-- FIM APROVEITE NOSSO COMBOS -->


  <?php } ?>

</div>


<?php if ($tem_produto >= 0 and $total_cat2 >= 0 and $total_cat >= 0) {
  
}
?>

<?php if($id_mesa == ""){ ?>
<button id="botao-instalar" style="display: none; position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); padding: 12px 20px; background: #007bff; color: #fff; border: none; border-radius: 8px; z-index: 9999;">Instalar App</button>
<?php } ?>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />



<?php if ($id_mesa == "" and $sessão_balcao == "") { 
  $whats_limpo = preg_replace('/[^0-9]/', '', (string)(@$whatsapp_sistema ?: @$telefone_sistema));
  if (!empty($whats_limpo)) {
    if (strlen($whats_limpo) == 10 || strlen($whats_limpo) == 11) {
      $whats_limpo = '55' . $whats_limpo;
    }
  }
?>
  <footer class="rodape">
    <div class="container">
      <div class="row align-items-center py-2">
        <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
          <?php if ($endereco_sistema == "") { ?>
            <span class="ocultar-mobile"><?php echo htmlspecialchars($nome_sistema ?? ''); ?></span>
          <?php } else { ?>
            <span><i class="bi bi-geo-alt text-danger me-1"></i> <?php echo htmlspecialchars($endereco_sistema); ?></span>
          <?php } ?>
        </div>
        <div class="col-md-6 text-center text-md-end">
          <?php if (!empty($whats_limpo)) { ?>
          <span class="d-inline-block me-3"><a target="_blank"
              href="https://api.whatsapp.com/send?phone=<?php echo $whats_limpo; ?>" class="link-neutro"><i
                class="bi bi-whatsapp text-success me-1"></i> <?php echo htmlspecialchars($telefone_sistema ?? $whatsapp_sistema ?? ''); ?></a></span>
          <?php } ?>
          <?php if (!empty($url_instagram)) { ?>
          <span class="d-inline-block"><a target="_blank" href="<?php echo $url_instagram ?>"
              class="link-neutro"><i class="bi bi-instagram me-1" style="color:#d11144"></i> @Instagram</a></span>
          <?php } ?>
        </div>
      </div>
      <hr style="opacity: 0.15; margin: 15px 0;">
      <div class="footer-copyright text-center" style="font-size: 14px; color: #6c757d;">
          <p class="mb-1">&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($nome_sistema ?? 'Empresa'); ?>. Todos os direitos reservados.</p>
          <p class="mb-0">Desenvolvido por: <a href="https://crnsistemas.com.br/" target="_blank" rel="noopener noreferrer" style="color: #6c757d; font-weight: bold; text-decoration: none;">CRN Sistemas</a></p>
      </div>
    </div>
  </footer>

<?php 

$img_rodape = 'aberto.png';
$classe_extra = '';

if (@$status_delivery_txt == 'FECHADO') {
  $img_rodape = 'fechado.png';
  $classe_extra = 'status-fechado';
}

if (@$status_delivery_txt == 'PAUSADO') {
  $img_rodape = 'pausado.png';
  $classe_extra = 'status-pausado';
}

if ($img_rodape == "aberto.png" and $mostrar_aberto != "Sim") {
  // não mostra aberto se a configuração estiver desativada
} else {
?>
  <img src="img/<?php echo $img_rodape ?>" width="70px" class="img-aberto <?php echo $classe_extra ?>">
<?php } ?>



<?php } ?>




<!-- JS here -->
<script src="assets/js/waypoints.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/swiper-bundle.js"></script>
<script src="assets/js/nice-select.js"></script>
<script src="assets/js/slick.js"></script>
<script src="assets/js/magnific-popup.js"></script>
<script src="assets/js/counterup.js"></script>
<script src="assets/js/wow.js"></script>
<script src="assets/js/isotope-pkgd.js"></script>
<script src="assets/js/imagesloaded-pkgd.js"></script>
<script src="assets/js/countdown.js"></script>
<script src="assets/js/ajax-form.js"></script>
<script src="assets/js/meanmenu.js"></script>
<script src="assets/js/main.js"></script>

</body>

</html>


<script type="text/javascript">
  function buscarProduto(){
    var buscar = $('#buscar').val();
    if(buscar == ""){
      $('#area_busca').hide();
    }else{
      $('#area_busca').show();

      $.ajax({
      url: 'js/ajax/buscar_produtos.php',
      method: 'POST',
      data: {
        buscar
      },
      dataType: "text",

      success: function(mensagem) {       
       
       $('#area_busca').html(mensagem);

      },

    });

    }




  }
</script>









<script>
  document.addEventListener("DOMContentLoaded", function () {
      

    // Registrar Service Worker
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('sw.js')
        .then(() => navigator.serviceWorker.ready)
        .then(reg => {
          const worker = reg.active || reg.waiting || reg.installing;
          if (worker) {
            worker.postMessage({
              type: 'CACHE_START_URL',
              url: <?php echo json_encode($url_sistema . $url_site) ?>
            });
          }
          console.log('Service Worker registrado:', reg.scope);
        })
        .catch(err => console.error('❌ Erro ao registrar SW:', err));
    }

    // Gerenciar instalação do PWA
    let deferredPrompt;
    window.addEventListener('beforeinstallprompt', (e) => {
      e.preventDefault();
      deferredPrompt = e;
      const btn = document.getElementById('botao-instalar');
      if (btn) {
        btn.style.display = 'block';

        btn.addEventListener('click', () => {
          btn.style.display = 'none';
          deferredPrompt.prompt();
          deferredPrompt.userChoice.then((result) => {
            console.log('Resultado da instalação:', result.outcome);
            deferredPrompt = null;
          });
        });
      }
    });

    window.addEventListener('appinstalled', () => {
      console.log('✅ App instalado!');
    });
  });
</script>

 
<script src="app_assets/js/custom.js"></script>

