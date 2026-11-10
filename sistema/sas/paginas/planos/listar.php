<?php
$tabela = 'planos';
require_once("../../../conexao.php");

$query = $pdo->query("SELECT * from $tabela order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	echo <<<HTML
<small>
	<table class="table table-striped table-hover table-bordered text-nowrap border-bottom dt-responsive" id="tabela">
	<thead> 
	<tr> 
	<th align="center" width="5%" class="text-center">Selecionar</th>
	<th>Nome</th>	
	<th>Valor</th>		
	<th>Limite Clientes</th>		
	<th>Limite Usuários</th>
	<th>Limite Dispositivos</th>			
	<th>Ações</th>
	</tr> 
	</thead> 
	<tbody>	
HTML;


	for ($i = 0; $i < $linhas; $i++) {
		$id = $res[$i]['id'];
		$nome = $res[$i]['nome'];
		$valor = $res[$i]['valor'];
		$ativo = $res[$i]['ativo'];
		$clientes = $res[$i]['clientes'];
		$usuarios = $res[$i]['usuarios'];
		$dispositivos = $res[$i]['dispositivos'];

		if($clientes == 0){
			$clientes = "";
		}

		if($usuarios == 0){
			$usuarios = "";
		}
		
		$valorF = @number_format($valor, 2, ',', '.');


		if ($ativo == 'Sim') {
			$icone = 'fa-check-square';
			$titulo_link = 'Desativar Usuário';
			$acao = 'Não';
			$classe_ativo = '';
		} else {
			$icone = 'fa-square-o';
			$titulo_link = 'Ativar Usuário';
			$acao = 'Sim';
			$classe_ativo = '#c4c4c4';
		}

		echo <<<HTML
<tr>
<td align="center">
<div class="custom-checkbox custom-control">
<input type="checkbox" class="custom-control-input" id="seletor-{$id}" onchange="selecionar('{$id}')">
<label for="seletor-{$id}" class="custom-control-label mt-1 text-dark"></label>
</div>
</td>
<td style="color:{$classe_ativo}">{$nome}</td>
<td class="esc" style="color:{$classe_ativo}">{$valorF}</td>
<td class="esc" style="color:{$classe_ativo}">{$clientes}</td>
<td class="esc" style="color:{$classe_ativo}">{$usuarios}</td>
<td class="esc" style="color:{$classe_ativo}">{$dispositivos}</td>
<td>
	<big><a class="btn btn-info-light btn-sm" href="#" onclick="editar('{$id}','{$nome}','{$valorF}','{$clientes}','{$usuarios}','{$dispositivos}')" title="Editar Dados"><i class="fa fa-edit "></i></a></big>

		<big><a href="#" class="btn btn-danger-light btn-sm" onclick="excluir('{$id}')" title="Excluir"><i class="fa fa-trash-can text-danger"></i></a></big>

		<big><a href="#" class="btn btn-info btn-sm" onclick="itens('{$id}', '{$nome}')" title="Características do Plano"><i class="fa fa-plus "></i></a></big>

		<big><a href="#" class="btn btn-secondary btn-sm" onclick="recursos('{$id}', '{$nome}')" title="Recursos do Plano"><i class="fa fa-plus-square-o "></i></a></big>

		<a class="btn btn-success-light btn-sm" href="#" onclick="ativar('{$id}', '{$acao}')" title="{$titulo_link}"><i class="fa {$icone} "></i></a>
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
	echo 'Nenhum Registro Encontrado!';
}

?>



<script type="text/javascript">
	$(document).ready(function() {
		$('#tabela').DataTable({
			"language": {
				//"url" : '//cdn.datatables.net/plug-ins/1.13.2/i18n/pt-BR.json'
			},
			"ordering": false,
			"stateSave": true
		});
	});
</script>

<script type="text/javascript">
	function editar(id, nome, valor, clientes, usuarios, dispositivos) {
		$('#mensagem').text('');
		$('#titulo_inserir').text('Editar Registro');

		$('#id').val(id);
		$('#nome').val(nome);
		$('#valor').val(valor);
		$('#clientes').val(clientes);
		$('#usuarios').val(usuarios);
		$('#dispositivos').val(dispositivos);
		

		$('#modalForm').modal('show');
	}



	function limparCampos() {
		$('#id').val('');
		$('#nome').val('');
		$('#valor').val('');
		$('#clientes').val('');
		$('#usuarios').val('');
		$('#dispositivos').val('');
		
		$('#ids').val('');
		$('#btn-deletar').hide();
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



	function itens(id, nome) {		
		$('#titulo_item').text(nome);
		$('#id_item').val(id);		
		listarItens();	
		$('#modalItem').modal('show');
	}

	function recursos(id, nome) {		
		$('#titulo_rec').text(nome);
		$('#id_rec').val(id);		
		listarRecursos();	
		$('#modalRec').modal('show');
	}
</script>