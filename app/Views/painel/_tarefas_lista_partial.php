<?php
$tarefas = isset($tarefas) && is_array($tarefas) ? $tarefas : [];
$prioridades = isset($prioridades) && is_array($prioridades) ? $prioridades : [];
$processoNome = isset($processoNome) ? (string) $processoNome : '';
$mensagem = isset($mensagem) ? (string) $mensagem : '';
$tituloLista = isset($tituloLista) ? (string) $tituloLista : 'Lista de Tarefas';
$rotuloNome = isset($rotuloNome) && trim((string) $rotuloNome) !== '' ? (string) $rotuloNome : 'Nome do arquivo';
$mostrarDimensaoDxf = !empty($mostrarDimensaoDxf);
$aba = isset($aba) ? (string) $aba : '';
$isAdm = $aba === 'lista_tarefas_adm';
$agrupadoPorProjeto = !empty($agrupadoPorProjeto);
$exibirControlesAdm = $isAdm;
$exibirAcoesAdm = $isAdm;
$isConcluidas = $aba === 'tarefas_concluidas';
$statusConcluidos = ['pronto', 'cortado', 'cortado_notfile', 'concluido', 'concluida', 'finalizado', 'finalizada'];
$totalConcluidas = 0;
foreach ($tarefas as $itemStatus) {
    $statusNormalizadoItem = strtolower((string) ($itemStatus['status_normalizado'] ?? $itemStatus['status'] ?? ''));
    if (in_array($statusNormalizadoItem, $statusConcluidos, true)) {
        $totalConcluidas++;
    }
}
$totalAtivas = max(0, count($tarefas) - $totalConcluidas);
$colunas = [];
if ($isConcluidas) {
        $colunas = [
            'Prioridade',
            'Desenhista',
            $rotuloNome,
            'Empresa/Cliente',
            'Empreendimento',
            'Finalidade',
            'Subpastas',
            'Status',
            'Data de Conclusao',
            'Data de Envio',
            'Acoes',
        ];
        if ($mostrarDimensaoDxf) {
            array_splice($colunas, 7, 0, ['Dimensao DXF']);
        }
} else {
    $colunas = [
        'Prioridade',
        'Ordem',
        'Desenhista',
        $rotuloNome,
        'Empresa/Cliente',
        'Empreendimento',
        'Finalidade',
        'Subpastas',
        'Status',
        'Data de Envio',
    ];
    if ($mostrarDimensaoDxf) {
        array_splice($colunas, 8, 0, ['Dimensao DXF']);
    }
}

if ($exibirAcoesAdm) {
    $colunas[] = 'Acao';
    $colunas[] = 'Prioridade';
    $colunas[] = 'Ordem';
}
$colunasAcoesAdm = $exibirAcoesAdm ? 3 : 0;
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h5 class="mb-0"><?= esc($tituloLista) ?><?= $processoNome !== '' ? ' - ' . esc($processoNome) : '' ?></h5>
    <span class="badge bg-primary-subtle text-primary fw-semibold">
        <?= count($tarefas) ?> item(ns) | Ativas: <?= $totalAtivas ?> | Concluidas: <?= $totalConcluidas ?>
    </span>
</div>

<?php if ($mensagem !== '') { ?>
    <div class="alert alert-warning mb-0" role="alert"><?= esc($mensagem) ?></div>
<?php } elseif (empty($tarefas)) { ?>
    <div class="alert alert-info mb-0" role="alert">Nenhuma tarefa encontrada para a lista selecionada.</div>
<?php } else { ?>
    <?php if ($exibirControlesAdm) { ?>
        <div class="d-flex flex-wrap justify-content-start align-items-center gap-2 mb-2">
            <button type="button" class="btn btn-sm btn-outline-primary" data-action="abrir_modal_lote" data-lote-modo="mudar_prioridade">
                <i class="ri-stack-line align-bottom me-1"></i>Mudar prioridade de varios
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" data-action="abrir_modal_lote" data-lote-modo="apagar">
                <i class="ri-delete-bin-line align-bottom me-1"></i>Apagar varios
            </button>
        </div>
    <?php } ?>

    <div class="table-responsive">
        <table id="painel-tarefas-table" class="table table-bordered table-striped table-hover table-nowrap align-middle mb-0 wl-tarefas-table<?= $isConcluidas ? ' wl-tarefas-concluidas-table' : '' ?><?= $agrupadoPorProjeto ? ' wl-processo-ind' : '' ?>">
            <thead>
                <tr>
                    <?php foreach ($colunas as $indice => $coluna) { ?>
                        <?php
                        $classe = '';
                        if ($exibirAcoesAdm && $indice >= count($colunas) - $colunasAcoesAdm) {
                            $classe = ' class="text-end text-nowrap wl-col-acoes"';
                        } elseif ($isConcluidas && $indice === count($colunas) - 1) {
                            $classe = ' class="text-end text-nowrap wl-col-acoes"';
                        }
                        ?>
                        <th<?= $classe ?>><?= esc($coluna) ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tarefas as $tarefa) { ?>
                    <?php
                    $desenhoId = (int) ($tarefa['desenho_id'] ?? $tarefa['id'] ?? 0);
                    $itemTipo = (string) ($tarefa['item_tipo'] ?? 'desenho');
                    $projetoId = (int) ($tarefa['projeto_id'] ?? 0);
                    $status = (string) ($tarefa['status'] ?? '');
                    $statusNormalizado = strtolower((string) ($tarefa['status_normalizado'] ?? $status));
                    $statusRequerAutorizacao = in_array($statusNormalizado, ['pronto', 'concluido', 'concluida'], true);
                    $arquivosVinculados = isset($tarefa['arquivos_vinculados']) && is_array($tarefa['arquivos_vinculados'])
                        ? array_values(array_filter($tarefa['arquivos_vinculados'], static fn ($arquivo) => trim((string) $arquivo) !== ''))
                        : [];
                    $arquivosResumo = array_slice($arquivosVinculados, 0, 4);
                    $arquivosRestantes = max(0, count($arquivosVinculados) - count($arquivosResumo));
                    $empreendimentoNome = (string) ($tarefa['empreendimento_nome'] ?? '');
                    $empreendimentoEscala = (string) ($tarefa['empreendimento_escala'] ?? '');
                    $empreendimentoTitulo = $empreendimentoNome;
                    if ($empreendimentoEscala !== '') {
                        $empreendimentoTitulo .= ($empreendimentoTitulo !== '' ? ' - ' : '') . 'Escala ' . $empreendimentoEscala;
                    }
                    ?>
                    <tr>
                        <td
                            bgcolor="<?= esc($tarefa['prioridade_cor'] ?? '#cbd5e1') ?>"
                            style="background-color: <?= esc($tarefa['prioridade_cor'] ?? '#cbd5e1') ?> !important; color: <?= esc($tarefa['prioridade_texto'] ?? '#0f172a') ?> !important;"
                            class="text-center">
                            <span class="marca_texto" style="color: <?= esc($tarefa['prioridade_texto'] ?? '#0f172a') ?> !important;">
                                <?= esc($tarefa['prioridade_nome'] ?? '-') ?>
                            </span>
                        </td>
                        <?php if (!$isConcluidas) { ?>
                            <td class="text-center"><?= esc((string) ($tarefa['ordem'] ?? '')) ?></td>
                        <?php } ?>
                        <td><span class="wl-cell-truncate" title="<?= esc($tarefa['desenhista_nome'] ?? '') ?>"><?= esc($tarefa['desenhista_nome'] ?? '') ?></span></td>
                        <td>
                            <span class="wl-cell-truncate" title="<?= esc($tarefa['nome_arquivo'] ?? '') ?>"><?= esc($tarefa['nome_arquivo'] ?? '') ?></span>
                            <?php if ($isConcluidas && $arquivosVinculados !== []) { ?>
                                <div class="text-muted small wl-linked-files" title="<?= esc(implode(', ', $arquivosVinculados)) ?>">
                                    <?= (int) ($tarefa['arquivos_count'] ?? count($arquivosVinculados)) ?> arquivo(s):
                                    <?= esc(implode(', ', $arquivosResumo)) ?><?= $arquivosRestantes > 0 ? ' +' . $arquivosRestantes : '' ?>
                                </div>
                            <?php } ?>
                        </td>
                        <td><span class="wl-cell-truncate" title="<?= esc($tarefa['empresa_nome'] ?? '') ?>"><?= esc($tarefa['empresa_nome'] ?? '') ?></span></td>
                        <td>
                            <span class="wl-cell-truncate" title="<?= esc($empreendimentoTitulo) ?>"><?= esc($empreendimentoNome) ?></span>
                            <?php if ($empreendimentoEscala !== '') { ?>
                                <div class="text-muted small">Escala <?= esc($empreendimentoEscala) ?></div>
                            <?php } ?>
                        </td>
                        <td><span class="wl-cell-truncate" title="<?= esc($tarefa['finalidade_nome'] ?? '') ?>"><?= esc($tarefa['finalidade_nome'] ?? '') ?></span></td>
                        <td><span class="wl-cell-truncate" title="<?= esc($tarefa['subpastas'] ?? '') ?>"><?= esc($tarefa['subpastas'] ?? '') ?></span></td>
                        <?php if ($mostrarDimensaoDxf) { ?>
                            <td><span class="wl-cell-truncate" title="<?= esc($tarefa['dimensao_dxf'] ?? '') ?>"><?= esc($tarefa['dimensao_dxf'] ?? '') ?></span></td>
                        <?php } ?>
                        <td><span class="wl-cell-truncate" title="<?= esc($status) ?>"><?= esc($status !== '' ? $status : '-') ?></span></td>
                        <?php if ($isConcluidas) { ?>
                            <td><?= esc($tarefa['data_conclusao'] ?? '') ?></td>
                        <?php } ?>
                        <td><?= esc($tarefa['data_envio'] ?? '') ?></td>

                        <?php if ($isConcluidas) { ?>
                            <?php $recolocarPendente = !empty($tarefa['recolocar_pendente']); ?>
                            <td class="text-end text-nowrap wl-col-acoes">
                                <div class="wl-row-actions wl-row-actions--with-menu">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-info"
                                        data-action="visualizar_concluida"
                                        data-desenho-id="<?= $desenhoId ?>">
                                        Visualizar
                                    </button>
                                    <?php if ($statusRequerAutorizacao) { ?>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-success"
                                            data-action="abrir_modal_autorizar_conclusao"
                                            data-desenho-id="<?= $desenhoId ?>">
                                            Autorizar conclusao
                                        </button>
                                    <?php } else { ?>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
                                            Conclusao autorizada
                                        </button>
                                    <?php } ?>
                                    <?php if ($recolocarPendente) { ?>
                                        <button type="button" class="btn btn-sm btn-outline-warning" disabled>
                                            Aguardando avaliacao
                                        </button>
                                    <?php } else { ?>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-action="solicitar_recolocar"
                                            data-desenho-id="<?= $desenhoId ?>">
                                            Solicitar recolocar
                                        </button>
                                    <?php } ?>
                                </div>
                            </td>
                        <?php } ?>

                        <?php if ($exibirAcoesAdm) { ?>
                            <td class="text-end text-nowrap wl-col-acoes">
                                <div class="wl-row-actions wl-row-actions--confirm">
                                    <?php if ($statusNormalizado === 'pendente') { ?>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            data-action="apagar"
                                            data-desenho-id="<?= $desenhoId ?>"
                                            data-item-tipo="<?= esc($itemTipo) ?>"
                                            data-projeto-id="<?= $projetoId ?>">
                                            Apagar
                                        </button>
                                    <?php } elseif ($statusNormalizado === 'cortando') { ?>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-warning"
                                            data-action="cancelar_corte"
                                            data-desenho-id="<?= $desenhoId ?>"
                                            data-item-tipo="<?= esc($itemTipo) ?>"
                                            data-projeto-id="<?= $projetoId ?>">
                                            Cancelar corte
                                        </button>
                                    <?php } else { ?>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
                                            Sem acao
                                        </button>
                                    <?php } ?>
                                </div>
                            </td>
                            <td class="text-end text-nowrap wl-col-acoes">
                                <div class="wl-row-actions wl-row-actions--with-menu">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-action="abrir_modal_item"
                                        data-desenho-id="<?= $desenhoId ?>"
                                        data-item-tipo="<?= esc($itemTipo) ?>"
                                        data-projeto-id="<?= $projetoId ?>">
                                        Modal
                                    </button>
                                    <?php if (!empty($prioridades)) { ?>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm btn-outline-secondary wl-row-action-main dropdown-toggle" data-bs-toggle="dropdown" data-toggle="dropdown" aria-expanded="false">
                                                Mudar prioridade
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <?php foreach ($prioridades as $prioridade) { ?>
                                                    <li>
                                                        <button
                                                            type="button"
                                                            class="dropdown-item"
                                                            data-action="mudar_prioridade"
                                                            data-desenho-id="<?= $desenhoId ?>"
                                                            data-item-tipo="<?= esc($itemTipo) ?>"
                                                            data-projeto-id="<?= $projetoId ?>"
                                                            data-prioridade-id="<?= (int) ($prioridade['id'] ?? 0) ?>">
                                                            <span class="badge rounded-pill me-1" style="background: <?= esc($prioridade['cor'] ?? '#cbd5e1') ?>;">&nbsp;</span>
                                                            <?= esc($prioridade['nome'] ?? '') ?>
                                                        </button>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    <?php } else { ?>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
                                            Sem prioridades
                                        </button>
                                    <?php } ?>
                                </div>
                            </td>
                            <td class="text-end text-nowrap wl-col-acoes">
                                <div class="wl-row-actions wl-row-actions--with-menu">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        data-action="mover_ordem"
                                        data-direcao="up"
                                        data-desenho-id="<?= $desenhoId ?>"
                                        data-item-tipo="<?= esc($itemTipo) ?>"
                                        data-projeto-id="<?= $projetoId ?>"
                                        title="Subir na ordem">
                                        <i class="ri-arrow-up-s-line"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        data-action="mover_ordem"
                                        data-direcao="down"
                                        data-desenho-id="<?= $desenhoId ?>"
                                        data-item-tipo="<?= esc($itemTipo) ?>"
                                        data-projeto-id="<?= $projetoId ?>"
                                        title="Descer na ordem">
                                        <i class="ri-arrow-down-s-line"></i>
                                    </button>
                                </div>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <?php foreach ($colunas as $indice => $coluna) { ?>
                        <?php
                        $classe = '';
                        if ($exibirAcoesAdm && $indice >= count($colunas) - $colunasAcoesAdm) {
                            $classe = ' class="text-end text-nowrap wl-col-acoes"';
                        } elseif ($isConcluidas && $indice === count($colunas) - 1) {
                            $classe = ' class="text-end text-nowrap wl-col-acoes"';
                        }
                        ?>
                        <th<?= $classe ?>><?= esc($coluna) ?></th>
                    <?php } ?>
                </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
