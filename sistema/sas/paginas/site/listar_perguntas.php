<?php
$tabela = 'perguntas_site';
@session_start();
$id_usuario = @$_SESSION['id'];

require_once("../../../conexao.php");
require_once("../../verificar.php");

$query = $pdo->query("SELECT * from $tabela where empresa = 0 order by id desc limit 1");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$posicao = @$res[0]['posicao_pergunta'];
$nova_posicao = $posicao + 2;	

$query = $pdo->query("SELECT * from $tabela where empresa = 0 order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	echo <<<HTML
<small>
	<table class="table table-hover table-bordered text-nowrap border-bottom dt-responsive" id="">
	<thead> 
	<tr>
	<th>Pergunta</th>		
	<th class="esc">Resposta</th>
	<th class="esc">Posição</th>
	<th>Editar / Excluir</th>
	</tr> 
	</thead> 
	<tbody>	
HTML;

	for ($i = 0; $i < $linhas; $i++) {
		$id = $res[$i]['id'];
		$titulo = $res[$i]['titulo_pergunta'];		
		$descricao = $res[$i]['descricao_pergunta'];
		$posicao = $res[$i]['posicao_pergunta'];			
		$descricaoF = mb_strimwidth($descricao, 0, 55, "...");

		$descricaoF2 = rawurlencode($descricao);

echo <<<HTML

<tr class="">
<td>{$titulo}</td>
<td class="esc">{$descricaoF}</td>
<td class="esc">{$posicao}</td>
<td>
	<a class="btn btn-info-light btn-sm" href="#" onclick="editarPerguntas('{$id}','{$titulo}','{$descricaoF2}','{$posicao}')" title="Editar Dados"><i class="fa fa-edit"></i></a>

	
	<big><a href="#" class="btn btn-danger-light btn-sm" onclick="excluirPergunta('{$id}')" title="Excluir"><i class="fa fa-trash-can"></i></a></big>



</td>
</tr>
HTML;
	}
} else {
	echo '<small>Não possui nenhuma pergunta!</small>';
}


echo <<<HTML
</tbody>
<small><div align="center" id="mensagem-excluir"></div></small>
</table>
HTML;
?>


<script type="text/javascript">
	function editarPerguntas(id, titulo, descricao, posicao) {
			
		$('#id_pergunta').val(id);
		$('#titulo_pergunta').val(titulo);
		$('#descricao_pergunta').val(decodeURIComponent(descricao));
		
		$('#posicao_pergunta').val(posicao);

		alertsucesso('Campos Recuperados para Edição!');
		
	}



	function limparCamposPerguntas() {
		var posicao = "<?= $nova_posicao ?>";		
		$('#id_pergunta').val('');
		$('#titulo_pergunta').val('');
		$('#descricao_pergunta').val('');
		$('#posicao_pergunta').val(posicao);
		
	}




// ALERT EXCLUIR #######################################
function excluirPergunta(id) {
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
                url: 'paginas/' + pag + "/excluir_pergunta.php",
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
                        listarPerguntas();
                        
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