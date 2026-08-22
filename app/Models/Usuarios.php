<?php

namespace App\Models;

use CodeIgniter\Model;

class Usuarios extends Model {
    protected $table = 'usuarios';
    protected $primarykey = 'id';
    protected $allowedFields = ['usuario_id', 'nivel_id', 'nome', 'senha', 'status', 'whatsapp', 'email', 'acesso_remoto', 'data_add'];
    protected $returnType = 'array';
        
    protected $useTimestamps = true;
    protected $dateFormat     = 'datetime';
    protected $createdField   = 'data_add';
    protected $updatedField  = '';
}