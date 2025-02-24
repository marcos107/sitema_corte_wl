<?php

namespace App\Models;

use CodeIgniter\Model;

class Filtros extends Model {
    protected $table = 'filtros';
    protected $primarykey = 'id';
    protected $allowedFields = ['nome','status','individuo','data_hora_add'];
    protected $returnType = 'array';
}