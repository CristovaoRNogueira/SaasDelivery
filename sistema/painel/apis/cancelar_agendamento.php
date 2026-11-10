<?php

if(@$referencia_agendamento == ""){
  $referencia_agendamento = 'receber';
}
// ===============================
// 1️⃣ Excluir agendamento vinculado
// ===============================
$delAgd = $pdo->prepare("
    DELETE FROM whatsapp_agendamentos
    WHERE referencia = :referencia
    AND id_ref = :id_ref
");
$delAgd->execute([
  ':referencia' => $referencia_agendamento,
    ':id_ref' => $id
]);


?>