<?php 
@session_start();
require_once('../../sistema/conexao.php');

$id_empresa = @$_SESSION['empresa'];
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, acesse novamente!';
	exit();
}
require_once("../../sistema/painel/buscar_config.php");

$id = @$_POST['id'];
$grade = @$_POST['grade'];
$valor = @$_POST['valor'];
$tipo = @$_POST['tipo'];
$sessao = @$_SESSION['sessao_usuario'];
$marcado = @$_POST['marcado'];
$quantidade = @$_POST['quantidade'];
$tipagem = @$_POST['tipagem'];

//verificar se o item já existe na tabela para remove-lo
if($tipo == '1 de Cada'){
     $query = $pdo->query("SELECT * FROM temp where grade = '$grade' and carrinho = '0' and sessao = '$sessao' and id_item = '$id' and empresa = '$id_empresa' ");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){
		 $pdo->query("DELETE FROM temp where grade = '$grade' and carrinho = '0' and sessao = '$sessao' and id_item = '$id' and empresa = '$id_empresa'");

		 echo 'Alterado com Sucesso';
		exit();   
	}



}

if($quantidade == ""){
	$quantidade = 1;
}





if($quantidade == 0){
	$pdo->query("DELETE FROM temp where id_item = '$id' and carrinho = '0' and sessao = '$sessao' and empresa = '$id_empresa'");
	echo 'Alterado com Sucesso';
	exit();
}

if($tipo == 'Múltiplo'){
	$pdo->query("DELETE FROM temp where id_item = '$id' and carrinho = '0' and sessao = '$sessao' and empresa = '$id_empresa'");
}

if($tipo == 'Único' || $tipo == 'Variação'){
	$query = $pdo->query("SELECT * FROM temp where grade = '$grade' and carrinho = '0' and sessao = '$sessao' and empresa = '$id_empresa'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){
		 $pdo->query("DELETE FROM temp where grade = '$grade' and carrinho = '0' and sessao = '$sessao' and empresa = '$id_empresa'");
	}
}


$pdo->query("INSERT INTO temp SET sessao = '$sessao', tabela = '$tipo', id_item = '$id', carrinho = '0', data = curDate(), grade = '$grade', valor_item = '$valor', quantidade = '$quantidade', tipagem = '$tipagem', pedido = 0, empresa = '$id_empresa'"); 


echo 'Alterado com Sucesso';
