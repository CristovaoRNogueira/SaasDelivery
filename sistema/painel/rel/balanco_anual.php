<?php

$data_atual = date('Y-m-d');
$mes_atual = Date('m');
$ano_atual = Date('Y');

$data_inicial_filtro = $_POST['dataInicial'] ?? $ano_atual . '-01-01';
$data_final_filtro = $_POST['dataFinal'] ?? $ano_atual . '-12-31';

$data_inicial_obj = DateTime::createFromFormat('!Y-m-d', $data_inicial_filtro);
$data_final_obj = DateTime::createFromFormat('!Y-m-d', $data_final_filtro);
$data_inicial_valida = $data_inicial_obj && $data_inicial_obj->format('Y-m-d') === $data_inicial_filtro;
$data_final_valida = $data_final_obj && $data_final_obj->format('Y-m-d') === $data_final_filtro;

if (!$data_inicial_valida || !$data_final_valida || $data_inicial_filtro > $data_final_filtro) {
	exit('Período inválido. Verifique as datas informadas.');
}

if ($data_inicial_obj->format('Y') !== $data_final_obj->format('Y')) {
	exit('Para o balanço anual, a data inicial e a data final devem pertencer ao mesmo ano.');
}

$ano_atual = $data_inicial_obj->format('Y');
$periodo_relatorio = $data_inicial_obj->format('d/m/Y') . ' a ' . $data_final_obj->format('d/m/Y');
$data_inicio_mes = $ano_atual . "-" . $mes_atual . "-01";
$data_inicio_ano = $ano_atual . "-01-01";




//PEGAR DADOS DO MES JANEIRO
$classe_janeiro = '#363636';
$pagar_janeiro = 0;
$receber_janeiro = 0;
$saldo_janeiro = 0;
$data_inicio_mes = $ano_atual . "-01-01";
$data_final_mes = $ano_atual . "-01-31";
$query = $pdo->query("SELECT * from receber where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and (arquivado IS NULL or arquivado != 'Sim') and empresa = '$id_empresa' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$receber_janeiro += $res[$i]['subtotal'];
	}
}
$query = $pdo->query("SELECT * from pagar where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim'  and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$pagar_janeiro += $res[$i]['subtotal'];
	}
}
$saldo_janeiro = $receber_janeiro - $pagar_janeiro;
$pagar_janeiroF = @number_format($pagar_janeiro, 2, ',', '.');
$receber_janeiroF = @number_format($receber_janeiro, 2, ',', '.');
$saldo_janeiroF = @number_format($saldo_janeiro, 2, ',', '.');
if ($saldo_janeiro > 0) {
	$classe_janeiro = 'green';
} else if ($saldo_janeiro < 0) {
	$classe_janeiro = 'red';
}




//PEGAR DADOS DO MES fevereiro
$classe_fevereiro = '#363636';
$pagar_fevereiro = 0;
$receber_fevereiro = 0;
$saldo_fevereiro = 0;
$data_inicio_mes = $ano_atual . "-02-01";

$bissexto = date('L', @mktime(0, 0, 0, 1, 1, $ano_atual));
if ($bissexto == 1) {
	$data_final_mes = $ano_atual . '-02-29';
} else {
	$data_final_mes = $ano_atual . '-02-28';
}

$query = $pdo->query("SELECT * from receber where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and (arquivado IS NULL or arquivado != 'Sim') and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$receber_fevereiro += $res[$i]['subtotal'];
	}
}
$query = $pdo->query("SELECT * from pagar where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$pagar_fevereiro += $res[$i]['subtotal'];
	}
}
$saldo_fevereiro = $receber_fevereiro - $pagar_fevereiro;
$pagar_fevereiroF = @number_format($pagar_fevereiro, 2, ',', '.');
$receber_fevereiroF = @number_format($receber_fevereiro, 2, ',', '.');
$saldo_fevereiroF = @number_format($saldo_fevereiro, 2, ',', '.');
if ($saldo_fevereiro > 0) {
	$classe_fevereiro = 'green';
} else if ($saldo_fevereiro < 0) {
	$classe_fevereiro = 'red';
}




//PEGAR DADOS DO MES marco
$classe_marco = '#363636';
$pagar_marco = 0;
$receber_marco = 0;
$saldo_marco = 0;
$data_inicio_mes = $ano_atual . "-03-01";
$data_final_mes = $ano_atual . "-03-31";
$query = $pdo->query("SELECT * from receber where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and (arquivado IS NULL or arquivado != 'Sim') and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$receber_marco += $res[$i]['subtotal'];
	}
}
$query = $pdo->query("SELECT * from pagar where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$pagar_marco += $res[$i]['subtotal'];
	}
}
$saldo_marco = $receber_marco - $pagar_marco;
$pagar_marcoF = @number_format($pagar_marco, 2, ',', '.');
$receber_marcoF = @number_format($receber_marco, 2, ',', '.');
$saldo_marcoF = @number_format($saldo_marco, 2, ',', '.');
if ($saldo_marco > 0) {
	$classe_marco = 'green';
} else if ($saldo_marco < 0) {
	$classe_marco = 'red';
}





//PEGAR DADOS DO MES abril
$classe_abril = '#363636';
$pagar_abril = 0;
$receber_abril = 0;
$saldo_abril = 0;
$data_inicio_mes = $ano_atual . "-04-01";
$data_final_mes = $ano_atual . "-04-30";
$query = $pdo->query("SELECT * from receber where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and (arquivado IS NULL or arquivado != 'Sim') and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$receber_abril += $res[$i]['subtotal'];
	}
}
$query = $pdo->query("SELECT * from pagar where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$pagar_abril += $res[$i]['subtotal'];
	}
}
$saldo_abril = $receber_abril - $pagar_abril;
$pagar_abrilF = @number_format($pagar_abril, 2, ',', '.');
$receber_abrilF = @number_format($receber_abril, 2, ',', '.');
$saldo_abrilF = @number_format($saldo_abril, 2, ',', '.');
if ($saldo_abril > 0) {
	$classe_abril = 'green';
} else if ($saldo_abril < 0) {
	$classe_abril = 'red';
}




//PEGAR DADOS DO MES maio
$classe_maio = '#363636';
$pagar_maio = 0;
$receber_maio = 0;
$saldo_maio = 0;
$data_inicio_mes = $ano_atual . "-05-01";
$data_final_mes = $ano_atual . "-05-31";
$query = $pdo->query("SELECT * from receber where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and (arquivado IS NULL or arquivado != 'Sim') and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$receber_maio += $res[$i]['subtotal'];
	}
}
$query = $pdo->query("SELECT * from pagar where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$pagar_maio += $res[$i]['subtotal'];
	}
}
$saldo_maio = $receber_maio - $pagar_maio;
$pagar_maioF = @number_format($pagar_maio, 2, ',', '.');
$receber_maioF = @number_format($receber_maio, 2, ',', '.');
$saldo_maioF = @number_format($saldo_maio, 2, ',', '.');
if ($saldo_maio > 0) {
	$classe_maio = 'green';
} else if ($saldo_maio < 0) {
	$classe_maio = 'red';
}




//PEGAR DADOS DO MES junho
$classe_junho = '#363636';
$pagar_junho = 0;
$receber_junho = 0;
$saldo_junho = 0;
$data_inicio_mes = $ano_atual . "-06-01";
$data_final_mes = $ano_atual . "-06-30";
$query = $pdo->query("SELECT * from receber where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and (arquivado IS NULL or arquivado != 'Sim') and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$receber_junho += $res[$i]['subtotal'];
	}
}
$query = $pdo->query("SELECT * from pagar where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$pagar_junho += $res[$i]['subtotal'];
	}
}
$saldo_junho = $receber_junho - $pagar_junho;
$pagar_junhoF = @number_format($pagar_junho, 2, ',', '.');
$receber_junhoF = @number_format($receber_junho, 2, ',', '.');
$saldo_junhoF = @number_format($saldo_junho, 2, ',', '.');
if ($saldo_junho > 0) {
	$classe_junho = 'green';
} else if ($saldo_junho < 0) {
	$classe_junho = 'red';
}





//PEGAR DADOS DO MES julho
$classe_julho = '#363636';
$pagar_julho = 0;
$receber_julho = 0;
$saldo_julho = 0;
$data_inicio_mes = $ano_atual . "-07-01";
$data_final_mes = $ano_atual . "-07-31";
$query = $pdo->query("SELECT * from receber where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and (arquivado IS NULL or arquivado != 'Sim') and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$receber_julho += $res[$i]['subtotal'];
	}
}
$query = $pdo->query("SELECT * from pagar where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$pagar_julho += $res[$i]['subtotal'];
	}
}
$saldo_julho = $receber_julho - $pagar_julho;
$pagar_julhoF = @number_format($pagar_julho, 2, ',', '.');
$receber_julhoF = @number_format($receber_julho, 2, ',', '.');
$saldo_julhoF = @number_format($saldo_julho, 2, ',', '.');
if ($saldo_julho > 0) {
	$classe_julho = 'green';
} else if ($saldo_julho < 0) {
	$classe_julho = 'red';
}




//PEGAR DADOS DO MES agosto
$classe_agosto = '#363636';
$pagar_agosto = 0;
$receber_agosto = 0;
$saldo_agosto = 0;
$data_inicio_mes = $ano_atual . "-08-01";
$data_final_mes = $ano_atual . "-08-31";
$query = $pdo->query("SELECT * from receber where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and (arquivado IS NULL or arquivado != 'Sim') and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$receber_agosto += $res[$i]['subtotal'];
	}
}
$query = $pdo->query("SELECT * from pagar where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$pagar_agosto += $res[$i]['subtotal'];
	}
}
$saldo_agosto = $receber_agosto - $pagar_agosto;
$pagar_agostoF = @number_format($pagar_agosto, 2, ',', '.');
$receber_agostoF = @number_format($receber_agosto, 2, ',', '.');
$saldo_agostoF = @number_format($saldo_agosto, 2, ',', '.');
if ($saldo_agosto > 0) {
	$classe_agosto = 'green';
} else if ($saldo_agosto < 0) {
	$classe_agosto = 'red';
}




//PEGAR DADOS DO MES setembro
$classe_setembro = '#363636';
$pagar_setembro = 0;
$receber_setembro = 0;
$saldo_setembro = 0;
$data_inicio_mes = $ano_atual . "-09-01";
$data_final_mes = $ano_atual . "-09-30";
$query = $pdo->query("SELECT * from receber where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and (arquivado IS NULL or arquivado != 'Sim') and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$receber_setembro += $res[$i]['subtotal'];
	}
}
$query = $pdo->query("SELECT * from pagar where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$pagar_setembro += $res[$i]['subtotal'];
	}
}
$saldo_setembro = $receber_setembro - $pagar_setembro;
$pagar_setembroF = @number_format($pagar_setembro, 2, ',', '.');
$receber_setembroF = @number_format($receber_setembro, 2, ',', '.');
$saldo_setembroF = @number_format($saldo_setembro, 2, ',', '.');
if ($saldo_setembro > 0) {
	$classe_setembro = 'green';
} else if ($saldo_setembro < 0) {
	$classe_setembro = 'red';
}




//PEGAR DADOS DO MES outubro
$classe_outubro = '#363636';
$pagar_outubro = 0;
$receber_outubro = 0;
$saldo_outubro = 0;
$data_inicio_mes = $ano_atual . "-10-01";
$data_final_mes = $ano_atual . "-10-31";
$query = $pdo->query("SELECT * from receber where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and (arquivado IS NULL or arquivado != 'Sim') and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$receber_outubro += $res[$i]['subtotal'];
	}
}
$query = $pdo->query("SELECT * from pagar where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$pagar_outubro += $res[$i]['subtotal'];
	}
}
$saldo_outubro = $receber_outubro - $pagar_outubro;
$pagar_outubroF = @number_format($pagar_outubro, 2, ',', '.');
$receber_outubroF = @number_format($receber_outubro, 2, ',', '.');
$saldo_outubroF = @number_format($saldo_outubro, 2, ',', '.');
if ($saldo_outubro > 0) {
	$classe_outubro = 'green';
} else if ($saldo_outubro < 0) {
	$classe_outubro = 'red';
}




//PEGAR DADOS DO MES novembro
$classe_novembro = '#363636';
$pagar_novembro = 0;
$receber_novembro = 0;
$saldo_novembro = 0;
$data_inicio_mes = $ano_atual . "-11-01";
$data_final_mes = $ano_atual . "-11-30";
$query = $pdo->query("SELECT * from receber where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and (arquivado IS NULL or arquivado != 'Sim') and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$receber_novembro += $res[$i]['subtotal'];
	}
}
$query = $pdo->query("SELECT * from pagar where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$pagar_novembro += $res[$i]['subtotal'];
	}
}
$saldo_novembro = $receber_novembro - $pagar_novembro;
$pagar_novembroF = @number_format($pagar_novembro, 2, ',', '.');
$receber_novembroF = @number_format($receber_novembro, 2, ',', '.');
$saldo_novembroF = @number_format($saldo_novembro, 2, ',', '.');
if ($saldo_novembro > 0) {
	$classe_novembro = 'green';
} else if ($saldo_novembro < 0) {
	$classe_novembro = 'red';
}




//PEGAR DADOS DO MES dezembro
$classe_dezembro = '#363636';
$pagar_dezembro = 0;
$receber_dezembro = 0;
$saldo_dezembro = 0;
$data_inicio_mes = $ano_atual . "-12-01";
$data_final_mes = $ano_atual . "-12-31";
$query = $pdo->query("SELECT * from receber where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and (arquivado IS NULL or arquivado != 'Sim') and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$receber_dezembro += $res[$i]['subtotal'];
	}
}
$query = $pdo->query("SELECT * from pagar where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$pagar_dezembro += $res[$i]['subtotal'];
	}
}
$saldo_dezembro = $receber_dezembro - $pagar_dezembro;
$pagar_dezembroF = @number_format($pagar_dezembro, 2, ',', '.');
$receber_dezembroF = @number_format($receber_dezembro, 2, ',', '.');
$saldo_dezembroF = @number_format($saldo_dezembro, 2, ',', '.');
if ($saldo_dezembro > 0) {
	$classe_dezembro = 'green';
} else if ($saldo_dezembro < 0) {
	$classe_dezembro = 'red';
}

// Recalcula os meses respeitando o período escolhido no filtro do relatório.
$totais_receber = array_fill(1, 12, 0.0);
$totais_pagar = array_fill(1, 12, 0.0);

$query_receber = $pdo->prepare("SELECT MONTH(data_pgto) AS mes, COALESCE(SUM(subtotal), 0) AS total
	FROM receber
	WHERE data_pgto BETWEEN :data_inicial AND :data_final
	AND pago = 'Sim' AND (arquivado IS NULL OR arquivado != 'Sim') AND empresa = :empresa
	GROUP BY MONTH(data_pgto)");
$query_receber->execute([
	':data_inicial' => $data_inicial_filtro,
	':data_final' => $data_final_filtro,
	':empresa' => $id_empresa
]);
foreach ($query_receber->fetchAll(PDO::FETCH_ASSOC) as $total_mes) {
	$totais_receber[(int) $total_mes['mes']] = (float) $total_mes['total'];
}

$query_pagar = $pdo->prepare("SELECT MONTH(data_pgto) AS mes, COALESCE(SUM(subtotal), 0) AS total
	FROM pagar
	WHERE data_pgto BETWEEN :data_inicial AND :data_final
	AND pago = 'Sim' AND empresa = :empresa
	GROUP BY MONTH(data_pgto)");
$query_pagar->execute([
	':data_inicial' => $data_inicial_filtro,
	':data_final' => $data_final_filtro,
	':empresa' => $id_empresa
]);
foreach ($query_pagar->fetchAll(PDO::FETCH_ASSOC) as $total_mes) {
	$totais_pagar[(int) $total_mes['mes']] = (float) $total_mes['total'];
}

$nomes_meses = [
	1 => 'janeiro',
	2 => 'fevereiro',
	3 => 'marco',
	4 => 'abril',
	5 => 'maio',
	6 => 'junho',
	7 => 'julho',
	8 => 'agosto',
	9 => 'setembro',
	10 => 'outubro',
	11 => 'novembro',
	12 => 'dezembro'
];
$nomes_meses_exibicao = [
	1 => 'JANEIRO', 2 => 'FEVEREIRO', 3 => 'MARÇO', 4 => 'ABRIL',
	5 => 'MAIO', 6 => 'JUNHO', 7 => 'JULHO', 8 => 'AGOSTO',
	9 => 'SETEMBRO', 10 => 'OUTUBRO', 11 => 'NOVEMBRO', 12 => 'DEZEMBRO'
];
$mes_inicial_relatorio = (int) $data_inicial_obj->format('n');
$mes_final_relatorio = (int) $data_final_obj->format('n');

foreach ($nomes_meses as $numero_mes => $nome_mes) {
	$recebido = $totais_receber[$numero_mes];
	$pago = $totais_pagar[$numero_mes];
	$saldo = $recebido - $pago;
	$classe = $saldo > 0 ? 'green' : ($saldo < 0 ? 'red' : '#363636');

	${'receber_' . $nome_mes} = $recebido;
	${'pagar_' . $nome_mes} = $pago;
	${'saldo_' . $nome_mes} = $saldo;
	${'receber_' . $nome_mes . 'F'} = number_format($recebido, 2, ',', '.');
	${'pagar_' . $nome_mes . 'F'} = number_format($pago, 2, ',', '.');
	${'saldo_' . $nome_mes . 'F'} = number_format($saldo, 2, ',', '.');
	${'classe_' . $nome_mes} = $classe;
}
?>
<!DOCTYPE html>
<html>

<head>

	<style>
		@import url('https://fonts.cdnfonts.com/css/tw-cen-mt-condensed');

		@page {
			margin: 145px 20px 25px 20px;
		}

		#header {
			position: fixed;
			left: 0px;
			top: -110px;
			bottom: 100px;
			right: 0px;
			height: 35px;
			text-align: center;
			padding-bottom: 100px;
		}

		#content {
			margin-top: 0px;
		}

		#footer {
			position: fixed;
			left: 0px;
			bottom: -60px;
			right: 0px;
			height: 80px;
		}

		#footer .page:after {
			content: counter(page, my-sec-counter);
		}

		body {
			font-family: 'Tw Cen MT', sans-serif;
		}

		.marca {
			position: fixed;
			left: 50;
			top: 100;
			width: 80%;
			opacity: 8%;
		}
	</style>

</head>

<body>
	<?php
	if ($marca_dagua == 'Sim' and $tipo_rel != 'HTML') { ?>
		<img class="marca" src="<?php echo $url_sistema ?>sistema/img/<?php echo $logo_sistema ?>">
	<?php } ?>


	<div id="header">

		<div style="border-style: solid; font-size: 10px; height: 55px;">
			<table style="width: 100%; border: 0px solid #ccc;">
				<tr>
					<td style="border: 1px; solid #000; width: 25%; text-align: left;">
						<img style="margin-top: 0px; margin-left: 7px;" id="imag"
							src="<?php echo $url_sistema ?>sistema/img/<?php echo $logo_sistema ?>" width="60%">
					</td>

					<td style="text-align: center; font-size: 10px; width: 40%;">

						<b><?php echo mb_strtoupper($nome_sistema) ?></b><br>
						<b>CNPJ: </b><?php echo mb_strtoupper($cnpj_sistema) ?><br>
						<b>INSTAGRAM:</b> <?php echo mb_strtoupper($instagram_sistema) ?><br>
						<?php echo mb_strtoupper($endereco_sistema) ?>

					</td>
					<td style="width: 1%; text-align: center; font-size: 13px;">

					</td>
					<td style="width: 35%; text-align: right; font-size: 9px;padding-right: 10px;">
						<b><big>RELATÓRIO DE BALANÇO ANUAL</big></b><br>
						Período: <?php echo $periodo_relatorio ?><br>
						<?php echo mb_strtoupper($data_hoje) ?>
					</td>
				</tr>
			</table>
		</div>

		<br>


		<table id="cabecalhotabela" style="border-bottom-style: solid; font-size: 11px; margin-bottom:10px; width: 100%; table-layout: fixed;">
			<thead>
				<?php for ($numero_mes = $mes_inicial_relatorio; $numero_mes <= $mes_final_relatorio; $numero_mes++) {
					$nome_mes = $nomes_meses[$numero_mes];
					$classe_mes = ${'classe_' . $nome_mes};
					$receber_mes = ${'receber_' . $nome_mes . 'F'};
					$pagar_mes = ${'pagar_' . $nome_mes . 'F'};
					$saldo_mes = ${'saldo_' . $nome_mes . 'F'};
				?>
				<tr style="margin-left: 0px; background-color:#fff">
					<td style="width:20%; font-weight: bold; color:<?php echo $classe_mes ?>"><?php echo $nomes_meses_exibicao[$numero_mes] ?></td>
					<td style="width:27%">RECEBIMENTOS R$ <?php echo $receber_mes ?></td>
					<td style="width:27%">PAGAMENTOS R$ <?php echo $pagar_mes ?></td>
					<td style="width:26%">SALDO <span style="color:<?php echo $classe_mes ?>"><b>R$ <?php echo $saldo_mes ?></b></span></td>
				</tr>
				<?php } ?>
			</thead>
		</table>
	</div>




	<section id="stats-subtitle" style="margin-top: 200px">


		<style type="text/css">
			#principal {
				width: 100%;
				height: 600px;
				margin-left: 10px;
				font-family: Verdana, Helvetica, sans-serif;
				font-size: 14px;

			}

			#barra {
				margin: 0 2px;
				vertical-align: bottom;
				display: inline-block;
				padding: 2px;
				text-align: center;
				width: 50px;


			}

			.cor1,
			.cor2,
			.cor3,
			.cor4,
			.cor5,
			.cor6,
			.cor7,
			.cor8,
			.cor9,
			.cor10,
			.cor11,
			.cor12 {
				color: #FFF;
				padding: 5px;
			}

			.cor1 {
				background-color: <?php echo $classe_janeiro ?>;
			}

			.cor2 {
				background-color: <?php echo $classe_fevereiro ?>;
			}

			.cor3 {
				background-color: <?php echo $classe_marco ?>;
			}

			.cor4 {
				background-color: <?php echo $classe_abril ?>;
			}

			.cor5 {
				background-color: <?php echo $classe_maio ?>;
			}

			.cor6 {
				background-color: <?php echo $classe_junho ?>;
			}

			.cor7 {
				background-color: <?php echo $classe_julho ?>;
			}

			.cor8 {
				background-color: <?php echo $classe_agosto ?>;
			}

			.cor9 {
				background-color: <?php echo $classe_setembro ?>;
			}

			.cor10 {
				background-color: <?php echo $classe_outubro ?>;
			}

			.cor11 {
				background-color: <?php echo $classe_novembro ?>;
			}

			.cor12 {
				background-color: <?php echo $classe_dezembro ?>;
			}
		</style>

		<div id="principal">
			<p>Lucros no período de <?php echo $periodo_relatorio ?></p>

			<?php
			$divisao_barra = 1;
			?>

			<div id="barra" style="<?php echo ($mes_inicial_relatorio <= 1 && $mes_final_relatorio >= 1) ? '' : 'display:none' ?>">
				<div class="cor1" style="height:<?php echo $saldo_janeiro / $divisao_barra + 25 ?>px; font-size: 9px; max-height: 600px"><?php echo $saldo_janeiroF ?> </div>
				<div style="font-size: 10px">JANEIRO</div>
			</div>

			<div id="barra" style="<?php echo ($mes_inicial_relatorio <= 2 && $mes_final_relatorio >= 2) ? '' : 'display:none' ?>">
				<div class="cor2" style="height:<?php echo $saldo_fevereiro / $divisao_barra + 25 ?>px; font-size: 9px; max-height: 600px"><?php echo $saldo_fevereiroF ?> </div>
				<div style="font-size: 10px">FEVEREIRO</div>
			</div>

			<div id="barra" style="<?php echo ($mes_inicial_relatorio <= 3 && $mes_final_relatorio >= 3) ? '' : 'display:none' ?>">
				<div class="cor3" style="height:<?php echo $saldo_marco / $divisao_barra + 25 ?>px; font-size: 9px; max-height: 600px"><?php echo $saldo_marcoF ?> </div>
				<div style="font-size: 10px">MARÇO</div>
			</div>

			<div id="barra" style="<?php echo ($mes_inicial_relatorio <= 4 && $mes_final_relatorio >= 4) ? '' : 'display:none' ?>">
				<div class="cor4" style="height:<?php echo $saldo_abril / $divisao_barra + 25 ?>px; font-size: 9px; max-height: 600px"><?php echo $saldo_abrilF ?> </div>
				<div style="font-size: 10px">ABRIL</div>
			</div>


			<div id="barra" style="<?php echo ($mes_inicial_relatorio <= 5 && $mes_final_relatorio >= 5) ? '' : 'display:none' ?>">
				<div class="cor5" style="height:<?php echo $saldo_maio / $divisao_barra + 25 ?>px; font-size: 9px; max-height: 600px">R$ <?php echo $saldo_maioF ?> </div>
				<div style="font-size: 10px">MAIO</div>
			</div>


			<div id="barra" style="<?php echo ($mes_inicial_relatorio <= 6 && $mes_final_relatorio >= 6) ? '' : 'display:none' ?>">
				<div class="cor6" style="height:<?php echo $saldo_junho / $divisao_barra + 25 ?>px; font-size: 9px; max-height: 600px"><?php echo $saldo_junhoF ?> </div>
				<div style="font-size: 10px">JUNHO</div>
			</div>


			<div id="barra" style="<?php echo ($mes_inicial_relatorio <= 7 && $mes_final_relatorio >= 7) ? '' : 'display:none' ?>">
				<div class="cor7" style="height:<?php echo $saldo_julho / $divisao_barra + 25 ?>px; font-size: 9px; max-height: 600px"><?php echo $saldo_julhoF ?> </div>
				<div style="font-size: 10px">JULHO</div>
			</div>


			<div id="barra" style="<?php echo ($mes_inicial_relatorio <= 8 && $mes_final_relatorio >= 8) ? '' : 'display:none' ?>">
				<div class="cor8" style="height:<?php echo $saldo_agosto / $divisao_barra + 25 ?>px; font-size: 9px; max-height: 600px"><?php echo $saldo_agostoF ?> </div>
				<div style="font-size: 10px">AGOSTO</div>
			</div>


			<div id="barra" style="<?php echo ($mes_inicial_relatorio <= 9 && $mes_final_relatorio >= 9) ? '' : 'display:none' ?>">
				<div class="cor9" style="height:<?php echo $saldo_setembro / $divisao_barra + 25 ?>px; font-size: 9px; max-height: 600px"><?php echo $saldo_setembroF ?> </div>
				<div style="font-size: 10px">SETEMBRO</div>
			</div>



			<div id="barra" style="<?php echo ($mes_inicial_relatorio <= 10 && $mes_final_relatorio >= 10) ? '' : 'display:none' ?>">
				<div class="cor10" style="height:<?php echo $saldo_outubro / $divisao_barra + 25 ?>px; font-size: 9px; max-height: 600px"><?php echo $saldo_outubroF ?> </div>
				<div style="font-size: 10px">OUTUBRO</div>
			</div>


			<div id="barra" style="<?php echo ($mes_inicial_relatorio <= 11 && $mes_final_relatorio >= 11) ? '' : 'display:none' ?>">
				<div class="cor11" style="height:<?php echo $saldo_novembro / $divisao_barra + 25 ?>px; font-size: 9px; max-height: 600px"><?php echo $saldo_novembroF ?> </div>
				<div style="font-size: 10px">NOVEMBRO</div>
			</div>

			<div id="barra" style="<?php echo ($mes_inicial_relatorio <= 12 && $mes_final_relatorio >= 12) ? '' : 'display:none' ?>">
				<div class="cor12" style="height:<?php echo $saldo_dezembro / $divisao_barra + 25 ?>px; font-size: 9px; max-height: 600px"><?php echo $saldo_dezembroF ?> </div>
				<div style="font-size: 10px">DEZEMBRO</div>
			</div>



		</div>



	</section>



	<div id="footer" class="row">
		<hr style="margin-bottom: 0;">
		<table style="width:100%;">
			<tr style="width:100%;">
				<td style="width:60%; font-size: 10px; text-align: left;"><?php echo $nome_sistema ?> Telefone: <?php echo $telefone_sistema ?></td>
				<td style="width:40%; font-size: 10px; text-align: right;">
					<p class="page">Página </p>
				</td>
			</tr>
		</table>
	</div>







</body>

</html>
