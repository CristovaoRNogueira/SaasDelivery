<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
$tabela = 'cargos';
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
$nivel_usuario = $_SESSION['nivel'];
$id_usuario = $_SESSION['id'];



$pagina = 'caixas';

$data_atual = date('Y-m-d');

//trazer valor fechamento ultimo caixa
$query = $pdo->query("SELECT * from $pagina where empresa = '$id_empresa' order by id desc limit 1");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$ultimo_valor_fechado = @$res[0]['valor_fechamento'];

if ($ultimo_valor_fechado == "") {
	$ultimo_valor_fechado = 0;
}

$dataInicial = @$_POST['p1'];
$dataFinal = @$_POST['p2'];
$operador = @$_POST['p3'];

if ($dataInicial == "") {
	$dataInicial = $data_atual;
}

if ($dataFinal == "") {
	$dataFinal = $data_atual;
}


if ($nivel_usuario != 'Administrador' and $nivel_usuario != 'Gerente') {
	$operador = $id_usuario;
}

if ($operador == "") {
	$query = $pdo->query("SELECT * from $pagina WHERE ((data_abertura >= '$dataInicial' and data_abertura <= '$dataFinal') or data_fechamento is null) and empresa = '$id_empresa' order by id desc ");
} else {
	$query = $pdo->query("SELECT * from $pagina WHERE ((data_abertura >= '$dataInicial' and data_abertura <= '$dataFinal') or data_fechamento is null) and operador = '$operador' and empresa = '$id_empresa' order by id desc ");
}


echo <<<HTML
<small>
HTML;


$res = $query->fetchAll(PDO::FETCH_ASSOC);

$total_reg = @count($res);

if ($total_reg > 0) {

	echo <<<HTML
	<table class="table table-hover table-bordered text-nowrap border-bottom dt-responsive" id="tabela">
		<thead> 
			<tr> 				

				<th>Operador</th>				
				<th>Abertura</th>
				<th>Fechamento</th>
				<th>Valor Abertura</th> 
				<th>Valor Fechamento</th> 
				<th>Sangrias</th> 
				<th>Quebra</th>	
				<th>Aberto Por</th> 
				<th>Fechado Por</th> 	
				<th>Ações</th>

			</tr> 

		</thead> 

		<tbody> 

HTML;

	for ($i = 0; $i < $total_reg; $i++) {

		foreach ($res[$i] as $key => $value) {
		}

		$id = $res[$i]['id'];
		$operador = $res[$i]['operador'];
		$data_abertura = $res[$i]['data_abertura'];
		$hora_abertura = $res[$i]['hora_abertura'] ?? null;
		$data_fechamento = $res[$i]['data_fechamento'];
		$hora_fechamento = $res[$i]['hora_fechamento'] ?? null;
		$valor_abertura = $res[$i]['valor_abertura'];
		$valor_fechamento = $res[$i]['valor_fechamento'];
		$quebra = $res[$i]['quebra'];
		$usuario_abertura = $res[$i]['usuario_abertura'];
		$usuario_fechamento = $res[$i]['usuario_fechamento'];
		$obs = $res[$i]['obs'];

		$data_aberturaF = @implode('/', array_reverse(@explode('-', $data_abertura)));
		$data_fechamentoF = $data_fechamento ? @implode('/', array_reverse(@explode('-', $data_fechamento))) : '—';
		$hora_aberturaF = $hora_abertura ? substr($hora_abertura, 0, 5) : '—';
		$hora_fechamentoF = $hora_fechamento ? substr($hora_fechamento, 0, 5) : '—';

		$valor_aberturaF = @number_format($valor_abertura, 2, ',', '.');
		$valor_fechamentoF = @number_format($valor_fechamento, 2, ',', '.');
		$quebraF = @number_format($quebra, 2, ',', '.');






		$query2 = $pdo->query("SELECT * FROM usuarios where id = '$operador'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);

		if (@count($res2) > 0) {

			$nome_operador = $res2[0]['nome'];
		} else {

			$nome_operador = '';
		}



		$query2 = $pdo->query("SELECT * FROM usuarios where id = '$usuario_abertura'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		if (@count($res2) > 0) {
			$nome_usu_abertura = $res2[0]['nome'];
		} else {
			$nome_usu_abertura = '';
		}




		$query2 = $pdo->query("SELECT * FROM usuarios where id = '$usuario_fechamento'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		if (@count($res2) > 0) {
			$nome_usu_fechamento = $res2[0]['nome'];
		} else {
			$nome_usu_fechamento = '';
		}



		if ($valor_fechamento == '') {
			$classe_aberto = 'text-danger';
			$ocultar = '';
		} else {
			$classe_aberto = 'verde';
			$ocultar = 'ocultar';
		}

		if ($quebra != 0) {
			$classe_quebra = 'text-danger';
		} else {
			$classe_quebra = 'text-verde';
		}


		if ($data_fechamento == "") {
			$data_fechamento_consulta = $data_atual;
		} else {
			$data_fechamento_consulta = $data_fechamento;
		}


		//pegar a forma de pagamento dinheiro
		$query2 = $pdo->query("SELECT * FROM formas_pgto where nome LIKE '%Dinheiro%' and empresa = '$id_empresa'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$pgto_dinheiro = $res2[0]['id'];


		//buscar total movimentado pelo caixa
		//totalizar recebimentos
		$total_recebido = 0;
		$query2 = $pdo->query("SELECT * FROM receber where usuario_pgto = '$operador' and data_pgto >= '$data_abertura' and data_pgto <= '$data_fechamento_consulta' and caixa = '$id' and forma_pgto = '$pgto_dinheiro' and (arquivado IS NULL or arquivado != 'Sim')");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		if (@count($res2) > 0) {
			for ($i2 = 0; $i2 < @count($res2); $i2++) {
				$valor_rec = $res2[$i2]['valor'];
				$total_recebido += $valor_rec;
			}
		}


		//totalizar saidas
		$total_saidas = 0;
		$query2 = $pdo->query("SELECT * FROM pagar where usuario_pgto = '$operador' and data_pgto >= '$data_abertura' and data_pgto <= '$data_fechamento_consulta' and caixa = '$id' and forma_pgto = '$pgto_dinheiro'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		if (@count($res2) > 0) {
			for ($i2 = 0; $i2 < @count($res2); $i2++) {
				$valor_sai = $res2[$i2]['valor'];
				$total_saidas += $valor_sai;
			}
		}


		//totalizar sangrias
		$total_sangrias = 0;
		$query2 = $pdo->query("SELECT * FROM sangrias where caixa = '$id'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		if (@count($res2) > 0) {
			for ($i2 = 0; $i2 < @count($res2); $i2++) {
				$valor_s = $res2[$i2]['valor'];
				$total_sangrias += $valor_s;
			}
		}
		$total_sangriasF = @number_format($total_sangrias, 2, ',', '.');


		$total_caixa = $total_recebido - $total_saidas + $valor_abertura - $total_sangrias;
		if ($data_fechamento == '') {
			$valor_fechamentoF = number_format($total_caixa, 2, ',', '.');
			$titulo_valor_fechamento = 'Valor previsto para fechamento';
		} else {
			$titulo_valor_fechamento = 'Valor informado no fechamento';
		}


		echo <<<HTML
			<tr> 
				<td>

				<i class="fa fa-square {$classe_aberto} mr-1"></i> {$nome_operador} </td> 
				
				<td>{$data_aberturaF} <small class="text-muted">{$hora_aberturaF}</small></td>
				<td>{$data_fechamentoF} <small class="text-muted">{$hora_fechamentoF}</small></td>
				<td>R$ {$valor_aberturaF}</td>
				<td title="{$titulo_valor_fechamento}">R$ {$valor_fechamentoF}</td>
				<td>R$ {$total_sangriasF}</td>
				<td class="{$classe_quebra}">R$ {$quebraF}</td>
				<td>{$nome_usu_abertura}</td>
				<td>{$nome_usu_fechamento}</td>
				<td>

		<big><a class="{$ocultar} btn btn-info-light btn-sm" href="#" onclick="editar('{$id}', '{$operador}', '{$data_abertura}','{$valor_abertura}','{$obs}')" title="Editar Dados"><i class="fa fa-edit "></i></a></big>


		<big><a href="#" class="btn btn-danger-light btn-sm {$ocultar}" onclick="excluirBusca('{$id}')" title="Excluir"><i class="fa fa-trash-can text-danger"></i></a></big>


		<big><a class="{$ocultar} btn btn-success-light btn-sm" href="#" onclick="fechamento('{$id}', '{$operador}', '{$nome_operador}', '{$valor_abertura}', '{$total_caixa}', '{$total_recebido}', '{$total_saidas}', '{$total_sangrias}')" title="Fechar Caixa"><i class="fa fa-check-square"></i></a></big>


		<big><a class="{$ocultar} btn btn-success-light btn-sm" href="#" onclick="sangria('{$id}')" title="Sangria"><i class="fa fa-money  "></i></a></big>



		<form   method="POST" action="rel/detalhamento_caixa_class.php" target="_blank" style="display:inline-block">
			<input type="hidden" name="id" value="{$id}">
			<button class="btn btn-danger-light btn-sm" title="Detalhamento do Caixa"><i class="fa fa-file-pdf-o " ></i></button>
		</form>


				</td>  
			</tr> 

HTML;
	}
	echo <<<HTML
		</tbody> 
		<small><div align="center" id="mensagem-excluir"></div></small>
	</table>

</small>

HTML;
} else {

	echo 'Não possui nenhuma conta para esta data!';
}


?>



<script type="text/javascript">
	$(document).ready(function() {
		$('#tabela').DataTable({
			"ordering": false,
			"stateSave": true,
		});
		$('#tabela_filter label input').focus();
	});



	function editar(id, operador, data_abertura, valor_abertura, obs) {



		$('#id').val(id);
		$('#operador_original').val(operador);
		$('#operador').val(operador).change();
		$('#valor_abertura').val(valor_abertura);
		$('#data_abertura').val(data_abertura);
		$('#obs').val(obs);

		$('#tituloModal').text('Editar Registro');
		$('#modalForm').modal('show');
		$('#mensagem').text('');
		$('#titulo_inserir').text('Editar Registro');


	}




	function fechamento(id, operador_id, operador, valor_abertura, total_caixa, total_recebido, total_saidas, total_sangrias) {
		

		$('#id_fechar').val(id);
		$('#operador_fechamento_id').val(operador_id);
		$('#nome_operador').val(operador);
		$('#valor_abertura_fechar').val(valor_abertura);
		$('#total_caixa_fechar').val(formatarValorInputCaixa(total_caixa));
		$('#resumo_abertura').text(formatarMoedaCaixa(valor_abertura));
		$('#resumo_entradas').text(formatarMoedaCaixa(total_recebido));
		$('#resumo_saidas').text(formatarMoedaCaixa(total_saidas));
		$('#resumo_sangrias').text(formatarMoedaCaixa(total_sangrias));
		$('#resumo_esperado').text(formatarMoedaCaixa(total_caixa));
		$('#valor_esperado_fechar').val(total_caixa);
		atualizarDiferencaCaixa();
		$('#total_caixa_fechar').off('input.resumoCaixa').on('input.resumoCaixa', function() {
			aplicarMascaraCaixa(this);
			atualizarDiferencaCaixa();
		});

		$('#tituloModalFechar').text('Fechar Caixa');
		$('#modalFechar').modal('show');
		$('#mensagem_fechar').text('');

	}

	function numeroCaixa(valor) {
		valor = String(valor ?? '').trim();
		if (valor.indexOf(',') !== -1) {
			valor = valor.replace(/\./g, '').replace(',', '.');
		}
		valor = parseFloat(valor);
		return Number.isFinite(valor) ? valor : 0;
	}

	function formatarMoedaCaixa(valor) {
		return numeroCaixa(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
	}

	function formatarValorInputCaixa(valor) {
		return numeroCaixa(valor).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
	}

	function aplicarMascaraCaixa(input) {
		const digitos = input.value.replace(/\D/g, '');
		input.value = digitos === '' ? '' : (parseInt(digitos, 10) / 100).toLocaleString('pt-BR', {
			minimumFractionDigits: 2,
			maximumFractionDigits: 2
		});
	}

	function atualizarDiferencaCaixa() {
		const esperado = numeroCaixa($('#valor_esperado_fechar').val());
		const informado = numeroCaixa($('#total_caixa_fechar').val());
		const diferenca = informado - esperado;
		const $status = $('#resumo_diferenca');

		if (Math.abs(diferenca) < 0.005) {
			$status.removeClass('text-danger text-primary').addClass('text-success').text('Sem diferença');
		} else if (diferenca < 0) {
			$status.removeClass('text-success text-primary').addClass('text-danger').text('Quebra de ' + formatarMoedaCaixa(Math.abs(diferenca)));
		} else {
			$status.removeClass('text-success text-danger').addClass('text-primary').text('Sobra de ' + formatarMoedaCaixa(diferenca));
		}
	}



	function sangria(id) {

		$('#id_sangria').val(id);
		//$('#nome_operador_sangria').val(operador);
		$('#tituloModalSangria').text('Efetuar Sangria');

		$('#modalSangria').modal('show');
		$('#mensagem_sangria').text('');


	}


	function limparCampos() {

		$('#id').val('');
		$('#operador_original').val('');
		//$('#operador').val('').change();		
		$('#valor_abertura').val('<?= $ultimo_valor_fechado ?>');
		$('#data_abertura').val('<?= $data_atual ?>');

		$('#obs').val('');
		$('#ids').val('');
		$('#div_botoes').hide();
		setTimeout(validarOperadorCaixa, 0);

	}


</script>
