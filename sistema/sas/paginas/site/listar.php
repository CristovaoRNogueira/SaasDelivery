<?php
$tabela = 'anotacoes';
@session_start();
$id_usuario = @$_SESSION['id'];

require_once("../../../conexao.php");
require_once("../../verificar.php");

$query = $pdo->query("SELECT * from $tabela where empresa = 0 order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	echo <<<HTML
<small>
	<table class="table table-hover table-bordered text-nowrap border-bottom dt-responsive" id="tabela">
	<thead> 
	<tr>
	<th>data</th>		
	<th>Título</th>
	<th>Usuário</th>
	<th>Monstrar na Home</th>
	<th>Privado</th>
	<th>Ações</th>
	</tr> 
	</thead> 
	<tbody>	
HTML;

	for ($i = 0; $i < $linhas; $i++) {
		$id = $res[$i]['id'];
		$titulo = $res[$i]['titulo'];
		$msg = $res[$i]['msg'];
		$usuario = $res[$i]['usuario'];
		$data = $res[$i]['data'];
		$mostrar_home = $res[$i]['mostrar_home'];
		$privado = $res[$i]['privado'];

		$query2 = $pdo->query("SELECT * FROM usuarios where id = '$usuario'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		if (@count($res2) > 0) {
			$nome_usuario = $res2[0]['nome'];
		} else {
			$nome_usuario = 'Sem Usuário';
		}

		$dataF = implode('/', array_reverse(@explode('-', $data)));


		if ($privado == 'Sim' and $usuario != $id_usuario) {
			$priv = 'ocultar';
		} else {
			$priv = '';
		}

		$msg = str_replace('"', "**", $msg);
		$msgF = mb_strimwidth($msg, 0, 50, "...");

		echo <<<HTML
		<input type="text" id="texto_anot_{$id}" value="{$msg}" style="display:none">

<tr class="{$priv}">
<td>{$dataF}</td>
<td>{$titulo}</td>
<td>{$nome_usuario}</td>
<td>{$mostrar_home}</td>
<td>{$privado}</td>
<td>
	<a class="btn btn-info-light btn-sm" href="#" onclick="editar('{$id}','{$titulo}','{$usuario}','{$mostrar_home}','{$privado}')" title="Editar Dados"><i class="fa fa-edit"></i></a>

	<a class="btn btn-warning-light btn-sm" href="#" onclick="mostrar('{$id}','{$titulo}','{$dataF}','{$nome_usuario}','{$mostrar_home}','{$privado}')" title="Mostrar Dados"><i class="fa-regular fa-eye"></i></a>

	<big><a href="#" class="btn btn-danger-light btn-sm" onclick="excluir('{$id}')" title="Excluir"><i class="fa fa-trash-can"></i></a></big>

	<form method="POST" class="btn btn-dark-light btn-sm" action="rel/anotacoes_class.php" target="_blank" style="display:inline-block">
		<input type="hidden" name="id" value="{$id}">
		<button title="Imprimir" style="background:transparent; border:none; margin:0; padding:0"><i class="fa fa-file-pdf-o "></i></button>
	</form>



</td>
</tr>
HTML;
	}
} else {
	echo '<small>Não possui nenhuma anotação!</small>';
}


echo <<<HTML
</tbody>
<small><div align="center" id="mensagem-excluir"></div></small>
</table>
HTML;
?>



<script type="text/javascript">
	$(document).ready(function () {
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
	function editar(id, titulo, usuario, mostrar_home, privado) {
		// Obtém o texto do campo de anotação
		var msg = $('#texto_anot_' + id).val();

		// Substitui todas as ocorrências de '**' por '"'
		msg = msg.replace(/\*\*/g, '"');

		// Atualiza os campos do formulário
		$('#titulo_inserir').text('Editar Registro');
		$('#id').val(id);
		$('#titulo').val(titulo);
		$('#usuario').val(usuario);
		$('#mostrar_home').val(mostrar_home);
		$('#privado').val(privado);

		// Atualiza o conteúdo do editor Quill
		quill.root.innerHTML = msg; // Define o conteúdo no editor
		// Sincroniza o conteúdo com o textarea oculto
		document.getElementById('hiddenTextarea').value = msg;

		// Mostra a aba de edição
		$('#nav-tab button[data-bs-target="#nav-profile"]').tab('show');
	}



	function mostrar(id, titulo, data, usuario, mostrar_home, privado) {
		var msg = $('#texto_anot_' + id).val();

		for (let letra of msg) {
			if (letra === '*') {
				msg = msg.replace('**', '"');
			}
		}

		$('#id_dados').text(id);
		$('#titulo_dados').text(titulo);
		$('#data_dados').text(data);
		$('#mensagem_dados').html(msg);
		$('#usuario_dados').text(usuario);
		$('#mostrar_dados').text(mostrar_home);
		$('#privado_dados').text(privado);
		$('#modalDados').modal('show');
	}


	function limparCampos() {
		$('#id').val('');
		$('#titulo').val('');
		$('#msg').val('');
		$('#usuario').val('');
		$('#mostrar_home').val('Não').change();
		$('#privado').val('Não').change();

		// Limpa o conteúdo do editor Quill
		quill.root.innerHTML = '';
		// Limpa os textareas ocultos
		document.getElementById('hiddenTextarea').value = ''; // Limpa o primeiro textarea oculto

		$('#ids').val('');
		$('#btn-deletar').hide();
	}
</script>