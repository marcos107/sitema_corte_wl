<?php

namespace App\Models;

use CodeIgniter\Model;

class Tokens extends Model {
    protected $table = 'tokens';
    protected $primarykey = 'id';
    protected $allowedFields = ['code','local','data_hora_add','status','individuo','data_hora_user'];
    protected $returnType = 'array';
}