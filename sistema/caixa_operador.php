<?php

/**
 * Retorna o caixa aberto do operador, quando existir.
 * Fora do PDV a operacao pode continuar com caixa 0.
 */
function obterCaixaAbertoOperador(PDO $pdo, $idUsuario, $idEmpresa)
{
    $idUsuario = (int) $idUsuario;
    $idEmpresa = (int) $idEmpresa;

    $query = $pdo->prepare('SELECT id FROM caixas WHERE operador = :operador AND empresa = :empresa AND data_fechamento IS NULL ORDER BY id DESC LIMIT 1');
    $query->execute([':operador' => $idUsuario, ':empresa' => $idEmpresa]);
    $idCaixa = $query->fetchColumn();

    return $idCaixa ? (int) $idCaixa : 0;
}

/**
 * Regra exclusiva do PDV: exige caixa aberto para usuarios que nao sejam
 * administradores.
 */
function exigirCaixaAbertoOperador(PDO $pdo, $idUsuario, $idEmpresa)
{
    $idUsuario = (int) $idUsuario;
    $idCaixa = obterCaixaAbertoOperador($pdo, $idUsuario, $idEmpresa);

    $query = $pdo->prepare('SELECT nivel FROM usuarios WHERE id = :id LIMIT 1');
    $query->execute([':id' => $idUsuario]);
    $nivel = $query->fetchColumn();

    if ($nivel === 'Administrador') {
        return $idCaixa ? (int) $idCaixa : 0;
    }

    if (!$idCaixa) {
        echo 'Você precisa abrir o seu caixa antes de realizar esta operação! Acesse a tela de Caixas, abra um novo caixa e tente novamente.';
        exit();
    }

    return $idCaixa;
}

