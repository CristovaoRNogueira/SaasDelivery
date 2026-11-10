<?php
@session_start();
require_once ('../../sistema/conexao.php');

$id_empresa = @$_SESSION['empresa'];
if($id_empresa == ""){
  echo 'Sem Sessão da Empresa, acesse novamente!';
  exit();
}
require_once("../../sistema/painel/buscar_config.php");

$tel = @$_POST['tel'];

if ($tel == "") {
  exit();
}


$taxa_entrega = "0";
$taxa_entregaF = "0";

$query = $pdo->query("SELECT * FROM clientes where telefone = '$tel' and empresa = '$id_empresa' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if (@count($res) > 0) {
  $nome = $res[0]['nome'];
  $id_cliente = $res[0]['id'];
  $rua = $res[0]['endereco'];
  $numero = $res[0]['numero'];
  $bairro = $res[0]['bairro'];
  $complemento = $res[0]['complemento'];
  $cep = $res[0]['cep'];
  $cidade = $res[0]['cidade'];
  $referencia = $res[0]['referencia'];


  $query = $pdo->query("SELECT * FROM bairros where nome = '$bairro' and empresa = '$id_empresa'");
  $res = $query->fetchAll(PDO::FETCH_ASSOC);

  $taxa_entregaF = "";
  if (@count($res) > 0 and ($entrega_distancia != "Sim" or $chave_api_maps == "")) {
    $taxa_entrega = $res[0]['valor'];
    $taxa_entregaF = number_format($taxa_entrega, 2, ',', '.');
  } else {
    $taxa_entrega = "";
  }


}

echo @$nome . '**' . @$rua . '**' . @$numero . '**' . @$bairro . '**' . @$complemento . '**' . $taxa_entrega . '**' . $taxa_entregaF . '**' . @$id_cliente . '**' . @$cep . '**' . @$cidade. '**' . @$referencia;

?>