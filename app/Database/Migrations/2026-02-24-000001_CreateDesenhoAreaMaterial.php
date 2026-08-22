<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDesenhoAreaMaterial extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('desenho_area_material')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'desenho_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'arquivo_ext' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => false,
                'default' => 'dxf',
            ],
            'area_m2' => [
                'type' => 'DECIMAL',
                'constraint' => '16,6',
                'null' => false,
                'default' => 0,
            ],
            'margem_percentual' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => false,
                'default' => 10.00,
            ],
            'area_m2_com_margem' => [
                'type' => 'DECIMAL',
                'constraint' => '16,6',
                'null' => false,
                'default' => 0,
            ],
            'fonte_calculo' => [
                'type' => 'VARCHAR',
                'constraint' => 40,
                'null' => false,
                'default' => 'dxf_entities',
            ],
            'data_add' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'data_up' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('desenho_id');
        $this->forge->addUniqueKey('desenho_id', 'ux_desenho_area_material_desenho');
        $this->forge->createTable('desenho_area_material', true);
    }

    public function down()
    {
        $this->forge->dropTable('desenho_area_material', true);
    }
}

