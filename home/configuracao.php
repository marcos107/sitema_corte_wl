<!DOCTYPE html>
<html>
<head>
  <title>Gráfico de linha</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
	<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
</head>
<style>
   body {
        background-color: #212127;
        color: #98ABB4;
        overflow-x: hidden; /* Desabilita a barra de rolagem horizontal */
        word-wrap: break-word;
        caret-color: transparent;
    }

    .sidebar {
        top: 20px;
        width: 200px;
        height: 100%;
        position: fixed;
        top: 0;
        left: 0;
        background-color: #373F52;
        border-right: 1px solid #788490;
    }

    .content {
        
        margin-left: 400px;
        margin-right: 200px;
       
        height: 40%;
        font-family: Merriweather,Book Antiqua,Georgia,Century Schoolbook,serif;
        font-size: 1em;
    
    }
    .content h1 {
        color: #fff;
    }
    .sidebar ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .titulo{
        color: rgba(255, 255, 255, 0.8);
    }
    .titulo p{
        color: rgba(255, 255, 255, 0.8);
    }

    .sidebar li:last-child {
        border-bottom: none;
    }

    .sidebar a {
        display: block;
        padding: 10px;
        color: #98ABB4;
        text-decoration: none;
    }

    .sidebar a:hover {
        color: #ffffff;
    }

    .sidebar span {
     
        color: #fff;
        display: block;
        padding: 10px;
        text-decoration: none;

    }
.lado_lado{
    display: inline-block;
        margin: 0 10px;
}
.element_top {
        position: absolute;
        
    }

    .btn {

box-sizing: border-box;
appearance: none;
background-color: transparent;
border: 2px solid #6697FF;
border-radius: 0.6em;
color: #6697FF;
cursor: pointer;
display: flex;
align-self: center;
font-size: 13px;
font-weight: 400;
line-height: 1;
margin: 5px;
padding: 1.2em 2.8em;
text-decoration: none;
text-align: center;


font-family: 'Montserrat', sans-serif;
font-weight: 700;

&:hover,
&:focus {
    color: rgba(255, 255, 255, 0.9);
    outline: 0;
}
}
.input_text{
            color: #fff;
            padding: 0px 5px;
            width: 20%;
            background-color: #212127;
            
        
        }
.rounded-input {
  padding:5px;
  border-radius:10px;
}

.first {
        transition: box-shadow 300ms ease-in-out, color 300ms ease-in-out;

        &:hover {
            box-shadow: 0 0 40px 40px #6697FF inset;
        }
    }
    .code_escuro{
        border-style:solid;
        border-style: inset ;
border-width: 1px;
border-color: #A6B4B4;
        background-color: #2c2c31;
       
  padding: 3px;

     
        
        
    }
</style>
<body>
<?php

include('../login/verifica_login.php');
?>  
<div class="sidebar">
    <ul>
      <li><a  href="mensagens.php">Mensagens</a></li>
      <li><a  href="usuarios.php">Gerenciar usuario</a></li>
      <li><span >Configuração</span></li>
      <li><a  href="templates.php">Templates</a></li>
      <li><a  href="exemplos.php">Exemplos</a></li>
      <li><a  href="../login/logout.php">Sair</a></li>
  
    </ul>
  </div>

  
<?php
include('../db/db_comandos.php');
$tempo_token=select('config','tempo_token');

if(!empty($tempo_token)){
  $tempo_token = $tempo_token[0]['tempo_token'];
}else{
  $tempo_token = '';
}


$tempo_telefone=select('config','tempo_telefone');
if(!empty($tempo_telefone)){
  $tempo_telefone = $tempo_telefone[0]['tempo_telefone'];
}else{
  $tempo_telefone = '';
}

$numero_caracter_token=select('config','num_caracteres_aleatorios_token');
if(!empty($numero_caracter_token)){
  $numero_caracter_token = $numero_caracter_token[0]['num_caracteres_aleatorios_token'];
}else{
  $numero_caracter_token = '';
}




?>



  
  <div class="content">
    <div class="code_escuro">
      
  <form method="post" action="">
                <p>Tempo de duração do token:</br>
   <input class="rounded-input input_text" name="temp_token" value="<?php echo $tempo_token; ?>" type="number" pattern="[0-9]+([,\.][0-9]+)?" min="0" step="any"> </p>
   <p>Numeros de caracteres que haverá no token:</br>
   <input class="rounded-input input_text" name="num_caracter_token" value="<?php echo $numero_caracter_token; ?>" type="number"> </p>
   <p>Tempo em que um telefone fica privado para apenas uma aplicação:</br>
    <input class="rounded-input input_text" name="temp_telefone" value="<?php echo $tempo_telefone; ?>" type="number" pattern="[0-9]+([,\.][0-9]+)?" min="0" step="any"> </p>
    
    <p><input name="atualizar_config" class="btn first" value="Atualizar" type="submit"> </p>

    
    </form>

  </div></div>
 

<?php
if(isset($_POST["atualizar_config"])){
  if(isset($_POST["temp_token"])){
    if(is_numeric($_POST["temp_token"])){
    update('config',['tempo_token'],[$_POST["temp_token"]]);
  }
  }
 
  if(isset($_POST["num_caracter_token"])){
    if(is_numeric($_POST["num_caracter_token"])){
    update('config',['num_caracteres_aleatorios_token'],[$_POST["num_caracter_token"]]);
  }}
  if(isset($_POST["temp_telefone"])){
    if(is_numeric($_POST["temp_telefone"])){
    update('config',['tempo_telefone'],[$_POST["temp_telefone"]]);
  }
 
}
reload_l();
}
?>
  

</body>
</html>