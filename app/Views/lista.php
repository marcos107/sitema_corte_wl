<?php
$embed_mode = isset($embed_mode)
    ? (bool) $embed_mode
    : (service('request')->getGet('embed') === '1');
?>
<?= $this->include('partials/wl-layout-open') ?>

<?php
$isListaTarefas = isset($array_titulo_lista) && is_array($array_titulo_lista)
    && in_array('Cortar', $array_titulo_lista, true)
    && in_array('Confirmar Corte', $array_titulo_lista, true);
?>

<style>
<?php if ($isListaTarefas) { ?>
.wl-page-title {
    display: none;
}
<?php } ?>

.page-lista-tarefas-header {
    padding: 0 0 1rem;
}

.page-lista-tarefas-header .breadcrumb {
    margin-bottom: 0;
}

.page-lista-tarefas .card-header {
    padding-bottom: 0;
}

.page-lista-tarefas .card-body {
    padding-top: 1rem;
}

.page-lista-tarefas .wl-filter-group {
    border: 1px dashed var(--tb-border-color);
    border-radius: .65rem;
    padding: .75rem .85rem;
    background: rgba(248, 250, 252, .75);
}

.page-lista-tarefas .wl-filter-group legend {
    color: #64748b;
    text-transform: uppercase;
    font-weight: 700;
    font-size: .74rem;
    letter-spacing: .02em;
    margin-bottom: .5rem;
}

.page-lista-tarefas .wl-toggle-concluidas {
    background: #f8fafc;
    border: 1px solid #dbe5f1;
    border-radius: .65rem;
    padding: .45rem .7rem;
}

.page-lista-tarefas .wl-toggle-concluidas .form-check-label {
    font-size: .84rem;
    color: #334155;
}

.page-lista-tarefas .wl-filtro-finalidade {
    display: flex;
    flex-wrap: wrap;
    gap: .55rem;
    align-items: end;
    margin-bottom: .85rem;
}

.page-lista-tarefas .wl-filtro-finalidade .form-label {
    margin: 0;
    color: #475569;
    font-size: .84rem;
    font-weight: 600;
}

.page-lista-tarefas .wl-filtro-finalidade .form-select {
    min-width: 180px;
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
.page-lista-tarefas .wl-tarefas-table td:nth-child(11) {
    width: 180px;
    text-align: right;
}

.page-lista-tarefas .wl-tarefas-table td {
    position: relative;
}

.page-lista-tarefas .wl-tarefas-table td:nth-child(10),
.page-lista-tarefas .wl-tarefas-table td:nth-child(11) {
    z-index: 4;
    overflow: visible;
}

.page-lista-tarefas .wl-row-actions {
    display: flex;
    flex-wrap: nowrap;
    gap: .35rem;
    justify-content: flex-end;
    align-items: center;
    width: 100%;
}

.page-lista-tarefas .wl-row-actions,
.page-lista-tarefas .wl-row-actions * {
    pointer-events: auto;
}

#lista-tarefas-container,
#lista-tarefas-container .table-responsive,
#lista-tarefas-container .dataTables_wrapper {
    overflow: visible;
}

#lista-tarefas-container .table-responsive {
    overflow-x: auto;
    overflow-y: visible !important;
}

.page-lista-tarefas .wl-row-actions .dropdown {
    position: relative;
    z-index: 30;
}

.page-lista-tarefas .wl-row-actions .btn {
    line-height: 1.1;
    font-weight: 600;
}

.page-lista-tarefas .wl-row-action-main {
    min-width: 112px;
}

.page-lista-tarefas .wl-row-action-more {
    width: 32px;
    min-width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.page-lista-tarefas .wl-row-actions .dropdown-menu {
    min-width: 190px;
    z-index: 3000;
}

.page-lista-tarefas .wl-row-actions .dropdown-item {
    display: flex;
    align-items: center;
    gap: .25rem;
    font-size: .84rem;
}

.page-lista-tarefas .wl-row-actions--confirm .btn {
    min-width: 104px;
}

.page-lista-tarefas .wl-tarefas-table th.wl-col-dimensao-dxf,
.page-lista-tarefas .wl-tarefas-table td.wl-col-dimensao-dxf,
.page-lista-tarefas .wl-tarefas-table.wl-has-dxf th:nth-child(9),
.page-lista-tarefas .wl-tarefas-table.wl-has-dxf td:nth-child(9) {
    width: 1% !important;
    min-width: 112px;
    max-width: max-content;
    white-space: nowrap;
    text-align: center;
}

.page-lista-tarefas .wl-tarefas-table.wl-has-dxf th:nth-child(10),
.page-lista-tarefas .wl-tarefas-table.wl-has-dxf td:nth-child(10) {
    width: 145px;
    text-align: center;
}

.page-lista-tarefas .wl-tarefas-table th.wl-col-acoes,
.page-lista-tarefas .wl-tarefas-table td.wl-col-acoes,
.page-lista-tarefas .wl-tarefas-table:not(.wl-has-dxf) th:nth-child(10),
.page-lista-tarefas .wl-tarefas-table:not(.wl-has-dxf) td:nth-child(10),
.page-lista-tarefas .wl-tarefas-table:not(.wl-has-dxf) th:nth-child(11),
.page-lista-tarefas .wl-tarefas-table:not(.wl-has-dxf) td:nth-child(11),
.page-lista-tarefas .wl-tarefas-table.wl-has-dxf th:nth-child(11),
.page-lista-tarefas .wl-tarefas-table.wl-has-dxf td:nth-child(11),
.page-lista-tarefas .wl-tarefas-table.wl-has-dxf th:nth-child(12),
.page-lista-tarefas .wl-tarefas-table.wl-has-dxf td:nth-child(12) {
    width: 150px !important;
    min-width: 150px;
    max-width: 150px;
    text-align: right;
}

.page-lista-tarefas .wl-tarefas-table th.wl-col-acoes,
.page-lista-tarefas .wl-tarefas-table td.wl-col-acoes {
    overflow: visible;
}

.page-lista-tarefas .wl-tarefas-table td.wl-col-acoes {
    z-index: 4;
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

#lista-tarefas-container.wl-view-detailed .wl-tarefas-table {
    table-layout: auto;
}

#lista-tarefas-container.wl-view-detailed .wl-tarefas-table td:nth-child(4),
#lista-tarefas-container.wl-view-detailed .wl-tarefas-table td:nth-child(8) {
    white-space: normal;
    overflow: visible;
    text-overflow: clip;
}

#lista-tarefas-container.wl-view-detailed .wl-cell-truncate {
    display: block;
    max-width: none;
    white-space: normal;
    overflow: visible;
    text-overflow: clip;
    word-break: break-word;
}

.page-lista-tarefas tbody tr.wl-row-menu-open {
    position: relative;
    z-index: 35;
}

.page-lista-tarefas tbody tr.wl-row-menu-open > td {
    position: relative;
    z-index: 35;
    overflow: visible !important;
}

#modal.modal-1 {
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
#modal .modal-footer {
    background-color: #ffffff;
}

#modal .modal-header {
    padding: .9rem 1.1rem;
    border-bottom: 1px solid #dbe5f1;
}

#modal .modal-title {
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: .01em;
}

#modal .modal-footer {
    padding: .8rem 1rem;
    border-top: 1px solid #dbe5f1;
}

#modal .modal-footer .btn {
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

#modal .custom-select,
#modal select,
#modal input[type="text"],
#modal input[type="file"] {
    width: 100%;
    min-height: 34px;
    border: 1px solid #cbd5e1;
    border-radius: .5rem;
    padding: .3rem .5rem;
    font-size: .85rem;
    background-color: #ffffff;
}

#modal .wl-prioridade-cell {
    font-weight: 700;
}

#modal #modal_apagar_container {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    margin-right: auto;
}

#modal #modal_apagar_checkbox {
    width: 18px;
    height: 18px;
}

@media (max-width: 768px) {
    .page-lista-tarefas-header {
        padding-bottom: .75rem;
    }

    .page-lista-tarefas .wl-filter-group {
        padding: .65rem .7rem;
    }

    .page-lista-tarefas .wl-filtro-finalidade {
        gap: .45rem;
    }

    .page-lista-tarefas .wl-filtro-finalidade .form-select {
        min-width: 150px;
    }

    .page-lista-tarefas .wl-tarefas-table:not(.wl-has-dxf) th:nth-child(10),
    .page-lista-tarefas .wl-tarefas-table:not(.wl-has-dxf) td:nth-child(10),
    .page-lista-tarefas .wl-tarefas-table:not(.wl-has-dxf) th:nth-child(11),
    .page-lista-tarefas .wl-tarefas-table:not(.wl-has-dxf) td:nth-child(11),
    .page-lista-tarefas .wl-tarefas-table.wl-has-dxf th:nth-child(11),
    .page-lista-tarefas .wl-tarefas-table.wl-has-dxf td:nth-child(11),
    .page-lista-tarefas .wl-tarefas-table.wl-has-dxf th:nth-child(12),
    .page-lista-tarefas .wl-tarefas-table.wl-has-dxf td:nth-child(12) {
        width: 136px;
        min-width: 136px;
        max-width: 136px;
    }

    .page-lista-tarefas .wl-row-action-main {
        min-width: 92px;
        padding-left: .55rem;
        padding-right: .55rem;
    }

    .page-lista-tarefas .wl-row-actions--confirm .btn {
        min-width: 92px;
    }

    .page-lista-tarefas .wl-row-actions--with-menu .wl-row-action-main {
        display: none;
    }

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
    <div class="modal-dialog" id="modal_sizer" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_titulo"></h5>
                <button type="button" class="btn-close" onclick="fecharModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal_bory"></div>
            <div class="modal-footer" id="modal_rodape">
                <button type="button" class="btn btn-secondary" id="botao_fechar_modal" onclick="fecharModal()">Cancelar</button>
                <button type="button" class="btn btn-primary" id="botao_confirmar_modal" onclick="confirmarModal()"></button>
            </div>
        </div>
    </div>
</div>

<audio id="bell-sound" src="<?= base_url('public/assets/som/bell.mp4') ?>" preload="auto"></audio>

<div class="page-title-box d-sm-flex align-items-center justify-content-between <?= $isListaTarefas ? 'page-lista-tarefas-header' : '' ?>">
    <div>
        <h4 class="mb-sm-0"><?= esc($titulo ?? 'Lista') ?></h4>
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Desenhos</a></li>
            <li class="breadcrumb-item active"><?= esc($titulo ?? 'Lista') ?></li>
        </ol>
    </div>
    <span class="badge bg-info-subtle text-info fw-semibold">
        <i class="ri-list-check-3 align-bottom me-1"></i>Painel
    </span>
</div>

<div id="list1" class="card wl-card <?= $isListaTarefas ? 'page-lista-tarefas' : '' ?>">
    <div class="card-header border-0">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h5 class="card-title mb-1"><?= esc($titulo) ?></h5>
                <p class="text-muted mb-0">Acompanhe a fila de tarefas por processo.</p>
            </div>
            <span class="badge bg-primary-subtle text-primary fw-semibold">
                <i class="ri-time-line align-bottom me-1"></i>Fila ativa
            </span>
        </div>
    </div>
    <div class="card-body" id="top-lista">
        <?php if ($selecao_lista) { ?>
            <fieldset class="wl-filter-group mb-3">
                <legend>Mostrar</legend>
                <div class="d-flex flex-wrap gap-3">
                    <div class="form-check form-check-primary mb-0">
                        <input class="form-check-input" type="checkbox" id="checkbox_ativos" onclick="lista()" checked>
                        <label class="form-check-label" for="checkbox_ativos">Ativos</label>
                    </div>
                    <div class="form-check form-check-primary mb-0">
                        <input class="form-check-input" type="checkbox" id="checkbox_desativado" onclick="lista()">
                        <label class="form-check-label" for="checkbox_desativado">Desativados</label>
                    </div>
                </div>
            </fieldset>
        <?php } ?>

        <?php if (isset($hora_lista)) { ?>
            <fieldset class="wl-filter-group mb-3">
                <legend>Intervalo</legend>
                <div class="row g-2">
                    <div class="col-12 col-md-4 col-lg-3">
                        <label class="form-label mb-1" for="dataFinal">Data inicial</label>
                        <input class="form-control" type="date" id="dataFinal" name="dataFinal" required>
                    </div>
                    <div class="col-12 col-md-4 col-lg-3">
                        <label class="form-label mb-1" for="dataInicial">Data final</label>
                        <input class="form-control" type="date" id="dataInicial" name="dataInicial" required>
                    </div>
                </div>
            </fieldset>
        <?php } ?>

        <?php if ($isListaTarefas) { ?>
            <div class="row g-3 align-items-end mb-3">
                <div class="col-12 col-lg-auto">
                    <div class="form-check form-switch wl-toggle-concluidas">
                        <input class="form-check-input" type="checkbox" id="checkbox_alerta_novo_desenho" onchange="alternarAlertaNovoDesenho(this.checked)" checked>
                        <label class="form-check-label" for="checkbox_alerta_novo_desenho">Tocar som e mostrar notificacao ao chegar desenho novo</label>
                    </div>
                </div>
                <div class="col-12 col-lg-auto">
                    <div class="form-check form-switch wl-toggle-concluidas wl-view-toggle">
                        <input class="form-check-input" type="checkbox" id="checkbox_visualizacao_detalhada">
                        <label class="form-check-label" for="checkbox_visualizacao_detalhada">Visualizacao detalhada</label>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php
        $titulo_lista = "";
        for ($i = 0; $i < count($array_titulo_lista); $i++) {
            $titulo_lista .= "<th>" . $array_titulo_lista[$i] . "</th>";
        }
        ?>

        <div id="lista-tarefas-container">
            <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped table-hover table-nowrap align-middle mb-0 <?= $isListaTarefas ? 'wl-tarefas-table' : '' ?>">
                    <thead>
                        <tr><?= $titulo_lista ?></tr>
                    </thead>
                    <tbody id="lista">
                        <?= $lista ?>
                    </tbody>
                    <tfoot>
                        <tr><?= $titulo_lista ?></tr>
                    </tfoot>
                </table>
            </div>

            <div id="roda_pe"></div>
        </div>
    </div>
</div>

<?= $this->include('partials/wl-layout-close') ?>
<?= $this->include('partials/wl-scripts') ?>

<script>
<?php if (empty($ajax)) { ?>
(function () {
    if (window.jQuery && $('#example1').length) {
        $('#example1').DataTable({
            responsive: true,
            deferRender: true,
            lengthChange: false,
            autoWidth: false,
            buttons: ['colvis'],
            order: [],
            ordering: true,
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
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    }
})();
<?php } ?>

const handlePhone = (event) => {
    let input = event.target;
    input.value = phoneMask(input.value);
};

const WL_VIEW_MODE_KEY = 'wl_lista_modo_visualizacao';

function lerModoVisualizacaoDetalhada() {
    try {
        return window.localStorage.getItem(WL_VIEW_MODE_KEY) === 'detalhada';
    } catch (error) {
        return false;
    }
}

function salvarModoVisualizacaoDetalhada(ativada) {
    try {
        window.localStorage.setItem(WL_VIEW_MODE_KEY, ativada ? 'detalhada' : 'compacta');
    } catch (error) {
        // Ignora falhas de storage e mantem o modo apenas na sessao atual.
    }
}

function aplicarModoVisualizacaoLista() {
    const listContainer = document.getElementById('lista-tarefas-container');
    const input = document.getElementById('checkbox_visualizacao_detalhada');
    const ativada = input ? !!input.checked : lerModoVisualizacaoDetalhada();

    if (listContainer) {
        listContainer.classList.toggle('wl-view-detailed', ativada);
    }
}

function alternarClasseLinhaDropdown(evento, aberta) {
    const dropdown = evento.target && typeof evento.target.closest === 'function'
        ? evento.target.closest('.dropdown')
        : null;

    if (!dropdown) {
        return;
    }

    const linha = dropdown.closest('tr');
    if (linha) {
        linha.classList.toggle('wl-row-menu-open', aberta);
    }
}

document.addEventListener('show.bs.dropdown', function (event) {
    if (event.target && event.target.closest && event.target.closest('#list1.page-lista-tarefas')) {
        alternarClasseLinhaDropdown(event, true);
    }
});

document.addEventListener('hidden.bs.dropdown', function (event) {
    if (event.target && event.target.closest && event.target.closest('#list1.page-lista-tarefas')) {
        alternarClasseLinhaDropdown(event, false);
    }
});

const checkboxVisualizacaoDetalhada = document.getElementById('checkbox_visualizacao_detalhada');
if (checkboxVisualizacaoDetalhada) {
    checkboxVisualizacaoDetalhada.checked = lerModoVisualizacaoDetalhada();
    aplicarModoVisualizacaoLista();
    checkboxVisualizacaoDetalhada.addEventListener('change', function () {
        salvarModoVisualizacaoDetalhada(checkboxVisualizacaoDetalhada.checked);
        aplicarModoVisualizacaoLista();
    });
}

const phoneMask = (value) => {
    if (!value) return '';
    value = value.replace(/\D/g, '');
    value = value.replace(/(\d{2})(\d)/, '($1) $2');
    value = value.replace(/(\d)(\d{4})$/, '$1-$2');
    return value;
};

const modalRodapePadrao = document.getElementById('modal_rodape') ? document.getElementById('modal_rodape').innerHTML : '';

function resetarModalEstadoPadrao() {
    const modalSizer = document.getElementById('modal_sizer');
    if (modalSizer) {
        modalSizer.classList.remove('modal-sm', 'modal-lg', 'modal-xl', 'modal-xxl');
    }

    const modalRodape = document.getElementById('modal_rodape');
    if (modalRodape && modalRodapePadrao) {
        modalRodape.innerHTML = modalRodapePadrao;
    }

    const botaoAlternativo = document.getElementById('botao_confirmar_modal1') || document.getElementById('botao_confirmar_modal_apagar');
    if (botaoAlternativo) {
        botaoAlternativo.id = 'botao_confirmar_modal';
    }

    const botaoConfirmar = document.getElementById('botao_confirmar_modal');
    if (botaoConfirmar) {
        botaoConfirmar.disabled = false;
        botaoConfirmar.setAttribute('onclick', 'confirmarModal()');
    }
}

function mostrarModal() {
    const modal = document.getElementById('modal');
    modal.style.display = 'block';
    document.body.classList.add('no-scroll');
}

function fecharModal() {
    resetarModalEstadoPadrao();
    const modal = document.getElementById('modal');
    modal.style.display = 'none';
    document.getElementById('modal_bory').innerHTML = '';
    document.body.classList.remove('no-scroll');
}

window.onclick = function (event) {
    const modal = document.getElementById('modal');
    if (event.target === modal) {
        fecharModal();
    }
};
</script>

<?php if ($ajax != '') {
    echo view($ajax);
} ?>

<?php if (isset($hora_lista)) { ?>
<script>
setDataAtual();

function setDataAtual() {
    let dataInput = document.getElementById('dataInicial');
    let dataAtual = new Date();

    let ano = dataAtual.getFullYear().toString().padStart(4, '0');
    let mes = (dataAtual.getMonth() + 1).toString().padStart(2, '0');
    let dia = dataAtual.getDate().toString().padStart(2, '0');

    let dataFormatada = `${ano}-${mes}-${dia}`;
    dataInput.value = dataFormatada;

    dataInput = document.getElementById('dataFinal');
    dataAtual.setDate(dataAtual.getDate() - 3);
    ano = dataAtual.getFullYear().toString().padStart(4, '0');
    mes = (dataAtual.getMonth() + 1).toString().padStart(2, '0');
    dia = dataAtual.getDate().toString().padStart(2, '0');

    dataFormatada = `${ano}-${mes}-${dia}`;
    dataInput.value = dataFormatada;
}
</script>
<?php } ?>

<?= $this->include('partials/wl-layout-end') ?>
