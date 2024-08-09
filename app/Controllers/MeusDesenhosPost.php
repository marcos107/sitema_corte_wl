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
      $caminho = "";
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
            $caminho = $value['caminho'];
            $ultima_barra_invertida = strrpos($caminho, 'i061n');
      
            // Dividir a string em duas partes
            $caminho_diretorio = substr($caminho, 0, $ultima_barra_invertida);
            $nome_arquivo = substr($caminho, $ultima_barra_invertida);
      
            // Criar o array resultante
            $array_resultante = [$caminho_diretorio, $nome_arquivo];
      
            $caminho = str_replace(["ci083ni061n", "wli074ndesenhos", "i061n"], ["c:/", "wl_desenhos", "/"], $array_resultante[0]) . '/' . Ferramentas::decodificador($array_resultante[1]);
            $caminho = str_replace("//", "/", $caminho);

            if (!file_exists($caminho)) {
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
        "lista_ids" => $lista_ids,
        "data" => $dias,
        '1' => $caminho

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
      $caminho_antigo = Ferramentas::decodificador($lista['caminho'].$lista["nome"]);
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
     // $_SESSION = ['funcao' => $_SESSION['funcao'], 'usuario' => $_SESSION['usuario'], 'usuario_nome' => $_SESSION['usuario_nome'], 'lista_completa' => $_SESSION["lista_completa"]];
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

}