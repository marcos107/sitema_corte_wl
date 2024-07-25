<script type="modal.js"></script>
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
			<th class='not_efet titulo'>Usuario</th>
			<th class='not_efet titulo'>Email</th>
			<th class='not_efet titulo'>Telefone</th>
			<th class='not_efet titulo'>Total de Mensagem</th>
		</tr>
	</thead>
	<tbody>";

foreach ($result as $key => $value) {
	$id=$value['id'];
	echo "<tr>
	
			<td><input  type='submit' onclick=\"listar_mensagens('".$id."')\" class='not_efet text_cor but_escuro' value=\"" . $value['usuario'] . "\"></td>
			<td><input  type='submit' onclick=\"listar_mensagens('".$id."')\" class='not_efet text_cor but_escuro' value=\"" . $value['email'] . "\"></td>
			<td><input  type='submit' onclick=\"listar_mensagens('".$id."')\" class='not_efet text_cor but_escuro' value=\"" . $value['whatzap'] . "\"></td>

			
        ";
         $i = 0;
		 $res = select('mensages','*','id_user = \''.$id.'\'');
	
		 foreach ($res as $key => $value) {
			$i ++;
		 }
		echo "<td><input type='submit' onclick=\"listar_mensagens('".$id."')\" class='not_efet text_cor but_escuro' value=\"" . $i . "\"></td>";


		echo"
		</tr>";
}
echo "</tbody>
</table>";




?>


