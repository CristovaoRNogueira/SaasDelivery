<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
$id_usuario = @$_SESSION['id'];
require_once("../../conexao.php");
require_once("../buscar_config.php");

$dadosXls = "";
$dadosXls .= " <table border='1' >";

$dadosXls .= " <tr>";
$dadosXls .= " <th>Nome</th>";
$dadosXls .= " <th>Telefone</th>";
$dadosXls .= " <th>CPF</th>";
$dadosXls .= " <th>Data Cadastro</th>";
$dadosXls .= " <th>Data Nasc</th>";
$dadosXls .= " <th>" . @utf8_decode('Endereço') . "</th>";
$dadosXls .= " <th>" . @utf8_decode('Número') . "</th>";
$dadosXls .= " <th>CEP</th>";
$dadosXls .= " <th>" . @utf8_decode('Bairro') . "</th>";
$dadosXls .= " <th>" . @utf8_decode('Cidade') . "</th>";
$dadosXls .= " <th>" . @utf8_decode('Estado') . "</th>";
$dadosXls .= " <th>" . @utf8_decode('Complemento') . "</th>";
$dadosXls .= " </tr>";



$query = $pdo->query("SELECT * from clientes where empresa = '$id_empresa' order by id desc");

$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
	for ($i = 0; $i < $linhas; $i++) {
		$id = $res[$i]['id'];
		$nome = $res[$i]['nome'];
		$telefone = $res[$i]['telefone'];
		$endereco = $res[$i]['endereco'];
		$data_cad = $res[$i]['data_cad'];
		$endereco = $res[$i]['endereco'];
		$numero = $res[$i]['numero'];
		$cep = $res[$i]['cep'];
		$bairro = $res[$i]['bairro'];
		$cidade = $res[$i]['cidade'];
		$estado = $res[$i]['estado'];
		$complemento = $res[$i]['complemento'];
		$data_nasc = $res[$i]['data_nasc'];
		$cpf = $res[$i]['cpf'];


		$data_cadF = implode('/', array_reverse(@explode('-', $data_cad)));
		$data_nascF = implode('/', array_reverse(@explode('-', $data_nasc)));


		$tel_whatsF = '55' . preg_replace('/[ ()-]+/', '', $telefone);

		$dadosXls .= " <tr>";
		$dadosXls .= " <td>" . @utf8_decode($nome) . "</td>";
		$dadosXls .= " <td>" . $telefone . "</td>";
		$dadosXls .= " <td>" . $cpf . "</td>";
		$dadosXls .= " <td>" . $data_cadF . "</td>";
		$dadosXls .= " <td>" . $data_nascF . "</td>";
		$dadosXls .= " <td>" . @utf8_decode($endereco) . "</td>";
		$dadosXls .= " <td>" . $numero . "</td>";
		$dadosXls .= " <td>" . $cep . "</td>";
		$dadosXls .= " <td>" . @utf8_decode($bairro) . "</td>";
		$dadosXls .= " <td>" . @utf8_decode($cidade) . "</td>";
		$dadosXls .= " <td>" . @utf8_decode($estado) . "</td>";
		$dadosXls .= " <td>" . @utf8_decode($complemento) . "</td>";


		$dadosXls .= " </tr>";
	}
}


$dadosXls .= " </table>";

$arquivo = "rel-clientes.xls";

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $arquivo . '"');
header('Cache-Control: max-age=0');

echo $dadosXls;
exit;
