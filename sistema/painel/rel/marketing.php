<?php 
include('data_formatada.php');

if ($token_rel != 'M543661') {
	echo '<script>window.location="../../"</script>';
	exit();
}

if($filtro_busca != ""){
	$sql_busca = " and total_disparos > 0 ";
	
}else{
	$sql_busca = " ";
	
}

?>
<!DOCTYPE html>
<html>
<head>

<style>

@import url('https://fonts.cdnfonts.com/css/tw-cen-mt-condensed');
@page { margin: 145px 20px 25px 20px; }
#header { position: fixed; left: 0px; top: -110px; bottom: 100px; right: 0px; height: 35px; text-align: center; padding-bottom: 100px; }
#content {margin-top: 0px;}
#footer { position: fixed; left: 0px; bottom: -60px; right: 0px; height: 80px; }
#footer .page:after {content: counter(page, my-sec-counter);}
body {font-family: 'Tw Cen MT', sans-serif;}

.marca{
	position:fixed;
	left:50;
	top:100;
	width:80%;
	opacity:10%;
}

</style>

</head>
<body>
<?php 
if($marca_dagua == 'Sim'){ ?>
<img class="marca" src="<?php echo $url_sistema ?>sistema/img/<?php echo $logo_sistema ?>">	
<?php } ?>


<div id="header" >

	<div style="border-style: solid; font-size: 10px; height: 60px;">
		<table style="width: 100%; border: 0px solid #ccc;">
			<tr>
			<td style="border: 1px; solid #000; width: 25%; text-align: left;">
					<img style="margin-top: 0px; margin-left: 7px;" id="imag" src="<?php echo $url_sistema ?>sistema/img/<?php echo $logo_sistema ?>" width="150px">
				</td>
				
				<td style="text-align: center; font-size: 10px; width: 40%;">
				
                   <b><?php echo mb_strtoupper($nome_sistema) ?></b><br>
                   	<?php echo mb_strtoupper($cnpj_sistema) ?><br>
                   	INSTAGRAM: <b><?php echo mb_strtoupper($instagram_sistema) ?></b><br>
                   	<?php echo mb_strtoupper($endereco_sistema) ?>
 
				</td>
				<td style="width: 40%; text-align: right; font-size: 9px;padding-right: 10px;">
						<b><big>RELATÓRIO DE MARKETING</big></b><br><br> <?php echo mb_strtoupper($data_hoje) ?>
				</td>
			</tr>		
		</table>
	</div>

<br>


		<table id="cabecalhotabela" style="border-bottom-style: solid; font-size: 8px; margin-bottom:10px; width: 100%; table-layout: fixed;">
			<thead>
				
				<tr id="cabeca" style="margin-left: 0px; background-color:#CCC">
					<td style="width:30%">CAMPANHA</td>
					<td style="width:15%">INÍCIO DISPARO</td>
					<td style="width:15%">FINAL DISPARO</td>
					<td style="width:26%">GRUPO DISPARO</td>
					<td style="width:14%">DISPAROS PENDENTES</td>
				</tr>
			</thead>
		</table>
</div>

<div id="footer" class="row">
<hr style="margin-bottom: 0;">
	<table style="width:100%;">
		<tr style="width:100%;">
			<td style="width:60%; font-size: 10px; text-align: left;"><?php echo $nome_sistema ?> Telefone: <?php echo $telefone_sistema ?></td>
			<td style="width:40%; font-size: 10px; text-align: right;"><p class="page">Página  </p></td>
		</tr>
	</table>
</div>

<div id="content" style="margin-top: 0;">



		<table style="width: 100%; table-layout: fixed; font-size:8px; text-transform: uppercase;">
			<thead>
				<tbody>
					<?php 
$query = $pdo->query("SELECT * from marketing where titulo like '%$pesquisar%' and empresa = '$id_empresa' $sql_busca order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if($linhas > 0){
for($i=0; $i<$linhas; $i++){
	$id = $res[$i]['id'];
			$arquivo = $res[$i]['arquivo'];
			$audio = $res[$i]['audio'];
			$data_envio = $res[$i]['data_envio'];
			$data = $res[$i]['data'];
			$envios = $res[$i]['envios'];
			$forma_envio = $res[$i]['forma_envio'];
			$documento = $res[$i]['documento'];
			$dispositivo = $res[$i]['dispositivo'];
			$total_disparos = $res[$i]['total_disparos'];

			if($total_disparos == ""){
				$total_disparos = 0;
			}


			// Obter dados brutos e decodificar as entidades HTML
			$titulo = html_entity_decode($res[$i]['titulo'], ENT_QUOTES, 'UTF-8');
			$msg = html_entity_decode($res[$i]['mensagem'], ENT_QUOTES, 'UTF-8');
			$msg2 = html_entity_decode($res[$i]['mensagem2'], ENT_QUOTES, 'UTF-8');

			// Codificar a mensagem para ser enviada via URL
			$msgF = rawurlencode($msg);
			$msg2F = rawurlencode($msg2);

			$data_envioF = implode('/', array_reverse(@explode('-', $data_envio)));
			$dataF = implode('/', array_reverse(@explode('-', $data)));

			if ($forma_envio == "") {
				$forma_envio = "Todos";
			}

			$tituloF = mb_strimwidth($titulo, 0, 40, "...");


			$ocultar_audio = 'ocultar';
			if ($audio != "") {
				$ocultar_audio = '';
			}

			$ocultar_foto = 'ocultar';
			if ($arquivo != "sem-foto.png") {
				$ocultar_foto = '';
			}

			if ($forma_envio == "") {
				$forma_envio = "Todos";
			}

			$ocultar_doc = 'ocultar';
			if ($documento != "sem-foto.png") {
				$ocultar_doc = '';
			}

			$ocultar_reg = '';

			//extensão do arquivo
			$ext = pathinfo($documento, PATHINFO_EXTENSION);
			if ($ext == 'pdf') {
				$tumb_arquivo = 'pdf.png';
			} else if ($ext == 'rar' || $ext == 'zip') {
				$tumb_arquivo = 'rar.png';
			} else {
				$tumb_arquivo = $documento;
			}

			$query2 = $pdo->query("SELECT * FROM disparos where campanha = '$id' ORDER BY id desc");
			$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
			$total_envios = @count($res2);

			if($total_envios == 0){
				$pdo->query("UPDATE marketing set total_disparos = 0 where id = '$id'");
			}

			$query2 = $pdo->query("SELECT * FROM disparos where campanha = '$id' ORDER BY data_disparo desc, hora desc limit 1");
			$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
			$hora_ultimo_envio = @$res2[0]['hora'];
			$data_ultimo_envio = @$res2[0]['data_disparo'];


			$query2 = $pdo->query("SELECT * FROM disparos where campanha = '$id' ORDER BY data_disparo asc, hora asc limit 1");
			$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
			$hora_primeiro_envio = @$res2[0]['hora'];
			$data_primeiro_envio = @$res2[0]['data_disparo'];

			$ocultar_parar = '';
			if($total_envios == 0){
				$ocultar_parar = 'ocultar';
			}


			$data_primeiro_envioF = implode('/', array_reverse(@explode('-', $data_primeiro_envio)));
			$data_ultimo_envioF = implode('/', array_reverse(@explode('-', $data_ultimo_envio)));

			$titulo = preg_replace('/[\x{1F000}-\x{1FFFF}]/u', '', $titulo);
	
  	 ?>

  	 
      <tr>
<td style="width:30%"><?php echo $titulo ?></td>
<td style="width:15%"><?php echo $data_primeiro_envioF ?> <span style="color:red"><?php echo $hora_primeiro_envio ?></span></td>
<td style="width:15%"><?php echo $data_ultimo_envioF ?> <span style="color:red"><?php echo $hora_ultimo_envio ?></span></td>
<td style="width:26%;"><?php echo $forma_envio ?></td>
<td style="width:14%;"><?php echo $total_envios ?> / <?php echo $total_disparos ?></td>
    </tr>

<?php } } ?>
				</tbody>
	
			</thead>
		</table>


</div>
<hr>
		

</body>

</html>


