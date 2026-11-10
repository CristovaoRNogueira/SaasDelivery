<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
$tabela = 'produtos';


$id = @$_POST['p1'];
$busca = @$_POST['p2'];

$id_cat = @$_POST['p1'];

if($id == ""){
	$query = $pdo->query("SELECT * from categorias where ativo = 'Sim' and empresa = '$id_empresa' order by nome asc limit 1");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$id = @$res[0]['id'];
$nome_cat = @$res[0]['nome'];
}else{
	$query = $pdo->query("SELECT * from categorias where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$nome_cat = @$res[0]['nome'];
}

if($busca == ""){
	$query = $pdo->query("SELECT * from $tabela where categoria = '$id' and ativo = 'Sim' and (estoque > 0 or tem_estoque = 'Não') and empresa = '$id_empresa' order by nome asc");
}else{
	$query = $pdo->query("SELECT * from $tabela where (nome LIKE '%$busca%' or codigo = '$busca') and ativo = 'Sim'  and (estoque > 0 or tem_estoque = 'Não') and empresa = '$id_empresa' order by nome asc");
}

$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if($linhas > 0){
	for($i=0; $i<$linhas; $i++){
	$id = $res[$i]['id'];
	$nome = $res[$i]['nome'];	
	$valor_venda = $res[$i]['valor_venda'];	
	$estoque = $res[$i]['estoque'];			
	$foto = $res[$i]['foto'];
	$categoria = $res[$i]['categoria'];
	$unidade = $res[$i]['unidade'];
	$promocao = $res[$i]['promocao'];
	$valor_promocao = $res[$i]['val_promocional'];

	
	if($promocao == 'Sim' and $valor_promocao > 0){
		$valor_venda = $valor_promocao;
	}
	
	$tem_estoque = $res[$i]['tem_estoque'];
		
	$valor_vendaF = @number_format($valor_venda, 2, ',', '.'); 
	$nomeF = mb_strimwidth($nome, 0, 80, "..."); 

	if($estoque <= 0){
		$ocultar_card = 'ocultar';
	}else{
		$ocultar_card = '';
	}

	$query2 = $pdo->query("SELECT * from categorias where id = '$categoria'");
	$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
	$cat_ativo = $res2[0]['ativo'];	

	$possui_estoque = '';
	if($tem_estoque == 'Não'){
		$possui_estoque = 'ocultar';
	}


	//tratamento separa string
	$est = @explode(".", $estoque);
	if(@$est[1] > 0){
		$estoqueF = $estoque;		
	}else{
		$estoqueF = $est[0];
	}


	$query3 = $pdo->query("SELECT * FROM unidade_medida where id = '$unidade'");
	$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
	if(@count($res3) > 0){
		$nome_unidade = $res3[0]['nome'];
	}else{
		$nome_unidade = 'Sem Unidade';
	}

	$sigla_unidade = ' Itens';
	$estoque_unit = '';
	if($nome_unidade == 'Quilogramas' or $nome_unidade == 'Quilo' or $nome_unidade == 'Quilograma' or $nome_unidade == 'KG'){
		$sigla_unidade = ' (KG)';
		$estoque_unit = 'Não';
	}

	if($nome_unidade == 'Metros' or $nome_unidade == 'Metro' or $nome_unidade == 'M' or $nome_unidade == 'm'){
		$sigla_unidade = ' (m)';
		$estoque_unit = 'Não';
	}

	if($nome_unidade == 'Litro' or $nome_unidade == 'Litros' or $nome_unidade == 'L'){
		$sigla_unidade = ' (L)';
		$estoque_unit = 'Não';
	}

	if($cat_ativo == 'Sim'){


		//verificar se o produto tem grade
					$query3 = $pdo->query("SELECT * FROM grades where produto = '$id' and ativo = 'Sim'");
					$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
					$total_adc = @count($res3);
					if ($total_adc > 0) {
						$tem_grade = 'Sim';
					} else {
						$tem_grade = 'Não';
					}
	
echo <<<HTML


		 <!-- Produto 1 -->
		 <a href="#" onclick="addVenda('{$id}', '{$estoque_unit}', '{$nome}', '{$nome_unidade}', '$tem_grade')">	
			  <div class="product-card" data-id="1" data-name="{$nome}" data-price="" data-stock="15">
			    <div class="card-image">
			      <img src="images/produtos/{$foto}" alt="{$nome}" class="w-full h-20 object-contain mx-auto">
			    </div>
			    <div class="card-content">
			      <h3 class="card-title">{$nomeF}</h3>
			      <p class="card-price">R$ {$valor_vendaF}</p>
			      <p class="card-stock " style="min-height:20px; display:flex; align-items:center;"><span class="$possui_estoque">Estoque: ({$estoqueF}) {$sigla_unidade}</span></p>
			      <button class="add-to-cart">
			        <i class="fas fa-plus text-xs"></i>
			      </button>
			    </div>
			  </div>
			</a>



HTML;
}
}
}else{
	echo '<p>Nenhum Produto Encontrado!</p>';
}
?>


<script type="text/javascript">
	$(document).ready( function () {
		
	//campo buscar
	$('#nome_categoria').text('<?=$nome_cat?>')

	var busca = $('#txt_buscar').val();
	var id_cat = '<?=$id_cat?>';

	if(id_cat != ""){
		$('#txt_buscar').val('');
	}

	if(busca != ""){
		$('#area_cat').hide();
	}else{
		$('#area_cat').show();
	}

	});

	function produto(id, nome, valor, codigo){

		$('#mensagem').text('');
    	$('#titulo_inserir').text('Venda: '+nome);
    	
    	$('#id').val(id);     	
    	$('#quantidade').val('1');
    	$('#codigo').val(codigo);


    	
    	$('#modalForm').modal('show');
    	listarVendas();
	}

	function limparCampos(){
		setTimeout(function() {
					$('#txt_buscar').val('');
			        $('#txt_buscar').focus();

			    }, 100);


	}
</script>