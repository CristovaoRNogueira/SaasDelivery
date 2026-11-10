<?php
@session_start();
if (!empty($_GET['url_empresa'])) {
  $_SESSION['url_site'] = $_GET['url_empresa'];
}
require_once("cabecalho.php");

$sessao = @$_SESSION['sessao_usuario'];
$nome_mesa = @$_SESSION['nome_mesa'];
$pedido_balcao = @$_SESSION['pedido_balcao'];
$pedido_balcao = @$_SESSION['pedido_balcao'];
// Formatando a data e hora para exibir apenas horas e minutos
$horario_aberturaF = date('H:i', strtotime($horario_abertura));
// Formatando a data e hora para exibir apenas horas e minutos
$horario_fechamentoF = date('H:i', strtotime($horario_fechamento));


if (@$_SESSION['nome_mesa'] != '') {
  unset($pedido_balcao);
}


$id_edicao = 0;
if (@$_SESSION['id_edicao'] != "") {
  $id_edicao = $_SESSION['id_edicao'];
}


if($id_edicao == ""){
// VERFICAR SE ESTÁ ABERTO O ESTABELICMENTO

if ($nome_mesa == '' and $pedido_balcao == "") {
  if ($status_estabelecimento == "Fechado") {

    echo "<script>
        window.onload = function() {
            Swal.fire({
                title: 'Delivery Fechado no momento',
                text: '$texto_fechamento',
                icon: 'info',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Ação a ser realizada após o clique no botão de confirmação
                    console.log('Botão OK clicado!');
                    // Você pode redirecionar para outra página, se necessário
                    window.location.href = '<?php echo $url_site ?>';
                }
            });
        };
    </script>";



    //echo "<script>window.alert('$texto_fechamento')</script>";
    //echo "<script>window.location='index.php'</script>";
    exit();
  }


  $data = date('Y-m-d');
  //verificar se está aberto hoje
  $diasemana = array("Domingo", "Segunda-Feira", "Terça-Feira", "Quarta-Feira", "Quinta-Feira", "Sexta-Feira", "Sábado");
  $diasemana_numero = date('w', strtotime($data));
  $dia_procurado = $diasemana[$diasemana_numero];

  //percorrer os dias da semana que ele trabalha
  $query = $pdo->query("SELECT * FROM dias where dia = '$dia_procurado' and empresa = '$id_empresa'");
  $res = $query->fetchAll(PDO::FETCH_ASSOC);
  if (@count($res) > 0) {

    echo "<script>
        window.onload = function() {
            Swal.fire({
                title: 'Delivery Fechado no momento',
                text: 'Estamos Fechados! Não funcionamos Hoje!',
                icon: 'info',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Ação a ser realizada após o clique no botão de confirmação
                    console.log('Botão OK clicado!');
                    // Você pode redirecionar para outra página, se necessário
                    window.location.href = '<?php echo $url_site ?>';
                }
            });
        };
    </script>";

    //echo "<script>window.alert('Estamos Fechados! Não funcionamos Hoje!')</script>";
    //echo "<script>window.location='index.php'</script>";
    exit();
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
        if (
          $now < $end
        ) {
        } else {

          echo "<script>
        window.onload = function() {
            Swal.fire({
                title: 'Delivery Fechado no momento',
                text: '$texto_fechamento_horario $horario_aberturaF às $horario_fechamentoF',
                icon: 'info',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Ação a ser realizada após o clique no botão de confirmação
                    console.log('Botão OK clicado!');
                    // Você pode redirecionar para outra página, se necessário
                    window.location.href = '<?php echo $url_site ?>';
                }
            });
        };
    </script>";

          //echo "<script>window.alert('$texto_fechamento_horario')</script>";
          //echo "<script>window.location='index.php'</script>";
          exit();
        }
      }
    } else {


      echo "<script>
        window.onload = function() {
            Swal.fire({
                title: 'Delivery Fechado no momento',
                text: '$texto_fechamento_horario $horario_aberturaF às $horario_fechamentoF',
                icon: 'info',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Ação a ser realizada após o clique no botão de confirmação
                    console.log('Botão OK clicado!');
                    // Você pode redirecionar para outra página, se necessário
                    window.location.href = '<?php echo $url_site ?>';
                }
            });
        };
    </script>";



      //echo "<script>window.alert('$texto_fechamento_horario')</scripwindow.location=>";
      //echo "<script>window.location='index.php'</script>";
      exit();
    }
  }
}


}

?>

<style type="text/css">
  body {
    background: #f8f9fa;
  }
</style>

<div class="main-container" style="max-width: 720px; margin: 0 auto; padding-bottom: 130px;">

  <nav class="navbar bg-white fixed-top" style="box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);">
    <div class="container-fluid" style="max-width: 720px;">
      <div class="navbar-brand d-flex align-items-center">
        <a href="<?php echo $url_site ?>" class="text-dark text-decoration-none"><big><i class="bi bi-arrow-left"></i></big></a>
        <span class="fw-bold text-dark" style="margin-left: 12px; font-size: 14px;">SACOLA / RESUMO <?php echo $nome_mesa ?> <?php echo @$_SESSION['pedido_balcao'] ?></span>
      </div>

      <a class="text-decoration-none" href="<?php echo $url_site ?>">
        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" style="font-size: 12px;">
          <i class="bi bi-plus-lg me-1"></i> Adicionar mais
        </button>
      </a>
    </div>
  </nav>

  <!-- Lista de Itens do Carrinho -->
  <ol class="list-group ps-0" style="margin-top: 68px; margin-bottom: 16px;" id="listar-itens-carrinho">
  </ol>

  <!-- Resumo Financeiro Limpo -->
  <div class="cart-summary-card">
    <h6 class="fw-bold text-dark mb-3" style="font-size: 14px;">Resumo de valores</h6>
    <div class="d-flex justify-content-between align-items-center mb-2 text-muted" style="font-size: 13px;">
      <span>Subtotal dos itens</span>
      <span>R$ <span class="total-do-pedido-resumo">0,00</span></span>
    </div>
    <?php if ($nome_mesa == "") { ?>
    <div class="d-flex justify-content-between align-items-center mb-2 text-muted" style="font-size: 13px;">
      <span>Taxa de entrega</span>
      <span>R$ 0,00</span>
    </div>
    <?php } ?>
    <hr class="my-2" style="opacity: 0.12;">
    <div class="d-flex justify-content-between align-items-center mt-2">
      <span class="fw-bold text-dark" style="font-size: 15px;">Total parcial</span>
      <span class="fw-bold text-success" style="font-size: 17px;">R$ <span id="total-do-pedido">0,00</span></span>
    </div>
  </div>

</div>

<!-- Sticky Footer Estilo iFood -->
<div class="sticky-footer-cart">
  <?php if ($id_edicao > 0) { ?>
    <a href="" onclick="window.close()" class="btn-ifood-cart">
      <span>Finalizar Edição</span>
      <span>R$ <span class="total-do-pedido-resumo">0,00</span> <i class="bi bi-check-circle-fill ms-1"></i></span>
    </a>
  <?php } else { ?>
    <?php if ($nome_mesa == "") { ?>
      <a href="<?php echo $url_site ?>/finalizar" class="btn-ifood-cart">
        <span>Finalizar Pedido</span>
        <span>R$ <span class="total-do-pedido-resumo">0,00</span> <i class="bi bi-arrow-right-circle-fill ms-1"></i></span>
      </a>
    <?php } else { ?>
      <div class="d-flex justify-content-between align-items-center mx-auto" style="max-width: 520px;">
        <span class="fw-bold text-dark">Total da Mesa</span>
        <span class="fw-bold text-success" style="font-size: 17px;">R$ <span class="total-do-pedido-resumo">0,00</span></span>
      </div>
    <?php } ?>
  <?php } ?>
</div>


</body>

</html>





<!-- Modal -->
<div class="modal fade" id="modalObs" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><span id="nome_item"></span></h5>
        <button type="button" id="btn-fechar-obs" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="form-obs">
        <div class="modal-body">
          <div class="destaque-qtd">
            <b>OBSERVAÇÕES</b>
            <div class="form-group mt-3">
              <textarea maxlength="255" class="form-control" type="text" name="obs" id="obs" placeholder="Deseja adicionar alguma Observação?"></textarea>
            </div>
          </div>

          <input type="hidden" name="id" id="id_obs">
          <br><small>
            <div id="mensagem-obs" align="center"></div>
          </small>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>




<!-- Modal -->
<div class="modal fade" id="modalAdc" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><span id="nome_item_adc"></span></h5>
        <button type="button" id="btn-fechar-adc" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div id="listar-adc-carrinho">

        </div>

      </div>
    </div>
  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="modalGrades" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><span id="nome_item_grade"></span></h5>
        <button type="button" id="btn-fechar-grade" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div id="listar-grade-carrinho">

        </div>

      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  $(document).ready(function() {
    listarCarrinho()
  });

  function listarCarrinho() {

    $.ajax({
      url: 'js/ajax/listar-itens-carrinho.php',
      method: 'POST',
      data: {},
      dataType: "html",

      success: function(result) {

        $("#listar-itens-carrinho").html(result);

      }
    });
  }
</script>


<script type="text/javascript">
  $("#form-obs").submit(function() {

    event.preventDefault();
    var formData = new FormData(this);

    $.ajax({
      url: 'js/ajax/editar-obs-carrinho.php',
      type: 'POST',
      data: formData,

      success: function(mensagem) {
        $('#mensagem-obs').text('');
        $('#mensagem-obs').removeClass()
        if (mensagem.trim() == "Salvo com Sucesso") {
          $('#btn-fechar-obs').click();
          listarCarrinho();

        } else {

          $('#mensagem-obs').addClass('text-danger')
          $('#mensagem-obs').text(mensagem)
        }


      },

      cache: false,
      contentType: false,
      processData: false,

    });

  });
</script>
