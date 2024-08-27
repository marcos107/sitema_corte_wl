<?php

namespace App\Models;

use CodeIgniter\Model;

class Desenhos extends Model {
    protected $table = 'desenhos';
    protected $primarykey = 'id';
    protected $allowedFields = ['nome','caminho','desenhista','status','prioridade','finalidade','empreendimento','empresa','cortador','data_hora_add','processos_id'];
    protected $returnType = 'array';
}