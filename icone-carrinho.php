<!-- BOTÃO DO CARRINHO -->
<div class="col-xl-3 mr-5">
  <div class="header__info d-flex align-items-center">
    <div class="header__info-cart tpcolor__oasis ml-10 tp-cart-toggle">
      <button><i><img src="img/icon/cart-1.svg" alt=""></i>
        <span id="total-itens-carrinho"></span>
      </button>
    </div>
  </div>
</div>



<!-- header-cart-start -->
<div class="tpcartinfo tp-cart-info-area p-relative">
  <button class="tpcart__close"><i class="icon-x"></i></button>
  <div class="tpcart">
    <h4 class="tpcart__title">CARRINHO</h4>
    <div class="tpcart__product" style="display:flex;flex-direction:column;max-height:80vh;">
      <div class="tpcart__product-list" style="flex:1;min-height:0;overflow-y:auto;">
        <ul id="listar-itens-carrinho-icone" style="margin-left:-40px;">
          <!-- itens do carrinho aqui -->
        </ul>
      </div>
      <div class="tpcart__checkout">
        <div class="tpcart__total-price d-flex justify-content-between align-items-center mb-3">
          <span class="text-muted" style="font-size: 13px; font-weight: 600;">Subtotal:</span>
          <span class="heilight-price text-success fw-bold" style="font-size: 18px;">R$ <span id="total-carrinho-icone"></span></span>
        </div>
        <div class="tpcart__checkout-btn d-flex flex-column gap-2">
          <a class="tpcheck-btn link-neutro2" href="carrinho">VER CARRINHO</a>
          <a class="tpcheck-btn tpcheck-btn-finalizar link-neutro2" href="finalizar">FINALIZAR COMPRA</a>
        </div>
      </div>
    </div>
  </div>
</div>



<!-- JS here -->

<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/nice-select.js"></script>
<script src="assets/js/main.js"></script>



<script type="text/javascript">
  $(document).ready(function() {
    listarCarrinhoIcone()
  });

  function listarCarrinhoIcone() {

    $.ajax({
      url: 'js/ajax/listar-itens-carrinho-icone.php',
      method: 'POST',
      data: {},
      dataType: "html",

      success: function(result) {
        //alert(result)
        $("#listar-itens-carrinho-icone").html(result);

      }
    });
  }
</script>