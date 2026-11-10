<?php

// Espera existir no contexto:
// $id_empresa, $telefone_envio, $mensagem_whatsapp, $data_agd

$hash = ''; // vai voltar para seu UPDATE $tabela SET hash = '$hash'

if(@$referencia_agendamento == ""){
	$referencia_agendamento = 'receber';
}

if(@$ultimo_id == ""){
	$ultimo_id = @$id_ult_registro;
}

try {

    // normaliza telefone (garante só números)
    $telefone = preg_replace('/\D+/', '', (string)$telefone_envio);

    // normaliza mensagem (mantém padrão que você já usa)
    $mensagem = (string)$mensagem_whatsapp;

    // valida data agendada (precisa vir YYYY-mm-dd HH:ii:ss)
    $data_agendada = (string)$data_agd;

   

    if ($telefone == '') {
        throw new Exception('Telefone inválido para agendamento.');
    }

    if (trim($mensagem) == '') {
        throw new Exception('Mensagem vazia.');
    }

    if (trim($data_agendada) == '') {
        throw new Exception('Data agendada vazia.');
    }

    $stmt = $pdo->prepare("
        INSERT INTO whatsapp_agendamentos
        (empresa, telefone, mensagem, data_agendada, status, tentativas, referencia, id_ref)
        VALUES
        (:empresa, :telefone, :mensagem, :data_agendada, 'Pendente', 0, :referencia, :id_ref)
    ");

    $stmt->execute([
        ':empresa'       => (int)$id_empresa,
        ':telefone'      => $telefone,
        ':mensagem'      => $mensagem,
        ':data_agendada' => $data_agendada,
        ':referencia' => $referencia_agendamento,
        ':id_ref' => $ultimo_id
    ]);

    // usa o ID como hash (você já grava esse campo na tarefa)
    $hash = (string)$pdo->lastInsertId();

} catch (Exception $e) {
    // se quiser, você pode gravar isso em log; por enquanto só retorna hash vazio
    $hash = '';
}