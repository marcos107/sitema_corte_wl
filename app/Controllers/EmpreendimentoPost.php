<?php

namespace App\Controllers;

use App\Controllers\Ferramentas;
use Config\App;

class EmpreendimentoPost extends EmpresaPost
{
  private function valorBancoDecodificado($valor): string
  {
    $texto = trim((string) Ferramentas::decodificador((string) $valor));
    if ($texto !== '') {
      return $texto;
    }

    return trim((string) $valor);
  }

  private function normalizarEscala($escala): string
  {
    $escala = trim((string) $escala);
    if ($escala === '') {
      return '';
    }

    $escala = preg_replace('/\s+/', '', $escala) ?? $escala;
    if (!preg_match('/^\d{1,5}:\d{1,5}$/', $escala)) {
      return '';
    }

    return $escala;
  }

  private function validarEscala($escala, bool $obrigatoria, array &$msg, array &$violacao, string $contexto): string
  {
    $escalaInformada = trim((string) $escala);
    if ($escalaInformada === '') {
      if ($obrigatoria) {
        $msg['Escala'] = 'Selecione ou informe uma escala.';
        $violacao[] = $contexto . ' Escala nao informada';
      }

      return '';
    }

    $escalaNormalizada = $this->normalizarEscala($escalaInformada);
    if ($escalaNormalizada === '') {
      $msg['Escala'] = 'Informe a escala no formato 1:100.';
      $violacao[] = $contexto . ' Escala invalida';
      return '';
    }

    return $escalaNormalizada;
  }

  private function empreendimentoJaExiste(array $empreendimentoData, int $empresaId, string $nomeEmpreendimento, ?int $ignorarId = null): bool
  {
    foreach ($empreendimentoData as $registro) {
      $registroId = (int) ($registro['id'] ?? 0);
      if ($ignorarId !== null && $registroId === $ignorarId) {
        continue;
      }

      if ((int) ($registro['empresa_id'] ?? 0) !== $empresaId) {
        continue;
      }

      if (strcasecmp($this->valorBancoDecodificado($registro['nome'] ?? ''), $nomeEmpreendimento) === 0) {
        return true;
      }
    }

    return false;
  }

  function empreendimento()
  {
    if ($this->request->isAJAX()) {
      session_start();

      $empresa = new \App\Models\Empresa();
      $empreendimento = new \App\Models\Empreendimentos();

      $empresa_data = $empresa->find();
      $empreendimento_data = $empreendimento->find();
      $ativos = service('request')->getPost('ativos');
      $desativados = service('request')->getPost('desativados');
      $lista = '';
      $lista_ids = array();
      $id_temp = 0;
      $lista_completa = array();

      foreach ($empreendimento_data as $value) {
        $nomeEmpreendimento = htmlspecialchars($this->valorBancoDecodificado($value['nome'] ?? ''), ENT_QUOTES, 'UTF-8');
        $escalaEmpreendimento = htmlspecialchars($this->valorBancoDecodificado($value['escala'] ?? ''), ENT_QUOTES, 'UTF-8');
        $empresaNome = htmlspecialchars((string) Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa_id']), ['nome']), ENT_QUOTES, 'UTF-8');
        $statusEmpreendimento = htmlspecialchars(ucfirst($this->valorBancoDecodificado($value['status'] ?? '')), ENT_QUOTES, 'UTF-8');
        $nomeComEscala = '<div ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . $nomeEmpreendimento;

        if ($escalaEmpreendimento !== '') {
          $nomeComEscala .= '<div class="text-muted small">Escala ' . $escalaEmpreendimento . '</div>';
        }

        $nomeComEscala .= '</div>';

        if (($ativos == 'true' && Ferramentas::decodificador($value['status']) == 'ativo')) {
          $lista .= '
      <tr>
       <td>' . $nomeComEscala . '</td>
       <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . $empresaNome . '</td>
       <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . $statusEmpreendimento . '</td>
       <td><button name="cadastarar" type="submit" onclick="desativar(\'' . $id_temp . '\')" class="btn btn-outline-danger btn-lg btn-block"> Desativar </button></td>
       <td><button name="cadastarar" type="submit" class="btn btn-outline-warning btn-lg btn-block" onclick="modal_modificar(\'modal_' . $id_temp . '\')"> Modificar </button></td>
      </tr>
      ';
        } else if (($desativados == 'true' && Ferramentas::decodificador($value['status']) == 'desativado')) {
          $lista .= '
      <tr>
      <td>' . $nomeComEscala . '</td>
      <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . $empresaNome . '</td>
       <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . $statusEmpreendimento . '</td>
       <td><button name="cadastarar" type="submit" onclick="ativar(\'' . $id_temp . '\')" class="btn btn-outline-success btn-lg btn-block"> Ativar </button></td>
       <td><button name="cadastarar" type="submit" class="btn btn-outline-warning btn-lg btn-block" onclick="modal_modificar(\'modal_' . $id_temp . '\')"> Modificar </button></td>
      </tr>
      ';
        }

        $lista_ids[$id_temp] = $value['id'];
        $value['empresa_id'] = Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa_id']), ['nome']);
        $lista_completa[$id_temp] = $value;
        $id_temp++;
      }

      $_SESSION["lista"] = $lista_ids;
      $_SESSION["lista_completa"] = $lista_completa;

      return $this->response->setJSON([
        "lista" => $lista,
      ]);
    }
  }

  function empreendimento_cadastrar()
  {
    if ($this->request->isAJAX()) {
      $msg = array();
      $violacao = array();
      $ok = false;
      $empreendimento = trim((string) service('request')->getPost('empreendimento'));
      $escala = service('request')->getPost('escala');
      $empresa = service('request')->getPost('empresa');
      $escalaNormalizada = $this->validarEscala($escala, true, $msg, $violacao, 'empreendimento_cadastrar');

      if (strlen($empreendimento) > 17) {
        $msg['Empreendimento'] = "Nome do empreendimento excedeu o tamanho mÃ¡ximo de 17 caracter";
        $violacao[] = "empreendimento_cadastrar Nome da empreendimento excedeu o tamanho mÃ¡ximo";
      }

      if (strlen($empreendimento) < 3) {
        $msg['Empreendimento'] = "Nome da empreendimento nÃ£o possui o tamanho mÃ­nimo de 3 caracter";
      } else if (Ferramentas::codificador($empreendimento) == '') {
        $msg['Empreendimento'] = "Nome do empreendimento possui caracteres nÃ£o permitidos";
        $violacao[] = "empreendimento_cadastrar Nome do empreendimento possui caracteres nÃ£o permitidos";
      }

      $lista_array = EmpresaPost::lista_empresa();
      $lista_array = json_decode($lista_array->getBody(), true);

      if (!in_array($empresa, $lista_array['lista'])) {
        $msg['Empresa'] = "Nome da empresa nÃ£o cadastrado";
        $violacao[] = "empreendimento_cadastrar Nome da empresa nÃ£o cadastrado";
      } else if (Ferramentas::codificador($empresa) == '') {
        $msg['Empresa'] = "Empresa possui caracteres nÃ£o permitidos";
        $violacao[] = "empreendimento_cadastrar Empresa possui caracteres nÃ£o permitidos";
      }

      session_start();

      if (count($msg) == 0) {
        $empresa_db = new \App\Models\Empresa();
        $db = new \App\Models\Empreendimentos();
        $empreendimento_data = $db->find();
        $empresa_data = $empresa_db->find();

        $empresa_id = (int) Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'nome', ($empresa)), ['id']);

        if (!$this->empreendimentoJaExiste($empreendimento_data, $empresa_id, $empreendimento)) {
          $date = [
            'nome' => Ferramentas::codificador($empreendimento),
            'escala' => Ferramentas::codificador($escalaNormalizada),
            'data_hora_add' => Ferramentas::codificador(date('d/m/Y H:i')),
            'status' => 'ativo',
            'empresa_id' => $empresa_id,
            'individuo' => $_SESSION['usuario']
          ];
          $db->insert($date);
          $ok = true;
        } else {
          $msg["empreendimento"] = 'Nome do empreendimento jÃ¡ existente nessa empresa';
          $violacao[] = "empreendimento_cadastrar Nome do empreendimento jÃ¡ existente nessa empresa";
        }
      }

      if (count($violacao) != 0) {
        $db = new \App\Models\Violacao();
        foreach ($violacao as $value) {
          $data = [
            "usuario_id" => $_SESSION["usuario"],
            "causa" => $value
          ];

          $db->insert($data);
        }
      }

      return $this->response->setJSON(['ok' => $ok, 'msg' => $msg]);
    }
  }

  function empreendimento_modal()
  {
    if ($this->request->isAJAX()) {
      session_start();
      $ok = false;
      $id = service('request')->getPost('id');

      $desenhos = new \App\Models\Desenhos();
      $desenhos_data = $desenhos->find();
      $lista = $_SESSION["lista_completa"][$id];

      if (count(Ferramentas::array_pesquisa($desenhos_data, 'empreendimento', $lista['id'])) != 0) {
        $ok = true;
      }

      return $this->response->setJSON([
        "nome" => $this->valorBancoDecodificado($lista['nome'] ?? ''),
        "escala" => $this->valorBancoDecodificado($lista['escala'] ?? ''),
        "empresa_id" => $this->valorBancoDecodificado($lista['empresa_id'] ?? ''),
        "desenho" => $ok,
        "status" => $this->valorBancoDecodificado($lista['status'] ?? ''),
      ]);
    }
  }

  function empreendimento_update()
  {
    if ($this->request->isAJAX()) {
      $msg = array();
      $ok = false;
      $violacao = array();
      $empreendimento = trim((string) service('request')->getPost('empreendimento'));
      $escala = service('request')->getPost('escala');
      $empresa = service('request')->getPost('empresa');

      if (strlen($empreendimento) > 17) {
        $msg['Empreendimento'] = "Nome do empreendimento excedeu o tamanho mÃ¡ximo de 17 caracter";
        $violacao[] = "empreendimento_update Nome do empreendimento excedeu o tamanho mÃ¡ximo";
      }

      if (strlen($empreendimento) < 3) {
        $msg['Empreendimento'] = "Nome da empreendimento nÃ£o possui o tamanho mÃ­nimo de 3 caracter";
      } else if (Ferramentas::codificador($empreendimento) == '') {
        $msg['Empreendimento'] = "Nome do empreendimento possui caracteres nÃ£o permitidos";
        $violacao[] = "empreendimento_update Nome do empreendimento possui caracteres";
      }

      $lista_array = EmpresaPost::lista_empresa();
      $lista_array = json_decode($lista_array->getBody(), true);

      if (!in_array($empresa, $lista_array['lista'])) {
        $msg['Empresa'] = "Nome da empresa nÃ£o cadastrado";
        $violacao[] = "empreendimento_update Nome da empresa nÃ£o cadastrado";
      } else if (Ferramentas::codificador($empresa) == '') {
        $msg['Empresa'] = "Empresa possui caracteres nÃ£o permitidos";
        $violacao[] = "empreendimento_update Empresa possui caracteres nÃ£o permitidos";
      }

      session_start();

      if (count($msg) == 0) {
        $empresa_db = new \App\Models\Empresa();
        $db = new \App\Models\Empreendimentos();
        $empreendimento_data = $db->find();
        $empresa_data = $empresa_db->find();
        $id1 = service('request')->getPost('id');
        $id = (int) $_SESSION['lista'][$id1];
        $desenhos = new \App\Models\Desenhos();
        $desenhos_data = $desenhos->find();
        $lista = $_SESSION["lista_completa"][$id1];
        $registroAtual = Ferramentas::array_pesquisa($empreendimento_data, 'id', $id);
        $nomeAtual = $this->valorBancoDecodificado($registroAtual['nome'] ?? '');
        $escalaAtual = $this->valorBancoDecodificado($registroAtual['escala'] ?? '');
        $empresaAtualId = (int) ($registroAtual['empresa_id'] ?? 0);
        $empresa_id = (int) Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'nome', ($empresa)), ['id']);
        $escalaNormalizada = $this->validarEscala($escala, $escalaAtual !== '', $msg, $violacao, 'empreendimento_update');

        if (count(Ferramentas::array_pesquisa($desenhos_data, 'empreendimento', $lista['id'])) == 0) {
          $semMudancas = (
            strcasecmp($nomeAtual, $empreendimento) === 0
            && $empresaAtualId === $empresa_id
            && $escalaAtual === $escalaNormalizada
          );

          if ($semMudancas) {
            $msg["Modificar"] = 'Nenhum item foi modificado.';
          } else if (!$this->empreendimentoJaExiste($empreendimento_data, $empresa_id, $empreendimento, $id)) {
            $alteracao = new \App\Models\Alteracoes();
            $detalhesAlteracao = [
              [
                "valor_antes" => $nomeAtual,
                "valor_depois" => $empreendimento,
                "campo" => "nome"
              ],
              [
                "valor_antes" => $empresaAtualId,
                "valor_depois" => $empresa_id,
                "campo" => "empresa_id"
              ]
            ];

            if ($escalaAtual !== $escalaNormalizada) {
              $detalhesAlteracao[] = [
                "valor_antes" => $escalaAtual,
                "valor_depois" => $escalaNormalizada,
                "campo" => "escala"
              ];
            }

            $alteracao->insertWithDetails(
              [
                "usuario_id" => $_SESSION["usuario"],
                "id_item" => $id,
                "item" => "empreendimentos",
              ],
              $detalhesAlteracao
            );

            $date = [
              'nome' => Ferramentas::codificador($empreendimento),
              'empresa_id' => $empresa_id
            ];

            if ($escalaNormalizada !== '' || $escalaAtual !== '') {
              $date['escala'] = $escalaNormalizada === '' ? null : Ferramentas::codificador($escalaNormalizada);
            }

            $db->update($id, $date);
            $ok = true;
          } else {
            $msg["Empreendimento"] = 'Nome do empreendimento jÃ¡ existente nessa empresa';
            $violacao[] = "empreendimento_update Nome do empreendimento jÃ¡ existente nessa empresa";
          }
        } else {
          $msg["Modificar"] = 'Empreendimento jÃ¡ estÃ¡ em uso.';
          $violacao[] = "empreendimento_update Empreendimento jÃ¡ estÃ¡ em uso";
        }
      }

      if (count($violacao) != 0) {
        $db = new \App\Models\Violacao();
        foreach ($violacao as $value) {
          $data = [
            "usuario_id" => $_SESSION["usuario"],
            "causa" => $value
          ];

          $db->insert($data);
        }
      }

      return $this->response->setJSON(['ok' => $ok, 'msg' => $msg, '1' => $violacao]);
    }
  }

  function empreendimento_lista()
  {
    if ($this->request->isAJAX()) {
      session_start();
      $empreendimento = new \App\Models\Empreendimentos();
      $empresa = new \App\Models\Empresa();
      $empresa_data = $empresa->find();
      $empresa_usando = service('request')->getPost('empresa');
      $empresaTokens = $_SESSION['desenho_empresa_tokens'] ?? [];
      $id = (int) ($empresaTokens[(string) $empresa_usando] ?? 0);
      if ($id <= 0) {
        $id = Ferramentas::array_index(
          Ferramentas::array_pesquisa($empresa_data, 'nome', Ferramentas::codificador($empresa_usando)),
          ['id']
        );
      }
      $empreendimento_data = $empreendimento->find();
      $lista = array();
      $lista_session = array();
      $tokens = $_SESSION['subpasta_empreendimento_tokens'] ?? [];

      foreach ($empreendimento_data as $value) {
        if ($value['status'] == 'ativo' && ($value['empresa_id'] == $id or $id == "")) {
          $empreendimentoId = (int) ($value['id'] ?? 0);
          $token = array_search($empreendimentoId, $tokens, true);
          if ($token === false) {
            $token = bin2hex(random_bytes(16));
            $tokens[$token] = $empreendimentoId;
          }

          $temp['id'] = $token;
          $temp['empreendimento'] = $this->valorBancoDecodificado($value['nome'] ?? '');
          $lista_session[$empreendimentoId] = $temp['empreendimento'];
          $lista[] = $temp;
        }
      }

      usort($lista, function ($a, $b) {
        return strcasecmp($a['empreendimento'], $b['empreendimento']);
      });

      $_SESSION["lista_empreendimento"] = $lista_session;
      $_SESSION['subpasta_empreendimento_tokens'] = $tokens;

      return $this->response->setJSON([
        "lista" => $lista,
      ]);
    }
  }

  public function empreendimentos_lista()
  {
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON(['lista' => []]);
    }

    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    $empresaIdRaw = $this->request->getGet('empresaId');
    if ($empresaIdRaw === null || $empresaIdRaw === '') {
      $empresaIdRaw = service('request')->getPost('empresaId');
    }
    $empresaTokens = $_SESSION['desenho_empresa_tokens'] ?? [];
    $empresaId = (int) ($empresaTokens[(string) $empresaIdRaw] ?? 0);
    if ($empresaId <= 0) {
      return $this->response->setJSON(['lista' => []]);
    }

    $empreendimento = new \App\Models\Empreendimentos();
    $builder = $empreendimento
      ->select('id, nome, empresa_id, status')
      ->where('status', 'ativo');

    $builder->where('empresa_id', $empresaId);

    $empreendimento_data = $builder
      ->orderBy('nome', 'ASC')
      ->findAll();

    $tokens = $_SESSION['desenho_empreendimento_tokens'] ?? [];
    $lista = [];
    foreach ($empreendimento_data as $value) {
      $empreendimentoId = (int) ($value['id'] ?? 0);
      $token = null;
      foreach ($tokens as $tokenExistente => $dadosToken) {
        if (
          (int) ($dadosToken['id'] ?? 0) === $empreendimentoId &&
          (int) ($dadosToken['empresa_id'] ?? 0) === $empresaId
        ) {
          $token = $tokenExistente;
          break;
        }
      }

      if ($token === null) {
        $token = bin2hex(random_bytes(16));
        $tokens[$token] = [
          'id' => $empreendimentoId,
          'empresa_id' => $empresaId,
        ];
      }

      $lista[] = [
        'id' => $token,
        'nome' => $this->valorBancoDecodificado($value['nome'] ?? '')
      ];
    }

    $_SESSION['desenho_empreendimento_tokens'] = $tokens;

    return $this->response->setJSON(['lista' => $lista]);
  }
}
