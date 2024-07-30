<script>

  lista_temp = "";


  <?php $processo = "finalidade"; ?>
  function desativar(id) {
    $.ajax({
      url: '<?= base_url('public/troca_status/' . $processo . '/desativado') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { id: id },
      success: function (response) {

        lista();
      }
    });
  }

  function ativar(id) {

    $.ajax({
      url: '<?= base_url('public/troca_status/' . $processo . '/ativo') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { id: id },
      success: function (response) {

        lista();
      }
    });
  }





  function lista() {
    var ativos = document.getElementById('checkbox_ativos').checked;
    var desativados = document.getElementById('checkbox_desativado').checked;
    $.ajax({
      url: '<?= base_url('public/finalidade') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { ativos: ativos, desativados: desativados },
      success: function (response) {
        if (response.lista != lista_temp) {
          $('#example1').DataTable().destroy();





          // Recriar e configurar a tabela DataTable


          var div = $('#minhaDiv');

          div.load(location.href + ' #minhaDiv');
          // Selecione o elemento <tbody> pelo seu ID
          var lista = document.getElementById('lista');
          // Substitua o conteúdo do elemento <tbody> com o novo HTML
          lista.innerHTML = response.lista;
          $(function () {
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



          lista_temp = response.lista;
        }
      }
    });
  }
  // Executar função ao abrir o site
  document.addEventListener('DOMContentLoaded', lista);



  function cadastrar() {

    var finalidade = document.getElementById("nome_Finalidade_novo").value;

    $.ajax({
      url: '<?= base_url('public/finalidade_cadastrar') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { finalidade: finalidade },
      success: function (response) {

        if (!response.ok) {
          //response.msg

          for (const chave in response.msg) {
            const valor = response.msg[chave];
            alert_personalizado(chave, valor);
          }
        } else {
          lista();
          alert_certo('Cadastrado', 'Finalidade cadastrado com sucesso.');
          document.getElementById("nome_Finalidade_novo").value = '';
        }

      }
    });
  }
  function alert_certo(titulo, bory) {
    $(document).Toasts('create', {
      class: 'bg-success',
      title: titulo,
      subtitle: 'Subtitle',
      autohide: true,
      delay: 5000,
      body: bory
    });
  }
  function alert_personalizado(titulo, bory) {
    $(document).Toasts('create', {
      class: 'bg-danger',
      title: titulo,
      subtitle: 'Subtitle',
      autohide: true,
      delay: 13000,
      body: bory
    });
  }

  document.getElementById("nome_Finalidade_novo").addEventListener("input", function () {
    var input = this;
    var maxLength = 17;
    var valor = input.value;
    input.value = valor.slice(0, maxLength); // Trunca o valor para o tamanho máximo 

  });

  id_g = '';
  function modal_modificar(id) {
    id = id.replace('modal_', '');
    id_g = id;
    $.ajax({
      url: '<?= base_url('public/finalidade_modal') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { id: id },
      success: function (response) {

        if (!response.desenho) {
          var botao_confirmar_modal = document.getElementById('botao_confirmar_modal');
          botao_confirmar_modal.innerHTML = "Confirmar";
          var modal_titulo = document.getElementById('modal_titulo');
          var modal_bory = document.getElementById('modal_bory');
          modal_titulo.textContent = "Modificar a finalidade: " + response.nome;

          var inputElement = document.createElement("input");

          var divElemnt = document.createElement("div");
          divElemnt.classList.add("form-group");

          modal_bory.innerHTML = '';





          divElemnt = document.createElement("div");
          divElemnt.classList.add("form-group");
          inputElement = document.createElement("input");
          inputElement.type = 'text';
          inputElement.id = 'nome_Finalidade_novo';
          inputElement.classList.add("form-control");
          inputElement.value = response.nome;
          // Adiciona o evento de input para truncar o valor
          inputElement.addEventListener("input", function () {
            var input = this;
            var maxLength = 17;
            var valor = input.value;
            input.value = valor.slice(0, maxLength); // Trunca o valor para o tamanho máximo 
          });
          labelElement = document.createElement("label");
          labelElement.textContent = "Finalidade";
          divElemnt.innerHTML = '';
          divElemnt.appendChild(labelElement);
          divElemnt.appendChild(inputElement);
          modal_bory.appendChild(divElemnt);//coloca o input name no modal


          mostrarModal();
        } else {
          alert_personalizado('Modificar', 'Finalidade já está em uso.');
        }
      }
    });


  }
  function confirmarModal() {

    var finalidade = document.getElementById("nome_Finalidade_novo").value;

    $.ajax({
      url: '<?= base_url('public/finalidade_update') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { id: id_g, finalidade: finalidade },
      success: function (response) {
        if (!response.ok) {
          //response.msg
          for (const chave in response.msg) {
            const valor = response.msg[chave];
            alert_personalizado(chave, valor);
          }
        } else {
          alert_certo('Alteração', 'Finalidade Modificado com sucesso.');
          lista();
        }

      }
    });

    fecharModal();
  }

  // // Repetir função a cada segundo
  // setInterval(lista, 1000);
</script>