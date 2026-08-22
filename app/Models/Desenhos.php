<?php

namespace App\Models;

use CodeIgniter\Model;

class Desenhos extends Model {
    protected $table = 'desenhos';
    protected $primarykey = 'id';
    protected $allowedFields = ['corte_id', 'usuario_id_desenhista', 'prioridade_id', 'finalidade_id', 'empreendimentos_id', 'empresa_id', 'processos_id', 'nome', 'diretorio', 'status', 'data_add'];
    protected $returnType = 'array';
        
    protected $useTimestamps = true;
    protected $dateFormat     = 'datetime';
    protected $createdField   = 'data_add';
    protected $updatedField  = '';
}