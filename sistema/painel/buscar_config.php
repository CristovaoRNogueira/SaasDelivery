<?php
// buscar_config.php

if (session_status() !== PHP_SESSION_ACTIVE) {
	@session_start();
}

if (!isset($id_empresa) || $id_empresa === '') {
	$id_empresa = $_SESSION['empresa'] ?? '';
}

if ($id_empresa == "") {
	echo 'Sessão expirada! Atualize a página!';
	exit();
}

if (!isset($pdo) || !($pdo instanceof PDO)) {
	require_once __DIR__ . '/../conexao.php';
}

$query_config = $pdo->query("SELECT * FROM config WHERE empresa = '$id_empresa' LIMIT 1");
$res_config = $query_config->fetchAll(PDO::FETCH_ASSOC);

if (@count($res_config) > 0) {

	$config = $res_config[0];

	$nome_sistema = $config['nome'] ?? '';
	$email_sistema = $config['email'] ?? '';
	$telefone_sistema = $config['telefone'] ?? '';
	$whatsapp_sistema = $config['telefone'] ?? '';
	$telefone_fixo_sistema = $config['telefone_fixo'] ?? '';
	$endereco_sistema = $config['endereco'] ?? '';
	$instagram_sistema = $config['instagram'] ?? '';
	$cnpj_sistema = $config['cnpj'] ?? '';

	$logo_sistema = $config['logo'] ?? 'sem-foto.png';
	$logo_rel = $config['logo_rel'] ?? 'sem-foto.png';
	$icone_sistema = $config['icone'] ?? 'sem-foto.png';
	$logo_painel = $config['logo_painel'] ?? 'sem-foto.png';
	$imagem_assinatura = $config['imagem_assinatura'] ?? 'sem-foto.png';
	$fundo_login = $config['fundo_login'] ?? 'sem-foto.png';

	$multa_atraso = $config['multa_atraso'] ?? '0';
	$juros_atraso = $config['juros_atraso'] ?? '0';
	$marca_dagua = $config['marca_dagua'] ?? 'Sim';
	$assinatura_recibo = $config['assinatura_recibo'] ?? 'Não';
	$impressao_automatica = $config['impressao_automatica'] ?? 'Não';
	$entrar_automatico = $config['entrar_automatico'] ?? 'Não';
	$mostrar_preloader = $config['mostrar_preloader'] ?? 'Sim';
	$ocultar_mobile = $config['ocultar_mobile'] ?? 'Não';

	$api_whatsapp = $config['api_whatsapp'] ?? 'Não';
	$token_whatsapp = $config['token_whatsapp'] ?? '';
	$instancia_whatsapp = $config['instancia_whatsapp'] ?? '';
	$dados_pagamento = $config['dados_pagamento'] ?? '';
	$url_evolution = $config['url_evolution'] ?? '';

	$tipo_rel = $config['tipo_rel'] ?? 'PDF';
	$tipo_miniatura = $config['tipo_miniatura'] ?? 'Cores';
	$previsao_entrega = $config['previsao_entrega'] ?? '60';
	$horario_abertura = $config['horario_abertura'] ?? '18:00';
	$horario_fechamento = $config['horario_fechamento'] ?? '00:00';
	$texto_fechamento_horario = $config['texto_fechamento_horario'] ?? '';
	$texto_fechamento = $config['texto_fechamento'] ?? '';
	$status_estabelecimento = $config['status_estabelecimento'] ?? 'Aberto';
	$tempo_atualizar = $config['tempo_atualizar'] ?? '30';
	$tipo_chave = $config['tipo_chave'] ?? '';
	$dias_apagar = $config['dias_apagar'] ?? '30';

	$banner_rotativo = $config['banner_rotativo'] ?? 'Sim';
	$pedido_minimo = $config['pedido_minimo'] ?? '0';
	$mostrar_aberto = $config['mostrar_aberto'] ?? 'Sim';
	$entrega_distancia = $config['entrega_distancia'] ?? '';
	$chave_api_maps = $config['chave_api_maps'] ?? '';
	$latitude_rest = $config['latitude_rest'] ?? '';
	$longitude_rest = $config['longitude_rest'] ?? '';
	$distancia_entrega_km = $config['distancia_entrega_km'] ?? '';
	$valor_km = $config['valor_km'] ?? '0';

	$abrir_comprovante = $config['abrir_comprovante'] ?? 'Não';
	$fonte_comprovante = $config['fonte_comprovante'] ?? '11';
	$mensagem_auto = $config['mensagem_auto'] ?? 'Sim';
	$api_merc = $config['api_merc'] ?? '';
	$token_mp = $config['api_merc'] ?? '';

	$comissao_garcon = $config['comissao_garcon'] ?? '0';
	$couvert = $config['couvert'] ?? '0';
	$mostrar_acessos = $config['mostrar_acessos'] ?? 'Sim';
	$abertura_caixa = $config['abertura_caixa'] ?? 'Não';
	$mais_sabores = $config['mais_sabores'] ?? '';

	$dias_retorno = $config['dias_retorno'] ?? '';
	$link_retorno = $config['link_retorno'] ?? '';
	$mensagem_retorno = $config['mensagem_retorno'] ?? '';

	$total_cartoes_config = $config['total_cartoes'] ?? '5';
	$valor_cupom_config = $config['valor_cupom'] ?? '15';
	$taxa_extra = $config['taxa_extra'] ?? '0';
	$titulo_taxa = $config['titulo_taxa'] ?? '';
	$retirar_local = $config['retirar_local'] ?? 'Sim';
	$consumir_local = $config['consumir_local'] ?? 'Sim';
	$entrega_delivery = $config['entrega_delivery'] ?? 'Sim';
	$delivery_monitor = $config['delivery_monitor'] ?? 'Não';
	$somente_pix_delivery = $config['somente_pix_delivery'] ?? 'Não';
	$impressao_cozinha = $config['impressao_cozinha'] ?? 'Não';
	$pedidos_pdv_delivery = $config['pedidos_pdv_delivery'] ?? 'Não';

	$alterar_acessos = $config['alterar_acessos'] ?? 'Não';
	$access_token_mp = $config['access_token'] ?? '';
	$public_key_mp = $config['public_key'] ?? '';
	$limitar_recursos = $config['limitar_recursos'] ?? 'Sim';
	$chave_api_asaas = $config['chave_api_asaas'] ?? '';
	$alterar_api_whatsapp = $config['alterar_api_whatsapp'] ?? 'Não';
	$pagina_entrada = $config['pagina_entrada'] ?? '';
	
	$meta_descricao = $config['meta_descricao'] ?? '';
	$url_site = $config['url_site'] ?? '';
	$dispositivos = $config['dispositivos'] ?? '0';

	$emailMenuia = $config['email_menuia'] ?? '';
	$planoMenuia = $config['plano_menuia'] ?? '';
	$validadeMenuia = $config['validade_menuia'] ?? '';
	$senhaMenuia = $config['senha_menuia'] ?? '';

	if ($fundo_login == "") {
		$fundo_login = 'sem-foto.png';
	}

	if ($logo_sistema == "") {
		$logo_sistema = 'sem-foto.png';
	}

	if ($logo_rel == "") {
		$logo_rel = 'sem-foto.png';
	}

	if ($icone_sistema == "") {
		$icone_sistema = 'sem-foto.png';
	}

	if ($logo_painel == "") {
		$logo_painel = 'sem-foto.png';
	}

	if ($imagem_assinatura == "") {
		$imagem_assinatura = 'sem-foto.png';
	}

	if ($taxa_extra == "") {
		$taxa_extra = 0;
	}

	$tel_whatsapp = '55' . preg_replace('/[ ()-]+/', '', $whatsapp_sistema);

} else {
	echo 'Configuração da empresa não encontrada!';
	exit();
}
?>
