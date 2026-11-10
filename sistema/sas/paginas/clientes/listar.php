<?php
@session_start();
$mostrar_registros = @$_SESSION['registros'];
$id_usuario = @$_SESSION['id'];

$tabela = 'empresas';
require_once("../../../conexao.php");

if ($mostrar_registros == 'Não') {
	$query = $pdo->query("SELECT * from $tabela where usuario = '$id_usuario' order by id desc");
} else {
	$query = $pdo->query("SELECT * from $tabela order by id desc");
}

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
	<th >Telefone</th>
	<th >Email</th>
	<th >Ativo</th>
	<th >Tipo Pessoa</th>
	<th >Data Cadastro</th>
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
		$endereco = $res[$i]['endereco'];
		$tipo_pessoa = $res[$i]['tipo_pessoa'];
		$cpf = $res[$i]['cpf'];
		$numero = $res[$i]['numero'];
		$bairro = $res[$i]['bairro'];
		$cidade = $res[$i]['cidade'];
		$estado = $res[$i]['estado'];
		$cep = $res[$i]['cep'];
		$data_cad = $res[$i]['data_cad'];
		$data_nasc = $res[$i]['data_nasc'];
		$complemento = $res[$i]['complemento'];
		$dias_teste = $res[$i]['dias_teste'];
		$mensalidade = $res[$i]['mensalidade'];
		$ativo = $res[$i]['ativo'];
		$plano = $res[$i]['plano'];
		$url_site = $res[$i]['url_site'];
		$frequencia = $res[$i]['frequencia'];
		$dispositivos = $res[$i]['dispositivos'];

		$data_cadF = implode('/', array_reverse(@explode('-', $data_cad)));
		$data_nascF = implode('/', array_reverse(@explode('-', $data_nasc)));

		$tel_whatsF = '55' . preg_replace('/[ ()-]+/', '', $telefone);

		//verificar débitos cliente
		$query2 = $pdo->query("SELECT * from receber_sas where cliente = '$id' and vencimento < curDate() and pago != 'Sim'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$linhas2 = @count($res2);
		if ($linhas2 > 0) {
			$debito2 = '#ffdddd';
			$debito = 'text-danger';
		} else {
			$debito2 = '';
			$debito = '';
		}

		if ($telefone == '' || $telefone == "Sem Registro") {
			$ocultar_whats = 'ocultar';
		} else {
			$ocultar_whats = '';
		}


		if ($tipo_pessoa == 'Física') {
			$cor_adm = 'bg-success-transparent text-success';
		} else {
			$cor_adm = 'bg-danger-transparent text-danger';
		}


		if ($ativo == 'Sim') {
			$icone_at = 'fa-check-square';
			$titulo_link = 'Desativar Usuário';
			$acao = 'Não';
			$classe_ativo = '';
			$cor_ativo = 'bg-success';
		} else {
			$icone_at = 'fa-square-o';
			$titulo_link = 'Ativar Usuário';
			$acao = 'Sim';
			$classe_ativo = '#c4c4c4';
			$cor_ativo = 'bg-danger';
		}


//buscar imagens da empresa no config
$query2 = $pdo->query("SELECT logo, icone, logo_rel, logo_painel FROM config WHERE empresa = '$id' LIMIT 1");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);

if (@count($res2) > 0) {
	$logo = $res2[0]['logo'];
	$icone = $res2[0]['icone'];
	$logo_rel = $res2[0]['logo_rel'];
	$logo_painel = $res2[0]['logo_painel'];
} else {
	$logo = '';
	$icone = '';
	$logo_rel = '';
	$logo_painel = '';
}

if ($logo == "" || !file_exists("../../../img/".$logo)) {
	$logo = 'sem-foto.png';
}

if ($icone == "" || !file_exists("../../../img/".$icone)) {
	$icone = 'sem-foto.png';
}

if ($logo_rel == "" || !file_exists("../../../img/".$logo_rel)) {
	$logo_rel = 'sem-foto.png';
}

if ($logo_painel == "" || !file_exists("../../../img/".$logo_painel)) {
	$logo_painel = 'sem-foto.png';
}


		$query2 = $pdo->query("SELECT * FROM planos where id = '$plano'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$total_reg2 = @count($res2);
		if ($total_reg2 > 0) {
		    $nome_plano = $res2[0]['nome'];
		}else{
			$nome_plano = "";
		}

		if($dias_teste > 0){
			$mostrar_dias_teste = ' <span style="color:red">('.$dias_teste.')<span>';
		}else{
			$mostrar_dias_teste = '';
		}


		$query2 = $pdo->query("SELECT * FROM frequencias where dias = '$frequencia'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$total_reg2 = @count($res2);
		if ($total_reg2 > 0) {
		    $nome_freq = $res2[0]['frequencia'];
		}else{
			$nome_freq = "";
		}


		$nomeF = mb_strimwidth($nome, 0, 40, "...");

		echo <<<HTML
<tr style="background: {$debito2}">
<td align="center">
<div class="custom-checkbox custom-control">
<input type="checkbox" class="custom-control-input" id="seletor-{$id}" onchange="selecionar('{$id}')">
<label for="seletor-{$id}" class="custom-control-label mt-1 text-dark"></label>
</div>
</td>
<td style="color:{$classe_ativo}" class="{$debito}"> <a title="Acessar Empresa" style="text-decoration: underline;" href="#" onclick="abrirEmpresa('{$id}')">{$nomeF}</a> {$mostrar_dias_teste}</td>
<td style="color:{$classe_ativo}">{$telefone}</td>
<td style="color:{$classe_ativo}">{$email}</td>
<td><span class="badge font-weight-semibold {$cor_ativo} tx-12" style="width:90%;">{$ativo}</span></td>
<td><span class="badge font-weight-semibold {$cor_adm} tx-12" style="width:90%;">{$tipo_pessoa}</span></td>
<td style="color:{$classe_ativo}">{$data_cadF}</td>
<td>
<a class="btn btn-info-light btn-sm" class="" href="#" onclick="editar('{$id}','{$nome}','{$email}','{$telefone}','{$endereco}','{$cpf}','{$tipo_pessoa}','{$data_nascF}','{$numero}','{$bairro}','{$cidade}','{$estado}','{$cep}','{$complemento}','{$dias_teste}','{$mensalidade}','{$logo}','{$icone}','{$logo_rel}','{$logo_painel}','{$plano}','{$url_site}','{$frequencia}','{$dispositivos}')" title="Editar Dados"><i class="fa fa-edit text-info"></i></a>

<a class="btn btn-danger-light btn-sm" href="#" onclick="excluir('{$id}')" title="Excluir"><i class="fa fa-trash-can text-danger"></i></a>

<a class="btn btn-warning-light btn-sm" href="#" onclick="mostrar('{$nome}','{$email}','{$telefone}','{$endereco}', '{$data_cadF}','{$cpf}','{$tipo_pessoa}','{$data_nascF}','{$numero}','{$bairro}','{$cidade}','{$estado}','{$cep}','{$complemento}','{$nome_plano}','{$nome_freq}','{$dispositivos}')" title="Mostrar Dados"><i class="fa fa-eye"></i></a>

<a class="btn btn-success-light btn-sm" href="#" onclick="mostrarContas('{$nome}','{$id}')" title="Mostrar Contas"><i style="color: green" class="fa fa-hand-holding-dollar"></i></a>

<a class="btn btn-dark-light btn-sm" href="#" onclick="arquivo('{$id}', '{$nome}')" title="Inserir / Ver Arquivos"><i class="fa fa-file-o taxt-secondary"></i></a>

<a class="btn btn-success-light btn-sm" href="#" onclick="ativar('{$id}', '{$acao}')" title="{$titulo_link}"><i class="fa {$icone_at} "></i></a>


<a class="{$ocultar_whats} btn btn-success-light btn-sm" class="" href="http://api.whatsapp.com/send?1=pt_BR&phone={$tel_whatsF}" title="Whatsapp" target="_blank"><i style="color: green" class="bi bi-whatsapp"></i></i></a>

<a class="btn btn-primary-light btn-sm"  href="#" onclick="permissoes('{$id}', '{$nome}')" title="Dar Permissões Recursos"><i class="fa fa-lock "></i></a>


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
HTML;
?>



<script type="text/javascript">
	$(document).ready(function() {

		 if ($.fn.DataTable.isDataTable('#tabela')) {
        $('#tabela').DataTable().destroy();
    }
    
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
	function editar(id, nome, email, telefone, endereco, cpf, tipo_pessoa, data_nasc, numero, bairro, cidade, estado, cep, complemento, dias_teste, mensalidade, logo, icone, logo_rel, logo_painel, plano, url_site, frequencia, dispositivos) {
		$('#mensagem').text('');
		$('#titulo_inserir').text('Editar Registro');

		$('#id').val(id);
		$('#nome').val(nome);
		$('#email').val(email);
		$('#telefone').val(telefone);
		$('#endereco').val(endereco);
		$('#cpf').val(cpf);
		$('#tipo_pessoa').val(tipo_pessoa).change();
		$('#data_nasc').val(data_nasc);
		$('#plano').val(plano);

		$('#frequencia').val(frequencia);
		$('#dispositivos').val(dispositivos);

		$('#numero').val(numero);
		$('#bairro').val(bairro);
		$('#cidade').val(cidade);
		$('#estado').val(estado).change();
		$('#cep').val(cep);
		$('#complemento').val(complemento);
		$('#url_site').val(url_site);

		$('#dias_teste').val(dias_teste);
		$('#mensalidade').val(mensalidade);	
		mascaraMoeda(document.getElementById('mensalidade'));	

		var cache = '?v=' + new Date().getTime();

$('#target-logo-emp').attr("src", "../img/" + logo + cache);
$('#target-icone-emp').attr("src", "../img/" + icone + cache);
$('#target-logo-rel-emp').attr("src", "../img/" + logo_rel + cache);
$('#target-painel-emp').attr("src", "../img/" + logo_painel + cache);
		


		$('#modalForm').modal('show');
	}


	function mostrar(nome, email, telefone, endereco, data_cad, cpf, tipo_pessoa, data_nasc, numero, bairro, cidade, estado, cep, complemento, nome_plano, frequencia, dispositivos) {

		$('#titulo_dados').text(nome);
		$('#email_dados').text(email);
		$('#telefone_dados').text(telefone);
		$('#endereco_dados').text(endereco);
		$('#cpf_dados').text(cpf);
		$('#data_dados').text(data_cad);
		$('#pessoa_dados').text(tipo_pessoa);
		$('#data_nasc_dados').text(data_nasc);

		$('#numero_dados').text(numero);
		$('#bairro_dados').text(bairro);
		$('#cidade_dados').text(cidade);
		$('#estado_dados').text(estado);
		$('#cep_dados').text(cep);
		$('#complemento_dados').text(complemento);
		$('#nome_plano_dados').text(nome_plano);

		$('#nome_frequencia_dados').text(frequencia);
		$('#nome_dispositivos_dados').text(dispositivos);

		$('#modalDados').modal('show');
	}

	function limparCampos() {
		$('#id').val('');
		$('#nome').val('');
		$('#email').val('');
		$('#telefone').val('');
		$('#endereco').val('');
		$('#cpf').val('');
		$('#tipo_pessoa').val('Jurídica').change();
		$('#data_nasc').val('');
		$('#numero').val('');
		$('#bairro').val('');
		$('#cidade').val('');
		$('#estado').val('').change();
		$('#cep').val('');
		$('#complemento').val('');
		$('#plano').val('').change();
		$('#url_site').val('');

		$('#frequencia').val('30');
		$('#dispositivos').val('1');

		$('#dias_teste').val('');
		$('#mensalidade').val('');
		
		$('#data_pgto').val('');

		$('#target-logo-emp').attr("src", "../img/sem-foto.png");
		$('#target-icone-emp').attr("src", "../img/sem-foto.png");
		$('#target-logo-rel-emp').attr("src", "../img/sem-foto.png");
		$('#target-painel-emp').attr("src", "../img/sem-foto.png");

		// Limpar inputs de arquivos
		$('#foto-logo').val('');
		$('#foto-logo-rel').val('');
		$('#foto-icone').val('');
		$('#foto-painel').val('');

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



	function mostrarContas(nome, id) {

		$('#titulo_contas').text(nome);
		$('#id_contas').val(id);

		$('#modalContas').modal('show');
		listarDebitos(id);

	}


	function listarDebitos(id) {

		$.ajax({
			url: 'paginas/' + pag + "/listar_debitos.php",
			method: 'POST',
			data: {
				id
			},
			dataType: "html",

			success: function(result) {
				$("#listar_debitos").html(result);
			}
		});
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



		function permissoes(id, nome) {

		$('#id_permissoes').val(id);
		$('#nome_permissoes').text(nome);

		$('#modalPermissoes').modal('show');
		listarPermissoes(id);
	}

</script>


<script type="text/javascript">
	 function abrirEmpresa(id) {
           $.ajax({
               url: 'pegar_sessao.php',
               method: 'POST',
               data: {id},
               dataType: "html",
               success: function(result) {
                   window.location = "../painel";
               }
           });
       }
</script>