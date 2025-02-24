<script>
  lista_temp = "";
  var bellQueue = [];
  var isPlaying = false;
  var playInterval;

  tela_add_desenho = document.querySelector('.card-body').innerHTML;
  document.querySelector('.card-body').innerHTML = '<div id="processos" class="form-group">                  <label>Processos</label>                       <div id="processos_radio"> </div>                   </div></br></br><button name="cadastarar" type="submit" onclick="aparecer_lista()" class="btn btn-block btn-outline-primary btn-lg">Proximo</button>';

  var tituloCard = document.querySelector('.card-title');
  // Altera o texto do elemento
  tituloCard.innerHTML = "Escolha a lista que deseja trabalhar hoje.";
  var processo_nome = '';
  processo_lista();

  function inicio_tela() {

    document.querySelector('.card-body').innerHTML = '<div id="processos" class="form-group">                  <label>Processos</label>                      <div id="processos_radio"> </div>                    </div></br></br><button name="cadastarar" type="submit" onclick="aparecer_lista()" class="btn btn-block btn-outline-primary btn-lg">Proximo</button>';

    var tituloCard = document.querySelector('.card-title');
    // Altera o texto do elemento
    tituloCard.innerHTML = "Escolha a lista que deseja trabalhar hoje.";
    var processo_nome = '';
    processo_lista();
  }

  function get_radio() {
    var radios = document.getElementsByName('processo'); // Seleciona todos os botões de rádio com o nome 'processo'
    var processo_var = '';

    // Itera sobre todos os botões de rádio para encontrar o selecionado
    for (var i = 0; i < radios.length; i++) {
      if (radios[i].checked) {
        return radios[i].value; // Captura o valor do botão de rádio selecionado
        break; // Sai do loop após encontrar o botão selecionado
      }
    }
    return processo_var;
  }

  function aparecer_lista() {
    processo_nome = get_radio();
    tituloCard.innerHTML = "<button type='submit' onclick='inicio_tela()' class='btn btn-outline-primary'>  ⬅ Voltar </button>&nbsp&nbsp&nbsp Lista " + processo_nome;
    document.querySelector('.card-body').innerHTML = tela_add_desenho;
    lista(true);
  }






  processos = "";

  function processo_lista() {





    $.ajax({
      url: '<?= base_url('public/processos_lista') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      async: false, // Define a requisição como síncrona
      success: function(response) {
        processos = response.lista;

        // Verifica se o elemento <div> existe
        var radioContainer = document.getElementById('processos_radio');
        if (!radioContainer)
          return;

        // Limpa os elementos de rádio existentes na <div>
        radioContainer.innerHTML = '';

        // Itera sobre cada processo na lista
        processos.forEach(function(processo, index) {
          // Cria um novo elemento <input> para o botão de rádio
          var radioElement = document.createElement('input');
          radioElement.type = 'radio';
          radioElement.name = 'processo'; // Define o mesmo nome para agrupar os botões de rádio
          radioElement.id = 'processo_' + index; // Define um ID único para cada botão de rádio
          radioElement.value = processo.nome; // Define o nome do processo como o valor do botão

          // Cria um <label> para o botão de rádio
          var labelElement = document.createElement('label');
          labelElement.htmlFor = 'processo_' + index; // Associa o label ao botão de rádio
          labelElement.textContent = processo.nome; // Define o nome do processo como o texto do label
          labelElement.style.fontWeight = 'normal'; // Remove o negrito do texto

          // Cria um <span> para envolver o rádio e o label, mantendo-os juntos horizontalmente
          var spanElement = document.createElement('span');
          spanElement.style.marginRight = '15px'; // Adiciona espaço entre os botões de rádio
          spanElement.appendChild(radioElement);
          spanElement.appendChild(labelElement);

          // Adiciona o <span> à <div>
          radioContainer.appendChild(spanElement);
        });
        if (document.getElementById('processo_0'))
          document.getElementById('processo_0').checked = true;
      }
    });
  }

  function playBellSound() {
    var audio = document.getElementById("bell-sound");

    function playNext() {
      if (bellQueue.length > 0) {
        isPlaying = true;
        audio.play().then(() => {
          bellQueue.shift();
          if (bellQueue.length > 0) {
            playNext();
          } else {
            isPlaying = false;
          }
        }).catch(error => {
          console.error("Erro ao tentar tocar o áudio:", error);
          isPlaying = false;
        });
      } else {
        clearInterval(playInterval);
        isPlaying = false;
      }
    }

    bellQueue.push(true);

    if (!isPlaying) {
      playNext();
    }
  }

  function lista(ok = false) {
    $.ajax({
      url: '<?= base_url('public/lista_tarefas') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: {
        processo: processo_nome
      },
      success: function(response) {
        if (response.lista != lista_temp || ok) {
          if (response.status != "cortando" && lista_temp != "" && response.som == "true") {
            playBellSound();
          }
          $('#example1').DataTable().destroy();

          var div = $('#minhaDiv');
          div.load(location.href + ' #minhaDiv');

          var lista = document.getElementById('lista');
          lista.innerHTML = response.lista;

          $(function() {
            $("#example1").DataTable({
              "order": [
               
                [0, "asc"],
                [1, "asc"]
              ], // Ordena pela coluna 1 e depois pela coluna 0 (ambas em ordem ascendente)
              "responsive": true,
              "lengthChange": false,
              "autoWidth": false,
              "buttons": ["colvis"],
              "language": {
                "decimal": "",
                "emptyTable": "Sem dados disponíveis",
                "infoEmpty": "Mostrando de 0 até 0 de 0 registos",
                "infoFiltered": "(filtrado de MAX registos no total)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": " MENU",
                "loadingRecords": "A carregar dados...",
                "processing": "A processar...",
                "search": "Buscar:",
                "zeroRecords": "Não foram encontrados resultados",
                "paginate": {
                  "first": "Primeiro",
                  "last": "Último",
                  "next": "Seguinte",
                  "previous": "Anterior"
                },
                "aria": {
                  "sortAscending": ": clique para ordenar ascendente (ASC)",
                  "sortDescending": ": clique para ordenar descendente (DESC)"
                }
              }
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
          });


          lista_temp = response.lista;
        }
      }
    });
  }

  // Verifica a fila e tenta tocar o som a cada 1 segundo
  // playInterval = setInterval(() => {
  //   if (bellQueue.length > 0 && !isPlaying) {
  //     playBellSound();
  //   }
  // }, 1000);

  // // Simulação de chamadas periódicas para a função lista
  // setInterval(() => {
  //   lista();
  // }, 5000); // Intervalo de 5 segundos para chamar a função lista // Executar função ao abrir o site
  // document.addEventListener('DOMContentLoaded', lista);

  // // Repetir função a cada segundo
  // lista_tarefas(lista, 60000);

  function cortando(nome) {
    copy(nome);
    alert(nome);

  }

  function copy(nome) {
    var textoCopiado = nome;

    var tempTextarea = document.createElement('textarea');
    tempTextarea.value = textoCopiado;

    document.body.appendChild(tempTextarea);

    tempTextarea.select();

    document.execCommand('copy');

    document.body.removeChild(tempTextarea);
  }


  function cortar(id) {
    $.ajax({
      url: '<?= base_url('public/caminho_desenho') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: {
        id: id
      },
      success: function(response) {
        copy(response.caminho);
        alert(response.caminho);
        lista();

      }
    });
  }

  function confirmar(id, nome) {
    if (mostrarConfirmacao("Confirmar corte do desenho: " + nome)) {

      $.ajax({
        url: '<?= base_url('public/confirmar_corte') ?>',
        type: "POST",
        dataType: "json", // Indicar que o retorno é em formato JSON
        data: {
          id: id
        },
        success: function(response) {


          lista();

        }
      });
    }
  }

  function mostrarConfirmacao(texto = '') {
    // Exibe a caixa de diálogo de confirmação e armazena a resposta em uma variável
    var resposta = window.confirm(texto);
    // Verifica a resposta e faz algo com ela
    return resposta;

  }
</script>