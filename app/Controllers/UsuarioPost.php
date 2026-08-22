<?php

namespace App\Controllers;


use App\Controllers\Ferramentas;
use App\Controllers\AdmPost;


class UsuarioPost extends NivelPost
{

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
        "nome" => $lista['nome'],
        "senha" => $lista['senha'],
        "nivel" => $lista['nivel'],
        "email" => $lista['email'],
        "whatsapp" => $lista['whatsapp'],
        "status" => $lista['status']


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
      $nivel = service('request')->getPost('nivel');
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

      $lista_array = NivelPost::lista_nivel();
      $lista_array = json_decode($lista_array->getBody(), true);

      // Validações dos dados recebidos
      if (!in_array($nivel, $lista_array['lista'])) {
        $msg['Função'] = "Nome da Função não cadastrado";
        $violacao[] = "user_cadastrar Função não cadastrado";
      } else {
        if (Ferramentas::codificador($nivel) == '') {
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
        $db1 = new \App\Models\Nivel();
        $db1_data = $db1->find();
        $db_data = $db->find();

        if (count(Ferramentas::array_pesquisa($db_data, 'nome', Ferramentas::codificador($nome))) == 0) {
          // Os dados estão corretos, e o usuário pode ser inserido no banco de dados
          $date = [
            'nome' => ($nome),
            'senha' => (($senha)),
            'nivel_id' => Ferramentas::array_pesquisa($db1_data, 'nome', Ferramentas::codificador($nivel))['id'],
            'email' => ($email),
            'whatsapp' => str_replace(['(', ')', '-', ' '], [''], $whazapp),
            'status' => 'ativo',
            'usuario_id' => $_SESSION['usuario'],
            'acesso_remoto' => 0

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
      $nivel = service('request')->getPost('nivel');
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

      $lista_array = NivelPost::lista_nivel();
      $lista_array = json_decode($lista_array->getBody(), true);

      // Validações dos dados recebidos
      if (!in_array($nivel, $lista_array['lista'])) {
        $msg['Função'] = "Nome da Função não cadastrado";
        $violacao[] = "user_modificar_update Nome da Função não cadastrado";
      } else {
        if (Ferramentas::codificador($nivel) == '') {
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
        $db1 = new \App\Models\Nivel();
        $db1_data = $db1->find();
        $db_data = $db->find();
        $nivel = Ferramentas::array_pesquisa($db1_data, 'nome', Ferramentas::codificador($nivel))['id'];

        if (
          (count(Ferramentas::array_pesquisa($db_data, 'nome', Ferramentas::codificador($nome))) == 0 ||
            count(Ferramentas::array_pesquisa_mult($db_data, ['id', 'nome'], [$id, Ferramentas::codificador($nome)])) != 0) &&
          count(Ferramentas::array_pesquisa_mult($db_data, [
            'id',
            'nome',
            'senha',
            'nivel_id',
            'email',
            'whatsapp'
          ], [
            $id,
            Ferramentas::codificador($nome),
            (Ferramentas::codificador($senha)),
            $nivel,
            Ferramentas::codificador($email),
            str_replace(['(', ')', '-', ' '], [''], $whazapp)
          ])) == 0
        ) {


          // Substitui a inserção manual por um único chamado ao método genérico insertWithDetails
          $alteracao = new \App\Models\Alteracoes();

          $alteracao->insertWithDetails(
            [
              'usuario_id' => $_SESSION['usuario'],
              'id_item'    => $id,
              'item'       => 'user',
            ],
            [
              [
                'campo'        => 'nome',
                'valor_antes'  => Ferramentas::array_index(
                  Ferramentas::array_pesquisa($db_data, 'id', $id),
                  ['nome']
                ),
                'valor_depois' => ($nome),
              ],
              [
                'campo'        => 'senha',
                'valor_antes'  => Ferramentas::array_index(
                  Ferramentas::array_pesquisa($db_data, 'id', $id),
                  ['senha']
                ),
                'valor_depois' => ($senha),
              ],
              [
                'campo'        => 'nivel',
                'valor_antes'  => Ferramentas::array_index(
                  Ferramentas::array_pesquisa($db_data, 'id', $id),
                  ['nivel']
                ),
                'valor_depois' => $nivel,
              ],
              [
                'campo'        => 'email',
                'valor_antes'  => Ferramentas::array_index(
                  Ferramentas::array_pesquisa($db_data, 'id', $id),
                  ['email']
                ),
                'valor_depois' => ($email),
              ],
              [
                'campo'        => 'whatsapp',
                'valor_antes'  => Ferramentas::array_index(
                  Ferramentas::array_pesquisa($db_data, 'id', $id),
                  ['whazapp']
                ),
                'valor_depois' => str_replace(['(', ')', '-', ' '], '', $whazapp),
              ],
            ]
          );


          $date = [
            'nome' => ($nome),
            'senha' => (($senha)),
            'nivel_id' => $nivel,
            'email' => ($email),
            'whatsapp' => str_replace(['(', ')', '-', ' '], [''], $whazapp)


          ];



          $db->update($id, $date); // Atualiza os dados do usuário no banco de dados
          $ok = true;
         // $msg['1'] = $date;
        } else if (count(Ferramentas::array_pesquisa_mult($db_data, ['id', 'nome', 'senha', 'nivel_id', 'email', 'whatsapp'], [$id, Ferramentas::codificador($nome), (Ferramentas::codificador($senha)), $nivel, Ferramentas::codificador($email), str_replace(['(', ')', '-', ' '], [''], $whazapp)])) != 0) {
          $msg["Modificar"] = 'Nenhum item foi modificado.';
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
      $nivelModel = new \App\Models\Nivel(); // Obtém a tabela de funções do banco
      $usuarios_data = $usuarios->find();

      $ativos = service('request')->getPost('ativos'); // Obtém a informação POST fornecida via AJAX para listar usuários ativos
      $desativados = service('request')->getPost('desativados'); // Obtém a informação POST fornecida via AJAX para listar usuários desativados
      $lista = "";
      $lista_ids = array();
      $lista_completa = array();
      $id_temp = 0;

      foreach ($usuarios_data as $key => $value) {

        $nivel = Ferramentas::array_index($nivelModel
          ->where('id', $value['nivel_id'])
          ->where('status', 'ativo')
          ->first(), ['nome']);
        // Cria a lista com base nos usuários ativos ou desativados, dependendo da solicitação
        if (($ativos == 'true' && $value['status'] == 'ativo')) { //verifica se é para mostrar os com estus ativo//verifica se é para mostrar os com estus ativo
          $lista .= '
      <tr id="" >
       <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . $value['nome'] . '</p></td>
       <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">  ********  </td>
       <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . $nivel . '</td>
       <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . $value['email'] . '</td>
       <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . $value['whatsapp'] . '</td>
       
 
       <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . ucfirst($value['status']) . '</td>
       <td><button name="cadastarar" type="submit" onclick="desativar(\'' . $id_temp . '\')" class="btn btn-outline-danger btn-lg btn-block"> Desativar </button></td>
       <td><button name="cadastarar" type="submit" class="btn btn-outline-warning btn-lg btn-block" onclick="modal_modificar(\'modal_' . $id_temp . '\')"> Modificar </button></td>
      </tr>
      ';
        } else if (($desativados == 'true' && $value['status'] == 'desativado')) { //verifica se é para mostrar os com estus desativado
          $lista .= '
          <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . $value['nome'] . '</p></td>
          <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">  ********  </td>
          <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . $nivel . '</td>
          <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . $value['email'] . '</td>
          <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . $value['whatsapp'] . '</td>
          
       <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . ucfirst($value['status']) . '</td>
       <td><button name="cadastarar" type="submit" onclick="ativar(\'' . $id_temp . '\')" on class="btn btn-outline-success btn-lg btn-block"> Ativar </button></td>
       <td><button name="cadastarar" type="submit" class="btn btn-outline-warning btn-lg btn-block" onclick="modal_modificar(\'modal_' . $id_temp . '\')"> Modificar </button></td>
      </tr>
      ';
        }
        $value['nivel'] = $nivel;
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
}
