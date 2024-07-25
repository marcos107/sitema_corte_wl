<?php

include('../login/verifica_login.php');
?>  
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

/*     */
    /* The modal_cadastrar container */
    .oi {
            display: block;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.4);
            /* Black w/ opacity */
            backdrop-filter: blur(5px);
            /* Add blur effect to items behind the modal_cadastrar */
        }

        /* modal_cadastrar content */
        .modal_Atualizar-content {
            background-color: #fefefe;
            margin: 5% auto;
            /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 30%;
            height: 25%;
            /* Could be more or less, depending on screen size */
            animation-name: tv-effect;
            animation-duration: 0.2s;
            animation-timing-function: ease-in-out;
            animation-fill-mode: forwards;
            opacity: 0;
            box-shadow: 0px 0px 20px 2px #fff, 0px 0px 10px 2px #fff;
            padding-bottom: 40%;
        }

















    /*     */
    /* The modal_cadastrar container */
    .modal_cadastrar {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.4);
            /* Black w/ opacity */
            backdrop-filter: blur(5px);
            /* Add blur effect to items behind the modal_cadastrar */
        }

        /* modal_cadastrar content */
        .modal_cadastrar-content {
            background-color: #fefefe;
            margin: 5% auto;
            /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 30%;
            height: 25%;
            /* Could be more or less, depending on screen size */
            animation-name: tv-effect;
            animation-duration: 0.2s;
            animation-timing-function: ease-in-out;
            animation-fill-mode: forwards;
            opacity: 0;
            box-shadow: 0px 0px 20px 2px #fff, 0px 0px 10px 2px #fff;
            padding-bottom: 40%;
        }

        /* Close button */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        @keyframes tv-effect {
            0% {
                padding-bottom: 0%;
                opacity: 1;
            }

            10% {
                padding-bottom: 2.5%;
                opacity: 1;
            }

            20% {
                padding-bottom: 5%;
                opacity: 1;
            }

            30% {
                padding-bottom: 7.5%;
                opacity: 1;
            }

            40% {
                padding-bottom: 10%;
                opacity: 1;
            }

            50% {
                padding-bottom: 12.5%;
                opacity: 1;
            }

            60% {
                padding-bottom: 15%;
                opacity: 1;
            }

            70% {
                padding-bottom: 17.5%;
                opacity: 1;
            }

            80% {
                padding-bottom: 20%;
                opacity: 1;
            }

            90% {
                padding-bottom: 22.5%;
                opacity: 1;
            }

            100% {
                padding-bottom: 25%;
                opacity: 1;
            }
        }

        input[type=text],
        input[type=password] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            box-sizing: border-box;
            border: 2px solid #ccc;
            border-radius: 4px;
            background-color: #f8f8f8;
            font-size: 16px;
        }
        .conteudo_modal_cadastrar_caregar{
            display: none;
            top:100%;
  justify-content: center;
  align-items: center;

        }
        .conteudo_modal_cadastrar_caregado{
            display: none;
        }

</style>

<body>


<div class="sidebar">
    <ul>
      <li><a  href="mensagens.php">Mensagens</a></li>
      <li><span  href="#">Gerenciar usuario</span></li>
      <li><a  href="configuracao.php">Configuração</a></li>
      <li><a  href="../login/logout.php">Sair</a></li>
  
    </ul>
  </div>

  


  
  <div class="content">
  <div class="lado_lado" id="mensagem"><?php include('atualiza_planilha_usuario.php'); ?></div>
  <div class="lado_lado element_top">
  <button class="open-button" onclick="openmodal_cadastrar()">Cadastrar novo usuario</button>

  </div>

 <!-- The modal_cadastrar container -->
    <div class="modal_cadastrar">
        <!-- modal_cadastrar content -->
        <div class="modal_cadastrar-content">
   
  <div class="conteudo_modal_cadastrar_caregado" id="conteudo_modal_cadastrar_caregado">
                <span class="close" onclick="closemodal_cadastrar()">&times;</span>
                <form method="post" action="">
                    <p>Cadastrar</p>
                <p>Nome</br>
   <input name="nome_cadastro" type="text"> </p>
   <p>Webhook</br>
   <input name="webhook_cadastro" type="text"> </p>
   <p>Whatzap</br>
    <input name="whatzap_cadastro" type="text"> </p>
    <p>Email</br>
    <input name="email_cadastro" type="text"> </p>
    <p>Senha</br>
    <input name="senha_cadastro" type="text"> </p>
    <p><input name="botao_cadastro" value="Adicionar" onclick="cadastrar_fechar()" type="submit"> </p>

    
    </form>
    </div>
    </div>
    </div>

    <div id='modal_atualizar'></div>
  
 <?php
  if(isset($_POST['nome_atualizar'])&&isset($_POST['webhook_atualizar'])&&isset($_POST['whatzap_atualizar'])&&isset($_POST['email_atualizar'])&&isset($_POST['senha_atualizar'])){
    isThreat($_POST['nome_atualizar']);
    isThreat($_POST['webhook_atualizar']);
    isThreat($_POST['whatzap_atualizar']);
    isThreat($_POST['email_atualizar']);
    isThreat($_POST['senha_atualizar']);


    $id = $_SESSION['id_atualiza'];
    if(isset($_POST['Ativar'])){
        $ativo = 'Ativo';
    }else{
        $ativo = 'Desativado';
    }
    if(select('users','*','usuario=\''.$_POST['nome_atualizar'].'\' and id!=\''.$id.'\'')==[]){
        update('users',['usuario_1_camadas','usuario','senha_1_camadas','senha','ativo','email','whatzap','webhook'],[hash('md5', strtolower(hash('md5', strtolower(hash('md5', $_POST['nome_atualizar'], false)), false)), false),$_POST['nome_atualizar'],hash('md5', strtolower(hash('md5', strtolower(hash('md5', $_POST['senha_atualizar'], false)), false)), false),$_POST['senha_atualizar'],$ativo,$_POST['email_atualizar'],$_POST['whatzap_atualizar'],$_POST['webhook_atualizar']],"id='".$id."'");
    }else{
        echo 'exixte';
    }
  }
 if(isset($_POST['nome_cadastro'])&&isset($_POST['webhook_cadastro'])&&isset($_POST['whatzap_cadastro'])&&isset($_POST['email_cadastro'])&&isset($_POST['senha_cadastro'])){
    isThreat($_POST['nome_cadastro']);
    isThreat($_POST['webhook_cadastro']);
    isThreat($_POST['whatzap_cadastro']);
    isThreat($_POST['email_cadastro']);
    isThreat($_POST['senha_cadastro']);
    

    if(select('users','*','usuario=\''.$_POST['nome_cadastro'].'\'')==[]){
        insert('users',['usuario_1_camadas','usuario','senha_1_camadas','senha','ativo','email','whatzap','webhook'],[hash('md5', strtolower(hash('md5', strtolower(hash('md5', $_POST['nome_cadastro'], false)), false)), false),$_POST['nome_cadastro'],hash('md5', strtolower(hash('md5', strtolower(hash('md5', $_POST['senha_cadastro'], false)), false)), false),$_POST['senha_cadastro'],'ativo',$_POST['email_cadastro'],$_POST['whatzap_cadastro'],$_POST['webhook_cadastro']]);
    }else{
        echo 'exixte';
    }
 }
 ?>
    

</body>
</html>
<script type="modal.js"></script>
<script>
      function mostrarMensagem() {
            fetch('atualiza_planilha_usuario.php')
            .then(response => response.text())
            .then(data => document.getElementById('mensagem').innerHTML  = data);
            

        }
        setInterval(mostrarMensagem, 1000);



        // Get the modal_cadastrar
        const modal_cadastrar = document.querySelector('.modal_cadastrar');
        const modal_atualizar = document.querySelector('.id_atualizar');
        // Get the close button
        const close = document.querySelector('.close');


        const ConteudoModalcadastrarCaregado = document.getElementById('conteudo_modal_cadastrar_caregado');
        const ModalcadastrarContent = document.getElementById('modal_cadastrar-content');

       

        // When the user clicks anywhere outside of the modal_cadastrar, close it
        window.addEventListener('click', (event) => {
            if (event.target == modal_cadastrar) {
               // ConteudoModalcadastrarCaregado.style.display = 'none';
                modal_cadastrar.style.display = 'none';
               // ModalcadastrarContent.style.color = '#fefefe';
            }
            if (event.target == modal_atualizar) {
                document.getElementById('modal_atualizar').innerHTML = "";
            }
        });

        // When the user clicks on the button, open the modal_cadastrar
        const openmodal_cadastrar = () => {
            modal_cadastrar.style.display = 'block';
 
            setTimeout(function () {
               
              
                ConteudoModalcadastrarCaregado.style.display = 'block';
                ModalcadastrarContent.style.color = '#fefefe';

            }, 300);

        }

        // When the user clicks on the close button, close the modal_cadastrar
        const closemodal_cadastrar = () => {
            ConteudoModalcadastrarCaregado.style.display = 'none';
                modal_cadastrar.style.display = 'none';
                ModalcadastrarContent.style.color = '#fefefe';
        }
        const closemodal_atualizar = () => {
            document.getElementById('modal_atualizar').innerHTML = "";
        }
        const cadastrar_fechar = () => {
            ConteudoModalcadastrarCaregado.style.display = 'none';
                modal_cadastrar.style.display = 'none';
                ModalcadastrarContent.style.color = '#fefefe';
        }









</script>