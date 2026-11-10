<?php 
$pag = 'tarefas';
$itens_pag = 10;

if(@$tarefas == 'ocultar'){
	echo "<script>window.location='index'</script>";
	exit();
}

$checked_atrasadas = '';
$checked_agendadas = '';
$checked_hoje = '';

if(@$_POST['pago'] == "Atrasadas"){
	$checked_atrasadas = 'checked';
}else if(@$_POST['pago'] == "Agendada"){
	$checked_agendadas = 'checked';
}if(@$_POST['pago'] == "Hoje"){
	$checked_hoje = 'checked';
}

$filtro_tarefas = @$_POST['pago'];


// pegar a pagina atual
if(@$_POST['pagina'] == ""){
    @$_POST['pagina'] = 0;
}
$pagina = intval(@$_POST['pagina']);
$limite = $pagina * $itens_pag;

$numero_pagina = $pagina + 1;

if($pagina > 0){
	$pag_anterior = $pagina - 1;
	$pag_inativa_ant = '';
}else{
	$pag_anterior = $pagina;
	$pag_inativa_ant = 'desabilitar_botao';
}

$pag_proxima = $pagina + 1;


if($dataInicial == ""){
	$dataInicial = $data_hoje;
}

if($dataFinal == ""){
	$dataFinal = $data_hoje;
}

$usuario = @$_POST['usuario'];
if($usuario == "" ){
	$usuario = $id_usuario;
}

$query = $pdo->query("SELECT * from tarefas where usuario = '$usuario' and status = 'Agendada' and data <= curDate() and hora < curTime() order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$tarefas_atrasadas = @count($res);

$query = $pdo->query("SELECT * from tarefas where usuario = '$usuario' and status = 'Agendada' order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_tarefas = @count($res);


$query = $pdo->query("SELECT * from tarefas where usuario = '$usuario' and status = 'Agendada' and data = '$data_hoje' order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$agendadas_hoje = @count($res);

//totalizar páginas
if($filtro_tarefas == 'Atrasadas'){
	$query2 = $pdo->query("SELECT * from tarefas where usuario = '$usuario' and status = 'Agendada' and data <= curDate() and hora < curTime() order by id desc");
}else if($filtro_tarefas == 'Agendada'){
	$query2 = $pdo->query("SELECT * from tarefas where usuario = '$usuario' and status = 'Agendada' order by id desc");
}else if($filtro_tarefas == 'Hoje'){
	$query2 = $pdo->query("SELECT * from tarefas where usuario = '$usuario' and status = 'Agendada' and data = '$data_hoje' order by id desc");
}else{
	$query2 = $pdo->query("SELECT * from tarefas where data >= '$dataInicial' and data <= '$dataFinal' and usuario = '$usuario' order by id desc");
}

$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$linhas2 = @count($res2);

$num_paginas = ceil($linhas2/$itens_pag);					
if($pag_proxima == $num_paginas){
	$pag_inativa_prox = 'desabilitar_botao';
	$pag_proxima = $pagina;						
}else{
	$pag_inativa_prox = '';

}


?>
<div id="pages_maincontent" style="background: #FFF; ">

	<div class="loginform" style="margin-top: 10px">
		<form method="post">		
		<input class="form_input" type="date" name="dataInicial" id="dataInicial" style="width:37%; float:left" value="<?php echo $dataInicial ?>" onchange="buscar('')"/>
		<img src="images/icons/black/icone_datas4.png" style="float:left; width:13%">
			<input class="form_input" type="date" name="dataFinal" id="dataFinal" style="width:37%; float:right" value="<?php echo $dataFinal ?>" onchange="buscar('')"/>


			<div class="<?php echo @$lancar_tarefas ?>" style="margin-bottom: 5px">						
						<div class="selector_overlay">
							<select name="usuario" id="usuario" class="sel_pagina" style="height:40px !important; width:100%" onchange="buscar('')" >
								<option value="0">Selecionar Usuário</option>
								<?php 
				if($nivel_usuario == 'Administrador'){
					$query = $pdo->query("SELECT * FROM usuarios where nivel != 'Cliente' order by nome asc");				
				}else{
					$query = $pdo->query("SELECT * FROM usuarios where id = '$id_usuario' ");
				}		

				$res = $query->fetchAll(PDO::FETCH_ASSOC);
				for($i=0; $i < @count($res); $i++){
					foreach ($res[$i] as $key => $value){}
						?>	

						<option value="<?php echo $res[$i]['id'] ?>" <?php if($res[$i]['id'] == $usuario){ ?> selected <?php } ?> ><?php echo $res[$i]['nome'] ?> </option>



				<?php } ?>			
								
							</select>
						</div>
					</div>		

			<div class="tabs tabs--photos" style="margin:5px !important; width:100%">
					
			<input type="radio" name="tabs" class="tabradio" id="tabone" <?php echo $checked_atrasadas ?> onchange="buscar('Atrasadas')">
			<label style="width:30%" class="tablabel tablabel--13" for="tabone" >Atrasadas <span style="color:red">(<?php echo $tarefas_atrasadas ?>)</span></label>	
                          <div class="tab ocultar">
                             1  
                          </div>
    
			<input type="radio" name="tabs" class="tabradio" id="tabtwo" <?php echo $checked_agendadas ?> onchange="buscar('Agendada')">
			<label style="width:30%" class="tablabel tablabel--13" for="tabtwo">Pendentes <span style="color:#7a0c0c">(<?php echo $total_tarefas ?>)</span></label>
                          <div class="tab ocultar">
                            2
                          </div> 
			<input type="radio" name="tabs" class="tabradio" id="tabthree" <?php echo $checked_hoje ?> onchange="buscar('Hoje')">
			<label style="width:40%" class="tablabel tablabel--13" for="tabthree">Pendentes Hoje <span style="color:blue">(<?php echo $agendadas_hoje ?>)</span></label>
                          <div class="tab ocultar">
                             3
                          </div> 

           
                           
                    </div>

                    <input type="hidden" name="pago" id="pago">
                    <button id="btn_filtrar" class="limpar_botao ocultar" type="submit"></button>
		</form>
	</div>
	<div class="page_single layout_fullwidth_padding"> 

		<ul class="posts2" id="listar">
<?php 
if($filtro_tarefas == 'Atrasadas'){
	$query = $pdo->query("SELECT * from tarefas where usuario = '$usuario' and status = 'Agendada' and data <= curDate() and hora < curTime() order by id desc LIMIT $limite, $itens_pag");
}else if($filtro_tarefas == 'Agendada'){
	$query = $pdo->query("SELECT * from tarefas where usuario = '$usuario' and status = 'Agendada' order by id desc LIMIT $limite, $itens_pag");
}else if($filtro_tarefas == 'Hoje'){
	$query = $pdo->query("SELECT * from tarefas where usuario = '$usuario' and status = 'Agendada' and data = '$data_hoje' order by id desc LIMIT $limite, $itens_pag");
}else{
	$query = $pdo->query("SELECT * from tarefas where data >= '$dataInicial' and data <= '$dataFinal' and usuario = '$usuario' order by id desc LIMIT $limite, $itens_pag");
}


			
			$res = $query->fetchAll(PDO::FETCH_ASSOC);
			$linhas = @count($res);
			if($linhas > 0){
				for($i=0; $i<$linhas; $i++){
				
				$id = $res[$i]['id'];
	$usuario = $res[$i]['usuario'];
	$usuario_lanc = $res[$i]['usuario_lanc'];
	$data = $res[$i]['data'];
	$hora = $res[$i]['hora'];
	$descricao = $res[$i]['descricao'];
	$status = $res[$i]['status'];
	$hora_mensagem = $res[$i]['hora_mensagem'];
	$prioridade = $res[$i]['prioridade'];

	if($status == 'Agendada'){
		$bg_card = 'bg-red';
		$ocultar_itens = '';
		$img = 'vermelho.jpg';
		$class_span = 'red';
	}else{
		$bg_card = 'bg-success';
		$ocultar_itens = 'ocultar';
		$img = 'verde.jpg';
		$class_span = 'green';
	}

	if($prioridade == 'Baixa'){
		$classe_pri = 'circ_verde.png';
	}else if($prioridade == 'Média'){
		$classe_pri = 'circ_azul.png';
	}else{
		$classe_pri = 'circ_vermelho.png';
	}
	

	$dataF = implode('/', array_reverse(@explode('-', $data)));
	$horaF = date("H:i", @strtotime($hora));

	$descricaoF = mb_strimwidth($descricao, 0, 70, "...");

					

					echo <<<HTML
					<li class="swipeout">
					<div class="swiper-wrapper">

					<div class="swiper-slide swipeout-content item-content" >
					<div class="post_entry">
					
					<div class="post_details">
					<div class="post_category textos_list"> {$descricaoF}</div>				
					<p style="font-size:12px"><img src="images/icons/black/blog.png" width="13px" style="display:inline-block;" /> {$dataF} / {$horaF}</p>
					<p class="subtitulo_list"><img src="images/{$classe_pri}" width="13px" style="display:inline-block;" /> {$prioridade} (<span style="color:{$class_span}">{$status}</span>) </p>
					
					</div>
					<div class="post_swipe"><img src="images/swipe_more.png" alt="" title="" /></div>
					</div>
					</div>	
					
					<div class="swiper-slide swipeout-actions-right">
					<a class="{$ocultar_itens}" onclick="concluir_reg('{$id}', '{$descricaoF}')" style="width: 22%; background: #37de56" href="#"><img src="images/icons/white/square-check.png" alt="" title="Concluir Tarefa" /></a>
				
										
					<a onclick="excluir_reg('{$id}', '{$descricaoF}')" style="width: 22%; background: #d4412a" href="#"><img src="images/icons/white/trash.png" alt="" title="" /></a>
					<a onclick="editar('{$id}','{$usuario}','{$data}','{$hora}','{$hora_mensagem}','{$descricao}','{$prioridade}')" style="width: 22%; background: #3c6cb5" href="#" class="action1"><img src="images/icons/white/edit.png" alt="" title="" /></a>
					
					
					
					
					</div>
					</div>
					</li>	
					HTML;

				}

			}else{
				echo 'Nenhum Registro Encontrado!';
			}
			?> 

		</ul>


		

		
	<div class="shop_pagination">
          <a href="#" onclick="paginar('<?php echo $pag_anterior ?>', '<?php echo $buscar ?>')" class="prev_shop <?php echo $pag_inativa_ant ?>">ANTERIOR</a>
          <span class="shop_pagenr"><?php echo @$numero_pagina ?>/<?php echo @$num_paginas ?></span>
          <a href="#" onclick="paginar('<?php echo $pag_proxima ?>', '<?php echo $buscar ?>')" class="next_shop <?php echo $pag_inativa_prox ?>">PRÓXIMA</a>
     </div>	

     <form method="post" style="display:none">
     	<input type="text" name="pagina" id="input_pagina">
     	<input type="text" name="buscar" id="input_buscar">
     	<input type="text" name="dataInicial" id="dataInicialPag">
     	<input type="text" name="dataFinal" id="dataFinalPag">
     	<input type="text" name="pago" value="<?php echo $pago ?>">
     	<input type="text" name="usuario" id="usuario_pagina">
     	<button type="submit" id="paginacao"></button>
     </form>

	</div>	


</div>


<div class="fab" style="z-index: 100 !important">
	<button class="main open-popup"  data-popup=".popup-form" id="btn_novo">
		+
	</button>			  
</div>



</div>
</div>
</div>

</div>
</div>


<div class="popup popup-form" id="popupForm">
	<div class="content-block" style="padding:5px !important">
		<h2 class="page_title" id="titulo_inserir">INSERIR DADOS</h2>      
		<div class="page_single layout_fullwidth_padding">
			<div class="contactform">
				<form id="form" method="post">


					<div style="width:48%; float:left">
						<label class="labels_form">Hora</label>
						<input style="height:25px; background: #FFF" type="time" class="form_input" id="hora" name="hora" placeholder="" required>	
					</div>

						<div style="width:48%; float:right">
						<label class="labels_form">Data</label>
						<input type="date" style="height:25px; background: #FFF" class="form_input" id="data_tarefa" name="data" required="" value="<?php echo $data_atual ?>">
					</div>

					<div style="width:48%; float:left">
						<label class="labels_form">Hora Alerta</label>
						<input type="time" style="height:25px; background: #FFF" class="form_input" id="hora_alerta" name="hora_alerta" placeholder="" >		
					</div>

						<div style="width:48%; float:right">
						<label class="labels_form">Prioridade</label>
						<div class="selector_overlay">
						<select  id="prioridade" name="prioridade" style="height:40px !important;">
									<option value="Baixa">Baixa</option>
									<option value="Média">Média</option>
									<option value="Alta">Alta</option>
								</select>
							</div>
					</div>

						

					
					<div class="form_row">
						<label class="labels_form">Descrição</label>
						<input type="text" name="descricao" id="descricao" placeholder="Descrição da Tarefa" value="" class="form_input" />
					</div>					

					

					<input type="submit" name="btn_salvar" class="form_submit botao_salvar" id="btn_salvar" value="SALVAR"/>
					<div align="center" style="display:none" id="img_loader"><img src="images/loader.gif"></div>

					<input type="hidden" name="id" id="id">
					<input type="hidden" id="usuario_tarefa" name="usuario_tarefa" value="<?php echo $usuario ?>">	

				</form> 
			</div>    
		</div>          
	</div>
	<div class="close_popup_button">
		<a id="btn-fechar" href="#" onclick="limparCampos()" class="close-popup" data-popup=".popup-form"><img src="images/icons/black/menu_close.png" alt="" title="" /></a>
	</div>
</div>







<a style="display:block" href="#" class="action1 open-popup" data-popup=".popup-excluir" id="btn_excluir"></a>
<div class="popup popup-excluir" style="width:85%; margin:20px;  background: transparent; margin-top: 100px; overflow: none ">
	<div class="content-block">	

		<h2 class="page_title_excluir" id="nome_excluir"></h2> 

		<div class="botoes_excluir" align="center" style="height: 70px">
			<div style="width:49%; float:left"><input onclick="excluir()" type="button" class="form_submit botao_excluir botao_modelo" value="EXCLUIR"/></div>
			<div style="width:49%; float:right"><input data-popup=".popup-excluir" type="button" class="form_submit botao_cancelar botao_modelo close-popup" value="CANCELAR"/></div>
		</div>

		<input type="hidden" id="id_excluir">  

		<div class="close_popup_button"><a href="#" class="close-popup" data-popup=".popup-excluir"><img style="margin-top: 10px" src="images/icons/white/menu_close.png" alt="" title="" width="25px"/></a></div>
	
</div>
</div>





<a style="display:block" href="#" class="action1 open-popup" data-popup=".popup-concluir" id="btn_concluir"></a>
<div class="popup popup-concluir" style="width:85%; margin:20px;  background: transparent; margin-top: 100px; overflow: none ">
	<div class="content-block">	

		<h2 class="page_title_excluir" id="nome_concluir"></h2> 

		<div class="botoes_excluir" align="center" style="height: 70px">
			<div style="width:49%; float:left"><input onclick="concluir()" type="button" class="form_submit botao_confirmar botao_modelo" value="CONCLUIR"/></div>
			<div style="width:49%; float:right"><input data-popup=".popup-concluir" type="button" class="form_submit botao_cancelar botao_modelo close-popup" value="CANCELAR"/></div>
		</div>

		<input type="hidden" id="id_concluir">  

		<div class="close_popup_button"><a href="#" class="close-popup" data-popup=".popup-concluir"><img style="margin-top: 10px" src="images/icons/white/menu_close.png" alt="" title="" width="25px"/></a></div>
	
</div>
</div>








<script type="text/javascript">var pag = "<?=$pag?>"</script>


<script type="text/javascript">
	function editar(id, usuario, data, hora, hora_mensagem, descricao, prioridade){
		
    	$('#titulo_inserir').text('Editar Registro');

    	$('#id').val(id);
    	$('#data').val(data);
    	$('#hora').val(hora);
    	$('#hora_alerta').val(hora_mensagem);
    	$('#descricao').val(descricao);
    	$('#usuario_tarefa').val(usuario);
    	$('#prioridade').val(prioridade).change();		

    	$('#btn_novo').click();
	}




	function limparCampos(){
		$('#id').val('');
    	$('#hora_alerta').val('');
    	$('#hora').val('');
    	$('#descricao').val('');

    	$('#target').attr("src", "images/contas/sem-foto.png");
	}

	

	
	function paginar(pag, busca){
		$('#dataInicialPag').val($('#dataInicial').val());
		$('#dataFinalPag').val($('#dataFinal').val());

		$('#usuario_pagina').val($('#usuario').val());

		$('#input_pagina').val(pag);
		$('#input_buscar').val(busca);
		$('#paginacao').click();    
	}


	function buscar(filtro){
		$('#pago').val(filtro);
		$('#btn_filtrar').click();
	}	



function concluir_reg(id, nome){
		$('#id_concluir').val(id);    	 
		$('#nome_concluir').text(nome.toUpperCase());    	    
		$('#btn_concluir').click();
	}

	function concluir(){  
    var id =  $('#id_concluir').val();    
    $('#mensagem-concluir').text('Concluindo...')
    
    $.ajax({
        url: '../../painel/paginas/' + pag + "/concluir.php",
        method: 'POST',
        data: {id},
        dataType: "html",

        success:function(mensagem){
            if (mensagem.trim() == "Concluído com Sucesso") {  
            	toast(mensagem, 'verde');              
                location.reload();
            } else {
                toast(mensagem, 'vermelha');
            }
        }
    });
}

	
	
</script>



