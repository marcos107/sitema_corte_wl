<?php

namespace App\Controllers;

use App\Controllers\Ferramentas;
use Config\App;

class ExtencaoPost extends Ferramentas
{
  /**
   * Função extencao()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por listar os tipos de arquivo com base em critérios específicos, como ativos ou desativados. Também oferece a opção de ativar ou desativar tipos de arquivo.
   *
   * Retorna um JSON contendo a lista de tipos de arquivo de acordo com os critérios fornecidos, bem como a capacidade de ativar ou desativar tipos de arquivo.
   */
  function extencao()
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
   * Cadastra um novo tipo de arquivo via AJAX.
   *
   * Esta função é acionada via AJAX para cadastrar um novo tipo de arquivo com base nas informações fornecidas.
   *
   * Resposta JSON indicando se o cadastro foi bem-sucedido ou não, e qualquer mensagem associada.
   */
  function extencao_cadastrar()
  {
    if ($this->request->isAJAX()) {
      $msg = array();
      $ok = false;
      $violacao = array();

      $filtro = service('request')->getPost('filtro');
      $filtro = str_replace('.', '', $filtro);
      if (strlen($filtro) > 4) {
        $msg['Tipo de arquivo'] = "Nome do tipo de arquivo excedeu o tamanho máximo de 4 caracter";
        $violacao[] = "extencao_cadastrar tipo de arquivo excedeu o tamanho máximo";
      }

      if (strlen($filtro) < 1) {
        $msg['Tipo de arquivo'] = "Nome do tipo de arquivo não possui o tamanho mínimo de 1 caracter";
      } else {
        if (Ferramentas::codificador($filtro) == '') {
          $msg['Tipo de arquivo'] = "Nome do tipo de arquivo possui caracteres não permitidos";
          $violacao[] = "extencao_cadastrar tipo de arquivo possui caracteres não permitidos";
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
          $violacao[] = "extencao_cadastrar tipo de arquivo já existente";
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
  function extencao_modal()
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
  function extencao_update()
  {
    if ($this->request->isAJAX()) {
      $msg = array();
      $ok = false;
      $violacao = array();

      $filtro = service('request')->getPost('filtro');
      $filtro = str_replace('.', '', $filtro);
      if (strlen($filtro) > 4) {
        $msg['Tipo de arquivo'] = "Nome do tipo de arquivo excedeu o tamanho máximo de 4 caracter";
        $violacao[] = "extencao_update tipo de arquivo excedeu o tamanho máximo";
      }

      if (strlen($filtro) < 1) {
        $msg['Tipo de arquivo'] = "Nome do tipo de arquivo não possui o tamanho mínimo de 1 caracter";
      } else {
        if (Ferramentas::codificador($filtro) == '') {
          $msg['Tipo de arquivo'] = "Nome do tipo de arquivo possui caracteres não permitidos";
          $violacao[] = "extencao_update tipo de arquivo possui caracteres não permitidos";
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
          $violacao[] = "extencao_update tipo de arquivo já existente";
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
   * Obtém uma lista de filtros a partir de dados armazenados em um banco de dados.
   *
   * Essa função é acionada via AJAX para recuperar a lista de filtros a partir dos dados armazenados em um banco de dados.
   *
   * @Resposta JSON contendo a lista de filtros separados por vírgulas.
   */
  function lista_filtro()
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

}