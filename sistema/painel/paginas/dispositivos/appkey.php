<?php 
@session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$tabela = 'dispositivos';
require_once("../../../conexao.php");
$id_empresa = @$_SESSION['empresa'];
require_once("../../buscar_config.php");

$appkey = $_POST['appkey'];

if (empty($appkey)) 
{
    $result = $pdo->query("SELECT * FROM dispositivos WHERE status_api IS NULL and empresa = '$id_empresa' LIMIT 1");
    $dispositivo = $result->fetch(PDO::FETCH_ASSOC);

    if (!$dispositivo) 
    {
        $nucleo = rand(1, 4);
        $versao = 2;
        $appkey = uniqid('appkey_', true);
        $stmt = $pdo->prepare("INSERT INTO dispositivos SET appkey = :appkey, empresa = '$id_empresa'");
        $stmt->bindParam(':appkey', $appkey);
        
        if($stmt->execute())
        {
            $status = 200;

            if($alterar_api_whatsapp == 'Sim'){
                //salvar no config esse appKey
            $pdo->query("UPDATE config SET token_whatsapp = '$appkey' where empresa = '$id_empresa'");
            }else{
                $pdo->query("UPDATE config SET token_whatsapp = '$appkey', instancia_whatsapp = '$instancia_whatsapp' where empresa = '$id_empresa'");
            }

            

        }else{
            $status = 500;
        }
    } 
    else 
    {
        $appkey = $dispositivo['appkey'];
        $status = 200;
    }
}
else
{
    $result = $pdo->query("SELECT * FROM dispositivos WHERE appkey = '$appkey' and empresa = '$id_empresa' LIMIT 1");
    $dispositivo = $result->fetch(PDO::FETCH_ASSOC); 
    
    if($dispositivo)
    {
        $status = 200;
    }else{
        $status = 404;
    }
}

$response = json_encode(['appkey' => $appkey, 'status' => $status]);
echo $response;