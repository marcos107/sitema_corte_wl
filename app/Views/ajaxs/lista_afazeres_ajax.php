<script>
  lista_temp = "";
  var bellQueue = [];
  var isPlaying = false;
  var playInterval;

  tela_add_desenho = document.querySelector('.card-body').innerHTML;
  document.querySelector('.card-body').innerHTML = '<div id="processos" class="form-group">                  <label>Processos</label>                      <select id="processos_desenho" class="custom-select"><option value="">Processos</option></select>                    </div></br></br><button name="cadastarar" type="submit" onclick="aparecer_lista()" class="btn btn-block btn-outline-primary btn-lg">Proximo</button>';

  var tituloCard = document.querySelector('.card-title');
  // Altera o texto do elemento
  tituloCard.innerHTML = "Escolha a lista que deseja trabalhar hoje.";
  var processo_nome = '';
  processo_lista();

  function inicio_tela() {

    document.querySelector('.card-body').innerHTML = '<div id="processos" class="form-group">                  <label>Processos</label>                      <select id="processos_desenho" class="custom-select"><option value="">Processos</option></select>                    </div></br></br><button name="cadastarar" type="submit" onclick="aparecer_lista()" class="btn btn-block btn-outline-primary btn-lg">Proximo</button>';

    var tituloCard = document.querySelector('.card-title');
    // Altera o texto do elemento
    tituloCard.innerHTML = "Escolha a lista que deseja trabalhar hoje.";
    var processo_nome = '';
    processo_lista();
  }


  function aparecer_lista() {
    processo_nome = document.getElementById("processos_desenho").value;
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

      success: function (response) {

        processos = response.lista;


        if (!document.getElementById('processos_desenho'))
          return;
        // Seleciona o elemento <select> onde as opções serão adicionadas
        var selectElement = document.getElementById('processos_desenho');

        // Limpa as opções existentes no <select>
        selectElement.innerHTML = '';
        // Cria a opção padrão e adiciona ao início do <select>
        var defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'Processos';
        selectElement.appendChild(defaultOption);
        // Itera sobre cada processo na lista
        processos.forEach(function (processo) {
          // Cria um novo elemento <option>
          var optionElement = document.createElement('option');
          optionElement.value = processo.nome; // Define o nome do processo como o valor da opção
          optionElement.textContent = processo.nome; // Define o nome do processo como o texto da opção

          // Adiciona a nova opção ao <select>
          selectElement.appendChild(optionElement);
        });
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
      url: '<?= base_url('public/lista_afazeres') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { processo: processo_nome },
      success: function (response) {
        if (response.lista != lista_temp || ok) {
          if (response.status != "cortando" && lista_temp != "" && response.som == "true") {
            playBellSound();
          }
          $('#example1').DataTable().destroy();

          var div = $('#minhaDiv');
          div.load(location.href + ' #minhaDiv');

          var lista = document.getElementById('lista');
          lista.innerHTML = response.lista;

          $(function () {
            $("#example1").DataTable({
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
  // lista_afazeres(lista, 60000);

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
      data: { id: id },
      success: function (response) {
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
        data: { id: id },
        success: function (response) {


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