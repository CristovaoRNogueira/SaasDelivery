<?php 
@session_start();
require_once('../../sistema/conexao.php');

$id_empresa = @$_SESSION['empresa'];
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, acesse novamente!';
	exit();
}
require_once("../../sistema/painel/buscar_config.php");

$buscar = $_POST['buscar'];


echo <<<HTML


<div class="catalog-search-results px-2 py-1">


HTML;
				
			$buscar = trim($_POST['buscar'] ?? '');
			$like = "%{$buscar}%";

			// Busca produtos que batem pelo nome ou pela categoria
			$sql = "
			  SELECT p.*
			    FROM produtos p
			   WHERE p.ativo = 'Sim' and empresa = '$id_empresa' 
			     AND (
			       p.nome LIKE :q
			       OR p.categoria IN (
			            SELECT c.id FROM categorias c
			             WHERE c.ativo = 'Sim'
			               AND (c.nome LIKE :q OR c.url LIKE :q)
			       )
			     )
			   ORDER BY p.valor_venda ASC
			";

			$stmt = $pdo->prepare($sql);
			$stmt->execute([':q' => $like]);
			$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$total_reg = count($res);


			$total_reg = @count($res);
			if ($total_reg > 0) {
				for ($i = 0; $i < $total_reg; $i++) {
					foreach ($res[$i] as $key => $value) {
					}
					$id = $res[$i]['id'];
					$foto = $res[$i]['foto'];
					$nome = $res[$i]['nome'];
					$descricao = $res[$i]['descricao'];
					$url = $res[$i]['url'];
					$estoque = $res[$i]['estoque'];
					$tem_estoque = $res[$i]['tem_estoque'];
					$valor = $res[$i]['valor_venda'];
					$promocao = $res[$i]['promocao'];
					$delivery = $res[$i]['delivery'];
					$val_promocional = $res[$i]['val_promocional'];
					$promocao = $res[$i]['promocao'];
					$categoria = $res[$i]['categoria'];

					$query3 = $pdo->query("SELECT * FROM categorias where id = '$categoria'");
					$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
					$url_cat = $res3[0]['url'];
					$mais_sabores = $res3[0]['mais_sabores'];
					$ativo = $res3[0]['ativo'];

					if($ativo != 'Sim'){
						continue;
					}



					$val_promocionalF = @number_format($val_promocional, 2, ',', '.');


					$valorF = @number_format($valor, 2, ',', '.');

					if (@$_SESSION['nome_mesa'] == "" and $delivery == 'Não') {
						continue;
					}


					if ($tem_estoque == 'Sim' and $estoque <= 0) {
						$mostrar = '';
						$url_produto = '#';
					} else {
						$mostrar = 'ocultar';
						$url_produto = 'produto-' . $url;
					}


					if ($promocao == 'Sim') {
						$mostrar_promo = '';
					} else {
						$mostrar_promo = 'ocultar';
					}


					//verificar se o produto tem grade
					$query3 = $pdo->query("SELECT * FROM grades where produto = '$id' and ativo = 'Sim'");
					$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
					$total_adc = @count($res3);
					if ($total_adc > 0) {
						if ($tem_estoque == 'Sim' and $estoque <= 0) {
							$url_produto = '#';
						} else {
							$url_produto = 'adicionais-' . $url;
						}
					} else {
						if ($tem_estoque == 'Sim' and $estoque <= 0) {
							$url_produto = '#';
						} else {
							$url_produto = 'observacoes-' . $url;
						}
					}



					if($mais_sabores == "Sim"){
						$link_red = 'categoria-sabores-'.$url_cat;
					}else{
						$link_red = $url_produto;
					}



echo <<<HTML

					<a href="{$link_red}" class="text-decoration-none">
						<div class="catalog-search-item position-relative">

							<img class="{$mostrar}" src="img/esgotado.png" width="50px" height="50px" style="position:absolute; right:6px; top:6px; z-index: 2;">
HTML;

if($mostrar != "") {
echo <<<HTML
								<img class="{$mostrar_promo}" src="img/promocao2.png" width="50px" height="50px" style="position:absolute; right:6px; top:6px; opacity:85%; z-index: 2;">
HTML;
}

echo <<<HTML
							<div style="width: 68px; height: 68px; flex-shrink: 0; border-radius: 12px; overflow: hidden; background: #f8f9fa;">
								<img src="sistema/painel/images/produtos/{$foto}" style="width: 100%; height: 100%; object-fit: cover;">
							</div>

							<div style="flex: 1; min-width: 0;">
								<div class="fw-bold text-dark" style="font-size: 14px; line-height: 1.2;">{$nome}</div>
								<div class="text-muted text-truncate" style="font-size: 11px; margin-top: 2px;">{$descricao}</div>
								<div class="mt-1" style="font-size: 13px; font-weight: 700; color: #198754;">
HTML;

											$query2 = $pdo->query("SELECT * FROM grades where produto = '$id' and tipo_item = 'Variação' and ativo = 'Sim' order by id asc");
											$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
											$total_reg2 = @count($res2);
											if ($total_reg2 > 0) {
												$id_grade = $res2[0]['id'];

												$query3 = $pdo->query("SELECT * FROM itens_grade where grade = '$id_grade' and ativo = 'Sim' order by id asc");
												$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
												$total_reg3 = @count($res3);
												if ($total_reg3 > 0) {
													for ($i3 = 0; $i3 < $total_reg3; $i3++) {
														$id_item = $res3[$i3]['id'];
														$nome_item = $res3[$i3]['texto'];
														$valor_item = $res3[$i3]['valor'];
														$valor_itemF = @number_format($valor_item, 2, ',', '.');

														echo '<span style="font-size:10px">(' . $nome_item . ') R$ ' . $valor_itemF;
														if ($i3 < $total_reg3 - 1) {
															echo ' / ';
														}
														echo '</span>';
													}
												}
											} else {
												if ($valor > 0) {

													if($val_promocional != 0 and $promocao != 'Não'){
														echo 'R$ ' . $val_promocionalF;
														echo ' <del style="color: #a0aec0; font-size: 11px; font-weight: 500;">R$ ' . $valorF . '</del>';
													}else{
														echo 'R$ ' . $valorF;
													}
												}
											}
echo <<<HTML
								</div>
							</div>

							<div class="catalog-btn-add" title="Ver produto">
								<i class="bi bi-plus-lg"></i>
							</div>

						</div>
					</a>

HTML;

		 }
			} 

echo <<<HTML

		</div>

HTML;

 ?>

