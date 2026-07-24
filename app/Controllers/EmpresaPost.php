<?php

namespace App\Controllers;

use App\Controllers\Ferramentas;
use Config\App;

class EmpresaPost extends Ferramentas
{
  /**
   * Função empresa_cliente()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por listar as empresas/clientes ativos e seus detalhes, como o nome e o status.
   *
   * Retorna um JSON contendo a lista de empresas/clientes ativos e seus detalhes.
   */
  function empresa_cliente()
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
       <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['nome']) . '</p></td>
       <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
       <td><button name="cadastarar" type="submit" onclick="desativar(\'' . $id_temp . '\')" class="btn btn-outline-danger btn-lg btn-block"> Desativar </button></td>
       <td><button name="cadastarar" type="submit" class="btn btn-outline-warning btn-lg btn-block" onclick="modal_modificar(\'modal_' . $id_temp . '\')"> Modificar </button></td>
      </tr>
      ';
        } else if (($desativados == 'true' && Ferramentas::decodificador($value['status']) == 'desativado')) { //verifica se é para mostrar os com estus desativado
          $lista .= '
      <tr>
       <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['nome']) . '</p></td>
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
   * Função empresa_cadastrar()
   *
   * Esta função é responsável por cadastrar uma nova empresa.
   *
   * Retorna um JSON indicando o sucesso da operação e possíveis mensagens de erro.
   */
  function empresa_cadastrar()
  {
    if ($this->request->isAJAX()) {
      $msg = array();
      $ok = false;
      $violacao = array();
      $empresa = service('request')->getPost('empresa');

      if (strlen($empresa) > 100) {
        $msg['Empresa'] = "Nome da empresa excedeu o tamanho máximo de 100 caracter";
        $violacao[] = "empresa_cadastrar empresa excedeu o tamanho máximo";
      }

      if (strlen($empresa) < 2) {
        $msg['Empresa'] = "Nome da empresa não possui o tamanho mínimo de 2 caracter";
      } else {
        if (Ferramentas::codificador($empresa) == '') {
          $msg['Empresa'] = "Nome da empresa possui caracteres não permitidos";
          $violacao[] = "empresa_cadastrar empresa possui caracteres não permitidos";
        }
      }


      session_start();
      if (count($msg) == 0) {
        $db = new \App\Models\Empresa();


        $empresa_data = $db->find();

        if (count(Ferramentas::array_pesquisa($empresa_data, 'nome',  Ferramentas::norma_lizar_str($empresa))) == 0) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 
          $date = [
            'nome' => Ferramentas::norma_lizar_str($empresa),
            'status' => 'ativo',
            'individuo' => $_SESSION['usuario']

          ];
          $db->insert($date);
          $ok = true;
        } else {
          $msg["Empresa"] = 'Nome da empresa já existente';
          $violacao[] = "empresa_cadastrar empresa já existente";
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
   * Função para obter informações de uma empresa via AJAX.
   *
   * Esta função é acionada via AJAX para obter informações detalhadas sobre uma empresa, incluindo
   * se a empresa está associada a algum desenho no sistema.
   *
   * Resposta JSON com as informações da empresa.
   */
  function empresa_modal()
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
        "nome" => ($lista['nome']),
        "desenho" => $ok,
        "status" => ($lista['status'])


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
  function empresa_update()
  {
    if ($this->request->isAJAX()) {
      $msg = array();
      $ok = false;
      $violacao = array();
      $empresa = service('request')->getPost('empresa');

      // Valida o tamanho do nome da empresa
      if (strlen($empresa) > 100) {
        $msg['Empresa'] = "Nome da empresa excedeu o tamanho máximo de 100 caracter";
        $violacao[] = "empresa_update empresa excedeu o tamanho máximo";
      }

      if (strlen($empresa) < 2) {
        $msg['Empresa'] = "Nome da empresa não possui o tamanho mínimo de 2 caracter";
      } else {
        if (Ferramentas::codificador($empresa) == '') {
          $msg['Empresa'] = "Nome da empresa possui caracteres não permitidos";
          $violacao[] = "empresa_update empresa possui caracteres não permitidos";
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


          if (count(Ferramentas::array_pesquisa($empresa_data, 'nome',  Ferramentas::norma_lizar_str($empresa))) == 0) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 
            // Verifica se o nome da empresa não está em uso
          $alteracao = new \App\Models\Alteracoes();

          $alteracao->insertWithDetails(
            [
              "usuario_id" => $_SESSION["usuario"],
              "id_item" => $id,
              "item" => "empresa",

            ],
            [
              [
                "valor_antes" => Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $id), ['nome']),
                "valor_depois" => $empresa,
                "campo" => "nome"
              ]
            ]
          );




            





            $date = [
              'nome' => Ferramentas::norma_lizar_str($empresa),

            ];


            $db->update($id, $date);
            $ok = true;
          } else if (count(Ferramentas::array_pesquisa_mult($empresa_data, ['id', 'nome'], [$id, ($empresa)])) != 0) {
            $msg["Modificar"] = 'Nenhum item foi modificado.';
          } else {
            $msg["Empresa"] = 'Nome da empresa já existente';
            $violacao[] = "empresa_update empresa já existente";
          }
        } else { //violação 
          $msg["Modificar"] = 'Empresa já está em uso.';
          $violacao[] = "empresa_update Empresa já está em uso";
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
   * Lista as empresas ativas via AJAX.
   *
   * Esta função é acionada via AJAX para listar as empresas ativas no sistema.
   *
   * Resposta JSON contendo a lista de empresas ativas.
   */
  function empresa_lista() //rece um post via ajax pedindo para listar os usuarios
  {
    if ($this->request->isAJAX()) {
      session_start();
      $finalidade = new \App\Models\Empresa(); //pega do banco a tabela

      $finalidade_data = $finalidade->find();
      $lista = array();



      foreach ($finalidade_data as $key => $value) { //cria a lista

        // Verifica se a empresa está ativa
        if ($value['status'] == 'ativo') {
          $temp['empresa'] = Ferramentas::decodificador((string) ($value['nome'] ?? ''));
          if ($temp['empresa'] === '') {
            $temp['empresa'] = (string) ($value['nome'] ?? '');
          }

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
   * Lista empresas ativas no formato simples para filtros de tela.
   * Retorno: [{id, nome}], onde id e um token temporario da sessao.
   */
  public function empresas_lista()
  {
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON(['lista' => []]);
    }

    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    $empresa = new \App\Models\Empresa();
    $empresa_data = $empresa
      ->select('id, nome, status')
      ->where('status', 'ativo')
      ->orderBy('nome', 'ASC')
      ->findAll();

    $tokens = $_SESSION['desenho_empresa_tokens'] ?? [];
    $lista = [];
    foreach ($empresa_data as $value) {
      $empresaId = (int) ($value['id'] ?? 0);
      $token = array_search($empresaId, $tokens, true);
      if ($token === false) {
        $token = bin2hex(random_bytes(16));
        $tokens[$token] = $empresaId;
      }

      $nome = trim((string) Ferramentas::decodificador((string) ($value['nome'] ?? '')));
      if ($nome === '') {
        $nome = (string) ($value['nome'] ?? '');
      }

      $lista[] = [
        'id' => $token,
        'nome' => $nome
      ];
    }

    $_SESSION['desenho_empresa_tokens'] = $tokens;

    return $this->response->setJSON(['lista' => $lista]);
  }

    /**
   * Função lista_empresa()
   *
   * Esta função é responsável por buscar informações sobre empresas no banco de dados e retorná-las em formato JSON.
   *
   * Retorna um JSON contendo uma lista de nomes de empresas obtidos do banco de dados.
   */
  public function lista_empresa()
  {
    $funcao = new \App\Models\Empresa(); // Inicializa o modelo de Empresa para acessar o banco de dados

    $funcao_data = $funcao->find(); // Busca dados sobre empresas no banco de dados
    $lista = array();

    // Cria uma lista de nomes de empresas decodificados
    foreach ($funcao_data as $key => $value) { //cria a lista 
      if ($value['status'] == 'ativo') {
        $lista[] = ($value['nome']);
      }
    }
    usort($lista, function ($a, $b) {
      return strnatcasecmp($a, $b);
    });
    $data = ['lista' => $lista]; // Prepara os dados para serem retornados em formato JSON
    return $this->response->setJSON($data);
  }

}
