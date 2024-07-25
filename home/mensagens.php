

<?php

include('../login/verifica_login.php');
?>
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

    .element_top {
        position: absolute;

    }
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
    .text_cor{
        color: #98ABB4;
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
        .texto_esquerda{
          text-align: left;
        }
        /* modal_cadastrar content */
        .modal_Atualizar-content {
          background-color: #f8f8f8;
            margin: 2% auto;
            /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: auto;
          
            max-width: 70%;
           
            /* Could be more or less, depending on screen size */
            
            height: auto;
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
                padding-bottom: 10%;
                opacity: 1;
            }
        }

        input[type=text],
        input[type=password] {
       
          caret-color: #fff;
            margin: 8px;
            box-sizing: border-box;
            border: 2px solid #ccc;
            border-radius: 4px;
            color: #fff;
            font-size: 16px;
        }
        .input_text{
            color: #fff;
            padding: 0px 5px;
            width: 20%;
            background-color: #212127;
            
        
        }
        .input_date{
          margin: 8px;
            color: #fff;
            padding: 0px 5px;
            background-color: #212127;
            
        
        }
        .conteudo_modal_cadastrar_caregar{
            display: none;
            top:100%;
  justify-content: center;
  align-items: center;

        }
        .not_efet {

background-color: rgba(0,0,0,0);

border: 0 ;


}
        .template_border{
          border-collapse: collapse; /* CSS2 */
    background: #FFFFF0;
    border: solid green 1px;
        }
        .conteudo_modal_cadastrar_caregado{
            display: none;
        }
        .alert_msg{
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

  align-self: center;
  font-size: 13px;
  font-weight: 400;
  line-height: 1;
  margin: 5px;
  padding: 5px 5px;
  text-decoration: none;
  text-align: center;
  
            
  font-family: 'Montserrat', sans-serif;
  font-weight: 700;

  &:hover,
  &:focus {
    color: #fff;
    outline: 0;
  }
}

.first {
  transition: box-shadow 300ms ease-in-out, color 300ms ease-in-out;
  &:hover {
    box-shadow: 0 0 40px 40px #6697FF inset;
  }
}
.lado_lado{
    display: inline-block;
        margin: 0 10px;
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
    .margin{
      margin: 9px 0;
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
<script>
async function listar_mensagens(id){
    //console.log(id);
     const dados=  await fetch('listar_mensagens.php?id='+id);
     const resposta = await dados.json();
    
    // //console.log(resposta);
    
     if(!resposta['status']){
       document.getElementById('msgAlert').innerHTML = resposta['msg'];
     }else{
       var dado = resposta['dados'];
       document.getElementById('modal_mensagens').innerHTML = dado;
    }
  
  }

  async function pesquisa_mensagens(){
    var Usuario = "";
    var data = "";
    var id_cliente = "";

    Usuario = document. querySelector("#Usuario").value;
    data = document. querySelector("#data").value;
    id_cliente = document. querySelector("#id_cliente").value;
    
    console. log(Usuario);
    console. log(data);
   //console. log('listar_mensagens.php?Usuario='+Usuario+'&data='+data+'&id_cliente='+id_cliente);
   var temp = "";
  
    temp+='Usuario='+Usuario;

 if(data.length){
  
    temp+='&';
  
  temp+='data='+(data.replace("-",",")).replace("-",",");
}
 if(id_cliente.length){

    temp+='&';
  
  temp+='id_cliente='+id_cliente;
}
      console. log('listar_mensagens.php?'+temp.replace(" ", "%20"));
      const dados=  await fetch('listar_mensagens.php?'+temp.replace(" ", "%20"));
      const resposta = await dados.json();
    
     console.log(resposta);
    
      if(!resposta['status']){
        document.getElementById('msgAlert').innerHTML = resposta['msg'];
      }else{
        var dado = resposta['dados'];
        document.getElementById('modal_mensagens').innerHTML = dado;
     }
  
  }

  const closemodal_listar_mensagens = () => {
            document.getElementById('modal_mensagens').innerHTML = "";
        }

</script>
<body>


  <div class="sidebar">
    <ul>
      <li><span>Mensagens</span></li>
      <li><a href="usuarios.php">Gerenciar usuario</a></li>
      <li><a href="configuracao.php">Configuração</a></li>
      <li><a  href="templates.php">Templates</a></li>
      <li><a  href="exemplos.php">Exemplos</a></li>
      <li><a href="../login/logout.php">Sair</a></li>

    </ul>
  </div>

  <div id="modal_mensagens"></div>
  
  <div class="content">
    
    <div>
      <canvas id="myChart" style="height: 400px;"></canvas>
    </div>
    <center>
    <div class="code_escuro margin">
     
     Data:<input type="date" id="data" class="input_date">
     ID Clietne:<input type="text" id="id_cliente" class="input_text">
     Usuario:<input type="text" id="Usuario" class="input_text">
      <button onclick="pesquisa_mensagens()" class="btn first">Pesquisar</button>
      
      </div>
    <div id="mensagem" class="lado_lado">
      <?php
      include_once("atualiza_planilha_mensagens.php");
          ?>
        </tbody>
      </table>

    

    </div>
    
    </center>
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
        .catch(error => console.log(''));
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
