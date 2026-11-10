<?php
$tabela = 'receber_sas';
require_once("../../../conexao.php");

@session_start();
$id_usuario = @$_SESSION['id'];

$hora = date('H');

########## SAUDAÇÃO #############
if ($hora < 12 && $hora >= 6)
	$saudacao = "Bom Dia";

if ($hora >= 12 && $hora < 18)
	$saudacao = "Boa Tarde";

if ($hora >= 18 && $hora <= 23)
	$saudacao = "Boa Noite";
if ($hora < 6 && $hora >= 0)
	$saudacao = "Boa madrugada";


$descricao = $_POST['descricao'];
$valor = $_POST['valor'];
$cliente = $_POST['cliente'];
$vencimento = $_POST['vencimento'];
$data_pgto = $_POST['data_pgto'];
$forma_pgto = $_POST['forma_pgto'];
$frequencia = $_POST['frequencia'];
$obs = $_POST['obs'];
$id = $_POST['id'];
$quant_recorrencia = @$_POST['quant_recorrencia'];

if ($valor == "0,00") {
	echo 'Você precisa Adicionar um Valor!';
	exit();
}


//VALIDAÇÃO
if ($quant_recorrencia > 0 and $frequencia == '0') {
	echo 'Você precisa selecionar uma Frequência!';
	exit();
}

$valor = str_replace('.', '', $valor);
$valor = str_replace(',', '.', $valor);

$valorF = @number_format($valor, 2, ',', '.');

if ($cliente == "") {
	$cliente = 0;
}

if ($forma_pgto == "") {
	$forma_pgto = 0;
}

if ($frequencia == "") {
	$frequencia = 0;
}

if ($data_pgto == "") {
	$pgto = '';
	$usu_pgto = " ,usuario_pgto = '0' ";
	$pago = 'Não';
} else {
	$pgto = " ,data_pgto = '$data_pgto'";
	$usu_pgto = " ,usuario_pgto = '$id_usuario' ";
	$pago = 'Sim';
}

//validacao
if ($descricao == "" and $cliente == "0") {
	echo 'Selecione um Cliente ou adicone uma Descrição!';
	exit();
}

$query2 = $pdo->query("SELECT * FROM clientes where id = '$cliente'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
if (@count($res2) > 0) {
	$nome_cliente = $res2[0]['nome'];
	$telefone_cliente = $res2[0]['telefone'];
} else {
	$nome_cliente = 'Sem Registro';
	$telefone_cliente = "";
}

if ($descricao == "") {
	$descricao = $nome_cliente;
}

//validar troca da foto
$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {
	$foto = $res[0]['arquivo'];
	$quant_recorrencia_rec = $res[0]['quant_recorrencia'];
	$vencimento_antiga = $res[0]['vencimento'];
} else {
	$foto = 'sem-foto.png';
	$quant_recorrencia_rec = '';
	$vencimento_antiga = '';
}


// SCRIPT PARA SUBIR FOTO NO SERVIDOR
$nome_img = date('d-m-Y H:i:s') . '-' . @$_FILES['foto']['name'];
$nome_img = preg_replace('/[ :]+/', '-', $nome_img);

$caminho = '../../images/contas/' . $nome_img;

$imagem_temp = @$_FILES['foto']['tmp_name'];

if (@$_FILES['foto']['name'] != "") {
	$ext = strtolower(pathinfo($nome_img, PATHINFO_EXTENSION)); // Converte a extensão para minúsculas
	$extensoes_permitidas = ['png', 'jpg', 'jpeg', 'gif', 'pdf', 'rar', 'zip', 'doc', 'docx', 'webp', 'xlsx', 'xlsm', 'xls', 'xml'];

	if (in_array($ext, $extensoes_permitidas)) {

		// EXCLUO A FOTO ANTERIOR
		if ($foto != "sem-foto.png") {
			@unlink('../../images/contas/' . $foto);
		}

		$foto = $nome_img;

		// pegar o tamanho da imagem
		list($largura, $altura) = getimagesize($imagem_temp);

		// Redimensionar a imagem se a largura for maior que 1400
		if ($largura > 1400) {
			// Calcular a nova altura mantendo a proporção
			$nova_largura = 1400;
			$nova_altura = ($altura / $largura) * $nova_largura;

			// Criar uma nova imagem em branco
			$image = imagecreatetruecolor($nova_largura, $nova_altura);

			// Criar a imagem a partir do arquivo original
			if ($ext == 'png') {
				$imagem_original = imagecreatefrompng($imagem_temp);
				imagealphablending($image, false);
				imagesavealpha($image, true);
			} else if ($ext == 'jpeg' || $ext == 'jpg') {
				$imagem_original = imagecreatefromjpeg($imagem_temp);
			} else if ($ext == 'gif') {
				$imagem_original = imagecreatefromgif($imagem_temp);
			} else {
				die("Formato de imagem não suportado.");
			}

			// Redimensionar a imagem
			imagecopyresampled($image, $imagem_original, 0, 0, 0, 0, $nova_largura, $nova_altura, $largura, $altura);

			// Salvar a imagem com qualidade de 20%
			imagejpeg($image, $caminho, 20);
			imagedestroy($imagem_original);
			imagedestroy($image);
		} else {
			// Se a largura não for maior que 1400, apenas move o arquivo
			move_uploaded_file($imagem_temp, $caminho);
		}
	} else {
		echo 'Extensão de Imagem não permitida!';
		exit();
	}
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
	$query = $pdo->prepare("INSERT INTO $tabela SET descricao = :descricao, cliente = :cliente, valor = :valor, vencimento = '$vencimento' $pgto, data_lanc = curDate(), forma_pgto = '$forma_pgto', frequencia = '$frequencia', obs = :obs, arquivo = '$foto', subtotal = :valor, usuario_lanc = '$id_usuario' $usu_pgto, pago = '$pago', referencia = 'Conta', hora = curTime(), quant_recorrencia = :quant_recorrencia, caixa = '$id_caixa' ");


	$data_venc = $vencimento;

	################# CRIAR A PRÓXIMA CONTA A RECEBER CASO TENHA SIDO PAGA #################
	$dias_frequencia = $frequencia;

	if ($dias_frequencia == 30 || $dias_frequencia == 31) {
		$nova_data_vencimento = date('Y/m/d', strtotime("+1 month", strtotime($data_venc)));

	} else if ($dias_frequencia == 90) {
		$nova_data_vencimento = date('Y/m/d', strtotime("+3 month", strtotime($data_venc)));

	} else if ($dias_frequencia == 180) {
		$nova_data_vencimento = date('Y/m/d', strtotime("+6 month", strtotime($data_venc)));

	} else if ($dias_frequencia == 360 || $dias_frequencia == 365) {
		$nova_data_vencimento = date('Y/m/d', strtotime("+1 year", strtotime($data_venc)));

	} else {
		$nova_data_vencimento = date('Y/m/d', strtotime("+$dias_frequencia days", strtotime($data_venc)));
	}

	################# CRIAR A PRÓXIMA CONTA A RECEBER CASO TENHA SIDO PAGA #################
	if (@$dias_frequencia > 0 and $data_pgto != '' and $quant_recorrencia < 0) {


		$pdo->query("INSERT INTO $tabela set descricao = '$descricao', cliente = '$cliente', valor = '$valor', data_lanc = curDate(), vencimento = '$nova_data_vencimento', frequencia = '$frequencia', forma_pgto = '$forma_pgto', arquivo = '$foto', pago = 'Não', referencia = 'Conta', usuario_lanc = '$id_usuario', hora = curTime(), obs = '$obs', quant_recorrencia = '$quant_recorrencia'");
		$id_ult_registro = $pdo->lastInsertId();



		if ($api_whatsapp != 'Não' and $telefone_cliente != '') {


			$valorF = @number_format($valor, 2, ',', '.');
			$telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone_cliente);
			$nova_data_vencimentoF = implode('/', array_reverse(explode('-', $nova_data_vencimento)));


			############## AGENDAR MENSAGEM PARA RENOVAÇÃO ###########################
			$mensagem_whatsapp = '🔔_Lembrete Automático de Vencimento!_ %0A%0A';
			$mensagem_whatsapp .= $saudacao . ' tudo bem? 😀%0A%0A';
			$mensagem_whatsapp .= '_Queremos lembrar que você tem uma Conta Vencendo Hoje_ %0A%0A';
			$mensagem_whatsapp .= 'Empresa: *' . $nome_sistema . '* %0A';
			$mensagem_whatsapp .= 'Nome: *' . $nome_cliente . '* %0A';
			$mensagem_whatsapp .= 'Valor: *R$ ' . $valorF . '* %0A';
			$mensagem_whatsapp .= 'Data de Vencimento: *' . $nova_data_vencimentoF . '* %0A%0A';
			$mensagem_whatsapp .= '_Entre em contato conosco para acertar o pagamento!_ %0A%0A';

			if ($dados_pagamento != "") {
				$mensagem_whatsapp .= '*Dados para o Pagamento:* %0A';
				$mensagem_whatsapp .= $dados_pagamento;
			}

			$mensagem_whatsapp .= '%0A';
			$mensagem_whatsapp .= '🤖 _Esta é uma mensagem automática!_';

			$data_agd = $nova_data_vencimento . ' 09:00:00';

			require('../../apis/agendar.php');

			$pdo->query("UPDATE $tabela SET hash = '$hash' where id = '$id_ult_registro'");


		}


	}

} else {
	$query = $pdo->prepare("UPDATE $tabela SET descricao = :descricao, cliente = :cliente, valor = :valor, vencimento = '$vencimento' $pgto, forma_pgto = '$forma_pgto', frequencia = '$frequencia', obs = :obs, arquivo = '$foto', subtotal = :valor, quant_recorrencia = :quant_recorrencia where id = '$id'");


	if ($vencimento_antiga != $vencimento) {


		$query4 = $pdo->query("SELECT * FROM $tabela where id = '$id'");
		$res4 = $query4->fetchAll(PDO::FETCH_ASSOC);
		$hash = @$res4[0]['hash'];

		if ($hash != "") {
			require("../../apis/cancelar_agendamento.php");
		}

		$vencimentoF = implode('/', array_reverse(explode('-', $vencimento)));


		//enviar whatsapp
		if ($api_whatsapp != 'Não' and $telefone_cliente != '') {


			$telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone_cliente);

			############## AGENDAR MENSAGEM PARA RENOVAÇÃO ###########################
			$mensagem_whatsapp = '🔔_Lembrete Automático de Vencimento!_ %0A%0A';
			$mensagem_whatsapp .= $saudacao . ' tudo bem? 😀%0A%0A';
			$mensagem_whatsapp .= '_Queremos lembrar que você tem uma Conta Vencendo Hoje_ %0A%0A';
			$mensagem_whatsapp .= 'Empresa: *' . $nome_sistema . '* %0A';
			$mensagem_whatsapp .= 'Nome: *' . $nome_cliente . '* %0A';
			$mensagem_whatsapp .= 'Valor: *R$ ' . $valorF . '* %0A';
			$mensagem_whatsapp .= 'Data de Vencimento: *' . $vencimentoF . '* %0A%0A';
			$mensagem_whatsapp .= '_Entre em contato conosco para acertar o pagamento!_ %0A%0A';


			if ($dados_pagamento != "") {
				$mensagem_whatsapp .= '*Dados para o Pagamento:* %0A';
				$mensagem_whatsapp .= $dados_pagamento;
			}

			$mensagem_whatsapp .= '%0A';
			$mensagem_whatsapp .= '🤖 _Esta é uma mensagem automática!_';

			$data_agd = $vencimento . ' 09:00:00';
			require('../../apis/agendar.php');

			$pdo->query("UPDATE $tabela SET hash = '$hash' where id = '$id'");

		}

	}

}


$query->bindValue(":descricao", "$descricao");
$query->bindValue(":cliente", "$cliente");
$query->bindValue(":valor", "$valor");
$query->bindValue(":obs", "$obs");
$query->bindValue(":quant_recorrencia", "$quant_recorrencia");
$query->execute();
$ultimo_id = $pdo->lastInsertId();

$vencimentoF = implode('/', array_reverse(explode('-', $vencimento)));

//enviar whatsapp
if ($api_whatsapp != 'Não' and $telefone_cliente != '' and $data_pgto == '' and $id == '') {

	$telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone_cliente);


	############## AGENDAR MENSAGEM PARA RENOVAÇÃO ###########################
	$mensagem_whatsapp = '🔔_Lembrete Automático de Vencimento!_ %0A%0A';
	$mensagem_whatsapp .= $saudacao . ' tudo bem? 😀%0A%0A';
	$mensagem_whatsapp .= '_Queremos lembrar que você tem uma Conta Vencendo Hoje_ %0A%0A';
	$mensagem_whatsapp .= 'Empresa: *' . $nome_sistema . '* %0A';
	$mensagem_whatsapp .= 'Nome: *' . $nome_cliente . '* %0A';
	$mensagem_whatsapp .= 'Valor: *R$ ' . $valorF . '* %0A';
	$mensagem_whatsapp .= 'Data de Vencimento: *' . $vencimentoF . '* %0A%0A';
	$mensagem_whatsapp .= '_Entre em contato conosco para acertar o pagamento!_ %0A%0A';


	if ($dados_pagamento != "") {
		$mensagem_whatsapp .= '*Dados para o Pagamento:* %0A';
		$mensagem_whatsapp .= $dados_pagamento;
	}

	$mensagem_whatsapp .= '%0A';
	$mensagem_whatsapp .= '🤖 _Esta é uma mensagem automática!_';

	$data_agd = $vencimento . ' 09:00:00';
	require('../../apis/agendar.php');

	$pdo->query("UPDATE $tabela SET hash = '$hash' where id = '$ultimo_id'");

}








############ CRIAR AS RECORRÊNCIAS########################
if ($quant_recorrencia > 0) {

	############ EXCLUIR AS RECORRENCIA SE JÁ EXISTIR ###########3
	if ($id > 0) {
		$ultimo_id = $id;
		$query = $pdo->query("SELECT * from $tabela where id_recorrencia = '$id' and pago != 'Sim'");
		$res = $query->fetchAll(PDO::FETCH_ASSOC);
		$total_reg = @count($res);
		if ($total_reg > 0) {
			for ($i = 0; $i < $total_reg; $i++) {
				$hash = @$res[$i]['hash'];
				$id_conta = @$res[$i]['id'];

				if ($hash != "") {
					require("../../apis/cancelar_agendamento.php");
				}
				$pdo->query("DELETE FROM $tabela WHERE id = '$id_conta'");

			}
		}

	}


	$qtd_parcelas = $quant_recorrencia;

	$dias_frequencia = $frequencia;

	for ($i = 2; $i <= $qtd_parcelas; $i++) {
		$data_venc = $vencimento;

		$nova_descricao = $descricao;
		$novo_valor = $valor;
		$dias_parcela = $i - 1;
		$dias_parcela_2 = ($i - 1) * $dias_frequencia;


		if ($dias_frequencia == 30 || $dias_frequencia == 31) {

			$novo_vencimento = date('Y/m/d', strtotime("+$dias_parcela month", strtotime($data_venc)));

		} else if ($dias_frequencia == 90) {
			$dias_parcela = $dias_parcela * 3;
			$novo_vencimento = date('Y/m/d', strtotime("+$dias_parcela month", strtotime($data_venc)));

		} else if ($dias_frequencia == 180) {

			$dias_parcela = $dias_parcela * 6;
			$novo_vencimento = date('Y/m/d', strtotime("+$dias_parcela month", strtotime($data_venc)));

		} else if ($dias_frequencia == 360 || $dias_frequencia == 365) {

			$dias_parcela = $dias_parcela * 12;
			$novo_vencimento = date('Y/m/d', strtotime("+$dias_parcela month", strtotime($data_venc)));

		} else {

			$novo_vencimento = date('Y/m/d', strtotime("+$dias_parcela_2 days", strtotime($data_venc)));
		}




		$query = $pdo->prepare("INSERT INTO $tabela SET descricao = :descricao, cliente = '$cliente', valor = :valor, vencimento = '$novo_vencimento', data_lanc = curDate(), frequencia = '0', forma_pgto = '$forma_pgto', obs = :obs, arquivo = '$foto', subtotal = :valor, usuario_lanc = '$id_usuario', pago = 'Não', referencia = 'Conta', hora = curTime() $usu_pgto, id_recorrencia = '$ultimo_id' ");



		$query->bindValue(":descricao", "$descricao");
		$query->bindValue(":cliente", "$cliente");
		$query->bindValue(":valor", "$valor");
		$query->bindValue(":obs", "$obs");
		$query->execute();
		$ultimo_id_rec = $pdo->lastInsertId();



		//enviar whatsapp
		if ($api_whatsapp != 'Não' and $cliente > 0 and $telefone_cliente != '') {


			$valorF = @number_format($valor, 2, ',', '.');
			$nova_data_vencimentoF = implode('/', array_reverse(explode('-', $nova_data_vencimento)));
			$telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone_cliente);


			############## AGENDAR MENSAGEM PARA RENOVAÇÃO ###########################
			$mensagem_whatsapp = '🔔_Lembrete Automático de Vencimento!_ %0A%0A';
			$mensagem_whatsapp .= $saudacao . ' tudo bem? 😀%0A%0A';
			$mensagem_whatsapp .= '_Queremos lembrar que você tem uma Conta Vencendo Hoje_ %0A%0A';
			$mensagem_whatsapp .= 'Empresa: *' . $nome_sistema . '* %0A';
			$mensagem_whatsapp .= 'Nome: *' . $nome_cliente . '* %0A';
			$mensagem_whatsapp .= 'Valor: *R$ ' . $valorF . '* %0A';
			$mensagem_whatsapp .= 'Data de Vencimento: *' . $novo_vencimento . '* %0A%0A';
			$mensagem_whatsapp .= '_Entre em contato conosco para acertar o pagamento!_ %0A%0A';

			if ($dados_pagamento != "") {
				$mensagem_whatsapp .= '*Dados para o Pagamento:* %0A';
				$mensagem_whatsapp .= $dados_pagamento;
			}

			$mensagem_whatsapp .= '%0A';
			$mensagem_whatsapp .= '🤖 _Esta é uma mensagem automática!_';

			$data_agd = $novo_vencimento . ' 08:00:00';
			require('../../apis/agendar.php');

			$pdo->query("UPDATE $tabela SET hash = '$hash' where id = '$ultimo_id_rec'");

		}

	}

}


echo 'Salvo com Sucesso';
