//Auto Close Timer
function sucesso() {
	$('body').removeClass('timer-alert');
	Swal.fire({
		title: 'Salvo com Sucesso!',
		text: 'Fecharei em 1 segundo.',
		icon: "success",
		timer: 1000,
		customClass: {
            container: 'swal-whatsapp-container'
        }
	})?.then(
		function () {
		},
		// lidando com a rejeição da promessa
		function (dismiss) {
			if (dismiss === 'timer') {
				console.log('Eu estava fechado pelo cronômetro')
			}
		}
	)
}




//Auto Close Timer
function excluido() {
	$('body').removeClass('timer-alert');
	Swal.fire({
		title: 'Excluido com Sucesso!',
		text: 'Fecharei em 1 segundo.',
		icon: "success",
		timer: 1000,
		customClass: {
            container: 'swal-whatsapp-container'
        }
	})?.then(
		function () {
		},
		// lidando com a rejeição da promessa
		function (dismiss) {
			if (dismiss === 'timer') {
				console.log('Eu estava fechado pelo cronômetro')
			}
		}
	)
}



//Auto Close Timer
function alertcobrar() {
	$('body').removeClass('timer-alert');
	Swal.fire({
		title: 'Cobrança Efetuada!',
		text: 'Fecharei em 1 segundo.',
		icon: "success",
		timer: 1000,
		customClass: {
            container: 'swal-whatsapp-container'
        }
	})?.then(
		function () {
		},
		// lidando com a rejeição da promessa
		function (dismiss) {
			if (dismiss === 'timer') {
				console.log('Eu estava fechado pelo cronômetro')
			}
		}
	)
}


function finalizado() {
	$('body').removeClass('timer-alert');
	Swal.fire({
		title: 'Pedido Finalizado com Sucesso!',
		text: 'Redirecionando!!!',
		icon: "success",
		timer: 2000,
		customClass: {
            container: 'swal-whatsapp-container'
        }
	})?.then(
		function () {
		},
		// lidando com a rejeição da promessa
		function (dismiss) {
			if (dismiss === 'timer') {
				console.log('Eu estava fechado pelo cronômetro')
			}
		}
	)
}


function baixado() {
	$('body').removeClass('timer-alert');
	Swal.fire({
		title: 'Baixa Efeturada com Sucesso!',
		text: 'Fecharei em 1 segundo.',
		icon: "success",
		timer: 2000,
		customClass: {
            container: 'swal-whatsapp-container'
        }
	})?.then(
		function () {
		},
		// lidando com a rejeição da promessa
		function (dismiss) {
			if (dismiss === 'timer') {
				console.log('Eu estava fechado pelo cronômetro')
			}
		}
	)
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


function alertWarning(result) {
    Swal.fire({
        title: 'Alerta!',
        text: result,
        icon: "warning",
        confirmButtonText: 'OK',
        customClass: {
            container: 'swal-whatsapp-container'
        }
    })
}



function inserido(){
        $('body').removeClass('timer-alert');
        Swal.fire({
            title: 'Produto Inserido',
            text: '',
            icon: "success",
            timer: 700,
            customClass: {
            container: 'swal-whatsapp-container'
        }
        })?.then(
            function () {
            },
            // lidando com a rejeição da promessa
            function (dismiss) {
                if (dismiss === 'timer') {
                    console.log('')
                }
            }
        )
    }


    
    function alertSucesso(mensagem){
        $('body').removeClass('timer-alert');
        Swal.fire({
            title: mensagem,
            text: 'Fecharei em 1 segundo.',
            icon: "success",
            timer: 1000,
            customClass: {
            container: 'swal-whatsapp-container'
        }
        })?.then(
            function () {
            },
            // lidando com a rejeição da promessa
            function (dismiss) {
                if (dismiss === 'timer') {
                    console.log('Eu estava fechado pelo cronômetro')
                }
            }
        )
    }





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
