<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
$tabela = 'vendas';
$data_hoje = date('Y-m-d');

$dataInicial = @$_POST['dataInicial'];
$dataFinal = @$_POST['dataFinal'];
$status_solicitado = @$_POST['status'];
$status = '%'.$status_solicitado.'%';


$total_vendas = 0;

$sql_arquivado = $status_solicitado === 'Arquivados'
	? " and arquivado = 'Sim' "
	: " and (arquivado IS NULL or arquivado != 'Sim') ";
$sql_status = $status_solicitado === 'Arquivados' ? "" : " and status LIKE '$status' ";
$query = $pdo->query("SELECT * FROM $tabela where data >= '$dataInicial' and data <= '$dataFinal' $sql_status $sql_arquivado and pago = 'Sim' and empresa = '$id_empresa' order by data asc, hora desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){

echo <<<HTML
	<small>
	<table class="table table-hover table-bordered text-nowrap border-bottom dt-responsive" id="tabela">
	<thead> 
	<tr> 
	<th>Cliente</th>
	<th>Valor</th> 
	<th>Total Pago</th>
	<th>Troco</th>
	<th>Forma PGTO</th>
	<th>Data</th>
	<th>Hora</th>
	<th>Ações</th>
	</tr> 
	</thead> 
	<tbody>	
HTML;

for($i=0; $i < $total_reg; $i++){
	foreach ($res[$i] as $key => $value){}
	$id = $res[$i]['id'];	
	$cliente = $res[$i]['cliente'];
	$valor = $res[$i]['valor'];
	$total_pago = $res[$i]['total_pago'];
	$troco = $res[$i]['troco'];
	$data = $res[$i]['data'];
	$hora = $res[$i]['hora'];
	$status = $res[$i]['status'];
	$pago = $res[$i]['pago'];
	$obs = $res[$i]['obs'];
	$taxa_entrega = $res[$i]['taxa_entrega'];
	$tipo_pgto = $res[$i]['tipo_pgto'];
	$usuario_baixa = $res[$i]['usuario_baixa'];
	$mesa = $res[$i]['mesa'];
	$nome_cliente_ped = $res[$i]['nome_cliente'];
	$pedido = $res[$i]['pedido'];
	
	$valorF = number_format($valor, 2, ',', '.');
	$total_pagoF = number_format($total_pago, 2, ',', '.');
	$trocoF = number_format($troco, 2, ',', '.');
	$taxa_entregaF = number_format($taxa_entrega, 2, ',', '.');
	$dataF = implode('/', array_reverse(explode('-', $data)));
	//$horaF = date("H:i", strtotime($hora));	

	

		$query2 = $pdo->query("SELECT * FROM usuarios where id = '$usuario_baixa'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$total_reg2 = @count($res2);
		if($total_reg2 > 0){
			$nome_usuario_pgto = $res2[0]['nome'];
		}else{
			$nome_usuario_pgto = 'Nenhum!';
		}


		$query2 = $pdo->query("SELECT * FROM clientes where id = '$cliente'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$total_reg2 = @count($res2);
		if($total_reg2 > 0){
			$nome_cliente = $res2[0]['nome'];
			$telefone_cliente = $res2[0]['telefone'];
		}else{
			if($mesa != '0' and $mesa != ''){
				$nome_cliente = 'Mesa: '.$mesa;
			}else{
				$nome_cliente = $nome_cliente_ped;
			}
			$telefone_cliente = '';
		}


		if($status == 'Finalizado'){
			$classe_alerta = 'text-verde';	
			$total_vendas += $valor;	
			$classe_linha = '';
			$classe_ativo = '';
			$classe_ocultar = '';
		}else if($status == 'Cancelado'){
			$classe_alerta = 'text-danger';
			$total_vendas += 0;
			$classe_linha = 'text-muted';
			$classe_ativo = '#c4c4c4';
			$classe_ocultar = 'ocultar';
		}else{
			$classe_alerta = 'text-primary';
			$total_vendas += $valor;
			$classe_linha = '';
			$classe_ativo = '';
			$classe_ocultar = '';
		}


		if($pedido == 0){
			$pedido = '';
		}else{
			$pedido = 'Pedido ('.$pedido.') / ';
		}



echo <<<HTML
<tr class="{$classe_linha}">
<td style="color:{$classe_ativo}"><i class="fa fa-square {$classe_alerta}"></i> <b>{$pedido}</b> {$nome_cliente}</td>
<td style="color:{$classe_ativo}">R$ {$valorF}</td>
<td style="color:{$classe_ativo}">R$ {$total_pagoF}</td>
<td style="color:{$classe_ativo}">R$ {$trocoF}</td>
<td style="color:{$classe_ativo}">{$tipo_pgto}</td>
<td style="color:{$classe_ativo}">{$dataF}</td>
<td style="color:{$classe_ativo}">{$hora}</td>
<td>	

		<a class="btn btn-primary-light btn-sm" href="#" onclick="mostrar('{$nome_cliente}', '{$valorF}', '{$total_pagoF}', '{$trocoF}',  '{$dataF}', '{$hora}', '{$status}', '{$pago}', '{$obs}', '{$taxa_entregaF}', '{$tipo_pgto}', '{$nome_usuario_pgto}')" title="Ver Dados"><i class="fa fa-info-circle"></i></a>


<big><a href="#" class="btn btn-danger-light btn-sm {$classe_ocultar}" onclick="excluir('{$id}')" title="Excluir"><i class="fa fa-trash-can text-danger"></i></a></big>

		<a class="btn btn-dark-light btn-sm" href="#" onclick="gerarComprovante('{$id}')" title="Gerar Comprovante"><i class="fa fa-file-pdf-o"></i></a>
	
		</td>
</tr>
HTML;

}

$total_vendasF = number_format($total_vendas, 2, ',', '.');

echo <<<HTML
</tbody>
<small><div align="center" id="mensagem-excluir"></div></small>
</table>

<br>	
<div align="right">Total Vendas: <span class="text-verde">R$ {$total_vendasF}</span> </div>

</small>
HTML;


}else{
	echo '<small>Não possui nenhum registro Cadastrado!</small>';
}

?>

<script type="text/javascript">
	$(document).ready( function () {
    $('#tabela').DataTable({
    		"ordering": false,
			"stateSave": true
    	});
    $('#tabela_filter label input').focus();
} );
</script>


<script type="text/javascript">
	function mostrar(cliente, valor, total_pago, troco, data, hora, status, pago, obs, taxa_entrega, tipo_pgto, usuario_pgto){

		$('#nome_dados').text(cliente);
		$('#valor_dados').text(valor);
		$('#total_pago_dados').text(total_pago);
		$('#troco_dados').text(troco);
		$('#data_dados').text(data);
		$('#hora_dados').text(hora);
		$('#status_dados').text(status);
		$('#pago_dados').text(pago);
		$('#obs_dados').text(obs);
		$('#taxa_entrega_dados').text(taxa_entrega);
		$('#tipo_pgto_dados').text(tipo_pgto);
		$('#usuario_pgto_dados').text(usuario_pgto);		
	
		$('#modalDados').modal('show');
	}
</script>




<script type="text/javascript">
	function gerarComprovante(id){
		let a= document.createElement('a');
		                a.target= '_blank';
		                a.href= 'rel/comprovante.php?id='+ id + '&imp=Não';
		                a.click();
	}
</script>
