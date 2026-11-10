<?php

function escaparDetalhePedido($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function quantidadeDetalhePedido($quantidade)
{
    $numero = (float) $quantidade;
    return floor($numero) == $numero ? (string) (int) $numero : rtrim(rtrim(number_format($numero, 3, '.', ''), '0'), '.');
}

function linhaDetalhePedido($titulo, array $itens)
{
    if (!$itens) {
        return '';
    }

    return '<div style="display:flex; flex-wrap:wrap; gap:4px 12px; margin-top:3px;">'
        . '<strong style="color:#3f4663;">' . escaparDetalhePedido($titulo) . '</strong>'
        . '<span style="color:#4d5267;">' . implode(', ', $itens) . '</span>'
        . '</div>';
}

/**
 * Monta os mesmos complementos exibidos no comprovante/detalhamento do painel.
 */
function renderizarDetalhesItemPedido(PDO $pdo, array $item)
{
    $idCarrinho = (int) $item['id'];
    $idProduto = (int) $item['produto'];
    $sabores = (int) ($item['sabores'] ?? 0);
    $html = '';

    $idBorda = (int) ($item['borda'] ?? 0);
    if ($idBorda > 0) {
        $query = $pdo->prepare('SELECT nome FROM bordas WHERE id = :id LIMIT 1');
        $query->execute([':id' => $idBorda]);
        $nome = $query->fetchColumn();
        if ($nome) {
            $html .= linhaDetalhePedido('Borda', [escaparDetalhePedido($nome)]);
        }
    }

    $tabelaAdicional = $sabores > 0 ? 'adicionais_cat' : 'adicionais';
    $query = $pdo->prepare("SELECT id_item, quantidade FROM temp WHERE carrinho = :carrinho AND tabela = 'adicionais' ORDER BY id");
    $query->execute([':carrinho' => $idCarrinho]);
    $selecionados = [];
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $temp) {
        $busca = $pdo->prepare("SELECT nome FROM {$tabelaAdicional} WHERE id = :id LIMIT 1");
        $busca->execute([':id' => (int) $temp['id_item']]);
        $nome = $busca->fetchColumn();
        if ($nome) {
            $selecionados[] = '(' . quantidadeDetalhePedido($temp['quantidade']) . ') ' . escaparDetalhePedido($nome);
        }
    }
    $html .= linhaDetalhePedido('Adicionais', $selecionados);

    foreach (['guarnicoes' => ['Guarnições', 'guarnicoes'], 'ingredientes' => ['Retirar ingredientes', 'ingredientes']] as $tabelaTemp => $config) {
        [$titulo, $tabelaCadastro] = $config;
        $query = $pdo->prepare("SELECT id_item FROM temp WHERE carrinho = :carrinho AND tabela = :tabela ORDER BY id");
        $query->execute([':carrinho' => $idCarrinho, ':tabela' => $tabelaTemp]);
        $selecionados = [];
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $temp) {
            $busca = $pdo->prepare("SELECT nome FROM {$tabelaCadastro} WHERE id = :id LIMIT 1");
            $busca->execute([':id' => (int) $temp['id_item']]);
            $nome = $busca->fetchColumn();
            if ($nome) {
                $selecionados[] = escaparDetalhePedido($nome);
            }
        }
        $html .= linhaDetalhePedido($titulo, $selecionados);
    }

    $query = $pdo->prepare("SELECT id, tipo_item, nome_comprovante FROM grades WHERE produto = :produto AND tipo_item != 'Variação' ORDER BY posicao, id");
    $query->execute([':produto' => $idProduto]);
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $grade) {
        $busca = $pdo->prepare('SELECT id_item, quantidade FROM temp WHERE carrinho = :carrinho AND grade = :grade ORDER BY id');
        $busca->execute([':carrinho' => $idCarrinho, ':grade' => (int) $grade['id']]);
        $selecionados = [];
        foreach ($busca->fetchAll(PDO::FETCH_ASSOC) as $temp) {
            $itemGrade = $pdo->prepare('SELECT texto FROM itens_grade WHERE id = :id LIMIT 1');
            $itemGrade->execute([':id' => (int) $temp['id_item']]);
            $texto = $itemGrade->fetchColumn();
            if ($texto) {
                $quantidade = $grade['tipo_item'] == 'Múltiplo' ? '(' . quantidadeDetalhePedido($temp['quantidade']) . ') ' : '';
                $selecionados[] = $quantidade . escaparDetalhePedido($texto);
            }
        }
        $titulo = $grade['nome_comprovante'] ?: 'Itens escolhidos';
        $html .= linhaDetalhePedido($titulo, $selecionados);
    }

    $observacao = trim((string) ($item['obs'] ?? ''));
    if ($observacao !== '') {
        $html .= linhaDetalhePedido('Observação', [escaparDetalhePedido($observacao)]);
    }

    if ($html === '') {
        return '';
    }

    return '<div style="margin:2px 0 7px 18px; padding:5px 9px; border-left:3px solid #85899b; background:rgba(255,255,255,.35); font-size:11px;">' . $html . '</div>';
}

