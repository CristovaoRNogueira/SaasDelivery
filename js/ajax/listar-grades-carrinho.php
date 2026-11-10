<?php 
@session_start();
require_once('../../sistema/conexao.php');
$id = $_POST['id'];
$sessao = $_SESSION['sessao_usuario'];
$id_produto = $_POST['produto'];
$id_carrinho = $_POST['id'];

//percorrer as grades do produto
$query20 =$pdo->query("SELECT * FROM grades where produto = '$id_produto' and tipo_item != 'Variação' order by posicao asc");
  $res20 = $query20->fetchAll(PDO::FETCH_ASSOC);
  $total_reg20 = @count($res20);
  if($total_reg20 > 0){  
     for($i20=0; $i20 < $total_reg20; $i20++){
      $id_da_grade = $res20[$i20]['id'];
      $nome_da_grade = $res20[$i20]['nome_comprovante'];
      $tipo_item_grade = $res20[$i20]['tipo_item'];


//buscar os itens selecionados pela grade
$query2 =$pdo->query("SELECT * FROM temp where carrinho = '$id_carrinho' and grade = '$id_da_grade'");
  $res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
  $total_reg2 = @count($res2);
  if($total_reg2 > 0){   

    echo '<b>'.$nome_da_grade.'</b><br>';

        for($i2=0; $i2 < $total_reg2; $i2++){
          foreach ($res2[$i2] as $key => $value){}
            $id_temp = $res2[$i2]['id'];        
          $id_item = $res2[$i2]['id_item']; 
           $tabela_item = $res2[$i2]['tabela'];   
            $tipagem_item = $res2[$i2]['tipagem']; 
            $grade_item = $res2[$i2]['grade']; 

            if($tipo_item_grade == 'Múltiplo'){
              $quant_item = '('.$res2[$i2]['quantidade'].')'; 
            }else{
              $quant_item = '';
            }
            
        

          $query3 =$pdo->query("SELECT * FROM itens_grade where id = '$id_item'");
          $res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
          $total_reg3 = @count($res3);
          $nome_item = $res3[0]['texto'];         
          if($i2 < ($total_reg2 - 1)){
           $nome_item .= ', ';
          }            
            echo '<i><span style="font-size:13px">'.$quant_item.$nome_item.'</span></i><br>';            
        }

      echo '<br>';
       

  }

}
}

?>

