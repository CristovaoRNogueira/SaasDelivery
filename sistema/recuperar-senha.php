<?php
require_once ("conexao.php");

//recuperar email
$email = filter_var(@$_POST['email'], @FILTER_SANITIZE_STRING);

$query = $pdo->prepare("SELECT * from usuarios where email = :email and nivel != 'Administrador'");
$query->bindValue(":email", "$email");
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {
    $telefone = $res[0]['telefone'];

    $token_email = hash('sha256', time());

    $q = $pdo->prepare("UPDATE usuarios SET token=? WHERE email=?");
    $q->execute([$token_email, $email]);

    $reset_link = $url_sistema .'sistema/'. 'resetar-senha.php' . '?email=' . $email . '&token=' . $token_email;


    //envio do email
    $destinatario = $email;
    $assunto = utf8_decode($nome_sistema . ' - Recuperação de Senha');
    $mensagem = utf8_decode('Clique no Link ao lado para atualizar sua senha:' . $reset_link);
    $cabecalhos = "From: " . $email_sistema;

    @mail($destinatario, $assunto, $mensagem, $cabecalhos);

    //disparar para o telefone do cliente a recuperação
    if ($api_whatsapp != 'Não' and $telefone != '') {
        $telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone);

        $mensagem_whatsapp = '*' . $nome_sistema . '*%0A%0A';
        $mensagem_whatsapp .= '🤩 _Link para Recuperação de Senha_ %0A%0A';
        $mensagem_whatsapp .= $reset_link;

        require ("painel/apis/texto.php");

    }

    //echo $reset_link;
    //exit();

    echo 'Recuperado com Sucesso';
} else {
    echo 'Esse email não está Cadastrado!';
}

?>