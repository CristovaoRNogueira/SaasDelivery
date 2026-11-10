<?php 
@session_start();

$pag = 'marketing';

//verificar se ele tem a permissão de estar nessa página
if(@$pag == 'ocultar'){
    echo "<script>window.location='../index.php'</script>";
    exit();
}


?>

<div class="justify-content-between breadcrumb-header">
	<div class="left-content mt-2">
		<a class="btn ripple btn-primary text-white" onclick="inserir()" type="button"><i class="fe fe-plus me-1"></i> Nova Campanha</a>
	</div>

	<div class="">
		<a class="btn ripple btn-success text-white" onclick="$('#btn_rel').click()" type="button"><i class="fa fa-file-pdf-o me-1"></i> Relatório</a>
	</div>
</div>




<div class="bs-example widget-shadow" style="padding:15px" id="listar">



</div>


<!-- Aviso sobre o uso de disparos em massa (revitalizado) -->
<div class="alert mt-4 border-0 shadow-sm" style="background: #fff1e3">
    <div class="d-flex">
        <div class="me-3">
            <i class="fa fa-exclamation-triangle fa-2x text-warning"></i>
        </div>
        <div style="color:#525252">
            <h5 class="alert-heading">Atenção ao usar disparos em massa</h5>
            <p class="mb-0">O WhatsApp pode banir seu número se detectar envios automatizados em excesso. 
            O sistema realiza envios a cada 1 minuto para minimizar riscos, mas recomendamos:</p>
            
            <ul class="mt-2 mb-0">
                <li>Enviar para grupos menores de contatos</li>
                <li>Evitar mensagens idênticas para muitos destinatários</li>
                <li>Não realizar disparos com muita frequência</li>
                <li>Personalizar mensagens quando possível</li>
            </ul>
			<p class="mt-2">Deixamos dois campos para mensagens: mensagem principal e secundária. O sistema irá alternar entre as duas para evitar bainimentos e altere algumas palavras entre as duas mensagens.</p>

        </div>
    </div>
</div>







<!-- Modal Inserir-->
<div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title"><span id="titulo_inserir">Nova Campanha</span></h4>
				<button id="btn-fechar" aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><span class="text-white" aria-hidden="true">&times;</span></button>
			</div>
			<form id="form">
			<div class="modal-body">

				<div class="row bg-light">
					<div class="col-md-9">

						<!-- Seção do Nome da Campanha -->
					<div class="p-4 border-bottom bg-light">
						<div class="form-floating mb-0">
							<input type="text" class="form-control form-control-lg" id="titulo" name="titulo"
								placeholder="Nome da campanha" required>
							<label for="nomeCampanha">
								<i class="fe fe-tag me-1 text-primary"></i>
								Nome da Campanha
							</label>
							<div class="invalid-feedback">Por favor, informe um nome para a campanha.</div>
						</div>
					</div>
						
					</div>
					<div class="col-md-3" style="margin-top: 15px">	


								<label>Dispositivo Cobrança</label>
								<select class="form-select" name="dispositivo" id="dispositivo">							
								<option value="">Dispositivo Padrão</option>	
								<?php 
									$query = $pdo->query("SELECT * from dispositivos where status_api = 'conectado' and empresa = '$id_empresa' order by id asc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$linhas = @count($res);
									if($linhas > 0){
									for($i=0; $i<$linhas; $i++){

									$telefone = $res[$i]['telefone'];
									$tel = preg_replace('/\D/', '', $res[$i]['telefone']); // Remove tudo que não for número

									if (substr($tel, 0, 2) === '55') {
									    $tel = substr($tel, 2); // Remove DDI
									}

									if (strlen($tel) === 11) {
									    // Celular com 9 na frente
									    $tel = "(".substr($tel, 0, 2).") ".substr($tel, 2, 5)."-".substr($tel, 7, 4);
									} elseif (strlen($tel) === 10) {
									    // Fixo ou celular sem 9
									    $tel = "(".substr($tel, 0, 2).") ".substr($tel, 2, 4)."-".substr($tel, 6, 4);
									} else {
									    $tel = 'Número inválido';
									}

								 ?>
								  <option value="<?php echo $res[$i]['telefone'] ?>"><?php echo $tel ?></option>

								<?php } } ?>
									
								</select>	
						</div>
				</div>
			


					<!-- Navegação por Abas -->
					<ul class="nav nav-tabs nav-fill" id="campanhaTab" role="tablist">
						<li class="nav-item" role="presentation">
							<button class="nav-link active py-3" id="mensagem-tab" data-bs-toggle="tab"
								data-bs-target="#mensagem-conteudo" type="button" role="tab"
								aria-controls="mensagem-conteudo" aria-selected="true">
								<i class="bi bi-chat-text me-2"></i>Mensagem Principal
							</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link py-3" id="secundaria-tab" data-bs-toggle="tab"
								data-bs-target="#secundaria-conteudo" type="button" role="tab"
								aria-controls="secundaria-conteudo" aria-selected="false">
								<i class="bi bi-chat-square-text me-2"></i>Mensagem Secundária
							</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link py-3" id="anexos-tab" data-bs-toggle="tab"
								data-bs-target="#anexos-conteudo" type="button" role="tab"
								aria-controls="anexos-conteudo" aria-selected="false">
								<i class="bi bi-paperclip me-2"></i>Anexos
							</button>
						</li>
					</ul>


					<!-- Conteúdo das Abas -->
					<div class="tab-content" id="campanhaTabContent">
						<!-- Aba da Mensagem Principal -->
						<div class="tab-pane fade show active" id="mensagem-conteudo" role="tabpanel"
							aria-labelledby="mensagem-tab">
							<div class="row p-3 mt-5">

					<div class="col-md-6">
					 <div class="d-flex align-items-center justify-content-between mb-2">
						<span class="text-muted">💬 Mensagem:</span>
						<div class="btn-group btn-group-sm me-2" style="box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
							<button type="button" class="btn btn-light border" onclick="formatarTexto('bold', 'msg')" title="Negrito" style="transition: background-color 0.2s ease;">
							<i class="fa fa-bold"></i>
							</button>
							<button type="button" class="btn btn-light border" onclick="formatarTexto('italic', 'msg')" title="Itálico" style="transition: background-color 0.2s ease;">
							<i class="fa fa-italic"></i>
							</button>
							<button type="button" class="btn btn-light border" onclick="formatarTexto('strike', 'msg')" title="Tachado" style="transition: background-color 0.2s ease;">
							<i class="fa fa-strikethrough"></i>
							</button>
							<button type="button" class="btn btn-light border" onclick="formatarTexto('mono', 'msg')" title="Monospace" style="transition: background-color 0.2s ease;">
							<i class="fa fa-code"></i>
							</button>
							<button type="button" class="btn btn-light border" onclick="inserirEmoji('{cliente}', 'msg')" title="Nome Cliente" style="transition: background-color 0.2s ease;">
							<i class="fa fa-user"></i>
							</button>
						</div>
						<div class="btn-group">
							
								<button type="button" class="btn btn-outline-success btn-sm" onclick="inserirEmoji('👋', 'msg')" title="Olá">👋</button>
								<button type="button" class="btn btn-outline-success btn-sm" onclick="inserirEmoji('😊', 'msg')" title="Sorriso">😊</button>
								<button type="button" class="btn btn-outline-success btn-sm" onclick="inserirEmoji('👍', 'msg')" title="Like">👍</button>
								<button type="button" class="btn btn-outline-success btn-sm" onclick="inserirEmoji('🔥', 'msg')" title="Fogo">🔥</button>
							<button type="button" class="btn btn-outline-success btn-sm" onclick="inserirEmoji('✅', 'msg')" title="Like">✅</button>
							<button type="button" class="btn btn-outline-success btn-sm" onclick="inserirEmoji('✔️', 'msg')" title="Like">✔️</button>
							<button type="button" class="btn btn-outline-success btn-sm" onclick="inserirEmoji('🎯', 'msg')" title="Alvo">🎯</button>
						</div>
					</div>
						<textarea class="form-control" id="msg" name="msg" rows="13" placeholder="Digite a mensagem" onblur="$('#msg2').val($('#msg').val()), atualizarPreview2()" onclick="atualizarPreview2()"
							style="background-image: url('images/whatsapp.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: 0.95;"></textarea>
					</div>
					<div class="col-md-6">
						<div class="card h-100">
						<div class="card-header bg-light py-2">
									<span class="text-muted fw-bold">🔍 Pré-visualização</span>
								</div>
						<div class="border p-3 bg-light ounded-3 h-100" id="preview" style="min-height: 50px; white-space: pre-wrap; font-weight: normal; background-image: url('images/whatsapp.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: 0.95;"></div>
					</div>
					</div>
				</div>
						</div>

						<!-- Aba da Mensagem Secundária -->
						<div class="tab-pane fade" id="secundaria-conteudo" role="tabpanel"
							aria-labelledby="secundaria-tab">
							<div class="row p-3 mt-5">

					<div class="col-md-6">
					 <div class="d-flex align-items-center justify-content-between mb-2">
						<span class="text-muted">💬 Mensagem Sec:</span>
						 <!-- Grupo de botões de formatação de texto -->
						<div class="btn-group btn-group-sm me-2" style="box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
							<button type="button" class="btn btn-light border" onclick="formatarTexto('bold', 'msg2')" title="Negrito" style="transition: background-color 0.2s ease;">
							<i class="fa fa-bold"></i>
							</button>
							<button type="button" class="btn btn-light border" onclick="formatarTexto('italic', 'msg2')" title="Itálico" style="transition: background-color 0.2s ease;">
							<i class="fa fa-italic"></i>
							</button>
							<button type="button" class="btn btn-light border" onclick="formatarTexto('strike', 'msg2')" title="Tachado" style="transition: background-color 0.2s ease;">
							<i class="fa fa-strikethrough"></i>
							</button>
							<button type="button" class="btn btn-light border" onclick="formatarTexto('mono', 'msg2')" title="Monospace" style="transition: background-color 0.2s ease;">
							<i class="fa fa-code"></i>
							</button>
						</div>
						<div class="btn-group">
							
								<button type="button" class="btn btn-outline-success btn-sm" onclick="inserirEmoji('👋', 'msg2')" title="Olá">👋</button>
								<button type="button" class="btn btn-outline-success btn-sm" onclick="inserirEmoji('😊', 'msg2')" title="Sorriso">😊</button>
								<button type="button" class="btn btn-outline-success btn-sm" onclick="inserirEmoji('👍', 'msg2')" title="Like">👍</button>
								<button type="button" class="btn btn-outline-success btn-sm" onclick="inserirEmoji('🔥', 'msg2')" title="Fogo">🔥</button>
							<button type="button" class="btn btn-outline-success btn-sm" onclick="inserirEmoji('✅', 'msg2')" title="Like">✅</button>
							<button type="button" class="btn btn-outline-success btn-sm" onclick="inserirEmoji('✔️', 'msg2')" title="Like">✔️</button>
							<button type="button" class="btn btn-outline-success btn-sm" onclick="inserirEmoji('🎯', 'msg2')" title="Alvo">🎯</button>
						</div>
					</div>
						<textarea class="form-control" id="msg2" name="msg2" rows="13" placeholder="Digite a mensagem"
							style="background-image: url('images/whatsapp.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: 0.95;"></textarea>
					</div>
					<div class="col-md-6">
						<div class="card h-100">
						<div class="card-header bg-light py-2">
									<span class="text-muted fw-bold">🔍 Pré-visualização</span>
								</div>
						<div class="border p-3 bg-light ounded-3 h-100" id="preview2" style="min-height: 50px; white-space: pre-wrap; font-weight: normal; background-image: url('images/whatsapp.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: 0.95;"></div>
					</div>
					</div>
				</div>
						</div>

						<!-- Aba dos Anexos -->
						<div class="tab-pane fade" id="anexos-conteudo" role="tabpanel" aria-labelledby="anexos-tab">
							<div class="p-4">
								<div class="row">
									<!-- Imagem -->
									<div class="col-md-4 mb-4">
										<div class="card border-0 shadow-sm h-100">
											<div class="card-header bg-white py-3">
												<h6 class="mb-0 text-primary">
													<i class="bi bi-image me-2"></i>Imagem
												</h6>
											</div>
											<div class="card-body text-center">
												<div class="mb-3 anexo-preview">
													<img src="images/marketing/sem-foto.png" class="img-fluid rounded"
														id="target" style="max-height: 150px; width: auto;">
												</div>
												<div class="input-group">
													<input type="file" class="form-control" id="foto" name="foto"
														accept="image/*" onchange="carregarImg();">
													<button class="btn btn-outline-danger" type="button"
														onclick="limparImagem()">
														<i class="bi bi-x-lg"></i>
													</button>
												</div>
												<small class="form-text text-muted mt-2">
													Formato recomendado:  PNG ou JPG (máx. 2MB)
												</small>
											</div>
										</div>
									</div>

									<!-- Áudio -->
									<div class="col-md-4 mb-4">
										<div class="card border-0 shadow-sm h-100">
											<div class="card-header bg-white py-3">
												<h6 class="mb-0 text-primary">
													<i class="bi bi-music-note-beamed me-2"></i>Áudio
												</h6>
											</div>
											<div class="card-body text-center d-flex flex-column">
												<div
													class="flex-grow-1 d-flex align-items-center justify-content-center mb-3">
													<div id="audio-preview">
														<i class="bi bi-file-earmark-music display-4 text-muted"></i>
														<p class="text-muted" id="audio-info">Áudio
														</p>
													</div>
												</div>
												<div class="input-group mt-auto">
													<input type="file" class="form-control" id="audio" name="audio"
														accept="audio/*" onchange="atualizarInfoAudio();">
													<button class="btn btn-outline-danger" type="button"
														onclick="limparAudio()">
														<i class="bi bi-x-lg"></i>
													</button>
												</div>
												<small class="form-text text-muted mt-2">
													Formato Recomendados: OGG ou MP3 (máx. 5MB)
												</small>
											</div>
										</div>
									</div>

									<!-- Documento -->
									<div class="col-md-4 mb-4">
										<div class="card border-0 shadow-sm h-100">
											<div class="card-header bg-white py-3">
												<h6 class="mb-0 text-primary">
													<i class="bi bi-file-earmark-pdf me-2"></i>Documento
												</h6>
											</div>
											<div class="card-body text-center d-flex flex-column">
												<div
													class="flex-grow-1 d-flex align-items-center justify-content-center mb-3">
													<img src="images/marketing/sem-foto.png" class="img-fluid" id="target_documento" style="max-height: 150px; width: auto;">
												</div>
												<div class="input-group mt-auto">
													<input type="file" class="form-control" id="documento" name="documento" accept=".pdf,.zip,.rar" onchange="carregarImgArquivo();">
													<button class="btn btn-outline-danger" type="button" onclick="limparDocumento()">
														<i class="bi bi-x-lg"></i>
													</button>
												</div>
												<small class="form-text text-muted mt-2">
													Formatos aceitos: PDF, ZIP, RAR (máx. 10MB)
												</small>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>



					<div class="alert alert-dismissible fade show m-3" role="alert" id="mensagem-form" style="display: none;">
						<i class="bi bi-info-circle me-2"></i>
						<span id="texto-mensagem"></span>
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
					</div>




						<input type="hidden" name="id" id="id">
					<br>
					<small><div id="mensagem" align="center"></div></small>
				</div>
				<div class="modal-footer">      
					<button type="submit" id="btn_salvar" class="btn btn-primary"><i class="fe fe-check-circle me-2"></i>Salvar</button>
					<button class="btn btn-primary" type="button" id="btn_carregando" style="display: none;">
						<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Carregando...
					</button>
				</div>
			</form>
		</div>
	</div>
</div>





<!-- Modal Visualizar Detalhes -->
<div class="modal fade" id="modalDados" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title"><span id="nome_dados"></span></h4>
				<button id="btn-fechar-perfil" aria-label="Close" class="btn-close" data-bs-dismiss="modal"
					type="button"><span class="text-white" aria-hidden="true">&times;</span></button>
			</div>

			<div class="modal-body">
				<div class="row" style="border-bottom: 1px solid #cac7c7;">
					<div class="col-md-6">
						<span><b>Criada Em: </b></span>
						<span id="data_dados"></span>
					</div>
					<div class="col-md-6">
						<span><b>Último Envio: </b></span>
						<span id="data_envio_dados"></span>
					</div>
				</div>
				<div class="row py-2" style="border-bottom: 1px solid #cac7c7;">
					<div class="col-md-6">
						<span><b>Total de Envios: </b></span>
						<span id="envios_dados" class="badge bg-success"></span>
					</div>
				</div>

				<!-- Demonstração da Mensagem -->
				<div class="mt-4" align="center">
					<!-- Demonstração das Mensagens -->
				<h5 class="text-center mb-3"><u>Pré-visualização das Mensagens</u></h5>


				<div class="row">
					<!-- Mensagem Principal -->
					<div class="col-md-6 mb-3">
						<div class="card h-100">
							<div class="card-header bg-light py-1">
								<h6 class="mb-0 text-muted">Mensagem Principal</h6>
							</div>
							<div class="card-body p-3"style="text-align: left; background-image: url('images/whatsapp.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: 0.95;">
								<div class="message-text">
										<span id="mensagem_dados"></span>
									</div>
							</div>
						</div>
					</div>

					<!-- Mensagem Secundária -->
					<div class="col-md-6 mb-3">
						<div class="card h-100">
							<div class="card-header bg-light py-1">
								<h6 class="mb-0 text-muted">Mensagem Secundária</h6>
							</div>
							<div class="card-body p-3" style="text-align: left; background-image: url('images/whatsapp.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: 0.95;">
								<div class="message-text">
										<span id="mensagem2_dados"></span>
									</div>
							</div>
						</div>
					</div>
				</div>


				

					<div class="message-preview"
						style="max-width:400px; border:1px solid #acacad; margin:0 auto; text-align: left; padding:15px; border-radius:10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">

						<div class="message-body">

							<!-- Arquivos anexados -->
							<div class="attachments">
								<div id="target_dados_div" class="mb-3">
									<img src="" id="target_dados" class="img-fluid rounded" style="max-width:100%">
								</div>

								<div id="audio_dados_div" class="mb-3">
									<audio controls class="w-100" style="height:40px;" id="audio_dados">
										<source src="" type="audio/mp3">
									</audio>
								</div>

								<div id="target_documento_dados_div">
									<div class="d-flex align-items-center">
										<img src="" id="target_documento_dados" width="40px">
										<span class="ms-2">Documento anexado</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>






<!-- Modal Disparar Campanha -->
<div class="modal fade" id="modalEntrada" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h4 class="modal-title"><span id="nome_entrada"></span> - <span id="total_clientes"
						class="badge bg-light text-dark"></span> Clientes</h4>
				<button id="btn-fechar-entrada" aria-label="Close" class="btn-close" data-bs-dismiss="modal"
					type="button"><span class="text-white" aria-hidden="true">&times;</span></button>
			</div>

			<div class="modal-body">
				<form id="form-disparar">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Selecionar Destinatários</label>
								<select name="clientes" id="clientes" class="sel_disparos"
									onchange="selecionarClientes()" style="width:100%">
									<option value="Teste">Teste (Apenas Administrador)</option>
									<option value="Todos">Todos os Clientes</option>
									<option value="Clientes Semana">Clientes Cadastrados na Última Semana</option>
									<option value="Clientes Mês">Clientes Cadastrados no Último Mês</option>
									<option value="1">1 Cartão</option>
									<option value="2">2 Cartões</option>
									<option value="3">3 Cartões</option>
									<option value="4">4 Cartões</option>
									<option value="5">5 Cartões</option>
									
									
									<?php 
									$query = $pdo->query("SELECT * FROM grupos_disparos where empresa = '$id_empresa' order by id desc");
										$res = $query->fetchAll(PDO::FETCH_ASSOC);
										for($i=0; $i < @count($res); $i++){
											foreach ($res[$i] as $key => $value){}

											echo '<option value="'.$res[$i]['id'].'">'.$res[$i]['nome'].'</option>';

										}
								 ?>

								</select>
							</div>
						</div>
						<div class="col-md-3">
							<label>Programar Disparo para</label>
							<input type="date" name="data_disparo" id="data_disparo" value="<?php echo date('Y-m-d'); ?>" class="form-control" required>
							
						</div>
						<div class="col-md-3">
										<div class="form-group">
											<label class="text-muted">Horário do Disparo
												<div class="icones_mobile dropdown" style="display: inline-block;">
													<a title="Clique para ver as opções das tags" href="#" aria-expanded="false"
														aria-haspopup="true" data-bs-toggle="dropdown" class="dropdown">
														<i class="fa fa-info-circle text-info" style="font-size: 14px;"></i>
													</a>
													<div class="dropdown-menu tx-13">
														<div data-bs-toggle="tooltip" data-bs-placement="top" class="dropdown-item-text " style="width: 400px;">
															<p class="mt-1">Escolha uma Hora de Inicio e o sistema vai começar a disparar as mensagens automaticamente nesse horario. Os minutos o sistema vai alternar entre 00 e 59 para evitar uma sobrecarga no Servidor<br>
															</p>
														</div>
													</div>
												</div>
											</label>
											<select class="form-select" id="hora_disparo" name="hora_disparo" required>
												<option value="agora">Imediatamente</option>
												<?php
												for ($i = 0; $i < 24; $i++) {
													$hora = str_pad($i, 2, "0", STR_PAD_LEFT);
													$selected = (@$res_edit['hora_disparo'] == $hora . ':00') ? 'selected' : ($hora == '09' ? 'selected' : '');
													echo "<option value='{$hora}:00' {$selected}>{$hora}:00</option>";
												}
												?>
											</select>
									</div>
								</div>
					</div>

					<div class="row">
						<div class="col-md-5 mb-2">
							<label>Arquivo Excel .Xls <small>(Cabelalho nome e telefone)</small> <a href="images/marketing/modelo_excel.xlsx" target="_blank" title="Baixar Modelo"><i class="fas fa-file-excel" style="color: green; font-size: 15px"></i></a></label>
							<input type="file" class="form-control" id="arquivo_excel" name="arquivo_excel" onchange="carregarImgArquivoExcel()">
						</div>
						<div class="col-md-1" style="margin-top: 20px">
							<img width="80px" id="target_arquivo_excel">
						</div>

						<div class="col-md-5 mb-2">
							<label>Arquivo Texto .txt <small>(Separar por virgula ;)</small> <a href="images/marketing/modelo_txt.txt" target="_blank" title="Baixar Modelo"><i class="fas fa-file" style="color: gray; font-size: 15px"></i></a></label>
							<input type="file" class="form-control" id="arquivo_texto" name="arquivo_texto" onchange="carregarImgArquivoTexto()">
						</div>
						<div class="col-md-1" style="margin-top: 20px">
							<img width="80px" id="target_arquivo_texto">
						</div>

						
					</div>


					<div class="row">

						<div class="col-md-3  mb-2">
							<label>Frequência</label>
							<select name="frequencia" id="frequencia" class="form-select">
							<option value="0">Nenhuma</option>
								<?php
								$query = $pdo->query("SELECT * from frequencias where empresa = '$id_empresa' order by id asc");
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


						<div class="col-md-2 mb-2">
							<label>Quantidade</label>
							<input type="number" name="quantidade" id="quantidade" placeholder="Vezes" class="form-select">
						</div>


						<div class="col-md-2 mb-2">
							<label>Intervalo Horas</label>
							<input type="number" value="1" name="intervalo_horas" id="intervalo_horas" placeholder="Horas" class="form-select">
						</div>

						
						<div class="col-md-2" style="margin-top: 25px">
							<button id="btn_disparar" type="submit" class="btn btn-primary"><i
									class="fe fe-send me-2"></i>Disparar</button>
							<button class="btn btn-primary" type="button" id="btn_carregando_disparo"
								style="display:none">
								<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
								Processando...
							</button>
						</div>
					</div>

					<input type="hidden" id="id_entrada" name="id">
				</form>

				<div id="mensagem-entrada" class="alert mt-3" style="display:none;"></div>

				<!-- Pré-visualização da mensagem -->
				<div class="mt-4 border-top pt-3">
					<h5 class="text-center mb-3"><u>Pré-visualização da Mensagem</u></h5>

					<div class="row">
					<!-- Mensagem Principal -->
					<div class="col-md-6 mb-3">
						<div class="card h-100">
							<div class="card-header bg-light py-1">
								<h6 class="mb-0 text-muted">Mensagem Principal</h6>
							</div>
							<div class="card-body p-3"style="text-align: left; background-image: url('images/whatsapp.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: 0.95;">
								<div class="message-text">
										<span id="mensagem_disparar"></span>
									</div>
							</div>
						</div>
					</div>

					<!-- Mensagem Secundária -->
					<div class="col-md-6 mb-3">
						<div class="card h-100">
							<div class="card-header bg-light py-1">
								<h6 class="mb-0 text-muted">Mensagem Secundária</h6>
							</div>
							<div class="card-body p-3" style="text-align: left; background-image: url('images/whatsapp.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: 0.95;">
								<div class="message-text">
										<span id="mensagem2_disparar"></span>
									</div>
							</div>
						</div>
					</div>
				</div>

					<div class="message-preview"
						style="max-width:400px; border:1px solid #acacad; margin:0 auto; text-align: left; padding:15px; border-radius:10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">


						<div class="message-body">
							<!-- Anexos -->
							<div class="attachments">
								<div id="target_disparar_div" class="mb-3 text-center" style="display:none;">
									<img src="" id="target_disparar" class="img-fluid rounded" style="max-width:100%">
								</div>

								<div id="audio_disparar_div" class="mb-3" style="display:none;">
									<audio controls class="w-100" style="height:40px;" id="audio_disparar">
										<source src="" type="audio/mp3">
									</audio>
								</div>

								<div id="target_documento_disparar_div" style="display:none;">
									<div class="d-flex align-items-center">
										<img src="" id="target_documento_disparar" width="40px">
										<span class="ms-2">Documento anexado</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">var pag = "<?= $pag ?>"</script>
<script src="js/ajax.js"></script>


<script type="text/javascript">
	$(document).ready(function() {
		$('#area_registro').hide();

    $('.sel2').select2({
    	dropdownParent: $('#modalForm')
    });

    $('.sel_disparos').select2({
    	dropdownParent: $('#modalEntrada')
    });


	// Inicializa as pré-visualizações
    atualizarPreview();
    atualizarPreview2();
    
    // Adiciona listeners para os textareas
    $('#msg').on('input', atualizarPreview);
    $('#msg2').on('input', atualizarPreview2);
});
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
	

$("#form-saida").submit(function () {

    event.preventDefault();
    var formData = new FormData(this);

    $.ajax({
        url: 'paginas/' + pag + "/saida.php",
        type: 'POST',
        data: formData,

        success: function (mensagem) {
            $('#mensagem-saida').text('');
            $('#mensagem-saida').removeClass()
            if (mensagem.trim() == "Salvo com Sucesso") {

                $('#btn-fechar-saida').click();
                listar();          

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
	

$("#form-disparar").submit(function () {		

    event.preventDefault();
    var formData = new FormData(this);

	$('#btn_carregando_disparo').show();
	$('#btn_disparar').hide();

	

    var ultimo_registro = $('#ultimo_registro').val();
	
	var cli = $('#clientes').val();
	if(cli == "Todos"){
		if(ultimo_registro == ""){			
			$('#mensagem-entrada').text('Preencha o Registro inicial dos disparos!!!');
			//$('#btn_disparar').show();
			return;
		}
	}

    $.ajax({
        url: 'paginas/' + pag + "/disparar.php",
        type: 'POST',
        data: formData,

        success: function (mensagem) {
            if (mensagem.trim() == "Salvo com Sucesso") {
				alertsucesso('Disparo agendado com sucesso!');
				$('#btn-fechar-entrada').click();
                listar(); 
            } else {
				alertErro(mensagem);
            }

            $('#btn_disparar').show();
			$('#btn_carregando_disparo').hide();
        },

        cache: false,
        contentType: false,
        processData: false,

    });

});
</script>


<script type="text/javascript">
	function selecionarClientes(){
		var cli = $('#clientes').val();

		if(cli == "Todos"){
			$('#area_registro').show();
		}else{
			$('#area_registro').hide();
		}

		  $.ajax({
        url: 'paginas/' + pag + "/listar_clientes.php",
        method: 'POST',
        data: {cli},
        dataType: "text",

        success: function (mensagem) {               
           $('#total_clientes').text(mensagem);
        },      

    });
	}
</script>


<script type="text/javascript">
	function pararDisparo(id){
		
	$.ajax({
        url: 'paginas/' + pag + "/api_parar_disparos.php",
        method: 'POST',
        data: {id},
        dataType: "text",

        success: function (mensagem) {               
          listar(); 
        },      

    });
	}
</script>





		<script type="text/javascript">
			function carregarImgArquivo() {
				var target = document.getElementById('target_documento');
				var file = document.querySelector("#documento").files[0];

				var arquivo = file['name'];
				resultado = arquivo.split(".", 2);

				if(resultado[1] === 'pdf'){
					$('#target_documento').attr('src', "images/marketing/pdf.png");
					return;
				}

				if(resultado[1] === 'rar' || resultado[1] === 'zip'){
					$('#target_documento').attr('src', "images/rar.png");
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
	 // Formata o texto no textarea especificado (msg ou msg2)
        function formatarTexto(tipo, textareaId) {
            let textarea = document.getElementById(textareaId);
            let inicio = textarea.selectionStart;
            let fim = textarea.selectionEnd;
            let texto = textarea.value;

            let marcador;
            switch (tipo) {
                case 'bold':
                    marcador = '*';
                    break;
                case 'italic':
                    marcador = '_';
                    break;
                case 'strike':
                    marcador = '~';
                    break;
                case 'mono':
                    marcador = '`';
                    break;
            }

            let selecionado = texto.substring(inicio, fim);
            let novoTexto;
            let novaPosicaoCursor;

            if (selecionado.length > 0) {
                // Se há texto selecionado
                if (tipo === 'mono') {
                    novoTexto = texto.substring(0, inicio) + '```' + selecionado + '```' + texto.substring(fim);
                    novaPosicaoCursor = fim + 6; // 3 caracteres no início + 3 no fim
                } else {
                    novoTexto = texto.substring(0, inicio) + marcador + selecionado + marcador + texto.substring(fim);
                    novaPosicaoCursor = fim + 2; // 1 caractere no início + 1 no fim
                }
            } else {
                // Se não há texto selecionado
                if (tipo === 'mono') {
                    novoTexto = texto.substring(0, inicio) + '```' + '```' + texto.substring(inicio);
                    novaPosicaoCursor = inicio + 3;
                } else {
                    novoTexto = texto.substring(0, inicio) + marcador + marcador + texto.substring(inicio);
                    novaPosicaoCursor = inicio + 1;
                }
            }

            textarea.value = novoTexto;
            textarea.selectionStart = novaPosicaoCursor;
            textarea.selectionEnd = novaPosicaoCursor;
            textarea.focus();

            // Atualiza a pré-visualização correspondente
            if (textareaId === 'msg') {
                atualizarPreview();
            } else if (textareaId === 'msg2') {
                atualizarPreview2();
            }
        }

	 // Insere emoji no textarea especificado
        function inserirEmoji(emoji, textareaId) {
            let textarea = document.getElementById(textareaId);
            let posicaoAtual = textarea.selectionStart;
            let texto = textarea.value;

            // Insere o emoji na posição atual do cursor
            textarea.value = texto.slice(0, posicaoAtual) + emoji + texto.slice(posicaoAtual);

            // Move o cursor para depois do emoji
            let novaPosicao = posicaoAtual + emoji.length;
            textarea.selectionStart = novaPosicao;
            textarea.selectionEnd = novaPosicao;

            textarea.focus();

            // Atualiza a pré-visualização correspondente
            if (textareaId === 'msg') {
                atualizarPreview();
            } else if (textareaId === 'msg2') {
                atualizarPreview2();
            }
        }

	// Atualiza a pré-visualização da mensagem
	function atualizarPreview() {
		let mensagem = document.getElementById("msg").value;
		let preview = document.getElementById("preview");

		// Aplica as formatações do WhatsApp
		let mensagemFormatada = mensagem
			.replace(/\*(.*?)\*/g, '<strong>$1</strong>') // Negrito
			.replace(/_(.*?)_/g, '<em>$1</em>')          // Itálico
			.replace(/~(.*?)~/g, '<del>$1</del>')        // Tachado
			.replace(/```(.*?)```/g, '<code>$1</code>')  // Monospace
			.replace(/\n/g, '<br>');                     // Quebras de linha

		preview.innerHTML = mensagemFormatada || '...';
	}

	// Atualiza a pré-visualização da mensagem
	function atualizarPreview2() {
		let mensagem = document.getElementById("msg2").value;
		let preview = document.getElementById("preview2");

		// Aplica as formatações do WhatsApp
		let mensagemFormatada = mensagem
			.replace(/\*(.*?)\*/g, '<strong>$1</strong>') // Negrito
			.replace(/_(.*?)_/g, '<em>$1</em>')          // Itálico
			.replace(/~(.*?)~/g, '<del>$1</del>')        // Tachado
			.replace(/```(.*?)```/g, '<code>$1</code>')  // Monospace
			.replace(/\n/g, '<br>');                     // Quebras de linha

		preview.innerHTML = mensagemFormatada || '...';
	}

	// Remove o event listener anterior para evitar duplicação
	document.getElementById("msg").removeEventListener("input", atualizarPreview);
	document.getElementById("msg2").removeEventListener("input", atualizarPreview2);
	// Adiciona o novo event listener
	document.getElementById("msg").addEventListener("input", atualizarPreview);
	document.getElementById("msg2").addEventListener("input", atualizarPreview2);

	// Inicializa a pré-visualização
	atualizarPreview();
	atualizarPreview2();
</script>


<script type="text/javascript">
 // Formata texto conforme regras do WhatsApp
        function formatarMensagemWhatsApp(mensagem) {
            if (!mensagem) return '';

            return mensagem
                .replace(/\*(.*?)\*/g, '<strong>$1</strong>') // Negrito
                .replace(/_(.*?)_/g, '<em>$1</em>')          // Itálico
                .replace(/~(.*?)~/g, '<del>$1</del>')        // Tachado
                .replace(/```(.*?)```/g, '<code>$1</code>')  // Monospace
                .replace(/\n/g, '<br>');                     // Quebras de linha
        }

		// Formata mensagem para exibição nos modais
        function formatarMensagemModal(mensagem) {
            return formatarMensagemWhatsApp(mensagem);
        }

</script>

<script>
	  // Atualiza a pré-visualização da mensagem principal
        function atualizarPreview() {
            let mensagem = document.getElementById("msg").value;
            let preview = document.getElementById("preview");

            // Aplica as formatações do WhatsApp
            let mensagemFormatada = formatarMensagemWhatsApp(mensagem);
            preview.innerHTML = mensagemFormatada || '<span class="text-muted">Pré-visualização da mensagem principal...</span>';
        }

        // Atualiza a pré-visualização da mensagem secundária
        function atualizarPreview2() {
            let mensagem = document.getElementById("msg2").value;
            let preview = document.getElementById("preview2");

            // Aplica as formatações do WhatsApp
            let mensagemFormatada = formatarMensagemWhatsApp(mensagem);
            preview.innerHTML = mensagemFormatada || '<span class="text-muted">Pré-visualização da mensagem secundária...</span>';
        }
</script>


<script>
	
/**
 * Função para limpar a imagem selecionada e restaurar a imagem padrão
 */
function limparImagem() {
    // Redefine a imagem para a padrão "sem-foto"
    document.getElementById('target').src = 'images/marketing/sem-foto.png';
    
    // Limpa o input de arquivo
    document.getElementById('foto').value = '';
}


/**
 * Função para limpar o áudio selecionado
 */
function limparAudio() {
    // Limpa o input de arquivo
    document.getElementById('audio').value = '';
    
    // Redefine a informação de áudio para o padrão
    document.getElementById('audio-info').textContent = 'Áudio';
}

/**
 * Função para limpar o documento selecionado
 */
function limparDocumento() {
    // Redefine a imagem para a padrão "sem-foto"
    document.getElementById('target_documento').src = 'images/marketing/sem-foto.png';
    
    // Limpa o input de arquivo
    document.getElementById('documento').value = '';
}
</script>

<script type="text/javascript">var pag = "<?= $pag ?>"</script>



<script type="text/javascript">
			function carregarImgArquivoTexto() {
				var target = document.getElementById('target_arquivo_texto');
				var file = document.querySelector("#arquivo_texto").files[0];

				var arquivo = file['name'];
				resultado = arquivo.split(".", 2);

				if(resultado[1] === 'txt'){
					$('#target_arquivo_texto').attr('src', "images/marketing/txt.png");
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
			function carregarImgArquivoExcel() {
				var target = document.getElementById('target_arquivo_excel');
				var file = document.querySelector("#arquivo_excel").files[0];

				var arquivo = file['name'];
				resultado = arquivo.split(".", 2);

				if (resultado[1] === 'xlsx' || resultado[1] === 'xlsm' || resultado[1] === 'xls') {
					$('#target_arquivo_excel').attr('src', "images/marketing/excel.png");
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
	function buscar(){
		var filtro_busca = $('#filtro_busca').val();
		listar(filtro_busca)
	}
</script>