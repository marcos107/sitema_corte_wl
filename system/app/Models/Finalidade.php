<?php

namespace App\Models;

use CodeIgniter\Model;

class Finalidade extends Model {
    protected $table = 'finalidade';
    protected $primarykey = 'id';
    protected $allowedFields = ['nome','status','individuo','data_hora_add'];
    protected $returnType = 'array';
}