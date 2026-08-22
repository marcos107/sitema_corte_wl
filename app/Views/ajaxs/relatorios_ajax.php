<script>
var processos = [];
var processo_nome = '';
var cadastrar_glob = false;
var periodoAlteradoManualmente = false;
var aplicandoPresetPeriodo = false;
var modalSemDadosInstance = null;
var data_glob = {
  lista: {},
  id_groups: {}
};

function get_radio() {
  var radios = document.getElementsByName('processo');
  for (var i = 0; i < radios.length; i++) {
    if (radios[i].checked) {
      return radios[i];
    }
  }
  return null;
}

function formatDateInput(dateObj) {
  if (!(dateObj instanceof Date) || isNaN(dateObj.getTime())) {
    return '';
  }

  var year = dateObj.getFullYear();
  var month = String(dateObj.getMonth() + 1).padStart(2, '0');
  var day = String(dateObj.getDate()).padStart(2, '0');
  return year + '-' + month + '-' + day;
}

function parseDateInput(value) {
  if (!value) {
    return null;
  }

  var normalizedValue = String(value).trim().replace(' ', 'T');
  var parsedDate = new Date(normalizedValue);
  if (!isNaN(parsedDate.getTime())) {
    return parsedDate;
  }

  var dateOnly = String(value).trim().split(' ')[0];
  parsedDate = new Date(dateOnly + 'T00:00:00');
  return isNaN(parsedDate.getTime()) ? null : parsedDate;
}

function isRespostaSemDados(response) {
  if (!response || response.ok || !response.msg || typeof response.msg !== 'object') {
    return false;
  }

  for (var chave in response.msg) {
    if (!Object.prototype.hasOwnProperty.call(response.msg, chave)) {
      continue;
    }

    var texto = String(response.msg[chave] || '').toLowerCase();
    if (texto.indexOf('sem dados para os filtros selecionados') !== -1) {
      return true;
    }
  }

  return false;
}

function confirmarGeracaoSemDados(onConfirm) {
  var modalElement = document.getElementById('confirmar_sem_dados_modal');
  var confirmButton = document.getElementById('confirmar_sem_dados_btn');

  if (modalElement && confirmButton && window.bootstrap && bootstrap.Modal) {
    if (!modalSemDadosInstance) {
      modalSemDadosInstance = new bootstrap.Modal(modalElement, {
        backdrop: 'static',
        keyboard: true
      });
    }

    confirmButton.onclick = function () {
      modalSemDadosInstance.hide();
      if (typeof onConfirm === 'function') {
        onConfirm();
      }
    };

    modalSemDadosInstance.show();
    return;
  }

  if (window.confirm('Nao ha dados para os filtros selecionados. Deseja gerar o PDF mesmo assim?')) {
    if (typeof onConfirm === 'function') {
      onConfirm();
    }
  }
}

function hasPeriodoTipoSelecionado() {
  var checkboxPeriodoAdicionado = document.getElementById('periodo_adicionado');
  var checkboxPeriodoFinalizado = document.getElementById('periodo_finalizado');

  return !!(
    (checkboxPeriodoAdicionado && checkboxPeriodoAdicionado.checked) ||
    (checkboxPeriodoFinalizado && checkboxPeriodoFinalizado.checked)
  );
}

function garantirAoMenosUmPeriodo(checkboxAlterado) {
  var checkboxPeriodoAdicionado = document.getElementById('periodo_adicionado');
  var checkboxPeriodoFinalizado = document.getElementById('periodo_finalizado');
  if (!checkboxPeriodoAdicionado || !checkboxPeriodoFinalizado) {
    return;
  }

  if (!checkboxPeriodoAdicionado.checked && !checkboxPeriodoFinalizado.checked) {
    if (checkboxAlterado) {
      checkboxAlterado.checked = true;
    }
    alert_personalizado('Periodo', 'Selecione pelo menos um tipo de periodo: Adicionado ou Finalizado.');
  }

  refreshSubmitState(false);
}

function aplicarPresetPeriodoPorProcesso(forceApply) {
  var dataInicial = document.getElementById('data_inicial');
  var dataFinal = document.getElementById('data_final');
  if (!dataInicial || !dataFinal) {
    return;
  }

  if (!forceApply && periodoAlteradoManualmente) {
    return;
  }

  var processoSelecionado = get_radio();
  if (!processoSelecionado) {
    return;
  }

  var ultimaDataRaw = processoSelecionado.dataset.ultimaData || '';
  var ultimaData = parseDateInput(ultimaDataRaw);
  if (!ultimaData) {
    return;
  }

  var maxDateAttr = dataFinal.getAttribute('max');
  var maxDate = parseDateInput(maxDateAttr ? (maxDateAttr + 'T00:00:00') : '');
  if (maxDate && ultimaData > maxDate) {
    ultimaData = maxDate;
  }

  var fimPreset = new Date(ultimaData.getTime());
  var inicioPreset = new Date(ultimaData.getTime());
  inicioPreset.setDate(inicioPreset.getDate() - 7);

  var inicioStr = formatDateInput(inicioPreset);
  var fimStr = formatDateInput(fimPreset);
  if (!inicioStr || !fimStr) {
    return;
  }

  aplicandoPresetPeriodo = true;
  dataInicial.value = inicioStr;
  dataFinal.removeAttribute('disabled');
  dataFinal.value = fimStr;
  dataFinal.setAttribute('min', inicioStr);
  aplicandoPresetPeriodo = false;

  refreshSubmitState(false);
}

function sincronizarCartoesProcesso() {
  var radios = document.querySelectorAll('input[name="processo"]');
  radios.forEach(function (radio) {
    var card = radio.closest('.wl-process-card');
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

function sincronizarProcessoSelecionado() {
  var selecionado = get_radio();
  var cardTitle = document.getElementById('relatorio_card_title');
  var cardSubtitle = document.getElementById('relatorio_card_subtitle');

  if (!selecionado) {
    processo_nome = '';
    if (cardTitle) {
      cardTitle.textContent = 'Filtros do Relatorio';
    }
    if (cardSubtitle) {
      cardSubtitle.textContent = 'Selecione o período, tipo e participantes.';
    }
    sincronizarCartoesProcesso();
    return;
  }

  processo_nome = selecionado.value;

  if (cardTitle) {
    cardTitle.textContent = 'Filtros do Relatorio - ' + processo_nome;
  }

  if (cardSubtitle) {
    cardSubtitle.textContent = 'Processo selecionado: ' + processo_nome;
  }

  aplicarPresetPeriodoPorProcesso(false);
  sincronizarCartoesProcesso();
  refreshSubmitState(false);
}

function modalr_processo() {
  processo_lista();
  var processoCard = document.getElementById('inicioOverlay');
  if (processoCard && typeof processoCard.scrollIntoView === 'function') {
    processoCard.scrollIntoView({
      behavior: 'smooth',
      block: 'center'
    });
  }
}

function aparecer_lista() {
  sincronizarProcessoSelecionado();

  if (!processo_nome) {
    alert_personalizado('Processo', 'Selecione um processo para continuar.');
  }
}

function createProcessoRadioItem(processo, index) {
  var id = 'processo_' + (processo.input || 'item') + '_' + index;
  var label = document.createElement('label');
  label.className = 'wl-process-card';

  var radioElement = document.createElement('input');
  radioElement.type = 'radio';
  radioElement.name = 'processo';
  radioElement.id = id;
  radioElement.value = processo.nome;
  radioElement.dataset.input = processo.input || '';
  radioElement.dataset.filtro = processo.filtro || '';
  radioElement.dataset.processoId = processo.id || '';
  radioElement.dataset.ultimaData = processo.ultima_data || '';

  if (processo_nome) {
    radioElement.checked = (processo.nome === processo_nome);
  }

  radioElement.addEventListener('change', function () {
    sincronizarProcessoSelecionado();
  });

  var labelElement = document.createElement('span');
  labelElement.className = 'wl-process-name';
  labelElement.textContent = processo.nome;

  label.appendChild(radioElement);
  label.appendChild(labelElement);

  return label;
}

function renderProcessos() {
  var radioContainer = document.getElementById('processos_radio');
  if (!radioContainer) {
    return;
  }

  radioContainer.innerHTML = '';

  if (!processos.length) {
    var emptyState = document.createElement('div');
    emptyState.className = 'wl-upload-empty';
    emptyState.textContent = 'Nenhum processo encontrado.';
    radioContainer.appendChild(emptyState);
    sincronizarProcessoSelecionado();
    return;
  }

  processos.forEach(function (processo, index) {
    radioContainer.appendChild(createProcessoRadioItem(processo, index));
  });

  if (!get_radio()) {
    var firstProcess = radioContainer.querySelector('input[name="processo"]');
    if (firstProcess) {
      firstProcess.checked = true;
    }
  }

  sincronizarProcessoSelecionado();
}

function processo_lista() {
  $.ajax({
    url: '<?= base_url('public/processos_lista') ?>',
    type: 'POST',
    dataType: 'json',
    data: { contexto_tela: 'relatorios' },
    async: false,
    success: function (response) {
      processos = Array.isArray(response.lista) ? response.lista : [];
      renderProcessos();
    }
  });
}

function resetSelectOptions(selectElement, defaultLabel) {
  if (!selectElement) {
    return;
  }

  selectElement.innerHTML = '';

  var option = document.createElement('option');
  option.value = '';
  option.textContent = defaultLabel;
  selectElement.appendChild(option);
}

function appendSelectOptions(selectElement, items) {
  if (!selectElement || !Array.isArray(items)) {
    return;
  }

  items.forEach(function (item) {
    var option = document.createElement('option');
    option.value = item.id;
    option.textContent = item.nome;
    selectElement.appendChild(option);
  });
}

function carregarEmpresasRelatorio() {
  var empresaSelect = document.getElementById('empresa_id');
  if (!empresaSelect) {
    return;
  }

  $.ajax({
    url: '<?= base_url('public/empresas_lista') ?>',
    type: 'GET',
    dataType: 'json',
    success: function (response) {
      var lista = Array.isArray(response.lista) ? response.lista : [];
      resetSelectOptions(empresaSelect, 'Todas');
      appendSelectOptions(empresaSelect, lista);
    },
    error: function () {
      resetSelectOptions(empresaSelect, 'Todas');
    }
  });
}

function carregarEmpreendimentosRelatorio(empresaId) {
  var empreendimentoSelect = document.getElementById('empreendimento_id');
  if (!empreendimentoSelect) {
    return;
  }

  $.ajax({
    url: '<?= base_url('public/empreendimentos_lista') ?>',
    type: 'GET',
    dataType: 'json',
    data: {
      empresaId: empresaId || ''
    },
    success: function (response) {
      var lista = Array.isArray(response.lista) ? response.lista : [];
      resetSelectOptions(empreendimentoSelect, 'Todos');
      appendSelectOptions(empreendimentoSelect, lista);
    },
    error: function () {
      resetSelectOptions(empreendimentoSelect, 'Todos');
    }
  });
}

function initFiltrosEmpresaEmpreendimento() {
  var empresaSelect = document.getElementById('empresa_id');
  var empreendimentoSelect = document.getElementById('empreendimento_id');
  if (!empresaSelect) {
    return;
  }

  if (empreendimentoSelect) {
    resetSelectOptions(empreendimentoSelect, 'Todos');
    empreendimentoSelect.disabled = true;
  }

  carregarEmpresasRelatorio();

  empresaSelect.addEventListener('change', function () {
    var empresaId = empresaSelect.value || '';

    if (!empresaId) {
      if (empreendimentoSelect) {
        resetSelectOptions(empreendimentoSelect, 'Todos');
        empreendimentoSelect.disabled = true;
      }
      return;
    }

    if (empreendimentoSelect) {
      empreendimentoSelect.disabled = false;
    }
    carregarEmpreendimentosRelatorio(empresaId);
  });
}

function openPdfDownload(pdfBase64, pdfFileName) {
  var pdfContent = atob(pdfBase64 || '');
  var bytes = new Uint8Array(pdfContent.length);

  for (var i = 0; i < pdfContent.length; i++) {
    bytes[i] = pdfContent.charCodeAt(i);
  }

  var blob = new Blob([bytes], { type: 'application/pdf' });
  var url = window.URL.createObjectURL(blob);
  var a = document.createElement('a');
  a.href = url;
  a.download = pdfFileName;
  document.body.appendChild(a);
  a.click();
  a.remove();
  window.URL.revokeObjectURL(url);
}

function cadastrar(forceGerarSemDados) {
  forceGerarSemDados = !!forceGerarSemDados;
  if (!processo_nome) {
    alert_personalizado('Processo', 'Selecione o processo antes de gerar o relatorio.');
    modalr_processo();
    return;
  }

  var dataInicial = document.getElementById('data_inicial').value;
  var dataFinal = document.getElementById('data_final').value;
  var processoSelecionado = get_radio();
  var processoId = processoSelecionado ? (processoSelecionado.dataset.processoId || '') : '';
  var processoTipo = processoSelecionado ? (processoSelecionado.dataset.input || '') : '';
  var periodoAdicionado = !!(document.getElementById('periodo_adicionado') && document.getElementById('periodo_adicionado').checked);
  var periodoFinalizado = !!(document.getElementById('periodo_finalizado') && document.getElementById('periodo_finalizado').checked);
  var empresaId = document.getElementById('empresa_id') ? document.getElementById('empresa_id').value : '';
  var empreendimentoId = document.getElementById('empreendimento_id') ? document.getElementById('empreendimento_id').value : '';
  if (!empresaId) {
    empreendimentoId = '';
  }

  var selectedValues = getSelectedCheckboxValues();

  if (!dataInicial) {
    alert_personalizado('Data Inicial', 'Selecione uma data inicial válida.');
    return;
  }

  if (!dataFinal) {
    alert_personalizado('Data Final', 'Selecione uma data final válida.');
    return;
  }

  if (dataFinal < dataInicial) {
    alert_personalizado('Período', 'A data final não pode ser anterior à data inicial.');
    return;
  }

  if (!periodoAdicionado && !periodoFinalizado) {
    alert_personalizado('Periodo', 'Selecione pelo menos um tipo de periodo: Adicionado ou Finalizado.');
    return;
  }

  cadastrar_glob = true;
  refreshSubmitState(false);

  var payload = {
    dataInicial: dataInicial,
    dataFinal: dataFinal,
    relatorio: document.getElementById('rad_1').checked,
    selectedValues: selectedValues,
    processo: processo_nome,
    processoId: processoId,
    processoTipo: processoTipo,
    empresaId: empresaId,
    empreendimentoId: empreendimentoId,
    periodoAdicionado: periodoAdicionado,
    periodoFinalizado: periodoFinalizado,
    gerarSemDados: forceGerarSemDados
  };
  console.log('[Relatorio] POST /relatorio payload', payload);

  $.ajax({
    url: '<?= base_url('public/relatorio') ?>',
    type: 'POST',
    dataType: 'json',
    data: payload,
    success: function (response) {
      if (!response || !response.ok) {
        var ehSemDados = isRespostaSemDados(response);

        if (ehSemDados && !forceGerarSemDados) {
          confirmarGeracaoSemDados(function () {
            cadastrar(true);
          });
        } else {
          if (!response || !response.msg) {
            alert_personalizado('Relatorio', 'Nao foi possivel gerar o relatorio para os filtros informados.');
          }
          if (response && response.msg) {
            for (var chave in response.msg) {
              if (Object.prototype.hasOwnProperty.call(response.msg, chave)) {
                alert_personalizado(chave, response.msg[chave]);
              }
            }
          }
        }
      } else {
        openPdfDownload(response.pdf, response.nome_pdf);
      }

      setTimeout(function () {
        cadastrar_glob = false;
        refreshSubmitState(false);
      }, 500);
    },
    error: function () {
      cadastrar_glob = false;
      refreshSubmitState(false);
    }
  });
}

function alert_personalizado(titulo, bory) {
  if (window.jQuery && $(document).Toasts) {
    $(document).Toasts('create', {
      class: 'bg-danger',
      title: titulo,
      subtitle: 'Subtitle',
      autohide: true,
      delay: 13000,
      body: bory
    });
    return;
  }

  if (window.toastr) {
    toastr.error(bory, titulo);
    return;
  }

  window.alert(titulo + ': ' + bory);
}

function getNextGroupId() {
  var existingGroups = document.querySelectorAll('[id^="group_"]');
  var maxId = 0;

  existingGroups.forEach(function (group) {
    var parts = group.id.split('_');
    if (parts.length < 2) {
      return;
    }

    var idNumber = parseInt(parts[1], 10);
    if (!isNaN(idNumber) && idNumber > maxId) {
      maxId = idNumber;
    }
  });

  return 'group_' + (maxId + 1);
}

function createControls(label) {
  var nextGroupId = getNextGroupId();
  var container = document.createElement('section');
  container.id = nextGroupId;
  container.className = 'wl-report-user-group';

  var header = document.createElement('div');
  header.className = 'wl-report-user-group-header';

  var title = document.createElement('h6');
  title.className = 'mb-0';
  title.textContent = label;

  var buttonContainer = document.createElement('div');
  buttonContainer.className = 'wl-report-user-group-actions';

  var selectAllButton = document.createElement('button');
  selectAllButton.id = 'selectAll_' + nextGroupId;
  selectAllButton.type = 'button';
  selectAllButton.className = 'btn btn-outline-primary btn-sm';
  selectAllButton.textContent = 'Selecionar Todos';
  selectAllButton.addEventListener('click', function () {
    selectAllCheckboxes(nextGroupId);
  });

  var deselectAllButton = document.createElement('button');
  deselectAllButton.id = 'deselectAll_' + nextGroupId;
  deselectAllButton.type = 'button';
  deselectAllButton.className = 'btn btn-outline-primary btn-sm';
  deselectAllButton.textContent = 'Desmarcar Todos';
  deselectAllButton.addEventListener('click', function () {
    deselectAllCheckboxes(nextGroupId);
  });

  buttonContainer.appendChild(selectAllButton);
  buttonContainer.appendChild(deselectAllButton);

  header.appendChild(title);
  header.appendChild(buttonContainer);

  var checkboxList = document.createElement('div');
  checkboxList.id = nextGroupId + '_list';
  checkboxList.className = 'wl-report-checkbox-grid';

  container.appendChild(header);
  container.appendChild(checkboxList);

  var parent = document.getElementById('user_groups_placeholder') || document.getElementById('inputs_body');
  parent.appendChild(container);

  return nextGroupId;
}

function sanitizeForId(value) {
  return String(value).replace(/[^a-zA-Z0-9_-]/g, '_');
}

function addCheckboxes(items, groupId) {
  var listContainer = document.getElementById(groupId + '_list');
  if (!listContainer) {
    return;
  }

  listContainer.innerHTML = '';

  var checkboxAtivo = document.getElementById('checkbox_ativo');
  var checkboxDesativado = document.getElementById('checkbox_desativado');
  var showAtivo = checkboxAtivo ? checkboxAtivo.checked : false;
  var showDesativado = checkboxDesativado ? checkboxDesativado.checked : false;

  var entries = [];

  if (showAtivo && items && items.ativo) {
    for (var keyAtivo in items.ativo) {
      if (Object.prototype.hasOwnProperty.call(items.ativo, keyAtivo)) {
        entries.push({
          key: keyAtivo,
          label: items.ativo[keyAtivo],
          status: 'ativo'
        });
      }
    }
  }

  if (showDesativado && items && items.desativado) {
    for (var keyDesativado in items.desativado) {
      if (Object.prototype.hasOwnProperty.call(items.desativado, keyDesativado)) {
        entries.push({
          key: keyDesativado,
          label: items.desativado[keyDesativado],
          status: 'desativado'
        });
      }
    }
  }

  entries.forEach(function (entry, index) {
    var wrapper = document.createElement('div');
    wrapper.className = 'form-check mb-0';

    var checkboxId = groupId + '_checkbox_' + entry.status + '_' + sanitizeForId(entry.key) + '_' + index;
    var checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.className = 'form-check-input grupo-checkbox';
    checkbox.value = entry.key;
    checkbox.id = checkboxId;
    checkbox.dataset.group = groupId;
    checkbox.checked = true;
    checkbox.addEventListener('change', function () {
      refreshSubmitState(false);
    });

    var label = document.createElement('label');
    label.className = 'form-check-label';
    label.htmlFor = checkboxId;
    label.textContent = entry.label;

    wrapper.appendChild(checkbox);
    wrapper.appendChild(label);
    listContainer.appendChild(wrapper);
  });

  refreshSubmitState(false);
}

function createCheckboxes(data, groupId) {
  addCheckboxes(data, groupId);
}

function selectAllCheckboxes(groupId) {
  var checkboxes = document.querySelectorAll('#' + groupId + '_list input.grupo-checkbox');
  checkboxes.forEach(function (checkbox) {
    checkbox.checked = true;
  });
  refreshSubmitState(false);
}

function deselectAllCheckboxes(groupId) {
  var checkboxes = document.querySelectorAll('#' + groupId + '_list input.grupo-checkbox');
  checkboxes.forEach(function (checkbox) {
    checkbox.checked = false;
  });
  checkIfAllDesenhistaUnchecked(false);
}

function checkIfAllUnchecked(checked, groupId) {
  if (checked) {
    refreshSubmitState(false);
    return;
  }

  var checkboxes = document.querySelectorAll('#' + groupId + '_list input.grupo-checkbox');
  var allUnchecked = Array.from(checkboxes).every(function (checkbox) {
    return !checkbox.checked;
  });

  if (allUnchecked) {
    refreshSubmitState(true);
  }
}

function areAllGeneratedCheckboxesUnchecked() {
  var checkboxes = document.querySelectorAll('#user_groups_placeholder input.grupo-checkbox');
  if (!checkboxes.length) {
    return true;
  }

  return Array.from(checkboxes).every(function (checkbox) {
    return !checkbox.checked;
  });
}

function areAllCheckboxesDesenhistaUnchecked() {
  return areAllGeneratedCheckboxesUnchecked();
}

function areAllCheckboxesCortadorUnchecked() {
  return areAllGeneratedCheckboxesUnchecked();
}

function checkIfAllDesenhistaUnchecked(eventChecked) {
  if (!eventChecked) {
    var allUncheckedDesenhista = areAllCheckboxesDesenhistaUnchecked();
    var allUncheckedCortador = areAllCheckboxesCortadorUnchecked();

    if (allUncheckedDesenhista && allUncheckedCortador) {
      refreshSubmitState(true);
      return;
    }
  }

  refreshSubmitState(false);
}

function refreshSubmitState(showAlert) {
  var cadastrarBtn = document.getElementById('cadastrar_btn');
  if (!cadastrarBtn) {
    return;
  }

  var checkboxAtivo = document.getElementById('checkbox_ativo');
  var checkboxDesativado = document.getElementById('checkbox_desativado');
  var hasStatusSelecionado = (checkboxAtivo && checkboxAtivo.checked) || (checkboxDesativado && checkboxDesativado.checked);
  var hasProcessoSelecionado = !!processo_nome;
  var dataInicial = document.getElementById('data_inicial');
  var dataFinal = document.getElementById('data_final');
  var dataInicialValue = dataInicial ? dataInicial.value : '';
  var dataFinalValue = dataFinal ? dataFinal.value : '';
  var hasPeriodoValido = !!dataInicialValue && !!dataFinalValue && dataFinalValue >= dataInicialValue;
  var hasPeriodoTipo = hasPeriodoTipoSelecionado();

  var disableButton = cadastrar_glob || !hasStatusSelecionado || !hasProcessoSelecionado || !hasPeriodoValido || !hasPeriodoTipo;
  cadastrarBtn.disabled = disableButton;
}

function getSelectedCheckboxValues() {
  var selectedValues = {};

  Object.entries(data_glob.id_groups || {}).forEach(function (entry) {
    var key = entry[0];
    var groupId = entry[1];
    var checkboxes = document.querySelectorAll('#' + groupId + '_list input.grupo-checkbox:checked');

    selectedValues[key] = Array.from(checkboxes).map(function (checkbox) {
      return checkbox.value;
    });
  });

  return selectedValues;
}

function updateGroupsByStatusFilter() {
  Object.entries(data_glob.lista || {}).forEach(function (entry) {
    var key = entry[0];
    var value = entry[1];
    var groupId = data_glob.id_groups[key];

    if (groupId) {
      addCheckboxes(value, groupId);
    }
  });

  refreshSubmitState(false);
}

function inicio() {
  $.ajax({
    url: '<?= base_url('public/lista_usuarios_niveis') ?>',
    type: 'POST',
    dataType: 'json',
    success: function (response) {
      response.id_groups = {};

      Object.entries(response.lista || {}).forEach(function (entry) {
        var key = entry[0];
        var value = entry[1];
        var idGroup = createControls(key);

        response.id_groups[key] = idGroup;
        createCheckboxes(value, idGroup);
      });

      data_glob = response;
      refreshSubmitState(false);
    }
  });
}

function initDateValidation() {
  var dataInicial = document.getElementById('data_inicial');
  var dataFinal = document.getElementById('data_final');

  if (!dataInicial || !dataFinal) {
    return;
  }

  var today = new Date();
  var maxDate = today.toISOString().split('T')[0];
  var presetStartDate = new Date(today);
  presetStartDate.setDate(presetStartDate.getDate() - 7);
  var presetStart = presetStartDate.toISOString().split('T')[0];

  dataInicial.setAttribute('max', maxDate);
  dataFinal.setAttribute('max', maxDate);

  if (!dataInicial.value && !dataFinal.value) {
    dataInicial.value = presetStart;
    dataFinal.removeAttribute('disabled');
    dataFinal.value = maxDate;
    dataFinal.setAttribute('min', presetStart);
    refreshSubmitState(false);
  }

  dataInicial.addEventListener('change', function () {
    if (!aplicandoPresetPeriodo) {
      periodoAlteradoManualmente = true;
    }

    if (dataInicial.value) {
      dataFinal.removeAttribute('disabled');
      dataFinal.setAttribute('min', dataInicial.value);

      if (dataFinal.value && dataFinal.value < dataInicial.value) {
        alert_personalizado('Período', 'A data final não pode ser anterior à data inicial.');
        dataFinal.value = '';
      }
    } else {
      dataFinal.setAttribute('disabled', 'true');
      dataFinal.value = '';
    }
    refreshSubmitState(false);
  });

  dataFinal.addEventListener('change', function () {
    if (!aplicandoPresetPeriodo) {
      periodoAlteradoManualmente = true;
    }

    if (dataInicial.value && dataFinal.value && dataFinal.value < dataInicial.value) {
      alert_personalizado('Período', 'A data final não pode ser anterior à data inicial.');
      dataFinal.value = '';
    }
    refreshSubmitState(false);
  });

  if (typeof dataInicial.showPicker === 'function') {
    dataInicial.addEventListener('dblclick', function () {
      dataInicial.showPicker();
    });
  }

  if (typeof dataFinal.showPicker === 'function') {
    dataFinal.addEventListener('dblclick', function () {
      dataFinal.showPicker();
    });
  }
}

function initInputs() {
  var radioAnalitico = document.getElementById('rad_1');
  var checkboxAtivo = document.getElementById('checkbox_ativo');
  var checkboxDesativado = document.getElementById('checkbox_desativado');
  var checkboxPeriodoAdicionado = document.getElementById('periodo_adicionado');
  var checkboxPeriodoFinalizado = document.getElementById('periodo_finalizado');

  if (radioAnalitico) {
    radioAnalitico.checked = true;
  }

  if (checkboxPeriodoAdicionado) {
    checkboxPeriodoAdicionado.checked = true;
    checkboxPeriodoAdicionado.addEventListener('change', function () {
      garantirAoMenosUmPeriodo(checkboxPeriodoAdicionado);
    });
  }

  if (checkboxPeriodoFinalizado) {
    checkboxPeriodoFinalizado.addEventListener('change', function () {
      garantirAoMenosUmPeriodo(checkboxPeriodoFinalizado);
    });
  }

  if (checkboxPeriodoAdicionado || checkboxPeriodoFinalizado) {
    garantirAoMenosUmPeriodo(null);
  }

  if (checkboxAtivo) {
    checkboxAtivo.checked = true;
    checkboxAtivo.addEventListener('click', function (event) {
      updateGroupsByStatusFilter();
      checkIfAllDesenhistaUnchecked(event.target.checked);
    });
  }

  if (checkboxDesativado) {
    checkboxDesativado.addEventListener('click', function (event) {
      updateGroupsByStatusFilter();
      checkIfAllDesenhistaUnchecked(event.target.checked);
    });
  }

  initDateValidation();
  initFiltrosEmpresaEmpreendimento();
  inicio();
  processo_lista();
  refreshSubmitState(false);
}

document.addEventListener('DOMContentLoaded', initInputs);
</script>
