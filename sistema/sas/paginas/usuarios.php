<?php
$pag = 'usuarios';

if (@$usuarios == 'ocultar') {
	echo "<script>window.location='index'</script>";
	exit();
}

?>

<div class="breadcrumb-header justify-content-between">
	<div class="left-content mt-2">
		<a class="btn ripple btn-primary text-white" onclick="inserir()" type="button"><i class="fe fe-user-plus me-1"></i>
			Adicionar <?php echo ucfirst($pag); ?></a>
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

<!-- Modal  -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_inserir"></span></h4>
				<button id="btn-fechar" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<form id="form">
				<div class="modal-body">
					<div class="row needs-validation was-validated">
						<div class="col-md-6 mb-2">
							<label>Nome <span class="text-danger" style="font-size: 9px">(Obrigatório)</span></label>
							<input type="text" class="form-control" id="nome" name="nome" placeholder="Digite o Nome" required>
						</div>
						<div class="col-md-6 mb-2 ">
							<label>Email <span class="text-danger" style="font-size: 9px">(Obrigatório)</span></label>
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
								</div>
								<input type="email" class="form-control" name="email" id="email" required
									placeholder="exemplo@gmail.com">
							</div>
						</div>
					</div>
					<div class="row">
					<div class="col-md-3 mb-2 col-6">
							<label>Telefone <span class="text-danger" style="font-size: 9px">(Obrigatório)</span></label>
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
								</div>
								<input type="text" class="form-control" name="telefone" id="telefone"
									onkeyup="verificarTelefone('telefone', this.value)" placeholder="(00) 00000-0000">
							</div>
						</div>
						<div class="col-md-3 mb-2 col-6">
							<label>Cargo</label>
							<select class="form-select" name="nivel" id="nivel">
								<?php
								$query = $pdo->query("SELECT * from cargos where empresa is null or empresa = 0 order by id desc");
								$res = $query->fetchAll(PDO::FETCH_ASSOC);
								$linhas = @count($res);
								if ($linhas > 0) {
									for ($i = 0; $i < $linhas; $i++) { ?>
										<option value="<?php echo $res[$i]['nome'] ?>"><?php echo $res[$i]['nome'] ?></option>
									<?php }
								} ?>
							</select>
						</div>
						<div class="col-md-3 mb-2 ">
							<label>Mostrar todos os Registros</label>
							<select class="form-select" name="mostrar_registros" id="mostrar_registros">
								<option value="Sim">Sim</option>
								<option value="Não">Não</option>
							</select>
						</div>
						<div class="col-md-3 mb-2 ">
							<label>Data Nascimento</label>
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa-regular fa-calendar"></i></span>
								</div>
								<input type="date" name="data_nasc" id="data_nasc" class="form-control"
									value="">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3 mb-2 col-6">
							<label>CPF</label>
							<input type="text" class="form-control" id="cpf" name="cpf" placeholder="CPF">
						</div>
						<div class="col-md-3 mb-2 col-6">
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
						<div class="col-md-6 mb-2 ">
							<label>Chave Pix</label>
							<input type="text" class="form-control" id="pix" name="pix" placeholder="Chave Pix">
						</div>
					</div>
					<div class="row">
						<div class="col-md-2 mb-2">
							<label>CEP</label>
							<input type="text" class="form-control" id="cep" name="cep" placeholder="CEP"
								onblur="pesquisacep(this.value);">
						</div>
						<div class="col-md-5 mb-2">
							<label>Rua</label>
							<input type="text" class="form-control" id="endereco" name="endereco" placeholder="Ex. Rua Central">
						</div>
						<div class="col-md-2 mb-2">
							<label>Número</label>
							<input type="text" class="form-control" id="numero" name="numero" placeholder="1570">
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
						<div class="col-md-5 mb-2">
							<label>Cidade</label>
							<input type="text" class="form-control" id="cidade" name="cidade" placeholder="Cidade">
						</div>
						<div class="col-md-3 mb-2">
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
					<div class="row">
						<div class="col-md-8 mb-3">
							<label>Foto</label>
							<input type="file" class="form-control" id="foto" name="foto" value="" onchange="carregarImg()">
						</div>
						<div class="col-md-4 mb-3">
							<img src="images/sem-foto.jpg" width="80px" id="target">
						</div>
					</div>
					<input type="hidden" class="form-control" id="id" name="id">
					<br>
					<small>
						<div id="mensagem" align="center"></div>
					</small>
				</div>
				<div class="modal-footer">
					<button id="btn_salvar" type="submit" class="btn btn-primary">Salvar<i class="fa fa-check ms-2"></i></button>
					<button class="btn btn-primary" type="button" id="btn_carregando">
						<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Carregando...
					</button>
				</div>
			</form>
		</div>
	</div>
</div>






<!-- Modal Dados -->
<div class="modal fade" id="modalDados" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_dados"></span></h4>
				<button id="btn-fechar-dados" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-8">
						<div class="tile">
							<div class="table-responsive">
								<table id="" class="text-left table table-bordered">
									<tr>
										<td style="width: 30%" class="bg-primary text-white">Telefone</td>
										<td><span id="telefone_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white">Email</td>
										<td><span id="email_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">CPF</td>
										<td><span id="cpf_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Nível</td>
										<td><span id="nivel_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Ativo</td>
										<td><span id="ativo_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Data Cadastro</td>
										<td><span id="data_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Data Nascimento</td>
										<td><span id="data_nasc_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Chave Pix</td>
										<td><span id="pix_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Rua</td>
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
									<tr>
										<td class="bg-primary text-white w_150">CEP</td>
										<td><span id="cep_dados"></span></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="tile">
							<div class="table-responsive">
								<table id="" class="text-left table table-bordered">
									<tr>
										<td align="center"><img src="" id="foto_dados" width="200px"></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>




<!-- Modal Permissoes -->
<div class="modal fade" id="modalPermissoes" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="exampleModalLabel"><span id="nome_permissoes"></span>
					<span style="position:absolute; right:45px">
						<input class="form-check-input" type="checkbox" id="input-todos" onchange="marcarTodos()">
						<label class="">Marcar Todos</label>
					</span>
				</h4>
				<button style="" id="btn-fechar-arquivos" aria-label="Close" class="btn-close" data-bs-dismiss="modal"
					type="button"><span class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div id="listar_permissoes">
				</div>
				<br>
				<input type="hidden" name="id" id="id_permissoes">
				<small>
					<div id="mensagem_permissao" align="center" class="mt-3"></div>
				</small>
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
							<button id="btn_inserir" type="submit" class="btn btn-primary">Inserir <i class="fa-solid fa-check ms-2"></i></button>
							<button class="btn btn-primary" type="button" id="btn_carregando_inserir" style="display: none">
								<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Inserindo...
							</button>
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

		function listarPermissoes(id) {
			$.ajax({
				url: 'paginas/' + pag + "/listar_permissoes.php",
				method: 'POST',
				data: { id },
				dataType: "html",
				success: function (result) {
					$("#listar_permissoes").html(result);
					$('#mensagem_permissao').text('');
				}
			});
		}
		function adicionarPermissao(id, usuario) {
			$.ajax({
				url: 'paginas/' + pag + "/add_permissao.php",
				method: 'POST',
				data: { id, usuario },
				dataType: "html",
				success: function (result) {
					listarPermissoes(usuario);
				}
			});
		}
		function marcarTodos() {
			let checkbox = document.getElementById('input-todos');
			var usuario = $('#id_permissoes').val();
			if (checkbox.checked) {
				adicionarPermissoes(usuario);
			} else {
				limparPermissoes(usuario);
			}
		}
		function adicionarPermissoes(id_usuario) {

			$.ajax({
				url: 'paginas/' + pag + "/add_permissoes.php",
				method: 'POST',
				data: { id_usuario },
				dataType: "html",

				success: function (result) {
					listarPermissoes(id_usuario);
				}
			});
		}


		function limparPermissoes(id_usuario) {

			$.ajax({
				url: 'paginas/' + pag + "/limpar_permissoes.php",
				method: 'POST',
				data: { id_usuario },
				dataType: "html",

				success: function (result) {
					listarPermissoes(id_usuario);
				}
			});
		}
	</script>

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
		function carregarImg() {
			var target = document.getElementById('target');
			var file = document.querySelector("#foto").files[0];
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



<script type="text/javascript">var pag = "<?= $pag ?>"</script>
	<script src="js/ajax.js"></script>
