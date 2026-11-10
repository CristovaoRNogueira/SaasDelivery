<?php 
@session_start();
require_once('../../sistema/conexao.php');

$id_empresa = @$_SESSION['empresa'];
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, acesse novamente!';
	exit();
}
require_once("../../sistema/painel/buscar_config.php");

$id = $_POST['id'];
$sessao = @$_SESSION['sessao_usuario'];

$query = $pdo->query("SELECT * FROM produtos where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){
	$nome = $res[0]['nome'];
	$descricao = $res[0]['descricao'];
	$foto = $res[0]['foto'];	
	$valor_produto = $res[0]['valor_venda'];

	$val_promocional = $res[0]['val_promocional'];
    $promocao = $res[0]['promocao'];

    if($promocao == 'Sim'){
         $valor_produto = $val_promocional;
    }
}else{
	exit();
}

$valor_total_do_item = $valor_produto;
$query =$pdo->query("SELECT * FROM temp where sessao = '$sessao' and carrinho = '0' and tabela = 'Variação' and empresa = '$id_empresa' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0 and $valor_total_do_item == 0){ 
    $tot_it = $res[0]['valor_item'];
    $valor_total_do_item = $tot_it;
}


//totalizar a temp com esse novo valor
$pdo->query("UPDATE temp set valor_item = '$valor_total_do_item' where sessao = '$sessao' and carrinho = '0' and tipagem = 'Produto' and empresa = '$id_empresa'");


$total_dos_itens = $valor_produto;

$query =$pdo->query("SELECT * FROM temp where sessao = '$sessao' and carrinho = '0' and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){ 
	for($i=0; $i < $total_reg; $i++){     
		
		$valor = $res[$i]['valor_item'];
		$quantidade = $res[$i]['quantidade'];
		
		$valor_item = $valor * $quantidade;

        $total_dos_itens += $valor_item;              

	      
	}

}


//verificar se a tipagem é Produto e a tabela é Multiplo
$query =$pdo->query("SELECT * FROM temp where sessao = '$sessao' and carrinho = '0' and tipagem = 'Produto' and tabela = 'Múltiplo' and empresa = '$id_empresa'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){ 
	 $total_dos_itens = $total_dos_itens - $valor_total_do_item;
}

$total_dos_itensF = number_format($total_dos_itens, 2, ',', '.');
echo $total_dos_itens.'*'.$total_dos_itensF.'*'.$valor_total_do_item;

?>