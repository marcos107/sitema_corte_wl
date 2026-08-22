<?php

namespace App\Libraries;

use App\Controllers\Ferramentas;
use App\Models\ArquivoMetricasMaterial;

class DxfDimensoes
{
    private const METRICA_LARGURA = 'largura_max_mm';
    private const METRICA_ALTURA = 'altura_max_mm';

    private ?bool $tabelaMetricasDisponivel = null;

    public function enriquecerDesenhos(array $desenhos): array
    {
        if (empty($desenhos)) {
            return $desenhos;
        }

        $ids = [];
        foreach ($desenhos as $desenho) {
            $id = (int) ($desenho['id'] ?? 0);
            if ($id > 0) {
                $ids[] = $id;
            }
        }

        $cache = $this->carregarCacheMetricas(array_values(array_unique($ids)));

        foreach ($desenhos as $indice => $desenho) {
            $desenhos[$indice]['dxf_largura_mm'] = null;
            $desenhos[$indice]['dxf_altura_mm'] = null;
            $desenhos[$indice]['dimensao_dxf'] = '';

            $id = (int) ($desenho['id'] ?? 0);
            if ($id <= 0 || !$this->desenhoEhDxf($desenho)) {
                continue;
            }

            $dimensoes = $cache[$id] ?? null;
            if ($dimensoes === null) {
                $arquivoPath = $this->resolverCaminhoArquivo($desenho);
                if ($arquivoPath !== '') {
                    $dimensoes = $this->calcularDimensoesDxfMm($arquivoPath);
                    if ($dimensoes !== null) {
                        $cache[$id] = $dimensoes;
                        $this->persistirMetricas($id, isset($desenho['processos_id']) ? (int) $desenho['processos_id'] : null, $dimensoes);
                    }
                }
            }

            if ($dimensoes === null) {
                continue;
            }

            $desenhos[$indice]['dxf_largura_mm'] = $dimensoes['largura_mm'];
            $desenhos[$indice]['dxf_altura_mm'] = $dimensoes['altura_mm'];
            $desenhos[$indice]['dimensao_dxf'] = $this->formatarDimensoes($dimensoes['largura_mm'], $dimensoes['altura_mm']);
        }

        return $desenhos;
    }

    private function carregarCacheMetricas(array $desenhoIds): array
    {
        if (empty($desenhoIds) || !$this->tabelaMetricasDisponivel()) {
            return [];
        }

        $rows = (new ArquivoMetricasMaterial())
            ->select('entidade_id, metrica, valor_base')
            ->where('entidade_tipo', 'desenho')
            ->where('tipo_arquivo', 'dxf')
            ->whereIn('metrica', [self::METRICA_LARGURA, self::METRICA_ALTURA])
            ->whereIn('entidade_id', $desenhoIds)
            ->findAll();

        $cache = [];
        foreach ($rows as $row) {
            $id = (int) ($row['entidade_id'] ?? 0);
            if ($id <= 0) {
                continue;
            }

            if (!isset($cache[$id])) {
                $cache[$id] = [
                    'largura_mm' => null,
                    'altura_mm' => null,
                ];
            }

            $metrica = (string) ($row['metrica'] ?? '');
            $valor = isset($row['valor_base']) ? (float) $row['valor_base'] : null;
            if ($valor === null || $valor <= 0) {
                continue;
            }

            if ($metrica === self::METRICA_LARGURA) {
                $cache[$id]['largura_mm'] = $valor;
            } elseif ($metrica === self::METRICA_ALTURA) {
                $cache[$id]['altura_mm'] = $valor;
            }
        }

        foreach ($cache as $id => $info) {
            if (($info['largura_mm'] ?? null) === null || ($info['altura_mm'] ?? null) === null) {
                unset($cache[$id]);
            }
        }

        return $cache;
    }

    private function persistirMetricas(int $desenhoId, ?int $processoId, array $dimensoes): void
    {
        if ($desenhoId <= 0 || !$this->tabelaMetricasDisponivel()) {
            return;
        }

        $largura = isset($dimensoes['largura_mm']) ? (float) $dimensoes['largura_mm'] : 0.0;
        $altura = isset($dimensoes['altura_mm']) ? (float) $dimensoes['altura_mm'] : 0.0;
        if ($largura <= 0 || $altura <= 0) {
            return;
        }

        $model = new ArquivoMetricasMaterial();
        $existentes = $model
            ->where('entidade_tipo', 'desenho')
            ->where('entidade_id', $desenhoId)
            ->where('tipo_arquivo', 'dxf')
            ->whereIn('metrica', [self::METRICA_LARGURA, self::METRICA_ALTURA])
            ->findAll();

        $existentesIndexados = [];
        foreach ($existentes as $row) {
            $existentesIndexados[(string) ($row['metrica'] ?? '')] = (int) ($row['id'] ?? 0);
        }

        $payloads = [
            self::METRICA_LARGURA => [
                'entidade_tipo' => 'desenho',
                'entidade_id' => $desenhoId,
                'processo_id' => ($processoId !== null && $processoId > 0) ? $processoId : null,
                'tipo_arquivo' => 'dxf',
                'metrica' => self::METRICA_LARGURA,
                'unidade' => 'mm',
                'valor_base' => round($largura, 3),
                'margem_percentual' => 0,
                'valor_final' => round($largura, 3),
                'fonte_calculo' => 'dxf_bounds',
                'data_referencia' => date('Y-m-d H:i:s'),
            ],
            self::METRICA_ALTURA => [
                'entidade_tipo' => 'desenho',
                'entidade_id' => $desenhoId,
                'processo_id' => ($processoId !== null && $processoId > 0) ? $processoId : null,
                'tipo_arquivo' => 'dxf',
                'metrica' => self::METRICA_ALTURA,
                'unidade' => 'mm',
                'valor_base' => round($altura, 3),
                'margem_percentual' => 0,
                'valor_final' => round($altura, 3),
                'fonte_calculo' => 'dxf_bounds',
                'data_referencia' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($payloads as $metrica => $payload) {
            if (!empty($existentesIndexados[$metrica])) {
                $model->update((int) $existentesIndexados[$metrica], $payload);
                continue;
            }

            $model->insert($payload);
        }
    }

    private function tabelaMetricasDisponivel(): bool
    {
        if ($this->tabelaMetricasDisponivel !== null) {
            return $this->tabelaMetricasDisponivel;
        }

        $db = \Config\Database::connect();
        $this->tabelaMetricasDisponivel = $db->tableExists('arquivo_metricas_material');

        return $this->tabelaMetricasDisponivel;
    }

    private function desenhoEhDxf(array $desenho): bool
    {
        $candidatos = [
            (string) ($desenho['nome'] ?? ''),
            (string) ($desenho['diretorio'] ?? ''),
        ];

        foreach ($candidatos as $candidato) {
            $extensao = strtolower((string) pathinfo($candidato, PATHINFO_EXTENSION));
            if ($extensao === 'dxf') {
                return true;
            }

            $decodificado = Ferramentas::decodificador($candidato);
            if ($decodificado !== '' && strtolower((string) pathinfo($decodificado, PATHINFO_EXTENSION)) === 'dxf') {
                return true;
            }
        }

        return false;
    }

    private function resolverCaminhoArquivo(array $desenho): string
    {
        $diretorio = Ferramentas::wlStoragePath((string) ($desenho['diretorio'] ?? ''));
        if ($diretorio === '') {
            return '';
        }

        if (is_file($diretorio)) {
            return $diretorio;
        }

        $baseDir = dirname($diretorio);
        if ($baseDir === '.' || $baseDir === '') {
            return '';
        }

        $nomes = [];
        $nomes[] = (string) ($desenho['nome'] ?? '');
        $nomeDecodificado = Ferramentas::decodificador((string) ($desenho['nome'] ?? ''));
        if ($nomeDecodificado !== '') {
            $nomes[] = $nomeDecodificado;
        }
        $nomes[] = basename($diretorio);

        $candidatos = [];
        foreach ($nomes as $nome) {
            $nome = trim($nome);
            if ($nome === '') {
                continue;
            }

            $candidatos[] = $nome;
            if (strpos($nome, '.') === false) {
                $pos = strrpos($nome, '_');
                if ($pos !== false) {
                    $candidatos[] = substr_replace($nome, '.', $pos + 1, 0);
                }
            }
        }

        $candidatos = array_values(array_unique(array_filter($candidatos)));
        foreach ($candidatos as $nomeArquivo) {
            $caminho = $this->normalizarPath($baseDir . DIRECTORY_SEPARATOR . $nomeArquivo);
            if ($caminho !== '' && is_file($caminho)) {
                return $caminho;
            }
        }

        return '';
    }

    private function calcularDimensoesDxfMm(string $arquivoPath): ?array
    {
        $pares = $this->carregarParesDxf($arquivoPath);
        if ($pares === null || empty($pares)) {
            return null;
        }

        $insunits = $this->detectarInsunitsDxf($pares);
        $fatorMm = $this->fatorMmPorInsunits($insunits);
        if ($fatorMm <= 0) {
            $fatorMm = 1.0;
        }

        $bounds = $this->extrairBoundsHeader($pares);
        if ($bounds === null) {
            $bounds = $this->extrairBoundsEntidades($pares);
        }

        if ($bounds === null) {
            return null;
        }

        $larguraMm = abs((float) $bounds['max_x'] - (float) $bounds['min_x']) * $fatorMm;
        $alturaMm = abs((float) $bounds['max_y'] - (float) $bounds['min_y']) * $fatorMm;
        if ($larguraMm <= 0 || $alturaMm <= 0) {
            return null;
        }

        return [
            'largura_mm' => round($larguraMm, 3),
            'altura_mm' => round($alturaMm, 3),
        ];
    }

    private function carregarParesDxf(string $arquivoPath): ?array
    {
        if ($arquivoPath === '' || !is_file($arquivoPath)) {
            return null;
        }

        $conteudo = @file_get_contents($arquivoPath);
        if ($conteudo === false || trim($conteudo) === '') {
            return null;
        }

        $linhas = preg_split('/\r\n|\r|\n/', $conteudo);
        if (!is_array($linhas) || count($linhas) < 4) {
            return null;
        }

        $pares = [];
        $totalLinhas = count($linhas);
        for ($i = 0; $i + 1 < $totalLinhas; $i += 2) {
            $codigo = trim((string) $linhas[$i]);
            if ($codigo === '') {
                continue;
            }

            $pares[] = [
                'code' => $codigo,
                'value' => trim((string) $linhas[$i + 1]),
            ];
        }

        return empty($pares) ? null : $pares;
    }

    private function extrairBoundsHeader(array $pares): ?array
    {
        $extMin = $this->extrairVariavelPontoHeader($pares, '$EXTMIN');
        $extMax = $this->extrairVariavelPontoHeader($pares, '$EXTMAX');

        if ($extMin === null || $extMax === null) {
            return null;
        }

        if ($extMax['x'] <= $extMin['x'] || $extMax['y'] <= $extMin['y']) {
            return null;
        }

        return [
            'min_x' => $extMin['x'],
            'min_y' => $extMin['y'],
            'max_x' => $extMax['x'],
            'max_y' => $extMax['y'],
        ];
    }

    private function extrairVariavelPontoHeader(array $pares, string $nomeVariavel): ?array
    {
        $total = count($pares);
        for ($i = 0; $i < $total; $i++) {
            if (($pares[$i]['code'] ?? '') !== '9') {
                continue;
            }

            if (strtoupper((string) ($pares[$i]['value'] ?? '')) !== strtoupper($nomeVariavel)) {
                continue;
            }

            $x = null;
            $y = null;
            for ($j = $i + 1; $j < $total; $j++) {
                $codigo = (string) ($pares[$j]['code'] ?? '');
                if ($codigo === '9') {
                    break;
                }

                if ($codigo === '10') {
                    $x = $this->dxfToFloat((string) ($pares[$j]['value'] ?? ''));
                } elseif ($codigo === '20') {
                    $y = $this->dxfToFloat((string) ($pares[$j]['value'] ?? ''));
                }
            }

            if ($x !== null && $y !== null) {
                return ['x' => $x, 'y' => $y];
            }

            break;
        }

        return null;
    }

    private function extrairBoundsEntidades(array $pares): ?array
    {
        $bounds = null;
        $total = count($pares);
        $dentroEntidades = false;

        for ($i = 0; $i < $total; $i++) {
            if (($pares[$i]['code'] ?? '') !== '0') {
                continue;
            }

            $tipo = strtoupper((string) ($pares[$i]['value'] ?? ''));
            if ($tipo === 'SECTION') {
                $codigoSecao = (string) ($pares[$i + 1]['code'] ?? '');
                $nomeSecao = strtoupper((string) ($pares[$i + 1]['value'] ?? ''));
                $dentroEntidades = ($codigoSecao === '2' && $nomeSecao === 'ENTITIES');
                continue;
            }

            if ($tipo === 'ENDSEC') {
                $dentroEntidades = false;
                continue;
            }

            if (!$dentroEntidades) {
                continue;
            }

            $j = $i + 1;
            while ($j < $total && ($pares[$j]['code'] ?? '') !== '0') {
                $j++;
            }

            $entidade = array_slice($pares, $i + 1, $j - $i - 1);
            $this->atualizarBoundsPorEntidade($bounds, $tipo, $entidade);
            $i = $j - 1;
        }

        return $bounds;
    }

    private function atualizarBoundsPorEntidade(?array &$bounds, string $tipo, array $entidade): void
    {
        if ($tipo === 'CIRCLE') {
            $centro = $this->extrairPontoEntidade($entidade, 10, 20);
            $raio = $this->extrairValorEntidade($entidade, '40');
            if ($centro !== null && $raio !== null && $raio > 0) {
                $this->atualizarBounds($bounds, $centro['x'] - $raio, $centro['y'] - $raio);
                $this->atualizarBounds($bounds, $centro['x'] + $raio, $centro['y'] + $raio);
            }
            return;
        }

        if ($tipo === 'ARC') {
            $centro = $this->extrairPontoEntidade($entidade, 10, 20);
            $raio = $this->extrairValorEntidade($entidade, '40');
            $anguloInicial = $this->extrairValorEntidade($entidade, '50');
            $anguloFinal = $this->extrairValorEntidade($entidade, '51');
            if ($centro !== null && $raio !== null && $raio > 0 && $anguloInicial !== null && $anguloFinal !== null) {
                $this->atualizarBoundsArco($bounds, $centro['x'], $centro['y'], $raio, $anguloInicial, $anguloFinal);
            }
            return;
        }

        foreach ([10, 11, 12, 13, 14, 15, 16, 17, 18] as $codigoX) {
            $codigoY = $codigoX + 10;
            $pontos = $this->extrairPontosSequenciais($entidade, (string) $codigoX, (string) $codigoY);
            foreach ($pontos as $ponto) {
                $this->atualizarBounds($bounds, $ponto['x'], $ponto['y']);
            }
        }
    }

    private function extrairPontosSequenciais(array $entidade, string $codigoX, string $codigoY): array
    {
        $pontos = [];
        $xAtual = null;

        foreach ($entidade as $par) {
            $codigo = (string) ($par['code'] ?? '');
            if ($codigo === $codigoX) {
                $xAtual = $this->dxfToFloat((string) ($par['value'] ?? ''));
                continue;
            }

            if ($codigo === $codigoY && $xAtual !== null) {
                $y = $this->dxfToFloat((string) ($par['value'] ?? ''));
                if ($y !== null) {
                    $pontos[] = ['x' => $xAtual, 'y' => $y];
                }
                $xAtual = null;
            }
        }

        return $pontos;
    }

    private function extrairPontoEntidade(array $entidade, int $codigoX, int $codigoY): ?array
    {
        $x = null;
        $y = null;

        foreach ($entidade as $par) {
            $codigo = (string) ($par['code'] ?? '');
            if ($codigo === (string) $codigoX) {
                $x = $this->dxfToFloat((string) ($par['value'] ?? ''));
            } elseif ($codigo === (string) $codigoY) {
                $y = $this->dxfToFloat((string) ($par['value'] ?? ''));
            }
        }

        if ($x === null || $y === null) {
            return null;
        }

        return ['x' => $x, 'y' => $y];
    }

    private function extrairValorEntidade(array $entidade, string $codigoBusca): ?float
    {
        foreach ($entidade as $par) {
            if ((string) ($par['code'] ?? '') !== $codigoBusca) {
                continue;
            }

            return $this->dxfToFloat((string) ($par['value'] ?? ''));
        }

        return null;
    }

    private function atualizarBoundsArco(?array &$bounds, float $cx, float $cy, float $raio, float $anguloInicial, float $anguloFinal): void
    {
        $candidatos = [$anguloInicial, $anguloFinal, 0.0, 90.0, 180.0, 270.0];
        foreach ($candidatos as $angulo) {
            if (!$this->anguloEstaNoArco($angulo, $anguloInicial, $anguloFinal)) {
                continue;
            }

            $rad = deg2rad($angulo);
            $x = $cx + ($raio * cos($rad));
            $y = $cy + ($raio * sin($rad));
            $this->atualizarBounds($bounds, $x, $y);
        }
    }

    private function anguloEstaNoArco(float $angulo, float $inicio, float $fim): bool
    {
        $angulo = $this->normalizarAngulo($angulo);
        $inicio = $this->normalizarAngulo($inicio);
        $fim = $this->normalizarAngulo($fim);

        if ($inicio <= $fim) {
            return $angulo >= $inicio && $angulo <= $fim;
        }

        return $angulo >= $inicio || $angulo <= $fim;
    }

    private function normalizarAngulo(float $angulo): float
    {
        $angulo = fmod($angulo, 360.0);
        if ($angulo < 0) {
            $angulo += 360.0;
        }

        return $angulo;
    }

    private function atualizarBounds(?array &$bounds, ?float $x, ?float $y): void
    {
        if ($x === null || $y === null) {
            return;
        }

        if ($bounds === null) {
            $bounds = [
                'min_x' => $x,
                'max_x' => $x,
                'min_y' => $y,
                'max_y' => $y,
            ];
            return;
        }

        $bounds['min_x'] = min((float) $bounds['min_x'], $x);
        $bounds['max_x'] = max((float) $bounds['max_x'], $x);
        $bounds['min_y'] = min((float) $bounds['min_y'], $y);
        $bounds['max_y'] = max((float) $bounds['max_y'], $y);
    }

    private function detectarInsunitsDxf(array $pares): int
    {
        $total = count($pares);
        for ($i = 0; $i < $total; $i++) {
            if (($pares[$i]['code'] ?? '') !== '9') {
                continue;
            }

            if (strtoupper((string) ($pares[$i]['value'] ?? '')) !== '$INSUNITS') {
                continue;
            }

            for ($j = $i + 1; $j < $total; $j++) {
                $codigo = (string) ($pares[$j]['code'] ?? '');
                if ($codigo === '9') {
                    break;
                }
                if ($codigo === '70') {
                    return (int) ($pares[$j]['value'] ?? 0);
                }
            }
        }

        return 4;
    }

    private function fatorMmPorInsunits(int $insunits): float
    {
        switch ($insunits) {
            case 1:
                return 25.4;
            case 2:
                return 304.8;
            case 4:
                return 1.0;
            case 5:
                return 10.0;
            case 6:
                return 1000.0;
            case 7:
                return 1000000.0;
            case 8:
                return 0.0000254;
            case 9:
                return 0.0254;
            case 10:
                return 914.4;
            case 13:
                return 0.001;
            case 14:
                return 100.0;
            default:
                return 1.0;
        }
    }

    private function dxfToFloat(string $valor): ?float
    {
        $normalizado = str_replace(',', '.', trim($valor));
        if ($normalizado === '' || !is_numeric($normalizado)) {
            return null;
        }

        return (float) $normalizado;
    }

    private function formatarDimensoes(float $larguraMm, float $alturaMm): string
    {
        return 'L max: ' . $this->formatarNumero($larguraMm) . ' mm | H max: ' . $this->formatarNumero($alturaMm) . ' mm';
    }

    private function formatarNumero(float $valor): string
    {
        $texto = number_format(round($valor, 2), 2, ',', '.');
        $texto = preg_replace('/,00$/', '', $texto) ?? $texto;
        $texto = preg_replace('/(\,\d*[1-9])0+$/', '$1', $texto) ?? $texto;

        return $texto;
    }

    private function normalizarPath(string $path): string
    {
        $path = trim($path);
        if ($path === '') {
            return '';
        }

        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $sep = preg_quote(DIRECTORY_SEPARATOR, '#');
        $path = preg_replace("#{$sep}+#", DIRECTORY_SEPARATOR, $path) ?? $path;
        $path = preg_replace_callback('/^([a-z]):/i', static function ($match) {
            return strtoupper($match[1]) . ':';
        }, $path) ?? $path;

        if (!preg_match('/^[A-Z]:' . preg_quote(DIRECTORY_SEPARATOR, '/') . '$/i', $path)) {
            $path = rtrim($path, DIRECTORY_SEPARATOR);
        }

        return str_replace('\\', '/', $path);
    }
}
