<?php
$tabela = 'config';
require_once("../conexao.php");

if($modo_teste == 'Sim'){
	echo 'Em modo de teste esse recurso fica desabilitado!';
	exit();
}

$p = $_POST['p'];



if($p == 'Fundo'){

	$query = $pdo->query("SELECT * FROM config where empresa = 0");
		$res = $query->fetchAll(PDO::FETCH_ASSOC);
		$fundo_login_antigo = @$res[0]['fundo_login'];

		if($fundo_login_antigo != "sem-foto.png"){
			@unlink('../img/'.$fundo_login_antigo);
		} 

	$pdo->query("UPDATE $tabela SET fundo_login = 'sem-foto.png' where empresa = 0");
}

echo 'Excluído com Sucesso';

?>