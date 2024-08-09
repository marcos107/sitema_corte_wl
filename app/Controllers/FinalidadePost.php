<?php

namespace App\Controllers;

use App\Controllers\Ferramentas;
use Config\App;

class FinalidadePost extends Ferramentas
{

  /**
   * Função finalidade()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por listar as finalidades (propósitos) ativas e seus detalhes, como o nome e o status da finalidade.
   *
   * Retorna um JSON contendo a lista de finalidades ativas e seus detalhes.
   */
  function finalidade() //rece um post via ajax pedindo para listar os usuarios
  {
    if ($this->request->isAJAX()) {
      // Inicializa a sessão para acessar os dados da lista armazenados nela
      session_start();
      $finalidade = new \App\Models\Finalidade(); // Obtém a tabela de finalidades do banco


      $finalidade_data = $finalidade->find();
      $ativos = service('request')->getPost('ativos'); // Obtém a informação POST fornecida via AJAX para listar finalidades ativas
      $desativados = service('request')->getPost('desativados'); // Obtém a informação POST fornecida via AJAX para listar finalidades desativadas
      $lista = "";
      $lista_ids = array();
      $id_temp = 0;
      $lista_completa = array();

      foreach ($finalidade_data as $key => $value) {
        // Cria a lista com base nas finalidades ativas ou desativadas, dependendo da solicitação
        if (($ativos == 'true' && Ferramentas::decodificador($value['status']) == 'ativo')) { //verifica se é para mostrar os com estus ativo
          $lista .= '
      <tr>
       <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['nome']) . '</p></td>
       <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
       <td><button name="cadastarar" type="submit" onclick="desativar(\'' . $id_temp . '\')" class="btn btn-outline-danger btn-lg btn-block"> Desativar </button></td>
      </tr>
      ';
        } else if (($desativados == 'true' && Ferramentas::decodificador($value['status']) == 'desativado')) { //verifica se é para mostrar os com estus desativado
          $lista .= '
      <tr>
       <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['nome']) . '</p></td>
       <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
       <td><button name="cadastarar" type="submit" onclick="ativar(\'' . $id_temp . '\')" class="btn btn-outline-success btn-lg btn-block"> Ativar </button></td>
      </tr>
      ';
        }


        $lista_ids[$id_temp] = $value['id'];
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
   * Cadastra uma nova finalidade via AJAX.
   *
   * Esta função é acionada via AJAX para cadastrar uma nova finalidade no sistema.
   *
   * Resposta JSON indicando se o cadastro foi bem-sucedido e qualquer mensagem de erro associada.
   */
  function finalidade_cadastrar()
  {

    if ($this->request->isAJAX()) {
      $msg = array();
      $ok = false;
      $violacao = array();

      $finalidade = service('request')->getPost('finalidade');

      if (strlen($finalidade) > 17) {
        $msg['Finalidade'] = "Nome da Finalidade excedeu o tamanho máximo de 17 caracter";
        $violacao[] = "finalidade_cadastrar Finalidade excedeu o tamanho máximo";
      }

      if (strlen($finalidade) < 3) {
        $msg['Finalidade'] = "Nome da Finalidade não possui o tamanho mínimo de 3 caracter";
      } else {
        if (Ferramentas::codificador($finalidade) == '') {
          $msg['Finalidade'] = "Nome da Finalidade possui caracteres não permitidos";
          $violacao[] = "finalidade_cadastrar Finalidade possui caracteres não permitidos";
        }
      }


      session_start();
      if (count($msg) == 0) {
        $db = new \App\Models\Finalidade();


        $finalidade_data = $db->find();

        if (count(Ferramentas::array_pesquisa($finalidade_data, 'nome', Ferramentas::codificador($finalidade))) == 0) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 
          // Verifica se o nome da finalidade já existe no sistema
          $date = [
            'nome' => Ferramentas::codificador($finalidade),
            'data_hora_add' => Ferramentas::codificador(date('d/m/Y H:i')),
            'status' => 'ativo',
            'individuo' => $_SESSION['usuario']
          ];

          $db->insert($date);
          $ok = true;
        } else {
          $msg["Finalidade"] = 'Nome da Finalidade já existente';
          $violacao[] = "finalidade_cadastrar Finalidade já existente";
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
   * Retorna informações de uma finalidade via AJAX.
   *
   * Esta função é acionada via AJAX para recuperar informações de uma finalidade específica no sistema.
   *
   * Resposta JSON contendo o nome da finalidade, informações sobre desenhos associados a ela e o status da finalidade.
   */
  function finalidade_modal()
  {
    if ($this->request->isAJAX()) {
      session_start();
      $ok = false;
      $id = service('request')->getPost('id'); // Obtém o ID fornecido via AJAX para buscar informações da finalidade.

      $desenhos = new \App\Models\Desenhos();
      $desenhos_data = $desenhos->find();
      $lista = $_SESSION["lista_completa"][$id];
      if (count(Ferramentas::array_pesquisa($desenhos_data, 'finalidade', $lista['id'])) != 0) {
        $ok = true;
      }

      $data = [
        "nome" => Ferramentas::decodificador($lista['nome']),
        "desenho" => $ok,
        "status" => Ferramentas::decodificador($lista['status'])


      ];
      return $this->response->setJSON($data);
    }
  }


    /**
   * Atualiza informações de uma finalidade via AJAX.
   *
   * Esta função é acionada via AJAX para atualizar informações de uma finalidade no sistema.
   *
   * Resposta JSON indicando se a atualização foi bem-sucedida, as mensagens de erro (se houver) e outras informações relevantes.
   */
  function finalidade_update()
  {
    if ($this->request->isAJAX()) {
      $msg = array();
      $ok = false;
      $violacao = array();
      $finalidade = service('request')->getPost('finalidade');

      if (strlen($finalidade) > 17) {
        $msg['Finalidade'] = "Nome da Finalidade excedeu o tamanho máximo de 17 caracter";
        $violacao[] = "finalidade_update Finalidade excedeu o tamanho máximo";
      }

      if (strlen($finalidade) < 3) {
        $msg['Finalidade'] = "Nome da Finalidade não possui o tamanho mínimo de 3 caracter";
      } else {
        if (Ferramentas::codificador($finalidade) == '') {
          $msg['Finalidade'] = "Nome da Finalidade possui caracteres não permitidos";
          $violacao[] = "finalidade_update Finalidade possui caracteres não permitidos";
        }
      }


      session_start();
      if (count($msg) == 0) {
        $db = new \App\Models\Finalidade();

        $id1 = service('request')->getPost('id');
        $id = $_SESSION['lista'][$id1];
        $finalidade_data = $db->find();
        $desenhos = new \App\Models\Desenhos();
        $desenhos_data = $desenhos->find();
        $lista = $_SESSION["lista_completa"][$id1];
        if (count(Ferramentas::array_pesquisa($desenhos_data, 'finalidade', $lista['id'])) == 0) {
          if (count(Ferramentas::array_pesquisa($finalidade_data, 'nome', Ferramentas::codificador($finalidade))) == 0) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 

            $alteracao = new \App\Models\Alteracoes();

            $data = [
              "individuo" => $_SESSION["usuario"],
              "id_item" => $id,
              "antes" => Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $id), ['nome']),
              "depois" => Ferramentas::codificador($finalidade),
              "item" => "finalidade",
              "info_mais" => "nome",
              "data_add" => Ferramentas::codificador(date('d/m/Y H:i'))

            ];
            $alteracao->insert($data);



            $date = [
              'nome' => Ferramentas::codificador($finalidade),

            ];

            $db->update($id, $date);

            $ok = true;
          } else if (count(Ferramentas::array_pesquisa_mult($finalidade_data, ['id', 'nome'], [$id, Ferramentas::codificador($finalidade)])) != 0) {
            $msg["Modificar"] = 'Não foi feita nenhuma alteração.';
          } else {
            $msg["Finalidade"] = 'Nome da Finalidade já existente';
            $violacao[] = "finalidade_update Finalidade já existente";
          }
        } else { //violação 
          $msg["Modificar"] = 'Finalidade já está em uso.';
          $violacao[] = "finalidade_update Finalidade já está em uso";
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
   * Lista as finalidades ativas via AJAX.
   *
   * Esta função é acionada via AJAX para listar as finalidades ativas no sistema.
   *
   * Resposta JSON contendo a lista de finalidades ativas.
   */
  function finalidade_lista()
  {
    if ($this->request->isAJAX()) {
      session_start();
      $finalidade = new \App\Models\Finalidade(); //pega do banco a tabela

      $finalidade_data = $finalidade->find();
      $lista = array();



      foreach ($finalidade_data as $key => $value) { //cria a lista

        // Verifica se a finalidade está ativa
        if ($value['status'] == 'ativo') {
          $temp['finalidade'] = Ferramentas::decodificador($value['nome']);

          $lista[] = $temp;
        }

      }
      usort($lista, function ($a, $b) {
        return strcasecmp($a['finalidade'], $b['finalidade']);
      });

      //retorna a lista para o ajax
      $data = [
        "lista" => $lista


      ];

      return $this->response->setJSON($data);
    }
  }

}