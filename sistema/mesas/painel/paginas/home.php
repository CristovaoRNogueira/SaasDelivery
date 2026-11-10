<?php
if (@$home == 'ocultar') {
    echo "<script>window.location='index'</script>";
    exit();
}

#######TOTAL MESAS#############################
$query = $pdo->query("SELECT * from mesas");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_mesas = @count($res);


##### MESAS INATIVAS###############################################
$query = $pdo->query("SELECT * from mesas WHERE ativo = 'Não'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_mesas_inativas = @count($res);


#### TOTOAL DE MESAS DISPONÍVEIS######################################
$query = $pdo->query("SELECT * from mesas WHERE ativo = 'Sim' and status = 'Fechada'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_mesas_ativas = @count($res);





##### MESAS INDISPONÍVEIS###############################################
$query = $pdo->query("SELECT * from mesas WHERE ativo = 'Sim' and status = 'Aberta'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_mesas_abertas = @count($res);









############## TAREFAS ########################################################################
$query = $pdo->query("SELECT * from tarefas where usuario = '$id_usuario' and status = 'Agendada' and data <= curDate() and hora < curTime() order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$tarefas_atrasadas = @count($res);

$query = $pdo->query("SELECT * from tarefas where usuario = '$id_usuario' and status = 'Agendada' order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$tarefas_pendentes = @count($res);


$query = $pdo->query("SELECT * from tarefas where usuario = '$id_usuario' and data = curDate() order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$tarefas_agendadas_hoje = @count($res);


//Tarefas Concluidas
$query = $pdo->query("SELECT * from tarefas where usuario = '$id_usuario' and status = 'Concluída' and data = curDate() order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$tarefas_concluidas_hoje = @count($res);





?>
<!-- Slider -->
<div class="swiper-container slidertoolbar">
    <div class="swiper-wrapper">



        <div class="swiper-slide" style="margin-top:50px; background-image:url(images/slide2.png); ">
            <div class="slider_trans" style="">

                <div class="swiper-container-team" style="margin-top: 5px">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide " style="width:46%; margin-left:4px; margin-top:3px; margin-right:3px; padding:5px; box-shadow: 2px 2px 0px 0px rgba(0, 0, 0, 0.1); border-radius:4px; background: #f5f5f5;">

                            <a href="novo_pedido">

                                <img style="float:right;" src="images/cards/mesa_verde.png" width="42px" />
                                <span style="float:left; color:#363636; margin-left:0px; font-size: 9px">TOTAL DE MESAS</span><br>
                                <span style="font-size: 19px; color:red; margin-left:0; float:left; "><?php echo $total_mesas ?> </span><br>

                                <div
                                    style="border-top:1px solid #c7c5c5; text-align: right; margin-top: 7px; margin-right: 8px; width:100%; padding:2px">

                                    <span style="color:#363636; margin-left:0px; font-size: 10px; padding: 2px;">
                                        MESAS INATIVAS

                                        <img style="margin-left: 0px; margin-left:2px; margin-top:4px;"
                                            src="images/cards/down3.png" width="6px" />
                                    </span>

                                    <span
                                        style="color: red; font-size: 14px; margin-left: 5px; margin-right: 8px; "><?php echo $total_mesas_inativas ?>

                                    </span>
                                </div>
                            </a>


                        </div>
                        <div class="swiper-slide" style="margin-right:3px; margin-top:3px; width:46%; padding:5px; box-shadow: 2px 2px 0px 0px rgba(0, 0, 0, 0.1); border-radius:4px; background: #f5f5f5;">

                            <a href="novo_pedido">

                                <img style="float:right;" src="images/cards/mesa_vermelha.png" width="42px" />
                                <span style="float:left; color:#363636; margin-left:0px; font-size: 9px">MESAS DIPONÍVEIS</span><br>
                                <span style="font-size: 19px; color:green; margin-left:0; float:left;"><?php echo $total_mesas_ativas ?></span><br>

                                <div
                                    style="border-top:1px solid #c7c5c5; text-align: right; margin-top: 7px; margin-right: 8px; width:100%; padding:2px">

                                    <span style="color:#363636; margin-left:0px; font-size: 10px; padding: 2px;">
                                        MESAS ABERTAS

                                        <img style="margin-left: 0px; margin-left:2px; margin-top:4px;"
                                            src="images/cards/up3.png" width="6px" />
                                    </span>

                                    <span
                                        style="font-size: 14px; margin-left: 5px; margin-right: 8px; color:green"><?php echo $total_mesas_abertas ?>

                                    </span>
                                </div>
                            </a>

                        </div>

                    </div>
                </div>





                <div class="swiper-container-team" style="margin-top: -15px">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide " style="width:46%; margin-left:4px; margin-top:3px; margin-right:3px; padding:5px; box-shadow: 2px 2px 0px 0px rgba(0, 0, 0, 0.1); border-radius:4px; background: #f5f5f5;">

                            <a href="tarefas">

                                <img style="float:right;" src="images/cards/data.png" width="35px" />
                                <span style="float:left; color:#363636; margin-left:0px; font-size: 9px">TAREFAS
                                    PENDENTES</span><br>
                                <span
                                    style="font-size: 19px; color:red; margin-left:0; float:left; "><?php echo @$tarefas_pendentes ?></span><br>

                                <div
                                    style="border-top:1px solid #c7c5c5; text-align: right; margin-top: 7px; margin-right: 8px; width:100%; padding:2px">

                                    <span style="color:#363636; margin-left:0px; font-size: 10px; padding: 2px;">
                                        ATRASADAS

                                        <img style="margin-left: 0px; margin-left:2px; margin-top:4px;"
                                            src="images/cards/down3.png" width="6px" />
                                    </span>

                                    <span
                                        style="color: red; font-size: 14px; margin-left: 5px; margin-right: 8px; "><?php echo @$tarefas_atrasadas ?>

                                    </span>
                                </div>
                            </a>


                        </div>
                        <div class="swiper-slide" style="margin-right:3px; margin-top:3px; width:46%; padding:5px; box-shadow: 2px 2px 0px 0px rgba(0, 0, 0, 0.1); border-radius:4px; background: #f5f5f5;">

                            <a href="tarefas" style="  ">

                                <img style="float:right;" src="images/cards/data.png" width="35px" />
                                <span style="float:left; color:#363636; margin-left:0px; font-size: 9px">TAREFAS
                                    HOJE</span><br>
                                <span
                                    style="font-size: 19px; color:green; margin-left:0; float:left;"><?php echo @$tarefas_agendadas_hoje ?></span><br>

                                <div
                                    style="border-top:1px solid #c7c5c5; text-align: right; margin-top: 7px; margin-right: 8px; width:100%; padding:2px">

                                    <span style="color:#363636; margin-left:0px; font-size: 10px; padding: 2px;">
                                        CONCLUÍDAS HOJE

                                        <img style="margin-left: 0px; margin-left:2px; margin-top:4px;"
                                            src="images/cards/up3.png" width="6px" />
                                    </span>

                                    <span
                                        style="font-size: 14px; margin-left: 5px; margin-right: 8px; color:green"><?php echo @$tarefas_concluidas_hoje ?>

                                    </span>
                                </div>
                            </a>

                        </div>

                    </div>
                </div>






            </div>
        </div>




    </div>

</div>





<div class="swiper-toolbar">
    <div class="swiper-pagination-toolbar"></div>
    <div class="swiper-wrapper">
        <div class="swiper-slide toolbar-icon">
            <div style="width: 150%"> 
            <a  class="<?php echo $novo_pedido ?>" href="novo_pedido"><img src="images/icons/blue/food.png" alt=""
                    title="" /><span>NOVO PEDIDO</span></a>  
                     
            <a  class="<?php echo $tarefas ?>" href="tarefas"><img src="images/icons/blue/blog.png" alt=""
                    title="" /><span>TAREFAS</span></a>
                    </div> 
        </div>


    </div>
</div>




</div>
</div>
</div>

</div>
</div>