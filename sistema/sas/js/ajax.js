$(document).ready(function () {
    $('#btn_carregando').hide();
    $('#listar').text("Carregando Dados...");
    listar();
});

function listar(p1, p2, p3, p4, p5, p6) {
    $.ajax({
        url: 'paginas/' + pag + "/listar.php",
        method: 'POST',
        data: { p1, p2, p3, p4, p5, p6 },
        dataType: "html",

        success: function (result) {
            $("#listar").html(result);
            $('#mensagem-excluir').text('');
        }
    });
}

function inserir() {
    $('#mensagem').text('');
    $('#titulo_inserir').text('Inserir Registro');
    $('#modalForm').modal('show');
    limparCampos();
}





$("#form").submit(function (event) {

    event.preventDefault();
    var formData = new FormData(this);
    $('#btn_salvar').hide();
    $('#btn_carregando').show();
    $.ajax({
        url: 'paginas/' + pag + "/salvar.php",
        type: 'POST',
        data: formData,
        success: function (mensagem) {
            $('#mensagem').text('');
            $('#mensagem').removeClass()
            if (mensagem.trim() == "Salvo com Sucesso") {
                $('#btn-fechar').click();
                sucesso();
                listar();
                $('#mensagem').text('')
            } else {
                $('#mensagem').addClass('text-danger')
                $('#mensagem').text(mensagem)
            }
            $('#btn_salvar').show();
            $('#btn_carregando').hide();
        },
        cache: false,
        contentType: false,
        processData: false,
    });
});




    $("#form-arquivos").submit(function(event) {
        event.preventDefault();
        var formData = new FormData(this);

        $('#btn_inserir').hide();
    $('#btn_carregando_inserir').show();

        $.ajax({
            url: 'paginas/' + pag + "/arquivos.php",
            type: 'POST',
            data: formData,

            success: function(mensagem) {
                $('#mensagem-arquivo').text('');
                $('#mensagem-arquivo').removeClass()
                if (mensagem.trim() == "Inserido com Sucesso") {
                    //$('#btn-fechar-arquivos').click();
                    $('#nome-arq').val('');
                    $('#arquivo_conta').val('');
                    $('#target-arquivos').attr('src', 'images/arquivos/sem-foto.png');
                    listarArquivos();
                } else {
                    $('#mensagem-arquivo').addClass('text-danger')
                    $('#mensagem-arquivo').text(mensagem)
                }
                $('#btn_inserir').show();
                $('#btn_carregando_inserir').hide();
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });


    function listarArquivos() {
        var id = $('#id-arquivo').val();
        $.ajax({
            url: 'paginas/' + pag + "/listar-arquivos.php",
            method: 'POST',
            data: {
                id
            },
            dataType: "text",
            success: function(result) {
                $("#listar-arquivos").html(result);
            }
        });
    }






// ALERT EXCLUIR #######################################
function excluir(id) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success", // Adiciona margem à direita do botão "Sim, Excluir!"
            cancelButton: "btn btn-danger me-1",
            container: 'swal-whatsapp-container'
        },
        buttonsStyling: false
    });

    swalWithBootstrapButtons.fire({
        title: "Deseja Excluir?",
        text: "Você não conseguirá recuperá-lo novamente!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sim, Excluir!",
        cancelButtonText: "Não, Cancelar!",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Realiza a requisição AJAX para excluir o item
            $.ajax({
                url: 'paginas/' + pag + "/excluir.php",
                method: 'POST',
                data: { id },
                dataType: "html",
                success: function (mensagem) {
                    if (mensagem.trim() == "Excluído com Sucesso") {
                        // Exibe mensagem de sucesso após a exclusão
                        swalWithBootstrapButtons.fire({
                            title: mensagem,
                            text: 'Fecharei em 1 segundo.',
                            icon: "success",
                            timer: 1000,
                            timerProgressBar: true,
                            confirmButtonText: 'OK',
                            customClass: {
                             container: 'swal-whatsapp-container'
                             }
                        });
                        listar();
                        limparCampos()
                    } else {
                        // Exibe mensagem de erro se a requisição falhar
                        swalWithBootstrapButtons.fire({
                            title: "Opss!",
                            text: mensagem,
                            icon: "error",
                            confirmButtonText: 'OK',
                            customClass: {
                             container: 'swal-whatsapp-container'
                             }
                        });
                    }
                }
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            swalWithBootstrapButtons.fire({
                title: "Cancelado",
                text: "Fecharei em 1 segundo.",
                icon: "error",
                timer: 1000,
                timerProgressBar: true,
            });
        }
    });
}


function excluirMultiplos(id) {
    //$('#mensagem-excluir').text('Excluindo...')

    $.ajax({
        url: 'paginas/' + pag + "/excluir.php",
        method: 'POST',
        data: { id },
        dataType: "html",

        success: function (mensagem) {
            if (mensagem.trim() == "Excluído com Sucesso") {
                listar();
                limparCampos()
            } else {
                $('#mensagem-excluir').addClass('text-danger')
                $('#mensagem-excluir').text(mensagem)
            }
        }
    });
}



function ativar(id, acao) {
    $.ajax({
        url: 'paginas/' + pag + "/mudar-status.php",
        method: 'POST',
        data: { id, acao },
        dataType: "html",

        success: function (mensagem) {
            if (mensagem.trim() == "Alterado com Sucesso") {
                listar();
            } else {
                $('#mensagem-excluir').addClass('text-danger')
                $('#mensagem-excluir').text(mensagem)
            }
        }
    });
}



//############### ALERTAS #########################################

// ALERT SALVAR FINAL ##############
function alertsucesso(mensagem) {
    $('body').removeClass('timer-alert');
    Swal.fire({
        title: mensagem,
        text: 'Fecharei em 1 segundo.',
        icon: "success",
        timer: 1000,
        timerProgressBar: true,
        confirmButtonText: 'OK',
         customClass: {
            container: 'swal-whatsapp-container'
        }
    })
}

function alertCobrar(mensagem) {
    $('body').removeClass('timer-alert');
    Swal.fire({
        title: mensagem,
        icon: "success",
        confirmButtonText: 'OK',
         customClass: {
            container: 'swal-whatsapp-container'
        }
    })
}

//ALERTA MENSAGEM EXCLUIDO ###################
function alertexcluido(mensagem) {
    $('body').removeClass('timer-alert');
    Swal.fire({
        title: mensagem,
        text: 'Fecharei em 1 segundo.',
        icon: "success",
        timer: 1000,
        timerProgressBar: true,
        confirmButtonText: 'OK',
         customClass: {
            container: 'swal-whatsapp-container'
        }
    })
}



// ALERT ERRO ##############
function alertErro(mensagem) {
    $('body').removeClass('timer-alert');
    Swal.fire({
        title: 'Opss...',
        text: mensagem,
        icon: "error",
        confirmButtonText: 'OK',
        customClass: {
            container: 'swal-whatsapp-container'
        },
        // Adicione a opção `target` se necessário
        target: document.body // ou um seletor específico se necessário
    });
}


//############### MÁSCRAS MOEDAS #########################################
function mascaraMoeda(elemento) {
    let value = elemento.value;
    // Remove todos os caracteres que não são números
    value = value.replace(/\D/g, '');
    // Converte o valor para um número decimal com duas casas
    value = (value / 100).toFixed(2);
    // Substitui ponto por vírgula
    value = value.replace('.', ',');
    // Adiciona separadores de milhar
    value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    // Atualiza o valor do input sem o prefixo "R$"
    elemento.value = value;
}

// FUNÇÃO PARA FORMATAR VALOR PARA REAL
function formatarParaReal(valor) {
    return valor.toLocaleString({
        style: 'currency',
        currency: 'BRL'
    });
}


//FUNÇÃO PARA MOEDA
function formatarMoeda(input) {
    // Remove caracteres que não são dígitos
    let valor = input.value.replace(/\D/g, '');

    // Formata como moeda
    valor = (valor / 100).toFixed(2).replace('.', ',');

    // Adiciona o prefixo de moeda
    input.value = valor;
}




// FUNÇÃO PARA VERIFICAR TELEFONE
function verificarTelefone(tel, valor) {
    // pega só os números (remove ()- )
    var numeros = valor.replace(/\D/g, '');

    if (numeros.length === 11) {
        $('#' + tel).mask('(00) 00000-0000'); // celular
    } else {
        $('#' + tel).mask('(00) 0000-00009'); // fixo (9 no fim é opcional)
    }

    // força o cursor pro final
    var input = document.getElementById(tel);
    setTimeout(() => {
        input.setSelectionRange(input.value.length, input.value.length);
    }, 0);
}

// Ajuste para quando COLAR (sem precisar mexer no HTML)
$(document).on('paste input', 'input[id^="telefone"], input[name^="telefone"]', function () {
    setTimeout(() => verificarTelefone(this.id, this.value), 0);
});


// ALERT SALVAR FINAL ##############
function alertInformativo(mensagem) {
    $('body').removeClass('timer-alert');
    Swal.fire({
        title: mensagem,
        text: 'Fecharei em 3 segundo.',
        icon: "error",
        timer: 3000,
        timerProgressBar: true,
        confirmButtonText: 'OK',
         customClass: {
            container: 'swal-whatsapp-container'
        }
    })
}


function alertWarning(mensagem) {
    $('body').removeClass('timer-alert');
    Swal.fire({
        title: 'Informação',
        text: mensagem,
        icon: "warning",
        confirmButtonText: 'OK',
         customClass: {
            container: 'swal-whatsapp-container'
        }
    })
}