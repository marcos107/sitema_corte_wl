// public/assets/dxf-viewer/dxf_viewer.js
// ------------------------------------------------------
// Responsável APENAS por renderizar DXF dentro do
// container indicado (padrão: #dxf-viewer-container)
//
// Requisitos globais:
// - window.THREE            (three.min.js da pasta viewstl)
// - THREE.OrbitControls     (OrbitControls.js da pasta viewstl)
// - window.DXFViewer        (exposto pelo main.umd.cjs + main.js via <script type="module">)
//
// Também usa, se existir:
// - window.VISUALIZAR_CONFIG.base_url  (configurado em visualizar.js)
//
// Uso típico (visualizar.js chama):
//   renderizarDXF(b64, {
//     containerId: 'dxf-viewer-container',
//     controlsCheckboxId: 'dxf-enable-controls',
//     centerButtonId: 'dxf-center-view-btn'
//   });
// ------------------------------------------------------

(function (global) {
  async function renderizarDXF(dxfBase64, opts) {
    opts = opts || {};

    const containerId        = opts.containerId        || 'dxf-viewer-container';
    const controlsCheckboxId = opts.controlsCheckboxId || 'dxf-enable-controls';
    const centerButtonId     = opts.centerButtonId     || 'dxf-center-view-btn';

    const container        = document.getElementById(containerId);
    const controlsCheckbox = document.getElementById(controlsCheckboxId);
    const centerViewBtn    = document.getElementById(centerButtonId);
    const titleElement     = document.getElementById('modal-dxf-title'); // se existir

    let modelUrl = null;
    let resizeObserver = null;

    // ---------- MONTA FONT URL com base no base_url do sistema ----------
    (function () {
      const cfg  = global.VISUALIZAR_CONFIG || {};
      const base = (cfg.base_url || '').replace(/\/+$/, ''); // tira barras finais
      // Caminho padrão assumindo estrutura: /public/assets/dxf-viewer/fonts/...
      global.DXF_VIEWER_CONFIG = global.DXF_VIEWER_CONFIG || {};
      global.DXF_VIEWER_CONFIG.fontUrl = base
        ? base + '/public/assets/dxf-viewer/fonts/helvetiker_regular.typeface.json'
        : '/public/assets/dxf-viewer/fonts/helvetiker_regular.typeface.json';
    })();

    const fontUrl =
      (global.DXF_VIEWER_CONFIG && global.DXF_VIEWER_CONFIG.fontUrl) ||
      '/public/assets/dxf-viewer/fonts/helvetiker_regular.typeface.json';

    try {
      if (!container) {
        throw new Error('Container DXF não encontrado: #' + containerId);
      }

      container.innerHTML = '';

      if (!global.DXFViewer || !global.THREE) {
        throw new Error('DXFViewer ou THREE.js não encontrados no escopo global.');
      }

      // ---------------- base64 -> Blob -> URL ----------------
      const pureB64 = dxfBase64.includes(',')
        ? dxfBase64.split(',')[1]
        : dxfBase64;

      const byteCharacters = atob(pureB64);
      const byteNumbers    = new Array(byteCharacters.length);

      for (let i = 0; i < byteCharacters.length; i++) {
        byteNumbers[i] = byteCharacters.charCodeAt(i);
      }

      const byteArray = new Uint8Array(byteCharacters.length);
      for (let i = 0; i < byteCharacters.length; i++) {
        byteArray[i] = byteCharacters.charCodeAt(i);
      }

      const blob      = new Blob([byteArray], { type: 'application/dxf' });
      modelUrl        = URL.createObjectURL(blob);

      console.log('[DXF] modelUrl:', modelUrl);
      console.log('[DXF] fontUrl :', fontUrl);

      // ---------------- DXF -> THREE.Object3D ----------------
      const viewer    = new global.DXFViewer();
      const dxfObject = await viewer.getFromPath(modelUrl, fontUrl);

      if (!dxfObject) {
        throw new Error('DXFViewer retornou objeto nulo.');
      }

      // ---------------- Monta cena THREE ----------------
      const width  = container.clientWidth  || 800;
      const height = container.clientHeight || 500;

      const renderer = new THREE.WebGLRenderer({
        antialias: true,
        alpha: true
      });

      renderer.setSize(width, height);
      renderer.setPixelRatio(global.devicePixelRatio || 1);

      container.appendChild(renderer.domElement);

      const scene = new THREE.Scene();
      scene.background = new THREE.Color(0x111111);
      scene.add(dxfObject);

      // -------------- Centralização e câmera ortográfica --------------
      const bbox   = new THREE.Box3().setFromObject(dxfObject);
      const center = bbox.getCenter(new THREE.Vector3());
      const size   = bbox.getSize(new THREE.Vector3());

      // centraliza modelo na origem
      dxfObject.position.sub(center);

      const viewSpan = Math.max(size.x, size.y, 1);

      const camera = new THREE.OrthographicCamera(
        -viewSpan / 2, viewSpan / 2,
         viewSpan / 2, -viewSpan / 2,
        0.1, 1000
      );

      camera.position.set(0, 0, 10);
      camera.updateProjectionMatrix();

      // -------------- Controles (pan/zoom) --------------
      const controls = new THREE.OrbitControls(camera, renderer.domElement);
      controls.enableRotate = false;   // sem rotação
      controls.enableZoom   = true;
      controls.enablePan    = true;
      controls.enableDamping = true;
      controls.dampingFactor = 0.1;

      // IMPORTANTE: não mexer em controls.mouseButtons aqui,
      // pois a versão antiga do OrbitControls não expõe esse objeto.

      if (controlsCheckbox) {
        controlsCheckbox.checked  = true;
        controls.enabled          = true;
        controlsCheckbox.onchange = function () {
          controls.enabled = controlsCheckbox.checked;
        };
      }

      if (centerViewBtn) {
        centerViewBtn.onclick = function () {
          controls.reset();
        };
      }

      // -------------- Loop de renderização --------------
      (function animate() {
        requestAnimationFrame(animate);
        if (controls.enabled) controls.update();
        renderer.render(scene, camera);
      })();

      // -------------- Responsividade --------------
      resizeObserver = new ResizeObserver(function () {
        const w = container.clientWidth;
        const h = container.clientHeight;
        if (!w || !h) return;

        renderer.setSize(w, h);

        const ar = w / h;
        const V  = Math.max(size.x, size.y, 1);
        camera.left   = -0.5 * V * ar;
        camera.right  =  0.5 * V * ar;
        camera.top    =  0.5 * V;
        camera.bottom = -0.5 * V;
        camera.updateProjectionMatrix();
      });

      resizeObserver.observe(container);

    } catch (e) {
      console.error('[DXF] Erro:', e);

      if (titleElement) {
        titleElement.textContent = 'Erro ao renderizar o DXF';
      }

      if (container) {
        container.innerHTML = (
          '<div class="alert alert-danger m-3">' +
            '<strong>Ocorreu um erro:</strong> ' + e.message +
          '</div>'
        );
      }
    } finally {
      if (modelUrl) {
        URL.revokeObjectURL(modelUrl);
      }
    }
  }

  // expõe no escopo global
  global.renderizarDXF = renderizarDXF;

})(window);
