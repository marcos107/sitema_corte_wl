<script>
  var roda_pe = document.getElementById('roda_pe');
  roda_pe.innerHTML = '<div style="position: relative; width: 100%; height: 50px;"><div style="position: absolute; top: 50%;"><button name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="prio_modal_todos()"> Mudar prioridade de varios </button></div><div style="position: absolute; top: 50%; right: 0;"><button name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="apagar_todos()"> Apagar varios</button></div></div>';

  check_prima = true;

  lista_temp = "";



  function lista_corte() {
    checkbox = document.getElementById("cortadorCheckbox");
    var checkboxValue = checkbox ? (checkbox.checked ? "true" : "false") : "";

    $.ajax({
      url: '<?= base_url('public/lista_corte_adm') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { check: checkboxValue },
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
              "responsive": true,
              "lengthChange": false,
              "autoWidth": false,
              
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
            });

            // Adiciona a checkbox manualmente ao container do DataTable
            $('#example1_wrapper .col-md-6:eq(0)').append(
              '<input type="checkbox" id="cortadorCheckbox" onclick="lista_corte()">Som ao adicionar desenhos'
            );
          });



          lista_temp = response.lista;
        }
        if (response.check != "") {
          setTimeout(function () {
            checkbox = document.getElementById("cortadorCheckbox");
            check_prima = false;
            checkbox.checked = (response.check == "true");
          }, 100);
        }


        // 

        // if (checkbox) {
        //   checkbox.checked = response.check == "true" ? true : false;
        // }
      }



    });
  }
  // Executar função ao abrir o site
  document.addEventListener('DOMContentLoaded', lista_corte);





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


  function prio_modal_todos() {
  $.ajax({
    url: '<?= base_url('public/desenho_modal') ?>',
    type: "POST",
    dataType: "json",
    data: { id: "" },
    success: function (response) {
      document.getElementById('modal_sizer').classList.add('modal-xl');
      const modalBory = document.getElementById('modal_bory');
      const modalTitulo = document.getElementById('modal_titulo');
      const botaoConfirmarModal = document.getElementById('botao_confirmar_modal');

      modalBory.innerHTML = ''; // Limpa o conteúdo anterior
      modalTitulo.textContent = "Modificar prioridade desenho";
      botaoConfirmarModal.innerHTML = "Confirmar";

      // Ajusta a largura do modal
      document.getElementById('modal_sizer').classList.add('modal-xxl'); // Ajuste para um tamanho maior



      // Adiciona o campo select
      const divSelect = document.createElement("div");
      divSelect.classList.add("form-group");

      const labelPrioridade = document.createElement("label");
      labelPrioridade.textContent = "Prioridade";
      divSelect.appendChild(labelPrioridade);

      const selectPrioridade = document.createElement("select");
      selectPrioridade.classList.add("custom-select");
      selectPrioridade.id = "prioridade_novo";

      const novoOption = document.createElement("option");
      novoOption.value = response.empresa_id;
      novoOption.textContent = response.empresa_id;
      selectPrioridade.appendChild(novoOption);

      divSelect.appendChild(selectPrioridade);
      modalBory.appendChild(divSelect);

      // Criação da tabela
      const tabela = document.createElement("table");
      tabela.classList.add("table", "table-bordered", "table-striped");
      tabela.id = "tabelaPrioridade";

      const thead = document.createElement("thead");
      thead.innerHTML = `
    <tr>
      <th>Prioridade</th>
      
      <th> Processos </th>
      <th> Desenhista </th>
      <th>Nome do arquivo</th>
      <th>Empresa/Cliente</th>
      <th>Empreendimento</th>
      <th>Finalidade</th>
      <th>Subpastas</th>
      <th>Data de Envio</th>
      <th>Selecionar</th>
    </tr>
  `;
      tabela.appendChild(thead);

      const tbody = document.createElement("tbody");
      response.lista.forEach((item) => {
        if (item.status === 'pendente') {
          const tr = document.createElement("tr");

          tr.innerHTML = `
            <td style="background-color: ${item.cor};">${item.prioridade}</td>
           
            <td>${item.processo}</td>
            <td>${item.desenhista_nome}</td>
             <td>${item.nome}</td>
            
            <td>${item.empresa}</td>
            <td>${item.empreendimento}</td>
            <td>${item.finalidade}</td>
            <td>${item.tags}</td>
            <td>${item.data_hora_add}</td>
            <td>
              <input type="checkbox" id="prio_${item.id}" class="form-control">
            </td>
          `;
          tbody.appendChild(tr);
        }
      });

      tabela.appendChild(tbody);
      modalBory.appendChild(tabela);

 // Inicializa o DataTable com paginação
 $('#tabelaPrioridade').DataTable({
        responsive: true,
        lengthChange: false,
        autoWidth: false,
        pageLength: 10, // Número de registros por página
        language: {
          decimal: "",
          emptyTable: "Sem dados disponíveis",
          infoEmpty: "Mostrando de 0 até 0 de 0 registros",
          infoFiltered: "(filtrado de _MAX_ registros no total)",
          infoPostFix: "",
          thousands: ",",
          lengthMenu: "Mostrar _MENU_ registros",
          loadingRecords: "A carregar dados...",
          processing: "A processar...",
          search: "Buscar:",
          zeroRecords: "Não foram encontrados resultados",
          paginate: {
            first: "Primeiro",
            last: "Último",
            next: "Seguinte",
            previous: "Anterior"
          },
          aria: {
            sortAscending: ": clique para ordenar ascendente (ASC)",
            sortDescending: ": clique para ordenar descendente (DESC)"
          }
        }
      });
      value_prioridade();
      mostrarModal(); // Exibe o modal
    },
    error: function (xhr, status, error) {
      console.error("Erro na requisição AJAX:", error);
    },
  });
}


  function prio_modal(id) {

    $.ajax({
      url: '<?= base_url('public/desenho_modal') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { id: id },
      success: function (response) {



        lista = response.lista;
        var botao_confirmar_modal = document.getElementById('botao_confirmar_modal');

        document.getElementById('modal_sizer').classList.add('modal-xl');
        botao_confirmar_modal.innerHTML = "Confirmar";

        var modal_titulo = document.getElementById('modal_titulo');
        var modal_bory = document.getElementById('modal_bory');
        modal_titulo.textContent = "Modificar prioridade desenho: " + removeIdFromFile(response.lista[0].nome);
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




        modal_bory.appendChild(divElemnt);//coloca o input name no modal


        tabel_bory.classList.add('table', 'table-bordered', 'table-striped');
        modal_bory.appendChild(tabel_bory);
        value_prioridade();
        mostrarModal();

      }

    });

  }


  function confirmarModal() {
    array = [];
    ok = false;
    for (let index = 0; index < lista.length; index++) {
      if (document.getElementById("prio_" + index) != null) {
        if (document.getElementById("prio_" + index).checked) {
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
          

          } else {
            console.log('erro');
          }
          lista_corte();


        }

      });
    }
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
  const modalRodape = document.getElementById('modal_rodape');
  modalRodape.innerHTML = `
    <input id="modal_apagar" style="height: 25px; width: 25px;" class="form-control" onClick="confirmar_botao_apagar()" type="checkbox">
    <label id="modal_apagar">Apagar</label>
    ${modalRodape.innerHTML}
  `;

  const botaoConfirmarModal = document.getElementById('botao_confirmar_modal');
  const modalBory = document.getElementById('modal_bory');
  const modalTitulo = document.getElementById('modal_titulo');

  // Configurações do modal
  document.getElementById('modal_sizer').classList.add('modal-xl');
  modalTitulo.textContent = "Apagar desenhos";
  botaoConfirmarModal.innerHTML = "Apagar";
  botaoConfirmarModal.id = 'botao_confirmar_modal_apagar';
  botaoConfirmarModal.disabled = true;

  modalBory.innerHTML = ''; // Limpa o conteúdo anterior

  // Criação da tabela
  const tabela = document.createElement("table");
  tabela.classList.add("table", "table-bordered", "table-striped");
  tabela.id = "tabelaApagar";

  const thead = document.createElement("thead");
  thead.innerHTML = `
    <tr>
      <th>Prioridade</th>
      
      <th> Processos </th>
      <th> Desenhista </th>
      <th>Nome do arquivo</th>
      <th>Empresa/Cliente</th>
      <th>Empreendimento</th>
      <th>Finalidade</th>
      <th>Subpastas</th>
      <th>Data de Envio</th>
      <th>Selecionar</th>
    </tr>
  `;
  tabela.appendChild(thead);

  const tbody = document.createElement("tbody");

  $.ajax({
    url: '<?= base_url('public/desenho_modal') ?>',
    type: "POST",
    dataType: "json",
    data: { id: "" },
    success: function (response) {
      const lista = response.lista;

      lista.forEach((item) => {
        if (item.status === 'pendente') {
          const tr = document.createElement("tr");

          tr.innerHTML = `
            <td style="background-color: ${item.cor};">${item.prioridade}</td>
           
            <td>${item.processo}</td>
            <td>${item.desenhista_nome}</td>
             <td>${item.nome}</td>
            
            <td>${item.empresa}</td>
            <td>${item.empreendimento}</td>
            <td>${item.finalidade}</td>
            <td>${item.tags}</td>
            <td>${item.data_hora_add}</td>
            <td>
              <input type="checkbox" id="apagar_${item.id}" class="form-control">
            </td>
          `;
          tbody.appendChild(tr);
        }
      });

      tabela.appendChild(tbody);
      modalBory.appendChild(tabela);

      // Inicializa o DataTable
      $('#tabelaApagar').DataTable({
        responsive: true,
        lengthChange: false,
        autoWidth: false,
        pageLength: 10,
        language: {
          decimal: "",
          emptyTable: "Sem dados disponíveis",
          infoEmpty: "Mostrando de 0 até 0 de 0 registros",
          infoFiltered: "(filtrado de MAX registros no total)",
          infoPostFix: "",
          thousands: ",",
          lengthMenu: "Mostrar MENU registros",
          loadingRecords: "A carregar dados...",
          processing: "A processar...",
          search: "Buscar:",
          zeroRecords: "Não foram encontrados resultados",
          paginate: {
            first: "Primeiro",
            last: "Último",
            next: "Seguinte",
            previous: "Anterior"
          },
          aria: {
            sortAscending: ": clique para ordenar ascendente (ASC)",
            sortDescending: ": clique para ordenar descendente (DESC)"
          }
        }
      });

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
          lista_corte();

        };


      mostrarModal(); // Exibe o modal
    },
    error: function (xhr, status, error) {
      console.error("Erro na requisição AJAX:", error);
    },
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
            lista_corte();
          }
        });

      }
    });
  }
  function cancelar_corte(id) {
    if (mostrarConfirmacao('Cancelar corte?')) {
      $.ajax({
        url: '<?= base_url('public/cancelar_corte') ?>',
        type: "POST",
        dataType: "json", // Indicar que o retorno é em formato JSON
        data: { id: id },
        success: function (response) {
          lista_corte();
         

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