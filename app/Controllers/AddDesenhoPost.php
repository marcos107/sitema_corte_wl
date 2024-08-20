<?php

namespace App\Controllers;

use App\Controllers\Ferramentas;
use Config\App;

class AddDesenhoPost extends Ferramentas
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
  function criar_pasta_temp()
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
      $_SESSION['desenho_add_proc'] = isset($_SESSION['desenho_add_proc']) ? $_SESSION['desenho_add_proc'] : FALSE;
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
        $base_dir .= (Ferramentas::norma_lizar_str($value["empresa"])) . '/';
        $base_dir .= (Ferramentas::norma_lizar_str($value["empreendimento"])) . '/';
        $base_dir .= (Ferramentas::norma_lizar_str($value["finalidade"])) . '/';
        $base_dir .= (Ferramentas::norma_lizar_str($value["tag1"])) . '/';
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
            $base_dir .= Ferramentas::norma_lizar_str(Ferramentas::codificador($value["tag2"])) . '/';

            // Valida a tag3 associada.
            if ($value["tag3"] != '') { // -----------------
              if (Ferramentas::array_pesquisa($tag_data, 'nome', Ferramentas::norma_lizar_str(Ferramentas::codificador($value["tag3"]))) == array()) {
                //violacao
                $msg["Tag3 " . $value["desenho"]] = 'Não existe.';
                $violacao[] = "desenhos_add Tag3 não exist";
                $erro = true;
              } else {
                $tag3_id = Ferramentas::array_index(Ferramentas::array_pesquisa($tag_data, 'nome', Ferramentas::codificador($value["tag3"])), ['id']);
              }
              $base_dir .= Ferramentas::norma_lizar_str(Ferramentas::codificador($value["tag3"])) . '/';

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

            $desenho = $base_dir . Ferramentas::norma_lizar_str(str_replace('.' . Ferramentas::get_type_file($value["desenho"]), '', $value["desenho"])) . '.' . Ferramentas::get_type_file($value["desenho"]);
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


}