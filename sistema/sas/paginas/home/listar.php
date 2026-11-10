<?php
$tabela = 'empresas';
require_once("../../../conexao.php");

$buscar = @$_POST['busca'];

$query = $pdo->query("SELECT * from empresas where nome like '%$buscar%'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if($linhas > 0) {

	for($i=0; $i<$linhas; $i++) {
            $nome_empresa = $res[$i]['nome'];    
            $id_empresa = $res[$i]['id'];
            $data_cad = $res[$i]['data_cad'];
            $data_cadF = implode('/', array_reverse(@explode('-', $data_cad)));
            $ativo = $res[$i]['ativo'];
            
            // Contas vencidas
            $query2 = $pdo->query("SELECT * from receber_sas where cliente = '$id_empresa' and pago != 'Sim' and vencimento < curDate()");
            $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
            $contas_vencidas = @count($res2);
            
            // Logo da empresa
            $query4 = $pdo->query("SELECT * from config where empresa = '$id_empresa'");
            $res4 = $query4->fetchAll(PDO::FETCH_ASSOC);
            $logo_empresa = @$res4[0]['icone'];            
           
            // Gerar uma cor de fundo suave para o card
            $card_colors = ['#f0f7ff', '#f0fff7', '#fff7f0', '#f7f0ff', '#fffaf0'];
            $card_color = $card_colors[$i % count($card_colors)];

            if($ativo == 'Sim'){
            	$icone_ativo = 'check-circle';
            	$texto_ativo = 'Ativo';
            	$cor_ativo = 'text-success';
            }else{
            	$icone_ativo = 'times-circle';
            	$texto_ativo = 'Inativo';
            	$cor_ativo = 'text-danger';
            }

            $nome_empresaF = mb_strtoupper($nome_empresa);

            if($contas_vencidas > 0){
                $classe_venc = 'text-danger';
            }else{
                $classe_venc = 'text-success';
            }


            //total de clientes
            $query2 = $pdo->query("SELECT * from clientes where empresa = '$id_empresa' ");
            $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
            $total_clientes = @count($res2);

            //total de funcionarios
            $query2 = $pdo->query("SELECT * from usuarios where empresa = '$id_empresa' ");
            $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
            $total_funcionarios = @count($res2);

            //total de clientes
            $query2 = $pdo->query("SELECT * from dispositivos where empresa = '$id_empresa' ");
            $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
            $total_dispositivos = @count($res2);


echo <<<HTML

 <div class="company-card" onclick="abrirEmpresa('{$id_empresa}')" data-index="{$i}" style="background-color: {$card_color}">
        <div class="company-card-banner">
            <div class="company-card-badge {$cor_ativo}">
                <i class="fa fa-{$icone_ativo}"></i>
                {$texto_ativo}
            </div>
        </div>
        
        <div class="company-card-logo esc">
            <img  src="../img/{$logo_empresa}" alt="Logo" loading="lazy" decoding="async">
        </div>
        
        <div class="company-card-content">
            <div class="company-card-header">
                <h3 class="company-card-title">{$nome_empresaF}</h3>
                <div class="company-card-subtitle">
                    <i class="fa fa-money {$classe_venc}"></i>
                    <span>{$contas_vencidas} Contas Vencidas</span>

                     <div class="informacoes_card_empresa">
        <span><i class="fa fa-user fa-xs" style="margin-right: 3px;"></i> {$total_clientes} Clientes</span>
        <span><i class="fa fa-users fa-xs" style="margin-right: 3px;"></i> {$total_funcionarios} Funcionários</span>
        <span><i class="fa fa-microchip fa-xs" style="margin-right: 3px;"></i> {$total_dispositivos} Dispositivos</span>
    </div>
                </div>
            </div>
          
            
            <div class="company-card-footer">
                <div class="company-card-action">
                    <span>Acessar Empresa</span>
                    <i class="fa fa-arrow-right"></i>
                </div>
            </div>
        </div>
    </div>

HTML;

}

}else {
	echo '<small>Nenhuma Empresa Encontrada!</small>';
}

?>


