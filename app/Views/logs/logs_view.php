<?= $this->include('partials/wl-layout-open') ?>

<style>
.wl-page-title {
    display: none;
}

.wl-logs-header {
    padding: 0 0 1rem;
}

.wl-logs-header .breadcrumb {
    margin-bottom: 0;
}

.wl-logs-filter-card .card-body,
.wl-logs-list-card .card-body {
    padding-top: 1rem;
}

.wl-logs-filter-grid {
    display: grid;
    gap: .85rem;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
}

.wl-logs-filter-actions {
    display: flex;
    gap: .65rem;
    align-items: end;
    flex-wrap: wrap;
}

.wl-logs-table td,
.wl-logs-table th {
    vertical-align: top;
}

.wl-logs-table .wl-log-meta {
    font-size: .8rem;
    color: #475569;
    line-height: 1.35;
}

.wl-logs-table .wl-log-resumo {
    min-width: 220px;
    white-space: normal;
}

.wl-logs-table .wl-log-uri {
    min-width: 220px;
    max-width: 320px;
    white-space: normal;
    word-break: break-word;
}

.wl-log-section + .wl-log-section {
    margin-top: 1rem;
}

.wl-log-section h6 {
    margin-bottom: .55rem;
    color: #0f172a;
}

.wl-log-kv {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: .65rem .9rem;
}

.wl-log-kv-item {
    border: 1px solid #dbe5f1;
    border-radius: .7rem;
    padding: .75rem .85rem;
    background: #f8fbff;
}

.wl-log-kv-item small {
    display: block;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .02em;
    font-size: .72rem;
    margin-bottom: .25rem;
}

.wl-log-pre {
    white-space: pre-wrap;
    word-break: break-word;
    margin: 0;
    background: #0f172a;
    color: #e2e8f0;
    border-radius: .7rem;
    padding: .85rem;
    font-size: .83rem;
}

@media (max-width: 768px) {
    .wl-logs-filter-actions {
        width: 100%;
    }

    .wl-logs-filter-actions .btn {
        flex: 1 1 auto;
    }
}
</style>

<div class="page-title-box d-sm-flex align-items-center justify-content-between wl-logs-header">
    <div>
        <h4 class="mb-sm-0"><?= esc($titulo ?? 'Logs de Alteracoes') ?></h4>
        <p class="text-muted mb-1"><?= esc($subtitulo ?? 'Auditoria completa das mudancas registradas no sistema.') ?></p>
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Configuracoes</a></li>
            <li class="breadcrumb-item active">Logs</li>
        </ol>
    </div>
</div>

<div class="card wl-card wl-logs-filter-card mb-3">
    <div class="card-header border-0">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h5 class="card-title mb-1">Filtros</h5>
                <p class="text-muted mb-0">Use o periodo e o limite para abrir a auditoria sem sobrecarregar a tela.</p>
            </div>
            <span id="logs_total_badge" class="badge bg-primary-subtle text-primary fw-semibold">0 registros</span>
        </div>
    </div>
    <div class="card-body">
        <div class="wl-logs-filter-grid">
            <div class="form-group mb-0">
                <label class="form-label" for="logs_data_inicial">Data inicial</label>
                <input type="date" class="form-control" id="logs_data_inicial">
            </div>
            <div class="form-group mb-0">
                <label class="form-label" for="logs_data_final">Data final</label>
                <input type="date" class="form-control" id="logs_data_final">
            </div>
            <div class="form-group mb-0">
                <label class="form-label" for="logs_limite">Limite</label>
                <select id="logs_limite" class="form-select">
                    <option value="200">200</option>
                    <option value="500" selected>500</option>
                    <option value="1000">1000</option>
                    <option value="2000">2000</option>
                </select>
            </div>
            <div class="wl-logs-filter-actions">
                <button type="button" class="btn btn-primary" onclick="carregarLogsAlteracoes()">
                    <i class="ri-refresh-line align-bottom me-1"></i>Atualizar
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="limparFiltrosLogsAlteracoes()">
                    Limpar filtro
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card wl-card wl-logs-list-card">
    <div class="card-header border-0">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h5 class="card-title mb-1">Auditoria</h5>
                <p class="text-muted mb-0">Cada linha mostra o resumo da alteracao; o botao "Ver tudo" abre o contexto completo.</p>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="logs_table" class="table table-bordered table-striped table-hover table-nowrap align-middle mb-0 wl-logs-table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Usuario</th>
                        <th>Item</th>
                        <th>ID item</th>
                        <th>Acao</th>
                        <th>Resumo</th>
                        <th>IP / MAC</th>
                        <th>URI</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="logs_lista">
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">Carregando logs...</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Data</th>
                        <th>Usuario</th>
                        <th>Item</th>
                        <th>ID item</th>
                        <th>Acao</th>
                        <th>Resumo</th>
                        <th>IP / MAC</th>
                        <th>URI</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="log_detalhe_modal" tabindex="-1" aria-labelledby="log_detalhe_modal_label" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="log_detalhe_modal_label">Detalhes do log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body" id="log_detalhe_conteudo"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->include('partials/wl-layout-close') ?>
<?= $this->include('partials/wl-scripts') ?>

<script>
var logsAlteracoesRegistros = [];

function escapeHtmlLogsAlteracoes(valor) {
    return String(valor == null ? '' : valor)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function formatarRotuloLogAlteracoes(chave) {
    return String(chave || '')
        .replace(/^auditoria\./, '')
        .replace(/_/g, ' ')
        .replace(/\b\w/g, function (letra) {
            return letra.toUpperCase();
        });
}

function montarDataTableLogsAlteracoes() {
    if (!(window.jQuery && $('#logs_table').length)) {
        return;
    }

    if ($.fn.DataTable.isDataTable('#logs_table')) {
        $('#logs_table').DataTable().destroy();
    }

    $('#logs_table').DataTable({
        responsive: true,
        lengthChange: false,
        autoWidth: false,
        pageLength: 25,
        order: [[0, 'desc']],
        columnDefs: [
            {
                targets: [8],
                orderable: false,
                searchable: false,
                className: 'text-end'
            }
        ],
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
            }
        }
    });
}

function atualizarBadgeLogsAlteracoes(total, limite) {
    var badge = document.getElementById('logs_total_badge');
    if (!badge) {
        return;
    }

    badge.textContent = total + ' registros';
    if (Number(total) >= Number(limite)) {
        badge.textContent += ' (limite ' + limite + ')';
    }
}

function montarResumoMetaLogAlteracoes(registro) {
    var partes = [];
    var meta = registro && registro.meta ? registro.meta : {};

    if (meta.ip) {
        partes.push('<div><strong>IP:</strong> ' + escapeHtmlLogsAlteracoes(meta.ip) + '</div>');
    }
    if (meta.mac_cliente) {
        partes.push('<div><strong>MAC:</strong> ' + escapeHtmlLogsAlteracoes(meta.mac_cliente) + '</div>');
    }
    if (meta.metodo) {
        partes.push('<div><strong>Metodo:</strong> ' + escapeHtmlLogsAlteracoes(meta.metodo) + '</div>');
    }

    return partes.length ? partes.join('') : '<span class="text-muted">-</span>';
}

function renderizarLogsAlteracoes(registros, limite) {
    logsAlteracoesRegistros = Array.isArray(registros) ? registros : [];

    var corpo = document.getElementById('logs_lista');
    if (!corpo) {
        return;
    }

    if (!logsAlteracoesRegistros.length) {
        corpo.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">Nenhum log encontrado para os filtros informados.</td></tr>';
        atualizarBadgeLogsAlteracoes(0, limite);
        montarDataTableLogsAlteracoes();
        return;
    }

    corpo.innerHTML = logsAlteracoesRegistros.map(function (registro, indice) {
        var usuario = escapeHtmlLogsAlteracoes(registro.usuario || '');
        var usuarioId = escapeHtmlLogsAlteracoes(registro.usuario_id || '');
        var item = escapeHtmlLogsAlteracoes(registro.item || '-');
        var itemId = escapeHtmlLogsAlteracoes(registro.item_id || '-');
        var acao = escapeHtmlLogsAlteracoes(registro.acao || '-');
        var resumo = escapeHtmlLogsAlteracoes(registro.resumo || '-');
        var uri = escapeHtmlLogsAlteracoes((registro.meta && registro.meta.uri) ? registro.meta.uri : '-');

        return '<tr>'
            + '<td data-order="' + escapeHtmlLogsAlteracoes(registro.data_ordem || '') + '">' + escapeHtmlLogsAlteracoes(registro.data || '-') + '</td>'
            + '<td>' + usuario + (usuarioId ? '<div class="text-muted small">ID ' + usuarioId + '</div>' : '') + '</td>'
            + '<td>' + item + '</td>'
            + '<td>' + itemId + '</td>'
            + '<td>' + acao + '</td>'
            + '<td class="wl-log-resumo">' + resumo + '</td>'
            + '<td class="wl-log-meta">' + montarResumoMetaLogAlteracoes(registro) + '</td>'
            + '<td class="wl-log-uri">' + uri + '</td>'
            + '<td><button type="button" class="btn btn-outline-primary btn-sm" onclick="abrirDetalheLogAlteracoes(' + indice + ')">Ver tudo</button></td>'
            + '</tr>';
    }).join('');

    atualizarBadgeLogsAlteracoes(logsAlteracoesRegistros.length, limite);
    montarDataTableLogsAlteracoes();
}

function carregarLogsAlteracoes() {
    var dataInicial = document.getElementById('logs_data_inicial');
    var dataFinal = document.getElementById('logs_data_final');
    var limite = document.getElementById('logs_limite');
    var corpo = document.getElementById('logs_lista');

    if (corpo) {
        corpo.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">Carregando logs...</td></tr>';
    }

    $.ajax({
        url: '<?= base_url('public/logs_alteracoes_lista') ?>',
        type: 'POST',
        dataType: 'json',
        data: {
            data_inicial: dataInicial ? dataInicial.value : '',
            data_final: dataFinal ? dataFinal.value : '',
            limite: limite ? limite.value : '500'
        },
        success: function (response) {
            if (!response || response.ok !== true) {
                renderizarLogsAlteracoes([], limite ? limite.value : 500);
                return;
            }

            renderizarLogsAlteracoes(response.registros || [], response.limite || (limite ? limite.value : 500));
        },
        error: function () {
            renderizarLogsAlteracoes([], limite ? limite.value : 500);
        }
    });
}

function montarTabelaMudancasLogAlteracoes(registro) {
    var detalhes = registro && Array.isArray(registro.detalhes) ? registro.detalhes : [];
    if (!detalhes.length) {
        return '<div class="text-muted">Nenhum detalhe estruturado registrado.</div>';
    }

    var linhas = detalhes.map(function (detalhe) {
        return '<tr>'
            + '<td>' + escapeHtmlLogsAlteracoes(detalhe.campo || '-') + '</td>'
            + '<td>' + escapeHtmlLogsAlteracoes(detalhe.valor_antes || '-') + '</td>'
            + '<td>' + escapeHtmlLogsAlteracoes(detalhe.valor_depois || '-') + '</td>'
            + '</tr>';
    }).join('');

    return '<div class="table-responsive"><table class="table table-sm table-bordered align-middle mb-0">'
        + '<thead><tr><th>Campo</th><th>Antes</th><th>Depois</th></tr></thead>'
        + '<tbody>' + linhas + '</tbody></table></div>';
}

function montarTabelaMetaLogAlteracoes(registro) {
    var meta = registro && registro.meta ? registro.meta : {};
    var chaves = Object.keys(meta);
    if (!chaves.length) {
        return '<div class="text-muted">Nenhum metadado adicional registrado.</div>';
    }

    var linhas = chaves.map(function (chave) {
        return '<tr>'
            + '<td>' + escapeHtmlLogsAlteracoes(formatarRotuloLogAlteracoes(chave)) + '</td>'
            + '<td>' + escapeHtmlLogsAlteracoes(meta[chave] || '-') + '</td>'
            + '</tr>';
    }).join('');

    return '<div class="table-responsive"><table class="table table-sm table-bordered align-middle mb-0">'
        + '<thead><tr><th>Campo</th><th>Valor</th></tr></thead>'
        + '<tbody>' + linhas + '</tbody></table></div>';
}

function abrirDetalheLogAlteracoes(indice) {
    var registro = logsAlteracoesRegistros[indice];
    if (!registro) {
        return;
    }

    var conteudo = document.getElementById('log_detalhe_conteudo');
    if (!conteudo) {
        return;
    }

    conteudo.innerHTML = ''
        + '<div class="wl-log-section">'
        + '  <div class="wl-log-kv">'
        + '    <div class="wl-log-kv-item"><small>Data</small>' + escapeHtmlLogsAlteracoes(registro.data || '-') + '</div>'
        + '    <div class="wl-log-kv-item"><small>Usuario</small>' + escapeHtmlLogsAlteracoes(registro.usuario || '-') + '</div>'
        + '    <div class="wl-log-kv-item"><small>ID do usuario</small>' + escapeHtmlLogsAlteracoes(registro.usuario_id || '-') + '</div>'
        + '    <div class="wl-log-kv-item"><small>Item</small>' + escapeHtmlLogsAlteracoes(registro.item || '-') + '</div>'
        + '    <div class="wl-log-kv-item"><small>ID do item</small>' + escapeHtmlLogsAlteracoes(registro.item_id || '-') + '</div>'
        + '    <div class="wl-log-kv-item"><small>Acao</small>' + escapeHtmlLogsAlteracoes(registro.acao || '-') + '</div>'
        + '  </div>'
        + '</div>'
        + '<div class="wl-log-section"><h6>Mudancas registradas</h6>' + montarTabelaMudancasLogAlteracoes(registro) + '</div>'
        + '<div class="wl-log-section"><h6>Contexto de auditoria</h6>' + montarTabelaMetaLogAlteracoes(registro) + '</div>'
        + '<div class="wl-log-section"><h6>Campos brutos</h6>'
        + '  <div class="wl-log-kv">'
        + '    <div class="wl-log-kv-item"><small>Info mais</small>' + escapeHtmlLogsAlteracoes(registro.info_mais || '-') + '</div>'
        + '    <div class="wl-log-kv-item"><small>Resumo antes</small>' + escapeHtmlLogsAlteracoes(registro.antes || '-') + '</div>'
        + '    <div class="wl-log-kv-item"><small>Resumo depois</small>' + escapeHtmlLogsAlteracoes(registro.depois || '-') + '</div>'
        + '  </div>'
        + '</div>';

    var modalElement = document.getElementById('log_detalhe_modal');
    if (!modalElement || !(window.bootstrap && window.bootstrap.Modal)) {
        return;
    }

    window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
}

function limparFiltrosLogsAlteracoes() {
    var dataInicial = document.getElementById('logs_data_inicial');
    var dataFinal = document.getElementById('logs_data_final');
    var limite = document.getElementById('logs_limite');

    if (dataInicial) {
        dataInicial.value = '';
    }
    if (dataFinal) {
        dataFinal.value = '';
    }
    if (limite) {
        limite.value = '500';
    }

    carregarLogsAlteracoes();
}

function preencherPeriodoInicialLogsAlteracoes() {
    var dataInicial = document.getElementById('logs_data_inicial');
    var dataFinal = document.getElementById('logs_data_final');
    if (!dataInicial || !dataFinal || dataInicial.value || dataFinal.value) {
        return;
    }

    var hoje = new Date();
    var inicio = new Date();
    inicio.setDate(hoje.getDate() - 30);

    var pad = function (valor) {
        return String(valor).padStart(2, '0');
    };

    dataInicial.value = inicio.getFullYear() + '-' + pad(inicio.getMonth() + 1) + '-' + pad(inicio.getDate());
    dataFinal.value = hoje.getFullYear() + '-' + pad(hoje.getMonth() + 1) + '-' + pad(hoje.getDate());
}

document.addEventListener('DOMContentLoaded', function () {
    preencherPeriodoInicialLogsAlteracoes();
    carregarLogsAlteracoes();
});
</script>

<?= $this->include('partials/wl-layout-end') ?>
