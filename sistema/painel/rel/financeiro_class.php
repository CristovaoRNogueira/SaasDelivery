<?php 
@session_start();
$id_empresa = $_SESSION['empresa'];
$id_usuario = $_SESSION['id'];
require_once("../../conexao.php");
require_once("../buscar_config.php");
require_once ("../verificar.php");
$id_usuario = @$_SESSION['id'];



$filtro_data = $_POST['filtro_data'];
$dataInicial = $_POST['dataInicial'];
$dataFinal = $_POST['dataFinal'];
$filtro_tipo = $_POST['filtro_tipo'];
$filtro_lancamento = $_POST['filtro_lancamento'];
$filtro_pendentes = $_POST['filtro_pendentes'];

ob_start();
include("financeiro.php");
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
	'financeiro.pdf',
	array("Attachment" => false)
);

 ?>