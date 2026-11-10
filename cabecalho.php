<?php
@session_start();
require_once("sistema/conexao.php");

$url_site = $_SESSION['url_site'];


$query2 = $pdo->prepare("SELECT * FROM config WHERE url_site = :url_site LIMIT 1");
$query2->execute([':url_site' => $url_site]);
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);

if (@count($res2) == 0) {
	http_response_code(404);
	echo 'Empresa não encontrada para esta URL.';
	exit();
}

$id_empresa = @$res2[0]['empresa'];
$_SESSION['empresa'] = $id_empresa;
require_once("sistema/painel/buscar_config.php");


?>
<!DOCTYPE html>
<html>

<head>
	<title><?php echo $nome_sistema ?></title>

	<base href="<?php echo $url_sistema ?>">

	 <meta property="og:description" content="<?php echo $nome_sistema ?>" />
  	<meta property="og:image" content="sistema/img/logo.png">
    <meta property="og:image:type" content="image/jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630"> 

	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<!-- Mobile Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<!-- Site Metas -->
	<meta name="keywords" content="delivery interativo, pedir sanduíche" />
	<meta name="description" content="Temos sanduíches de qualidade.." />
	<meta name="author" content="Hugo Vasconcelos" />

	<link rel="canonical" href="<?php echo $url_sistema . rawurlencode($url_site) ?>">
	<link rel="shortcut icon" href="sistema/img/<?php echo $icone_sistema ?>" type="image/x-icon">
	<link rel="manifest" href="manifest.php?empresa=<?php echo (int) $id_empresa ?>">


	<!-- CSS here -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/animate.css">
	<link rel="stylesheet" href="assets/css/swiper-bundle.css">
	<link rel="stylesheet" href="assets/css/slick.css">
	<link rel="stylesheet" href="assets/css/magnific-popup.css">
	<link rel="stylesheet" href="assets/css/spacing.css">
	<link rel="stylesheet" href="assets/css/meanmenu.css">
	<link rel="stylesheet" href="assets/css/nice-select.css">
	<link rel="stylesheet" href="assets/css/fontawesome.min.css">
	<link rel="stylesheet" href="assets/css/icon-dukamarket.css">
	<link rel="stylesheet" href="assets/css/main.css">
	<link rel="stylesheet" href="assets/css/style.css">


	<!-- CSS only -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">

	<!-- JavaScript Bundle with Popper -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">

	<link rel="stylesheet" type="text/css" href="css/style.css?v=<?php echo @filemtime(__DIR__ . '/css/style.css') ?: time(); ?>">

	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>


	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>



	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
	<!-- SweetAlert2 JS -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>




</head>

<body>
