<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNivelAdicionalToNivel extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('nivel') || $this->db->fieldExists('nivel_adicional_id', 'nivel')) {
            return;
        }

        $this->forge->addColumn('nivel', [
            'nivel_adicional_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'tela_inicial',
            ],
        ]);
    }

    public function down()
    {
        if (!$this->db->tableExists('nivel') || !$this->db->fieldExists('nivel_adicional_id', 'nivel')) {
            return;
        }

        $this->forge->dropColumn('nivel', 'nivel_adicional_id');
    }
}
