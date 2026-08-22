<?php

namespace App\Models;

use CodeIgniter\Model;

class Projeto_desenho extends Model
{
    protected $table            = 'projeto_desenho';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['usuario_id', 'desenho_id', 'projeto_id', 'data_add','marcador'];

    protected $useTimestamps = true;
    protected $dateFormat     = 'datetime';
    protected $createdField   = 'data_add';
    protected $updatedField  = '';
}
