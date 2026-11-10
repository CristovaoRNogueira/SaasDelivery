<?php
require_once("../../../conexao.php");
$id = $_POST['id'];
?>

<style type="text/css">
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
		font-size: <?php echo $fonte_comprovante ?>px;



	}

	.th {
		font-weight: inherit;
		/*Espaçamento entre as uma linha para outra*/
		padding: 5px;
		text-align: center;
		/*largura dos tracinhos entre as linhas*/
		border-bottom: 1px dashed #000000;
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



</style>


<?php
//BUSCAR AS INFORMAÇÕES DO PEDIDO
$query = $pdo->query("SELECT * from vendas where id = '$id' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);

$id = $res[0]['id'];
$cliente = $res[0]['cliente'];
$valor = $res[0]['valor'];
$total_pago = $res[0]['total_pago'];
$valor_recebido = (float) ($res[0]['valor_recebido'] ?? 0);
$saldo_pendente = max(0, (float) $res[0]['valor'] - $valor_recebido);
$troco = $res[0]['troco'];
$data = $res[0]['data'];
$hora = $res[0]['hora'];
$status = $res[0]['status'];
$pago = $res[0]['pago'];
$obs = $res[0]['obs'];
$taxa_entrega = $res[0]['taxa_entrega'];
$tipo_pgto = $res[0]['tipo_pgto'];
$descricao_pagamento = htmlspecialchars($tipo_pgto, ENT_QUOTES, 'UTF-8');
$query_pagamentos = $pdo->prepare("SELECT forma_pgto, valor FROM vendas_pagamentos WHERE venda = :venda AND empresa = :empresa ORDER BY id");
$query_pagamentos->execute([':venda' => $id, ':empresa' => (int) ($res[0]['empresa'] ?? $id_empresa)]);
$pagamentos_pedido = $query_pagamentos->fetchAll(PDO::FETCH_ASSOC);
if (count($pagamentos_pedido) > 0) {
	$linhas_pagamento = [];
	foreach ($pagamentos_pedido as $pagamento_pedido) {
		$linhas_pagamento[] = htmlspecialchars($pagamento_pedido['forma_pgto'], ENT_QUOTES, 'UTF-8') . ': R$ ' . number_format($pagamento_pedido['valor'], 2, ',', '.');
	}
	$descricao_pagamento = implode('<br>', $linhas_pagamento);
}
$usuario_baixa = $res[0]['usuario_baixa'];
$entrega = $res[0]['entrega'];
$cupom = $res[0]['cupom'];
$pedido = $res[0]['pedido'];
$taxa_extra_pedido = $res[0]['taxa_extra_pedido'];
$descricao_taxa_extra = trim((string) ($titulo_taxa ?? ''));
if ($descricao_taxa_extra === '') {
	$descricao_taxa_extra = 'Taxa de Embalagem';
}

$cupomF = number_format($cupom, 2, ',', '.');
$valorF = number_format($valor, 2, ',', '.');
$total_pagoF = number_format($total_pago, 2, ',', '.');
$valor_recebidoF = number_format($valor_recebido, 2, ',', '.');
$saldo_pendenteF = number_format($saldo_pendente, 2, ',', '.');
$trocoF = number_format($troco, 2, ',', '.');
$taxa_entregaF = number_format($taxa_entrega, 2, ',', '.');
$taxa_extra_pedidoF = number_format($taxa_extra_pedido, 2, ',', '.');
$dataF = implode('/', array_reverse(explode('-', $data)));
//$horaF = date("H:i", strtotime($hora));	

$valor_dos_itens = $valor - $taxa_entrega + $cupom - $taxa_extra_pedido;
$valor_dos_itensF = number_format($valor_dos_itens, 2, ',', '.');

$hora_pedido = date('H:i', strtotime("+$previsao_entrega minutes", strtotime($hora)));

$query2 = $pdo->query("SELECT * FROM clientes where id = '$cliente'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$nome_cliente = @$res2[0]['nome'];
$telefone_cliente = @$res2[0]['telefone'];
$rua_cliente = @$res2[0]['endereco'];
$numero_cliente = @$res2[0]['numero'];
$complemento_cliente = @$res2[0]['complemento'];
$referencia_cliente = @$res2[0]['referencia'];
$bairro_cliente = @$res2[0]['bairro'];
$cidade_cliente = @$res2[0]['cidade'];

if ($entrega == 'Retirar') {
	$entrega = 'Retirar no Local';
}

if ($entrega == 'Consumir Local') {
	$entrega = 'Consumir no Local';
}

$nome_produto2 = '';
$res = $pdo->query("SELECT * from carrinho where pedido = '$id' order by id asc");
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
		} else {
			$nome_produto = $nome_produto_tab;
			$foto_produto = "";
			$unidade = "";
		}


		$query3 = $pdo->query("SELECT * FROM unidade_medida where id = '$unidade'");
			$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
			if(@count($res3) > 0){
				$nome_unidade = $res3[0]['nome'];
			}else{
				$nome_unidade = 'Sem Unidade';
			}


		$ocultar_quantidades = '';
			$sigla_unidade = '';
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

			//tratamento separa string
			$qt = explode(".", $quantidade);
			if($qt[1] > 0){
				$quantidadeF = $quantidade;		
			}else{
				$quantidadeF = $qt[0];
			}


	$tabela_ad = 'adicionais';
	if ($sabores > 0) {
		$nome_produto = $nome_produto_tab;
		$tabela_ad = 'adicionais_cat';
	}



	$query8 = $pdo->query("SELECT * FROM bordas where id = '$borda'");
	$res8 = $query8->fetchAll(PDO::FETCH_ASSOC);
	$total_reg8 = @count($res8);
	if ($total_reg8 > 0) {
		$nome_borda = $res8[0]['nome'];
	} else {
		$nome_borda = '';
	}

	$total_item_final = $total_item * $quantidade;

	echo <<<HTML

	<div class="row itens">
		<div align="left" class="col-md-9"> <span class="">{$quantidadeF} {$sigla_unidade} </span> - {$nome_produto} {$sigla_variacao} {$sigla_grade}
	</div>		

	<div align="right" class="col-md-3">
		R$ 
HTML;
	$total_item_finalF = number_format($total_item_final, 2, ',', '.');
	// $total = number_format( $cp1 , 2, ',', '.');
	echo $total_item_finalF;
	echo <<<HTML
	</div>
HTML;

	if ($sabores > 1) {
		echo '<span style="margin-left: 15px"><small><b>Atenção: </b>' . $sabores . ' Sabores</small></span><br>';
	}

	if ($total_reg8 > 0) {
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
		echo <<<HTML

		<div align="left" style="margin-left: 15px">
			<small><b>{$texto_adicional} : </b>
HTML;
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
		echo <<<HTML
			</small>
		</div>
HTML;
	}





	$query2 = $pdo->query("SELECT * FROM temp where carrinho = '$id_carrinho' and tabela = 'guarnicoes'");
	$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
	$total_reg2 = @count($res2);
	if ($total_reg2 > 0) {
		if ($total_reg2 > 1) {
			$texto_adicional = '(' . $total_reg2 . ') Guarnições';
		} else {
			$texto_adicional = '(' . $total_reg2 . ') Guarnição';
		}
		echo <<<HTML

		<div align="left" style="margin-left: 15px">
			<small><b>{$texto_adicional} : </b>
HTML;
		for ($i2 = 0; $i2 < $total_reg2; $i2++) {
			foreach ($res2[$i2] as $key => $value) {
			}
			$id_temp = $res2[$i2]['id'];
			$id_item = $res2[$i2]['id_item'];

			$query3 = $pdo->query("SELECT * FROM guarnicoes where id = '$id_item'");
			$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
			$total_reg3 = @count($res3);
			$nome_adc = $res3[0]['nome'];
			echo $nome_adc;
			if ($i2 < ($total_reg2 - 1)) {
				echo ', ';
			}
		}
		echo <<<HTML
			</small>
		</div>
HTML;
	}



	$query2 = $pdo->query("SELECT * FROM temp where carrinho = '$id_carrinho' and tabela = 'ingredientes'");
	$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
	$total_reg2 = @count($res2);
	if ($total_reg2 > 0) {
		if ($total_reg2 > 1) {
			$texto_adicional = '(' . $total_reg2 . ') Retirar Ingredientes';
		} else {
			$texto_adicional = '(' . $total_reg2 . ') Retirar Ingrediente';
		}
		echo <<<HTML
		<div align="left" style="margin-left: 15px">
			<small><b>{$texto_adicional} : </b>
HTML;
		for ($i2 = 0; $i2 < $total_reg2; $i2++) {
			foreach ($res2[$i2] as $key => $value) {
			}
			$id_temp = $res2[$i2]['id'];
			$id_item = $res2[$i2]['id_item'];

			$query3 = $pdo->query("SELECT * FROM ingredientes where id = '$id_item'");
			$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
			$total_reg3 = @count($res3);
			$nome_adc = $res3[0]['nome'];
			echo $nome_adc;
			if ($i2 < ($total_reg2 - 1)) {
				echo ', ';
			}
		}
		echo <<<HTML
			</small>
		</div>
HTML;
	}




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

				echo '<small><b>' . $nome_da_grade . ' </b></small>';;

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
					echo '<small><i>' . $quant_item . $nome_item . '</i></small>';
				}

				echo '<br>';
			}
		}
	}

	if ($obs_item != "") {
		echo <<<HTML
		<div align="left" style="margin-left: 15px">
			<small><b>Observação : </b>
				{$obs_item}
			</small>		
		</div>
HTML;
	}
	echo <<<HTML
</div>
HTML;
}
echo <<<HTML
<div class="th" style="margin-bottom: 7px"></div>
HTML;
if ($entrega == "Delivery") {
	echo <<<HTML
	<div class="row valores">			
		<div class="col-md-6">Total</div>
		<div class="col-md-6" align="right">R$ {$valor_dos_itensF}</div>
	</div>
HTML;
}

if ($entrega == "Delivery") {
	echo <<<HTML
	<div class="row valores">			
		<div class="col-md-6">Frete</div>
		<div class="col-md-6 text-verde" align="right"> R$ {$taxa_entregaF}</div>	
	</div>			
HTML;
}


if ($taxa_extra_pedido > 0) {
	echo <<<HTML
	<div class="row valores">			
		<div class="col-md-6">{$descricao_taxa_extra}</div>
		<div class="col-md-6 text-verde" align="right"> R$ {$taxa_extra_pedidoF}</div>	
	</div>			
HTML;
}

if ($cupom > 0) {
	echo <<<HTML
	<div class="row valores">			
		<div class="col-md-6">Cupom Desconto</div>
		<div class="col-md-6 text-danger" align="right"> R$ {$cupomF}</div>	
	</div>			
HTML;
}

echo <<<HTML
<div class="row valores">			
	<div class="col-md-6">SubTotal</div>
	<div class="col-md-6" align="right">R$ <b>{$valorF}</b></div>	
</div>	


</tr>

HTML;
if ($valor_recebido > 0 && $saldo_pendente > 0) {
	echo <<<HTML
	<div class="row valores">			
		<div class="col-md-6">Valor Recebido</div>
		<div class="col-md-6" align="right">R$ {$valor_recebidoF}</div>	
	</div>			
HTML;
}

if ($saldo_pendente > 0) {
	echo <<<HTML
	<div class="row valores" style="font-weight: bold;">
		<div class="col-md-6">Acréscimo Pendente</div>
		<div class="col-md-6" align="right">R$ {$saldo_pendenteF}</div>
	</div>
HTML;
}

if ($troco > 0) {
	echo <<<HTML
	<div class="row valores">			
		<div class="col-md-6">Troco</div>
		<div class="col-md-6" align="right">R$ {$trocoF}</div>	
	</div>			
HTML;
}
echo <<<HTML
<div class="th" style="margin-bottom: 7px"></div>

<div class="row valores">			
	<div class="col-md-6">Forma de Pagamento</div>
	<div class="col-md-6" align="right">{$descricao_pagamento}</div>
</div>	

<div class="row valores">			
	<div class="col-md-6">Forma de Entrega</div>
	<div class="col-md-6" align="right">{$entrega}</div>	
</div>


<div class="row valores">			
	<div class="col-md-6">Está Pago</div>
	<div class="col-md-6" align="right"><b>{$pago}</b></div>	
</div>

<div class="th" style="margin-bottom: 10px"></div>

HTML;
if ($entrega == "Delivery") {
	echo <<<HTML
	<div class="valores" align="center" style="font-weight: normal">
		<b>Endereço	para Entrega</b>		
			<br>
			{$rua_cliente} {$numero_cliente} {$complemento_cliente}
			{$bairro_cliente} {$cidade_cliente}
			<br>
			Ponto de Referencia: <b>{$referencia_cliente}</b>
		</div>	
<div class="th" style="margin-bottom: 10px"></div>
HTML;
}

if ($obs != "") {
	echo <<<HTML
	<div class="valores" align="center">
		<b>Observação do Pedido</b>		
			<br>			
			{$obs}
		</div>	
<div class="th" style="margin-bottom: 10px"></div>
HTML;
}

?>
