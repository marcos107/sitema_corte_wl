<?php
/**
 * Biblioteca portátil — Fluxo de Adição de Desenhos (IND/MULT) em Modais
 *
 * Como usar no seu view:
 * 1) Inclua este arquivo (exemplo):
 *    <?= view('componentes/desenho_uploader_lib'); ?>
 *
 * 2) Em qualquer botão/ação do seu view:
 *    <button type="button" class="btn btn-outline-primary" onclick="add_arquivo_ind()">Adicionar Desenho (1)</button>
 *    <button type="button" class="btn btn-outline-primary" onclick="add_arquivo_mult()">Adicionar Desenhos (vários)</button>
 *
 * Observações:
 * - Reutiliza os mesmos endpoints que você já tem:
 *   public/processos_lista, public/criar_pasta_temp, public/desenho_adicionar_temp,
 *   public/desenho_adicionar_modal, public/desenhos_add, public/desenhos_add_uni, etc.
 * - Se o seu layout já possui os modais "modal" e "modal_cadastrar", este componente NÃO duplica.
 */
?>

<style>
/* Base do modal simples (mesmo padrão que você já usa em lista.php) */
.modal-1 {
    display: none;
    position: fixed;
    z-index: 1039;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
   /* background-color: #dbe4ee;*/
}
.no-scroll { overflow: hidden; }
.container-mesma-linha { display: flex; gap: 6px; }
#import_modal_add_desenho .modal-dialog { max-width: 760px; margin: 2rem auto; }
#import_modal_add_desenho .modal-content,
#modal .modal-content,
#modal_cadastrar .modal-content {
    background: #fff;
}
.wl-add-upload-shell { display: grid; gap: 1rem; }
.wl-add-upload-block {
    border: 1px solid #d9dee7;
    border-radius: 12px;
    padding: 1rem;
    background: #f8fafc;
}
.wl-add-upload-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    margin-bottom: .75rem;
}
.wl-add-upload-title {
    margin: 0;
    font-size: .95rem;
    font-weight: 600;
    color: #1f2937;
}
.wl-add-upload-subtitle {
    margin: .2rem 0 0 0;
    font-size: .8rem;
    color: #64748b;
}
.wl-add-upload-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 82px;
    padding: .25rem .6rem;
    border-radius: 999px;
    background: #dbeafe;
    color: #1d4ed8;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
}
.wl-add-upload-context {
    border-radius: 10px;
    padding: .8rem .95rem;
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #bfdbfe;
    font-size: .9rem;
}
.wl-add-upload-context.d-none { display: none; }
.wl-add-upload-process-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: .65rem;
}
.wl-add-upload-process-item {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .8rem .9rem;
    border: 1px solid #d9dee7;
    border-radius: 10px;
    background: #fff;
    margin: 0;
    cursor: pointer;
}
.wl-add-upload-process-item.is-selected {
    border-color: #2563eb;
    background: #eff6ff;
    box-shadow: 0 0 0 1px rgba(37, 99, 235, .08);
}
.wl-add-upload-process-item input { margin: 0; }
.wl-add-upload-process-label {
    margin: 0;
    font-weight: 500;
    color: #111827;
}
.wl-add-upload-files {
    margin-top: .85rem;
    min-height: 52px;
    border: 1px dashed #cbd5e1;
    border-radius: 10px;
    background: #fff;
    padding: .8rem .9rem;
    color: #475569;
    font-size: .88rem;
}
.wl-add-upload-files-title {
    font-weight: 600;
    color: #0f172a;
    margin-bottom: .35rem;
}
.wl-add-upload-file-list {
    display: grid;
    gap: .25rem;
}
.wl-add-upload-file-item {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.wl-upload-item {
    display: flex;
    align-items: center;
    gap: .75rem;
    border: 1px solid #e2e8f0;
    background: #fff;
    border-radius: .6rem;
    padding: .55rem .7rem;
}
.wl-upload-meta {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: .15rem;
    align-items: flex-start;
    flex: 1 1 auto;
}
.wl-upload-name {
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-weight: 600;
    color: #0f172a;
}
.wl-upload-remove {
    border: 0;
    width: 1.65rem;
    height: 1.65rem;
    border-radius: 999px;
    background: #fee2e2;
    color: #b91c1c;
    font-weight: 700;
    line-height: 1;
    cursor: pointer;
}
.wl-upload-remove:hover {
    background: #fecaca;
}
.wl-upload-empty {
    color: #64748b;
    font-size: .9rem;
    border: 1px dashed #cbd5e1;
    border-radius: .6rem;
    padding: .8rem;
    text-align: center;
}
.wl-add-upload-block input[type="file"] {
    background: #fff;
    border: 1px solid #cbd5e1;
}
#modal .modal-dialog.modal-xl {
    max-width: 96vw;
    width: 96vw;
    height: 94vh;
    margin: 3vh auto;
}
#modal .modal-content {
    height: 100%;
    max-height: 100%;
    min-height: 0;
    display: flex;
    flex-direction: column;
}
#modal .modal-body {
    flex: 1 1 auto;
    min-height: 0;
    overflow: auto;
}
.wl-add-meta-table-wrap {
    max-height: calc(94vh - 15rem);
    overflow: auto;
}
@media (max-width: 768px) {
    #modal .modal-dialog.modal-xl {
        width: 98vw;
        height: 98vh;
        margin: 1vh auto;
    }

    .wl-add-meta-table-wrap {
        max-height: calc(98vh - 13rem);
    }
}
.wl-add-meta-table th,
.wl-add-meta-table td {
    vertical-align: middle;
}
.wl-add-meta-description-block {
    display: grid;
    gap: .45rem;
    margin-top: 0;
}
.wl-add-meta-description-cell {
    background: #fff;
    vertical-align: top !important;
}
.wl-add-meta-description-label {
    margin: 0;
    font-size: .92rem;
    font-weight: 600;
    color: #1f2937;
}
.wl-add-meta-description-input {
    width: 100%;
    min-height: 160px;
    padding: .85rem .95rem;
    border: 1px solid #cbd5e1;
    border-radius: 12px;
    background: #fff;
    color: #111827;
    resize: vertical;
}
</style>

<!-- Modal 1: seleção do processo + upload (IND/MULT) -->
<div id="import_modal_add_desenho" class="modal-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="import_modal_add_desenho_titulo">Adicionar desenho</h5>
                <button type="button" class="close" onclick="fecharModal1('import_modal_add_desenho')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body" id="import_modal_add_desenho_bory">
                <div class="wl-add-upload-shell">
                    <div id="import_modal_add_desenho_context" class="wl-add-upload-context d-none"></div>

                    <div id="processos_desenho_select" class="wl-add-upload-block">
                        <div class="wl-add-upload-head">
                            <div>
                                <p class="wl-add-upload-title">Processo</p>
                                <p class="wl-add-upload-subtitle">Escolha em qual processo o arquivo sera cadastrado.</p>
                            </div>
                        </div>
                        <div id="processos_radio_desenho" class="wl-add-upload-process-grid"></div>
                    </div>

                    <div class="wl-add-upload-block">
                        <div class="wl-add-upload-head">
                            <div>
                                <p class="wl-add-upload-title">Arquivo(s)</p>
                                <p class="wl-add-upload-subtitle">Envie o arquivo e siga para o preenchimento dos dados.</p>
                            </div>
                            <span id="import_modal_add_desenho_badge" class="wl-add-upload-badge">MULT</span>
                        </div>
                        <input type="file" name="file" id="desenhos_add" class="form-control" multiple />
                        <small class="form-text text-muted" id="import_modal_add_desenho_hint"></small>
                        <div id="import_modal_add_desenho_files" class="wl-add-upload-files">Nenhum arquivo selecionado.</div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="fecharModal1('import_modal_add_desenho')">Cancelar</button>
                <button type="button" class="btn btn-primary" id="import_modal_add_desenho_confirmar" onclick="adicionar()">Continuar</button>
            </div>
        </div>
    </div>
</div>

<script>
/* =======================================================================
 * Bootstrap: garante que os modais base existam (modal + modal_cadastrar)
 * ======================================================================= */
(function() {
    function ensureBaseModal(id, titleId, bodyId, footerId, closeFn, confirmFn) {
        if (document.getElementById(id)) return;

        var modal = document.createElement('div');
        modal.id = id;
        modal.className = 'modal-1';

        modal.innerHTML = `
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="${titleId}"></h5>
                        <button type="button" class="close" onclick="${closeFn}('${id}')">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="${bodyId}"></div>
                    <div class="modal-footer" id="${footerId}">
                        <button type="button" class="btn btn-secondary" onclick="${closeFn}('${id}')">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="botao_confirmar_modal" onclick="${confirmFn}()"></button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }

    // modal principal (metadados / confirmação)
    ensureBaseModal('modal', 'modal_titulo', 'modal_bory', 'modal_rodape', 'fecharModal1', 'confirmarModal');

    // modal de cadastro (subpastas)
    if (!document.getElementById('modal_cadastrar')) {
        var m = document.createElement('div');
        m.id = 'modal_cadastrar';
        m.className = 'modal-1';
        m.innerHTML = `
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="modal_cadastrar_titulo"></h5>
                  <button type="button" class="close" onclick="fecharModal1('modal_cadastrar')">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body" id="modal_cadastrar_bory"></div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" onclick="fecharModal1('modal_cadastrar')">Cancelar</button>
                  <button type="button" class="btn btn-primary" id="botao_confirmar_modal_cadastrar" onclick="cadastrar()"></button>
                </div>
              </div>
            </div>
        `;
        document.body.appendChild(m);
    }

    // Funções básicas de modal, se ainda não existirem no view atual
    if (typeof window.mostrarModal1 !== 'function') {
        window.mostrarModal1 = function(class_var = "modal") {
            const modal = document.getElementById(class_var);
            if (!modal) return;
            modal.style.display = "block";
            document.body.classList.add("no-scroll");
        }
    }
    if (typeof window.fecharModal1 !== 'function') {
        window.fecharModal1 = function(class_var = "modal") {
            const modal = document.getElementById(class_var);
            if (!modal) return;
            modal.style.display = "none";
            const b = document.getElementById(class_var + '_bory');
            if (class_var === 'import_modal_add_desenho') {
                if (typeof window.import_reset_add_modal === 'function') {
                    window.import_reset_add_modal();
                }
            } else if (b) {
                b.innerHTML = '';
                if (class_var === 'modal' && typeof window.import_clear_runtime_state === 'function' && typeof window.import_modal_flow_is_active === 'function' && window.import_modal_flow_is_active()) {
                    window.import_clear_runtime_state();
                }
            }
            document.body.classList.remove("no-scroll");
        }
    }
})();
</script>

<script>
/* =======================================================================
 * API pública: add_arquivo_ind / add_arquivo_mult
 * ======================================================================= */
window.import_desenho_mode = 'mult';
window.processo_filtro = "";
window.desenho_dependencia_preselecao = null;
window.import_selected_files = [];

function import_escape_html(value) {
    return String(value || '').replace(/[&<>"']/g, function(char) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        }[char];
    });
}

function import_bind_add_modal_file_input() {
    var input = document.getElementById('desenhos_add');
    if (!input || input.dataset.wlBound === '1') {
        return;
    }

    input.addEventListener('change', function() {
        import_merge_selected_files(Array.from(input.files || []));
        import_render_selected_files();
    });
    input.dataset.wlBound = '1';
}

function import_file_key(file) {
    return [
        String(file && file.name ? file.name : ''),
        String(file && typeof file.size !== 'undefined' ? file.size : ''),
        String(file && typeof file.lastModified !== 'undefined' ? file.lastModified : ''),
        String(file && file.type ? file.type : '')
    ].join('::');
}

function import_format_file_size(bytes) {
    if (bytes < 1024) {
        return bytes + ' B';
    }

    if (bytes < 1024 * 1024) {
        return (bytes / 1024).toFixed(1) + ' KB';
    }

    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

function import_sync_input_files() {
    var input = document.getElementById('desenhos_add');
    var files = Array.isArray(window.import_selected_files) ? window.import_selected_files : [];
    if (!input || typeof DataTransfer === 'undefined') {
        return;
    }

    try {
        var dt = new DataTransfer();
        files.forEach(function(file) {
            dt.items.add(file);
        });
        input.files = dt.files;
    } catch (error) {
        // Browsers mais restritivos podem bloquear a escrita em input.files.
    }
}

function import_get_selected_files() {
    if (Array.isArray(window.import_selected_files) && window.import_selected_files.length > 0) {
        return window.import_selected_files.slice();
    }

    var input = document.getElementById('desenhos_add');
    return Array.from(input && input.files ? input.files : []);
}

function import_modal_flow_is_active() {
    return !!(
        document.getElementById('descricao_desenho') ||
        document.getElementById('prioridade_novo_todos') ||
        document.getElementById('empresa_cliente_novo_todos') ||
        (Array.isArray(window.import_selected_files) && window.import_selected_files.length > 0) ||
        (typeof window.desenho_dependencia_preselecao !== 'undefined' && window.desenho_dependencia_preselecao)
    );
}

function import_merge_selected_files(newFiles) {
    var arquivosNovos = Array.isArray(newFiles) ? newFiles.filter(Boolean) : [];
    if (arquivosNovos.length === 0) {
        return import_get_selected_files();
    }

    var arquivosAtuais = Array.isArray(window.import_selected_files) ? window.import_selected_files.slice() : [];
    var chaves = new Set(arquivosAtuais.map(import_file_key));

    arquivosNovos.forEach(function(file) {
        var chave = import_file_key(file);
        if (!chaves.has(chave)) {
            arquivosAtuais.push(file);
            chaves.add(chave);
        }
    });

    window.import_selected_files = arquivosAtuais;
    import_sync_input_files();
    return arquivosAtuais.slice();
}

function import_clear_runtime_state() {
    var inp = document.getElementById('desenhos_add');
    window.import_selected_files = [];
    window.desenho_dependencia_preselecao = null;
    window.processo_filtro = "";
    window.desenhos = [];
    window.lista_array = [];
    processo_desenho_nome = '';
    tipo_input = '';

    if (inp) {
        inp.value = '';
    }

    if (typeof wlResetDependenciaBootstrapState === 'function') {
        wlResetDependenciaBootstrapState();
    }

    import_sync_input_files();
    import_render_selected_files();
    import_update_add_modal_context();
}

function import_remove_selected_file(index) {
    if (!Array.isArray(window.import_selected_files) || index < 0 || index >= window.import_selected_files.length) {
        return;
    }

    window.import_selected_files.splice(index, 1);
    import_sync_input_files();
    import_render_selected_files();
}

function import_render_selected_files() {
    var container = document.getElementById('import_modal_add_desenho_files');
    if (!container) {
        return;
    }

    var files = import_get_selected_files();
    if (files.length === 0) {
        if (window.import_desenho_mode === 'ind') {
            container.innerHTML = '<div class="wl-upload-empty">Nenhum arquivo selecionado ainda.</div>';
        } else {
            container.textContent = 'Nenhum arquivo selecionado.';
        }
        return;
    }

    if (window.import_desenho_mode === 'ind') {
        container.innerHTML = '';

        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var item = document.createElement('div');
            item.className = 'wl-upload-item';

            var meta = document.createElement('div');
            meta.className = 'wl-upload-meta';

            var name = document.createElement('span');
            name.className = 'wl-upload-name';
            name.textContent = file.name;

            var size = document.createElement('small');
            size.className = 'text-muted';
            size.textContent = import_format_file_size(file.size);

            var removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'wl-upload-remove';
            removeBtn.title = 'Remover arquivo';
            removeBtn.setAttribute('aria-label', 'Remover arquivo ' + file.name);
            removeBtn.textContent = 'X';

            (function(fileIndex) {
                removeBtn.addEventListener('click', function() {
                    import_remove_selected_file(fileIndex);
                });
            })(i);

            meta.appendChild(name);
            meta.appendChild(size);
            item.appendChild(meta);
            item.appendChild(removeBtn);
            container.appendChild(item);
        }
        return;
    }

    var itens = files.slice(0, 5).map(function(file) {
        return '<div class="wl-add-upload-file-item">' + import_escape_html(file.name) + '</div>';
    }).join('');

    var resto = files.length > 5
        ? '<div class="text-muted small mt-1">+' + (files.length - 5) + ' arquivo(s) adicional(is).</div>'
        : '';

    container.innerHTML = '<div class="wl-add-upload-files-title">' + files.length + ' arquivo(s) selecionado(s)</div><div class="wl-add-upload-file-list">' + itens + '</div>' + resto;
}

function import_update_process_visual_state() {
    var wrappers = document.querySelectorAll('#processos_radio_desenho .wl-add-upload-process-item');
    wrappers.forEach(function(wrapper) {
        var radio = wrapper.querySelector("input[name='processo']");
        wrapper.classList.toggle('is-selected', !!(radio && radio.checked));
    });
}

function import_update_add_modal_context() {
    var title = document.getElementById('import_modal_add_desenho_titulo');
    var hint = document.getElementById('import_modal_add_desenho_hint');
    var badge = document.getElementById('import_modal_add_desenho_badge');
    var context = document.getElementById('import_modal_add_desenho_context');
    var button = document.getElementById('import_modal_add_desenho_confirmar');
    var input = document.getElementById('desenhos_add');
    var processoTravado = typeof window.processo_filtro !== 'undefined' ? String(window.processo_filtro || '') : '';
    var modoInd = window.import_desenho_mode === 'ind';

    if (title) {
        title.textContent = processoTravado !== ''
            ? 'Adicionar arquivos da dependencia'
            : 'Adicionar arquivos';
    }

    if (badge) {
        badge.textContent = modoInd ? 'IND' : 'MULT';
    }

    if (button) {
        button.textContent = 'Continuar';
    }

    if (input) {
        input.multiple = true;
        input.setAttribute('multiple', 'multiple');
    }

    if (hint) {
        hint.textContent = modoInd
            ? 'Modo IND: selecione um ou mais arquivos. Voce pode adicionar mais arquivos de outras pastas e todos usarao o mesmo preenchimento.'
            : 'Modo MULT: selecione um ou mais arquivos, inclusive em selecoes sucessivas de outras pastas.';
    }

    if (context) {
        if (processoTravado !== '') {
            context.textContent = 'Dependencia ativa: o processo ' + processoTravado + ' ja esta definido para este envio.';
            context.classList.remove('d-none');
        } else {
            context.textContent = '';
            context.classList.add('d-none');
        }
    }
}

function add_arquivo_ind(processo) {
    import_clear_runtime_state();
    window.processo_filtro = processo || "";
    window.import_desenho_mode = 'ind';
    import_open_add_modal();
}

function add_arquivo_mult(processo) {
    import_clear_runtime_state();
    window.import_desenho_mode = 'mult';
    window.processo_filtro = processo || "";
    import_open_add_modal();
}

function import_open_add_modal() {
    import_bind_add_modal_file_input();
    import_update_add_modal_context();
    import_render_selected_files();
    // Ajusta múltiplo/único
    var inp = document.getElementById('desenhos_add');


    // Hint
    var hint = document.getElementById('import_modal_add_desenho_hint');
    if (hint) {
        hint.textContent = (window.import_desenho_mode === 'ind')
            ? 'Modo IND: selecione um ou mais arquivos. Voce pode adicionar mais arquivos de outras pastas e todos usarao o mesmo preenchimento.'
            : 'Modo MULT: selecione um ou mais arquivos, inclusive em selecoes sucessivas de outras pastas.';
    }

    // Carrega processos e abre
    mostrarModal1('import_modal_add_desenho');
    setTimeout(function() {
        try {
            if (typeof processo_lista_desenho === 'function') {
                processo_lista_desenho(window.import_desenho_mode);
            }
        } catch (error) {
            console.error('Falha ao carregar processos do modal de upload:', error);
            if (typeof alert_personalizado === 'function') {
                alert_personalizado('Desenho', 'O modal abriu, mas houve erro ao carregar os processos. Recarregue a tela e tente novamente.');
            }
        }
    }, 0);
}

function import_reset_add_modal() {
    // Reseta input + recarrega processos para o modo atual
    var inp = document.getElementById('desenhos_add');
    if (inp) inp.value = '';
    window.import_selected_files = [];
    import_render_selected_files();
    import_update_add_modal_context();
    processo_lista_desenho(window.import_desenho_mode);
}
/* =======================================================================
 * A partir daqui: JS original (desenho_adicionar_ajax.php) com patches
 * - processo_lista_desenho(filtro_input)
 * - inicio_tela() -> import_reset_add_modal()
 * - adicionar() -> fluxo em modal
 * - reset de input após confirmação -> import_reset_add_modal()
 * ======================================================================= */
</script>

<script>
function get_radio() {
        var radios = document.getElementsByName('processo_desenho'); // Seleciona todos os botões de rádio com o nome 'processo'
        var processo_var = '';

        // Itera sobre todos os botões de rádio para encontrar o selecionado
        for (var i = 0; i < radios.length; i++) {
            if (radios[i].checked) {
                return radios[i]; // Captura o valor do botão de rádio selecionado
                break; // Sai do loop após encontrar o botão selecionado
            }
        }
        return processo_var;
    }

    function inicio_tela() { import_reset_add_modal(); }



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


    function getFiltroPorNome(lista, nomeProcesso) {
        if (!Array.isArray(lista) || !nomeProcesso) return null;

        const nomeNormalizado = (typeof normalizarValorPreselecaoDependencia === 'function')
            ? normalizarValorPreselecaoDependencia(nomeProcesso)
            : String(nomeProcesso || '').trim().toLowerCase();
        const proc = lista.find(function(p) {
            if (!p || typeof p.nome === 'undefined') {
                return false;
            }

            if (p.nome === nomeProcesso) {
                return true;
            }

            if (typeof normalizarValorPreselecaoDependencia === 'function') {
                return normalizarValorPreselecaoDependencia(p.nome) === nomeNormalizado;
            }

            return String(p.nome || '').trim().toLowerCase() === nomeNormalizado;
        });
        return proc ? proc.filtro : null;
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

    function adicionarCampoDescricaoProjeto(tabela, nomesArquivos, preselecoesBrutas = null) {
        if (!tabela || document.getElementById('descricao_desenho')) {
            return;
        }

        var nomes = Array.isArray(nomesArquivos)
            ? nomesArquivos.filter(function(nome) {
                return String(nome || '').trim() !== '';
            })
            : [];
        var preselecoes = null;
        if (typeof normalizarPreselecoesDependencia === 'function') {
            preselecoes = normalizarPreselecoesDependencia(preselecoesBrutas);
        } else {
            preselecoes = preselecoesBrutas || null;
        }

        var trDescricao = document.createElement('tr');
        var thDescricao = document.createElement('th');
        thDescricao.style.fontWeight = 'normal';
        thDescricao.style.fontSize = '16px';
        thDescricao.textContent = nomes.length > 0 ? nomes[0] : '';
        trDescricao.appendChild(thDescricao);

        var tdDescricao = document.createElement('td');
        tdDescricao.colSpan = 7;
        tdDescricao.rowSpan = Math.max(1, nomes.length);
        tdDescricao.className = 'wl-add-meta-description-cell';

        var bloco = document.createElement('div');
        bloco.className = 'wl-add-meta-description-block';

        var label = document.createElement('label');
        label.className = 'wl-add-meta-description-label';
        label.setAttribute('for', 'descricao_desenho');
        label.textContent = 'Descricao do projeto';

        var textarea = document.createElement('textarea');
        textarea.id = 'descricao_desenho';
        textarea.className = 'wl-add-meta-description-input';
        textarea.placeholder = 'Escreva a descricao do projeto';
        textarea.value = preselecoes && preselecoes.descricao ? preselecoes.descricao : '';
        textarea.style.minHeight = Math.max(160, nomes.length * 52) + 'px';

        bloco.appendChild(label);
        bloco.appendChild(textarea);
        tdDescricao.appendChild(bloco);
        trDescricao.appendChild(tdDescricao);
        tabela.appendChild(trDescricao);
    }


    processos_desenho_select = "";
    filtro = "";
    function processo_lista_desenho(filtro_input = null) {
        if (typeof window.processo_filtro !== 'undefined' && window.processo_filtro !== '') {

            const divSelect = document.getElementById('processos_desenho_select');
            if (divSelect) {
                divSelect.style.display = 'none';
            }

       
        }else{
            const divSelect = document.getElementById('processos_desenho_select');
            if (divSelect) {
                    divSelect.style.display = 'block';
            }
        }
        
        $.ajax({
            url: '<?= base_url('public/processos_lista') ?>',
            type: "POST",
            data: { contexto_tela: 'desenho_adicionar' },
            dataType: "json", // Indicar que o retorno é em formato JSON
            async: false, // Define a requisição como síncrona

            success: function(response) {
                processos_desenho_select = response.lista;
                filtro = getFiltroPorNome(processos_desenho_select, window.processo_filtro);
                const input = document.getElementById('desenhos_add');
                if (input) {
                    input.setAttribute('accept', filtro);
                }
                temp_response = response;
                console.log(response);
                // Verifica se o elemento <div> existe
                var radioContainer = document.getElementById('processos_radio_desenho');
                if (!radioContainer)
                    return;

                // Limpa os elementos de rádio existentes na <div>
                radioContainer.innerHTML = '';
                var cont = 0;
                // Itera sobre cada processo na lista
                processos_desenho_select.forEach(function(processo, index) {
                    if (filtro_input && processo.input !== filtro_input) { return; }
                    //console.log(processo);
                    var temp = 'processo_desenho_' + processo.input + '_' + index;
                    // Cria um novo elemento <input> para o botão de rádio
                    var radioElement = document.createElement('input');
                    radioElement.type = 'radio';
                    radioElement.name = 'processo'; // Define o mesmo nome para agrupar os botões de rádio
                    radioElement.id = 'processo_desenho_' + processo.input + '_' + index; // Define um ID único para cada botão de rádio
                    radioElement.value = processo.nome; // Define o nome do processo como o valor do botão

                    // Cria um <label> para o botão de rádio
                    var labelElement = document.createElement('label');
                    labelElement.htmlFor = 'processo_desenho_' + index; // Associa o label ao botão de rádio
                    labelElement.textContent = processo.nome; // Define o nome do processo como o texto do label
                    labelElement.style.fontWeight = 'normal'; // Remove o negrito do texto

                    // Cria um <span> para envolver o rádio e o label, mantendo-os juntos horizontalmente
                    var spanElement = document.createElement('span');
                    spanElement.style.marginRight = '15px'; // Adiciona espaço entre os botões de rádio
                    spanElement.appendChild(radioElement);
                    spanElement.appendChild(labelElement);

                    // Adiciona o <span> à <div>
                    radioContainer.appendChild(spanElement);
                    if (cont == 0) {
                        cont++
                        document.getElementById(temp).checked = true;
                    }
                });

            }
        });
    } // Função para pegar o filtro baseado no nome do processo
    function getFiltroByNome(nome) {

        // Procura pelo processo no array global
        for (var i = 0; i < processos_desenho_select.length; i++) {
            var nomeAtual = processos_desenho_select[i].nome;
            var mesmoNome = nomeAtual === nome;

            if (!mesmoNome && typeof normalizarValorPreselecaoDependencia === 'function') {
                mesmoNome = normalizarValorPreselecaoDependencia(nomeAtual) === normalizarValorPreselecaoDependencia(nome);
            }

            if (mesmoNome) {
                var desenhos = document.querySelectorAll('[id="desenhos_add"]');

                // Itera sobre todos os elementos encontrados
                desenhos.forEach(function(desenho) {
                    // Armazenar o valor da opção selecionada antes de limpar o select
                    desenho.accept = processos_desenho_select[i].filtro;
                    // Você pode remover o `return` aqui, pois estamos aplicando a mudança a todos os elementos
                });
                return; // Retorna o filtro associado ao nome
            }
        }

    }
    var processo_desenho_nome = '';
    var tipo_input = '';

    
function adicionar() {
        if (typeof window.processo_filtro !== 'undefined' && window.processo_filtro !== '') {
            processo_desenho_nome = window.processo_filtro;
        }else{
        // Passo único: já estamos em modal (processo + upload). Apenas envia para temp e abre o modal de metadados.
        var temp = get_radio();
        if (!temp) {
            alert_personalizado('Desenho', 'Selecione um processo.');
            return;
        }

        processo_desenho_nome = temp;
        }

        tipo_input = window.import_desenho_mode;

        var fileInput = document.getElementById('desenhos_add');
        if (!fileInput) {
            alert_personalizado('Desenho', 'Componente de upload não encontrado (#desenhos_add).');
            return;
        }

        var files = import_get_selected_files();
        if (!files || files.length === 0) {
            alert_personalizado('Desenho', 'Selecione um arquivo antes de adicioná-lo.');
            return;
        }



        $.ajax({
            url: '<?= base_url('public/criar_pasta_temp') ?>',
            type: "POST",
            dataType: "json",
            success: function(response) {
                if (response.ok != 'true') {
                    alert_personalizado("Desenho", 'Erro ao criar pasta temporária.');
                    return;
                }

                // Envia cada arquivo para a pasta temp
                for (var i = 0; i < files.length; i++) {
                    var file = files[i];
                    if (!file) continue;

                    var formData = new FormData();
                    formData.append('file', file);

                    $.ajax({
                        url: '<?= site_url('public/desenho_adicionar_temp') ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: formData,
                        processData: false,
                        async: false,
                        contentType: false,
                        error: function() {
                            alert_personalizado("Desenho", 'Erro ao enviar o arquivo.');
                        }
                    });
                }

                // Fecha o modal de seleção/upload e abre o modal de metadados
                fecharModal1('import_modal_add_desenho');
                if (tipo_input == 'mult') {
                    desenho_modal();
                } else {
                    desenho_modal_ind();
                }
            }
        });
    }


function desenho_modal_ind() {
    
        $.ajax({
            url: '<?= site_url('public/desenho_adicionar_modal') ?>',
            type: 'POST',
            dataType: 'json',

            data: {
                nome_processos: processo_desenho_nome,
                dependencia_ativa: (typeof window.processo_filtro !== 'undefined' && window.processo_filtro !== '') ? '1' : '0'
            },
            success: function(response) {
                desenhos = response.desenhos;
                lista_array = response;
                var botao_confirmar_modal = document.getElementById('botao_confirmar_modal');
                botao_confirmar_modal.innerHTML = "Confirmar";
                var modal_titulo = document.getElementById('modal_titulo');
                var modal_bory = document.getElementById('modal_bory');
                modal_bory.innerHTML = '';
                modal_titulo.textContent = (typeof window.processo_filtro !== 'undefined' && window.processo_filtro !== '')
                    ? "Adicionar arquivos da dependencia"
                    : "Adicionar arquivos";
                tabel_bory = document.createElement("table");

                tabel_bory.setAttribute('border', '1');



                tr = document.createElement('tr');
                th = document.createElement('th');
                th.innerHTML = 'Nome';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Prioridade';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Empresa/Cliente';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Empreendimento';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Finalidade';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Subpasta-01';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Subpasta-02';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Subpasta-03';
                tr.appendChild(th);
                tr.classList.add()
                tabel_bory.appendChild(tr);
                ////////////////////////////////////// começo 
                tr = document.createElement('tr');
                th = document.createElement('th');

                th.style.fontWeight = 'normal';
                th.innerHTML = '*';
                tr.appendChild(th);
                //selecte prioridade
                selectElement = document.createElement("select");
                selectElement.id = 'prioridade_novo_todos';
                selectElement.classList.add("custom-select");



                th = document.createElement('th');

                th.appendChild(selectElement);
                tr.appendChild(th); //coloca o input name no modal



                //selecte empresa

                selectElement = document.createElement("select");
                selectElement.id = 'empresa_cliente_novo_todos';
                selectElement.addEventListener("change", function() {
                    var selectedValue = this.value; // Valor da opção selecionada
                    var selectedIndex1 = this.selectedIndex; // Índice da opção selecionada

                    // Chame a função que deseja executar quando uma opção é selecionada
                    value_empreendimento_c(selectedValue, selectedIndex1, desenhos.length);

                    for (let j = 0; j < desenhos.length; j++) {
                        if (document.getElementById("empresa_cliente_novo_" + j)) {
                            document.getElementById("empresa_cliente_novo_" + j).selectedIndex = selectedIndex1;
                        }
                    }

                });
                selectElement.classList.add("custom-select");



                th = document.createElement('th');
                th.appendChild(selectElement);
                tr.appendChild(th);


                //coloca o input name no modal




                //selecte empreendimento
                selectElement = document.createElement("select");
                selectElement.id = 'empreendimento_novo_todos';
                selectElement.classList.add("custom-select");
                selectElement.addEventListener("change", function() {
                    value_tags(true);
                    value_tags_c(true);
                });


                var novoOption = document.createElement("option");
                selectElement.disabled = true;
                novoOption.value = '';
                novoOption.textContent = 'Empreendimento';
                selectElement.appendChild(novoOption);

                th = document.createElement('th');

                th.appendChild(selectElement);
                tr.appendChild(th); //coloca o input name no modal



                //selecte finalidade
                selectElement = document.createElement("select");
                selectElement.id = 'finalidade_novo_todos';
                selectElement.classList.add("custom-select");
                selectElement.addEventListener("change", function() {
                    value_tags(true);
                    value_tags_c(true);
                });


                th = document.createElement('th');

                th.appendChild(selectElement);
                tr.appendChild(th); //coloca o input name no modal



                //selecte tag
                selectElement = document.createElement("select");
                selectElement.id = 'tag1_novo_todos';
                selectElement.disabled = true;
                selectElement.classList.add("custom-select");



                th = document.createElement('th');
                div = document.createElement('div');
                div.classList.add("container-mesma-linha");
                div.appendChild(selectElement);
                div.innerHTML += '<button id="tag1_botao_todos" name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="adicinar_subpasta(\'empreendimento_novo_todos\',\'finalidade_novo_todos\')">+</button>';

                th.appendChild(div);

                tr.appendChild(th); //coloca o input name no modal

                //selecte tag
                selectElement = document.createElement("select");
                selectElement.id = 'tag2_novo_todos';
                selectElement.classList.add("custom-select");



                th = document.createElement('th');
                div = document.createElement('div');
                div.classList.add("container-mesma-linha");
                div.appendChild(selectElement);
                div.innerHTML += '<button id="tag2_botao_todos" name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="adicinar_subpasta(\'empreendimento_novo_todos\',\'finalidade_novo_todos\')">+</button>';

                th.appendChild(div);


                tr.appendChild(th); //coloca o input name no modal

                //selecte tag
                selectElement = document.createElement("select");
                selectElement.id = 'tag3_novo_todos';
                selectElement.classList.add("custom-select");



                th = document.createElement('th');

                div = document.createElement('div');
                div.classList.add("container-mesma-linha");
                div.appendChild(selectElement);
                div.innerHTML += '<button id="tag3_botao_todos" name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="adicinar_subpasta(\'empreendimento_novo_todos\',\'finalidade_novo_todos\')">+</button>';

                th.appendChild(div);

                tr.appendChild(th); //coloca o input name no modal

                tabel_bory.appendChild(tr);
                adicionarCampoDescricaoProjeto(
                    tabel_bory,
                    desenhos,
                    lista_array && lista_array.preselecoes ? lista_array.preselecoes : null
                );





                for (i = (desenhos.length > 0 ? 1 : 0); i < desenhos.length; i++) {

                    tr = document.createElement('tr');
                    tr.id = "desenho_" + i;
                    if (i % 2 == 0) {
                        tr.classList.add('odd');
                    } else {
                        tr.classList.add('even');
                    }


                    //nome
                    th = document.createElement('th');
                    th.style.fontWeight = 'normal';
                    th.style.fontSize = '16px';
                    th.innerHTML = desenhos[i];


                    tr.appendChild(th);
                    tabel_bory.appendChild(tr);










                }

                tabel_bory.classList.add('table', 'table-bordered', 'table-striped', 'wl-add-meta-table');
                var tabelaWrapper = document.createElement('div');
                tabelaWrapper.className = 'wl-add-meta-table-wrap';
                tabelaWrapper.appendChild(tabel_bory);
                modal_bory.appendChild(tabelaWrapper);
                selects();
                mostrarModal1();

            },
            error: function() {
                alert_personalizado('Desenho', 'Erro ao enviar o arquivo.');

            }
        });

    }


    desenhos = [];
    lista_array = [];

    function desenho_modal() {
        $.ajax({
            url: '<?= site_url('public/desenho_adicionar_modal') ?>',
            type: 'POST',
            dataType: 'json',

            data: {
                nome_processos: processo_desenho_nome,
                dependencia_ativa: (typeof window.processo_filtro !== 'undefined' && window.processo_filtro !== '') ? '1' : '0'
            },
            success: function(response) {
                desenhos = response.desenhos;
                lista_array = response;
                var botao_confirmar_modal = document.getElementById('botao_confirmar_modal');
                botao_confirmar_modal.innerHTML = "Confirmar";
                var modal_titulo = document.getElementById('modal_titulo');
                var modal_bory = document.getElementById('modal_bory');
                modal_bory.innerHTML = '';
                modal_titulo.textContent = (typeof window.processo_filtro !== 'undefined' && window.processo_filtro !== '')
                    ? "Adicionar arquivos da dependencia"
                    : "Adicionar desenhos";
                tabel_bory = document.createElement("table");

                tabel_bory.setAttribute('border', '1');



                tr = document.createElement('tr');
                th = document.createElement('th');
                th.innerHTML = 'Nome';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Prioridade';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Empresa/Cliente';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Empreendimento';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Finalidade';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Subpasta-01';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Subpasta-02';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Subpasta-03';
                tr.appendChild(th);
                tr.classList.add()
                tabel_bory.appendChild(tr);
                ////////////////////////////////////// começo 
                tr = document.createElement('tr');
                th = document.createElement('th');

                th.style.fontWeight = 'normal';
                th.innerHTML = '*';
                tr.appendChild(th);
                //selecte prioridade
                selectElement = document.createElement("select");
                selectElement.id = 'prioridade_novo_todos';
                selectElement.classList.add("custom-select");



                th = document.createElement('th');

                th.appendChild(selectElement);
                tr.appendChild(th); //coloca o input name no modal



                //selecte empresa

                selectElement = document.createElement("select");
                selectElement.id = 'empresa_cliente_novo_todos';
                selectElement.addEventListener("change", function() {
                    var selectedValue = this.value; // Valor da opção selecionada
                    var selectedIndex1 = this.selectedIndex; // Índice da opção selecionada

                    // Chame a função que deseja executar quando uma opção é selecionada
                    value_empreendimento_c(selectedValue, selectedIndex1, desenhos.length);

                    for (let j = 0; j < desenhos.length; j++) {
                        if (document.getElementById("empresa_cliente_novo_" + j)) {
                            document.getElementById("empresa_cliente_novo_" + j).selectedIndex = selectedIndex1;
                        }
                    }

                });
                selectElement.classList.add("custom-select");



                th = document.createElement('th');
                th.appendChild(selectElement);
                tr.appendChild(th);


                //coloca o input name no modal




                //selecte empreendimento
                selectElement = document.createElement("select");
                selectElement.id = 'empreendimento_novo_todos';
                selectElement.classList.add("custom-select");
                selectElement.addEventListener("change", function() {
                    value_tags(true);
                    value_tags_c(true);
                });


                var novoOption = document.createElement("option");
                selectElement.disabled = true;
                novoOption.value = '';
                novoOption.textContent = 'Empreendimento';
                selectElement.appendChild(novoOption);

                th = document.createElement('th');

                th.appendChild(selectElement);
                tr.appendChild(th); //coloca o input name no modal



                //selecte finalidade
                selectElement = document.createElement("select");
                selectElement.id = 'finalidade_novo_todos';
                selectElement.classList.add("custom-select");
                selectElement.addEventListener("change", function() {
                    value_tags(true);
                    value_tags_c(true);
                });


                th = document.createElement('th');

                th.appendChild(selectElement);
                tr.appendChild(th); //coloca o input name no modal



                //selecte tag
                selectElement = document.createElement("select");
                selectElement.id = 'tag1_novo_todos';
                selectElement.disabled = true;
                selectElement.classList.add("custom-select");



                th = document.createElement('th');
                div = document.createElement('div');
                div.classList.add("container-mesma-linha");
                div.appendChild(selectElement);
                div.innerHTML += '<button id="tag1_botao_todos" name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="adicinar_subpasta(\'empreendimento_novo_todos\',\'finalidade_novo_todos\')">+</button>';

                th.appendChild(div);

                tr.appendChild(th); //coloca o input name no modal

                //selecte tag
                selectElement = document.createElement("select");
                selectElement.id = 'tag2_novo_todos';
                selectElement.classList.add("custom-select");



                th = document.createElement('th');
                div = document.createElement('div');
                div.classList.add("container-mesma-linha");
                div.appendChild(selectElement);
                div.innerHTML += '<button id="tag2_botao_todos" name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="adicinar_subpasta(\'empreendimento_novo_todos\',\'finalidade_novo_todos\')">+</button>';

                th.appendChild(div);


                tr.appendChild(th); //coloca o input name no modal

                //selecte tag
                selectElement = document.createElement("select");
                selectElement.id = 'tag3_novo_todos';
                selectElement.classList.add("custom-select");



                th = document.createElement('th');

                div = document.createElement('div');
                div.classList.add("container-mesma-linha");
                div.appendChild(selectElement);
                div.innerHTML += '<button id="tag3_botao_todos" name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="adicinar_subpasta(\'empreendimento_novo_todos\',\'finalidade_novo_todos\')">+</button>';

                th.appendChild(div);

                tr.appendChild(th); //coloca o input name no modal

                tabel_bory.appendChild(tr);





                for (i = 0; i < desenhos.length; i++) {

                    tr = document.createElement('tr');
                    tr.id = "desenho_" + i;
                    if (i % 2 == 0) {
                        tr.classList.add('odd');
                    } else {
                        tr.classList.add('even');
                    }


                    //nome
                    th = document.createElement('th');
                    th.style.fontWeight = 'normal';
                    th.style.fontSize = '16px';
                    th.innerHTML = desenhos[i];


                    tr.appendChild(th);








                    //selecte prioridade
                    selectElement = document.createElement("select");
                    selectElement.id = 'prioridade_novo_' + i;
                    selectElement.classList.add("custom-select");



                    th = document.createElement('th');
                    th.appendChild(selectElement);
                    tr.appendChild(th); //coloca o input name no modal


                    //selecte empresa
                    selectElement = document.createElement("select");
                    selectElement.id = 'empresa_cliente_novo_' + i;
                    selectElement.classList.add("custom-select");

                    th = document.createElement('th');
                    th.appendChild(selectElement);
                    tr.appendChild(th);


                    //coloca o input name no modal




                    //selecte empreendimento
                    selectElement = document.createElement("select");
                    selectElement.id = 'empreendimento_novo_' + i;
                    selectElement.classList.add("custom-select");
                    selectElement.addEventListener("change", function() {
                        value_tags(true);
                        value_tags_c(true);
                    });

                    var novoOption = document.createElement("option");
                    selectElement.disabled = true;
                    novoOption.value = '';
                    novoOption.textContent = 'Empreendimento';
                    selectElement.appendChild(novoOption);

                    th = document.createElement('th');

                    th.appendChild(selectElement);
                    tr.appendChild(th); //coloca o input name no modal



                    //selecte finalidade
                    selectElement = document.createElement("select");
                    selectElement.id = 'finalidade_novo_' + i;
                    selectElement.classList.add("custom-select");
                    selectElement.addEventListener("change", function() {
                        value_tags(true);
                        value_tags_c(true);
                    });


                    th = document.createElement('th');

                    th.appendChild(selectElement);
                    tr.appendChild(th); //coloca o input name no modal




                    th = document.createElement('th');
                    //selecte tag
                    selectElement = document.createElement("select");
                    selectElement.id = 'tag1_novo_' + i;

                    selectElement.classList.add("custom-select");

                    div = document.createElement('div');
                    div.classList.add("container-mesma-linha");
                    div.appendChild(selectElement);
                    div.innerHTML += '<button id="tag1_botao_' + +i + '" name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="adicinar_subpasta(\'empreendimento_novo_' + i + '\',\'finalidade_novo_' + i + '\')">+</button>';

                    th.appendChild(div);


                    tr.appendChild(th); //coloca o input name no modal
                    th = document.createElement('th');
                    //selecte tag
                    selectElement = document.createElement("select");
                    selectElement.id = 'tag2_novo_' + i;
                    selectElement.classList.add("custom-select");



                    div = document.createElement('div');
                    div.classList.add("container-mesma-linha");
                    div.appendChild(selectElement);
                    div.innerHTML += '<button id="tag2_botao_' + +i + '" name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="adicinar_subpasta(\'empreendimento_novo_' + i + '\',\'finalidade_novo_' + i + '\')">+</button>';

                    th.appendChild(div);


                    tr.appendChild(th); //coloca o input name no modal
                    th = document.createElement('th');
                    //selecte tag
                    selectElement = document.createElement("select");
                    selectElement.id = 'tag3_novo_' + i;
                    selectElement.classList.add("custom-select");



                    div = document.createElement('div');
                    div.classList.add("container-mesma-linha");
                    div.appendChild(selectElement);
                    div.innerHTML += '<button id="tag3_botao_' + +i + '" name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="adicinar_subpasta(\'empreendimento_novo_' + i + '\',\'finalidade_novo_' + i + '\')">+</button>';

                    th.appendChild(div);


                    tr.appendChild(th); //coloca o input name no modal

                    tabel_bory.appendChild(tr);










                }

                tabel_bory.classList.add('table', 'table-bordered', 'table-striped', 'wl-add-meta-table');
                var tabelaWrapper = document.createElement('div');
                tabelaWrapper.className = 'wl-add-meta-table-wrap';
                tabelaWrapper.appendChild(tabel_bory);
                modal_bory.appendChild(tabelaWrapper);
                selects();
                mostrarModal1();

            },
            error: function() {
                alert_personalizado('Desenho', 'Erro ao enviar o arquivo.');

            }
        });

    }

    var wlImportLookupCache = {
        prioridade: { value: null, pending: null },
        tags: { value: null, pending: null },
        finalidade: { value: null, pending: null },
        empresa: { value: null, pending: null },
        empreendimento: { values: {}, pending: {} }
    };

    function wlImportNormalizeCacheKey(valor) {
        return String(valor || '__default__').trim().toLowerCase();
    }

    function wlImportCachedRequest(tipo, chave, requestFactory) {
        if (tipo === 'empreendimento') {
            var keyEmpreendimento = wlImportNormalizeCacheKey(chave);
            if (Object.prototype.hasOwnProperty.call(wlImportLookupCache.empreendimento.values, keyEmpreendimento)) {
                return $.Deferred().resolve(wlImportLookupCache.empreendimento.values[keyEmpreendimento]).promise();
            }

            if (wlImportLookupCache.empreendimento.pending[keyEmpreendimento]) {
                return wlImportLookupCache.empreendimento.pending[keyEmpreendimento];
            }

            var requestEmpreendimento = requestFactory();
            wlImportLookupCache.empreendimento.pending[keyEmpreendimento] = requestEmpreendimento;
            requestEmpreendimento.done(function(response) {
                wlImportLookupCache.empreendimento.values[keyEmpreendimento] = response;
            }).always(function() {
                delete wlImportLookupCache.empreendimento.pending[keyEmpreendimento];
            });

            return requestEmpreendimento;
        }

        if (wlImportLookupCache[tipo] && wlImportLookupCache[tipo].value !== null) {
            return $.Deferred().resolve(wlImportLookupCache[tipo].value).promise();
        }

        if (wlImportLookupCache[tipo] && wlImportLookupCache[tipo].pending) {
            return wlImportLookupCache[tipo].pending;
        }

        var request = requestFactory();
        if (wlImportLookupCache[tipo]) {
            wlImportLookupCache[tipo].pending = request;
            request.done(function(response) {
                wlImportLookupCache[tipo].value = response;
            }).always(function() {
                wlImportLookupCache[tipo].pending = null;
            });
        }

        return request;
    }

    function wlImportInvalidateLookupCache(chaves) {
        (Array.isArray(chaves) ? chaves : [chaves]).forEach(function(chave) {
            if (chave === 'empreendimento') {
                wlImportLookupCache.empreendimento.values = {};
                wlImportLookupCache.empreendimento.pending = {};
                return;
            }

            if (wlImportLookupCache[chave]) {
                wlImportLookupCache[chave].value = null;
                wlImportLookupCache[chave].pending = null;
            }
        });
    }

    function wlGetPrioridadeData() {
        return wlImportCachedRequest('prioridade', '__default__', function() {
            return $.ajax({
                url: '<?= base_url('public/prioridade_lista') ?>',
                type: "POST",
                dataType: "json"
            });
        });
    }

    function wlGetTagsData() {
        return wlImportCachedRequest('tags', '__default__', function() {
            return $.ajax({
                url: '<?= base_url('public/desenho_tag_lista') ?>',
                type: "POST",
                dataType: "json"
            });
        });
    }

    function wlGetFinalidadeData() {
        return wlImportCachedRequest('finalidade', '__default__', function() {
            return $.ajax({
                url: '<?= base_url('public/finalidade_lista') ?>',
                type: "POST",
                dataType: "json"
            });
        });
    }

    function wlGetEmpresaData() {
        return wlImportCachedRequest('empresa', '__default__', function() {
            return $.ajax({
                url: '<?= base_url('public/empresa_lista') ?>',
                type: "POST",
                dataType: "json"
            });
        });
    }

    function wlGetEmpreendimentoData(empresa) {
        var empresaSelecionada = String(empresa || '');
        return wlImportCachedRequest('empreendimento', empresaSelecionada, function() {
            return $.ajax({
                url: '<?= base_url('public/empreendimento_lista') ?>',
                type: "POST",
                dataType: "json",
                data: {
                    empresa: empresaSelecionada
                }
            });
        });
    }

    var wlDependenciaBootstrapState = {
        empresa: false,
        finalidade: false,
        empreendimento: false,
        tags: false
    };

    function wlResetDependenciaBootstrapState() {
        wlDependenciaBootstrapState = {
            empresa: false,
            finalidade: false,
            empreendimento: false,
            tags: false
        };

        if (wlPreselecaoDependenciaTimer) {
            clearTimeout(wlPreselecaoDependenciaTimer);
            wlPreselecaoDependenciaTimer = null;
        }
    }

    function selects() {
        value_prioridade(true);
        value_tags(true);
        value_finalidade(true);
        value_empresa(true);

        value_tags_c(true);
        value_empresa_c(true);
        value_finalidade_c(true);
        value_prioridade_c(true);

    }

    function tag_ordem(selectedValue, selectedIndex, coluna, id) {
        if (coluna == '1' && selectedIndex == 0) {
            select = document.getElementById("tag1_novo_" + id);
            select.options[0].selected = true;
            select.disabled = true;
            document.getElementById("tag1_botao_" + id).disabled = true;
            select = document.getElementById("tag2_novo_" + id);
            select.options[0].selected = true;
            select.disabled = true;
            document.getElementById("tag2_botao_" + id).disabled = true;
            select = document.getElementById("tag3_novo_" + id);
            select.options[0].selected = true;
            select.disabled = true;
            document.getElementById("tag3_botao_" + id).disabled = true;
        } else if (coluna == '2' && selectedIndex == 0) {
            select = document.getElementById("tag3_novo_" + id);
            select.options[0].selected = true;
            select.disabled = true;
            document.getElementById("tag3_botao_" + id).disabled = true;
        }

        console.log("A");
        if (document.getElementById("finalidade_novo_" + id).selectedIndex != 0 && document.getElementById("empreendimento_novo_" + id).selectedIndex != 0) {
            select = document.getElementById("tag1_novo_" + id);
            select.disabled = false;
            select.options.disabled = false;
            document.getElementById("tag1_botao_" + id).disabled = false;
        }

        if (coluna == '1' && selectedIndex != 0) {
            select = document.getElementById("tag2_novo_" + id);
            select.disabled = false;
            select.options.disabled = false;
            document.getElementById("tag2_botao_" + id).disabled = false;
        }

        if (coluna == '2' && selectedIndex != 0) {
            select = document.getElementById("tag3_novo_" + id);
            select.disabled = false;
            document.getElementById("tag3_botao_" + id).disabled = false;
        }
        select3 = document.getElementById("tag3_novo_" + id);

        select2 = document.getElementById("tag2_novo_" + id);

        select1 = document.getElementById("tag1_novo_" + id);
        for (let index = 0; index < select1.length; index++) {
            select2.options[index].disabled = false;
            select3.options[index].disabled = false;
            select1.options[index].disabled = false;
        }
        if (select1.selectedIndex != 0) {
            select2.options[select1.selectedIndex].disabled = true;
            select3.options[select1.selectedIndex].disabled = true;
        }
        if (select2.selectedIndex != 0) {
            select1.options[select2.selectedIndex].disabled = true;
            select3.options[select2.selectedIndex].disabled = true;
        }
        if (select3.selectedIndex != 0) {
            select1.options[select3.selectedIndex].disabled = true;
            select2.options[select3.selectedIndex].disabled = true;
        }



    }












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



    lista_temp2 = "";

    function value_prioridade(efeturar = false) {
        wlGetPrioridadeData().done(function(response) {
                if (document.getElementById("prioridade_novo_0") != null && (response.toString() != lista_temp2 || efeturar)) {
                    for (let j = 0; j < desenhos.length; j++) {
                        if (document.getElementById("prioridade_novo_" + j)) {


                            // Obter referência ao elemento select
                            var funcao = document.getElementById("prioridade_novo_" + j);
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

                        }
                    }
                    lista_temp2 = response.toString();
                }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_prioridade);

    // Repetir função a cada segundo 
    //setInterval(value_prioridade, 15000);


    lista_temp3 = "";

    function textoOpcaoSelecionada(select) {
        if (!select || select.selectedIndex < 0) {
            return '';
        }

        return select.options[select.selectedIndex].textContent;
    }

    function value_tags(efeturar = false) {
        wlGetTagsData().done(function(response) {
                var assinaturaResposta = JSON.stringify(response);

                if (document.getElementById("tag1_novo_0") != null && (assinaturaResposta != lista_temp3 || efeturar)) {
                    for (let j = 0; j < desenhos.length; j++) {
                        valorSelecionadoAntes = '';
                        for (let h = 1; h < 4; h++) {
                            console.log(h);
                            if (document.getElementById("tag" + h + "_novo_" + j)) {
                                // Obter referência ao elemento select
                                var funcao = document.getElementById("tag" + h + "_novo_" + j);
                                // Armazenar o valor da opção selecionada antes de limpar o select

                                if (valorSelecionadoAntes == '') {
                                    funcao.disabled = true;
                                } // Limpar o select
                                valorSelecionadoAntes = funcao.value;
                                funcao.innerHTML = '';

                                funcao.onchange = function() {
                                    var selectedValue = this.value; // Valor da opção selecionada
                                    var selectedIndex = this.selectedIndex; // Índice da opção selecionada

                                    // Chame a função que deseja executar quando uma opção é selecionada
                                    tag_ordem(selectedValue, selectedIndex, h, j);
                                };

                                // Criar um novo elemento option
                                var novoOption = document.createElement("option");

                                // Definir o valor e texto do novo elemento option
                                novoOption.value = '';
                                novoOption.textContent = 'Subpasta';

                                // Adicionar o novo elemento option ao select
                                funcao.appendChild(novoOption);

                                let empreendimento = textoOpcaoSelecionada(document.getElementById("empreendimento_novo_" + j));
                                let finalidade = textoOpcaoSelecionada(document.getElementById("finalidade_novo_" + j));

                                response.tags?.[empreendimento]?.[finalidade]?.forEach(element => {


                                    // Criar um novo elemento option
                                    var novoOption = document.createElement("option");

                                    // Definir o valor e texto do novo elemento option
                                    novoOption.value = element;
                                    novoOption.textContent = element;

                                    funcao.appendChild(novoOption);
                                });
                                var opcoes = funcao.options;
                                for (var i = 0; i < opcoes.length; i++) {
                                    if (opcoes[i].value === valorSelecionadoAntes) {
                                        opcoes[i].selected = true;
                                        break;
                                    }
                                }

                            }
                        }
                        tag_ordem(document.getElementById("tag1_novo_" + j).value, document.getElementById("tag1_novo_" + j).selectedIndex, 1, j);


                    }
                    lista_temp3 = assinaturaResposta;
                    agendarPreselecaoDependencia();
                }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_tags);

    // Repetir função a cada segundo 
    //setInterval(value_tags, 15000);

    var lista_temp4 = '';

    function value_finalidade(efeturar = false) {
        wlGetFinalidadeData().done(function(response) {
                if (document.getElementById("finalidade_novo_0") != null || efeturar) {
                    if (response.toString() != lista_temp4 || efeturar) {

                        lista_temp4 = response.toString();
                        for (let j = 0; j < desenhos.length; j++) {
                            if (document.getElementById("finalidade_novo_" + j)) {


                                // Obter referência ao elemento select
                                var funcao = document.getElementById("finalidade_novo_" + j);
                                // Armazenar o valor da opção selecionada antes de limpar o select
                                var valorSelecionadoAntes = funcao.value;

                                // Limpar o select
                                funcao.innerHTML = '';

                                // Criar um novo elemento option
                                var novoOption = document.createElement("option");

                                // Definir o valor e texto do novo elemento option
                                novoOption.value = '';
                                novoOption.textContent = 'Finalidade';

                                // Adicionar o novo elemento option ao select
                                funcao.appendChild(novoOption);

                                response.lista.forEach(element => {



                                    // Criar um novo elemento option
                                    var novoOption = document.createElement("option");

                                    // Definir o valor e texto do novo elemento option
                                    novoOption.value = element.finalidade;
                                    novoOption.textContent = element.finalidade;
                                    funcao.appendChild(novoOption);
                                });
                                var opcoes = funcao.options;
                                for (var i = 0; i < opcoes.length; i++) {
                                    if (opcoes[i].value === valorSelecionadoAntes) {
                                        opcoes[i].selected = true;
                                        break;
                                    }
                                }

                            }
                        }

                    }
                    agendarPreselecaoDependencia();
                }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_finalidade);

    // Repetir função a cada segundo 
    //setInterval(value_finalidade, 15000);

    lista_temp5 = "";

    function value_empresa(efeturar = false) {
        wlGetEmpresaData().done(function(response) {
                if (document.getElementById("empresa_cliente_novo_0") != null && (response.toString() != lista_temp5 || efeturar)) {

                    for (let j = 0; j < desenhos.length; j++) {

                        if (document.getElementById("empresa_cliente_novo_" + j)) {

                            // Obter referência ao elemento select
                            var funcao = document.getElementById("empresa_cliente_novo_" + j);
                            // Armazenar o valor da opção selecionada antes de limpar o select
                            var valorSelecionadoAntes = funcao.value;

                            // Limpar o select
                            funcao.innerHTML = '';

                            // Criar um novo elemento option
                            var novoOption = document.createElement("option");

                            // Definir o valor e texto do novo elemento option
                            novoOption.value = '';
                            novoOption.textContent = 'Empresa';

                            // Adicionar o novo elemento option ao select
                            funcao.appendChild(novoOption);
                            funcao.onchange = function() {
                                var selectedValue = this.value; // Valor da opção selecionada
                                var selectedIndex = this.selectedIndex; // Índice da opção selecionada

                                // Chame a função que deseja executar quando uma opção é selecionada
                                value_empreendimento(selectedValue, selectedIndex, j);
                            };
                            response.lista.forEach(element => {



                                // Criar um novo elemento option
                                var novoOption = document.createElement("option");

                                // Definir o valor e texto do novo elemento option
                                novoOption.value = element.empresa;
                                novoOption.textContent = element.empresa;
                                funcao.appendChild(novoOption);
                            });
                            var opcoes = funcao.options;
                            for (var i = 0; i < opcoes.length; i++) {
                                if (opcoes[i].value === valorSelecionadoAntes) {
                                    opcoes[i].selected = true;
                                    break;
                                }
                            }

                        }
                    }
                    lista_temp5 = response.toString();
                    agendarPreselecaoDependencia();
                }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_empresa);

    // Repetir função a cada segundo 
    //setInterval(value_empresa, 15000);
    function value_empreendimento_lista(selectedValue, selectedIndex, id, response) {

        $.ajax({
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: {
                empresa: selectedValue
            },
            success: function(response) {

                if (document.getElementById("empreendimento_novo_0") != null) {

                    var funcao = document.getElementById("empreendimento_novo_" + id);
                    if (selectedIndex == 0) {
                        funcao.disabled = true;
                    } else {
                        funcao.disabled = false;
                    }



                    // Obter referência ao elemento select

                    // Armazenar o valor da opção selecionada antes de limpar o select
                    var valorSelecionadoAntes = funcao.value;

                    // Limpar o select
                    funcao.innerHTML = '';

                    // Criar um novo elemento option
                    var novoOption = document.createElement("option");

                    // Definir o valor e texto do novo elemento option

                    novoOption.value = '';
                    novoOption.textContent = 'Empreendimento';

                    // Adicionar o novo elemento option ao select
                    funcao.appendChild(novoOption);
                    var novoOption = document.createElement("option");

                    response.lista.forEach(element => {



                        // Criar um novo elemento option
                        var novoOption = document.createElement("option");

                        // Definir o valor e texto do novo elemento option
                        novoOption.value = element.empreendimento;
                        novoOption.textContent = element.empreendimento;
                        funcao.appendChild(novoOption);
                    });
                    var opcoes = funcao.options;
                    for (var i = 0; i < opcoes.length; i++) {
                        if (opcoes[i].value === valorSelecionadoAntes) {
                            opcoes[i].selected = true;
                            break;
                        }
                    }


                    lista_temp6 = response.toString();
                    agendarPreselecaoDependencia();
                }
            }
        });

    }

    function value_empreendimento(selectedValue, selectedIndex, id) {
        wlGetEmpreendimentoData(selectedValue).done(function(response) {

                if (document.getElementById("empreendimento_novo_0") != null) {
                    if (document.getElementById("empreendimento_novo_" + id)) {
                        var funcao = document.getElementById("empreendimento_novo_" + id);
                        if (selectedIndex == 0) {
                            funcao.disabled = true;
                        } else {
                            funcao.disabled = false;
                        }



                        // Obter referência ao elemento select

                        // Armazenar o valor da opção selecionada antes de limpar o select
                        var valorSelecionadoAntes = funcao.value;

                        // Limpar o select
                        funcao.innerHTML = '';

                        // Criar um novo elemento option
                        var novoOption = document.createElement("option");

                        // Definir o valor e texto do novo elemento option

                        novoOption.value = '';
                        novoOption.textContent = 'Empreendimento';

                        // Adicionar o novo elemento option ao select
                        funcao.appendChild(novoOption);
                        var novoOption = document.createElement("option");

                        response.lista.forEach(element => {



                            // Criar um novo elemento option
                            var novoOption = document.createElement("option");

                            // Definir o valor e texto do novo elemento option
                            novoOption.value = element.empreendimento;
                            novoOption.textContent = element.empreendimento;
                            funcao.appendChild(novoOption);
                        });
                        var opcoes = funcao.options;
                        for (var i = 0; i < opcoes.length; i++) {
                            if (opcoes[i].value === valorSelecionadoAntes) {
                                opcoes[i].selected = true;
                                break;
                            }
                        }
                    }

                    lista_temp6 = response.toString();
                    agendarPreselecaoDependencia();
                }
        });

    }

    function confirmarModal() {
        if (tipo_input == 'mult') {

            desenhos_enviar = [];
            i = 0;
            for (let j = 0; j < desenhos.length; j++) {
                if (desenhos[j] != null) {
                    data = {
                        empresa: document.getElementById("empresa_cliente_novo_" + j).value,
                        empreendimento: document.getElementById("empreendimento_novo_" + j).value,
                        finalidade: document.getElementById("finalidade_novo_" + j).value,
                        prioridade: document.getElementById("prioridade_novo_" + j).value,
                        tag1: document.getElementById("tag1_novo_" + j).value,
                        tag2: document.getElementById("tag2_novo_" + j).value,
                        tag3: document.getElementById("tag3_novo_" + j).value,
                        desenho: desenhos[j]

                    };
                    desenhos_enviar[i] = data;
                    i++;
                }
            }
            $.ajax({
                url: '<?= base_url('public/desenhos_add') ?>',
                type: "POST",
                dataType: "json", // Indicar que o retorno é em formato JSON
                data: {
                    desenhos: desenhos_enviar,
                    nome_processos: processo_desenho_nome
                },
                success: function(response) {

                    ok1 = true;
                    cont = 0;
                    cont_rep = 0;
                    mgs_final = [];
                    var index = [];
                    var mgs = response.msg;
                    var ok = response.ok;
                    for (const chave in mgs) {
                        cont_rep = 0;
                        cont1 = 0;

                        const valor = mgs[chave];
                        if (valor != null) {
                            for (const chave1 in mgs) {
                                if (mgs[chave1] == valor && chave1 != chave) {
                                    mgs[chave1] = null;
                                    ok[cont1] = null;
                                    cont_rep++;
                                }
                                cont1++;
                            }

                            if (cont_rep != 0) {
                                mgs_final["O core em " + (cont_rep + 1) + " desenhos"] = valor;
                                index[chave] = cont;
                            } else {
                                mgs_final[chave] = valor;
                                index[chave] = cont;
                            }
                        }



                        cont++;

                    }

                    ok = ok.filter(item => item !== null);

                    ok = Array.from(ok);


                    cont = 0;
                    cont_certo = 0;
                    for (const chave in mgs_final) {
                        if (ok[cont]) {
                            index1 = 0;
                            for (let j = 0; j < desenhos.length; j++) {
                                if (desenhos[j] == chave) {
                                    index1 = j;
                                    break;
                                }
                            }
                            desenhos[index1] = null;

                            var node = document.getElementById("desenho_" + index1);
                            if (node && node.parentNode) {
                                node.parentNode.removeChild(node);
                            }
                            cont_certo++;

                        } else {
                            const valor = mgs_final[chave];
                            alert_personalizado(chave, valor);

                            ok1 = false;
                        }

                        cont++;
                    }
                    if (cont_certo != 0) {
                        alert_certo("Desenhos adicionados", "Ao total " + cont_certo + " desenhos foram adicionados.");
                    }
                    if (ok1) {
                        window.processo_filtro = "";
                        window.desenho_dependencia_preselecao = null;
                        fecharModal1();
                        // Cria um novo elemento de entrada de arquivo vazio
                        var newFileInput = $('<input type="file" id="desenhos_add" multiple data-multiple-caption="{count} files selected" class="inputfile">');

                        // Substitui o elemento de entrada de arquivo original com o novo elemento
                        import_reset_add_modal();

                    }
                }
            });
            return;
        } else {
   
            data = {
                empresa: document.getElementById("empresa_cliente_novo_todos").value,
                empreendimento: document.getElementById("empreendimento_novo_todos").value,
                finalidade: document.getElementById("finalidade_novo_todos").value,
                prioridade: document.getElementById("prioridade_novo_todos").value,
                tag1: document.getElementById("tag1_novo_todos").value,
                tag2: document.getElementById("tag2_novo_todos").value,
                tag3: document.getElementById("tag3_novo_todos").value,
                desenho: desenhos,
                descricao: (document.getElementById("descricao_desenho") || { value: '' }).value

            };
            desenhos_enviar = data;


       
        $.ajax({
            url: '<?= base_url('public/desenhos_add_uni') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: {
                desenhos: desenhos_enviar,
                nome_processos: processo_desenho_nome
            },
            success: function(response) {

                ok1 = true;
                cont = 0;
                cont_rep = 0;
                mgs_final = [];
                var index = [];
                var mgs = response.msg;
                var ok = response.ok;
                for (const chave in mgs) {
                    cont_rep = 0;
                    cont1 = 0;

                    const valor = mgs[chave];
                    if (valor != null) {
                        for (const chave1 in mgs) {
                            if (mgs[chave1] == valor && chave1 != chave) {
                                mgs[chave1] = null;
                                ok[cont1] = null;
                                cont_rep++;
                            }
                            cont1++;
                        }

                        if (cont_rep != 0) {
                            mgs_final["O core em " + (cont_rep + 1) + " desenhos"] = valor;
                            index[chave] = cont;
                        } else {
                            mgs_final[chave] = valor;
                            index[chave] = cont;
                        }
                    }



                    cont++;

                }

                ok = ok.filter(item => item !== null);

                ok = Array.from(ok);


                cont = 0;
                cont_certo = 0;
                for (const chave in mgs_final) {
                    if (ok[cont]) {
                        index1 = 0;
                        for (let j = 0; j < desenhos.length; j++) {
                            if (desenhos[j] == chave) {
                                index1 = j;
                                break;
                            }
                        }
                        desenhos[index1] = null;

                        var node = document.getElementById("desenho_" + index1);
                        if (node && node.parentNode) {
                            node.parentNode.removeChild(node);
                        }
                        cont_certo++;

                    } else {
                        const valor = mgs_final[chave];
                        alert_personalizado(chave, valor);

                        ok1 = false;
                    }

                    cont++;
                }
                if (cont_certo != 0) {
                    alert_certo("Desenhos adicionados", "Ao total " + cont_certo + " desenhos foram adicionados.");
                }
                if (ok1) {
                    window.processo_filtro = "";
                    window.desenho_dependencia_preselecao = null;
                    fecharModal1();
                    // Cria um novo elemento de entrada de arquivo vazio
                    var newFileInput = $('<input type="file" id="desenhos_add" multiple data-multiple-caption="{count} files selected" class="inputfile">');

                    // Substitui o elemento de entrada de arquivo original com o novo elemento
                    import_reset_add_modal();

                }
            }
        });
 }
    }
    



    //lista cabeçario




    lista_temp_c5 = "";

    function value_empresa_c(efeturar = false) {
        wlGetEmpresaData().done(function(response) {
                if (document.getElementById("empresa_cliente_novo_todos") != null && (response.toString() != lista_temp_c5 || efeturar)) {





                    // Obter referência ao elemento select
                    var funcao = document.getElementById("empresa_cliente_novo_todos");
                    // Armazenar o valor da opção selecionada antes de limpar o select
                    var valorSelecionadoAntes = funcao.value;

                    // Limpar o select
                    funcao.innerHTML = '';

                    // Criar um novo elemento option
                    var novoOption = document.createElement("option");

                    // Definir o valor e texto do novo elemento option
                    novoOption.value = '';
                    novoOption.textContent = 'Empresa';

                    // Adicionar o novo elemento option ao select
                    funcao.appendChild(novoOption);


                    response.lista.forEach(element => {



                        // Criar um novo elemento option
                        var novoOption = document.createElement("option");

                        // Definir o valor e texto do novo elemento option
                        novoOption.value = element.empresa;
                        novoOption.textContent = element.empresa;
                        funcao.appendChild(novoOption);
                    });
                    var opcoes = funcao.options;
                    for (var i = 0; i < opcoes.length; i++) {
                        if (opcoes[i].value === valorSelecionadoAntes) {
                            opcoes[i].selected = true;
                            break;
                        }
                    }


                    lista_temp_c5 = response.toString();
                    agendarPreselecaoDependencia();
                }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_empresa_c);

    // Repetir função a cada segundo 
    //setInterval(value_empresa_c, 15000);






    function value_empreendimento_c(selectedValue, selectedIndex, index) {
        wlGetEmpreendimentoData(selectedValue).done(function(response) {

                if (document.getElementById("empreendimento_novo_todos") != null) {

                    var funcao = document.getElementById("empreendimento_novo_todos");
                    if (selectedIndex == 0) {
                        funcao.disabled = true;
                    } else {
                        funcao.disabled = false;
                    }

                    funcao.onchange = function() {
                        var selectedValue1 = this.value; // Valor da opção selecionada
                        var selectedIndex1 = this.selectedIndex; // Índice da opção selecionada

                        // Chame a função que deseja executar quando uma opção é selecionada

                        for (let j = 0; j < desenhos.length; j++) {
                            if (document.getElementById("empresa_cliente_novo_" + j)) {
                                if (document.getElementById("empresa_cliente_novo_" + j).value == selectedValue) {
                                    document.getElementById("empreendimento_novo_" + j).selectedIndex = selectedIndex1;
                                }
                            }
                        }

                    };

                    // Obter referência ao elemento select

                    // Armazenar o valor da opção selecionada antes de limpar o select
                    var valorSelecionadoAntes = funcao.value;

                    // Limpar o select
                    funcao.innerHTML = '';

                    // Criar um novo elemento option
                    var novoOption = document.createElement("option");

                    // Definir o valor e texto do novo elemento option

                    novoOption.value = '';
                    novoOption.textContent = 'Empreendimento';

                    // Adicionar o novo elemento option ao select
                    funcao.appendChild(novoOption);
                    var novoOption = document.createElement("option");

                    response.lista.forEach(element => {



                        // Criar um novo elemento option
                        var novoOption = document.createElement("option");

                        // Definir o valor e texto do novo elemento option
                        novoOption.value = element.empreendimento;
                        novoOption.textContent = element.empreendimento;
                        funcao.appendChild(novoOption);
                    });
                    var opcoes = funcao.options;
                    for (var i = 0; i < opcoes.length; i++) {
                        if (opcoes[i].value === valorSelecionadoAntes) {
                            opcoes[i].selected = true;
                            break;
                        }
                    }
                    var opcoes1 = opcoes;



                    for (let id = 0; id < index; id++) {
                        if (document.getElementById("empreendimento_novo_0") != null) {
                            if (document.getElementById("empreendimento_novo_" + id)) {
                                var funcao = document.getElementById("empreendimento_novo_" + id);
                                if (selectedIndex == 0) {
                                    funcao.disabled = true;
                                } else {
                                    funcao.disabled = false;
                                }



                                // Obter referência ao elemento select

                                // Armazenar o valor da opção selecionada antes de limpar o select
                                var valorSelecionadoAntes = funcao.value;

                                // Limpar o select
                                funcao.innerHTML = '';

                                // Criar um novo elemento option
                                var novoOption = document.createElement("option");

                                // Definir o valor e texto do novo elemento option

                                novoOption.value = '';
                                novoOption.textContent = 'Empreendimento';

                                // Adicionar o novo elemento option ao select
                                funcao.appendChild(novoOption);
                                var novoOption = document.createElement("option");

                                response.lista.forEach(element => {



                                    // Criar um novo elemento option
                                    var novoOption = document.createElement("option");

                                    // Definir o valor e texto do novo elemento option
                                    novoOption.value = element.empreendimento;
                                    novoOption.textContent = element.empreendimento;
                                    funcao.appendChild(novoOption);
                                });
                                funcao.options = opcoes1;


                            }
                            lista_temp6 = response.toString();
                        }
                    }
                    agendarPreselecaoDependencia();
                }
        });

    }




    var lista_temp_c4 = '';

    function value_finalidade_c(efeturar = false) {
        wlGetFinalidadeData().done(function(response) {
                if (document.getElementById("finalidade_novo_todos") != null || efeturar) {
                    if (response.toString() != lista_temp_c4 || efeturar) {

                        lista_temp_c4 = response.toString();




                        // Obter referência ao elemento select
                        var funcao = document.getElementById("finalidade_novo_todos");
                        // Armazenar o valor da opção selecionada antes de limpar o select
                        var valorSelecionadoAntes = funcao.value;

                        // Limpar o select
                        funcao.innerHTML = '';

                        // Criar um novo elemento option
                        var novoOption = document.createElement("option");

                        // Definir o valor e texto do novo elemento option
                        novoOption.value = '';
                        novoOption.textContent = 'Finalidade';

                        // Adicionar o novo elemento option ao select
                        funcao.appendChild(novoOption);
                        funcao.onchange = function() {
                            var selectedValue = this.value; // Valor da opção selecionada
                            var selectedIndex1 = this.selectedIndex; // Índice da opção selecionada

                            // Chame a função que deseja executar quando uma opção é selecionada

                            for (let j = 0; j < desenhos.length; j++) {
                                if (document.getElementById("finalidade_novo_" + j)) {
                                    document.getElementById("finalidade_novo_" + j).selectedIndex = selectedIndex1;
                                }
                            }

                        };
                        response.lista.forEach(element => {



                            // Criar um novo elemento option
                            var novoOption = document.createElement("option");

                            // Definir o valor e texto do novo elemento option
                            novoOption.value = element.finalidade;
                            novoOption.textContent = element.finalidade;
                            funcao.appendChild(novoOption);
                        });
                        var opcoes = funcao.options;
                        for (var i = 0; i < opcoes.length; i++) {
                            if (opcoes[i].value === valorSelecionadoAntes) {
                                opcoes[i].selected = true;
                                break;
                            }
                        }



                    }
                    agendarPreselecaoDependencia();
                }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_finalidade_c);

    // Repetir função a cada segundo 
    //setInterval(value_finalidade_c, 15000);

    lista_temp_c2 = "";

    function value_prioridade_c(efeturar = false) {
        wlGetPrioridadeData().done(function(response) {
                if (document.getElementById("prioridade_novo_todos") != null && (response.toString() != lista_temp_c2 || efeturar)) {




                    // Obter referência ao elemento select
                    var funcao = document.getElementById("prioridade_novo_todos");

                    funcao.onchange = function() {
                        var selectedValue = this.value; // Valor da opção selecionada
                        var selectedIndex1 = this.selectedIndex; // Índice da opção selecionada

                        // Chame a função que deseja executar quando uma opção é selecionada

                        for (let j = 0; j < desenhos.length; j++) {
                            if (document.getElementById("prioridade_novo_" + j)) {
                                document.getElementById("prioridade_novo_" + j).selectedIndex = selectedIndex1;
                            }

                        }

                    };










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


                    lista_temp_c2 = response.toString();
                }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_prioridade_c);

    // Repetir função a cada segundo 
    //setInterval(value_prioridade_c, 15000);


    lista_temp_c3 = "";

    function value_tags_c(efeturar = false) {
        wlGetTagsData().done(function(response) {
                var assinaturaResposta = JSON.stringify(response);

                if ((document.getElementById("tag1_novo_0") != null || efeturar) && (assinaturaResposta != lista_temp_c3 || efeturar)) {

                    valorSelecionadoAntes = '';
                    for (let h = 1; h < 4; h++) {

                        // Obter referência ao elemento selecttag2_novo_todos
                        var funcao = document.getElementById("tag" + h + "_novo_todos");
                        // Armazenar o valor da opção selecionada antes de limpar o select


                        if (valorSelecionadoAntes == '') {
                            funcao.disabled = true;
                            document.getElementById("tag" + h + "_botao_todos").disabled = true;
                        }

                        if (document.getElementById("empreendimento_novo_todos").selectedIndex > 0 && document.getElementById("finalidade_novo_todos").selectedIndex > 0) {
                            select = document.getElementById("tag1_novo_todos");
                            select.disabled = false;
                            select.options.disabled = false;
                            document.getElementById("tag1_botao_todos").disabled = false;
                        }

                        // Limpar o select
                        valorSelecionadoAntes = funcao.value;
                        funcao.innerHTML = '';
                        funcao.onchange = function() {
                            var selectedValue = this.value; // Valor da opção selecionada
                            var selectedIndex = this.selectedIndex; // Índice da opção selecionada
                            tag_ordem(selectedValue, selectedIndex, h, 'todos');
                            // Chame a função que deseja executar quando uma opção é selecionada
                            for (let j = 0; j < desenhos.length; j++) {
                                if (document.getElementById("tag" + h + "_novo_" + j)) {
                                    var selectElement = document.getElementById("tag" + h + "_novo_" + j);
                                    if (selectElement && selectElement.querySelector('option[value="' + selectedValue + '"]')) {
                                        selectElement.value = selectedValue;
                                        tag_ordem(selectedValue, selectedIndex, h, j);
                                    }

                                }
                            }
                        };

                        // Criar um novo elemento option
                        var novoOption = document.createElement("option");

                        // Definir o valor e texto do novo elemento option
                        novoOption.value = '';
                        novoOption.textContent = 'Subpasta';

                        // Adicionar o novo elemento option ao select
                        funcao.appendChild(novoOption);
                        let empreendimento = textoOpcaoSelecionada(document.getElementById("empreendimento_novo_todos"));
                        let finalidade = textoOpcaoSelecionada(document.getElementById("finalidade_novo_todos"));

                        response.tags?.[empreendimento]?.[finalidade]?.forEach(element => {


                            // Criar um novo elemento option
                            var novoOption = document.createElement("option");

                            // Definir o valor e texto do novo elemento option
                            novoOption.value = element;
                            novoOption.textContent = element;

                            funcao.appendChild(novoOption);
                        });
                        var opcoes = funcao.options;
                        for (var i = 0; i < opcoes.length; i++) {
                            if (opcoes[i].value === valorSelecionadoAntes) {
                                opcoes[i].selected = true;
                                break;
                            }
                        }
                    }


                    lista_temp_c3 = assinaturaResposta;
                    agendarPreselecaoDependencia();
                }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_tags_c);

    // Repetir função a cada segundo
    //setInterval(value_tags_c, 15000);

    modal_bory_geral = '';

    function adicinar_subpasta(empreendimento, finalidade) {
        //Remove o prefixo 'modal_' do ID para obter o ID real





        //Modificar o conteúdo do modal de acordo com a resposta do servidor

        //Altera o texto do botão de confirmação no modal
        var botao_confirmar_modal = document.getElementById('botao_confirmar_modal_cadastrar');
        botao_confirmar_modal.innerHTML = "Confirmar";
        botao_confirmar_modal.disabled = true;

        var empreendimentoOrigem = document.getElementById(empreendimento);
        var finalidadeOrigem = document.getElementById(finalidade);
        var sufixoLinha = empreendimento.replace('empreendimento_novo_', '');
        var empresaOrigem = document.getElementById('empresa_cliente_novo_' + sufixoLinha);
        var empreendimentoNome = empreendimentoOrigem && empreendimentoOrigem.selectedIndex >= 0
            ? empreendimentoOrigem.options[empreendimentoOrigem.selectedIndex].text
            : '';
        var finalidadeNome = finalidadeOrigem && finalidadeOrigem.selectedIndex >= 0
            ? finalidadeOrigem.options[finalidadeOrigem.selectedIndex].text
            : '';

        //Obtém referências aos elementos do modal
        var modal_titulo = document.getElementById('modal_cadastrar_titulo');
        var modal_bory = document.getElementById('modal_cadastrar_bory');
        modal_bory_geral = modal_bory.innerHTML;
        //Define o título do modal como "Modificar a tag: Nome da Tag"
        modal_titulo.textContent = "Adicionar Subpasta";

        // Limpa o conteúdo do modal_body
        modal_bory.innerHTML = '';



        // Cria e configura o segundo grupo de form para "Empreendimento"
        divElemnt = document.createElement("div");
        divElemnt.classList.add("form-group");

        labelElement = document.createElement("label");
        labelElement.textContent = "Empreendimento";

        inputElement = document.createElement("select");
        inputElement.id = 'empreendimento_tag_novo';
        inputElement.classList.add("form-control");
        inputElement.disabled = true;

        divElemnt.appendChild(labelElement);
        divElemnt.appendChild(inputElement);
        modal_bory.appendChild(divElemnt);

        // Cria e configura o terceiro grupo de form para "Finalidade"
        divElemnt = document.createElement("div");
        divElemnt.classList.add("form-group");

        labelElement = document.createElement("label");
        labelElement.textContent = "Finalidade";

        inputElement = document.createElement("select");
        inputElement.id = 'finalidade_tag_novo';
        inputElement.classList.add("form-control");
        inputElement.disabled = true;

        divElemnt.appendChild(labelElement);
        divElemnt.appendChild(inputElement);
        modal_bory.appendChild(divElemnt);

        // Cria e configura o primeiro grupo de form para "Subpasta"
        var divElemnt = document.createElement("div");
        divElemnt.classList.add("form-group");

        var labelElement = document.createElement("label");
        labelElement.textContent = "Subpasta";

        var inputElement = document.createElement("input");
        inputElement.type = 'text';
        inputElement.id = 'nome_tag_novo';
        inputElement.classList.add("form-control");


        // Adiciona um evento de input ao elemento para limitar o comprimento do valor
        inputElement.addEventListener("input", function() {
            var input = this;
            var maxLength = 17;
            input.value = input.value.slice(0, maxLength); // Trunca o valor para o tamanho máximo
        });

        // Adiciona os elementos ao divElemnt e depois ao modal_body
        divElemnt.appendChild(labelElement);
        divElemnt.appendChild(inputElement);
        modal_bory.appendChild(divElemnt);

        $.when(
            empreendimento_select(
                empreendimentoOrigem ? empreendimentoOrigem.value : '',
                empresaOrigem ? empresaOrigem.value : '',
                empreendimentoNome
            ),
            finalidade_select(finalidadeOrigem ? finalidadeOrigem.value : '', finalidadeNome)
        ).always(function() {
            botao_confirmar_modal.disabled = false;
        });



        //Exibe o modal
        mostrarModal1("modal_cadastrar");





    }


    function finalidade_select(id = null, nome = '') {
        return wlGetFinalidadeData().done(function(response) {
                // Limpa as opções atuais do select
                $('#finalidade_tag_novo').empty();

                // Adiciona uma opção padrão
                $('#finalidade_tag_novo').append('<option value="">Finalidade</option>');

                // Itera sobre o array de resposta e adiciona as opções ao select
                $.each(response.lista, function(index, item) {
                    var option = document.createElement('option');
                    option.value = item.id || item.finalidade;
                    option.textContent = item.finalidade;
                    document.getElementById('finalidade_tag_novo').appendChild(option);
                });
                var select = document.getElementById("finalidade_tag_novo");
                Array.from(select.options).some(function(option) {
                    if (option.value === String(id || '') || option.textContent === String(nome || id || '')) {
                        select.value = option.value;
                        return true;
                    }
                    return false;
                });
            }).fail(function(xhr, status, error) {
                console.error("Ocorreu um erro ao carregar os dados: ", error);
            });

    }

    function empreendimento_select(id = null, empresa = '', nome = '') {
        return wlGetEmpreendimentoData(empresa).done(function(response) {
                // Limpa as opções atuais do select
                $('#empreendimento_tag_novo').empty();

                // Adiciona uma opção padrão
                $('#empreendimento_tag_novo').append('<option value="">Empreendimento</option>');

                // Itera sobre o array de resposta e adiciona as opções ao select
                $.each(response.lista, function(index, item) {
                    var option = document.createElement('option');
                    option.value = item.id || item.empreendimento;
                    option.textContent = item.empreendimento;
                    document.getElementById('empreendimento_tag_novo').appendChild(option);
                });
                var select = document.getElementById("empreendimento_tag_novo");
                Array.from(select.options).some(function(option) {
                    if (option.value === String(id || '') || option.textContent === String(nome || id || '')) {
                        select.value = option.value;
                        return true;
                    }
                    return false;
                });

            }).fail(function(xhr, status, error) {
                console.error("Ocorreu um erro ao carregar os dados: ", error);
            });

    }

    function cadastrar() {
        //Esta função é usada para cadastrar uma nova "tag".

        //Obtém o valor da tag a partir do elemento com o ID "nome_tag_novo".
        var tag = document.getElementById("nome_tag_novo").value;
        var empreendimento = document.getElementById("empreendimento_tag_novo").value;
        var finalidade = document.getElementById("finalidade_tag_novo").value;

        $.ajax({
            url: '<?= base_url('public/desenho_tag_cadastro') ?>',
            type: "POST",
            dataType: "json", //Indicar que o retorno é em formato JSON
            data: {
                tag: tag,
                empreendimento: empreendimento,
                finalidade: finalidade
            },
            success: function(response) {
                //Função a ser executada em caso de sucesso da solicitação AJAX.

                if (!response.ok) {
                    //Se a resposta não indica sucesso, isso significa que ocorreu um erro no cadastramento da tag.

                    //A resposta contém mensagens de erro no formato de um objeto. O loop for percorre essas mensagens.
                    for (const chave in response.msg) {
                        const valor = response.msg[chave];
                        //Para cada mensagem de erro, exibe um alerta personalizado com a chave (nome do campo) e o valor (mensagem de erro).
                        alert_personalizado(chave, valor);
                    }
                } else {
                    //Se a resposta indica sucesso, exibe um alerta informando que a "tag" foi cadastrada com sucesso.
                    alert_certo('Cadastrado', 'Tag cadastrado com sucesso.');
                    //Limpa o valor do campo de entrada para que o usuário possa inserir outra "tag".
                    document.getElementById("nome_tag_novo").value = '';
                    wlImportInvalidateLookupCache('tags');
                    value_tags_c(true);
                    value_tags(true);
                    fecharModal1('modal_cadastrar');
                }

            }
        });
    }

    function normalizarPreselecoesDependencia(preselecoes) {
        if (!preselecoes || typeof preselecoes !== 'object') {
            return null;
        }

        var tags = Array.isArray(preselecoes.tags) ? preselecoes.tags : [];

        return {
            empresa: String(preselecoes.empresa || ''),
            empreendimento: String(preselecoes.empreendimento || ''),
            finalidade: String(preselecoes.finalidade || ''),
            prioridade: String(preselecoes.prioridade || ''),
            descricao: String(preselecoes.descricao || ''),
            tags: [
                String(tags[0] || ''),
                String(tags[1] || ''),
                String(tags[2] || '')
            ]
        };
    }

    function normalizarValorPreselecaoDependencia(valor) {
        return String(valor || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/\s+/g, ' ')
            .trim()
            .toLowerCase();
    }

    function selecionarValorSelectDependencia(id, valor) {
        var select = document.getElementById(id);
        if (!select || valor === undefined || valor === null || valor === '') {
            return false;
        }

        var valorNormalizado = normalizarValorPreselecaoDependencia(valor);

        for (var i = 0; i < select.options.length; i++) {
            var valorOpcao = select.options[i].value;
            if (valorOpcao === valor || normalizarValorPreselecaoDependencia(valorOpcao) === valorNormalizado) {
                select.selectedIndex = i;
                return true;
            }
        }

        return false;
    }

    function preencherDescricaoDependencia(preselecoes) {
        if (!preselecoes || !preselecoes.descricao) {
            return;
        }

        var campoDescricao = document.getElementById('descricao_desenho');
        if (!campoDescricao) {
            return;
        }

        if (String(campoDescricao.value || '').trim() === '') {
            campoDescricao.value = preselecoes.descricao;
        }
    }

    function aplicarCamposFixosDependencia(preselecoes) {
        if (!preselecoes) {
            return;
        }

        preencherDescricaoDependencia(preselecoes);
        selecionarValorSelectDependencia('prioridade_novo_todos', preselecoes.prioridade);
        selecionarValorSelectDependencia('finalidade_novo_todos', preselecoes.finalidade);
        selecionarValorSelectDependencia('empresa_cliente_novo_todos', preselecoes.empresa);

        for (var i = 0; i < desenhos.length; i++) {
            selecionarValorSelectDependencia('prioridade_novo_' + i, preselecoes.prioridade);
            selecionarValorSelectDependencia('finalidade_novo_' + i, preselecoes.finalidade);
            selecionarValorSelectDependencia('empresa_cliente_novo_' + i, preselecoes.empresa);
        }
    }

    function aplicarTagsDependencia(preselecoes) {
        if (!preselecoes || !Array.isArray(preselecoes.tags)) {
            return;
        }

        for (var i = 0; i < preselecoes.tags.length; i++) {
            var coluna = i + 1;
            var valor = preselecoes.tags[i];

            selecionarValorSelectDependencia('tag' + coluna + '_novo_todos', valor);
            for (var j = 0; j < desenhos.length; j++) {
                selecionarValorSelectDependencia('tag' + coluna + '_novo_' + j, valor);
            }
        }

        var tagCabecalho = document.getElementById('tag1_novo_todos');
        if (tagCabecalho) {
            tag_ordem(tagCabecalho.value, tagCabecalho.selectedIndex, 1, 'todos');
        }

        for (var linha = 0; linha < desenhos.length; linha++) {
            var tagLinha = document.getElementById('tag1_novo_' + linha);
            if (tagLinha) {
                tag_ordem(tagLinha.value, tagLinha.selectedIndex, 1, linha);
            }
        }
    }

    function preSelecaoDependenciaCompleta(preselecoes) {
        if (!preselecoes) {
            return true;
        }

        var checks = [
            ['empresa_cliente_novo_todos', preselecoes.empresa],
            ['empreendimento_novo_todos', preselecoes.empreendimento],
            ['finalidade_novo_todos', preselecoes.finalidade],
            ['prioridade_novo_todos', preselecoes.prioridade],
            ['tag1_novo_todos', preselecoes.tags[0]],
            ['tag2_novo_todos', preselecoes.tags[1]],
            ['tag3_novo_todos', preselecoes.tags[2]]
        ];

        for (var i = 0; i < checks.length; i++) {
            var elemento = document.getElementById(checks[i][0]);
            var valorEsperado = checks[i][1];
            if (elemento && valorEsperado && elemento.value !== valorEsperado) {
                return false;
            }
        }

        return true;
    }

    var wlPreselecaoDependenciaTimer = null;

    function agendarPreselecaoDependencia(atraso) {
        if (typeof window.processo_filtro === 'undefined' || String(window.processo_filtro || '') === '') {
            return;
        }

        if (wlPreselecaoDependenciaTimer) {
            clearTimeout(wlPreselecaoDependenciaTimer);
        }

        wlPreselecaoDependenciaTimer = setTimeout(function() {
            wlPreselecaoDependenciaTimer = null;
            tentarAplicarPreselecaoDependencia(0);
        }, typeof atraso === 'number' ? atraso : 90);
    }

    function tentarAplicarPreselecaoDependencia(tentativa) {
        var origemPreselecoes = window.desenho_dependencia_preselecao || (lista_array && lista_array.preselecoes ? lista_array.preselecoes : null);
        var preselecoes = normalizarPreselecoesDependencia(origemPreselecoes);
        if (!preselecoes) {
            return;
        }

        var empresaCabecalho = document.getElementById('empresa_cliente_novo_todos');
        var finalidadeCabecalho = document.getElementById('finalidade_novo_todos');
        if (empresaCabecalho && empresaCabecalho.options.length <= 1 && !wlDependenciaBootstrapState.empresa) {
            wlDependenciaBootstrapState.empresa = true;
            value_empresa_c(true);
        }
        if (finalidadeCabecalho && finalidadeCabecalho.options.length <= 1 && !wlDependenciaBootstrapState.finalidade) {
            wlDependenciaBootstrapState.finalidade = true;
            value_finalidade_c(true);
        }

        aplicarCamposFixosDependencia(preselecoes);

        empresaCabecalho = document.getElementById('empresa_cliente_novo_todos');
        var indiceEmpresaCabecalho = empresaCabecalho ? empresaCabecalho.selectedIndex : -1;

        if (preselecoes.empresa && indiceEmpresaCabecalho > 0) {
            var empreendimentoCabecalho = document.getElementById('empreendimento_novo_todos');
            if (empreendimentoCabecalho && empreendimentoCabecalho.options.length <= 1 && !wlDependenciaBootstrapState.empreendimento) {
                wlDependenciaBootstrapState.empreendimento = true;
                value_empreendimento_c(preselecoes.empresa, indiceEmpresaCabecalho, desenhos.length);
            }
        }

        selecionarValorSelectDependencia('finalidade_novo_todos', preselecoes.finalidade);
        selecionarValorSelectDependencia('empreendimento_novo_todos', preselecoes.empreendimento);
        for (var j = 0; j < desenhos.length; j++) {
            selecionarValorSelectDependencia('finalidade_novo_' + j, preselecoes.finalidade);
            selecionarValorSelectDependencia('empreendimento_novo_' + j, preselecoes.empreendimento);
        }

        if (preselecoes.tags[0]) {
            var tagCabecalho = document.getElementById('tag1_novo_todos');
            var empreendimentoCabecalhoSelecionado = document.getElementById('empreendimento_novo_todos');
            var dependenciasTagsProntas = empreendimentoCabecalhoSelecionado && empreendimentoCabecalhoSelecionado.selectedIndex > 0 &&
                finalidadeCabecalho && finalidadeCabecalho.selectedIndex > 0;

            if (!dependenciasTagsProntas) {
                wlDependenciaBootstrapState.tags = false;
            } else if (tagCabecalho && tagCabecalho.options.length <= 1 && !wlDependenciaBootstrapState.tags) {
                wlDependenciaBootstrapState.tags = true;
                value_tags(true);
                value_tags_c(true);
            }
        }

        aplicarTagsDependencia(preselecoes);

        if (!preSelecaoDependenciaCompleta(preselecoes) && tentativa < 24) {
            setTimeout(function() {
                tentarAplicarPreselecaoDependencia(tentativa + 1);
            }, 180);
        }
    }

    var wlGetRadioOriginal = get_radio;
    get_radio = function() {
        var radios = document.getElementsByName('processo');
        if (!radios || radios.length === 0) {
            return wlGetRadioOriginal();
        }

        for (var i = 0; i < radios.length; i++) {
            if (radios[i].checked) {
                return radios[i];
            }
        }

        return '';
    };

    var wlProcessoListaDesenhoOriginal = processo_lista_desenho;
    processo_lista_desenho = function(filtro_input) {
        wlProcessoListaDesenhoOriginal(filtro_input);

        var processoTravado = typeof window.processo_filtro !== 'undefined' ? String(window.processo_filtro || '') : '';
        var processoTravadoNormalizado = (typeof normalizarValorPreselecaoDependencia === 'function')
            ? normalizarValorPreselecaoDependencia(processoTravado)
            : String(processoTravado || '').trim().toLowerCase();
        var divSelect = document.getElementById('processos_desenho_select');
        var radioContainer = document.getElementById('processos_radio_desenho');
        if (radioContainer) {
            Array.from(radioContainer.querySelectorAll("input[name='processo']")).forEach(function(radio) {
                var wrapper = radio.parentElement;
                if (!wrapper) {
                    return;
                }

                wrapper.classList.add('wl-add-upload-process-item');
                if (wrapper.tagName !== 'LABEL') {
                    wrapper.setAttribute('role', 'button');
                }
                if (!wrapper.dataset.wlClickBound) {
                    wrapper.addEventListener('click', function(event) {
                        if (event.target === radio) {
                            return;
                        }

                        radio.checked = true;
                        radio.dispatchEvent(new Event('change', { bubbles: true }));
                    });
                    wrapper.dataset.wlClickBound = '1';
                }

                var texto = wrapper.querySelector('label');
                if (texto) {
                    texto.className = 'wl-add-upload-process-label';
                }

                if (!radio.dataset.wlBound) {
                    radio.addEventListener('change', function() {
                        getFiltroByNome(radio.value);
                        import_update_process_visual_state();
                    });
                    radio.dataset.wlBound = '1';
                }
            });
        }

        if (divSelect) {
            divSelect.style.display = processoTravado !== '' ? 'none' : 'block';
        }

        if (processoTravado === '') {
            import_update_process_visual_state();
            import_update_add_modal_context();
            return;
        }

        if (!radioContainer) {
            return;
        }

        var radios = Array.from(radioContainer.querySelectorAll("input[name='processo']"));
        var radioAlvo = radios.find(function(radio) {
            if (radio.value === processoTravado) {
                return true;
            }

            if (typeof normalizarValorPreselecaoDependencia === 'function') {
                return normalizarValorPreselecaoDependencia(radio.value) === processoTravadoNormalizado;
            }

            return String(radio.value || '').trim().toLowerCase() === processoTravadoNormalizado;
        });

        if (!radioAlvo) {
            if (radios[0]) {
                radios[0].checked = true;
            }
            return;
        }

        radios.forEach(function(radio) {
            var wrapper = radio.parentElement;
            var radioEhAlvo = radio.value === processoTravado;
            if (!radioEhAlvo && typeof normalizarValorPreselecaoDependencia === 'function') {
                radioEhAlvo = normalizarValorPreselecaoDependencia(radio.value) === processoTravadoNormalizado;
            }

            if (!radioEhAlvo && typeof normalizarValorPreselecaoDependencia !== 'function') {
                radioEhAlvo = String(radio.value || '').trim().toLowerCase() === processoTravadoNormalizado;
            }

            if (radioEhAlvo) {
                radio.checked = true;
                return;
            }

            if (wrapper) {
                wrapper.remove();
            }
        });

        import_update_process_visual_state();
        import_update_add_modal_context();
    };

    adicionar = function() {
        if (typeof window.processo_filtro !== 'undefined' && window.processo_filtro !== '') {
            processo_desenho_nome = window.processo_filtro;
        } else {
            var processoSelecionado = document.querySelector("#processos_radio_desenho input[name='processo']:checked");
            if (!processoSelecionado) {
                alert_personalizado('Desenho', 'Selecione um processo.');
                return;
            }

            processo_desenho_nome = processoSelecionado && typeof processoSelecionado.value !== 'undefined'
                ? processoSelecionado.value
                : processoSelecionado;
        }

        tipo_input = window.import_desenho_mode;

        var fileInput = document.getElementById('desenhos_add');
        if (!fileInput) {
            alert_personalizado('Desenho', 'Componente de upload nao encontrado (#desenhos_add).');
            return;
        }

        var files = import_get_selected_files();
        if (!files || files.length === 0) {
            alert_personalizado('Desenho', 'Selecione um arquivo antes de adiciona-lo.');
            return;
        }

        $.ajax({
            url: '<?= base_url('public/criar_pasta_temp') ?>',
            type: "POST",
            dataType: "json",
            success: function(response) {
                if (response.ok != 'true') {
                    alert_personalizado("Desenho", 'Erro ao criar pasta temporaria.');
                    return;
                }

                for (var i = 0; i < files.length; i++) {
                    var file = files[i];
                    if (!file) {
                        continue;
                    }

                    var formData = new FormData();
                    formData.append('file', file);

                    $.ajax({
                        url: '<?= site_url('public/desenho_adicionar_temp') ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: formData,
                        processData: false,
                        async: false,
                        contentType: false,
                        error: function() {
                            alert_personalizado("Desenho", 'Erro ao enviar o arquivo.');
                        }
                    });
                }

                fecharModal1('import_modal_add_desenho');
                if (tipo_input == 'mult') {
                    desenho_modal();
                } else {
                    desenho_modal_ind();
                }
            }
        });
    };

    var wlSelectsOriginal = selects;
    selects = function(preselecoes) {
        wlResetDependenciaBootstrapState();
        window.desenho_dependencia_preselecao = normalizarPreselecoesDependencia(preselecoes || (lista_array && lista_array.preselecoes ? lista_array.preselecoes : null));
        wlSelectsOriginal();
        agendarPreselecaoDependencia(80);
    };

</script>

