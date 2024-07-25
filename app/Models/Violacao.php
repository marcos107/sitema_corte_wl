<?php

namespace App\Models;

use CodeIgniter\Model;

class Violacao extends Model {
    protected $table = 'violacao';
    protected $primarykey = 'id';
    protected $allowedFields = ['individuo','causa','data'];
    protected $returnType = 'array';
}