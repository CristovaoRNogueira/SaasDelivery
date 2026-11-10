<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
$id_usuario = $_SESSION['id'];

$_SESSION['sessao_usuario'] = $id_usuario;
$nova_sessao = $id_usuario;
$sessao = $id_usuario;


$tem_variação = "";
$id_ab_mesa = $_SESSION['id_ab_mesa'];

$id = $_POST['id'];

$pdo->query("DELETE FROM temp where carrinho = '0' and sessao = '$nova_sessao' and empresa = '$id_empresa' ");

$tem_variação = 'Não';

$data = date('Y-m-d');


$query = $pdo->query("SELECT * FROM produtos where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {
    $nome = $res[0]['nome'];
    $descricao = $res[0]['descricao'];
    $foto = $res[0]['foto'];
    $id_prod = $res[0]['id'];
    $valor = $res[0]['valor_venda'];
    
    $categoria = $res[0]['categoria'];
    $val_promocional = $res[0]['val_promocional'];
    $promocao = $res[0]['promocao'];

    if($promocao == 'Sim'){
         $valor = $val_promocional;
    }

    $valorF = number_format($valor, 2, ',', '.');


    $query2 = $pdo->query("SELECT * FROM categorias where id = '$categoria'");
    $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
    $total_reg2 = @count($res2);
    if ($total_reg2 > 0) {
        $url_cat = $res2[0]['url'];
    }


    $valor_total_do_item = $valor;
}
?>

<div class="main-container">  

    <div id="" style="margin-top: 15px">
    <?php
    $query = $pdo->query("SELECT * FROM grades where produto = '$id_prod' and ativo = 'Sim' order by posicao asc");
    $res = $query->fetchAll(PDO::FETCH_ASSOC);
    $total_reg = @count($res);
    if ($total_reg > 0) {
        for ($i = 0; $i < $total_reg; $i++) {
            $id_grade = $res[$i]['id'];
            $tipo_item = $res[$i]['tipo_item'];
            $valor_item = $res[$i]['valor_item'];
            $texto = $res[$i]['texto'];
            $limite = $res[$i]['limite'];

            if ($tipo_item == 'Variação') {
                $tem_variação = 'Sim';
            }
    ?>

            <div class="titulo-itens" style="font-weight:600; font-size:14px; margin-top:12px; margin-bottom:6px; color:#333;">
                <input type="hidden" id="qt_<?php echo $id_grade ?>">
                <?php echo $texto ?> 
                <?php if ($limite > 0) { echo ' <span style="font-size:12px; color:#666;">(até ' . $limite . ' itens!)</span>'; } ?>
            </div>

            <ol class="list-group" style="padding-left:0; margin-bottom:10px;">
                <?php
                $query2 = $pdo->query("SELECT * FROM itens_grade where grade = '$id_grade' and ativo = 'Sim' order by posicao asc");
                $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
                $total_reg2 = @count($res2);
                if ($total_reg2 > 0) {
                    for ($i2 = 0; $i2 < $total_reg2; $i2++) {
                        $id_item = $res2[$i2]['id'];
                        $texto_item = $res2[$i2]['texto'];
                        $valor_item_grade = $res2[$i2]['valor'];
                        $limite_item = $res2[$i2]['limite'];
                        $valor_item_gradeF = number_format($valor_item_grade, 2, ',', '.');

                        $ocultar_valor = 'ocultar';
                        if ($valor_item_grade > 0) {
                            $ocultar_valor = '';
                        }
                ?>
                        <li class="list-group-item" style="font-size:13px; padding:8px 10px; display:flex; justify-content:space-between; align-items:center; border:1px solid #eee; margin-bottom:5px; border-radius:6px;">
                            <span>
                                <span style="font-size:13px; color:#333;"><?php echo $texto_item ?></span> 
                                <span class="valor-item <?php echo $ocultar_valor ?>" style="color:#28a745;">(R$ <?php echo $valor_item_gradeF ?>)</span> 
                                <?php if ($limite_item > 0) { echo ' <span style="font-size:11px; color:#d9534f">(até ' . $limite_item . ' itens!)</span>'; } ?>
                            </span>

                            <?php if ($tipo_item == '1 de Cada') { ?>
                                <span class="form-switch direita">
                                    <input class="form-check-input" type="checkbox" id="<?php echo $id_item ?>" onchange="itens('<?php echo $id_item ?>', '<?php echo $id_grade ?>', '<?php echo $valor_item_grade ?>', '<?php echo $tipo_item ?>', '1', '<?php echo $valor_item ?>', '<?php echo $limite ?>' )">
                                </span>
                            <?php } ?>

                            <?php if ($tipo_item == 'Múltiplo') { ?>
                                <span class="direita" style="font-size:14px; display:flex; gap:4px; align-items:center;">
                                    <button onclick="dim('<?php echo $id_grade ?>', '<?php echo $id_item ?>', '<?php echo $valor_item_grade ?>', '<?php echo $tipo_item ?>', '<?php echo $valor_item ?>', '<?php echo $limite_item ?>', '<?php echo $limite ?>')" style="background:transparent; border:none"><i class="bi bi-dash-circle-fill text-danger"></i></button>
                                    <input id="quantidade_item_<?php echo $id_item ?>" value="0" style="background: #f8f9fa; border:1px solid #ccc; border-radius:4px; width:30px; text-align:center; font-size:13px;">
                                    <button onclick="aum('<?php echo $id_grade ?>', '<?php echo $id_item ?>', '<?php echo $valor_item_grade ?>', '<?php echo $tipo_item ?>', '<?php echo $valor_item ?>', '<?php echo $limite_item ?>', '<?php echo $limite ?>')" style="background:transparent; border:none"><i class="bi bi-plus-circle-fill text-success"></i></button>
                                </span>
                            <?php } ?>

                            <?php if ($tipo_item == 'Único' || $tipo_item == 'Variação') { ?>
                                <span class="direita">
                                    <input class="form-check-input" type="radio" value="Sim" name="<?php echo $id_grade ?>" id="<?php echo $id_grade ?>" onchange="itens('<?php echo $id_item ?>', '<?php echo $id_grade ?>', '<?php echo $valor_item_grade ?>', '<?php echo $tipo_item ?>', '1', '<?php echo $valor_item ?>' )">
                                </span>
                            <?php } ?>
                        </li>
                <?php }
                }
            } ?>
    </div>

<?php




            $valor_total_pedidoF = @number_format($valor_total_do_item, 2, ',', '.');
        } ?>





<div class="destaque-qtd" style="
  border:1px solid #e5e7eb; border-radius:12px;
  padding:12px 14px; margin-top:10px;
  background:#ffffff;
  display:flex; align-items:center; justify-content:space-between;">
  <b style="font-size:13px; color:#374151; letter-spacing:.2px;">Subtotal</b>

  <span class="direita" style="display:flex; align-items:center; gap:6px;">
    <input type="hidden" id="valor_total_produto" value="<?php echo $valor_total_do_item ?>">
    <input type="hidden" id="valor_total_input" value="<?php echo $valor_total_do_item ?>">

    <b style="font-size:16px; color:#111827;">
      R$ <span id="valor_item_quantF" style="font-variant-numeric:tabular-nums;">
        <?php echo $valor_total_pedidoF ?>
      </span>
    </b>
  </span>
</div>



<?php

$texto_botao = 'Adicionar ao Carrinho';
$funcao_botao = 'finalizarItem()';


if (@$_SESSION['sessao_usuario'] == "") {
    $sessao = date('Y-m-d-H:i:s-') . rand(0, 1500);
    $_SESSION['sessao_usuario'] = $sessao;
} else {
    $sessao = $_SESSION['sessao_usuario'];
}



if (@$_SESSION['id'] != "") {
    $id_usuario = $_SESSION['id'];
} else {
    $id_usuario = '';
}

$query = $pdo->query("SELECT * FROM carrinho where sessao = '$sessao' and empresa = '$id_empresa' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {
    $id_cliente = $res[0]['cliente'];
    $mesa_carrinho = $res[0]['mesa'];
    $nome_cli_pedido = $res[0]['nome_cliente'];


    $query = $pdo->query("SELECT * FROM clientes where id = '$id_cliente'");
    $res = $query->fetchAll(PDO::FETCH_ASSOC);
    if (@count($res) > 0) {
        $nome_cliente = $res[0]['nome'];
        $telefone_cliente = $res[0]['telefone'];
    } else {
        $nome_cliente = $nome_cli_pedido;
        $telefone_cliente = '';
    }
}



$query = $pdo->query("SELECT * FROM produtos where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {
    $nome = $res[0]['nome'];
    $descricao = $res[0]['descricao'];
    $foto = $res[0]['foto'];
    $id_prod = $res[0]['id'];
    $valor = $res[0]['valor_venda'];
    $valorF = number_format($valor, 2, ',', '.');
    $categoria = $res[0]['categoria'];
}

$query = $pdo->query("SELECT * FROM categorias where id = '$categoria'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {
    $url_cat = $res[0]['url'];
}


?>

<hr>





<input type="hidden" id="quantidade" value="1">



<div class="destaque-qtd" style="
    margin-top:12px; 
    padding:10px 12px; 
    border-top:1px solid #e5e7eb;
">
    <b style="font-size:13px; color:#374151;">Observações</b>

    <div class="form-group mt-2">
        <textarea maxlength="255" class="form-control" 
            name="obs" id="obs" 
            placeholder="Deseja adicionar alguma observação?"
            style="font-size:13px; border-radius:6px; min-height:70px; resize:none;">
        </textarea>
    </div>
</div>




</div>


<div class="d-grid gap-2 col-8 mx-auto mt-3">
    <button onclick="addCarrinho()" class="btn btn-warning btn-lg">Adicionar ao Pedido </button>
</div>

<br>

</body>

</html>

<input type="text" id="id_prod_g">


<script type="text/javascript">
    $(document).ready(function() {

    });

    function itens(id, grade, valor, tipo, quantidade, tipagem, limite_grade) {


        var marcado = $("#" + grade).val();
        var qtd_marcada = $("#qt_" + grade).val();
        if (qtd_marcada == "") {
            qtd_marcada = 0;
        }

        if (tipo == '1 de Cada' && limite_grade > 0) {
            if ($('#' + id).is(":checked") == true) {
                qtd_marcada_final = parseFloat(qtd_marcada) + 1;
            } else {
                qtd_marcada_final = parseFloat(qtd_marcada) - 1;
            }

            if (qtd_marcada_final > limite_grade) {
                alertWarning('O limite para essa escolha é de ' + limite_grade + ' Itens!');
                $('#' + id).prop("checked", false);
                return;
            } else {
                $("#qt_" + grade).val(qtd_marcada_final);
            }
        }


        $.ajax({
            url: '../../js/ajax/adicionar_item.php',
            method: 'POST',
            data: {
                id,
                grade,
                valor,
                tipo,
                marcado,
                quantidade,
                tipagem
            },
            dataType: "text",

            success: function(mensagem) {
                //alert(mensagem)
                if (mensagem.trim() == "Alterado com Sucesso") {
                    listarItens();
                }

            },

        });
    }




    function listarItens() {


        var id = '<?= $id_prod ?>';

        $.ajax({
            url: '../../js/ajax/listar_itens_grade.php',
            method: 'POST',
            data: {
                id
            },
            dataType: "html",

            success: function(result) {
                //alert(result)
                var split = result.split('*');

                $("#valor_total_input").val(split[0]);
                $("#valor_item_quantF").text(split[1]);
                $("#valor_total_produto").val(split[2]);
            }
        });
    }
</script>




<script type="text/javascript">
    function aum(grade, item, valor, tipo, tipagem, limite, limite_grade) {
        var quant = $("#quantidade_item_" + item).val();
        var quantidade = parseFloat(quant) + 1;


        if (limite > 0) {
            if (quantidade > limite) {
                alertWarning("A quantidade de itens não pode ser maior que " + limite)
                return;
            }
        }




        var qt_grade = $("#qt_" + grade).val();
        if (qt_grade == "") {
            qt_grade = 0;
        }
        qt_grade = parseFloat(qt_grade) + 1;


        if (limite_grade > 0) {
            if (qt_grade > limite_grade) {
                alertWarning("A quantidade de itens selecionados não pode ser maior que " + limite_grade)
                return;
            }
        }
        $("#qt_" + grade).val(qt_grade);


        $("#quantidade_item_" + item).val(quantidade);



        itens(item, grade, valor, tipo, quantidade, tipagem);
    }

    function dim(grade, item, valor, tipo, tipagem, limite, limite_grade) {
        var quant = $("#quantidade_item_" + item).val();

        var quantidade = parseFloat(quant) - 1;

        if (quantidade < 0) {
            alertWarning('Insira um valor igual ou maior que zero');
            return;
        }


        var qt_grade = $("#qt_" + grade).val();
        if (qt_grade == "") {
            qt_grade = 0;
        }
        qt_grade = parseFloat(qt_grade) - 1;
        $("#qt_" + grade).val(qt_grade);


        $("#quantidade_item_" + item).val(quantidade);



        itens(item, grade, valor, tipo, quantidade, tipagem);
    }
</script>




<script type="text/javascript">
    $(document).ready(function() {
        var quant = $("#quantidade").val();
        $("#quant").text(quant);

    });

    function aumentarQuant() {

        var quant = $("#quantidade").val();
        var novo_valor = parseInt(quant) + parseInt(1);
        $("#quant").text(novo_valor)
        $("#quantidade").val(novo_valor);


        var total_quant = parseInt(quant) + parseInt(1);
        var total_inicial = $("#valor_total_input").val();
        var total_it = parseFloat(total_inicial) * parseFloat(total_quant);
        $("#total_item").text(total_it.toFixed(2));
        $("#total_item_input").val(total_it);

        $("#valor_item_quantF").text(total_it.toFixed(2));
    }

    function diminuirQuant() {
        var quant = $("#quantidade").val();
        if (quant > 1) {
            var novo_valor = parseInt(quant) - parseInt(1);
            $("#quant").text(novo_valor)
            $("#quantidade").val(novo_valor)

            var total_quant = parseInt(quant) - parseInt(1);
            var total_inicial = $("#valor_total_input").val();
            var total_it = parseFloat(total_inicial) * parseFloat(total_quant);
            $("#total_item").text(total_it.toFixed(2));
            $("#total_item_input").val(total_it);

            $("#valor_item_quantF").text(total_it.toFixed(2));
        }

    }
</script>




<script type="text/javascript">
    function addCarrinho() {

        var quantidade = $('#quantidade').val();
        var total_item = $('#valor_total_input').val();
        var produto = "<?= $id_prod ?>"; 
        var obs = $('#obs').val();
        var tem_var = "<?= $tem_variação ?>";
        var mesa = "<?= $id_ab_mesa ?>";

        var valor_produto = $('#valor_total_produto').val();

        if (total_item <= 0 && valor_produto <= 0) {
            alertWarning("O valor do Pedido é zero, selecione as opções!");
            return;
        }

        if (valor_produto <= 0 && tem_var == 'Sim') {
            alertWarning("Selecione a Variação do Item");
            return;
        }



        $.ajax({
            url: '../../js/ajax/add-carrinho.php',
            method: 'POST',
            data: {
                quantidade,
                total_item,
                produto,
                obs,
                valor_produto,
                mesa
            },
            dataType: "text",

            success: function(mensagem) {
                if (mensagem.trim() == "Inserido com Sucesso") {
                    inserido();
                    $('#btn-fechar-grade').click();
                    listarVendas();
                } else {
                    alertWarning(mensagem)
                }

            },

        });
    }
</script>

 