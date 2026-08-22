<script>

  lista_temp = "";

  <?php $processo = "empreendimentos"; ?>
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
      url: '<?= base_url('public/empreendimento') ?>',
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



  lista_temp1 = "";
  function value_empresa(efeturar = false, fieldId = "empresa_cliente_novo") {
    $.ajax({
      url: '<?= base_url('public/lista_empresa') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      success: function (response) {
        if (response != lista_temp1 || efeturar) {
          // Obter referência ao elemento select
          var funcao = document.getElementById(fieldId);
          if (!funcao) {
            return;
          }
          // Armazenar o valor da opção selecionada antes de limpar o select
          var valorSelecionadoAntes = funcao.value;

          // Limpar o select
          funcao.innerHTML = '';

          // Criar um novo elemento option
          var novoOption = document.createElement("option");

          // Definir o valor e texto do novo elemento option
          novoOption.value = '';
          novoOption.textContent = 'Empresa/Cliente';

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
  document.addEventListener('DOMContentLoaded', function () {
    value_empresa(false, "empresa_cliente_novo");
  });

  const ESCALAS_PREDEFINIDAS = ['1:32', '1:43', '1:64', '1:75', '1:87', '1:100', '1:150', '1:200', '1:300'];
  const ESCALA_DATALIST_ID = 'escala_empreendimento_sugestoes';

  function garantirSugestoesEscala() {
    var datalist = document.getElementById(ESCALA_DATALIST_ID);
    if (datalist) {
      return datalist;
    }

    datalist = document.createElement('datalist');
    datalist.id = ESCALA_DATALIST_ID;

    ESCALAS_PREDEFINIDAS.forEach(function (escala) {
      var option = document.createElement('option');
      option.value = escala;
      datalist.appendChild(option);
    });

    document.body.appendChild(datalist);
    return datalist;
  }

  function normalizarEscalaInput(valor) {
    valor = String(valor || '').trim();
    if (valor === '') {
      return '';
    }

    return valor.replace(/\s+/g, '');
  }

  function prepararCampoEscala(input, obrigatorio = false) {
    if (!input) {
      return;
    }

    garantirSugestoesEscala();
    input.setAttribute('list', ESCALA_DATALIST_ID);
    input.setAttribute('maxlength', '15');
    input.setAttribute('placeholder', 'Ex.: 1:100');
    input.setAttribute('autocomplete', 'off');
    input.dataset.escalaObrigatoria = obrigatorio ? 'true' : 'false';

    if (input.dataset.escalaPreparada === 'true') {
      return;
    }

    input.addEventListener('blur', function () {
      input.value = normalizarEscalaInput(input.value);
    });

    input.dataset.escalaPreparada = 'true';
  }

  function obterEscalaValidada(inputId, obrigatorio = false) {
    var input = document.getElementById(inputId);
    if (!input) {
      return obrigatorio ? null : '';
    }

    var escala = normalizarEscalaInput(input.value);
    input.value = escala;

    if (escala === '') {
      if (obrigatorio) {
        alert_personalizado('Escala', 'Selecione ou informe uma escala.');
        return null;
      }

      return '';
    }

    if (!/^\d{1,5}:\d{1,5}$/.test(escala)) {
      alert_personalizado('Escala', 'Informe a escala no formato 1:100.');
      return null;
    }

    return escala;
  }

  document.addEventListener('DOMContentLoaded', function () {
    prepararCampoEscala(document.getElementById('escala_nova'), true);
  });







  document.getElementById("empreendimento_novo").addEventListener("input", function () {
    var input = this;
    var maxLength = 17;
    var valor = input.value;
    input.value = valor.slice(0, maxLength); // Trunca o valor para o tamanho máximo 

  });

  function cadastrar() {
    var empreendimento = document.getElementById("empreendimento_novo").value;
    var escala = obterEscalaValidada("escala_nova", true);
    var empresa = document.getElementById("empresa_cliente_novo").value;
    if (escala === null) {
      return;
    }

    $.ajax({
      url: '<?= base_url('public/empreendimento_cadastrar') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { empreendimento: empreendimento, escala: escala, empresa: empresa },
      success: function (response) {

        if (!response.ok) {
          //response.msg

          for (const chave in response.msg) {
            const valor = response.msg[chave];
            alert_personalizado(chave, valor);
          }
        } else {
          lista();
          alert_certo('Cadastrado', 'Empreendimento cadastrado com sucesso.');
          document.getElementById("empreendimento_novo").value = '';
          document.getElementById("escala_nova").value = '';
          document.getElementById("empresa_cliente_novo").value = '';
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




  id_g = '';
  function modal_modificar(id) {
    id = id.replace('modal_', '');
    id_g = id;
    $.ajax({
      url: '<?= base_url('public/empreendimento_modal') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { id: id },
      success: function (response) {

        if (!response.desenho) {
          var botao_confirmar_modal = document.getElementById('botao_confirmar_modal');
          botao_confirmar_modal.innerHTML = "Confirmar";
          var modal_titulo = document.getElementById('modal_titulo');
          var modal_bory = document.getElementById('modal_bory');
          modal_titulo.textContent = "Modificar o empreendimento: " + response.nome;
          const selectElement = document.createElement("select");
          var inputElement = document.createElement("input");
          var labelElement = document.createElement("label");

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
          inputElement.id = 'nome_novo';
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
          labelElement.textContent = "Empreendimento";
          divElemnt.innerHTML = '';
          divElemnt.appendChild(labelElement);
          divElemnt.appendChild(inputElement);
          modal_bory.appendChild(divElemnt);//coloca o input name no modal

          divElemnt = document.createElement("div");
          divElemnt.classList.add("form-group");
          var escalaInputElement = document.createElement("input");
          escalaInputElement.type = 'text';
          escalaInputElement.id = 'escala_nova_modal';
          escalaInputElement.classList.add("form-control");
          escalaInputElement.value = response.escala || '';
          labelElement = document.createElement("label");
          labelElement.textContent = "Escala";
          divElemnt.innerHTML = '';
          divElemnt.appendChild(labelElement);
          divElemnt.appendChild(escalaInputElement);
          modal_bory.appendChild(divElemnt);
          prepararCampoEscala(escalaInputElement, Boolean(response.escala));

          divElemnt = document.createElement("div");
          divElemnt.classList.add("form-group");
          // Definir o valor e texto do novo elemento option
          novoOption.value = response.empresa_id;
          novoOption.textContent = response.empresa_id;
          selectElement.id = 'empresa_cliente_modal';
          selectElement.classList.add("custom-select");
          selectElement.appendChild(novoOption);
          labelElement = document.createElement("label");
          labelElement.textContent = "Empres/Cliente";
          divElemnt.innerHTML = '';
          divElemnt.appendChild(labelElement);
          divElemnt.appendChild(selectElement);
          modal_bory.appendChild(divElemnt);//coloca o input name no modal

          value_empresa(true, 'empresa_cliente_modal');
          mostrarModal();
        } else {
          alert_personalizado('Modificar', 'Empreendimento já está em uso.');
        }
      }
    });


  }
  function confirmarModal() {

    var empreendimento = document.getElementById("nome_novo").value;
    var empresa = document.getElementById("empresa_cliente_modal").value;
    var escalaInput = document.getElementById("escala_nova_modal");
    var escalaObrigatoria = escalaInput && escalaInput.dataset.escalaObrigatoria === 'true';
    var escala = obterEscalaValidada("escala_nova_modal", escalaObrigatoria);
    if (escala === null) {
      return;
    }

    $.ajax({
      url: '<?= base_url('public/empreendimento_update') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: { id: id_g, empreendimento: empreendimento, escala: escala, empresa: empresa },
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
          alert_certo('Alteração', 'Empreendimento Modificado com sucesso.');
          lista();
        }

      }
    });

    fecharModal();
  }

  //   // Repetir função a cada segundo
  //   setInterval(lista, 1000);
  //     // Repetir função a cada segundo 
  // setInterval(value_empresa, 1000);
</script>
