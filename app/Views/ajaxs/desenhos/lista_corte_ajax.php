<script>

  lista_temp = "";
  function lista() {
    // Realiza uma requisição AJAX para a URL especificada, que deve retornar dados em formato JSON.
    $.ajax({
      url: '<?= base_url('public/desenhista/lista_corte') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      success: function (response) {
        // A função success() é chamada quando a requisição AJAX é concluída com sucesso.

        // Verifica se a lista atualizada é diferente da lista anterior (lista_temp).

        if (response.lista != lista_temp) {
          // Destroi a tabela DataTable existente para recriá-la com os novos dados.

          $('#example1').DataTable().destroy();





          // Recriar e configurar a tabela DataTable

          // Atualiza o conteúdo da div com o ID "minhaDiv" com o novo HTML retornado na resposta.
          var div = $('#minhaDiv');

          div.load(location.href + ' #minhaDiv');
          // Selecione o elemento <tbody> pelo seu ID
          var lista = document.getElementById('lista');
          // Substitua o conteúdo do elemento <tbody> com o novo HTML
          lista.innerHTML = response.lista;
          $(function () {
            // Recria e configura a tabela DataTable com os novos dados.

            $("#example1").DataTable({

              "responsive": true, "lengthChange": false, "autoWidth": false,
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


          // Atualiza lista_temp com a nova lista.

          lista_temp = response.lista;
        }
      }
    });
  }
  // Executar função ao abrir o site
  document.addEventListener('DOMContentLoaded', lista);

  // Repetir função a cada segundo
  setInterval(lista, 1000);

</script>