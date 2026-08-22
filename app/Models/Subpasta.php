<?php

namespace App\Models;

use CodeIgniter\Model;

class Subpasta extends Model {
    protected $table = 'subpasta';
    protected $primarykey = 'id';
    protected $allowedFields = ['usuario_id', 'empreendimentos_id', 'finalidade_id', 'nome', 'status', 'data_add'];
    protected $returnType = 'array';
        
    protected $useTimestamps = true;
    protected $dateFormat     = 'datetime';
    protected $createdField   = 'data_add';
    protected $updatedField  = '';
}