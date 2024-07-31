<?php

namespace App\Models;

use CodeIgniter\Model;

class Usuarios extends Model {
    protected $table = 'usuarios';
    protected $primarykey = 'id';
    protected $allowedFields = ['nome','senha','nivel','status','individuo','data_hora_add','whatsapp','email'];
    protected $returnType = 'array';
}