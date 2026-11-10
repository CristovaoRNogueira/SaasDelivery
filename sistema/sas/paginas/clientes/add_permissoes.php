<?php 
require_once("../../../conexao.php");

$id_usuario = $_POST['id_usuario'];

$pdo->query("DELETE FROM clientes_recursos where empresa = '$id_usuario'");

$query = $pdo->query("SELECT * FROM recursos order by id asc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){
	for($i=0; $i < $total_reg; $i++){
		foreach ($res[$i] as $key => $value){}
			$nome = $res[$i]['nome'];
		$chave = $res[$i]['chave'];
		$id = $res[$i]['id'];

		$query = $pdo->query("INSERT INTO clientes_recursos SET recurso = '$id', empresa = '$id_usuario'");

	}
}

?>