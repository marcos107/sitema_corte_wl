<?php

namespace App\Controllers;
use Config\Services;
use App\Libraries\DxfDimensoes;

use App\Controllers\Ferramentas;
use Config\App;
use Mpdf\Mpdf;
use Endroid\QrCode\QrCode;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Encoding\Encoding;

class ListaCortePost extends Ferramentas
{
    private function iniciarSessaoSeNecessario()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    private function sincronizarOrdensComThrottle($intervaloSegundos = 20, ?int $processoId = null, string $tipoProcesso = 'mult')
    {
        $this->iniciarSessaoSeNecessario();
        $processoId = (int) ($processoId ?? 0);
        $tipoProcesso = strtolower(trim($tipoProcesso));

        $agora = time();
        $chaveSessao = 'wl_ordens_sync_last_' . ($processoId > 0 ? $processoId : 'geral') . '_' . ($tipoProcesso !== '' ? $tipoProcesso : 'mult');
        $ultimoSync = intval($_SESSION[$chaveSessao] ?? 0);
        if (($agora - $ultimoSync) < intval($intervaloSegundos)) {
            return;
        }

        if ($tipoProcesso === 'ind' && $processoId > 0) {
            $this->sincronizarOrdensProjetosDoProcessoLista($processoId);
            $_SESSION[$chaveSessao] = $agora;
            return;
        }

        if ($processoId > 0 || $tipoProcesso !== 'ind') {
            Ferramentas::ordenarOrdems($processoId > 0 ? $processoId : null);
            $_SESSION[$chaveSessao] = $agora;
        }
    }

    private function extrairTagsDeDiretorio($diretorio)
    {
        if (!is_string($diretorio) || $diretorio === '') {
            return '';
        }

        $tags = explode('/', $diretorio);
        $tags = array_slice($tags, 6);

        if (!empty($tags)) {
            unset($tags[count($tags) - 1]);
        }

        return implode(' - ', $tags);
    }

    private function normalizarStatusTexto($status)
    {
        $status = (string)$status;
        $decodificado = Ferramentas::decodificador($status);
        return $decodificado !== '' ? $decodificado : $status;
    }

    private function enriquecerDesenhosComDimensoesDxf(array $desenhos): array
    {
        if (empty($desenhos)) {
            return $desenhos;
        }

        static $dxfDimensoes = null;
        if (!$dxfDimensoes instanceof DxfDimensoes) {
            $dxfDimensoes = new DxfDimensoes();
        }

        return $dxfDimensoes->enriquecerDesenhos($desenhos);
    }

    private function buscarProcessoAtivoPorNome($nomeProcesso)
    {
        if (!$nomeProcesso) {
            return null;
        }

        return (new \App\Models\Processos())
            ->where('nome', $nomeProcesso)
            ->where('status', 'ativo')
            ->first();
    }

    private function respostaListaTarefasJsonOtimizada(): array
    {
        $inicioTotal = microtime(true);
        $queriesInicio = $this->contarQueriesRegistradasListaTarefas();
        $processoNome = trim((string) service('request')->getPost('processo'));
        $finalidadePesquisa = trim((string) service('request')->getPost('finalidade'));
        if ($finalidadePesquisa === '-1') {
            $finalidadePesquisa = '';
        }

        $model = new \App\Models\TarefasFilaModel();
        $proc = $model->processoPorNome($processoNome);
        $som = (string) (new \App\Models\Alteracoes())->latestDetailValueByItem('som_corte', '', 'false');
        if ($som === '') {
            $som = 'false';
        }

        if (!$proc) {
            return [
                'draw' => (int) service('request')->getPost('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'lista' => [],
                'status' => 'pendente',
                'som' => $som,
                'tipo_processo' => '',
                'mostrar_dimensao_dxf' => false,
                'itens_notificacao' => [],
                'finalidade_pesquisa' => $finalidadePesquisa,
            ];
        }

        $this->iniciarSessaoSeNecessario();
        $processoId = (int) ($proc['id'] ?? 0);
        $tipoProcesso = strtolower((string) ($proc['input'] ?? ''));
        $inicioSync = microtime(true);
        $this->sincronizarOrdensComThrottle(20, $processoId, $tipoProcesso);
        $syncMs = (microtime(true) - $inicioSync) * 1000;

        $searchPost = service('request')->getPost('search');
        $searchValue = is_array($searchPost)
            ? (string) ($searchPost['value'] ?? '')
            : (string) ($searchPost ?? '');

        $resposta = $model->listarPorProcesso($proc, [
            'draw' => (int) service('request')->getPost('draw'),
            'offset' => (int) service('request')->getPost('start'),
            'limit' => (int) (service('request')->getPost('length') ?: 50),
            'search' => $searchValue,
            'finalidade' => $finalidadePesquisa,
            'usuario_id' => (int) ($_SESSION['usuario'] ?? 0),
            'mostrar_dimensao_dxf' => $this->processoExibeDimensaoDxf((string) ($proc['nome'] ?? $processoNome)),
            'agrupar_projetos' => $tipoProcesso === 'ind',
            'sem_limite' => (string) service('request')->getPost('html_lista') === '1',
        ]);

        $inicioSessao = microtime(true);
        $this->atualizarSessaoListaTarefasJson($resposta, $proc, $model);
        $sessaoMs = (microtime(true) - $inicioSessao) * 1000;

        $possuiCorteUsuario = false;
        foreach ($resposta['data'] ?? [] as $row) {
            if (!empty($row['eh_corte_usuario'])) {
                $possuiCorteUsuario = true;
                break;
            }
        }

        $resposta['status'] = $possuiCorteUsuario ? 'cortando' : 'pendente';
        $resposta['som'] = $som;
        $resposta['finalidade_pesquisa'] = $finalidadePesquisa;
        $resposta['ordens_sincronizadas'] = true;

        $inicioHtml = microtime(true);
        if ((string) service('request')->getPost('html_lista') === '1') {
            $resposta['lista'] = $this->montarHtmlListaTarefasOtimizada($resposta);
        }
        $htmlMs = (microtime(true) - $inicioHtml) * 1000;

        $resposta['performance'] = array_merge(is_array($resposta['performance'] ?? null) ? $resposta['performance'] : [], [
            'total_ms' => round((microtime(true) - $inicioTotal) * 1000, 2),
            'sync_ms' => round($syncMs, 2),
            'html_ms' => round($htmlMs, 2),
            'session_ms' => round($sessaoMs, 2),
            'queries' => $this->contarQueriesExecutadasDesdeListaTarefas($queriesInicio),
        ]);

        $this->logarPerformanceListaTarefasBackend('lista_tarefas', [
            'processo_id' => $processoId,
            'processo' => (string) ($proc['nome'] ?? $processoNome),
            'tipo' => $tipoProcesso,
            'registros' => count($resposta['data'] ?? []),
            'query_ms' => (float) ($resposta['performance']['query_ms'] ?? 0),
            'sync_ms' => $syncMs,
            'html_ms' => $htmlMs,
            'session_ms' => $sessaoMs,
            'total_ms' => (float) ($resposta['performance']['total_ms'] ?? 0),
            'queries' => $resposta['performance']['queries'],
        ]);

        return $resposta;
    }

    private function montarHtmlListaTarefasOtimizada(array $resposta): string
    {
        $tipoProcesso = strtolower((string) ($resposta['tipo_processo'] ?? ''));
        $mostrarDimensaoDxf = !empty($resposta['mostrar_dimensao_dxf']) && $tipoProcesso !== 'ind';
        $possuiCorteAtivo = strtolower((string) ($resposta['status'] ?? '')) === 'cortando';
        $html = [];

        foreach (($resposta['data'] ?? []) as $row) {
            if (!is_array($row)) {
                continue;
            }

            $indice = (int) ($row['indice'] ?? 0);
            $statusNormalizado = strtolower((string) ($row['status_normalizado'] ?? $row['status'] ?? ''));
            $ehCorteUsuario = !empty($row['eh_corte_usuario']);
            $rotuloConfirmar = ($mostrarDimensaoDxf || $tipoProcesso === 'ind') ? 'Finalizar' : 'Confirmar Corte';
            $botaoAcao = '';
            $botaoConfirmar = '';

            if ($tipoProcesso === 'ind') {
                if ($statusNormalizado === 'processando') {
                    $botaoAcao = '<div class="wl-row-actions"><button type="button" disabled class="btn btn-sm btn-outline-dark wl-row-action-main">Processando...</button></div>';
                    $botaoConfirmar = '<div class="wl-row-actions wl-row-actions--confirm"><button type="button" disabled class="btn btn-sm btn-outline-dark">Finalizar</button></div>';
                } else {
                    $rotuloBaixarProjeto = htmlspecialchars($this->rotuloBotaoBaixarProjeto($row['arquivos_count'] ?? 0, $row['arquivos_baixados_count'] ?? 0), ENT_QUOTES, 'UTF-8');
                    $botaoAcao = '<div class="wl-row-actions"><button type="button" onclick="baixar(' . $indice . ')" class="btn btn-sm btn-primary wl-row-action-main">' . $rotuloBaixarProjeto . '</button></div>';
                    $botaoConfirmar = '<div class="wl-row-actions wl-row-actions--confirm"><button name="cadastarar" type="button" onclick="confirmar_ind(\'' . $indice . '\',\'\')" class="btn btn-sm btn-success">Finalizar</button></div>';
                }
            } elseif ($statusNormalizado === 'processando') {
                if ($mostrarDimensaoDxf) {
                    $botaoAcao = $this->montarAcoesCorteLaser(
                        $indice,
                        '<button type="button" disabled class="btn btn-sm btn-outline-dark wl-row-action-main">Processando...</button>'
                    );
                } else {
                    $botaoAcao = '<div class="wl-row-actions"><button type="button" disabled class="btn btn-sm btn-outline-dark wl-row-action-main">Processando...</button></div>';
                }
                $botaoConfirmar = '<div class="wl-row-actions wl-row-actions--confirm"><button type="button" disabled class="btn btn-sm btn-outline-dark">' . $rotuloConfirmar . '</button></div>';
            } elseif ($statusNormalizado === 'cortando') {
                if ($ehCorteUsuario) {
                    if ($mostrarDimensaoDxf) {
                        $botaoAcao = $this->montarAcoesCorteLaser(
                            $indice,
                            '<button type="button" onclick="mostrar_caminho_corte_atual(' . $indice . ')" class="btn btn-sm btn-outline-primary wl-row-action-main">Em corte</button>',
                            true,
                            [[
                                'rotulo' => 'Cancelar corte',
                                'onclick' => 'cancelar_corte(' . $indice . ', ' . json_encode((string) ($row['nome_arquivo'] ?? ''), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ')',
                                'icone' => 'ri-close-circle-line',
                                'classe' => 'text-danger',
                            ]]
                        );
                    } else {
                        $botaoAcao = '<div class="wl-row-actions">'
                            . '<button type="button" onclick="ver_dxf(' . $indice . ')" class="btn btn-sm btn-outline-info wl-row-action-main">Ver</button>'
                            . '<button type="button" onclick="buscarArquivos(' . $indice . ')" class="btn btn-sm btn-outline-primary">Baixar</button>'
                            . '</div>';
                    }
                    $botaoConfirmar = '<div class="wl-row-actions wl-row-actions--confirm"><button name="cadastarar" type="button" onclick="confirmar(\'' . $indice . '\',' . htmlspecialchars(json_encode((string) ($row['nome_arquivo'] ?? ''), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') . ')" class="btn btn-sm btn-success">' . $rotuloConfirmar . '</button></div>';
                } else {
                    if ($mostrarDimensaoDxf) {
                        $botaoAcao = $this->montarAcoesCorteLaser(
                            $indice,
                            '<button type="button" disabled class="btn btn-sm btn-outline-dark wl-row-action-main">Em corte</button>'
                        );
                    } else {
                        $botaoAcao = '<div class="wl-row-actions"><button type="button" disabled class="btn btn-sm btn-outline-dark wl-row-action-main">Processando...</button></div>';
                    }
                    $botaoConfirmar = '<div class="wl-row-actions wl-row-actions--confirm"><button type="button" disabled class="btn btn-sm btn-outline-dark">' . $rotuloConfirmar . '</button></div>';
                }
            } elseif ($possuiCorteAtivo) {
                if ($mostrarDimensaoDxf) {
                    $botaoAcao = $this->montarAcoesCorteLaser(
                        $indice,
                        '<button type="button" disabled class="btn btn-sm btn-outline-dark wl-row-action-main">Inicializar</button>'
                    );
                } else {
                    $botaoAcao = '<div class="wl-row-actions"><button type="button" disabled class="btn btn-sm btn-outline-dark wl-row-action-main">Cortar</button></div>';
                }
                $botaoConfirmar = '<div class="wl-row-actions wl-row-actions--confirm"><button type="button" disabled class="btn btn-sm btn-outline-dark">' . $rotuloConfirmar . '</button></div>';
            } else {
                if ($mostrarDimensaoDxf) {
                    $botaoAcao = $this->montarAcoesCorteLaser(
                        $indice,
                        '<button type="button" onclick="cortar(' . $indice . ')" class="btn btn-sm btn-primary wl-row-action-main">Inicializar</button>'
                    );
                } else {
                    $botaoAcao = '<div class="wl-row-actions"><button type="button" onclick="cortar(' . $indice . ')" class="btn btn-sm btn-primary wl-row-action-main">Cortar</button></div>';
                }
                $botaoConfirmar = '<div class="wl-row-actions wl-row-actions--confirm"><button name="cadastarar" type="button" onclick="confirmar(\'' . $indice . '\',' . htmlspecialchars(json_encode((string) ($row['nome_arquivo'] ?? ''), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') . ')" class="btn btn-sm btn-success">' . $rotuloConfirmar . '</button></div>';
            }

            $prioridadeCor = htmlspecialchars((string) ($row['prioridade_cor'] ?? '#cbd5e1'), ENT_QUOTES, 'UTF-8');
            $prioridadeTexto = htmlspecialchars((string) ($row['prioridade_texto'] ?? '#0f172a'), ENT_QUOTES, 'UTF-8');
            $prioridadeNome = htmlspecialchars((string) ($row['prioridade_nome'] ?? '-'), ENT_QUOTES, 'UTF-8');
            $desenhista = htmlspecialchars((string) ($row['desenhista_nome'] ?? ''), ENT_QUOTES, 'UTF-8');
            $nomeArquivo = htmlspecialchars((string) ($row['nome_arquivo'] ?? ''), ENT_QUOTES, 'UTF-8');
            $empresa = htmlspecialchars((string) ($row['empresa_nome'] ?? ''), ENT_QUOTES, 'UTF-8');
            $empreendimento = htmlspecialchars((string) ($row['empreendimento_nome'] ?? ''), ENT_QUOTES, 'UTF-8');
            $empreendimentoEscala = htmlspecialchars((string) ($row['empreendimento_escala'] ?? ''), ENT_QUOTES, 'UTF-8');
            $empreendimentoTitulo = $empreendimento . ($empreendimentoEscala !== '' ? ' - Escala ' . $empreendimentoEscala : '');
            $finalidade = htmlspecialchars((string) ($row['finalidade_nome'] ?? ''), ENT_QUOTES, 'UTF-8');
            $subpastas = htmlspecialchars((string) ($row['subpastas'] ?? ''), ENT_QUOTES, 'UTF-8');
            $status = htmlspecialchars((string) (($row['status'] ?? '') !== '' ? $row['status'] : '-'), ENT_QUOTES, 'UTF-8');
            $dataEnvio = htmlspecialchars((string) ($row['data_envio'] ?? ''), ENT_QUOTES, 'UTF-8');
            $ordem = htmlspecialchars((string) ($row['ordem'] ?? ''), ENT_QUOTES, 'UTF-8');

            $colunaExtra = $mostrarDimensaoDxf
                ? '<td class="text-center text-nowrap wl-col-dimensao-dxf"><span class="wl-cell-truncate" title="' . htmlspecialchars((string) ($row['dimensao_dxf'] ?? '-'), ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars((string) ($row['dimensao_dxf'] ?? '-'), ENT_QUOTES, 'UTF-8') . '</span></td>'
                : '<td><span class="wl-cell-truncate" title="' . $status . '">' . $status . '</span></td>';

            $html[] = '<tr>'
                . '<td bgcolor="' . $prioridadeCor . '" style="background-color: ' . $prioridadeCor . ' !important; color: ' . $prioridadeTexto . ' !important;" class="text-center"><span class="marca_texto" style="color: ' . $prioridadeTexto . ' !important;">' . $prioridadeNome . '</span></td>'
                . '<td class="text-center">' . $ordem . '</td>'
                . '<td><span class="wl-cell-truncate" title="' . $desenhista . '">' . $desenhista . '</span></td>'
                . '<td><span class="wl-cell-truncate" title="' . $nomeArquivo . '">' . $nomeArquivo . '</span></td>'
                . '<td><span class="wl-cell-truncate" title="' . $empresa . '">' . $empresa . '</span></td>'
                . '<td><span class="wl-cell-truncate" title="' . $empreendimentoTitulo . '">' . $empreendimento . '</span>' . ($empreendimentoEscala !== '' ? '<div class="text-muted small">Escala ' . $empreendimentoEscala . '</div>' : '') . '</td>'
                . '<td><span class="wl-cell-truncate" title="' . $finalidade . '">' . $finalidade . '</span></td>'
                . '<td><span class="wl-cell-truncate" title="' . $subpastas . '">' . $subpastas . '</span></td>'
                . $colunaExtra
                . '<td class="text-center">' . $dataEnvio . '</td>'
                . '<td class="text-end text-nowrap wl-col-acoes">' . $botaoAcao . '</td>'
                . '<td class="text-end text-nowrap wl-col-acoes">' . $botaoConfirmar . '</td>'
                . '</tr>';
        }

        return implode('', $html);
    }

    private function contarQueriesRegistradasListaTarefas(): ?int
    {
        $db = \Config\Database::connect();
        return method_exists($db, 'getQueries') ? count($db->getQueries()) : null;
    }

    private function contarQueriesExecutadasDesdeListaTarefas(?int $inicio): ?int
    {
        $fim = $this->contarQueriesRegistradasListaTarefas();
        if ($inicio === null || $fim === null) {
            return null;
        }

        return max(0, $fim - $inicio);
    }

    private function logarPerformanceListaTarefasBackend(string $origem, array $dados): void
    {
        $pontos = [];
        if ((float) ($dados['sync_ms'] ?? 0) > 80) {
            $pontos[] = 'sync_ordem';
        }
        if ((float) ($dados['query_ms'] ?? 0) > 120) {
            $pontos[] = 'query_principal';
        }
        if ((float) ($dados['html_ms'] ?? 0) > 120) {
            $pontos[] = 'montagem_html';
        }
        if ((float) ($dados['session_ms'] ?? 0) > 80) {
            $pontos[] = 'montagem_sessao';
        }

        log_message(
            'info',
            sprintf(
                '[perf:%s] processo_id=%s processo="%s" tipo=%s registros=%d query_ms=%.2f html_ms=%.2f session_ms=%.2f sync_ms=%.2f total_ms=%.2f queries=%s lentos=%s',
                $origem,
                (string) ($dados['processo_id'] ?? ''),
                (string) ($dados['processo'] ?? ''),
                (string) ($dados['tipo'] ?? ''),
                (int) ($dados['registros'] ?? 0),
                (float) ($dados['query_ms'] ?? 0),
                (float) ($dados['html_ms'] ?? 0),
                (float) ($dados['session_ms'] ?? 0),
                (float) ($dados['sync_ms'] ?? 0),
                (float) ($dados['total_ms'] ?? 0),
                ($dados['queries'] ?? null) === null ? 'n/a' : (string) $dados['queries'],
                $pontos === [] ? 'nenhum' : implode(',', $pontos)
            )
        );
    }

    private function atualizarSessaoListaTarefasJson(array $resposta, array $proc, \App\Models\TarefasFilaModel $model): void
    {
        $this->iniciarSessaoSeNecessario();

        $listaIds = [];
        $listaCompleta = [];
        $listaProjetos = [];
        $_SESSION["baixar_arquivo_tudo"] = [];
        $_SESSION["baixar_arquivo"] = [];

        $projetoIds = [];
        foreach (($resposta['data'] ?? []) as $row) {
            if (($row['item_tipo'] ?? '') === 'projeto' && (int) ($row['projeto_id'] ?? 0) > 0) {
                $projetoIds[] = (int) $row['projeto_id'];
            }
        }
        $itensProjetos = $model->buscarItensProjetos($projetoIds);

        foreach (($resposta['data'] ?? []) as $row) {
            $indice = (int) ($row['indice'] ?? 0);
            $desenhoId = (int) ($row['desenho_id'] ?? $row['id'] ?? 0);
            $itemSessao = [
                'id' => $indice,
                'desenho_id' => $desenhoId,
                'projeto_id' => (int) ($row['projeto_id'] ?? 0),
                'processos_id' => (int) ($proc['id'] ?? 0),
                'prioridade_id' => (int) ($row['prioridade_id'] ?? 0),
                'corte_id' => (int) ($row['corte_id'] ?? 0),
                'nome' => (string) ($row['nome_original'] ?? $row['nome_arquivo'] ?? ''),
                'nome_arquivo' => (string) ($row['nome_arquivo'] ?? ''),
                'diretorio' => (string) ($row['diretorio'] ?? ''),
                'status' => (string) ($row['status'] ?? ''),
                'data_add' => (string) ($row['data_envio'] ?? ''),
                'prioridade_nome' => (string) ($row['prioridade_nome'] ?? ''),
                'prioridade_cor' => (string) ($row['prioridade_cor'] ?? ''),
                'empresa_nome' => (string) ($row['empresa_nome'] ?? ''),
                'empreendimento_nome' => (string) ($row['empreendimento_nome'] ?? ''),
                'empreendimento_escala' => (string) ($row['empreendimento_escala'] ?? ''),
                'finalidade_nome' => (string) ($row['finalidade_nome'] ?? ''),
                'usuario_nome' => (string) ($row['desenhista_nome'] ?? ''),
                'tags' => (string) ($row['subpastas'] ?? ''),
                'item_tipo' => (string) ($row['item_tipo'] ?? 'desenho'),
            ];

            $listaIds[$indice] = $desenhoId;
            $listaCompleta[$indice] = $itemSessao;
            $_SESSION["baixar_arquivo_tudo"][$indice] = $itemSessao;
            $_SESSION["baixar_arquivo"][$indice] = (string) ($row['diretorio'] ?? '');

            if (($row['item_tipo'] ?? '') === 'projeto') {
                $projetoId = (int) ($row['projeto_id'] ?? 0);
                $listaProjetos[$indice] = $itensProjetos[$projetoId] ?? [];
            }
        }

        if (strtolower((string) ($proc['input'] ?? '')) === 'ind') {
            $listaCompleta['tipo'] = 'ind';
            $listaProjetos['processos_id'] = (int) ($proc['id'] ?? 0);
        }

        $_SESSION['lista'] = $listaIds;
        $_SESSION['lista_completa'] = $listaCompleta;
        $_SESSION['lista_primordial'] = $listaCompleta;
        $_SESSION['lista_projetos'] = $listaProjetos;
        $_SESSION['projeto_todos'] = $listaProjetos;
    }

    private function normalizarNomeProcessoToken($nomeProcesso)
    {
        $candidatos = array_unique(array_filter([
            (string) $nomeProcesso,
            Ferramentas::decodificador((string) $nomeProcesso),
        ], static function ($valor) {
            return trim((string) $valor) !== '';
        }));

        foreach ($candidatos as $candidato) {
            $normalizado = trim((string) $candidato);
            $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $normalizado);

            if ($ascii !== false && $ascii !== '') {
                $normalizado = $ascii;
            }

            $normalizado = preg_replace('/[^a-z0-9]+/i', '_', $normalizado);
            $normalizado = trim((string) $normalizado, '_');

            if ($normalizado !== '') {
                return strtoupper($normalizado);
            }
        }

        return '';
    }

    private function processoIgnoraBloqueiosOperacionais($nomeProcesso)
    {
        return $this->normalizarNomeProcessoToken($nomeProcesso) === 'ARTE_FINAL';
    }

    private function processoExibeDimensaoDxf($nomeProcesso): bool
    {
        return $this->normalizarNomeProcessoToken($nomeProcesso) === 'CORTE_LASER';
    }

    private function rotuloBotaoBaixarProjeto($totalArquivos, $arquivosBaixados): string
    {
        $total = max(0, (int) $totalArquivos);
        $baixados = max(0, (int) $arquivosBaixados);

        if ($total > 0 && $baixados >= $total) {
            return 'Baixado';
        }

        if ($baixados > 0) {
            return 'Baixando';
        }

        return 'Ver';
    }

    private function contarArquivosBaixadosProjeto(array $arquivosProjeto): array
    {
        $total = count($arquivosProjeto);
        $baixados = 0;

        foreach ($arquivosProjeto as $arquivoProjeto) {
            if (!is_array($arquivoProjeto)) {
                continue;
            }

            $marcador = trim((string) ($arquivoProjeto['marcador'] ?? '0'));
            if ($marcador !== '' && $marcador !== '0') {
                $baixados++;
            }
        }

        return [$total, $baixados];
    }

    private function montarMenuAcoesLista(array $itens): string
    {
        if ($itens === []) {
            return '';
        }

        $itensNormalizados = [];
        $botoes = [];
        foreach ($itens as $item) {
            $rotulo = trim((string) ($item['rotulo'] ?? ''));
            if ($rotulo === '') {
                continue;
            }

            $classe = trim((string) ($item['classe'] ?? ''));
            $onclick = trim((string) ($item['onclick'] ?? ''));
            $icone = trim((string) ($item['icone'] ?? ''));
            $tooltip = trim((string) ($item['tooltip'] ?? ''));
            $atributoOnclick = $onclick !== ''
                ? ' onclick="' . htmlspecialchars($onclick, ENT_QUOTES, 'UTF-8') . '"'
                : '';
            $atributoTooltip = $tooltip !== ''
                ? ' data-bs-toggle="tooltip" data-bs-title="' . htmlspecialchars($tooltip, ENT_QUOTES, 'UTF-8') . '" title="' . htmlspecialchars($tooltip, ENT_QUOTES, 'UTF-8') . '"'
                : '';
            $iconeHtml = $icone !== ''
                ? '<i class="' . htmlspecialchars($icone, ENT_QUOTES, 'UTF-8') . ' me-1"></i>'
                : '';
            $classeHtml = $classe !== ''
                ? ' ' . htmlspecialchars($classe, ENT_QUOTES, 'UTF-8')
                : '';
            $rotuloHtml = htmlspecialchars($rotulo, ENT_QUOTES, 'UTF-8');

            $itensNormalizados[] = [
                'rotulo' => $rotulo,
                'rotuloHtml' => $rotuloHtml,
                'onclick' => $atributoOnclick,
                'classeHtml' => $classeHtml,
                'icone' => $icone,
            ];

            $botoes[] = '<li><button type="button"'
                . $atributoOnclick
                . $atributoTooltip
                . ' class="dropdown-item' . $classeHtml . '">'
                . $iconeHtml
                . $rotuloHtml
                . '</button></li>';
        }

        if ($botoes === []) {
            return '';
        }

        if (count($itensNormalizados) === 1) {
            $itemUnico = $itensNormalizados[0];
            $titulo = $itemUnico['rotuloHtml'];
            $iconeUnico = $itemUnico['icone'] !== ''
                ? '<i class="' . htmlspecialchars($itemUnico['icone'], ENT_QUOTES, 'UTF-8') . '"></i>'
                : $titulo;

            return '<button type="button"'
                . $itemUnico['onclick']
                . ' class="btn btn-sm btn-outline-secondary wl-row-action-more wl-row-action-single' . $itemUnico['classeHtml'] . '"'
                . ' data-bs-toggle="tooltip" data-bs-title="' . $titulo . '" title="' . $titulo . '" aria-label="' . $titulo . '">'
                . $iconeUnico
                . '</button>';
        }

        return '<div class="dropdown">'
            . '<button type="button" class="btn btn-sm btn-outline-secondary wl-row-action-more" data-bs-toggle="dropdown" data-toggle="dropdown" aria-expanded="false" aria-label="Mais acoes">'
            . '<i class="ri-more-2-fill"></i>'
            . '</button>'
            . '<ul class="dropdown-menu dropdown-menu-end">'
            . implode('', $botoes)
            . '</ul>'
            . '</div>';
    }

    private function montarAcoesCorteLaser(int $indiceLista, string $botaoPrincipalHtml, bool $mostrarAbrirNaMaquina = false, array $itensExtras = []): string
    {
        $itensMenu = [
            [
                'rotulo' => 'Visualizar',
                'onclick' => "ver_dxf('{$indiceLista}')",
                'icone' => 'ri-eye-line',
                'tooltip' => 'Abrir visualizacao',
            ],
        ];

        if ($mostrarAbrirNaMaquina) {
            $itensMenu[] = [
                'rotulo' => 'Abrir na maquina',
                'onclick' => 'abrir_cort(' . $indiceLista . ')',
                'icone' => 'ri-send-plane-line',
                'tooltip' => 'Enviar novamente para a maquina',
            ];
        }

        foreach ($itensExtras as $itemExtra) {
            if (is_array($itemExtra)) {
                $itensMenu[] = $itemExtra;
            }
        }

        return '<div class="wl-row-actions wl-row-actions--with-menu">'
            . $botaoPrincipalHtml
            . $this->montarMenuAcoesLista($itensMenu)
            . '</div>';
    }

    private function buscarItemListaPorIndice(int $indice): ?array
    {
        $this->iniciarSessaoSeNecessario();
        $lista = $_SESSION['lista_completa'] ?? [];
        if (!is_array($lista)) {
            return null;
        }

        $item = Ferramentas::array_pesquisa($lista, 'id', $indice);
        return is_array($item) ? $item : null;
    }

    private function montarCaminhoExibicaoLista(array $item): string
    {
        $diretorio = (string) ($item['diretorio'] ?? '');
        if ($diretorio === '') {
            return '';
        }

        return preg_replace('/\\\\+/', '\\\\', str_replace(["c:/wl/", "/"], ["i:/", "\\\\"], $diretorio));
    }

    private function comprimentoTexto($texto): int
    {
        $texto = trim((string) $texto);
        if ($texto === '') {
            return 0;
        }

        if (function_exists('mb_strlen')) {
            return mb_strlen($texto);
        }

        return strlen($texto);
    }

    private function decodificarValorAuditoria($valor): string
    {
        $valor = (string) ($valor ?? '');
        if ($valor === '') {
            return '';
        }

        $decodificado = Ferramentas::decodificador($valor);
        return $decodificado !== '' ? $decodificado : $valor;
    }

    private function montarEmpreendimentoComEscalaHtml($nome, $escala): string
    {
        $nomeTexto = $this->decodificarValorAuditoria($nome);
        if ($nomeTexto === '' && $nome !== null) {
            $nomeTexto = trim((string) $nome);
        }

        $escalaTexto = $this->decodificarValorAuditoria($escala);
        if ($escalaTexto === '' && $escala !== null) {
            $escalaTexto = trim((string) $escala);
        }

        $nomeHtml = htmlspecialchars($nomeTexto, ENT_QUOTES, 'UTF-8');
        if ($escalaTexto === '') {
            return $nomeHtml;
        }

        return $nomeHtml
            . '<div class="text-muted small">Escala '
            . htmlspecialchars($escalaTexto, ENT_QUOTES, 'UTF-8')
            . '</div>';
    }

    private function obterNomeProcessoPorId(int $processoId): string
    {
        if ($processoId <= 0) {
            return '';
        }

        $processo = (new \App\Models\Processos())
            ->select('id, nome')
            ->where('id', $processoId)
            ->first();

        return is_array($processo) ? $this->decodificarValorAuditoria($processo['nome'] ?? '') : '';
    }

    private function valorOrdenacaoFila($valor): int
    {
        return is_numeric($valor) ? (int) $valor : PHP_INT_MAX;
    }

    private function timestampOrdenacaoFila($valor): int
    {
        $timestamp = strtotime((string) ($valor ?? ''));
        return $timestamp !== false ? $timestamp : PHP_INT_MAX;
    }

    private function buscarIdsDesenhosEmCorteDoUsuario(int $usuarioId, ?int $processoId = null): array
    {
        if ($usuarioId <= 0) {
            return [];
        }

        $builder = (new \App\Models\Desenhos())
            ->select('desenhos.id')
            ->join('corte', 'corte.id = desenhos.corte_id', 'inner')
            ->where('corte.usuario_id_ini', $usuarioId)
            ->where('corte.status', 'inicio')
            ->where('desenhos.status', 'cortando');

        if ($processoId !== null && $processoId > 0) {
            $builder->where('desenhos.processos_id', $processoId);
        }

        $desenhos = $builder->findAll() ?: [];

        return array_values(array_unique(array_filter(array_map(static function ($row): int {
            return (int) ($row['id'] ?? 0);
        }, $desenhos))));
    }

    private function prepararItemFilaParaOrdenacao(array &$item, array $desenhosEmCorteUsuarioIds = []): void
    {
        $desenhoId = (int) ($item['desenho_id'] ?? $item['id'] ?? 0);
        $status = strtolower(trim($this->normalizarStatusTexto($item['status'] ?? '')));
        $item['eh_corte_usuario'] = $status === 'cortando' && in_array($desenhoId, $desenhosEmCorteUsuarioIds, true);
    }

    private function compararItensFila(array $a, array $b): int
    {
        $comparacao = ((!empty($a['eh_corte_usuario'])) ? 0 : 1) <=> ((!empty($b['eh_corte_usuario'])) ? 0 : 1);
        if ($comparacao !== 0) {
            return $comparacao;
        }

        $comparacao = $this->valorOrdenacaoFila($a['prioridade_ordem'] ?? null) <=> $this->valorOrdenacaoFila($b['prioridade_ordem'] ?? null);
        if ($comparacao !== 0) {
            return $comparacao;
        }

        $comparacao = $this->valorOrdenacaoFila($a['ordem'] ?? null) <=> $this->valorOrdenacaoFila($b['ordem'] ?? null);
        if ($comparacao !== 0) {
            return $comparacao;
        }

        $comparacao = $this->timestampOrdenacaoFila($a['data_add'] ?? null) <=> $this->timestampOrdenacaoFila($b['data_add'] ?? null);
        if ($comparacao !== 0) {
            return $comparacao;
        }

        return $this->valorOrdenacaoFila($a['id'] ?? null) <=> $this->valorOrdenacaoFila($b['id'] ?? null);
    }

    private function compararReferenciasProjetoOrdemLista(array $a, array $b): int
    {
        $comparacao = $this->valorOrdenacaoFila($a['prioridade_ordem'] ?? null) <=> $this->valorOrdenacaoFila($b['prioridade_ordem'] ?? null);
        if ($comparacao !== 0) {
            return $comparacao;
        }

        $comparacao = $this->valorOrdenacaoFila($a['desenho_ordem'] ?? null) <=> $this->valorOrdenacaoFila($b['desenho_ordem'] ?? null);
        if ($comparacao !== 0) {
            return $comparacao;
        }

        $comparacao = $this->valorOrdenacaoFila($a['desenho_referencia_id'] ?? null) <=> $this->valorOrdenacaoFila($b['desenho_referencia_id'] ?? null);
        if ($comparacao !== 0) {
            return $comparacao;
        }

        return $this->valorOrdenacaoFila($a['projeto_id'] ?? null) <=> $this->valorOrdenacaoFila($b['projeto_id'] ?? null);
    }

    private function buscarReferenciasProjetosParaOrdemLista(int $processoId): array
    {
        if ($processoId <= 0) {
            return [];
        }

        $linhas = (new \App\Models\Projeto())
            ->select('p.id AS projeto_id,
                      d.id AS desenho_referencia_id,
                      d.prioridade_id,
                      prioridade.ordem AS prioridade_ordem,
                      od.ordem AS desenho_ordem')
            ->from('projeto p')
            ->join('projeto_desenho pd', 'pd.projeto_id = p.id', 'inner')
            ->join('desenhos d', 'd.id = pd.desenho_id', 'inner')
            ->join('prioridade', 'prioridade.id = d.prioridade_id', 'left')
            ->join('ordem od', 'od.desenho_id = d.id AND od.projeto_id IS NULL AND od.status = "ativo"', 'left')
            ->where('d.processos_id', $processoId)
            ->whereIn('d.status', ['pendente', 'cortando', 'processando'])
            ->whereIn('p.status', ['ativo', 'pendente', 'processando', 'finalizado'])
            ->orderBy('p.id', 'ASC')
            ->orderBy('prioridade.ordem IS NULL', 'ASC', false)
            ->orderBy('prioridade.ordem', 'ASC')
            ->orderBy('od.ordem IS NULL', 'ASC', false)
            ->orderBy('od.ordem', 'ASC')
            ->orderBy('d.id', 'ASC')
            ->findAll();

        $referencias = [];
        foreach ($linhas as $linha) {
            $projetoId = (int) ($linha['projeto_id'] ?? 0);
            $prioridadeId = (int) ($linha['prioridade_id'] ?? 0);
            if ($projetoId <= 0 || $prioridadeId <= 0 || isset($referencias[$projetoId])) {
                continue;
            }

            $referencias[$projetoId] = [
                'projeto_id' => $projetoId,
                'desenho_referencia_id' => (int) ($linha['desenho_referencia_id'] ?? 0),
                'prioridade_id' => $prioridadeId,
                'prioridade_ordem' => $linha['prioridade_ordem'] ?? null,
                'desenho_ordem' => $linha['desenho_ordem'] ?? null,
            ];
        }

        uasort($referencias, [$this, 'compararReferenciasProjetoOrdemLista']);

        return $referencias;
    }

    private function garantirOrdemAtivaProjetoLista(int $projetoId, int $processoId, int $prioridadeId): void
    {
        if ($projetoId <= 0 || $processoId <= 0 || $prioridadeId <= 0) {
            return;
        }

        $ordemModel = new \App\Models\Ordem();
        $ordemAtiva = $ordemModel
            ->select('id')
            ->where('projeto_id', $projetoId)
            ->where('desenho_id IS NULL', null, false)
            ->where('processos_id', $processoId)
            ->where('status', 'ativo')
            ->first();

        if (is_array($ordemAtiva) && !empty($ordemAtiva['id'])) {
            return;
        }

        $maxLinha = $ordemModel
            ->selectMax('ordem', 'max_ordem')
            ->where('desenho_id IS NULL', null, false)
            ->where('projeto_id IS NOT NULL', null, false)
            ->where('processos_id', $processoId)
            ->where('prioridade_id', $prioridadeId)
            ->where('status', 'ativo')
            ->first();

        $ordemModel->insert([
            'desenho_id' => null,
            'projeto_id' => $projetoId,
            'processos_id' => $processoId,
            'prioridade_id' => $prioridadeId,
            'ordem' => ((int) ($maxLinha['max_ordem'] ?? 0)) + 1,
            'status' => 'ativo',
        ]);
    }

    private function normalizarSequenciaOrdensProjetosLista(int $processoId, array $referenciasProjetos): void
    {
        if ($processoId <= 0) {
            return;
        }

        $ordensAtivas = (new \App\Models\Ordem())
            ->select('id, projeto_id, prioridade_id, ordem')
            ->where('desenho_id IS NULL', null, false)
            ->where('projeto_id IS NOT NULL', null, false)
            ->where('processos_id', $processoId)
            ->where('status', 'ativo')
            ->findAll();

        $grupos = [];
        foreach ($ordensAtivas as $ordem) {
            $prioridadeId = (int) ($ordem['prioridade_id'] ?? 0);
            if ($prioridadeId <= 0) {
                continue;
            }

            $grupos[$prioridadeId][] = $ordem;
        }

        $ordemModel = new \App\Models\Ordem();
        foreach ($grupos as $linhas) {
            usort($linhas, function (array $a, array $b) use ($referenciasProjetos): int {
                $comparacao = $this->valorOrdenacaoFila($a['ordem'] ?? null) <=> $this->valorOrdenacaoFila($b['ordem'] ?? null);
                if ($comparacao !== 0) {
                    return $comparacao;
                }

                $aProjetoId = (int) ($a['projeto_id'] ?? 0);
                $bProjetoId = (int) ($b['projeto_id'] ?? 0);
                $comparacao = $this->valorOrdenacaoFila($referenciasProjetos[$aProjetoId]['desenho_referencia_id'] ?? null)
                    <=> $this->valorOrdenacaoFila($referenciasProjetos[$bProjetoId]['desenho_referencia_id'] ?? null);
                if ($comparacao !== 0) {
                    return $comparacao;
                }

                $comparacao = $this->valorOrdenacaoFila($aProjetoId) <=> $this->valorOrdenacaoFila($bProjetoId);
                if ($comparacao !== 0) {
                    return $comparacao;
                }

                return $this->valorOrdenacaoFila($a['id'] ?? null) <=> $this->valorOrdenacaoFila($b['id'] ?? null);
            });

            $posicao = 1;
            foreach ($linhas as $linha) {
                $ordemId = (int) ($linha['id'] ?? 0);
                if ($ordemId > 0 && (int) ($linha['ordem'] ?? 0) !== $posicao) {
                    $ordemModel->update($ordemId, ['ordem' => $posicao]);
                }

                $posicao++;
            }
        }
    }

    private function sincronizarOrdensProjetosDoProcessoLista(int $processoId): void
    {
        if ($processoId <= 0) {
            return;
        }

        $referenciasProjetos = $this->buscarReferenciasProjetosParaOrdemLista($processoId);
        $projetosAtivosMap = array_fill_keys(array_keys($referenciasProjetos), true);
        $ordemModel = new \App\Models\Ordem();

        $ordemModel
            ->where('desenho_id IS NULL', null, false)
            ->where('projeto_id IS NULL', null, false)
            ->where('processos_id', $processoId)
            ->where('status', 'ativo')
            ->set('status', 'desativado')
            ->update();

        $ordemModel = new \App\Models\Ordem();
        $ordensAtivas = $ordemModel
            ->select('id, projeto_id, prioridade_id, ordem')
            ->where('desenho_id IS NULL', null, false)
            ->where('projeto_id IS NOT NULL', null, false)
            ->where('processos_id', $processoId)
            ->where('status', 'ativo')
            ->orderBy('projeto_id', 'ASC')
            ->orderBy('ordem', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        $ordensPorProjeto = [];
        foreach ($ordensAtivas as $ordem) {
            $projetoId = (int) ($ordem['projeto_id'] ?? 0);
            if ($projetoId <= 0) {
                continue;
            }

            if (!isset($projetosAtivosMap[$projetoId])) {
                $ordemModel->update((int) ($ordem['id'] ?? 0), ['status' => 'desativado']);
                continue;
            }

            $ordensPorProjeto[$projetoId][] = $ordem;
        }

        foreach ($referenciasProjetos as $projetoId => $referencia) {
            $prioridadeId = (int) ($referencia['prioridade_id'] ?? 0);
            if ($prioridadeId <= 0) {
                continue;
            }

            $ordensProjeto = $ordensPorProjeto[$projetoId] ?? [];
            if ($ordensProjeto === []) {
                $this->garantirOrdemAtivaProjetoLista((int) $projetoId, $processoId, $prioridadeId);
                continue;
            }

            usort($ordensProjeto, function (array $a, array $b) use ($prioridadeId): int {
                $aPrioridadeAtual = (int) ($a['prioridade_id'] ?? 0) === $prioridadeId ? 0 : 1;
                $bPrioridadeAtual = (int) ($b['prioridade_id'] ?? 0) === $prioridadeId ? 0 : 1;
                $comparacao = $aPrioridadeAtual <=> $bPrioridadeAtual;
                if ($comparacao !== 0) {
                    return $comparacao;
                }

                $comparacao = $this->valorOrdenacaoFila($a['ordem'] ?? null) <=> $this->valorOrdenacaoFila($b['ordem'] ?? null);
                if ($comparacao !== 0) {
                    return $comparacao;
                }

                return $this->valorOrdenacaoFila($a['id'] ?? null) <=> $this->valorOrdenacaoFila($b['id'] ?? null);
            });

            $ordemManter = array_shift($ordensProjeto);
            $ordemManterId = (int) ($ordemManter['id'] ?? 0);
            if ($ordemManterId > 0 && (int) ($ordemManter['prioridade_id'] ?? 0) !== $prioridadeId) {
                $ordemModel->update($ordemManterId, ['prioridade_id' => $prioridadeId]);
            }

            foreach ($ordensProjeto as $ordemDuplicada) {
                $ordemDuplicadaId = (int) ($ordemDuplicada['id'] ?? 0);
                if ($ordemDuplicadaId > 0) {
                    $ordemModel->update($ordemDuplicadaId, ['status' => 'desativado']);
                }
            }
        }

        $this->normalizarSequenciaOrdensProjetosLista($processoId, $referenciasProjetos);
    }

    private function buscarLinhasProjetosLista(int $processoId, array $desenhosEmCorteUsuarioIds, \App\Models\Projeto $projetoModel): array
    {
        if ($processoId <= 0) {
            return [];
        }

        $rows = (new \App\Models\Projeto_desenho())
            ->select("
                projeto_desenho.id AS projeto_desenho_id,
                projeto_desenho.usuario_id AS projeto_desenho_usuario_id,
                projeto_desenho.desenho_id,
                projeto_desenho.projeto_id,
                projeto_desenho.data_add AS projeto_desenho_data_add,
                projeto_desenho.marcador,
                p.usuario_id AS projeto_usuario_id,
                p.descricao AS projeto_descricao,
                p.diretorio AS projeto_diretorio,
                p.status AS projeto_status,
                p.data_add AS projeto_data_add,
                op.ordem AS projeto_ordem,
                op.prioridade_id AS projeto_prioridade_id,
                desenhos.id,
                desenhos.processos_id,
                desenhos.status,
                desenhos.nome,
                desenhos.diretorio,
                desenhos.corte_id,
                desenhos.prioridade_id,
                desenhos.finalidade_id,
                desenhos.empreendimentos_id,
                desenhos.empresa_id,
                desenhos.usuario_id_desenhista,
                desenhos.data_add,
                prioridade.ordem AS prioridade_ordem,
                prioridade.nome AS prioridade_nome,
                prioridade.cor AS prioridade_cor,
                empresa.nome AS empresa_nome,
                empreendimentos.nome AS empreendimento_nome,
                empreendimentos.escala AS empreendimento_escala,
                finalidade.nome AS finalidade_nome,
                usuarios.nome AS usuario_nome,
                corte.data_end AS corte_data_end,
                corte.status AS corte_status,
                od.ordem AS ordem
            ")
            ->join('projeto p', 'p.id = projeto_desenho.projeto_id', 'inner')
            ->join('desenhos', 'desenhos.id = projeto_desenho.desenho_id', 'inner')
            ->join('prioridade', 'prioridade.id = desenhos.prioridade_id', 'left')
            ->join('empresa', 'empresa.id = desenhos.empresa_id', 'left')
            ->join('empreendimentos', 'empreendimentos.id = desenhos.empreendimentos_id', 'left')
            ->join('finalidade', 'finalidade.id = desenhos.finalidade_id', 'left')
            ->join('usuarios', 'usuarios.id = desenhos.usuario_id_desenhista', 'left')
            ->join('corte', 'corte.id = desenhos.corte_id', 'left')
            ->join('ordem od', "od.desenho_id = desenhos.id AND od.projeto_id IS NULL AND od.status = 'ativo'", 'left')
            ->join('ordem op', "op.projeto_id = p.id AND op.desenho_id IS NULL AND op.processos_id = {$processoId} AND op.status = 'ativo'", 'left')
            ->where('desenhos.processos_id', $processoId)
            ->whereIn('desenhos.status', ['pendente', 'cortando', 'processando'])
            ->whereIn('p.status', ['ativo', 'pendente', 'processando', 'finalizado'])
            ->orderBy('op.ordem IS NULL', 'ASC', false)
            ->orderBy('op.ordem', 'ASC')
            ->orderBy('prioridade.ordem IS NULL', 'ASC', false)
            ->orderBy('prioridade.ordem', 'ASC')
            ->orderBy('od.ordem IS NULL', 'ASC', false)
            ->orderBy('od.ordem', 'ASC')
            ->orderBy('desenhos.id', 'ASC')
            ->findAll();

        $projetos = [];
        foreach ($rows as $row) {
            $projetoId = (int) ($row['projeto_id'] ?? 0);
            $desenhoId = (int) ($row['id'] ?? 0);
            if ($projetoId <= 0 || $desenhoId <= 0) {
                continue;
            }

            if (!isset($projetos[$projetoId])) {
                $projetos[$projetoId] = [
                    'projeto' => [
                        'id' => $projetoId,
                        'usuario_id' => $row['projeto_usuario_id'] ?? null,
                        'descricao' => $row['projeto_descricao'] ?? '',
                        'diretorio' => $row['projeto_diretorio'] ?? '',
                        'status' => $row['projeto_status'] ?? '',
                        'data_add' => $row['projeto_data_add'] ?? null,
                        'ordem' => $row['projeto_ordem'] ?? null,
                        'prioridade_id' => $row['projeto_prioridade_id'] ?? ($row['prioridade_id'] ?? null),
                    ],
                    'desenhos' => [],
                ];
            }

            $desenho = $row;
            $desenho['desenho_id'] = (int) ($row['desenho_id'] ?? $desenhoId);
            $desenho['projeto_id'] = $projetoId;
            $this->prepararItemFilaParaOrdenacao($desenho, $desenhosEmCorteUsuarioIds);
            $projetos[$projetoId]['desenhos'][] = $desenho;
        }

        $linhasProjeto = [];
        foreach ($projetos as $projetoId => $grupo) {
            $projetoAtual = $grupo['projeto'];
            $this->normalizarProjetoComPendencias($projetoAtual, $projetoModel);

            $todosDesenhosProjeto = $grupo['desenhos'];
            usort($todosDesenhosProjeto, [$this, 'compararItensFila']);

            $desenhoLinha = $todosDesenhosProjeto[0] ?? null;
            if (!is_array($desenhoLinha)) {
                continue;
            }

            $linhasProjeto[] = [
                'projeto_id' => (int) $projetoId,
                'projeto' => $projetoAtual,
                'desenho' => $desenhoLinha,
                'todos_desenhos' => $todosDesenhosProjeto,
                'eh_corte_usuario' => !empty($desenhoLinha['eh_corte_usuario']),
                'prioridade_ordem' => $desenhoLinha['prioridade_ordem'] ?? null,
                'ordem' => $projetoAtual['ordem'] ?? null,
                'data_add' => $desenhoLinha['data_add'] ?? null,
                'id' => (int) $projetoId,
            ];
        }

        usort($linhasProjeto, [$this, 'compararItensFila']);

        return $linhasProjeto;
    }

    private function buscarPrimeiroDesenhoPendenteProjeto(int $projetoId, array $desenhosEmCorteUsuarioIds = []): ?array
    {
        $desenhos = $this->buscarDesenhosPendentesProjeto($projetoId, $desenhosEmCorteUsuarioIds);
        return $desenhos[0] ?? null;
    }

    private function buscarDesenhosPendentesProjeto(int $projetoId, array $desenhosEmCorteUsuarioIds = []): array
    {
        if ($projetoId <= 0) {
            return [];
        }

        $desenhos = (new \App\Models\Projeto_desenho())
            ->select('projeto_desenho.*, desenhos.id, desenhos.processos_id, desenhos.status, desenhos.nome, desenhos.diretorio, desenhos.corte_id, desenhos.prioridade_id, desenhos.finalidade_id, desenhos.empreendimentos_id, desenhos.empresa_id, desenhos.usuario_id_desenhista, desenhos.data_add, prioridade.ordem AS prioridade_ordem, o.ordem AS ordem')
            ->join('desenhos', 'desenhos.id = projeto_desenho.desenho_id', 'left')
            ->join('prioridade', 'prioridade.id = desenhos.prioridade_id', 'left')
            ->join('ordem o', "o.desenho_id = desenhos.id AND o.status = 'ativo'", 'left')
            ->where('projeto_desenho.projeto_id', $projetoId)
            ->whereIn('desenhos.status', ['pendente', 'cortando', 'processando'])
            ->findAll();

        foreach ($desenhos as &$desenho) {
            $this->prepararItemFilaParaOrdenacao($desenho, $desenhosEmCorteUsuarioIds);
        }
        unset($desenho);

        usort($desenhos, [$this, 'compararItensFila']);

        return $desenhos;
    }

    private function normalizarProjetoComPendencias(array &$projeto, \App\Models\Projeto $projetoModel): void
    {
        $projetoId = intval($projeto['id'] ?? 0);
        if ($projetoId <= 0) {
            return;
        }

        $statusAtual = strtolower(trim((string) ($projeto['status'] ?? '')));
        if (in_array($statusAtual, ['ativo', 'processando'], true)) {
            return;
        }

        $projetoModel->update($projetoId, ['status' => 'ativo']);
        $projeto['status'] = 'ativo';
    }

    private function obterProjetoDaSessaoPorIndice($indice): array
    {
        $indices = array_values(array_unique([
            $indice,
            (string) $indice,
            is_numeric($indice) ? (int) $indice : $indice,
        ]));

        foreach (['lista_projetos', 'projeto_todos'] as $chaveSessao) {
            $fonte = $_SESSION[$chaveSessao] ?? null;
            if (!is_array($fonte)) {
                continue;
            }

            foreach ($indices as $indiceCandidato) {
                if (!array_key_exists($indiceCandidato, $fonte)) {
                    continue;
                }

                $itensProjeto = $fonte[$indiceCandidato];
                if (is_array($itensProjeto) && isset($itensProjeto[0]) && is_array($itensProjeto[0])) {
                    return $itensProjeto;
                }
            }
        }

        $listaCompleta = $_SESSION['lista_completa'] ?? [];
        if (!is_array($listaCompleta)) {
            return [];
        }

        $linhaLista = null;
        foreach ($indices as $indiceCandidato) {
            if (array_key_exists($indiceCandidato, $listaCompleta) && is_array($listaCompleta[$indiceCandidato])) {
                $linhaLista = $listaCompleta[$indiceCandidato];
                break;
            }
        }

        if (!is_array($linhaLista)) {
            $projetoIdDireto = is_numeric($indice) ? (int) $indice : 0;
            if ($projetoIdDireto <= 0) {
                return [];
            }

            return $this->buscarItensProjetoPorId($projetoIdDireto);
        }

        $projetoId = (int) ($linhaLista['projeto_id'] ?? 0);
        if ($projetoId <= 0) {
            return [];
        }

        return $this->buscarItensProjetoPorId($projetoId);
    }

    private function buscarItensProjetoPorId(int $projetoId): array
    {
        if ($projetoId <= 0) {
            return [];
        }

        $builder = (new \App\Models\Projeto_desenho())
            ->select('projeto_desenho.*, desenhos.processos_id, desenhos.status, desenhos.nome, desenhos.diretorio, desenhos.corte_id, desenhos.prioridade_id, desenhos.finalidade_id, desenhos.empreendimentos_id, desenhos.empresa_id, desenhos.usuario_id_desenhista, desenhos.data_add')
            ->join('desenhos', 'desenhos.id = projeto_desenho.desenho_id', 'left')
            ->where('projeto_desenho.projeto_id', $projetoId)
            ->orderBy('projeto_desenho.data_add', 'ASC');

        $itensProjeto = $builder
            ->whereNotIn('desenhos.status', ['pronto', 'cortado', 'cortado_notfile', 'concluido', 'concluida', 'finalizado', 'finalizada', 'apagado'])
            ->findAll();

        if ($itensProjeto === []) {
            $itensProjeto = (new \App\Models\Projeto_desenho())
                ->select('projeto_desenho.*, desenhos.processos_id, desenhos.status, desenhos.nome, desenhos.diretorio, desenhos.corte_id, desenhos.prioridade_id, desenhos.finalidade_id, desenhos.empreendimentos_id, desenhos.empresa_id, desenhos.usuario_id_desenhista, desenhos.data_add')
                ->join('desenhos', 'desenhos.id = projeto_desenho.desenho_id', 'left')
                ->where('projeto_desenho.projeto_id', $projetoId)
                ->orderBy('projeto_desenho.data_add', 'ASC')
                ->findAll();
        }

        return is_array($itensProjeto) ? array_values(array_filter($itensProjeto, static function ($item) {
            return is_array($item);
        })) : [];
    }

    private function obterExtensoesPermitidasProcesso(int $processoId): array
    {
        if ($processoId <= 0) {
            return [];
        }

        $rows = (new \App\Models\Processos_filtro())
            ->select('filtros.nome')
            ->join('filtros', 'filtros.id = processos_filtro.filtros_id', 'left')
            ->where('processos_filtro.processos_id', $processoId)
            ->where('filtros.status', 'ativo')
            ->findAll();

        $extensoes = [];
        foreach ($rows as $row) {
            $nome = trim((string) Ferramentas::decodificador((string) ($row['nome'] ?? '')));
            if ($nome === '') {
                $nome = trim((string) ($row['nome'] ?? ''));
            }

            $nome = ltrim(strtolower($nome), '.');
            if ($nome !== '') {
                $extensoes[$nome] = $nome;
            }
        }

        return array_values($extensoes);
    }

    private function montarAcceptExtensoes(array $extensoes): string
    {
        $itens = array_values(array_filter(array_map(static function ($extensao): string {
            $extensao = ltrim(strtolower(trim((string) $extensao)), '.');
            return $extensao === '' ? '' : '.' . $extensao;
        }, $extensoes)));

        return implode(',', $itens);
    }

    private function obterContextoProjetoPorId(int $projetoId): ?array
    {
        if ($projetoId <= 0) {
            return null;
        }

        $projeto = (new \App\Models\Projeto())->find($projetoId);
        if (!is_array($projeto) || empty($projeto['id'])) {
            return null;
        }

        $desenhoBase = (new \App\Models\Projeto_desenho())
            ->select('projeto_desenho.id AS projeto_desenho_id, desenhos.*')
            ->join('desenhos', 'desenhos.id = projeto_desenho.desenho_id', 'inner')
            ->where('projeto_desenho.projeto_id', $projetoId)
            ->orderBy('projeto_desenho.data_add', 'ASC')
            ->first();

        if (!is_array($desenhoBase) || empty($desenhoBase['id'])) {
            return null;
        }

        $itensProjeto = $this->buscarItensProjetoPorId($projetoId);
        $processoId = (int) ($desenhoBase['processos_id'] ?? 0);
        $extensoes = $this->obterExtensoesPermitidasProcesso($processoId);

        return [
            'projeto' => $projeto,
            'desenho_base' => $desenhoBase,
            'itens' => $itensProjeto,
            'processo_id' => $processoId,
            'extensoes' => $extensoes,
            'accept' => $this->montarAcceptExtensoes($extensoes),
        ];
    }

    private function obterContextoProjetoPorIndice($indice): ?array
    {
        $projetoId = 0;
        $itensProjeto = $this->obterProjetoDaSessaoPorIndice($indice);
        if ($itensProjeto !== []) {
            $projetoId = (int) ($itensProjeto[0]['projeto_id'] ?? 0);
        }

        if ($projetoId <= 0 && is_numeric($indice)) {
            $projetoId = (int) $indice;
        }

        return $this->obterContextoProjetoPorId($projetoId);
    }

    private function montarDestinoArquivoProjeto(string $diretorioProjeto, string $nomeArquivoOriginal): array
    {
        $diretorioProjeto = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $diretorioProjeto), DIRECTORY_SEPARATOR);
        $nomeArquivoOriginal = basename($nomeArquivoOriginal);
        $baseNome = trim((string) pathinfo($nomeArquivoOriginal, PATHINFO_FILENAME));
        $extensao = strtolower((string) pathinfo($nomeArquivoOriginal, PATHINFO_EXTENSION));

        if ($baseNome === '') {
            $baseNome = 'arquivo';
        }

        $nomeFinal = $extensao !== ''
            ? $baseNome . '.' . $extensao
            : $baseNome;

        $caminhoFinal = $diretorioProjeto . DIRECTORY_SEPARATOR . $nomeFinal;
        while (file_exists($caminhoFinal)) {
            $sufixo = '_' . random_int(100, 9999) . '_';
            $nomeFinal = $extensao !== ''
                ? $baseNome . $sufixo . '.' . $extensao
                : $baseNome . $sufixo;
            $caminhoFinal = $diretorioProjeto . DIRECTORY_SEPARATOR . $nomeFinal;
        }

        return [
            'diretorio' => $diretorioProjeto,
            'nome_final' => $nomeFinal,
            'caminho_final' => $caminhoFinal,
            'nome_banco' => pathinfo($nomeFinal, PATHINFO_FILENAME),
        ];
    }

    private function registrarAlteracaoProjetoArquivo(string $acao, int $projetoId, int $desenhoId, array $detalhes, array $metaExtra = []): void
    {
        $usuarioId = (int) ($_SESSION['usuario'] ?? 0);
        (new \App\Models\Alteracoes())->insertWithDetails(
            [
                'usuario_id' => $usuarioId,
                'individuo' => $usuarioId,
                'id_item' => $projetoId,
                'item' => 'projeto',
                'info_mais' => 'lista_tarefas.' . $acao,
                '_meta' => array_merge([
                    'acao' => 'lista_tarefas.' . $acao,
                    'origem' => 'lista_corte_post',
                    'projeto_id' => $projetoId,
                    'desenho_id' => $desenhoId,
                ], $metaExtra),
            ],
            $detalhes
        );
    }

    private function resolverProcessoDependencia($dependenciaValor): ?array
    {
        if ($dependenciaValor === null || $dependenciaValor === '' || $dependenciaValor === 'Nenhuma') {
            return null;
        }

        $processosModel = new \App\Models\Processos();

        if (is_numeric($dependenciaValor)) {
            $processoDependencia = $processosModel
                ->select('processos.id AS id, processos.nome AS nome, processos.input AS input')
                ->where('processos.id', (int) $dependenciaValor)
                ->first();

            if (is_array($processoDependencia) && !empty($processoDependencia['id'])) {
                return $processoDependencia;
            }
        }

        $dependenciaTexto = $this->decodificarValorAuditoria($dependenciaValor);
        $nomeDependenciaCodificado = Ferramentas::codificador((string) $dependenciaTexto);

        $processoDependencia = $processosModel
            ->select('processos.id AS id, processos.nome AS nome, processos.input AS input')
            ->groupStart()
            ->where('processos.nome', (string) $dependenciaValor)
            ->orWhere('processos.nome', (string) $dependenciaTexto)
            ->orWhere('processos.nome', $nomeDependenciaCodificado)
            ->groupEnd()
            ->first();

        return is_array($processoDependencia) && !empty($processoDependencia['id'])
            ? $processoDependencia
            : null;
    }

    private function campoProcessosExiste(string $campo): bool
    {
        static $cache = [];

        if (!array_key_exists($campo, $cache)) {
            $cache[$campo] = \Config\Database::connect()->fieldExists($campo, 'processos');
        }

        return (bool) $cache[$campo];
    }

    private function normalizarIdsFinalidadesDependencia($valor): array
    {
        if (is_array($valor)) {
            $partes = $valor;
        } else {
            $partes = preg_split('/[,\-\s]+/', (string) ($valor ?? '')) ?: [];
        }

        $ids = [];
        foreach ($partes as $parte) {
            $id = (int) trim((string) $parte);
            if ($id > 0) {
                $ids[$id] = $id;
            }
        }

        return array_values($ids);
    }

    private function dependenciaObrigatoriaProcesso(array $processo): bool
    {
        if (!$this->campoProcessosExiste('dependencia_obrigatoria')) {
            return true;
        }

        $valor = strtolower(trim((string) ($processo['dependencia_obrigatoria'] ?? '1')));
        return !in_array($valor, ['0', 'false', 'nao', 'opcional'], true);
    }

    private function dependenciaOpcionalPorFinalidade(array $processo, array $itensProjeto): bool
    {
        if (!$this->campoProcessosExiste('dependencia_finalidades_opcionais')) {
            return false;
        }

        $finalidadesOpcionais = $this->normalizarIdsFinalidadesDependencia($processo['dependencia_finalidades_opcionais'] ?? '');
        if ($finalidadesOpcionais === []) {
            return false;
        }

        $finalidadesMap = array_fill_keys($finalidadesOpcionais, true);
        foreach ($itensProjeto as $itemProjeto) {
            $finalidadeId = (int) ($itemProjeto['finalidade_id'] ?? 0);
            if ($finalidadeId > 0 && isset($finalidadesMap[$finalidadeId])) {
                return true;
            }
        }

        return false;
    }

    private function registrarAlteracaoCancelamentoCorte(int $desenhoId, int $corteId, array $desenhoAntes, array $corteAntes, string $justificativa): bool
    {
        $usuarioId = (int) ($_SESSION['usuario'] ?? 0);
        $nomeDesenho = Ferramentas::remove_id_file($this->decodificarValorAuditoria($desenhoAntes['nome'] ?? ''));
        $processoId = (int) ($desenhoAntes['processos_id'] ?? 0);

        return (new \App\Models\Alteracoes())->insertWithDetails(
            [
                'usuario_id' => $usuarioId,
                'individuo' => $usuarioId,
                'id_item' => $desenhoId,
                'item' => 'desenho',
                'info_mais' => 'lista_tarefas.cancelar_corte',
                '_meta' => [
                    'acao' => 'lista_tarefas.cancelar_corte',
                    'origem' => 'lista_corte_post',
                    'corte_id' => $corteId,
                    'processo_id' => $processoId,
                    'processo_nome' => $this->obterNomeProcessoPorId($processoId),
                    'desenho_nome' => $nomeDesenho,
                    'justificativa_cancelamento' => $justificativa,
                ],
            ],
            [
                [
                    'campo' => 'desenho.status',
                    'valor_antes' => $this->normalizarStatusTexto($desenhoAntes['status'] ?? ''),
                    'valor_depois' => 'pendente',
                ],
                [
                    'campo' => 'corte.status',
                    'valor_antes' => $this->normalizarStatusTexto($corteAntes['status'] ?? ''),
                    'valor_depois' => 'cancelado',
                ],
                [
                    'campo' => 'corte.usuario_id_fim',
                    'valor_antes' => (string) ($corteAntes['usuario_id_fim'] ?? ''),
                    'valor_depois' => (string) $usuarioId,
                ],
                [
                    'campo' => 'justificativa',
                    'valor_antes' => '',
                    'valor_depois' => $justificativa,
                ],
            ]
        ) !== false;
    }

    private function registrarAlteracaoStatusDependenciaPai(string $item, int $itemId, array $detalhes, array $meta = []): void
    {
        if ($itemId <= 0 || $detalhes === []) {
            return;
        }

        $usuarioId = (int) ($_SESSION['usuario'] ?? 0);
        (new \App\Models\Alteracoes())->insertWithDetails(
            [
                'usuario_id' => $usuarioId,
                'individuo' => $usuarioId,
                'id_item' => $itemId,
                'item' => $item,
                'info_mais' => 'dependencia.status_pai',
                '_meta' => array_merge([
                    'acao' => 'dependencia.status_pai',
                    'origem' => 'lista_corte_post',
                ], $meta),
            ],
            $detalhes
        );
    }

    private function statusContaComoConcluidoDependencia($status): bool
    {
        $status = strtolower(trim($this->normalizarStatusTexto($status)));
        return in_array($status, [
            'pronto',
            'cortado',
            'cortado_notfile',
            'concluido',
            'concluida',
            'finalizado',
            'finalizada',
        ], true);
    }

    private function concluirProjetoPaiDependente(int $projetoPaiId, string $origemContexto): void
    {
        if ($projetoPaiId <= 0) {
            return;
        }

        $projetoModel = new \App\Models\Projeto();
        $projetoPai = $projetoModel->find($projetoPaiId);
        if (!is_array($projetoPai)) {
            return;
        }

        $statusProjetoAntes = strtolower(trim((string) ($projetoPai['status'] ?? '')));

        $projetoDesenhoRows = (new \App\Models\Projeto_desenho())
            ->select('desenho_id')
            ->where('projeto_id', $projetoPaiId)
            ->findAll();

        $desenhoIds = [];
        foreach ($projetoDesenhoRows as $projetoDesenhoRow) {
            $desenhoId = (int) ($projetoDesenhoRow['desenho_id'] ?? 0);
            if ($desenhoId > 0) {
                $desenhoIds[$desenhoId] = $desenhoId;
            }
        }

        if ($desenhoIds === []) {
            return;
        }

        $desenhoModel = new \App\Models\Desenhos();
        $desenhosPai = $desenhoModel
            ->whereIn('id', array_values($desenhoIds))
            ->findAll();

        if (count($desenhosPai) !== count($desenhoIds)) {
            return;
        }

        $this->iniciarSessaoSeNecessario();
        $usuarioId = (int) ($_SESSION['usuario'] ?? 0);
        $ip = $this->ipAtualRequisicao();
        $db = \Config\Database::connect();
        $db->transStart();
        $alteracoesDesenhos = [];

        foreach ($desenhosPai as $desenhoPai) {
            $desenhoId = (int) ($desenhoPai['id'] ?? 0);
            if ($desenhoId <= 0) {
                $db->transRollback();
                return;
            }

            $resultadoCorte = $this->garantirCorteFinalizadoParaDesenhoDependente($desenhoPai, $usuarioId, $ip);
            if ($resultadoCorte === null) {
                $db->transRollback();
                return;
            }

            if ($resultadoCorte['alterado']) {
                $alteracoesDesenhos[] = [
                    'desenho' => $desenhoPai,
                    'resultado' => $resultadoCorte,
                ];
            }
        }

        $projetoAlterado = false;
        if ($statusProjetoAntes !== 'finalizado') {
            $projetoAlterado = $projetoModel->update($projetoPaiId, ['status' => 'finalizado']);
            if (!$projetoAlterado) {
                $db->transRollback();
                return;
            }
        }

        $db->transComplete();
        if (!$db->transStatus()) {
            return;
        }

        foreach ($alteracoesDesenhos as $alteracaoDesenho) {
            $desenhoPai = $alteracaoDesenho['desenho'];
            $resultadoCorte = $alteracaoDesenho['resultado'];
            $desenhoId = (int) ($desenhoPai['id'] ?? 0);
            $detalhes = [];
            if ($resultadoCorte['status_alterado']) {
                $detalhes[] = [
                    'campo' => 'desenho.status',
                    'valor_antes' => $resultadoCorte['status_antes'],
                    'valor_depois' => 'pronto',
                ];
            }
            if ($resultadoCorte['corte_alterado']) {
                $detalhes[] = [
                    'campo' => 'desenho.corte_id',
                    'valor_antes' => (string) ($desenhoPai['corte_id'] ?? ''),
                    'valor_depois' => (string) $resultadoCorte['corte_id'],
                ];
            }
            if ($resultadoCorte['data_conclusao_alterada']) {
                $detalhes[] = [
                    'campo' => 'corte.data_end',
                    'valor_antes' => $resultadoCorte['data_conclusao_antes'],
                    'valor_depois' => $resultadoCorte['data_conclusao_depois'],
                ];
            }

            if ($detalhes !== []) {
                $this->registrarAlteracaoStatusDependenciaPai(
                    'desenho',
                    $desenhoId,
                    $detalhes,
                    [
                        'origem_contexto' => $origemContexto,
                        'projeto_id' => $projetoPaiId,
                        'desenho_id' => $desenhoId,
                        'desenho_nome' => Ferramentas::remove_id_file($this->decodificarValorAuditoria($desenhoPai['nome'] ?? '')),
                        'corte_id' => $resultadoCorte['corte_id'],
                    ]
                );
            }
        }

        if ($projetoAlterado) {
            $this->registrarAlteracaoStatusDependenciaPai(
                'projeto',
                $projetoPaiId,
                [[
                    'campo' => 'projeto.status',
                    'valor_antes' => $statusProjetoAntes,
                    'valor_depois' => 'finalizado',
                ]],
                [
                    'origem_contexto' => $origemContexto,
                    'projeto_id' => $projetoPaiId,
                    'projeto_descricao' => trim((string) ($projetoPai['descricao'] ?? '')),
                ]
            );
        }

        Ferramentas::sincronizarNovasOrdens();
    }

    private function concluirDesenhoPaiDependente(int $desenhoPaiId, string $origemContexto): void
    {
        if ($desenhoPaiId <= 0) {
            return;
        }

        $desenhoModel = new \App\Models\Desenhos();
        $desenhoPai = $desenhoModel->find($desenhoPaiId);
        if (!is_array($desenhoPai)) {
            return;
        }

        $this->iniciarSessaoSeNecessario();
        $db = \Config\Database::connect();
        $db->transStart();
        $resultadoCorte = $this->garantirCorteFinalizadoParaDesenhoDependente(
            $desenhoPai,
            (int) ($_SESSION['usuario'] ?? 0),
            $this->ipAtualRequisicao()
        );
        if ($resultadoCorte === null) {
            $db->transRollback();
            return;
        }

        $db->transComplete();
        if (!$db->transStatus() || !$resultadoCorte['alterado']) {
            return;
        }

        $detalhes = [];
        if ($resultadoCorte['status_alterado']) {
            $detalhes[] = [
                'campo' => 'desenho.status',
                'valor_antes' => $resultadoCorte['status_antes'],
                'valor_depois' => 'pronto',
            ];
        }
        if ($resultadoCorte['corte_alterado']) {
            $detalhes[] = [
                'campo' => 'desenho.corte_id',
                'valor_antes' => (string) ($desenhoPai['corte_id'] ?? ''),
                'valor_depois' => (string) $resultadoCorte['corte_id'],
            ];
        }
        if ($resultadoCorte['data_conclusao_alterada']) {
            $detalhes[] = [
                'campo' => 'corte.data_end',
                'valor_antes' => $resultadoCorte['data_conclusao_antes'],
                'valor_depois' => $resultadoCorte['data_conclusao_depois'],
            ];
        }

        $this->registrarAlteracaoStatusDependenciaPai(
            'desenho',
            $desenhoPaiId,
            $detalhes,
            [
                'origem_contexto' => $origemContexto,
                'desenho_id' => $desenhoPaiId,
                'desenho_nome' => Ferramentas::remove_id_file($this->decodificarValorAuditoria($desenhoPai['nome'] ?? '')),
                'corte_id' => $resultadoCorte['corte_id'],
            ]
        );

        Ferramentas::sincronizarNovasOrdens();
    }

    private function avaliarConclusaoDependenciaProjeto(int $projetoFilhoId): void
    {
        if ($projetoFilhoId <= 0) {
            return;
        }

        $dependencias = (new \App\Models\Dependencia())
            ->select('projeto_id_dependente, desenhos_id_dependente')
            ->where('projeto_id', $projetoFilhoId)
            ->findAll();

        foreach ($dependencias as $dependencia) {
            $projetoPaiId = (int) ($dependencia['projeto_id_dependente'] ?? 0);
            $desenhoPaiId = (int) ($dependencia['desenhos_id_dependente'] ?? 0);

            if ($projetoPaiId > 0) {
                $this->concluirProjetoPaiDependente($projetoPaiId, 'dependencia.projeto');
            } elseif ($desenhoPaiId > 0) {
                $this->concluirDesenhoPaiDependente($desenhoPaiId, 'dependencia.projeto');
            }
        }
    }

    private function avaliarConclusaoDependenciaDesenho(int $desenhoFilhoId): void
    {
        if ($desenhoFilhoId <= 0) {
            return;
        }

        $dependenciaModel = new \App\Models\Dependencia();
        $dependencias = $dependenciaModel
            ->select('projeto_id_dependente, desenhos_id_dependente')
            ->where('desenhos_id', $desenhoFilhoId)
            ->findAll();

        foreach ($dependencias as $dependencia) {
            $projetoPaiId = (int) ($dependencia['projeto_id_dependente'] ?? 0);
            $desenhoPaiId = (int) ($dependencia['desenhos_id_dependente'] ?? 0);

            if ($projetoPaiId > 0) {
                $filhosDependentes = $dependenciaModel
                    ->select('desenhos_id')
                    ->where('projeto_id_dependente', $projetoPaiId)
                    ->where('desenhos_id IS NOT NULL', null, false)
                    ->findAll();

                $desenhoIdsFilhos = [];
                foreach ($filhosDependentes as $filhoDependente) {
                    $filhoId = (int) ($filhoDependente['desenhos_id'] ?? 0);
                    if ($filhoId > 0) {
                        $desenhoIdsFilhos[$filhoId] = $filhoId;
                    }
                }

                if ($desenhoIdsFilhos === []) {
                    $this->concluirProjetoPaiDependente($projetoPaiId, 'dependencia.desenho');
                    continue;
                }

                $filhosStatusRows = (new \App\Models\Desenhos())
                    ->select('id, status')
                    ->whereIn('id', array_values($desenhoIdsFilhos))
                    ->findAll();

                $statusPorFilho = [];
                foreach ($filhosStatusRows as $filhoStatusRow) {
                    $statusPorFilho[(int) ($filhoStatusRow['id'] ?? 0)] = $filhoStatusRow['status'] ?? '';
                }

                $todosConcluidos = count($statusPorFilho) === count($desenhoIdsFilhos);
                if ($todosConcluidos) {
                    foreach ($desenhoIdsFilhos as $filhoId) {
                        if (!$this->statusContaComoConcluidoDependencia($statusPorFilho[$filhoId] ?? '')) {
                            $todosConcluidos = false;
                            break;
                        }
                    }
                }

                if ($todosConcluidos) {
                    $this->concluirProjetoPaiDependente($projetoPaiId, 'dependencia.desenho');
                }
            } elseif ($desenhoPaiId > 0) {
                $this->concluirDesenhoPaiDependente($desenhoPaiId, 'dependencia.desenho');
            }
        }
    }

    private function ipAtualRequisicao(): string
    {
        return (string) ($_SERVER['REMOTE_ADDR'] ?? '');
    }

    private function cortePossuiDataConclusao(?array $corte): bool
    {
        $dataConclusao = trim((string) ($corte['data_end'] ?? ''));
        return $dataConclusao !== '' && $dataConclusao !== '0000-00-00 00:00:00';
    }

    /**
     * A conclusao por dependencia nao passa pela tela de corte. Garanta o corte
     * antes de marcar o desenho pai como pronto para preservar a data final.
     */
    private function garantirCorteFinalizadoParaDesenhoDependente(array $desenho, int $usuarioId, string $ip): ?array
    {
        $desenhoId = (int) ($desenho['id'] ?? 0);
        if ($desenhoId <= 0) {
            return null;
        }

        $statusAntes = strtolower(trim($this->normalizarStatusTexto($desenho['status'] ?? '')));
        $corteIdAntes = (int) ($desenho['corte_id'] ?? 0);
        $corteModel = new \App\Models\Corte();
        $corteAntes = $corteIdAntes > 0 ? $corteModel->find($corteIdAntes) : null;
        $corteSemConclusao = !$this->cortePossuiDataConclusao(is_array($corteAntes) ? $corteAntes : null);
        $corteId = $corteIdAntes;

        if ($corteSemConclusao) {
            $corteId = $this->registrarCorteFinalizado($desenho, $usuarioId, $ip);
            if ($corteId === null) {
                return null;
            }
        }

        $dadosAtualizacao = [];
        $statusAlterado = !$this->statusContaComoConcluidoDependencia($statusAntes);
        if ($statusAlterado) {
            $dadosAtualizacao['status'] = 'pronto';
        }
        if ($corteId !== $corteIdAntes) {
            $dadosAtualizacao['corte_id'] = $corteId;
        }

        if ($dadosAtualizacao !== []) {
            $ok = (new \App\Models\Desenhos())->update($desenhoId, $dadosAtualizacao);
            if (!$ok) {
                return null;
            }
        }

        $corteDepois = $corteId > 0 ? $corteModel->find($corteId) : null;
        return [
            'alterado' => $corteSemConclusao || $dadosAtualizacao !== [],
            'status_alterado' => $statusAlterado,
            'status_antes' => $statusAntes,
            'corte_alterado' => $corteId !== $corteIdAntes,
            'corte_id' => $corteId,
            'data_conclusao_alterada' => $corteSemConclusao,
            'data_conclusao_antes' => (string) ($corteAntes['data_end'] ?? ''),
            'data_conclusao_depois' => (string) ($corteDepois['data_end'] ?? ''),
        ];
    }

    private function registrarCorteFinalizado(array $desenho, int $usuarioId, string $ip): ?int
    {
        $corteModel = new \App\Models\Corte();
        $agora = date('Y-m-d H:i:s');
        $corteId = (int) ($desenho['corte_id'] ?? 0);
        $corteAtual = $corteId > 0 ? $corteModel->find($corteId) : null;
        $statusCorteAtual = strtolower(trim((string) ($corteAtual['status'] ?? '')));

        if (is_array($corteAtual) && !empty($corteAtual['id']) && $statusCorteAtual === 'inicio') {
            $ok = $corteModel->update($corteId, [
                'usuario_id_fim' => $usuarioId,
                'data_end' => $agora,
                'status' => 'finalizado',
            ]);

            return $ok ? $corteId : null;
        }

        $ok = $corteModel->insert([
            'usuario_id_ini' => (int) ($corteAtual['usuario_id_ini'] ?? $usuarioId),
            'usuario_id_fim' => $usuarioId,
            'status' => 'finalizado',
            'ip' => $ip,
            'data_end' => $agora,
        ]);

        return $ok ? (int) $corteModel->getInsertID() : null;
    }

    private function carregarDesenhoEtiqueta(int $desenhoId): ?array
    {
        if ($desenhoId <= 0) {
            return null;
        }

        $desenho = (new \App\Models\Desenhos())
            ->select("
                desenhos.*,
                processos.nome AS processo_nome,
                usuarios.nome AS usuario_nome,
                empresa.nome AS empresa_nome,
                empreendimentos.nome AS empreendimento_nome,
                empreendimentos.escala AS empreendimento_escala,
                finalidade.nome AS finalidade_nome
            ")
            ->join('processos', 'processos.id = desenhos.processos_id', 'left')
            ->join('usuarios', 'usuarios.id = desenhos.usuario_id_desenhista', 'left')
            ->join('empresa', 'empresa.id = desenhos.empresa_id', 'left')
            ->join('empreendimentos', 'empreendimentos.id = desenhos.empreendimentos_id', 'left')
            ->join('finalidade', 'finalidade.id = desenhos.finalidade_id', 'left')
            ->where('desenhos.id', $desenhoId)
            ->first();

        return is_array($desenho) ? $desenho : null;
    }

    private function gerarEtiquetaPdfBase64(array $desenho): ?string
    {
        try {
            $dados = [
                'nome' => Ferramentas::remove_id_file($this->decodificarValorAuditoria($desenho['nome'] ?? '')),
                'processo' => $this->decodificarValorAuditoria($desenho['processo_nome'] ?? ''),
                'desenhista' => $this->decodificarValorAuditoria($desenho['usuario_nome'] ?? ''),
                'empresa' => $this->decodificarValorAuditoria($desenho['empresa_nome'] ?? ''),
                'empreendimento' => $this->decodificarValorAuditoria($desenho['empreendimento_nome'] ?? ''),
                'finalidade' => $this->decodificarValorAuditoria($desenho['finalidade_nome'] ?? ''),
                'subpasta' => $this->extrairTagsDeDiretorio((string) ($desenho['diretorio'] ?? '')),
            ];

            $conteudoQr = "Nome: {$dados['nome']}\n"
                . "Processo: {$dados['processo']}\n"
                . "Desenhista: {$dados['desenhista']}\n"
                . "Empresa/Cliente: {$dados['empresa']}\n"
                . "Empreendimento: {$dados['empreendimento']}\n"
                . "Finalidade: {$dados['finalidade']}\n"
                . "Subpasta: {$dados['subpasta']}";

            $qrCode = Builder::create()
                ->writer(new PngWriter())
                ->data($conteudoQr)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
                ->size(150)
                ->margin(10)
                ->build();

            $html = '
                <!DOCTYPE html>
                <html lang="pt-br">
                <head>
                    <meta charset="UTF-8">
                    <title>Etiqueta PDF</title>
                </head>
                <body>
                    <table border="1" cellpadding="0" cellspacing="0" style="width: 60mm; height: 40mm; font-family: Arial; font-size: 8pt; border-collapse: collapse;">
                        <tr>
                            <td style="width: 50%; vertical-align: top; padding: 4px; white-space: normal; word-break: break-word; word-wrap: break-word;">
                                <div>
                                    <p><strong>Processo:</strong> ' . htmlspecialchars($dados['processo'], ENT_QUOTES, 'UTF-8') . '</p>
                                    <p><strong>Empresa/Cliente:</strong> ' . htmlspecialchars($dados['empresa'], ENT_QUOTES, 'UTF-8') . '</p>
                                    <p><strong>Empreendimento:</strong> ' . htmlspecialchars($dados['empreendimento'], ENT_QUOTES, 'UTF-8') . '</p>
                                    <p><strong>Finalidade:</strong> ' . htmlspecialchars($dados['finalidade'], ENT_QUOTES, 'UTF-8') . '</p>
                                    <p><strong>Subpasta:</strong> ' . htmlspecialchars($dados['subpasta'], ENT_QUOTES, 'UTF-8') . '</p>
                                </div>
                            </td>
                            <td style="width: 50%; text-align: center; vertical-align: middle;">
                                <div style="width: 100%; height: 100%;">
                                    <img src="' . $qrCode->getDataUri() . '" alt="QR Code" style="width: 100%; height: 100%; object-fit: contain;">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 50%; vertical-align: top; padding: 4px; white-space: normal; word-break: break-word; word-wrap: break-word;">
                                <div>
                                    <p><strong>Nome:</strong> ' . wordwrap(htmlspecialchars($dados['nome'], ENT_QUOTES, 'UTF-8'), 30, "<br>", true) . '</p>
                                </div>
                            </td>
                            <td style="width: 50%; text-align: center; vertical-align: middle;">
                                <div style="width: 100%; height: 100%;">
                                    <p><strong>Desenhista:</strong> ' . htmlspecialchars($dados['desenhista'], ENT_QUOTES, 'UTF-8') . '</p>
                                </div>
                            </td>
                        </tr>
                    </table>
                </body>
                </html>
            ';

            $mpdf = new Mpdf([
                'format' => [60, 40],
                'margin_top' => 0,
                'margin_bottom' => 0,
                'margin_left' => 0,
                'margin_right' => 0,
            ]);
            $mpdf->WriteHTML($html);

            return base64_encode($mpdf->Output('', 'S'));
        } catch (\Throwable $e) {
            log_message('error', 'ListaCortePost: falha ao gerar etiqueta PDF do desenho ' . (int) ($desenho['id'] ?? 0) . ': ' . $e->getMessage());
            return null;
        }
    }

    public function confirmar_projeto()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'ok' => false,
                'mensagem' => 'Requisicao invalida.',
            ]);
        }

        $this->iniciarSessaoSeNecessario();

        $indice = $this->request->getPost('id');
        $modoFinalizacao = strtolower(trim((string) $this->request->getPost('modo_finalizacao')));
        $finalizarDireto = in_array($modoFinalizacao, ['finalizar_direto', 'direto', 'sem_continuacao'], true);
        $criarContinuacao = in_array($modoFinalizacao, ['criar_continuacao', 'continuacao', 'dependencia'], true);
        $itensProjeto = $this->obterProjetoDaSessaoPorIndice($indice);
        if ($itensProjeto === []) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Projeto nao encontrado na lista atual.',
            ]);
        }

        foreach ($itensProjeto as $itemProjeto) {
            $statusAtual = strtolower(trim($this->normalizarStatusTexto($itemProjeto['status'] ?? '')));
            if ($statusAtual === 'processando') {
                return $this->response->setStatusCode(422)->setJSON([
                    'ok' => false,
                    'mensagem' => 'Projeto em processamento. Conclua a dependencia antes de finalizar.',
                ]);
            }
        }

        $processoOrigemId = (int) ($itensProjeto[0]['processos_id'] ?? 0);
        $processoOrigem = $processoOrigemId > 0
            ? (new \App\Models\Processos())->find($processoOrigemId)
            : null;

        $processoDependencia = is_array($processoOrigem)
            ? $this->resolverProcessoDependencia($processoOrigem['processos_id_proximo'] ?? null)
            : null;

        if (is_array($processoDependencia) && !empty($processoDependencia['id'])) {
            $dependenciaNome = $this->decodificarValorAuditoria($processoDependencia['nome'] ?? '');
            $dependenciaOpcional = is_array($processoOrigem)
                && (!$this->dependenciaObrigatoriaProcesso($processoOrigem) || $this->dependenciaOpcionalPorFinalidade($processoOrigem, $itensProjeto));

            if ($finalizarDireto && !$dependenciaOpcional) {
                $finalizarDireto = false;
            }

            if (!$finalizarDireto) {
                if ($dependenciaOpcional && !$criarContinuacao) {
                    return $this->response->setJSON([
                        'ok' => true,
                        'requer_escolha_finalizacao' => true,
                        'dependencia' => $dependenciaNome,
                        'tipo' => (string) ($processoDependencia['input'] ?? ''),
                        'mensagem' => 'Escolha se a Arte Final sera finalizada direto ou se deve criar uma tarefa de continuacao.',
                    ]);
                }

                $_SESSION['processo_dependencia'] = $itensProjeto;

                return $this->response->setJSON([
                    'ok' => true,
                    'dependencia' => $dependenciaNome,
                    'tipo' => (string) ($processoDependencia['input'] ?? ''),
                ]);
            }
        }

        unset($_SESSION['processo_dependencia']);

        $usuarioId = (int) ($_SESSION['usuario'] ?? 0);
        $ip = $this->ipAtualRequisicao();
        $projetoId = (int) ($itensProjeto[0]['projeto_id'] ?? 0);
        $desenhoModel = new \App\Models\Desenhos();

        $desenhoIds = [];
        foreach ($itensProjeto as $itemProjeto) {
            $desenhoId = (int) ($itemProjeto['desenho_id'] ?? $itemProjeto['id'] ?? 0);
            if ($desenhoId > 0) {
                $desenhoIds[$desenhoId] = $desenhoId;
            }
        }

        if ($desenhoIds === []) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Projeto sem desenhos para finalizar.',
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        foreach (array_values($desenhoIds) as $desenhoId) {
            $desenhoAtual = $desenhoModel->find($desenhoId);
            if (!is_array($desenhoAtual) || empty($desenhoAtual['id'])) {
                $db->transRollback();
                return $this->response->setStatusCode(422)->setJSON([
                    'ok' => false,
                    'mensagem' => 'Um ou mais desenhos do projeto nao foram encontrados.',
                ]);
            }

            $statusAtual = strtolower(trim($this->normalizarStatusTexto($desenhoAtual['status'] ?? '')));
            if ($this->statusContaComoConcluidoDependencia($statusAtual)) {
                continue;
            }

            $corteId = $this->registrarCorteFinalizado($desenhoAtual, $usuarioId, $ip);
            if ($corteId === null) {
                $db->transRollback();
                return $this->response->setStatusCode(422)->setJSON([
                    'ok' => false,
                    'mensagem' => 'Falha ao finalizar o corte de um ou mais desenhos do projeto.',
                ]);
            }

            $ok = $desenhoModel->update($desenhoId, [
                'status' => 'pronto',
                'corte_id' => $corteId,
            ]);

            if (!$ok) {
                $db->transRollback();
                return $this->response->setStatusCode(422)->setJSON([
                    'ok' => false,
                    'mensagem' => 'Falha ao atualizar um ou mais desenhos do projeto.',
                ]);
            }
        }

        if ($projetoId > 0) {
            $okProjeto = (new \App\Models\Projeto())->update($projetoId, ['status' => 'finalizado']);
            if (!$okProjeto) {
                $db->transRollback();
                return $this->response->setStatusCode(422)->setJSON([
                    'ok' => false,
                    'mensagem' => 'Falha ao finalizar o projeto.',
                ]);
            }
        }

        $db->transComplete();
        if (!$db->transStatus()) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Falha ao concluir a finalizacao do projeto.',
            ]);
        }

        foreach (array_values($desenhoIds) as $desenhoId) {
            $this->avaliarConclusaoDependenciaDesenho($desenhoId);
        }

        if ($projetoId > 0) {
            $this->avaliarConclusaoDependenciaProjeto($projetoId);
        }

        Ferramentas::sincronizarNovasOrdens();

        return $this->response->setJSON([
            'ok' => true,
        ]);
    }

    public function confirmar_corte()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'ok' => false,
                'mensagem' => 'Requisicao invalida.',
            ]);
        }

        $this->iniciarSessaoSeNecessario();

        $indice = (int) $this->request->getPost('id');
        $desenhoId = (int) ($_SESSION['lista'][$indice] ?? 0);
        if ($desenhoId <= 0) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Desenho nao encontrado na lista atual.',
            ]);
        }

        $desenhoModel = new \App\Models\Desenhos();
        $desenhoAtual = $desenhoModel->find($desenhoId);
        if (!is_array($desenhoAtual) || empty($desenhoAtual['id'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Desenho nao encontrado para confirmar corte.',
            ]);
        }

        $statusAtual = strtolower(trim($this->normalizarStatusTexto($desenhoAtual['status'] ?? '')));
        if (!in_array($statusAtual, ['pendente', 'cortando'], true)) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'O desenho nao esta disponivel para confirmar corte.',
            ]);
        }

        $usuarioId = (int) ($_SESSION['usuario'] ?? 0);
        $ip = $this->ipAtualRequisicao();
        $corteAtual = null;
        $corteIdAtual = (int) ($desenhoAtual['corte_id'] ?? 0);
        if ($corteIdAtual > 0) {
            $corteAtual = (new \App\Models\Corte())->find($corteIdAtual);
        }

        if (is_array($corteAtual) && strtolower(trim((string) ($corteAtual['status'] ?? ''))) === 'inicio') {
            $usuarioInicio = (int) ($corteAtual['usuario_id_ini'] ?? 0);
            if ($usuarioInicio > 0 && $usuarioInicio !== $usuarioId) {
                return $this->response->setStatusCode(403)->setJSON([
                    'ok' => false,
                    'mensagem' => 'Somente o usuario que iniciou o corte pode confirma-lo.',
                ]);
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $corteId = $this->registrarCorteFinalizado($desenhoAtual, $usuarioId, $ip);
        if ($corteId === null) {
            $db->transRollback();
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Falha ao finalizar o corte do desenho.',
            ]);
        }

        $ok = $desenhoModel->update($desenhoId, [
            'status' => 'pronto',
            'corte_id' => $corteId,
        ]);

        if (!$ok) {
            $db->transRollback();
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Falha ao atualizar o status do desenho.',
            ]);
        }

        $db->transComplete();
        if (!$db->transStatus()) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Falha ao concluir a confirmacao do corte.',
            ]);
        }

        $this->avaliarConclusaoDependenciaDesenho($desenhoId);
        Ferramentas::sincronizarNovasOrdens();

        $pdf = null;
        $desenhoEtiqueta = $this->carregarDesenhoEtiqueta($desenhoId);
        if (is_array($desenhoEtiqueta)) {
            $pdf = $this->gerarEtiquetaPdfBase64($desenhoEtiqueta);
        }

        return $this->response->setJSON([
            'ok' => true,
            'pdf' => $pdf,
            'nome_pdf' => $pdf !== null ? 'etiqueta.pdf' : null,
        ]);
    }


    /**
     * Cancela o processo de corte de um desenho.
     *
     * Esta funÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o ÃƒÆ’Ã‚Â© acionada por uma requisiÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o AJAX para cancelar o corte de um desenho.
     * Ela atualiza o status do corte para 'cancelado' e redefine o status do desenho como 'corte'.
     *
     */
    function cancelar_corte()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'ok' => false,
                'mensagem' => 'Requisicao invalida.',
            ]);
        }

        $this->iniciarSessaoSeNecessario();

        $id = (int) service('request')->getPost('id');
        $justificativa = trim((string) service('request')->getPost('justificativa'));

        if ($id < 0) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Item de corte invalido.',
            ]);
        }

        if ($this->comprimentoTexto($justificativa) < 15) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Informe uma justificativa com pelo menos 15 caracteres para cancelar o corte.',
            ]);
        }

        $desenhoId = (int) ($_SESSION['lista'][$id] ?? 0);
        if ($desenhoId <= 0) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Desenho nao encontrado na lista atual.',
            ]);
        }

        $desenhoModel = new \App\Models\Desenhos();
        $corteModel = new \App\Models\Corte();

        $desenhoAtual = $desenhoModel->where('id', $desenhoId)->first();
        if (!is_array($desenhoAtual) || empty($desenhoAtual['id'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Desenho nao encontrado para cancelar o corte.',
            ]);
        }

        $corteAtual = $corteModel
            ->where('id', (int) ($desenhoAtual['corte_id'] ?? 0))
            ->where('status', 'inicio')
            ->first();

        if (!is_array($corteAtual) || empty($corteAtual['id'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Nao existe corte em processamento para cancelar.',
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $corteAtualizado = $corteModel->update((int) $corteAtual['id'], [
            'usuario_id_fim' => (int) ($_SESSION['usuario'] ?? 0),
            'data_end' => date('Y-m-d H:i:s'),
            'status' => 'cancelado',
        ]);

        if (!$corteAtualizado) {
            $db->transRollback();
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Falha ao cancelar o corte em processamento.',
            ]);
        }

        $desenhoAtualizado = $desenhoModel->update($desenhoId, ['status' => 'pendente']);
        if (!$desenhoAtualizado) {
            $db->transRollback();
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Falha ao devolver o desenho para pendente.',
            ]);
        }

        $auditou = $this->registrarAlteracaoCancelamentoCorte(
            $desenhoId,
            (int) $corteAtual['id'],
            $desenhoAtual,
            $corteAtual,
            $justificativa
        );

        if (!$auditou) {
            $db->transRollback();
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Falha ao auditar o cancelamento do corte.',
            ]);
        }

        $db->transComplete();
        if (!$db->transStatus()) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Falha ao concluir o cancelamento do corte.',
            ]);
        }

        return $this->response->setJSON([
            'ok' => true,
            'mensagem' => 'Corte cancelado com sucesso.',
        ]);

        if ($this->request->isAJAX()) {
            $id = service('request')->getPost('id');
            session_start();

            // Cria uma instÃƒÆ’Ã‚Â¢ncia do modelo de dados 'Corte'
            $corte = new \App\Models\Corte();

            // Busca os dados de corte no banco de dados
            $corte_data = $corte->find();

            // ObtÃƒÆ’Ã‚Â©m o ID do corte que corresponde ao desenho em 'inicio'
            $id_corte = Ferramentas::array_index(Ferramentas::array_pesquisa_mult($corte_data, ['id_desenho', 'status'], [$_SESSION["lista"][$id], 'inicio']), ['id']);

            // Define os dados a serem atualizados para cancelar o pendente
            $update = [
                'cortador_fim' => $_SESSION["usuario"],
                'data_fim' => Ferramentas::codificador(date('d/m/Y H:i:s')),
                'status' => 'cancelado'
            ];

            // Atualiza o registro de corte com o status 'cancelado'
            $corte->update($id_corte, $update);

            // Cria uma instÃƒÆ’Ã‚Â¢ncia do modelo de dados 'Desenhos'
            $desenho = new \App\Models\Desenhos();

            // Define os dados a serem atualizados para definir o status como 'corte'
            $updat = [
                'status' => 'pendente'
            ];

            // Atualiza o registro de desenho para o status 'corte'
            $desenho->update($_SESSION["lista"][$id], $updat);

            // Prepara uma resposta com sucesso
            $data = [
                "ok" => true
            ];

            // Retorna a resposta no formato JSON
            return $this->response->setJSON($data);
        }
    }


    /**
     * FunÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o lista_corte_adm()
     *
     * Esta funÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o ÃƒÆ’Ã‚Â© chamada por meio de uma solicitaÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o AJAX e ÃƒÆ’Ã‚Â© responsÃƒÆ’Ã‚Â¡vel por listar os usuÃƒÆ’Ã‚Â¡rios com status de 'corte' ou 'cortando' em uma tabela.
     *
     * Retorna um JSON contendo a lista de usuÃƒÆ’Ã‚Â¡rios com status de 'corte' ou 'cortando'.
     */
    function lista_tarefas()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'lista' => '',
                'status' => 'pendente',
                'som' => 'false',
                'tipo_processo' => '',
                'mostrar_dimensao_dxf' => false,
                'itens_notificacao' => [],
                'finalidade_pesquisa' => '',
            ]);
        }

        return $this->response->setJSON($this->respostaListaTarefasJsonOtimizada());

        $processoNome = trim((string) service('request')->getPost('processo'));
        $finalidadePesquisa = trim((string) service('request')->getPost('finalidade'));
        if ($finalidadePesquisa === '-1') {
            $finalidadePesquisa = '';
        }
        $proc = $this->buscarProcessoAtivoPorNome($processoNome);
        $som = (string) (new \App\Models\Alteracoes())->latestDetailValueByItem('som_corte', '', 'false');
        if ($som === '') {
            $som = 'false';
        }

        if (!$proc) {
            return $this->response->setJSON([
                'lista' => '',
                'status' => 'pendente',
                'som' => $som,
                'tipo_processo' => '',
                'mostrar_dimensao_dxf' => false,
                'itens_notificacao' => [],
                'finalidade_pesquisa' => $finalidadePesquisa,
            ]);
        }

        $this->iniciarSessaoSeNecessario();

        $desenhosModel = new \App\Models\Desenhos();
        $projetoModel = new \App\Models\Projeto();
        $this->sincronizarOrdensComThrottle(20, (int) ($proc['id'] ?? 0), (string) ($proc['input'] ?? ''));
        $usuarioLogadoId = (int) ($_SESSION['usuario'] ?? 0);
        $desenhosEmCorteUsuarioIds = $this->buscarIdsDesenhosEmCorteDoUsuario($usuarioLogadoId, (int) ($proc['id'] ?? 0));
        $mostrarDimensaoDxf = $this->processoExibeDimensaoDxf((string) ($proc['nome'] ?? $processoNome));

        $lista = '';
        $listaIds = [];
        $listaCompleta = [];
        $listaProjetos = [];
        $itensNotificacao = [];
        $statusResposta = 'pendente';
        $indiceLista = 0;
        $_SESSION["baixar_arquivo_tudo"] = [];
        $_SESSION["baixar_arquivo"] = [];

        if (($proc['input'] ?? '') === 'mult') {
            $rows = $desenhosModel
                ->select("
                    desenhos.*,
                    o.ordem AS ordem,
                    prioridade.ordem AS prioridade_ordem,
                    prioridade.nome AS prioridade_nome,
                    prioridade.cor AS prioridade_cor,
                    empresa.nome AS empresa_nome,
                    empreendimentos.nome AS empreendimento_nome,
                    empreendimentos.escala AS empreendimento_escala,
                    finalidade.nome AS finalidade_nome,
                    usuarios.nome AS usuario_nome
                ")
                ->join('prioridade', 'prioridade.id = desenhos.prioridade_id', 'left')
                ->join('empresa', 'empresa.id = desenhos.empresa_id', 'left')
                ->join('empreendimentos', 'empreendimentos.id = desenhos.empreendimentos_id', 'left')
                ->join('finalidade', 'finalidade.id = desenhos.finalidade_id', 'left')
                ->join('usuarios', 'usuarios.id = desenhos.usuario_id_desenhista', 'left')
                ->join('ordem o', "o.desenho_id = desenhos.id AND o.status = 'ativo'", 'left')
                ->where('desenhos.processos_id', $proc['id'])
                ->whereIn('desenhos.status', ['pendente', 'cortando', 'processando'])
                ->orderBy('o.ordem IS NULL', 'ASC', false)
                ->orderBy('o.ordem', 'ASC')
                ->findAll();

            if ($mostrarDimensaoDxf) {
                $rows = $this->enriquecerDesenhosComDimensoesDxf($rows);
            }

            foreach ($rows as &$row) {
                $this->prepararItemFilaParaOrdenacao($row, $desenhosEmCorteUsuarioIds);
            }
            unset($row);

            usort($rows, [$this, 'compararItensFila']);

            $idsAtivos = $desenhosEmCorteUsuarioIds;
            $possuiCorteAtivo = $idsAtivos !== [];
            if ($possuiCorteAtivo) {
                $statusResposta = 'cortando';
            }

            foreach ($rows as $row) {
                $finalidadeNome = trim((string) ($row['finalidade_nome'] ?? ''));
                if ($finalidadePesquisa !== '' && $finalidadeNome !== $finalidadePesquisa) {
                    continue;
                }

                $tags = $this->extrairTagsDeDiretorio((string) ($row['diretorio'] ?? ''));
                $statusLinha = strtolower(trim($this->normalizarStatusTexto($row['status'] ?? '')));
                $desenhoId = (int) ($row['id'] ?? 0);
                $isCorteAtual = in_array($desenhoId, $idsAtivos, true);
                $ehCorteLaser = $mostrarDimensaoDxf;

                if ($statusLinha === 'processando') {
                    if ($ehCorteLaser) {
                        $botaoAcao = $this->montarAcoesCorteLaser(
                            $indiceLista,
                            '<button type="button" disabled class="btn btn-sm btn-outline-dark wl-row-action-main">Processando...</button>'
                        );
                    } else {
                        $botaoAcao = '<div class="wl-row-actions"><button type="button" disabled class="btn btn-sm btn-outline-dark wl-row-action-main">Processando...</button></div>';
                    }
                    $botaoConfirmar = '<div class="wl-row-actions wl-row-actions--confirm"><button type="button" disabled class="btn btn-sm btn-outline-dark">Finalizar</button></div>';
                } elseif ($statusLinha === 'cortando') {
                    if ($isCorteAtual) {
                        $nomeParaCancelar = Ferramentas::remove_id_file($this->decodificarValorAuditoria($row['nome'] ?? ''));
                        $itensMenuCorteAtivo = [];
                        if ($ehCorteLaser) {
                            $itensMenuCorteAtivo[] = [
                                'rotulo' => 'Cancelar corte',
                                'onclick' => 'cancelar_corte(' . $indiceLista . ', ' . json_encode($nomeParaCancelar, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ')',
                                'icone' => 'ri-close-circle-line',
                                'tooltip' => 'Cancelar o corte atual',
                                'classe' => 'text-danger',
                            ];
                        }

                        if ($ehCorteLaser) {
                            $botaoAcao = $this->montarAcoesCorteLaser(
                                $indiceLista,
                                '<button type="button" onclick="mostrar_caminho_corte_atual(' . $indiceLista . ')" class="btn btn-sm btn-outline-primary wl-row-action-main">Em corte</button>',
                                true,
                                $itensMenuCorteAtivo
                            );
                        } else {
                            $botaoAcao = '<div class="wl-row-actions">'
                                . '<button type="button" onclick="ver_dxf(' . $indiceLista . ')" class="btn btn-sm btn-outline-info wl-row-action-main">Ver</button>'
                                . '<button type="button" onclick="buscarArquivos(' . $indiceLista . ')" class="btn btn-sm btn-outline-primary">Baixar</button>'
                                . '</div>';
                        }
                        $nomeConfirmacao = htmlspecialchars(Ferramentas::remove_id_file((string) ($row['nome'] ?? '')), ENT_QUOTES, 'UTF-8');
                        if ($ehCorteLaser) {
                            $botaoConfirmar = '<div class="wl-row-actions wl-row-actions--confirm"><button name="cadastarar" type="button" onclick="confirmar(\'' . $indiceLista . '\',\'' . $nomeConfirmacao . '\')" class="btn btn-sm btn-success">Finalizar</button></div>';
                        } else {
                            $botaoConfirmar = '<div class="wl-row-actions wl-row-actions--confirm"><button name="cadastarar" type="button" onclick="confirmar(\'' . $indiceLista . '\',\'' . $nomeConfirmacao . '\')" class="btn btn-sm btn-success">Confirmar Corte</button></div>';
                        }
                    } else {
                        if ($ehCorteLaser) {
                            $botaoAcao = $this->montarAcoesCorteLaser(
                                $indiceLista,
                                '<button type="button" disabled class="btn btn-sm btn-outline-dark wl-row-action-main">Em corte</button>'
                            );
                        } else {
                            $botaoAcao = '<div class="wl-row-actions"><button type="button" disabled class="btn btn-sm btn-outline-dark wl-row-action-main">Processando...</button></div>';
                        }
                        $botaoConfirmar = '<div class="wl-row-actions wl-row-actions--confirm"><button type="button" disabled class="btn btn-sm btn-outline-dark">' . ($ehCorteLaser ? 'Finalizar' : 'Confirmar Corte') . '</button></div>';
                    }
                } elseif ($possuiCorteAtivo) {
                    if ($ehCorteLaser) {
                        $botaoAcao = $this->montarAcoesCorteLaser(
                            $indiceLista,
                            '<button type="button" disabled class="btn btn-sm btn-outline-dark wl-row-action-main">Inicializar</button>'
                        );
                    } else {
                        $botaoAcao = '<div class="wl-row-actions"><button type="button" disabled class="btn btn-sm btn-outline-dark wl-row-action-main">Cortar</button></div>';
                    }
                    $botaoConfirmar = '<div class="wl-row-actions wl-row-actions--confirm"><button type="button" disabled class="btn btn-sm btn-outline-dark">' . ($ehCorteLaser ? 'Finalizar' : 'Confirmar Corte') . '</button></div>';
                } else {
                    $nomeConfirmacao = htmlspecialchars(Ferramentas::remove_id_file((string) ($row['nome'] ?? '')), ENT_QUOTES, 'UTF-8');
                    if ($ehCorteLaser) {
                        $botaoAcao = $this->montarAcoesCorteLaser(
                            $indiceLista,
                            '<button type="button" onclick="cortar(' . $indiceLista . ')" class="btn btn-sm btn-primary wl-row-action-main">Inicializar</button>'
                        );
                        $botaoConfirmar = '<div class="wl-row-actions wl-row-actions--confirm"><button name="cadastarar" type="button" onclick="confirmar(\'' . $indiceLista . '\',\'' . $nomeConfirmacao . '\')" class="btn btn-sm btn-success">Finalizar</button></div>';
                    } else {
                        $botaoAcao = '<div class="wl-row-actions"><button type="button" onclick="cortar(' . $indiceLista . ')" class="btn btn-sm btn-primary wl-row-action-main">Cortar</button></div>';
                        $botaoConfirmar = '<div class="wl-row-actions wl-row-actions--confirm"><button name="cadastarar" type="button" onclick="confirmar(\'' . $indiceLista . '\',\'' . $nomeConfirmacao . '\')" class="btn btn-sm btn-success">Confirmar Corte</button></div>';
                    }
                }

                $nomeArquivo = Ferramentas::remove_id_file($this->decodificarValorAuditoria($row['nome'] ?? ''));
                $nomeArquivoHtml = htmlspecialchars($nomeArquivo, ENT_QUOTES, 'UTF-8');
                $empresaNomeHtml = htmlspecialchars((string) ($row['empresa_nome'] ?? ''), ENT_QUOTES, 'UTF-8');
                $empreendimentoNomeHtml = $this->montarEmpreendimentoComEscalaHtml(
                    $row['empreendimento_nome'] ?? '',
                    $row['empreendimento_escala'] ?? ''
                );
                $finalidadeNomeHtml = htmlspecialchars($finalidadeNome, ENT_QUOTES, 'UTF-8');
                $usuarioNomeHtml = htmlspecialchars((string) ($row['usuario_nome'] ?? ''), ENT_QUOTES, 'UTF-8');
                $tagsHtml = htmlspecialchars($tags, ENT_QUOTES, 'UTF-8');
                $ordemHtml = htmlspecialchars((string) ($row['ordem'] ?? ''), ENT_QUOTES, 'UTF-8');

                $colunaDimensao = '';
                if ($mostrarDimensaoDxf) {
                    $dimensaoTexto = trim((string) ($row['dimensao_dxf'] ?? ''));
                    if ($dimensaoTexto === '') {
                        $dimensaoTexto = '-';
                    }
                    $colunaDimensao = '<td class="text-center text-nowrap wl-col-dimensao-dxf">' . htmlspecialchars($dimensaoTexto, ENT_QUOTES, 'UTF-8') . '</td>';
                }

                $lista .= '<tr>'
                    . '<td bgcolor="' . htmlspecialchars((string) ($row['prioridade_cor'] ?? '#ffffff'), ENT_QUOTES, 'UTF-8') . '"><span class="marca_texto">' . htmlspecialchars((string) ($row['prioridade_nome'] ?? ''), ENT_QUOTES, 'UTF-8') . '</span></td>'
                    . '<td>' . $ordemHtml . '</td>'
                    . '<td>' . $usuarioNomeHtml . '</td>'
                    . '<td><span class="wl-cell-truncate" title="' . $nomeArquivoHtml . '">' . $nomeArquivoHtml . '</span></td>'
                    . '<td>' . $empresaNomeHtml . '</td>'
                    . '<td>' . $empreendimentoNomeHtml . '</td>'
                    . '<td>' . $finalidadeNomeHtml . '</td>'
                    . '<td><span class="wl-cell-truncate" title="' . $tagsHtml . '">' . $tagsHtml . '</span></td>'
                    . $colunaDimensao
                    . '<td>' . Ferramentas::formatarDataHora((string) ($row['data_add'] ?? '')) . '</td>'
                    . '<td>' . $botaoAcao . '</td>'
                    . '<td>' . $botaoConfirmar . '</td>'
                    . '</tr>';

                $rowOriginalId = $desenhoId;
                $row['id'] = $indiceLista;
                $row['tags'] = $tags;
                $listaIds[$indiceLista] = $rowOriginalId;
                $listaCompleta[$indiceLista] = $row;
                $_SESSION["baixar_arquivo_tudo"][$indiceLista] = $row;
                $_SESSION["baixar_arquivo"][$indiceLista] = $row['diretorio'] ?? '';
                $itensNotificacao[] = [
                    'item_id' => 'desenho_' . $rowOriginalId,
                    'processo' => (string) ($proc['nome'] ?? $processoNome),
                    'projetista' => trim((string) ($row['usuario_nome'] ?? '')),
                    'desenho' => $nomeArquivo,
                ];
                $indiceLista++;
            }

            $_SESSION['lista'] = $listaIds;
            $_SESSION['lista_completa'] = $listaCompleta;
            $_SESSION['lista_primordial'] = $listaCompleta;
        } else {
            $linhasProjeto = $this->buscarLinhasProjetosLista(
                (int) ($proc['id'] ?? 0),
                $desenhosEmCorteUsuarioIds,
                $projetoModel
            );

            foreach ($linhasProjeto as $linhaProjeto) {
                $projetoId = (int) ($linhaProjeto['projeto_id'] ?? 0);
                $projetoAtual = is_array($linhaProjeto['projeto'] ?? null) ? $linhaProjeto['projeto'] : [];
                $desenhoLinha = is_array($linhaProjeto['desenho'] ?? null) ? $linhaProjeto['desenho'] : [];
                $todosDesenhosProjeto = is_array($linhaProjeto['todos_desenhos'] ?? null) ? $linhaProjeto['todos_desenhos'] : [];
                [$totalArquivosProjeto, $arquivosBaixadosProjeto] = $this->contarArquivosBaixadosProjeto($todosDesenhosProjeto);
                $finalidadeNome = trim((string) ($desenhoLinha['finalidade_nome'] ?? ''));

                if ($finalidadePesquisa !== '' && $finalidadeNome !== $finalidadePesquisa) {
                    continue;
                }

                $tags = $this->extrairTagsDeDiretorio((string) ($desenhoLinha['diretorio'] ?? ''));
                $statusLinha = strtolower(trim($this->normalizarStatusTexto($desenhoLinha['status'] ?? '')));

                if ($statusLinha === 'processando') {
                    $botaoAcao = '<div class="wl-row-actions"><button type="button" disabled class="btn btn-sm btn-outline-dark wl-row-action-main">Processando...</button></div>';
                    $botaoConfirmar = '<div class="wl-row-actions wl-row-actions--confirm"><button type="button" disabled class="btn btn-sm btn-outline-dark">Finalizar</button></div>';
                } else {
                    $rotuloBaixarProjeto = htmlspecialchars($this->rotuloBotaoBaixarProjeto($totalArquivosProjeto, $arquivosBaixadosProjeto), ENT_QUOTES, 'UTF-8');
                    $botaoAcao = '<div class="wl-row-actions"><button type="button" onclick="baixar(' . $indiceLista . ')" class="btn btn-sm btn-primary wl-row-action-main">' . $rotuloBaixarProjeto . '</button></div>';
                    $botaoConfirmar = '<div class="wl-row-actions wl-row-actions--confirm"><button name="cadastarar" type="button" onclick="confirmar_ind(\'' . $indiceLista . '\',\'\')" class="btn btn-sm btn-success">Finalizar</button></div>';
                }

                $descricaoProjeto = trim((string) ($projetoAtual['descricao'] ?? ''));
                $descricaoProjetoHtml = htmlspecialchars($descricaoProjeto, ENT_QUOTES, 'UTF-8');
                $empresaNomeHtml = htmlspecialchars((string) ($desenhoLinha['empresa_nome'] ?? ''), ENT_QUOTES, 'UTF-8');
                $empreendimentoNomeHtml = $this->montarEmpreendimentoComEscalaHtml(
                    $desenhoLinha['empreendimento_nome'] ?? '',
                    $desenhoLinha['empreendimento_escala'] ?? ''
                );
                $finalidadeNomeHtml = htmlspecialchars($finalidadeNome, ENT_QUOTES, 'UTF-8');
                $usuarioNomeHtml = htmlspecialchars((string) ($desenhoLinha['usuario_nome'] ?? ''), ENT_QUOTES, 'UTF-8');
                $tagsHtml = htmlspecialchars($tags, ENT_QUOTES, 'UTF-8');
                $ordemHtml = htmlspecialchars((string) ($projetoAtual['ordem'] ?? ''), ENT_QUOTES, 'UTF-8');
                $statusHtml = htmlspecialchars((string) ($desenhoLinha['status'] ?? ''), ENT_QUOTES, 'UTF-8');

                $lista .= '<tr>'
                    . '<td bgcolor="' . htmlspecialchars((string) ($desenhoLinha['prioridade_cor'] ?? '#ffffff'), ENT_QUOTES, 'UTF-8') . '"><span class="marca_texto">' . htmlspecialchars((string) ($desenhoLinha['prioridade_nome'] ?? ''), ENT_QUOTES, 'UTF-8') . '</span></td>'
                    . '<td>' . $ordemHtml . '</td>'
                    . '<td>' . $usuarioNomeHtml . '</td>'
                    . '<td><span class="wl-cell-truncate" title="' . $descricaoProjetoHtml . '">' . $descricaoProjetoHtml . '</span></td>'
                    . '<td>' . $empresaNomeHtml . '</td>'
                    . '<td>' . $empreendimentoNomeHtml . '</td>'
                    . '<td>' . $finalidadeNomeHtml . '</td>'
                    . '<td><span class="wl-cell-truncate" title="' . $tagsHtml . '">' . $tagsHtml . '</span></td>'
                    . '<td>' . $statusHtml . '</td>'
                    . '<td>' . Ferramentas::formatarDataHora((string) ($desenhoLinha['data_add'] ?? '')) . '</td>'
                    . '<td>' . $botaoAcao . '</td>'
                    . '<td>' . $botaoConfirmar . '</td>'
                    . '</tr>';

                $desenhoOriginalId = (int) ($desenhoLinha['id'] ?? 0);
                $desenhoLinha['id'] = $indiceLista;
                $desenhoLinha['tags'] = $tags;
                $desenhoLinha['projeto_id'] = $projetoId;
                $listaIds[$indiceLista] = $desenhoOriginalId;
                $listaCompleta[$indiceLista] = $desenhoLinha;
                $listaProjetos[$indiceLista] = $todosDesenhosProjeto;
                $itensNotificacao[] = [
                    'item_id' => 'projeto_' . $projetoId,
                    'processo' => (string) ($proc['nome'] ?? $processoNome),
                    'projetista' => trim((string) ($desenhoLinha['usuario_nome'] ?? '')),
                    'desenho' => $descricaoProjeto,
                ];
                $indiceLista++;
            }

            $_SESSION['lista'] = $listaIds;
            $_SESSION['lista_completa'] = $listaCompleta;
            $_SESSION['lista_projetos'] = $listaProjetos;
            $_SESSION['projeto_todos'] = $listaProjetos;
        }

        return $this->response->setJSON([
            'lista' => $lista,
            'status' => $statusResposta,
            'som' => $som,
            'tipo_processo' => (string) ($proc['input'] ?? ''),
            'mostrar_dimensao_dxf' => $mostrarDimensaoDxf,
            'itens_notificacao' => $itensNotificacao,
            'finalidade_pesquisa' => $finalidadePesquisa,
            'ordens_sincronizadas' => true,
        ]);
    }

    function lista_corte_adm() //rece um post via ajax pedindo para listar os usuarios
    {
        if ($this->request->isAJAX()) {

            // Ferramentas::desativarOrdems();
            Ferramentas::ordenarOrdems();
            Ferramentas::ordenarOrdems();

            //Ferramentas::reordenarPorPrioridade(22548,3,1,1);
            $nome_processos = service('request')->getPost('nome_processos');

            // session_start();
            // InicializaÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o de objetos para acessar tabelas do banco de dados
            $desenhos = new \App\Models\Desenhos();
            $prioridade = new \App\Models\Prioridade();
            $finalidade = new \App\Models\Finalidade();
            $empresa = new \App\Models\Empresa();
            $empreendimento = new \App\Models\Empreendimentos();
            $usuario = new \App\Models\Usuarios();
            $processos = new \App\Models\Processos();



            session_start();
            $desenhos_data = $desenhos->find();
            $proc = (new \App\Models\Processos())
                ->where('nome', $nome_processos)
                ->where('status', 'ativo')
                ->first();

            if (!$proc) {
                return $this->response->setJSON([
                    "lista" => "",
                    "check" => (string) service('request')->getPost('check'),
                ]);
            }

            $check = service('request')->getPost('check'); // ObtÃƒÆ’Ã‚Â©m a informaÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o POST fornecida via AJAX para listar usuÃƒÆ’Ã‚Â¡rios ativos


            $lista = "";
            $id_temp = 0;
            $lista_ids = array();
            $lista_completa = array();
            $p = array();


            $desenhos_data = $desenhos
                ->select("
        desenhos.*,
        o.ordem AS ordem
    ")
                ->join(
                    'ordem o',
                    "o.desenho_id = desenhos.id AND o.status = 'ativo'",
                    'left'
                )
                // ** filtra apenas pelo processo que o usuÃƒÆ’Ã‚Â¡rio escolheu **
                ->where('desenhos.processos_id', $proc['id'])
                ->whereIn('desenhos.status', [
                    Ferramentas::codificador('pendente'),
                    Ferramentas::codificador('cortando'),
                    Ferramentas::codificador('processando'),
                ])
                ->orderBy('o.ordem IS NULL', 'ASC', false)
                ->orderBy('o.ordem', 'ASC')
                ->findAll();


            $i = 0;
            if ($proc['input'] == 'mult') {
                $ids = array();
                // Itera sobre os dados de desenhos para criar a lista
                foreach ($desenhos_data as $key => $value) {
                    if (in_array($value['id'], $ids))
                        continue;
                    $ids[] = $value['id'];

                    $prioridade_desenho = $prioridade->where('id', $value['prioridade_id'])->first();
                    $processos_nome = $processos->where('id', $value['processos_id'])->first()['nome'];
                    $usuario_nome = $usuario->where('id', $value['usuario_id_desenhista'])->first()['nome'];
                    $empresa_nome = $empresa->where('id', $value['empresa_id'])->first()['nome'];
                    $empreendimento_registro = $empreendimento->where('id', $value['empreendimentos_id'])->first();
                    $empreendimento_nome = is_array($empreendimento_registro) ? ($empreendimento_registro['nome'] ?? '') : '';
                    $empreendimento_escala = is_array($empreendimento_registro) ? ($empreendimento_registro['escala'] ?? '') : '';
                    $empreendimento_html = $this->montarEmpreendimentoComEscalaHtml($empreendimento_nome, $empreendimento_escala);
                    $finalidade_nome = $finalidade->where('id', $value['finalidade_id'])->first()['nome'];
                    $cort_hora_add = Ferramentas::array_index($finalidade->where('id', $value['corte_id'])->first(), ['hora_add']);

                    $tags = explode('/', ($value['diretorio']));

                    // Remover os ÃƒÆ’Ã‚Â­ndices de 0 a 5
                    $tags = array_slice($tags, 6);

                    // Remover o ÃƒÆ’Ã‚Âºltimo elemento
                    unset($tags[count($tags) - 1]);
                    $tags = implode(" . ", $tags);




                    // Monta a linha da tabela com os dados do usuÃƒÆ’Ã‚Â¡rio
                    $statusLinhaAdm = $this->normalizarStatusTexto($value['status'] ?? '');
                    if ($statusLinhaAdm == 'pendente') {
                        $lista .= '<tr><td onclick="prio_modal(' . $id_temp . ')" bgcolor="' . $prioridade_desenho['cor'] . '"><span class="marca_texto">' . $prioridade_desenho['nome'] . '</span></td>';
                    } else {
                        $lista .= '<tr><td bgcolor="' . $prioridade_desenho['cor'] . '"><span class="marca_texto">' . $prioridade_desenho['nome'] . '</span></td>';
                    }

                    $lista .= '
                <td>' . $value['ordem'] . ' </td>
                <td>' . $usuario_nome . '</td>
                <td>' . Ferramentas::remove_id_file($value['nome']) . '</td>
                <td>' . $empresa_nome . '</td>
                <td>' . $empreendimento_html . '</td>
                <td>' . $finalidade_nome . '</td>
                <td>' . $tags . '</td>
                <td>' . $value['status'] . '</td>
                <td>' . Ferramentas::formatarDataHora($cort_hora_add) . '</td>
                <td>' . Ferramentas::formatarDataHora($value['data_add']) . '</td>
                 ';


                    if ($statusLinhaAdm == 'pendente') {
                        $lista .= '<td><button name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="apagar(' . $id_temp . ')"> Apagar </button></td>';
                    } else if ($statusLinhaAdm == 'cortando') {
                        $lista .= '<td><button name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="cancelar_corte(' . $id_temp . ')"> Cancelar corte </button></td> ';
                    } else {
                        $lista .= '<td></td>';
                    }
                    $lista .= '<td><button name="cadastarar" type="submit" class="btn btn-outline-warning" onclick="prio_modal(' . $id_temp . ')"> Mudar prioridade </button></td></tr>';


                    // Prepara dados do usuÃƒÆ’Ã‚Â¡rio para armazenamento em arrays
                    $value['cor'] = $prioridade_desenho['cor'];
                    $value['finalidade'] = $finalidade_nome;
                    $value['empresa'] = $empresa_nome;
                    $value['empreendimento'] = $empreendimento_nome;
                    $value['empreendimento_escala'] = $empreendimento_escala;
                    $value['prioridade'] = $prioridade_desenho['nome'];

                    $value['desenhista_nome'] = $usuario_nome;
                    $value['tags'] = $tags;
                    $value['processo'] = $processos_nome;
                    $value['ordem'] = $value['ordem'];
                    $lista_ids[$id_temp] = $value['id'];
                    $value['id'] = $id_temp;
                    $value['tags'] = $tags;
                    $lista_completa[$id_temp] = $value;
                    $id_temp++;
                }

                // Inicializa a sessÃƒÆ’Ã‚Â£o e armazena as listas

                $_SESSION["lista"] = $lista_ids;
                $_SESSION["lista_completa"] = $lista_completa;
                $_SESSION["lista_primordial"] = $lista_completa;
            } else if ($proc['input'] == 'ind') {

                $lista_completa = [];
                $lista_ids = [];

                $html = array();

                $desBuilder = (new \App\Models\Projeto())
                    ->select('p.*,o.ordem AS ordem') // traz sÃƒÆ’Ã‚Â³ os campos de ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œdesenhosÃƒÂ¢Ã¢â€šÂ¬Ã‚Â
                    ->from('projeto p')
                    ->join('projeto_desenho pd', 'pd.projeto_id = p.id', 'LEFT')
                    ->join('desenhos d', 'd.id          = pd.desenho_id', 'LEFT')
                    ->join('ordem o', 'p.id          = o.projeto_id AND o.status = "ativo"', 'LEFT')
                    ->whereIn('p.status', ['ativo', 'processando'])
                    ->where('d.processos_id', $proc['id'])
                    ->findAll();
                $ids = array();
                foreach ($desBuilder as $key => $value) {
                    if (in_array($value['id'], $ids))
                        continue;
                    $ids[] = $value['id'];
                    $projeto_desenhos = (new \App\Models\Projeto_desenho())
                        ->where('projeto_id', $value['id'])
                        ->orderBy('data_add', 'ASC')   // opcional: garantir o ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œprimeiroÃƒÂ¢Ã¢â€šÂ¬Ã‚Â por data
                        ->first();



                    if (!$projeto_desenhos)
                        continue;
                    $projeto_desenhos_todos = (new \App\Models\Projeto_desenho())
                        ->where('projeto_id', $value['id'])
                        ->orderBy('data_add', 'ASC')   // opcional: garantir o ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œprimeiroÃƒÂ¢Ã¢â€šÂ¬Ã‚Â por data
                        ->findAll();
                    [$totalArquivosProjeto, $arquivosBaixadosProjeto] = $this->contarArquivosBaixadosProjeto($projeto_desenhos_todos);

                    $d = (new \App\Models\Desenhos())
                        ->select("
                            desenhos.*,
                            o.ordem              AS ordem,
                            prioridade.nome      AS prioridade_nome,
                            prioridade.cor       AS prioridade_cor,
                            empresa.nome         AS empresa_nome,
                            empreendimentos.nome AS empreendimento_nome,
                            empreendimentos.escala AS empreendimento_escala,
                            finalidade.nome      AS finalidade_nome,
                            usuarios.nome        AS usuario_nome,
                            corte.data_end       AS corte_data_end,
                            corte.status         AS corte_status
                        ")
                        ->join('prioridade', 'prioridade.id        = desenhos.prioridade_id', 'left')
                        ->join('empresa', 'empresa.id           = desenhos.empresa_id', 'left')
                        ->join('empreendimentos', 'empreendimentos.id   = desenhos.empreendimentos_id', 'left')
                        ->join('finalidade', 'finalidade.id        = desenhos.finalidade_id', 'left')
                        ->join('usuarios', 'usuarios.id          = desenhos.usuario_id_desenhista', 'left')
                        ->join('corte', 'corte.id             = desenhos.corte_id', 'left')
                        // aqui: puxa sÃƒÆ’Ã‚Â³ a ordem ATIVA para este desenho
                        ->join(
                            'ordem o',
                            "o.desenho_id = desenhos.id AND o.status = 'ativo'",
                            'left'
                        )
                        ->where('desenhos.id', $projeto_desenhos['desenho_id'])
                        ->orderBy('prioridade.ordem', 'ASC')
                        ->orderBy('o.ordem', 'ASC')
                        ->first();

                // extrai tags
                $parts = array_filter(explode('/', $d['diretorio']));
                $tags = implode(' - ', array_slice($parts, 6, -1));
                $statusLinha = $this->normalizarStatusTexto($d['status'] ?? '');

                if ($statusLinha === 'processando') {
                    $cBtn = '<div class="wl-row-actions"><button type="button" disabled class="btn btn-sm btn-outline-dark wl-row-action-main">Processando...</button></div>';
                    $confBtn = '<div class="wl-row-actions wl-row-actions--confirm"><button type="button" disabled class="btn btn-sm btn-outline-dark">Finalizar</button></div>';
                } else {
                    $rotuloBaixarProjeto = htmlspecialchars($this->rotuloBotaoBaixarProjeto($totalArquivosProjeto, $arquivosBaixadosProjeto), ENT_QUOTES, 'UTF-8');
                    $cBtn = '<div class="wl-row-actions"><button type="button" onclick="baixar(' . $i . ')" class="btn btn-sm btn-primary wl-row-action-main">' . $rotuloBaixarProjeto . '</button></div>';
                    $confBtn = '<div class="wl-row-actions wl-row-actions--confirm"><button name="cadastarar" type="button" onclick="confirmar_ind(\'' . $i . '\',\'\')" class="btn btn-sm btn-success">Finalizar</button></div>';
                }

                $descricaoNotificacao = trim((string) ($value['descricao'] ?? ''));
                $nomeArquivoOriginal = Ferramentas::remove_id_file($this->decodificarValor($d['nome'] ?? ''));
                $itensNotificacao[] = [
                    'item_id' => 'projeto_' . intval($value['id']),
                    'processo' => (string) ($proc['nome'] ?? $procName),
                    'projetista' => trim((string) ($d['usuario_nome'] ?? '')),
                    'desenho' => $descricaoNotificacao,
                ];

                $descricaoExibicao = htmlspecialchars($descricaoNotificacao, ENT_QUOTES, 'UTF-8');
                $tagsExibicao = htmlspecialchars($tags, ENT_QUOTES, 'UTF-8');

                // monta a linha
                $html[] = "
                <tr>
                    <td bgcolor=\"{$d['prioridade_cor']}\"><span class=\"marca_texto\">{$d['prioridade_nome']}</span></td>
                    <td>" . $value['ordem'] . "</td>
                    <td>{$d['usuario_nome']}</td>
                    <td><span class=\"wl-cell-truncate\" title=\"{$descricaoExibicao}\">{$descricaoExibicao}</span></td>
                    <td>{$d['empresa_nome']}</td>
                    <td>" . $this->montarEmpreendimentoComEscalaHtml($d['empreendimento_nome'] ?? '', $d['empreendimento_escala'] ?? '') . "</td>
                    <td>{$d['finalidade_nome']}</td>
                    <td><span class=\"wl-cell-truncate\" title=\"{$tagsExibicao}\">{$tagsExibicao}</span></td>
                    <td>{$d['status']}</td>
                    <td>" . date('d/m/Y H:i:s', strtotime($d['data_add'])) . "</td>
                    <td>{$cBtn}</td>
                    <td>{$confBtn}</td>
                </tr>";

                // preenche arrays de sessÃƒÆ’Ã‚Â£o
                $p[$i] = $projeto_desenhos_todos;
                $lista_ids[$i] = $d['id'];
                $d['id'] = $i;
                $d['tags'] = $tags;
                $d['item_tipo'] = 'projeto';
                $d['projeto_id'] = (int) ($value['id'] ?? 0);
                $d['projeto_descricao'] = $descricaoNotificacao;
                $d['nome_arquivo'] = $nomeArquivoOriginal;
                $d['nome'] = $descricaoNotificacao;
                $d['prioridade'] = $d['prioridade_nome'] ?? '';
                $d['cor'] = $d['prioridade_cor'] ?? '';
                $d['empresa'] = $d['empresa_nome'] ?? '';
                $d['empreendimento'] = $d['empreendimento_nome'] ?? '';
                $d['empreendimento_escala'] = $d['empreendimento_escala'] ?? '';
                $d['finalidade'] = $d['finalidade_nome'] ?? '';
                $d['desenhista_nome'] = $d['usuario_nome'] ?? '';

                $lista_completa[$i] = $d;
                $lista_completa[$i]['idrs'] = $d;
                $i++;
            }
            $html = implode('', $html);


            $lista_completa['tipo'] = 'ind';
            $p['processos_id'] = (int) ($proc['id'] ?? 0);
            $_SESSION["lista"] = $lista_ids;
            $_SESSION["lista_completa"] = $lista_completa;
        }
        $_SESSION["lista_projetos"] = $p;
        $_SESSION["projeto_todos"] = $p;
        // 9) Retorna JSON
        return $this->response->setJSON([
            'lista' => $html,
            'status' => $isBeingCut ? 'cortando' : 'pendente',
            'som' => $som,
            'tipo_processo' => $proc['input'],
            'mostrar_dimensao_dxf' => $mostrarDimensaoDxf,
            'itens_notificacao' => $itensNotificacao,
            '1' => Ferramentas::ordenarOrdems2()
        ]);
    }
    }

public function ver_desenho()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON([
            'status' => false,
            'msg'    => 'Requisição inválida'
        ]);
    }

    $id = $this->request->getPost('id');
    $tipo = $this->request->getPost('tipo') ?? "";

    session_start();
    if($tipo == 'projeto'){
     $lista = $_SESSION["baixar_arquivo_tudo"] ?? [];
     $novaEntrada =  $lista[$id];
    }else{
         $lista = $_SESSION["lista_completa"] ?? [];
         $novaEntrada = Ferramentas::array_pesquisa($lista, 'id', $id);
    }
   
    if (!$novaEntrada) {
        return $this->response->setJSON([
            'status' => false,
            'msg'    => 'Desenho não encontrado na lista'
        ]);
    }

    $caminhoExibicao = $this->montarCaminhoExibicaoLista($novaEntrada);
    $nome = Ferramentas::decodificador($novaEntrada['nome']);
    $caminho     = dirname($novaEntrada['diretorio']) . DIRECTORY_SEPARATOR;
    $nomeArquivo = basename($novaEntrada['diretorio']);
    $nome_ajuste1 = "";
    $nome_ajuste2 = "";

    // Verifica possÃƒÆ’Ã‚Â­veis nomes no disco (igual ao seu)
    if (!file_exists($caminho . $novaEntrada['nome'])) {
        if (!file_exists($caminho . $nomeArquivo)) {

            // Inserir ponto apÃƒÆ’Ã‚Â³s ÃƒÆ’Ã‚Âºltimo "_" se nÃƒÆ’Ã‚Â£o tiver extensÃƒÆ’Ã‚Â£o
            if (strpos($nomeArquivo, '.') === false) {
                $pos = strrpos($nomeArquivo, '_');
                if ($pos !== false) {
                    $nome_ajuste1 = substr_replace($nomeArquivo, '.', $pos + 1, 0);
                }
            }
            if (!file_exists($caminho . $nome_ajuste1)) {
                if (strpos($novaEntrada['nome'], '.') === false) {
                    $pos = strrpos($novaEntrada['nome'], '_');
                    if ($pos !== false) {
                        $nome_ajuste2 = substr_replace($novaEntrada['nome'], '.', $pos + 1, 0);
                    }
                }

                if (!file_exists($caminho . $nome_ajuste2)) {
                    return $this->response->setJSON([
                        'status' => false,
                        'msg'    => 'Arquivo não encontrado em nenhuma das variações de nome',
                        'caminho' => $caminhoExibicao,
                        'tentativas' => [
                            $caminho . $novaEntrada['nome'],
                            $caminho . $nomeArquivo,
                            $caminho . $nome_ajuste1,
                            $caminho . $nome_ajuste2,
                        ],
                        'original' => $novaEntrada['diretorio'],
                    ]);
                } else {
                    $nomeArquivo = $nome_ajuste2;
                }
            } else {
                $nomeArquivo = $nome_ajuste1;
            }
        }
    } else {
        $nomeArquivo = $novaEntrada['nome'];
    }

    // Remove prefixo cortado_DD_MM_AAAA__HH_MM_
    $s = preg_replace('/^cortado_\d{2}_\d{2}_\d{4}__\d{2}_\d{2}_/', '', $nomeArquivo);

    // Extrai nome + extensÃƒÆ’Ã‚Â£o (inclusive casos achatados _123_456_stl)
    if (preg_match('/^(.*?)(?:_[0-9_]+_([a-z0-9]+))$/i', $s, $m)) {
        $nomeFinal = trim($m[1]);
        $extensao  = '.' . strtolower($m[2]);
    } else {
        $nomeFinal = trim($s);
        $ext       = pathinfo($s, PATHINFO_EXTENSION);
        $extensao  = $ext ? '.' . strtolower($ext) : '';
    }

    $caminhoCompleto = $caminho . $nomeArquivo;
    $caminhoCompleto = str_replace(['\\','//'],'/', $caminhoCompleto);

    if (!file_exists($caminhoCompleto)) {
        return $this->response->setJSON([
            'status' => false,
            'msg'    => 'Arquivo não encontrado no caminho final',
            'caminho' => $caminhoExibicao,
            'arquivo'=> $caminhoCompleto
        ]);
    }

    // LÃƒÆ’Ã‚Âª e codifica
    $conteudo        = file_get_contents($caminhoCompleto);
    $conteudoBase64  = base64_encode($conteudo);

    // >>>> ALTERAÃƒÆ’Ã¢â‚¬Â¡ÃƒÆ’Ã†â€™O CHAVE: define o campo pela extensÃƒÆ’Ã‚Â£o real <<<<
    $ext = ltrim($extensao, '.'); // ex: 'dxf', 'stl', 'slt'
    $campoConteudo = 'arquivo';   // fallback genérico

    if ($ext === 'dxf') {
        $campoConteudo = 'dxf';
    } elseif ($ext === 'stl' || $ext === 'slt') {
        // use 'slt' para casar com seu JS que checa response.slt || response.stl
        $campoConteudo = 'slt';
    }

    return $this->response->setJSON([
        'status'   => true,
        'nome'     => $nomeFinal . $extensao, // exibe com a extensão correta
        $campoConteudo => $conteudoBase64,     // <-- chave dinâmica: dxf | slt | arquivo
        'caminho1' => $caminhoCompleto,        // debug opcional
        'ext'      => $ext                      // debug opcional
    ]);
}




    

    function baixar_projeto()
    {
        if ($this->request->isAJAX()) {
            // ObtÃƒÆ’Ã‚Â©m o ID do desenho a ser cortado
            $id = service('request')->getPost('id');
            session_start();
            // $_SESSION['confirmar_corte_proc'] = isset ($_SESSION['confirmar_corte_proc']) ? $_SESSION['confirmar_corte_proc'] : FALSE;
            // if ($_SESSION['confirmar_corte_proc']) {
            //     return;
            // } 
            $projetoIdPost = (int) service('request')->getPost('projeto_id');
            $array = $projetoIdPost > 0
                ? $this->buscarItensProjetoPorId($projetoIdPost)
                : $this->obterProjetoDaSessaoPorIndice($id);
            if (!is_array($array) || $array === []) {
                return $this->response->setJSON([
                    'ok' => false,
                    'mensagem' => 'Projeto nao encontrado na lista atual para baixar.'
                ]);
            }

            $projetoId = $projetoIdPost > 0 ? $projetoIdPost : (int) ($array[0]['projeto_id'] ?? 0);
            $contextoProjeto = $this->obterContextoProjetoPorId($projetoId);
            $_SESSION["baixar_arquivo_tudo"] = [];
            $_SESSION["baixar_arquivo"] = [];
            $_SESSION["marcar_arquivo"] = [];
            // $arquivos = array();
            // $corte = new \App\Models\Corte();
            // $desenho = new \App\Models\Desenhos();
            // Salva o inicio do corte

            // $input = [
            //     'usuario_id_ini' => $_SESSION["usuario"],
            //     'ip' => $_SERVER['REMOTE_ADDR'],
            //     'status' => 'inicio'
            // ];
            // $corte->insert($input);
            // $corteId = $corte->getInsertID();

            // Inicia o processo de corte do desenho no banco de dados
            // $updat = [
            //     'status' => 'cortando',
            //     'corte_id' => $corteId
            // ];
            // $desenho->update($_SESSION["lista"][$id], $updat);

            // Retorna o caminho do arquivo que estÃƒÆ’Ã‚Â¡ sendo cortado
            //Ferramentas::enviar_desenho($_SERVER['REMOTE_ADDR'],$array['diretorio']);

            // $projeto_desenhos = (new \App\Models\Projeto_desenho())
            //     ->where('desenho_id', $array)
            //     ->orderBy('data_add', 'ASC')
            //     ->findAll();   // opcional: garantir o ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œprimeiroÃƒÂ¢Ã¢â€šÂ¬Ã‚Â por data

            $i = 0;
            $arquivos = array();
            $itens = array();
            foreach ($array as $key => $value) {
                $d = (new \App\Models\Desenhos())
                    ->select("
                desenhos.*,
                prioridade.nome      AS prioridade_nome,
                prioridade.cor       AS prioridade_cor,
                empresa.nome         AS empresa_nome,
                empreendimentos.nome AS empreendimento_nome,
                empreendimentos.escala AS empreendimento_escala,
                finalidade.nome      AS finalidade_nome,
                usuarios.nome        AS usuario_nome,
                corte.data_end       AS corte_data_end,
                corte.status         AS corte_status,
                projeto_desenho.marcador AS marcador,
                projeto_desenho.id AS projeto_desenho_id,
                o.ordem          AS ordem
            ")
                    ->join('projeto_desenho', 'projeto_desenho.desenho_id         = desenhos.id', 'left')
                    ->join('prioridade', 'prioridade.id        = desenhos.prioridade_id', 'left')
                    ->join('empresa', 'empresa.id           = desenhos.empresa_id', 'left')
                    ->join('empreendimentos', 'empreendimentos.id   = desenhos.empreendimentos_id', 'left')
                    ->join('finalidade', 'finalidade.id        = desenhos.finalidade_id', 'left')
                    ->join('usuarios', 'usuarios.id          = desenhos.usuario_id_desenhista', 'left')
                    ->join('corte', 'corte.id             = desenhos.corte_id', 'left')
                    ->join(
                        'ordem o',
                        "o.desenho_id = desenhos.id
     AND o.status = 'ativo'",
                        'left'
                    )
                    ->whereIn('desenhos.status', ['pendente', 'cortando'])
                    ->where('desenhos.id', $value['desenho_id'])
                    ->orderBy('prioridade.ordem', 'ASC')
                    ->orderBy('o.ordem', 'ASC')
                    ->first();
                if (!$d)
                    continue;
                $marcador = "";
                $marcado = ($d['marcador'] ?? "0") != "0";
                if ($marcado) {
                    $marcador = "checked";
                }
                $checkboxId = 'todoCheck6_' . $i;
                $arquivos[] = ' <div  class="icheck-primary d-inline ml-2">
                      <button onclick="ver_dxf_projeto(\'' . $i . '\')" class="btn btn-outline-info"><img src="'. base_url('public/img/icon/visao.png'). '" alt="Corte a Laser" style="height:20px;width:auto;vertical-align:middle;"></button>
                      <input type="checkbox" value="" onclick="marcarArquivos(' . $i . ')" id="' . $checkboxId . '" ' . $marcador . '>
                      <label for="' . $checkboxId . '"></label>
                    </div>
                    <span class="text"><button class="btn btn-outline-info" onclick="buscarArquivos(' . $i . ')">' . basename($d['diretorio']) . '</button></span> ';
                $itens[] = [
                    'id' => $i,
                    'nome' => basename($d['diretorio']),
                    'marcado' => $marcado,
                    'status' => $this->normalizarStatusTexto($d['status'] ?? ''),
                    'removivel' => strtolower(trim($this->normalizarStatusTexto($d['status'] ?? ''))) === 'pendente',
                    'remover_bloqueio' => 'Somente arquivos pendentes podem ser removidos do projeto.'
                ];
                $_SESSION["baixar_arquivo_tudo"][$i] = $d;
                $_SESSION["baixar_arquivo"][$i] = $d['diretorio'];
                $_SESSION["marcar_arquivo"][$i] = $d['projeto_desenho_id'];
                $i++;
            }



            $data = [
                "arquivos" => $arquivos,
                "itens" => $itens,
                "total" => count($itens),
                "projeto_id" => $projetoId,
                "descricao" => (string) (($contextoProjeto['projeto']['descricao'] ?? '') ?: ''),
                "accept" => (string) (($contextoProjeto['accept'] ?? '') ?: ''),
                "1" => $array,
                "2" => $_SESSION["lista_projetos"]
            ];
            return $this->response->setJSON($data);
        }
    }

    function baixar_arquivo()
    {
        // if ($this->request->isAJAX()) {
        // ObtÃƒÆ’Ã‚Â©m o ID do desenho a ser cortado
        session_start();
        $id = service('request')->getPost('id');
        $diretorio = $_SESSION["baixar_arquivo"][$id];
        if (!file_exists($diretorio)) {
            $data = [
                "erro" => 'arquivo não encontrado',
                //  "1" => $array,
                //"2" => ($array)
            ];
            return $this->response->setJSON($data);
        }

        return $this->response->download($diretorio, null);
        //}
    }
    public function marcar_arquivo()
    {
        // (Opcional) garanta que ÃƒÆ’Ã‚Â© uma chamada AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)
                ->setJSON(['ok' => false, 'msg' => 'Requisição inválida.']);
        }

        $id = (int) $this->request->getPost('id');
        session_start();
        $id = $_SESSION["marcar_arquivo"][$id];

        // Atualiza o desenho
        $desenhos = new \App\Models\Projeto_desenho();

        $ok = $desenhos->where('id', $id)
            ->set('marcador', '1 - IFNULL(marcador,0)', false)
            ->update();

        if (!$ok) {
            return $this->response->setStatusCode(500)
                ->setJSON([
                    'ok' => false,
                    'msg' => 'Falha ao atualizar marcador.',
                    'errors' => $desenhos->errors()
                ]);
        }

        return $this->response->setJSON([
            'ok' => true
        ]);
    }

    public function preparar_adicionar_projeto()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'ok' => 'false',
                'mensagem' => 'Requisicao invalida.',
            ]);
        }

        $id = service('request')->getPost('id');
        $projetoIdPost = (int) service('request')->getPost('projeto_id');
        $this->iniciarSessaoSeNecessario();
        $contexto = $projetoIdPost > 0
            ? $this->obterContextoProjetoPorId($projetoIdPost)
            : $this->obterContextoProjetoPorIndice($id);

        if (!is_array($contexto)) {
            return $this->response->setStatusCode(404)->setJSON([
                'ok' => 'false',
                'mensagem' => 'Projeto nao encontrado para adicionar arquivos.',
            ]);
        }

        $_SESSION['projeto_upload_contexto'] = [
            'projeto_id' => (int) ($contexto['projeto']['id'] ?? 0),
            'processo_id' => (int) ($contexto['processo_id'] ?? 0),
            'accept' => (string) ($contexto['accept'] ?? ''),
            'extensoes' => array_values($contexto['extensoes'] ?? []),
        ];

        return $this->response->setJSON([
            'ok' => 'true',
            'projeto_id' => (int) ($contexto['projeto']['id'] ?? 0),
            'descricao' => (string) ($contexto['projeto']['descricao'] ?? ''),
            'accept' => (string) ($contexto['accept'] ?? ''),
        ]);
    }

    public function desenho_adicionar_projeto()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'ok' => 'false',
                'mensagem' => 'Requisicao invalida.',
            ]);
        }

        $this->iniciarSessaoSeNecessario();
        $uploadContexto = $_SESSION['projeto_upload_contexto'] ?? null;
        $projetoId = (int) ($uploadContexto['projeto_id'] ?? 0);
        if ($projetoId <= 0) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => 'false',
                'mensagem' => 'Projeto nao preparado para receber arquivos.',
            ]);
        }

        $contexto = $this->obterContextoProjetoPorId($projetoId);
        if (!is_array($contexto)) {
            return $this->response->setStatusCode(404)->setJSON([
                'ok' => 'false',
                'mensagem' => 'Projeto nao encontrado para adicionar arquivos.',
            ]);
        }

        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => 'false',
                'mensagem' => 'Arquivo invalido para envio.',
            ]);
        }

        $extensoesPermitidas = array_values($contexto['extensoes'] ?? []);
        if ($extensoesPermitidas === []) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => 'false',
                'mensagem' => 'O processo deste projeto nao possui filtro de arquivo configurado.',
            ]);
        }

        $nomeOriginal = (string) ($file->getClientName() ?: $file->getName());
        $extensao = ltrim(strtolower((string) pathinfo($nomeOriginal, PATHINFO_EXTENSION)), '.');
        if ($extensao === '' || !in_array($extensao, $extensoesPermitidas, true)) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => 'false',
                'mensagem' => 'Tipo de arquivo nao permitido para este processo.',
            ]);
        }

        $diretorioProjeto = trim((string) ($contexto['projeto']['diretorio'] ?? ''));
        if ($diretorioProjeto === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => 'false',
                'mensagem' => 'Projeto sem diretorio configurado para receber arquivos.',
            ]);
        }

        $diretorioFisico = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $diretorioProjeto), DIRECTORY_SEPARATOR);
        if (!is_dir($diretorioFisico) && !@mkdir($diretorioFisico, 0777, true) && !is_dir($diretorioFisico)) {
            return $this->response->setStatusCode(500)->setJSON([
                'ok' => 'false',
                'mensagem' => 'Nao foi possivel preparar o diretorio do projeto.',
            ]);
        }

        $destino = $this->montarDestinoArquivoProjeto($diretorioProjeto, $nomeOriginal);
        if (!$file->move($destino['diretorio'], $destino['nome_final'])) {
            return $this->response->setStatusCode(500)->setJSON([
                'ok' => 'false',
                'mensagem' => 'Nao foi possivel mover o arquivo para o projeto.',
            ]);
        }

        $desenhoBase = $contexto['desenho_base'];
        $usuarioId = (int) ($_SESSION['usuario'] ?? 0);
        $desenhoModel = new \App\Models\Desenhos();
        $projetoDesenhoModel = new \App\Models\Projeto_desenho();
        $projetoModel = new \App\Models\Projeto();
        $db = \Config\Database::connect();
        $desenhoId = 0;
        $caminhoBanco = str_replace('\\', '/', $destino['caminho_final']);

        $db->transStart();

        $desenhoModel->insert([
            'nome' => (string) ($destino['nome_banco'] ?? ''),
            'diretorio' => $caminhoBanco,
            'usuario_id_desenhista' => (int) ($desenhoBase['usuario_id_desenhista'] ?? $usuarioId),
            'status' => 'pendente',
            'prioridade_id' => (int) ($desenhoBase['prioridade_id'] ?? 0),
            'finalidade_id' => (int) ($desenhoBase['finalidade_id'] ?? 0),
            'empreendimentos_id' => (int) ($desenhoBase['empreendimentos_id'] ?? 0),
            'empresa_id' => (int) ($desenhoBase['empresa_id'] ?? 0),
            'processos_id' => (int) ($contexto['processo_id'] ?? 0),
        ]);
        $desenhoId = (int) $desenhoModel->getInsertID();

        if ($desenhoId > 0) {
            Ferramentas::garantirOrdemAtivaDesenho(
                $desenhoId,
                (int) ($contexto['processo_id'] ?? 0),
                (int) ($desenhoBase['prioridade_id'] ?? 0)
            );

            $projetoDesenhoModel->insert([
                'usuario_id' => $usuarioId,
                'projeto_id' => $projetoId,
                'desenho_id' => $desenhoId,
            ]);

            $statusProjeto = strtolower(trim((string) ($contexto['projeto']['status'] ?? '')));
            if ($statusProjeto === 'finalizado') {
                $projetoModel->update($projetoId, ['status' => 'ativo']);
            }
        }

        $db->transComplete();

        if (!$db->transStatus() || $desenhoId <= 0) {
            if (is_file($destino['caminho_final'])) {
                @unlink($destino['caminho_final']);
            }

            return $this->response->setStatusCode(500)->setJSON([
                'ok' => 'false',
                'mensagem' => 'Nao foi possivel salvar o novo arquivo no projeto.',
            ]);
        }

        $this->registrarAlteracaoProjetoArquivo(
            'projeto_arquivo_adicionar',
            $projetoId,
            $desenhoId,
            [
                [
                    'campo' => 'projeto_desenho.desenho_id',
                    'valor_antes' => '',
                    'valor_depois' => (string) $desenhoId,
                ],
                [
                    'campo' => 'arquivo.nome',
                    'valor_antes' => '',
                    'valor_depois' => (string) ($destino['nome_final'] ?? ''),
                ],
            ],
            [
                'processo_id' => (int) ($contexto['processo_id'] ?? 0),
                'processo_nome' => $this->obterNomeProcessoPorId((int) ($contexto['processo_id'] ?? 0)),
                'arquivo_nome' => Ferramentas::remove_id_file((string) ($destino['nome_final'] ?? '')),
            ]
        );

        Ferramentas::sincronizarOrdensFaltantes();

        return $this->response->setJSON([
            'ok' => 'true',
            'desenho_id' => $desenhoId,
            'projeto_id' => $projetoId,
            'mensagem' => 'Arquivo adicionado ao projeto com sucesso.',
        ]);
    }

    public function remover_arquivo_projeto()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'ok' => false,
                'mensagem' => 'Requisicao invalida.',
            ]);
        }

        $id = (int) $this->request->getPost('id');
        $this->iniciarSessaoSeNecessario();

        $projetoDesenhoId = (int) ($_SESSION['marcar_arquivo'][$id] ?? 0);
        $itemModal = $_SESSION['baixar_arquivo_tudo'][$id] ?? null;
        if ($projetoDesenhoId <= 0 || !is_array($itemModal)) {
            return $this->response->setStatusCode(404)->setJSON([
                'ok' => false,
                'mensagem' => 'Arquivo do projeto nao encontrado no modal atual.',
            ]);
        }

        $projetoDesenhoModel = new \App\Models\Projeto_desenho();
        $projetoDesenho = $projetoDesenhoModel->find($projetoDesenhoId);
        if (!is_array($projetoDesenho) || empty($projetoDesenho['id'])) {
            return $this->response->setStatusCode(404)->setJSON([
                'ok' => false,
                'mensagem' => 'Vinculo do arquivo com o projeto nao encontrado.',
            ]);
        }

        $desenhoId = (int) ($projetoDesenho['desenho_id'] ?? 0);
        $projetoId = (int) ($projetoDesenho['projeto_id'] ?? 0);
        $desenhoModel = new \App\Models\Desenhos();
        $desenho = $desenhoModel->find($desenhoId);
        if (!is_array($desenho) || empty($desenho['id'])) {
            return $this->response->setStatusCode(404)->setJSON([
                'ok' => false,
                'mensagem' => 'Arquivo do projeto nao encontrado.',
            ]);
        }

        $statusDesenho = strtolower(trim($this->normalizarStatusTexto($desenho['status'] ?? '')));
        if ($statusDesenho !== 'pendente') {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Somente arquivos pendentes podem ser removidos do projeto.',
            ]);
        }

        $diretorioArquivo = (string) ($desenho['diretorio'] ?? '');
        $projeto = (new \App\Models\Projeto())->find($projetoId);
        $db = \Config\Database::connect();
        $dependenciaModel = new \App\Models\Dependencia();
        $ordemModel = new \App\Models\Ordem();
        $corteModel = new \App\Models\Corte();
        $arquivoMetricasModel = null;
        if (class_exists('\App\Models\ArquivoMetricasMaterial') && $db->tableExists('arquivo_metricas_material')) {
            $arquivoMetricasModel = new \App\Models\ArquivoMetricasMaterial();
        }

        $db->transStart();

        $projetoDesenhoModel->delete($projetoDesenhoId);
        $ordemModel->where('desenho_id', $desenhoId)->where('status', 'ativo')->set('status', 'desativado')->update();
        $dependenciaModel->where('desenhos_id', $desenhoId)->delete();
        $dependenciaModel->where('desenhos_id_dependente', $desenhoId)->delete();

        $corteId = (int) ($desenho['corte_id'] ?? 0);
        if ($corteId > 0) {
            $corteModel->delete($corteId);
        }

        if ($arquivoMetricasModel !== null) {
            $arquivoMetricasModel
                ->where('entidade_tipo', 'desenho')
                ->where('entidade_id', $desenhoId)
                ->delete();
        }

        $desenhoModel->delete($desenhoId);

        $restantes = $projetoDesenhoModel->where('projeto_id', $projetoId)->countAllResults();
        $projetoRemovido = false;
        if ($restantes <= 0 && $projetoId > 0) {
            $ordemModel->where('projeto_id', $projetoId)->where('status', 'ativo')->set('status', 'desativado')->update();
            $dependenciaModel->where('projeto_id', $projetoId)->delete();
            $dependenciaModel->where('projeto_id_dependente', $projetoId)->delete();
            (new \App\Models\Projeto())->delete($projetoId);
            $projetoRemovido = true;
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return $this->response->setStatusCode(500)->setJSON([
                'ok' => false,
                'mensagem' => 'Nao foi possivel remover o arquivo do projeto.',
            ]);
        }

        if ($diretorioArquivo !== '' && is_file($diretorioArquivo)) {
            @unlink($diretorioArquivo);
        }

        if (is_array($projeto) && !empty($projeto['diretorio'])) {
            $diretorioProjeto = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, (string) $projeto['diretorio']), DIRECTORY_SEPARATOR);
            if (is_dir($diretorioProjeto)) {
                $itensDiretorio = @scandir($diretorioProjeto);
                if (is_array($itensDiretorio) && count(array_diff($itensDiretorio, ['.', '..'])) === 0) {
                    @rmdir($diretorioProjeto);
                }
            }
        }

        $this->registrarAlteracaoProjetoArquivo(
            'projeto_arquivo_remover',
            $projetoId,
            $desenhoId,
            [
                [
                    'campo' => 'projeto_desenho.desenho_id',
                    'valor_antes' => (string) $desenhoId,
                    'valor_depois' => '',
                ],
                [
                    'campo' => 'arquivo.nome',
                    'valor_antes' => basename($diretorioArquivo),
                    'valor_depois' => '',
                ],
            ],
            [
                'processo_id' => (int) ($desenho['processos_id'] ?? 0),
                'processo_nome' => $this->obterNomeProcessoPorId((int) ($desenho['processos_id'] ?? 0)),
                'arquivo_nome' => Ferramentas::remove_id_file((string) ($desenho['nome'] ?? '')),
                'projeto_removido' => $projetoRemovido ? '1' : '0',
            ]
        );

        Ferramentas::sincronizarOrdensFaltantes();
        unset($_SESSION['baixar_arquivo_tudo'], $_SESSION['baixar_arquivo'], $_SESSION['marcar_arquivo']);

        return $this->response->setJSON([
            'ok' => true,
            'projeto_id' => $projetoId,
            'projeto_removido' => $projetoRemovido,
            'mensagem' => $projetoRemovido
                ? 'Projeto removido porque nao restaram arquivos.'
                : 'Arquivo removido do projeto com sucesso.',
        ]);
    }

    public function caminho_desenho_atual()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'ok' => false,
                'mensagem' => 'Requisicao invalida.',
            ]);
        }

        $id = (int) service('request')->getPost('id');
        $this->iniciarSessaoSeNecessario();

        $itemLista = $this->buscarItemListaPorIndice($id);
        if (!is_array($itemLista)) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Desenho nao encontrado na lista atual.',
            ]);
        }

        $desenhoId = (int) ($_SESSION["lista"][$id] ?? 0);
        $desenhoAtual = $desenhoId > 0 ? (new \App\Models\Desenhos())->find($desenhoId) : null;
        if (!is_array($desenhoAtual) || empty($desenhoAtual['id'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Desenho nao encontrado para consultar o caminho.',
            ]);
        }

        $corteAtualId = (int) ($desenhoAtual['corte_id'] ?? 0);
        $corteAtual = $corteAtualId > 0 ? (new \App\Models\Corte())->find($corteAtualId) : null;
        if (!is_array($corteAtual) || strtolower(trim((string) ($corteAtual['status'] ?? ''))) !== 'inicio') {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Nao existe corte em andamento para este desenho.',
            ]);
        }

        $usuarioInicio = (int) ($corteAtual['usuario_id_ini'] ?? 0);
        $usuarioId = (int) ($_SESSION["usuario"] ?? 0);
        if ($usuarioInicio > 0 && $usuarioInicio !== $usuarioId) {
            return $this->response->setStatusCode(403)->setJSON([
                'ok' => false,
                'mensagem' => 'Somente o usuario que iniciou o corte pode consultar este caminho.',
            ]);
        }

        return $this->response->setJSON([
            'ok' => true,
            'caminho' => $this->montarCaminhoExibicaoLista($itemLista),
        ]);
    }

    /**
     * Inicia o processo de corte de um desenho e fornece o caminho do arquivo.
     *
     * Esta funÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o ÃƒÆ’Ã‚Â© usada para iniciar o processo de corte de um desenho especÃƒÆ’Ã‚Â­fico. Ela atualiza o status do desenho para "cortando" e registra informaÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Âµes sobre o inÃƒÆ’Ã‚Â­cio do processo de corte no banco de dados.
     *
     * ParÃƒÆ’Ã‚Â¢metros:
     * - $id (POST): O ID do desenho que serÃƒÆ’Ã‚Â¡ iniciado para corte.
     *
     * Retorna:
     * - Um array contendo o caminho do arquivo que estÃƒÆ’Ã‚Â¡ sendo cortado.
     */
    function caminho_desenho()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'ok' => false,
                'mensagem' => 'Requisicao invalida.',
            ]);
        }

        $id = (int) service('request')->getPost('id');
        $this->iniciarSessaoSeNecessario();

        $itemLista = $this->buscarItemListaPorIndice($id);
        if (!is_array($itemLista)) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Desenho nao encontrado na lista atual.',
            ]);
        }

        $desenhoId = (int) ($_SESSION["lista"][$id] ?? 0);
        if ($desenhoId <= 0) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Desenho nao encontrado para iniciar corte.',
            ]);
        }

        $desenhoModel = new \App\Models\Desenhos();
        $corteModel = new \App\Models\Corte();
        $desenhoAtual = $desenhoModel->find($desenhoId);
        if (!is_array($desenhoAtual) || empty($desenhoAtual['id'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Desenho nao encontrado.',
            ]);
        }

        $usuarioId = (int) ($_SESSION["usuario"] ?? 0);
        $statusAtual = strtolower(trim($this->normalizarStatusTexto($desenhoAtual['status'] ?? '')));
        $caminhoExibicao = $this->montarCaminhoExibicaoLista($itemLista);
        $corteAtual = null;
        $corteAtualId = (int) ($desenhoAtual['corte_id'] ?? 0);
        if ($corteAtualId > 0) {
            $corteAtual = $corteModel->find($corteAtualId);
        }

        if (
            is_array($corteAtual)
            && strtolower(trim((string) ($corteAtual['status'] ?? ''))) === 'inicio'
            && $statusAtual === 'cortando'
        ) {
            $usuarioInicio = (int) ($corteAtual['usuario_id_ini'] ?? 0);
            if ($usuarioInicio > 0 && $usuarioInicio !== $usuarioId) {
                return $this->response->setStatusCode(403)->setJSON([
                    'ok' => false,
                    'mensagem' => 'Somente o usuario que iniciou o corte pode continuar com este desenho.',
                ]);
            }

            return $this->response->setJSON([
                'ok' => true,
                'caminho' => $caminhoExibicao,
                'ja_ativo' => true,
            ]);
        }

        if (!in_array($statusAtual, ['pendente', 'cortando'], true)) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'O desenho nao esta disponivel para iniciar corte.',
            ]);
        }

        $corteModel->insert([
            'usuario_id_ini' => $usuarioId,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'status' => 'inicio',
        ]);
        $corteId = (int) $corteModel->getInsertID();

        $ok = $desenhoModel->update($desenhoId, [
            'status' => 'cortando',
            'corte_id' => $corteId,
        ]);

        if (!$ok) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensagem' => 'Nao foi possivel iniciar o corte do desenho.',
            ]);
        }

        return $this->response->setJSON([
            'ok' => true,
            'caminho' => $caminhoExibicao,
        ]);

        if (!$this->request->isAJAX()) {
            // ObtÃƒÆ’Ã‚Â©m o ID do desenho a ser cortado
            $id = service('request')->getPost('id');
            session_start();
            // $_SESSION['confirmar_corte_proc'] = isset ($_SESSION['confirmar_corte_proc']) ? $_SESSION['confirmar_corte_proc'] : FALSE;
            // if ($_SESSION['confirmar_corte_proc']) {
            //     return;
            // } 
            $array = Ferramentas::array_pesquisa($_SESSION["lista_completa"], 'id', $id);

            $corte = new \App\Models\Corte();
            $desenho = new \App\Models\Desenhos();
            // Salva o inicio do corte
            $input = [
                'usuario_id_ini' => $_SESSION["usuario"],
                'ip' => $_SERVER['REMOTE_ADDR'],
                'status' => 'inicio'
            ];
            $corte->insert($input);
            $corteId = $corte->getInsertID();

            // Inicia o processo de corte do desenho no banco de dados
            $updat = [
                'status' => 'cortando',
                'corte_id' => $corteId
            ];
            $desenho->update($_SESSION["lista"][$id], $updat);

            // Retorna o caminho do arquivo que estÃƒÆ’Ã‚Â¡ sendo cortado
            //Ferramentas::enviar_desenho($_SERVER['REMOTE_ADDR'],$array['diretorio']);
            $data = [
                "caminho" => preg_replace('/\\\\+/', '\\\\', str_replace(["c:/wl/", "/"], ["i:/", "\\\\"], $array['diretorio']))
            ];
            return $this->response->setJSON($data);
        }
    }

    /**
     * Lista desenhos com status de "corte" ou "cortando".
     *
     * Esta funÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o retorna uma lista de desenhos que possuem status "corte" ou "cortando".
     *
     * @return 
     */
    function lista_corte_desenhista() //rece um post via ajax pedindo para listar os usuarios
    {
        if ($this->request->isAJAX()) {

            //Ferramentas::();
            $nome_processos = service('request')->getPost('nome_processos');

            $desenhos = new \App\Models\Desenhos(); // Instancia o modelo de dados para desenhos.

            $prioridade = new \App\Models\Prioridade(); // Instancia o modelo de dados para prioridades.


            $empresa = new \App\Models\Empresa(); // Instancia o modelo de dados para empresas.

            $empreendimento = new \App\Models\Empreendimentos(); // Instancia o modelo de dados para empreendimentos.
            $finalidade = new \App\Models\Finalidade(); // Instancia o modelo de dados para finalidades.

            $usuario = new \App\Models\Usuarios();


            $processos = new \App\Models\Processos();
            $proc = (new \App\Models\Processos())
                ->where('nome', $nome_processos)
                ->where('status', 'ativo')
                ->first();

            $lista = "";
            $desenhos_data = $desenhos
                ->select("
                    desenhos.*,
                    o.ordem AS ordem
                ")
                ->join(
                    'ordem o',
                    "o.desenho_id = desenhos.id AND o.status = 'ativo'",
                    'left'
                )
                // ** filtra apenas pelo processo que o usuÃƒÆ’Ã‚Â¡rio escolheu **
                ->where('desenhos.processos_id', $proc['id'])
                ->whereIn('desenhos.status', [
                    Ferramentas::codificador('pendente'),
                    Ferramentas::codificador('cortando'),
                    Ferramentas::codificador('processando'),
                ])
                ->orderBy('o.ordem IS NULL', 'ASC', false)
                ->orderBy('o.ordem', 'ASC')
                ->findAll();


            $i = 0;
            if ($proc['input'] == 'mult') {
                $ids = array();
                foreach ($desenhos_data as $key => $value) {
                    if (in_array($value['id'], $ids))
                        continue;
                    $ids[] = $value['id'];

                    //Ferramentas::sicroniaOrdems();

                    // ObtÃƒÆ’Ã‚Â©m a prioridade do desenho.

                    $prioridade_desenho = $prioridade->where('id', $value['prioridade_id'])->first();
                    $processos_nome = $processos->where('id', $value['processos_id'])->first()['nome'];
                    $usuario_nome = $usuario->where('id', $value['usuario_id_desenhista'])->first()['nome'];
                    $empresa_nome = $empresa->where('id', $value['empresa_id'])->first()['nome'];
                    $empreendimento_registro = $empreendimento->where('id', $value['empreendimentos_id'])->first();
                    $empreendimento_nome = is_array($empreendimento_registro) ? ($empreendimento_registro['nome'] ?? '') : '';
                    $empreendimento_escala = is_array($empreendimento_registro) ? ($empreendimento_registro['escala'] ?? '') : '';
                    $empreendimento_html = $this->montarEmpreendimentoComEscalaHtml($empreendimento_nome, $empreendimento_escala);
                    $finalidade_nome = $finalidade->where('id', $value['finalidade_id'])->first()['nome'];

                    $tags = explode('/', ($value['diretorio']));
                    $cort_hora_add = Ferramentas::array_index($finalidade->where('id', $value['corte_id'])->first(), ['hora_add']);
                    // Remover os ÃƒÆ’Ã‚Â­ndices de 0 a 5
                    $tags = array_slice($tags, 6);

                    // Remover o ÃƒÆ’Ã‚Âºltimo elemento
                    unset($tags[count($tags) - 1]);
                    $tags = implode(" - ", $tags);
                    // ConstrÃƒÆ’Ã‚Â³i a linha da tabela com informaÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Âµes do desenho.
                    $lista .= '
                <tr>
                <td bgcolor="' . $prioridade_desenho['cor'] . '"><span class="marca_texto">' . $prioridade_desenho['nome'] . '</span></td>
                <td>' . $value['ordem'] . ' </td>
                <td>' . $usuario_nome . '</td>
                <td>' . Ferramentas::remove_id_file($value['nome']) . '</td>
                <td>' . $empresa_nome . '</td>
                <td>' . $empreendimento_html . '</td>
                <td>' . $finalidade_nome . '</td>
                <td>' . $tags . '</td>
                <td>' . $value['status'] . '</td>
                <td>' . $cort_hora_add . '</td>
                <td>' . Ferramentas::formatarDataHora($value['data_add']) . '</td>
                </tr>
                ';
                }
            } else if ($proc['input'] == 'ind') {

                $lista_completa = [];
                $lista_ids = [];

                $html = array();
                $desBuilder = (new \App\Models\Projeto())
                    ->select('p.*,o.ordem AS ordem') // traz sÃƒÆ’Ã‚Â³ os campos de ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œdesenhosÃƒÂ¢Ã¢â€šÂ¬Ã‚Â
                    ->from('projeto p')
                    ->join('projeto_desenho pd', 'pd.projeto_id = p.id', 'LEFT')
                    ->join('desenhos d', 'd.id          = pd.desenho_id', 'LEFT')
                    ->join('ordem o', 'p.id          = o.projeto_id AND o.status = "ativo"', 'LEFT')
                    ->whereIn('p.status', ['ativo', 'processando'])
                    ->where('d.processos_id', $proc['id'])
                    ->findAll();

                $ids = array();
                foreach ($desBuilder as $key => $value) {
                    if (in_array($value['id'], $ids))
                        continue;

                    $projeto_desenhos = (new \App\Models\Projeto_desenho())
                        ->where('projeto_id', $value['id'])
                        ->orderBy('data_add', 'ASC')   // opcional: garantir o ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œprimeiroÃƒÂ¢Ã¢â€šÂ¬Ã‚Â por data
                        ->first();



                    if (!$projeto_desenhos)
                        continue;
                    $projeto_desenhos_todos = (new \App\Models\Projeto_desenho())
                        ->where('projeto_id', $value['id'])
                        ->orderBy('data_add', 'ASC')   // opcional: garantir o ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œprimeiroÃƒÂ¢Ã¢â€šÂ¬Ã‚Â por data
                        ->findAll();

                    $d = (new \App\Models\Desenhos())
                        ->select("
                desenhos.*,
                prioridade.nome      AS prioridade_nome,
                prioridade.cor       AS prioridade_cor,
                empresa.nome         AS empresa_nome,
                empreendimentos.nome AS empreendimento_nome,
                empreendimentos.escala AS empreendimento_escala,
                finalidade.nome      AS finalidade_nome,
                usuarios.nome        AS usuario_nome,
                corte.data_end       AS corte_data_end,
                corte.status         AS corte_status,
                o.ordem          AS ordem
            ")
                        ->join('prioridade', 'prioridade.id        = desenhos.prioridade_id', 'left')
                        ->join('empresa', 'empresa.id           = desenhos.empresa_id', 'left')
                        ->join('empreendimentos', 'empreendimentos.id   = desenhos.empreendimentos_id', 'left')
                        ->join('finalidade', 'finalidade.id        = desenhos.finalidade_id', 'left')
                        ->join('usuarios', 'usuarios.id          = desenhos.usuario_id_desenhista', 'left')
                        ->join('corte', 'corte.id             = desenhos.corte_id', 'left')
                        ->join(
                            'ordem o',
                            "o.desenho_id = desenhos.id
     AND o.status = 'ativo'",
                            'left'
                        )
                        ->where('desenhos.id', $projeto_desenhos['desenho_id'])
                        ->orderBy('prioridade.ordem', 'ASC')
                        ->orderBy('o.ordem', 'ASC')
                        ->first();

                    // 5) Quais desenhos ESTE IP estÃƒÆ’Ã‚Â¡ cortando agora?
                    $ip = $_SERVER['REMOTE_ADDR'];
                    $myActiveDesenhos = (new \App\Models\Desenhos())
                        ->select('desenhos.id')
                        ->join('corte', 'corte.id = desenhos.corte_id', 'inner')
                        ->where('corte.ip', $ip)
                        ->where('corte.status', 'inicio')
                        ->findAll() ?: [];

                    $myActiveAny = !empty($myActiveDesenhos);
                    // extrai tags
                    $parts = array_filter(explode('/', $d['diretorio']));
                    $tags = implode(' - ', array_slice($parts, 6, -1));



                    // monta a linha
                    $html[] = "
                <tr>
                    <td bgcolor=\"{$d['prioridade_cor']}\"><span class=\"marca_texto\">{$d['prioridade_nome']}</span></td>
                    <td>" . $value['ordem'] . "</td>
                    <td>{$d['usuario_nome']}</td>
                    <td>" . $value['descricao'] . "</td>
                    <td>{$d['empresa_nome']}</td>
                    <td>" . $this->montarEmpreendimentoComEscalaHtml($d['empreendimento_nome'] ?? '', $d['empreendimento_escala'] ?? '') . "</td>
                    <td>{$d['finalidade_nome']}</td>
                    <td>{$tags}</td>
                     <td>{$d['status']}</td>
                    <td></td>
                    <td>" . date('d/m/Y H:i:s', strtotime($d['data_add'])) . "</td>
            
                </tr>";

                    // preenche arrays de sessÃƒÆ’Ã‚Â£o
                    $p[$i] = $projeto_desenhos_todos;
                    $lista_ids[$i] = $d['id'];
                    $d['id'] = $i;
                    $lista_completa[$i] = $d;
                    $i++;
                }
                // 2) Ordena pelas chaves originais (1, 18, 40, etc)


                $lista = implode("", $html);
                // agora ÃƒÆ’Ã‚Â© sÃƒÆ’Ã‚Â³ imprimir:
                //  $html = implode("", $html);


                $_SESSION["lista"] = $lista_ids;
                $_SESSION["lista_completa"] = $lista_completa;
            }


            $data = [
                "lista" => $lista,
                //'1' => Ferramentas::ordenarOrdems(),
                //'2' => Ferramentas::desativarOrdems(),
                //'3' => Ferramentas::sicroniaOrdems(),
                '4' => Ferramentas::ordenarOrdems2()
            ];

            return $this->response->setJSON($data);
        }
    }

public function enviar_para_lista_corte()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON([
            'status' => false,
            'msg'    => 'Requisição inválida'
        ]);
    }

    $id = $this->request->getPost('id');

    session_start();
    $lista = $_SESSION["lista_completa"] ?? [];

    $novaEntrada = Ferramentas::array_pesquisa($lista, 'id', $id);
    $caminhoExibicao = '';
    if (!$novaEntrada) {
        return $this->response->setJSON([
            'status' => false,
            'msg'    => 'Desenho não encontrado na lista'
        ]);
    }

    // ====== Resolve caminho do arquivo (mesma lÃƒÆ’Ã‚Â³gica do seu ver_desenho) ======
    $caminhoExibicao = $this->montarCaminhoExibicaoLista($novaEntrada);
    $caminho      = dirname($novaEntrada['diretorio']) . DIRECTORY_SEPARATOR;
    $nomeArquivo  = basename($novaEntrada['diretorio']);
    $nome_ajuste1 = "";
    $nome_ajuste2 = "";

    if (!file_exists($caminho . $novaEntrada['nome'])) {
        if (!file_exists($caminho . $nomeArquivo)) {

            if (strpos($nomeArquivo, '.') === false) {
                $pos = strrpos($nomeArquivo, '_');
                if ($pos !== false) {
                    $nome_ajuste1 = substr_replace($nomeArquivo, '.', $pos + 1, 0);
                }
            }

            if (!file_exists($caminho . $nome_ajuste1)) {
                if (strpos($novaEntrada['nome'], '.') === false) {
                    $pos = strrpos($novaEntrada['nome'], '_');
                    if ($pos !== false) {
                        $nome_ajuste2 = substr_replace($novaEntrada['nome'], '.', $pos + 1, 0);
                    }
                }

                if (!file_exists($caminho . $nome_ajuste2)) {
                    return $this->response->setJSON([
                        'status' => false,
                        'msg'    => 'Arquivo não encontrado em nenhuma das variações de nome',
                        'tentativas' => [
                            $caminho . $novaEntrada['nome'],
                            $caminho . $nomeArquivo,
                            $caminho . $nome_ajuste1,
                            $caminho . $nome_ajuste2,
                        ],
                        'original' => $novaEntrada['diretorio'],
                    ]);
                } else {
                    $nomeArquivo = $nome_ajuste2;
                }
            } else {
                $nomeArquivo = $nome_ajuste1;
            }
        }
    } else {
        $nomeArquivo = $novaEntrada['nome'];
    }

    $caminhoCompleto = $caminho . $nomeArquivo;
    $caminhoCompleto = str_replace(['\\', '//'], '/', $caminhoCompleto);

    if (!file_exists($caminhoCompleto)) {
        return $this->response->setJSON([
            'status' => false,
            'msg'    => 'Arquivo não encontrado no caminho final',
            'arquivo'=> $caminhoCompleto
        ]);
    }

    // ====== Monta URL do Flask pelo IP do cliente (mantido) ======
    $req = Services::request();

    $ip = $req->getHeaderLine('X-Forwarded-For');
    if (!empty($ip)) {
        $ip = trim(explode(',', $ip)[0]);
    } else {
        $ip = $req->getIPAddress();
    }

    if ($ip === '::1') {
        $ip = '127.0.0.1';
    }

    $url = "http://{$ip}:5000/lista_corte";

    // ====== Envio multipart compatÃƒÆ’Ã‚Â­vel com Flask (request.files['file']) ======
    try {
        $filename = basename($caminhoCompleto);
        $mime     = @mime_content_type($caminhoCompleto) ?: 'application/octet-stream';

        // Monta o arquivo para multipart
        $curlFile = new \CURLFile($caminhoCompleto, $mime, $filename);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => [
                'file' => $curlFile, // campo "file" (igual seu Flask)
            ],
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
                // NÃO setar Content-Type manualmente (o cURL cria boundary)
            ],
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 120,
        ]);

        $body     = curl_exec($ch);
        $curlErr  = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false) {

            $fallbackUrl = "http://127.0.0.1:5000/lista_corte";
            
            $ch2 = curl_init($fallbackUrl);
            curl_setopt_array($ch2, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => [
                    'file' => $curlFile,
                ],
                CURLOPT_HTTPHEADER     => [
                    'Accept: application/json',
                ],
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT        => 120,
            ]);

            $body2     = curl_exec($ch2);
            $curlErr2  = curl_error($ch2);
            $httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
            curl_close($ch2);

            if ($body2 === false) {
                return $this->response->setJSON([
                    'status' => false,
                    'msg'    => 'Erro ao chamar Flask',
                    'erro'   => $curlErr ?: $curlErr2 ?: 'Falha desconhecida',
                    'url'    => $url,
                    'url_fallback' => $fallbackUrl,
                    'caminho' => $caminhoExibicao,
                    'arquivo'=> $caminhoCompleto
                ]);
            }

            $json2 = json_decode((string)$body2, true);
            return $this->response->setJSON([
                'status'   => ($httpCode2 === 200),
                'msg'      => ($httpCode2 === 200) ? 'Arquivo enviado ao Lista Corte com sucesso' : 'Falha ao enviar ao Lista Corte',
                'httpCode' => $httpCode2,
                'url'      => $fallbackUrl,
                'retorno'  => $json2 ?? (string)$body2,
                'caminho'  => $caminhoExibicao,
                'arquivo'  => $caminhoCompleto,
            ]);
        }

        $json = json_decode((string)$body, true);

        return $this->response->setJSON([
            'status'   => ($httpCode === 200),
            'msg'      => ($httpCode === 200) ? 'Arquivo enviado ao Lista Corte com sucesso' : 'Falha ao enviar ao Lista Corte',
            'httpCode' => $httpCode,
            'url'      => $url,
            'retorno'  => $json ?? (string)$body,
            'caminho'  => $caminhoExibicao,
            'arquivo'  => $caminhoCompleto,
        ]);

    } catch (\Throwable $e) {
        return $this->response->setJSON([
            'status' => false,
            'msg'    => 'Erro ao chamar Flask',
            'erro'   => $e->getMessage(),
            'url'    => $url,
            'caminho' => $caminhoExibicao,
            'arquivo'=> $caminhoCompleto
        ]);
    }
}

    



}
