<?php 
@session_start();
$id_empresa = @$_SESSION['empresa'];
require_once("../../../conexao.php");

$dataMes = Date('m');
$dataDia = Date('d');
$dataAno = Date('Y');
$data_atual = date('Y-m-d');


$data_semana = date('Y/m/d', strtotime("-7 days",strtotime($data_atual)));



$id_usuario = $_SESSION['id'];

$id = $_POST['id'];
$clientes = $_POST['clientes'];
$data_disparo = $_POST['data_disparo'];
$hora_disparo = $_POST['hora_disparo'];
$frequencia = $_POST['frequencia'];
$quantidade = $_POST['quantidade'];
$intervalo_horas = $_POST['intervalo_horas'];
$delay = 1;

if($intervalo_horas <= 0){
	$intervalo_horas = 1;
}

$tipo_de_disparo = $_POST['clientes'];

$grupo = 'Não';

// Validar horário
$hora_atual = date('H:i');
$data_atual = date('Y-m-d');

if($frequencia > 0){
	if($quantidade == ""){
		echo 'Coloque uma quantidade de vezes que você deseja que a mensagem seja disparada!';
		exit();
	}
}

if($data_disparo == $data_atual){
	if($hora_disparo != "agora"){
		if(strtotime($hora_disparo) <= strtotime($hora_atual)){
			echo 'O horário de disparo precisa ser maior que o horário atual!';
			exit();
		}
	}    
}else if($data_disparo < $data_atual){
	echo 'A data de disparo não pode ser menor que a data atual!';
	exit();
}


if(empty($hora_disparo)){
	echo 'Selecione um horário de disparo!';
	exit();
}

if (empty($data_disparo)) {
	echo 'Selecione uma data de disparo!';
	exit();
}



require("importar_excel.php");
require("importar_txt.php");

if ($_FILES['arquivo_excel']['name'] != "" || $_FILES['arquivo_texto']['name'] != ""){
	echo 'Salvo com Sucesso';
	exit();
}


// Buscar os Contatos que serão enviados
if ($clientes == "Teste") {
	$query = $pdo->query("SELECT * FROM config where telefone != '' and empresa = '$id_empresa' ");

} else if ($clientes == "Clientes Mês") {
	$query = $pdo->query("SELECT * FROM clientes where month(data_cad) = '$dataMes' and year(data_cad) = '$dataAno' and telefone != '' and empresa = '$id_empresa' and (marketing = '' or marketing is null) ");

} else if ($clientes == "Clientes Semana") {
	$query = $pdo->query("SELECT * FROM clientes where data_cad >= '$data_semana' and telefone != '' and empresa = '$id_empresa' and (marketing = '' or marketing is null) ");

} else if ($clientes == "Todos") {
	$query = $pdo->query("SELECT * FROM clientes where telefone != '' and empresa = '$id_empresa' and (marketing = '' or marketing is null) ");


}else if ($clientes == "Aniversariantes Mês"){
	$query = $pdo->query("SELECT * FROM clientes where month(data_nasc) = '$dataMes' and empresa = '$id_empresa' and telefone != '' and (marketing = '' or marketing is null)  ");	

}else if ($clientes == "Aniversariantes Dia"){
	$query = $pdo->query("SELECT * FROM clientes where month(data_nasc) = '$dataMes' and day(data_nasc) = '$dataDia'  and telefone != '' and empresa = '$id_empresa' and (marketing = '' or marketing is null) ");	

}else if ($clientes == "Inadimplentes"){
	$query = $pdo->query("SELECT DISTINCT c.nome, c.telefone, c.id FROM clientes c JOIN receber r ON c.id = r.cliente WHERE r.vencimento < CURDATE() AND r.pago = 'Não' and (r.arquivado IS NULL or r.arquivado != 'Sim') and r.empresa = '$id_empresa' ");

}else if ($clientes == "1") {
	$query = $pdo->query("SELECT * FROM clientes where cartoes = 1 and empresa = '$id_empresa' ");

}else if ($clientes == "2") {
	$query = $pdo->query("SELECT * FROM clientes where cartoes = 2 and empresa = '$id_empresa' ");

}else if ($clientes == "3") {
	$query = $pdo->query("SELECT * FROM clientes where cartoes = 3 and empresa = '$id_empresa' ");

}else if ($clientes == "4") {
	$query = $pdo->query("SELECT * FROM clientes where cartoes = 4 and empresa = '$id_empresa' ");

}else if ($clientes == "5") {
	$query = $pdo->query("SELECT * FROM clientes where cartoes = 5 and empresa = '$id_empresa' ");

}else{
	$query = $pdo->query("SELECT * FROM grupos_clientes where grupo = '$clientes' and empresa = '$id_empresa'");
	$grupo = 'Sim';	

	$query2 = $pdo->query("SELECT * FROM grupos_disparos where id = '$clientes'");
	$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
	$clientes = @$res2[0]['nome'];	
	$tipo_de_disparo = @$res2[0]['nome'];	

}


$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_clientes = @count($res);
for ($i = 0; $i < $total_clientes; $i++) {
	if($grupo == 'Não'){
		$id_cliente = @$res[$i]['id'];
		$nome_cliente = @$res[$i]['nome'];
		$telefone_cliente = @$res[$i]['telefone'];
	}else{
		$id_cliente = @$res[$i]['cliente'];
		$query2 = $pdo->query("SELECT * FROM clientes where id = '$id_cliente'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$nome_cliente = @$res2[0]['nome'];
		$telefone_cliente = @$res2[0]['telefone'];

			
	}
	

	if($hora_disparo == "agora"){
		$hora_disparo = date('H:i:s');
	}
		
		//$intervalo_horas = 1;

$data_base = $data_disparo; // Ex: '2025-06-27'
$timestamp_inicial = strtotime("$data_base $hora_disparo");

// Calcular timestamp final com base no intervalo (em horas)
$timestamp_final = strtotime("+{$intervalo_horas} hour", $timestamp_inicial);

// Gerar um timestamp aleatório entre início e fim
$timestamp_randomico = rand($timestamp_inicial, $timestamp_final);

// Formatar a hora e a data reais do disparo
$hora = date('H:i:s', $timestamp_randomico);
$data_disparo_real = date('Y-m-d', $timestamp_randomico);
			


	$pdo->query("INSERT INTO disparos set campanha = '$id', cliente = '$id_cliente', nome = '$nome_cliente', telefone = '$telefone_cliente', hora = '$hora', data_disparo = '$data_disparo_real', empresa = '$id_empresa'");




	if($frequencia > 0){
					for ($if = 2; $if <= $quantidade; $if++) {
						$dias_frequencia = $frequencia;
						$dias_parcela = $if - 1;
						$dias_parcela_2 = ($if - 1) * $dias_frequencia;
						
						if ($dias_frequencia == 30 || $dias_frequencia == 31) {

							$nova_data = date('Y/m/d', strtotime("+$dias_parcela month", strtotime($data_disparo_real)));

						} else if ($dias_frequencia == 90) {
							$dias_parcela = $dias_parcela * 3;
							$nova_data = date('Y/m/d', strtotime("+$dias_parcela month", strtotime($data_disparo_real)));

						} else if ($dias_frequencia == 180) {

							$dias_parcela = $dias_parcela * 6;
							$nova_data = date('Y/m/d', strtotime("+$dias_parcela month", strtotime($data_disparo_real)));

						} else if ($dias_frequencia == 360 || $dias_frequencia == 365) {

							$dias_parcela = $dias_parcela * 12;
							$nova_data = date('Y/m/d', strtotime("+$dias_parcela month", strtotime($data_disparo_real)));

						} else {

							$nova_data = date('Y/m/d', strtotime("+$dias_parcela_2 days", strtotime($data_disparo_real)));
						}

					$pdo->query("INSERT INTO disparos set campanha = '$id', cliente = '$id_cliente', nome = '$nome_cliente', telefone = '$telefone_cliente', hora = '$hora', data_disparo = '$nova_data', empresa = '$id_empresa'");

					}
				}



}


//totalizar os disparos para a campanha
$query = $pdo->query("SELECT * FROM disparos where campanha = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_disparos = @count($res);

$pdo->query("UPDATE marketing SET total_disparos = '$total_disparos', forma_envio = '$tipo_de_disparo' where id = '$id'");

echo 'Salvo com Sucesso';
?>
