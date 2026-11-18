<?php
@session_start();
require_once("sistema/conexao.php");

$url_site = resolverSubdominioTenant(true);
$_SESSION['url_site'] = $url_site;
$url_empresa = $url_site;

if ($url_site === '') {
	http_response_code(404);
	echo 'Empresa não encontrada para esta URL.';
	exit();
}

$query2 = $pdo->prepare("SELECT * FROM config WHERE url_site = :url_site AND empresa > 0 LIMIT 1");
$query2->execute([':url_site' => $url_site]);
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);

if (@count($res2) == 0) {
	http_response_code(404);
	echo 'Empresa não encontrada para esta URL.';
	exit();
}

$id_empresa = @$res2[0]['empresa'];
$_SESSION['empresa'] = $id_empresa;

if (@$_SESSION['sessao_usuario'] == "") {
	$_SESSION['sessao_usuario'] = date('Y-m-d-H:i:s-') . rand(0, 1500);
}

require_once("sistema/painel/buscar_config.php");
$url_empresa = $url_site;

// Resolução do ícone da loja para Favicon e PWA / iOS (Safari)
$candidatos_icone = array_filter([
	"icone_192_{$id_empresa}.png",
	$icone_sistema ?? '',
	"icone_{$id_empresa}.png",
	($logo_sistema ?? '') !== 'sem-foto.png' ? ($logo_sistema ?? '') : '',
	'icone_192.png',
	'icone.png'
]);

$arquivo_icone_loja = 'icone.png';
foreach ($candidatos_icone as $candidato) {
	if ($candidato !== 'sem-foto.png' && is_file(__DIR__ . '/sistema/img/' . $candidato)) {
		$arquivo_icone_loja = $candidato;
		break;
	}
}

// Garantir um ícone 180x180 em PNG sem transparência (exigência do Safari/iOS para apple-touch-icon)
$arquivo_apple_touch = $arquivo_icone_loja;
$caminho_origem_icone = __DIR__ . '/sistema/img/' . $arquivo_icone_loja;
$nome_apple_180 = "icone_180_{$id_empresa}.png";
$caminho_apple_180 = __DIR__ . '/sistema/img/' . $nome_apple_180;

if (is_file($caminho_origem_icone)) {
	$precisa_gerar_180 = !is_file($caminho_apple_180) || (@filemtime($caminho_origem_icone) > @filemtime($caminho_apple_180));
	if ($precisa_gerar_180 && function_exists('imagecreatetruecolor')) {
		$dados_img = @file_get_contents($caminho_origem_icone);
		$img_origem = $dados_img ? @imagecreatefromstring($dados_img) : false;
		if ($img_origem) {
			$largura_orig = imagesx($img_origem);
			$altura_orig = imagesy($img_origem);
			$img_180 = imagecreatetruecolor(180, 180);
			// Fundo branco sólido para evitar fundo preto do iOS em PNGs transparentes
			$fundo_branco = imagecolorallocate($img_180, 255, 255, 255);
			imagefilledrectangle($img_180, 0, 0, 180, 180, $fundo_branco);
			imagealphablending($img_180, true);
			imagecopyresampled($img_180, $img_origem, 0, 0, 0, 0, 180, 180, $largura_orig, $altura_orig);
			@imagepng($img_180, $caminho_apple_180);
			imagedestroy($img_180);
			imagedestroy($img_origem);
		}
	}
	if (is_file($caminho_apple_180)) {
		$arquivo_apple_touch = $nome_apple_180;
	}
}

$base_url_loja = rtrim($url_sistema, '/');
$versao_icone = @filemtime(__DIR__ . '/sistema/img/' . $arquivo_icone_loja) ?: time();
$versao_apple = @filemtime(__DIR__ . '/sistema/img/' . $arquivo_apple_touch) ?: $versao_icone;
$url_icone_loja = $base_url_loja . '/sistema/img/' . $arquivo_icone_loja . '?v=' . $versao_icone;
$url_apple_touch_icon = $base_url_loja . '/sistema/img/' . $arquivo_apple_touch . '?v=' . $versao_apple;
$url_logo_og = ($logo_sistema != '' && $logo_sistema != 'sem-foto.png' && is_file(__DIR__ . '/sistema/img/' . $logo_sistema))
	? ($base_url_loja . '/sistema/img/' . $logo_sistema)
	: $url_apple_touch_icon;
$cor_tema_loja = '#ffffff';


?>
<!DOCTYPE html>
<html>

<head>
	<title><?php echo htmlspecialchars($nome_sistema) ?></title>

	<base href="<?php echo $url_sistema ?>">

	<meta property="og:description" content="<?php echo htmlspecialchars($nome_sistema) ?>" />
	<meta property="og:image" content="<?php echo htmlspecialchars($url_logo_og) ?>">
	<meta property="og:image:type" content="image/png">
	<meta property="og:image:width" content="1200">
	<meta property="og:image:height" content="630">

	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<!-- Mobile Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<!-- Site Metas -->
	<meta name="keywords" content="delivery interativo, pedir sanduíche" />
	<meta name="description" content="<?php echo htmlspecialchars(!empty($meta_descricao) ? $meta_descricao : 'Temos sanduíches de qualidade..') ?>" />
	<meta name="author" content="Hugo Vasconcelos" />

	<!-- PWA / iOS (Safari) & Theme Metas -->
	<meta name="theme-color" content="<?php echo $cor_tema_loja ?>">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="default">
	<meta name="apple-mobile-web-app-title" content="<?php echo htmlspecialchars($nome_sistema) ?>">

	<link rel="canonical" href="<?php echo $url_sistema ?>">
	<link rel="shortcut icon" href="<?php echo htmlspecialchars($url_icone_loja) ?>" type="image/png">
	<link rel="icon" type="image/png" sizes="192x192" href="<?php echo htmlspecialchars($url_icone_loja) ?>">
	<link rel="apple-touch-icon" href="<?php echo htmlspecialchars($url_apple_touch_icon) ?>">
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo htmlspecialchars($url_apple_touch_icon) ?>">
	<link rel="manifest" href="<?php echo $base_url_loja ?>/manifest.php?empresa=<?php echo (int) $id_empresa ?>">


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
