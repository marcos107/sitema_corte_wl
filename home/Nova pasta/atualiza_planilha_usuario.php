<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
	<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
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

        /* Close input */
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
        .conteudo_modal_Atualizar_caregar{
            display: none;
            top:100%;
  justify-content: center;
  align-items: center;

        }
        .conteudo_modal_Atualizar_caregado{
            display: none;
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
		<tr>
			<th>Nome</th>
			<th>Email</th>
            <th>whatzap</th>
			<th>Webhook</th>
            <th>Status</th>
		</tr>
	</thead>
	<tbody>";
$php = '';

foreach ($result as $key =>$row ) {
	echo "<tr>
	                              
			<td><input type='submit' onclick=\"atualizar_user('".$row['id']."')\" class='not_efet' value=\"" . $row['usuario'] . "\"></td>
            <td><input type='submit' onclick=\"atualizar_user('".$row['id']."')\" class='not_efet' value=\"" . $row['email'] . "\"></td>
			<td><input type='submit' onclick=\"atualizar_user('".$row['id']."')\" class='not_efet' value=\"" . $row['whatzap'] . "\"></td>
            <td><input type='submit' onclick=\"atualizar_user('".$row['id']."')\" class='not_efet' value=\"" . $row['webhook'] . "\"></td>
            <td><input type='submit' onclick=\"atualizar_user('".$row['id']."')\" class='not_efet' value=\"" . $row['ativo'] . "\"></td>
		</tr>";
		$php.= 'if(isset($_POST["botao_'.$row["usuario"].'"])){
			echo
		}';
}
echo "</tbody>
</table>

<style>
  .not_efet {

  background-color: rgba(0,0,0,0);

  border: 0 ;
 

}
</style>

";




?>

<script src="modal.js"></script>
