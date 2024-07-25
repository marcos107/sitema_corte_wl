<?php

include('../login/verifica_login.php');


if (isset($_GET['id'])) {
	$id = (filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT));
    include('../db/db_comandos.php');


    $dados = select('users', '*','id=\''.$id.'\'');
  
    $_SESSION['id_atualiza'] = $id;
    foreach ($dados as $key => $value) {
        $usuario =  $value['usuario'];
        $senha =  $value['senha'];
        $ativo = $value['ativo'];
        $email = $value['email'];
        $whatzap = $value['whatzap'];
        $webhook =  $value['webhook'];
    }
    if($ativo=="Ativo"){
        $ativo = '<input type = "checkbox" id = "subscribeNews" name = "Ativar"  checked> Ativada';
    }else{
        $ativo = '<input type = "checkbox" id = "subscribeNews" name = "Ativar" > Ativar';
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
   <input name="nome_atualizar" value="'.$value['usuario'].'" type="text"> </p>
   <p>Webhook</br>
   <input name="webhook_atualizar" value="'.$value['webhook'].'" type="text"> </p>
   <p>Whatzap</br>
    <input name="whatzap_atualizar" value="'.$value['whatzap'].'" type="text"> </p>
    <p>Email</br>
    <input name="email_atualizar" value="'.$value['email'].'" type="text"> </p>
    <p>Senha</br>
    <input name="senha_atualizar" value="'.$value['senha'].'" type="text">   '.$ativo.' </p>
    <p><center><input name="botao_atualizar" class="btn first" value="Atualizar Conta" onclick="Atualizar_fechar()" type="submit"></center> </p>

    
    </form>
    
    </div>
    </div>'];
   
}else{
    $retorna = ['status'=> false, 'msg' => "erro", 'dados' => "erro"];

}
echo json_encode($retorna);



?>

