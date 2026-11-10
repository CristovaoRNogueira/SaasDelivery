<?php
$tabela = 'usuarios';
require_once("../../../conexao.php");

if($modo_teste == 'Sim'){
	echo 'Em modo de teste esse recurso fica desabilitado!';
	exit();
}

$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$nivel = $_POST['nivel'];
$endereco = $_POST['endereco'];
$id = $_POST['id'];
$mostrar_registros = $_POST['mostrar_registros'];
$numero = @ $_POST['numero'];
$bairro = @$_POST['bairro'];
$cidade = @$_POST['cidade'];
$estado = @$_POST['estado'];
$cep = @$_POST['cep'];
$data_nasc = @$_POST['data_nasc'];
$cpf = @$_POST['cpf'];
$pix = @$_POST['pix'];
$acessar_painel = @$_POST['acessar_painel'];
$complemento = $_POST['complemento'];
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


$senha = '123';
$senha_crip = password_hash($senha, PASSWORD_DEFAULT);

//validacao email
$query = $pdo->query("SELECT * from $tabela where email = '$email'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$id_reg = @$res[0]['id'];
if (@count($res) > 0 and $id != $id_reg) {
    echo 'Email já Cadastrado!';
    exit();
}

//validacao telefone
$query = $pdo->query("SELECT * from $tabela where telefone = '$telefone'");
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



// SCRIPT PARA SUBIR FOTO NO SERVIDOR
// REDIMECIONAR A IMAGEM PARA 700PX DE LARGURA E REDUZIR A QUALIDADE PARA 20%
$nome_img = date('d-m-Y H:i:s') . '-' . @$_FILES['foto']['name'];
$nome_img = preg_replace('/[ :]+/', '-', $nome_img);
$caminho = '../../images/perfil/' . $nome_img;
$imagem_temp = @$_FILES['foto']['tmp_name'];
if (@$_FILES['foto']['name'] != "") {
	$ext = strtolower(pathinfo($nome_img, PATHINFO_EXTENSION));
	// Suporte a WebP incluído
	if (in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'PNG', 'JPG', 'JPEG', 'GIF', 'WEBP'])) {
		// EXCLUI A FOTO ANTERIOR
		if ($foto != "sem-foto.png") {
			@unlink('../../images/perfil/' . $foto);
		}
		$foto = $nome_img;
		// PEGA O TAMANHO ORIGINAL DA IMAGEM
		list($largura, $altura) = getimagesize($imagem_temp);
		// APENAS REDIMENSIONA SE A LARGURA FOR MAIOR QUE 1400
		if ($largura > 1400) {
			// CALCULA A NOVA ALTURA MANTENDO A PROPORÇÃO
			$nova_largura = 1400;
			$nova_altura = ($altura / $largura) * $nova_largura;
			// CRIA UMA NOVA IMAGEM EM BRANCO
			$nova_imagem = imagecreatetruecolor($nova_largura, $nova_altura);
			// MANTÉM A TRANSPARÊNCIA PARA PNG, GIF E WEBP
			if (in_array($ext, ['png', 'gif', 'webp'])) {
				imagealphablending($nova_imagem, false);
				imagesavealpha($nova_imagem, true);
				$fundo_transparente = imagecolorallocatealpha($nova_imagem, 0, 0, 0, 127);
				imagefill($nova_imagem, 0, 0, $fundo_transparente);
			}
			// CARREGA A IMAGEM ORIGINAL
			if ($ext == 'png') {
				$imagem_original = imagecreatefrompng($imagem_temp);
			} elseif ($ext == 'jpeg' || $ext == 'jpg') {
				$imagem_original = imagecreatefromjpeg($imagem_temp);
			} elseif ($ext == 'gif') {
				$imagem_original = imagecreatefromgif($imagem_temp);
			} elseif ($ext == 'webp') {
				$imagem_original = imagecreatefromwebp($imagem_temp);
			} else {
				die("Formato de imagem não suportado.");
			}
			// REDIMENSIONA A IMAGEM
			imagecopyresampled($nova_imagem, $imagem_original, 0, 0, 0, 0, $nova_largura, $nova_altura, $largura, $altura);
			// SALVA A NOVA IMAGEM MANTENDO A TRANSPARÊNCIA OU QUALIDADE
			if ($ext == 'png') {
				imagepng($nova_imagem, $caminho, 9); // Salva PNG com compressão máxima
			} elseif ($ext == 'gif') {
				imagegif($nova_imagem, $caminho);
			} elseif ($ext == 'webp') {
				imagewebp($nova_imagem, $caminho, 80); // Salva WebP com qualidade de 80%
			} else {
				imagejpeg($nova_imagem, $caminho, 20); // Salva JPEG com 20% de qualidade
			}
			// LIBERA A MEMÓRIA
			imagedestroy($imagem_original);
			imagedestroy($nova_imagem);
		} else {
			// SE A LARGURA FOR MENOR OU IGUAL A 1400, APENAS MOVE A IMAGEM SEM REDIMENSIONAR
			move_uploaded_file($imagem_temp, $caminho);
		}
	} else {
		echo 'Extensão de Imagem não permitida!';
		exit();
	}
}





if ($id == "") {
    $query = $pdo->prepare("INSERT INTO $tabela SET nome = :nome, email = :email, senha = '', senha_crip = '$senha_crip', nivel = '$nivel', ativo = 'Sim', foto = '$foto', telefone = :telefone, data = curDate(), endereco = :endereco, mostrar_registros = :mostrar_registros, numero = :numero, bairro = :bairro, cidade = :cidade, estado = :estado, cep = :cep $nasc, cpf = :cpf, pix = :pix, acessar_painel = 'Sim', complemento = :complemento, tipo_chave = '$tipo_chave', empresa = 0");

//enviar whatsapp
    if ($api_whatsapp != 'Não' and $telefone != '') {
        $telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone);
        $mensagem_whatsapp = '*' . $nome_sistema . '*%0A%0A';
        $mensagem_whatsapp .= '_Olá ' . $nome . ' Você foi Cadastrado no Sistema!!_ %0A';
        $mensagem_whatsapp .= '*Email:* ' . $email . ' %0A';
        $mensagem_whatsapp .= '*Senha:* ' . $senha . ' %0A';
        $mensagem_whatsapp .= '*Url Acesso:* %0A' . $url_sistema . 'login %0A%0A';
        $mensagem_whatsapp .= '_Após acessar seu painel, adicone uma foto e trocar a senha para uma de sua preferência!_';
        $mensagem = $mensagem_whatsapp;
        require('../../apis/texto.php');
    }

//enviar email
    if ($email != '') {
        $url_logo = $url_sistema . 'img/logo.png';
        $destinatario = $email;
        $assunto = 'Cadastrado no sistema ' . $nome_sistema;
        $mensagem_email = 'Olá ' . $nome . ' você foi cadastrado no sistema <br>';
        $mensagem_email .= '<b>Usuário</b>: ' . $email . '<br>';
        $mensagem_email .= '<b>Senha: </b>' . $senha . '<br><br>';
        $mensagem_email .= 'Url Acesso: <br><a href="' . $url_sistema . 'login">' . $url_sistema . 'login</a><br><br>';
        $mensagem_email .= '<i>Após acessar seu painel, adicone uma foto e trocar a senha para uma de sua preferência!</i>' . '<br><br>';
        $mensagem_email .= "<img src='" . $url_logo . "' width='200px'> ";
    }


    require('../../apis/disparar_email.php');

} else {
    $query = $pdo->prepare("UPDATE $tabela SET nome = :nome, email = :email, nivel = '$nivel', telefone = :telefone, endereco = :endereco, mostrar_registros = :mostrar_registros, numero = :numero, bairro = :bairro, cidade = :cidade, estado = :estado, cep = :cep $nasc, cpf = :cpf, pix = :pix, acessar_painel = 'Sim', complemento = :complemento, tipo_chave = '$tipo_chave', foto = '$foto' where id = '$id'");
}
$query->bindValue(":nome", "$nome");
$query->bindValue(":email", "$email");
$query->bindValue(":telefone", "$telefone");
$query->bindValue(":endereco", "$endereco");
$query->bindValue(":mostrar_registros", "$mostrar_registros");
$query->bindValue(":numero", "$numero");
$query->bindValue(":bairro", "$bairro");
$query->bindValue(":cidade", "$cidade");
$query->bindValue(":estado", "$estado");
$query->bindValue(":cep", "$cep");
$query->bindValue(":cpf", "$cpf");
$query->bindValue(":pix", "$pix");
$query->bindValue(":complemento", "$complemento");
$query->execute();

echo 'Salvo com Sucesso';
?>