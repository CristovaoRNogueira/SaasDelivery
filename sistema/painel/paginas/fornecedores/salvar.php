<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, refaça o Login!';
	exit();
}
$tabela = 'fornecedores';


$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$endereco = $_POST['endereco'];
$pix = $_POST['pix'];
$id = $_POST['id'];
$numero =@ $_POST['numero'];
$bairro = @$_POST['bairro'];
$cidade = @$_POST['cidade'];
$estado = @$_POST['estado'];
$cep = @$_POST['cep'];
$cnpj = @$_POST['cnpj'];
$complemento = @$_POST['complemento'];
$tipo_chave = @$_POST['tipo_chave'];


// Validação da Chave Pix
if($pix == '' and $tipo_chave != ''){
	echo 'Preencher o campo Pix';
	exit();
}

if ($pix != '' and $tipo_chave == '') {
	echo 'Escola o Tipo de Chave';
	exit();
}



//validacao email
if($email != ""){
	$query = $pdo->query("SELECT * from $tabela where email = '$email' and empresa = '$id_empresa' ");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	if(@count($res) > 0 and $id != $res[0]['id']){
		echo 'Email já Cadastrado, escolha outro!!';
		exit();
	}
}


//validacao telefone
$query = $pdo->query("SELECT * from $tabela where telefone = '$telefone'  and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$id_reg = @$res[0]['id'];
if(@count($res) > 0 and $id != $id_reg){
	echo 'Telefone já Cadastrado!';
	exit();
}

if($id == ""){
$query = $pdo->prepare("INSERT INTO $tabela SET nome = :nome, email = :email, telefone = :telefone, data = curDate(), endereco = :endereco, pix = :pix, numero = :numero, bairro = :bairro, cidade = :cidade, estado = :estado, cep = :cep, cnpj = :cnpj, complemento = :complemento, tipo_chave = '$tipo_chave', empresa = '$id_empresa' ");
	
}else{
$query = $pdo->prepare("UPDATE $tabela SET nome = :nome, email = :email, telefone = :telefone, endereco = :endereco, pix = :pix, numero = :numero, bairro = :bairro, cidade = :cidade, estado = :estado, cep = :cep, cnpj = :cnpj, complemento = :complemento, tipo_chave = '$tipo_chave' where id = '$id'");
}
$query->bindValue(":nome", "$nome");
$query->bindValue(":email", "$email");
$query->bindValue(":telefone", "$telefone");
$query->bindValue(":endereco", "$endereco");
$query->bindValue(":pix", "$pix");
$query->bindValue(":numero", "$numero");
$query->bindValue(":bairro", "$bairro");
$query->bindValue(":cidade", "$cidade");
$query->bindValue(":estado", "$estado");
$query->bindValue(":cep", "$cep");
$query->bindValue(":cnpj", "$cnpj");
$query->bindValue(":complemento", "$complemento");
$query->execute();

echo 'Salvo com Sucesso';


 ?>
