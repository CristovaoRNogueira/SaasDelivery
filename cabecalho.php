<?php
@session_start();
require_once("sistema/conexao.php");

$url_empresa_get = trim((string) ($_GET['url_empresa'] ?? $_GET['url_site'] ?? ''));
if ($url_empresa_get === '' && basename($_SERVER['PHP_SELF'] ?? '') === 'site.php' && !empty($_GET['url'])) {
	$url_empresa_get = trim((string) $_GET['url']);
}

if ($url_empresa_get !== '') {
	$_SESSION['url_site'] = $url_empresa_get;
}

$url_site = trim((string) ($_SESSION['url_site'] ?? ''));

// Fallback para links de acompanhamento de pedido legados sem slug na URL
if ($url_site === '' && !empty($_GET['id'])) {
	$id_dec = base64_decode((string) $_GET['id']);
	$partes_id = explode('|', (string) $id_dec);
	if (count($partes_id) === 2 && $partes_id[1] === 'hugo') {
		$stmt_ped = $pdo->prepare("SELECT c.url_site FROM vendas v INNER JOIN config c ON c.empresa = v.empresa WHERE v.id = :id LIMIT 1");
		$stmt_ped->execute([':id' => (int) $partes_id[0]]);
		$row_ped = $stmt_ped->fetch(PDO::FETCH_ASSOC);
		if (!empty($row_ped['url_site'])) {
			$url_site = $row_ped['url_site'];
			$_SESSION['url_site'] = $url_site;
		}
	}
}

$url_empresa = $url_site;

// =========================================================================
// STRICT ROUTING MULTI-TENANT: Trava e Redirecionamento Estrito Baseado em Slug
// =========================================================================
$pagina_atual = basename($_SERVER['PHP_SELF'] ?? '', '.php');
$paginas_funil = ['carrinho', 'finalizar', 'pedido', 'itens', 'pizza', 'adicionais', 'observacoes'];

$is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
	|| (isset($_SERVER['REQUEST_URI']) && (strpos($_SERVER['REQUEST_URI'], '/ajax/') !== false || strpos($_SERVER['REQUEST_URI'], '/api/') !== false));

$is_admin = (isset($_SERVER['REQUEST_URI']) && (
	strpos($_SERVER['REQUEST_URI'], '/sistema') !== false ||
	strpos($_SERVER['REQUEST_URI'], '/painel') !== false
));

if (!$is_ajax && !$is_admin && empty($_GET['url_empresa'])) {
	if (in_array($pagina_atual, $paginas_funil, true) || $pagina_atual === 'site') {
		// Se não houver slug na URL e nem na sessão, redireciona para a raiz pública
		if ($url_site === '') {
			header("Location: /", true, 302);
			exit();
		}

		// Preserva parâmetros extras de query string (excluindo parâmetros de roteamento interno)
		$extra_params = [];
		if (!empty($_SERVER['QUERY_STRING'])) {
			parse_str($_SERVER['QUERY_STRING'], $extra_params);
			unset($extra_params['url_empresa'], $extra_params['url_site'], $extra_params['url'], $extra_params['id']);
		}
		$qs = !empty($extra_params) ? '?' . http_build_query($extra_params) : '';

		// Redirecionamento 301 automático para a URL canônica com slug da empresa
		switch ($pagina_atual) {
			case 'carrinho':
				header("Location: /" . $url_site . "/carrinho" . $qs, true, 301);
				exit();

			case 'finalizar':
				header("Location: /" . $url_site . "/finalizar" . $qs, true, 301);
				exit();

			case 'pedido':
				$id_ped = trim((string) ($_GET['id'] ?? ''));
				if ($id_ped !== '') {
					header("Location: /" . $url_site . "/pedido/" . rawurlencode($id_ped) . $qs, true, 301);
				} else {
					header("Location: /" . $url_site . $qs, true, 301);
				}
				exit();

			case 'itens':
				$url_cat = trim((string) ($_GET['url'] ?? ''));
				if ($url_cat !== '') {
					header("Location: /" . $url_site . "/categoria-" . rawurlencode($url_cat) . $qs, true, 301);
				} else {
					header("Location: /" . $url_site . $qs, true, 301);
				}
				exit();

			case 'pizza':
				$url_p = trim((string) ($_GET['url'] ?? ''));
				$req_uri = (string) ($_SERVER['REQUEST_URI'] ?? '');
				if ($url_p !== '') {
					$prefixo = (strpos($req_uri, 'categoria-sabores-') !== false) ? 'categoria-sabores-' : 'produto-';
					header("Location: /" . $url_site . "/" . $prefixo . rawurlencode($url_p) . $qs, true, 301);
				} else {
					header("Location: /" . $url_site . $qs, true, 301);
				}
				exit();

			case 'adicionais':
				$url_p = trim((string) ($_GET['url'] ?? ''));
				if ($url_p !== '') {
					header("Location: /" . $url_site . "/adicionais-" . rawurlencode($url_p) . $qs, true, 301);
				} else {
					header("Location: /" . $url_site . $qs, true, 301);
				}
				exit();

			case 'observacoes':
				$url_p = trim((string) ($_GET['url'] ?? ''));
				if ($url_p !== '') {
					header("Location: /" . $url_site . "/observacoes-" . rawurlencode($url_p), true, 301);
				} else {
					header("Location: /" . $url_site . $qs, true, 301);
				}
				exit();

			case 'site':
				header("Location: /" . $url_site . $qs, true, 301);
				exit();

			default:
				header("Location: /" . $url_site . "/" . $pagina_atual . $qs, true, 301);
				exit();
		}
	}
}

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

if (@$_SESSION['sessao_usuario'] == "") {
	$_SESSION['sessao_usuario'] = date('Y-m-d-H:i:s-') . rand(0, 1500);
}

require_once("sistema/painel/buscar_config.php");
$url_empresa = $url_site;


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
