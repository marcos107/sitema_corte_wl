<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAlteracoesDetalhes extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('alteracoes_detalhes')) {
            $this->forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'alteracao_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'campo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 120,
                    'null' => false,
                ],
                'valor_antes' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'valor_depois' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'data_add' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->addKey('alteracao_id');
            $this->forge->addKey('campo');
            $this->forge->createTable('alteracoes_detalhes', true);
        }

        if ($this->db->tableExists('alteracoes') && $this->db->fieldExists('info_mais', 'alteracoes')) {
            $this->forge->modifyColumn('alteracoes', [
                'info_mais' => [
                    'name' => 'info_mais',
                    'type' => 'TEXT',
                    'null' => false,
                ],
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('alteracoes_detalhes', true);

        if ($this->db->tableExists('alteracoes') && $this->db->fieldExists('info_mais', 'alteracoes')) {
            $this->forge->modifyColumn('alteracoes', [
                'info_mais' => [
                    'name' => 'info_mais',
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => false,
                ],
            ]);
        }
    }
}
