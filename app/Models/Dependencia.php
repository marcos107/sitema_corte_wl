<?php

namespace App\Models;

use CodeIgniter\Model;

class Dependencia extends Model {
    protected $table = 'dependencia';
    protected $primarykey = 'id';
    protected $allowedFields = ['desenhos_id', 'projeto_id', 'desenhos_id_dependente', 'projeto_id_dependente', 'data_add'];
    protected $returnType = 'array';
        
    protected $useTimestamps = true;
    protected $dateFormat     = 'datetime';
    protected $createdField   = 'data_add';
    protected $updatedField  = '';
}