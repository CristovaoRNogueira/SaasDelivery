<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
$tabela = 'abertura_mesa';
$data_hoje = date('Y-m-d');

$dataInicial = @$_POST['dataInicial'];
$dataFinal = @$_POST['dataFinal'];
$status = '%'.@$_POST['status'].'%';

$nome_forma_pgto = '';

$total_vendas = 0;

$query = $pdo->query("SELECT * FROM $tabela where data >= '$dataInicial' and data <= '$dataFinal' and status = 'Fechada' and empresa = '$id_empresa' order by data asc, horario_abertura desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){

echo <<<HTML
	<small>
	<table class="table table-hover table-bordered text-nowrap border-bottom dt-responsive" id="tabela">
	<thead> 
	<tr> 
	<th>Mesa</th>	
	<th>Garçon</th> 
	<th>Fehamento</th>		
	<th>Consumo</th> 
	<th>Comissão</th> 
	<th>Couvert</th>	
	<th>Adiantado</th>	
	<th>Subtotal</th>
	<th>Forma Pgto</th>	 	
	<th>Data</th>		
	<th>Detalhamento</th>
	</tr> 
	</thead> 
	<tbody>	
HTML;

for($i=0; $i < $total_reg; $i++){
	foreach ($res[$i] as $key => $value){}
	$id = $res[$i]['id'];	
	$cliente = $res[$i]['cliente'];
		$horario_abertura = $res[$i]['horario_abertura'];
		$horario_fechamento = $res[$i]['horario_fechamento'];
		$garcon = $res[$i]['garcon'];
		$obs = $res[$i]['obs'];
		$id_abertura_mesa = $res[$i]['id'];
		$pessoas = $res[$i]['pessoas'];
		$mesa = $res[$i]['mesa'];
		$data = $res[$i]['data'];
		$total = $res[$i]['total'];
		$comissao = $res[$i]['comissao_garcon'];
		$couvert = $res[$i]['couvert'];
		$subtotal = $res[$i]['subtotal'];
		$forma_pgto = $res[$i]['forma_pgto'];
		$valor_adiantado = $res[$i]['valor_adiantado'];


		$query2 = $pdo->query("SELECT * FROM formas_pgto WHERE id = '$forma_pgto'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		if (@count($res2) > 0) {
			$nome_forma_pgto = $res2[0]['nome'];
		}

		$totalF = @number_format($total, 2, ',', '.');
		$comissaoF = @number_format($comissao, 2, ',', '.');
		$couvertF = @number_format($couvert, 2, ',', '.');
		$subtotalF = @number_format($subtotal, 2, ',', '.');
		$valor_adiantadoF = @number_format($valor_adiantado, 2, ',', '.');
	
		$dataF = implode('/', array_reverse(@explode('-', $data)));

		$query2 = $pdo->query("SELECT * FROM mesas where id = '$mesa' ");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$nome_mesa = $res2[0]['nome'];

		$query2 = $pdo->query("SELECT * FROM usuarios where id = '$garcon' ");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$nome_garcon = $res2[0]['nome'];

		$horario_aberturaF = date('H:i', @strtotime($horario_abertura));
		$horario_fechamentoF = date('H:i', @strtotime($horario_fechamento));

		$total_vendas += $subtotal;

echo <<<HTML
<tr>
<td>{$nome_mesa}</td>
<td>{$horario_fechamentoF}</td>
<td>{$nome_garcon}</td>
<td>R$ {$totalF}</td>
<td>R$ {$comissaoF}</td>
<td>R$ {$couvertF}</td>
<td>R$ {$valor_adiantadoF}</td>
<td>R$ {$subtotalF}</td>
<td>{$nome_forma_pgto}</td>
<td>{$dataF}</td>
<td>	
		
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
	function gerarComprovante(id){
		let a= document.createElement('a');
		                a.target= '_blank';
		                a.href= 'rel/comprovante_mesa.php?id='+ id + '&imp=Não';
		                a.click();
	}
</script>