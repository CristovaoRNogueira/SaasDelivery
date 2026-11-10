<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
require_once("../../../caixa_operador.php");
if($id_empresa == ""){
  echo 'Sem Sessão da Empresa, refaça o Login!';
  exit();
}
$id_usuario = $_SESSION['id'];
$id_caixa = obterCaixaAbertoOperador($pdo, $id_usuario, $id_empresa);

$tabela = 'abertura_mesa';

$id_ab = $_POST['id_ab'];
$id_mesa = $_POST['id'];
$total_itens = $_POST['total_itens'];
$garcon = $_POST['garcon'];
$obs = $_POST['obs'];
$id_ab = $_POST['id_ab'];
$comissao = $_POST['comissao'];
$couvert = $_POST['couvert'];
$forma_pgto = $_POST['forma_pgto'];

$total_itens = str_replace(',', '.', $total_itens);
$comissao = str_replace(',', '.', $comissao);
$couvert = str_replace(',', '.', $couvert);

$desconto = $_POST['desconto'];
$tipo_desconto = $_POST['tipo_desconto'];
$total_recebido = @$_POST['total_recebido'];
$troco = @$_POST['troco'];

if($total_recebido == ""){
  $total_recebido = 0;
}

if($troco == ""){
  $troco = 0;
}

if($desconto == ""){
  $desconto = 0;
}

$desconto = str_replace(',', '.', $desconto);

$total_recebido = str_replace('.', '', $total_recebido);
$total_recebido = str_replace(',', '.', $total_recebido);
$troco = str_replace('.', '', $troco);
$troco = str_replace(',', '.', $troco);

// Valores determinantes do fechamento são sempre reconstruídos no servidor.
// Isso impede alteração manual do subtotal pelo navegador ou por requisição direta.
$query_totais = $pdo->prepare("SELECT COALESCE(SUM(total_item), 0) AS total_itens FROM carrinho WHERE mesa = :abertura AND empresa = :empresa");
$query_totais->execute([':abertura' => $id_ab, ':empresa' => $id_empresa]);
$total_itens = (float) $query_totais->fetchColumn();

$query_adiantamentos = $pdo->prepare("SELECT COALESCE(SUM(valor), 0) FROM valor_adiantamento WHERE abertura = :abertura AND empresa = :empresa");
$query_adiantamentos->execute([':abertura' => $id_ab, ':empresa' => $id_empresa]);
$total_valor_fechamento = (float) $query_adiantamentos->fetchColumn();

$total_mesa_fechamento = max(0, $total_itens + (float) $comissao + (float) $couvert - $total_valor_fechamento);
$total_desconto = 0;
if($tipo_desconto == "%"){
  $total_desconto = $desconto * $total_mesa_fechamento / 100;
}else{
  $total_desconto = $desconto;
}
$total_desconto = min(max(0, (float) $total_desconto), $total_mesa_fechamento);
$subtotal = round($total_mesa_fechamento - $total_desconto, 2);


$total_compra = 0;

$query = $pdo->prepare("UPDATE $tabela SET garcon = :garcon, total = :total, horario_fechamento = curTime(), status = 'Fechada', obs = :obs, comissao_garcon = :comissao, couvert = :couvert, subtotal = :subtotal, forma_pgto = :forma_pgto, valor_adiantado = '$total_valor_fechamento', desconto = :desconto, total_recebido = :total_recebido, troco = :troco WHERE id = '$id_ab'");


$query->bindValue(":garcon", "$garcon");
$query->bindValue(":total", "$total_itens");
$query->bindValue(":obs", "$obs");
$query->bindValue(":comissao", "$comissao");
$query->bindValue(":couvert", "$couvert");
$query->bindValue(":subtotal", "$subtotal");
$query->bindValue(":forma_pgto", "$forma_pgto");
$query->bindValue(":desconto", "$total_desconto");
$query->bindValue(":total_recebido", "$total_recebido");
$query->bindValue(":troco", "$troco");
$query->execute();

$pdo->query("UPDATE mesas set status = 'Fechada' where id = '$id_mesa'");



$query1 = $pdo->query("SELECT * from formas_pgto where id = '$forma_pgto'");
$res1 = $query1->fetchAll(PDO::FETCH_ASSOC);
$nome_forma_pgto = $res1[0]['nome'];

$query1 = $pdo->query("SELECT * from abertura_mesa where id = '$id_ab'");
$res1 = $query1->fetchAll(PDO::FETCH_ASSOC);
$nome_cliente_ab = $res1[0]['cliente'];

//lançar a comissão do garçon
$query = $pdo->prepare("INSERT INTO pagar SET descricao = 'Comissão Garçon', tipo = 'Comissão', valor = :valor, data_lanc = curDate(), vencimento = curDate(), usuario_lanc = '$id_usuario', foto = 'sem-foto.png', arquivo = 'sem-foto.jpg', pessoa = '0', pago = 'Não', funcionario = '$garcon', referencia = 'Comissão', id_ref = '$id_ab', caixa = '$id_caixa',  comissao = 'Garçon', hora = curTime(), empresa = '$id_empresa' ");
$query->bindValue(":valor", "$comissao");
$query->execute();



//atualizar produtos
$query = $pdo->query("SELECT * FROM carrinho where mesa = '$id_ab'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {
  for ($i = 0; $i < $total_reg; $i++) {
    foreach ($res[$i] as $key => $value) {
    }

    $id = $res[$i]['id'];
    $total_item = $res[$i]['total_item'];
    $produto = $res[$i]['produto'];
    $quantidade = $res[$i]['quantidade'];   

    $query2 = $pdo->query("SELECT * FROM produtos where id = '$produto'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    $id_categoria = $res2[0]['categoria'];
    $valor_produto = $res2[0]['valor_venda'];
    $estoque = $res2[0]['estoque'];
    $tem_estoque = $res2[0]['tem_estoque'];
    $valor_compra = $res2[0]['valor_compra'];
    $valor_compra = $quantidade * $valor_compra;
    $total_compra += $valor_compra;


    if ($tem_estoque == 'Sim') {
      $total_produtos = $estoque - $quantidade;
      $pdo->query("UPDATE produtos SET estoque = '$total_produtos' where id = '$produto'");
    }

  }
} 



#######LANÇAR AS VENDAS###########################################
$query = $pdo->prepare("INSERT INTO receber SET descricao = 'Venda Mesa', tipo = 'Venda', valor = :valor, data_lanc = curDate(), vencimento = curDate(), data_pgto = curDate(), usuario_lanc = '$id_usuario', usuario_pgto = '$id_usuario', foto = 'sem-foto.png', arquivo = 'sem-foto.png', pessoa = '0', pago = 'Sim', referencia = 'Venda', id_ref = '$id_ab', caixa = '$id_caixa', forma_pgto = '$forma_pgto', subtotal = '$subtotal', hora = curTime(), troco = :troco, total_recebido = :total_recebido, desconto = :desconto, custo = '$total_compra', empresa = '$id_empresa' ");
$query->bindValue(":valor", "$subtotal");
$query->bindValue(":troco", "$troco");
$query->bindValue(":total_recebido", "$total_recebido");
$query->bindValue(":desconto", "$total_desconto");
$query->execute();



$pdo->query("INSERT INTO vendas SET cliente = '0', valor = '$subtotal', total_pago = '$subtotal', valor_recebido = '$subtotal', troco = '$troco', data = curDate(), hora = curTime(), status = 'Finalizado', pago = 'Sim',  taxa_entrega = '0', tipo_pgto = '$nome_forma_pgto', usuario_baixa = '$id_usuario', entrega = 'Pedido Mesa', mesa = '$id_mesa', nome_cliente = '$nome_cliente_ab', cupom = '$total_desconto', pago_entregador = 'Não', pedido = '', ref_api = '', tipo_pedido = 'Mesa', custo = '$total_compra', empresa = '$id_empresa' ");

$id_venda_mesa = (int) $pdo->lastInsertId();
$query_vincular_itens = $pdo->prepare("UPDATE carrinho SET pedido = :pedido WHERE mesa = :abertura AND empresa = :empresa");
$query_vincular_itens->execute([':pedido' => $id_venda_mesa, ':abertura' => $id_ab, ':empresa' => $id_empresa]);

echo 'Salvo com Sucesso';

?>
