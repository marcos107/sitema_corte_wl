<?= $this->include('partials/wl-layout-open') ?>

<style>
.wl-upload-shell {
    display: grid;
    gap: 1rem;
}

.wl-step-track {
    display: flex;
    gap: .75rem;
    flex-wrap: wrap;
    margin-bottom: .75rem;
}

.wl-step-chip {
    appearance: none;
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    border-radius: 999px;
    padding: .4rem .85rem;
    font-size: .82rem;
    font-weight: 600;
    border: 1px solid #dbe1ea;
    background: #f8fafc;
    color: #475569;
    cursor: pointer;
    transition: all .2s ease;
}

.wl-step-chip.is-active {
    background: #dbeafe;
    border-color: #93c5fd;
    color: #1d4ed8;
}

.wl-step-chip:focus-visible {
    outline: 2px solid rgba(59, 130, 246, .35);
    outline-offset: 2px;
}

.wl-process-grid {
    display: grid;
    gap: .75rem;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    margin-top: .75rem;
}

.wl-process-card {
    border: 1px solid #dbe1ea;
    border-radius: .75rem;
    background: #fff;
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
}

.wl-process-type {
    display: inline-block;
    margin-top: .35rem;
    font-size: .72rem;
    border-radius: 999px;
    background: #eef2ff;
    color: #3730a3;
    padding: .2rem .55rem;
}

.wl-upload-drop {
    border: 2px dashed #bfdbfe;
    background: #f8fbff;
    border-radius: .9rem;
    padding: 1.2rem;
    text-align: center;
    transition: all .2s ease;
}

.wl-upload-drop.is-dragging {
    border-color: #3b82f6;
    background: #eff6ff;
}

.wl-upload-list {
    margin-top: .8rem;
    display: grid;
    gap: .5rem;
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

.wl-upload-summary dt {
    font-size: .74rem;
    color: #64748b;
    margin-bottom: .2rem;
}

.wl-upload-summary dd {
    margin: 0 0 .65rem;
    font-weight: 600;
    color: #0f172a;
}

.wl-flow-actions {
    display: flex;
    justify-content: space-between;
    gap: .75rem;
}

.container-mesma-linha {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: center;
    gap: .4rem;
    width: 100%;
}

.container-mesma-linha .custom-select {
    min-width: 0;
    width: 100%;
}

#modal.modal-1,
#modal_cadastrar.modal-1 {
    background-color: rgba(15, 23, 42, .58);
}

#modal .modal-dialog {
    width: min(96vw, 1700px);
    max-width: none;
    margin: 1rem auto;
}

#modal .modal-content {
    max-height: calc(100vh - 2.5rem);
    display: flex;
    flex-direction: column;
    border: 0;
    border-radius: .9rem;
    overflow: hidden;
    background-color: #ffffff;
    box-shadow: 0 28px 65px rgba(15, 23, 42, .28);
}

#modal .modal-body {
    padding: .85rem 1rem 1rem;
    overflow: auto;
    background: linear-gradient(180deg, #f8fbff 0%, #ffffff 72%);
}

#modal .modal-header,
#modal .modal-body,
#modal .modal-footer,
#modal_cadastrar .modal-content,
#modal_cadastrar .modal-header,
#modal_cadastrar .modal-body,
#modal_cadastrar .modal-footer {
    background-color: #ffffff;
}

#modal .modal-header,
#modal_cadastrar .modal-header {
    padding: .9rem 1.1rem;
    border-bottom: 1px solid #dbe5f1;
}

#modal .modal-title,
#modal_cadastrar .modal-title {
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: .01em;
}

#modal .modal-footer,
#modal_cadastrar .modal-footer {
    padding: .8rem 1rem;
    border-top: 1px solid #dbe5f1;
}

#modal .modal-footer .btn,
#modal_cadastrar .modal-footer .btn {
    min-width: 110px;
    border-radius: .55rem;
    font-weight: 600;
}

#modal .wl-modal-table-wrap {
    border: 1px solid #dbe5f1;
    border-radius: .75rem;
    background: #ffffff;
    max-height: calc(100vh - 13rem);
    overflow: auto;
}

#modal .wl-modal-table-wrap table {
    border-collapse: separate;
    border-spacing: 0;
    table-layout: fixed;
    min-width: 1180px;
    margin-bottom: 0;
}

#modal .wl-modal-table-wrap table th,
#modal .wl-modal-table-wrap table td {
    border-color: #e2e8f0 !important;
    padding: .45rem .55rem;
    vertical-align: middle;
    background-color: #ffffff;
}

#modal .wl-modal-table-wrap table tr:first-child th {
    position: sticky;
    top: 0;
    z-index: 5;
    background: #f1f5f9;
    color: #334155;
    font-size: .74rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .02em;
    white-space: nowrap;
}

#modal .wl-modal-table-wrap table tr:not(:first-child) > th {
    font-size: .86rem;
    font-weight: 500;
    color: #0f172a;
    text-transform: none;
    letter-spacing: normal;
}

#modal .wl-modal-table-wrap table th:first-child,
#modal .wl-modal-table-wrap table td:first-child {
    min-width: 220px;
}

#modal .wl-modal-table-wrap table th:nth-child(6),
#modal .wl-modal-table-wrap table td:nth-child(6),
#modal .wl-modal-table-wrap table th:nth-child(7),
#modal .wl-modal-table-wrap table td:nth-child(7),
#modal .wl-modal-table-wrap table th:nth-child(8),
#modal .wl-modal-table-wrap table td:nth-child(8) {
    min-width: 190px;
}

#modal .custom-select,
#modal select,
#modal input[type="text"] {
    width: 100%;
    min-height: 34px;
    border: 1px solid #cbd5e1;
    border-radius: .5rem;
    padding: .3rem .5rem;
    font-size: .85rem;
    background-color: #ffffff;
}

#modal textarea {
    border: 1px solid #cbd5e1;
    border-radius: .5rem;
    padding: .6rem;
    font-size: .86rem;
    background-color: #f8fafc;
}

#modal .container-mesma-linha .btn {
    width: 32px;
    height: 32px;
    min-width: 32px;
    flex: 0 0 32px;
    border-radius: .5rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    font-weight: 700;
}

#modal_cadastrar .modal-dialog {
    max-width: 560px;
    margin: 1.2rem auto;
}

#modal_cadastrar .modal-content {
    border: 0;
    border-radius: .9rem;
    overflow: hidden;
    box-shadow: 0 24px 55px rgba(15, 23, 42, .24);
}

#modal_cadastrar .modal-body {
    padding: 1rem 1.1rem .9rem;
}

#modal_cadastrar .modal-body .form-group {
    margin-bottom: .75rem;
}

#modal_cadastrar .modal-body label {
    display: block;
    margin-bottom: .3rem;
    font-weight: 600;
    color: #334155;
}

#modal_cadastrar .modal-body .form-control {
    border-radius: .5rem;
    border: 1px solid #cbd5e1;
    min-height: 36px;
}

@media (max-width: 768px) {
    #modal .modal-dialog {
        width: 99vw;
        margin: .6rem auto;
    }

    #modal .modal-content {
        max-height: calc(100vh - 1.2rem);
    }

    #modal .modal-body {
        padding: .65rem;
    }

    #modal .wl-modal-table-wrap {
        max-height: calc(100vh - 12.5rem);
    }

    #modal .wl-modal-table-wrap table {
        min-width: 1040px;
    }
}
</style>

<div id="modal" class="modal-1">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_titulo"></h5>
                <button type="button" class="btn-close" onclick="fecharModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal_bory"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="botao_fechar_modal" onclick="fecharModal()">Cancelar</button>
                <button type="button" class="btn btn-primary" id="botao_confirmar_modal" onclick="confirmarModal()"></button>
            </div>
        </div>
    </div>
</div>

<div id="modal_cadastrar" class="modal-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_cadastrar_titulo"></h5>
                <button type="button" class="btn-close" onclick="fecharModal('modal_cadastrar')" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal_cadastrar_bory"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="botao_fechar_modal_cadastrar" onclick="fecharModal('modal_cadastrar')">Cancelar</button>
                <button type="button" class="btn btn-primary" id="botao_confirmar_modal_cadastrar" onclick="cadastrar()">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card wl-card">
            <div class="card-body p-4 wl-upload-shell">
                <div class="wl-step-track">
                    <button type="button" id="step_chip_1" class="wl-step-chip is-active" onclick="irParaEtapa(1)">1. Processo</button>
                    <button type="button" id="step_chip_2" class="wl-step-chip" onclick="irParaEtapa(2)">2. Arquivos</button>
                </div>

                <div>
                    <h4 id="flow_title" class="mb-1">Escolha o processo</h4>
                    <p id="flow_subtitle" class="text-muted mb-0">Primeiro, selecione o processo de destino dos desenhos.</p>
                </div>

                <div id="step_process">
                    <label class="form-label fw-semibold">Processos disponíveis</label>
                    <div id="processos_radio" class="wl-process-grid"></div>
                </div>

                <div id="step_upload" class="d-none">
                    <label class="form-label fw-semibold">Arquivos de desenho</label>

                    <div id="upload_dropzone" class="wl-upload-drop">
                        <p class="mb-2">Arraste os arquivos para esta área ou selecione manualmente.</p>
                        <button type="button" id="upload_choose_btn" class="btn btn-outline-primary btn-sm">Selecionar arquivos</button>
                        <input type="file" name="file" id="desenhos_add" class="d-none" accept="<?= esc($filtro) ?>" multiple>
                    </div>

                    <div id="files_list" class="wl-upload-list"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card wl-card">
            <div class="card-header border-0">
                <h5 class="card-title mb-0">Resumo do envio</h5>
            </div>
            <div class="card-body">
                <dl class="wl-upload-summary mb-0">
                    <dt>Processo selecionado</dt>
                    <dd id="summary_processo">Nenhum processo selecionado</dd>

                    <dt>Arquivos selecionados</dt>
                    <dd id="summary_count">0 arquivo(s)</dd>
                </dl>
            </div>
        </div>

        <div class="wl-flow-actions mt-2">
            <button id="flow_back_btn" type="button" class="btn btn-light w-100" onclick="inicio_tela()" disabled>Voltar</button>
            <button id="flow_primary_btn" type="button" class="btn btn-primary w-100" onclick="adicionar()">Continuar</button>
        </div>
    </div>
</div>

<?= $this->include('partials/wl-layout-close') ?>
<?= $this->include('partials/wl-scripts') ?>

<script>
function mostrarModal(class_var = 'modal') {
    const modal = document.getElementById(class_var);
    if (!modal) return;
    modal.style.display = 'block';
    document.body.classList.add('no-scroll');
}

function fecharModal(class_var = 'modal') {
    const modal = document.getElementById(class_var);
    if (!modal) return;

    modal.style.display = 'none';

    const body = document.getElementById(class_var + '_bory');
    if (body) {
        body.innerHTML = '';
    }

    document.body.classList.remove('no-scroll');
}

window.onclick = function(event) {
    const modal = document.getElementById('modal');
    const modalCadastrar = document.getElementById('modal_cadastrar');

    if (event.target === modal) {
        modal.style.display = 'none';
        document.body.classList.remove('no-scroll');
    }

    if (event.target === modalCadastrar) {
        modalCadastrar.style.display = 'none';
        document.body.classList.remove('no-scroll');
    }
};
</script>

<?php if ($ajax != '') {
    echo view($ajax);
} ?>

<?= $this->include('partials/wl-layout-end') ?>
