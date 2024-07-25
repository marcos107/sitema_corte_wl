<?php

namespace App\Models;

use CodeIgniter\Model;

class Funcao extends Model {
    protected $table = 'funcao';
    protected $primarykey = 'id';
    protected $allowedFields = ['nome','data_add','individuo'];
    protected $returnType = 'array';
}