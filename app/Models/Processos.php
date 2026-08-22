<?php

namespace App\Models;

use CodeIgniter\Model;

class Processos extends Model {
    protected $table = 'processos';
    protected $primarykey = 'id';
    protected $allowedFields = ['usuario_id', 'filtros_id', 'nome', 'status', 'diretorio','input', 'data_add','processos_id_proximo', 'dependencia_obrigatoria', 'dependencia_finalidades_opcionais'];
    protected $returnType = 'array';
        
    protected $useTimestamps = true;
    protected $dateFormat     = 'datetime';
    protected $createdField   = 'data_add';
    protected $updatedField  = '';
}
