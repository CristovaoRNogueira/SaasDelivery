<?php
@session_start();
header('Content-Type: application/manifest+json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

require_once('sistema/conexao.php');

$id_empresa = filter_input(INPUT_GET, 'empresa', FILTER_VALIDATE_INT);
if (!$id_empresa) {
    $id_empresa = (int) ($_SESSION['empresa'] ?? 0);
}

$url_site_subdominio = resolverSubdominioTenant(true);

if ($id_empresa > 0) {
    $query = $pdo->prepare('SELECT empresa, nome, url_site, icone, logo FROM config WHERE empresa = :empresa LIMIT 1');
    $query->execute([':empresa' => $id_empresa]);
    $config_empresa = $query->fetch(PDO::FETCH_ASSOC);
} elseif ($url_site_subdominio !== '') {
    $query = $pdo->prepare('SELECT empresa, nome, url_site, icone, logo FROM config WHERE url_site = :url_site AND empresa > 0 LIMIT 1');
    $query->execute([':url_site' => $url_site_subdominio]);
    $config_empresa = $query->fetch(PDO::FETCH_ASSOC);
    if ($config_empresa) {
        $id_empresa = (int) $config_empresa['empresa'];
    }
} else {
    $config_empresa = false;
}

if (!$config_empresa || empty($config_empresa['url_site'])) {
    http_response_code(404);
    echo json_encode(['erro' => 'Empresa não encontrada']);
    exit();
}

$nome_empresa = $config_empresa['nome'] ?: 'Delivery';
$url_site_empresa = $config_empresa['url_site'];
$icone_banco = $config_empresa['icone'] ?? '';
$logo_banco = $config_empresa['logo'] ?? '';

$base_url = rtrim($url_sistema, '/');
$base_path = parse_url($url_sistema, PHP_URL_PATH) ?: '/';
$base_path = '/' . trim($base_path, '/') . '/';
if ($base_path === '//') {
    $base_path = '/';
}

$arquivo_180 = "icone_180_{$id_empresa}.png";
$arquivo_192 = "icone_192_{$id_empresa}.png";
$arquivo_512 = "icone_512_{$id_empresa}.png";

// Caso os ícones redimensionados 180/192/512 ainda não existam mas a loja tenha ícone ou logo cadastrados, gera em PNG
$candidatos_origem = array_filter([
    $icone_banco !== 'sem-foto.png' ? $icone_banco : '',
    "icone_{$id_empresa}.png",
    $logo_banco !== 'sem-foto.png' ? $logo_banco : ''
]);

$origem_empresa = '';
foreach ($candidatos_origem as $cand) {
    if (is_file(__DIR__ . '/sistema/img/' . $cand)) {
        $origem_empresa = __DIR__ . '/sistema/img/' . $cand;
        break;
    }
}

if ($origem_empresa !== '' && function_exists('imagecreatetruecolor')) {
    $alvos_gerar = [
        180 => __DIR__ . '/sistema/img/' . $arquivo_180,
        192 => __DIR__ . '/sistema/img/' . $arquivo_192,
        512 => __DIR__ . '/sistema/img/' . $arquivo_512,
    ];
    $img_src = null;
    foreach ($alvos_gerar as $dim => $caminho_alvo) {
        if (!is_file($caminho_alvo)) {
            if ($img_src === null) {
                $raw = @file_get_contents($origem_empresa);
                $img_src = $raw ? @imagecreatefromstring($raw) : false;
            }
            if ($img_src) {
                $w = imagesx($img_src);
                $h = imagesy($img_src);
                $nova = imagecreatetruecolor($dim, $dim);
                if ($dim === 180) {
                    $branco = imagecolorallocate($nova, 255, 255, 255);
                    imagefilledrectangle($nova, 0, 0, $dim, $dim, $branco);
                    imagealphablending($nova, true);
                } else {
                    imagealphablending($nova, false);
                    imagesavealpha($nova, true);
                    $transp = imagecolorallocatealpha($nova, 0, 0, 0, 127);
                    imagefilledrectangle($nova, 0, 0, $dim, $dim, $transp);
                }
                imagecopyresampled($nova, $img_src, 0, 0, 0, 0, $dim, $dim, $w, $h);
                @imagepng($nova, $caminho_alvo);
                imagedestroy($nova);
            }
        }
    }
    if ($img_src) {
        imagedestroy($img_src);
    }
}

if (!is_file(__DIR__ . '/sistema/img/' . $arquivo_192)) {
    $arquivo_192 = ($icone_banco !== '' && $icone_banco !== 'sem-foto.png' && is_file(__DIR__ . '/sistema/img/' . $icone_banco))
        ? $icone_banco
        : 'icone_192.png';
}
if (!is_file(__DIR__ . '/sistema/img/' . $arquivo_512)) {
    $arquivo_512 = ($icone_banco !== '' && $icone_banco !== 'sem-foto.png' && is_file(__DIR__ . '/sistema/img/' . $icone_banco))
        ? $icone_banco
        : 'icone_512.png';
}
if (!is_file(__DIR__ . '/sistema/img/' . $arquivo_180)) {
    $arquivo_180 = $arquivo_192;
}

$v180 = @filemtime(__DIR__ . '/sistema/img/' . $arquivo_180) ?: 1;
$v192 = @filemtime(__DIR__ . '/sistema/img/' . $arquivo_192) ?: 1;
$v512 = @filemtime(__DIR__ . '/sistema/img/' . $arquivo_512) ?: 1;

$start_url = $base_url . '/';
$scope_url = $base_url . '/';

$manifest = [
    'id' => $base_url . '/?pwa=' . $id_empresa,
    'name' => $nome_empresa,
    'short_name' => $nome_empresa,
    'start_url' => $start_url,
    'scope' => $scope_url,
    'display' => 'standalone',
    'orientation' => 'portrait',
    'background_color' => '#ffffff',
    'theme_color' => '#ffffff',
    'icons' => [
        [
            'src' => $base_url . '/sistema/img/' . $arquivo_180 . '?v=' . $v180,
            'sizes' => '180x180',
            'type' => 'image/png',
            'purpose' => 'any'
        ],
        [
            'src' => $base_url . '/sistema/img/' . $arquivo_192 . '?v=' . $v192,
            'sizes' => '192x192',
            'type' => 'image/png',
            'purpose' => 'any maskable'
        ],
        [
            'src' => $base_url . '/sistema/img/' . $arquivo_512 . '?v=' . $v512,
            'sizes' => '512x512',
            'type' => 'image/png',
            'purpose' => 'any maskable'
        ]
    ]
];

echo json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
