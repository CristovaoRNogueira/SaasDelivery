<?php 
@session_start();
$nivel_usuario = @$_SESSION['nivel'];
if($nivel_usuario != 'Administrador'){
	echo 'Somente um Administrador pode mudar o status das mesas';
	exit();
}
require_once("../../../conexao.php");
$tabela = 'mesas';

$id = $_POST['id'];
$status = $_POST['status'];

$pdo->query("UPDATE $tabela SET status = '$status' where id = '$id'");

if($status == 'Fechada'){
	$pdo->query("UPDATE abertura_mesa SET status = '$status' where mesa = '$id' and status = 'Aberta'");
}

echo 'Editado';

?>