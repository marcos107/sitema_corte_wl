// public/assets/visualizar.js
// Visualizador de arquivos (DXF, STL, imagens e PDF)

(function (global) {
  'use strict';

  var currentBlobUrl = null;
  var viewerLastRequest = { id: null, tipo: null };
  var viewerCurrentExt = '';
  var viewerCurrentName = '';
  var viewerCloseNotified = true;

  function ensureModalCloseStyle() {
    if (document.getElementById('wl-modal-close-style')) {
      return;
    }
    var style = document.createElement('style');
    style.id = 'wl-modal-close-style';
    style.textContent =
      '.wl-modal-close{' +
      'border:1px solid #d1d5db;background:#fff;color:#334155;width:32px;height:32px;border-radius:6px;font-size:18px;line-height:1;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;padding:0;' +
      '}' +
      '.wl-modal-close:hover{background:#f1f5f9;color:#0f172a;}';
    document.head.appendChild(style);
  }

  function ensureViewerModalStyle() {
    if (document.getElementById('wl-viewer-modal-style')) {
      return;
    }
    var style = document.createElement('style');
    style.id = 'wl-viewer-modal-style';
    style.textContent =
      '.wl-viewer-modal .modal-dialog{' +
      'width:min(96vw,1500px);max-width:none;margin:1rem auto;' +
      '}' +
      '.wl-viewer-modal .modal-content{' +
      'border:0;border-radius:12px;overflow:hidden;background:#0b1324;box-shadow:0 28px 70px rgba(2,6,23,.55);' +
      '}' +
      '.wl-viewer-modal .modal-header{' +
      'border:0;background:linear-gradient(130deg,#10233f 0%,#0b1629 100%);color:#e2e8f0;padding:.9rem 1rem;' +
      '}' +
      '.wl-viewer-title-wrap{display:flex;flex-direction:column;gap:2px;min-width:0;}' +
      '.wl-viewer-title-wrap h5{margin:0;font-weight:700;font-size:1rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:72vw;}' +
      '.wl-viewer-subtitle{font-size:.78rem;color:#94a3b8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:72vw;}' +
      '.wl-viewer-type{font-size:.72rem;font-weight:700;letter-spacing:.03em;border-radius:999px;padding:.25rem .6rem;}' +
      '.wl-viewer-modal .modal-body{padding:0;background:#060b15;}' +
      '.wl-viewer-canvas{' +
      'position:relative;width:100%;height:min(72vh,820px);background:radial-gradient(circle at top,#0f213b 0%,#070d19 46%,#04070d 100%);' +
      'display:flex;align-items:center;justify-content:center;overflow:hidden;' +
      '}' +
      '.wl-viewer-canvas iframe,.wl-viewer-canvas img{border:0;}' +
      '.wl-viewer-loading{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.6rem;color:#cbd5e1;font-size:.86rem;}' +
      '.wl-viewer-loading .spinner-border{width:2rem;height:2rem;}' +
      '.wl-viewer-status{' +
      'position:absolute;left:.8rem;top:.8rem;z-index:20;padding:.35rem .55rem;border-radius:.45rem;background:rgba(15,23,42,.72);' +
      'font-size:.75rem;color:#cbd5e1;border:1px solid rgba(148,163,184,.28);' +
      '}' +
      '.wl-viewer-status.is-error{background:rgba(127,29,29,.78);color:#fecaca;border-color:rgba(252,165,165,.36);}' +
      '.wl-viewer-status.is-info{background:rgba(30,58,138,.72);color:#dbeafe;border-color:rgba(147,197,253,.35);}' +
      '.wl-viewer-modal .modal-footer{' +
      'border:0;background:#0b1629;padding:.75rem 1rem;display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;' +
      '}' +
      '.wl-viewer-modal .modal-footer .form-check-label{color:#94a3b8;font-size:.82rem;}' +
      '.wl-viewer-modal .btn.wl-close-dxf-modal{min-width:96px;}' +
      '@media (max-width: 768px){' +
      '.wl-viewer-modal .modal-dialog{width:99vw;margin:.45rem auto;}' +
      '.wl-viewer-canvas{height:66vh;}' +
      '.wl-viewer-title-wrap h5,.wl-viewer-subtitle{max-width:58vw;}' +
      '.wl-viewer-modal .modal-footer{justify-content:flex-end;}' +
      '.wl-viewer-modal .modal-footer .form-check{order:3;width:100%;}' +
      '}';
    document.head.appendChild(style);
  }

  function setViewerStatus(message, type) {
    var statusEl = document.getElementById('wl-viewer-status');
    if (!statusEl) {
      return;
    }
    if (!message) {
      statusEl.textContent = '';
      statusEl.classList.add('d-none');
      statusEl.classList.remove('is-error', 'is-info');
      return;
    }
    statusEl.textContent = String(message);
    statusEl.classList.remove('d-none', 'is-error', 'is-info');
    if (type === 'error') {
      statusEl.classList.add('is-error');
    } else {
      statusEl.classList.add('is-info');
    }
  }

  function setViewerMeta(title, subtitle, ext) {
    var titleEl = document.getElementById('modal-dxf-title');
    var subtitleEl = document.getElementById('modal-dxf-subtitle');
    var badgeEl = document.getElementById('wl-viewer-type-badge');
    var extNorm = normalizeExt(ext || '');

    if (titleEl) {
      titleEl.textContent = title || 'Visualizador';
    }
    if (subtitleEl) {
      subtitleEl.textContent = subtitle || '';
      subtitleEl.title = subtitle || '';
    }
    if (badgeEl) {
      badgeEl.textContent = extNorm ? extNorm.toUpperCase() : 'ARQ';
      badgeEl.className = 'badge wl-viewer-type ' + (
        extNorm === 'pdf' ? 'bg-danger-subtle text-danger' :
        (extNorm === 'dxf' || extNorm === 'stl' || extNorm === 'slt') ? 'bg-primary-subtle text-primary' :
        'bg-light text-dark'
      );
    }
  }

  function updateViewerActionButtons() {
    var openBtn = document.getElementById('wl-viewer-open-tab');
    var downloadBtn = document.getElementById('wl-viewer-download');
    var hasBlob = !!currentBlobUrl;

    if (openBtn) {
      openBtn.disabled = !hasBlob;
      openBtn.title = hasBlob ? 'Abrir arquivo em nova aba' : 'Arquivo ainda nao carregado';
    }
    if (downloadBtn) {
      downloadBtn.disabled = !hasBlob;
      downloadBtn.title = hasBlob ? 'Baixar arquivo atual' : 'Arquivo ainda nao carregado';
    }
  }

  function bindViewerActionButtons() {
    var openBtn = document.getElementById('wl-viewer-open-tab');
    var downloadBtn = document.getElementById('wl-viewer-download');
    if (openBtn && openBtn.dataset.wlBound !== '1') {
      openBtn.dataset.wlBound = '1';
      openBtn.addEventListener('click', function () {
        if (!currentBlobUrl) {
          return;
        }
        window.open(currentBlobUrl, '_blank', 'noopener');
      });
    }
    if (downloadBtn && downloadBtn.dataset.wlBound !== '1') {
      downloadBtn.dataset.wlBound = '1';
      downloadBtn.addEventListener('click', function () {
        if (!currentBlobUrl) {
          return;
        }
        var nome = viewerCurrentName || 'arquivo';
        var ext = normalizeExt(viewerCurrentExt);
        if (ext && nome.toLowerCase().lastIndexOf('.' + ext) !== nome.length - (ext.length + 1)) {
          nome += '.' + ext;
        }
        var a = document.createElement('a');
        a.href = currentBlobUrl;
        a.download = nome;
        document.body.appendChild(a);
        a.click();
        a.remove();
      });
    }
    updateViewerActionButtons();
  }

  function normalizeExt(ext) {
    return String(ext || '').toLowerCase().replace(/^\./, '').trim();
  }

  function mimeFromExt(ext) {
    var e = normalizeExt(ext);
    switch (e) {
      case 'jpg':
      case 'jpeg':
        return 'image/jpeg';
      case 'png':
        return 'image/png';
      case 'gif':
        return 'image/gif';
      case 'bmp':
        return 'image/bmp';
      case 'webp':
        return 'image/webp';
      case 'svg':
        return 'image/svg+xml';
      case 'tif':
      case 'tiff':
        return 'image/tiff';
      case 'pdf':
        return 'application/pdf';
      case 'dxf':
        return 'application/dxf';
      case 'stl':
      case 'slt':
        return 'model/stl';
      default:
        return 'application/octet-stream';
    }
  }

  function sanitizeBase64(value) {
    var s = String(value || '').trim();
    if (!s) {
      return '';
    }
    if (s.indexOf('data:') === 0) {
      var parts = s.split(',');
      return parts.length > 1 ? parts[1].replace(/\s/g, '') : '';
    }
    return s.replace(/\s/g, '');
  }

  function base64ToBlobUrl(base64Value, ext) {
    var clean = sanitizeBase64(base64Value);
    if (!clean) {
      return null;
    }

    try {
      var bytes = atob(clean);
      var len = bytes.length;
      var buffer = new Uint8Array(len);
      for (var i = 0; i < len; i++) {
        buffer[i] = bytes.charCodeAt(i);
      }
      var blob = new Blob([buffer], { type: mimeFromExt(ext) });
      return URL.createObjectURL(blob);
    } catch (err) {
      console.error('[visualizar] Falha ao converter base64 para Blob:', err);
      return null;
    }
  }

  function revokeCurrentBlobUrl() {
    if (currentBlobUrl) {
      try {
        URL.revokeObjectURL(currentBlobUrl);
      } catch (err) {
        console.warn('[visualizar] Falha ao revogar blob URL:', err);
      }
      currentBlobUrl = null;
    }
  }

  function notificarVisualizadorFechado() {
    if (viewerCloseNotified) {
      return;
    }

    viewerCloseNotified = true;
    try {
      document.dispatchEvent(new CustomEvent('wl:visualizador:fechado', {
        detail: {
          id: viewerLastRequest.id,
          tipo: viewerLastRequest.tipo,
        },
      }));
    } catch (err) {
      var event = document.createEvent('Event');
      event.initEvent('wl:visualizador:fechado', true, true);
      document.dispatchEvent(event);
    }

    if (typeof global.reabrirModalArquivosProjetoSeNecessario === 'function') {
      global.reabrirModalArquivosProjetoSeNecessario();
    }
  }

  function limparVisualizadorAposFechar() {
    revokeCurrentBlobUrl();
    var container = document.getElementById('dxf-viewer-container');
    if (container) {
      container.innerHTML = '';
    }
    setViewerStatus('');
    setViewerMeta('Visualizador CAD', 'Aguardando arquivo...', '');
    viewerCurrentExt = '';
    viewerCurrentName = '';
    updateViewerActionButtons();
    notificarVisualizadorFechado();
  }

  function showCadControls(show) {
    var chk = document.getElementById('dxf-enable-controls');
    if (chk && chk.closest('.form-check')) {
      chk.closest('.form-check').style.display = show ? '' : 'none';
    }
  }

  global.VISUALIZAR_CONFIG = {
    base_url: '',
    endpoint: 'public/ver_desenho',
  };

  global.configurarVisualizador = function (config) {
    if (!config) {
      return;
    }
    if (config.base_url) {
      global.VISUALIZAR_CONFIG.base_url = String(config.base_url).replace(/\/+$/, '');
    }
    if (config.endpoint) {
      global.VISUALIZAR_CONFIG.endpoint = String(config.endpoint).replace(/^\/+/, '');
    }
  };

  function garantirModal() {
    ensureModalCloseStyle();
    ensureViewerModalStyle();

    if (!document.getElementById('modal-dxf-viewer')) {
      var modalHtml = '' +
        '<div class="modal fade wl-viewer-modal" id="modal-dxf-viewer" tabindex="-1" role="dialog" aria-hidden="true">' +
        '  <div class="modal-dialog modal-lg wl-viewer-dialog" role="document">' +
        '    <div class="modal-content">' +
        '      <div class="modal-header">' +
        '        <div class="wl-viewer-title-wrap">' +
        '          <h5 class="modal-title" id="modal-dxf-title">Visualizador CAD</h5>' +
        '          <div id="modal-dxf-subtitle" class="wl-viewer-subtitle">Aguardando arquivo...</div>' +
        '        </div>' +
        '        <div class="d-flex align-items-center gap-2">' +
        '          <span id="wl-viewer-type-badge" class="badge wl-viewer-type bg-light text-dark">ARQ</span>' +
        '          <button type="button" class="wl-modal-close wl-close-dxf-modal" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Fechar">&times;</button>' +
        '        </div>' +
        '      </div>' +
        '      <div class="modal-body">' +
        '        <div id="dxf-viewer-container" class="wl-viewer-canvas">' +
        '          <div class="wl-viewer-loading">' +
        '            <div class="spinner-border text-light" role="status"><span class="sr-only">Loading...</span></div>' +
        '            <span>Carregando visualizador...</span>' +
        '          </div>' +
        '        </div>' +
        '        <div id="wl-viewer-status" class="wl-viewer-status d-none"></div>' +
        '      </div>' +
        '      <div class="modal-footer">' +
        '        <div class="form-check mr-auto me-auto">' +
        '          <input class="form-check-input" type="checkbox" id="dxf-enable-controls">' +
        '          <label class="form-check-label text-muted" for="dxf-enable-controls">Navegacao livre</label>' +
        '        </div>' +
        '        <button class="btn btn-secondary ml-2 ms-2 wl-close-dxf-modal" type="button" data-dismiss="modal" data-bs-dismiss="modal">Fechar</button>' +
        '      </div>' +
        '    </div>' +
        '  </div>' +
        '</div>';
      document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    var modalEl = document.getElementById('modal-dxf-viewer');
    if (modalEl && modalEl.dataset.wlBound !== '1') {
      modalEl.dataset.wlBound = '1';
      modalEl.addEventListener('click', function (event) {
        var target = event.target;
        if (!target) {
          return;
        }
        if (target === modalEl) {
          fecharModalVisualizador();
          return;
        }
        if (target.classList.contains('wl-close-dxf-modal') || target.closest('.wl-close-dxf-modal')) {
          event.preventDefault();
          fecharModalVisualizador();
        }
      });
    }
    if (modalEl && modalEl.dataset.wlHiddenBound !== '1') {
      modalEl.dataset.wlHiddenBound = '1';
      modalEl.addEventListener('hidden.bs.modal', function () {
        limparVisualizadorAposFechar();
      });
    }
    bindViewerActionButtons();

    var $ = global.jQuery || global.$;
    if ($ && $.fn && $.fn.modal) {
      $('#modal-dxf-viewer')
        .off('hidden.bs.modal.wlviewer')
        .on('hidden.bs.modal.wlviewer', function () {
          limparVisualizadorAposFechar();
        });
    }
    setViewerMeta('Visualizador CAD', 'Aguardando arquivo...', '');
    setViewerStatus('');
    updateViewerActionButtons();
  }

  function fecharModalVisualizador() {
    var modalEl = document.getElementById('modal-dxf-viewer');
    if (!modalEl) {
      return;
    }

    var fallbackClose = function () {
      modalEl.classList.remove('show');
      modalEl.style.display = 'none';
      modalEl.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('modal-open');
      document.querySelectorAll('.modal-backdrop').forEach(function (item) {
        item.remove();
      });
      limparVisualizadorAposFechar();
    };

    if (global.bootstrap && global.bootstrap.Modal) {
      try {
        var bsInstance = global.bootstrap.Modal.getInstance(modalEl) || new global.bootstrap.Modal(modalEl);
        bsInstance.hide();
        setTimeout(fallbackClose, 350);
        return;
      } catch (e) {}
    }

    var $ = global.jQuery || global.$;
    if ($ && $.fn && $.fn.modal) {
      $('#modal-dxf-viewer').modal('hide');
      setTimeout(fallbackClose, 350);
      return;
    }

    fallbackClose();
  }

  function obterUrlVerDesenho() {
    var cfg = global.VISUALIZAR_CONFIG || {};
    if (cfg.base_url) {
      return cfg.base_url + '/' + (cfg.endpoint || 'public/ver_desenho');
    }
    return '/ver_desenho';
  }

  function extrairExtensao(nomeArquivo) {
    var nome = String(nomeArquivo || '');
    var idx = nome.lastIndexOf('.');
    if (idx < 0 || idx === nome.length - 1) {
      return '';
    }
    return normalizeExt(nome.substring(idx + 1));
  }

  function setErro(msg) {
    var container = document.getElementById('dxf-viewer-container');
    setViewerMeta('Erro na visualizacao', String(msg || 'Falha ao carregar arquivo.'), viewerCurrentExt);
    setViewerStatus('Erro ao carregar arquivo', 'error');
    if (container) {
      container.innerHTML =
        '<div class="alert alert-danger m-3" style="max-width:640px;">' +
        '<strong>Ocorreu um erro:</strong><br>' + String(msg || 'Falha ao carregar arquivo.') +
        '</div>';
    }
    updateViewerActionButtons();
  }

  function setLoadingState(message) {
    var container = document.getElementById('dxf-viewer-container');
    if (!container) {
      return;
    }
    setViewerStatus(String(message || 'Carregando arquivo...'), 'info');
    container.innerHTML =
      '<div class="wl-viewer-loading">' +
      '  <div class="spinner-border text-light" role="status"><span class="sr-only">Loading...</span></div>' +
      '  <span>' + String(message || 'Carregando arquivo...') + '</span>' +
      '</div>';
  }

  function renderizarMidia(base64Value, ext) {
    var container = document.getElementById('dxf-viewer-container');
    if (!container) {
      return;
    }

    var extNorm = normalizeExt(ext);
    var blobUrl = base64ToBlobUrl(base64Value, extNorm);
    if (!blobUrl) {
      setErro('Arquivo vazio ou invalido.');
      return;
    }

    revokeCurrentBlobUrl();
    currentBlobUrl = blobUrl;
    showCadControls(false);

    if (extNorm === 'pdf') {
      container.innerHTML =
        '<div style="width:100%;height:100%;background:#0b111d;display:flex;align-items:stretch;justify-content:center;">' +
        '  <iframe src="' + blobUrl + '" style="width:100%;height:100%;border:0;background:#0b111d" title="PDF Viewer"></iframe>' +
        '</div>';
      setViewerStatus('PDF carregado', 'info');
      updateViewerActionButtons();
      return;
    }

    container.innerHTML =
      '<div style="width:100%;height:100%;background:#0b111d;display:flex;align-items:center;justify-content:center;padding:10px;">' +
      '  <img src="' + blobUrl + '" alt="Visualizacao" style="max-width:100%;max-height:100%;object-fit:contain;border-radius:6px;box-shadow:0 18px 40px rgba(15,23,42,.55);" />' +
      '</div>';
    setViewerStatus('Imagem carregada', 'info');
    updateViewerActionButtons();
  }

  function abrirVisualizador(id, tipo) {
    garantirModal();
    viewerCloseNotified = false;
    revokeCurrentBlobUrl();
    viewerLastRequest.id = id;
    viewerLastRequest.tipo = (tipo !== undefined && tipo !== null && String(tipo).trim() !== '') ? String(tipo) : null;
    viewerCurrentName = '';
    viewerCurrentExt = '';
    updateViewerActionButtons();

    var $ = global.jQuery || global.$;
    var modal = $ && $.fn && $.fn.modal ? $('#modal-dxf-viewer') : null;

    if (modal) {
      modal.modal('show');
    } else if (global.bootstrap && global.bootstrap.Modal) {
      try {
        var modalEl = document.getElementById('modal-dxf-viewer');
        var instance = global.bootstrap.Modal.getInstance(modalEl) || new global.bootstrap.Modal(modalEl);
        instance.show();
      } catch (e) {}
    }

    setViewerMeta('Carregando arquivo...', 'Buscando dados do desenho ' + id, '');
    setLoadingState('Carregando arquivo...');

    if (!$ || !$.ajax) {
      setErro('Dependencia jQuery ausente.');
      return;
    }

    var payload = { id: id };
    if (tipo !== undefined && tipo !== null && String(tipo).trim() !== '') {
      payload.tipo = String(tipo);
    }

    $.ajax({
      url: obterUrlVerDesenho(),
      type: 'POST',
      dataType: 'json',
      data: payload,
      success: function (response) {
        if (!response) {
          setErro('Resposta vazia do servidor.');
          return;
        }
        if (response.status === false) {
          setErro(response.msg || 'Nao foi possivel carregar o arquivo.');
          return;
        }

        var nome = String(response.nome || '');
        var b64 = response.dxf || response.slt || response.stl || response.arquivo || null;
        var ext = normalizeExt(response.ext) || extrairExtensao(nome);

        if (!b64) {
          setErro('Arquivo vazio ou campo de dados ausente.');
          return;
        }

        viewerCurrentName = nome || String(id);
        viewerCurrentExt = ext;
        setViewerMeta('Visualizando: ' + (nome || id), 'Tipo: ' + (ext ? ext.toUpperCase() : 'Arquivo'), ext);
        setViewerStatus('Arquivo carregado', 'info');

        // libera abrir em nova aba/download para qualquer tipo de arquivo
        currentBlobUrl = base64ToBlobUrl(b64, ext);
        updateViewerActionButtons();

        if (ext === 'dxf') {
          showCadControls(true);
          setLoadingState('Renderizando DXF...');
          if (typeof global.renderizarDXF === 'function') {
            global.renderizarDXF(b64, {
              containerId: 'dxf-viewer-container',
              controlsCheckboxId: 'dxf-enable-controls',
            });
          } else {
            setErro('Funcao renderizarDXF nao encontrada.');
          }
          return;
        }

        if (ext === 'stl' || ext === 'slt') {
          showCadControls(true);
          setLoadingState('Renderizando STL...');
          if (typeof global.renderizarSTL === 'function') {
            global.renderizarSTL(b64, {
              containerId: 'dxf-viewer-container',
              controlsCheckboxId: 'dxf-enable-controls',
            });
          } else {
            setErro('Funcao renderizarSTL nao encontrada.');
          }
          return;
        }

        if (
          ext === 'pdf' || ext === 'jpg' || ext === 'jpeg' || ext === 'png' ||
          ext === 'gif' || ext === 'bmp' || ext === 'webp' || ext === 'svg' ||
          ext === 'tif' || ext === 'tiff'
        ) {
          setLoadingState('Renderizando arquivo...');
          renderizarMidia(b64, ext);
          return;
        }

        showCadControls(true);
        setLoadingState('Renderizando arquivo CAD...');
        if (typeof global.renderizarDXF === 'function') {
          global.renderizarDXF(b64, {
            containerId: 'dxf-viewer-container',
            controlsCheckboxId: 'dxf-enable-controls',
          });
        } else {
          setErro('Extensao nao suportada.');
        }
      },
      error: function () {
        setErro('Erro de comunicacao com o servidor.');
      },
    });
  }

  global.ver_arquivo = abrirVisualizador;
  global.ver_dxf = abrirVisualizador;
  global.ver_dxf_projeto = function (id) {
    abrirVisualizador(id, 'projeto');
  };
})(window);
