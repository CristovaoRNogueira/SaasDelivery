<?php 
@session_start();
$id_usuario = @$_SESSION['id'];

require_once("../../../conexao.php");
$tabela = 'recursos';
$id_usuario = @$_POST['id'];


$checked = '';
$query3 = $pdo->query("SELECT * FROM recursos order by id asc");
$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
$total_reg3 = @count($res3);
if($total_reg3 > 0){
	

	for($i3=0; $i3 < $total_reg3; $i3++){
		foreach ($res3[$i3] as $key => $value){}
		$nome = $res3[$i3]['nome'];
		$chave = $res3[$i3]['chave'];
		$id = $res3[$i3]['id'];

		$query2 = $pdo->query("SELECT * FROM clientes_recursos where empresa = '$id_usuario' and recurso = '$id'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$total_reg2 = @count($res2);
		if($total_reg2 > 0){
			$checked = 'checked';
		}else{
			$checked = '';
		}

		echo '
		<div class="form-check col-md-4" >
		<input class="form-check-input" type="checkbox" value="" id="" '.$checked.' onclick="adicionarPermissao('.$id.','.$id_usuario.')">
		<label class="labelcheck" style="font-size:13px">
		'.$nome.'
		</label>
		</div>
		';

	}

}



?>