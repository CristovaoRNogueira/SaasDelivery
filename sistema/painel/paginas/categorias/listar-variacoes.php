<?php
require_once ("../../../conexao.php");
$tabela = 'variacoes_cat';

$id = $_POST['id'];

$query = $pdo->query("SELECT * FROM $tabela where categoria = '$id' order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {

	echo <<<HTML
	<small>
	<table class="table table-hover table-bordered text-nowrap border-bottom dt-responsive">
	<thead> 
	<tr> 
	<th>Nome</th>	
	<th>Sigla</th> 	
	<th>Sabores</th> 		
	<th>Descrição</th> 		
	<th>Ações</th>
	</tr> 
	</thead> 
	<tbody>	
HTML;

	for ($i = 0; $i < $total_reg; $i++) {
		foreach ($res[$i] as $key => $value) {
		}
		$id = $res[$i]['id'];
		$nome = $res[$i]['nome'];
		$sigla = $res[$i]['sigla'];
		$descricao = $res[$i]['descricao'];
		$sabores = $res[$i]['sabores'];


		echo <<<HTML
<tr class="">
<td>
{$nome}
</td>
<td>{$sigla}</td>
<td>{$sabores}</td>
<td>{$descricao}</td>

<td>
	
<div class="dropdown" style="display: inline-block;">                      
	<a href="#" aria-expanded="false" aria-haspopup="true" data-bs-toggle="dropdown" class="dropdown btn btn-danger-light btn-sm"><i class="fe fe-trash-2 text-danger"></i> 
	</a>
		<div  class="dropdown-menu tx-13">
			<div class="dropdown-item-text botao_excluir_listar">
			<p>Confirmar Exclusão? <a href="#" onclick="excluirVar('{$id}', '{$nome}')"><span class="botao_excluir_listar_sim">Sim</span></a></p>
			</div>
		</div>
</div>


		<a class="btn btn-info-light btn-sm" href="#" onclick="editarVar('{$id}', '{$nome}', '{$sigla}', '{$descricao}', '{$sabores}')" title="Editar Variações"><i class="fa fa-edit"></i></a>

		



</td>
</tr>
HTML;

	}

	echo <<<HTML
	</tbody>
	<small><div align="center" id="mensagem-excluir-var"></div></small>
	</table>
	</small>
HTML;


} else {
	echo '<small>Não possui nenhuma variação cadastrada!</small>';
}


?>

<script>
	function editarVar(id, nome, sigla, descricao, sabores) {

		$('#id_var_editar').val(id);
		$('#nome_var').val(nome);
		$('#sigla').val(sigla);
		$('#descricao_var').val(descricao);
		$('#sabores').val(sabores);

		$('#btn_var').text('Editar');

	}
</script>