<?php

namespace App\Models;

use App\Controllers\Ferramentas;
use CodeIgniter\Model;

class Lixo_desenhos extends Model
{
    protected $table = 'lixo_desenhos';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'desenho_id',
        'usuario_id',
        'diretorio',
        'nome',
        'data_add',
        'id_desenho',
        'caminho',
        'nome_desenho',
        'individuo',
    ];
    protected $returnType = 'array';

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'data_add';
    protected $updatedField = '';
    protected $beforeInsert = ['normalizeInsertPayload'];

    private ?array $tableColumnsCache = null;

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
        if ($this->usaSchemaLegado()) {
            $payload = [
                'id_desenho' => (string) ($data['id_desenho'] ?? $data['desenho_id'] ?? ''),
                'caminho' => $this->encodeLegacy($data['caminho'] ?? $data['diretorio'] ?? ''),
                'nome_desenho' => $this->encodeLegacy($data['nome_desenho'] ?? $data['nome'] ?? ''),
                'data_add' => $this->encodeLegacy($data['data_add'] ?? date('d/m/Y H:i:s')),
                'individuo' => (string) ($data['individuo'] ?? $data['usuario_id'] ?? ($_SESSION['usuario'] ?? '')),
            ];

            return $this->filtrarPayload($payload);
        }

        $payload = [
            'desenho_id' => (string) ($data['desenho_id'] ?? $data['id_desenho'] ?? ''),
            'usuario_id' => (string) ($data['usuario_id'] ?? $data['individuo'] ?? ($_SESSION['usuario'] ?? '')),
            'diretorio' => $this->stringify($data['diretorio'] ?? $data['caminho'] ?? '', 1000),
            'nome' => $this->stringify($data['nome'] ?? $data['nome_desenho'] ?? '', 255),
            'data_add' => $this->stringify($data['data_add'] ?? date('Y-m-d H:i:s'), 255),
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

    private function encodeLegacy($valor): string
    {
        $texto = $this->stringify($valor, 1000);
        return $texto === '' ? '' : Ferramentas::codificador($texto);
    }

    private function stringify($valor, int $limite = 255): string
    {
        if (is_bool($valor)) {
            return $valor ? 'true' : 'false';
        }

        if (is_array($valor) || is_object($valor)) {
            $json = json_encode($valor, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $valor = $json !== false ? $json : print_r($valor, true);
        }

        $texto = trim((string) $valor);
        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            return mb_strlen($texto) > $limite ? mb_substr($texto, 0, $limite) : $texto;
        }

        return strlen($texto) > $limite ? substr($texto, 0, $limite) : $texto;
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
        return in_array('id_desenho', $this->getTableColumns(), true);
    }
}
