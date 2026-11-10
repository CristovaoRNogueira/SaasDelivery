<?php 
@session_start();
$id_empresa = @$_SESSION['empresa'];

if($id_empresa == ""){
    echo 'Sessão expirada! Atualize a página!';
    exit();
}

$tabela = 'config';
require_once("../conexao.php");

if($modo_teste == 'Sim'){
	echo 'Esse recurso está desativado no modo teste!';
	exit();
}


$nome = $_POST['nome_sistema'];
$email = $_POST['email_sistema'];
$telefone = $_POST['telefone_sistema'];
$endereco = $_POST['endereco_sistema'];
$instagram = $_POST['instagram_sistema'];
$multa_atraso = $_POST['multa_atraso'];
$juros_atraso = $_POST['juros_atraso'];
$marca_dagua = $_POST['marca_dagua'];
$assinatura_recibo = $_POST['assinatura_recibo'];
$impressao_automatica = $_POST['impressao_automatica'];
$cnpj_sistema = $_POST['cnpj_sistema'];
$entrar_automatico = $_POST['entrar_automatico'];
$mostrar_preloader = $_POST['mostrar_preloader'];
@$ocultar_mobile = $_POST['ocultar_mobile'];
$api_whatsapp = $_POST['api_whatsapp'];
$token_whatsapp = $_POST['token_whatsapp'];
$instancia_whatsapp = $_POST['instancia_whatsapp'];
$dados_pagamento = $_POST['dados_pagamento'];
$telefone_fixo = $_POST['telefone_fixo'];
$tipo_rel = $_POST['tipo_rel'];
@$tipo_miniatura = $_POST['tipo_miniatura'];
$previsao_entrega = $_POST['previsao_entrega'];
$horario_abertura = $_POST['horario_abertura'];
$horario_fechamento = $_POST['horario_fechamento'];
$texto_fechamento_horario = $_POST['texto_fechamento_horario'];
$texto_fechamento = $_POST['texto_fechamento'];
$status_estabelecimento = $_POST['status_estabelecimento'];
$tempo_atualizar = $_POST['tempo_atualizar'];
$tipo_chave = $_POST['tipo_chave'];
$dias_apagar = $_POST['dias_apagar'];
$banner_rotativo = $_POST['banner_rotativo'];
$pedido_minimo = $_POST['pedido_minimo'];
$mostrar_aberto = $_POST['mostrar_aberto'];
$entrega_distancia = $_POST['entrega_distancia'];
$chave_api_maps = $_POST['chave_api_maps'];
$latitude_rest = $_POST['latitude_rest'];
$longitude_rest = $_POST['longitude_rest'];
$distancia_entrega_km = $_POST['distancia_entrega_km'];
$valor_km = $_POST['valor_km'];
$abrir_comprovante = $_POST['abrir_comprovante'];
$fonte_comprovante = $_POST['fonte_comprovante'];
$mensagem_auto = $_POST['mensagem_auto'];
$api_merc = $_POST['api_merc'];
$comissao_garcon = $_POST['comissao_garcon'];
$couvert = $_POST['couvert'];
$mostrar_acessos = $_POST['mostrar_acessos'];
$abertura_caixa = $_POST['abertura_caixa'];
$mais_sabores = $_POST['mais_sabores'];
$dias_retorno = $_POST['dias_retorno'];
$link_retorno = $_POST['link_retorno'];
$mensagem_retorno = $_POST['mensagem_retorno'];
$total_cartoes = $_POST['total_cartoes'];
$valor_cupom = $_POST['valor_cupom'];
$taxa_extra = $_POST['taxa_extra'];
$titulo_taxa = $_POST['titulo_taxa'];
$retirar_local = $_POST['retirar_local'];
$consumir_local = $_POST['consumir_local'];
$entrega_delivery = $_POST['entrega_delivery'];
$delivery_monitor = $_POST['delivery_monitor'];
$somente_pix_delivery = $_POST['somente_pix_delivery'];
$impressao_cozinha = $_POST['impressao_cozinha'];
$url_evolution = $_POST['url_evolution'];
$pedidos_pdv_delivery = $_POST['pedidos_pdv_delivery'];
$url_site = strtolower(trim($_POST['url_site'] ?? ''));
$url_site = preg_replace('/[^a-z0-9-]+/', '-', $url_site);
$url_site = trim(preg_replace('/-+/', '-', $url_site), '-');

$urls_reservadas = [
	'site', 'index', 'carrinho', 'finalizar', 'pedido', 'mesa', 'categoria',
	'categoria-sabores', 'adicionais', 'observacoes', 'pagamento',
	'pagamento-plano', 'pagamento-plano-asaas', 'sistema', 'assets',
	'app-assets', 'css', 'fonts', 'img', 'js', 'cron', 'manifest', 'sw'
];

$url_colide_com_rota = preg_match('/^(index|carrinho|finalizar|categoria-|categoria-sabores-|adicionais-|observacoes-)/', $url_site);
if ($url_site == '' || in_array($url_site, $urls_reservadas, true) || $url_colide_com_rota) {
	echo 'URL do site inválida ou reservada. Escolha outro identificador.';
	exit();
}

$query_url = $pdo->prepare("SELECT empresa FROM config WHERE url_site = :url_site AND empresa != :empresa LIMIT 1");
$query_url->execute([':url_site' => $url_site, ':empresa' => $id_empresa]);
if ($query_url->fetch(PDO::FETCH_ASSOC)) {
	echo 'Esta URL do site já está sendo usada por outra empresa!';
	exit();
}

$pedido_minimo = str_replace(',', '.', $pedido_minimo);

$taxa_extra = str_replace(',', '.', $taxa_extra);
$taxa_extra = str_replace('%', '', $taxa_extra);

$multa_atraso = str_replace(',', '.', $multa_atraso);
$multa_atraso = str_replace('%', '', $multa_atraso);

$juros_atraso = str_replace(',', '.', $juros_atraso);
$juros_atraso = str_replace('%', '', $juros_atraso);





$logo = 'logo_'.$id_empresa.'.png';
$logo_rel = 'logo_rel_'.$id_empresa.'.jpg';
$icone = 'icone_'.$id_empresa.'.png';
$logo_painel = 'foto-painel_'.$id_empresa.'.png';
$assinatura = 'assinatura_'.$id_empresa.'.jpg';


//foto logo
if(@$_FILES['foto-logo']['name'] != ""){
	$ext = pathinfo($_FILES['foto-logo']['name'], PATHINFO_EXTENSION);   

	if($ext == 'png' || $ext == 'PNG'){ 	
		move_uploaded_file(@$_FILES['foto-logo']['tmp_name'], '../img/'.$logo);
	}else{
		echo 'Extensão de Imagem não permitida para a Logo, use somente PNG!';
		exit();
	}
}


//foto logo rel
if(@$_FILES['foto-logo-rel']['name'] != ""){
	$ext = pathinfo(@$_FILES['foto-logo-rel']['name'], PATHINFO_EXTENSION);   

	if($ext == 'jpg' || $ext == 'JPG' || $ext == 'jpeg' || $ext == 'JPEG'){ 	
		move_uploaded_file(@$_FILES['foto-logo-rel']['tmp_name'], '../img/'.$logo_rel);
	}else{
		echo 'Extensão de Imagem não permitida para o relatório, use somente JPG!';
		exit();
	}
}


//foto icone
if(@$_FILES['foto-icone']['name'] != ""){
	$ext = pathinfo(@$_FILES['foto-icone']['name'], PATHINFO_EXTENSION);   

	if($ext == 'png' || $ext == 'PNG'){ 	
		$caminho = '../img/'.$icone;
		$caminho_192 = '../img/icone_192_'.$id_empresa.'.png';
		$caminho_512 = '../img/icone_512_'.$id_empresa.'.png';

		move_uploaded_file(@$_FILES['foto-icone']['tmp_name'], $caminho);

		$img = @imagecreatefrompng($caminho);

		if($img){
			if(!function_exists('redimensionar_png')){
				function redimensionar_png($origem, $largura, $altura, $destino) {
					$largura_original = imagesx($origem);
					$altura_original = imagesy($origem);

					$nova = imagecreatetruecolor($largura, $altura);

					imagealphablending($nova, false);
					imagesavealpha($nova, true);

					$transparente = imagecolorallocatealpha($nova, 0, 0, 0, 127);
					imagefilledrectangle($nova, 0, 0, $largura, $altura, $transparente);

					imagecopyresampled($nova, $origem, 0, 0, 0, 0, $largura, $altura, $largura_original, $altura_original);
					imagepng($nova, $destino);
					imagedestroy($nova);
				}
			}

			redimensionar_png($img, 192, 192, $caminho_192);
			redimensionar_png($img, 512, 512, $caminho_512);

			imagedestroy($img);
		}

	}else{
		echo 'Extensão de Imagem não permitida para o ícone, use somente PNG!';
		exit();
	}
}


//foto ass
if(@$_FILES['assinatura_rel']['name'] != ""){
	$ext = pathinfo(@$_FILES['assinatura_rel']['name'], PATHINFO_EXTENSION);   

	if($ext == 'jpg' || $ext == 'JPG' || $ext == 'jpeg' || $ext == 'JPEG'){ 	
		move_uploaded_file(@$_FILES['assinatura_rel']['tmp_name'], '../img/'.$assinatura);
	}else{
		echo 'Extensão de Imagem não permitida para assinatura, use somente JPG!';
		exit();
	}
}


//foto painel
if(@$_FILES['foto-painel']['name'] != ""){
	$ext = pathinfo(@$_FILES['foto-painel']['name'], PATHINFO_EXTENSION);   

	if($ext == 'png' || $ext == 'PNG'){ 	
		move_uploaded_file(@$_FILES['foto-painel']['tmp_name'], '../img/'.$logo_painel);
	}else{
		echo 'Extensão de Imagem não permitida para a foto do painel, use somente PNG!';
		exit();
	}
}



$query = $pdo->prepare("UPDATE $tabela SET nome = :nome, email = :email, telefone = :telefone, endereco = :endereco, instagram = :instagram, multa_atraso = :multa_atraso, juros_atraso = :juros_atraso, marca_dagua = :marca_dagua, marca_dagua = :marca_dagua, assinatura_recibo = :assinatura_recibo, impressao_automatica = :impressao_automatica, cnpj = :cnpj_sistema, entrar_automatico = :entrar_automatico, mostrar_preloader = :mostrar_preloader, ocultar_mobile = :ocultar_mobile, api_whatsapp = '$api_whatsapp', token_whatsapp = :token_whatsapp, instancia_whatsapp = :instancia_whatsapp, dados_pagamento = :dados_pagamento, telefone_fixo = :telefone_fixo, tipo_rel = :tipo_rel, tipo_miniatura = :tipo_miniatura, previsao_entrega = :previsao_entrega, horario_abertura = :horario_abertura, horario_fechamento = :horario_fechamento, texto_fechamento_horario = :texto_fechamento_horario, texto_fechamento = :texto_fechamento, status_estabelecimento = :status_estabelecimento, tempo_atualizar = :tempo_atualizar, tipo_chave = :tipo_chave, dias_apagar = :dias_apagar, banner_rotativo = :banner_rotativo, pedido_minimo = :pedido_minimo, mostrar_aberto = :mostrar_aberto, entrega_distancia = :entrega_distancia, chave_api_maps = :chave_api_maps, latitude_rest = :latitude_rest, longitude_rest = :longitude_rest, distancia_entrega_km = :distancia_entrega_km, valor_km = :valor_km, abrir_comprovante = '$abrir_comprovante', fonte_comprovante = :fonte_comprovante, mensagem_auto = '$mensagem_auto', api_merc = :api_merc, comissao_garcon = :comissao_garcon, couvert = :couvert, mostrar_acessos = '$mostrar_acessos', abertura_caixa = '$abertura_caixa', mais_sabores = :mais_sabores, link_retorno = :link_retorno, dias_retorno = :dias_retorno, mensagem_retorno = :mensagem_retorno, total_cartoes = :total_cartoes, valor_cupom = :valor_cupom, taxa_extra = :taxa_extra, titulo_taxa = :titulo_taxa, retirar_local = :retirar_local, consumir_local = :consumir_local, entrega_delivery = :entrega_delivery, delivery_monitor = :delivery_monitor, somente_pix_delivery = :somente_pix_delivery, impressao_cozinha = :impressao_cozinha, url_evolution = :url_evolution, pedidos_pdv_delivery = :pedidos_pdv_delivery, logo = '$logo',
logo_rel = '$logo_rel',
icone = '$icone',
logo_painel = '$logo_painel', imagem_assinatura = '$assinatura', url_site = :url_site where empresa = '$id_empresa' ");

$query->bindValue(":nome", "$nome");
$query->bindValue(":email", "$email");
$query->bindValue(":telefone", "$telefone");
$query->bindValue(":endereco", "$endereco");
$query->bindValue(":instagram", "$instagram");
$query->bindValue(":multa_atraso", "$multa_atraso");
$query->bindValue(":juros_atraso", "$juros_atraso");
$query->bindValue(":marca_dagua", "$marca_dagua");
$query->bindValue(":assinatura_recibo", "$assinatura_recibo");
$query->bindValue(":impressao_automatica", "$impressao_automatica");
$query->bindValue(":cnpj_sistema", "$cnpj_sistema");
$query->bindValue(":entrar_automatico", "$entrar_automatico");
$query->bindValue(":mostrar_preloader", "$mostrar_preloader");
$query->bindValue(":ocultar_mobile", "$ocultar_mobile");
$query->bindValue(":token_whatsapp", "$token_whatsapp");
$query->bindValue(":instancia_whatsapp", "$instancia_whatsapp");
$query->bindValue(":dados_pagamento", "$dados_pagamento");
$query->bindValue(":telefone_fixo", "$telefone_fixo");
$query->bindValue(":tipo_rel", "$tipo_rel");
$query->bindValue(":tipo_miniatura", "$tipo_miniatura");
$query->bindValue(":previsao_entrega", "$previsao_entrega");
$query->bindValue(":horario_abertura", "$horario_abertura");
$query->bindValue(":horario_fechamento", "$horario_fechamento");
$query->bindValue(":texto_fechamento_horario", "$texto_fechamento_horario");
$query->bindValue(":texto_fechamento", "$texto_fechamento");
$query->bindValue(":status_estabelecimento", "$status_estabelecimento");
$query->bindValue(":tempo_atualizar", "$tempo_atualizar");
$query->bindValue(":tipo_chave", "$tipo_chave");
$query->bindValue(":dias_apagar", "$dias_apagar");
$query->bindValue(":banner_rotativo", "$banner_rotativo");
$query->bindValue(":pedido_minimo", "$pedido_minimo");
$query->bindValue(":mostrar_aberto", "$mostrar_aberto");
$query->bindValue(":entrega_distancia", "$entrega_distancia");
$query->bindValue(":chave_api_maps", "$chave_api_maps");
$query->bindValue(":latitude_rest", "$latitude_rest");
$query->bindValue(":longitude_rest", "$longitude_rest");
$query->bindValue(":distancia_entrega_km", "$distancia_entrega_km");
$query->bindValue(":valor_km", "$valor_km");
$query->bindValue(":fonte_comprovante", "$fonte_comprovante");
$query->bindValue(":api_merc", "$api_merc");
$query->bindValue(":comissao_garcon", "$comissao_garcon");
$query->bindValue(":couvert", "$couvert");
$query->bindValue(":mais_sabores", "$mais_sabores");
$query->bindValue(":link_retorno", "$link_retorno");
$query->bindValue(":dias_retorno", "$dias_retorno");
$query->bindValue(":mensagem_retorno", "$mensagem_retorno");
$query->bindValue(":total_cartoes", "$total_cartoes");
$query->bindValue(":valor_cupom", "$valor_cupom");
$query->bindValue(":taxa_extra", "$taxa_extra");
$query->bindValue(":titulo_taxa", "$titulo_taxa");
$query->bindValue(":retirar_local", "$retirar_local");
$query->bindValue(":consumir_local", "$consumir_local");
$query->bindValue(":entrega_delivery", "$entrega_delivery");
$query->bindValue(":delivery_monitor", "$delivery_monitor");
$query->bindValue(":somente_pix_delivery", "$somente_pix_delivery");
$query->bindValue(":impressao_cozinha", "$impressao_cozinha");
$query->bindValue(":url_evolution", "$url_evolution");
$query->bindValue(":pedidos_pdv_delivery", "$pedidos_pdv_delivery");
$query->bindValue(":url_site", "$url_site");
$query->execute();

$query_empresa = $pdo->prepare("UPDATE empresas SET url_site = :url_site WHERE id = :empresa");
$query_empresa->execute([
	':url_site' => $url_site,
	':empresa' => $id_empresa
]);

echo 'Editado com Sucesso';
