<script>
  let telaAddDesenho = "";
  var processo_nome = "";

  function injetarEstilosSelecaoProcesso() {
    if (document.getElementById('wl-process-picker-style-corte')) {
      return;
    }

    var style = document.createElement('style');
    style.id = 'wl-process-picker-style-corte';
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
          <p>Selecione o processo para carregar a lista de tarefas.</p>
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
      tituloCard.innerHTML = "<button type=\"button\" onclick=\"atualizarInterfaceParaSelecaoDeProcesso(false)\" class=\"btn btn-outline-primary\">Voltar</button>&nbsp;&nbsp;Lista de tarefas \"" + processo_nome + "\"";
    }

    lista(processo_nome, true);
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
      celula.style.setProperty('background-color', corValida, 'important');
      celula.style.setProperty('color', corTexto, 'important');

      var textos = celula.querySelectorAll('span, .marca_texto');
      textos.forEach(function(texto) {
        texto.style.setProperty('color', corTexto, 'important');
      });
    });
  }


  processos_select = "";

  function processo_lista() {
    $.ajax({
      url: '<?= base_url('public/processos_lista') ?>',
      type: "POST",
      dataType: "json",
      data: { contexto_tela: 'lista_corte' },
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
  lista_temp = "";

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
    var aviso = document.getElementById('wl-lista-vazia-aviso');

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
      aviso.id = 'wl-lista-vazia-aviso';
      aviso.className = 'text-muted py-2';
      aviso.textContent = 'Sem desenhos para exibir.';
      tabelaContainer.parentElement.insertBefore(aviso, rodaPe || null);
    }
  }

  function lista(processo_nome,ok = false) {
    // Realiza uma requisição AJAX para a URL especificada, que deve retornar dados em formato JSON.
    $.ajax({
      url: '<?= base_url('public/lista_corte') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: {
                nome_processos: processo_nome
      },
      success: function(response) {
        // A função success() é chamada quando a requisição AJAX é concluída com sucesso.

        // Verifica se a lista atualizada é diferente da lista anterior (lista_temp).

        if (response.lista != lista_temp || ok) {
          // Destroi a tabela DataTable existente para recriá-la com os novos dados.

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
            // Recria e configura a tabela DataTable com os novos dados.

            $("#example1").DataTable({

              "responsive": true,
              "lengthChange": false,
              "autoWidth": false,
              "buttons": ["colvis"],
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

            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

          });


          // Atualiza lista_temp com a nova lista.

          lista_temp = response.lista;
        }
      }
    });
  }
  // Executar função ao abrir o site
  // document.addEventListener('DOMContentLoaded', lista);

  // Repetir função a cada segundo
  //setInterval(lista, 10000);
</script>
