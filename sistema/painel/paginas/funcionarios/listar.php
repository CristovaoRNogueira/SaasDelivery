<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
$tabela = 'usuarios';


$query = $pdo->query("SELECT * from $tabela where nivel != 'Cliente' and nivel != 'Administrador' and empresa = '$id_empresa' order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	echo <<<HTML
<small>
	<table class="table table-hover table-bordered text-nowrap border-bottom dt-responsive" id="tabela">
	<thead> 
	<tr> 
	<th align="center" width="5%" class="text-center">Selecionar</th>
	<th>Nome</th>	
	<th>Telefone</th>	
	<th>Email</th>	
	<th>Acessar Painel</th>
	<th>Nível</th>
	<th class="text-center">Foto</th>	
	<th>Ações</th>
	</tr> 
	</thead> 
	<tbody>	
HTML;

	for ($i = 0; $i < $linhas; $i++) {
		$id = $res[$i]['id'];
		$nome = $res[$i]['nome'];
		$telefone = $res[$i]['telefone'];
		$email = $res[$i]['email'];
		$senha = $res[$i]['senha'];
		$foto = $res[$i]['foto'];
		$nivel = $res[$i]['nivel'];
		$endereco = $res[$i]['endereco'];
		$ativo = $res[$i]['ativo'];
		$data = $res[$i]['data'];
		$pix = $res[$i]['pix'];
		$numero = $res[$i]['numero'];
		$bairro = $res[$i]['bairro'];
		$cidade = $res[$i]['cidade'];
		$estado = $res[$i]['estado'];
		$cep = $res[$i]['cep'];
		$data_nasc = $res[$i]['data_nasc'];
		$acessar_painel = $res[$i]['acessar_painel'];
		$complemento = $res[$i]['complemento'];
		$tipo_chave = $res[$i]['tipo_chave'];

		$dataF = implode('/', array_reverse(@explode('-', $data)));

		$data_nascF = implode('/', array_reverse(@explode('-', $data_nasc)));

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

		if ($nivel == 'Administrador') {
			$senha = '******';
			$cor_adm = 'bg-danger-transparent text-danger';
		} else {
			$cor_adm = 'bg-success-transparent text-success';
		}


		if($nivel == "Entregador" || $nivel == "Motoboy"){
			$classe_nivel = '';
		}else{
			$classe_nivel = 'ocultar';
		}


		echo <<<HTML
<tr >
<td align="center">
<div class="custom-checkbox custom-control">
<input type="checkbox" class="custom-control-input" id="seletor-{$id}" onchange="selecionar('{$id}')">
<label for="seletor-{$id}" class="custom-control-label mt-1 text-dark"></label>
</div>
</td>
<td style="color:{$classe_ativo}">{$nome}</td>
<td style="color:{$classe_ativo}">{$telefone}</td>
<td style="color:{$classe_ativo}">{$email}</td>
<td style="color:{$classe_ativo}">{$acessar_painel}</td>
<td><span class="badge font-weight-semibold {$cor_adm} tx-12" style="width:90%;">{$nivel}</span></td>
<td class="text-center" style="color:{$classe_ativo}"><img alt="avatar" width="30px" height="30px" class="rounded-circle" onclick="mostrar('{$nome}','{$email}','{$telefone}','{$endereco}','{$ativo}','{$dataF}', '{$senha}', '{$nivel}', '{$foto}','{$pix}','{$data_nasc}','{$numero}','{$bairro}','{$cidade}','{$estado}','{$cep}','{$acessar_painel}','{$complemento}','{$tipo_chave}')" src="images/perfil/{$foto}" class="hovv" width="25px"></td>
<td>
	<a class="btn btn-info-light btn-sm" href="#" onclick="editar('{$id}','{$nome}','{$email}','{$telefone}','{$endereco}','{$nivel}','{$pix}','{$data_nasc}','{$numero}','{$bairro}','{$cidade}','{$estado}','{$cep}','{$acessar_painel}','{$complemento}','{$tipo_chave}', '{$foto}')" title="Editar Dados"><i class="fa fa-edit "></i></a>

<a href="#" class="btn btn-danger-light btn-sm" onclick="excluir('{$id}')" title="Excluir"><i class="fa fa-trash-can text-danger"></i></a>

<a class="btn btn-primary-light btn-sm" href="#" onclick="mostrar('{$nome}','{$email}','{$telefone}','{$endereco}','{$ativo}','{$dataF}', '{$senha}', '{$nivel}', '{$foto}','{$pix}','{$data_nasc}','{$numero}','{$bairro}','{$cidade}','{$estado}','{$cep}','{$acessar_painel}','{$complemento}','{$tipo_chave}')" title="Mostrar Dados"><i class="fa fa-info-circle "></i></a>


<a class="btn btn-success-light btn-sm" href="#" onclick="ativar('{$id}', '{$acao}')" title="{$titulo_link}"><i class="fa {$icone} "></i></a>


<a class="btn btn-dark-light btn-sm" href="#" onclick="arquivo('{$id}', '{$nome}')" title="Inserir / Ver Arquivos"><i class="fa fa-file-o"></i></a>


<a class="{$classe_nivel} btn btn-success btn-sm" href="#" onclick="entregas('{$id}', '{$nome}')" title="Ver Entregas Pendentes"><i class="fa fa-usd"></i></a>

</td>
</tr>
HTML;
	}
} else {
	echo 'Não possui nenhum cadastro!';
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
	function editar(id, nome, email, telefone, endereco, nivel, pix, data_nasc, numero, bairro, cidade, estado, cep, acessar_painel, complemento, tipo_chave, foto) {
		$('#mensagem').text('');
		$('#titulo_inserir').text('Editar Registro');

		$('#id').val(id);
		$('#nome').val(nome);
		$('#email').val(email);
		$('#telefone').val(telefone);
		$('#endereco').val(endereco);
		$('#nivel').val(nivel).change();
		$('#pix').val(pix);
		$('#numero').val(numero);
		$('#bairro').val(bairro);
		$('#cidade').val(cidade);
		$('#estado').val(estado).change();
		$('#cep').val(cep);
		$('#data_nasc').val(data_nasc);
		$('#complemento').val(complemento);
		$('#acessar_painel').val(acessar_painel).change();
		$('#tipo_chave').val(tipo_chave).change();

		$('#target').attr("src", "images/perfil/" + foto);

		$('#modalForm').modal('show');
	}


	function mostrar(nome, email, telefone, endereco, ativo, data, senha, nivel, foto, pix, data_nasc, numero, bairro, cidade, estado, cep, acessar_painel, complemento, tipo_chave) {

		if (pix != '') {
			$('#pix_dados').text(tipo_chave + ': ' + pix);
		} else {
			$('#pix_dados').text(tipo_chave);
		}

		$('#titulo_dados').text(nome);
		$('#email_dados').text(email);
		$('#telefone_dados').text(telefone);
		$('#endereco_dados').text(endereco);
		$('#ativo_dados').text(ativo);
		$('#data_dados').text(data);
		$('#senha_dados').text(senha);
		$('#nivel_dados').text(nivel);
		$('#data_nasc_dados').text(data_nasc);
		$('#numero_dados').text(numero);
		$('#bairro_dados').text(bairro);
		$('#cidade_dados').text(cidade);
		$('#estado_dados').text(estado);
		$('#cep_dados').text(cep);
		$('#complemento_dados').text(complemento);
		$('#acessar_painel_dados').text(acessar_painel);

		$('#foto_dados').attr("src", "images/perfil/" + foto);

		$('#modalDados').modal('show');
	}

	function limparCampos() {
		$('#id').val('');
		$('#nome').val('');
		$('#email').val('');
		$('#telefone').val('');
		$('#endereco').val('');
		$('#pix').val('');
		$('#data_nasc').val('');
		$('#numero').val('');
		$('#bairro').val('');
		$('#cidade').val('');
		$('#estado').val('').change();
		$('#tipo_chave').val('').change();
		$('#cep').val('');
		$('#complemento').val('');

		$('#target').attr("src", "images/perfil/sem-foto.jpg");

		$('#ids').val('');
		$('#btn-deletar').hide();
	}



	function arquivo(id, nome) {
		$('#id-arquivo').val(id);
		$('#nome-arquivo').text(nome);
		$('#modalArquivos').modal('show');
		$('#mensagem-arquivo').text('');
		$('#arquivo_conta').val('');
		listarArquivos();
	}

	function entregas(id, nome){
		$('#id_entrega').val(id);
		$('#nome_entrega').text(nome);

		listarEntregas(id);
		$('#modalEntregas').modal('show');
	}
</script>