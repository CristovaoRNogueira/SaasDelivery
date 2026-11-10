<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
$tabela = 'categorias';

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
		<th>Posição</th>
		<th>Foto</th>
	<th>Nome</th>	
	<th>Descrição</th> 	
	<th>Mais Sabores</th> 	
	<th>Delivery</th> 
	<th>Produtos</th> 
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
		$ativo = $res[$i]['ativo'];
		$foto = $res[$i]['foto'];
		$descricao = $res[$i]['descricao'];
		$mais_sabores = $res[$i]['mais_sabores'];
		$delivery = $res[$i]['delivery'];
		$posicao = $res[$i]['posicao'];

		$ocultar_ad = 'ocultar';
		if ($mais_sabores == 'Sim') {
			$ocultar_ad = '';
		}

		$descricaoF = mb_strimwidth($descricao, 0, 50, "...");

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

		$query2 = $pdo->query("SELECT * from produtos where categoria = '$id'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$produtos = @count($res2);

		$nomeF = rawurlencode($nome);


		echo <<<HTML
<tr class="{$classe_linha}">
	<td align="center">
<div class="custom-checkbox custom-control">
<input type="checkbox" class="custom-control-input" id="seletor-{$id}" onchange="selecionar('{$id}')">
<label for="seletor-{$id}" class="custom-control-label mt-1 text-dark"></label>
</div>
</td>
	<td><input value="{$posicao}" onblur="editarPosicao('{$id}')" type="text" name="posicao" id="posicao_{$id}" style="width:35px; border:none; border-bottom:1px solid #000; outline:none; background:transparent;"></td>
	<td class="text-center" style="color:{$classe_linha}">
	<img alt="avatar" class="rounded-circle" src="images/{$tabela}/{$foto}" width="30px" height="30px">
	</td>
<td style="color:{$classe_linha}">{$nome}</td>
<td style="color:{$classe_linha}">{$descricaoF}</td>
<td style="color:{$classe_linha}">{$mais_sabores}</td>
<td style="color:{$classe_linha}">{$delivery}</td>
<td align="center" style="color:{$classe_linha}">{$produtos}</td>
<td>
	<a class="btn btn-info-light btn-sm" href="#" onclick="editar('{$id}','{$nomeF}', '{$descricao}', '{$foto}', '{$mais_sabores}', '{$delivery}')" title="Editar Dados"><i class="fa fa-edit"></i></a>

	
<big><a href="#" class="btn btn-danger-light btn-sm" onclick="excluir('{$id}')" title="Excluir"><i class="fa fa-trash-can text-danger"></i></a></big>


		<a class="btn btn-success-light btn-sm" href="#" onclick="ativar('{$id}', '{$acao}')" title="{$titulo_link}"><i class="fa {$icone}"></i></a>

		<a class="$ocultar_ad btn btn-primary-light btn-sm" href="#" onclick="variacoes('{$id}','{$nome}')" title="Variações do Produto"><i class="fa fa-list"></i></a>

		<a class="{$ocultar_ad} btn btn-success-light btn-sm" href="#" onclick="adicionais('{$id}','{$nome}')" title="Adicionais da Categoria"><i class="fa fa-plus "></i></a>

		<a class="{$ocultar_ad} btn btn-warning-light btn-sm" href="#" onclick="bordas('{$id}','{$nome}')" title="Opções de Bordas"><i class="fa fa-plus-circle"></i></a>


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
	function editar(id, nome, descricao, foto, mais_sabores, delivery) {
		$('#id').val(id);
		$('#nome').val(decodeURIComponent(nome));
		$('#descricao').val(descricao);
		$('#mais_sabores').val(mais_sabores).change();
		$('#delivery').val(delivery).change();

		$('#titulo_inserir').text('Editar Registro');
		$('#modalForm').modal('show');
		$('#foto').val('');
		$('#target').attr('src', 'images/categorias/' + foto);
	}




	function limparCampos() {
		$('#id').val('');
		$('#nome').val('');
		$('#descricao').val('');
		$('#foto').val('');
		$('#delivery').val('Sim').change()
		$('#target').attr('src', 'images/categorias/sem-foto.jpg');
	}
</script>


<script type="text/javascript">
	function variacoes(id, nome) {

		$('#titulo_nome_var').text(nome);
		$('#id_var').val(id);

		listarVariacoes(id);
		$('#modalVariacoes').modal('show');
		$('#btn_var').text('Salvar');
		$('#id_var_editar').val('');
		limparCamposVar();
	}
</script>


<script type="text/javascript">
	function adicionais(id, nome) {

		$('#titulo_nome_adc').text(nome);
		$('#id_adc').val(id);

		$('#btn_editar_adc').text('Salvar');
		$('#id_adc_editar').val('');
		limparCamposVar()

		listarAdicionais(id);
		$('#modalAdicionais').modal('show');
		limparCamposAdc();
	}
</script>


<script type="text/javascript">
	function bordas(id, nome) {

		$('#titulo_nome_bordas').text(nome);
		$('#id_bordas').val(id);

		listarBordas(id);
		$('#modalBordas').modal('show');
		limparCamposBordas();
	}

	function editarPosicao(id) {
		var posicao = $('#posicao_'+id).val();

		$.ajax({
			url: 'paginas/' + pag + "/editar_posicao.php",
			method: 'POST',
			data: {
				id, posicao
			},
			dataType: "html",

			success: function(result) {
				if(result.trim() == "Editado com Sucesso"){
					listar();
				}else{
					alert(result)
				}
				
			}
		});
	}
</script>

