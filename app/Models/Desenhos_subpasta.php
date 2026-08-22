<?php

namespace App\Models;

use CodeIgniter\Model;

class Desenhos_subpast extends Model {
    protected $table = 'desenhos_subpasta';
    protected $primarykey = 'id';
    protected $allowedFields = ['desenho_id', 'tag_id'];
    protected $returnType = 'array';
}