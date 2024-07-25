<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
	<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
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
            background-color: #212127;
            margin: 2% auto;
            /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 30%;
           
            /* Could be more or less, depending on screen size */
            
            height: 80%;
        }



        /*     */
    /* The modal_Atualizar container */
    .modal_Atualizar {
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
            /* Add blur effect to items behind the modal_Atualizar */
        }

        /* modal_Atualizar content */
        .modal_Atualizar-content {
            background-color: #212127;
            margin: 2% auto;
            /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 30%;
           
            /* Could be more or less, depending on screen size */
            
            height: 80%;
        }

        /* Close input */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
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

        input[type=text],
        input[type=password] {
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
        .conteudo_modal_cadastrar_caregar{
            display: none;
            top:100%;
  justify-content: center;
  align-items: center;

        }
        .conteudo_modal_cadastrar_caregado{
            display: none;
        }
        .conteudo_modal_Atualizar_caregar{
            display: none;
            top:100%;
  justify-content: center;
  align-items: center;

        }
        .conteudo_modal_Atualizar_caregado{
            display: none;
        }
        .borde{
        border: 5px solid #888;
        margin: 5px;
    }

</style>

<?php
// Definir a conexão com o banco de dados
include('../db/db_comandos.php');
// Executar a consulta para obter os dados a serem exibidos na tabela
$sql = "SELECT * FROM users";

$result = select('users');

$page = '';
// Verificar se a variável de consulta 'page' está definida
if (isset($_GET['page'])) {
	$page = $_GET['page'];
   
}




// Imprimir a tabela com os dados recuperados da consulta, limitados pelo índice de início e fim
echo "<table id='tabela'>
	<thead>
		<tr class='borde'>
			<th class='not_efet titulo'>Usuario</th>
			<th class='not_efet titulo'>Email</th>
            <th class='not_efet titulo'>whatzap</th>
			<th class='not_efet titulo'>Webhook</th>
            <th class='not_efet titulo'>Status</th>
		</tr>
	</thead>
	<tbody>";
$php = '';

foreach ($result as $key =>$row ) {
	echo "<tr class='borde'>
	                              
			<td><input type='submit' onclick=\"atualizar_user('".$row['id']."')\" class='not_efet text_cor but_escuro' value=\"" . $row['usuario'] . "\"></td>
            <td><input type='submit' onclick=\"atualizar_user('".$row['id']."')\" class='not_efet text_cor but_escuro' value=\"" . $row['email'] . "\"></td>
			<td><input type='submit' onclick=\"atualizar_user('".$row['id']."')\" class='not_efet text_cor but_escuro' value=\"" . $row['whatzap'] . "\"></td>
            <td><input type='submit' onclick=\"atualizar_user('".$row['id']."')\" class='not_efet text_cor but_escuro' value=\"" . $row['webhook'] . "\"></td>
            <td><input type='submit' onclick=\"atualizar_user('".$row['id']."')\" class='not_efet text_cor but_escuro'  value=\"" . $row['ativo'] . "\"></td>
		</tr>";
		$php.= 'if(isset($_POST["botao_'.$row["usuario"].'"])){
			echo
		}';
}
echo "</tbody>
</table>


";




?>

