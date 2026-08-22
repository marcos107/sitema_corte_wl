<?php

namespace App\Models;

use CodeIgniter\Model;

class Processos_filtro extends Model {
    protected $table = 'processos_filtro';
    protected $primarykey = 'id';
    protected $allowedFields = ['usuario_id','processos_id', 'filtros_id', 'status',  'data_add'];
    protected $returnType = 'array';
        
    protected $useTimestamps = true;
    protected $dateFormat     = 'datetime';
    protected $createdField   = 'data_add';
    protected $updatedField  = '';
}