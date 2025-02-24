<?php

namespace App\Models;

use CodeIgniter\Model;

class Recolocar_desenho extends Model {
    protected $table = 'recolocar_desenho';
    protected $primarykey = 'id';
    protected $allowedFields = ['desenho','individuo','responsavel','status','data_add','quantidade','id_anterior','data_fim'];
    protected $returnType = 'array';
}