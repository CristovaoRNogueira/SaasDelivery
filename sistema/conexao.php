<?php

//definir fuso horário
date_default_timezone_set('America/Sao_Paulo');

$modo_teste = 'Não';

//dados conexão bd local
$servidor = getenv('DB_HOST') ?: 'db';
$banco = getenv('DB_NAME') ?: 'delivery_interativo';
$usuario = getenv('DB_USER') ?: '';
$senha = getenv('DB_PASS') ?: '';



$url_sistema = "https://$_SERVER[HTTP_HOST]/";
$url = explode("//", $url_sistema);
if ($url[1] == 'localhost/') {
	$url_sistema = "http://$_SERVER[HTTP_HOST]/delivery-interativo/";
}



try {
	$pdo = new PDO("mysql:dbname=$banco;host=$servidor;charset=utf8mb4", "$usuario", "$senha");
} catch (Exception $e) {
	echo 'Erro ao conectar ao banco de dados!<br>';
	//echo $e;
}


//variaveis globais
$nome_sistema = 'Nome do Sistema';
$email_sistema = 'contato@hugocursos.com.br';
$telefone_sistema = '(31)97527-5084';
$instagram_sistema = 'portalhugocursos';





//VERIFICAR SE EXISTE DADOS NO CONFIG
$query = $pdo->query("SELECT * from config where empresa = 0");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas == 0) {
	$pdo->query("INSERT INTO config SET nome = '$nome_sistema', email = '$email_sistema', telefone = '$telefone_sistema', logo = 'logo.png', logo_rel = 'logo.jpg', icone = 'icone.png', ativo = 'Sim', multa_atraso = '0', juros_atraso = '0', marca_dagua = 'Sim', assinatura_recibo = 'Não', impressao_automatica = 'Não', api_whatsapp = 'Não', tipo_rel = 'PDF', tipo_miniatura = 'Cores', previsao_entrega = '60', horario_abertura = '18:00', horario_fechamento = '00:00', status_estabelecimento = 'Aberto', tempo_atualizar = '30', dias_apagar = '30', fonte_comprovante = '11', banner_rotativo = 'Sim', mostrar_aberto = 'Sim', abrir_comprovante = 'Não', mensagem_auto = 'Sim', mostrar_acessos = 'Sim', abertura_caixa = 'Não', total_cartoes = '5', valor_cupom = '15',
		-- NOVOS CAMPOS (ERP)	
		logo_painel = '',
		entrar_automatico = 'Não',
		mostrar_preloader = 'Sim',
		ocultar_mobile = 'Não',
		token_whatsapp = '',
		instancia_whatsapp = '',
		alterar_acessos = 'Não',
		dados_pagamento = '',
		access_token = '',
		public_key = '',
		limitar_recursos = 'Sim',
		chave_api_asaas = '',
		alterar_api_whatsapp = 'Não',
		pagina_entrada = '',
		multi_empresas = 'Sim',
		meta_descricao = '',
		empresa = 0

		");
} else {
	$nome_sistema = $res[0]['nome'];
	$email_sistema = $res[0]['email'];
	$telefone_sistema = $res[0]['telefone'];
	$endereco_sistema = $res[0]['endereco'];
	$instagram_sistema = $res[0]['instagram'];
	$logo_sistema = $res[0]['logo'];
	$logo_rel = $res[0]['logo_rel'];
	$icone_sistema = $res[0]['icone'];
	$ativo_sistema = $res[0]['ativo'];
	$multa_atraso = $res[0]['multa_atraso'];
	$juros_atraso = $res[0]['juros_atraso'];
	$marca_dagua = $res[0]['marca_dagua'];
	$assinatura_recibo = $res[0]['assinatura_recibo'];
	$impressao_automatica = $res[0]['impressao_automatica'];
	$cnpj_sistema = $res[0]['cnpj'];
	$entrar_automatico = $res[0]['entrar_automatico'];
	$mostrar_preloader = $res[0]['mostrar_preloader'];
	$ocultar_mobile = $res[0]['ocultar_mobile'];
	$api_whatsapp = $res[0]['api_whatsapp'];
	$token_whatsapp = $res[0]['token_whatsapp'];
	$instancia_whatsapp = $res[0]['instancia_whatsapp'];
	$dados_pagamento = $res[0]['dados_pagamento'];
	$telefone_fixo = $res[0]['telefone_fixo'];
	$tipo_rel = $res[0]['tipo_rel'];
	$tipo_miniatura = $res[0]['tipo_miniatura'];
	$previsao_entrega = $res[0]['previsao_entrega'];
	$horario_abertura = $res[0]['horario_abertura'];
	$horario_fechamento = $res[0]['horario_fechamento'];
	$texto_fechamento_horario = $res[0]['texto_fechamento_horario'];
	$texto_fechamento = $res[0]['texto_fechamento'];
	$status_estabelecimento = $res[0]['status_estabelecimento'];
	$tempo_atualizar = $res[0]['tempo_atualizar'];
	$tipo_chave = $res[0]['tipo_chave'];
	$dias_apagar = $res[0]['dias_apagar'];
	$banner_rotativo = $res[0]['banner_rotativo'];
	$pedido_minimo = $res[0]['pedido_minimo'];
	$mostrar_aberto = $res[0]['mostrar_aberto'];
	$entrega_distancia = $res[0]['entrega_distancia'];
	$chave_api_maps = $res[0]['chave_api_maps'];
	$latitude_rest = $res[0]['latitude_rest'];
	$longitude_rest = $res[0]['longitude_rest'];
	$distancia_entrega_km = $res[0]['distancia_entrega_km'];
	$valor_km = $res[0]['valor_km'];
	$mais_sabores = $res[0]['mais_sabores'];
	$abrir_comprovante = $res[0]['abrir_comprovante'];
	$fonte_comprovante = $res[0]['fonte_comprovante'];
	$mensagem_auto = $res[0]['mensagem_auto'];
	$data_cobranca = $res[0]['data_cobranca'];
	$api_merc = $res[0]['api_merc'];
	$comissao_garcon = $res[0]['comissao_garcon'];
	$couvert = $res[0]['couvert'];
	$mostrar_acessos = $res[0]['mostrar_acessos'];
	$abertura_caixa = $res[0]['abertura_caixa'];
	$dias_retorno = $res[0]['dias_retorno'];
	$link_retorno = $res[0]['link_retorno'];
	$mensagem_retorno = $res[0]['mensagem_retorno'];
	$total_cartoes_config = $res[0]['total_cartoes'];
	$valor_cupom_config = $res[0]['valor_cupom'];
	$taxa_extra = $res[0]['taxa_extra'];
	$titulo_taxa = $res[0]['titulo_taxa'];
	$retirar_local = $res[0]['retirar_local'];
	$consumir_local = $res[0]['consumir_local'];
	$entrega_delivery = $res[0]['entrega_delivery'];
	$delivery_monitor = $res[0]['delivery_monitor'];
	$somente_pix_delivery = $res[0]['somente_pix_delivery'];
	$impressao_cozinha = $res[0]['impressao_cozinha'];
	$url_evolution = $res[0]['url_evolution'];
	$pausar_delivery = $res[0]['pausar_delivery'];
	$pedidos_pdv_delivery = $res[0]['pedidos_pdv_delivery'];
	$token_mp = $res[0]['api_merc'];
	$api_pagamento = $res[0]['api_pagamento'];


	// ✅ NOVOS CAMPOS (ERP) - recuperando com os MESMOS NOMES de variáveis do ERP
// ✅ NOVOS CAMPOS (ERP)
$logo_painel          = $res[0]['logo_painel'] ?? '';
$alterar_acessos      = $res[0]['alterar_acessos'] ?? 'Não';
$access_token_mp      = $res[0]['access_token'] ?? '';
$public_key_mp        = $res[0]['public_key'] ?? '';
$limitar_recursos     = $res[0]['limitar_recursos'] ?? 'Sim';
$chave_api_asaas      = $res[0]['chave_api_asaas'] ?? '';
$alterar_api_whatsapp = $res[0]['alterar_api_whatsapp'] ?? 'Não';
$pagina_entrada       = $res[0]['pagina_entrada'] ?? '';
$multi_empresas       = $res[0]['multi_empresas'] ?? 'Não';
$meta_descricao       = $res[0]['meta_descricao'] ?? '';
$fundo_login = $res[0]['fundo_login'];

	// Novas variaveis Menuia (mantive seu bloco)
    $emailMenuia = $res[0]['email_menuia'] ?? '';
    $planoMenuia = $res[0]['plano_menuia'] ?? '';
    $validadeMenuia = $res[0]['validade_menuia'] ?? '';
    $senhaMenuia = $res[0]['senha_menuia'] ?? '';

    // ⚠️ Aqui você já tinha $token_whatsapp e $instancia_whatsapp vindo de token/instancia.
    // Mantive como estava, mas agora também existem colunas próprias token_whatsapp/instancia_whatsapp no config.
    // Se você quiser usar as novas, comente as duas linhas abaixo.
    
    if($fundo_login == ""){
		$fundo_login = 'sem-foto.png';
	}

	if($taxa_extra == ""){
		$taxa_extra = 0;
	}

	$tel_whats = '55' . preg_replace('/[ ()-]+/', '', $telefone_sistema);

	if ($ativo_sistema != 'Sim' and $ativo_sistema != '') { ?>
		<style type="text/css">
			@media only screen and (max-width: 700px) {
				.imgsistema_mobile {
					width: 300px;
				}

			}
		</style>
		<div style="text-align: center; margin-top: 100px">
			<img src="<?php echo $url_sistema ?>img/bloqueio.png" class="imgsistema_mobile">
		</div>
		<?php
		exit();
	}

}
?>