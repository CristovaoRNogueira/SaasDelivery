
function toast(mensagem, cor){ 
	if(cor == 'verde'){
		cor = '#24c76a';
	} 

	if(cor == 'vermelha'){
		cor = '#d4483b';
	}

	if(cor == ''){
		cor = '#4a4949';
	}

	Toastify({
		text: mensagem,
		duration: 3000,
		destination: "https://github.com/apvarun/toastify-js",
		newWindow: true,
		close: true,
          gravity: "top", // `top` or `bottom`
          position: "center", // `left`, `center` or `right`
          stopOnFocus: true, // Prevents dismissing of toast on hover
          style: {
            background: cor, //verde #24c76a    vermelha #d4483b
        },
          onClick: function(){} // Callback after click
      }).showToast();
}



$("#form").submit(function () {

	event.preventDefault();
	var formData = new FormData(this);


	$('#btn_salvar').hide();
	$('#img_loader').show();
	$('#btn_carregando').show();

	$.ajax({
		url: '../../painel/paginas/' + pag + "/salvar.php",
		type: 'POST',
		data: formData,

		success: function (mensagem) {
			$('#mensagem').text('');
			$('#mensagem').removeClass()
			if (mensagem.trim() == "Salvo com Sucesso") {
				//sucesso();
				$('#btn-fechar').click();
				location.reload();                      

			} else {

				$('#mensagem').addClass('text-danger')
				toast(mensagem, 'vermelha');
			}


			$('#img_loader').hide();
			$('#btn_salvar').show();
			$('#btn_carregando').hide();

		},

		cache: false,
		contentType: false,
		processData: false,

	});

});



$("#form_status").submit(function () {
		
	event.preventDefault();
	var formData = new FormData(this);


	$('#btn_salvar_status').hide();	

	$.ajax({
		url: '../../painel/paginas/' + pag + "/mudar-status.php",
		type: 'POST',
		data: formData,

		success: function (mensagem) {			
			if (mensagem.trim() == "Salvo com Sucesso") {
				//$('#btn-fechar').click();
				location.reload();                    

			} else {				
				toast(mensagem, 'vermelha');
			}
			
			$('#btn_salvar_status').show();

		},

		cache: false,
		contentType: false,
		processData: false,

	});

});



function excluir_reg(id, nome){
		$('#id_excluir').val(id);    	 
		$('#nome_excluir').text(nome.toUpperCase());    	    
		$('#btn_excluir').click();
	}

function excluir(){  
    var id =  $('#id_excluir').val();    
    $('#mensagem-excluir').text('Excluindo...')
    
    $.ajax({
        url: '../../painel/paginas/' + pag + "/excluir.php",
        method: 'POST',
        data: {id},
        dataType: "html",

        success:function(mensagem){
            if (mensagem.trim() == "Excluído com Sucesso") {                
                location.reload();
            } else {
                toast(mensagem, 'vermelha');
            }
        }
    });
}




function limparFiltro() {
	$('#listar li').show();
}
function filtrar() {
	var termo = $('#buscar').val().toUpperCase();
	$('#listar li').each(function() { 
		if($(this).html().toUpperCase().indexOf(termo) === -1) {
			$(this).hide();
		}
	});
}



function ativar(id, acao){  
    $.ajax({
        url: '../../painel/paginas/' + pag + "/mudar-status.php",
        method: 'POST',
        data: {id, acao},
        dataType: "html",

        success:function(mensagem){
            if (mensagem.trim() == "Alterado com Sucesso") {
                location.reload();
            } else {
                toast(mensagem, 'vermelha');
            }
        }
    });
}






	function arquivo(id, nome){
		$('#id-arquivo').val(id);    
		$('#titulo_arquivo').text(nome);
		$('#btn_arquivos').click();

		$('#arquivo_conta').val('');
		listarArquivos();   
	}		


$("#form_arquivos").submit(function () {

		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: '../../painel/paginas/' + pag + "/arquivos.php",
			type: 'POST',
			data: formData,

			success: function (mensagem) {	
				//alert(mensagem)					
				if (mensagem.trim() == "Inserido com Sucesso") {                    
						//$('#btn-fechar-arquivos').click();
						$('#nome-arq').val('');
						$('#arquivo_conta').val('');
						$('#target-arquivos').attr('src','images/arquivos/sem-foto.png');
						listarArquivos();
					} else {
						toast(mensagem, 'vermelha');
					}

				},

				cache: false,
				contentType: false,
				processData: false,

			});

	});



function listarArquivos(){
		var id = $('#id-arquivo').val();	
		$.ajax({
			url: 'paginas/' + pag + "/listar-arquivos.php",
			method: 'POST',
			data: {id},
			dataType: "text",

			success:function(result){
						//alert(result)
						$("#listar-arquivos").html(result);
					}
				});
	}



function excluir_arq(id, nome){
		$('#id_excluir_arquivo').val(id);    	 
		$('#nome_excluir_arquivo').text(nome.toUpperCase());    	    
		$('#btn_excluir_arquivo').click();
	}


function excluirArquivo(){
    var id = $('#id_excluir_arquivo').val();
    var nome = $('#nome_excluir_arquivo').val();
    $.ajax({
        url: '../../painel/paginas/' + pag + "/excluir-arquivo.php",
        method: 'POST',
        data: {id, nome},
        dataType: "text",

        success: function (mensagem) {          
            if (mensagem.trim() == "Excluído com Sucesso") {                
                listarArquivos(); 
                toast(mensagem, 'verde'); 
                $('#btn_fechar_excluir_arquivos').click();              
            } else {
               toast(mensagem, 'vermelha');
            }


        },      

    });
}

function carregarImgArquivos() {
		var target = document.getElementById('target-arquivos');
		var file = document.querySelector("#arquivo_conta").files[0];

		var arquivo = file['name'];
		resultado = arquivo.split(".", 2);

		if(resultado[1] === 'pdf'){
			$('#target-arquivos').attr('src', "images/pdf.png");
			return;
		}

		if(resultado[1] === 'rar' || resultado[1] === 'zip'){
			$('#target-arquivos').attr('src', "images/rar.png");
			return;
		}

		if(resultado[1] === 'doc' || resultado[1] === 'docx' || resultado[1] === 'txt'){
			$('#target-arquivos').attr('src', "images/word.png");
			return;
		}


		if(resultado[1] === 'xlsx' || resultado[1] === 'xlsm' || resultado[1] === 'xls'){
			$('#target-arquivos').attr('src', "images/excel.png");
			return;
		}


		if(resultado[1] === 'xml'){
			$('#target-arquivos').attr('src', "images/xml.png");
			return;
		}



		var reader = new FileReader();

		reader.onloadend = function () {
			target.src = reader.result;
		};

		if (file) {
			reader.readAsDataURL(file);

		} else {
			target.src = "";
		}
	}




	$("#form_parcelar").submit(function () {				
				event.preventDefault();
				var formData = new FormData(this);

				$.ajax({
					url: '../../painel/paginas/' + pag + "/parcelar.php",
					type: 'POST',
					data: formData,

					success: function (mensagem) {										
						if (mensagem.trim() == "Parcelado com Sucesso") {
							toast(mensagem, 'verde'); 
							location.reload();
							//$('#btn_fechar_parcelar').click();
						} else {
							toast(mensagem, 'vermelha'); 
							
						}

					},

					cache: false,
					contentType: false,
					processData: false,

				});

			});




$("#form_baixar").submit(function () {
				event.preventDefault();
				var formData = new FormData(this);

				$.ajax({
					url: '../../painel/paginas/' + pag + "/baixar.php",
					type: 'POST',
					data: formData,

					success: function (mensagem) {
						//alert(mensagem)
						$('#mensagem-baixar').text('');
						$('#mensagem-baixar').removeClass()
						if (mensagem.trim() == "Baixado com Sucesso") {                    
							toast(mensagem, 'verde'); 
							location.reload();
						} else {
							toast(mensagem, 'vermelha'); 
						}

					},

					cache: false,
					contentType: false,
					processData: false,

				});

			});




$("#form_baixar_cliente").submit(function () {
				event.preventDefault();
				var formData = new FormData(this);

				$.ajax({
					url: '../../painel/paginas/receber/baixar.php',
					type: 'POST',
					data: formData,

					success: function (mensagem) {
						//alert(mensagem)
						$('#mensagem-baixar').text('');
						$('#mensagem-baixar').removeClass()
						if (mensagem.trim() == "Baixado com Sucesso") {                    
							toast(mensagem, 'verde'); 
							$('#btn_fechar_baixar').click();
							var id = $('#id_contas').val();
							
							listarDebitos(id);
						} else {
							toast(mensagem, 'vermelha'); 
						}

					},

					cache: false,
					contentType: false,
					processData: false,

				});

			});