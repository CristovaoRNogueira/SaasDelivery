<?php 
@session_start();
$id_empresa = @$_SESSION['empresa'];
require_once("../../conexao.php");
require_once ("../verificar.php");

$pesquisar = @$_POST['pesquisar'];
$filtro_busca = @$_POST['filtro_busca'];

$token_rel = "M543661";
ob_start();
include("marketing.php");
$html = ob_get_clean();

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
	'marketing.pdf',
	array("Attachment" => false)
);



 ?>