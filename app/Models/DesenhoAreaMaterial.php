<?php

namespace App\Models;

use CodeIgniter\Model;

class DesenhoAreaMaterial extends Model
{
    protected $table = 'desenho_area_material';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'desenho_id',
        'arquivo_ext',
        'area_m2',
        'margem_percentual',
        'area_m2_com_margem',
        'fonte_calculo',
        'data_add',
        'data_up',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'data_add';
    protected $updatedField = 'data_up';
}

