<?php
@session_start();
require_once("../../../conexao.php");

$id_usuario = @$_SESSION['id'];
$tabela = 'tarefas_sas';



$data_hoje = date('Y-m-d');
$data_atual = date('Y-m-d');
$mes_atual = Date('m');
$ano_atual = Date('Y');
$data_inicio_mes = $ano_atual . "-" . $mes_atual . "-01";
$data_inicio_ano = $ano_atual . "-01-01";

$data_ontem = date('Y-m-d', @strtotime("-1 days", @strtotime($data_atual)));
$data_amanha = date('Y-m-d', @strtotime("+1 days", @strtotime($data_atual)));


if ($mes_atual == '04' || $mes_atual == '06' || $mes_atual == '09' || $mes_atual == '11') {
	$data_final_mes = $ano_atual . '-' . $mes_atual . '-30';
} else if ($mes_atual == '02') {
	$bissexto = date('L', @mktime(0, 0, 0, 1, 1, $ano_atual));
	if ($bissexto == 1) {
		$data_final_mes = $ano_atual . '-' . $mes_atual . '-29';
	} else {
		$data_final_mes = $ano_atual . '-' . $mes_atual . '-28';
	}
} else {
	$data_final_mes = $ano_atual . '-' . $mes_atual . '-31';
}

$dataInicial = @$_POST['p2'];
$dataFinal = @$_POST['p3'];
$filtro = @$_POST['p1'];
$usuario = @$_POST['p5'];
$data = @$_POST['p6'];
$usuario_selecionado = @$_POST['p4'];

if (empty($usuario_selecionado)) {
	$usuario_selecionado = $id_usuario;
}

if ($dataInicial == "") {
	$dataInicial = $data_inicio_mes;
}

if ($dataFinal == "") {
	$dataFinal = $data_final_mes;
}


if ($data == "") {
	$data = $data_atual;
}

if ($usuario == "") {
	$usuario = $id_usuario;
}




$query = $pdo->query("SELECT * from $tabela where usuario = '$usuario_selecionado' and status = 'Agendada' and data <= curDate() and hora < curTime() order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$tarefas_atrasadas = @count($res);

$query = $pdo->query("SELECT * from $tabela where usuario = '$usuario_selecionado' and status = 'Agendada' order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$tarefas_pendentes = @count($res);

$query = $pdo->query("SELECT * from $tabela where usuario = '$usuario_selecionado' and status = 'Agendada' and data = '$data_atual' order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$tarefas_hoje = @count($res);

$query = $pdo->query("SELECT * from $tabela where usuario = '$usuario_selecionado' and status = 'Concluída' and data >= '$dataInicial' and data <= '$dataFinal' order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$tarefas_concluidas = @count($res);


if ($filtro == 'Atrasadas') {
	$query = $pdo->query("SELECT * from $tabela where usuario = '$usuario_selecionado' and status = 'Agendada' and data <= curDate() and hora < curTime() order by hora asc");
} else if ($filtro == 'Pendentes') {
	$query = $pdo->query("SELECT * from $tabela where usuario = '$usuario_selecionado' and status = 'Agendada' order by hora asc");
} else if ($filtro == 'Hoje') {
	$query = $pdo->query("SELECT * from $tabela where usuario = '$usuario_selecionado' and status = 'Agendada' and data = '$data_atual' order by hora asc");
} else if ($filtro == 'Concluidas') {
	$query = $pdo->query("SELECT * from $tabela where usuario = '$usuario_selecionado' and status = 'Concluída' order by hora asc");
} else {
	$query = $pdo->query("SELECT * from $tabela where usuario = '$usuario_selecionado' and data >= '$dataInicial' and data <= '$dataFinal' order by  CASE 
   WHEN status = 'Agendada' THEN 1 ELSE 2 END, hora ASC");
}


$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	echo <<<HTML
<small>
	<table class="table table-bordered text-nowrap border-bottom dt-responsive" id="tabela">
	<thead>
	<tr>
	<th align="center" width="5%" class="text-center">Selecionar</th>
	<th>Título</th>
	<th width="7%">Data</th>
	<th width="4%">Hora</th>
	<th width="7%">Hora Alerta</th>
	<th width="6%">Prioridade</th>
	<th width="10%">Status</th>
	<th width="20%">Ações</th>
	</tr>
	</thead>
	<tbody>
	<small>
HTML;


	for ($i = 0; $i < $linhas; $i++) {
		$id = $res[$i]['id'];
		$usuario = $res[$i]['usuario'];
		$usuario_lanc = $res[$i]['usuario_lanc'];
		$data = $res[$i]['data'];
		$hora = $res[$i]['hora'];
		$descricao = $res[$i]['descricao'];
		$status = $res[$i]['status'];
		$hora_mensagem = $res[$i]['hora_mensagem'];
		$prioridade = $res[$i]['prioridade'];
		$titulo = $res[$i]['titulo'];
		$recorrencia = $res[$i]['recorrencia'];

		$query2 = $pdo->query("SELECT * FROM frequencias where dias = '$recorrencia'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		if (@count($res2) > 0) {
			$nome_recorrencia = $res2[0]['frequencia'];
		} else {
			$nome_recorrencia = 'Sem Registro';
		}


		$query2 = $pdo->query("SELECT * FROM usuarios where id = '$usuario_lanc'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		if (@count($res2) > 0) {
			$nome_usuario_cad = $res2[0]['nome'];
		} else {
			$nome_usuario_cad = '';
		}

		$query2 = $pdo->query("SELECT * FROM usuarios where id = '$usuario'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		if (@count($res2) > 0) {
			$nome_usuario = $res2[0]['nome'];
		} else {
			$nome_usuario = '';
		}

		if ($status == 'Agendada') {
			$bg_card = 'bg-red';
			$ocultar_itens = '';
			$cor_card = 'bg-danger-transparent text-danger';
			$classe_tarefa = 'text-danger';
		} else {
			$bg_card = 'bg-success';
			$ocultar_itens = 'ocultar';
			$cor_card = 'bg-success-transparent text-success';
			$classe_tarefa = 'text-success';
		}

		if ($prioridade == 'Baixa') {
			$classe_pri = 'bg-info-transparent text-info';
		} else if ($prioridade == 'Média') {
			$classe_pri = 'bg-warning-transparent text-warning';
		} else {
			$classe_pri = 'bg-danger-transparent text-danger';
		}


		$dataF = implode('/', array_reverse(@explode('-', $data)));
		$horaF = date("H:i", @strtotime($hora));
		$hora_mensagemF = date("H:i", @strtotime($hora_mensagem));

		$descricaoF = mb_strimwidth($descricao, 0, 50, "...");


		echo <<<HTML
<tr>
<td align="center">
<div class="custom-checkbox custom-control">
<input type="checkbox" class="custom-control-input" id="seletor-{$id}" onchange="selecionar('{$id}')">
<label for="seletor-{$id}" class="custom-control-label mt-1 text-dark"></label>
</div>
</td>
<td><i class="fa fa-square {$classe_tarefa} mr-1"></i> {$titulo}</td>
<td align="center">{$dataF}</td>
<td align="center">{$horaF}</td>
<td align="center">{$hora_mensagemF}</td>
<td><span class="badge font-weight-semibold {$classe_pri} tx-12" style="width:100%;">{$prioridade}</span></td>
<td><span class="badge font-weight-semibold {$cor_card} tx-12" style="width:100%;">{$status}</span></td>
<td>

	<a class="btn btn-info-light btn-sm {$ocultar_itens}" href="#" onclick="editar('{$id}','{$usuario}','{$data}','{$hora}','{$hora_mensagem}','{$descricao}','{$prioridade}','{$titulo}','{$recorrencia}')" title="Editar Dados"><i class="fa fa-edit"></i></a>

<a class="btn btn-primary-light btn-sm" href="#" onclick="mostrar('{$nome_usuario_cad}','{$nome_usuario}','{$data}','{$hora}','{$descricao}','{$status}','{$hora_mensagem}','{$prioridade}','{$titulo}','{$nome_recorrencia}')" title="Mostrar Dados"><i class="fa-regular fa-eye"></i></a>

<a class="btn btn-danger-light btn-sm {$ocultar_itens}" href="#" onclick="excluir('{$id}')" title="Excluir"><i class="fa fa-trash-can"></i></a>

<a class="btn btn-success-light btn-sm {$ocultar_itens}" href="#" onclick="concluirTarefa('{$id}')" title="Concluir Tarefa"><i class="fa-solid fa-check"></i></a>

</td>
</tr>
HTML;
	}

	echo <<<HTML
</small>
</tbody>
<small><div align="center" id="mensagem-excluir"></div></small>
</table>
</small>
<br>
HTML;
} else {
	echo '<small>Nenhum Registro Encontrado!</small>';
}
?>

<script type="text/javascript">
	$(document).ready(function () {

		$('#tarefas_hoje').text("<?= $tarefas_hoje ?>");
		$('#tarefas_pendentes').text("<?= $tarefas_pendentes ?>");
		$('#tarefas_atrasadas').text("<?= $tarefas_atrasadas ?>");
		$('#tarefas_concluidas').text("<?= $tarefas_concluidas ?>");


		if (!$.fn.DataTable.isDataTable('#tabela')) {
			$('#tabela').DataTable({
				"language": {
					//"url" : '//cdn.datatables.net/plug-ins/1.13.2/i18n/pt-BR.json'
				},
				"ordering": false,
				"stateSave": true
			});
		}

	});
</script>

<script>
	function editar(id, usuario, data, hora, hora_mensagem, descricao, prioridade, titulo, recorrencia) {
		$('#mensagem').text('');
		$('#titulo_inserir').text('Editar Tarefa');
		$('#id').val(id);
		$('#usuario').val(usuario);
		$('#data_tarefa').val(data);
		$('#hora').val(hora);
		$('#hora_alerta').val(hora_mensagem);
		$('#descricao').val(descricao);
		$('#prioridade').val(prioridade).change();
		$('#titulo').val(titulo);
		$('#recorrencia').val(recorrencia).change();
		$('#modalForm').modal('show');
	}
</script>

<script>
	function mostrar(usuario, usuario_lanc, data, hora, descricao, status, hora_mensagem, prioridade, titulo, recorrencia) {
		$('#titulo_dados').text(titulo);
		$('#usuario_dados').text(usuario);
		$('#usuario_lanc_dados').text(usuario_lanc);
		$('#data_dados').text(data);
		$('#hora_dados').text(hora);
		$('#descricao_dados').text(descricao);
		$('#status_dados').text(status);
		$('#hora_mensagem_dados').text(hora_mensagem);
		$('#prioridade_dados').text(prioridade);
		$('#recorrencia_dados').text(recorrencia);

		$('#modalDados').modal('show');
	}
</script>

<script>
	function limparCampos() {
		$('#id').val('');
		$('#titulo').val('');
		$('#usuario').val('');
		$('#usuario_lanc').val('');
		$('#data_tarefa').val("<?= $data_atual ?>");
		$('#hora_alerta').val('');
		$('#hora').val('');
		$('#descricao').val('');
		$('#prioridade').val('Baixa').change();
		$('#recorrencia').val('Não').change();

		$('#ids').val('');
	}



	function concluirTarefa(id) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success", // Adiciona margem à direita do botão "Sim, Excluir!"
            cancelButton: "btn btn-danger me-1"
        },
        buttonsStyling: false
    });

    swalWithBootstrapButtons.fire({
        title: "Concluir",
		text: "Deseja Concluir esta Tarefa?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sim, Concluir!",
        cancelButtonText: "Não, Cancelar!",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Realiza a requisição AJAX para excluir o item
            $.ajax({
                url: 'paginas/' + pag + "/concluir.php",
                method: 'POST',
                data: { id },
                dataType: "html",
                success: function (mensagem) {
                    if (mensagem.trim() == "Tarefa Concluída") {
                        // Exibe mensagem de sucesso após a exclusão
                        swalWithBootstrapButtons.fire({
                            title: mensagem,
                            text: 'Fecharei em 1 segundo.',
                            icon: "success",
                            timer: 1000,
                            timerProgressBar: true,
                            confirmButtonText: 'OK',
                        });
                        listar();
                        limparCampos()
                    } else {
                        // Exibe mensagem de erro se a requisição falhar
                        swalWithBootstrapButtons.fire({
                            title: "Opss!",
                            text: mensagem,
                            icon: "error",
                            confirmButtonText: 'OK',
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


<script type="text/javascript">

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
			$('#btn-concluir').hide();
		} else {
			$('#btn-deletar').show();
			$('#btn-concluir').show();
		}
	}



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


				alertexcluido('Excluido com Sucesso!')

				$('#btn-deletar').hide();
				$('#btn-concluir').hide();

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





	function concluirSel(id) {
		const swalWithBootstrapButtons = Swal.mixin({
			customClass: {
				confirmButton: "btn btn-success", // Adiciona margem à direita do botão "Sim, Excluir!"
				cancelButton: "btn btn-danger me-1"
			},
			buttonsStyling: false
		});

		swalWithBootstrapButtons.fire({
			title: "Concluir",
			text: "Deseja Concluir as Tarefas Selecionadas?",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: "Sim, Concluir!",
			cancelButtonText: "Não, Cancelar!",
			reverseButtons: true
		}).then((result) => {
			if (result.isConfirmed) {
				// Realiza a requisição AJAX para excluir o item
				var ids = $('#ids').val();
				var id = ids.split("-");

				for (i = 0; i < id.length - 1; i++) {
					concluirMultiplos(id[i]);
				}

				// Exibe mensagem de sucesso após a exclusão
				swalWithBootstrapButtons.fire({
					title: 'Tarefa Concluída',
					text: 'Fecharei em 2 segundo.',
					icon: "success",
					timer: 2000,
					timerProgressBar: true,
					confirmButtonText: 'OK',
				});

				$('#btn-deletar').hide();
				$('#btn-concluir').hide();


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

	function concluirMultiplos(id) {
		//$('#mensagem-excluir').text('Excluindo...')

		$.ajax({
			url: 'paginas/' + pag + "/concluir.php",
			method: 'POST',
			data: { id },
			dataType: "html",

			success: function (mensagem) {
				if (mensagem.trim() == "Tarefa Concluída") {
					buscar();
				} else {
					alertErro(mensagem)
				}
			}
		});
	}

	function deletarSelBaixar() {
		var ids = $('#ids').val();
		var id = ids.split("-");

		for (i = 0; i < id.length - 1; i++) {
			var novo_id = id[i];
			$.ajax({
				url: 'paginas/' + pag + "/baixar_multiplas.php",
				method: 'POST',
				data: {
					novo_id
				},
				dataType: "html",

				success: function (result) {
					if (result.trim() == "Baixado com Sucesso") {
						alertsucesso(result)
					} else {
						alertErro(result)
					}

				}
			});
		}


	}


</script>