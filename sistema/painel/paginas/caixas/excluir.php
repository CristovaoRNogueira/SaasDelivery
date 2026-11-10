<?php 
@session_start();
$id_empresa = $_SESSION['empresa'];
require_once("../../../conexao.php");
require_once("../../buscar_config.php");
if($id_empresa == ""){
	echo 'Sem Sessão da Empresa, refaça o Login!';
	exit();
}
$tabela = 'caixas';



$nivel_usuario = @$_SESSION['nivel'];

$id = $_POST['id'];



$query = $pdo->query("SELECT * FROM $tabela where valor_fechamento is not null and empresa = '$id_empresa' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0 and $nivel_usuario != 'Administrador'){
	echo 'Somente um Administrador pode excluir um caixa já fechado!';
	exit();

}

$pdo->query("DELETE FROM $tabela where id = '$id'");


echo 'Excluído com Sucesso';


?>