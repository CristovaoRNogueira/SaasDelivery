<?php 
@session_start();
$id_empresa = @$_SESSION['empresa'];
require_once("../../../conexao.php");
$tabela = 'grupos_clientes';

$id_grupo = $_POST['id_grupo'];

$query = $pdo->query("SELECT * from $tabela where empresa = '$id_empresa' and grupo = '$id_grupo' order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if($linhas > 0){
for($i=0; $i<$linhas; $i++){
	$id = $res[$i]['id'];
	$cliente = $res[$i]['cliente'];		
	
	
	$query2 = $pdo->query("SELECT * from clientes where id = '$cliente'");
	$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
	$nome_cliente = @$res2[0]['nome'];


echo <<<HTML
<div style="width: 100%; max-width: 600px; margin: auto; font-weight: 500;">
  <div style="display: flex; justify-content: space-between; align-items: center; padding: 7px 0; border-bottom: 1px solid #ccc;">
    
    <!-- Esquerda: Check + Nome -->
    <div style="display: flex; align-items: center; gap: 6px;">
      <i class="fa fa-check text-success"></i>
      <span>{$nome_cliente}</span>
    </div>

    <!-- Direita: Excluir -->
    <div>
      <a href="#" onclick="excluirCliente('{$id}')" title="Excluir Cliente">
        <i class="fa fa-trash-can text-danger"></i>
      </a>
    </div>

  </div>
</div>


HTML;

}

}else{
	echo 'Não possui nenhum Registro!';
}


echo <<<HTML
</tbody>
<small><div align="center" id="mensagem-excluir"></div></small>
</table>
</small></small>
HTML;
?>



<script type="text/javascript">
	

// ALERT EXCLUIR #######################################
function excluirCliente(id) {
   	$.ajax({
        url: 'paginas/' + pag + "/excluir_cliente.php",
        method: 'POST',
        data: { id }, // Passa a página aqui
        dataType: "html",

        success: function (result) {
            listarClientes();
            listarClientesGrupos();
        }
    });
   }	
</script>