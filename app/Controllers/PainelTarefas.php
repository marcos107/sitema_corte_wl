<?php

namespace App\Controllers;

use App\Controllers\Ferramentas;
use App\Libraries\DxfDimensoes;
use App\Libraries\NivelTelaInicial;

class PainelTarefas extends BaseController
{
    private const LIMITE_CONCLUIDAS_TAREFAS = 300;
    private array $ultimaPerformanceListaPainel = [];

    private function iniciarSessao(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    private function decodificarValor($valor): string
    {
        $valor = (string) ($valor ?? '');
        if ($valor === '') {
            return '';
        }

        $decodificado = Ferramentas::decodificador($valor);
        return $decodificado !== '' ? $decodificado : $valor;
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

    private function truncateAuditText(string $valor, int $limite): string
    {
        if ($limite <= 0) {
            return '';
        }

        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            return mb_strlen($valor) > $limite ? mb_substr($valor, 0, $limite) : $valor;
        }

        return strlen($valor) > $limite ? substr($valor, 0, $limite) : $valor;
    }

    private function stringifyAuditValue($valor, int $limite = 4000): string
    {
        if (is_bool($valor)) {
            return $valor ? 'true' : 'false';
        }

        if (is_array($valor) || is_object($valor)) {
            $json = json_encode($valor, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $valor = $json !== false ? $json : print_r($valor, true);
        }

        return $this->truncateAuditText(trim((string) $valor), $limite);
    }

    private function carregarDesenhoAuditoria(int $desenhoId): ?array
    {
        if ($desenhoId <= 0) {
            return null;
        }

        $desenho = (new \App\Models\Desenhos())
            ->select('*')
            ->where('id', $desenhoId)
            ->first();

        return is_array($desenho) && !empty($desenho['id']) ? $desenho : null;
    }

    private function obterCaminhoArquivoDesenho(array $desenho): string
    {
        $caminho = trim((string) ($desenho['diretorio'] ?? $desenho['caminho'] ?? ''));
        if ($caminho === '') {
            return '';
        }

        return Ferramentas::wlStoragePath($caminho);
    }

    private function obterNomeExibicaoDesenho(array $desenho): string
    {
        $nome = $this->decodificarValor($desenho['nome'] ?? '');
        if ($nome === '') {
            $caminho = $this->obterCaminhoArquivoDesenho($desenho);
            $nome = $caminho !== '' ? basename($caminho) : '';
        }

        return $nome !== '' ? Ferramentas::remove_id_file($nome) : '';
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

        return is_array($processo) ? $this->decodificarValor($processo['nome'] ?? '') : '';
    }

    private function obterTipoProcessoPorId(int $processoId): string
    {
        if ($processoId <= 0) {
            return '';
        }

        static $cache = [];
        if (array_key_exists($processoId, $cache)) {
            return $cache[$processoId];
        }

        $processo = (new \App\Models\Processos())
            ->select('id, input')
            ->where('id', $processoId)
            ->first();

        $cache[$processoId] = strtolower(trim((string) ($processo['input'] ?? '')));
        return $cache[$processoId];
    }

    private function processoUsaDescricaoProjeto(int $processoId): bool
    {
        return $this->obterTipoProcessoPorId($processoId) === 'ind';
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
        $status = strtolower(trim($this->normalizarStatusTexto((string) ($item['status'] ?? ''))));
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

        $ordemA = $this->valorOrdenacaoFila($a['ordem'] ?? null);
        $ordemB = $this->valorOrdenacaoFila($b['ordem'] ?? null);
        $comparacao = $ordemA <=> $ordemB;
        if ($comparacao !== 0) {
            return $comparacao;
        }

        if ($ordemA === PHP_INT_MAX && $ordemB === PHP_INT_MAX) {
            $comparacao = $this->valorOrdenacaoFila($a['id'] ?? null) <=> $this->valorOrdenacaoFila($b['id'] ?? null);
            if ($comparacao !== 0) {
                return $comparacao;
            }
        }

        $comparacao = $this->timestampOrdenacaoFila($a['data_add'] ?? null) <=> $this->timestampOrdenacaoFila($b['data_add'] ?? null);
        if ($comparacao !== 0) {
            return $comparacao;
        }

        return $this->valorOrdenacaoFila($a['id'] ?? null) <=> $this->valorOrdenacaoFila($b['id'] ?? null);
    }

    private function compararReferenciasProjetoOrdem(array $a, array $b): int
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

    private function buscarDesenhosPendentesProjeto(
        int $projetoId,
        array $statusPermitidos,
        array $desenhosEmCorteUsuarioIds = [],
        ?int $usuarioId = null,
        ?array $periodo = null
    ): array
    {
        if ($projetoId <= 0 || $statusPermitidos === []) {
            return [];
        }

        $consulta = (new \App\Models\Projeto_desenho())
            ->select("projeto_desenho.*,
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
                      usuarios.nome AS desenhista_nome,
                      o.ordem AS ordem")
            ->join('desenhos', 'desenhos.id = projeto_desenho.desenho_id', 'left')
            ->join('prioridade', 'prioridade.id = desenhos.prioridade_id', 'left')
            ->join('empresa', 'empresa.id = desenhos.empresa_id', 'left')
            ->join('empreendimentos', 'empreendimentos.id = desenhos.empreendimentos_id', 'left')
            ->join('finalidade', 'finalidade.id = desenhos.finalidade_id', 'left')
            ->join('usuarios', 'usuarios.id = desenhos.usuario_id_desenhista', 'left')
            ->join('ordem o', "o.desenho_id = desenhos.id AND o.projeto_id IS NULL AND o.status = 'ativo'", 'left')
            ->where('projeto_desenho.projeto_id', $projetoId)
            ->whereIn('desenhos.status', $statusPermitidos);

        if ($usuarioId !== null && $usuarioId > 0) {
            $consulta->where('desenhos.usuario_id_desenhista', $usuarioId);
        }

        if (is_array($periodo)) {
            $consulta
                ->where('desenhos.data_add >=', (string) ($periodo['inicio'] ?? ''))
                ->where('desenhos.data_add <=', (string) ($periodo['fim'] ?? ''));
        }

        $desenhos = $consulta->findAll();

        foreach ($desenhos as &$desenho) {
            $this->prepararItemFilaParaOrdenacao($desenho, $desenhosEmCorteUsuarioIds);
        }
        unset($desenho);

        usort($desenhos, [$this, 'compararItensFila']);

        return $desenhos;
    }

    private function buscarReferenciasProjetosParaOrdem(int $processoId, array $statusPermitidos): array
    {
        if ($processoId <= 0 || $statusPermitidos === []) {
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
            ->whereIn('d.status', $statusPermitidos)
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

        uasort($referencias, [$this, 'compararReferenciasProjetoOrdem']);

        return $referencias;
    }

    private function garantirOrdemAtivaProjeto(int $projetoId, int $processoId, int $prioridadeId): void
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

    private function normalizarSequenciaOrdensProjetos(int $processoId, array $referenciasProjetos): void
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
        foreach ($grupos as $prioridadeId => $linhas) {
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

    private function sincronizarOrdensProjetosDoProcesso(int $processoId): void
    {
        if ($processoId <= 0) {
            return;
        }

        $statusPermitidos = $this->statusPermitidosTarefas(false);
        if ($statusPermitidos === []) {
            return;
        }

        $referenciasProjetos = $this->buscarReferenciasProjetosParaOrdem($processoId, $statusPermitidos);
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
                $this->garantirOrdemAtivaProjeto((int) $projetoId, $processoId, $prioridadeId);
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

        $this->normalizarSequenciaOrdensProjetos($processoId, $referenciasProjetos);
    }

    private function buscarTarefasAgrupadasPorProjeto(
        int $processoId,
        ?int $usuarioId = null,
        ?array $periodo = null
    ): array
    {
        if ($processoId <= 0) {
            return [];
        }

        $statusPermitidos = $this->statusPermitidosTarefas(false);
        if ($statusPermitidos === []) {
            return [];
        }

        $this->sincronizarOrdensProjetosDoProcesso($processoId);

        $this->iniciarSessao();
        $usuarioLogadoId = (int) ($_SESSION['usuario'] ?? 0);
        $usuarioFiltroId = $usuarioId !== null && $usuarioId > 0 ? (int) $usuarioId : null;
        $desenhosEmCorteUsuarioIds = $this->buscarIdsDesenhosEmCorteDoUsuario($usuarioLogadoId, $processoId);

        $consultaProjetos = (new \App\Models\Projeto())
            ->select('p.id,
                      p.descricao,
                      p.status,
                      o.ordem AS ordem_projeto,
                      o.prioridade_id AS prioridade_projeto_id,
                      prioridade_projeto.nome AS prioridade_projeto_nome,
                      prioridade_projeto.cor AS prioridade_projeto_cor,
                      prioridade_projeto.ordem AS prioridade_projeto_ordem')
            ->from('projeto p')
            ->join('projeto_desenho pd', 'pd.projeto_id = p.id', 'left')
            ->join('desenhos d', 'd.id = pd.desenho_id', 'left')
            ->join('ordem o', 'p.id = o.projeto_id AND o.processos_id = ' . (int) $processoId . ' AND o.desenho_id IS NULL AND o.status = "ativo"', 'left')
            ->join('prioridade prioridade_projeto', 'prioridade_projeto.id = o.prioridade_id', 'left')
            ->where('d.processos_id', $processoId)
            ->whereIn('d.status', $statusPermitidos)
            ->whereIn('p.status', ['ativo', 'pendente', 'processando', 'finalizado']);

        if ($usuarioFiltroId !== null) {
            $consultaProjetos->where('d.usuario_id_desenhista', $usuarioFiltroId);
        }

        if (is_array($periodo)) {
            $consultaProjetos
                ->where('d.data_add >=', (string) ($periodo['inicio'] ?? ''))
                ->where('d.data_add <=', (string) ($periodo['fim'] ?? ''));
        }

        $projetos = $consultaProjetos
            ->orderBy('o.ordem IS NULL', 'ASC', false)
            ->orderBy('o.ordem', 'ASC')
            ->findAll();

        $linhasProjeto = [];
        $projetosProcessados = [];

        foreach ($projetos as $projeto) {
            $projetoId = (int) ($projeto['id'] ?? 0);
            if ($projetoId <= 0 || isset($projetosProcessados[$projetoId])) {
                continue;
            }
            $projetosProcessados[$projetoId] = true;

            $desenhosProjeto = $this->buscarDesenhosPendentesProjeto(
                $projetoId,
                $statusPermitidos,
                $desenhosEmCorteUsuarioIds,
                $usuarioFiltroId,
                $periodo
            );
            if ($desenhosProjeto === []) {
                continue;
            }

            $desenhoLinha = $desenhosProjeto[0];
            $descricaoProjeto = trim($this->decodificarValor($projeto['descricao'] ?? ''));
            $desenhoLinha['projeto_id'] = $projetoId;
            $desenhoLinha['projeto_descricao'] = $descricaoProjeto;
            $desenhoLinha['ordem'] = $projeto['ordem_projeto'] ?? '';

            if (!empty($projeto['prioridade_projeto_id'])) {
                $desenhoLinha['prioridade_id'] = (int) $projeto['prioridade_projeto_id'];
                $desenhoLinha['prioridade_nome'] = $projeto['prioridade_projeto_nome'] ?? $desenhoLinha['prioridade_nome'] ?? '';
                $desenhoLinha['prioridade_cor'] = $projeto['prioridade_projeto_cor'] ?? $desenhoLinha['prioridade_cor'] ?? '';
                $desenhoLinha['prioridade_ordem'] = $projeto['prioridade_projeto_ordem'] ?? $desenhoLinha['prioridade_ordem'] ?? null;
            }

            $linhasProjeto[] = [
                'eh_corte_usuario' => !empty($desenhoLinha['eh_corte_usuario']),
                'prioridade_ordem' => $desenhoLinha['prioridade_ordem'] ?? null,
                'ordem' => $projeto['ordem_projeto'] ?? null,
                'data_add' => $desenhoLinha['data_add'] ?? null,
                'id' => $projetoId,
                'desenho' => $desenhoLinha,
            ];
        }

        usort($linhasProjeto, [$this, 'compararItensFila']);

        $tarefas = [];
        foreach ($linhasProjeto as $linhaProjeto) {
            $desenhoLinha = is_array($linhaProjeto['desenho'] ?? null) ? $linhaProjeto['desenho'] : [];
            if ($desenhoLinha === []) {
                continue;
            }

            $tarefa = $this->mapearDesenhoParaLinha($desenhoLinha);
            if (trim((string) ($desenhoLinha['projeto_descricao'] ?? '')) !== '') {
                $tarefa['nome_arquivo'] = trim((string) $desenhoLinha['projeto_descricao']);
            }
            if (!empty($tarefa['projeto_id'])) {
                $tarefa['desenho_id'] = (int) ($desenhoLinha['id'] ?? 0);
                $tarefa['id'] = (int) $tarefa['projeto_id'];
                $tarefa['item_tipo'] = 'projeto';
            }

            $tarefas[] = $tarefa;
        }

        return $tarefas;
    }

    private function deveAgruparProjetosNoPainel(string $aba, int $processoId): bool
    {
        if (!$this->processoUsaDescricaoProjeto($processoId)) {
            return false;
        }

        return in_array($aba, ['lista_tarefas', 'lista_tarefas_adm', 'meus_desenhos'], true);
    }

    private function processoExibeDimensaoDxf(int $processoId): bool
    {
        $token = trim((string) $this->obterNomeProcessoPorId($processoId));
        if ($token === '') {
            return false;
        }

        $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $token);
        if ($ascii !== false && $ascii !== '') {
            $token = $ascii;
        }

        $token = strtoupper(trim((string) preg_replace('/[^a-z0-9]+/i', '_', $token), '_'));
        return $token === 'CORTE_LASER';
    }

    private function aplicarDescricaoProjetoAoInd(array $desenhos, int $processoId): array
    {
        if ($desenhos === [] || !$this->processoUsaDescricaoProjeto($processoId)) {
            return $desenhos;
        }

        $desenhoIds = array_values(array_unique(array_filter(array_map(static function ($desenho) {
            return (int) ($desenho['id'] ?? 0);
        }, $desenhos))));

        if ($desenhoIds === []) {
            return $desenhos;
        }

        $linhasProjeto = (new \App\Models\Projeto_desenho())
            ->select('projeto_desenho.desenho_id, projeto.descricao AS projeto_descricao')
            ->join('projeto', 'projeto.id = projeto_desenho.projeto_id', 'left')
            ->whereIn('projeto_desenho.desenho_id', $desenhoIds)
            ->orderBy('projeto_desenho.data_add', 'ASC')
            ->findAll();

        if ($linhasProjeto === []) {
            return $desenhos;
        }

        $descricaoPorDesenho = [];
        foreach ($linhasProjeto as $linhaProjeto) {
            $desenhoId = (int) ($linhaProjeto['desenho_id'] ?? 0);
            if ($desenhoId <= 0 || array_key_exists($desenhoId, $descricaoPorDesenho)) {
                continue;
            }

            $descricao = trim($this->decodificarValor($linhaProjeto['projeto_descricao'] ?? ''));
            if ($descricao !== '') {
                $descricaoPorDesenho[$desenhoId] = $descricao;
            }
        }

        if ($descricaoPorDesenho === []) {
            return $desenhos;
        }

        foreach ($desenhos as &$desenho) {
            $desenhoId = (int) ($desenho['id'] ?? 0);
            if ($desenhoId > 0 && isset($descricaoPorDesenho[$desenhoId])) {
                $desenho['projeto_descricao'] = $descricaoPorDesenho[$desenhoId];
            }
        }
        unset($desenho);

        return $desenhos;
    }

    private function registrarAlteracaoPainel(string $item, int $idItem, string $acao, array $detalhes = [], array $contexto = []): bool
    {
        $usuarioId = (int) ($_SESSION['usuario'] ?? 0);
        $payload = [
            'usuario_id' => $usuarioId,
            'individuo' => $usuarioId,
            'id_item' => $idItem,
            'item' => $item,
            'info_mais' => $acao,
            '_meta' => array_merge([
                'acao' => $acao,
                'origem' => 'painel_tarefas',
            ], $contexto),
        ];

        return (new \App\Models\Alteracoes())->insertWithDetails($payload, $detalhes) !== false;
    }

    private function prepararDestinoLixeira(string $caminhoOriginal): array
    {
        $caminhoOriginal = Ferramentas::wlStoragePath($caminhoOriginal);
        if ($caminhoOriginal === '') {
            return ['ok' => false, 'mensagem' => 'Caminho do desenho invalido para mover para a lixeira.'];
        }

        $relativo = Ferramentas::wlStorageRelativePath($caminhoOriginal);

        $relativo = trim(str_replace('\\', '/', $relativo), '/');
        $partes = $relativo === '' ? [] : explode('/', $relativo);
        $nomeOriginal = array_pop($partes);
        if ($nomeOriginal === null || $nomeOriginal === '') {
            return ['ok' => false, 'mensagem' => 'Nome do arquivo invalido para mover para a lixeira.'];
        }

        $diretorioLixeira = Ferramentas::wlStoragePath('lixo') . '/';
        if ($partes !== []) {
            $diretorioLixeira .= implode('/', $partes) . '/';
        }
        $diretorioLixeira = str_replace('\\', '/', Ferramentas::normalizePath($diretorioLixeira));

        $baseNome = pathinfo($nomeOriginal, PATHINFO_FILENAME);
        $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
        $sufixoExtensao = $extensao !== '' ? '.' . $extensao : '';

        do {
            $novoNome = $baseNome . '_' . date('d_m_Y_H_i_s_') . mt_rand(0, 999) . $sufixoExtensao;
            $novoCaminho = str_replace('\\', '/', Ferramentas::normalizePath($diretorioLixeira . $novoNome));
        } while (file_exists($novoCaminho));

        return [
            'ok' => true,
            'diretorio_lixeira' => $diretorioLixeira,
            'nome_original' => $nomeOriginal,
            'novo_nome' => $novoNome,
            'novo_caminho' => $novoCaminho,
        ];
    }

    private function apagarDesenhoPainel(int $desenhoId, int $usuarioId): array
    {
        if ($desenhoId <= 0) {
            return ['ok' => false, 'mensagem' => 'Desenho invalido para apagar.'];
        }

        $desenho = $this->carregarDesenhoAuditoria($desenhoId);
        if ($desenho === null) {
            return ['ok' => false, 'mensagem' => 'Desenho nao encontrado.'];
        }

        $caminhoOriginal = $this->obterCaminhoArquivoDesenho($desenho);
        if ($caminhoOriginal === '' || !file_exists($caminhoOriginal)) {
            $db = \Config\Database::connect();
            $db->transStart();

            $atualizou = (new \App\Models\Desenhos())->update($desenhoId, ['status' => 'apagado']);
            if (!$atualizou) {
                $db->transRollback();
                return ['ok' => false, 'mensagem' => 'Falha ao marcar o desenho como apagado.'];
            }

            $mensagemAusencia = $caminhoOriginal === ''
                ? 'Caminho do arquivo indisponivel no momento da exclusao.'
                : 'Arquivo nao encontrado no momento da exclusao.';

            $auditou = $this->registrarAlteracaoPainel(
                'desenho',
                $desenhoId,
                'painel_tarefas.apagar_desenho',
                [
                    [
                        'campo' => 'status',
                        'valor_antes' => $this->decodificarValor($desenho['status'] ?? ''),
                        'valor_depois' => 'apagado',
                    ],
                    [
                        'campo' => 'arquivo_origem',
                        'valor_antes' => $caminhoOriginal,
                        'valor_depois' => '[arquivo ausente]',
                    ],
                    [
                        'campo' => 'observacao_exclusao',
                        'valor_antes' => '',
                        'valor_depois' => $mensagemAusencia,
                    ],
                ],
                [
                    'desenho_nome' => $this->obterNomeExibicaoDesenho($desenho),
                    'processo_id' => (int) ($desenho['processos_id'] ?? 0),
                    'processo_nome' => $this->obterNomeProcessoPorId((int) ($desenho['processos_id'] ?? 0)),
                    'usuario_executor' => $usuarioId,
                    'arquivo_ausente' => true,
                ]
            );

            if (!$auditou) {
                $db->transRollback();
                return ['ok' => false, 'mensagem' => 'Falha ao auditar a exclusao do desenho sem arquivo.'];
            }

            $db->transComplete();
            if (!$db->transStatus()) {
                return ['ok' => false, 'mensagem' => 'Falha ao concluir a exclusao do desenho sem arquivo.'];
            }

            return [
                'ok' => true,
                'mensagem' => 'Arquivo nao encontrado. O desenho foi marcado como apagado.',
            ];
        }

        $destino = $this->prepararDestinoLixeira($caminhoOriginal);
        if (empty($destino['ok'])) {
            return ['ok' => false, 'mensagem' => (string) ($destino['mensagem'] ?? 'Falha ao preparar a lixeira.')];
        }

        $problema = Ferramentas::criet_diretorio((string) $destino['diretorio_lixeira']);
        if (!empty($problema)) {
            return ['ok' => false, 'mensagem' => 'Falha ao criar diretorio da lixeira.'];
        }

        $db = \Config\Database::connect();
        $db->transStart();

        if (!rename($caminhoOriginal, (string) $destino['novo_caminho'])) {
            $db->transRollback();
            return ['ok' => false, 'mensagem' => 'Falha ao mover o desenho para a lixeira.'];
        }

        $inseriuLixo = (new \App\Models\Lixo_desenhos())->insert([
            'desenho_id' => $desenhoId,
            'usuario_id' => $usuarioId,
            'diretorio' => (string) $destino['diretorio_lixeira'],
            'nome' => (string) $destino['novo_nome'],
        ]);

        if (!$inseriuLixo) {
            @rename((string) $destino['novo_caminho'], $caminhoOriginal);
            $db->transRollback();
            return ['ok' => false, 'mensagem' => 'Falha ao registrar o desenho na lixeira.'];
        }

        $atualizou = (new \App\Models\Desenhos())->update($desenhoId, ['status' => 'apagado']);
        if (!$atualizou) {
            @rename((string) $destino['novo_caminho'], $caminhoOriginal);
            $db->transRollback();
            return ['ok' => false, 'mensagem' => 'Falha ao atualizar o status do desenho apagado.'];
        }

        $auditou = $this->registrarAlteracaoPainel(
            'desenho',
            $desenhoId,
            'painel_tarefas.apagar_desenho',
            [
                [
                    'campo' => 'status',
                    'valor_antes' => $this->decodificarValor($desenho['status'] ?? ''),
                    'valor_depois' => 'apagado',
                ],
                [
                    'campo' => 'arquivo_origem',
                    'valor_antes' => $caminhoOriginal,
                    'valor_depois' => (string) $destino['novo_caminho'],
                ],
                [
                    'campo' => 'lixeira_diretorio',
                    'valor_antes' => '',
                    'valor_depois' => (string) $destino['diretorio_lixeira'],
                ],
                [
                    'campo' => 'lixeira_nome',
                    'valor_antes' => (string) $destino['nome_original'],
                    'valor_depois' => (string) $destino['novo_nome'],
                ],
            ],
            [
                'desenho_nome' => $this->obterNomeExibicaoDesenho($desenho),
                'processo_id' => (int) ($desenho['processos_id'] ?? 0),
                'processo_nome' => $this->obterNomeProcessoPorId((int) ($desenho['processos_id'] ?? 0)),
                'usuario_executor' => $usuarioId,
                'novo_caminho_lixeira' => (string) $destino['novo_caminho'],
            ]
        );

        if (!$auditou) {
            @rename((string) $destino['novo_caminho'], $caminhoOriginal);
            $db->transRollback();
            return ['ok' => false, 'mensagem' => 'Falha ao auditar a exclusao do desenho.'];
        }

        $db->transComplete();
        if (!$db->transStatus()) {
            @rename((string) $destino['novo_caminho'], $caminhoOriginal);
            return ['ok' => false, 'mensagem' => 'Falha ao concluir a exclusao do desenho.'];
        }

        return [
            'ok' => true,
            'mensagem' => 'Desenho apagado e enviado para a lixeira.',
            'novo_caminho' => (string) $destino['novo_caminho'],
        ];
    }

    private function restaurarArquivosMovidosLote(array $movimentacoes): void
    {
        for ($indice = count($movimentacoes) - 1; $indice >= 0; $indice--) {
            $movimentacao = is_array($movimentacoes[$indice] ?? null) ? $movimentacoes[$indice] : [];
            $origem = trim((string) ($movimentacao['origem'] ?? ''));
            $destino = trim((string) ($movimentacao['destino'] ?? ''));

            if ($origem === '' || $destino === '' || !file_exists($destino)) {
                continue;
            }

            $diretorioOrigem = dirname($origem);
            if ($diretorioOrigem !== '' && !is_dir($diretorioOrigem)) {
                Ferramentas::criet_diretorio($diretorioOrigem);
            }

            @rename($destino, $origem);
        }
    }

    private function usuarioTemPermissao(array $permissoesUsuario, array $permissoesPermitidas): bool
    {
        if (in_array('all', $permissoesUsuario, true)) {
            return true;
        }

        foreach ($permissoesPermitidas as $permissao) {
            if (in_array($permissao, $permissoesUsuario, true) || in_array(str_replace(' ', '_', $permissao), $permissoesUsuario, true)) {
                return true;
            }
        }

        return false;
    }

    private function mapaAcessoAbas(array $permissoesUsuario): array
    {
        $acessoListaTarefas = $this->usuarioTemPermissao($permissoesUsuario, [
            'Lista De Corte',
            'Lista_De_Corte',
            'lista_corte',
            'lista_tarefas',
            'Lista De Corte Cortador',
            'Lista_De_Corte_Cortador',
            'lista_de_corte_cortador',
        ]);
        $acessoListaTarefasAdm = $this->usuarioTemPermissao($permissoesUsuario, [
            'Lista De Corte ADM',
            'Lista_De_Corte_ADM',
            'Lista_De_Corte ADM',
            'lista_corte_adm',
            'lista_tarefas_adm',
        ]);

        return [
            'meus_desenhos' => $this->usuarioTemPermissao($permissoesUsuario, [
                'Meus Desenhos',
                'Meus_Desenhos',
                'meus_desenhos',
                'desenho_meus',
            ]),
            'lista_tarefas' => $acessoListaTarefas,
            'lista_tarefas_adm' => $acessoListaTarefasAdm,
            'tarefas_concluidas' => $acessoListaTarefas || $acessoListaTarefasAdm,
        ];
    }

    private function permissoesAutorizacaoConclusao(): array
    {
        return [
            'all',
            'Lista De Corte ADM',
            'Lista_De_Corte_ADM',
            'Lista_De_Corte ADM',
            'lista_corte_adm',
            'lista_tarefas_adm',
        ];
    }

    private function usuarioPodeAutorizarConclusao(array $permissoesUsuario): bool
    {
        return $this->usuarioTemPermissao($permissoesUsuario, $this->permissoesAutorizacaoConclusao());
    }

    private function usuarioPodeGerenciarRecolocar(array $permissoesUsuario): bool
    {
        return $this->usuarioPodeAutorizarConclusao($permissoesUsuario);
    }

    private function idsProcessosPermitidos(): array
    {
        $processos = $this->buscarProcessosDisponiveis();
        $ids = [];
        foreach ($processos as $processo) {
            $id = (int) ($processo['id'] ?? 0);
            if ($id > 0) {
                $ids[] = $id;
            }
        }

        return array_values(array_unique($ids));
    }

    private function validarCredenciaisAutorizador(string $nome, string $senha): ?array
    {
        $nome = trim($nome);
        $senha = trim($senha);
        if ($nome === '' || $senha === '') {
            return null;
        }

        $usuario = (new \App\Models\Usuarios())
            ->where('nome', $nome)
            ->where('senha', $senha)
            ->where('status', 'ativo')
            ->first();

        if (!is_array($usuario) || empty($usuario['id'])) {
            return null;
        }

        $permissoesUsuario = (new \App\Models\Nivel())->listarPermissoesAtivas((int) ($usuario['nivel_id'] ?? 0));

        if (!$this->usuarioPodeAutorizarConclusao($permissoesUsuario)) {
            return null;
        }

        return $usuario;
    }

    private function usuarioPodeAcessarPainel(array $permissoesUsuario): bool
    {
        $acessoAbas = $this->mapaAcessoAbas($permissoesUsuario);

        return !empty(array_filter($acessoAbas, static fn ($permitido) => (bool) $permitido));
    }

    private function usuarioPodeVerProcesso(array $permissoesUsuario, array $processosUsuario, string $nomeProcesso, string $nomeProcessoCodificado): bool
    {
        if (in_array('all', $permissoesUsuario, true) || in_array('Processos', $permissoesUsuario, true)) {
            return true;
        }

        $nomeProcessoComUnderscore = str_replace(' ', '_', $nomeProcesso);

        return in_array($nomeProcesso, $processosUsuario, true)
            || in_array($nomeProcessoComUnderscore, $processosUsuario, true)
            || in_array($nomeProcessoCodificado, $processosUsuario, true);
    }

    private function menu(string $menuSelect = ''): string
    {
        $this->iniciarSessao();

        $menu = view('menu/menu', ['permissao' => $_SESSION['permissao'] ?? []]);
        if ($menuSelect !== '') {
            $menu = str_replace(
                'id="' . $menuSelect . '" class="nav-link"',
                'id="' . $menuSelect . '" class="nav-link active"',
                $menu
            );
        }

        return $menu;
    }

    private function extrairTagsDeDiretorio($diretorio): string
    {
        if (!is_string($diretorio) || $diretorio === '') {
            return '';
        }

        $tags = explode('/', str_replace('\\', '/', $diretorio));
        $tags = array_slice($tags, 6);

        if (!empty($tags)) {
            array_pop($tags);
        }

        return implode(' - ', $tags);
    }

    private function corTextoParaFundo(string $corHex): string
    {
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $corHex)) {
            return '#0f172a';
        }

        $r = hexdec(substr($corHex, 1, 2));
        $g = hexdec(substr($corHex, 3, 2));
        $b = hexdec(substr($corHex, 5, 2));
        $luminancia = (0.299 * $r) + (0.587 * $g) + (0.114 * $b);

        return $luminancia > 165 ? '#0f172a' : '#f8fafc';
    }

    private function normalizarCorPrioridade($cor): string
    {
        $cor = trim((string) $cor);
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $cor)) {
            return '#cbd5e1';
        }

        return strtoupper($cor);
    }

    private function abasDisponiveis(array $permissoesUsuario): array
    {
        if (!$this->usuarioPodeAcessarPainel($permissoesUsuario)) {
            return [];
        }

        return [
            'meus_desenhos' => 'Meus Desenhos',
            'lista_tarefas' => 'Lista de Tarefas',
            'lista_tarefas_adm' => 'Lista de Tarefas ADM',
            'tarefas_concluidas' => 'Tarefas Concluidas',
        ];
    }

    private function abaPreferidaNivel(): ?string
    {
        $this->iniciarSessao();

        $abaQuery = trim((string) $this->request->getGet('aba'));
        if ($abaQuery !== '') {
            return $abaQuery;
        }

        $nivelId = (int) ($_SESSION['nivel_id'] ?? 0);
        if ($nivelId <= 0) {
            return null;
        }

        $nivel = (new \App\Models\Nivel())->find($nivelId);
        if (!is_array($nivel)) {
            return null;
        }

        return NivelTelaInicial::abaPainel((string) ($nivel['tela_inicial'] ?? ''));
    }

    private function abaInicial(array $abas, array $acessoAbas, ?string $preferida = null): string
    {
        $preferida = trim((string) $preferida);
        if ($preferida !== '' && isset($abas[$preferida]) && !empty($acessoAbas[$preferida])) {
            return $preferida;
        }

        if (isset($abas['lista_tarefas']) && !empty($acessoAbas['lista_tarefas'])) {
            return 'lista_tarefas';
        }

        if (isset($abas['meus_desenhos']) && !empty($acessoAbas['meus_desenhos'])) {
            return 'meus_desenhos';
        }

        if (isset($abas['lista_tarefas_adm']) && !empty($acessoAbas['lista_tarefas_adm'])) {
            return 'lista_tarefas_adm';
        }

        if (isset($abas['tarefas_concluidas']) && !empty($acessoAbas['tarefas_concluidas'])) {
            return 'tarefas_concluidas';
        }

        return (string) array_key_first($abas);
    }

    private function abaPermitida(string $aba, array $acessoAbas): bool
    {
        return !empty($acessoAbas[$aba]);
    }

    private function tituloAba(string $aba): string
    {
        if ($aba === 'lista_tarefas_adm') {
            return 'Lista de Tarefas ADM';
        }

        if ($aba === 'lista_tarefas') {
            return 'Lista de Tarefas';
        }

        if ($aba === 'tarefas_concluidas') {
            return 'Tarefas Concluidas';
        }

        return 'Meus Desenhos';
    }

    private function normalizarStatusTexto(string $status): string
    {
        return strtolower(trim($status));
    }

    private function montarStatusConsulta(array $statusBase): array
    {
        $statusPermitidos = [];

        foreach ($statusBase as $status) {
            $statusNormalizado = $this->normalizarStatusTexto((string) $status);
            if ($statusNormalizado === '') {
                continue;
            }

            $statusPermitidos[$statusNormalizado] = $statusNormalizado;

            $statusCodificado = (string) Ferramentas::codificador($statusNormalizado);
            if ($statusCodificado !== '') {
                $statusPermitidos[$statusCodificado] = $statusCodificado;
            }
        }

        return array_values($statusPermitidos);
    }

    private function statusPermitidosTarefas(bool $incluirConcluidas = true): array
    {
        $statusBase = [
            'pendente',
            'cortando',
            'processando',
        ];

        if ($incluirConcluidas) {
            $statusBase = array_merge($statusBase, [
                'pronto',
                'cortado',
                'cortado_notfile',
                'concluido',
                'concluida',
                'finalizado',
                'finalizada',
            ]);
        }

        return $this->montarStatusConsulta($statusBase);
    }

    private function statusConcluidosPainel(): array
    {
        return $this->montarStatusConsulta([
            'pronto',
            'cortado',
            'cortado_notfile',
            'concluido',
            'concluida',
            'finalizado',
            'finalizada',
        ]);
    }

    private function prioridadesDisponiveis(): array
    {
        $linhas = (new \App\Models\Prioridade())
            ->where('status', 'ativo')
            ->orderBy('ordem', 'ASC')
            ->findAll();

        $prioridades = [];
        foreach ($linhas as $linha) {
            $prioridades[] = [
                'id' => (int) ($linha['id'] ?? 0),
                'nome' => $this->decodificarValor($linha['nome'] ?? ''),
                'cor' => $this->normalizarCorPrioridade($linha['cor'] ?? ''),
            ];
        }

        return $prioridades;
    }

    private function permissoesPodemVerListasSemVinculo(array $permissoesUsuario): bool
    {
        $permissoesLiberadas = [
            'all',
            'Processos',
            'Lista_De_Corte_Cortador',
            'Lista De Corte Cortador',
            'lista_de_corte_cortador',
            'Lista De Corte',
            'Lista_De_Corte',
            'Lista_De_Corte_ADM',
            'Lista De Corte ADM',
            'lista_tarefas',
            'lista_tarefas_adm',
            'lista_corte',
            'lista_corte_adm',
        ];

        foreach ($permissoesLiberadas as $permissao) {
            if (in_array($permissao, $permissoesUsuario, true)) {
                return true;
            }
        }

        return false;
    }

    private function listarNomesProcessosAtivos(): array
    {
        $processosAtivos = (new \App\Models\Processos())
            ->select('nome')
            ->where('status', 'ativo')
            ->orderBy('id', 'ASC')
            ->findAll();

        $processosUsuario = [];
        foreach ($processosAtivos as $processoAtivo) {
            $nome = trim($this->decodificarValor($processoAtivo['nome'] ?? ''));
            if ($nome !== '') {
                $processosUsuario[] = $nome;
            }
        }

        return array_values(array_unique($processosUsuario));
    }

    private function obterProcessosPermitidosSessao(): array
    {
        $this->iniciarSessao();

        $permissoesUsuario = is_array($_SESSION['permissao'] ?? null) ? $_SESSION['permissao'] : [];

        if (in_array('Lista_De_Corte_Cortador', $permissoesUsuario, true) || in_array('Lista De Corte Cortador', $permissoesUsuario, true)) {
            $processosUsuario = $this->listarNomesProcessosAtivos();
            $_SESSION['processos'] = $processosUsuario;

            return $processosUsuario;
        }

        $processosUsuario = is_array($_SESSION['processos'] ?? null) ? $_SESSION['processos'] : [];
        $processosUsuario = array_values(array_unique(array_filter(array_map('strval', $processosUsuario), static function ($valor) {
            return trim((string) $valor) !== '';
        })));

        if ($processosUsuario !== []) {
            return $processosUsuario;
        }

        $nivelId = (int) ($_SESSION['nivel_id'] ?? 0);
        if ($nivelId <= 0) {
            $usuarioId = (int) ($_SESSION['usuario'] ?? 0);
            if ($usuarioId > 0) {
                $usuario = (new \App\Models\Usuarios())
                    ->select('nivel_id')
                    ->where('id', $usuarioId)
                    ->first();

                $nivelId = (int) ($usuario['nivel_id'] ?? 0);
                if ($nivelId > 0) {
                    $_SESSION['nivel_id'] = $nivelId;
                }
            }
        }

        if ($nivelId > 0) {
            $processosDetalhados = (new \App\Models\Nivel())->listarProcessosDetalhados($nivelId);
            foreach ($processosDetalhados as $processoDetalhado) {
                $nome = trim((string) ($processoDetalhado['nome'] ?? ''));
                if ($nome !== '') {
                    $processosUsuario[] = $nome;
                }
            }
        }

        if ($processosUsuario === [] && $this->permissoesPodemVerListasSemVinculo($permissoesUsuario)) {
            $processosUsuario = $this->listarNomesProcessosAtivos();
        }

        $processosUsuario = array_values(array_unique($processosUsuario));
        $_SESSION['processos'] = $processosUsuario;

        return $processosUsuario;
    }

    private function buscarProcessosDisponiveis(): array
    {
        $this->iniciarSessao();

        $permissoesUsuario = is_array($_SESSION['permissao'] ?? null) ? $_SESSION['permissao'] : [];
        $processosUsuario = $this->obterProcessosPermitidosSessao();

        $processosAtivos = (new \App\Models\Processos())
            ->where('status', 'ativo')
            ->orderBy('id', 'ASC')
            ->findAll();

        $processos = [];
        foreach ($processosAtivos as $processo) {
            $nomeCodificado = (string) ($processo['nome'] ?? '');
            $nomeDecodificado = $this->decodificarValor($nomeCodificado);
            if ($nomeDecodificado === '') {
                continue;
            }

            if (!$this->usuarioPodeVerProcesso($permissoesUsuario, $processosUsuario, $nomeDecodificado, $nomeCodificado)) {
                continue;
            }

            $processos[] = [
                'id' => (int) ($processo['id'] ?? 0),
                'nome' => $nomeDecodificado,
            ];
        }

        return $processos;
    }

    private function mapearDesenhoParaLinha(array $desenho): array
    {
        $corPrioridade = $this->normalizarCorPrioridade($desenho['prioridade_cor'] ?? '');
        $status = $this->decodificarValor($desenho['status'] ?? '');
        $status = $status !== '' ? $status : (string) ($desenho['status'] ?? '');
        $statusNormalizado = $this->normalizarStatusTexto($status);

        $dataEnvioOriginal = (string) ($desenho['data_add'] ?? '');
        $timestamp = strtotime($dataEnvioOriginal);
        $dataEnvio = $timestamp ? date('d/m/Y H:i:s', $timestamp) : $dataEnvioOriginal;
        $dataConclusaoOriginal = (string) ($desenho['data_fim_corte'] ?? '');
        $timestampConclusao = strtotime($dataConclusaoOriginal);
        $dataConclusao = $timestampConclusao ? date('d/m/Y H:i:s', $timestampConclusao) : $dataConclusaoOriginal;

        $nomeArquivo = trim($this->decodificarValor($desenho['projeto_descricao'] ?? ''));
        if ($nomeArquivo === '') {
            $nomeArquivo = $this->decodificarValor($desenho['nome'] ?? '');
            if ($nomeArquivo !== '') {
                $nomeArquivo = Ferramentas::remove_id_file($nomeArquivo);
            }
        }

        return [
            'id' => (int) ($desenho['id'] ?? 0),
            'projeto_id' => (int) ($desenho['projeto_id'] ?? 0),
            'corte_id' => (int) ($desenho['corte_id'] ?? 0),
            'prioridade_id' => (int) ($desenho['prioridade_id'] ?? 0),
            'prioridade_nome' => $this->decodificarValor($desenho['prioridade_nome'] ?? ''),
            'prioridade_cor' => $corPrioridade,
            'prioridade_texto' => $this->corTextoParaFundo($corPrioridade),
            'ordem' => $desenho['ordem'] ?? '',
            'desenhista_nome' => $this->decodificarValor($desenho['desenhista_nome'] ?? ''),
            'nome_arquivo' => $nomeArquivo,
            'empresa_nome' => $this->decodificarValor($desenho['empresa_nome'] ?? ''),
            'empreendimento_nome' => $this->decodificarValor($desenho['empreendimento_nome'] ?? ''),
            'empreendimento_escala' => $this->decodificarValor($desenho['empreendimento_escala'] ?? ''),
            'finalidade_nome' => $this->decodificarValor($desenho['finalidade_nome'] ?? ''),
            'subpastas' => $this->extrairTagsDeDiretorio($desenho['diretorio'] ?? ''),
            'dimensao_dxf' => (string) ($desenho['dimensao_dxf'] ?? ''),
            'status' => $status,
            'status_normalizado' => $statusNormalizado,
            'data_envio' => $dataEnvio,
            'data_conclusao' => $dataConclusao,
            'recolocar_pendente' => !empty($desenho['recolocar_pendente']),
        ];
    }

    private function buscarTarefasPorProcesso(
        int $processoId,
        bool $mostrarConcluidas = false,
        bool $usarDescricaoProjeto = true
    ): array
    {
        if ($processoId <= 0) {
            return [];
        }
        $statusAtivos = $this->statusPermitidosTarefas(false);

        $desenhosAtivos = (new \App\Models\Desenhos())
            ->select("desenhos.id,
                      desenhos.corte_id,
                      desenhos.prioridade_id,
                      desenhos.nome,
                      desenhos.diretorio,
                      desenhos.status,
                      desenhos.data_add,
                      prioridade.nome AS prioridade_nome,
                      prioridade.cor AS prioridade_cor,
                      usuarios.nome AS desenhista_nome,
                      empresa.nome AS empresa_nome,
                      empreendimentos.nome AS empreendimento_nome,
                      empreendimentos.escala AS empreendimento_escala,
                      finalidade.nome AS finalidade_nome,
                      o.ordem AS ordem")
            ->join('prioridade', 'prioridade.id = desenhos.prioridade_id', 'left')
            ->join('usuarios', 'usuarios.id = desenhos.usuario_id_desenhista', 'left')
            ->join('empresa', 'empresa.id = desenhos.empresa_id', 'left')
            ->join('empreendimentos', 'empreendimentos.id = desenhos.empreendimentos_id', 'left')
            ->join('finalidade', 'finalidade.id = desenhos.finalidade_id', 'left')
            ->join('ordem o', "o.desenho_id = desenhos.id AND o.status = 'ativo'", 'left')
            ->where('desenhos.processos_id', $processoId)
            ->whereIn('desenhos.status', $statusAtivos)
            ->orderBy('prioridade.ordem IS NULL', 'ASC', false)
            ->orderBy('prioridade.ordem', 'ASC')
            ->orderBy('o.ordem IS NULL', 'ASC', false)
            ->orderBy('o.ordem', 'ASC')
            ->orderBy('desenhos.data_add', 'ASC')
            ->orderBy('desenhos.id', 'ASC')
            ->findAll();

        if ($usarDescricaoProjeto) {
            $desenhosAtivos = $this->aplicarDescricaoProjetoAoInd($desenhosAtivos, $processoId);
        }
        $desenhosAtivos = $this->enriquecerDesenhosComDimensoesDxf($desenhosAtivos);

        $tarefas = [];
        foreach ($desenhosAtivos as $desenho) {
            $tarefas[] = $this->mapearDesenhoParaLinha($desenho);
        }

        if (!$mostrarConcluidas) {
            return $tarefas;
        }

        $statusConcluidos = $this->statusConcluidosPainel();
        if (empty($statusConcluidos)) {
            return $tarefas;
        }

        $desenhosConcluidos = (new \App\Models\Desenhos())
            ->select("desenhos.id,
                      desenhos.corte_id,
                      desenhos.prioridade_id,
                      desenhos.nome,
                      desenhos.diretorio,
                      desenhos.status,
                      desenhos.data_add,
                      prioridade.nome AS prioridade_nome,
                      prioridade.cor AS prioridade_cor,
                      usuarios.nome AS desenhista_nome,
                      empresa.nome AS empresa_nome,
                      empreendimentos.nome AS empreendimento_nome,
                      empreendimentos.escala AS empreendimento_escala,
                      finalidade.nome AS finalidade_nome,
                      o.ordem AS ordem")
            ->join('prioridade', 'prioridade.id = desenhos.prioridade_id', 'left')
            ->join('usuarios', 'usuarios.id = desenhos.usuario_id_desenhista', 'left')
            ->join('empresa', 'empresa.id = desenhos.empresa_id', 'left')
            ->join('empreendimentos', 'empreendimentos.id = desenhos.empreendimentos_id', 'left')
            ->join('finalidade', 'finalidade.id = desenhos.finalidade_id', 'left')
            ->join('ordem o', "o.desenho_id = desenhos.id AND o.status = 'ativo'", 'left')
            ->where('desenhos.processos_id', $processoId)
            ->whereIn('desenhos.status', $statusConcluidos)
            ->orderBy('desenhos.data_add', 'DESC')
            ->orderBy('desenhos.id', 'DESC')
            ->limit(self::LIMITE_CONCLUIDAS_TAREFAS)
            ->findAll();

        if ($usarDescricaoProjeto) {
            $desenhosConcluidos = $this->aplicarDescricaoProjetoAoInd($desenhosConcluidos, $processoId);
        }
        $desenhosConcluidos = $this->enriquecerDesenhosComDimensoesDxf($desenhosConcluidos);

        foreach ($desenhosConcluidos as $desenhoConcluido) {
            $tarefas[] = $this->mapearDesenhoParaLinha($desenhoConcluido);
        }

        return $tarefas;
    }

    private function buscarTarefasConcluidasPorProcesso(
        int $processoId,
        bool $aplicarPeriodo = false,
        ?string $dataInicio = null,
        ?string $dataFim = null
    ): array
    {
        if ($processoId <= 0 || !$aplicarPeriodo) {
            return [];
        }

        $periodo = $this->normalizarPeriodo($dataInicio, $dataFim);
        if ($periodo === null) {
            return [];
        }

        $statusConcluidos = $this->statusConcluidosPainel();
        if (empty($statusConcluidos)) {
            return [];
        }

        if ($this->processoUsaDescricaoProjeto($processoId)) {
            return $this->buscarTarefasConcluidasAgrupadasPorProjeto($processoId, $periodo, $statusConcluidos);
        }

        $desenhos = (new \App\Models\Desenhos())
            ->select("desenhos.id,
                      desenhos.corte_id,
                      desenhos.prioridade_id,
                      desenhos.nome,
                      desenhos.diretorio,
                      desenhos.status,
                      desenhos.data_add,
                      prioridade.nome AS prioridade_nome,
                      prioridade.cor AS prioridade_cor,
                      usuarios.nome AS desenhista_nome,
                      empresa.nome AS empresa_nome,
                      empreendimentos.nome AS empreendimento_nome,
                      empreendimentos.escala AS empreendimento_escala,
                      finalidade.nome AS finalidade_nome,
                      o.ordem AS ordem,
                      c.data_end AS data_fim_corte")
            ->join('prioridade', 'prioridade.id = desenhos.prioridade_id', 'left')
            ->join('usuarios', 'usuarios.id = desenhos.usuario_id_desenhista', 'left')
            ->join('empresa', 'empresa.id = desenhos.empresa_id', 'left')
            ->join('empreendimentos', 'empreendimentos.id = desenhos.empreendimentos_id', 'left')
            ->join('finalidade', 'finalidade.id = desenhos.finalidade_id', 'left')
            ->join('ordem o', "o.desenho_id = desenhos.id AND o.status = 'ativo'", 'left')
            ->join('corte c', 'c.id = desenhos.corte_id', 'left')
            ->where('desenhos.processos_id', $processoId)
            ->whereIn('desenhos.status', $statusConcluidos)
            ->groupStart()
                ->groupStart()
                    ->where('c.data_end >=', $periodo['inicio'])
                    ->where('c.data_end <=', $periodo['fim'])
                ->groupEnd()
                ->orGroupStart()
                    ->where('c.data_end', null)
                    ->where('desenhos.data_add >=', $periodo['inicio'])
                    ->where('desenhos.data_add <=', $periodo['fim'])
                ->groupEnd()
            ->groupEnd()
            ->orderBy('c.data_end IS NULL', 'ASC', false)
            ->orderBy('c.data_end', 'DESC')
            ->orderBy('desenhos.data_add', 'DESC')
            ->orderBy('desenhos.id', 'DESC')
            ->findAll();

        $desenhos = $this->aplicarDescricaoProjetoAoInd($desenhos, $processoId);
        $recolocarPendentesMap = [];
        $desenhoIds = array_values(array_filter(array_map(static fn ($row) => (int) ($row['id'] ?? 0), $desenhos)));
        if (!empty($desenhoIds)) {
            $recolocarPendentes = (new \App\Models\Recolocar_desenho())
                ->select('desenhos_id')
                ->whereIn('desenhos_id', $desenhoIds)
                ->where('status', 'pendente')
                ->findAll();

            foreach ($recolocarPendentes as $pendencia) {
                $recolocarPendentesMap[(int) ($pendencia['desenhos_id'] ?? 0)] = true;
            }
        }

        $tarefas = [];
        foreach ($desenhos as $desenho) {
            $desenho['recolocar_pendente'] = isset($recolocarPendentesMap[(int) ($desenho['id'] ?? 0)]);
            $tarefas[] = $this->mapearDesenhoParaLinha($desenho);
        }

        return $tarefas;
    }

    private function buscarTarefasConcluidasAgrupadasPorProjeto(int $processoId, array $periodo, array $statusConcluidos): array
    {
        $linhas = (new \App\Models\Projeto_desenho())
            ->select("projeto_desenho.projeto_id,
                      projeto.descricao AS projeto_descricao,
                      projeto.status AS projeto_status,
                      desenhos.id AS id,
                      desenhos.corte_id,
                      desenhos.prioridade_id,
                      desenhos.nome,
                      desenhos.diretorio,
                      desenhos.status,
                      desenhos.data_add,
                      prioridade.nome AS prioridade_nome,
                      prioridade.cor AS prioridade_cor,
                      usuarios.nome AS desenhista_nome,
                      empresa.nome AS empresa_nome,
                      empreendimentos.nome AS empreendimento_nome,
                      empreendimentos.escala AS empreendimento_escala,
                      finalidade.nome AS finalidade_nome,
                      o.ordem AS ordem,
                      c.data_end AS data_fim_corte")
            ->join('projeto', 'projeto.id = projeto_desenho.projeto_id', 'inner')
            ->join('desenhos', 'desenhos.id = projeto_desenho.desenho_id', 'inner')
            ->join('prioridade', 'prioridade.id = desenhos.prioridade_id', 'left')
            ->join('usuarios', 'usuarios.id = desenhos.usuario_id_desenhista', 'left')
            ->join('empresa', 'empresa.id = desenhos.empresa_id', 'left')
            ->join('empreendimentos', 'empreendimentos.id = desenhos.empreendimentos_id', 'left')
            ->join('finalidade', 'finalidade.id = desenhos.finalidade_id', 'left')
            ->join('ordem o', "o.projeto_id = projeto_desenho.projeto_id AND o.desenho_id IS NULL AND o.status = 'ativo'", 'left')
            ->join('corte c', 'c.id = desenhos.corte_id', 'left')
            ->where('desenhos.processos_id', $processoId)
            ->whereIn('desenhos.status', $statusConcluidos)
            ->where('projeto.status !=', 'apagado')
            ->groupStart()
                ->groupStart()
                    ->where('c.data_end >=', $periodo['inicio'])
                    ->where('c.data_end <=', $periodo['fim'])
                ->groupEnd()
                ->orGroupStart()
                    ->where('c.data_end', null)
                    ->where('desenhos.data_add >=', $periodo['inicio'])
                    ->where('desenhos.data_add <=', $periodo['fim'])
                ->groupEnd()
            ->groupEnd()
            ->orderBy('projeto_desenho.projeto_id', 'ASC')
            ->orderBy('c.data_end IS NULL', 'ASC', false)
            ->orderBy('c.data_end', 'DESC')
            ->orderBy('desenhos.data_add', 'DESC')
            ->orderBy('desenhos.id', 'DESC')
            ->findAll();

        if ($linhas === []) {
            return [];
        }

        $desenhoIds = [];
        $grupos = [];
        foreach ($linhas as $linha) {
            $projetoId = (int) ($linha['projeto_id'] ?? 0);
            $desenhoId = (int) ($linha['id'] ?? 0);
            if ($projetoId <= 0 || $desenhoId <= 0) {
                continue;
            }

            $desenhoIds[$desenhoId] = $desenhoId;
            if (!isset($grupos[$projetoId])) {
                $grupos[$projetoId] = [
                    'referencia' => $linha,
                    'arquivos' => [],
                    'timestamp' => $this->timestampConclusaoProjeto($linha),
                ];
            }

            $grupos[$projetoId]['arquivos'][] = $linha;
            $timestampLinha = $this->timestampConclusaoProjeto($linha);
            if ($timestampLinha > (int) ($grupos[$projetoId]['timestamp'] ?? 0)) {
                $grupos[$projetoId]['referencia'] = $linha;
                $grupos[$projetoId]['timestamp'] = $timestampLinha;
            }
        }

        if ($grupos === []) {
            return [];
        }

        $recolocarPendentesMap = [];
        if ($desenhoIds !== []) {
            $recolocarPendentes = (new \App\Models\Recolocar_desenho())
                ->select('desenhos_id')
                ->whereIn('desenhos_id', array_values($desenhoIds))
                ->where('status', 'pendente')
                ->findAll();

            foreach ($recolocarPendentes as $pendencia) {
                $recolocarPendentesMap[(int) ($pendencia['desenhos_id'] ?? 0)] = true;
            }
        }

        uasort($grupos, static function (array $a, array $b): int {
            $comparacao = (int) ($b['timestamp'] ?? 0) <=> (int) ($a['timestamp'] ?? 0);
            if ($comparacao !== 0) {
                return $comparacao;
            }

            return (int) ($b['referencia']['projeto_id'] ?? 0) <=> (int) ($a['referencia']['projeto_id'] ?? 0);
        });

        $tarefas = [];
        foreach ($grupos as $projetoId => $grupo) {
            $referencia = is_array($grupo['referencia'] ?? null) ? $grupo['referencia'] : [];
            if ($referencia === []) {
                continue;
            }

            $nomesArquivos = [];
            $recolocarPendente = false;
            foreach (($grupo['arquivos'] ?? []) as $arquivo) {
                $arquivoId = (int) ($arquivo['id'] ?? 0);
                if ($arquivoId > 0 && isset($recolocarPendentesMap[$arquivoId])) {
                    $recolocarPendente = true;
                }

                $nomeArquivo = $this->obterNomeExibicaoDesenho($arquivo);
                if ($nomeArquivo !== '') {
                    $nomesArquivos[] = $nomeArquivo;
                }
            }

            $descricaoProjeto = trim($this->decodificarValor($referencia['projeto_descricao'] ?? ''));
            if ($descricaoProjeto === '') {
                $descricaoProjeto = 'Projeto #' . (int) $projetoId;
            }

            $referencia['projeto_descricao'] = $descricaoProjeto;
            $referencia['recolocar_pendente'] = $recolocarPendente;
            $tarefa = $this->mapearDesenhoParaLinha($referencia);
            $tarefa['id'] = (int) ($referencia['id'] ?? 0);
            $tarefa['desenho_id'] = (int) ($referencia['id'] ?? 0);
            $tarefa['projeto_id'] = (int) $projetoId;
            $tarefa['item_tipo'] = 'projeto';
            $tarefa['nome_arquivo'] = $descricaoProjeto;
            $tarefa['arquivos_count'] = count($grupo['arquivos'] ?? []);
            $tarefa['arquivos_vinculados'] = array_values(array_unique($nomesArquivos));
            $tarefas[] = $tarefa;
        }

        return $tarefas;
    }

    private function timestampConclusaoProjeto(array $linha): int
    {
        $valor = trim((string) ($linha['data_fim_corte'] ?? ''));
        if ($valor === '') {
            $valor = trim((string) ($linha['data_add'] ?? ''));
        }

        $timestamp = strtotime($valor);
        return $timestamp !== false ? $timestamp : 0;
    }

    private function normalizarPeriodo(?string $dataInicio, ?string $dataFim): ?array
    {
        $dataInicio = trim((string) $dataInicio);
        $dataFim = trim((string) $dataFim);
        if ($dataInicio === '' || $dataFim === '') {
            return null;
        }

        $inicio = \DateTime::createFromFormat('Y-m-d', $dataInicio);
        $fim = \DateTime::createFromFormat('Y-m-d', $dataFim);
        if (!$inicio || !$fim) {
            return null;
        }

        $inicio->setTime(0, 0, 0);
        $fim->setTime(23, 59, 59);
        if ($inicio > $fim) {
            return null;
        }

        return [
            'inicio' => $inicio->format('Y-m-d H:i:s'),
            'fim' => $fim->format('Y-m-d H:i:s'),
        ];
    }

    private function periodoPadraoMeusDesenhos(): array
    {
        $fim = new \DateTimeImmutable('today');
        $inicio = $fim->sub(new \DateInterval('P6D'));

        return [
            'inicio_data' => $inicio->format('Y-m-d'),
            'fim_data' => $fim->format('Y-m-d'),
        ];
    }

    private function periodoPadraoTarefasConcluidas(): array
    {
        return $this->periodoPadraoMeusDesenhos();
    }

    private function buscarMeusDesenhosPorProcesso(
        int $processoId,
        int $usuarioId,
        bool $aplicarPeriodo = false,
        ?string $dataInicio = null,
        ?string $dataFim = null
    ): array
    {
        if ($processoId <= 0 || $usuarioId <= 0) {
            return [];
        }

        $periodo = null;
        if ($aplicarPeriodo) {
            $periodo = $this->normalizarPeriodo($dataInicio, $dataFim);
            if ($periodo === null) {
                return [];
            }
        }

        if ($this->deveAgruparProjetosNoPainel('meus_desenhos', $processoId)) {
            return $this->buscarTarefasAgrupadasPorProjeto($processoId, $usuarioId, $periodo);
        }

        $consulta = (new \App\Models\Desenhos())
            ->select("desenhos.id,
                      desenhos.prioridade_id,
                      desenhos.nome,
                      desenhos.diretorio,
                      desenhos.status,
                      desenhos.data_add,
                      prioridade.nome AS prioridade_nome,
                      prioridade.cor AS prioridade_cor,
                      usuarios.nome AS desenhista_nome,
                      empresa.nome AS empresa_nome,
                      empreendimentos.nome AS empreendimento_nome,
                      empreendimentos.escala AS empreendimento_escala,
                      finalidade.nome AS finalidade_nome,
                      o.ordem AS ordem")
            ->join('prioridade', 'prioridade.id = desenhos.prioridade_id', 'left')
            ->join('usuarios', 'usuarios.id = desenhos.usuario_id_desenhista', 'left')
            ->join('empresa', 'empresa.id = desenhos.empresa_id', 'left')
            ->join('empreendimentos', 'empreendimentos.id = desenhos.empreendimentos_id', 'left')
            ->join('finalidade', 'finalidade.id = desenhos.finalidade_id', 'left')
            ->join('ordem o', "o.desenho_id = desenhos.id AND o.status = 'ativo'", 'left')
            ->where('desenhos.processos_id', $processoId)
            ->where('desenhos.usuario_id_desenhista', $usuarioId);

        if ($periodo !== null) {
            $consulta
                ->where('desenhos.data_add >=', $periodo['inicio'])
                ->where('desenhos.data_add <=', $periodo['fim']);
        }

        $desenhosAtivos = $consulta
            ->orderBy('prioridade.ordem IS NULL', 'ASC', false)
            ->orderBy('prioridade.ordem', 'ASC')
            ->orderBy('o.ordem IS NULL', 'ASC', false)
            ->orderBy('o.ordem', 'ASC')
            ->orderBy('desenhos.data_add', 'ASC')
            ->orderBy('desenhos.id', 'ASC')
            ->findAll();

        $desenhosAtivos = $this->aplicarDescricaoProjetoAoInd($desenhosAtivos, $processoId);
        $listaAtivos = [];
        foreach ($desenhosAtivos as $desenhoAtivo) {
            $listaAtivos[] = $this->mapearDesenhoParaLinha($desenhoAtivo);
        }

        return $listaAtivos;
    }

    private function renderizarVazioPorAba(string $aba, string $mensagem): string
    {
        if ($aba === 'meus_desenhos') {
            return view('painel/_meus_desenhos_partial', [
                'processoNome' => '',
                'desenhos' => [],
                'mensagem' => $mensagem,
            ]);
        }

        return view('painel/_tarefas_lista_partial', [
            'aba' => $aba,
            'tituloLista' => $this->tituloAba($aba),
            'processoNome' => '',
            'tarefas' => [],
            'prioridades' => [],
            'mensagem' => $mensagem,
            'mostrarDimensaoDxf' => false,
        ]);
    }

    private function sincronizarOrdensDoProcesso(int $processoId): void
    {
        if ($processoId <= 0) {
            return;
        }

        $this->iniciarSessao();
        $agora = time();
        $chaveSessao = 'wl_painel_ordens_sync_last_' . $processoId;
        $ultimoSync = (int) ($_SESSION[$chaveSessao] ?? 0);
        if (($agora - $ultimoSync) < 20) {
            return;
        }

        try {
            if ($this->processoUsaDescricaoProjeto($processoId)) {
                $this->sincronizarOrdensProjetosDoProcesso($processoId);
                $_SESSION[$chaveSessao] = $agora;
                return;
            }

            Ferramentas::ordenarOrdems($processoId);
            $_SESSION[$chaveSessao] = $agora;
        } catch (\Throwable $e) {
            log_message('error', 'PainelTarefas: falha ao sincronizar ordens do processo ' . $processoId . ': ' . $e->getMessage());
        }
    }

    private function renderizarConteudoAba(
        string $aba,
        int $processoId,
        array $processosIndexados,
        int $usuarioId,
        array $acessoAbas,
        bool $mostrarConcluidas = false,
        bool $periodoFinalizadosAplicado = false,
        string $dataInicioFinalizados = '',
        string $dataFimFinalizados = '',
        bool $periodoMeusAplicado = false,
        string $dataInicioMeus = '',
        string $dataFimMeus = ''
    ): string
    {
        if (!$this->abaPermitida($aba, $acessoAbas)) {
            return $this->renderizarVazioPorAba($aba, 'Sem permissao para acessar esta aba.');
        }

        if ($processoId <= 0 || !isset($processosIndexados[$processoId])) {
            return $this->renderizarVazioPorAba($aba, 'Processo invalido ou sem permissao.');
        }

        $this->sincronizarOrdensDoProcesso($processoId);

        if ($aba === 'meus_desenhos') {
            $mensagemMeus = '';
            if (!$periodoMeusAplicado) {
                $mensagemMeus = 'Selecione o periodo e clique em "Buscar meus desenhos" para carregar a lista.';
            }

            $meusDesenhos = $this->buscarMeusDesenhosPorProcesso($processoId, $usuarioId, $periodoMeusAplicado, $dataInicioMeus, $dataFimMeus);
            return view('painel/_meus_desenhos_partial', [
                'processoNome' => $processosIndexados[$processoId],
                'desenhos' => $meusDesenhos,
                'mensagem' => $mensagemMeus,
                'rotuloNome' => $this->processoUsaDescricaoProjeto($processoId) ? 'Descricao' : 'Nome do arquivo',
            ]);
        }

        if ($aba === 'tarefas_concluidas') {
            $mensagemConcluidas = '';
            if (!$periodoFinalizadosAplicado) {
                $mensagemConcluidas = 'Selecione o periodo e clique em "Buscar finalizados" para carregar tarefas concluidas de todos os desenhistas.';
            }

            $tarefasConcluidas = $this->buscarTarefasConcluidasPorProcesso(
                $processoId,
                $periodoFinalizadosAplicado,
                $dataInicioFinalizados,
                $dataFimFinalizados
            );

            return view('painel/_tarefas_lista_partial', [
                'aba' => $aba,
                'tituloLista' => $this->tituloAba($aba),
                'processoNome' => $processosIndexados[$processoId],
                'tarefas' => $tarefasConcluidas,
                'prioridades' => [],
                'mensagem' => $mensagemConcluidas,
                'rotuloNome' => $this->processoUsaDescricaoProjeto($processoId) ? 'Descricao' : 'Nome do arquivo',
                'mostrarDimensaoDxf' => false,
                'agrupadoPorProjeto' => $this->processoUsaDescricaoProjeto($processoId),
            ]);
        }

        $agruparProjetos = $this->deveAgruparProjetosNoPainel($aba, $processoId);
        if (in_array($aba, ['lista_tarefas', 'lista_tarefas_adm'], true)) {
            $tarefasModel = new \App\Models\TarefasFilaModel();
            $processo = $tarefasModel->processoPorId($processoId);
            $resposta = $processo
                ? $tarefasModel->listarPorProcesso($processo, [
                    'usuario_id' => $usuarioId,
                    'mostrar_dimensao_dxf' => $aba === 'lista_tarefas' && $this->processoExibeDimensaoDxf($processoId),
                    'agrupar_projetos' => $agruparProjetos,
                    'sem_limite' => true,
                ])
                : ['data' => []];
            $tarefas = is_array($resposta['data'] ?? null) ? $resposta['data'] : [];
            $this->ultimaPerformanceListaPainel = [
                'registros' => count($tarefas),
                'query_ms' => (float) ($resposta['performance']['query_ms'] ?? 0),
            ];
        } else {
            $tarefas = $agruparProjetos
                ? $this->buscarTarefasAgrupadasPorProjeto($processoId)
                : $this->buscarTarefasPorProcesso($processoId, false, $aba !== 'lista_tarefas_adm');
        }
        $prioridades = $aba === 'lista_tarefas_adm' ? $this->prioridadesDisponiveis() : [];
        $rotuloNome = $agruparProjetos
            ? 'Descricao'
            : ($aba === 'lista_tarefas_adm'
            ? 'Nome do arquivo'
            : ($this->processoUsaDescricaoProjeto($processoId) ? 'Descricao' : 'Nome do arquivo'));

        return view('painel/_tarefas_lista_partial', [
            'aba' => $aba,
            'tituloLista' => $this->tituloAba($aba),
            'processoNome' => $processosIndexados[$processoId],
            'tarefas' => $tarefas,
            'prioridades' => $prioridades,
            'mensagem' => '',
            'rotuloNome' => $rotuloNome,
            'mostrarDimensaoDxf' => $aba === 'lista_tarefas' && $this->processoExibeDimensaoDxf($processoId),
            'agrupadoPorProjeto' => $agruparProjetos,
        ]);
    }

    private function contarQueriesRegistradasPainelTarefas(): ?int
    {
        $db = \Config\Database::connect();
        return method_exists($db, 'getQueries') ? count($db->getQueries()) : null;
    }

    private function contarQueriesExecutadasDesdePainelTarefas(?int $inicio): ?int
    {
        $fim = $this->contarQueriesRegistradasPainelTarefas();
        if ($inicio === null || $fim === null) {
            return null;
        }

        return max(0, $fim - $inicio);
    }

    private function logarPerformancePainelTarefasBackend(string $origem, array $dados): void
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

        log_message(
            'info',
            sprintf(
                '[perf:%s] processo_id=%s processo="%s" tipo=%s registros=%d query_ms=%.2f html_ms=%.2f sync_ms=%.2f total_ms=%.2f queries=%s lentos=%s',
                $origem,
                (string) ($dados['processo_id'] ?? ''),
                (string) ($dados['processo'] ?? ''),
                (string) ($dados['tipo'] ?? ''),
                (int) ($dados['registros'] ?? 0),
                (float) ($dados['query_ms'] ?? 0),
                (float) ($dados['html_ms'] ?? 0),
                (float) ($dados['sync_ms'] ?? 0),
                (float) ($dados['total_ms'] ?? 0),
                ($dados['queries'] ?? null) === null ? 'n/a' : (string) $dados['queries'],
                $pontos === [] ? 'nenhum' : implode(',', $pontos)
            )
        );
    }

    private function podeManipularDesenhoAdm(int $desenhoId, array $acessoAbas): bool
    {
        if ($desenhoId <= 0 || empty($acessoAbas['lista_tarefas_adm'])) {
            return false;
        }

        $processosPermitidos = $this->buscarProcessosDisponiveis();
        $processosPermitidosMap = array_fill_keys(array_map(static fn ($p) => (int) ($p['id'] ?? 0), $processosPermitidos), true);

        $desenho = (new \App\Models\Desenhos())
            ->select('id, processos_id')
            ->where('id', $desenhoId)
            ->first();

        if (!is_array($desenho) || empty($desenho['id'])) {
            return false;
        }

        return isset($processosPermitidosMap[(int) ($desenho['processos_id'] ?? 0)]);
    }

    private function desenhoPertenceProcessoPermitido(int $desenhoId): bool
    {
        if ($desenhoId <= 0) {
            return false;
        }

        $processosPermitidos = $this->buscarProcessosDisponiveis();
        $processosPermitidosMap = array_fill_keys(array_map(static fn ($p) => (int) ($p['id'] ?? 0), $processosPermitidos), true);

        $desenho = (new \App\Models\Desenhos())
            ->select('id, processos_id')
            ->where('id', $desenhoId)
            ->first();

        if (!is_array($desenho) || empty($desenho['id'])) {
            return false;
        }

        return isset($processosPermitidosMap[(int) ($desenho['processos_id'] ?? 0)]);
    }

    private function carregarResumoProjetoAdm(int $projetoId): ?array
    {
        if ($projetoId <= 0) {
            return null;
        }

        $statusPermitidos = $this->statusPermitidosTarefas(false);
        $desenhosProjeto = $this->buscarDesenhosPendentesProjeto($projetoId, $statusPermitidos);
        if ($desenhosProjeto === []) {
            return null;
        }

        $desenhoReferencia = $desenhosProjeto[0];
        $processoId = (int) ($desenhoReferencia['processos_id'] ?? 0);
        $prioridadeId = (int) ($desenhoReferencia['prioridade_id'] ?? 0);
        if ($processoId <= 0 || $prioridadeId <= 0) {
            return null;
        }

        $this->garantirOrdemAtivaProjeto($projetoId, $processoId, $prioridadeId);

        $ordem = (new \App\Models\Ordem())
            ->select('ordem, prioridade_id')
            ->where('projeto_id', $projetoId)
            ->where('desenho_id IS NULL', null, false)
            ->where('processos_id', $processoId)
            ->where('status', 'ativo')
            ->first();

        $projeto = (new \App\Models\Projeto())
            ->select('id, descricao, status')
            ->where('id', $projetoId)
            ->first();

        return [
            'projeto_id' => $projetoId,
            'descricao' => is_array($projeto) ? $this->decodificarValor($projeto['descricao'] ?? '') : '',
            'status' => is_array($projeto) ? (string) ($projeto['status'] ?? '') : '',
            'processo_id' => $processoId,
            'prioridade_id' => (int) ($ordem['prioridade_id'] ?? $prioridadeId),
            'ordem' => (int) ($ordem['ordem'] ?? 0),
            'desenho_referencia_id' => (int) ($desenhoReferencia['id'] ?? 0),
            'desenhos' => $desenhosProjeto,
        ];
    }

    private function podeManipularProjetoAdm(int $projetoId, array $acessoAbas): bool
    {
        if ($projetoId <= 0 || empty($acessoAbas['lista_tarefas_adm'])) {
            return false;
        }

        $resumo = $this->carregarResumoProjetoAdm($projetoId);
        if (!is_array($resumo)) {
            return false;
        }

        $processosPermitidos = $this->buscarProcessosDisponiveis();
        $processosPermitidosMap = array_fill_keys(array_map(static fn ($p) => (int) ($p['id'] ?? 0), $processosPermitidos), true);
        $processoId = (int) ($resumo['processo_id'] ?? 0);

        return $processoId > 0
            && isset($processosPermitidosMap[$processoId])
            && $this->processoUsaDescricaoProjeto($processoId);
    }

    private function idsDesenhosProjeto(int $projetoId, ?array $statusPermitidos = null): array
    {
        if ($projetoId <= 0) {
            return [];
        }

        $builder = (new \App\Models\Projeto_desenho())
            ->select('desenhos.id')
            ->join('desenhos', 'desenhos.id = projeto_desenho.desenho_id', 'inner')
            ->where('projeto_desenho.projeto_id', $projetoId);

        if (is_array($statusPermitidos) && $statusPermitidos !== []) {
            $builder->whereIn('desenhos.status', $statusPermitidos);
        }

        $linhas = $builder->findAll();

        return array_values(array_unique(array_filter(array_map(static function ($linha): int {
            return (int) ($linha['id'] ?? 0);
        }, $linhas))));
    }

    private function aplicarMudancaPrioridadeProjeto(
        int $projetoId,
        int $prioridadeId,
        ?int $ordemDestino,
        array $acessoAbas
    ): array {
        if (!$this->podeManipularProjetoAdm($projetoId, $acessoAbas)) {
            return ['ok' => false, 'mensagem' => 'Sem permissao para alterar este projeto.'];
        }

        if ($prioridadeId <= 0) {
            return ['ok' => false, 'mensagem' => 'Prioridade invalida para o projeto.'];
        }

        $prioridadeExiste = (new \App\Models\Prioridade())
            ->where('id', $prioridadeId)
            ->where('status', 'ativo')
            ->first();
        if (!is_array($prioridadeExiste)) {
            return ['ok' => false, 'mensagem' => 'Prioridade nao encontrada.'];
        }

        $resumo = $this->carregarResumoProjetoAdm($projetoId);
        if (!is_array($resumo)) {
            return ['ok' => false, 'mensagem' => 'Projeto nao encontrado na fila.'];
        }

        $processoId = (int) ($resumo['processo_id'] ?? 0);
        $prioridadeAntes = (int) ($resumo['prioridade_id'] ?? 0);
        $ordemAntes = (int) ($resumo['ordem'] ?? 0);
        $desenhoIds = $this->idsDesenhosProjeto($projetoId, $this->statusPermitidosTarefas(false));
        if ($desenhoIds === []) {
            return ['ok' => false, 'mensagem' => 'Projeto sem desenhos ativos para alterar.'];
        }

        $ordemDestino = (int) ($ordemDestino ?? 0);
        if ($ordemDestino <= 0) {
            $maxLinha = (new \App\Models\Ordem())
                ->selectMax('ordem', 'max_ordem')
                ->where('desenho_id IS NULL', null, false)
                ->where('processos_id', $processoId)
                ->where('prioridade_id', $prioridadeId)
                ->where('status', 'ativo')
                ->first();
            $ordemDestino = ((int) ($maxLinha['max_ordem'] ?? 0)) + 1;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $db->table('desenhos')
            ->whereIn('id', $desenhoIds)
            ->update(['prioridade_id' => $prioridadeId]);

        try {
            Ferramentas::reordenarPorPrioridade($projetoId, $ordemDestino, $prioridadeId, $processoId, true);
        } catch (\Throwable $e) {
            $db->transRollback();
            return ['ok' => false, 'mensagem' => 'Falha ao ajustar ordem do projeto: ' . $e->getMessage()];
        }

        $ordemDepoisRow = (new \App\Models\Ordem())
            ->select('ordem')
            ->where('projeto_id', $projetoId)
            ->where('desenho_id IS NULL', null, false)
            ->where('processos_id', $processoId)
            ->where('prioridade_id', $prioridadeId)
            ->where('status', 'ativo')
            ->first();
        $ordemDepois = (int) ($ordemDepoisRow['ordem'] ?? $ordemDestino);

        $auditou = $this->registrarAlteracaoPainel(
            'projeto',
            $projetoId,
            'painel_tarefas.projeto.mudar_prioridade',
            [
                [
                    'campo' => 'prioridade_id',
                    'valor_antes' => (string) $prioridadeAntes,
                    'valor_depois' => (string) $prioridadeId,
                ],
                [
                    'campo' => 'ordem',
                    'valor_antes' => (string) $ordemAntes,
                    'valor_depois' => (string) $ordemDepois,
                ],
            ],
            [
                'processo_id' => $processoId,
                'processo_nome' => $this->obterNomeProcessoPorId($processoId),
                'desenhos_afetados' => $desenhoIds,
            ]
        );

        if (!$auditou) {
            $db->transRollback();
            return ['ok' => false, 'mensagem' => 'Falha ao auditar a alteracao do projeto.'];
        }

        $db->transComplete();
        if (!$db->transStatus()) {
            return ['ok' => false, 'mensagem' => 'Falha ao concluir a alteracao do projeto.'];
        }

        return ['ok' => true, 'mensagem' => 'Projeto atualizado com sucesso.'];
    }

    private function moverOrdemProjeto(int $projetoId, string $direcao, array $acessoAbas): array
    {
        if (!$this->podeManipularProjetoAdm($projetoId, $acessoAbas)) {
            return ['ok' => false, 'mensagem' => 'Sem permissao para ordenar este projeto.'];
        }

        $resumo = $this->carregarResumoProjetoAdm($projetoId);
        if (!is_array($resumo)) {
            return ['ok' => false, 'mensagem' => 'Projeto nao encontrado na fila.'];
        }

        $processoId = (int) ($resumo['processo_id'] ?? 0);
        $prioridadeId = (int) ($resumo['prioridade_id'] ?? 0);
        $ordemAtualValor = (int) ($resumo['ordem'] ?? 0);
        if ($processoId <= 0 || $prioridadeId <= 0 || $ordemAtualValor <= 0) {
            return ['ok' => false, 'mensagem' => 'Ordem atual invalida para mover o projeto.'];
        }

        $direcao = strtolower(trim($direcao));
        if (!in_array($direcao, ['up', 'down'], true)) {
            return ['ok' => false, 'mensagem' => 'Direcao de ordem invalida.'];
        }

        $vizinhoBuilder = (new \App\Models\Ordem())
            ->select('ordem')
            ->where('processos_id', $processoId)
            ->where('prioridade_id', $prioridadeId)
            ->where('status', 'ativo')
            ->where('desenho_id IS NULL', null, false)
            ->where('projeto_id !=', $projetoId);
        if ($direcao === 'up') {
            $vizinhoBuilder->where('ordem <', $ordemAtualValor)->orderBy('ordem', 'DESC');
        } else {
            $vizinhoBuilder->where('ordem >', $ordemAtualValor)->orderBy('ordem', 'ASC');
        }

        $vizinho = $vizinhoBuilder->first();
        $novaOrdem = (int) ($vizinho['ordem'] ?? 0);
        if ($novaOrdem <= 0 || $novaOrdem === $ordemAtualValor) {
            return ['ok' => false, 'mensagem' => 'Projeto ja esta no limite da ordem.'];
        }

        return $this->aplicarMudancaPrioridadeProjeto($projetoId, $prioridadeId, $novaOrdem, $acessoAbas);
    }

    private function apagarProjetoPainel(int $projetoId, int $usuarioId, array $acessoAbas): array
    {
        if (!$this->podeManipularProjetoAdm($projetoId, $acessoAbas)) {
            return ['ok' => false, 'mensagem' => 'Sem permissao para apagar este projeto.'];
        }

        $desenhoIds = $this->idsDesenhosProjeto($projetoId, $this->statusPermitidosTarefas(false));
        if ($desenhoIds === []) {
            return ['ok' => false, 'mensagem' => 'Projeto sem desenhos ativos para apagar.'];
        }

        $resultado = $this->aplicarApagarLote($desenhoIds, $usuarioId, $acessoAbas);
        if (empty($resultado['ok'])) {
            return $resultado;
        }

        (new \App\Models\Projeto())->update($projetoId, ['status' => 'apagado']);
        \Config\Database::connect()
            ->table('ordem')
            ->where('projeto_id', $projetoId)
            ->where('desenho_id IS NULL', null, false)
            ->where('status', 'ativo')
            ->update(['status' => 'desativado']);

        $this->registrarAlteracaoPainel(
            'projeto',
            $projetoId,
            'painel_tarefas.projeto.apagar',
            [
                [
                    'campo' => 'status',
                    'valor_antes' => 'ativo',
                    'valor_depois' => 'apagado',
                ],
            ],
            [
                'usuario_executor' => $usuarioId,
                'desenhos_afetados' => $desenhoIds,
            ]
        );

        return ['ok' => true, 'mensagem' => 'Projeto apagado com sucesso.'];
    }

    private function cancelarCorteProjeto(int $projetoId, int $usuarioId, array $acessoAbas): array
    {
        if (!$this->podeManipularProjetoAdm($projetoId, $acessoAbas)) {
            return ['ok' => false, 'mensagem' => 'Sem permissao para cancelar este projeto.'];
        }

        $statusCortando = $this->montarStatusConsulta(['cortando']);
        $desenhos = (new \App\Models\Projeto_desenho())
            ->select('desenhos.id, desenhos.corte_id, desenhos.status, desenhos.processos_id')
            ->join('desenhos', 'desenhos.id = projeto_desenho.desenho_id', 'inner')
            ->where('projeto_desenho.projeto_id', $projetoId)
            ->whereIn('desenhos.status', $statusCortando)
            ->findAll();

        if ($desenhos === []) {
            return ['ok' => false, 'mensagem' => 'Projeto sem desenhos em corte para cancelar.'];
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $desenhoIds = [];
        foreach ($desenhos as $desenho) {
            $desenhoId = (int) ($desenho['id'] ?? 0);
            if ($desenhoId <= 0) {
                continue;
            }

            $desenhoIds[] = $desenhoId;
            $corteId = (int) ($desenho['corte_id'] ?? 0);
            if ($corteId > 0) {
                (new \App\Models\Corte())->update($corteId, [
                    'status' => 'cancelado',
                    'usuario_id_fim' => $usuarioId,
                    'data_end' => date('Y-m-d H:i:s'),
                ]);
            }

            (new \App\Models\Desenhos())->update($desenhoId, ['status' => 'pendente']);
        }

        $auditou = $this->registrarAlteracaoPainel(
            'projeto',
            $projetoId,
            'painel_tarefas.projeto.cancelar_corte',
            [
                [
                    'campo' => 'status_desenhos',
                    'valor_antes' => 'cortando',
                    'valor_depois' => 'pendente',
                ],
            ],
            [
                'usuario_executor' => $usuarioId,
                'desenhos_afetados' => $desenhoIds,
            ]
        );

        if (!$auditou) {
            $db->transRollback();
            return ['ok' => false, 'mensagem' => 'Falha ao auditar cancelamento do projeto.'];
        }

        $db->transComplete();
        if (!$db->transStatus()) {
            return ['ok' => false, 'mensagem' => 'Falha ao cancelar corte do projeto.'];
        }

        return ['ok' => true, 'mensagem' => 'Corte do projeto cancelado.'];
    }

    private function solicitarRecolocarDesenho(int $desenhoId, int $usuarioId, array $acessoAbas): array
    {
        if (empty($acessoAbas['tarefas_concluidas'])) {
            return ['ok' => false, 'mensagem' => 'Sem permissao para solicitar recolocacao.'];
        }

        if ($desenhoId <= 0) {
            return ['ok' => false, 'mensagem' => 'Desenho invalido para recolocacao.'];
        }

        if ($usuarioId <= 0) {
            return ['ok' => false, 'mensagem' => 'Usuario invalido para solicitar recolocacao.'];
        }

        if (!$this->desenhoPertenceProcessoPermitido($desenhoId)) {
            return ['ok' => false, 'mensagem' => 'Sem permissao para este desenho.'];
        }

        $desenho = $this->carregarDesenhoAuditoria($desenhoId);
        if ($desenho === null) {
            return ['ok' => false, 'mensagem' => 'Desenho nao encontrado para solicitar recolocacao.'];
        }

        $historico = (new \App\Models\Recolocar_desenho())
            ->where('desenhos_id', $desenhoId)
            ->orderBy('id', 'ASC')
            ->findAll();

        if (empty($historico)) {
            $db = \Config\Database::connect();
            $db->transStart();

            $ok = (new \App\Models\Recolocar_desenho())->insert([
                'desenhos_id' => $desenhoId,
                'usuario_id_pedido' => $usuarioId,
                'status' => 'pendente',
            ]);

            if (!$ok) {
                $db->transRollback();
                return ['ok' => false, 'mensagem' => 'Falha ao registrar solicitacao de recolocacao.'];
            }

            $auditou = $this->registrarAlteracaoPainel(
                'recolocar_desenho',
                $desenhoId,
                'painel_tarefas.solicitar_recolocacao',
                [
                    [
                        'campo' => 'solicitacao_status',
                        'valor_antes' => 'inexistente',
                        'valor_depois' => 'pendente',
                    ],
                    [
                        'campo' => 'usuario_solicitante',
                        'valor_antes' => '',
                        'valor_depois' => (string) $usuarioId,
                    ],
                ],
                [
                    'desenho_nome' => $this->obterNomeExibicaoDesenho($desenho),
                    'processo_id' => (int) ($desenho['processos_id'] ?? 0),
                    'processo_nome' => $this->obterNomeProcessoPorId((int) ($desenho['processos_id'] ?? 0)),
                ]
            );

            if (!$auditou) {
                $db->transRollback();
                return ['ok' => false, 'mensagem' => 'Falha ao auditar a solicitacao de recolocacao.'];
            }

            $db->transComplete();
            if (!$db->transStatus()) {
                return ['ok' => false, 'mensagem' => 'Falha ao concluir a solicitacao de recolocacao.'];
            }

            return ['ok' => true, 'mensagem' => 'Solicitacao enviada para recolocar o desenho.'];
        }

        $ultimaSolicitacao = $historico[count($historico) - 1];
        if (strtolower((string) ($ultimaSolicitacao['status'] ?? '')) === 'pendente') {
            return ['ok' => false, 'mensagem' => 'Ja existe uma solicitacao pendente para este desenho.'];
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $dadosNovaSolicitacao = [
            'desenhos_id' => $desenhoId,
            'usuario_id_pedido' => $usuarioId,
            'status' => 'pendente',
            'recolocar_desenho_id_anterior' => (int) ($ultimaSolicitacao['id'] ?? 0),
            'quantidade' => (int) ($ultimaSolicitacao['quantidade'] ?? 0),
        ];

        $ok = (new \App\Models\Recolocar_desenho())->insert($dadosNovaSolicitacao);
        if (!$ok) {
            $db->transRollback();
            return ['ok' => false, 'mensagem' => 'Falha ao registrar solicitacao de recolocacao.'];
        }

        $auditou = $this->registrarAlteracaoPainel(
            'recolocar_desenho',
            $desenhoId,
            'painel_tarefas.solicitar_recolocacao',
            [
                [
                    'campo' => 'solicitacao_status',
                    'valor_antes' => (string) ($ultimaSolicitacao['status'] ?? ''),
                    'valor_depois' => 'pendente',
                ],
                [
                    'campo' => 'quantidade_recolocacoes',
                    'valor_antes' => (string) ($ultimaSolicitacao['quantidade'] ?? 0),
                    'valor_depois' => (string) ($ultimaSolicitacao['quantidade'] ?? 0),
                ],
                [
                    'campo' => 'solicitacao_anterior_id',
                    'valor_antes' => '',
                    'valor_depois' => (string) ($ultimaSolicitacao['id'] ?? 0),
                ],
            ],
            [
                'desenho_nome' => $this->obterNomeExibicaoDesenho($desenho),
                'processo_id' => (int) ($desenho['processos_id'] ?? 0),
                'processo_nome' => $this->obterNomeProcessoPorId((int) ($desenho['processos_id'] ?? 0)),
            ]
        );

        if (!$auditou) {
            $db->transRollback();
            return ['ok' => false, 'mensagem' => 'Falha ao auditar a solicitacao de recolocacao.'];
        }

        $db->transComplete();
        if (!$db->transStatus()) {
            return ['ok' => false, 'mensagem' => 'Falha ao concluir a solicitacao de recolocacao.'];
        }

        return ['ok' => true, 'mensagem' => 'Solicitacao enviada para recolocar o desenho.'];
    }

    private function listarSolicitacoesRecolocarPendentes(array $acessoAbas, array $permissoesUsuario): array
    {
        if (empty($acessoAbas['tarefas_concluidas'])) {
            return ['ok' => false, 'mensagem' => 'Sem permissao para listar solicitacoes de recolocacao.', 'itens' => []];
        }

        $processosPermitidos = $this->idsProcessosPermitidos();
        if (empty($processosPermitidos)) {
            return [
                'ok' => true,
                'mensagem' => '',
                'itens' => [],
                'podeGerenciar' => $this->usuarioPodeGerenciarRecolocar($permissoesUsuario),
            ];
        }

        $linhas = (new \App\Models\Recolocar_desenho())
            ->select("recolocar_desenho.id AS solicitacao_id,
                      recolocar_desenho.desenhos_id,
                      recolocar_desenho.quantidade,
                      recolocar_desenho.data_add AS data_solicitacao,
                      desenhos.processos_id,
                      desenhos.nome,
                      desenhos.prioridade_id,
                      prioridade.nome AS prioridade_nome,
                      prioridade.cor AS prioridade_cor,
                      usuarios_pedido.nome AS solicitante_nome,
                      usuarios_desenhista.nome AS desenhista_nome,
                      processos.nome AS processo_nome")
            ->join('desenhos', 'desenhos.id = recolocar_desenho.desenhos_id', 'inner')
            ->join('prioridade', 'prioridade.id = desenhos.prioridade_id', 'left')
            ->join('usuarios usuarios_pedido', 'usuarios_pedido.id = recolocar_desenho.usuario_id_pedido', 'left')
            ->join('usuarios usuarios_desenhista', 'usuarios_desenhista.id = desenhos.usuario_id_desenhista', 'left')
            ->join('processos', 'processos.id = desenhos.processos_id', 'left')
            ->where('recolocar_desenho.status', 'pendente')
            ->whereIn('desenhos.processos_id', $processosPermitidos)
            ->orderBy('recolocar_desenho.data_add', 'DESC')
            ->orderBy('recolocar_desenho.id', 'DESC')
            ->findAll();

        $itens = [];
        foreach ($linhas as $linha) {
            $dataSolicitacaoOriginal = (string) ($linha['data_solicitacao'] ?? '');
            $timestamp = strtotime($dataSolicitacaoOriginal);
            $dataSolicitacao = $timestamp ? date('d/m/Y H:i:s', $timestamp) : $dataSolicitacaoOriginal;

            $corPrioridade = $this->normalizarCorPrioridade($linha['prioridade_cor'] ?? '');
            $nomeArquivo = $this->decodificarValor($linha['nome'] ?? '');
            if ($nomeArquivo !== '') {
                $nomeArquivo = Ferramentas::remove_id_file($nomeArquivo);
            }

            $itens[] = [
                'solicitacao_id' => (int) ($linha['solicitacao_id'] ?? 0),
                'desenho_id' => (int) ($linha['desenhos_id'] ?? 0),
                'processo_id' => (int) ($linha['processos_id'] ?? 0),
                'processo_nome' => $this->decodificarValor($linha['processo_nome'] ?? ''),
                'prioridade_id' => (int) ($linha['prioridade_id'] ?? 0),
                'prioridade_nome' => $this->decodificarValor($linha['prioridade_nome'] ?? ''),
                'prioridade_cor' => $corPrioridade,
                'prioridade_texto' => $this->corTextoParaFundo($corPrioridade),
                'desenhista_nome' => $this->decodificarValor($linha['desenhista_nome'] ?? ''),
                'solicitante_nome' => $this->decodificarValor($linha['solicitante_nome'] ?? ''),
                'nome_arquivo' => $nomeArquivo,
                'quantidade' => (int) ($linha['quantidade'] ?? 0),
                'data_solicitacao' => $dataSolicitacao,
            ];
        }

        return [
            'ok' => true,
            'mensagem' => '',
            'itens' => $itens,
            'podeGerenciar' => $this->usuarioPodeGerenciarRecolocar($permissoesUsuario),
        ];
    }

    private function decidirSolicitacaoRecolocar(
        int $solicitacaoId,
        string $decisao,
        array $acessoAbas,
        array $permissoesUsuario,
        int $usuarioId
    ): array {
        if (empty($acessoAbas['tarefas_concluidas'])) {
            return ['ok' => false, 'mensagem' => 'Sem permissao para gerenciar solicitacoes de recolocacao.'];
        }

        if (!$this->usuarioPodeGerenciarRecolocar($permissoesUsuario)) {
            return ['ok' => false, 'mensagem' => 'Somente usuario com permissao ADM pode aprovar ou negar solicitacao.'];
        }

        if ($usuarioId <= 0) {
            return ['ok' => false, 'mensagem' => 'Usuario invalido para confirmar solicitacao.'];
        }

        if ($solicitacaoId <= 0) {
            return ['ok' => false, 'mensagem' => 'Solicitacao invalida.'];
        }

        $decisao = strtolower(trim($decisao));
        if (!in_array($decisao, ['aprovado', 'negado'], true)) {
            return ['ok' => false, 'mensagem' => 'Decisao invalida para solicitacao.'];
        }

        $recolocarModel = new \App\Models\Recolocar_desenho();
        $solicitacao = $recolocarModel
            ->select('id, desenhos_id, status, quantidade')
            ->where('id', $solicitacaoId)
            ->first();

        if (!is_array($solicitacao) || empty($solicitacao['id'])) {
            return ['ok' => false, 'mensagem' => 'Solicitacao nao encontrada.'];
        }

        if (strtolower((string) ($solicitacao['status'] ?? '')) !== 'pendente') {
            return ['ok' => false, 'mensagem' => 'Solicitacao ja foi processada anteriormente.'];
        }

        $desenhoId = (int) ($solicitacao['desenhos_id'] ?? 0);
        if ($desenhoId <= 0) {
            return ['ok' => false, 'mensagem' => 'Desenho da solicitacao e invalido.'];
        }

        if (!$this->desenhoPertenceProcessoPermitido($desenhoId)) {
            return ['ok' => false, 'mensagem' => 'Sem permissao para este desenho.'];
        }

        $desenho = $this->carregarDesenhoAuditoria($desenhoId);
        if ($desenho === null) {
            return ['ok' => false, 'mensagem' => 'Desenho da solicitacao nao foi encontrado.'];
        }

        $db = \Config\Database::connect();
        $db->transStart();

        if ($decisao === 'aprovado') {
            try {
                $resultadoRecolocar = (new Ferramentas())->re_colcoar_desenho($desenhoId);
            } catch (\Throwable $e) {
                $db->transRollback();
                return ['ok' => false, 'mensagem' => 'Erro ao recolocar o desenho: ' . $e->getMessage()];
            }

            if (is_array($resultadoRecolocar) && array_key_exists('ok', $resultadoRecolocar) && empty($resultadoRecolocar['ok'])) {
                $db->transRollback();
                return ['ok' => false, 'mensagem' => $this->montarMensagemErroRecolocar($resultadoRecolocar)];
            }
            if ($resultadoRecolocar === null || $resultadoRecolocar === false) {
                $db->transRollback();
                return ['ok' => false, 'mensagem' => 'Falha ao recolocar o desenho.'];
            }
        }

        $dadosAtualizacao = [
            'usuario_id_confirmado' => $usuarioId,
            'status' => $decisao,
            'data_end' => date('Y-m-d H:i:s'),
        ];
        if ($decisao === 'aprovado') {
            $dadosAtualizacao['quantidade'] = (int) ($solicitacao['quantidade'] ?? 0) + 1;
        }

        $ok = $recolocarModel->update($solicitacaoId, $dadosAtualizacao);
        if (!$ok) {
            $db->transRollback();
            return ['ok' => false, 'mensagem' => 'Falha ao salvar a decisao da solicitacao.'];
        }

        $auditou = $this->registrarAlteracaoPainel(
            'recolocar_desenho',
            $solicitacaoId,
            'painel_tarefas.decidir_recolocacao',
            [
                [
                    'campo' => 'solicitacao_status',
                    'valor_antes' => (string) ($solicitacao['status'] ?? ''),
                    'valor_depois' => $decisao,
                ],
                [
                    'campo' => 'quantidade_recolocacoes',
                    'valor_antes' => (string) ($solicitacao['quantidade'] ?? 0),
                    'valor_depois' => (string) ($dadosAtualizacao['quantidade'] ?? $solicitacao['quantidade'] ?? 0),
                ],
                [
                    'campo' => 'usuario_confirmador',
                    'valor_antes' => '',
                    'valor_depois' => (string) $usuarioId,
                ],
            ],
            [
                'desenho_id' => $desenhoId,
                'desenho_nome' => $this->obterNomeExibicaoDesenho($desenho),
                'processo_id' => (int) ($desenho['processos_id'] ?? 0),
                'processo_nome' => $this->obterNomeProcessoPorId((int) ($desenho['processos_id'] ?? 0)),
            ]
        );

        if (!$auditou) {
            $db->transRollback();
            return ['ok' => false, 'mensagem' => 'Falha ao auditar a decisao da solicitacao.'];
        }

        $db->transComplete();
        if (!$db->transStatus()) {
            return ['ok' => false, 'mensagem' => 'Falha ao concluir a decisao da solicitacao.'];
        }

        if ($decisao === 'aprovado') {
            return ['ok' => true, 'mensagem' => 'Solicitacao aprovada e desenho recolocado na lista de tarefas.'];
        }

        return ['ok' => true, 'mensagem' => 'Solicitacao negada com sucesso.'];
    }

    private function montarMensagemErroRecolocar(array $resultado): string
    {
        $mensagem = trim((string) ($resultado['mensagem'] ?? $resultado['msg'] ?? ''));

        $detalhes = [];
        foreach ([1, 2, 3, 4] as $indice) {
            $valor = trim((string) ($resultado[$indice] ?? ''));
            if ($valor !== '') {
                $detalhes[] = $valor;
            }
        }
        $original = trim((string) ($resultado['original'] ?? ''));
        if ($original !== '') {
            $detalhes[] = 'Original: ' . $original;
        }

        if (!empty($detalhes)) {
            return 'Arquivo do desenho corrompido ou indisponivel para recolocar.';
        }

        if ($mensagem !== '') {
            return $mensagem;
        }

        return 'Falha ao recolocar o desenho.';
    }

    private function autorizarConclusaoDesenho(
        int $desenhoId,
        array $acessoAbas,
        array $permissoesUsuarioSessao,
        int $usuarioSessaoId,
        string $autorizadorNome = '',
        string $autorizadorSenha = ''
    ): array {
        if (empty($acessoAbas['tarefas_concluidas'])) {
            return ['ok' => false, 'mensagem' => 'Sem permissao para autorizar conclusao.'];
        }

        if ($desenhoId <= 0) {
            return ['ok' => false, 'mensagem' => 'Desenho invalido para autorizacao.'];
        }

        if (!$this->desenhoPertenceProcessoPermitido($desenhoId)) {
            return ['ok' => false, 'mensagem' => 'Sem permissao para este desenho.'];
        }

        $desenho = (new \App\Models\Desenhos())
            ->select('id, status')
            ->where('id', $desenhoId)
            ->first();
        if (!is_array($desenho) || empty($desenho['id'])) {
            return ['ok' => false, 'mensagem' => 'Desenho nao encontrado para autorizacao.'];
        }

        $statusAtual = $this->normalizarStatusTexto($this->decodificarValor((string) ($desenho['status'] ?? '')));
        $statusConcluidos = array_map(fn ($item) => $this->normalizarStatusTexto((string) $item), $this->statusConcluidosPainel());
        if (!in_array($statusAtual, $statusConcluidos, true)) {
            return ['ok' => false, 'mensagem' => 'A tarefa ainda nao esta na fila de concluidas.'];
        }

        $statusQuePrecisamAutorizacao = ['pronto', 'concluido', 'concluida'];
        if (!in_array($statusAtual, $statusQuePrecisamAutorizacao, true)) {
            return ['ok' => true, 'mensagem' => 'Tarefa ja possui conclusao autorizada.'];
        }

        $autorizadorId = 0;
        if ($this->usuarioPodeAutorizarConclusao($permissoesUsuarioSessao) && $usuarioSessaoId > 0 && trim($autorizadorNome) === '' && trim($autorizadorSenha) === '') {
            $autorizadorId = $usuarioSessaoId;
        } else {
            $autorizador = $this->validarCredenciaisAutorizador($autorizadorNome, $autorizadorSenha);
            if (!is_array($autorizador) || empty($autorizador['id'])) {
                return ['ok' => false, 'mensagem' => 'Credenciais de autorizacao invalidas ou sem permissao.'];
            }
            $autorizadorId = (int) ($autorizador['id'] ?? 0);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $atualizou = (new \App\Models\Desenhos())->update($desenhoId, ['status' => 'cortado']);
        if (!$atualizou) {
            $db->transRollback();
            return ['ok' => false, 'mensagem' => 'Falha ao registrar a autorizacao de conclusao.'];
        }

        $auditou = $this->registrarAlteracaoPainel(
            'desenho',
            $desenhoId,
            'painel_tarefas.autorizar_conclusao',
            [
                [
                    'campo' => 'status',
                    'valor_antes' => $statusAtual,
                    'valor_depois' => 'cortado',
                ],
                [
                    'campo' => 'autorizador_id',
                    'valor_antes' => '',
                    'valor_depois' => (string) $autorizadorId,
                ],
            ],
            [
                'autorizador_id' => $autorizadorId,
            ]
        );

        if (!$auditou) {
            $db->transRollback();
            return ['ok' => false, 'mensagem' => 'Falha ao auditar a autorizacao de conclusao.'];
        }

        $db->transComplete();
        if (!$db->transStatus()) {
            return ['ok' => false, 'mensagem' => 'Falha ao concluir a autorizacao de conclusao.'];
        }

        return [
            'ok' => true,
            'mensagem' => 'Conclusao autorizada com sucesso.',
            'autorizador_id' => $autorizadorId,
        ];
    }

    private function localizarArquivoDesenho(array $desenho): array
    {
        $diretorio = (string) ($desenho['diretorio'] ?? '');
        $nome = (string) ($desenho['nome'] ?? '');
        if ($diretorio === '') {
            return ['ok' => false, 'mensagem' => 'Diretorio do desenho invalido.'];
        }

        $caminho = dirname($diretorio) . DIRECTORY_SEPARATOR;
        $nomeArquivoDiretorio = basename($diretorio);
        $candidatos = [];
        if ($nome !== '') {
            $candidatos[] = $nome;
        }
        if ($nomeArquivoDiretorio !== '') {
            $candidatos[] = $nomeArquivoDiretorio;
        }

        if ($nomeArquivoDiretorio !== '' && strpos($nomeArquivoDiretorio, '.') === false) {
            $posUnderscore = strrpos($nomeArquivoDiretorio, '_');
            if ($posUnderscore !== false) {
                $candidatos[] = substr_replace($nomeArquivoDiretorio, '.', $posUnderscore + 1, 0);
            }
        }

        if ($nome !== '' && strpos($nome, '.') === false) {
            $posUnderscore = strrpos($nome, '_');
            if ($posUnderscore !== false) {
                $candidatos[] = substr_replace($nome, '.', $posUnderscore + 1, 0);
            }
        }

        $candidatos = array_values(array_unique(array_filter($candidatos, static fn ($item) => (string) $item !== '')));
        foreach ($candidatos as $candidato) {
            $arquivo = $caminho . $candidato;
            if (file_exists($arquivo)) {
                return [
                    'ok' => true,
                    'arquivo' => str_replace(['\\', '//'], '/', $arquivo),
                ];
            }
        }

        return ['ok' => false, 'mensagem' => 'Arquivo do desenho nao encontrado no disco.'];
    }

    private function respostaVisualizacaoDesenho(int $desenhoId, array $acessoAbas): array
    {
        if ($desenhoId <= 0) {
            return ['status' => false, 'msg' => 'Desenho invalido para visualizacao.'];
        }

        if (empty($acessoAbas['tarefas_concluidas']) && empty($acessoAbas['lista_tarefas']) && empty($acessoAbas['lista_tarefas_adm']) && empty($acessoAbas['meus_desenhos'])) {
            return ['status' => false, 'msg' => 'Sem permissao para visualizar desenho.'];
        }

        if (!$this->desenhoPertenceProcessoPermitido($desenhoId)) {
            return ['status' => false, 'msg' => 'Sem permissao para o processo deste desenho.'];
        }

        $desenho = (new \App\Models\Desenhos())
            ->select('id, nome, diretorio')
            ->where('id', $desenhoId)
            ->first();
        if (!is_array($desenho) || empty($desenho['id'])) {
            return ['status' => false, 'msg' => 'Desenho nao encontrado.'];
        }

        $arquivoLocalizado = $this->localizarArquivoDesenho($desenho);
        if (empty($arquivoLocalizado['ok'])) {
            return ['status' => false, 'msg' => (string) ($arquivoLocalizado['mensagem'] ?? 'Arquivo nao encontrado.')];
        }

        $arquivo = (string) ($arquivoLocalizado['arquivo'] ?? '');
        if ($arquivo === '' || !file_exists($arquivo)) {
            return ['status' => false, 'msg' => 'Arquivo nao encontrado no caminho final.'];
        }

        $conteudo = @file_get_contents($arquivo);
        if ($conteudo === false) {
            return ['status' => false, 'msg' => 'Falha ao ler o arquivo para visualizacao.'];
        }

        $nomeArquivo = $this->decodificarValor($desenho['nome'] ?? '');
        if ($nomeArquivo === '') {
            $nomeArquivo = basename($arquivo);
        }
        $nomeArquivo = Ferramentas::remove_id_file($nomeArquivo);

        $ext = strtolower((string) pathinfo($arquivo, PATHINFO_EXTENSION));
        $campoConteudo = 'arquivo';
        if ($ext === 'dxf') {
            $campoConteudo = 'dxf';
        } elseif ($ext === 'stl' || $ext === 'slt') {
            $campoConteudo = 'slt';
        }

        return [
            'status' => true,
            'nome' => $nomeArquivo,
            'ext' => $ext,
            $campoConteudo => base64_encode($conteudo),
        ];
    }

    private function moverOrdemDesenho(int $desenhoId, string $direcao): array
    {
        $desenho = (new \App\Models\Desenhos())
            ->select('id, prioridade_id, processos_id')
            ->where('id', $desenhoId)
            ->first();

        if (!is_array($desenho) || empty($desenho['id'])) {
            return ['ok' => false, 'mensagem' => 'Desenho nao encontrado.'];
        }

        $prioridadeId = (int) ($desenho['prioridade_id'] ?? 0);
        $processoId = (int) ($desenho['processos_id'] ?? 0);
        if ($prioridadeId <= 0 || $processoId <= 0) {
            return ['ok' => false, 'mensagem' => 'Dados de prioridade/processo invalidos para ordenar.'];
        }

        Ferramentas::garantirOrdemAtivaDesenho($desenhoId, $processoId, $prioridadeId);

        $ordemModel = new \App\Models\Ordem();
        $ordemAtual = $ordemModel
            ->select('id, ordem')
            ->where('desenho_id', $desenhoId)
            ->where('prioridade_id', $prioridadeId)
            ->where('processos_id', $processoId)
            ->where('status', 'ativo')
            ->first();

        if (!is_array($ordemAtual) || empty($ordemAtual['id'])) {
            return ['ok' => false, 'mensagem' => 'Ordem ativa nao encontrada para este desenho.'];
        }

        $ordemAtualValor = (int) ($ordemAtual['ordem'] ?? 0);
        if ($ordemAtualValor <= 0) {
            return ['ok' => false, 'mensagem' => 'Ordem atual invalida para mover.'];
        }

        $direcao = strtolower(trim($direcao));
        if (!in_array($direcao, ['up', 'down'], true)) {
            return ['ok' => false, 'mensagem' => 'Direcao de ordem invalida.'];
        }

        $vizinhoBuilder = $ordemModel
            ->select('ordem')
            ->where('processos_id', $processoId)
            ->where('prioridade_id', $prioridadeId)
            ->where('status', 'ativo')
            ->where('desenho_id IS NOT NULL', null, false)
            ->where('desenho_id !=', $desenhoId);
        if ($direcao === 'up') {
            $vizinhoBuilder->where('ordem <', $ordemAtualValor)->orderBy('ordem', 'DESC');
        } else {
            $vizinhoBuilder->where('ordem >', $ordemAtualValor)->orderBy('ordem', 'ASC');
        }
        $vizinho = $vizinhoBuilder->first();

        $novaOrdem = (int) ($vizinho['ordem'] ?? 0);
        if ($novaOrdem <= 0 || $novaOrdem === $ordemAtualValor) {
            return ['ok' => false, 'mensagem' => 'Desenho ja esta no limite da ordem.'];
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            Ferramentas::reordenarPorPrioridade($desenhoId, $novaOrdem, $prioridadeId, $processoId);
        } catch (\Throwable $e) {
            $db->transRollback();
            return ['ok' => false, 'mensagem' => 'Falha ao reordenar: ' . $e->getMessage()];
        }

        $ordemAtualizada = $ordemModel
            ->select('ordem')
            ->where('desenho_id', $desenhoId)
            ->where('prioridade_id', $prioridadeId)
            ->where('processos_id', $processoId)
            ->where('status', 'ativo')
            ->first();
        $ordemDepois = (int) ($ordemAtualizada['ordem'] ?? $novaOrdem);

        $auditou = $this->registrarAlteracaoPainel(
            'desenho',
            $desenhoId,
            'painel_tarefas.mover_ordem',
            [
                [
                    'campo' => 'ordem',
                    'valor_antes' => (string) $ordemAtualValor,
                    'valor_depois' => (string) $ordemDepois,
                ],
                [
                    'campo' => 'direcao',
                    'valor_antes' => '',
                    'valor_depois' => $direcao,
                ],
            ],
            [
                'prioridade_id' => $prioridadeId,
                'processo_id' => $processoId,
                'processo_nome' => $this->obterNomeProcessoPorId($processoId),
            ]
        );

        if (!$auditou) {
            $db->transRollback();
            return ['ok' => false, 'mensagem' => 'Falha ao auditar a alteracao de ordem.'];
        }

        $db->transComplete();
        if (!$db->transStatus()) {
            return ['ok' => false, 'mensagem' => 'Falha ao concluir a alteracao de ordem.'];
        }

        return ['ok' => true, 'mensagem' => 'Ordem atualizada com sucesso.'];
    }

    private function normalizarIdsLote($idsRaw): array
    {
        if (is_string($idsRaw)) {
            $idsRaw = trim($idsRaw);
            if ($idsRaw === '') {
                return [];
            }

            if (substr($idsRaw, 0, 1) === '[' && substr($idsRaw, -1) === ']') {
                $decodificado = json_decode($idsRaw, true);
                if (is_array($decodificado)) {
                    $idsRaw = $decodificado;
                }
            }

            if (!is_array($idsRaw)) {
                $idsRaw = array_map('trim', explode(',', $idsRaw));
            }
        }

        if (!is_array($idsRaw)) {
            return [];
        }

        $ids = [];
        foreach ($idsRaw as $valor) {
            $id = (int) $valor;
            if ($id > 0) {
                $ids[$id] = $id;
            }
        }

        return array_values($ids);
    }

    private function aplicarMudancaLote(array $desenhoIds, int $prioridadeId, int $ordemInicial, array $acessoAbas): array
    {
        if (empty($desenhoIds)) {
            return ['ok' => false, 'mensagem' => 'Selecione ao menos um desenho.'];
        }

        if ($prioridadeId <= 0) {
            return ['ok' => false, 'mensagem' => 'Prioridade invalida para aplicacao em lote.'];
        }

        if ($ordemInicial <= 0) {
            return ['ok' => false, 'mensagem' => 'Ordem inicial invalida.'];
        }

        $prioridadeExiste = (new \App\Models\Prioridade())
            ->where('id', $prioridadeId)
            ->where('status', 'ativo')
            ->first();
        if (!is_array($prioridadeExiste)) {
            return ['ok' => false, 'mensagem' => 'Prioridade selecionada nao encontrada.'];
        }

        $desenhosModel = new \App\Models\Desenhos();
        $desenhosMap = [];
        foreach ($desenhoIds as $desenhoId) {
            if (!$this->podeManipularDesenhoAdm($desenhoId, $acessoAbas)) {
                return ['ok' => false, 'mensagem' => 'Sem permissao para alterar um ou mais desenhos selecionados.'];
            }

            $desenho = $desenhosModel
                ->select('id, prioridade_id, processos_id')
                ->where('id', $desenhoId)
                ->first();
            if (!is_array($desenho) || empty($desenho['id'])) {
                return ['ok' => false, 'mensagem' => 'Um dos desenhos selecionados nao foi encontrado.'];
            }

            $desenhosMap[$desenhoId] = $desenho;
        }

        $processoBase = (int) ($desenhosMap[$desenhoIds[0]]['processos_id'] ?? 0);
        if ($processoBase <= 0) {
            return ['ok' => false, 'mensagem' => 'Processo invalido para os desenhos selecionados.'];
        }

        foreach ($desenhosMap as $desenho) {
            if ((int) ($desenho['processos_id'] ?? 0) !== $processoBase) {
                return ['ok' => false, 'mensagem' => 'Selecione apenas desenhos do mesmo processo para atualizar em lote.'];
            }
        }

        try {
            Ferramentas::ordenarOrdems($processoBase);
        } catch (\Throwable $e) {
            return ['ok' => false, 'mensagem' => 'Falha ao preparar as ordens do processo: ' . $e->getMessage()];
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $ordemAtual = $ordemInicial;
        foreach ($desenhoIds as $desenhoId) {
            $desenho = $desenhosMap[$desenhoId];
            $prioridadeAntes = (int) ($desenho['prioridade_id'] ?? 0);

            if ($prioridadeAntes !== $prioridadeId) {
                $desenhosModel->update($desenhoId, ['prioridade_id' => $prioridadeId]);
            }

            try {
                Ferramentas::reordenarPorPrioridade($desenhoId, $ordemAtual, $prioridadeId, $processoBase);
            } catch (\Throwable $e) {
                $db->transRollback();
                return ['ok' => false, 'mensagem' => 'Falha ao atualizar lote: ' . $e->getMessage()];
            }

            $ordemDepois = (new \App\Models\Ordem())
                ->select('ordem')
                ->where('desenho_id', $desenhoId)
                ->where('processos_id', $processoBase)
                ->where('prioridade_id', $prioridadeId)
                ->where('status', 'ativo')
                ->first();

            $auditou = $this->registrarAlteracaoPainel(
                'desenho',
                $desenhoId,
                'painel_tarefas.mudar_lote',
                [
                    [
                        'campo' => 'prioridade_id',
                        'valor_antes' => (string) $prioridadeAntes,
                        'valor_depois' => (string) $prioridadeId,
                    ],
                    [
                        'campo' => 'ordem',
                        'valor_antes' => '',
                        'valor_depois' => (string) ($ordemDepois['ordem'] ?? $ordemAtual),
                    ],
                ],
                [
                    'processo_id' => $processoBase,
                    'processo_nome' => $this->obterNomeProcessoPorId($processoBase),
                    'lote_ordem_solicitada' => $ordemInicial,
                ]
            );

            if (!$auditou) {
                $db->transRollback();
                return ['ok' => false, 'mensagem' => 'Falha ao auditar a alteracao em lote.'];
            }

            $ordemAtual++;
        }

        $db->transComplete();
        if (!$db->transStatus()) {
            return ['ok' => false, 'mensagem' => 'Falha ao concluir a alteracao em lote.'];
        }

        return [
            'ok' => true,
            'mensagem' => 'Atualizacao em lote aplicada para ' . count($desenhoIds) . ' desenho(s).',
        ];
    }

    private function aplicarMudancaProjetoLote(array $projetoIds, int $prioridadeId, int $ordemInicial, array $acessoAbas): array
    {
        if (empty($projetoIds)) {
            return ['ok' => false, 'mensagem' => 'Selecione ao menos um projeto.'];
        }

        if ($prioridadeId <= 0) {
            return ['ok' => false, 'mensagem' => 'Prioridade invalida para aplicacao em lote.'];
        }

        if ($ordemInicial <= 0) {
            return ['ok' => false, 'mensagem' => 'Ordem inicial invalida.'];
        }

        $processoBase = 0;
        foreach ($projetoIds as $projetoId) {
            if (!$this->podeManipularProjetoAdm($projetoId, $acessoAbas)) {
                return ['ok' => false, 'mensagem' => 'Sem permissao para alterar um ou mais projetos selecionados.'];
            }

            $resumo = $this->carregarResumoProjetoAdm($projetoId);
            if (!is_array($resumo)) {
                return ['ok' => false, 'mensagem' => 'Um dos projetos selecionados nao foi encontrado.'];
            }

            $processoId = (int) ($resumo['processo_id'] ?? 0);
            if ($processoBase <= 0) {
                $processoBase = $processoId;
            } elseif ($processoId !== $processoBase) {
                return ['ok' => false, 'mensagem' => 'Selecione apenas projetos do mesmo processo para atualizar em lote.'];
            }
        }

        $ordemAtual = $ordemInicial;
        foreach ($projetoIds as $projetoId) {
            $resultado = $this->aplicarMudancaPrioridadeProjeto($projetoId, $prioridadeId, $ordemAtual, $acessoAbas);
            if (empty($resultado['ok'])) {
                return $resultado;
            }

            $ordemAtual++;
        }

        return [
            'ok' => true,
            'mensagem' => 'Atualizacao em lote aplicada para ' . count($projetoIds) . ' projeto(s).',
        ];
    }

    private function aplicarApagarProjetoLote(array $projetoIds, int $usuarioId, array $acessoAbas): array
    {
        if (empty($projetoIds)) {
            return ['ok' => false, 'mensagem' => 'Selecione ao menos um projeto para apagar.'];
        }

        foreach ($projetoIds as $projetoId) {
            $resultado = $this->apagarProjetoPainel($projetoId, $usuarioId, $acessoAbas);
            if (empty($resultado['ok'])) {
                return $resultado;
            }
        }

        return [
            'ok' => true,
            'mensagem' => count($projetoIds) . ' projeto(s) apagado(s) com sucesso.',
        ];
    }

    private function aplicarApagarLote(array $desenhoIds, int $usuarioId, array $acessoAbas): array
    {
        if (empty($desenhoIds)) {
            return ['ok' => false, 'mensagem' => 'Selecione ao menos um desenho para apagar.'];
        }

        $desenhosPlanejados = [];
        $processoBase = 0;
        foreach ($desenhoIds as $desenhoId) {
            if (!$this->podeManipularDesenhoAdm($desenhoId, $acessoAbas)) {
                return ['ok' => false, 'mensagem' => 'Sem permissao para apagar um ou mais desenhos selecionados.'];
            }

            $desenho = $this->carregarDesenhoAuditoria($desenhoId);
            if ($desenho === null) {
                return ['ok' => false, 'mensagem' => 'Um dos desenhos selecionados nao foi encontrado.'];
            }

            $statusAtual = $this->decodificarValor($desenho['status'] ?? '');
            $statusAtual = $statusAtual !== '' ? $statusAtual : (string) ($desenho['status'] ?? '');
            $statusNormalizado = $this->normalizarStatusTexto($statusAtual);
            if ($statusNormalizado !== 'pendente') {
                return ['ok' => false, 'mensagem' => 'A exclusao em lote permite apenas desenhos pendentes.'];
            }

            $processoId = (int) ($desenho['processos_id'] ?? 0);
            if ($processoId <= 0) {
                return ['ok' => false, 'mensagem' => 'Processo invalido para um dos desenhos selecionados.'];
            }

            if ($processoBase <= 0) {
                $processoBase = $processoId;
            } elseif ($processoBase !== $processoId) {
                return ['ok' => false, 'mensagem' => 'Selecione apenas desenhos do mesmo processo para apagar em lote.'];
            }

            $caminhoOriginal = $this->obterCaminhoArquivoDesenho($desenho);
            $arquivoExiste = $caminhoOriginal !== '' && file_exists($caminhoOriginal);
            $planejado = [
                'desenho' => $desenho,
                'caminho_original' => $caminhoOriginal,
                'arquivo_existe' => $arquivoExiste,
                'processo_id' => $processoId,
                'processo_nome' => $this->obterNomeProcessoPorId($processoId),
                'desenho_nome' => $this->obterNomeExibicaoDesenho($desenho),
            ];

            if ($arquivoExiste) {
                $destino = $this->prepararDestinoLixeira($caminhoOriginal);
                if (empty($destino['ok'])) {
                    return ['ok' => false, 'mensagem' => (string) ($destino['mensagem'] ?? 'Falha ao preparar a lixeira para o lote.')];
                }

                $problema = Ferramentas::criet_diretorio((string) $destino['diretorio_lixeira']);
                if (!empty($problema)) {
                    return ['ok' => false, 'mensagem' => 'Falha ao criar diretorio da lixeira para o lote.'];
                }

                $planejado['destino'] = $destino;
            }

            $desenhosPlanejados[] = $planejado;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $desenhosModel = new \App\Models\Desenhos();
        $lixoModel = new \App\Models\Lixo_desenhos();
        $movimentacoes = [];

        foreach ($desenhosPlanejados as $planejado) {
            $desenho = $planejado['desenho'];
            $desenhoId = (int) ($desenho['id'] ?? 0);
            $caminhoOriginal = (string) ($planejado['caminho_original'] ?? '');
            $arquivoExiste = !empty($planejado['arquivo_existe']);
            $destino = is_array($planejado['destino'] ?? null) ? $planejado['destino'] : [];

            if ($arquivoExiste) {
                $novoCaminho = (string) ($destino['novo_caminho'] ?? '');
                if ($novoCaminho === '' || !@rename($caminhoOriginal, $novoCaminho)) {
                    $db->transRollback();
                    $this->restaurarArquivosMovidosLote($movimentacoes);
                    return ['ok' => false, 'mensagem' => 'Falha ao mover um dos desenhos para a lixeira durante a exclusao em lote.'];
                }

                $movimentacoes[] = [
                    'origem' => $caminhoOriginal,
                    'destino' => $novoCaminho,
                ];

                $inseriuLixo = $lixoModel->insert([
                    'desenho_id' => $desenhoId,
                    'usuario_id' => $usuarioId,
                    'diretorio' => (string) ($destino['diretorio_lixeira'] ?? ''),
                    'nome' => (string) ($destino['novo_nome'] ?? ''),
                ]);

                if (!$inseriuLixo) {
                    $db->transRollback();
                    $this->restaurarArquivosMovidosLote($movimentacoes);
                    return ['ok' => false, 'mensagem' => 'Falha ao registrar um dos desenhos na lixeira durante a exclusao em lote.'];
                }
            }

            $atualizou = $desenhosModel->update($desenhoId, ['status' => 'apagado']);
            if (!$atualizou) {
                $db->transRollback();
                $this->restaurarArquivosMovidosLote($movimentacoes);
                return ['ok' => false, 'mensagem' => 'Falha ao atualizar o status de um dos desenhos apagados em lote.'];
            }

            if ($arquivoExiste) {
                $detalhes = [
                    [
                        'campo' => 'status',
                        'valor_antes' => $this->decodificarValor($desenho['status'] ?? ''),
                        'valor_depois' => 'apagado',
                    ],
                    [
                        'campo' => 'arquivo_origem',
                        'valor_antes' => $caminhoOriginal,
                        'valor_depois' => (string) ($destino['novo_caminho'] ?? ''),
                    ],
                    [
                        'campo' => 'lixeira_diretorio',
                        'valor_antes' => '',
                        'valor_depois' => (string) ($destino['diretorio_lixeira'] ?? ''),
                    ],
                    [
                        'campo' => 'lixeira_nome',
                        'valor_antes' => (string) ($destino['nome_original'] ?? ''),
                        'valor_depois' => (string) ($destino['novo_nome'] ?? ''),
                    ],
                ];

                $contexto = [
                    'desenho_nome' => (string) ($planejado['desenho_nome'] ?? ''),
                    'processo_id' => (int) ($planejado['processo_id'] ?? 0),
                    'processo_nome' => (string) ($planejado['processo_nome'] ?? ''),
                    'usuario_executor' => $usuarioId,
                    'novo_caminho_lixeira' => (string) ($destino['novo_caminho'] ?? ''),
                    'modo_lote' => true,
                ];
            } else {
                $mensagemAusencia = $caminhoOriginal === ''
                    ? 'Caminho do arquivo indisponivel no momento da exclusao.'
                    : 'Arquivo nao encontrado no momento da exclusao.';

                $detalhes = [
                    [
                        'campo' => 'status',
                        'valor_antes' => $this->decodificarValor($desenho['status'] ?? ''),
                        'valor_depois' => 'apagado',
                    ],
                    [
                        'campo' => 'arquivo_origem',
                        'valor_antes' => $caminhoOriginal,
                        'valor_depois' => '[arquivo ausente]',
                    ],
                    [
                        'campo' => 'observacao_exclusao',
                        'valor_antes' => '',
                        'valor_depois' => $mensagemAusencia,
                    ],
                ];

                $contexto = [
                    'desenho_nome' => (string) ($planejado['desenho_nome'] ?? ''),
                    'processo_id' => (int) ($planejado['processo_id'] ?? 0),
                    'processo_nome' => (string) ($planejado['processo_nome'] ?? ''),
                    'usuario_executor' => $usuarioId,
                    'arquivo_ausente' => true,
                    'modo_lote' => true,
                ];
            }

            $auditou = $this->registrarAlteracaoPainel(
                'desenho',
                $desenhoId,
                'painel_tarefas.apagar_lote',
                $detalhes,
                $contexto
            );

            if (!$auditou) {
                $db->transRollback();
                $this->restaurarArquivosMovidosLote($movimentacoes);
                return ['ok' => false, 'mensagem' => 'Falha ao auditar a exclusao em lote.'];
            }
        }

        $db->transComplete();
        if (!$db->transStatus()) {
            $this->restaurarArquivosMovidosLote($movimentacoes);
            return ['ok' => false, 'mensagem' => 'Falha ao concluir a exclusao em lote.'];
        }

        return [
            'ok' => true,
            'mensagem' => count($desenhoIds) . ' desenho(s) apagado(s) em lote com sucesso.',
        ];
    }

    private function dadosModalLote(int $processoId, array $acessoAbas): array
    {
        if (empty($acessoAbas['lista_tarefas_adm'])) {
            return ['ok' => false, 'mensagem' => 'Sem permissao para usar alteracao em lote.'];
        }

        if ($processoId <= 0) {
            return ['ok' => false, 'mensagem' => 'Processo invalido para carregar dados do lote.'];
        }

        $processosPermitidos = $this->buscarProcessosDisponiveis();
        $processosMap = array_fill_keys(array_map(static fn ($p) => (int) ($p['id'] ?? 0), $processosPermitidos), true);
        if (!isset($processosMap[$processoId])) {
            return ['ok' => false, 'mensagem' => 'Sem permissao para o processo selecionado.'];
        }

        $inicioTotal = microtime(true);
        $queriesInicio = $this->contarQueriesRegistradasPainelTarefas();
        $tarefasModel = new \App\Models\TarefasFilaModel();
        $processo = $tarefasModel->processoPorId($processoId);
        if (!is_array($processo)) {
            return ['ok' => false, 'mensagem' => 'Processo nao encontrado para carregar dados do lote.'];
        }

        $itemTipo = $this->processoUsaDescricaoProjeto($processoId) ? 'projeto' : 'desenho';
        $inicioSync = microtime(true);
        $this->sincronizarOrdensDoProcesso($processoId);
        $syncMs = (microtime(true) - $inicioSync) * 1000;
        $resposta = $tarefasModel->listarPorProcesso($processo, [
            'usuario_id' => (int) ($_SESSION['usuario'] ?? 0),
            'mostrar_dimensao_dxf' => false,
            'agrupar_projetos' => $itemTipo === 'projeto',
            'sem_limite' => true,
        ]);
        $tarefas = is_array($resposta['data'] ?? null) ? $resposta['data'] : [];
        $agrupados = [];
        foreach ($tarefas as $tarefa) {
            $prioridadeId = (int) ($tarefa['prioridade_id'] ?? 0);
            if ($prioridadeId <= 0) {
                continue;
            }

            if (!isset($agrupados[$prioridadeId])) {
                $agrupados[$prioridadeId] = 0;
            }
            $agrupados[$prioridadeId]++;
        }
        $performance = is_array($resposta['performance'] ?? null) ? $resposta['performance'] : [];
        $queries = $this->contarQueriesExecutadasDesdePainelTarefas($queriesInicio);
        $totalMs = (microtime(true) - $inicioTotal) * 1000;

        $this->logarPerformancePainelTarefasBackend('painel_tarefas_dados_lote', [
            'processo_id' => $processoId,
            'processo' => (string) ($processo['nome'] ?? ''),
            'tipo' => (string) ($processo['input'] ?? ''),
            'registros' => count($tarefas),
            'query_ms' => (float) ($performance['query_ms'] ?? 0),
            'sync_ms' => $syncMs,
            'html_ms' => 0,
            'total_ms' => $totalMs,
            'queries' => $queries,
        ]);

        return [
            'ok' => true,
            'lista' => $tarefas,
            'agrupados' => $agrupados,
            'item_tipo' => $itemTipo,
            'performance' => [
                'query_ms' => round((float) ($performance['query_ms'] ?? 0), 2),
                'sync_ms' => round($syncMs, 2),
                'total_ms' => round($totalMs, 2),
                'queries' => $queries,
            ],
        ];
    }

    public function index()
    {
        $this->iniciarSessao();

        if (!isset($_SESSION['usuario'])) {
            return redirect()->to(site_url('/'));
        }

        $permissoesUsuario = is_array($_SESSION['permissao'] ?? null) ? $_SESSION['permissao'] : [];
        if (!$this->usuarioPodeAcessarPainel($permissoesUsuario)) {
            Login::logout();
            return;
        }

        $acessoAbas = $this->mapaAcessoAbas($permissoesUsuario);
        $abas = $this->abasDisponiveis($permissoesUsuario);
        if (empty($abas)) {
            Login::logout();
            return;
        }

        $abaInicial = $this->abaInicial($abas, $acessoAbas, $this->abaPreferidaNivel());

        $processos = $this->buscarProcessosDisponiveis();
        $processosIndexados = [];
        foreach ($processos as $processo) {
            $processosIndexados[(int) $processo['id']] = $processo['nome'];
        }

        $periodoPadraoMeus = $this->periodoPadraoMeusDesenhos();
        $periodoPadraoFinalizados = $this->periodoPadraoTarefasConcluidas();
        $periodoMeusAplicadoInicial = $abaInicial === 'meus_desenhos';
        $periodoFinalizadosAplicadoInicial = $abaInicial === 'tarefas_concluidas';
        $dataInicioMeusInicial = $periodoMeusAplicadoInicial ? (string) ($periodoPadraoMeus['inicio_data'] ?? '') : '';
        $dataFimMeusInicial = $periodoMeusAplicadoInicial ? (string) ($periodoPadraoMeus['fim_data'] ?? '') : '';
        $dataInicioFinalizadosInicial = $periodoFinalizadosAplicadoInicial ? (string) ($periodoPadraoFinalizados['inicio_data'] ?? '') : '';
        $dataFimFinalizadosInicial = $periodoFinalizadosAplicadoInicial ? (string) ($periodoPadraoFinalizados['fim_data'] ?? '') : '';

        $processoInicialId = isset($processos[0]['id']) ? (int) $processos[0]['id'] : 0;
        if ($processoInicialId > 0 && in_array($abaInicial, ['lista_tarefas', 'lista_tarefas_adm'], true)) {
            $conteudoInicial = '<div class="alert alert-info mb-0" role="alert">Selecione o processo para carregar a lista.</div>';
        } elseif ($processoInicialId > 0) {
            $conteudoInicial = $this->renderizarConteudoAba(
                $abaInicial,
                $processoInicialId,
                $processosIndexados,
                (int) ($_SESSION['usuario'] ?? 0),
                $acessoAbas,
                false,
                $periodoFinalizadosAplicadoInicial,
                $dataInicioFinalizadosInicial,
                $dataFimFinalizadosInicial,
                $periodoMeusAplicadoInicial,
                $dataInicioMeusInicial,
                $dataFimMeusInicial
            );
        } else {
            $conteudoInicial = $this->renderizarVazioPorAba($abaInicial, 'Nenhuma lista disponivel para o seu perfil.');
        }

        $arrayView = [
            'titulo' => 'Painel de Tarefas',
            'functionType' => 'Painel de Tarefas',
            'nomeUsuario' => $this->decodificarValor($_SESSION['usuario_nome'] ?? ''),
            'menu' => $this->menu('painel_tarefas'),
            'abas' => $abas,
            'acessoAbas' => $acessoAbas,
            'abaInicial' => $abaInicial,
            'processos' => $processos,
            'processoInicialId' => $processoInicialId,
            'finalizadosDataInicioPadrao' => (string) ($periodoPadraoFinalizados['inicio_data'] ?? ''),
            'finalizadosDataFimPadrao' => (string) ($periodoPadraoFinalizados['fim_data'] ?? ''),
            'meusDataInicioPadrao' => (string) ($periodoPadraoMeus['inicio_data'] ?? ''),
            'meusDataFimPadrao' => (string) ($periodoPadraoMeus['fim_data'] ?? ''),
            'prioridadesModal' => $this->prioridadesDisponiveis(),
            'usuarioPodeAutorizarConclusao' => $this->usuarioPodeAutorizarConclusao($permissoesUsuario),
            'usuarioPodeGerenciarRecolocar' => $this->usuarioPodeGerenciarRecolocar($permissoesUsuario),
            'conteudoInicial' => $conteudoInicial,
        ];

        return view('painel_tarefas', $arrayView);
    }

    public function lista()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setBody('Requisicao invalida.');
        }

        $this->iniciarSessao();

        $permissoesUsuario = is_array($_SESSION['permissao'] ?? null) ? $_SESSION['permissao'] : [];
        if (!$this->usuarioPodeAcessarPainel($permissoesUsuario)) {
            return $this->response->setStatusCode(403)->setBody('Acesso negado.');
        }

        $acessoAbas = $this->mapaAcessoAbas($permissoesUsuario);
        $abas = $this->abasDisponiveis($permissoesUsuario);
        $aba = (string) $this->request->getPost('aba');
        if ($aba === '' || !isset($abas[$aba])) {
            $aba = $this->abaInicial($abas, $acessoAbas, $this->abaPreferidaNivel());
        }

        $processoId = (int) $this->request->getPost('processo_id');
        $mostrarConcluidasRaw = strtolower(trim((string) $this->request->getPost('mostrar_concluidas')));
        $mostrarConcluidas = in_array($mostrarConcluidasRaw, ['1', 'true', 'on', 'sim'], true);
        $periodoFinalizadosRaw = strtolower(trim((string) $this->request->getPost('finalizados_periodo_aplicado')));
        $periodoFinalizadosAplicado = in_array($periodoFinalizadosRaw, ['1', 'true', 'on', 'sim'], true);
        $dataInicioFinalizados = trim((string) $this->request->getPost('finalizados_data_inicio'));
        $dataFimFinalizados = trim((string) $this->request->getPost('finalizados_data_fim'));
        if ($aba === 'tarefas_concluidas' && (!$periodoFinalizadosAplicado || $dataInicioFinalizados === '' || $dataFimFinalizados === '')) {
            $periodoPadraoFinalizados = $this->periodoPadraoTarefasConcluidas();
            $periodoFinalizadosAplicado = true;
            $dataInicioFinalizados = (string) ($periodoPadraoFinalizados['inicio_data'] ?? '');
            $dataFimFinalizados = (string) ($periodoPadraoFinalizados['fim_data'] ?? '');
        }

        $periodoMeusRaw = strtolower(trim((string) $this->request->getPost('meus_periodo_aplicado')));
        $periodoMeusAplicado = in_array($periodoMeusRaw, ['1', 'true', 'on', 'sim'], true);
        $dataInicioMeus = trim((string) $this->request->getPost('meus_data_inicio'));
        $dataFimMeus = trim((string) $this->request->getPost('meus_data_fim'));
        if ($aba === 'meus_desenhos' && (!$periodoMeusAplicado || $dataInicioMeus === '' || $dataFimMeus === '')) {
            $periodoPadraoMeus = $this->periodoPadraoMeusDesenhos();
            $periodoMeusAplicado = true;
            $dataInicioMeus = (string) ($periodoPadraoMeus['inicio_data'] ?? '');
            $dataFimMeus = (string) ($periodoPadraoMeus['fim_data'] ?? '');
        }

        $processosDisponiveis = $this->buscarProcessosDisponiveis();
        $processosIndexados = [];
        foreach ($processosDisponiveis as $processo) {
            $processosIndexados[(int) $processo['id']] = $processo['nome'];
        }

        $formato = strtolower(trim((string) $this->request->getPost('formato')));
        if ($formato === 'json' && in_array($aba, ['lista_tarefas', 'lista_tarefas_adm'], true)) {
            $inicioTotalJson = microtime(true);
            $queriesInicioJson = $this->contarQueriesRegistradasPainelTarefas();
            if (!$this->abaPermitida($aba, $acessoAbas)) {
                return $this->response->setStatusCode(403)->setJSON([
                    'ok' => false,
                    'mensagem' => 'Sem permissao para acessar esta aba.',
                    'draw' => (int) $this->request->getPost('draw'),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                ]);
            }

            if ($processoId <= 0 || !isset($processosIndexados[$processoId])) {
                return $this->response->setStatusCode(422)->setJSON([
                    'ok' => false,
                    'mensagem' => 'Processo invalido ou sem permissao.',
                    'draw' => (int) $this->request->getPost('draw'),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                ]);
            }

            $inicioSyncJson = microtime(true);
            $this->sincronizarOrdensDoProcesso($processoId);
            $syncMsJson = (microtime(true) - $inicioSyncJson) * 1000;
            $tarefasModel = new \App\Models\TarefasFilaModel();
            $processo = $tarefasModel->processoPorId($processoId);
            if (!is_array($processo)) {
                return $this->response->setStatusCode(404)->setJSON([
                    'ok' => false,
                    'mensagem' => 'Processo nao encontrado.',
                    'draw' => (int) $this->request->getPost('draw'),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                ]);
            }

            $searchPost = $this->request->getPost('search');
            $searchValue = is_array($searchPost)
                ? (string) ($searchPost['value'] ?? '')
                : (string) ($searchPost ?? '');
            $agruparProjetos = $this->deveAgruparProjetosNoPainel($aba, $processoId);
            $mostrarDimensaoDxf = $aba === 'lista_tarefas' && $this->processoExibeDimensaoDxf($processoId);
            $resposta = $tarefasModel->listarPorProcesso($processo, [
                'draw' => (int) $this->request->getPost('draw'),
                'offset' => (int) $this->request->getPost('start'),
                'limit' => (int) ($this->request->getPost('length') ?: 50),
                'search' => $searchValue,
                'usuario_id' => (int) ($_SESSION['usuario'] ?? 0),
                'mostrar_dimensao_dxf' => $mostrarDimensaoDxf,
                'agrupar_projetos' => $agruparProjetos,
            ]);

            $resposta['ok'] = true;
            $resposta['aba'] = $aba;
            $resposta['titulo_lista'] = $this->tituloAba($aba);
            $resposta['processo_nome'] = $processosIndexados[$processoId] ?? '';
            $resposta['is_adm'] = $aba === 'lista_tarefas_adm';
            $resposta['tipo_processo'] = strtolower((string) ($processo['input'] ?? ''));
            $resposta['agrupado_por_projeto'] = $agruparProjetos;
            $resposta['mostrar_dimensao_dxf'] = $mostrarDimensaoDxf;
            $resposta['rotulo_nome'] = $agruparProjetos ? 'Descricao' : ($this->processoUsaDescricaoProjeto($processoId) ? 'Descricao' : 'Nome do arquivo');
            $resposta['performance'] = array_merge(is_array($resposta['performance'] ?? null) ? $resposta['performance'] : [], [
                'total_ms' => round((microtime(true) - $inicioTotalJson) * 1000, 2),
                'sync_ms' => round($syncMsJson, 2),
                'html_ms' => 0.0,
                'queries' => $this->contarQueriesExecutadasDesdePainelTarefas($queriesInicioJson),
            ]);

            $this->logarPerformancePainelTarefasBackend('painel_tarefas_' . $aba . '_json', [
                'processo_id' => $processoId,
                'processo' => $processosIndexados[$processoId] ?? '',
                'tipo' => (string) ($processo['input'] ?? ''),
                'registros' => count($resposta['data'] ?? []),
                'query_ms' => (float) ($resposta['performance']['query_ms'] ?? 0),
                'sync_ms' => $syncMsJson,
                'html_ms' => 0,
                'total_ms' => (float) ($resposta['performance']['total_ms'] ?? 0),
                'queries' => $resposta['performance']['queries'],
            ]);

            return $this->response->setJSON($resposta);
        }

        $inicioHtml = microtime(true);
        $queriesInicioHtml = $this->contarQueriesRegistradasPainelTarefas();
        $html = $this->renderizarConteudoAba(
            $aba,
            $processoId,
            $processosIndexados,
            (int) ($_SESSION['usuario'] ?? 0),
            $acessoAbas,
            $mostrarConcluidas,
            $periodoFinalizadosAplicado,
            $dataInicioFinalizados,
            $dataFimFinalizados,
            $periodoMeusAplicado,
            $dataInicioMeus,
            $dataFimMeus
        );
        $htmlMs = (microtime(true) - $inicioHtml) * 1000;
        if (in_array($aba, ['lista_tarefas', 'lista_tarefas_adm'], true)) {
            $perfListaPainel = $this->ultimaPerformanceListaPainel;
            $this->logarPerformancePainelTarefasBackend('painel_tarefas_' . $aba . '_html', [
                'processo_id' => $processoId,
                'processo' => $processosIndexados[$processoId] ?? '',
                'tipo' => '',
                'registros' => (int) ($perfListaPainel['registros'] ?? 0),
                'query_ms' => (float) ($perfListaPainel['query_ms'] ?? 0),
                'sync_ms' => 0,
                'html_ms' => $htmlMs,
                'total_ms' => $htmlMs,
                'queries' => $this->contarQueriesExecutadasDesdePainelTarefas($queriesInicioHtml),
            ]);
        }

        return $this->response
            ->setContentType('text/html; charset=UTF-8')
            ->setBody($html);
    }

    public function acao()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'mensagem' => 'Requisicao invalida.']);
        }

        $this->iniciarSessao();

        $permissoesUsuario = is_array($_SESSION['permissao'] ?? null) ? $_SESSION['permissao'] : [];
        if (!$this->usuarioPodeAcessarPainel($permissoesUsuario)) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'mensagem' => 'Acesso negado.']);
        }

        $acessoAbas = $this->mapaAcessoAbas($permissoesUsuario);
        $acao = (string) $this->request->getPost('acao');
        if ($acao === 'dados_lote') {
            $processoId = (int) $this->request->getPost('processo_id');
            $resultado = $this->dadosModalLote($processoId, $acessoAbas);
            $status = !empty($resultado['ok']) ? 200 : 422;
            return $this->response->setStatusCode($status)->setJSON($resultado);
        }

        if ($acao === 'mudar_lote') {
            $idsLote = $this->normalizarIdsLote($this->request->getPost('desenho_ids'));
            $itemTipo = strtolower(trim((string) $this->request->getPost('item_tipo')));
            $prioridadeId = (int) $this->request->getPost('prioridade_id');
            $ordemInicial = (int) $this->request->getPost('ordem_inicial');

            $resultado = $itemTipo === 'projeto'
                ? $this->aplicarMudancaProjetoLote($idsLote, $prioridadeId, $ordemInicial, $acessoAbas)
                : $this->aplicarMudancaLote($idsLote, $prioridadeId, $ordemInicial, $acessoAbas);
            $status = !empty($resultado['ok']) ? 200 : 422;

            return $this->response->setStatusCode($status)->setJSON([
                'ok' => !empty($resultado['ok']),
                'mensagem' => (string) ($resultado['mensagem'] ?? ''),
            ]);
        }

        if ($acao === 'apagar_lote') {
            $idsLote = $this->normalizarIdsLote($this->request->getPost('desenho_ids'));
            $itemTipo = strtolower(trim((string) $this->request->getPost('item_tipo')));
            $resultado = $itemTipo === 'projeto'
                ? $this->aplicarApagarProjetoLote($idsLote, (int) ($_SESSION['usuario'] ?? 0), $acessoAbas)
                : $this->aplicarApagarLote($idsLote, (int) ($_SESSION['usuario'] ?? 0), $acessoAbas);
            $status = !empty($resultado['ok']) ? 200 : 422;

            return $this->response->setStatusCode($status)->setJSON([
                'ok' => !empty($resultado['ok']),
                'mensagem' => (string) ($resultado['mensagem'] ?? ''),
            ]);
        }

        if ($acao === 'listar_solicitacoes_recolocar') {
            $resultado = $this->listarSolicitacoesRecolocarPendentes($acessoAbas, $permissoesUsuario);
            $status = !empty($resultado['ok']) ? 200 : 422;

            return $this->response->setStatusCode($status)->setJSON([
                'ok' => !empty($resultado['ok']),
                'mensagem' => (string) ($resultado['mensagem'] ?? ''),
                'itens' => is_array($resultado['itens'] ?? null) ? $resultado['itens'] : [],
                'podeGerenciar' => !empty($resultado['podeGerenciar']),
            ]);
        }

        if ($acao === 'decidir_solicitacao_recolocar') {
            $solicitacaoId = (int) $this->request->getPost('solicitacao_id');
            $decisao = (string) $this->request->getPost('decisao');
            $resultado = $this->decidirSolicitacaoRecolocar(
                $solicitacaoId,
                $decisao,
                $acessoAbas,
                $permissoesUsuario,
                (int) ($_SESSION['usuario'] ?? 0)
            );
            $status = !empty($resultado['ok']) ? 200 : 422;

            return $this->response->setStatusCode($status)->setJSON([
                'ok' => !empty($resultado['ok']),
                'mensagem' => (string) ($resultado['mensagem'] ?? ''),
            ]);
        }

        if ($acao === 'solicitar_recolocar') {
            $desenhoId = (int) $this->request->getPost('desenho_id');
            $resultado = $this->solicitarRecolocarDesenho($desenhoId, (int) ($_SESSION['usuario'] ?? 0), $acessoAbas);
            $status = !empty($resultado['ok']) ? 200 : 422;

            return $this->response->setStatusCode($status)->setJSON([
                'ok' => !empty($resultado['ok']),
                'mensagem' => (string) ($resultado['mensagem'] ?? ''),
            ]);
        }

        if ($acao === 'autorizar_conclusao') {
            $desenhoId = (int) $this->request->getPost('desenho_id');
            $autorizadorNome = trim((string) $this->request->getPost('autorizador_nome'));
            $autorizadorSenha = trim((string) $this->request->getPost('autorizador_senha'));

            $resultado = $this->autorizarConclusaoDesenho(
                $desenhoId,
                $acessoAbas,
                $permissoesUsuario,
                (int) ($_SESSION['usuario'] ?? 0),
                $autorizadorNome,
                $autorizadorSenha
            );
            $status = !empty($resultado['ok']) ? 200 : 422;

            return $this->response->setStatusCode($status)->setJSON([
                'ok' => !empty($resultado['ok']),
                'mensagem' => (string) ($resultado['mensagem'] ?? ''),
            ]);
        }

        $desenhoId = (int) $this->request->getPost('desenho_id');
        $itemTipo = strtolower(trim((string) $this->request->getPost('item_tipo')));
        $projetoId = (int) $this->request->getPost('projeto_id');
        if ($itemTipo === 'projeto') {
            if ($projetoId <= 0) {
                $projetoId = $desenhoId;
            }

            if (!$this->podeManipularProjetoAdm($projetoId, $acessoAbas)) {
                return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'mensagem' => 'Sem permissao para alterar este projeto.']);
            }

            if ($acao === 'apagar') {
                $resultado = $this->apagarProjetoPainel($projetoId, (int) ($_SESSION['usuario'] ?? 0), $acessoAbas);
                $status = !empty($resultado['ok']) ? 200 : 422;
                return $this->response->setStatusCode($status)->setJSON([
                    'ok' => !empty($resultado['ok']),
                    'mensagem' => (string) ($resultado['mensagem'] ?? ''),
                ]);
            }

            if ($acao === 'cancelar_corte') {
                $resultado = $this->cancelarCorteProjeto($projetoId, (int) ($_SESSION['usuario'] ?? 0), $acessoAbas);
                $status = !empty($resultado['ok']) ? 200 : 422;
                return $this->response->setStatusCode($status)->setJSON([
                    'ok' => !empty($resultado['ok']),
                    'mensagem' => (string) ($resultado['mensagem'] ?? ''),
                ]);
            }

            if ($acao === 'mudar_prioridade') {
                $prioridadeId = (int) $this->request->getPost('prioridade_id');
                $resultado = $this->aplicarMudancaPrioridadeProjeto($projetoId, $prioridadeId, null, $acessoAbas);
                $status = !empty($resultado['ok']) ? 200 : 422;
                return $this->response->setStatusCode($status)->setJSON([
                    'ok' => !empty($resultado['ok']),
                    'mensagem' => (string) ($resultado['mensagem'] ?? ''),
                ]);
            }

            if ($acao === 'mover_ordem') {
                $direcao = (string) $this->request->getPost('direcao');
                $resultado = $this->moverOrdemProjeto($projetoId, $direcao, $acessoAbas);
                $status = !empty($resultado['ok']) ? 200 : 422;
                return $this->response->setStatusCode($status)->setJSON([
                    'ok' => !empty($resultado['ok']),
                    'mensagem' => (string) ($resultado['mensagem'] ?? ''),
                ]);
            }

            return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'mensagem' => 'Acao invalida para projeto.']);
        }

        if (!$this->podeManipularDesenhoAdm($desenhoId, $acessoAbas)) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'mensagem' => 'Sem permissao para alterar este desenho.']);
        }

        $desenhosModel = new \App\Models\Desenhos();

        if ($acao === 'apagar') {
            $resultado = $this->apagarDesenhoPainel($desenhoId, (int) ($_SESSION['usuario'] ?? 0));
            $status = !empty($resultado['ok']) ? 200 : 422;
            return $this->response->setStatusCode($status)->setJSON([
                'ok' => !empty($resultado['ok']),
                'mensagem' => (string) ($resultado['mensagem'] ?? ''),
            ]);
        }

        if ($acao === 'cancelar_corte') {
            $desenho = $desenhosModel->where('id', $desenhoId)->first();
            $corteId = (int) ($desenho['corte_id'] ?? 0);
            $statusAntes = $this->decodificarValor($desenho['status'] ?? '');

            $db = \Config\Database::connect();
            $db->transStart();

            if ($corteId > 0) {
                (new \App\Models\Corte())->update($corteId, [
                    'status' => 'cancelado',
                    'usuario_id_fim' => (int) ($_SESSION['usuario'] ?? 0),
                    'data_end' => date('Y-m-d H:i:s'),
                ]);
            }

            $atualizou = $desenhosModel->update($desenhoId, ['status' => 'pendente']);
            if (!$atualizou) {
                $db->transRollback();
                return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'mensagem' => 'Falha ao cancelar o corte.']);
            }

            $auditou = $this->registrarAlteracaoPainel(
                'desenho',
                $desenhoId,
                'painel_tarefas.cancelar_corte',
                [
                    [
                        'campo' => 'status',
                        'valor_antes' => $statusAntes,
                        'valor_depois' => 'pendente',
                    ],
                    [
                        'campo' => 'corte_id',
                        'valor_antes' => (string) $corteId,
                        'valor_depois' => (string) $corteId,
                    ],
                ],
                [
                    'processo_id' => (int) ($desenho['processos_id'] ?? 0),
                    'processo_nome' => $this->obterNomeProcessoPorId((int) ($desenho['processos_id'] ?? 0)),
                    'corte_id' => $corteId,
                ]
            );

            if (!$auditou) {
                $db->transRollback();
                return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'mensagem' => 'Falha ao auditar o cancelamento do corte.']);
            }

            $db->transComplete();
            if (!$db->transStatus()) {
                return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'mensagem' => 'Falha ao concluir o cancelamento do corte.']);
            }

            return $this->response->setJSON(['ok' => true, 'mensagem' => 'Corte cancelado.']);
        }

        if ($acao === 'mudar_prioridade') {
            $prioridadeId = (int) $this->request->getPost('prioridade_id');
            if ($prioridadeId <= 0) {
                return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'mensagem' => 'Prioridade invalida.']);
            }

            $prioridadeExiste = (new \App\Models\Prioridade())
                ->where('id', $prioridadeId)
                ->where('status', 'ativo')
                ->first();
            if (!is_array($prioridadeExiste)) {
                return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'mensagem' => 'Prioridade nao encontrada.']);
            }

            $desenhoAtual = $desenhosModel->select('id, processos_id, prioridade_id')->where('id', $desenhoId)->first();
            if (!is_array($desenhoAtual) || empty($desenhoAtual['id'])) {
                return $this->response->setStatusCode(404)->setJSON(['ok' => false, 'mensagem' => 'Desenho nao encontrado.']);
            }
            $processoId = (int) ($desenhoAtual['processos_id'] ?? 0);
            $prioridadeAntes = (int) ($desenhoAtual['prioridade_id'] ?? 0);

            $ordemAntesRow = (new \App\Models\Ordem())
                ->select('ordem')
                ->where('desenho_id', $desenhoId)
                ->where('status', 'ativo')
                ->first();
            $ordemAntes = (int) ($ordemAntesRow['ordem'] ?? 0);

            $db = \Config\Database::connect();
            $db->transStart();

            $atualizou = $desenhosModel->update($desenhoId, ['prioridade_id' => $prioridadeId]);
            if (!$atualizou) {
                $db->transRollback();
                return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'mensagem' => 'Falha ao atualizar a prioridade.']);
            }

            if ($processoId > 0) {
                $maxLinha = (new \App\Models\Ordem())
                    ->selectMax('ordem', 'max_ordem')
                    ->where('status', 'ativo')
                    ->where('processos_id', $processoId)
                    ->where('prioridade_id', $prioridadeId)
                    ->where('desenho_id IS NOT NULL', null, false)
                    ->first();
                $novaOrdem = (int) ($maxLinha['max_ordem'] ?? 0) + 1;
                if ($novaOrdem <= 0) {
                    $novaOrdem = 1;
                }

                try {
                    Ferramentas::reordenarPorPrioridade($desenhoId, $novaOrdem, $prioridadeId, $processoId);
                } catch (\Throwable $e) {
                    $db->transRollback();
                    return $this->response->setStatusCode(422)->setJSON([
                        'ok' => false,
                        'mensagem' => 'Prioridade alterada, mas houve erro ao ajustar ordem: ' . $e->getMessage(),
                    ]);
                }
            }

            $ordemDepoisRow = (new \App\Models\Ordem())
                ->select('ordem')
                ->where('desenho_id', $desenhoId)
                ->where('status', 'ativo')
                ->first();
            $ordemDepois = (int) ($ordemDepoisRow['ordem'] ?? 0);

            $auditou = $this->registrarAlteracaoPainel(
                'desenho',
                $desenhoId,
                'painel_tarefas.mudar_prioridade',
                [
                    [
                        'campo' => 'prioridade_id',
                        'valor_antes' => (string) $prioridadeAntes,
                        'valor_depois' => (string) $prioridadeId,
                    ],
                    [
                        'campo' => 'ordem',
                        'valor_antes' => (string) $ordemAntes,
                        'valor_depois' => (string) $ordemDepois,
                    ],
                ],
                [
                    'processo_id' => $processoId,
                    'processo_nome' => $this->obterNomeProcessoPorId($processoId),
                ]
            );

            if (!$auditou) {
                $db->transRollback();
                return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'mensagem' => 'Falha ao auditar a mudanca de prioridade.']);
            }

            $db->transComplete();
            if (!$db->transStatus()) {
                return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'mensagem' => 'Falha ao concluir a mudanca de prioridade.']);
            }

            return $this->response->setJSON(['ok' => true, 'mensagem' => 'Prioridade atualizada.']);
        }

        if ($acao === 'mover_ordem') {
            $direcao = (string) $this->request->getPost('direcao');
            $resultado = $this->moverOrdemDesenho($desenhoId, $direcao);
            $status = !empty($resultado['ok']) ? 200 : 422;

            return $this->response->setStatusCode($status)->setJSON([
                'ok' => !empty($resultado['ok']),
                'mensagem' => (string) ($resultado['mensagem'] ?? ''),
            ]);
        }

        return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'mensagem' => 'Acao invalida.']);
    }

    public function visualizar()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => false, 'msg' => 'Requisicao invalida.']);
        }

        $this->iniciarSessao();

        $permissoesUsuario = is_array($_SESSION['permissao'] ?? null) ? $_SESSION['permissao'] : [];
        if (!$this->usuarioPodeAcessarPainel($permissoesUsuario)) {
            return $this->response->setStatusCode(403)->setJSON(['status' => false, 'msg' => 'Acesso negado.']);
        }

        $acessoAbas = $this->mapaAcessoAbas($permissoesUsuario);
        $desenhoId = (int) $this->request->getPost('id');
        $resposta = $this->respostaVisualizacaoDesenho($desenhoId, $acessoAbas);
        $statusCode = !empty($resposta['status']) ? 200 : 422;

        return $this->response->setStatusCode($statusCode)->setJSON($resposta);
    }
}
