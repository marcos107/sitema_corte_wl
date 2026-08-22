<?php

namespace App\Models;

use CodeIgniter\Model;

class Empresa extends Model {
    protected $table = 'empresa';
    protected $primarykey = 'id';
    protected $allowedFields = ['usuario_id', 'nome', 'status', 'data_add'];
    protected $returnType = 'array';
        
    protected $useTimestamps = true;
    protected $dateFormat     = 'datetime';
    protected $createdField   = 'data_add';
    protected $updatedField  = '';
}