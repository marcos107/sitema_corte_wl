<?php

namespace App\Models;

use CodeIgniter\Model;

class Nivel_permissoes extends Model
{
    protected $table = 'nivel_permissoes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['usuario_id', 'nivel_id', 'permissao', 'status'];
    protected $returnType = 'array';

}