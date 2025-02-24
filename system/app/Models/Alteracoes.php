<?php

namespace App\Models;

use CodeIgniter\Model;

class Alteracoes extends Model {
    protected $table = 'alteracoes';
    protected $primarykey = 'id';
    protected $allowedFields = ['item','id_item','antes','individuo','data_add','depois','info_mais'];
    protected $returnType = 'array';
}