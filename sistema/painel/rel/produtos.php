<!DOCTYPE html>
<html>

<head>

	<style>
		@import url('https://fonts.cdnfonts.com/css/tw-cen-mt-condensed');

		@page {
			margin: 145px 20px 25px 20px;
		}

		#header {
			position: fixed;
			left: 0px;
			top: -110px;
			bottom: 100px;
			right: 0px;
			height: 35px;
			text-align: center;
			padding-bottom: 100px;
		}

		#content {
			margin-top: 0px;
		}

		#footer {
			position: fixed;
			left: 0px;
			bottom: -60px;
			right: 0px;
			height: 80px;
		}

		#footer .page:after {
			content: counter(page, my-sec-counter);
		}

		body {
			font-family: 'Tw Cen MT', sans-serif;
		}

		.marca {
			position: fixed;
			left: 50;
			top: 100;
			width: 80%;
			opacity: 10%;
		}
	</style>

</head>

<body>
	<?php
	if ($marca_dagua == 'Sim' and $tipo_rel != 'HTML') { ?>
		<img class="marca" src="<?php echo $url_sistema ?>sistema/img/<?php echo $logo_sistema ?>">
	<?php } ?>


	<div id="header">

		<div style="border-style: solid; font-size: 10px; height: 55px;">
			<table style="width: 100%; border: 0px solid #ccc;">
				<tr>
					<td style="border: 1px; solid #000; width: 25%; text-align: left;">
						<img style="margin-top: 0px; margin-left: 7px;" id="imag"
							src="<?php echo $url_sistema ?>sistema/img/<?php echo $logo_sistema ?>" width="60%">
					</td>

					<td style="text-align: center; font-size: 10px; width: 40%;">

						<b><?php echo mb_strtoupper($nome_sistema) ?></b><br>
						<b>CNPJ: </b><?php echo mb_strtoupper($cnpj_sistema) ?><br>
						<b>INSTAGRAM:</b> <?php echo mb_strtoupper($instagram_sistema) ?><br>
						<?php echo mb_strtoupper($endereco_sistema) ?>

					</td>
					<td style="width: 35%; text-align: right; font-size: 9px;padding-right: 10px;">
						<b><big>RELATÓRIO DE PRODUTOS</big></b><br> <?php echo mb_strtoupper($data_hoje) ?>
					</td>
				</tr>
			</table>
		</div>

		<br>


		<table id="cabecalhotabela" style="border-bottom-style: solid; font-size: 8px; margin-bottom:0px; width: 100%; table-layout: fixed;">
			<thead>

				<tr id="cabeca" style="margin-left: 0px; background-color:#CCC">
					<td style="width:25%">PRODUTO</td>
					<td style="width:15%">CATEGORIA</td>
					<td style="width:10%">R$ VALOR COMPRA</td>
					<td style="width:10%">R$ VALOR VENDA</td>
					<td style="width:6%">ESTOQUE</td>
					<td style="width:10%">NÍVEL ESTOQUE</td>
					<td style="width:7%">ATIVO</td>

				</tr>
			</thead>
		</table>
	</div>

	<div id="footer" class="row">
		<hr style="margin-bottom: 0;">
		<table style="width:100%;">
			<tr style="width:100%;">
				<td style="width:60%; font-size: 10px; text-align: left;"><?php echo $nome_sistema ?> Telefone:
					<?php echo $telefone_sistema ?></td>
				<td style="width:40%; font-size: 10px; text-align: right;">
					<p class="page">Página </p>
				</td>
			</tr>
		</table>
	</div>

	<div id="content" style="margin-top: -10;">



		<table style="width: 100%; table-layout: fixed; font-size:8px; text-transform: uppercase;">
			<thead>
			<tbody>
				<?php
				$produtos_ativos = 0;
				$produtos_inativos = 0;
				$estoque_baixo = 0;

				$total_compra = 0;
				$total_venda = 0;

				$total_compraF = 0;
				$total_vendaF = 0;


				$query = $pdo->query("SELECT * FROM produtos where empresa = '$id_empresa' ORDER BY nome asc");
				$res = $query->fetchAll(PDO::FETCH_ASSOC);
				$total_reg = @count($res);
				if ($total_reg > 0) {

					for ($i = 0; $i < $total_reg; $i++) {
						foreach ($res[$i] as $key => $value) {
						}
						$id = $res[$i]['id'];
						$nome = $res[$i]['nome'];
						$descricao = $res[$i]['descricao'];
						$categoria = $res[$i]['categoria'];
						$valor_venda = $res[$i]['valor_venda'];
						$valor_compra = $res[$i]['valor_compra'];
						$estoque = $res[$i]['estoque'];
						$nivel_estoque = $res[$i]['nivel_estoque'];
						$foto = $res[$i]['foto'];
						$ativo = $res[$i]['ativo'];
						$tem_estoque = $res[$i]['tem_estoque'];

						$valor_vendaF = number_format($valor_venda, 2, ',', '.');
						$valor_compraF = number_format($valor_compra, 2, ',', '.');


						if ($estoque < $nivel_estoque and $ativo == 'Sim') {
							$classe_estoque = 'red';
							$estoque_baixo += 1;
						} else {
							$classe_estoque = '';
						}

						if ($ativo != 'Sim') {
							$classe_ativo = '#c2c2c2';
							$produtos_inativos += 1;
						} else {
							$classe_ativo = '';
							$produtos_ativos += 1;
						}


						$query2 = $pdo->query("SELECT * from categorias where id = '$categoria'");
						$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
						$nome_categoria = @$res2[0]['nome'];

						$total_compra += $valor_compra * $estoque;
						$total_venda += $valor_venda * $estoque;

						$total_compraF = number_format($total_compra, 2, ',', '.');
						$total_vendaF = number_format($total_venda, 2, ',', '.');


				?>


						<tr style="color:<?php echo $classe_ativo ?>">
							<td style="width:25%"><?php echo $nome ?></td>
							<td style="width:15%"><?php echo $nome_categoria ?></td>
							<td style="width:10%">R$ <?php echo $valor_compraF ?></td>
							<td style="width:10%">R$ <?php echo $valor_vendaF ?></td>
							<td style="width:6%; color:<?php echo $classe_estoque ?>"><?php echo $estoque ?></td>
							<td style="width:10%;"><?php echo $nivel_estoque ?></td>

							<td style="width:7%;"><?php echo $ativo ?> </td>

						</tr>

				<?php }
				} ?>
			</tbody>

			</thead>
		</table>


	</div>
	<hr>
	<table>
		<thead>
		<tbody>
			<tr>
				<td style="font-size: 10px; width:180px; text-align: right;"><b>Estoque Baixo:<span style="color:red">
							<?php echo $estoque_baixo ?></span> </td>

				<td style="font-size: 10px; width:180px; text-align: right;"><b>Produtos Ativos:<span style="color:green"><?php echo $produtos_ativos ?></span>
				</td>

				<td style="font-size: 10px; width:180px; text-align: right;"><b>Produtos Inativos:
						<span style="color: #FF9E00"><?php echo $produtos_inativos ?></span></td>

				<td style="font-size: 10px; width:180px; text-align: right;"><b>Total de Produtos: <?php echo $total_reg ?></td>

			</tr>
		</tbody>
		</thead>
	</table>


	<table>
		<thead>
		<tbody>
			<tr>
				<td style="font-size: 10px; width:180px; text-align: right;"> </td>

				<td style="font-size: 10px; width:180px; text-align: right;"></td>

				<td style="font-size: 10px; width:180px; text-align: right;"><b>Total Custo:
						<span style="color: red">R$ <?php echo $total_compraF ?></span></td>

				<td style="font-size: 10px; width:180px; text-align: right;"><b>Total Venda: <span style="color:green">R$ <?php echo $total_vendaF ?></span></td>

			</tr>
		</tbody>
		</thead>
	</table>

</body>

</html>