<?php

namespace App\Models;

use CodeIgniter\Model;

class Recolocar_desenho extends Model {
    protected $table = 'recolocar_desenho';
    protected $primarykey = 'id';
    protected $allowedFields = ['usuario_id_pedido', 'usuario_id_confirmado', 'recolocar_desenho_id_anterior', 'desenhos_id', 'status', 'quantidade', 'data_add', 'data_end'];
    protected $returnType = 'array';
        
    protected $useTimestamps = true;
    protected $dateFormat     = 'datetime';
    protected $createdField   = 'data_add';
    protected $updatedField  = 'data_end';
}