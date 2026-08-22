<?php

namespace App\Models;

use CodeIgniter\Model;

class Alteracoes_detalhes extends Model {
    protected $table = 'alteracoes_detalhes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['alteracao_id', 'campo', 'valor_antes', 'valor_depois', 'data_add'];
    protected $returnType = 'array';

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'data_add';
    protected $updatedField = '';

    protected function initialize()
    {
        if ($this->db === null || !$this->db->tableExists($this->table)) {
            return;
        }

        $campos = $this->db->getFieldNames($this->table);
        if (!in_array('data_add', $campos, true)) {
            $this->useTimestamps = false;
            $this->createdField = '';
            $this->allowedFields = array_values(array_filter(
                $this->allowedFields,
                static fn (string $campo): bool => $campo !== 'data_add'
            ));
        }
    }
}
