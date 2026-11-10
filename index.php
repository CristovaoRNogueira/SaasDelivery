<?php 
session_start();
require_once("sistema/conexao.php");

// Verifica se o usuário está logado (exemplo: verifica se existe uma sessão)
if (!isset($_SESSION['usuario_logado_pagina'])) {
    if($pagina_entrada == 'Login'){
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
    $base = rtrim($url_sistema ?? '', '/');      // ex.: http://localhost/veterinaria
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

//buscar as informações
$query = $pdo->query("SELECT * FROM site where empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
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

$bg_desktop = "";
$bg_mobile = "";

if($fundo_topo != "sem-foto.png"){
  $bg_desktop = $url_sistema . "sistema/img/logos/" . $fundo_topo;
}

if($fundo_topo_mobile != "sem-foto.png"){
  $bg_mobile = $url_sistema . "sistema/img/logos/" . $fundo_topo_mobile;
}

if($logo == ""){
  $logo = 'sem-foto.png';
}

// Separando o título em palavras
$palavras = @explode(' ', $titulo);
// Pegando as últimas duas palavras
$ultimo_palavra = array_pop($palavras);
$penultima_palavra = array_pop($palavras);

// Recriar o título com as últimas duas palavras com a classe 'text-accent'
$titulo_modificado = implode(' ', $palavras) . ' <span class="text-accent">' . $penultima_palavra . ' ' . $ultimo_palavra . '</span>';


if (@strpos($subtitulo, $nome_sistema) !== false) {
    // Se o texto foi encontrado, colocamos ele dentro da tag <strong>
    $subtitulo_modificado = @str_ireplace($nome_sistema, "<strong>$nome_sistema</strong>", $subtitulo);
} else {
    // Caso não tenha o texto, usamos o título original
    $subtitulo_modificado = $subtitulo;
}


if (@strpos($descricao_rodape, $nome_sistema) !== false) {
    // Se o texto foi encontrado, colocamos ele dentro da tag <strong>
    $descricao_rodape_modificado = @str_ireplace($nome_sistema, "<strong class='text-white'>$nome_sistema</strong>", $descricao_rodape);
} else {
    // Caso não tenha o texto, usamos o título original
    $descricao_rodape_modificado = $descricao_rodape;
}

if($id_empresa == 0){
  $link_painel = 'sistema';
}else{
  $link_painel = '../acesso';
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="keywords" content="Gestor SAAS Barbearia, sistema de gestão barbearias, <?php echo $nome_sistema ?>, controle financeiro, gestão de estoque, agendamentos" />
  <meta name="description" content="<?php echo $meta_descricao ?>" />
  <meta name="author" content="Hugo Vasconcelos" />
  <title><?php echo $nome_sistema ?></title>
  <!-- Favicon -->
  <link rel="icon" href="sistema/img/<?php echo $icone_sistema ?>" type="image/x-icon" />
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- GSAP -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              DEFAULT: '#2d4c63',
              dark: '#1a2c3d',
              light: '#3a6080'
            },
            accent: {
              DEFAULT: '#ffc107',
              dark: '#e0a800',
              light: '#ffcd38'
            },
            success: '#10b981',
            warning: '#f59e0b',
            danger: '#ef4444',
            customAzul: '#045d8a',
            customGreen: '#2ba304', // Adicionando a cor personalizada
          },
          fontFamily: {
            poppins: ['Poppins', 'sans-serif'],
          },
        }
      }
    }
  </script>

    <style>
  @media (max-width: 768px) {
    .fundo-topo {
      background-image: url('<?php echo $bg_mobile ?>') !important;
    }
  }
</style>

  <link rel="stylesheet" href="css/estilos_site.css" />
</head>
<body class="font-poppins bg-gradient-to-br from-primary-dark to-primary text-white min-h-screen overflow-x-hidden">
  <div class="py-12 px-4 sm:px-6 lg:px-8">
    <div class="container mx-auto">
      
      <!-- Bloco CTA Simplificado -->
      <div class="mb-12 bg-primary-dark p-8 rounded-xl border border-accent/20 shadow-lg fundo-topo"
     style="background-image: url('<?php echo $bg_desktop ?>'); background-size: cover; background-position: center;">
        <div class="text-center mb-6">
          <!-- Logo adicionada acima do título -->
          <div class="flex justify-center mb-6">
            <?php if($logo_topo != 'Não'){ ?>
            <img src="<?php echo $url_sistema ?>sistema/img/logos/<?php echo $logo ?>" alt="Logo" class="large-logo" />
          <?php }else{ echo '<br>&nbsp;<br><br>&nbsp;<br><br>&nbsp;<br>'; } ?>
          </div>
          
          <?php if($titulo != ""){ ?>
          <h1 class="text-3xl md:text-4xl font-bold mb-4">
            <?php echo $titulo_modificado ?>
          </h1>
        <?php } ?>

        <?php if($subtitulo != ""){ ?>
          <p class="text-gray-300 text-lg max-w-3xl mx-auto">
            <strong class="text-white"></strong> <?php echo $subtitulo_modificado ?>
          </p>
        <?php } ?>

        </div>
        
        <?php if($botao1 != "" || $botao2 != "" || $botao3 != ""){ ?>
        <div class="flex flex-col sm:flex-row justify-center gap-4 mb-6">
          <?php if($botao1 != ""){ ?>
          <a href="#plans" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-lg text-white bg-customGreen hover:bg-green-700 focus:outline-none animate-pulse-green transition-all duration-300">
            <i class="fas fa-rocket mr-2"></i> <?php echo $botao1 ?>
          </a>
        <?php } ?>
         <?php if($botao2 != ""){ ?>
          <a href="#faq-container" class="inline-flex items-center justify-center px-6 py-3 border border-white/20 text-base font-medium rounded-lg shadow-lg text-white hover:bg-white/10 focus:outline-none transition-all duration-300">
            <i class="fas fa-info-circle mr-2"></i> <?php echo $botao2 ?>
          </a>
           <?php } ?>
         <?php if($botao3 != ""){ ?>
           <a href="<?php echo $link_painel ?>" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-lg text-white bg-customAzul hover:bg-blue-700 focus:outline-none animate-pulse-blue transition-all duration-300">
            <i class="fas fa-key mr-2"></i> <?php echo $botao3 ?>
          </a>
        <?php } ?>
        </div>
      <?php } ?>
        
        <?php if($item1 != "" || $item2 != "" || $item3 != ""){ ?>
        <div class="flex justify-center flex-wrap gap-4 text-sm text-gray-300">
          <div class="flex items-center">
            <i class="fas fa-check-circle text-accent mr-2"></i> <?php echo $item1 ?>
          </div>
          <div class="flex items-center">
            <i class="fas fa-lock text-accent mr-2"></i> <?php echo $item2 ?>
          </div>
          <div class="flex items-center">
            <i class="fas fa-headset text-accent mr-2"></i> <?php echo $item3 ?>
          </div>
        </div>
      <?php } ?>

      <br>&nbsp;<br>

      </div>

      <?php 
        $query = $pdo->query("SELECT * FROM recursos_site where empresa = '$id_empresa' order by posicao_recurso asc");
          $res = $query->fetchAll(PDO::FETCH_ASSOC);
          $total_reg = @count($res);
          if($total_reg > 0) {
       ?>
      
      <!-- Feature Cards Section -->
      <div class="mb-10 md:mb-20">
        <?php if($titulo_recursos != ""){ ?>
        <h2 class="text-3xl font-bold text-center mb-6 md:mb-10 gsap-fade-up"><?php echo $titulo_recursos ?></h2>
      <?php } ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8" id="features-container">

          <?php 
            for ($i = 0; $i < $total_reg; $i++) {
              $id = $res[$i]['id'];
              $titulo = $res[$i]['titulo_recurso'];
              $icone = $res[$i]['icone_recurso'];
              $descricao = $res[$i]['descricao_recurso'];
              $posicao = $res[$i]['posicao_recurso'];     
   
           ?>
          <div class="bg-glass rounded-xl p-6 text-center feature-card gsap-stagger-item">
            <div class="text-4xl mb-4 custom-green-icon" style="color: #2ba304;">
              <i class="<?php echo $icone ?>"></i>
            </div>
            <h3 class="text-xl font-semibold mb-3"><?php echo $titulo ?></h3>
            <p class="text-gray-300"><?php echo $descricao ?></p>
          </div>
        <?php } ?>
          
         
        </div>
      </div>

    <?php } ?>


      <?php if($id_empresa == 0){ ?>
      <!-- Dynamic Plans Section -->
      <div class="mb-12 md:mb-20" id="plans">
        <h2 class="text-2xl md:text-3xl font-bold text-center mb-6 md:mb-10 gsap-fade-up">Escolha o Plano Ideal para Seu Negócio</h2>
        <?php if($modo_teste == 'Sim'){ ?>
        <p align="center">(PLANOS APENAS DEMONSTRATIVOS, ESSE É UM SITE DE DEMONSTRAÇÃO DO SISTEMA, NÃO COMPRAR PLANO AQUI)<br></p>
      <?php } ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6" id="plans-container">
         <?php 
// Buscar todos os planos ativos
$query = $pdo->query("SELECT * FROM planos WHERE ativo = 'Sim'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);

if($total_reg > 0) {

    // Buscar o ID do plano que possui mais itens
    $query5 = $pdo->query("
        SELECT planos.id, COUNT(planos_itens.id) as total_itens
        FROM planos
        LEFT JOIN planos_itens ON planos.id = planos_itens.plano
        WHERE planos.ativo = 'Sim'
        GROUP BY planos.id
        ORDER BY total_itens DESC
        LIMIT 1
    ");
    $res5 = $query5->fetch(PDO::FETCH_ASSOC);
    
    $id_plano_mais_itens = $res5['id'] ?? 0;
    $total_itens_plano = $res5['total_itens'] ?? 0;

    // Agora você já tem o ID e total de itens do plano mais completo
    // (Se quiser usar depois)

    // Exibir todos os planos
    for($i=0; $i < $total_reg; $i++) {
        $id_item = $res[$i]['id'];
        $nome_item = $res[$i]['nome'];  
        $valor = $res[$i]['valor'];  
        $ativo = $res[$i]['ativo'];    
        $valorF = number_format($valor, 2, ',', '.');
?>
          <div class="transform transition-all duration-300 hover:-translate-y-2 gsap-stagger-item plan-card">
            <div class="bg-glass border border-gray-700 rounded-xl overflow-hidden h-full flex flex-col shadow-lg">
              <div class="p-6 text-center border-b border-gray-700">
                <h3 class="text-xl font-semibold mb-3"><?php echo $nome_item ?></h3>
                <div class="flex items-baseline justify-center">
                  <span class="text-lg">R$</span>
                  <span class="text-4xl font-bold "><?php echo $valorF ?></span>
                  <span class="text-gray-400 ml-1">/mês</span>
                </div>
              </div>
              <div class="p-6 flex-grow">
                <ul class="space-y-2">
                  <?php 
                  $query2 = $pdo->query("SELECT * FROM planos_itens where plano = '$id_item'");
                  $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
                  $total_reg2 = @count($res2);
                  if($total_reg2 > 0) {
                    for($i2=0; $i2 < $total_itens_plano; $i2++) {  
                      $nome_item_carac = @$res2[$i2]['nome'];  
                      if($nome_item_carac == "") {
                        $nome_item_carac = " - ";
                      }
                  ?>
                  <li class="flex items-center feature-item"><i class="fas fa-check-circle text-accent mr-2"></i> <?php echo $nome_item_carac ?></li>
                  <?php 
                    }
                  } 
                  ?>
                </ul>
              </div>
              <div class="p-6 text-center">
                <form action="assinar.php" method="POST" target="_blank">
                  <input type="hidden" name="id_item" value="<?php echo $id_item ?>">
                  <button class="w-full bg-customGreen hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-full transition-all duration-300 animate-pulse-green btn-subscribe" type="submit">
                    Assinar Agora <i class="fas fa-arrow-right ml-2"></i>
                  </button>
                </form>
              </div>
            </div>
          </div>
          <?php 
            }
          } 
          ?>
        </div>
      </div>

    <?php } ?>

         <?php 
        $query = $pdo->query("SELECT * FROM perguntas_site where empresa = '$id_empresa' order by posicao_pergunta asc");
          $res = $query->fetchAll(PDO::FETCH_ASSOC);
          $total_reg = @count($res);
          if($total_reg > 0) {
       ?>

      <!-- FAQ Section -->
      <div class="mb-12 md:mb-20">
        <?php if($titulo_perguntas != ""){ ?>
        <h2 class="text-2xl md:text-3xl font-bold text-center mb-6 md:mb-10 gsap-fade-up"><?php echo $titulo_perguntas ?></h2>
      <?php } ?>
        <div class="max-w-3xl mx-auto space-y-3 md:space-y-4 px-4" id="faq-container">

           <?php 
            for ($i = 0; $i < $total_reg; $i++) {
              $id = $res[$i]['id'];
              $titulo = $res[$i]['titulo_pergunta'];              
              $descricao = $res[$i]['descricao_pergunta'];
              $posicao = $res[$i]['posicao_pergunta'];              

               if (@strpos($descricao, $nome_sistema) !== false) {
                // Se o texto foi encontrado, colocamos ele dentro da tag <strong>
                $descricao_modificado = @str_ireplace($nome_sistema, "<strong>$nome_sistema</strong>", $descricao);
            } else {
                // Caso não tenha o texto, usamos o título original
                $descricao_modificado = $descricao;
            }  
   
           ?>

          <div class="bg-glass border border-gray-700 rounded-xl overflow-hidden gsap-stagger-item faq-item">
            <button class="w-full flex justify-between items-center p-4 text-left font-semibold focus:outline-none" 
                    onclick="toggleFaq('faq<?php echo $id ?>')">
              <?php echo $titulo ?>
              <i class="fas fa-chevron-down transition-transform duration-300" id="icon-faq1"></i>
            </button>
            <div class="hidden p-4 bg-gray-900 bg-opacity-50 border-t border-gray-700" id="faq<?php echo $id ?>">
             <?php echo $descricao_modificado ?>
            </div>
          </div>

        <?php } ?>
               
        
        </div>
      </div>

    <?php } ?>



      <!-- CTA Final após FAQ -->
      <div class="mb-12 md:mb-20 bg-glass rounded-xl p-8 border border-accent/20 shadow-lg text-center">
          <?php if($titulo_rodape != ""){ ?>
        <h2 class="text-2xl md:text-3xl font-bold mb-4"><?php echo $titulo_rodape ?></h2>
      <?php } ?>

       <?php if($descricao_rodape_modificado != ""){ ?>
        <p class="text-gray-300 text-lg max-w-3xl mx-auto mb-8">
          <?php echo $descricao_rodape_modificado ?>
        </p>
      <?php } ?>

      <?php if($botao_rodape != ""){ ?>
        <div class="flex justify-center">
          <a href="<?php echo $link_rodape ?>" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-lg text-white bg-customGreen hover:bg-green-700 focus:outline-none animate-pulse-green transition-all duration-300 transform hover:-translate-y-1">
            <i class="fas fa-rocket mr-2"></i> <?php echo $botao_rodape ?>
          </a>
        </div>
      <?php } ?>

      </div>
      <!-- Footer -->
      <footer class="py-4 md:py-6 border-t border-gray-700 gsap-fade-up">
        <div class="container mx-auto flex flex-col md:flex-row items-center justify-center md:justify-between px-4">
          <!-- Logo Grande no Footer - ajustado para ser menor em dispositivos móveis -->
          <div class="flex justify-center mb-4 md:mb-6">
            <img src="<?php echo $url_sistema ?>sistema/img/logos/<?php echo $logo ?>" alt="Logo" class="large-logo-footer h-16 md:h-20" />
          </div>
          <div class="text-center md:text-right">
            <p class="text-gray-400 text-sm">&copy; <?php echo date('Y'); ?> <?php echo $nome_sistema ?>. Todos os direitos reservados.</p>
            <p class="text-gray-500 text-xs md:text-sm mt-1 md:mt-2"><?php echo $nome_sistema ?> – Tecnologia que simplifica e acelera resultados!</p>
          </div>
        </div>
      </footer>
    </div>
  </div>



 <a title="Ir para o whatsapp" target="_blank" href="http://api.whatsapp.com/send?1=pt_BR&phone=<?php echo @$tel_whats ?>"><img src="<?php echo $url_sistema ?>sistema/img/logo_whats.png" width="50px" style="position:fixed; bottom:20px; right:20px; z-index: 100"></a>


 <script src="assets/js/lgpd-cookie.js" type="module"></script>
<Lgpd-cookie text='Não capturamos nenhuma informação de dados sensíveis, somente cookies para melhor performance de nosso website!' />

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- JavaScript for FAQ Accordion and Responsive Utilities -->
  <script>
    // FAQ Accordion
    function toggleFaq(id) {
      const content = document.getElementById(id);
      const icon = document.getElementById('icon-' + id);
      if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.classList.add('rotate-180');
      } else {
        content.classList.add('hidden');
        icon.classList.remove('rotate-180');
      }
    }
    // Utilitários responsivos
    function adjustForScreenSize() {
      const isMobile = window.innerWidth < 768;
      const isSmallMobile = window.innerWidth < 480;
      // Ajustar tamanho do logo
      const logos = document.querySelectorAll('.large-logo');
      logos.forEach(logo => {
        if (isSmallMobile) {
          logo.style.height = '70px';
        } else if (isMobile) {
          logo.style.height = '90px';
        } else {
          logo.style.height = '120px';
        }
      });
      // Ajustar tamanho do logo no footer
      const footerLogos = document.querySelectorAll('.large-logo-footer');
      footerLogos.forEach(logo => {
        if (isSmallMobile) {
          logo.style.height = '50px';
        } else if (isMobile) {
          logo.style.height = '60px';
        } else {
          logo.style.height = '80px';
        }
      });
      // Ajustar padding em cards
      const cards = document.querySelectorAll('.feature-card, .plan-card .bg-glass');
      cards.forEach(card => {
        if (isMobile) {
          card.style.padding = isSmallMobile ? '0.75rem' : '1rem';
        }
      });
    }
    // Melhorar o scroll em dispositivos móveis
    function smoothScrollToElement(elementId) {
      const element = document.getElementById(elementId);
      if (element) {
        const headerOffset = 60;
        const elementPosition = element.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
        window.scrollTo({
          top: offsetPosition,
          behavior: 'smooth'
        });
      }
    }
    // GSAP Animations
    document.addEventListener('DOMContentLoaded', function() {
      // Executar ajustes responsivos
      adjustForScreenSize();
      // Adicionar listener para redimensionamento
      window.addEventListener('resize', adjustForScreenSize);
      // Substituir links de âncora por scroll suave
      const anchorLinks = document.querySelectorAll('a[href^="#"]');
      anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          const targetId = this.getAttribute('href').substring(1);
          smoothScrollToElement(targetId);
        });
      });
      // Registrar o plugin ScrollTrigger
      gsap.registerPlugin(ScrollTrigger);
      // Animação dos títulos de seção
      gsap.utils.toArray(".gsap-fade-up").forEach(element => {
        gsap.to(element, {
          scrollTrigger: {
            trigger: element,
            start: "top 80%",
          },
          opacity: 1,
          y: 0,
          duration: 0.8,
          ease: "power2.out"
        });
      });
      // Animação dos cards de planos
      gsap.to(".plan-card", {
        scrollTrigger: {
          trigger: "#plans-container",
          start: "top 70%",
        },
        opacity: 1,
        y: 0,
        stagger: 0.1,
        duration: 0.6,
        ease: "back.out(1.2)"
      });
      // Animação dos preços
      gsap.from(".price-text", {
        scrollTrigger: {
          trigger: "#plans-container",
          start: "top 60%",
        },
        textContent: 0,
        duration: 1.5,
        ease: "power1.out",
        snap: { textContent: 1 },
        stagger: 0.1,
        delay: 0.5
      });
      // Animação dos itens de recursos
      gsap.from(".feature-item", {
        scrollTrigger: {
          trigger: "#plans-container",
          start: "top 50%",
        },
        opacity: 0,
        x: -20,
        stagger: 0.05,
        duration: 0.4,
        delay: 0.8
      });
      // Animação dos cards de recursos
      gsap.to(".feature-card", {
        scrollTrigger: {
          trigger: "#features-container",
          start: "top 70%",
        },
        opacity: 1,
        y: 0,
        stagger: 0.1,
        duration: 0.6,
        ease: "back.out(1.2)"
      });
      // Animação dos itens de FAQ
      gsap.to(".faq-item", {
        scrollTrigger: {
          trigger: "#faq-container",
          start: "top 80%",
        },
        opacity: 1,
        y: 0,
        stagger: 0.1,
        duration: 0.6
      });
      // Efeito de hover nos botões de assinatura
      const buttons = document.querySelectorAll('.btn-subscribe');
      buttons.forEach(button => {
        button.addEventListener('mouseenter', () => {
          gsap.to(button, {
            scale: 1.05,
            duration: 0.3,
            ease: "power1.out"
          });
        });
        button.addEventListener('mouseleave', () => {
          gsap.to(button, {
            scale: 1,
            duration: 0.3,
            ease: "power1.out"
          });
        });
      });
      // Efeito de parallax no fundo
      gsap.to("body", {
        backgroundPosition: "50% 100%",
        ease: "none",
        scrollTrigger: {
          trigger: "body",
          start: "top top",
          end: "bottom top",
          scrub: true
        }
      });
    });
  </script>
</body>
</html>

