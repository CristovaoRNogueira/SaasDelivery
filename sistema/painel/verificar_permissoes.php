<?php 
require_once("../conexao.php");
@session_start();
$id_usuario = $_SESSION['id'];

//sem grupo
$home = 'ocultar';
$pedidos = 'ocultar';
$pedidos_esteiras = 'ocultar';
$novo_pedido = 'ocultar';
$caixas = 'ocultar';
$tarefas = 'ocultar';
$lancar_tarefas = 'ocultar';
$anotacoes = 'ocultar';
$configuracoes = 'ocultar';
$pausar_delivery = 'ocultar';
$mensalidades = 'ocultar';

$pedido_site = 'ocultar';
$minhas_comissoes = 'ocultar';
$marketing = 'ocultar';
$dispositivos = 'ocultar';

//grupo pessoas
$usuarios = 'ocultar';
$fornecedores = 'ocultar';
$funcionarios = 'ocultar';
$clientes = 'ocultar';

//grupo cadastros
$grupo_acessos = 'ocultar';
$acessos = 'ocultar';
$frequencias = 'ocultar';
$cargos = 'ocultar';
$formas_pgto = 'ocultar';
$bairros = 'ocultar';
$dias = 'ocultar';
$banner_rotativo = 'ocultar';
$mesas = 'ocultar';
$cupons = 'ocultar';
$adicionais = 'ocultar';
$grupos_disparos = 'ocultar';
$unidade_medida = 'ocultar';

//grupo produtos
$produtos = 'ocultar';
$categorias = 'ocultar';
$estoque = 'ocultar';
$saidas = 'ocultar';
$entradas = 'ocultar';


//grupo financeiro
$receber = 'ocultar';
$pagar = 'ocultar';
$vendas = 'ocultar';
$compras = 'ocultar';
$lista_pedidos_mesas = 'ocultar';
$comissoes = 'ocultar';

// Relatórios
$rel_produtos = 'ocultar';
$rel_vendas = 'ocultar';
$rel_financeiro = 'ocultar';
$rel_lucro = 'ocultar';
$rel_sintetico_despesas = 'ocultar';
$rel_sintetico_receber = 'ocultar';
$rel_balanco = 'ocultar';



//pedidos locais
$pedidos_mesa = 'ocultar';
$pedidos_bar = 'ocultar';
$pedidos_outros = 'ocultar';


$query = $pdo->query("SELECT * FROM usuarios_permissoes where usuario = '$id_usuario'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){
	for($i=0; $i < $total_reg; $i++){
		foreach ($res[$i] as $key => $value){}
		$permissao = $res[$i]['permissao'];

		$query2 = $pdo->query("SELECT * FROM acessos where id = '$permissao'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$nome = $res2[0]['nome'];
		$chave = $res2[0]['chave'];
		$id = $res2[0]['id'];

		if($chave == 'home'){
			$home = '';
		}

		if ($chave == 'pedidos') {
			$pedidos = '';
		}

		if ($chave == 'novo_pedido') {
			$novo_pedido = '';
		}

		if ($chave == 'pedidos_esteira') {
			$pedidos_esteira = '';
		}

		if ($chave == 'pedido_mesas') {
			$pedido_mesas = '';
		}


		if($chave == 'configuracoes'){
			$configuracoes = '';
		}

		if($chave == 'caixas'){
			$caixas = '';
		}

		if($chave == 'tarefas'){
			$tarefas = '';
		}

		if ($chave == 'anotacoes') {
			$anotacoes = '';
		}

		if ($chave == 'minhas_comissoes') {
			$minhas_comissoes = '';
		}

		if ($chave == 'marketing') {
			$marketing = '';
		}

		if ($chave == 'dispositivos') {
			$dispositivos = '';
		}







		if ($chave == 'bairros') {
			$bairros = '';
		}

		if ($chave == 'cargos') {
			$cargos = '';
		}

		if ($chave == 'dias') {
			$dias = '';
		}

		if($chave == 'lancar_tarefas'){
			$lancar_tarefas = '';
		}

	
		if($chave == 'usuarios'){
			$usuarios = '';
		}

		if($chave == 'fornecedores'){
			$fornecedores = '';
		}

		if($chave == 'funcionarios'){
			$funcionarios = '';
		}

		if($chave == 'clientes'){
			$clientes = '';
		}


		if($chave == 'grupo_acessos'){
			$grupo_acessos = '';
		}

		if($chave == 'acessos'){
			$acessos = '';
		}


		if ($chave == 'banner_rotativo') {
			$banner_rotativo = '';
		}

		if ($chave == 'mesas') {
			$mesas = '';
		}

		if ($chave == 'cupons') {
			$cupons = '';
		}

		if ($chave == 'adicionais') {
			$adicionais = '';
		}




		if ($chave == 'produtos') {
			$produtos = '';
		}

		if ($chave == 'categorias') {
			$categorias = '';
		}

		if ($chave == 'estoque') {
			$estoque = '';
		}

		if ($chave == 'saidas') {
			$saidas = '';
		}

		if ($chave == 'entradas') {
			$entradas = '';
		}



		if ($chave == 'compras') {
			$compras = '';
		}

		if ($chave == 'vendas') {
			$vendas = '';
		}

		if($chave == 'frequencias'){
			$frequencias = '';
		}

		if($chave == 'cargos'){
			$cargos = '';
		}

		if($chave == 'formas_pgto'){
			$formas_pgto = '';
		}

		if($chave == 'grupos_disparos'){
			$grupos_disparos = '';
		}

		if($chave == 'unidade_medida'){
			$unidade_medida = '';
		}



		if($chave == 'receber'){
			$receber = '';
		}


		if($chave == 'pagar'){
			$pagar = '';
		}

		if ($chave == 'rel_produtos') {
			$rel_produtos = '';
		}


		if ($chave == 'rel_vendas') {
			$rel_vendas = '';
		}

		if($chave == 'rel_financeiro'){
			$rel_financeiro = '';
		}

		if($chave == 'rel_sintetico_receber'){
			$rel_sintetico_receber = '';
		}

		if($chave == 'rel_sintetico_despesas'){
			$rel_sintetico_despesas = '';
		}

		if($chave == 'rel_balanco'){
			$rel_balanco = '';
		}

		if ($chave == 'rel_lucro') {
			$rel_lucro = '';
		}

		if ($chave == 'lista_pedidos_mesas') {
			$lista_pedidos_mesas = '';
		}

		if ($chave == 'pedido_site') {
			$pedido_site = '';
		}
		
		if ($chave == 'pedidos_esteiras') {
			$pedidos_esteiras = '';
		}
		
		if ($chave == 'pedidos_mesa') {
			$pedidos_mesa = '';
		}


		if ($chave == 'comissoes') {
			$comissoes = '';
		}


		if ($chave == 'pedidos_bar') {
			$pedidos_bar = '';
		}

		if ($chave == 'pedidos_outros') {
			$pedidos_outros = '';
		}


		if ($chave == 'pausar_delivery') {
			$pausar_delivery = '';
		}

		if ($chave == 'mensalidades') {
			$mensalidades = '';
		}

		


	}

}



$pag_inicial = '';
if($home != 'ocultar'){
	$pag_inicial = 'home';
}else{
	$query = $pdo->query("SELECT * FROM usuarios_permissoes where usuario = '$id_usuario'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);	
	if($total_reg > 0){
		for($i=0; $i<$total_reg; $i++){
			$permissao = $res[$i]['permissao'];		
			$query2 = $pdo->query("SELECT * FROM acessos where id = '$permissao'");
			$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
			if($res2[0]['pagina'] == 'Não'){
				continue;
			}else{
				// A permissão manteve a chave histórica "pedido_site", mas o PDV
				// atual é servido pela rota/página "pdv".
				$pag_inicial = $res2[0]['chave'] == 'pedido_site' ? 'pdv' : $res2[0]['chave'];
				break;
			}	
				
		}
				

	}else{
		echo 'Você não tem permissão para acessar nenhuma página, acione o administrador!';
		echo "<script>localStorage.setItem('id_usu', '')</script>";
		exit();
	}
}



if($usuarios == 'ocultar' and $funcionarios == 'ocultar' and $fornecedores == 'ocultar' and $clientes == 'ocultar'){
	$menu_pessoas = 'ocultar';
}else{
	$menu_pessoas = '';
}


if($bairros == 'ocultar' and $cargos == 'ocultar' and $dias == 'ocultar' and $grupo_acessos == 'ocultar' and $acessos == 'ocultar' and $cargos == 'ocultar' and $frequencias == 'ocultar' and $formas_pgto == 'ocultar' and $banner_rotativo == 'ocultar' and $mesas == 'ocultar' and $cupons == 'ocultar' and $adicionais == 'ocultar' and $grupos_disparos == 'ocultar' and $unidade_medida == 'ocultar'){
	$menu_cadastros = 'ocultar';
}else{
	$menu_cadastros = '';
}

if ($produtos == 'ocultar' and $categorias == 'ocultar' and $estoque == 'ocultar' and $saidas == 'ocultar' and $entradas == 'ocultar') {
	$menu_produtos = 'ocultar';
} else {
	$menu_produtos = '';
}


if($compras == 'ocultar' and $vendas == 'ocultar' and $receber == 'ocultar' and $pagar == 'ocultar' and $rel_balanco == 'ocultar' and $rel_sintetico_despesas == 'ocultar' and $rel_sintetico_receber == 'ocultar' and $rel_financeiro =='ocultar' and $lista_pedidos_mesas == 'ocultar' and $comissoes == 'ocultar'){
	$menu_financeiro = 'ocultar';
}else{
	$menu_financeiro = '';
}


if ($rel_produtos == 'ocultar' and $rel_lucro == 'ocultar' and $rel_balanco == 'ocultar' and $rel_sintetico_despesas == 'ocultar' and $rel_sintetico_receber == 'ocultar' and $rel_financeiro == 'ocultar' and $rel_vendas == 'ocultar') {
	$relatorios = 'ocultar';
} else {
	$relatorios = '';
}


if ($pedidos_mesa == 'ocultar' and $pedidos_bar == 'ocultar' and $pedidos_outros == 'ocultar' ) {
	$menu_pedidos_locais = 'ocultar';
} else {
	$menu_pedidos_locais = '';
}
