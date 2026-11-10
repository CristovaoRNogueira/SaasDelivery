<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
$tabela = 'mesas';

$query = $pdo->query("SELECT * FROM $tabela where empresa = '$id_empresa' ORDER BY id asc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);

if ($total_reg > 0) {

	echo <<<HTML
	<small>
	<table class="table table-hover table-bordered text-nowrap border-bottom dt-responsive" id="tabela">
	<thead> 
	<tr> 
		<th align="center" width="5%" class="text-center">Selecionar</th>
	<th class="width60">Nome</th>			
	
	<th>Ativ</th>
	<th>Status</th>
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
		$status = $res[$i]['status'];

		if ($ativo == 'Sim') {
			$icone = 'fa-check-square';
			$titulo_link = 'Tornar Indisponível';
			$acao = 'Não';
			$classe_linha = '';
		} else {
			$icone = 'fa-square-o';
			$titulo_link = 'Mesa Disponível';
			$acao = 'Sim';
			$classe_linha = '#c4c4c4';
		}


		$selected_aberta = '';
		if($status == 'Aberta'){
			$selected_aberta = 'selected';
		}

		$selected_fechada = '';
		if($status == 'Fechada'){
			$selected_fechada = 'selected';
		}

		echo <<<HTML
<tr>
<td align="center">
<div class="custom-checkbox custom-control">
<input type="checkbox" class="custom-control-input" id="seletor-{$id}" onchange="selecionar('{$id}')">
<label for="seletor-{$id}" class="custom-control-label mt-1 text-dark"></label>
</div>
</td>
<td style="color:{$classe_linha}">{$nome}</td>
<td style="color:{$classe_linha}">{$ativo}</td>
<td style="color:{$classe_linha}">
<select id="status_{$id}" style="background:transparent; border:none; border-bottom:1px solid #000; outline:none" onchange="editarStatus('{$id}')">
	<option value="Aberta" {$selected_aberta}>Aberta</option>
	<option value="Fechada" {$selected_fechada}>Fechada</option>
</select>
</td>
<td>
			<a class="btn btn-info-light btn-sm" href="#" onclick="editar('{$id}','{$nome}')" title="Editar Dados"><i class="fa fa-edit"></i></a>

		
<big><a href="#" class="btn btn-danger-light btn-sm" onclick="excluir('{$id}')" title="Excluir"><i class="fa fa-trash-can text-danger"></i></a></big>

			<a class="btn btn-success-light btn-sm" href="#" onclick="ativar('{$id}', '{$acao}')" title="{$titulo_link}"><i class="fa {$icone}"></i></a>

			<a class="btn btn-dark btn-sm" href="#" onclick="gerarQrCode('{$id}', '{$nome}')" title="QR Code"><i class="fa fa-qrcode"></i></a>

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
	echo '<small>Não possui nenhum registro Cadastrado!</small>';
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
	function editar(id, nome) {
		$('#id').val(id);
		$('#nome').val(nome);
		$('#titulo_inserir').text('Editar Registro');
		$('#modalForm').modal('show');
	}

	function limparCampos() {
		$('#nome').val('');
		$('#id').val('');
	}


	function editarStatus(id){
		var status = $('#status_'+id).val();		

		$.ajax({
        url: 'paginas/' + pag + "/editar_status.php",
        method: 'POST',
        data: {id, status},
        dataType: "html",

        success:function(result){
        	if(result.trim() === 'Editado'){
        		listar();
        	}else{
        		alertWarning(result)
        	}
            
        }
    });
	}


	function gerarQrCode(id, nome){	


		$('#titulo_qrcode').text(nome);
		$('#modalQRCode').modal('show');	

		$.ajax({
        url: 'paginas/' + pag + "/gerar_qrcode.php",
        method: 'POST',
        data: {id, nome},
        dataType: "html",

        success:function(result){
        	$('#gerar_qrcode').html(result);	           
        }
    });
	}
</script>

