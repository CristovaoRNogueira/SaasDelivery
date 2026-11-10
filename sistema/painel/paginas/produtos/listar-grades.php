<?php
require_once ("../../../conexao.php");
$tabela = 'grades';

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
	<th>Posição</th>
	<th>Texto</th>	
	<th>Tipo Item</th> 	
	<th>Valor Item</th> 
	<th>Limite</th> 		
	<th>Ações</th>
	</tr> 
	</thead> 
	<tbody>	
HTML;

	for ($i = 0; $i < $total_reg; $i++) {
		foreach ($res[$i] as $key => $value) {
		}
		$id = $res[$i]['id'];
		$texto = $res[$i]['texto'];
		$tipo_item = $res[$i]['tipo_item'];
		$valor_item = $res[$i]['valor_item'];
		$limite = $res[$i]['limite'];
		$ativo = $res[$i]['ativo'];
		$produto = $res[$i]['produto'];
		$nome_comprovante = $res[$i]['nome_comprovante'];
		$adicional = $res[$i]['adicional'];
		$posicao = $res[$i]['posicao'];


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

		if ($limite == 0) {
			$limite = 'ilimitado';
		}

		$textoF = mb_strimwidth($texto, 0, 45, "...");


		echo <<<HTML
<tr>
<td><input value="{$posicao}" onblur="editarPosicaoGrade('{$id}')" type="text" name="posicao" id="posicao_grade_{$id}" style="width:35px; border:none; border-bottom:1px solid #000; outline:none; background:transparent;"></td>

<td style="color:{$classe_linha}">{$textoF}</td>
<td style="color:{$classe_linha}">{$tipo_item}</td>
<td style="color:{$classe_linha}">{$valor_item}</td>
<td style="color:{$classe_linha}">{$limite}</td>
<td>


<div class="dropdown" style="display: inline-block;">                      
	<a href="#" aria-expanded="false" aria-haspopup="true" data-bs-toggle="dropdown" class="dropdown btn btn-danger-light btn-sm"><i class="fe fe-trash-2 text-danger"></i> 
	</a>
		<div  class="dropdown-menu tx-13">
			<div class="dropdown-item-text botao_excluir_listar">
			<p>Confirmar Exclusão? <a href="#" onclick="excluirGrades('{$id}')"><span class="botao_excluir_listar_sim">Sim</span></a></p>
			</div>
		</div>
</div>





		<a class="btn btn-success-light btn-sm" href="#" onclick="ativarGrades('{$id}', '{$acao}')" title="{$titulo_link}"><i class="fa {$icone}"></i></a>


		<a class="btn btn-primary-light btn-sm" href="#" onclick="addItensGrade('{$id}', '{$produto}', '$texto', '$adicional')" title="Adicionar Itens"><i class="fa fa-plus"></i></a>

		<a class="btn btn-info-light btn-sm" href="#" onclick="editarGrade('{$id}', '{$tipo_item}', '$valor_item', '$texto', '$limite', '$nome_comprovante', '$adicional')" title="Editar Grade"><i class="fa fa-edit"></i></a>



</td>
</tr>
HTML;

	}

	echo <<<HTML
	</tbody>
	<small><div align="center" id="mensagem-excluir-grades"></div></small>
	</table>
	</small>
HTML;


} else {
	echo '<small>Não possui nenhuma variação cadastrada!</small>';
}






?>



<script type="text/javascript">
	function addItensGrade(id, produto, texto, adicional) {

		$('#titulo_nome_itens').text(texto);
		$('#id_item').val(id);
		$('#id_item_produto').val(produto);
		$('#e_adicional').val(adicional);

		listarItens(id);

		if (adicional == 'Sim') {
			$('#div_adicional').show();
			$('#div_nome').hide();
		} else {
			$('#div_adicional').hide();
			$('#div_nome').show();
		}

		$('#btn_itens').text('Salvar');
		$('#adicional_grade').val('');
		$('#id_item_editar').val('');

		$('#modalItens').modal('show');
		limparCamposItens();

		$('#mensagem-itens').text('');
	}


	function editarGrade(id, tipo_item, valor_item, texto, limite, nome_comprovante, adicional) {


		$('#tipo_item').val(tipo_item);
		$('#valor_item').val(valor_item);
		$('#texto').val(texto);
		$('#limite').val(limite);
		$('#nome_comprovante').val(nome_comprovante);
		$('#adicional').val(adicional);
		$('#id_grade_editar').val(id);

		$('#btn_grade').text('Editar');

	}


	function editarPosicaoGrade(id) {
		var posicao = $('#posicao_grade_'+id).val();

		$.ajax({
			url: 'paginas/' + pag + "/editar_posicao_grade.php",
			method: 'POST',
			data: {
				id, posicao
			},
			dataType: "html",

			success: function(result) {
				if(result.trim() == "Editado com Sucesso"){
					//listarGrades();
				}else{
					alert(result)
				}
				
			}
		});
	}
</script>