<!DOCTYPE html>
<html>

<head>
  <title>Mensagens</title>
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

  .sidebar a {
    display: block;
    padding: 10px;
    color: #333;
    text-decoration: none;
  }

  .sidebar a:hover {
    background-color: #333;
    color: #fff;
  }

  .sidebar span {
    background-color: #FFFFFF;
    color: #333;
    display: block;
    padding: 10px;
    text-decoration: none;

  }
</style>

<body>
  <?php

  include('../login/verifica_login.php');
  ?>

  <div class="sidebar">
    <ul>
      <li><span>Mensagens</span></li>
      <li><a href="usuarios.php">Gerenciar usuario</a></li>
      <li><a href="configuracao.php">Configuração</a></li>
      <li><a href="../login/logout.php">Sair</a></li>

    </ul>
  </div>
  <div class="content">
    <div>
      <canvas id="myChart" style="height: 400px;"></canvas>
    </div>
    <div id="mensagem">
      <?php

      include('../db/db_comandos.php');
      // Executar a consulta para obter os dados a serem exibidos na tabela
      $result = select('users');

      $page = '';
      // Verificar se a variável de consulta 'page' está definida
      if (isset($_GET['page'])) {
        $page = $_GET['page'];

      }
      ?>


      <table id='tabela'>
        <thead>
          <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Telefone</th>
            <th>Toral de Mensagem</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($result as $key => $value) {
            echo "<tr>
			<td>" . $value['usuario'] . "</td>
			<td>" . $value['email'] . "</td>
			<td>" . $value['whatzap'] . "</td>

			
        ";
            $i = 0;
            $res = select('mensages', '*', 'id_user = \'' . $value['id'] . '\'');

            foreach ($res as $key => $value) {
              $i++;
            }
            echo "<td>" . $i . "</td>";


            echo "
		</tr>";
          }
          ?>
        </tbody>
      </table>



    </div>

  </div>


  <?php
  //include_once('atualiza_dados.php');
  $labels = array('Jan', 'Fev', 'Mar', 'Abr', 'Maio', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez');
  $data = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
  $data1 = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

  $envi = select('mensages', 'date', 'id_user!=\'webhook\' and id_user!=\'menu\'');
  $rece = select('mensages', 'date', 'id_user=\'webhook\' or id_user=\'menu\'');
  $mr1 = 0;
  $mr2 = 0;
  $mr3 = 0;
  $mr4 = 0;
  $mr5 = 0;
  $mr6 = 0;
  $mr7 = 0;
  $mr8 = 0;
  $mr9 = 0;
  $mr10 = 0;
  $mr11 = 0;
  $mr12 = 0;

  $me1 = 0;
  $me2 = 0;
  $me3 = 0;
  $me4 = 0;
  $me5 = 0;
  $me6 = 0;
  $me7 = 0;
  $me8 = 0;
  $me9 = 0;
  $me10 = 0;
  $me11 = 0;
  $me12 = 0;

  foreach ($envi as $key => $value) {
    switch (substr($value['date'], 5, 2)) {

      case '01':
        $me1++;
        break;
      case '02':
        $me2++;
        break;
      case '03':
        $me3++;
        break;
      case '04':
        $me4++;
        break;
      case '05':
        $me5++;
        break;
      case '06':
        $me6++;
        break;
      case '07':
        $me7++;
        break;
      case '08':
        $me8++;
        break;
      case '10':
        $me10++;
        break;
      case '11':
        $me11++;
        break;
      case '12':
        $me12++;
        break;
    }

  }
  foreach ($rece as $key => $value) {
    switch (substr($value['date'], 5, 2)) {

      case '01':
        $mr1++;
        break;
      case '02':
        $mr2++;
        break;
      case '03':
        $mr3++;
        break;
      case '04':
        $mr4++;
        break;
      case '05':
        $mr5++;
        break;
      case '06':
        $mr6++;
        break;
      case '07':
        $mr7++;
        break;
      case '08':
        $mr8++;
        break;
      case '10':
        $mr10++;
        break;
      case '11':
        $mr11++;
        break;
      case '12':
        $mr12++;
        break;
    }

  }
  $recebidas = array($me1, $me2, $me3, $me4, $me5, $me6, $me7, $me8, $me9, $me10, $me11, $me12);

  $enviadas = array($mr1, $mr2, $mr3, $mr4, $mr5, $mr6, $mr7, $mr8, $mr9, $mr10, $mr11, $mr12);

  // Simula a busca de novos dados aleatórios
  
  $data = $enviadas;
  $data1 = $recebidas;


  ?>

  <script>
    // Obtenha o contexto do canvas
    var ctx = document.getElementById('myChart').getContext('2d');

    // Crie um novo gráfico de linha com opções personalizadas
    var myChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
          label: 'Mensagens recebidas',
          data: <?php echo json_encode($data); ?>,
          backgroundColor: function (context) {
            var chart = context.chart;
            var canvas = chart.canvas;
            var gradient = canvas.getContext('2d').createLinearGradient(0, 0, 0, canvas.height);
            gradient.addColorStop(0, 'rgba(27, 179, 176, 1)');
            gradient.addColorStop(1, 'rgba(0,0,0,0.1)');

            return gradient;
          },
          borderColor: 'rgba(0, 0, 0, 0.8)',
          borderWidth: 1,
          pointRadius: 2,
          lineTension: 0.2,
          fill: true
        },
        {
          label: 'Mensagens enviadas',
          data: <?php echo json_encode($data1); ?>,
          backgroundColor: function (context) {
            var chart = context.chart;
            var canvas = chart.canvas;
            var gradient = canvas.getContext('2d').createLinearGradient(0, 0, 0, canvas.height);
            gradient.addColorStop(0, 'rgba(62, 18, 138, 1)');
            gradient.addColorStop(1, 'rgba(0,0,0,0.1)');

            return gradient;
          },
          borderColor: 'rgba(0, 0, 0, 0.8)',
          borderWidth: 1,
          pointRadius: 2,
          lineTension: 0.2,
          fill: true
        }]

      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          title: {
            display: true,
            text: 'Mensagens'
          },
          legend: {
            position: 'bottom',
            labels: {
              font: {
                size: 14,
                family: 'Arial'
              }
            }
          }
        },
        scales: {
          x: {
            title: {
              display: true,
              text: 'Meses'
            },
            ticks: {
              font: {
                size: 14,
                family: 'Arial'
              }
            }
          },
          y: {
            title: {
              display: true,
              text: 'Mensagens'
            },

            ticks: {
              font: {
                size: 14,
                family: 'Arial'
              },
              stepSize: 10, // intervalo entre cada tick
              max: 50, // valor máximo da escala
              min: 0 // valor mínimo da escala
            }
          },
          yAxes: [{
            ticks: {
              beginAtZero: true
            }
          }]
        }
      }
    });

    // Crie uma função que atualiza os dados do gráfico
    function updateData() {
      // Chame um arquivo PHP que retorna os novos dados do gráfico em JSON
      fetch('atualiza_dados.php')
        .then(response => response.json())
        .then(data => {
          // Atualize os dados do gráfico
          myChart.data.datasets[0].data = data.data;
          myChart.data.datasets[1].data = data.data1;
          myChart.update();
        })
        .catch(error => console.log(error));
    }

    function mostrarMensagem() {
      fetch('atualiza_planilha_mensagens.php?page=12')
        .then(response => response.text())
        .then(data => document.getElementById('mensagem').innerHTML = data);


    }

    // Atualize os dados do gráfico a cada segundo
    setInterval(updateData, 2500);
    setInterval(mostrarMensagem, 2500);
  </script>






</body>

</html>