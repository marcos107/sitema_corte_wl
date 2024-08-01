<?php

namespace App\Models;

use CodeIgniter\Model;

class Nivel extends Model {
    protected $table = 'nivel';
    protected $primarykey = 'id';
    protected $allowedFields = ['nome','data_add','individuo','permissao','status','processos'];
    protected $returnType = 'array';
}