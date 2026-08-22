<?php

namespace App\Models;

use CodeIgniter\Model;

class Violacao extends Model {
    protected $table = 'violacao';
    protected $primarykey = 'id';
    protected $allowedFields = ['usuario_id','causa','data_add'];
    protected $returnType = 'array';
        
    protected $useTimestamps = true;
    protected $dateFormat     = 'datetime';
    protected $createdField   = 'data_add';
    protected $updatedField  = '';
}