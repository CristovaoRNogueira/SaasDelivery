<?php
@session_start();
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
                    window.location.href = 'index';
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
                    window.location.href = 'index';
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
                    window.location.href = 'index';
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
                    window.location.href = 'index';
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
    background: #f2f2f2;
  }
</style>

<div class="main-container">

  <nav class="navbar bg-light fixed-top" style="box-shadow: 0px 3px 5px rgba(0, 0, 0, 0.20);">
    <div class="container-fluid">
      <div class="navbar-brand">
        <a href="<?php echo $url_site ?>"><big><i class="bi bi-arrow-left"></i></big></a>
        <span style="margin-left: 15px; font-size:14px">RESUMO DO PEDIDO <?php echo $nome_mesa ?> <?php echo @$_SESSION['pedido_balcao'] ?></span>

      </div>

      <a class="" href="<?php echo $url_site ?>">
        <div class="">
          <button type="button" class="btn btn-warning btn-sm">Comprar Mais?</button>
        </div>
      </a>


    </div>
  </nav>



  <ol class="list-group" style="margin-top: 65px; margin-bottom: 95px; overflow: scroll; height:100%; scrollbar-width: thin;"
    id="listar-itens-carrinho">
  </ol>


</div>


<div class="area-pedidos">
  <div class="total-pedido" style="border: solid 1px #ababab; border-radius: 10px;">
    <big>
      <span><b>SUB TOTAL</b></span>
      <span class="direita"> <b>R$ <span id="total-do-pedido"></span></b></span>
    </big>
  </div>


  <div class="d-grid gap-2 mt-4 abaixo">

    <?php if($id_edicao > 0){ ?>
          <a href='' onclick="window.close()" class="btn btn-warning btn-lg">Finalizar Edição <i class="fal fa-long-arrow-right"></i></a>
    <?php }else{ ?>

      <?php if ($nome_mesa == "") { ?>
        <a href='finalizar' class="btn btn-warning btn-lg">Finalizar Pedido <i class="fal fa-long-arrow-right"></i></a>
      <?php } else { ?>
        
      <?php } ?>


  <?php } ?>
  </div>
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
