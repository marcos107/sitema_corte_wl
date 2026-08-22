<?php

namespace App\Models;

use App\Controllers\Ferramentas;
use CodeIgniter\Model;

class TarefasFilaModel extends Model
{
    protected $table = 'desenhos';
    protected $returnType = 'array';

    private ?bool $metricasDisponiveis = null;
    private static ?bool $metricasDisponiveisGlobal = null;
    private static ?array $statusPermitidosCache = null;

    public function processoPorNome(string $nomeProcesso): ?array
    {
        $nomeProcesso = trim($nomeProcesso);
        if ($nomeProcesso === '') {
            return null;
        }

        $processo = (new Processos())
            ->select('id, nome, input')
            ->where('nome', $nomeProcesso)
            ->where('status', 'ativo')
            ->first();

        return is_array($processo) ? $processo : null;
    }

    public function processoPorId(int $processoId): ?array
    {
        if ($processoId <= 0) {
            return null;
        }

        $processo = (new Processos())
            ->select('id, nome, input')
            ->where('id', $processoId)
            ->where('status', 'ativo')
            ->first();

        return is_array($processo) ? $processo : null;
    }

    public function listarPorProcesso(array $processo, array $opcoes = []): array
    {
        $processoId = (int) ($processo['id'] ?? 0);
        $tipoProcesso = strtolower(trim((string) ($processo['input'] ?? '')));
        if ($processoId <= 0) {
            return $this->respostaVazia($opcoes);
        }

        $usarProjetos = !empty($opcoes['agrupar_projetos']) || $tipoProcesso === 'ind';
        return $usarProjetos
            ? $this->listarProjetos($processo, $opcoes)
            : $this->listarDesenhos($processo, $opcoes);
    }

    public function buscarItensProjetos(array $projetoIds): array
    {
        $projetoIds = array_values(array_unique(array_filter(array_map('intval', $projetoIds))));
        if ($projetoIds === []) {
            return [];
        }

        $rows = (new Projeto_desenho())
            ->select('projeto_desenho.*, desenhos.id AS id, desenhos.processos_id, desenhos.status, desenhos.nome, desenhos.diretorio, desenhos.corte_id, desenhos.prioridade_id, desenhos.finalidade_id, desenhos.empreendimentos_id, desenhos.empresa_id, desenhos.usuario_id_desenhista, desenhos.data_add')
            ->join('desenhos', 'desenhos.id = projeto_desenho.desenho_id', 'inner')
            ->whereIn('projeto_desenho.projeto_id', $projetoIds)
            ->whereIn('desenhos.status', $this->statusPermitidosAtivos())
            ->orderBy('projeto_desenho.projeto_id', 'ASC')
            ->orderBy('desenhos.id', 'ASC')
            ->findAll();

        $itens = [];
        foreach ($rows as $row) {
            $projetoId = (int) ($row['projeto_id'] ?? 0);
            if ($projetoId <= 0) {
                continue;
            }

            $itens[$projetoId][] = $row;
        }

        return $itens;
    }

    private function listarDesenhos(array $processo, array $opcoes): array
    {
        $processoId = (int) ($processo['id'] ?? 0);
        $usuarioId = (int) ($opcoes['usuario_id'] ?? 0);
        $semLimite = !empty($opcoes['sem_limite']);
        $limit = $this->normalizarLimit($opcoes['limit'] ?? 50);
        $offset = $semLimite ? 0 : max(0, (int) ($opcoes['offset'] ?? 0));
        $draw = (int) ($opcoes['draw'] ?? 0);
        $busca = trim((string) ($opcoes['search'] ?? ''));
        $finalidade = trim((string) ($opcoes['finalidade'] ?? ''));
        $mostrarDimensao = !empty($opcoes['mostrar_dimensao_dxf']);
        [$metricasSelect, $metricasJoin] = $this->sqlMetricasDxf('d', $mostrarDimensao);

        $statusPermitidos = $this->statusPermitidosAtivos();
        $statusCortandoCodificado = (string) Ferramentas::codificador('cortando');
        $bindingsBase = [$usuarioId, $statusCortandoCodificado, $processoId];
        $where = 'd.processos_id = ? AND d.status IN (' . $this->placeholders($statusPermitidos) . ')';
        $bindingsBase = array_merge($bindingsBase, $statusPermitidos);

        if ($finalidade !== '') {
            $where .= ' AND finalidade.nome = ?';
            $bindingsBase[] = $finalidade;
        }

        if ($busca !== '') {
            $like = '%' . $this->minusculo($busca) . '%';
            $where .= " AND (
                LOWER(d.nome) LIKE ?
                OR LOWER(empresa.nome) LIKE ?
                OR LOWER(empreendimentos.nome) LIKE ?
                OR LOWER(finalidade.nome) LIKE ?
                OR LOWER(usuarios.nome) LIKE ?
            )";
            array_push($bindingsBase, $like, $like, $like, $like, $like);
        }

        $sql = "
            SELECT base.*, COUNT(*) OVER() AS total_filtrado
            FROM (
                SELECT
                    d.id AS desenho_id,
                    d.id AS id,
                    d.corte_id,
                    d.prioridade_id,
                    d.nome,
                    d.diretorio,
                    d.status,
                    d.data_add,
                    prioridade.nome AS prioridade_nome,
                    prioridade.cor AS prioridade_cor,
                    prioridade.ordem AS prioridade_ordem,
                    usuarios.nome AS desenhista_nome,
                    empresa.nome AS empresa_nome,
                    empreendimentos.nome AS empreendimento_nome,
                    empreendimentos.escala AS empreendimento_escala,
                    finalidade.nome AS finalidade_nome,
                    o.ordem AS ordem,
                    CASE WHEN c.usuario_id_ini = ? AND c.status = 'inicio' AND (d.status = 'cortando' OR d.status = ?) THEN 1 ELSE 0 END AS eh_corte_usuario
                    {$metricasSelect}
                FROM desenhos d
                LEFT JOIN prioridade ON prioridade.id = d.prioridade_id
                LEFT JOIN usuarios ON usuarios.id = d.usuario_id_desenhista
                LEFT JOIN empresa ON empresa.id = d.empresa_id
                LEFT JOIN empreendimentos ON empreendimentos.id = d.empreendimentos_id
                LEFT JOIN finalidade ON finalidade.id = d.finalidade_id
                LEFT JOIN ordem o ON o.desenho_id = d.id AND o.projeto_id IS NULL AND o.status = 'ativo'
                LEFT JOIN corte c ON c.id = d.corte_id
                {$metricasJoin}
                WHERE {$where}
            ) base
            ORDER BY
                CASE WHEN base.eh_corte_usuario = 1 THEN 0 ELSE 1 END ASC,
                CASE WHEN base.prioridade_ordem IS NULL THEN 1 ELSE 0 END ASC,
                base.prioridade_ordem ASC,
                CASE WHEN base.ordem IS NULL THEN 1 ELSE 0 END ASC,
                base.ordem ASC,
                base.data_add ASC,
                base.desenho_id ASC
            " . ($semLimite ? '' : 'LIMIT ? OFFSET ?') . "
        ";

        $bindings = $semLimite ? $bindingsBase : array_merge($bindingsBase, [$limit, $offset]);
        $queryInicio = microtime(true);
        $rows = $this->db->query($sql, $bindings)->getResultArray();
        $queryMs = (microtime(true) - $queryInicio) * 1000;
        $total = $this->totalDaResposta($rows);
        if ($total === 0 && $offset > 0) {
            $total = $this->contarDesenhos($processoId, $statusPermitidos, $busca, $finalidade);
        }

        $dados = [];
        foreach ($rows as $indice => $row) {
            $dados[] = $this->mapearLinha($row, [
                'indice' => $offset + $indice,
                'item_tipo' => 'desenho',
                'tipo_processo' => strtolower((string) ($processo['input'] ?? '')),
                'mostrar_dimensao_dxf' => $mostrarDimensao,
            ]);
        }

        return $this->montarResposta($dados, $total, $draw, $limit, $offset, $processo, array_merge($opcoes, [
            '_query_ms' => $queryMs,
        ]));
    }

    private function listarProjetos(array $processo, array $opcoes): array
    {
        $processoId = (int) ($processo['id'] ?? 0);
        $usuarioId = (int) ($opcoes['usuario_id'] ?? 0);
        $semLimite = !empty($opcoes['sem_limite']);
        $limit = $this->normalizarLimit($opcoes['limit'] ?? 50);
        $offset = $semLimite ? 0 : max(0, (int) ($opcoes['offset'] ?? 0));
        $draw = (int) ($opcoes['draw'] ?? 0);
        $busca = trim((string) ($opcoes['search'] ?? ''));
        $finalidade = trim((string) ($opcoes['finalidade'] ?? ''));

        $statusPermitidos = $this->statusPermitidosAtivos();
        $statusCortandoCodificado = (string) Ferramentas::codificador('cortando');
        $bindingsBase = [$usuarioId, $statusCortandoCodificado, $processoId, $processoId];
        $where = 'd.processos_id = ? AND d.status IN (' . $this->placeholders($statusPermitidos) . ')';
        $bindingsBase = array_merge($bindingsBase, $statusPermitidos);
        $where .= " AND p.status IN ('ativo', 'pendente', 'processando', 'finalizado')";

        if ($finalidade !== '') {
            $where .= ' AND finalidade.nome = ?';
            $bindingsBase[] = $finalidade;
        }

        if ($busca !== '') {
            $like = '%' . $this->minusculo($busca) . '%';
            $where .= " AND (
                LOWER(p.descricao) LIKE ?
                OR LOWER(d.nome) LIKE ?
                OR LOWER(empresa.nome) LIKE ?
                OR LOWER(empreendimentos.nome) LIKE ?
                OR LOWER(finalidade.nome) LIKE ?
                OR LOWER(usuarios.nome) LIKE ?
            )";
            array_push($bindingsBase, $like, $like, $like, $like, $like, $like);
        }

        $sql = "
            SELECT projetos.*, COUNT(*) OVER() AS total_filtrado
            FROM (
                SELECT ranked.*
                FROM (
                    SELECT
                        pd.projeto_id,
                        pd.desenho_id,
                        p.descricao AS projeto_descricao,
                        p.status AS projeto_status,
                        p.data_add AS projeto_data_add,
                        d.id AS id,
                        d.id AS desenho_id_real,
                        d.corte_id,
                        d.prioridade_id,
                        d.nome,
                        d.diretorio,
                        d.status,
                        d.data_add,
                        prioridade.nome AS prioridade_nome,
                        prioridade.cor AS prioridade_cor,
                        prioridade.ordem AS prioridade_ordem,
                        usuarios.nome AS desenhista_nome,
                        empresa.nome AS empresa_nome,
                        empreendimentos.nome AS empreendimento_nome,
                        empreendimentos.escala AS empreendimento_escala,
                        finalidade.nome AS finalidade_nome,
                        od.ordem AS desenho_ordem,
                        op.ordem AS ordem,
                        op.prioridade_id AS projeto_prioridade_id,
                        COUNT(*) OVER (PARTITION BY pd.projeto_id) AS arquivos_count,
                        SUM(CASE WHEN IFNULL(pd.marcador, 0) <> 0 THEN 1 ELSE 0 END) OVER (PARTITION BY pd.projeto_id) AS arquivos_baixados_count,
                        CASE WHEN c.usuario_id_ini = ? AND c.status = 'inicio' AND (d.status = 'cortando' OR d.status = ?) THEN 1 ELSE 0 END AS eh_corte_usuario,
                        ROW_NUMBER() OVER (
                            PARTITION BY pd.projeto_id
                            ORDER BY
                                CASE WHEN prioridade.ordem IS NULL THEN 1 ELSE 0 END ASC,
                                prioridade.ordem ASC,
                                CASE WHEN od.ordem IS NULL THEN 1 ELSE 0 END ASC,
                                od.ordem ASC,
                                d.id ASC
                        ) AS rn
                    FROM projeto_desenho pd
                    INNER JOIN projeto p ON p.id = pd.projeto_id
                    INNER JOIN desenhos d ON d.id = pd.desenho_id
                    LEFT JOIN prioridade ON prioridade.id = d.prioridade_id
                    LEFT JOIN usuarios ON usuarios.id = d.usuario_id_desenhista
                    LEFT JOIN empresa ON empresa.id = d.empresa_id
                    LEFT JOIN empreendimentos ON empreendimentos.id = d.empreendimentos_id
                    LEFT JOIN finalidade ON finalidade.id = d.finalidade_id
                    LEFT JOIN ordem od ON od.desenho_id = d.id AND od.projeto_id IS NULL AND od.status = 'ativo'
                    LEFT JOIN ordem op ON op.projeto_id = p.id AND op.desenho_id IS NULL AND op.processos_id = ? AND op.status = 'ativo'
                    LEFT JOIN corte c ON c.id = d.corte_id
                    WHERE {$where}
                ) ranked
                WHERE ranked.rn = 1
            ) projetos
            ORDER BY
                CASE WHEN projetos.eh_corte_usuario = 1 THEN 0 ELSE 1 END ASC,
                CASE WHEN projetos.prioridade_ordem IS NULL THEN 1 ELSE 0 END ASC,
                projetos.prioridade_ordem ASC,
                CASE WHEN projetos.ordem IS NULL THEN 1 ELSE 0 END ASC,
                CAST(projetos.ordem AS UNSIGNED) ASC,
                projetos.ordem ASC,
                projetos.projeto_data_add ASC,
                projetos.projeto_id ASC
            " . ($semLimite ? '' : 'LIMIT ? OFFSET ?') . "
        ";

        $bindings = $semLimite ? $bindingsBase : array_merge($bindingsBase, [$limit, $offset]);
        $queryInicio = microtime(true);
        $rows = $this->db->query($sql, $bindings)->getResultArray();
        $queryMs = (microtime(true) - $queryInicio) * 1000;
        $total = $this->totalDaResposta($rows);
        if ($total === 0 && $offset > 0) {
            $total = $this->contarProjetos($processoId, $statusPermitidos, $busca, $finalidade);
        }

        $dados = [];
        foreach ($rows as $indice => $row) {
            $row['id'] = (int) ($row['projeto_id'] ?? 0);
            $row['desenho_id'] = (int) ($row['desenho_id_real'] ?? $row['desenho_id'] ?? 0);
            $row['projeto_id'] = (int) ($row['projeto_id'] ?? 0);
            $row['diretorio'] = dirname(rtrim(str_replace('\\', '/', $row['diretorio']), '/'));
            $row['nome_arquivo_exibicao'] = trim((string) ($row['projeto_descricao'] ?? ''));
            $dados[] = $this->mapearLinha($row, [
                'indice' => $offset + $indice,
                'item_tipo' => 'projeto',
                'tipo_processo' => 'ind',
                'mostrar_dimensao_dxf' => false,
            ]);
        }

        return $this->montarResposta($dados, $total, $draw, $limit, $offset, $processo, array_merge($opcoes, [
            'tipo_processo' => 'ind',
            '_query_ms' => $queryMs,
        ]));
    }

    private function montarResposta(array $dados, int $total, int $draw, int $limit, int $offset, array $processo, array $opcoes): array
    {
        $tipoProcesso = strtolower((string) ($opcoes['tipo_processo'] ?? $processo['input'] ?? ''));
        $mostrarDimensao = !empty($opcoes['mostrar_dimensao_dxf']) && $tipoProcesso !== 'ind';

        return [
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $dados,
            'lista' => $dados,
            'start' => $offset,
            'length' => !empty($opcoes['sem_limite']) ? count($dados) : $limit,
            'tipo_processo' => $tipoProcesso,
            'mostrar_dimensao_dxf' => $mostrarDimensao,
            'rotulo_nome' => $tipoProcesso === 'ind' ? 'Descricao' : 'Nome do arquivo',
            'itens_notificacao' => $this->itensNotificacao($dados, (string) ($processo['nome'] ?? '')),
            'performance' => [
                'query_ms' => round((float) ($opcoes['_query_ms'] ?? 0), 2),
                'registros' => count($dados),
            ],
        ];
    }

    private function mapearLinha(array $row, array $meta): array
    {
        $cor = $this->normalizarCor((string) ($row['prioridade_cor'] ?? ''));
        $status = $this->decodificar((string) ($row['status'] ?? ''));
        $status = $status !== '' ? $status : (string) ($row['status'] ?? '');
        $statusNormalizado = strtolower(trim($status));
        $dataEnvio = $this->formatarDataHora((string) ($row['data_add'] ?? $row['projeto_data_add'] ?? ''));
        $nomeArquivo = trim((string) ($row['nome_arquivo_exibicao'] ?? ''));
        if ($nomeArquivo === '') {
            $nomeArquivo = Ferramentas::remove_id_file($this->decodificar((string) ($row['nome'] ?? '')));
        }

        $largura = isset($row['dxf_largura_mm']) ? (float) $row['dxf_largura_mm'] : 0.0;
        $altura = isset($row['dxf_altura_mm']) ? (float) $row['dxf_altura_mm'] : 0.0;

        return [
            'indice' => (int) ($meta['indice'] ?? 0),
            'id' => (int) ($row['id'] ?? 0),
            'desenho_id' => (int) ($row['desenho_id'] ?? $row['id'] ?? 0),
            'projeto_id' => (int) ($row['projeto_id'] ?? 0),
            'item_tipo' => (string) ($meta['item_tipo'] ?? 'desenho'),
            'corte_id' => (int) ($row['corte_id'] ?? 0),
            'prioridade_id' => (int) ($row['prioridade_id'] ?? $row['projeto_prioridade_id'] ?? 0),
            'prioridade_nome' => $this->decodificar((string) ($row['prioridade_nome'] ?? '')),
            'prioridade_cor' => $cor,
            'prioridade_texto' => $this->corTextoParaFundo($cor),
            'ordem' => $row['ordem'] ?? '',
            'desenhista_nome' => $this->decodificar((string) ($row['desenhista_nome'] ?? '')),
            'nome_arquivo' => $nomeArquivo,
            'empresa_nome' => $this->decodificar((string) ($row['empresa_nome'] ?? '')),
            'empreendimento_nome' => $this->decodificar((string) ($row['empreendimento_nome'] ?? '')),
            'empreendimento_escala' => $this->decodificar((string) ($row['empreendimento_escala'] ?? '')),
            'finalidade_nome' => $this->decodificar((string) ($row['finalidade_nome'] ?? '')),
            'subpastas' => $this->extrairTags((string) ($row['diretorio'] ?? '')),
            'dimensao_dxf' => ($largura > 0 && $altura > 0) ? $this->formatarDimensao($largura, $altura) : '',
            'status' => $status,
            'status_normalizado' => $statusNormalizado,
            'data_envio' => $dataEnvio,
            'eh_corte_usuario' => !empty($row['eh_corte_usuario']),
            'arquivos_count' => (int) ($row['arquivos_count'] ?? 1),
            'arquivos_baixados_count' => (int) ($row['arquivos_baixados_count'] ?? 0),
            'tipo_processo' => (string) ($meta['tipo_processo'] ?? ''),
            'mostrar_dimensao_dxf' => !empty($meta['mostrar_dimensao_dxf']),
            'nome_original' => (string) ($row['nome'] ?? ''),
            'diretorio' => (string) ($row['diretorio'] ?? ''),
        ];
    }

    private function contarDesenhos(int $processoId, array $statusPermitidos, string $busca, string $finalidade): int
    {
        $where = 'd.processos_id = ? AND d.status IN (' . $this->placeholders($statusPermitidos) . ')';
        $bindings = array_merge([$processoId], $statusPermitidos);

        if ($finalidade !== '') {
            $where .= ' AND finalidade.nome = ?';
            $bindings[] = $finalidade;
        }

        if ($busca !== '') {
            $like = '%' . $this->minusculo($busca) . '%';
            $where .= " AND (
                LOWER(d.nome) LIKE ?
                OR LOWER(empresa.nome) LIKE ?
                OR LOWER(empreendimentos.nome) LIKE ?
                OR LOWER(finalidade.nome) LIKE ?
                OR LOWER(usuarios.nome) LIKE ?
            )";
            array_push($bindings, $like, $like, $like, $like, $like);
        }

        $sql = "
            SELECT COUNT(*) AS total
            FROM desenhos d
            LEFT JOIN empresa ON empresa.id = d.empresa_id
            LEFT JOIN empreendimentos ON empreendimentos.id = d.empreendimentos_id
            LEFT JOIN finalidade ON finalidade.id = d.finalidade_id
            LEFT JOIN usuarios ON usuarios.id = d.usuario_id_desenhista
            WHERE {$where}
        ";

        $row = $this->db->query($sql, $bindings)->getRowArray();
        return (int) ($row['total'] ?? 0);
    }

    private function contarProjetos(int $processoId, array $statusPermitidos, string $busca, string $finalidade): int
    {
        $where = 'd.processos_id = ? AND d.status IN (' . $this->placeholders($statusPermitidos) . ") AND p.status IN ('ativo', 'pendente', 'processando', 'finalizado')";
        $bindings = array_merge([$processoId], $statusPermitidos);

        if ($finalidade !== '') {
            $where .= ' AND finalidade.nome = ?';
            $bindings[] = $finalidade;
        }

        if ($busca !== '') {
            $like = '%' . $this->minusculo($busca) . '%';
            $where .= " AND (
                LOWER(p.descricao) LIKE ?
                OR LOWER(d.nome) LIKE ?
                OR LOWER(empresa.nome) LIKE ?
                OR LOWER(empreendimentos.nome) LIKE ?
                OR LOWER(finalidade.nome) LIKE ?
                OR LOWER(usuarios.nome) LIKE ?
            )";
            array_push($bindings, $like, $like, $like, $like, $like, $like);
        }

        $sql = "
            SELECT COUNT(DISTINCT pd.projeto_id) AS total
            FROM projeto_desenho pd
            INNER JOIN projeto p ON p.id = pd.projeto_id
            INNER JOIN desenhos d ON d.id = pd.desenho_id
            LEFT JOIN empresa ON empresa.id = d.empresa_id
            LEFT JOIN empreendimentos ON empreendimentos.id = d.empreendimentos_id
            LEFT JOIN finalidade ON finalidade.id = d.finalidade_id
            LEFT JOIN usuarios ON usuarios.id = d.usuario_id_desenhista
            WHERE {$where}
        ";

        $row = $this->db->query($sql, $bindings)->getRowArray();
        return (int) ($row['total'] ?? 0);
    }

    private function respostaVazia(array $opcoes): array
    {
        return [
            'draw' => (int) ($opcoes['draw'] ?? 0),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'lista' => [],
            'start' => max(0, (int) ($opcoes['offset'] ?? 0)),
            'length' => $this->normalizarLimit($opcoes['limit'] ?? 50),
            'tipo_processo' => '',
            'mostrar_dimensao_dxf' => false,
            'rotulo_nome' => 'Nome do arquivo',
            'itens_notificacao' => [],
        ];
    }

    private function statusPermitidosAtivos(): array
    {
        if (self::$statusPermitidosCache !== null) {
            return self::$statusPermitidosCache;
        }

        $status = ['pendente', 'cortando', 'processando'];
        foreach (['pendente', 'cortando', 'processando'] as $item) {
            $codificado = (string) Ferramentas::codificador($item);
            if ($codificado !== '') {
                $status[] = $codificado;
            }
        }

        self::$statusPermitidosCache = array_values(array_unique($status));
        return self::$statusPermitidosCache;
    }

    private function normalizarLimit($limit): int
    {
        $limit = (int) $limit;
        if ($limit <= 0) {
            return 50;
        }

        return min($limit, 100);
    }

    private function placeholders(array $valores): string
    {
        return implode(',', array_fill(0, count($valores), '?'));
    }

    private function totalDaResposta(array $rows): int
    {
        if ($rows === []) {
            return 0;
        }

        return (int) ($rows[0]['total_filtrado'] ?? count($rows));
    }

    private function sqlMetricasDxf(string $aliasDesenho, bool $usarMetricas): array
    {
        if (!$usarMetricas || !$this->tabelaMetricasDisponivel()) {
            return [', NULL AS dxf_largura_mm, NULL AS dxf_altura_mm', ''];
        }

        return [
            ",
                    amm.dxf_largura_mm,
                    amm.dxf_altura_mm",
            "LEFT JOIN (
                    SELECT
                        entidade_id,
                        MAX(CASE WHEN metrica = 'largura_max_mm' THEN valor_base END) AS dxf_largura_mm,
                        MAX(CASE WHEN metrica = 'altura_max_mm' THEN valor_base END) AS dxf_altura_mm
                    FROM arquivo_metricas_material
                    WHERE entidade_tipo = 'desenho'
                      AND tipo_arquivo = 'dxf'
                      AND metrica IN ('largura_max_mm', 'altura_max_mm')
                    GROUP BY entidade_id
                ) amm ON amm.entidade_id = {$aliasDesenho}.id",
        ];
    }

    private function tabelaMetricasDisponivel(): bool
    {
        if ($this->metricasDisponiveis !== null) {
            return $this->metricasDisponiveis;
        }

        if (self::$metricasDisponiveisGlobal === null) {
            self::$metricasDisponiveisGlobal = $this->db->tableExists('arquivo_metricas_material');
        }

        $this->metricasDisponiveis = self::$metricasDisponiveisGlobal;
        return $this->metricasDisponiveis;
    }

    private function decodificar(string $valor): string
    {
        if ($valor === '') {
            return '';
        }

        $decodificado = Ferramentas::decodificador($valor);
        return $decodificado !== '' ? $decodificado : $valor;
    }

    private function minusculo(string $valor): string
    {
        return function_exists('mb_strtolower') ? mb_strtolower($valor, 'UTF-8') : strtolower($valor);
    }

    private function extrairTags(string $diretorio): string
    {
        if ($diretorio === '') {
            return '';
        }

        $tags = explode('/', str_replace('\\', '/', $diretorio));
        $tags = array_slice($tags, 6);
        if ($tags !== []) {
            array_pop($tags);
        }

        return implode(' - ', array_filter($tags, static fn ($tag) => trim((string) $tag) !== ''));
    }

    private function formatarDataHora(string $data): string
    {
        $timestamp = strtotime($data);
        return $timestamp ? date('d/m/Y H:i:s', $timestamp) : $data;
    }

    private function normalizarCor(string $cor): string
    {
        $cor = trim($this->decodificar($cor));
        return preg_match('/^#[0-9A-F]{6}$/i', $cor) ? $cor : '#cbd5e1';
    }

    private function corTextoParaFundo(string $cor): string
    {
        if (!preg_match('/^#[0-9A-F]{6}$/i', $cor)) {
            return '#0f172a';
        }

        $r = hexdec(substr($cor, 1, 2));
        $g = hexdec(substr($cor, 3, 2));
        $b = hexdec(substr($cor, 5, 2));
        $luminancia = (0.299 * $r) + (0.587 * $g) + (0.114 * $b);

        return $luminancia > 165 ? '#0f172a' : '#f8fafc';
    }

    private function formatarDimensao(float $largura, float $altura): string
    {
        return 'L max: ' . $this->formatarNumero($largura) . ' mm | H max: ' . $this->formatarNumero($altura) . ' mm';
    }

    private function formatarNumero(float $valor): string
    {
        $texto = number_format(round($valor, 2), 2, ',', '.');
        $texto = preg_replace('/,00$/', '', $texto) ?? $texto;
        return preg_replace('/(,\d*[1-9])0+$/', '$1', $texto) ?? $texto;
    }

    private function itensNotificacao(array $dados, string $processoNome): array
    {
        $itens = [];
        foreach ($dados as $row) {
            $prefixo = ($row['item_tipo'] ?? 'desenho') === 'projeto' ? 'projeto_' : 'desenho_';
            $id = ($row['item_tipo'] ?? 'desenho') === 'projeto'
                ? (int) ($row['projeto_id'] ?? $row['id'] ?? 0)
                : (int) ($row['desenho_id'] ?? $row['id'] ?? 0);

            if ($id <= 0) {
                continue;
            }

            $itens[] = [
                'item_id' => $prefixo . $id,
                'processo' => $this->decodificar($processoNome),
                'projetista' => (string) ($row['desenhista_nome'] ?? ''),
                'desenho' => (string) ($row['nome_arquivo'] ?? ''),
            ];
        }

        return $itens;
    }
}
