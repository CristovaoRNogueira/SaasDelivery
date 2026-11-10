<?php 
require_once("sistema/conexao.php");
$data_atual = date('Y-m-d');

$id_item = @$_POST['id_item'];

$query5 = $pdo->query("SELECT * FROM planos where id = '$id_item'");
$res5 = $query5->fetchAll(PDO::FETCH_ASSOC);
$nome_plano = $res5[0]['nome'];
$valor_plano = $res5[0]['valor'];
$valorF = number_format($valor_plano, 2, ',', '.');

//buscar as informações
$query = $pdo->query("SELECT * FROM site where empresa = '0'");
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
if($logo == ""){
  $logo = 'sem-foto.png';
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="keywords" content="planos assinatura, erp saas, planos saas, planos sistemas saas, sistema gestão saas" />
  <meta name="description" content="Planos de Assinaturas para nosso Sistema de Gestão SAAS" />
  <meta name="author" content="Hugo Vasconcelos" />

  <title><?php echo $nome_sistema ?> - Assinatura</title>

  <!-- Favicon -->
  <link rel="icon" href="sistema/img/icone.png" type="image/x-icon" />
  
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- GSAP -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  
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
            gray: {
              50: '#f9fafb',
              100: '#f3f4f6',
              200: '#e5e7eb',
              300: '#d1d5db',
              400: '#9ca3af',
              500: '#6b7280',
              600: '#4b5563',
              700: '#374151',
              800: '#1f2937',
              900: '#111827',
            }
          },
          fontFamily: {
            poppins: ['Poppins', 'sans-serif'],
          },
          boxShadow: {
            'inner-light': 'inset 0 2px 4px 0 rgba(255, 255, 255, 0.05)',
            'card': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05), 0 0 0 1px rgba(255, 255, 255, 0.05)',
          }
        }
      }
    }
  </script>
  
  <style type="text/css">
    /* Estilos base */
    body {
      background-image: radial-gradient(circle at 30% 20%, rgba(58, 96, 128, 0.3) 0%, rgba(26, 44, 61, 0.3) 100%), 
                        linear-gradient(135deg, #1a2c3d 0%, #2d4c63 100%);
      background-attachment: fixed;
    }
    
    /* Efeito de vidro */
    .bg-glass {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
    }
    
    /* Estilos para formulários - Ajustado para texto mais escuro */
    .form-input {
      background-color: #ffffff !important;
      color: #000000 !important;
      border: 1px solid #d1d5db !important;
      border-radius: 0.5rem !important;
      padding: 0.75rem 1rem 0.75rem 2.5rem !important;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
      transition: all 0.2s ease !important;
    }
    
    .form-input:focus {
      border-color: #ffc107 !important;
      box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.25) !important;
      outline: none !important;
    }
    
    .form-input::placeholder {
      color: #9ca3af !important;
    }
    
    .form-label {
      display: block !important;
      font-size: 0.875rem !important;
      font-weight: 500 !important;
      color: #f3f4f6 !important;
      margin-bottom: 0.375rem !important;
    }
    
    .form-select {
      background-color: #ffffff !important;
      color: #000000 !important;
      border: 1px solid #d1d5db !important;
      border-radius: 0.5rem !important;
      padding: 0.75rem 1rem 0.75rem 2.5rem !important;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
      transition: all 0.2s ease !important;
    }
    
    .form-select:focus {
      border-color: #ffc107 !important;
      box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.25) !important;
      outline: none !important;
    }
    
    /* Botões */
    .btn-primary {
      @apply bg-accent hover:bg-accent-dark text-gray-900 font-semibold py-3 px-8 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5;
    }
    
    /* Animações */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in {
      animation: fadeIn 0.5s ease-out forwards;
    }
    
    /* Decorações */
    .decoration-dot {
      @apply absolute w-2 h-2 rounded-full bg-accent opacity-70;
    }
    
    /* Efeito de brilho */
    .glow {
      box-shadow: 0 0 15px rgba(255, 193, 7, 0.5);
    }
    
    /* Efeito de gradiente no texto */
    .text-gradient {
      background: linear-gradient(90deg, #ffc107, #ffcd38);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    
    /* Ajuste para ícones nos inputs */
    .input-icon {
      position: absolute !important;
      top: 50% !important;
      left: 0.75rem !important;
      transform: translateY(-50%) !important;
      color: #6b7280 !important;
      pointer-events: none !important;
    }
    
    /* Estilo para o badge de pagamento seguro */
    .secure-payment-badge {
      background-color: rgba(16, 185, 129, 0.1) !important;
      border: 1px solid rgba(16, 185, 129, 0.3) !important;
      border-radius: 0.5rem !important;
      padding: 0.75rem !important;
      display: flex !important;
      align-items: center !important;
      margin-top: 1rem !important;
    }
    
    .secure-payment-badge i {
      color: #10b981 !important;
      margin-right: 0.75rem !important;
    }
    
    /* Estilo para o logo grande */
    .large-logo {
      width: auto !important;
      height: 120px !important;
      filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.3)) !important;
      animation: logoGlow 3s infinite alternate !important;
    }
    
    @keyframes logoGlow {
      from { filter: drop-shadow(0 0 5px rgba(255, 255, 255, 0.3)); }
      to { filter: drop-shadow(0 0 15px rgba(255, 193, 7, 0.5)); }
    }

    /* Estilo para o logo grande no footer */
    .large-logo-footer {
      width: auto !important;
      height: 80px !important;
      filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.2)) !important;
      animation: logoFooterGlow 3s infinite alternate !important;
    }

    @keyframes logoFooterGlow {
      from { filter: drop-shadow(0 0 3px rgba(255, 255, 255, 0.2)); }
      to { filter: drop-shadow(0 0 10px rgba(255, 193, 7, 0.4)); }
    }
  </style>
</head>

<body class="font-poppins text-white min-h-screen">
  <!-- Decorative elements -->
  <div class="decoration-dot" style="top: 10%; left: 5%;"></div>
  <div class="decoration-dot" style="top: 15%; right: 10%;"></div>
  <div class="decoration-dot" style="bottom: 20%; left: 8%;"></div>
  <div class="decoration-dot" style="bottom: 10%; right: 5%;"></div>
  
  <div class="relative py-16 px-4 sm:px-6 lg:px-8 min-h-screen flex flex-col">
    <div class="container mx-auto max-w-6xl">

      <?php if($logo_topo != 'Não'){ ?>
      <!-- Logo Grande -->
      <div class="flex justify-center mb-10 animate-fade-in" style="animation-delay: 0.1s;">
         <img src="sistema/img/logos/<?php echo $logo ?>" alt="Logo" class="large-logo" />
      </div>
    <?php } ?>
      
      <!-- Header Section -->
      <header class="text-center mb-12 animate-fade-in" style="animation-delay: 0.2s;">
        <h1 class="text-4xl md:text-5xl font-bold mb-3 text-gradient">Finalize sua Assinatura</h1>
        <p class="text-lg text-gray-300 max-w-3xl mx-auto">
          Você está prestes a assinar o plano <span class="font-semibold text-accent"><?php echo $nome_plano ?></span>. 
          Complete seu cadastro para continuar.
        </p>
      </header>

      <!-- Main Content -->
      <div class="bg-glass rounded-2xl overflow-hidden shadow-card animate-fade-in" style="animation-delay: 0.3s;">
        <!-- Progress Bar -->
        <div class="bg-primary-dark p-4 border-b border-gray-700">
          <div class="flex items-center justify-center">
            <div class="flex items-center">
              <div class="flex items-center justify-center w-8 h-8 rounded-full bg-accent text-gray-900 font-bold">1</div>
              <div class="ml-2 font-medium">Cadastro</div>
            </div>
            <div class="w-16 h-1 mx-3 bg-gray-700">
              <div class="h-full w-0 bg-accent" id="progress-bar"></div>
            </div>
            <div class="flex items-center">
              <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-700 text-white font-bold">2</div>
              <div class="ml-2 font-medium text-gray-400">Pagamento</div>
            </div>
          </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-0">
          <!-- Formulário de Cadastro -->
          <div class="lg:col-span-2 p-8 lg:border-r border-gray-700">
            <div class="mb-8">
              <h2 class="text-2xl font-semibold mb-2 flex items-center">
                <i class="fas fa-user-plus mr-3 text-accent"></i>
                Informações Pessoais
              </h2>
              <p class="text-gray-400 text-sm">Preencha os campos abaixo com seus dados</p>
            </div>
            
            <form id="form-agenda" method="post" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="nome" class="form-label">
                    Nome Completo <span class="text-accent">*</span>
                  </label>
                  <div class="relative">
                    <div class="input-icon">
                      <i class="fas fa-user"></i>
                    </div>
                    <input type="text" id="nome" name="nome" placeholder="Digite seu nome completo" 
                           required class="form-input w-full">
                  </div>
                </div>
                
                <div>
                  <label for="telefone" class="form-label">Telefone</label>
                  <div class="relative">
                    <div class="input-icon">
                      <i class="fas fa-phone"></i>
                    </div>
                    <input type="text" id="telefone" name="telefone" placeholder="(00) 00000-0000" 
                           class="form-input w-full" onkeyup="verificarTelefone('telefone', this.value)">
                  </div>
                </div>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                  <label for="tipo_pessoa" class="form-label">Tipo de Pessoa</label>
                  <div class="relative">
                    <div class="input-icon">
                      <i class="fas fa-id-card"></i>
                    </div>
                    <select id="tipo_pessoa" name="tipo_pessoa" class="form-select w-full" 
                            onchange="mudarPessoa()">
                      <option value="Física">Física</option>
                      <option value="Jurídica">Jurídica</option>
                    </select>
                  </div>
                </div>
                
                <div>
                  <label for="cpf" class="form-label">CPF / CNPJ</label>
                  <div class="relative">
                    <div class="input-icon">
                      <i class="fas fa-fingerprint"></i>
                    </div>
                    <input type="text" id="cpf" name="cpf" placeholder="CPF" 
                           class="form-input w-full">
                  </div>
                </div>
                
                <div>
                  <label for="email" class="form-label">Email</label>
                  <div class="relative">
                    <div class="input-icon">
                      <i class="fas fa-envelope"></i>
                    </div>
                    <input type="email" id="email" name="email" placeholder="exemplo@email.com" 
                           class="form-input w-full">
                  </div>
                </div>
              </div>
              
              <!-- Campos ocultos -->
              <input type="hidden" name="plano" value="<?php echo $id_item ?>">
              <input type="hidden" name="dias_teste" value="3">
              <input type="hidden" name="mensalidade" value="<?php echo $valorF ?>">
              <input type="hidden" name="assinatura" value="Sim">
              
              <div class="pt-6">
                <button id="btn_agendar" type="submit" class="btn-primary w-full flex items-center justify-center group">
                  <span>CONTINUAR PARA PAGAMENTO</span>
                  <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </button>
                
                <!-- Mensagem de Pagamento Seguro -->
                <div class="secure-payment-badge">
                  <i class="fas fa-shield-alt text-lg"></i>
                  <div>
                    <p class="font-medium text-sm text-white">Pagamento Seguro</p>
                    <p class="text-xs text-gray-300">Seus dados estão protegidos e o pagamento é processado em ambiente seguro.</p>
                  </div>
                </div>
                
                <div id="mensagem" class="text-center mt-3 text-sm"></div>
              </div>
            </form>
          </div>
          
          <!-- Detalhes do Plano -->
          <div class="bg-primary-dark p-8">
            <div class="mb-6">
              <h2 class="text-2xl font-semibold mb-2 flex items-center">
                <i class="fas fa-clipboard-check mr-3 text-accent"></i>
                Resumo do Plano
              </h2>
              <p class="text-gray-400 text-sm">Detalhes do plano selecionado</p>
            </div>
            
            <div class="mb-8 p-6 bg-primary rounded-xl border border-gray-700">
              <div class="text-center mb-6">
                <span class="inline-block px-4 py-1 rounded-full bg-accent bg-opacity-20 text-accent text-sm font-medium mb-3">
                  Plano Selecionado
                </span>
                <h3 class="text-2xl font-bold"><?php echo $nome_plano ?></h3>
              </div>
              
              <div class="flex justify-center items-baseline mb-6">
                <span class="text-lg mr-1">R$</span>
                <span class="text-4xl font-bold"><?php echo $valorF ?></span>
                <span class="text-gray-400 ml-1">/mês</span>
              </div>
              
              <div class="border-t border-gray-700 pt-6">
                <h4 class="font-medium mb-4 text-accent flex items-center">
                  <i class="fas fa-star mr-2"></i>
                  Recursos Incluídos:
                </h4>
                <ul class="space-y-3">
                  <?php 
                  $query2 = $pdo->query("SELECT * FROM planos_itens where plano = '$id_item'");
                  $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
                  $total_reg2 = @count($res2);
                  if($total_reg2 > 0){
                    for($i2=0; $i2 < $total_reg2; $i2++){  
                      $nome_item_carac = @$res2[$i2]['nome'];  
                      if($nome_item_carac == ""){
                        $nome_item_carac = " - ";
                      }
                  ?>
                  <li class="flex items-center">
                    <i class="fas fa-check-circle text-success mr-3"></i> 
                    <span><?php echo $nome_item_carac ?></span>
                  </li>
                  <?php } } ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Footer -->
      <footer class="mt-12 text-center py-6 animate-fade-in" style="animation-delay: 0.5s;">
        <!-- Logo Grande no Footer -->
        <div class="flex justify-center mb-6">
          <img src="sistema/img/logos/<?php echo $logo ?>" alt="Logo" class="large-logo-footer" />
        </div>
        <p class="text-gray-400 text-sm">&copy; <?php echo date('Y'); ?> <?php echo $nome_sistema ?>. Todos os direitos reservados.</p>
      </footer>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <script>
    // Inicialização de máscaras
    $(document).ready(function() {
      $('#cpf').mask('000.000.000-00');
      $('#telefone').mask('(00) 0000-0000');
      
      // Animação da barra de progresso
      gsap.to("#progress-bar", {
        width: "100%",
        duration: 1,
        delay: 0.5,
        ease: "power2.out"
      });
      
      // Animações para elementos decorativos
      gsap.to(".decoration-dot", {
        scale: 1.5,
        opacity: 0.5,
        duration: 2,
        repeat: -1,
        yoyo: true,
        stagger: 0.5,
        ease: "sine.inOut"
      });
      
      // Animação do logo
      gsap.from(".large-logo", {
        y: -20,
        opacity: 0,
        duration: 1.2,
        ease: "elastic.out(1, 0.5)"
      });

      // Animação do logo no rodapé
      gsap.from(".large-logo-footer", {
        scale: 0.8,
        opacity: 0,
        duration: 1,
        scrollTrigger: {
          trigger: "footer",
          start: "top 90%"
        },
        ease: "back.out(1.5)"
      });
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
      document.getElementById(tel).setSelectionRange(100, 100);
    }
    
    // Função para mudar tipo de pessoa
    function mudarPessoa() {
      var pessoa = $('#tipo_pessoa').val();
      if (pessoa == 'Física') {
        $('#cpf').mask('000.000.000-00');
        $('#cpf').attr("placeholder", "Insira CPF");
      } else {
        $('#cpf').mask('00.000.000/0000-00');
        $('#cpf').attr("placeholder", "Insira CNPJ");
      }
    }
    
    // Submissão do formulário
    $("#form-agenda").submit(function () {
      event.preventDefault();
      
      $('#btn_agendar').html('<i class="fas fa-circle-notch fa-spin mr-2"></i> PROCESSANDO...');
      $('#btn_agendar').attr('disabled', true);
      $('#btn_agendar').addClass('opacity-75');
      $('#mensagem').html('<span class="text-accent">Processando sua solicitação...</span>');
      
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
            $('#mensagem').html('<span class="text-red-400">' + msg[0] + '</span>');
            $('#btn_agendar').html('<span>CONTINUAR PARA PAGAMENTO</span> <i class="fas fa-arrow-right ml-2"></i>');
            $('#btn_agendar').attr('disabled', false);
            $('#btn_agendar').removeClass('opacity-75');
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

