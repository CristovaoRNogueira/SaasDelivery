<?php
@session_start();
require_once("../../../conexao.php");
$tabela = 'chamados_respostas';
$id_usuario = @$_SESSION['id'];


$id = @$_POST['id'];


$query = $pdo->query("SELECT * from $tabela where chamado = '$id' order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {

	for ($i = 0; $i < $linhas; $i++) {
		$id = $res[$i]['id'];
		$data = $res[$i]['data'];
		$texto = $res[$i]['texto'];	
		$hora = $res[$i]['hora'];
		$empresa = $res[$i]['empresa'];
		$usuario = $res[$i]['usuario'];

		$query2 = $pdo->query("SELECT * from empresas where id = '$empresa'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$nome_empresa = $res2[0]['nome'];

		$query2 = $pdo->query("SELECT * from usuarios where id = '$usuario'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		if(@count($res2) > 0){
			$nome_usuario = $res2[0]['nome'].' (Administrador)';
			$classe_cor = 'blue';
		}else{
			$nome_usuario = $nome_empresa;
			$classe_cor = '';
		}
		

		$dataF = implode('/', array_reverse(@explode('-', $data)));

        $ocultar_excluir = 'ocultar';
        if($usuario == 0){
            $ocultar_excluir = '';
        }



echo <<<HTML

<div>
<span style="color:{$classe_cor}">{$nome_usuario}</span> <span style="font-weight: 500; margin-right: 10px; margin-left: 10px">Data: {$dataF} Hora: {$hora}</span> 

<a class="{$ocultar_excluir}" href="#" onclick="excluirResposta('{$id}')" title="Excluir"><i class="fa fa-trash-can text-danger"></i></a>

 <br>
<span class="text-muted" style="font-weight: 500; margin-top: 5px"><i>{$texto}</i></span>
</div>
<hr>

HTML;

	}

}




?>


<script type="text/javascript">
	
// ALERT EXCLUIR #######################################
function excluirResposta(id) {
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
                url: 'paginas/' + pag + "/excluir_resposta.php",
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
                        listarRespostas();
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