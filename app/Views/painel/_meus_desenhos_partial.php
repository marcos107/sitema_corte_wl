<?php
$desenhos = isset($desenhos) && is_array($desenhos) ? $desenhos : [];
$processoNome = isset($processoNome) ? (string) $processoNome : '';
$mensagem = isset($mensagem) ? (string) $mensagem : '';
$rotuloNome = isset($rotuloNome) && trim((string) $rotuloNome) !== '' ? (string) $rotuloNome : 'Nome do arquivo';
$statusConcluidos = ['pronto', 'cortado', 'cortado_notfile', 'concluido', 'concluida', 'finalizado', 'finalizada'];
$totalConcluidas = 0;
foreach ($desenhos as $itemStatus) {
    $statusNormalizadoItem = strtolower((string) ($itemStatus['status_normalizado'] ?? $itemStatus['status'] ?? ''));
    if (in_array($statusNormalizadoItem, $statusConcluidos, true)) {
        $totalConcluidas++;
    }
}
$totalAtivas = max(0, count($desenhos) - $totalConcluidas);
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h5 class="mb-0">Meus Desenhos<?= $processoNome !== '' ? ' - ' . esc($processoNome) : '' ?></h5>
    <span class="badge bg-primary-subtle text-primary fw-semibold">
        <?= count($desenhos) ?> item(ns) | Ativas: <?= $totalAtivas ?> | Concluidas: <?= $totalConcluidas ?>
    </span>
</div>

<?php if ($mensagem !== '') { ?>
    <div class="alert alert-warning mb-3" role="alert"><?= esc($mensagem) ?></div>
<?php } ?>

<?php if (empty($desenhos)) { ?>
    <div class="alert alert-info mb-0" role="alert">Nenhum desenho encontrado para a lista selecionada.</div>
<?php } else { ?>
    <div class="table-responsive">
        <table id="painel-tarefas-table" class="table table-bordered table-striped table-hover table-nowrap align-middle mb-0 wl-tarefas-table">
            <thead>
                <tr>
                    <th>Prioridade</th>
                    <th>Desenhista</th>
                    <th><?= esc($rotuloNome) ?></th>
                    <th>Empresa/Cliente</th>
                    <th>Empreendimento</th>
                    <th>Finalidade</th>
                    <th>Subpastas</th>
                    <th>Data de envio</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($desenhos as $desenho) { ?>
                    <?php
                    $statusTexto = (string) ($desenho['status'] ?? '');
                    ?>
                    <tr>
                        <td
                            bgcolor="<?= esc($desenho['prioridade_cor'] ?? '#cbd5e1') ?>"
                            style="background-color: <?= esc($desenho['prioridade_cor'] ?? '#cbd5e1') ?> !important; color: <?= esc($desenho['prioridade_texto'] ?? '#0f172a') ?> !important;"
                            class="text-center">
                            <span class="marca_texto" style="color: <?= esc($desenho['prioridade_texto'] ?? '#0f172a') ?> !important;">
                                <?= esc($desenho['prioridade_nome'] ?? '-') ?>
                            </span>
                        </td>
                        <td><span class="wl-cell-truncate" title="<?= esc($desenho['desenhista_nome'] ?? '') ?>"><?= esc($desenho['desenhista_nome'] ?? '') ?></span></td>
                        <td>
                            <span class="wl-cell-truncate" title="<?= esc($desenho['nome_arquivo'] ?? '') ?>"><?= esc($desenho['nome_arquivo'] ?? '') ?></span>
                            <?php if ($statusTexto !== '') { ?>
                                <div class="small text-muted mt-1">Status: <?= esc($statusTexto) ?></div>
                            <?php } ?>
                        </td>
                        <td><span class="wl-cell-truncate" title="<?= esc($desenho['empresa_nome'] ?? '') ?>"><?= esc($desenho['empresa_nome'] ?? '') ?></span></td>
                        <td><span class="wl-cell-truncate" title="<?= esc($desenho['empreendimento_nome'] ?? '') ?>"><?= esc($desenho['empreendimento_nome'] ?? '') ?></span></td>
                        <td><span class="wl-cell-truncate" title="<?= esc($desenho['finalidade_nome'] ?? '') ?>"><?= esc($desenho['finalidade_nome'] ?? '') ?></span></td>
                        <td>
                            <span class="wl-cell-truncate" title="<?= esc($desenho['subpastas'] ?? '') ?>">
                                <?= esc($desenho['subpastas'] ?? '') ?>
                            </span>
                        </td>
                        <td><?= esc($desenho['data_envio'] ?? '') ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Prioridade</th>
                    <th>Desenhista</th>
                    <th><?= esc($rotuloNome) ?></th>
                    <th>Empresa/Cliente</th>
                    <th>Empreendimento</th>
                    <th>Finalidade</th>
                    <th>Subpastas</th>
                    <th>Data de envio</th>
                </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
