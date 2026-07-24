<?= $this->include('partials/wl-layout-open') ?>

<style>
#painel-tarefas-container.is-loading {
    position: relative;
    min-height: 180px;
}

#painel-tarefas-container.is-loading::after {
    content: 'Carregando painel...';
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, .85);
    color: #334155;
    font-weight: 600;
    letter-spacing: .01em;
}

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

.wl-process-picker-head h6 {
    margin: 0 0 .2rem;
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
}

.wl-process-picker-head p {
    margin: 0;
    color: #64748b;
    font-size: .9rem;
}

.wl-process-step {
    background: #e0f2fe;
    color: #0369a1;
    border-radius: 999px;
    font-size: .74rem;
    font-weight: 700;
    padding: .24rem .62rem;
    letter-spacing: .01em;
}

.wl-process-grid {
    display: grid;
    gap: .75rem;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
}

.wl-process-card {
    border: 1px solid #dbe1ea;
    border-radius: .75rem;
    background: #ffffff;
    padding: .85rem;
    cursor: pointer;
    transition: all .2s ease;
    position: relative;
    margin: 0;
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

.wl-painel-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem;
}

.wl-painel-tab {
    border: 1px solid #dbe5f1;
    background: #f8fafc;
    color: #334155;
    border-radius: 999px;
    padding: .35rem .9rem;
    font-size: .84rem;
    font-weight: 600;
}

.wl-painel-tab.is-active {
    background: #dbeafe;
    border-color: #93c5fd;
    color: #1d4ed8;
}

.wl-painel-tab.is-disabled {
    opacity: .55;
    cursor: not-allowed;
}

.wl-toggle-concluidas {
    background: #f8fafc;
    border: 1px solid #dbe5f1;
    border-radius: .65rem;
    padding: .45rem .7rem;
}

.wl-toggle-concluidas .form-check-label {
    font-size: .84rem;
    color: #334155;
}

.wl-finalizados-filtro {
    display: flex;
    flex-wrap: wrap;
    align-items: end;
    gap: .5rem;
    background: #f8fafc;
    border: 1px solid #dbe5f1;
    border-radius: .65rem;
    padding: .45rem .6rem;
}

.wl-finalizados-filtro .form-label {
    margin-bottom: .2rem;
    font-size: .74rem;
    font-weight: 600;
    color: #475569;
}

.wl-finalizados-filtro .form-control {
    min-width: 145px;
}

.page-lista-tarefas .card-header {
    padding-bottom: 0;
}

.page-lista-tarefas .card-body {
    padding-top: 1rem;
}

.page-lista-tarefas .table thead th,
.page-lista-tarefas .table tfoot th {
    white-space: nowrap;
    font-size: .76rem;
    text-transform: uppercase;
    letter-spacing: .02em;
    color: #475569;
}

.page-lista-tarefas .table tbody td {
    vertical-align: middle;
}

.page-lista-tarefas .wl-tarefas-table {
    table-layout: fixed;
}

.page-lista-tarefas .wl-tarefas-table th:nth-child(1),
.page-lista-tarefas .wl-tarefas-table td:nth-child(1) {
    width: 90px;
    text-align: center;
}

.page-lista-tarefas .wl-tarefas-table th:nth-child(2),
.page-lista-tarefas .wl-tarefas-table td:nth-child(2) {
    width: 64px;
    text-align: center;
}

.page-lista-tarefas .wl-tarefas-table th:nth-child(3),
.page-lista-tarefas .wl-tarefas-table td:nth-child(3) {
    width: 140px;
}

.page-lista-tarefas .wl-tarefas-table th:nth-child(4),
.page-lista-tarefas .wl-tarefas-table td:nth-child(4) {
    min-width: 280px;
}

.page-lista-tarefas .wl-tarefas-table td:nth-child(4),
.page-lista-tarefas .wl-tarefas-table td:nth-child(8) {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.page-lista-tarefas .wl-tarefas-table th:nth-child(5),
.page-lista-tarefas .wl-tarefas-table td:nth-child(5),
.page-lista-tarefas .wl-tarefas-table th:nth-child(6),
.page-lista-tarefas .wl-tarefas-table td:nth-child(6),
.page-lista-tarefas .wl-tarefas-table th:nth-child(7),
.page-lista-tarefas .wl-tarefas-table td:nth-child(7) {
    width: 150px;
}

.page-lista-tarefas .wl-tarefas-table th:nth-child(8),
.page-lista-tarefas .wl-tarefas-table td:nth-child(8) {
    min-width: 200px;
}

.page-lista-tarefas .wl-tarefas-table th:nth-child(9),
.page-lista-tarefas .wl-tarefas-table td:nth-child(9) {
    width: 145px;
    text-align: center;
}

.page-lista-tarefas .wl-tarefas-table th:nth-child(10),
.page-lista-tarefas .wl-tarefas-table td:nth-child(10),
.page-lista-tarefas .wl-tarefas-table th:nth-child(11),
.page-lista-tarefas .wl-tarefas-table td:nth-child(11),
.page-lista-tarefas .wl-tarefas-table th:nth-child(12),
.page-lista-tarefas .wl-tarefas-table td:nth-child(12) {
    width: 180px;
    text-align: right;
}

.page-lista-tarefas .wl-tarefas-table td {
    position: relative;
}

.page-lista-tarefas .wl-tarefas-table td:nth-child(10),
.page-lista-tarefas .wl-tarefas-table td:nth-child(11),
.page-lista-tarefas .wl-tarefas-table td:nth-child(12) {
    z-index: 4;
    overflow: visible;
}

.wl-row-actions {
    display: flex;
    flex-wrap: nowrap;
    gap: .35rem;
    justify-content: flex-end;
    align-items: center;
}

.wl-row-action-main {
    min-width: 112px;
}

#painel-tarefas-container,
#painel-tarefas-container .card-body,
#painel-tarefas-container .table-responsive,
#painel-tarefas-container .dataTables_wrapper {
    overflow: visible;
}

#painel-tarefas-container .table-responsive {
    overflow-x: auto;
    overflow-y: visible !important;
}

#painel-tarefas-container .wl-row-actions,
#painel-tarefas-container .wl-row-actions * {
    pointer-events: auto;
}

#painel-tarefas-container .wl-row-actions .dropdown {
    position: relative;
    z-index: 30;
}

#painel-tarefas-container .wl-row-actions .btn {
    line-height: 1.1;
    font-weight: 600;
}

#painel-tarefas-container .wl-row-action-more {
    width: 32px;
    min-width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

#painel-tarefas-container .wl-row-actions .dropdown-menu {
    min-width: 190px;
}

#painel-tarefas-container .wl-row-actions .dropdown-item {
    display: flex;
    align-items: center;
    gap: .25rem;
    font-size: .84rem;
}

#painel-tarefas-container .wl-row-actions--confirm .btn {
    min-width: 104px;
}

#painel-tarefas-container .dropdown-menu {
    min-width: 210px;
    z-index: 3000;
}

.wl-bulk-modal .form-label {
    font-weight: 600;
    color: #334155;
}

.wl-bulk-modal .form-text {
    color: #64748b;
}

.wl-modal-wide .modal-dialog {
    max-width: min(96vw, 1600px);
}

.wl-modal-wide .modal-content {
    max-height: calc(100vh - 2rem);
}

.wl-modal-wide .modal-body {
    overflow: auto;
}

.wl-bulk-modal .table th,
.wl-bulk-modal .table td {
    white-space: nowrap;
    vertical-align: middle;
}

.wl-bulk-modal .wl-bulk-prio {
    font-weight: 700;
    text-align: center;
}

.page-lista-tarefas .wl-cell-truncate {
    display: inline-block;
    max-width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    vertical-align: middle;
}

.page-lista-tarefas .wl-tarefas-table.wl-processo-ind th:nth-child(4),
.page-lista-tarefas .wl-tarefas-table.wl-processo-ind td:nth-child(4) {
    width: 320px;
    min-width: 320px;
}

.page-lista-tarefas .wl-tarefas-table.wl-processo-ind td:nth-child(4),
.page-lista-tarefas .wl-tarefas-table.wl-processo-ind td:nth-child(4) .wl-cell-truncate {
    white-space: normal;
    overflow: visible;
    text-overflow: clip;
    word-break: break-word;
    overflow-wrap: anywhere;
}

.page-lista-tarefas .wl-tarefas-concluidas-table {
    min-width: 1540px;
    table-layout: fixed;
}

.page-lista-tarefas .wl-tarefas-concluidas-table th:nth-child(1),
.page-lista-tarefas .wl-tarefas-concluidas-table td:nth-child(1) {
    width: 90px;
    text-align: center;
}

.page-lista-tarefas .wl-tarefas-concluidas-table th:nth-child(2),
.page-lista-tarefas .wl-tarefas-concluidas-table td:nth-child(2) {
    width: 145px;
    text-align: left;
}

.page-lista-tarefas .wl-tarefas-concluidas-table th:nth-child(3),
.page-lista-tarefas .wl-tarefas-concluidas-table td:nth-child(3) {
    width: 320px;
    min-width: 320px;
}

.page-lista-tarefas .wl-tarefas-concluidas-table th:nth-child(4),
.page-lista-tarefas .wl-tarefas-concluidas-table td:nth-child(4),
.page-lista-tarefas .wl-tarefas-concluidas-table th:nth-child(5),
.page-lista-tarefas .wl-tarefas-concluidas-table td:nth-child(5),
.page-lista-tarefas .wl-tarefas-concluidas-table th:nth-child(6),
.page-lista-tarefas .wl-tarefas-concluidas-table td:nth-child(6) {
    width: 150px;
}

.page-lista-tarefas .wl-tarefas-concluidas-table th:nth-child(7),
.page-lista-tarefas .wl-tarefas-concluidas-table td:nth-child(7) {
    width: 180px;
}

.page-lista-tarefas .wl-tarefas-concluidas-table th:nth-child(8),
.page-lista-tarefas .wl-tarefas-concluidas-table td:nth-child(8) {
    width: 120px;
    text-align: center;
}

.page-lista-tarefas .wl-tarefas-concluidas-table th:nth-child(9),
.page-lista-tarefas .wl-tarefas-concluidas-table td:nth-child(9),
.page-lista-tarefas .wl-tarefas-concluidas-table th:nth-child(10),
.page-lista-tarefas .wl-tarefas-concluidas-table td:nth-child(10) {
    width: 145px;
    text-align: center;
}

.page-lista-tarefas .wl-tarefas-concluidas-table th:nth-child(11),
.page-lista-tarefas .wl-tarefas-concluidas-table td:nth-child(11) {
    width: 300px;
    min-width: 300px;
    max-width: 300px;
    text-align: right;
    overflow: visible;
    z-index: 4;
}

.page-lista-tarefas .wl-tarefas-concluidas-table td:nth-child(3),
.page-lista-tarefas .wl-tarefas-concluidas-table td:nth-child(3) .wl-cell-truncate,
.page-lista-tarefas .wl-tarefas-concluidas-table .wl-linked-files {
    white-space: normal;
    overflow: visible;
    text-overflow: clip;
    word-break: break-word;
    overflow-wrap: anywhere;
}

.page-lista-tarefas .wl-tarefas-concluidas-table .wl-linked-files {
    margin-top: .25rem;
    line-height: 1.25;
}

.page-lista-tarefas .wl-tarefas-concluidas-table .wl-row-actions {
    flex-wrap: wrap;
}

.wl-view-toggle .form-check-label {
    font-size: .84rem;
    color: #334155;
}

#painel-tarefas-container.wl-view-detailed .wl-tarefas-table {
    table-layout: auto;
}

#painel-tarefas-container.wl-view-detailed .wl-tarefas-table td:nth-child(4),
#painel-tarefas-container.wl-view-detailed .wl-tarefas-table td:nth-child(8) {
    white-space: normal;
    overflow: visible;
    text-overflow: clip;
}

#painel-tarefas-container.wl-view-detailed .wl-cell-truncate {
    display: block;
    max-width: none;
    white-space: normal;
    overflow: visible;
    text-overflow: clip;
    word-break: break-word;
}

#painel-tarefas-container tr.wl-row-menu-open {
    position: relative;
    z-index: 35;
}

#painel-tarefas-container tr.wl-row-menu-open > td {
    position: relative;
    z-index: 35;
    overflow: visible !important;
}

@media (max-width: 576px) {
    .wl-process-picker {
        padding: .75rem;
    }

    .wl-process-picker-actions .btn {
        width: 100%;
        min-width: 0;
    }

    .wl-row-action-main {
        min-width: 92px;
        padding-left: .55rem;
        padding-right: .55rem;
    }

    .wl-row-actions--confirm .btn {
        min-width: 92px;
    }
}
</style>

<?php $acessoAbas = isset($acessoAbas) && is_array($acessoAbas) ? $acessoAbas : []; ?>

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <div>
        <h4 class="mb-sm-0"><?= esc($titulo ?? 'Painel de Tarefas') ?></h4>
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Desenhos</a></li>
            <li class="breadcrumb-item active">Painel de Tarefas</li>
        </ol>
    </div>
</div>

<div class="card wl-card page-lista-tarefas">
    <div class="card-header border-0">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h5 class="card-title mb-1" id="painel-card-title">Escolha o processo</h5>
                <p class="text-muted mb-0" id="painel-card-subtitle">Primeiro, selecione o processo para carregar a lista de tarefas.</p>
            </div>
            <span class="badge bg-primary-subtle text-primary fw-semibold">
                <i class="ri-time-line align-bottom me-1"></i>Fila ativa
            </span>
        </div>
    </div>

    <div class="card-body" id="painel-process-picker-wrap" <?= empty($processos) ? 'style="display:none;"' : '' ?>>
        <div class="wl-process-picker">
            <div class="wl-process-picker-head">
                <div>
                    <h6>Selecionar Processo</h6>
                    <p>Primeiro, selecione o processo para carregar a lista de tarefas.</p>
                </div>
                <span class="wl-process-step">Etapa 1/2</span>
            </div>

            <div class="wl-process-grid" id="painel-process-grid">
                <?php foreach (($processos ?? []) as $idx => $processo) { ?>
                    <label class="wl-process-card<?= ((int) $idx === 0) ? ' is-selected' : '' ?>" for="painel_processo_<?= (int) $idx ?>">
                        <input type="radio" id="painel_processo_<?= (int) $idx ?>" name="painel_processo" value="<?= (int) ($processo['id'] ?? 0) ?>" <?= ((int) $idx === 0) ? 'checked' : '' ?>>
                        <span class="wl-process-name"><?= esc($processo['nome'] ?? '') ?></span>
                    </label>
                <?php } ?>
            </div>

            <div class="wl-process-picker-actions">
                <button type="button" class="btn btn-primary" id="painel-process-continuar">Continuar</button>
            </div>
        </div>
    </div>

    <?php if (empty($processos)) { ?>
        <div class="card-body">
            <div class="wl-process-empty">Nenhum processo disponivel para o seu perfil.</div>
        </div>
    <?php } ?>

    <div class="card-body" id="painel-main-wrap" style="display:none;">
        <div class="row g-3 align-items-end mb-3">
            <div class="col-12 col-lg">
                <div class="wl-painel-tabs" id="painel-aba-tabs">
                    <?php foreach (($abas ?? []) as $abaKey => $abaLabel) { ?>
                        <?php $abaPermitida = !empty($acessoAbas[$abaKey]); ?>
                        <button
                            type="button"
                            class="wl-painel-tab<?= (($abaInicial ?? '') === $abaKey) ? ' is-active' : '' ?><?= $abaPermitida ? '' : ' is-disabled' ?>"
                            data-aba="<?= esc($abaKey) ?>"
                            data-enabled="<?= $abaPermitida ? '1' : '0' ?>">
                            <?= esc($abaLabel) ?>
                        </button>
                    <?php } ?>
                </div>
            </div>
            <div class="col-12 col-lg-auto">
                <div class="form-check form-switch wl-toggle-concluidas" id="painel-toggle-concluidas-wrap">
                    <input class="form-check-input" type="checkbox" id="painel-mostrar-concluidas">
                    <label class="form-check-label" for="painel-mostrar-concluidas">Tabela de finalizados</label>
                </div>
            </div>
            <div class="col-12 col-lg-auto">
                <div class="form-check form-switch wl-toggle-concluidas wl-view-toggle">
                    <input class="form-check-input" type="checkbox" id="painel-visualizacao-detalhada">
                    <label class="form-check-label" for="painel-visualizacao-detalhada">Visualizacao detalhada</label>
                </div>
            </div>
            <div class="col-12 col-lg-auto" id="painel-finalizados-filtro-wrap" style="display:none;">
                <div class="wl-finalizados-filtro">
                    <div>
                        <label class="form-label" for="painel-finalizados-inicio">De</label>
                        <input type="date" class="form-control form-control-sm" id="painel-finalizados-inicio">
                    </div>
                    <div>
                        <label class="form-label" for="painel-finalizados-fim">Ate</label>
                        <input type="date" class="form-control form-control-sm" id="painel-finalizados-fim">
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm" id="painel-finalizados-buscar">
                            Buscar finalizados
                        </button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="painel-recolocar-pendencias">
                            Solicitacoes recolocar
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-auto" id="painel-meus-filtro-wrap" style="display:none;">
                <div class="wl-finalizados-filtro">
                    <div>
                        <label class="form-label" for="painel-meus-inicio">Meus desenhos: de</label>
                        <input type="date" class="form-control form-control-sm" id="painel-meus-inicio">
                    </div>
                    <div>
                        <label class="form-label" for="painel-meus-fim">Ate</label>
                        <input type="date" class="form-control form-control-sm" id="painel-meus-fim">
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm" id="painel-meus-buscar">
                            Buscar meus desenhos
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-auto">
                <button type="button" class="btn btn-outline-primary btn-sm" id="painel-trocar-processo">
                    <i class="ri-arrow-left-line align-bottom me-1"></i>Voltar
                </button>
            </div>
        </div>

        <div id="painel-tarefas-container"><?= $conteudoInicial ?? '' ?></div>
    </div>
</div>

<div class="modal fade wl-bulk-modal wl-modal-wide" id="painel-bulk-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="painel-bulk-modal-title">Atualizar prioridade e ordem em lote</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div id="painel-bulk-config-wrap">
                    <div class="mb-3">
                        <label for="painel-bulk-prioridade" class="form-label">Prioridade</label>
                        <select id="painel-bulk-prioridade" class="form-select"></select>
                    </div>
                    <div class="mb-2">
                        <label for="painel-bulk-ordem" class="form-label">Ordem inicial</label>
                        <select id="painel-bulk-ordem" class="form-select"></select>
                        <div class="form-text">Os selecionados serao posicionados em sequencia: ordem inicial, +1, +2...</div>
                    </div>
                </div>
                <div class="small text-muted" id="painel-bulk-modal-help">
                    Selecione os desenhos pendentes que receberao a nova prioridade e ordem.
                </div>
                <div class="small text-muted mt-1">
                    Itens selecionados: <strong id="painel-bulk-modal-count">0</strong>
                </div>
                <div class="table-responsive mt-3">
                    <table id="painel-bulk-table" class="table table-bordered table-striped table-hover table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Prioridade</th>
                                <th>Ordem</th>
                                <th>Desenhista</th>
                                <th>Nome do arquivo</th>
                                <th>Empresa/Cliente</th>
                                <th>Empreendimento</th>
                                <th>Finalidade</th>
                                <th>Subpastas</th>
                                <th>Data de Envio</th>
                                <th class="text-center">Selecionar</th>
                            </tr>
                        </thead>
                        <tbody id="painel-bulk-table-body"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="painel-bulk-confirmar">Aplicar em lote</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade wl-bulk-modal wl-modal-wide" id="painel-item-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="painel-item-modal-title">Modificar prioridade desenho</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="painel-item-id" value="">
                <div class="mb-3">
                    <label for="painel-item-prioridade" class="form-label">Prioridade</label>
                    <select id="painel-item-prioridade" class="form-select"></select>
                </div>
                <div class="mb-2">
                    <label for="painel-item-ordem" class="form-label">Ordem</label>
                    <select id="painel-item-ordem" class="form-select"></select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="painel-item-confirmar">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="painel-autorizacao-conclusao-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Autorizar conclusao da tarefa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="painel-autorizacao-conclusao-id" value="">
                <p class="text-muted mb-3" id="painel-autorizacao-conclusao-msg">
                    Informe um usuario com permissao para autorizar a conclusao.
                </p>
                <div class="mb-2">
                    <label class="form-label" for="painel-autorizacao-conclusao-usuario">Usuario autorizador</label>
                    <input type="text" class="form-control" id="painel-autorizacao-conclusao-usuario" autocomplete="username">
                </div>
                <div>
                    <label class="form-label" for="painel-autorizacao-conclusao-senha">Senha</label>
                    <input type="password" class="form-control" id="painel-autorizacao-conclusao-senha" autocomplete="current-password">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="painel-autorizacao-conclusao-confirmar">Autorizar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade wl-bulk-modal wl-modal-wide" id="painel-recolocar-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Solicitacoes de recolocar pendentes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2 d-flex flex-wrap justify-content-between gap-2">
                    <p class="text-muted mb-0" id="painel-recolocar-info">
                        Lista global de solicitacoes pendentes (todos os processos permitidos).
                    </p>
                    <span class="badge bg-primary-subtle text-primary fw-semibold" id="painel-recolocar-count">0 pendente(s)</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-nowrap align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Processo</th>
                                <th>Prioridade</th>
                                <th>Desenhista</th>
                                <th>Nome do arquivo</th>
                                <th>Solicitante</th>
                                <th>Data da solicitacao</th>
                                <th>Qtd</th>
                                <th class="text-end text-nowrap">Acao</th>
                            </tr>
                        </thead>
                        <tbody id="painel-recolocar-body">
                            <tr>
                                <td colspan="8" class="text-center text-muted">Clique em "Solicitacoes recolocar" para carregar.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="painel-confirmacao-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar acao</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0" id="painel-confirmacao-texto">Deseja continuar?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="painel-confirmacao-confirmar">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->include('partials/wl-layout-close') ?>
<?= $this->include('partials/wl-scripts') ?>

<script>
(function () {
    var containerTarefas = document.getElementById('painel-tarefas-container');
    var endpointLista = '<?= base_url('public/painel_tarefas_lista') ?>';
    var endpointAcao = '<?= base_url('public/painel_tarefas_acao') ?>';
    var usuarioPodeAutorizarConclusao = <?= !empty($usuarioPodeAutorizarConclusao) ? 'true' : 'false' ?>;
    var usuarioPodeGerenciarRecolocar = <?= !empty($usuarioPodeGerenciarRecolocar) ? 'true' : 'false' ?>;
    var tabs = document.querySelectorAll('#painel-aba-tabs [data-aba]');
    var abaAtual = '<?= esc($abaInicial ?? '') ?>';
    var processos = <?= json_encode(array_values($processos ?? []), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    var prioridadesModal = <?= json_encode(array_values($prioridadesModal ?? []), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    var pickerWrap = document.getElementById('painel-process-picker-wrap');
    var mainWrap = document.getElementById('painel-main-wrap');
    var btnContinuar = document.getElementById('painel-process-continuar');
    var btnTrocar = document.getElementById('painel-trocar-processo');
    var cardTitle = document.getElementById('painel-card-title');
    var cardSubtitle = document.getElementById('painel-card-subtitle');
    var toggleConcluidasWrap = document.getElementById('painel-toggle-concluidas-wrap');
    var mostrarConcluidasInput = document.getElementById('painel-mostrar-concluidas');
    var visualizacaoDetalhadaInput = document.getElementById('painel-visualizacao-detalhada');
    var finalizadosFiltroWrap = document.getElementById('painel-finalizados-filtro-wrap');
    var finalizadosInicioInput = document.getElementById('painel-finalizados-inicio');
    var finalizadosFimInput = document.getElementById('painel-finalizados-fim');
    var finalizadosBuscarBtn = document.getElementById('painel-finalizados-buscar');
    var recolocarPendenciasBtn = document.getElementById('painel-recolocar-pendencias');
    var meusFiltroWrap = document.getElementById('painel-meus-filtro-wrap');
    var meusInicioInput = document.getElementById('painel-meus-inicio');
    var meusFimInput = document.getElementById('painel-meus-fim');
    var meusBuscarBtn = document.getElementById('painel-meus-buscar');
    var mostrarConcluidasPorAba = {
        meus_desenhos: false,
        lista_tarefas: false,
        lista_tarefas_adm: false,
        tarefas_concluidas: true
    };
    var mostrarConcluidas = !!mostrarConcluidasPorAba[abaAtual];
    var finalizadosDataInicioPadrao = '<?= esc($finalizadosDataInicioPadrao ?? '') ?>';
    var finalizadosDataFimPadrao = '<?= esc($finalizadosDataFimPadrao ?? '') ?>';
    var finalizadosPeriodoPadraoAtivo = !!(finalizadosDataInicioPadrao && finalizadosDataFimPadrao);
    var finalizadosPeriodoAplicadoPorAba = {
        tarefas_concluidas: finalizadosPeriodoPadraoAtivo
    };
    var finalizadosPeriodoInicioPorAba = {
        tarefas_concluidas: finalizadosDataInicioPadrao
    };
    var finalizadosPeriodoFimPorAba = {
        tarefas_concluidas: finalizadosDataFimPadrao
    };
    var meusDataInicioPadrao = '<?= esc($meusDataInicioPadrao ?? '') ?>';
    var meusDataFimPadrao = '<?= esc($meusDataFimPadrao ?? '') ?>';
    var meusPeriodoPadraoAtivo = !!(meusDataInicioPadrao && meusDataFimPadrao);
    var meusPeriodoAplicadoPorAba = {
        meus_desenhos: meusPeriodoPadraoAtivo
    };
    var meusPeriodoInicioPorAba = {
        meus_desenhos: meusDataInicioPadrao
    };
    var meusPeriodoFimPorAba = {
        meus_desenhos: meusDataFimPadrao
    };

    var processoAtualId = 0;
    var processoAtualNome = '';
    var bulkModalEl = document.getElementById('painel-bulk-modal');
    var bulkModalInstance = null;
    var bulkModalTitle = document.getElementById('painel-bulk-modal-title');
    var bulkConfigWrap = document.getElementById('painel-bulk-config-wrap');
    var bulkHelpText = document.getElementById('painel-bulk-modal-help');
    var bulkPrioridade = document.getElementById('painel-bulk-prioridade');
    var bulkOrdem = document.getElementById('painel-bulk-ordem');
    var bulkConfirmarBtn = document.getElementById('painel-bulk-confirmar');
    var bulkCountModal = document.getElementById('painel-bulk-modal-count');
    var bulkTableBody = document.getElementById('painel-bulk-table-body');
    var bulkLista = [];
    var bulkAgrupados = {};
    var bulkItemTipo = 'desenho';
    var bulkSelectedIds = new Set();
    var bulkActionMode = 'mudar_prioridade';
    var itemModalEl = document.getElementById('painel-item-modal');
    var itemModalInstance = null;
    var itemModalTitle = document.getElementById('painel-item-modal-title');
    var itemModalIdInput = document.getElementById('painel-item-id');
    var itemModalPrioridade = document.getElementById('painel-item-prioridade');
    var itemModalOrdem = document.getElementById('painel-item-ordem');
    var itemModalConfirmarBtn = document.getElementById('painel-item-confirmar');
    var itemModalOrdemOriginal = 1;
    var autorizacaoModalEl = document.getElementById('painel-autorizacao-conclusao-modal');
    var autorizacaoModalInstance = null;
    var autorizacaoIdInput = document.getElementById('painel-autorizacao-conclusao-id');
    var autorizacaoUsuarioInput = document.getElementById('painel-autorizacao-conclusao-usuario');
    var autorizacaoSenhaInput = document.getElementById('painel-autorizacao-conclusao-senha');
    var autorizacaoMensagem = document.getElementById('painel-autorizacao-conclusao-msg');
    var autorizacaoConfirmarBtn = document.getElementById('painel-autorizacao-conclusao-confirmar');
    var recolocarModalEl = document.getElementById('painel-recolocar-modal');
    var recolocarModalInstance = null;
    var recolocarInfo = document.getElementById('painel-recolocar-info');
    var recolocarCount = document.getElementById('painel-recolocar-count');
    var recolocarTableBody = document.getElementById('painel-recolocar-body');
    var confirmacaoModalEl = document.getElementById('painel-confirmacao-modal');
    var confirmacaoModalInstance = null;
    var confirmacaoTextoEl = document.getElementById('painel-confirmacao-texto');
    var confirmacaoConfirmarBtn = document.getElementById('painel-confirmacao-confirmar');
    var confirmacaoOnConfirm = null;
    var viewModeStorageKey = 'wl_lista_modo_visualizacao';
    var painelTabelaDt = null;
    var painelListaMeta = {};

    function lerModoVisualizacaoDetalhada() {
        try {
            return window.localStorage.getItem(viewModeStorageKey) === 'detalhada';
        } catch (error) {
            return false;
        }
    }

    function salvarModoVisualizacaoDetalhada(ativada) {
        try {
            window.localStorage.setItem(viewModeStorageKey, ativada ? 'detalhada' : 'compacta');
        } catch (error) {
            // Ignora falhas de storage e mantem o modo apenas nesta carga.
        }
    }

    function aplicarModoVisualizacaoPainel() {
        if (!containerTarefas) {
            return;
        }

        var ativada = visualizacaoDetalhadaInput ? !!visualizacaoDetalhadaInput.checked : lerModoVisualizacaoDetalhada();
        containerTarefas.classList.toggle('wl-view-detailed', ativada);
    }

    function alternarClasseLinhaDropdownPainel(evento, aberta) {
        var dropdown = evento.target && typeof evento.target.closest === 'function'
            ? evento.target.closest('.dropdown')
            : null;

        if (!dropdown) {
            return;
        }

        var linha = dropdown.closest('tr');
        if (linha) {
            linha.classList.toggle('wl-row-menu-open', aberta);
        }
    }

    function atualizarContadorSelecionados() {
        if (bulkCountModal) {
            bulkCountModal.textContent = String(bulkSelectedIds.size);
        }
    }

    function bulkModoEhApagar() {
        return bulkActionMode === 'apagar';
    }

    function contarItensPendentesLote() {
        return bulkLista.reduce(function (total, item) {
            return String(item && item.status_normalizado || '').toLowerCase() === 'pendente' ? total + 1 : total;
        }, 0);
    }

    function configurarModalLoteModo(modo) {
        bulkActionMode = modo === 'apagar' ? 'apagar' : 'mudar_prioridade';

        if (bulkModalTitle) {
            bulkModalTitle.textContent = bulkModoEhApagar()
                ? 'Apagar desenhos em lote'
                : 'Atualizar prioridade e ordem em lote';
        }

        if (bulkHelpText) {
            bulkHelpText.textContent = bulkModoEhApagar()
                ? 'Selecione os desenhos pendentes que serao apagados e enviados para a lixeira.'
                : 'Selecione os desenhos pendentes que receberao a nova prioridade e ordem.';
        }

        if (bulkConfigWrap) {
            bulkConfigWrap.style.display = bulkModoEhApagar() ? 'none' : '';
        }

        if (bulkConfirmarBtn) {
            bulkConfirmarBtn.textContent = bulkModoEhApagar() ? 'Apagar selecionados' : 'Aplicar em lote';
            bulkConfirmarBtn.classList.remove('btn-primary', 'btn-danger');
            bulkConfirmarBtn.classList.add(bulkModoEhApagar() ? 'btn-danger' : 'btn-primary');
        }
    }

    function preencherSelectPrioridadesLote() {
        if (!bulkPrioridade) {
            return;
        }

        bulkPrioridade.innerHTML = '';
        prioridadesModal.forEach(function (item) {
            var id = parseInt(item.id || '0', 10);
            if (!id) {
                return;
            }

            var option = document.createElement('option');
            option.value = String(id);
            option.textContent = item.nome || ('Prioridade ' + id);
            bulkPrioridade.appendChild(option);
        });
    }

    function popularOrdemSelectLote(prioridadeId, ordemPadrao) {
        if (!bulkOrdem) {
            return;
        }

        var prioId = parseInt(prioridadeId || '0', 10);
        var maxOrder = parseInt((bulkAgrupados && bulkAgrupados[String(prioId)]) || bulkAgrupados[prioId] || '0', 10);
        if (!maxOrder || maxOrder < 1) {
            maxOrder = 1;
        }

        bulkOrdem.innerHTML = '';
        for (var i = 1; i <= (maxOrder + 1); i++) {
            var option = document.createElement('option');
            option.value = String(i);
            option.textContent = 'Ordem ' + i;
            if (parseInt(ordemPadrao || '0', 10) === i) {
                option.selected = true;
            }
            bulkOrdem.appendChild(option);
        }

        if (!bulkOrdem.value && bulkOrdem.options.length) {
            bulkOrdem.options[0].selected = true;
        }
    }

    function destruirDataTableModalLote() {
        if (!(window.jQuery && $.fn && $.fn.DataTable)) {
            return;
        }
        if ($.fn.DataTable.isDataTable('#painel-bulk-table')) {
            $('#painel-bulk-table').DataTable().destroy();
        }
    }

    function inicializarDataTableModalLote() {
        if (!(window.jQuery && $.fn && $.fn.DataTable && $('#painel-bulk-table').length)) {
            return;
        }

        $('#painel-bulk-table').DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            pageLength: 10,
            order: [[0, 'asc'], [1, 'asc']],
            drawCallback: function () {
                var checks = document.querySelectorAll('#painel-bulk-table .js-bulk-modal-row');
                checks.forEach(function (cb) {
                    var id = parseInt(cb.value || '0', 10);
                    cb.checked = id > 0 && bulkSelectedIds.has(id);
                });
            },
            language: {
                decimal: '',
                emptyTable: 'Sem dados disponiveis',
                infoEmpty: 'Mostrando de 0 ate 0 de 0 registros',
                infoFiltered: '(filtrado do total de registros)',
                infoPostFix: '',
                thousands: ',',
                lengthMenu: 'MENU',
                loadingRecords: 'Carregando dados...',
                processing: 'Processando...',
                search: 'Buscar:',
                zeroRecords: 'Nao foram encontrados resultados',
                paginate: {
                    first: 'Primeiro',
                    last: 'Ultimo',
                    next: 'Seguinte',
                    previous: 'Anterior'
                },
                aria: {
                    sortAscending: ': clique para ordenar ascendente',
                    sortDescending: ': clique para ordenar descendente'
                }
            }
        });
    }

    function renderizarTabelaModalLote() {
        if (!bulkTableBody) {
            return;
        }

        destruirDataTableModalLote();
        bulkTableBody.innerHTML = '';
        bulkSelectedIds.clear();

        var pendentes = bulkLista.filter(function (item) {
            return String(item && item.status_normalizado || '').toLowerCase() === 'pendente';
        });

        pendentes.forEach(function (item) {
            var id = parseInt(item.id || '0', 10);
            if (!id) {
                return;
            }

            var tr = document.createElement('tr');
            tr.innerHTML = ''
                + '<td class="wl-bulk-prio" style="background:' + escapeHtml(item.prioridade_cor || '#cbd5e1') + ';color:' + escapeHtml(item.prioridade_texto || '#0f172a') + ';">' + escapeHtml(item.prioridade_nome || '-') + '</td>'
                + '<td>' + escapeHtml(item.ordem || '') + '</td>'
                + '<td>' + escapeHtml(item.desenhista_nome || '') + '</td>'
                + '<td>' + escapeHtml(item.nome_arquivo || '') + '</td>'
                + '<td>' + escapeHtml(item.empresa_nome || '') + '</td>'
                + '<td>' + renderizarEmpreendimentoComEscala(item) + '</td>'
                + '<td>' + escapeHtml(item.finalidade_nome || '') + '</td>'
                + '<td>' + escapeHtml(item.subpastas || '') + '</td>'
                + '<td>' + escapeHtml(item.data_envio || '') + '</td>'
                + '<td class="text-center"><input type="checkbox" class="form-check-input js-bulk-modal-row" value="' + id + '"></td>';
            bulkTableBody.appendChild(tr);
        });

        inicializarDataTableModalLote();
        atualizarContadorSelecionados();
    }

    function idsSelecionadosModalNaOrdem() {
        var ids = [];
        if (!bulkTableBody) {
            return ids;
        }

        var selecionadosMap = {};
        bulkSelectedIds.forEach(function (id) {
            selecionadosMap[id] = true;
        });

        bulkLista.forEach(function (item) {
            var id = parseInt(item.id || '0', 10);
            if (id > 0 && selecionadosMap[id]) {
                ids.push(id);
            }
        });

        return ids;
    }

    function buscarDadosLote(callback) {
        if (!processoAtualId) {
            exibirAlerta('error', 'Selecione um processo antes de usar alteracao em lote.');
            return;
        }

        containerTarefas.classList.add('is-loading');
        $.ajax({
            url: endpointAcao,
            type: 'POST',
            dataType: 'json',
            data: {
                acao: 'dados_lote',
                processo_id: processoAtualId
            },
            success: function (response) {
                var ok = !!(response && response.ok);
                if (!ok) {
                    exibirAlerta('error', (response && response.mensagem) || 'Nao foi possivel carregar os dados do lote.');
                    if (typeof callback === 'function') {
                        callback(false);
                    }
                    return;
                }

                bulkLista = Array.isArray(response.lista) ? response.lista : [];
                bulkAgrupados = (response.agrupados && typeof response.agrupados === 'object') ? response.agrupados : {};
                bulkItemTipo = response.item_tipo === 'projeto' ? 'projeto' : 'desenho';
                if (typeof callback === 'function') {
                    callback(true);
                }
            },
            error: function (xhr) {
                var mensagem = xhr && xhr.responseJSON && xhr.responseJSON.mensagem
                    ? xhr.responseJSON.mensagem
                    : 'Erro ao carregar dados do modal.';
                exibirAlerta('error', mensagem);
                if (typeof callback === 'function') {
                    callback(false);
                }
            },
            complete: function () {
                containerTarefas.classList.remove('is-loading');
            }
        });
    }

    function carregarDadosModalLote() {
        buscarDadosLote(function (ok) {
            if (!ok) {
                return;
            }

            if (!contarItensPendentesLote()) {
                exibirAlerta('error', bulkModoEhApagar()
                    ? 'Nao ha desenhos pendentes para apagar em lote neste processo.'
                    : 'Nao ha desenhos pendentes para atualizar em lote neste processo.');
                return;
            }

            if (!bulkModoEhApagar()) {
                preencherSelectPrioridadesLote();
                if (!prioridadesModal.length) {
                    exibirAlerta('error', 'Nao ha prioridades ativas para aplicar em lote.');
                    return;
                }

                var prioridadeAtual = parseInt((bulkPrioridade && bulkPrioridade.value) || '0', 10);
                if (!prioridadeAtual && prioridadesModal.length) {
                    prioridadeAtual = parseInt(prioridadesModal[0].id || '0', 10);
                    if (bulkPrioridade && prioridadeAtual) {
                        bulkPrioridade.value = String(prioridadeAtual);
                    }
                }

                popularOrdemSelectLote(prioridadeAtual, 1);
            }

            renderizarTabelaModalLote();

            if (window.bootstrap && window.bootstrap.Modal) {
                bulkModalInstance = window.bootstrap.Modal.getInstance(bulkModalEl) || new window.bootstrap.Modal(bulkModalEl);
                bulkModalInstance.show();
            } else if (bulkModalEl) {
                bulkModalEl.style.display = 'block';
                bulkModalEl.classList.add('show');
            }
        });
    }

    function abrirModalLote(modo) {
        if (abaAtual !== 'lista_tarefas_adm') {
            exibirAlerta('error', 'A alteracao em lote esta disponivel apenas na lista ADM.');
            return;
        }

        if (!bulkModalEl || !bulkConfirmarBtn) {
            exibirAlerta('error', 'Modal de lote indisponivel.');
            return;
        }

        configurarModalLoteModo(modo);
        carregarDadosModalLote();
    }

    function preencherSelectPrioridadesItem(prioridadeSelecionada) {
        if (!itemModalPrioridade) {
            return;
        }

        itemModalPrioridade.innerHTML = '';
        prioridadesModal.forEach(function (item) {
            var id = parseInt(item.id || '0', 10);
            if (!id) {
                return;
            }

            var option = document.createElement('option');
            option.value = String(id);
            option.textContent = item.nome || ('Prioridade ' + id);
            if (id === parseInt(prioridadeSelecionada || '0', 10)) {
                option.selected = true;
            }
            itemModalPrioridade.appendChild(option);
        });
    }

    function popularOrdemSelectItem(prioridadeId, ordemPadrao) {
        if (!itemModalOrdem) {
            return;
        }

        var prioId = parseInt(prioridadeId || '0', 10);
        var maxOrder = parseInt((bulkAgrupados && bulkAgrupados[String(prioId)]) || bulkAgrupados[prioId] || '0', 10);
        if (!maxOrder || maxOrder < 1) {
            maxOrder = 1;
        }

        var limite = maxOrder + 1;
        var ordemInicial = parseInt(ordemPadrao || '1', 10);
        if (ordemInicial > limite) {
            limite = ordemInicial;
        }

        itemModalOrdem.innerHTML = '';
        for (var i = 1; i <= limite; i++) {
            var option = document.createElement('option');
            option.value = String(i);
            option.textContent = 'Ordem ' + i;
            if (i === ordemInicial) {
                option.selected = true;
            }
            itemModalOrdem.appendChild(option);
        }

        if (!itemModalOrdem.value && itemModalOrdem.options.length) {
            itemModalOrdem.options[0].selected = true;
        }
    }

    function abrirModalItem(desenhoId) {
        if (abaAtual !== 'lista_tarefas_adm') {
            exibirAlerta('error', 'O modal individual esta disponivel apenas na lista ADM.');
            return;
        }

        if (!itemModalEl || !itemModalIdInput || !itemModalPrioridade || !itemModalOrdem) {
            exibirAlerta('error', 'Modal individual indisponivel.');
            return;
        }

        var id = parseInt(desenhoId || '0', 10);
        if (!id) {
            exibirAlerta('error', 'Desenho invalido para edicao.');
            return;
        }

        buscarDadosLote(function (ok) {
            if (!ok) {
                return;
            }

            var item = null;
            bulkLista.forEach(function (linha) {
                if (!item && parseInt(linha.id || '0', 10) === id) {
                    item = linha;
                }
            });

            if (!item) {
                exibirAlerta('error', 'Desenho nao encontrado para abrir modal.');
                return;
            }

            var prioridadeAtual = parseInt(item.prioridade_id || '0', 10);
            var ordemAtual = parseInt(item.ordem || '1', 10);
            if (!prioridadeAtual) {
                exibirAlerta('error', 'Prioridade atual invalida para o desenho.');
                return;
            }

            itemModalIdInput.value = String(id);
            itemModalOrdemOriginal = ordemAtual > 0 ? ordemAtual : 1;
            preencherSelectPrioridadesItem(prioridadeAtual);
            itemModalPrioridade.dataset.originalPrio = String(prioridadeAtual);
            popularOrdemSelectItem(prioridadeAtual, itemModalOrdemOriginal);

            if (itemModalTitle) {
                itemModalTitle.textContent = 'Modificar prioridade ' + (bulkItemTipo === 'projeto' ? 'projeto' : 'desenho') + ': ' + (item.nome_arquivo || ('ID ' + id));
            }

            if (window.bootstrap && window.bootstrap.Modal) {
                itemModalInstance = window.bootstrap.Modal.getInstance(itemModalEl) || new window.bootstrap.Modal(itemModalEl);
                itemModalInstance.show();
            } else {
                itemModalEl.style.display = 'block';
                itemModalEl.classList.add('show');
            }
        });
    }

    function fecharModalItem() {
        if (!itemModalEl) {
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            itemModalInstance = window.bootstrap.Modal.getInstance(itemModalEl) || itemModalInstance;
            if (itemModalInstance) {
                itemModalInstance.hide();
                return;
            }
        }

        itemModalEl.classList.remove('show');
        itemModalEl.style.display = 'none';
    }

    function abrirModalAutorizacaoConclusao(desenhoId) {
        if (abaAtual !== 'tarefas_concluidas') {
            exibirAlerta('error', 'A autorizacao de conclusao so pode ser feita na aba de tarefas concluidas.');
            return;
        }

        if (!autorizacaoModalEl || !autorizacaoIdInput || !autorizacaoUsuarioInput || !autorizacaoSenhaInput) {
            exibirAlerta('error', 'Modal de autorizacao indisponivel.');
            return;
        }

        var id = parseInt(desenhoId || '0', 10);
        if (!id) {
            exibirAlerta('error', 'Desenho invalido para autorizacao.');
            return;
        }

        autorizacaoIdInput.value = String(id);
        autorizacaoUsuarioInput.value = '';
        autorizacaoSenhaInput.value = '';

        if (autorizacaoMensagem) {
            if (usuarioPodeAutorizarConclusao) {
                autorizacaoMensagem.textContent = 'Voce possui permissao para autorizar. Se preferir, informe outro usuario autorizador.';
            } else {
                autorizacaoMensagem.textContent = 'Para autorizar, informe as credenciais de um usuario com permissao (ex.: administrador).';
            }
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            autorizacaoModalInstance = window.bootstrap.Modal.getInstance(autorizacaoModalEl) || new window.bootstrap.Modal(autorizacaoModalEl);
            autorizacaoModalInstance.show();
        } else {
            autorizacaoModalEl.style.display = 'block';
            autorizacaoModalEl.classList.add('show');
        }
    }

    function fecharModalAutorizacaoConclusao() {
        if (!autorizacaoModalEl) {
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            autorizacaoModalInstance = window.bootstrap.Modal.getInstance(autorizacaoModalEl) || autorizacaoModalInstance;
            if (autorizacaoModalInstance) {
                autorizacaoModalInstance.hide();
                return;
            }
        }

        autorizacaoModalEl.classList.remove('show');
        autorizacaoModalEl.style.display = 'none';
    }

    function executarAutorizacaoConclusao(desenhoId, usuario, senha) {
        containerTarefas.classList.add('is-loading');
        $.ajax({
            url: endpointAcao,
            type: 'POST',
            dataType: 'json',
            data: {
                acao: 'autorizar_conclusao',
                desenho_id: desenhoId,
                autorizador_nome: usuario || '',
                autorizador_senha: senha || ''
            },
            success: function (response) {
                var ok = !!(response && response.ok);
                var mensagem = response && response.mensagem ? response.mensagem : (ok ? 'Conclusao autorizada.' : 'Nao foi possivel autorizar a conclusao.');
                exibirAlerta(ok ? 'success' : 'error', mensagem);
                if (ok) {
                    fecharModalAutorizacaoConclusao();
                    carregarConteudo();
                }
            },
            error: function (xhr) {
                var mensagem = xhr && xhr.responseJSON && xhr.responseJSON.mensagem
                    ? xhr.responseJSON.mensagem
                    : 'Erro ao autorizar conclusao.';
                exibirAlerta('error', mensagem);
            },
            complete: function () {
                containerTarefas.classList.remove('is-loading');
            }
        });
    }

    function fecharModalLote() {
        if (!bulkModalEl) {
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            bulkModalInstance = window.bootstrap.Modal.getInstance(bulkModalEl) || bulkModalInstance;
            if (bulkModalInstance) {
                bulkModalInstance.hide();
                return;
            }
        }

        bulkModalEl.classList.remove('show');
        bulkModalEl.style.display = 'none';
    }

    function executarAcaoLote(ids, prioridadeId, ordemInicial, opcoes) {
        opcoes = opcoes || {};
        var idsLote = Array.isArray(ids)
            ? ids.map(function (id) {
                return parseInt(id || '0', 10);
            }).filter(function (id) {
                return id > 0;
            }).join(',')
            : String(ids || '');

        if (!idsLote) {
            exibirAlerta('error', 'Selecione ao menos um ' + (bulkItemTipo === 'projeto' ? 'projeto' : 'desenho') + ' para aplicacao em lote.');
            return;
        }

        containerTarefas.classList.add('is-loading');
        $.ajax({
            url: endpointAcao,
            type: 'POST',
            dataType: 'json',
            data: {
                acao: 'mudar_lote',
                desenho_ids: idsLote,
                item_tipo: bulkItemTipo,
                processo_id: processoAtualId,
                prioridade_id: prioridadeId,
                ordem_inicial: ordemInicial
            },
            success: function (response) {
                var ok = !!(response && response.ok);
                var mensagem = response && response.mensagem ? response.mensagem : (ok ? 'Lote aplicado com sucesso.' : 'Nao foi possivel aplicar o lote.');
                exibirAlerta(ok ? 'success' : 'error', mensagem);
                if (ok) {
                    if (typeof opcoes.onSuccess === 'function') {
                        opcoes.onSuccess();
                    } else {
                        fecharModalLote();
                        bulkSelectedIds.clear();
                        atualizarContadorSelecionados();
                    }
                    carregarConteudo();
                }
            },
            error: function (xhr) {
                var mensagem = xhr && xhr.responseJSON && xhr.responseJSON.mensagem
                    ? xhr.responseJSON.mensagem
                    : 'Erro ao aplicar alteracao em lote.';
                exibirAlerta('error', mensagem);
            },
            complete: function () {
                containerTarefas.classList.remove('is-loading');
            }
        });
    }

    function executarApagarLote(ids, opcoes) {
        opcoes = opcoes || {};
        var idsLote = Array.isArray(ids)
            ? ids.map(function (id) {
                return parseInt(id || '0', 10);
            }).filter(function (id) {
                return id > 0;
            }).join(',')
            : String(ids || '');

        if (!idsLote) {
            exibirAlerta('error', 'Selecione ao menos um ' + (bulkItemTipo === 'projeto' ? 'projeto' : 'desenho') + ' para apagar em lote.');
            return;
        }

        containerTarefas.classList.add('is-loading');
        $.ajax({
            url: endpointAcao,
            type: 'POST',
            dataType: 'json',
            data: {
                acao: 'apagar_lote',
                desenho_ids: idsLote,
                item_tipo: bulkItemTipo,
                processo_id: processoAtualId
            },
            success: function (response) {
                var ok = !!(response && response.ok);
                var mensagem = response && response.mensagem ? response.mensagem : (ok ? 'Desenhos apagados com sucesso.' : 'Nao foi possivel apagar os desenhos selecionados.');
                exibirAlerta(ok ? 'success' : 'error', mensagem);
                if (ok) {
                    if (typeof opcoes.onSuccess === 'function') {
                        opcoes.onSuccess();
                    } else {
                        fecharModalLote();
                        bulkSelectedIds.clear();
                        atualizarContadorSelecionados();
                    }
                    carregarConteudo();
                }
            },
            error: function (xhr) {
                var mensagem = xhr && xhr.responseJSON && xhr.responseJSON.mensagem
                    ? xhr.responseJSON.mensagem
                    : 'Erro ao apagar desenhos em lote.';
                exibirAlerta('error', mensagem);
            },
            complete: function () {
                containerTarefas.classList.remove('is-loading');
            }
        });
    }

    function destruirDataTablePainel() {
        if (!(window.jQuery && $.fn && $.fn.DataTable)) {
            return;
        }

        if ($.fn.DataTable.isDataTable('#painel-tarefas-table')) {
            $('#painel-tarefas-table').DataTable().destroy();
        }
        painelTabelaDt = null;
    }

    function normalizarCorHexPainel(cor) {
        if (!cor) {
            return null;
        }

        var corTratada = String(cor).trim();
        if (/^#[0-9A-F]{6}$/i.test(corTratada)) {
            return corTratada;
        }

        return null;
    }

    function escapeHtml(valor) {
        return String(valor == null ? '' : valor)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderizarEmpreendimentoComEscala(item) {
        var nome = escapeHtml(item && item.empreendimento_nome || '');
        var escala = escapeHtml(item && item.empreendimento_escala || '');

        if (!escala) {
            return nome;
        }

        return nome + '<div class="text-muted small">Escala ' + escala + '</div>';
    }

    function obterCorTextoParaFundoPainel(corHex) {
        var corValida = normalizarCorHexPainel(corHex);
        if (!corValida) {
            return '#0f172a';
        }

        var r = parseInt(corValida.substring(1, 3), 16);
        var g = parseInt(corValida.substring(3, 5), 16);
        var b = parseInt(corValida.substring(5, 7), 16);
        var luminancia = (0.299 * r) + (0.587 * g) + (0.114 * b);
        return luminancia > 165 ? '#0f172a' : '#f8fafc';
    }

    function aplicarCorPrioridadePainel() {
        var tabela = document.getElementById('painel-tarefas-table');
        if (!tabela) {
            return;
        }

        var celulasPrioridade = tabela.querySelectorAll('tbody td[bgcolor]');
        celulasPrioridade.forEach(function (celula) {
            var cor = celula.getAttribute('bgcolor');
            var corValida = normalizarCorHexPainel(cor);
            if (!corValida) {
                return;
            }

            var corTexto = obterCorTextoParaFundoPainel(corValida);
            celula.style.setProperty('background-color', corValida, 'important');
            celula.style.setProperty('color', corTexto, 'important');

            var textos = celula.querySelectorAll('span, .marca_texto');
            textos.forEach(function (texto) {
                texto.style.setProperty('color', corTexto, 'important');
            });
        });
    }

    function inicializarDataTablePainel() {
        if (!(window.jQuery && $.fn && $.fn.DataTable && $('#painel-tarefas-table').length)) {
            return;
        }

        var colunas = $('#painel-tarefas-table thead th').length;
        var totalAcoes = $('#painel-tarefas-table thead th.wl-col-acoes').length;
        var columnDefs = [];
        if (totalAcoes > 0 && colunas >= totalAcoes) {
            var targets = [];
            for (var i = 0; i < totalAcoes; i++) {
                targets.push(colunas - 1 - i);
            }
            columnDefs.push({
                targets: targets,
                orderable: false,
                searchable: false,
                className: 'text-end text-nowrap wl-col-acoes'
            });
        }

        var dataTable = $('#painel-tarefas-table').DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            order: [],
            ordering: true,
            buttons: ['colvis'],
            columnDefs: columnDefs,
            drawCallback: function () {
                aplicarCorPrioridadePainel();
            },
            language: {
                decimal: '',
                emptyTable: 'Sem dados disponiveis',
                infoEmpty: 'Mostrando de 0 ate 0 de 0 registros',
                infoFiltered: '(filtrado do total de registros)',
                infoPostFix: '',
                thousands: ',',
                lengthMenu: 'MENU',
                loadingRecords: 'Carregando dados...',
                processing: 'Processando...',
                search: 'Buscar:',
                zeroRecords: 'Nao foram encontrados resultados',
                paginate: {
                    first: 'Primeiro',
                    last: 'Ultimo',
                    next: 'Seguinte',
                    previous: 'Anterior'
                },
                aria: {
                    sortAscending: ': clique para ordenar ascendente',
                    sortDescending: ': clique para ordenar descendente'
                }
            }
        });

        if (dataTable && typeof dataTable.buttons === 'function') {
            dataTable.buttons().container().appendTo('#painel-tarefas-table_wrapper .col-md-6:eq(0)');
        }

        aplicarCorPrioridadePainel();
    }

    function painelSpanTruncado(valor) {
        var texto = escapeHtml(valor || '');
        return '<span class="wl-cell-truncate" title="' + texto + '">' + texto + '</span>';
    }

    function tituloAbaAtualPainel() {
        if (abaAtual === 'lista_tarefas_adm') {
            return 'Lista de Tarefas ADM';
        }
        if (abaAtual === 'lista_tarefas') {
            return 'Lista de Tarefas';
        }
        if (abaAtual === 'tarefas_concluidas') {
            return 'Tarefas Concluidas';
        }
        return 'Meus Desenhos';
    }

    function renderizarShellListaPainel(isAdm) {
        var titulo = escapeHtml(tituloAbaAtualPainel());
        var processo = escapeHtml(processoAtualNome || '');
        var controlesAdm = isAdm
            ? '<div class="d-flex flex-wrap justify-content-start align-items-center gap-2 mb-2">' +
                '<button type="button" class="btn btn-sm btn-outline-primary" data-action="abrir_modal_lote" data-lote-modo="mudar_prioridade"><i class="ri-stack-line align-bottom me-1"></i>Mudar prioridade de varios</button>' +
                '<button type="button" class="btn btn-sm btn-outline-danger" data-action="abrir_modal_lote" data-lote-modo="apagar"><i class="ri-delete-bin-line align-bottom me-1"></i>Apagar varios</button>' +
              '</div>'
            : '';
        var colunasAdm = isAdm
            ? '<th class="text-end text-nowrap wl-col-acoes">Acao</th><th class="text-end text-nowrap wl-col-acoes">Prioridade</th><th class="text-end text-nowrap wl-col-acoes">Ordem</th>'
            : '';

        containerTarefas.innerHTML =
            '<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">' +
                '<h5 class="mb-0">' + titulo + (processo ? ' - ' + processo : '') + '</h5>' +
                '<span class="badge bg-primary-subtle text-primary fw-semibold" id="painel-lista-json-count">Carregando...</span>' +
            '</div>' +
            controlesAdm +
            '<div class="table-responsive">' +
                '<table id="painel-tarefas-table" class="table table-bordered table-striped table-hover table-nowrap align-middle mb-0 wl-tarefas-table">' +
                    '<thead><tr>' +
                        '<th>Prioridade</th><th>Ordem</th><th>Desenhista</th><th>Nome do arquivo</th>' +
                        '<th>Empresa/Cliente</th><th>Empreendimento</th><th>Finalidade</th><th>Subpastas</th>' +
                        '<th class="text-center text-nowrap wl-col-dimensao-dxf">Dimensao DXF</th><th>Status</th><th>Data de Envio</th><th>Data de Conclusao</th>' +
                        colunasAdm +
                    '</tr></thead>' +
                    '<tbody></tbody>' +
                    '<tfoot><tr>' +
                        '<th>Prioridade</th><th>Ordem</th><th>Desenhista</th><th>Nome do arquivo</th>' +
                        '<th>Empresa/Cliente</th><th>Empreendimento</th><th>Finalidade</th><th>Subpastas</th>' +
                        '<th class="text-center text-nowrap wl-col-dimensao-dxf">Dimensao DXF</th><th>Status</th><th>Data de Envio</th><th>Data de Conclusao</th>' +
                        colunasAdm +
                    '</tr></tfoot>' +
                '</table>' +
            '</div>';
    }

    function aplicarMetaListaPainel(json) {
        painelListaMeta = json || {};
        var count = document.getElementById('painel-lista-json-count');
        if (count) {
            var total = parseInt(painelListaMeta.recordsFiltered || painelListaMeta.recordsTotal || '0', 10);
            count.textContent = total + ' item(ns) | Ativas: ' + total + ' | Concluidas: 0';
        }

        var rotuloNome = painelListaMeta.rotulo_nome || 'Nome do arquivo';
        document.querySelectorAll('#painel-tarefas-table thead th:nth-child(4), #painel-tarefas-table tfoot th:nth-child(4)').forEach(function(th) {
            th.textContent = rotuloNome;
        });

        var tabela = document.getElementById('painel-tarefas-table');
        if (tabela) {
            var tipoProcesso = String(painelListaMeta.tipo_processo || '').toLowerCase();
            var ehProcessoInd = painelListaMeta.agrupado_por_projeto === true ||
                String(painelListaMeta.agrupado_por_projeto || '').toLowerCase() === 'true' ||
                tipoProcesso === 'ind' ||
                String(rotuloNome).toLowerCase() === 'descricao';
            tabela.classList.toggle('wl-processo-ind', ehProcessoInd);
        }

        var mostrarDimensao = painelListaMeta.mostrar_dimensao_dxf === true || String(painelListaMeta.mostrar_dimensao_dxf || '').toLowerCase() === 'true';
        setTimeout(function() {
            if (painelTabelaDt && painelTabelaDt.column) {
                painelTabelaDt.column(8).visible(mostrarDimensao, false);
                painelTabelaDt.columns.adjust();
                aplicarCorPrioridadePainel();
            }
        }, 0);
    }

    function renderizarPrioridadePainel(data, type, row) {
        var corTexto = obterCorTextoParaFundoPainel(normalizarCorHexPainel(row.prioridade_cor) || '#cbd5e1');
        return '<span class="marca_texto" style="color: ' + corTexto + ' !important;">' + escapeHtml(data || '-') + '</span>';
    }

    function aplicarCelulaPrioridadePainel(td, row) {
        var cor = normalizarCorHexPainel(row.prioridade_cor) || '#cbd5e1';
        var corTexto = obterCorTextoParaFundoPainel(cor);
        td.setAttribute('bgcolor', cor);
        td.style.setProperty('background-color', cor, 'important');
        td.style.setProperty('color', corTexto, 'important');
    }

    function renderizarAcaoAdmPainel(row) {
        var id = parseInt(row.id || '0', 10);
        var itemTipo = row.item_tipo === 'projeto' ? 'projeto' : 'desenho';
        var projetoId = parseInt(row.projeto_id || '0', 10);
        var status = String(row.status_normalizado || '').toLowerCase();
        var attrs = ' data-desenho-id="' + id + '" data-item-tipo="' + itemTipo + '" data-projeto-id="' + projetoId + '"';

        if (status === 'pendente') {
            return '<div class="wl-row-actions wl-row-actions--confirm"><button type="button" class="btn btn-sm btn-outline-danger" data-action="apagar"' + attrs + '>Apagar</button></div>';
        }
        if (status === 'cortando') {
            return '<div class="wl-row-actions wl-row-actions--confirm"><button type="button" class="btn btn-sm btn-outline-warning" data-action="cancelar_corte"' + attrs + '>Cancelar corte</button></div>';
        }
        return '<div class="wl-row-actions wl-row-actions--confirm"><button type="button" class="btn btn-sm btn-outline-secondary" disabled>Sem acao</button></div>';
    }

    function renderizarPrioridadeAdmPainel(row) {
        var id = parseInt(row.id || '0', 10);
        var itemTipo = row.item_tipo === 'projeto' ? 'projeto' : 'desenho';
        var projetoId = parseInt(row.projeto_id || '0', 10);
        var attrs = ' data-desenho-id="' + id + '" data-item-tipo="' + itemTipo + '" data-projeto-id="' + projetoId + '"';
        var itens = prioridadesModal.map(function(prioridade) {
            var prioridadeId = parseInt(prioridade.id || '0', 10);
            if (!prioridadeId) {
                return '';
            }

            return '<li><button type="button" class="dropdown-item" data-action="mudar_prioridade"' + attrs + ' data-prioridade-id="' + prioridadeId + '">' +
                '<span class="badge rounded-pill me-1" style="background: ' + escapeHtml(prioridade.cor || '#cbd5e1') + ';">&nbsp;</span>' +
                escapeHtml(prioridade.nome || '') +
            '</button></li>';
        }).join('');

        return '<div class="wl-row-actions wl-row-actions--with-menu">' +
            '<button type="button" class="btn btn-sm btn-outline-primary" data-action="abrir_modal_item"' + attrs + '>Modal</button>' +
            '<div class="dropdown">' +
                '<button type="button" class="btn btn-sm btn-outline-secondary wl-row-action-main dropdown-toggle" data-bs-toggle="dropdown" data-toggle="dropdown" aria-expanded="false">Mudar prioridade</button>' +
                '<ul class="dropdown-menu dropdown-menu-end">' + itens + '</ul>' +
            '</div>' +
        '</div>';
    }

    function renderizarOrdemAdmPainel(row) {
        var id = parseInt(row.id || '0', 10);
        var itemTipo = row.item_tipo === 'projeto' ? 'projeto' : 'desenho';
        var projetoId = parseInt(row.projeto_id || '0', 10);
        var attrs = ' data-desenho-id="' + id + '" data-item-tipo="' + itemTipo + '" data-projeto-id="' + projetoId + '"';

        return '<div class="wl-row-actions wl-row-actions--with-menu">' +
            '<button type="button" class="btn btn-sm btn-outline-secondary" data-action="mover_ordem" data-direcao="up"' + attrs + ' title="Subir na ordem"><i class="ri-arrow-up-s-line"></i></button>' +
            '<button type="button" class="btn btn-sm btn-outline-secondary" data-action="mover_ordem" data-direcao="down"' + attrs + ' title="Descer na ordem"><i class="ri-arrow-down-s-line"></i></button>' +
        '</div>';
    }

    function carregarConteudoJsonListaPainel() {
        var isAdm = abaAtual === 'lista_tarefas_adm';
        destruirDataTablePainel();
        renderizarShellListaPainel(isAdm);
        aplicarModoVisualizacaoPainel();

        var columns = [
            { data: 'prioridade_nome', className: 'text-center', createdCell: function(td, cellData, row) { aplicarCelulaPrioridadePainel(td, row); }, render: renderizarPrioridadePainel },
            { data: 'ordem', className: 'text-center', render: function(data) { return escapeHtml(data || ''); } },
            { data: 'desenhista_nome', render: function(data) { return painelSpanTruncado(data); } },
            { data: 'nome_arquivo', render: function(data) { return painelSpanTruncado(data); } },
            { data: 'empresa_nome', render: function(data) { return painelSpanTruncado(data); } },
            { data: null, render: function(data, type, row) { return renderizarEmpreendimentoComEscala(row); } },
            { data: 'finalidade_nome', render: function(data) { return painelSpanTruncado(data); } },
            { data: 'subpastas', render: function(data) { return painelSpanTruncado(data); } },
            { data: 'dimensao_dxf', className: 'text-center text-nowrap wl-col-dimensao-dxf', render: function(data) { return painelSpanTruncado(data || '-'); } },
            { data: 'status', render: function(data) { return painelSpanTruncado(data || '-'); } },
            { data: 'data_envio', render: function(data) { return escapeHtml(data || ''); } },
            { data: 'data_conclusao', render: function(data) { return escapeHtml(data || ''); } }
        ];

        if (isAdm) {
            columns.push({ data: null, orderable: false, searchable: false, className: 'text-end text-nowrap wl-col-acoes', render: function(data, type, row) { return renderizarAcaoAdmPainel(row); } });
            columns.push({ data: null, orderable: false, searchable: false, className: 'text-end text-nowrap wl-col-acoes', render: function(data, type, row) { return renderizarPrioridadeAdmPainel(row); } });
            columns.push({ data: null, orderable: false, searchable: false, className: 'text-end text-nowrap wl-col-acoes', render: function(data, type, row) { return renderizarOrdemAdmPainel(row); } });
        }

        $('#painel-tarefas-table')
            .off('xhr.dt.wlPainelJson')
            .on('xhr.dt.wlPainelJson', function(e, settings, json) {
                aplicarMetaListaPainel(json || {});
            });

        painelTabelaDt = $('#painel-tarefas-table').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ordering: false,
            responsive: true,
            deferRender: true,
            lengthChange: true,
            pageLength: 50,
            lengthMenu: [25, 50, 100],
            autoWidth: false,
            buttons: ['colvis'],
            ajax: {
                url: endpointLista,
                type: 'POST',
                dataType: 'json',
                data: function(d) {
                    d.formato = 'json';
                    d.processo_id = processoAtualId;
                    d.aba = abaAtual;
                    d.mostrar_concluidas = '0';
                },
                dataSrc: function(response) {
                    return Array.isArray(response && response.data) ? response.data : [];
                },
                error: function(xhr) {
                    var mensagem = xhr && xhr.responseJSON && xhr.responseJSON.mensagem
                        ? xhr.responseJSON.mensagem
                        : 'Nao foi possivel carregar o conteudo.';
                    destruirDataTablePainel();
                    containerTarefas.innerHTML = '<div class="alert alert-danger mb-0" role="alert">' + escapeHtml(mensagem) + '</div>';
                }
            },
            columns: columns,
            drawCallback: function() {
                aplicarCorPrioridadePainel();
            },
            language: {
                decimal: '',
                emptyTable: 'Sem dados disponiveis',
                infoEmpty: 'Mostrando de 0 ate 0 de 0 registros',
                infoFiltered: '(filtrado do total de registros)',
                infoPostFix: '',
                thousands: ',',
                lengthMenu: '_MENU_',
                loadingRecords: 'Carregando dados...',
                processing: 'Processando...',
                search: 'Buscar:',
                zeroRecords: 'Nao foram encontrados resultados',
                paginate: {
                    first: 'Primeiro',
                    last: 'Ultimo',
                    next: 'Seguinte',
                    previous: 'Anterior'
                },
                aria: {
                    sortAscending: ': clique para ordenar ascendente',
                    sortDescending: ': clique para ordenar descendente'
                }
            }
        });

        if (painelTabelaDt.buttons) {
            painelTabelaDt.buttons().container().appendTo('#painel-tarefas-table_wrapper .col-md-6:eq(0)');
        }
    }

    function exibirAlerta(tipo, mensagem) {
        if (!containerTarefas) {
            return;
        }

        var classe = tipo === 'success' ? 'alert-success' : 'alert-danger';
        var alerta = document.createElement('div');
        alerta.className = 'alert ' + classe + ' py-2 px-3 mb-3';
        alerta.textContent = mensagem;
        containerTarefas.insertAdjacentElement('afterbegin', alerta);

        setTimeout(function () {
            if (alerta && alerta.parentNode) {
                alerta.parentNode.removeChild(alerta);
            }
        }, 2800);
    }

    function exibirPopupErroRecolocar(mensagem) {
        var texto = String(mensagem || 'Nao foi possivel recolocar o desenho.');
        if (window.Swal && typeof window.Swal.fire === 'function') {
            window.Swal.fire({
                icon: 'error',
                title: 'Erro ao recolocar',
                text: texto,
                confirmButtonText: 'OK'
            });
            return;
        }

        window.alert('Erro ao recolocar:\n' + texto);
    }

    function abrirModalConfirmacao(mensagem, onConfirm) {
        var texto = String(mensagem || 'Deseja continuar?');
        if (!confirmacaoModalEl || !confirmacaoTextoEl || !confirmacaoConfirmarBtn) {
            if (window.confirm(texto) && typeof onConfirm === 'function') {
                onConfirm();
            }
            return;
        }

        confirmacaoOnConfirm = typeof onConfirm === 'function' ? onConfirm : null;
        confirmacaoTextoEl.textContent = texto;

        if (window.bootstrap && window.bootstrap.Modal) {
            confirmacaoModalInstance = window.bootstrap.Modal.getInstance(confirmacaoModalEl) || new window.bootstrap.Modal(confirmacaoModalEl);
            confirmacaoModalInstance.show();
        } else {
            confirmacaoModalEl.style.display = 'block';
            confirmacaoModalEl.classList.add('show');
        }
    }

    function fecharModalConfirmacao() {
        if (!confirmacaoModalEl) {
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            confirmacaoModalInstance = window.bootstrap.Modal.getInstance(confirmacaoModalEl) || confirmacaoModalInstance;
            if (confirmacaoModalInstance) {
                confirmacaoModalInstance.hide();
                return;
            }
        }

        confirmacaoModalEl.classList.remove('show');
        confirmacaoModalEl.style.display = 'none';
    }

    function atualizarResumoSolicitacoesRecolocar(total) {
        if (recolocarCount) {
            recolocarCount.textContent = String(total) + ' pendente(s)';
        }
        if (recolocarInfo) {
            recolocarInfo.textContent = 'Lista global de solicitacoes pendentes (todos os processos permitidos).';
        }
    }

    function renderizarSolicitacoesRecolocar(itens, podeGerenciar) {
        if (!recolocarTableBody) {
            return;
        }

        var lista = Array.isArray(itens) ? itens : [];
        if (!lista.length) {
            recolocarTableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Nenhuma solicitacao pendente no momento.</td></tr>';
            atualizarResumoSolicitacoesRecolocar(0);
            return;
        }

        var linhas = lista.map(function (item) {
            var solicitacaoId = parseInt(item.solicitacao_id || '0', 10);
            var prioridadeNome = escapeHtml(item.prioridade_nome || '-');
            var prioridadeCor = normalizarCorHexPainel(item.prioridade_cor || '#cbd5e1') || '#cbd5e1';
            var prioridadeTexto = obterCorTextoParaFundoPainel(prioridadeCor);
            var qtd = parseInt(item.quantidade || '0', 10);
            if (!qtd || qtd < 0) {
                qtd = 0;
            }

            var acoesHtml = '<span class="badge bg-secondary-subtle text-secondary">Sem permissao</span>';
            if (podeGerenciar && solicitacaoId > 0) {
                acoesHtml = ''
                    + '<div class="d-flex justify-content-end gap-1">'
                    + '<button type="button" class="btn btn-sm btn-outline-primary" data-recolocar-action="aprovado" data-solicitacao-id="' + solicitacaoId + '">Aprovar</button>'
                    + '<button type="button" class="btn btn-sm btn-outline-danger" data-recolocar-action="negado" data-solicitacao-id="' + solicitacaoId + '">Negar</button>'
                    + '</div>';
            }

            return ''
                + '<tr>'
                + '<td><span class="wl-cell-truncate" title="' + escapeHtml(item.processo_nome || '') + '">' + escapeHtml(item.processo_nome || '') + '</span></td>'
                + '<td class="text-center" style="background-color:' + prioridadeCor + ';color:' + prioridadeTexto + ';font-weight:700;">' + prioridadeNome + '</td>'
                + '<td><span class="wl-cell-truncate" title="' + escapeHtml(item.desenhista_nome || '') + '">' + escapeHtml(item.desenhista_nome || '') + '</span></td>'
                + '<td><span class="wl-cell-truncate" title="' + escapeHtml(item.nome_arquivo || '') + '">' + escapeHtml(item.nome_arquivo || '') + '</span></td>'
                + '<td><span class="wl-cell-truncate" title="' + escapeHtml(item.solicitante_nome || '') + '">' + escapeHtml(item.solicitante_nome || '') + '</span></td>'
                + '<td class="text-nowrap">' + escapeHtml(item.data_solicitacao || '') + '</td>'
                + '<td class="text-center">' + String(qtd) + '</td>'
                + '<td class="text-end text-nowrap">' + acoesHtml + '</td>'
                + '</tr>';
        });

        recolocarTableBody.innerHTML = linhas.join('');
        atualizarResumoSolicitacoesRecolocar(lista.length);
    }

    function carregarSolicitacoesRecolocarPendentes() {
        if (!recolocarTableBody) {
            return;
        }

        recolocarTableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Carregando solicitacoes...</td></tr>';
        atualizarResumoSolicitacoesRecolocar(0);

        $.ajax({
            url: endpointAcao,
            type: 'POST',
            dataType: 'json',
            data: {
                acao: 'listar_solicitacoes_recolocar'
            },
            success: function (response) {
                var ok = !!(response && response.ok);
                if (!ok) {
                    var msgErro = response && response.mensagem ? response.mensagem : 'Falha ao carregar solicitacoes.';
                    recolocarTableBody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">' + escapeHtml(msgErro) + '</td></tr>';
                    atualizarResumoSolicitacoesRecolocar(0);
                    return;
                }

                var itens = Array.isArray(response.itens) ? response.itens : [];
                var podeGerenciar = !!(response && response.podeGerenciar && usuarioPodeGerenciarRecolocar);
                renderizarSolicitacoesRecolocar(itens, podeGerenciar);
            },
            error: function (xhr) {
                var mensagem = xhr && xhr.responseJSON && xhr.responseJSON.mensagem
                    ? xhr.responseJSON.mensagem
                    : 'Erro ao carregar solicitacoes pendentes.';
                recolocarTableBody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">' + escapeHtml(mensagem) + '</td></tr>';
                atualizarResumoSolicitacoesRecolocar(0);
            }
        });
    }

    function abrirModalSolicitacoesRecolocar() {
        if (!recolocarModalEl) {
            exibirAlerta('error', 'Modal de solicitacoes de recolocacao indisponivel.');
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            recolocarModalInstance = window.bootstrap.Modal.getInstance(recolocarModalEl) || new window.bootstrap.Modal(recolocarModalEl);
            recolocarModalInstance.show();
        } else {
            recolocarModalEl.style.display = 'block';
            recolocarModalEl.classList.add('show');
        }

        carregarSolicitacoesRecolocarPendentes();
    }

    function decidirSolicitacaoRecolocar(solicitacaoId, decisao) {
        var id = parseInt(solicitacaoId || '0', 10);
        if (!id) {
            exibirAlerta('error', 'Solicitacao invalida.');
            return;
        }

        var decisaoFinal = String(decisao || '').toLowerCase();
        if (decisaoFinal !== 'aprovado' && decisaoFinal !== 'negado') {
            exibirAlerta('error', 'Decisao invalida para solicitacao.');
            return;
        }

        var confirmacao = decisaoFinal === 'aprovado'
            ? 'Confirmar aprovacao da solicitacao e recolocar desenho na fila?'
            : 'Confirmar negacao da solicitacao de recolocar?';

        abrirModalConfirmacao(confirmacao, function () {
            $.ajax({
                url: endpointAcao,
                type: 'POST',
                dataType: 'json',
                data: {
                    acao: 'decidir_solicitacao_recolocar',
                    solicitacao_id: id,
                    decisao: decisaoFinal
                },
                success: function (response) {
                    var ok = !!(response && response.ok);
                    var mensagem = response && response.mensagem
                        ? response.mensagem
                        : (ok ? 'Solicitacao processada com sucesso.' : 'Falha ao processar solicitacao.');
                    exibirAlerta(ok ? 'success' : 'error', mensagem);
                    if (!ok) {
                        exibirPopupErroRecolocar(mensagem);
                    }
                    if (ok) {
                        carregarSolicitacoesRecolocarPendentes();
                        if (processoAtualId) {
                            carregarConteudo();
                        }
                    }
                },
                error: function (xhr) {
                    var mensagem = xhr && xhr.responseJSON && xhr.responseJSON.mensagem
                        ? xhr.responseJSON.mensagem
                        : 'Erro ao processar solicitacao.';
                    exibirAlerta('error', mensagem);
                    exibirPopupErroRecolocar(mensagem);
                }
            });
        });
    }

    function marcarAbaAtiva() {
        tabs.forEach(function (tab) {
            if (tab.dataset.aba === abaAtual) {
                tab.classList.add('is-active');
            } else {
                tab.classList.remove('is-active');
            }
        });
    }

    function sincronizarToggleConcluidasDaAba() {
        var ehFinalizados = abaAtual === 'tarefas_concluidas';
        var ehMeusDesenhos = abaAtual === 'meus_desenhos';
        if (toggleConcluidasWrap) {
            toggleConcluidasWrap.style.display = 'none';
        }

        var valorAba = !!mostrarConcluidasPorAba[abaAtual];
        mostrarConcluidas = valorAba;
        if (mostrarConcluidasInput) {
            mostrarConcluidasInput.checked = valorAba;
        }

        if (finalizadosInicioInput) {
            finalizadosInicioInput.value = String(finalizadosPeriodoInicioPorAba[abaAtual] || '');
        }
        if (finalizadosFimInput) {
            finalizadosFimInput.value = String(finalizadosPeriodoFimPorAba[abaAtual] || '');
        }

        if (finalizadosFiltroWrap) {
            finalizadosFiltroWrap.style.display = ehFinalizados ? '' : 'none';
        }

        if (meusInicioInput) {
            meusInicioInput.value = String(meusPeriodoInicioPorAba[abaAtual] || '');
        }
        if (meusFimInput) {
            meusFimInput.value = String(meusPeriodoFimPorAba[abaAtual] || '');
        }
        if (meusFiltroWrap) {
            meusFiltroWrap.style.display = ehMeusDesenhos ? '' : 'none';
        }
    }

    function atualizarCardsProcessoSelecionado() {
        var radios = document.querySelectorAll('#painel-process-grid input[name="painel_processo"]');
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

    function processoSelecionado() {
        var radio = document.querySelector('#painel-process-grid input[name="painel_processo"]:checked');
        if (!radio) {
            return null;
        }

        var processoId = parseInt(radio.value, 10);
        if (!processoId) {
            return null;
        }

        var processo = null;
        processos.forEach(function (item) {
            if (parseInt(item.id, 10) === processoId) {
                processo = item;
            }
        });

        return processo;
    }

    function carregarConteudo() {
        if (!containerTarefas || !processoAtualId || !abaAtual) {
            return;
        }

        var ehFinalizados = abaAtual === 'tarefas_concluidas';
        var ehMeusDesenhos = abaAtual === 'meus_desenhos';
        var mostrarConcluidasEfetivo = ehFinalizados;
        var periodoAplicado = ehFinalizados && !!finalizadosPeriodoAplicadoPorAba[abaAtual];
        var periodoInicio = ehFinalizados ? String(finalizadosPeriodoInicioPorAba[abaAtual] || '') : '';
        var periodoFim = ehFinalizados ? String(finalizadosPeriodoFimPorAba[abaAtual] || '') : '';
        var meusPeriodoAplicado = ehMeusDesenhos && !!meusPeriodoAplicadoPorAba[abaAtual];
        var meusPeriodoInicio = ehMeusDesenhos ? String(meusPeriodoInicioPorAba[abaAtual] || '') : '';
        var meusPeriodoFim = ehMeusDesenhos ? String(meusPeriodoFimPorAba[abaAtual] || '') : '';

        containerTarefas.classList.add('is-loading');

        $.ajax({
            url: endpointLista,
            type: 'POST',
            dataType: 'html',
            data: {
                processo_id: processoAtualId,
                aba: abaAtual,
                mostrar_concluidas: mostrarConcluidasEfetivo ? '1' : '0',
                finalizados_periodo_aplicado: periodoAplicado ? '1' : '0',
                finalizados_data_inicio: periodoInicio,
                finalizados_data_fim: periodoFim,
                meus_periodo_aplicado: meusPeriodoAplicado ? '1' : '0',
                meus_data_inicio: meusPeriodoInicio,
                meus_data_fim: meusPeriodoFim
            },
            success: function (html) {
                destruirDataTablePainel();
                containerTarefas.innerHTML = html;
                aplicarModoVisualizacaoPainel();
                inicializarDataTablePainel();
            },
            error: function (xhr) {
                destruirDataTablePainel();
                var mensagem = xhr && xhr.responseText ? xhr.responseText : 'Nao foi possivel carregar o conteudo.';
                containerTarefas.innerHTML = '<div class="alert alert-danger mb-0" role="alert">' + mensagem + '</div>';
            },
            complete: function () {
                containerTarefas.classList.remove('is-loading');
            }
        });
    }

    function executarAcaoAdm(acao, desenhoId, prioridadeId, direcao, itemTipo, projetoId) {
        if (!acao || !desenhoId) {
            return;
        }

        itemTipo = itemTipo === 'projeto' ? 'projeto' : 'desenho';

        containerTarefas.classList.add('is-loading');
        $.ajax({
            url: endpointAcao,
            type: 'POST',
            dataType: 'json',
            data: {
                acao: acao,
                desenho_id: desenhoId,
                item_tipo: itemTipo,
                projeto_id: projetoId || '',
                processo_id: processoAtualId,
                prioridade_id: prioridadeId || '',
                direcao: direcao || ''
            },
            success: function (response) {
                var ok = !!(response && response.ok);
                var mensagem = response && response.mensagem ? response.mensagem : (ok ? 'Acao realizada.' : 'Nao foi possivel executar a acao.');
                exibirAlerta(ok ? 'success' : 'error', mensagem);
                if (ok) {
                    carregarConteudo();
                }
            },
            error: function (xhr) {
                var mensagem = xhr && xhr.responseJSON && xhr.responseJSON.mensagem
                    ? xhr.responseJSON.mensagem
                    : 'Erro ao executar acao.';
                exibirAlerta('error', mensagem);
            },
            complete: function () {
                containerTarefas.classList.remove('is-loading');
            }
        });
    }

    function abrirPainelComProcesso() {
        var processo = processoSelecionado();
        if (!processo) {
            alert('Selecione um processo para continuar.');
            return;
        }

        processoAtualId = parseInt(processo.id, 10);
        processoAtualNome = processo.nome || '';

        if (pickerWrap) {
            pickerWrap.style.display = 'none';
        }
        if (mainWrap) {
            mainWrap.style.display = '';
        }

        if (cardTitle) {
            cardTitle.textContent = 'Painel de Tarefas - ' + processoAtualNome;
        }
        if (cardSubtitle) {
            cardSubtitle.textContent = 'Processo selecionado: ' + processoAtualNome;
        }

        marcarAbaAtiva();
        sincronizarToggleConcluidasDaAba();
        carregarConteudo();
    }

    function voltarParaSelecao() {
        if (mainWrap) {
            mainWrap.style.display = 'none';
        }
        if (pickerWrap) {
            pickerWrap.style.display = '';
        }

        if (cardTitle) {
            cardTitle.textContent = 'Escolha o processo';
        }
        if (cardSubtitle) {
            cardSubtitle.textContent = 'Primeiro, selecione o processo para carregar a lista de tarefas.';
        }
    }

    if (btnContinuar) {
        btnContinuar.addEventListener('click', abrirPainelComProcesso);
    }

    if (btnTrocar) {
        btnTrocar.addEventListener('click', voltarParaSelecao);
    }

    if (mostrarConcluidasInput) {
        mostrarConcluidasInput.addEventListener('change', function () {
            mostrarConcluidas = !!mostrarConcluidasInput.checked;
            mostrarConcluidasPorAba[abaAtual] = mostrarConcluidas;
            if (!mostrarConcluidas) {
                finalizadosPeriodoAplicadoPorAba[abaAtual] = false;
                if (finalizadosInicioInput) {
                    finalizadosInicioInput.value = '';
                }
                if (finalizadosFimInput) {
                    finalizadosFimInput.value = '';
                }
                finalizadosPeriodoInicioPorAba[abaAtual] = '';
                finalizadosPeriodoFimPorAba[abaAtual] = '';
            }
            sincronizarToggleConcluidasDaAba();
            if (processoAtualId) {
                carregarConteudo();
            }
        });
    }

    if (visualizacaoDetalhadaInput) {
        visualizacaoDetalhadaInput.checked = lerModoVisualizacaoDetalhada();
        aplicarModoVisualizacaoPainel();
        visualizacaoDetalhadaInput.addEventListener('change', function () {
            salvarModoVisualizacaoDetalhada(!!visualizacaoDetalhadaInput.checked);
            aplicarModoVisualizacaoPainel();
        });
    }

    document.addEventListener('show.bs.dropdown', function (event) {
        if (event.target && event.target.closest && event.target.closest('#painel-tarefas-container')) {
            alternarClasseLinhaDropdownPainel(event, true);
        }
    });

    document.addEventListener('hidden.bs.dropdown', function (event) {
        if (event.target && event.target.closest && event.target.closest('#painel-tarefas-container')) {
            alternarClasseLinhaDropdownPainel(event, false);
        }
    });

    if (finalizadosBuscarBtn) {
        finalizadosBuscarBtn.addEventListener('click', function () {
            if (abaAtual !== 'tarefas_concluidas') {
                return;
            }

            var dataInicio = finalizadosInicioInput ? String(finalizadosInicioInput.value || '') : '';
            var dataFim = finalizadosFimInput ? String(finalizadosFimInput.value || '') : '';

            if (!dataInicio || !dataFim) {
                exibirAlerta('error', 'Selecione as datas inicial e final para buscar finalizados.');
                return;
            }

            if (dataInicio > dataFim) {
                exibirAlerta('error', 'Data inicial nao pode ser maior que a data final.');
                return;
            }

            finalizadosPeriodoInicioPorAba[abaAtual] = dataInicio;
            finalizadosPeriodoFimPorAba[abaAtual] = dataFim;
            finalizadosPeriodoAplicadoPorAba[abaAtual] = true;

            if (processoAtualId) {
                carregarConteudo();
            }
        });
    }

    if (recolocarPendenciasBtn) {
        recolocarPendenciasBtn.addEventListener('click', function () {
            if (abaAtual !== 'tarefas_concluidas') {
                exibirAlerta('error', 'As solicitacoes de recolocar sao exibidas na aba de tarefas concluidas.');
                return;
            }

            abrirModalSolicitacoesRecolocar();
        });
    }

    if (meusBuscarBtn) {
        meusBuscarBtn.addEventListener('click', function () {
            if (abaAtual !== 'meus_desenhos') {
                return;
            }

            var dataInicio = meusInicioInput ? String(meusInicioInput.value || '') : '';
            var dataFim = meusFimInput ? String(meusFimInput.value || '') : '';

            if (!dataInicio || !dataFim) {
                exibirAlerta('error', 'Selecione as datas inicial e final para buscar meus desenhos.');
                return;
            }

            if (dataInicio > dataFim) {
                exibirAlerta('error', 'Data inicial nao pode ser maior que a data final.');
                return;
            }

            meusPeriodoInicioPorAba[abaAtual] = dataInicio;
            meusPeriodoFimPorAba[abaAtual] = dataFim;
            meusPeriodoAplicadoPorAba[abaAtual] = true;

            if (processoAtualId) {
                carregarConteudo();
            }
        });
    }

    document.querySelectorAll('#painel-process-grid input[name="painel_processo"]').forEach(function (radio) {
        radio.addEventListener('change', atualizarCardsProcessoSelecionado);
    });

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            if (tab.dataset.enabled !== '1') {
                return;
            }
            abaAtual = tab.dataset.aba || abaAtual;
            marcarAbaAtiva();
            sincronizarToggleConcluidasDaAba();
            carregarConteudo();
        });
    });

    if (containerTarefas) {
        containerTarefas.addEventListener('click', function (event) {
            var alvo = event.target.closest('[data-action]');
            if (!alvo) {
                return;
            }

            var acao = alvo.dataset.action || '';
            var desenhoId = parseInt(alvo.dataset.desenhoId || '0', 10);
            var itemTipo = alvo.dataset.itemTipo === 'projeto' ? 'projeto' : 'desenho';
            var projetoId = parseInt(alvo.dataset.projetoId || '0', 10);
            if (acao === 'abrir_modal_lote') {
                abrirModalLote(alvo.dataset.loteModo || 'mudar_prioridade');
                return;
            }

            if (acao === 'abrir_modal_item') {
                abrirModalItem(alvo.dataset.desenhoId || '0');
                return;
            }

            if (!desenhoId) {
                return;
            }

            if (acao === 'visualizar_concluida') {
                if (typeof window.ver_dxf === 'function') {
                    window.ver_dxf(String(desenhoId));
                } else {
                    exibirAlerta('error', 'Visualizador indisponivel nesta tela.');
                }
                return;
            }

            if (acao === 'abrir_modal_autorizar_conclusao') {
                abrirModalAutorizacaoConclusao(desenhoId);
                return;
            }

            if (acao === 'solicitar_recolocar') {
                abrirModalConfirmacao('Deseja solicitar para recolocar este desenho?', function () {
                    executarAcaoAdm('solicitar_recolocar', desenhoId, 0, '');
                });
                return;
            }

            var prioridadeId = parseInt(alvo.dataset.prioridadeId || '0', 10);
            var direcao = alvo.dataset.direcao || '';

            if (acao === 'apagar') {
                abrirModalConfirmacao(itemTipo === 'projeto' ? 'Apagar projeto selecionado?' : 'Apagar desenho selecionado?', function () {
                    executarAcaoAdm(acao, desenhoId, prioridadeId, direcao, itemTipo, projetoId);
                });
                return;
            }

            if (acao === 'cancelar_corte') {
                abrirModalConfirmacao(itemTipo === 'projeto' ? 'Cancelar corte deste projeto?' : 'Cancelar corte deste desenho?', function () {
                    executarAcaoAdm(acao, desenhoId, prioridadeId, direcao, itemTipo, projetoId);
                });
                return;
            }

            executarAcaoAdm(acao, desenhoId, prioridadeId, direcao, itemTipo, projetoId);
        });
    }

    if (bulkPrioridade) {
        bulkPrioridade.addEventListener('change', function () {
            var prioridadeId = parseInt(bulkPrioridade.value || '0', 10);
            popularOrdemSelectLote(prioridadeId, 1);
        });
    }

    if (itemModalPrioridade) {
        itemModalPrioridade.addEventListener('change', function () {
            var prioridadeId = parseInt(itemModalPrioridade.value || '0', 10);
            var original = parseInt(itemModalPrioridade.dataset.originalPrio || '0', 10);
            var ordemPadrao = prioridadeId === original ? itemModalOrdemOriginal : 1;
            popularOrdemSelectItem(prioridadeId, ordemPadrao);
        });
    }

    if (bulkModalEl) {
        bulkModalEl.addEventListener('change', function (event) {
            var alvo = event.target;
            if (!alvo || !alvo.classList.contains('js-bulk-modal-row')) {
                return;
            }

            var id = parseInt(alvo.value || '0', 10);
            if (!id) {
                return;
            }

            if (alvo.checked) {
                bulkSelectedIds.add(id);
            } else {
                bulkSelectedIds.delete(id);
            }
            atualizarContadorSelecionados();
        });

        bulkModalEl.addEventListener('hidden.bs.modal', function () {
            destruirDataTableModalLote();
            if (bulkTableBody) {
                bulkTableBody.innerHTML = '';
            }
            bulkSelectedIds.clear();
            bulkItemTipo = 'desenho';
            atualizarContadorSelecionados();
            configurarModalLoteModo('mudar_prioridade');
        });
    }

    if (itemModalEl) {
        itemModalEl.addEventListener('hidden.bs.modal', function () {
            if (itemModalIdInput) {
                itemModalIdInput.value = '';
            }
            if (itemModalPrioridade) {
                itemModalPrioridade.innerHTML = '';
                delete itemModalPrioridade.dataset.originalPrio;
            }
            if (itemModalOrdem) {
                itemModalOrdem.innerHTML = '';
            }
            itemModalOrdemOriginal = 1;
        });
    }

    if (autorizacaoModalEl) {
        autorizacaoModalEl.addEventListener('hidden.bs.modal', function () {
            if (autorizacaoIdInput) {
                autorizacaoIdInput.value = '';
            }
            if (autorizacaoUsuarioInput) {
                autorizacaoUsuarioInput.value = '';
            }
            if (autorizacaoSenhaInput) {
                autorizacaoSenhaInput.value = '';
            }
        });
    }

    if (recolocarModalEl) {
        recolocarModalEl.addEventListener('click', function (event) {
            var alvo = event.target.closest('[data-recolocar-action]');
            if (!alvo) {
                return;
            }

            var decisao = String(alvo.dataset.recolocarAction || '').toLowerCase();
            var solicitacaoId = parseInt(alvo.dataset.solicitacaoId || '0', 10);
            decidirSolicitacaoRecolocar(solicitacaoId, decisao);
        });

        recolocarModalEl.addEventListener('hidden.bs.modal', function () {
            if (recolocarTableBody) {
                recolocarTableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Clique em "Solicitacoes recolocar" para carregar.</td></tr>';
            }
            atualizarResumoSolicitacoesRecolocar(0);
        });
    }

    if (confirmacaoConfirmarBtn) {
        confirmacaoConfirmarBtn.addEventListener('click', function () {
            var acaoConfirmada = confirmacaoOnConfirm;
            confirmacaoOnConfirm = null;
            fecharModalConfirmacao();

            if (typeof acaoConfirmada === 'function') {
                setTimeout(function () {
                    acaoConfirmada();
                }, 0);
            }
        });
    }

    if (confirmacaoModalEl) {
        confirmacaoModalEl.addEventListener('hidden.bs.modal', function () {
            confirmacaoOnConfirm = null;
            if (confirmacaoTextoEl) {
                confirmacaoTextoEl.textContent = 'Deseja continuar?';
            }
        });
    }

    if (autorizacaoConfirmarBtn) {
        autorizacaoConfirmarBtn.addEventListener('click', function () {
            var desenhoId = parseInt((autorizacaoIdInput && autorizacaoIdInput.value) || '0', 10);
            if (!desenhoId) {
                exibirAlerta('error', 'Desenho invalido para autorizacao.');
                return;
            }

            var usuario = autorizacaoUsuarioInput ? String(autorizacaoUsuarioInput.value || '').trim() : '';
            var senha = autorizacaoSenhaInput ? String(autorizacaoSenhaInput.value || '').trim() : '';

            if (!usuarioPodeAutorizarConclusao && (!usuario || !senha)) {
                exibirAlerta('error', 'Informe usuario e senha de um perfil autorizado para liberar a conclusao.');
                return;
            }

            executarAutorizacaoConclusao(desenhoId, usuario, senha);
        });
    }

    if (itemModalConfirmarBtn) {
        itemModalConfirmarBtn.addEventListener('click', function () {
            var desenhoId = parseInt((itemModalIdInput && itemModalIdInput.value) || '0', 10);
            if (!desenhoId) {
                exibirAlerta('error', (bulkItemTipo === 'projeto' ? 'Projeto' : 'Desenho') + ' invalido para atualizar.');
                return;
            }

            var prioridadeId = parseInt((itemModalPrioridade && itemModalPrioridade.value) || '0', 10);
            if (!prioridadeId) {
                exibirAlerta('error', 'Selecione uma prioridade valida.');
                return;
            }

            var ordem = parseInt((itemModalOrdem && itemModalOrdem.value) || '0', 10);
            if (!ordem || ordem < 1) {
                exibirAlerta('error', 'Selecione uma ordem valida.');
                return;
            }

            executarAcaoLote([desenhoId], prioridadeId, ordem, {
                onSuccess: function () {
                    fecharModalItem();
                }
            });
        });
    }

    if (bulkConfirmarBtn) {
        bulkConfirmarBtn.addEventListener('click', function () {
            var ids = idsSelecionadosModalNaOrdem();
            if (!ids.length) {
                exibirAlerta('error', bulkModoEhApagar()
                    ? 'Selecione ao menos um ' + (bulkItemTipo === 'projeto' ? 'projeto' : 'desenho') + ' para apagar em lote.'
                    : 'Selecione ao menos um ' + (bulkItemTipo === 'projeto' ? 'projeto' : 'desenho') + ' para aplicacao em lote.');
                return;
            }

            if (bulkModoEhApagar()) {
                abrirModalConfirmacao('Apagar ' + ids.length + ' ' + (bulkItemTipo === 'projeto' ? 'projeto(s)' : 'desenho(s)') + ' selecionado(s)?', function () {
                    executarApagarLote(ids);
                });
                return;
            }

            var prioridadeId = parseInt((bulkPrioridade && bulkPrioridade.value) || '0', 10);
            if (!prioridadeId) {
                exibirAlerta('error', 'Selecione uma prioridade valida.');
                return;
            }

            var ordemInicial = parseInt((bulkOrdem && bulkOrdem.value) || '0', 10);
            if (!ordemInicial || ordemInicial < 1) {
                exibirAlerta('error', 'Informe uma ordem inicial valida.');
                return;
            }

            executarAcaoLote(ids, prioridadeId, ordemInicial);
        });
    }

    marcarAbaAtiva();
    sincronizarToggleConcluidasDaAba();
    atualizarCardsProcessoSelecionado();
    atualizarContadorSelecionados();
    destruirDataTablePainel();
    aplicarModoVisualizacaoPainel();
})();
</script>

<script>
const VIEWSTL_BASE = "<?= base_url('public/assets/viewstl/'); ?>";
window.stl_viewer_script_path = VIEWSTL_BASE;
</script>
<script src="<?= base_url('public/assets/viewstl/three.min.js'); ?>"></script>
<script src="<?= base_url('public/assets/viewstl/Projector.js'); ?>"></script>
<script src="<?= base_url('public/assets/viewstl/CanvasRenderer.js'); ?>"></script>
<script src="<?= base_url('public/assets/viewstl/TrackballControls.js'); ?>"></script>
<script src="<?= base_url('public/assets/viewstl/webgl_detector.js'); ?>"></script>
<script src="<?= base_url('public/assets/viewstl/ie_polyfills.js'); ?>"></script>
<script src="<?= base_url('public/assets/viewstl/OrbitControls.js'); ?>"></script>
<script src="<?= base_url('public/assets/viewstl/load_stl.min.js'); ?>"></script>
<script src="<?= base_url('public/assets/dxf-viewer/main.umd.cjs'); ?>"></script>
<script type="module">
import { DXFViewer as DXFViewerClass } from "<?= base_url('public/assets/dxf-viewer/main.js'); ?>";
window.DXFViewer = DXFViewerClass;
</script>
<script src="<?= base_url('public/assets/viewstl/stl_viewer.min.js'); ?>"></script>
<script src="<?= base_url('public/assets/viewstl/stl_viewer.js'); ?>"></script>
<script src="<?= base_url('public/assets/dxf-viewer/dxf_viewer.js'); ?>"></script>
<script src="<?= base_url('public/assets/visualizar.js?v=20260224_02'); ?>"></script>
<script>
if (typeof configurarVisualizador === 'function') {
    configurarVisualizador({
        base_url: "<?= base_url(); ?>",
        endpoint: "public/painel_tarefas_visualizar"
    });
}
</script>
