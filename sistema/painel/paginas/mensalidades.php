<?php
$pag = 'mensalidades';

if (@$mensalidades == 'ocultar') {
	echo "<script>window.location='index'</script>";
	exit();
}

$query = $pdo->query("SELECT * from receber_sas where cliente = '$id_empresa' and pago != 'Sim'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
for ($i = 0; $i < $linhas; $i++) {
$ref_pix = @$res[$i]['ref_pix'];
$id = @$res[$i]['id'];
$frequencia = @$res[$i]['frequencia'];
$vencimento = @$res[$i]['vencimento'];
$cliente = @$res[$i]['cliente'];
$descricao = $res[$i]['descricao'];
    $valor = $res[$i]['valor'];
    $ref_pix = $res[$i]['ref_pix'];
    $data = $res[$i]['data_lanc'];
    $hora = $res[$i]['hora'];
    $dataF = implode('/', array_reverse(explode('-', $data)));
    $horaF = date("H:i", strtotime($hora));   
    $vencimento = $res[$i]['vencimento'];
    $pago = $res[$i]['pago'];
    $frequencia = $res[$i]['frequencia'];
    $saida_antiga = $res[$i]['forma_pgto'];
    $arquivo = $res[$i]['arquivo'];
    $referencia = $res[$i]['referencia'];
    $usuario_lanc = $res[$i]['usuario_lanc'];
    $id_ref = $res[0]['id_ref'];

if($ref_pix != ""){

	$query5 = $pdo->query("SELECT * from config where empresa is null or empresa = 0");
	$res5 = $query5->fetchAll(PDO::FETCH_ASSOC);
	$api_pagamento = $res5[0]['api_pagamento'];	

			if($api_pagamento == "Mercado Pago"){
				require('../sas/pagamentos/consultar_pagamento.php');

				 if($status_api == 'approved'){
			     		//buscar a forma de pagamento
			     		$query2 = $pdo->query("SELECT * from formas_pgto where nome like '%$tipo_pagamento%' and (empresa = 0 or empresa is null)");
						$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
						$id_forma_pgto = @$res2[0]['id'];

			         	$pdo->query("UPDATE receber_sas set pago = 'Sim', data_pgto = curDate(), valor = '$total_pago_api', subtotal = '$total_pago_api', forma_pgto = '$id_forma_pgto' where id = '$id'");	



			         	if (@$frequencia > 0) {

        $data_venc = $vencimento;
    //CRIAR A PRÓXIMA CONTA A PAGAR
    $dias_frequencia = $frequencia;

    if ($dias_frequencia == 30 || $dias_frequencia == 31) {
        $nova_data_vencimento = date('Y/m/d', strtotime("+1 month", strtotime($data_venc)));

    } else if ($dias_frequencia == 90) {
        $nova_data_vencimento = date('Y/m/d', strtotime("+3 month", strtotime($data_venc)));

    } else if ($dias_frequencia == 180) {
        $nova_data_vencimento = date('Y/m/d', strtotime("+6 month", strtotime($data_venc)));

    } else if ($dias_frequencia == 360 || $dias_frequencia == 365) {
        $nova_data_vencimento = date('Y/m/d', strtotime("+1 year", strtotime($data_venc)));

    } else {
        $nova_data_vencimento = date('Y/m/d', strtotime("+$dias_frequencia days", strtotime($data_venc)));
    }


    
        $pdo->query("INSERT INTO receber_sas set descricao = '$descricao', cliente = '$cliente', valor = '$valor', data_lanc = curDate(), vencimento = '$nova_data_vencimento', frequencia = '$frequencia', forma_pgto = '$saida_antiga', arquivo = '$arquivo', pago = 'Não', referencia = '$referencia', usuario_lanc = '$usuario_lanc', hora = curTime()");
                
    }



     //chamar o aprova plano caso essa conta seja de um upgrade de plano
    if($referencia == 'Upgrade'){
        $id_plano = $id_ref;
        $id_empresa = $cliente;
        if($id_plano > 0 and $id_empresa > 0){
            require("../sas/asaas/aprovar_plano.php");
        }
        
    }



			        }

			}else{
				//require('../sas/asaas/verificar_pagamentos.php');
			}		     
					    
		}

}

?>



<div class="row row-sm">
	<div class="col-lg-12">
		<div class="card custom-card">
			<div class="card-body" id="listar">

			</div>
		</div>
	</div>
</div>


<script type="text/javascript">var pag = "<?= $pag ?>"</script>
<script src="js/ajax.js"></script>

