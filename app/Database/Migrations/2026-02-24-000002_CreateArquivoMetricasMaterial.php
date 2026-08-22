<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateArquivoMetricasMaterial extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('arquivo_metricas_material')) {
            $this->forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'entidade_tipo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 30,
                    'null' => false,
                    'default' => 'desenho',
                ],
                'entidade_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'processo_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                ],
                'tipo_arquivo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'null' => false,
                    'default' => 'dxf',
                ],
                'metrica' => [
                    'type' => 'VARCHAR',
                    'constraint' => 40,
                    'null' => false,
                    'default' => 'area_m2',
                ],
                'unidade' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'null' => false,
                    'default' => 'm2',
                ],
                'valor_base' => [
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
                'valor_final' => [
                    'type' => 'DECIMAL',
                    'constraint' => '16,6',
                    'null' => false,
                    'default' => 0,
                ],
                'fonte_calculo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 60,
                    'null' => false,
                    'default' => 'dxf_entities',
                ],
                'data_referencia' => [
                    'type' => 'DATETIME',
                    'null' => true,
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
            $this->forge->addKey('entidade_id');
            $this->forge->addKey('processo_id');
            $this->forge->addKey('tipo_arquivo');
            $this->forge->addKey('metrica');
            $this->forge->addUniqueKey(
                ['entidade_tipo', 'entidade_id', 'tipo_arquivo', 'metrica'],
                'ux_arquivo_metricas_entidade'
            );
            $this->forge->createTable('arquivo_metricas_material', true);
        }

        // Migra dados legados, se existir tabela antiga.
        if ($this->db->tableExists('desenho_area_material')) {
            $sql = "
                INSERT INTO arquivo_metricas_material
                    (entidade_tipo, entidade_id, processo_id, tipo_arquivo, metrica, unidade, valor_base, margem_percentual, valor_final, fonte_calculo, data_referencia, data_add, data_up)
                SELECT
                    'desenho' AS entidade_tipo,
                    dam.desenho_id AS entidade_id,
                    NULL AS processo_id,
                    dam.arquivo_ext AS tipo_arquivo,
                    'area_m2' AS metrica,
                    'm2' AS unidade,
                    dam.area_m2 AS valor_base,
                    dam.margem_percentual AS margem_percentual,
                    dam.area_m2_com_margem AS valor_final,
                    dam.fonte_calculo AS fonte_calculo,
                    dam.data_add AS data_referencia,
                    dam.data_add AS data_add,
                    dam.data_up AS data_up
                FROM desenho_area_material dam
                LEFT JOIN arquivo_metricas_material amm
                    ON amm.entidade_tipo = 'desenho'
                    AND amm.entidade_id = dam.desenho_id
                    AND amm.tipo_arquivo = dam.arquivo_ext
                    AND amm.metrica = 'area_m2'
                WHERE amm.id IS NULL
            ";
            $this->db->query($sql);
        }
    }

    public function down()
    {
        $this->forge->dropTable('arquivo_metricas_material', true);
    }
}

