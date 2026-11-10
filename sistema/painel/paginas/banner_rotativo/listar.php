<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
$tabela = 'banner_rotativo';
require_once("../../../conexao.php");
require_once("../../buscar_config.php");


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
	<th class="width70">Categoria</th>		
	<th>Foto</th>		
	<th>Ações</th>
	</tr> 
	</thead> 
	<tbody>	
HTML;

	for ($i = 0; $i < $total_reg; $i++) {
		foreach ($res[$i] as $key => $value) {
		}
		$id = $res[$i]['id'];
		$categoria = $res[$i]['categoria'];
		$foto = $res[$i]['foto'];

		$query2 = $pdo->query("SELECT * FROM categorias where id = '$categoria'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$total_reg2 = @count($res2);
		if ($total_reg2 > 0) {
			$nome_cat = $res2[0]['nome'];
		} else {
			$nome_cat = 'Sem Categoria!';
		}


		echo <<<HTML
<tr>
<td align="center">
<div class="custom-checkbox custom-control">
<input type="checkbox" class="custom-control-input" id="seletor-{$id}" onchange="selecionar('{$id}')">
<label for="seletor-{$id}" class="custom-control-label mt-1 text-dark"></label>
</div>
</td>
<td>{$nome_cat}</td>
<td class="esc"><img src="images/{$tabela}/{$foto}" width="30px"></td>
<td>
	<a class="btn btn-info-light btn-sm" href="#" onclick="editar('{$id}','{$categoria}', '{$foto}')" title="Editar Dados"><i class="fa fa-edit"></i></a>

	
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
	function editar(id, cat, foto) {
		$('#id').val(id);
		$('#categoria').val(cat).change();

		$('#titulo_inserir').text('Editar Registro');
		$('#modalForm').modal('show');
		$('#foto').val('');
		$('#target').attr('src', 'images/banner_rotativo/' + foto);
	}




	function limparCampos() {
		$('#id').val('');
		$('#foto').val('');
		$('#target').attr('src', 'images/banner_rotativo/sem-foto.jpg');
	}
</script>

