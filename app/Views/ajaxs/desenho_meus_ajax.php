<script>
  var roda_pe = document.getElementById('roda_pe');
  roda_pe.innerHTML = '<div style="position: relative; width: 100%; height: 50px;"><div style="position: absolute; top: 50%;"><button name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="prio_modal_todos()"> Mudar prioridade de varios </button></div><div style="position: absolute; top: 50%; right: 0;"><button name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="apagar_todos()"> Apagar varios</button></div></div>';


  lista_temp = "";
  function lista() {
    data = document.getElementById('dataInicial').value;
    data1 = document.getElementById('dataFinal').value;
    $.ajax({
      url: '<?= base_url('public/desenho_meus') ?>',
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


  const dataInicialInput = document.getElementById('dataInicial');
  const dataFinalInput = document.getElementById('dataFinal');



  // Adiciona ouvinte de evento de mudança aos campos de entrada de data
  dataInicialInput.addEventListener('change', lista);
  dataFinalInput.addEventListener('change', lista);





  lista_temp1 = "";
  function value_prioridade(efeturar = false) {
    $.ajax({
      url: '<?= base_url('public/prioridade_lista') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      success: function (response) {
        if (document.getElementById("prioridade_novo") != null && (response != lista_temp1 || efeturar)) {
          // Obter referência ao elemento select
          var funcao = document.getElementById("prioridade_novo");
          // Armazenar o valor da opção selecionada antes de limpar o select
          var valorSelecionadoAntes = funcao.value;

          // Limpar o select
          funcao.innerHTML = '';

          // Criar um novo elemento option
          var novoOption = document.createElement("option");

          // Definir o valor e texto do novo elemento option
          novoOption.value = '';
          novoOption.textContent = 'Prioridade';

          // Adicionar o novo elemento option ao select
          funcao.appendChild(novoOption);

          response.lista.forEach(element => {



            // Criar um novo elemento option
            var novoOption = document.createElement("option");

            // Definir o valor e texto do novo elemento option
            novoOption.value = element.prioridade;
            novoOption.textContent = element.prioridade;
            novoOption.style.backgroundColor = element.cor;
            novoOption.style.color = inverterCor(element.cor);
            funcao.appendChild(novoOption);
          });
          var opcoes = funcao.options;
          for (var i = 0; i < opcoes.length; i++) {
            if (opcoes[i].value === valorSelecionadoAntes) {
              opcoes[i].selected = true;
              break;
            }
          }


          lista_temp1 = response;
        }
      }
    });
  }
  // Executar função ao abrir o site
  document.addEventListener('DOMContentLoaded', value_prioridade);



  function inverterCor(hex) {
    // Verificar se a cor é válida (começa com # seguido por 6 caracteres hexadecimais)
    var regex = /^#[0-9A-F]{6}$/i;
    if (!regex.test(hex)) {
      throw new Error("Cor inválida. Use notação hexadecimal de 6 dígitos, começando com '#'.");
    }

    // Extrair os componentes de cor
    var red = parseInt(hex.substr(1, 2), 16);
    var green = parseInt(hex.substr(3, 2), 16);
    var blue = parseInt(hex.substr(5, 2), 16);

    // Inverter os componentes de cor
    red = 255 - red;
    green = 255 - green;
    blue = 255 - blue;

    // Converter os valores invertidos de volta para notação hexadecimal
    var invertedHex = "#" + ((1 << 24) | (red << 16) | (green << 8) | blue).toString(16).slice(1);

    return invertedHex;
  }

  var lista;

  function prio_modal(id) {
    if (event.shiftKey) {
      id = "";
    }

    $.ajax({
      url: '<?= base_url('public/desenho_meus_modal') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { id: id },
      success: function (response) {




        console.log(response);
        var botao_confirmar_modal = document.getElementById('botao_confirmar_modal');

        document.getElementById('modal_sizer').classList.add('modal-xl');
        botao_confirmar_modal.innerHTML = "Confirmar";
        botao_confirmar_modal.onclick = '';
        botao_confirmar_modal.id = 'botao_confirmar_modal1';



        // Passo 1: Selecionar o elemento HTML (neste caso, o botão)
        var botao = document.getElementById('botao_confirmar_modal1');

        // Passo 2: Modificar o evento onclick existente
        botao.onclick = function () {
          array = [];
          ok = false;
          for (let index = 0; index < lista.length; index++) {
            if (document.getElementById("prio_" + lista[index]['id']) != null) {
              if (document.getElementById("prio_" + lista[index]['id']).checked) {
                array.push(lista[index]['id']);
                ok = true;
              }
            }

          }
          prioridade = document.getElementById("prioridade_novo").value;
          if (ok && prioridade != '') {

            $.ajax({
              url: '<?= base_url('public/desenho_update') ?>',
              type: "POST",
              dataType: "json", // Indicar que o retorno é em formato JSON
              data: { array: array, prioridade: prioridade },
              success: function (response) {



                if (response.ok) {
                  fecharModal();
                  data = document.getElementById('dataInicial').value;
                  data1 = document.getElementById('dataFinal').value;
                  $.ajax({
                    url: '<?= base_url('public/desenho_meus') ?>',
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
                } else {
                  console.log('erro');
                }



              }

            });
          }
        };



        var modal_titulo = document.getElementById('modal_titulo');
        var modal_bory = document.getElementById('modal_bory');
        modal_titulo.textContent = "Modificar prioridade desenho";
        const selectElement = document.createElement("select");
        var inputElement = document.createElement("input");

        var divElemnt = document.createElement("div");
        divElemnt.classList.add("form-group");

        modal_bory.innerHTML = '';
        // Limpar o select
        selectElement.innerHTML = '';
        // Criar um novo elemento option

        var novoOption = document.createElement("option");



        divElemnt = document.createElement("div");
        divElemnt.classList.add("form-group");
        // Definir o valor e texto do novo elemento option
        novoOption.value = response.empresa_id;
        novoOption.textContent = response.empresa_id;
        selectElement.id = 'prioridade_novo';
        selectElement.classList.add("custom-select");
        selectElement.appendChild(novoOption);
        labelElement = document.createElement("label");
        labelElement.textContent = "Prioridade";
        divElemnt.appendChild(labelElement);
        divElemnt.appendChild(selectElement);
        modal_bory.appendChild(divElemnt);//coloca o input name no modal



        divElemnt = document.createElement("div");
        divElemnt.classList.add("form-group");
        lista = response.lista;
        tabel_bory = document.createElement("table");
        tr = document.createElement('tr');
        th = document.createElement('th');
        th.textContent = 'Nome';

        tr.appendChild(th);
        th = document.createElement('th');
        th.textContent = 'Prioridade';

        tr.appendChild(th);
        th = document.createElement('th');
        th.textContent = 'Finalidade';

        tr.appendChild(th);
        th = document.createElement('th');
        th.textContent = 'Empresa/Cliente';

        tr.appendChild(th);
        th = document.createElement('th');
        th.textContent = 'Empreendimento';

        tr.appendChild(th);
        th = document.createElement('th');
        th.textContent = 'Data de Envio';

        tr.appendChild(th);
        tabel_bory.appendChild(tr);
        th = document.createElement('th');
        th.textContent = '';

        tr.appendChild(th);
        tabel_bory.appendChild(tr);
        console.log(lista);
        for (let index = 0; index < response.lista.length; index++) {

          if (lista[index]['status'] == 'corte') {
            tr = document.createElement('tr');
            if (index % 2 == 0) {
              tr.classList.add('odd');
            } else {
              tr.classList.add('even');
            }

            inputElement = document.createElement("input");
            inputElement.type = 'checkbox';
            inputElement.id = 'prio_' + lista[index]['id'];
            inputElement.classList.add("form-control");
            inputElement.value = '';
            labelElement = document.createElement("label");
            labelElement.textContent = lista[index]['nome'];
            th = document.createElement('th');
            th.appendChild(labelElement);
            tr.appendChild(th);

            labelElement = document.createElement("label");


            th = document.createElement('th');
            th.appendChild(labelElement);
            p = document.createElement("p");
            p.textContent = lista[index]['prioridade'];
            p.classList.add('marca_texto');
            th.style.backgroundColor = lista[index]['cor'];

            th.appendChild(p);
            tr.appendChild(th);

            labelElement = document.createElement("label");
            labelElement.textContent = lista[index]['finalidade'];
            th = document.createElement('th');
            th.appendChild(labelElement);
            tr.appendChild(th);

            labelElement = document.createElement("label");
            labelElement.textContent = lista[index]['empresa'];
            th = document.createElement('th');
            th.appendChild(labelElement);
            tr.appendChild(th);

            labelElement = document.createElement("label");
            labelElement.textContent = lista[index]['empreendimento'];
            th = document.createElement('th');
            th.appendChild(labelElement);
            tr.appendChild(th);

            labelElement = document.createElement("label");
            labelElement.textContent = lista[index]['data_hora_add'];
            th = document.createElement('th');
            th.appendChild(labelElement);
            tr.appendChild(th);




            th = document.createElement('th');
            th.appendChild(inputElement);
            tr.appendChild(th);
            tabel_bory.appendChild(tr);
          }

        }
        modal_bory.appendChild(divElemnt);//coloca o input name no modal


        tabel_bory.classList.add('table', 'table-bordered', 'table-striped');
        modal_bory.appendChild(tabel_bory);
        value_prioridade();
        mostrarModal();

      }

    });

  }



  function confirmar_botao_apagar() {
    var check_box = document.getElementById('modal_apagar');
    var botao_confirmar_modal = document.getElementById('botao_confirmar_modal_apagar');
    if (check_box.checked) {
      botao_confirmar_modal.disabled = false;
    } else {
      botao_confirmar_modal.disabled = true;
    }
  }

  function apagar_todos() {

    var modal_rodape = document.getElementById('modal_rodape');
    modal_rodape.innerHTML = "  <input id=\"modal_apagar\" style=\"height: 25px; width: 25px;\" class=\"form-control\" onClick=\"confirmar_botao_apagar()\" type=\"checkbox\"><label id=\"modal_apagar\">Apagar</label>" + modal_rodape.innerHTML;


    var botao_confirmar_modal = document.getElementById('botao_confirmar_modal');
    document.getElementById('modal_sizer').classList.add('modal-xl');
    botao_confirmar_modal.innerHTML = "Apagar";
    botao_confirmar_modal.onclick = '';
    botao_confirmar_modal.id = 'botao_confirmar_modal_apagar';
    botao_confirmar_modal.disabled = true;
    var modal_titulo = document.getElementById('modal_titulo');
    var modal_bory = document.getElementById('modal_bory');
    modal_titulo.textContent = "Apagar desenhos";
    const selectElement = document.createElement("select");
    var inputElement = document.createElement("input");

    var divElemnt = document.createElement("div");
    divElemnt.classList.add("form-group");

    modal_bory.innerHTML = '';




    divElemnt = document.createElement("div");
    divElemnt.classList.add("form-group");

    tabel_bory = document.createElement("table");
    tr = document.createElement('tr');
    th = document.createElement('th');
    th.textContent = 'Nome';

    tr.appendChild(th);
    th = document.createElement('th');
    th.textContent = 'Prioridade';

    tr.appendChild(th);
    th = document.createElement('th');
    th.textContent = 'Finalidade';

    tr.appendChild(th);
    th = document.createElement('th');
    th.textContent = 'Empresa/Cliente';

    tr.appendChild(th);
    th = document.createElement('th');
    th.textContent = 'Empreendimento';

    tr.appendChild(th);
    th = document.createElement('th');
    th.textContent = 'Data de Envio';

    tr.appendChild(th);
    tabel_bory.appendChild(tr);
    th = document.createElement('th');
    th.textContent = '';

    tr.appendChild(th);
    tabel_bory.appendChild(tr);

    $.ajax({
      url: '<?= base_url('public/desenho_modal') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { id: "" },
      success: function (response) {
        var lista = response.lista;








        var element;
        console.log(response);
        for (let index = 0; index < lista.length; index++) {

          if (lista[index]['status'] == 'corte') {

            tr = document.createElement('tr');
            if (index % 2 == 0) {
              tr.classList.add('odd');
            } else {
              tr.classList.add('even');
            }
            nome_de = lista[index]['nome'];
            var ponto_nome = 0;
            for (let i = 0; i < nome_de.length; i++) {
              if (nome_de[i] + nome_de[i + 1] == "_.") {
                ponto_nome = i + 1;
              }
            }


            inputElement = document.createElement("input");
            inputElement.type = 'checkbox';
            inputElement.id = 'apagar_' + lista[index]['id'];
            inputElement.classList.add("form-control");
            inputElement.value = '';
            inputElement.style.height = '25px';
            inputElement.style.width = '25px';
            labelElement = document.createElement("label");
            labelElement.textContent = nome_de.slice(0, ponto_nome - 5) + nome_de.slice(ponto_nome, nome_de.length);
            th = document.createElement('th');
            th.appendChild(labelElement);
            tr.appendChild(th);

            labelElement = document.createElement("label");


            th = document.createElement('th');
            th.appendChild(labelElement);
            p = document.createElement("p");
            p.textContent = lista[index]['prioridade'];
            p.classList.add('marca_texto');
            th.style.backgroundColor = lista[index]['cor'];

            th.appendChild(p);
            tr.appendChild(th);

            labelElement = document.createElement("label");
            labelElement.textContent = lista[index]['finalidade'];
            th = document.createElement('th');
            th.appendChild(labelElement);
            tr.appendChild(th);

            labelElement = document.createElement("label");
            labelElement.textContent = lista[index]['empresa'];
            th = document.createElement('th');
            th.appendChild(labelElement);
            tr.appendChild(th);

            labelElement = document.createElement("label");
            labelElement.textContent = lista[index]['empreendimento'];
            th = document.createElement('th');
            th.appendChild(labelElement);
            tr.appendChild(th);

            labelElement = document.createElement("label");
            labelElement.textContent = lista[index]['data_hora_add'];
            th = document.createElement('th');
            th.appendChild(labelElement);
            tr.appendChild(th);




            th = document.createElement('th');
            th.appendChild(inputElement);
            tr.appendChild(th);
            tabel_bory.appendChild(tr);
          }
        }
        var botao = document.getElementById('botao_confirmar_modal_apagar');

        botao.onclick = function () {


          for (let index = 0; index < lista.length; index++) {
            if (document.getElementById("apagar_" + lista[index]['id']) != null) {
              if (document.getElementById("apagar_" + lista[index]['id']).checked) {
                apagar_mesmo(lista[index]['id']);


              }
            }

          }
          fecharModal();
          data = document.getElementById('dataInicial').value;
          data1 = document.getElementById('dataFinal').value;
          $.ajax({
            url: '<?= base_url('public/desenho_meus') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: { data: data, data1: data1 },
            success: function (response) {


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
          });

        };

        modal_bory.appendChild(divElemnt);//coloca o input name no modal


        tabel_bory.classList.add('table', 'table-bordered', 'table-striped');
        modal_bory.appendChild(tabel_bory);
        mostrarModal();
      }
    });






  }

  function apagar(id = "") {
    if (event.shiftKey) {
      id = "";
    }
    var modal_rodape = document.getElementById('modal_rodape');
    modal_rodape.innerHTML = "  <input id=\"modal_apagar\" style=\"height: 25px; width: 25px;\" class=\"form-control\" onClick=\"confirmar_botao_apagar()\" type=\"checkbox\"><label id=\"modal_apagar\">Apagar</label>" + modal_rodape.innerHTML;


    var botao_confirmar_modal = document.getElementById('botao_confirmar_modal');
    botao_confirmar_modal.innerHTML = "Apagar";
    botao_confirmar_modal.onclick = '';
    botao_confirmar_modal.id = 'botao_confirmar_modal_apagar';
    botao_confirmar_modal.disabled = true;
    var modal_titulo = document.getElementById('modal_titulo');
    var modal_bory = document.getElementById('modal_bory');
    modal_titulo.textContent = "Apagar desenhos";
    const selectElement = document.createElement("select");
    var inputElement = document.createElement("input");

    var divElemnt = document.createElement("div");
    divElemnt.classList.add("form-group");

    modal_bory.innerHTML = '';




    divElemnt = document.createElement("div");
    divElemnt.classList.add("form-group");

    $.ajax({
      url: '<?= base_url('public/desenho_modal') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { id: id },
      success: function (response) {
        var lista = response.lista;
        modal_titulo.textContent = "Apagar desenho: " + removeIdFromFile(response.lista[0].nome);







        console.log(response);

        var botao = document.getElementById('botao_confirmar_modal_apagar');

        botao.onclick = function () {


          apagar_mesmo(lista[0]['id']);


          fecharModal();
          data = document.getElementById('dataInicial').value;
          data1 = document.getElementById('dataFinal').value;
          $.ajax({
            url: '<?= base_url('public/desenho_meus') ?>',
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

        };

        modal_bory.appendChild(divElemnt);//coloca o input name no modal



        mostrarModal();
      }
    });






  }



  function apagar_mesmo(id) {
    $.ajax({
      url: '<?= base_url('public/nome_desenho') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { id: id },
      success: function (response) {

        $.ajax({
          url: '<?= base_url('public/apagar_desenho') ?>',
          type: "POST",
          dataType: "json", // Indicar que o retorno é em formato JSON
          data: { id: id },
          success: function (response) {
            if (response.ok == 'true') {

              alert_certo('Desenho', response.mensagem);
            } else if (response.ok == 'false') {

              alert_personalizado('Desenho', response.mensagem_false);
              alert_certo('Desenho', response.mensagem);
            } else {
              apagar_mesmo(id)
            }

            data = document.getElementById('dataInicial').value;
            data1 = document.getElementById('dataFinal').value;
            $.ajax({
              url: '<?= base_url('public/desenho_meus') ?>',
              type: "POST",
              dataType: "json", // Indicar que o retorno é em formato JSON
              data: { data: data, data1: data1 },
              success: function (response) {


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
            });







          }
        });

      }
    });
  }


  function subistituir_desenho_modal(id) {

    $.ajax({
      url: '<?= base_url('public/subistituir_desenho_modal') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { id: id },
      success: function (response) {





        console.log(response);
        var botao_confirmar_modal = document.getElementById('botao_confirmar_modal');


        botao_confirmar_modal.innerHTML = "Confirmar";
        var modal_titulo = document.getElementById('modal_titulo');
        var modal_bory = document.getElementById('modal_bory');
        modal_titulo.textContent = "Renomear/Substituir desenho";





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
              url: '<?= base_url('public/desenho_novo_nome') ?>',
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
              url: '<?= base_url('public/subistituir_desenho') ?>',
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
                data = document.getElementById('dataInicial').value;
                data1 = document.getElementById('dataFinal').value;
                $.ajax({
                  url: '<?= base_url('public/desenho_meus') ?>',
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
              },
              error: function (xhr, status, error) {

                confirmarModal();
              }
            });
          };


        $.ajax({
          url: '<?= base_url('public/lista_filtro') ?>',
          type: "POST",
          async: false,
          dataType: "json", // Indicar que o retorno é em formato JSON
          success: function (response) {
            if (response.lista != lista_temp1 || efeturar) {
              // Obter referência ao elemento select
              var desenho = document.getElementById("novo_arquvivo");
              // Armazenar o valor da opção selecionada antes de limpar o select
              desenho.accept = response.lista;
            }
          }
        });

        mostrarModal();

      }

    });


  }
  var lista;
  function prio_modal_todos() {


    $.ajax({
      url: '<?= base_url('public/desenho_modal') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { id: "" },
      success: function (response) {

        var botao_confirmar_modal = document.getElementById('botao_confirmar_modal');



        botao_confirmar_modal.onclick = function () {
          array = [];
          ok = false;
          for (let index = 0; index < lista.length; index++) {
            if (document.getElementById("prio_" + lista[index]['id']) != null) {
              if (document.getElementById("prio_" + lista[index]['id']).checked) {
                array.push(lista[index]['id']);
                ok = true;
              }
            } else {
              if (lista.length == 1) {
                array.push(lista[index]['id']);
                ok = true;
              }

            }

          }
          if (ok) {
            prioridade = document.getElementById("prioridade_novo").value;
            $.ajax({
              url: '<?= base_url('public/desenho_update') ?>',
              type: "POST",
              dataType: "json", // Indicar que o retorno é em formato JSON
              data: { array: array, prioridade: prioridade },
              success: function (response) {



                if (response.ok) {
                  fecharModal();
                  data = document.getElementById('dataInicial').value;
                  data1 = document.getElementById('dataFinal').value;
                  $.ajax({
                    url: '<?= base_url('public/desenho_meus') ?>',
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
                } else {
                  console.log('erro');
                }



              }

            });
          }


        };

        document.getElementById('modal_sizer').classList.add('modal-xl');
        botao_confirmar_modal.innerHTML = "Confirmar";
        var modal_titulo = document.getElementById('modal_titulo');
        var modal_bory = document.getElementById('modal_bory');
        modal_titulo.textContent = "Modificar prioridade desenho";
        const selectElement = document.createElement("select");
        var inputElement = document.createElement("input");

        var divElemnt = document.createElement("div");
        divElemnt.classList.add("form-group");

        modal_bory.innerHTML = '';
        // Limpar o select
        selectElement.innerHTML = '';
        // Criar um novo elemento option

        var novoOption = document.createElement("option");



        divElemnt = document.createElement("div");
        divElemnt.classList.add("form-group");
        // Definir o valor e texto do novo elemento option
        novoOption.value = response.empresa_id;
        novoOption.textContent = response.empresa_id;
        selectElement.id = 'prioridade_novo';
        selectElement.classList.add("custom-select");
        selectElement.appendChild(novoOption);
        labelElement = document.createElement("label");
        labelElement.textContent = "Prioridade";
        divElemnt.appendChild(labelElement);
        divElemnt.appendChild(selectElement);
        modal_bory.appendChild(divElemnt);//coloca o input name no modal



        divElemnt = document.createElement("div");
        divElemnt.classList.add("form-group");
        lista = response.lista;
        tabel_bory = document.createElement("table");
        tr = document.createElement('tr');
        th = document.createElement('th');
        th.textContent = 'Nome';

        tr.appendChild(th);
        th = document.createElement('th');
        th.textContent = 'Prioridade';

        tr.appendChild(th);
        th = document.createElement('th');
        th.textContent = 'Finalidade';

        tr.appendChild(th);
        th = document.createElement('th');
        th.textContent = 'Empresa/Cliente';

        tr.appendChild(th);
        th = document.createElement('th');
        th.textContent = 'Empreendimento';

        tr.appendChild(th);
        th = document.createElement('th');
        th.textContent = 'Data de Envio';

        tr.appendChild(th);
        tabel_bory.appendChild(tr);
        th = document.createElement('th');
        th.textContent = '';

        tr.appendChild(th);
        tabel_bory.appendChild(tr);


        for (let index = 0; index < response.lista.length; index++) {
          if (lista[index]['status'] == 'corte') {
            tr = document.createElement('tr');
            if (index % 2 == 0) {
              tr.classList.add('odd');
            } else {
              tr.classList.add('even');
            }

            inputElement = document.createElement("input");
            inputElement.type = 'checkbox';
            inputElement.id = 'prio_' + lista[index]['id'];
            inputElement.classList.add("form-control");
            inputElement.value = '';
            labelElement = document.createElement("label");
            labelElement.textContent = lista[index]['nome'];
            th = document.createElement('th');
            th.appendChild(labelElement);
            tr.appendChild(th);

            labelElement = document.createElement("label");


            th = document.createElement('th');
            th.appendChild(labelElement);
            p = document.createElement("p");
            p.textContent = lista[index]['prioridade'];
            p.classList.add('marca_texto');
            th.style.backgroundColor = lista[index]['cor'];

            th.appendChild(p);
            tr.appendChild(th);

            labelElement = document.createElement("label");
            labelElement.textContent = lista[index]['finalidade'];
            th = document.createElement('th');
            th.appendChild(labelElement);
            tr.appendChild(th);

            labelElement = document.createElement("label");
            labelElement.textContent = lista[index]['empresa'];
            th = document.createElement('th');
            th.appendChild(labelElement);
            tr.appendChild(th);

            labelElement = document.createElement("label");
            labelElement.textContent = lista[index]['empreendimento'];
            th = document.createElement('th');
            th.appendChild(labelElement);
            tr.appendChild(th);

            labelElement = document.createElement("label");
            labelElement.textContent = lista[index]['data_hora_add'];
            th = document.createElement('th');
            th.appendChild(labelElement);
            tr.appendChild(th);




            th = document.createElement('th');
            th.appendChild(inputElement);
            tr.appendChild(th);
            tabel_bory.appendChild(tr);


          }
        }
        modal_bory.appendChild(divElemnt);//coloca o input name no modal


        tabel_bory.classList.add('table', 'table-bordered', 'table-striped');
        modal_bory.appendChild(tabel_bory);
        value_prioridade();
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
      url: '<?= base_url('public/desenho_novo_nome') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      async: false,
      data: { nome: nome },

      success: function (response) {
        console.log(response);
      }
    });


    var formData = new FormData();
    formData.append('file', file);
    $.ajax({
      url: '<?= base_url('public/subistituir_desenho') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      processData: false,
      contentType: false,
      data: formData,

      success: function (response) {
        console.log(response);
        fecharModal();
        if (response.ok == 'true') {
          data = document.getElementById('dataInicial').value;
          data1 = document.getElementById('dataFinal').value;
          $.ajax({
            url: '<?= base_url('public/desenho_meus') ?>',
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
      url: '<?= base_url('public/recolocar_desenho') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      //async: true,
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
  function removeIdFromFile(str) {
    // Usa uma expressão regular para procurar um ID no formato '_<números>_' na string.
    const matches = str.match(/_([0-9]+)_/);

    if (matches && matches[0]) {
      const id = matches[0];
      // Remove o ID encontrado da string e retorna o resultado.
      return str.replace(id, '');
    } else {
      // Se nenhum ID for encontrado na string, retorna a string original.
      return str;
    }
  }

  //   // Repetir função a cada segundo
  //   setInterval(lista, 1000);
  // // Repetir função a cada segundo
  // setInterval(value_prioridade, 1000);

</script>