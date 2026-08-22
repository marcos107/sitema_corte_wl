<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEscalaToEmpreendimentos extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('empreendimentos') || $this->db->fieldExists('escala', 'empreendimentos')) {
            return;
        }

        $campoEscala = [
            'type' => 'VARCHAR',
            'constraint' => 15,
            'null' => true,
        ];

        if ($this->db->fieldExists('nome', 'empreendimentos')) {
            $campoEscala['after'] = 'nome';
        }

        $this->forge->addColumn('empreendimentos', [
            'escala' => $campoEscala,
        ]);
    }

    public function down()
    {
        if (!$this->db->tableExists('empreendimentos') || !$this->db->fieldExists('escala', 'empreendimentos')) {
            return;
        }

        $this->forge->dropColumn('empreendimentos', 'escala');
    }
}
