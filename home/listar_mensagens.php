<?php

include('../login/verifica_login.php');


if (isset($_GET['id'])) {
    $id = (filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT));
    include('../db/db_comandos.php');


    $dados = select('mensages', '*', 'id_user=\'' . $id . '\'');
    $enviar = "<tr><th>ID</th><th>Usuario</th><th>ID Cliente</th><th>Telefone do destino</th><th>Nome Template</th><th>Type</th><th>Parametros</th><th style=\"width:150px\">data</th></tr>";
    $p = 1;
    $pio = "";
    
    foreach ($dados as $key => $value) {
        $type = "";
        $id_user = "";
        $date = "";
        $parametros = "";


        $search_word = "type";
        
        if (strpos($value['mensagem'], $search_word) !== false) {
            $position = strpos($value['mensagem'], $search_word);
            $result = substr($value['mensagem'], $position + strlen($search_word), 12);
            if (strpos($result, 'template') !== false) {
                $type .= 'template';
              
                $search_word = "text";
                $posicao = 0;
                $parametro = 1;
                while (($posicao = strpos($value['mensagem'], $search_word, $posicao)) !== false) {
                    $inicio = $posicao + strlen($search_word);
                    $fim = strpos($value['mensagem'], "}", $inicio);
                    $resultado = substr($value['mensagem'], $inicio, $fim - $inicio);
                    $parametros .="<br/>Parametro ".$parametro.":". substr($resultado, 2)."<br/>";
                    $posicao = $fim;
                    $parametro++;
                }

              

            } else {
                $type .= 'Não identificado';
            }
       

            $pos = strpos($value['mensagem'], 'name');
           
            $name = substr($value['mensagem'], $pos + strlen('type'), strpos($value['mensagem'], ',', $pos) - $pos - strlen('type'));
            $pos1 = substr($value['mensagem'],strripos($value['mensagem'],"\"to\":"));
            $telefone_destino = "";
            for ($i=5; $i < strlen($pos1); $i++) { 
                if($pos1[$i]==','){
                    break;
                }
               if($pos1[$i]!='"' && $pos1[$i]!=' '){
                $telefone_destino.=$pos1[$i];
               }
            }
            //$telefone_destino=substr($pos1,0,strripos($pos1,""));
            //substr($value['mensagem'], $pos1 + strlen('to'), strpos($value['mensagem'], '}', $pos1) - $pos1 - strlen('to'));
           

        }

      
        $date .= $value['date'];
        if(is_numeric($value['id_user'])){
        
            $user = select_colum_str("users","usuario","id = '".$value['id_user']."'");
            }else{
                $user=$value['id_user'];
            }
        $enviar.="<tr class=\"template_border\"><th class=\"template_border\">".$p."</th><th class=\"template_border\">".$user."</th><th class=\"template_border\">".$value['id_cliente']."</th><th class=\"template_border\">".$telefone_destino."</th><th class=\"template_border\">".str_replace([":",'"'," "], "", $name)."</th><th class=\"template_border\">  ".$type."  </th><th class=\"texto_esquerda template_border\">".str_replace(['text:','"text":','"',"\\n"], "",$parametros)."</th><th class=\"template_border\">".$date."</th><tr>";

        $p++;
    }
    $p--;

    $retorna = [
        'status' => true,
        'dados' => '  <div id="modal_atualizar"></div>
    
    <!-- The modal_Atualizar container -->
    <div class="oi" id="id_atualizar">
    
        <!-- modal_Atualizar content -->
        <div class="modal_Atualizar-content">
        <span class="close" onclick="closemodal_listar_mensagens()">&times;</span>
        Possue '.$p.' Mensagens.
        <table class="template_border" width = 100%>
        ' . $enviar . '
        </table >
                
    
    </div>
    </div>'
    ];
    
}else if(isset($_GET['Usuario']) or isset($_GET['data']) or isset($_GET['id_cliente'])) {

    include('../db/db_comandos.php');

    $where = "";
    if(isset($_GET['Usuario'])){
        $users = select('users', '*', " usuario LIKE '%".isThreat($_GET['Usuario'])."%'");
        $i = 0;
        $where .= "(";
        foreach ($users as $key => $value) {
            if( $i == 0){
                $where .= " id_user LIKE '".$value['id']."'";
            }else{
                $where .= " or id_user LIKE '".$value['id']."'";
            }
            $i++;
        }
        $where .= ")";

    }
    if(isset($_GET['data'])){
        if(isset($_GET['Usuario'])){
            $where .= ' and';
        }
        $where .= " date LIKE '%".str_replace(",","-",isThreat($_GET['data']))."%'";
    }
    if(isset($_GET['id_cliente'])){
        if(isset($_GET['data']) or isset($_GET['Usuario'])){
            $where .= ' and';
        }

        $where .= " id_cliente LIKE '%".isThreat($_GET['id_cliente'])."%'";
    }
    
    
    

    $dados = select('mensages', '*', $where);
    $enviar = "<tr><th>ID</th><th>Usuario</th><th>ID Cliente</th><th>Telefone do destino</th><th>Nome Template</th><th>Type</th><th>Parametros</th><th style=\"width:150px\">data</th></tr>";
    $p = 1;
    $pio = "";

    foreach ($dados as $key => $value) {
        $type = "";
        $id_user = "";
        $date = "";
        $parametros = "";


        $search_word = "type";
        if(substr($value['mensagem'], 0, 1)!='e'){
        if (strpos($value['mensagem'], $search_word) !== false && strpos($value['mensagem'], 'template') !== false) {
            $position = strpos($value['mensagem'], $search_word);
            $result = substr($value['mensagem'], $position + strlen($search_word), 12);
            if (strpos($result, 'template') !== false) {
                $type .= 'template';
              
                $search_word = "text";
                $posicao = 0;
                $parametro = 1;
                while (($posicao = strpos($value['mensagem'], $search_word, $posicao)) !== false) {
                    $inicio = $posicao + strlen($search_word);
                    $fim = strpos($value['mensagem'], "}", $inicio);
                    $resultado = substr($value['mensagem'], $inicio, $fim - $inicio);
                    $parametros .="<br/>Parametro ".$parametro.":". substr($resultado, 2)."<br/>";
                    $posicao = $fim;
                    $parametro++;
                }

              

            } else {
                $type .= 'Não identificado';
            }
       
           
            $pos = strpos($value['mensagem'], 'name');
            
            $name = substr($value['mensagem'], $pos + strlen('type'), strpos($value['mensagem'], ',', $pos) - $pos - strlen('type'));
            
            $pos1 = substr($value['mensagem'],strripos($value['mensagem'],"\"to\":"));
            $telefone_destino = "";
            for ($i=5; $i < strlen($pos1); $i++) { 
                if($pos1[$i]==','){
                    break;
                }
               if($pos1[$i]!='"' && $pos1[$i]!=' '){
                $telefone_destino.=$pos1[$i];
               }
            }
           

        }

      
        $date .= $value['date'];
        if(is_numeric($value['id_user'])){
        
            $user = select_colum_str("users","usuario","id = '".$value['id_user']."'");
            }else{
                $user=$value['id_user'];
            }
        $enviar.="<tr class=\"template_border\"><th class=\"template_border\">".$p."</th><th class=\"template_border\">".$user."</th><th class=\"template_border\">".$value['id_cliente']."</th><th class=\"template_border\">".$telefone_destino."</th><th class=\"template_border\">".str_replace([":",'"'," "], "", $name)."</th><th class=\"template_border\">  ".$type."  </th><th class=\"texto_esquerda template_border\">".str_replace(['text:','"text":','"',"\\n"], "",$parametros)."</th><th class=\"template_border\">".$date."</th><tr>";
        $p++;
    }
    }
    $p--;

    $retorna = [
        'status' => true,
        'dados' => '  <div id="modal_atualizar"></div>
    
    <!-- The modal_Atualizar container -->
    <div class="oi" id="id_atualizar">
    
        <!-- modal_Atualizar content -->
        <div class="modal_Atualizar-content">
        <span class="close" onclick="closemodal_listar_mensagens()">&times;</span>
        Possue '.$p.' Mensagens.
        <table class="template_border" width = 100%>
        ' . $enviar . '
        </table >
                
    
    </div>
    </div>'
    ];

    $retorna = [
        'status' => true,
        'dados' => '  <div id="modal_atualizar"></div>
    
    <!-- The modal_Atualizar container -->
    <div class="oi" id="id_atualizar">
    
        <!-- modal_Atualizar content -->
        <div class="modal_Atualizar-content">
        <span class="close" onclick="closemodal_listar_mensagens()">&times;</span>
        Possue '.$p.' Mensagens.
        <table class="template_border" width = 100%>
        ' . $enviar . '
        </table >
                
    
    </div>
    </div>'
    ];
    
    
}else {
    $retorna = ['status' => false, 'msg' => "erro", 'dados' => "erro"];

}
echo json_encode($retorna);



?>