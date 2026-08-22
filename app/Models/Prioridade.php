<?php

namespace App\Models;

use CodeIgniter\Model;

class Prioridade extends Model {
    protected $table = 'prioridade';
    protected $primarykey = 'id';
    protected $allowedFields = ['usuario_id', 'nome', 'status', 'cor', 'ordem', 'data_add', 'periodo'];
    protected $returnType = 'array';
        
    protected $useTimestamps = true;
    protected $dateFormat     = 'datetime';
    protected $createdField   = 'data_add';
    protected $updatedField  = '';
}