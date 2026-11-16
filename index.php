<?php 
session_start();
require_once("sistema/conexao.php");

$url_site = resolverSubdominioTenant(false);
if ($url_site !== '') {
  $_SESSION['url_site'] = $url_site;
  require_once("site.php");
  exit();
}

// Verifica se o usuário está logado (exemplo: verifica se existe uma sessão)
if (!isset($_SESSION['usuario_logado_pagina'])) {
  if ($pagina_entrada == 'Login') {
    echo '<script>window.location="login"</script>';
  }
}

if ($multi_empresas == 'Não') {
  // Busca a primeira empresa
  $stmt = $pdo->query("SELECT url_site FROM config WHERE empresa > 0 ORDER BY id ASC LIMIT 1");
  $row  = $stmt->fetch(PDO::FETCH_ASSOC);
  $slug = $row['url_site'] ?? '';

  if (!empty($slug)) {
    // Normaliza a base e monta a URL canônica curta: {base}/{slug}
    $base = rtrim($url_sistema ?? '', '/');
    $dest = $base . '/' . rawurlencode($slug);

    // Evita cache e redireciona
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Location: ' . $dest, true, 302);
    exit;
  }

  // Fallback caso não exista empresa configurada
  http_response_code(404);
  echo 'Empresa padrão não configurada.';
  exit;
}

$data_atual = date('Y-m-d');
$id_empresa = 0;
$empresa = 0;

// Buscar configurações da tabela site (empresa = 0) configuradas no painel SaaS
$query = $pdo->query("SELECT * FROM site WHERE empresa = '$id_empresa'");
$res = $query ? $query->fetchAll(PDO::FETCH_ASSOC) : [];
$logo = @$res[0]['logo'];
$titulo = @$res[0]['titulo'];
$subtitulo = @$res[0]['subtitulo'];
$botao1 = @$res[0]['botao1'];
$botao2 = @$res[0]['botao2'];
$botao3 = @$res[0]['botao3'];
$item1 = @$res[0]['item1'];
$item2 = @$res[0]['item2'];
$item3 = @$res[0]['item3'];
$titulo_recursos = @$res[0]['titulo_recursos'];
$titulo_perguntas = @$res[0]['titulo_perguntas'];
$titulo_rodape = @$res[0]['titulo_rodape'];
$link_rodape = @$res[0]['link_rodape'];
$descricao_rodape = @$res[0]['descricao_rodape'];
$botao_rodape = @$res[0]['botao_rodape'];
$logo_topo = @$res[0]['logo_topo'];
$fundo_topo = @$res[0]['fundo_topo'];
$fundo_topo_mobile = @$res[0]['fundo_topo_mobile'];

if ($logo == "") {
  $logo = 'sem-foto.png';
}

$tem_logo_site = ($logo != 'sem-foto.png' && is_file(__DIR__ . '/sistema/img/logos/' . $logo));
$caminho_logo_site = $tem_logo_site
  ? ('sistema/img/logos/' . $logo)
  : ('sistema/img/' . (!empty($icone_sistema) && $icone_sistema != 'sem-foto.png' ? $icone_sistema : 'icone.png'));

$nome_exibicao = !empty($nome_sistema) ? $nome_sistema : 'Na Região';

if ($id_empresa == 0) {
  $link_painel = 'sistema';
} else {
  $link_painel = '../acesso';
}

// WhatsApp e Redes Sociais vindos de conexao.php ($tel_whats, $telefone_sistema, $instagram_sistema, $email_sistema)
$whats_numero = !empty($tel_whats) ? preg_replace('/[^0-9]/', '', (string) $tel_whats) : preg_replace('/[^0-9]/', '', (string) ($telefone_sistema ?? ''));
if ($whats_numero !== '' && strlen($whats_numero) <= 11) {
  $whats_numero = '55' . $whats_numero;
}
$url_whats_suporte = $whats_numero !== ''
  ? "https://api.whatsapp.com/send?1=pt_BR&phone={$whats_numero}&text=" . rawurlencode("Olá! Gostaria de falar com um consultor sobre o {$nome_exibicao}.")
  : "#planos";

$url_instagram_rodape = '';
if (!empty($instagram_sistema)) {
  if (strpos($instagram_sistema, 'http') === 0) {
    $url_instagram_rodape = $instagram_sistema;
  } else {
    $url_instagram_rodape = 'https://www.instagram.com/' . trim($instagram_sistema, '@/') . '/';
  }
}

// Buscar planos ativos cadastrados no painel SaaS
$query_planos = $pdo->query("SELECT * FROM planos WHERE ativo = 'Sim' ORDER BY id ASC");
$planos_ativos = $query_planos ? $query_planos->fetchAll(PDO::FETCH_ASSOC) : [];
$total_planos = count($planos_ativos);

// Identificar o plano mais completo (com mais itens) para aplicar o destaque visual discreto
$id_plano_destaque = 0;
$total_itens_max = 0;
if ($total_planos > 0) {
  $query_destaque = $pdo->query("
    SELECT planos.id, COUNT(planos_itens.id) as total_itens
    FROM planos
    LEFT JOIN planos_itens ON planos.id = planos_itens.plano
    WHERE planos.ativo = 'Sim'
    GROUP BY planos.id
    ORDER BY total_itens DESC, planos.valor DESC
    LIMIT 1
  ");
  $row_destaque = $query_destaque ? $query_destaque->fetch(PDO::FETCH_ASSOC) : null;
  $id_plano_destaque = (int) ($row_destaque['id'] ?? $planos_ativos[$total_planos - 1]['id']);
  $total_itens_max = (int) ($row_destaque['total_itens'] ?? 0);
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="keywords" content="sistema de delivery, cardápio digital, delivery próprio, <?php echo htmlspecialchars($nome_exibicao) ?>, pedidos no whatsapp, qr code na mesa, saas delivery" />
  <meta name="description" content="<?php echo htmlspecialchars(!empty($meta_descricao) ? $meta_descricao : "Crie o Delivery Perfeito para o seu Negócio em Minutos com {$nome_exibicao}.") ?>" />
  <meta name="author" content="CRN Sistemas" />
  <title><?php echo htmlspecialchars($nome_exibicao) ?> — Crie o Delivery Perfeito para o seu Negócio</title>

  <!-- Favicon Dinâmico -->
  <link rel="icon" href="sistema/img/<?php echo htmlspecialchars($icone_sistema ?? 'icone.png') ?>" type="image/x-icon" />

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
          },
          colors: {
            brand: {
              50: '#fff1f2',
              100: '#ffe4e6',
              500: '#f43f5e',
              600: '#e11d48',
              700: '#be123c',
            }
          }
        }
      }
    }
  </script>
</head>
<body class="font-sans bg-white text-gray-800 antialiased selection:bg-brand-600 selection:text-white overflow-x-hidden">

  <!-- ==================== NAVBAR ==================== -->
  <header class="sticky top-0 z-50 bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-20">
        <!-- Exclusivamente a Imagem da Logo ($logo ou $icone_sistema) -->
        <a href="#" class="flex items-center">
          <img src="<?php echo htmlspecialchars($caminho_logo_site) ?>" alt="Logo" class="h-12 w-auto object-contain" />
        </a>

        <!-- Desktop Links -->
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
          <a href="#recursos" class="hover:text-gray-900 transition-colors">Recursos</a>
          <a href="#como-funciona" class="hover:text-gray-900 transition-colors">Como Funciona</a>
          <a href="#planos" class="hover:text-gray-900 transition-colors">Planos</a>
          <a href="#contato" class="hover:text-gray-900 transition-colors">Contato</a>
        </nav>

        <!-- Action Buttons -->
        <div class="hidden md:flex items-center gap-3">
          <a href="/<?php echo ltrim($link_painel, '/') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-gray-700 hover:text-gray-900 bg-white hover:bg-gray-50 border border-gray-300 transition-colors">
            <i class="fas fa-sign-in-alt text-brand-600"></i>
            Entrar
          </a>
          <a href="#planos" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 shadow-sm transition-colors">
            <i class="fas fa-store"></i>
            Criar Loja
          </a>
        </div>

        <!-- Mobile Menu Button -->
        <div class="md:hidden">
          <button type="button" onclick="toggleMobileMenu()" aria-label="Abrir Menu" class="p-2.5 rounded-lg text-gray-600 hover:text-gray-900 bg-gray-50 border border-gray-200">
            <i class="fas fa-bars text-lg" id="mobile-menu-icon"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Menu Dropdown -->
    <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200 bg-white px-4 pt-4 pb-6 space-y-2">
      <a href="#recursos" onclick="toggleMobileMenu()" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Recursos</a>
      <a href="#como-funciona" onclick="toggleMobileMenu()" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Como Funciona</a>
      <a href="#planos" onclick="toggleMobileMenu()" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Planos</a>
      <a href="#contato" onclick="toggleMobileMenu()" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Contato</a>
      <div class="pt-3 border-t border-gray-200 grid grid-cols-2 gap-3">
        <a href="/<?php echo ltrim($link_painel, '/') ?>" class="flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm font-semibold text-gray-700 bg-white border border-gray-300">
          <i class="fas fa-sign-in-alt text-brand-600"></i> Entrar
        </a>
        <a href="#planos" onclick="toggleMobileMenu()" class="flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm font-bold text-white bg-brand-600 hover:bg-brand-700">
          <i class="fas fa-store"></i> Criar Loja
        </a>
      </div>
    </div>
  </header>

  <!-- ==================== HERO SECTION ==================== -->
  <section class="py-16 lg:py-24 bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

        <!-- Left Column: Headline, Sub-headline & Action Buttons -->
        <div class="lg:col-span-7 text-center lg:text-left">
          <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 text-brand-600 text-xs font-semibold uppercase tracking-wider mb-5">
            Plataforma Completa de Delivery e Gestão
          </span>

          <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-gray-900 leading-tight mb-5">
            Crie o <span class="text-brand-600">Delivery Perfeito</span> para o seu Negócio em Minutos.
          </h1>

          <p class="text-base sm:text-lg text-gray-600 leading-relaxed max-w-2xl mx-auto lg:mx-0 mb-8">
            Sem comissões por venda. Receba pedidos direto no WhatsApp, tenha seu próprio site (<span class="font-semibold text-gray-900">sualoja.naregiao.com.br</span>) e painel de gestão completo.
          </p>

          <!-- Botões Principais Sólidos -->
          <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 mb-10">
            <a href="#planos" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-3.5 rounded-lg text-base font-bold text-white bg-brand-600 hover:bg-brand-700 shadow-sm transition-colors">
              <i class="fas fa-rocket"></i>
              <?php echo htmlspecialchars(!empty($botao1) ? $botao1 : 'Teste Grátis Agora') ?>
            </a>
            <a href="<?php echo htmlspecialchars($url_whats_suporte) ?>" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-3.5 rounded-lg text-base font-semibold text-gray-700 hover:text-gray-900 bg-white hover:bg-gray-100 border border-gray-300 shadow-sm transition-colors">
              <i class="fab fa-whatsapp text-emerald-600 text-lg"></i>
              Falar com Consultor
            </a>
          </div>

          <!-- Destaques Dinâmicos -->
          <div class="grid grid-cols-3 gap-4 pt-6 border-t border-gray-200 max-w-lg mx-auto lg:mx-0 text-left">
            <div>
              <div class="text-xl font-bold text-gray-900">0%</div>
              <div class="text-xs text-gray-600"><?php echo htmlspecialchars(!empty($item1) ? $item1 : 'Taxa por pedido') ?></div>
            </div>
            <div>
              <div class="text-xl font-bold text-gray-900">100%</div>
              <div class="text-xs text-gray-600"><?php echo htmlspecialchars(!empty($item2) ? $item2 : 'Integrado ao WhatsApp') ?></div>
            </div>
            <div>
              <div class="text-xl font-bold text-gray-900">Suporte</div>
              <div class="text-xs text-gray-600"><?php echo htmlspecialchars(!empty($item3) ? $item3 : 'Atendimento dedicado') ?></div>
            </div>
          </div>
        </div>

        <!-- Right Column: Clean Storefront Mockup -->
        <div class="lg:col-span-5">
          <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm max-w-md mx-auto lg:max-w-none">
            <!-- Browser Top Bar -->
            <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-gray-100">
              <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-gray-300"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-gray-300"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-gray-300"></span>
              </div>
              <div class="px-3 py-1 rounded-md bg-gray-50 border border-gray-200 text-xs text-gray-600 font-medium flex items-center gap-1.5">
                <i class="fas fa-lock text-emerald-600 text-[10px]"></i>
                sualoja.naregiao.com.br
              </div>
              <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full">Aberto</span>
            </div>

            <!-- Store Header Preview -->
            <div class="flex items-center justify-between bg-gray-50 p-3.5 rounded-lg mb-4 border border-gray-200">
              <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-lg bg-brand-50 border border-brand-100 flex items-center justify-center text-brand-600">
                  <i class="fas fa-utensils"></i>
                </div>
                <div>
                  <div class="font-bold text-gray-900 text-sm">Cardápio Digital Online</div>
                  <div class="text-xs text-gray-500">Entrega • Retirada • Mesa</div>
                </div>
              </div>
              <span class="text-xs font-semibold text-gray-700 bg-white border border-gray-200 px-2.5 py-1 rounded-md">
                <i class="fas fa-star text-amber-400 mr-1"></i> 4.9
              </span>
            </div>

            <!-- Sample Product Cards -->
            <div class="space-y-3 mb-4">
              <div class="flex items-center justify-between p-3 rounded-lg bg-white border border-gray-200">
                <div>
                  <div class="text-sm font-bold text-gray-900">Combo Artesanal Duplo</div>
                  <div class="text-xs text-gray-500">2 Burgers, fritas crocante e bebida</div>
                </div>
                <div class="text-right">
                  <div class="text-sm font-bold text-gray-900">R$ 44,90</div>
                  <span class="inline-block text-[11px] font-semibold text-brand-600">+ Adicionar</span>
                </div>
              </div>

              <div class="flex items-center justify-between p-3 rounded-lg bg-white border border-gray-200">
                <div>
                  <div class="text-sm font-bold text-gray-900">Pizza Grande 2 Sabores</div>
                  <div class="text-xs text-gray-500">Borda recheada + refrigerante 2L</div>
                </div>
                <div class="text-right">
                  <div class="text-sm font-bold text-gray-900">R$ 69,90</div>
                  <span class="inline-block text-[11px] font-semibold text-brand-600">+ Adicionar</span>
                </div>
              </div>
            </div>

            <!-- Notification Bar -->
            <div class="p-3.5 rounded-lg bg-gray-50 border border-gray-200 flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-emerald-600 flex items-center justify-center text-white">
                  <i class="fab fa-whatsapp"></i>
                </div>
                <div>
                  <div class="text-xs font-bold text-gray-900">Pedido #1048 Recebido</div>
                  <div class="text-[11px] text-gray-500">Notificação automática no WhatsApp</div>
                </div>
              </div>
              <span class="text-sm font-bold text-gray-900">R$ 114,80</span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ==================== FEATURES SECTION (DIFERENCIAIS) ==================== -->
  <section id="recursos" class="py-16 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-3xl mx-auto mb-14">
        <span class="text-brand-600 font-semibold text-xs uppercase tracking-wider px-3 py-1 rounded-full bg-brand-50">
          Recursos Exclusivos
        </span>
        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3 mb-3">
          <?php echo htmlspecialchars(!empty($titulo_recursos) ? $titulo_recursos : 'Tudo o que seu Restaurante Precisa para Vender Mais') ?>
        </h2>
        <p class="text-gray-600 text-base">
          Desenvolvido para pizzarias, hamburguerias, açaiterias, restaurantes e lanchonetes que buscam independência e gestão simples.
        </p>
      </div>

      <!-- 4 Main Feature Pillars -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <!-- Feature 1: Domínio Exclusivo -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 flex flex-col justify-between">
          <div>
            <div class="w-12 h-12 rounded-lg bg-brand-50 flex items-center justify-center text-brand-600 text-xl mb-5">
              <i class="fas fa-globe"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Domínio Exclusivo</h3>
            <p class="text-gray-600 text-sm leading-relaxed">
              Tenha seu próprio endereço <strong class="text-gray-900">sualoja.naregiao.com.br</strong> com HTTPS seguro e atalho instalável (PWA) no celular do cliente.
            </p>
          </div>
          <div class="mt-5 pt-4 border-t border-gray-100 flex items-center text-xs font-semibold text-gray-700">
            <i class="fas fa-check-circle text-emerald-600 mr-2"></i> Link exclusivo para bio e WhatsApp
          </div>
        </div>

        <!-- Feature 2: Zero Taxas -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 flex flex-col justify-between">
          <div>
            <div class="w-12 h-12 rounded-lg bg-brand-50 flex items-center justify-center text-brand-600 text-xl mb-5">
              <i class="fas fa-hand-holding-dollar"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Zero Taxas por Venda</h3>
            <p class="text-gray-600 text-sm leading-relaxed">
              Economize nas comissões sobre vendas. Você investe apenas um valor fixo mensal e 100% do lucro de cada pedido fica com o seu negócio.
            </p>
          </div>
          <div class="mt-5 pt-4 border-t border-gray-100 flex items-center text-xs font-semibold text-gray-700">
            <i class="fas fa-check-circle text-emerald-600 mr-2"></i> Pedidos ilimitados sem comissão
          </div>
        </div>

        <!-- Feature 3: Painel Intuitivo -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 flex flex-col justify-between">
          <div>
            <div class="w-12 h-12 rounded-lg bg-brand-50 flex items-center justify-center text-brand-600 text-xl mb-5">
              <i class="fas fa-chart-pie"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Painel Intuitivo & PDV</h3>
            <p class="text-gray-600 text-sm leading-relaxed">
              Gestão completa de pedidos em tempo real, impressão automática na cozinha, controle de caixa, estoque, entregadores e relatórios financeiros.
            </p>
          </div>
          <div class="mt-5 pt-4 border-t border-gray-100 flex items-center text-xs font-semibold text-gray-700">
            <i class="fas fa-check-circle text-emerald-600 mr-2"></i> Notificações automáticas no WhatsApp
          </div>
        </div>

        <!-- Feature 4: Pedidos na Mesa via QR Code -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 flex flex-col justify-between">
          <div>
            <div class="w-12 h-12 rounded-lg bg-brand-50 flex items-center justify-center text-brand-600 text-xl mb-5">
              <i class="fas fa-qrcode"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Pedidos na Mesa via QR Code</h3>
            <p class="text-gray-600 text-sm leading-relaxed">
              Gere QR Codes individuais para cada mesa. O cliente escaneia, faz o pedido pelo celular e envia direto para a cozinha com agilidade.
            </p>
          </div>
          <div class="mt-5 pt-4 border-t border-gray-100 flex items-center text-xs font-semibold text-gray-700">
            <i class="fas fa-check-circle text-emerald-600 mr-2"></i> Comandas e módulo de mesas integrados
          </div>
        </div>

      </div>

      <!-- Secondary Feature Strip -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-5 rounded-xl bg-gray-50 border border-gray-200 flex items-start gap-4">
          <div class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-brand-600 shrink-0">
            <i class="fas fa-pizza-slice"></i>
          </div>
          <div>
            <h4 class="font-bold text-gray-900 text-sm mb-1">Montagem de Pizzas e Açaí</h4>
            <p class="text-xs text-gray-600 leading-relaxed">Suporte nativo a múltiplos sabores, bordas, adicionais e observações por item.</p>
          </div>
        </div>

        <div class="p-5 rounded-xl bg-gray-50 border border-gray-200 flex items-start gap-4">
          <div class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-brand-600 shrink-0">
            <i class="fas fa-credit-card"></i>
          </div>
          <div>
            <h4 class="font-bold text-gray-900 text-sm mb-1">PIX Automático & Cartão</h4>
            <p class="text-xs text-gray-600 leading-relaxed">Receba via PIX Copia e Cola, cartão ou dinheiro com cálculo automático de troco.</p>
          </div>
        </div>

        <div class="p-5 rounded-xl bg-gray-50 border border-gray-200 flex items-start gap-4">
          <div class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-brand-600 shrink-0">
            <i class="fas fa-gift"></i>
          </div>
          <div>
            <h4 class="font-bold text-gray-900 text-sm mb-1">Cartão Fidelidade & Cupons</h4>
            <p class="text-xs text-gray-600 leading-relaxed">Fidelize clientes com cartões fidelidade, cupons de desconto e taxas de entrega por bairro ou KM.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ==================== COMO FUNCIONA ==================== -->
  <section id="como-funciona" class="py-16 lg:py-20 bg-gray-50 border-y border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-2xl mx-auto mb-12">
        <span class="text-brand-600 font-semibold text-xs uppercase tracking-wider px-3 py-1 rounded-full bg-brand-50">
          Passo a Passo Simples
        </span>
        <h2 class="text-3xl font-bold text-gray-900 mt-3">
          Sua Loja Pronta para Vender em 3 Passos
        </h2>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl p-6 sm:p-8 shadow-sm border border-gray-200">
          <div class="w-10 h-10 rounded-lg bg-brand-600 text-white font-bold flex items-center justify-center text-sm mb-4">1</div>
          <h3 class="text-lg font-bold text-gray-900 mb-2">Escolha seu Plano</h3>
          <p class="text-sm text-gray-600 leading-relaxed">Selecione o plano ideal abaixo e preencha o cadastro rápido da sua empresa.</p>
        </div>

        <div class="bg-white rounded-xl p-6 sm:p-8 shadow-sm border border-gray-200">
          <div class="w-10 h-10 rounded-lg bg-brand-600 text-white font-bold flex items-center justify-center text-sm mb-4">2</div>
          <h3 class="text-lg font-bold text-gray-900 mb-2">Personalize o Cardápio</h3>
          <p class="text-sm text-gray-600 leading-relaxed">Adicione sua logo, categorias, produtos, fotos, horários de atendimento e taxas de entrega.</p>
        </div>

        <div class="bg-white rounded-xl p-6 sm:p-8 shadow-sm border border-gray-200">
          <div class="w-10 h-10 rounded-lg bg-brand-600 text-white font-bold flex items-center justify-center text-sm mb-4">3</div>
          <h3 class="text-lg font-bold text-gray-900 mb-2">Compartilhe e Venda</h3>
          <p class="text-sm text-gray-600 leading-relaxed">Divulgue seu link no Instagram e WhatsApp e receba pedidos prontos para preparo e entrega.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ==================== PRICING SECTION (PLANOS DINÂMICOS DO BANCO) ==================== -->
  <section id="planos" class="py-16 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-3xl mx-auto mb-14">
        <span class="text-brand-600 font-semibold text-xs uppercase tracking-wider px-3 py-1 rounded-full bg-brand-50">
          Planos e Preços
        </span>
        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3 mb-3">
          Escolha o Plano Ideal para Seu Negócio
        </h2>
        <p class="text-gray-600 text-base">
          Planos transparentes, sem taxa de adesão e sem comissões sobre suas vendas.
        </p>
        <?php if (@$modo_teste == 'Sim') { ?>
          <p class="mt-3 text-xs font-semibold text-amber-600">(PLANOS DEMONSTRATIVOS — AMBIENTE DE TESTE)</p>
        <?php } ?>
      </div>

      <?php if ($total_planos > 0) {
        $grid_cols_class = 'lg:grid-cols-3';
        if ($total_planos === 1) {
          $grid_cols_class = 'lg:grid-cols-1 max-w-md';
        } elseif ($total_planos === 2) {
          $grid_cols_class = 'lg:grid-cols-2 max-w-4xl';
        } elseif ($total_planos >= 4) {
          $grid_cols_class = 'lg:grid-cols-4';
        }
      ?>
      <!-- Loop Dinâmico dos Planos Cadastrados no Painel SaaS -->
      <div class="grid grid-cols-1 md:grid-cols-2 <?php echo $grid_cols_class ?> gap-6 mx-auto" id="plans-container">
        <?php
        $stmt_itens = $pdo->prepare("SELECT * FROM planos_itens WHERE plano = :plano ORDER BY id ASC");

        foreach ($planos_ativos as $plano) {
          $id_item = (int) ($plano['id'] ?? 0);
          $nome_do_plano = $plano['nome'] ?? 'Plano';
          $valor_plano = (float) ($plano['valor'] ?? 0);
          $valorF = number_format($valor_plano, 2, ',', '.');
          $frequencia_plano = !empty($plano['frequencia']) ? $plano['frequencia'] : 'mês';
          $descricao_plano = !empty($plano['descricao']) ? $plano['descricao'] : '';

          $is_destaque = ($id_item === $id_plano_destaque && $total_planos > 1);

          // Buscar as características/itens deste plano na tabela planos_itens
          $stmt_itens->execute([':plano' => $id_item]);
          $itens_do_plano = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <div class="bg-white rounded-xl p-6 sm:p-8 shadow-sm <?php echo $is_destaque ? 'border-2 border-brand-600' : 'border border-gray-200' ?> flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between gap-2 mb-4">
              <h3 class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($nome_do_plano) ?></h3>
              <?php if ($is_destaque) { ?>
              <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-brand-50 text-brand-600 shrink-0">
                Mais Completo
              </span>
              <?php } ?>
            </div>

            <?php if ($descricao_plano !== '') { ?>
            <p class="text-xs text-gray-500 mb-4"><?php echo htmlspecialchars($descricao_plano) ?></p>
            <?php } ?>

            <div class="my-5 pb-5 border-b border-gray-200">
              <div class="flex items-baseline gap-1">
                <span class="text-base font-medium text-gray-500">R$</span>
                <span class="text-4xl font-extrabold text-gray-900"><?php echo $valorF ?></span>
                <span class="text-gray-500 text-sm">/<?php echo htmlspecialchars($frequencia_plano) ?></span>
              </div>
            </div>

            <ul class="space-y-3 text-sm text-gray-600 mb-8">
              <?php if (count($itens_do_plano) > 0) {
                foreach ($itens_do_plano as $carac) {
                  $nome_carac = trim((string) ($carac['nome'] ?? ''));
                  if ($nome_carac === '') continue;
              ?>
              <li class="flex items-start gap-2.5">
                <i class="fas fa-check-circle text-emerald-600 mt-0.5 shrink-0"></i>
                <span><?php echo htmlspecialchars($nome_carac) ?></span>
              </li>
              <?php }
              } ?>
            </ul>
          </div>

          <form action="assinar.php" method="POST">
            <input type="hidden" name="id_item" value="<?php echo $id_item ?>">
            <button type="submit" class="w-full py-3.5 px-6 rounded-lg font-bold text-sm text-white bg-brand-600 hover:bg-brand-700 transition-colors flex items-center justify-center gap-2 shadow-sm">
              <span>Assinar Agora</span>
              <i class="fas fa-arrow-right"></i>
            </button>
          </form>
        </div>
        <?php } ?>
      </div>

      <?php } else { ?>
      <!-- Fallback seguro caso o banco retorne zero planos temporariamente -->
      <div class="max-w-xl mx-auto bg-white rounded-xl p-8 text-center shadow-sm border border-gray-200">
        <div class="w-12 h-12 rounded-lg bg-brand-50 flex items-center justify-center text-brand-600 text-xl mx-auto mb-4">
          <i class="fas fa-store"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Planos Sob Consulta</h3>
        <p class="text-gray-600 text-sm mb-6">
          Entre em contato com nosso atendimento no WhatsApp para conhecer os planos disponíveis e liberar o acesso da sua loja.
        </p>
        <a href="<?php echo htmlspecialchars($url_whats_suporte) ?>" target="_blank" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-lg font-bold text-sm text-white bg-brand-600 hover:bg-brand-700 transition-colors shadow-sm">
          <i class="fab fa-whatsapp text-lg"></i> Falar com Consultor
        </a>
      </div>
      <?php } ?>

    </div>
  </section>

  <?php
  // Seção de Perguntas Frequentes (FAQ) dinâmica caso existam perguntas cadastradas no painel SaaS
  $query_faq = $pdo->query("SELECT * FROM perguntas_site WHERE empresa = '$id_empresa' ORDER BY posicao_pergunta ASC");
  $perguntas_db = $query_faq ? $query_faq->fetchAll(PDO::FETCH_ASSOC) : [];
  if (count($perguntas_db) > 0) {
  ?>
  <!-- ==================== FAQ DINÂMICO ==================== -->
  <section id="faq" class="py-16 lg:py-20 bg-gray-50 border-t border-gray-200">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-10">
        <h2 class="text-3xl font-bold text-gray-900">
          <?php echo htmlspecialchars(!empty($titulo_perguntas) ? $titulo_perguntas : 'Perguntas Frequentes') ?>
        </h2>
      </div>
      <div class="space-y-3">
        <?php foreach ($perguntas_db as $perg) {
          $id_perg = (int) ($perg['id'] ?? 0);
          $tit_perg = $perg['titulo_pergunta'] ?? '';
          $desc_perg = $perg['descricao_pergunta'] ?? '';
        ?>
        <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-200">
          <button type="button" onclick="toggleFaq('faq-<?php echo $id_perg ?>')" class="w-full flex items-center justify-between p-5 text-left font-semibold text-gray-900 hover:text-brand-600 transition-colors">
            <span><?php echo htmlspecialchars($tit_perg) ?></span>
            <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300" id="icon-faq-<?php echo $id_perg ?>"></i>
          </button>
          <div id="faq-<?php echo $id_perg ?>" class="hidden px-5 pb-5 text-sm text-gray-600 border-t border-gray-100 pt-4 leading-relaxed">
            <?php echo nl2br(htmlspecialchars($desc_perg)) ?>
          </div>
        </div>
        <?php } ?>
      </div>
    </div>
  </section>
  <?php } ?>

  <!-- ==================== CTA & CONTATO SECTION ==================== -->
  <section id="contato" class="py-16 bg-gray-50 border-t border-gray-200">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="bg-white rounded-xl p-8 sm:p-10 shadow-sm border border-gray-200 flex flex-col lg:flex-row items-center justify-between gap-8 text-center lg:text-left">
        <div class="max-w-2xl">
          <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
            <?php echo htmlspecialchars(!empty($titulo_rodape) ? $titulo_rodape : 'Pronto para profissionalizar o seu Delivery?') ?>
          </h2>
          <p class="text-gray-600 text-base">
            <?php echo htmlspecialchars(!empty($descricao_rodape) ? strip_tags($descricao_rodape) : 'Crie sua loja em poucos minutos ou fale com nosso time no WhatsApp para tirar suas dúvidas.') ?>
          </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 shrink-0 w-full sm:w-auto">
          <a href="<?php echo htmlspecialchars(!empty($link_rodape) ? $link_rodape : '#planos') ?>" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-lg text-sm font-bold bg-brand-600 hover:bg-brand-700 text-white shadow-sm transition-colors">
            <i class="fas fa-store"></i> <?php echo htmlspecialchars(!empty($botao_rodape) ? $botao_rodape : 'Criar Minha Loja') ?>
          </a>
          <a href="<?php echo htmlspecialchars($url_whats_suporte) ?>" target="_blank" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-lg text-sm font-semibold bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 shadow-sm transition-colors">
            <i class="fab fa-whatsapp text-emerald-600 text-lg"></i> Falar com Consultor
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- ==================== FOOTER ==================== -->
  <footer class="bg-white border-t border-gray-200 pt-12 pb-8 text-gray-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-10 border-b border-gray-200">

        <!-- Brand Info -->
        <div class="md:col-span-2">
          <div class="mb-4">
            <img src="<?php echo htmlspecialchars($caminho_logo_site) ?>" alt="Logo" class="h-11 w-auto object-contain" />
          </div>
          <p class="text-sm text-gray-600 max-w-md leading-relaxed mb-5">
            Plataforma SaaS completa para gestão de Delivery, Cardápio Digital, Pedidos via WhatsApp e Autoatendimento em Mesas por QR Code.
          </p>
          <!-- Links Dinâmicos das Redes Sociais -->
          <div class="flex items-center gap-2.5">
            <?php if ($url_instagram_rodape !== '') { ?>
            <a href="<?php echo htmlspecialchars($url_instagram_rodape) ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram"
              class="w-9 h-9 rounded-lg bg-gray-50 hover:bg-brand-600 text-gray-600 hover:text-white border border-gray-200 flex items-center justify-center transition-colors">
              <i class="fab fa-instagram"></i>
            </a>
            <?php } ?>
            <?php if ($whats_numero !== '') { ?>
            <a href="<?php echo htmlspecialchars($url_whats_suporte) ?>" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"
              class="w-9 h-9 rounded-lg bg-gray-50 hover:bg-emerald-600 text-gray-600 hover:text-white border border-gray-200 flex items-center justify-center transition-colors">
              <i class="fab fa-whatsapp"></i>
            </a>
            <?php } ?>
            <?php if (!empty($email_sistema)) { ?>
            <a href="mailto:<?php echo htmlspecialchars($email_sistema) ?>" aria-label="E-mail"
              class="w-9 h-9 rounded-lg bg-gray-50 hover:bg-brand-600 text-gray-600 hover:text-white border border-gray-200 flex items-center justify-center transition-colors">
              <i class="far fa-envelope"></i>
            </a>
            <?php } ?>
          </div>
        </div>

        <!-- Links Rápidos -->
        <div>
          <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-3">Navegação</h4>
          <ul class="space-y-2 text-sm">
            <li><a href="#recursos" class="hover:text-brand-600 transition-colors">Recursos</a></li>
            <li><a href="#como-funciona" class="hover:text-brand-600 transition-colors">Como Funciona</a></li>
            <li><a href="#planos" class="hover:text-brand-600 transition-colors">Planos e Preços</a></li>
            <li><a href="/<?php echo ltrim($link_painel, '/') ?>" class="hover:text-brand-600 transition-colors">Área do Lojista (Entrar)</a></li>
          </ul>
        </div>

        <!-- Suporte e Contato Dinâmicos -->
        <div>
          <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-3">Suporte & Contato</h4>
          <ul class="space-y-2 text-sm">
            <?php if (!empty($telefone_sistema)) { ?>
            <li>
              <a href="<?php echo htmlspecialchars($url_whats_suporte) ?>" target="_blank" rel="noopener noreferrer" class="hover:text-brand-600 transition-colors flex items-center gap-2">
                <i class="fab fa-whatsapp text-emerald-600"></i>
                <span><?php echo htmlspecialchars($telefone_sistema) ?></span>
              </a>
            </li>
            <?php } ?>
            <?php if (!empty($email_sistema)) { ?>
            <li class="flex items-center gap-2">
              <i class="far fa-envelope text-brand-600"></i>
              <a href="mailto:<?php echo htmlspecialchars($email_sistema) ?>" class="hover:text-brand-600 transition-colors">
                <?php echo htmlspecialchars($email_sistema) ?>
              </a>
            </li>
            <?php } ?>
            <?php if (!empty($endereco_sistema)) { ?>
            <li class="flex items-start gap-2 text-xs text-gray-500">
              <i class="fas fa-map-marker-alt text-brand-600 mt-1"></i>
              <span><?php echo htmlspecialchars($endereco_sistema) ?></span>
            </li>
            <?php } ?>
            <li>
              <a href="/<?php echo ltrim($link_painel, '/') ?>" class="hover:text-brand-600 transition-colors flex items-center gap-2">
                <i class="fas fa-lock text-gray-400"></i> Acesso ao Painel (/sistema)
              </a>
            </li>
          </ul>
        </div>

      </div>

      <!-- Copyright Dinâmico + CRN Sistemas -->
      <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-4">
        <p>&copy; <?php echo date('Y'); ?> <strong class="text-gray-700"><?php echo htmlspecialchars($nome_exibicao) ?></strong>. Todos os direitos reservados.</p>
        <p>Desenvolvido por <strong class="text-gray-700">CRN Sistemas</strong>.</p>
      </div>
    </div>
  </footer>

  <!-- Botão Flutuante do WhatsApp -->
  <?php if ($whats_numero !== '') { ?>
  <a title="Falar com Consultor no WhatsApp" target="_blank" rel="noopener noreferrer" href="<?php echo htmlspecialchars($url_whats_suporte) ?>"
    class="fixed bottom-5 right-5 z-50 w-13 h-13 p-3.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center shadow-md transition-colors">
    <i class="fab fa-whatsapp text-2xl"></i>
  </a>
  <?php } ?>

  <script src="assets/js/lgpd-cookie.js" type="module"></script>
  <Lgpd-cookie text="Não capturamos nenhuma informação de dados sensíveis, somente cookies para melhor performance de nosso website!" />

  <script>
    function toggleMobileMenu() {
      const menu = document.getElementById('mobile-menu');
      const icon = document.getElementById('mobile-menu-icon');
      if (menu.classList.contains('hidden')) {
        menu.classList.remove('hidden');
        icon.classList.remove('fa-bars');
        icon.classList.add('fa-times');
      } else {
        menu.classList.add('hidden');
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
      }
    }

    function toggleFaq(id) {
      const content = document.getElementById(id);
      const icon = document.getElementById('icon-' + id);
      if (!content) return;
      if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        if (icon) icon.classList.add('rotate-180');
      } else {
        content.classList.add('hidden');
        if (icon) icon.classList.remove('rotate-180');
      }
    }
  </script>
</body>
</html>
