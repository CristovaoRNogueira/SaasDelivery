<?php
@session_start();
require_once("sistema/conexao.php");

$id_mesa = $_GET['id_mesa'];
$url_empresa = trim((string) ($_GET['url_empresa'] ?? $_GET['url_site'] ?? $_SESSION['url_site'] ?? ''));

if ($url_empresa === '' && !empty($id_mesa)) {
	$stmt_mesa = $pdo->prepare("SELECT c.url_site FROM mesas m INNER JOIN config c ON c.empresa = m.empresa WHERE m.id = :id LIMIT 1");
	$stmt_mesa->execute([':id' => (int) $id_mesa]);
	$row_mesa = $stmt_mesa->fetch(PDO::FETCH_ASSOC);
	if (!empty($row_mesa['url_site'])) {
		$url_empresa = $row_mesa['url_site'];
	}
}

if ($url_empresa !== '') {
	$_SESSION['url_site'] = $url_empresa;
}

$url_destino = $url_sistema . $url_empresa;
?>

<form id="formRedirect" method="POST" action="<?php echo $url_destino; ?>">
    <input type="hidden" name="id_mesa" value="<?php echo $id_mesa; ?>">
</form>

<script>
// envia automaticamente ao carregar
document.getElementById('formRedirect').submit();
</script>
