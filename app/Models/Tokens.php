<?php

namespace App\Models;

use CodeIgniter\Model;

class Tokens extends Model {
    protected $table = 'tokens';
    protected $primarykey = 'id';
    protected $allowedFields = ['code','loca','data_hora_add'];
    protected $returnType = 'array';
}