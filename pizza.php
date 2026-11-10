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

<style>
	.wizard-stepper {
		display: flex;
		justify-content: space-between;
		align-items: center;
		background: #ffffff;
		padding: 10px 10px;
		border-radius: 14px;
		box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
		margin-bottom: 16px;
		gap: 6px;
	}
	.wizard-step-pill {
		flex: 1;
		text-align: center;
		padding: 7px 4px;
		border-radius: 10px;
		font-size: 11px;
		font-weight: 600;
		color: #6c757d;
		background: #f8f9fa;
		cursor: pointer;
		transition: all 0.25s ease;
		user-select: none;
		border: 1px solid transparent;
		white-space: nowrap;
	}
	.wizard-step-pill.active {
		background: #ea1d2c;
		color: #ffffff;
		box-shadow: 0 3px 8px rgba(234, 29, 44, 0.28);
	}
	.wizard-step-pill.completed {
		background: #fff3f3;
		color: #ea1d2c;
		border-color: #f5c2c7;
	}
	.wizard-section {
		animation: fadeInStep 0.28s ease-in-out;
	}
	@keyframes fadeInStep {
		from {
			opacity: 0;
			transform: translateY(8px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}
	.wizard-header-box {
		display: flex;
		justify-content: space-between;
		align-items: center;
		background: #f8f9fa;
		padding: 12px 16px;
		border-radius: 12px;
		margin-bottom: 12px;
		border-left: 4px solid #ea1d2c;
	}
	.wizard-header-title {
		font-size: 15px;
		font-weight: 700;
		color: #212529;
		margin: 0;
	}
	.wizard-header-sub {
		font-size: 12px;
		color: #6c757d;
		margin: 2px 0 0 0;
	}
	.ifood-card-item {
		border: 1px solid #e9ecef !important;
		border-radius: 12px !important;
		margin-bottom: 10px !important;
		padding: 14px 16px !important;
		transition: all 0.2s ease;
		cursor: pointer;
		background: #ffffff;
		box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
	}
	.ifood-card-item:hover {
		border-color: #ea1d2c !important;
	}
	.ifood-card-item.card-selecionado {
		border: 2px solid #ea1d2c !important;
		background-color: #fffafa !important;
	}
	.ifood-card-item.sabor-desabilitado {
		opacity: 0.45;
		background-color: #f8f9fa !important;
		cursor: not-allowed;
	}
	.search-sabor-wrapper {
		position: sticky;
		top: 58px;
		z-index: 900;
		background: #ffffff;
		padding: 10px 0;
		margin-bottom: 10px;
		border-bottom: 1px solid #f1f1f1;
	}
	.search-sabor-box {
		display: flex;
		align-items: center;
		background: #f3f4f6;
		border-radius: 50px;
		padding: 8px 16px;
		border: 1px solid #e5e7eb;
		transition: all 0.2s ease;
	}
	.search-sabor-box:focus-within {
		background: #ffffff;
		border-color: #ea1d2c;
		box-shadow: 0 0 0 3px rgba(234, 29, 44, 0.12);
	}
	.search-sabor-box input {
		border: none;
		background: transparent;
		width: 100%;
		outline: none;
		font-size: 14px;
		margin-left: 8px;
		color: #212529;
	}
	.sticky-footer-ifood {
		position: fixed;
		bottom: 0;
		left: 0;
		right: 0;
		background: #ffffff;
		padding: 12px 16px;
		box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.12);
		z-index: 1050;
		display: none;
		animation: slideUpFooter 0.25s ease-out;
	}
	@keyframes slideUpFooter {
		from {
			transform: translateY(100%);
		}
		to {
			transform: translateY(0);
		}
	}
	.btn-ifood-action {
		background: #ea1d2c;
		color: #ffffff;
		border: none;
		border-radius: 50px;
		font-weight: 700;
		font-size: 15px;
		padding: 12px 20px;
		width: 100%;
		max-width: 500px;
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin: 0 auto;
		box-shadow: 0 4px 12px rgba(234, 29, 44, 0.3);
		transition: background 0.2s ease;
	}
	.btn-ifood-action:hover, .btn-ifood-action:active {
		background: #c81421;
		color: #ffffff;
	}
	.btn-voltar-etapa {
		font-size: 12px;
		font-weight: 600;
		color: #ea1d2c;
		background: #fff3f3;
		border: 1px solid #f5c2c7;
		border-radius: 50px;
		padding: 4px 12px;
		text-decoration: none;
		cursor: pointer;
	}
</style>

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


	<ol class="list-group" style="margin-top: 68px; padding-bottom: 100px;">

		<!-- Barra de Progresso Wizard (Stepper 4 Etapas) -->
		<div class="wizard-stepper">
			<div id="step-pill-1" class="wizard-step-pill active" onclick="irParaEtapa(1)">
				1. Tamanho
			</div>
			<div id="step-pill-2" class="wizard-step-pill" onclick="tentarIrParaEtapa(2)">
				2. Sabores
			</div>
			<div id="step-pill-3" class="wizard-step-pill" onclick="tentarIrParaEtapa(3)">
				3. Borda
			</div>
			<div id="step-pill-4" class="wizard-step-pill" onclick="tentarIrParaEtapa(4)">
				4. Adicionais
			</div>
		</div>

		<!-- ==================== ETAPA 1: ESCOLHA O TAMANHO ==================== -->
		<div id="etapa-1" class="wizard-section">
			<?php
			//VERIFICAR SE POSSUI VARIAÇÕES NA CATEGORIA
			$query = $pdo->query("SELECT * FROM variacoes_cat where categoria = '$id' and empresa = '$id_empresa' order by id asc");
			$res = $query->fetchAll(PDO::FETCH_ASSOC);
			$total_reg = @count($res);
			if ($total_reg > 0) {
			?>
				<div class="wizard-header-box">
					<div>
						<h6 class="wizard-header-title">Escolha o tamanho</h6>
						<p class="wizard-header-sub">Selecione uma opção para avançar</p>
					</div>
					<span class="badge bg-danger rounded-pill">Obrigatório</span>
				</div>

				<ul class="list-group form-check ps-0">
				<?php
				for ($i = 0; $i < $total_reg; $i++) {
					$id_var = $res[$i]['id'];
					$nome_var = $res[$i]['nome'];
					$sigla_var = $res[$i]['sigla'];
					$descricao_var = $res[$i]['descricao'];
					$sabores_var = $res[$i]['sabores'];

					// Consulta o menor valor (A partir de) para esta sigla nos produtos ativos desta categoria
					$query_min = $pdo->query("SELECT MIN(v.valor) AS menor_valor FROM variacoes v INNER JOIN produtos p ON v.produto = p.id WHERE p.categoria = '$id' AND p.ativo = 'Sim' AND v.sigla = '$sigla_var' AND v.ativo = 'Sim' AND v.valor > 0 AND v.empresa = '$id_empresa' AND p.empresa = '$id_empresa'");
					$res_min = $query_min->fetch(PDO::FETCH_ASSOC);
					$menor_valor = (float) (@$res_min['menor_valor'] ?? 0);
					$menor_valorF = ($menor_valor > 0) ? number_format($menor_valor, 2, ',', '.') : '';
				?>
					<li onclick="selecVar('<?php echo $sigla_var ?>', '<?php echo $id_var ?>')"
						id="card_var_<?php echo $sigla_var ?>"
						class="list-group-item d-flex justify-content-between align-items-center ifood-card-item card-variacao">
						<div>
							<div class="fw-bold text-dark" style="font-size: 14px;">
								(<?php echo $sigla_var ?>) <?php echo $nome_var ?>
							</div>
							<small class="text-muted d-block">
								<?php echo $descricao_var ?> • Até <?php echo $sabores_var ?> <?php echo ($sabores_var > 1) ? 'Sabores' : 'Sabor' ?>
							</small>
							<?php if ($menor_valor > 0) { ?>
								<div class="text-success fw-bold mt-1" style="font-size: 13px;">
									A partir de R$ <?php echo $menor_valorF ?>
								</div>
							<?php } ?>
						</div>
						<input onchange="selecionarVar('<?php echo $sigla_var ?>', '<?php echo $id_var ?>')" class="form-check-input ms-2"
							type="radio" value="<?php echo $sigla_var ?>" name="variacoes" id="variacoes_<?php echo $sigla_var ?>"
							style="transform: scale(1.2);">
					</li>
				<?php } ?>
				</ul>
			<?php } ?>
		</div>


		<!-- ==================== ETAPA 2: ESCOLHA OS SABORES ==================== -->
		<div id="etapa-2" class="wizard-section" style="display: none;">
			<div class="wizard-header-box">
				<div>
					<h6 class="wizard-header-title">Escolha <span id="quant_sabores">os</span> sabores</h6>
					<p class="wizard-header-sub" id="subtitulo_limite_sabores">Marque os sabores desejados abaixo</p>
				</div>
				<div class="d-flex align-items-center gap-2">
					<span id="badge_contador_sabores" class="badge bg-dark rounded-pill" style="font-size: 12px;">0 selecionados</span>
					<button type="button" class="btn-voltar-etapa" onclick="irParaEtapa(1)">
						<i class="bi bi-chevron-left"></i> Tamanho
					</button>
				</div>
			</div>

			<!-- Campo de Pesquisa de Sabor em Tempo Real -->
			<div class="search-sabor-wrapper">
				<div class="search-sabor-box">
					<i class="bi bi-search text-muted"></i>
					<input type="text" id="busca_sabor" onkeyup="filtrarSabores()" placeholder="Buscar sabor ou ingrediente...">
					<button type="button" id="btn_limpar_busca" onclick="limparBuscaSabor()" style="display:none; border:none; background:transparent; color:#6c757d;">
						<i class="bi bi-x-circle-fill"></i>
					</button>
				</div>
			</div>

			<div id="lista_sabores_container">
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

						<li id="card_sabor_<?php echo $id_produto ?>"
							data-search="<?php echo htmlspecialchars(mb_strtolower($nome . ' ' . $descricao), ENT_QUOTES, 'UTF-8') ?>"
							class="list-group-item d-flex justify-content-between align-items-start ifood-card-item item-sabor-pizza"
							style="position: relative;">

							<img onclick="selectPizzas('<?php echo $id_produto ?>')" class="<?php echo $mostrar ?>" src="img/esgotado.png"
								width="65px" height="65px" style="position:absolute; right:0; top:0px">

							<?php if ($mostrar != "") { ?>
								<img onclick="selectPizzas('<?php echo $id_produto ?>')" class="<?php echo $mostrar_promo ?>"
									src="img/promocao2.png" width="65px" height="65px" style="position:absolute; right:0; top:0px; opacity:80%">
							<?php } ?>

							<div class="row align-items-center" style="width:100%">
								<div class="col-9 col-sm-10">
									<div class="me-auto">
										<div class="fw-bold titulo-item d-flex align-items-center gap-2">
											<input class="form-check-input check-sabor-pizza" type="checkbox" value="<?php echo $id_produto ?>"
												id="seletor-<?php echo $id_produto ?>" onchange="selecionar('<?php echo $id_produto ?>')"
												style="margin-top: 0; transform: scale(1.2);">
											<small onclick="selectPizzas('<?php echo $id_produto ?>')" style="font-size: 14px;"> <?php echo $nome ?></small>
										</div>

										<div onclick="selectPizzas('<?php echo $id_produto ?>')" class="subtitulo-item-menor mt-1" style="color:#6c757d; font-size: 12px;">
											<?php echo $descricao ?>
										</div>
									</div>
								</div>

								<div class="col-3 col-sm-2 text-end">
									<img onclick="selectPizzas('<?php echo $id_produto ?>')" class="rounded-3"
										src="sistema/painel/images/produtos/<?php echo $foto ?>" width="64px" height="64px" style="object-fit: cover;">
								</div>
							</div>

						</li>

				<?php }
				} ?>
			</div>

			<div id="aviso_sem_sabor" class="text-center text-muted py-4" style="display: none;">
				<i class="bi bi-search" style="font-size: 24px;"></i>
				<p class="mt-2 mb-0" style="font-size: 14px;">Nenhum sabor encontrado para sua busca.</p>
			</div>

			<!-- Botão auxiliar caso o usuário queira avançar com menos sabores que o limite máximo -->
			<div id="box_avancar_sabores" class="mt-3" style="display: none;">
				<button type="button" onclick="avancarDeSabores()" class="btn-ifood-action">
					<span id="texto_avancar_sabores">Sabores selecionados</span>
					<span>Continuar <i class="bi bi-arrow-right-circle-fill ms-1"></i></span>
				</button>
			</div>
		</div>


		<!-- ==================== ETAPA 3: ESCOLHA A BORDA ==================== -->
		<?php
		//VERIFICAR SE POSSUI BORDAS NA CATEGORIA
		$query = $pdo->query("SELECT * FROM bordas where categoria = '$id' and ativo = 'Sim' and empresa = '$id_empresa' order by id asc");
		$res = $query->fetchAll(PDO::FETCH_ASSOC);
		$total_reg_bordas = @count($res);
		?>
		<div id="etapa-3" class="wizard-section" style="display: none;">
			<div class="wizard-header-box">
				<div>
					<h6 class="wizard-header-title">Escolha a borda</h6>
					<p class="wizard-header-sub">Selecione a borda da sua pizza</p>
				</div>
				<button type="button" class="btn-voltar-etapa" onclick="irParaEtapa(2)">
					<i class="bi bi-chevron-left"></i> Sabores
				</button>
			</div>

			<?php if ($total_reg_bordas > 0) { ?>
				<ul class="list-group form-check ps-0" style="margin-bottom:15px">
					<li onclick="selectBorda('0')" id="card_borda_0"
						class="list-group-item d-flex justify-content-between align-items-center ifood-card-item card-borda">
						<div class="fw-semibold text-dark" style="font-size: 14px;">
							Borda Catupiry + <span class="text-success fw-bold">R$ 0,00</span>
						</div>
						<input onchange="selecionarBorda('0')" class="form-check-input ms-2" type="radio" name="borda" id="borda_0" checked
							value="0" style="transform: scale(1.2);">
					</li>

					<?php
					for ($i = 0; $i < $total_reg_bordas; $i++) {
						$id_bor = $res[$i]['id'];
						$nome_bor = $res[$i]['nome'];
						$valor_bor = $res[$i]['valor'];
					?>
						<li onclick="selectBorda('<?php echo $id_bor ?>')" id="card_borda_<?php echo $id_bor ?>"
							class="list-group-item d-flex justify-content-between align-items-center ifood-card-item card-borda">
							<div class="fw-semibold text-dark" style="font-size: 14px;">
								<?php echo $nome_bor ?> + <span class="text-success fw-bold">R$ <?php echo $valor_bor ?></span>
							</div>
							<input onchange="selecionarBorda('<?php echo $id_bor ?>')" class="form-check-input ms-2" type="radio" name="borda"
								id="borda_<?php echo $id_bor ?>" value="<?php echo $id_bor ?>" style="transform: scale(1.2);">
						</li>
					<?php } ?>
				</ul>
			<?php } ?>
		</div>


		<!-- ==================== ETAPA 4: ADICIONAIS E OBSERVAÇÕES ==================== -->
		<div id="etapa-4" class="wizard-section" style="display: none;">
			<div class="wizard-header-box">
				<div>
					<h6 class="wizard-header-title">Adicionais e Observações</h6>
					<p class="wizard-header-sub">Personalize os detalhes finais da sua pizza</p>
				</div>
				<button type="button" class="btn-voltar-etapa" onclick="voltarDeAdicionais()">
					<i class="bi bi-chevron-left"></i> Voltar
				</button>
			</div>

			<?php
			$query = $pdo->query("SELECT * FROM adicionais_cat where categoria = '$id' and ativo = 'Sim' and empresa = '$id_empresa'");
			$res = $query->fetchAll(PDO::FETCH_ASSOC);
			$total_reg = @count($res);
			if ($total_reg > 0) { ?>

				<div class="wizard-header-box mt-2">
					<div>
						<h6 class="wizard-header-title">Escolha os Adicionais</h6>
						<p class="wizard-header-sub">Opcional</p>
					</div>
				</div>
				<div>
					<?php
					for ($i = 0; $i < $total_reg; $i++) {
						foreach ($res[$i] as $key => $value) {
						}
						$id_adc = $res[$i]['id'];
						$nome = $res[$i]['nome'];
						$valor = $res[$i]['valor'];
						$valorF = number_format($valor, 2, ',', '.');
					?>
						<li class="list-group-item d-flex justify-content-between align-items-center ifood-card-item">
							<span style="font-size: 13px; font-weight: 500;"><?php echo $nome ?> <span class="text-success fw-bold">(R$ <?php echo $valorF ?>)</span></span>

							<span class="direita d-flex align-items-center" style="font-size:16px">
								<span><button type="button" onclick="dim('<?php echo $id_adc ?>', '<?php echo $sessao ?>')"
										style="background:transparent; border:none"><i
											class="bi bi-dash-circle-fill text-danger"></i></button></span>
								<span> <b><input id="quantidade_item_<?php echo $id_adc ?>" value="0"
											style="background: transparent; border:none; width:28px; text-align: center"></b> </span>
								<span><button type="button" onclick="aum('<?php echo $id_adc ?>', '<?php echo $sessao ?>')"
										style="background:transparent; border:none"><i
											class="bi bi-plus-circle-fill text-success"></i></button></span>
							</span>
						</li>
					<?php } ?>
				</div>
			<?php } ?>

			<div class="destaque-qtd mt-3" style="border-radius: 12px;">
				<b>OBSERVAÇÕES</b>
				<div class="form-group mt-2">
					<textarea maxlength="255" class="form-control rounded-3" type="text" name="obs" id="obs" placeholder="Ex: Tirar cebola, massa bem assada..."></textarea>
				</div>
			</div>
		</div>

		<input type="hidden" id="variacao_sel">
		<input type="hidden" id="borda_sel">
		<input type="hidden" id="ids">
		<input type="hidden" id="quantidade_sabores">

	</ol>

	<!-- Sticky Footer estilo iFood (Visível permanentemente na Etapa 4) -->
	<div id="sticky-footer-carrinho" class="sticky-footer-ifood">
		<button type="button" onclick="addCarrinho()" class="btn-ifood-action">
			<span id="texto_sticky_sabores">Finalizar Pizza</span>
			<span><?php echo $texto_botao ?> <i class="bi bi-arrow-right-circle-fill ms-1"></i></span>
		</button>
	</div>

</div>

</body>

</html>


<script type="text/javascript">
	var temBordasCategoria = <?php echo ($total_reg_bordas > 0) ? 'true' : 'false'; ?>;

	function atualizarStepper(etapaAtiva) {
		for (var i = 1; i <= 4; i++) {
			var pill = $('#step-pill-' + i);
			pill.removeClass('active completed');
			if (i < etapaAtiva) {
				pill.addClass('completed');
			} else if (i === etapaAtiva) {
				pill.addClass('active');
			}
		}
	}

	function irParaEtapa(etapa) {
		$('.wizard-section').hide();
		$('#etapa-' + etapa).fadeIn(220);
		atualizarStepper(etapa);
		window.scrollTo({ top: 0, behavior: 'smooth' });

		if (etapa === 4) {
			$('#sticky-footer-carrinho').fadeIn(200);
		} else {
			$('#sticky-footer-carrinho').fadeOut(150);
		}

		if (etapa === 2) {
			atualizarControleLimiteSabores(false);
		}
	}

	function tentarIrParaEtapa(etapa) {
		var variacao = $('#variacao_sel').val();
		if (etapa >= 2 && variacao === '') {
			Swal.fire({
				title: 'Escolha o tamanho',
				text: 'Selecione primeiro o tamanho da pizza para avançar.',
				icon: 'warning',
				confirmButtonColor: '#ea1d2c'
			});
			return;
		}
		var marcados = $('.check-sabor-pizza:checked').length;
		if (etapa >= 3 && marcados === 0) {
			Swal.fire({
				title: 'Escolha os sabores',
				text: 'Selecione pelo menos 1 sabor para avançar.',
				icon: 'warning',
				confirmButtonColor: '#ea1d2c'
			});
			irParaEtapa(2);
			return;
		}
		if (etapa === 3 && !temBordasCategoria) {
			irParaEtapa(4);
			return;
		}
		irParaEtapa(etapa);
	}

	function avancarDeSabores() {
		if (temBordasCategoria) {
			irParaEtapa(3);
		} else {
			irParaEtapa(4);
		}
	}

	function voltarDeAdicionais() {
		if (temBordasCategoria) {
			irParaEtapa(3);
		} else {
			irParaEtapa(2);
		}
	}

	function addCarrinho() {
		var variacao = $('#variacao_sel').val();
		var borda = $('#borda_sel').val();
		var mesa = "<?= $id_ab_mesa ?>";

		if (variacao == "") {
			alert("Selecione um Tamanho");
			irParaEtapa(1);
			return;
		}

		var ids = $('#ids').val();
		if (ids == "") {
			alert("Selecione pelo menos um sabor de pizza!");
			irParaEtapa(2);
			return;
		}

		var obs = $('#obs').val();
		var quantidade_sabores = parseInt($('#quantidade_sabores').val(), 10) || 1;

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
				alert(mensagem);
				if (mensagem.trim() == "Inserido com Sucesso") {
					window.location = 'carrinho';
				}
			},

		});
	}

	function selecionarVar(sigla, id) {
		var variacaoAnterior = $('#variacao_sel').val();
		$('#variacao_sel').val(id);

		$('.card-variacao').removeClass('card-selecionado');
		$('#card_var_' + sigla).addClass('card-selecionado');

		// Se trocou de tamanho, reseta os sabores marcados para respeitar o novo limite
		if (variacaoAnterior !== "" && variacaoAnterior !== id) {
			$('.check-sabor-pizza').prop('checked', false).prop('disabled', false);
			$('.item-sabor-pizza').removeClass('card-selecionado sabor-desabilitado');
			$('#ids').val('');
		}

		$.ajax({
			url: 'js/ajax/buscar_variacoes.php',
			method: 'POST',
			data: {
				id
			},
			dataType: "text",

			success: function(mensagem) {
				var limite = parseInt(mensagem.trim(), 10) || 1;
				$('#quant_sabores').text(" até " + limite);
				$('#quantidade_sabores').val(limite);
				$('#subtitulo_limite_sabores').text('Escolha de 1 até ' + limite + (limite > 1 ? ' sabores' : ' sabor'));
				atualizarControleLimiteSabores(false);

				// Escolheu Tamanho -> Auto-avança para Etapa 2 (Sabores)
				setTimeout(function() {
					irParaEtapa(2);
				}, 180);
			},

		});

	}


	function selecionarBorda(id) {
		var valorBorda = $('#borda_' + id).val();
		$('#borda_sel').val(valorBorda);

		$('.card-borda').removeClass('card-selecionado');
		$('#card_borda_' + id).addClass('card-selecionado');

		// Escolheu Borda -> Auto-avança para Etapa 4 (Adicionais e Observações)
		setTimeout(function() {
			irParaEtapa(4);
		}, 180);
	}


	function selecionar(id) {
		var limite = parseInt($('#quantidade_sabores').val(), 10) || 1;
		var checkbox = $('#seletor-' + id);
		var totalMarcados = $('.check-sabor-pizza:checked').length;

		// Trava de segurança caso tente marcar além do limite
		if (checkbox.is(':checked') && totalMarcados > limite) {
			checkbox.prop('checked', false);
			return;
		}

		// Reconstrói o campo #ids de forma consistente a partir dos checkboxes ativos
		var novoIds = '';
		$('.check-sabor-pizza:checked').each(function() {
			novoIds += $(this).val() + '-';
		});
		$('#ids').val(novoIds);

		var acabouDeMarcar = checkbox.is(':checked');
		if (acabouDeMarcar) {
			$('#card_sabor_' + id).addClass('card-selecionado');
		} else {
			$('#card_sabor_' + id).removeClass('card-selecionado');
		}

		atualizarControleLimiteSabores(acabouDeMarcar);
	}

	function atualizarControleLimiteSabores(autoAvancarAoLimite) {
		var limite = parseInt($('#quantidade_sabores').val(), 10) || 1;
		var marcados = $('.check-sabor-pizza:checked').length;

		$('#badge_contador_sabores').text(marcados + ' / ' + limite + (limite > 1 ? ' sabores' : ' sabor'));
		$('#texto_sticky_sabores').text(marcados + ' de ' + limite + (limite > 1 ? ' sabores' : ' sabor'));

		if (marcados >= limite) {
			$('#badge_contador_sabores').removeClass('bg-dark').addClass('bg-success');
			// Desabilita imediatamente todos os demais checkboxes não marcados
			$('.check-sabor-pizza:not(:checked)').each(function() {
				$(this).prop('disabled', true);
				var prodId = $(this).val();
				$('#card_sabor_' + prodId).addClass('sabor-desabilitado');
			});

			// Atingiu o limite máximo de sabores -> Auto-avança para Etapa 3 (Borda)
			if (autoAvancarAoLimite) {
				setTimeout(function() {
					avancarDeSabores();
				}, 250);
			}
		} else {
			$('#badge_contador_sabores').removeClass('bg-success').addClass('bg-dark');
			// Reabilita os checkboxes caso o usuário desmarque algum sabor
			$('.check-sabor-pizza').prop('disabled', false);
			$('.item-sabor-pizza').removeClass('sabor-desabilitado');
		}

		if (marcados > 0) {
			$('#texto_avancar_sabores').text(marcados + ' de ' + limite + (limite > 1 ? ' sabores selecionados' : ' sabor selecionado'));
			$('#box_avancar_sabores').fadeIn(150);
		} else {
			$('#box_avancar_sabores').fadeOut(150);
		}
	}

	function normalizarTexto(texto) {
		return (texto || '').toString().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
	}

	function filtrarSabores() {
		var termo = normalizarTexto($('#busca_sabor').val().trim());
		if (termo.length > 0) {
			$('#btn_limpar_busca').show();
		} else {
			$('#btn_limpar_busca').hide();
		}

		var visiveis = 0;
		$('.item-sabor-pizza').each(function() {
			var conteudo = normalizarTexto($(this).attr('data-search'));
			if (conteudo.indexOf(termo) !== -1) {
				$(this).removeClass('d-none').addClass('d-flex');
				visiveis++;
			} else {
				$(this).removeClass('d-flex').addClass('d-none');
			}
		});

		if (visiveis === 0) {
			$('#aviso_sem_sabor').show();
		} else {
			$('#aviso_sem_sabor').hide();
		}
	}

	function limparBuscaSabor() {
		$('#busca_sabor').val('');
		filtrarSabores();
		$('#busca_sabor').focus();
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
		selecionarVar(sigla, id);
	}

	function selectBorda(id) {
		$('#borda_' + id).prop('checked', true);
		selecionarBorda(id);
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
		var checkbox = $('#seletor-' + id);
		if (checkbox.is(':disabled')) {
			return;
		}

		if (checkbox.is(":checked") == true) {
			checkbox.prop('checked', false);
		} else {
			checkbox.prop('checked', true);
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
