<?php

namespace App\Controllers;

use App\Controllers\Ferramentas;
use Config\App;

class EmpreendimentoPost extends EmpresaPost
{
   /**
   * Função empreendimento()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por listar empreendimentos ativos e seus detalhes, como o nome, empresa relacionada e status.
   *
   * Retorna um JSON contendo a lista de empreendimentos ativos e seus detalhes.
   */
  function empreendimento() //rece um post via ajax pedindo para listar os usuarios
  {
    if ($this->request->isAJAX()) {
      // Inicializa a sessão para acessar os dados da lista armazenados nela
      session_start();

      $empresa = new \App\Models\Empresa(); // Obtém a tabela de empresas do banco
      $empreendimento = new \App\Models\Empreendimentos(); // Obtém a tabela de empreendimentos do banco

      $empresa_data = $empresa->find();
      $empreendimento_data = $empreendimento->find();
      $ativos = service('request')->getPost('ativos'); // Obtém a informação POST fornecida via AJAX para listar empreendimentos ativos
      $desativados = service('request')->getPost('desativados'); // Obtém a informação POST fornecida via AJAX para listar empreendimentos desativados
      $lista = "";
      $lista_ids = array();
      $id_temp = 0;
      $lista_completa = array();

      foreach ($empreendimento_data as $key => $value) {
        // Cria a lista com base nos empreendimentos ativos ou desativados, dependendo da solicitação
        if (($ativos == 'true' && Ferramentas::decodificador($value['status']) == 'ativo')) { //verifica se é para mostrar os com estus ativo
          $lista .= '
      <tr>
       <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['nome']) . '</p></td>
       <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa_id']), ['nome']) . '</td>
       <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
       <td><button name="cadastarar" type="submit" onclick="desativar(\'' . $id_temp . '\')" class="btn btn-outline-danger btn-lg btn-block"> Desativar </button></td>
       <td><button name="cadastarar" type="submit" class="btn btn-outline-warning btn-lg btn-block" onclick="modal_modificar(\'modal_' . $id_temp . '\')"> Modificar </button></td>
      </tr>
      ';
        } else if (($desativados == 'true' && Ferramentas::decodificador($value['status']) == 'desativado')) { //verifica se é para mostrar os com estus desativado
          $lista .= '
      <tr>
      <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['nome']) . '</p></td>
      <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa_id']), ['nome']) . '</td>
       <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
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

      // Armazena os IDs e detalhes da lista na sessão para uso posterior
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
   * Função empreendimento_cadastrar()
   *
   * Esta função é responsável por cadastrar um novo empreendimento no banco de dados.
   *
   * Retorna um JSON indicando se o cadastro foi bem-sucedido ou não, juntamente com mensagens de erro, se houverem.
   */
  function empreendimento_cadastrar()
  {
    if ($this->request->isAJAX()) {
      $msg = array();
      $violacao = array();
      $ok = false;
      $empreendimento = service('request')->getPost('empreendimento');
      $empresa = service('request')->getPost('empresa');

      //verifica o tamanho maximo do empreendimento
      if (strlen($empreendimento) > 17) {
        $msg['Empreendimento'] = "Nome do empreendimento excedeu o tamanho máximo de 17 caracter";
        $violacao[] = "empreendimento_cadastrar Nome da empreendimento excedeu o tamanho máximo";
      }

      //verifica o tamanho mínimo do empreendimento
      if (strlen($empreendimento) < 3) {
        $msg['Empreendimento'] = "Nome da empreendimento não possui o tamanho mínimo de 3 caracter";
      } else {
        //verifica se possui caracter invalido
        if (Ferramentas::codificador($empreendimento) == '') {
          $msg['Empreendimento'] = "Nome do empreendimento possui caracteres não permitidos";
          $violacao[] = "empreendimento_cadastrar Nome do empreendimento possui caracteres não permitidos";
        }
      }

      //
      $lista_array = EmpresaPost::lista_empresa();
      $lista_array = json_decode($lista_array->getBody(), true);

      if (!in_array($empresa, $lista_array['lista'])) {
        $msg['Empresa'] = "Nome da empresa não cadastrado";
        $violacao[] = "empreendimento_cadastrar Nome da empresa não cadastrado";
      } else {
        if (Ferramentas::codificador($empresa) == '') {
          $msg['Empresa'] = "Empresa possui caracteres não permitidos";
          $violacao[] = "empreendimento_cadastrar Empresa possui caracteres não permitidos";
        }
      }


      session_start();
      if (count($msg) == 0) {
        $empresa_db = new \App\Models\Empresa();
        $db = new \App\Models\Empreendimentos();
        $empreendimento_data = $db->find();
        $empresa_data = $empresa_db->find();

        $empresa_id = Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'nome', Ferramentas::codificador($empresa)), ['id']); // pega o id da empresa fornecida 
        if (count(Ferramentas::array_pesquisa_mult($empreendimento_data, ['nome', 'empresa_id'], [Ferramentas::codificador($empreendimento), $empresa_id])) == 0) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 
          $date = [
            'nome' => Ferramentas::codificador($empreendimento),
            'data_hora_add' => Ferramentas::codificador(date('d/m/Y H:i')),
            'status' => 'ativo',
            'empresa_id' => $empresa_id,
            'individuo' => $_SESSION['usuario']

          ];
          $db->insert($date);
          $ok = true;
        } else {
          $msg["empreendimento"] = 'Nome do empreendimento já existente nessa empresa';
          $violacao[] = "empreendimento_cadastrar Nome do empreendimento já existente nessa empresa";
        }


      }

      if (count($violacao) != 0) {
        //$violacao = Ferramentas::array_codificar($violacao);
        $db = new \App\Models\Violacao();
        foreach ($violacao as $key => $value) {

          $data = [
            "individuo" => $_SESSION["usuario"],
            "causa" => $value,
            "data" => Ferramentas::codificador(date('d/m/Y H:i'))

          ];

          $db->insert($data);

        }
      }


      $data = ['ok' => $ok, 'msg' => $msg];
      return $this->response->setJSON($data);


    }
  }

    /**
   * Função empreendimento_modal()
   *
   * Esta função é responsável por retornar informações específicas de um empreendimento, incluindo seu nome,
   * ID da empresa e se possui desenhos associados.
   *
   * Retorna um JSON contendo informações sobre o empreendimento.
   */
  function empreendimento_modal()
  {
    if ($this->request->isAJAX()) {
      session_start();
      $ok = false;
      $id = service('request')->getPost('id'); // Obtém o ID do empreendimento via requisição AJAX

      // Retorna a lista para o AJAX
      $desenhos = new \App\Models\Desenhos();
      $desenhos_data = $desenhos->find();
      $lista = $_SESSION["lista_completa"][$id];

      // Verifica se existem desenhos associados a este empreendimento
      if (count(Ferramentas::array_pesquisa($desenhos_data, 'empreendimento', $lista['id'])) != 0) {
        $ok = true;
      }

      $data = [
        "nome" => Ferramentas::decodificador($lista['nome']),
        "empresa_id" => Ferramentas::decodificador($lista['empresa_id']),
        "desenho" => $ok,
        "status" => Ferramentas::decodificador($lista['status'])


      ];
      return $this->response->setJSON($data);
    }
  }

    /**
   * Função empreendimento_update()
   *
   * Esta função é responsável por atualizar informações de um empreendimento, incluindo seu nome e a empresa associada.
   *
   * Retorna um JSON indicando o sucesso da operação e possíveis mensagens de erro.
   */
  function empreendimento_update()
  {
    if ($this->request->isAJAX()) {
      $msg = array();
      $ok = false;
      $violacao = array();
      $empreendimento = service('request')->getPost('empreendimento');
      $empresa = service('request')->getPost('empresa');

      if (strlen($empreendimento) > 17) {
        $msg['Empreendimento'] = "Nome do empreendimento excedeu o tamanho máximo de 17 caracter";
        $violacao[] = "empreendimento_update Nome do empreendimento excedeu o tamanho máximo";
      }

      if (strlen($empreendimento) < 3) {
        $msg['Empreendimento'] = "Nome da empreendimento não possui o tamanho mínimo de 3 caracter";
      } else {
        if (Ferramentas::codificador($empreendimento) == '') {
          $msg['Empreendimento'] = "Nome do empreendimento possui caracteres não permitidos";
          $violacao[] = "empreendimento_update Nome do empreendimento possui caracteres";
        }
      }


      $lista_array = EmpresaPost::lista_empresa();
      $lista_array = json_decode($lista_array->getBody(), true);

      if (!in_array($empresa, $lista_array['lista'])) {
        $msg['Empresa'] = "Nome da empresa não cadastrado";
        $violacao[] = "empreendimento_update Nome da empresa não cadastrado";
      } else {
        if (Ferramentas::codificador($empresa) == '') {
          $msg['Empresa'] = "Empresa possui caracteres não permitidos";
          $violacao[] = "empreendimento_update Empresa possui caracteres não permitidos";
        }
      }

      session_start();

      if (count($msg) == 0) {
        $empresa_db = new \App\Models\Empresa();
        $db = new \App\Models\Empreendimentos();
        $empreendimento_data = $db->find();
        $empresa_data = $empresa_db->find();
        $id1 = service('request')->getPost('id');
        $id = $_SESSION['lista'][$id1];
        $desenhos = new \App\Models\Desenhos();
        $desenhos_data = $desenhos->find();
        $lista = $_SESSION["lista_completa"][$id1];
        if (count(Ferramentas::array_pesquisa($desenhos_data, 'empreendimento', $lista['id'])) == 0) {//verifica se 
          $empresa_id = Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'nome', Ferramentas::codificador($empresa)), ['id']); // verifica se o empreendimento que esta tentando modificar ja não foi usada
          if (count(Ferramentas::array_pesquisa_mult($empreendimento_data, ['nome', 'empresa_id'], [Ferramentas::codificador($empreendimento), $empresa_id])) == 0) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 
            $alteracao = new \App\Models\Alteracoes();

            $data = [
              "individuo" => $_SESSION["usuario"],
              "id_item" => $id,
              "antes" => Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $id), ['nome']) . " - " . Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $id), ['empresa_id']),
              "depois" => Ferramentas::codificador($empreendimento) . " - " . $empresa_id,
              "item" => "empreendimento",
              "info_mais" => "nome - empresa_id",
              "data_add" => Ferramentas::codificador(date('d/m/Y H:i'))

            ];
            $alteracao->insert($data);

            $date = [
              'nome' => Ferramentas::codificador($empreendimento),

              'empresa_id' => $empresa_id


            ];


            $db->update($id, $date);
            $ok = true;
          } else if (count(Ferramentas::array_pesquisa_mult($empreendimento_data, ['id', 'nome', 'empresa_id'], [$id, Ferramentas::codificador($empreendimento), $empresa_id])) != 0) {
            $msg["Modificar"] = 'Não foi feita nenhuma alteração.';
          } else {
            $msg["Empreendimento"] = 'Nome do empreendimento já existente nessa empresa';
            $violacao[] = "empreendimento_update Nome do empreendimento já existente nessa empresa";
          }
        } else { //violação 
          $msg["Modificar"] = 'Empreendimento já está em uso.';
          $violacao[] = "empreendimento_update Empreendimento já está em uso";
        }


      }


      if (count($violacao) != 0) {
        //$violacao = Ferramentas::array_codificar($violacao);
        $db = new \App\Models\Violacao();
        foreach ($violacao as $key => $value) {

          $data = [
            "individuo" => $_SESSION["usuario"],
            "causa" => $value,
            "data" => Ferramentas::codificador(date('d/m/Y H:i'))

          ];

          $db->insert($data);

        }
      }

      $data = ['ok' => $ok, 'msg' => $msg, '1' => $violacao];
      return $this->response->setJSON($data);


    }
  }


    /**
   * Lista os empreendimentos ativos associados a uma empresa específica via AJAX.
   *
   * Esta função é acionada via AJAX para listar os empreendimentos ativos que estão associados a uma empresa específica.
   *
   * Resposta JSON contendo a lista de empreendimentos ativos da empresa.
   */
  function empreendimento_lista()
  {
    if ($this->request->isAJAX()) {
      session_start();
      $empreendimento = new \App\Models\Empreendimentos(); //pega do banco a tabela
      $empresa = new \App\Models\Empresa();
      $empresa_data = $empresa->find();
      $empresa_usando = service('request')->getPost('empresa');
      $id = Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'nome', Ferramentas::codificador($empresa_usando)), ['id']);
      $empreendimento_data = $empreendimento->find();
      $lista = array();
      $lista_session = array();


      foreach ($empreendimento_data as $key => $value) { //cria a lista

        // Verifica se o empreendimento está ativo e associado à empresa específica
        if ($value['status'] == 'ativo' && ($value['empresa_id'] == $id or $id == "") ) {
          $temp['empreendimento'] = Ferramentas::decodificador($value['nome']);
          $lista_session[$value['id']] = $temp['empreendimento'];
          $lista[] = $temp;
        }

      }
      usort($lista, function ($a, $b) {
        return strcasecmp($a['empreendimento'], $b['empreendimento']);
      });
      $_SESSION["lista_empreendimento"] = $lista_session;
      //retorna a lista para o ajax
      $data = [
        "lista" => $lista,


      ];

      return $this->response->setJSON($data);
    }
  }
}