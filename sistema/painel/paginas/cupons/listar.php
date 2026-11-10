<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
$tabela = 'cupons';

$query = $pdo->query("SELECT * FROM $tabela where empresa = '$id_empresa' order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {

	echo <<<HTML
	<small>
	<table class="table table-hover table-bordered text-nowrap border-bottom dt-responsive" id="tabela">
	<thead> 
	<tr> 
		<th align="center" width="5%" class="text-center">Selecionar</th>
	<th>Código</th>	
	<th>Valor</th>	
	<th>Data</th>	
	<th>Quantidade</th>	
		<th>Valor Mínimo</th>	
	<th>Ações</th>
	</tr> 
	</thead> 
	<tbody>	
HTML;

	for ($i = 0; $i < $total_reg; $i++) {
		foreach ($res[$i] as $key => $value) {
		}
		$id = $res[$i]['id'];
		$codigo = $res[$i]['codigo'];
		$valor = $res[$i]['valor'];
		$data = $res[$i]['data'];
		$quantidade = $res[$i]['quantidade'];
		$valor_minimo = $res[$i]['valor_minimo'];
		$tipo = $res[$i]['tipo'];

		$dataF = implode('/', array_reverse(explode('-', $data)));
		$valorF = number_format($valor, 2, ',', '.');
		$valor_minimoF = number_format($valor_minimo, 2, ',', '.');
		$valor_p = number_format($valor, 0, ',', '.');

		if ($tipo == '%') {
			$nome_tipo = $valor_p . ' %';
		} else {
			$nome_tipo = 'R$ ' . $valorF;
		}

		echo <<<HTML
<tr>
	<td align="center">
<div class="custom-checkbox custom-control">
<input type="checkbox" class="custom-control-input" id="seletor-{$id}" onchange="selecionar('{$id}')">
<label for="seletor-{$id}" class="custom-control-label mt-1 text-dark"></label>
</div>
</td>
<td>{$codigo}</td>
<td>{$nome_tipo}</td>
<td>{$dataF}</td>
<td>{$quantidade}</td>
<td>{$valor_minimoF}</td>
<td>
	<a class="btn btn-info-light btn-sm" href="#" onclick="editar('{$id}','{$codigo}','{$valor}','{$data}','{$quantidade}','{$valor_minimo}','{$tipo}')" title="Editar Dados"><i class="fa fa-edit"></i></a>

	

<big><a href="#" class="btn btn-danger-light btn-sm" onclick="excluir('{$id}')" title="Excluir"><i class="fa fa-trash-can text-danger"></i></a></big>





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
	echo 'Não possui registros cadastrados!';
}


?>


<script type="text/javascript">
	$(document).ready(function() {
		$('#tabela').DataTable({
			"ordering": false,
			"stateSave": true
		});
		$('#tabela_filter label input').focus();
	});
</script>


<script type="text/javascript">
	function editar(id, codigo, valor, data, quantidade, valor_minimo, tipo) {
		$('#id').val(id);
		$('#codigo').val(codigo);
		$('#valor').val(valor);
		$('#data').val(data);
		$('#quantidade').val(quantidade);
		$('#valor_minimo').val(valor_minimo);
		$('#tipo').val(tipo).change();

		$('#titulo_inserir').text('Editar Registro');
		$('#modalForm').modal('show');

	}



	function limparCampos() {
		$('#id').val('');
		$('#codigo').val('');
		$('#valor').val('');
		$('#data').val('');
		$('#quantidade').val('1');
		$('#valor_minimo').val('');
	}
</script>


