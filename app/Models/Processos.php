<?php

namespace App\Models;

use CodeIgniter\Model;

class Processos extends Model {
    protected $table = 'processos';
    protected $primarykey = 'id';
    protected $allowedFields = ['nome','filtros_id','status','data_hora_add','responsavel','diretorio'];
    protected $returnType = 'array';
}