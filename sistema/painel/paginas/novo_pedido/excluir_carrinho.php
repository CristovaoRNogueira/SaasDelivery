<?php
require_once("../../../conexao.php");
$tabela = 'carrinho';

$id = $_POST['id'];
$gerente = $_POST['gerente'];
$senha = $_POST['senha'];


$query = $pdo->query("SELECT * FROM usuarios WHERE id = '$gerente'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {

  if (!password_verify($senha, $res[0]['senha_crip'])) {
    echo 'Senha incorreta!!';
  } else {
    $pdo->query("DELETE from $tabela where id = '$id'");
    $pdo->query("DELETE from temp where carrinho = '$id'");
    echo 'Excluído com Sucesso';
  }
}
