<script>

  lista_temp = "";
  <?php $processo = "prioridade"; ?>
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
      url: '<?= base_url('public/prioridade') ?>',
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


  var inputElement = document.getElementById("nova_ordem");
  inputElement.disabled = true;

  function ordem_max() {
    $.ajax({
      url: '<?= base_url('public/ordem_max') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON

      success: function (response) {
        var inputElement = document.getElementById("nova_ordem");
        inputElement.disabled = true;
        inputElement.value = parseInt(response.max) + 1;





      }
    });
  }
  // Executar função ao abrir o site
  document.addEventListener('DOMContentLoaded', ordem_max);

  function cadastrar() {

    var prioridade = document.getElementById("nome_prioridade_nova").value;
    var cor = document.getElementById("nova_cor").value;

    $.ajax({
      url: '<?= base_url('public/prioridade_cadastrar') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { prioridade: prioridade, cor: cor },
      success: function (response) {

        if (!response.ok) {
          //response.msg

          for (const chave in response.msg) {
            const valor = response.msg[chave];
            alert_personalizado(chave, valor);
          }
        } else {
          lista();
          alert_certo('Cadastrado', 'Prioridade cadastrado com sucesso.');
          document.getElementById("nome_prioridade_nova").value = '';
          document.getElementById("nova_cor").value = '';
        }

      }
    });
    ordem_max();
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

  document.getElementById("nome_prioridade_nova").addEventListener("input", function () {
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
      url: '<?= base_url('public/prioridade_modal') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { id: id },
      success: function (response) {


        var botao_confirmar_modal = document.getElementById('botao_confirmar_modal');
        botao_confirmar_modal.innerHTML = "Confirmar";
        var modal_titulo = document.getElementById('modal_titulo');
        var modal_bory = document.getElementById('modal_bory');
        modal_titulo.textContent = "Modificar a prioridade: " + response.nome;
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
        inputElement = document.createElement("input");
        inputElement.type = 'text';
        inputElement.id = 'prioridade_nova';
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
        labelElement.textContent = "Prioridade";
        divElemnt.innerHTML = '';
        divElemnt.appendChild(labelElement);
        divElemnt.appendChild(inputElement);
        modal_bory.appendChild(divElemnt);//coloca o input name no modal



        divElemnt = document.createElement("div");
        divElemnt.classList.add("form-group");
        // Definir o valor e texto do novo elemento option
        novoOption.value = response.ordem;
        novoOption.textContent = response.ordem;
        selectElement.id = 'ordem_nova';
        selectElement.classList.add("custom-select");
        selectElement.appendChild(novoOption);
        labelElement = document.createElement("label");
        labelElement.textContent = "Ordem";
        divElemnt.innerHTML = '';
        divElemnt.appendChild(labelElement);
        divElemnt.appendChild(selectElement);
        modal_bory.appendChild(divElemnt);//coloca o select name no modal


        divElemnt = document.createElement("div");
        divElemnt.classList.add("form-group");
        inputElement = document.createElement("input");
        inputElement.type = 'color';
        inputElement.id = 'cor_nova';
        inputElement.classList.add("form-control");
        inputElement.value = response.cor;
        labelElement = document.createElement("label");
        labelElement.textContent = "Cor";
        divElemnt.innerHTML = '';
        divElemnt.appendChild(labelElement);
        divElemnt.appendChild(inputElement);
        modal_bory.appendChild(divElemnt);//coloca o input name no modal


        mostrarModal();
        value_ordem(true);

      }
    });


  }
  function confirmarModal() {

    var prioridade = document.getElementById("prioridade_nova").value;
    var ordem = document.getElementById("ordem_nova").value;
    var cor = document.getElementById("cor_nova").value;

    $.ajax({
      url: '<?= base_url('public/prioridade_update') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { id: id_g, cor: cor, ordem: ordem, prioridade: prioridade },
      success: function (response) {
        console.log(response);
        if (!response.ok) {
          const mensagens = response && response.msg ? response.msg : null;
          let exibiuMensagem = false;
          if (mensagens && typeof mensagens === 'object') {
            for (const chave in mensagens) {
              if (!Object.prototype.hasOwnProperty.call(mensagens, chave)) {
                continue;
              }
              const valor = mensagens[chave];
              alert_personalizado(chave, valor);
              exibiuMensagem = true;
            }
          } else if (typeof mensagens === 'string' && mensagens.trim() !== '') {
            alert_personalizado('Alteracao', mensagens);
            exibiuMensagem = true;
          }
          if (!exibiuMensagem) {
            alert_personalizado('Alteracao', 'Nenhum item foi modificado.');
          }
          return;
        } else {
          alert_certo('Alteração', 'Prioridade Modificado com sucesso.');
          lista();
        }

      }
    });

    fecharModal();
  }

  lista_temp1 = "";
  function value_ordem(efeturar = false) {
    $.ajax({
      url: '<?= base_url('public/lista_ordem') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      success: function (response) {
        if (response != lista_temp1 || efeturar) {
          // Obter referência ao elemento select
          var funcao = document.getElementById("ordem_nova");
          // Armazenar o valor da opção selecionada antes de limpar o select
          var valorSelecionadoAntes = funcao.value;

          // Limpar o select
          funcao.innerHTML = '';

          // Criar um novo elemento option
          var novoOption = document.createElement("option");

          // Definir o valor e texto do novo elemento option
          novoOption.value = '';
          novoOption.textContent = 'Ordem';

          // Adicionar o novo elemento option ao select
          funcao.appendChild(novoOption);

          response.lista.forEach(element => {



            // Criar um novo elemento option
            var novoOption = document.createElement("option");

            // Definir o valor e texto do novo elemento option
            novoOption.value = element;
            novoOption.textContent = element;

            // Adicionar o novo elemento option ao select
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
  document.addEventListener('DOMContentLoaded', value_empresa);

  //   // Repetir função a cada segundo
  //   setInterval(lista, 1000);
  // // Repetir função a cada segundo
  // setInterval(ordem_max, 1000);
  // // Repetir função a cada segundo 
  // setInterval(value_empresa, 1000);
</script>
