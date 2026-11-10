<?php 
$pag = 'home';

if ($mostrar_registros == 'Não') {
   $sql_usuario = " and usuario = '$id_usuario'";
   $sql_usuario_lanc = " and usuario_lanc = '$id_usuario'";
} else {
   $sql_usuario = " ";
   $sql_usuario_lanc = " ";
}

//total empresas
if ($mostrar_registros == 'Não') {
   $query = $pdo->query("SELECT * from empresas where usuario = '$id_usuario'");
} else {
   $query = $pdo->query("SELECT * from empresas");
}
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_empresas = @count($res);

//total empresas mes
$query = $pdo->query("SELECT * from empresas where data_cad >= '$data_inicio_mes' and data_cad <= '$data_final_mes' $sql_usuario");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_empresas_mes = @count($res);

$total_pagar_vencidas = 0;
$query = $pdo->query("SELECT * from pagar_sas where vencimento < curDate() and pago != 'Sim' $sql_usuario_lanc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$contas_pagar_vencidas = @count($res);
for ($i = 0; $i < $contas_pagar_vencidas; $i++) {
   $valor_1 = $res[$i]['valor'];
   $total_pagar_vencidas += $valor_1;
}
$total_pagar_vencidasF = @number_format($total_pagar_vencidas, 2, ',', '.');

$total_receber_vencidas = 0;
$query = $pdo->query("SELECT * from receber_sas where vencimento < curDate() and pago != 'Sim' $sql_usuario_lanc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$contas_receber_vencidas = @count($res);
for ($i = 0; $i < $contas_receber_vencidas; $i++) {
   $valor_2 = $res[$i]['valor'];
   $total_receber_vencidas += $valor_2;
}
$total_receber_vencidasF = @number_format($total_receber_vencidas, 2, ',', '.');

//total recebidas mes
$total_recebidas_mes = 0;
$query = $pdo->query("SELECT * from receber_sas where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' $sql_usuario_lanc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$contas_recebidas_mes = @count($res);
for ($i = 0; $i < $contas_recebidas_mes; $i++) {
   $valor_3 = $res[$i]['valor'];
   $total_recebidas_mes += $valor_3;
}
$total_recebidas_mesF = @number_format($total_recebidas_mes, 2, ',', '.');

//total contas pagas mes
$total_pagas_mes = 0;
$query = $pdo->query("SELECT * from pagar_sas where data_pgto >= '$data_inicio_mes' and data_pgto <= '$data_final_mes' and pago = 'Sim' $sql_usuario_lanc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$contas_pagas_mes = @count($res);
for ($i = 0; $i < $contas_pagas_mes; $i++) {
   $valor_4 = $res[$i]['valor'];
   $total_pagas_mes += $valor_4;
}
$total_pagas_mesF = @number_format($total_pagas_mes, 2, ',', '.');

$total_saldo_mes = $total_recebidas_mes - $total_pagas_mes;
$total_saldo_mesF = @number_format($total_saldo_mes, 2, ',', '.');
if ($total_saldo_mes >= 0) {
   $classe_saldo = 'text-success';
   $classe_saldo2 = 'bg-success';
   $classe_icon_dia = 'fa fa-caret-up text-success';
} else {
   $classe_saldo = 'text-danger';
   $classe_saldo2 = 'bg-danger';
   $classe_icon_dia = 'fa fa-caret-down text-danger';
}

//total recebidas dia
$total_recebidas_dia = 0;
$query = $pdo->query("SELECT * from receber_sas where data_pgto = curDate() and pago = 'Sim' $sql_usuario_lanc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$contas_recebidas_dia = @count($res);
for ($i = 0; $i < $contas_recebidas_dia; $i++) {
   $valor_3 = $res[$i]['valor'];
   $total_recebidas_dia += $valor_3;
}
$total_recebidas_diaF = @number_format($total_recebidas_dia, 2, ',', '.');

//total contas pagas dia
$total_pagas_dia = 0;
$query = $pdo->query("SELECT * from pagar_sas where data_pgto = curDate() and pago = 'Sim' $sql_usuario_lanc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$contas_pagas_dia = @count($res);
for ($i = 0; $i < $contas_pagas_dia; $i++) {
   $valor_4 = $res[$i]['valor'];
   $total_pagas_dia += $valor_4;
}
$total_pagas_diaF = @number_format($total_pagas_dia, 2, ',', '.');

$total_saldo_dia = $total_recebidas_dia - $total_pagas_dia;
$total_saldo_diaF = @number_format($total_saldo_dia, 2, ',', '.');
if ($total_saldo_dia >= 0) {
   $classe_saldo_dia = 'bg-success';
} else {
   $classe_saldo_dia = 'bg-danger';
}

//verificar se ele tem a permissão de estar nessa página
if (@$home == 'ocultar') {
   echo "<script>window.location='index'</script>";
   exit();
}

// Função para gerar cores de fundo para os cards de empresas
function getCardColor($index) {
   $colors = [
       '#edefff', '#ffeeed', '#ffedfc', '#edfbff', '#edffee',
       '#faffed', '#fffaed', '#fcedff', '#fff0ed', '#edfff4'
   ];
   return $colors[$index % count($colors)];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
   
   <!-- GSAP -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
   
   <title>Dashboard</title>
   
   <!-- Adicionar no head, antes do fechamento da tag </head> -->
   <link rel="preconnect" href="https://cdnjs.cloudflare.com">
   <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">

   <link rel="stylesheet" type="text/css" href="css/estilo_home.css">
   
 
</head>
<body>
   <div class="container">
      

       <!-- Espaçador entre o alerta e os cards -->
       <div style="margin-bottom: 2.5rem;"></div>

       <!-- Cards de estatísticas -->
       <div class="section">
           <div class="row">
               <!-- Card de Empresas -->
               <div class="col col-xl-3 col-lg-6 col-md-6 col-sm-12">
                   <a href="clientes" style="text-decoration: none; color: inherit;">
                       <div class="stats-card bg-white">
                           <div class="stats-card-content">
                               <h5 class="stats-card-title text-dark">Total de Empresas</h5>
                               <h2 class="stats-card-value text-primary"><?php echo $total_empresas ?></h2>
                               <p class="stats-card-subtitle text-dark">
                                   Este Mês 
                                   <span class="badge badge-success">
                                       <i class="fa fa-arrow-up"></i> <?php echo $total_empresas_mes ?>
                                   </span>
                               </p>
                               <div class="stats-card-icon icon-primary">
                                   <i class="fe fe-user"></i>
                               </div>
                           </div>
                       </div>
                   </a>
               </div>

               <!-- Card de Despesas Vencidas -->
               <div class="col col-xl-3 col-lg-6 col-md-6 col-sm-12">
                   <a href="pagar" style="text-decoration: none; color: inherit;">
                       <div class="stats-card bg-white">
                           <div class="stats-card-content">
                               <h5 class="stats-card-title text-dark">Despesas Vencidas</h5>
                               <h2 class="stats-card-value text-danger">R$ <?php echo $total_pagar_vencidasF ?></h2>
                               <p class="stats-card-subtitle text-dark">
                                   Contas Vencidas
                                   <span class="badge badge-danger">
                                       <i class="fa fa-arrow-down"></i> <?php echo $contas_pagar_vencidas ?>
                                   </span>
                               </p>
                               <div class="stats-card-icon icon-danger">
                                   <i class="bi bi-currency-dollar"></i>
                               </div>
                           </div>
                       </div>
                   </a>
               </div>

               <!-- Card de Contas a Receber Vencidas -->
               <div class="col col-xl-3 col-lg-6 col-md-6 col-sm-12">
                   <a href="receber" style="text-decoration: none; color: inherit;">
                       <div class="stats-card bg-white">
                           <div class="stats-card-content">
                               <h5 class="stats-card-title text-dark">Receber Vencidas</h5>
                               <h2 class="stats-card-value text-success">R$ <?php echo $total_receber_vencidasF ?></h2>
                               <p class="stats-card-subtitle text-dark">
                                   Contas Vencidas
                                   <span class="badge badge-success">
                                       <i class="fa fa-arrow-up"></i> <?php echo $contas_receber_vencidas ?>
                                   </span>
                               </p>
                               <div class="stats-card-icon icon-success">
                                   <i class="bi bi-currency-dollar"></i>
                               </div>
                           </div>
                       </div>
                   </a>
               </div>

               <!-- Card de Saldo no Mês -->
               <div class="col col-xl-3 col-lg-6 col-md-6 col-sm-12">
                   <a href="receber" style="text-decoration: none; color: inherit;">
                       <div class="stats-card bg-white">
                           <div class="stats-card-content">
                               <h5 class="stats-card-title text-dark">Saldo no Mês</h5>
                               <h2 class="stats-card-value <?php echo $classe_saldo ?>">R$ <?php echo $total_saldo_mesF ?></h2>
                               <p class="stats-card-subtitle text-dark">
                                   Saldo Hoje
                                   <span class="badge <?php echo ($total_saldo_dia >= 0) ? 'badge-success' : 'badge-danger' ?>">
                                       <i class="fa <?php echo ($total_saldo_dia >= 0) ? 'fa-arrow-up' : 'fa-arrow-down' ?>"></i> R$ <?php echo $total_saldo_diaF ?>
                                   </span>
                               </p>
                               <div class="stats-card-icon <?php echo ($total_saldo_mes >= 0) ? 'icon-success' : 'icon-danger' ?>">
                                   <i class="bi bi-currency-dollar"></i>
                               </div>
                           </div>
                       </div>
                   </a>
               </div>
           </div>
       </div>

       <!-- Painel de Informações em formato Kanban -->
       <?php
       $query = $pdo->query("SELECT * from anotacoes where mostrar_home = 'Sim' and empresa = 0 order by id desc");
       $res = $query->fetchAll(PDO::FETCH_ASSOC);
       $linhas = @count($res);
       if ($linhas > 0) {
       ?>
       <div class="kanban-container">
           <div class="kanban-header">
               <h3 class="kanban-title">
                   <i class="fa fa-clipboard-list"></i> PAINEL DE INFORMAÇÕES
               </h3>
               <button class="kanban-toggle" onclick="toggleKanbanView()">
                   <i class="fa fa-th-large"></i> <span id="toggle-text">Visualização em Lista</span>
               </button>
           </div>
           
           <div id="kanban-board" class="kanban-board">
               <?php
               $card_colors = ['primary', 'success', 'warning', 'danger', 'purple'];
               $card_icons = ['info-circle', 'bell', 'exclamation-circle', 'clipboard-check', 'sticky-note'];
               
               for ($i = 0; $i < $linhas; $i++) {
                   $id = $res[$i]['id'];
                   $titulo = $res[$i]['titulo'];
                   $msg = $res[$i]['msg'];
                   $usuario = $res[$i]['usuario'];
                   $data = $res[$i]['data'];

                   $query2 = $pdo->query("SELECT * FROM usuarios where id = '$usuario'");
                   $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
                   if (@count($res2) > 0) {
                       $nome_usuario = $res2[0]['nome'];
                   } else {
                       $nome_usuario = 'Sem Usuário';
                   }

                   $dataF = implode('/', array_reverse(@explode('-', $data)));
                   
                   // Determinar cor e ícone com base no índice
                   $color_index = $i % count($card_colors);
                   $card_color = $card_colors[$color_index];
                   $card_icon = $card_icons[$color_index];
                   
                   // Verificar se o título contém palavras-chave para determinar o ícone
                   if (stripos($titulo, 'aviso') !== false) {
                       $card_icon = 'exclamation-circle';
                   } elseif (stripos($titulo, 'lembrete') !== false) {
                       $card_icon = 'bell';
                   } elseif (stripos($titulo, 'tarefa') !== false) {
                       $card_icon = 'tasks';
                   } elseif (stripos($titulo, 'nota') !== false) {
                       $card_icon = 'sticky-note';
                   }
               ?>
               <div class="kanban-card">
                   <div class="kanban-card-header kanban-card-header-<?php echo $card_color ?>">
                       <h4 class="kanban-card-title">
                           <i class="fa fa-<?php echo $card_icon ?>"></i> <?php echo $titulo ?>
                       </h4>
                       <div class="kanban-card-date"><?php echo $dataF ?></div>
                   </div>
                   <div class="kanban-card-body">
                       <?php echo $msg ?>
                   </div>
                   <div class="kanban-card-footer">
                       <div class="kanban-card-user">
                           <i class="fa fa-user"></i> <?php echo $nome_usuario ?>
                       </div>
                       <div class="kanban-card-id">#<?php echo $id ?></div>
                   </div>
               </div>
               <?php } ?>
           </div>
           
           <div id="scroll-indicator" class="scroll-indicator">
               <i class="fa fa-arrows-alt-h"></i> Deslize para ver mais cards
           </div>
       </div>
       <?php } ?>

       <!-- Grid de Empresas -->
       <div class="section">
           <div class="section-header">
               <h2 class="section-title-large text-primary">Empresas Cadastradas</h2>
              
               <div class="section-counter pulse-animation">
                <input style="background: transparent; border:none; border-bottom: 1px solid #FFF; outline: none; color:#FFF; " type="text" id="buscar_empresas" onkeyup="buscar()"> <i class="fa fa-search me-2" ></i>
                   <div style="margin-left:15px"><i class="fa fa-building me-2"></i> <?php echo $total_empresas ?> empresas</div>
               </div>
           </div>
           
<div class="company-grid" id="listar_empresas">
  
</div>
       </div>
   </div>


<script type="text/javascript">
  var pag = "<?= $pag ?>"
</script>
<script src="js/ajax.js"></script>

   <script type="text/javascript">
     $(document).ready(function () {   
    buscar();
});
   </script>

   <script>

    function buscar() {
      var busca = $('#buscar_empresas').val();
          $.ajax({
            url: 'paginas/' + pag + "/listar.php",
            method: 'POST',
            data: { busca },
            dataType: "html",

            success: function (result) {
                $("#listar_empresas").html(result);
               
            }
        });
       }

       function abrirEmpresa(id) {
           $.ajax({
               url: 'pegar_sessao.php',
               method: 'POST',
               data: {id},
               dataType: "html",
               success: function(result) {
                   window.location = "../painel";
               }
           });
       }

       // Função para alternar a visualização do Kanban
       function toggleKanbanView() {
           const board = document.getElementById('kanban-board');
           const toggleText = document.getElementById('toggle-text');
           const scrollIndicator = document.getElementById('scroll-indicator');
           
           if (board.classList.contains('horizontal')) {
               // Mudar para visualização em grid
               board.classList.remove('horizontal');
               toggleText.textContent = 'Visualização em Lista';
               scrollIndicator.style.display = 'none';
           } else {
               // Mudar para visualização horizontal
               board.classList.add('horizontal');
               toggleText.textContent = 'Visualização em Grid';
               scrollIndicator.style.display = 'block';
           }
       }

       // Inicializar GSAP e animações
       document.addEventListener('DOMContentLoaded', function() {
           // Registrar ScrollTrigger
           gsap.registerPlugin(ScrollTrigger);
           
           // Animar os cards de estatísticas
           const statsCards = document.querySelectorAll('.stats-card');
           gsap.fromTo(statsCards, 
               { opacity: 0, y: 20 },
               { 
                   opacity: 1, 
                   y: 0, 
                   duration: 0.5, 
                   stagger: 0.1,
                   ease: "power2.out"
               }
           );
           
           // Animar os cards do Kanban
           const kanbanCards = document.querySelectorAll('.kanban-card');
           gsap.fromTo(kanbanCards, 
               { opacity: 0, y: 20 },
               { 
                   opacity: 1, 
                   y: 0, 
                   duration: 0.5, 
                   stagger: 0.1,
                   ease: "power2.out",
                   delay: 0.3
               }
           );

           // Animar o título da seção de empresas
           gsap.fromTo('.section-title-large',
               { opacity: 0, x: -20 },
               { 
                   opacity: 1, 
                   x: 0, 
                   duration: 0.5,
                   ease: "power2.out",
                   delay: 0.5
               }
           );
           
           // Animar o contador de empresas
           gsap.fromTo('.section-counter',
               { opacity: 0, x: 20 },
               { 
                   opacity: 1, 
                   x: 0, 
                   duration: 0.5,
                   ease: "power2.out",
                   delay: 0.6
               }
           );

// Animar os cards de empresas
const companyCards = document.querySelectorAll('.company-card');
companyCards.forEach((card, index) => {
    gsap.fromTo(card,
        { opacity: 0, y: 30 },
        { 
            opacity: 1, 
            y: 0, 
            duration: 0.5,
            delay: 0.7 + (index * 0.05),
            ease: "back.out(1.2)"
        }
    );
    
    // Animar o banner e o logo separadamente para um efeito mais interessante
    const banner = card.querySelector('.company-card-banner');
    const logo = card.querySelector('.company-card-logo');
    const title = card.querySelector('.company-card-title');
    
    gsap.fromTo(banner,
        { opacity: 0 },
        { 
            opacity: 1, 
            duration: 0.6,
            delay: 0.8 + (index * 0.05),
            ease: "power2.out"
        }
    );
    
    gsap.fromTo(logo,
        { scale: 0.5, opacity: 0, y: 20 },
        { 
            scale: 1,
            opacity: 1,
            y: 0,
            duration: 0.7,
            delay: 0.9 + (index * 0.05),
            ease: "back.out(1.7)"
        }
    );
    
    gsap.fromTo(title,
        { opacity: 0, y: 10 },
        { 
            opacity: 1,
            y: 0,
            duration: 0.5,
            delay: 1.1 + (index * 0.05),
            ease: "power2.out"
        }
    );
    
    // Animar os stats com um efeito de contagem
    const statValues = card.querySelectorAll('.company-card-stat-value');
    statValues.forEach(stat => {
        const endValue = parseInt(stat.textContent);
        gsap.fromTo(stat,
            { textContent: 0 },
            {
                textContent: endValue,
                duration: 1,
                delay: 1 + (index * 0.05),
                ease: "power1.out",
                snap: { textContent: 1 },
                onUpdate: function() {
                    stat.textContent = Math.round(this.targets()[0].textContent);
                }
            }
        );
    });
});

// Melhorar as animações dos cards de empresas
companyCards.forEach((card, index) => {
    // Adicionar efeito de destaque ao passar o mouse
    card.addEventListener('mouseenter', () => {
        gsap.to(card, {
            backgroundColor: '#ffffff',
            duration: 0.3,
            ease: 'power2.out'
        });
        
        // Animar os stats
        const stats = card.querySelectorAll('.company-card-stat');
        gsap.to(stats, {
            y: -3,
            boxShadow: '0 6px 15px rgba(0, 0, 0, 0.05)',
            stagger: 0.1,
            duration: 0.3
        });
        
        // Animar o botão
        const action = card.querySelector('.company-card-action');
        gsap.to(action, {
            scale: 1.03,
            boxShadow: '0 6px 15px rgba(15, 76, 129, 0.3)',
            duration: 0.3
        });
    });
    
    card.addEventListener('mouseleave', () => {
        // Restaurar o background original
        const originalColor = card.getAttribute('style').split('background-color: ')[1].split(';')[0];
        gsap.to(card, {
            backgroundColor: originalColor,
            duration: 0.3,
            ease: 'power2.out'
        });
        
        // Restaurar os stats
        const stats = card.querySelectorAll('.company-card-stat');
        gsap.to(stats, {
            y: 0,
            boxShadow: '0 4px 10px rgba(0, 0, 0, 0.03)',
            stagger: 0.05,
            duration: 0.3
        });
        
        // Restaurar o botão
        const action = card.querySelector('.company-card-action');
        gsap.to(action, {
            scale: 1,
            boxShadow: '0 4px 10px rgba(15, 76, 129, 0.2)',
            duration: 0.3
        });
    });
});
       });
   </script>


   <script>
    // Adicionar lazy loading para imagens
    document.addEventListener('DOMContentLoaded', function() {
        const images = document.querySelectorAll('.company-card-logo img');
        images.forEach(img => {
            img.loading = 'lazy';
            img.decoding = 'async';
        });
    });

    // Detectar orientação do dispositivo e ajustar a visualização
    window.addEventListener('orientationchange', function() {
        setTimeout(function() {
            // Reajustar o layout após a mudança de orientação
            const kanbanBoard = document.getElementById('kanban-board');
            if (kanbanBoard && kanbanBoard.classList.contains('horizontal')) {
                // Reajustar o scroll para o início
                kanbanBoard.scrollLeft = 0;
            }
            
            // Verificar se estamos em modo paisagem em dispositivo móvel
            if (window.innerWidth < 768 && window.innerWidth > window.innerHeight) {
                // Ajustar o layout para melhor visualização em paisagem
                document.querySelectorAll('.company-grid').forEach(grid => {
                    grid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(230px, 1fr))';
                });
            } else {
                // Restaurar layout padrão
                document.querySelectorAll('.company-grid').forEach(grid => {
                    grid.style.gridTemplateColumns = '';
                });
            }
        }, 300);
    });
    
    // Melhorar a experiência de rolagem em dispositivos touch
    document.addEventListener('DOMContentLoaded', function() {
        const kanbanBoard = document.getElementById('kanban-board');
        if (kanbanBoard) {
            // Adicionar indicadores de rolagem para dispositivos touch
            kanbanBoard.addEventListener('scroll', function() {
                const scrollIndicator = document.getElementById('scroll-indicator');
                if (scrollIndicator) {
                    if (this.scrollLeft + this.clientWidth >= this.scrollWidth - 20) {
                        scrollIndicator.style.opacity = '0';
                    } else {
                        scrollIndicator.style.opacity = '1';
                    }
                }
            });
        }
        
        // Ajustar altura dos cards para melhor visualização
        function equalizeCardHeights() {
            const companyCards = document.querySelectorAll('.company-card');
            let maxHeight = 0;
            
            // Resetar alturas
            companyCards.forEach(card => {
                card.style.height = 'auto';
                const height = card.offsetHeight;
                maxHeight = Math.max(maxHeight, height);
            });
            
            // Aplicar altura máxima a todos os cards
            if (window.innerWidth >= 768) {
                companyCards.forEach(card => {
                    card.style.height = maxHeight + 'px';
                });
            }
        }
        
        // Executar após o carregamento e em redimensionamentos
        equalizeCardHeights();
        window.addEventListener('resize', equalizeCardHeights);
    });

    // Ajustar layout responsivo quando a janela for redimensionada
    window.addEventListener('resize', function() {
        // Ajustar layout responsivo quando a janela for redimensionada
        const width = window.innerWidth;
        
        // Ajustar grid de empresas com base na largura da tela
        const companyGrid = document.querySelector('.company-grid');
        if (companyGrid) {
            if (width < 576) {
                companyGrid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(100%, 1fr))';
            } else if (width < 768) {
                companyGrid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(200px, 1fr))';
            } else if (width < 992) {
                companyGrid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(220px, 1fr))';
            } else if (width < 1200) {
                companyGrid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(250px, 1fr))';
            } else {
                companyGrid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(280px, 1fr))';
            }
        }
        
        // Reajustar alturas dos cards
        equalizeCardHeights();
    });

    // Melhorar a função equalizeCardHeights para ser mais responsiva
    function equalizeCardHeights() {
        const companyCards = document.querySelectorAll('.company-card');
        
        // Resetar alturas
        companyCards.forEach(card => {
            card.style.height = 'auto';
        });
        
        // Em telas maiores, igualar as alturas
        if (window.innerWidth >= 768) {
            let maxHeight = 0;
            
            companyCards.forEach(card => {
                const height = card.offsetHeight;
                maxHeight = Math.max(maxHeight, height);
            });
            
            companyCards.forEach(card => {
                card.style.height = maxHeight + 'px';
            });
        }
    }

    // Detectar quando as imagens são carregadas para recalcular alturas
    window.addEventListener('load', function() {
        equalizeCardHeights();
        
        // Adicionar listener para quando as imagens carregarem
        const images = document.querySelectorAll('.company-card-logo img');
        images.forEach(img => {
            if (img.complete) {
                equalizeCardHeights();
            } else {
                img.addEventListener('load', equalizeCardHeights);
            }
        });
    });

    // Otimizar animações para dispositivos de baixo desempenho
    const isReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (isReducedMotion) {
        // Simplificar animações para dispositivos que preferem movimento reduzido
        document.documentElement.classList.add('reduced-motion');
    }

    // Melhorar experiência de toque para o kanban horizontal
    document.addEventListener('DOMContentLoaded', function() {
        const kanbanBoard = document.getElementById('kanban-board');
        if (kanbanBoard) {
            let startX, scrollLeft, isDragging = false;
            
            const startDragging = function(e) {
                isDragging = true;
                startX = e.pageX || e.touches[0].pageX;
                scrollLeft = kanbanBoard.scrollLeft;
                kanbanBoard.classList.add('grabbing');
            };
            
            const stopDragging = function() {
                isDragging = false;
                kanbanBoard.classList.remove('grabbing');
            };
            
            const move = function(e) {
                if (!isDragging) return;
                e.preventDefault();
                const x = e.pageX || e.touches[0].pageX;
                const walk = (x - startX) * 2; // Velocidade do scroll
                kanbanBoard.scrollLeft = scrollLeft - walk;
            };
            
            // Eventos de mouse
            kanbanBoard.addEventListener('mousedown', startDragging);
            kanbanBoard.addEventListener('mouseleave', stopDragging);
            kanbanBoard.addEventListener('mouseup', stopDragging);
            kanbanBoard.addEventListener('mousemove', move);
            
            // Eventos de toque
            kanbanBoard.addEventListener('touchstart', startDragging);
            kanbanBoard.addEventListener('touchend', stopDragging);
            kanbanBoard.addEventListener('touchcancel', stopDragging);
            kanbanBoard.addEventListener('touchmove', move);
        }
    });
</script>

<footer class="footer">
    <div class="container">
        <p class="text-center text-neutral-500 text-sm py-4"></p>
    </div>
</footer>
</body>
</html>

