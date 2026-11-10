<?php
$pag = 'clientes';

//verificar se ele tem a permissão de estar nessa página
if (@$clientes == 'ocultar') {
	echo "<script>window.location='index'</script>";
	exit();
}
?>



<div class="breadcrumb-header justify-content-between">
	<div class="left-content mt-2">
		<a class="btn ripple btn-primary text-white" onclick="inserir()" type="button"><i class="fe fe-user-plus me-1"></i>
			Adicionar Empresa</a>
		<a style="position:absolute; right:40px;" href="rel/excel_clientes.php" type="button"
			class="btn btn-success ocultar_mobile_app" target="_blank"><span class="fa fa-file-excel-o"></span> Exportar</a>
		<a class="btn btn-danger" href="#" onclick="deletarSel()" title="Excluir" id="btn-deletar"
			style="display:none"><i class="fe fe-trash-2"></i> Deletar</a>
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

<!-- Modal Cliente -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_inserir"></span></h4>
				<button id="btn-fechar" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<form id="form">
				<div class="modal-body ">
					<div class="row">
						<div class="col-md-6 mb-2  needs-validation was-validated">
							<label>Nome <span class="text-danger" style="font-size: 9px">(Obrigatório)</span></label>
							<div class="form-group has-success mg-b-0">
								<input class="form-control" id="nome" name="nome" placeholder="Digite o Nome" required type="text"
									value="This is input">
							</div>
						</div>
						<div class="col-md-3 mb-2 ">
							<label>Telefone</label>
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
								</div>
								<input type="text" class="form-control" name="telefone" id="telefone"
									onkeyup="verificarTelefone('telefone', this.value);"  placeholder="(00) 00000-0000">
							</div>
						</div>
						<div class="col-md-3 mb-2 ">
							<label>Data Abertura / Nascimento</label>
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa-regular fa-calendar"></i></span>
								</div>
								<input type="text" name="data_nasc" id="data_nasc" class="form-control"
									value="">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-2 mb-2 ">
							<label>Pessoa</label>
							<select name="tipo_pessoa" id="tipo_pessoa" class="form-select" onchange="mudarPessoa()">
								<option value="Jurídica">Jurídica</option>
								<option value="Física">Física</option>
								
							</select>
						</div>
						<div class="col-md-2 mb-2 col-10">
							<label>CPF / CNPJ</label>
							<input type="text" class="form-control" id="cpf" name="cpf" placeholder="CPF">
						</div>
						<div class="col-md-1 col-2" style="margin-top: 28px; margin-left: -10px">
							<a title="Buscar CNPJ" class="btn btn-primary" href="#" onclick="buscarCNPJ()" class="btn btn-primary"> <i
									class="bi bi-search"></i> </a>
						</div>
						<div class="col-md-3 mb-2  needs-validation was-validated">
							<label>Email</label>
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
								</div>
								<input type="email" class="form-control" name="email" id="email"
									placeholder="exemplo@gmail.com">
							</div>
						</div>
						<div class="col-md-4 mb-2  needs-validation was-validated">
							<label>Url Site</label>
							<div class="input-group mb-3">								
								<div style="display: flex; align-items: center;">
								    <input type="text" value="<?php echo $url_sistema ?>" readonly
								        style="background: #eee; border: 1px solid #ccc; padding: 6px; width: 230px;">
								    <input type="text" class="form-control" name="url_site" id="url_site"
								        placeholder="empresa1, loja1, etc"
								        style="flex: 1; border-left: none;">
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-2 mb-2">
							<label>CEP</label>
							<input type="text" class="form-control" id="cep" name="cep" placeholder="Digite o CEP"
								onblur="pesquisacep(this.value);">
						</div>
						<div class="col-md-3 mb-2">
							<label>Rua</label>
							<input type="text" class="form-control" id="endereco" name="endereco" placeholder="Ex. Rua Central">
						</div>
						<div class="col-md-1 mb-2">
							<label>Número</label>
							<input type="text" class="form-control" id="numero" name="numero" placeholder="1500">
						</div>
						<div class="col-md-2 mb-2">
							<label>Complemento</label>
							<input type="text" class="form-control" id="complemento" name="complemento" placeholder="Bloco B AP 104">
						</div>
						<div class="col-md-2 mb-2">
							<label>Bairro</label>
							<input type="text" class="form-control" id="bairro" name="bairro" placeholder="Bairro">
						</div>

						<div class="col-md-2 mb-2">
							<label>Cidade</label>
							<input type="text" class="form-control" id="cidade" name="cidade" placeholder="Cidade">
						</div>
					</div>
					<div class="row">
						
						<div class="col-md-2 mb-2">
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

						<div class="col-md-3 mb-2">
							<label>Dias Testes</label>
							<input type="number" class="form-control" id="dias_teste" name="dias_teste" placeholder="(Vazio se Não For Teste)">
						</div>


						<div class="col-md-2 mb-2">
							<label>Dispositivos</label>
							<input type="number" value="1" class="form-control" id="dispositivos" name="dispositivos" placeholder="Whatsapp">
						</div>


						<div class="col-md-2 mb-3 col-6">
							<label>Plano</label>
							<select class="form-select" name="plano" id="plano" onchange="alterarPlano()" required="">
								<option value="">Selecionar Plano</option>
								<?php
								$query = $pdo->query("SELECT * from planos where ativo = 'Sim' order by id desc");
								$res = $query->fetchAll(PDO::FETCH_ASSOC);
								$linhas = @count($res);
								if ($linhas > 0) {
									for ($i = 0; $i < $linhas; $i++) { ?>
										<option value="<?php echo $res[$i]['id'] ?>"><?php echo $res[$i]['nome'] ?></option>
									<?php }
								} ?>
							</select>
						</div>

						<div class="col-md-2 mb-2">
							<label>Valor do Plano</label>
							<input type="text" class="form-control" id="mensalidade" name="mensalidade" placeholder="R$ 0,00" oninput="mascaraMoeda(this)">
						</div>				

						
					</div>




					<div class="row">
						<div class="col-md-2 mb-2">
							<label>Frequência</label>
							<select name="frequencia" id="frequencia" class="form-select" required="">
						
								<?php
								$query = $pdo->query("SELECT * from frequencias where empresa = 0 or empresa is null order by id asc");
								$res = $query->fetchAll(PDO::FETCH_ASSOC);
								$linhas = @count($res);
								if ($linhas > 0) {
									for ($i = 0; $i < $linhas; $i++) {
										echo '<option value="' . $res[$i]['dias'] . '">' . $res[$i]['frequencia'] . '</option>';
									}
								} else {
									echo '<option value="0">Cadastre uma Forma de Pagamento</option>';
								}
								?>
							</select>
						</div>


						<div class="col-md-3 mb-2 col-6">
							<label>Vencimento</label>
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" ><i class="fa-regular fa-calendar"></i></span>
								</div>
								<input type="date" name="vencimento" id="vencimento" class="form-control"
									value="<?php echo $data_atual ?>">
							</div>
						</div>
						<div class="col-md-3 col-6">
							<label>Pago Em (Se já foi Pago)</label>
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa-regular fa-calendar"></i></span>
								</div>
								<input type="date" name="data_pgto" id="data_pgto" value="" class="form-control">
							</div>
						</div>
						<div class="col-md-2 col-6">
							<label>Forma Pgto</label>
							<select name="forma_pgto" id="forma_pgto" class="form-select">
								<?php
								$query = $pdo->query("SELECT * from formas_pgto where empresa = 0 or empresa is null order by id asc");
								$res = $query->fetchAll(PDO::FETCH_ASSOC);
								$linhas = @count($res);
								if ($linhas > 0) {
									for ($i = 0; $i < $linhas; $i++) {
										echo '<option value="' . $res[$i]['id'] . '">' . $res[$i]['nome'] . '</option>';
									}
								} else {
									echo '<option value="0">Cadastre uma Forma de Pagamento</option>';
								}
								?>
							</select>
						</div>
					</div>



						<div class="row mb-3">
						<div class="col-md-4">
							<div class="form-group">
								<label>Logo (*PNG)</label>
								<input class="form-control" type="file" name="foto-logo" onChange="carregarImgLogoEmp();" id="foto-logo-emp">
							</div>
						</div>
						<div class="col-md-2">
							<div id="divImg">
								<img src="../img/sem-foto.png" width="80px" id="target-logo-emp">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Ícone (*Png)</label>
								<input class="form-control" type="file" name="foto-icone" onChange="carregarImgIconeEmp();"
									id="foto-icone-emp">
							</div>
						</div>
						<div class="col-md-2">
							<div id="divImg">
								<img src="../img/sem-foto.png" width="50px" id="target-icone-emp">
							</div>
						</div>
					</div>
					<div class="row mb-3">
						<div class="col-md-4">
							<div class="form-group">
								<label>Logo Relatório 644 x 142 (*Jpg)</label>
								<input class="form-control" type="file" name="foto-logo-rel" onChange="carregarImgLogoRelEmp();"
									id="foto-logo-rel-emp">
							</div>
						</div>
						<div class="col-md-2">
							<div id="divImg">
								<img src="../img/sem-foto.png" width="80px" id="target-logo-rel-emp">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Logo Painél 644 x 142 (Clara)(*PNG)</label>
								<input class="form-control" type="file" name="foto-painel" onChange="carregarImgPainelEmp();"
									id="foto-painel-emp">
							</div>
						</div>
						<div class="col-md-2">
							<div id="divImg">
								<img src="../img/sem-foto.png" width="80px" id="target-painel-emp">
							</div>
						</div>
					
					</div>



					<input type="hidden" class="form-control" id="id" name="id">
					<br>
					<small>
						<div id="mensagem" align="center"></div>
					</small>
				</div>
				<div class="modal-footer">
					<button type="submit" id="btn_salvar" class="btn btn-primary" value="">Salvar<i
							class="fa fa-check ms-2"></i></button>
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
				<small>
					<div class="row">
						<div class="col-md-12">
							<div class="tile">
								<div class="table-responsive">
									<table id="" class="text-left table table-bordered">
										<tr>
											<td style="width: 20%" class="bg-primary text-white">Telefone</td>
											<td><span id="telefone_dados"></span></td>
										</tr>
										<tr>
											<td class="bg-primary text-white w_150">Pessoa</td>
											<td><span id="pessoa_dados"></span></td>
										</tr>
										<tr>
											<td class="bg-primary text-white w_150">CPF / CNPJ</td>
											<td><span id="cpf_dados"></span></td>
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
											<td class="bg-primary text-white w_150">Rua</td>
											<td><span id="endereco_dados"></span></td>
										</tr>
										<tr>
											<td style="width: 25%" class="bg-primary text-white w_150">Número</td>
											<td><span id="numero_dados"></span></td>
										</tr>
										<tr>
											<td class="bg-primary text-white w_150">Bairro</td>
											<td><span id="bairro_dados"></span></td>
										</tr>
										<tr>
											<td class="bg-primary text-white w_150">Complemento</td>
											<td><span id="complemento_dados"></span></td>
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
										<tr>
											<td class="bg-primary text-white w_150">Plano</td>
											<td><span id="nome_plano_dados"></span></td>
										</tr>
										<tr>
											<td class="bg-primary text-white w_150">Plano</td>
											<td><span id="nome_frequencia_dados"></span></td>
										</tr>
										<tr>
											<td class="bg-primary text-white w_150">Dispositivos</td>
											<td><span id="nome_dispositivos_dados"></span> </td>
										</tr>
									</table>
								</div>
							</div>
						</div>
					</div>
				</small>
				<div id="listar_servicos" style="margin-top: 15px"></div>
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



<!-- Modal Contas -->
<div class="modal fade" id="modalContas" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_contas"></span></h4>
				<button id="btn-fechar-contas" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div id="listar_debitos" style="margin-top: 15px">
				</div>
				<input type="hidden" id="id_contas">
			</div>
		</div>
	</div>
</div>






<!-- Modal Baixar-->
<div class="modal fade" id="modalBaixar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" style="box-shadow: rgba(0, 0, 0, 0.07) 0px 1px 2px, rgba(0, 0, 0, 0.07) 0px 2px 4px, rgba(0, 0, 0, 0.07) 0px 4px 8px, rgba(0, 0, 0, 0.07) 0px 8px 16px, rgba(0, 0, 0, 0.07) 0px 16px 32px, rgba(0, 0, 0, 0.07) 0px 32px 64px;">
		<div class="modal-content">
			<div class="modal-header bg-dark text-white">
				<h4 class="modal-title" id="tituloModal">Baixar Conta: <span id="descricao-baixar"> </span></h4>
				<button id="btn-fechar-baixar" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<form id="form-baixar" method="post">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-5 col-6">
							<label>Valor <small class="text-muted">(Total ou Parcial)</small></label>
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text">R$</span>
								</div>
								<input onkeyup="mascaraMoeda(this); totalizar()" type="text" class="form-control" name="valor-baixar" id="valor-baixar"
									required>
							</div>
						</div>
						<div class="col-md-7 col-6">
							<div class="form-group">
								<label>Forma PGTO</label>
								<select class="form-select" name="saida-baixar" id="saida-baixar" required onchange="calcularTaxa()">
									<?php
									$query = $pdo->query("SELECT * FROM formas_pgto order by id asc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									for ($i = 0; $i < @count($res); $i++) {
										foreach ($res[$i] as $key => $value) {
										}
									?>
										<option value="<?php echo $res[$i]['id'] ?>"><?php echo $res[$i]['nome'] ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3 col-6">
							<div class="mb-3">
								<label>Multa em R$</label>
								<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text">R$</span>
								</div>
								<input onkeyup="mascaraMoeda(this); totalizar()" type="text" class="form-control" name="valor-multa" id="valor-multa"
									placeholder="Ex 15.00" value="0">
									</div>
							</div>
						</div>
						<div class="col-md-3 col-6">
							<div class="mb-3">
								<label>Júros em R$</label>
								<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text">R$</span>
								</div>
								<input onkeyup="mascaraMoeda(this); totalizar()" type="text" class="form-control" name="valor-juros" id="valor-juros"
									placeholder="Ex 0.15" value="0">
								</div>
							</div>
						</div>
						<div class="col-md-3 col-6">
							<div class="mb-3">
								<label>Desconto R$</label>
								<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text">R$</span>
								</div>
								<input onkeyup="mascaraMoeda(this); totalizar()" type="text" class="form-control" name="valor-desconto" id="valor-desconto"
									placeholder="Ex 15.00" value="0">
								</div>
							</div>
						</div>
						<div class="col-md-3 col-6">
							<div class="mb-3">
								<label>Taxa PGTO</label>
								<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text">R$</span>
								</div>
								<input onkeyup="mascaraMoeda(this); totalizar()" type="text" class="form-control" name="valor-taxa" id="valor-taxa"
									placeholder="" value="">
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 col-6">
							<div class="mb-3">
								<label>Data da Baixa</label>
								<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa-regular fa-calendar"></i></span>
								</div>
								<input type="date" class="form-control" name="data-baixar" id="data-baixar"
									value="<?php echo date('Y-m-d') ?>">
								</div>
							</div>
						</div>
						<div class="col-md-6 col-6">
							<div class="mb-3">
								<label>SubTotal</label>
								<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text">R$</span>
								</div>
								<input type="text" class="form-control" name="subtotal" id="subtotal" readonly>
								</div>
							</div>
						</div>
					</div>
					<small>
						<div id="mensagem-baixar" align="center"></div>
					</small>
					<input type="hidden" class="form-control" name="id-baixar" id="id-baixar">
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success" id="btn_salvar_baixar">Baixar <i class="fa-solid fa-check ms-2"></i></button>
					<button class="btn btn-success" type="button" id="btn_carregando_baixar" style="display: none">
						<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Carregando...
					</button>
				</div>
			</form>
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



<script type="text/javascript">
	var pag = "<?= $pag ?>"
</script>
<script src="js/ajax.js"></script>




<script type="text/javascript">
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

		reader.onloadend = function() {
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
function totalizar() {
// Obtém os valores dos campos
let valor = $('#valor-baixar').val();
let desconto = $('#valor-desconto').val();
let juros = $('#valor-juros').val();
let multa = $('#valor-multa').val();
let taxa = $('#valor-taxa').val();

// Substitui vírgulas por pontos e remove pontos extras
valor = valor.replace(",", ".").replace(/\.(?=.*\.)/g, "");
desconto = desconto.replace(",", ".").replace(/\.(?=.*\.)/g, "");
juros = juros.replace(",", ".").replace(/\.(?=.*\.)/g, "");
multa = multa.replace(",", ".").replace(/\.(?=.*\.)/g, "");
taxa = taxa.replace(",", ".").replace(/\.(?=.*\.)/g, "");


// Define valores padrão como 0 se estiverem vazios
valor = valor === "" ? 0 : parseFloat(valor);
desconto = desconto === "" ? 0 : parseFloat(desconto);
juros = juros === "" ? 0 : parseFloat(juros);
multa = multa === "" ? 0 : parseFloat(multa);
taxa = taxa === "" ? 0 : parseFloat(taxa);

// Calcula o subtotal
let subtotal = valor + juros + taxa + multa - desconto;

// Supondo que subtotal já tenha sido calculado
$('#subtotal').val(formatarMoeda(subtotal));
}

  function formatarMoeda(valor) {
// Converte o valor para um número
valor = parseFloat(valor);

// Formata o número com duas casas decimais e separador de milhar
return valor.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}


	function calcularTaxa() {
		pgto = $('#saida-baixar').val();
		valor = $('#valor-baixar').val();
		$.ajax({
			url: 'paginas/receber/calcular_taxa.php',
			method: 'POST',
			data: {
				valor,
				pgto
			},
			dataType: "html",
			success: function(result) {
				$('#valor-taxa').val(result);
				totalizar();
			}
		});


	}
</script>




<script type="text/javascript">
	$("#form-baixar").submit(function() {

		$('#btn_salvar_baixar').hide();
		$('#btn_carregando_baixar').show();

		event.preventDefault();
		var formData = new FormData(this);

		var id = $('#id_contas').val();

		$.ajax({
			url: 'paginas/receber/baixar.php',
			type: 'POST',
			data: formData,

			success: function(mensagem) {

				$('#mensagem-baixar').text('');
				$('#mensagem-baixar').removeClass()
				if (mensagem.trim() == "Baixado com Sucesso") {
					$('#btn-fechar-baixar').click();
					listarDebitos(id);

				} else {
					$('#mensagem-baixar').addClass('text-danger')
					$('#mensagem-baixar').text(mensagem)
				}

				$('#btn_salvar_baixar').show();
				$('#btn_carregando_baixar').hide();

			},

			cache: false,
			contentType: false,
			processData: false,

		});

	});
</script>





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



<script>
	function buscarCNPJ() {

		var cnpj = $('#cpf').val().replace(/\D/g, ''); // Remover tudo que não for número
		if (cnpj.length === 14) { // Verifica se o CNPJ tem 14 dígitos
			$.ajax({
				url: 'https://www.receitaws.com.br/v1/cnpj/' + cnpj,
				type: 'GET',
				dataType: 'jsonp', // A API retorna um JSONP para evitar CORS
				success: function(dados) {
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
				error: function() {
					alert("Erro ao buscar os dados do CNPJ.");
				}
			});
		} else {
			alert("Por favor, insira um CNPJ válido com 14 dígitos.");
		}
	}
</script>



<script type="text/javascript">
	function carregarImgLogoEmp() {
		var target = document.getElementById('target-logo-emp');
		var file = document.querySelector("#foto-logo-emp").files[0];

		var reader = new FileReader();

		reader.onloadend = function() {
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
	function carregarImgLogoRelEmp() {
		var target = document.getElementById('target-logo-rel-emp');
		var file = document.querySelector("#foto-logo-rel-emp").files[0];

		var reader = new FileReader();

		reader.onloadend = function() {
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
	function carregarImgIconeEmp() {
		var target = document.getElementById('target-icone-emp');
		var file = document.querySelector("#foto-icone-emp").files[0];

		var reader = new FileReader();

		reader.onloadend = function() {
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
	function carregarImgPainelEmp() {
		var target = document.getElementById('target-painel-emp');
		var file = document.querySelector("#foto-painel-emp").files[0];

		var reader = new FileReader();

		reader.onloadend = function() {
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


		function alterarPlano() {
			var plano = $('#plano').val();
			$.ajax({
				url: 'paginas/' + pag + "/valor_plano.php",
				method: 'POST',
				data: { plano },
				dataType: "html",

				success: function (result) {
					var split = result.split("*");
					var mensalidade = split[0];	
					var dispositivos = split[1];						
					$('#mensalidade').val(mensalidade);
					$('#dispositivos').val(dispositivos);
				}
			});
		}
	</script>
