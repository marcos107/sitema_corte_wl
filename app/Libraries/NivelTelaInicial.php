<?php

namespace App\Libraries;

class NivelTelaInicial
{
    public static function definicoes(): array
    {
        return [
            [
                'key' => 'desenho_adicionar',
                'label' => 'Adicionar desenho',
                'aliases' => ['Adicionar'],
            ],
            [
                'key' => 'painel_tarefas:meus_desenhos',
                'label' => 'Painel - Meus desenhos',
                'aliases' => ['Meus Desenhos', 'Meus_Desenhos', 'meus_desenhos', 'desenho_meus'],
            ],
            [
                'key' => 'painel_tarefas:lista_tarefas',
                'label' => 'Painel - Lista de tarefas',
                'aliases' => ['Lista De Corte', 'Lista_De_Corte', 'lista_corte', 'lista_tarefas', 'Lista De Corte Cortador', 'Lista_De_Corte_Cortador', 'lista_de_corte_cortador'],
            ],
            [
                'key' => 'painel_tarefas:lista_tarefas_adm',
                'label' => 'Painel - Lista de tarefas ADM',
                'aliases' => ['Lista De Corte ADM', 'Lista_De_Corte_ADM', 'Lista_De_Corte ADM', 'lista_corte_adm', 'lista_tarefas_adm'],
            ],
            [
                'key' => 'painel_tarefas:tarefas_concluidas',
                'label' => 'Painel - Tarefas concluidas',
                'aliases' => ['Lista De Corte', 'Lista_De_Corte', 'lista_corte', 'lista_tarefas', 'Lista De Corte Cortador', 'Lista_De_Corte_Cortador', 'lista_de_corte_cortador', 'Lista De Corte ADM', 'Lista_De_Corte_ADM', 'Lista_De_Corte ADM', 'lista_corte_adm', 'lista_tarefas_adm'],
            ],
            [
                'key' => 'subpasta',
                'label' => 'Subpastas',
                'aliases' => ['Subpasta'],
            ],
            [
                'key' => 'tipo_de_arquivo',
                'label' => 'Tipo de arquivo',
                'aliases' => ['Tipo De Arquivo', 'Tipo_De_Arquivo', 'tipo_de_arquivo'],
            ],
            [
                'key' => 'prioridade',
                'label' => 'Prioridade',
                'aliases' => ['Prioridade', 'prioridade'],
            ],
            [
                'key' => 'finalidade',
                'label' => 'Finalidade',
                'aliases' => ['Fialidade', 'Finalidade', 'finalidade'],
            ],
            [
                'key' => 'empresa',
                'label' => 'Empresa/Cliente',
                'aliases' => ['Empresa', 'empresa'],
            ],
            [
                'key' => 'empreendimento',
                'label' => 'Empreendimento',
                'aliases' => ['Empreendimento', 'empreendimento'],
            ],
            [
                'key' => 'nivel',
                'label' => 'Nivel',
                'aliases' => ['NÃ­vel', 'Nivel', 'nivel'],
            ],
            [
                'key' => 'usuario',
                'label' => 'Usuario',
                'aliases' => ['Usuario', 'usuario', 'user_cadastrar'],
            ],
            [
                'key' => 'relatorios',
                'label' => 'Relatorios',
                'aliases' => ['RelÃ¡torio', 'Relatorio', 'relatorios', 'relatorio'],
            ],
            [
                'key' => 'logs_alteracoes',
                'label' => 'Logs de alteracoes',
                'aliases' => ['Logs', 'logs', 'logs_alteracoes', 'alteracoes'],
            ],
            [
                'key' => 'processos',
                'label' => 'Processos',
                'aliases' => ['Processos', 'processos'],
            ],
        ];
    }

    public static function opcoes(array $permissoes = []): array
    {
        $opcoes = ['' => 'Automatica'];

        foreach (self::definicoes() as $definicao) {
            if (!self::permitida($definicao['key'], $permissoes)) {
                continue;
            }

            $opcoes[$definicao['key']] = $definicao['label'];
        }

        return $opcoes;
    }

    public static function rotulo(?string $key): string
    {
        $key = trim((string) $key);
        if ($key === '') {
            return 'Automatica';
        }

        foreach (self::definicoes() as $definicao) {
            if ($definicao['key'] === $key) {
                return $definicao['label'];
            }
        }

        return $key;
    }

    public static function permitida(?string $key, array $permissoes): bool
    {
        $key = trim((string) $key);
        if ($key === '') {
            return true;
        }

        $permissoes = self::normalizarPermissoes($permissoes);
        if (in_array('all', $permissoes, true)) {
            return true;
        }

        foreach (self::definicoes() as $definicao) {
            if ($definicao['key'] !== $key) {
                continue;
            }

            foreach ($definicao['aliases'] as $alias) {
                if (in_array(self::normalizarToken($alias), $permissoes, true)) {
                    return true;
                }
            }

            return false;
        }

        return false;
    }

    public static function abaPainel(?string $key): ?string
    {
        $key = trim((string) $key);
        if (strpos($key, 'painel_tarefas:') !== 0) {
            return null;
        }

        return substr($key, strlen('painel_tarefas:')) ?: null;
    }

    public static function normalizarPermissoes(array $permissoes): array
    {
        return array_values(array_unique(array_filter(array_map([self::class, 'normalizarToken'], $permissoes), static fn ($valor): bool => $valor !== '')));
    }

    private static function normalizarToken(string $valor): string
    {
        $valor = strtolower(trim($valor));
        if ($valor === '') {
            return '';
        }

        $transliterado = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $valor);
        if ($transliterado !== false && $transliterado !== '') {
            $valor = strtolower(trim((string) $transliterado));
        }

        $valor = preg_replace('/[^a-z0-9]+/', '_', $valor) ?? $valor;
        $valor = trim($valor, '_');

        $aliasesEspeciais = [
            'n_vel' => 'nivel',
            'relat_rio' => 'relatorio',
            'rel_torio' => 'relatorio',
        ];

        if (array_key_exists($valor, $aliasesEspeciais)) {
            return $aliasesEspeciais[$valor];
        }

        return $valor;
    }
}
