<?php

namespace App\Models;

use CodeIgniter\Model;

class ArquivoMetricasMaterial extends Model
{
    protected $table = 'arquivo_metricas_material';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'entidade_tipo',
        'entidade_id',
        'processo_id',
        'tipo_arquivo',
        'metrica',
        'unidade',
        'valor_base',
        'margem_percentual',
        'valor_final',
        'fonte_calculo',
        'data_referencia',
        'data_add',
        'data_up',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'data_add';
    protected $updatedField = 'data_up';
}

