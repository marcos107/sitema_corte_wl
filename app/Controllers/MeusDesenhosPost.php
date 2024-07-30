<?php

namespace App\Controllers;

use App\Controllers\Ferramentas;
use Config\App;

class MeusDesenhosPost extends Ferramentas
{
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


  
}