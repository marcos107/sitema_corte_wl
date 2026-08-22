<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTelaInicialToNivel extends Migration
{
    private function campoAfterDisponivel(array $camposPreferidos): ?string
    {
        foreach ($camposPreferidos as $campo) {
            if ($this->db->fieldExists($campo, 'nivel')) {
                return $campo;
            }
        }

        return null;
    }

    public function up()
    {
        if (!$this->db->tableExists('nivel')) {
            return;
        }

        $fields = [];
        $afterBase = $this->campoAfterDisponivel(['processos', 'permissao', 'status', 'nome']);

        if (!$this->db->fieldExists('relatorio', 'nivel')) {
            $campoRelatorio = [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ];

            if ($afterBase !== null) {
                $campoRelatorio['after'] = $afterBase;
            }

            $fields['relatorio'] = $campoRelatorio;
        }

        if (!$this->db->fieldExists('tela_inicial', 'nivel')) {
            $campoTelaInicial = [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
                'default' => '',
            ];

            $afterTelaInicial = array_key_exists('relatorio', $fields)
                ? 'relatorio'
                : $afterBase;

            if ($afterTelaInicial !== null) {
                $campoTelaInicial['after'] = $afterTelaInicial;
            }

            $fields['tela_inicial'] = $campoTelaInicial;
        }

        if (!empty($fields)) {
            $this->forge->addColumn('nivel', $fields);
        }
    }

    public function down()
    {
        if (!$this->db->tableExists('nivel')) {
            return;
        }

        if ($this->db->fieldExists('tela_inicial', 'nivel')) {
            $this->forge->dropColumn('nivel', 'tela_inicial');
        }

        if ($this->db->fieldExists('relatorio', 'nivel')) {
            $this->forge->dropColumn('nivel', 'relatorio');
        }
    }
}
