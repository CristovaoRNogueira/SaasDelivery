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
  body {
    background: #f8f9fa;
  }

  .checkout-wrapper {
    max-width: 720px;
    margin: 0 auto;
    padding: 68px 10px 150px 10px;
  }

  .checkout-accordion .accordion-item {
    border: 1px solid #e9ecef !important;
    border-radius: 14px !important;
    margin-bottom: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    background: #ffffff;
  }

  .checkout-accordion .accordion-button {
    font-size: 14px !important;
    font-weight: 700;
    color: #212529;
    background: #ffffff;
    padding: 15px 18px;
    box-shadow: none !important;
    border-left: 4px solid transparent;
    transition: all 0.2s ease;
  }

  .checkout-accordion .accordion-button:not(.collapsed) {
    background: #fff8f8;
    color: #ea1d2c;
    border-left: 4px solid #ea1d2c;
    border-bottom: 1px solid #f1f3f5;
  }

  .checkout-accordion .accordion-body {
    padding: 18px;
    background: #ffffff;
  }

  .checkout-input-group {
    margin-bottom: 14px;
    text-align: left;
  }

  .checkout-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 5px;
  }

  .checkout-input {
    width: 100%;
    border: 1.5px solid #dee2e6;
    border-radius: 12px;
    padding: 11px 14px;
    font-size: 14px;
    color: #212529;
    background: #f8f9fa;
    transition: all 0.2s ease;
    outline: none;
  }

  .checkout-input:focus {
    background: #ffffff;
    border-color: #ea1d2c;
    box-shadow: 0 0 0 3px rgba(234, 29, 44, 0.12);
  }

  .delivery-option-card {
    border: 1.5px solid #e9ecef !important;
    border-radius: 12px !important;
    padding: 14px 16px !important;
    margin-bottom: 10px !important;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #ffffff;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .delivery-option-card:hover {
    border-color: #ea1d2c !important;
  }

  .delivery-option-card.selected {
    border: 2px solid #ea1d2c !important;
    background: #fff5f5 !important;
  }

  .payment-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
  }

  .payment-pill-card {
    border: 1.5px solid #e9ecef;
    border-radius: 12px;
    padding: 13px 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-weight: 600;
    font-size: 14px;
    color: #212529;
    user-select: none;
  }

  .payment-pill-card:hover {
    border-color: #ea1d2c;
  }

  .payment-pill-card.selected {
    border: 2px solid #ea1d2c;
    background: #fff5f5;
    color: #ea1d2c;
  }

  .payment-info-box {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 14px;
    margin-top: 14px;
  }

  .btn-gps-locate {
    width: 100%;
    height: 44px;
    border-radius: 12px;
    background: #fff5f5;
    color: #ea1d2c;
    border: 1.5px solid #f5c2c7;
    font-weight: 600;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s ease;
  }

  .btn-gps-locate:hover {
    background: #ea1d2c;
    color: #ffffff;
  }

  .checkout-summary-card {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 14px;
    padding: 14px 16px;
    margin-top: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
  }
</style>

<div class="main-container">

  <nav class="navbar bg-white fixed-top" style="box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);">
    <div class="container-fluid" style="max-width: 720px;">
      <a class="navbar-brand d-flex align-items-center gap-2 text-dark text-decoration-none" href="carrinho">
        <i class="bi bi-arrow-left" style="font-size: 20px;"></i>
        <img src="sistema/img/<?php echo $logo_sistema ?>" alt="" width="40px" height="40px" style="object-fit: contain; border-radius: 8px;" class="d-inline-block align-text-top">
        <span class="fw-bold" style="font-size: 15px;">Finalizar Pedido</span>
      </a>

      <?php require_once("icone-carrinho.php") ?>

    </div>
  </nav>

  <div class="checkout-wrapper">
    <div class="accordion checkout-accordion" id="accordionExample">

      <!-- ==================== FASE 1: IDENTIFICAÇÃO ==================== -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingOne">
          <button id="btn_ident" class="accordion-button" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <i class="bi bi-person-badge me-2"></i> 1 - IDENTIFICAÇÃO
          </button>
        </h2>
        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
          data-bs-parent="#accordionExample">
          <div class="accordion-body">

            <div class="checkout-input-group">
              <label class="checkout-label" for="telefone"><i class="bi bi-whatsapp text-success me-1"></i> Seu Telefone / WhatsApp</label>
              <input onkeyup="buscarNome()" onblur="buscarNome()" type="tel" class="checkout-input telefone_user" name="telefone" id="telefone" required value="" placeholder="(00) 00000-0000" style="font-size: 16px; font-weight: 600;">
            </div>

            <div class="checkout-input-group">
              <label class="checkout-label" for="nome"><i class="bi bi-person me-1"></i> Seu Nome Completo</label>
              <input onclick="" type="text" class="checkout-input" name="nome" id="nome" required value="" placeholder="Digite seu nome" style="font-size: 15px;">
            </div>

            <div class="text-center mt-3 mb-2">
              <span class="fw-bold text-dark" style="font-size: 14px;">Deseja avançar com seu pedido?</span>
            </div>

            <div class="row g-2 mt-1">
              <div class="col-6">
                <a href="<?php echo $url_site ?>" class="btn btn-outline-secondary w-100 rounded-pill py-2 fw-bold">
                  <i class="bi bi-arrow-left me-1"></i> Voltar
                </a>
              </div>
              <div class="col-6">
                <a class="btn btn-danger w-100 rounded-pill py-2 fw-bold" style="background: #ea1d2c; border-color: #ea1d2c;" onclick="dados(); buscarNome()">
                  Continuar <i class="bi bi-arrow-right ms-1"></i>
                </a>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- ==================== FASE 2: MODO DE ENTREGA ==================== -->
      <div class="accordion-item <?php echo $esconder_opc_delivery ?>">
        <h2 class="accordion-header" id="headingTwo">
          <button id="colapse-2" class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
            <i class="bi bi-truck me-2"></i> 2 - MODO DE ENTREGA
          </button>
        </h2>

        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
          data-bs-parent="#accordionExample">
          <div class="accordion-body">

            <ul class="list-group ps-0 mb-0">

              <li id="card_entrega_retirar" style="display:<?php echo $ocultar_retirar ?> !important" onclick="retirar()" class="list-group-item delivery-option-card">
                <div class="d-flex align-items-center gap-3">
                  <i class="bi bi-shop text-danger" style="font-size: 20px;"></i>
                  <div>
                    <div class="fw-bold text-dark" style="font-size: 14px;">Retirar no Local</div>
                    <small class="text-muted" style="font-size: 12px;">Você busca o pedido no balcão da loja</small>
                  </div>
                </div>
                <input onchange="retirar()" class="form-check-input" type="radio" name="radio_retirar" id="radio_retirar" style="transform: scale(1.25);">
              </li>

              <li id="card_entrega_local" style="display:<?php echo $ocultar_consumir ?> !important" onclick="local()" class="list-group-item delivery-option-card">
                <div class="d-flex align-items-center gap-3">
                  <i class="bi bi-cup-straw text-danger" style="font-size: 20px;"></i>
                  <div>
                    <div class="fw-bold text-dark" style="font-size: 14px;">Consumir no Local</div>
                    <small class="text-muted" style="font-size: 12px;">Seu pedido será servido na nossa unidade</small>
                  </div>
                </div>
                <input onchange="local()" class="form-check-input" type="radio" name="radio_local" id="radio_local" style="transform: scale(1.25);">
              </li>

              <li id="card_entrega_delivery" style="display:<?php echo $ocultar_delivery ?> !important" onclick="entrega()" class="list-group-item delivery-option-card">
                <div class="d-flex align-items-center gap-3">
                  <i class="bi bi-bicycle text-danger" style="font-size: 20px;"></i>
                  <div>
                    <div class="fw-bold text-dark" style="font-size: 14px;">Entrega Delivery</div>
                    <small class="text-muted" style="font-size: 12px;">Receba no conforto do seu endereço</small>
                  </div>
                </div>
                <input onchange="entrega()" class="form-check-input" type="radio" name="radio_entrega" id="radio_entrega" style="transform: scale(1.25);">
              </li>

            </ul>

          </div>
        </div>
      </div>

      <!-- ==================== FASE 3: ENDEREÇO OU UNIDADE DE RETIRADA ==================== -->
      <div class="accordion-item <?php echo $esconder_opc_delivery ?>">
        <h2 class="accordion-header" id="headingThree">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree" id="colapse-3">
            <i class="bi bi-geo-alt me-2"></i> 3 - ENDEREÇO OU UNIDADE DE RETIRADA
          </button>
        </h2>
        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
          data-bs-parent="#accordionExample">
          <div class="accordion-body">

            <div id="area-retirada">
              <a href="" class="text-decoration-none" data-bs-toggle="collapse" data-bs-target="#collapse4">
                <div class="payment-info-box d-flex justify-content-between align-items-center mt-0">
                  <div>
                    <div class="fw-bold text-dark" style="font-size: 14px;">
                      <i class="bi bi-geo-alt-fill text-danger me-1"></i> <span id="consumir-local">Endereço da nossa Loja</span>
                    </div>
                    <div class="text-muted mt-1" style="font-size: 13px;"><?php echo $endereco_sistema ?></div>
                    <span class="badge bg-danger rounded-pill mt-2">Toque para escolher o pagamento <i class="bi bi-arrow-right"></i></span>
                  </div>
                  <i class="bi bi-check-circle-fill text-success" style="font-size: 22px;"></i>
                </div>
              </a>
            </div>

            <div id="area-endereco">

              <?php if ($entrega_distancia == "Sim" and $chave_api_maps != "") { ?>
                <div class="row g-2">
                  <div class="col-6 col-sm-5">
                    <div class="checkout-input-group">
                      <label class="checkout-label" for="cep">CEP*</label>
                      <input type="text" class="checkout-input" name="cep" id="cep" placeholder="00000-000" required onblur="calcularFreteDistancia()">
                    </div>
                  </div>
                </div>
              <?php } ?>

              <div class="row g-2 align-items-end">
                <div class="col-8">
                  <div class="checkout-input-group">
                    <label class="checkout-label" for="rua">Rua / Logradouro*</label>
                    <input type="text" class="checkout-input" name="endereco" id="rua" placeholder="Nome da rua" required>
                  </div>
                </div>

                <div class="col-4">
                  <div class="checkout-input-group">
                    <label class="checkout-label" for="numero">Número*</label>
                    <input type="text" class="checkout-input" name="numero" id="numero" placeholder="Nº" required>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <button type="button" title="Buscar Localização" class="btn-gps-locate" onclick="localizacao()">
                  <i class="bi bi-geo-alt-fill"></i> Usar minha localização atual (GPS)
                </button>
              </div>

              <div class="row g-2">
                <div class="col-12 col-sm-5">
                  <div class="checkout-input-group">
                    <label class="checkout-label" for="complemento">Complemento</label>
                    <input type="text" class="checkout-input" name="complemento" id="complemento" placeholder="Apto, bloco, casa..." required>
                  </div>
                </div>

                <?php if ($entrega_distancia != "Sim" or $chave_api_maps == "") { ?>

                  <div class="col-12 col-sm-7">
                    <div class="checkout-input-group">
                      <label class="checkout-label" for="bairro">Bairro*</label>
                      <select class="checkout-input" name="bairro" id="bairro" required onchange="calcularFrete()">
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
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="checkout-input-group">
                      <label class="checkout-label" for="referencia">Ponto de Referência</label>
                      <input type="text" class="checkout-input" name="referencia" id="referencia" placeholder="Próximo a...">
                    </div>
                  </div>

                <?php } else { ?>
                  <div class="col-12 col-sm-7">
                    <div class="checkout-input-group">
                      <label class="checkout-label" for="bairro">Bairro*</label>
                      <input type="text" class="checkout-input" name="bairro" id="bairro" placeholder="Seu bairro" required>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="checkout-input-group">
                      <label class="checkout-label" for="cidade">Cidade*</label>
                      <input type="text" class="checkout-input" name="cidade" id="cidade" placeholder="Sua cidade">
                    </div>
                  </div>
                <?php } ?>

                <div class="col-12 mt-2">
                  <a id="colap-4" href="javascript:void(0)" class="btn btn-danger w-100 rounded-pill py-2 fw-bold" style="background: #ea1d2c; border-color: #ea1d2c;" data-bs-toggle="collapse"
                    data-bs-target="#collapse4">
                    Avançar para Pagamento <i class="bi bi-arrow-right ms-1"></i>
                  </a>
                </div>

              </div>

            </div>
          </div>
        </div>
      </div>

      <!-- ==================== FASE 4: PAGAMENTO ==================== -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="heading4">
          <button id="collapse-4" class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
            <i class="bi bi-credit-card me-2"></i> <?php echo $numero_colapse ?> - PAGAMENTO
          </button>
        </h2>
        <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="heading4"
          data-bs-parent="#accordionExample">
          <div class="accordion-body">

            <label class="checkout-label mb-2">Selecione como deseja pagar:</label>

            <div class="payment-grid">

              <div id="card_pgto_pix" class="payment-pill-card" onclick="document.getElementById('radio_pix').checked = true; pix();">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-qr-code text-success" style="font-size: 18px;"></i>
                  <span>Pix</span>
                </div>
                <input onchange="pix()" class="form-check-input m-0" type="radio" name="radio_pix" id="radio_pix">
              </div>

              <div id="card_pgto_dinheiro" class="payment-pill-card" style="display:<?php echo ($visualizar_pagamento == 'none') ? 'none' : 'flex'; ?>" onclick="document.getElementById('radio_dinheiro').checked = true; dinheiro();">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-cash-coin text-success" style="font-size: 18px;"></i>
                  <span>Dinheiro</span>
                </div>
                <input onchange="dinheiro()" class="form-check-input m-0" type="radio" name="racio_dinheiro" id="radio_dinheiro">
              </div>

              <div id="card_pgto_credito" class="payment-pill-card" style="display:<?php echo ($visualizar_pagamento == 'none') ? 'none' : 'flex'; ?>" onclick="document.getElementById('radio_credito').checked = true; credito();">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-credit-card text-primary" style="font-size: 18px;"></i>
                  <span>Crédito</span>
                </div>
                <input onchange="credito()" class="form-check-input m-0" type="radio" name="radio_credito" id="radio_credito">
              </div>

              <div id="card_pgto_debito" class="payment-pill-card" style="display:<?php echo ($visualizar_pagamento == 'none') ? 'none' : 'flex'; ?>" onclick="document.getElementById('radio_debito').checked = true; debito();">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-credit-card-2-front text-info" style="font-size: 18px;"></i>
                  <span>Débito</span>
                </div>
                <input onchange="debito()" class="form-check-input m-0" type="radio" name="radio_debito" id="radio_debito">
              </div>

            </div>

            <?php if($sessao_pedido_balcao == 'BALCÃO' ){ ?>
            <div class="mt-3">
              <label class="checkout-label" for="esta_pago">Status do pagamento (Balcão):</label>
              <select class="checkout-input" name="esta_pago" id="esta_pago" style="max-width: 240px;">
                <option value="Não">Não está Pago</option>
                <option value="Sim">Já está Pago</option>
              </select>
            </div>
            <?php } ?>

            <div id="pagar_pix" style="margin-top: 15px;">
              <div id="listar_pix"></div>
            </div>

            <div id="pagar_dinheiro" class="payment-info-box">
              <div class="fw-bold text-dark mb-2" style="font-size: 14px;">
                <i class="bi bi-cash-stack text-success me-1"></i> Pagamento em Dinheiro na Entrega
              </div>
              <div class="checkout-input-group mb-0">
                <label class="checkout-label" for="troco">Precisa de troco para quanto?</label>
                <input type="text" class="checkout-input" name="troco" id="troco" placeholder="Ex: 50,00 (deixe em branco se não precisar)" style="background: #ffffff;">
              </div>
            </div>

            <div id="pagar_credito" class="payment-info-box">
              <div class="fw-bold text-dark" style="font-size: 14px;">
                <i class="bi bi-credit-card text-primary me-1"></i> Pagar com Cartão de Crédito
              </div>
              <small class="text-muted">Levamos a maquininha! O pagamento será efetuado no ato da entrega.</small>
            </div>

            <div id="pagar_debito" class="payment-info-box">
              <div class="fw-bold text-dark" style="font-size: 14px;">
                <i class="bi bi-credit-card-2-front text-info me-1"></i> Pagar com Cartão de Débito
              </div>
              <small class="text-muted">Levamos a maquininha! O pagamento será efetuado no ato da entrega.</small>
            </div>

            <!-- Cupom de Desconto -->
            <div class="mt-3 pt-2 border-top" id="div_cupom">
              <label class="checkout-label" for="cupom"><i class="bi bi-ticket-perforated text-danger me-1"></i> Possui Cupom de Desconto?</label>
              <div class="d-flex gap-2">
                <input class="checkout-input" type="text" name="cupom" id="cupom" placeholder="Digite o código do cupom">
                <button onclick="cupom()" class="btn btn-dark rounded-3 px-3 fw-bold" type="button" name="btn_cupom" id="btn_cupom">
                  Aplicar
                </button>
              </div>
            </div>

            <!-- Observações do Pedido -->
            <div class="checkout-input-group mt-3 mb-0" id="area-obs">
              <label class="checkout-label" for="obs"><i class="bi bi-chat-left-text me-1"></i> Observações Gerais do Pedido</label>
              <input type="text" class="checkout-input" name="obs" id="obs" value="" placeholder="Ex: Tocar interfone, deixar na portaria...">
            </div>

          </div>
        </div>
      </div>

    </div>

    <!-- Card de Resumo de Taxas e Descontos -->
    <div class="checkout-summary-card">
      <div id="area-taxa" class="w-100">
        <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 13px;">
          <span class="text-muted">Taxa de Entrega: <strong class="text-danger">R$ <span id="taxa-entrega">0,00</span></strong></span>
          <span class="badge bg-light text-dark border rounded-pill"><i class="bi bi-clock me-1"></i> ~<?php echo $previsao_entrega ?> min</span>
        </div>
      </div>

      <?php if($taxa_extra > 0){ ?>
        <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 13px;">
          <span class="text-muted"><?php echo $titulo_taxa ?></span>
          <strong class="text-danger">R$ <?php echo $taxa_extra ?></strong>
        </div>
      <?php } ?>

      <div id="desconto_div" style="display:none;" class="mt-1">
        <div class="d-flex justify-content-between align-items-center text-success" style="font-size: 13px;">
          <span><b>Desconto do Cupom</b></span>
          <span><b>- R$ <span id="total_cupom"></span></b></span>
        </div>
      </div>

      <div class="d-flex justify-content-between align-items-center pt-1">
        <span class="fw-bold text-dark" style="font-size: 14px;">TOTAL A PAGAR</span>
        <span class="fw-bold text-success" style="font-size: 16px;">R$ <span class="total-carrinho-espelho"><?php echo $total_carrinhoF ?></span></span>
      </div>
    </div>

  </div>

</div>


<input type="hidden" id="entrega" value="<?php echo $valor_entrega ?>">
<input type="hidden" id="pagamento">
<input type="hidden" id="taxa-entrega-input">
<input type="hidden" id="id_cliente">
<input type="hidden" id="valor-cupom">


<!-- Sticky Footer Fixo de Conclusão do Pedido -->
<div class="sticky-footer-cart">
  <a href="javascript:void(0)" id="botao_finalizar" onclick="finalizarPedido()" class="btn-ifood-cart">
    <span>Concluir Pedido</span>
    <span>R$ <span id="total-carrinho-finalizar"><?php echo $total_carrinhoF ?></span> <i class="bi bi-check-circle-fill ms-1"></i></span>
  </a>

  <div align="center" id="div_img" style="display:none"><img src="img/loading.gif" width="100%" height="40px" style="max-width: 300px; object-fit: contain;"></div>
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
    $('.delivery-option-card').removeClass('selected');
    $('#card_entrega_retirar').addClass('selected');
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
    $('.delivery-option-card').removeClass('selected');
    $('#card_entrega_local').addClass('selected');
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
    $('.delivery-option-card').removeClass('selected');
    $('#card_entrega_delivery').addClass('selected');
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
    $('.payment-pill-card').removeClass('selected');
    $('#card_pgto_pix').addClass('selected');

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
    $('.payment-pill-card').removeClass('selected');
    $('#card_pgto_dinheiro').addClass('selected');

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
    $('.payment-pill-card').removeClass('selected');
    $('#card_pgto_debito').addClass('selected');

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
    $('.payment-pill-card').removeClass('selected');
    $('#card_pgto_credito').addClass('selected');

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
        $('.total-carrinho-espelho').text(split[1]);
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

        if (split[0] !== undefined && split[0].trim() !== "") {
          $('#nome').val(split[0].trim());
        }
        $('#rua').val(split[1]);
        $('#numero').val(split[2]);
        $('#bairro').val(split[3]).change();
        $('#complemento').val(split[4]);
        $('#taxa-entrega-input').val(split[5]);
        $('#taxa-entrega').text(split[6]);
        $('#id_cliente').val(split[7]);
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
          $('.total-carrinho-espelho').text(split[0]);
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
            var totalFormatado = total_final_compra.toLocaleString('pt-br', {
              minimumFractionDigits: 2
            });
            $('#total-carrinho-finalizar').text(totalFormatado);
            $('.total-carrinho-espelho').text(totalFormatado);

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
  .select2-container--default .select2-selection--single {
    border: 1.5px solid #dee2e6 !important;
    border-radius: 12px !important;
    height: 44px !important;
    background-color: #f8f9fa !important;
    padding: 0 8px;
  }

  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 42px !important;
    font-size: 14px !important;
    color: #212529 !important;
  }

  .select2-container--default .select2-selection--single .select2-selection__arrow {
    top: 8px !important;
    right: 8px !important;
  }

  .select2-container--default.select2-container--open .select2-selection--single {
    border-color: #ea1d2c !important;
    background-color: #ffffff !important;
  }
</style>
