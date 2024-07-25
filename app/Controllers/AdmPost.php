<?php
namespace App\Controllers;


use App\Controllers\Ferramentas;

use Mpdf\Mpdf;

class AdmPost extends Ferramentas
{

  /**
   * Função lista_corte()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por listar os usuários com status de 'corte' ou 'cortando' em uma tabela.
   *
   * Retorna um JSON contendo a lista de usuários com status de 'corte' ou 'cortando'.
   */
  function lista_corte() //rece um post via ajax pedindo para listar os usuarios
  {
    if ($this->request->isAJAX()) {
      session_start();
      // Inicialização de objetos para acessar tabelas do banco de dados
      $desenhos = new \App\Models\Desenhos();
      $prioridade = new \App\Models\Prioridade();
      $finalidade = new \App\Models\Finalidade();
      $empresa = new \App\Models\Empresa();
      $empreendimento = new \App\Models\Empreendimentos();
      $usuario = new \App\Models\Usuarios();
      // Recupera dados das tabelas do banco de dados
      $prioridade_data = $prioridade->find();
      $finalidade_data = $finalidade->find();
      $empresa_data = $empresa->find();
      $empreendimento_data = $empreendimento->find();
      $desenhos_data = $desenhos->find();
      $usuario_data = $usuario->find();

      $check = service('request')->getPost('check'); // Obtém a informação POST fornecida via AJAX para listar usuários ativos


      $lista = "";
      $id_temp = 0;
      $lista_ids = array();
      $lista_completa = array();

      if ($check != null) {
        $alteracao = new \App\Models\Alteracoes();
        $alteracao_data = $alteracao->where('item', 'som_corte')
          ->orderBy('id', 'DESC')
          ->first();

        if ($alteracao_data) {
          if ($check != $alteracao_data["depois"]) {

            $data = [
              "individuo" => $_SESSION["usuario"],
              "id_item" => $alteracao_data["id"],
              "antes" => $alteracao_data["depois"],
              "depois" => $check,
              "item" => "som_corte",
              "info_mais" => "se vai sari som para o cortador",
              "data_add" => Ferramentas::codificador(date('d/m/Y H:i'))

            ];
            $alteracao->insert($data);
          }
          $check = $alteracao_data["depois"];
        } else {
          $data = [
            "individuo" => $_SESSION["usuario"],
            "id_item" => "",
            "antes" => "",
            "depois" => $check,
            "item" => "som_corte",
            "info_mais" => "se vai sari som para o cortador",
            "data_add" => Ferramentas::codificador(date('d/m/Y H:i'))

          ];
          $alteracao->insert($data);
        }





      }

      // Itera sobre os dados de desenhos para criar a lista
      foreach ($desenhos_data as $key => $value) {
        $tags = explode('/', Ferramentas::decodificador($value['caminho']));
        // Remover os índices de 0 a 5
        $tags = array_slice($tags, 6);

        // Remover o último elemento
        unset($tags[count($tags) - 1]);
        $tags = implode(" - ", $tags);
        if (Ferramentas::decodificador($value['status']) == "corte" || Ferramentas::decodificador($value['status']) == 'cortando') {
          $prioridade_desenho = Ferramentas::array_pesquisa($prioridade_data, 'id', $value['prioridade']);

          // Monta a linha da tabela com os dados do usuário
          if (Ferramentas::decodificador($value['status']) == 'corte') {
            $lista .= '<tr><td onclick="prio_modal(' . $id_temp . ')" bgcolor="' . Ferramentas::decodificador(Ferramentas::array_index($prioridade_desenho, ['cor'])) . '"><span class="marca_texto">' . Ferramentas::array_index($prioridade_desenho, ['nome']) . '</span></td>';
          } else {
            $lista .= '<tr><td bgcolor="' . Ferramentas::decodificador(Ferramentas::array_index($prioridade_desenho, ['cor'])) . '"><span class="marca_texto">' . Ferramentas::array_index($prioridade_desenho, ['nome']) . '</span></td>';
          }
          $lista .= '
      

       
          <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['desenhista']), ['nome'])) . '</td>
       <td>' . Ferramentas::decodificador(Ferramentas::remove_id_file(Ferramentas::decodificador($value['nome']))) . '</td>
       
       <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ['nome'])) . '</td>
       <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ['nome'])) . '</td>
       <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ['nome'])) . '</td>
       <td>' . $tags . '</td>
       <td>' . Ferramentas::decodificador(Ferramentas::decodificador($value['status'])) . '</td>
       <td>' . Ferramentas::decodificador(Ferramentas::decodificador($value['data_hora_add'])) . '</td>
      ';
          if (Ferramentas::decodificador($value['status']) == 'corte') {
            $lista .= '<td><button name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="apagar(' . $id_temp . ')"> Apagar </button></td> </tr>';
          } else if (Ferramentas::decodificador($value['status']) == 'cortando') {
            $lista .= '<td><button name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="cancelar_corte(' . $id_temp . ')"> Cancelar corte </button></td> </tr>';
          } else {
            $lista .= '<td></td></tr>';
          }
          // Prepara dados do usuário para armazenamento em arrays
          $value['nome'] = Ferramentas::decodificador($value['nome']);
          $value['cor'] = Ferramentas::decodificador($prioridade_desenho['cor']);
          $value['finalidade'] = Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ["nome"]);
          $value['empresa'] = Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ["nome"]);
          $value['empreendimento'] = Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ["nome"]);
          $value['data_hora_add'] = Ferramentas::decodificador($value['data_hora_add']);
          $value['prioridade'] = Ferramentas::decodificador($prioridade_desenho['nome']);

          $lista_ids[$id_temp] = $value['id'];
          $value['id'] = $id_temp;
          $lista_completa[$id_temp] = $value;
          $id_temp++;
        }
      }

      // Inicializa a sessão e armazena as listas

      $_SESSION["lista"] = $lista_ids;
      $_SESSION["lista_completa"] = $lista_completa;
      $_SESSION["lista_primordial"] = $lista_completa;

      // Resposta do AJAX que retorna a lista de usuários
      $data = [
        "lista" => $lista,
        'check' => $check
      ];

      return $this->response->setJSON($data);
    }
  }

  /**
   * Função desenho_modal()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por retornar a lista completa de usuários, armazenada em uma sessão, para ser utilizada em um modal ou em outro contexto na interface do usuário.
   *
   * Retorna um JSON contendo a lista completa de usuários armazenada na sessão.
   */
  function desenho_modal()
  {
    if ($this->request->isAJAX()) {
      // Inicializa a sessão para acessar os dados da lista armazenados nela
      session_start();
      $id = service('request')->getPost('id');
      if ($id == "") {
        $data = [
          "lista" => $_SESSION["lista_completa"]


        ];
      } else {
        $data = [
          "lista" => [Ferramentas::array_pesquisa($_SESSION["lista_completa"], 'id', $id)]


        ];
      }

      // Resposta do AJAX que retorna a lista completa de usuários
      return $this->response->setJSON($data);
    }
  }


  /**
   * Função desenho_meus()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por listar os usuários com base em diferentes critérios, como a data de adição e o status. Além disso, a função possibilita ações como adicionar novamente desenhos e exibir informações detalhadas sobre eles.
   *
   * Retorna um JSON contendo a lista de usuários com base nos critérios definidos.
   */
  function desenho_meus()
  {
    if ($this->request->isAJAX()) {
      // Inicializa a sessão para acessar os dados da lista armazenados nela
      session_start();

      // Instancia tabelas do banco de dados
      $desenhos = new \App\Models\Desenhos();
      $prioridade = new \App\Models\Prioridade();
      $finalidade = new \App\Models\Finalidade();
      $empresa = new \App\Models\Empresa();
      $empreendimento = new \App\Models\Empreendimentos();

      // Recupera dados das tabelas do banco de dados
      $prioridade_data = $prioridade->find();
      $finalidade_data = $finalidade->find();
      $empresa_data = $empresa->find();
      $empreendimento_data = $empreendimento->find();
      $desenhos_data = $desenhos->find();

      $lista = "";
      $id_temp = 0;
      $lista_ids = array();
      $lista_completa = array();

      // Recupera dados da solicitação AJAX
      $dataInicial = service('request')->getPost('data');
      $dataFinal = service('request')->getPost('data1');

      // Converte datas em timestamps para comparação
      $dataInicialTimestamp = strtotime($dataInicial);
      $dataFinalTimestamp = strtotime($dataFinal);

      // Garante que a data inicial seja menor que a data final
      if ($dataInicialTimestamp > $dataFinalTimestamp) {
        list($dataInicialTimestamp, $dataFinalTimestamp) = array($dataFinalTimestamp, $dataInicialTimestamp);
      }

      $dias = array();

      // Gera uma lista de datas entre a data inicial e a data final
      while ($dataInicialTimestamp <= $dataFinalTimestamp) {
        $dias[] = date("d/m/Y", $dataInicialTimestamp);
        $dataInicialTimestamp = strtotime("+1 day", $dataInicialTimestamp);
      }




      foreach ($desenhos_data as $key => $value) { //cria a lista
        $tags = explode('/', Ferramentas::decodificador($value['caminho']));
        // Remover os índices de 0 a 5
        $tags = array_slice($tags, 6);

        // Remover o último elemento
        unset($tags[count($tags) - 1]);
        $tags = implode(" - ", $tags);


        $_SESSION["lista_primordial"][$id_temp] = $value;



        // Verifica se a data de adição está dentro do intervalo de datas especificado
        $encontrou = false;
        foreach ($dias as $data) {
          if (strpos(Ferramentas::decodificador($value['data_hora_add']), $data) !== false) {
            $encontrou = true;
            break; // Se encontrou, não precisa continuar verificando
          }
        }


        if ($value['desenhista'] == $_SESSION['usuario'] and $encontrou) { // pega apenas os desenhos do desenhista que esta vendo
          $prioridade_desenho = Ferramentas::array_pesquisa($prioridade_data, 'id', $value['prioridade']);
          if (Ferramentas::decodificador($value['status']) == 'corte') {
            //<button name="cadastarar" type="submit" onclick="subistituir_desenho_modal(\''. $id_temp .'\')" class="btn btn-outline-primary"Renomear/Substituir/button>

            // Cria a linha da tabela para desenhos com status 'corte'
            $lista .= '
      <tr>

       
       <td  bgcolor="' . Ferramentas::decodificador($prioridade_desenho['cor']) . '"><span onclick="prio_modal(\'' . $id_temp . '\')" class="marca_texto">' . Ferramentas::decodificador($prioridade_desenho['nome']) . '</span></td>
       <td onclick="prio_modal(\'' . $id_temp . '\')">' . Ferramentas::decodificador(Ferramentas::remove_id_file(Ferramentas::decodificador($value['nome']))) . '</td>
       <td onclick="prio_modal(\'' . $id_temp . '\')">' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ["nome"])) . '</td>
       <td onclick="prio_modal(\'' . $id_temp . '\')">' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ["nome"])) . '</td>
       <td onclick="prio_modal(\'' . $id_temp . '\')">' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ["nome"])) . '</td>
       <td onclick="prio_modal(\'' . $id_temp . '\')">' . $tags . '</td>
       <td onclick="prio_modal(\'' . $id_temp . '\')">' . Ferramentas::decodificador($value['status']) . '</td>
       <td onclick="prio_modal(\'' . $id_temp . '\')">' . Ferramentas::decodificador($value['data_hora_add']) . '</td>
       <td><button name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="apagar(' . $id_temp . ')"> Apagar </button></td>
       <td><button name="cadastarar" type="submit" onclick="subistituir_desenho_modal(\'' . $id_temp . '\')" class="btn btn-outline-primary">Renomear/Substituir</button></td>
      </tr>
      ';

            // Prepara dados do usuário para armazenamento em arrays
            $value['nome'] = Ferramentas::decodificador($value['nome']);
            $value['cor'] = Ferramentas::decodificador($prioridade_desenho['cor']);
            $value['finalidade'] = Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ["nome"]);
            $value['empresa'] = Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ["nome"]);
            $value['empreendimento'] = Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ["nome"]);
            $value['data_hora_add'] = Ferramentas::decodificador($value['data_hora_add']);
            $value['prioridade'] = Ferramentas::decodificador($prioridade_desenho['nome']);
            $lista_ids[$id_temp] = $value['id'];
            $value['id'] = $id_temp;
            $lista_completa[$id_temp] = $value;
            $id_temp++;
          } else if (Ferramentas::decodificador($value['status']) == 'cortado') {
            if (!file_exists(Ferramentas::decodificador($value['caminho']))) {
              $value['status'] = "cortado_notfile";

              $desenhos->update($value['id'], $value);

            }
            //<button name="cadastarar" type="submit" class="btn btn-outline-primary "> adicionar <br> novamente </button>
            // Cria a linha da tabela para desenhos com status 'cortado'
            $lista .= '
      <tr>

       
       <td  bgcolor="' . Ferramentas::decodificador($prioridade_desenho['cor']) . '"><span class="marca_texto">' . Ferramentas::decodificador($prioridade_desenho['nome']) . '</span></td>
       <td>' . Ferramentas::remove_id_file(substr(Ferramentas::decodificador($value['nome']), 19)) . '</td>
       <td>' . Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ["nome"]) . '</td>
       <td>' . Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ["nome"]) . '</td>
       <td>' . Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ["nome"]) . '</td>
       <td>' . $tags . '</td>
       <td>' . Ferramentas::decodificador($value['status']) . '</td>
       <td>' . Ferramentas::decodificador($value['data_hora_add']) . '</td>
       <td><button name="cadastarar" onclick="recolocar_desenho(\'' . $id_temp . '\')" type="submit" class="btn btn-outline-primary "> adicionar <br> novamente </button></td>
       <td><button name="cadastarar" onclick="recolocar_desenho(\'' . $id_temp . '\')" type="submit" class="btn btn-outline-primary "> adicionar <br> novamente </button></td>
      </tr>
      ';
            // Prepara dados do usuário para armazenamento em arrays
            $value['nome'] = Ferramentas::decodificador($value['nome']);
            $value['cor'] = Ferramentas::decodificador($prioridade_desenho['cor']);
            $value['finalidade'] = Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ["nome"]);
            $value['empresa'] = Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ["nome"]);
            $value['empreendimento'] = Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ["nome"]);
            $value['data_hora_add'] = Ferramentas::decodificador($value['data_hora_add']);
            $value['prioridade'] = Ferramentas::decodificador($prioridade_desenho['nome']);
            $lista_ids[$id_temp] = $value['id'];
            $value['id'] = $id_temp;
            $lista_completa[$id_temp] = $value;
            $id_temp++;

          } else if (Ferramentas::decodificador($value['status']) == 'cortando') {
            // Cria a linha da tabela para desenhos com status 'cortando'
            $lista .= '
      <tr>

       
       <td bgcolor="' . Ferramentas::decodificador($prioridade_desenho['cor']) . '"><span class="marca_texto">' . Ferramentas::decodificador($prioridade_desenho['nome']) . '</span></td>
       <td>' . Ferramentas::remove_id_file(Ferramentas::decodificador($value['nome'])) . '</td>
       <td>' . Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ["nome"]) . '</td>
       <td>' . Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ["nome"]) . '</td>
       <td>' . Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ["nome"]) . '</td>
       <td>' . $tags . '</td>
       <td>' . Ferramentas::decodificador($value['status']) . '</td>
       <td>' . Ferramentas::decodificador($value['data_hora_add']) . '</td>
       <td><button name="cadastarar" type="submit" disabled class="btn btn-outline-dark"> Cortando... </button></td>
       <td><button name="cadastarar" type="submit" disabled class="btn btn-outline-dark"> Cortando... </button></td>
      </tr>
      ';
          }
        }
      }

      $_SESSION["lista"] = $lista_ids;
      $_SESSION["lista_completa"] = $lista_completa;


      // Resposta do AJAX que retorna a lista e as datas
      $data = [
        "lista" => $lista,
        "data" => $dias

      ];

      return $this->response->setJSON($data);
    }
  }
  /**
   * Função desenho_meus()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por listar os usuários com base em diferentes critérios, como a data de adição e o status. Além disso, a função possibilita ações como adicionar novamente desenhos e exibir informações detalhadas sobre eles.
   *
   * Retorna um JSON contendo a lista de usuários com base nos critérios definidos.
   */
  function desenhos_cortados()
  {
    if ($this->request->isAJAX()) {
      // Inicializa a sessão para acessar os dados da lista armazenados nela
      session_start();

      // Instancia tabelas do banco de dados
      $desenhos = new \App\Models\Desenhos();
      $prioridade = new \App\Models\Prioridade();
      $finalidade = new \App\Models\Finalidade();
      $empresa = new \App\Models\Empresa();
      $empreendimento = new \App\Models\Empreendimentos();
      $usuario = new \App\Models\Usuarios();

      // Recupera dados das tabelas do banco de dados
      $prioridade_data = $prioridade->find();
      $finalidade_data = $finalidade->find();
      $empresa_data = $empresa->find();
      $empreendimento_data = $empreendimento->find();
      $desenhos_data = $desenhos->find();
      $usuario_data = $usuario->find();
      $lista = "";
      $id_temp = 0;
      $lista_ids = array();
      $lista_completa = array();

      // Recupera dados da solicitação AJAX
      $dataInicial = service('request')->getPost('data');
      $dataFinal = service('request')->getPost('data1');

      // Converte datas em timestamps para comparação
      $dataInicialTimestamp = strtotime($dataInicial);
      $dataFinalTimestamp = strtotime($dataFinal);

      // Garante que a data inicial seja menor que a data final
      if ($dataInicialTimestamp > $dataFinalTimestamp) {
        list($dataInicialTimestamp, $dataFinalTimestamp) = array($dataFinalTimestamp, $dataInicialTimestamp);
      }

      $dias = array();

      // Gera uma lista de datas entre a data inicial e a data final
      while ($dataInicialTimestamp <= $dataFinalTimestamp) {
        $dias[] = date("d/m/Y", $dataInicialTimestamp);
        $dataInicialTimestamp = strtotime("+1 day", $dataInicialTimestamp);
      }




      foreach ($desenhos_data as $key => $value) { //cria a lista
        $_SESSION["lista_primordial"][$id_temp] = $value;



        // Verifica se a data de adição está dentro do intervalo de datas especificado
        $encontrou = false;
        foreach ($dias as $data) {
          if (strpos(Ferramentas::decodificador($value['data_hora_add']), $data) !== false) {
            $encontrou = true;
            break; // Se encontrou, não precisa continuar verificando
          }
        }


        if ($encontrou) { // pega apenas os desenhos do desenhista que esta vendo
          $prioridade_desenho = Ferramentas::array_pesquisa($prioridade_data, 'id', $value['prioridade']);
          if (Ferramentas::decodificador($value['status']) == 'cortado') {
            if (!file_exists(Ferramentas::decodificador($value['caminho']))) {
              $value['status'] = "cortado_notfile";

              $desenhos->update($value['id'], $value);

            }
            //<button name="cadastarar" type="submit" class="btn btn-outline-primary "> adicionar <br> novamente </button>
            // Cria a linha da tabela para desenhos com status 'cortado'
            $lista .= '
      <tr>

       
       <td  bgcolor="' . Ferramentas::decodificador($prioridade_desenho['cor']) . '"><span class="marca_texto">' . Ferramentas::decodificador($prioridade_desenho['nome']) . '</span></td>
       <td>' . Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['desenhista']), ['nome']) . '</td>

       <td>' . Ferramentas::remove_id_file(substr(Ferramentas::decodificador($value['nome']), 19)) . '</td>
       <td>' . Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ["nome"]) . '</td>
       <td>' . Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ["nome"]) . '</td>
       <td>' . Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ["nome"]) . '</td>
       <td>' . Ferramentas::decodificador($value['status']) . '</td>
       <td>' . Ferramentas::decodificador($value['data_hora_add']) . '</td>
       <td><button name="cadastarar" onclick="recolocar_desenho(\'' . $id_temp . '\')" type="submit" class="btn btn-outline-primary "> adicionar <br> novamente </button></td>
       <td><button name="cadastarar" onclick="recolocar_desenho(\'' . $id_temp . '\')" type="submit" class="btn btn-outline-primary "> adicionar <br> novamente </button></td>
      </tr>
      ';
            // Prepara dados do usuário para armazenamento em arrays
            $value['nome'] = Ferramentas::decodificador($value['nome']);
            $value['cor'] = Ferramentas::decodificador($prioridade_desenho['cor']);
            $value['finalidade'] = Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ["nome"]);
            $value['empresa'] = Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ["nome"]);
            $value['empreendimento'] = Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ["nome"]);
            $value['data_hora_add'] = Ferramentas::decodificador($value['data_hora_add']);
            $value['prioridade'] = Ferramentas::decodificador($prioridade_desenho['nome']);
            $lista_ids[$id_temp] = $value['id'];
            $value['id'] = $id_temp;
            $lista_completa[$id_temp] = $value;
            $id_temp++;
          }
        }
      }

      $_SESSION["lista"] = $lista_ids;
      $_SESSION["lista_completa"] = $lista_completa;


      // Resposta do AJAX que retorna a lista e as datas
      $data = [
        "lista" => $lista,
        "data" => $dias

      ];

      return $this->response->setJSON($data);
    }
  }




  /**
   * Função desenho_meus_modal()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por retornar informações detalhadas sobre um desenho específico a ser exibido em um modal ou em outro contexto na interface do usuário.
   *
   * Retorna um JSON contendo a lista completa de usuários e informações detalhadas sobre um desenho específico com base no ID fornecido.
   */
  function desenho_meus_modal()
  {
    if ($this->request->isAJAX()) {
      // Inicializa a sessão para acessar os dados da lista armazenados nela
      session_start();

      // Obtém o ID do desenho a ser exibido no modal
      $id = service('request')->getPost('id');
      if ($id != "") {
        $data = [
          "lista" => [Ferramentas::array_pesquisa($_SESSION["lista_completa"], 'id', $id)]


        ];
      } else {
        $data = [
          "lista" => $_SESSION["lista_completa"]


        ];
      }

      // Resposta do AJAX que retorna a lista completa de usuários e informações detalhadas sobre um desenho específico
      return $this->response->setJSON($data);
    }
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


      $ok = false; // Variável para indicar se a operação foi bem-sucedida
      $violacao = array(); // Array para armazenar informações de violações

      if ($prioridade_nova != '') {
        // Se a nova prioridade não estiver vazia, prossiga com a atualização
        $prioridade = new \App\Models\Prioridade(); // Obtém a tabela de prioridades do banco
        $ok = true; // Define que a operação está OK

        $prioridade_data = $prioridade->find();
        $id_prio = Ferramentas::array_pesquisa($prioridade_data, 'nome', Ferramentas::codificador($prioridade_nova))['id'];

        $desenhos = new \App\Models\Desenhos(); // Obtém a tabela de desenhos do banco
        foreach ($array as $key => $value) {
          // Para cada ID no array de IDs, registre as alterações em um log
          $ids = $_SESSION["lista"][$value];
          $alteracao = new \App\Models\Alteracoes();
          $lista_temp = new \App\Models\Historico_desenhos();

          $data = [
            "individuo" => $_SESSION["usuario"],
            "id_item" => $ids,
            "antes" => $_SESSION["lista_completa"][$value]['prioridade'],
            "depois" => $id_prio,
            "item" => "desenho",
            "info_mais" => "prioridade",
            "data_add" => Ferramentas::codificador(date('d/m/Y H:i'))

          ];
          $alteracao->insert($data);

          $data = [
            "prioridade" => $id_prio

          ];
          $desenhos->update($ids, $data);
          $data = [
            'id_desenhos' => $ids,
            'data_hora_mod' => Ferramentas::codificador(date('d/m/Y H:i:s')),
            'status' => Ferramentas::codificador('mudança de prioridade')
          ];
          $lista_temp->insert($data);

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
            "individuo" => $_SESSION["usuario"],
            "causa" => $value,
            "data" => Ferramentas::codificador(date('d/m/Y H:i'))

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
   * Função config_tipo_de_arquivo()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por listar os tipos de arquivo com base em critérios específicos, como ativos ou desativados. Também oferece a opção de ativar ou desativar tipos de arquivo.
   *
   * Retorna um JSON contendo a lista de tipos de arquivo de acordo com os critérios fornecidos, bem como a capacidade de ativar ou desativar tipos de arquivo.
   */
  function config_tipo_de_arquivo()
  {
    if ($this->request->isAJAX()) {
      // Inicializa a sessão para acessar os dados da lista armazenados nela
      session_start();

      $filtros = new \App\Models\Filtros(); // Obtém a tabela de tipos de arquivo do banco

      $filtros_data = $filtros->find();
      $ativos = service('request')->getPost('ativos'); // Obtém a informação post que foi fornecida via AJAX para listar tipos de arquivo ativos
      $desativados = service('request')->getPost('desativados'); // Obtém a informação post que foi fornecida via AJAX para listar tipos de arquivo desativados

      $lista = "";
      $lista_ids = array();
      $id_temp = 0;
      $lista_completa = array();

      foreach ($filtros_data as $key => $value) {
        // Cria a lista com base nos critérios especificados
        if (($ativos == 'true' && Ferramentas::decodificador($value['status']) == 'ativo')) { //verifica se é para mostrar os com estus ativo
          $lista .= '
      <tr>
       <td><p onclick="modal_modificar(\'modal_' . $id_temp . '\')">.' . Ferramentas::decodificador($value['nome']) . '</p></td>
       <td onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
       <td><button name="cadastarar" type="submit" onclick="desativar(\'' . $id_temp . '\')" class="btn btn-outline-danger btn-lg btn-block"> Desativar </button></td>
      </tr>
      ';
        } else if (($desativados == 'true' && Ferramentas::decodificador($value['status']) == 'desativado')) { //verifica se é para mostrar os com estus desativado
          $lista .= '
      <tr>
       <td><p onclick="modal_modificar(\'modal_' . $id_temp . '\')">.' . Ferramentas::decodificador($value['nome']) . '</p></td>
       <td onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
       <td><button name="cadastarar" type="submit" onclick="ativar(\'' . $id_temp . '\')" class="btn btn-outline-success btn-lg btn-block"> Ativar </button></td>
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
   * Função config_prioridade()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por listar as prioridades com base em critérios específicos, como ativas ou desativadas. Também oferece a opção de ativar ou desativar prioridades.
   *
   * Retorna um JSON contendo a lista de prioridades de acordo com os critérios fornecidos, bem como a capacidade de ativar ou desativar prioridades.
   */
  function config_prioridade()
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
       <td><p onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['nome']) . '</p></td>
       <td onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['ordem']) . '</td>
       <td onclick="modal_modificar(\'modal_' . $id_temp . '\')" bgcolor="' . Ferramentas::decodificador($value['cor']) . '"><span class="marca_texto">' . Ferramentas::decodificador($value['cor']) . '</span></td>
       <td>' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
       <td><button name="cadastarar" type="submit" onclick="desativar(\'' . $id_temp . '\')" class="btn btn-outline-danger btn-lg btn-block"> Desativar </button></td>
      </tr>
      ';
        } else if (($desativados == 'true' && Ferramentas::decodificador($value['status']) == 'desativado')) { //verifica se é para mostrar os com estus desativado
          $lista .= '
      <tr>
      <td><p onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['nome']) . '</p></td>
      <td onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['ordem']) . '</td>
      <td onclick="modal_modificar(\'modal_' . $id_temp . '\')" bgcolor="' . Ferramentas::decodificador($value['cor']) . '"><span class="marca_texto">' . Ferramentas::decodificador($value['cor']) . '</span></td>
       <td>' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
       <td><button name="cadastarar" type="submit" onclick="ativar(\'' . $id_temp . '\')" class="btn btn-outline-success btn-lg btn-block"> Ativar </button></td>
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
   * Função config_prioridade_lista()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por listar as prioridades ativas e seus detalhes, como a cor e o nome da prioridade.
   *
   * Retorna um JSON contendo a lista de prioridades ativas e seus detalhes.
   */
  function config_prioridade_lista()
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
        "lista" => $lista,


      ];

      return $this->response->setJSON($data);
    }
  }




  /**
   * Função config_finalidade()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por listar as finalidades (propósitos) ativas e seus detalhes, como o nome e o status da finalidade.
   *
   * Retorna um JSON contendo a lista de finalidades ativas e seus detalhes.
   */
  function config_finalidade() //rece um post via ajax pedindo para listar os usuarios
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
       <td><p onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['nome']) . '</p></td>
       <td onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
       <td><button name="cadastarar" type="submit" onclick="desativar(\'' . $id_temp . '\')" class="btn btn-outline-danger btn-lg btn-block"> Desativar </button></td>
      </tr>
      ';
        } else if (($desativados == 'true' && Ferramentas::decodificador($value['status']) == 'desativado')) { //verifica se é para mostrar os com estus desativado
          $lista .= '
      <tr>
       <td><p onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['nome']) . '</p></td>
       <td onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
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
   * Função config_empresa_cliente()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por listar as empresas/clientes ativos e seus detalhes, como o nome e o status.
   *
   * Retorna um JSON contendo a lista de empresas/clientes ativos e seus detalhes.
   */
  function config_empresa_cliente()
  {
    if ($this->request->isAJAX()) {
      // Inicializa a sessão para acessar os dados da lista armazenados nela
      session_start();

      $empresa = new \App\Models\Empresa(); // Obtém a tabela de empresas/clientes do banco

      $empresa_data = $empresa->find();
      $ativos = service('request')->getPost('ativos'); // Obtém a informação POST fornecida via AJAX para listar empresas/clientes ativos
      $desativados = service('request')->getPost('desativados'); // Obtém a informação POST fornecida via AJAX para listar empresas/clientes desativados
      $lista = "";
      $lista_ids = array();
      $id_temp = 0;
      $lista_completa = array();

      foreach ($empresa_data as $key => $value) {
        // Cria a lista com base nas empresas/clientes ativas ou desativadas, dependendo da solicitação
        if (($ativos == 'true' && Ferramentas::decodificador($value['status']) == 'ativo')) { //verifica se é para mostrar os com estus ativo
          $lista .= '
      <tr>
       <td><p onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['nome']) . '</p></td>
       <td onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
       <td><button name="cadastarar" type="submit" onclick="desativar(\'' . $id_temp . '\')" class="btn btn-outline-danger btn-lg btn-block"> Desativar </button></td>
      </tr>
      ';
        } else if (($desativados == 'true' && Ferramentas::decodificador($value['status']) == 'desativado')) { //verifica se é para mostrar os com estus desativado
          $lista .= '
      <tr>
       <td><p onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['nome']) . '</p></td>
       <td onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
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
   * Função config_empreendimento()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por listar empreendimentos ativos e seus detalhes, como o nome, empresa relacionada e status.
   *
   * Retorna um JSON contendo a lista de empreendimentos ativos e seus detalhes.
   */
  function config_empreendimento() //rece um post via ajax pedindo para listar os usuarios
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
       <td><p onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['nome']) . '</p></td>
       <td onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa_id']), ['nome']) . '</td>
       <td onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
       <td><button name="cadastarar" type="submit" onclick="desativar(\'' . $id_temp . '\')" class="btn btn-outline-danger btn-lg btn-block"> Desativar </button></td>
      </tr>
      ';
        } else if (($desativados == 'true' && Ferramentas::decodificador($value['status']) == 'desativado')) { //verifica se é para mostrar os com estus desativado
          $lista .= '
      <tr>
      <td><p onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['nome']) . '</p></td>
      <td onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa_id']), ['nome']) . '</td>
       <td onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
       <td><button name="cadastarar" type="submit" onclick="ativar(\'' . $id_temp . '\')" class="btn btn-outline-success btn-lg btn-block"> Ativar </button></td>
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
   * Função user_modificar()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por listar usuários e seus detalhes, como nome, função, email, WhatsApp e status. Ela também fornece a opção de ativar ou desativar os usuários com base em uma solicitação.
   *
   * @Retorna um JSON contendo a lista de usuários e seus detalhes.
   */
  function user_modificar()
  {
    if ($this->request->isAJAX()) {
      // Inicializa a sessão para acessar os dados da lista armazenados nela
      session_start();

      $usuarios = new \App\Models\Usuarios(); // Obtém a tabela de usuários do banco
      $funcao = new \App\Models\Funcao(); // Obtém a tabela de funções do banco
      $usuarios_data = $usuarios->find();
      $funcao_data = $funcao->find();

      $ativos = service('request')->getPost('ativos'); // Obtém a informação POST fornecida via AJAX para listar usuários ativos
      $desativados = service('request')->getPost('desativados'); // Obtém a informação POST fornecida via AJAX para listar usuários desativados
      $lista = "";
      $lista_ids = array();
      $lista_completa = array();
      $id_temp = 0;

      foreach ($usuarios_data as $key => $value) {
        // Cria a lista com base nos usuários ativos ou desativados, dependendo da solicitação
        if (($ativos == 'true' && Ferramentas::decodificador($value['status']) == 'ativo')) { //verifica se é para mostrar os com estus ativo//verifica se é para mostrar os com estus ativo
          $lista .= '
      <tr id="" >
       <td><p onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['nome']) . '</p></td>
       <td onclick="modal_modificar(\'modal_' . $id_temp . '\')">  ********  </td>
       <td onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($funcao_data, 'id', $value['tipo']), ['nome'])) . '</td>
       <td onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['email']) . '</td>
       <td onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['whatsapp']) . '</td>
       
 
       <td onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
       <td><button name="cadastarar" type="submit" onclick="desativar(\'' . $id_temp . '\')" class="btn btn-outline-danger btn-lg btn-block"> Desativar </button></td>
      </tr>
      ';
        } else if (($desativados == 'true' && Ferramentas::decodificador($value['status']) == 'desativado')) { //verifica se é para mostrar os com estus desativado
          $lista .= '
          <td><p onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['nome']) . '</p></td>
          <td onclick="modal_modificar(\'modal_' . $id_temp . '\')">  ********  </td>
          <td onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($funcao_data, 'id', $value['tipo']), ['nome'])) . '</td>
          <td onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['email']) . '</td>
          <td onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['whatsapp']) . '</td>
          
       <td onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
       <td><button name="cadastarar" type="submit" onclick="ativar(\'' . $id_temp . '\')" on class="btn btn-outline-success btn-lg btn-block"> Ativar </button></td>
      </tr>
      ';
        }
        $value['tipo'] = Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($funcao_data, 'id', $value['tipo']), ['nome']));
        $lista_ids[$id_temp] = $value['id'];
        $lista_completa[$id_temp] = $value;
        $id_temp++;

      }
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
   * Função user_modificar_modal()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por buscar e retornar os detalhes de um usuário específico com base em um ID fornecido na solicitação.
   *
   * Retorna um JSON contendo os detalhes do usuário, incluindo nome, senha, função, email, WhatsApp e status.
   */
  function user_modificar_modal()
  {
    if ($this->request->isAJAX()) {
      // Inicializa a sessão para acessar os dados da lista armazenados nela
      session_start();


      $id = service('request')->getPost('id'); // Obtém o ID fornecido na solicitação AJAX para buscar os detalhes do usuário
      $lista = $_SESSION["lista_completa"][$id];


      // Retorna os detalhes do usuário em um formato JSON
      $data = [
        "nome" => Ferramentas::decodificador($lista['nome']),
        "senha" => Ferramentas::decodificador($lista['senha']),
        "tipo" => Ferramentas::decodificador($lista['tipo']),
        "email" => Ferramentas::decodificador($lista['email']),
        "whatsapp" => Ferramentas::decodificador($lista['whatsapp']),
        "status" => Ferramentas::decodificador($lista['status'])


      ];

      return $this->response->setJSON($data);
    }
  }



  /**
   * Função user_cadastrar()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por cadastrar um novo usuário no sistema.
   *
   * Retorna um JSON contendo informações sobre o sucesso ou falha do cadastro e mensagens de erro, se aplicável.
   */
  function user_cadastrar()
  {
    if ($this->request->isAJAX()) {
      $msg = array(); // Inicializa um array para armazenar mensagens de erro
      $ok = false; // Inicializa uma variável de sucesso como falsa
      $violacao = array(); // Inicializa um array para armazenar informações sobre violações ou erros

      // Obtém os dados enviados via AJAX
      $nome = service('request')->getPost('nome');
      $senha = service('request')->getPost('senha');
      $funcao = service('request')->getPost('funcao');
      $email = service('request')->getPost('email');
      $whazapp = service('request')->getPost('whazapp');

      // Validações dos dados recebidos
      if (strlen($nome) > 17) {
        $msg['Nome'] = "Nome excedeu o tamanho máximo de 17 caracter";
        $violacao[] = "user_cadastrar Nome excedeu tamanho";
      }

      // Validações dos dados recebidos
      if (strlen($senha) > 50) {
        $msg['Senha'] = "Senha excedeu o tamanho máximo de 50 caracter";
        $violacao[] = "user_cadastrar Senha excedeu tamanho";
      }

      // Validações dos dados recebidos
      if (strlen($email) > 50) {
        $msg['Email'] = "Email excedeu o tamanho máximo de 50 caracter";
        $violacao[] = "user_cadastrar Email excedeu tamanho";
      }

      // Validações dos dados recebidos
      if (strlen($whazapp) > 19) {
        $msg['Whazapp'] = "Whazapp excedeu o tamanho máximo de 15 caracter";
        $violacao[] = "user_cadastrar Whazapp excedeu tamanho";
      }

      // Validações dos dados recebidos
      if (strlen($nome) < 3) {
        $msg['Nome'] = "Nome não possui o tamanho mínimo de 3 caracter";
      } else {
        if (Ferramentas::codificador($nome) == '') {
          $msg['Nome'] = "Nome possui caracteres não permitidos";
          $violacao[] = "user_cadastrar Nome possui caracteres não permitidos";
        }
      }

      // Validações dos dados recebidos
      if (strlen($senha) < 3) {
        $msg['Senha'] = " não possui o tamanho mínimo de 3 caracter";
      } else {
        if (Ferramentas::codificador($senha) == '') {
          $msg['Senha'] = "Senha possui caracteres não permitidos";
          $violacao[] = "user_cadastrar Senha possui caracteres não permitidos";
        }
      }

      // Validações dos dados recebidos
      if (strlen($email) < 13) {
        $msg['Email'] = "Email não possui o tamanho mínimo de 13 caracter";
      } else {
        if (Ferramentas::codificador($email) == '') {
          $msg['Email'] = "Email possui caracteres não permitidos";
          $violacao[] = "user_cadastrar Email possui caracteres não permitidos";
        }
      }

      // Validações dos dados recebidos
      if (strlen($whazapp) < 14) {
        $msg['Whazapp'] = "Whazapp não possui o tamanho mínimo de 13 caracter";
      }

      $lista_array = $this->lita_funcao();
      $lista_array = json_decode($lista_array->getBody(), true);

      // Validações dos dados recebidos
      if (!in_array($funcao, $lista_array['lista'])) {
        $msg['Função'] = "Nome da Função não cadastrado";
        $violacao[] = "user_cadastrar Função não cadastrado";
      } else {
        if (Ferramentas::codificador($funcao) == '') {
          $msg['Função'] = "Função possui caracteres não permitidos";
          $violacao[] = "user_cadastrar Função possui caracteres não permitidos";
        }
      }

      // Validações dos dados recebidos
      $teste = true;
      foreach (str_split($email) as $key => $value) {
        if (in_array($value, ['@'])) {
          $teste = false;
        }
      }
      if ($teste) {
        $msg['Email'] = "Email com nome invalido";
      }

      // Validações dos dados recebidos
      $teste = false;
      foreach (str_split($whazapp) as $key => $value) {
        if (!in_array($value, ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ' ', '(', ')', '-'])) {
          $teste = true;
        }
      }

      // Validações dos dados recebidos
      if ($teste) {
        $msg['Whazapp'] = "Whazapp possui caracteres não permitidos";
        $violacao[] = "user_cadastrar Whazapp possui caracteres não permitidos";
      }
      session_start();
      if (count($msg) == 0) {
        // Inicializa o banco de dados e busca dados relacionados

        $db = new \App\Models\Usuarios();
        $db1 = new \App\Models\Funcao();
        $db1_data = $db1->find();
        $db_data = $db->find();

        if (count(Ferramentas::array_pesquisa($db_data, 'nome', Ferramentas::codificador($nome))) == 0) {
          // Os dados estão corretos, e o usuário pode ser inserido no banco de dados
          $date = [
            'nome' => Ferramentas::codificador($nome),
            'senha' => (Ferramentas::codificador($senha)),
            'tipo' => Ferramentas::array_pesquisa($db1_data, 'nome', Ferramentas::codificador($funcao))['id'],
            'email' => Ferramentas::codificador($email),
            'whatsapp' => str_replace(['(', ')', '-', ' '], [''], $whazapp),
            'data_hora_add' => Ferramentas::codificador(date('d/m/Y H:i')),
            'status' => 'ativo',
            'individuo' => $_SESSION['usuario']

          ];

          $db->insert($date); // Insere o novo usuário no banco de dados
          $ok = true; // O cadastro foi bem-sucedido
        } else {
          $msg["Nome"] = 'Nome de usuário já existente';
          $violacao[] = "user_cadastrar Nome de usuário já existente";
        }


      }

      if (count($violacao) != 0) {
        //$violacao = Ferramentas::array_codificar($violacao);
        // Se houver violações, insira informações sobre as violações no banco de dados
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
   * Função user_modificar_update()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por atualizar os dados de um usuário existente no sistema.
   *
   * Retorna um JSON contendo informações sobre o sucesso ou falha da atualização e mensagens de erro, se aplicável.
   */
  function user_modificar_update()
  {
    if ($this->request->isAJAX()) {
      $msg = array(); // Inicializa um array para armazenar mensagens de erro
      $ok = false; // Inicializa uma variável de sucesso como falsa
      $violacao = array(); // Inicializa um array para armazenar informações sobre violações ou erros

      // Obtém os dados enviados via AJAX
      $nome = service('request')->getPost('nome');
      $senha = service('request')->getPost('senha');
      $funcao = service('request')->getPost('funcao');
      $email = service('request')->getPost('email');
      $whazapp = service('request')->getPost('whazapp');

      // Validações dos dados recebidos
      if (strlen($nome) > 17) {
        $msg['Nome'] = "Nome excedeu o tamanho máximo de 17 caracter";
        $violacao[] = "user_modificar_update Nome excedeu o tamanho máximo";
      }

      // Validações dos dados recebidos
      if (strlen($senha) > 50) {
        $msg['Senha'] = "Senha excedeu o tamanho máximo de 50 caracter";
        $violacao[] = "user_modificar_update Senha excedeu o tamanho máximo";
      }

      // Validações dos dados recebidos
      if (strlen($email) > 50) {
        $msg['Email'] = "Email excedeu o tamanho máximo de 50 caracter";
        $violacao[] = "user_modificar_update Email excedeu o tamanho máximo";
      }

      // Validações dos dados recebidos
      if (strlen($whazapp) > 19) {
        $msg['Whazapp'] = "Whazapp excedeu o tamanho máximo de 15 caracter";
        $violacao[] = "user_modificar_update Whazapp excedeu o tamanho máximo";
      }

      // Validações dos dados recebidos
      if (strlen($nome) < 3) {
        $msg['Nome'] = "Nome não possui o tamanho mínimo de 3 caracter";
      } else {
        if (Ferramentas::codificador($nome) == '') {
          $msg['Nome'] = "Nome possui caracteres não permitidos";
          $violacao[] = "user_modificar_update Nome possui caracteres não permitidos";
        }
      }

      // Validações dos dados recebidos
      if (strlen($senha) < 3) {
        $msg['Senha'] = " não possui o tamanho mínimo de 3 caracter";
      } else {
        if (Ferramentas::codificador($senha) == '') {
          $msg['Senha'] = "Senha possui caracteres não permitidos";
          $violacao[] = "user_modificar_update Senha possui caracteres não permitidos";
        }
      }

      // Validações dos dados recebidos
      if (strlen($email) < 13) {
        $msg['Email'] = "Email não possui o tamanho mínimo de 13 caracter";
      } else {
        if (Ferramentas::codificador($email) == '') {
          $msg['Email'] = "Email possui caracteres não permitidos";
          $violacao[] = "user_modificar_update Email possui caracteres não permitidos";
        }
      }

      // Validações dos dados recebidos
      if (strlen($whazapp) < 14) {
        $msg['Whazapp'] = "Whazapp não possui o tamanho mínimo de 13 caracter";
      }

      $lista_array = $this->lita_funcao();
      $lista_array = json_decode($lista_array->getBody(), true);

      // Validações dos dados recebidos
      if (!in_array($funcao, $lista_array['lista'])) {
        $msg['Função'] = "Nome da Função não cadastrado";
        $violacao[] = "user_modificar_update Nome da Função não cadastrado";
      } else {
        if (Ferramentas::codificador($funcao) == '') {
          $msg['Função'] = "Função possui caracteres não permitidos";
          $violacao[] = "user_modificar_update Função possui caracteres não permitidos";
        }
      }

      // Validações dos dados recebidos
      $teste = true;
      foreach (str_split($email) as $key => $value) {
        if (in_array($value, ['@'])) {
          $teste = false;
        }
      }
      if ($teste) {
        $msg['Email'] = "Email com nome invalido";
      }


      // Validações dos dados recebidos
      $teste = false;
      foreach (str_split($whazapp) as $key => $value) {
        if (!in_array($value, ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ' ', '(', ')', '-'])) {
          $teste = true;
        }
      }

      // Validações dos dados recebidos
      if ($teste) {
        $msg['Whazapp'] = "Whazapp possui caracteres não permitidos";
        $violacao[] = "user_modificar_update Whazapp possui caracteres não permitidos";
      }

      session_start();
      if (count($msg) == 0) {
        // Inicializa o banco de dados e busca dados relacionados
        $id = service('request')->getPost('id');
        $id = $_SESSION['lista'][$id];
        $_SESSION['lista_completa'];
        $db = new \App\Models\Usuarios();
        $db1 = new \App\Models\Funcao();
        $db1_data = $db1->find();
        $db_data = $db->find();
        $tipo = Ferramentas::array_pesquisa($db1_data, 'nome', Ferramentas::codificador($funcao))['id'];

        if (
          (count(Ferramentas::array_pesquisa($db_data, 'nome', Ferramentas::codificador($nome))) == 0 ||
            count(Ferramentas::array_pesquisa_mult($db_data, ['id', 'nome'], [$id, Ferramentas::codificador($nome)])) != 0) &&
          count(Ferramentas::array_pesquisa_mult($db_data, [
            'id',
            'nome',
            'senha',
            'tipo',
            'email',
            'whatsapp'
          ], [
            $id,
            Ferramentas::codificador($nome),
            (Ferramentas::codificador($senha)),
            $tipo,
            Ferramentas::codificador($email),
            str_replace(['(', ')', '-', ' '], [''], $whazapp)
          ])) == 0
        ) {
          // Verifica se as alterações não estão duplicadas
          $alteracao = new \App\Models\Alteracoes();
          $data = [
            "individuo" => $_SESSION["usuario"],
            "id_item" => $id,
            "antes" => Ferramentas::array_index(Ferramentas::array_pesquisa($db_data, 'id', $id), ['nome']) . " - " .
              Ferramentas::array_index(Ferramentas::array_pesquisa($db_data, 'id', $id), ['senha']) . " - " .
              Ferramentas::array_index(Ferramentas::array_pesquisa($db_data, 'id', $id), ['tipo']) . " - " .
              Ferramentas::array_index(Ferramentas::array_pesquisa($db_data, 'id', $id), ['email']) . " - " .
              Ferramentas::array_index(Ferramentas::array_pesquisa($db_data, 'id', $id), ['whazapp']),
            "depois" => Ferramentas::codificador($nome) . " - " .
              Ferramentas::codificador($senha) . " - " .
              $tipo . " - " .
              Ferramentas::codificador($email) . " - " .
              str_replace(['(', ')', '-', ' '], [''], $whazapp),
            "item" => "user",
            "info_mais" => "nome - senha - tipo - email - whazapp",
            "data_add" => Ferramentas::codificador(date('d/m/Y H:i'))

          ];
          $alteracao->insert($data);


          $date = [
            'nome' => Ferramentas::codificador($nome),
            'senha' => (Ferramentas::codificador($senha)),
            'tipo' => $tipo,
            'email' => Ferramentas::codificador($email),
            'whatsapp' => str_replace(['(', ')', '-', ' '], [''], $whazapp)


          ];



          $db->update($id, $date); // Atualiza os dados do usuário no banco de dados
          $ok = true;
          $msg['1'] = $date;
        } else if (count(Ferramentas::array_pesquisa_mult($db_data, ['id', 'nome', 'senha', 'tipo', 'email', 'whatsapp'], [$id, Ferramentas::codificador($nome), (Ferramentas::codificador($senha)), $tipo, Ferramentas::codificador($email), str_replace(['(', ')', '-', ' '], [''], $whazapp)])) != 0) {
          $msg["Modificar"] = 'Não foi feita nenhuma alteração.';
        } else {
          $msg["Nome"] = 'Nome de usuário já existente';
          $violacao[] = "user_modificar_update Nome de usuário já existente";
        }


      }

      if (count($violacao) != 0) {
        // Se houver violações, insira informações sobre as violações no banco de dados
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
   * Função lista_funcao()
   *
   * Esta função é responsável por buscar informações sobre funções no banco de dados e retorná-las em formato JSON.
   *
   * Retorna um JSON contendo uma lista de nomes de funções obtidos do banco de dados.
   */
  function lita_funcao()
  {
    $funcao = new \App\Models\Funcao(); // Inicializa o modelo de Função para acessar o banco de dados

    $funcao_data = $funcao->find(); // Busca dados sobre funções no banco de dados
    $lista = array();

    // Cria uma lista de nomes de funções decodificadas
    foreach ($funcao_data as $key => $value) { //cria a lista 
      $lista[] = Ferramentas::decodificador($value['nome']);
    }
    $data = ['lista' => $lista]; // Prepara os dados para serem retornados em formato JSON
    return $this->response->setJSON($data);
  }


  /**
   * Função lista_empresa()
   *
   * Esta função é responsável por buscar informações sobre empresas no banco de dados e retorná-las em formato JSON.
   *
   * Retorna um JSON contendo uma lista de nomes de empresas obtidos do banco de dados.
   */
  function lita_empresa()
  {
    $funcao = new \App\Models\Empresa(); // Inicializa o modelo de Empresa para acessar o banco de dados

    $funcao_data = $funcao->find(); // Busca dados sobre empresas no banco de dados
    $lista = array();

    // Cria uma lista de nomes de empresas decodificados
    foreach ($funcao_data as $key => $value) { //cria a lista 
      if ($value['status'] == 'ativo') {
        $lista[] = Ferramentas::decodificador($value['nome']);
      }
    }
    usort($lista, function ($a, $b) {
      return strnatcasecmp($a, $b);
    });
    $data = ['lista' => $lista]; // Prepara os dados para serem retornados em formato JSON
    return $this->response->setJSON($data);
  }

  /**
   * Função troca_status()
   *
   * Esta função é responsável por alterar o status de um objeto no banco de dados para "ativo" ou "desativado".
   *
   * @param string $table O nome da tabela do banco de dados onde a alteração deve ser realizada.
   * @param string $status O novo status a ser definido ("ativo" ou "desativado").
   *
   * Retorna um JSON indicando se a operação foi bem-sucedida ou não.
   */
  function troca_status($table = null, $status = NULL)
  {
    if ($status == "desativado" || $status == "ativo") { // Verifica se a variável status está correta
      if ($this->request->isAJAX()) {
        session_start();
        $id = service('request')->getPost('id'); // Obtém o ID falso fornecido via AJAX
        $lista = $_SESSION["lista"]; // Obtém a lista de IDs

        if (Ferramentas::array_index($lista, [$id]) != "") { // Verifica se o ID existe na lista
          $item = '';
          switch ($table) { // Determina qual tabela do banco de dados deve ser atualizada
            case 'user':
              $db = new \App\Models\Usuarios();
              $item = "user";
              break;
            case 'empreendimentos':
              $db = new \App\Models\Empreendimentos();
              $item = "empreendimentos";
              break;
            case 'empresa':
              $db = new \App\Models\Empresa();
              $item = "empresa";
              break;
            case 'finalidade':
              $db = new \App\Models\Finalidade();
              $item = "finalidade";
              break;
            case 'prioridade':
              $db = new \App\Models\Prioridade();
              $item = "prioridade";
              break;
            case 'filtros':
              $db = new \App\Models\Filtros();
              $item = "filtros";
              break;
            case 'tag':
              $db = new \App\Models\Tag();
              $item = "tag";
              break;
            default:
              $data = [
                //caso não exista retorna que deu errado
                "ok" => false,
              ];
              return $this->response->setJSON($data);
              break;
          }
          $alteracao = new \App\Models\Alteracoes();

          // Registra a alteração no histórico de alterações
          $data = [
            "individuo" => $_SESSION["usuario"],
            "id_item" => Ferramentas::array_index($lista, [$id]),
            "antes" => Ferramentas::array_index(Ferramentas::array_pesquisa($db->find(), 'id', Ferramentas::array_index($lista, [$id])), ['status']),
            "depois" => $status,
            "item" => $item,
            "info_mais" => "status",
            "data_add" => Ferramentas::codificador(date('d/m/Y H:i'))

          ];
          $alteracao->insert($data);

          // Atualiza o status no banco de dados
          $db->update(Ferramentas::array_index($lista, [$id]), ['status' => $status]); //faz o update no banco e troca o id falso pelo verdadeiro
          $data = [
            //retorna que deu certo para o ajax
            "ok" => true,
          ];
        } else {
          $data = [
            //se o não ouver nada na lista retorna que deu errado
            "ok" => false,
          ];
        }
        return $this->response->setJSON($data);
      }
    }
  }


  /**
   * Função config_empreendimento_cadastrar()
   *
   * Esta função é responsável por cadastrar um novo empreendimento no banco de dados.
   *
   * Retorna um JSON indicando se o cadastro foi bem-sucedido ou não, juntamente com mensagens de erro, se houverem.
   */
  function config_empreendimento_cadastrar()
  {
    if ($this->request->isAJAX()) {
      $msg = array();
      $violacao = array();
      $ok = false;
      $empreendimento = service('request')->getPost('empreendimento');
      $empresa = service('request')->getPost('empresa');

      if (strlen($empreendimento) > 17) {
        $msg['Empreendimento'] = "Nome do empreendimento excedeu o tamanho máximo de 17 caracter";
        $violacao[] = "config_empreendimento_cadastrar Nome da empreendimento excedeu o tamanho máximo";
      }

      if (strlen($empreendimento) < 3) {
        $msg['Empreendimento'] = "Nome da empreendimento não possui o tamanho mínimo de 3 caracter";
      } else {
        if (Ferramentas::codificador($empreendimento) == '') {
          $msg['Empreendimento'] = "Nome do empreendimento possui caracteres não permitidos";
          $violacao[] = "config_empreendimento_cadastrar Nome do empreendimento possui caracteres não permitidos";
        }
      }


      $lista_array = $this->lita_empresa();
      $lista_array = json_decode($lista_array->getBody(), true);

      if (!in_array($empresa, $lista_array['lista'])) {
        $msg['Empresa'] = "Nome da empresa não cadastrado";
        $violacao[] = "config_empreendimento_cadastrar Nome da empresa não cadastrado";
      } else {
        if (Ferramentas::codificador($empresa) == '') {
          $msg['Empresa'] = "Empresa possui caracteres não permitidos";
          $violacao[] = "config_empreendimento_cadastrar Empresa possui caracteres não permitidos";
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
          $violacao[] = "config_empreendimento_cadastrar Nome do empreendimento já existente nessa empresa";
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
   * Função config_empreendimento_modal()
   *
   * Esta função é responsável por retornar informações específicas de um empreendimento, incluindo seu nome,
   * ID da empresa e se possui desenhos associados.
   *
   * Retorna um JSON contendo informações sobre o empreendimento.
   */
  function config_empreendimento_modal()
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
   * Função config_empreendimento_update()
   *
   * Esta função é responsável por atualizar informações de um empreendimento, incluindo seu nome e a empresa associada.
   *
   * Retorna um JSON indicando o sucesso da operação e possíveis mensagens de erro.
   */
  function config_empreendimento_update()
  {
    if ($this->request->isAJAX()) {
      $msg = array();
      $ok = false;
      $violacao = array();
      $empreendimento = service('request')->getPost('empreendimento');
      $empresa = service('request')->getPost('empresa');

      if (strlen($empreendimento) > 17) {
        $msg['Empreendimento'] = "Nome do empreendimento excedeu o tamanho máximo de 17 caracter";
        $violacao[] = "config_empreendimento_update Nome do empreendimento excedeu o tamanho máximo";
      }

      if (strlen($empreendimento) < 3) {
        $msg['Empreendimento'] = "Nome da empreendimento não possui o tamanho mínimo de 3 caracter";
      } else {
        if (Ferramentas::codificador($empreendimento) == '') {
          $msg['Empreendimento'] = "Nome do empreendimento possui caracteres não permitidos";
          $violacao[] = "config_empreendimento_update Nome do empreendimento possui caracteres";
        }
      }


      $lista_array = $this->lita_empresa();
      $lista_array = json_decode($lista_array->getBody(), true);

      if (!in_array($empresa, $lista_array['lista'])) {
        $msg['Empresa'] = "Nome da empresa não cadastrado";
        $violacao[] = "config_empreendimento_update Nome da empresa não cadastrado";
      } else {
        if (Ferramentas::codificador($empresa) == '') {
          $msg['Empresa'] = "Empresa possui caracteres não permitidos";
          $violacao[] = "config_empreendimento_update Empresa possui caracteres não permitidos";
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
        if (count(Ferramentas::array_pesquisa($desenhos_data, 'empreendimento', $lista['id'])) == 0) {
          $empresa_id = Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'nome', Ferramentas::codificador($empresa)), ['id']); // pega o id da empresa fornecida 
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
            $violacao[] = "config_empreendimento_update Nome do empreendimento já existente nessa empresa";
          }
        } else { //violação 
          $msg["Modificar"] = 'Empreendimento já está em uso.';
          $violacao[] = "config_empreendimento_update Empreendimento já está em uso";
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
   * Função config_empresa_cadastrar()
   *
   * Esta função é responsável por cadastrar uma nova empresa.
   *
   * Retorna um JSON indicando o sucesso da operação e possíveis mensagens de erro.
   */
  function config_empresa_cadastrar()
  {
    if ($this->request->isAJAX()) {
      $msg = array();
      $ok = false;
      $violacao = array();
      $empresa = service('request')->getPost('empresa');

      if (strlen($empresa) > 100) {
        $msg['Empresa'] = "Nome da empresa excedeu o tamanho máximo de 100 caracter";
        $violacao[] = "config_empresa_cadastrar empresa excedeu o tamanho máximo";
      }

      if (strlen($empresa) < 2) {
        $msg['Empresa'] = "Nome da empresa não possui o tamanho mínimo de 2 caracter";
      } else {
        if (Ferramentas::codificador($empresa) == '') {
          $msg['Empresa'] = "Nome da empresa possui caracteres não permitidos";
          $violacao[] = "config_empresa_cadastrar empresa possui caracteres não permitidos";
        }
      }


      session_start();
      if (count($msg) == 0) {
        $db = new \App\Models\Empresa();


        $empresa_data = $db->find();

        if (count(Ferramentas::array_pesquisa($empresa_data, 'nome', Ferramentas::codificador($empresa))) == 0) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 
          $date = [
            'nome' => Ferramentas::codificador($empresa),
            'data_hora_add' => Ferramentas::codificador(date('d/m/Y H:i')),
            'status' => 'ativo',
            'individuo' => $_SESSION['usuario']

          ];
          $db->insert($date);
          $ok = true;
        } else {
          $msg["Empresa"] = 'Nome da empresa já existente';
          $violacao[] = "config_empresa_cadastrar empresa já existente";
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
   * Função para obter informações de uma empresa via AJAX.
   *
   * Esta função é acionada via AJAX para obter informações detalhadas sobre uma empresa, incluindo
   * se a empresa está associada a algum desenho no sistema.
   *
   * Resposta JSON com as informações da empresa.
   */
  function config_empresa_modal()
  {
    if ($this->request->isAJAX()) {
      session_start();
      $ok = false;
      $id = service('request')->getPost('id'); // Obtém o ID da empresa fornecido via AJAX

      // Recupera dados dos desenhos no sistema
      $desenhos = new \App\Models\Desenhos();
      $desenhos_data = $desenhos->find();

      // Obtém as informações detalhadas da empresa a partir da sessão
      $lista = $_SESSION["lista_completa"][$id];

      // Verifica se a empresa está associada a algum desenho
      if (count(Ferramentas::array_pesquisa($desenhos_data, 'empresa', $lista['id'])) != 0) {
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
   * Atualiza os detalhes de uma empresa via AJAX.
   *
   * Esta função é acionada via AJAX para atualizar os detalhes de uma empresa, incluindo seu nome.
   *
   * Resposta JSON indicando se a atualização foi bem-sucedida e mensagens de erro, se houver.
   */
  function config_empresa_update()
  {
    if ($this->request->isAJAX()) {
      $msg = array();
      $ok = false;
      $violacao = array();
      $empresa = service('request')->getPost('empresa');

      // Valida o tamanho do nome da empresa
      if (strlen($empresa) > 100) {
        $msg['Empresa'] = "Nome da empresa excedeu o tamanho máximo de 100 caracter";
        $violacao[] = "config_empresa_update empresa excedeu o tamanho máximo";
      }

      if (strlen($empresa) < 2) {
        $msg['Empresa'] = "Nome da empresa não possui o tamanho mínimo de 2 caracter";
      } else {
        if (Ferramentas::codificador($empresa) == '') {
          $msg['Empresa'] = "Nome da empresa possui caracteres não permitidos";
          $violacao[] = "config_empresa_update empresa possui caracteres não permitidos";
        }
      }


      session_start();
      if (count($msg) == 0) {
        $db = new \App\Models\Empresa();

        $id1 = service('request')->getPost('id');
        $id = $_SESSION['lista'][$id1];
        $empresa_data = $db->find();
        $desenhos = new \App\Models\Desenhos();
        $desenhos_data = $desenhos->find();
        $lista = $_SESSION["lista_completa"][$id1];

        // Verifica se a empresa não está associada a nenhum desenho
        if (count(Ferramentas::array_pesquisa($desenhos_data, 'empresa', $lista['id'])) == 0) {


          if (count(Ferramentas::array_pesquisa($empresa_data, 'nome', Ferramentas::codificador($empresa))) == 0) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 
            // Verifica se o nome da empresa não está em uso
            $alteracao = new \App\Models\Alteracoes();

            $data = [
              "individuo" => $_SESSION["usuario"],
              "id_item" => $id,
              "antes" => Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $id), ['nome']),
              "depois" => Ferramentas::codificador($empresa),
              "item" => "empresa",
              "info_mais" => "nome",
              "data_add" => Ferramentas::codificador(date('d/m/Y H:i'))

            ];
            $alteracao->insert($data);




            $date = [
              'nome' => Ferramentas::codificador($empresa),


            ];


            $db->update($id, $date);
            $ok = true;
          } else if (count(Ferramentas::array_pesquisa_mult($empresa_data, ['id', 'nome'], [$id, Ferramentas::codificador($empresa)])) != 0) {
            $msg["Modificar"] = 'Não foi feita nenhuma alteração.';
          } else {
            $msg["Empresa"] = 'Nome da empresa já existente';
            $violacao[] = "config_empresa_update empresa já existente";
          }
        } else { //violação 
          $msg["Modificar"] = 'Empresa já está em uso.';
          $violacao[] = "config_empresa_update Empresa já está em uso";
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
   * Lista os empreendimentos ativos associados a uma empresa específica via AJAX.
   *
   * Esta função é acionada via AJAX para listar os empreendimentos ativos que estão associados a uma empresa específica.
   *
   * Resposta JSON contendo a lista de empreendimentos ativos da empresa.
   */
  function config_empreendimento_lista()
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



      foreach ($empreendimento_data as $key => $value) { //cria a lista

        // Verifica se o empreendimento está ativo e associado à empresa específica
        if ($value['status'] == 'ativo' && $value['empresa_id'] == $id) {
          $temp['empreendimento'] = Ferramentas::decodificador($value['nome']);

          $lista[] = $temp;
        }

      }
      usort($lista, function ($a, $b) {
        return strcasecmp($a['empreendimento'], $b['empreendimento']);
      });

      //retorna a lista para o ajax
      $data = [
        "lista" => $lista,


      ];

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
  function config_finalidade_lista()
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
        "lista" => $lista,


      ];

      return $this->response->setJSON($data);
    }
  }



  /**
   * Lista as empresas ativas via AJAX.
   *
   * Esta função é acionada via AJAX para listar as empresas ativas no sistema.
   *
   * Resposta JSON contendo a lista de empresas ativas.
   */
  function config_empresa_lista() //rece um post via ajax pedindo para listar os usuarios
  {
    if ($this->request->isAJAX()) {
      session_start();
      $finalidade = new \App\Models\Empresa(); //pega do banco a tabela

      $finalidade_data = $finalidade->find();
      $lista = array();



      foreach ($finalidade_data as $key => $value) { //cria a lista

        // Verifica se a empresa está ativa
        if ($value['status'] == 'ativo') {
          $temp['empresa'] = Ferramentas::decodificador($value['nome']);

          $lista[] = $temp;
        }

      }
      usort($lista, function ($a, $b) {
        return strcasecmp($a['empresa'], $b['empresa']);
      });

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
  function config_finalidade_cadastrar()
  {

    if ($this->request->isAJAX()) {
      $msg = array();
      $ok = false;
      $violacao = array();

      $finalidade = service('request')->getPost('finalidade');

      if (strlen($finalidade) > 17) {
        $msg['Finalidade'] = "Nome da Finalidade excedeu o tamanho máximo de 17 caracter";
        $violacao[] = "config_finalidade_cadastrar Finalidade excedeu o tamanho máximo";
      }

      if (strlen($finalidade) < 3) {
        $msg['Finalidade'] = "Nome da Finalidade não possui o tamanho mínimo de 3 caracter";
      } else {
        if (Ferramentas::codificador($finalidade) == '') {
          $msg['Finalidade'] = "Nome da Finalidade possui caracteres não permitidos";
          $violacao[] = "config_finalidade_cadastrar Finalidade possui caracteres não permitidos";
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
          $violacao[] = "config_finalidade_cadastrar Finalidade já existente";
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
  function config_finalidade_modal()
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
  function config_finalidade_update()
  {
    if ($this->request->isAJAX()) {
      $msg = array();
      $ok = false;
      $violacao = array();
      $finalidade = service('request')->getPost('finalidade');

      if (strlen($finalidade) > 17) {
        $msg['Finalidade'] = "Nome da Finalidade excedeu o tamanho máximo de 17 caracter";
        $violacao[] = "config_finalidade_update Finalidade excedeu o tamanho máximo";
      }

      if (strlen($finalidade) < 3) {
        $msg['Finalidade'] = "Nome da Finalidade não possui o tamanho mínimo de 3 caracter";
      } else {
        if (Ferramentas::codificador($finalidade) == '') {
          $msg['Finalidade'] = "Nome da Finalidade possui caracteres não permitidos";
          $violacao[] = "config_finalidade_update Finalidade possui caracteres não permitidos";
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
            $violacao[] = "config_finalidade_update Finalidade já existente";
          }
        } else { //violação 
          $msg["Modificar"] = 'Finalidade já está em uso.';
          $violacao[] = "config_finalidade_update Finalidade já está em uso";
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
   * Cadastra uma nova prioridade via AJAX.
   *
   * Esta função é acionada via AJAX para cadastrar uma nova prioridade no sistema.
   *
   * Resposta JSON indicando se o cadastro foi bem-sucedido, as mensagens de erro (se houver) e outras informações relevantes.
   */
  function config_prioridade_cadastrar()
  {

    if ($this->request->isAJAX()) {
      $msg = array();
      $ok = false;
      $violacao = array();

      $prioridade = service('request')->getPost('prioridade');
      $cor = service('request')->getPost('cor');


      if (strlen($prioridade) > 17) {
        $msg['Prioridade'] = "Nome da Prioridade excedeu o tamanho máximo de 17 caracter";
        $violacao[] = "config_prioridade_cadastrar Prioridade excedeu o tamanho máximo";
      }

      if (strlen($prioridade) < 1) {
        $msg['Prioridade'] = "Nome da Prioridade não possui o tamanho mínimo de 1 caracter";
      } else {
        if (Ferramentas::codificador($prioridade) == '') {
          $msg['Prioridade'] = "Nome da Prioridade possui caracteres não permitidos";
          $violacao[] = "config_prioridade_cadastrar Prioridade possui caracteres não permitidos";
        }
      }
      if (Ferramentas::codificador($cor) == '') {
        $msg['Cor'] = "Cor possui caracteres não permitidos";
        $violacao[] = "config_prioridade_cadastrar Cor possui caracteres não permitidos";
      }

      $lista_array = $this->lita_empresa();
      $lista_array = json_decode($lista_array->getBody(), true);


      session_start();
      if (count($msg) == 0) {
        $db = new \App\Models\Prioridade();


        $prioridade_data = $db->find();

        $ordem_max = $this->ordem_max();
        $ordem_max = Ferramentas::array_index(json_decode($ordem_max->getBody(), true), ['max']) + 1;

        if (count(Ferramentas::array_pesquisa($prioridade_data, 'nome', Ferramentas::codificador($prioridade))) == 0) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 
          $date = [
            'nome' => Ferramentas::codificador($prioridade),
            'data_hora_add' => Ferramentas::codificador(date('d/m/Y H:i')),
            'status' => 'ativo',
            'individuo' => $_SESSION['usuario'],
            'cor' => Ferramentas::codificador($cor),
            'ordem' => $ordem_max
          ];
          $db->insert($date);
          $ok = true;
        } else {
          $msg["Prioridade"] = 'Nome da Prioridade já existente';
          $violacao[] = "config_prioridade_cadastrar Nome da Prioridade já existente";
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
   * Obtém os detalhes de uma prioridade via AJAX.
   *
   * Esta função é acionada via AJAX para obter os detalhes de uma prioridade com base no ID fornecido.
   *
   * Resposta JSON contendo os detalhes da prioridade, como nome, cor, ordem e status.
   */
  function config_prioridade_modal()
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
  function config_prioridade_update()
  {
    if ($this->request->isAJAX()) {
      $msg = array();
      $ok = false;
      $violacao = array();

      $prioridade = service('request')->getPost('prioridade');
      $cor = service('request')->getPost('cor');


      if (strlen($prioridade) > 17) {
        $msg['Prioridade'] = "Nome da Prioridade excedeu o tamanho máximo de 17 caracter";
        $violacao[] = "config_prioridade_update Prioridade excedeu o tamanho máximo";
      }

      if (strlen($prioridade) < 1) {
        $msg['Prioridade'] = "Nome da Prioridade não possui o tamanho mínimo de 1 caracter";
      } else {
        if (Ferramentas::codificador($prioridade) == '') {
          $msg['Prioridade'] = "Nome da Prioridade possui caracteres não permitidos";
          $violacao[] = "config_prioridade_update Prioridade possui caracteres";
        }
      }
      if (Ferramentas::codificador($cor) == '') {
        $msg['Cor'] = "Cor possui caracteres não permitidos";
        $violacao[] = "config_prioridade_update Cor possui caracteres não permitidos";
      }

      $lista_array = $this->lita_empresa();
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

            $data = [
              "individuo" => $_SESSION["usuario"],
              "id_item" => $id,
              "antes" => Ferramentas::array_index(Ferramentas::array_pesquisa($prioridade_data, 'id', $id), ['nome']) . " - " .
                Ferramentas::array_index(Ferramentas::array_pesquisa($prioridade_data, 'id', $id), ['cor']) . " - " .
                Ferramentas::array_index(Ferramentas::array_pesquisa($prioridade_data, 'id', $id), ['ordem']),
              "depois" => Ferramentas::codificador($prioridade) . " - " . Ferramentas::codificador($cor) . " - " . $ordem,
              "item" => "prioridade",
              "info_mais" => "nome - cor - orendem",
              "data_add" => Ferramentas::codificador(date('d/m/Y H:i'))

            ];
            $alteracao->insert($data);


            if (count(Ferramentas::array_pesquisa_mult($prioridade_data, ['id', 'ordem'], [$id, $ordem])) == 0) {


              $db->update(
                Ferramentas::array_index(Ferramentas::array_pesquisa_mult($prioridade_data, ['ordem'], [$ordem]), ['id']),
                ['ordem' => Ferramentas::array_index(Ferramentas::array_pesquisa_mult($prioridade_data, ['id'], [$id]), ['ordem'])]
              );
            }
            $date = [
              'nome' => Ferramentas::codificador($prioridade),
              'cor' => Ferramentas::codificador($cor),
              'ordem' => $ordem
            ];
            $db->update($id, $date);
            $ok = true;
          } else if (count(Ferramentas::array_pesquisa_mult($prioridade_data, ['id', 'nome', 'cor', 'ordem'], [$id, Ferramentas::codificador($prioridade), Ferramentas::codificador($cor), $ordem])) != 0) {
            $msg["Modificar"] = 'Não foi feita nenhuma alteração.';
          } else {
            $msg["Prioridade"] = 'Nome da Prioridade já existente';
            $violacao[] = "config_prioridade_update Nome da Prioridade já existente";
          }


        }
      } else {
        $msg["Ordem"] = 'Ordem não cadastrada';
        $violacao[] = "config_prioridade_update Ordem não cadastrada";
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
   * Cadastra um novo tipo de arquivo via AJAX.
   *
   * Esta função é acionada via AJAX para cadastrar um novo tipo de arquivo com base nas informações fornecidas.
   *
   * Resposta JSON indicando se o cadastro foi bem-sucedido ou não, e qualquer mensagem associada.
   */
  function config_tipo_de_arquivo_cadastrar()
  {
    if ($this->request->isAJAX()) {
      $msg = array();
      $ok = false;
      $violacao = array();

      $filtro = service('request')->getPost('filtro');
      $filtro = str_replace('.', '', $filtro);
      if (strlen($filtro) > 4) {
        $msg['Tipo de arquivo'] = "Nome do tipo de arquivo excedeu o tamanho máximo de 4 caracter";
        $violacao[] = "config_tipo_de_arquivo_cadastrar tipo de arquivo excedeu o tamanho máximo";
      }

      if (strlen($filtro) < 1) {
        $msg['Tipo de arquivo'] = "Nome do tipo de arquivo não possui o tamanho mínimo de 1 caracter";
      } else {
        if (Ferramentas::codificador($filtro) == '') {
          $msg['Tipo de arquivo'] = "Nome do tipo de arquivo possui caracteres não permitidos";
          $violacao[] = "config_tipo_de_arquivo_cadastrar tipo de arquivo possui caracteres não permitidos";
        }
      }


      session_start();
      if (count($msg) == 0) {
        $db = new \App\Models\Filtros();


        $filtro_data = $db->find();

        if (count(Ferramentas::array_pesquisa($filtro_data, 'nome', Ferramentas::codificador($filtro))) == 0) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 
          $date = [
            'nome' => Ferramentas::codificador($filtro),
            'data_hora_add' => Ferramentas::codificador(date('d/m/Y H:i')),
            'status' => 'ativo',
            'individuo' => $_SESSION['usuario']

          ];
          $db->insert($date);
          $ok = true;
        } else {
          $msg["Tipo de arquivo"] = 'Esse tipo de arquivo já existente';
          $violacao[] = "config_tipo_de_arquivo_cadastrar tipo de arquivo já existente";
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
   * Retorna informações de um tipo de arquivo via AJAX.
   *
   * Esta função é acionada via AJAX para buscar informações detalhadas de um tipo de arquivo com base em seu ID.
   *
   * Resposta JSON contendo as informações do tipo de arquivo, indicando se a operação foi bem-sucedida ou não.
   */
  function config_tipo_de_arquivo_modal()
  {
    if ($this->request->isAJAX()) {
      session_start();
      $ok = false;
      $id = service('request')->getPost('id'); //pega a informação post que foi fornecida via ajax se é para pegar os usuarios ativos
      $lista = $_SESSION["lista_completa"][$id];

      $data = [
        "nome" => Ferramentas::decodificador($lista['nome']),
        "status" => Ferramentas::decodificador($lista['status'])


      ];
      return $this->response->setJSON($data);
    }
  }



  /**
   * Atualiza as informações de um tipo de arquivo via AJAX.
   *
   * Esta função é acionada via AJAX para atualizar as informações de um tipo de arquivo com base no ID fornecido.
   *
   * Resposta JSON indicando se a operação foi bem-sucedida ou não, junto com mensagens de erro ou sucesso.
   */
  function config_tipo_de_arquivo_update()
  {
    if ($this->request->isAJAX()) {
      $msg = array();
      $ok = false;
      $violacao = array();

      $filtro = service('request')->getPost('filtro');
      $filtro = str_replace('.', '', $filtro);
      if (strlen($filtro) > 4) {
        $msg['Tipo de arquivo'] = "Nome do tipo de arquivo excedeu o tamanho máximo de 4 caracter";
        $violacao[] = "config_tipo_de_arquivo_update tipo de arquivo excedeu o tamanho máximo";
      }

      if (strlen($filtro) < 1) {
        $msg['Tipo de arquivo'] = "Nome do tipo de arquivo não possui o tamanho mínimo de 1 caracter";
      } else {
        if (Ferramentas::codificador($filtro) == '') {
          $msg['Tipo de arquivo'] = "Nome do tipo de arquivo possui caracteres não permitidos";
          $violacao[] = "config_tipo_de_arquivo_update tipo de arquivo possui caracteres não permitidos";
        }
      }


      session_start();
      if (count($msg) == 0) {
        $db = new \App\Models\Filtros();

        $id1 = service('request')->getPost('id');
        $id = $_SESSION['lista'][$id1];
        $filtro_data = $db->find();

        if (count(Ferramentas::array_pesquisa($filtro_data, 'nome', Ferramentas::codificador($filtro))) == 0) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 

          $alteracao = new \App\Models\Alteracoes();

          $data = [
            "individuo" => $_SESSION["usuario"],
            "id_item" => $id,
            "antes" => Ferramentas::array_index(Ferramentas::array_pesquisa($filtro_data, 'id', $id), ['nome']),
            "depois" => Ferramentas::codificador($filtro),
            "item" => "filtros",
            "info_mais" => "nome",
            "data_add" => Ferramentas::codificador(date('d/m/Y H:i'))

          ];
          $alteracao->insert($data);



          $date = [
            'nome' => Ferramentas::codificador($filtro),
          ];
          $db->update($id, $date);

          $ok = true;
        } else if (count(Ferramentas::array_pesquisa_mult($filtro_data, ['id', 'nome'], [$id, Ferramentas::codificador($filtro)])) != 0) {
          $msg["Modificar"] = 'Não foi feita nenhuma alteração.';
        } else {
          $msg["Tipo de arquivo"] = 'Esse tipo de arquivo já existente';
          $violacao[] = "config_tipo_de_arquivo_update tipo de arquivo já existente";
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
   * Obtém uma lista de ordens das prioridades a partir de dados armazenados em um banco de dados.
   *
   * Essa função é acionada via AJAX para recuperar a lista de ordens das prioridades a partir dos dados armazenados em um banco de dados.
   *
   * Resposta JSON contendo a lista de ordens das prioridades.
   */
  function lita_ordem()
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
   * Obtém uma lista de filtros a partir de dados armazenados em um banco de dados.
   *
   * Essa função é acionada via AJAX para recuperar a lista de filtros a partir dos dados armazenados em um banco de dados.
   *
   * @Resposta JSON contendo a lista de filtros separados por vírgulas.
   */
  function lita_filtro()
  {
    if ($this->request->isAJAX()) {
      $prioridade = new \App\Models\Filtros(); //pega do banco a tabela

      $prioridade_data = $prioridade->find();
      $lista = '';
      foreach ($prioridade_data as $key => $value) { //cria a lista 
        if ($value['status'] == 'ativo') {
          $lista .= '.' . Ferramentas::decodificador($value['nome']) . ',';
        }
      }
      $data = ['lista' => $lista];
      return $this->response->setJSON($data);
    }
  }


  function lista_desenhistas()
  {// 3
    if ($this->request->isAJAX()) {
      // Inicializa a sessão para acessar os dados da lista armazenados nela
      session_start();

      $usuarios = new \App\Models\Usuarios(); // Obtém a tabela de usuários do banco
      $usuarios_data = $usuarios->find();

      $lista = array();
      $id_temp = 0;

      foreach ($usuarios_data as $key => $value) {
        // Cria a lista com base nos usuários ativos ou desativados, dependendo da solicitação
        if ((Ferramentas::decodificador($value['tipo']) == '3' or Ferramentas::decodificador($value['tipo']) == '1')) { //verifica se é para mostrar os com estus ativo//verifica se é para mostrar os com estus ativo
          $lista['nome'][$value['status']][$id_temp] = Ferramentas::decodificador($value['nome']);
          $lista[strval($id_temp)] = $value;

        }
        $id_temp++;

      }
      $_SESSION["lista_desenhista"] = $lista;

      //retorna a lista para o ajax
      $data = [
        "lista" => $lista['nome']
      ];

      return $this->response->setJSON($data);
    }
  }
  function lista_cortadores()
  {// 3
    if ($this->request->isAJAX()) {
      // Inicializa a sessão para acessar os dados da lista armazenados nela
      session_start();

      $usuarios = new \App\Models\Usuarios(); // Obtém a tabela de usuários do banco
      $usuarios_data = $usuarios->find();

      $lista = array();
      $id_temp = 0;

      foreach ($usuarios_data as $key => $value) {
        // Cria a lista com base nos usuários ativos ou desativados, dependendo da solicitação
        if ((Ferramentas::decodificador($value['tipo']) == '2')
        ) { //verifica se é para mostrar os com estus ativo//verifica se é para mostrar os com estus ativo
          $lista['nome'][Ferramentas::decodificador($value['status'])][$id_temp] = Ferramentas::decodificador($value['nome']);
          $lista[strval($id_temp)] = $value;
        }
        $id_temp++;

      }
      $_SESSION["lista_cortador"] = $lista;

      //retorna a lista para o ajax
      $data = [
        "lista" => $lista['nome']
      ];

      return $this->response->setJSON($data);
    }
  }

  function timeToSeconds($time)
  {
    list($hours, $minutes, $seconds) = explode(':', $time);
    return $hours * 3600 + $minutes * 60 + $seconds;
  }

  function secondsToTime($seconds)
  {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $seconds = $seconds % 60;
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
  }

  function relatorio_analitico()
  {
    if ($this->request->isAJAX()) {
      session_start();


      $dataFinal_str = service('request')->getPost('dataFinal');
      $dataInicial_str = service('request')->getPost('dataInicial');
      $desenhista_permissao = service('request')->getPost('desenhistas');
      $cortador_permissao = service('request')->getPost('cortador');
      $relatorio = service('request')->getPost('relatorio');
      $msg = array();

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


      $dataFinal = strtotime($dataFinal_str);
      $dataInicial = strtotime($dataInicial_str);

      // Compare as datas
      if (!($dataFinal >= $dataInicial)) {
        $msg["Data Inicial"] = "A data final não pode ser anterior à data inicial.";
      }


      if ($msg != []) {
        $data = [
          'ok' => false,
          'msg' => $msg
        ];
        return $this->response->setJSON($data);
      }










      // Inicialização de objetos para acessar tabelas do banco de dados
      $desenhos = new \App\Models\Desenhos();
      $prioridade = new \App\Models\Prioridade();
      $finalidade = new \App\Models\Finalidade();
      $empresa = new \App\Models\Empresa();
      $empreendimento = new \App\Models\Empreendimentos();
      $usuario = new \App\Models\Usuarios();
      $cortado = new \App\Models\Corte();
      // Recupera dados das tabelas do banco de dados
      $prioridade_data = $prioridade->find();
      $finalidade_data = $finalidade->find();
      $empresa_data = $empresa->find();
      $empreendimento_data = $empreendimento->find();
      $desenhos_data = $desenhos->find();
      $usuario_data = $usuario->find();
      $cortado_data = $cortado->find();

      $corte = array();
      $desenhista = array();
      $teste = 0;
      // Itera sobre os dados de desenhos para criar a lista
      foreach ($desenhos_data as $key => $value) {
        $ok_desenhista = false;
        $ok_cortador = false;
        if ($desenhista_permissao)
          foreach ($desenhista_permissao as $value1) {
            if ($_SESSION["lista_desenhista"][$value1]['id'] == $value['desenhista']) {
              $ok_desenhista = true;
            }
          }
        if ($cortador_permissao)
          foreach ($cortador_permissao as $value1) {
            if ($_SESSION["lista_cortador"][$value1]['id'] == $value['cortador']) {
              $ok_cortador = true;
            }
          }


        if (!$ok_cortador and !$ok_desenhista)
          continue;

        // $teste++;
        // Converter a data específica para timestamp
        $dataEspecifica_desenho_add = strtotime(str_replace('/', '-', Ferramentas::decodificador(Ferramentas::decodificador($value['data_hora_add']))));
        $dataEspecifica_corte = strtotime(str_replace('/', '-', Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($cortado_data, 'id_desenho', $value['id']), ['data_fim']))));



        $tags = explode('/', Ferramentas::decodificador($value['caminho']));
        // Remover os índices de 0 a 5
        $tags = array_slice($tags, 6);

        // Remover o último elemento
        unset($tags[count($tags) - 1]);
        $tags = implode(" - ", $tags);
        $data_hora_corte_fim = strtotime(str_replace('/', '-', Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($cortado_data, 'id_desenho', $value['id']), ['data_fim']))));

        if (
          (
            (Ferramentas::decodificador($value['status']) == "cortado" or Ferramentas::decodificador($value['status']) == "cortado_notfile") &&
            (($dataEspecifica_corte >= $dataInicial && $dataEspecifica_corte <= $dataFinal) or
              ($dataEspecifica_corte == null && ($dataEspecifica_desenho_add >= $dataInicial && $dataEspecifica_desenho_add <= $dataFinal)))
          )
        ) {

          $data_hora_corte_fim = Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($cortado_data, 'id_desenho', $value['id']), ['data_fim']));
          $data_hora_corte_ini = Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($cortado_data, 'id_desenho', $value['id']), ['data_add']));

          // if (strlen($data_hora_corte_fim) < 5) {
          //   continue;
          // }
          // Convertendo as datas para timestamps Unix


          $timestamp1 = strtotime(str_replace('/', '-', $data_hora_corte_fim));
          $timestamp2 = strtotime(str_replace('/', '-', $data_hora_corte_ini));

          // Calculando a diferença em segundos
          $diferencaSegundos = abs($timestamp2 - $timestamp1);

          // Convertendo a diferença para horas, minutos e segundos
          $horas = floor($diferencaSegundos / 3600);
          $minutos = floor(($diferencaSegundos % 3600) / 60);
          $segundos = $diferencaSegundos % 60;

          // Formatando a diferença como "horas:minutos:segundos"
          $diferencaHoras = sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);
          if ($data_hora_corte_fim == null or $data_hora_corte_ini == null) {
            $diferencaHoras = "00:00:00";
          }

          $corte[Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['cortador']), ['nome']))][] =
            [
              "desenhista" => Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['desenhista']), ['nome'])),
              "nome_arquivo" => Ferramentas::decodificador(Ferramentas::remove_id_file(Ferramentas::decodificador($value['nome']))),
              "empresa" => Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ['nome'])),
              "empreendimento" => Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ['nome'])),
              "finalidade" => Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ['nome'])),
              "tags" => $tags,
              "data_hora_add" => Ferramentas::decodificador(Ferramentas::decodificador($value['data_hora_add'])),
              "data_hora_corte" => $data_hora_corte_fim,
              "tempo_corte" => $diferencaHoras,
              "cortador" => Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['cortador']), ['nome'])),
              "status" => Ferramentas::decodificador(Ferramentas::decodificador($value['status'])),
              "ok" => $ok_cortador

            ];
        } else {
          if ($value['status'] != 'apagado') {
            $value['status'] = 'corte';
          }
        }







        if (($dataEspecifica_desenho_add >= $dataInicial && $dataEspecifica_desenho_add <= $dataFinal)) {



          $desenhista[Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['desenhista']), ['nome']))][] =
            [
              "desenhista" => Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['desenhista']), ['nome'])),
              "nome_arquivo" => Ferramentas::decodificador(Ferramentas::remove_id_file(Ferramentas::decodificador($value['nome']))),
              "empresa" => Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ['nome'])),
              "empreendimento" => Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ['nome'])),
              "finalidade" => Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ['nome'])),
              "tags" => $tags,
              "data_hora_add" => Ferramentas::decodificador(Ferramentas::decodificador($value['data_hora_add'])),
              "status" => Ferramentas::decodificador(Ferramentas::decodificador($value['status'])),
              "ok" => $ok_desenhista

            ];
        }

      }

      $total_desenhos_tr = 0;
      $total_desenhos_apagados_tr = 0;
      $desenhista_tr = "";
      $desenhistas_tr = "";
      $N = 0;
      $N1 = 0;
      $desenhistas = '';
      $totalN = array_reduce($desenhista, function ($carry, $item) {
        return $carry + count($item);
      }, 0);


      foreach ($desenhista as $key => $value1) {

        $apagados = 0;
        $N1++;
        $total_desenhos_tr += count($value1);


        $temp_desenhista_tr = '<h3>' . $key . '</h3>
               <table class="table tabela">
              <tr style="background-color: white;">
                <th> Nº</th>
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

          if ($value["status"] == "apagado"){
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
            <th> ' . $value["data_hora_add"] . ' </th>
            <th> ' . str_replace(["cortado_notfile", "corte"], ["cortado", "pendente"], $value["status"]) . ' </th>
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
        <th> ' . abs(count($value1)-$apagados) . ' </th>
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
        $temp_corte_tr = '<h3>' . $key . '</h3>
                  <table class="table tabela">
                  <tr style="background-color: white;">
                  <th> Nº</th>
                  <th> Desenhista </th>
                  <th> Nome do arquivo </th>
                  <th> Empresa/Cliente </th>
                  <th> Empreendimento </th>
                  <th> Finalidade </th>
                  <th> Data de Envio </th>
                  <th> Data de corte </th>
                  <th> Tempo de corte </th>
                  <th> Status </th>
                  </tr> ';
        $N = 0;
        foreach ($value1 as $value) {
          if (!$value["ok"])
            continue;
          $temp_nome_arquivo = $value["nome_arquivo"];
          if (strpos($temp_nome_arquivo, "cortado") === 0) {
            // Remove o prefixo e os 8 caracteres seguintes
            $temp_nome_arquivo = substr($temp_nome_arquivo, 15);
          }
          $N++;
          $temp_corte_tr .= '
          <tr style="background-color: white;">
            <th> ' . str_pad($N, strlen(strval(count($value1))), '0', STR_PAD_LEFT) . '</th>
            <th> ' . $value["desenhista"] . ' </th>
            <th> ' . $temp_nome_arquivo . ' </th>
            <th> ' . $value["empresa"] . ' </th>
            <th> ' . $value["empreendimento"] . ' </th>
            <th> ' . $value["finalidade"] . ' </th>
            <th> ' . $value["data_hora_add"] . ' </th>
            <th> ' . $value["data_hora_corte"] . ' </th>
            <th> ' . $value["tempo_corte"] . ' </th>
            <th> ' . str_replace(["cortado_notfile", "corte"], ["cortado", "pendente"], $value["status"]) . ' </th>
          </tr>
          
          ';
        }
        $temp_corte_tr .= '</table>';
        if ($N != 0) {
          $corte_tr .= $temp_corte_tr;
          $cortadores .= '<p>' . $key . '</p>';
        }



      }
      $qtd_corte = $N;



      $pdf = ' 
      <h1>WL Maquetaria</h1><br/>
                <table style="width: 100%;  border: 0px; vertical-align: top;">
                <tr>
                    <td>Relatorio do sistema de corte</td>
                    <td style="text-align: right;">Período: ' . date("d/m/Y", strtotime($dataInicial_str)) . ' a ' . date("d/m/Y", strtotime($dataFinal_str)) . '</td>
                </tr>
                <tr>
                    <td></td>
                    <td style="text-align: right;">Emissão: ' . date('d/m/Y H:i') . '</td>
                </tr>
                </table>
                <br/><br/>
                <table style="width: 100%;  border: 0px; vertical-align: top;">
                <tr>
                    <td><b>Desenhistas participando</b></td>
                    <td><b>Cortados participando</b></td>
                </tr>
                <tr>
                    <td>' . $desenhistas . '</td>
                    <td>' . $cortadores . '</td>
                </tr>
                </table>
<br/><br/>
                <h2 >Desenhista</h2></br>
              <table class="table tabela">
                <tr style="background-color: white;">
                <th style="width: 7px;"> Nº</th>
                <th style="width: 150px;"> Desenhista </th>
                <th> Quant. de desenhos </th>
                <th> Quant. de desenhos apagados </th>
                <th> Total de desenhos</th>
                </tr>
                ' . $desenhistas_tr . '
                                <tr>
                <th colspan="2">Total de desenhos : ' . $total_desenhos_tr . ' </th>

                <th>Total de desenhos apagados: ' . $total_desenhos_apagados_tr . ' </th>

                <th colspan="2"><b>Total de desenhos adicionados: ' . abs($total_desenhos_tr - $total_desenhos_apagados_tr) . ' </b></td>
                </tr>
                
              </table>
              
              <h2 style="padding-top: 20px;">Cortador</h2></br>
              <table class="table tabela">
                <tr style="background-color: white;">
                <th> Nº</th>
                <th> Desenhista </th>
                <th> Quantidade de desenhos cortados </th>
                <th> Tempo total de corte (h:m:s) </th>
                <th> Tempo medio por corte (h:m:s)</th>
                </tr>
                ' . $cortes_tr . '
                <tr>
                <th colspan="5">Total de desenhos cortados: ' . $total_corte_tr . ' 
                </tr>
              </table>
                ';


      if ($desenhista_tr != "" and $relatorio == "true")
        $pdf .= '<h2 class="page-break">Desenhos enviados</h2></br></br>' . $desenhista_tr;
      if ($corte_tr != "" and $relatorio == "true")
        $pdf .= ' <h2 class="page-break">Desenhos cortados</h2></br></br>' . $corte_tr;



     



      if (($qtd_corte + $qtd_desenho) != 0) {
        $corte_porcento = intval(((100 * $qtd_corte) / ($qtd_corte + $qtd_desenho)));
        $desenhos_porcento = intval(((100 * $qtd_desenho) / ($qtd_corte + $qtd_desenho)));
      } else {
        $corte_porcento = 0;
        $desenhos_porcento = 0;
      }
      //       $pdf .= '
// <center>
//       <div class="grafico-circular"></div>
//       </center>
//       <div class="legenda">
//         <div class="variavel1"><span></span>Cortados ' . $corte_porcento . '%</div>
//         <div class="variavel2"><span></span>Enviados ' . $desenhos_porcento . '%</div>
//       </div>

      //       ';

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
    text-align: center;
    border: 0px solid black;
    border-collapse: collapse;
    padding: 5px;
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

h2 {
    font-weight: bold;
    font-size: 1.5em;
    margin: 0;
    padding: 0;
    line-height: normal;
    text-transform: none;
    letter-spacing: normal;
    word-spacing: normal;
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

      // Inicializa o mPDF
      $mpdf = new Mpdf();

      // Adiciona conteúdo ao PDF
      $mpdf->WriteHTML($style . $pdf);

      // Saída do PDF
      // Saída do PDF como binário
      $pdfContent = $mpdf->Output('', 'S');

      // Retorna a resposta JSON com o conteúdo PDF e o nome do arquivo
      $data = [
        'oi' => $relatorio,
        'ok' => true,
        'pdf' => base64_encode($pdfContent),
        'nome_pdf' => 'Relatorio Wl maquetaria ' . date("d_m_Y", strtotime($dataInicial_str)) . ' a ' . date("d_m_Y", strtotime($dataFinal_str)) . '.pdf'
      ];




      return $this->response->setJSON($data);

    }
  }



}