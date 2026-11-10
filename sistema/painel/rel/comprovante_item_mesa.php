<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
include('../../conexao.php');
require_once("../buscar_config.php");
include('data_formatada.php');

$id = $_GET['id'];

//BUSCAR AS INFORMAÇÕES DO PEDIDO
$query = $pdo->query("SELECT * from carrinho where id = '$id' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);

 	$id = $res[0]['id'];
    $total_item = $res[0]['total_item'];
    $produto = $res[0]['produto'];
    $quantidade = $res[0]['quantidade'];
    $obs = $res[0]['obs'];
    $item = $res[0]['item'];
    $variacao = $res[0]['variacao'];
    $nome_produto_tab = $res[0]['nome_produto'];
    $sabores = $res[0]['sabores'];
    $borda = $res[0]['borda'];
    $categoria = $res[0]['categoria'];
    $valor_unit = $res[0]['valor_unitario'];
    $status = $res[0]['status'];
    $hora = $res[0]['hora'];
    $ab_mesa = $res[0]['mesa'];
    $obs_item = $res[0]['obs'];
    $pedido = $res[0]['pedido'];


$horaF = date("H:i", strtotime($hora));

     $query2 = $pdo->query("SELECT * FROM abertura_mesa where id = '$ab_mesa'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    $total_reg2 = @count($res2);
    if($total_reg2 > 0){
      $garcon = $res2[0]['garcon'];
      $mesa = $res2[0]['mesa'];
    }else{
      $garcon = 0;
      $mesa = 0;
    }

     $query2 = $pdo->query("SELECT * FROM vendas where id = '$pedido'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    $total_reg2 = @count($res2);
    if ($total_reg2 > 0) {
      $id_pedido = $res2[0]['pedido'];
    } else {
      $id_pedido = '';
    }


    $query2 = $pdo->query("SELECT * FROM mesas where id = '$mesa'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    if(@count($res2) > 0){
      $nome_mesa = 'Mesa '.@$res2[0]['nome'];
    }else{
      $nome_mesa = 'Pedido '.$id_pedido;
    }
    


    $query2 = $pdo->query("SELECT * FROM usuarios where id = '$garcon'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    $total_reg2 = @count($res2);
    if ($total_reg2 > 0) {
      $nome_garcon = '<b>Garçon: </b>'.$res2[0]['nome'];
    } else {
      $nome_garcon = '';
    }





    if ($status == 'Aguardando') {
      $classe_alerta = '#DC3545';
      $class_btn = 'btn-danger';
      $titulo_link = 'Mudar para Preparando!';
      $cor_icone_link = '#DC3545';
      $acao_link = 'Preparando';
    } else if ($status == 'Preparando') {
      $classe_alerta = '#0097ff';
      $class_btn = 'btn-info';
      $titulo_link = 'Mudar para Pronto!';
      $cor_icone_link = '#0097ff';
      $acao_link = 'Pronto';
    } else {
      $class_btn = 'btn-success';
      $classe_alerta = '#00dc49';
      $titulo_link = 'Mudar para Finalizado!';
      $cor_icone_link = '#00dc49';
      $acao_link = 'Finalizado';
    }



    if ($obs != "") {
      $classe_info = 'warning';
    } else {
      $classe_info = 'info';
    }


    $query2 = $pdo->query("SELECT * FROM variacoes where id = '$variacao'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    if (@count(@$res2) > 0) {
      $sigla_variacao = '(' . $res2[0]['sigla'] . ')';
    } else {
      $sigla_variacao = '';
    }



    $query2 = $pdo->query("SELECT * FROM temp where carrinho = '$id' and tabela = 'Variação' order by id asc limit 1");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    $id_do_item = @$res2[0]['id_item'];

    $query2 = $pdo->query("SELECT * FROM itens_grade where id = '$id_do_item'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    if (@$res2[0]['texto'] != "") {
      $sigla_grade = @$res2[0]['texto'];
    } else {
      $sigla_grade = '';
    }

    $query2 = $pdo->query("SELECT * FROM produtos where id = '$produto'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    if (@count(@$res2) > 0) {
      $nome_produto = $res2[0]['nome'];
      $foto_produto = $res2[0]['foto'];
    } else {
      $nome_produto = $nome_produto_tab;
      $foto_produto = "";
    }


    $tabela_ad = 'adicionais';
    if ($sabores > 0) {
      $nome_produto = $nome_produto_tab;
      $tabela_ad = 'adicionais_cat';
    }

    $total_reg8 = 0;
    $nome_borda = '';
    if ($borda != "") {
      $query8 = $pdo->query("SELECT * FROM bordas where id = '$borda'");
      $res8 = $query8->fetchAll(PDO::FETCH_ASSOC);
      $total_reg8 = @count($res8);
      if ($total_reg8 > 0) {
        $nome_borda = $res8[0]['nome'];
      } else {
        $nome_borda = 'Sem Borda';
      }
    }


?>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<?php if (@$impressao_cozinha == 'Sim') { ?>
	<script type="text/javascript">
		$(document).ready(function() {
			window.print();
			window.close();
		});
	</script>
<?php } ?>

<!-- CSS only -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
	integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">

<style type="text/css">
	* {
		margin: 0px;

		/*Espaçamento da margem da esquerda e da Direita*/
		padding: 0px;
		background-color: #ffffff;


	}

	.text {
		&-center {
			text-align: center;
		}
	}

	.printer-ticket {
		display: table !important;
		width: 100%;

		/*largura do Campos que vai os textos*/
		max-width: 400px;
		font-weight: light;
		line-height: 1.3em;

		/*Espaçamento da margem da esquerda e da Direita*/
		padding: 0px;
		font-family: TimesNewRoman, Geneva, sans-serif;

		/*tamanho da Fonte do Texto*/
		font-size:
			<?php echo $fonte_comprovante ?> px;



	}

	.th {
		font-weight: inherit;
		/*Espaçamento entre as uma linha para outra*/
		padding: 5px;
		text-align: center;
		/*largura dos tracinhos entre as linhas*/
		border-bottom: 1px dashed #000000;
	}

	.itens {
		font-weight: inherit;
		/*Espaçamento entre as uma linha para outra*/
		padding: 5px;

	}

	.valores {
		font-weight: inherit;
		/*Espaçamento entre as uma linha para outra*/
		padding: 2px 5px;

	}


	.cor {
		color: #000000;
	}


	.title {
		font-size: 12px;
		text-transform: uppercase;
		font-weight: bold;
	}

	/*margem Superior entre as Linhas*/
	.margem-superior {
		padding-top: 5px;
	}


	}
</style>



<div class="printer-ticket">
	<div align="center">
		<td>
			<img style="margin: 12px; margin-left: 50px;" id="imag" src="<?php echo $url_sistema ?>sistema/img/<?php echo $logo_sistema ?>"
				width="120px">
		</td>
	</div>

	<div class="th">		
		<b></b> <?php echo $nome_mesa ?>
			
		</small>
	</div>



	<div class="th">
		<?php echo $nome_garcon ?>
		
		<br>
		<?php if($mesa > 0){ ?>
		Pedido: <b><?php echo $id ?></b> - <?php } ?>  Hora: <?php echo $hora ?>
		<br>
		Status: <b><?php echo $status ?></b> 
	</div>

	
	<?php

	$nome_produto2 = '';
	$res = $pdo->query("SELECT * from carrinho where id = '$id' order by id asc");
	$dados = $res->fetchAll(PDO::FETCH_ASSOC);
	$linhas = count($dados);

	$sub_tot;
	for ($i = 0; $i < count($dados); $i++) {
		foreach ($dados[$i] as $key => $value) {
		}
		$id_carrinho = $dados[$i]['id'];
		$id_produto = $dados[$i]['produto'];
		$quantidade = $dados[$i]['quantidade'];
		$total_item = $dados[$i]['total_item'];
		$obs_item = $dados[$i]['obs'];
		$item = $dados[$i]['item'];
		$variacao = $dados[$i]['variacao'];
		$nome_produto_tab = $dados[$i]['nome_produto'];
		$sabores = $dados[$i]['sabores'];
		$borda = $dados[$i]['borda'];
		$categoria = $dados[$i]['categoria'];

		
		//tratamento separa string
	$qt = explode(".", $quantidade);
	if($qt[1] > 0){
		$quantidadeF = $quantidade;		
	}else{
		$quantidadeF = $qt[0];
	}


		$query2 = $pdo->query("SELECT * FROM variacoes where id = '$variacao'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		if (@count(@$res2) > 0) {
			$sigla_variacao = '(' . $res2[0]['sigla'] . ')';
		} else {
			$sigla_variacao = '';
		}

		$query2 = $pdo->query("SELECT * FROM temp where carrinho = '$id_carrinho' and tabela = 'Variação' order by id asc limit 1");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$id_do_item = @$res2[0]['id_item'];

		$query2 = $pdo->query("SELECT * FROM itens_grade where id = '$id_do_item'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		if (@$res2[0]['texto'] != "") {
			$sigla_grade = @$res2[0]['texto'];
		} else {
			$sigla_grade = '';
		}

		$query2 = $pdo->query("SELECT * FROM produtos where id = '$id_produto'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		if (@count(@$res2) > 0) {
			$nome_produto = $res2[0]['nome'];
			$foto_produto = $res2[0]['foto'];
			$unidade = $res2[0]['unidade'];

			$ocultar_quantidades = '';
	$sigla_unidade = '';

	$query3 = $pdo->query("SELECT * FROM unidade_medida where id = '$unidade'");
	$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
	if(@count($res3) > 0){
		$nome_unidade = $res3[0]['nome'];
	}else{
		$nome_unidade = 'Sem Unidade';
	}

	
	if($nome_unidade == 'Quilogramas' or $nome_unidade == 'Quilo' or $nome_unidade == 'Quilograma' or $nome_unidade == 'KG'){
		$sigla_unidade = ' (KG)';
		$ocultar_quantidades = 'ocultar';
	}

	if($nome_unidade == 'Metros' or $nome_unidade == 'Metro' or $nome_unidade == 'M' or $nome_unidade == 'm'){
		$sigla_unidade = ' (m)';
		$ocultar_quantidades = 'ocultar';
	}

	if($nome_unidade == 'Litro' or $nome_unidade == 'Litros' or $nome_unidade == 'L'){
		$sigla_unidade = ' (L)';
		$ocultar_quantidades = 'ocultar';
	}

		} else {
			$nome_produto = $nome_produto_tab;
			$foto_produto = "";
		}


		$tabela_ad = 'adicionais';
		if ($sabores > 0) {
			$nome_produto = $nome_produto_tab;
			$tabela_ad = 'adicionais_cat';
		}


		$nome_borda = '';
		if ($borda != "") {
			$query8 = $pdo->query("SELECT * FROM bordas where id = '$borda'");
			$res8 = $query8->fetchAll(PDO::FETCH_ASSOC);
			$total_reg8 = @count($res8);
			if ($total_reg8 > 0) {
				$nome_borda = $res8[0]['nome'];
			} else {
				$nome_borda = 'Sem Borda';
			}
		}





	?>

		<div class="row itens">

			<div align="left" class="col-9"> <?php echo $quantidadeF ?> <?php echo $sigla_unidade ?> - <?php echo $nome_produto ?>
				<?php echo $sigla_variacao ?> <?php echo $sigla_grade ?>

			</div>


			<div align="right" class="col-3">
				R$ <?php
						$total_itemF = number_format($total_item, 2, ',', '.');
						// $total = number_format( $cp1 , 2, ',', '.');
						echo $total_itemF;
						?>
			</div>



			<?php

			if ($sabores > 1) {
				echo '<span style="margin-left: 15px"><small><b>Atenção: </b>' . $sabores . ' Sabores</small></span><br>';
			}

			if ($nome_borda != '') {
				echo '<span style="margin-left: 15px"><small><b>Borda: </b>' . $nome_borda . '</small></span><br>';
			}


			$query2 = $pdo->query("SELECT * FROM temp where carrinho = '$id_carrinho' and tabela = 'adicionais'");
			$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
			$total_reg2 = @count($res2);
			if ($total_reg2 > 0) {
				if ($total_reg2 > 1) {
					$texto_adicional = '(' . $total_reg2 . ') Tipos de Adicionais';
				} else {
					$texto_adicional = '(' . $total_reg2 . ') Tipo de Adicional';
				}
			?>

				<div align="left" style="margin-left: 15px">
					<small><b><?php echo $texto_adicional ?> : </b>
						<?php
						for ($i2 = 0; $i2 < $total_reg2; $i2++) {
							foreach ($res2[$i2] as $key => $value) {
							}
							$id_temp = $res2[$i2]['id'];
							$id_item = $res2[$i2]['id_item'];
							$quantidade_temp = $res2[$i2]['quantidade'];

							$query3 = $pdo->query("SELECT * FROM $tabela_ad where id = '$id_item'");
							$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
							$total_reg3 = @count($res3);
							$nome_adc = $res3[0]['nome'];
							echo '(' . $quantidade_temp . ') ' . $nome_adc;
							if ($i2 < ($total_reg2 - 1)) {
								echo ', ';
							}
						}
						?>
					</small>
				</div>

			<?php } ?>







			<?php

			//percorrer as grades do produto
			$query20 = $pdo->query("SELECT * FROM grades where produto = '$id_produto' and tipo_item != 'Variação'");
			$res20 = $query20->fetchAll(PDO::FETCH_ASSOC);
			$total_reg20 = @count($res20);
			if ($total_reg20 > 0) {
				for ($i20 = 0; $i20 < $total_reg20; $i20++) {
					$id_da_grade = $res20[$i20]['id'];
					$nome_da_grade = $res20[$i20]['nome_comprovante'];
					$tipo_item_grade = $res20[$i20]['tipo_item'];


					//buscar os itens selecionados pela grade
					$query2 = $pdo->query("SELECT * FROM temp where carrinho = '$id_carrinho' and grade = '$id_da_grade'");
					$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
					$total_reg2 = @count($res2);
					if ($total_reg2 > 0) {
						echo '<div>';
						echo '<b><small>' . $nome_da_grade . '</small></b>';;

						for ($i2 = 0; $i2 < $total_reg2; $i2++) {
							foreach ($res2[$i2] as $key => $value) {
							}
							$id_temp = $res2[$i2]['id'];
							$id_item = $res2[$i2]['id_item'];
							$tabela_item = $res2[$i2]['tabela'];
							$tipagem_item = $res2[$i2]['tipagem'];
							$grade_item = $res2[$i2]['grade'];

							if ($tipo_item_grade == 'Múltiplo') {
								$quant_item = '(' . $res2[$i2]['quantidade'] . ')';
							} else {
								$quant_item = '';
							}



							$query3 = $pdo->query("SELECT * FROM itens_grade where id = '$id_item'");
							$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
							$total_reg3 = @count($res3);
							$nome_item = $res3[0]['texto'];
							if ($i2 < ($total_reg2 - 1)) {
								$nome_item .= ', ';
							}
							echo ' <i><small>' . $quant_item . $nome_item . '</i></small>';
						}

						echo '<br>';
						echo '</div>';
					}
				}
			}
			?>


			<?php

			if ($obs_item != "") {
			?>
				<div align="left" style="margin-left: 15px">
					<small><b>Observação : </b>
						<?php echo $obs_item ?>
					</small>
				</div>
			<?php } ?>

		</div>


	<?php } ?>

	