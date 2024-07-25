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
    .lado_lado {
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
        background-color: #000;
        margin: 2% auto;
        /* 15% from the top and centered */
        padding: 20px;
        border: 1px solid #888;
        width: 30%;
        height: 50%;
        /* Could be more or less, depending on screen size */
      
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
    .titulo{
        color: rgba(255, 255, 255, 0.8);
    }
    .borde{
        border: 5px solid #888;
        margin: 5px;
    }
    /* modal_cadastrar content */
    .modal_cadastrar-content {
        background-color: #212127;
            margin: 2% auto;
            /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 30%;
           
            /* Could be more or less, depending on screen size */
            
            height: 80%;
    }

    /* Close button */
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }
    .but_escuro{
        border-style:solid;
        border-style: inset ;
border-width: 1px;
border-color: #A6B4B4;
        background-color: #2c2c31;
       
  padding: 3px;

        width: 105%;
        
    }
    .close:hover,
    .close:focus {
        color: #fff;
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
    .text_cor{
        color: #98ABB4;
    }
    input[type=text],
    input[type=password] {
        caret-color: #fff;
        width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            box-sizing: border-box;
            border: 2px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            color: #fff;
            background-color: #212127;
    }

    .conteudo_modal_cadastrar_caregar {
        display: none;
        top: 100%;
        justify-content: center;
        align-items: center;

    }

    .conteudo_modal_cadastrar_caregado {
        display: none;
    }

    .alert_msg {
        margin-left: 192px;
        display: block;
        /* Hidden by default */
        position: fixed;
        top: 0;
        width: 100%;
        /* Full width */
        height: 30px;
        /* Full height */
        overflow: auto;
        /* Enable scroll if needed */
        background-color: rgba(255, 34, 0, 0.88);
        /* Black w/ opacity */
        backdrop-filter: blur(5px);
        /* Add blur effect to items behind the modal_cadastrar */
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

    .first {
        transition: box-shadow 300ms ease-in-out, color 300ms ease-in-out;

        &:hover {
            box-shadow: 0 0 40px 40px #6697FF inset;
        }
    }
</style>

<body>

    <div class="sidebar">
        <ul>
            <li><a href="mensagens.php">Mensagens</a></li>
            <li><span href="#">Gerenciar usuario</span></li>
            <li><a href="configuracao.php">Configuração</a></li>
            <li><a  href="templates.php">Templates</a></li>
            <li><a  href="exemplos.php">Exemplos</a></li>
            <li><a href="../login/logout.php">Sair</a></li>

        </ul>
    </div>



    <?php
    if (isset($_POST['error'])) {
        if ($_POST['error'] == "atualizar_exixte") {
            echo '<div class="alert_msg" id="alert_msg">atualizar exixte</div>';
        }
        if ($_POST['error'] == "criar_exite") {
            echo '<div class="alert_msg" id="alert_msg">criar exite</div>';
        }
    }
    ?>
    <div class="content">

        <div class="lado_lado" id="mensagem">
            <?php include('atualiza_planilha_usuario.php'); ?>
        </div>
        <div class="lado_lado element_top">
            <button class="btn first" onclick="openmodal_cadastrar()">Cadastrar novo usuario</button>

        </div>

        <!-- The modal_cadastrar container -->
        <div class="modal_cadastrar">
            <!-- modal_cadastrar content -->
            <div class="modal_cadastrar-content">

                <div class="conteudo_modal_cadastrar_caregado" id="conteudo_modal_cadastrar_caregado">
                    <span class="close" onclick="closemodal_cadastrar()">&times;</span>
                    <form method="post" action="">
                        <h3><p style="color: #fff">Cadastrar novo usuário</p></h3>
                        <p>Nome</br>
                            <input name="nome_cadastro" type="text">
                        </p>
                        <p>Webhook</br>
                            <input name="webhook_cadastro" type="text">
                        </p>
                        <p>Whatzap</br>
                            <input name="whatzap_cadastro" type="text">
                        </p>
                        <p>Email</br>
                            <input name="email_cadastro" type="text">
                        </p>
                        <p>Senha</br>
                            <input name="senha_cadastro" type="text">
                        </p>
                        <p>
                            <center><input name="botao_cadastro" class="btn first" value="Adicionar"
                                    onclick="cadastrar_fechar()" type="submit"></center>
                        </p>


                    </form>
                </div>
            </div>
        </div>

        <div id='modal_atualizar'></div>

        <?php
        if (isset($_POST['nome_atualizar']) && isset($_POST['webhook_atualizar']) && isset($_POST['whatzap_atualizar']) && isset($_POST['email_atualizar']) && isset($_POST['senha_atualizar'])) {
            if ($_POST['nome_cadastro'] != "" && $_POST['webhook_cadastro'] != "" && $_POST['whatzap_cadastro'] != "" && $_POST['email_cadastro'] != "" && $_POST['senha_cadastro'] != "" ) {
            isThreat($_POST['nome_atualizar']);
            isThreat($_POST['webhook_atualizar']);
            isThreat($_POST['whatzap_atualizar']);
            isThreat($_POST['email_atualizar']);
            isThreat($_POST['senha_atualizar']);


            $id = $_SESSION['id_atualiza'];
            if (isset($_POST['Ativar'])) {
                $ativo = 'Ativo';
            } else {
                $ativo = 'Desativado';
            }
            if (select('users', '*', 'usuario=\'' . $_POST['nome_atualizar'] . '\' and id!=\'' . $id . '\'') == []) {
                update('users', ['usuario_1_camadas', 'usuario', 'senha_1_camadas', 'senha', 'ativo', 'email', 'whatzap', 'webhook'], [hash('md5', strtolower(hash('md5', strtolower(hash('md5', $_POST['nome_atualizar'], false)), false)), false), $_POST['nome_atualizar'], hash('md5', strtolower(hash('md5', strtolower(hash('md5', $_POST['senha_atualizar'], false)), false)), false), $_POST['senha_atualizar'], $ativo, $_POST['email_atualizar'], $_POST['whatzap_atualizar'], $_POST['webhook_atualizar']], "id='" . $id . "'");
            } else {
                $_POST['error'] = 'atualizar_exixte';
            }
        }}
        if (isset($_POST['nome_cadastro']) && isset($_POST['webhook_cadastro']) && isset($_POST['whatzap_cadastro']) && isset($_POST['email_cadastro']) && isset($_POST['senha_cadastro'])) {
            if ($_POST['nome_cadastro'] != "" && $_POST['webhook_cadastro'] != "" && $_POST['whatzap_cadastro'] != "" && $_POST['email_cadastro'] != "" && $_POST['senha_cadastro'] != "" ) {
            isThreat($_POST['nome_cadastro']);
            isThreat($_POST['webhook_cadastro']);
            isThreat($_POST['whatzap_cadastro']);
            isThreat($_POST['email_cadastro']);
            isThreat($_POST['senha_cadastro']);


            if (select('users', '*', 'usuario=\'' . $_POST['nome_cadastro'] . '\'') == []) {
                insert('users', ['usuario_1_camadas', 'usuario', 'senha_1_camadas', 'senha', 'ativo', 'email', 'whatzap', 'webhook'], [hash('md5', strtolower(hash('md5', strtolower(hash('md5', $_POST['nome_cadastro'], false)), false)), false), $_POST['nome_cadastro'], hash('md5', strtolower(hash('md5', strtolower(hash('md5', $_POST['senha_cadastro'], false)), false)), false), $_POST['senha_cadastro'], 'ativo', $_POST['email_cadastro'], $_POST['whatzap_cadastro'], $_POST['webhook_cadastro']]);
            } else {
                $_POST['error'] = 'criar_exite';
            }
        }}
        ?>


</body>

</html>
<script type="modal.js"></script>
<script>
    function mostrarMensagem() {
        fetch('atualiza_planilha_usuario.php')
            .then(response => response.text())
            .then(data => document.getElementById('mensagem').innerHTML = data);


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


    // When the user clicks on the button, open the modal_cadastrar
    const openmodal_cadastrar = () => {
        modal_cadastrar.style.display = 'block';

        setTimeout(function () {


            ConteudoModalcadastrarCaregado.style.display = 'block';
            ModalcadastrarContent.style.color = '#fefefe';

        });

    }

    // When the user clicks on the close button, close the modal_cadastrar
    const closemodal_cadastrar = () => {
        ConteudoModalcadastrarCaregado.style.display = 'none';
        modal_cadastrar.style.display = 'none';
        ModalcadastrarContent.style.color = '#fefefe';
    }
  
    const cadastrar_fechar = () => {
        ConteudoModalcadastrarCaregado.style.display = 'none';
        modal_cadastrar.style.display = 'none';
        ModalcadastrarContent.style.color = '#fefefe';
    }

    const closemodal_atualizar = () => {
        document.getElementById('modal_atualizar').innerHTML = "";
    }


    async function atualizar_user(id) {
        //console.log(id);
        const dados = await fetch('modificar_usuario.php?id=' + id);
        const resposta = await dados.json();

        //console.log(resposta);

        if (!resposta['status']) {
            document.getElementById('msgAlert').innerHTML = resposta['msg'];
        } else {
            var dado = resposta['dados'];
            document.getElementById('modal_atualizar').innerHTML = dado;
        }

    }




</script>