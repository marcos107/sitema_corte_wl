<script>
  var roda_pe = document.getElementById('roda_pe');
  roda_pe.innerHTML = '<div style="position: relative; width: 100%; height: 50px;"><div style="position: absolute; top: 50%;"><button name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="prio_modal_todos()"> Mudar prioridade de varios </button></div><div style="position: absolute; top: 50%; right: 0;"><button name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="apagar_todos()"> Apagar varios</button></div></div>';

  check_prima = true;

  lista_temp = "";

  let telaAddDesenho = "";
var processo_nome = "";

  function injetarEstilosSelecaoProcesso() {
    if (document.getElementById('wl-process-picker-style-corte-adm')) {
      return;
    }

    var style = document.createElement('style');
    style.id = 'wl-process-picker-style-corte-adm';
    style.textContent = `
      .wl-process-picker { display: grid; gap: 1rem; }
      .wl-process-picker-head h6 { margin: 0 0 .2rem; font-size: 1rem; font-weight: 700; color: #0f172a; }
      .wl-process-picker-head p { margin: 0; color: #64748b; font-size: .9rem; }
      .wl-process-grid { display: grid; gap: .7rem; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }
      .wl-process-option {
        position: relative;
        border: 1px solid #dbe5f1;
        border-radius: .75rem;
        background: #ffffff;
        padding: .8rem;
        cursor: pointer;
        display: grid;
        gap: .35rem;
        transition: all .18s ease;
      }
      .wl-process-option:hover {
        border-color: #93c5fd;
        box-shadow: 0 8px 16px rgba(37, 99, 235, .08);
      }
      .wl-process-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
      }
      .wl-process-option.is-selected {
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, .16);
        background: #f8fbff;
      }
      .wl-process-option-name {
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
      }
      .wl-process-picker-actions {
        display: flex;
        justify-content: flex-end;
      }
      .wl-process-picker-actions .btn {
        min-width: 160px;
      }
    `;

    document.head.appendChild(style);
  }

  function atualizarCardsProcessoSelecionado() {
    var radios = document.querySelectorAll('#processos_radio input[name="processo"]');
    radios.forEach(function (radio) {
      var card = radio.closest('.wl-process-option');
      if (!card) {
        return;
      }

      if (radio.checked) {
        card.classList.add('is-selected');
      } else {
        card.classList.remove('is-selected');
      }
    });
  }

  function atualizarInterfaceParaSelecaoDeProcesso(ok = true) {
    const topLista = document.getElementById("top-lista");

    if (!topLista) {
      console.error("Elemento com ID 'top-lista' não encontrado.");
      return;
    }

    injetarEstilosSelecaoProcesso();

    if (ok) {
      telaAddDesenho = topLista.outerHTML;
    }

    topLista.outerHTML = `
      <div id="top-lista" class="card-body wl-process-picker">
        <div class="wl-process-picker-head">
          <h6>Escolha o processo</h6>
          <p>Selecione o processo para carregar a lista de tarefas ADM.</p>
        </div>
        <div id="processos_radio" class="wl-process-grid"></div>
        <div class="wl-process-picker-actions">
          <button name="cadastarar" type="button" onclick="proximo()" class="btn btn-primary btn-lg">Continuar</button>
        </div>
      </div>
    `;

    const tituloCard = document.querySelector('.card-title');
    if (tituloCard) {
      tituloCard.textContent = "Escolha o processo";
    }

    processo_lista();
  }

  function get_radio() {
    var radios = document.getElementsByName('processo');
    for (var i = 0; i < radios.length; i++) {
      if (radios[i].checked) {
        return radios[i];
      }
    }
    return null;
  }

  function proximo() {
    var rad_temp = get_radio();
    if (!rad_temp) {
      alert_personalizado("Processo", "Selecione um processo para continuar.");
      return;
    }

    processo_nome = rad_temp.value;
    const containerPai = document.getElementById("top-lista")?.parentElement;

    if (!containerPai || !telaAddDesenho) {
      console.error("Não foi possível restaurar o conteúdo original.");
      return;
    }

    const temp = document.createElement("div");
    temp.innerHTML = telaAddDesenho;

    const elementoOriginal = temp.firstElementChild;
    if (elementoOriginal) {
      containerPai.replaceChild(elementoOriginal, document.getElementById("top-lista"));
    }

    const tituloCard = document.querySelector('.card-title');
    if (tituloCard) {
      tituloCard.innerHTML = "<button type=\"button\" onclick=\"atualizarInterfaceParaSelecaoDeProcesso(false)\" class=\"btn btn-outline-primary\">Voltar</button>&nbsp;&nbsp;Lista de tarefas ADM \"" + processo_nome + "\"";
    }

    lista_corte(processo_nome, true);
  }

  atualizarInterfaceParaSelecaoDeProcesso();

  function alert_certo(titulo, bory) { //cria um alerte verde no canto superior direto
    $(document).Toasts('create', {
      class: 'bg-success',
      title: titulo,
      subtitle: 'Subtitle',
      autohide: true,
      delay: 5000,
      body: bory
    });
  }

  function alert_personalizado(titulo, bory) { //cria um alerte vermelho no canto superior direto 
    $(document).Toasts('create', {
      class: 'bg-danger',
      title: titulo,
      subtitle: 'Subtitle',
      autohide: true,
      delay: 13000,
      body: bory,
      encodeHTML: false
    });
  }


  processos_select = "";

  function processo_lista() {
    $.ajax({
      url: '<?= base_url('public/processos_lista') ?>',
      type: "POST",
      dataType: "json",
      data: { contexto_tela: 'lista_corte_adm' },
      async: false,
      success: function(response) {
        processos_select = response.lista;
        var radioContainer = document.getElementById('processos_radio');
        if (!radioContainer) {
          return;
        }

        radioContainer.innerHTML = '';
        var cont = 0;

        processos_select.forEach(function(processo, index) {
          var temp = 'processo_' + (processo.input || 'proc') + '_' + index;

          var radioElement = document.createElement('input');
          radioElement.type = 'radio';
          radioElement.name = 'processo';
          radioElement.id = temp;
          radioElement.value = processo.nome;
          radioElement.addEventListener('change', atualizarCardsProcessoSelecionado);

          var labelElement = document.createElement('label');
          labelElement.className = 'wl-process-option';
          labelElement.htmlFor = temp;

          var nomeElement = document.createElement('span');
          nomeElement.className = 'wl-process-option-name';
          nomeElement.textContent = processo.nome;

          labelElement.appendChild(radioElement);
          labelElement.appendChild(nomeElement);
          radioContainer.appendChild(labelElement);

          if (cont === 0) {
            radioElement.checked = true;
            cont++;
          }
        });

        atualizarCardsProcessoSelecionado();
      }
    });
  }


  function resetarDataTableExample1() {
    if (!window.jQuery || !$.fn || !$.fn.DataTable) {
      return;
    }

    if ($.fn.DataTable.isDataTable('#example1')) {
      $('#example1').DataTable().destroy();
    }
  }

  function atualizarVisibilidadeLista(temDesenhos) {
    var tabela = document.getElementById('example1');
    var tabelaContainer = tabela ? tabela.closest('.table-responsive') : null;
    var rodaPe = document.getElementById('roda_pe');
    var aviso = document.getElementById('wl-lista-vazia-aviso-adm');

    if (temDesenhos) {
      if (tabelaContainer) {
        tabelaContainer.style.display = '';
      }
      if (rodaPe) {
        rodaPe.style.display = '';
      }
      if (aviso) {
        aviso.remove();
      }
      return;
    }

    if (tabelaContainer) {
      tabelaContainer.style.display = 'none';
    }
    if (rodaPe) {
      rodaPe.style.display = 'none';
    }

    if (!aviso && tabelaContainer && tabelaContainer.parentElement) {
      aviso = document.createElement('div');
      aviso.id = 'wl-lista-vazia-aviso-adm';
      aviso.className = 'text-muted py-2';
      aviso.textContent = 'Sem desenhos para exibir.';
      tabelaContainer.parentElement.insertBefore(aviso, rodaPe || null);
    }
  }

  function lista_corte(processo_nome, ok = false) {
    checkbox = document.getElementById("cortadorCheckbox");
    var checkboxValue = checkbox ? (checkbox.checked ? "true" : "false") : "";

    $.ajax({
      url: '<?= base_url('public/lista_corte_adm') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: {
        check: checkboxValue,
        nome_processos: processo_nome
      },
      success: function(response) {

        if (response.lista != lista_temp || ok) {
          resetarDataTableExample1();





          // Recriar e configurar a tabela DataTable

          // Selecione o elemento <tbody> pelo seu ID
          var lista = document.getElementById('lista');
          if (!lista) {
            console.error("Elemento #lista não encontrado para renderizar as tarefas.");
            return;
          }
          // Substitua o conteúdo do elemento <tbody> com o novo HTML
          lista.innerHTML = response.lista;
          var possuiDesenhos = !!lista.querySelector('tr');
          atualizarVisibilidadeLista(possuiDesenhos);
          if (!possuiDesenhos) {
            lista_temp = response.lista;
            return;
          }
          aplicarCorPrioridadeNaTabelaPrincipal();
          if (!document.getElementById('example1')) {
            console.error("Tabela #example1 não encontrada para inicializar DataTable.");
            return;
          }
          $(function() {
            $("#example1").DataTable({
              "responsive": true,
              "lengthChange": false,
              "autoWidth": false,
              "drawCallback": function() {
                aplicarCorPrioridadeNaTabelaPrincipal();
              },
              "order": [

                [0, "asc"],
                [1, "asc"]
              ],

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
              '<input type="checkbox" id="cortadorCheckbox" onclick="lista_corte(processo_nome,true)">Som ao adicionar desenhos'
            );
          });



          lista_temp = response.lista;
        }
        if (response.check != "") {
          setTimeout(function() {
            checkbox = document.getElementById("cortadorCheckbox");
            if (checkbox) {
              check_prima = false;
              checkbox.checked = (response.check == "true");
            }
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
  // document.addEventListener('DOMContentLoaded', lista_corte);





  lista_temp1 = "";

  function value_prioridade(efeturar = false) {
    $.ajax({
      url: '<?= base_url('public/prioridade_lista') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      success: function(response) {
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

  var lista = [];
  var selectedIds = [];

  // Função chamada ao clicar no checkbox
  function array_ids(checkbox) {
    var id = checkbox.id;
    if (checkbox.checked) {
      // Se estiver marcado, adiciona o id ao array (se ainda não estiver)
      if (selectedIds.indexOf(id) === -1) {
        selectedIds.push(id);
      }
    } else {
      // Se desmarcado, remove o id do array
      var index = selectedIds.indexOf(id);
      if (index !== -1) {
        selectedIds.splice(index, 1);
      }
    }
    console.log("IDs selecionados:", selectedIds);
  }

  function definirTamanhoModal(classes = ['modal-xl']) {
    const modalSizer = document.getElementById('modal_sizer');
    if (!modalSizer) {
      return;
    }

    modalSizer.classList.remove('modal-sm', 'modal-lg', 'modal-xl', 'modal-xxl');
    classes.forEach(function(classe) {
      modalSizer.classList.add(classe);
    });
  }

  function normalizarCorHex(cor) {
    if (!cor) {
      return null;
    }

    var corTratada = String(cor).trim();
    if (/^#[0-9A-F]{6}$/i.test(corTratada)) {
      return corTratada;
    }

    return null;
  }

  function obterCorPrioridade(item) {
    return normalizarCorHex(item && item.prioridade_cor) ||
      normalizarCorHex(item && item.cor) ||
      '#cbd5e1';
  }

  function obterCorTextoParaFundo(corHex) {
    var corValida = normalizarCorHex(corHex);
    if (!corValida) {
      return '#0f172a';
    }

    var r = parseInt(corValida.substring(1, 3), 16);
    var g = parseInt(corValida.substring(3, 5), 16);
    var b = parseInt(corValida.substring(5, 7), 16);
    var luminancia = (0.299 * r) + (0.587 * g) + (0.114 * b);
    return luminancia > 165 ? '#0f172a' : '#f8fafc';
  }

  function respostaLoteEhProjeto(response) {
    return response && response.tipo === 'ind';
  }

  function itemLoteEhProjeto(item, response) {
    return respostaLoteEhProjeto(response) ||
      (item && (item.item_tipo === 'projeto' || item.projeto_descricao));
  }

  function rotuloNomeLote(response) {
    return respostaLoteEhProjeto(response) ? 'Projeto/Descricao' : 'Nome do arquivo';
  }

  function tituloItemLote(response, plural) {
    if (respostaLoteEhProjeto(response)) {
      return plural ? 'projetos' : 'projeto';
    }

    return plural ? 'desenhos' : 'desenho';
  }

  function nomeItemLote(item, response) {
    if (!item) {
      return '';
    }

    if (itemLoteEhProjeto(item, response)) {
      return item.projeto_descricao || item.descricao || item.nome || '';
    }

    return removeIdFromFile(item.nome || '');
  }

  function escapeHtmlLote(valor) {
    return String(valor ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function anexarTabelaNoModal(modalBory, tabela) {
    if (!modalBory || !tabela) {
      return;
    }

    var wrapper = document.createElement('div');
    wrapper.className = 'wl-modal-table-wrap';
    wrapper.appendChild(tabela);
    modalBory.appendChild(wrapper);
  }

  function prepararBotaoConfirmarModal(texto, onclickFn, idBotao = 'botao_confirmar_modal') {
    var botao = document.getElementById('botao_confirmar_modal') || document.getElementById('botao_confirmar_modal_apagar') || document.getElementById('botao_confirmar_modal1');
    if (!botao) {
      return null;
    }

    botao.id = idBotao;
    botao.disabled = false;
    botao.textContent = texto || '';
    if (onclickFn) {
      botao.setAttribute('onclick', onclickFn);
    }

    return botao;
  }

  function prepararRodapeModalApagar() {
    var modalRodape = document.getElementById('modal_rodape');
    if (!modalRodape) {
      return null;
    }

    var antigoContainer = document.getElementById('modal_apagar_container');
    if (antigoContainer) {
      antigoContainer.remove();
    }

    var container = document.createElement('div');
    container.id = 'modal_apagar_container';

    var check = document.createElement('input');
    check.id = 'modal_apagar_checkbox';
    check.type = 'checkbox';
    check.className = 'form-control';
    check.onclick = confirmar_botao_apagar;

    var label = document.createElement('label');
    label.setAttribute('for', 'modal_apagar_checkbox');
    label.textContent = 'Apagar';
    label.className = 'mb-0';

    container.appendChild(check);
    container.appendChild(label);
    modalRodape.prepend(container);

    return check;
  }

  function aplicarCorPrioridadeNaCelula(celula, item) {
    if (!celula) {
      return;
    }

    var corFundo = obterCorPrioridade(item);
    var corTexto = obterCorTextoParaFundo(corFundo);
    celula.classList.add('wl-prioridade-cell');
    celula.style.setProperty('background-color', corFundo, 'important');
    celula.style.setProperty('color', corTexto, 'important');
  }

  function aplicarCorPrioridadeNaTabelaPrincipal() {
    var tabela = document.getElementById('example1');
    if (!tabela) {
      return;
    }

    var celulasPrioridade = tabela.querySelectorAll('tbody td[bgcolor]');
    celulasPrioridade.forEach(function(celula) {
      var cor = celula.getAttribute('bgcolor');
      var corValida = normalizarCorHex(cor);
      if (!corValida) {
        return;
      }

      var corTexto = obterCorTextoParaFundo(corValida);
      celula.classList.add('wl-prioridade-cell');
      celula.style.setProperty('background-color', corValida, 'important');
      celula.style.setProperty('color', corTexto, 'important');

      var textos = celula.querySelectorAll('span, .marca_texto');
      textos.forEach(function(texto) {
        texto.style.setProperty('color', corTexto, 'important');
      });
    });
  }



  function prio_modal_todos() {
    selectedIds = [];

    $.ajax({
      url: '<?= base_url('public/desenho_modal') ?>',
      type: "POST",
      dataType: "json",
      data: {
        id: ""
      },
      success: function(response) {
        const modalBory = document.getElementById('modal_bory');
        const modalTitulo = document.getElementById('modal_titulo');
        const botaoConfirmarModal = prepararBotaoConfirmarModal("Confirmar", "confirmarModal()");

        if (!modalBory || !modalTitulo || !botaoConfirmarModal) {
          console.error("Elementos do modal nao encontrados.");
          return;
        }

        definirTamanhoModal(['modal-xl']);
        modalBory.innerHTML = '';
        modalTitulo.textContent = "Modificar prioridade " + tituloItemLote(response, false);

        // Select de prioridade
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

        // Select de ordem
        const divOrdem = document.createElement("div");
        divOrdem.classList.add("form-group");

        const labelOrdem = document.createElement("label");
        labelOrdem.textContent = "Ordem";
        divOrdem.appendChild(labelOrdem);

        const selectOrdem = document.createElement("select");
        selectOrdem.classList.add("custom-select");
        selectOrdem.id = "ordem_novo";

        divOrdem.appendChild(selectOrdem);
        modalBory.appendChild(divOrdem);

        function populateOrderSelect(priority) {
          selectOrdem.innerHTML = "";
          const maxOrder = response.agrupados[priority] || 1;
          lista = response.lista;

          for (let i = 1; i <= (maxOrder+1); i++) {
            const opt = document.createElement("option");
            opt.value = i;
            opt.textContent = "Ordem " + i;

            if (response.lista[0] && i == response.lista[0].ordem) {
              opt.selected = true;
            }

            selectOrdem.appendChild(opt);
          }
        }

        populateOrderSelect(response.lista[0]?.prioridade || response.empresa_id);

        selectPrioridade.addEventListener("change", function() {
          populateOrderSelect(this.value);
        });

        const tabela = document.createElement("table");
        tabela.classList.add("table", "table-bordered", "table-striped");
        tabela.id = "tabelaPrioridade";

        const thead = document.createElement("thead");
        thead.innerHTML = `
        <tr>
          <th class="quebrar" style="max-width: 6%;">Prioridade</th>
          <th class="quebrar" style="max-width: 12%;">Ordem</th>
          <th class="quebrar" style="max-width: 6%;">Desenhista</th>
          <th class="quebrar" style="max-width: 8%;">${rotuloNomeLote(response)}</th>
          <th class="quebrar" style="max-width: 6%;">Empresa/Cliente</th>
          <th class="quebrar" style="max-width: 12%;">Empreendimento</th>
          <th class="quebrar" style="max-width: 12%;">Finalidade</th>
          <th class="quebrar" style="max-width: 8%;">Subpastas</th>
          <th class="quebrar" style="max-width: 12%;">Data de Envio</th>
          <th class="quebrar" style="max-width: 8%;">Selecionar</th>
        </tr>`;
        tabela.appendChild(thead);

        const tbody = document.createElement("tbody");

        Object.values(response.lista)
          .filter(item => typeof item === 'object' && item !== null)
          .forEach((item) => {
            if (item.status === 'pendente') {
              const isNovoModelo = !!item.finalidade_nome;
              const corPrioridade = obterCorPrioridade(item);
              const corTextoPrioridade = obterCorTextoParaFundo(corPrioridade);

              const tr = document.createElement("tr");
              tr.innerHTML = `
              <td class="quebrar wl-prioridade-cell" style="background-color: ${corPrioridade} !important; color: ${corTextoPrioridade} !important; max-width: 6%;">${isNovoModelo ? item.prioridade_nome : item.prioridade}</td>
              <td class="quebrar" style="max-width: 12%;">${item.ordem}</td>
              <td class="quebrar" style="max-width: 6%;">${isNovoModelo ? item.usuario_nome : item.desenhista_nome || ''}</td>
              <td class="quebrar" style="max-width: 8%;">${escapeHtmlLote(nomeItemLote(item, response))}</td>
              <td class="quebrar" style="max-width: 6%;">${isNovoModelo ? item.empresa_nome : item.empresa || ''}</td>
              <td class="quebrar" style="max-width: 12%;">${isNovoModelo ? item.empreendimento_nome : item.empreendimento || ''}</td>
              <td class="quebrar" style="max-width: 12%;">${isNovoModelo ? item.finalidade_nome : item.finalidade || ''}</td>
              <td class="quebrar" style="max-width: 8%;">${item.tags || ''}</td>
              <td class="quebrar" style="max-width: 12%;">${item.data_hora_add || item.data_add || ''}</td>
              <td style="max-width: 8%;">
                <input type="checkbox" id="prio_${item.id}" class="form-control"
                  onclick="array_ids(this)"
                  onmouseover="if(event.buttons === 1){ this.checked = !this.checked; array_ids(this); }">
              </td>`;
              tbody.appendChild(tr);
            }
          });

        tabela.appendChild(tbody);
        anexarTabelaNoModal(modalBory, tabela);

        $("#tabelaPrioridade").DataTable({
          responsive: true,
          lengthChange: false,
          autoWidth: false,
          pageLength: 10,
          order: [
            [0, "asc"],
            [1, "asc"]
          ],
          language: {
            decimal: "",
            emptyTable: "Sem dados disponíveis",
            infoEmpty: "Mostrando de 0 até 0 de 0 registros",
            infoFiltered: "(filtrado de _MAX_ registros no total)",
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
        mostrarModal();
      },
      error: function(xhr, status, error) {
        console.error("Erro na requisição AJAX:", error);
      }
    });
  }




  function prio_modal(id) {

    $.ajax({
      url: '<?= base_url('public/desenho_modal') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: {
        id: id
      },
      success: function(response) {

        // Dados do desenho (array de objetos) e os agrupados (ex: { "1": 5, "2": 3, ... })
        lista = response.lista;
        var agrupados = response.agrupados;

        var botao_confirmar_modal = prepararBotaoConfirmarModal("Confirmar", "confirmarModal()");
        definirTamanhoModal(['modal-xl']);
        if (!botao_confirmar_modal) {
          return;
        }

        var modal_titulo = document.getElementById('modal_titulo');
        var modal_bory = document.getElementById('modal_bory');
        modal_titulo.textContent = "Modificar prioridade " + tituloItemLote(response, false) + ": " + nomeItemLote(lista[0], response);

        // Limpa o conteúdo do modal
        modal_bory.innerHTML = '';


        var selectPrioridade = document.createElement("select");
        selectPrioridade.id = 'prioridade_novo';
        selectPrioridade.classList.add("custom-select");
        selectPrioridade.innerHTML = '';

        // Preenche o select com as prioridades (as chaves do agrupados)
        for (var prioridade in agrupados) {
          if (agrupados.hasOwnProperty(prioridade)) {
            var option = document.createElement("option");
            option.value = prioridade;
            option.textContent = prioridade;
            // Se for a prioridade atual do desenho, marca como selecionado
            if (prioridade == lista[0].prioridade) {
              option.selected = true;
            }
            selectPrioridade.appendChild(option);
          }
        }

        var divPrioridade = document.createElement("div");
        divPrioridade.classList.add("form-group");
        var labelPrioridade = document.createElement("label");
        labelPrioridade.textContent = "Prioridade";
        divPrioridade.appendChild(labelPrioridade);
        divPrioridade.appendChild(selectPrioridade);
        modal_bory.appendChild(divPrioridade);


        var selectOrdem = document.createElement("select");
        selectOrdem.id = 'ordem_novo';
        selectOrdem.classList.add("custom-select");

        // Função para popular o select de ordem de acordo com a prioridade selecionada
        function populateOrderSelect(priority) {
          selectOrdem.innerHTML = ''; // Limpa as opções atuais

          // Pega o número máximo de ordem para a prioridade selecionada
          var maxOrder = agrupados[priority];
          // Caso a prioridade não exista em 'agrupados', define maxOrder como 1
          if (!maxOrder) {
            maxOrder = 1;
          }

          // As opções vão de 1 até maxOrder
          var startOrder = 1;
          for (var i = startOrder; i <= (maxOrder+1); i++) {
            var opt = document.createElement("option");
            opt.value = i;
            opt.textContent = "Ordem " + i;
            // Se for a ordem atual do desenho, seleciona esta opção
            if (i == lista[0].ordem) {
              opt.selected = true;
            }
            selectOrdem.appendChild(opt);
          }
        }

        // Popula inicialmente o select de ordem com base na prioridade atual
        populateOrderSelect(lista[0].prioridade);

        var divOrdem = document.createElement("div");
        divOrdem.classList.add("form-group");
        var labelOrdem = document.createElement("label");
        labelOrdem.textContent = "Ordem";
        divOrdem.appendChild(labelOrdem);
        divOrdem.appendChild(selectOrdem);
        modal_bory.appendChild(divOrdem);

        // Atualiza o select de ordem sempre que a prioridade for alterada
        selectPrioridade.addEventListener("change", function() {
          populateOrderSelect(this.value);
        });

        value_prioridade();
        mostrarModal();
      }
    });



  }


  function confirmarModal(id = null) {




    if (document.getElementById('input_arquivos')) {

      var fileInput = document.getElementById('input_arquivos');
      var files = fileInput.files;

      if (files.length > 0) {


        if (files.length > 0) {
          $.ajax({
            url: '<?= base_url('public/preparar_adicionar_projeto') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: {
              id: id
            },
            success: function(response) {
              var formData = new FormData();

              for (var i = 0; i < files.length; i++) {
                var file = fileInput.files[i];

                if (file) {
                  var formData = new FormData();
                  formData.append('file', file);

                  $.ajax({
                    url: '<?= site_url('public/desenho_adicionar_projeto') ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    processData: false,
                    async: false, // Torna a solicitação síncrona
                    contentType: false,

                    success: function(response) {
                      if (response.ok == 'true') {
                        fecharModal();
                      } else {
                        console.error("Erro ao atualizar:", response);
                      }

                    },
                    error: function() {
                      alert_personalizado("Desenho", 'Erro ao enviar o arquivo.');


                    }
                  });
                }

              }
            }
          });
        }
      } else {
        alert_personalizado('Desenho', 'Selecione um arquivo antes de adicioná lo.');
      }

    } else {

      // Garantir que lista (do modal) e selectedIds existam
      selectedIds = Array.isArray(selectedIds) ? selectedIds : [];
      var idsToConfirm = [];

      // Caso tenha checkboxes marcados (multiâ€seleção)
      if (selectedIds.length > 0) {
        idsToConfirm = selectedIds.map(raw =>
          String(raw).replace(/^prio_/, '')
        );
      } else if (Array.isArray(lista) && lista.length === 1) {
        // Caso seja modal de só um desenho
        idsToConfirm = [lista[0].id];
      } else if (Array.isArray(lista) && lista.length > 1) {
        // Caso multiâ€modal mas sem selectedIds preenchido, varre checkboxes
        lista.forEach(item => {
          var cb = document.getElementById("prio_" + item.id);
          if (cb && cb.checked) {
            idsToConfirm.push(item.id);
          }
        });
      }

      // Se não veio nenhum ID, abortar
      if (idsToConfirm.length === 0) {
        return;
      }

      // Campos do modal
      var prioridade = document.getElementById("prioridade_novo").value;
      var ordem = document.getElementById("ordem_novo").value;

      $.ajax({
        url: '<?= base_url('public/desenho_update') ?>',
        type: "POST",
        dataType: "json",
        data: {
          array: idsToConfirm,
          prioridade: prioridade,
          ordem: ordem
        },
        success: function(response) {
          if (response.ok) {
            fecharModal();
          } else {
            console.error("Erro ao atualizar:", response);
          }
          lista_corte(processo_nome, true); // atualizar a lista
        },
        error: function(xhr, status, err) {
          console.error("XHR Error:", err);
        }
      });
    }
  }





  function confirmar_botao_apagar() {
    var check_box = document.getElementById('modal_apagar_checkbox');
    var botao_confirmar_modal = document.getElementById('botao_confirmar_modal_apagar');
    if (!botao_confirmar_modal) {
      botao_confirmar_modal = document.getElementById('botao_confirmar_modal');
    }

    if (!check_box || !botao_confirmar_modal) {
      return;
    }

    if (check_box.checked) {
      botao_confirmar_modal.disabled = false;
    } else {
      botao_confirmar_modal.disabled = true;
    }
  }

  function apagar_todos() {
    selectedIds = [];
    prepararRodapeModalApagar();
    const botaoConfirmarModal = prepararBotaoConfirmarModal("Apagar", "", "botao_confirmar_modal_apagar");
    const modalBory = document.getElementById('modal_bory');
    const modalTitulo = document.getElementById('modal_titulo');

    if (!modalBory || !modalTitulo || !botaoConfirmarModal) {
      console.error("Elementos do modal nao encontrados.");
      return;
    }

    // Configurações do modal
    definirTamanhoModal(['modal-xl']);
    botaoConfirmarModal.disabled = true;

    modalBory.innerHTML = ''; // Limpa o conteúdo anterior

    // Criação da tabela
    const tabela = document.createElement("table");
    tabela.classList.add("table", "table-bordered", "table-striped");
    tabela.id = "tabelaApagar";

    const thead = document.createElement("thead");
    tabela.appendChild(thead);

    const tbody = document.createElement("tbody");

    $.ajax({
      url: '<?= base_url('public/desenho_modal') ?>',
      type: "POST",
      dataType: "json",
      data: {
        id: ""
      },
      success: function(response) {
        const lista = response.lista;
        modalTitulo.textContent = "Apagar " + tituloItemLote(response, true);
        thead.innerHTML = `
        <tr>
            <th class="quebrar" style="max-width: 6%;">Prioridade</th>
            <th class="quebrar" style="max-width: 12%;">Ordem</th>
            <th class="quebrar" style="max-width: 6%;">Desenhista</th>
            <th class="quebrar" style="max-width: 8%;">${rotuloNomeLote(response)}</th>
            <th class="quebrar" style="max-width: 6%;">Empresa/Cliente</th>
            <th class="quebrar" style="max-width: 12%;">Empreendimento</th>
            <th class="quebrar" style="max-width: 12%;">Finalidade</th>
            <th class="quebrar" style="max-width: 8%;">Subpastas</th>
            <th class="quebrar" style="max-width: 12%;">Data de Envio</th>
            <th class="quebrar" style="max-width: 8%;">Selecionar</th>
          </tr>
        `;

        Object.values(response.lista)
          .filter(item => item && item.status === 'pendente')
          .forEach(item => {
            const isNovo = !!item.finalidade_nome; // seu “novo modelo”

            // 1) Defino aqui em um array as colunas que quero, em ordem:
            const cols = [{
                // prioridade: no novo modelo uso prioridade_nome+prioridade_cor,   
                val: isNovo ? item.prioridade_nome : item.prioridade,
                style: 'max-width:6%;'
              },
              {
                val: item.ordem,
                style: 'max-width:12%;'
              },
          
              {
                val: isNovo ? item.usuario_nome : (item.desenhista_nome || ''),
                style: 'max-width:6%;'
              },
              {
                val: nomeItemLote(item, response),
                style: 'max-width:8%;'
              },
              {
                val: isNovo ? item.empresa_nome : (item.empresa || ''),
                style: 'max-width:6%;'
              },
              {
                val: isNovo ? item.empreendimento_nome : (item.empreendimento || ''),
                style: 'max-width:12%;'
              },
              {
                val: isNovo ? item.finalidade_nome : (item.finalidade || ''),
                style: 'max-width:12%;'
              },
              {
                val: item.tags || '',
                style: 'max-width:8%;'
              },
              {
                // data pode vir em data_hora_add ou data_add
                val: item.data_hora_add || item.data_add || '',
                style: 'max-width:12%;'
              }
            ];

            // 2) Crio a <tr> e “anexo” cada <td> dinamicamente
            const tr = document.createElement('tr');
            cols.forEach((col, indexCol) => {
              // só renderiza se tiver valor
              if (col.val !== null && col.val !== undefined) {
                const td = document.createElement('td');
                td.classList.add('quebrar');
                td.setAttribute('style', col.style);
                td.textContent = col.val;
                if (indexCol === 0) {
                  aplicarCorPrioridadeNaCelula(td, item);
                }
                tr.appendChild(td);
              }
            });

            // 3) Checkbox final (sempre existe)
            const tdChk = document.createElement('td');
            tdChk.setAttribute('style', 'max-width:8%;');
            const cb = document.createElement('input');
            cb.type = 'checkbox';
            cb.id = `prio_${item.id}`;
            cb.classList.add('form-control');
            cb.onclick = () => array_ids(cb);
            cb.onmouseover = e => {
              if (e.buttons === 1) {
                cb.checked = !cb.checked;
                array_ids(cb);
              }
            };
            tdChk.appendChild(cb);
            tr.appendChild(tdChk);

            // 4) Anexo ao tbody
            tbody.appendChild(tr);
          });

        tabela.appendChild(tbody);
        anexarTabelaNoModal(modalBory, tabela);

        // Inicializa o DataTable
        $('#tabelaApagar').DataTable({
          responsive: true,
          lengthChange: false,
          autoWidth: false,
          pageLength: 10,
          "order": [

            [0, "asc"],
            [1, "asc"]
          ],
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

        botao.onclick = function() {


          for (let i = 0; i < selectedIds.length; i++) {
            temp = String(selectedIds[i]).replace(/^prio_/, '');

            apagar_mesmo(temp);
          }
          // Opcional: Limpar o array após processar os itens
          selectedIds = [];
          fecharModal();
          lista_corte(processo_nome, true);

        };


        mostrarModal(); // Exibe o modal
      },
      error: function(xhr, status, error) {
        console.error("Erro na requisição AJAX:", error);
      },
    });
  }

  function apagar(id = "") {
    if (event.shiftKey) {
      id = "";
    }
    prepararRodapeModalApagar();
    var botao_confirmar_modal = prepararBotaoConfirmarModal("Apagar", "", "botao_confirmar_modal_apagar");
    if (!botao_confirmar_modal) {
      return;
    }
    botao_confirmar_modal.disabled = true;
    var modal_titulo = document.getElementById('modal_titulo');
    var modal_bory = document.getElementById('modal_bory');
    definirTamanhoModal(['modal-xl']);
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
      data: {
        id: id
      },
      success: function(response) {
        var lista = response.lista;
        modal_titulo.textContent = "Apagar " + tituloItemLote(response, false) + ": " + nomeItemLote(response.lista[0], response);







        console.log(response);

        var botao = document.getElementById('botao_confirmar_modal_apagar');

        botao.onclick = function() {

          temp = String(lista[0]['id']).replace(/^apagar_/, '');
          apagar_mesmo(temp);


          fecharModal();


        };

        modal_bory.appendChild(divElemnt); //coloca o input name no modal



        mostrarModal();
      }
    });






  }



  function apagar_mesmo(id) {
    $.ajax({
      url: '<?= base_url('public/nome_desenho') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: {
        id: id
      },
      success: function(response) {

        $.ajax({
          url: '<?= base_url('public/apagar_desenho') ?>',
          type: "POST",
          dataType: "json", // Indicar que o retorno é em formato JSON
          data: {
            id: id
          },
          success: function(response) {
            const tituloAlerta = response.projeto_id ? 'Projeto' : 'Desenho';
            if (response.ok == 'true') {
              alert_certo(tituloAlerta, response.mensagem);
            } else if (response.ok == 'false') {
              alert_personalizado(tituloAlerta, response.mensagem_false || response.mensagem || 'Nao foi possivel apagar.');
              if (response.mensagem_false && response.mensagem) {
                alert_certo(tituloAlerta, response.mensagem);
              }
            } else {
              temp = String(id).replace(/^apagar_/, '');

              apagar_mesmo(temp)
            }
            lista_corte(processo_nome, true);
          }
        });

      }
    });
  }

  function add_arquivo(id) {
    $.ajax({
      url: '<?= base_url('public/lista_filtro') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: {
        id: id
      },
      success: function(response) {
        // Dados do desenho
        var response = response.lista;

        // Verificação segura para evitar erro se lista ou nome estiver ausente
        var modal_titulo = document.getElementById('modal_titulo');
        if (lista && lista.length > 0 && lista[0].nome) {
          modal_titulo.textContent = "Enviar arquivos para: " + removeIdFromFile(lista[0].nome);
        } else {
          modal_titulo.textContent = "Enviar arquivos";
        }

        // Prepara o botão de confirmar com a função dinâmica
        var botao_confirmar_modal = prepararBotaoConfirmarModal("Confirmar", "confirmarModal(" + id + ")");
        definirTamanhoModal(['modal-xl']);
        if (!botao_confirmar_modal) {
          return;
        }

        // Limpa o conteúdo anterior do corpo do modal
        var modal_bory = document.getElementById('modal_bory');
        modal_bory.innerHTML = '';

        // INPUT DE ARQUIVOS PDF E DXF
        var divArquivos = document.createElement("div");
        divArquivos.classList.add("form-group");

        var labelArquivo = document.createElement("label");
        labelArquivo.textContent = "Selecionar arquivos";
        divArquivos.appendChild(labelArquivo);

        var inputArquivo = document.createElement("input");
        inputArquivo.type = "file";
        inputArquivo.name = "arquivos[]";
        inputArquivo.id = "input_arquivos";
        inputArquivo.classList.add("form-control");
        inputArquivo.multiple = true;
        inputArquivo.accept = response; // Corrigido: lista de extensões

        divArquivos.appendChild(inputArquivo);
        modal_bory.appendChild(divArquivos);

        // Exibe o modal
        mostrarModal();
      }
    });
  }

  function solicitarJustificativaCancelamentoCorte(nome) {
    var nomeLimpo = String(nome || '').trim();
    var titulo = nomeLimpo ? ('Cancelar corte de ' + nomeLimpo) : 'Cancelar corte';
    var mensagem = 'Informe a justificativa com pelo menos 15 caracteres.';

    if (window.Swal && typeof window.Swal.fire === 'function') {
      return window.Swal.fire({
        icon: 'warning',
        title: titulo,
        input: 'textarea',
        inputLabel: 'Justificativa',
        inputPlaceholder: 'Descreva o motivo do cancelamento...',
        inputAttributes: {
          'aria-label': 'Justificativa do cancelamento',
          maxlength: '4000'
        },
        inputValidator: function(value) {
          var texto = String(value || '').trim();
          if (texto.length < 15) {
            return mensagem;
          }
          return null;
        },
        showCancelButton: true,
        confirmButtonText: 'Cancelar corte',
        cancelButtonText: 'Fechar',
        reverseButtons: true
      }).then(function(result) {
        if (!result || !result.isConfirmed) {
          return '';
        }

        return String(result.value || '').trim();
      });
    }

    return new Promise(function(resolve) {
      while (true) {
        var valor = window.prompt(titulo + '\n' + mensagem, '');
        if (valor === null) {
          resolve('');
          return;
        }

        var texto = String(valor || '').trim();
        if (texto.length >= 15) {
          resolve(texto);
          return;
        }

        window.alert(mensagem);
      }
    });
  }

  function cancelar_corte(id, nome) {
    nome = nome || '';
    solicitarJustificativaCancelamentoCorte(nome).then(function(justificativa) {
      if (!justificativa) {
        return;
      }

      $.ajax({
        url: '<?= base_url('public/cancelar_corte') ?>',
        type: "POST",
        dataType: "json", // Indicar que o retorno Ã© em formato JSON
        data: {
          id: id,
          justificativa: justificativa
        },
        success: function(response) {
          if (response && response.ok) {
            alert_certo('Corte', response.mensagem || 'Corte cancelado com sucesso.');
          } else {
            alert_personalizado('Corte', response && response.mensagem ? response.mensagem : 'Nao foi possivel cancelar o corte.');
          }
          lista_corte(processo_nome, true);
        },
        error: function(xhr) {
          var mensagem = 'Nao foi possivel cancelar o corte.';
          if (xhr && xhr.responseJSON && xhr.responseJSON.mensagem) {
            mensagem = xhr.responseJSON.mensagem;
          }
          alert_personalizado('Corte', mensagem);
          lista_corte(processo_nome, true);
        }
      });
    });
    return;

    if (mostrarConfirmacao('Cancelar corte?')) {
      $.ajax({
        url: '<?= base_url('public/cancelar_corte') ?>',
        type: "POST",
        dataType: "json", // Indicar que o retorno é em formato JSON
        data: {
          id: id
        },
        success: function(response) {
          lista_corte(processo_nome, true);


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
<style>
  .quebrar {
    word-break: break-all;
    white-space: normal;
  }
</style>
