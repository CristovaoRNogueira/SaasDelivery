<?php 
$tabela = 'planos_itens';
require_once("../../../conexao.php");


$nome = $_POST['nome'];
$plano = $_POST['id'];

//validacao nome
$query = $pdo->query("SELECT * from $tabela where nome = '$nome' and plano = '$plano'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) > 0){
	echo 'Característica já Cadastrada para o plano!';
	exit();
}


$query = $pdo->prepare("INSERT INTO $tabela SET nome = :nome, plano = :plano");
$query->bindValue(":nome", "$nome");
$query->bindValue(":plano", "$plano");
$query->execute();

echo 'Salvo com Sucesso';
 ?>