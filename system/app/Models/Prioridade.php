<?php

namespace App\Models;

use CodeIgniter\Model;

class Prioridade extends Model {
    protected $table = 'prioridade';
    protected $primarykey = 'id';
    protected $allowedFields = ['nome','status','individuo','data_hora_add','cor','ordem'];
    protected $returnType = 'array';
}