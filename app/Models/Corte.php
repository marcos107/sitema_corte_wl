<?php

namespace App\Models;

use CodeIgniter\Model;

class Corte extends Model {
    protected $table = 'corte';
    protected $primarykey = 'id';
    protected $allowedFields = ['usuario_id_ini', 'usuario_id_fim', 'data_add', 'status', 'ip', 'data_end'];
    protected $returnType = 'array';
        
    protected $useTimestamps = true;
    protected $dateFormat     = 'datetime';
    protected $createdField   = 'data_add';
    protected $updatedField  = '';
}