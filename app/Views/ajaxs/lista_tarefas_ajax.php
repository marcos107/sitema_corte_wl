<?= view('imports/desenho_adicionar_import'); ?>

<script>
  lista_temp = "";
  finalidade_pesquisa = "";
  var bellQueue = [];
  var isPlaying = false;
  var playInterval;
  var tela_add_desenho = "";
  var processo_nome = "";
  var processos = [];
  var itensNotificacaoAnterior = {};
  var notificacoesFilaInicializadas = false;
  var chavePreferenciaAlertasNovosDesenhos = "wl_lista_tarefas_alerta_novos_desenhos";
  var tabelaListaTarefasDt = null;
  var listaTarefasMeta = {};

  function injetarEstilosSelecaoProcesso() {
    if (document.getElementById("wl-process-picker-style-lista-tarefas")) {
      return;
    }

    var style = document.createElement("style");
    style.id = "wl-process-picker-style-lista-tarefas";
    style.textContent = `
      .wl-process-picker {
        display: grid;
        gap: 1rem;
        border: 1px dashed var(--tb-border-color, #dbe5f1);
        border-radius: .7rem;
        padding: .95rem;
        background: rgba(248, 250, 252, .8);
      }
      .wl-process-picker-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: .75rem;
        flex-wrap: wrap;
      }
      .wl-process-picker-head h6 { margin: 0 0 .2rem; font-size: 1rem; font-weight: 700; color: #0f172a; }
      .wl-process-picker-head p { margin: 0; color: #64748b; font-size: .9rem; }
      .wl-process-step {
        background: #e0f2fe;
        color: #0369a1;
        border-radius: 999px;
        font-size: .74rem;
        font-weight: 700;
        padding: .24rem .62rem;
        letter-spacing: .01em;
      }
      .wl-process-grid { display: grid; gap: .75rem; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }
      .wl-process-card {
        border: 1px solid #dbe1ea;
        border-radius: .75rem;
        background: #ffffff;
        padding: .85rem;
        cursor: pointer;
        transition: all .2s ease;
        position: relative;
      }
      .wl-process-card:hover {
        border-color: #93c5fd;
        box-shadow: 0 2px 10px rgba(37, 99, 235, .1);
      }
      .wl-process-card.is-selected {
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, .16);
        background: #f8fbff;
      }
      .wl-process-card input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
      }
      .wl-process-name {
        display: block;
        font-weight: 600;
        color: #0f172a;
        line-height: 1.25;
      }
      .wl-process-picker-actions {
        display: flex;
        justify-content: flex-end;
      }
      .wl-process-picker-actions .btn {
        min-width: 160px;
        font-weight: 600;
      }
      .wl-process-empty {
        color: #64748b;
        font-size: .9rem;
        border: 1px dashed #cbd5e1;
        border-radius: .6rem;
        padding: .8rem;
        text-align: center;
      }
      @media (max-width: 576px) {
        .wl-process-picker {
          padding: .75rem;
        }
        .wl-process-picker-actions .btn {
          width: 100%;
          min-width: 0;
        }
      }
    `;

    document.head.appendChild(style);
  }

  function injetarEstilosAcoesListaTarefas() {
    if (document.getElementById("wl-lista-tarefas-acoes-style")) {
      return;
    }

    var style = document.createElement("style");
    style.id = "wl-lista-tarefas-acoes-style";
    style.textContent = `
      .wl-row-actions {
        display: inline-flex;
        align-items: center;
        justify-content: flex-end;
        gap: .35rem;
      }
      .wl-row-action-main {
        min-width: 86px;
      }
      .wl-row-actions--with-menu .wl-row-action-main {
        min-width: 98px;
      }
      .wl-row-actions--with-menu .dropdown-menu {
        min-width: 190px;
        padding: .35rem;
        border: 1px solid #dbe5f1;
        border-radius: .75rem;
        box-shadow: 0 12px 24px rgba(15, 23, 42, .14);
      }
      .wl-row-actions--with-menu .dropdown-item {
        display: flex;
        align-items: center;
        padding: .55rem .75rem;
        border-radius: .5rem;
        font-weight: 500;
      }
      .wl-row-actions--with-menu .dropdown-item i {
        font-size: 1rem;
      }
      .wl-row-action-more {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 31px;
        padding: 0;
        font-size: 16px;
        line-height: 1;
      }
      .wl-row-action-more i {
        pointer-events: none;
      }
    `;

    document.head.appendChild(style);
  }

  function atualizarCardsProcessoSelecionado() {
    var radios = document.querySelectorAll("#processos_radio input[name='processo']");
    radios.forEach(function(radio) {
      var card = radio.closest(".wl-process-card");
      if (!card) {
        return;
      }

      if (radio.checked) {
        card.classList.add("is-selected");
      } else {
        card.classList.remove("is-selected");
      }
    });
  }

  function get_radio() {
    var radios = document.getElementsByName("processo");
    for (var i = 0; i < radios.length; i++) {
      if (radios[i].checked) {
        return radios[i];
      }
    }
    return null;
  }

  function storageAlertaNovoDesenhoDisponivel() {
    try {
      return typeof window.localStorage !== "undefined";
    } catch (error) {
      return false;
    }
  }

  function obterAlertaNovoDesenhoAtivo() {
    if (!storageAlertaNovoDesenhoDisponivel()) {
      var checkboxAtual = document.getElementById("checkbox_alerta_novo_desenho");
      return checkboxAtual ? checkboxAtual.checked : true;
    }

    var valorSalvo = window.localStorage.getItem(chavePreferenciaAlertasNovosDesenhos);
    if (valorSalvo === null) {
      return true;
    }

    return valorSalvo === "1";
  }

  function aplicarEstadoCheckboxAlertaNovoDesenho() {
    var checkbox = document.getElementById("checkbox_alerta_novo_desenho");
    if (!checkbox) {
      return;
    }

    checkbox.checked = obterAlertaNovoDesenhoAtivo();
  }

  function alternarAlertaNovoDesenho(ativo) {
    if (storageAlertaNovoDesenhoDisponivel()) {
      window.localStorage.setItem(chavePreferenciaAlertasNovosDesenhos, ativo ? "1" : "0");
    }

    aplicarEstadoCheckboxAlertaNovoDesenho();
  }

  function montarTelaSelecaoProcesso(guardarConteudoOriginal) {
    var cardBody = document.getElementById("top-lista");
    if (!cardBody) {
      cardBody = document.querySelector(".card-body");
    }

    if (!cardBody) {
      return;
    }

    if (guardarConteudoOriginal || !tela_add_desenho) {
      tela_add_desenho = cardBody.innerHTML;
    }

    injetarEstilosSelecaoProcesso();

    cardBody.innerHTML = `
      <div class="wl-process-picker">
        <div class="wl-process-picker-head">
          <div>
            <h6>Selecionar Processo</h6>
            <p>Primeiro, selecione o processo para carregar a lista de tarefas.</p>
          </div>
          <span class="wl-process-step">Etapa 1/2</span>
        </div>
        <div id="processos_radio" class="wl-process-grid"></div>
        <div class="wl-process-picker-actions">
          <button name="cadastarar" type="button" onclick="aparecer_lista()" class="btn btn-primary">Continuar</button>
        </div>
      </div>
    `;

    var tituloCard = document.querySelector(".card-title");
    if (tituloCard) {
      tituloCard.textContent = "Escolha o processo";
    }

    processo_lista();
  }

  function inicio_tela() {
    processo_nome = "";
    lista_temp = "";
    resetarNotificacoesFila();
    montarTelaSelecaoProcesso(false);
  }

  function aparecer_lista() {
    var processoSelecionado = get_radio();
    if (!processoSelecionado) {
      alert("Selecione um processo para continuar.");
      return;
    }

    processo_nome = processoSelecionado.value;
    lista_temp = "";
    resetarNotificacoesFila();

    var cardBody = document.getElementById("top-lista");
    if (!cardBody) {
      cardBody = document.querySelector(".card-body");
    }

    if (cardBody && tela_add_desenho) {
      cardBody.innerHTML = tela_add_desenho;
      aplicarEstadoCheckboxAlertaNovoDesenho();
    }

    var tituloCard = document.querySelector(".card-title");
    if (tituloCard) {
      tituloCard.innerHTML = "<div class='d-flex flex-wrap align-items-center gap-2'><button type='button' onclick='inicio_tela()' class='btn btn-outline-primary btn-sm'><i class='ri-arrow-left-line align-bottom me-1'></i>Voltar</button><span>Lista | " + processo_nome + "</span></div>";
    }

    lista(true);
  }

  function processo_lista() {
    $.ajax({
      url: '<?= base_url('public/processos_lista') ?>',
      type: "POST",
      dataType: "json",
      data: { contexto_tela: 'lista_tarefas' },
      success: function(response) {
        processos = Array.isArray(response.lista) ? response.lista : [];

        var radioContainer = document.getElementById("processos_radio");
        if (!radioContainer) {
          return;
        }

        radioContainer.innerHTML = "";

        if (!processos.length) {
          var emptyState = document.createElement("div");
          emptyState.className = "wl-process-empty";
          emptyState.textContent = "Nenhum processo encontrado.";
          radioContainer.appendChild(emptyState);
          return;
        }

        var possuiSelecionado = false;

        processos.forEach(function(processo, index) {
          var inputId = "processo_" + (processo.input || "proc") + "_" + index;

          var label = document.createElement("label");
          label.className = "wl-process-card";
          label.htmlFor = inputId;

          var radioElement = document.createElement("input");
          radioElement.type = "radio";
          radioElement.name = "processo";
          radioElement.id = inputId;
          radioElement.value = processo.nome;
          radioElement.addEventListener("change", atualizarCardsProcessoSelecionado);

          if (processo_nome && processo.nome === processo_nome) {
            radioElement.checked = true;
            possuiSelecionado = true;
          }

          var nomeElement = document.createElement("span");
          nomeElement.className = "wl-process-name";
          nomeElement.textContent = processo.nome;

          label.appendChild(radioElement);
          label.appendChild(nomeElement);
          radioContainer.appendChild(label);
        });

        if (!possuiSelecionado) {
          var primeiroRadio = radioContainer.querySelector("input[name='processo']");
          if (primeiroRadio) {
            primeiroRadio.checked = true;
          }
        }

        atualizarCardsProcessoSelecionado();
      }
    });
  }

  montarTelaSelecaoProcesso(true);
  injetarEstilosAcoesListaTarefas();

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
          console.error("Erro ao tentar tocar o ÃƒÂ¡udio:", error);
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

  function resetarNotificacoesFila() {
    itensNotificacaoAnterior = {};
    notificacoesFilaInicializadas = false;
  }

  function escaparHtml(valor) {
    return String(valor || "").replace(/[&<>"']/g, function(caractere) {
      return {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        "\"": "&quot;",
        "'": "&#039;"
      }[caractere];
    });
  }

  function normalizarItensNotificacao(itens) {
    if (!Array.isArray(itens)) {
      return [];
    }

    return itens
      .filter(function(item) {
        return item && item.item_id;
      })
      .map(function(item) {
        return {
          item_id: String(item.item_id),
          processo: String(item.processo || processo_nome || ""),
          projetista: String(item.projetista || ""),
          desenho: String(item.desenho || "")
        };
      });
  }

  function indexarItensNotificacao(itens) {
    var mapa = {};

    itens.forEach(function(item) {
      mapa[item.item_id] = item;
    });

    return mapa;
  }

  function obterNovosItensNotificacao(itens) {
    var itensNormalizados = normalizarItensNotificacao(itens);
    var mapaAtual = indexarItensNotificacao(itensNormalizados);

    if (!notificacoesFilaInicializadas) {
      itensNotificacaoAnterior = mapaAtual;
      notificacoesFilaInicializadas = true;
      return [];
    }

    var novosItens = itensNormalizados.filter(function(item) {
      return !Object.prototype.hasOwnProperty.call(itensNotificacaoAnterior, item.item_id);
    });

    itensNotificacaoAnterior = mapaAtual;
    notificacoesFilaInicializadas = true;

    return novosItens;
  }

  function montarTextoNotificacao(item) {
    return [
      "Processo: " + (item.processo || "-"),
      "Projetista: " + (item.projetista || "-"),
      "Desenho: " + (item.desenho || "-")
    ];
  }

  function mostrarNotificacaoNovoItem(item) {
    var linhas = montarTextoNotificacao(item);
    var titulo = "Novo desenho na fila";
    var corpoHtml = linhas.map(function(linha) {
      return "<div>" + escaparHtml(linha) + "</div>";
    }).join("");

    if (window.toastr && typeof window.toastr.info === "function") {
      window.toastr.options = window.toastr.options || {};
      window.toastr.options.timeOut = 9000;
      window.toastr.options.extendedTimeOut = 3000;
      window.toastr.options.closeButton = true;
      window.toastr.options.progressBar = true;
      window.toastr.options.preventDuplicates = false;
      window.toastr.options.escapeHtml = false;
      window.toastr.info(corpoHtml, titulo);
      return;
    }

    if (window.Swal && typeof window.Swal.fire === "function") {
      window.Swal.fire({
        icon: "info",
        title: titulo,
        html: corpoHtml,
        timer: 9000,
        timerProgressBar: true,
        toast: true,
        position: "top-end",
        showConfirmButton: false
      });
      return;
    }

    window.alert(titulo + "\n" + linhas.join("\n"));
  }

  function notificarNovosItensFila(itens, podeTocarSom) {
    if (!Array.isArray(itens) || !itens.length) {
      return;
    }

    if (!obterAlertaNovoDesenhoAtivo()) {
      return;
    }

    if (podeTocarSom) {
      playBellSound();
    }

    itens.forEach(function(item) {
      mostrarNotificacaoNovoItem(item);
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

  function aplicarCorPrioridadeNaTabela() {
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

  function resetarDataTableExample1() {
    if (!window.jQuery || !$.fn || !$.fn.DataTable) {
      return;
    }

    if ($.fn.DataTable.isDataTable('#example1')) {
      $('#example1').DataTable().destroy();
    }
    tabelaListaTarefasDt = null;
  }

  function ativarTooltipsAcoes() {
    if (!(window.bootstrap && window.bootstrap.Tooltip)) {
      return;
    }

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (elemento) {
      if (!window.bootstrap.Tooltip.getInstance(elemento)) {
        new window.bootstrap.Tooltip(elemento, {
          container: 'body'
        });
      }
    });
  }

  function listaLegacyHtmlDesativada(ok = false) {
    $.ajax({
      url: '<?= base_url('public/lista_tarefas') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno ÃƒÂ© em formato JSON
      data: {
        processo: processo_nome,
        finalidade: finalidade_pesquisa
      },
      success: function(response) {
        var novosItens = obterNovosItensNotificacao(response.itens_notificacao);
        if (novosItens.length > 0) {
          var podeTocarSom = response.som === true || String(response.som || "").toLowerCase() === "true";
          notificarNovosItensFila(novosItens, podeTocarSom);
        }

        if (response.lista != lista_temp || ok) {
          adicionarPesquisa(response.finalidade_pesquisa);
          resetarDataTableExample1();

          var lista = document.getElementById('lista');
          if (!lista) {
            return;
          }
          return;
          ativarTooltipsAcoes();
          aplicarCorPrioridadeNaTabela();
          if (response.tipo_processo == 'ind') {
            document.querySelectorAll("th").forEach(th => {
              if (th.textContent.trim() === "Nome do arquivo") {
                th.textContent = "Descrição";
              }
            });
          } else {
            document.querySelectorAll("th").forEach(th => {
              if (th.textContent.trim() === "Descrição") {
                th.textContent = "Nome do arquivo";
              }
            });
          }

          atualizarCabecalhoListaTarefas(response.tipo_processo, response.mostrar_dimensao_dxf);
          var totalColunasTabela = $('#example1 thead th').length;
          var colunasAcoes = totalColunasTabela >= 2
            ? [totalColunasTabela - 2, totalColunasTabela - 1]
            : [];
          var colunaDimensaoDxf = -1;
          $('#example1 thead th').each(function(index) {
            if ($(this).text().trim() === 'Dimensao DXF') {
              colunaDimensaoDxf = index;
            }
          });

          var definicoesColunas = [
            {
              "targets": colunasAcoes,
              "orderable": false,
              "searchable": false,
              "width": "150px",
              "className": "text-end text-nowrap wl-col-acoes"
            }
          ];

          if (colunaDimensaoDxf >= 0) {
            definicoesColunas.push({
              "targets": [colunaDimensaoDxf],
              "width": "1%",
              "className": "text-center text-nowrap wl-col-dimensao-dxf"
            });
          }

          $(function() {
            $("#example1").DataTable({
              "order": [], // respeita a ordem enviada pelo backend
              "responsive": true,
              "deferRender": true,
              "lengthChange": false,
              "autoWidth": false,
              "buttons": ["colvis"],
              "columnDefs": definicoesColunas,
              "drawCallback": function() {
                aplicarCorPrioridadeNaTabela();
                ativarTooltipsAcoes();
              },
              "language": {
                "decimal": "",
                "emptyTable": "Sem dados disponíveis",
                "infoEmpty": "Mostrando de 0 até 0 de 0 registros",
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

  function atualizarCabecalhoListaTarefas(tipoProcesso, mostrarDimensaoDxf) {
    var thead = document.querySelector('#example1 thead tr');
    var tfoot = document.querySelector('#example1 tfoot tr');
    var tabela = document.getElementById('example1');
    if (!thead || !tfoot) {
      return;
    }

    var tipo = String(tipoProcesso || '').trim().toLowerCase();
    var exibeDimensao = mostrarDimensaoDxf === true || String(mostrarDimensaoDxf || '').toLowerCase() === 'true';
    var cabecalhos = [];

    if (tipo === 'ind') {
      cabecalhos = [
        'Prioridade',
        'Ordem',
        'Desenhista',
        'Descricao',
        'Empresa/Cliente',
        'Empreendimento',
        'Finalidade',
        'Subpastas',
        'Status',
        'Data de Envio',
        'Baixar',
        'Finalizar'
      ];
    } else if (exibeDimensao) {
      cabecalhos = [
        'Prioridade',
        'Ordem',
        'Desenhista',
        'Nome do arquivo',
        'Empresa/Cliente',
        'Empreendimento',
        'Finalidade',
        'Subpastas',
        'Dimensao DXF',
        'Data de Envio',
        'Cortar',
        'Confirmar Corte'
      ];
    } else {
      cabecalhos = [
        'Prioridade',
        'Ordem',
        'Desenhista',
        'Nome do arquivo',
        'Empresa/Cliente',
        'Empreendimento',
        'Finalidade',
        'Subpastas',
        'Status',
        'Data de Envio',
        'Cortar',
        'Confirmar Corte'
      ];
    }

    if (tabela) {
      tabela.classList.toggle('wl-has-dxf', exibeDimensao && tipo !== 'ind');
      tabela.classList.toggle('wl-processo-ind', tipo === 'ind');
    }

    var html = cabecalhos.map(function(coluna) {
      var classes = [];
      if (coluna === 'Dimensao DXF') {
        classes.push('wl-col-dimensao-dxf', 'text-center', 'text-nowrap');
      }
      if (['Cortar', 'Confirmar Corte', 'Baixar', 'Finalizar'].indexOf(coluna) !== -1) {
        classes.push('wl-col-acoes', 'text-end', 'text-nowrap');
      }

      return '<th' + (classes.length ? ' class="' + classes.join(' ') + '"' : '') + '>' + coluna + '</th>';
    }).join('');

    thead.innerHTML = html;
    tfoot.innerHTML = html;
  }

  function textoJs(valor) {
    return JSON.stringify(String(valor == null ? '' : valor));
  }

  function spanTruncado(valor) {
    var texto = escaparHtml(valor || '');
    return '<span class="wl-cell-truncate" title="' + texto + '">' + texto + '</span>';
  }

  function renderizarEmpreendimentoLista(row) {
    var nome = escaparHtml(row && row.empreendimento_nome || '');
    var escala = escaparHtml(row && row.empreendimento_escala || '');
    if (!escala) {
      return spanTruncado(nome);
    }

    var titulo = escaparHtml((row.empreendimento_nome || '') + ' - Escala ' + (row.empreendimento_escala || ''));
    return '<span class="wl-cell-truncate" title="' + titulo + '">' + nome + '</span>' +
      '<div class="text-muted small">Escala ' + escala + '</div>';
  }

  function renderizarMenuAcoesLista(itens) {
    if (!Array.isArray(itens) || !itens.length) {
      return '';
    }

    var itensValidos = itens.filter(function(item) {
      return item && String(item.rotulo || '').trim();
    });

    if (!itensValidos.length) {
      return '';
    }

    if (itensValidos.length === 1) {
      var itemUnico = itensValidos[0];
      var rotuloUnico = escaparHtml(itemUnico.rotulo || '');
      var classeUnica = itemUnico.classe ? ' ' + escaparHtml(itemUnico.classe) : '';
      var onclickUnico = itemUnico.onclick ? ' onclick="' + escaparHtml(itemUnico.onclick) + '"' : '';
      var iconeUnico = itemUnico.icone ? '<i class="' + escaparHtml(itemUnico.icone) + '"></i>' : rotuloUnico;

      return '<button type="button" class="btn btn-sm btn-outline-secondary wl-row-action-more wl-row-action-single' + classeUnica + '"' +
        onclickUnico +
        ' data-bs-toggle="tooltip" data-bs-title="' + rotuloUnico + '" title="' + rotuloUnico + '" aria-label="' + rotuloUnico + '">' +
        iconeUnico + '</button>';
    }

    var html = itensValidos.map(function(item) {
      var classe = item.classe ? ' ' + escaparHtml(item.classe) : '';
      var onclick = item.onclick ? ' onclick="' + escaparHtml(item.onclick) + '"' : '';
      var icone = item.icone ? '<i class="' + escaparHtml(item.icone) + ' align-bottom me-1"></i>' : '';
      return '<li><button type="button" class="dropdown-item' + classe + '"' + onclick + '>' +
        icone + escaparHtml(item.rotulo || '') + '</button></li>';
    }).join('');

    return '<div class="dropdown">' +
      '<button type="button" class="btn btn-sm btn-outline-secondary wl-row-action-more dropdown-toggle" data-bs-toggle="dropdown" data-toggle="dropdown" aria-expanded="false" aria-label="Mais acoes">' +
      '<i class="ri-more-2-fill"></i>' +
      '</button>' +
      '<ul class="dropdown-menu dropdown-menu-end">' + html + '</ul>' +
      '</div>';
  }

  function renderizarAcoesCorteLaserLista(indice, botaoPrincipal, mostrarAbrirNaMaquina, extras) {
    var itens = [{
      rotulo: 'Visualizar',
      onclick: "ver_dxf('" + indice + "')",
      icone: 'ri-eye-line'
    }];

    if (mostrarAbrirNaMaquina) {
      itens.push({
        rotulo: 'Abrir na maquina',
        onclick: 'abrir_cort(' + indice + ')',
        icone: 'ri-send-plane-line'
      });
    }

    if (Array.isArray(extras)) {
      extras.forEach(function(extra) {
        itens.push(extra);
      });
    }

    return '<div class="wl-row-actions wl-row-actions--with-menu">' + botaoPrincipal + renderizarMenuAcoesLista(itens) + '</div>';
  }

  function rotuloBotaoBaixarProjetoPorContagem(totalArquivos, arquivosBaixados) {
    var total = parseInt(totalArquivos || 0, 10);
    var baixados = parseInt(arquivosBaixados || 0, 10);

    if (isNaN(total) || total < 0) {
      total = 0;
    }

    if (isNaN(baixados) || baixados < 0) {
      baixados = 0;
    }

    if (total > 0 && baixados >= total) {
      return 'Baixado';
    }

    if (baixados > 0) {
      return 'Baixando';
    }

    return 'Ver';
  }

  function rotuloBotaoBaixarProjeto(row) {
    return rotuloBotaoBaixarProjetoPorContagem(
      row && row.arquivos_count,
      row && row.arquivos_baixados_count
    );
  }

  function renderizarAcaoPrincipalLista(row) {
    var indice = parseInt(row && row.indice || 0, 10);
    var tipo = String(listaTarefasMeta.tipo_processo || row.tipo_processo || '').toLowerCase();
    var status = String(row && row.status_normalizado || '').toLowerCase();
    var exibeDxf = listaTarefasMeta.mostrar_dimensao_dxf === true || String(listaTarefasMeta.mostrar_dimensao_dxf || '').toLowerCase() === 'true';
    var possuiCorteAtivo = String(listaTarefasMeta.status || '').toLowerCase() === 'cortando';

    if (tipo === 'ind') {
      if (status === 'processando') {
        return '<div class="wl-row-actions"><button type="button" disabled class="btn btn-sm btn-outline-dark wl-row-action-main">Processando...</button></div>';
      }
      return '<div class="wl-row-actions"><button type="button" onclick="baixar(' + indice + ')" class="btn btn-sm btn-primary wl-row-action-main">' + rotuloBotaoBaixarProjeto(row) + '</button></div>';
    }

    if (status === 'processando') {
      if (exibeDxf) {
        return renderizarAcoesCorteLaserLista(
          indice,
          '<button type="button" disabled class="btn btn-sm btn-outline-dark wl-row-action-main">Processando...</button>'
        );
      }
      return '<div class="wl-row-actions"><button type="button" disabled class="btn btn-sm btn-outline-dark wl-row-action-main">Processando...</button></div>';
    }

    if (status === 'cortando') {
      if (row && row.eh_corte_usuario) {
        if (exibeDxf) {
          return renderizarAcoesCorteLaserLista(
            indice,
            '<button type="button" onclick="mostrar_caminho_corte_atual(' + indice + ')" class="btn btn-sm btn-outline-primary wl-row-action-main">Em corte</button>',
            true,
            [{
              rotulo: 'Cancelar corte',
              onclick: 'cancelar_corte(' + indice + ', ' + textoJs(row.nome_arquivo || '') + ')',
              icone: 'ri-close-circle-line',
              classe: 'text-danger'
            }]
          );
        }

        return '<div class="wl-row-actions">' +
          '<button type="button" onclick="ver_dxf(' + indice + ')" class="btn btn-sm btn-outline-info wl-row-action-main">Ver</button>' +
          '<button type="button" onclick="buscarArquivos(' + indice + ')" class="btn btn-sm btn-outline-primary">Baixar</button>' +
          '</div>';
      }

      if (exibeDxf) {
        return renderizarAcoesCorteLaserLista(
          indice,
          '<button type="button" disabled class="btn btn-sm btn-outline-dark wl-row-action-main">Em corte</button>'
        );
      }
      return '<div class="wl-row-actions"><button type="button" disabled class="btn btn-sm btn-outline-dark wl-row-action-main">Processando...</button></div>';
    }

    if (possuiCorteAtivo) {
      if (exibeDxf) {
        return renderizarAcoesCorteLaserLista(
          indice,
          '<button type="button" disabled class="btn btn-sm btn-outline-dark wl-row-action-main">Inicializar</button>'
        );
      }
      return '<div class="wl-row-actions"><button type="button" disabled class="btn btn-sm btn-outline-dark wl-row-action-main">Cortar</button></div>';
    }

    if (exibeDxf) {
      return renderizarAcoesCorteLaserLista(
        indice,
        '<button type="button" onclick="cortar(' + indice + ')" class="btn btn-sm btn-primary wl-row-action-main">Inicializar</button>'
      );
    }

    return '<div class="wl-row-actions"><button type="button" onclick="cortar(' + indice + ')" class="btn btn-sm btn-primary wl-row-action-main">Cortar</button></div>';
  }

  function renderizarAcaoConfirmarLista(row) {
    var indice = parseInt(row && row.indice || 0, 10);
    var tipo = String(listaTarefasMeta.tipo_processo || row.tipo_processo || '').toLowerCase();
    var status = String(row && row.status_normalizado || '').toLowerCase();
    var exibeDxf = listaTarefasMeta.mostrar_dimensao_dxf === true || String(listaTarefasMeta.mostrar_dimensao_dxf || '').toLowerCase() === 'true';
    var possuiCorteAtivo = String(listaTarefasMeta.status || '').toLowerCase() === 'cortando';
    var rotulo = exibeDxf || tipo === 'ind' ? 'Finalizar' : 'Confirmar Corte';

    if (status === 'processando' || (possuiCorteAtivo && status !== 'cortando') || (status === 'cortando' && !(row && row.eh_corte_usuario))) {
      return '<div class="wl-row-actions wl-row-actions--confirm"><button type="button" disabled class="btn btn-sm btn-outline-dark">' + rotulo + '</button></div>';
    }

    if (tipo === 'ind') {
      return '<div class="wl-row-actions wl-row-actions--confirm"><button name="cadastarar" type="button" onclick="confirmar_ind(\'' + indice + '\',\'\')" class="btn btn-sm btn-success">Finalizar</button></div>';
    }

    return '<div class="wl-row-actions wl-row-actions--confirm"><button name="cadastarar" type="button" onclick="confirmar(\'' + indice + '\',' + textoJs(row.nome_arquivo || '') + ')" class="btn btn-sm btn-success">' + rotulo + '</button></div>';
  }

  function aplicarMetadataListaTarefas(response) {
    listaTarefasMeta = response || {};
    adicionarPesquisa(listaTarefasMeta.finalidade_pesquisa || '');
    atualizarCabecalhoListaTarefas(listaTarefasMeta.tipo_processo, listaTarefasMeta.mostrar_dimensao_dxf);

    setTimeout(function() {
      if (tabelaListaTarefasDt && tabelaListaTarefasDt.columns) {
        tabelaListaTarefasDt.columns.adjust();
        aplicarCorPrioridadeNaTabela();
        ativarTooltipsAcoes();
      }
    }, 0);
  }

  function lista(ok = false) {
    if (!processo_nome) {
      return;
    }

    $.ajax({
      url: '<?= base_url('public/lista_tarefas') ?>',
      type: 'POST',
      dataType: 'json',
      data: {
        processo: processo_nome,
        finalidade: finalidade_pesquisa,
        html_lista: '1'
      },
      success: function(response) {
        var novosItens = obterNovosItensNotificacao(response && response.itens_notificacao);
        if (novosItens.length > 0) {
          var podeTocarSom = response.som === true || String(response.som || '').toLowerCase() === 'true';
          notificarNovosItensFila(novosItens, podeTocarSom);
        }

        var htmlLista = String(response && response.lista || '');
        if (htmlLista === lista_temp && !ok) {
          return;
        }

        resetarDataTableExample1();
        aplicarMetadataListaTarefas(response || {});

        var listaBody = document.getElementById('lista');
        if (!listaBody) {
          return;
        }
        listaBody.innerHTML = htmlLista;

        tabelaListaTarefasDt = $('#example1').DataTable({
          order: [],
          responsive: true,
          deferRender: true,
          lengthChange: true,
          pageLength: 50,
          lengthMenu: [25, 50, 100],
          autoWidth: false,
          buttons: ['colvis'],
          columnDefs: [
            {
              targets: [-2, -1],
              orderable: false,
              searchable: false,
              className: 'text-end text-nowrap wl-col-acoes'
            }
          ],
          drawCallback: function() {
            aplicarCorPrioridadeNaTabela();
            ativarTooltipsAcoes();
          },
          language: {
            decimal: '',
            emptyTable: 'Sem dados disponiveis',
            infoEmpty: 'Mostrando de 0 ate 0 de 0 registros',
            infoFiltered: '(filtrado de _MAX_ registros no total)',
            infoPostFix: '',
            thousands: ',',
            lengthMenu: '_MENU_',
            loadingRecords: 'A carregar dados...',
            processing: 'A processar...',
            search: 'Buscar:',
            zeroRecords: 'Nao foram encontrados resultados',
            paginate: {
              first: 'Primeiro',
              last: 'Ultimo',
              next: 'Seguinte',
              previous: 'Anterior'
            },
            aria: {
              sortAscending: ': clique para ordenar ascendente (ASC)',
              sortDescending: ': clique para ordenar descendente (DESC)'
            }
          }
        });

        if (tabelaListaTarefasDt.buttons) {
          tabelaListaTarefasDt.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        }

        lista_temp = htmlLista;
        aplicarCorPrioridadeNaTabela();
        ativarTooltipsAcoes();
      }
    });
  }

  // Verifica a fila e tenta tocar o som a cada 1 segundo
  // playInterval = setInterval(() => {
  //   if (bellQueue.length > 0 && !isPlaying) {
  //     playBellSound();
  //   }
  // }, 1000);

  // // SimulaÃƒÂ§ÃƒÂ£o de chamadas periÃƒÂ³dicas para a funÃƒÂ§ÃƒÂ£o lista
  // setInterval(() => {
  //   lista();
  // }, 5000); // Intervalo de 5 segundos para chamar a funÃƒÂ§ÃƒÂ£o lista // Executar funÃƒÂ§ÃƒÂ£o ao abrir o site
  // document.addEventListener('DOMContentLoaded', lista);

  // // Repetir funÃƒÂ§ÃƒÂ£o a cada segundo
  // lista_tarefas(lista, 60000);

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

  function mostrarCaminhoArquivoCorte(caminho, titulo) {
    var caminhoTexto = String(caminho || "").trim();
    if (!caminhoTexto) {
      return;
    }

    try {
      copy(caminhoTexto);
    } catch (e) {}

    var tituloModal = String(titulo || "Caminho do arquivo");

    if (window.Swal && typeof window.Swal.fire === "function") {
      window.Swal.fire({
        icon: "info",
        title: tituloModal,
        html: '<div class="text-start"><p class="mb-2">O caminho foi copiado para a area de transferencia.</p><textarea class="form-control" rows="4" readonly>' + escaparHtml(caminhoTexto) + '</textarea></div>',
        confirmButtonText: "Fechar"
      });
      return;
    }

    if (typeof alert_personalizado === "function") {
      alert_personalizado(tituloModal, caminhoTexto);
      return;
    }

    window.alert(tituloModal + "\n" + caminhoTexto);
  }

  function mostrarErroOperacaoCorte(titulo, mensagem) {
    notificarCancelamentoCorte("error", titulo || "Corte", mensagem || "Nao foi possivel concluir a operacao.");
  }

  function mostrar_caminho_corte_atual(id) {
    $.ajax({
      url: '<?= base_url('public/caminho_desenho_atual') ?>',
      type: "POST",
      dataType: "json",
      data: {
        id: id
      },
      success: function(response) {
        if (response && response.ok && response.caminho) {
          mostrarCaminhoArquivoCorte(response.caminho, "Caminho do arquivo em corte");
          return;
        }

        mostrarErroOperacaoCorte("Corte", response && response.mensagem ? response.mensagem : "Nao foi possivel consultar o caminho do arquivo.");
      },
      error: function(xhr) {
        var mensagem = "Nao foi possivel consultar o caminho do arquivo.";
        if (xhr && xhr.responseJSON && xhr.responseJSON.mensagem) {
          mensagem = xhr.responseJSON.mensagem;
        }
        mostrarErroOperacaoCorte("Corte", mensagem);
      }
    });
  }


  function cortar(id) {

  
    $.ajax({
      url: '<?= base_url('public/caminho_desenho') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno ÃƒÂ© em formato JSON
      async: true,
      data: {
        id: id
      },
      success: function(response) {
        if (response && response.ok === false) {
          mostrarErroOperacaoCorte("Corte", response.mensagem || "Nao foi possivel iniciar o corte.");
          lista(true);
          return;
        }

        if (response && response.caminho) {
          mostrarCaminhoArquivoCorte(response.caminho, "Caminho do arquivo");
        }

        abrir_cort(id, false);
      },
      error: function(xhr) {
        var mensagem = "Nao foi possivel iniciar o corte.";
        if (xhr && xhr.responseJSON && xhr.responseJSON.mensagem) {
          mensagem = xhr.responseJSON.mensagem;
        }
        mostrarErroOperacaoCorte("Corte", mensagem);
        lista(true);
      }
    });
  }

  window.wlModalProjetoState = window.wlModalProjetoState || {
    listaId: null,
    projetoId: null,
    accept: '',
    descricao: '',
    totalArquivos: 0,
    arquivosBaixados: 0,
    reabrirAposVisualizador: false
  };

  function obterMarcacaoArquivosProjetoAtual() {
    var tabela = document.getElementById("modal-download-list");
    var total = 0;
    var marcados = 0;
    if (!tabela) {
      return { total: 0, marcados: 0, todos: false };
    }

    var checks = tabela.querySelectorAll('input[type="checkbox"]');
    total = checks.length;
    checks.forEach(function(check) {
      if (check.checked) {
        marcados++;
      }
    });

    return {
      total: total,
      marcados: marcados,
      todos: total > 0 && marcados === total
    };
  }

  function atualizarBotaoFinalizarProjetoAtual(marcacao) {
    var botaoFinalizar = document.getElementById("modal-download-finish");
    if (!botaoFinalizar) {
      return;
    }

    marcacao = marcacao || obterMarcacaoArquivosProjetoAtual();
    botaoFinalizar.disabled = !marcacao.todos;
    botaoFinalizar.title = marcacao.todos
      ? "Finalizar projeto"
      : "Marque todos os arquivos para liberar a finalizacao.";
  }

  function atualizarRotuloBotaoProjetoAtual() {
    var estado = window.wlModalProjetoState || {};
    var listaId = estado.listaId;
    if (listaId === null || listaId === undefined) {
      return;
    }

    var marcacao = obterMarcacaoArquivosProjetoAtual();
    var total = marcacao.total;
    var baixados = marcacao.marcados;

    estado.totalArquivos = total;
    estado.arquivosBaixados = baixados;
    atualizarBotaoFinalizarProjetoAtual(marcacao);

    var rotulo = rotuloBotaoBaixarProjetoPorContagem(total, baixados);
    var listaIdTexto = String(listaId);

    document.querySelectorAll('button[onclick^="baixar("]').forEach(function(botao) {
      var onclick = botao.getAttribute("onclick") || "";
      var match = onclick.match(/baixar\(([^)]+)\)/);
      var idBotao = match && match[1] ? match[1].replace(/['"]/g, "").trim() : "";
      if (idBotao === listaIdTexto) {
        botao.textContent = rotulo;
      }
    });
  }

  function avisarOperacaoProjeto(titulo, mensagem) {
    if (typeof alert_personalizado === "function") {
      alert_personalizado(titulo, mensagem);
      return;
    }

    window.alert(mensagem);
  }

  function garantirModalArquivosProjeto() {
    if (!document.getElementById("wl-modal-close-style")) {
      var style = document.createElement("style");
      style.id = "wl-modal-close-style";
      style.textContent = `
        .wl-modal-close {
          border: 1px solid #d1d5db;
          background: #fff;
          color: #334155;
          width: 32px;
          height: 32px;
          border-radius: 6px;
          font-size: 18px;
          line-height: 1;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          cursor: pointer;
          padding: 0;
        }
        .wl-modal-close:hover {
          background: #f1f5f9;
          color: #0f172a;
        }
        .wl-project-file-name {
          font-weight: 600;
          color: #0f172a;
        }
        .wl-project-file-status {
          display: block;
          font-size: .78rem;
          color: #64748b;
          margin-top: .15rem;
        }
      `;
      document.head.appendChild(style);
    }

    var modal = document.getElementById("modal-download");
    if (modal) {
      return modal;
    }

    modal = document.createElement("div");
    modal.className = "modal fade";
    modal.id = "modal-download";
    modal.tabIndex = -1;
    modal.setAttribute("role", "dialog");
    modal.innerHTML = `
      <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Arquivos do projeto</h5>
            <button type="button" class="wl-modal-close wl-close-download-modal" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Fechar">&times;</button>
          </div>
          <div class="modal-body">
            <div id="modal-download-loading" class="text-center py-4 d-none">
              <div class="spinner-border text-primary" role="status"></div>
              <p class="mb-0 mt-2 text-muted">Carregando arquivos...</p>
            </div>
            <div id="modal-download-empty" class="text-center py-4 text-muted d-none">
              Nenhum arquivo disponivel.
            </div>
            <div id="modal-download-table-wrap" class="table-responsive d-none">
              <table class="table table-sm table-hover mb-0">
                <thead>
                  <tr>
                    <th style="width:50px;">#</th>
                    <th>Arquivo</th>
                    <th class="text-center" style="width:260px;">Acoes</th>
                    <th class="text-center" style="width:110px;">Marcado</th>
                  </tr>
                </thead>
                <tbody id="modal-download-list"></tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" id="modal-download-add" onclick="abrirSeletorArquivosProjetoAtual()">Adicionar arquivos</button>
            <button type="button" class="btn btn-success" id="modal-download-finish" onclick="finalizarProjetoModalAtual()" disabled title="Marque todos os arquivos para liberar a finalizacao.">Finalizar</button>
            <button type="button" class="btn btn-secondary wl-close-download-modal" data-dismiss="modal" data-bs-dismiss="modal">Fechar</button>
          </div>
        </div>
      </div>
    `;

    document.body.appendChild(modal);
    modal.addEventListener("click", function(event) {
      var target = event.target;
      if (!target) {
        return;
      }
      if (target === modal) {
        fecharModalArquivosProjeto();
        return;
      }
      if (target.classList.contains("wl-close-download-modal") || target.closest(".wl-close-download-modal")) {
        event.preventDefault();
        fecharModalArquivosProjeto();
      }
    });

    return modal;
  }

  function fecharModalArquivosProjeto(callback) {
    var modalElement = document.getElementById("modal-download");
    var finalizado = false;
    var concluir = function() {
      if (finalizado) {
        return;
      }
      finalizado = true;
      if (typeof callback === "function") {
        callback();
      }
    };

    if (!modalElement) {
      concluir();
      return;
    }

    var aplicarFallback = function() {
      if (finalizado) {
        return;
      }

      modalElement.classList.remove("show");
      modalElement.style.display = "none";
      modalElement.setAttribute("aria-hidden", "true");
      document.body.classList.remove("modal-open");
      document.querySelectorAll(".modal-backdrop").forEach(function(item) {
        item.remove();
      });
      concluir();
    };

    if (window.bootstrap && window.bootstrap.Modal) {
      try {
        var instance = window.bootstrap.Modal.getInstance(modalElement) || new window.bootstrap.Modal(modalElement);
        modalElement.addEventListener("hidden.bs.modal", concluir, { once: true });
        instance.hide();
        setTimeout(aplicarFallback, 350);
        return;
      } catch (e) {}
    }

    if (window.jQuery && $.fn && $.fn.modal) {
      $('#modal-download').one('hidden.bs.modal', function() {
        concluir();
      });
      $('#modal-download').modal('hide');
      setTimeout(aplicarFallback, 350);
      return;
    }

    aplicarFallback();
  }

  function exibirModalArquivosProjetoExistente() {
    var modalElement = document.getElementById("modal-download");
    if (!modalElement) {
      return;
    }

    if (window.bootstrap && window.bootstrap.Modal) {
      try {
        var modalInstance = window.bootstrap.Modal.getInstance(modalElement) || new window.bootstrap.Modal(modalElement, {
          backdrop: true,
          keyboard: true
        });
        modalInstance.show();
        return;
      } catch (e) {}
    }

    if (window.jQuery && $.fn && $.fn.modal) {
      $('#modal-download').modal({
        backdrop: true,
        keyboard: true,
        show: true
      });
      return;
    }

    modalElement.style.display = "block";
    modalElement.classList.add("show");
    modalElement.removeAttribute("aria-hidden");
    document.body.classList.add("modal-open");
  }

  function reabrirModalArquivosProjetoSeNecessario() {
    if (!window.wlModalProjetoState || !window.wlModalProjetoState.reabrirAposVisualizador) {
      return;
    }

    window.wlModalProjetoState.reabrirAposVisualizador = false;
    setTimeout(function() {
      garantirModalArquivosProjeto();

      var listaArquivos = document.getElementById("modal-download-list");
      if ((!listaArquivos || !listaArquivos.children.length) && window.wlModalProjetoState.listaId) {
        atualizarEstadoModalArquivosProjeto("loading");
        carregarArquivosProjeto(window.wlModalProjetoState.listaId);
        return;
      }

      exibirModalArquivosProjetoExistente();
    }, 420);
  }

  if (!window.wlModalProjetoViewerReturnBound) {
    window.wlModalProjetoViewerReturnBound = true;
    window.reabrirModalArquivosProjetoSeNecessario = reabrirModalArquivosProjetoSeNecessario;
    document.addEventListener("wl:visualizador:fechado", reabrirModalArquivosProjetoSeNecessario);
  } else {
    window.reabrirModalArquivosProjetoSeNecessario = reabrirModalArquivosProjetoSeNecessario;
  }

  function atualizarEstadoModalArquivosProjeto(estado) {
    var loading = document.getElementById("modal-download-loading");
    var empty = document.getElementById("modal-download-empty");
    var tableWrap = document.getElementById("modal-download-table-wrap");
    var botaoAdicionar = document.getElementById("modal-download-add");
    var botaoFinalizar = document.getElementById("modal-download-finish");

    if (!loading || !empty || !tableWrap) {
      return;
    }

    loading.classList.add("d-none");
    empty.classList.add("d-none");
    tableWrap.classList.add("d-none");

    if (estado === "loading") {
      loading.classList.remove("d-none");
    } else if (estado === "empty" || estado === "error") {
      empty.classList.remove("d-none");
    } else if (estado === "list") {
      tableWrap.classList.remove("d-none");
    }

    if (botaoAdicionar) {
      botaoAdicionar.disabled = estado === "loading";
    }
    if (botaoFinalizar) {
      if (estado === "loading" || estado === "empty" || estado === "error") {
        botaoFinalizar.disabled = true;
        botaoFinalizar.title = "Marque todos os arquivos para liberar a finalizacao.";
      } else {
        atualizarBotaoFinalizarProjetoAtual();
      }
    }
  }

  function normalizarItensModalArquivosProjeto(response) {
    if (Array.isArray(response.itens)) {
      return response.itens;
    }

    var itens = [];
    if (!Array.isArray(response.arquivos)) {
      return itens;
    }

    response.arquivos.forEach(function(htmlLink, index) {
      var wrapper = document.createElement("div");
      wrapper.innerHTML = htmlLink || "";

      var botaoDownload = wrapper.querySelector('button[onclick*="buscarArquivos"]');
      var checkbox = wrapper.querySelector('input[type="checkbox"]');
      var id = index;
      var onclick = botaoDownload ? (botaoDownload.getAttribute("onclick") || "") : "";
      var matchId = onclick.match(/\((\d+)\)/);

      if (matchId && matchId[1] !== undefined) {
        id = parseInt(matchId[1], 10);
      }

      itens.push({
        id: id,
        nome: botaoDownload ? botaoDownload.textContent.trim() : ("Arquivo " + (index + 1)),
        marcado: checkbox ? checkbox.checked : false,
        status: "",
        removivel: false,
        remover_bloqueio: "Somente arquivos pendentes podem ser removidos do projeto."
      });
    });

    return itens;
  }

  function configurarContextoModalArquivosProjeto(id, response) {
    window.wlModalProjetoState.listaId = id;
    window.wlModalProjetoState.projetoId = response && response.projeto_id ? response.projeto_id : id;
    window.wlModalProjetoState.accept = response && response.accept ? response.accept : "";
    window.wlModalProjetoState.descricao = response && response.descricao ? response.descricao : "";
    window.wlModalProjetoState.totalArquivos = response && response.total ? parseInt(response.total, 10) || 0 : 0;
    window.wlModalProjetoState.arquivosBaixados = 0;
  }

  function recarregarArquivosProjetoAtual() {
    var state = window.wlModalProjetoState || {};
    var id = state.listaId || state.projetoId;
    if (!id) {
      return;
    }

    carregarArquivosProjeto(id);
  }

  function preencherModalArquivosProjeto(itens) {
    var tabela = document.getElementById("modal-download-list");
    var titulo = document.querySelector("#modal-download .modal-title");
    if (!tabela) {
      return;
    }

    tabela.innerHTML = "";
    window.wlModalProjetoState.totalArquivos = itens.length;
    window.wlModalProjetoState.arquivosBaixados = itens.filter(function(item) {
      return !!item.marcado;
    }).length;

    if (titulo) {
      titulo.textContent = window.wlModalProjetoState.descricao
        ? "Arquivos do projeto: " + window.wlModalProjetoState.descricao + " (" + itens.length + ")"
        : "Arquivos do projeto (" + itens.length + ")";
    }

    itens.forEach(function(item, index) {
      var row = document.createElement("tr");

      var colIndice = document.createElement("td");
      colIndice.textContent = String(index + 1).padStart(2, "0");
      row.appendChild(colIndice);

      var colNome = document.createElement("td");
      var nomePrincipal = document.createElement("span");
      nomePrincipal.className = "wl-project-file-name";
      nomePrincipal.textContent = item.nome || "Arquivo sem nome";
      colNome.appendChild(nomePrincipal);

      if (item.status) {
        var statusSecundario = document.createElement("small");
        statusSecundario.className = "wl-project-file-status";
        statusSecundario.textContent = "Status: " + item.status;
        colNome.appendChild(statusSecundario);
      }

      row.appendChild(colNome);

      var colAcoes = document.createElement("td");
      colAcoes.className = "text-center";

      var btnVisualizar = document.createElement("button");
      btnVisualizar.type = "button";
      btnVisualizar.className = "btn btn-outline-info btn-sm mr-1";
      btnVisualizar.textContent = "Ver";
      btnVisualizar.addEventListener("click", function() {
        window.wlModalProjetoState.reabrirAposVisualizador = true;
        fecharModalArquivosProjeto(function() {
          ver_dxf_projeto(String(item.id));
        });
      });

      var btnDownload = document.createElement("button");
      btnDownload.type = "button";
      btnDownload.className = "btn btn-outline-primary btn-sm";
      btnDownload.textContent = "Baixar";
      btnDownload.addEventListener("click", function() {
        buscarArquivos(String(item.id));
      });

      var btnRemover = document.createElement("button");
      btnRemover.type = "button";
      btnRemover.className = "btn btn-outline-danger btn-sm ml-1";
      btnRemover.textContent = "Remover";
      btnRemover.disabled = !item.removivel;
      btnRemover.title = item.removivel ? "Remover arquivo do projeto" : (item.remover_bloqueio || "Somente arquivos pendentes podem ser removidos do projeto.");
      if (item.removivel) {
        btnRemover.addEventListener("click", function() {
          removerArquivoProjeto(String(item.id));
        });
      }

      colAcoes.appendChild(btnVisualizar);
      colAcoes.appendChild(btnDownload);
      colAcoes.appendChild(btnRemover);
      row.appendChild(colAcoes);

      var colMarcado = document.createElement("td");
      colMarcado.className = "text-center";
      var check = document.createElement("input");
      check.type = "checkbox";
      check.checked = !!item.marcado;
      check.addEventListener("change", function() {
        atualizarRotuloBotaoProjetoAtual();
        marcarArquivos(String(item.id));
      });
      colMarcado.appendChild(check);
      row.appendChild(colMarcado);

      tabela.appendChild(row);
    });

    atualizarRotuloBotaoProjetoAtual();
  }

  function carregarArquivosProjeto(id) {
    exibirModalArquivosProjetoExistente();
    var state = window.wlModalProjetoState || {};
    var projetoIdAtual = state.projetoId || "";

    $.ajax({
      url: '<?= base_url('public/baixar_projeto') ?>',
      type: "POST",
      dataType: "json",
      data: {
        id: id,
        projeto_id: projetoIdAtual
      },
      success: function(response) {
        configurarContextoModalArquivosProjeto(id, response || {});
        var itens = normalizarItensModalArquivosProjeto(response || {});
        if (!itens.length) {
          atualizarEstadoModalArquivosProjeto("empty");
          return;
        }

        preencherModalArquivosProjeto(itens);
        atualizarEstadoModalArquivosProjeto("list");
      },
      error: function() {
        var empty = document.getElementById("modal-download-empty");
        if (empty) {
          empty.textContent = "Erro ao buscar os arquivos.";
        }
        atualizarEstadoModalArquivosProjeto("error");
      }
    });
  }

  function baixar(id) {
    garantirModalArquivosProjeto();
    window.wlModalProjetoState.listaId = id;
    window.wlModalProjetoState.projetoId = null;
    window.wlModalProjetoState.accept = "";
    window.wlModalProjetoState.descricao = "";
    atualizarEstadoModalArquivosProjeto("loading");
    carregarArquivosProjeto(id);
  }

  function abrirSeletorArquivosProjetoAtual() {
    var projetoId = window.wlModalProjetoState.projetoId || window.wlModalProjetoState.listaId;
    if (!projetoId) {
      avisarOperacaoProjeto("Projeto", "Projeto nao encontrado para adicionar arquivos.");
      return;
    }

    $.ajax({
      url: '<?= base_url('public/preparar_adicionar_projeto') ?>',
      type: "POST",
      dataType: "json",
      data: {
        id: window.wlModalProjetoState.listaId || projetoId,
        projeto_id: projetoId
      },
      success: function(response) {
        if (!response || response.ok !== 'true') {
          avisarOperacaoProjeto("Projeto", response && response.mensagem ? response.mensagem : "Nao foi possivel preparar o projeto para receber arquivos.");
          return;
        }

        window.wlModalProjetoState.projetoId = response.projeto_id || projetoId;
        window.wlModalProjetoState.accept = response.accept || window.wlModalProjetoState.accept || "";

        var input = document.createElement("input");
        input.type = "file";
        input.multiple = true;
        if (window.wlModalProjetoState.accept) {
          input.accept = window.wlModalProjetoState.accept;
        }
        input.addEventListener("change", function() {
          enviarArquivosProjetoSelecionados(window.wlModalProjetoState.projetoId || projetoId, input.files);
        });
        input.click();
      },
      error: function(xhr) {
        var mensagem = "Nao foi possivel preparar o projeto para receber arquivos.";
        if (xhr && xhr.responseJSON && xhr.responseJSON.mensagem) {
          mensagem = xhr.responseJSON.mensagem;
        }
        avisarOperacaoProjeto("Projeto", mensagem);
      }
    });
  }

  function finalizarProjetoModalAtual() {
    var state = window.wlModalProjetoState || {};
    var listaId = state.listaId;
    if (listaId === null || listaId === undefined || listaId === "") {
      avisarOperacaoProjeto("Projeto", "Projeto nao encontrado para finalizar.");
      return;
    }

    if (!obterMarcacaoArquivosProjetoAtual().todos) {
      avisarOperacaoProjeto("Projeto", "Marque todos os arquivos antes de finalizar.");
      atualizarBotaoFinalizarProjetoAtual();
      return;
    }

    var descricao = state.descricao ? ": " + state.descricao : "";
    fecharModalArquivosProjeto(function() {
      confirmar_ind(String(listaId), descricao);
    });
  }

  function enviarArquivosProjetoSelecionados(projetoId, files) {
    if (!files || !files.length) {
      return;
    }

    var botaoAdicionar = document.getElementById("modal-download-add");
    var textoOriginal = botaoAdicionar ? botaoAdicionar.textContent : "";
    if (botaoAdicionar) {
      botaoAdicionar.disabled = true;
      botaoAdicionar.textContent = "Enviando...";
    }

    var indiceAtual = 0;
    var falhas = [];

    function finalizarEnvio() {
      if (botaoAdicionar) {
        botaoAdicionar.disabled = false;
        botaoAdicionar.textContent = textoOriginal || "Adicionar arquivos";
      }

      atualizarEstadoModalArquivosProjeto("loading");
      recarregarArquivosProjetoAtual();
      if (typeof lista === "function") {
        lista(true);
      }

      if (falhas.length) {
        avisarOperacaoProjeto("Projeto", falhas.join("\n"));
      }
    }

    function enviarProximo() {
      if (indiceAtual >= files.length) {
        finalizarEnvio();
        return;
      }

      var formData = new FormData();
      formData.append("file", files[indiceAtual]);

      $.ajax({
        url: '<?= site_url('public/desenho_adicionar_projeto') ?>',
        type: "POST",
        dataType: "json",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          if (!response || response.ok !== 'true') {
            falhas.push((files[indiceAtual] && files[indiceAtual].name ? files[indiceAtual].name + ": " : "") + ((response && response.mensagem) || "Falha ao adicionar o arquivo ao projeto."));
          }
          indiceAtual++;
          enviarProximo();
        },
        error: function(xhr) {
          var mensagem = "Falha ao adicionar o arquivo ao projeto.";
          if (xhr && xhr.responseJSON && xhr.responseJSON.mensagem) {
            mensagem = xhr.responseJSON.mensagem;
          }
          falhas.push((files[indiceAtual] && files[indiceAtual].name ? files[indiceAtual].name + ": " : "") + mensagem);
          indiceAtual++;
          enviarProximo();
        }
      });
    }

    enviarProximo();
  }
  function buscarArquivos(id) {
    // Cria formulÃƒÂ¡rio oculto
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= base_url('public/baixar_arquivo') ?>';

    // Input com o ID
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'id';
    input.value = id;

    // Adiciona ao DOM e envia
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
    form.remove();
  }

function marcarArquivos(id) {


          $.ajax({
        url: '<?= base_url('public/marcar_arquivo') ?>',
        type: "POST",
        dataType: "json", // Indicar que o retorno ÃƒÂ© em formato JSON
        data: {
          id: id
        },
        success: function(response) {
          if (response && response.ok === false) {
            avisarOperacaoProjeto("Projeto", response.msg || "Nao foi possivel atualizar o marcador do arquivo.");
            if (window.wlModalProjetoState && window.wlModalProjetoState.listaId) {
              recarregarArquivosProjetoAtual();
            }
          }
        },
        error: function(xhr) {
          var mensagem = "Nao foi possivel atualizar o marcador do arquivo.";
          if (xhr && xhr.responseJSON && xhr.responseJSON.msg) {
            mensagem = xhr.responseJSON.msg;
          }
          avisarOperacaoProjeto("Projeto", mensagem);
          if (window.wlModalProjetoState && window.wlModalProjetoState.listaId) {
            recarregarArquivosProjetoAtual();
          }
        }
      });
  }

  function removerArquivoProjeto(id) {
    if (!mostrarConfirmacao("Remover este arquivo do projeto?")) {
      return;
    }

    $.ajax({
      url: '<?= base_url('public/remover_arquivo_projeto') ?>',
      type: "POST",
      dataType: "json",
      data: {
        id: id
      },
      success: function(response) {
        if (!response || response.ok !== true) {
          avisarOperacaoProjeto("Projeto", response && response.mensagem ? response.mensagem : "Nao foi possivel remover o arquivo do projeto.");
          return;
        }

        if (response.projeto_removido) {
          fecharModalArquivosProjeto(function() {
            lista(true);
          });
          return;
        }

        if (response.projeto_id) {
          window.wlModalProjetoState.projetoId = response.projeto_id;
        }
        atualizarEstadoModalArquivosProjeto("loading");
        recarregarArquivosProjetoAtual();
        if (typeof lista === "function") {
          lista(true);
        }
      },
      error: function(xhr) {
        var mensagem = "Nao foi possivel remover o arquivo do projeto.";
        if (xhr && xhr.responseJSON && xhr.responseJSON.mensagem) {
          mensagem = xhr.responseJSON.mensagem;
        }
        avisarOperacaoProjeto("Projeto", mensagem);
      }
    });
  }



    function tentarAbrirPdfImpressao(pdfBase64) {
    if (!pdfBase64 || typeof pdfBase64 !== 'string') {
      return;
    }

    try {
      var pdfContent = atob(pdfBase64);
      var byteCharacters = pdfContent;
      var byteNumbers = new Array(byteCharacters.length);
      for (var i = 0; i < byteCharacters.length; i++) {
        byteNumbers[i] = byteCharacters.charCodeAt(i);
      }
      var byteArray = new Uint8Array(byteNumbers);
      var blob = new Blob([byteArray], { type: 'application/pdf' });
      var blobUrl = URL.createObjectURL(blob);

      var printWindow = window.open(blobUrl, '_blank');
      if (printWindow && typeof printWindow.focus === 'function') {
        printWindow.focus();
        setTimeout(function () {
          try {
            if (typeof printWindow.print === 'function') {
              printWindow.print();
            }
          } catch (e) {}
        }, 250);
      }

      setTimeout(function () {
        try {
          URL.revokeObjectURL(blobUrl);
        } catch (e) {}
      }, 20000);
    } catch (e) {
      console.warn('Falha ao abrir/imprimir PDF:', e);
    }
  }

  function escolherModoFinalizacaoArteFinal(response, callback) {
    var dependencia = response && response.dependencia ? String(response.dependencia) : "proxima etapa";
    var texto = "Existe uma proxima etapa configurada (" + dependencia + "). Escolha como deseja finalizar a Arte Final.";

    if (window.Swal && typeof window.Swal.fire === "function") {
      window.Swal.fire({
        title: "Finalizar Arte Final",
        text: texto,
        icon: "question",
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: "Finalizar direto",
        denyButtonText: "Criar continuacao",
        cancelButtonText: "Cancelar"
      }).then(function(result) {
        if (result.isConfirmed) {
          callback("finalizar_direto");
        } else if (result.isDenied) {
          callback("criar_continuacao");
        }
      });
      return;
    }

    if (window.confirm(texto + "\n\nClique em OK para criar a continuacao.")) {
      callback("criar_continuacao");
      return;
    }

    if (window.confirm("Finalizar direto sem criar continuacao?")) {
      callback("finalizar_direto");
    }
  }

  function confirmar_ind(id, nome, modoFinalizacao) {
    if (modoFinalizacao || mostrarConfirmacao("Finalizar Projeto" + nome)) {
      $.ajax({
        url: '<?= base_url('public/confirmar_projeto') ?>',
        type: "POST",
        dataType: "json",
        data: {
          id: id,
          modo_finalizacao: modoFinalizacao || ""
        },
        success: function(response) {
          var dependenciaAtivada = false;
          var aguardandoEscolha = false;
          try {
            if (response && response.ok === false) {
              var mensagemErro = response.mensagem || "Nao foi possivel finalizar o projeto.";
              if (typeof alert_personalizado === "function") {
                alert_personalizado("Finalizar", mensagemErro);
              } else {
                window.alert(mensagemErro);
              }
              return;
            }

            if (response && response.requer_escolha_finalizacao) {
              aguardandoEscolha = true;
              escolherModoFinalizacaoArteFinal(response, function(modoEscolhido) {
                confirmar_ind(id, nome, modoEscolhido);
              });
              return;
            }

            if (response && response.dependencia) {
              var tipoDependencia = String(response.tipo || "").trim().toLowerCase();
              if (tipoDependencia == "ind") {
                add_arquivo_ind(response.dependencia);
              } else {
                add_arquivo_mult(response.dependencia);
              }
              dependenciaAtivada = true;
            }
            if (!dependenciaAtivada) {
              tentarAbrirPdfImpressao(response ? response.pdf : null);
            }
          } catch (e) {
            console.error('Erro no finalizar projeto:', e);
            if (typeof alert_personalizado === "function") {
              alert_personalizado("Finalizar", "Houve um erro ao abrir o modal da dependencia. Recarregue a tela e tente novamente.");
            }
            dependenciaAtivada = false;
          } finally {
            // Nao recarrega a grade aqui quando ha dependencia, porque o modal
            // de upload mora dentro da propria lista e seria destruido.
            if (!dependenciaAtivada && !aguardandoEscolha) {
              lista(true);
            }
          }
        },
        error: function(xhr, status, error) {
          console.error('Erro AJAX ao finalizar projeto:', status, error);
          lista(true);
        }
      });
    }
  }

  function confirmar(id, nome) {
    if (mostrarConfirmacao("Confirmar corte do desenho: " + nome)) {
      $.ajax({
        url: '<?= base_url('public/confirmar_corte') ?>',
        type: "POST",
        dataType: "json",
        data: {
          id: id
        },
        success: function(response) {
          try {
            tentarAbrirPdfImpressao(response ? response.pdf : null);
          } catch (e) {
            console.error('Erro no confirmar corte:', e);
          } finally {
            lista(true);
          }
        },
        error: function(xhr, status, error) {
          console.error('Erro AJAX ao confirmar corte:', status, error);
          lista(true);
        }
      });
    }
  }

  function abrir_cort(id, mostrarCaminho) {
      if (mostrarCaminho === undefined) {
        mostrarCaminho = true;
      }


      $.ajax({
        url: '<?= base_url('public/enviar_para_lista_corte') ?>',
        type: "POST",
        dataType: "json", // Indicar que o retorno ÃƒÂ© em formato JSON
        data: {
          id: id
        },
        success: function(response) {
          if (response && response.caminho && mostrarCaminho) {
            mostrarCaminhoArquivoCorte(response.caminho, "Caminho do arquivo");
          }

          if (response && response.status === false) {
            mostrarErroOperacaoCorte("Corte", response.msg || "Nao foi possivel abrir o desenho na maquina.");
          }

          lista(true);
        },
        error: function(xhr) {
          var mensagem = "Nao foi possivel abrir o desenho na maquina.";
          if (xhr && xhr.responseJSON && xhr.responseJSON.msg) {
            mensagem = xhr.responseJSON.msg;
          }
          mostrarErroOperacaoCorte("Corte", mensagem);
          lista(true);
        }
      });
    
  }
  
  function mostrarConfirmacao(texto = '') {
    // Exibe a caixa de diÃƒÂ¡logo de confirmaÃƒÂ§ÃƒÂ£o e armazena a resposta em uma variÃƒÂ¡vel
    var resposta = window.confirm(texto);
    // Verifica a resposta e faz algo com ela
    return resposta;

  }

  function notificarCancelamentoCorte(tipo, titulo, mensagem) {
    var tipoToast = tipo === "success" ? "success" : "error";
    var tituloFinal = titulo || (tipoToast === "success" ? "Cancelamento" : "Erro");
    var mensagemFinal = String(mensagem || "").trim();

    if (window.toastr && typeof window.toastr[tipoToast] === "function") {
      window.toastr.options = window.toastr.options || {};
      window.toastr.options.timeOut = tipoToast === "success" ? 5000 : 9000;
      window.toastr.options.extendedTimeOut = 2500;
      window.toastr.options.closeButton = true;
      window.toastr.options.progressBar = true;
      window.toastr.options.preventDuplicates = false;
      window.toastr[tipoToast](mensagemFinal, tituloFinal);
      return;
    }

    if (window.Swal && typeof window.Swal.fire === "function") {
      window.Swal.fire({
        icon: tipoToast === "success" ? "success" : "error",
        title: tituloFinal,
        text: mensagemFinal
      });
      return;
    }

    window.alert(tituloFinal + "\n" + mensagemFinal);
  }

  function solicitarJustificativaCancelamentoCorte(nome) {
    var nomeLimpo = String(nome || "").trim();
    var titulo = nomeLimpo ? ("Cancelar corte de " + nomeLimpo) : "Cancelar corte";
    var mensagem = "Informe a justificativa com pelo menos 15 caracteres.";

    if (window.Swal && typeof window.Swal.fire === "function") {
      return window.Swal.fire({
        icon: "warning",
        title: titulo,
        input: "textarea",
        inputLabel: "Justificativa",
        inputPlaceholder: "Descreva o motivo do cancelamento...",
        inputAttributes: {
          "aria-label": "Justificativa do cancelamento",
          maxlength: "4000"
        },
        inputValidator: function(value) {
          var texto = String(value || "").trim();
          if (texto.length < 15) {
            return mensagem;
          }
          return null;
        },
        showCancelButton: true,
        confirmButtonText: "Cancelar corte",
        cancelButtonText: "Fechar",
        reverseButtons: true
      }).then(function(result) {
        if (!result || !result.isConfirmed) {
          return "";
        }

        return String(result.value || "").trim();
      });
    }

    return new Promise(function(resolve) {
      while (true) {
        var valor = window.prompt(titulo + "\n" + mensagem, "");
        if (valor === null) {
          resolve("");
          return;
        }

        var texto = String(valor || "").trim();
        if (texto.length >= 15) {
          resolve(texto);
          return;
        }

        window.alert(mensagem);
      }
    });
  }

  function cancelar_corte(id, nome) {
    solicitarJustificativaCancelamentoCorte(nome).then(function(justificativa) {
      if (!justificativa) {
        return;
      }

      $.ajax({
        url: '<?= base_url('public/cancelar_corte') ?>',
        type: "POST",
        dataType: "json",
        data: {
          id: id,
          justificativa: justificativa
        },
        success: function(response) {
          if (response && response.ok) {
            notificarCancelamentoCorte("success", "Corte", response.mensagem || "Corte cancelado com sucesso.");
          } else {
            notificarCancelamentoCorte("error", "Corte", response && response.mensagem ? response.mensagem : "Nao foi possivel cancelar o corte.");
          }
          lista(true);
        },
        error: function(xhr) {
          var mensagem = "Nao foi possivel cancelar o corte.";
          if (xhr && xhr.responseJSON && xhr.responseJSON.mensagem) {
            mensagem = xhr.responseJSON.mensagem;
          }
          notificarCancelamentoCorte("error", "Corte", mensagem);
          lista(true);
        }
      });
    });
  }



  function adicionarPesquisa(finalidade = '') {
    const topLista = document.getElementById("top-lista");
    if (!topLista) {
      return;
    }

    // Remove uma barra de pesquisa anterior (se existir)
    const pesquisaExistente = document.getElementById("container-pesquisa");
    if (pesquisaExistente) {
      pesquisaExistente.remove();
    }

    // Criar o container para organizar os elementos
    const container = document.createElement("div");
    container.id = "container-pesquisa"; // ID para identificar a barra de pesquisa
    container.className = "wl-filtro-finalidade";

    // Criar o label
    const label = document.createElement("label");
    label.className = "form-label";
    label.setAttribute("for", "finalidade_pesquisa");
    label.innerText = "Pesquisar por finalidade:";

    // Criar o select (vazio por enquanto, serÃƒÂ¡ preenchido pelo AJAX)
    const selectFiltro = document.createElement("select");
    selectFiltro.id = "finalidade_pesquisa";
    selectFiltro.className = "form-select form-select-sm custom-select";




    // Criar o botÃƒÂ£o de pesquisa
    const botaoPesquisa = document.createElement("button");
    botaoPesquisa.innerText = "Pesquisar";
    botaoPesquisa.className = "btn btn-primary btn-sm";
    botaoPesquisa.onclick = function() {
      finalidade_pesquisa = document.getElementById("finalidade_pesquisa").value;
      lista(true);
    };

    // Adicionar elementos no container
    container.appendChild(label);
    container.appendChild(selectFiltro);
    container.appendChild(botaoPesquisa);

    // Inserir o container como primeiro elemento da div "top-lista"
    if (topLista.firstChild) {
      topLista.insertBefore(container, topLista.firstChild);
    } else {
      topLista.appendChild(container);
    }

    // Fazer requisiÃƒÂ§ÃƒÂ£o AJAX para buscar os finalidades
    $.ajax({
      url: '<?= base_url('public/finalidade_lista') ?>',
      type: "POST",
      dataType: "json",
      success: function(response) {
        // Limpa as opÃƒÂ§ÃƒÂµes atuais do select
        $('#finalidade_pesquisa').empty();

        // Adiciona uma opÃƒÂ§ÃƒÂ£o padrÃƒÂ£o
        $('#finalidade_pesquisa').append('<option value="">finalidade</option>');

        // Adiciona cada finalidade retornado no select
        $.each(response.lista, function(index, item) {
          $('#finalidade_pesquisa').append('<option value="' + item.finalidade + '">' + item.finalidade + '</option>');
        });


        document.getElementById("finalidade_pesquisa").value = finalidade;
      },
      error: function(xhr, status, error) {
        console.error("Ocorreu um erro ao carregar os dados: ", error);
      }
    });
  }
</script>






<!-- <script type="module">
  // Importa THREE em modo mÃƒÂ³dulo
  import * as THREE from 'https://unpkg.com/three@0.149.0/build/three.module.js';

  // Importa DXFViewer do main.js da lib
  import { DXFViewer } from '<?= base_url('public/assets/dxf-viewer/main.js'); ?>';

  // ================= VISUALIZAÃƒâ€¡ÃƒÆ’O DXF =================

  // chamada usada no botÃƒÂ£o: ver_dxf(ID)
  window.ver_dxf = function(id) {
    garantirModal();

    $('#modal-dxf-viewer').modal('show');
    const titleElement = document.getElementById('modal-dxf-title');
    const container    = document.getElementById('dxf-viewer-container');
    
    titleElement.textContent = 'Carregando desenho...';
    container.innerHTML = `
      <div class="d-flex justify-content-center align-items-center h-100">
        <div class="spinner-border text-light" role="status">
          <span class="sr-only">Loading...</span>
        </div>
      </div>`;

    $.ajax({
      url: '<?= base_url('public/ver_desenho') ?>',
      type: 'POST',
      dataType: 'json',
      data: { id: id },
      success: function(response) {
        if (!response || response.status === false || !response.dxf) {
          titleElement.textContent = (response && response.msg) || 'Erro ao carregar DXF';
          container.innerHTML = '';
          return;
        }

        titleElement.textContent = 'Visualizando: ' + (response.nome || id);
        renderizarDXF(response.dxf);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.error("Erro na requisiÃƒÂ§ÃƒÂ£o AJAX:", textStatus, errorThrown);
        titleElement.textContent = 'Erro de comunicaÃƒÂ§ÃƒÂ£o ao buscar o DXF';
        container.innerHTML = '';
      }
    });
  };


  // ================= MODAL =================
function garantirModal() {
  if (document.getElementById('modal-dxf-viewer')) return;

  const html = `
  <div class="modal fade" id="modal-dxf-viewer" tabindex="-1">
    <div class="modal-dialog modal-lg" style="max-width:80%">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modal-dxf-title">Visualizador DXF</h5>
          <button class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" style="padding:0">
          <div id="dxf-viewer-container" style="width:100%;height:60vh;background:#111"></div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>`;
  
  document.body.insertAdjacentHTML('beforeend', html);
}



  // ================= RENDERIZAÃƒâ€¡ÃƒÆ’O COM three-dxf-viewer =================
 async function renderizarDXF(dxfBase64) {
  const container    = document.getElementById('dxf-viewer-container');
  const titleElement = document.getElementById('modal-dxf-title');
  let modelUrl = null;

  // usa o mesmo caminho de fonte que jÃƒÂ¡ estava funcionando aÃƒÂ­
  const fontUrl = '<?= base_url('public/assets/dxf-viewer/fonts/helvetiker_regular.typeface.json'); ?>';

  try {
    container.innerHTML = '';

    // base64 -> Blob -> URL
    const byteCharacters = atob(dxfBase64);
    const byteNumbers = new Array(byteCharacters.length);
    for (let i = 0; i < byteCharacters.length; i++) {
      byteNumbers[i] = byteCharacters.charCodeAt(i);
    }
    const byteArray = new Uint8Array(byteNumbers);
    const blob = new Blob([byteArray], { type: "application/dxf" });
    modelUrl = URL.createObjectURL(blob);

    // DXF -> THREE.Object3D
    const viewer   = new DXFViewer();
    const dxfObject = await viewer.getFromPath(modelUrl, fontUrl);

    if (!dxfObject) {
      throw new Error('DXFViewer retornou objeto nulo. Verifique DXF e fonte.');
    }

    // --- monta cena three.js ---
    const width  = container.clientWidth  || container.offsetWidth || 800;
    const height = container.clientHeight || 500;

    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(width, height);
    renderer.setPixelRatio(window.devicePixelRatio || 1);
    container.appendChild(renderer.domElement);

    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0x111111);

    // adiciona objeto na cena
    scene.add(dxfObject);

    // luz bÃƒÂ¡sica
    const ambient = new THREE.AmbientLight(0xffffff, 0.8);
    scene.add(ambient);
    const dir = new THREE.DirectionalLight(0xffffff, 0.6);
    dir.position.set(0, 0, 1);
    scene.add(dir);

    // ---------- CENTRALIZAÃƒâ€¡ÃƒÆ’O ----------
    // pega bounding sphere do modelo
    const bbox    = new THREE.Box3().setFromObject(dxfObject);
    const sphere  = bbox.getBoundingSphere(new THREE.Sphere());
    const center  = sphere.center.clone();
    const radius  = sphere.radius || 1;

    // move o objeto para o centro (0,0,0)
    dxfObject.position.sub(center);

    // cÃƒÂ¢mera
    const fov    = 45;
    const aspect = width / height;
    const camera = new THREE.PerspectiveCamera(fov, aspect, radius / 100, radius * 20);

    // calcula distÃƒÂ¢ncia para caber tudo no FOV
    const fovRad   = fov * Math.PI / 180;
    let distance   = radius / Math.sin(fovRad / 2);
    distance      *= 1.2; // margem

    camera.position.set(0, 0, distance);
    camera.lookAt(0, 0, 0);

    // animaÃƒÂ§ÃƒÂ£o
    function animate() {
      requestAnimationFrame(animate);
      renderer.render(scene, camera);
    }
    animate();

    // responsivo dentro do modal
    window.addEventListener('resize', () => {
      const w = container.clientWidth  || container.offsetWidth || width;
      const h = container.clientHeight || height;
      camera.aspect = w / h;
      camera.updateProjectionMatrix();
      renderer.setSize(w, h);
    });

  } catch (e) {
    console.error('Erro ao renderizar o DXF:', e);
    titleElement.textContent = 'Erro ao renderizar o desenho';
    container.innerHTML = `
      <div class="alert alert-danger m-3">
        <strong>Ocorreu um erro:</strong><br>${e.message}
      </div>`;
  } finally {
    if (modelUrl) {
      URL.revokeObjectURL(modelUrl);
    }
  }
}

</script> -->
<!-- Caminho base para os arquivos do ViewSTL (usado por algumas libs) -->
<script>
  const VIEWSTL_BASE = "<?= base_url('public/assets/viewstl/'); ?>";
  window.stl_viewer_script_path = VIEWSTL_BASE;
</script>

<!-- THREE + complementos do pacote ViewSTL (r122) -->
<script src="<?= base_url('public/assets/viewstl/three.min.js'); ?>"></script>
<script src="<?= base_url('public/assets/viewstl/Projector.js'); ?>"></script>
<script src="<?= base_url('public/assets/viewstl/CanvasRenderer.js'); ?>"></script>
<script src="<?= base_url('public/assets/viewstl/TrackballControls.js'); ?>"></script>
<script src="<?= base_url('public/assets/viewstl/webgl_detector.js'); ?>"></script>
<script src="<?= base_url('public/assets/viewstl/ie_polyfills.js'); ?>"></script>
<script src="<?= base_url('public/assets/viewstl/OrbitControls.js'); ?>"></script>
<script src="<?= base_url('public/assets/viewstl/load_stl.min.js'); ?>"></script>

<!-- DXF viewer: UMD + mÃƒÂ³dulo ES que expÃƒÂµe window.DXFViewer -->
<script src="<?= base_url('public/assets/dxf-viewer/main.umd.cjs'); ?>"></script>
<script type="module">
  import { DXFViewer as DXFViewerClass } from "<?= base_url('public/assets/dxf-viewer/main.js'); ?>";
  window.DXFViewer = DXFViewerClass;
</script>

<!-- Viewer STL do exemplo + wrapper STL -->
<script src="<?= base_url('public/assets/viewstl/stl_viewer.min.js'); ?>"></script>
<script src="<?= base_url('public/assets/viewstl/stl_viewer.js'); ?>"></script>

<!-- Wrapper DXF (usa window.DXFViewer + window.THREE) -->
<script src="<?= base_url('public/assets/dxf-viewer/dxf_viewer.js'); ?>"></script>

<!-- Orquestrador: modal + AJAX + switch DXF/STL -->
<script src="<?= base_url('public/assets/visualizar.js?v=20260430_01'); ?>"></script>
<script>
  configurarVisualizador({
    base_url: "<?= base_url(); ?>",      // ex: https://wl.ngrok.dev/codi
    endpoint: "public/ver_desenho"       // seu endpoint atual
  });
</script>




