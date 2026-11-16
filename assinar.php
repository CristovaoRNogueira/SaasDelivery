<?php 
require_once("sistema/conexao.php");
$data_atual = date('Y-m-d');

$id_item = @$_POST['id_item'] ?: @$_GET['id_item'];

if (empty($id_item)) {
  $query_first = $pdo->query("SELECT id FROM planos WHERE ativo = 'Sim' ORDER BY id ASC LIMIT 1");
  $row_first = $query_first ? $query_first->fetch(PDO::FETCH_ASSOC) : null;
  $id_item = $row_first['id'] ?? 1;
}

$query5 = $pdo->prepare("SELECT * FROM planos WHERE id = :id_item LIMIT 1");
$query5->execute([':id_item' => (int) $id_item]);
$res5 = $query5->fetchAll(PDO::FETCH_ASSOC);

if (@count($res5) == 0) {
  $query_fallback = $pdo->query("SELECT * FROM planos WHERE ativo = 'Sim' ORDER BY id ASC LIMIT 1");
  $res5 = $query_fallback ? $query_fallback->fetchAll(PDO::FETCH_ASSOC) : [];
}

$id_item = @$res5[0]['id'] ?? $id_item;
$nome_plano = @$res5[0]['nome'] ?: 'Plano Profissional';
$valor_plano = (float) (@$res5[0]['valor'] ?? 0);
$valorF = number_format($valor_plano, 2, ',', '.');
$dispositivos_plano = (int) (@$res5[0]['dispositivos'] ?? 1);

// Buscar as informações do site (empresa = 0)
$query = $pdo->query("SELECT * FROM site WHERE empresa = '0'");
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
if ($logo == "") {
  $logo = 'sem-foto.png';
}

$tem_logo_site = ($logo != 'sem-foto.png' && $logo_topo != 'Não' && is_file(__DIR__ . '/sistema/img/logos/' . $logo));
$caminho_logo_site = $tem_logo_site
  ? ($url_sistema . 'sistema/img/logos/' . $logo)
  : (!empty($logo_sistema) && $logo_sistema != 'sem-foto.png' ? ($url_sistema . 'sistema/img/' . $logo_sistema) : '');

$nome_exibicao = !empty($nome_sistema) ? $nome_sistema : 'Na Região';

$whats_numero = !empty($tel_whats) ? preg_replace('/[^0-9]/', '', (string) $tel_whats) : preg_replace('/[^0-9]/', '', (string) ($telefone_sistema ?? ''));
if ($whats_numero !== '' && strlen($whats_numero) <= 11) {
  $whats_numero = '55' . $whats_numero;
}
$url_whats_suporte = $whats_numero !== ''
  ? "https://api.whatsapp.com/send?1=pt_BR&phone={$whats_numero}&text=" . rawurlencode("Olá! Preciso de ajuda para finalizar minha assinatura do {$nome_plano}.")
  : "#";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="keywords" content="planos assinatura, erp saas, delivery saas, <?php echo htmlspecialchars($nome_exibicao) ?>" />
  <meta name="description" content="Finalize sua assinatura do <?php echo htmlspecialchars($nome_plano) ?> no <?php echo htmlspecialchars($nome_exibicao) ?> e crie sua loja de delivery agora." />
  <meta name="author" content="CRN Sistemas" />

  <title><?php echo htmlspecialchars($nome_exibicao) ?> — Finalizar Assinatura (<?php echo htmlspecialchars($nome_plano) ?>)</title>

  <!-- Favicon -->
  <link rel="icon" href="sistema/img/<?php echo htmlspecialchars($icone_sistema ?? 'icone.png') ?>" type="image/x-icon" />

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- GSAP -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

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
              400: '#fb7185',
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
      background: rgba(9, 13, 22, 0.85);
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
    .hero-glow {
      background: radial-gradient(circle at 50% 0%, rgba(244, 63, 94, 0.18) 0%, rgba(249, 115, 22, 0.08) 40%, rgba(9, 13, 22, 0) 75%);
    }
  </style>
</head>

<body class="antialiased selection:bg-brand-600 selection:text-white min-h-screen flex flex-col hero-glow">

  <!-- ==================== TOPBAR DE CHECKOUT ==================== -->
  <header class="glass-nav sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
      <a href="/" class="flex items-center gap-3 group">
        <?php if ($caminho_logo_site !== '') { ?>
          <img src="<?php echo htmlspecialchars($caminho_logo_site) ?>" alt="<?php echo htmlspecialchars($nome_exibicao) ?>" class="h-10 w-auto object-contain rounded-xl" />
        <?php } else { ?>
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-600 to-accent-500 flex items-center justify-center shadow-lg shadow-brand-600/30">
            <i class="fas fa-utensils text-white text-base"></i>
          </div>
        <?php } ?>
        <div class="flex flex-col">
          <span class="text-lg font-extrabold tracking-tight text-white"><?php echo htmlspecialchars($nome_exibicao) ?></span>
          <span class="text-[11px] font-medium text-slate-400 -mt-1">Ambiente Seguro de Assinatura</span>
        </div>
      </a>

      <!-- Steps Indicator (Desktop) -->
      <div class="hidden md:flex items-center gap-3 text-xs font-bold">
        <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-500/15 border border-brand-500/40 text-brand-400">
          <span class="w-5 h-5 rounded-full bg-brand-600 text-white flex items-center justify-center text-[11px]">1</span>
          <span>Dados da Loja</span>
        </div>
        <div class="w-10 h-0.5 bg-slate-800 overflow-hidden rounded-full">
          <div id="progress-bar" class="h-full w-0 bg-gradient-to-r from-brand-500 to-accent-500"></div>
        </div>
        <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-900 border border-slate-800 text-slate-400">
          <span class="w-5 h-5 rounded-full bg-slate-800 text-slate-400 flex items-center justify-center text-[11px]">2</span>
          <span>Pagamento & Liberação</span>
        </div>
      </div>

      <a href="/" class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-300 hover:text-white px-3.5 py-2 rounded-xl bg-slate-800/70 hover:bg-slate-800 border border-slate-700/80 transition-all">
        <i class="fas fa-arrow-left text-brand-400"></i>
        <span>Voltar aos Planos</span>
      </a>
    </div>
  </header>

  <!-- ==================== MAIN CHECKOUT CONTENT (2 COLUNAS) ==================== -->
  <main class="flex-grow py-10 sm:py-14 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">

      <!-- Cabeçalho da Página -->
      <div class="text-center max-w-2xl mx-auto mb-10">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold uppercase tracking-wider mb-3">
          <i class="fas fa-bolt"></i> Liberação Imediata após Confirmação
        </span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight mb-2">
          Crie sua Loja no <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-500 to-accent-500"><?php echo htmlspecialchars($nome_exibicao) ?></span>
        </h1>
        <p class="text-slate-400 text-sm sm:text-base">
          Complete os dados abaixo para ativar o plano <strong class="text-white"><?php echo htmlspecialchars($nome_plano) ?></strong> e garantir seu subdomínio exclusivo.
        </p>
      </div>

      <!-- Grid Dividido: Coluna 1 (Esquerda - Resumo do Pedido) | Coluna 2 (Direita - Formulário) -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- ==================== COLUNA 1 (ESQUERDA): RESUMO DO PLANO ==================== -->
        <div class="lg:col-span-5 order-2 lg:order-1">
          <div class="relative rounded-3xl p-7 sm:p-8 bg-gradient-to-b from-slate-900 via-slate-900 to-brand-950/40 border-2 border-brand-500/80 shadow-2xl shadow-brand-600/15">
            <div class="flex items-center justify-between gap-2 pb-6 mb-6 border-b border-slate-800">
              <div>
                <span class="inline-block text-[11px] font-extrabold uppercase tracking-widest text-brand-400 mb-1">
                  Resumo do Pedido
                </span>
                <h2 class="text-2xl font-extrabold text-white"><?php echo htmlspecialchars($nome_plano) ?></h2>
              </div>
              <span class="px-3 py-1 rounded-full text-xs font-bold bg-brand-500/20 text-brand-300 border border-brand-500/30">
                Selecionado
              </span>
            </div>

            <!-- Valor em Destaque -->
            <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800/90 mb-6">
              <div class="text-xs font-semibold text-slate-400 mb-1">Investimento Mensal</div>
              <div class="flex items-baseline gap-1">
                <span class="text-lg font-bold text-slate-400">R$</span>
                <span class="text-4xl sm:text-5xl font-extrabold text-white"><?php echo $valorF ?></span>
                <span class="text-sm text-slate-400 font-medium">/mês</span>
              </div>
              <div class="mt-3 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-emerald-400 font-semibold"><i class="fas fa-check-circle mr-1"></i> 0% de comissão por venda</span>
                <span class="text-slate-400">Sem fidelidade</span>
              </div>
            </div>

            <!-- Lista de Recursos do Plano vindos do Banco -->
            <div class="mb-6">
              <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300 mb-4 flex items-center gap-2">
                <i class="fas fa-star text-amber-400"></i> Recursos Incluídos no Plano:
              </h3>
              <ul class="space-y-3 text-sm text-slate-200">
                <?php 
                $query2 = $pdo->prepare("SELECT * FROM planos_itens WHERE plano = :plano ORDER BY id ASC");
                $query2->execute([':plano' => (int) $id_item]);
                $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
                $total_reg2 = @count($res2);
                if ($total_reg2 > 0) {
                  for ($i2 = 0; $i2 < $total_reg2; $i2++) {  
                    $nome_item_carac = trim((string) (@$res2[$i2]['nome'] ?? ''));  
                    if ($nome_item_carac === '') continue;
                ?>
                <li class="flex items-start gap-3">
                  <i class="fas fa-check-circle text-emerald-400 mt-1 shrink-0"></i> 
                  <span><?php echo htmlspecialchars($nome_item_carac) ?></span>
                </li>
                <?php } } else { ?>
                <li class="flex items-start gap-3">
                  <i class="fas fa-check-circle text-emerald-400 mt-1 shrink-0"></i>
                  <span>Cardápio Digital Completo e Pedidos Ilimitados</span>
                </li>
                <li class="flex items-start gap-3">
                  <i class="fas fa-check-circle text-emerald-400 mt-1 shrink-0"></i>
                  <span>Painel de Gestão, PDV e Impressão Automática</span>
                </li>
                <?php } ?>
                <li class="flex items-start gap-3">
                  <i class="fas fa-check-circle text-brand-400 mt-1 shrink-0"></i>
                  <span>Subdomínio próprio <strong>sualoja.naregiao.com.br</strong> com SSL</span>
                </li>
              </ul>
            </div>

            <!-- Garantias / Segurança -->
            <div class="pt-5 border-t border-slate-800 space-y-3 text-xs text-slate-400">
              <div class="flex items-center gap-2.5">
                <i class="fas fa-shield-alt text-emerald-400 text-sm"></i>
                <span>Ambiente criptografado ponta a ponta (SSL 256-bits)</span>
              </div>
              <div class="flex items-center gap-2.5">
                <i class="fab fa-whatsapp text-emerald-400 text-sm"></i>
                <span>Suporte técnico especializado via WhatsApp</span>
              </div>
            </div>
          </div>
        </div>

        <!-- ==================== COLUNA 2 (DIREITA): DADOS DA LOJA / USUÁRIO ==================== -->
        <div class="lg:col-span-7 order-1 lg:order-2">
          <div class="glass-card rounded-3xl p-7 sm:p-10 border border-slate-700/80 shadow-2xl">
            <div class="mb-7 pb-5 border-b border-slate-800">
              <h2 class="text-2xl font-extrabold text-white flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-brand-500/15 border border-brand-500/30 flex items-center justify-center text-brand-400 text-base">
                  <i class="fas fa-store"></i>
                </span>
                Dados da sua Loja e Acesso
              </h2>
              <p class="text-slate-400 text-sm mt-1">
                Preencha as informações abaixo para configurar seu painel e endereço exclusivo.
              </p>
            </div>

            <form id="form-agenda" method="post" class="space-y-5">

              <!-- Nome da Empresa / Responsável e Telefone -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                  <label for="nome" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    Nome da Loja / Responsável <span class="text-brand-500">*</span>
                  </label>
                  <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                      <i class="fas fa-store"></i>
                    </span>
                    <input type="text" id="nome" name="nome" placeholder="Ex: Pizzaria A Quadrada" required
                      class="w-full pl-10 pr-4 p-3 rounded-lg bg-slate-900/90 border border-gray-300/20 text-white placeholder-slate-500 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all" />
                  </div>
                </div>

                <div>
                  <label for="telefone" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    WhatsApp / Telefone <span class="text-brand-500">*</span>
                  </label>
                  <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-emerald-400 pointer-events-none">
                      <i class="fab fa-whatsapp"></i>
                    </span>
                    <input type="text" id="telefone" name="telefone" placeholder="(00) 00000-0000" required
                      onkeyup="verificarTelefone('telefone', this.value)"
                      class="w-full pl-10 pr-4 p-3 rounded-lg bg-slate-900/90 border border-gray-300/20 text-white placeholder-slate-500 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all" />
                  </div>
                </div>
              </div>

              <!-- Escolha do Subdomínio Exclusivo (url_site) -->
              <div>
                <label for="url_site" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                  Endereço Exclusivo da sua Loja (Subdomínio) <span class="text-brand-500">*</span>
                </label>
                <div class="flex items-stretch rounded-lg overflow-hidden border border-gray-300/25 bg-slate-900/90 focus-within:ring-2 focus-within:ring-brand-500 focus-within:border-brand-500 transition-all">
                  <span class="hidden sm:inline-flex items-center px-3.5 bg-slate-950 text-slate-400 text-xs font-semibold border-r border-slate-800 select-none">
                    <i class="fas fa-lock text-emerald-400 mr-1.5"></i> https://
                  </span>
                  <input type="text" id="url_site" name="url_site" placeholder="sualoja" required
                    class="flex-1 min-w-0 p-3 bg-transparent text-white placeholder-slate-500 text-sm font-bold focus:outline-none text-right sm:text-left" />
                  <span class="inline-flex items-center px-3.5 bg-slate-950 text-brand-400 text-xs sm:text-sm font-extrabold border-l border-slate-800 select-none">
                    .naregiao.com.br
                  </span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1.5">
                  <i class="fas fa-globe text-brand-400"></i>
                  <span>Seu link ficará: <strong class="text-white" id="preview-url-completa">https://sualoja.naregiao.com.br</strong> (use apenas letras minúsculas, números ou hífen).</span>
                </p>
              </div>

              <!-- Tipo de Pessoa, CPF/CNPJ e Email -->
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                  <label for="tipo_pessoa" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    Tipo de Pessoa
                  </label>
                  <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                      <i class="fas fa-id-card"></i>
                    </span>
                    <select id="tipo_pessoa" name="tipo_pessoa" onchange="mudarPessoa()"
                      class="w-full pl-10 pr-4 p-3 rounded-lg bg-slate-900/90 border border-gray-300/20 text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all">
                      <option value="Física">Pessoa Física</option>
                      <option value="Jurídica">Pessoa Jurídica</option>
                    </select>
                  </div>
                </div>

                <div>
                  <label for="cpf" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    CPF / CNPJ <span class="text-brand-500">*</span>
                  </label>
                  <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                      <i class="fas fa-fingerprint"></i>
                    </span>
                    <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" required
                      class="w-full pl-10 pr-4 p-3 rounded-lg bg-slate-900/90 border border-gray-300/20 text-white placeholder-slate-500 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all" />
                  </div>
                </div>

                <div>
                  <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    E-mail de Acesso <span class="text-brand-500">*</span>
                  </label>
                  <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                      <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" id="email" name="email" placeholder="contato@sualoja.com" required
                      class="w-full pl-10 pr-4 p-3 rounded-lg bg-slate-900/90 border border-gray-300/20 text-white placeholder-slate-500 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all" />
                  </div>
                </div>
              </div>

              <!-- Campos Ocultos Obrigatórios para salvar.php -->
              <input type="hidden" name="plano" value="<?php echo (int) $id_item ?>">
              <input type="hidden" name="dias_teste" value="3">
              <input type="hidden" name="mensalidade" value="<?php echo htmlspecialchars($valorF) ?>">
              <input type="hidden" name="dispositivos" value="<?php echo (int) $dispositivos_plano ?>">
              <input type="hidden" name="assinatura" value="Sim">

              <!-- Botão Finalizar Cadastro / Assinar -->
              <div class="pt-4">
                <button id="btn_agendar" type="submit"
                  class="w-full py-4 px-6 rounded-xl font-extrabold text-base text-white bg-gradient-to-r from-brand-600 to-accent-500 hover:from-brand-500 hover:to-accent-600 shadow-xl shadow-brand-600/30 hover:shadow-brand-600/50 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2.5 group">
                  <span>FINALIZAR CADASTRO E ASSINAR</span>
                  <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </button>

                <div id="mensagem" class="text-center mt-3 text-sm font-medium"></div>

                <!-- Badge de Segurança -->
                <div class="mt-5 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/25 flex items-center gap-3.5">
                  <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
                    <i class="fas fa-lock text-lg"></i>
                  </div>
                  <div>
                    <p class="font-bold text-xs sm:text-sm text-white">Cadastro 100% Seguro e Instantâneo</p>
                    <p class="text-xs text-slate-300">Seus dados estão protegidos e seu acesso administrativo é gerado automaticamente.</p>
                  </div>
                </div>
              </div>

            </form>
          </div>
        </div>

      </div>
    </div>
  </main>

  <!-- ==================== FOOTER ==================== -->
  <footer class="border-t border-slate-800/80 bg-slate-950/80 py-8 mt-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
      <p>&copy; <?php echo date('Y'); ?> <strong class="text-slate-300"><?php echo htmlspecialchars($nome_exibicao) ?></strong>. Todos os direitos reservados.</p>
      <p>Desenvolvido por <strong class="text-slate-300">CRN Sistemas</strong> — Tecnologia para Delivery e Gestão.</p>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    var slugEditadoManualmente = false;

    function higienizarSlug(valor) {
      return valor
        .toString()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9-]/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');
    }

    function atualizarPreviewSlug() {
      var slug = $('#url_site').val() || 'sualoja';
      $('#preview-url-completa').text('https://' + slug + '.naregiao.com.br');
    }

    $(document).ready(function() {
      $('#cpf').mask('000.000.000-00');
      $('#telefone').mask('(00) 00000-0000');

      // Sugere o subdomínio automaticamente a partir do nome da loja se o usuário ainda não editou manualmente
      $('#nome').on('input', function() {
        if (!slugEditadoManualmente) {
          var sugerido = higienizarSlug($(this).val());
          $('#url_site').val(sugerido);
          atualizarPreviewSlug();
        }
      });

      $('#url_site').on('input', function() {
        slugEditadoManualmente = true;
        var limpo = higienizarSlug($(this).val());
        $(this).val(limpo);
        atualizarPreviewSlug();
      });

      // Animação da barra de progresso no topo
      if (typeof gsap !== 'undefined') {
        gsap.to("#progress-bar", {
          width: "100%",
          duration: 1,
          delay: 0.3,
          ease: "power2.out"
        });
      }
    });

    // Função para verificar telefone
    function verificarTelefone(tel, valor) {
      if (valor.length > 14) {
        $('#' + tel).mask('(00) 00000-0000');
      } else if (valor.length == 14) {
        $('#' + tel).mask('(00) 0000-00000');
      } else {
        $('#' + tel).mask('(00) 0000-0000');
      }
      var el = document.getElementById(tel);
      if (el) {
        el.setSelectionRange(100, 100);
      }
    }

    // Função para mudar tipo de pessoa
    function mudarPessoa() {
      var pessoa = $('#tipo_pessoa').val();
      if (pessoa == 'Física') {
        $('#cpf').mask('000.000.000-00');
        $('#cpf').attr("placeholder", "000.000.000-00");
      } else {
        $('#cpf').mask('00.000.000/0000-00');
        $('#cpf').attr("placeholder", "00.000.000/0000-00");
      }
    }

    // Submissão do formulário de assinatura
    $("#form-agenda").submit(function (event) {
      event.preventDefault();

      $('#btn_agendar').html('<i class="fas fa-circle-notch fa-spin"></i> <span>PROCESSANDO CADASTRO...</span>');
      $('#btn_agendar').attr('disabled', true);
      $('#btn_agendar').addClass('opacity-75 cursor-not-allowed');
      $('#mensagem').html('<span class="text-amber-400">Processando sua solicitação...</span>');

      var formData = new FormData(this);

      $.ajax({
        url: "sistema/sas/paginas/clientes/salvar.php",
        type: 'POST',
        data: formData,
        success: function (mensagem) {
          var msg = mensagem.split('*');
          var id_agd = msg[1];

          if (msg[0].trim() == "Salvo com Sucesso") {
            Swal.fire({
              icon: 'success',
              title: 'Cadastro Realizado!',
              text: 'Redirecionando para a página de pagamento...',
              showConfirmButton: false,
              timer: 2000,
              timerProgressBar: true,
              didOpen: () => {
                Swal.showLoading();
              }
            }).then(() => {
              window.location = "pagamento_plano/" + id_agd;
            });
          } else {
            Swal.fire({
              icon: 'warning',
              title: 'Atenção!',
              text: msg[0]
            });
            $('#mensagem').html('<span class="text-rose-400">' + msg[0] + '</span>');
            $('#btn_agendar').html('<span>FINALIZAR CADASTRO E ASSINAR</span> <i class="fas fa-arrow-right"></i>');
            $('#btn_agendar').attr('disabled', false);
            $('#btn_agendar').removeClass('opacity-75 cursor-not-allowed');
          }
        },
        cache: false,
        contentType: false,
        processData: false,
      });
    });
  </script>
</body>
</html>
