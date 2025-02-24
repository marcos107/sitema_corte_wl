<?php

namespace App\Models;

use CodeIgniter\Model;

class Empreendimentos extends Model {
    protected $table = 'empreendimentos';
    protected $primarykey = 'id';
    protected $allowedFields = ['nome','empresa_id','status','individuo','data_hora_add'];
    protected $returnType = 'array';
}