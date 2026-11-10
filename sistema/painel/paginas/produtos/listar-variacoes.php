<?php
require_once("../../../conexao.php");
$tabela = 'variacoes';

$id = $_POST['id'];

$query = $pdo->query("SELECT * FROM $tabela where produto = '$id' order by id desc");
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
	<th>Valor</th> 		
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
		$valor = $res[$i]['valor'];
		$ativo = $res[$i]['ativo'];
		$produto = $res[$i]['produto'];

		$valorF = number_format($valor, 2, ',', '.');

		$query2 = $pdo->query("SELECT * FROM produtos where id = '$produto' ORDER BY id asc");
	$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
	$categoria_p = $res2[0]['categoria'];

	$query2 = $pdo->query("SELECT * FROM variacoes_cat where categoria = '$categoria_p' and sigla = '$sigla' ORDER BY id asc");
	$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
	$id_variacoes_cat = $res2[0]['id'];


		if ($ativo == 'Sim') {
			$icone = 'fa-check-square';
			$titulo_link = 'Desativar Item';
			$acao = 'Não';
			$classe_linha = '';
		} else {
			$icone = 'fa-square-o';
			$titulo_link = 'Ativar Item';
			$acao = 'Sim';
			$classe_linha = '#c4c4c4';
		}



		echo <<<HTML
<tr>
<td style="color:{$classe_linha}">{$nome}</td>
<td style="color:{$classe_linha}">{$sigla}</td>
<td style="color:{$classe_linha}">R$ {$valorF}</td>

<td>


<div class="dropdown" style="display: inline-block;">                      
	<a href="#" aria-expanded="false" aria-haspopup="true" data-bs-toggle="dropdown" class="dropdown btn btn-danger-light btn-sm"><i class="fe fe-trash-2 text-danger"></i> 
	</a>
		<div  class="dropdown-menu tx-13">
			<div class="dropdown-item-text botao_excluir_listar">
			<p>Confirmar Exclusão? <a href="#" onclick="excluirVar('{$id}')"><span class="botao_excluir_listar_sim">Sim</span></a></p>
			</div>
		</div>
</div>


		<a class="btn btn-success-light btn-sm" href="#" onclick="ativarVar('{$id}', '{$acao}')" title="{$titulo_link}"><i class="fa {$icone}"></i></a>


<a class="btn btn-info btn-sm" href="#" onclick="editarVa('{$id}', '{$id_variacoes_cat}', '{$valor}', '{$nome}')" title="Editar Variação"><i class="fa fa-edit"></i></a>


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

<script type="text/javascript">
		function editarVa(id, sigla, valor, nome) {

		$('#valor_var').val(valor);
		$('#variacao').val(sigla).change();		
		$('#id_it_var').val(id);

		$('#btn_grade').text('Editar');

	}
</script>