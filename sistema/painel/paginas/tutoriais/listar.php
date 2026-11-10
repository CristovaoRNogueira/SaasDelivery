<?php
$tabela = 'videos';
require_once("../../../conexao.php");

// Consulta para obter os vídeos tutoriais
$query = $pdo->query("SELECT * from $tabela order by ordem asc, id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
?>

<!-- Container principal com classes responsivas -->
<div class="container-fluid px-2 px-md-4">
    <div class="row">
        <div class="col-12">
            <!-- Card para conter a tabela -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-video me-2"></i>Tutoriais em Vídeo
                    </h5>
                </div>
                <div class="card-body px-2 px-md-3">
                    <?php if ($linhas > 0): ?>
                        <!-- Tabela responsiva simplificada -->
                        <div class="table-responsive">
                            <table class="table table-hover border-bottom dt-responsive nowrap w-100" id="tabela">
                                <thead class="table-light"> 
                                    <tr> 
                                        <th>Título do Tutorial</th>
                                    </tr> 
                                </thead> 
                                <tbody>
                                    <?php foreach ($res as $row): ?>
                                        <tr class="tutorial-row" 
                                            style="cursor: pointer;" 
                                            data-link="<?php echo $row['link']; ?>" 
                                            data-title="<?php echo addslashes($row['titulo']); ?>"
                                            onclick="visualizarVideo(this.dataset.link, this.dataset.title)">
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    <div class="tutorial-icon-wrapper me-3">
                                                        <i class="fas fa-play-circle tutorial-icon"></i>
                                                    </div>
                                                    <span class="tutorial-title"><?php echo $row['titulo']; ?></span>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <!-- Mensagem quando não há registros -->
                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>Não há tutoriais cadastrados no momento.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para visualizar vídeo (extremamente simplificado) -->
<div class="modal fade video-modal" id="modalVideo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Conteúdo do vídeo -->
            <div class="video-container">
                <div class="ratio ratio-16x9">
                    <iframe id="videoFrame" src="" allowfullscreen></iframe>
                </div>
                
                <!-- Overlay de carregamento -->
                <div class="video-loading-overlay" id="videoLoadingOverlay">
                    <div class="spinner-border text-light" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos adicionais para responsividade, interatividade e aparência profissional -->
<style>
    /* Estilo para as linhas clicáveis */
    .tutorial-row {
        transition: all 0.2s ease;
    }
    
    .tutorial-row:hover {
        background-color: rgba(13, 110, 253, 0.05);
        transform: translateX(3px);
    }
    
    .tutorial-row:active {
        background-color: rgba(13, 110, 253, 0.1);
    }
    
    /* Estilo para o ícone do tutorial */
    .tutorial-icon-wrapper {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: rgba(13, 110, 253, 0.1);
        transition: all 0.2s ease;
    }
    
    .tutorial-row:hover .tutorial-icon-wrapper {
        background-color: rgba(13, 110, 253, 0.2);
    }
    
    .tutorial-icon {
        font-size: 18px;
        color: #0d6efd;
    }
    
    /* Estilo para o título do tutorial */
    .tutorial-title {
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .tutorial-row:hover .tutorial-title {
        color: #0d6efd;
    }
    
    /* Estilos para o modal de vídeo */
    .video-modal .modal-content {
        border: none;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        background-color: #000;
        padding: 0;
    }
    
    .video-modal .modal-dialog {
        max-width: 800px;
        margin: 1.75rem auto;
    }
    
    .video-container {
        position: relative;
        background-color: #000;
    }
    
    .video-loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 10;
        transition: opacity 0.3s ease;
    }
    
    /* Animação para o modal */
    .modal.fade .modal-dialog {
        transform: scale(0.95);
        opacity: 0;
        transition: transform 0.3s ease, opacity 0.3s ease;
    }
    
    .modal.show .modal-dialog {
        transform: scale(1);
        opacity: 1;
    }
    
    /* Melhorias para DataTables em dispositivos móveis */
    @media (max-width: 767.98px) {
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            text-align: left !important;
            margin-bottom: 0.75rem !important;
            width: 100% !important;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            width: calc(100% - 70px) !important;
            margin-left: 5px !important;
            display: inline-block !important;
        }
        
        .dataTables_wrapper .dataTables_filter label {
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
        }
        
        .dataTables_wrapper .dataTables_filter label::before {
            content: "Buscar: ";
            white-space: nowrap;
        }
        
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            width: 100% !important;
            text-align: center !important;
            margin-top: 0.5rem !important;
        }
        
        .dataTables_wrapper .dataTables_paginate {
            margin-bottom: 0.5rem !important;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.25rem 0.5rem !important;
        }
        
        .video-modal .modal-dialog {
            margin: 0.5rem;
        }
        
        /* Ajuste para o campo de busca */
        div.dataTables_wrapper div.dataTables_filter input {
            width: calc(100% - 60px) !important;
            margin-left: 5px !important;
        }
    }
    
    /* Ajustes para telas muito pequenas */
    @media (max-width: 575.98px) {
        /* Melhorar espaçamento em telas pequenas */
        .card-body {
            padding: 0.75rem;
        }
        
        .tutorial-icon-wrapper {
            width: 32px;
            height: 32px;
        }
        
        .tutorial-icon {
            font-size: 16px;
        }
        
        /* Ajustes específicos para o DataTables em telas muito pequenas */
        .dataTables_wrapper .dataTables_length select {
            width: 80px !important;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.2rem 0.4rem !important;
            font-size: 0.875rem !important;
        }
    }
    
    /* Estilos adicionais para o DataTables */
    .dataTables_wrapper {
        width: 100%;
    }
    
    .dataTables_filter {
        margin-bottom: 1rem;
    }
    
    .dataTables_filter input {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .dataTables_filter input:focus {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>

<script>
    // Inicialização do DataTables com configurações responsivas
    $(document).ready(function() {
        $('#tabela').DataTable({
            "language": {
                "url": '//cdn.datatables.net/plug-ins/1.13.2/i18n/pt-BR.json'
            },
            "ordering": false,
            "stateSave": true,
            "responsive": true,
            "pageLength": 10,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
            // Configuração personalizada para melhor responsividade
            "dom": '<"row mb-3"<"col-12 col-md-6"l><"col-12 col-md-6"f>>rt<"row"<"col-12 col-md-6"i><"col-12 col-md-6"p>>',
            // Ajustar para dispositivos móveis
            "initComplete": function(settings, json) {
                if (window.innerWidth < 768) {
                    this.api().page.len(5).draw();
                    
                    // Ajustes adicionais para o campo de busca em dispositivos móveis
                    $('.dataTables_filter input').attr('placeholder', 'Digite para buscar...');
                    
                    // Remover o texto "Search:" do label
                    $('.dataTables_filter label').contents().filter(function() {
                        return this.nodeType === 3; // Nó de texto
                    }).remove();
                }
            }
        });
        
        // Ajustar DataTables quando a janela for redimensionada
        $(window).resize(function() {
            if (window.innerWidth < 768) {
                $('#tabela').DataTable().page.len(5).draw();
                
                // Ajustes adicionais para o campo de busca em dispositivos móveis
                $('.dataTables_filter input').attr('placeholder', 'Digite para buscar...');
                
                // Remover o texto "Search:" do label
                $('.dataTables_filter label').contents().filter(function() {
                    return this.nodeType === 3; // Nó de texto
                }).remove();
            } else {
                $('#tabela').DataTable().page.len(10).draw();
            }
        });
        
        // Evento para quando o iframe terminar de carregar
        $('#videoFrame').on('load', function() {
            // Esconder o overlay de carregamento
            $('#videoLoadingOverlay').fadeOut(300);
        });
        
        // Ajuste inicial para o campo de busca
        $('.dataTables_filter input').attr('placeholder', 'Digite para buscar...');
        
        // Remover o texto "Search:" do label
        $('.dataTables_filter label').contents().filter(function() {
            return this.nodeType === 3; // Nó de texto
        }).remove();
    });

    // Função para visualizar vídeo
    function visualizarVideo(link, titulo) {
        // Mostrar o overlay de carregamento
        $('#videoLoadingOverlay').show();
        
        // Tenta extrair o ID do vídeo do YouTube
        let videoId = '';
        let embedUrl = link;
        
        if (link.includes('youtube.com') || link.includes('youtu.be')) {
            // Para links do YouTube
            if (link.includes('youtube.com/watch?v=')) {
                videoId = link.split('v=')[1];
                const ampersandPosition = videoId.indexOf('&');
                if (ampersandPosition !== -1) {
                    videoId = videoId.substring(0, ampersandPosition);
                }
                embedUrl = `https://www.youtube.com/embed/${videoId}?rel=0&showinfo=0&autoplay=1`;
            } else if (link.includes('youtu.be/')) {
                videoId = link.split('youtu.be/')[1];
                embedUrl = `https://www.youtube.com/embed/${videoId}?rel=0&showinfo=0&autoplay=1`;
            }
        } else if (link.includes('vimeo.com')) {
            // Para links do Vimeo
            const vimeoId = link.split('vimeo.com/')[1];
            if (vimeoId) {
                embedUrl = `https://player.vimeo.com/video/${vimeoId}?autoplay=1`;
            }
        }
        
        // Definir a URL do iframe
        $('#videoFrame').attr('src', embedUrl);
        
        // Mostrar o modal
        $('#modalVideo').modal('show');
    }

    // Limpar o iframe quando o modal de vídeo for fechado
    $('#modalVideo').on('hidden.bs.modal', function () {
        $('#videoFrame').attr('src', '');
    });
    
    // Detectar orientação do dispositivo e ajustar a visualização
    window.addEventListener('orientationchange', function() {
        setTimeout(function() {
            $('#tabela').DataTable().columns.adjust().responsive.recalc();
            
            // Reajustar o campo de busca após a mudança de orientação
            $('.dataTables_filter input').attr('placeholder', 'Digite para buscar...');
            
            // Remover o texto "Search:" do label
            $('.dataTables_filter label').contents().filter(function() {
                return this.nodeType === 3; // Nó de texto
            }).remove();
        }, 500);
    });
</script>

