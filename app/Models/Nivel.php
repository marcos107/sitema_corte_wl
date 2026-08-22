<?php

namespace App\Models;

use App\Controllers\Ferramentas;
use App\Libraries\NivelTelaInicial;
use CodeIgniter\Model;
use Config\Database;

class Nivel extends Model
{
    protected $table = 'nivel';
    protected $primaryKey = 'id';
    protected $allowedFields = ['usuario_id', 'nome', 'status', 'relatorio', 'tela_inicial', 'permissao', 'processos', 'data_add', 'individuo', 'nivel_adicional_id'];
    protected $returnType = 'array';

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'data_add';
    protected $updatedField = '';

    private static array $tableExistsCache = [];
    private static array $tableColumnsCache = [];

    public function supportsColumn(string $column): bool
    {
        return in_array($column, $this->tableColumns(), true);
    }

    public function hasRelationTables(): bool
    {
        return $this->tableExists('nivel_permissoes') && $this->tableExists('nivel_processos');
    }

    public function listarPermissoesAtivas(int $nivelId): array
    {
        if ($nivelId <= 0) {
            return [];
        }

        if ($this->tableExists('nivel_permissoes')) {
            $rows = (new Nivel_permissoes())
                ->select('permissao')
                ->where('nivel_id', $nivelId)
                ->where('status', 'ativo')
                ->findAll();

            $permissoes = $this->normalizarValores(array_column($rows, 'permissao'));
            if (!empty($permissoes)) {
                return $permissoes;
            }
        }

        $nivel = $this->find($nivelId);
        if (!is_array($nivel)) {
            return [];
        }

        return $this->explodirValores((string) ($nivel['permissao'] ?? ''));
    }

    public function listarProcessosIds(int $nivelId): array
    {
        if ($nivelId <= 0) {
            return [];
        }

        if ($this->tableExists('nivel_processos')) {
            $rows = (new Nivel_processos())
                ->select('processo_id')
                ->where('nivel_id', $nivelId)
                ->findAll();

            $processos = $this->normalizarValores(array_column($rows, 'processo_id'));
            if (!empty($processos)) {
                return $processos;
            }
        }

        $nivel = $this->find($nivelId);
        if (!is_array($nivel)) {
            return [];
        }

        return $this->explodirValores((string) ($nivel['processos'] ?? ''));
    }

    public function listarProcessosDetalhados(int $nivelId): array
    {
        $processosIds = $this->listarProcessosIds($nivelId);
        if (empty($processosIds)) {
            return [];
        }

        $processosModel = new Processos();
        $processos = [];

        foreach ($processosIds as $processoId) {
            $processo = $processosModel->find((int) $processoId);
            if (!is_array($processo)) {
                continue;
            }

            $processos[] = [
                'id' => (string) ($processo['id'] ?? $processoId),
                'nome' => Ferramentas::decodificador((string) ($processo['nome'] ?? '')),
                'status' => (string) ($processo['status'] ?? ''),
            ];
        }

        return $processos;
    }

    public function listarNivelAdicionalId(int $nivelId): int
    {
        if ($nivelId <= 0 || !$this->supportsColumn('nivel_adicional_id')) {
            return 0;
        }

        $nivel = $this->find($nivelId);
        if (!is_array($nivel)) {
            return 0;
        }

        $nivelAdicionalId = (int) ($nivel['nivel_adicional_id'] ?? 0);
        if ($nivelAdicionalId <= 0 || $nivelAdicionalId === $nivelId) {
            return 0;
        }

        return $nivelAdicionalId;
    }

    public function listarNiveisRelacionados(int $nivelId): array
    {
        if ($nivelId <= 0) {
            return [];
        }

        $niveis = [];
        $visitados = [];
        $nivelAtualId = $nivelId;
        $guard = 0;

        while ($nivelAtualId > 0 && !isset($visitados[$nivelAtualId]) && $guard < 5) {
            $nivel = $this->find($nivelAtualId);
            if (!is_array($nivel) || empty($nivel)) {
                break;
            }

            if ($guard > 0 && (string) ($nivel['status'] ?? '') !== 'ativo') {
                break;
            }

            $visitados[$nivelAtualId] = true;
            $niveis[] = $nivelAtualId;
            $guard++;

            if (!$this->supportsColumn('nivel_adicional_id')) {
                break;
            }

            $proximoId = (int) ($nivel['nivel_adicional_id'] ?? 0);
            if ($proximoId <= 0 || isset($visitados[$proximoId])) {
                break;
            }

            $nivelAtualId = $proximoId;
        }

        return $niveis;
    }

    public function montarContextoAcesso(int $nivelId): array
    {
        $niveisRelacionados = $this->listarNiveisRelacionados($nivelId);
        $permissoes = [];
        $processosIds = [];
        $processosNomes = [];
        $processosPorContexto = [];
        $processosIdsPorContexto = [];
        $origemContexto = [];

        foreach ($niveisRelacionados as $nivelRelacionadoId) {
            $nivelRelacionado = $this->find($nivelRelacionadoId);
            if (!is_array($nivelRelacionado) || empty($nivelRelacionado)) {
                continue;
            }

            $permissoesNivel = $this->listarPermissoesAtivas($nivelRelacionadoId);
            $processosIdsNivel = $this->normalizarValores(array_map('strval', $this->listarProcessosIds($nivelRelacionadoId)));
            $processosDetalhadosNivel = $this->listarProcessosDetalhados($nivelRelacionadoId);
            $processosNomesNivel = [];

            foreach ($processosDetalhadosNivel as $processoDetalhado) {
                $nomeProcesso = trim((string) ($processoDetalhado['nome'] ?? ''));
                if ($nomeProcesso !== '') {
                    $processosNomesNivel[] = $nomeProcesso;
                }
            }

            $permissoes = $this->normalizarValores(array_merge($permissoes, $permissoesNivel));
            $processosIds = $this->normalizarValores(array_merge($processosIds, $processosIdsNivel));
            $processosNomes = $this->normalizarValores(array_merge($processosNomes, $processosNomesNivel));

            foreach (array_keys($this->contextosProcessos()) as $contexto) {
                if (array_key_exists($contexto, $processosPorContexto)) {
                    continue;
                }

                if (!$this->nivelConcedeContexto($permissoesNivel, $contexto)) {
                    continue;
                }

                $nomeNivel = Ferramentas::decodificador((string) ($nivelRelacionado['nome'] ?? ''));
                if ($nomeNivel === '') {
                    $nomeNivel = (string) ($nivelRelacionado['nome'] ?? '');
                }

                $processosPorContexto[$contexto] = $this->normalizarValores($processosNomesNivel);
                $processosIdsPorContexto[$contexto] = $processosIdsNivel;
                $origemContexto[$contexto] = [
                    'nivel_id' => $nivelRelacionadoId,
                    'nivel_nome' => $nomeNivel,
                ];
            }
        }

        return [
            'nivel_ids' => $niveisRelacionados,
            'permissoes' => $permissoes,
            'processos_ids' => $processosIds,
            'processos_nomes' => $processosNomes,
            'processos_por_contexto' => $processosPorContexto,
            'processos_ids_por_contexto' => $processosIdsPorContexto,
            'origem_contexto' => $origemContexto,
        ];
    }

    private function tableExists(string $table): bool
    {
        $table = trim($table);
        if ($table === '') {
            return false;
        }

        if (!array_key_exists($table, self::$tableExistsCache)) {
            self::$tableExistsCache[$table] = Database::connect()->tableExists($table);
        }

        return self::$tableExistsCache[$table];
    }

    private function tableColumns(): array
    {
        if (!array_key_exists($this->table, self::$tableColumnsCache)) {
            self::$tableColumnsCache[$this->table] = Database::connect()->getFieldNames($this->table) ?: [];
        }

        return self::$tableColumnsCache[$this->table];
    }

    private function normalizarValores(array $valores): array
    {
        return array_values(array_unique(array_filter(array_map(static function ($valor): string {
            return trim((string) $valor);
        }, $valores), static fn ($valor): bool => $valor !== '')));
    }

    private function explodirValores(string $valores): array
    {
        return $this->normalizarValores(explode('-', $valores));
    }

    private function filtrarCamposExistentes(array $dados): array
    {
        $campos = $this->tableColumns();
        $filtrados = [];

        foreach ($dados as $campo => $valor) {
            if (in_array($campo, $campos, true)) {
                $filtrados[$campo] = $valor;
            }
        }

        return $filtrados;
    }

    private function prepararNivelData(array $nivelData, string $permissoesString, string $processosString): array
    {
        $dados = $nivelData;

        if (array_key_exists('nome', $dados)) {
            $nomeCodificado = Ferramentas::codificador((string) $dados['nome']);
            $dados['nome'] = $nomeCodificado !== '' ? $nomeCodificado : (string) $dados['nome'];
        }

        if ($this->supportsColumn('permissao')) {
            $dados['permissao'] = $permissoesString;
        }

        if ($this->supportsColumn('processos')) {
            $dados['processos'] = $processosString;
        }

        if ($this->supportsColumn('tela_inicial')) {
            $dados['tela_inicial'] = trim((string) ($dados['tela_inicial'] ?? ''));
        } else {
            unset($dados['tela_inicial']);
        }

        if ($this->supportsColumn('nivel_adicional_id')) {
            $nivelAdicionalId = (int) ($dados['nivel_adicional_id'] ?? 0);
            $dados['nivel_adicional_id'] = $nivelAdicionalId > 0 ? $nivelAdicionalId : null;
        } else {
            unset($dados['nivel_adicional_id']);
        }

        if (!$this->supportsColumn('relatorio')) {
            unset($dados['relatorio']);
        }

        if (!$this->supportsColumn('usuario_id')) {
            unset($dados['usuario_id']);
        }

        return $this->filtrarCamposExistentes($dados);
    }

    private function contextosProcessos(): array
    {
        return [
            'desenho_adicionar' => ['Adicionar', 'desenho_adicionar'],
            'desenho_meus' => ['Meus Desenhos', 'Meus_Desenhos', 'desenho_meus'],
            'lista_corte' => ['Lista De Corte', 'Lista_De_Corte', 'lista_corte'],
            'lista_corte_adm' => ['Lista De Corte ADM', 'Lista_De_Corte_ADM', 'Lista_De_Corte ADM', 'lista_corte_adm', 'lista_tarefas_adm'],
            'lista_tarefas' => ['Lista De Corte Cortador', 'Lista_De_Corte_Cortador', 'lista_tarefas'],
            'desenhos_cortados' => ['Desenhos cortados', 'Desenhos_cortados', 'desenhos_cortados'],
            'subpasta' => ['Subpasta', 'subpasta'],
            'relatorios' => ['Relatorio', 'Relatório', 'Relatorio', 'relatorios'],
        ];
    }

    private function nivelConcedeContexto(array $permissoes, string $contexto): bool
    {
        $permissoesNormalizadas = NivelTelaInicial::normalizarPermissoes($permissoes);
        if (in_array('all', $permissoesNormalizadas, true)) {
            return true;
        }

        foreach ($this->contextosProcessos()[$contexto] ?? [] as $alias) {
            $aliasNormalizado = NivelTelaInicial::normalizarPermissoes([$alias]);
            $aliasNormalizado = $aliasNormalizado[0] ?? '';
            if ($aliasNormalizado !== '' && in_array($aliasNormalizado, $permissoesNormalizadas, true)) {
                return true;
            }
        }

        return false;
    }

    private function sincronizarPermissoes(int $nivelId, string $permissoesString, int $usuarioId): void
    {
        if (!$this->tableExists('nivel_permissoes')) {
            return;
        }

        $permModel = new Nivel_permissoes();
        $permModel->where('nivel_id', $nivelId)->delete();

        foreach ($this->explodirValores($permissoesString) as $permissao) {
            $permModel->insert([
                'nivel_id' => $nivelId,
                'permissao' => $permissao,
                'status' => 'ativo',
                'usuario_id' => $usuarioId,
            ]);
        }
    }

    private function sincronizarProcessos(int $nivelId, string $processosString): void
    {
        if (!$this->tableExists('nivel_processos')) {
            return;
        }

        $procModel = new Nivel_processos();
        $procModel->where('nivel_id', $nivelId)->delete();

        foreach ($this->explodirValores($processosString) as $processoId) {
            $processoIdInt = (int) $processoId;
            if ($processoIdInt <= 0) {
                continue;
            }

            $procModel->insert([
                'nivel_id' => $nivelId,
                'processo_id' => $processoIdInt,
            ]);
        }
    }

    public function insertWithRelations(array $nivelData, string $permissoesString, string $processosString)
    {
        $db = Database::connect();
        $db->transStart();

        $registro = $this->prepararNivelData($nivelData, $permissoesString, $processosString);
        $this->insert($registro);
        $nivelId = (int) $this->getInsertID();

        if ($nivelId > 0) {
            $this->sincronizarPermissoes($nivelId, $permissoesString, (int) ($nivelData['usuario_id'] ?? 0));
            $this->sincronizarProcessos($nivelId, $processosString);
        }

        $db->transComplete();
        return $db->transStatus() && $nivelId > 0 ? $nivelId : false;
    }

    public function updateWithRelations(int $nivelId, array $nivelData, string $permissoesString, string $processosString): bool
    {
        $db = Database::connect();
        $db->transStart();

        $registro = $this->prepararNivelData($nivelData, $permissoesString, $processosString);
        $this->update($nivelId, $registro);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->sincronizarPermissoes($nivelId, $permissoesString, (int) ($_SESSION['usuario'] ?? 0));
        $this->sincronizarProcessos($nivelId, $processosString);

        $db->transComplete();
        return $db->transStatus();
    }
}
