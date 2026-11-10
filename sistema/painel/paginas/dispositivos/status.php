<?php
@session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$tabela = 'dispositivos';

require_once("../../../conexao.php");
$id_empresa = @$_SESSION['empresa'];
require_once("../../buscar_config.php");

$id = $_POST['id'];
$status = $_POST['status'] ?? 'backup';
$novoStatus = $status == 'backup' ? 'Ativo' : 'backup';

$stmt = $pdo->prepare("UPDATE $tabela SET status = :novoStatus WHERE id = :id");
$stmt->execute(['novoStatus' => $novoStatus, 'id' => $id]);

echo "Dispositivo alterado para $novoStatus";
?>
