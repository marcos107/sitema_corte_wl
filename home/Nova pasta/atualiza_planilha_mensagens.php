<?php
include('../db/db_comandos.php');
// Executar a consulta para obter os dados a serem exibidos na tabela
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
			<th>Telefone</th>
			<th>Toral de Mensagem</th>
		</tr>
	</thead>
	<tbody>";

foreach ($result as $key => $value) {
	echo "<tr>
			<td>" . $value['usuario'] . "</td>
			<td>" . $value['email'] . "</td>
			<td>" . $value['whatzap'] . "</td>

			
        ";
         $i = 0;
		 $res = select('mensages','*','id_user = \''.$value['id'].'\'');
	
		 foreach ($res as $key => $value) {
			$i ++;
		 }
		echo "<td>" . $i . "</td>";


		echo"
		</tr>";
}
echo "</tbody>
</table>";




?>

