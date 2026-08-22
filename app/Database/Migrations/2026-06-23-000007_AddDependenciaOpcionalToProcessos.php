<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDependenciaOpcionalToProcessos extends Migration
{
    private function campoAfterDisponivel(array $camposPreferidos): ?string
    {
        foreach ($camposPreferidos as $campo) {
            if ($this->db->fieldExists($campo, 'processos')) {
                return $campo;
            }
        }

        return null;
    }

    public function up()
    {
        if (!$this->db->tableExists('processos')) {
            return;
        }

        if (!$this->db->fieldExists('dependencia_obrigatoria', 'processos')) {
            $campoObrigatoria = [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ];

            $after = $this->campoAfterDisponivel(['processos_id_proximo', 'input', 'diretorio']);
            if ($after !== null) {
                $campoObrigatoria['after'] = $after;
            }

            $this->forge->addColumn('processos', [
                'dependencia_obrigatoria' => $campoObrigatoria,
            ]);
        }

        if (!$this->db->fieldExists('dependencia_finalidades_opcionais', 'processos')) {
            $campoFinalidades = [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
                'default' => '',
            ];

            $after = $this->campoAfterDisponivel(['dependencia_obrigatoria', 'processos_id_proximo', 'input']);
            if ($after !== null) {
                $campoFinalidades['after'] = $after;
            }

            $this->forge->addColumn('processos', [
                'dependencia_finalidades_opcionais' => $campoFinalidades,
            ]);
        }
    }

    public function down()
    {
        if (!$this->db->tableExists('processos')) {
            return;
        }

        if ($this->db->fieldExists('dependencia_finalidades_opcionais', 'processos')) {
            $this->forge->dropColumn('processos', 'dependencia_finalidades_opcionais');
        }

        if ($this->db->fieldExists('dependencia_obrigatoria', 'processos')) {
            $this->forge->dropColumn('processos', 'dependencia_obrigatoria');
        }
    }
}
