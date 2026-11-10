<?php
require_once ("../../../conexao.php");
$tabela = 'itens_grade';

$id = $_POST['id'];

$query = $pdo->query("SELECT * FROM $tabela where grade = '$id' order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {

	echo <<<HTML
	<small>
	<table class="table table-hover table-bordered text-nowrap border-bottom dt-responsive">
	<thead> 
	<tr> 
	<th>Posição</th>
	<th>Nome</th>	
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
		$valor = $res[$i]['valor'];
		$limite = $res[$i]['limite'];
		$ativo = $res[$i]['ativo'];
		$produto = $res[$i]['produto'];
		$adicional = $res[$i]['adicional'];
		$posicao = $res[$i]['posicao'];

		$valorF = number_format($valor, 2, ',', '.');

		$textoF = mb_strimwidth($texto, 0, 20, "...");



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



		echo <<<HTML
<tr>
<td><input value="{$posicao}" onblur="editarPosicaoItens('{$id}')" type="text" name="posicao" id="posicao_itens_{$id}" style="width:35px; border:none; border-bottom:1px solid #000; outline:none; background:transparent;"></td>

<td style="color:{$classe_linha}">{$textoF}</td>
<td style="color:{$classe_linha}">R$ {$valorF}</td>
<td style="color:{$classe_linha}">{$limite}</td>
<td>
	

<div class="dropdown" style="display: inline-block;">                      
	<a href="#" aria-expanded="false" aria-haspopup="true" data-bs-toggle="dropdown" class="dropdown btn btn-danger-light btn-sm"><i class="fe fe-trash-2 text-danger"></i> 
	</a>
		<div  class="dropdown-menu tx-13">
			<div class="dropdown-item-text botao_excluir_listar">
			<p>Confirmar Exclusão? <a href="#" onclick="excluirItens('{$id}')"><span class="botao_excluir_listar_sim">Sim</span></a></p>
			</div>
		</div>
</div>



		<a class="btn btn-success-light btn-sm" href="#" onclick="ativarItens('{$id}', '{$acao}')" title="{$titulo_link}"><i class="fa {$icone}"></i></a>


		<a class="btn btn-info-light btn-sm" href="#" onclick="editarItens('{$id}', '{$texto}', '$valor', '$limite', '$adicional', '$produto')" title="Editar Item"><i class="fa fa-edit"></i></a>


</td>
</tr>
HTML;

	}

	echo <<<HTML
	</tbody>
	<small><div align="center" id="mensagem-excluir-itens"></div></small>
	</table>
	</small>
HTML;


} else {
	echo '<small>Não possui nenhuma variação cadastrada!</small>';
}


?>

<script type="text/javascript">
	function editarItens(id, texto, valor, limite, adicional, produto) {


		$('#texto_item').val(texto);
		$('#adicional_grade').val(adicional).change();
		$('#valor_do_item').val(valor);
		$('#limite_itens').val(limite);
		$('#id_item_editar').val(id);
		$('#id_item_produto').val(produto);

		if (adicional > 0) {
			$('#e_adicional').val('Sim');
		} else {
			$('#e_adicional').val('Não');
		}
		$('#adicional').val(adicional);

		$('#btn_itens').text('Editar');

	}


	function editarPosicaoItens(id) {
		var posicao = $('#posicao_itens_'+id).val();

		$.ajax({
			url: 'paginas/' + pag + "/editar_posicao_itens.php",
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