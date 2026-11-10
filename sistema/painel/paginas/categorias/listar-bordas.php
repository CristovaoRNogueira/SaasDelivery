<?php
require_once("../../../conexao.php");
$tabela = 'bordas';

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
		$valor = $res[$i]['valor'];
		$ativo = $res[$i]['ativo'];
		$categoria = $res[$i]['categoria'];



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
<td style="color:{$classe_linha}">{$valor}</td>
<td>
	


<div class="dropdown" style="display: inline-block;">                      
	<a href="#" aria-expanded="false" aria-haspopup="true" data-bs-toggle="dropdown" class="dropdown btn btn-danger-light btn-sm"><i class="fe fe-trash-2 text-danger"></i> 
	</a>
		<div  class="dropdown-menu tx-13">
			<div class="dropdown-item-text botao_excluir_listar">
			<p>Confirmar Exclusão? <a href="#" onclick="excluirBordas('{$id}', '{$nome}')"><span class="botao_excluir_listar_sim">Sim</span></a></p>
			</div>
		</div>
</div>


	<a class="btn btn-success-light btn-sm" href="#" onclick="ativarBordas('{$id}', '{$acao}')" title="{$titulo_link}"><i class="fa {$icone}"></i></a>


	<a class="btn btn-info-light btn-sm" href="#" onclick="editarBordas('{$id}', '{$nome}', '{$valor}', '{$categoria}')" title="Editar Variações"><i class="fa fa-edit"></i></a>



</td>
</tr>
HTML;
	}

	echo <<<HTML
	</tbody>
	<small><div align="center" id="mensagem-excluir-ing"></div></small>
	</table>
	</small>
HTML;
} else {
	echo '<small>Não possui nenhum Adicional cadastrado!</small>';
}


?>


<script>
	function editarBordas(id, nome, valor, categoria) {

		$('#id_borda_editar').val(id);
		$('#nome_bordas').val(nome);
		$('#valor_bordas').val(valor);
		$('#id_categoria').val(categoria);

		$('#btn_borda').text('Editar');

	}
</script>