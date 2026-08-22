<?php

namespace App\Controllers;



use App\Controllers\Ferramentas;
use Config\App;
use App\Controllers\EmpresaPost;


class PrioridadePost extends EmpresaPost
{

  /**
   * Função prioridade()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por listar as prioridades com base em critérios específicos, como ativas ou desativadas. Também oferece a opção de ativar ou desativar prioridades.
   *
   * Retorna um JSON contendo a lista de prioridades de acordo com os critérios fornecidos, bem como a capacidade de ativar ou desativar prioridades.
   */
  function prioridade()
  {
    if ($this->request->isAJAX()) {
      // Inicializa a sessão para acessar os dados da lista armazenados nela
      session_start();
      $prioridade = new \App\Models\Prioridade(); // Obtém a tabela de prioridades do banco

      $prioridade_data = $prioridade->find();
      $ativos = service('request')->getPost('ativos'); // Obtém a informação post que foi fornecida via AJAX para listar prioridades ativas
      $desativados = service('request')->getPost('desativados'); // Obtém a informação post que foi fornecida via AJAX para listar prioridades desativadas

      $lista = "";
      $lista_ids = array();
      $id_temp = 0;
      $lista_completa = array();

      foreach ($prioridade_data as $key => $value) {
        // Cria a lista com base nos critérios especificados
        if (($ativos == 'true' && Ferramentas::decodificador($value['status']) == 'ativo')) { //verifica se é para mostrar os com estus ativo
          $lista .= '
      <tr>
       <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['nome']) . '</p></td>
       <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['ordem']) . '</td>
       <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')" bgcolor="' . Ferramentas::decodificador($value['cor']) . '"><span class="marca_texto">' . Ferramentas::decodificador($value['cor']) . '</span></td>
       <td>' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
       <td><button name="cadastarar" type="submit" onclick="desativar(\'' . $id_temp . '\')" class="btn btn-outline-danger btn-lg btn-block"> Desativar </button></td>
       <td><button name="cadastarar" type="submit" class="btn btn-outline-warning btn-lg btn-block" onclick="modal_modificar(\'modal_' . $id_temp . '\')"> Modificar </button></td>
       </tr>
      ';
        } else if (($desativados == 'true' && Ferramentas::decodificador($value['status']) == 'desativado')) { //verifica se é para mostrar os com estus desativado
          $lista .= '
      <tr>
      <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['nome']) . '</p></td>
      <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['ordem']) . '</td>
      <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')" bgcolor="' . Ferramentas::decodificador($value['cor']) . '"><span class="marca_texto">' . Ferramentas::decodificador($value['cor']) . '</span></td>
       <td>' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
       <td><button name="cadastarar" type="submit" onclick="ativar(\'' . $id_temp . '\')" class="btn btn-outline-success btn-lg btn-block"> Ativar </button></td>
      <td><button name="cadastarar" type="submit" class="btn btn-outline-warning btn-lg btn-block" onclick="modal_modificar(\'modal_' . $id_temp . '\')"> Modificar </button></td>
       </tr>
      ';
        }


        $lista_ids[$id_temp] = $value['id'];
        $lista_completa[$id_temp] = $value;
        $id_temp++;
      }

      // Armazena os IDs da lista e os dados completos na sessão
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
   * Obtém a ordem máxima das prioridades a partir de dados armazenados em um banco de dados.
   *
   * Essa função é acionada via AJAX para recuperar a ordem máxima das prioridades a partir dos dados armazenados em um banco de dados. A ordem máxima é usada geralmente para adicionar uma nova prioridade com uma ordem maior.
   *
   * Resposta JSON contendo o valor da ordem máxima.
   */
  function ordem_max()
  {
    if ($this->request->isAJAX()) {
      $db = new \App\Models\Prioridade();
      $max = 0;

      $prioridade_data = $db->find();
      foreach ($prioridade_data as $key => $value) {
        if (intval($value['ordem']) >= $max) {
          $max = $value['ordem'];
        }
      }
      return $this->response->setJSON(['max' => $max]);
    }
  }


    /**
   * Cadastra uma nova prioridade via AJAX.
   *
   * Esta função é acionada via AJAX para cadastrar uma nova prioridade no sistema.
   *
   * Resposta JSON indicando se o cadastro foi bem-sucedido, as mensagens de erro (se houver) e outras informações relevantes.
   */
  function prioridade_cadastrar()
  {

    if ($this->request->isAJAX()) {
      $msg = array();
      $ok = false;
      $violacao = array();

      $prioridade = service('request')->getPost('prioridade');
      $cor = service('request')->getPost('cor');


      if (strlen($prioridade) > 17) {
        $msg['Prioridade'] = "Nome da Prioridade excedeu o tamanho máximo de 17 caracter";
        $violacao[] = "prioridade_cadastrar Prioridade excedeu o tamanho máximo";
      }

      if (strlen($prioridade) < 1) {
        $msg['Prioridade'] = "Nome da Prioridade não possui o tamanho mínimo de 1 caracter";
      } else {
        if (Ferramentas::codificador($prioridade) == '') {
          $msg['Prioridade'] = "Nome da Prioridade possui caracteres não permitidos";
          $violacao[] = "prioridade_cadastrar Prioridade possui caracteres não permitidos";
        }
      }
      if (Ferramentas::codificador($cor) == '') {
        $msg['Cor'] = "Cor possui caracteres não permitidos";
        $violacao[] = "prioridade_cadastrar Cor possui caracteres não permitidos";
      }

      $lista_array = EmpresaPost::lista_empresa();
      $lista_array = json_decode($lista_array->getBody(), true);


      session_start();
      if (count($msg) == 0) {
        $db = new \App\Models\Prioridade();


        $prioridade_data = $db->find();

        $ordem_max = $this->ordem_max();
        $ordem_max = Ferramentas::array_index(json_decode($ordem_max->getBody(), true), ['max']) + 1;

        if (count(Ferramentas::array_pesquisa($prioridade_data, 'nome', Ferramentas::codificador($prioridade))) == 0) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 
          $date = [
            'nome' => ($prioridade),
            'status' => 'ativo',
            'usuario_id' => $_SESSION['usuario'],
            'cor' => ($cor),
            'ordem' => $ordem_max
          ];
          $db->insert($date);
          $ok = true;
        } else {
          $msg["Prioridade"] = 'Nome da Prioridade já existente';
          $violacao[] = "prioridade_cadastrar Nome da Prioridade já existente";
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
   * Obtém os detalhes de uma prioridade via AJAX.
   *
   * Esta função é acionada via AJAX para obter os detalhes de uma prioridade com base no ID fornecido.
   *
   * Resposta JSON contendo os detalhes da prioridade, como nome, cor, ordem e status.
   */
  function prioridade_modal()
  {
    if ($this->request->isAJAX()) {
      session_start();
      $ok = false;
      $id = service('request')->getPost('id'); // Pega a informação post fornecida via ajax para obter detalhes da prioridade
      $lista = $_SESSION["lista_completa"][$id];

      $data = [
        "nome" => Ferramentas::decodificador($lista['nome']),
        "cor" => Ferramentas::decodificador($lista['cor']),
        "ordem" => Ferramentas::decodificador($lista['ordem']),
        "status" => Ferramentas::decodificador($lista['status'])


      ];
      return $this->response->setJSON($data);
    }
  }



  /**
   * Atualiza os detalhes de uma prioridade via AJAX.
   *
   * Esta função é acionada via AJAX para atualizar os detalhes de uma prioridade com base nas informações fornecidas.
   *
   * Resposta JSON indicando se a atualização foi bem-sucedida ou não, e qualquer mensagem associada.
   */
  function prioridade_update()
  {
    if ($this->request->isAJAX()) {
      $msg = array();
      $ok = false;
      $violacao = array();

      $prioridade = service('request')->getPost('prioridade');
      $cor = service('request')->getPost('cor');


      if (strlen($prioridade) > 17) {
        $msg['Prioridade'] = "Nome da Prioridade excedeu o tamanho máximo de 17 caracter";
        $violacao[] = "prioridade_update Prioridade excedeu o tamanho máximo";
      }

      if (strlen($prioridade) < 1) {
        $msg['Prioridade'] = "Nome da Prioridade não possui o tamanho mínimo de 1 caracter";
      } else {
        if (Ferramentas::codificador($prioridade) == '') {
          $msg['Prioridade'] = "Nome da Prioridade possui caracteres não permitidos";
          $violacao[] = "prioridade_update Prioridade possui caracteres";
        }
      }
      if (Ferramentas::codificador($cor) == '') {
        $msg['Cor'] = "Cor possui caracteres não permitidos";
        $violacao[] = "prioridade_update Cor possui caracteres não permitidos";
      }

      $lista_array = EmpresaPost::lista_empresa();
      $lista_array = json_decode($lista_array->getBody(), true);


      session_start();
      if (count($msg) == 0) {
        $db = new \App\Models\Prioridade();

        $id1 = service('request')->getPost('id');
        $id = $_SESSION['lista'][$id1];

        $prioridade_data = $db->find();

        $ordem = service('request')->getPost('ordem');

        if (count(Ferramentas::array_pesquisa($prioridade_data, 'ordem', $ordem)) != 0) {

          if (count(Ferramentas::array_pesquisa($prioridade_data, 'nome', Ferramentas::codificador($prioridade))) == 0 || count(Ferramentas::array_pesquisa_mult($prioridade_data, ['id', 'nome', 'cor', 'ordem'], [$id, Ferramentas::codificador($prioridade), Ferramentas::codificador($cor), $ordem])) == 0) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 
          $alteracao = new \App\Models\Alteracoes();

          $alteracao->insertWithDetails(
            [
              "usuario_id" => $_SESSION["usuario"],
              "id_item" => $id,
              "item" => "prioridade",

            ],
            [
              [
                "valor_antes" => Ferramentas::array_index(Ferramentas::array_pesquisa($prioridade_data, 'id', $id), ['nome']),
                "valor_depois" => Ferramentas::norma_lizar_str($prioridade),
                "campo" => "nome"
              ],


              [
                "valor_antes" => Ferramentas::array_index(Ferramentas::array_pesquisa($prioridade_data, 'id', $id), ['cor']),
                "valor_depois" => $cor,
                "campo" => "cor"
              ],

              [
                "valor_antes" => Ferramentas::array_index(Ferramentas::array_pesquisa($prioridade_data, 'id', $id), ['ordem']),
                "valor_depois" =>  $ordem,
                "campo" => "orendem"
              ]
            ]
          );



            if (count(Ferramentas::array_pesquisa_mult($prioridade_data, ['id', 'ordem'], [$id, $ordem])) == 0) {


              $db->update(
                Ferramentas::array_index(Ferramentas::array_pesquisa_mult($prioridade_data, ['ordem'], [$ordem]), ['id']),
                ['ordem' => Ferramentas::array_index(Ferramentas::array_pesquisa_mult($prioridade_data, ['id'], [$id]), ['ordem'])]
              );
            }
            $date = [
              'nome' => Ferramentas::norma_lizar_str($prioridade),
              'cor' => ($cor),
              'ordem' => $ordem
            ];
            $db->update($id, $date);
            $ok = true;
          } else if (count(Ferramentas::array_pesquisa_mult($prioridade_data, ['id', 'nome', 'cor', 'ordem'], [$id, Ferramentas::codificador($prioridade), Ferramentas::codificador($cor), $ordem])) != 0) {
            $msg["Modificar"] = 'Nenhum item foi modificado.';
          } else {
            $msg["Prioridade"] = 'Nome da Prioridade já existente';
            $violacao[] = "prioridade_update Nome da Prioridade já existente";
          }


        }
      } else {
        $msg["Ordem"] = 'Ordem não cadastrada';
        $violacao[] = "prioridade_update Ordem não cadastrada";
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
   * Obtém uma lista de ordens das prioridades a partir de dados armazenados em um banco de dados.
   *
   * Essa função é acionada via AJAX para recuperar a lista de ordens das prioridades a partir dos dados armazenados em um banco de dados.
   *
   * Resposta JSON contendo a lista de ordens das prioridades.
   */
  function lista_ordem()
  {
    if ($this->request->isAJAX()) {
      $prioridade = new \App\Models\Prioridade(); //pega do banco a tabela

      $prioridade_data = $prioridade->find();
      $lista = array();
      foreach ($prioridade_data as $key => $value) { //cria a lista 
        $lista[] = Ferramentas::decodificador($value['ordem']);
      }
      $data = ['lista' => $lista];
      return $this->response->setJSON($data);
    }
  }



    /**
   * Função prioridade_lista()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por listar as prioridades ativas e seus detalhes, como a cor e o nome da prioridade.
   *
   * Retorna um JSON contendo a lista de prioridades ativas e seus detalhes.
   */
  function prioridade_lista()
  {
    if ($this->request->isAJAX()) {
      // Inicializa a sessão para acessar os dados da lista armazenados nela
      session_start();

      $prioridade = new \App\Models\Prioridade(); // Obtém a tabela de prioridades do banco

      $prioridade_data = $prioridade->find();
      $lista = array();



      foreach ($prioridade_data as $key => $value) {
        // Cria a lista com base nas prioridades ativas


        if ($value['status'] == 'ativo') {
          $temp['cor'] = Ferramentas::decodificador($value['cor']);
          $temp['prioridade'] = Ferramentas::decodificador($value['nome']);

          $lista[] = $temp;
        }

      }

      //retorna a lista para o ajax
      $data = [
        "lista" => $lista


      ];

      return $this->response->setJSON($data);
    }
  }



}
