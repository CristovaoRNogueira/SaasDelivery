<?php
@session_start();
require_once("../../../conexao.php");
require_once("../../buscar_config.php");

$id_empresa = (int) @$_SESSION['empresa'];
$id_abertura = (int) @$_POST['id'];
$query_abertura = $pdo->prepare("SELECT a.*, m.nome AS nome_mesa FROM abertura_mesa a INNER JOIN mesas m ON m.id = a.mesa WHERE a.id = :id AND a.empresa = :empresa LIMIT 1");
$query_abertura->execute([':id' => $id_abertura, ':empresa' => $id_empresa]);
$abertura = $query_abertura->fetch(PDO::FETCH_ASSOC);
if (!$abertura) { echo '<div class="alert alert-danger">Mesa não encontrada.</div>'; exit; }

$query_itens = $pdo->prepare("SELECT COALESCE(SUM(total_item), 0) FROM carrinho WHERE mesa = :abertura AND empresa = :empresa");
$query_itens->execute([':abertura' => $id_abertura, ':empresa' => $id_empresa]);
$total_itens = (float) $query_itens->fetchColumn();
$query_valores = $pdo->prepare("SELECT * FROM valor_adiantamento WHERE abertura = :abertura AND empresa = :empresa ORDER BY id DESC");
$query_valores->execute([':abertura' => $id_abertura, ':empresa' => $id_empresa]);
$valores = $query_valores->fetchAll(PDO::FETCH_ASSOC);
$total_recebido = array_sum(array_map(function ($registro) { return (float) $registro['valor']; }, $valores));
$total_comissao = $comissao_garcon > 0 ? ($total_itens * (float) $comissao_garcon / 100) : 0;
$total_couvert = (float) $abertura['pessoas'] * (float) $couvert;
$total_mesa = $total_itens + $total_comissao + $total_couvert;
$saldo_restante = max(0, $total_mesa - $total_recebido);
function moedaResumoMesa($valor) { return 'R$ ' . number_format((float) $valor, 2, ',', '.'); }
?>
<div class="row g-2 mb-3">
	<div class="col-6 col-md"><div class="border rounded p-2 h-100 bg-light"><small class="text-muted">Itens consumidos</small><div class="fw-bold"><?= moedaResumoMesa($total_itens) ?></div></div></div>
	<div class="col-6 col-md"><div class="border rounded p-2 h-100"><small class="text-muted">Comissão</small><div class="fw-bold"><?= moedaResumoMesa($total_comissao) ?></div></div></div>
	<div class="col-6 col-md"><div class="border rounded p-2 h-100"><small class="text-muted">Couvert</small><div class="fw-bold"><?= moedaResumoMesa($total_couvert) ?></div></div></div>
	<div class="col-6 col-md"><div class="border rounded p-2 h-100" style="background:#eaf8ef"><small class="text-muted">Já recebido</small><div class="fw-bold text-success"><?= moedaResumoMesa($total_recebido) ?></div></div></div>
	<div class="col-12 col-md"><div class="border border-danger rounded p-2 h-100" style="background:#fff1f2"><small class="text-muted">Falta receber</small><div class="fw-bold text-danger"><?= moedaResumoMesa($saldo_restante) ?></div></div></div>
</div>
<div class="d-flex justify-content-between align-items-center mb-3 px-1"><span class="fw-semibold">Total da mesa</span><span class="fs-5 fw-bold"><?= moedaResumoMesa($total_mesa) ?></span></div>

<?php if (count($valores) > 0) { ?>
<div class="table-responsive"><table class="table table-hover table-bordered text-nowrap border-bottom">
	<thead><tr><th width="40%">Nome</th><th>Forma de pagamento</th><th>Valor</th><th>Excluir</th></tr></thead><tbody>
	<?php foreach ($valores as $registro) {
		$query_forma = $pdo->prepare("SELECT nome FROM formas_pgto WHERE id = :id AND empresa = :empresa");
		$query_forma->execute([':id' => $registro['forma_pgto'], ':empresa' => $id_empresa]);
		$nome_pgto = (string) $query_forma->fetchColumn(); ?>
	<tr><td><?= htmlspecialchars($registro['nome'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($nome_pgto, ENT_QUOTES, 'UTF-8') ?></td><td><?= moedaResumoMesa($registro['valor']) ?></td><td><a href="#" onclick="excluirValor('<?= (int) $registro['id'] ?>'); return false" title="Excluir"><i class="fe fe-trash-2 text-danger"></i></a></td></tr>
	<?php } ?>
	</tbody></table></div>
<?php } else { ?><div class="alert alert-light border">Ainda não possui adiantamentos lançados.</div><?php } ?>

<?php
$argumentos = [(int) $abertura['mesa'], (string) $abertura['nome_mesa'], (string) $abertura['obs'], $id_abertura, number_format($total_couvert, 2, '.', ''), number_format($total_comissao, 2, '.', ''), number_format($saldo_restante, 2, '.', ''), (int) $abertura['garcon'], number_format($total_itens, 2, '.', ''), number_format($total_recebido, 2, '.', '')];
$argumentos_js = implode(',', array_map(function ($valor) { return json_encode($valor, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT); }, $argumentos));
?>
<div class="d-flex justify-content-end mt-3"><button type="button" class="btn btn-success" onclick="abrirFechamentoPeloAdiantamento(<?= htmlspecialchars($argumentos_js, ENT_QUOTES, 'UTF-8') ?>)"><i class="fa fa-check-circle me-2"></i>Fechar Mesa</button></div>

<script>
function excluirValor(id) {
	$.ajax({url: 'paginas/' + pag + '/excluir_valor.php', method: 'POST', data: {id:id}, dataType: 'text', success: function(mensagem) {
		if (mensagem.trim() === 'Excluído com Sucesso' || mensagem.trim() === 'ExcluÃ­do com Sucesso') listarValores();
		else $('#mensagem-valor').addClass('text-danger').text(mensagem);
	}});
}
</script>
