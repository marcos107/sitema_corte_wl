<script>


  lista_temp = "";
  function lista() {
    data = document.getElementById('dataInicial').value;
    data1 = document.getElementById('dataFinal').value;
    $.ajax({
      url: '<?= base_url('public/adm/desenhos_cortados') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { data: data, data1: data1 },
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

  // Repetir função a cada segundo
  setInterval(lista, 5000);

  const dataInicialInput = document.getElementById('dataInicial');
  const dataFinalInput = document.getElementById('dataFinal');



  // Adiciona ouvinte de evento de mudança aos campos de entrada de data
  dataInicialInput.addEventListener('change', lista);
  dataFinalInput.addEventListener('change', lista);






  function subistituir_desenho_modal(id) {

    $.ajax({
      url: '<?= base_url('public/adm/subistituir_desenho_modal') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { id: id },
      success: function (response) {





        console.log(response);
        var botao_confirmar_modal = document.getElementById('botao_confirmar_modal');


        botao_confirmar_modal.innerHTML = "Confirmar";
        var modal_titulo = document.getElementById('modal_titulo');
        var modal_bory = document.getElementById('modal_bory');
        modal_titulo.textContent = "Subistiruit desenho";





        modal_bory.innerHTML = '';






        divElemnt = document.createElement("div");
        divElemnt.classList.add("form-group");
        inputElement = document.createElement("input");
        inputElement.type = 'text';
        inputElement.id = 'novo_nome_arquivo';
        inputElement.classList.add("form-control");
        inputElement.value = response.nome;
        divElemnt.innerHTML = '';
        labelElement = document.createElement("label");
        labelElement.textContent = "Novo nome do arquivo";
        divElemnt.appendChild(labelElement);
        divElemnt.appendChild(inputElement);
        modal_bory.appendChild(divElemnt);//coloca o input name no modal


        divElemnt = document.createElement("div");
        divElemnt.classList.add("form-group");
        inputElement = document.createElement("input");
        inputElement.type = 'file';
        inputElement.id = 'novo_arquvivo';
        inputElement.classList.add("form-control");
        divElemnt.innerHTML = '';
        labelElement = document.createElement("label");
        labelElement.textContent = "Novo arquivo";
        divElemnt.appendChild(labelElement);
        divElemnt.appendChild(inputElement);
        modal_bory.appendChild(divElemnt);//coloca o input name no modal

        botao_confirmar_modal.onclick =
          function () {
            var nome = document.getElementById("novo_nome_arquivo").value;
            var fileInput = document.getElementById('novo_arquvivo');
            var file = fileInput.files[0];

            $.ajax({
              url: '<?= base_url('public/adm/desenho_novo_nome') ?>',
              type: "POST",
              dataType: "json",
              data: { nome: nome },
              success: function (response) {
                console.log(response);
              }
            });

            var formData = new FormData();
            formData.append('file', file);

            $.ajax({
              url: '<?= base_url('public/adm/subistituir_desenho') ?>',
              type: "POST",
              dataType: "json",
              processData: false,
              contentType: false,
              data: formData,
              success: function (response) {
                console.log(response);
                fecharModal();
                if (response.ok == 'true') {
                  fecharModal();
                  alert_certo('Desenho', response.mensagem);
                } else {
                  fecharModal();
                  alert_personalizado('Desenho', response.mensagem);
                }
              },
              error: function (xhr, status, error) {

                confirmarModal();
              }
            });
          };


        mostrarModal();

      }

    });


  }

  function confirmarModal() {
    var nome = document.getElementById("novo_nome_arquivo").value;
    var fileInput = document.getElementById('novo_arquvivo');
    var file = fileInput.files[0];
    console.log(fileInput.files);
    $.ajax({
      url: '<?= base_url('public/adm/desenho_novo_nome') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON

      data: { nome: nome },

      success: function (response) {
        console.log(response);
      }
    });


    var formData = new FormData();
    formData.append('file', file);
    $.ajax({
      url: '<?= base_url('public/adm/subistituir_desenho') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      processData: false,
      contentType: false,
      data: formData,

      success: function (response) {
        console.log(response);
        fecharModal();
        if (response.ok == 'true') {

          fecharModal();

          alert_certo('Desenho', response.mensagem);
        } else {


          fecharModal();
          alert_personalizado('Desenho', response.mensagem);
        }


      }
    });

  }




  function recolocar_desenho(id) {
    if(mostrarConfirmacao("Recolocar desenho?")){
    $.ajax({
      url: '<?= base_url('public/adm/recolocar_desenho') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON

      data: { id: id },

      success: function (response) {
        console.log(response);
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
</script>