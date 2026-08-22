<?php

namespace App\Models;

use App\Controllers\Ferramentas;
use CodeIgniter\Model;

class Alteracoes extends Model
{
    protected $table = 'alteracoes';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'usuario_id',
        'item',
        'id_item',
        'data_add',
        'individuo',
        'antes',
        'depois',
        'info_mais',
    ];
    protected $returnType = 'array';

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'data_add';
    protected $updatedField = '';
    protected $beforeInsert = ['normalizeInsertPayload'];

    private ?array $tableColumnsCache = null;
    private ?bool $detailsTableExistsCache = null;
    private array $macCache = [];

    /**
     * Insere uma alteracao com detalhes e contexto de auditoria.
     *
     * Funciona tanto com o schema legado (`alteracoes` + `info_mais`)
     * quanto com o schema novo (`alteracoes` + `alteracoes_detalhes`).
     *
     * @param array $alteracao Dados da linha principal. Aceita `_meta` com contexto extra.
     * @param array $detalhes Array de arrays com chaves `campo`, `valor_antes`, `valor_depois`.
     * @return int|false
     */
    public function insertWithDetails(array $alteracao, array $detalhes)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $metaExtra = [];
        if (isset($alteracao['_meta']) && is_array($alteracao['_meta'])) {
            $metaExtra = $alteracao['_meta'];
        }
        unset($alteracao['_meta']);

        $detalhesNormalizados = $this->normalizarDetalhes($detalhes);
        $metaAuditoria = $this->coletarMetaAuditoria($metaExtra);

        if ($this->usaSchemaLegado()) {
            $alteracao['antes'] = $alteracao['antes'] ?? $this->resumirDetalhes($detalhesNormalizados, 'valor_antes');
            $alteracao['depois'] = $alteracao['depois'] ?? $this->resumirDetalhes($detalhesNormalizados, 'valor_depois');
            $alteracao['info_mais'] = $this->montarInfoMaisLegado(
                $alteracao['info_mais'] ?? '',
                $detalhesNormalizados,
                $metaAuditoria
            );
            $alteracao['data_add'] = $alteracao['data_add'] ?? date('d/m/Y H:i:s');
        }

        $this->insert($alteracao);
        $insertId = (int) $this->getInsertID();
        if ($insertId <= 0) {
            $db->transComplete();
            return false;
        }

        if ($this->detailsTableExists()) {
            $detalhesModel = new \App\Models\Alteracoes_detalhes();

            foreach ($detalhesNormalizados as $detalhe) {
                $detalhesModel->insert([
                    'alteracao_id' => $insertId,
                    'campo' => $detalhe['campo'],
                    'valor_antes' => $detalhe['valor_antes'],
                    'valor_depois' => $detalhe['valor_depois'],
                ]);
            }

            foreach ($metaAuditoria as $chave => $valor) {
                $detalhesModel->insert([
                    'alteracao_id' => $insertId,
                    'campo' => 'auditoria.' . $chave,
                    'valor_antes' => '',
                    'valor_depois' => $this->stringify($valor, 60000),
                ]);
            }
        }

        $db->transComplete();

        return $db->transStatus() ? $insertId : false;
    }

    protected function normalizeInsertPayload(array $event): array
    {
        $data = $event['data'] ?? null;
        if (!is_array($data)) {
            return $event;
        }

        $event['data'] = $this->normalizarPayloadParaSchema($data);
        return $event;
    }

    private function normalizarPayloadParaSchema(array $data): array
    {
        unset($data['_meta']);

        if ($this->usaSchemaLegado()) {
            $payload = [
                'individuo' => (string) ($data['individuo'] ?? $data['usuario_id'] ?? ($_SESSION['usuario'] ?? '')),
                'id_item' => $this->stringify($data['id_item'] ?? ''),
                'antes' => $this->encodeLegacy($data['antes'] ?? ''),
                'data_add' => $this->encodeLegacy($data['data_add'] ?? date('d/m/Y H:i:s')),
                'depois' => $this->encodeLegacy($data['depois'] ?? ''),
                'info_mais' => $this->encodeLegacy($data['info_mais'] ?? ''),
                'item' => $this->encodeLegacy($data['item'] ?? ''),
            ];

            return $this->filtrarPayload($payload);
        }

        $payload = [
            'usuario_id' => (string) ($data['usuario_id'] ?? $data['individuo'] ?? ($_SESSION['usuario'] ?? '')),
            'item' => $this->stringify($data['item'] ?? ''),
            'id_item' => $this->stringify($data['id_item'] ?? ''),
            'data_add' => $this->stringify($data['data_add'] ?? date('Y-m-d H:i:s')),
        ];

        return $this->filtrarPayload($payload);
    }

    private function filtrarPayload(array $payload): array
    {
        $camposPermitidos = array_fill_keys($this->getTableColumns(), true);
        $filtrado = [];
        foreach ($payload as $chave => $valor) {
            if (!isset($camposPermitidos[$chave])) {
                continue;
            }
            $filtrado[$chave] = $valor;
        }

        return $filtrado;
    }

    private function normalizarDetalhes(array $detalhes): array
    {
        $normalizados = [];
        foreach ($detalhes as $detalhe) {
            if (!is_array($detalhe)) {
                continue;
            }

            $campo = trim((string) ($detalhe['campo'] ?? ''));
            if ($campo === '') {
                $campo = 'campo';
            }

            $normalizados[] = [
                'campo' => $campo,
                'valor_antes' => $this->stringify($detalhe['valor_antes'] ?? ''),
                'valor_depois' => $this->stringify($detalhe['valor_depois'] ?? ''),
            ];
        }

        return $normalizados;
    }

    private function resumirDetalhes(array $detalhes, string $chave): string
    {
        $partes = [];
        foreach ($detalhes as $detalhe) {
            $campo = (string) ($detalhe['campo'] ?? 'campo');
            $valor = trim((string) ($detalhe[$chave] ?? ''));
            $partes[] = $campo . '=' . $valor;
        }

        return $this->truncate(implode(' | ', $partes), 255);
    }

    private function montarInfoMaisLegado(string $infoMais, array $detalhes, array $metaAuditoria): string
    {
        $partes = [];

        $infoMais = trim($infoMais);
        if ($infoMais !== '') {
            $partes[] = $infoMais;
        }

        if ($detalhes !== []) {
            $campos = array_map(static fn ($detalhe) => (string) ($detalhe['campo'] ?? ''), $detalhes);
            $campos = array_values(array_unique(array_filter($campos, static fn ($campo) => $campo !== '')));
            if ($campos !== []) {
                $partes[] = 'campos=' . implode(',', $campos);
            }
        }

        foreach (['acao', 'ip', 'ip_forwarded', 'mac_cliente', 'uri'] as $chave) {
            $valor = trim((string) ($metaAuditoria[$chave] ?? ''));
            if ($valor === '') {
                continue;
            }
            $partes[] = $chave . '=' . $valor;
        }

        return $this->truncate(implode(' | ', $partes), 255);
    }

    private function coletarMetaAuditoria(array $metaExtra = []): array
    {
        $request = service('request');
        $meta = [];

        if ($request !== null) {
            $ipForwarded = trim((string) $request->getHeaderLine('X-Forwarded-For'));
            $ip = trim((string) $request->getIPAddress());
            if ($ip === '' && isset($_SERVER['REMOTE_ADDR'])) {
                $ip = trim((string) $_SERVER['REMOTE_ADDR']);
            }

            $uri = '';
            if (method_exists($request, 'getUri')) {
                $requestUri = $request->getUri();
                if (is_object($requestUri)) {
                    $uri = trim((string) $requestUri);
                    if ($uri === '' && method_exists($requestUri, 'getPath')) {
                        $uri = trim((string) $requestUri->getPath());
                    }
                }
            }
            if ($uri === '' && isset($_SERVER['REQUEST_URI'])) {
                $uri = trim((string) $_SERVER['REQUEST_URI']);
            }

            $meta = [
                'ip' => $ip,
                'ip_forwarded' => $ipForwarded,
                'user_agent' => trim((string) $request->getUserAgent()),
                'metodo' => trim((string) $request->getMethod()),
                'uri' => $uri,
                'host' => trim((string) $request->getHeaderLine('Host')),
                'referer' => trim((string) $request->getHeaderLine('Referer')),
                'origem' => trim((string) $request->getHeaderLine('Origin')),
            ];
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            $meta['session_id'] = session_id();
            $meta['usuario_sessao_id'] = (string) ($_SESSION['usuario'] ?? '');
            $meta['usuario_sessao_nome'] = (string) ($_SESSION['usuario_nome'] ?? '');
        }

        $meta['mac_cliente'] = $this->resolverMacCliente((string) ($meta['ip'] ?? ''));

        foreach ($metaExtra as $chave => $valor) {
            if ($valor === null) {
                continue;
            }
            $meta[(string) $chave] = $valor;
        }

        return array_filter($meta, static fn ($valor) => $valor !== '' && $valor !== null);
    }

    private function resolverMacCliente(string $ip): string
    {
        $ip = trim($ip);
        if ($ip === '' || isset($this->macCache[$ip])) {
            return $this->macCache[$ip] ?? '';
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $this->macCache[$ip] = '';
            return '';
        }

        $shellExecDisabled = !function_exists('shell_exec')
            || stripos((string) ini_get('disable_functions'), 'shell_exec') !== false;
        if ($shellExecDisabled) {
            $this->macCache[$ip] = '';
            return '';
        }

        $comandos = DIRECTORY_SEPARATOR === '\\'
            ? ['arp -a ' . escapeshellarg($ip)]
            : ['ip neigh show ' . escapeshellarg($ip), 'arp -n ' . escapeshellarg($ip)];

        foreach ($comandos as $comando) {
            $saida = @shell_exec($comando . ' 2>&1');
            if (!is_string($saida) || $saida === '') {
                continue;
            }

            if (preg_match('/(?:[0-9A-Fa-f]{2}[:-]){5}[0-9A-Fa-f]{2}/', $saida, $matches) === 1) {
                $this->macCache[$ip] = strtoupper(str_replace('-', ':', $matches[0]));
                return $this->macCache[$ip];
            }
        }

        $this->macCache[$ip] = '';
        return '';
    }

    public function latestDetailValueByItem(string $item, string $campo = '', string $default = ''): string
    {
        $item = trim($item);
        if ($item === '') {
            return $default;
        }

        $alteracao = $this->where('item', $item)
            ->orderBy('id', 'DESC')
            ->first();

        if (!is_array($alteracao) || empty($alteracao['id'])) {
            return $default;
        }

        if ($this->detailsTableExists()) {
            $builder = $this->db->table('alteracoes_detalhes')
                ->select('valor_depois')
                ->where('alteracao_id', (int) $alteracao['id']);

            if ($campo !== '') {
                $builder->where('campo', $campo);
            }

            $detalhe = $builder
                ->orderBy('id', 'DESC')
                ->get(1)
                ->getRowArray();

            $valorDetalhe = trim((string) ($detalhe['valor_depois'] ?? ''));
            if ($valorDetalhe !== '') {
                return $valorDetalhe;
            }
        }

        foreach (['depois', 'info_mais'] as $colunaLegada) {
            $valorLegado = trim((string) ($alteracao[$colunaLegada] ?? ''));
            if ($valorLegado !== '') {
                return $valorLegado;
            }
        }

        return $default;
    }

    private function stringify($valor, int $limite = 4000): string
    {
        if (is_bool($valor)) {
            return $valor ? 'true' : 'false';
        }

        if (is_array($valor) || is_object($valor)) {
            $json = json_encode($valor, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            return $this->truncate($json !== false ? $json : print_r($valor, true), $limite);
        }

        return $this->truncate(trim((string) $valor), $limite);
    }

    private function encodeLegacy($valor): string
    {
        $texto = $this->stringify($valor, 255);
        return $texto === '' ? '' : Ferramentas::codificador($texto);
    }

    private function truncate(string $valor, int $limite): string
    {
        if ($limite <= 0) {
            return '';
        }

        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            return mb_strlen($valor) > $limite ? mb_substr($valor, 0, $limite) : $valor;
        }

        return strlen($valor) > $limite ? substr($valor, 0, $limite) : $valor;
    }

    private function getTableColumns(): array
    {
        if ($this->tableColumnsCache !== null) {
            return $this->tableColumnsCache;
        }

        if (!$this->db->tableExists($this->table)) {
            $this->tableColumnsCache = [];
            return $this->tableColumnsCache;
        }

        $this->tableColumnsCache = $this->db->getFieldNames($this->table);
        return $this->tableColumnsCache;
    }

    private function usaSchemaLegado(): bool
    {
        return in_array('individuo', $this->getTableColumns(), true);
    }

    private function detailsTableExists(): bool
    {
        if ($this->detailsTableExistsCache !== null) {
            return $this->detailsTableExistsCache;
        }

        $this->detailsTableExistsCache = $this->db->tableExists('alteracoes_detalhes');
        return $this->detailsTableExistsCache;
    }
}
