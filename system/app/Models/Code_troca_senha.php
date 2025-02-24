<?php

namespace App\Models;

use CodeIgniter\Model;

class Code_troca_senha extends Model {
    protected $table = 'code_troca_senha';
    protected $primarykey = 'id';
    protected $allowedFields = ['code','data_hora_add','status','data_hora_finalizado','user'];
    protected $returnType = 'array';
}