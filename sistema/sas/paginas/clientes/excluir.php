<?php 
$tabela = 'empresas';
require_once("../../../conexao.php");

$id = $_POST['id'];

$query = $pdo->query("SELECT * FROM receber_sas WHERE cliente = '$id' AND pago = 'Não'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if (@count($res) > 0) {
	echo 'Você não pode excluir este cliente, existem Valores a receber associadas a ele, primeiro exclua a conta e depois exclua o cliente!';
	exit();
}


// BUSCAR IMAGENS DA EMPRESA
$query = $pdo->query("SELECT * FROM config WHERE empresa = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);

if (@count($res) > 0) {

	$logo = $res[0]['logo'];
	$icone = $res[0]['icone'];
	$logo_rel = $res[0]['logo_rel'];
	$logo_painel = $res[0]['logo_painel'];
	$fundo_login = @$res[0]['fundo_login'];

	$arquivos = array(
		$logo,
		$logo_rel,
		$icone,
		$logo_painel,
		$fundo_login,
		'icone_192_'.$id.'.png',
		'icone_512_'.$id.'.png'
	);

	foreach($arquivos as $arquivo){

		if($arquivo != "" && $arquivo != "sem-foto.png"){

			$caminho = "../../../img/".$arquivo;

			if(file_exists($caminho)){
				@unlink($caminho);
			}

		}

	}
}


// EXCLUIR REGISTROS
$pdo->query("DELETE FROM $tabela WHERE id = '$id'");
$pdo->query("DELETE FROM usuarios WHERE empresa = '$id'");
$pdo->query("DELETE FROM receber_sas WHERE cliente = '$id' AND pago != 'Sim'");
$pdo->query("DELETE FROM config WHERE empresa = '$id'");
$pdo->query("DELETE FROM clientes_recursos WHERE empresa = '$id'");

echo 'Excluído com Sucesso';
?>