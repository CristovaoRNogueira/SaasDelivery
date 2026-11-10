<?php
$tabela = 'receber_sas';
require_once("../../../conexao.php");

$data_atual = date('Y-m-d');
$data_hoje = date('Y-m-d');

$id = @$_POST['id'];

$total_pago = 0;
$total_pendentes = 0;
$total_vencidas = 0;
$total_pendentesF = 0;
$total_vencidasF = 0;
$query = $pdo->query("SELECT * from $tabela where cliente = '$id' and pago != 'Sim'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	echo <<<HTML
<small><small>
	<table class="table table-striped table-hover table-bordered text-nowrap border-bottom dt-responsive" id="tabela3">
	<thead> 
	<tr> 
	<th class="esc">Descrição</th>
	<th>Valor</th>	
	<th class="esc">Vencimento</th>		
	<th>Baixar</th>				
	</tr> 
	</thead> 
	<tbody>	
HTML;

	for ($i = 0; $i < $linhas; $i++) {
		$id = $res[$i]['id'];
		$descricao = $res[$i]['descricao'];
		$cliente = $res[$i]['cliente'];
		$valor = $res[$i]['valor'];
		$vencimento = $res[$i]['vencimento'];
		$data_pgto = $res[$i]['data_pgto'];
		$data_lanc = $res[$i]['data_lanc'];
		$forma_pgto = $res[$i]['forma_pgto'];
		$frequencia = $res[$i]['frequencia'];
		$obs = $res[$i]['obs'];
		$arquivo = $res[$i]['arquivo'];
		$referencia = $res[$i]['referencia'];
		$id_ref = $res[$i]['id_ref'];
		$multa = $res[$i]['multa'];
		$juros = $res[$i]['juros'];
		$desconto = $res[$i]['desconto'];
		$taxa = $res[$i]['taxa'];
		$subtotal = $res[$i]['subtotal'];
		$usuario_lanc = $res[$i]['usuario_lanc'];
		$usuario_pgto = $res[$i]['usuario_pgto'];
		$pago = $res[$i]['pago'];
		$ref_pix = $res[$i]['ref_pix'];

		if($ref_pix != ""){
		     require('../../pagamentos/consultar_pagamento.php');
			     if($status_api == 'approved'){
			         	$pdo->query("UPDATE receber_sas set pago = 'Sim', data_pgto = curDate() where id = '$id'");	
			        }
		}

		$vencimentoF = implode('/', array_reverse(@explode('-', $vencimento)));
		$data_pgtoF = implode('/', array_reverse(@explode('-', $data_pgto)));
		$data_lancF = implode('/', array_reverse(@explode('-', $data_lanc)));



		$valorF = @number_format($valor, 2, ',', '.');
		$multaF = @number_format($multa, 2, ',', '.');
		$jurosF = @number_format($juros, 2, ',', '.');
		$descontoF = @number_format($desconto, 2, ',', '.');
		$taxaF = @number_format($taxa, 2, ',', '.');
		$subtotalF = @number_format($subtotal, 2, ',', '.');

		if ($pago == "Sim") {
			$valor_finalF = @number_format($subtotal, 2, ',', '.');
		} else {
			$valor_finalF = @number_format($valor, 2, ',', '.');
		}



		$query2 = $pdo->query("SELECT * FROM formas_pgto where id = '$forma_pgto'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		if (@count($res2) > 0) {
			$nome_pgto = $res2[0]['nome'];
			$taxa_pgto = $res2[0]['taxa'];
		} else {
			$nome_pgto = 'Sem Registro';
			$taxa_pgto = 0;
		}


		$query2 = $pdo->query("SELECT * FROM frequencias where dias = '$frequencia'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		if (@count($res2) > 0) {
			$nome_frequencia = $res2[0]['frequencia'];
		} else {
			$nome_frequencia = 'Sem Registro';
		}




		if ($pago == 'Sim') {
			$classe_pago = 'table-success';
			$ocultar = 'ocultar';
			$total_pago += $subtotal;
		} else {
			$classe_pago = 'table-danger text-danger';
			$ocultar = '';
			$total_pendentes += $valor;
		}


		$valor_multa = 0;
		$valor_juros = 0;
		$classe_venc = '';
		if (@strtotime($vencimento) < @strtotime($data_hoje)) {
			$classe_venc = 'table-danger';
			$valor_multa = $multa_atraso;

			//pegar a quantidade de dias que o pagamento está atrasado
			$dif = @strtotime($data_hoje) - @strtotime($vencimento);
			$dias_vencidos = floor($dif / (60 * 60 * 24));

			$valor_juros = ($valor * $juros_atraso / 100) * $dias_vencidos;
			$total_vencidas += $valor;
		}

		$total_pendentesF = @number_format($total_pendentes, 2, ',', '.');
		$total_vencidasF = @number_format($total_vencidas, 2, ',', '.');

		$taxa_conta = $taxa_pgto * $valor / 100;

		$taxa_contaF = @number_format($taxa_conta, 2, ',', '.');
		$valor_multaF = @number_format($valor_multa, 2, ',', '.');
		$valor_jurosF = @number_format($valor_juros, 2, ',', '.');
		

		//PEGAR RESIDUOS DA CONTA
		$total_resid = 0;
		$valor_com_residuos = 0;
		$query2 = $pdo->query("SELECT * FROM receber_sas WHERE id_ref = '$id' and residuo = 'Sim'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		if (@count($res2) > 0) {

			$descricao = '(Resíduo) - ' . $descricao;

			for ($i2 = 0; $i2 < @count($res2); $i2++) {
				foreach ($res2[$i2] as $key => $value) {
				}
				$id_res = $res2[$i2]['id'];
				$valor_resid = $res2[$i2]['valor'];
				$total_resid += $valor_resid - $res2[$i2]['desconto'];
			}


			$valor_com_residuos = $valor + $total_resid;
		}

		if ($valor_com_residuos > 0) {
			$vlr_antigo_conta = '(' . $valor_com_residuos . ')';
			$descricao_link = '';
			$descricao_texto = 'd-none';
		} else {
			$vlr_antigo_conta = '';
			$descricao_link = 'd-none';
			$descricao_texto = '';
		}


		if ($api_whatsapp != 'Não') {
			$ocultar_cobranca = '';
		} else {
			$ocultar_cobranca = 'ocultar';
		}

		//echo $taxa_contaF;


		echo <<<HTML
<tr class="{$classe_venc}">
<td class="esc" width="45%"><i class="fa fa-square {$classe_pago} mr-1"></i> {$descricao}</td>
<td >R$ {$valor_finalF}</td>
<td class="esc">{$vencimentoF}</td>
<td >
<big><a class="" href="#" onclick="baixar('{$id}', '{$valorF}', '{$descricao}', '{$forma_pgto}', '{$taxa_contaF}', '{$valor_multaF}', '{$valor_jurosF}')" title="Baixar Conta"><i class="fa fa-check-square " style="color:#079934"></i></a></big>

<big><a class="{$ocultar} {$ocultar_cobranca} icones_mobile" href="#" onclick="cobrar('{$id}')" title="Gerar Cobrança"><i class="fa-brands fa-whatsapp " style="color:green"></i></a></big>

<a class="text-danger {$ocultar}" href="#" onclick="excluirConta('{$id}', '{$cliente}')" title="Excluir"><i class="fa fa-trash-can"></i></a>

</td>
</tr>
HTML;
	}
} else {
	echo '<small>Não possui nenhuma conta!</small>';
}


echo <<<HTML
</tbody>
</table>
</small></small>
<br>
	<div align="right"><span>Total Pendentes: <span class="text-verde">R$ {$total_pendentesF}</span></span> 
	<span style="margin-left: 25px">Total Vencidas: <span class="text-danger">R$ {$total_vencidasF}</span></span>
	</div>
HTML;
?>


<script type="text/javascript">
	$(document).ready(function() {
		$('#tabela3').DataTable({
			"language": {
				//"url" : '//cdn.datatables.net/plug-ins/1.13.2/i18n/pt-BR.json'
			},
			"ordering": false,
			"stateSave": true
		});
	});


	function baixar(id, valor, descricao, pgto, taxa, multa, juros) {
		$('#id-baixar').val(id);
		$('#descricao-baixar').text(descricao);
		$('#valor-baixar').val(valor);
		$('#saida-baixar').val(pgto).change();
		$('#subtotal').val(valor);
		$('#valor-juros').val(juros);
		$('#valor-desconto').val('0,00');
		$('#valor-multa').val(multa);
		$('#valor-taxa').val(taxa);


		totalizar()

		$('#modalBaixar').modal('show');
		$('#mensagem-baixar').text('');
	}


	function cobrar(id) {
		$.ajax({
			url: 'paginas/receber/cobrar.php',
			method: 'POST',
			data: {
				id
			},
			dataType: "html",
			success: function(result) {
				alertsucesso(result);
			}
		});
	}
</script>


<script type="text/javascript">
	



// ALERT EXCLUIR #######################################
function excluirConta(id, cliente) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success", // Adiciona margem à direita do botão "Sim, Excluir!"
            cancelButton: "btn btn-danger me-1",
            container: 'swal-whatsapp-container'
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
            $.ajax({
                url: 'paginas/receber/excluir.php',
                method: 'POST',
                data: { id },
                dataType: "html",
                success: function (mensagem) {
                    if (mensagem.trim() == "Excluído com Sucesso") {
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
                        listarDebitos(cliente)
                        limparCampos()
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