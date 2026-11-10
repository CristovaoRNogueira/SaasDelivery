<?php 
$tabela = 'pagar_sas';
require_once("../../../conexao.php");

$id = $_POST['id'];

$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$id_recorrencia = @$res[0]['id_recorrencia'];



	$query = $pdo->query("SELECT * from $tabela where id_recorrencia = '$id_recorrencia' and pago != 'Sim'");
		$res = $query->fetchAll(PDO::FETCH_ASSOC);
		$total_reg = @count($res);
		if($total_reg > 0){
			for($i=0; $i < $total_reg; $i++){
				$hash = @$res[$i]['hash'];
				$id_conta = @$res[$i]['id'];

				if($hash != ""){
					require("../../apis/cancelar_agendamento.php");
				}
				$pdo->query("DELETE FROM $tabela WHERE id = '$id_conta'");

			}
		}

echo 'Cancelado com Sucesso';
?>