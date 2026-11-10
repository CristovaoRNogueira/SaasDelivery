<?php
@session_start();
if (!empty($_GET['url_empresa'])) {
	$_SESSION['url_site'] = $_GET['url_empresa'];
}
require_once("cabecalho.php");
$url = $_GET['url'];


$query = $pdo->query("SELECT * FROM categorias where url = '$url' and empresa = '$id_empresa' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {
	$nome = $res[0]['nome'];
	$descricao = $res[0]['descricao'];
	$id = $res[0]['id'];


	@session_start();
	$sessao = @$_SESSION['sessao_usuario'];
	$sessao_pedido_balcao = @$_SESSION['pedido_balcao'];



	$pdo->query("DELETE FROM temp where carrinho = 0 and sessao = '$sessao' and empresa = '$id_empresa'");
}

if (@$_SESSION['nome_mesa'] != '') {
	unset($_SESSION['pedido_balcao']);
};


?>

<div class="main-container">
	<div class="container">
		<nav class="navbar bg-light fixed-top" style="box-shadow: 0px 3px 5px rgba(0, 0, 0, 0.20);">
			<div class="container-fluid">
				<div class="navbar-brand">
					<a href="<?php echo $url_site ?>"><big><i class="bi bi-arrow-left"></i></big></a>
					<span style="margin-left: 15px; font-size:14px"><?php echo mb_strtoupper($nome) ?> <?php echo @$_SESSION['nome_mesa'] ?> <?php echo @$_SESSION['pedido_balcao'] ?></span>
				</div>

				<?php require_once("icone-carrinho.php") ?>

			</div>
		</nav>

		<ol class="list-group " style="margin-top: 65px">


			<?php
			$query = $pdo->query("SELECT * FROM produtos where categoria = '$id' and ativo = 'Sim' order by valor_venda asc");
			$res = $query->fetchAll(PDO::FETCH_ASSOC);
			$total_reg = @count($res);
			if ($total_reg > 0) {
				for ($i = 0; $i < $total_reg; $i++) {
					foreach ($res[$i] as $key => $value) {
					}
					$id = $res[$i]['id'];
					$foto = $res[$i]['foto'];
					$nome = $res[$i]['nome'];
					$descricao = $res[$i]['descricao'];
					$url = $res[$i]['url'];
					$estoque = $res[$i]['estoque'];
					$tem_estoque = $res[$i]['tem_estoque'];
					$valor = $res[$i]['valor_venda'];
					$promocao = $res[$i]['promocao'];
					$delivery = $res[$i]['delivery'];
					$val_promocional = $res[$i]['val_promocional'];
					$promocao = $res[$i]['promocao'];

					$val_promocionalF = @number_format($val_promocional, 2, ',', '.');


					$valorF = @number_format($valor, 2, ',', '.');

					if (@$_SESSION['nome_mesa'] == "" and $delivery == 'Não') {
						continue;
					}


					if ($tem_estoque == 'Sim' and $estoque <= 0) {
						$mostrar = '';
						$url_produto = '#';
					} else {
						$mostrar = 'ocultar';
						$url_produto = $url_site . '/produto-' . $url;
					}


					if ($promocao == 'Sim') {
						$mostrar_promo = '';
					} else {
						$mostrar_promo = 'ocultar';
					}


					//verificar se o produto tem grade
					$query3 = $pdo->query("SELECT * FROM grades where produto = '$id' and ativo = 'Sim'");
					$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
					$total_adc = @count($res3);
					if ($total_adc > 0) {
						if ($tem_estoque == 'Sim' and $estoque <= 0) {
							$url_produto = '#';
						} else {
							$url_produto = $url_site . '/adicionais-' . $url;
						}
					} else {
						if ($tem_estoque == 'Sim' and $estoque <= 0) {
							$url_produto = '#';
						} else {
							$url_produto = $url_site . '/observacoes-' . $url;
						}
					}



			?>

					<a href="<?php echo $url_produto ?>" class="link-neutro">
						<li class="list-group-item d-flex justify-content-between align-items-start mb-2" style="box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px; border-radius: 8px;">

							<img class="<?php echo $mostrar ?>" src="img/esgotado.png" width="65px" height="65px" style="position:absolute; right:0; top:0px">

							<?php if ($mostrar != "") { ?>
								<img class="<?php echo $mostrar_promo ?>" src="img/promocao2.png" width="65px" height="65px" style="position:absolute; right:0; top:0px; opacity:80%">
							<?php } ?>

							<div class="row" style="width:100%">
								<div class="col-10">

									<div class="me-auto">
										<div class="fw-bold titulo-item" style="font-weight: 600"><?php echo $nome ?></div>
										<div class="subtitulo-item-menor" style="font-size:10px; font-weight: 380"><?php echo $descricao ?></div>
										<span class="valor-item-maior">
											<?php
											$query2 = $pdo->query("SELECT * FROM grades where produto = '$id' and tipo_item = 'Variação' and ativo = 'Sim' order by id asc");
											$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
											$total_reg2 = @count($res2);
											if ($total_reg2 > 0) {
												$id_grade = $res2[0]['id'];

												$query3 = $pdo->query("SELECT * FROM itens_grade where grade = '$id_grade' and ativo = 'Sim' order by id asc");
												$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
												$total_reg3 = @count($res3);
												if ($total_reg3 > 0) {
													for ($i3 = 0; $i3 < $total_reg3; $i3++) {
														$id_item = $res3[$i3]['id'];
														$nome_item = $res3[$i3]['texto'];
														$valor_item = $res3[$i3]['valor'];
														$valor_itemF = @number_format($valor_item, 2, ',', '.');

														echo '<span style="font-size:10px">(' . $nome_item . ') R$ ' . $valor_itemF;
														if ($i3 < $total_reg3 - 1) {
															echo ' / ';
														}
														echo '</span>';
													}
												}
											} else {
												if ($valor > 0) {

													if($val_promocional != 0 and $promocao != 'Não'){
														echo 'R$ ' . $val_promocionalF;
														echo '  <del style="color: red">R$ ' . $valorF . ' </del';

														//$valor = $val_promocional;
													}else{
														echo 'R$ ' . $valorF;
													}

													
												}
											}
											?>

										</span>
									</div>

								</div>


								<div class="col-2" style="margin-top: 10px;" align="right">
									<img class="" src="sistema/painel/images/produtos/<?php echo $foto ?>" width="60px" height="60px">
								</div>



							</div>

						</li>
					</a>

			<?php }
			} ?>



		</ol>


	</div>
</div>

</body>

</html>
