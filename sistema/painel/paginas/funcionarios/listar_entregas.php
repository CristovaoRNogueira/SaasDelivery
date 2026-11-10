<?php 
require_once("../../../conexao.php");
$tabela = 'vendas';

$data_atual = date('Y-m-d');

$id = $_POST['id'];

$total_entregas = 0;
$total_entregasF = 0;
$query = $pdo->query("SELECT * FROM $tabela where entregador = '$id' and pago_entregador = 'Não' and (arquivado IS NULL or arquivado != 'Sim') order by id asc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){

echo <<<HTML
	<small>
	<table class="table table-hover" id="">
	<thead> 
	<tr> 
	<th>Pedido</th>		
	<th class="esc">Valor</th>
	<th class="esc">Forma PGTO</th>
	<th class="esc">Data</th>
	<th class="esc">Hora</th>
	<th class="esc">Taxa</th>		
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
	$entrega = $res[$i]['entrega'];
	$mesa = $res[$i]['mesa'];
	$nome_cliente_ped = $res[$i]['nome_cliente'];
	$pedido = $res[$i]['pedido'];
	
	$valorF = number_format($valor, 2, ',', '.');

	$dataF = implode('/', array_reverse(explode('-', $data)));
	//$horaF = date("H:i", strtotime($hora));	

	$hora_pedido = date('H:i', strtotime("+$previsao_entrega minutes",strtotime($hora)));
	$taxa_entregaF = number_format($taxa_entrega, 2, ',', '.');

	$total_entregas += $taxa_entrega;

echo <<<HTML
<tr class="">
<td><b>{$pedido}</b></td>
<td>R$ {$valorF}</td>
<td>{$tipo_pgto}</td>
<td>{$dataF}</td>
<td>{$hora_pedido}</td>
<td class="text-danger">R$ {$taxa_entregaF}</td>
</tr>

HTML;
}

$total_entregasF = number_format($total_entregas, 2, ',', '.');

echo <<<HTML
</tbody>
<small><div align="center" id=""></div></small>
</table>

<br>	

<div style="margin-right: 15px; display:inline-block;" align="right">Total de Entregas: <span class="">{$total_reg}</span> </div>

<div style="display:inline-block;" align="right">Total à Pagar: <span class=""> <input id="valor_pago" style="width:60px; display:inline-block; font-size: 13px" class="form-control text-danger" type="text" name="total_entregas" value="{$total_entregas}"></span> </div>

<div style="display:inline-block; width:140px" align="center">Data PGTO
<input type="date" id="data_pgto" class="form-control" value="{$data_atual}">
</div>

<div style="display:inline-block;" align="right">
<button onclick="pagar()" type="button" id="btn_entregas" class="btn btn-danger">Pagar</button></div>

</small>
HTML;


}else{
	echo '<small>Não possui nenhuma Entrega Pendente!</small>';
}

 ?>


 <script type="text/javascript">
 	$("#total_pagar").text("<?=$total_entregasF?>");

 	function pagar(){
 		var total = $("#valor_pago").val();
 		var data = $("#data_pgto").val();
 		var id_func = $("#id_entrega").val();

 		if(total <= 0){
 			alert('Insira um total maior que zero!');
 			return;
 		}


 		$.ajax({
        url: 'paginas/' + pag + "/pagar.php",
        method: 'POST',
        data: {total, data, id_func},
        dataType: "text",

        success: function (mensagem) {            
            if (mensagem.trim() == "Salvo com Sucesso") {
                $('#modalEntregas').modal('hide');                
            } else {
               alert(mensagem)
            }

        },      

    });
 	}
 </script>
