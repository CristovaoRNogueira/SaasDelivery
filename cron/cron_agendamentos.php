<?php
require_once("../sistema/conexao.php");
@date_default_timezone_set('America/Sao_Paulo');

// limite por rodada (evita sobrecarga)
$limite = 30;

// 1) Buscar agendamentos pendentes vencidos
$query = $pdo->query("
  SELECT *
  FROM whatsapp_agendamentos
  WHERE status = 'Pendente'
    AND data_agendada <= NOW()
    AND tentativas < 5
  ORDER BY data_agendada ASC
  LIMIT $limite
");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);

if ($linhas > 0) {

  for ($i = 0; $i < $linhas; $i++) {

    $id_agd   = (int)$res[$i]['id'];
    $id_empresa = (int)$res[$i]['empresa'];
    $telefone = $res[$i]['telefone'];
    $mensagem = $res[$i]['mensagem'];
   

    require("../sistema/painel/buscar_config.php");

    // 2) trava o registro pra não duplicar (se rodar 2 crons juntos)
    $st = $pdo->prepare("
      UPDATE whatsapp_agendamentos
      SET status = 'Processando'
      WHERE id = :id AND status = 'Pendente'
    ");
    $st->execute([':id' => $id_agd]);
    if ($st->rowCount() == 0) {
      continue;
    }

    
    // normaliza telefone (igual seu padrão)
    $telefone_envio = $telefone;

    // 4) chama seu arquivo que dispara (ele usa $api_whatsapp, $token_whatsapp, etc.)
    // aqui ele deve usar a variável $mensagem (ou $mensagem_whatsapp).
    // Então vamos garantir as duas:
    $mensagem = $mensagem;
    $mensagem_whatsapp = $mensagem;

    // arquivo do seu projeto que faz o envio conforme a API configurada
    require("texto.php");

    // 5) se enviou, marca como enviado / se deu erro, marca erro
    // texto.php geralmente seta $status_mensagem (no seu cron marketing você usa)
    $enviou = false;

    if (isset($status_mensagem)) {
      // ajuste conforme suas mensagens reais retornadas
      if (
        $status_mensagem == "Mensagem enviada com sucesso." ||
        $status_mensagem == "WhatsApp (Evolution): enviado com sucesso." ||
        $status_mensagem == "OK" ||
        stripos($status_mensagem, 'sucesso') !== false
      ) {
        $enviou = true;
      }
    } else {
      // se seu texto.php não seta status_mensagem, consideramos que executou
      // (você pode remover esse else se preferir)
      $enviou = true;
    }

    
    if ($enviou) {

    // 🔥 Remove da fila após envio
    $pdo->prepare("
        DELETE FROM whatsapp_agendamentos
        WHERE id = :id
    ")->execute([
        ':id' => $id_agd
    ]);

} else {

    $erro = isset($status_mensagem) ? $status_mensagem : 'Falha ao enviar.';

    $pdo->prepare("
        UPDATE whatsapp_agendamentos
        SET status='Erro',
            erro=:erro,
            tentativas = tentativas + 1
        WHERE id = :id
    ")->execute([
        ':id' => $id_agd,
        ':erro' => $erro
    ]);
}

    // limpa status_mensagem para não “vazar” para o próximo loop
    unset($status_mensagem);
  }
}

echo $linhas;