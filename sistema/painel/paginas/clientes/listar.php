<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
$mostrar_registros = @$_SESSION['registros'];
$id_usuario = @$_SESSION['id'];

$tabela = 'clientes';


if ($mostrar_registros == 'Não') {
	$query = $pdo->query("SELECT * from $tabela where usuario = '$id_usuario' and empresa = '$id_empresa' order by id desc");
} else {
	$query = $pdo->query("SELECT * from $tabela where empresa = '$id_empresa' order by id desc");
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
	<th class="width40">Nome</th>
	<th >Telefone</th>
	<th >Email</th>
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
		$cpf = $res[$i]['cpf'];
		$numero = $res[$i]['numero'];
		$bairro = $res[$i]['bairro'];
		$cidade = $res[$i]['cidade'];
		$estado = $res[$i]['estado'];
		$cep = $res[$i]['cep'];
		$data_cad = $res[$i]['data_cad'];
		$data_nasc = $res[$i]['data_nasc'];
		$complemento = $res[$i]['complemento'];
		$referencia = $res[$i]['referencia'] ?? '';
		$marketing = $res[$i]['marketing'];
		$cartoes = $res[$i]['cartoes'];
		$observacao = $res[$i]['observacao'] ?? '';

		$data_cadF = implode('/', array_reverse(@explode('-', $data_cad)));
		$data_nascF = implode('/', array_reverse(@explode('-', $data_nasc)));

		$tel_whatsF = '55' . preg_replace('/[ ()-]+/', '', $telefone);

		//verificar débitos cliente
		$query2 = $pdo->query("SELECT * from receber where cliente = '$id' and vencimento < curDate() and pago != 'Sim' and (arquivado IS NULL or arquivado != 'Sim')");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$linhas2 = @count($res2);
		if ($linhas2 > 0) {
			$debito2 = 'table-danger';
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

		if ($marketing == 'Não') {
			$ocultar_mark = 'ocultar';
		} else {
			$ocultar_mark = '';
		}

		$nomeF = rawurlencode($nome);
		$enderecoF = rawurlencode($endereco);
		$observacaoF = rawurlencode($observacao);
		$referenciaF = rawurlencode($referencia);

		$mostrar_cartao = '';
		if($total_cartoes_config == 0){
			$mostrar_cartao = 'ocultar';
		}

		echo <<<HTML
<tr class="{$debito2}">
<td align="center">
<div class="custom-checkbox custom-control">
<input type="checkbox" class="custom-control-input" id="seletor-{$id}" onchange="selecionar('{$id}')">
<label for="seletor-{$id}" class="custom-control-label mt-1 text-dark"></label>
</div>
</td>
<td class="{$debito}"> {$nome}</td>
<td>{$telefone}</td>
<td>{$email}</td>
<td>
<a class="btn btn-info-light btn-sm" href="#" onclick="editar('{$id}','{$nomeF}','{$email}','{$telefone}','{$enderecoF}','{$cpf}','{$data_nasc}','{$numero}','{$bairro}','{$cidade}','{$estado}','{$cep}','{$complemento}','{$referenciaF}','{$observacaoF}')" title="Editar Dados"><i class="fa fa-edit"></i></a>

<big><a class="btn btn-danger-light btn-sm" href="#" onclick="excluir('{$id}')" title="Excluir"><i class="fa fa-trash-can text-danger"></i></a></big>

<a class="btn btn-primary-light btn-sm" href="#" onclick="mostrar('{$nomeF}','{$email}','{$telefone}','{$enderecoF}', '{$data_cadF}','{$cpf}','{$data_nascF}','{$numero}','{$bairro}','{$cidade}','{$estado}','{$cep}','{$complemento}','{$referenciaF}','{$cartoes}','{$observacaoF}')" title="Mostrar Dados"><i class="fa fa-info-circle"></i></a>

<a class="btn btn-dark-light btn-sm" href="#" onclick="arquivo('{$id}', '{$nome}')" title="Inserir / Ver Arquivos"><i class="fa fa-file-o "></i></a>

<a class="btn btn-success-light btn-sm" href="#" onclick="mostrarContas('{$nome}','{$id}')" title="Mostrar Contas"><i class="fa fa-money"></i></a>

<a class="btn btn-success-light btn-sm {$ocultar_whats}" href="http://api.whatsapp.com/send?1=pt_BR&phone={$tel_whatsF}" title="Whatsapp" target="_blank"><i class="fa-brands fa-whatsapp"></i></a>

<a class="btn btn-info btn-sm {$mostrar_cartao}" href="#" title="Mensagem Cartões" onclick="mensagemMarketing('{$id}', '{$nome}', '{$cartoes}', '{$telefone}')"><i class="fa fa-id-card"></i></a>

<big><a class="btn btn-danger-light btn-sm {$ocultar_mark}" href="#" onclick="excluirMarketing('{$id}')" title="Remover do Marketing Whatsapp"><i class="fa fa-ban text-danger"></i></a></big>

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
	function editar(id, nome, email, telefone, endereco, cpf, data_nasc, numero, bairro, cidade, estado, cep, complemento, referencia, observacao) {
		$('#mensagem').text('');
		$('#titulo_inserir').text('Editar Registro');

		$('#id').val(id);
		$('#nome').val(decodeURIComponent(nome));
		$('#email').val(email);
		$('#telefone').val(telefone);
		$('#endereco').val(decodeURIComponent(endereco));
		$('#cpf').val(cpf);
		$('#data_nasc').val(data_nasc);
		$('#numero').val(numero);
		$('#bairro').val(bairro);
		$('#cidade').val(cidade);
		$('#estado').val(estado).change();
		$('#cep').val(cep);
		$('#complemento').val(complemento);
		$('#referencia').val(decodeURIComponent(referencia));
		$('#observacao').val(decodeURIComponent(observacao));

		$('#modalForm').modal('show');
	}

	function mostrar(nome, email, telefone, endereco, data_cad, cpf, data_nasc, numero, bairro, cidade, estado, cep, complemento, referencia, cartoes, observacao) {

		$('#titulo_dados').text(decodeURIComponent(nome));
		$('#email_dados').text(email);
		$('#telefone_dados').text(telefone);
		$('#endereco_dados').text(decodeURIComponent(endereco));
		$('#cpf_dados').text(cpf);
		$('#data_dados').text(data_cad);
		$('#data_nasc_dados').text(data_nasc);

		$('#numero_dados').text(numero);
		$('#bairro_dados').text(bairro);
		$('#cidade_dados').text(cidade);
		$('#estado_dados').text(estado);
		$('#cep_dados').text(cep);
		$('#complemento_dados').text(complemento);
		$('#referencia_dados').text(decodeURIComponent(referencia));
		$('#cartoes_dados').text(cartoes);
		$('#observacao_dados').text(decodeURIComponent(observacao));

		$('#modalDados').modal('show');
	}

	function limparCampos() {
		$('#id').val('');
		$('#nome').val('');
		$('#email').val('');
		$('#telefone').val('');
		$('#endereco').val('');
		$('#cpf').val('');
		$('#data_nasc').val('');
		$('#numero').val('');
		$('#bairro').val('');
		$('#cidade').val('');
		$('#estado').val('').change();
		$('#cep').val('');
		$('#complemento').val('');
		$('#referencia').val('');
		$('#observacao').val('');

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





function excluirMarketing(id) {
    //$('#mensagem-excluir').text('Excluindo...')

    $('body').removeClass('timer-alert');
    Swal.fire({
        title: "Excluir do Marketing?",
        text: "Ele não receberá mais mensagens!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33', // Cor do botão de confirmação (vermelho)
        cancelButtonColor: '#3085d6', // Cor do botão de cancelamento (azul)
        confirmButtonText: "Sim, Excluir!",
        cancelButtonText: "Cancelar",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {


            $.ajax({
                url: 'paginas/' + pag + "/excluir_marketing.php",
                method: 'POST',
                data: { id },
                dataType: "html",

                success: function (mensagem) {
                    if (mensagem.trim() == "Excluído com Sucesso") {

                        // Ação de exclusão aqui
                        Swal.fire({
                            title: 'Excluido com Sucesso!',
                            text: 'Fecharei em 1 segundo.',
                            icon: "success",
                            timer: 1000
                        })
                        //excluido();
                        listar();
                        limparCampos();


                    } else {
                        $('#mensagem-excluir').addClass('text-danger')
                        $('#mensagem-excluir').text(mensagem)
                    }
                }
            });

        }
    });


};



function mensagemMarketing(id, nome, cartoes, telefone) {

		$.ajax({
			url: 'paginas/' + pag + "/mensagem_marketing.php",
			method: 'POST',
			data: {
				id, nome, cartoes, telefone
			},
			dataType: "html",

			success: function(result) {
				
				alertsucesso('Mensagem Enviada!');
			}
		});
	}

</script>
