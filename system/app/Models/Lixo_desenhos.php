<?php

namespace App\Models;

use CodeIgniter\Model;

class Lixo_desenhos extends Model {
    protected $table = 'lixo_desenhos';
    protected $primarykey = 'id';
    protected $allowedFields = ['nome_desenho','caminho','id_desenho','data_add','individuo'];
    protected $returnType = 'array';
}