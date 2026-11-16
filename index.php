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

// Buscar configurações opcionais da tabela site (empresa = 0)
$query = $pdo->query("SELECT * FROM site WHERE empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$logo = @$res[0]['logo'];
if ($logo == "") {
  $logo = 'sem-foto.png';
}

// Buscar planos ativos do banco para manter integração funcional com assinar.php
$query_planos = $pdo->query("SELECT * FROM planos WHERE ativo = 'Sim' ORDER BY valor ASC");
$planos_db = $query_planos ? $query_planos->fetchAll(PDO::FETCH_ASSOC) : [];
$id_plano_mensal = $planos_db[0]['id'] ?? 1;
$valor_plano_mensal = isset($planos_db[0]['valor']) && $planos_db[0]['valor'] > 0
  ? number_format((float) $planos_db[0]['valor'], 2, ',', '.')
  : '120,00';

$ultimo_idx = count($planos_db) > 1 ? count($planos_db) - 1 : 0;
$id_plano_anual = $planos_db[$ultimo_idx]['id'] ?? $id_plano_mensal;
$valor_base_anual = isset($planos_db[0]['valor']) && $planos_db[0]['valor'] > 0
  ? ((float) $planos_db[0]['valor'] * 0.8)
  : 96.00;
$valor_plano_anual = number_format($valor_base_anual, 2, ',', '.');

$link_painel = '/sistema';
$whats_limpo = preg_replace('/[^0-9]/', '', (string) ($telefone_sistema ?? ''));
if ($whats_limpo !== '' && strlen($whats_limpo) <= 11) {
  $whats_limpo = '55' . $whats_limpo;
}
$url_whats_suporte = $whats_limpo !== ''
  ? "https://api.whatsapp.com/send?phone={$whats_limpo}&text=" . rawurlencode("Olá! Quero criar minha loja de delivery no Na Região.")
  : "#planos";
?>
<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="keywords" content="sistema de delivery, cardápio digital, delivery próprio, pedidos no whatsapp, qr code na mesa, na região, saas delivery" />
  <meta name="description" content="Crie o Delivery Perfeito para o seu Negócio em Minutos. Sem comissões por venda, pedidos direto no WhatsApp, domínio próprio sualoja.naregiao.com.br e gestão completa." />
  <meta name="author" content="CRN Sistemas" />
  <title>Na Região — Crie o Delivery Perfeito para o seu Negócio</title>

  <!-- Favicon -->
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
            },
            accent: {
              500: '#f97316',
              600: '#ea580c',
            }
          }
        }
      }
    }
  </script>

  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background-color: #090d16;
      color: #f8fafc;
    }
    .glass-nav {
      background: rgba(9, 13, 22, 0.82);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .glass-card {
      background: rgba(30, 41, 59, 0.45);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .glass-card:hover {
      border-color: rgba(244, 63, 94, 0.4);
      box-shadow: 0 20px 40px -15px rgba(244, 63, 94, 0.15);
    }
    .hero-glow {
      background: radial-gradient(circle at 50% 0%, rgba(244, 63, 94, 0.22) 0%, rgba(249, 115, 22, 0.10) 35%, rgba(9, 13, 22, 0) 70%);
    }
  </style>
</head>
<body class="antialiased selection:bg-brand-600 selection:text-white overflow-x-hidden">

  <!-- ==================== NAVBAR ==================== -->
  <header class="fixed top-0 left-0 right-0 z-50 glass-nav">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-20">
        <!-- Logo Na Região -->
        <a href="#" class="flex items-center gap-3 group">
          <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-brand-600 to-accent-500 flex items-center justify-center shadow-lg shadow-brand-600/30 group-hover:scale-105 transition-transform">
            <i class="fas fa-utensils text-white text-lg"></i>
          </div>
          <div class="flex flex-col">
            <span class="text-xl font-extrabold tracking-tight text-white flex items-center gap-1.5">
              Na Região
              <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            </span>
            <span class="text-[11px] font-medium text-slate-400 -mt-1 tracking-wider uppercase">Delivery SaaS</span>
          </div>
        </a>

        <!-- Desktop Links -->
        <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-300">
          <a href="#recursos" class="hover:text-white transition-colors">Recursos</a>
          <a href="#como-funciona" class="hover:text-white transition-colors">Como Funciona</a>
          <a href="#planos" class="hover:text-white transition-colors">Planos</a>
          <a href="#contato" class="hover:text-white transition-colors">Contato</a>
        </nav>

        <!-- Action Buttons -->
        <div class="hidden md:flex items-center gap-3">
          <a href="<?php echo $link_painel ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-200 hover:text-white bg-slate-800/70 hover:bg-slate-800 border border-slate-700/80 transition-all">
            <i class="fas fa-sign-in-alt text-brand-500"></i>
            Entrar
          </a>
          <a href="#planos" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-brand-600 to-accent-500 hover:from-brand-500 hover:to-accent-600 shadow-lg shadow-brand-600/25 hover:shadow-brand-600/40 hover:-translate-y-0.5 transition-all">
            <i class="fas fa-store"></i>
            Criar Loja
          </a>
        </div>

        <!-- Mobile Menu Button -->
        <div class="md:hidden">
          <button type="button" onclick="toggleMobileMenu()" aria-label="Abrir Menu" class="p-2.5 rounded-xl text-slate-300 hover:text-white bg-slate-800/70 border border-slate-700">
            <i class="fas fa-bars text-lg" id="mobile-menu-icon"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Menu Dropdown -->
    <div id="mobile-menu" class="hidden md:hidden border-t border-slate-800 bg-slate-950/95 px-4 pt-4 pb-6 space-y-3">
      <a href="#recursos" onclick="toggleMobileMenu()" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-300 hover:text-white hover:bg-slate-800/60">Recursos</a>
      <a href="#como-funciona" onclick="toggleMobileMenu()" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-300 hover:text-white hover:bg-slate-800/60">Como Funciona</a>
      <a href="#planos" onclick="toggleMobileMenu()" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-300 hover:text-white hover:bg-slate-800/60">Planos</a>
      <a href="#contato" onclick="toggleMobileMenu()" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-300 hover:text-white hover:bg-slate-800/60">Contato</a>
      <div class="pt-3 border-t border-slate-800 grid grid-cols-2 gap-3">
        <a href="<?php echo $link_painel ?>" class="flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-semibold text-slate-200 bg-slate-800 border border-slate-700">
          <i class="fas fa-sign-in-alt text-brand-500"></i> Entrar
        </a>
        <a href="#planos" onclick="toggleMobileMenu()" class="flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-brand-600 to-accent-500">
          <i class="fas fa-store"></i> Criar Loja
        </a>
      </div>
    </div>
  </header>

  <!-- ==================== HERO SECTION ==================== -->
  <section class="relative pt-32 pb-20 lg:pt-40 lg:pb-28 hero-glow overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">

        <!-- Left Column: Copy & CTAs -->
        <div class="lg:col-span-7 text-center lg:text-left">
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-500/10 border border-brand-500/30 text-brand-500 text-xs sm:text-sm font-bold mb-6">
            <span class="flex h-2 w-2 rounded-full bg-brand-500 animate-ping"></span>
            PLATAFORMA COMPLETA DE DELIVERY E GESTÃO
          </div>

          <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-[1.12] mb-6">
            Crie o <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-500 via-rose-400 to-accent-500">Delivery Perfeito</span> para o seu Negócio em Minutos.
          </h1>

          <p class="text-lg sm:text-xl text-slate-300 leading-relaxed max-w-2xl mx-auto lg:mx-0 mb-8 font-normal">
            Sem comissões por venda. Receba pedidos direto no WhatsApp, tenha seu próprio site (<span class="text-white font-semibold underline decoration-brand-500/60">sualoja.naregiao.com.br</span>) e painel de gestão completo.
          </p>

          <!-- Subdomain Interactive Preview -->
          <div class="max-w-xl mx-auto lg:mx-0 mb-8 p-2 rounded-2xl bg-slate-900/90 border border-slate-700/80 shadow-2xl flex flex-col sm:flex-row items-center gap-2">
            <div class="flex items-center w-full px-3 py-2 bg-slate-950/80 rounded-xl border border-slate-800 text-sm">
              <i class="fas fa-lock text-emerald-400 mr-2 text-xs"></i>
              <span class="text-slate-400">https://</span>
              <input type="text" id="preview-slug" placeholder="sualoja" value="sualoja" aria-label="Nome da sua loja"
                class="bg-transparent text-white font-bold focus:outline-none w-28 sm:w-32 text-center border-b border-dashed border-brand-500/60" />
              <span class="text-brand-400 font-semibold">.naregiao.com.br</span>
            </div>
            <a href="#planos" class="w-full sm:w-auto whitespace-nowrap inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-sm font-extrabold text-white bg-gradient-to-r from-brand-600 to-accent-500 hover:from-brand-500 hover:to-accent-600 shadow-lg shadow-brand-600/30 transition-all">
              Criar Minha Loja Agora <i class="fas fa-arrow-right"></i>
            </a>
          </div>

          <!-- Primary & Secondary CTA Buttons -->
          <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 mb-10">
            <a href="#planos" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-4 rounded-2xl text-base font-extrabold text-white bg-emerald-600 hover:bg-emerald-500 shadow-xl shadow-emerald-600/25 hover:-translate-y-0.5 transition-all">
              <i class="fas fa-rocket"></i>
              Teste Grátis Agora
            </a>
            <a href="<?php echo htmlspecialchars($url_whats_suporte) ?>" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-4 rounded-2xl text-base font-bold text-slate-200 hover:text-white glass-card hover:bg-slate-800/60 transition-all">
              <i class="fab fa-whatsapp text-emerald-400 text-xl"></i>
              Falar com Consultor
            </a>
          </div>

          <!-- Trust Pills -->
          <div class="grid grid-cols-3 gap-4 pt-6 border-t border-slate-800/80 max-w-lg mx-auto lg:mx-0 text-left">
            <div>
              <div class="text-2xl font-extrabold text-white">0%</div>
              <div class="text-xs text-slate-400">Taxa por pedido</div>
            </div>
            <div>
              <div class="text-2xl font-extrabold text-emerald-400">100%</div>
              <div class="text-xs text-slate-400">Integrado ao WhatsApp</div>
            </div>
            <div>
              <div class="text-2xl font-extrabold text-amber-400">5 min</div>
              <div class="text-xs text-slate-400">Para colocar no ar</div>
            </div>
          </div>
        </div>

        <!-- Right Column: Visual Storefront & Dashboard Mockup -->
        <div class="lg:col-span-5">
          <div class="relative mx-auto max-w-md lg:max-w-none">
            <!-- Glow effect behind mockup -->
            <div class="absolute -inset-1 bg-gradient-to-r from-brand-600 to-accent-500 rounded-3xl blur-2xl opacity-30"></div>

            <div class="relative glass-card rounded-3xl p-5 border border-slate-700/70 shadow-2xl">
              <!-- Browser Top Bar -->
              <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-800">
                <div class="flex items-center gap-1.5">
                  <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                  <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                  <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                </div>
                <div class="px-3 py-1 rounded-full bg-slate-950/90 border border-slate-800 text-[11px] text-slate-300 font-medium flex items-center gap-1.5">
                  <i class="fas fa-lock text-emerald-400 text-[10px]"></i>
                  pizzaria.naregiao.com.br
                </div>
                <span class="text-xs font-bold text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded-full">Aberto</span>
              </div>

              <!-- Store Header Preview -->
              <div class="flex items-center justify-between bg-slate-900/90 p-3.5 rounded-2xl mb-4 border border-slate-800">
                <div class="flex items-center gap-3">
                  <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-brand-500 to-amber-500 flex items-center justify-center text-xl shadow-md">
                    🍕
                  </div>
                  <div>
                    <div class="font-bold text-white text-sm">Pizzaria & Burger Na Região</div>
                    <div class="text-xs text-slate-400 flex items-center gap-2">
                      <span><i class="fas fa-clock text-brand-400 mr-1"></i>30-45 min</span>
                      <span>•</span>
                      <span class="text-emerald-400 font-semibold"> Sem taxas</span>
                    </div>
                  </div>
                </div>
                <div class="text-right">
                  <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-400 bg-amber-400/10 px-2.5 py-1 rounded-lg">
                    <i class="fas fa-star"></i> 4.9
                  </span>
                </div>
              </div>

              <!-- Sample Product Cards -->
              <div class="space-y-3 mb-4">
                <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-900/60 border border-slate-800/80">
                  <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-rose-500/15 border border-rose-500/30 flex items-center justify-center text-lg">🍔</div>
                    <div>
                      <div class="text-sm font-bold text-white">Combo Smash Duplo + Fritas</div>
                      <div class="text-xs text-slate-400">2 Burgers artesanais, cheddar e bebida</div>
                    </div>
                  </div>
                  <div class="text-right">
                    <div class="text-sm font-extrabold text-emerald-400">R$ 44,90</div>
                    <span class="inline-block text-[10px] font-bold bg-brand-600 text-white px-2 py-0.5 rounded-md">+ Adicionar</span>
                  </div>
                </div>

                <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-900/60 border border-slate-800/80">
                  <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-lg">🍕</div>
                    <div>
                      <div class="text-sm font-bold text-white">Pizza Grande 2 Sabores</div>
                      <div class="text-xs text-slate-400">Borda recheada + refrigerante 2L</div>
                    </div>
                  </div>
                  <div class="text-right">
                    <div class="text-sm font-extrabold text-emerald-400">R$ 69,90</div>
                    <span class="inline-block text-[10px] font-bold bg-brand-600 text-white px-2 py-0.5 rounded-md">+ Adicionar</span>
                  </div>
                </div>
              </div>

              <!-- Live Notification Toast Mockup -->
              <div class="p-3.5 rounded-2xl bg-gradient-to-r from-emerald-950/90 to-slate-900 border border-emerald-500/40 flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
                    <i class="fab fa-whatsapp text-lg"></i>
                  </div>
                  <div>
                    <div class="text-xs font-bold text-emerald-300 uppercase tracking-wider">Novo Pedido #1048 Recebido!</div>
                    <div class="text-xs text-slate-300">Pagamento PIX Aprovado • Impressão Automática</div>
                  </div>
                </div>
                <span class="text-sm font-extrabold text-white">R$ 114,80</span>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ==================== FEATURES SECTION (DIFERENCIAIS) ==================== -->
  <section id="recursos" class="py-20 lg:py-28 bg-slate-950/60 border-y border-slate-800/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-3xl mx-auto mb-16">
        <span class="text-brand-500 font-bold text-xs uppercase tracking-widest px-3 py-1 rounded-full bg-brand-500/10 border border-brand-500/20">
          RECURSOS EXCLUSIVOS
        </span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-4 mb-4">
          Tudo o que seu Restaurante Precisa para Vender Mais
        </h2>
        <p class="text-slate-400 text-base sm:text-lg">
          Desenvolvido para pizzarias, hamburguerias, açaiterias, restaurantes e lanchonetes que buscam independência e lucro real.
        </p>
      </div>

      <!-- 4 Main Feature Pillars -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

        <!-- Feature 1: Domínio Exclusivo -->
        <div class="glass-card rounded-3xl p-7 transition-all duration-300 hover:-translate-y-1.5 flex flex-col justify-between">
          <div>
            <div class="w-14 h-14 rounded-2xl bg-brand-500/15 border border-brand-500/30 flex items-center justify-center text-2xl mb-6 text-brand-400">
              🌐
            </div>
            <h3 class="text-xl font-bold text-white mb-3">Domínio Exclusivo</h3>
            <p class="text-slate-400 text-sm leading-relaxed">
              Tenha seu próprio endereço <strong class="text-slate-200">sualoja.naregiao.com.br</strong> com HTTPS seguro e aplicativo instalável (PWA) no celular do seu cliente.
            </p>
          </div>
          <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center text-xs font-bold text-brand-400">
            <i class="fas fa-check-circle mr-2"></i> Link exclusivo para bio e WhatsApp
          </div>
        </div>

        <!-- Feature 2: Zero Taxas -->
        <div class="glass-card rounded-3xl p-7 transition-all duration-300 hover:-translate-y-1.5 flex flex-col justify-between">
          <div>
            <div class="w-14 h-14 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-2xl mb-6 text-emerald-400">
              💸
            </div>
            <h3 class="text-xl font-bold text-white mb-3">Zero Taxas por Venda</h3>
            <p class="text-slate-400 text-sm leading-relaxed">
              Chega de pagar até 27% de comissão para aplicativos terceiros. No <strong class="text-slate-200">Na Região</strong> você paga apenas uma mensalidade fixa e 100% do lucro é seu.
            </p>
          </div>
          <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center text-xs font-bold text-emerald-400">
            <i class="fas fa-check-circle mr-2"></i> Pedidos ilimitados sem comissão
          </div>
        </div>

        <!-- Feature 3: Painel Intuitivo -->
        <div class="glass-card rounded-3xl p-7 transition-all duration-300 hover:-translate-y-1.5 flex flex-col justify-between">
          <div>
            <div class="w-14 h-14 rounded-2xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-2xl mb-6 text-amber-400">
              📊
            </div>
            <h3 class="text-xl font-bold text-white mb-3">Painel Intuitivo & PDV</h3>
            <p class="text-slate-400 text-sm leading-relaxed">
              Gestão completa de pedidos em tempo real, impressão térmica automática na cozinha, controle de caixa, estoque, motoboys e financeiro.
            </p>
          </div>
          <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center text-xs font-bold text-amber-400">
            <i class="fas fa-check-circle mr-2"></i> Notificações automáticas no WhatsApp
          </div>
        </div>

        <!-- Feature 4: Pedidos na Mesa via QR Code -->
        <div class="glass-card rounded-3xl p-7 transition-all duration-300 hover:-translate-y-1.5 flex flex-col justify-between">
          <div>
            <div class="w-14 h-14 rounded-2xl bg-sky-500/15 border border-sky-500/30 flex items-center justify-center text-2xl mb-6 text-sky-400">
              📱
            </div>
            <h3 class="text-xl font-bold text-white mb-3">Pedidos na Mesa via QR Code</h3>
            <p class="text-slate-400 text-sm leading-relaxed">
              Gere QR Codes individuais para cada mesa. O cliente escaneia, faz o pedido sozinho pelo celular e envia direto para a cozinha sem fila.
            </p>
          </div>
          <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center text-xs font-bold text-sky-400">
            <i class="fas fa-check-circle mr-2"></i> App do Garçom e Comandas integrados
          </div>
        </div>

      </div>

      <!-- Secondary Feature Strip -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 flex items-start gap-4">
          <div class="w-11 h-11 rounded-xl bg-purple-500/15 border border-purple-500/30 flex items-center justify-center text-purple-400 shrink-0">
            <i class="fas fa-pizza-slice text-lg"></i>
          </div>
          <div>
            <h4 class="font-bold text-white text-base mb-1">Montagem de Pizzas e Açaí</h4>
            <p class="text-xs text-slate-400 leading-relaxed">Suporte nativo a múltiplos sabores, bordas recheadas, adicionais pagos ou gratuitos e observações.</p>
          </div>
        </div>

        <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 flex items-start gap-4">
          <div class="w-11 h-11 rounded-xl bg-teal-500/15 border border-teal-500/30 flex items-center justify-center text-teal-400 shrink-0">
            <i class="fas fa-qrcode text-lg"></i>
          </div>
          <div>
            <h4 class="font-bold text-white text-base mb-1">PIX Automático & Cartão</h4>
            <p class="text-xs text-slate-400 leading-relaxed">Cobre via PIX Copia e Cola com confirmação instantânea, cartão ou dinheiro com cálculo de troco.</p>
          </div>
        </div>

        <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 flex items-start gap-4">
          <div class="w-11 h-11 rounded-xl bg-rose-500/15 border border-rose-500/30 flex items-center justify-center text-rose-400 shrink-0">
            <i class="fas fa-gift text-lg"></i>
          </div>
          <div>
            <h4 class="font-bold text-white text-base mb-1">Cartão Fidelidade & Cupons</h4>
            <p class="text-xs text-slate-400 leading-relaxed">Fidelize seus clientes com programas de pontos automáticos, cupons de desconto e taxas por bairro ou KM.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ==================== COMO FUNCIONA ==================== -->
  <section id="como-funciona" class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-2xl mx-auto mb-14">
        <span class="text-amber-400 font-bold text-xs uppercase tracking-widest px-3 py-1 rounded-full bg-amber-400/10 border border-amber-400/20">
          PASSO A PASSO SIMPLES
        </span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-4 mb-3">
          Sua Loja Pronta para Vender em 3 Passos
        </h2>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="glass-card rounded-3xl p-8 relative">
          <span class="text-5xl font-black text-slate-800 absolute top-6 right-6">01</span>
          <div class="w-12 h-12 rounded-2xl bg-brand-600 text-white font-bold flex items-center justify-center text-lg mb-5">1</div>
          <h3 class="text-xl font-bold text-white mb-2">Cadastre sua Loja</h3>
          <p class="text-sm text-slate-400 leading-relaxed">Escolha seu plano e defina o nome do seu subdomínio exclusivo (<span class="text-slate-200">sualoja.naregiao.com.br</span>).</p>
        </div>

        <div class="glass-card rounded-3xl p-8 relative">
          <span class="text-5xl font-black text-slate-800 absolute top-6 right-6">02</span>
          <div class="w-12 h-12 rounded-2xl bg-accent-500 text-white font-bold flex items-center justify-center text-lg mb-5">2</div>
          <h3 class="text-xl font-bold text-white mb-2">Personalize o Cardápio</h3>
          <p class="text-sm text-slate-400 leading-relaxed">Adicione sua logo, cores, categorias, produtos, fotos, horários de funcionamento e taxas de entrega.</p>
        </div>

        <div class="glass-card rounded-3xl p-8 relative">
          <span class="text-5xl font-black text-slate-800 absolute top-6 right-6">03</span>
          <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white font-bold flex items-center justify-center text-lg mb-5">3</div>
          <h3 class="text-xl font-bold text-white mb-2">Compartilhe e Receba Pedidos</h3>
          <p class="text-sm text-slate-400 leading-relaxed">Divulgue seu link no Instagram e WhatsApp e receba pedidos organizados prontos para preparo e entrega!</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ==================== PRICING SECTION (PLANOS) ==================== -->
  <section id="planos" class="py-20 lg:py-28 bg-slate-950/70 border-t border-slate-800/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-3xl mx-auto mb-16">
        <span class="text-emerald-400 font-bold text-xs uppercase tracking-widest px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20">
          PLANOS E PREÇOS TRANSPARENTES
        </span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-4 mb-4">
          Invista no Crescimento do seu Delivery
        </h2>
        <p class="text-slate-400 text-base sm:text-lg">
          Sem taxa de adesão, sem comissão sobre vendas e com todos os recursos liberados para você escalar.
        </p>
      </div>

      <!-- Featured Plans Grid (Plano Mensal & Plano Anual) -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 max-w-5xl mx-auto">

        <!-- Card 1: Plano Mensal -->
        <div class="glass-card rounded-3xl p-8 sm:p-10 flex flex-col justify-between border border-slate-700/80">
          <div>
            <div class="flex items-center justify-between mb-4">
              <div>
                <h3 class="text-2xl font-extrabold text-white">Plano Mensal</h3>
                <p class="text-sm text-slate-400">Ideal para começar sem fidelidade</p>
              </div>
              <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-800 text-slate-300 border border-slate-700">
                Sem Fidelidade
              </span>
            </div>

            <div class="my-6 pb-6 border-b border-slate-800">
              <div class="flex items-baseline gap-1">
                <span class="text-lg font-bold text-slate-400">R$</span>
                <span class="text-5xl font-extrabold text-white"><?php echo $valor_plano_mensal ?></span>
                <span class="text-slate-400 font-medium">/mês</span>
              </div>
              <p class="text-xs text-slate-400 mt-2">Cancele quando quiser • 0% de taxa por pedido</p>
            </div>

            <ul class="space-y-3.5 text-sm text-slate-300 mb-8">
              <li class="flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-400 text-base"></i>
                <span>Site exclusivo <strong>sualoja.naregiao.com.br</strong> com SSL</span>
              </li>
              <li class="flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-400 text-base"></i>
                <span>Pedidos ilimitados no Delivery, Balcão e WhatsApp</span>
              </li>
              <li class="flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-400 text-base"></i>
                <span>Cardápio Digital na Mesa via <strong>QR Code</strong></span>
              </li>
              <li class="flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-400 text-base"></i>
                <span>Painel Administrativo, PDV e Impressão Automática</span>
              </li>
              <li class="flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-400 text-base"></i>
                <span>Controle Financeiro, Estoque e Relatórios</span>
              </li>
              <li class="flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-400 text-base"></i>
                <span>Suporte técnico via WhatsApp</span>
              </li>
            </ul>
          </div>

          <form action="assinar.php" method="POST">
            <input type="hidden" name="id_item" value="<?php echo (int) $id_plano_mensal ?>">
            <button type="submit" class="w-full py-4 px-6 rounded-2xl font-extrabold text-base text-white bg-slate-800 hover:bg-slate-700 border border-slate-600 transition-all flex items-center justify-center gap-2">
              Começar no Plano Mensal <i class="fas fa-arrow-right"></i>
            </button>
          </form>
        </div>

        <!-- Card 2: Plano Anual (Destaque) -->
        <div class="relative rounded-3xl p-8 sm:p-10 flex flex-col justify-between bg-gradient-to-b from-slate-900 via-slate-900 to-brand-950/40 border-2 border-brand-500 shadow-2xl shadow-brand-600/20">
          <div class="absolute -top-4 right-6 px-4 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-gradient-to-r from-brand-600 to-accent-500 text-white shadow-lg">
            🔥 Mais Escolhido • Economize 20%
          </div>

          <div>
            <div class="flex items-center justify-between mb-4">
              <div>
                <h3 class="text-2xl font-extrabold text-white">Plano Anual</h3>
                <p class="text-sm text-rose-200/80">Máxima economia e implantação VIP inclusa</p>
              </div>
              <span class="px-3 py-1 rounded-full text-xs font-bold bg-brand-500/20 text-brand-300 border border-brand-500/30">
                VIP
              </span>
            </div>

            <div class="my-6 pb-6 border-b border-slate-800">
              <div class="flex items-baseline gap-1">
                <span class="text-lg font-bold text-slate-400">R$</span>
                <span class="text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-white to-rose-200"><?php echo $valor_plano_anual ?></span>
                <span class="text-slate-400 font-medium">/mês*</span>
              </div>
              <p class="text-xs text-emerald-400 font-semibold mt-2">
                <i class="fas fa-bolt mr-1"></i> Desconto especial no ciclo anual + Teste Grátis
              </p>
            </div>

            <ul class="space-y-3.5 text-sm text-slate-200 mb-8">
              <li class="flex items-center gap-3">
                <i class="fas fa-check-circle text-brand-500 text-base"></i>
                <span><strong>Tudo do Plano Mensal</strong> + Prioridade no Servidor</span>
              </li>
              <li class="flex items-center gap-3">
                <i class="fas fa-check-circle text-brand-500 text-base"></i>
                <span>Subdomínio exclusivo <strong>sualoja.naregiao.com.br</strong></span>
              </li>
              <li class="flex items-center gap-3">
                <i class="fas fa-check-circle text-brand-500 text-base"></i>
                <span><strong>Chatbot e Notificações WhatsApp</strong> automáticas</span>
              </li>
              <li class="flex items-center gap-3">
                <i class="fas fa-check-circle text-brand-500 text-base"></i>
                <span>QR Code de Mesas + App para Garçons e Entregadores</span>
              </li>
              <li class="flex items-center gap-3">
                <i class="fas fa-check-circle text-brand-500 text-base"></i>
                <span>Cartão Fidelidade, Cupons de Desconto e Pixel</span>
              </li>
              <li class="flex items-center gap-3">
                <i class="fas fa-check-circle text-brand-500 text-base"></i>
                <span><strong>Suporte VIP Prioritário</strong> e auxílio na configuração</span>
              </li>
            </ul>
          </div>

          <form action="assinar.php" method="POST">
            <input type="hidden" name="id_item" value="<?php echo (int) $id_plano_anual ?>">
            <button type="submit" class="w-full py-4 px-6 rounded-2xl font-extrabold text-base text-white bg-gradient-to-r from-brand-600 to-accent-500 hover:from-brand-500 hover:to-accent-600 shadow-xl shadow-brand-600/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
              <i class="fas fa-rocket"></i> Criar Minha Loja Agora
            </button>
          </form>
        </div>

      </div>

      <?php if (count($planos_db) > 2) { ?>
      <!-- Demais opções de planos cadastrados no sistema -->
      <div class="mt-14 max-w-5xl mx-auto">
        <p class="text-center text-xs font-bold uppercase tracking-widest text-slate-400 mb-6">Outras modalidades disponíveis</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <?php foreach ($planos_db as $p) {
            $p_id = (int) $p['id'];
            $p_nome = htmlspecialchars($p['nome'] ?? 'Plano');
            $p_val = number_format((float) ($p['valor'] ?? 0), 2, ',', '.');
          ?>
          <div class="glass-card rounded-2xl p-5 flex flex-col justify-between border border-slate-800">
            <div>
              <div class="text-base font-bold text-white mb-1"><?php echo $p_nome ?></div>
              <div class="text-2xl font-extrabold text-emerald-400 mb-4">R$ <?php echo $p_val ?><span class="text-xs text-slate-400 font-normal">/mês</span></div>
            </div>
            <form action="assinar.php" method="POST">
              <input type="hidden" name="id_item" value="<?php echo $p_id ?>">
              <button type="submit" class="w-full py-2.5 px-4 rounded-xl text-xs font-bold text-white bg-slate-800 hover:bg-brand-600 transition-colors">
                Selecionar <?php echo $p_nome ?>
              </button>
            </form>
          </div>
          <?php } ?>
        </div>
      </div>
      <?php } ?>
    </div>
  </section>

  <!-- ==================== CTA & CONTATO SECTION ==================== -->
  <section id="contato" class="py-20">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="rounded-3xl p-8 sm:p-12 bg-gradient-to-r from-brand-700 via-brand-600 to-accent-600 text-white shadow-2xl relative overflow-hidden">
        <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-8 text-center lg:text-left">
          <div class="max-w-2xl">
            <h2 class="text-3xl sm:text-4xl font-extrabold mb-3">
              Pronto para Ter o Seu Próprio Aplicativo de Delivery?
            </h2>
            <p class="text-rose-100 text-base sm:text-lg">
              Crie sua loja em poucos cliques ou fale agora com nosso time de suporte para tirar todas as suas dúvidas.
            </p>
          </div>
          <div class="flex flex-col sm:flex-row gap-4 shrink-0 w-full sm:w-auto">
            <a href="#planos" class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-2xl text-base font-extrabold bg-white text-brand-700 hover:bg-rose-50 shadow-xl transition-all">
              <i class="fas fa-store"></i> Criar Loja Grátis
            </a>
            <a href="<?php echo htmlspecialchars($url_whats_suporte) ?>" target="_blank" class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-2xl text-base font-bold bg-slate-950/40 hover:bg-slate-950/60 text-white border border-white/20 transition-all">
              <i class="fab fa-whatsapp text-emerald-300 text-xl"></i> Suporte WhatsApp
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ==================== FOOTER ==================== -->
  <footer class="bg-slate-950 border-t border-slate-800/80 pt-14 pb-10 text-slate-400">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-slate-800/80">

        <!-- Brand Info -->
        <div class="md:col-span-2">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-600 to-accent-500 flex items-center justify-center text-white">
              <i class="fas fa-utensils"></i>
            </div>
            <span class="text-xl font-extrabold text-white">Na Região</span>
          </div>
          <p class="text-sm text-slate-400 max-w-md leading-relaxed mb-6">
            Plataforma SaaS completa para gestão de Delivery, Cardápio Digital, Pedidos via WhatsApp e Autoatendimento em Mesas por QR Code.
          </p>
          <!-- Redes Sociais -->
          <div class="flex items-center gap-3">
            <?php if (!empty($instagram_sistema)) { ?>
            <a href="https://www.instagram.com/<?php echo htmlspecialchars(trim($instagram_sistema, '@/')) ?>/" target="_blank" aria-label="Instagram"
              class="w-10 h-10 rounded-xl bg-slate-900 hover:bg-brand-600 text-slate-300 hover:text-white border border-slate-800 flex items-center justify-center transition-colors">
              <i class="fab fa-instagram text-lg"></i>
            </a>
            <?php } else { ?>
            <a href="#contato" aria-label="Instagram"
              class="w-10 h-10 rounded-xl bg-slate-900 hover:bg-brand-600 text-slate-300 hover:text-white border border-slate-800 flex items-center justify-center transition-colors">
              <i class="fab fa-instagram text-lg"></i>
            </a>
            <?php } ?>
            <a href="<?php echo htmlspecialchars($url_whats_suporte) ?>" target="_blank" aria-label="WhatsApp"
              class="w-10 h-10 rounded-xl bg-slate-900 hover:bg-emerald-600 text-slate-300 hover:text-white border border-slate-800 flex items-center justify-center transition-colors">
              <i class="fab fa-whatsapp text-lg"></i>
            </a>
          </div>
        </div>

        <!-- Links Rápidos -->
        <div>
          <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Navegação</h4>
          <ul class="space-y-2.5 text-sm">
            <li><a href="#recursos" class="hover:text-white transition-colors">Recursos e Diferenciais</a></li>
            <li><a href="#como-funciona" class="hover:text-white transition-colors">Como Funciona</a></li>
            <li><a href="#planos" class="hover:text-white transition-colors">Planos e Preços</a></li>
            <li><a href="<?php echo $link_painel ?>" class="hover:text-white transition-colors">Área do Lojista (Entrar)</a></li>
          </ul>
        </div>

        <!-- Suporte e Contato -->
        <div>
          <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Suporte & Contato</h4>
          <ul class="space-y-2.5 text-sm">
            <li>
              <a href="<?php echo htmlspecialchars($url_whats_suporte) ?>" target="_blank" class="hover:text-white transition-colors flex items-center gap-2">
                <i class="fab fa-whatsapp text-emerald-400"></i> Central de Suporte
              </a>
            </li>
            <?php if (!empty($email_sistema)) { ?>
            <li class="flex items-center gap-2 text-slate-400">
              <i class="far fa-envelope text-brand-400"></i>
              <span><?php echo htmlspecialchars($email_sistema) ?></span>
            </li>
            <?php } ?>
            <li>
              <a href="<?php echo $link_painel ?>" class="hover:text-white transition-colors flex items-center gap-2">
                <i class="fas fa-lock text-slate-500"></i> Acesso ao Painel (/sistema)
              </a>
            </li>
          </ul>
        </div>

      </div>

      <!-- Copyright CRN Sistemas -->
      <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
        <p>&copy; <?php echo date('Y'); ?> <strong class="text-slate-300">Na Região</strong>. Todos os direitos reservados.</p>
        <p>Desenvolvido por <strong class="text-slate-300">CRN Sistemas</strong> — Tecnologia para Delivery e Gestão.</p>
      </div>
    </div>
  </footer>

  <!-- Botão Flutuante do WhatsApp -->
  <?php if ($whats_limpo !== '') { ?>
  <a title="Falar com Suporte no WhatsApp" target="_blank" href="<?php echo htmlspecialchars($url_whats_suporte) ?>"
    class="fixed bottom-5 right-5 z-50 w-14 h-14 rounded-full bg-emerald-500 hover:bg-emerald-400 text-white flex items-center justify-center shadow-2xl shadow-emerald-500/40 hover:scale-105 transition-transform">
    <i class="fab fa-whatsapp text-3xl"></i>
  </a>
  <?php } ?>

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

    // Sanitiza o preview de subdomínio em tempo real na Hero
    const slugInput = document.getElementById('preview-slug');
    if (slugInput) {
      slugInput.addEventListener('input', function () {
        this.value = this.value.toLowerCase().replace(/[^a-z0-9-]/g, '');
      });
    }
  </script>
</body>
</html>
