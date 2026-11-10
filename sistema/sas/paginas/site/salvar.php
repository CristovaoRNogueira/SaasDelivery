<?php
$tabela = 'site';
require_once("../../../conexao.php");

@session_start();
$id_usuario = @$_SESSION['id'];


$id = @$_POST['id'];
$titulo = @$_POST['titulo'];
$subtitulo = @$_POST['subtitulo'];
$botao1 = @$_POST['botao1'];
$botao2 = @$_POST['botao2'];
$botao3 = @$_POST['botao3'];
$item1 = @$_POST['item1'];
$item2 = @$_POST['item2'];
$item3 = @$_POST['item3'];
$titulo_recursos = @$_POST['titulo_recursos'];
$titulo_perguntas = @$_POST['titulo_perguntas'];
$titulo_rodape = @$_POST['titulo_rodape'];
$link_rodape = @$_POST['link_rodape'];
$descricao_rodape = @$_POST['descricao_rodape'];
$botao_rodape = @$_POST['botao_rodape'];
$logo_topo = @$_POST['logo_topo'];

//validar troca da foto
$query = $pdo->query("SELECT * FROM $tabela where empresa = '0'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {
	$logo = $res[0]['logo'];	
	$fundo_topo = $res[0]['fundo_topo'];
	$fundo_topo_mobile = $res[0]['fundo_topo_mobile'];		
} else {
	$logo = 'sem-foto.png';	
	$fundo_topo = 'sem-foto.png';	
	$fundo_topo_mobile = 'sem-foto.png';	
}


// SCRIPT PARA SUBIR FOTO NO SERVIDOR
$nome_img = date('d-m-Y H:i:s') . '-' . @$_FILES['foto']['name'];
$nome_img = preg_replace('/[ :]+/', '-', $nome_img);

$caminho = '../../../img/logos/' . $nome_img;

$imagem_temp = @$_FILES['foto']['tmp_name'];

if (@$_FILES['foto']['name'] != "") {
	$ext = strtolower(pathinfo($nome_img, PATHINFO_EXTENSION)); // Converte a extensão para minúsculas
	$extensoes_permitidas = ['png', 'jpg', 'jpeg', 'gif', 'webp'];

	if (in_array($ext, $extensoes_permitidas)) {

		// EXCLUO A FOTO ANTERIOR
		if ($logo != "sem-foto.png") {
			@unlink('../../../img/logos/' . $logo);
		}

		$logo = $nome_img;

		// Se a largura não for maior que 1400, apenas move o arquivo
		move_uploaded_file($imagem_temp, $caminho);

		
	} else {
		echo 'Extensão de Imagem não permitida!';
		exit();
	}
}






// SCRIPT PARA SUBIR FOTO NO SERVIDOR
$nome_img = date('d-m-Y H:i:s') . '-' . @$_FILES['fundo_topo']['name'];
$nome_img = preg_replace('/[ :]+/', '-', $nome_img);

$caminho = '../../../img/logos/' . $nome_img;

$imagem_temp = @$_FILES['fundo_topo']['tmp_name'];

if (@$_FILES['fundo_topo']['name'] != "") {
	$ext = strtolower(pathinfo($nome_img, PATHINFO_EXTENSION)); // Converte a extensão para minúsculas
	$extensoes_permitidas = ['png', 'jpg', 'jpeg', 'gif', 'webp'];

	if (in_array($ext, $extensoes_permitidas)) {

		// EXCLUO A FOTO ANTERIOR
		if ($fundo_topo != "sem-foto.png") {
			@unlink('../../../img/logos/' . $fundo_topo);
		}

		$fundo_topo = $nome_img;

		// Se a largura não for maior que 1400, apenas move o arquivo
		move_uploaded_file($imagem_temp, $caminho);

		
	} else {
		echo 'Extensão de Imagem não permitida!';
		exit();
	}
}




// SCRIPT PARA SUBIR FOTO NO SERVIDOR
$nome_img = date('d-m-Y H:i:s') . '-' . @$_FILES['fundo_topo_mobile']['name'];
$nome_img = preg_replace('/[ :]+/', '-', $nome_img);

$caminho = '../../../img/logos/' . $nome_img;

$imagem_temp = @$_FILES['fundo_topo_mobile']['tmp_name'];

if (@$_FILES['fundo_topo_mobile']['name'] != "") {
	$ext = strtolower(pathinfo($nome_img, PATHINFO_EXTENSION)); // Converte a extensão para minúsculas
	$extensoes_permitidas = ['png', 'jpg', 'jpeg', 'gif', 'webp'];

	if (in_array($ext, $extensoes_permitidas)) {

		// EXCLUO A FOTO ANTERIOR
		if ($fundo_topo_mobile != "sem-foto.png") {
			@unlink('../../../img/logos/' . $fundo_topo_mobile);
		}

		$fundo_topo_mobile = $nome_img;

		// Se a largura não for maior que 1400, apenas move o arquivo
		move_uploaded_file($imagem_temp, $caminho);

		
	} else {
		echo 'Extensão de Imagem não permitida!';
		exit();
	}
}



if ($total_reg == 0) {
	$query = $pdo->prepare("INSERT INTO $tabela SET titulo = :titulo, subtitulo = :subtitulo, botao1 = :botao1, botao2 = :botao2, botao3 = :botao3, item1 = :item1, item2 = :item2, item3 = :item3, logo = '$logo', empresa = 0, titulo_recursos = :titulo_recursos, titulo_perguntas = :titulo_perguntas, titulo_rodape = :titulo_rodape, link_rodape = :link_rodape, descricao_rodape = :descricao_rodape, botao_rodape = :botao_rodape, fundo_topo = '$fundo_topo', logo_topo = '$logo_topo', fundo_topo_mobile = '$fundo_topo_mobile'");

} else {
	$query = $pdo->prepare("UPDATE $tabela SET titulo = :titulo, subtitulo = :subtitulo, botao1 = :botao1, botao2 = :botao2, botao3 = :botao3, item1 = :item1, item2 = :item2, item3 = :item3, logo = '$logo', titulo_recursos = :titulo_recursos, titulo_perguntas = :titulo_perguntas, titulo_rodape = :titulo_rodape, link_rodape = :link_rodape, descricao_rodape = :descricao_rodape, botao_rodape = :botao_rodape, fundo_topo = '$fundo_topo', logo_topo = '$logo_topo', fundo_topo_mobile = '$fundo_topo_mobile' where empresa = '$id'");
}
$query->bindValue(":titulo", $titulo);
$query->bindValue(":subtitulo", $subtitulo);
$query->bindValue(":botao1", $botao1);
$query->bindValue(":botao2", $botao2);
$query->bindValue(":botao3", $botao3);
$query->bindValue(":item1", $item1);
$query->bindValue(":item2", $item2);
$query->bindValue(":item3", $item3);
$query->bindValue(":titulo_recursos", $titulo_recursos);
$query->bindValue(":titulo_perguntas", $titulo_perguntas);
$query->bindValue(":titulo_rodape", $titulo_rodape);
$query->bindValue(":link_rodape", $link_rodape);
$query->bindValue(":descricao_rodape", $descricao_rodape);
$query->bindValue(":botao_rodape", $botao_rodape);
$query->execute();

echo 'Salvo com Sucesso';

