<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, refaça o Login!';
	exit();
}
$tabela = 'usuarios';


$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$nivel = $_POST['nivel'];
$endereco = $_POST['endereco'];
$senha = '123';
$senha_crip = password_hash($senha, PASSWORD_DEFAULT);
$pix = $_POST['pix'];
$id = $_POST['id'];
$numero = @$_POST['numero'];
$bairro = @$_POST['bairro'];
$cidade = @$_POST['cidade'];
$estado = @$_POST['estado'];
$cep = @$_POST['cep'];
$data_nasc = @$_POST['data_nasc'];
$cpf = @$_POST['cpf'];
$acessar_painel = @$_POST['acessar_painel'];
$complemento = @$_POST['complemento'];
$tipo_chave = @$_POST['tipo_chave'];



// Validação da Chave Pix
if ($pix == '' and $tipo_chave != '') {
	echo 'Preencher o campo Pix';
	exit();
}

if ($pix != '' and $tipo_chave == '') {
	echo 'Escola o Tipo de Chave';
	exit();
}



if ($cpf != "") {
	require_once("../../validar_cpf.php");
}


//validacao telefone
$query = $pdo->query("SELECT * from $tabela where telefone = '$telefone' and empresa = '$id_empresa' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$id_reg = @$res[0]['id'];
if (@count($res) > 0 and $id != $id_reg) {
	echo 'Telefone já Cadastrado!';
	exit();
}

if ($data_nasc == "") {
	$nasc = '';
} else {
	$nasc = " ,data_nasc = '$data_nasc'";

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

$caminho = '../../images/perfil/' . $nome_img;

$imagem_temp = @$_FILES['foto']['tmp_name'];

if (@$_FILES['foto']['name'] != "") {

	$ext = pathinfo($nome_img, PATHINFO_EXTENSION);
	if ($ext == 'png' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'PNG' or $ext == 'JPG' or $ext == 'JPEG' or $ext == 'GIF') {

		//EXCLUO A FOTO ANTERIOR
		if ($foto != "sem-foto.jpg") {
			@unlink('../../images/perfil/' . $foto);
		}

		$foto = $nome_img;

		//pegar o tamanho da imagem
		list($largura, $altura) = getimagesize($imagem_temp);

		if ($largura > 1400) {


			if ($ext == 'png') {
				$image = imagecreatefrompng($imagem_temp);
			} else if ($ext == 'jpeg' or $ext == 'jpg') {
				$image = imagecreatefromjpeg($imagem_temp);
			} else {
				die("Formato de imagem não suportado.");
			}



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




if ($id == "") {
	$query = $pdo->prepare("INSERT INTO $tabela SET nome = :nome, email = :email, senha = '', senha_crip = '$senha_crip', nivel = '$nivel', ativo = 'Sim', telefone = :telefone, data = curDate(), endereco = :endereco, pix = :pix $nasc, numero = :numero, bairro = :bairro, cidade = :cidade, estado = :estado, cep = :cep, acessar_painel = '$acessar_painel', complemento = :complemento, tipo_chave = '$tipo_chave', foto = '$foto', empresa = '$id_empresa' ");


	//api whats
	if ($api_whatsapp != 'Não' and $telefone != '' and $acessar_painel == 'Sim') {
		$telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone);

		$mensagem_whatsapp = '*' . $nome_sistema . '*%0A%0A';
		$mensagem_whatsapp .= '_Olá ' . $nome . ' Você foi Cadastrado no Sistema!!_ %0A';
		$mensagem_whatsapp .= '*Email:* ' . $email . ' %0A';
		$mensagem_whatsapp .= '*Senha:* ' . $senha . ' %0A';
		$mensagem_whatsapp .= '*Url Acesso:* ' . $url_sistema . ' %0A%0A';
		$mensagem_whatsapp .= '_Após acessar seu painel, adicone uma foto e trocar a senha para uma de sua preferência!_';

		require('../../apis/texto.php');



	}


} else {
	$query = $pdo->prepare("UPDATE $tabela SET nome = :nome, email = :email, nivel = '$nivel', telefone = :telefone, endereco = :endereco, pix = :pix $nasc, numero = :numero, bairro = :bairro, cidade = :cidade, estado = :estado, cep = :cep, acessar_painel = '$acessar_painel', complemento = :complemento, tipo_chave = '$tipo_chave', foto = '$foto' where id = '$id'");
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
$query->bindValue(":complemento", "$complemento");
$query->execute();






//enviar email
if ($email != '') {
	$url_logo = $url_sistema . 'img/logo.png';
	$destinatario = $email;
	$assunto = 'Cadastrado no sistema ' . $nome_sistema;
	$mensagem_email = 'Olá ' . $nome . ' você foi cadastrado no sistema <br>';
	$mensagem_email .= '<b>Usuário</b>: ' . $email . '<br>';
	$mensagem_email .= '<b>Senha: </b>' . $senha . '<br><br>';
	$mensagem_email .= 'Url Acesso: <br><a href="' . $url_sistema . '">' . $url_sistema . '</a><br><br>';
	$mensagem_email .= '<i>Após acessar seu painel, adicone uma foto e trocar a senha para uma de sua preferência!</i>' . '<br><br>';
	$mensagem_email .= "<img src='" . $url_logo . "' width='200px'> ";
}


require('../../apis/disparar_email.php');

echo 'Salvo com Sucesso';

?>