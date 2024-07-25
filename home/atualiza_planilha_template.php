<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
	<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
	
<?php
// Definir a conexão com o banco de dados
include('../db/db_comandos.php');
// Executar a consulta para obter os dados a serem exibidos na tabela


$result = select('templates');

$page = '';
// Verificar se a variável de consulta 'page' está definida
if (isset($_GET['page'])) {
	$page = $_GET['page'];
   
}




// Imprimir a tabela com os dados recuperados da consulta, limitados pelo índice de início e fim
echo "<table id='tabela'>
	<thead>
		<tr class='borde'>
			<th class='not_efet titulo'>Nome</th>
			<th class='not_efet titulo'>Texto template</th>
            <th class='not_efet titulo'>Status</th>
		</tr>
	</thead>
	<tbody>";
$php = '';

foreach ($result as $key =>$row ) {
	echo "<tr class='borde'>
	                              
			<td><input type='submit' onclick=\"atualizar_template('".$row['id']."')\" class='not_efet text_cor but_escuro' value=\"" . $row['nome'] . "\"></td>
            <td><input type='submit'  onclick=\"atualizar_template('".$row['id']."')\" class='not_efet text_cor but_escuro' value=\"" . $row['text'] . "\"></td>
			<td><input type='submit' onclick=\"atualizar_template('".$row['id']."')\" class='not_efet text_cor but_escuro' value=\"" . $row['status'] . "\"></td>
            
		</tr>";
	
}
echo "</tbody>
</table>


";




?>

