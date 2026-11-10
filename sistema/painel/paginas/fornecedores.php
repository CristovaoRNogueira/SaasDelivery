<?php
$pag = 'fornecedores';

//verificar se ele tem a permissão de estar nessa página
if (@$fornecedores == 'ocultar') {
	echo "<script>window.location='index.php'</script>";
	exit();
}
?>
<div class="breadcrumb-header justify-content-between">
	<div class="left-content mt-2">
		<a class="btn ripple btn-primary text-white" onclick="inserir()" type="button"><i class="fe fe-user-plus me-1"></i>
			Adicionar <?php echo ucfirst($pag); ?></a>



		<!-- BOTÃO EXCLUIR SELEÇÃO -->
	<big><a class="btn btn-danger" href="#" onclick="deletarSel()" title="Excluir" id="btn-deletar"
				style="display:none"><i class="fe fe-trash-2"></i> Deletar</a></big>
	</div>

</div>

<div class="row row-sm">
	<div class="col-lg-12">
		<div class="card custom-card">
			<div class="card-body" id="listar">

			</div>
		</div>
	</div>
</div>


<input type="hidden" id="ids">

<!-- Modal Principal -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_inserir"></span></h4>
				<button id="btn-fechar" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<form id="form">
				<div class="modal-body">


					<div class="row">
						<div class="col-md-6 mb-2 needs-validation was-validated">
							<label>Nome <span class="text-danger" style="font-size: 9px">(Obrigatório)</span></label>
							<input type="text" class="form-control" id="nome" name="nome" placeholder="Digite o Nome" required>
						</div>

						<div class="col-md-6">
							<label>Email</label>
							<input type="email" class="form-control" id="email" name="email" placeholder="Digite o Email">
						</div>


					</div>


					<div class="row">

						<div class="col-md-3 mb-2 col-6 needs-validation was-validated">
							<label>Telefone <span class="text-danger" style="font-size: 9px">(Obrigatório)</span></label>
							<input type="text" class="form-control" id="telefone" name="telefone" placeholder="Telefone" required>
						</div>

						<div class="col-md-4 mb-2 col-6">
							<label>CNPJ</label>
							<input type="text" class="form-control" id="cnpj" name="cnpj" placeholder="CNPJ">
						</div>

						<div class="col-md-1 col-1" style="margin-top: 28px; margin-left: -10px">
							<a title="Buscar CNPJ" class="btn btn-primary" href="#" onclick="buscarCNPJ()" class="btn btn-primary"> <i
									class="bi bi-search"></i> </a>
						</div>


						<div class="col-md-4 mb-2">
							<label>Tipo de Chave</label>
							<select class="form-select" id="tipo_chave" name="tipo_chave">
								<option value="">Selecionar Chave</option>
								<option value="Telefone">Telefone</option>
								<option value="CPF">CPF</option>
								<option value="Aleatória">Aleatória</option>
								<option value="CNPJ">CNPJ</option>
								<option value="E-mail">E-mail</option>
							</select>
						</div>





					</div>

					<div class="row">
						<div class="col-md-9 col-6">
							<label>Pix</label>
							<input type="text" class="form-control" id="pix" name="pix" placeholder="Chavew Pix">
						</div>

						<div class="col-md-3 mb-2">
							<label>CEP</label>
							<input type="text" class="form-control" id="cep" name="cep" placeholder="CEP"
								onblur="pesquisacep(this.value);">
						</div>

					</div>

					<div class="row">



						<div class="col-md-7 mb-2">
							<label>Rua</label>
							<input type="text" class="form-control" id="endereco" name="endereco" placeholder="Ex. Rua Central">
						</div>

						<div class="col-md-2 mb-2">
							<label>Número</label>
							<input type="text" class="form-control" id="numero" name="numero" placeholder="1580">
						</div>
						<div class="col-md-3 mb-2">
							<label>Complemento</label>
							<input type="text" class="form-control" id="complemento" name="complemento" placeholder="Bloco B AP 104">
						</div>


					</div>


					<div class="row">

						<div class="col-md-4 mb-2">
							<label>Bairro</label>
							<input type="text" class="form-control" id="bairro" name="bairro" placeholder="Bairro">
						</div>

						<div class="col-md-4 mb-2">
							<label>Cidade</label>
							<input type="text" class="form-control" id="cidade" name="cidade" placeholder="Cidade">
						</div>

						<div class="col-md-4 mb-2">
							<label>Estado</label>
							<select class="form-select" id="estado" name="estado">
								<option value="">Selecionar</option>
								<option value="AC">Acre</option>
								<option value="AL">Alagoas</option>
								<option value="AP">Amapá</option>
								<option value="AM">Amazonas</option>
								<option value="BA">Bahia</option>
								<option value="CE">Ceará</option>
								<option value="DF">Distrito Federal</option>
								<option value="ES">Espírito Santo</option>
								<option value="GO">Goiás</option>
								<option value="MA">Maranhão</option>
								<option value="MT">Mato Grosso</option>
								<option value="MS">Mato Grosso do Sul</option>
								<option value="MG">Minas Gerais</option>
								<option value="PA">Pará</option>
								<option value="PB">Paraíba</option>
								<option value="PR">Paraná</option>
								<option value="PE">Pernambuco</option>
								<option value="PI">Piauí</option>
								<option value="RJ">Rio de Janeiro</option>
								<option value="RN">Rio Grande do Norte</option>
								<option value="RS">Rio Grande do Sul</option>
								<option value="RO">Rondônia</option>
								<option value="RR">Roraima</option>
								<option value="SC">Santa Catarina</option>
								<option value="SP">São Paulo</option>
								<option value="SE">Sergipe</option>
								<option value="TO">Tocantins</option>
								<option value="EX">Estrangeiro</option>
							</select>
						</div>


					</div>


					<input type="hidden" class="form-control" id="id" name="id">

					<br>
					<small>
						<div id="mensagem" align="center"></div>
					</small>
				</div>
				<div class="modal-footer">
					<button type="submit" id="btn_salvar" class="btn btn-primary">Salvar<i
							class="fa-solid fa-check ms-2"></i>
						</button>

						<button class="btn btn-primary" type="button" id="btn_carregando" style="display: none;">
						<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...
					</button>
				</div>
			</form>
		</div>
	</div>
</div>





<!-- Modal Dados -->
<div class="modal fade" id="modalDados" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_dados"></span></h4>
				<button id="btn-fechar-dados" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>

			<div class="modal-body">


				<div class="col-md-12">
					<div class="tile">
						<div class="table-responsive">
							<table id="" class="text-left table table-bordered">

								<tr>
									<td class="bg-primary text-white">Telefone</td>
									<td><span id="telefone_dados"></span></td>
								</tr>

								<tr>
									<td class="bg-primary text-white">Email</td>
									<td><span id="email_dados"></span></td>
								</tr>

								<tr>
									<td class="bg-primary text-white w_150">Chave Pix</td>
									<td><span id="pix_dados"></span></td>
								</tr>

								<tr>
									<td class="bg-primary text-white w_150">Data Cadastro</td>
									<td><span id="data_dados"></span></td>
								</tr>
								<tr>
									<td class="bg-primary text-white w_150">CNPJ</td>
									<td><span id="cnpj_dados"></span></td>
								</tr>
								<tr>
									<td class="bg-primary text-white w_150">CEP</td>
									<td><span id="cep_dados"></span></td>
								</tr>
								<tr>
									<td class="bg-primary text-white w_150">Endereço</td>
									<td><span id="endereco_dados"></span></td>
								</tr>

								<tr>
									<td class="bg-primary text-white w_150">Número</td>
									<td><span id="numero_dados"></span></td>
								</tr>
								<tr>
									<td class="bg-primary text-white w_150">Complemento</td>
									<td><span id="complemento_dados"></span></td>
								</tr>


								<tr>
									<td class="bg-primary text-white w_150">Bairro</td>
									<td><span id="bairro_dados"></span></td>
								</tr>

								<tr>
									<td class="bg-primary text-white w_150">Cidade</td>
									<td><span id="cidade_dados"></span></td>
								</tr>

								<tr>
									<td class="bg-primary text-white w_150">Estado</td>
									<td><span id="estado_dados"></span></td>
								</tr>




							</table>
						</div>
					</div>
				</div>


			</div>

		</div>
	</div>
</div>


<!-- Modal Arquivos -->
<div class="modal fade" id="modalArquivos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="tituloModal">Gestão de Arquivos - <span id="nome-arquivo"> </span></h4>
				<button id="btn-fechar-arquivos" aria-label="Close" class="btn-close" data-bs-dismiss="modal"
					type="button"><span class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<form id="form-arquivos" method="post">
				<div class="modal-body">

					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								<label>Arquivo</label>
								<input class="form-control" type="file" name="arquivo_conta" onChange="carregarImgArquivos();"
									id="arquivo_conta">
							</div>
						</div>
						<div class="col-md-4">
							<div id="divImgArquivos">
								<img src="images/arquivos/sem-foto.png" width="60px" id="target-arquivos">
							</div>
						</div>




					</div>

					<div class="row">
						<div class="col-md-8">
							<input type="text" class="form-control" name="nome-arq" id="nome-arq" placeholder="Nome do Arquivo * "
								required>
						</div>

						<div class="col-md-4">
							<button type="submit" class="btn btn-primary">Inserir</button>
						</div>
					</div>

					<hr>

					<small>
						<div id="listar-arquivos"></div>
					</small>

					<br>
					<small>
						<div align="center" id="mensagem-arquivo"></div>
					</small>

					<input type="hidden" class="form-control" name="id-arquivo" id="id-arquivo">


				</div>
			</form>
		</div>
	</div>

</div>


	<script type="text/javascript">
		function carregarImgArquivos() {
			var target = document.getElementById('target-arquivos');
			var file = document.querySelector("#arquivo_conta").files[0];

			var arquivo = file['name'];
			resultado = arquivo.split(".", 2);

			if (resultado[1] === 'pdf') {
				$('#target-arquivos').attr('src', "images/pdf.png");
				return;
			}

			if (resultado[1] === 'rar' || resultado[1] === 'zip') {
				$('#target-arquivos').attr('src', "images/rar.png");
				return;
			}

			if (resultado[1] === 'doc' || resultado[1] === 'docx' || resultado[1] === 'txt') {
				$('#target-arquivos').attr('src', "images/word.png");
				return;
			}


			if (resultado[1] === 'xlsx' || resultado[1] === 'xlsm' || resultado[1] === 'xls') {
				$('#target-arquivos').attr('src', "images/excel.png");
				return;
			}


			if (resultado[1] === 'xml') {
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
	</script>




	<script type="text/javascript">
		$("#form-arquivos").submit(function () {
			event.preventDefault();
			var formData = new FormData(this);

			$.ajax({
				url: 'paginas/' + pag + "/arquivos.php",
				type: 'POST',
				data: formData,

				success: function (mensagem) {
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

				},

				cache: false,
				contentType: false,
				processData: false,

			});

		});
	</script>

	<script type="text/javascript">
		function listarArquivos() {
			var id = $('#id-arquivo').val();
			$.ajax({
				url: 'paginas/' + pag + "/listar-arquivos.php",
				method: 'POST',
				data: { id },
				dataType: "text",

				success: function (result) {
					$("#listar-arquivos").html(result);
				}
			});
		}

	</script>




	<script>
		function buscarCNPJ() {

			var cnpj = $('#cnpj').val().replace(/\D/g, ''); // Remover tudo que não for número
			if (cnpj.length === 14) { // Verifica se o CNPJ tem 14 dígitos
				$.ajax({
					url: 'https://www.receitaws.com.br/v1/cnpj/' + cnpj,
					type: 'GET',
					dataType: 'jsonp', // A API retorna um JSONP para evitar CORS
					success: function (dados) {
						if (dados.status === "ERROR") {
							alert("CNPJ inválido ou não encontrado!");
						} else {
							$('#nome').val(dados.nome);
							//$('#atividade_principal').html("Atividade Principal: " + dados.atividade_principal[0].text);
							$('#cep').val(dados.cep);
							$('#telefone').val(dados.telefone);
							$('#email').val(dados.email);
							$('#endereco').val(dados.logradouro);
							$('#bairro').val(dados.bairro);
							$('#numero').val(dados.numero);
							$('#cidade').val(dados.municipio);
							$('#complemento').val(dados.complemento);
							$('#estado').val(dados.uf);
						}
					},
					error: function () {
						alert("Erro ao buscar os dados do CNPJ.");
					}
				});
			} else {
				alert("Por favor, insira um CNPJ válido com 14 dígitos.");
			}
		}
	</script>

	<script type="text/javascript">var pag = "<?= $pag ?>"</script>
	<script src="js/ajax.js"></script>




<script>
	function limpa_formulário_cep() {
		//Limpa valores do formulário de cep.
		document.getElementById('endereco').value = ("");
		document.getElementById('bairro').value = ("");
		document.getElementById('cidade').value = ("");
		document.getElementById('estado').value = ("");
		//document.getElementById('ibge').value=("");
	}

	function meu_callback(conteudo) {
		if (!("erro" in conteudo)) {
			//Atualiza os campos com os valores.
			document.getElementById('endereco').value = (conteudo.logradouro);
			document.getElementById('bairro').value = (conteudo.bairro);
			document.getElementById('cidade').value = (conteudo.localidade);
			document.getElementById('estado').value = (conteudo.uf);
			//document.getElementById('ibge').value=(conteudo.ibge);
		} //end if.
		else {
			//CEP não Encontrado.
			limpa_formulário_cep();
			alert("CEP não encontrado.");
		}
	}

	function pesquisacep(valor) {

		//Nova variável "cep" somente com dígitos.
		var cep = valor.replace(/\D/g, '');

		//Verifica se campo cep possui valor informado.
		if (cep != "") {

			//Expressão regular para validar o CEP.
			var validacep = /^[0-9]{8}$/;

			//Valida o formato do CEP.
			if (validacep.test(cep)) {

				//Preenche os campos com "..." enquanto consulta webservice.
				document.getElementById('endereco').value = "...";
				document.getElementById('bairro').value = "...";
				document.getElementById('cidade').value = "...";
				document.getElementById('estado').value = "...";
				//document.getElementById('ibge').value="...";

				//Cria um elemento javascript.
				var script = document.createElement('script');

				//Sincroniza com o callback.
				script.src = 'https://viacep.com.br/ws/' + cep + '/json/?callback=meu_callback';

				//Insere script no documento e carrega o conteúdo.
				document.body.appendChild(script);

			} //end if.
			else {
				//cep é inválido.
				limpa_formulário_cep();
				alert("Formato de CEP inválido.");
			}
		} //end if.
		else {
			//cep sem valor, limpa formulário.
			limpa_formulário_cep();
		}
	};
</script>