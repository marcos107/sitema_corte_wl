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
      $dataInicial = service('request')->getPost('data1');
      $dataFinal = service('request')->getPost('data');
      $processo = service('request')->getPost('processo');



      $processoModel = new \App\Models\Processos();
      $proc = $processoModel
        ->where('nome', $processo)
        ->where('status', 'ativo')   // opcional, se você quiser só ativos
        ->first();



      if ($proc) {
        $start = (new \DateTime($dataInicial))
          ->setTime(0, 0, 0)
          ->format('Y-m-d H:i:s');
        $end   = (new \DateTime($dataFinal))
          ->setTime(23, 59, 59)
          ->format('Y-m-d H:i:s');
        $desenhos_data = $desenhos
          ->where('usuario_id_desenhista', $_SESSION['usuario'])
          ->where('data_add >=', $start)
          ->where('data_add <=', $end)
          ->where('processos_id',  $proc['id'])
          ->findAll();



        foreach ($desenhos_data as $key => $value) { //cria a lista


          $prioridade_desenho = $prioridade->where('id', $value['prioridade_id'])->first();
          $empresa_nome = $empresa->where('id', $value['empresa_id'])->first()['nome'];
          $empreendimento_nome = $empreendimento->where('id', $value['empreendimentos_id'])->first()['nome'];
          $finalidade_nome = $finalidade->where('id', $value['finalidade_id'])->first()['nome'];

          $tags = explode('/', ($value['diretorio']));
          $cort_hora_add = Ferramentas::array_index($finalidade->where('id', $value['corte_id'])->first(), ['hora_add']);
          // Remover os índices de 0 a 5
          $tags = array_slice($tags, 6);

          // Remover o último elemento
          unset($tags[count($tags) - 1]);

          $tags = implode(" - ", $tags);
          // Constrói a linha da tabela com informações do desenho.










          $meio = '<td  bgcolor="' . $prioridade_desenho['cor'] . '"><span onclick="prio_modal(\'' . $id_temp . '\')" class="marca_texto">' . $prioridade_desenho['nome'] . '</span></td>
            <td onclick="prio_modal(\'' . $id_temp . '\')">' . Ferramentas::remove_id_file($value['nome']) . '</td>
            <td onclick="prio_modal(\'' . $id_temp . '\')">' . $empresa_nome . '</td>
            <td onclick="prio_modal(\'' . $id_temp . '\')">' . $empreendimento_nome . '</td>
            <td onclick="prio_modal(\'' . $id_temp . '\')">' . $finalidade_nome . '</td>
            <td onclick="prio_modal(\'' . $id_temp . '\')">' . $tags . '</td>
            <td onclick="prio_modal(\'' . $id_temp . '\')">' . $value['status'] . '</td>
            <td onclick="prio_modal(\'' . $id_temp . '\')">' . Ferramentas::formatarDataHora($value['data_add']) . '</td>';








          if (Ferramentas::decodificador($value['status']) == 'pendente') {
            //<button name="cadastarar" type="submit" onclick="subistituir_desenho_modal(\''. $id_temp .'\')" class="btn btn-outline-primary"Renomear/Substituir/button>

            // Cria a linha da tabela para desenhos com status 'pendente'
            $lista .= '
            <tr>
            '. $meio.'
            <td><button name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="apagar(' . $id_temp . ')"> Apagar </button></td>
            <td><button name="cadastarar" type="submit" onclick="subistituir_desenho_modal(\'' . $id_temp . '\')" class="btn btn-outline-primary">Renomear/Substituir</button></td>
            </tr>
            ';
          } else if (Ferramentas::decodificador($value['status']) == 'pronto' || Ferramentas::decodificador($value['status']) == 'cortado') {
    
            

            if (!file_exists(Ferramentas::wlStoragePath((string) ($value['diretorio'] ?? '')))) {
              $value['status'] = "cortado_notfile";

              $desenhos->update($value['id'], $value);
            }
            //<button name="cadastarar" type="submit" class="btn btn-outline-primary "> adicionar <br> novamente </button>
            // Cria a linha da tabela para desenhos com status 'pronto'
            $lista .= '
            <tr>
          '. $meio.'
            <td><button name="cadastarar" onclick="recolocar_desenho(\'' . $id_temp . '\')" type="submit" class="btn btn-outline-primary "> adicionar <br> novamente </button></td>
            <td></td>
            </tr>
            ';
          } else if (Ferramentas::decodificador($value['status']) == 'cortando') {
            // Cria a linha da tabela para desenhos com status 'cortando'
            $lista .= '
            <tr>  
            '. $meio.'
            <td><button name="cadastarar" type="submit" disabled class="btn btn-outline-dark"> Cortando... </button></td>
            <td></td>
            </tr>
            ';
          }else  {
            // Cria a linha da tabela para desenhos com status 'cortando'
            $lista .= '
            <tr>  
            '. $meio.'
            <td></td>
            <td></td>
            </tr>
            ';
          }

          if (Ferramentas::decodificador($value['status']) == 'pendente' || Ferramentas::decodificador($value['status']) == 'pronto') {
            // Prepara dados do usuário para armazenamento em arrays
            $value['cor'] =$prioridade_desenho['cor'];
            $value['finalidade'] = $finalidade_nome;
            $value['empresa'] = $empresa_nome;
            $value['empreendimento'] = $empreendimento_nome;
            $value['data_hora_add'] = Ferramentas::formatarDataHora($value['data_add']);
            $value['prioridade'] = $prioridade_desenho['nome'];
            $lista_ids[$id_temp] = $value['id'];
            $value['id'] = $id_temp;
            $lista_completa[$id_temp] = $value;
            $id_temp++;
          }

        }



        $_SESSION["lista"] = $lista_ids;
        $_SESSION["lista_completa"] = $lista_completa;


        // Resposta do AJAX que retorna a lista e as datas
        $data = [
          "lista" => $lista,
          "lista_ids" => $lista_ids
,"1" => $desenhos_data


        ];

        return $this->response->setJSON($data);
      }
      return $this->response->setJSON(["ok" => false]);
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
      $caminhoBanco = (string) ($lista['diretorio'] ?? $lista['caminho'] ?? '');
      if (Ferramentas::get_type_file(Ferramentas::decodificador($caminhoBanco)) == "") {
        $caminho_antigo = Ferramentas::decodificador($caminhoBanco . $lista["nome"]);
      } else {
        $caminho_antigo = Ferramentas::decodificador($caminhoBanco);
      }

      //tratamento para evitar barras duplas
      $caminho_antigo = Ferramentas::wlStoragePath(str_replace('//', '/', $caminho_antigo));

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
