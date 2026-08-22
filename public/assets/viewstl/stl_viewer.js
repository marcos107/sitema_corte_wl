// public/assets/viewstl/stl_viewer.js
// ------------------------------------------------------
// Responsável APENAS por renderizar STL/SLT dentro do
// container indicado (padrão: #dxf-viewer-container)
//
// Requisitos globais (já incluídos na página):
// - window.THREE (three.min.js da pasta viewstl)
// - window.StlViewer (definido por stl_viewer.min.js)
// - window.VIEWSTL_BASE (opcional) (caminho base para arquivos do viewstl)
// - jQuery/Bootstrap (apenas para limpar URL ao fechar modal)
//
// Uso típico (visualizar.js chama):
// renderizarSTL(b64, {
// containerId: 'dxf-viewer-container',
// controlsCheckboxId: 'dxf-enable-controls',
// centerButtonId: 'dxf-center-view-btn'
// });
// ------------------------------------------------------
(function (global) {
  function renderizarSTL(b64, opts) {
    opts = opts || {};
    const containerId = opts.containerId || 'dxf-viewer-container';
    const controlsCheckboxId = opts.controlsCheckboxId || 'dxf-enable-controls';
    const centerButtonId = opts.centerButtonId || 'dxf-center-view-btn';
    const container = document.getElementById(containerId);
    const controlsCheckbox = document.getElementById(controlsCheckboxId);
    const centerViewBtn = document.getElementById(centerButtonId);
    if (!container) {
      console.error('[STL] Container não encontrado:', containerId);
      return;
    }
    if (!global.StlViewer || !global.THREE) {
      container.innerHTML = (
        '<div class="alert alert-danger m-3">' +
          '<b>Erro:</b> StlViewer ou THREE.js não encontrados.' +
        '</div>'
      );
      console.error('[STL] StlViewer ou THREE não definidos no escopo global.');
      return;
    }
    // limpa container
    container.innerHTML = '';
    container.style.position = 'relative';
    container.style.background = '#111';
    if (!container.style.height) {
      container.style.height = '60vh';
    }
    // se já tinha um viewer, limpa referência
    if (container.__stlViewer) {
      try {
        if (typeof container.__stlViewer.dispose === 'function') {
          container.__stlViewer.dispose();
        }
      } catch (_) {}
      container.__stlViewer = null;
    }
    // base64 -> Blob -> URL
    const pure = b64.includes(',') ? b64.split(',')[1] : b64;
    const binStr = atob(pure);
    const len = binStr.length;
    const bytes = new Uint8Array(len);
    for (let i = 0; i < len; i++) {
      bytes[i] = binStr.charCodeAt(i);
    }
    const blob = new Blob([bytes], { type: 'model/stl' });
    const url = URL.createObjectURL(blob);
    // garante caminho base do viewstl (se a lib usar isso internamente)
    if (typeof global.VIEWSTL_BASE !== 'undefined') {
      global.stl_viewer_script_path = global.VIEWSTL_BASE;
    }
    // shim simples para o detector de WebGL
    global.webgl_Detector = global.webgl_Detector || function () { return true; };
    let viewer;
    try {
      // cria viewer com interação ativa
      viewer = new StlViewer(container, {
        // onde estão three/worker/etc, se a lib precisar
        load_three_files: (typeof global.VIEWSTL_BASE !== 'undefined'
          ? global.VIEWSTL_BASE
          : ''),
        // dimensões
        canvas_width: '100%',
        canvas_height: '100%',
        // aparência
        bg_color: '#111111',
        grid: true,
        rotation: true, // permite rotação com mouse
        zoom: true, // permite zoom
        drag: true, // permite arrastar/pan
        auto_resize: true,
        auto_rotate: false,
        center_models: true,
        models: [{
          id: 1,
          filename: url,
          display: 'smooth',
          color: '#dddddd',
          units: 'mm'
        }],
        model_loaded_callback: function () {
          try {
            // centraliza e enquadra o modelo ao carregar
            if (typeof viewer.set_center_models === 'function') {
              viewer.set_center_models(true);
            }
            if (typeof viewer.set_auto_zoom === 'function') {
              viewer.set_auto_zoom();
            }
            if (typeof viewer.set_zoom === 'function') {
              viewer.set_zoom(-1); // Garante zoom automático
            }
          } catch (e) {
            console.warn('[STL] Erro em model_loaded_callback:', e);
          }
        },
        load_error_callback: function (err) {
          console.error('[STL] load_error_callback:', err);
          container.innerHTML = (
            '<div class="alert alert-danger m-3">' +
              '<b>Falha ao carregar STL:</b> ' +
              (err && err.message ? err.message : err) +
            '</div>'
          );
        }
      });
      container.__stlViewer = viewer;
    } catch (e) {
      console.error('[STL] Erro ao iniciar StlViewer:', e);
      container.innerHTML = (
        '<div class="alert alert-danger m-3">' +
          '<b>Erro ao iniciar visualização STL:</b> ' + e.message +
        '</div>'
      );
      URL.revokeObjectURL(url);
      return;
    }
    // checkbox "Navegação livre"
    if (controlsCheckbox) {
      controlsCheckbox.checked = true;
      controlsCheckbox.onchange = function () {
        const ativo = controlsCheckbox.checked;
        try {
          if (typeof viewer.set_zoom === 'function') {
            viewer.set_zoom(ativo);
          }
          if (typeof viewer.set_drag === 'function') {
            viewer.set_drag(ativo);
          }
          if (!ativo) {
            // só por garantia, trava um pouco a rotação também
            if (typeof viewer.set_rotation === 'function') {
              viewer.set_rotation(false);
            }
          } else {
            if (typeof viewer.set_rotation === 'function') {
              viewer.set_rotation(true);
            }
          }
        } catch (e) {
          console.warn('[STL] Erro ao alternar navegação livre:', e);
        }
      };
    }
    // botão "Centralizar Vista"
    if (centerViewBtn) {
      centerViewBtn.onclick = function () {
        try {
          if (typeof viewer.set_center_models === 'function') {
            viewer.set_center_models(true);
          }
          if (typeof viewer.set_auto_zoom === 'function') {
            viewer.set_auto_zoom();
          }
        } catch (e) {
          console.warn('[STL] Erro ao centralizar vista:', e);
        }
      };
    }
    // libera URL quando fechar o modal (se Bootstrap/jQuery estiverem presentes)
    if (global.$ && typeof global.$ === 'function') {
      $('#modal-dxf-viewer').one('hidden.bs.modal', function () {
        URL.revokeObjectURL(url);
        container.__stlViewer = null;
      });
    } else {
      // fallback simples: libera URL após alguns segundos
      setTimeout(function () {
        URL.revokeObjectURL(url);
      }, 30000);
    }
  }
  // expõe no escopo global
  global.renderizarSTL = renderizarSTL;
})(window);