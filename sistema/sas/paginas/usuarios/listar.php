<?php
$tabela = 'usuarios';
require_once("../../../conexao.php");
@session_start();
$id_usuario = @$_SESSION['id'];

$query = $pdo->query("SELECT * from $tabela where id != $id_usuario and acessar_painel != 'Não' and (empresa = 0 or nivel = 'Administrador' or empresa is null) order by empresa asc, id desc");
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
	<th>Empresa</th>	
	<th>Telefone</th>	
	<th>Email</th>	
	<th width="10%">Nível</th>	
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
		$mostrar_registros = $res[$i]['mostrar_registros'];
		$numero = $res[$i]['numero'];
		$bairro = $res[$i]['bairro'];
		$cidade = $res[$i]['cidade'];
		$estado = $res[$i]['estado'];
		$cep = $res[$i]['cep'];
		$data_nasc = $res[$i]['data_nasc'];
		$cpf = $res[$i]['cpf'];
		$pix = $res[$i]['pix'];
		$complemento = $res[$i]['complemento'];
		$tipo_chave = $res[$i]['tipo_chave'];
		$empresa = $res[$i]['empresa'];


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

		$mostrar_adm = '';
		if ($nivel == 'Administrador') {
			$senha = '******';
			$mostrar_adm = 'ocultar';
			$cor_adm = 'bg-danger-transparent text-danger';
		} else {
			$cor_adm = 'bg-success-transparent text-success';
		}


		$query2 = $pdo->query("SELECT * from empresas where id = '$empresa'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$total_reg2 = @count($res2);
		if ($total_reg2 > 0) {
			$nome_empresa = $res2[0]['nome'];
		}else{
			$nome_empresa = 'SAAS';
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
<td style="color:{$classe_ativo}">{$nome_empresa}</td>
<td style="color:{$classe_ativo}">{$telefone}</td>
<td style="color:{$classe_ativo}">{$email}</td>
<td><span class="badge font-weight-semibold {$cor_adm} tx-12" style="width:100%;">{$nivel}</span></td>

<td class="text-center" style="color:{$classe_ativo}"><img alt="avatar" width="30px" height="30px" class="rounded-circle" onclick="mostrar('{$nome}','{$email}','{$telefone}','{$endereco}','{$ativo}','{$dataF}', '{$senha}', '{$nivel}', '{$foto}','{$numero}','{$bairro}','{$cidade}','{$estado}','{$cep}','{$data_nascF}','{$cpf}','{$pix}','{$complemento}', '{$tipo_chave}')" class="hovv" src="images/perfil/{$foto}" width="25px"></td>
<td>
<a class="btn btn-info-light btn-sm" href="#" onclick="editar('{$id}','{$nome}','{$email}','{$telefone}','{$endereco}','{$nivel}','{$mostrar_registros}','{$numero}','{$bairro}','{$cidade}','{$estado}','{$cep}','{$data_nasc}','{$cpf}','{$pix}','{$complemento}', '{$tipo_chave}', '{$foto}')" title="Editar Dados"><i class="fa fa-edit "></i></a>


<a href="#" class="btn btn-danger-light btn-sm" onclick="excluir('{$id}')" title="Excluir"><i class="fa fa-trash-can text-danger"></i></a>

<a class="btn btn-warning-light btn-sm" href="#" onclick="mostrar('{$nome}','{$email}','{$telefone}','{$endereco}','{$ativo}','{$dataF}', '{$senha}', '{$nivel}','{$foto}','{$numero}','{$bairro}','{$cidade}','{$estado}','{$cep}','{$data_nascF}','{$cpf}','{$pix}','{$complemento}', '{$tipo_chave}')" title="Mostrar Dados"><i class="fa fa-eye "></i></a>


<a class="btn btn-success-light btn-sm" href="#" onclick="ativar('{$id}', '{$acao}')" title="{$titulo_link}"><i class="fa {$icone} "></i></a>

<a class="btn btn-dark-light btn-sm" href="#" onclick="arquivo('{$id}', '{$nome}')" title="Inserir / Ver Arquivos"><i class="fa fa-file-o " ></i></a>

<a href="#" class="btn btn-danger-light btn-sm" onclick="resetar('{$id}')" title="Redefinir senha para 123"><i class="fa fa-ban text-danger"></i></a>


<a class="btn btn-primary-light btn-sm {$mostrar_adm}"  href="#" onclick="permissoes('{$id}', '{$nome}')" title="Dar Permissões"><i class="fa fa-lock "></i></a>

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
	function editar(id, nome, email, telefone, endereco, nivel, mostrar_registros, numero, bairro, cidade, estado, cep, data_nasc, cpf, pix, complemento, tipo_chave, foto) {
		$('#mensagem').text('');
		$('#titulo_inserir').text('Editar Registro');

		$('#id').val(id);
		$('#nome').val(nome);
		$('#email').val(email);
		$('#telefone').val(telefone);
		$('#endereco').val(endereco);
		$('#nivel').val(nivel).change();
		$('#mostrar_registros').val(mostrar_registros).change();
		$('#numero').val(numero);
		$('#bairro').val(bairro);
		$('#cidade').val(cidade);
		$('#estado').val(estado).change();
		$('#cep').val(cep);
		$('#data_nasc').val(data_nasc);
		$('#cpf').val(cpf);
		$('#pix').val(pix);
		$('#complemento').val(complemento);
		$('#tipo_chave').val(tipo_chave).change();
		$('#target').attr("src", "images/perfil/" + foto);


		$('#modalForm').modal('show');
	}


	function mostrar(nome, email, telefone, endereco, ativo, data, senha, nivel, foto, numero, bairro, cidade, estado, cep, data_nasc, cpf, pix, complemento, tipo_chave) {

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
		$('#nivel_dados').text(nivel);
		$('#data_dados').text(data);
		$('#numero_dados').text(numero);
		$('#bairro_dados').text(bairro);
		$('#cidade_dados').text(cidade);
		$('#estado_dados').text(estado);
		$('#cep_dados').text(cep);
		$('#data_nasc_dados').text(data_nasc);
		$('#cpf_dados').text(cpf);
		$('#complemento_dados').text(complemento);


		$('#foto_dados').attr("src", "images/perfil/" + foto);


		$('#modalDados').modal('show');
	}

	function limparCampos() {
		$('#id').val('');
		$('#nome').val('');
		$('#email').val('');
		$('#telefone').val('');
		$('#endereco').val('');
		$('#numero').val('');
		$('#bairro').val('');
		$('#cidade').val('');
		$('#estado').val('').change();
		$('#cep').val('');
		$('#cpf').val('');
		$('#pix').val('');
		$('#complemento').val('');
		$('#tipo_chave').val('').change();

		$('#target').attr("src", "images/perfil/sem-foto.jpg");


		$('#ids').val('');
		$('#btn-deletar').hide();
	}




	function permissoes(id, nome) {

		$('#id_permissoes').val(id);
		$('#nome_permissoes').text(nome);

		$('#modalPermissoes').modal('show');
		listarPermissoes(id);
	}



	function arquivo(id, nome) {
		$('#id-arquivo').val(id);
		$('#nome-arquivo').text(nome);
		$('#modalArquivos').modal('show');
		$('#mensagem-arquivo').text('');
		$('#arquivo_conta').val('');
		listarArquivos();
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


	

// ALERT REDEFINIR SENHA #######################################
function resetar(id) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success", // Adiciona margem à direita do botão "Sim, Excluir!"
            cancelButton: "btn btn-danger me-1",
            container: 'swal-whatsapp-container'
        },
        buttonsStyling: false
    });

    swalWithBootstrapButtons.fire({
        title: "Deseja Redefinir?",
        text: "A senha agora será 123",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sim, Redefinir!",
        cancelButtonText: "Não, Cancelar!",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Realiza a requisição AJAX para excluir o item
            $.ajax({
                url: 'paginas/' + pag + "/redefinir.php",
                method: 'POST',
                data: { id },
                dataType: "html",
                success: function (mensagem) {
                    if (mensagem.trim() == "Redefinido com Sucesso") {
                        // Exibe mensagem de sucesso após a exclusão
                        swalWithBootstrapButtons.fire({
                            title: mensagem,
                            text: 'Fecharei em 1 segundo.',
                            icon: "success",
                            timer: 1000,
                            timerProgressBar: true,
                            confirmButtonText: 'OK',
                            customClass: {
                             container: 'swal-whatsapp-container'
                             }
                        });
                        listar();
                        
                    } else {
                        // Exibe mensagem de erro se a requisição falhar
                        swalWithBootstrapButtons.fire({
                            title: "Opss!",
                            text: mensagem,
                            icon: "error",
                            confirmButtonText: 'OK',
                            customClass: {
                             container: 'swal-whatsapp-container'
                             }
                        });
                    }
                }
            });
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