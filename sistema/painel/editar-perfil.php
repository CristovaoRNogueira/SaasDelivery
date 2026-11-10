<?php
@session_start();
$id_empresa = @$_SESSION['empresa'];

if($id_empresa == ""){
    echo 'Sessão expirada! Atualize a página!';
    exit();
}
$tabela = 'usuarios';
require_once("../conexao.php");
require_once("buscar_config.php");

if($modo_teste == 'Sim'){
	echo 'Esse recurso está desativado no modo teste!';
	exit();
}


@$nome = $_POST['nome'];
@$email = $_POST['email'];
@$telefone = $_POST['telefone'];
@$conf_senha = $_POST['conf_senha'];
@$endereco = $_POST['endereco'];
@$senha = $_POST['senha'];
@$senha_crip = password_hash($senha, PASSWORD_DEFAULT);
@$id = $_POST['id_usuario'];
@$numero = $_POST['numero'];
@$bairro = $_POST['bairro'];
@$cidade = $_POST['cidade'];
@$estado = $_POST['estado'];
@$cep = $_POST['cep'];
@$data_nasc = $_POST['data_nasc'];
@$complemento = $_POST['complemento'];

if ($conf_senha != $senha) {
	echo 'As senhas não se coincidem';
	exit();
}

//validacao email
$query = $pdo->query("SELECT * from $tabela where email = '$email' and empresa = '$id_empresa' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$id_reg = @$res[0]['id'];
if (@count($res) > 0 and $id != $id_reg) {
	echo 'Email já Cadastrado!';
	exit();
}

//validacao telefone
$query = $pdo->query("SELECT * from $tabela where telefone = '$telefone' and empresa = '$id_empresa' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$id_reg = @$res[0]['id'];
if (@count($res) > 0 and $id != $id_reg) {
	echo 'Telefone já Cadastrado!';
	exit();
}




//validar troca da foto
$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {
	$foto = $res[0]['foto'];
} else {
	$foto = 'sem-foto.jpg';
}



//SCRIPT PARA SUBIR FOTO NO SERVIDOR
$nome_img = date('d-m-Y H:i:s') . '-' . @$_FILES['foto']['name'];
$nome_img = preg_replace('/[ :]+/', '-', $nome_img);

$caminho = 'images/perfil/' . $nome_img;

$imagem_temp = @$_FILES['foto']['tmp_name'];

if (@$_FILES['foto']['name'] != "") {
	$ext = pathinfo($nome_img, PATHINFO_EXTENSION);
	if ($ext == 'png' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'webp' or $ext == 'PNG' or $ext == 'JPG' or $ext == 'JPEG' or $ext == 'GIF' or $ext == 'WEBP') {

		//EXCLUO A FOTO ANTERIOR
		if ($foto != "sem-foto.jpg") {
			@unlink('images/perfil/' . $foto);
		}

		$foto = $nome_img;

		//pegar o tamanho da imagem
		list($largura, $altura) = getimagesize($imagem_temp);
		if ($largura > 1400) {
			$image = imagecreatefromjpeg($imagem_temp);
			// Reduza a qualidade para 20% ajuste conforme necessário
			imagejpeg($image, $caminho, 20);
			imagedestroy($image);
		} else {
			move_uploaded_file($imagem_temp, $caminho);
		}
	} else {
		echo 'Extensão de Imagem não permitida!';
		exit();
	}
}

if ($data_nasc == "") {
	$nasc = '';
} else {
	$nasc = " ,data_nasc = '$data_nasc'";
}

$query = $pdo->prepare("UPDATE $tabela SET nome = :nome, email = :email, telefone = :telefone, senha = :senha, senha_crip = '$senha_crip', endereco = :endereco, foto = '$foto' $nasc, numero = :numero, bairro = :bairro, cidade = :cidade, estado = :estado, cep = :cep, complemento = :complemento where id = '$id'");

$query->bindValue(":nome", "$nome");
$query->bindValue(":email", "$email");
$query->bindValue(":telefone", "$telefone");
$query->bindValue(":endereco", "$endereco");
$query->bindValue(":senha", "");
$query->bindValue(":numero", "$numero");
$query->bindValue(":bairro", "$bairro");
$query->bindValue(":cidade", "$cidade");
$query->bindValue(":estado", "$estado");
$query->bindValue(":cep", "$cep");
$query->bindValue(":complemento", "$complemento");
$query->execute();

echo 'Editado com Sucesso';
