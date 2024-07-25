<?php

namespace App\Models;

use CodeIgniter\Model;

class Corte extends Model {
    protected $table = 'corte';
    protected $primarykey = 'id';
    protected $allowedFields = ['id_desenho','cortador','data_add','status','ip','data_fim','cortador_fim'];
    protected $returnType = 'array';
}