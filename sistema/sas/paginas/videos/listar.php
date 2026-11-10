<?php
$tabela = 'videos';
require_once("../../../conexao.php");

$query = $pdo->query("SELECT * from $tabela order by ordem asc, id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	echo <<<HTML
<small>
	<table class="table table-hover table-bordered text-nowrap border-bottom dt-responsive" id="tabela">
	<thead> 
	<tr> 
	<th width="">Posição</th>	
	<th width="">Título</th>	
	<th>Link</th>		
	<th>Ações</th>
	</tr> 
	</thead> 
	<tbody>	
HTML;
} else {
	echo 'Não possui nenhum cadastro!';
}

for ($i = 0; $i < $linhas; $i++) {
	$id = $res[$i]['id'];
	$titulo = $res[$i]['titulo'];
	$link = $res[$i]['link'];
	$ordem = $res[$i]['ordem'];

	echo <<<HTML
<tr >
<td><input type="number" name="ordem" id="ordem_{$id}" style="width:50px; border:none; border-bottom: 1px solid #000; outline:none; background:transparent;" value="{$ordem}" onblur="editarOrdem('{$id}')"></td>
<td>{$titulo}</td>
<td><a href="{$link}" target="_blank">{$link}</a></td>
<td>
	<big><a class="btn btn-info-light btn-sm" href="#" onclick="editar('{$id}','{$titulo}','{$link}')" title="Editar Dados"><i class="fa fa-edit "></i></a></big>

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
	function editar(id, titulo, link) {
		$('#mensagem').text('');
		$('#titulo_inserir').text('Editar Registro');

		$('#id').val(id);
		$('#titulo').val(titulo);
		$('#link').val(link);

		$('#modalForm').modal('show');
	}




	function limparCampos() {
		$('#id').val('');
		$('#titulo').val('');
		$('#link').val('');

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
</script>

<script type="text/javascript">
	function editarOrdem(id){
		var posicao = $('#ordem_'+id).val();
		 $.ajax({
        url: 'paginas/' + pag + "/editar_posicao.php",
        method: 'POST',
        data: { id, posicao },
        dataType: "html",

        success: function (result) {
            listar();
        }
    });
	}
</script>