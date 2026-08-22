<?php

namespace App\Models;

use CodeIgniter\Model;

class Projeto extends Model
{
    protected $table            = 'projeto';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['usuario_id', 'descricao','diretorio', 'status', 'data_add'];

    protected $useTimestamps = true;
    protected $dateFormat     = 'datetime';
    protected $createdField   = 'data_add';
    protected $updatedField  = '';
}
