<?php
require_once("../funcoes/extenso.php");


$query = $pdo->query("SELECT * from pagar where id = '$id' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if (@count($res) > 0) {
  $id = $res[0]['id'];
  $descricao = $res[0]['descricao'];
  $tipo = $res[0]['tipo'];
  $valor = $res[0]['valor'];
  $data_lanc = $res[0]['data_lanc'];
  $data_pgto = $res[0]['data_pgto'];
  $data_venc = $res[0]['vencimento'];
  $usuario_lanc = $res[0]['usuario_lanc'];
  $usuario_baixa = $res[0]['usuario_pgto'];
  $foto = $res[0]['arquivo'];
  $pessoa = $res[0]['pessoa'];
  $pago = $res[0]['pago'];
  $funcionario = $res[0]['funcionario'];

  $valorF = number_format($valor, 2, ',', '.');
  $data_lancF = implode('/', array_reverse(explode('-', $data_lanc)));
  $data_pgtoF = implode('/', array_reverse(explode('-', $data_pgto)));
  $data_vencF = implode('/', array_reverse(explode('-', $data_venc)));


  $nome_pessoa = '';
  $query2 = $pdo->query("SELECT * FROM fornecedores where id = '$pessoa'");
  $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
  $total_reg2 = @count($res2);
  if ($total_reg2 > 0) {
    $nome_pessoa = $res2[0]['nome'];
  }


  $query2 = $pdo->query("SELECT * FROM usuarios where id = '$funcionario'");
  $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
  $total_reg2 = @count($res2);
  if ($total_reg2 > 0) {
    $nome_pessoa = $res2[0]['nome'];
  }
}

?>




<!DOCTYPE html>
<html>

<head>
  <title>Recibo de Pagamento</title>

  <?php
  if ($tipo_rel != 'pdf') {
  ?>
    <link rel="icon" href="<?php echo $url_sistema ?>/img/<?php echo $favicon ?>" type="image/x-icon">

  <?php } ?>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">


  <style>
    @page {
      margin: 0px;

    }

    body {
      padding: 10px;

    }

    * {
      box-sizing: border-box;
    }

    .receipt-main {
      width: 95%;
      padding: 15px;
      font-size: 12px;
      border: 1px solid #000;
    }

    .receipt-title {
      text-align: center;
      text-transform: uppercase;
      font-size: 20px;
      font-weight: 600;
      margin: 0;
    }

    .receipt-label {
      font-weight: 600;
    }

    .text-large {
      font-size: 16px;
    }

    .receipt-section {
      margin-top: 10px;
    }

    .receipt-footer {
      text-align: center;
      background: #ff0000;
    }

    .receipt-signature {
      height: 80px;
      margin: 50px 0;
      padding: 0 50px;
      background: #fff;

      .receipt-line {
        margin-bottom: 10px;
        border-bottom: 1px solid #000;
      }

      p {
        text-align: center;
        margin: 0;
      }

      .direita {
        position: absolute;
        right: 30px;
      }


      .imagem {
        width: 150px;
        position: absolute;
        left: 15px;
        top: 20px;

      }
  </style>


</head>

<body>



  <div class="receipt-main">

    <?php
    if ($tipo_rel != '') {
    ?>
      <img class="imagem" src="<?php echo $url_sistema ?>sistema/img/<?php echo $logo_sistema ?>">

    <?php } ?>


    <p class="receipt-title">Recibo de Pagamento</p>
    <br>

    <div class="receipt-section pull-left">
      <span class="receipt-label text-large">Número:</span>
      <span class="text-large"><?php echo date('Y/m') ?></span>

      <span class="text-large receipt-label direita">VALOR R$ <?php echo $valorF ?></span>

    </div>



    <div class="clearfix"></div>
    <br>

    <div class="receipt-section">
      <span><big>
          Pagamos ao <b><?php echo $nome_pessoa ?></b> a quantia de R$ <b><?php echo $valorF ?> </b> reais na data <b><?php echo $data_pgtoF ?></b> correspondente a(ao) <?php echo $descricao ?>.

        </big></span>

    </div>

    <br><br>
    <div align="center">

      <br><br>

      ______________________________________________________________________<br>
      (<b>ASSINATURA</b>)

    </div>
    <br>

    <div align="center">
      <?php echo mb_strtoupper($nome_sistema) ?> CNPJ <?php echo $cnpj_sistema ?><br>
      <small><?php echo $endereco_sistema ?> - <?php echo $telefone_sistema ?></small>
    </div>

  </div>




</body>

</html>