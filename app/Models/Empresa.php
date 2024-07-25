<?php

namespace App\Models;

use CodeIgniter\Model;

class Empresa extends Model {
    protected $table = 'empresa';
    protected $primarykey = 'id';
    protected $allowedFields = ['nome','status','individuo','data_hora_add'];
    protected $returnType = 'array';
}