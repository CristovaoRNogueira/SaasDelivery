<?php
@session_start();
require_once("cabecalho.php");

$url = $_GET['url'];
$id_usuario = @$_SESSION['id'];
$pedido_balcao = @$_SESSION['pedido_balcao'];
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


$nome_mesa = @$_SESSION['nome_mesa'];
$id_mesa = @$_SESSION['id_mesa'];
$id_ab_mesa = @$_SESSION['id_ab_mesa'];

$texto_botao = 'Adicionar ao Carrinho';

if ($_SESSION['nome_mesa'] != '') {
	unset($_SESSION['pedido_balcao']);
}

if ($nome_mesa != "") {
	$texto_botao = 'Adicionar a Mesa';
}

$query = $pdo->query("SELECT * FROM categorias where url = '$url' and empresa = '$id_empresa' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {
	$nome = $res[0]['nome'];
	$descricao = $res[0]['descricao'];
	$id = $res[0]['id'];
	$mais_sabores = $res[0]['mais_sabores'];


	$pdo->query("DELETE FROM temp where carrinho = 0 and sessao = '$sessao' and empresa = '$id_empresa' ");


}






if (@$_SESSION['id_edicao'] != "") {
  $id_edicao = $_SESSION['id_edicao'];
}


if(@$id_edicao == ""){
// VERFICAR SE ESTÁ ABERTO O ESTABELICMENTO

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


}

?>

<div class="main-container">

	<nav class="navbar bg-light fixed-top" style="box-shadow: 0px 3px 5px rgba(0, 0, 0, 0.20);">
		<div class="container-fluid">
			<div class="navbar-brand">
				<a href="<?php echo $url_site ?>"><big><i class="bi bi-arrow-left"></i></big></a>
				<span style="margin-left: 15px; font-size:14px"><?php echo mb_strtoupper($nome) ?>
					<?php echo @$_SESSION['nome_mesa'] ?><?php echo @$_SESSION['pedido_balcao'] ?></span>
			</div>

			<?php require_once("icone-carrinho.php") ?>

		</div>
	</nav>


	<ol class="list-group " style="margin-top: 65px">

		<?php
		//VERIFICAR SE POSSUI VARIAÇÕES NA CATEGORIA
		$query = $pdo->query("SELECT * FROM variacoes_cat where categoria = '$id' and empresa = '$id_empresa' order by id asc");
		$res = $query->fetchAll(PDO::FETCH_ASSOC);
		$total_reg = @count($res);
		if ($total_reg > 0) {

			echo '<ul class="list-group form-check">';
			echo '<span class="titulo-itens-pizza"><span style="margin-left:20px">Escolha o tamanho</span></span>';
			for ($i = 0; $i < $total_reg; $i++) {
				$id_var = $res[$i]['id'];
				$nome_var = $res[$i]['nome'];
				$sigla_var = $res[$i]['sigla'];
				$descricao_var = $res[$i]['descricao'];
				$sabores_var = $res[$i]['sabores'];
		?>

				<li onclick="selecVar('<?php echo $sigla_var ?>', '<?php echo $id_var ?>')"
					class="list-group-item d-flex justify-content-between align-items-center">
					<small>(<?php echo $sigla_var ?>) <?php echo $nome_var ?> / <?php echo $descricao_var ?> / <small>Até
							<?php echo $sabores_var ?> Sabores</small></small>
					<input onchange="selecionarVar('<?php echo $sigla_var ?>', '<?php echo $id_var ?>')" class="form-check-input"
						type="radio" value="<?php echo $sigla_var ?>" name="variacoes" id="variacoes_<?php echo $sigla_var ?>">
				</li>




		<?php }
			echo '</ul>';
		} ?>




		<?php
		//VERIFICAR SE POSSUI VARIAÇÕES NA CATEGORIA
		$query = $pdo->query("SELECT * FROM bordas where categoria = '$id' and ativo = 'Sim' and empresa = '$id_empresa' order by id asc");
		$res = $query->fetchAll(PDO::FETCH_ASSOC);
		$total_reg = @count($res);
		if ($total_reg > 0) {
			echo '<ul class="list-group form-check" style="margin-bottom:15px">';
			echo '<span class="titulo-itens-pizza"><span style="margin-left:20px">Escolha a borda</span></span>';
		?>

			<li onclick="selectBorda('0')" class="list-group-item d-flex justify-content-between align-items-center">
				<small>Borda Catupiry + <span class="text-success">R$ 0,00</span></small>
				<input onchange="selecionarBorda('0')" class="form-check-input" type="radio" name="borda" id="borda_0" checked
					value="0">
			</li>

			<?php
			for ($i = 0; $i < $total_reg; $i++) {
				$id_bor = $res[$i]['id'];
				$nome_bor = $res[$i]['nome'];
				$valor_bor = $res[$i]['valor'];

			?>

				<li onclick="selectBorda('<?php echo $id_bor ?>')"
					class="list-group-item d-flex justify-content-between align-items-center">
					<small><?php echo $nome_bor ?> + <span class="text-success">R$ <?php echo $valor_bor ?></span></small>
					<input onchange="selecionarBorda('<?php echo $id_bor ?>')" class="form-check-input" type="radio" name="borda"
						id="borda_<?php echo $id_bor ?>" value="<?php echo $id_bor ?>">
				</li>



		<?php }
			echo '</ul>';
		} ?>

		<span class="titulo-itens-pizza"><span style="margin-left:20px">Escolha <span id="quant_sabores"> os </span> sabores</span></span>
		<?php
		$query = $pdo->query("SELECT * FROM produtos where categoria = '$id' and ativo = 'Sim' and empresa = '$id_empresa'");
		$res = $query->fetchAll(PDO::FETCH_ASSOC);
		$total_reg = @count($res);
		if ($total_reg > 0) {
			for ($i = 0; $i < $total_reg; $i++) {
				foreach ($res[$i] as $key => $value) {
				}
				$id_produto = $res[$i]['id'];
				$foto = $res[$i]['foto'];
				$nome = $res[$i]['nome'];
				$descricao = $res[$i]['descricao'];
				$url = $res[$i]['url'];
				$estoque = $res[$i]['estoque'];
				$tem_estoque = $res[$i]['tem_estoque'];
				$valor = $res[$i]['valor_venda'];
				$valorF = number_format($valor, 2, ',', '.');
				$promocao = $res[$i]['promocao'];

				if ($tem_estoque == 'Sim' and $estoque <= 0) {
					$mostrar = '';
					$url_produto = '#';
				} else {
					$mostrar = 'ocultar';
					$url_produto = 'produto-' . $url;
				}


				if ($promocao == 'Sim') {
					$mostrar_promo = '';
				} else {
					$mostrar_promo = 'ocultar';
				}




		?>


				<li class="list-group-item d-flex justify-content-between align-items-start ">

					<img onclick="selectPizzas('<?php echo $id_produto ?>')" class="<?php echo $mostrar ?>" src="img/esgotado.png"
						width="65px" height="65px" style="position:absolute; right:0; top:0px">

					<?php if ($mostrar != "") { ?>
						<img onclick="selectPizzas('<?php echo $id_produto ?>')" class="<?php echo $mostrar_promo ?>"
							src="img/promocao2.png" width="65px" height="65px" style="position:absolute; right:0; top:0px; opacity:80%">
					<?php } ?>

					<div class="row" style="width:100%">
						<div class="col-10">

							<div class="me-auto">
								<div class="fw-bold titulo-item">
									<input class="form-check-input" type="checkbox" value="<?php echo $id_produto ?>"
										id="seletor-<?php echo $id_produto ?>" onchange="selecionar('<?php echo $id_produto ?>')"
										style="margin-top: 5px">
									<small onclick="selectPizzas('<?php echo $id_produto ?>')"> <?php echo $nome ?></small>
								</div>

								<div onclick="selectPizzas('<?php echo $id_produto ?>')" class="subtitulo-item-menor" style="color:#450703">
									<?php echo $descricao ?></div>
								<span onclick="selectPizzas('<?php echo $id_produto ?>')" class="valor-item-maior">
									<?php
									$query2 = $pdo->query("SELECT * FROM variacoes where produto = '$id_produto' and ativo = 'Sim' and empresa = '$id_empresa'");
									$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
									$total_reg2 = @count($res2);
									if ($total_reg2 > 0) {
										for ($i2 = 0; $i2 < $total_reg2; $i2++) {
											foreach ($res2[$i2] as $key => $value) {
											}
											$sigla = $res2[$i2]['sigla'];
											$valorvar = $res2[$i2]['valor'];
											$valorvarF = number_format($valorvar, 2, ',', '.');

											echo '<span style="font-size:10px">(' . $sigla . ') R$ ' . $valorvarF;
											if ($i2 < $total_reg2 - 1) {
												echo ' / ';
											}
											echo '</span>';
										}
									} else {
										echo 'R$ ' . $valorF;
									}
									?>

								</span>


							</div>

						</div>


						<div class="col-2" style="margin-top: 10px;" align="right">
							<img onclick="selectPizzas('<?php echo $id_produto ?>')" class=""
								src="sistema/painel/images/produtos/<?php echo $foto ?>" width="60px" height="60px">
						</div>



					</div>

				</li>


		<?php }
		} ?>








		<?php
		$query = $pdo->query("SELECT * FROM adicionais_cat where categoria = '$id' and ativo = 'Sim' and empresa = '$id_empresa'");
		$res = $query->fetchAll(PDO::FETCH_ASSOC);
		$total_reg = @count($res);
		if ($total_reg > 0) { ?>

			<span class="titulo-itens-pizza"><span
					style="margin-left:20px">Escolha os Adicionais</span></span>;
			<div style="margin-top: -18px">
				<?php
				for ($i = 0; $i < $total_reg; $i++) {
					foreach ($res[$i] as $key => $value) {
					}
					$id_adc = $res[$i]['id'];
					$nome = $res[$i]['nome'];
					$valor = $res[$i]['valor'];
					$valorF = number_format($valor, 2, ',', '.');

				?>


					<li class="list-group-item d-flex  ">
						<span style="font-size: 12px"><?php echo $nome ?> (R$ <?php echo $valorF ?>) </span>

						<span class="direita" style="font-size:14px">

							<span><button onclick="dim('<?php echo $id_adc ?>', '<?php echo $sessao ?>')"
									style="background:transparent; border:none"><i
										class="bi bi-dash-circle-fill text-danger"></i></button></span>
							<span> <b><input id="quantidade_item_<?php echo $id_adc ?>" value="0"
										style="background: transparent; border:none; width:20px; text-align: center"></b> </span>
							<span><button onclick="aum('<?php echo $id_adc ?>', '<?php echo $sessao ?>')"
									style="background:transparent; border:none"><i
										class="bi bi-plus-circle-fill text-success"></i></button></span>

						</span>

					</li>




			<?php }
			} ?>

			</div>


			<div class="destaque-qtd">
				<b>OBSERVAÇÕES</b>
				<div class="form-group mt-3">
					<textarea maxlength="255" class="form-control" type="text" name="obs" id="obs" placeholder="Deseja adicionar alguma Observação?"></textarea>
				</div>
			</div>



			<input type="hidden" id="variacao_sel">
			<input type="hidden" id="borda_sel">
			<input type="hidden" id="ids">
			<input type="hidden" id="quantidade_sabores">



			<div class="d-grid gap-2 col-8 mx-auto mt-4 mb-4">
				<button onclick="addCarrinho()" class="btn btn-warning btn-lg"><?php echo $texto_botao ?> <i class="fal fa-long-arrow-right"></i></button>
			</div>

	</ol>


</div>

</body>

</html>


<script type="text/javascript">
	function addCarrinho() {
		var variacao = $('#variacao_sel').val();
		var borda = $('#borda_sel').val();
		var mesa = "<?= $id_ab_mesa ?>";

		if (variacao == "") {
			alert("Selecione um Tamanho");
			return;
		}

		var ids = $('#ids').val();
		if (ids == "") {
			alert("Selecione pelo menos um sabor de pizza!");
			return;
		}

		var obs = $('#obs').val();
		var quantidade_sabores = $('#quantidade_sabores').val();

		var id_prod = ids.split("-");

		if (id_prod.length - 1 > quantidade_sabores) {
			alert('Você pode escolher só até ' + quantidade_sabores + ' Sabores!');
			return;
		}

		$.ajax({
			url: 'js/ajax/add-carrinho-pizza.php',
			method: 'POST',
			data: {
				variacao,
				ids,
				obs,
				borda,
				mesa
			},
			dataType: "text",

			success: function(mensagem) {
				alert(mensagem)                
				if (mensagem.trim() == "Inserido com Sucesso") {
					window.location = 'carrinho';
				}

			},

		});
	}

	function selecionarVar(sigla, id) {
		$('#variacao_sel').val(id);

		$.ajax({
			url: 'js/ajax/buscar_variacoes.php',
			method: 'POST',
			data: {
				id
			},
			dataType: "text",

			success: function(mensagem) {
				$('#quant_sabores').text(" até " + mensagem);
				$('#quantidade_sabores').val(mensagem);
			},

		});

	}


	function selecionarBorda(id) {
		var id = $('#borda_' + id).val();
		$('#borda_sel').val(id);

	}


	function selecionar(id) {

		var ids = $('#ids').val();

		if ($('#seletor-' + id).is(":checked") == true) {
			var novo_id = ids + id + '-';
			$('#ids').val(novo_id);
		} else {
			var retirar = ids.replace(id + '-', '');
			$('#ids').val(retirar);

		}


	}



	function func_adicional(id) {

		var id = $('#adicional_' + id).val();

		if ($('#adicional_' + id).is(":checked") == true) {
			var acao = 'Sim';
		} else {
			var acao = 'Não';
		}

		adicionar(id, acao, 'Sim');


	}



	function adicionar(id, quantidade) {

		$.ajax({
			url: 'js/ajax/adicionais.php',
			method: 'POST',
			data: {
				id,
				quantidade
			},
			dataType: "text",

			success: function(mensagem) {

				if (mensagem.trim() == "Alterado com Sucesso") {

				}

			},

		});
	}


	function selecVar(sigla, id) {
		$('#variacoes_' + sigla).prop('checked', true);
		selecionarVar(sigla, id)
	}

	function selectBorda(id) {
		$('#borda_' + id).prop('checked', true);
		selecionarBorda(id)
	}

	function selectAdc(id) {

		if ($('#adicional_' + id).is(":checked") == true) {
			var acao = 'Não';
			$('#adicional_' + id).prop('checked', false);
		} else {
			var acao = 'Sim';
			$('#adicional_' + id).prop('checked', true);
		}

		adicionar(id, acao, 'Sim');

	}



	function selectPizzas(id) {

		if ($('#seletor-' + id).is(":checked") == true) {
			$('#seletor-' + id).prop('checked', false);
		} else {
			$('#seletor-' + id).prop('checked', true);
		}

		selecionar(id);

	}
</script>



<script type="text/javascript">
	function aum(id, cat) {
		var quant = $("#quantidade_item_" + id).val();
		var quantidade = parseFloat(quant) + 1;

		$("#quantidade_item_" + id).val(quantidade);
		adicionar(id, quantidade)

	}

	function dim(id, cat) {
		var quant = $("#quantidade_item_" + id).val();
		var quantidade = parseFloat(quant) - 1;

		if (quantidade == -1) {
			var quant = $("#quantidade_item_" + id).val('0');
			return;
		}

		$("#quantidade_item_" + id).val(quantidade);
		adicionar(id, quantidade)



	}
</script>
