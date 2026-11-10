<?php

if (@$produtos === 'ocultar') {
	echo "<script>window.location='../index.php'</script>";
	exit();
}


$pag = 'produtos';
?>
<div class="breadcrumb-header justify-content-between">
	<div class="left-content mt-2">
		<a class="btn ripple btn-primary text-white" onclick="inserir()" type="button"><i class="fe fe-plus me-1"></i>
			Adicionar <?php echo ucfirst($pag); ?></a>

		<!-- EXCLUIR MULTIPLO -->
		<a class="btn btn-danger" href="#" onclick="deletarSelProdutos()" title="Excluir" id="btn-deletar" style="display:none"><i class="fe fe-trash-2"></i> Deletar</a>

		<select class="form-select sel7" name="categoria" id="busca" style="width:350px" onchange="buscar()">
			<option value="">Buscasr por Categoria</option>
			<?php
			$query = $pdo->query("SELECT * from categorias where empresa = '$id_empresa' order by id desc");
			$res = $query->fetchAll(PDO::FETCH_ASSOC);
			$linhas = @count($res);
			if ($linhas > 0) {
				for ($i = 0; $i < $linhas; $i++) { ?>
					<option value="<?php echo $res[$i]['id'] ?>"><?php echo $res[$i]['nome'] ?></option>
			<?php }
			} ?>
		</select>


	</div>


</div>


<div class="bs-example widget-shadow" style="padding:15px" id="listar">

</div>



<input type="hidden" id="ids">


<!-- Modal Inserir-->
<div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title"><span id="titulo_inserir"></span></h4>
				<button id="btn-fechar" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<form id="form_produtos">
				<div class="modal-body">

					<div class="row">
						<div class="col-md-4 mb-2">							
								<label>Código</label>
								<input type="text" class="form-control" id="codigo" name="codigo" placeholder="Código de Barras" onkeyup="gerarCodigo()">							
							</div>
						<div class="col-md-5 needs-validation was-validated">
							<div class="form-group">
								<label for="exampleInputEmail1">Nome do Produto</label>
								<input type="text" class="form-control" id="nome" name="nome" placeholder="Digite o Produto" required>
							</div>
						</div>

						<div class="col-md-3">

							<div class="form-group">
								<label for="exampleInputEmail1">Categoria</label>
								<select class="form-select sel3" id="categoria" name="categoria" style="width:100%;">

									<?php
									$query = $pdo->query("SELECT * FROM categorias where empresa = '$id_empresa' ORDER BY id desc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$total_reg = @count($res);
									if ($total_reg > 0) {
										for ($i = 0; $i < $total_reg; $i++) {
											foreach ($res[$i] as $key => $value) {
											}
											echo '<option value="' . $res[$i]['id'] . '">' . $res[$i]['nome'] . '</option>';
										}
									} else {
										echo '<option value="0">Cadastre uma Categoria</option>';
									}
									?>


								</select>
							</div>
						</div>

					</div>



					<div class="row">
						<div class="col-md-12 mb-2">
							<label>Descrição <small>(Até 1000 Caracteres)</small></label>
							<textarea type="text" class="form-control" id="descricao" name="descricao"
								placeholder="Descrição do Produto">	</textarea>
						</div>
					</div>



					<div class="row">

						<div class="col-md-2">
							<div class="form-group">
								<label for="exampleInputEmail1">Valor Custo</label>
								<input type="text" class="form-control" id="valor_compra" name="valor_compra" placeholder="R$ 0,00">
							</div>
						</div>


						<div class="col-md-2">
							<div class="form-group">
								<label for="exampleInputEmail1">Valor Venda</label>
								<input type="text" class="form-control" id="valor_venda" name="valor_venda" placeholder="R$ 0,00">
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label for="val_promocional">Valor Promoção</label>
								<input type="text" class="form-control" id="val_promocional" name="val_promocional" placeholder="R$ 0,00">
							</div>
						</div>


						<div class="col-md-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Promoção?</label>
								<select class="form-select" id="promocao" name="promocao" style="width:100%;">
									<option value="Não">Não</option>
									<option value="Sim">Sim</option>
								</select>
							</div>
						</div>


						<div class="col-md-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Alerta Estoque</label>
								<input type="number" class="form-control" id="nivel_estoque" name="nivel_estoque"
									placeholder="Nível Min.">
							</div>
						</div>




					</div>

					<div class="row">




						<div class="col-md-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Combo?</label>
								<select class="form-select" id="combo" name="combo" style="width:100%;">

									<option value="Não">Não</option>
									<option value="Sim">Sim</option>

								</select>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Delivery?</label>
								<select class="form-select" id="delivery" name="delivery" style="width:100%;">
									<option value="Sim">Sim</option>
									<option value="Não">Não</option>
								</select>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Preparado Cozinha?</label>
								<select class="form-select" id="preparado" name="preparado" style="width:100%;">
									<option value="Cozinha">Cozinha</option>
									<option value="Bar">Bar</option>
									<option value="Outro">Outro Local (Local 3)</option>
									<option value="Não">Não</option>
								</select>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label for="tem_estoque">Tem Estoque?</label>
								<select class="form-select" id="tem_estoque" name="tem_estoque" style="width:100%;">

									<option value="Sim">Sim</option>
									<option value="Não">Não</option>

								</select>
							</div>
						</div>


					</div>





					<div class="row">

						<div class="col-md-3 mb-2 col-6">							
							<label>Unidade de Medida</label>
							<select class="form-select" name="unidade" id="unidade">
								<option value="0">Escolher Unidade</option>
								<?php 
								$query3 = $pdo->query("SELECT * from unidade_medida where empresa = '$id_empresa' order by id asc");
								$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
								$linhas = @count($res3);
								if($linhas > 0){
									for($i=0; $i<$linhas; $i++){ ?>
										<option value="<?php echo $res3[$i]['id'] ?>"><?php echo $res3[$i]['nome'] ?></option>
									<?php } } ?>
								</select>			
							</div>

						<div class="col-md-7">
							<div class="form-group">
								<label>Foto</label>
								<input class="form-control" type="file" name="foto" onChange="carregarImg();" id="foto">
							</div>
						</div>
						<div class="col-md-2">
							<div id="divImg">
								<img src="images/produtos/sem-foto.jpg" width="80px" id="target">
							</div>
						</div>
					</div>

					<div id="listar-codigo"></div>

					<input type="hidden" name="id" id="id">

					<br>
					<small>
						<div id="mensagem" align="center"></div>
					</small>
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary" id="btn_salvar">Salvar<i class="fa fa-check ms-2"></i></button>
					<button class="btn btn-primary" type="button" id="btn_carregando" style="display: none">
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
				<h4 class="modal-title" id="exampleModalLabel"><span id="nome_dados"></span><span id="titulo_dados"></span></h4>
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
										<td width="30%" class="bg-primary text-white">Código</td>
										<td><span id="codigo_dados"></span></td>
									</tr>

									<tr>
										<td width="30%" class="bg-primary text-white">Categoria</td>
										<td><span id="categoria_dados"></span></td>
									</tr>

									<tr>
										<td class="bg-primary text-white">Valor Compra</td>
										<td>R$ <span id="valor_compra_dados"></span></td>
									</tr>

									<tr>
										<td class="bg-primary text-white">Valor Venda</td>
										<td>R$ <span id="valor_venda_dados"></span></td>
									</tr>

									<tr>
										<td class="bg-primary text-white">Valor Promocional</td>
										<td>R$ <span id="val_promocional_dados"></span></td>
									</tr>

									<tr>
										<td class="bg-primary text-white">Estoque</td>
										<td><span id="estoque_dados"></span></td>
									</tr>

									<tr>
										<td class="bg-primary text-white">Alerta Nível Estoque</td>
										<td><span id="nivel_estoque_dados"></span></td>
									</tr>

									<tr>
										<td class="bg-primary text-white">Tem Estoque</td>
										<td><span id="tem_estoque_dados"></span></td>
									</tr>

									<tr>
										<td class="bg-primary text-white w_150">Promoção</td>
										<td><span id="promocao_dados"></span></td>
									</tr>

									<tr>
										<td class="bg-primary text-white w_150">Combo</td>
										<td><span id="combo_dados"></span></td>
									</tr>




									<tr>
										<td class="bg-primary text-white w_150">Preparar Cozinha</td>
										<td><span id="preparado_dados"></span></td>
									</tr>

									<tr>
										<td class="bg-primary text-white w_150">Delivery</td>
										<td><span id="delivery_dados"></span></td>
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
										<td align="center"><img src="" id="target_mostrar" width="200px"></td>
									</tr>

								</table>
							</div>
						</div>
					</div>


				</div>

				<div class="row">

					<div class="col-md-12">
						<div class="tile">
							<div class="table-responsive">
								<table id="" class="text-left table table-bordered">

									<tr>
										<td width="15%" class="bg-primary text-white">Descrição</td>
										<td><span id="descricao_dados"></span></td>
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









<!-- Modal Saida-->
<div class="modal fade" id="modalSaida" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="exampleModalLabel"><span id="nome_saida"></span></h4>
				<button id="btn-fechar-saida" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>

			<div class="modal-body">
				<form id="form-saida">

					<div class="row">
						<div class="col-md-4">
							<div class="form-group">

								<input type="number" class="form-control" id="quantidade_saida" name="quantidade_saida"
									placeholder="Quantidade" required>
							</div>
						</div>

						<div class="col-md-5">
							<div class="form-group">
								<input type="text" class="form-control" id="motivo_saida" name="motivo_saida" placeholder="Motivo Saída"
									required>
							</div>
						</div>
						<div class="col-md-3">
							<button type="submit" class="btn btn-primary">Salvar</button>

						</div>
					</div>

					<input type="hidden" id="id_saida" name="id">
					<input type="hidden" id="estoque_saida" name="estoque">

				</form>

				<br>
				<small>
					<div id="mensagem-saida" align="center"></div>
				</small>
			</div>


		</div>
	</div>
</div>





<!-- Modal Entrada-->
<div class="modal fade" id="modalEntrada" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="exampleModalLabel"><span id="nome_entrada"></span></h4>
				<button id="btn-fechar-entrada" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>

			<div class="modal-body">
				<form id="form-entrada">

					<div class="row">
						<div class="col-md-4">
							<div class="form-group">

								<input type="number" class="form-control" id="quantidade_entrada" name="quantidade_entrada"
									placeholder="Quantidade" required>
							</div>
						</div>

						<div class="col-md-5">
							<div class="form-group">
								<input type="text" class="form-control" id="motivo_entrada" name="motivo_entrada"
									placeholder="Motivo Entrada" required>
							</div>
						</div>
						<div class="col-md-3">
							<button type="submit" class="btn btn-primary">Salvar</button>

						</div>
					</div>

					<input type="hidden" id="id_entrada" name="id">
					<input type="hidden" id="estoque_entrada" name="estoque">

				</form>

				<br>
				<small>
					<div id="mensagem-entrada" align="center"></div>
				</small>
			</div>


		</div>
	</div>
</div>







<!-- Modal Variações-->
<div class="modal fade" id="modalVariacoes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_nome_var"></span></h4>
				<button id="btn-fechar-var" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>

			<div class="modal-body">
				<form id="form-var">


					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="exampleInputEmail1">Variação</label>
								<div id="listar_var_cat">

								</div>

							</div>
						</div>


						<div class="col-md-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Valor</label>
								<input type="text" class="form-control" id="valor_var" name="valor" placeholder="R$ 50,00" required>
							</div>
						</div>

						<div class="col-md-3" style="margin-top: 25px">
							<button id="btn_var" type="submit" class="btn btn-primary">Salvar</button>

						</div>

					</div>

					<input type="hidden" id="id_var" name="id">
					<input type="hidden" id="id_it_var" name="id_item">

				</form>

				<br>
				<small>
					<div id="mensagem-var" align="center"></div>
				</small>


				<div id="listar-var"></div>
			</div>


		</div>
	</div>
</div>





<!-- Modal Grades-->
<div class="modal fade" id="modalGrades" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_nome_grades"></span></h4>
				<button id="btn-fechar-grades" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>

			<div class="modal-body">
				<form id="form-grades">


					<div class="row">

						<div class="col-md-8">
							<div class="form-group">
								<label for="exampleInputEmail1">Descrição na hora da compra <small>(Até 70 Caracteres)</small></label>
								<input maxlength="70" type="text" class="form-control" id="texto" name="texto"
									placeholder="Descrição do item" required="">
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label for="exampleInputEmail1">Tipo Item

									<div class="icones_mobile" class="dropdown" style="display: inline-block; ">
										<a title="Clique para ver as Informações" href="#" aria-expanded="false" aria-haspopup="true"
											data-bs-toggle="dropdown" class="dropdown"><i class="fa fa-info-circle text-primary"></i> </a>
										<div class="dropdown-menu tx-13" style="width:500px">
											<div class="dropdown-item-text botao_excluir" style="width:500px">
												<div class="notification_desc2">
													<p>
														<b>Seletor Único</b><br>
														<span class="text-muted" style="font-size: 12px">Você poderá selecionar apenas uma opção,
															exemplo, esse produto acompanha uma bebida, selecione a bebida desejada.</span>
													</p><br>

													<p>
														<b>Seletor Múltiplos</b><br>
														<span class="text-muted" style="font-size: 12px">Você poderá selecionar diversos itens
															dentro desta grade, exemplo de adicionais, 3 adicionais de bacon, 2 de cheddar,
															etc.</span>
													</p><br>


													<p>
														<b>Apenas 1 Item de cada</b><br>
														<span class="text-muted" style="font-size: 12px">Você pode selecionar várias opções porém só
															poderá inserir 1 item de cada, exemplo remoção de ingredientes, retirar cebola, retirar
															tomate, etc, será sempre uma unica seleção por cada item.</span>
													</p><br>

													<p>
														<b>Seletor Variação</b><br>
														<span class="text-muted" style="font-size: 12px">Você poderá selecionar apenas uma opção,
															exemplo, Tamanho Grande, Médio, etc, será mostrado em locais onde define a variação do
															produto.</span>
													</p><br>


													</p>

												</div>

											</div>
										</div>
									</div>



								</label>
								<select class="form-select" id="tipo_item" name="tipo_item" style="width:100%;">

									<option value="Único">Seletor Único</option>
									<option value="Múltiplo">Seletor Múltiplos</option>
									<option value="1 de Cada">1 item de Cada</option>
									<option value="Variação">Variação Produto</option>

								</select>

							</div>
						</div>




					</div>


					<div class="row">
						<div class="col-md-9">
							<div class="form-group">
								<label for="exampleInputEmail1">Descrição Comprovante <small>(Até 70 Caracteres)</small></label>
								<input maxlength="70" type="text" class="form-control" id="nome_comprovante" name="nome_comprovante"
									placeholder="Descrição do item no comprovante" required="">
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label for="exampleInputEmail1">É Adicional?</label>
								<select class="form-select" id="adicional" name="adicional" style="width:100%;">

									<option value="Não">Não</option>
									<option value="Sim">Sim</option>

								</select>
							</div>
						</div>

					</div>

					<div class="row">
						<div class="col-md-5">
							<div class="form-group">
								<label for="exampleInputEmail1">Tipo Valor</label>
								<select class="form-select" id="valor_item" name="valor_item" style="width:100%;">
									<option value="Agregado">Valor Agregado</option>
									<option value="Único">Valor Único Produto</option>
									<option value="Produto">Mesmo Valor do Produto</option>
									<option value="Sem Valor">Sem Valor</option>

								</select>

							</div>
						</div>


						<div class="col-md-5">
							<div class="form-group">
								<label for="exampleInputEmail1">Limite de Seleção Itens</label>
								<input type="number" class="form-control" id="limite" name="limite"
									placeholder="Selecionar até x Itens">
							</div>
						</div>

						<div class="col-md-2" style="margin-top: 24px">
							<button id="btn_grade" type="submit" class="btn btn-primary">Salvar</button>

						</div>
					</div>



					<input type="hidden" id="id_grades" name="id">
					<input type="hidden" id="id_grade_editar" name="id_grade_editar">

				</form>

				<br>
				<small>
					<div id="mensagem-grades" align="center"></div>
				</small>


				<hr>
				<div id="listar-grades"></div>
			</div>


		</div>
	</div>
</div>








<!-- Modal Itens-->
<div class="modal fade" id="modalItens" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document" style="box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_nome_itens"></span></h4>
				<button id="btn-fechar-itens" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span
						class="text-white" aria-hidden="true">&times;</span></button>
			</div>

			<div class="modal-body">
				<form id="form-itens">


					<div class="row">

						<div class="col-md-12" id="div_nome">
							<div class="form-group">
								<label for="exampleInputEmail1">Nome <small>(Até 70 Caracteres)</small></label>
								<input maxlength="70" type="text" class="form-control" id="texto_item" name="texto"
									placeholder="Descrição do item">
							</div>
						</div>


						<div class="col-md-12" id="div_adicional">
							<div class="form-group">
								<label for="exampleInputEmail1">Escolher Adicional </label>

								<select class="form-select sel5" id="adicional_grade" name="adicional" style="width:100%;"
									onchange="alterarValor()">
									<option value="">Selecione um Adicional</option>
									<?php
									$query = $pdo->query("SELECT * FROM adicionais ORDER BY nome asc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$total_reg = @count($res);
									if ($total_reg > 0) {
										for ($i = 0; $i < $total_reg; $i++) {
											foreach ($res[$i] as $key => $value) {
											}
											echo '<option value="' . $res[$i]['id'] . '">' . $res[$i]['nome'] . '</option>';
										}
									} else {
										echo '<option value="">Cadastre os Adicionais</option>';
									}
									?>


								</select>
							</div>
						</div>



					</div>

					<div class="row">

						<div class="col-md-4">

							<div class="form-group">
								<label for="exampleInputEmail1">Valor</label>
								<input type="text" class="form-control" id="valor_do_item" name="valor" placeholder="Valor Se Houver">
							</div>
						</div>



						<div class="col-md-5">
							<div class="form-group">
								<label for="exampleInputEmail1">Limite de Seleção Itens</label>
								<input type="number" class="form-control" id="limite_itens" name="limite"
									placeholder="Selecionar até x Itens">
							</div>
						</div>



						<div class="col-md-3" style="margin-top: 24px">
							<button id="btn_itens" type="submit" class="btn btn-primary">Salvar</button>

						</div>
					</div>



					<input type="hidden" id="id_item" name="id">
					<input type="hidden" id="id_item_produto" name="id_item_produto">
					<input type="hidden" id="e_adicional" name="e_adicional">
					<input type="hidden" id="id_item_editar" name="id_item_editar">

				</form>

				<br>
				<small>
					<div id="mensagem-itens" align="center"></div>
				</small>


				<hr>
				<div id="listar-itens"></div>
			</div>


		</div>
	</div>
</div>





<script type="text/javascript">
	var pag = "<?= $pag ?>"
</script>
<script src="js/ajax.js"></script>

<script>
	$(document).ready(function() {

		$('.sel7').select2({
		
		});

	});
</script>

<script type="text/javascript">
	function carregarImg() {
		var target = document.getElementById('target');
		var file = document.querySelector("#foto").files[0];

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
	$("#form-saida").submit(function() {

		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: 'paginas/' + pag + "/saida.php",
			type: 'POST',
			data: formData,

			success: function(mensagem) {
				$('#mensagem-saida').text('');
				$('#mensagem-saida').removeClass()
				if (mensagem.trim() == "Salvo com Sucesso") {

					$('#btn-fechar-saida').click();
					buscar();

				} else {

					$('#mensagem-saida').addClass('text-danger')
					$('#mensagem-saida').text(mensagem)
				}


			},

			cache: false,
			contentType: false,
			processData: false,

		});

	});
</script>





<script type="text/javascript">
	$("#form-entrada").submit(function() {

		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: 'paginas/' + pag + "/entrada.php",
			type: 'POST',
			data: formData,

			success: function(mensagem) {
				$('#mensagem-entrada').text('');
				$('#mensagem-entrada').removeClass()
				if (mensagem.trim() == "Salvo com Sucesso") {

					$('#btn-fechar-entrada').click();
					buscar();

				} else {

					$('#mensagem-entrada').addClass('text-danger')
					$('#mensagem-entrada').text(mensagem)
				}


			},

			cache: false,
			contentType: false,
			processData: false,

		});

	});
</script>






<script type="text/javascript">
	$("#form-var").submit(function() {

		var id_var = $('#id_var').val()

		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: 'paginas/' + pag + "/inserir-variacoes.php",
			type: 'POST',
			data: formData,

			success: function(mensagem) {
				$('#mensagem-var').text('');
				$('#mensagem-var').removeClass()
				if (mensagem.trim() == "Salvo com Sucesso") {

					//$('#btn-fechar-var').click();
					listarVariacoes(id_var);
					limparCamposVar();


				} else {

					$('#mensagem-var').addClass('text-danger')
					$('#mensagem-var').text(mensagem)
				}


			},

			cache: false,
			contentType: false,
			processData: false,

		});

	});


	function limparCamposVar() {

		$('#nome_var').val('');
		$('#valor_var').val('');
		$('#sigla').val('');
		$('#descricao_var').val('');
		$('#id_it_var').val('');

	}




	function listarVariacoes(id) {
		$.ajax({
			url: 'paginas/' + pag + "/listar-variacoes.php",
			method: 'POST',
			data: {
				id
			},
			dataType: "html",

			success: function(result) {
				$("#listar-var").html(result);
				$('#mensagem-excluir-var').text('');
			}
		});
	}


	function excluirVar(id) {
		var id_var = $('#id_var').val()
		$.ajax({
			url: 'paginas/' + pag + "/excluir-variacoes.php",
			method: 'POST',
			data: {
				id
			},
			dataType: "text",

			success: function(mensagem) {
				if (mensagem.trim() == "Excluído com Sucesso") {
					listarVariacoes(id_var);
				} else {
					$('#mensagem-excluir-var').addClass('text-danger')
					$('#mensagem-excluir-var').text(mensagem)
				}

			},

		});
	}


	function ativarVar(id, acao) {
		var id_var = $('#id_var').val()
		$.ajax({
			url: 'paginas/' + pag + "/mudar-status-var.php",
			method: 'POST',
			data: {
				id,
				acao
			},
			dataType: "text",

			success: function(mensagem) {
				if (mensagem.trim() == "Alterado com Sucesso") {
					listarVariacoes(id_var);
				} else {
					$('#mensagem-excluir-var').addClass('text-danger')
					$('#mensagem-excluir-var').text(mensagem)
				}

			},

		});
	}
</script>


<script type="text/javascript">
	$("#form-grades").submit(function() {

		var id_var = $('#id_grades').val()

		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: 'paginas/' + pag + "/inserir-grades.php",
			type: 'POST',
			data: formData,

			success: function(mensagem) {
				$('#mensagem-grades').text('');
				$('#mensagem-grades').removeClass()
				if (mensagem.trim() == "Salvo com Sucesso") {

					//$('#btn-fechar-var').click();
					listarGrades(id_var);
					limparCamposGrades();

				} else {

					$('#mensagem-grades').addClass('text-danger')
					$('#mensagem-grades').text(mensagem)
				}


			},

			cache: false,
			contentType: false,
			processData: false,

		});

	});
</script>


<script type="text/javascript">
	function limparCamposGrades() {

		$('#texto').val('');
		$('#limite').val('');
		$('#nome_comprovante').val('');
		$('#id_grade_editar').val('');
		$('#btn_grade').text('Salvar');


	}



	function listarGrades(id) {
		$.ajax({
			url: 'paginas/' + pag + "/listar-grades.php",
			method: 'POST',
			data: {
				id
			},
			dataType: "html",

			success: function(result) {
				$("#listar-grades").html(result);
				$('#mensagem-excluir-grades').text('');
			}
		});
	}







	function excluirGrades(id) {
		var id_var = $('#id_grades').val()
		$.ajax({
			url: 'paginas/' + pag + "/excluir-grade.php",
			method: 'POST',
			data: {
				id
			},
			dataType: "text",

			success: function(mensagem) {
				if (mensagem.trim() == "Excluído com Sucesso") {
					listarGrades(id_var);
				} else {
					$('#mensagem-excluir-grades').addClass('text-danger')
					$('#mensagem-excluir-grades').text(mensagem)
				}

			},

		});
	}




	function ativarGrades(id, acao) {
		var id_var = $('#id_grades').val()
		$.ajax({
			url: 'paginas/' + pag + "/mudar-status-grade.php",
			method: 'POST',
			data: {
				id,
				acao
			},
			dataType: "text",

			success: function(mensagem) {
				if (mensagem.trim() == "Alterado com Sucesso") {
					listarGrades(id_var);
				} else {
					$('#mensagem-excluir-grades').addClass('text-danger')
					$('#mensagem-excluir-grades').text(mensagem)
				}

			},

		});
	}
</script>











<script type="text/javascript">
	$("#form-itens").submit(function() {

		var id_var = $('#id_item').val()

		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: 'paginas/' + pag + "/inserir-itens.php",
			type: 'POST',
			data: formData,

			success: function(mensagem) {
				$('#mensagem-itens').text('');
				$('#mensagem-itens').removeClass()
				if (mensagem.trim() == "Salvo com Sucesso") {

					//$('#btn-fechar-var').click();
					listarItens(id_var);
					limparCamposItens();

				} else {

					$('#mensagem-itens').addClass('text-danger')
					$('#mensagem-itens').text(mensagem)
				}


			},

			cache: false,
			contentType: false,
			processData: false,

		});

	});
</script>


<script type="text/javascript">
	function limparCamposItens() {

		$('#texto_item').val('');
		$('#limite_itens').val('');
		$('#valor_do_item').val('');


		$('#id_item_editar').val('');
		$('#btn_itens').text('Salvar');

	}



	function listarItens(id) {
		$.ajax({
			url: 'paginas/' + pag + "/listar-itens.php",
			method: 'POST',
			data: {
				id
			},
			dataType: "html",

			success: function(result) {
				$("#listar-itens").html(result);
				$('#mensagem-excluir-itens').text('');
			}
		});
	}







	function excluirItens(id) {
		var id_var = $('#id_item').val()
		$.ajax({
			url: 'paginas/' + pag + "/excluir-item.php",
			method: 'POST',
			data: {
				id
			},
			dataType: "text",

			success: function(mensagem) {
				if (mensagem.trim() == "Excluído com Sucesso") {
					listarItens(id_var);
				} else {
					$('#mensagem-excluir-itens').addClass('text-danger')
					$('#mensagem-excluir-itens').text(mensagem)
				}

			},

		});
	}




	function ativarItens(id, acao) {
		var id_var = $('#id_item').val()
		$.ajax({
			url: 'paginas/' + pag + "/mudar-status-itens.php",
			method: 'POST',
			data: {
				id,
				acao
			},
			dataType: "text",

			success: function(mensagem) {
				if (mensagem.trim() == "Alterado com Sucesso") {
					listarItens(id_var);
				} else {
					$('#mensagem-excluir-itens').addClass('text-danger')
					$('#mensagem-excluir-itens').text(mensagem)
				}

			},

		});
	}
</script>




<script type="text/javascript">
	$(document).ready(function() {
		$('.sel2').select2({
			dropdownParent: $('#modalForm')
		});

		$('.sel5').select2({
			dropdownParent: $('#modalItens')
		});
	});
</script>


<script type="text/javascript">
	function listarVarCat(id) {
		$.ajax({
			url: 'paginas/' + pag + "/listar_var_cat.php",
			method: 'POST',
			data: {
				id
			},
			dataType: "html",

			success: function(result) {
				$("#listar_var_cat").html(result);
			}
		});
	}
</script>


<script type="text/javascript">
	function alterarValor() {
		var adicional = $('#adicional_grade').val();
		var e_adicional = $('#e_adicional').val();

		if (e_adicional == 'Não') {
			return;
		}

		$.ajax({
			url: 'paginas/' + pag + "/listar_valor_adicional.php",
			method: 'POST',
			data: {
				adicional
			},
			dataType: "html",

			success: function(result) {
				//alert(result)
				$("#valor_do_item").val(result);
			}
		});

	}


	function buscar() {
		var busca = $('#busca').val();
		listar(busca)
	}
</script>



<script type="text/javascript">
	
$("#form_produtos").submit(function () {

    event.preventDefault();
    var formData = new FormData(this);

    $('#mensagem').text('Salvando...')
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



<script type="text/javascript">
	

function excluirProduto(id) {
    //$('#mensagem-excluir').text('Excluindo...')

    $('body').removeClass('timer-alert');
    Swal.fire({
        title: "Deseja Excluir?",
        text: "Você não conseguirá recuperá-lo novamente!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33', // Cor do botão de confirmação (vermelho)
        cancelButtonColor: '#3085d6', // Cor do botão de cancelamento (azul)
        confirmButtonText: "Sim, Excluir!",
        cancelButtonText: "Cancelar",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {


            $.ajax({
                url: 'paginas/' + pag + "/excluir.php",
                method: 'POST',
                data: { id },
                dataType: "html",

                success: function (mensagem) {
                    if (mensagem.trim() == "Excluído com Sucesso") {

                        // Ação de exclusão aqui
                        Swal.fire({
                            title: 'Excluido com Sucesso!',
                            text: 'Fecharei em 1 segundo.',
                            icon: "success",
                            timer: 1000
                        })
                        //excluido();
                        buscar();
                        limparCampos();


                    } else {
                        $('#mensagem-excluir').addClass('text-danger')
                        $('#mensagem-excluir').text(mensagem)
                    }
                }
            });

        }
    });


};
</script>


<script type="text/javascript">
			function gerarCodigo(){
				var codigo = $('#codigo').val();


				$.ajax({
					url: 'paginas/' + pag + "/gerar-codigo.php",
					method: 'POST',
					data: {codigo},
					dataType: "html",

					success:function(result){
						$("#listar-codigo").html(result);

					}
				});

			}
		</script>