<?php

namespace App\Controllers;

class LogsPost extends Ferramentas
{
    private function iniciarSessaoSeNecessario(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    private function decodificarValor($valor): string
    {
        $valor = trim((string) ($valor ?? ''));
        if ($valor === '') {
            return '';
        }

        $decodificado = Ferramentas::decodificador($valor);
        return $decodificado !== '' ? $decodificado : $valor;
    }

    private function normalizarUsuarioNome($valor): string
    {
        $valor = trim((string) ($valor ?? ''));
        return $valor !== '' ? $valor : 'Sistema';
    }

    private function parseDateTime(?string $valor): ?\DateTimeImmutable
    {
        $valor = trim((string) $valor);
        if ($valor === '') {
            return null;
        }

        $formatos = [
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'd/m/Y H:i:s',
            'd/m/Y H:i',
            \DateTimeInterface::ATOM,
        ];

        foreach ($formatos as $formato) {
            $date = \DateTimeImmutable::createFromFormat($formato, $valor);
            if ($date instanceof \DateTimeImmutable) {
                return $date;
            }
        }

        $timestamp = strtotime($valor);
        if ($timestamp === false) {
            return null;
        }

        return (new \DateTimeImmutable())->setTimestamp($timestamp);
    }

    private function formatarDataAuditoria(?string $valor): array
    {
        $valor = $this->decodificarValor($valor);
        $date = $this->parseDateTime($valor);

        if (!$date instanceof \DateTimeImmutable) {
            return [
                'texto' => $valor,
                'ordem' => '',
            ];
        }

        return [
            'texto' => $date->format('d/m/Y H:i:s'),
            'ordem' => $date->format('YmdHis'),
        ];
    }

    private function truncarTexto(string $texto, int $limite = 160): string
    {
        if ($limite <= 0 || $texto === '') {
            return '';
        }

        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            return mb_strlen($texto) > $limite ? mb_substr($texto, 0, $limite - 1) . '...' : $texto;
        }

        return strlen($texto) > $limite ? substr($texto, 0, $limite - 1) . '...' : $texto;
    }

    private function extrairMetaInfoMais(string $infoMais): array
    {
        $meta = [];
        $partes = array_filter(array_map('trim', explode('|', $infoMais)), static fn ($parte): bool => $parte !== '');

        foreach ($partes as $indice => $parte) {
            if (strpos($parte, '=') !== false) {
                [$chave, $valor] = array_pad(explode('=', $parte, 2), 2, '');
                $chave = trim((string) $chave);
                $valor = trim((string) $valor);
                if ($chave !== '' && $valor !== '') {
                    $meta[$chave] = $valor;
                }
                continue;
            }

            if ($indice === 0 && !isset($meta['acao'])) {
                $meta['acao'] = $parte;
            }
        }

        return $meta;
    }

    private function agruparDetalhesPorAlteracao(array $alteracaoIds): array
    {
        if (empty($alteracaoIds)) {
            return [];
        }

        $db = \Config\Database::connect();
        if (!$db->tableExists('alteracoes_detalhes')) {
            return [];
        }

        $rows = $db->table('alteracoes_detalhes')
            ->whereIn('alteracao_id', $alteracaoIds)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $detalhes = [];
        foreach ($rows as $row) {
            $alteracaoId = (int) ($row['alteracao_id'] ?? 0);
            if ($alteracaoId <= 0) {
                continue;
            }

            if (!isset($detalhes[$alteracaoId])) {
                $detalhes[$alteracaoId] = [];
            }

            $detalhes[$alteracaoId][] = $row;
        }

        return $detalhes;
    }

    private function montarResumoMudancas(array $mudancas, string $antes, string $depois, string $infoMais): string
    {
        if ($mudancas !== []) {
            $partes = [];
            foreach (array_slice($mudancas, 0, 3) as $mudanca) {
                $campo = trim((string) ($mudanca['campo'] ?? ''));
                $valorDepois = trim((string) ($mudanca['valor_depois'] ?? ''));
                if ($campo === '' && $valorDepois === '') {
                    continue;
                }

                $partes[] = $campo !== '' ? ($campo . ': ' . $valorDepois) : $valorDepois;
            }

            return implode(' | ', $partes);
        }

        if ($depois !== '') {
            return $this->truncarTexto($depois, 180);
        }

        if ($antes !== '') {
            return $this->truncarTexto($antes, 180);
        }

        return $this->truncarTexto($infoMais, 180);
    }

    private function normalizarRegistro(array $row, array $detalhes): array
    {
        $registroId = (int) ($row['id'] ?? 0);
        $data = $this->formatarDataAuditoria($row['data_add'] ?? '');
        $usuarioId = trim((string) ($row['usuario_id'] ?? $row['individuo'] ?? ''));
        $usuarioNome = $this->normalizarUsuarioNome($row['usuario_nome'] ?? ($usuarioId !== '' ? ('ID ' . $usuarioId) : 'Sistema'));
        $item = $this->decodificarValor($row['item'] ?? '');
        $itemId = trim((string) ($row['id_item'] ?? ''));
        $antes = $this->decodificarValor($row['antes'] ?? '');
        $depois = $this->decodificarValor($row['depois'] ?? '');
        $infoMais = $this->decodificarValor($row['info_mais'] ?? '');

        $meta = $this->extrairMetaInfoMais($infoMais);
        $mudancas = [];

        foreach ($detalhes as $detalhe) {
            $campo = $this->decodificarValor($detalhe['campo'] ?? '');
            $valorAntes = $this->decodificarValor($detalhe['valor_antes'] ?? '');
            $valorDepois = $this->decodificarValor($detalhe['valor_depois'] ?? '');

            if (strpos($campo, 'auditoria.') === 0) {
                $metaKey = substr($campo, strlen('auditoria.'));
                if ($metaKey !== '' && $valorDepois !== '') {
                    $meta[$metaKey] = $valorDepois;
                }
                continue;
            }

            $mudancas[] = [
                'campo' => $campo,
                'valor_antes' => $valorAntes,
                'valor_depois' => $valorDepois,
            ];
        }

        $acao = trim((string) ($meta['acao'] ?? ''));
        if ($acao === '' && $infoMais !== '') {
            $acao = trim((string) explode('|', $infoMais)[0]);
            if (strpos($acao, '=') !== false) {
                $acao = '';
            }
        }

        return [
            'id' => $registroId,
            'data' => $data['texto'],
            'data_ordem' => $data['ordem'],
            'usuario' => $usuarioNome,
            'usuario_id' => $usuarioId,
            'item' => $item,
            'item_id' => $itemId,
            'acao' => $acao,
            'resumo' => $this->montarResumoMudancas($mudancas, $antes, $depois, $infoMais),
            'antes' => $antes,
            'depois' => $depois,
            'info_mais' => $infoMais,
            'detalhes' => $mudancas,
            'meta' => $meta,
        ];
    }

    private function filtrarPorPeriodo(array $registros, string $dataInicial, string $dataFinal): array
    {
        $inicio = $dataInicial !== '' ? $this->parseDateTime($dataInicial . ' 00:00:00') : null;
        $fim = $dataFinal !== '' ? $this->parseDateTime($dataFinal . ' 23:59:59') : null;

        if (!$inicio instanceof \DateTimeImmutable && !$fim instanceof \DateTimeImmutable) {
            return $registros;
        }

        return array_values(array_filter($registros, function (array $registro) use ($inicio, $fim): bool {
            $data = $this->parseDateTime($registro['data'] ?? '');
            if (!$data instanceof \DateTimeImmutable) {
                return false;
            }

            if ($inicio instanceof \DateTimeImmutable && $data < $inicio) {
                return false;
            }

            if ($fim instanceof \DateTimeImmutable && $data > $fim) {
                return false;
            }

            return true;
        }));
    }

    public function logs_alteracoes_lista()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'ok' => false,
                'mensagem' => 'Requisicao invalida.',
            ]);
        }

        $this->iniciarSessaoSeNecessario();

        $dataInicial = trim((string) service('request')->getPost('data_inicial'));
        $dataFinal = trim((string) service('request')->getPost('data_final'));
        $limite = (int) service('request')->getPost('limite');
        if ($limite <= 0) {
            $limite = 500;
        }
        $limite = max(50, min($limite, 2000));

        $db = \Config\Database::connect();
        $colunas = $db->getFieldNames('alteracoes');
        $usaSchemaLegado = in_array('individuo', $colunas, true);
        $campoUsuario = $usaSchemaLegado ? 'individuo' : 'usuario_id';

        $limiteConsulta = min(max($limite * 4, 500), 5000);

        $builder = $db->table('alteracoes a')
            ->select('a.*')
            ->select('u.nome AS usuario_nome')
            ->join('usuarios u', 'u.id = a.' . $campoUsuario, 'left')
            ->orderBy('a.id', 'DESC')
            ->limit($limiteConsulta);

        $rows = $builder->get()->getResultArray();
        $alteracaoIds = array_values(array_filter(array_map(static fn ($row): int => (int) ($row['id'] ?? 0), $rows)));
        $detalhesPorAlteracao = $this->agruparDetalhesPorAlteracao($alteracaoIds);

        $registros = [];
        foreach ($rows as $row) {
            $id = (int) ($row['id'] ?? 0);
            $registros[] = $this->normalizarRegistro($row, $detalhesPorAlteracao[$id] ?? []);
        }

        $registros = $this->filtrarPorPeriodo($registros, $dataInicial, $dataFinal);
        $registros = array_slice($registros, 0, $limite);

        return $this->response->setJSON([
            'ok' => true,
            'total' => count($registros),
            'limite' => $limite,
            'registros' => $registros,
        ]);
    }
}
