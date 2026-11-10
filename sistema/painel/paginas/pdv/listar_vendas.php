<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
$tabela = 'carrinho';


$id_ab_mesa = $_SESSION['id_ab_mesa'];

$nome_mesa = $_SESSION['nome_mesa'];

$id_usuario = $_SESSION['id'];
$desconto = trim((string) (@$_POST['desconto']));
$desconto = str_replace(' ', '', $desconto);
if (strpos($desconto, ',') !== false) {
	$desconto = str_replace('.', '', $desconto);
	$desconto = str_replace(',', '.', $desconto);
}
$desconto = (float) $desconto;
$troco = @$_POST['troco'];
$valor_dinheiro = trim((string) (@$_POST['valor_dinheiro'] ?? '0'));
$valor_dinheiro = str_replace(',', '.', $valor_dinheiro);
$valor_dinheiro = (float) $valor_dinheiro;
$tipo_desconto = @$_POST['tipo_desconto'];
$entrega = trim((string) (@$_POST['entrega']));
$frete = @$_POST['frete'];
$frete = @str_replace(',', '.', $frete);

if($frete == ""){
	$frete = 0;
}

if($desconto == ""){
	$desconto = 0;
}

$total_troco = 0;
$total_trocoF = 0;

$total_v = 0;

//buscar o total da venda
$query = $pdo->query("SELECT * from $tabela where sessao = '$id_usuario' and pedido = '0' and mesa = '$id_ab_mesa' and empresa = '$id_empresa' order by id asc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if($linhas > 0){
	for($i=0; $i<$linhas; $i++){	
		$total_das_vendas = $res[$i]['total_item'];
		$total_v += $total_das_vendas;
	}
}

if ($tipo_desconto == '%') {
	$valor_desconto = $total_v > 0 ? ($total_v * $desconto / 100) : 0;
} else {
	$valor_desconto = $desconto;
}

$total_final = max(0, $total_v + $frete - $valor_desconto);


$query = $pdo->query("SELECT * from $tabela where sessao = '$id_usuario' and pedido = '0' and mesa = '$id_ab_mesa' and empresa = '$id_empresa' order by id asc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
echo '<div class="pdv-cart-items-scroll">';
if($linhas > 0){
	for($i=0; $i<$linhas; $i++){
	$id = $res[$i]['id'];
	$produto = $res[$i]['produto'];
	$valor = $res[$i]['valor_unitario'];
	$quantidade = $res[$i]['quantidade'];
	$total = $res[$i]['total_item'];
	$nome_produto_tab = $res[$i]['nome_produto'];
    $sabores = $res[$i]['sabores'];

   


	$total_finalF = @number_format($total_final, 2, ',', '.');
	$valorF = @number_format($valor, 2, ',', '.');
	$totalF = @number_format($total, 2, ',', '.');
	
	if($troco > 0){
		$total_troco = $troco - $valor_dinheiro;
		$total_trocoF = @number_format($total_troco, 2, ',', '.');
	}
	

	$query2 = $pdo->query("SELECT * from produtos where id = '$produto'");
	$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    if(@count($res2) > 0){
        $nome_produto = $res2[0]['nome'];
        $foto_produto = $res2[0]['foto'];
        $unidade = $res2[0]['unidade'];
    }else{
        $nome_produto = $nome_produto_tab;
        $foto_produto = "";
        $unidade = "";
    }


     if($sabores > 0){           
            $nome_produto = $nome_produto_tab;            
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

	$nome_produtoF = mb_strimwidth($nome_produto, 0, 40, "..."); 




    $query2 = $pdo->query("SELECT * FROM temp where carrinho = '$id' and tabela = 'Variação' order by id asc limit 1");
        $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
        $id_do_item = @$res2[0]['id_item'];

        $query2 = $pdo->query("SELECT * FROM itens_grade where id = '$id_do_item'");
        $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
        if(@$res2[0]['texto'] != ""){
            $sigla_grade = '<small> ('.@$res2[0]['texto'].')</small>';
        }else{
            $sigla_grade = '';
        }


        $ocultar_temp = 'ocultar';
        $query2 = $pdo->query("SELECT * FROM temp where carrinho = '$id' order by id asc limit 1");
        $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
        if(@count($res2) > 0){
            $ocultar_temp = '';
        }



	echo '<div class="row g-0">'; // g-0 remove o “gutter” padrão sem precisar de margens negativas
  echo '<div class="col-3 col-md-3 pe-1">'; // antes: margin-right:3px
    echo '<img src="images/produtos/'.$foto_produto.'" width="45px">'; // mantém 45px
  echo '</div>';

  // Torna este bloco "relative" para o X absoluto ancorar corretamente
  echo '<div class="col-9 col-md-9 position-relative">'; // antes tinha margin-left:-15px
    echo '<span class="text-dark" style="font-size:13px;">'; // mantém 13px (sem margin-left negativo)
      echo '<span class="'.$ocultar_quantidades.'">'.$quantidadeF.'</span> '.$nome_produtoF.' '.$sigla_grade;
    echo '</span>
    <span class="'.$ocultar_temp.'"><a title="Ver Grade" onclick="verGrade('.$id.')"><i class="fa fa-file text-info"></i></a></span>
    <br>';

    echo '<div class="text-dark" style="font-size:12px; margin-top:0;">'; // mantém 12px e sem margin-left
      echo '<a class="'.$ocultar_quantidades.'" href="#" onclick="diminuir('.$id.', '.$quantidadeF.')"><big><i class="fa fa-minus-circle text-danger"></i></big></a>
      <input type="text" value="'.$quantidadeF.'" style="background:transparent; border:none; outline:none; width:28px; text-align:center" id="input_quant_'.$id.'" onblur="alterarQuantidade('.$id.', '.$quantidadeF.')"> '.$sigla_unidade.'
      <a class="'.$ocultar_quantidades.'" href="#" onclick="aumentar('.$id.', '.$quantidadeF.')"><big><i class="fa fa-plus-circle text-success"></i></big></a>
      Total Item: R$ '.$totalF.'
      ';

      // Ícone remover no canto — mesmo visual, mas sem depender de margem negativa
      echo '<div class="dropdown head-dpdn2" style="position:absolute; top:0; right:10px;">
        <a title="Remover Item" href="#" onclick="excluirAlert('.$id.')"><big><i class="fa fa-times" style="color:#7d1107"></i></big></a>
      </div>';

    echo '</div>';
  echo '</div>';
echo '</div>';

	
	}
}
echo '</div>';

$total_finalF = @number_format($total_final, 2, ',', '.');
$total_vF = @number_format($total_v, 2, ',', '.');
$freteF = @number_format($frete, 2, ',', '.');
$valor_descontoF = @number_format($valor_desconto, 2, ',', '.');
echo '<div style="margin-top:12px; padding-top:12px; border-top:1px solid #d7dce2; font-size:13px;">';
if ($entrega === 'Delivery') {
	echo '<div style="background:#f8f9fb; border:1px solid #e4e7ec; border-radius:10px; padding:10px 12px;">';
	echo '<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:7px; padding-bottom:7px; border-bottom:1px dashed #d8dce3;">';
	echo '<span style="color:#697386; font-weight:600;"><i class="fa fa-shopping-basket" style="margin-right:5px;"></i>'.$linhas.' '.($linhas == 1 ? 'item' : 'itens').'</span>';
	echo '<span style="font-size:11px; font-weight:700; color:#ff7800; background:#fff1e6; border-radius:12px; padding:3px 8px;">DELIVERY</span>';
	echo '</div>';
	echo '<div style="display:flex; justify-content:space-between; gap:12px; margin-bottom:4px; color:#4f566b;"><span>Total dos itens</span><strong style="color:#252a3d;">R$ '.$total_vF.'</strong></div>';
	if ($valor_desconto > 0) {
		echo '<div style="display:flex; justify-content:space-between; gap:12px; margin-bottom:4px; color:#dc3545;"><span>Desconto</span><strong>- R$ '.$valor_descontoF.'</strong></div>';
	}
	echo '<div style="display:flex; justify-content:space-between; gap:12px; margin-bottom:7px; color:#4f566b;"><span>Frete</span><strong style="color:#252a3d;">R$ '.$freteF.'</strong></div>';
	echo '<div style="display:flex; justify-content:space-between; align-items:center; gap:12px; padding-top:7px; border-top:1px solid #dfe3e8; font-size:15px;"><strong>Total</strong><strong style="color:#ff7800; font-size:17px;">R$ '.$total_finalF.'</strong></div>';
	echo '</div>';
} else {
	echo '<div style="display:flex; justify-content:flex-end; align-items:center; gap:22px;">';
	echo '<span style="color:#697386;">Itens: <strong style="color:#252a3d;">'.$linhas.'</strong></span>';
	echo '<span style="color:#697386;">Subtotal: <strong style="color:#252a3d;">R$ '.$total_finalF.'</strong></span>';
	echo '</div>';
}
if($troco > 0){
echo '<br><span>Troco: </span>';
echo '<span style="font-weight:bold"> R$ ';
echo $total_trocoF;
echo '</span>';
}
echo '</div>';


?>

<script type="text/javascript">
	var itens = "<?=$linhas?>";
	if (typeof atualizarTotalPagamentosPdv === 'function') {
		atualizarTotalPagamentosPdv(<?=$total_final?>);
	} else {
		$('#subtotal_venda').val('<?=$total_final?>');
	}
	if(itens > 0){
		$("#btn_limpar").show();
		$("#btn_venda").show();
	}else{
		$("#btn_limpar").hide();
		$("#btn_venda").hide();
	}
	



// ALERT EXCLUIR #######################################
function excluirAlert(id) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success", // Adiciona margem à direita do botão "Sim, Excluir!"
            cancelButton: "btn btn-danger me-1",
            container: 'swal-whatsapp-container'
        },
        buttonsStyling: false
    });

    swalWithBootstrapButtons.fire({
        title: "Deseja Excluir?",
        text: "Você não conseguirá recuperá-lo novamente!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sim, Excluir!",
        cancelButtonText: "Não, Cancelar!",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Realiza a requisição AJAX para excluir o item
            $.ajax({
                url: 'paginas/' + pag + "/excluir-item.php",
                method: 'POST',
                data: { id },
                dataType: "html",
                success: function (mensagem) {
                    if (mensagem.trim() == "Excluído com Sucesso") {
                        // Exibe mensagem de sucesso após a exclusão
                        swalWithBootstrapButtons.fire({
                            title: mensagem,
                            text: 'Fecharei em 1 segundo.',
                            icon: "success",
                            timer: 1000,
                            timerProgressBar: true,
                            confirmButtonText: 'OK',
                            customClass: {
                             container: 'swal-whatsapp-container'
                             }
                        });                        
                        limparCampos();
                        listarVendas();
                        buscar();
                    } else {
                        // Exibe mensagem de erro se a requisição falhar
                        swalWithBootstrapButtons.fire({
                            title: "Opss!",
                            text: mensagem,
                            icon: "error",
                            confirmButtonText: 'OK',
                            customClass: {
                             container: 'swal-whatsapp-container'
                             }
                        });
                    }
                }
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            swalWithBootstrapButtons.fire({
                title: "Cancelado",
                text: "Fecharei em 1 segundo.",
                icon: "error",
                timer: 1000,
                timerProgressBar: true,
            });
        }
    });
}


	function diminuir(id, quantidade){
		 $.ajax({
        url: 'paginas/' + pag + "/diminuir.php",
        method: 'POST',
        data: {id, quantidade},
        dataType: "html",

        success:function(mensagem){
            
            if (mensagem.trim() == "Excluído com Sucesso") {           	
                listarVendas();
            } else {
                $('#mensagem-excluir').addClass('text-danger')
                $('#mensagem-excluir').text(mensagem)
            }
        }
    });
	}


	function aumentar(id, quantidade){
		 $.ajax({
        url: 'paginas/' + pag + "/aumentar.php",
        method: 'POST',
        data: {id, quantidade},
        dataType: "html",

        success:function(mensagem){
        	
            if (mensagem.trim() == "Excluído com Sucesso") {           	
                listarVendas();
            } else {
            	alert(mensagem)
                $('#mensagem-excluir').addClass('text-danger')
                $('#mensagem-excluir').text(mensagem)
            }
        }
    });
	}


		function alterarQuantidade(id, quant_enviada){
			var quantidade =  $('#input_quant_'+id).val();			
		 $.ajax({
        url: 'paginas/' + pag + "/alterar_quantidade.php",
        method: 'POST',
        data: {id, quantidade},
        dataType: "html",

        success:function(mensagem){
        	
            if (mensagem.trim() == "Excluído com Sucesso") {           	
                listarVendas();
            } else {
            	alert(mensagem);
            	$('#input_quant_'+id).val(quant_enviada);	
                $('#mensagem-excluir').addClass('text-danger')
                $('#mensagem-excluir').text(mensagem)
            }
        }
    });
	}


function verGrade(id){
    $('#modalVerGrade').modal('show');
    listarItensGrades(id);
}
	
</script>
