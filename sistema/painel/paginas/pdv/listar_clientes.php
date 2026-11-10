<?php
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
$pagina = 'clientes';

$valor = @$_POST['valor'];
$selecionado = @$_POST['selecionado'];

echo '<select class="sel2" name="cliente" id="cliente" style="width:100%;" onchange="trocarCliente()">';

if($valor == ""){
	echo '<option value="">Selecionar Cliente</option>';
}

$query = $pdo->query("SELECT * FROM clientes where empresa = '$id_empresa' order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
for($i=0; $i < @count($res); $i++){
	foreach ($res[$i] as $key => $value){}
	$id_cliente = $res[0]['id'];
	$selected = ((string) $selecionado === (string) $res[$i]['id']) ? ' selected' : '';
	$telefone = trim((string) @$res[$i]['telefone']);
	$texto_cliente = trim((string) $res[$i]['nome']) . ($telefone !== '' ? ' — ' . $telefone : ' — Sem telefone');
	$endereco_partes = [];
	$rua = trim((string) @$res[$i]['endereco']);
	$numero = trim((string) @$res[$i]['numero']);
	if ($rua !== '') $endereco_partes[] = $rua . ($numero !== '' ? ', ' . $numero : '');
	if (trim((string) @$res[$i]['complemento']) !== '') $endereco_partes[] = trim((string) $res[$i]['complemento']);
	if (trim((string) @$res[$i]['bairro']) !== '') $endereco_partes[] = trim((string) $res[$i]['bairro']);
	$cidade_estado = trim((string) @$res[$i]['cidade']);
	if (trim((string) @$res[$i]['estado']) !== '') $cidade_estado .= ($cidade_estado !== '' ? ' - ' : '') . trim((string) $res[$i]['estado']);
	if ($cidade_estado !== '') $endereco_partes[] = $cidade_estado;
	if (trim((string) @$res[$i]['cep']) !== '') $endereco_partes[] = 'CEP ' . trim((string) $res[$i]['cep']);
	if (trim((string) @$res[$i]['referencia']) !== '') $endereco_partes[] = 'Referência: ' . trim((string) $res[$i]['referencia']);
	$endereco_completo = count($endereco_partes) > 0 ? implode(' • ', $endereco_partes) : 'Endereço não informado';
	echo '<option value="'.(int) $res[$i]['id'].'" data-endereco="'.htmlspecialchars($endereco_completo, ENT_QUOTES, 'UTF-8').'" title="'.htmlspecialchars($endereco_completo, ENT_QUOTES, 'UTF-8').'"'.$selected.'>'.htmlspecialchars($texto_cliente, ENT_QUOTES, 'UTF-8').'</option>';

}

echo '</select>';

?>


	<script type="text/javascript">
			$(document).ready(function() {
			var valor = "<?=$valor?>";	
			var id_cliente = "<?=$id_cliente?>";	
			if(valor == '1'){
				$('#cliente_input').val(id_cliente);
			}
			if ($('#cliente').val()) {
				$('#cliente_input').val($('#cliente').val());
				$('#btn_editar_cliente_pdv').show();
				if (typeof calcularFreteClientePdv === 'function' && $('#entrega').val() === 'Delivery') calcularFreteClientePdv();
			} else {
				$('#btn_editar_cliente_pdv').hide();
			}
			

				function formatarClientePdv(cliente) {
					if (!cliente.id) return cliente.text;
					var endereco = $(cliente.element).data('endereco') || 'Endereço não informado';
					return $('<span></span>').text(cliente.text).attr('title', endereco);
				}

				$('.sel2').select2({
					templateResult: formatarClientePdv,
					templateSelection: formatarClientePdv
				});
			});

			function trocarCliente(){
				$('#cliente_input').val($('#cliente').val());
				$('#btn_editar_cliente_pdv').toggle(!!$('#cliente').val());
				if (typeof calcularFreteClientePdv === 'function' && $('#entrega').val() === 'Delivery') {
					calcularFreteClientePdv();
				}
			}
	</script>

