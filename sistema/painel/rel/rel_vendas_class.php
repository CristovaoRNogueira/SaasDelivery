<?php 
@session_start();
$id_empresa = $_SESSION['empresa'];
$id_usuario = $_SESSION['id'];
require_once("../../conexao.php");
require_once("../buscar_config.php");
require_once ("../verificar.php");


$dataInicial = $_POST['dataInicial'];
$dataFinal = $_POST['dataFinal'];
$horaInicial = $_POST['horaInicial'];
$horaFinal = $_POST['horaFinal'];
$status = urlencode($_POST['status']);
$forma_pgto = urlencode($_POST['forma_pgto']);
$tipo = urlencode($_POST['tipo']);
$impressao = urlencode($_POST['impressao']);

if($impressao == 'Sim'){
$token_rel = 'M543664';
require_once("data_formatada.php");
ob_start();
include("imprimir_vendas.php");
$html = ob_get_clean();
	exit();
}


$token_rel = 'M543664';
require_once("data_formatada.php");
ob_start();
include("rel_vendas.php");
$html = ob_get_clean();


if($tipo_rel != 'PDF'){
	echo $html;
	exit();
}

//CARREGAR DOMPDF
require_once '../dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

header("Content-Transfer-Encoding: binary");
header("Content-Type: image/png");

//INICIALIZAR A CLASSE DO DOMPDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$pdf = new DOMPDF($options);



//Definir o tamanho do papel e orientação da página
$pdf->set_paper('A4', 'portrait');

//CARREGAR O CONTEÚDO HTML
$pdf->load_html($html);

//RENDERIZAR O PDF
$pdf->render();

//NOMEAR O PDF GERADO
$pdf->stream(
'vendas.pdf',
array("Attachment" => false)
);
?>