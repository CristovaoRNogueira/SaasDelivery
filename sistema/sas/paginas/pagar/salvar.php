<?php
$tabela = 'pagar_sas';
require_once("../../../conexao.php");

@session_start();
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
$quant_recorrencia = @$_POST['quant_recorrencia'];
$recorrencia_inf = @$_POST['recorrencia_inf'];

if ($valor == "0,00") {
	echo 'Você precisa Adicionar um Valor!';
	exit();
}

// 2. Converte a vírgula para ponto e remove os pontos de milhar
$valor = str_replace(',', '.', str_replace('.', '', $valor));

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

//VALIDAÇÃO
if ($quant_recorrencia > 0 and $frequencia == '0') {
	echo 'Você precisa selecionar uma Frequência!';
	exit();
}

if ($recorrencia_inf == 'Sim' and $frequencia == '0') {
	echo 'Você precisa selecionar uma Frequência!';
	exit();
}


// ADICIONAR O NOME NA DESCRIÇÃO DA CONTA
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


//validar troca da foto
$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {
	$foto = $res[0]['arquivo'];
	$quant_recorrencia_rec = $res[0]['quant_recorrencia'];
	$recorrencia_inf_rec = $res[0]['recorrencia_inf'];
	$vencimento_antiga = $res[0]['vencimento'];
} else {
	$foto = 'sem-foto.png';
	$quant_recorrencia_rec = '';
	$recorrencia_inf_rec = '';
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


if ($data_pgto == "" and $data_pgto != "0000-00-00") {
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
	$query = $pdo->prepare("INSERT INTO $tabela SET descricao = :descricao, fornecedor = :fornecedor, funcionario = :funcionario, valor = :valor, vencimento = '$vencimento' $pgto, data_lanc = curDate(), forma_pgto = '$forma_pgto', frequencia = '$frequencia', obs = :obs, arquivo = '$foto', subtotal = :valor, usuario_lanc = '$id_usuario' $usu_pgto, pago = '$pago', referencia = 'Conta', caixa = '$id_caixa', hora = curTime() , quant_recorrencia = :quant_recorrencia, recorrencia_inf = :recorrencia_inf");


	################# CRIAR A PRÓXIMA CONTA A PAGAR CASO TENHA SIDO PAGA #################
	$dias_frequencia = $frequencia;

	if ($dias_frequencia == 30 || $dias_frequencia == 31) {
		$nova_data_vencimento = date('Y/m/d', strtotime("+1 month", strtotime($vencimento)));

	} else if ($dias_frequencia == 90) {
		$nova_data_vencimento = date('Y/m/d', strtotime("+3 month", strtotime($vencimento)));

	} else if ($dias_frequencia == 180) {
		$nova_data_vencimento = date('Y/m/d', strtotime("+6 month", strtotime($vencimento)));

	} else if ($dias_frequencia == 360 || $dias_frequencia == 365) {
		date('Y/m/d', strtotime("+6 month", strtotime($vencimento)));
	} else {
		$nova_data_vencimento = date('Y/m/d', strtotime("+$dias_frequencia days", strtotime($vencimento)));
	}


	if (@$dias_frequencia > 0 and $data_pgto != '' and $quant_recorrencia < 0 and $recorrencia_inf != 'Sim') {

		$pdo->query("INSERT INTO $tabela set descricao = '$descricao', fornecedor = '$fornecedor', funcionario = '$funcionario', valor = '$valor', data_lanc = curDate(), vencimento = '$nova_data_vencimento', frequencia = '$frequencia', forma_pgto = '$forma_pgto', arquivo = '$foto', pago = 'Não', referencia = 'Conta', usuario_lanc = '$id_usuario', hora = curTime(), obs = '$obs', caixa = '$id_caixa', quant_recorrencia = '$quant_recorrencia', recorrencia_inf = '$recorrencia_inf'");
		$id_ult_registro = $pdo->lastInsertId();

		//enviar whatsapp
		if ($api_whatsapp != 'Não' and $telefone_sistema != '') {
			$telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone_sistema);
			$mensagem_whatsapp = '💰 *' . $nome_sistema . '*%0A';
			$mensagem_whatsapp .= '_Conta Vencendo Hoje_ %0A';
			$mensagem_whatsapp .= '*Descrição:* ' . $descricao . ' %0A';
			$mensagem_whatsapp .= '*Valor:* ' . $valorF . ' %0A';
			$data_agd = $nova_data_vencimento . ' 09:00:00';
			require('../../apis/agendar.php');
			$pdo->query("UPDATE $tabela SET hash = '$hash' where id = '$id_ult_registro'");
		}
	}



} else {
	$query = $pdo->prepare("UPDATE $tabela SET descricao = :descricao, fornecedor = :fornecedor, funcionario = :funcionario, valor = :valor, vencimento = '$vencimento' $pgto, forma_pgto = '$forma_pgto', frequencia = '$frequencia', obs = :obs, arquivo = '$foto', subtotal = :valor, quant_recorrencia = :quant_recorrencia, recorrencia_inf = :recorrencia_inf where id = '$id'");



	if ($vencimento_antiga != $vencimento) {


		$query4 = $pdo->query("SELECT * FROM $tabela where id = '$id'");
		$res4 = $query4->fetchAll(PDO::FETCH_ASSOC);
		$hash = @$res4[0]['hash'];

		if ($hash != "") {
			require("../../apis/cancelar_agendamento.php");
		}

		//enviar whatsapp
		if ($api_whatsapp != 'Não' and $telefone_sistema != '') {

			$telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone_sistema);

			$mensagem_whatsapp = '💰 *' . $nome_sistema . '*%0A';
			$mensagem_whatsapp .= '_Você tem uma Conta Vencendo Hoje_ %0A';
			$mensagem_whatsapp .= '*Descrição:* ' . $descricao . ' %0A';
			$mensagem_whatsapp .= '*Valor:* ' . $valorF . ' %0A';

			$data_agd = $vencimento . ' 09:00:00';
			require('../../apis/agendar.php');

			$pdo->query("UPDATE $tabela SET hash = '$hash' where id = '$id'");

		}

	}


}

$query->bindValue(":descricao", "$descricao");
$query->bindValue(":fornecedor", "$fornecedor");
$query->bindValue(":funcionario", "$funcionario");
$query->bindValue(":valor", "$valor");
$query->bindValue(":obs", "$obs");
$query->bindValue(":quant_recorrencia", "$quant_recorrencia");
$query->bindValue(":recorrencia_inf", "$recorrencia_inf");
$query->execute();
$ultimo_id = $pdo->lastInsertId();



if ($id == "" and $data_pgto == "") {

	//enviar whatsapp
	if ($api_whatsapp != 'Não' and $telefone_sistema != '') {


		$telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone_sistema);
		$mensagem_whatsapp = '💰 *' . $nome_sistema . '*%0A';
		$mensagem_whatsapp .= '_Você tem uma Conta Vencendo Hoje_ %0A';
		$mensagem_whatsapp .= '*Descrição:* ' . $descricao . ' %0A';
		$mensagem_whatsapp .= '*Valor:* ' . $valorF . ' %0A';

		$data_agd = $vencimento . ' 09:00:00';
		require('../../apis/agendar.php');

		$pdo->query("UPDATE $tabela SET hash = '$hash' where id = '$ultimo_id'");

	}


}


############### CRIAR as recorrências ###############
if ($quant_recorrencia > 0 or $recorrencia_inf == 'Sim') {


	############### excluir as recorrencia se já EXISTIR ###############
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

	if ($recorrencia_inf == 'Sim') {
		$qtd_parcelas = '120';
	} else {
		$qtd_parcelas = $quant_recorrencia;
	}

	$dias_frequencia = $frequencia;

	for ($i = 2; $i <= $qtd_parcelas; $i++) {
		$nova_descricao = $descricao;
		$novo_valor = $valor;
		$dias_parcela = $i - 1;
		$dias_parcela_2 = ($i - 1) * $dias_frequencia;
		if ($dias_frequencia == 30 || $dias_frequencia == 31) {

			$novo_vencimento = date('Y/m/d', strtotime("+$dias_parcela month", strtotime($vencimento)));

		} else if ($dias_frequencia == 90) {
			$dias_parcela = $dias_parcela * 3;
			$novo_vencimento = date('Y/m/d', strtotime("+$dias_parcela month", strtotime($vencimento)));

		} else if ($dias_frequencia == 180) {

			$dias_parcela = $dias_parcela * 6;
			$novo_vencimento = date('Y/m/d', strtotime("+$dias_parcela month", strtotime($vencimento)));

		} else if ($dias_frequencia == 360 || $dias_frequencia == 365) {

			$dias_parcela = $dias_parcela * 12;
			$novo_vencimento = date('Y/m/d', strtotime("+$dias_parcela month", strtotime($vencimento)));

		} else {

			$novo_vencimento = date('Y/m/d', strtotime("+$dias_parcela_2 days", strtotime($vencimento)));
		}




		$query = $pdo->prepare("INSERT INTO $tabela SET descricao = :descricao, fornecedor = :fornecedor, funcionario = :funcionario, valor = :valor, vencimento = '$novo_vencimento', data_lanc = curDate(), forma_pgto = '$forma_pgto', frequencia = '0', obs = :obs, arquivo = '$foto', subtotal = :valor, usuario_lanc = '$id_usuario', pago = 'Não', referencia = 'Conta', caixa = '$id_caixa', hora = curTime() $pgto, id_recorrencia = '$ultimo_id' ");



		$query->bindValue(":descricao", "$descricao");
		$query->bindValue(":fornecedor", "$fornecedor");
		$query->bindValue(":funcionario", "$funcionario");
		$query->bindValue(":valor", "$valor");
		$query->bindValue(":obs", "$obs");
		$query->execute();
		$ultimo_id_rec = $pdo->lastInsertId();


		//enviar whatsapp
		if ($api_whatsapp != 'Não' and $telefone_sistema != '') {

			$telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone_sistema);
			$mensagem_whatsapp = '💰 *' . $nome_sistema . '*%0A';
			$mensagem_whatsapp .= '_Você tem uma Conta Vencendo Hoje_ %0A';
			$mensagem_whatsapp .= '*Descrição:* ' . $descricao . ' %0A';
			$mensagem_whatsapp .= '*Valor:* ' . $valorF . ' %0A';

			$data_agd = $novo_vencimento . ' 09:00:00';
			require('../../apis/agendar.php');

			$pdo->query("UPDATE $tabela SET hash = '$hash' where id = '$ultimo_id_rec'");

		}


	}

}




echo 'Salvo com Sucesso';
?>