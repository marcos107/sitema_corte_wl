<?php

namespace App\Models;

use CodeIgniter\Model;

class Historico_desenhos extends Model {
    protected $table = 'Historico_desenhos';
    protected $primarykey = 'id';
    protected $allowedFields = ['nome','status','id_desenhos','data_hora_add','data_hora_mod','individuo'];
    protected $returnType = 'array';
}