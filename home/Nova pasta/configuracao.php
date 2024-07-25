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
  .sidebar {
  width: 200px;
  height: 100%;
  position: fixed;
  top: 0;
  left: 0;
  background-color: #f5f5f5;
  border-right: 1px solid #ccc;
}

.content {
  margin-left: 200px;
  padding: 20px;
  height: 40%;
}
.sidebar ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.sidebar li {
  border-bottom: 1px solid #ccc;
}

.sidebar li:last-child {
  border-bottom: none;
}

.sidebar a{
  display: block;
  padding: 10px;
  color: #333;
  text-decoration: none;
}

.sidebar a:hover {
  background-color: #333;
  color: #fff;
}
.sidebar span{
    background-color: #FFFFFF;
  color: #333;
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
  <form method="post" action="">
                <p>Tempo de duração do token:</br>
   <input name="temp_token" value="<?php echo $tempo_token; ?>" type="number" pattern="[0-9]+([,\.][0-9]+)?" min="0" step="any"> </p>
   <p>Numeros de caracteres que haverá no token:</br>
   <input name="num_caracter_token" value="<?php echo $numero_caracter_token; ?>" type="number"> </p>
   <p>Tempo em que um telefone fica privado para apenas uma aplicação:</br>
    <input name="temp_telefone" value="<?php echo $tempo_telefone; ?>" type="number" pattern="[0-9]+([,\.][0-9]+)?" min="0" step="any"> </p>
    
    <p><input name="atualizar_config" value="Atualizar" type="submit"> </p>

    
    </form>
  </div>
 

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