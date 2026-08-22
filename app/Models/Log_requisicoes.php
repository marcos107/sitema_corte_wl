<?php

namespace App\Models;

use CodeIgniter\Model;

class Log_requisicoes extends Model {
    protected $table = 'log_requisicoes';
    protected $primarykey = 'id';
    protected $allowedFields = ['usuario_id', 'acao', 'metodo', 'status_execucao', 'mensagem', 'ip_origem', 'data_add'];
    protected $returnType = 'array';
        
    protected $useTimestamps = true;
    protected $dateFormat     = 'datetime';
    protected $createdField   = 'data_add';
    protected $updatedField  = '';
}