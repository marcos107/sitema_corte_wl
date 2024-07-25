<script>
  lista_temp = "";
  var bellQueue = [];
  var isPlaying = false;
  var playInterval;

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

  function lista() {
    $.ajax({
      url: '<?= base_url('public/corte/lista_corte') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      success: function (response) {
        if (response.lista != lista_temp) {
          if (response.status != "cortando" && lista_temp != "" && response.som == "true")  {
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
  playInterval = setInterval(() => {
    if (bellQueue.length > 0 && !isPlaying) {
      playBellSound();
    }
  }, 1000);

  // Simulação de chamadas periódicas para a função lista
  setInterval(() => {
    lista();
  }, 5000); // Intervalo de 5 segundos para chamar a função lista // Executar função ao abrir o site
  document.addEventListener('DOMContentLoaded', lista);

  // Repetir função a cada segundo
  setInterval(lista, 1000);

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
      url: '<?= base_url('public/corte/caminho_desenho') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { id: id },
      success: function (response) {
        copy(response.caminho);
        alert(response.caminho);

      }
    });
  }
  function confirmar(id, nome) {
    if (mostrarConfirmacao("Confirmar corte do desenho: " + nome)) {
      console.log('1');
      $.ajax({
        url: '<?= base_url('public/corte/confirmar_corte') ?>',
        type: "POST",
        dataType: "json", // Indicar que o retorno é em formato JSON
        data: { id: id },
        success: function (response) {

          console.log(response);

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