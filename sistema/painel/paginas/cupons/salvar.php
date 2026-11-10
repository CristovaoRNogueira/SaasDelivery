<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, refaça o Login!';
	exit();
}
$tabela = 'cupons';

$id = $_POST['id'];
$codigo = $_POST['codigo'];
$valor = $_POST['valor'];
$valor = str_replace(',', '.', $valor);
$data = $_POST['data'];
$quantidade = $_POST['quantidade'];
$valor_minimo = $_POST['valor_minimo'];
$valor_minimo = str_replace(',', '.', $valor_minimo);
$tipo = $_POST['tipo'];

//validar email
$query = $pdo->query("SELECT * from $tabela where codigo = '$codigo' and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) > 0 and $id != $res[0]['id']){
	echo 'Código já Cadastrado, escolha outro!!';
	exit();
}


if($id == ""){
	$query = $pdo->prepare("INSERT INTO $tabela SET codigo = :codigo, valor = :valor, data = '$data', quantidade = '$quantidade', valor_minimo = :valor_minimo, tipo = '$tipo', empresa = '$id_empresa' ");
}else{
	$query = $pdo->prepare("UPDATE $tabela SET codigo = :codigo, valor = :valor, data = '$data', quantidade = '$quantidade', valor_minimo = :valor_minimo, tipo = '$tipo' WHERE id = '$id'");
}

$query->bindValue(":codigo", "$codigo");
$query->bindValue(":valor", "$valor");
$query->bindValue(":valor_minimo", "$valor_minimo");
$query->execute();

echo 'Salvo com Sucesso';

?>