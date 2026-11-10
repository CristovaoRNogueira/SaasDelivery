<?php
@session_start();
$url_completa = $_GET['url'];
$sabores = @$_GET['sabores'];
$id_usuario = @$_SESSION['id'];

$nome_mesa = @$_SESSION['nome_mesa'];
$id_mesa = @$_SESSION['id_mesa'];
$id_ab_mesa = @$_SESSION['id_ab_mesa'];
$pedido_balcao = @$_SESSION['pedido_balcao'];

require_once("cabecalho.php");

// Formatando a data e hora para exibir apenas horas e minutos
$horario_aberturaF = date('H:i', strtotime($horario_abertura));
// Formatando a data e hora para exibir apenas horas e minutos
$horario_fechamentoF = date('H:i', strtotime($horario_fechamento));


if (@$_SESSION['sessao_usuario'] == "") {
	$sessao = date('Y-m-d-H:i:s-') . rand(0, 1500);
	$_SESSION['sessao_usuario'] = $sessao;
} else {
	$sessao = $_SESSION['sessao_usuario'];
}

$texto_botao = 'Adicionar ao Carrinho';

if (@$_SESSION['id'] != "") {
	$id_usuario = $_SESSION['id'];
} else {
	$id_usuario = '';
}

if ($nome_mesa != "") {
	$texto_botao = 'Adicionar a Mesa';
}

if ($nome_mesa != '') {
	unset($_SESSION['pedido_balcao']);
}


$query = $pdo->query("SELECT * FROM carrinho where sessao = '$sessao' and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {
	$id_cliente = $res[0]['cliente'];
	$mesa_carrinho = $res[0]['mesa'];
	$nome_cli_pedido = $res[0]['nome_cliente'];


	$query = $pdo->query("SELECT * FROM clientes where id = '$id_cliente'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	if (@count($res) > 0) {
		$nome_cliente = $res[0]['nome'];
		$telefone_cliente = $res[0]['telefone'];
	} else {
		$nome_cliente = $nome_cli_pedido;
		$telefone_cliente = '';
	}
}

$separar_url = explode("_", $url_completa);
$url = $separar_url[0];
$item = @$separar_url[1];


$query = $pdo->query("SELECT * FROM produtos where url = '$url' and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {
	$nome = $res[0]['nome'];
	$descricao = $res[0]['descricao'];
	$foto = $res[0]['foto'];
	$id_prod = $res[0]['id'];
	$valor = $res[0]['valor_venda'];
	$valorF = number_format($valor, 2, ',', '.');
	$categoria = $res[0]['categoria'];
	$val_promocional = $res[0]['val_promocional'];
	$promocao = $res[0]['promocao'];
}

if ($val_promocional != 0 and $promocao != 'Não') {
	$valor = $val_promocional;
}


$query = $pdo->query("SELECT * FROM categorias where id = '$categoria'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {
	$url_cat = $res[0]['url'];
}

$valor_item = $valor;
$valor_itemF = number_format($valor_item, 2, ',', '.');


if ($nome_mesa == '' and $pedido_balcao == "") {

	// VERIFICAR SE DELIVERY ESTÁ PAUSADO
  if (@$pausar_delivery == "Sim") {

    echo "<script>
        window.onload = function() {
            Swal.fire({
                title: 'Delivery Pausado no momento',
                text: 'Estamos temporariamente sem receber pedidos. Tente novamente em instantes.',
                icon: 'warning',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '".$url_site."';
                }
            });
        };
    </script>";

    exit();
  }

  
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
                    window.location.href = '".$url_site."';
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
                    window.location.href = '".$url_site."';
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
				if ($now < $end) {
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
                    window.location.href = '".$url_site."';
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
                    window.location.href = '".$url_site."';
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


?>

<style type="text/css">
	body {
		background: #f2f2f2;
	}
</style>


<div class="main-container" style="background:#fff">


	<nav class="navbar bg-light fixed-top" style="box-shadow: 0px 3px 5px rgba(0, 0, 0, 0.20);">
		<div class="container-fluid">
			<div class="navbar-brand">

				<a href="categoria-<?php echo $url_cat ?>"><big><i class="bi bi-arrow-left"></i></big><small></small></a>

				<span style="margin-left: 15px; font-size:14px">RESUMO DO ITEM <?php echo $nome_mesa ?> <?php echo @$_SESSION['pedido_balcao'] ?></span>
			</div>

			<?php require_once("icone-carrinho.php") ?>

		</div>
	</nav>



	<div class="destaque" style="border:  solid 1px #ababab; border-radius: 10px;">
		<b><?php echo mb_strtoupper($nome); ?></b>
		<div class="col-lg-4 col-md-6">
			<div class="tpbanner__item">
				<img class="ocultar_img" src="sistema/painel/images/produtos/<?php echo $foto ?>" width="70%" height="70%">
			</div>
		</div>
	</div>


	<?php if ($sabores != 2 and $sabores != 1) { ?>
		<div class="destaque-qtd" style="border:  solid 1px #ababab; border-radius: 10px;">
			<b>QUANTIDADE</b>
			<span class="direita">
				<big>
					<span><a href="javascript:void(0)" onclick="diminuirQuant()"><i class="bi bi-dash-circle-fill text-danger"></i></a></span>
					<span> <b><span id="quant"></span></b> </span>
					<span><a href="javascript:void(0)" onclick="aumentarQuant()"><i class="bi bi-plus-circle-fill text-success"></i></a></span>
				</big>
			</span>
		</div>
	<?php } ?>

	<input type="hidden" id="quantidade" value="1">
	<input type="hidden" id="total_item_input" value="<?php echo $valor_item ?>">


	<div class="destaque-qtd" style="border:  solid 1px #ababab; border-radius: 10px;">
		<b>OBSERVAÇÕES</b>
		<div class="form-group mt-3">
			<textarea maxlength="255" class="form-control" type="text" name="obs" id="obs" placeholder="Deseja adicionar alguma Observação?"></textarea>
		</div>
	</div>

</div>


<div class="destaque-qtd" style="border: solid 1px #ababab; border-radius: 10px;">
	<b>TOTAL</b>
	<span class="direita">
		<b>R$ <span id="total_item"><?php echo $valor_itemF ?></b>
	</span>
</div>



<div class="d-grid gap-2 col-8 mx-auto mt-4">
	<button onclick="addCarrinho()" class="btn btn-warning btn-lg"><?php echo $texto_botao ?> <i class="fal fa-long-arrow-right"></i></button>
</div>



</body>

</html>




<script type="text/javascript">
	$(document).ready(function() {
		var quant = $("#quantidade").val();
		$("#quant").text(quant);

	});

	function aumentarQuant() {
		var quant = $("#quantidade").val();
		var novo_valor = parseInt(quant) + parseInt(1);
		$("#quant").text(novo_valor)
		$("#quantidade").val(novo_valor);


		var total_quant = parseInt(quant) + parseInt(1);
		var total_inicial = '<?= $valor_item ?>';
		var total_it = parseFloat(total_inicial) * parseFloat(total_quant);
		$("#total_item").text(total_it.toFixed(2));
		$("#total_item_input").val(total_it);
	}

	function diminuirQuant() {
		var quant = $("#quantidade").val();
		if (quant > 1) {
			var novo_valor = parseInt(quant) - parseInt(1);
			$("#quant").text(novo_valor)
			$("#quantidade").val(novo_valor)

			var total_quant = parseInt(quant) - parseInt(1);
			var total_inicial = '<?= $valor_item ?>';
			var total_it = parseFloat(total_inicial) * parseFloat(total_quant);
			$("#total_item").text(total_it.toFixed(2));
			$("#total_item_input").val(total_it);
		}

	}
</script>





<script type="text/javascript">
	function addCarrinho() {
		var quantidade = $('#quantidade').val();
		var total_item = $('#total_item_input').val();
		var produto = "<?= $id_prod ?>";
		var obs = $('#obs').val();
		var mesa = "<?= $id_ab_mesa ?>";


		total_item = parseFloat(total_item) / parseFloat(quantidade);

		if (total_item == "") {
			return;
		}

		$.ajax({
			url: 'js/ajax/add-carrinho.php',
			method: 'POST',
			data: {
				quantidade,
				total_item,
				produto,
				obs,
				mesa
			},
			dataType: "text",

			success: function(mensagem) {


				if (mensagem.trim() == "Inserido com Sucesso") {

					window.location = 'carrinho';

				} else {
					alert(mensagem)
				}

			},

		});
	}

	function teste() {
		alert('teste')
	}
</script>
