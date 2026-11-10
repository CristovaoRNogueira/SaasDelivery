<?php 
$tabela = 'planos';
require_once("../../../conexao.php");

$plano = $_POST['plano'];

$query5 = $pdo->query("SELECT * FROM planos where id = '$plano'");
$res5 = $query5->fetchAll(PDO::FETCH_ASSOC);
$nome_plano = @$res5[0]['nome'];
$valor_plano = @$res5[0]['valor'];
$dispositivos = @$res5[0]['dispositivos'];
$valorF = @number_format($valor_plano, 2, ',', '.');

echo $valorF.'*'.$dispositivos;
?>