<?php

namespace App\Models;

use CodeIgniter\Model;

class Empreendimentos extends Model {
    protected $table = 'empreendimentos';
    protected $primarykey = 'id';
    protected $allowedFields = ['empresa_id', 'usuario_id', 'nome', 'escala', 'status', 'data_add'];
    protected $returnType = 'array';
        
    protected $useTimestamps = true;
    protected $dateFormat     = 'datetime';
    protected $createdField   = 'data_add';
    protected $updatedField  = '';
}
