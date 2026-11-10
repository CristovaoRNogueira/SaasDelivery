<?php
require_once("sistema/conexao.php");

$id_mesa = $_GET['id_mesa'];

$url_destino = $url_sistema; // raiz do sistema
?>

<form id="formRedirect" method="POST" action="<?php echo $url_destino; ?>">
    <input type="hidden" name="id_mesa" value="<?php echo $id_mesa; ?>">
</form>

<script>
// envia automaticamente ao carregar
document.getElementById('formRedirect').submit();
</script>
