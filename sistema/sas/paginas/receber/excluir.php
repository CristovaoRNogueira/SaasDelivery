<?php
$tabela = 'receber_sas';
require_once("../../../conexao.php");

$id = $_POST['id'];

$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$foto = @$res[0]['arquivo'];
$hash = @$res[0]['hash'];
$quant_recorrencia = @$res[0]['quant_recorrencia'];

if ($foto != "sem-foto.png") {
	@unlink('../../images/contas/' . $foto);
}

if ($hash != "") {
	require("../../apis/cancelar_agendamento.php");
}


if ($quant_recorrencia > 0) {
	$query = $pdo->query("SELECT * from $tabela where id_recorrencia = '$id' and pago != 'Sim'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if ($total_reg > 0) {
		for ($i = 0; $i < $total_reg; $i++) {
			$hash = @$res[$i]['hash'];
			$id_conta = @$res[$i]['id'];
			if ($hash != "") {
				require("../../apis/cancelar_agendamento.php");
			}
			$pdo->query("DELETE FROM $tabela WHERE id = '$id_conta'");
		}
	}
}


$pdo->query("DELETE FROM $tabela WHERE id = '$id' ");
echo 'Excluído com Sucesso';
?>