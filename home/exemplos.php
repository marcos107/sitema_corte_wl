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
    .escuro{
        border-style:solid;
        border-style: solid ;
border-width:3px;
border-color: #44444c;
        background-color: #2c2c31;
    color: #A6B4B4;
  padding: 2px;
        display: inline-block;
        width: auto;
        

    }
    .code_escuro{
        border-style:solid;
        border-style: inset ;
border-width: 1px;
border-color: #A6B4B4;
        background-color: #2c2c31;
       
  padding: 3px;

        width: auto;
        
    }
</style>

<body>
    <?php

    include('../login/verifica_login.php');
    ?>
    <div class="sidebar">
        <ul>
            <li><a href="mensagens.php">Mensagens</a></li>
            <li><a href="usuarios.php">Gerenciar usuario</a></li>
            <li><a href="configuracao.php">Configuração</a></li>
            <li><a href="templates.php">Templates</a></li>
            <li><span>Exemplos</span></li>
            <li><a href="../login/logout.php">Sair</a></li>

        </ul>
    </div>

    <div class="content">
        <div clase="texto">
        

        <?php
        include('../db/db_comandos.php');
//         $dados = select('users', '*', "ativo='Ativo'");
//         $users = "";
//         foreach ($dados as $key => $value) {
//             $users .= "<option>" . $value["usuario"] . "</option>";
//         }
//         echo '
//     <select id="usuarios">
//     <option>Escolha aqui</option>
//         ' . $users . '
// </select>
    
//     ';
// Escolha um usuario para teste:
//         <br>
//         <button onclick="Demostrar_exemplo()">Demostrar</button>
        $nome_aleatorios = array(
            "Luna Figueiredo",
            "Davi Alves",
            "Luiza Rodrigues",
            "André Fernandes",
            "Alice Santana",
            "Marcos Pereira",
            "Sofia Carvalho",
            "Gabriel Ferreira",
            "Clara Ribeiro",
            "Lucas Castro",
            "Isadora Gonçalves",
            "Pedro da Costa",
            "Bianca Oliveira",
            "Gustavo Rodrigues",
            "Julia Santos",
            "Enzo Ferreira",
            "Mariana Ribeiro",
            "Rafaela Vieira",
            "Thiago Almeida",
            "Camila Costa",
            "Leonardo Fernandes",
            "Manuela Lima",
            "João Vitor",
            "Beatriz Oliveira",
            "Felipe Ribeiro",
            "Natália Souza",
            "Bruno Gonçalves",
            "Ana Luiza Ferreira",
            "Arthur Silva",
            "Gabriela Costa",
            "Loreta Raelene",
            "Davy Bascomb",
            "Malvina Reatha",
            "Eben Johnie",
            "Lissi Reynaldo",
            "Calista Terrell",
            "Salvador Harman",
            "Isla Dominga",
            "Prisca Garey",
            "Briar Rylee",
            "Salem Lazarus",
            "Gulliver Tisha",
            "Finella Marquis",
            "Egbert Damion",
            "Cecil Terrie",
            "Abegail Braden",
            "Rosalind Anselm",
            "Domenic Frieda",
            "Jolene Terence",
            "Latham Guillermina",
            "Herschel Teodora",
            "Gigi Nathalie",
            "Isabelline Gustavo",
            "Clyve Marjory",
            "Evonne Creighton",
            "Myranda Iain",
            "Bellamy Veronique",
            "Anitra Tammy",
            "Viridiana Ulysses",
            "Mireille Kirk",
            "Raydon Robbyn"
        );
        $r_senha_p1 = str_replace(' ', '%20', $nome_aleatorios[random_int(0, 59)]);
        $r_senha_p2 = random_int(1111, 9999);

        $r_senha_p11 = str_replace(' ', '%20', $nome_aleatorios[random_int(0, 59)]);
        $r_senha_p21 = random_int(1111, 9999);

        $r_senha_p12 = str_replace(' ', '%20', $nome_aleatorios[random_int(0, 59)]);
        $r_senha_p22 = random_int(1111, 9999);

        $r_senha_idc = random_int(100000000, 999999999);

        ?>
        

        <div id="exemplos">
        <h1 clase="titulo">Solicitação do token</h1>
            <h3 style="color: #fff">Exemplo de solicitação do token:</h3>
            <ul>
            <li><div class="code_escuro"><p>https://ikonis.com.br/token?nome=ian,senha=123</p></li>
            </div></ul>
            <br />
            <h3>Recebe como resposta:</h3>
            <ul>
            <li><p><div class="escuro">f85b7fa31c9658b41a618eb7077ab93a</div> Quando a solicitação for bem sucedida  irá enviar o token como resposta.</p></li>
            <li><p><div class="escuro">usuario ou senha incoretos</div> Quando o usuario ou a sera estão errados.</p></li>
            </ul>
            <br />
            <h3><p>Desmembramento da URL:</p></h3>
            <ul>
            <li><p><div class="escuro">https://ikonis.com.br/token?</div> Sintaxe padrão para fazer solicitações de do token.</p></li>
            <li><p><div class="escuro">nome=ian</div> Nome do usuario a qual o token irá ficar vinculado.</p></li>
            <li><p><div class="escuro">senha=123</div> Senha do usuario para fazer a confirmação. </p></li>
            </ul>
    

            <p></p>
            <br />
            <br />
            <br />
            <h1 clase="titulo">Solicitação de envio de mensagem</h1>
            <h3 ><p style="color: #fff">Exemplo de solicitação de envio de uma mensagem:</p></h3>
            <lu>
            <p><div class="code_escuro"><li>https://ikonis.com.br/messagem?type=template,name=trocar_senha,idioma=pt_BR,token=f85b7fa31c9658b41a618eb7077ab93a,numero=554700000000,parametros1=<?php echo $r_senha_p1 ?>,parametros2=<?php echo $r_senha_p2 ?>,id_cliente=<?php echo $r_senha_idc ?></li></p>
            </div></lu>
            <br />
            <h3>Recebe como resposta:</h3>
            <lu>
            <p><li><div class="escuro">solicitação INVALIDA</div> Quando a sintaxe da url está errada ou quando o token ja foi substituído.</li></p>
            <p><li><div class="escuro">token expirou</div> Quando o token passou do tempo.</li></p>
            <p><li><div class="escuro">Enviado</div> Quando o envio da mensagem é bem sucedido. </li></p>
            <p><li><div class="escuro">token INVALIDO</div> Quando o token não existe. </li></p>
            </lu>
            <br />
            <h3><p>Desmembramento da URL:</p></h3>
            <lu>
            <p><li><div class="escuro">https://ikonis.com.br/messagem?</div> Sintaxe padrão para fazer solicitações de envio de mensagens.</li></p>
            <p><li><div class="escuro">type=template</div> Tipo de mensagem.</li></p>
            <p><li><div class="escuro">name=trocar_senha</div> Quando o tipo exigir um nome como o "template" que exige o nome do template.</li></p>
            <p><li><div class="escuro">idioma=pt_BR</div> Quando o tipo exigir a linguagem que está usando.</li></p>
            <p><li><div class="escuro">token=f85b7fa31c9658b41a618eb7077ab93a</div> Token para permitir o envio da mensagem.</li></p>
            <p><li><div class="escuro">numero=554700000000</div> Numero em que a mensagem irá enviar.</li></p>
            <p><li><div class="escuro">parametros1=
                <?php echo $r_senha_p1 ?></div> Parametro 1 que o template exigi.</li>
            </p>
            <p><li><div class="escuro">parametros2=
                <?php echo $r_senha_p2 ?></div> Parametro 2 que o template exigi.</li>
            </p>
            <p><li><div class="escuro">id_cliente=
                <?php echo $r_senha_idc ?></div> Id do cliente que está fazendo a solicitação.</li>
            </p>
            </lu>
            <br />
            <h3><p>Exemplo de como o cliente receberá a mensagem no numero(554700000000):</p></h3>
            <div class="code_escuro"><p>Troca de senha do usuário<b>
                <?php echo str_replace('%20', ' ', $r_senha_p1) ?></b>.
            <p></p>Código:<b>
            <?php echo $r_senha_p2 ?></b>.<p></div>


            <br />
            <br />
            <br />
            <h3 ><p style="color: #fff">Exemplo de solicitação de envio de N mensagens:</p></h3>
            <lu>
            <p><div class="code_escuro"><li>https://ikonis.com.br/messagem?type=template,name=trocar_senha,idioma=pt_BR,token=f85b7fa31c9658b41a618eb7077ab93a,numero=554700000000;554711111111;554722222222,parametros1=<?php echo $r_senha_p1 ?>;<?php echo $r_senha_p11 ?>;<?php echo $r_senha_p12 ?>,parametros2=<?php echo $r_senha_p2 ?>;<?php echo $r_senha_p21 ?>;<?php echo $r_senha_p22 ?>,id_cliente=<?php echo $r_senha_idc ?></li></p>
            </div></lu>    
            
            <br />
            <h3><p>Recebe como resposta:</p></h3>
            <lu>  
            <p><li><div class="escuro">solicitação INVALIDA</div> Quando a sintaxe da url está errada ou quando o token ja foi substituído.</li></p>
            <p><li><div class="escuro">token expirou</div> Quando o token passou do tempo.</li></p>
            <p><li><div class="escuro">Enviado</div> Quando o envio da mensagem é bem sucedido.</li></p>
            <p><li><div class="escuro">token INVALIDO</div> Quando o token não existe.</li></p>
            </lu>  
            <br />
            <h3><p>Desmembramento da URL:</p></h3>
            <lu>
            <p><li><div class="escuro">https://ikonis.com.br/messagem?</div> Sintaxe padrão para fazer solicitações de envio de mensagens.</li></p>
            <p><li><div class="escuro">type=template</div> Tipo de mensagem </p>
            <p><li><div class="escuro">name=trocar_senha</div> Quando o tipo exigir um nome como o "template" que exige o nome do template.</li></p>
            <p><li><div class="escuro">idioma=pt_BR</div> Quando o tipo exigir a linguagem que está usando.</li></p>
            <p><li><div class="escuro">token=f85b7fa31c9658b41a618eb7077ab93a</div> Token para permitir o envio da mensagem.</li></p>
            <p><li><div class="escuro">numero=554700000000;554711111111;554722222222</div> Números em que a mensagem irá enviar usando ";" para separar.</li></p>
            <p><li><div class="escuro">parametros1=
                <?php echo $r_senha_p1 ?>;<?php echo $r_senha_p11 ?>;<?php echo $r_senha_p12 ?></div> Parametro 1 Usando ";" para separar.
                </li></p>
            <p><li><div class="escuro">parametros2=
                <?php echo $r_senha_p2 ?>;<?php echo $r_senha_p21 ?>;<?php echo $r_senha_p22 ?></div> Parametro 2 Usando ";" para separar.
                </li></p>
            <p><li><div class="escuro">id_cliente=
                <?php echo $r_senha_idc ?></div> Id do cliente que está fazendo a solicitação.
                </li></p>
            </lu>
         
            <br />
            <h3><p>Exemplo de como o cliente receberá a mensagem no numero(554700000000):</p></h3>
            <div class="code_escuro"><p>Troca de senha do usuário<b>
                <?php echo str_replace('%20', ' ', $r_senha_p1) ?></b>.
            <p></p>Código:<b>
            <?php echo $r_senha_p2 ?>.</b><p></div>
            <br />
            <h3><p>Exemplo de como o cliente receberá a mensagem no numero(554711111111):</p></h3>
            <div class="code_escuro"><p>Troca de senha do usuário<b>
                <?php echo str_replace('%20', ' ', $r_senha_p11) ?></b>.
            <p></p>Código:<b>
            <?php echo $r_senha_p21 ?></b>.<p></div>
            <br />
            <h3><p>Exemplo de como o cliente receberá a mensagem no numero(554722222222):</p></h3>
            <div class="code_escuro"> <p>Troca de senha do usuário<b>
                <?php echo str_replace('%20', ' ', $r_senha_p12) ?></b>.
            <p></p>Código:<b>
            <?php echo $r_senha_p22 ?></b>.<p></div>
        </div>
    </div>
            </div>



</body>

</html>
<script>
    async function Demostrar_exemplo() {


        var Usuario = document.querySelector("#usuarios").value;



        if (Usuario != "Escolha aqui") {
            // console.log(Usuario);
            const dados = await fetch('listar_exemplos.php?nome=' + Usuario);
            const resposta = await dados.json();

            console.log(resposta);

            if (!resposta['status']) {
                document.getElementById('msgAlert').innerHTML = resposta['msg'];
            } else {
                var dado = resposta['dados'];
                document.getElementById('exemplos').innerHTML = dado;
            }
        }
    }

</script>