<?php

namespace App\Controllers;


use App\Controllers\Ferramentas;

use Mpdf\Mpdf;

class RelatorioPost extends Ferramentas
{
  private array $relatorioDebugContext = [];

  /**
   * Converte um tempo no formato 'HH:MM:SS' para o equivalente em segundos.
   *
   * @param string $time O tempo no formato 'HH:MM:SS' a ser convertido.
   * @return int O total de segundos correspondentes ao tempo fornecido.
   */
  function timeToSeconds($time)
  {
    list($hours, $minutes, $seconds) = explode(':', $time);
    return $hours * 3600 + $minutes * 60 + $seconds;
  }

  /**
   * Converte um valor em segundos para o formato de tempo 'HH:MM:SS'.
   *
   * @param int $seconds O total de segundos a ser convertido.
   * @return string O tempo formatado como 'HH:MM:SS'.
   */
  function secondsToTime($seconds)
  {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $seconds = $seconds % 60;
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
  }

  /**
   * Dispatcher do relatorio por tipo de processo.
   */
  function relatorio()
  {
    if (!$this->request->isAJAX()) {
      return;
    }

    $processo = service('request')->getPost('processo');
    $processoId = $this->normalizarFiltroId(service('request')->getPost('processoId'));
    $processoTipoFront = trim((string) service('request')->getPost('processoTipo'));
    $tipoProcesso = $this->getTipoProcesso($processo, $processoId, $processoTipoFront);

    switch ($tipoProcesso) {
      case 'mult':
        return $this->relatorio_mult();
      case 'ind':
        return $this->relatorio_ind();
      default:
        return $this->relatorio_mult();
    }
  }

  /**
   * Resolve o tipo do processo (campo input: ind/mult).
   */
  private function getTipoProcesso($processo, ?int $processoId = null, string $processoTipoFront = ''): string
  {
    $processosModel = new \App\Models\Processos();

    $processoDb = null;

    if ($processoId !== null) {
      $processoDb = $processosModel
        ->select('input')
        ->where('id', $processoId)
        ->first();
    }

    if (!$processoDb) {
      $processoDb = $processosModel
        ->select('input')
        ->where('nome', $processo)
        ->first();
    }

    if (!$processoDb && is_string($processo) && $processo !== '') {
      $processoCodificado = Ferramentas::codificador($processo);
      if ($processoCodificado !== '') {
        $processoDb = $processosModel
          ->select('input')
          ->where('nome', $processoCodificado)
          ->first();
      }
    }

    if (is_array($processoDb) && isset($processoDb['input'])) {
      return strtolower(trim((string) $processoDb['input']));
    }

    $processoTipoFront = strtolower(trim($processoTipoFront));
    if (in_array($processoTipoFront, ['mult', 'ind'], true)) {
      return $processoTipoFront;
    }

    return '';
  }

  /**
   * Resposta padronizada de erro para o front.
   */
  private function respostaErroRelatorio(array $msg, array $debugExtra = [])
  {
    if ($debugExtra !== []) {
      $this->relatorioDebugMerge($debugExtra);
    }

    $this->relatorioDebugSet('erro', $msg);

    return $this->respostaJsonRelatorio([
      'ok' => false,
      'msg' => $msg
    ]);
  }

  /**
   * Extrai IDs de participantes selecionados e prepara resumo inicial por participante.
   *
   * Retorno:
   * - [0] => array<int> IDs únicos dos participantes
   * - [1] => array<int, array{nome:string, funcao:string, publicacoes:int, finalizacoes:int}>
   */
  private function extrairParticipantesSelecionados($selectedValues): array
  {
    $participantIds = [];
    $participantesResumo = [];

    if (!is_array($selectedValues)) {
      return [[], []];
    }

    foreach ($selectedValues as $funcao => $group) {
      if (!is_array($group)) {
        continue;
      }

      $funcaoNome = trim((string) Ferramentas::decodificador((string) $funcao));
      if ($funcaoNome === '') {
        $funcaoNome = trim((string) $funcao);
      }
      if ($funcaoNome === '') {
        $funcaoNome = 'Participante';
      }

      foreach ($group as $idx) {
        $idxKey = (string) $idx;
        if (!isset($_SESSION['lista_usuarios'][$idxKey]) || !is_array($_SESSION['lista_usuarios'][$idxKey])) {
          continue;
        }

        $usuario = $_SESSION['lista_usuarios'][$idxKey];
        $participantId = (int) Ferramentas::array_index($usuario, ['id']);
        if ($participantId <= 0) {
          continue;
        }

        $nome = trim((string) Ferramentas::decodificador((string) Ferramentas::array_index($usuario, ['nome'])));
        if ($nome === '') {
          $nome = 'Usuário #' . $participantId;
        }

        $participantIds[$participantId] = $participantId;

        if (!isset($participantesResumo[$participantId])) {
          $participantesResumo[$participantId] = [
            'nome' => $nome,
            'funcao' => $funcaoNome,
            'publicacoes' => 0,
            'finalizacoes' => 0
          ];
          continue;
        }

        if (strpos($participantesResumo[$participantId]['funcao'], $funcaoNome) === false) {
          $participantesResumo[$participantId]['funcao'] .= ' / ' . $funcaoNome;
        }
      }
    }

    return [array_values($participantIds), $participantesResumo];
  }

  /**
   * Normaliza filtros opcionais (empresa/empreendimento) vindos do POST.
   */
  private function normalizarFiltroId($valor): ?int
  {
    if (is_string($valor)) {
      $valor = trim($valor);
      $valorLower = strtolower($valor);
      if ($valorLower === 'null' || $valorLower === 'undefined') {
        return null;
      }
    }

    if ($valor === null || $valor === '' || $valor === '0' || $valor === 0) {
      return null;
    }

    if (!is_numeric($valor)) {
      return null;
    }

    $id = (int) $valor;
    return $id > 0 ? $id : null;
  }

  /**
   * Resolve nome legível a partir de um campo potencialmente codificado.
   */
  private function nomeLegivel($valor): string
  {
    $nome = trim((string) Ferramentas::decodificador((string) $valor));
    if ($nome !== '') {
      return $nome;
    }

    return trim((string) $valor);
  }

  private function normalizarTextoBusca(string $texto): string
  {
    $texto = strtolower(trim($texto));
    if ($texto === '') {
      return '';
    }

    $map = [
      'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a',
      'é' => 'e', 'ê' => 'e',
      'í' => 'i',
      'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
      'ú' => 'u',
      'ç' => 'c',
    ];
    $texto = strtr($texto, $map);
    $texto = preg_replace('/\s+/', ' ', $texto) ?? $texto;

    return $texto;
  }

  private function processoEhCorteLaser(?int $processoId = null, string $processoNome = ''): bool
  {
    $nome = trim($processoNome);
    if ($nome === '' && $processoId !== null && $processoId > 0) {
      $processoRow = (new \App\Models\Processos())
        ->select('nome')
        ->where('id', $processoId)
        ->first();
      if (is_array($processoRow)) {
        $nome = trim((string) ($processoRow['nome'] ?? ''));
      }
    }

    $nome = $this->nomeLegivel($nome);
    $nome = $this->normalizarTextoBusca($nome);

    return strpos($nome, 'corte') !== false && strpos($nome, 'laser') !== false;
  }

  /**
   * Mensagem padrão para retorno sem dados.
   */
  /**
   * Quebra linhas <tr> de uma tabela em dois blocos: primeiras N e restante.
   *
   * @return array{0:string,1:string}
   */
  private function splitTableRowsHtml(string $rowsHtml, int $firstCount = 3): array
  {
    $rowsHtml = trim($rowsHtml);
    if ($rowsHtml === '' || $firstCount <= 0) {
      return [$rowsHtml, ''];
    }

    if (!preg_match_all('/<tr\\b[^>]*>.*?<\\/tr>/is', $rowsHtml, $matches)) {
      return [$rowsHtml, ''];
    }

    $rows = $matches[0] ?? [];
    if ($rows === []) {
      return [$rowsHtml, ''];
    }

    $firstBlock = implode('', array_slice($rows, 0, $firstCount));
    $restBlock = implode('', array_slice($rows, $firstCount));

    return [$firstBlock, $restBlock];
  }

  /**
   * Monta HTML de secao mantendo junto: titulo + cabecalho + 3 primeiras linhas.
   */
  private function buildTitleTableSectionWithMinRows(
    string $titleTag,
    string $titleTextHtml,
    string $tableAttrsHtml,
    string $tableHeaderRowsHtml,
    string $tableBodyRowsHtml,
    string $tableFooterRowsHtml = '',
    int $minRows = 3
  ): string {
    [$firstRows, $restRows] = $this->splitTableRowsHtml($tableBodyRowsHtml, $minRows);

    if ($firstRows === '') {
      $firstRows = $tableBodyRowsHtml;
      $restRows = '';
    }

    $titleAndFirstBlock = '<div class="section-keep-min">
      <' . $titleTag . '>' . $titleTextHtml . '</' . $titleTag . '>
      <table ' . $tableAttrsHtml . '>
        ' . $tableHeaderRowsHtml . '
        ' . $firstRows . '
        ' . ($restRows === '' ? $tableFooterRowsHtml : '') . '
      </table>
    </div>';

    if ($restRows === '') {
      return $titleAndFirstBlock;
    }

    $restTable = '<table ' . $tableAttrsHtml . '>
      ' . $tableHeaderRowsHtml . '
      ' . $restRows . '
      ' . $tableFooterRowsHtml . '
    </table>';

    return $titleAndFirstBlock . $restTable;
  }

  /**
   * Carrega areas de material (m2) por desenho.
   *
   * @return array<int,array{area_m2:float,area_m2_com_margem:float,margem_percentual:float,status:string,fonte:string}>
   */
  private function carregarAreasMaterialPorDesenho(array $desenhoIds, ?int $processoId = null): array
  {
    $desenhoIds = array_values(array_unique(array_filter(array_map('intval', $desenhoIds), static function ($id) {
      return $id > 0;
    })));

    if ($desenhoIds === []) {
      return [];
    }

    $db = \Config\Database::connect();
    $map = [];

    // Estrutura generica (nova)
    if ($db->tableExists('arquivo_metricas_material')) {
      $query = (new \App\Models\ArquivoMetricasMaterial())
        ->select('entidade_id, valor_base, valor_final, margem_percentual, fonte_calculo')
        ->where('entidade_tipo', 'desenho')
        ->where('metrica', 'area_m2')
        ->where('tipo_arquivo', 'dxf')
        ->whereIn('entidade_id', $desenhoIds);

      if ($processoId !== null && $processoId > 0) {
        $query->groupStart()
          ->where('processo_id', $processoId)
          ->orWhere('processo_id', null)
          ->groupEnd();
      }

      $rows = $query->findAll();
      foreach ($rows as $row) {
        $id = (int) ($row['entidade_id'] ?? 0);
        if ($id <= 0) {
          continue;
        }

        $fonte = strtolower(trim((string) ($row['fonte_calculo'] ?? '')));
        $status = 'ok';
        if ($fonte === 'arquivo_nao_encontrado') {
          $status = 'arquivo_nao_encontrado';
        } elseif ($fonte === 'dxf_sem_area') {
          $status = 'sem_area';
        }

        $map[$id] = [
          'area_m2' => (float) ($row['valor_base'] ?? 0),
          'area_m2_com_margem' => (float) ($row['valor_final'] ?? 0),
          'margem_percentual' => (float) ($row['margem_percentual'] ?? 10),
          'status' => $status,
          'fonte' => $fonte,
        ];
      }
    }

    // Fallback legado (tabela antiga)
    $idsSemNovaTabela = array_values(array_diff($desenhoIds, array_keys($map)));
    if ($idsSemNovaTabela !== [] && $db->tableExists('desenho_area_material')) {
      $rows = (new \App\Models\DesenhoAreaMaterial())
        ->select('desenho_id, area_m2, area_m2_com_margem, margem_percentual')
        ->whereIn('desenho_id', $idsSemNovaTabela)
        ->findAll();

      foreach ($rows as $row) {
        $id = (int) ($row['desenho_id'] ?? 0);
        if ($id <= 0) {
          continue;
        }

        $map[$id] = [
          'area_m2' => (float) ($row['area_m2'] ?? 0),
          'area_m2_com_margem' => (float) ($row['area_m2_com_margem'] ?? 0),
          'margem_percentual' => (float) ($row['margem_percentual'] ?? 10),
          'status' => 'ok',
          'fonte' => 'legado_desenho_area_material',
        ];
      }
    }

    return $map;
  }

  /**
   * Garante que a tabela generica de metricas exista.
   * Evita depender de migrate manual para a geracao de relatorio.
   */
  private function garantirTabelaArquivoMetricasMaterial(): bool
  {
    $db = \Config\Database::connect();
    if ($db->tableExists('arquivo_metricas_material')) {
      return true;
    }

    try {
      $forge = \Config\Database::forge();
      $forge->addField([
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

      $forge->addKey('id', true);
      $forge->addKey('entidade_id');
      $forge->addKey('processo_id');
      $forge->addKey('tipo_arquivo');
      $forge->addKey('metrica');
      $forge->addUniqueKey(
        ['entidade_tipo', 'entidade_id', 'tipo_arquivo', 'metrica'],
        'ux_arquivo_metricas_entidade'
      );
      $forge->createTable('arquivo_metricas_material', true);

      // Migra dados legados, se existir tabela antiga.
      if ($db->tableExists('desenho_area_material')) {
        $db->query(
          "INSERT INTO arquivo_metricas_material
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
           WHERE amm.id IS NULL"
        );
      }
    } catch (\Throwable $e) {
      $this->relatorioLog('error', '[Relatorio][material] erro ao criar tabela arquivo_metricas_material: ' . $e->getMessage());
      return false;
    }

    return $db->tableExists('arquivo_metricas_material');
  }

  /**
   * Garante cache de area de material para DXF antes da composicao do relatorio.
   * Se arquivo nao existir, grava marcador para nao recalcular em novas geracoes.
   *
   * @return array<string,mixed>
   */
  private function garantirAreasMaterialPorDesenho(array $desenhosRows, ?int $processoId = null): array
  {
    $db = \Config\Database::connect();
    $tabelaJaExistia = $db->tableExists('arquivo_metricas_material');
    $tabelaDisponivel = $this->garantirTabelaArquivoMetricasMaterial();
    if (!$tabelaDisponivel) {
      return [
        'habilitado' => false,
        'motivo' => 'tabela arquivo_metricas_material nao encontrada',
      ];
    }
    $tabelaCriadaAutomaticamente = !$tabelaJaExistia;

    $dadosDxf = $this->extrairDadosDxfRelatorio($desenhosRows);
    if ($dadosDxf === []) {
      return [
        'habilitado' => true,
        'tabela_criada_automaticamente' => $tabelaCriadaAutomaticamente,
        'dxf_total' => 0,
        'ja_em_cache' => 0,
        'calculados' => 0,
        'arquivos_inexistentes' => 0,
        'sem_area' => 0,
      ];
    }

    $desenhoIds = array_keys($dadosDxf);
    $model = new \App\Models\ArquivoMetricasMaterial();
    $rowsExistentes = $model
      ->select('entidade_id')
      ->where('entidade_tipo', 'desenho')
      ->where('metrica', 'area_m2')
      ->where('tipo_arquivo', 'dxf')
      ->whereIn('entidade_id', $desenhoIds)
      ->findAll();

    $idsComCache = [];
    foreach ($rowsExistentes as $rowExistente) {
      $idExistente = (int) ($rowExistente['entidade_id'] ?? 0);
      if ($idExistente > 0) {
        $idsComCache[$idExistente] = true;
      }
    }

    $faltantes = [];
    foreach ($desenhoIds as $desenhoId) {
      if (!isset($idsComCache[$desenhoId])) {
        $faltantes[] = $desenhoId;
      }
    }

    $resultado = [
      'habilitado' => true,
      'tabela_criada_automaticamente' => $tabelaCriadaAutomaticamente,
      'dxf_total' => count($desenhoIds),
      'ja_em_cache' => count($desenhoIds) - count($faltantes),
      'faltantes_total' => count($faltantes),
      'faltantes_ids' => $faltantes,
      'calculados' => 0,
      'arquivos_inexistentes' => 0,
      'sem_area' => 0,
      'amostra_inexistentes' => [],
      'amostra_processamento' => [],
    ];

    foreach ($faltantes as $desenhoId) {
      $info = $dadosDxf[$desenhoId] ?? [];
      $diretorio = (string) ($info['diretorio'] ?? '');
      $arquivo = (string) ($info['arquivo'] ?? '');
      $tentativasCaminho = [];
      $caminhoArquivo = $this->resolverCaminhoArquivoDesenho($diretorio, $arquivo, $tentativasCaminho);

      if ($caminhoArquivo === null) {
        $this->salvarMetricaAreaDxf(
          $desenhoId,
          $processoId,
          0.0,
          10.0,
          0.0,
          'arquivo_nao_encontrado'
        );
        $resultado['arquivos_inexistentes']++;
        if (count($resultado['amostra_inexistentes']) < 10) {
          $resultado['amostra_inexistentes'][] = [
            'desenho_id' => $desenhoId,
            'arquivo' => Ferramentas::remove_id_file($arquivo),
            'diretorio' => $diretorio,
          ];
        }
        if (count($resultado['amostra_processamento']) < 50) {
          $resultado['amostra_processamento'][] = [
            'desenho_id' => $desenhoId,
            'arquivo' => Ferramentas::remove_id_file($arquivo),
            'resultado' => 'arquivo_nao_encontrado',
            'tentativas' => array_slice($tentativasCaminho, 0, 12),
          ];
        }
        continue;
      }

      $areaM2 = $this->calcularAreaM2Dxf($caminhoArquivo);
      if ($areaM2 === null || $areaM2 <= 0) {
        $this->salvarMetricaAreaDxf(
          $desenhoId,
          $processoId,
          0.0,
          10.0,
          0.0,
          'dxf_sem_area'
        );
        $resultado['sem_area']++;
        if (count($resultado['amostra_processamento']) < 50) {
          $resultado['amostra_processamento'][] = [
            'desenho_id' => $desenhoId,
            'arquivo' => Ferramentas::remove_id_file($arquivo),
            'resultado' => 'dxf_sem_area',
            'caminho_resolvido' => $caminhoArquivo,
          ];
        }
        continue;
      }

      $areaM2 = round($areaM2, 6);
      $margem = 10.0;
      $areaComMargemM2 = round($areaM2 * (1 + ($margem / 100)), 6);

      $this->salvarMetricaAreaDxf(
        $desenhoId,
        $processoId,
        $areaM2,
        $margem,
        $areaComMargemM2,
        'dxf_entities'
      );
      $resultado['calculados']++;
      if (count($resultado['amostra_processamento']) < 50) {
        $resultado['amostra_processamento'][] = [
          'desenho_id' => $desenhoId,
          'arquivo' => Ferramentas::remove_id_file($arquivo),
          'resultado' => 'calculado',
          'caminho_resolvido' => $caminhoArquivo,
          'area_m2' => $areaM2,
          'area_m2_com_margem' => $areaComMargemM2,
        ];
      }
    }

    return $resultado;
  }

  /**
   * @return array<int,array{diretorio:string,arquivo:string}>
   */
  private function extrairDadosDxfRelatorio(array $desenhosRows): array
  {
    $dados = [];

    foreach ($desenhosRows as $row) {
      if (!is_array($row)) {
        continue;
      }

      $desenhoId = (int) (($row['id'] ?? $row['desenho_id'] ?? 0));
      if ($desenhoId <= 0 || isset($dados[$desenhoId])) {
        continue;
      }

      $diretorio = (string) ($row['diretorio'] ?? $row['arquivo_diretorio'] ?? '');
      $arquivo = (string) ($row['arquivo'] ?? $row['nome'] ?? basename($diretorio));

      $referenciaExt = $diretorio !== '' ? $diretorio : $arquivo;
      $extensao = strtolower((string) pathinfo($referenciaExt, PATHINFO_EXTENSION));
      if ($extensao === '' && preg_match('/_([a-z0-9]{2,5})$/i', $arquivo, $m)) {
        $extensao = strtolower((string) ($m[1] ?? ''));
      }

      if ($extensao !== 'dxf') {
        continue;
      }

      $dados[$desenhoId] = [
        'diretorio' => $diretorio,
        'arquivo' => $arquivo,
      ];
    }

    return $dados;
  }

  private function resolverCaminhoArquivoDesenho(string $diretorioBruto, string $arquivoBruto = '', array &$tentativas = []): ?string
  {
    $diretorios = [];
    foreach ([$diretorioBruto, Ferramentas::decodificador($diretorioBruto)] as $dirCandidato) {
      $dir = trim((string) $dirCandidato);
      if ($dir === '') {
        continue;
      }
      $diretorios[$dir] = true;
      $diretorios[str_replace('\\', '/', $dir)] = true;
    }

    foreach (array_keys($diretorios) as $diretorio) {
      $diretorio = trim($diretorio);
      if ($diretorio === '') {
        continue;
      }

      $diretorioNormalizado = Ferramentas::wlStoragePath($diretorio);
      $this->registrarTentativaCaminho($tentativas, $diretorioNormalizado);
      if (is_file($diretorioNormalizado)) {
        return $diretorioNormalizado;
      }

      $baseDir = dirname($diretorioNormalizado);
      $baseNome = basename($diretorioNormalizado);
      $nomesCandidatos = $this->gerarNomesCandidatosArquivo([$arquivoBruto, $baseNome]);
      foreach ($nomesCandidatos as $nomeArquivo) {
        $nomeArquivo = basename(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $nomeArquivo));
        if ($nomeArquivo === '' || $baseDir === '') {
          continue;
        }

        $caminhoFinal = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $nomeArquivo;
        $this->registrarTentativaCaminho($tentativas, $caminhoFinal);
        if (is_file($caminhoFinal)) {
          return $caminhoFinal;
        }
      }
    }

    return null;
  }

  private function registrarTentativaCaminho(array &$tentativas, string $caminho): void
  {
    $caminho = trim($caminho);
    if ($caminho === '') {
      return;
    }

    $caminhoNormalizado = str_replace(['\\', '//'], ['/', '/'], $caminho);
    if (!in_array($caminhoNormalizado, $tentativas, true)) {
      $tentativas[] = $caminhoNormalizado;
    }
  }

  /**
   * @param array<int,string> $nomesBrutos
   * @return array<int,string>
   */
  private function gerarNomesCandidatosArquivo(array $nomesBrutos): array
  {
    $nomes = [];

    foreach ($nomesBrutos as $nomeBruto) {
      if (!is_string($nomeBruto) || trim($nomeBruto) === '') {
        continue;
      }

      foreach ([$nomeBruto, Ferramentas::decodificador($nomeBruto)] as $candidato) {
        $nome = basename(trim(str_replace('\\', '/', (string) $candidato)));
        if ($nome === '') {
          continue;
        }

        $nomes[$nome] = true;
        $ajustado = $this->inserirPontoAntesUltimoUnderscore($nome);
        if ($ajustado !== $nome) {
          $nomes[$ajustado] = true;
        }
      }
    }

    return array_keys($nomes);
  }

  private function inserirPontoAntesUltimoUnderscore(string $nome): string
  {
    if (strpos($nome, '.') !== false) {
      return $nome;
    }

    $pos = strrpos($nome, '_');
    if ($pos === false || $pos >= (strlen($nome) - 1)) {
      return $nome;
    }

    return substr_replace($nome, '.', $pos + 1, 0);
  }

  private function salvarMetricaAreaDxf(
    int $desenhoId,
    ?int $processoId,
    float $areaBaseM2,
    float $margemPercentual,
    float $areaFinalM2,
    string $fonteCalculo
  ): void {
    if ($desenhoId <= 0) {
      return;
    }

    $payload = [
      'entidade_tipo' => 'desenho',
      'entidade_id' => $desenhoId,
      'processo_id' => ($processoId !== null && $processoId > 0) ? $processoId : null,
      'tipo_arquivo' => 'dxf',
      'metrica' => 'area_m2',
      'unidade' => 'm2',
      'valor_base' => round($areaBaseM2, 6),
      'margem_percentual' => round($margemPercentual, 2),
      'valor_final' => round($areaFinalM2, 6),
      'fonte_calculo' => $fonteCalculo,
      'data_referencia' => date('Y-m-d H:i:s'),
    ];

    $model = new \App\Models\ArquivoMetricasMaterial();
    $existente = $model
      ->where('entidade_tipo', 'desenho')
      ->where('entidade_id', $desenhoId)
      ->where('tipo_arquivo', 'dxf')
      ->where('metrica', 'area_m2')
      ->first();

    if (is_array($existente) && isset($existente['id'])) {
      $model->update((int) $existente['id'], $payload);
      return;
    }

    $model->insert($payload);
  }

  /**
   * Calcula area de entidades fechadas do DXF e converte para m2.
   */
  private function calcularAreaM2Dxf(string $arquivoPath): ?float
  {
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

      $valor = trim((string) $linhas[$i + 1]);
      $pares[] = [
        'code' => $codigo,
        'value' => $valor,
      ];
    }

    if ($pares === []) {
      return null;
    }

    $insunits = $this->detectarInsunitsDxf($pares);
    $fatorMetro = $this->fatorMetroPorInsunits($insunits);
    if ($fatorMetro <= 0) {
      $fatorMetro = 0.001;
    }

    $areaUnidadeQuadrada = 0.0;
    $totalPares = count($pares);
    $dentroEntidades = false;

    for ($i = 0; $i < $totalPares; $i++) {
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

      if ($tipo === 'LWPOLYLINE') {
        $vertices = [];
        $fechado = false;
        $xAtual = null;
        $j = $i + 1;

        while ($j < $totalPares && ($pares[$j]['code'] ?? '') !== '0') {
          $codigo = (string) ($pares[$j]['code'] ?? '');
          $valor = (string) ($pares[$j]['value'] ?? '');

          if ($codigo === '70') {
            $fechado = (((int) $valor & 1) === 1);
          } elseif ($codigo === '10') {
            $xAtual = $this->dxfToFloat($valor);
          } elseif ($codigo === '20' && $xAtual !== null) {
            $yAtual = $this->dxfToFloat($valor);
            if ($yAtual !== null) {
              $vertices[] = [$xAtual, $yAtual];
            }
            $xAtual = null;
          }

          $j++;
        }

        if ($fechado && count($vertices) >= 3) {
          $areaUnidadeQuadrada += abs($this->calcularAreaPoligono($vertices));
        }

        $i = $j - 1;
        continue;
      }

      if ($tipo === 'CIRCLE') {
        $raio = null;
        $j = $i + 1;
        while ($j < $totalPares && ($pares[$j]['code'] ?? '') !== '0') {
          if (($pares[$j]['code'] ?? '') === '40') {
            $raio = $this->dxfToFloat((string) ($pares[$j]['value'] ?? ''));
          }
          $j++;
        }

        if ($raio !== null && $raio > 0) {
          $areaUnidadeQuadrada += M_PI * $raio * $raio;
        }

        $i = $j - 1;
        continue;
      }

      if ($tipo === 'POLYLINE') {
        $fechado = false;
        $vertices = [];
        $j = $i + 1;

        while ($j < $totalPares && ($pares[$j]['code'] ?? '') !== '0') {
          if (($pares[$j]['code'] ?? '') === '70') {
            $fechado = (((int) ($pares[$j]['value'] ?? 0) & 1) === 1);
          }
          $j++;
        }

        while ($j < $totalPares) {
          if (($pares[$j]['code'] ?? '') !== '0') {
            $j++;
            continue;
          }

          $tipoSub = strtoupper((string) ($pares[$j]['value'] ?? ''));
          if ($tipoSub === 'SEQEND') {
            break;
          }

          if ($tipoSub !== 'VERTEX') {
            break;
          }

          $x = null;
          $y = null;
          $k = $j + 1;
          while ($k < $totalPares && ($pares[$k]['code'] ?? '') !== '0') {
            $codigo = (string) ($pares[$k]['code'] ?? '');
            $valor = (string) ($pares[$k]['value'] ?? '');
            if ($codigo === '10') {
              $x = $this->dxfToFloat($valor);
            } elseif ($codigo === '20') {
              $y = $this->dxfToFloat($valor);
            }
            $k++;
          }

          if ($x !== null && $y !== null) {
            $vertices[] = [$x, $y];
          }
          $j = $k;
        }

        if ($fechado && count($vertices) >= 3) {
          $areaUnidadeQuadrada += abs($this->calcularAreaPoligono($vertices));
        }

        $i = $j;
      }
    }

    if ($areaUnidadeQuadrada <= 0) {
      return null;
    }

    $areaM2 = $areaUnidadeQuadrada * $fatorMetro * $fatorMetro;
    return ($areaM2 > 0) ? $areaM2 : null;
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

    // fallback comum em corte: milimetros
    return 4;
  }

  private function fatorMetroPorInsunits(int $insunits): float
  {
    switch ($insunits) {
      case 1:
        return 0.0254; // inches
      case 2:
        return 0.3048; // feet
      case 4:
        return 0.001; // mm
      case 5:
        return 0.01; // cm
      case 6:
        return 1.0; // m
      case 7:
        return 1000.0; // km
      case 10:
        return 0.9144; // yards
      case 14:
        return 0.1; // decimetro
      default:
        return 0.001;
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

  private function calcularAreaPoligono(array $vertices): float
  {
    $total = count($vertices);
    if ($total < 3) {
      return 0.0;
    }

    $soma = 0.0;
    for ($i = 0; $i < $total; $i++) {
      $j = ($i + 1) % $total;
      $x1 = (float) ($vertices[$i][0] ?? 0.0);
      $y1 = (float) ($vertices[$i][1] ?? 0.0);
      $x2 = (float) ($vertices[$j][0] ?? 0.0);
      $y2 = (float) ($vertices[$j][1] ?? 0.0);
      $soma += ($x1 * $y2) - ($x2 * $y1);
    }

    return $soma / 2.0;
  }

  private function formatarM2(float $valor): string
  {
    return number_format($valor, 4, ',', '.');
  }

  private function formatarQuantidade(float $valor, int $casas = 2): string
  {
    return number_format($valor, $casas, ',', '.');
  }

  private function formatarTamanhoArquivo(int $bytes): string
  {
    if ($bytes <= 0) {
      return '0 B';
    }

    $unidades = ['B', 'KB', 'MB', 'GB', 'TB'];
    $valor = (float) $bytes;
    $idx = 0;

    while ($valor >= 1024 && $idx < count($unidades) - 1) {
      $valor /= 1024;
      $idx++;
    }

    return number_format($valor, $idx === 0 ? 0 : 2, ',', '.') . ' ' . $unidades[$idx];
  }

  private function obterTamanhoArquivoDesenho(string $diretorioBruto, string $arquivoBruto = ''): string
  {
    $tentativas = [];
    $caminhoArquivo = $this->resolverCaminhoArquivoDesenho($diretorioBruto, $arquivoBruto, $tentativas);
    if ($caminhoArquivo === null) {
      return '-';
    }

    $bytes = @filesize($caminhoArquivo);
    if ($bytes === false || (int) $bytes < 0) {
      return '-';
    }

    return $this->formatarTamanhoArquivo((int) $bytes);
  }

  private function obterAreaChapaMdfM2(): float
  {
    $areaDireta = env('RELATORIO_MDF_CHAPA_M2');
    if ($areaDireta !== null && $areaDireta !== '' && is_numeric((string) $areaDireta)) {
      $valor = (float) $areaDireta;
      if ($valor > 0) {
        return $valor;
      }
    }

    $largura = env('RELATORIO_MDF_CHAPA_LARGURA_M');
    $altura = env('RELATORIO_MDF_CHAPA_ALTURA_M');
    if (
      $largura !== null && $largura !== '' && is_numeric((string) $largura) &&
      $altura !== null && $altura !== '' && is_numeric((string) $altura)
    ) {
      $area = (float) $largura * (float) $altura;
      if ($area > 0) {
        return $area;
      }
    }

    // Padrao solicitado: 5,08 m2 por chapa MDF.
    return 5.08;
  }

  private function montarSecaoGastoMaterialHtml(
    int $itensDxf,
    int $itensComArea,
    int $itensSemMedicao,
    int $itensArquivoInexistente,
    float $areaBaseM2,
    float $areaComMargemM2,
    float $margemPercentual = 10.0
  ): string {
    $areaChapaMdfM2 = $this->obterAreaChapaMdfM2();
    $qtdChapasMdfEstimada = ($areaComMargemM2 > 0 && $areaChapaMdfM2 > 0)
      ? round($areaComMargemM2 / $areaChapaMdfM2, 2)
      : 0.0;

    $headerTr = '<tr style="background-color: white;">
      <th>Itens DXF no periodo</th>
      <th>Itens com area</th>
      <th>Itens sem medicao</th>
      <th>Area (m&sup2;)</th>
      <th>Qtd. chapas MDF estimadas</th>
    </tr>';

    $bodyTr = '<tr style="background-color: white;">
      <td>' . $itensDxf . '</td>
      <td>' . $itensComArea . '</td>
      <td>' . $itensSemMedicao . '</td>
      <td>' . $this->formatarM2($areaComMargemM2) . '</td>
      <td>' . $this->formatarQuantidade($qtdChapasMdfEstimada, 2) . '</td>
    </tr>';

    return $this->buildTitleTableSectionWithMinRows(
      'h2',
      'Gasto estimado de material (DXF)',
      'class="table tabela"',
      $headerTr,
      $bodyTr,
      '',
      1
    );
  }

  private function mensagemSemDadosFiltros(?int $empresaId = null, ?int $empreendimentoId = null): string
  {
    $filtros = ['Periodo', 'Processo'];
    if ($empresaId !== null) {
      $filtros[] = 'Empresa/Cliente';
    }
    if ($empreendimentoId !== null) {
      $filtros[] = 'Empreendimento';
    }

    return 'Sem dados para os filtros selecionados (' . implode('/', $filtros) . ').';
  }

  /**
   * Gera PDF minimo para confirmacao de geracao mesmo sem dados.
   */
  private function respostaPdfSemDados(
    string $processo,
    string $dataInicial_str,
    string $dataFinal_str,
    string $empresaNomeFiltro,
    string $empreendimentoNomeFiltro,
    string $mensagem,
    array $debugExtra = []
  ) {
    if ($debugExtra !== []) {
      $this->relatorioDebugMerge($debugExtra);
    }

    $this->relatorioDebugSet('sem_dados_confirmado', true);
    $this->relatorioDebugSet('sem_dados_mensagem', $mensagem);

    $mpdf = new Mpdf();

    $html = '
      <style>
        body { font-family: Verdana, Geneva, Tahoma, sans-serif; font-size: 12px; }
        h1 { font-size: 22px; margin: 0; }
        .muted { color: #666; }
      </style>
      <h1>WL Maquetaria</h1>
      <br/>
      <table style="width: 100%; border: 0;">
        <tr>
          <td><b>Relatorio do processo: ' . htmlspecialchars($processo, ENT_QUOTES, 'UTF-8') . '</b></td>
          <td style="text-align:right;">Periodo: ' . date("d/m/Y", strtotime($dataInicial_str)) . ' a ' . date("d/m/Y", strtotime($dataFinal_str)) . '</td>
        </tr>
        <tr>
          <td></td>
          <td style="text-align:right;">Emissao: ' . date('d/m/Y H:i') . '</td>
        </tr>
        <tr>
          <td><b>Empresa/Cliente:</b> ' . htmlspecialchars($empresaNomeFiltro, ENT_QUOTES, 'UTF-8') . '</td>
          <td style="text-align:right;"><b>Empreendimento:</b> ' . htmlspecialchars($empreendimentoNomeFiltro, ENT_QUOTES, 'UTF-8') . '</td>
        </tr>
      </table>
      <br/><br/>
      <p><b>' . htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') . '</b></p>
      <p class="muted">Este PDF foi gerado por confirmacao manual, mesmo sem registros para os filtros informados.</p>
    ';

    $mpdf->WriteHTML($html);
    $pdfContent = $mpdf->Output('', 'S');
    $pdfBase64 = base64_encode($pdfContent);

    $this->relatorioDebugSet('pdf', [
      'bytes' => strlen($pdfContent),
      'base64_length' => strlen($pdfBase64),
      'tipo' => 'sem_dados'
    ]);

    return $this->respostaJsonRelatorio([
      'ok' => true,
      'q' => [],
      'pdf' => $pdfBase64,
      'nome_pdf' => 'Relatorio_WL_' .
        date("d_m_Y", strtotime($dataInicial_str)) .
        '_a_' .
        date("d_m_Y", strtotime($dataFinal_str)) .
        '.pdf'
    ]);
  }

  /**
   * Resolve processo por nome (normal ou codificado) e retorna id/nome persistido.
   */
  private function resolverProcesso(string $processo, ?int $processoId = null): ?array
  {
    $processosModel = new \App\Models\Processos();
    $processoRow = null;

    if ($processoId !== null) {
      $processoRow = $processosModel
        ->select('id,nome')
        ->where('id', $processoId)
        ->first();
    }

    if (!$processoRow) {
      $processoRow = $processosModel
        ->select('id,nome')
        ->where('nome', $processo)
        ->first();
    }

    if (!$processoRow && $processo !== '') {
      $processoCodificado = Ferramentas::codificador($processo);
      if ($processoCodificado !== '') {
        $processoRow = $processosModel
          ->select('id,nome')
          ->where('nome', $processoCodificado)
          ->first();
      }
    }

    if (!is_array($processoRow) || !isset($processoRow['id'])) {
      return null;
    }

    return [
      'id' => (int) $processoRow['id'],
      'nome' => (string) ($processoRow['nome'] ?? $processo)
    ];
  }

  /**
   * Logs de diagnostico do relatorio (temporarios). Controle por env RELATORIO_DEBUG.
   */
  private function relatorioDebugAtivo(): bool
  {
    // Para geracao de relatorio, mantemos debug no JSON por padrao
    // para diagnostico de arquivos sem metrica de area.
    if (isset($this->request) && $this->request->isAJAX()) {
      return true;
    }

    $flag = env('RELATORIO_DEBUG');
    if ($flag === null || $flag === '') {
      return true;
    }
    return filter_var($flag, FILTER_VALIDATE_BOOLEAN);
  }

  private function relatorioLog(string $level, string $message): void
  {
    if (!$this->relatorioDebugAtivo()) {
      return;
    }
    log_message($level, $message);
  }

  private function relatorioDebugReset(string $modo, array $data = []): void
  {
    if (!$this->relatorioDebugAtivo()) {
      $this->relatorioDebugContext = [];
      return;
    }

    $this->relatorioDebugContext = array_merge([
      'modo' => $modo,
      'timestamp' => date('c'),
      'request_uri' => (string) ($this->request->getServer('REQUEST_URI') ?? '')
    ], $data);
  }

  private function relatorioDebugSet(string $key, $value): void
  {
    if (!$this->relatorioDebugAtivo()) {
      return;
    }

    $this->relatorioDebugContext[$key] = $value;
  }

  private function relatorioDebugMerge(array $data): void
  {
    if (!$this->relatorioDebugAtivo()) {
      return;
    }

    $this->relatorioDebugContext = array_merge($this->relatorioDebugContext, $data);
  }

  private function respostaJsonRelatorio(array $payload)
  {
    if ($this->relatorioDebugAtivo()) {
      $payload['debug'] = $this->relatorioDebugContext;
    }

    return $this->response->setJSON($payload);
  }




  /**
   * Gera um relatório de desenhos e cortes, com base em dados enviados via AJAX, e retorna um PDF com o conteúdo do relatório.
   * 
   * A função realiza diversas verificações sobre as datas, participantes, e status dos desenhos e cortes.
   * Os dados são coletados de várias tabelas do banco de dados e processados para criar o relatório, que é gerado em formato PDF.
   * 
   */
  function relatorio_mult()
  {
    if ($this->request->isAJAX()) {
      session_start();


      $dataFinal_str = trim((string) service('request')->getPost('dataFinal'));
      $dataInicial_str = trim((string) service('request')->getPost('dataInicial'));
      $relatorio = service('request')->getPost('relatorio');
      $selectedValues = service('request')->getPost('selectedValues');
      $processo = trim((string) service('request')->getPost('processo'));
      $processoIdRequest = $this->normalizarFiltroId(service('request')->getPost('processoId'));
      $empresaId = $this->normalizarFiltroId(service('request')->getPost('empresaId'));
      $empreendimentoId = $this->normalizarFiltroId(service('request')->getPost('empreendimentoId'));
      $periodoAdicionado = filter_var(service('request')->getPost('periodoAdicionado'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
      $periodoFinalizado = filter_var(service('request')->getPost('periodoFinalizado'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
      $gerarSemDados = filter_var(service('request')->getPost('gerarSemDados'), FILTER_VALIDATE_BOOLEAN);
      $periodoAdicionado = $periodoAdicionado ?? true;
      $periodoFinalizado = $periodoFinalizado ?? false;
      if ($empresaId === null) {
        $empreendimentoId = null;
      }
      $tipoRelatorio = ($relatorio == "true") ? 'analitico' : 'sintetico';
      $msgSemDados = $this->mensagemSemDadosFiltros($empresaId, $empreendimentoId);
      $msg = array();

      $this->relatorioDebugReset('mult', [
        'inputs' => [
          'dataInicial' => $dataInicial_str,
          'dataFinal' => $dataFinal_str,
          'relatorio' => $relatorio,
          'tipoRelatorio' => $tipoRelatorio,
          'processo' => $processo,
          'processoIdRequest' => $processoIdRequest,
          'empresaId' => $empresaId,
          'empreendimentoId' => $empreendimentoId,
          'periodoAdicionado' => $periodoAdicionado,
          'periodoFinalizado' => $periodoFinalizado,
          'gerarSemDados' => $gerarSemDados
        ],
        'selectedValues' => $selectedValues
      ]);

      if ($processo === "") {
        $msg["Processo"] = "É preciso selecionar um processo.";
      }
      if ($dataFinal_str == "") {
        $msg["Data Final"] = "É precisso selecionar uma data final.";
      }
      if ($dataInicial_str == "") {
        $msg["Data Inicial"] = "É precisso selecionar uma data inicial.";
      }
      if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $dataFinal_str)) {
        $msg["Data Final"] = "É precisso selecionar uma data final valida.";
      }
      if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $dataInicial_str)) {
        $msg["Data Inicial"] = "É precisso selecionar uma data inicial valida.";
      }

      $dataFinal = strtotime($dataFinal_str . ' 23:59:59');
      $dataInicial = strtotime($dataInicial_str . ' 00:00:00');

      // Compare as datas
      if (!($dataFinal >= $dataInicial)) {
        $msg["Data Inicial"] = "A data final não pode ser anterior à data inicial.";
      }

      //se possuir algum erro ele retorna e finaliça a função
      if (!$periodoAdicionado && !$periodoFinalizado) {
        $msg["Periodo"] = "Selecione pelo menos um tipo de periodo: Adicionado ou Finalizado.";
      }

      if ($msg != []) {
        return $this->respostaErroRelatorio($msg);
      }

      $processoRow = $this->resolverProcesso($processo, $processoIdRequest);
      if (!is_array($processoRow) || !isset($processoRow['id'])) {
        return $this->respostaErroRelatorio([
          'Processo' => 'Processo informado nao foi encontrado.'
        ]);
      }
      $processoId = (int) $processoRow['id'];
      $processoNomeBanco = (string) ($processoRow['nome'] ?? $processo);

      $this->relatorioDebugSet('processo_resolvido', [
        'id' => $processoId,
        'nome_db' => $processoNomeBanco
      ]);
      $mostrarSecaoMaterial = $this->processoEhCorteLaser($processoId, $processoNomeBanco);
      $this->relatorioDebugSet('material_habilitado', $mostrarSecaoMaterial);










      // Inicialização de objetos para acessar tabelas do banco de dados
      $desenhos = new \App\Models\Desenhos();
      $prioridade = new \App\Models\Prioridade();
      $finalidade = new \App\Models\Finalidade();
      $empresa = new \App\Models\Empresa();
      $empreendimento = new \App\Models\Empreendimentos();
      $usuario = new \App\Models\Usuarios();
      $cortado = new \App\Models\Corte();
      $processos = new \App\Models\Processos();

      $empresaNomeFiltro = 'Todas';
      $empreendimentoNomeFiltro = 'Todos';

      if ($empresaId !== null) {
        $empresaRow = $empresa
          ->select('id, nome')
          ->where('id', $empresaId)
          ->first();

        if (!is_array($empresaRow) || !isset($empresaRow['id'])) {
          $msg['Empresa/Cliente'] = 'Empresa selecionada nao foi encontrada.';
        } else {
          $empresaNomeFiltro = $this->nomeLegivel((string) ($empresaRow['nome'] ?? ''));
        }
      }

      if ($empreendimentoId !== null) {
        $empreendimentoRow = $empreendimento
          ->select('id, nome, empresa_id')
          ->where('id', $empreendimentoId)
          ->first();

        if (!is_array($empreendimentoRow) || !isset($empreendimentoRow['id'])) {
          $msg['Empreendimento'] = 'Empreendimento selecionado nao foi encontrado.';
        } else {
          $empreendimentoNomeFiltro = $this->nomeLegivel((string) ($empreendimentoRow['nome'] ?? ''));

          if (
            $empresaId !== null &&
            isset($empreendimentoRow['empresa_id']) &&
            (int) $empreendimentoRow['empresa_id'] !== $empresaId
          ) {
            $msg['Empreendimento'] = 'Empreendimento selecionado nao pertence a empresa/cliente informado.';
          }
        }
      }

      if ($msg != []) {
        return $this->respostaErroRelatorio($msg);
      }

      // Recupera dados das tabelas do banco de dados
      $prioridade_data = $prioridade->find();
      $finalidade_data = $finalidade->find();
      $empresa_data = $empresa->find();
      $empreendimento_data = $empreendimento->find();
      $desenhos_data = $desenhos->find();
      $usuario_data = $usuario->find();
      $cortado_data = $cortado->find();
      $processos_data = $processos->find();

      $corte = array();
      $desenhista = array();


      // Extrai participantes selecionados (ids + função) com base no payload atual.
      [$participantIds, $participantesResumo] = $this->extrairParticipantesSelecionados($selectedValues);
      $filtrarParticipantes = !empty($participantIds);

      $this->relatorioDebugSet('participantes', [
        'filtrar' => $filtrarParticipantes,
        'ids' => $participantIds,
        'qtd' => count($participantIds),
        'resumo_inicial' => $participantesResumo
      ]);


      $this->relatorioLog(
        'info',
        '[Relatorio] modo=mult tipo=' . $tipoRelatorio .
          ' processo_front="' . $processo . '"' .
          ' processo_db="' . $processoNomeBanco . '"' .
          ' processo_id=' . $processoId .
          ' periodo=' . $dataInicial_str . '->' . $dataFinal_str .
          ' periodo_tipo=' . ($periodoAdicionado ? 'adicionado' : '') . ($periodoAdicionado && $periodoFinalizado ? '+' : '') . ($periodoFinalizado ? 'finalizado' : '') .
          ' participantes=' . count($participantIds) .
          ' empresa_filtro=' . ($empresaId !== null ? (string) $empresaId : 'todos') .
          ' empreendimento_filtro=' . ($empreendimentoId !== null ? (string) $empreendimentoId : 'todos') .
          ' selectedValues=' . (json_encode($selectedValues, JSON_UNESCAPED_UNICODE) ?: 'null')
      );

      $db = \Config\Database::connect();

      $diagBase = $db
        ->table('desenhos d')
        ->select('d.id')
        ->join('corte c', 'c.id = d.corte_id', 'left')
        ->where('d.processos_id', $processoId);

      if ($periodoAdicionado && $periodoFinalizado) {
        $diagBase
          ->groupStart()
            ->groupStart()
              ->where('d.data_add >=', $dataInicial_str . ' 00:00:00')
              ->where('d.data_add <=', $dataFinal_str . ' 23:59:59')
            ->groupEnd()
            ->orGroupStart()
              ->where('c.data_end >=', $dataInicial_str . ' 00:00:00')
              ->where('c.data_end <=', $dataFinal_str . ' 23:59:59')
            ->groupEnd()
          ->groupEnd();
      } elseif ($periodoAdicionado) {
        $diagBase
          ->where('d.data_add >=', $dataInicial_str . ' 00:00:00')
          ->where('d.data_add <=', $dataFinal_str . ' 23:59:59');
      } else {
        $diagBase
          ->where('c.data_end >=', $dataInicial_str . ' 00:00:00')
          ->where('c.data_end <=', $dataFinal_str . ' 23:59:59');
      }

      if ($filtrarParticipantes) {
        $diagBase
          ->groupStart()
            ->whereIn('d.usuario_id_desenhista', $participantIds)
            ->orWhereIn('c.usuario_id_fim', $participantIds)
          ->groupEnd();
      }

      $diagA = clone $diagBase;
      $diagASql = preg_replace('/\s+/', ' ', trim($diagA->getCompiledSelect(false)));
      $diagACount = (int) $diagA->countAllResults(false);

      $diagB = clone $diagBase;
      if ($empresaId !== null) {
        $diagB->where('d.empresa_id', $empresaId);
      }
      $diagBSql = preg_replace('/\s+/', ' ', trim($diagB->getCompiledSelect(false)));
      $diagBCount = (int) $diagB->countAllResults(false);

      $diagC = clone $diagB;
      if ($empreendimentoId !== null) {
        $diagC->where('d.empreendimentos_id', $empreendimentoId);
      }
      $diagCSql = preg_replace('/\s+/', ' ', trim($diagC->getCompiledSelect(false)));
      $diagCCount = (int) $diagC->countAllResults(false);

      $this->relatorioLog(
        'info',
        '[RelatorioDiag] modo=mult etapaA=' . $diagACount .
          ' etapaB=' . $diagBCount .
          ' etapaC=' . $diagCCount
      );
      $this->relatorioLog('debug', '[RelatorioDiag][mult][SQL A] ' . $diagASql);
      $this->relatorioLog('debug', '[RelatorioDiag][mult][SQL B] ' . $diagBSql);
      $this->relatorioLog('debug', '[RelatorioDiag][mult][SQL C] ' . $diagCSql);

      $this->relatorioDebugSet('diagnostico_sql', [
        'A' => ['count' => $diagACount, 'sql' => $diagASql],
        'B' => ['count' => $diagBCount, 'sql' => $diagBSql],
        'C' => ['count' => $diagCCount, 'sql' => $diagCSql]
      ]);

      $builder = $db
          ->table('desenhos d')
          ->select([
              'd.id',
              'd.usuario_id_desenhista',
              'd.nome               AS arquivo',
              'd.diretorio',
              'd.data_add           AS data_add',
              'd.status',
              'pr.nome              AS prioridade_nome',
              'pr.cor               AS prioridade_cor',
              'e.nome               AS empresa_nome',
              'emp.nome             AS empreendimento_nome',
              'f.nome               AS finalidade_nome',
              'u.nome               AS desenhista_nome',
              'p.nome               AS processo_nome',
              'c.data_add           AS corte_inicio',
              'c.data_end           AS corte_fim',
              'c.usuario_id_fim     AS cortador_id',
              'cu.nome              AS cortador_nome'
          ])
          ->join('prioridade pr',      'pr.id     = d.prioridade_id',         'left')
          ->join('empresa e',          'e.id      = d.empresa_id',            'left')
          ->join('empreendimentos emp','emp.id    = d.empreendimentos_id',    'left')
          ->join('finalidade f',       'f.id      = d.finalidade_id',         'left')
          ->join('processos p',        'p.id      = d.processos_id',          'inner')
          ->join('usuarios u',         'u.id      = d.usuario_id_desenhista', 'left')
          ->join('corte c',            'c.id      = d.corte_id',              'left')
          ->join('usuarios cu',        'cu.id     = c.usuario_id_fim',        'left')
          // filtro por processo padronizado por ID
          ->where('d.processos_id', $processoId)
          // só traz quem é desenhista OU cortador entre os selecionados
          // ordenação principal por prioridade
          ->orderBy('pr.ordem', 'ASC');

      if ($periodoAdicionado && $periodoFinalizado) {
        $builder
          ->groupStart()
            ->groupStart()
              ->where('d.data_add >=', $dataInicial_str . ' 00:00:00')
              ->where('d.data_add <=', $dataFinal_str . ' 23:59:59')
            ->groupEnd()
            ->orGroupStart()
              ->where('c.data_end >=', $dataInicial_str . ' 00:00:00')
              ->where('c.data_end <=', $dataFinal_str . ' 23:59:59')
            ->groupEnd()
          ->groupEnd();
      } elseif ($periodoAdicionado) {
        $builder
          ->where('d.data_add >=', $dataInicial_str . ' 00:00:00')
          ->where('d.data_add <=', $dataFinal_str . ' 23:59:59');
      } else {
        $builder
          ->where('c.data_end >=', $dataInicial_str . ' 00:00:00')
          ->where('c.data_end <=', $dataFinal_str . ' 23:59:59');
      }

      if ($filtrarParticipantes) {
        $builder
          ->groupStart()
            ->whereIn('d.usuario_id_desenhista', $participantIds)
            ->orWhereIn('c.usuario_id_fim', $participantIds)
          ->groupEnd();
      }

      if ($empresaId !== null) {
        $builder->where('d.empresa_id', $empresaId);
      }
      if ($empreendimentoId !== null) {
        $builder->where('d.empreendimentos_id', $empreendimentoId);
      }

      // Algumas bases não possuem a coluna desenhos.ordem
      $desenhosHasOrdem = $db->fieldExists('ordem', 'desenhos');
      $this->relatorioDebugSet('desenhos_has_ordem', $desenhosHasOrdem);
      if ($desenhosHasOrdem) {
        $builder->orderBy('d.ordem', 'ASC');
      } else {
        $builder->orderBy('d.id', 'ASC');
      }

      $builderFinalSql = preg_replace('/\s+/', ' ', trim($builder->getCompiledSelect(false)));
      $this->relatorioDebugSet('sql_final', $builderFinalSql);

      $desenhos_data = $builder
          ->get()
          ->getResultArray();

      $totalRowsQuery = count($desenhos_data);
      $this->relatorioDebugSet('query_resultado', [
        'rows' => $totalRowsQuery,
        'sample' => array_slice($desenhos_data, 0, 10)
      ]);
      if ($totalRowsQuery === 0) {
        log_message(
          'info',
          '[Relatorio] modo=mult tipo=' . $tipoRelatorio .
            ' processo="' . $processo . '"' .
            ' periodo=' . $dataInicial_str . '->' . $dataFinal_str .
            ' participantes=' . count($participantIds) .
            ' empresa_filtro=' . ($empresaId !== null ? (string) $empresaId : 'todos') .
            ' empreendimento_filtro=' . ($empreendimentoId !== null ? (string) $empreendimentoId : 'todos') .
            ' rows=0 publicacoes=0 finalizacoes=0'
        );
        $this->relatorioDebugSet('sem_dados', [
          'motivo' => $msgSemDados,
          'gerarSemDados' => $gerarSemDados
        ]);

        if (!$gerarSemDados) {
          return $this->respostaErroRelatorio([
            'Relatorio' => $msgSemDados
          ]);
        }

        return $this->respostaPdfSemDados(
          $processo,
          $dataInicial_str,
          $dataFinal_str,
          $empresaNomeFiltro,
          $empreendimentoNomeFiltro,
          $msgSemDados
        );
      }

      $materialCacheMult = $mostrarSecaoMaterial
        ? $this->garantirAreasMaterialPorDesenho($desenhos_data, $processoId)
        : [];
      $this->relatorioDebugSet('material_cache_mult', $materialCacheMult);

      $areasMaterialPorDesenho = $mostrarSecaoMaterial
        ? $this->carregarAreasMaterialPorDesenho(array_column($desenhos_data, 'id'), $processoId)
        : [];
      $materialItensDxf = 0;
      $materialItensComArea = 0;
      $materialItensSemMedicao = 0;
      $materialItensArquivoInexistente = 0;
      $materialAreaBaseM2 = 0.0;
      $materialAreaComMargemM2 = 0.0;

      // Itera sobre os dados de desenhos para criar a lista
      $totalPublicacoes = 0;
      $totalFinalizacoes = 0;
      $loopDebug = [
        'linhas' => count($desenhos_data),
        'in_periodo_true' => 0,
        'ok_desenhista_true' => 0,
        'ok_cortador_true' => 0,
        'status_finalizacao_true' => 0,
        'publicacoes_add' => 0,
        'finalizacoes_add' => 0
      ];
      foreach ($desenhos_data as $key => $value) {

        $desenhistaIdAtual = (int) ($value['usuario_id_desenhista'] ?? 0);
        $cortadorIdAtual = (int) ($value['cortador_id'] ?? 0);
        $statusRaw = trim((string) ($value['status'] ?? ''));
        $statusDecodificado = trim((string) Ferramentas::decodificador($statusRaw));
        if ($statusDecodificado === '') {
          $statusDecodificado = $statusRaw;
        }

        $ok_desenhista = !$filtrarParticipantes || in_array($desenhistaIdAtual, $participantIds, true);
        $ok_cortador   = !$filtrarParticipantes || in_array($cortadorIdAtual, $participantIds, true);
        if ($ok_desenhista) {
          $loopDebug['ok_desenhista_true']++;
        }
        if ($ok_cortador) {
          $loopDebug['ok_cortador_true']++;
        }

        // Converter a data específica para timestamp
        $dataEspecifica_desenho_add = strtotime((string) ($value['data_add'] ?? ''));
        $dataEspecifica_corte = strtotime((string) ($value['corte_fim'] ?? ''));
        $inPeriodoAdicao = $periodoAdicionado && $dataEspecifica_desenho_add !== false && ($dataEspecifica_desenho_add >= $dataInicial && $dataEspecifica_desenho_add <= $dataFinal);
        $inPeriodoFinalizacao = $periodoFinalizado && $dataEspecifica_corte !== false && ($dataEspecifica_corte >= $dataInicial && $dataEspecifica_corte <= $dataFinal);
        $inPeriodoSelecionado = $inPeriodoAdicao || $inPeriodoFinalizacao;
        if ($dataEspecifica_desenho_add === false && $dataEspecifica_corte === false) {
          $inPeriodoSelecionado = true;
        }
        if ($inPeriodoSelecionado) {
          $loopDebug['in_periodo_true']++;
        }

        $desenhoIdAtual = (int) ($value['id'] ?? 0);
        $extensaoAtual = strtolower((string) pathinfo((string) ($value['diretorio'] ?? ''), PATHINFO_EXTENSION));
        if ($extensaoAtual === '' && preg_match('/_([a-z0-9]{2,5})$/i', (string) ($value['arquivo'] ?? ''), $matchExt)) {
          $extensaoAtual = strtolower((string) ($matchExt[1] ?? ''));
        }
        if ($mostrarSecaoMaterial && $inPeriodoSelecionado && $extensaoAtual === 'dxf') {
          $materialItensDxf++;
          if ($desenhoIdAtual > 0 && isset($areasMaterialPorDesenho[$desenhoIdAtual])) {
            $areaInfo = $areasMaterialPorDesenho[$desenhoIdAtual];
            $statusArea = (string) ($areaInfo['status'] ?? 'ok');
            if ($statusArea === 'ok') {
              $materialItensComArea++;
              $materialAreaBaseM2 += (float) ($areaInfo['area_m2'] ?? 0);
              $materialAreaComMargemM2 += (float) ($areaInfo['area_m2_com_margem'] ?? 0);
            } else {
              $materialItensSemMedicao++;
              if ($statusArea === 'arquivo_nao_encontrado') {
                $materialItensArquivoInexistente++;
              }
            }
          } else {
            $materialItensSemMedicao++;
          }
        }


        $tags = explode('/', Ferramentas::decodificador((string) ($value['diretorio'] ?? '')));
        // Remover os índices de 0 a 5
        $tags = array_slice($tags, 6);

        // Remover o último elemento
        unset($tags[count($tags) - 1]);
        $tags = implode(" - ", $tags);

        //verifica se o desenho esta no período selecionado 
        $isStatusFinalizacao = in_array($statusDecodificado, ['pronto', 'cortado_notfile', 'cortado'], true);
        if ($isStatusFinalizacao) {
          $loopDebug['status_finalizacao_true']++;
        }

        if ($isStatusFinalizacao && $inPeriodoSelecionado) {

          if ($ok_cortador && isset($participantesResumo[$cortadorIdAtual])) {
            $participantesResumo[$cortadorIdAtual]['finalizacoes']++;
            $totalFinalizacoes++;
            $loopDebug['finalizacoes_add']++;
          }

  

          // Convertendo as datas para timestamps Unix


          $timestamp1 = strtotime((string) ($value['corte_fim'] ?? ''));
          $timestamp2 = strtotime((string) ($value['corte_inicio'] ?? ''));

          // Calculando a diferença em segundos
          $diferencaSegundos = abs($timestamp2 - $timestamp1);

          // Convertendo a diferença para horas, minutos e segundos
          $horas = floor($diferencaSegundos / 3600);
          $minutos = floor(($diferencaSegundos % 3600) / 60);
          $segundos = $diferencaSegundos % 60;

          // Formatando a diferença como "horas:minutos:segundos"
          $diferencaHoras = sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);
          if (($value['corte_fim'] ?? null) == null or ($value['corte_inicio'] ?? null) == null) {
            $diferencaHoras = "00:00:00";
          }

          // Armazena no array de corte o cortador correspondente a este desenho, incluindo suas respectivas informações.
          $corte[(string) ($value['cortador_nome'] ?? 'Nao identificado')][] =
            [
              "desenhista" => (string) ($value['desenhista_nome'] ?? ''),
              "nome_arquivo" => (Ferramentas::remove_id_file((string) ($value['arquivo'] ?? ''))),
              "empresa" => (string) ($value['empresa_nome'] ?? ''),
              "empreendimento" => (string) ($value['empreendimento_nome'] ?? ''),
              "finalidade" => (string) ($value['finalidade_nome'] ?? ''),
              "tags" => $tags,
              "data_add" => Ferramentas::decodificador(Ferramentas::decodificador((string) ($value['data_add'] ?? ''))),
              "data_hora_corte" => (string) ($value['corte_fim'] ?? ''),
              "tempo_corte" => $diferencaHoras,
              "cortador" => (string) ($value['cortador_nome'] ?? ''),
              "status" => $statusRaw,
              "ok" => $ok_cortador

            ];
        } else {
          if ($statusDecodificado != 'apagado') {
            $statusRaw = 'pendente';
          }
        }






        // Caso o participante seja o criador do desenho, armazena no array as informações do criador e o arquivo associado.
        if ($inPeriodoSelecionado) {

          if ($ok_desenhista && isset($participantesResumo[$desenhistaIdAtual])) {
            $participantesResumo[$desenhistaIdAtual]['publicacoes']++;
            $totalPublicacoes++;
            $loopDebug['publicacoes_add']++;
          }



          $desenhista[(string) ($value['desenhista_nome'] ?? 'Nao identificado')][] =
            [
              "desenhista" => (string) ($value['desenhista_nome'] ?? ''),
              "nome_arquivo" => (Ferramentas::remove_id_file((string) ($value['arquivo'] ?? ''))),
              "tamanho_arquivo" => $this->obterTamanhoArquivoDesenho(
                (string) ($value['diretorio'] ?? ''),
                (string) ($value['arquivo'] ?? '')
              ),
              "empresa" => (string) ($value['empresa_nome'] ?? ''),
              "empreendimento" => (string) ($value['empreendimento_nome'] ?? ''),
              "finalidade" => (string) ($value['finalidade_nome'] ?? ''),
              "tags" => $tags,
              "data_add" => (string) ($value['data_add'] ?? ''),
              "status" => $statusRaw,
              "ok" => $ok_desenhista

            ];
        }
      }

      $this->relatorioDebugSet('loop_contadores', $loopDebug);

      $totalDetalhesDesenhista = 0;
      foreach ($desenhista as $rowsDesenhista) {
        foreach ($rowsDesenhista as $itemDesenhista) {
          if (!empty($itemDesenhista['ok'])) {
            $totalDetalhesDesenhista++;
          }
        }
      }

      $totalDetalhesCortador = 0;
      foreach ($corte as $rowsCorte) {
        foreach ($rowsCorte as $itemCorte) {
          if (!empty($itemCorte['ok'])) {
            $totalDetalhesCortador++;
          }
        }
      }

      // Mantemos "sem dados" apenas quando a query principal retorna 0 linhas.

      $participantesSelecionadosTr = '';
      $participantesTr = '';
      $participantesTotalPublicacoes = 0;
      $participantesTotalFinalizacoes = 0;
      $participantesCount = count($participantesResumo);
      $participanteIdx = 0;

      foreach ($participantesResumo as $participante) {
        $participanteIdx++;
        $numeroParticipante = str_pad((string) $participanteIdx, max(2, strlen((string) max(1, $participantesCount))), '0', STR_PAD_LEFT);
        $nomeParticipante = htmlspecialchars((string) ($participante['nome'] ?? ''), ENT_QUOTES, 'UTF-8');
        $funcaoParticipante = htmlspecialchars((string) ($participante['funcao'] ?? 'Participante'), ENT_QUOTES, 'UTF-8');
        $publicacoes = (int) ($participante['publicacoes'] ?? 0);
        $finalizacoes = (int) ($participante['finalizacoes'] ?? 0);
        $participantesTotalPublicacoes += $publicacoes;
        $participantesTotalFinalizacoes += $finalizacoes;

        $participantesSelecionadosTr .= '<tr style="background-color: white;">
          <td>' . $numeroParticipante . '</td>
          <td>' . $nomeParticipante . '</td>
          <td>' . $funcaoParticipante . '</td>
        </tr>';

        $participantesTr .= '<tr style="background-color: white;">
          <td>' . $numeroParticipante . '</td>
          <td>' . $nomeParticipante . '</td>
          <td>' . $funcaoParticipante . '</td>
          <td>' . $publicacoes . '</td>
          <td>' . $finalizacoes . '</td>
        </tr>';
      }

      if ($participantesSelecionadosTr === '') {
        $participantesSelecionadosTr = '<tr style="background-color: white;">
          <td>01</td>
          <td>Todos os participantes</td>
          <td>Sem filtro</td>
        </tr>';
      }

      if ($participantesTr === '') {
        $participantesTr = '<tr style="background-color: white;">
          <td>01</td>
          <td>Todos os participantes</td>
          <td>Sem filtro</td>
          <td>-</td>
          <td>-</td>
        </tr>';
      }

      log_message(
        'info',
        '[Relatorio] modo=mult tipo=' . $tipoRelatorio .
          ' processo="' . $processo . '"' .
          ' periodo=' . $dataInicial_str . '->' . $dataFinal_str .
          ' participantes=' . $participantesCount .
          ' empresa_filtro=' . ($empresaId !== null ? (string) $empresaId : 'todos') .
          ' empreendimento_filtro=' . ($empreendimentoId !== null ? (string) $empreendimentoId : 'todos') .
          ' rows=' . $totalRowsQuery .
          ' publicacoes=' . $participantesTotalPublicacoes .
          ' finalizacoes=' . $participantesTotalFinalizacoes
      );

      $materialSectionHtml = '';
      if ($mostrarSecaoMaterial) {
        $materialSectionHtml = $this->montarSecaoGastoMaterialHtml(
          $materialItensDxf,
          $materialItensComArea,
          $materialItensSemMedicao,
          $materialItensArquivoInexistente,
          $materialAreaBaseM2,
          $materialAreaComMargemM2,
          10.0
        );
      }

         $style = '
          <style>
              html, body {
                  height: 100%;
                  width: 100%;
                  margin: 0;
                  padding: 0;
                  display: table;
                  font-family: Verdana, Geneva, Tahoma, sans-serif;
              }
              
              div.content {
                  display: table-row;
                  height: 100%;
              }
              
              div.list {
                  display: table-footer-group;
                  width: 100%;
              }
              
              .espaco {
                  height: 30px;
                  background-color: rgba(0,0,0,0);
              }
              
              .tabela h3 {
                  color: #666;
                  margin-bottom: 30px;
              }
              
              .tabela dt {
                  float: left;
                  animation: none;
                  padding: 10px;
              }
              
              .tabela dl {
                  overflow: hidden;
              }
              
              .tabela th {
                  font-weight: 100;
                  text-align: left;
                  border: 0px solid black;
                  padding: 5px;
              }
              
              .tabela td {
                  text-align: left;
                  border: 0px solid black;
                  border-collapse: collapse;
                  padding: 5px;
              }

              .tabela td:first-child,
              .tabela th:first-child:not([colspan]) {
                  text-align: center;
              }
              
              .tabela table {
                  border-collapse: collapse;
                  width: 100%;
              }
              
              .tabela {
                  font-size: 12px;
                  width: 100%;
                  border-collapse: collapse;
              }
              
              .linha {
                  background-color: #dddddd;
              }
              
              .caixa {
                  border: 1px solid black;
                  border-collapse: collapse;
                  height: 60px;
                  width: 100%;
                  background-color: #E6E7E8;
                  display: flex;
                  align-items: center;
              }
              
              .caixa1 {
                  height: 100%;
                  width: 30px;
                  background-color: #0098DA;
              }
              
              .caixa2 {
                  height: 100%;
                  width: 30px;
                  background-color: #5CA47A;
              }
              
              .caixa3 {
                  height: 100%;
                  width: 30px;
                  background-color: #faa952;
              }
              
              .caixa div {
                  display: inline-block;
              }
              
              .text-center {
                  padding: 30px;
              }
              
              #nome {
                  text-align: center;
              }
              
              #nome_principal {
                  text-align: center;
              }
              
              .caixa-center {
                  text-align: center;
                  display: flex;
                  justify-content: center;
                  align-items: center;
                  height: 100%;
              }
              
              .container {
                  width: 100%;
                  max-width: 500px;
                  font-size: 16px;
              }
              
              .lista {
                  font-size: 12px;
                  width: 100%;
              }
              
              .lista li {
                  display: flex;
                  align-items: center;
                  margin-bottom: 8px;
                  font-size: 0.95em;
              }
              
              .lista li div {
                  text-align: left;
              }
              
              .num {
                  padding: 0px;
                  width: 30px;
                  text-align: center;
              }
              
              .nome {
                  padding: 0px;
                  height: 20px;
                  flex: 1;
                  background-color: #e0e0e0;
                  border: 1px solid black;
                  border-collapse: collapse;
              }
              
              .progress-container {
                  height: 12px;
                  width: 100%;
                  background-color: #e0e0e0;
                  display: flex;
              }
              
              .skill {
                  background-color: #FAA954;
              }
              
              .tabela table {
                  width: 100%;
                  max-width: 100%;
              }
              
              .tabela tr, .tabela th, .tabela td {
                  page-break-inside: avoid;
                  word-wrap: break-word;
              }

              .section-keep-min {
                  page-break-inside: avoid;
              }

              h2 {
                  font-weight: bold;
                  font-size: 1.5em;
                  margin: 0;
                  padding: 0;
                  line-height: normal;
                  text-transform: none;
                  letter-spacing: normal;
                  word-spacing: normal;
                  page-break-after: avoid;
                  page-break-inside: avoid;
              }
              h3 {
                  page-break-after: avoid;
                  page-break-inside: avoid;
              }
              
              h1 {
                  font-weight: bold;
                  font-size: 2em;
                  margin: 0;
                  padding: 0;
                  line-height: normal;
                  letter-spacing: normal;
                  word-spacing: normal;
              }
              
              .pontuacao {
                  padding: 5px;
                  width: 40px;
                  text-align: right;
              }
              
              .escala {
                  text-align: center;
                  margin-top: 20px;
              }
              
              .caixa-lista {
                  height: 90px;
                  width: 100%;
                  max-width: 364px;
              }
              
              .tabela table {
                  border-collapse: collapse;
                  width: 100%;
              }
              
              .tabela td, .tabela th {
                  border-top: 1px solid black;
                  border-bottom: 1px solid black;
                  border-left: none;
                  border-right: none;
                  padding: 5px;
              }
              
              .tabela tr {
                  min-height: 50px;
                
              }
              
              .page-break {
                  page-break-before: always;
              }
              
              
              </style>';





     
     
     
     
     
     
     
              $total_desenhos_tr = 0;
          // 2) Inicia o mPDF
    
    
          $total_desenhos_apagados_tr = 0;
      $desenhista_tr = "";
      $desenhistas_tr = "";
      $N = 0;
      $N1 = 0;
      $desenhistas = '';
      $totalN = array_reduce($desenhista, function ($carry, $item) {
        return $carry + count($item);
      }, 0);


      //faz a montagem dos usuarios que adicionaram algul arquivo
      foreach ($desenhista as $key => $value1) {

        $apagados = 0;
        $N1++;
        $total_desenhos_tr += count($value1);


        $temp_desenhista_tr = '<h3>' . $key . '</h3>
                 <table class="table tabela">
                <tr style="background-color: white;">
                  <th> N&ordm;</th>
                  <th> Nome do arquivo </th>
                  <th> Empresa/Cliente </th>
                  <th> Empreendimento </th>
                  <th> Finalidade </th>
                  <th> Data de Envio </th>
                  <th> Status </th>
                </tr>';
        $N = 0;
        foreach ($value1 as $value) {
          if (!$value["ok"])
            continue;

          if ($value["status"] == "apagado") {
            $total_desenhos_apagados_tr++;
            $apagados++;
          }

          $temp_nome_arquivo = $value["nome_arquivo"];
          if (strpos($temp_nome_arquivo, "cortado") === 0) {
            // Remove o prefixo e os 8 caracteres seguintes
            $temp_nome_arquivo = substr($temp_nome_arquivo, 15);
          }
          $N++;
          $temp_desenhista_tr .= '
            <tr style="background-color: white;">
              <th> ' . str_pad($N, strlen(strval($totalN)), '0', STR_PAD_LEFT) . '</th>
              <th> ' . $temp_nome_arquivo . ' </th>
              <th> ' . $value["empresa"] . ' </th>
              <th> ' . $value["empreendimento"] . ' </th>
              <th> ' . $value["finalidade"] . ' </th>
              <th> ' . $value["data_add"] . ' </th>
              <th> ' . str_replace(["cortado_notfile", "pendente"], ["cortado", "pendente"], $value["status"]) . ' </th>
            </tr>
            ';
        }
        $temp_desenhista_tr .= '</table>';
        if ($N != 0) {
          $desenhista_tr .= $temp_desenhista_tr;
          $desenhistas .= '<p>' . $key . '</p>';
        }

        $desenhistas_tr .= '<tr style="background-color: white;">
          <th> ' . str_pad($N1, strlen(strval(count($desenhista))), '0', STR_PAD_LEFT) . '</th>
          <th> ' . $key . ' </th>
          <th> ' . count($value1) . ' </th>
          <th> ' . $apagados . ' </th>
          <th> ' . abs(count($value1) - $apagados) . ' </th>
        </tr>';
      }
      $qtd_desenho = $N;

      $total_corte_tr = 0;
      $corte_tr = "";
      $cortes_tr = "";
      $N = 0;
      $N1 = 0;
      $cortadores = '';
   foreach ($corte as $key => $value1) {
    // para segurança, escapamos o texto
    $cortadores .= '<p>' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '</p>';
          $N1++;
          $totalTempoCorte = "";
          foreach ($value1 as $item) {
            if ($totalTempoCorte == "")
              $totalTempoCorte = $item["tempo_corte"];
            else
              $totalTempoCorte = $this->secondsToTime($this->timeToSeconds($item["tempo_corte"]) + $this->timeToSeconds($totalTempoCorte));
          }
          $total_corte_tr += count($value1);
          $cortes_tr .= '<tr style="background-color: white;">
              <th> ' . str_pad($N1, strlen(strval(count($corte))), '0', STR_PAD_LEFT) . '</th>
              <th> ' . $key . ' </th>
              <th> ' . count($value1) . ' </th>
              <th> ' . $totalTempoCorte . ' </th>
              <th> ' . $this->secondsToTime($this->timeToSeconds($totalTempoCorte) / count($value1)) . ' </th>
            </tr>';
}

      $indicadoresHeaderTr = '<tr style="background-color: white;">
                      <th style="width: 7px;"> N&ordm;</th>
                      <th> Nome </th>
                      <th> Fun&ccedil;&atilde;o </th>
                      <th> Publica&ccedil;&otilde;es </th>
                      <th> Finaliza&ccedil;&otilde;es </th>
                    </tr>';
      $indicadoresFooterTr = '<tr>
                      <th colspan="3">Total de participantes: ' . $participantesCount . '</th>
                      <th>' . $participantesTotalPublicacoes . '</th>
                      <th>' . $participantesTotalFinalizacoes . '</th>
                    </tr>';
      $indicadoresSectionHtml = '<h2>Indicadores dos participantes</h2>
                  <table class="table tabela">
                    ' . $indicadoresHeaderTr . '
                    ' . $participantesTr . '
                    ' . $indicadoresFooterTr . '
                  </table>';

      $participandoHeaderTr = '<tr style="background-color: white;">
                      <th style="text-align: left;">Projetistas participando</th>
                      <th style="text-align: left;">Cortados participando</th>
                    </tr>';
      $participandoRowsTr = '<tr style="background-color: white;">
                      <td style="text-align: left;">' . $desenhistas . '</td>
                      <td style="text-align: left;">' . $cortadores . '</td>
                    </tr>';
      $participandoSectionHtml = $this->buildTitleTableSectionWithMinRows(
        'h2',
        'Participando',
        'class="table tabela"',
        $participandoHeaderTr,
        $participandoRowsTr,
        '',
        1
      );

      $desenhistasResumoHeaderTr = '<tr style="background-color: white;">
                  <th style="width: 7px;"> N&ordm;</th>
                  <th style="width: 150px;"> Projetista </th>
                  <th> Quant. de desenhos </th>
                  <th> Quant. de desenhos apagados </th>
                  <th> Total de desenhos</th>
                  </tr>';
      $desenhistasResumoFooterTr = '<tr>
                  <th colspan="2">Total de desenhos : ' . $total_desenhos_tr . ' </th>
                  <th>Total de desenhos apagados: ' . $total_desenhos_apagados_tr . ' </th>
                  <th colspan="2"><b>Total de desenhos adicionados: ' . abs($total_desenhos_tr - $total_desenhos_apagados_tr) . ' </b></th>
                  </tr>';
      $desenhistasResumoSectionHtml = $this->buildTitleTableSectionWithMinRows(
        'h2',
        'Projetista',
        'class="table tabela"',
        $desenhistasResumoHeaderTr,
        $desenhistas_tr,
        $desenhistasResumoFooterTr,
        3
      );

      $cortadorResumoHeaderTr = '<tr style="background-color: white;">
                  <th> N&ordm;</th>
                  <th> Projetista </th>
                  <th> Quantidade de desenhos cortados </th>
                  <th> Tempo total de corte (h:m:s) </th>
                  <th> Tempo medio por corte (h:m:s)</th>
                  </tr>';
      $cortadorResumoFooterTr = '<tr>
                  <th colspan="5">Total de desenhos cortados: ' . $total_corte_tr . '</th>
                  </tr>';
      $cortadorResumoSectionHtml = $this->buildTitleTableSectionWithMinRows(
        'h2',
        'Cortador',
        'class="table tabela"',
        $cortadorResumoHeaderTr,
        $cortes_tr,
        $cortadorResumoFooterTr,
        3
      );

          $mpdf = new Mpdf();

    // 3) Escreve o CSS (uma única vez, como HEADER_CSS)
    $mpdf->WriteHTML($style);

    // 4) Cabeçalho fixo
    $header = ' 
        <h1>WL Maquetaria</h1><br/>
                  <table style="width: 100%;  border: 0px; vertical-align: top;">
                  <tr>
                      <td>Relatorio do sistema de corte</td>
                      <td style="text-align: right;">Per&iacute;odo: ' . date("d/m/Y", strtotime($dataInicial_str)) . ' a ' . date("d/m/Y", strtotime($dataFinal_str)) . '</td>
                  </tr>
                  <tr>
                      <td></td>
                      <td style="text-align: right;">Emiss&atilde;o: ' . date('d/m/Y H:i') . '</td>
                  </tr>
                  <tr>
                      <td><b>Empresa/Cliente:</b> ' . htmlspecialchars((string) $empresaNomeFiltro, ENT_QUOTES, 'UTF-8') . '</td>
                      <td style="text-align: right;"><b>Empreendimento:</b> ' . htmlspecialchars((string) $empreendimentoNomeFiltro, ENT_QUOTES, 'UTF-8') . '</td>
                  </tr>
                  </table>
                  <br/><br/>
                  ' . $indicadoresSectionHtml . '
                  <br/><br/>
                  ' . $materialSectionHtml . '
                  <br/><br/>
                  ' . $participandoSectionHtml . '
               <br/><br/>
                  ' . $desenhistasResumoSectionHtml . '
                <br/><br/>
                  ' . $cortadorResumoSectionHtml . '
                  ';
    $mpdf->WriteHTML($header);


  if($relatorio == "true"){
    // 6) Seção “Projetista”
    $mpdf->WriteHTML('<h2>Projetista</h2>');
    foreach ($desenhista as $user => $rows) {
        // cabeçalho de cada bloco
        $html  = '<h3>'.htmlspecialchars($user).'</h3>';
        $html .= '<table class="table tabela">
                    <tr style="background-color:white;">
                      <th> N&ordm;</th><th>Nome do arquivo</th><th>Tamanho</th><th>Empresa/Cliente</th>
                      <th>Empreendimento</th><th>Finalidade</th><th>Data de Envio</th><th>Status</th>
                    </tr>';
        $i = 0;
        $html1 = '';
        foreach ($rows as $item) {
            if (! $item['ok']) continue;
            $i++;
            $status = str_replace(['cortado_notfile','pendente'],['cortado','pendente'],$item['status']);
            $html1 .= '<tr style="background-color:white;">
                        <td>'.str_pad($i,2,'0',STR_PAD_LEFT).'</td>
                        <td>'.htmlspecialchars($item['nome_arquivo']).'</td>
                        <td>'.htmlspecialchars((string) ($item['tamanho_arquivo'] ?? '-')).'</td>
                        <td>'.htmlspecialchars($item['empresa']).'</td>
                        <td>'.htmlspecialchars($item['empreendimento']).'</td>
                        <td>'.htmlspecialchars($item['finalidade']).'</td>
                        <td>'.htmlspecialchars($item['data_add']).'</td>
                        <td>'.htmlspecialchars($status).'</td>
                      </tr>';
        }
        $html .= $html1.'</table><br/>';
        if($html1 != '')
        $mpdf->WriteHTML($html);
    }

    // 7) Seção “Cortador”
    $mpdf->WriteHTML('<h2>Cortador</h2>');
    foreach ($corte as $user => $rows) {
        $html  = '<h3>'.htmlspecialchars($user).'</h3>';
        $html .= '<table class="table tabela">
                    <tr style="background-color:white;">
                      <th> N&ordm;</th><th>Projetista</th><th>Nome do arquivo</th>
                      <th>Empresa/Cliente</th><th>Empreendimento</th><th>Finalidade</th>
                      <th>Data de Envio</th><th>Data de corte</th><th>Tempo de corte</th><th>Status</th>
                    </tr>';
        $i = 0;
        $html1 = '';
        foreach ($rows as $item) {
         
            if (! $item['ok']) continue;
            $i++;
            $status = str_replace(['cortado_notfile','pendente'],['pronto','pendente'],$item['status']);
            $html1 .= '<tr style="background-color:white;">
                        <td>'.str_pad($i,2,'0',STR_PAD_LEFT).'</td>
                        <td>'.htmlspecialchars($item['desenhista']).'</td>
                        <td>'.htmlspecialchars($item['nome_arquivo']).'</td>
                        <td>'.htmlspecialchars($item['empresa']).'</td>
                        <td>'.htmlspecialchars($item['empreendimento']).'</td>
                        <td>'.htmlspecialchars($item['finalidade']).'</td>
                        <td>'.htmlspecialchars($item['data_add']).'</td>
                        <td>'.htmlspecialchars($item['data_hora_corte']).'</td>
                        <td>'.htmlspecialchars($item['tempo_corte']).'</td>
                        <td>'.htmlspecialchars($status).'</td>
                      </tr>';
        }
        $html .= $html1.'</table><br/>';
        if($html1 != '')
        $mpdf->WriteHTML($html);
    }
  }
    // 8) Gera o PDF final e retorna
    $pdfContent = $mpdf->Output('', 'S');
    $pdfBase64 = base64_encode($pdfContent);

    $this->relatorioDebugSet('totais_saida', [
      'rows_query' => $totalRowsQuery,
      'total_publicacoes' => $totalPublicacoes,
      'total_finalizacoes' => $totalFinalizacoes,
      'total_desenhos_tr' => $total_desenhos_tr,
      'total_desenhos_apagados_tr' => $total_desenhos_apagados_tr,
      'total_corte_tr' => $total_corte_tr,
      'material_itens_dxf' => $materialItensDxf,
      'material_itens_com_area' => $materialItensComArea,
      'material_itens_sem_medicao' => $materialItensSemMedicao,
      'material_itens_arquivo_inexistente' => $materialItensArquivoInexistente,
      'material_area_base_m2' => round($materialAreaBaseM2, 6),
      'material_area_com_margem_m2' => round($materialAreaComMargemM2, 6),
      'material_area_chapa_mdf_m2' => round($this->obterAreaChapaMdfM2(), 6),
      'material_qtd_chapas_mdf_estimada' => ($materialAreaComMargemM2 > 0 && $this->obterAreaChapaMdfM2() > 0)
        ? round($materialAreaComMargemM2 / $this->obterAreaChapaMdfM2(), 2)
        : 0.0,
      'material_margem_percentual' => 10.0
    ]);
    $this->relatorioDebugSet('pdf', [
      'bytes' => strlen($pdfContent),
      'base64_length' => strlen($pdfBase64),
      'tipo' => 'com_dados'
    ]);

    return $this->respostaJsonRelatorio([
      'q'=>$corte,
        'ok'       => true,
        'pdf'      => $pdfBase64,
        'nome_pdf' => 'Relatorio_WL_'.
                       date("d_m_Y",strtotime($dataInicial_str)).
                       '_a_'.
                       date("d_m_Y",strtotime($dataFinal_str)).
                       '.pdf'
    ]);
}
  }


  private function relatorio_ind()
  {
    if (!$this->request->isAJAX()) {
      return;
    }

    session_start();

    $dataFinal_str = trim((string) service('request')->getPost('dataFinal'));
    $dataInicial_str = trim((string) service('request')->getPost('dataInicial'));
    $relatorio = service('request')->getPost('relatorio');
    $selectedValues = service('request')->getPost('selectedValues');
    $processo = trim((string) service('request')->getPost('processo'));
    $processoIdRequest = $this->normalizarFiltroId(service('request')->getPost('processoId'));
    $empresaId = $this->normalizarFiltroId(service('request')->getPost('empresaId'));
    $empreendimentoId = $this->normalizarFiltroId(service('request')->getPost('empreendimentoId'));
    $periodoAdicionado = filter_var(service('request')->getPost('periodoAdicionado'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    $periodoFinalizado = filter_var(service('request')->getPost('periodoFinalizado'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    $gerarSemDados = filter_var(service('request')->getPost('gerarSemDados'), FILTER_VALIDATE_BOOLEAN);
    $periodoAdicionado = $periodoAdicionado ?? true;
    $periodoFinalizado = $periodoFinalizado ?? false;
    if ($empresaId === null) {
      $empreendimentoId = null;
    }
    $tipoRelatorio = ($relatorio == "true") ? 'analitico' : 'sintetico';
    $msgSemDados = $this->mensagemSemDadosFiltros($empresaId, $empreendimentoId);
    $msg = array();

    $this->relatorioDebugReset('ind', [
      'inputs' => [
        'dataInicial' => $dataInicial_str,
        'dataFinal' => $dataFinal_str,
        'relatorio' => $relatorio,
        'tipoRelatorio' => $tipoRelatorio,
        'processo' => $processo,
        'processoIdRequest' => $processoIdRequest,
        'empresaId' => $empresaId,
        'empreendimentoId' => $empreendimentoId,
        'periodoAdicionado' => $periodoAdicionado,
        'periodoFinalizado' => $periodoFinalizado,
        'gerarSemDados' => $gerarSemDados
      ],
      'selectedValues' => $selectedValues
    ]);

    if ($processo === "") {
      $msg["Processo"] = "E preciso selecionar um processo.";
    }
    if ($dataFinal_str == "") {
      $msg["Data Final"] = "E preciso selecionar uma data final.";
    }
    if ($dataInicial_str == "") {
      $msg["Data Inicial"] = "E preciso selecionar uma data inicial.";
    }
    if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $dataFinal_str)) {
      $msg["Data Final"] = "E preciso selecionar uma data final valida.";
    }
    if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $dataInicial_str)) {
      $msg["Data Inicial"] = "E preciso selecionar uma data inicial valida.";
    }

    $dataFinal = strtotime($dataFinal_str . ' 23:59:59');
    $dataInicial = strtotime($dataInicial_str . ' 00:00:00');

    if (!($dataFinal >= $dataInicial)) {
      $msg["Data Inicial"] = "A data final nao pode ser anterior a data inicial.";
    }

    if (!$periodoAdicionado && !$periodoFinalizado) {
      $msg["Periodo"] = "Selecione pelo menos um tipo de periodo: Adicionado ou Finalizado.";
    }

    if ($msg != []) {
      return $this->respostaErroRelatorio($msg);
    }

    [$participantIds, $participantesResumo] = $this->extrairParticipantesSelecionados($selectedValues);
    $filtrarParticipantes = !empty($participantIds);

    $this->relatorioDebugSet('participantes', [
      'filtrar' => $filtrarParticipantes,
      'ids' => $participantIds,
      'qtd' => count($participantIds),
      'resumo_inicial' => $participantesResumo
    ]);

    $empresaNomeFiltro = 'Todas';
    $empreendimentoNomeFiltro = 'Todos';

    if ($empresaId !== null) {
      $empresaRow = (new \App\Models\Empresa())
        ->select('id, nome')
        ->where('id', $empresaId)
        ->first();

      if (!is_array($empresaRow) || !isset($empresaRow['id'])) {
        $msg['Empresa/Cliente'] = 'Empresa selecionada nao foi encontrada.';
      } else {
        $empresaNomeFiltro = $this->nomeLegivel((string) ($empresaRow['nome'] ?? ''));
      }
    }

    if ($empreendimentoId !== null) {
      $empreendimentoRow = (new \App\Models\Empreendimentos())
        ->select('id, nome, empresa_id')
        ->where('id', $empreendimentoId)
        ->first();

      if (!is_array($empreendimentoRow) || !isset($empreendimentoRow['id'])) {
        $msg['Empreendimento'] = 'Empreendimento selecionado nao foi encontrado.';
      } else {
        $empreendimentoNomeFiltro = $this->nomeLegivel((string) ($empreendimentoRow['nome'] ?? ''));

        if (
          $empresaId !== null &&
          isset($empreendimentoRow['empresa_id']) &&
          (int) $empreendimentoRow['empresa_id'] !== $empresaId
        ) {
          $msg['Empreendimento'] = 'Empreendimento selecionado nao pertence a empresa/cliente informado.';
        }
      }
    }

    if ($msg != []) {
      return $this->respostaErroRelatorio($msg);
    }

    $processoRow = $this->resolverProcesso($processo, $processoIdRequest);
    if (!is_array($processoRow) || !isset($processoRow['id'])) {
      return $this->respostaErroRelatorio([
        'Processo' => 'Processo informado nao foi encontrado.'
      ]);
    }
    $processoId = (int) $processoRow['id'];
    $processoNomeBanco = (string) ($processoRow['nome'] ?? $processo);

    $this->relatorioDebugSet('processo_resolvido', [
      'id' => $processoId,
      'nome_db' => $processoNomeBanco
    ]);
    $mostrarSecaoMaterialInd = $this->processoEhCorteLaser($processoId, $processoNomeBanco);
    $this->relatorioDebugSet('material_habilitado', $mostrarSecaoMaterialInd);

    $this->relatorioLog(
      'info',
      '[Relatorio] modo=ind tipo=' . $tipoRelatorio .
        ' processo_front="' . $processo . '"' .
        ' processo_db="' . $processoNomeBanco . '"' .
        ' processo_id=' . $processoId .
        ' periodo=' . $dataInicial_str . '->' . $dataFinal_str .
        ' periodo_tipo=' . ($periodoAdicionado ? 'adicionado' : '') . ($periodoAdicionado && $periodoFinalizado ? '+' : '') . ($periodoFinalizado ? 'finalizado' : '') .
        ' participantes=' . count($participantIds) .
        ' empresa_filtro=' . ($empresaId !== null ? (string) $empresaId : 'todos') .
        ' empreendimento_filtro=' . ($empreendimentoId !== null ? (string) $empreendimentoId : 'todos') .
        ' selectedValues=' . (json_encode($selectedValues, JSON_UNESCAPED_UNICODE) ?: 'null')
    );

    $db = \Config\Database::connect();
    $this->relatorioDebugSet('desenhos_has_ordem', $db->fieldExists('ordem', 'desenhos'));

    // IND: usa desenhos como fonte principal para nao depender de projeto finalizado.
    $builderProjetos = $db
      ->table('desenhos d')
      ->select([
        'd.id                    AS projeto_id',
        'd.nome                  AS projeto_descricao',
        'd.data_add              AS projeto_data_add',
        'd.usuario_id_desenhista AS projeto_usuario_id',
        'd.id                    AS desenho_id',
        'd.nome                  AS arquivo',
        'd.diretorio             AS arquivo_diretorio',
        'd.data_add              AS arquivo_data_add',
        'd.status                AS arquivo_status',
        'd.usuario_id_desenhista AS desenho_usuario_id',
        'u.nome                  AS desenhista_nome',
        'c.usuario_id_fim        AS finalizador_id',
        'c.data_end              AS finalizacao_data',
        'uf.nome                 AS finalizador_nome',
        'e.nome                  AS empresa_nome',
        'emp.nome                AS empreendimento_nome',
        'f.nome                  AS finalidade_nome'
      ])
      ->join('usuarios u', 'u.id = d.usuario_id_desenhista', 'left')
      ->join('corte c', 'c.id = d.corte_id', 'left')
      ->join('usuarios uf', 'uf.id = c.usuario_id_fim', 'left')
      ->join('empresa e', 'e.id = d.empresa_id', 'left')
      ->join('empreendimentos emp', 'emp.id = d.empreendimentos_id', 'left')
      ->join('finalidade f', 'f.id = d.finalidade_id', 'left')
      ->where('d.processos_id', $processoId)
      ->orderBy('d.data_add', 'ASC')
      ->orderBy('d.id', 'ASC');

    if ($periodoAdicionado && $periodoFinalizado) {
      $builderProjetos
        ->groupStart()
          ->groupStart()
            ->where('d.data_add >=', $dataInicial_str . ' 00:00:00')
            ->where('d.data_add <=', $dataFinal_str . ' 23:59:59')
          ->groupEnd()
          ->orGroupStart()
            ->where('c.data_end >=', $dataInicial_str . ' 00:00:00')
            ->where('c.data_end <=', $dataFinal_str . ' 23:59:59')
          ->groupEnd()
        ->groupEnd();
    } elseif ($periodoAdicionado) {
      $builderProjetos
        ->where('d.data_add >=', $dataInicial_str . ' 00:00:00')
        ->where('d.data_add <=', $dataFinal_str . ' 23:59:59');
    } else {
      $builderProjetos
        ->where('c.data_end >=', $dataInicial_str . ' 00:00:00')
        ->where('c.data_end <=', $dataFinal_str . ' 23:59:59');
    }

    if ($filtrarParticipantes) {
      $builderProjetos
        ->groupStart()
        ->whereIn('d.usuario_id_desenhista', $participantIds)
        ->orWhereIn('c.usuario_id_fim', $participantIds)
        ->groupEnd();
    }

    $diagIndA = clone $builderProjetos;
    $diagIndASql = preg_replace('/\s+/', ' ', trim($diagIndA->getCompiledSelect(false)));
    $diagIndACount = (int) $diagIndA->countAllResults(false);

    $diagIndB = clone $builderProjetos;
    if ($empresaId !== null) {
      $diagIndB->where('d.empresa_id', $empresaId);
    }
    $diagIndBSql = preg_replace('/\s+/', ' ', trim($diagIndB->getCompiledSelect(false)));
    $diagIndBCount = (int) $diagIndB->countAllResults(false);

    $diagIndC = clone $diagIndB;
    if ($empreendimentoId !== null) {
      $diagIndC->where('d.empreendimentos_id', $empreendimentoId);
    }
    $diagIndCSql = preg_replace('/\s+/', ' ', trim($diagIndC->getCompiledSelect(false)));
    $diagIndCCount = (int) $diagIndC->countAllResults(false);

    $this->relatorioLog(
      'info',
      '[RelatorioDiag] modo=ind etapaA=' . $diagIndACount .
        ' etapaB=' . $diagIndBCount .
        ' etapaC=' . $diagIndCCount
    );
    $this->relatorioLog('debug', '[RelatorioDiag][ind][SQL A] ' . $diagIndASql);
    $this->relatorioLog('debug', '[RelatorioDiag][ind][SQL B] ' . $diagIndBSql);
    $this->relatorioLog('debug', '[RelatorioDiag][ind][SQL C] ' . $diagIndCSql);

    $this->relatorioDebugSet('diagnostico_sql', [
      'A' => ['count' => $diagIndACount, 'sql' => $diagIndASql],
      'B' => ['count' => $diagIndBCount, 'sql' => $diagIndBSql],
      'C' => ['count' => $diagIndCCount, 'sql' => $diagIndCSql]
    ]);

    $builderProjetosFinal = clone $builderProjetos;
    if ($empresaId !== null) {
      $builderProjetosFinal->where('d.empresa_id', $empresaId);
    }
    if ($empreendimentoId !== null) {
      $builderProjetosFinal->where('d.empreendimentos_id', $empreendimentoId);
    }

    $builderIndFinalSql = preg_replace('/\s+/', ' ', trim($builderProjetosFinal->getCompiledSelect(false)));
    $this->relatorioDebugSet('sql_final', $builderIndFinalSql);

    $desenhosRows = $builderProjetosFinal
      ->get()
      ->getResultArray();

    $this->relatorioDebugSet('query_resultado', [
      'rows' => count($desenhosRows),
      'sample' => array_slice($desenhosRows, 0, 10)
    ]);

    // IND: usa somente "desenhos" como base para o resumo e para os detalhes do periodo.
    $projetosRows = $desenhosRows;
    $desenhosPeriodoRows = $desenhosRows;

    $totalRowsQuery = count($desenhosRows);
    $totalDesenhosPeriodoQuery = $totalRowsQuery;
    if ($totalRowsQuery === 0 && $totalDesenhosPeriodoQuery === 0) {
    log_message(
      'info',
      '[Relatorio] modo=ind tipo=' . $tipoRelatorio .
        ' processo="' . $processo . '"' .
        ' periodo=' . $dataInicial_str . '->' . $dataFinal_str .
          ' participantes=' . count($participantIds) .
          ' empresa_filtro=' . ($empresaId !== null ? (string) $empresaId : 'todos') .
          ' empreendimento_filtro=' . ($empreendimentoId !== null ? (string) $empreendimentoId : 'todos') .
          ' rows=0 publicacoes=0 finalizacoes=0'
      );
      $this->relatorioDebugSet('sem_dados', [
        'motivo' => $msgSemDados,
        'gerarSemDados' => $gerarSemDados
      ]);

      if (!$gerarSemDados) {
        return $this->respostaErroRelatorio([
          'Relatorio' => $msgSemDados
        ]);
      }

      return $this->respostaPdfSemDados(
        $processo,
        $dataInicial_str,
        $dataFinal_str,
        $empresaNomeFiltro,
        $empreendimentoNomeFiltro,
        $msgSemDados
      );
    }

    $projetos = [];
    $totalPublicacoes = 0;
    $totalFinalizacoes = 0;
    foreach ($projetosRows as $row) {
      $desenhistaIdAtual = (int) ($row['desenho_usuario_id'] ?? 0);
      if (isset($participantesResumo[$desenhistaIdAtual])) {
        $participantesResumo[$desenhistaIdAtual]['publicacoes']++;
        $totalPublicacoes++;
      }

      $finalizadorIdAtual = (int) ($row['finalizador_id'] ?? 0);
      if ($finalizadorIdAtual > 0 && isset($participantesResumo[$finalizadorIdAtual])) {
        $participantesResumo[$finalizadorIdAtual]['finalizacoes']++;
        $totalFinalizacoes++;
      }

      $projetoId = (int) ($row['projeto_id'] ?? 0);
      if ($projetoId <= 0) {
        continue;
      }

      if (!isset($projetos[$projetoId])) {
        $descricao = trim((string) ($row['projeto_descricao'] ?? ''));
        if ($descricao === '') {
          $descricao = 'Sem descricao';
        }

        $projetos[$projetoId] = [
          'id' => $projetoId,
          'descricao' => $descricao,
          'data_add' => (string) ($row['projeto_data_add'] ?? ''),
          'desenhistas' => [],
          'finalizadores' => [],
          'finalizacao_data' => '',
          'arquivos' => []
        ];
      }

      $desenhistaNomeProjeto = trim((string) ($row['desenhista_nome'] ?? ''));
      if ($desenhistaNomeProjeto !== '') {
        $projetos[$projetoId]['desenhistas'][$desenhistaNomeProjeto] = true;
      }

      $finalizadorNome = trim((string) ($row['finalizador_nome'] ?? ''));
      if ($finalizadorNome !== '') {
        $projetos[$projetoId]['finalizadores'][$finalizadorNome] = true;
      }

      $dataFinalizacao = trim((string) ($row['finalizacao_data'] ?? ''));
      if ($dataFinalizacao !== '') {
        if (
          $projetos[$projetoId]['finalizacao_data'] === '' ||
          strtotime($dataFinalizacao) > strtotime($projetos[$projetoId]['finalizacao_data'])
        ) {
          $projetos[$projetoId]['finalizacao_data'] = $dataFinalizacao;
        }
      }

      $statusArquivo = trim((string) ($row['arquivo_status'] ?? ''));
      if ($statusArquivo === '') {
        $statusArquivo = 'pendente';
      }

      $projetos[$projetoId]['arquivos'][] = [
        'nome_arquivo' => Ferramentas::remove_id_file((string) ($row['arquivo'] ?? '')),
        'desenhista' => (string) ($row['desenhista_nome'] ?? ''),
        'empresa' => (string) ($row['empresa_nome'] ?? ''),
        'empreendimento' => (string) ($row['empreendimento_nome'] ?? ''),
        'finalidade' => (string) ($row['finalidade_nome'] ?? ''),
        'data_add' => (string) ($row['arquivo_data_add'] ?? ''),
        'status' => $statusArquivo
      ];
    }

    $style = '
    <style>
      body { font-family: Verdana, Geneva, Tahoma, sans-serif; font-size: 12px; }
      h1 { font-size: 22px; margin: 0; }
      h2 { font-size: 16px; margin: 20px 0 10px 0; page-break-after: avoid; page-break-inside: avoid; }
      h3 { font-size: 13px; margin: 12px 0; color: #444; page-break-after: avoid; page-break-inside: avoid; }
      table { border-collapse: collapse; width: 100%; }
      th, td { border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 5px; text-align: left; }
      .section-keep-min { page-break-inside: avoid; }
      .muted { color: #666; }
    </style>';

    $participantesTabelaTr = '';
    $participantesCount = count($participantesResumo);
    $participantesTotalPublicacoes = 0;
    $participantesTotalFinalizacoes = 0;
    $participanteIdx = 0;
    foreach ($participantesResumo as $participanteResumo) {
      $participanteIdx++;
      $numeroParticipante = str_pad((string) $participanteIdx, max(2, strlen((string) max(1, $participantesCount))), '0', STR_PAD_LEFT);
      $nomeParticipante = trim((string) ($participanteResumo['nome'] ?? ''));
      if ($nomeParticipante === '') {
        $nomeParticipante = 'Nao identificado';
      }
      $funcaoParticipante = trim((string) ($participanteResumo['funcao'] ?? ''));
      if ($funcaoParticipante === '') {
        $funcaoParticipante = 'Participante';
      }
      $publicacoesParticipante = (int) ($participanteResumo['publicacoes'] ?? 0);
      $finalizacoesParticipante = (int) ($participanteResumo['finalizacoes'] ?? 0);
      $participantesTotalPublicacoes += $publicacoesParticipante;
      $participantesTotalFinalizacoes += $finalizacoesParticipante;

      $participantesTabelaTr .= '<tr>
        <td>' . $numeroParticipante . '</td>
        <td>' . htmlspecialchars($nomeParticipante, ENT_QUOTES, 'UTF-8') . '</td>
        <td>' . htmlspecialchars($funcaoParticipante, ENT_QUOTES, 'UTF-8') . '</td>
        <td>' . $publicacoesParticipante . '</td>
        <td>' . $finalizacoesParticipante . '</td>
      </tr>';
    }
    if ($participantesTabelaTr === '') {
      $participantesTabelaTr = '<tr>
        <td>01</td>
        <td>Todos os participantes</td>
        <td>Sem filtro</td>
        <td>0</td>
        <td>0</td>
      </tr>';
    }

    $resumoProjetosTr = '';
    $detalhesProjetosHtml = '';
    $totalProjetos = count($projetos);
    $totalArquivos = 0;
    $idxProjeto = 0;

    foreach ($projetos as $projeto) {
      $idxProjeto++;
      $qtdArquivos = count($projeto['arquivos']);
      $totalArquivos += $qtdArquivos;
      $desenhistasProjeto = empty($projeto['desenhistas']) ? 'Nao identificado' : implode(', ', array_keys($projeto['desenhistas']));
      $finalizadoresProjeto = empty($projeto['finalizadores']) ? 'Nao identificado' : implode(', ', array_keys($projeto['finalizadores']));

      $resumoProjetosTr .= '<tr>
        <td>' . str_pad((string) $idxProjeto, max(2, strlen((string) max(1, $totalProjetos))), '0', STR_PAD_LEFT) . '</td>
        <td>#' . (int) $projeto['id'] . ' - ' . htmlspecialchars((string) $projeto['descricao'], ENT_QUOTES, 'UTF-8') . '</td>
        <td>' . htmlspecialchars((string) $projeto['data_add'], ENT_QUOTES, 'UTF-8') . '</td>
        <td>' . htmlspecialchars((string) $desenhistasProjeto, ENT_QUOTES, 'UTF-8') . '</td>
        <td>' . htmlspecialchars((string) $finalizadoresProjeto, ENT_QUOTES, 'UTF-8') . '</td>
        <td>' . $qtdArquivos . '</td>
      </tr>';

      $detalhesProjetosHtml .= '<h3>Projeto #' . (int) $projeto['id'] . ' - ' . htmlspecialchars((string) $projeto['descricao'], ENT_QUOTES, 'UTF-8') . '</h3>
      <table>
        <tr>
          <th>Data do projeto</th>
          <th>Projetista</th>
          <th>Finalizado por</th>
          <th>Data de finalizacao</th>
          <th>Total de arquivos</th>
        </tr>
        <tr>
          <td>' . htmlspecialchars((string) $projeto['data_add'], ENT_QUOTES, 'UTF-8') . '</td>
          <td>' . htmlspecialchars((string) $desenhistasProjeto, ENT_QUOTES, 'UTF-8') . '</td>
          <td>' . htmlspecialchars((string) $finalizadoresProjeto, ENT_QUOTES, 'UTF-8') . '</td>
          <td>' . htmlspecialchars((string) ($projeto['finalizacao_data'] !== '' ? $projeto['finalizacao_data'] : 'Nao identificada'), ENT_QUOTES, 'UTF-8') . '</td>
          <td>' . $qtdArquivos . '</td>
        </tr>
      </table>
      <br/>
      <table>
        <tr>
          <th>N&ordm;</th>
          <th>Arquivo</th>
          <th>Projetista</th>
          <th>Empresa/Cliente</th>
          <th>Empreendimento</th>
          <th>Finalidade</th>
          <th>Data de envio</th>
          <th>Status</th>
        </tr>';

      $idxArquivo = 0;
      foreach ($projeto['arquivos'] as $arquivo) {
        $idxArquivo++;
        $detalhesProjetosHtml .= '<tr>
          <td>' . str_pad((string) $idxArquivo, max(2, strlen((string) max(1, $qtdArquivos))), '0', STR_PAD_LEFT) . '</td>
          <td>' . htmlspecialchars((string) $arquivo['nome_arquivo'], ENT_QUOTES, 'UTF-8') . '</td>
          <td>' . htmlspecialchars((string) $arquivo['desenhista'], ENT_QUOTES, 'UTF-8') . '</td>
          <td>' . htmlspecialchars((string) $arquivo['empresa'], ENT_QUOTES, 'UTF-8') . '</td>
          <td>' . htmlspecialchars((string) $arquivo['empreendimento'], ENT_QUOTES, 'UTF-8') . '</td>
          <td>' . htmlspecialchars((string) $arquivo['finalidade'], ENT_QUOTES, 'UTF-8') . '</td>
          <td>' . htmlspecialchars((string) $arquivo['data_add'], ENT_QUOTES, 'UTF-8') . '</td>
          <td>' . htmlspecialchars((string) $arquivo['status'], ENT_QUOTES, 'UTF-8') . '</td>
        </tr>';
      }

      $detalhesProjetosHtml .= '</table><br/>';
    }

    $participantesIndHeaderTr = '<tr>
          <th style="width: 7px;"> N&ordm;</th>
          <th>Nome</th>
          <th>Fun&ccedil;&atilde;o</th>
          <th>Publica&ccedil;&otilde;es</th>
          <th>Finaliza&ccedil;&otilde;es</th>
        </tr>';
    $participantesIndFooterTr = '<tr>
          <th colspan="3">Total de participantes: ' . $participantesCount . '</th>
          <th>' . $participantesTotalPublicacoes . '</th>
          <th>' . $participantesTotalFinalizacoes . '</th>
        </tr>';
    $participantesIndSectionHtml = $this->buildTitleTableSectionWithMinRows(
      'h3',
      'Participantes selecionados',
      '',
      $participantesIndHeaderTr,
      $participantesTabelaTr,
      $participantesIndFooterTr,
      3
    );

    $projetosHeaderTr = '<tr>
          <th style="width: 7px;"> N&ordm;</th>
          <th>Projeto</th>
          <th>Data do projeto</th>
          <th>Projetista</th>
          <th>Finalizado por</th>
          <th>Qtd. arquivos</th>
        </tr>';
    $projetosFooterTr = '<tr>
          <th colspan="4">Total de projetos finalizados: ' . $totalProjetos . '</th>
          <th colspan="2"><b>Total de arquivos nos projetos: ' . $totalArquivos . '</b></th>
        </tr>';
    $projetosSectionHtml = $this->buildTitleTableSectionWithMinRows(
      'h2',
      'Projetos finalizados',
      '',
      $projetosHeaderTr,
      $resumoProjetosTr,
      $projetosFooterTr,
      3
    );

    $desenhosPeriodoSectionHtml = '';
    $totalDesenhosAdicionadosPeriodo = 0;
    $totalDesenhosCortadosPeriodo = 0;
    $totalDesenhosPeriodoExibidos = 0;
    $desenhosPeriodoTr = '';
    $materialCacheInd = $mostrarSecaoMaterialInd
      ? $this->garantirAreasMaterialPorDesenho($desenhosPeriodoRows, $processoId)
      : [];
    $this->relatorioDebugSet('material_cache_ind', $materialCacheInd);
    $areasMaterialPorDesenhoInd = $mostrarSecaoMaterialInd
      ? $this->carregarAreasMaterialPorDesenho(array_column($desenhosPeriodoRows, 'desenho_id'), $processoId)
      : [];
    $materialItensDxfInd = 0;
    $materialItensComAreaInd = 0;
    $materialItensSemMedicaoInd = 0;
    $materialItensArquivoInexistenteInd = 0;
    $materialAreaBaseM2Ind = 0.0;
    $materialAreaComMargemM2Ind = 0.0;

    foreach ($desenhosPeriodoRows as $desenhoPeriodo) {
      $statusRaw = trim((string) ($desenhoPeriodo['arquivo_status'] ?? ''));
      if ($statusRaw === 'apagado') {
        continue;
      }

      $totalDesenhosPeriodoExibidos++;
      $arquivoNome = Ferramentas::remove_id_file((string) ($desenhoPeriodo['arquivo'] ?? ''));
      $finalizacaoData = trim((string) ($desenhoPeriodo['finalizacao_data'] ?? ''));
      $isCortado = $finalizacaoData !== '' || in_array($statusRaw, ['pronto', 'cortado_notfile', 'cortado'], true);
      $tipoRegistro = $isCortado ? 'Cortado' : 'Adicionado';

      if ($isCortado) {
        $totalDesenhosCortadosPeriodo++;
      } else {
        $totalDesenhosAdicionadosPeriodo++;
      }

      $statusLabel = $statusRaw === '' ? 'pendente' : str_replace('cortado_notfile', 'cortado', $statusRaw);
      $desenhistaNome = trim((string) ($desenhoPeriodo['desenhista_nome'] ?? ''));
      $finalizadorNome = trim((string) ($desenhoPeriodo['finalizador_nome'] ?? ''));

      if ($desenhistaNome === '') {
        $desenhistaNome = 'Nao identificado';
      }
      if ($finalizadorNome === '') {
        $finalizadorNome = 'Nao identificado';
      }

      $desenhoIdPeriodo = (int) ($desenhoPeriodo['desenho_id'] ?? 0);
      $extensaoPeriodo = strtolower((string) pathinfo((string) ($desenhoPeriodo['arquivo_diretorio'] ?? ''), PATHINFO_EXTENSION));
      if ($extensaoPeriodo === '' && preg_match('/_([a-z0-9]{2,5})$/i', (string) ($desenhoPeriodo['arquivo'] ?? ''), $matchExtPeriodo)) {
        $extensaoPeriodo = strtolower((string) ($matchExtPeriodo[1] ?? ''));
      }
      if ($mostrarSecaoMaterialInd && $extensaoPeriodo === 'dxf') {
        $materialItensDxfInd++;
        if ($desenhoIdPeriodo > 0 && isset($areasMaterialPorDesenhoInd[$desenhoIdPeriodo])) {
          $areaInfo = $areasMaterialPorDesenhoInd[$desenhoIdPeriodo];
          $statusArea = (string) ($areaInfo['status'] ?? 'ok');
          if ($statusArea === 'ok') {
            $materialItensComAreaInd++;
            $materialAreaBaseM2Ind += (float) ($areaInfo['area_m2'] ?? 0);
            $materialAreaComMargemM2Ind += (float) ($areaInfo['area_m2_com_margem'] ?? 0);
          } else {
            $materialItensSemMedicaoInd++;
            if ($statusArea === 'arquivo_nao_encontrado') {
              $materialItensArquivoInexistenteInd++;
            }
          }
        } else {
          $materialItensSemMedicaoInd++;
        }
      }

      $desenhosPeriodoTr .= '<tr>
        <td>' . str_pad((string) $totalDesenhosPeriodoExibidos, max(2, strlen((string) max(1, $totalDesenhosPeriodoExibidos))), '0', STR_PAD_LEFT) . '</td>
        <td>' . htmlspecialchars((string) $arquivoNome, ENT_QUOTES, 'UTF-8') . '</td>
        <td>' . htmlspecialchars((string) $desenhistaNome, ENT_QUOTES, 'UTF-8') . '</td>
        <td>' . htmlspecialchars((string) $finalizadorNome, ENT_QUOTES, 'UTF-8') . '</td>
        <td>' . htmlspecialchars((string) ($desenhoPeriodo['empresa_nome'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td>
        <td>' . htmlspecialchars((string) ($desenhoPeriodo['empreendimento_nome'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td>
        <td>' . htmlspecialchars((string) ($desenhoPeriodo['arquivo_data_add'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td>
        <td>' . htmlspecialchars((string) ($finalizacaoData !== '' ? $finalizacaoData : '-'), ENT_QUOTES, 'UTF-8') . '</td>
        <td>' . htmlspecialchars($tipoRegistro, ENT_QUOTES, 'UTF-8') . '</td>
        <td>' . htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') . '</td>
      </tr>';
    }

    if ($totalDesenhosPeriodoExibidos > 0) {
      $desenhosPeriodoHeaderTr = '<tr>
            <th style="width: 7px;">N&ordm;</th>
            <th>Arquivo</th>
            <th>Projetista</th>
            <th>Finalizado por</th>
            <th>Empresa/Cliente</th>
            <th>Empreendimento</th>
            <th>Data de envio</th>
            <th>Data de finaliza&ccedil;&atilde;o</th>
            <th>Tipo</th>
            <th>Status</th>
          </tr>';
      $desenhosPeriodoFooterTr = '<tr>
            <th colspan="6">Total de desenhos no per&iacute;odo: ' . $totalDesenhosPeriodoExibidos . '</th>
            <th colspan="2">Adicionados: ' . $totalDesenhosAdicionadosPeriodo . '</th>
            <th colspan="2">Cortados: ' . $totalDesenhosCortadosPeriodo . '</th>
          </tr>';

      $desenhosPeriodoSectionHtml = '<br/>' . $this->buildTitleTableSectionWithMinRows(
        'h2',
        'Desenhos no per&iacute;odo',
        '',
        $desenhosPeriodoHeaderTr,
        $desenhosPeriodoTr,
        $desenhosPeriodoFooterTr,
        3
      );
    }

    $materialSectionHtmlInd = '';
    if ($mostrarSecaoMaterialInd) {
      $materialSectionHtmlInd = $this->montarSecaoGastoMaterialHtml(
        $materialItensDxfInd,
        $materialItensComAreaInd,
        $materialItensSemMedicaoInd,
        $materialItensArquivoInexistenteInd,
        $materialAreaBaseM2Ind,
        $materialAreaComMargemM2Ind,
        10.0
      );
    }

    // Mantemos "sem dados" apenas quando a query principal retorna 0 linhas.

log_message(
      'info',
      '[Relatorio] modo=ind tipo=' . $tipoRelatorio .
        ' processo="' . $processo . '"' .
        ' periodo=' . $dataInicial_str . '->' . $dataFinal_str .
        ' participantes=' . count($participantIds) .
        ' empresa_filtro=' . ($empresaId !== null ? (string) $empresaId : 'todos') .
        ' empreendimento_filtro=' . ($empreendimentoId !== null ? (string) $empreendimentoId : 'todos') .
        ' rows=' . $totalRowsQuery .
        ' projetos=' . $totalProjetos .
        ' arquivos=' . $totalArquivos .
        ' desenhos_periodo=' . $totalDesenhosPeriodoExibidos .
        ' publicacoes=' . $totalPublicacoes .
        ' finalizacoes=' . $totalFinalizacoes
    );

    $mpdf = new Mpdf();
    $mpdf->WriteHTML($style);

    $header = '
      <h1>WL Maquetaria</h1><br/>
      <table style="border:0;">
        <tr>
          <td><b>Relatorio do processo: ' . htmlspecialchars((string) $processo, ENT_QUOTES, 'UTF-8') . '</b></td>
          <td style="text-align:right;">Per&iacute;odo: ' . date("d/m/Y", strtotime($dataInicial_str)) . ' a ' . date("d/m/Y", strtotime($dataFinal_str)) . '</td>
        </tr>
        <tr>
          <td class="muted"></td>
          <td style="text-align:right;">Emiss&atilde;o: ' . date('d/m/Y H:i') . '</td>
        </tr>
        <tr>
          <td><b>Empresa/Cliente:</b> ' . htmlspecialchars((string) $empresaNomeFiltro, ENT_QUOTES, 'UTF-8') . '</td>
          <td style="text-align:right;"><b>Empreendimento:</b> ' . htmlspecialchars((string) $empreendimentoNomeFiltro, ENT_QUOTES, 'UTF-8') . '</td>
        </tr>
      </table>
      <br/>
      ' . $participantesIndSectionHtml . '
      <br/>
      ' . $materialSectionHtmlInd . '
      <br/>
      ' . $projetosSectionHtml . '';
    $mpdf->WriteHTML($header);
    if ($desenhosPeriodoSectionHtml !== '') {
      $mpdf->WriteHTML($desenhosPeriodoSectionHtml);
    }

    if ($totalProjetos === 0) {
      if ($desenhosPeriodoSectionHtml === '') {
        $mpdf->WriteHTML('<br/><p><b>Nenhum projeto finalizado encontrado para os filtros informados.</b></p>');
      } else {
        $mpdf->WriteHTML('<br/><p><b>Nao ha projetos finalizados no periodo, mas os desenhos do periodo foram listados acima.</b></p>');
      }
    } else if ($relatorio == "true") {
      $mpdf->WriteHTML('<h2>Detalhamento dos projetos e arquivos</h2>');
      $mpdf->WriteHTML($detalhesProjetosHtml);
    }

    $pdfContent = $mpdf->Output('', 'S');
    $pdfBase64 = base64_encode($pdfContent);

    $this->relatorioDebugSet('totais_saida', [
      'rows_query' => $totalRowsQuery,
      'total_projetos' => $totalProjetos,
      'total_arquivos' => $totalArquivos,
      'desenhos_periodo' => $totalDesenhosPeriodoExibidos,
      'adicionados_periodo' => $totalDesenhosAdicionadosPeriodo,
      'cortados_periodo' => $totalDesenhosCortadosPeriodo,
      'total_publicacoes' => $totalPublicacoes,
      'total_finalizacoes' => $totalFinalizacoes,
      'material_itens_dxf' => $materialItensDxfInd,
      'material_itens_com_area' => $materialItensComAreaInd,
      'material_itens_sem_medicao' => $materialItensSemMedicaoInd,
      'material_itens_arquivo_inexistente' => $materialItensArquivoInexistenteInd,
      'material_area_base_m2' => round($materialAreaBaseM2Ind, 6),
      'material_area_com_margem_m2' => round($materialAreaComMargemM2Ind, 6),
      'material_area_chapa_mdf_m2' => round($this->obterAreaChapaMdfM2(), 6),
      'material_qtd_chapas_mdf_estimada' => ($materialAreaComMargemM2Ind > 0 && $this->obterAreaChapaMdfM2() > 0)
        ? round($materialAreaComMargemM2Ind / $this->obterAreaChapaMdfM2(), 2)
        : 0.0,
      'material_margem_percentual' => 10.0
    ]);
    $this->relatorioDebugSet('pdf', [
      'bytes' => strlen($pdfContent),
      'base64_length' => strlen($pdfBase64),
      'tipo' => 'com_dados'
    ]);

    return $this->respostaJsonRelatorio([
      'q' => [],
      'ok' => true,
      'pdf' => $pdfBase64,
      'nome_pdf' => 'Relatorio_WL_' .
        date("d_m_Y", strtotime($dataInicial_str)) .
        '_a_' .
        date("d_m_Y", strtotime($dataFinal_str)) .
        '.pdf'
    ]);
  }


  /**
   * Gera uma lista de usuários com seus respectivos níveis de acesso e status, e retorna os dados via AJAX.
   * 
   * Esta função é chamada via uma requisição AJAX e gera uma lista de usuários baseada em seus níveis de acesso 
   * e status. Somente usuários que possuem acesso ao processo 'relatorio' são incluídos na lista. 
   * Os dados gerados são armazenados em uma sessão e enviados como resposta JSON.
   *
   * @return \CodeIgniter\HTTP\Response O objeto de resposta contendo a lista de usuários em formato JSON.
   */
  function lista_usuarios_niveis()
  {
    if ($this->request->isAJAX()) {
      // Inicializa a sessão para acessar os dados da lista armazenados nela
      session_start();

      $usuarios = new \App\Models\Usuarios(); // Obtém a tabela de usuários do banco
      $nivel = new \App\Models\Nivel();
      $usuarios_data = $usuarios->find();
      $nivel_data = $nivel->find();

      $lista = array();
      $id_temp = 0;

      foreach ($usuarios_data as $key => $value) {
        // Cria a lista com base nos usuários ativos ou desativados, dependendo da solicitação
        $nivel_user = Ferramentas::array_pesquisa($nivel_data, "id", $value['nivel_id']);

        // Mantém a regra de permissão, mas garante que o usuário 1 apareça na lista.
        if ($nivel_user['relatorio'] != '1') {
          continue;
        }

        $statusUsuario = Ferramentas::decodificador($value['status']);
        $lista['nome'][Ferramentas::array_index($nivel_user, ['nome'])][$statusUsuario][$id_temp] = Ferramentas::decodificador(Ferramentas::array_index($value, ['nome']));
        $lista[strval($id_temp)] = $value;

        $id_temp++;
      }
      $_SESSION["lista_usuarios"] = $lista;

      //retorna a lista para o ajax
      $data = [
        "lista" => $lista['nome']
      ];

      return $this->response->setJSON($data);
    }
  }
}
