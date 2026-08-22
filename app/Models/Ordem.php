<?php

namespace App\Models;

use CodeIgniter\Model;

class Ordem extends Model {
    protected $table = 'ordem';
    protected $primarykey = 'id';
    protected $allowedFields = ['desenho_id', 'projeto_id', 'prioridade_id', 'ordem','processos_id', 'status', 'data_add'];
    protected $returnType = 'array';
        
    protected $useTimestamps = true;
    protected $dateFormat     = 'datetime';
    protected $createdField   = 'data_add';
    protected $updatedField  = '';
}