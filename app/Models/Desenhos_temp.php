<?php

namespace App\Models;

use CodeIgniter\Model;

class Desenhos_temp extends Model {
    protected $table = 'desenhos_temp';
    protected $primarykey = 'id';
    protected $allowedFields = ['diretorio','individuo','data_add','data_finalizado','status','destino'];
    protected $returnType = 'array';
}