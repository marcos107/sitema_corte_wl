<?php

namespace App\Models;

use CodeIgniter\Model;

class Tag extends Model {
    protected $table = 'tag';
    protected $primarykey = 'id';
    protected $allowedFields = ['nome','status','responsavel','data_add','d_responsavel','data_apagado','empreendimento_id','finalidade_id'];
    protected $returnType = 'array';
}