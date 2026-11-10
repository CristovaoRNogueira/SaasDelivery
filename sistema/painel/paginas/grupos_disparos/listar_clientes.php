<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
$tabela = 'clientes';

$id_grupo = $_POST['id_grupo'];

$query = $pdo->query("SELECT * from $tabela where empresa = '$id_empresa' order by nome asc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if($linhas > 0){
echo <<<HTML
<small><small>
	<table class="table table-sm table-bordered table-striped table-hover text-nowrap dt-responsive" id="tabela_cliente" style="font-size: 13px;">
<thead class="table-light"> 
	<tr>	
	<th>Nome</th>					
	<th>Add</th>
	</tr> 
	</thead> 
	<tbody>	
HTML;

for($i=0; $i<$linhas; $i++){
	$id = $res[$i]['id'];
	$nome = $res[$i]['nome'];		
	
	
	
	$query2 = $pdo->query("SELECT * from grupos_clientes where cliente = '$id' and grupo = '$id_grupo'");
	$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
	if(@count($res2) > 0){
		continue;
	}
	


echo <<<HTML
<tr>
<td>{$nome}</td>
<td>
	<a class="btn btn-info btn-sm" href="#" onclick="addCliente('{$id}','{$id_grupo}')" title="AddCliente"><i class="fa fa-plus"></i></a>

</td>

</tr>
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
	$(document).ready(function () {
  $('#tabela_cliente').DataTable({
    language: {
      //url: "//cdn.datatables.net/plug-ins/1.13.2/i18n/pt-BR.json"
    },
    ordering: false,
    lengthChange: false,
    stateSave: true,
    pageLength: 5,
    lengthMenu: [5, 10, 20],
    dom: '<"top d-flex align-items-center justify-content-start mb-2"f>rt<"bottom"ip>',
    initComplete: function () {
      const $input = $('.dataTables_filter input');
      $input
        .addClass('form-control form-control-sm border border-dark rounded')
        .attr('placeholder', 'Buscar...')
        .css({
          width: '250px',
          display: 'inline-block',
          margin: '2',
        });

      $('.dataTables_filter label')
        .css({
          fontWeight: 'normal',
          marginRight: '10px',
        });
    }
  });
});

</script>

<style>
  #tabela_cliente td, 
  #tabela_cliente th {
    padding: 4px 8px !important;
    vertical-align: middle;
  }

  #tabela_cliente .btn-sm {
    padding: 2px 6px;
    font-size: 12px;
  }

  .dataTables_wrapper .dataTables_filter,
  .dataTables_wrapper .dataTables_info {
    font-size: 12px;
  }
</style>

<script type="text/javascript">
	function addCliente(id, grupo){
		$.ajax({
        url: 'paginas/' + pag + "/add_clientes.php",
        method: 'POST',
        data: { id, grupo }, // Passa a página aqui
        dataType: "html",

        success: function (result) {
        	if(result.trim() == 'Adicionado com Sucesso'){
        		 listarClientes();
        		 listarClientesGrupos();  
        		}else{
        			alertInformativo(result)
        		}
                     
        }
    });
	}
	
</script>