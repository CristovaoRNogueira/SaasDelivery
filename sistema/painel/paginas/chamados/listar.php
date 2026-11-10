<?php
@session_start();
$id_empresa = @$_SESSION['empresa'];
$tabela = 'chamados';
$id_usuario = @$_SESSION['id'];

require_once("../../../conexao.php");
require_once("../../verificar.php");
require_once("../../buscar_config.php");

$query = $pdo->query("SELECT * from $tabela where empresa = '$id_empresa' order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	echo <<<HTML
<small>
	<table class="table table-hover table-bordered text-nowrap border-bottom dt-responsive" id="tabela">
	<thead> 
	<tr>
	<th>ID</th>
	<th>Data</th>		
	<th>Título</th>
	<th>Status</th>
	<th>Respondida</th>
	<th>Ações</th>
	</tr> 
	</thead> 
	<tbody>	
HTML;

	for ($i = 0; $i < $linhas; $i++) {
		$id = $res[$i]['id'];
		$titulo = $res[$i]['titulo'];
		$texto = $res[$i]['texto'];		
		$data = $res[$i]['data'];
		$status = $res[$i]['status'];
		$respondido = $res[$i]['respondido'];
		
		
		$dataF = implode('/', array_reverse(@explode('-', $data)));

		$textoF = rawurlencode($texto);

		if($status == 'Aberto'){
			$cor_status = 'bg-danger-transparent text-danger';
		}else{
			$cor_status = 'bg-success-transparent text-success';
		}


		if($respondido == 'Não'){
			$cor_respondido = 'bg-danger-transparent text-danger';
		}else{
			$cor_respondido = 'bg-success-transparent text-success';
		}




echo <<<HTML

<tr class="">
<td>{$id}</td>
<td>{$dataF}</td>
<td>{$titulo}</td>
<td><span class="badge font-weight-semibold {$cor_status} tx-12" style="width:90%;">{$status}</span></td>
<td><span class="badge font-weight-semibold {$cor_respondido} tx-12" style="width:90%;">{$respondido}</span></td>

<td>
	<a class="btn btn-info-light btn-sm" href="#" onclick="editar('{$id}','{$titulo}','{$textoF}')" title="Editar Dados"><i class="fa fa-edit"></i></a>

	
	<big><a href="#" class="btn btn-danger-light btn-sm" onclick="excluir('{$id}')" title="Excluir"><i class="fa fa-trash-can"></i></a></big>


	<a class="btn btn-info btn-sm" href="#" onclick="respostas('{$id}','{$titulo}')" title="Respostas Chamado"><i class="fa-regular fa-comment"></i></a>


	<a class="btn btn-dark-light btn-sm" href="#" onclick="arquivo('{$id}', '{$titulo}')" title="Inserir / Ver Arquivos"><i class="fa fa-file-o taxt-secondary"></i></a>


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
	function editar(id, titulo, texto) {
		

		
		// Atualiza os campos do formulário
		$('#titulo_inserir').text('Editar Registro');
		$('#id').val(id);
		$('#titulo').val(titulo);
		
		// Atualiza o conteúdo do editor Quill
		quill.root.innerHTML = decodeURIComponent(texto); // Define o conteúdo no editor
		// Sincroniza o conteúdo com o textarea oculto
		document.getElementById('hiddenTextarea').value = texto;

		// Mostra a aba de edição
		$('#nav-tab button[data-bs-target="#nav-profile"]').tab('show');
	}



	


	function limparCampos() {
		$('#id').val('');
		$('#titulo').val('');		
		// Limpa o conteúdo do editor Quill
		quill.root.innerHTML = '';
		// Limpa os textareas ocultos
		document.getElementById('hiddenTextarea').value = ''; // Limpa o primeiro textarea oculto

		$('#ids').val('');
		$('#btn-deletar').hide();
	}
</script>

<script type="text/javascript">
	function respostas(id, titulo) {
		
		$('#id_resposta').val(id);
		$('#titulo_resposta').text(titulo);
		
		
		$('#texto_resposta').val('');

		listarRespostas()
		$('#modalResposta').modal('show');

		
	}

	function arquivo(id, nome) {
		$('#id-arquivo').val(id);
		$('#nome-arquivo').text(nome);
		$('#modalArquivos').modal('show');
		$('#mensagem-arquivo').text('');
		$('#arquivo_conta').val('');
		listarArquivos();
	}
</script>