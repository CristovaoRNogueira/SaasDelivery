<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, refaça o Login!';
	exit();
}
$id_usuario = $_SESSION['id'];

$tabela = 'valor_adiantamento';


$id = $_POST['id'];
$nome = $_POST['nome'];
$valor = $_POST['valor'];
$forma_pgto = $_POST['forma_pgto'];
$valor = str_replace(',', '.', $valor);


//verificar caixa aberto
$query1 = $pdo->query("SELECT * from caixas where operador = '$id_usuario' and data_fechamento is null order by id desc limit 1");
$res1 = $query1->fetchAll(PDO::FETCH_ASSOC);
if (@count($res1) > 0) {
  $id_caixa = @$res1[0]['id'];
} else {
  $id_caixa = 0;
}


$query = $pdo->prepare("INSERT INTO $tabela SET abertura = '$id', valor = :valor, nome = :nome, forma_pgto = :forma_pgto, empresa = '$id_empresa' ");
$query->bindValue(":nome", "$nome");
$query->bindValue(":valor", "$valor");
$query->bindValue(":forma_pgto", "$forma_pgto");
$query->execute();
$ultimo_id = $pdo->lastInsertId();

//lançar nas contas receber
$query = $pdo->prepare("INSERT INTO receber SET descricao = 'Adiantamento Mesa', tipo = 'Abertura', valor = :valor, data_lanc = curDate(), vencimento = curDate(), data_pgto = curDate(), usuario_lanc = '$id_usuario', usuario_pgto = '$id_usuario', foto = 'sem-foto.jpg', arquivo = 'sem-foto.jpg', pessoa = '0', pago = 'Sim', referencia = 'Adiantamento Mesa', id_ref = '$id', adiantamento = '$ultimo_id', forma_pgto = :forma_pgto, caixa = '$id_caixa', empresa = '$id_empresa'");
$query->bindValue(":valor", "$valor");
$query->bindValue(":forma_pgto", "$forma_pgto");
$query->execute();


echo 'Salvo com Sucesso';
