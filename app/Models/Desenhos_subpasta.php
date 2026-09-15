<?php

namespace App\Models;

use CodeIgniter\Model;

class Desenhos_subpasta extends Model
{
    protected $table = 'desenhos_subpasta';
    protected $primaryKey = 'id';
    protected $allowedFields = ['desenho_id', 'tag_id'];
    protected $returnType = 'array';
}
