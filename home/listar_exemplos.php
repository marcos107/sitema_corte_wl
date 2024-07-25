<?php

include('../login/verifica_login.php');


if (isset($_GET['nome'])) {
    $nome = (filter_var($_GET['nome'], FILTER_SANITIZE_NUMBER_INT));
    include('../db/db_comandos.php');


    $dados = select('users', '*', 'usuario=\'' . $nome . '\'');
    $enviar = "<tr><th>ID</th><th>ID Cliente</th><th>Usuario</th><th>Type</th><th>Parametros</th><th style=\"width:150px\">data</th></tr>";
  
    
    foreach ($dados as $key => $value) {
       
    }


    $retorna = [
        'status' => true,
        'dados' => $enviar 
        
    ];
    
    
}else {
    $retorna = ['status' => false, 'msg' => "erro", 'dados' => "erro"];

}
echo json_encode($retorna);



?>