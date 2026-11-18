<?php 
require_once("sistema/conexao.php");
$data_atual = date('Y-m-d');

$id_item = @$_POST['id_item'];

$query5 = $pdo->query("SELECT * FROM planos where id = '$id_item'");
$res5 = $query5->fetchAll(PDO::FETCH_ASSOC);
$nome_plano = @$res5[0]['nome'];
$valor_plano = @$res5[0]['valor'];
$valorF = number_format((float) $valor_plano, 2, ',', '.');

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
if ($logo == "") {
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
  <link rel="icon" href="sistema/img/<?php echo $icone_sistema ?: 'icone.png' ?>" type="image/x-icon" />

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

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

<body class="font-sans bg-gray-50 text-gray-800 min-h-screen flex flex-col">

  <div class="py-12 px-4 sm:px-6 lg:px-8 flex-grow">
    <div class="max-w-5xl mx-auto">

      <?php if ($logo_topo != 'Não') { ?>
      <div class="flex justify-center mb-8">
        <img src="sistema/img/logos/<?php echo $logo ?>" alt="Logo" class="h-16 w-auto object-contain" />
      </div>
      <?php } ?>

      <!-- Header -->
      <header class="text-center mb-10">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Finalize sua Assinatura</h1>
        <p class="text-base text-gray-600 max-w-2xl mx-auto">
          Você está prestes a assinar o plano <span class="font-semibold text-brand-600"><?php echo $nome_plano ?></span>.
          Complete seu cadastro para continuar.
        </p>
      </header>

      <!-- Layout de 2 Colunas: Esquerda (Resumo do Plano) | Direita (Formulário de Cadastro) -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Coluna 1 (Esquerda): Resumo do Plano -->
        <div class="lg:col-span-5 bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
          <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
              <i class="fas fa-clipboard-check mr-2.5 text-brand-600"></i>
              Resumo do Plano
            </h2>
            <p class="text-gray-500 text-sm mt-1">Detalhes do plano selecionado</p>
          </div>

          <div class="p-5 bg-gray-50 rounded-xl border border-gray-200">
            <div class="text-center mb-5">
              <span class="inline-block px-3 py-1 rounded-full bg-brand-50 text-brand-600 text-xs font-semibold mb-2">
                Plano Selecionado
              </span>
              <h3 class="text-2xl font-bold text-gray-900"><?php echo $nome_plano ?></h3>
            </div>

            <div class="flex justify-center items-baseline mb-6 pb-5 border-b border-gray-200">
              <span class="text-base font-medium text-gray-500 mr-1">R$</span>
              <span class="text-4xl font-extrabold text-gray-900"><?php echo $valorF ?></span>
              <span class="text-gray-500 text-sm ml-1">/mês</span>
            </div>

            <div>
              <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                <i class="fas fa-list-check mr-2 text-brand-600"></i>
                Recursos Incluídos:
              </h4>
              <ul class="space-y-2.5 text-sm text-gray-600">
                <?php 
                $query2 = $pdo->query("SELECT * FROM planos_itens where plano = '$id_item'");
                $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
                $total_reg2 = @count($res2);
                if ($total_reg2 > 0) {
                  for ($i2 = 0; $i2 < $total_reg2; $i2++) {  
                    $nome_item_carac = @$res2[$i2]['nome'];  
                    if ($nome_item_carac == "") {
                      $nome_item_carac = " - ";
                    }
                ?>
                <li class="flex items-center">
                  <i class="fas fa-check-circle text-emerald-600 mr-2.5 shrink-0"></i>
                  <span><?php echo $nome_item_carac ?></span>
                </li>
                <?php } } ?>
              </ul>
            </div>
          </div>
        </div>

        <!-- Coluna 2 (Direita): Formulário de Cadastro -->
        <div class="lg:col-span-7 bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
          <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
              <i class="fas fa-user-plus mr-2.5 text-brand-600"></i>
              Informações Pessoais
            </h2>
            <p class="text-gray-500 text-sm mt-1">Preencha os campos abaixo com seus dados</p>
          </div>

          <form id="form-agenda" method="post" class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label for="nome" class="block text-sm font-medium text-gray-700 mb-1.5">
                  Nome Completo <span class="text-brand-600">*</span>
                </label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                    <i class="fas fa-user"></i>
                  </span>
                  <input type="text" id="nome" name="nome" placeholder="Digite seu nome completo" required
                    class="w-full pl-10 p-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-colors" />
                </div>
              </div>

              <div>
                <label for="telefone" class="block text-sm font-medium text-gray-700 mb-1.5">
                  Telefone
                </label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                    <i class="fas fa-phone"></i>
                  </span>
                  <input type="text" id="telefone" name="telefone" placeholder="(00) 00000-0000"
                    onkeyup="verificarTelefone('telefone', this.value)"
                    class="w-full pl-10 p-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-colors" />
                </div>
              </div>
            </div>

            <div>
              <label for="url_site" class="block text-sm font-medium text-gray-700 mb-1.5">
                Endereço da sua loja (Subdomínio) <span class="text-brand-600">*</span>
              </label>
              <div class="flex items-center gap-2">
                <div class="relative flex-1">
                  <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                    <i class="fas fa-globe"></i>
                  </span>
                  <input type="text" id="url_site" name="url_site" placeholder="minhaloja" required
                    class="w-full pl-10 p-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-colors" />
                </div>
                <span class="text-sm font-medium text-gray-500 select-none shrink-0">.naregiao.com.br</span>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
              <div>
                <label for="tipo_pessoa" class="block text-sm font-medium text-gray-700 mb-1.5">
                  Tipo de Pessoa
                </label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                    <i class="fas fa-id-card"></i>
                  </span>
                  <select id="tipo_pessoa" name="tipo_pessoa" onchange="mudarPessoa()"
                    class="w-full pl-10 p-3 border border-gray-300 rounded-lg bg-white text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-colors">
                    <option value="Física">Física</option>
                    <option value="Jurídica">Jurídica</option>
                  </select>
                </div>
              </div>

              <div>
                <label for="cpf" class="block text-sm font-medium text-gray-700 mb-1.5">
                  CPF / CNPJ
                </label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                    <i class="fas fa-fingerprint"></i>
                  </span>
                  <input type="text" id="cpf" name="cpf" placeholder="CPF"
                    class="w-full pl-10 p-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-colors" />
                </div>
              </div>

              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                  Email
                </label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                    <i class="fas fa-envelope"></i>
                  </span>
                  <input type="email" id="email" name="email" placeholder="exemplo@email.com"
                    class="w-full pl-10 p-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-colors" />
                </div>
              </div>
            </div>

            <!-- Campos ocultos originais -->
            <input type="hidden" name="plano" value="<?php echo $id_item ?>">
            <input type="hidden" name="dias_teste" value="3">
            <input type="hidden" name="mensalidade" value="<?php echo $valorF ?>">
            <input type="hidden" name="assinatura" value="Sim">

            <div class="pt-4">
              <button id="btn_agendar" type="submit"
                class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-3.5 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center shadow-sm">
                <span>CONTINUAR PARA PAGAMENTO</span>
                <i class="fas fa-arrow-right ml-2"></i>
              </button>

              <div id="mensagem" class="text-center mt-3 text-sm"></div>
            </div>
          </form>
        </div>

      </div>

      <!-- Footer -->
      <footer class="mt-12 text-center py-6 border-t border-gray-200">
        <?php if ($logo != 'sem-foto.png') { ?>
        <div class="flex justify-center mb-4">
          <img src="sistema/img/logos/<?php echo $logo ?>" alt="Logo" class="h-12 w-auto object-contain" />
        </div>
        <?php } ?>
        <p class="text-gray-500 text-sm">&copy; <?php echo date('Y'); ?> <?php echo $nome_sistema ?>. Todos os direitos reservados.</p>
      </footer>

    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    $(document).ready(function() {
      $('#cpf').mask('000.000.000-00');
      $('#telefone').mask('(00) 0000-0000');
    });

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

    $("#form-agenda").submit(function () {
      event.preventDefault();

      $('#btn_agendar').html('<i class="fas fa-circle-notch fa-spin mr-2"></i> PROCESSANDO...');
      $('#btn_agendar').attr('disabled', true);
      $('#btn_agendar').addClass('opacity-75');
      $('#mensagem').html('<span class="text-gray-600">Processando sua solicitação...</span>');

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
            $('#mensagem').html('<span class="text-red-600">' + msg[0] + '</span>');
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
