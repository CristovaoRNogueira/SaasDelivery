<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, minimal-ui">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<link rel="apple-touch-icon" href="images/apple-touch-icon.png" />
	<link rel="apple-touch-startup-image" href="images/apple-touch-startup-image-640x920.png">
	<title><?php echo $nome_sistema ?></title>
	<link rel="stylesheet" href="css/swiper.css">
	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="css/novos_estilos.css">
	<link rel="stylesheet" href="css/fab.css">
	<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,900" rel="stylesheet"> 
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>
<body id="mobile_wrap">

	<div class="panel-overlay"></div>

	<div class="panel panel-left panel-reveal">
		<!-- Slider -->
		<div class="swiper-container-subnav multinav">
			<div class="swiper-wrapper">
				<div class="swiper-slide">		
					<nav class="main_nav_underline">
						<ul>
							<li class="<?php echo @$home ?>"><a href="home"><img src="images/icons/white/home.png" alt="" title="" /><span>Home</span></a></li>
							
							
							<li class="<?php echo @$tarefas ?>"><a href="tarefas"><img src="images/icons/white/blog.png" alt="" title="" /><span>Tarefas</span></a></li>

							<li class="<?php echo @$novo_pedido ?>"><a href="entregas"><img src="images/icons/white/motoboy.png" alt=""
										title="" /><span>Entregas</span></a></li>

										<li class="<?php echo @$entregador ?>"><a href="entregas"><img src="images/icons/white/motoboy.png" alt=""
													title="" /><span>Minhas Entregas</span></a></li>

						</ul>
					</nav>
				</div>	

				<!--SUBMENU PESSOAS-->
				<div class="swiper-slide">		
					<div class="subnav_header backtonav"><img src="images/icons/white/back.png" alt="" title="" /><span>VOLTAR AO MENU</span></div>
					<nav class="main_nav_underline">
						<ul>
							<div id="sub_pessoas" style="display:none">
							<li class="<?php echo @$clientes ?>"><a href="clientes"><img src="images/icons/white/users.png" alt="" title="" /><span>Clientes</span></a></li>	

							<li class="<?php echo @$usuarios ?>"><a href="usuarios"><img src="images/icons/white/user.png" alt="" title="" /><span>Usuários</span></a></li>		

							<li class="<?php echo @$funcionarios ?>"><a href="funcionarios"><img src="images/icons/white/orders.png" alt="" title="" /><span>Funcionários</span></a></li>		

							<li class="<?php echo @$fornecedores ?>"><a href="fornecedores"><img src="images/icons/white/bike.png" alt="" title="" /><span>Fornecedores</span></a></li>
							</div>	



							<div id="sub_cadastros" style="display:none">
							<li class="<?php echo @$formas_pgto ?>"><a href="formas_pgto"><img src="images/icons/white/financeiro.png" alt="" title="" /><span>Formas PGTO</span></a></li>
							<li class="<?php echo @$frequencias ?>"><a href="frequencias"><img src="images/icons/white/blog.png" alt="" title="" /><span>Frequências</span></a></li>	
							<li class="<?php echo @$cargos ?>"><a href="cargos"><img src="images/icons/white/features.png" alt="" title="" /><span>Cargos</span></a></li>
							<?php if($alterar_acessos == 'Sim'){ ?>
							<li class="<?php echo @$grupo_acessos ?>"><a href="grupo_acessos"><img src="images/icons/white/tables.png" alt="" title="" /><span>Grupos</span></a></li>
							<li class="<?php echo @$acessos ?>"><a href="acessos"><img src="images/icons/white/lock.png" alt="" title="" /><span>Acessos</span></a></li>
							<?php } ?>
							</div>



							<div id="sub_financeiro" style="display:none">
							<li class="<?php echo @$receber ?>"><a href="receber"><img src="images/icons/white/contas.png" alt="" title="" /><span>Contas à Receber</span></a></li>
							<li class="<?php echo @$pagar ?>"><a href="pagar"><img src="images/icons/white/financeiro2.png" alt="" title="" /><span>Contas à Pagar</span></a></li>
							</div>


							</ul>
							</nav>				
						</div>

					</div>
				</div>
			</div>






			<div class="panel panel-right panel-reveal">
				<div class="user_login_info">

					<div class="user_thumb">
						<img src="images/fundo_area_user.jpg" alt="" title="" />
						<div class="user_details">
							<p>Bem Vindo, <?php echo $nivel_usuario ?> <span><?php echo $nome_usuario ?></span></p>
						</div>  
						<div class="user_avatar"><img src="../../painel/images/perfil/<?php echo $foto_usuario ?>" alt="" title="" /></div>       
					</div>

					<nav class="user-nav">
						<ul>
						
							<li><a href="#" data-popup=".popup-perfil" class="open-popup"><img src="images/icons/white/briefcase.png" alt="" title="" /><span>Editar Perfil</span></a></li>	
							<li><a href="logout.php"><img src="images/icons/white/lock.png" alt="" title="" /><span>Sair</span></a></li>
						</ul>
					</nav>
				</div>
			</div>



			  <div class="views">
			      <div class="view view-main">
			        <div class="pages">

			          <div data-page="<?php echo $data_page ?>" class="page homepage">
			            <div class="page-content">
						
						    <div class="navbarpages" style="background:#3e3e3e">
								<div class="navbar_left">
									<div class="logo_text"><a href="index"><img src="../../img/foto-painel.png" alt="" title="" width="60%"/></a></div>
								</div>
								<div class="navbar_right navbar_right_menu">
								<a href="#" data-panel="left" class="open-panel"><img src="images/icons/white/menu.png" alt="" title="" /></a>
								</div>			
								<div class="navbar_right">
								<a href="#" data-panel="right" class="open-panel"><img src="images/icons/white/user.png" alt="" title="" /></a>
								</div>
								
			                </div>




	
