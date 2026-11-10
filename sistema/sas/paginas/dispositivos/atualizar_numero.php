<?php
@session_start();
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$tabela = 'dispositivos';

require_once("../../../conexao.php");
$id_empresa = 0;


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $appkey = $_POST['appkey'] ?? null;
    $dados = $_POST['dados'] ?? null;
    $telefone = $dados['phone'] ?? null;

    if ($appkey && $telefone) {

        $stmt = $pdo->prepare("UPDATE $tabela SET telefone = :telefone WHERE appkey = :appkey and empresa = :empresa");

        if ($stmt->execute([
            ':telefone' => $telefone,
            ':appkey' => $appkey,
            ':empresa' => $id_empresa
        ])) {
            echo json_encode(['status' => 200, 'message' => 'Telefone atualizado com sucesso']);
        } else {
            echo json_encode(['status' => 500, 'message' => 'Erro ao atualizar o telefone']);
        }

    } else {
        echo json_encode(['status' => 400, 'message' => 'Dados insuficientes']);
    }

} else {
    echo json_encode(['status' => 405, 'message' => 'Método não permitido']);
}
?>