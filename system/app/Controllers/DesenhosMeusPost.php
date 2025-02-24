<?php

namespace App\Controllers;

use App\Controllers\Ferramentas;
use Config\App;

class DesenhosMeusPost extends Ferramentas
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
      $processos = new \App\Models\Processos();

      // Recupera dados das tabelas do banco de dados
      $prioridade_data = $prioridade->find();
      $finalidade_data = $finalidade->find();
      $empresa_data = $empresa->find();
      $empreendimento_data = $empreendimento->find();
      $desenhos_data = $desenhos->find();
      $processos_data = $processos->find();

      $lista = "";
      $id_temp = 0;
      $lista_ids = array();
      $lista_completa = array();

      // Recupera dados da solicitação AJAX
      $dataInicial = service('request')->getPost('data');
      $dataFinal = service('request')->getPost('data1');
      $processo = service('request')->getPost('processo');

        // Converte as datas para timestamps (apenas para geração do array de datas para exibição)
        $dataInicialTimestamp = strtotime($dataInicial);
        $dataFinalTimestamp   = strtotime($dataFinal);
        if ($dataInicialTimestamp > $dataFinalTimestamp) {
            list($dataInicialTimestamp, $dataFinalTimestamp) = array($dataFinalTimestamp, $dataInicialTimestamp);
            $dataInicial = date('Y-m-d', $dataInicialTimestamp);
            $dataFinal   = date('Y-m-d', $dataFinalTimestamp);
        }



      $desenhos_data = $desenhos
      ->where('desenhista', $_SESSION['usuario'])
      ->where("STR_TO_DATE(REPLACE(SUBSTRING_INDEX(data_hora_add, ' ', 1), 'i061n', '/'), '%d/%m/%Y') >=", $dataInicial)
      ->where("STR_TO_DATE(REPLACE(SUBSTRING_INDEX(data_hora_add, ' ', 1), 'i061n', '/'), '%d/%m/%Y') <=", $dataFinal)
      ->find();



      foreach ($desenhos_data as $key => $value) { //cria a lista
        $tags = explode('/', Ferramentas::decodificador($value['caminho']));
        // Remover os índices de 0 a 5
        $tags = array_slice($tags, 6);

        // Remover o último elemento
        unset($tags[count($tags) - 1]);
        $tags = implode(" - ", $tags);


        $_SESSION["lista_primordial"][$id_temp] = $value;




         
        
          
          $prioridade_desenho = Ferramentas::array_pesquisa($prioridade_data, 'id', $value['prioridade']);
          if (Ferramentas::decodificador($value['status']) == 'pendente') {
            //<button name="cadastarar" type="submit" onclick="subistituir_desenho_modal(\''. $id_temp .'\')" class="btn btn-outline-primary"Renomear/Substituir/button>

            // Cria a linha da tabela para desenhos com status 'pendente'
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
          } else if (Ferramentas::decodificador($value['status']) == 'pronto') {
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
            // Cria a linha da tabela para desenhos com status 'pronto'
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
       <td></td>
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
       <td></td>
      </tr>
      ';
          
        }
      }

      $_SESSION["lista"] = $lista_ids;
      $_SESSION["lista_completa"] = $lista_completa;


      // Resposta do AJAX que retorna a lista e as datas
      $data = [
        "lista" => $lista,
        "lista_ids" => $lista_ids
     
 

      ];

      return $this->response->setJSON($data);
    }
  }


  /**
   * Função desenho_meus_modal()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por retornar informações detalhadas sobre um ou todos desenhos a ser exibido em um modal ou em outro contexto na interface do usuário.
   *
   * Retorna um JSON contendo a lista completa de usuários e informações detalhadas sobre um ou todos desenhos com base no ID fornecido.
   */
  function desenho_meus_modal()
  {
    if ($this->request->isAJAX()) {
      // Inicializa a sessão para acessar os dados da lista armazenados nela
      session_start();

      // Obtém o ID do desenho a ser exibido no modal
      $id = service('request')->getPost('id');

      //verfica se a solicitação esta pedindo um id expecifico ou a lista inteira 
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

      //pega o id do desenho que sera modificado
      $id_temp = $_SESSION["id_subistituir"];
      $lista = $_SESSION["lista_completa"][$id_temp];

      // Salva o caminho antigo do arquivo caso ele não tenha um nome junto com o caminho adiciona o nome. 
      if(Ferramentas::get_type_file(Ferramentas::decodificador($lista['caminho'])) == ""){
      $caminho_antigo = Ferramentas::decodificador($lista['caminho'].$lista["nome"]); 
      }else{
        $caminho_antigo = Ferramentas::decodificador($lista['caminho']);
      }
      
      //tratamento para evitar barras duplas
      $caminho_antigo = str_replace('//', '/', $caminho_antigo);
      
      //verifica se o arquivo existe
      if (file_exists($caminho_antigo)) { //para mudar o nome do arquivo
        //busca o arquivo mando pela solicitação ajax
        $file = $this->request->getFile('file');
        $nome_novo = $_SESSION["novo_nome_arquivo"];

        //pega o id verdadeiro do desenho
        $id = $_SESSION["lista"][$id_temp];

        //colcoa paenas o nome retirando a exteção do arquivo na varivael nome_antigo
        $nome = Ferramentas::decodificador($lista['nome']);
        $nome_antigo = Ferramentas::remove_id_file(str_replace('.' . Ferramentas::get_type_file($nome), '', $nome));

        //retira o nome do arquivo do caminho
        $caminho = str_replace($nome, '', $caminho_antigo);

        // Variável que armazenará o novo nome do desenho. 
        // Caso não haja alteração, ela será predefinida com o nome atual do desenho.
        $novo_nome = $nome;




        //caso tenha trocado o nome ira criar um nome do arquivo onde exita apenas aquele na pasta
        if (Ferramentas::codificador($nome_novo) != Ferramentas::codificador($nome_antigo)) {
          do {
            $random = rand(10000, 99999);
            $novo_nome = $nome_novo . '_' . $random . '_.' . Ferramentas::get_type_file($nome);
            $caminho = str_replace($nome, '', $caminho_antigo) . $novo_nome;
          } while (file_exists($caminho));
        }

        //verifica se é para trocar o arquivo ou apenas renomear
        if ($file != null) {

          //verifica a integridade do arquivo
          if ($file->isValid() && !$file->hasMoved()) {


            
            //move o novo arquivo para o diretorio do antigo retirando ele do local temporario
            if ($file->move(str_replace($nome, '', $caminho_antigo), $novo_nome)) {
              //apaga o arqivo antigo
              unlink($caminho_antigo);


              //atualiza a teabela desenhos no banco 
              $db = new \App\Models\Desenhos();
              $db->update($id, [
                'caminho' => Ferramentas::codificador($caminho),
                'nome' => Ferramentas::codificador($novo_nome)
              ]);

              //salva no historio que fez a modificação no banco
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
          //verifica se o nome foi modificado
          if (Ferramentas::codificador($nome_novo) != Ferramentas::codificador($nome_antigo)) {
             //renomeia o arquivo 
            if (rename($caminho_antigo, $caminho)) {

              //atualiza a teabela desenhos no banco 
              $db = new \App\Models\Desenhos();
              $db->update($id, [
                'caminho' => Ferramentas::codificador($caminho),
                'nome' => Ferramentas::codificador($novo_nome)
              ]);


              //salva no historio que fez a modificação no banco
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