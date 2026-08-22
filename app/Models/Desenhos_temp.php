<?php

namespace App\Models;

use CodeIgniter\Model;

class Desenhos_temp extends Model {
    protected $table = 'desenhos_temp';
    protected $primarykey = 'id';
    protected $allowedFields = ['usuario_id','diretorio','status','data_add','data_end'];
    protected $returnType = 'array';
        
    protected $useTimestamps  = true;
    protected $dateFormat     = 'datetime';
    protected $createdField   = 'data_add';
    protected $updatedField   = 'data_end';
}