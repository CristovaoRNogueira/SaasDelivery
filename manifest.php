<?php
@session_start();
header('Content-Type: application/manifest+json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

require_once('sistema/conexao.php');

$id_empresa = filter_input(INPUT_GET, 'empresa', FILTER_VALIDATE_INT);
if (!$id_empresa) {
    $id_empresa = (int) ($_SESSION['empresa'] ?? 0);
}

$query = $pdo->prepare('SELECT nome, url_site FROM config WHERE empresa = :empresa LIMIT 1');
$query->execute([':empresa' => $id_empresa]);
$config_empresa = $query->fetch(PDO::FETCH_ASSOC);

if (!$config_empresa || empty($config_empresa['url_site'])) {
    http_response_code(404);
    echo json_encode(['erro' => 'Empresa não encontrada']);
    exit();
}

$nome_empresa = $config_empresa['nome'] ?: 'Delivery';
$url_site_empresa = $config_empresa['url_site'];
$base_path = parse_url($url_sistema, PHP_URL_PATH) ?: '/';
$base_path = '/' . trim($base_path, '/') . '/';
if ($base_path === '//') {
    $base_path = '/';
}

$arquivo_192 = "icone_192_{$id_empresa}.png";
$arquivo_512 = "icone_512_{$id_empresa}.png";

if (!is_file(__DIR__ . '/sistema/img/' . $arquivo_192)) {
    $arquivo_192 = 'icone_192.png';
}
if (!is_file(__DIR__ . '/sistema/img/' . $arquivo_512)) {
    $arquivo_512 = 'icone_512.png';
}

$start_url = $base_path . rawurlencode($url_site_empresa);

$manifest = [
    'id' => $base_path . 'pwa/' . $id_empresa,
    'name' => $nome_empresa,
    'short_name' => $nome_empresa,
    'start_url' => $start_url,
    'scope' => $base_path,
    'display' => 'standalone',
    'orientation' => 'portrait',
    'background_color' => '#ffffff',
    'theme_color' => '#ffffff',
    'icons' => [
        [
            'src' => $base_path . 'sistema/img/' . $arquivo_192,
            'sizes' => '192x192',
            'type' => 'image/png'
        ],
        [
            'src' => $base_path . 'sistema/img/' . $arquivo_512,
            'sizes' => '512x512',
            'type' => 'image/png'
        ]
    ]
];

echo json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
