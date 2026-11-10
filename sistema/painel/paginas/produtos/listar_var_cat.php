<?php 
require_once("../../../conexao.php");
$id = $_POST['id'];

echo '<select class="form-select" id="variacao" name="variacao" style="width:100%;" required>'; 

$query = $pdo->query("SELECT * FROM variacoes_cat where categoria = '$id' ORDER BY id asc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){
	for($i=0; $i < $total_reg; $i++){
		foreach ($res[$i] as $key => $value){}
			echo '<option value="'.$res[$i]['id'].'">'.$res[$i]['sigla'].' - '.$res[$i]['nome'].'</option>';
	}
}else{
	echo '<option value="">Cadastre uma Variação</option>';
}
echo '</select>'; 	
?>