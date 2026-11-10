<?php
@session_start();
require_once("../../../conexao.php");
$tabela = 'chamados';
$id_usuario = @$_SESSION['id'];


$status = @$_POST['p1'];

if($status == ""){
	$status = 'Aberto';
}

$query = $pdo->query("SELECT * from $tabela where status = '$status' order by id desc");
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
	<th>Empresa</th>		
	<th>Título</th>
	<th>Status</th>
	<th>Respondida</th>
	<th>Ações</th>
	</tr> 
	</thead> 
	<tbody>	
HTML;

	for ($i = 0; $i < $linhas; $i++) {
		$id = @$res[$i]['id'];
		$titulo = @$res[$i]['titulo'];
		$texto = @$res[$i]['texto'];		
		$data = @$res[$i]['data'];
		$status = @$res[$i]['status'];
		$respondido = @$res[$i]['respondido'];
		$empresa = @$res[$i]['empresa'];

		$query2 = $pdo->query("SELECT * from empresas where id = '$empresa'");
													$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
													$nome_empresa_chamado = @$res2[0]['nome'];
													$telefone = @$res2[0]['telefone'];
													$tel_whatsF = '55' . @preg_replace('/[ ()-]+/', '', $telefone);

		
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

		if ($status == 'Fechado') {
			$icone = 'fa-check-square';
			$titulo_link = 'Voltar para Aberto';
			$acao = 'Aberto';
			$classe_ativo = '';
		} else {
			$icone = 'fa-square-o';
			$titulo_link = 'Fechar o Chamado';
			$acao = 'Fechado';
			$classe_ativo = '#c4c4c4';
		}


		$textoF = rawurlencode($texto);

$mostrar_arquivo = 'ocultar';
$query2 = $pdo->query("SELECT * FROM arquivos where id_reg = '$id' and registro = 'Chamado'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$total_reg2 = @count($res2);
if($total_reg2 > 0){
	$mostrar_arquivo = '';
}


echo <<<HTML

<tr class="">
<td>{$id}</td>
<td>{$dataF}</td>
<td>{$nome_empresa_chamado}</td>
<td>{$titulo}</td>
<td><span class="badge font-weight-semibold {$cor_status} tx-12" style="width:90%;">{$status}</span></td>
<td><span class="badge font-weight-semibold {$cor_respondido} tx-12" style="width:90%;">{$respondido}</span></td>

<td>
	
	<big><a href="#" class="btn btn-danger-light btn-sm" onclick="excluir('{$id}')" title="Excluir"><i class="fa fa-trash-can"></i></a></big>

		<a class="btn btn-success-light btn-sm" href="#" onclick="ativar('{$id}', '{$acao}')" title="{$titulo_link}"><i class="fa {$icone} "></i></a>


		<a class=" btn btn-success-light btn-sm" class="" href="http://api.whatsapp.com/send?1=pt_BR&phone={$tel_whatsF}" title="Whatsapp" target="_blank"><i style="color: green" class="bi bi-whatsapp"></i></i></a>

		<a class="btn btn-info btn-sm" href="#" onclick="respostas('{$id}','{$titulo}','{$nome_empresa_chamado}','{$textoF}')" title="Respostas Chamado"><i class="fa-regular fa-comment"></i></a>


		<a class="btn btn-dark-light btn-sm {$mostrar_arquivo}" href="#" onclick="arquivo('{$id}', '{$titulo}')" title="Inserir / Ver Arquivos"><i class="fa fa-file-o taxt-secondary"></i></a>



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
	function respostas(id, titulo, empreso, texto) {
		
		$('#id_resposta').val(id);
		$('#titulo_resposta').text(titulo);
		$('#empresa_resposta').text(empreso);

		$('#empresa_texto').html(decodeURIComponent(texto));
		
		
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