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
        overflow-x: hidden;
        /* Desabilita a barra de rolagem horizontal */
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
        font-family: Merriweather, Book Antiqua, Georgia, Century Schoolbook, serif;
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

    .titulo {
        color: rgba(255, 255, 255, 0.8);
    }

    .titulo p {
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
        background-color: #212127;
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

    .titulo {
        color: rgba(255, 255, 255, 0.8);
    }

    .borde {
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

    .but_escuro {
        border-style: solid;
        border-style: inset;
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

    .text_cor {
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
    .cadastrar{
        display: none;
    }
</style>

<body>

    <div class="sidebar">
        <ul>
            <li><a href="mensagens.php">Mensagens</a></li>
            <li><a href="usuarios.php">Gerenciar usuario</a></li>
            <li><a href="configuracao.php">Configuração</a></li>
            <li><span>Templates</span></li>
            <li><a href="exemplos.php">Exemplos</a></li>
            <li><a href="../login/logout.php">Sair</a></li>

        </ul>
    </div>




    <div id='modal_atualizar'></div>


    <div class="content">
        <div class="lado_lado" id="mensagem">
            <?php include('atualiza_planilha_template.php'); ?>
        </div>
        <div class="lado_lado element_top">
            <button class="btn first" onclick="openmodal_cadastrar()">Cadastrar novo template</button>

        </div>



    </div>

    <div class="oi cadastrar" id="id_cadastrar">

        <!-- modal_Atualizar content -->
        <div class="modal_Atualizar-content">
            <span class="close" onclick="closemodal_cadastrar()">×</span>


            <form method="post" action="">
                <p>Cadastrar</p>

                <p>Nome<br>
                    <input name="nome_cadastrar" type="text">
                </p>
                <p>Texto<br>

                    <textarea name="text_cadastrar" cols="100%" rows="5"
                        style="width: 453px; height: 176px;">  </textarea>
                </p>
                <p></p>
                <center><input name="botao_cadastar" class="btn first" value="Cadastrar template"
                        onclick="closemodal_cadastrar()" type="submit"></center>
                <p></p>


            </form>

        </div>
    </div>
<?php 

if(isset($_POST['botao_cadastar'])){
if($_POST['text_cadastrar'] != "" && $_POST['nome_cadastrar'] != ""){
    
    insert("templates",["text","nome","status"],[$_POST['text_cadastrar'],$_POST['nome_cadastrar'],"Ativo"]);



}
}


?>

</body>

</html>

<script>

let cadastrar = document.getElementById('id_cadastrar');
function openmodal_cadastrar() {
      
        cadastrar.style.display = "block";
    }
    function closemodal_cadastrar(){
        cadastrar.style.display = "none";
    }


    const closemodal_atualizar = () => {
        document.getElementById('modal_atualizar').innerHTML = "";
    }


    async function atualizar_template(id) {
        console.log(id);
        const dados = await fetch('modificar_template.php?id=' + id);
        const resposta = await dados.json();

        console.log(resposta);

        if (!resposta['status']) {
            document.getElementById('msgAlert').innerHTML = resposta['msg'];
        } else {
            var dado = resposta['dados'];
            document.getElementById('modal_atualizar').innerHTML = dado;
        }

    }


    function mostrarMensagem() {
        fetch('atualiza_planilha_template.php')
            .then(response => response.text())
            .then(data => document.getElementById('mensagem').innerHTML = data);


    }
    setInterval(mostrarMensagem, 1000);

</script>