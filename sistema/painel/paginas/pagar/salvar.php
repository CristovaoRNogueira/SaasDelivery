<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, refaça o Login!';
	exit();
}
$tabela = 'pagar';


$referencia_agendamento = 'pagar';

$id_usuario = @$_SESSION['id'];

$descricao = $_POST['descricao'];
$valor = $_POST['valor'];
$fornecedor = $_POST['fornecedor'];
$funcionario = $_POST['funcionario'];
$vencimento = $_POST['vencimento'];
$data_pgto = $_POST['data_pgto'];
$forma_pgto = $_POST['forma_pgto'];
$frequencia = $_POST['frequencia'];
$obs = $_POST['obs'];
$id = $_POST['id'];


$hora = date('H');

if ($hora < 12 && $hora >= 6)
	$saudacao = "Bom Dia";
if ($hora >= 12 && $hora < 18)
	$saudacao = "Boa Tarde";
if ($hora >= 18 && $hora <= 23)
	$saudacao = "Boa Noite";
if ($hora < 6 && $hora >= 0)
	$saudacao = "Boa madrugada";

$valor = str_replace(',', '.', $valor);

$valorF = @number_format($valor, 2, ',', '.');

if ($fornecedor == "") {
	$fornecedor = 0;
}

if ($funcionario == "") {
	$funcionario = 0;
}

if ($forma_pgto == "") {
	$forma_pgto = 0;
}

if ($frequencia == "") {
	$frequencia = 0;
}



//validacao
if ($descricao == "" and $fornecedor == "0" and $funcionario == "0") {
	echo 'Selecione um Fornecedor ou um Funcionário ou uma Descrição!';
	exit();
}

if ($fornecedor != "0" and $funcionario != "0") {
	echo 'Selecione um Fornecedor ou um Funcionário!';
	exit();
}



//validar troca da foto
$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {
	$foto = $res[0]['arquivo'];
	$vencimento_antiga = $res[0]['vencimento'];
} else {
	$foto = 'sem-foto.png';
	$vencimento_antiga = '';
}



//SCRIPT PARA SUBIR FOTO NO SERVIDOR
$nome_img = date('d-m-Y H:i:s') . '-' . @$_FILES['foto']['name'];
$nome_img = preg_replace('/[ :]+/', '-', $nome_img);

$caminho = '../../images/contas/' . $nome_img;

$imagem_temp = @$_FILES['foto']['tmp_name'];

if (@$_FILES['foto']['name'] != "") {
	$ext = pathinfo($nome_img, PATHINFO_EXTENSION);
	if ($ext == 'png' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'pdf' or $ext == 'rar' or $ext == 'zip' or $ext == 'doc' or $ext == 'docx' or $ext == 'webp' or $ext == 'PNG' or $ext == 'JPG' or $ext == 'JPEG' or $ext == 'GIF' or $ext == 'PDF' or $ext == 'RAR' or $ext == 'ZIP' or $ext == 'DOC' or $ext == 'DOCX' or $ext == 'WEBP' or $ext == 'xlsx' or $ext == 'xlsm' or $ext == 'xls' or $ext == 'xml') {

		//EXCLUO A FOTO ANTERIOR
		if ($foto != "sem-foto.png") {
			@unlink('../../images/contas/' . $foto);
		}

		$foto = $nome_img;

		//pegar o tamanho da imagem
		list($largura, $altura) = getimagesize($imagem_temp);
		if ($largura > 1400) {
			echo 'Diminua a imagem para um tamanho de no máximo 1400px de largura!';
			exit();
		} else {
			move_uploaded_file($imagem_temp, $caminho);
		}
	} else {
		echo 'Extensão de Imagem não permitida!';
		exit();
	}
}



if ($fornecedor != 0 || $funcionario != 0) {
	if ($fornecedor != 0) {
		$tab = 'fornecedores';
		$id_pessoa = $fornecedor;
	}

	if ($funcionario != 0) {
		$tab = 'usuarios';
		$id_pessoa = $funcionario;
	}

	//nome pessoa
	$query = $pdo->query("SELECT * FROM $tab where id = '$id_pessoa'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if ($total_reg > 0) {
		$nome_pessoa = $res[0]['nome'];
	} else {
		$nome_pessoa = '';
	}

	if ($descricao == "") {
		$descricao = $nome_pessoa;
	}
}


if ($data_pgto == "") {
	$pgto = '';
	$usu_pgto = '';
	$pago = 'Não';
} else {
	$pgto = " ,data_pgto = '$data_pgto'";
	$usu_pgto = " ,usuario_pgto = '$id_usuario'";
	$pago = 'Sim';
}


//verificar caixa aberto
$query1 = $pdo->query("SELECT * from caixas where operador = '$id_usuario' and data_fechamento is null order by id desc limit 1");
$res1 = $query1->fetchAll(PDO::FETCH_ASSOC);
if (@count($res1) > 0) {
	$id_caixa = @$res1[0]['id'];
} else {
	$id_caixa = 0;
}
//  


if ($id == "") {
	$query = $pdo->prepare("INSERT INTO $tabela SET descricao = :descricao, fornecedor = '$fornecedor', funcionario = '$funcionario', valor = :valor, vencimento = '$vencimento' $pgto, data_lanc = curDate(), forma_pgto = '$forma_pgto', frequencia = '$frequencia', obs = :obs, arquivo = '$foto', foto = '$foto', subtotal = :valor, usuario_lanc = '$id_usuario' $usu_pgto, pago = '$pago', referencia = 'Conta', caixa = '$id_caixa', hora = curTime(), empresa = '$id_empresa' ");
	


	################# CRIAR A PRÓXIMA CONTA A PAGAR CASO TENHA SIDO PAGA #################
	$dias_frequencia = $frequencia;


	if ($dias_frequencia == 30 || $dias_frequencia == 31) {
		$nova_data_vencimento = date('Y/m/d', strtotime("+1 month", strtotime($vencimento)));
	} else if ($dias_frequencia == 90) {
		$nova_data_vencimento = date('Y/m/d', strtotime("+3 month", strtotime($vencimento)));
	} else if ($dias_frequencia == 180) {
		$nova_data_vencimento = date('Y/m/d', strtotime("+6 month", strtotime($vencimento)));
	} else if ($dias_frequencia == 360 || $dias_frequencia == 365) {
		$nova_data_vencimento = date('Y/m/d', strtotime("+1 year", strtotime($vencimento)));
	} else {
		$nova_data_vencimento = date('Y/m/d', strtotime("+$dias_frequencia days", strtotime($vencimento)));
	}


	if(@$dias_frequencia > 0 and $data_pgto != ''){


		$pdo->query("INSERT INTO $tabela SET descricao = '$descricao', fornecedor = '$fornecedor', funcionario = '$funcionario', valor = '$valor' $pgto, data_lanc = curDate(), vencimento = '$nova_data_vencimento', forma_pgto = '$forma_pgto', frequencia = '$frequencia', obs = '$obs', arquivo = '$foto', foto = '$foto', subtotal = '$valor', usuario_lanc = '$id_usuario', referencia = 'Conta', caixa = '$id_caixa', hora = curTime(), pago = 'Não', empresa = '$id_empresa' ");

		$id_ultimo = $pdo->lastInsertId();


		//enviar whatsapp
		if ($api_whatsapp != 'Não' and $telefone_sistema != '') {

			$mensagem_whatsapp = '*Lembrete Automático de Vencimento!* 🤖 %0A%0A';

			$mensagem_whatsapp .= $saudacao . ' 😀%0A%0A';

			$telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone_sistema);
			$mensagem_whatsapp .= '*' . $nome_sistema . '*%0A';
			$mensagem_whatsapp .= '_Queremos lembrar que sua Conta Vencendo Hoje_ %0A';
			$mensagem_whatsapp .= '*Descrição:* ' . $descricao . ' %0A';
			$mensagem_whatsapp .= '*Valor:* ' . $valorF . ' %0A';

			$data_agd = $nova_data_vencimento . ' 09:00:00';
			$ultimo_id = $id_ultimo;
			$referencia_agendamento = 'pagar';
			require('../../apis/agendar.php');

			$pdo->query("UPDATE $tabela SET hash = '$hash' where id = '$id_ultimo'");
		}
	}

} else {
	$query = $pdo->prepare("UPDATE $tabela SET descricao = :descricao, fornecedor = '$fornecedor', funcionario = '$funcionario', valor = :valor, vencimento = '$vencimento' $pgto, forma_pgto = '$forma_pgto', frequencia = '$frequencia', obs = :obs, arquivo = '$foto', foto = '$foto', subtotal = :valor where id = '$id'");
	$id_ultimo_alt = $pdo->lastInsertId();


//AGENDA NOVA MSG SE A DATA DE VENICIMENTO FOR DIFERENTE DA ANTERIOR
	if ($vencimento_antiga != $vencimento) {


		$query4 = $pdo->query("SELECT * FROM $tabela where id = '$id'");
		$res4 = $query4->fetchAll(PDO::FETCH_ASSOC);
		$hash = @$res4[0]['hash'];

		$referencia_agendamento = 'pagar';
		if ($hash != "") {
			require("../../apis/cancelar_agendamento.php");
		}


		//enviar whatsapp
		if ($api_whatsapp != 'Não' and $telefone_sistema != '') {

			$telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone_sistema);

			$mensagem_whatsapp = '*Lembrete Automático de Vencimento! 🤖* %0A%0A';
			$mensagem_whatsapp .= $saudacao . ' 😀%0A%0A';
			$mensagem_whatsapp .= '*' . $nome_sistema . '*%0A';
			$mensagem_whatsapp .= '_Queremos lembrar que sua Conta Vencendo Hoje_ %0A';
			$mensagem_whatsapp .= '*Descrição:* ' . $descricao . ' %0A';
			$mensagem_whatsapp .= '*Valor:* ' . $valorF . ' %0A';

			$data_agd = $vencimento . ' 09:00:00';
			$ultimo_id = $id;
			$referencia_agendamento = 'pagar';
			require('../../apis/agendar.php');

			$pdo->query("UPDATE $tabela SET hash = '$hash' where id = '$id_ultimo_alt'");
		}


	}
}
$query->bindValue(":descricao", "$descricao");
$query->bindValue(":valor", "$valor");
$query->bindValue(":obs", "$obs");
$query->execute();
$ultimo_id = $pdo->lastInsertId();



	//enviar whatsapp
	if ($api_whatsapp != 'Não' and $telefone_sistema != '' and $id == "" and $data_pgto == "") {

		$mensagem_whatsapp = '*Lembrete Automático de Vencimento! 🤖* %0A%0A';

		$mensagem_whatsapp .= $saudacao . ' 😀%0A%0A';

		$telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone_sistema);
		$mensagem_whatsapp .= '*' . $nome_sistema . '*%0A';
		$mensagem_whatsapp .= '_Queremos lembrar que sua Conta Vencendo Hoje_ %0A';
		$mensagem_whatsapp .= '*Descrição:* ' . $descricao . ' %0A';
		$mensagem_whatsapp .= '*Valor:* ' . $valorF . ' %0A';

		$data_agd = $vencimento . ' 09:00:00';

		$referencia_agendamento = 'pagar';
		require('../../apis/agendar.php');

		$pdo->query("UPDATE $tabela SET hash = '$hash' where id = '$ultimo_id'");
	}


echo 'Salvo com Sucesso';
