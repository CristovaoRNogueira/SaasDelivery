<?php
$pag = 'receber';

if (@$receber == 'ocultar') {
	echo "<script>window.location='index'</script>";
	exit();
}
$query2 = $pdo->query("SELECT * FROM usuarios where id = '$id_usuario'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
if (@count($res2) > 0) {
	$nome_usuario = $res2[0]['nivel'];
}

if (@$pag == 'ocultar') {
	echo "<script>window.location='index'</script>";
	exit();
}


?>

<div class="justify-content-between">
	<form action="rel/receber_class.php" target="_blank" method="POST">
		<div class="left-content mt-2">
			<a style="margin-bottom: 20px; margin-top: 20px" class="btn ripple btn-primary text-white" onclick="inserir()"
				type="button"><i class="fe fe-plus me-2"></i>Adicionar Conta</a>

			<div style="display: inline-block; position:absolute; right:10px; margin-bottom: 10px">
				<button style="width:40px" type="submit" class="btn btn-danger ocultar_mobile_app" title="Gerar Relatório"><i
						class="fa fa-file-pdf-o"></i></button>
			</div>

			<big><a class="btn btn-danger" href="#" onclick="deletarSel()" title="Excluir" id="btn-deletar"
					style="display:none"><i class="fe fe-trash-2"></i> Deletar</a></big>

					<a class="btn btn-success" href="#" onclick="valorBaixar()" title="Baixar Contas Selecionadas" id="btn-baixar" style="display:none"><i class="fa fa-check-square"></i></i> Baixar</a>

			<div class="cab_mobile"></div>

			<div style="display: inline-block; margin-bottom: 10px">
				<input style="height:35px; width:49%; font-size: 13px;" type="date" class="form-control2" name="dataInicial"
					id="dataInicial" value="<?php echo $data_inicio_mes ?>" required onchange="buscar()">
				<input style="height:35px; width:49%; font-size: 13px;" type="date" class="form-control2" name="dataFinal"
					id="dataFinal" value="<?php echo $data_final_mes ?>" required onchange="buscar()">
			</div>
		</div>

		<div class="card-group" style="margin-bottom: -30px">

			<div class="card text-center mb-5" style="width: 100%; margin-right: 10px; border-radius: 10px; height:90px">
				<a class="text-white" href="#"
					onclick="$('#tipo_data_filtro').val('Vencidas'); $('#pago').val('Vencidas'); buscar(); ">
					<div class="card-header bg-red">
						Vencidas
						<i class="fa fa-external-link pull-right"></i>
					</div>
					<div class="card-corpo">
						<p class="card-text" style="margin-top:-15px;">
						<h5><span class="text-danger" id="total_vencidas">R$ 0,0</span></h5>
						</p>
					</div>
				</a>
			</div>


			<div class="card text-center mb-5" style="width: 100%; margin-right: 10px; border-radius: 10px; height:90px">
				<a href="#" onclick="$('#tipo_data_filtro').val('Hoje'); $('#pago').val(''); buscar(); ">
					<div class="card-header bg-orange">
						Vence Hoje
						<i class="fa fa-external-link pull-right"></i>
					</div>
					<div class="card-corpo">
						<p class="card-text" style="margin-top:-15px;">
						<h5><span class="text-danger" id="total_hoje">R$ 0,0</span></h5>
						</p>
					</div>
				</a>
			</div>
			<div class="card text-center mb-5" style="width: 100%; margin-right: 10px; border-radius: 10px; height:90px">
				<a href="#" onclick="$('#tipo_data_filtro').val('Amanha'); $('#pago').val(''); buscar(); ">
					<div class="card-header" style="background: gray">
						Vence Amanhã
						<i class="fa fa-external-link pull-right"></i>
					</div>
					<div class="card-corpo">
						<p class="card-text" style="margin-top:-15px;">
						<h5><span style="color: gray" id="total_amanha">R$ 0,0</span></h5>
						</p>
					</div>
				</a>
			</div>
			<div class="card text-center mb-5" style="width: 100%; margin-right: 10px; border-radius: 10px; height:90px">
				<a href="#" onclick=" $('#tipo_data_filtro').val('Recebidas'); $('#pago').val('Sim'); buscar();">
					<div class="card-header" style="background: #2b7a00">
						Recebidas
						<i class="fa fa-external-link pull-right"></i>
					</div>
					<div class="card-corpo">
						<p class="card-text" style="margin-top:-15px;">
						<h5><span style="color: #2b7a00" id="total_recebidas">R$ 0,0</span></h5>
						</p>
					</div>
				</a>
			</div>
			<div class="card text-center mb-5" style="width: 100%; margin-right: 10px; border-radius: 10px; height:90px">
				<a href="#" onclick="$('#tipo_data_filtro').val('Todas'); $('#pago').val(''); buscar();">
					<div class="card-header" style="background: #1f1f1f;">
						Total
						<i class="fa fa-external-link pull-right"></i>
					</div>
					<div class="card-corpo">
						<p class="card-text" style="margin-top:-15px;">
						<h5><span style="color: #1f1f1f" class="verde" id="total_total">R$ 0,0</span></h5>
						</p>
					</div>
				</a>
			</div>
			<div class="card text-center mb-5" style="width: 100%; margin-right: 10px; border-radius: 10px; height:90px">
				<a class="text-white" href="#" onclick="$('#tipo_data_filtro').val('Pedentes'); $('#pago').val(''); buscar();">
					<div class="card-header" style="background: #920801">
						Todas Pedentes
						<i class="fa fa-external-link pull-right"></i>
					</div>
					<div class="card-corpo">
						<p class="card-text" style="margin-top:-15px;">
						<h5><span style="color: #920801" id="total_pendentes">R$ 0,0</span></h5>
						</p>
					</div>
				</a>
			</div>
		</div>
		<input type="hidden" name="tipo_data" id="tipo_data">
		<input type="hidden" name="pago" id="pago">
		<input type="hidden" name="tipo_data_filtro" id="tipo_data_filtro">
	</form>
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

<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_inserir"></span></h4>
				<button id="btn-fechar" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<form id="form_ajax">
				<div class="modal-body">

					<div class="row">
						<div class="col-md-6 mb-2">
							<label>Descrição</label>
							<input type="text" class="form-control" id="descricao" name="descricao" placeholder="Descrição">
						</div>
						<div class="col-md-6">
							<label>Cliente</label>
							<select name="cliente" id="cliente" class="sel32" style="width:100%; height:35px; ">
								<option value="0">Selecione um Cliente</option>
								<?php
								if ($mostrar_registros == 'Não') {
									$query = $pdo->query("SELECT * from empresas where usuario = '$id_usuario' order by id asc");
								} else {
									$query = $pdo->query("SELECT * from empresas order by id asc");
								}
								$res = $query->fetchAll(PDO::FETCH_ASSOC);
								$linhas = @count($res);
								if ($linhas > 0) {
									for ($i = 0; $i < $linhas; $i++) {
										echo '<option value="' . $res[$i]['id'] . '">' . $res[$i]['nome'] . '</option>';
									}
								}
								?>
							</select>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3 col-6">
							<label>Digite o valor</label>
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text">R$</span>
								</div>
								<input type="text" id="valor" name="valor" class="form-control" placeholder="R$ 0,00"
									oninput="mascaraMoeda(this)">
							</div>
						</div>
						<div class="col-md-5 col-6">
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
						<div class="col-md-4 mb-2">
							<label>Frequência</label>
							<select name="frequencia" id="frequencia" class="form-select">
							<option value="0">Nenhuma</option>
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
					</div>

					<div class="row">
						<div class="col-md-4 mb-2 col-6">
							<label>Vencimento</label>
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" ><i class="fa-regular fa-calendar"></i></span>
								</div>
								<input type="date" name="vencimento" id="vencimento" class="form-control"
									value="<?php echo $data_atual ?>">
							</div>
						</div>
						<div class="col-md-4 col-6">
							<label>Pago Em</label>
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa-regular fa-calendar"></i></span>
								</div>
								<input type="date" name="data_pgto" id="data_pgto" value="" class="form-control">
							</div>
						</div>
						<div class="col-md-4 ">
							<label>Total Recorrência</label>
							<input type="number" name="quant_recorrencia" id="quant_recorrencia" value="" class="form-control"
								placeholder="Repetir x vezes">
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 mb-2">
							<label>Observações</label>
							<input type="text" class="form-control" id="obs" name="obs" placeholder="Observações">
						</div>
						<div class="col-md-4 mb-2">
							<label>Arquivo</label>
							<input type="file" class="form-control" id="arquivo" name="foto" onchange="carregarImg()">
						</div>
						<div class="col-md-2">
							<img width="80px" id="target">
						</div>
					</div>

					<input type="hidden" class="form-control" id="id" name="id">

					<br>
					<small>
						<div id="mensagem" align="center"></div>
					</small>
				</div>
				<div class="modal-footer">
					<button id="btn_salvar" type="submit" class="btn btn-primary">Salvar <i class="fa-solid fa-check"></i>
					</button>
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
					<div class="col-md-6">
						<div class="tile">
							<div class="table-responsive">
								<table id="" class="text-left table table-bordered">
									<tr>
										<td class="bg-primary text-white">Cliente</td>
										<td><span id="cliente_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white">Vencimento</td>
										<td><span id="vencimento_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Pagamento</td>
										<td><span id="data_pgto_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Frequência</td>
										<td><span id="frequencia_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Multa</td>
										<td>R$ <span id="multa_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Júros</td>
										<td>R$ <span id="juros_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Desconto</td>
										<td>R$ <span id="desconto_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Taxa</td>
										<td>R$ <span id="taxa_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Subtotal</td>
										<td>R$ <span id="total_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Qtd Recorrência</td>
										<td><span id="quant_recorrencia_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Pago</td>
										<td><span id="pago_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Forma PGTO</td>
										<td><span id="nome_pgto_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Lançado Por</td>
										<td><span id="usu_lanc_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">Baixa Usuário</td>
										<td><span id="usu_pgto_dados"></span></td>
									</tr>
									<tr>
										<td class="bg-primary text-white w_150">OBS</td>
										<td><span id="obs_dados"></span></td>
									</tr>
								</table>
							</div>
						</div>
					</div>


					<div class="col-md-6">
						<div class="tile">
							<div class="table-responsive">
								<table id="" class="text-left table table-bordered">
									<tr>
										<td align="center"><img src="" id="target_dados" width="200px"></td>
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





<!-- Modal Parcelar-->
<div class="modal fade" id="modalParcelar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="tituloModal">Parcelar Conta: <span id="nome-parcelar"> </span></h4>
				<button id="btn-fechar-parcelar" aria-label="Close" class="btn-close" data-bs-dismiss="modal"
					type="button"><span class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<form method="post" id="form-parcelar">
				<div class="modal-body">

					<div class="row">
						<div class="col-md-6 col-6">
							<label>Valor</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">R$</span>
								</div>
								<input type="text" name="valor-parcelar" id="valor-parcelar" class="form-control" readonly>
							</div>
						</div>
						<div class="col-md-6 col-6">
							<div class="">
								<label>Qtd de Parcelas</label>
								<input type="number" class="form-control" name="qtd-parcelar" id="qtd-parcelar"
									placeholder="Quantidade Parcela" required>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-7">
							<div class="form-group">
								<label>Frequência Parcelas</label>
								<select class="form-select" name="frequencia" id="frequencia-parcelar" required style="width:100%;">
									<?php
									$query = $pdo->query("SELECT * FROM frequencias where empresa = 0 or empresa is null order by id desc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									for ($i = 0; $i < @count($res); $i++) {
										foreach ($res[$i] as $key => $value) {
										}
										$id_item = $res[$i]['id'];
										$nome_item = $res[$i]['frequencia'];
										$dias = $res[$i]['dias'];
										if ($nome_item != 'Uma Vez' and $nome_item != 'Única') {
											?>
											<option <?php if ($nome_item == 'Mensal') { ?> selected <?php } ?> value="<?php echo $dias ?>">
												<?php echo $nome_item ?>
											</option>
										<?php }
									} ?>
								</select>
							</div>
						</div>
						<div align="right">
							<div class="" style="margin-top: 22px">
								<button type="submit" class="btn btn-primary" id="btn_salvar_parcelar">Parcelar<i
										class="fa-solid fa-check ms-2"></i>
								</button>
								<button class="btn btn-primary" type="button" id="btn_carregando_parcelar" style="display: none">
									<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Carregando...
								</button>
							</div>
						</div>
					</div>
					<input type="hidden" name="id-parcelar" id="id-parcelar">
					<input type="hidden" name="nome-parcelar" id="nome-input-parcelar">
					<small>
						<div id="mensagem-parcelar" align="center" class="mt-3"></div>
					</small>
				</div>
			</form>
		</div>
	</div>
</div>





<!-- Modal Baixar-->
<div class="modal fade" id="modalBaixar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="tituloModal">Baixar Conta: <span id="descricao-baixar"> </span></h4>
				<button id="btn-fechar-baixar" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<form id="form-baixar" method="post">
				<div class="modal-body">

					<div class="row">
						<div class="col-md-6 col-6">
							<label>Valor <small class="text-muted">(Total ou Parcial)</small></label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">R$</span>
								</div>
								<input type="text" name="valor-baixar" id="valor-baixar" required class="form-control"
									oninput="mascaraMoeda(this)" onkeyup="totalizar()">
							</div>
						</div>
						<div class="col-md-6 col-6">
							<div class="form-group">
								<label>Forma PGTO</label>
								<select class="form-select" name="saida-baixar" id="saida-baixar" required onchange="calcularTaxa()">
									<option value="0">Nenhuma</option>
									<?php
									$query = $pdo->query("SELECT * FROM formas_pgto where empresa = 0 or empresa is null order by id asc");
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
						<div class="col-md-4 col-4">
							<label>Multa</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">R$</span>
								</div>
								<input type="text" name="valor-multa" id="valor-multa" class="form-control" oninput="mascaraMoeda(this)"
									onkeyup="totalizar()" placeholder="Ex 0.15">
							</div>
						</div>
						<div class="col-md-4 col-4">
							<label>Júros</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">R$</span>
								</div>
								<input type="text" name="valor-juros" id="valor-juros" class="form-control" oninput="mascaraMoeda(this)"
									onkeyup="totalizar()" placeholder="Ex 0.15">
							</div>
						</div>
						<div class="col-md-4 col-4">
							<label>Taxa PGTO</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">R$</span>
								</div>
								<input type="text" name="valor-taxa" id="valor-taxa" class="form-control" oninput="mascaraMoeda(this)"
									onkeyup="totalizar()" placeholder="Ex 0.15">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4 col-6">
							<label>Desconto</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">R$</span>
								</div>
								<input type="text" name="valor-desconto" id="valor-desconto" class="form-control"
									oninput="mascaraMoeda(this)" onkeyup="totalizar()" placeholder="Ex 0.15">
							</div>
						</div>
						<div class="col-md-4 mb-2 col-6">
							<label>Data da Baixa</label>
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa-regular fa-calendar"></i></span>
								</div>
								<input type="date" name="data-baixar" id="data-baixar" class="form-control"
									value="<?php echo date('Y-m-d') ?>">
							</div>
						</div>
						<div class="col-md-4 col-6">
							<label>SubTotal</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">R$</span>
								</div>
								<input type="text" name="subtotal" id="subtotal" readonly class="form-control"
									oninput="mascaraMoeda(this)" onkeyup="totalizar()" placeholder="Ex 0.15">
							</div>
						</div>
					</div>

					<small>
						<div id="mensagem-baixar" align="center"></div>
					</small>
					<input type="hidden" class="form-control" name="id-baixar" id="id-baixar">
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary btn-lg" id="btn_salvar_baixar">Baixar<i
									class="fa fa-check ms-2"></i></button>
							<button class="btn btn-primary" type="button" id="btn_carregando_baixar" style="display: none">
								<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Carregando...
							</button>
				</div>
			</form>
		</div>
	</div>
</div>




<!-- Modal Residuo -->
<div class="modal fade" id="modalResiduos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="tituloModal">Residuos da Conta</h4>
				<button id="btn-fechar-residuos" aria-label="Close" class="btn-close" data-bs-dismiss="modal"
					type="button"><span class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">

				<small>
					<div id="listar-residuos"></div>
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
							<button type="submit" class="btn btn-primary" id="btn_inserir">Inserir<i
									class="fa fa-check ms-2"></i></button>
							<button class="btn btn-primary" type="button" id="btn_carregando_inserir" style="display: none">
								<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Carregando...
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
	var pag = "<?= $pag ?>"
</script>
<script src="js/ajax.js"></script>


<script type="text/javascript">
	$(document).ready(function () {
		$('.sel2').select2({
			dropdownParent: $('#modalForm')
		});

	});
</script>


<script type="text/javascript">
	function marcarTodos() {
		let checkbox = document.getElementById('input-todos');
		var usuario = $('#id_permissoes').val();

		if (checkbox.checked) {
			adicionarPermissoes(usuario);
		} else {
			limparPermissoes(usuario);
		}
	}
</script>



<script type="text/javascript">
	function carregarImg() {
		var target = document.getElementById('target');
		var file = document.querySelector("#arquivo").files[0];

		var arquivo = file['name'];
		resultado = arquivo.split(".", 2);
		if (resultado[1] === 'pdf') {
			$('#target').attr('src', "images/pdf.png");
			return;
		}
		if (resultado[1] === 'rar' || resultado[1] === 'zip') {
			$('#target').attr('src', "images/rar.png");
			return;
		}
		if (resultado[1] === 'doc' || resultado[1] === 'docx' || resultado[1] === 'txt') {
			$('#target').attr('src', "images/word.png");
			return;
		}
		if (resultado[1] === 'xlsx' || resultado[1] === 'xlsm' || resultado[1] === 'xls') {
			$('#target').attr('src', "images/excel.png");
			return;
		}
		if (resultado[1] === 'xml') {
			$('#target').attr('src', "images/xml.png");
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
	function buscar() {
		var filtro = $('#tipo_data_filtro').val();
		var dataInicial = $('#dataInicial').val();
		var dataFinal = $('#dataFinal').val();
		var tipo_data = $('#tipo_data').val();
		listar(filtro, dataInicial, dataFinal, tipo_data)
	}


	function tipoData(tipo) {
		$('#tipo_data').val(tipo);
		buscar();
	}

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
			url: 'paginas/' + pag + "/calcular_taxa.php",
			method: 'POST',
			data: { valor, pgto },
			dataType: "html",

			success: function (result) {
				$('#valor-taxa').val(result);
				totalizar();
			}
		});
	}
</script>

<script type="text/javascript">
	$("#form-baixar").submit(function () {

		$('#btn_salvar_baixar').hide();
		$('#btn_carregando_baixar').show();


		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: 'paginas/' + pag + "/baixar.php",
			type: 'POST',
			data: formData,

			success: function (mensagem) {
				$('#mensagem-baixar').text('');
				$('#mensagem-baixar').removeClass()
				if (mensagem.trim() == "Baixado com Sucesso") {
					alertsucesso(mensagem)
					$('#btn-fechar-baixar').click();
					buscar();
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



<script type="text/javascript">
	$("#form-parcelar").submit(function () {

		$('#btn_salvar_parcelar').hide();
		$('#btn_carregando_parcelar').show();

		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: 'paginas/' + pag + "/parcelar.php",
			type: 'POST',
			data: formData,

			success: function (mensagem) {
				$('#mensagem-parcelar').text('');
				$('#mensagem-parcelar').removeClass()
				if (mensagem.trim() == "Parcelado com Sucesso") {
					alertsucesso(mensagem)
					$('#btn-fechar-parcelar').click();
					buscar();
				} else {
					$('#mensagem-parcelar').addClass('text-danger')
					$('#mensagem-parcelar').text(mensagem)
				}

				$('#btn_salvar_parcelar').show();
				$('#btn_carregando_parcelar').hide();

			},
			cache: false,
			contentType: false,
			processData: false,
		});
	});




	function valorBaixar() {
		var ids = $('#ids').val();
		$.ajax({
			url: 'paginas/' + pag + "/valor_baixar.php",
			method: 'POST',
			data: {
				ids
			},
			dataType: "html",
			success: function (result) {
				BaixarContaSel(result)
			}
		});
	}

	function BaixarContaSel(result) {
		var total = result;

		const swalWithBootstrapButtons = Swal.mixin({
			customClass: {
				confirmButton: "btn btn-success",
				cancelButton: "btn btn-danger me-1"
			},
			buttonsStyling: false
		});

		swalWithBootstrapButtons.fire({
			title: "Contas Selecionadas?",
			html: "Baixar o Total de: <span style='color: green;'>R$ " + total + "</span>",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: "Sim, Baixar!",
			cancelButtonText: "Não, Cancelar!",
			reverseButtons: true
		}).then((result) => {
			if (result.isConfirmed) {
				deletarSelBaixar()
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
</script>



<script type="text/javascript">
	$("#form-arquivos").submit(function () {
		event.preventDefault();

		$('#btn_inserir').hide();
		$('#btn_carregando_inserir').show();

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

				$('#btn_inserir').show();
				$('#btn_carregando_inserir').hide();

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
			data: {
				id
			},
			dataType: "text",
			success: function (result) {
				$("#listar-arquivos").html(result);
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

<script>
	modalForm.addEventListener('shown.bs.modal', () => {
		descricao.focus()
	})
</script>


<script type="text/javascript">
	
$("#form_ajax").submit(function (event) {

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
                buscar();
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
</script>