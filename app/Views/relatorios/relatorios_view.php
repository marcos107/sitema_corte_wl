<?= $this->include('partials/wl-layout-open') ?>

<style>
.wl-page-title {
    display: none;
}

.wl-report-page-header {
    padding: 0 0 1rem;
}

.wl-report-page-header .breadcrumb {
    margin-bottom: 0;
}

.wl-report-card .card-header {
    padding-bottom: .9rem;
}

.wl-report-card .card-footer {
    border-top: 1px solid var(--tb-border-color);
}

.wl-report-static-groups .form-check-inline {
    margin-right: 1rem;
}

.wl-report-user-group {
    margin-top: 1rem;
    border: 1px solid var(--tb-border-color);
    border-radius: .75rem;
    padding: .9rem;
    background: #f8fbff;
}

.wl-report-user-group-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: .75rem;
    flex-wrap: wrap;
}

.wl-report-user-group-actions {
    display: flex;
    gap: .5rem;
    flex-wrap: wrap;
}

.wl-report-checkbox-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: .45rem .75rem;
    margin-top: .8rem;
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

.wl-upload-empty {
    color: #64748b;
    font-size: .9rem;
    border: 1px dashed #cbd5e1;
    border-radius: .6rem;
    padding: .8rem;
    text-align: center;
}

@media (max-width: 768px) {
    .wl-report-page-header {
        padding-bottom: .8rem;
    }

    .wl-report-user-group {
        padding: .75rem;
    }
}
</style>

<div class="page-title-box d-sm-flex align-items-center justify-content-between wl-report-page-header">
    <div>
        <h4 class="mb-sm-0"><?= esc($titulo ?? 'Relatorios') ?></h4>
        <p class="text-muted mb-1"><?= esc($subtitulo ?? 'Gerar PDF por período e processo') ?></p>
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Relatorios</a></li>
            <li class="breadcrumb-item active">Gerar PDF</li>
        </ol>
    </div>
</div>

<div id="inicioOverlay" class="card wl-card wl-report-card mb-3">
    <div class="card-header border-0">
        <h5 class="card-title mb-1">Selecionar Processo</h5>
        <p class="text-muted mb-0">Primeiro, selecione o processo de destino do relatorio.</p>
    </div>
    <div class="card-body">
        <label class="form-label fw-semibold">Processos disponíveis</label>
        <div id="processos_radio" class="wl-process-grid"></div>
    </div>
</div>

<div id="cadastro1" class="card wl-card wl-report-card">
    <div class="card-header border-0">
        <div>
            <h5 id="relatorio_card_title" class="card-title mb-1">Filtros do Relatorio</h5>
            <p id="relatorio_card_subtitle" class="text-muted mb-0">Selecione o período, tipo e participantes.</p>
        </div>
    </div>

    <div class="card-body" id="inputs_body">
        <div class="row g-3 wl-report-static-groups">
            <div id="group_0" class="col-12 col-md-6 col-xl-2">
                <label for="data_inicial" class="form-label">Data inicial</label>
                <input id="data_inicial" type="date" class="form-control" required>
            </div>

            <div id="group_1" class="col-12 col-md-6 col-xl-2">
                <label for="data_final" class="form-label">Data final</label>
                <input id="data_final" type="date" class="form-control" required disabled>
            </div>

            <div id="group_6" class="col-12 col-md-6 col-xl-2">
                <label class="form-label d-block">Período por</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="periodo_adicionado">
                    <label class="form-check-label" for="periodo_adicionado">Adicionado</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="periodo_finalizado">
                    <label class="form-check-label" for="periodo_finalizado">Finalizado</label>
                </div>
            </div>


            <div id="group_4" class="col-12 col-md-6 col-xl-2">
                <label for="empresa_id" class="form-label">Empresa/Cliente</label>
                <select id="empresa_id" class="form-select">
                    <option value="">Todas</option>
                </select>
            </div>

            <div id="group_5" class="col-12 col-md-6 col-xl-2">
                <label for="empreendimento_id" class="form-label">Empreendimento</label>
                <select id="empreendimento_id" class="form-select" disabled>
                    <option value="">Todos</option>
                </select>
            </div>
            <div id="group_2" class="col-12 col-md-6 col-xl-2">
                <label class="form-label d-block">Tipo de Relatorio</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="rad_1" name="tipo_relatorio">
                    <label class="form-check-label" for="rad_1">Analítico</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="rad_2" name="tipo_relatorio">
                    <label class="form-check-label" for="rad_2">Sintético</label>
                </div>
            </div>

            <div id="group_3" class="col-12 col-md-6 col-xl-2">
                <label class="form-label d-block">Usuários</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="checkbox_ativo">
                    <label class="form-check-label" for="checkbox_ativo">Ativo</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="checkbox_desativado">
                    <label class="form-check-label" for="checkbox_desativado">Desativado</label>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4">
            <h6 class="text-uppercase text-muted fw-semibold mb-0">Participantes por Grupo</h6>
            <small class="text-muted">Selecione pelo menos um usuário por relatorio.</small>
        </div>

        <div id="user_groups_placeholder"></div>
    </div>

    <div class="card-footer bg-transparent">
        <div class="d-grid d-md-flex justify-content-md-end">
            <button name="cadastarar" type="button" onclick="cadastrar()" id="cadastrar_btn" class="btn btn-primary btn-lg">
                <?= esc($button_execut_nome ?? 'Gerar PDF') ?>
            </button>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmar_sem_dados_modal" tabindex="-1" aria-labelledby="confirmar_sem_dados_modal_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmar_sem_dados_modal_label">Relatorio sem dados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                Nao ha dados para os filtros selecionados. Deseja gerar o PDF mesmo assim?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmar_sem_dados_btn">Gerar mesmo assim</button>
            </div>
        </div>
    </div>
</div>

<?= $this->include('partials/wl-layout-close') ?>
<?= $this->include('partials/wl-scripts') ?>

<?php if ($ajax != '') {
    echo view($ajax);
} ?>

<?= $this->include('partials/wl-layout-end') ?>

