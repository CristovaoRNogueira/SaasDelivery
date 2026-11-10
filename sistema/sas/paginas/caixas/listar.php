<?php
@session_start();
require_once("../../../conexao.php");
$nivel_usuario = $_SESSION['nivel'];
$id_usuario = $_SESSION['id'];



$pagina = 'caixas';

$data_atual = date('Y-m-d');

//trazer valor fechamento ultimo caixa
$query = $pdo->query("SELECT * from $pagina where empresa = 0 or empresa is null order by id desc limit 1");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$ultimo_valor_fechado = @$res[0]['valor_fechamento'];

if ($ultimo_valor_fechado == "") {
	$ultimo_valor_fechado = 0;
}

$ultimo_valor_fechadoF = @number_format($ultimo_valor_fechado, 2, ',', '.');

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
	$query = $pdo->query("SELECT * from $pagina WHERE data_abertura >= '$dataInicial' and data_abertura <= '$dataFinal' and empresa = 0 or empresa is null order by id desc ");
} else {
	$query = $pdo->query("SELECT * from $pagina WHERE data_abertura >= '$dataInicial' and data_abertura <= '$dataFinal' and operador = '$operador' and empresa = 0 or empresa is null order by id desc ");
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
				<th class="esc">Data Abertura</th>					
				<th class="esc">Valor Abertura</th> 
				<th class="esc">Valor Fechamento</th> 
				<th class="esc">Sangrias</th> 
				<th class="esc">Quebra</th>	
				<th class="esc">Aberto Por</th> 
				<th class="esc">Fechado Por</th> 	
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
		$data_fechamento = $res[$i]['data_fechamento'];
		$valor_abertura = $res[$i]['valor_abertura'];
		$valor_fechamento = $res[$i]['valor_fechamento'];
		$quebra = $res[$i]['quebra'];
		$usuario_abertura = $res[$i]['usuario_abertura'];
		$usuario_fechamento = $res[$i]['usuario_fechamento'];
		$obs = $res[$i]['obs'];
		$data_aberturaF = @implode('/', array_reverse(@explode('-', $data_abertura)));
		$data_fechamentoF = @implode('/', array_reverse(@explode('-', $data_fechamento)));
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
			$classe_quebra = 'verde';
		}


		if ($data_fechamento == "") {
			$data_fechamento_consulta = $data_atual;
		} else {
			$data_fechamento_consulta = $data_fechamento;
		}

		//pegar a forma de pagamento dinheiro
		$query2 = $pdo->query("SELECT * FROM formas_pgto where nome LIKE '%Dinheiro%' and (empresa = 0 or empresa is null)");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$pgto_dinheiro = $res2[0]['id'];

		//buscar total movimentado pelo caixa
		//totalizar recebimentos
		$total_recebido = 0;
		$query2 = $pdo->query("SELECT * FROM receber_sas where usuario_pgto = '$operador' and data_pgto >= '$data_abertura' and data_pgto <= '$data_fechamento_consulta' and forma_pgto = '$pgto_dinheiro' and caixa = '$id'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		if (@count($res2) > 0) {
			for ($i2 = 0; $i2 < @count($res2); $i2++) {
				$valor_rec = $res2[$i2]['valor'];
				$total_recebido += $valor_rec;
			}
		}

		//totalizar saidas
		$total_saidas = 0;
		$query2 = $pdo->query("SELECT * FROM pagar_sas where usuario_pgto = '$operador' and data_pgto >= '$data_abertura' and data_pgto <= '$data_fechamento_consulta' and forma_pgto = '$pgto_dinheiro' and caixa = '$id'");
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

		$total_caixaF = @number_format($total_caixa, 2, ',', '.');

		echo <<<HTML
			<tr> 
				<td>
				<i class="fa fa-square {$classe_aberto} mr-1"></i> {$nome_operador} </td> 
				
				<td class="esc">{$data_aberturaF}</td>				
				<td class="esc">R$ {$valor_aberturaF}</td>
				<td class="esc">R$ {$valor_fechamentoF}</td>
				<td class="esc">R$ {$total_sangriasF}</td>
				<td class="esc {$classe_quebra}">R$ {$quebraF}</td>
				<td class="esc">{$nome_usu_abertura}</td>
				<td class="esc">{$nome_usu_fechamento}</td>
				
				<td>
		<a class="{$ocultar} btn btn-info-light btn-sm" href="#" onclick="editar('{$id}', '{$operador}', '{$data_abertura}','{$valor_aberturaF}','{$obs}')" title="Editar Dados"><i class="fa fa-edit "></i></a>

		<a href="#" class="{$ocultar} btn btn-danger-light btn-sm" onclick="excluir('{$id}')" title="Excluir"><i class="fa fa-trash-can text-danger"></i></a>

		<a class="{$ocultar} btn btn-success-light btn-sm" href="#" onclick="fechamento('{$id}', '{$nome_operador}', '{$valor_aberturaF}', '{$total_caixaF}')" title="Fechar Caixa"><i class="fa fa-check-square "></i></a>

		<a class="{$ocultar} btn btn-success-light btn-sm" href="#" onclick="sangria('{$id}', '{$nome_operador}')" title="Sangria"><i class="fa fa-money  "></i></a>

		<form   method="POST" action="rel/detalhamento_caixa_class.php" target="_blank" style="display:inline-block">
					<input type="hidden" name="id" value="{$id}">
					<button class="btn btn-dark-light btn-sm" title="Detalhamento do Caixa"><i class="fa fa-file-pdf-o " ></i></button>
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
	echo 'Não possui nenhum registro!';
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
		$('#operador').val(operador).change();
		$('#valor_abertura').val(valor_abertura);
		$('#data_abertura').val(data_abertura);
		$('#obs').val(obs);
		$('#tituloModal').text('Editar Registro');
		$('#modalForm').modal('show');
		$('#mensagem').text('');
		$('#titulo_inserir').text('Editar Registro');
	}




	function fechamento(id, operador, valor_abertura, total_caixa) {

		//Formatar o valor antes de mandar pro campo
		let numeroFormatado = total_caixa.replace(/\B(?=(\d{3})+(?!\d))/g, '.').replace('.', ',');
		$('#id_fechar').val(id);
		$('#nome_operador').val(operador);
		$('#valor_abertura_fechar').val(valor_abertura);
		$('#total_caixa_fechar').val(numeroFormatado);
		$('#tituloModalFechar').text('Fechar Caixa');
		$('#modalFechar').modal('show');
		$('#mensagem_fechar').text('');
	}



	function sangria(id, operador) {
		$('#id_sangria').val(id);
		$('#nome_operador_sangria').val(operador);
		$('#tituloModalSangria').text('Efetuar Sangria');
		$('#modalSangria').modal('show');
		$('#mensagem_sangria').text('');
	}



	function limparCampos() {
		$('#id').val('');
		//$('#operador').val('').change();		
		$('#valor_abertura').val('<?= $ultimo_valor_fechadoF ?>');
		$('#data_abertura').val('<?= $data_atual ?>');

		$('#obs').val('');
		$('#ids').val('');
		$('#div_botoes').hide();

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

		var ids_final = $('#ids').val();
		if (ids_final == "") {
			$('#btn-deletar').hide();
		} else {
			$('#btn-deletar').show();

		}
	}


		// ALERT EXCLUIR #######################################
		function deletarSel(id) {
		const swalWithBootstrapButtons = Swal.mixin({
			customClass: {
				confirmButton: "btn btn-success", // Adiciona margem à direita do botão "Sim, Excluir!"
				cancelButton: "btn btn-danger me-1"
			},
			buttonsStyling: false
		});
		swalWithBootstrapButtons.fire({
			title: "Deseja Excluir?",
			text: "Você não conseguirá recuperá-lo novamente!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: "Sim, Excluir!",
			cancelButtonText: "Não, Cancelar!",
			reverseButtons: true
		}).then((result) => {
			if (result.isConfirmed) {
				// Realiza a requisição AJAX para excluir o item
				var ids = $('#ids').val();
				var id = ids.split("-");
				for (i = 0; i < id.length - 1; i++) {
					excluirMultiplos(id[i]);
				}
				setTimeout(() => {
					// Ação de exclusão aqui
					Swal.fire({
						title: 'Excluido com Sucesso!',
						text: 'Fecharei em 1 segundo.',
						icon: "success",
						timer: 1000
					})
					listar();
				}, 1000);
				limparCampos();
			} else if (result.dismiss === Swal.DismissReason.cancel) {
				swalWithBootstrapButtons.fire({
					title: "Cancelado",
					text: "Fecharei em 1 segundo.",
					icon: "error",
					timer: 1000,
					timerProgressBar: true,
				});
			}
		});
	}
</script>