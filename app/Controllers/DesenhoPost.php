<?php

namespace App\Controllers;

use App\Controllers\Ferramentas;
use Config\App;

class DesenhoPost extends Ferramentas
{
  private function sessaoListaEhProjetoInd(): bool
  {
    return isset($_SESSION["lista_completa"]['tipo'])
      && $_SESSION["lista_completa"]['tipo'] === 'ind';
  }

  private function apagarDesenhoDaLista(int $desenhoId, array $lista, int $usuarioId): array
  {
    $caminho = Ferramentas::wlStoragePath((string) ($lista['diretorio'] ?? ''));
    $caminho_antigo = $caminho;

    if ($desenhoId <= 0) {
      return ['ok' => 'false', 'mensagem' => 'Desenho invalido para apagar.'];
    }

    if ($caminho === '' || !file_exists($caminho)) {
      (new \App\Models\Desenhos())->update($desenhoId, ['status' => 'apagado']);
      return [
        'ok' => 'true',
        'mensagem' => 'Desenho marcado como apagado.',
        'mensagem_false' => $caminho === '' ? 'Desenho sem caminho' : 'Desenho nao existe',
        '1' => $caminho,
      ];
    }

    $relativo = Ferramentas::wlStorageRelativePath($caminho);
    $diretorioRelativo = str_replace('\\', '/', dirname($relativo));
    $caminho = Ferramentas::wlStoragePath(
      'lixo/' . ($diretorioRelativo === '.' ? '' : $diretorioRelativo)
    );

    $caminho = Ferramentas::normalizePath($caminho);
    $problema = Ferramentas::criet_diretorio($caminho);
    if (count($problema) !== 0) {
      return ['ok' => 'false', 'caminho' => $problema];
    }

    $baseDir = rtrim($caminho, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    $originalName = basename($caminho_antigo);
    $baseName = pathinfo($originalName, PATHINFO_FILENAME);
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);

    do {
      $suffix = date('Ymd_His') . '_' . mt_rand(0, 999);
      $newName = $extension !== '' ? "{$baseName}_{$suffix}.{$extension}" : "{$baseName}_{$suffix}";
      $fullPath = $baseDir . $newName;
    } while (file_exists($fullPath));

    $destino = Ferramentas::normalizePath($baseDir . $newName);
    if (!rename(Ferramentas::normalizePath($caminho_antigo), $destino)) {
      return ['ok' => 'false', 'mensagem' => 'Falha ao transferir para lixeira.'];
    }

    (new \App\Models\Lixo_desenhos())->insert([
      'desenho_id' => $desenhoId,
      'usuario_id' => $usuarioId,
      'diretorio' => $caminho,
      'nome' => $newName,
    ]);

    (new \App\Models\Desenhos())->update($desenhoId, ['status' => 'apagado']);

    (new \App\Models\Alteracoes())->insertWithDetails(
      [
        'usuario_id' => $usuarioId,
        'id_item' => $desenhoId,
        'item' => 'desenho',
        'info_mais' => 'desenho.apagar_desenho',
        '_meta' => [
          'origem' => 'desenho_post',
          'novo_caminho_lixeira' => $caminho . $newName,
          'desenho_nome' => Ferramentas::remove_id_file($this->decodificarValor($lista['nome_arquivo'] ?? $lista['nome'] ?? '')),
        ],
      ],
      [
        [
          'campo' => 'status',
          'valor_antes' => $this->decodificarValor($lista['status'] ?? ''),
          'valor_depois' => 'apagado',
        ],
        [
          'campo' => 'arquivo_origem',
          'valor_antes' => $caminho_antigo,
          'valor_depois' => $caminho . $newName,
        ],
      ]
    );

    return [
      'ok' => 'true',
      'mensagem' => 'Desenho apagado com sucesso.',
      'dir_antigo' => $caminho_antigo,
      'dir_novo' => $caminho . $newName,
    ];
  }

  /**
   * Função desenho_update()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por atualizar a prioridade de um ou mais desenhos, registrando as alterações em um log de alterações.
   *
   * Retorna um JSON indicando se a operação de atualização da prioridade foi bem-sucedida e, em caso de falha, fornece informações sobre a violação.
   */
  function desenho_update()
  {
    if ($this->request->isAJAX()) {
      // Inicializa a sessão para acessar os dados da lista armazenados nela
      session_start();


      // Obtém o array de IDs e a nova prioridade da solicitação
      $array = service('request')->getPost('array');
      $prioridade_nova = service('request')->getPost('prioridade');
      $ordem = service('request')->getPost('ordem');

      $ok = false; // Variável para indicar se a operação foi bem-sucedida
      $violacao = array(); // Array para armazenar informações de violações

      if ($prioridade_nova != '') {
        // Se a nova prioridade não estiver vazia, prossiga com a atualização
        $prioridade = new \App\Models\Prioridade(); // Obtém a tabela de prioridades do banco
        $ok = true; // Define que a operação está OK

        $prioridade_data = $prioridade->find();
        $id_prio = Ferramentas::array_pesquisa($prioridade_data, 'nome', Ferramentas::codificador($prioridade_nova))['id'];

        $desenhos = new \App\Models\Desenhos(); // Obtém a tabela de desenhos do banco
        $ordems = new \App\Models\Ordem();
        foreach ($array as $key => $value) {
          if ($this->sessaoListaEhProjetoInd()) {
            $ids = array();

            foreach ($_SESSION["projeto_todos"][$value] as $key => $value1) {

              $alteracao = new \App\Models\Alteracoes();
              $d = (new \App\Models\Desenhos())
                ->where('desenhos.id', $value1['desenho_id'])
                ->first();

              $data = [
                "usuario_id" => $_SESSION["usuario"],
                "id_item" => $value1['desenho_id'],
                "item" => "desenho",

              ];
              $alteracao->insertWithDetails(
                [
                  "usuario_id" => $_SESSION["usuario"],
                  "id_item" => $value1['desenho_id'],
                  "item" => "desenho",
                  "_meta" => [
                    "acao" => "desenho_update.prioridade",
                    "origem" => "desenho_post",
                  ],
                ],
                [
                  [
                    "campo" => "prioridade",
                    "valor_antes" => (string) ($d['prioridade_id'] ?? ''),
                    "valor_depois" => (string) $id_prio,
                  ],
                ]
              );

              $data = [
                "prioridade_id" => $id_prio
              ];

              $desenhos->update($value1['desenho_id'], $data);

              // opção A: usando where() + update($data)


              // opção B: buscando o PK primeiro e usando update($id, $data)


              $ids[] = $value1['desenho_id'];
            }

            $rec = $ordems
              ->select('id')
              ->where('projeto_id', $_SESSION["projeto_todos"][$value][0]['projeto_id'])
              ->where('desenho_id IS NULL', null, false)
              ->where('processos_id', (int) ($_SESSION["projeto_todos"]['processos_id'] ?? 0))
              ->where('status', 'ativo')
              ->first();
            if ($rec) {
              $ordems->update($rec['id'], $data);
            }
            Ferramentas::reordenarPorPrioridade($_SESSION["projeto_todos"][$value][0]['projeto_id'], $ordem, $id_prio, $_SESSION["projeto_todos"]['processos_id'], true);
          } else {
            // Para cada ID no array de IDs, registre as alterações em um log
            $ids = $_SESSION["lista"][$value];
            $alteracao = new \App\Models\Alteracoes();
            $desenhoAtual = $desenhos
              ->select('id, prioridade_id')
              ->where('id', $ids)
              ->first();

            $data = [
              "usuario_id" => $_SESSION["usuario"],
              "id_item" => $ids,
              "item" => "desenho",

            ];
            $alteracao->insertWithDetails(
              [
                "usuario_id" => $_SESSION["usuario"],
                "id_item" => $ids,
                "item" => "desenho",
                "_meta" => [
                  "acao" => "desenho_update.prioridade",
                  "origem" => "desenho_post",
                ],
              ],
              [
                [
                  "campo" => "prioridade",
                  "valor_antes" => (string) ($desenhoAtual['prioridade_id'] ?? ''),
                  "valor_depois" => (string) $id_prio,
                ],
              ]
            );
            $data = [
              "prioridade_id" => $id_prio

            ];
            $desenhos->update($ids, $data);
            Ferramentas::reordenarPorPrioridade($ids, $ordem, $id_prio, $_SESSION["lista_completa"][$value]['processos_id']);
          }
        }
      } else {
        $violacao[] = "desenho_update prioridade não existe";
      }

      if (count($violacao) != 0) {
        //$violacao = Ferramentas::array_codificar($violacao);
        // Cria uma instância do modelo Violacao
        $db = new \App\Models\Violacao();

        // Para cada violação no array de violações, registra a violação em um banco de dados de violações
        foreach ($violacao as $key => $value) {

          $data = [
            "usuario_id" => $_SESSION["usuario"],
            "causa" => $value

          ];

          // Para cada violação no array de violações, registra a violação em um banco de dados de violações
          $db->insert($data);
        }
      }

      $date = [
        "ok" => $ok

      ];

      return $this->response->setJSON($date);
    }
  }


  /**
   * Função desenho_modal()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por retornar a lista completa de usuários, armazenada em uma sessão, para ser utilizada em um modal ou em outro contexto na interface do usuário.
   *
   * Retorna um JSON contendo a lista completa de usuários armazenada na sessão.
   */
public function desenho_modal()
{
    if (!$this->request->isAJAX()) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    session_start();

    $DEBUG = ((int)($this->request->getGet('debug') ?? 0) === 1);
    $dbg = [];
    $log = function ($titulo, $data = null) use (&$dbg, $DEBUG) {
        if (!$DEBUG) return;
        $dbg[] = ['titulo' => $titulo, 'data' => $data];
    };

    // 1) id vindo do AJAX
    $idRaw = $this->request->getPost('id');
    $id = (is_string($idRaw) && $idRaw !== '' && ctype_digit($idRaw)) ? (int)$idRaw : null;

    // 2) lista completa
    $listaCompleta = $_SESSION['lista_completa'] ?? [];
    $log('listaCompleta (raw)', $listaCompleta);
    $tipoSessao = '';

    // Se a sessão tiver um índice "tipo", não é item; remove pra não atrapalhar
    if (isset($listaCompleta['tipo'])) {
        $tipoSessao = $listaCompleta['tipo'];
        unset($listaCompleta['tipo']);
        $log('tipoSessao', $tipoSessao);
    }

    // 3) agrupados (quantidade por prioridade)
    $agrupados = [];
    $procId = $listaCompleta[0]['processos_id'] ?? null;

    if ($procId !== null) {
        $ordemModel = new \App\Models\Ordem();
        $builderOrdens = $ordemModel
            ->select('prioridade_id, COUNT(*) AS quantidade')
            ->where('processos_id', $procId)
            ->where('status', 'ativo');

        if ($tipoSessao === 'ind') {
            $builderOrdens
                ->where('desenho_id IS NULL', null, false)
                ->where('projeto_id IS NOT NULL', null, false);
        } else {
            $builderOrdens->where('desenho_id IS NOT NULL', null, false);
        }

        $rows = $builderOrdens
            ->groupBy('prioridade_id')
            ->findAll();

        foreach ($rows as $r) {
            $agrupados[(int)$r['prioridade_id']] = (int)$r['quantidade'];
        }
    } else {
        // fallback
        $agrupados = $_SESSION['ordem_max'] ?? [];
    }

    $log('agrupados', $agrupados);

    // 4) limpa campos que não vão ao front (MAS guarda chaves internas para ordenar)
    $chavesParaRemover = [
        'empreendimentos_id',
        'empresa_id',
        'finalidade_id',
        'prioridade_id',
        'processos_id',
        'projeto_id',
        'usuario_id_desenhista',
        'corte_id'
    ];

    $listaFiltrada = [];
    foreach ($listaCompleta as $item) {
        if (!is_array($item)) continue;

        // Guarda valores internos para ordenação (não vaza pro front)
        $prioId = (int)($item['prioridade_id'] ?? $item['prioridade'] ?? 0);
        $ordem  = (int)($item['ordem'] ?? $item['ordem_valor'] ?? 0);

        $item['_prio_id'] = $prioId;
        $item['_ordem_sort'] = $ordem;

        if ($tipoSessao === 'ind' || ($item['item_tipo'] ?? '') === 'projeto') {
            $descricaoProjeto = trim((string) ($item['projeto_descricao'] ?? $item['descricao'] ?? $item['nome'] ?? ''));
            if ($descricaoProjeto !== '') {
                $item['nome'] = $descricaoProjeto;
            }
            $item['item_tipo'] = 'projeto';
        }

        // Remove campos sensíveis
        foreach ($chavesParaRemover as $chave) {
            unset($item[$chave]);
        }

        $listaFiltrada[] = $item;
    }

    $log('listaFiltrada (apos remover)', $listaFiltrada);

    // 5) ordena: prioridade asc, ordem asc
    usort($listaFiltrada, function ($a, $b) {
        $pa = (int)($a['_prio_id'] ?? 0);
        $pb = (int)($b['_prio_id'] ?? 0);
        if ($pa !== $pb) return $pa <=> $pb;

        $oa = (int)($a['_ordem_sort'] ?? 0);
        $ob = (int)($b['_ordem_sort'] ?? 0);
        if ($oa !== $ob) return $oa <=> $ob;

        // desempate estável
        $ida = (int)($a['id'] ?? 0);
        $idb = (int)($b['id'] ?? 0);
        return $ida <=> $idb;
    });

    $log('listaFiltrada (ordenada)', $listaFiltrada);

    // 6) renumera por prioridade: 1,2,3...
    $contPorPrio = [];
    foreach ($listaFiltrada as &$it) {
        $p = (int)($it['_prio_id'] ?? 0);
        if (!isset($contPorPrio[$p])) $contPorPrio[$p] = 0;

        $contPorPrio[$p] += 1;
        $it['ordem'] = $contPorPrio[$p]; // <<< começa em 1

        // remove helpers pra não vazar
        unset($it['_prio_id'], $it['_ordem_sort']);
    }
    unset($it);

    $log('listaFiltrada (renumerada)', $listaFiltrada);

    // 7) Se veio $id, tenta achar por campo id do item (mais correto)
    $itemUnico = null;
    if ($id !== null) {
        foreach ($listaFiltrada as $it) {
            if (isset($it['id']) && (int)$it['id'] === $id) {
                $itemUnico = $it;
                break;
            }
        }

        // fallback: se o front manda índice mesmo
        if ($itemUnico === null && isset($listaFiltrada[$id])) {
            $itemUnico = $listaFiltrada[$id];
        }

        $log('busca item unico', ['id_recebido' => $id, 'encontrado' => $itemUnico]);
    }

    // 8) monta response
    $response = [
        'lista' => ($itemUnico !== null) ? [$itemUnico] : array_values($listaFiltrada),
        'agrupados' => $agrupados,
        'tipo' => $tipoSessao,
    ];

    if ($DEBUG) {
        $response['debug'] = $dbg;
    }

    return $this->response->setJSON($response);
}



  /**
   * Função nome_desenho()
   *
   * Esta função recebe, via POST, um ID temporário e utiliza esse ID para retornar o nome completo de um desenho
   * que está em uma lista. 
   *
   * A função retorna um JSON contendo a lista completa de usuários que está armazenada na sessão.
   */
  function nome_desenho()
  {
    if ($this->request->isAJAX()) {
      session_start();

      $id = service('request')->getPost('id'); //id recebido via post

      $lista = $_SESSION["lista_completa"][str_replace('prio_', '', $id)]; //busca qual desenhos é da lsita usando o id e retornando as informações desse desenho

      $data = ['ok' => true, 'nome' => Ferramentas::decodificador($lista['nome'])]; // envia ok true indicando que deu certo e retrirar apenas o nome das informações do desenho
      return $this->response->setJSON($data);
    }
  }


  /**
   * Move-o um desenho especificado para o diretório de lixo.
   *
   * Esta função é responsável por apagar um desenho especificado ou movê-lo para o diretório de lixo.
   *
   * @return 
   */
  function apagar_desenho()
  {
    if ($this->request->isAJAX()) {
      session_start();
      $id_temp = service('request')->getPost('id');
      $id_temp = str_replace('prio_', '', $id_temp);

      if ($this->sessaoListaEhProjetoInd()) {
        $itensProjeto = $_SESSION["projeto_todos"][$id_temp] ?? [];
        if (!is_array($itensProjeto) || $itensProjeto === []) {
          return $this->response->setJSON(['ok' => 'false', 'mensagem' => 'Projeto nao encontrado na lista.']);
        }

        $desenhosModel = new \App\Models\Desenhos();
        $projetoId = (int) ($itensProjeto[0]['projeto_id'] ?? 0);
        $apagados = 0;
        $falhas = [];

        foreach ($itensProjeto as $itemProjeto) {
          $desenhoId = (int) ($itemProjeto['desenho_id'] ?? 0);
          if ($desenhoId <= 0) {
            continue;
          }

          $desenho = $desenhosModel->where('id', $desenhoId)->first();
          if (!is_array($desenho)) {
            continue;
          }

          $resultado = $this->apagarDesenhoDaLista($desenhoId, $desenho, (int) ($_SESSION['usuario'] ?? 0));
          if (($resultado['ok'] ?? 'false') === 'true') {
            $apagados++;
          } else {
            $falhas[] = (string) ($resultado['mensagem'] ?? 'Falha ao apagar desenho ' . $desenhoId);
          }
        }

        if ($projetoId > 0) {
          (new \App\Models\Projeto())->update($projetoId, ['status' => 'apagado']);
          (new \App\Models\Ordem())
            ->where('projeto_id', $projetoId)
            ->where('desenho_id IS NULL', null, false)
            ->where('status', 'ativo')
            ->set('status', 'desativado')
            ->update();
        }

        return $this->response->setJSON([
          'ok' => $apagados > 0 ? 'true' : 'false',
          'mensagem' => $apagados . ' arquivo(s) do projeto apagado(s).',
          'falhas' => $falhas,
          'projeto_id' => $projetoId,
        ]);
      }
      // Obtém informações sobre o desenho a partir da sessão.
      $lista = $_SESSION["lista_completa"][$id_temp];
      $id = $_SESSION["lista"][$id_temp];
      $caminho = Ferramentas::wlStoragePath((string) ($lista['diretorio'] ?? ''));


      $caminho_antigo = $caminho;
      // Verifica se o desenho não existe mais.
      if (!file_exists($caminho)) {
        // Atualiza o status do desenho para 'apagado' no banco de dados.
        $db = new \App\Models\Desenhos();
        $db->update($id, ['status' => 'apagado']);

        return $this->response->setJSON(['ok' => 'false', 'mensagem' => 'Desenho apagado com sucesso ', 'mensagem_false' => 'Desenho não existe', '1' => $caminho]);
      }

      // Define o novo caminho do desenho para o diretório de lixo.
      $relativo = Ferramentas::wlStorageRelativePath($caminho);
      $diretorioRelativo = str_replace('\\', '/', dirname($relativo));
      $caminho = Ferramentas::wlStoragePath(
        'lixo/' . ($diretorioRelativo === '.' ? '' : $diretorioRelativo)
      );
      // Normaliza o caminho removendo espaços extras e corrigindo barras
      $caminho = str_replace('\\', '/', trim($caminho));

      // Verifica se há múltiplas ocorrências de "C:/"
      if (preg_match_all('/[a-zA-Z]:\//', $caminho, $matches, PREG_OFFSET_CAPTURE)) {
        if (count($matches[0]) > 1) {
          // Pega a segunda ocorrência da raiz e corta o restante anterior
          $inicioSegundaRaiz = $matches[0][1][1]; // Índice da segunda raiz
          $caminho = substr($caminho, $inicioSegundaRaiz);
        }
      }
      $caminho = Ferramentas::normalizePath($caminho);
      // Cria o diretório de lixo, se não existir.
      $problema = Ferramentas::criet_diretorio($caminho);

      if (count($problema) == 0) {

        // Garante que $caminho termina com o separador de diretório
        $baseDir = rtrim($caminho, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        // Extrai nome-base e extensão do arquivo original
        $originalName = basename($caminho_antigo);
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);

        // Gera um nome único, evitando conflito com arquivos existentes em $baseDir
        do {
          // Sufixo com data/hora + número aleatório
          $suffix = date('Ymd_His') . '_' . mt_rand(0, 999);
          $newName = "{$baseName}_{$suffix}.{$extension}";
          $fullPath = $baseDir . $newName;
        } while (file_exists($fullPath));

        // Move o desenho para o diretório de lixo.
        if (rename(Ferramentas::normalizePath($caminho_antigo), Ferramentas::normalizePath($baseDir . $newName))) {
          // Registra informações sobre o desenho no banco de dados de lixo.
          $data = [
            'desenho_id' => $id,
            'usuario_id' => $_SESSION['usuario'],
            'diretorio' => $caminho,
            'nome' => $newName
          ];
          $db = new \App\Models\Lixo_desenhos();
          $db->insert($data);

          // Atualiza o status do desenho para 'apagado' no banco de dados.
          $db = new \App\Models\Desenhos();
          $db->update($id, ['status' => 'apagado']);

          (new \App\Models\Alteracoes())->insertWithDetails(
            [
              'usuario_id' => $_SESSION['usuario'],
              'id_item' => $id,
              'item' => 'desenho',
              'info_mais' => 'desenho.apagar_desenho',
              '_meta' => [
                'origem' => 'desenho_post',
                'novo_caminho_lixeira' => $caminho . $newName,
                'desenho_nome' => Ferramentas::remove_id_file($this->decodificarValor($lista['nome'] ?? '')),
              ],
            ],
            [
              [
                'campo' => 'status',
                'valor_antes' => $this->decodificarValor($lista['status'] ?? ''),
                'valor_depois' => 'apagado',
              ],
              [
                'campo' => 'arquivo_origem',
                'valor_antes' => $caminho_antigo,
                'valor_depois' => $caminho . $newName,
              ],
            ]
          );





      




          return $this->response->setJSON(['ok' => 'true', 'mensagem' => 'Desenho apagado com sucesso ', 'dir_antigo' => $caminho_antigo, 'dir_novo' => $caminho . $newName]);
        } else {
          return $this->response->setJSON(['ok' => 'false', 'mensagem' => 'Desenho apagado com sucesso ', 'mensagem_false' => 'Trasferencia para lixeira']);
        }
      }
      return $this->response->setJSON(['ok' => 'false', 'caminho' => $problema]);
    }
  }






  /**
   * Define um novo nome para um desenho que será substituído.
   *
   * Esta função é responsável por definir um novo nome para um desenho que será substituído e armazenar esse nome na sessão para uso posterior.
   *
   * @return 
   */
  function desenho_novo_nome()
  {
    if ($this->request->isAJAX()) {
      session_start();

      // Obtém o novo nome do desenho a ser definido.
      $nome_novo = Ferramentas::norma_lizar_str(service('request')->getPost('nome'));

      // Armazena o novo nome na sessão para uso posterior.
      $_SESSION["novo_nome_arquivo"] = $nome_novo;
      return $this->response->setJSON(['mensagem' => $nome_novo]);
    }
  }








  /**
   * Recoloca um desenho duplicado no banco de dados.
   *
   * Esta função lida com a recolocação de um desenho duplicado no banco de dados. Ela cria uma cópia do desenho original com um novo nome e insere os dados duplicados no banco de dados.
   *
   * @return 
   */
  function recolocar_desenho()
  {
    if ($this->request->isAJAX()) {

      $id = service('request')->getPost('id');

      $caminho = Ferramentas::re_colcoar_desenho($id);


      $data = ['ok' => true, 'nome' => $caminho];
      return $this->response->setJSON($data);
    }
  }
}
