<?php

namespace App\Controllers;

use App\Controllers\Ferramentas;
use App\Libraries\NivelTelaInicial;


class NivelPost extends Ferramentas
{
  //as paginas que possui no sistema e colocar no Login.php tambem
  private static $array_niveis = array(
    'Adicionar',
    'Meus Desenhos',
    'Lista De Corte',
    'Lista De Corte ADM',
    'Subpasta',
    'Desenhos cortados',
    'Tipo De Arquivo',
    'Prioridade',
    'Fialidade',
    'Empresa',
    'Empreendimento',
    'Nível',
    'Usuario',
    'Relátorio',
    'Logs',
    'Lista De Corte Cortador',
    'Processos'
  );

  private function opcoesTelaInicialHtml(string $selecionada = '', array $permissoes = []): string
  {
    $html = '';
    foreach (NivelTelaInicial::opcoes($permissoes) as $valor => $label) {
      $valor = (string) $valor;
      $selected = $valor === $selecionada ? ' selected' : '';
      $html .= '<option value="' . htmlspecialchars($valor, ENT_QUOTES, 'UTF-8') . '"' . $selected . '>' . htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8') . '</option>';
    }

    return $html;
  }

  private function rotuloTelaInicial(string $telaInicial): string
  {
    return NivelTelaInicial::rotulo($telaInicial);
  }

  private function definicoesTelaInicialHtmlAttr(): string
  {
    return htmlspecialchars(
      (string) json_encode(NivelTelaInicial::definicoes(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
      ENT_QUOTES,
      'UTF-8'
    );
  }

  private function validarTelaInicial(?string $telaInicial, string $permissao, array &$msg, array &$violacao): string
  {
    $telaInicial = trim((string) $telaInicial);
    if ($telaInicial === '') {
      return '';
    }

    $permissoesSelecionadas = $permissao === 'all'
      ? ['all']
      : array_values(array_unique(array_filter(explode('-', $permissao), 'strlen')));

    if (!NivelTelaInicial::permitida($telaInicial, $permissoesSelecionadas)) {
      $msg['Tela Inicial'] = 'Tela inicial nao e compativel com as permissoes selecionadas.';
      $violacao[] = 'nivel tela inicial incompativel com as permissoes';
      return '';
    }

    return $telaInicial;
  }

  private function opcoesNivelAdicionalHtml(int $selecionadoId = 0, int $nivelAtualId = 0): string
  {
    $dbNivel = new \App\Models\Nivel();
    $niveis = $dbNivel->find();
    $html = '<option value="">Nenhum nivel adicional</option>';
    $selecionadoEncontrado = $selecionadoId <= 0;

    foreach ($niveis as $nivel) {
      $id = (int) ($nivel['id'] ?? 0);
      if ($id <= 0 || $id === $nivelAtualId) {
        continue;
      }

      $status = (string) ($nivel['status'] ?? '');
      if ($status !== 'ativo' && $id !== $selecionadoId) {
        continue;
      }

      $nome = Ferramentas::decodificador((string) ($nivel['nome'] ?? ''));
      if ($nome === '') {
        $nome = (string) ($nivel['nome'] ?? '');
      }

      $selected = $id === $selecionadoId ? ' selected' : '';
      if ($selected !== '') {
        $selecionadoEncontrado = true;
      }

      $sufixo = $status !== 'ativo' ? ' (inativo)' : '';
      $html .= '<option value="' . $id . '"' . $selected . '>' . htmlspecialchars($nome . $sufixo, ENT_QUOTES, 'UTF-8') . '</option>';
    }

    if (!$selecionadoEncontrado && $selecionadoId > 0) {
      $nivelSelecionado = $dbNivel->find($selecionadoId);
      if (is_array($nivelSelecionado) && !empty($nivelSelecionado)) {
        $nome = Ferramentas::decodificador((string) ($nivelSelecionado['nome'] ?? ''));
        if ($nome === '') {
          $nome = (string) ($nivelSelecionado['nome'] ?? '');
        }

        $html .= '<option value="' . $selecionadoId . '" selected>' . htmlspecialchars($nome . ' (inativo)', ENT_QUOTES, 'UTF-8') . '</option>';
      }
    }

    return $html;
  }

  private function nomeNivelAdicionalTexto(int $nivelAdicionalId, array $mapaNiveis = []): string
  {
    if ($nivelAdicionalId <= 0) {
      return 'Nenhum';
    }

    $nivel = $mapaNiveis[$nivelAdicionalId] ?? null;
    if (!is_array($nivel)) {
      $nivel = (new \App\Models\Nivel())->find($nivelAdicionalId);
    }

    if (!is_array($nivel) || empty($nivel)) {
      return 'Nivel nao encontrado';
    }

    $nome = Ferramentas::decodificador((string) ($nivel['nome'] ?? ''));
    if ($nome === '') {
      $nome = (string) ($nivel['nome'] ?? '');
    }

    if ((string) ($nivel['status'] ?? '') !== 'ativo') {
      $nome .= ' (inativo)';
    }

    return $nome;
  }

  private function validarNivelAdicional($nivelAdicionalId, int $nivelAtualId, array &$msg, array &$violacao): int
  {
    $nivelAdicionalId = (int) ($nivelAdicionalId ?? 0);
    if ($nivelAdicionalId <= 0) {
      return 0;
    }

    $dbNivel = new \App\Models\Nivel();
    if (!$dbNivel->supportsColumn('nivel_adicional_id')) {
      $msg['Nivel adicional'] = 'O banco ainda nao possui o campo de nivel adicional. Execute a migracao antes de salvar.';
      $violacao[] = 'nivel nivel_adicional_id ausente no banco';
      return 0;
    }

    if ($nivelAtualId > 0 && $nivelAdicionalId === $nivelAtualId) {
      $msg['Nivel adicional'] = 'O nivel adicional nao pode ser o proprio nivel.';
      $violacao[] = 'nivel adicional igual ao proprio nivel';
      return 0;
    }

    $nivelAdicional = $dbNivel->find($nivelAdicionalId);
    if (!is_array($nivelAdicional) || empty($nivelAdicional) || (string) ($nivelAdicional['status'] ?? '') !== 'ativo') {
      $msg['Nivel adicional'] = 'Selecione um nivel adicional ativo e existente.';
      $violacao[] = 'nivel adicional inexistente ou inativo';
      return 0;
    }

    $visitados = [];
    $cursorId = $nivelAdicionalId;
    $guard = 0;

    while ($cursorId > 0 && $guard < 10) {
      if (isset($visitados[$cursorId])) {
        $msg['Nivel adicional'] = 'Foi encontrada uma referencia circular entre niveis.';
        $violacao[] = 'nivel adicional com referencia circular';
        return 0;
      }

      if ($nivelAtualId > 0 && $cursorId === $nivelAtualId) {
        $msg['Nivel adicional'] = 'Esse nivel adicional criaria uma referencia circular.';
        $violacao[] = 'nivel adicional gera ciclo';
        return 0;
      }

      $visitados[$cursorId] = true;
      $nivelCursor = $dbNivel->find($cursorId);
      if (!is_array($nivelCursor) || empty($nivelCursor)) {
        break;
      }

      $cursorId = (int) ($nivelCursor['nivel_adicional_id'] ?? 0);
      $guard++;
    }

    return $nivelAdicionalId;
  }

  /**
   * Gera uma lista de níveis ativos ou desativados e retorna os dados formatados via AJAX.
   *
   * @return \CodeIgniter\HTTP\Response Retorna uma resposta JSON contendo a lista formatada de níveis.
   */
  function nivel_lista()
  {
    if ($this->request->isAJAX()) {
      $ativo = service('request')->getPost('ativos');
      $desativado = service('request')->getPost('desativados');


      session_start();

      $id_temp = -1;

      $db_nivel = new \App\Models\Nivel();

      $nivel_data = $db_nivel->find();
      $mapaNiveis = [];
      foreach ($nivel_data as $nivelRow) {
        $mapaNiveis[(int) ($nivelRow['id'] ?? 0)] = $nivelRow;
      }

      $lista = "";
      $lista_array = array();
      foreach ($nivel_data as $key => $value) {
        if ((($value['status']) == "ativo" and $ativo == "true") or (($value['status']) == "desativado" and $desativado == "true")) {
          $id_temp += 1;

          $permissoesAtivas = $db_nivel->listarPermissoesAtivas((int) $value['id']);
          $permissoesArray = array_values(array_filter(array_map(
            static fn($permissao) => str_replace('_', ' ', (string) $permissao),
            $permissoesAtivas
          ), 'strlen'));
          $permissoes = implode('-', $permissoesAtivas);
          $permissoesLista = implode('<br>', array_map(
            static fn($grupo) => implode(' | ', $grupo),
            array_chunk($permissoesArray, 5)
          ));
          $processosIds = array();
          $processosNomes = array();
          foreach ($db_nivel->listarProcessosDetalhados((int) $value['id']) as $rowProcesso) {
            $processoId = (string) ($rowProcesso['processo_id'] ?? '');
            if ($processoId === '') {
              $processoId = (string) ($rowProcesso['id'] ?? '');
            }
            if ($processoId === '') {
              continue;
            }
            if (in_array($processoId, $processosIds, true)) {
              continue;
            }
            $processosIds[] = $processoId;
            $processosNomes[] = (string) ($rowProcesso['nome'] ?? '');
          }
          $processos = implode('-', $processosIds);
          $processosLista = implode('<br>', array_map(
            static fn($grupo) => implode(' | ', $grupo),
            array_chunk($processosNomes, 5)
          ));
          $relatorioTexto = (!empty($value['relatorio']) && (string) $value['relatorio'] !== '0') ? 'Sim' : 'Nao';
          $telaInicial = (string) ($value['tela_inicial'] ?? '');
          $telaInicialTexto = htmlspecialchars($this->rotuloTelaInicial($telaInicial), ENT_QUOTES, 'UTF-8');
          $nivelAdicionalId = (int) ($value['nivel_adicional_id'] ?? 0);
          $nivelAdicionalTexto = htmlspecialchars($this->nomeNivelAdicionalTexto($nivelAdicionalId, $mapaNiveis), ENT_QUOTES, 'UTF-8');
          $lista .= '<tr>
              <td ondblclick="modal_nivel(' . $id_temp . ')">' . Ferramentas::decodificador($value['nome']) . '</td>
              <td ondblclick="modal_nivel(' . $id_temp . ')">' . $permissoesLista . '</td>
              <td ondblclick="modal_nivel(' . $id_temp . ')">' . $processosLista . '</td>
              <td ondblclick="modal_nivel(' . $id_temp . ')">' . $telaInicialTexto . '</td>
              <td ondblclick="modal_nivel(' . $id_temp . ')">' . $nivelAdicionalTexto . '</td>
              <td ondblclick="modal_nivel(' . $id_temp . ')">' . $relatorioTexto . '</td>
              <td ondblclick="modal_nivel(' . $id_temp . ')">' . Ferramentas::decodificador($value['status']) . '</td>
  
              ';
          if (Ferramentas::decodificador($value['status']) == "ativo") {
            $lista .= "<td><button name='cadastrarar' type='submit' onclick='desativar(" . $id_temp . ")' class='btn btn-outline-danger btn-lg btn-block'> Desativar </button></td>";
          } else {
            $lista .= "<td><button name='cadastrarar' type='submit' onclick='ativar(" . $id_temp . ")' class='btn btn-outline-success btn-lg btn-block'> Ativar </button></td>";
          }

          $lista .= '<td><button name="cadastarar" type="submit" class="btn btn-outline-warning btn-lg btn-block" onclick="modal_nivel(\'' . $id_temp . '\')"> Modificar </button></td></tr>';
          $lista_array[$id_temp] = [
            'processos' => $processos,
            'permissao' => $permissoes,
            'relatorio' => (string) ($value['relatorio'] ?? '0'),
            'tela_inicial' => $telaInicial,
            'nivel_adicional_id' => (string) $nivelAdicionalId,
            'nome' => Ferramentas::decodificador($value['nome']),
            'status' => Ferramentas::decodificador($value['status']),
            'id' => $value['id']
          ];
        }
      }
      $_SESSION["lista"] = $lista_array;

      $data = ['lista' => $lista];
      return $this->response->setJSON($data);
    }
  }
  /**
   * Desativa um nível específico no banco de dados.
   *
   * @return \CodeIgniter\HTTP\Response Retorna uma resposta JSON com o status da operação.
   */
  function nivel_lista_desativar()
  {
    if ($this->request->isAJAX()) {
      session_start();
      $id = service('request')->getPost('id');
      $lista = $_SESSION["lista"];


      $db = new \App\Models\Nivel();

      $value['status'] = "desativado";

      $db->update($lista[$id]['id'], $value);

      $data = ['lista' => 'true'];
      return $this->response->setJSON($data);
    }
  }
  /**
   * Ativa um nível específico no banco de dados.
   *
   * @return \CodeIgniter\HTTP\Response Retorna uma resposta JSON com o status da operação.
   */
  function nivel_lista_ativar()
  {
    if ($this->request->isAJAX()) {
      session_start();
      $id = service('request')->getPost('id');
      $lista = $_SESSION["lista"];


      $db = new \App\Models\Nivel();

      $value['status'] = "ativo";

      $db->update($lista[$id]['id'], $value);

      $data = ['lista' => 'true'];
      return $this->response->setJSON($data);
    }
  }
  /**
   * Gera o modal para modificar as informações de um nível específico, retornando as opções de permissões e processos.
   *
   * @return \CodeIgniter\HTTP\Response Retorna uma resposta JSON contendo o modal gerado e os dados do nível.
   */
  function nivel_modifica_modal()
  {
    if ($this->request->isAJAX()) {
      $id = service('request')->getPost('id');
      session_start();

      $processos_db = new \App\Models\Processos();
      $processos_data = $processos_db->find();

      $array_processos = array();
      foreach ($processos_data as $value) {
        if (Ferramentas::array_index($value, ["status"]) == 'ativo') {
          $array_processos[] = [
            'id' => (string) Ferramentas::array_index($value, ["id"]),
            'nome' => Ferramentas::decodificador(Ferramentas::array_index($value, ["nome"]))
          ];
        }
      }

      $permissoesSelecionadas = array();
      $processosSelecionados = array();
      $dbNivel = new \App\Models\Nivel();
      if ($id !== null && $id !== "" && isset($_SESSION["lista"][$id])) {
        $lista = $_SESSION["lista"][$id];
        $nivelId = (int) $lista['id'];
        $_SESSION["modal_id"] = $nivelId;

        $nivelRow = $dbNivel->find($nivelId);
        if (is_array($nivelRow) && isset($nivelRow['relatorio'])) {
          $lista['relatorio'] = (string) $nivelRow['relatorio'];
        }
        if (is_array($nivelRow) && isset($nivelRow['tela_inicial'])) {
          $lista['tela_inicial'] = (string) $nivelRow['tela_inicial'];
        }
        if (is_array($nivelRow) && array_key_exists('nivel_adicional_id', $nivelRow)) {
          $lista['nivel_adicional_id'] = (string) ((int) ($nivelRow['nivel_adicional_id'] ?? 0));
        }

        $permissoesSelecionadas = $dbNivel->listarPermissoesAtivas($nivelId);
        $lista["permissao"] = implode('-', $permissoesSelecionadas);

        $processosSelecionados = $dbNivel->listarProcessosIds($nivelId);
        $lista["processos"] = implode('-', $processosSelecionados);
      } else {
        unset($_SESSION["modal_id"]);
        $lista = [
          "permissao" => "",
          "processos" => "",
          "nome" => "",
          "relatorio" => "0",
          "tela_inicial" => "",
          "nivel_adicional_id" => "0",
        ];
      }

      $enable = "";
      $check = "";
      if ($lista["permissao"] == "all") {
        $enable = " disabled";
        $check = " checked";
      }

      $checkboxPermissoes = "";
      if (empty($permissoesSelecionadas)) {
        $permissoesSelecionadas = array_filter(explode('-', (string) $lista["permissao"]), 'strlen');
      }
      foreach (self::$array_niveis as $item) {
        $permissaoValor = str_replace(' ', '_', $item);
        $checked = in_array($permissaoValor, $permissoesSelecionadas, true) ? " checked" : "";
        $checkboxPermissoes .= '<label class="form-check form-check-inline me-3 mb-2" style="font-weight: normal;">'
          . '<input type="checkbox" class="form-check-input nivel-checkbox" value="' . $permissaoValor . '"' . $enable . $checked . '>'
          . '<span class="form-check-label">' . $item . '</span>'
          . '</label>';
      }

      $checkboxProcessos = "";
      if (empty($processosSelecionados)) {
        $processosSelecionados = array_filter(explode('-', (string) $lista["processos"]), 'strlen');
      }
      $checkProcessos = "";
      if (!empty($array_processos)) {
        $totalProcessos = count($array_processos);
        $totalSelecionados = count($processosSelecionados);
        if ($totalSelecionados > 0 && $totalSelecionados === $totalProcessos) {
          $checkProcessos = " checked";
        }
      }
      foreach ($array_processos as $item) {
        $processoId = (string) ($item['id'] ?? '');
        $processoNome = (string) ($item['nome'] ?? '');
        $checked = in_array($processoId, $processosSelecionados, true) ? " checked" : "";
        $checkboxProcessos .= '<label class="form-check form-check-inline me-3 mb-2" style="font-weight: normal;">'
          . '<input type="checkbox" class="form-check-input processo-checkbox" value="' . $processoId . '"' . $checked . '>'
          . '<span class="form-check-label">' . $processoNome . '</span>'
          . '</label>';
      }
      if ($checkProcessos !== "") {
        $checkboxProcessos = str_replace('class="form-check-input processo-checkbox"', 'class="form-check-input processo-checkbox" disabled', $checkboxProcessos);
      }

      $checkRelatorio = (!empty($lista["relatorio"]) && (string) $lista["relatorio"] !== '0') ? " checked" : "";
      $telaInicialOptions = $this->opcoesTelaInicialHtml((string) ($lista["tela_inicial"] ?? ''), $permissoesSelecionadas);
      $telaInicialDefinicoes = $this->definicoesTelaInicialHtmlAttr();
      $nivelAdicionalOptions = $this->opcoesNivelAdicionalHtml((int) ($lista["nivel_adicional_id"] ?? 0), (int) ($_SESSION["modal_id"] ?? 0));
      $conteudo = '<div class="nivel-form-scope">'
        . '<div class="form-group mb-3">'
        . '<label>Nome</label>'
        . '<input type="text" class="form-control" id="nivel_novo" placeholder="Novo Nivel" value="' . $lista["nome"] . '">'
        . '</div>'
        . '<div class="form-group mb-3">'
        . '<label>Tela inicial</label>'
        . '<select class="form-select" id="tela_inicial" data-tela-inicial-definicoes="' . $telaInicialDefinicoes . '">' . $telaInicialOptions . '</select>'
        . '<small class="text-muted d-block mt-1">Somente telas permitidas neste nivel podem ser escolhidas como inicial.</small>'
        . '</div>'
        . '<div class="form-group mb-3">'
        . '<label>Nivel adicional</label>'
        . '<select class="form-select" id="nivel_adicional_id">' . $nivelAdicionalOptions . '</select>'
        . '<small class="text-muted d-block mt-1">As telas herdadas desse nivel usam os processos atribuidos a ele.</small>'
        . '</div>'
        . '<div class="form-group mb-3">'
        . '<label class="form-check mb-0" style="font-weight: normal;">'
        . '<input type="checkbox" class="form-check-input" id="checkbox_relatorio"' . $checkRelatorio . '>'
        . '<span class="form-check-label">&nbsp;Aparecer nos relatorios.</span>'
        . '</label>'
        . '</div>'
        . '<div class="form-group mb-3">'
        . '<label>Permissoes</label><br/>'
        . '<label class="form-check mb-2" style="font-weight: normal;">'
        . '<input type="checkbox" class="form-check-input" id="checkbox_todos" onclick="marcar_todos_nivel(this)"' . $check . '>'
        . '<span class="form-check-label">&nbsp;Selecionar todos.</span>'
        . '</label>'
        . '<br/>'
        . $checkboxPermissoes
        . '</div>'
        . '<div class="form-group mb-0">'
        . '<label>Processos</label><br/>'
        . '<label class="form-check mb-2" style="font-weight: normal;">'
        . '<input type="checkbox" class="form-check-input" id="checkbox_todos_processos" onclick="marcar_todos_processos(this)"' . $checkProcessos . '>'
        . '<span class="form-check-label">&nbsp;Selecionar todos.</span>'
        . '</label>'
        . '<br/>'
        . $checkboxProcessos
        . '</div>'
        . '</div>';

      $titulo = ($id !== null && $id !== "") ? 'Modificar Nivel: ' . $lista['nome'] : 'Cadastro de Nivel';
      $data = [
        'modal' => '',
        'titulo' => $titulo,
        'conteudo' => $conteudo,
        '1' => $array_processos,
        '2' => $lista["processos"],
        '3' => explode('-', $lista["processos"])
      ];
      return $this->response->setJSON($data);
    }
  }
  function nivel_modificar()
  {
    if ($this->request->isAJAX()) {


      $msg = array();
      $ok = false;
      $violacao = array();
      $nivel = service('request')->getPost('nivel');
      $permissao = service('request')->getPost('permissao');
      $relatorio = service('request')->getPost('relatorio');
      $processos = service('request')->getPost('processos');
      $telaInicial = service('request')->getPost('tela_inicial');
      $nivelAdicionalIdPost = service('request')->getPost('nivel_adicional_id');
      $processos_db = new \App\Models\Processos(); // Obtém a tabela de prioridades do banco

      $processos_data = $processos_db->find();

      if (strlen($nivel) > 30) {
        $msg['Nível'] = "Nome do nível excedeu o tamanho máximo de 30 caracter";
        $violacao[] = "nivel_modificar nivel excedeu o tamanho máximo";
      }

      if (strlen($nivel) < 2) {
        $msg['Nível'] = "Nome do nível não possui o tamanho mínimo de 2 caracter";
      } else {
        if (Ferramentas::codificador($nivel) == '') {
          $msg['Nível'] = "Nome do nível possui caracteres não permitidos";
          $violacao[] = "nivelnivel_modificarcadastrar nivel possui caracteres não permitidos";
        }
      }

      if (strlen($permissao) < 2) {
        $msg['Permissao'] = "Nenhuma Permissão escolhida.";
      } else if ($permissao != "all") {

        // Verificar se todos os valores existem no array global
        foreach (explode('-', str_replace('_', ' ', $permissao)) as $valor) {
          if (!in_array($valor, self::$array_niveis)) {
            $msg['Permissao'] = "Permissão não encontrada.";
            $violacao[] = "nivel_modificar permissão não encontrada.";
          }
        }
      }

      $array_prcoessos = array();
      foreach ($processos_data as $value) {
        if (Ferramentas::array_index($value, ["status"]) == 'ativo') {
          $processoId = (string) Ferramentas::array_index($value, ["id"]);
          if ($processoId !== '') {
            $array_prcoessos[] = $processoId;
          }
        }
      }

      if ($processos === "all") {
        $processos = implode("-", $array_prcoessos);
      } elseif ($processos !== null && $processos !== "") {
        foreach (array_filter(explode('-', $processos), 'strlen') as $valor) {
          if (!in_array((string) $valor, $array_prcoessos, true)) {
            $msg["Processos"] = "Processo não encontrada.";
            $violacao[] = "nivel_cadastrar processo não encontrada.";
          }
        }
      } else {
        $processos = "";
      }

      $telaInicial = $this->validarTelaInicial($telaInicial, (string) $permissao, $msg, $violacao);

      session_start();
      $id = (int) ($_SESSION["modal_id"] ?? 0);
      $nivelAdicionalId = $this->validarNivelAdicional($nivelAdicionalIdPost, $id, $msg, $violacao);
      if (count($msg) == 0 and count($violacao) == 0) {
        $processos_salva = array_values(array_unique(array_filter(explode('-', (string) $processos), 'strlen')));
        $relatorioFlag = ($relatorio === true || $relatorio === "true" || $relatorio === 1 || $relatorio === "1") ? 1 : 0;
        $db = new \App\Models\Nivel();

        $nivel_data = $db->find();
        $nome = Ferramentas::array_pesquisa($nivel_data, 'id', $id);

        $permissoesAtuais = $db->listarPermissoesAtivas($id);
        $processosAtuais = $db->listarProcessosIds($id);

        $permissoesNovas = array_values(array_unique(array_filter(
          $permissao === "all"
            ? ['all']
            : explode('-', (string) $permissao),
          'strlen'
        )));

        sort($permissoesAtuais);
        sort($processosAtuais);
        sort($permissoesNovas);
        sort($processos_salva);

        $nomeAtual = (string) Ferramentas::array_index($nome, ['nome']);
        $relatorioAtual = (string) Ferramentas::array_index($nome, ['relatorio']);
        $telaInicialAtual = (string) Ferramentas::array_index($nome, ['tela_inicial']);
        $nivelAdicionalAtual = (int) Ferramentas::array_index($nome, ['nivel_adicional_id']);
        $nomeNovoCodificado = Ferramentas::codificador($nivel);

        if (
          $nomeAtual === $nomeNovoCodificado &&
          $relatorioAtual === (string) $relatorioFlag &&
          $telaInicialAtual === $telaInicial &&
          $nivelAdicionalAtual === $nivelAdicionalId &&
          $permissoesAtuais === $permissoesNovas &&
          $processosAtuais === $processos_salva
        ) {
          $msg["Modificar"] = "Nenhum item foi modificado.";
        }

        $nivelDuplicado = $db->where('id !=', $id)->where('nome', Ferramentas::codificador($nivel))->first();
        if (count($msg) == 0 && !$nivelDuplicado) {
          $alteracao = new \App\Models\Alteracoes();

          $alteracao->insertWithDetails(
            [
              "usuario_id" => $_SESSION["usuario"],
              "id_item" => $id,
              "item" => "nivel",

            ],
            [
              [
                "valor_antes" => Ferramentas::array_index(Ferramentas::array_pesquisa($nivel_data, 'id', $id), ['nome']),
                "valor_depois" => ($nivel),
                "campo" => "nome"
              ],


              [
                "valor_antes" => Ferramentas::array_index(Ferramentas::array_pesquisa($nivel_data, 'id', $id), ['permissao']),
                "valor_depois" => $permissao,
                "campo" => "cor"
              ],

              [
                "valor_antes" => Ferramentas::array_index(Ferramentas::array_pesquisa($nivel_data, 'id', $id), ['processos']),
                "valor_depois" =>  implode('-', $processos_salva),
                "campo" => "orendem"
              ],
              [
                "valor_antes" => $telaInicialAtual,
                "valor_depois" => $telaInicial,
                "campo" => "tela_inicial"
              ],
              [
                "valor_antes" => (string) $nivelAdicionalAtual,
                "valor_depois" => (string) $nivelAdicionalId,
                "campo" => "nivel_adicional_id"
              ]
            ]
          );

           $db->updateWithRelations($id,[
             'nome' => ($nivel),
             'relatorio' => $relatorioFlag,
             'tela_inicial' => $telaInicial,
             'nivel_adicional_id' => $nivelAdicionalId > 0 ? $nivelAdicionalId : null,
           ],$permissao,implode('-', $processos_salva));

        

          $ok = true;
        } else {
          if ($nome['nome'] != Ferramentas::codificador($nivel)) {
            $msg["Nível"] = 'Nome do Nível já existente';
            $violacao[] = "nivel_modificar nivel já existente";
          } else {
            $msg["Nível"] = 'Nível não houve alteração';
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
  /**
   * Gera o modal para cadastrar um novo nível, exibindo as opções de permissões.
   *
   * @return \CodeIgniter\HTTP\Response Retorna uma resposta JSON contendo o modal gerado.
   */
  function nivel_cadastrar_modal()
  {
    if ($this->request->isAJAX()) {


      $option = "";
      foreach (self::$array_niveis as $item) {
        $option .= '<option value="' . str_replace(' ', '_', $item) . '">' . $item . '</option>';
      }

      $conteudo = [
        0 => '<div class="form-group">
        <label>Nome</label>
        <input type="text" class="form-control" id="nivel_novo" placeholder="Novo Nível">
      </div>
      <div class="form-group">
        <label>Permissões</label><br/> <input type="checkbox" class="" id="checkbox_todos" onclick="selecionar_todos()"><label for="scales">&nbsp; Selecionar todos</label>
        <select multiple="multiple" class="form-control" id="permissao_novo">' . $option . ' </select>
            </div>'
      ];

      $modal = Ferramentas::modal("Cadastrar Função", $conteudo[0], '', 'cadastrar()');
      $data = ['modal' => $modal];
      return $this->response->setJSON($data);
    }
  }
  /**
   * Cadastra um novo nível no banco de dados com base nos dados fornecidos via AJAX.
   *
   * @return \CodeIgniter\HTTP\Response Retorna uma resposta JSON contendo o status da operação e eventuais mensagens de erro.
   */
  function nivel_cadastrar()
  {
    if ($this->request->isAJAX()) {
      $msg = array();
      $ok = false;
      $violacao = array();
      $nivel = service('request')->getPost('nivel');
      $permissao = service('request')->getPost('permissao');
      $relatorio = service('request')->getPost('relatorio');
      $processos = service('request')->getPost('processos');
      $telaInicial = service('request')->getPost('tela_inicial');

      $processos_db = new \App\Models\Processos(); // Obtém a tabela de prioridades do banco

      $processos_data = $processos_db->find();


      if (strlen($nivel) > 30) {
        $msg['Nível'] = "Nome do nível excedeu o tamanho máximo de 30 caracter";
        $violacao[] = "nivel_cadastrar nivel excedeu o tamanho máximo";
      }

      if (strlen($nivel) < 2) {
        $msg['Nível'] = "Nome do nível não possui o tamanho mínimo de 2 caracter";
      } else {
        if (Ferramentas::codificador($nivel) == '') {
          $msg['Nível'] = "Nome do nível possui caracteres não permitidos";
          $violacao[] = "nivel_cadastrar nivel possui caracteres não permitidos";
        }
      }

      if (strlen($permissao) == null) {
        $msg['Permissao'] = "Nenhuma Permissão escolhida.";
      } else if ($permissao != "all") {

        // Verificar se todos os valores existem no array global
        foreach (explode('-', str_replace('_', ' ', $permissao)) as $valor) {
          if (!in_array($valor, self::$array_niveis)) {
            $msg['Permissao'] = "Permissão não encontrada.";
            $violacao[] = "nivel_cadastrar permissão não encontrada.";
          }
        }
      }
      $array_prcoessos = array();
      foreach ($processos_data as $value) {
        if (Ferramentas::array_index($value, ["status"]) == 'ativo') {
          $processoId = (string) Ferramentas::array_index($value, ["id"]);
          if ($processoId !== '') {
            $array_prcoessos[] = $processoId;
          }
        }
      }

      if ($processos === "all") {
        $processos = implode("-", $array_prcoessos);
      } elseif ($processos !== null && $processos !== "") {
        foreach (array_filter(explode('-', $processos), 'strlen') as $valor) {
          if (!in_array((string) $valor, $array_prcoessos, true)) {
            $msg['Permissao'] = "Processo não encontrada.";
            $violacao[] = "nivel_cadastrar processo não encontrada.";
          }
        }
      } else {
        $processos = "";
      }

      $telaInicial = $this->validarTelaInicial($telaInicial, (string) $permissao, $msg, $violacao);


      session_start();
      $nivelAdicionalIdPost = service('request')->getPost('nivel_adicional_id');
      $nivelAdicionalId = $this->validarNivelAdicional($nivelAdicionalIdPost, 0, $msg, $violacao);
      if (count($msg) == 0 and count($violacao) == 0) {

        $db = new \App\Models\Nivel();
        $processos_salva = array_values(array_unique(array_filter(explode('-', (string) $processos), 'strlen')));
        $relatorioFlag = ($relatorio === true || $relatorio === "true" || $relatorio === 1 || $relatorio === "1") ? 1 : 0;


        $nivel_data = $db->find();

        if (count(Ferramentas::array_pesquisa($nivel_data, 'nome', Ferramentas::codificador($nivel))) == 0) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 

          $db->insertWithRelations(
            [
              'nome' => ($nivel),
              'status' => 'ativo',
              'relatorio'  => $relatorioFlag,
              'tela_inicial' => $telaInicial,
              'nivel_adicional_id' => $nivelAdicionalId > 0 ? $nivelAdicionalId : null,
              'usuario_id' => $_SESSION["usuario"]
            ],
            $permissao,
            implode('-', $processos_salva),
          );

          $ok = true;
        } else {
          $msg["Nível"] = 'Nome do nível já existente';
          $violacao[] = "nivel_cadastrar nivel já existente";
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

      $data = ['ok' => $ok, 'msg' => $msg, '1' => $processos,'2' =>$permissao];
      return $this->response->setJSON($data);
    }
  }
  /**
   * Gera as opções de níveis ativos para serem exibidas em um select HTML.
   *
   * @return \CodeIgniter\HTTP\Response Retorna uma resposta JSON contendo as opções de níveis.
   */
  function nivel_option()
  {
    if ($this->request->isAJAX()) {
      $db = new \App\Models\Nivel();
      $nivel_data = $db->find();
      $array = [];
      $option = "<option value=''>Novo Nível</option>";
      session_start();
      foreach ($nivel_data as $key => $value) {
        if ($value["status"] != 'ativo')
          continue;

        $array[Ferramentas::decodificador($value["nome"])] = $value["id"];

        $option .= "<option value='" . Ferramentas::decodificador($value["nome"]) . "'>" . Ferramentas::decodificador($value["nome"]) . "</option>";
      }
      $_SESSION['nivel_option'] = $array;
      $data = ['option' => $option];
      return $this->response->setJSON($data);
    }
  }


  /**
   * Função lista_nivel()
   *
   * Esta função é responsável por buscar informações sobre funções no banco de dados e retorná-las em formato JSON.
   *
   * Retorna um JSON contendo uma lista de nomes de funções obtidos do banco de dados.
   */
  function lista_nivel()
  {
    $funcao = new \App\Models\Nivel(); // Inicializa o modelo de Função para acessar o banco de dados

    $funcao_data = $funcao->find(); // Busca dados sobre funções no banco de dados
    $lista = array();

    // Cria uma lista de nomes de funções decodificadas
    foreach ($funcao_data as $key => $value) { //cria a lista 
      if ($value["status"] != 'ativo')
        continue;

      $lista[] = Ferramentas::decodificador($value['nome']);
    }
    $data = ['lista' => $lista]; // Prepara os dados para serem retornados em formato JSON
    return $this->response->setJSON($data);
  }
}
