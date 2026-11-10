<?php
@session_start();
require_once('../../sistema/conexao.php');

$id_empresa = @$_SESSION['empresa'];
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, acesse novamente!';
	exit();
}
require_once("../../sistema/painel/buscar_config.php");

$sessao = @$_SESSION['sessao_usuario'];


$id_mesa = "";
if (@$_SESSION['id_ab_mesa'] != "") {
	$id_mesa = $_SESSION['id_ab_mesa'];
}


$nome_produto2 = '';
if ($id_mesa == "") {
	if($sessao != ""){
		$query = $pdo->query("SELECT * FROM carrinho where sessao = '$sessao' and empresa = '$id_empresa' ");
	}
	
} else {
	$query = $pdo->query("SELECT * FROM carrinho where mesa = '$id_mesa' and empresa = '$id_empresa' ");
}

$id_edicao = "";
if (@$_SESSION['id_edicao'] != "") {
	$id_edicao = $_SESSION['id_edicao'];
}

if($id_edicao != ""){
	$query = $pdo->query("SELECT * FROM carrinho where pedido = '$id_edicao' and empresa = '$id_empresa' ");
}


$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
$total_carrinho = 0;
$total_carrinhoF = 0;
if ($total_reg > 0) {
	for ($i = 0; $i < $total_reg; $i++) {
		foreach ($res[$i] as $key => $value) {
		}

		$id = $res[$i]['id'];
		$total_item = $res[$i]['total_item'];
		$produto = $res[$i]['produto'];
		$quantidade = $res[$i]['quantidade'];
		$obs = $res[$i]['obs'];
		$item = $res[$i]['item'];
		$variacao = $res[$i]['variacao'];
		$nome_produto_tab = $res[$i]['nome_produto'];
		$sabores = $res[$i]['sabores'];
		$borda = $res[$i]['borda'];
		$categoria = $res[$i]['categoria'];
		$valor_unit = $res[$i]['valor_unitario'];

		if ($valor_unit == "") {
			if ($total_item > 0 and $quantidade > 0) {
				$valor_unit = $total_item / $quantidade;
			} else {
				$valor_unit = 0;
			}
		}

		$total_item = $total_item * $quantidade;
		$total_carrinho += $total_item;


		$total_itemF = number_format($total_item, 2, ',', '.');
		$valor_unitF = number_format($valor_unit, 2, ',', '.');
		$total_carrinhoF = number_format($total_carrinho, 2, ',', '.');

		$query2 = $pdo->query("SELECT * FROM variacoes where id = '$variacao'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		if (@count(@$res2) > 0) {
			$sigla_variacao = '(' . $res2[0]['sigla'] . ')';
		} else {
			$sigla_variacao = '';
		}


		$query2 = $pdo->query("SELECT * FROM produtos where id = '$produto'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		if (@count(@$res2) > 0) {
			$nome_produto = $res2[0]['nome'];
			$foto_produto = $res2[0]['foto'];
			$unidade = $res2[0]['unidade'];
		} else {
			$nome_produto = $nome_produto_tab;
			$foto_produto = "";
			$unidade = "";
		}



		$query3 = $pdo->query("SELECT * FROM unidade_medida where id = '$unidade'");
			$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
			if(@count($res3) > 0){
				$nome_unidade = $res3[0]['nome'];
			}else{
				$nome_unidade = 'Sem Unidade';
			}


		$ocultar_quantidades = '';
			$sigla_unidade = '';
			if($nome_unidade == 'Quilogramas' or $nome_unidade == 'Quilo' or $nome_unidade == 'Quilograma' or $nome_unidade == 'KG'){
				$sigla_unidade = ' (KG)';
				$ocultar_quantidades = 'ocultar';
			}

			if($nome_unidade == 'Metros' or $nome_unidade == 'Metro' or $nome_unidade == 'M' or $nome_unidade == 'm'){
				$sigla_unidade = ' (m)';
				$ocultar_quantidades = 'ocultar';
			}

			if($nome_unidade == 'Litro' or $nome_unidade == 'Litros' or $nome_unidade == 'L'){
				$sigla_unidade = ' (L)';
				$ocultar_quantidades = 'ocultar';
			}

			//tratamento separa string
			$qt = explode(".", $quantidade);
			if($qt[1] > 0){
				$quantidadeF = $quantidade;		
			}else{
				$quantidadeF = $qt[0];
			}


		if ($obs == '') {
			$classe_obs = 'text-warning';
		} else {
			$classe_obs = 'text-danger';
		}

		if ($sabores > 0) {
			$nome_produto = $nome_produto_tab;
		}


		if($id_mesa > 0){
			$ocultar_excluir = 'ocultar';
		}else{
			$ocultar_excluir = '';
		}
		



		echo <<<HTML



						<li>
  <div class="tpcart__item" style="position:relative; padding-right:48px;">
    <div class="tpcart__img">
      <img src="sistema/painel/images/produtos/{$foto_produto}" alt="">
    </div>

    <div class="tpcart__content">
      <span class="tpcart__content-title"><b>{$nome_produto} {$sigla_variacao}</b></span>
      <div class="tpcart__cart-price">
        <span class="quantity"><span class="">{$quantidadeF} {$sigla_unidade}</span> x</span>
        <span class="text-success">R$ {$total_itemF}</span>
      </div>
    </div>

    <!-- agora ancorado ao próprio item -->
    <div style="position:absolute; right:12px; top:50%; transform:translateY(-50%);">
      <a href="javascript:void(0)" onclick="excluirCarrinhoIcone('{$id}')" class="link-neutro {$ocultar_excluir}">
        <i class="icon-x-circle text-warning"></i>
      </a>
    </div>
  </div>
</li>



						




			</li>

HTML;
	}
} else {
	echo 'Nenhum item adicionado!';
}


?>

<script type="text/javascript">
	$("#total-carrinho-icone").text("<?= $total_carrinhoF ?>");
	$("#total-itens-carrinho").text("<?= $total_reg ?>");
	$("#total-carrinho-finalizar").text("<?= $total_carrinhoF ?>");


	function excluirCarrinhoIcone(id) {

		$.ajax({
			url: 'js/ajax/excluir-carrinho.php',
			method: 'POST',
			data: {
				id
			},
			dataType: "text",

			success: function(mensagem) {
				
				if (mensagem.trim() == "Excluido com Sucesso") {
					listarCarrinhoIcone();
				}

			},

		});
	}
</script>