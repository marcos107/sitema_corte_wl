<?php

namespace App\Controllers;

use App\Controllers\Ferramentas;
use Exception;

class DesenhistaPost extends Ferramentas
{
  /**
   * Cria um diretório temporário e associa-o a uma sessão de usuário.
   *
   * Esta função é responsável por criar um diretório temporário no sistema de arquivos, associá-lo à sessão de usuário
   * e registrá-lo no banco de dados como um diretório temporário em processo. O diretório é criado com um nome aleatório
   * único e é usado para armazenar temporariamente arquivos relacionados a uma sessão de usuário.
   *
   * @return 
   */
  function criar_temp()
  {
    // Verifica se a requisição é AJAX.
    if ($this->request->isAJAX()) {
      // Inicia a sessão.
      session_start();

      do {
        // Gera um nome de diretório aleatório único na pasta 'C:/wl/temp/'.
        $targetDirectory = 'C:/wl/temp/' . rand(10000, 99999) . '/';
      } while (is_dir($targetDirectory));

      // Verifica se o diretório temporário foi criado com sucesso.
      if (!mkdir($targetDirectory, 0777, true)) {
        return $this->response->setJSON(['ok' => 'false']);
      }

      // Associa o diretório temporário à sessão do usuário.
      $_SESSION['pasta_temp'] = $targetDirectory;

      // Prepara os dados a serem inseridos no banco de dados.
      $data = [
        'diretorio' => Ferramentas::codificador($targetDirectory),
        'individuo' => $_SESSION['usuario'],
        'data_add' => Ferramentas::codificador(date('d/m/Y H:i:s')),
        'status' => 'processando'
      ];

      // Cria uma instância do modelo de Desenhos_temp.
      $db = new \App\Models\Desenhos_temp();

      // Insere os dados do diretório temporário no banco de dados.
      $db->insert($data);

      return $this->response->setJSON(['ok' => 'true']);
    }
  }
  /**
   * Adiciona um arquivo temporário a um diretório temporário associado à sessão do usuário.
   *
   * Esta função é responsável por receber um arquivo enviado pelo usuário e adicioná-lo a um diretório temporário
   * associado à sessão do usuário. O arquivo é movido para o diretório temporário e uma resposta JSON é retornada
   * indicando se a operação foi bem-sucedida.
   *
   * @return 
   */
  function desenho_adicionar_temp()
  {
    // Verifica se a requisição é AJAX.
    if ($this->request->isAJAX()) {
      $ok = 'false';
      $message = 'Erro ao enviar o arquivo.';

      // Obtém o arquivo enviado pelo usuário.
      $file = $this->request->getFile('file');

      if ($file->isValid() && !$file->hasMoved()) {
        // Define o diretório de destino para salvar os arquivos enviados.

        // Inicia a sessão para acessar o diretório temporário associado à sessão do usuário.
        session_start();
        $targetDirectory = $_SESSION['pasta_temp'];

        // Move o arquivo para o diretório de destino.
        if ($file->move($targetDirectory, $file->getName())) {
          $ok = 'true';
          $message = 'Arquivo enviado com sucesso.';
        }
      }

      // Retorna a resposta em formato JSON.
      return $this->response->setJSON(['ok' => $ok, 'message' => $message]);
    }
  }


  /**
   * Adiciona desenhos do diretório temporário à sessão do usuário após aplicar filtros.
   *
   * Esta função é responsável por adicionar desenhos do diretório temporário associado à sessão do usuário à sessão, após
   * aplicar filtros para verificar se as extensões dos arquivos correspondem aos filtros permitidos. Os desenhos permitidos
   * são adicionados à sessão, enquanto os desenhos não permitidos são excluídos do diretório temporário.
   *
   * @return 
   */
  function desenho_adicionar_modal()
  {
    // Verifica se a requisição é AJAX.
    if ($this->request->isAJAX()) {
      session_start();

      $msg = array();
      $ok = false;
      $targetDirectory = $_SESSION['pasta_temp'];
      $desenhos = Ferramentas::map_pasta($targetDirectory);

      $filtros = array();
      $filtro = new \App\Models\Filtros(); // Obtém os filtros da tabela do banco de dados.
      $filtro_data = $filtro->find();

      // Cria uma lista de filtros ativos a partir dos dados do banco.
      foreach ($filtro_data as $key => $value) {
        if ($value['status'] == 'ativo') {
          $filtros[] = Ferramentas::decodificador($value['nome']);
        }
      }

      foreach ($desenhos as $key => $value) {
        // Verifica se a extensão do arquivo não está na lista de filtros permitidos.
        if (!in_array(Ferramentas::get_type_file($value), $filtros)) {
          // Registra uma mensagem indicando que o tipo de arquivo não é permitido.
          $msg[Ferramentas::get_name_file($value)] = "Tipo de arquivo (." . Ferramentas::get_type_file($value) . ") não permitido.";

          // Remove o arquivo do diretório temporário.
          unlink($desenhos[$key]);
        }
      }

      // Obtém a lista de desenhos restante no diretório temporário.
      $desenhos = Ferramentas::map_pasta($targetDirectory);

      if (count($desenhos) != 0) {
        foreach ($desenhos as $key => $value) {
          // Remove a extensão dos nomes dos arquivos.
          $desenhos[$key] = Ferramentas::get_name_file($value);
        }
        $ok = true;
      } else {
        // Remove o diretório temporário se não houver desenhos restantes.
        rmdir($targetDirectory);
      }

      // Armazena a lista de desenhos na sessão do usuário.
      $_SESSION['desenhos'] = $desenhos;

      return $this->response->setJSON(['ok' => $ok, 'desenhos' => $desenhos, 'msg' => $msg]);
    }
  }

  /**
   * Adiciona desenhos após aplicar validações e move-os para diretórios baseados em informações associadas.
   *
   * Esta função é responsável por adicionar desenhos após aplicar validações a partir dos dados fornecidos. Ela verifica a existência
   * de informações associadas (por exemplo, empresa, prioridade, finalidade) e move os desenhos para diretórios correspondentes.
   * Além disso, atualiza o banco de dados com informações sobre os desenhos adicionados.
   *
   * @return 
   */

  function desenhos_add()
  {
    if ($this->request->isAJAX()) {
      session_start();
      $_SESSION['desenho_add_proc'] = isset ($_SESSION['desenho_add_proc']) ? $_SESSION['desenho_add_proc'] : FALSE;
      if ($_SESSION['desenho_add_proc']) {
        return;
      } else {
        $_SESSION['desenho_add_proc'] = TRUE;
      }

      $desenhos = service('request')->getPost('desenhos');
      $msg = array();
      $ok = array();
      $violacao = array();

      // Inicializa modelos de banco de dados para obter informações associadas (empresa, prioridade, finalidade, etc.).
      $empresa = new \App\Models\Empresa();
      $empresa_data = $empresa->find();

      $prioridade = new \App\Models\Prioridade();
      $prioridade_data = $prioridade->find();

      $finalidade = new \App\Models\Finalidade();
      $finalidade_data = $finalidade->find();

      $empreendimento = new \App\Models\Empreendimentos();
      $empreendimento_data = $empreendimento->find();

      $tag = new \App\Models\Tag();
      $tag_data = $tag->find();

      foreach ($desenhos as $key => $value) {
        $base_dir = 'c:/wl/wl_desenhos/';

        // Constrói o caminho base do diretório para armazenar o desenho.
        $base_dir .= Ferramentas::codificador($value["empresa"]) . '/';
        $base_dir .= Ferramentas::codificador($value["empreendimento"]) . '/';
        $base_dir .= Ferramentas::codificador($value["finalidade"]) . '/';
        $base_dir .= Ferramentas::codificador($value["tag1"]) . '/';
        $empresa_id = '';
        $prioridade_id = '';
        $finalidade_id = '';
        $empreendimento_id = '';
        $tag1_id = '';
        $tag2_id = '';
        $tag3_id = '';
        $erro = false;

        // Valida informações associadas (empresa, prioridade, finalidade, etc.).
        if ($value["empresa"] != '') {
          if (Ferramentas::array_pesquisa($empresa_data, 'nome', Ferramentas::codificador($value["empresa"])) == array()) {

            //violacao
            $msg["Empresa " . $value["desenho"]] = 'Não existe.';
            $violacao[] = "desenhos_add Empresa não exist";
            $erro = true;
          } else {
            $empresa_id = Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'nome', Ferramentas::codificador($value["empresa"])), ['id']);
          }

        } else {
          $msg["Empresa " . $value["desenho"]] = 'Não selecionada.';
          $erro = true;

        }

        // Valida a prioridade associada.
        if ($value["prioridade"] != '') {
          if (Ferramentas::array_pesquisa($prioridade_data, 'nome', Ferramentas::codificador($value["prioridade"])) == array()) {

            //violacao
            $msg["Prioridade " . $value["desenho"]] = 'Não existe.';
            $violacao[] = "desenhos_add Prioridade não exist";
            $erro = true;

          } else {
            $prioridade_id = Ferramentas::array_index(Ferramentas::array_pesquisa($prioridade_data, 'nome', Ferramentas::codificador($value["prioridade"])), ['id']);
          }

        } else {
          $msg["Prioridade " . $value["desenho"]] = 'Não selecionado.';
          $erro = true;
        }

        // Valida a finalidade associada.
        if ($value["finalidade"] != '') {
          if (Ferramentas::array_pesquisa($finalidade_data, 'nome', Ferramentas::codificador($value["finalidade"])) == array()) {

            //violacao
            $msg["Finalidade " . $value["desenho"]] = 'Não existe.';
            $violacao[] = "desenhos_add Finalidade não exist";
            $erro = true;

          } else {
            $finalidade_id = Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'nome', Ferramentas::codificador($value["finalidade"])), ['id']);
          }

        } else {
          $msg["Finalidade " . $value["desenho"]] = 'Não selecionado.';
          $erro = true;
        }

        // Valida o empreendimento associado.
        if ($value["empreendimento"] != '') {
          if (Ferramentas::array_pesquisa($empreendimento_data, 'nome', Ferramentas::codificador($value["empreendimento"])) == array()) {

            //violacao
            $msg["Empreendimento " . $value["desenho"]] = 'Não existe.';
            $violacao[] = "desenhos_add Empreendimento não exist";
            $erro = true;
          } else {
            $empreendimento_id = Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'nome', Ferramentas::codificador($value["empreendimento"])), ['id']);
          }

        } else {
          $msg["Empreendimento " . $value["desenho"]] = 'Não selecionado.';
          $erro = true;
        }

        // Valida a tag1 associada.
        if ($value["tag1"] != '') { // -----------------
          if (Ferramentas::array_pesquisa($tag_data, 'nome', Ferramentas::codificador($value["tag1"])) == array()) {
            //violacao
            $msg["Tag1 " . $value["desenho"]] = 'Não existe.';
            $violacao[] = "desenhos_add Tag1 não exist";
            $erro = true;
          } else {
            $tag1_id = Ferramentas::array_index(Ferramentas::array_pesquisa($tag_data, 'nome', Ferramentas::codificador($value["tag1"])), ['id']);
          }
          //$base_dir .= Ferramentas::codificador($value["tag1"]) . '/';

          // Valida a tag2 associada.
          if ($value["tag2"] != '') { // -----------------
            if (Ferramentas::array_pesquisa($tag_data, 'nome', Ferramentas::codificador($value["tag2"])) == array()) {
              //violacao
              $msg["Tag2 " . $value["desenho"]] = 'Não existe.';
              $violacao[] = "desenhos_add Tag2 não exist";
              $erro = true;
            } else {
              $tag2_id = Ferramentas::array_index(Ferramentas::array_pesquisa($tag_data, 'nome', Ferramentas::codificador($value["tag2"])), ['id']);
            }
            $base_dir .= Ferramentas::codificador($value["tag2"]) . '/';

            // Valida a tag3 associada.
            if ($value["tag3"] != '') { // -----------------
              if (Ferramentas::array_pesquisa($tag_data, 'nome', Ferramentas::codificador($value["tag3"])) == array()) {
                //violacao
                $msg["Tag3 " . $value["desenho"]] = 'Não existe.';
                $violacao[] = "desenhos_add Tag3 não exist";
                $erro = true;
              } else {
                $tag3_id = Ferramentas::array_index(Ferramentas::array_pesquisa($tag_data, 'nome', Ferramentas::codificador($value["tag3"])), ['id']);
              }
              $base_dir .= Ferramentas::codificador($value["tag3"]) . '/';

            }
          }

        } else {
          $msg["Subpasta-01 " . $value["desenho"]] = 'Não selecionado a Subpasta-01';
          $erro = true;
        }


        if (!$erro) {
          // Remove espaços em branco do caminho base.
          $base_dir = str_replace(' ', '', $base_dir);

          // Cria o diretório base se ele não existir.
          $problema = Ferramentas::criet_diretorio($base_dir);
          if (count($problema) == 0) {

            $desenho = $base_dir . $value["desenho"];
            $desenho_temp = $_SESSION['pasta_temp'] . $value["desenho"];
            $desenho_desmenber = str_replace('.' . Ferramentas::get_type_file($desenho), '', $desenho);
            $desenho_typer = '.' . Ferramentas::get_type_file($desenho);

            // Gera um nome de arquivo único para evitar conflitos.
            do {
              $desenho = $desenho_desmenber . '_' . rand(0, 1000) . '_' . $desenho_typer;
            } while (file_exists($desenho));

            if (!rename($desenho_temp, $desenho)) {
              $ok[] = false;
              $msg[$value["desenho"]] = 'Erro ao trasferir o desenho.';
            } else if (!file_exists($desenho)) {
              $ok[] = false;
              $msg[$value["desenho"]] = 'Erro ao trasferir o desenho.';
            } else {

              $msg[$value["desenho"]] = $desenho;
              $ok[] = true;


              // Insere informações sobre o desenho no banco de dados.
              $db = new \App\Models\Desenhos();
              $data = [
                'nome' => Ferramentas::codificador(Ferramentas::get_name_file($desenho)),
                'caminho' => Ferramentas::codificador($desenho),
                'desenhista' => $_SESSION['usuario'],
                'status' => 'corte',
                'prioridade' => $prioridade_id,
                'finalidade' => $finalidade_id,
                'empreendimento' => $empreendimento_id,
                'empresa' => $empresa_id,
                'data_hora_add' => Ferramentas::codificador(date('d/m/Y H:i'))

              ];
              $db->insert($data);


            }

          } else {
            $msg[$value["desenho"]] = 'Erro ao criar caminho.';
          }



        } else {
          $ok[] = false;


        }





      }
      $desenhos = Ferramentas::map_pasta($_SESSION['pasta_temp']);
      if (count($desenhos) == 0) {
        //apaga a pasta temp se estiver vazia
        $db = new \App\Models\Desenhos_temp();
        $db_data = $db->find();
        $id_desenhos = Ferramentas::array_index(Ferramentas::array_pesquisa_mult($db_data, ['diretorio', 'status'], [Ferramentas::codificador($_SESSION['pasta_temp']), 'processando']), ['id']);
        if ($id_desenhos != '') {
          $db->update($id_desenhos, ['status' => 'finalizado', 'data_finalizado' => Ferramentas::codificador(date('d/m/Y H:i:s'))]);

        }
      }
      if (count($violacao) != 0) {
        // Registra as violações no banco de dados.
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
      $data = [
        'ok' => $ok,
        'msg' => $msg

      ];
      $_SESSION['desenho_add_proc'] = false;
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

  /**
   * Lista desenhos com status de "corte" ou "cortando".
   *
   * Esta função retorna uma lista de desenhos que possuem status "corte" ou "cortando".
   *
   * @return 
   */
  function lista_corte() //rece um post via ajax pedindo para listar os usuarios
  {
    if ($this->request->isAJAX()) {
      $desenhos = new \App\Models\Desenhos(); // Instancia o modelo de dados para desenhos.

      $prioridade = new \App\Models\Prioridade(); // Instancia o modelo de dados para prioridades.

      $finalidade = new \App\Models\Finalidade(); // Instancia o modelo de dados para finalidades.

      $empresa = new \App\Models\Empresa(); // Instancia o modelo de dados para empresas.

      $empreendimento = new \App\Models\Empreendimentos(); // Instancia o modelo de dados para empreendimentos.
      $usuario = new \App\Models\Usuarios();

      $prioridade_data = $prioridade->find(); // Recupera dados de prioridades do banco de dados.
      $finalidade_data = $finalidade->find(); // Recupera dados de finalidades do banco de dados.
      $empresa_data = $empresa->find(); // Recupera dados de empresas do banco de dados.
      $empreendimento_data = $empreendimento->find(); // Recupera dados de empreendimentos do banco de dados.
      $desenhos_data = $desenhos->find(); // Recupera dados de desenhos do banco de dados.
      $usuario_data = $usuario->find();

      $lista = "";

      foreach ($desenhos_data as $key => $value) {
        // Verifica se o status do desenho é "corte" ou "cortando".
        if (Ferramentas::decodificador($value['status']) == "corte" || Ferramentas::decodificador($value['status']) == 'cortando') {
          // Obtém a prioridade do desenho.
          $prioridade_desenho = Ferramentas::array_pesquisa($prioridade_data, 'id', $value['prioridade']);

          // Constrói a linha da tabela com informações do desenho.
          $lista .= '
      <tr>

       
       <td bgcolor="' . Ferramentas::decodificador(Ferramentas::array_index($prioridade_desenho, ['cor'])) . '"><span class="marca_texto">' . Ferramentas::array_index($prioridade_desenho, ['nome']) . '</span></td>
       <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['desenhista']), ['nome'])) . '</td>
       <td>' . Ferramentas::decodificador(Ferramentas::remove_id_file(Ferramentas::decodificador($value['nome']))) . '</td>
       <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ['nome'])) . '</td>
       <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ['nome'])) . '</td>
       <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ['nome'])) . '</td>
       <td>' . Ferramentas::decodificador($value['status']) . '</td>
       <td>' . Ferramentas::decodificador($value['data_hora_add']) . '</td>
      </tr>
      ';

        }
      }

      $data = [
        "lista" => $lista
      ];

      return $this->response->setJSON($data);
    }
  }

  /**
   * Lista tags de acordo com seu status (ativo/desativado).
   *
   * Esta função retorna uma lista de tags com base no status (ativo ou desativado) fornecido via AJAX.
   *
   * @return 
   */
  function desenho_tag()
  {
    if ($this->request->isAJAX()) {
      session_start();
      $tag = new \App\Models\Tag(); // Instancia o modelo de dados para tags.

      $tag_data = $tag->find(); // Recupera dados de tags do banco de dados.
      $ativos = service('request')->getPost('ativos'); // Verifica se é para listar tags ativas.
      $desativados = service('request')->getPost('desativados'); // Verifica se é para listar tags desativadas.
      $lista = "";
      $lista_ids = array();
      $id_temp = 0;
      $lista_completa = array();
      foreach ($tag_data as $key => $value) { //cria a lista
        if (($ativos == 'true' && Ferramentas::decodificador($value['status']) == 'ativo')) { //verifica se é para mostrar os com estus ativo
          // Se a tag é ativa e deve ser listada, gera uma linha da tabela com opção "Desativar".
          $lista .= '
        <tr>
         <td><p onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['nome']) . '</p></td>
         <td onclick="modal_modificar(\'modal_' . $id_temp . '\')">' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
         <td><button name="cadastarar" type="submit" onclick="desativar(\'' . $id_temp . '\')" class="btn btn-outline-danger btn-lg btn-block"> Desativar </button></td>
        </tr>
        ';
        } else if (($desativados == 'true' && Ferramentas::decodificador($value['status']) == 'desativado')) { //verifica se é para mostrar os com estus desativado
          // Se a tag é desativada e deve ser listada, gera uma linha da tabela com opção "Ativar".
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
   * Função para cadastrar uma nova tag.
   *
   * Esta função é usada para cadastrar uma nova tag com base em dados fornecidos via AJAX.
   *
   * @return 
   */
  function desenho_tag_cadastro()
  {
    if ($this->request->isAJAX()) {
      $msg = array(); // Inicializa um array para mensagens de erro.
      $ok = false; // Inicializa uma variável de status para falso.
      $violacao = array(); // Inicializa um array para violações.

      $tag = service('request')->getPost('tag'); // Obtém o nome da tag enviado via POST.

      if (strlen($tag) > 30) {
        // Verifica se o nome da tag excedeu o tamanho máximo de 17 caracteres.
        $msg['Subpasta'] = "Nome da Subpasta excedeu o tamanho máximo de 30 caracter";
        $violacao[] = "desenho_tag_cadastro Tag excedeu o tamanho máximo";
      }

      if (strlen($tag) < 1) {
        // Verifica se o nome da tag possui o tamanho mínimo de 1 caractere.
        $msg['Subpasta'] = "Nome da Subpasta não possui o tamanho mínimo de 1 caracter";
      } else {
        if (Ferramentas::codificador($tag) == '') {
          // Verifica se o nome da tag possui caracteres não permitidos.
          $msg['Subpasta'] = "Nome da Subpasta possui caracteres não permitidos";
          $violacao[] = "desenho_tag_cadastro Tag possui caracteres não permitidos";
        }
      }


      session_start();
      if (count($msg) == 0) {
        $db = new \App\Models\Tag();


        $tag_data = $db->find();

        if (count(Ferramentas::array_pesquisa($tag_data, 'nome', Ferramentas::codificador($tag))) == 0) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 
          // Verifica se a tag com o mesmo nome já existe no banco de dados.
          // Se não existir, insere uma nova tag.
          $date = [
            'nome' => Ferramentas::codificador($tag),
            'data_add' => date('d/m/Y H:i'),
            'status' => 'ativo',
            'responsavel' => $_SESSION['usuario']
          ];

          $db->insert($date);
          $ok = true;
        } else {
          $msg["Subpasta"] = 'Nome da Subpasta já existente';
          $violacao[] = "desenho_tag_cadastro Tag já existente";
        }


      }
      if (count($violacao) != 0) {
        $db = new \App\Models\Violacao();
        foreach ($violacao as $key => $value) {
          // Registra violações no banco de dados, se houver.
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
   * Função para atualizar configurações de uma tag.
   *
   * Esta função é usada para atualizar as configurações de uma tag com base em dados fornecidos via AJAX.
   *
   * @return 
   */
  function config_tag_update()
  {
    if ($this->request->isAJAX()) {
      $msg = array(); // Inicializa um array para mensagens de erro.
      $ok = false; // Inicializa uma variável de status para falso.
      $violacao = array(); // Inicializa um array para violações.

      $tag = service('request')->getPost('tag'); // Obtém o nome da tag enviado via POST.

      if (strlen($tag) > 30) {
        // Verifica se o nome da tag excedeu o tamanho máximo de 17 caracteres.
        $msg['Subpasta'] = "Nome da Subpasta excedeu o tamanho máximo de 30 caracter";
        $violacao[] = "desenho_tag_cadastro Tag já existente";
      }

      if (strlen($tag) < 1) {
        // Verifica se o nome da tag possui o tamanho mínimo de 1 caractere.
        $msg['Subpasta'] = "Nome da Subpasta não possui o tamanho mínimo de 1 caracter.";
      } else {
        if (Ferramentas::codificador($tag) == '') {
          // Verifica se o nome da tag possui caracteres não permitidos.
          $msg['Subpasta'] = "Nome da Subpasta possui caracteres não permitidos";
          $violacao[] = "desenho_tag_cadastro Tag possui caracteres não permitidos";
        }
      }


      session_start();
      if (count($msg) == 0) {
        $db = new \App\Models\tag();

        $id1 = service('request')->getPost('id'); // Obtém o ID da tag enviado via POST.
        $id = $_SESSION['lista'][$id1]; // Obtém o ID da tag a partir de uma lista.
        $tag_data = $db->find();

        // Verifica se o nome da tag não é duplicado e se houve alterações.
        if ((count(Ferramentas::array_pesquisa_mult($tag_data, ['status', 'nome'], ['ativo', Ferramentas::codificador($tag)])) == 0) || (count(Ferramentas::array_pesquisa_mult($tag_data, ['status', 'nome'], ['novo', Ferramentas::codificador($tag)])) == 0)) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 

          $alteracao = new \App\Models\Alteracoes();

          $data = [
            "individuo" => $_SESSION["usuario"],
            "id_item" => $id,
            "antes" => Ferramentas::array_index(Ferramentas::array_pesquisa($tag_data, 'id', $id), ['nome']),
            "depois" => Ferramentas::codificador($tag),
            "item" => "tag",
            "info_mais" => "nome",
            "data_add" => Ferramentas::codificador(date('d/m/Y H:i'))

          ];
          $alteracao->insert($data);


          $date = [
            'nome' => Ferramentas::codificador($tag),

          ];

          $db->update($id, $date);

          $ok = true;
        } else if (count(Ferramentas::array_pesquisa_mult($tag_data, ['id', 'nome'], [$id, Ferramentas::codificador($tag)])) != 0) {
          $msg["Modificar"] = 'Não foi feita nenhuma alteração.';
        } else {
          $msg["Subpasta"] = 'Nome da Subpasta já existente';
          $violacao[] = "desenho_tag_cadastro Tag já existente";
        }
      }


    }
    if (count($violacao) != 0) {
      $db = new \App\Models\Violacao();
      foreach ($violacao as $key => $value) {
        // Registra violações no banco de dados, se houver.
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

  /**
   * Função para exibir as configurações de uma tag em um modal.
   *
   * Esta função é usada para exibir as configurações de uma tag em um modal com base em dados fornecidos via AJAX.
   *
   * @return 
   */
  function config_tag_modal()
  {
    if ($this->request->isAJAX()) {
      session_start();
      $ok = false;
      $id = service('request')->getPost('id'); //pega a informação post que foi fornecida via ajax se é para pegar os usuarios ativos

      // Obtém informações da tag da lista de tags armazenada na sessão.
      $lista = $_SESSION["lista_completa"][$id];

      // A função atualmente não verifica se um desenho está associado a essa tag.
      // Portanto, a variável $ok está definida como false.

      $data = [
        "nome" => Ferramentas::decodificador($lista['nome']),
        // Obtém o nome da tag e a decodifica.
        "desenho" => $ok,
        // Define se um desenho está associado (neste caso, sempre falso).
        "status" => Ferramentas::decodificador($lista['status']) // Obtém o status da tag e a decodifica.


      ];
      return $this->response->setJSON($data);
    }
  }

  /**
   * Função para listar as tags ativas.
   *
   * Esta função é usada para listar todas as tags ativas armazenadas no banco de dados.
   *
   * @return 
   */
  function desenho_tag_lista()
  {
    if ($this->request->isAJAX()) {
      $tags = array(); // Inicializa um array para armazenar as tags.

      $tag = new \App\Models\Tag(); // Instancia o modelo de dados de tags.

      $tag_data = $tag->find(); // Obtém todas as tags do banco de dados.

      foreach ($tag_data as $key => $value) { // Itera sobre as tags no banco de dados. 
        if ($value['status'] == 'ativo') { // Verifica se a tag está ativa.
          $tags[] = Ferramentas::decodificador($value['nome']); // Adiciona o nome da tag decodificado ao array de tags.
        }
      }
      usort($tags, function ($a, $b) {
        return strnatcasecmp($a, $b);
      });
      // Prepara os dados de resposta em formato JSON, incluindo a lista de tags ativas.
      $data = [
        'lista' => $tags
      ];
      return $this->response->setJSON($data);
    }
  }









  /**
   * Função para cadastrar uma nova finalidade.
   *
   * Esta função é usada para cadastrar uma nova finalidade no banco de dados.
   *
   * @return 
   */
  function config_finalidade_cadastrar()
  {

    if ($this->request->isAJAX()) {
      $msg = array(); // Inicializa um array para mensagens de erro.
      $ok = false; // Inicializa uma variável de status para indicar se o cadastro foi bem-sucedido.
      $violacao = array(); // Inicializa um array para violações.

      $finalidade = service('request')->getPost('finalidade');// Obtém a finalidade do corpo da solicitação.

      if (strlen($finalidade) > 17) {// Verifica se o nome da finalidade excede o tamanho máximo.
        $msg['Finalidade'] = "Nome da Finalidade excedeu o tamanho máximo de 17 caracter";
        $violacao[] = "config_finalidade_cadastrar Finalidade excedeu o tamanho máximo";
      }

      if (strlen($finalidade) < 3) {// Verifica se o nome da finalidade possui o tamanho mínimo.
        $msg['Finalidade'] = "Nome da Finalidade não possui o tamanho mínimo de 3 caracter";
      } else {
        if (Ferramentas::codificador($finalidade) == '') { // Verifica se o nome da finalidade contém caracteres não permitidos.
          $msg['Finalidade'] = "Nome da Finalidade possui caracteres não permitidos";
          $violacao[] = "config_finalidade_cadastrar Finalidade possui caracteres não permitidos";
        }
      }


      session_start();
      if (count($msg) == 0) {// Se não houver mensagens de erro, continue o processo de cadastro.
        $db = new \App\Models\Finalidade(); // Instancia o modelo de dados para finalidades.


        $finalidade_data = $db->find(); // Obtém todas as finalidades do banco de dados.

        if (count(Ferramentas::array_pesquisa($finalidade_data, 'nome', Ferramentas::codificador($finalidade))) == 0) {  // Verifica se o nome da finalidade já existe.
          $date = [
            'nome' => Ferramentas::codificador($finalidade),
            'data_hora_add' => Ferramentas::codificador(date('d/m/Y H:i')),
            'status' => 'ativo',
            'individuo' => $_SESSION['usuario']
          ];

          $db->insert($date); // Insere a nova finalidade no banco de dados.
          $ok = true; // Define o status como verdadeiro para indicar um cadastro bem-sucedido.
        } else {
          $msg["Finalidade"] = 'Nome da Finalidade já existente';
          $violacao[] = "config_finalidade_cadastrar Finalidade já existente";
        }


      }
      if (count($violacao) != 0) {// Se houver violações, registre-as no banco de dados de violações.

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
  function config_finalidade_modal()
  {
    if ($this->request->isAJAX()) {
      session_start();
      $ok = false;
      $id = service('request')->getPost('id'); //pega a informação post que foi fornecida via ajax se é para pegar os usuarios ativos
      //retorna a lista para o ajax
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
  function config_finalidade_update()
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

        $id1 = service('request')->getPost('id');
        $id = $_SESSION['lista'][$id1];
        $finalidade_data = $db->find();
        $desenhos = new \App\Models\Desenhos();
        $desenhos_data = $desenhos->find();
        $lista = $_SESSION["lista_completa"][$id1];
        if (count(Ferramentas::array_pesquisa($desenhos_data, 'finalidade', $lista['id'])) == 0) {
          if (count(Ferramentas::array_pesquisa($finalidade_data, 'nome', Ferramentas::codificador($finalidade))) == 0) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 
            $date = [
              'nome' => Ferramentas::codificador($finalidade),

            ];

            $db->update($id, $date);

            $ok = true;
          } else if (count(Ferramentas::array_pesquisa_mult($finalidade_data, ['id', 'nome'], [$id, Ferramentas::codificador($finalidade)])) != 0) {
            $msg["Modificar"] = 'Não foi feita nenhuma alteração.';
          } else {
            $msg["Finalidade"] = 'Nome da Finalidade já existente';
            $violacao[] = "config_finalidade_cadastrar Finalidade já existente";
          }
        } else { //violação 
          $msg["Modificar"] = 'Finalidade já está em uso.';
          $violacao[] = "config_finalidade_cadastrar Finalidade já está em uso";
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

}