<?php 
@session_start();
$id_empresa = $_SESSION['empresa'];
$id_usuario = $_SESSION['id'];
require_once("../../conexao.php");
require_once("../buscar_config.php");
require_once ("../verificar.php");


$token_rel = 'M543664';
require_once("data_formatada.php");
ob_start();
include("produtos.php");
$html = ob_get_clean();

if ($tipo_rel != 'PDF') {
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
$options->set('isRemoteEnabled', TRUE);
$pdf = new DOMPDF($options);


//Definir o tamanho do papel e orientação da página
$pdf->set_paper('A4', 'portrait');

//CARREGAR O CONTEÚDO HTML
$pdf->load_html($html);

//RENDERIZAR O PDF
$pdf->render();
//NOMEAR O PDF GERADO


$pdf->stream(
	'produtos.pdf',
	array("Attachment" => false)
);



 ?>