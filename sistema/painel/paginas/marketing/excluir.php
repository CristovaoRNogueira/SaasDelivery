<?php 
require_once("../../../conexao.php");
$tabela = 'marketing';

$id = $_POST['id'];

$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
$foto = $res[0]['arquivo'];
$audio = $res[0]['audio'];
$doc = $res[0]['documento'];

if($foto != "sem-foto.png"){
	@unlink('../../images/marketing/'.$foto);
}

@unlink('../../images/marketing/'.$audio);

if($doc != "sem-foto.png"){
	@unlink('../../images/marketing/'.$doc);
}

$pdo->query("DELETE from $tabela where id = '$id'");
echo 'Excluído com Sucesso';
 ?>