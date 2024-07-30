<?php

namespace App\Controllers;

use App\Controllers\Ferramentas;
use Config\App;

class DesenhoPost extends Ferramentas
{
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


  function nome_desenho()
  {
    if ($this->request->isAJAX()) {
      session_start();

      $id = service('request')->getPost('id'); //pega a informação post que foi fornecida via ajax se é para pegar os usuarios ativos

      $lista = $_SESSION["lista_completa"][$id];

      $data = ['ok' => true, 'nome' => Ferramentas::decodificador($lista['nome'])];
      return $this->response->setJSON($data);
    }
  }


    /**
   * Move-o um desenho especificado para o diretório de lixo.
   *
   * Esta função é responsável por apagar um desenho especificado ou movê-lo para o diretório de lixo.
   *
   * @return 
   */
  function apagar_desenho()
  {
    if ($this->request->isAJAX()) {
      session_start();
      $id_temp = service('request')->getPost('id');

      // Obtém informações sobre o desenho a partir da sessão.
      $lista = $_SESSION["lista_completa"][$id_temp];
      $id = $_SESSION["lista"][$id_temp];
      $caminho = $lista['caminho'];
      $nome = Ferramentas::decodificador($lista['nome']);

      // Verifica se o desenho não existe mais.
      if (!file_exists(Ferramentas::decodificador($caminho))) {
        // Atualiza o status do desenho para 'apagado' no banco de dados.
        $db = new \App\Models\Desenhos();
        $db->update($id, ['status' => 'apagado']);

        return $this->response->setJSON(['ok' => 'false', 'mensagem' => 'Desenho apagado com sucesso ', 'mensagem_false' => 'Desenho não existe']);
      }

      // Define o novo caminho do desenho para o diretório de lixo.
      $caminho = str_replace(['c:/wl/wl_desenhos/'], [''], Ferramentas::decodificador($caminho));
      $caminho = str_replace($nome, '', $caminho);
      $caminho = 'C:/wl/lixo/wl/wl_desenhos/' . $caminho . '/';
      $caminho = str_replace('//', '/', $caminho);

      // Cria o diretório de lixo, se não existir.
      $problema = Ferramentas::criet_diretorio($caminho);

      if (count($problema) == 0) {
        // Gera um nome de arquivo único para evitar conflitos.
        do {

          $nome = Ferramentas::get_name_file($nome, false) . '_' . date('d_m_Y_H_i_s_') . rand(0, 100) . '.' . Ferramentas::get_type_file($nome);

        } while (file_exists($caminho . $nome));

        // Move o desenho para o diretório de lixo.
        if (rename(Ferramentas::decodificador($lista['caminho']), $caminho . $nome)) {
          // Registra informações sobre o desenho no banco de dados de lixo.
          $data = [
            'id_desenho' => $id,
            'individuo' => $_SESSION['usuario'],
            'data_add' => Ferramentas::codificador(date('d/m/Y H:i:s')),
            'caminho' => Ferramentas::codificador($caminho),
            'nome_desenho' => Ferramentas::codificador($nome)
          ];
          $db = new \App\Models\Lixo_desenhos();
          $db->insert($data);

          // Atualiza o status do desenho para 'apagado' no banco de dados.
          $db = new \App\Models\Desenhos();
          $db->update($id, ['status' => 'apagado']);
          return $this->response->setJSON(['ok' => 'true', 'mensagem' => 'Desenho apagado com sucesso ']);
        } else {
          return $this->response->setJSON(['ok' => 'false', 'mensagem' => 'Desenho apagado com sucesso ', 'mensagem_false' => 'Trasferencia para lixeira']);
        }
      }
      return $this->response->setJSON(['ok' => 'false', 'caminho' => $caminho]);
    }




  }


    /**
   * Substitui um desenho existente ou renomeia-lo.
   *
   * Esta função lida com a substituição de um desenho existente ou renomeação de um arquivo no sistema. Ela suporta tanto o upload de um novo arquivo quanto a renomeação de um arquivo existente.
   *
   * @return 
   */
  function subistituir_desenho()
  {
    if ($this->request->isAJAX()) {
      session_start();


      $ok = '';

      $message = '';
      $id_temp = $_SESSION["id_subistituir"];
      $lista = $_SESSION["lista_completa"][$id_temp];
      $caminho_antigo = Ferramentas::decodificador($lista['caminho']);
      $caminho_antigo = str_replace('//', '/', $caminho_antigo);


      if (file_exists($caminho_antigo)) { //para mudar o nome do arquivo
        $file = $this->request->getFile('file');
        $nome_novo = $_SESSION["novo_nome_arquivo"];



        // return $this->response->setJSON(['ok' => '2', 'mensagem' => $nome_novo]);




        $id = $_SESSION["lista"][$id_temp];


        $nome = Ferramentas::decodificador($lista['nome']);
        $nome_antigo = Ferramentas::remove_id_file(str_replace('.' . Ferramentas::get_type_file($nome), '', $nome));

        //return $this->response->setJSON(['ok' => 'teste', 'mensagem' => '1']);

        // $caminho = str_replace($nome,'',$caminho);

        $caminho = str_replace($nome, '', $caminho_antigo);
        $novo_nome = $nome;





        if (Ferramentas::codificador($nome_novo) != Ferramentas::codificador($nome_antigo)) {
          do {
            $random = rand(10000, 99999);
            $novo_nome = $nome_novo . '_' . $random . '_.' . Ferramentas::get_type_file($nome);
            $caminho = str_replace($nome, '', $caminho_antigo) . $novo_nome;
          } while (file_exists($caminho));
        }


        if ($file != null) {

          if ($file->isValid() && !$file->hasMoved()) {

            unlink($caminho_antigo);

            if ($file->move(str_replace($nome, '', $caminho_antigo), $novo_nome)) {


              $db = new \App\Models\Desenhos();
              $db->update($id, [
                'caminho' => Ferramentas::codificador($caminho),
                'nome' => Ferramentas::codificador($novo_nome)
              ]);

              $db = new \App\Models\Historico_desenhos();
              $db->insert([
                'id_desenhos' => $id,
                'nome' => Ferramentas::codificador($nome_antigo),
                'individuo' => $_SESSION['usuario'],
                'data_hora_mod' => Ferramentas::codificador(date('d/m/Y H:i:s')),
                'status' => 'subistituiu o arquivo'

              ]);




              $ok = 'true';
              $message = 'Arquivo enviado com sucesso.';
            } else {

              $ok = 'false';
              $message = 'Erro ao mudar o arquivo.';
            }

          }


        } else {
          if (Ferramentas::codificador($nome_novo) != Ferramentas::codificador($nome_antigo)) {

            if (rename($caminho_antigo, $caminho)) {
              $db = new \App\Models\Desenhos();
              $db->update($id, [
                'caminho' => Ferramentas::codificador($caminho),
                'nome' => Ferramentas::codificador($novo_nome)
              ]);

              $db = new \App\Models\Historico_desenhos();
              $db->insert([
                'id_desenhos' => $id,
                'nome' => Ferramentas::codificador($nome_antigo),
                'individuo' => $_SESSION['usuario'],
                'data_hora_mod' => Ferramentas::codificador(date('d/m/Y H:i:s')),
                'status' => 'renomeado'

              ]);
              $ok = 'true';
              $message = 'Arquivo renomeado com sucesso.';
            } else {
              $ok = 'false';
              $message = 'Erro ao renomear o arquivo.';
            }
          }
        }

      } else {
        $ok = 'false';
        $message = 'Arquivo inexistente.';

      }

      return $this->response->setJSON(['ok' => $ok, 'mensagem' => $message]);


      // if ($file->isValid() && !$file->hasMoved()) {
      //   if ($nome_novo != $nome_antigo) {

      //     do {
      //       $random = rand(10000, 99999);

      //     } while (is_dir($caminho . $nome_novo . '_' . $random . '_' . Ferramentas::get_type_file($nome)));

      //     if ($file->move($caminho, $nome_novo . '_' . $random . '_' . Ferramentas::get_type_file($nome))) {
      //       $ok = 'true';
      //       $message = 'Arquivo enviado com sucesso.';
      //     }


      //   } else {





      //     if ($file->move($caminho, $nome)) {
      //       $ok = 'true';
      //       $message = 'Arquivo enviado com sucesso.';
      //     }
      //   }



      // } else {
      //   $ok = 'false';
      // }





      //       if (!rename(Ferramentas::decodificador($lista['caminho']), $caminho.$nome)) {
      //       $data = [
      //           'id_desenho' => $id,
      //           'individuo' => $_SESSION['usuario'],
      //           'data_add' => Ferramentas::codificador(date('d/m/Y H:i:s')),
      //           'caminho' => Ferramentas::codificador($caminho),
      //           'nome_desenho' => Ferramentas::codificador($nome)
      //       ]; 
      //       $db = new \App\Models\Lixo_desenhos();
      //       $db->insert($data);
      //       $db = new \App\Models\Desenhos();
      //       $db->update($id,['status' => 'apagado']);
      //       return $this->response->setJSON(['ok' => 'true']);
      //   }
      // return $this->response->setJSON(['ok' => 'true']);
      // return $this->response->setJSON(['ok' => $ok,'message'=>$message,'nome_novo'=>$nome_novo,'caminho'=>$caminho,'nome_antigo'=>$nome_antigo]);
    }

  }


    /**
   * Substitui o nome de um desenho a ser trocado.
   *
   * Esta função é responsável por substituir o nome de um desenho que será trocado, obtendo o nome base do desenho e removendo o ID.
   *
   * @return 
   */
  function subistituir_desenho_modal()
  {
    if ($this->request->isAJAX()) {
      session_start();

      // Salva apenas algumas informações da sessão e o `id` do desenho a ser substituído.
      $_SESSION = ['funcao' => $_SESSION['funcao'], 'usuario' => $_SESSION['usuario'], 'usuario_nome' => $_SESSION['usuario_nome'], 'lista_completa' => $_SESSION["lista_completa"]];
      $id_temp = service('request')->getPost('id');
      $_SESSION["id_subistituir"] = $id_temp;

      // Obtém informações sobre o desenho a ser substituído.
      $lista = $_SESSION["lista_completa"][$id_temp];
      $nome = Ferramentas::decodificador($lista['nome']);

      // Remove a extensão do nome do desenho e o ID do nome.
      $nome = str_replace('.' . Ferramentas::get_type_file($nome), '', $nome);
      $nome = Ferramentas::remove_id_file($nome);

      // Retorna o nome modificado como resposta.
      return $this->response->setJSON(['nome' => $nome]);
    }
  }


    /**
   * Define um novo nome para um desenho que será substituído.
   *
   * Esta função é responsável por definir um novo nome para um desenho que será substituído e armazenar esse nome na sessão para uso posterior.
   *
   * @return 
   */
  function desenho_novo_nome()
  {
    if ($this->request->isAJAX()) {
      session_start();

      // Obtém o novo nome do desenho a ser definido.
      $nome_novo = service('request')->getPost('nome');

      // Armazena o novo nome na sessão para uso posterior.
      $_SESSION["novo_nome_arquivo"] = $nome_novo;
      return $this->response->setJSON(['mensagem' => $nome_novo]);

    }
  }


    /**
   * Recoloca um desenho duplicado no banco de dados.
   *
   * Esta função lida com a recolocação de um desenho duplicado no banco de dados. Ela cria uma cópia do desenho original com um novo nome e insere os dados duplicados no banco de dados.
   *
   * @return 
   */
  function recolocar_desenho()
  {
    if ($this->request->isAJAX()) {
      session_start();

      $id = service('request')->getPost('id'); // Obtém o ID do desenho a ser duplicado via requisição AJAX.
      $caminho = '';


      $linhaParaDuplicar = $_SESSION["lista_primordial"][$id]; // Obtém as informações da linha a ser duplicada.

      $id_lista = $_SESSION["lista"][$id]; // Obtém o ID da linha original.

      if ($linhaParaDuplicar) {
        $novaEntrada = $linhaParaDuplicar;

        // Remove o ID e o cortador da nova entrada, para evitar conflitos.
        unset($novaEntrada['id'], $novaEntrada['cortador']);

        $novaEntrada['data_hora_add'] = Ferramentas::codificador(date('d/m/Y H:i'));
        $novaEntrada['status'] = 'corte';

        $desenhos = new \App\Models\Desenhos();
        $caminho = Ferramentas::decodificador($novaEntrada['caminho']);
        $nome = Ferramentas::decodificador($novaEntrada['nome']);
        $extencao = '.' . Ferramentas::get_type_file($nome);

        // Extrai o caminho do nome do arquivo.
        $caminho = str_replace($nome, '', $caminho);
        $nome = str_replace('.' . Ferramentas::get_type_file($nome), '', $nome);



        do {
          $radom = rand(1000, 9999);
          $novo_nome = Ferramentas::remove_id_file(substr($nome, 19)) . '_' . $radom . "_" . $extencao;
        } while (file_exists($caminho . $novo_nome));

        // Faz uma cópia do arquivo original com o novo nome.
        copy($caminho . $nome . $extencao, $caminho . $novo_nome);

        $novaEntrada['caminho'] = Ferramentas::codificador($caminho . $novo_nome);
        $novaEntrada['nome'] = Ferramentas::codificador($novo_nome);
        //return $this->response->setJSON(['1' => $caminho. $novo_nome, '2'=>$nome]);

        // Insere a nova entrada no banco de dados.
        $desenhos->insert($novaEntrada);

        // Atualiza o status da entrada original para 'duplicado' com o novo ID.
        $desenhos->update($id_lista, ['status' => 'duplicado_' . $desenhos->insertID()]);


      }





      $data = ['ok' => true, 'nome' => $caminho];
      return $this->response->setJSON($data);
    }
  }

  


  
}