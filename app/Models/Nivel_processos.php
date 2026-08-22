<?php

namespace App\Models;

use CodeIgniter\Model;

class Nivel_processos extends Model {
    protected $table = 'nivel_processos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nivel_id', 'processo_id'];
    protected $returnType = 'array';
}
