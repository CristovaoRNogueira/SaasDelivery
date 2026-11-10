<?php
$tabela = 'pagar_sas';
require_once("../../../conexao.php");

@session_start();
$nivel = @$_SESSION['nivel'];

$id = $_POST['id'];

if ($nivel != 'Administrador') {
	$query = $pdo->query("SELECT * FROM $tabela where id = '$id' and pago = 'Sim'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if ($total_reg > 0) {
		echo 'Você não pode excluir uma conta já Paga, somente o Administrador!';
		exit();
	}
}

$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
$foto = @$res[0]['arquivo'];
$hash = @$res[0]['hash'];
$quant_recorrencia = @$res[0]['quant_recorrencia'];
$recorrencia_inf = @$res[0]['recorrencia_inf'];


if ($foto != "sem-foto.png") {
	@unlink('../../images/contas/' . $foto);
}

if ($hash != "") {
	require("../../apis/cancelar_agendamento.php");
}

if ($quant_recorrencia > 0 or $recorrencia_inf == 'Sim') {
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