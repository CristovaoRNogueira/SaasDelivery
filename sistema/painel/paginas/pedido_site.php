<?php

if (@$pedido_site == 'ocultar') {
	echo "<script>window.location='../index.php'</script>";
	exit();
}

$query2 = $pdo->query("SELECT * FROM usuarios where id = '$id_usuario'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
if (@count($res2) > 0) {
	$nome_usuario = $res2[0]['nivel'];
}



$img = 'aberto.png';

if ($status_estabelecimento == "Fechado") {
	$img = 'fechado.png';
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
	$img = 'fechado.png';
}

$hora_atual = date('H:i:s');
//verificar se o delivery está aberto dentro da hora prevista
if (strtotime($hora_atual) > strtotime($horario_abertura) and strtotime($hora_atual) < strtotime($horario_fechamento)) {
} else {
	if (strtotime($hora_atual) > strtotime($horario_abertura) and strtotime($hora_atual) > strtotime($horario_fechamento)) {
	} else {
		$img = 'fechado.png';
	}
}

?>

<style type="text/css">
	.cards {
		margin-top: 25px;
		justify-content: center;

	}

	.card {
		margin: 10px;
		padding: 20px;
		height: 120px;
		border-radius: 10px;
		box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.25);
		transition: all 0.2s;
		width: 100%;
		background: #fafafa;
	}


	.card:hover {
		box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.4);
		transform: scale(1.01);
	}


	.card-title {
		grid-row: 2/4;
		font-weight: 300;
		color: #fff;
		text-align: center;
		position: relative;
		top: 50%;
		transform: translateY(-50%);
	}



	.link-card {
		text-decoration: none;
	}



	.carrinho {
		margin-right: 10px;
	}





	.badge2 {
		position: absolute;
		display: inline-block;
		min-width: 10px;
		padding: 4px 9px;
		font-size: 14px;
		font-weight: 700;
		text-transform: uppercase;
		color: #f6f2f8;
		line-height: 1;
		vertical-align: middle;
		white-space: nowrap;
		text-align: center;
		background-color: #242424;
		border-radius: 5px;
		left: 20px;
		top: 1px;
		z-index: 200;
		margin-top: -8px;

	}



	.ocultar {
		display: none;
	}
</style>




<div class="row cards" style="margin-bottom: 60px; ">

	<?php
	$query = $pdo->query("SELECT * FROM categorias where ativo = 'Sim' and empresa = '$id_empresa' ");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if ($total_reg > 0) {
		for ($i = 0; $i < $total_reg; $i++) {
			foreach ($res[$i] as $key => $value) {
			}
			$cor = $res[$i]['cor'];
			$foto = $res[$i]['foto'];
			$nome = $res[$i]['nome'];
			$url = $res[$i]['url'];
			$id = $res[$i]['id'];
			$mais_sabores = $res[$i]['mais_sabores'];

			$query2 = $pdo->query("SELECT * FROM produtos where categoria = '$id' and ativo = 'Sim' and empresa = '$id_empresa'");
			$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
			$tem_produto = @count($res2);
			$mostrar = 'ocultar';
			if ($tem_produto > 0) {
				for ($i2 = 0; $i2 < $tem_produto; $i2++) {
					foreach ($res2[$i2] as $key => $value) {
					}

					$estoque = $res2[$i2]['estoque'];
					$tem_estoque = $res2[$i2]['tem_estoque'];

					if (($tem_estoque == 'Sim' and $estoque > 0) or ($tem_estoque == 'Não')) {
						$mostrar = '';
					}
				}
			} else {
				$mostrar = 'ocultar';
			}

			if ($mais_sabores == 'Sim') {
				$categoria = 'categoria-sabores-';
			} else {
				$categoria = 'categoria-';
			}

	?>

			<div class="col-md-3 <?php echo $mostrar ?>">

				<form method="POST" action="" target="_blank">
					<input type="hidden" name="id_mesa" id="">
					<input type="hidden" name="id_abertura_mesa" id="">
					<input type="hidden" name="pedido_balcao" value='Sim'>
					<a class="link-card" href="<?php echo $url_sistema ?><?php echo $categoria  ?><?php echo $url ?>" target="_blank">
						<div class="card" style="background-image: url('images/categorias/<?php echo $foto ?>')!important; background-size: cover !important; border:none !important">
							<div class="badge2"><?php echo $nome ?></div>
						</div>
					</a>
				</form>


			</div>


	<?php }
	} ?>










</div>
