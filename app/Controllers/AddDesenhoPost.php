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
        'diretorio' => $targetDirectory,
        'usuario_id' => $_SESSION['usuario'],
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
      // $_SESSION['desenho_add_proc'] = isset($_SESSION['desenho_add_proc']) ? $_SESSION['desenho_add_proc'] : FALSE;
      // if ($_SESSION['desenho_add_proc']) {
      //   return;
      // } else {
      //   $_SESSION['desenho_add_proc'] = TRUE;
      // }

      $desenhos = service('request')->getPost('desenhos');
      $prcoesso = service('request')->getPost('nome_processos');
      $msg = array();
      $ok = array();
      $violacao = array();
      $desenhosInseridos = [];

      // Inicializa modelos de banco de dados para obter informações associadas (empresa, prioridade, finalidade, etc.).

      $subpasta = new \App\Models\Subpasta();
      $empresa = new \App\Models\Empresa();
      $prioridade = new \App\Models\Prioridade();
      $finalidade = new \App\Models\Finalidade();
      $empreendimento = new \App\Models\Empreendimentos();




      // Inicializa a variável que armazenará o filtro associado
      $prcoesso_nome = null;
      $prcoesso_id = null;

      // Procura pelo processo no array
      foreach ($_SESSION['processos_lista']['lista'] as $processo_lista) {
        if ($processo_lista['nome'] == $prcoesso) {
          $prcoesso_nome = $processo_lista['diretorio'];
          $prcoesso_id = $processo_lista['id'];
          break; // Encerra o loop uma vez que o processo é encontrado
        }
      }
      if ($prcoesso_nome == null or $prcoesso_id == null) {
        $msg["Processo"] = 'Não existe.';
        $violacao[] = "desenhos_add Processo não exist";
        $ok = false;
      } else {

        foreach ($desenhos as $key => $value) {
          //   $value['desenho'] = str_replace($value['desenho'],['/','\\'],'');
          $base_dir = 'c:/wl/' . $prcoesso_nome . '/';
          // Constrói o caminho base do diretório para armazenar o desenho.
          $empresa_id = null;
          $prioridade_id = null;
          $finalidade_id = null;
          $empreendimento_id = null;
          $Subpasta01_id = '';
          $Subpasta02_id = '';
          $Subpasta03_id = '';
          $erro = false;

















          // Valida informações associadas (empresa, prioridade, finalidade, etc.).
          if (($value["empresa"] ?? '') != '' || (int) ($value['empresa_id'] ?? 0) > 0) {

            $empresa_data = $this->localizarEmpresaSelecionada($value);
            if (!$empresa_data) {
              //violacao
              $msg["Empresa " . $value["desenho"]] = 'Empresa não existe.';
              $violacao[] = "desenhos_add Empresa não exist";
              $erro = true;
            } else {
              $empresa_id = (int) ($empresa_data['id'] ?? 0);
              $value['empresa'] = $this->obterNomeRegistro($empresa_data['nome'] ?? '');
            }
          } else {
            $msg["Empresa " . $value["desenho"]] = 'Não selecionada.';
            $erro = true;
          }

          // Valida a prioridade associada.
          if ($value["prioridade"] != '') {

            $prioridade_data = $prioridade
              ->where('status', 'ativo')
              ->where('nome',   $value['prioridade'])
              ->findAll();

            if (!$prioridade_data) {

              //violacao
              $msg["Prioridade " . $value["desenho"]] = 'Não existe.';
              $violacao[] = "desenhos_add Prioridade não exist";
              $erro = true;
            } else {
              $prioridade_id = $prioridade_data[0]['id'];
            }
          } else {
            $msg["Prioridade " . $value["desenho"]] = 'Não selecionado.';
            $erro = true;
          }

          // Valida a finalidade associada.
          if ($value["finalidade"] != '') {

            $finalidade_data = $finalidade
              ->where('status', 'ativo')
              ->where('nome',   $value['finalidade'])
              ->findAll();
            if (!$finalidade_data) {

              //violacao
              $msg["Finalidade " . $value["desenho"]] = 'Não existe.';
              $violacao[] = "desenhos_add Finalidade não exist";
              $erro = true;
            } else {
              $finalidade_id = $finalidade_data[0]['id'];
            }
          } else {
            $msg["Finalidade " . $value["desenho"]] = 'Não selecionado.';
            $erro = true;
          }

          // Valida o empreendimento associado.
          if (($value["empreendimento"] ?? '') != '' || (int) ($value['empreendimento_id'] ?? 0) > 0) {
            $empreendimento_data = $this->localizarEmpreendimentoSelecionado($value, (int) $empresa_id);
            if (!$empreendimento_data) {

              //violacao
              $msg["Empreendimento " . $value["desenho"]] = 'Não existe.';
              $violacao[] = "desenhos_add Empreendimento não exist";
              $erro = true;
            } else {
              $empreendimento_id = (int) ($empreendimento_data['id'] ?? 0);
              $value['empreendimento'] = $this->obterNomeRegistro($empreendimento_data['nome'] ?? '');
            }
          } else {
            $msg["Empreendimento " . $value["desenho"]] = 'Não selecionado.';
            $erro = true;
          }




          // Valida a Subpasta-01 associada.
          if ($value["tag1"] != '') { // -----------------
            $subpasta_data = $subpasta
              ->where('status', 'ativo')
              ->where('nome',   $value['tag1'])
              ->findAll();
            if (!$subpasta_data) {
              //violacao
              $msg["Subpasta-01 " . $value["desenho"]] = 'Não existe.';
              $violacao[] = "desenhos_add Subpasta-01 não exist";
              $erro = true;
            } else {
              $Subpasta01_id = $subpasta_data[0]['id'];
              $base_dir .= (Ferramentas::norma_lizar_str($value["empresa"])) . '/';
              $base_dir .= (Ferramentas::norma_lizar_str($value["empreendimento"])) . '/';
              $base_dir .= (Ferramentas::norma_lizar_str($value["finalidade"])) . '/';
              $base_dir .= (Ferramentas::norma_lizar_str($value["tag1"])) . '/';
            }

            // Valida a tag2 associada.
            if ($value["tag2"] != '') { // -----------------
              $subpasta_data = $subpasta
                ->where('status', 'ativo')
                ->where('nome',   $value['tag2'])
                ->findAll();
              if (!$subpasta_data) {
                //violacao
                $msg["Subpasta-02 " . $value["desenho"]] = 'Não existe.';
                $violacao[] = "desenhos_add Subpasta-02 não exist";
                $erro = true;
              } else {
                $Subpasta02_id = $subpasta_data[0]['id'];
              }
              $base_dir .= Ferramentas::norma_lizar_str(Ferramentas::codificador($value["tag2"])) . '/';

              // Valida a tag3 associada.
              if ($value["tag3"] != '') { // -----------------
                $subpasta_data = $subpasta
                  ->where('status', 'ativo')
                  ->where('nome',   $value['tag3'])
                  ->findAll();
                if (!$subpasta_data) {
                  //violacao
                  $msg["Subpasta-03 " . $value["desenho"]] = 'Não existe.';
                  $violacao[] = "desenhos_add Subpasta-03 não exist";
                  $erro = true;
                } else {
                  $Subpasta03_id = $subpasta_data[0]['id'];
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
              // remove barras iniciais de qualquer tipo
              $filename = basename($value['desenho']);
              $fileName = ltrim($value['desenho'], '/\\');


              // monta o caminho temporário
              $desenho_temp = rtrim($_SESSION['pasta_temp'], DIRECTORY_SEPARATOR)
                . DIRECTORY_SEPARATOR
                . $fileName;

              // 1) Extrai só o nome do arquivo, sem nenhuma barra
              $filename = basename($value['desenho']); // '\arquivo.dxf' → 'arquivo.dxf'

              // 2) Normaliza o diretório temporário
              $tempDir = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $_SESSION['pasta_temp']);
              $tempDir = rtrim($tempDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

              // 3) Monta o caminho completo para o arquivo temporário
              $desenho_temp = $tempDir . $filename;

              $baseDir = rtrim($base_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

              // 4) Gera um nome de arquivo único usando uniqid()

              // gera nome-base e extensão
              $desenho_desmenber = pathinfo($filename, PATHINFO_FILENAME);
              $desenho_typer     = '.' . pathinfo($filename, PATHINFO_EXTENSION);

              // gera um nome de arquivo único usando rand()
              do {
                $desenho = $baseDir
                  . $desenho_desmenber
                  . '_'
                  . rand(0, 1000)
                  . $desenho_typer;
              } while (file_exists($desenho));



              // 5) Move e valida
              if (
                ! is_file($desenho_temp)
                || ! rename($desenho_temp, $desenho)
                || ! file_exists($desenho)
              ) {
                $ok[]  = false;
                $msg[$value['desenho']] = 'Erro ao transferir o desenho.';
              } else {
                $ok[]  = true;
                $msg[$value['desenho']] = $desenho;

                // Insere informações sobre o desenho no banco de dados.
                $db = new \App\Models\Desenhos();




                $data = [
                  'nome' => $desenho_desmenber,
                  'diretorio' => $desenho,
                  'usuario_id_desenhista' => $_SESSION['usuario'],
                  'status' => 'pendente',
                  'prioridade_id' => $prioridade_id,
                  'finalidade_id' => $finalidade_id,
                  'empreendimentos_id' => $empreendimento_id,
                  'empresa_id' => $empresa_id,
                  'processos_id' => $prcoesso_id

                ];
                $db->insert($data);
                $idDesenho = (int) $db->getInsertID();
                if ($idDesenho > 0) {
                  $desenhosInseridos[] = $idDesenho;
                  Ferramentas::garantirOrdemAtivaDesenho($idDesenho, (int) $prcoesso_id, (int) $prioridade_id);
                  $this->salvarAreaMaterialSeDxf($idDesenho, $desenho, (int) $prcoesso_id);
                }
              }
            } else {
              $msg[$value["desenho"]] = $problema;
            }
          } else {
            $ok[] = false;
          }
        }
      }
      // Lista arquivos na pasta temporária
      if (!empty($desenhosInseridos) && isset($_SESSION['processo_dependencia']) && is_array($_SESSION['processo_dependencia']) && $_SESSION['processo_dependencia'] !== []) {
        $this->vincularDesenhosDependentesAoProjetoPai($desenhosInseridos);
        $this->marcarOrigemDependenciaComoProcessando();
      }
      $desenhos = Ferramentas::map_pasta($_SESSION['pasta_temp']);

      if (empty($desenhos)) {
        // Se não houver desenhos, marca como finalizado no banco
        $desenhosTempModel = new \App\Models\Desenhos_temp();

        $registro = $desenhosTempModel
          ->where('diretorio', $_SESSION['pasta_temp'])
          ->where('status',    'processando')
          ->first(); // traz apenas um registro

        if ($registro) {
          $desenhosTempModel->update(
            $registro['id'],
            ['status' => 'finalizado']
          );
        }
      }
      if (count($violacao) != 0) {
        // Registra as violações no banco de dados.
        $db = new \App\Models\Violacao();
        foreach ($violacao as $key => $value) {

          $data = [
            "usuario_id" => $_SESSION["usuario"],
            "causa" => $value

          ];

          $db->insert($data);
        }
      }
      if (in_array(true, $ok, true) && !in_array(false, $ok, true) && isset($_SESSION['processo_dependencia'])) {
        $_SESSION['processo_dependencia'] = '';
      }
      $data = [
        'ok' => $ok,
        'msg' => $msg,
        '1' => $_SESSION['processos_lista']['lista'],
        '2' => $prcoesso_nome . ' - ' . $prcoesso_id,
        '3' => $prcoesso_nome

      ];
      $_SESSION['desenho_add_proc'] = false;
      return $this->response->setJSON($data);
    }
    return $this->response->setJSON(['ok' => false]);
  }




  function desenhos_add_uni()
  {
    if ($this->request->isAJAX()) {
      session_start();
      // $_SESSION['desenho_add_proc'] = isset($_SESSION['desenho_add_proc']) ? $_SESSION['desenho_add_proc'] : FALSE;
      // if ($_SESSION['desenho_add_proc']) {
      //   return;
      // } else {
      //   $_SESSION['desenho_add_proc'] = TRUE;
      // }

      $desenhos = service('request')->getPost('desenhos');
      $prcoesso = service('request')->getPost('nome_processos');
      $msg = array();
      $ok = array();
      $violacao = array();
      $dependenciaProjetoCriada = false;

      // Inicializa modelos de banco de dados para obter informações associadas (empresa, prioridade, finalidade, etc.).

      $subpasta = new \App\Models\Subpasta();
      $empresa = new \App\Models\Empresa();
      $prioridade = new \App\Models\Prioridade();
      $finalidade = new \App\Models\Finalidade();
      $empreendimento = new \App\Models\Empreendimentos();




      // Inicializa a variável que armazenará o filtro associado
      $prcoesso_nome = null;
      $prcoesso_id = null;

      // Procura pelo processo no array
      foreach ($_SESSION['processos_lista']['lista'] as $processo_lista) {
        if ($processo_lista['nome'] == $prcoesso) {
          $prcoesso_nome = $processo_lista['diretorio'];
          $prcoesso_id = $processo_lista['id'];
          break; // Encerra o loop uma vez que o processo é encontrado
        }
      }
      if ($prcoesso_nome == null or $prcoesso_id == null) {
        $msg["Processo"] = 'Não existe.';
        $violacao[] = "desenhos_add_uni Processo não exist";
        $ok = false;
      } else {

        $desenhos = $desenhos;
        $base_dir = 'c:/wl/' . $prcoesso_nome . '/';
        // Constrói o caminho base do diretório para armazenar o desenho.

        $empresa_id = null;
        $prioridade_id = null;
        $finalidade_id = null;
        $empreendimento_id = null;
        $Subpasta01_id = '';
        $Subpasta02_id = '';
        $Subpasta03_id = '';
        $erro = false;













        if (isset($desenhos['descricao']) && $desenhos['descricao'] !== '') {


          if (Ferramentas::codificador($desenhos["descricao"]) == '') {
            //violacao
            $msg["Descricao"] = 'Possui caractere não permitido.';
            $violacao[] = "desenhos_add_uni Possui caractere não permitido";
            $erro = true;
          }
        } else {
          $msg["Descricao"] = 'Não informada.';
          $erro = true;
        }



        // Valida informações associadas (empresa, prioridade, finalidade, etc.).
        if (($desenhos["empresa"] ?? '') != '' || (int) ($desenhos['empresa_id'] ?? 0) > 0) {

          $empresa_data = $this->localizarEmpresaSelecionada($desenhos);
          if (!$empresa_data) {
            //violacao
            $msg["Empresa "] = 'Empresa não existe.';
            $violacao[] = "desenhos_add_uni Empresa não exist";
            $erro = true;
          } else {
            $empresa_id = (int) ($empresa_data['id'] ?? 0);
            $desenhos['empresa'] = $this->obterNomeRegistro($empresa_data['nome'] ?? '');
          }
        } else {
          $msg["Empresa "] = 'Não selecionada.';
          $erro = true;
        }

        // Valida a prioridade associada.
        if ($desenhos["prioridade"] != '') {

          $prioridade_data = $prioridade
            ->where('status', 'ativo')
            ->where('nome',   $desenhos['prioridade'])
            ->findAll();

          if (!$prioridade_data) {

            //violacao
            $msg["Prioridade "] = 'Não existe.';
            $violacao[] = "desenhos_add_uni Prioridade não exist";
            $erro = true;
          } else {
            $prioridade_id = $prioridade_data[0]['id'];
          }
        } else {
          $msg["Prioridade "] = 'Não selecionado.';
          $erro = true;
        }

        // Valida a finalidade associada.
        if ($desenhos["finalidade"] != '') {

          $finalidade_data = $finalidade
            ->where('status', 'ativo')
            ->where('nome',   $desenhos['finalidade'])
            ->findAll();
          if (!$finalidade_data) {

            //violacao
            $msg["Finalidade "] = 'Não existe.';
            $violacao[] = "desenhos_add_uni Finalidade não exist";
            $erro = true;
          } else {
            $finalidade_id = $finalidade_data[0]['id'];
          }
        } else {
          $msg["Finalidade "] = 'Não selecionado.';
          $erro = true;
        }

        // Valida o empreendimento associado.
        if (($desenhos["empreendimento"] ?? '') != '' || (int) ($desenhos['empreendimento_id'] ?? 0) > 0) {
          $empreendimento_data = $this->localizarEmpreendimentoSelecionado($desenhos, (int) $empresa_id);
          if (!$empreendimento_data) {

            //violacao
            $msg["Empreendimento "] = 'Não existe.';
            $violacao[] = "desenhos_add_uni Empreendimento não exist";
            $erro = true;
          } else {
            $empreendimento_id = (int) ($empreendimento_data['id'] ?? 0);
            $desenhos['empreendimento'] = $this->obterNomeRegistro($empreendimento_data['nome'] ?? '');
          }
        } else {
          $msg["Empreendimento "] = 'Não selecionado.';
          $erro = true;
        }




        // Valida a Subpasta-01 associada.
        if ($desenhos["tag1"] != '') { // -----------------
          $subpasta_data = $subpasta
            ->where('status', 'ativo')
            ->where('nome',   $desenhos['tag1'])
            ->findAll();
          if (!$subpasta_data) {
            //violacao
            $msg["Subpasta-01 "] = 'Não existe.';
            $violacao[] = "desenhos_add_uni Subpasta-01 não exist";
            $erro = true;
          } else {
            $Subpasta01_id = $subpasta_data[0]['id'];
            $base_dir .= (Ferramentas::norma_lizar_str($desenhos["empresa"])) . '/';
            $base_dir .= (Ferramentas::norma_lizar_str($desenhos["empreendimento"])) . '/';
            $base_dir .= (Ferramentas::norma_lizar_str($desenhos["finalidade"])) . '/';
            $base_dir .= (Ferramentas::norma_lizar_str($desenhos["tag1"])) . '/';
          }

          // Valida a tag2 associada.
          if ($desenhos["tag2"] != '') { // -----------------
            $subpasta_data = $subpasta
              ->where('status', 'ativo')
              ->where('nome',   $desenhos['tag2'])
              ->findAll();
            if (!$subpasta_data) {
              //violacao
              $msg["Subpasta-02 "] = 'Não existe.';
              $violacao[] = "desenhos_add_uni Subpasta-02 não exist";
              $erro = true;
            } else {
              $Subpasta02_id = $subpasta_data[0]['id'];
            }
            $base_dir .= Ferramentas::norma_lizar_str(Ferramentas::codificador($desenhos["tag2"])) . '/';

            // Valida a tag3 associada.
            if ($desenhos["tag3"] != '') { // -----------------
              $subpasta_data = $subpasta
                ->where('status', 'ativo')
                ->where('nome',   $desenhos['tag3'])
                ->findAll();
              if (!$subpasta_data) {
                //violacao
                $msg["Subpasta-03 "] = 'Não existe.';
                $violacao[] = "desenhos_add_uni Subpasta-03 não exist";
                $erro = true;
              } else {
                $Subpasta03_id = $subpasta_data[0]['id'];
              }
              $base_dir .= Ferramentas::norma_lizar_str(Ferramentas::codificador($desenhos["tag3"])) . '/';
            }
          }
        } else {
          $msg["Subpasta-01 "] = 'Não selecionado a Subpasta-01';
          $erro = true;
        }

          $base_dir .=  md5($desenhos['descricao'] ). '/';
        if (!$erro) {

          // Remove espaços em branco do caminho base.
          $base_dir = str_replace(' ', '', $base_dir);

          // Cria o diretório base se ele não existir.
          $problema = Ferramentas::criet_diretorio($base_dir);

          if (count($problema) == 0) {
            $baseDir = rtrim($base_dir, DIRECTORY_SEPARATOR);

            $db = new \App\Models\Projeto();
            $db->insert([
              'usuario_id' => $_SESSION['usuario'],
              'diretorio' => $baseDir,
              'status' =>  'ativo',
              'descricao' => $desenhos['descricao']

            ]);
            $id_projeto = $db->getInsertID();


            foreach ($desenhos['desenho'] as $key => $value) {
              // remove barras iniciais de qualquer tipo
              $filename = basename($value);
              $fileName = ltrim($value, '/\\');


              // monta o caminho temporário
              $desenho_temp = rtrim($_SESSION['pasta_temp'], DIRECTORY_SEPARATOR)
                . DIRECTORY_SEPARATOR
                . $fileName;

              // 1) Extrai só o nome do arquivo, sem nenhuma barra
              $filename = basename($value); // '\arquivo.dxf' → 'arquivo.dxf'

              // 2) Normaliza o diretório temporário
              $tempDir = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $_SESSION['pasta_temp']);
              $tempDir = rtrim($tempDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

              // 3) Monta o caminho completo para o arquivo temporário
              $desenho_temp = $tempDir . $filename;



              // 4) Gera um nome de arquivo único usando uniqid()

              // gera nome-base e extensão
              $desenho_desmenber = pathinfo($filename, PATHINFO_FILENAME);
              $desenho_typer     = '.' . pathinfo($filename, PATHINFO_EXTENSION);

              // gera um nome de arquivo único usando rand()
              do {
                $desenho = $baseDir
                  . $desenho_desmenber
                  . '_'
                  . rand(0, 1000)
                  . $desenho_typer;
              } while (file_exists($desenho));



              // 5) Move e valida
              if (
                ! is_file($desenho_temp)
                || ! rename($desenho_temp, $desenho)
                || ! file_exists($desenho)
              ) {
                $ok[]  = false;
                $msg[$value] = 'Erro ao transferir o desenho.';
              } else {
                $ok[]  = true;
                $msg[$value] = $desenho;

                // Insere informações sobre o desenho no banco de dados.
                $db = new \App\Models\Desenhos();

                $data = [
                  'nome' => $desenho_desmenber,
                  'diretorio' => $desenho,
                  'usuario_id_desenhista' => $_SESSION['usuario'],
                  'status' => 'pendente',
                  'prioridade_id' => $prioridade_id,
                  'finalidade_id' => $finalidade_id,
                  'empreendimentos_id' => $empreendimento_id,
                  'empresa_id' => $empresa_id,
                  'processos_id' => $prcoesso_id

                ];
                $db->insert($data);
                $id_arquivo = $db->getInsertID();
                if ((int) $id_arquivo > 0) {
                  Ferramentas::garantirOrdemAtivaDesenho((int) $id_arquivo, (int) $prcoesso_id, (int) $prioridade_id);
                  $this->salvarAreaMaterialSeDxf((int) $id_arquivo, $desenho, (int) $prcoesso_id);
                }

                $db = new \App\Models\Projeto_desenho();
                $db->insert([
                  'usuario_id' => $_SESSION['usuario'],
                  'projeto_id' =>    $id_projeto,
                  'desenho_id' =>  $id_arquivo,

                ]);
              }
            }
            if (isset($_SESSION['processo_dependencia']) && is_array($_SESSION['processo_dependencia']) && $_SESSION['processo_dependencia'] !== []) {
              (new \App\Models\Dependencia())->insert([
                'projeto_id_dependente' => $_SESSION['processo_dependencia'][0]['projeto_id'],
                'projeto_id' => $id_projeto
              ]);
              $dependenciaProjetoCriada = true;
            }
          } else {
            $msg["Erros"] = $problema;
          }
        } else {
          $ok[] = false;
        }
      }
    }
    // Lista arquivos na pasta temporária
    if ($dependenciaProjetoCriada) {
      $this->marcarOrigemDependenciaComoProcessando();
    }
    $desenhos = Ferramentas::map_pasta($_SESSION['pasta_temp']);

    if (empty($desenhos)) {
      // Se não houver desenhos, marca como finalizado no banco
      $desenhosTempModel = new \App\Models\Desenhos_temp();

      $registro = $desenhosTempModel
        ->where('diretorio', $_SESSION['pasta_temp'])
        ->where('status',    'processando')
        ->first(); // traz apenas um registro

      if ($registro) {
        $desenhosTempModel->update(
          $registro['id'],
          ['status' => 'finalizado']
        );
      }
    }
    if (count($violacao) != 0) {
      // Registra as violações no banco de dados.
      $db = new \App\Models\Violacao();
      foreach ($violacao as $key => $value) {

        $data = [
          "usuario_id" => $_SESSION["usuario"],
          "causa" => $value

        ];

        $db->insert($data);
      }
    }
    if (in_array(true, $ok, true) && !in_array(false, $ok, true) && isset($_SESSION['processo_dependencia'])) {
      $_SESSION['processo_dependencia'] = '';
    }
    $data = [
      'ok' => $ok,
      'msg' => $msg,
      '1' => $_SESSION['processos_lista']['lista'],
      '2' => $prcoesso_nome . ' - ' . $prcoesso_id,
      '3' => $prcoesso_nome

    ];
    $_SESSION['desenho_add_proc'] = false;
    return $this->response->setJSON($data);
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


      $prcoesso_nome = service('request')->getPost('nome_processos');


      // Inicializa a variável que armazenará o filtro associado
      $filtroAssociado = null;

      // Procura pelo processo no array
      foreach ($_SESSION['processos_lista']['lista'] as $processo) {
        if ($processo['nome'] == $prcoesso_nome) {
          $filtroAssociado = $processo['filtro'];
          break; // Encerra o loop uma vez que o processo é encontrado
        }
      }






      foreach ($desenhos as $key => $value) {
        // Verifica se a extensão do arquivo não está na lista de filtros permitidos.
        if (!in_array(Ferramentas::get_type_file($value), explode(",", str_replace(".", "", $filtroAssociado))) or $filtroAssociado == null) {
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

      $dependenciaAtiva = service('request')->getPost('dependencia_ativa') === '1';

      return $this->response->setJSON([
        'ok' => $ok,
        'desenhos' => $desenhos,
        'msg' => $msg,
        '1' => explode(",", str_replace(".", "", $filtroAssociado)),
        '2' => $_SESSION['processos_lista']['lista'],
        '3' => $prcoesso_nome,
        'preselecoes' => $dependenciaAtiva ? $this->obterPreselecoesDependencia() : []
      ]);
    }
  }

  private function obterOrigemDependenciaAtual(): array
  {
    $dependencia = $_SESSION['processo_dependencia'] ?? null;
    if (!is_array($dependencia) || $dependencia === []) {
      return [];
    }

    return array_values(array_filter($dependencia, static function ($item) {
      return is_array($item);
    }));
  }

  private function registrarAlteracaoStatusDependencia(string $item, int $itemId, array $detalhes, array $meta = []): void
  {
    if ($itemId <= 0 || $detalhes === []) {
      return;
    }

    $usuarioId = (int) ($_SESSION['usuario'] ?? 0);
    (new \App\Models\Alteracoes())->insertWithDetails(
      [
        'usuario_id' => $usuarioId,
        'individuo' => $usuarioId,
        'id_item' => $itemId,
        'item' => $item,
        'info_mais' => 'dependencia.status_origem',
        '_meta' => array_merge([
          'acao' => 'dependencia.status_origem',
          'origem' => 'add_desenho_post',
        ], $meta),
      ],
      $detalhes
    );
  }

  private function marcarOrigemDependenciaComoProcessando(): void
  {
    $dependencia = $this->obterOrigemDependenciaAtual();
    if ($dependencia === []) {
      return;
    }

    $agora = date('Y-m-d H:i:s');
    $usuarioId = (int) ($_SESSION['usuario'] ?? 0);
    $projetoPaiId = (int) ($dependencia[0]['projeto_id'] ?? 0);

    if ($projetoPaiId > 0) {
      $projetoModel = new \App\Models\Projeto();
      $projetoPai = $projetoModel->find($projetoPaiId);
      if (is_array($projetoPai)) {
        $statusProjetoAntes = strtolower(trim((string) ($projetoPai['status'] ?? '')));
        if ($statusProjetoAntes !== 'processando') {
          $projetoModel->update($projetoPaiId, ['status' => 'processando']);
          $this->registrarAlteracaoStatusDependencia(
            'projeto',
            $projetoPaiId,
            [[
              'campo' => 'projeto.status',
              'valor_antes' => $statusProjetoAntes,
              'valor_depois' => 'processando',
            ]],
            ['projeto_id' => $projetoPaiId]
          );
        }
      }
    }

    $desenhoIds = [];
    foreach ($dependencia as $item) {
      $desenhoId = (int) ($item['desenho_id'] ?? 0);
      if ($desenhoId > 0) {
        $desenhoIds[$desenhoId] = $desenhoId;
      }
    }

    if ($desenhoIds === [] && $projetoPaiId > 0) {
      $projetoDesenhoRows = (new \App\Models\Projeto_desenho())
        ->select('desenho_id')
        ->where('projeto_id', $projetoPaiId)
        ->findAll();

      foreach ($projetoDesenhoRows as $projetoDesenhoRow) {
        $desenhoId = (int) ($projetoDesenhoRow['desenho_id'] ?? 0);
        if ($desenhoId > 0) {
          $desenhoIds[$desenhoId] = $desenhoId;
        }
      }
    }

    if ($desenhoIds === []) {
      return;
    }

    $desenhoModel = new \App\Models\Desenhos();
    $corteModel = new \App\Models\Corte();
    $desenhosPai = $desenhoModel
      ->whereIn('id', array_values($desenhoIds))
      ->findAll();

    foreach ($desenhosPai as $desenhoPai) {
      $desenhoId = (int) ($desenhoPai['id'] ?? 0);
      if ($desenhoId <= 0) {
        continue;
      }

      $detalhes = [];
      $statusAntes = strtolower(trim((string) ($desenhoPai['status'] ?? '')));
      if ($statusAntes !== 'processando') {
        $desenhoModel->update($desenhoId, ['status' => 'processando']);
        $detalhes[] = [
          'campo' => 'desenho.status',
          'valor_antes' => $statusAntes,
          'valor_depois' => 'processando',
        ];
      }

      $corteId = (int) ($desenhoPai['corte_id'] ?? 0);
      if ($corteId > 0) {
        $cortePai = $corteModel->find($corteId);
        $statusCorteAntes = strtolower(trim((string) ($cortePai['status'] ?? '')));
        if ($statusCorteAntes === 'inicio') {
          $corteModel->update($corteId, [
            'usuario_id_fim' => $usuarioId,
            'data_end' => $agora,
            'status' => 'finalizado',
          ]);
          $detalhes[] = [
            'campo' => 'corte.status',
            'valor_antes' => $statusCorteAntes,
            'valor_depois' => 'finalizado',
          ];
          $detalhes[] = [
            'campo' => 'corte.usuario_id_fim',
            'valor_antes' => (string) ($cortePai['usuario_id_fim'] ?? ''),
            'valor_depois' => (string) $usuarioId,
          ];
        }
      }

      if ($detalhes !== []) {
        $this->registrarAlteracaoStatusDependencia(
          'desenho',
          $desenhoId,
          $detalhes,
          [
            'projeto_id' => $projetoPaiId,
            'desenho_id' => $desenhoId,
            'desenho_nome' => Ferramentas::remove_id_file($this->valorPreselecionadoDependencia($desenhoPai['nome'] ?? '')),
            'corte_id' => $corteId,
          ]
        );
      }
    }
  }

  private function vincularDesenhosDependentesAoProjetoPai(array $desenhoIds): void
  {
    $dependencia = $this->obterOrigemDependenciaAtual();
    if ($dependencia === [] || $desenhoIds === []) {
      return;
    }

    $projetoPaiId = (int) ($dependencia[0]['projeto_id'] ?? 0);
    if ($projetoPaiId <= 0) {
      return;
    }

    $dependenciaModel = new \App\Models\Dependencia();
    foreach (array_values(array_unique(array_map('intval', $desenhoIds))) as $desenhoId) {
      if ($desenhoId <= 0) {
        continue;
      }

      $existente = $dependenciaModel
        ->where('projeto_id_dependente', $projetoPaiId)
        ->where('desenhos_id', $desenhoId)
        ->first();

      if (is_array($existente) && !empty($existente['id'])) {
        continue;
      }

      $dependenciaModel->insert([
        'projeto_id_dependente' => $projetoPaiId,
        'desenhos_id' => $desenhoId,
      ]);
    }
  }

  private function obterPreselecoesDependencia(): array
  {
    $dependencia = $_SESSION['processo_dependencia'] ?? null;
    if (!is_array($dependencia) || $dependencia === []) {
      return [];
    }

    $primeiroItem = $dependencia[0] ?? null;
    if (!is_array($primeiroItem)) {
      return [];
    }

    $desenhoId = (int) ($primeiroItem['desenho_id'] ?? 0);
    $projetoId = (int) ($primeiroItem['projeto_id'] ?? 0);

    if ($desenhoId <= 0 && $projetoId > 0) {
      $primeiroDesenhoProjeto = (new \App\Models\Projeto_desenho())
        ->select('desenho_id')
        ->where('projeto_id', $projetoId)
        ->orderBy('data_add', 'ASC')
        ->first();

      $desenhoId = (int) ($primeiroDesenhoProjeto['desenho_id'] ?? 0);
    }

    if ($desenhoId <= 0) {
      return [];
    }

    $desenho = (new \App\Models\Desenhos())
      ->select('desenhos.diretorio,
                desenhos.empreendimentos_id,
                desenhos.finalidade_id,
                empresa.nome AS empresa_nome,
                empreendimentos.nome AS empreendimento_nome,
                finalidade.nome AS finalidade_nome,
                prioridade.nome AS prioridade_nome')
      ->join('empresa', 'empresa.id = desenhos.empresa_id', 'left')
      ->join('empreendimentos', 'empreendimentos.id = desenhos.empreendimentos_id', 'left')
      ->join('finalidade', 'finalidade.id = desenhos.finalidade_id', 'left')
      ->join('prioridade', 'prioridade.id = desenhos.prioridade_id', 'left')
      ->where('desenhos.id', $desenhoId)
      ->first();

    if (!is_array($desenho)) {
      return [];
    }

    $descricao = '';
    if ($projetoId > 0) {
      $projeto = (new \App\Models\Projeto())
        ->select('descricao')
        ->where('id', $projetoId)
        ->first();

      if (is_array($projeto) && isset($projeto['descricao'])) {
        $descricao = $this->valorPreselecionadoDependencia($projeto['descricao']);
      }
    }

    $empresaPreselecionada = $this->valorPreselecionadoDependencia($desenho['empresa_nome'] ?? '');
    $empreendimentoPreselecionado = $this->valorPreselecionadoDependencia($desenho['empreendimento_nome'] ?? '');
    $finalidadePreselecionada = $this->valorPreselecionadoDependencia($desenho['finalidade_nome'] ?? '');

    if ($empresaPreselecionada === '') {
      $empresaPreselecionada = $this->valorPreselecionadoDependencia($primeiroItem['empresa_nome'] ?? '');
    }
    if ($empreendimentoPreselecionado === '') {
      $empreendimentoPreselecionado = $this->valorPreselecionadoDependencia($primeiroItem['empreendimento_nome'] ?? '');
    }
    if ($finalidadePreselecionada === '') {
      $finalidadePreselecionada = $this->valorPreselecionadoDependencia($primeiroItem['finalidade_nome'] ?? '');
    }

    $tagsPreselecionadas = $this->obterSubpastasDependencia(
      (string) ($desenho['diretorio'] ?? ''),
      (int) ($desenho['empreendimentos_id'] ?? 0),
      (int) ($desenho['finalidade_id'] ?? 0)
    );

    if (($tagsPreselecionadas === [] || ($tagsPreselecionadas[0] ?? '') === '') && !empty($primeiroItem['tags'])) {
      $tagsPreselecionadas = array_values(array_filter(array_map('trim', explode(' - ', (string) $primeiroItem['tags']))));
    }

    return [
      'empresa' => $empresaPreselecionada,
      'empreendimento' => $empreendimentoPreselecionado,
      'finalidade' => $finalidadePreselecionada,
      'prioridade' => $this->valorPreselecionadoDependencia($desenho['prioridade_nome'] ?? ''),
      'descricao' => $descricao,
      'tags' => $tagsPreselecionadas
    ];
  }

  private function obterSubpastasDependencia(string $diretorio, int $empreendimentoId, int $finalidadeId): array
  {
    if ($diretorio === '') {
      return [];
    }

    $partes = explode('/', str_replace('\\', '/', $diretorio));
    $segmentos = array_values(array_filter(array_slice($partes, 6, -1), static function ($parte) {
      return $parte !== '';
    }));

    if ($segmentos === []) {
      return [];
    }

    $mapaSubpastas = [];
    if ($empreendimentoId > 0 && $finalidadeId > 0) {
      $subpastas = (new \App\Models\Subpasta())
        ->select('nome')
        ->where('status', 'ativo')
        ->where('empreendimentos_id', $empreendimentoId)
        ->where('finalidade_id', $finalidadeId)
        ->findAll();

      foreach ($subpastas as $subpasta) {
        $nomeOriginal = $this->valorPreselecionadoDependencia($subpasta['nome'] ?? '');
        if ($nomeOriginal === '') {
          continue;
        }

        $mapaSubpastas[Ferramentas::norma_lizar_str($nomeOriginal)] = $nomeOriginal;
      }
    }

    $resultado = [];
    foreach (array_slice($segmentos, 0, 3) as $segmento) {
      $chaveNormalizada = Ferramentas::norma_lizar_str($segmento);
      $resultado[] = $mapaSubpastas[$chaveNormalizada] ?? trim(str_replace('_', ' ', (string) $segmento));
    }

    return $resultado;
  }

  private function valorPreselecionadoDependencia($valor): string
  {
    $valor = trim((string) ($valor ?? ''));
    if ($valor === '') {
      return '';
    }

    $decodificado = Ferramentas::decodificador($valor);
    if ($decodificado !== '') {
      return trim($decodificado);
    }

    return $valor;
  }

  private function obterNomeRegistro($valor): string
  {
    $decodificado = trim((string) Ferramentas::decodificador((string) $valor));
    if ($decodificado !== '') {
      return $decodificado;
    }

    return trim((string) $valor);
  }

  private function variacoesNomeBusca(string $nome): array
  {
    $nome = trim($nome);
    if ($nome === '') {
      return [];
    }

    $variacoes = [$nome];
    $codificado = Ferramentas::codificador($nome);
    if ($codificado !== '' && !in_array($codificado, $variacoes, true)) {
      $variacoes[] = $codificado;
    }

    return $variacoes;
  }

  private function localizarEmpresaSelecionada(array $dados): ?array
  {
    $token = (string) ($dados['empresa_id'] ?? '');
    $empresaId = (int) (($_SESSION['desenho_empresa_tokens'] ?? [])[$token] ?? 0);
    if ($empresaId > 0) {
      $registro = (new \App\Models\Empresa())
        ->where('status', 'ativo')
        ->where('id', $empresaId)
        ->first();

      if (is_array($registro)) {
        return $registro;
      }
    }

    return null;
  }

  private function localizarEmpreendimentoSelecionado(array $dados, int $empresaId): ?array
  {
    $token = (string) ($dados['empreendimento_id'] ?? '');
    $tokenData = ($_SESSION['desenho_empreendimento_tokens'] ?? [])[$token] ?? [];
    $empreendimentoId = (int) ($tokenData['id'] ?? 0);
    if ((int) ($tokenData['empresa_id'] ?? 0) !== $empresaId) {
      return null;
    }
    if ($empreendimentoId > 0) {
      $builder = (new \App\Models\Empreendimentos())
        ->where('status', 'ativo')
        ->where('id', $empreendimentoId);

      if ($empresaId > 0) {
        $builder->where('empresa_id', $empresaId);
      }

      $registro = $builder->first();
      if (is_array($registro)) {
        return $registro;
      }
    }

    return null;
  }

  /**
   * Salva area estimada de material para arquivos DXF.
   * A margem fixa atual e de 10%.
   */
  private function salvarAreaMaterialSeDxf(int $desenhoId, string $desenhoPath, int $processoId): void
  {
    if ($desenhoId <= 0) {
      return;
    }

    $extensao = strtolower((string) pathinfo($desenhoPath, PATHINFO_EXTENSION));
    if ($extensao !== 'dxf') {
      return;
    }

    if (!is_file($desenhoPath)) {
      return;
    }

    if (!$this->processoEhCorteLaser($processoId)) {
      return;
    }

    $areaM2 = $this->calcularAreaM2Dxf($desenhoPath);
    if ($areaM2 === null || $areaM2 <= 0) {
      return;
    }

    $db = \Config\Database::connect();
    if (!$db->tableExists('arquivo_metricas_material')) {
      return;
    }

    $margemPercentual = 10.0;
    $areaM2 = round($areaM2, 6);
    $areaM2ComMargem = round($areaM2 * (1 + ($margemPercentual / 100)), 6);

    $payload = [
      'entidade_tipo' => 'desenho',
      'entidade_id' => $desenhoId,
      'processo_id' => $processoId,
      'tipo_arquivo' => 'dxf',
      'metrica' => 'area_m2',
      'unidade' => 'm2',
      'valor_base' => $areaM2,
      'margem_percentual' => $margemPercentual,
      'valor_final' => $areaM2ComMargem,
      'fonte_calculo' => 'dxf_entities',
      'data_referencia' => date('Y-m-d H:i:s'),
    ];

    $model = new \App\Models\ArquivoMetricasMaterial();
    $existente = $model
      ->where('entidade_tipo', 'desenho')
      ->where('entidade_id', $desenhoId)
      ->where('tipo_arquivo', 'dxf')
      ->where('metrica', 'area_m2')
      ->first();

    if (is_array($existente) && isset($existente['id'])) {
      $model->update((int) $existente['id'], $payload);
      return;
    }

    $model->insert($payload);
  }

  private function processoEhCorteLaser(int $processoId): bool
  {
    if ($processoId <= 0) {
      return false;
    }

    $processoRow = (new \App\Models\Processos())
      ->select('nome')
      ->where('id', $processoId)
      ->first();

    if (!is_array($processoRow)) {
      return false;
    }

    $nome = trim((string) Ferramentas::decodificador((string) ($processoRow['nome'] ?? '')));
    if ($nome === '') {
      $nome = trim((string) ($processoRow['nome'] ?? ''));
    }

    $nome = $this->normalizarTextoBusca($nome);

    return strpos($nome, 'corte') !== false && strpos($nome, 'laser') !== false;
  }

  private function normalizarTextoBusca(string $texto): string
  {
    $texto = strtolower(trim($texto));
    if ($texto === '') {
      return '';
    }

    $map = [
      'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a',
      'é' => 'e', 'ê' => 'e',
      'í' => 'i',
      'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
      'ú' => 'u',
      'ç' => 'c',
    ];
    $texto = strtr($texto, $map);
    $texto = preg_replace('/\s+/', ' ', $texto) ?? $texto;

    return $texto;
  }

  /**
   * Calcula area de entidades fechadas do DXF e converte para m2.
   */
  private function calcularAreaM2Dxf(string $arquivoPath): ?float
  {
    $conteudo = @file_get_contents($arquivoPath);
    if ($conteudo === false || trim($conteudo) === '') {
      return null;
    }

    $linhas = preg_split('/\r\n|\r|\n/', $conteudo);
    if (!is_array($linhas) || count($linhas) < 4) {
      return null;
    }

    $pares = [];
    $totalLinhas = count($linhas);
    for ($i = 0; $i + 1 < $totalLinhas; $i += 2) {
      $codigo = trim((string) $linhas[$i]);
      if ($codigo === '') {
        continue;
      }

      $valor = trim((string) $linhas[$i + 1]);
      $pares[] = [
        'code' => $codigo,
        'value' => $valor,
      ];
    }

    if ($pares === []) {
      return null;
    }

    $insunits = $this->detectarInsunitsDxf($pares);
    $fatorMetro = $this->fatorMetroPorInsunits($insunits);
    if ($fatorMetro <= 0) {
      $fatorMetro = 0.001;
    }

    $areaUnidadeQuadrada = 0.0;
    $totalPares = count($pares);
    $dentroEntidades = false;

    for ($i = 0; $i < $totalPares; $i++) {
      if (($pares[$i]['code'] ?? '') !== '0') {
        continue;
      }

      $tipo = strtoupper((string) ($pares[$i]['value'] ?? ''));

      if ($tipo === 'SECTION') {
        $codigoSecao = (string) ($pares[$i + 1]['code'] ?? '');
        $nomeSecao = strtoupper((string) ($pares[$i + 1]['value'] ?? ''));
        $dentroEntidades = ($codigoSecao === '2' && $nomeSecao === 'ENTITIES');
        continue;
      }

      if ($tipo === 'ENDSEC') {
        $dentroEntidades = false;
        continue;
      }

      if (!$dentroEntidades) {
        continue;
      }

      if ($tipo === 'LWPOLYLINE') {
        $vertices = [];
        $fechado = false;
        $xAtual = null;
        $j = $i + 1;

        while ($j < $totalPares && ($pares[$j]['code'] ?? '') !== '0') {
          $codigo = (string) ($pares[$j]['code'] ?? '');
          $valor = (string) ($pares[$j]['value'] ?? '');

          if ($codigo === '70') {
            $fechado = (((int) $valor & 1) === 1);
          } elseif ($codigo === '10') {
            $xAtual = $this->dxfToFloat($valor);
          } elseif ($codigo === '20' && $xAtual !== null) {
            $yAtual = $this->dxfToFloat($valor);
            if ($yAtual !== null) {
              $vertices[] = [$xAtual, $yAtual];
            }
            $xAtual = null;
          }

          $j++;
        }

        if ($fechado && count($vertices) >= 3) {
          $areaUnidadeQuadrada += abs($this->calcularAreaPoligono($vertices));
        }

        $i = $j - 1;
        continue;
      }

      if ($tipo === 'CIRCLE') {
        $raio = null;
        $j = $i + 1;
        while ($j < $totalPares && ($pares[$j]['code'] ?? '') !== '0') {
          if (($pares[$j]['code'] ?? '') === '40') {
            $raio = $this->dxfToFloat((string) ($pares[$j]['value'] ?? ''));
          }
          $j++;
        }

        if ($raio !== null && $raio > 0) {
          $areaUnidadeQuadrada += M_PI * $raio * $raio;
        }

        $i = $j - 1;
        continue;
      }

      if ($tipo === 'POLYLINE') {
        $fechado = false;
        $vertices = [];
        $j = $i + 1;

        while ($j < $totalPares && ($pares[$j]['code'] ?? '') !== '0') {
          if (($pares[$j]['code'] ?? '') === '70') {
            $fechado = (((int) ($pares[$j]['value'] ?? 0) & 1) === 1);
          }
          $j++;
        }

        while ($j < $totalPares) {
          if (($pares[$j]['code'] ?? '') !== '0') {
            $j++;
            continue;
          }

          $tipoSub = strtoupper((string) ($pares[$j]['value'] ?? ''));
          if ($tipoSub === 'SEQEND') {
            break;
          }

          if ($tipoSub !== 'VERTEX') {
            break;
          }

          $x = null;
          $y = null;
          $k = $j + 1;
          while ($k < $totalPares && ($pares[$k]['code'] ?? '') !== '0') {
            $codigo = (string) ($pares[$k]['code'] ?? '');
            $valor = (string) ($pares[$k]['value'] ?? '');
            if ($codigo === '10') {
              $x = $this->dxfToFloat($valor);
            } elseif ($codigo === '20') {
              $y = $this->dxfToFloat($valor);
            }
            $k++;
          }

          if ($x !== null && $y !== null) {
            $vertices[] = [$x, $y];
          }
          $j = $k;
        }

        if ($fechado && count($vertices) >= 3) {
          $areaUnidadeQuadrada += abs($this->calcularAreaPoligono($vertices));
        }

        $i = $j;
      }
    }

    if ($areaUnidadeQuadrada <= 0) {
      return null;
    }

    $areaM2 = $areaUnidadeQuadrada * $fatorMetro * $fatorMetro;
    return ($areaM2 > 0) ? $areaM2 : null;
  }

  private function detectarInsunitsDxf(array $pares): int
  {
    $total = count($pares);
    for ($i = 0; $i < $total; $i++) {
      if (($pares[$i]['code'] ?? '') !== '9') {
        continue;
      }

      if (strtoupper((string) ($pares[$i]['value'] ?? '')) !== '$INSUNITS') {
        continue;
      }

      for ($j = $i + 1; $j < $total; $j++) {
        $codigo = (string) ($pares[$j]['code'] ?? '');
        if ($codigo === '9') {
          break;
        }
        if ($codigo === '70') {
          return (int) ($pares[$j]['value'] ?? 0);
        }
      }
    }

    // fallback comum em corte: milimetros
    return 4;
  }

  private function fatorMetroPorInsunits(int $insunits): float
  {
    switch ($insunits) {
      case 1:
        return 0.0254; // inches
      case 2:
        return 0.3048; // feet
      case 4:
        return 0.001; // mm
      case 5:
        return 0.01; // cm
      case 6:
        return 1.0; // m
      case 7:
        return 1000.0; // km
      case 10:
        return 0.9144; // yards
      case 14:
        return 0.1; // decimetro
      default:
        return 0.001;
    }
  }

  private function dxfToFloat(string $valor): ?float
  {
    $normalizado = str_replace(',', '.', trim($valor));
    if ($normalizado === '' || !is_numeric($normalizado)) {
      return null;
    }
    return (float) $normalizado;
  }

  private function calcularAreaPoligono(array $vertices): float
  {
    $total = count($vertices);
    if ($total < 3) {
      return 0.0;
    }

    $soma = 0.0;
    for ($i = 0; $i < $total; $i++) {
      $j = ($i + 1) % $total;
      $x1 = (float) ($vertices[$i][0] ?? 0.0);
      $y1 = (float) ($vertices[$i][1] ?? 0.0);
      $x2 = (float) ($vertices[$j][0] ?? 0.0);
      $y2 = (float) ($vertices[$j][1] ?? 0.0);
      $soma += ($x1 * $y2) - ($x2 * $y1);
    }

    return $soma / 2.0;
  }
}
