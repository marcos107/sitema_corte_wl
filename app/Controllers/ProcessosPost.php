<?php

namespace App\Controllers;


use App\Controllers\Ferramentas;


class ProcessosPost extends Ferramentas
{
  private function iniciarSessaoSeNecessario(): void
  {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }
  }

  private function decodificarValor($valor): string
  {
    $valor = (string) ($valor ?? '');
    if ($valor === '') {
      return '';
    }

    $decodificado = Ferramentas::decodificador($valor);
    return $decodificado !== '' ? $decodificado : $valor;
  }

  private function contextosTelaProcessos(): array
  {
    return [
      'desenho_adicionar' => ['desenho_adicionar'],
      'desenho_meus' => ['desenho_meus'],
      'lista_corte' => ['lista_corte'],
      'lista_tarefas' => ['lista_tarefas'],
      'lista_corte_adm' => ['lista_corte_adm', 'lista_tarefas_adm'],
      'desenhos_cortados' => ['desenhos_cortados'],
      'relatorios' => ['relatorios'],
      'subpasta' => ['subpasta'],
    ];
  }

  private function normalizarContextoTela($contexto): string
  {
    $contexto = strtolower(trim((string) ($contexto ?? '')));
    if ($contexto === '') {
      return '';
    }

    $contexto = preg_replace('/[^a-z0-9]+/', '_', $contexto) ?? $contexto;
    return trim($contexto, '_');
  }

  private function canonicalizarContextoTela(?string $contexto): string
  {
    $contextoNormalizado = $this->normalizarContextoTela($contexto);
    if ($contextoNormalizado === '') {
      return '';
    }

    foreach ($this->contextosTelaProcessos() as $canonical => $aliases) {
      $normalizados = array_map([$this, 'normalizarContextoTela'], array_merge([$canonical], $aliases));
      if (in_array($contextoNormalizado, $normalizados, true)) {
        return $canonical;
      }
    }

    return $contextoNormalizado;
  }

  private function resolverContextoTelaProcessosLista(): string
  {
    $contextoPost = service('request')->getPost('contexto_tela');
    $contexto = $this->canonicalizarContextoTela(is_scalar($contextoPost) ? (string) $contextoPost : '');
    if ($contexto !== '') {
      return $contexto;
    }

    $referer = trim((string) ($_SERVER['HTTP_REFERER'] ?? ''));
    if ($referer === '') {
      return '';
    }

    $path = parse_url($referer, PHP_URL_PATH);
    if (!is_string($path) || $path === '') {
      return '';
    }

    $pathNormalizado = $this->normalizarContextoTela($path);
    foreach ($this->contextosTelaProcessos() as $canonical => $aliases) {
      foreach (array_merge([$canonical], $aliases) as $alias) {
        $aliasNormalizado = $this->normalizarContextoTela($alias);
        if ($aliasNormalizado !== '' && str_contains($pathNormalizado, $aliasNormalizado)) {
          return $canonical;
        }
      }
    }

    return '';
  }

  private function sincronizarContextoNivelSessao(int $nivelId): void
  {
    if ($nivelId <= 0) {
      return;
    }

    $contextoAcesso = (new \App\Models\Nivel())->montarContextoAcesso($nivelId);
    if (!is_array($contextoAcesso) || empty($contextoAcesso)) {
      return;
    }

    $_SESSION['processos'] = $contextoAcesso['processos_nomes'] ?? [];
    $_SESSION['processos_por_contexto'] = $contextoAcesso['processos_por_contexto'] ?? [];
    $_SESSION['processos_ids_por_contexto'] = $contextoAcesso['processos_ids_por_contexto'] ?? [];
    $_SESSION['origem_processos_contexto'] = $contextoAcesso['origem_contexto'] ?? [];
    $_SESSION['nivel_ids_contexto'] = $contextoAcesso['nivel_ids'] ?? [];
  }

  private function obterProcessosPermitidosSessao(?string $contextoTela = null): array
  {
    $permissoesUsuario = is_array($_SESSION['permissao'] ?? null) ? $_SESSION['permissao'] : [];
    $contextoTela = $this->canonicalizarContextoTela($contextoTela);
    $contextoDefinido = false;

    if ($contextoTela !== '') {
      $processosPorContexto = is_array($_SESSION['processos_por_contexto'] ?? null) ? $_SESSION['processos_por_contexto'] : [];
      $contextoDefinido = array_key_exists($contextoTela, $processosPorContexto);
      $processosContexto = $contextoDefinido ? $processosPorContexto[$contextoTela] : null;
      if (is_array($processosContexto)) {
        $processosContexto = array_values(array_unique(array_filter(array_map('strval', $processosContexto), static function ($valor) {
          return trim((string) $valor) !== '';
        })));

        if ($processosContexto !== []) {
          return $processosContexto;
        }
      }
    }

    $processosUsuario = is_array($_SESSION['processos'] ?? null) ? $_SESSION['processos'] : [];
    $processosUsuario = array_values(array_unique(array_filter(array_map('strval', $processosUsuario), static function ($valor) {
      return trim((string) $valor) !== '';
    })));

    if ($processosUsuario !== [] && $contextoTela === '') {
      return $processosUsuario;
    }

    $nivelId = (int) ($_SESSION['nivel_id'] ?? 0);
    if ($nivelId <= 0) {
      $usuarioId = (int) ($_SESSION['usuario'] ?? 0);
      if ($usuarioId > 0) {
        $usuario = (new \App\Models\Usuarios())
          ->select('nivel_id')
          ->where('id', $usuarioId)
          ->first();

        $nivelId = (int) ($usuario['nivel_id'] ?? 0);
        if ($nivelId > 0) {
          $_SESSION['nivel_id'] = $nivelId;
        }
      }
    }

    if ($nivelId <= 0) {
      return $processosUsuario;
    }

    $this->sincronizarContextoNivelSessao($nivelId);

    if ($contextoTela !== '') {
      $processosPorContexto = is_array($_SESSION['processos_por_contexto'] ?? null) ? $_SESSION['processos_por_contexto'] : [];
      $contextoDefinido = array_key_exists($contextoTela, $processosPorContexto);
      $processosContexto = $contextoDefinido ? $processosPorContexto[$contextoTela] : null;
      if (is_array($processosContexto)) {
        $processosContexto = array_values(array_unique(array_filter(array_map('strval', $processosContexto), static function ($valor) {
          return trim((string) $valor) !== '';
        })));

        if ($processosContexto !== []) {
          return $processosContexto;
        }
      }

      if (!$contextoDefinido && $processosUsuario !== []) {
        return $processosUsuario;
      }
    }

    $processosDetalhados = $contextoTela !== '' && $contextoDefinido
      ? []
      : (new \App\Models\Nivel())->listarProcessosDetalhados($nivelId);
    foreach ($processosDetalhados as $processoDetalhado) {
      $nome = trim((string) ($processoDetalhado['nome'] ?? ''));
      if ($nome !== '') {
        $processosUsuario[] = $nome;
      }
    }

    if ($processosUsuario === [] && $this->permissoesPodemVerListasSemVinculo($permissoesUsuario)) {
      $processosAtivos = (new \App\Models\Processos())
        ->select('nome')
        ->where('status', 'ativo')
        ->orderBy('id', 'ASC')
        ->findAll();

      foreach ($processosAtivos as $processoAtivo) {
        $nome = trim($this->decodificarValor($processoAtivo['nome'] ?? ''));
        if ($nome !== '') {
          $processosUsuario[] = $nome;
        }
      }
    }

    $processosUsuario = array_values(array_unique($processosUsuario));
    $_SESSION['processos'] = $processosUsuario;

    return $processosUsuario;
  }

  private function usuarioPodeVerProcesso(array $permissoesUsuario, array $processosUsuario, string $nomeProcesso, string $nomeProcessoCodificado): bool
  {
    if (in_array('Processos', $permissoesUsuario, true) || in_array('all', $permissoesUsuario, true)) {
      return true;
    }

    $nomeProcessoComUnderscore = str_replace(' ', '_', $nomeProcesso);

    return in_array($nomeProcesso, $processosUsuario, true)
      || in_array($nomeProcessoComUnderscore, $processosUsuario, true)
      || in_array($nomeProcessoCodificado, $processosUsuario, true);
  }

  private function permissoesPodemVerListasSemVinculo(array $permissoesUsuario): bool
  {
    $permissoesLiberadas = [
      'all',
      'Processos',
      'Lista_De_Corte_Cortador',
      'Lista De Corte',
      'Lista_De_Corte',
      'Lista_De_Corte_ADM',
      'Lista De Corte ADM',
      'lista_tarefas',
      'lista_tarefas_adm',
      'lista_corte',
      'lista_corte_adm',
    ];

    foreach ($permissoesLiberadas as $permissao) {
      if (in_array($permissao, $permissoesUsuario, true)) {
        return true;
      }
    }

    return false;
  }

  private function resolverDependenciaId($dependenciaValor): ?int
  {
    if ($dependenciaValor === null || $dependenciaValor === '' || $dependenciaValor === 'Nenhuma') {
      return null;
    }

    $processosModel = new \App\Models\Processos();

    if (is_numeric($dependenciaValor)) {
      $processoDependencia = $processosModel
        ->select("processos.id AS id")
        ->where('processos.id', (int) $dependenciaValor)
        ->first();

      if (is_array($processoDependencia) && isset($processoDependencia['id'])) {
        return (int) $processoDependencia['id'];
      }

      return null;
    }

    $dependenciaTexto = $this->decodificarValor($dependenciaValor);
    $nomeDependenciaCodificado = Ferramentas::codificador((string) $dependenciaTexto);

    $processoDependencia = $processosModel
      ->select("processos.id AS id")
      ->groupStart()
      ->where('processos.nome', (string) $dependenciaValor)
      ->orWhere('processos.nome', (string) $dependenciaTexto)
      ->orWhere('processos.nome', $nomeDependenciaCodificado)
      ->groupEnd()
      ->first();

    if (is_array($processoDependencia) && isset($processoDependencia['id'])) {
      return (int) $processoDependencia['id'];
    }

    return null;
  }

  private function resolverDependenciaNome($dependenciaValor): string
  {
    if ($dependenciaValor === null || $dependenciaValor === '') {
      return "Nenhuma";
    }

    $dependenciaId = $this->resolverDependenciaId($dependenciaValor);
    if ($dependenciaId === null) {
      $textoDependencia = $this->decodificarValor($dependenciaValor);
      return $textoDependencia !== '' ? $textoDependencia : 'Processo nÃƒÆ’Ã‚Â£o encontrado';
    }

    $processoDependencia = (new \App\Models\Processos())
      ->select("processos.nome AS nome")
      ->where('processos.id', $dependenciaId)
      ->first();

    if (is_array($processoDependencia) && !empty($processoDependencia['nome'])) {
      return $this->decodificarValor($processoDependencia['nome']);
    }

    return 'Processo nÃƒÆ’Ã‚Â£o encontrado';
  }

  private function campoProcessosExiste(string $campo): bool
  {
    static $cache = [];

    if (!array_key_exists($campo, $cache)) {
      $cache[$campo] = \Config\Database::connect()->fieldExists($campo, 'processos');
    }

    return (bool) $cache[$campo];
  }

  private function normalizarIdsFinalidades($valor): array
  {
    if (is_array($valor)) {
      $partes = $valor;
    } else {
      $partes = preg_split('/[,\-\s]+/', (string) ($valor ?? '')) ?: [];
    }

    $ids = [];
    foreach ($partes as $parte) {
      $id = (int) trim((string) $parte);
      if ($id > 0) {
        $ids[$id] = $id;
      }
    }

    return array_values($ids);
  }

  private function dependenciaObrigatoria(array $processo): bool
  {
    if (!$this->campoProcessosExiste('dependencia_obrigatoria')) {
      return true;
    }

    $valor = strtolower(trim((string) ($processo['dependencia_obrigatoria'] ?? '1')));
    return !in_array($valor, ['0', 'false', 'nao', 'opcional'], true);
  }

  private function listarFinalidadesAtivas(): array
  {
    $rows = (new \App\Models\Finalidade())
      ->select('id, nome')
      ->where('status', 'ativo')
      ->orderBy('nome', 'ASC')
      ->findAll();

    $finalidades = [];
    foreach ($rows as $row) {
      $id = (int) ($row['id'] ?? 0);
      if ($id <= 0) {
        continue;
      }

      $finalidades[] = [
        'id' => $id,
        'nome' => $this->decodificarValor($row['nome'] ?? ''),
      ];
    }

    usort($finalidades, static function ($a, $b) {
      return strcasecmp((string) ($a['nome'] ?? ''), (string) ($b['nome'] ?? ''));
    });

    return $finalidades;
  }

  private function montarOptionsFinalidadesDependencia(array $selecionadas): string
  {
    $selecionadas = array_fill_keys($selecionadas, true);
    $html = '';

    foreach ($this->listarFinalidadesAtivas() as $finalidade) {
      $id = (int) $finalidade['id'];
      $nome = htmlspecialchars((string) $finalidade['nome'], ENT_QUOTES, 'UTF-8');
      $selected = isset($selecionadas[$id]) ? ' selected' : '';
      $html .= '<option value="' . $id . '"' . $selected . '>' . $nome . '</option>';
    }

    return $html;
  }

  private function nomesFinalidadesPorIds(array $ids): array
  {
    $ids = $this->normalizarIdsFinalidades($ids);
    if ($ids === []) {
      return [];
    }

    $rows = (new \App\Models\Finalidade())
      ->select('id, nome')
      ->whereIn('id', $ids)
      ->findAll();

    $nomesPorId = [];
    foreach ($rows as $row) {
      $id = (int) ($row['id'] ?? 0);
      if ($id > 0) {
        $nomesPorId[$id] = $this->decodificarValor($row['nome'] ?? '');
      }
    }

    $nomes = [];
    foreach ($ids as $id) {
      if (isset($nomesPorId[$id])) {
        $nomes[] = $nomesPorId[$id];
      }
    }

    return $nomes;
  }

  private function idsFinalidadesInvalidas(array $ids): array
  {
    $ids = $this->normalizarIdsFinalidades($ids);
    if ($ids === []) {
      return [];
    }

    $rows = (new \App\Models\Finalidade())
      ->select('id')
      ->whereIn('id', $ids)
      ->findAll();

    $existentes = [];
    foreach ($rows as $row) {
      $id = (int) ($row['id'] ?? 0);
      if ($id > 0) {
        $existentes[$id] = true;
      }
    }

    return array_values(array_filter($ids, static function ($id) use ($existentes) {
      return !isset($existentes[$id]);
    }));
  }

  private function resumoDependenciaLista(array $processo, string $dependenciaNome): string
  {
    $dependenciaNome = trim($dependenciaNome) !== '' ? $dependenciaNome : 'Nenhuma';
    $html = htmlspecialchars($dependenciaNome, ENT_QUOTES, 'UTF-8');

    if ($dependenciaNome === 'Nenhuma') {
      return $html;
    }

    $obrigatoria = $this->dependenciaObrigatoria($processo);
    $badgeClasse = $obrigatoria ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success';
    $badgeTexto = $obrigatoria ? 'Obrigatoria' : 'Opcional';
    $html .= '<br><span class="badge ' . $badgeClasse . '">' . $badgeTexto . '</span>';

    $idsFinalidades = $this->normalizarIdsFinalidades($processo['dependencia_finalidades_opcionais'] ?? '');
    $nomesFinalidades = $this->nomesFinalidadesPorIds($idsFinalidades);
    if ($nomesFinalidades !== []) {
      $html .= '<br><small class="text-muted">Opcional: ' . htmlspecialchars(implode(', ', $nomesFinalidades), ENT_QUOTES, 'UTF-8') . '</small>';
    }

    return $html;
  }


  /**
   * Cadastra um novo processo no banco de dados, validando os dados fornecidos via requisiÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o AJAX.
   * 
   * Esta funÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o realiza as seguintes operaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes:
   * - ValidaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o dos campos 'nome', 'diretÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â³rio' e 'extensÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o' de acordo com os critÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â©rios de tamanho e caracteres permitidos.
   * - VerificaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o da existÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âªncia de tipos de arquivo na lista de extensÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes permitidas.
   * - VerificaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o da existÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âªncia prÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â©via do processo no banco de dados para evitar duplicaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes.
   * - InserÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o de violaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes no banco de dados caso alguma validaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o falhe.
   * - Caso todas as validaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes sejam aprovadas, o processo ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â© cadastrado com suas informaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes no banco de dados.
   * 
   */
  function processos_cadastrar()
  {
    if ($this->request->isAJAX()) {
      $msg = array();
      $ok = false;
      $violacao = array();
      $nome = service('request')->getPost('nome');
      $diretorio = service('request')->getPost('diretorio');
      $extencao = service('request')->getPost('extencao');
      $multivalorado = service('request')->getPost('multivalorado');
      session_start();
      if (strlen($nome) > 100) {
        $msg['Nome'] = "Nome excedeu o tamanho mÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡ximo de 100 caracter";
        $violacao[] = "processos_cadastrar nome excedeu o tamanho mÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡ximo";
      }

      if (strlen($nome) < 2) {
        $msg['Nome'] = "Nome nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o possui o tamanho mÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â­nimo de 2 caracter";
      } else {
        if (Ferramentas::codificador($nome) == '') {
          $msg['Nome'] = "Nome possui caracteres nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o permitidos";
          $violacao[] = "processos_cadastrar nome possui caracteres nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o permitidos";
        }
      }

      if (strlen($diretorio) > 100) {
        $msg['Nome da Pasta'] = "Nome da Pasta excedeu o tamanho mÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡ximo de 100 caracter";
        $violacao[] = "processos_cadastrar diretorio excedeu o tamanho mÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡ximo";
      }

      if (strlen($diretorio) < 2) {
        $msg['Nome da Pasta'] = "Nome da Pasta nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o possui o tamanho mÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â­nimo de 2 caracter";
      } else {
        if (Ferramentas::codificador($diretorio) == '') {
          $msg['Nome da Pasta'] = "Nome da Pasta possui caracteres nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o permitidos";
          $violacao[] = "processos_cadastrar diretorio possui caracteres nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o permitidos";
        }
      }


      foreach (explode('-', str_replace('_', ' ', $extencao)) as $key => $value) {
        if (!in_array($value, explode(',',  $_SESSION["lista_extencao"]))) {
          $msg['Tipo de Arquico'] = "Tipo de Arquico nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o existe: " . $value;
          $violacao[] = "processos_cadastrar tipo de arquivo nao existe";
        }
      }
      if ($extencao == '') {
        $msg['Tipo de Arquico'] = "Tipo de Arquico nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o existe selecionados";
      }
      if (count($msg) == 0 and count($violacao) == 0) {


        $prioridade = new \App\Models\Filtros(); //pega do banco a tabela

        $prioridade_data = $prioridade->find();
        $filtros_array = array();
        foreach (explode('-', $extencao) as $key1 => $value1) {
          foreach ($prioridade_data as $key => $value) { //cria a lista 
            if (Ferramentas::decodificador($value['nome']) == Ferramentas::decodificador(substr($value1, 1))) {
              $filtros_array[] = $value['id'];
            }
          }
        }


        $db = new \App\Models\Processos();



        $processos_data = $db->find();

        if (
          count(Ferramentas::array_pesquisa_mult($processos_data, ['nome', 'diretorio'], [Ferramentas::codificador($nome), Ferramentas::codificador(Ferramentas::norma_lizar_str($diretorio))])) == 0 and
          count(Ferramentas::array_pesquisa_mult($processos_data, ['diretorio'], [Ferramentas::codificador(Ferramentas::norma_lizar_str($diretorio))])) == 0
        ) { // verifica se o id do mepreendimento com o mesmo nome ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â© igual ao id 
          $input = "ind";
          if(isset($multivalorado) && $multivalorado == 'true'){
            $input = "mult";
          }

          $date = [
            'nome' => Ferramentas::codificador($nome),
            'diretorio' => (Ferramentas::norma_lizar_str($diretorio)),
            'filtros_id' => implode('-', $filtros_array),
            'data_hora_add' => Ferramentas::codificador(date('d/m/Y H:i')),
            'usuario_id' => $_SESSION["usuario"],
            'status' => 'ativo',
            'input' => $input
          ];
          if ($this->campoProcessosExiste('dependencia_obrigatoria')) {
            $date['dependencia_obrigatoria'] = 1;
          }
          if ($this->campoProcessosExiste('dependencia_finalidades_opcionais')) {
            $date['dependencia_finalidades_opcionais'] = '';
          }
          $db->insert($date);
          
          $id = $db->insertID();

          


          // Instancia o model e busca os filtros existentes no banco para esse processo
          $db_proc_ext = new \App\Models\Processos_filtro();
          $filtros_existentes = $db_proc_ext
            ->where('processos_id', $id)
            ->findAll();

          // Cria array associativo com os filtros existentes no banco
          $filtros_db = [];
          foreach ($filtros_existentes as $item) {
            $filtros_db[$item['filtros_id']] = $item; // indexado pelo filtros_id
          }

          // Verifica e processa cada filtro do banco
          foreach ($filtros_db as $filtro_id => $dados) {
            if (!in_array($filtro_id, $filtros_array)) {
              // EstÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡ no banco, mas nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o na lista nova: desativar se estiver ativo
              if ($dados['status'] === 'ativo') {
                $db_proc_ext->update($dados['id'], ['status' => 'desativado']);
              }
            }
          }

          // Verifica e insere novos filtros que estÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o no array mas nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o estÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o no banco
          foreach ($filtros_array as $filtro_id) {
            if (!isset($filtros_db[$filtro_id])) {
              // Inserir novo filtro
              $data = [
                "usuario_id" => $_SESSION['usuario'],
                "processos_id" => $id,
                "filtros_id" => $filtro_id,
                "status" => "ativo"
              ];
              $db_proc_ext->insert($data);
            }
          }
          $ok = true;
        } else {
          $msg["Processo"] = 'Processo jÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡ existente';
          $violacao[] = "processos_cadastrar nivel jÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡ existente";
        }
      }
      if (count($violacao) != 0) {

        //$violacao = Ferramentas::array_codificar($violacao);
        $db = new \App\Models\Violacao();
        foreach ($violacao as $key => $value) {

          $data = [
            "usuario_id" => $_SESSION["usuario"],
            "causa" => $value

          ];

          $db->insert($data);
        }
      }

      $data = ['ok' => $ok, 'msg' => $msg];
      return $this->response->setJSON($data);
    }
  }


  /**
   * Gera uma lista de processos com base em seu status (ativos ou desativados) e retorna os dados formatados para exibiÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o em uma tabela via AJAX.
   * 
   * Esta funÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o realiza as seguintes operaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes:
   * - Inicializa a sessÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o para armazenar a lista de IDs e dados completos dos processos.
   * - ObtÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â©m dados de processos e filtros do banco de dados e organiza a lista com base nos status ativos ou desativados.
   * - Para cada processo, verifica se ele deve ser exibido como ativo ou desativado, formatando as informaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes em uma tabela HTML.
   * - Armazena os IDs e os detalhes completos dos processos em variÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡veis de sessÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o para uso posterior.
   * 
   */
  function processos()
  {
    if ($this->request->isAJAX()) {
      // Inicializa a sessÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o para acessar os dados da lista armazenados nela
      session_start();
      $processos = new \App\Models\Processos(); // ObtÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â©m a tabela de finalidades do banco


      $processos_data = $processos->find();
      $ativos = service('request')->getPost('ativos'); // ObtÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â©m a informaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o POST fornecida via AJAX para listar finalidades ativas
      $desativados = service('request')->getPost('desativados'); // ObtÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â©m a informaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o POST fornecida via AJAX para listar finalidades desativadas
      $lista = "";
      $lista_ids = array();
      $id_temp = 0;
      $lista_completa = array();
      $filtro = new \App\Models\Filtros;
      $filtro_data = $filtro->find();
      foreach ($processos_data as $key => $value) {
       
   
        $filtros = array();

        $filtros_data = (new \App\Models\Processos_filtro())
          ->select("filtros.nome         AS nome,
                    filtros.status         AS status")
          ->join('filtros',      'filtros.id        = processos_filtro.filtros_id',        'left')
          ->where('processos_filtro.processos_id',  $value['id'])
          ->where('processos_filtro.status', 'ativo')
          ->findAll();

        foreach ($filtros_data as $key => $value1) {
           $filtros[] = '.'.$value1['nome'];
        }

        $value['processos_id_proximo'] = $this->resolverDependenciaNome($value['processos_id_proximo'] ?? null);
        $dependenciaResumo = $this->resumoDependenciaLista($value, (string) ($value['processos_id_proximo'] ?? 'Nenhuma'));

// Cria a lista com base nas finalidades ativas ou desativadas, dependendo da solicitaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o
        if (($ativos == 'true' && Ferramentas::decodificador($value['status']) == 'ativo')) { //verifica se ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â© para mostrar os com estus ativo
          $lista .= '
      <tr>
       <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['nome']) . '</p></td>
       <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['diretorio']) . '</p></td>
       <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . implode("&nbsp&nbsp|&nbsp&nbsp", $filtros) . '</p></td>
       <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . $dependenciaResumo . '</p></td>
       <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
       <td><button name="cadastarar" type="submit" onclick="desativar(\'' . $id_temp . '\')" class="btn btn-outline-danger btn-lg btn-block"> Desativar </button></td>
       <td><button name="cadastarar" type="submit" class="btn btn-outline-warning btn-lg btn-block" onclick="modal_modificar(\'modal_' . $id_temp . '\')"> Modificar </button></td>
      </tr>
      ';
        } else if (($desativados == 'true' && Ferramentas::decodificador($value['status']) == 'desativado')) { //verifica se ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â© para mostrar os com estus desativado
          $lista .= '
      <tr>
       <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['nome']) . '</p></td>
       <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['diretorio']) . '</p></td>
       <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . implode("&nbsp&nbsp|&nbsp&nbsp", $filtros) . '</p></td>
       <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . $dependenciaResumo . '</p></td>
       <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
       <td><button name="cadastarar" type="submit" onclick="ativar(\'' . $id_temp . '\')" class="btn btn-outline-success btn-lg btn-block"> Ativar </button></td>
       <td><button name="cadastarar" type="submit" class="btn btn-outline-warning btn-lg btn-block" onclick="modal_modificar(\'modal_' . $id_temp . '\')"> Modificar </button></td>
      </tr>
      ';
        }


        $lista_ids[$id_temp] = $value['id'];
        $lista_completa[$id_temp] = $value;
        $id_temp++;
      }

      // Armazena os IDs e detalhes da lista na sessÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o para uso posterior
      $_SESSION["lista"] = $lista_ids;
      $_SESSION["lista_completa"] = $lista_completa;



      //retorna a lista para o ajax
      $data = [
        "lista" => $lista,


      ];

      return $this->response->setJSON($data);
    }
  }




  /**
   * Gera uma lista de processos ativos com base nas permissÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes do usuÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡rio e retorna os dados formatados via AJAX.
   * 
   * Esta funÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o realiza as seguintes operaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes:
   * - Inicializa a sessÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o para acessar e armazenar os dados da lista de processos.
   * - ObtÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â©m os processos ativos do banco de dados e filtra os que podem ser exibidos, com base nas permissÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes do usuÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡rio, incluindo permissÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes especÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â­ficas ou globais ('Processos' ou 'all').
   * - Para cada processo vÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡lido, os filtros associados sÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o buscados e a lista ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â© organizada em um array.
   * - Armazena a lista completa dos processos na sessÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o para uso posterior.
   * 
   */
  function processos_lista()
  {
    if ($this->request->isAJAX()) {
      // Inicializa a sessÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o para acessar os dados da lista armazenados nela
      session_start();

      $processos = new \App\Models\Processos(); // ObtÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â©m a tabela de prioridades do banco
      $desenhos = new \App\Models\Desenhos();
      $permissoesUsuario = is_array($_SESSION['permissao'] ?? null) ? $_SESSION['permissao'] : [];
      $contextoTela = $this->resolverContextoTelaProcessosLista();
      $processosUsuario = $this->obterProcessosPermitidosSessao($contextoTela);

      $processos_data = $processos->find();
      $lista = array();

      $lista_session = array();
      $ultimasDatasProcesso = [];

      $ultimasDatasRows = $desenhos
        ->select('processos_id, MAX(data_add) AS ultima_data')
        ->groupBy('processos_id')
        ->findAll();

      foreach ($ultimasDatasRows as $rowUltimaData) {
        $processoIdMapa = (int) ($rowUltimaData['processos_id'] ?? 0);
        if ($processoIdMapa <= 0) {
          continue;
        }
        $ultimasDatasProcesso[$processoIdMapa] = (string) ($rowUltimaData['ultima_data'] ?? '');
      }

      foreach ($processos_data as $key => $value) {
        // Cria a lista com base nas prioridades ativas
        $nomeProcessoCodificado = (string) ($value['nome'] ?? '');
        $nomeProcesso = $this->decodificarValor($nomeProcessoCodificado);


        if ($value['status'] == 'ativo' && $this->usuarioPodeVerProcesso($permissoesUsuario, $processosUsuario, $nomeProcesso, $nomeProcessoCodificado)) {


          $filtros = array();

          $db_proc_ext = new \App\Models\Processos_filtro();
          $filtros_existentes = $db_proc_ext
            ->select('processos_filtro.*, 
              filtros.nome AS nome_filtro')
            ->join('filtros', 'filtros.id = processos_filtro.filtros_id')
            ->where('processos_filtro.processos_id', $value['id'])
            ->where('processos_filtro.status', 'ativo')
            ->findAll();
          // Cria array associativo com os filtros existentes no banco

          foreach ($filtros_existentes as $item) {
            $filtros[$item['filtros_id']] = '.'.$item['nome_filtro']; // indexado pelo filtros_id
          }


          $temp['input'] = Ferramentas::decodificador($value['input']);
          $temp['nome'] = $nomeProcesso;
          $temp['filtro'] = implode(",", $filtros);
          $temp['id'] = (int) ($value['id'] ?? 0);
          $temp['ultima_data'] = $ultimasDatasProcesso[$temp['id']] ?? '';

          $lista[] = $temp;

          $temp['id'] = Ferramentas::decodificador($value['id']);
          $temp['diretorio'] = Ferramentas::decodificador($value['diretorio']);
          $lista_session[] = $temp;
        }
      }
      $_SESSION['processos_lista']['lista'] = $lista_session;
      $_SESSION['processos_lista']['contexto_tela'] = $contextoTela;
      //retorna a lista para o ajax
      $data = [
        "lista" => $lista
      ];

      return $this->response->setJSON($data);
    }
  }




  /**
   * Gera e retorna um modal para modificar informaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes de um processo especÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â­fico, baseado nos dados fornecidos via AJAX.
   * 
   * Esta funÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o realiza as seguintes operaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes:
   * - Inicializa a sessÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o para acessar a lista de processos armazenados.
   * - ObtÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â©m os filtros ativos do banco de dados e organiza as opÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes de filtro para serem exibidas no modal.
   * - Se um ID de processo for fornecido, as informaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes do processo sÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o carregadas e o modal ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â© preenchido com seus dados. Caso contrÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡rio, o modal ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â© inicializado vazio.
   * - ConstrÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â³i o HTML do modal contendo campos de nome, diretÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â³rio e filtros relacionados ao processo.
   * 
   */
  function processos_modifica_modal()
  {
    if ($this->request->isAJAX()) {
      $idIndice = service('request')->getPost('id');
      session_start();

      $filtro_db = new \App\Models\Filtros(); // Obtem a tabela de filtros do banco
      $filtro_data = $filtro_db->find();

      $array_filtro = array();
      foreach ($filtro_data as $value) {
        if (Ferramentas::array_index($value, ["status"]) == 'ativo') {
          $array_filtro[] = Ferramentas::decodificador(Ferramentas::array_index($value, ["nome"]));
        }
      }

      $lista = [
        "diretorio" => "",
        "nome" => "",
        "processos_id_proximo" => null
      ];
      $processoAtualId = 0;

      if ($idIndice !== null && $idIndice !== '') {
        if (isset($_SESSION["lista_completa"]) && is_array($_SESSION["lista_completa"]) && isset($_SESSION["lista_completa"][$idIndice])) {
          $processoAtualId = (int) ($_SESSION["lista_completa"][$idIndice]['id'] ?? 0);
        } elseif (is_numeric($idIndice)) {
          // fallback para cenarios em que o frontend envie o id real
          $processoAtualId = (int) $idIndice;
        }
      }

      $processosModel = new \App\Models\Processos();
      if ($processoAtualId > 0) {
        $registroProcesso = $processosModel->find($processoAtualId);
        if (is_array($registroProcesso) && !empty($registroProcesso)) {
          $lista = array_merge($lista, $registroProcesso);
        }
        $_SESSION["modal_id"] = $processoAtualId;
      } else {
        unset($_SESSION["modal_id"]);
      }

      $enable_filtros = "disabled";
      $option_filtros = "";

      $db_proc_ext = new \App\Models\Processos_filtro();
      $filtros_existentes = [];
      if (!empty($_SESSION["modal_id"])) {
        $filtros_existentes = $db_proc_ext
          ->select('filtros.nome AS nome_filtro')
          ->join('filtros', 'filtros.id = processos_filtro.filtros_id')
          ->where('processos_filtro.processos_id', $_SESSION["modal_id"])
          ->where('processos_filtro.status', 'ativo')
          ->findAll();
      }

      $filtrosSelecionados = array_column($filtros_existentes, 'nome_filtro');
      foreach ($array_filtro as $item) {
        $itemSeguro = htmlspecialchars((string) $item, ENT_QUOTES, 'UTF-8');
        if (in_array($item, $filtrosSelecionados, true)) {
          $option_filtros .= '<option value="' . $itemSeguro . '" selected>.' . $itemSeguro . '</option>';
        } else {
          $option_filtros .= '<option value="' . $itemSeguro . '">.' . $itemSeguro . '</option>';
          $enable_filtros = "";
        }
      }

      $dependenciaAtualId = $this->resolverDependenciaId($lista['processos_id_proximo'] ?? null) ?? 0;
      $dependencia = '<option value="">Nenhuma</option>';
      $dependenciaEncontrada = false;

      $processosAtivos = $processosModel
        ->select("processos.id AS id, processos.nome AS nome")
        ->where('processos.status', 'ativo')
        ->orderBy('processos.nome', 'ASC')
        ->findAll();

      foreach ($processosAtivos as $value) {
        $processoId = (int) ($value['id'] ?? 0);
        if ($processoId <= 0) {
          continue;
        }

        // evita dependencia circular do processo com ele mesmo
        if (!empty($_SESSION["modal_id"]) && $processoId === (int) $_SESSION["modal_id"]) {
          continue;
        }

        $processoNome = Ferramentas::decodificador((string) ($value['nome'] ?? ''));
        $processoNomeSeguro = htmlspecialchars($processoNome, ENT_QUOTES, 'UTF-8');
        $selecionado = '';
        if ($dependenciaAtualId > 0 && $processoId === $dependenciaAtualId) {
          $selecionado = ' selected';
          $dependenciaEncontrada = true;
        }

        $dependencia .= '<option value="' . $processoId . '"' . $selecionado . '>' . $processoNomeSeguro . '</option>';
      }

      // Mantem visivel a dependencia atual mesmo se estiver inativa
      if ($dependenciaAtualId > 0 && !$dependenciaEncontrada) {
        $dependenciaAtual = $processosModel->find($dependenciaAtualId);
        if (is_array($dependenciaAtual) && !empty($dependenciaAtual)) {
          $nomeDependencia = Ferramentas::decodificador((string) ($dependenciaAtual['nome'] ?? ''));
          $nomeDependenciaSeguro = htmlspecialchars($nomeDependencia, ENT_QUOTES, 'UTF-8');
          $dependencia .= '<option value="' . $dependenciaAtualId . '" selected>' . $nomeDependenciaSeguro . ' (inativo)</option>';
        }
      }

      $nomeProcesso = htmlspecialchars(Ferramentas::decodificador((string) ($lista["nome"] ?? '')), ENT_QUOTES, 'UTF-8');
      $diretorioProcesso = htmlspecialchars(Ferramentas::decodificador((string) ($lista["diretorio"] ?? '')), ENT_QUOTES, 'UTF-8');
      $dependenciaObrigatoriaChecked = $this->dependenciaObrigatoria($lista) ? ' checked' : '';
      $finalidadesSelecionadas = $this->normalizarIdsFinalidades($lista['dependencia_finalidades_opcionais'] ?? '');
      $optionFinalidadesDependencia = $this->montarOptionsFinalidadesDependencia($finalidadesSelecionadas);

      $conteudo = [
        0 => '<div class="form-group">
        <label>Nome</label>
        <input type="text" class="form-control" id="nome_processos_novo_modal" placeholder="Novo Processo" value="' . $nomeProcesso . '">
      </div>
      <div class="form-group">
      <label>Pasta</label>
        <input type="text" class="form-control" id="diretorio_novo_modal" placeholder="Nome da Pasta" value="' . $diretorioProcesso . '">
      </div>
      <div class="form-group">
        <label>Dependencia</label>
        <select id="processo_dependencia" class="custom-select">' . $dependencia . '</select>
      </div>
      <div class="form-group">
        <div class="form-check form-check-primary">
          <input class="form-check-input" type="checkbox" id="dependencia_obrigatoria"' . $dependenciaObrigatoriaChecked . '>
          <label class="form-check-label" for="dependencia_obrigatoria">Dependencia obrigatoria</label>
        </div>
      </div>
      <div class="form-group">
        <label>Finalidades com dependencia opcional</label>
        <select id="dependencia_finalidades_opcionais" class="custom-select" multiple="multiple" size="5">' . $optionFinalidadesDependencia . '</select>
      </div>

            <div class="form-group">
            <label>Processos</label><br/>
            <select multiple="multiple" class="form-control" id="extencao_novo_modal" ' . $enable_filtros . '>' . $option_filtros . ' </select>
                </div>'
      ];

      $modal = '<div id="modal" class="modal-1" style="display: block;">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modal_titulo">Modificar Setor: ' . $nomeProcesso . '</h5>
              <button type="button" class="close" onclick="fecharModal()">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body" id="modal_bory"><div class="form-group">
        ' . $conteudo[0] . '


            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" id="botao_fechar_modal" onclick="fecharModal()">Cancelar</button>
              <button type="button" class="btn btn-primary" id="botao_confirmar_modal" onclick="confirmarModal()">Confirmar</button>
            </div></div></div>
          </div>
        </div>
      </div>';

      $data = ['modal' => $modal, 'conteudo' => $conteudo[0], '1' => $filtros_existentes];
      return $this->response->setJSON($data);
    }
  }



  /**
   * Modifica as informaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes de um processo existente no banco de dados com base nos dados fornecidos via AJAX.
   * 
   * Esta funÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o realiza as seguintes operaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes:
   * - Valida os campos 'nome', 'diretÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â³rio' e 'extensÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o' de acordo com os critÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â©rios de tamanho e caracteres permitidos.
   * - Verifica a existÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âªncia dos tipos de arquivo selecionados nos filtros disponÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â­veis.
   * - Se todas as validaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes forem aprovadas, o processo ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â© atualizado no banco de dados.
   * - Caso o processo jÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡ exista com os mesmos dados ou nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o haja alteraÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes, uma mensagem apropriada ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â© retornada.
   * - Registra violaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âµes, se houver, no banco de dados, armazenando a causa e o usuÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡rio responsÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡vel.
   * 
   */
  function processos_modificar()
  {
    if ($this->request->isAJAX()) {
      $msg = array();
      $ok = false;
      $violacao = array();
      $nome = service('request')->getPost('nome');
      $diretorio = service('request')->getPost('diretorio');
      $extencao = service('request')->getPost('extencao');
      $dependencia = service('request')->getPost('dependencia');
      $dependenciaObrigatoriaPost = service('request')->getPost('dependencia_obrigatoria');
      $dependenciaFinalidadesPost = service('request')->getPost('dependencia_finalidades_opcionais');
      $dependenciaObrigatoriaValor = ((string) $dependenciaObrigatoriaPost === '0') ? 0 : 1;
      $dependenciaFinalidadesIds = $this->normalizarIdsFinalidades($dependenciaFinalidadesPost);
      $dependenciaFinalidadesValor = implode('-', $dependenciaFinalidadesIds);

      session_start();
      if (strlen($nome) > 100) {
        $msg['Nome'] = "Nome excedeu o tamanho mÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡ximo de 100 caracter";
        $violacao[] = "processos_modificar nome excedeu o tamanho mÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡ximo";
      }

      if (strlen($nome) < 2) {
        $msg['Nome'] = "Nome nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o possui o tamanho mÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â­nimo de 2 caracter";
      } else {
        if (Ferramentas::codificador($nome) == '') {
          $msg['Nome'] = "Nome possui caracteres nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o permitidos";
          $violacao[] = "processos_modificar nome possui caracteres nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o permitidos";
        }
      }

      if (strlen($diretorio) > 100) {
        $msg['Nome da Pasta'] = "Nome da Pasta excedeu o tamanho mÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡ximo de 100 caracter";
        $violacao[] = "processos_modificar diretorio excedeu o tamanho mÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡ximo";
      }

      if (strlen($diretorio) < 2) {
        $msg['Nome da Pasta'] = "Nome da Pasta nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o possui o tamanho mÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â­nimo de 2 caracter";
      } else {
        if (Ferramentas::codificador($diretorio) == '') {
          $msg['Nome da Pasta'] = "Nome da Pasta possui caracteres nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o permitidos";
          $violacao[] = "processos_modificar diretorio possui caracteres nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o permitidos";
        }
      }
      $filtro = new \App\Models\Filtros;
      $filtro_data = $filtro->find();
      $filtros = array();
      foreach ($filtro_data as $key => $value1) {
        if ($value1['status'] == 'ativo') {
          $filtros[] =  Ferramentas::decodificador($value1['nome']);
        }
      }


      foreach (explode('-', str_replace('_', ' ', $extencao)) as $key => $value) {
        if (!in_array($value, $filtros)) {
          $msg['Tipo de Arquico'] = "Tipo de Arquico nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o existe: " . $value;
          $violacao[] = "processos_modificar tipo de arquivo nao existe";
        }
      }
      if ($extencao == '') {
        $msg['Tipo de Arquico'] = "Tipo de Arquico nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o existe selecionados";
      }

      $processo_nome = null;
      $finalidadesInvalidas = $this->idsFinalidadesInvalidas($dependenciaFinalidadesIds);
      if ($finalidadesInvalidas !== []) {
        $msg['Finalidade'] = "Finalidade da dependencia opcional nao existe: " . implode(', ', $finalidadesInvalidas);
        $violacao[] = "processos_modificar finalidade dependencia opcional nao existe";
      }
      if ($dependencia !== null && $dependencia !== '' && $dependencia !== 'Nenhuma') {
        $processo_nome = $this->resolverDependenciaId($dependencia);

        if ($processo_nome === null) {
          $msg['DependÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âªncia'] = "DependÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âªncia nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o existe: " . $dependencia;
          $violacao[] = "processos_modificar dependencia nao existe";
        }
      }
      

      if (count($msg) == 0 and count($violacao) == 0) {


        $prioridade = new \App\Models\Filtros(); //pega do banco a tabela

        $prioridade_data = $prioridade->find();
        $filtros_array = array();
        foreach (explode('-', $extencao) as $key1 => $value1) {
          foreach ($prioridade_data as $key => $value) { //cria a lista 
            if (Ferramentas::decodificador($value['nome']) == Ferramentas::decodificador($value1)) {
              $filtros_array[] = $value['id'];
            }
          }
        }


        $db = new \App\Models\Processos();
        $processos_data = $db->find();

        $id = $_SESSION["modal_id"];

        $camposComparacao = ['nome', 'diretorio', 'filtros_id', 'processos_id_proximo'];
        $valoresComparacao = [Ferramentas::codificador($nome), Ferramentas::codificador(Ferramentas::norma_lizar_str($diretorio)), implode('-', $filtros_array), $processo_nome];
        if ($this->campoProcessosExiste('dependencia_obrigatoria')) {
          $camposComparacao[] = 'dependencia_obrigatoria';
          $valoresComparacao[] = $dependenciaObrigatoriaValor;
        }
        if ($this->campoProcessosExiste('dependencia_finalidades_opcionais')) {
          $camposComparacao[] = 'dependencia_finalidades_opcionais';
          $valoresComparacao[] = $dependenciaFinalidadesValor;
        }

        if (count(Ferramentas::array_pesquisa_mult($processos_data, $camposComparacao, $valoresComparacao) ) == 0 and ((count(Ferramentas::array_pesquisa_mult($processos_data, ['diretorio'], [Ferramentas::codificador(Ferramentas::norma_lizar_str($diretorio))])) == 0 or count(Ferramentas::array_pesquisa_mult($processos_data, ['id', 'diretorio'], [$id, Ferramentas::codificador(Ferramentas::norma_lizar_str($diretorio))])) != 0) and
          (count(Ferramentas::array_pesquisa_mult($processos_data, ['nome'], [Ferramentas::codificador($nome)])) == 0 or count(Ferramentas::array_pesquisa_mult($processos_data, ['id', 'nome'], [$id, Ferramentas::codificador($nome)])) != 0))) { // verifica se o id do mepreendimento com o mesmo nome ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â© igual ao id 

          $date = [
            'nome' => Ferramentas::codificador($nome),
            'diretorio' => Ferramentas::norma_lizar_str($diretorio),
            'processos_id_proximo' => $processo_nome
          ];
          if ($this->campoProcessosExiste('dependencia_obrigatoria')) {
            $date['dependencia_obrigatoria'] = $dependenciaObrigatoriaValor;
          }
          if ($this->campoProcessosExiste('dependencia_finalidades_opcionais')) {
            $date['dependencia_finalidades_opcionais'] = $dependenciaFinalidadesValor;
          }
          $db->update($id, $date);

          // Instancia o model e busca os filtros existentes no banco para esse processo
          $db_proc_ext = new \App\Models\Processos_filtro();
          $filtros_existentes = $db_proc_ext
            ->where('processos_id', $id)
            ->findAll();

          // Cria array associativo com os filtros existentes no banco
          $filtros_db = [];
          foreach ($filtros_existentes as $item) {
            $filtros_db[$item['filtros_id']] = $item; // indexado pelo filtros_id
          }

          // Verifica e processa cada filtro do banco
          foreach ($filtros_db as $filtro_id => $dados) {
            if (!in_array($filtro_id, $filtros_array)) {
              // EstÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡ no banco, mas nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o na lista nova: desativar se estiver ativo
              if ($dados['status'] === 'ativo') {
                $db_proc_ext->update($dados['id'], ['status' => 'desativado']);
              }
            }
          }

          // Verifica e insere novos filtros que estÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o no array mas nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o estÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o no banco
          foreach ($filtros_array as $filtro_id ) {
            if (!isset($filtros_db[$filtro_id]) or (isset($filtros_db[$filtro_id]) and $filtros_db[$filtro_id]['status'] == 'desativado')) {
              // Inserir novo filtro
              $data = [
                "usuario_id" => $_SESSION['usuario'],
                "processos_id" => $id,
                "filtros_id" => $filtro_id,
                "status" => "ativo"
              ];
              $db_proc_ext->insert($data);
            }
          }
          $ok = true;
        } else {
          $camposComparacaoComId = array_merge(['id'], $camposComparacao);
          $valoresComparacaoComId = array_merge([$id], $valoresComparacao);
          if (count(Ferramentas::array_pesquisa_mult($processos_data, $camposComparacaoComId, $valoresComparacaoComId)) == 0) {
            $msg["Processo"] = 'Processo jÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡ existente';
            $violacao[] = "processos_modificar prcoesso jÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡ existente";
          } else {
            $msg["Processo"] = 'NÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o houve alteraÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o';
          }
        }
      }
      if (count($violacao) != 0) {

        //$violacao = Ferramentas::array_codificar($violacao);
        $db = new \App\Models\Violacao();
        foreach ($violacao as $key => $value) {

          $data = [
            "usuario_id" => $_SESSION["usuario"],
            "causa" => $value

          ];

          $db->insert($data);
        }
      }

      $data = ['ok' => $ok, 'msg' => $msg];
      return $this->response->setJSON($data);
    }
  }
}


