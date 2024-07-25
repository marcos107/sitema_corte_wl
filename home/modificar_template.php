<?php

include('../login/verifica_login.php');


if (isset($_GET['id'])) {
	$id = (filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT));
    include('../db/db_comandos.php');


    $dados = select('templates', '*','id=\''.$id.'\'');
  
    $_SESSION['id_atualiza'] = $id;
    foreach ($dados as $key => $value) {
        $nome =  $value['nome'];
        $text =  $value['text'];
        $status = $value['status'];
      
    }
    if($status=="Ativo"){
        $status = '<input type = "checkbox" id = "subscribeNews" name = "Ativar"  checked> Ativada';
    }else{
        $status = '<input type = "checkbox" id = "subscribeNews" name = "Ativar" > Ativar';
    }

    $retorna = ['status'=> true, 'dados' => '  <div id="modal_atualizar"></div>
    
    <!-- The modal_Atualizar container -->
    <div class="oi" id="id_atualizar">
    
        <!-- modal_Atualizar content -->
        <div class="modal_Atualizar-content">
        <span class="close" onclick="closemodal_atualizar()">&times;</span>
   
          
                <form method="post" action="">
                    <p>Atualizar</p>

                <p>Nome</br>
   <input name="nome_atualizar" value="'. $nome.'" type="text"> </p>
   <p>Texto</br>
   
   <textarea name="text_atualizar" cols="100%" rows="5" style="width: 453px; height: 176px;"> '.$text.' </textarea ></p>
   <p>'.$status.' </p>
    <p><center><input name="botao_atualizar" class="btn first" value="Atualizar Conta" onclick="Atualizar_fechar()" type="submit"></center> </p>

    
    </form>
    
    </div>
    </div>'];
   
}else{
    $retorna = ['status'=> false, 'msg' => "erro", 'dados' => "erro"];

}
echo json_encode($retorna);



?>

